<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tellering extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_tellering');
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_billing');
        $this->load->model('model_receipt');;
        $this->load->model('model_cad');;
        $this->load->model('model_ar');;

    }

    public function search() {
        $data = array();
        $qry = false;
        $search_mode = '';
        $search_num         = $this->input->post('servno');
        $search_mtr         = $this->input->post('mtr');
        $search_module      = $this->input->post('moduleid');
        $search_type = 0;
        $folder = '';

        $qry_get_navdetails = $this->db->from('prime_module_navigations_main')
            ->where('sysid', $search_module)->get()->row();

        if($qry_get_navdetails) {
            $folder_arr = explode('/', $qry_get_navdetails->pagefile);
            $folder_1 = $folder_arr[0];
            $qry = true;
            $search_type = $qry_get_navdetails->code;
            $folder = $folder_1;
        }

        $data['folder'] = $folder;
        $data['file'] = 'payments';
        $data['servno'] = $search_num;
        $data['mtr'] = $search_mtr;
        $data['types'] = $search_type;
        $data['moduleid'] = $search_module;
        $data['mode'] = ($qry_get_navdetails) ? $qry_get_navdetails->code : '';
        $data['qry'] = $qry;
        echo json_encode($data);
	}

	function queueserve() {
        $data = array();
        $qry = false;
        $search_mode = '';
        $search_num         = $this->input->post('servno');
        $search_mtr         = $this->input->post('mtr');
        $search_module      = $this->input->post('moduleid');

        $this->db->where('YEAR(datecreated)', date('Y'));
        $this->db->where('MONTH(datecreated)', date('m'));
        $this->db->where('DAY(datecreated)', date('d'));
        $qry_queue = $this->db->select()->from('customer_queue')
            ->where(array('num' => $search_num, 'status' => 0))
            ->get()->row();
        if ($qry_queue) {
            $qry = true;
            $data['types'] = $qry_queue->types;
            $data['servno'] = $qry_queue->servno;
            $data['name'] = $qry_queue->names;
            $data['mtr'] = $search_mtr;
        }

        $data['mode'] = $search_mode;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

	function getbilling() {
        $servno = $this->input->post('servno');
        $mtr = $this->input->post('mtr');
        // echo $this->model_billing->get_ar_tbl($servno, $mtr, 1);
        echo $this->model_ar->get_billing($servno, $mtr, 12, false, false, false, 1);
    }
    public function getbillingsp() {
		echo $this->model_tellering->get_billing_sp();
	}
    function submitorvoid() {
        echo $this->model_tellering->submit_or_void();
    }
    function getacctrec() {
        echo $this->model_billing->get_ar_tbl();
    }
    function tellersearchkey() {
        echo $this->model_tellering->get_search_key();
    }

    function submitpay() {
        //$data['compname'] = $this->model_receipt->printer_hostname();
        //$this->load->view('printing/test', $data);
        $computername = $this->input->post('computername');

        $payments = $this->model_tellering->pay_bill();

        $receipt = $this->db->select("
            ps.orno,
            ps.dataid,
            ps.moduleid,
            ps.vatsales,
            ps.vattaxamt,
            ps.vattype,
            ps.subtotal,
            ps.franchisetax,
            ps.vatzerorated,
            ps.vatexempt,
            ps.nonvatsales,
            ps.totalamt,
            ps.payform,
            ps.trnno,
            bp.billmo,
            bp.billyr,
            bp.interest
        ")->from('transaction_payments_logs AS ps')
            ->join('billing_payapplied AS bp','bp.paylogid = ps.sysid', 'left')
            ->where_in('ps.sysid', $payments->pids)
            ->get();

        $trn_cnt = $receipt->num_rows();
        // $trn_cnt = 0; FOR TESTING UNCOMMENT TO DISABLE PRINTING
        if($trn_cnt>0) {

            $conn = windows_printer_connector($computername);
            // CHECK IF CONNECTION STATUS IS TRUE
            if ($conn->res == true) {
                $printer = $conn->printer;
                $acctid = $payments->acctid;
                $info = get_active_account_info($acctid);

                receipt_header($printer);
                /* Name of Account Paid */
                $printer -> setJustification($printer::JUSTIFY_LEFT);
                $printer -> text(two_cols("Service No.: " . $info->servicenumber, "TIN: N/A"));
                $printer -> text("Name: " . $info->name."\n");
                $printer -> text("Address: " . $info->address."\n");
                $printer -> feed();

                /* Title of receipt */
                $printer -> setJustification($printer::JUSTIFY_CENTER);
                $printer -> setEmphasis(true);
                $printer -> setUnderline(true);
                $printer -> text(space_both_sides("O F F I C I A L   R E C E I P T"));
                $printer -> setEmphasis(false);
                $printer -> setUnderline(false);

                /* RECEIPT CONTENTS */
                /* Top Table */
                $printer -> setJustification($printer::JUSTIFY_LEFT);
                $printer -> setUnderline(true);
                $printer -> text(three_cols_a('BILL', 'INT', 'AMOUNT'));
                $printer -> setUnderline(false);

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


                    if($rows->billmo && $rows->billyr) {
                        $string = str_pad($rows->billmo, 2, '0', STR_PAD_LEFT) . '-' .convert_year('Y', 'y', $rows->billyr);
                    }else{
                        $string = '';
                    }

                    $check = false;
                    if ($rows->payform == 2) {
                        $check = true;
                        $check_cnt += 1;
                    }
                    if ($rows->payform == 1) {
                        $cash_cnt += 1;
                    }

                    $int = ($rows->interest>0) ? $rows->interest : 0;

                    $printer -> text(three_cols_a($string, number_format($int, 2), number_format($rows->totalamt, 2), $check));
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
                $printer -> setUnderline(true);
                $printer -> text(space_both_sides("Form of Payment: " . $payform));
                $printer -> setUnderline(false);
                $printer -> setEmphasis(true);
                $printer -> text("Tax Breakdown: \n");
                $printer -> setEmphasis(false);
                $printer -> text(three_cols('VAT Sales', 'VAT Tax Amt', 'Sub-Total'));
                $printer -> text(three_cols(number_format($vatsales_amt, 2), number_format($vattax_amt, 2), number_format($sabtotal_amt, 2)));
                $printer -> text(two_cols("ZERO RATED TAX", number_format($vatzerorated_amt, 2)));
                $printer -> text(two_cols("VAT EXEMPT", number_format($vatexempt_amt, 2)));
                $printer -> text(two_cols("CWT", number_format($cwt_amt, 2)));
                $printer -> text(two_cols("NON VAT SALES", number_format($nonvatsales_amt, 2)));
                $printer -> setUnderline(true);
                $printer -> setEmphasis(true);
                $printer -> text(two_cols("Total", number_format($total_amt, 2)));
                $printer -> setEmphasis(false);
                $printer -> setUnderline(false);
                $printer -> feed();
                /* END OF RECEIPT CONTENTS */
                receipt_footer($printer, $rows->orno, $rows->trnno);

                $printer -> feed();
                $printer -> cut();
                $printer -> pulse();
                $printer -> close();
            }
        }
        echo json_encode($payments);
    }

    function payserv() {
        $computername = $this->input->post('computername');
        $payments = $this->model_tellering->pay_cad();
        if($payments->qry==true) {
            $groups = $this->db->select('groups')->from('transaction_payments_logs')
                ->where_in('sysid', $payments->pids)
                ->group_by('groups')
                ->get();
            if($groups->num_rows()>0) {
                foreach($groups->result() as $row) {
                    $receipt = $this->db->select()->from('transaction_payments_logs')
                        ->where('groups', $row->groups)
                        ->where_in('sysid', $payments->pids)
                        ->get();
                    $trn_cnt = $receipt->num_rows();
                    if($trn_cnt>0) {
                        $conn = windows_printer_connector($computername);
                        // CHECK IF CONNECTION STATUS IS TRUE
                        if ($conn->res == true) {

                            $printer = $conn->printer;
                            $appid = $payments->appid;
                            $info = $this->model_cad->get_special_services_info($appid);
                            $servno = ($info->servno != '') ? $info->servno : str_pad($appid, 8, '0', STR_PAD_LEFT);
                            $name = $info->firstname.' '.$info->lastname;

                            receipt_header($printer);
                            /* Name of Account Paid */
                            $printer -> setJustification($printer::JUSTIFY_LEFT);
                            $printer -> text(two_cols("Service No.: " . $servno, "TIN: N/A"));
                            $printer -> text("Name: " . $name."\n");
                            $printer -> text("Address: " . $info->addrspec."\n");
                            $printer -> feed();

                            /* Title of receipt */
                            $printer -> setJustification($printer::JUSTIFY_CENTER);
                            $printer -> setEmphasis(true);
                            $printer -> setUnderline(true);
                            $printer -> text(space_both_sides("O F F I C I A L   R E C E I P T"));
                            $printer -> setEmphasis(false);
                            $printer -> setUnderline(false);

                            /* RECEIPT CONTENTS */
                            /* Top Table */
                            $printer -> setJustification($printer::JUSTIFY_LEFT);
                            $printer -> setUnderline(true);
                            $printer -> text(two_cols('Transaction', 'Amount'));
                            $printer -> setUnderline(false);

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

                            foreach($receipt->result() as $rows) {
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
                                    $string = $endPoint? substr($stringCut, 0, $endPoint):substr($stringCut, 0);
                                    $string .= '...';
                                }
                                $check = false;
                                if($rows->payform==2) {
                                    $check = true;
                                    $check_cnt += 1;
                                }
                                if($rows->payform==1) {
                                    $cash_cnt += 1;
                                }

                                $printer -> text(two_cols_a($string, number_format($rows->totalamt, 2), $check));
                            }

                            // CHECK PAYMENT FORM
                            if($cash_cnt > 0 && $check_cnt > 0) {
                                $payform = 'Cash/Check';
                            }else{
                                if($cash_cnt>0) {
                                    $payform = 'Cash';
                                }else{
                                    $payform = 'Check';
                                }
                            }

                            // ##############################
                            $printer -> setUnderline(true);
                            $printer -> text(space_both_sides("Form of Payment: " . $payform));
                            $printer -> setUnderline(false);
                            $printer -> setEmphasis(true);
                            $printer -> text("Tax Breakdown: \n");
                            $printer -> setEmphasis(false);
                            $printer -> text(three_cols('VAT Sales', 'VAT Tax Amt', 'Sub-Total'));
                            $printer -> text(three_cols(number_format($vatsales_amt,2), number_format($vattax_amt, 2), number_format($sabtotal_amt, 2)));
                            $printer -> text(two_cols("ZERO RATED TAX", number_format($vatzerorated_amt, 2)));
                            $printer -> text(two_cols("VAT EXEMPT", number_format($vatexempt_amt, 2)));
                            $printer -> text(two_cols("CWT", number_format($cwt_amt, 2)));
                            $printer -> text(two_cols("NON VAT SALES", number_format($nonvatsales_amt, 2)));
                            $printer -> setUnderline(true);
                            $printer -> setEmphasis(true);
                            $printer -> text(two_cols("Total", number_format($total_amt, 2)));
                            $printer -> setEmphasis(false);
                            $printer -> setUnderline(false);
                            $printer -> feed();
                            /* END OF RECEIPT CONTENTS */
                            receipt_footer($printer, $rows->orno, $rows->trnno);

                            $printer -> feed();
                            $printer -> cut();
                            $printer -> pulse();
                            $printer -> close();

                        }

                    }
                }
            }
        }
        echo json_encode($payments);
        exit();
    }

    function paycad() {
        //$computername = $this->input->post('computername');
        $payments = $this->model_tellering->pay_cad();

        echo json_encode($payments);
        exit();
        if($payments->qry==true) {
            $groups = $this->db->select('groups')->from('transaction_payments_logs')
                ->where_in('sysid', $payments->pids)
                ->group_by('groups')
                ->get();
            if($groups->num_rows()>0) {
                foreach($groups->result() as $row) {
                    $receipt = $this->db->select()->from('transaction_payments_logs')
                        ->where('groups', $row->groups)
                        ->where_in('sysid', $payments->pids)
                        ->get();
                    $trn_cnt = $receipt->num_rows();
                    //$trn_cnt = 0;
                    if($trn_cnt>0) {
                        $conn = windows_printer_connector($computername);
                        // CHECK IF CONNECTION STATUS IS TRUE
                        if ($conn->res == true) {

                            $printer = $conn->printer;
                            $appid = $payments->appid;
                            $info = get_application_details($appid);
                            $servno = ($info->info->servno != '') ? $info->info->servno : str_pad($appid, 8, '0', STR_PAD_LEFT);
                            $name = $info->info->firstname.' '.$info->info->lastname;

                            receipt_header($printer);
                            /* Name of Account Paid */
                            $printer -> setJustification($printer::JUSTIFY_LEFT);
                            $printer -> text(two_cols("Service No.: " . $servno, "TIN: N/A"));
                            $printer -> text("Name: " . $name."\n");
                            $printer -> text("Address: " . $info->info->addrspec."\n");
                            $printer -> feed();

                            /* Title of receipt */
                            $printer -> setJustification($printer::JUSTIFY_CENTER);
                            $printer -> setEmphasis(true);
                            $printer -> setUnderline(true);
                            $printer -> text(space_both_sides("O F F I C I A L   R E C E I P T"));
                            $printer -> setEmphasis(false);
                            $printer -> setUnderline(false);

                            /* RECEIPT CONTENTS */
                            /* Top Table */
                            $printer -> setJustification($printer::JUSTIFY_LEFT);
                            $printer -> setUnderline(true);
                            $printer -> text(two_cols('Transaction', 'Amount'));
                            $printer -> setUnderline(false);

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

                            foreach($receipt->result() as $rows) {
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
                                    $string = $endPoint? substr($stringCut, 0, $endPoint):substr($stringCut, 0);
                                    $string .= '...';
                                }
                                $check = false;
                                if($rows->payform==2) {
                                    $check = true;
                                    $check_cnt += 1;
                                }
                                if($rows->payform==1) {
                                    $cash_cnt += 1;
                                }

                                $printer -> text(two_cols_a($string, number_format($rows->totalamt, 2), $check));
                            }

                            // CHECK PAYMENT FORM
                            if($cash_cnt > 0 && $check_cnt > 0) {
                                $payform = 'Cash/Check';
                            }else{
                                if($cash_cnt>0) {
                                    $payform = 'Cash';
                                }else{
                                    $payform = 'Check';
                                }
                            }

                            // ##############################
                            $printer -> setUnderline(true);
                            $printer -> text(space_both_sides("Form of Payment: " . $payform));
                            $printer -> setUnderline(false);
                            $printer -> setEmphasis(true);
                            $printer -> text("Tax Breakdown: \n");
                            $printer -> setEmphasis(false);
                            $printer -> text(three_cols('VAT Sales', 'VAT Tax Amt', 'Sub-Total'));
                            $printer -> text(three_cols(number_format($vatsales_amt,2), number_format($vattax_amt, 2), number_format($sabtotal_amt, 2)));
                            $printer -> text(two_cols("ZERO RATED TAX", number_format($vatzerorated_amt, 2)));
                            $printer -> text(two_cols("VAT EXEMPT", number_format($vatexempt_amt, 2)));
                            $printer -> text(two_cols("CWT", number_format($cwt_amt, 2)));
                            $printer -> text(two_cols("NON VAT SALES", number_format($nonvatsales_amt, 2)));
                            $printer -> setUnderline(true);
                            $printer -> setEmphasis(true);
                            $printer -> text(two_cols("Total", number_format($total_amt, 2)));
                            $printer -> setEmphasis(false);
                            $printer -> setUnderline(false);
                            $printer -> feed();
                            /* END OF RECEIPT CONTENTS */
                            receipt_footer($printer, $rows->orno, $rows->trnno);

                            $printer -> feed();
                            $printer -> cut();
                            $printer -> pulse();
                            $printer -> close();

                        }

                    }
                }
            }
        }
        echo json_encode($payments);
        exit();
    }

    function paylegal(){
        $payments = $this->model_tellering->pay_legal();
        echo json_encode($payments);
    }
    
    // TEST ONLY
    public function test($amt) {
        $check_amt = $amt;

        $arr = array(
            array(
                'amt' => 2126.08,
                'code' => 'A1'
            ),
            array(
                'amt' => 12000,
                'code' => 'A2'
            ),
            array(
                'amt' => 89.60,
                'code' => 'B2'
            ),
        );
        echo 'Amount Check: ' . $check_amt;
        echo '<table border="1px">';
        echo '<head><th>Code</th><th>Amt Orig</th><th>Amt</th><th>Chk Bal</th><th>Mode</th></head>';
        echo '<tbody>';
        foreach($arr as $row) {

            $amt_topd = $row['amt'];
            $check_pd = 0;
            $cash_pd = 0;
            $mode = 'Cash';

            if($check_amt > 0) {
                $mode = 'Check';
                if($check_amt < $amt_topd) {
                    if($check_amt > 0) {
                        $amt_check = $check_amt + $cash_pd;
                        echo '<tr>';
                        echo '<td>'.$row['code'].'</td>';
                        echo '<td>'.$amt_topd.'</td>';
                        echo '<td>'.$check_amt.'</td>';
                        echo '<td>'.$amt_check.'</td>';
                        echo '<td>Check</td>';
                        echo '</tr>';

                        $amt_cash = $amt_topd - $amt_check;
                        echo '<tr>';
                        echo '<td>'.$row['code'].'</td>';
                        echo '<td>'.$amt_topd.'</td>';
                        echo '<td>'.$amt_cash.'</td>';
                        echo '<td>'.$amt_cash.'</td>';
                        echo '<td>Cash</td>';
                        echo '</tr>';
                    }
                }else{
                    $check_bal = $check_amt - $amt_topd;
                    $mode = 'Check';
                    echo '<tr>';
                    echo '<td>'.$row['code'].'</td>';
                    echo '<td>'.$amt_topd.'</td>';
                    echo '<td>'.$amt_topd.'</td>';
                    echo '<td>'.$check_bal.'</td>';
                    echo '<td>'.$mode.'</td>';
                    echo '</tr>';
                }
            }else{
                $mode = 'Cash';
                echo '<tr>';
                echo '<td>'.$row['code'].'</td>';
                echo '<td>'.$amt_topd.'</td>';
                echo '<td>'.$amt_topd.'</td>';
                echo '<td>'.$amt_topd.'</td>';
                echo '<td>'.$mode.'</td>';
                echo '</tr>';
            }
            $check_amt -= $amt_topd;
        }
        echo '</tbody>';
        echo '</table>';
    }

    function getpaylist(){
        $data = array();

        $d = date('d');
        $m = date('m');
        $y = date('Y');
        $sql = $this->db->select("sysid,orno,payforacctno,totalamt,dataid,trnno")
            ->from("transaction_payments_logs")
            ->where(array( 'YEAR(datecreated)' => $y,
                'MONTH(datecreated)' => $m,
                'DAY(datecreated)' => $d,
                'createdby' => user_id(),'status'=> 1))
            ->order_by("trnno" , "ASC")
            ->get();
        $num_rows = $sql->num_rows();
        if($num_rows > 0){
            foreach ($sql->result() as $row){

                $data['list'][] = array(
                    'trnno' => $row->trnno,
                    'accno' => $row->orno,
                    'payfor' => $row->payforacctno,
                    'totalamt' => number_format($row->totalamt, 2),
                    'checkbox' => ' <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="checkbox" value="'.$row->sysid.'">
                                    <label class="form-check-label" for="checkbox"></label>
                                  </div>'
                );
            }
        }
        echo json_encode($data);
    }
    function getpaymentdetails(){
        $data = array();
        $d = date('d');
        $m = date('m');
        $y = date('Y');
        $ids = $this->input->post('ids'); // orno

        $sql = $this->db->select("SUM(totalamt) AS totalamount , SUM(vattaxamt) AS vatamount , SUM(franchisetax) AS framount , SUM(nonvatsales) AS novatamount")
            ->from("transaction_payments_logs")
            ->where(array( 'YEAR(datecreated)' => $y,
                'MONTH(datecreated)' => $m,
                'DAY(datecreated)' => $d,
                'createdby' => user_id(),'status'=> 1))
            ->where_in("orno" , $ids)
            ->get()->row();

        $qry_groupid = $this->db->select_max('groupid')->from('trn_request_orvoid')->get()->row();
        $new_groupid = ($qry_groupid) ? $qry_groupid->groupid + 1 : 1;

        if($sql){
            $data['totalamount'] = number_format($sql->totalamount , 2);
            $data['vattaxamt'] = number_format($sql->vatamount , 2);
            $data['franchisetax'] = number_format($sql->framount , 2);
            $data['nonvatt'] = number_format($sql->novatamount , 2);
            $data['dataid'] = $new_groupid;
        }
        echo json_encode($data);
    }

    function transactions() {
        $this->load->view('user/trnlist');
    }

    function trnlist() {
        echo $this->model_tellering->get_trn_list();
    }

    function ordetails() {
        echo $this->model_tellering->get_trn_details();
    }

    function savevalidation() {
        echo $this->model_tellering->save_user_validation();
    }


    function printtest() {
        $computername = $this->input->post('computername');
        $data = array();
        if(user_id() > 0) {
            $conn = windows_printer_connector($computername);
            // CHECK IF CONNECTION STATUS IS TRUE
            if ($conn->res == true) {

                $printer = $conn->printer;

                receipt_header($printer);
                $printer->feed();

                $printer->setJustification($printer::JUSTIFY_CENTER);
                $printer->setEmphasis(true);
                $printer->setUnderline(true);
                $printer->text(space_both_sides("T E S T I N G - P R I N T"));
                $printer->setEmphasis(false);
                $printer->setUnderline(false);


                $printer->feed();
                $printer->cut();
                $printer->pulse();
                $printer->close();

                $msg = 'Test print has been set!';
                $func = 'success';
            }else{
                $func = 'warning';
                $msg = $conn->message;
            }
        }else{
            $msg = 'Please login!';
            $func = 'warning';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function printvalidation() {
        $computername = $this->input->post('computername');
        $this->db->where('CAST(datecreated AS DATE) = ', 'CAST(NOW() AS DATE)', false);
        $qry_trn = $this->db->select()
            ->from('transaction_payments_logs')
            ->where(array('createdby' => user_id(), 'status' => 1))
            ->order_by('trnno')
            ->get();

        $qry = false;
        $msg = '';
        $func = 'error';

        if(user_id() > 0) {
            $conn = windows_printer_connector($computername);
            // CHECK IF CONNECTION STATUS IS TRUE
            if ($qry_trn->num_rows() > 0) {
                if ($conn->res == true) {
                    $printer = $conn->printer;

                    receipt_header($printer);
                    $printer->setJustification($printer::JUSTIFY_LEFT);
                    $printer->setFont($printer::FONT_B);
                    $printer->text(two_cols("Date: " . sql_time()->DATENAME, sql_time()->TIME12));
                    $printer->text("Cashier Code: " . user_info()->sysid . " - " . trim(user_info()->firstname) . " " . trim(user_info()->lastname) . "\n");


                    $printer->setJustification($printer::JUSTIFY_CENTER);
                    $printer->setEmphasis(true);
                    $printer->setUnderline(true);
                    $printer->text(space_both_sides("V A L I D A T I O N"));
                    $printer->setEmphasis(false);
                    $printer->setUnderline(false);

                    /* RECEIPT CONTENTS */
                    /* Top Table */
                    $printer->setJustification($printer::JUSTIFY_LEFT);
                    $printer->setUnderline(true);
                    $printer->text(four_cols_br('REF', 'SERVICE', 'REPL', 'AMOUNT'));
                    $printer->setUnderline(false);


                    foreach ($qry_trn->result() as $row) {
                        $check = ($row->payform == 2) ? true : false;
                        $servno = 'N/A';
                        if ($row->moduleid == 43) {
                            $qry_acct = $this->db->select('servicenumber')->from('customer_accounts_main')
                                ->where('sysid', $row->dataid)->get()->row();
                            $servno = ($qry_acct) ? $qry_acct->servicenumber : 'N/A';
                        }

                        if ($row->moduleid == 35) {
                            $qry_acct = $this->db->select('sysid')->from('application_customers_details')
                                ->where('sysid', $row->dataid)->get()->row();
                            $servno = ($qry_acct) ? str_pad($qry_acct->sysid, 8, '0', STR_PAD_LEFT) : 'N/A';
                        }

                        //$printer -> text(two_cols_a(str_pad($row->trnno, 3, '0', STR_PAD_LEFT). ' - '. str_pad($row->orno, 8, '0', STR_PAD_LEFT), number_format($row->totalamt, 2), $check));
                        $printer->text(four_cols_br(str_pad($row->trnno, 3, '0', STR_PAD_LEFT), $servno, '1', number_format($row->totalamt, 2), $check));
                    }

                    $printer->setJustification($printer::JUSTIFY_CENTER);
                    $printer->text(space_both_sides("* * * * * NOTHING FOLLOWS * * * * *"));
                    /* END OF RECEIPT CONTENTS */
                    $printer->feed();
                    $printer->cut();
                    $printer->pulse();
                    $printer->close();

                    $msg = 'Printing....';
                    $func = 'success';
                    $qry = true;
                } else {
                    $msg = $conn->message;
                    $func = 'warning';
                }
            } else {
                $msg = 'No transaction yet!';
                $func = 'warning';
            }
        }else {
            $msg = 'Please login!';
            $func = 'error';
        }
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        echo json_encode($data);
    }




    function printordetails() {
        $computername = $this->input->post('computername');
        $orno = $this->input->post('orno');
        $this->db->where('CAST(datecreated AS DATE) = ', 'CAST(NOW() AS DATE)', false);
        $qry_trn = $this->db->select()
            ->from('transaction_payments_logs')
            ->where(array('createdby' => user_id(), 'orno' => $orno))
            ->order_by('trnno')
            ->get();
        if(user_id() > 0) {
            $conn = windows_printer_connector($computername);
            // CHECK IF CONNECTION STATUS IS TRUE
            if ($conn->res == true) {
                $printer = $conn->printer;

                receipt_header($printer);
                $printer -> setJustification($printer::JUSTIFY_LEFT);
                $printer -> setFont($printer::FONT_B);
                $printer -> text(two_cols("Date: " .sql_time()->DATENAME, sql_time()->TIME12));
                $printer -> text("Cashier Code: ".user_info()->sysid." - ".trim(user_info()->firstname)." ".trim(user_info()->lastname)."\n");
                $printer -> text("OR no.: ".str_pad($orno, 8, '0', STR_PAD_LEFT)."\n");


                $printer -> setJustification($printer::JUSTIFY_CENTER);
                $printer -> setEmphasis(true);
                $printer -> setUnderline(true);
                $printer -> text(space_both_sides("T R A N S A C T I O N S"));
                $printer -> setEmphasis(false);
                $printer -> setUnderline(false);

                /* RECEIPT CONTENTS */
                /* Top Table */
                $printer -> setJustification($printer::JUSTIFY_LEFT);
                $printer -> setUnderline(true);
                $printer -> text(two_cols('Transaction', 'Amount CHK'));
                $printer -> setUnderline(false);
                $total_amt = 0;
                if($qry_trn->num_rows()>0) {
                    foreach($qry_trn->result() as $row) {

                        $check = ($row->payform==1) ? true : false;
                        $total_amt += $row->totalamt;
                        $printer -> text(two_cols_a(str_pad($row->trnno, 6, '0', STR_PAD_LEFT). ' - '. str_pad($row->orno, 8, '0', STR_PAD_LEFT), number_format($row->totalamt, 2), $check));
                    }
                }
                $printer -> setJustification($printer::JUSTIFY_LEFT);
                $printer -> setUnderline(true);
                $printer -> setEmphasis(true);
                $printer -> text(two_cols('Total: ', number_format($total_amt, 2). " \n"));
                $printer -> setUnderline(false);
                $printer -> setEmphasis(false);
                $printer -> setJustification($printer::JUSTIFY_CENTER);
                $printer -> text(space_both_sides("***** NOTHING FOLLOWS *****"));
                /* END OF RECEIPT CONTENTS */
                $printer -> feed();
                $printer -> cut();
                $printer -> pulse();
                $printer -> close();

                $data['msg'] = 'Printing....';
            } else {
                $data['msg'] = $conn->message;
            }
        }else{
            $data['msg'] = 'Please login!';
        }

    }


    function totalcollectiontoday() {
        echo $this->model_tellering->total_collection_today();
    }
    function totalcollectionthisweek() {
        echo $this->model_tellering->total_collection_thisweek();
    }
    function totalcollectionthismonth() {
        echo $this->model_tellering->total_collection_thismonth();
    }
    function totalcollectionthisyear() {
        echo $this->model_tellering->total_collection_thisyear();
    }

    function testing() {
        $due_dates = array();
        $qt = 3;
        $sales_due_date = "2015-09-21";
        // create a time stamp of the date
        $time = strtotime($sales_due_date);
        for($i=0;$i<$qt;$i++){
            // convert timestamp back to date string
            $date = date('Y-m-d', $time);
            echo $date . '<br>';
            // move to next timestamp
            $time = strtotime('+1 month', $time);
        }
    }
}

?>