<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 8/30/2018
 * Time: 11:07 AM
 */

class Model_ar extends CI_Model
{
    function get_billing($servno = false, $mtr = false, $limit = false, $month = false, $year = false, $start = false, $searchtype = false) {
        $data = array();

        if (
            $servno == false  &&
            $mtr == false &&
            $limit == false &&
            $month == false &&
            $year == false &&
            $start == false
        ) {
            $servno = $this->input->post('servno');
            $mtr = $this->input->post('mtr');
            $limit = $this->input->post('limit');
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            $start = $this->input->post('start');
        }
        $acctid = 0;
        $address = '';
        $name = '';
        $pic = '';
        $status_class = '';
        $status_name = '';

        $ar_gdlb = 'None';
        $ar_rate = 'None';
        $ar_mult = 'None';
        $mtr_no = 'None';
        $ar_lastpay = '';
        $ar_nobills = 0;

        $legacy_amt_prev = 0;
        $amt_total_balance = 0;
        $amt_total_interest = 0;
        $amt_total_overdue = 0;
        $amt_total_current = 0;
        $amt_total_kwh = 0;
        $amt_total_pay = 0;
        $kwh_ave = 0;

        $amt_to_pay = 0;

        $paymentsp = false;


        $query_acct = $this->db->select(
            '
                ac.servicenumber AS SERVNO,
                ac.mtr AS MTR,
                ac.sysid AS ACCTID, 
                ac.mtrno AS MTRNO,
                ac.status AS STATUS,
                ac.ownerid AS OWNID,
                ac.types AS OWNERTYPE,
                addr.addrspecific AS ADDRSTR,
                rcs.codes AS RATECODE,
                rcs.descs AS RATEDESC,
                mm.codes AS MULT
                '
        )
            ->select("CONCAT(gdlb.g, '-', ads.codes, '-', gdlb.l, '-', gdlb.b) AS GDLB", false)
            ->from('customer_accounts_main AS ac')
            ->join('customer_accounts_address AS addr', 'addr.acctid = ac.sysid AND addr.status = 1', 'left')
            ->join('gdlb_main AS gdlb', 'gdlb.sysid = ac.gdlb', 'left')
            ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
            ->join('rate_class_specification AS rcs', 'rcs.sysid = ac.rateclassid', 'left')
            ->join('billing_rates_main_multiplier AS mm', 'mm.sysid = ac.multid', 'left')
            ->where(array('ac.servicenumber' => $servno, 'ac.mtr' => $mtr))
            ->get()->row();

        if($query_acct) {
            $acctid = $query_acct->ACCTID;
            $address = $query_acct->ADDRSTR;
            $ar_gdlb = $query_acct->GDLB;
            $ar_rate = $query_acct->RATEDESC;
            $ar_mult = $query_acct->MULT;
            $mtr_no = $query_acct->MTRNO;

            // NAMES FROM PERSON AND LEGACY NAMES
            if($query_acct->OWNERTYPE==5) {
                $qry_legacy = $this->db->select("name")
                    ->from('customer_accounts_name_legacy')
                    ->where("sysid", $query_acct->OWNID)
                    ->get()->row();
                if($qry_legacy) {
                    $name = $qry_legacy->name;
                }
            }else{
                $qry_person = $this->db->select("CONCAT(lastname, ', ', firstname, ' ', middlename) AS name")
                    ->from('person')
                    ->where("sysid", $query_acct->OWNID)
                    ->get()->row();
                if($qry_person) {
                    $name = $qry_person->name;
                }
            }

            // GET ACOUNT PICTURE
            $pic = base_url('assets/global/img/admin_pic.png');
            if(file_exists(FCPATH . 'uploads/attachements/attachments/' . str_pad($acctid, 8, '0', STR_PAD_LEFT) .'/current.jpg')) {
                $pic = base_url('uploads/attachements/attachments/' . str_pad($acctid, 8, '0', STR_PAD_LEFT) .'/current.jpg');
            }

            // GET STATUS
            $status_class = ($query_acct->STATUS==1) ? 'success' : 'danger';
            $status_name = ($query_acct->STATUS==1) ? 'Active' : 'Disconnected';

            // GET LEGACY AR
            // LEGACY PREVAMT
            $legacy_amt_prev = 0;
            $qry_legacy_ar = $this->db->select("amt_13 AS amt13")
                ->from('customer_accounts_ar AS ar')
                ->where(array('ar.acctid' => $acctid))
                ->get()->row();
            if($qry_legacy_ar) {
                $legacy_amt_prev = $qry_legacy_ar->amt13;
            }

            // LAST PAY DATE
            $qry_last_pay = $this->db->select('CAST(datecreated AS date) AS paydte')
                ->from('billing_payapplied')
                ->where(array('acctid' => $acctid, 'status' => 1))
                ->order_by('datecreated', 'desc')
                ->limit(1)
                ->get()->row();
            if($qry_last_pay) {
                $ar_lastpay = $qry_last_pay->paydte;
            }

            if($year) {
                $this->db->where('YEAR(b.dteprt) <= ', $year);
            }
            if($month && $month != '' ) {
                $this->db->where('MONTH(b.dteprt) <= ', $month);
            }
            if($start) {
                $this->db->limit($limit, $start);
            }else{
                $this->db->limit($limit);
            }
            $query = $this->db->select('
                    b.sysid, 
                    b.bmo AS month, 
                    b.byr AS year, 
                    b.current, 
                    b.kwhuse, 
                    b.billno, 
                    b.totalvat, 
                    b.duedate,
                    b.batch
            ')
                ->from('billing_reports_main AS b')
                ->where(array('b.servno' => $servno, 'b.mtr' => $mtr))
                ->order_by('b.prsdte', 'desc')
                ->get();
            if ($query->num_rows() > 0) {
                $i = 0;
                $len = $query->num_rows();
                foreach ($query->result() as $row) {

                    $reff = '';
                    $amt_bal = 0;

                    if($year && trim($year) != '') {
                        $this->db->where('YEAR(p.datecreated) <= ', $year);
                    }

                    if($month && trim($month) != '' ) {
                        $this->db->where('MONTH(p.datecreated) <= ', $month);
                    }

                    $qry_pay = $this->db->select('SUM(p.amtpd) AS amtpd, SUM(p.interest) AS interest, SUM(p.frtax) AS frtax')
                        ->from('billing_payapplied AS p')
                        ->where(array('p.acctid' => $acctid, 'p.billyr' => $row->year, 'p.billmo' => $row->month, 'p.status' => 1))
                        ->get()->row();

                    $amt_paid = 0;
                    $datepaid = '';
                    $duedate = $row->duedate;
                    $amt_current = $row->current;
                    $amt_int = 0;

                    if ($year && $month) {
                        $todate = $year . '-' . $month . '-' . date("d");
                    } else {
                        if ($year) {
                            $todate = $year . '-' . date("m-d");
                        } else {
                            if ($month) {
                                $todate = date("Y") . '-' . $month . '-' . date("d");
                            } else {
                                $todate = date("Y-m-d");
                            }
                        }
                    }
                    $int_per = 0.0224;
                    $num_dues = 0;


                        if (validateDate($duedate, 'Y-m-d')) {
                            // GET HOW MANYDUES
                            $qry_dues_bill = $this->db->select('duedate')
                                ->from('billing_reports_main')
                                ->where(array('duedate > ' => $duedate, 'acctid' => $acctid, 'mtr' => 1))
                                ->get();
                            $num_rows_d = $qry_dues_bill->num_rows();
                            $duedate_dte = new DateTime($duedate);
                            $today = new DateTime($todate);
                            if ($today > $duedate_dte) {
                                $num_rows_d = $num_rows_d + 1;
                            }
                            $num_dues = $num_rows_d;
                            $int_total_per = $int_per * $num_dues;
                            //$amt_int = $num_rows_d;
                            $amt_int = round(($amt_current * $int_total_per), 2);
                        }



                    if($qry_pay) {

                        $amt_paid = $qry_pay->amtpd + $qry_pay->interest;

                        $amt_bal = ($amt_current + $qry_pay->interest) - $amt_paid;
                        $amt_total_pay += $amt_paid;
                    }

                    $qry_pay_dt = $this->db->select('CAST(p.datecreated AS DATE) AS datepd')
                        ->from('billing_payapplied AS p')
                        ->where(array('p.acctid' => $acctid, 'p.billyr' => $row->year, 'p.billmo' => $row->month, 'p.status' => 1))
                        ->get()->row();

                    if($qry_pay_dt) {
                        $datepaid = $qry_pay_dt->datepd;
                    }else{
                        $datepaid = '';
                    }


                    if(trim($row->batch) != 'LATEBILL') {
                        $total_amt_row = $amt_current + $amt_int;
                    }else{
                        $total_amt_row = $amt_current;
                    }

                    $intamt = 0;

                    $paid = false;

                    if($amt_paid > 0) {
                        $intamt =  $qry_pay->interest;
                        $paid = true;
                    }else{
                        if(trim($row->batch) != 'LATEBILL') {
                            $intamt = $amt_int;
                            $amt_total_balance += (($amt_current + $amt_int) + $amt_bal);
                            $amt_total_interest += $amt_int;
                            $amt_total_overdue += $amt_current;
                            $ar_nobills += 1;
                        }
                    }
                    $check_box = '';
                    if($i==0) {
                        if($qry_pay) {
                            if($amt_bal>0) {
                                $amt_total_current = $amt_bal;
                            }else{
                                $amt_total_current = 0;
                            }
                        }else {
                            $amt_total_current = ($amt_current + $intamt);
                        }
                    }

                    if($searchtype==1) {
                        if($amt_bal>0) {

                            $check_box = 'checked="checked"';

                            $m = $row->month;
                            $yr = $row->year;
                            $amt_vat = '<span class="txt">' . number_format($row->totalvat, 2) . '</span><input type="hidden" readonly style="width: 100%" value="' . $row->totalvat . '" placeholder="0.00" class="form-control inline input-xs number" name="vatamt[' . $m . ']" />';

                            $collect = true;
                            $get_ref = $this->db->select('t.codeid')
                                ->from('billing_reports_tagging_trn AS t')
                                ->where(array('t.billid' => $row->sysid, 't.status != ' => 303))
                                ->order_by('t.datecreated', 'desc')
                                ->get()->row();
                            if($get_ref) {
                                if(hold_payments($get_ref->codeid)) {
                                    $collect = false;
                                }
                                $reff .= get_types_label_format($get_ref->codeid, false, false);
                            }

                            if(trim($row->batch) == 'LATEBILL') {
                                $reff .= '<span class="label label-danger">L</span>';
                            }

                            //$reff = ' L';
                            $dt = DateTime::createFromFormat('m', $m);
                            $monthname = $dt->format('F');
                            $monthcode = $dt->format('M');
                            $btn_control = '';
                            $check_box_select = '';
                            $check_box_check = '<input type="checkbox" class="icheck check" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $m . '" name="check[' . $m . ']" />';
                            if($collect) {
                                if ($num_dues > 0) {

                                    if(trim($row->batch) != 'LATEBILL') {
                                        $check_box_select .= '<input ' . $check_box . ' type="checkbox" class="hidden" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                                        $btn_control = '<a class=""><i class="fa fa-lock text-warning"></i></a>';
                                    }else{
                                        $check_box_select .= '<input type="checkbox" class="select" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                                        $btn_control = '<button type="button" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button>';
                                    }

                                } else {
                                    $check_box_select .= '<input ' . $check_box . ' type="checkbox" class="select" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                                    $btn_control = '<button type="button" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button>';
                                }
                            }else{
                                $check_box_select .= '<i class="fa fa-times text-danger"></i>';
                            }

                            if($paymentsp) {
                                $amt_paid_input = '<input type="text" style="width: 100%" value="' . $total_amt_row . '" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />';
                                $intamt = '<input type="text" style="width: 100%" value="' . $intamt . '" placeholder="0.00" class="form-control inline input-xs number" name="intamt[' . $m . ']" />';
                            }else{
                                $amt_paid_input = '<input type="hidden" style="width: 100%" value="' . $total_amt_row . '" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />' . number_format($total_amt_row, 2);
                                $intamt = '<input type="hidden" style="width: 100%" value="' . $intamt . '" placeholder="0.00" class="form-control inline input-xs number" name="intamt[' . $m . ']" />' . number_format($intamt, 2);
                            }


                            $input_frtx = '<input style="width: 100%" name="frtx[' . $m . ']" class="form-control inline input-xs number" placeholder="0.00" />';

                            if($num_dues > 0) {
                                $amt_to_pay += $total_amt_row;
                            }

                            $info_popover = '<a class="" href="javascript:;" title="" data-toggle="popover" 
                                        data-content="
                                            <ul class=\'list-group summary column no-border\'>
                                            <li class=\'list-group-item\'>PN:  <span class=\'label-default pull-right\'>None</span></li> 
                                            <li class=\'list-group-item\'>R.A.: <span class=\'label-default pull-right\'>None</span></li> 
                                            <li class=\'list-group-item\'>BP22: <span class=\'label-default pull-right\'>None</span></li>
                                            <li class=\'list-group-item\'>REF:  <span class=\'label-default pull-right\'>None</span></li> 
                                            <li class=\'list-group-item\'>Duedate: <span class=\'label-default pull-right\'>' . $duedate . '</span></li>
                                            </ul>
                                        " data-placement="left" data-trigger="hover" data-original-title="Billing Info"><i class="fa fa-search fa-fw"></i></a>';


                            $data['tellering'][] = array(
                                'expand' => btn_expand($row->sysid),
                                //'month' => '<span id="month_id" data-id="' . $row->sysid . '" class="label label-info">' . str_pad($row->month, 2, '0', STR_PAD_LEFT) . '</span> ' . strtoupper(date_formating($row->month, '!m', 'M')),
                                'month' => '<input type="hidden" name="moyr[' . $m . ']" value="' . $yr . '" /><span id="month" data-schedid="0" data-month="' . $m . '" data-year="' . $year . '" data-id="' . $acctid . '">' . $monthcode . '</span>',
                                'year' => date_formating($row->year, 'y', 'Y'),
                                'billno' => $row->billno,
                                'kwh' => round($row->kwhuse),
                                'current' => number_format($row->current, 2),
                                'duedate' => $duedate,
                                'interest' => $intamt,
                                'amtpd' => $amt_paid_input,
                                'frtx' => $input_frtx,
                                'vat' => $amt_vat,
                                'chk' => $check_box_check,
                                'balance' => number_format($amt_bal, 2),
                                'datepaid' => $datepaid,
                                'select' => $check_box_select,
                                'ref' => $reff,
                                'inf' => $info_popover,
                                'control' => '',
                                'del' => $btn_control
                            );
                        }
                    }else {
                        $data['list'][] = array(
                            'expand' => btn_expand($row->sysid),
                            'month' => '<span id="month_id" data-id="' . $row->sysid . '" class="label label-info">' . str_pad($row->month, 2, '0', STR_PAD_LEFT) . '</span> ' . strtoupper(date_formating($row->month, '!m', 'M')),
                            'year' => $row->year,
                            'billno' => $row->billno,
                            'kwh' => round($row->kwhuse),
                            'current' => number_format($amt_current, 2),
                            'duedate' => $duedate,
                            'interest' => number_format($intamt, 2),
                            'amtpaid' => number_format($amt_paid, 2),
                            'balance' => number_format($amt_bal, 2),
                            'datepaid' => $datepaid,
                            'remarks' => '',
                            'paid' => $paid
                        );
                    }
                    if($i<=12) {
                        $data['kwharr'][] = array('month' => date_formating($row->month, '!m', 'M'), 'value' => round($row->kwhuse));
                    }
                    $amt_total_kwh += $row->kwhuse;
                    $i++;
                }
            }
        }
        if($amt_total_kwh > 0) {
            $kwh_ave = $amt_total_kwh / $limit;
        }


        $data['servno'] = strtoupper($servno);
        $data['address'] = $address;
        $data['name'] = $name;
        $data['pic'] = $pic;
        $data['statusclass'] = $status_class;
        $data['status'] = $status_name;
        $data['lastpay'] = $ar_lastpay;
        $data['nobills'] = $ar_nobills;



        $data['gdlb'] = $ar_gdlb;
        $data['rate'] = $ar_rate;
        $data['mult'] = $ar_mult;
        $data['mtrno'] = $mtr_no;

        $data['limit'] = $limit;
        $data['starts'] = ($start + $limit) + 1;
        if($start) {
            if($start>$limit) {
                $back = $start - $limit;
            }else{
                $back = $start;
            }
            $data['back'] = $back;
        }else{
            $data['back'] = 0;
        }

        $data['acctid'] = $acctid;
        $data['amtbal'] = number_format($amt_total_balance, 2);
        $data['amtprev'] = number_format($legacy_amt_prev, 2);
        $data['amtint'] = number_format($amt_total_interest, 2);
        $data['amtdue'] = number_format($amt_total_overdue, 2);
        $data['amtcur'] = number_format($amt_total_current, 2);
        $data['amtpaid'] = number_format($amt_total_pay, 2);
        $data['amttopay'] = $amt_to_pay;
        $data['kwhave'] = number_format($kwh_ave, 0);
        return json_encode($data);
    }

    function search_account() {
        $data = array();
        $q = $this->input->post('searchtxt');
        $qry = $this->db->select('
                m.sysid,
                m.servicenumber AS servno,
                a.addrspecific AS addr,
                m.types,
                m.ownerid
            ')
            ->from('customer_accounts_main AS m')
            ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
            ->or_like('m.servicenumber', $q)
            ->get();

        $res = array();
        if($qry->num_rows()>0) {
            foreach ($qry->result() as $row) {

                $pic = base_url('assets/global/img/person_default.jpg');
                $name = '@TODO';
                if($row->types==5) {
                    $qry_legacy = $this->db->select("name")
                        ->from('customer_accounts_name_legacy')
                        ->where("sysid", $row->ownerid)
                        ->get()->row();
                    if($qry_legacy) {
                        $name = $qry_legacy->name;
                    }
                }
                $res[] = array(
                    'id' => $row->sysid,
                    'text' => $row->servno,
                    'name' => $name,
                    'addr' => $row->addr,
                    'pics' => $pic,
                    'control' => '<button data-servno="'.$row->servno.'" data-mtr="1" id="search_btn_row" class="btn btn-xs btn-default">Get</button>'
                );
            }
        }else{
            $qry = $this->db->select('
                m.sysid, 
                m.servicenumber AS servno, 
                l.name,
                a.addrspecific AS addr
                ')
                ->from('customer_accounts_name_legacy AS l')
                ->join('customer_accounts_main AS m', 'm.ownerid = l.sysid', 'left')
                ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
                ->or_like('l.name', $q)
                ->where('m.types', 5)
                ->get();

            $res = array();
            if($qry->num_rows()>0) {
                foreach ($qry->result() as $row) {

                    $pic = base_url('assets/global/img/person_default.jpg');
                    $res[] = array(
                        'id' => $row->sysid,
                        'text' => $row->servno,
                        'name' => $row->name,
                        'addr' => $row->addr,
                        'pics' => $pic,
                        'control' => '<button data-servno="'.$row->servno.'" data-mtr="1" id="search_btn_row" class="btn btn-xs btn-default">Get</button>'
                    );
                }
            }
        }
        $data['list'] = $res;
        return json_encode($data);
    }

    function get_credit_memo_db() {
        $data = array();
        $msg = '';
        if ( pecoapps_conn()) {
            $msg = 'DB Connected!';
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $query = $conn
                ->select("
                    cmno,
                    cast(tdate AS DATE) AS tdate,
                    servno,
                    mtr,
                    mo,
                    yr,
                    adiffkwh,
                    adiffamt,
                    sdiffkwh,
                    sdiffamt,
                    adjustkwh,
                    adjustamt,
                    billno,
                    rem1,
                    rem2,
                    rem3,
                    keyinc,
                    CAST(updte AS DATE) AS updte,
                    updrem,
                    fatheramt,
                    fatherkwh,
                    usercode,
                    reqestingDept

                ")
                ->from('prread')
                ->get();
            if($query->num_rows()>0) {
                foreach($query->result() as $row) {

                    $get_acctid = $this->db->select('sysid, mtrno')
                        ->from('customer_accounts_main')
                        ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                        ->get()->row();

                    if($get_acctid) {

                        $m = date('m', strtotime($row->mo));

                        if(trim($row->yr) > 0) {
                            if(is_numeric($row->yr)) {
                                $dt_y = DateTime::createFromFormat('y', $row->yr);
                                $y = $dt_y->format('Y');
                            }else{
                                $y = $row->yr;
                            }
                        }else{
                            $y = $row->yr;
                        }


                        $ins_arr = array(
                            'cmno' => $row->cmno,
                            'acctid' => $get_acctid->sysid,
                            'mtrno' => $get_acctid->mtrno,
                            'mo' => $m,
                            'yr' => $y,
                            'origamt' => $row->adiffamt,
                            'origkwh' => $row->adiffkwh,
                            'corramt' => $row->sdiffkwh,
                            'corrkwh' => $row->sdiffamt,
                            'billno' => $row->billno,
                            'remarks' => $row->rem1 . ' ' . $row->rem2 . ' ' . $row->rem3,
                            'remarksupd' => $row->updrem,
                            'datecreated' => $row->tdate,
                            'dateupdated' => $row->updte,
                            'createdby' => $row->usercode,
                            'udpatedby' => $row->usercode
                        );
                        $this->db->insert('customer_accounts_creditmemo', $ins_arr);
                        $data['err'][$row->cmno] = $this->db->_error_message();
                    }
                }
            }else{
                $msg = 'No record found!';
            }
        }

        $data['msg'] = $msg;
        return json_encode($data);
    }

    function get_customer_payment_fromold() {
        $data = array();
        $servno = $this->input->post('servno');
        $mtr = $this->input->post('mtr');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $msg = '';

        if ( pecoapps_conn()) {
            $msg = 'DB Connected!';
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $query = $conn
                ->select('p.billmo, p.billyr, p.tdate, p.teller')
                ->select('SUM(ISNULL(p.amtpd, 0)) AS amtpd, SUM(ISNULL(p.intrst,0)) AS intrst', false)
                ->from('payapplied AS p')
                ->where(array('p.servno' => $servno, 'p.mtr' => $mtr, 'YEAR(p.tdate) = ' => $year, 'MONTH(p.tdate) = ' => $month))
                ->group_by('p.billmo, p.billyr, p.tdate, p.teller')
                ->get();

            if($query->num_rows() > 0) {
                foreach($query->result() as $row) {
                    $get_acctid = $this->db->select('sysid')
                        ->from('customer_accounts_main')
                        ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                        ->get()->row();

                    if($get_acctid) {
                        $qry_dt = $conn
                            ->select('TOP 1 p.tdate, ps.orno')
                            ->from('payapplied AS p')
                            ->join('payments AS ps', 'CAST(ps.paydte AS DATE) = CAST(p.tdate AS DATE) AND ps.servno = p.servno AND ps.mtr = p.mtr', 'left')
                            ->where(array('p.servno' => $servno, 'p.mtr' => $mtr, 'p.billmo' => $row->billmo, 'p.billyr' => $row->billyr, 'YEAR(p.tdate) = ' => $year, 'MONTH(p.tdate) = ' => $month))
                            ->get()->row();

                        if($qry_dt) {
                            $date_obj = new DateTime($row->tdate);
                            $date_time_i = $date_obj->format('Y-m-d H:i:s');
                            $orno = $qry_dt->orno;
                        }else{
                            $date_time_i = null;
                            $orno = 0;
                        }
                        $ins_arr = array(
                            'tellerid' => $row->teller,
                            'acctid' => $get_acctid->sysid,
                            'billmo' => $row->billmo,
                            'billyr' => date_formating($row->billyr, 'y', 'Y'),
                            'amtpd' => $row->amtpd,
                            'interest' => $row->intrst,
                            'datecreated' => $date_time_i,
                            'orno' => $orno
                        );
                        $data['list'][] = $ins_arr;
                        $this->db->insert('billing_payapplied', $ins_arr);
                        $data['err'][] = $this->db->_error_message();
                    }
                }
            }
        } else {
            $msg = 'Cannot connect to old database!';
        }
        $data['msg'] = $msg;
        return json_encode($data);
    }
    function get_customer_payment_fromold_monthly() {
        $data = array();

        $year_post = $this->input->post('year');
        $month_post = $this->input->post('month');

        if($year_post && $month_post) {
            $year = $year_post;
            $month = $month_post;
        }else{
            $year_get = $this->input->get('year');
            $month_get = $this->input->get('month');
            $year = $year_get;
            $month = $month_get;
        }
        $func = 'error';
        $data_num_rows = 0;
        $data_ins_rows = 0;
        if($year && $month) {
            $pconn = pecoapps_conn();
            if ($pconn) {
                $msg = 'DB Connected!';
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                $query = $conn->select('p.billmo, p.billyr, p.tdate, p.teller, p.servno, p.mtr')
                    ->select('SUM(ISNULL(p.amtpd, 0)) AS amtpd, SUM(ISNULL(p.intrst,0)) AS intrst', false)
                    ->from('payapplied AS p')
                    ->where(array('YEAR(p.tdate) = ' => $year, 'MONTH(p.tdate) = ' => $month))
                    ->group_by('p.billmo, p.billyr, p.tdate, p.teller, p.servno, p.mtr')
                    ->get();

                $data_num_rows = $query->num_rows();

                if ($data_num_rows > 0) {
                    foreach ($query->result() as $row) {
                        $get_acctid = $this->db->select('sysid')
                            ->from('customer_accounts_main')
                            ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                            ->get()->row();

                        if ($get_acctid) {
                            $qry_dt = $conn
                                ->select('TOP 1 p.tdate, ps.orno')
                                ->from('payapplied AS p')
                                ->join('payments AS ps', 'CAST(ps.paydte AS DATE) = CAST(p.tdate AS DATE) AND ps.servno = p.servno AND ps.mtr = p.mtr', 'left')
                                ->where(array('p.servno' => $row->servno, 'p.mtr' => $row->mtr, 'p.billmo' => $row->billmo, 'p.billyr' => $row->billyr))
                                ->get()->row();

                            if ($qry_dt) {
                                $date_obj = new DateTime($row->tdate);
                                $date_time_i = $date_obj->format('Y-m-d H:i:s');
                                $orno = $qry_dt->orno;
                            } else {
                                $date_time_i = null;
                                $orno = 0;
                            }
                            $ins_arr = array(
                                'tellerid' => $row->teller,
                                'acctid' => $get_acctid->sysid,
                                'billmo' => $row->billmo,
                                'billyr' => date_formating($row->billyr, 'y', 'Y'),
                                'amtpd' => $row->amtpd,
                                'interest' => $row->intrst,
                                'datecreated' => $date_time_i,
                                'orno' => $orno
                            );
                            $data['list'][] = $ins_arr;
                            $ins = $this->db->insert('billing_payapplied', $ins_arr);
                            $data['err'][] = $this->db->_error_message();
                            if ($ins) {
                                $data_ins_rows += 1;
                            }else{
                                $catch_ins = array(
                                    'servno' => $row->servno,
                                    'mtr' => $row->mtr,
                                    'tellerid' => $row->teller,
                                    'billmo' => $row->billmo,
                                    'billyr' => date_formating($row->billyr, 'y', 'Y'),
                                    'datecreated' => $row->tdate,
                                );
                                $this->db->insert('billing_payapplied_catch', $catch_ins);
                                $data['err_cath'][] = $this->db->_error_message();
                            }
                        }else{
                            $catch_ins = array(
                                'servno' => $row->servno,
                                'mtr' => $row->mtr,
                                'tellerid' => $row->teller,
                                'billmo' => $row->billmo,
                                'billyr' => date_formating($row->billyr, 'y', 'Y'),
                                'datecreated' => $row->tdate,
                            );
                            $this->db->insert('billing_payapplied_catch', $catch_ins);
                            $data['err_cath'][] = $this->db->_error_message();
                        }
                    }

                    if($data_num_rows == $data_ins_rows) {
                        $func = 'success';
                        $msg = 'All records inserted!';
                    }else{
                        $func = 'info';
                        $msg = 'Some records inserted!';
                    }
                }
            } else {
                $func= 'warning';
                $msg = 'Cannot connect to old database!';
            }
        }else{
            $func= 'error';
            $msg = 'Year and month is not provided!';
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['num'] = $data_num_rows;
        $data['ins'] = $data_ins_rows;
        return json_encode($data);
    }

    function get_ar_list() {
        $data = array();
        $dist = $this->input->post('dist');
        $query_acct = $this->db->select('m.sysid, m.servicenumber AS servno, m.mtr, m.types, m.ownerid')
            ->from('customer_accounts_main AS m')
            ->join('gdlb_main AS g', 'g.sysid = m.gdlb', 'left')
            ->where(array('g.d' => $dist))
            ->get();

        if($query_acct->num_rows() > 0) {
            $num = 1;
            foreach($query_acct->result() as $row) {
                $nums = $num++;
                $qry_current = $this->db->select('current, overdue')
                    ->from('billing_reports_main')
                    ->where(array('acctid' => $row->sysid))
                    ->order_by('prsdte', 'desc')
                    ->get()->row();
                $name = '';
                // NAMES FROM PERSON AND LEGACY NAMES
                if($row->types==5) {
                    $qry_legacy = $this->db->select("name")
                        ->from('customer_accounts_name_legacy')
                        ->where("sysid", $row->ownerid)
                        ->get()->row();
                    if($qry_legacy) {
                        $name = $qry_legacy->name;
                    }
                }else{
                    $qry_person = $this->db->select("CONCAT(lastname, ', ', firstname, ' ', middlename) AS name")
                        ->from('person')
                        ->where("sysid", $query_acct->ownerid)
                        ->get()->row();
                    if($qry_person) {
                        $name = $qry_person->name;
                    }
                }

                $current = ($qry_current) ? $qry_current->current : 0;
                $overdue = ($qry_current) ? $qry_current->overdue : 0;


                $data['list'][] = array(
                    'num' => $nums,
                    'servno' => $row->servno,
                    'name' => $name,
                    'address' => '',
                    'current' => number_format($current, 2),
                    'overdue' => number_format($overdue, 2),
                    'control' => '<button data-servno="'.$row->servno.'"  data-mtr="'.$row->mtr.'" data-num="'.$nums.'" type="button" id="btn_query_payment" class="btn btn-default btn-sm '.$nums.'">Import Pay</button>',
                );
            }
        }
        return json_encode($data);
    }

    function get_payments() {

    }
}