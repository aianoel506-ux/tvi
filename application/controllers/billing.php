<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Billing extends CI_Controller {

    private $user_login;

    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_billing');
        $this->load->model('model_query');
        $this->load->model('model_mrd');
        $this->load->model('model_systems');
        $this->load->model('model_settings');
        $this->load->library('datatables');
        $this->user_login = $this->session->userdata('logged_in');
    }

    function index(){
        $data = array();
        $data['pagetitle'] = 'Billing Home';
        init_header($data);
        $this->load->view('admin/pages/billinghome', $data);
        init_footer($data, false);
    }

    // #########################################
    // BILLING TRANSACTION PROCESS
    // AUTHOR : LUCKY JOHN FADERON - SE
    function getbillingtrn() {
        echo $this->model_billing->get_billing_trn();
    }
    function getbillingdetails() {
        echo $this->model_billing->get_billing_details();
    }
    function processbilling() {
        echo $this->model_mrd->get_reading_analysis();
    }

    // #########################################
    // BILLING MAINTENANCE PROCESS
    // AUTHOR : LUCKY JOHN FADERON - SE
    function getratelist() {
        echo $this->model_billing->get_billing_rate_list();
    }
    function billingratesapproval() {
        echo $this->model_billing->billing_rates_approval();
    }
    function addbillingrates() {
        echo $this->model_billing->add_billing_rates();
    }
    function getselect2multcode() {
        echo $this->model_billing->get_select2_multcode();
    }
    function closebilling() {
        echo $this->model_billing->close_billing_ofthemonth();
    }


    function subtable() {
        $id = $this->input->post('id');
        $this->datatables->select('p.sysid, addr.addrspec');
        $this->datatables->select("CONCAT(p.lastname,', ', p.firstname, ' ', p.middlename) as name", false);
        $this->datatables->unset_column('name')->add_column('name', '$1', "tbl_input('inline', 'name', 'Editable..')");
        $this->datatables->add_column('mtr', '1');
        $this->datatables->add_column('servno', 'M000001');
        $this->datatables->add_column('due', date('Y-m-d'));
        $this->datatables->add_column('current', '0.00');
        $this->datatables->add_column('total', '0.00');
        $this->datatables->add_column('status', '<span class="label label-success">Printed</span>');
        $this->datatables->add_column('expand', '$1', 'btn_expand(sysid)');
        $this->datatables->add_column('control', '<button class="btn btn-info btn-xs"><i class="fa fa-search"></i></button><button class="btn btn-default btn-xs"><i class="fa fa-book"></i></button>');
        $this->datatables->from("person p");
        $this->datatables->join("person_address_matrix addr", 'addr.personid = p.sysid', 'left');
        $this->datatables->where("p.sysid", $id);
        echo $this->datatables->generate();
    }

    function getinfo() {
        $id = $this->input->post('id');
        $q = $this->db->select()->from('person')->where('sysid', $id)->get()->row();
        $kwh = 0;
        $genamt = 0;
        $genchrg = 0;
        $prevbill = 0;
        $prevint = 0;
        $prevvat = 0;
        $prevtotal = 0;
        $curamt = 0;
        $curint = 0;
        $curvat = 0;
        $curtotal = 0;
        $total = 0;
        $billdate = 0;
        $billno = 0;
        $billcount = 0;
        if ($q) {
            $increas = rand(0.01, 0.05);
            $charges = rand(2000, 6000);

            $qry = true;
            $name = $q->firstname;
            $kwh = rand(200, 1200);
            $genchrg = 7.9009;
            $genamt = ($kwh * $genchrg);

            $cur_amt = ($genamt + $charges);

            $prevbill = $cur_amt;


            $billcount = 2;

            $prevint = rand(95, 120);
            $prevtotal = ($prevbill + $prevint);

            $amt_increase = ($prevtotal * $increas);

            $curamt = ($prevtotal + $amt_increase + rand(100, 500));
            $curint = 0;
            $curtotal = ($curamt + $curint);
            $total = ($prevtotal + $curtotal);
            $billdate = date('Y-m-d');
            $billno = '0000001';
        } else {
            $qry = false;
            $name = '';
        }
        $data['kwh'] = number_format($kwh);
        $data['genamt'] = number_format($genamt);
        $data['genchrg'] = number_format($genchrg, 5);
        $data['prevbill'] = number_format($prevbill, 2);
        $data['prevint'] = number_format($prevint, 2);
        $data['prevvat'] = number_format($prevvat, 2);
        $data['prevtotal'] = number_format($prevtotal, 2);
        $data['curamt'] = number_format($curamt, 2);
        $data['curint'] = number_format($curint, 2);
        $data['curvat'] = number_format($curvat, 2);
        $data['curtotal'] = number_format($curtotal, 2);
        $data['total'] = number_format($total, 2);
        $data['billdate'] = $billdate;
        $data['billno'] = $billno;
        $data['billcount'] = $billcount;
        $data['qry'] = $qry;
        $data['name'] = $name;
        echo json_encode($data);
    }

    function test(){
        echo "<pre>";
        $num = -30222;
        echo money_format($num, 2);
        /*
        $kwh = 868;
        $genchg = 6.518300;
        $prevY = -138.45;
        $vat = 0.12;
        $gen_amt = bcmul($kwh, $genchg, 2);
        $number = bcmul(bcadd($gen_amt, $prevY, 2), $vat, 2);
        echo '<br>Prev Yr: ' . $this->floorp($prevY, 2);
        echo '<br>Gen Amt: ' . $this->floorp($gen_amt, 2);
        echo '<br>Total: '. $this->floorp($number, 2);
         
        $acctid = 1;
        $readingid = 5;
        $compute = compute_billing($readingid, $acctid);
        print_r($compute);
         *  
         */
    }



    function billingrep() {
        $trnid = $this->input->post('trnid');
        $qry_reading_hist_test = $this->db->select('h.mtrid, h.sysid, hb.trnid')
            ->from('trn_reading_history AS h')
            ->join('trn_reading_history_billing AS hb', 'hb.trnid = h.dataid AND hb.presreadid = h.sysid')
            ->where(array('h.dataid' => $trnid, 'hb.trnid' => $trnid))->get();
        $data = array();
        $html = '';
        if ($qry_reading_hist_test->num_rows() > 0) {
            foreach ($qry_reading_hist_test->result() as $row) {
                $acctid = get_acctinfo_mtr($row->mtrid)->ownerid;
                if ($acctid) {
                    $compute = compute_billing($row->sysid, $acctid);
                    $mtrinfo = get_acctinfo_mtr($row->mtrid);
                    $html .= ' 
                    <div class="rep-content">
                        <div class="accountinfo">
                            <div class="column-1">
                                <span class="info-list">'.$compute->name.'</span>
                                <span class="info-list">'.$compute->addr.'</span>
                            </div>
                            <div class="column-2">
                                <span class="info-list">'.$compute->servno.'</span>
                            </div>
                        </div>
                        <div class="accountdetails">
                            <span class="gdlb">'.$compute->gdlb.'</span>
                            <span class="servno">'.$compute->servno.'</span>
                            <span class="mn">1</span>
                            <span class="mo">'.$compute->mo.'</span>
                            <span class="moyr">'.$compute->moyr.'</span>
                            <span class="due">'.$compute->duedate.'</span>
                            <span class="curdue">'.number_format($compute->curr, 2).'</span>
                        </div>
                        <div class="metering">
                            <span class="mtrdetails">
                                <span class="meterno">'.$mtrinfo->mtrno.'</span>
                                <span class="serial">'.$mtrinfo->serialcodes.'</span>
                                <span class="load"></span>
                                <span class="rate">R</span>
                            </span>
                            <span class="readingdetails">
                                <span class="presread">'.$compute->presread.'</span>
                                <span class="prevread">'.$compute->prevread.'</span>
                                <span class="mult">'.$compute->mult.'</span>
                                <span class="kwh">'.number_format($compute->kwh).'</span>
                            </span>
                            <span class="unpaidbills">
                            
                            </span>
                        </div>
                        <div class="charges">'.$compute->rep.'
                            <br>
                            <h3 style="font-size: 8px; font-weight: bold">THIS IS A SYSTEM GENERATED STATEMENT OF ACCOUNT. NO SIGNATURE IS REQUIRED.</H3>
                        </div>
                        <footer></footer>
                    </div>
                 ' ;
                }
            }
        }
        $data['html'] = $html;
        echo json_encode($data);
    }

    function floorp($val, $precision)
    {
        $half = 0.01 / pow(10, $precision);
        return round($val + $half, $precision);
    }



    function cpustat() {


        $free = shell_exec('free');
        $free = (string)trim($free);

        $top = shell_exec('top');
        $top_arr = explode("\n", $top);

        $free_arr = explode("\n", $free);
        if( count($free_arr>0)) {
            if(isset($free_arr[1])) {
                $mem = explode(" ", $free_arr[1]);
                $mem = array_filter($mem);
                $mem = array_merge($mem);
            }else{
                $mem = 0;
            }
        }else{

            $mem = 0;
        }
        if($mem) {
            $memory_usage = $mem[2] / $mem[1] * 100;
        }else{
            $memory_usage = 0;
        }
        echo '<html><head><title>System.STAT</title><meta http-equiv="refresh" content="5" ></head>';
        echo '<body style="background: #cccccc">';
        echo '<div style="width: 80%; margin: 50px auto; background: #fff; padding: 20px 20px;">';
        echo '<h1>System Stats</h1>';
        echo '<hr>';
        echo '<pre>';
        print_r($free_arr);
        echo '</pre>';
        echo '<hr>';
        echo 'Memory Usage: ' . number_format($memory_usage, 2) . '%';
        echo '<pre>';
        print_r($top_arr);
        echo '</pre>';
        echo '</div>';
        echo '</body></html>';

    }

    function fathertrnquery() {
        echo $this->model_billing->father_query();
    }

    function citytrnquery() {
        echo $this->model_query->city_query();
    }

    function citytrnqueryupd() {
        echo '<pre>';
        echo $this->model_query->city_query_upd();
    }

    function interestquery() {
        echo $this->model_billing->interest_query();
    }

    function citymastquery($year = false, $month = false ) {
        echo $this->model_query->citymast_query($year, $month);
    }

    function billtrnquery($year = false, $month = false) {
        ini_set('memory_limit', '2048M');
        $data = array();
        $err_msg = '';
        $year_input = $this->input->post('year');
        $month_input = $this->input->post('month');

        $year = ($year_input) ? $year_input : $year;
        $month = ($month_input) ? $month_input : $month;

        if($year && $month) {
            //$qry_check = $this->db->select()->from('billing_reports')
            //    ->where(array('month' => $month, 'year' => $year))
            //    ->get()->row();
            //if($qry_check==false) {

            if (pecoapps_conn()) {
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                $query = $conn->select("
                          ctrlinc
                          ,group_____ AS 'group'
                          ,dist______ AS dist
                          ,lot_______ AS lot
                          ,book______ AS book
                          ,servno____ AS servno
                          ,name______ AS name
                          ,addr______ AS addr
                          ,mtr_______ AS mtr
                          ,m_________ AS month
                          ,yr________ AS year
                          ,prvdte____ AS prvdte
                          ,prsdte____ AS prsdte
                          ,duedate___ AS duedate
                          ,load______ AS load
                          ,rate______ AS rate
                          ,prvrdg____ AS prvrdg
                          ,prsrdg____ AS prsrdg
                          ,multcd____ AS multcd
                          ,kwhuse____ AS kwhuse
                          ,genamt____ AS genamt
                          ,genchg____ AS genchg
                          ,trnamt____ AS trnamt
                          ,trnchg____ AS trnchg
                          ,disamt____ AS disamt
                          ,dischg____ AS dischg
                          ,demamt____ AS demamt
                          ,supamt____ AS supamt
                          ,supchg____ AS supchg
                          ,supper____ AS supper
                          ,mtramt____ AS mtramt
                          ,mtrchg____ AS mtrchg
                          ,mtrper____ AS mtrper
                          ,slamt_____ AS slamt
                          ,slchg_____ AS slchg
                          ,iccamt____ AS iccamt
                          ,iccsub____ AS iccsub
                          ,llramt____ AS llramt
                          ,llrsub____ AS llrsub
                          ,lldamt____ AS lldamt
                          ,misamt____ AS misamt
                          ,mischg____ AS mischg
                          ,envamt____ AS envamt
                          ,envchg____ AS envchg
                          ,framt_____ AS framt
                          ,genvat____ AS genvat
                          ,trnvat____ AS trnvat
                          ,disvat____ AS disvat
                          ,slvat_____ AS slvat
                          ,othvat____ AS othvat
                          ,appsur____ AS appsur
                          ,surbal____ AS surbal
                          ,current___ AS 'current'
                          ,overdue___ AS overdue
                          ,totacc____ AS totacc
                          ,totint____ AS totint
                          ,dolpay____ AS dolpay
                          ,cntapp____ AS cntapp
                          ,billno
                          ,moyr
                          ,batch
                          ,dteprt
                          ,lttype
                          ,ltsta
                          ,lttodt
                          ,ltfrdt
                          ,ltmo
                          ,ltyear
                          ,ctrlinc
                          ,intdte
                          ,genamt1___ AS genamt1
                          ,genchg1___ AS genchg1
                          ,papc
                          ,papcchg
                          ,mtrser
                          ,serial
                          ,scdisc
                          ,npcchg____ AS npcchg
                          ,npcamt____ AS npcamt
                          ,iccschg
                          ,iccsamt
                          ,fitchg
                          ,fitamt
                        ")
                    ->from('billtrn')
                    ->where(
                        array(
                            'yr________' => $year,
                            'm_________' => $month,
                            'group_____ > ' => 0,
                            'lot_______ > ' => 0
                            //'ctrlinc' => 5395192
                            //'servno____' => 'J11954'
                        )
                    )->get();
                // @TODO REMOVE TRUNCATE IF SCRIPT IS FINAL
                // TRUNCATE TABLE
                // $this->db->query("TRUNCATE TABLE billing_reports");


                if ($query->num_rows() > 0) {
                    $ins_num = 0;
                    $err_msg .= 'Number of Records: ' . $query->num_rows() . '<br>';

                    // DELETE RECORDS
                    $this->db->query("DELETE FROM billing_reports WHERE `year` = $year AND `month` = $month");

                    foreach ($query->result() as $row) {
                        $qry_acctid = $this->db->select('sysid')->from('customer_accounts_main')
                            ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                            ->get()->row();
                        $acctid = ($qry_acctid) ? $qry_acctid->sysid : 0;



                        $date_str_prv = trim($row->prvdte);

                        $dt_explode_prv = explode('/', $date_str_prv);
                        if(!empty($dt_explode_prv[0]) &&!empty($dt_explode_prv[1]) && !empty($dt_explode_prv[2])) {

                            $old_prvdte = DateTime::createFromFormat('m/d/y', $date_str_prv);

                            if (!empty($date_str_prv)) {
                                $old_prvdte = DateTime::createFromFormat('m/d/Y', $date_str_prv);
                                if ($old_prvdte && $old_prvdte->format('Y') >= 1900) {
                                    $prvdte = $old_prvdte->format('Y-m-d');
                                } else {
                                    $old_prvdte = DateTime::createFromFormat('m/d/y', $date_str_prv);
                                    $prvdte = $old_prvdte->format('Y-m-d');
                                }
                            } else {
                                $prvdte = '1900-01-01';
                            }
                        }else{
                            $prvdte = '1900-01-01';
                        }


                        $date_str_prs = trim($row->prsdte);

                        $dt_explode_prs = explode('/', $date_str_prs);
                        if(!empty($dt_explode_prs[0]) &&!empty($dt_explode_prs[1]) && !empty($dt_explode_prs[2])) {

                            $old_prsdte = DateTime::createFromFormat('m/d/y', $date_str_prs);

                            if (!empty($date_str_prs)) {
                                $old_prsdte = DateTime::createFromFormat('m/d/Y', $date_str_prs);
                                if ($old_prsdte && $old_prsdte->format('Y') >= 1900) {
                                    $prsdte = $old_prsdte->format('Y-m-d');
                                } else {
                                    $old_prsdte = DateTime::createFromFormat('m/d/y', $date_str_prs);
                                    $prsdte = $old_prsdte->format('Y-m-d');
                                }
                            } else {
                                $prsdte = '1900-01-01';
                            }
                        }else{
                            $prsdte = '1900-01-01';
                        }


                        if (!empty(trim($row->duedate))) {
                            $old_duedate = DateTime::createFromFormat('m/d/Y', trim($row->duedate));
                            if ($old_duedate) {
                                $duedate = $old_duedate->format('Y-m-d');
                            } else {
                                $duedate = '1900-01-01';
                            }
                        } else {
                            $duedate = '1900-01-01';
                        }

                        if (!empty(trim($row->dteprt))) {
                            $t = strtotime($row->dteprt);
                            $dteprt = date('Y-m-d H:i:s', $t);
                        } else {
                            $dteprt = '1900-01-01';
                        }

                        if (!empty(trim($row->intdte))) {
                            $t = strtotime($row->intdte);
                            $intdte = date('Y-m-d', $t);
                        } else {
                            $intdte = '1900-01-01';
                        }

                        if (!empty(trim($row->dolpay))) {
                            $t = strtotime($row->dolpay);
                            $dolpay = date('Y-m-d', $t);
                        } else {
                            $dolpay = '1900-01-01';
                        }

                        $billno = (!empty(trim($row->billno))) ? $row->billno : 0;
                        $name = ($row->name) ? ucwords(utf8_decode(trim($row->name))) : ' ';
                        $addr = ($row->addr) ? ucwords(utf8_decode(trim($row->addr))) : ' ';
                        $moyr = trim($row->moyr);
                        $moyr_arr = explode('-', $moyr);
                        $bmo = (isset($moyr_arr[0])) ? $moyr_arr[0] : 0;
                        $byr = (isset($moyr_arr[1])) ? $moyr_arr[1] : 0;



                        $ins_arr = array(
                            'billno' => $billno,
                            'acctid' => $acctid,
                            'group' => ($row->group) ? $row->group : 0,
                            'dist' => ($row->dist) ? trim($row->dist) : '',
                            'lot' => ($row->lot) ? $row->lot : 0,
                            'book' => (trim($row->book) != '') ? trim($row->book) : 0,
                            'servno' => trim($row->servno),
                            'mtr' => $row->mtr,
                            'mtrser' => (trim($row->mtrser) != '') ? trim($row->mtrser) : 0,
                            'serial' => trim($row->serial),
                            'name' => $name,
                            'addr' => $addr,
                            'bmo' => $bmo,
                            'byr' => $byr,
                            'month' => $row->month,
                            'year' => $row->year,
                            'prvdte' => $prvdte,
                            'prsdte' => $prsdte,
                            'duedate' => $duedate,
                            'load' => $row->load,
                            'rate' => $row->rate,
                            'prvrdg' => $row->prvrdg,
                            'prsrdg' => $row->prsrdg,
                            'multcd' => $row->multcd,
                            'kwhuse' => $row->kwhuse,
                            'genamt' => $row->genamt,
                            'genamt1' => $row->genamt1,
                            'trnamt' => $row->trnamt,
                            'disamt' => $row->disamt,
                            'demamt' => $row->demamt,
                            'supamt' => $row->supamt,
                            'supper' => $row->supper,
                            'mtramt' => $row->mtramt,
                            'slamt' => $row->slamt,
                            'iccamt' => $row->iccamt,
                            'iccsub' => $row->iccsub,
                            'llramt' => $row->llramt,
                            'llrsub' => $row->llrsub,
                            'lldamt' => $row->lldamt,
                            'misamt' => $row->misamt,
                            'envamt' => $row->envamt,
                            'framt' => $row->framt,
                            'npcamt' => $row->npcamt,
                            'iccsamt' => $row->iccsamt,
                            'papc' => $row->papc,
                            'fitamt' => $row->fitamt,
                            'genchg' => $row->genchg,
                            'genchg1' => $row->genchg1,
                            'trnchg' => $row->trnchg,
                            'dischg' => $row->dischg,
                            'demchg' => 0,
                            'supchg' => $row->supchg,
                            'mtrchg' => $row->mtrchg,
                            'mtrper' => $row->mtrper,
                            'slchg' => $row->slchg,
                            'mischg' => $row->mischg,
                            'envchg' => $row->envchg,
                            'npcchg' => $row->npcchg,
                            'iccschg' => $row->iccschg,
                            'fitchg' => $row->fitchg,
                            'papcchg' => $row->papcchg,
                            'genvat' => $row->genvat,
                            'trnvat' => $row->trnvat,
                            'disvat' => $row->disvat,
                            'slvat' => $row->slvat,
                            'othvat' => $row->othvat,
                            'appsur' => $row->appsur,
                            'surbal' => $row->surbal,
                            'current' => $row->current,
                            'overdue' => $row->overdue,
                            'totacc' => $row->totacc,
                            'totint' => $row->totint,
                            'scdisc' => $row->scdisc,
                            'dolpay' => $dolpay,
                            'batch' => $row->batch,
                            'dteprt' => $dteprt,
                            'intdte' => $intdte,
                            'lttype' => $row->lttype,
                            'ltsta' => $row->ltsta,
                            'lttodt' => $row->lttodt,
                            'ltfrdt' => $row->ltfrdt,
                            'ltmo' => $row->ltmo,
                            'ltyear' => $row->ltyear,
                            'cntapp' => $row->cntapp,
                            'createdby' => 1
                        );
                        $ins = true;
                        $ins = $this->db->insert('billing_reports', $ins_arr);
                        if ($ins) {
                            //$err_msg .= '<br><b>Ok</b> == ' . $this->db->insert_id() . '<br>';
                            $ins_num += 1;
                        } else {
                            $err_msg .= '<br><b>Er: </b> ' . $this->db->_error_message() . ' == ' . $this->db->insert_id() . '<br>';
                            $this->db->insert('billing_reports_ins_error', array('ctrlinc' => $row->ctrlinc));
                        }
                    }
                    $err_msg .= 'Inserted: ' . $ins_num;
                }

            } else {
                return false;
            }
            //}else{
            //    $err_msg = 'Billing period provided is existing already!';
            //}
        }else{
            $err_msg = 'Please provide Year/Month';
        }
        $data['curr'] = $year . ' / ' . $month;
        $data['msg'] = $err_msg;
        echo json_encode($data);
    }

    function getartbl() {
        echo $this->model_billing->get_ar_tbl();
    }

    function getbillinghist() {
        echo $this->model_billing->get_billing_hist();
    }

    function getpayapplied() {
        echo $this->model_billing->get_payment_applied();
    }

    function getotherinfo() {
        echo $this->model_billing->get_ar_other_info();
    }

    function singleprintbill($billid = false) {
        $billing = $this->model_billing->single_print_bill($billid);


        $html = $billing->html;
        $filename = 'Test Bill';

        $this->load->library('pdf');
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $customPaper = array(0, 0, 615, 930);
        $dompdf->setPaper($customPaper, 'portrate');
        $dompdf->render();
        // Add PDF Document Information
        $dompdf->add_info('Subject', 'LEAVE FORM | ' . '');
        $dompdf->add_info('Author', 'Municipality of Trinidad');
        $dompdf->add_info('Creator', 'ITD');
        $dompdf->add_info('Keywords', 'LEAVE FORM');
        $dompdf->stream($filename);
    }

    function singlepreviewbill() {
        echo $this->model_billing->single_preview_bill();
    }

    function printbilling()  {
        echo $this->model_billing->print_billing();
    }

    function updatectdetails()  {
        echo $this->model_query->update_ct_details();
    }

    function getbillingcompute(){
        echo $this->model_billing->get_billing_compute();
    }


    function getbillingcomputect(){
        echo $this->model_billing->get_billing_compute_ct();
    }


    function sendratestoaudit(){
        echo $this->model_billing->send_rates_to_audit();
    }


    // BILLING CHARGES MAINTENANCE
    function getchargesmain() {
        echo $this->model_billing->get_charges_maintenance();
    }
    function chargescomp() {
        echo $this->model_billing->get_charges_comp();
    }
    function sendebill() {
        $result = false;
        $data = array();
        $email = 'lfaderon@gmail.com';
        $id = $this->input->post('id');
        $qry_bill_main = $this->db->select('month, year, acctid')
            ->from('billing_reports_main')
            ->where(array('sysid' => $id))
            ->get()->row();
        if($qry_bill_main) {
            $acctid = $qry_bill_main->acctid;
            $month = $qry_bill_main->month;
            $year = $qry_bill_main->year;
            $qry_bill = $this->db->select(
                '
            billno,
            acctid,
            group,
            dist,
            lot,
            book,
            servno,
            mtr,
            mtrser,
            serial,
            name,
            addr,
            bmo,
            byr,
            month,
            year,
            prvdte,
            prsdte,
            duedate,
            load,
            rate,
            prvrdg,
            prsrdg,
            multcd,
            kwhuse,
            genamt,
            genamt1,
            trnamt,
            disamt,
            demamt,
            supamt,
            supper,
            mtramt,
            slamt,
            iccamt,
            iccsub,
            llramt,
            llrsub,
            lldamt,
            misamt,
            envamt,
            framt,
            npcamt,
            iccsamt,
            papc,
            fitamt,
            genchg,
            genchg1,
            trnchg,
            dischg,
            demchg,
            supchg,
            mtrchg,
            mtrper,
            slchg,
            mischg,
            envchg,
            npcchg,
            iccschg,
            fitchg,
            papcchg,
            genvat,
            trnvat,
            disvat,
            slvat,
            othvat,
            appsur,
            surbal,
            current,
            overdue,
            totacc,
            totint,
            scdisc,
            dolpay
            '
            )
                ->from('billing_reports')
                ->where(array('acctid' => $acctid, 'year' => $year, 'month' => $month))
                ->get()->row();



            $qry_hist = $this->db->select('bmo, byr, prvdte, prsdte, prvrdg, prsrdg, kwhuse, current')
                ->from('billing_reports_main')
                ->where(array('acctid' => $acctid, 'kwhuse > ' => 0))
                ->order_by('byr', 'desc')
                ->order_by('bmo', 'desc')
                ->limit(6)
                ->get();
            $hist_data = ($qry_hist->num_rows() > 0) ? $qry_hist->result() : false;

            $content = $this->model_systems->bill_form($qry_bill, $hist_data);

            $filename = $qry_bill->servno . '_' . $qry_bill->year . '-' . str_pad($qry_bill->month, 2, '0', STR_PAD_LEFT) . '.pdf';

            // Load all views as normal
            // $this->load->view('sample_tcpdf.php');
            // Get output html
            // $html = $this->output->get_output();

            $bill_month = $qry_bill->month;
            $bill_year = $qry_bill->year;

            // ##################################################################
            // ######################## Load library ############################
            $this->load->library('pdf');
            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($content);
            $customPaper = array(0, 0, 610, 910);
            $dompdf->setPaper($customPaper, 'portrate');
            $dompdf->render();
            // ##################################################################
            // ################## Add PDF Document Information ##################
            $dompdf->add_info('Subject', 'PECO BILL | ' . $filename);
            $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
            $dompdf->add_info('Creator', 'Lucky John Faderon - SE');
            $dompdf->add_info('Keywords', 'BILL');

            $output = $dompdf->output();

            $dt = DateTime::createFromFormat('m', $bill_month);
            $monthcode = strtoupper($dt->format('M'));

            if(!file_exists(FCPATH . 'uploads/billing/')) {
                mkdir(FCPATH . 'uploads/billing');
            } else {
                chmod(FCPATH . 'uploads/billing', '0777');
            }

            file_put_contents(FCPATH . 'uploads/billing/' . $filename, $output);
            /* ################################################################
            // ################## SMTP & mail configuration ###################
            $this->load->library('email');
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_port' => 465,
                'smtp_user' => 'bills.peco@gmail.com',
                'smtp_pass' => 'P3C02018!!',
                'mailtype' => 'html',
                'charset' => 'utf-8'
            );

            $this->email->initialize($config);
            $this->email->set_mailtype("html");
            $this->email->set_newline("\r\n");

            //Email content
            $this->email->to($email);
            $this->email->from('no-reply@panayelectric.com', 'PECO BILL | ' . $monthcode . '-' . $bill_year);
            $this->email->subject('PECO BILL | ' . $monthcode . '-' . $bill_year);
            $this->email->message($this->model_systems->bill_html($qry_bill));
            $this->email->attach(FCPATH . 'uploads/billing/' . $filename);
            */
            // Send email
            // $result = $this->email->send();
            //$result = true;
        }
        $data['qry'] = $result;
        echo json_encode($data);
    }


    function paebilling() {
        echo $this->model_billing->pae_billing();
    }





    function testing() {
        echo '<pre>';
        $ar = get_customer_overdues(76197, 1, 2019, 2);

        echo '<hr>';
        print_r($ar);
    }
}


