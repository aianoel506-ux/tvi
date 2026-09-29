<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_tellering extends CI_Model {

    function submit_or_void() {
        $data = array();
        $qry = false;
        $orn_submited = 'Multiple ORs';
        $msg = '';
        $func = '';
        $this->db->trans_begin();
        $ors = $this->input->post('ors');
        $dataid = $this->input->post('dataid');
        $reason = $this->input->post('reason');
        $types = $this->input->post('voidtype');
        $reason = ($reason) ? ' - '.$reason : '';

        $or_arr = explode(',', $ors);

        if(count($or_arr)<=1) {
            $orn_submited = str_pad($ors, 9, '0', STR_PAD_LEFT);
        }

        $qry_payments = $this->db->select('sysid')
            ->from('transaction_payments_logs')
            ->where_in('orno', $or_arr)->get();

        $num_rows = $qry_payments->num_rows();
        $cnt_or_exists = 0;
        $cnt_inserted = 0;

        if($num_rows > 0) {
            foreach($qry_payments->result() as $row) {
                $checkor = $this->db->select("orid")
                    ->from("trn_request_orvoid")
                    ->where(array("orid" => $row->sysid, 'status' => 1))
                    ->get()->row();

                if($checkor == false){
                    $ins_arr = array(
                        'orid' => $row->sysid,
                        'groupid' => $dataid,
                        'types' => $types,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    if($this->db->insert('trn_request_orvoid', $ins_arr)) {
                        $cnt_inserted += 1;

                    }
                }else{
                    $cnt_or_exists += 1;
                }
            }

            if($cnt_or_exists==$num_rows) {
                $this->db->trans_rollback();
                $qry = false;
                $msg = 'All O.R. you submited is alread existing in request for Void.';
                $func = 'warning';
            }else {
                if($cnt_inserted > 0) {
                    // GET USER INFO
                    $userinfo = get_users_info(0, true);
                    // GLOBAL FUNCTION IF THE MODULE HAVE A PROCESS FLOW
                    $req = create_transaction_trails($orn_submited, 'OR VOID '.$reason, 8, $dataid);

                    if ($this->db->trans_status() === TRUE && $req) {
                        $this->db->trans_commit();
                        if ($num_rows > $cnt_inserted) {
                            $qry = true;
                            $func = 'info';
                            $msg = 'Some O.R. are existing in request for void! ' . $cnt_inserted . ' added to request.';
                        } else {
                            $qry = true;
                            $func = 'success';
                            $msg = 'Request for OR Void is submitted! ';
                        }
                    } else {
                        $this->db->trans_rollback();
                        $qry = false;
                        $msg = 'Void error';
                        $func = 'error';
                    }
                }
            }
        }


        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        return json_encode($data);
    }

    function get_billing_sp() {
        $input_yr = $this->input->post('year');
        $yr = ($input_yr) ? $input_yr : date('Y');

        $this_month = date('m');
        $num_rows = 0;
        $balance = 0;
        $num_bill = 0;
        $num_pay = 0;
        $armon = array();

        $qry_bill_logs = $this->db->select('sm.rmonth, sm.ryear')
            ->from('trn_billing_logs AS l')
            ->join('trn_reading_history AS rh', 'l.presreadid = rh.sysid')
            ->join('reading_schedule_main AS sm', 'sm.schedid = rh.schedid')
            ->get();
        if ($qry_bill_logs->num_rows() > 0) {
            foreach ($qry_bill_logs->result() as $row) {
                $get_payments = $this->db->select()->from('trn_payments_logs')->where(array('trntype' => 1, 'dataid' => $row->sysid))->get()->row();
                if ($get_payments) {
                    $total_amt_paid = ($get_payments->amtpd + $get_payments->amtintpd);
                } else {
                    $total_amt_paid = 0;
                }
                if ($total_amt_paid < $row->amt) {
                    $num_rows += 1;
                    $data['data'][] = array(
                        'mnum' => '<input name="month[]" type="hidden" class="" value="' . $row->rmonth . '" />' . $row->rmonth,
                        'byear' => '<input name="year[]" type="hidden" class="" value="' . $row->ryear . '" />' . $row->ryear,
                        'amt' => '<input name="amt[]" type="hidden" class="" value="' . $row->amt . '" />' . number_format($row->amt, 2),
                        'int' => '<input name="int[]" style="width: 70px !important;" class="form-control inline input-xs" value="' . round(get_billing_interest($row->duedate, $row->amt), 2, PHP_ROUND_HALF_ODD) . '" />',
                        'vat' => '<input name="vat[]" style="width: 70px !important;" class="form-control inline input-xs" value="0" />',
                        'net' => '<input name="net[]"  style="width: 100% !important;" class="form-control inline input-xs" value="0" />',
                        'frtx' => '<input name="frtx[]" style="width: 60px !important;" class="form-control inline input-xs" value="0" />',
                        'chk' => '<input name="chk[]"  style="width: 30px !important;" class="form-control inline input-xs" value="" />',
                        'remarks' => '',
                        'control' => '<i class="fa fa-times text-danger btn-remove"></i>'
                    );
                    $balance = $row->amt - $total_amt_paid;
                    if ($balance > 0) {
                        $num_bill += 1;
                        $armon[] = array(
                            'mo' => $row->billmonth,
                            'amt' => $balance,
                            'duedate' => $row->duedate,
                            'isdue' => get_billing_passdue($row->duedate)
                        );
                    }
                }
            }
        }

        // GET MIN AMT
        $amt_min = 0;
        $amt_cur = 0;
        foreach ($armon as $row) {
            if ($row['isdue'] == true) {
                $amt_min += $row['amt'] + get_billing_interest($row['duedate'], $row['amt']);
            }
            if ($row['isdue'] == false) {
                $amt_cur += $row['amt'];
            }
        }

        $amt_total = $amt_min + $amt_cur;
        $data['curamtdisp'] = number_format($amt_cur, 2);
        $data['curamt'] = $amt_cur;
        $data['minamtdisp'] = number_format($amt_min, 2);
        $data['minamt'] = $amt_min;
        $data['amttotal'] = round($amt_total, 3);
        $data['amttotaldisp'] = number_format($amt_total, 2);
        $data['armon'] = $armon;
        $data['input'] = $this->input->post();
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_rows;
        $data['iTotalDisplayRecords'] = $num_rows;
        return json_encode($data);
    }

    function get_billing_reg() {
        $input_yr = $this->input->post('year');
        $yr = ($input_yr) ? $input_yr : date('Y');
        $this_month = date('m');
        $num_rows = 13;
        $balance = 0;
        $num_bill = 0;
        $num_pay = 0;
        $armon = array();
        for ($i = 1; $i <= $num_rows; $i++) {
            $amt_pd = 0;
            $date_pd = '';
            $kwh = 0;



            // ======================================================
            // QUERY BILLING LOGS
            $qry_bill_logs = $this->db->select('rh.sysid, sm.rmonth, sm.ryear, l.duedate, l.prevreadid, l.presreadid')
                ->from('trn_billing_logs AS l')
                ->join('trn_reading_history AS rh', 'l.presreadid = rh.sysid')
                ->join('reading_schedule_main AS sm', 'sm.sysid = rh.schedid')
                ->where(array('sm.ryear' => $yr, 'sm.rmonth' => $i))
                ->get()->row();



            if ($qry_bill_logs) {
                $kwh = get_reading_kwh($qry_bill_logs->prevreadid, $qry_bill_logs->presreadid);
                // QUERY PAYMENTS LOGS
                $qry_pay_logs = $this->db->select()
                    ->from('trn_payments_logs')
                    ->where(array('trntype' => 1, 'dataid' => $qry_bill_logs->sysid))
                    ->get()->row();
                if ($qry_pay_logs) {
                    $amt_pd = $qry_pay_logs->amtpd;
                    $date_pd = $qry_pay_logs->datecreated;
                    $balance = $qry_bill_logs->amt - $qry_pay_logs->amtpd;
                    if ($balance > 0) {
                        $num_bill += 1;
                        $armon[] = array(
                            'mo' => $qry_bill_logs->billmonth,
                            'amt' => $balance,
                            'duedate' => $qry_bill_logs->duedate,
                            'isdue' => get_billing_passdue($qry_bill_logs->duedate)
                        );
                    }
                } else {
                    $amt_pd = 0;
                    $balance = $qry_bill_logs->amt;
                    $date_pd = '';
                    $num_bill += 1;
                    $armon[] = array(
                        'mo' => $qry_bill_logs->billmonth,
                        'amt' => $balance,
                        'duedate' => $qry_bill_logs->duedate,
                        'isdue' => get_billing_passdue($qry_bill_logs->duedate)
                    );
                }
            }

            // ======================================================
            if ($qry_bill_logs == false) {
                // GET PREVIOUS BILLING UNPAID
                $yr_prev = ($yr - 1);
                // QUERY BILLING LOGS
                $qry_bill_logs = $this->db->select('rh.sysid, sm.rmonth, sm.ryear, l.duedate, l.prevreadid, l.presreadid')
                    ->from('trn_billing_logs AS l')
                    ->join('trn_reading_history AS rh', 'l.presreadid = rh.sysid')
                    ->join('reading_schedule_main AS sm', 'sm.sysid = rh.schedid')
                    ->where(array('sm.ryear' => $yr_prev, 'sm.rmonth' => $i))
                    ->get()->row();
                if ($qry_bill_logs) {
                    $kwh = get_reading_kwh($qry_bill_logs->presreadid, $qry_bill_logs->prevreadid);
                    // QUERY PAYMENTS LOGS
                    $qry_pay_logs = $this->db->select()
                        ->from('trn_payments_logs')
                        ->where(array('trntype' => 1, 'dataid' => $qry_bill_logs->sysid))
                        ->get()->row();
                    if ($qry_pay_logs) {
                        $amt_pd = $qry_pay_logs->amtpd;
                        $date_pd = $qry_pay_logs->datecreated;
                        $balance = $qry_bill_logs->amt - $qry_pay_logs->amtpd;
                        if ($balance > 0) {
                            $num_bill += 1;
                            $armon[] = array(
                                'mo' => $qry_bill_logs->rmonth,
                                'amt' => $balance,
                                'duedate' => $qry_bill_logs->duedate,
                                'isdue' => get_billing_passdue($qry_bill_logs->duedate)
                            );
                        }

                        if ($amt_pd > 0) {
                            $num_pay += 1;
                        }
                    } else {
                        $amt_pd = 0;
                        $balance = 0;
                        $date_pd = '';
                        $num_bill += 1;
                        $armon[] = array(
                            'mo' => $qry_bill_logs->rmonth,
                            'amt' => $balance,
                            'duedate' => $qry_bill_logs->duedate,
                            'isdue' => get_billing_passdue($qry_bill_logs->duedate)
                        );
                    }
                }
                // VAR
                $billno = ($qry_bill_logs) ? $qry_bill_logs->sysid : 0;
                $rate = '';
                $amt = 0;
                $duedate = ($qry_bill_logs) ? $qry_bill_logs->duedate : '';
                $int = ($amt_pd > 0) ? 0 : get_billing_interest($duedate, $amt);
            } else {
                // VAR
                $billno = ($qry_bill_logs) ? $qry_bill_logs->sysid : 0;
                $kwh = ($qry_bill_logs) ? $qry_bill_logs->kwh : 0;
                $rate = ($qry_bill_logs) ? $qry_bill_logs->rates : 0;
                $amt = 0;
                $duedate = ($qry_bill_logs) ? $qry_bill_logs->duedate : '';
                $int = ($amt_pd > 0) ? 0 : get_billing_interest($duedate, $amt);
            }

            if ($billno > 0) {
                $details_btn = ''
                    . '<a role="button" '
                    . 'class="popovers" '
                    . 'data-trigger="click" '
                    . 'data-container="body" '
                    . 'data-placement="left" '
                    . 'data-content="'
                    . '<a href=\'\' target=\'_blank\' class=\'btn btn-primary btn-xs\'><i class=\'fa fa-file\'></i> Statement</a>'
                    . '<a href=\'\' target=\'_blank\' class=\'btn btn-default btn-xs\'>Account</a>  " '
                    . 'data-original-title="<span class=\'text-danger\'><i class=\'fa fa-file-text\'></i> Billing Details</span>"><i class="fa fa-search"><i></a>';
            } else {
                $details_btn = '';
            }

            if ($amt_pd > 0) {
                $date_pd_format = strtotime($date_pd);
                $date_pd = date('Y-m-d', $date_pd_format);
            } else {
                $date_pd = '';
            }

            if ($i <= 12) {

                $this_curr = ($this_month == $i) ? true : false;
                $data['data'][] = array(
                    'mnum' => $i,
                    'mname' => date_formating($i, 'm', 'F'),
                    'billno' => ($billno > 0) ? str_pad($billno, 8, '0', STR_PAD_LEFT) : '',
                    'kwh' => number_format($kwh, 2),
                    'amt' => number_format($amt, 2),
                    'int' => number_format($int, 2),
                    'duedate' => $duedate,
                    'amtpd' => number_format($amt_pd, 2),
                    'datepd' => $date_pd,
                    'rem' => '',
                    'curm' => $this_curr,
                    'details' => $details_btn
                );
            } else {
                $data['data'][] = array(
                    'mnum' => 13,
                    'mname' => 'Previous Yr.',
                    'billno' => '',
                    'kwh' => '',
                    'amt' => number_format($amt * 3, 2),
                    'int' => number_format(0, 2),
                    'duedate' => '',
                    'amtpd' => number_format($amt * 3, 2),
                    'datepd' => $date_pd,
                    'rem' => '',
                    'curm' => false,
                    'details' => $details_btn,
                );
            }
        }

        // GET MIN AMT
        $amt_min = 0;
        $amt_cur = 0;
        foreach ($armon as $row) {
            if ($row['isdue'] == true) {
                $amt_min += $row['amt'] + get_billing_interest($row['duedate'], $row['amt']);
            }
            if ($row['isdue'] == false) {
                $amt_cur += $row['amt'];
            }
        }

        $amt_total = $amt_min + $amt_cur;
        $data['curamtdisp'] = number_format($amt_cur, 2);
        $data['curamt'] = $amt_cur;
        $data['minamtdisp'] = number_format($amt_min, 2);
        $data['minamt'] = $amt_min;
        $data['amttotal'] = $amt_total;
        $data['amttotaldisp'] = number_format($amt_total, 2);
        $data['armon'] = $armon;
        $data['numbill'] = $num_bill;
        $data['numpay'] = $num_pay;
        $data['input'] = $this->input->post();
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_rows;
        $data['iTotalDisplayRecords'] = $num_rows;
        return json_encode($data);
    }

    function get_search_key() {
        $data = array();
        $search_key = $this->input->post('searchkey');
        $search_key_length = strlen($search_key);

        $fdo_num = 0;
        $active_num = 0;
        // LEGACY SEARCH
        if($search_key_length >= 3) {
            $qry_legacy = $this->db->select('am.servicenumber, am.mtr, nl.name, aa.addrspecific, am.status')
                ->from('customer_accounts_name_legacy AS nl')
                ->join('customer_accounts_main AS am', 'am.ownerid = nl.sysid AND am.types = 5')
                ->join('customer_accounts_address AS aa', 'aa.acctid = am.sysid', 'left')
                ->or_like('nl.name', $search_key)
                ->or_like('am.servicenumber', $search_key)
                ->order_by('am.status', 'desc')
                ->order_by('am.servicenumber', 'asc')
                ->order_by('nl.name', 'asc')
                ->get();
            $num_rows = $qry_legacy->num_rows();
            if ($num_rows > 0) {
                $num = 1;
                foreach ($qry_legacy->result() as $row) {
                    if($row->status==1) {
                        $active_num += 1;
                    }else{
                        $fdo_num += 1;
                    }
                    $data['res'][] = array(
                        'num' => $num++,
                        'servno' => highlightkeyword($row->servicenumber, $search_key),
                        'name' => highlightkeyword($row->name, $search_key),
                        'address' => $row->addrspecific, $search_key,
                        'control' => '<button id="btn_search_submit" data-servno="'.$row->servicenumber.'" data-mtr="'.$row->mtr.'" class="btn btn-default btn-xs"><i class="fa fa-sign-out"></i></button>',
                        'fdo' => ($row->status==0) ? true : false,
                    );
                }
            }
        }
        $data['num'] = $num_rows;
        $data['fdo'] = $fdo_num;
        $data['act'] = $active_num;
        return json_encode($data);
    }

    function pay_legal() {
        $data = array();
        $qry = false;
        $func = 'error';
        $msg = 'Commit Rollback';
        $input = $this->input->post();
        $appid = $this->input->post('appid');
        $moduleid = $this->input->post('moduleid');
        $payable = $this->input->post('payable');
        $total = 0;

        $chk_arr = $this->input->post('chk');
        $amt_rec = $this->input->post('amtrec');
        $input_check = $this->input->post('amtchk');
        $input_cash = $this->input->post('amtcash');
        $input_vat = $this->input->post('vatarr');
        $input_cwt = $this->input->post('frtx');
        $frtx_arr = $this->input->post('frtx');

        $printserial = 'TEST0000032';

        $this->db->trans_begin();

        $total_amt_topay = 0;

        $orno = get_orno();
        $trnno = get_trnno();

        $pay_ins = 0;

        // GET CHECKED CHECK
        $total_chk_input = 0;
        if($payable && is_array($payable)) {
            foreach ($payable as $trnid => $crow) {

                if(isset($chk_arr[$trnid]) && $chk_arr[$trnid] > 0) {
                    $payform = 2;
                }else{
                    $payform = 1;
                }

                $amt = $crow;

                $vat_amt = (isset($input_vat[$trnid]) && $input_vat[$trnid] > 0) ? $input_vat[$trnid] : 0;
                $cwt_amt = (isset($input_cwt[$trnid]) && $input_cwt[$trnid] > 0) ? $input_cwt[$trnid] : 0;
                $no_vat_amt = bcdiv($amt, 1.12, 2);
                $vat_sale = bcsub($amt, $vat_amt, 2);
                $total = $amt;

                $ins_arr = array(
                    'orno' => $orno,
                    'servno' => null,
                    'mtr' => 1,
                    'dataid' => $appid,
                    'moduleid' => $moduleid,
                    'vatsales' => $vat_sale,
                    'vattaxamt' => $vat_amt,
                    'vattype' => 0,
                    'subtotal' => $no_vat_amt,
                    'franchisetax' => $cwt_amt,
                    'vatzerorated' => 0,
                    'vatexempt' => 0,
                    'nonvatsales' => $no_vat_amt,
                    'totalamt' => $total,
                    'payform' => $payform,
                    'trnno' => $trnno,
                    'amtrec' => $amt_rec,
                    'printerserial' => $printserial,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'payforacctno' => 146,
                    'refid' => $trnid,
                    'groups' => 1
                );
                $payments = insert_paylogs($ins_arr);
                $data['payins'][] = $payments;
                $payments_ids[] = $payments['id'];
                if ($payments && $payments['id'] > 0) {
                    $pay_ins += 1;
                }

            }

        }

        if($pay_ins > 0) {
            $data = db_trans($this->db);


            $computername = $this->input->post('computername');
            $groups = $this->db->select('groups')->from('transaction_payments_logs')
                ->where_in('sysid', $payments_ids)
                ->group_by('groups')
                ->get();
            if ($groups->num_rows() > 0) {
                foreach ($groups->result() as $row) {
                    $receipt = $this->db->select()->from('transaction_payments_logs')
                        ->where('groups', $row->groups)
                        ->where_in('sysid', $payments_ids)
                        ->get();
                    $trn_cnt = $receipt->num_rows();
                    //$trn_cnt = 0;
                    if ($trn_cnt > 0) {
                        $conn = windows_printer_connector($computername);
                        // CHECK IF CONNECTION STATUS IS TRUE
                        if ($conn->res == true) {

                            $printer = $conn->printer;


                            // TNO
                            $info = get_ticket_logs_details($appid);

                            if($info->acctid > 0) {
                                $acct_info = get_active_account_info($info->acctid);
                                $servno = $acct_info->servicenumber;
                                $address = $acct_info->address;
                                $name = $acct_info->name;
                            } else {
                                $name = $info->firstname . ' ' . $info->info->lastname;
                                $address = $info->addrspec;
                                $servno = str_pad($appid, 8, '0', STR_PAD_LEFT);
                            }


                            receipt_header($printer);
                            /* Name of Account Paid */
                            $printer->setJustification($printer::JUSTIFY_LEFT);
                            $printer->text(two_cols("Service No.: " . $servno, "TIN: N/A"));
                            $printer->text("Name: " . $name . "\n");
                            $printer->text("Address: " . $address . "\n");
                            $printer->feed();

                            /* Title of receipt */
                            $printer->setJustification($printer::JUSTIFY_CENTER);
                            $printer->setEmphasis(true);
                            $printer->setUnderline(true);
                            $printer->text(space_both_sides("O F F I C I A L   R E C E I P T"));
                            $printer->setEmphasis(false);
                            $printer->setUnderline(false);

                            /* RECEIPT CONTENTS */
                            /* Top Table */
                            $printer->setJustification($printer::JUSTIFY_LEFT);
                            $printer->setUnderline(true);
                            $printer->text(two_cols('Transaction', 'Amount'));
                            $printer->setUnderline(false);

                            // PAYMENTS DETAILS
                            $vatsales_amt = 0;
                            $vattax_amt = 0;
                            $sabtotal_amt = 0;
                            $vatzerorated_amt = 0;
                            $vatexempt_amt = 0;
                            $nonvatsales_amt = 0;
                            $cwt_amt = 0;
                            $total_amt = 0;

                            $cash_cnt = 0;
                            $check_cnt = 0;

                            foreach ($receipt->result() as $rows) {
                                $vatsales_amt += $rows->vatsales;
                                $sabtotal_amt += $rows->subtotal;
                                $vattax_amt += $rows->vattaxamt;
                                $vatzerorated_amt += $rows->vatzerorated;
                                $vatexempt_amt += $rows->vatexempt;
                                $nonvatsales_amt += $rows->nonvatsales;
                                $cwt_amt += $rows->franchisetax;
                                $total_amt += $rows->totalamt;
                                $acct_name = get_name_chart_of_accounts($rows->payforacctno);

                                $string = strip_tags($acct_name->descs);
                                if (strlen($string) > 25) {

                                    // truncate string
                                    $stringCut = substr($string, 0, 25);
                                    $endPoint = strrpos($stringCut, ' ');

                                    //if the string doesn't contain any space then it will cut without word basis.
                                    $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                    $string .= '...';
                                }
                                $check = false;
                                if ($rows->payform == 2) {
                                    $check = true;
                                    $check_cnt += 1;
                                }
                                if ($rows->payform == 1) {
                                    $cash_cnt += 1;
                                }

                                $printer->text(two_cols_a($string, number_format($rows->totalamt, 2), $check));
                            }

                            // CHECK PAYMENT FORM
                            if ($cash_cnt > 0 && $check_cnt > 0) {
                                $payform = 'Cash/Check';
                            } else {
                                if ($cash_cnt > 0) {
                                    $payform = 'Cash';
                                } else {
                                    $payform = 'Check';
                                }
                            }

                            // ##############################
                            $printer->setUnderline(true);
                            $printer->text(space_both_sides("Form of Payment: " . $payform));
                            $printer->setUnderline(false);
                            $printer->setEmphasis(true);
                            $printer->text("Tax Breakdown: \n");
                            $printer->setEmphasis(false);
                            $printer->text(three_cols('VAT Sales', 'VAT Tax Amt', 'Sub-Total'));
                            $printer->text(three_cols(number_format($vatsales_amt, 2), number_format($vattax_amt, 2), number_format($sabtotal_amt, 2)));
                            $printer->text(two_cols("ZERO RATED TAX", number_format($vatzerorated_amt, 2)));
                            $printer->text(two_cols("VAT EXEMPT", number_format($vatexempt_amt, 2)));
                            $printer->text(two_cols("CWT", number_format($cwt_amt, 2)));
                            $printer->text(two_cols("NON VAT SALES", number_format($nonvatsales_amt, 2)));
                            $printer->setUnderline(true);
                            $printer->setEmphasis(true);
                            $printer->text(two_cols("Total", number_format($total_amt, 2)));
                            $printer->setEmphasis(false);
                            $printer->setUnderline(false);
                            $printer->feed();
                            /* END OF RECEIPT CONTENTS */
                            receipt_footer($printer, $rows->orno, $rows->trnno);

                            $printer->feed();
                            $printer->cut();
                            $printer->pulse();
                            $printer->close();

                        }

                    }
                }

            }
        }
        $data['inputs'] = $this->input->post();

        return (object) $data;
    }
    function pay_cad() {
        $data = array();
        $qry = false;
        $func = 'error';
        $msg = 'Commit Rollback';
        $input = $this->input->post();
        $appid = $this->input->post('appid');
        $moduleid = $this->input->post('moduleid');
        $total = 0;

        $chk_arr = $this->input->post('chk');
        $amt_rec = $this->input->post('amtrec');
        $input_check = $this->input->post('amtchk');
        $input_cash = $this->input->post('amtcash');
        $frtx_arr = $this->input->post('frtx');

        $printserial = 'TEST0000032';

        $this->db->trans_begin();

        $total_amt_topay = 0;

        $payments_ids = array();

        $query = $this->db->select('c.sysid, c.chargeid, a.codes, a.descs, c.amt, c.vatamt, c.vattype, a.groups')
            ->from('application_customers_charges AS c')
            ->join('prime_chart_of_accounts AS a', 'a.sysid = c.chargeid')
            ->where(array('c.appid' => $appid, 'c.moduleid' => $moduleid, 'c.status' => 1))
            ->order_by('CAST(c.datecreated AS DATE)', 'asc')
            ->order_by('a.groups')
            ->order_by('a.codes')
            ->get();

        // VARIABLES
        $amt_chk = $this->input->post('amtchk');
        // GET CHECKED CHECK
        $chk_input = $this->input->post("chk");
        $total_chk_input = 0;
        if($chk_input && is_array($amt_chk)) {
            foreach($amt_chk as $crow) {
                $total_chk_input += $crow;
            }
        }

        $orno = get_orno();
        $trnno = get_trnno();
        $orno_group_3 = $orno + 2;
        $orno_group_2 = $orno + 1;

        if($query->num_rows() > 0) {
            foreach($query->result() as $col => $row) {
                $payments = array();
                if(check_account_payement($appid, $moduleid, $row->chargeid) == false) {
                    $amt = $row->amt;

                    $frtx  = (isset($frtx_arr[$row->chargeid]) && $frtx_arr[$row->chargeid] > 0) ? $frtx_arr[$row->chargeid] : 0;

                    if($row->groups==3) {
                        $orno = $orno_group_3;
                    }
                    if($row->groups==2) {
                        $orno = $orno_group_2;
                    }


                    if ($row->vattype == 1) {
                        $no_vat_amt = bcdiv($amt, 1.12, 2);
                        $vat_amt = bcsub($amt, $no_vat_amt, 2);
                        $total = bcadd($no_vat_amt, $vat_amt, 2);
                    } else {
                        $no_vat_amt = $amt;
                        $vat_amt = bcmul($amt, 0.12, 2);
                        $total = $amt + $vat_amt;
                    }

                    $vatsales_row = bcdiv($vat_amt, 0.12, 2);
                    $sabtotal_row = bcadd($vat_amt, $vatsales_row, 2);
                    if($row->vattype == 1) {
                        $nonvatsales_row    = bcsub($total, $vat_amt, 2);
                    }else{
                        $nonvatsales_row 	= bcsub($total, $sabtotal_row, 2);
                    }


                    //if ($chk_arr && isset($chk_arr[$row->sysid])) { }
                    if (isset($chk_arr[$row->chargeid]) && $chk_arr[$row->chargeid]) {
                        $new_total_amt = $total - $frtx;
                        $check_bal = $amt_chk - $amt;
                        $data['payments'][] = array('code' => $row->chargeid, 'amt' => $check_bal, 'frtx' => $frtx, 'mode' => 'Check');
                        $ins_arr = array(
                            'orno' => $orno,
                            'servno' => null,
                            'mtr' => 1,
                            'dataid' => $appid,
                            'moduleid' => $moduleid,
                            'vatsales' => $vatsales_row,
                            'vattaxamt' => $vat_amt,
                            'vattype' => $row->vattype,
                            'subtotal' => $sabtotal_row,
                            'franchisetax' => $frtx,
                            'vatzerorated' => 0,
                            'vatexempt' => 0,
                            'nonvatsales' => $nonvatsales_row,
                            'totalamt' => $new_total_amt,
                            'payform' => 2,
                            'trnno' => $trnno,
                            'amtrec' => $amt_rec,
                            'printerserial' => $printserial,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                            'payforacctno' => $row->chargeid,
                            'groups' => $row->groups
                        );
                        $payments = insert_paylogs($ins_arr);
                        $data['payins'][] = $payments;
                        $payments_ids[] = $payments['id'];
                    }else{
                        $new_total_amt = $total - $frtx;
                        if($amt_chk > 0) {
                            $check_bal = $amt_chk - $amt;
                        }else{
                            $check_bal = $amt;
                        }
                        $data['payments'][] = array('code' => $row->chargeid, 'amt' => $check_bal, 'frtx' => $frtx, 'mode' => 'Check');
                        $ins_arr = array(
                            'orno' => $orno,
                            'servno' => null,
                            'mtr' => 1,
                            'dataid' => $appid,
                            'moduleid' => $moduleid,
                            'vatsales' => $vatsales_row,
                            'vattaxamt' => $vat_amt,
                            'vattype' => $row->vattype,
                            'subtotal' => $sabtotal_row,
                            'franchisetax' => $frtx,
                            'vatzerorated' => 0,
                            'vatexempt' => 0,
                            'nonvatsales' => $nonvatsales_row,
                            'totalamt' => $new_total_amt,
                            'payform' => 1,
                            'trnno' => $trnno,
                            'amtrec' => $amt_rec,
                            'printerserial' => $printserial,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                            'payforacctno' => $row->chargeid,
                            'groups' => $row->groups
                        );
                        $payments = insert_paylogs($ins_arr);
                        $data['payins'][] = $payments;
                        $payments_ids[] = $payments['id'];
                    }

                    /*
                    if($amt_chk > 0) {
                            if ($amt_chk < $total) {
                                if ($amt_chk > 0) {
                                    // INSERT CHECK
                                    $amt_check = $amt_chk;
                                    $percent_of_pay = $amt_check / $total;
                                    $new_sabtotal_row = $sabtotal_row * $percent_of_pay;
                                    $new_nonvat_sales = $nonvatsales_row * $percent_of_pay;
                                    $new_vat_sales = $vatsales_row * $percent_of_pay;
                                    $new_vat_amt = $vat_amt * $percent_of_pay;
                                    $new_frxt = $frtx * $percent_of_pay;
                                    $new_total_amt = $amt_check - $new_frxt;
                                    $data['payments'][] = array('code' => $row->chargeid, 'amt' => $amt_check, 'frtx' => $new_frxt, 'mode' => 'Check');
                                    $ins_arr = array(
                                        'orno' => $orno,
                                        'servno' => null,
                                        'mtr' => 1,
                                        'dataid' => $appid,
                                        'moduleid' => $moduleid,
                                        'vatsales' => $new_vat_sales,
                                        'vattaxamt' => $new_vat_amt,
                                        'vattype' => $row->vattype,
                                        'subtotal' => $new_sabtotal_row,
                                        'franchisetax' => $new_frxt,
                                        'vatzerorated' => 0,
                                        'vatexempt' => 0,
                                        'nonvatsales' => $new_nonvat_sales,
                                        'totalamt' => $new_total_amt,
                                        'payform' => 2,
                                        'trnno' => $trnno,
                                        'amtrec' => $amt_rec,
                                        'printerserial' => $printserial,
                                        'createdby' => user_id(),
                                        'updatedby' => user_id(),
                                        'payforacctno' => $row->chargeid,
                                        'groups' => $row->groups
                                    );
                                    $payments = insert_paylogs($ins_arr);
                                    $data['payins'][] = $payments;
                                    $payments_ids[] = $payments['id'];

                                    // INSERT CASH
                                    $amt_cash = $total - $amt_check;
                                    $percent_of_pay = $amt_cash / $total;
                                    $new_sabtotal_row = $sabtotal_row * $percent_of_pay;
                                    $new_nonvat_sales = $nonvatsales_row * $percent_of_pay;
                                    $new_vat_sales = $vatsales_row * $percent_of_pay;
                                    $new_vat_amt = $vat_amt * $percent_of_pay;
                                    $new_frxt = $frtx * $percent_of_pay;
                                    $new_total_amt = $amt_cash - $new_frxt;
                                    $data['payments'][] = array('code' => $row->chargeid, 'amt' => $amt_cash, 'frtx' => $new_frxt, 'mode' => 'Cash');
                                    $ins_arr = array(
                                        'orno' => $orno,
                                        'servno' => null,
                                        'mtr' => 1,
                                        'dataid' => $appid,
                                        'moduleid' => $moduleid,
                                        'vatsales' => $new_vat_sales,
                                        'vattaxamt' => $new_vat_amt,
                                        'vattype' => $row->vattype,
                                        'subtotal' => $new_sabtotal_row,
                                        'franchisetax' => $new_frxt,
                                        'vatzerorated' => 0,
                                        'vatexempt' => 0,
                                        'nonvatsales' => $new_nonvat_sales,
                                        'totalamt' => $new_total_amt,
                                        'payform' => 1,
                                        'trnno' => $trnno,
                                        'amtrec' => $amt_rec,
                                        'printerserial' => $printserial,
                                        'createdby' => user_id(),
                                        'updatedby' => user_id(),
                                        'payforacctno' => $row->chargeid,
                                        'groups' => $row->groups
                                    );
                                    $payments = insert_paylogs($ins_arr);
                                    $data['payins'][] = $payments;
                                    $payments_ids[] = $payments['id'];
                                }
                            } else {
                                $new_total_amt = $total - $frtx;
                                $check_bal = $amt_chk - $amt;
                                $data['payments'][] = array('code' => $row->chargeid, 'amt' => $check_bal, 'frtx' => $frtx, 'mode' => 'Check');
                                $ins_arr = array(
                                    'orno' => $orno,
                                    'servno' => null,
                                    'mtr' => 1,
                                    'dataid' => $appid,
                                    'moduleid' => $moduleid,
                                    'vatsales' => $vatsales_row,
                                    'vattaxamt' => $vat_amt,
                                    'vattype' => $row->vattype,
                                    'subtotal' => $sabtotal_row,
                                    'franchisetax' => $frtx,
                                    'vatzerorated' => 0,
                                    'vatexempt' => 0,
                                    'nonvatsales' => $nonvatsales_row,
                                    'totalamt' => $new_total_amt,
                                    'payform' => 2,
                                    'trnno' => $trnno,
                                    'amtrec' => $amt_rec,
                                    'printerserial' => $printserial,
                                    'createdby' => user_id(),
                                    'updatedby' => user_id(),
                                    'payforacctno' => $row->chargeid,
                                    'groups' => $row->groups
                                );
                                $payments = insert_paylogs($ins_arr);
                                $data['payins'][] = $payments;
                                $payments_ids[] = $payments['id'];
                            }

                    } else{
                        $new_total_amt = $total - $frtx;
                        $data['payments'][] = array('code' => $row->chargeid, 'amt' => $total, 'frtx' => $frtx, 'mode' => 'Cash');
                        $ins_arr = array(
                            'orno' => $orno,
                            'servno' => null,
                            'mtr' => 1,
                            'dataid' => $appid,
                            'moduleid' => $moduleid,
                            'vatsales' => $vatsales_row,
                            'vattaxamt' => $vat_amt,
                            'vattype' => $row->vattype,
                            'subtotal' => $sabtotal_row,
                            'franchisetax' => $frtx,
                            'vatzerorated' => 0,
                            'vatexempt' => 0,
                            'nonvatsales' => $nonvatsales_row,
                            'totalamt' => $new_total_amt,
                            'payform' => 1,
                            'trnno' => $trnno,
                            'amtrec' => $amt_rec,
                            'printerserial' => $printserial,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                            'payforacctno' => $row->chargeid,
                            'groups' => $row->groups
                        );
                        $payments = insert_paylogs($ins_arr);
                        $data['payins'][] = $payments;
                        $payments_ids[] = $payments['id'];
                    }
                    */


                    if($amt_chk > 0) {
                        $amt_chk -= $amt;
                    }

                    $total_amt_topay += ($total - $frtx);
                    $data['amt'][] = $amt;
                }
                $qry = true;
            }



            //$this->db->trans_commit();
            if($qry == true) {
                $computername = $this->input->post('computername');
                $groups = $this->db->select('groups')->from('transaction_payments_logs')
                    ->where_in('sysid', $payments_ids)
                    ->group_by('groups')
                    ->get();
                if ($groups->num_rows() > 0) {
                    foreach ($groups->result() as $row) {
                        $receipt = $this->db->select()->from('transaction_payments_logs')
                            ->where('groups', $row->groups)
                            ->where_in('sysid', $payments_ids)
                            ->get();
                        $trn_cnt = $receipt->num_rows();
                        //$trn_cnt = 0;
                        if ($trn_cnt > 0) {
                            $conn = windows_printer_connector($computername);
                            // CHECK IF CONNECTION STATUS IS TRUE
                            if ($conn->res == true) {

                                $printer = $conn->printer;
                                if($moduleid != 35) {
                                    // JOB ORDERS
                                    $info = get_joborder_info($appid);

                                    if($info->types == 5) {
                                        $qry_acctname = $this->db->select()->from('customer_accounts_name_legacy')
                                            ->where(array('sysid' => $info->ownerid))
                                            ->get()->row();
                                        $ownername = ($qry_acctname) ? $qry_acctname->name : 'N/A';
                                    }else{

                                        $qry_acctname = $this->db->select()->from('person')
                                            ->where(array('sysid' => $info->ownerid))
                                            ->get()->row();
                                        $ownername = ($qry_acctname) ? $qry_acctname->lastname.', '.$qry_acctname->firstname : 'N/A';
                                    }
                                    $servno = $info->servicenumber;
                                    $name = $ownername;
                                    $address = $info->addrspecific;
                                }else {
                                    // TNO
                                    $info = get_application_details($appid);
                                    $servno = ($info->info->servno != '') ? $info->info->servno : str_pad($appid, 8, '0', STR_PAD_LEFT);
                                    $name = $info->info->firstname . ' ' . $info->info->lastname;
                                    $address = $info->info->addrspec;
                                }


                                receipt_header($printer);
                                /* Name of Account Paid */
                                $printer->setJustification($printer::JUSTIFY_LEFT);
                                $printer->text(two_cols("Service No.: " . $servno, "TIN: N/A"));
                                $printer->text("Name: " . $name . "\n");
                                $printer->text("Address: " . $address . "\n");
                                $printer->feed();

                                /* Title of receipt */
                                $printer->setJustification($printer::JUSTIFY_CENTER);
                                $printer->setEmphasis(true);
                                $printer->setUnderline(true);
                                $printer->text(space_both_sides("O F F I C I A L   R E C E I P T"));
                                $printer->setEmphasis(false);
                                $printer->setUnderline(false);

                                /* RECEIPT CONTENTS */
                                /* Top Table */
                                $printer->setJustification($printer::JUSTIFY_LEFT);
                                $printer->setUnderline(true);
                                $printer->text(two_cols('Transaction', 'Amount'));
                                $printer->setUnderline(false);

                                // PAYMENTS DETAILS
                                $vatsales_amt = 0;
                                $vattax_amt = 0;
                                $sabtotal_amt = 0;
                                $vatzerorated_amt = 0;
                                $vatexempt_amt = 0;
                                $nonvatsales_amt = 0;
                                $cwt_amt = 0;
                                $total_amt = 0;

                                $cash_cnt = 0;
                                $check_cnt = 0;

                                foreach ($receipt->result() as $rows) {
                                    $vatsales_amt += $rows->vatsales;
                                    $sabtotal_amt += $rows->subtotal;
                                    $vattax_amt += $rows->vattaxamt;
                                    $vatzerorated_amt += $rows->vatzerorated;
                                    $vatexempt_amt += $rows->vatexempt;
                                    $nonvatsales_amt += $rows->nonvatsales;
                                    $cwt_amt += $rows->franchisetax;
                                    $total_amt += $rows->totalamt;
                                    $acct_name = get_name_chart_of_accounts($rows->payforacctno);

                                    $string = strip_tags($acct_name->descs);
                                    if (strlen($string) > 25) {

                                        // truncate string
                                        $stringCut = substr($string, 0, 25);
                                        $endPoint = strrpos($stringCut, ' ');

                                        //if the string doesn't contain any space then it will cut without word basis.
                                        $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                        $string .= '...';
                                    }
                                    $check = false;
                                    if ($rows->payform == 2) {
                                        $check = true;
                                        $check_cnt += 1;
                                    }
                                    if ($rows->payform == 1) {
                                        $cash_cnt += 1;
                                    }

                                    $printer->text(two_cols_a($string, number_format($rows->totalamt, 2), $check));
                                }

                                // CHECK PAYMENT FORM
                                if ($cash_cnt > 0 && $check_cnt > 0) {
                                    $payform = 'Cash/Check';
                                } else {
                                    if ($cash_cnt > 0) {
                                        $payform = 'Cash';
                                    } else {
                                        $payform = 'Check';
                                    }
                                }

                                // ##############################
                                $printer->setUnderline(true);
                                $printer->text(space_both_sides("Form of Payment: " . $payform));
                                $printer->setUnderline(false);
                                $printer->setEmphasis(true);
                                $printer->text("Tax Breakdown: \n");
                                $printer->setEmphasis(false);
                                $printer->text(three_cols('VAT Sales', 'VAT Tax Amt', 'Sub-Total'));
                                $printer->text(three_cols(number_format($vatsales_amt, 2), number_format($vattax_amt, 2), number_format($sabtotal_amt, 2)));
                                $printer->text(two_cols("ZERO RATED TAX", number_format($vatzerorated_amt, 2)));
                                $printer->text(two_cols("VAT EXEMPT", number_format($vatexempt_amt, 2)));
                                $printer->text(two_cols("CWT", number_format($cwt_amt, 2)));
                                $printer->text(two_cols("NON VAT SALES", number_format($nonvatsales_amt, 2)));
                                $printer->setUnderline(true);
                                $printer->setEmphasis(true);
                                $printer->text(two_cols("Total", number_format($total_amt, 2)));
                                $printer->setEmphasis(false);
                                $printer->setUnderline(false);
                                $printer->feed();
                                /* END OF RECEIPT CONTENTS */
                                receipt_footer($printer, $rows->orno, $rows->trnno);

                                $printer->feed();
                                $printer->cut();
                                $printer->pulse();
                                $printer->close();

                            }

                        }
                    }
                }
            }


            if($total_amt_topay>0) {
                if ($input_check > 0 && $input_cash > 0) {
                    $total_amt_cash = $total_amt_topay - $input_check;
                    if (round($total_amt_cash, 2) > round($input_cash, 2)) {
                        $msg = 'Input cash is not enough! :  ' . $input_cash . ' < ' . $total_amt_cash;
                        $func = 'warning';
                        $this->db->trans_rollback();
                        $qry = false;
                    } else {
                        if ($this->db->trans_status() == true) {
                            $qry = true;
                            $this->db->trans_commit();
                        } else {
                            $msg = 'Error Query';
                            $func = 'error';
                            $this->db->trans_rollback();
                            $qry = false;
                        }
                    }
                } else {
                    if ($input_check > 0 && $input_check >= $total_amt_topay) {
                        if ($this->db->trans_status() == true) {
                            $qry = true;
                            $this->db->trans_commit();
                        } else {
                            $msg = 'Error Query';
                            $func = 'error';
                            $this->db->trans_rollback();
                            $qry = false;
                        }
                    } else {
                        if ($input_cash > 0 && $input_cash >= $total_amt_topay) {
                            if ($this->db->trans_status() == true) {
                                $qry = true;
                                $this->db->trans_commit();
                            } else {
                                $msg = 'Error Query';
                                $func = 'error';
                                $this->db->trans_rollback();
                                $qry = false;
                            }
                        } else {
                            $msg = 'Amount recieved is not enough!';
                            $func = 'info';
                            $this->db->trans_rollback();
                            $qry = false;
                        }
                    }
                }
            }
        }
        $data['pids'] = $payments_ids;
        $data['appid'] = $appid;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['total'] = $total;
        $data['amttopay'] = $total_amt_topay;
        $data['input'] = $input;
        return (object) $data;
    }

    function pay_bill() {
        $data = array();
        $acctid = $this->input->post('acctid');
        $mtr = $this->input->post('mtr');
        $moduleid = $this->input->post('moduleid');
        $amt_rec = $this->input->post('amtrec');
        $moyr_arr = $this->input->post('select');
        $net_pay = $this->input->post('netpay');
        $fr_tx = $this->input->post('frtx');
        $int_amt = $this->input->post('intamt');

        $payments_ids = array();
        $orno = get_orno();
        $trnno = get_trnno();

        // VARIABLES
        $amt_chk = doubleval($this->input->post('amtchk'));
        // GET CHECKED CHECK
        $chk_input = $this->input->post("chk");
        $total_chk_input = 0;
        if($chk_input && is_array($amt_chk)) {
            foreach($amt_chk as $crow) {
                $total_chk_input += $crow;
            }
        }


        if($moyr_arr && is_array($moyr_arr)) {
            foreach($moyr_arr as $key => $row) {
                $month = $key;
                $year = $row;
                $amtpd = (isset($net_pay[$key]) && $net_pay[$key] != '') ? $net_pay[$key] : 0;
                $amtpd = cleanData($amtpd);
                $frtx = floatval((isset($fr_tx[$key]) || $net_pay[$key] != '') ? $fr_tx[$key] : 0);

                $ar = get_ar_billing_details($acctid, $mtr, $year, $month);
                $total_ar_cur = ($ar) ? $ar->current : 0;
                $total_ar_vat = ($ar) ? $ar->totalvat : 0;

                if($total_ar_cur > $amtpd){
                    $percent_paid = bcdiv($amtpd, $total_ar_cur, 6);
                    $total_new_vat = bcmul($total_ar_vat, $percent_paid, 6);
                }else{
                    $percent_paid = 100;
                    $total_new_vat = $total_ar_vat;
                }

                $vatsales_row = bcdiv($total_new_vat, 0.12, 2);
                $sabtotal_row = bcadd($total_new_vat, $vatsales_row, 2);

                if( $frtx > 0 ){
                    $nonvatsales_row 	= bcadd(bcsub($amtpd, $sabtotal_row, 2), $frtx, 2);
                }else{
                    $nonvatsales_row 	= bcsub($amtpd, $sabtotal_row, 2);
                }

                $data['current'][] = array('amt' => $total_ar_cur, 'month' => $month, 'year' => $year);


                if($amt_chk >= 0) {
                    if($amt_chk > $amtpd)     {
                        $new_total_amt = ($amtpd - $frtx);
                        $ins_arr = array(
                            'orno' => $orno,
                            'servno' => null,
                            'mtr' => $mtr,
                            'dataid' => $acctid,
                            'moduleid' => $moduleid,
                            'vatsales' => $vatsales_row,
                            'vattaxamt' => $total_new_vat,
                            'vattype' => 1,
                            'subtotal' => $sabtotal_row,
                            'franchisetax' => $frtx,
                            'vatzerorated' => 0,
                            'vatexempt' => 0,
                            'nonvatsales' => $nonvatsales_row,
                            'totalamt' => $new_total_amt,
                            'payform' => 2,
                            'trnno' => $trnno,
                            'amtrec' => $amt_rec,
                            'printerserial' => 0000,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                            'payforacctno' => 114,
                            'groups' => 1
                        );
                        $payments = insert_paylogs($ins_arr);
                        $data['payments'][] = $payments;
                        $payments_ids[] = $payments['id'];

                    }else{
                        if($amt_chk > 0) {
                            // INSERT PART CHECK
                            $percent_of_pay     = bcdiv($amt_chk, $amtpd, 6);
                            $new_nonvat_sales   = bcmul($nonvatsales_row, $percent_of_pay, 6);
                            $new_vat_sales      = bcmul($vatsales_row, $percent_of_pay,6);
                            $new_vat_amt        = bcmul($total_new_vat, $percent_of_pay,6);
                            $new_subtotal_amt   = bcmul($sabtotal_row, $percent_of_pay,6);
                            $new_frtxt_amt      = bcmul($frtx, $percent_of_pay,6);
                            $new_total_amt      = ($amt_chk - $new_frtxt_amt);

                            $ins_arr = array(
                                'orno' => $orno,
                                'servno' => null,
                                'mtr' => $mtr,
                                'dataid' => $acctid,
                                'moduleid' => $moduleid,
                                'vatsales' => $new_vat_sales,
                                'vattaxamt' => $new_vat_amt,
                                'vattype' => 1,
                                'subtotal' => $new_subtotal_amt,
                                'franchisetax' => $new_frtxt_amt,
                                'vatzerorated' => 0,
                                'vatexempt' => 0,
                                'nonvatsales' => $new_nonvat_sales,
                                'totalamt' => $new_total_amt,
                                'payform' => 2,
                                'trnno' => $trnno,
                                'amtrec' => $amt_rec,
                                'printerserial' => 0000,
                                'createdby' => user_id(),
                                'updatedby' => user_id(),
                                'payforacctno' => 114,
                                'groups' => 1
                            );
                            $payments = insert_paylogs($ins_arr);
                            $data['payments'][] = $payments;
                            $payments_ids[] = $payments['id'];

                            // INSERT PART CASH
                            $amt_cash = $amtpd - $amt_chk;
                            $percent_of_pay     = bcdiv($amt_cash, $amtpd, 6);
                            $new_nonvat_sales   = bcmul($nonvatsales_row, $percent_of_pay, 6);
                            $new_vat_sales      = bcmul($vatsales_row, $percent_of_pay,6);
                            $new_vat_amt        = bcmul($total_new_vat, $percent_of_pay,6);
                            $new_subtotal_amt   = bcmul($sabtotal_row, $percent_of_pay,6);
                            $new_frtxt_amt      = bcmul($frtx, $percent_of_pay,6);
                            $new_total_amt      = ($amt_cash - $new_frtxt_amt);

                            $ins_arr = array(
                                'orno' => $orno,
                                'servno' => null,
                                'mtr' => $mtr,
                                'dataid' => $acctid,
                                'moduleid' => $moduleid,
                                'vatsales' => $new_vat_sales,
                                'vattaxamt' => $new_vat_amt,
                                'vattype' => 1,
                                'subtotal' => $new_subtotal_amt,
                                'franchisetax' => $new_frtxt_amt,
                                'vatzerorated' => 0,
                                'vatexempt' => 0,
                                'nonvatsales' => $new_nonvat_sales,
                                'totalamt' => $new_total_amt,
                                'payform' => 1,
                                'trnno' => $trnno,
                                'amtrec' => $amt_rec,
                                'printerserial' => 0000,
                                'createdby' => user_id(),
                                'updatedby' => user_id(),
                                'payforacctno' => 114,
                                'groups' => 1
                            );
                            $payments = insert_paylogs($ins_arr);
                            $data['payments'][] = $payments;
                            $payments_ids[] = $payments['id'];
                        }else{

                            $new_total_amt      = ($amtpd - $frtx);
                            $ins_arr = array(
                                'orno' => $orno,
                                'servno' => null,
                                'mtr' => $mtr,
                                'dataid' => $acctid,
                                'moduleid' => $moduleid,
                                'vatsales' => $vatsales_row,
                                'vattaxamt' => $total_new_vat,
                                'vattype' => 1,
                                'subtotal' => $sabtotal_row,
                                'franchisetax' => $frtx,
                                'vatzerorated' => 0,
                                'vatexempt' => 0,
                                'nonvatsales' => $nonvatsales_row,
                                'totalamt' => $new_total_amt,
                                'payform' => 1,
                                'trnno' => $trnno,
                                'amtrec' => $amt_rec,
                                'printerserial' => 1,
                                'createdby' => user_id(),
                                'updatedby' => user_id(),
                                'payforacctno' => 114,
                                'groups' => 1
                            );
                            $payments = insert_paylogs($ins_arr);
                            $data['payments'][] = $payments;
                            $data['err'][] = $payments['err'];
                            $payments_ids[] = $payments['id'];
                        }
                    }
                }else{

                    $new_total_amt      = ($amtpd - $frtx);
                    $ins_arr = array(
                        'orno' => $orno,
                        'servno' => null,
                        'mtr' => $mtr,
                        'dataid' => $acctid,
                        'moduleid' => $moduleid,
                        'vatsales' => $vatsales_row,
                        'vattaxamt' => $total_new_vat,
                        'vattype' => 1,
                        'subtotal' => $sabtotal_row,
                        'franchisetax' => $frtx,
                        'vatzerorated' => 0,
                        'vatexempt' => 0,
                        'nonvatsales' => $nonvatsales_row,
                        'totalamt' => $new_total_amt,
                        'payform' => 1,
                        'trnno' => $trnno,
                        'amtrec' => $amt_rec,
                        'printerserial' => 1,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                        'payforacctno' => 114,
                        'groups' => 1
                    );
                    $payments = insert_paylogs($ins_arr);
                    $data['payments'][] = $payments;
                    $data['err'][] = $payments['err'];
                    $payments_ids[] = $payments['id'];
                }
                $amt_chk -= $amtpd;

                $intamt = (isset($int_amt[$month]) && $int_amt[$month] > 0) ? $int_amt[$month] : 0;
                $amtpd = ($amtpd - $intamt) - $frtx;
                $ins_payapplied = array(
                    'paylogid' => (isset($payments['id'])) ? $payments['id'] : 0,
                    'acctid' => $acctid,
                    'orno' => $orno,
                    'amtpd' => $amtpd,
                    'interest' => cleanData($intamt),
                    'frtax' => cleanData($frtx),
                    'billmo' => $month,
                    'billyr' => $year,
                    'createdby' => user_id()
                );
                $this->db->insert('billing_payapplied', $ins_payapplied);
                $data['payappliederr'][] = $this->db->_error_message();

            }
        }
        $data['amtchk'] = $amt_chk;
        $data['pids'] = $payments_ids;
        $data['input'] = $this->input->post();
        $data['acctid'] = $acctid;
        return (object) $data;
    }

    function save_user_validation() {
        $data = array();
        $cash = $this->input->post('totalcash');
        $check = $this->input->post('totalcheck');
        $userid = $this->input->post('userid');
        $totalamt = ($check + $check);
        $ins_arr = array(
            'amtcash' => $cash,
            'amtcheck' => $check,
            'amttotal' => $totalamt,
            'createdby' => $userid
        );
        $this->db->insert('trn_transactions_validation', $ins_arr);
        $data['inputs'] = $this->input->post();
        return json_encode($data);
    }


    function get_trn_list() {

        $input_userid = $this->input->post('userid');
        $input_paytype = $this->input->post('paytype');

        $userid = ($input_userid>0) ? $input_userid : user_id();
        $data = array();

        $d = date('d');
        $m = date('m');
        $y = date('Y');

        if($input_paytype) {
            // BILL FILTER
            if ($input_paytype == 1) {
                $this->db->where_in('payforacctno', array(114));
            }
            // CAD FILTER
            if ($input_paytype == 2) {
                $arr_cad = array(161, 162, 163, 165, 189, 260, 261, 262, 263, 264, 265, 266, 267, 268, 269, 270, 271);
                $this->db->where_in('payforacctno', $arr_cad);
            }
            // LEGAL FILTER
            if ($input_paytype == 3) {
                $this->db->where_in('payforacctno', array(198));
            }
        }

        $qry_trn = $this->db->select('pl.orno, pl.trnno, pl.moduleid, pl.dataid')
            ->select("SUM(totalamt) AS amt", false)
            ->select("SUM(franchisetax) AS frtx", false)
            ->from('transaction_payments_logs AS pl')
            ->where(array(
                'YEAR(datecreated)' => $y,
                'MONTH(datecreated)' => $m,
                'DAY(datecreated)' => $d,
                'createdby' => $userid,
                'status' => 1
            ))
            ->group_by('pl.orno, pl.trnno, pl.moduleid, pl.dataid')
            ->order_by('pl.trnno')
            ->get();

        $total_amt = 0;
        $total_amt_chk = 0;
        $total_amt_cash = 0;

        $total_amt_bill_cash = 0;
        $total_amt_bill_check = 0;

        $total_amt_cad_cash = 0;
        $total_amt_cad_check = 0;

        $total_amt_bp_cash = 0;
        $total_amt_bp_check = 0;

        $total_amt_ra_cash = 0;
        $total_amt_ra_check = 0;

        $num_rows = $qry_trn->num_rows();
        if($num_rows>0) {
            foreach($qry_trn->result() as $row) {
                $amt = $row->amt;
                $moduleid = $row->moduleid;

                $total_amt += $amt;
                $mode_check = check_paymode($row->orno);
                $mode = '<span class="'.$mode_check->class.'">'.$mode_check->text.'</span>';

                $total_amt_chk += $mode_check->amtcheck;
                $total_amt_cash += $mode_check->amtcash;

                $get_navdep = $this->db->select('cm.names')
                    ->from('prime_module_navigations_departments AS nd')
                    ->join('prime_costcenter_main AS cm', 'cm.sysid = nd.ccid')
                    ->where(array('nd.navid' => $moduleid))
                    ->get()->row();
                $payments = ($get_navdep) ? $get_navdep->names : 'Others';
                $servno = '';

                if($moduleid==43) {
                    $qry_acct = $this->db->select('servicenumber')->from('customer_accounts_main')
                        ->where('sysid', $row->dataid)->get()->row();
                    $servno = ($qry_acct) ? $qry_acct->servicenumber : '<code>N/A</code>';
                }

                if($moduleid==35){
                    $qry_acct = $this->db->select('sysid')->from('application_customers_details')
                        ->where('sysid', $row->dataid)->get()->row();
                    $servno = ($qry_acct) ? str_pad($qry_acct->sysid, 8, '0', STR_PAD_LEFT) : '<code>N/A</code>';
                }

                if($moduleid==106){
                    $qry_acct = $this->db->select('sysid')
                        ->from('service_customers')
                        ->where('sysid', $row->dataid)
                        ->get()->row();
                    $servno = ($qry_acct) ? 'X'.str_pad($qry_acct->sysid, 6, '0', STR_PAD_LEFT) : '<code>'.$row->dataid.'</code>';
                }


                $info_popover = '<a class="text-primary" href="javascript:;" title="" data-toggle="popover" 
                                        data-content="
                                        <ul class=\'list-group summary column no-border\'>
                                        <li class=\'list-group-item\'>PN:  <span class=\'label-default pull-right\'>None</span></li> 
                                        <li class=\'list-group-item\'>R.A.: <span class=\'label-default pull-right\'>None</span></li> 
                                        <li class=\'list-group-item\'>BP22: <span class=\'label-default pull-right\'>None</span></li>
                                        <li class=\'list-group-item\'>REF:  <span class=\'label-default pull-right\'>None</span></li> 
                                        <li class=\'list-group-item\'>Duedate: <span class=\'label-default pull-right\'></span></li>
                                        </ul>"
                                        data-placement="left" data-trigger="hover" data-original-title="Billing Info"> 
                                        <i class="fa fa-print fa-fw"></i></a>';

                $print_popover = '<a id="print_or_trans" data-or="'.$row->orno.'" class="text-primary" href="javascript:;" title="" data-toggle="popover" 
                                        data-content="Print Subdetails"
                                        data-placement="left" data-trigger="hover" data-original-title="Payment Details"> 
                                        <i class="fa fa-print fa-fw"></i></a>';

                $data['list'][] = array(
                    'expand' => btn_expand($row->orno),
                    'trnno' => $row->trnno,
                    'orno' => str_pad($row->orno, 9, '0', STR_PAD_LEFT),
                    'servno' => $servno,
                    'frtx' => $row->frtx,
                    'amt' => number_format($row->amt, 2),
                    'mode' => $mode,
                    'payfor' => $payments,
                    'control' => $print_popover,
                    'select' => '<input type="checkbox" name="orcheck" class="icheck" value="'.$row->orno.'" id="orcheck" />'
                );
            }
        }

        $data['totalamt'] = number_format($total_amt, 2);
        $data['totalamtchk'] = number_format($total_amt_chk, 2);
        $data['totalamtcash'] = number_format($total_amt_cash, 2);

        return json_encode($data);
    }

    function get_trn_details() {
        $data = array();
        $id = $this->input->post('id');
        $html = '';

        $qry = $this->db->select('ca.codes, ca.descs, pl.totalamt, pl.payform')
            ->from('transaction_payments_logs AS pl')
            ->join('prime_chart_of_accounts AS ca', 'ca.sysid = pl.payforacctno')
            ->where(array('pl.orno' => $id))->get();

        $html .= '<div class="col-md-12"><ul class="list-group summary column no-border">';
        if($qry->num_rows() > 0) {
            foreach($qry->result() as $row) {
                $payform = ($row->payform==1) ? '<span class="label label-success">Cash</span>' : '<span class="label label-danger">Check</span>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-2 text-bold">'.$row->codes.'</span>';
                $html .= '<span class="col-md-5">'.$row->descs.'</span>';
                $html .= '<span class="col-md-1 label-name">'.$payform.'</span>';
                $html .= '<span class="col-md-4 label-default number">'.number_format($row->totalamt, 2).'</span>';
                $html .= '</li>';
            }
        }
        $html .= '</ul></div>';

        $data['html'] = $html;
        return json_encode($data);
    }

    function total_collection_today() {
        $data = array();
        $this->db->where('CAST(datecreated AS DATE) = ', 'CAST(NOW() AS DATE)', false);
        $qry = $this->db->select('SUM(totalamt) AS amt')
            ->from('transaction_payments_logs')
            ->where(array('status' => 1))
            ->get()->row();

        $data['amt'] = ($qry) ? $this->custom_number_format($qry->amt, 2) : '0.00';
        return json_encode($data);
    }

    function total_collection_thisweek() {
        $data = array();
        $from = $this->getWeekDates(date('Y'), date('Y-m-d'), true);
        $to = $this->getWeekDates(date('Y'), date('Y-m-d'), false);
        $this->db->where('CAST(datecreated AS DATE) >= ', $from);
        $this->db->where('CAST(datecreated AS DATE) <= ', $to);
        $qry = $this->db->select('SUM(amtpd) AS amt')
            ->from('billing_payapplied')
            ->where(array('status' => 1))
            ->get()->row();

        $data['amt'] = ($qry) ? $this->custom_number_format($qry->amt, 2) : '0.00';
        return json_encode($data);
    }
    function total_collection_thismonth() {
        $data = array();
        $this->db->where('MONTH(datecreated)', date('m'));
        $this->db->where('YEAR(datecreated)', date('Y'));
        $qry = $this->db->select('SUM(amtpd) AS amt')
            ->from('billing_payapplied')
            ->where(array('status' => 1))
            ->get()->row();

        $data['amt'] = ($qry) ? $this->custom_number_format($qry->amt, 2) : '0.00';
        return json_encode($data);
    }
    function total_collection_thisyear() {
        $data = array();
        $this->db->where('YEAR(datecreated)', date('Y'));
        $qry = $this->db->select('SUM(amtpd) AS amt')
            ->from('billing_payapplied')
            ->where(array('status' => 1))
            ->get()->row();

        $data['amt'] = ($qry) ? $this->custom_number_format($qry->amt, 2) : '0.00';
        return json_encode($data);
    }

    function getWeekDates($year, $date, $start = true)
    {
        $week = date('W', strtotime($date));
        $from = date("Y-m-d", strtotime("{$year}-W{$week}-1")); //Returns the date of monday in week
        $to = date("Y-m-d", strtotime("{$year}-W{$week}-7"));   //Returns the date of sunday in week

        if($start) {
            return $from;
        } else {
            return $to;
        }
        //return "Week {$week} in {$year} is from {$from} to {$to}.";
    }

    function custom_number_format($n, $precision = 3) {
        if ($n < 1000000) {
            // Anything less than a million
            if($n>=1000) {
                $n_format = number_format($n / 1000, $precision) . 'K';
            }else{
                $n_format = number_format($n);
            }
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision) . 'M';
        } else {
            // At least a billion
            $n_format = number_format($n / 1000000000, $precision) . 'B';
        }

        return $n_format;
    }

}

?>