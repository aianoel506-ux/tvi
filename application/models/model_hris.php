<?php

// ############################################
// AUTHOR : LUCKY JOHN FADERON - SE
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_hris extends CI_Model {
     /*
      function emplist_model() {
      $stat = $this->input->post('stat');
      $depid = $this->input->post('depid');
      $modulehash = $this->input->post('modulehash');
      $this->datatables->select('e.sysid, e.empid, e.status, p.firstname, p.lastname, p.middlename, p.gender, p.birthdate');
      $this->datatables->unset_column('firstname')->add_column('firstname', '$1', 'firstname');
      $this->datatables->unset_column('lastname')->add_column('lastname', '$1$2', 'gender_icon(gender) , lastname');
      $this->datatables->unset_column('gender')->add_column('gender', '$1', 'gender_icon(gender)');
      $this->datatables->unset_column('birthdate')->add_column('birthdate', '$1', 'birthdate');
      $this->datatables->unset_column('empid')->add_column('empid', '$1', 'data_empty(empid)');
      $this->datatables->unset_column('status')->add_column('empstat', '$1', 'row_status(status)');
      $this->datatables->unset_column('sysid')->add_column('controls', '$1', 'row_btn_view(' . $modulehash . ', sysid, true, \'View\')');
      $this->datatables->add_column('department', '$1', 'N/A');
      $this->datatables->add_column('position', '$1', 'N/A');
      $this->datatables->from("prime_employee_main e");
      //add filter default active
      $this->datatables->where("e.status", 1);

      $this->datatables->join("person p", "e.personid = p.sysid");
      if ($stat) {
      $this->datatables->where('e.status', $stat);
      }
      return $this->datatables->generate();
      }
     */

    function employee_list() {
        $data = array();
        $html = '';
        $payclass = $this->input->post('class');
        $ccid = $this->input->post('dept');
        $moduleid = $this->input->post('moduleid');

        $process = $this->input->post('process');
        $clear = $this->input->post('clear');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $preview = $this->input->post('preview');
        $parentid = 0;
        $i = 1;
        $stat = $this->input->post('status');
        $hashcode =  $this->input->post('modulehash');
        $view_type = $this->input->post('viewtype');
        $dept = $this->input->post('dept');
        $typestat = $this->input->post('typestat');
        $payclass_filter = $this->input->post('payclass');
        $jobcat = $this->input->post('jobcat');
        $report = $this->input->post('report');

        $totalearningssum = 0;
        $totalloanssum = 0;
        $totalpremiumssum = 0;
        $totaltaxsum = 0;
        $totaldeductionssum = 0;
        $totalnetsum = 0;

        $group_id = 0;
        $group_id_ui = 0;
        $group_status = 0;
        $group_status_ui = 0;
        $payrollstat = '';

        if($report > 0){
            $html .= '';
            $html .= peco_print_header(user_id(),"Employee Report", false, false);;
            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">';
            $html .= '<table class="table table-bordered table-condensed tbl-xs">';
            $html .= '<thead>';
            $html .= '<th></th>';
            $html .= '<th>Emp. Code</th>';
            $html .= '<th>Lastname</th>';
            $html .= '<th>Firstname</th>';
            $html .= '<th>Middlename</th>';
            $html .= '<th>Department</th>';
            $html .= '</thead>';
            $html .= '<tbody>';
        }

        // GET PARENT HASH DATA
        $cur_nav = $this->db->select('parent')->from('prime_module_navigations_main')
            ->where('sysid', $moduleid)
            ->get()->row();
        if($cur_nav) {
            $qry_parent = $this->db->select('sysid, hashcode')->from('prime_module_navigations_main')
                ->where(array('sysid' => $cur_nav->parent))->get()->row();
            if($qry_parent) {
                $hashcode = $qry_parent->hashcode;
                $parentid = $qry_parent->sysid;
            }
        }

        if($payclass==1) {
            $paytype = 1;
        }else {
            $paytype = $this->input->post('paytype');
        }

        $data['paytype'] = $paytype;

        $group_qry = $this->db->select()
            ->from('payroll_reports_group')
            ->where(array(
                'years' => $year,
                'months' => $month,
                'payclass' => $payclass,
                'paytype' => $paytype,
                'status != ' => 302
            ))
            ->order_by('sysid', 'desc')
            ->limit(1)
            ->get()->row();

        if ($group_qry) {
            $group_id = $group_qry->sysid;
            $group_status = (int) $group_qry->status;

            if ($group_status == 301) {
                $group_id_ui = $group_id;
                $group_status_ui = 301;
                $payrollstat = '<i class="fa fa-check-square-o"></i> Approved';
            } else {
                $group_id_ui = 0;
                $group_status_ui = 0;
                $payrollstat = '';
            }
        }

        if($payclass>0) {
            if($payclass==1) {
                $this->db->where_in('empc.payclass_id', array(129,130));
            }else {
                $this->db->where('empc.payclass_id', $payclass);
            }
        }



        if($ccid > 0) {
            $this->db->where('ec.ccid', $ccid);
        }



        $jobcatarr = array(157 , 160);
        if($view_type == 1 || $view_type == 4){
            $this->db->where(array('emp.status' => 1, 'ec.type' => 1));
            // $this->db->where(array('emp.status' => 1, "ec.type" => 1,"peb.status" => 1)); // force view no bank no.
            $this->db->where_in('pemjc.jobcatid' , $jobcatarr);
            $typestat = 1;
        }else{
            //2 is ALL
            if($stat != 2){
                if ($stat == 1) {
                    $this->db->where(array("emp.status" => $stat));
                } else {
                    /*$empstat = $this->db->select('sysid')->from('prime_types_parameter')
                        ->where('codes','EMPSTATUS')->get();
                    $empstatus[] = 0;
                    if ($empstat->num_rows() > 0){
                        foreach ($empstat->result() as $empstats){
                            $empstatus[] = $empstats->sysid;
                        }
                    }*/
                    $this->db->where("emp.status != ",1);
                }
            }
            if($jobcat > 0){
                $this->db->where(array("pemjc.jobcatid" => $jobcat));
            }
            if($payclass_filter > 0){
                $this->db->where(array('empc.payclass_id' =>$payclass_filter));
            }
            if($dept > 0){
                $this->db->where(array('cc.sysid' =>$dept));
            }

        }



        if($typestat > 0){
            $this->db->where('emp.type', $typestat);
        }



        $query = $this->db->select('
                    emp.sysid, 
                    emp.empid,
                    emp.status,
                    emp.personid,
                    emp.datecreated,
                    emp.createdby,
                    ec.ccid,
                    p.firstname,
                    p.lastname,
                    p.middlename,
                    p.gender,
                    p.birthdate,
                    cc.desc AS deptname,
                    cc.names AS deptcode,
                    cc.codes,
                    empc.payclass_id AS payclassid,
                    peb.bioid,
                    pe.accntno,
                    pemjc.jobcatid
                ')
            ->from('prime_employee_main AS emp')
            ->join('prime_employee_main_payclass AS empc', 'empc.emp_id = emp.sysid AND empc.status = 1', 'left')
            ->join('person AS p', 'p.sysid = emp.personid', 'left')
            ->join('prime_employee_costcenter AS ec', 'ec.empid = emp.sysid AND ec.status = 1', 'left')
            ->join('prime_costcenter_main AS cc', 'cc.sysid = ec.ccid AND ec.status = 1', 'left')
            ->join('payroll_emplist as pe','pe.empid = emp.sysid AND pe.status = 1' , 'left')
            ->join('prime_employee_main_job_category as pemjc' , "pemjc.empid = emp.sysid AND pemjc.status = 1" , "left")
            ->join("prime_employee_bioid as peb" , "peb.empid = emp.sysid AND peb.status = 1" , "left")
            ->where(array("ec.type" => 1 , "ec.status" => 1 ))
            ->group_by('emp.empid,
                    emp.sysid,   
                    emp.status,  
                    emp.personid, 
                    emp.datecreated,
                    emp.createdby,
                    ec.ccid,
                    p.firstname,
                    p.lastname,
                    p.middlename,
                    p.gender,
                    p.birthdate,
                    cc.desc,
                    cc.names,
                    cc.codes,
                    empc.payclass_id,
                    peb.bioid,
                    pe.accntno,
                    pemjc.jobcatid')
            ->order_by('p.lastname', "asc")
            ->get();
        $data['queryerr'] = $this->db->_error_message();
        $data['query'] = $this->db->last_query();



        if ($query->num_rows() > 0) {
            $total_net = 0;
            $total_deduction = 0;

            if($process==1) {
                //   $this->db->where(array('months' => $month, 'years' => $year, 'paytype' => $paytype));
                //   $this->db->update('payroll_reports_group', array('updatedby' => user_id(), 'status' => 0));
                if ($group_id > 0) {
                    $this->db->where(array('sysid' => $group_id));
                    $this->db->update('payroll_reports_group', array('status' => 1, 'updatedby' => user_id()));
                } else {
                    $ins_payroll_group_arr = array(
                        'months' => $month,
                        'years' => $year,
                        'payclass' => $payclass,
                        'paytype' => $paytype,
                        'status' => 1,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $this->db->insert('payroll_reports_group', $ins_payroll_group_arr);
                    $group_id = $this->db->insert_id();
                }
                $group_status = 1;
                $group_id_ui = 0;
                $group_status_ui = 0;
                $payrollstat = '';
                $payclassname = '';
                if($payclass==128) {
                    $payclassname = get_types_name($payclass)->names;
                }else{
                    $payclassname = 'Confidentials';
                }
                create_transaction_trails('PAYROLL: '.$year.'-'.$month.' | '.$payclassname, 'PAYROLL: '.$year.'-'.$month.' | '.$payclassname, $parentid, $group_id);
                //$approval = create_transaction_trails('PAYROLL: '.$year.'/'.$month, 'PAYROLL: '.$year.'/'.$month, 39, 0);
            }

            if($clear==1) {
                // ADMIN AND DEV ONLY
                $this->db->trans_begin();
                //     $this->db->query("TRUNCATE TABLE payroll_transactions;");
                //  $this->db->query("TRUNCATE TABLE payroll_reports_group;");
                //   $this->db->query("TRUNCATE TABLE payroll_reports_main;");
                //    $this->db->query("TRUNCATE TABLE payroll_reports_trn;");

                $stages = $this->db->select('sysid')->from('prime_transaction_flow_main_stages')
                    ->where('flowid', 10)
                    ->get();

                $this->db->delete('transaction_request_main', array('flowid' => 10));
                if($stages->num_rows()>0) {
                    foreach($stages->result() as $row) {
                        $this->db->delete('transaction_request_main_trails', array('sysid' => $row->sysid));
                    }
                }
                if($this->db->trans_status()===TRUE) {
                    $this->db->trans_commit();
                }else{
                    $this->db->trans_rollback();
                }
            }

            $num = 1;

            foreach ($query->result() as $row) {
                $empid = $row->sysid;
                $payclass = $row->payclassid;

                $viewbtn    = row_btn_view($hashcode, $empid, true, '');
                if($stat == 1){
                    if($row->status == 1) {
                        $btn_class = 'btn-success';
                        $btn_icon = 'fa-check';
                    }else{
                        $btn_class = 'btn-danger';
                        $btn_icon = 'fa-times';
                    }
                    if (super_admin()) {
                        $btn_stats   = '<button type="button" id="btn_status" data-stat="'.$row->status.'" data-id="' . $row->sysid . '" class="btn '.$btn_class.' btn-xs"><i class="fa '.$btn_icon.'"></i></button> ';
                    } else {
                        $btn_stats = '';
                    }
                    $control    = $btn_stats.$viewbtn;
                }else if($stat == 2){
                    if($row->status == 1){
                        $statdesc = '<span class="text-success"><i class="fa fa-toggle-on"></i></span>';
                    }else if($row->status == 0){
                        $statdesc = '<span class="text-danger"><i class="fa fa-toggle-off"></i></span>';
                    }
                    $control    = $statdesc.' '.$viewbtn;
                }else if($stat == 0){
                    if($row->status == 1) {
                        $btn_class = 'btn-success';
                        $btn_icon = 'fa-check';
                    }else{
                        $btn_class = 'btn-danger';
                        $btn_icon = 'fa-times';
                    }
                    if (super_admin()) {
                        $btn_stats   = '<button type="button" id="btn_status" data-stat="'.$row->status.'" data-id="' . $row->sysid . '" class="btn '.$btn_class.' btn-xs"><i class="fa '.$btn_icon.'"></i></button> ';
                    }else{
                        $btn_stats = '';
                    }
                    $control    = $btn_stats.$viewbtn;
                }else{
                    if($row->status == 1){
                        $statdesc = '<span class="text-success"><i class="fa fa-toggle-on"></i></span>';
                    }else if($row->status == 0){
                        $statdesc = '<span class="text-danger"><i class="fa fa-ban"></i></span>';
                    }
                    $control    = $statdesc.' '.$viewbtn;
                }

                $department = '<a class="popovers" href="javascript:;" title="'.$row->deptcode.'" data-content="'.$row->deptname.'" data-trigger="hover">'.$row->codes.' - '.$row->deptcode.'</a>';

                if($view_type==1) {
                    $check_stat = '';
                    $salary = 0;
                    $deduction = 0;
                    $netpay = 0;
                    $earnings = 0;
                    $loans = 0;
                    $premiums = 0;
                    $tax = 0;

                    //check if there is distributed
                    $checkfordistributedamt = $this->db->select("paytype")->from("payroll_transactions")
                        ->where(array("empid" => $empid , "months" => $month , "years" => $year , "paytype" => 0))
                        ->get()->row();
                    $popover = ($checkfordistributedamt) ? $checkfordistributedamt->paytype : $paytype;
                    $data['popover'] = $popover;
                    $compute = compute_employee_netpay($empid, $month, $year, $paytype, $popover, $payclass , $view_type);
                    if($compute) {
                        $deduction = $compute->deductionamount;
                        $netpay = $compute->netpay;
                        $loans = $compute->loans;
                        $premiums = $compute->premiums;
                        $earnings = $compute->earnings;
                        $salary = $compute->basic;
                        $check_stat = $compute->status;

                        $checkforannualtax = $this->db->select("amount")->from("payroll_anual_tax_distribution")
                            ->where(array("month" => $month , "year" => $year , "empid" => $row->sysid , "status" => 313))->get()->row();

                        if($checkforannualtax){
                            if($payclass != 128){
                                $tax = $checkforannualtax->amount * 2;
                            }else{
                                $tax = $checkforannualtax->amount;
                            }
                        }else{
                            $tax = $compute->taxamt;
                        }


                    }
                    $total_net += $netpay;
                    $total_deduction += $deduction;

                    $prerequisit_inputs = '
                        <input value=\''.$empid.'\' type=\'hidden\' name=\'empid\' class=\'form-control input-md\' id=\'empid\' />
                        <input value=\''.$month.'\' type=\'hidden\' name=\'month\' class=\'form-control input-md\' id=\'month\' />
                        <input value=\''.$year.'\' type=\'hidden\' name=\'year\' class=\'form-control input-md\' id=\'year\' />
                        <input value=\''.$paytype.'\' type=\'hidden\' name=\'paytype\' class=\'form-control input-md\' id=\'paytype\' />
                        <input value=\''.$payclass.'\' type=\'hidden\' name=\'payclass\' class=\'form-control input-md\' id=\'payclass\' />
                    ';

                    $payspecific = '
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Type</label>
                                <select style=\'width: 100%\' id=\'payspec\' name=\'payspec\' class=\'form-control input-sm\'>
                                    <option></option>
                                    <option value=\'0\' selected>Distributed</option>
                                    <option value=\'1\'>1st Half</option>
                                    <option value=\'2\'>2st Half</option>
                                </select>
                            </div>
                         
                            
                ';


                    $loans_popovers = '
                    <form id=\'frm_transaction_loan_entry\' style=\'\' class=\'form-horizontal\' action=\''.base_url().'payroll/addpayrolltransactions\' method=\'post\'>
                        '.$prerequisit_inputs.'
                        <input value=\'0\' type=\'hidden\' name=\'trntype\' class=\'form-control input-md\' id=\'year\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Loan Type</label>
                                <input required type=\'text\' name=\'type\' class=\'form-control input-md\' id=\'type\' style=\'width: 100% !important;\' />
                            </div>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Loan Amount</label>
                                <input required type=\'text\' name=\'amt\' class=\'form-control input-md\' id=\'amt\' placeholder=\'Amount this month\' style=\'width: 100%;\'/>
                            </div>
                            '.$payspecific.'
                        </div>
                        
                        <div class=\'form-actions bottom margin-top-20\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button type=\'submit\' class=\'btn blue\'>Save</button>
                        </div>
                    </form>
                 ';
                    $earnings_popovers = '
                    <form id=\'frm_transaction_earnings_entry\' style=\'\' class=\'form-horizontal\' action=\''.base_url().'payroll/addpayrolltransactions\' method=\'post\'>
                        '.$prerequisit_inputs.'
                        <input value=\'1\' type=\'hidden\' name=\'trntype\' class=\'form-control input-md\' id=\'year\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Earning Type</label>
                                <input required type=\'text\' name=\'type\' class=\'form-control input-md\' id=\'type\' style=\'width: 100% !important;\' />
                            </div>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Earning Amount</label>
                                <input required type=\'text\' name=\'amt\' class=\'form-control input-md\' id=\'amt\' placeholder=\'Amount this month\' style=\'width: 100%;\'/>
                            </div>
                            '.$payspecific.'
                        </div>
                        <div class=\'form-actions bottom margin-top-20\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button data-style=\'slide-up\' type=\'submit\' class=\'btn blue\'>Save</button>
                        </div>
                    </form>
                 ';
                    $deduction_popovers = '
                    <form id=\'frm_transaction_deduction_entry\' style=\'\' class=\'form-horizontal\' action=\''.base_url().'payroll/addpayrolltransactions\' method=\'post\'>
                        '.$prerequisit_inputs.'
                        <input value=\'2\' type=\'hidden\' name=\'trntype\' class=\'form-control input-md\' id=\'year\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Deduction Type</label>
                                <input required type=\'text\' name=\'type\' class=\'form-control input-md\' id=\'type\' style=\'width: 100% !important;\' />
                            </div>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Deduction Amount</label>
                                <input required type=\'text\' name=\'amt\' class=\'form-control input-md\' id=\'amt\' placeholder=\'Amount this month\' style=\'width: 100%;\'/>
                            </div>
                            '.$payspecific.'
                        </div>
                        <div class=\'form-actions bottom margin-top-20\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button data-style=\'slide-up\' type=\'submit\' class=\'btn blue\'>Save</button>
                        </div>
                    </form>
                 ';

                    if($month == 1){
                        $monthlate = 12;
                        $yearlate = $year - 1;
                    }else{
                        $monthlate = $month - 1;
                        $yearlate = $year;
                    }
                    $gettotallatelastmonth  = $this->db->select("SEC_TO_TIME( SUM( TIME_TO_SEC( `totallate` ) ) ) AS timeSum")
                        ->from("attendance_reports")->where(array("month" => $monthlate , "empid" => $row->sysid , "year" => $yearlate , "paid" => 0 , "status" => 1))->get()->row();

                    $gettotalattendancepay = $this->db->select("SUM(charge) as TOTALATTENDANCECHARGE")->from("attendance_reports")
                        ->where(array("month" => $monthlate , "empid" => $row->sysid , "year" => $yearlate , "paid" => 0 , "status" => 1))->get()->row();

                    $data['month'] = $month;
                    $data['year'] = $year;

                    $row_color = (($premiums == 0 && $paytype == 1 && $payclass == 3078) || ($premiums == 0 && $paytype == 1 && $payclass == 3077) || ($premiums == 0 && $payclass == 1) || ($premiums == 0 && $paytype == 1 && $payclass == 128) || $salary == 0  || $row->accntno == null || $row->accntno == 0 || $row->accntno == '' || strlen($row->accntno) != 9 || $netpay <= 0) ? 'danger' : '';
                    $printbtn = '<button id="btn_print_payslip_temp" data-id="'.$empid.'" class="btn btn-default inline btn-xs" type="button"><i class="fa fa-print"></i></button>';
                    $status = ($salary == 0) ? '<i class="fa fa-warning font-yellow-gold"></i>' : '<i class="fa fa-check font-green-haze"></i>';

                    if($row->jobcatid == 157){
                        $posdesc = 'R';
                    }else if($row->jobcatid == 160){
                        $posdesc = 'P';
                    }else{
                        $posdesc = 'N/A';
                    }

                    $data['list'][] = array(
                        'expand' => $empid,
                        'num' => $num++,
                        'empid' =>  emp_pic_draw($row->personid, '20px', '20px') . ' '.$posdesc.' - '.$row->accntno,
                        'firstname' => strtoupper($row->firstname),
                        'lastname' => strtoupper($row->lastname) . ' ' .gender_icon($row->gender),
                        'middlename' => strtoupper($row->middlename),
                        'department' => $department,
                        'basic' => number_format($salary, 2), // GET SALARY
                        'earnings' => row_popover_a('earningpopover', number_format($earnings, 2), $earnings_popovers, 'Earning Manual Entry', 'left'),
                        'premiums' => number_format($premiums, 2),
                        //      'tax' => ($checkforannualtax)  ? number_format($checkforannualtax->amount , 2) : number_format($tax, 2),
                        'tax' => number_format($tax , 2),
                        'loans' => row_popover_a('loanpopover', number_format($loans, 2), $loans_popovers, 'Loan Manual Entry', 'left'),
                        'deductions' => row_popover_a('deductionpopover', number_format($deduction, 2), $deduction_popovers, 'Deduction Manual Entry', 'left'),
                        'netpay' => number_format($netpay, 2),
                        //  'lateminutes' => ($gettotallatelastmonth && $gettotallatelastmonth->timeSum != '00:00:00') ? $gettotallatelastmonth-> timeSum : '',
                        //  'latecharge' => ($gettotalattendancepay && $gettotalattendancepay->TOTALATTENDANCECHARGE != '0.00') ? $gettotalattendancepay->TOTALATTENDANCECHARGE : '',
                        'control' => $control,
                        'rowcolor' => $row_color,
                        'status' => ($check_stat!='') ? $check_stat : $status.' '.$printbtn,
                        'payclass' => '<input class="payclass" type="hidden" value="'.$row->payclassid.'" />'
                    );
                    $totalearningssum += $earnings;
                    $totalloanssum += $loans;
                    $totalpremiumssum += $premiums;
                    $totaltaxsum += $tax;
                    $totaldeductionssum += $deduction;
                    $totalnetsum += $netpay;

                    if($process == 1 && $group_id > 0) {

                        if($salary > 0 ) {

                            $insert_arr = array(
                                'empid' => $empid,
                                'groupid' => $group_id,
                                'basic' => ($payclass == 128 || $payclass == 3077 || $payclass == 3078) ?  ($salary / 2) : $salary,
                                'earnings' => $earnings,
                                'deductions' => $deduction,
                                'ccid' => ($row->ccid>0) ? $row->ccid : 0,
                                'tax' => $tax,
                                'net' => $netpay,
                                'createdby' => user_id()
                            );
                            $this->db->insert('payroll_reports_main', $insert_arr);
                            $data['payrollerr'][] = $this->db->_error_message();
                            $payrollid = $this->db->insert_id();
                            if($compute->transactions) {

                                $payspec = 1;
                                $qry_trns = $this->db->select('paytype, payspec,typesid')->from('payroll_transactions')
                                    ->where(array('empid' => $empid, 'years' => $year, 'months' => $month))
                                    ->get()->row();

                                foreach($compute->transactions as $trn_row) {

                                    // CHECK TRANSACTIONO
                                    $trn_amt = $trn_row['amt'];
                                    $trn_type = $trn_row['type'];

                                    if($qry_trns) {
                                        $payspec = $qry_trns->payspec;
                                    }

                                    $trn_ins_arr = array(
                                        'payrollid' => $payrollid,
                                        'trntype' => $trn_type,
                                        'amt' => $trn_amt,
                                        'payspec' => $paytype
                                    );
                                    $this->db->insert('payroll_reports_trn', $trn_ins_arr);
                                    $data['trnerr'][] = $this->db->_error_message();
                                }


                            }

                            $data['process'][] = $insert_arr;

                            //Update all the previous month tardiness  charge
                            if($month == 1){
                                $monthlate = 12;
                                $yearlate = $year - 1;
                            }else{
                                $monthlate = $month - 1;
                                $yearlate = $year;
                            }
                            $updatetardinessarr = array(
                                'paid' => 1
                            );
                            $this->db->where(array("month" => $monthlate , "year" => $yearlate,"payclass" => $payclass , "paid" => 0));
                            $this->db->update("attendance_reports",$updatetardinessarr);

                        }
                    }

                    if($preview == 1) {
                        $trn_arr = array();
                        if($compute->transactions) {

                            $payspec = 1;
                            $qry_trns = $this->db->select('paytype, payspec,typesid')->from('payroll_transactions')
                                ->where(array('empid' => $empid, 'years' => $year, 'months' => $month))
                                ->get()->row();

                            foreach($compute->transactions as $trn_row) {

                                // CHECK TRANSACTIONO
                                $trn_amt = $trn_row['amt'];
                                $trn_type = $trn_row['type'];

                                $trn_arr[] = array(
                                    'payrollid' => $payrollid,
                                    'trntype' => $trn_type,
                                    'amt' => $trn_amt,
                                    'payspec' => $paytype
                                );
                            }
                        }
                        $data['list'][] = array(
                            'empid' => $empid,
                            'groupid' => $group_id,
                            'basic' => ($payclass == 128) ?  ($salary / 2) : $salary,
                            'earnings' => $earnings,
                            'deductions' => $deduction,
                            'ccid' => ($row->ccid>0) ? $row->ccid : 0,
                            'tax' => $tax,
                            'net' => $netpay,
                            'trn' => $trn_arr
                        );
                    }

                }else if($view_type == 4) {

                    $check_stat = '';
                    $salary = 0;
                    $deduction = 0;
                    $netpay = 0;
                    $earnings = 0;
                    $loans = 0;
                    $premiums = 0;
                    $tax = 0;

                    //check if there is distributed
                    $checkfordistributedamt = $this->db->select("paytype")->from("payroll_transactions")
                        ->where(array("empid" => $empid , "months" => $month , "years" => $year , "paytype" => 0))
                        ->get()->row();
                    $popover = ($checkfordistributedamt) ? $checkfordistributedamt->paytype : $paytype;
                    $data['popover'] = $popover;
                    $compute = compute_employee_netpay($empid, $month, $year, $paytype, $popover, $payclass , $view_type);
                    if($compute) {
                        $deduction = $compute->deductionamount;
                        $netpay = $compute->netpay;
                        $loans = $compute->loans;
                        $premiums = $compute->premiums;
                        $earnings = $compute->earnings;
                        $salary = $compute->basic;
                        $check_stat = $compute->status;

                        $checkforannualtax = $this->db->select("amount")->from("payroll_anual_tax_distribution")
                            ->where(array("month" => $month , "year" => $year , "empid" => $row->sysid , "status" => 313))->get()->row();

                        if($checkforannualtax){
                            if($payclass != 128){
                                $tax = $checkforannualtax->amount * 2;
                            }else{
                                $tax = $checkforannualtax->amount;
                            }
                        }else{
                            $tax = $compute->taxamt;
                        }


                    }
                    $total_net += $netpay;
                    $total_deduction += $deduction;

                    $prerequisit_inputs = '
                        <input value=\''.$empid.'\' type=\'hidden\' name=\'empid\' class=\'form-control input-md\' id=\'empid\' />
                        <input value=\''.$month.'\' type=\'hidden\' name=\'month\' class=\'form-control input-md\' id=\'month\' />
                        <input value=\''.$year.'\' type=\'hidden\' name=\'year\' class=\'form-control input-md\' id=\'year\' />
                        <input value=\''.$paytype.'\' type=\'hidden\' name=\'paytype\' class=\'form-control input-md\' id=\'paytype\' />
                        <input value=\''.$payclass.'\' type=\'hidden\' name=\'payclass\' class=\'form-control input-md\' id=\'payclass\' />
                    ';

                    $payspecific = '
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Type</label>
                                <select style=\'width: 100%\' id=\'payspec\' name=\'payspec\' class=\'form-control input-sm\'>
                                    <option></option>
                                    <option value=\'0\' selected>Distributed</option>
                                    <option value=\'1\'>1st Half</option>
                                    <option value=\'2\'>2st Half</option>
                                </select>
                            </div>
                         
                            
                ';


                    $loans_popovers = '
                    <form id=\'frm_transaction_loan_entry\' style=\'\' class=\'form-horizontal\' action=\''.base_url().'payroll/addpayrolltransactions\' method=\'post\'>
                        '.$prerequisit_inputs.'
                        <input value=\'0\' type=\'hidden\' name=\'trntype\' class=\'form-control input-md\' id=\'year\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Loan Type</label>
                                <input required type=\'text\' name=\'type\' class=\'form-control input-md\' id=\'type\' style=\'width: 100% !important;\' />
                            </div>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Loan Amount</label>
                                <input required type=\'text\' name=\'amt\' class=\'form-control input-md\' id=\'amt\' placeholder=\'Amount this month\' style=\'width: 100%;\'/>
                            </div>
                            '.$payspecific.'
                        </div>
                        
                        <div class=\'form-actions bottom margin-top-20\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button type=\'submit\' class=\'btn blue\'>Save</button>
                        </div>
                    </form>
                 ';
                    $earnings_popovers = '
                    <form id=\'frm_transaction_earnings_entry\' style=\'\' class=\'form-horizontal\' action=\''.base_url().'payroll/addpayrolltransactions\' method=\'post\'>
                        '.$prerequisit_inputs.'
                        <input value=\'1\' type=\'hidden\' name=\'trntype\' class=\'form-control input-md\' id=\'year\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Earning Type</label>
                                <input required type=\'text\' name=\'type\' class=\'form-control input-md\' id=\'type\' style=\'width: 100% !important;\' />
                            </div>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Earning Amount</label>
                                <input required type=\'text\' name=\'amt\' class=\'form-control input-md\' id=\'amt\' placeholder=\'Amount this month\' style=\'width: 100%;\'/>
                            </div>
                            '.$payspecific.'
                        </div>
                        <div class=\'form-actions bottom margin-top-20\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button data-style=\'slide-up\' type=\'submit\' class=\'btn blue\'>Save</button>
                        </div>
                    </form>
                 ';
                    $deduction_popovers = '
                    <form id=\'frm_transaction_deduction_entry\' style=\'\' class=\'form-horizontal\' action=\''.base_url().'payroll/addpayrolltransactions\' method=\'post\'>
                        '.$prerequisit_inputs.'
                        <input value=\'2\' type=\'hidden\' name=\'trntype\' class=\'form-control input-md\' id=\'year\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Deduction Type</label>
                                <input required type=\'text\' name=\'type\' class=\'form-control input-md\' id=\'type\' style=\'width: 100% !important;\' />
                            </div>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Deduction Amount</label>
                                <input required type=\'text\' name=\'amt\' class=\'form-control input-md\' id=\'amt\' placeholder=\'Amount this month\' style=\'width: 100%;\'/>
                            </div>
                            '.$payspecific.'
                        </div>
                        <div class=\'form-actions bottom margin-top-20\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button data-style=\'slide-up\' type=\'submit\' class=\'btn blue\'>Save</button>
                        </div>
                    </form>
                 ';

                    if($month == 1){
                        $monthlate = 12;
                        $yearlate = $year - 1;
                    }else{
                        $monthlate = $month - 1;
                        $yearlate = $year;
                    }
                    $gettotallatelastmonth  = $this->db->select("SEC_TO_TIME( SUM( TIME_TO_SEC( `totallate` ) ) ) AS timeSum")
                        ->from("attendance_reports")->where(array("month" => $monthlate , "empid" => $row->sysid , "year" => $yearlate , "paid" => 0 , "status" => 1))->get()->row();

                    $gettotalattendancepay = $this->db->select("SUM(charge) as TOTALATTENDANCECHARGE")->from("attendance_reports")
                        ->where(array("month" => $monthlate , "empid" => $row->sysid , "year" => $yearlate , "paid" => 0 , "status" => 1))->get()->row();



                    $data['month'] =$month;
                    $data['year'] = $year;

                    $status = '';
                    $row_color = ($salary == 0) ? 'danger' : '';
                    $printbtn = '<button id="btn_print_payslip_temp" data-id="'.$empid.'" class="btn btn-default inline btn-xs" type="button"><i class="fa fa-print"></i></button>';
                    $status = ($salary == 0) ? '<i class="fa fa-warning font-yellow-gold"></i>' : '<i class="fa fa-check font-green-haze"></i>';
                    $data['list'][] = array(
                        'expand' => $empid,
                        'num' => $num++,
                        'empid' =>  emp_pic_draw($row->personid, '20px', '20px') . data_empty($empid),
                        'firstname' => strtoupper($row->firstname),
                        'lastname' => strtoupper($row->lastname) . ' ' .gender_icon($row->gender),
                        'middlename' => strtoupper($row->middlename),
                        'department' => $department,
                        'basic' => number_format(0, 2), // GET SALARY
                        'earnings' => row_popover_a('earningpopover', number_format($earnings, 2), $earnings_popovers, 'Earning Manual Entry', 'left'),
                        'premiums' => number_format(0, 2),
                        //      'tax' => ($checkforannualtax)  ? number_format($checkforannualtax->amount , 2) : number_format($tax, 2),
                        'tax' => number_format(0 , 2),
                        'loans' => row_popover_a('loanpopover', number_format(0, 2), 0, 'Loan Manual Entry', 'left'),
                        'deductions' => row_popover_a('deductionpopover', number_format(0, 2), 0, 'Deduction Manual Entry', 'left'),
                        'netpay' => number_format($earnings, 2),
                        //  'lateminutes' => ($gettotallatelastmonth && $gettotallatelastmonth->timeSum != '00:00:00') ? $gettotallatelastmonth-> timeSum : '',
                        //  'latecharge' => ($gettotalattendancepay && $gettotalattendancepay->TOTALATTENDANCECHARGE != '0.00') ? $gettotalattendancepay->TOTALATTENDANCECHARGE : '',
                        'control' => $control,
                        'rowcolor' => $row_color,
                        'status' => ($check_stat!='') ? $check_stat : $status.' '.$printbtn,
                        'payclass' => '<input class="payclass" type="hidden" value="'.$row->payclassid.'" />'
                    );
                    $totalearningssum += $earnings;
                    $totalloanssum += $loans;
                    $totalpremiumssum += $premiums;
                    $totaltaxsum += $tax;
                    $totaldeductionssum += $deduction;
                    $totalnetsum += $netpay;

                    if($process == 1 && $group_id > 0) {

                        if($salary > 0 ) {
                            $insert_arr = array(
                                'empid' => $empid,
                                'groupid' => $group_id,
                                'basic' => 0,
                                'earnings' => $earnings,
                                'deductions' => 0,
                                'ccid' => ($row->ccid>0) ? $row->ccid : 0,
                                'tax' => 0,
                                'net' => $earnings
                            );
                            $this->db->insert('payroll_reports_main', $insert_arr);
                            $data['payrollerr'][] = $this->db->_error_message();
                            $payrollid = $this->db->insert_id();
                            if($compute->transactions) {

                                $payspec = 1;
                                $qry_trns = $this->db->select('paytype, payspec,typesid')->from('payroll_transactions')
                                    ->where(array('empid' => $empid, 'years' => $year, 'months' => $month))
                                    ->get()->row();

                                foreach($compute->transactions as $trn_row) {

                                    // CHECK TRANSACTIONO
                                    $trn_amt = $trn_row['amt'];
                                    $trn_type = $trn_row['type'];

                                    if($qry_trns) {
                                        $payspec = $qry_trns->payspec;
                                    }
                                    //only bunos will be inserted here.

                                    if($trn_type == 264){
                                        $trn_ins_arr = array(
                                            'payrollid' => $payrollid,
                                            'trntype' => $trn_type,
                                            'amt' => $trn_amt,
                                            'payspec' => $paytype
                                        );
                                        $this->db->insert('payroll_reports_trn', $trn_ins_arr);
                                        $data['trnerr'][] = $this->db->_error_message();
                                    }

                                }
                            }

                            $data['process'][] = $insert_arr;

                            //Update all the previous month tardiness  charge
                            if($month == 1){
                                $monthlate = 12;
                                $yearlate = $year - 1;
                            }else{
                                $monthlate = $month - 1;
                                $yearlate = $year;
                            }
                            $updatetardinessarr = array(
                                'paid' => 1
                            );
                            $this->db->where(array("month" => $monthlate , "year" => $yearlate,"payclass" => $payclass , "paid" => 0));
                            $this->db->update("attendance_reports",$updatetardinessarr);

                        }
                    }

                    if($preview == 1) {
                        $trn_arr = array();
                        if($compute->transactions) {

                            $payspec = 1;
                            $qry_trns = $this->db->select('paytype, payspec,typesid')->from('payroll_transactions')
                                ->where(array('empid' => $empid, 'years' => $year, 'months' => $month))
                                ->get()->row();

                            foreach($compute->transactions as $trn_row) {

                                // CHECK TRANSACTIONO
                                $trn_amt = $trn_row['amt'];
                                $trn_type = $trn_row['type'];

                                $trn_arr[] = array(
                                    'payrollid' => $payrollid,
                                    'trntype' => $trn_type,
                                    'amt' => $trn_amt,
                                    'payspec' => $paytype
                                );
                            }
                        }
                        $data['list'][] = array(
                            'empid' => $empid,
                            'groupid' => $group_id,
                            'basic' => ($payclass == 128) ?  ($salary / 2) : $salary,
                            'earnings' => $earnings,
                            'deductions' => 0,
                            'ccid' => ($row->ccid>0) ? $row->ccid : 0,
                            'tax' => 0,
                            'net' => $netpay,
                            'trn' => $trn_arr
                        );
                    }

                }else{

                    $createdby = '<a class="tooltips" href="javascript:;" data-placement="left" title="'.user_info($row->createdby)->lastname . ', ' . user_info($row->createdby)->firstname.'">'.user_info($row->createdby)->username.'</a>';

                    if($row->sysid != 1){
                        if($report > 0){
                            $html .= '<tr>';
                            $html .= '<td>'.$i++.'</td>';
                            $html .= '<td>'.$row->empid.'</td>';
                            $html .= '<td>'.strtoupper($row->lastname).'</td>';
                            $html .= '<td>'.strtoupper($row->firstname).'</td>';
                            $html .= '<td>'.strtoupper($row->middlename).'</td>';
                            $html .= '<td>'.$department.'</td>';
                            $html .= '</tr>';
                        }


                        $data['list'][] = array(
                            'expand' => $row->sysid,
                            //'empid' => emp_pic_draw($row->personid, '20px', '20px') . $row->empid,
                            'empid' =>  ($row->bioid != '') ? emp_pic_draw($row->personid, '20px', '20px').$row->bioid : '',
                            'lastname' => strtoupper($row->lastname) . ' ' . gender_icon($row->gender) ,
                            'firstname' => strtoupper($row->firstname),
                            'middlename' => strtoupper($row->middlename),
                            'depname' => $department,
                            'datecreated' => $row->datecreated,
                            'createdby' => $createdby,
                            'control' => $control
                        );
                    }
                }
            }
        }

        if($report > 0){
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            $data['html'] = $html;
        }

        $data['report'] = $report;
        $data['input'] = $this->input->post();
        $data['moduleid'] = $moduleid;
        $data['parentid'] = $parentid;
        $data['paytype'] = $paytype;
        $data['viewtype'] = $view_type;
        $data['groupid'] = $group_id_ui;
        $data['groupstatus'] = $group_status_ui;
        $data['payrollstat'] = $payrollstat;
        //total summary
        $data['totaleaningssum'] = number_format($totalearningssum , 2);
        $data['totalloanssum'] = number_format($totalloanssum , 2);
        $data['totalpremiumssum'] = number_format($totalpremiumssum ,2);
        $data['totaltaxsum'] = number_format($totaltaxsum ,2);
        $data['totaldeductionssum'] = number_format($totaldeductionssum,2);
        $data['totalnetsums'] = number_format($totalnetsum,2);
        return json_encode($data);
    }


    function upload_employee_pic() {
        $data = array();
        $data['inputs'] = $this->input->post();
        return json_encode($data);
    }

    function get_employee_info() {
        $id = $this->input->post('id');
        $data = array();
        $qry_emp = $this->db->select()->from('prime_employee_main')->where('sysid', $id)->get()->row();

        $data_details = '';
        if ($qry_emp) {
            $person_info = get_person_info($qry_emp->personid);
            $emp_department = get_emp_department($qry_emp->sysid);
            $emp_position = select_emp_position($qry_emp->sysid);
            $emposition = ($emp_position) ? $emp_position->names : '';
            $emp_status = select_emp_jobcat($qry_emp->sysid);
            $emp_class = (select_emp_payclass($qry_emp->sysid)) ? select_emp_payclass($qry_emp->sysid)->names : '';
            if ($person_info && isset($person_info->info)) {
                $info = $person_info->info;
                $status = ($emp_status) ? $emp_status->names : '';
                $func = 'success';
                $data_details .= '<div class="row">';
                $data_details .= '<div class="col-md-1">';
                $data_details .= '
                                            <form id="frm_upload_pic" method="post" action="" enctype="multipart/form-data">
                                                <input name="personid" type="hidden" value="'.$qry_emp->personid.'" />
                                                <div class="fileinput fileinput-new fileinput-custom" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" data-trigger="fileinput">
                                                    <img alt="" class="fileinput-new" src="' . get_owner_pic($qry_emp->personid, 'person') . '">
                                                    <div class="fileinput-preview fileinput-exists thumbnail" >
                                                    </div>
                                                    </div>
                                                        <span class="btn-file">
                                                        <input type="file" id="emppic" name="newpic">
                                                        </span>
                                                        <a id="btn_upload_pic" href="javascript:;" class="btn btn-xs btn-circle blue btn-upload fileinput-exists">
                                                        <i class="fa fa-upload"></i></a>
                                                        <a href="javascript:;" class="btn btn-xs btn-circle btn-remove red fileinput-exists" data-dismiss="fileinput">
                                                        <i class="fa fa-times"></i> </a>
                                                </div>
											</form>
                ';

                $data_details .= '</div>';

                $data_details .= '<div class="col-md-4">';
                $data_details .= '<ul class="list-group summary column no-border">';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Employee Key: </span><span class="col-md-9 text-primary text-bold">' . $qry_emp->sysid . '</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Employee ID: </span><span class="col-md-9 text-primary text-bold">' . $qry_emp->empid . '</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Name: </span><span class="col-md-9 text-primary text-bold">' . $info->lastname . ', ' . $info->firstname . ' ' . $info->middlename . '</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Gender: </span><span class="col-md-9 text-primary text-bold">' . $info->gender . '</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Address: </span><span class="col-md-9 text-primary text-bold">' . $info->addrspec . '</span></li>';

                $age = date_diff(date_create($info->birthdate), date_create('now'))->y;



                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Age: </span><span class="col-md-9 text-primary text-bold">' . $age . '</span></li>';
                $data_details .= '</ul>';
                $data_details .= '</div>';

                $data_details .= '<div class="col-md-3">';
                $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Department: </span></span><span class="col-md-9 text-primary text-bold">'.$emp_department->desc.'</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Position: </span><span class="col-md-9 text-primary text-bold">'.$emposition.'</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Status: </span><span class="col-md-9 text-primary text-bold">'.$status.'</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Class: </span><span class="col-md-9 text-primary text-bold">'.$emp_class.'</span></li>';
                $data_details .= '</ul>';
                $data_details .= '</div>';

                $data_details .= '<div class="col-md-4">';
                $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Company Email: </span></span><span class="col-md-9 text-primary text-bold">'.$info->companyemail.'</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Personal Email: </span><span class="col-md-9 text-primary text-bold">'.$info->personalemail.'</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Birthday: </span><span class="col-md-9 text-primary text-bold">'.$info->birthdate.'</span></li>';
                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-3 bold">Cell #: </span><span class="col-md-9 text-primary text-bold">'.$info->mobilephone.'</span></li>';
                $data_details .= '</ul>';
                $data_details .= '</div>';
                $data_details .= '</div>';
            } else {
                $func = 'warning';
                $data_details .= '<div class="col-md-12"><h4><i class="fa fa-warning"></i> Unable to retrive person\'s data!</h4></div>';
            }
        } else {
            $func = 'warning';
        }

        $data['func'] = $func;
        $data['html'] = $data_details;
        return json_encode($data);
    }

    function check_emp_workshift($empid) {
        $query = $this->db->query('select * from prime_employee_main_workshift_matrix where empid = "' . $empid . '"');
        if ($query->num_rows() > 0) {
            return $query->workshift_id;
        } else {
            return false;
        }
    }

    function emptime_logs_daily($today = false, $qtype = 0, $payclass = 0, $ccid = false) {
        $data = array();

        $logcnt = 0;

        $logtype = '';
        $codes = '';
        $desc = '';
        $status = '';
        $row_complete = false;
        $amin = null;
        $amout = null;
        $pmin = null;
        $pmout = null;
        $irrin = null;
        $irrout = null;
        $irrlate = null;
        $otin = null;
        $otout = null;
        $locatorout = null;
        $locatorin = null;
        $amabsent = false;
        $pmabsent = false;
        $amlate = '00:00:00';
        $pmlate = '00:00:00';
        $today_input = $this->input->post('today');
        $datetime = DateTime::createFromFormat('Y-m-d', $today_input);
        $dayname  =  strtolower($datetime->format('D'));
        $daynum  =  strtolower($datetime->format('N'));
        $todayarr = explode("-", $today_input);
        $daytoday = $todayarr[2];
        $currentmonth = $todayarr[1];
        $currentyear = $todayarr[0];
        if($daytoday > 15){
            $daytype = 2;
        }else{
            $daytype = 1;
        }

        if($today==false) {
            $payclass_input = $this->input->post('payclass');
            $ccid = $this->input->post('ccid');
            if($today_input) {
                if($payclass_input){
                    $payclass = $payclass_input;
                }
                $today = $today_input;
            }else {
                if($payclass_input){
                    $payclass = $payclass_input;
                }
                $today = sql_time()->DATENUM;
            }
        }

        if($payclass>0) {
            $this->db->where('empc.payclass_id', $payclass);
        }
        if($ccid > 0) {
            $this->db->where('ec.ccid', $ccid);
        }
        $query = $this->db->select('emp.sysid,emp.type,emp.empid, bio.bioid,  ec.ccid')
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS empname", false)
            ->from('prime_employee_main AS emp')
            ->join('prime_employee_main_payclass AS empc', 'empc.emp_id = emp.sysid')
            ->join('prime_employee_bioid AS bio', 'bio.empid = emp.sysid')
            ->join('prime_employee_main_workshift_matrix AS mwm', 'mwm.empid = emp.sysid AND mwm.status = 1', 'left')
            ->join('prime_employee_main_workshift AS mw', 'mw.sysid = mwm.workshift_id ', 'left')
            ->join('person AS p', 'p.sysid = emp.personid')
            ->join('prime_employee_costcenter AS ec', 'ec.empid = emp.sysid AND ec.status = 1', 'left')
            //   ->join("prime_employee_salary as pes","pes.empid = emp.sysid","left")
            // ->join("payroll_emplist as pe","pe.empid = emp.sysid","left")
            ->where(array('emp.status' => 1 , "mwm.status" => 1 , "ec.type" => 1 ,"bio.status" => 1 ))
            ->group_by('emp.sysid,emp.type,emp.empid, bio.bioid,  ec.ccid')
            ->order_by('p.lastname','asc')
            ->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            $num = 1;

            foreach ($query->result() as $row) {
                $logcnttotal = 0;
                $time_logs_arr = array();
                $qry_timelogs = $this->db->select()
                    ->from('prime_employee_attendance_timelogs')
                    ->where(array('logdate' => $today, 'bioid' => $row->bioid))
                    ->order_by("logtime" , "ASC")
                    ->get();
                $timelog_num = $qry_timelogs->num_rows();
                $logcnttotal = $qry_timelogs->num_rows();
                $time_logs_details = '';
                if ($timelog_num > 0) {

                    $amtime = array();
                    $pmtime = array();

                    $year = $currentyear;
                    $month = $currentmonth;
                    $day = $daytoday;

                    $specifiedTime = checkempsched($row->sysid, $day, $month , $year , 301)->sepcfiedTime;
                    $specifiedTime2 = checkempsched($row->sysid, $day, $month , $year , 301)->sepcfiedTime2;
                    $codes = checkempsched($row->sysid, $day, $month , $year , 301)->codes;
                    $logcnt = checkempsched($row->sysid, $day, $month , $year , 301)->logcount;
                    $desc = checkempsched($row->sysid, $day, $month , $year , 301)->desc;
                    $daytype = checkempsched($row->sysid, $day, $month , $year , 301)->daytype;


                    foreach ($qry_timelogs->result() as $trow) {

                        $time_logs_arr[] = $trow->logtime;
                        $time_logs_details .= '<li>'.$trow->logtime.'</li>';
                        $timearr = substr($trow->logtime , 0 , 2);

                        //ALL LOGS
                        if($timearr < 11){
                            $amtime[] = $trow->logtime;
                        }else{
                            $pmtime[] = $trow->logtime;
                        }
                        $amcount = count($amtime);
                        $pmcount = count($pmtime);
                        if($logcnt == 4 && $daytype == 1){
                            $amin = isset($amtime[0]) ? $amtime[0] : '00:00:00';
                            $amout = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';
                            $pmin = isset($pmtime[1]) ? $pmtime[1] : '00:00:00';

                            $lastlog = substr(isset($pmtime[2]) ? $pmtime[2] : '00:00:00' , 0 , 2);
                            if($lastlog > 16){
                                $pmout = isset($pmtime[2]) ? $pmtime[2] : '00:00:00';
                            }else{
                                $pmout = '00:00:00';
                            }



                            $irrin = '';
                            $irrout = '';
                        }
                        if($logcnt == 2 && $daytype == 1){
                            if($amcount > 0){
                                $amin = isset($amtime[0]) ? $amtime[0] : '00:00:00';
                                $amout = '00:00:00';
                                $pmin =  '00:00:00';
                                $pmout = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';
                            }else{
                                $amin =  '00:00:00';
                                $amout = '00:00:00';
                                $pmin = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';
                                $pmout = isset($pmtime[1]) ? $pmtime[1] : '00:00:00';
                            }


                            $irrin = '';
                            $irrout = '';
                        }
                        if($logcnt == 2 && $daytype == 2){
                            $amin = '';
                            $amout = '';
                            $pmin = '';
                            $pmout = '';


                            $forwarddate = date('Y-m-d H:i:s', strtotime($today_input . ' +1 day'));

                            $irrin = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';

                            $getirrout = $this->db->select("logtime")
                                ->from('prime_employee_attendance_timelogs')
                                ->where(array('logdate' => $forwarddate, 'bioid' => $row->bioid))
                                ->order_by("logtime" , "ASC")
                                ->limit(1)
                                ->get()->row();

                            $irrout = ($getirrout) ? $getirrout->logtime : '00:00:00';


                        }
                        if($logcnt == 2 && $daytype == 3){
                            $amin = '';
                            $amout = '';
                            $pmin = '';
                            $pmout = '';
                            $irrin = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';

                            $getirrout = $this->db->select("logtime")
                                ->from('prime_employee_attendance_timelogs')
                                ->where(array('logdate' => $today_input, 'bioid' => $row->bioid))
                                ->order_by("logtime" , "DESC")
                                ->limit(1)
                                ->get()->row();
                            if($getirrout){
                                $irrout = $getirrout->logtime;
                            }else{
                                $forwarddate = date('Y-m-d H:i:s', strtotime($today_input . ' +1 day'));
                                $getirrout = $this->db->select("logtime")
                                    ->from('prime_employee_attendance_timelogs')
                                    ->where(array('logdate' => $forwarddate, 'bioid' => $row->bioid))
                                    ->order_by("logtime" , "ASC")
                                    ->limit(1)
                                    ->get()->row();
                                if($getirrout){
                                    $irrout = $getirrout->logtime;
                                }
                            }


                        }
                    }


                    //for under time
                    $amoutspecifiedtime1 =  checkempsched($row->sysid, $day, $month , $year , 301)->undertimeamout;
                    $pmoutspecifiedtime2 =  checkempsched($row->sysid, $day, $month , $year , 301)->undertimepmout;

                    if($amout >= $amoutspecifiedtime1){
                        $amundertime = '00:00:00';
                    }else{
                        $amundertimemin  = ((strtotime($amoutspecifiedtime1) - strtotime($amout)+ 86400) % 86400) / 60;
                        $amundertime = convertminutetotimeformat($amundertimemin); // this is in time format already.
                    }

                    if($pmout >= $pmoutspecifiedtime2){
                        $pmundertime= '00:00:00';
                    }else{
                        $pmundertimemin  = ((strtotime($pmoutspecifiedtime2) - strtotime($pmout)+ 86400) % 86400) / 60;
                        $pmundertime = convertminutetotimeformat($pmundertimemin); // this is in time format already.
                    }

                    $timeDifference = (strtotime($amin) - strtotime($specifiedTime) + 86400) % 86400;
                    $timeDifference2 = (strtotime($pmin) - strtotime($specifiedTime2) + 86400) % 86400;

                    //AM LATE
                    if ($timeDifference >= 360) {
                        // proceed if less than 15 minutes has elapsed since specifiedTime
                        $diff = strtotime($amin) - strtotime($specifiedTime);

                        $hours = floor($diff / 3600);
                        $mins = floor($diff / 60 % 60);
                        $secs = floor($diff % 60);
                        $hrlate = $hours . ':' . $mins . ':' . $secs;
                        if ($hours >= 0) {
                            $amlate = $hrlate;
                        } else {
                            $amlate = '00:00:00';
                        }
                    }
                    //PM LATE
                    if ($timeDifference2 >= 360) {
                        // proceed if less than 15 minutes has elapsed since specifiedTime
                        $diff = strtotime($pmin) - strtotime($specifiedTime2);
                        $hours = floor($diff / 3600);
                        $mins = floor($diff / 60 % 60);
                        $secs = floor($diff % 60);
                        $hrlate = $hours . ':' . $mins . ':' . $secs;
                        if ($hours >= 0) {
                            $pmlate = $hrlate;
                        } else {
                            $pmlate = '0:00:00';
                        }
                    }

                    if($amabsent){
                        $amlate = '00:00:00';
                    }
                    if($pmabsent){
                        $pmlate = '00:00:00';
                    }

                    $info_popover = '<a class="btn inline btn-success btn-xs " title="" data-toggle="popover" 
                                        data-content="<ol style=\'width: 200px;\'>'.$time_logs_details.'</ol>"
                                        data-placement="left" data-trigger="hover" data-original-title="Time Logs"> 
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                     </a>';
                    //get totallate
                    $latetotal = sum_the_time($amlate, $pmlate);
                    $control = '';
                    $control .= '<a href="#form_edit_attendance" id="editattlogsbtn" data-toggle="ajax-modal" data-arr="'.$today.'" data-view="'.$row->bioid.'" class="btn inline btn-warning btn-xs "><i class="fa fa-pencil"></i></a>';
                    $control .= $info_popover.$logcnttotal;
                    $control .= '<a class="btn inline btn-primary btn-xs " target="_blank" href="'.base_url('module/f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59/view/'.$row->sysid).'"><i class="fa fa-search"></i></a>';

                    $status .= ($row_complete) ? get_types_label_format(308) : get_types_label_format(309);

                    $amlate = ($amlate == '00:00:00' || $amlate  == '0:00:00')? $amlate : '<span class="text-danger">'.$amlate.'</span>';
                    $pmlate = ($pmlate == '00:00:00'|| $pmlate  == '0:00:00')? $pmlate : '<span class="text-danger">'.$pmlate.'</span>';

                    if(strtotime($latetotal)  > 0){
                        $rowclass = 'warning';
                    }else{
                        $rowclass = '';
                    }
                    if($daytype == 2 && $logcnttotal == 1 || $daytype == 3 && $logcnttotal == 1){

                    }else{
                        $data['data'][] = array(
                            'expand' => btn_expand($row->sysid),
                            'empid' => '<span data-placement="right" title="'.$desc.'" class="label label-success tooltips">'.$codes.'</span>'.$daytype,
                            'empname' => $row->empname,
                            'time' => $row->bioid,
                            'logcnt' => $logcnt,
                            'amin' =>  $amin,
                            'amout' => $amout,
                            'amlate' => $amlate,
                            'pmin' => $pmin,
                            'pmout' => $pmout,
                            'pmlate' => $pmlate,
                            'irrin' => $irrin,
                            'irrout' => $irrout,
                            'irrlate' => $irrlate,
                            'otin' => $otin,
                            'otout' => $otout,
                            'locatorout' => $locatorout,
                            'locatorin' => $locatorin,
                            'complete' => '',
                            'lateam' => '',
                            'latepm' => '',
                            'totalot' => '',
                            'totallocator' => '',
                            'latetotal' =>  ($latetotal == '00:00:00' || $latetotal  == '0:00:00')? $latetotal : '<span class="text-danger">'.$latetotal.'</span>',
                            'status' => '',
                            'control' => $control,
                            'rowclass' => $rowclass
                        );
                    }

                    $amlate = '00:00:00';
                    $pmlate = '00:00:00';

                }
            }
        }

        $data['date'] = $today;
        return json_encode($data);
    }

    function get_employee_teammembers() {
        $data = array();
        $html = '';
        $teamid = $this->input->post('teamid');
        $query = $this->db->select('emp.empid')
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS empname", false)
            ->from('prime_employee_main AS emp')
            ->join('person AS p', 'p.sysid = emp.personid')
            ->join('prime_employee_team_assignments AS t', 't.empid = emp.sysid')
            ->where(array('t.teamid' => $teamid))
            ->group_by('emp.empid')
            ->get();
        $html .= "<div  style='width: 250px'>";
        $html .= "<ul class='list-group summary column no-border list-group-xs'>";
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $html .= "<li class='list-group-item'>";
                $html .= "<span class='label-name col-md-3'>{$row->empid}</span>";
                $html .= "<span class='label-default col-md-6'>{$row->empname}</span>";
                $html .= "</li>";
            }
        }
        $html .= "</ul>";
        $html .= "</div>";
        $data['html'] = $html;
        return json_encode($data);
    }

    function emptime_logs_range() {
        $data = array();
        $today = sql_time()->DATENUM;
        // $today = '2017-07-12';
        $date_from = $this->input->post("from");
        $date_end = $this->input->post("end");

        $date_from = '2018-01-01';
        $date_end = '2018-01-31';


        $query = $this->db->select('emp.empid, bio.bioid')
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS empname", false)
            ->from('prime_employee_main AS emp')
            ->join('prime_employee_bioid AS bio', 'bio.empid = emp.sysid')
            ->join('person AS p', 'p.sysid = emp.personid')
            ->where(array('emp.status' => 1))
            ->group_by('emp.empid')
            ->get();

        $num_rows = $query->num_rows();

        if ($num_rows > 0) {
            foreach ($query->result() as $row) {

                $time_logs_arr = array();

                $qry_timelogs = $this->db->select()
                    ->from('prime_employee_attendance_timelogs')
                    //->where(array('logdate >= ' => $date_from, 'logdate <= ' => $date_end, 'bioid' => $row->bioid))->get();
                    ->where(array('logdate ' => $today, 'bioid' => $row->bioid))->get();
                $timelog_num = $qry_timelogs->num_rows();
                if ($timelog_num > 0) {
                    foreach ($qry_timelogs->result() as $trow) {
                        $time_logs_arr[] = $trow->logtime;
                    }


                    $amin = ($timelog_num > 0) ? $time_logs_arr[0] : '';
                    $amout = ($timelog_num > 1) ? $time_logs_arr[1] : '';
                    $pmin = ($timelog_num > 2) ? $time_logs_arr[2] : '';
                    $pmout = ($timelog_num > 3) ? $time_logs_arr[3] : '';

                    $locator = false;
                    // GET QUERY LOCATOR REQUEST HERE
                    // IF LOCATOR REQUEST TRANSACTION IS TRUE : fetch time of locators array (0, 1) in out.
                    if ($locator) {
                        $locatorin = '2:00'; // STATIC
                        $locatorout = '1:00'; // STATIC
                    } else {
                        $locatorin = '';
                        $locatorout = '';
                    }
                    // END OF LOCATOR QUERY

                    $ot = false;
                    // GET QUERY OVER TIME REQUEST HERE
                    // IF OVER TIME REQUEST IS TRUE : fetch time of locators array (0, 1) in out.
                    if ($ot) {
                        $otin = '6:00'; // STATIC
                        $otout = '8:00'; // STATIC
                    } else {
                        $otin = '';
                        $otout = '';
                    }

                    $row_complete = ($timelog_num >= 4) ? true : false;
                    $row_late = false;

                    $specifiedTime = '08:00';
                    $timeDifference = (strtotime($amin) - strtotime($specifiedTime) + 86400) % 86400;

                    if ($timeDifference >= 360) {
                        // proceed if less than 15 minutes has elapsed since specifiedTime
                        $diff = strtotime($amin) - strtotime($specifiedTime);
                        $hours = floor($diff / 3600);
                        $mins = floor($diff / 60 % 60);
                        $secs = floor($diff % 60);
                        $hrlate = $hours . ':' . $mins . ':' . $secs;
                        if ($hours >= 0) {
                            $amlate = $hrlate;
                            $row_late = true;
                        } else {
                            $amlate = '';
                            $row_late = false;
                        }
                    }

                    $data['data'][] = array(
                        'empid' => $row->empid,
                        'empname' => $row->empname,
                        'time' => $row->bioid,
                        'amin' => $amin,
                        'amout' => $amout,
                        'amlate' => $amlate,
                        'pmin' => $pmin,
                        'pmout' => $pmout,
                        'pmlate' => '',
                        'otin' => $otin,
                        'otout' => $otout,
                        'locatorout' => $locatorout,
                        'locatorin' => $locatorin,
                        'complete' => $row_complete,
                        'late' => $row_late
                    );
                }
            }
        }
        return $data;
    }

    // create function to check if there is employee schedule
    function check_emp_schedule($empid, $date) {
        $query = $this->db->query('select * from prime_employee_main_schedule_matrix where empid = "' . $empid . '" and "' . $date . '" between schedstart and schedend ');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function emptime_logs() {

        $datestart = $this->input->post('datestart');
        $dateend = $this->input->post('dateend');
        $amabsent = false;
        $pmabsent = false;

        $empid = $this->input->post('empid');

        $begin = new DateTime($datestart);
        $end = new DateTime($dateend);
        $end = $end->modify('+1 day');
        $interval_num = $begin->diff($end);
        $num_days = $interval_num->days;

        //GET BIO ID
        $getbioid = $this->db->select("bioid")->from("prime_employee_bioid")
            ->where(array("empid" => $empid , "status" => 1))->get()->row();
        $bioid = ($getbioid) ? $getbioid->bioid : '';
        //GET LOG COUNT
        $getlogcnt = $this->db->select("pemw.logcnt , pemw.logtype")->from("prime_employee_main_workshift_matrix as pemwm")
            ->join("prime_employee_main_workshift as pemw","pemw.sysid = pemwm.workshift_id" , "left")
            ->where(array("pemwm.empid" => $empid , "pemwm.status" => 1))
            ->get()->row();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        // VARS
        $all_total_late = 0;
        $overalltotallate = 0;
        foreach ($period as $dt) {


            $amin = '';
            $amout = '';
            $pmin = '';
            $pmout = '';

            $hourslatee = '00:00:00';
            $minuteslate = '00:00:00';
            $secondslate = '00:00:00';

            $amlate = '';
            $pmlate = '';


            //CHECK IF WEEK END
            $date = $dt->format("Y-m-d");

            $this_date = date("l", strtotime($date));
            if ($this_date == "Saturday" || $this_date == "Sunday") {
                $weekend = true;
            } else {
                $weekend = false;
            }

            $time_logs_arr = array();
            $qry_timelogs = $this->db->select()
                ->from('prime_employee_attendance_timelogs')
                ->where(array('logdate' => $date, 'bioid' => $bioid))->get();
            $logsrows = $qry_timelogs->num_rows();

            $data['datelist'][] = array(
                'date' => $date,
                'numrows' => $logsrows
            );
            $checkforleave = $this->db->query("SELECT telr.sysid FROM trn_employee_leave_requests as telr
LEFT JOIN trn_employee_leave_requests_approval as telra ON telr.groupid = telra.sysid
WHERE telr.status = 301 AND telra.status = 301 AND empid = ".$empid." AND ('".$date."' BETWEEN telr.`from` AND telr.`to` OR '".$date."' BETWEEN telr.`from` AND telr.`to`)")->row();
            if($weekend == true){
                $status = '';
            }else{
                if(($getlogcnt) && $getlogcnt->logcnt == 4){
                    if($checkforleave){
                        $status = '<button type="button" class="btn-xs btn btn-info">On-Leave</button>';
                    }else{
                        if($logsrows == 0){
                            $status = '<button type="button" class="btn-xs btn btn-danger">Absent</button>';
                        }else{
                            if($logsrows >= 4){
                                $status = '<button type="button" class="btn-xs btn btn-success">Complete</button>';
                            }else{
                                $status = '<button type="button" class="btn-xs btn btn-danger">Incomplete</button>';
                            }
                        }

                    }
                }else{
                    if($checkforleave){
                        $status = '<button type="button" class="btn-xs btn btn-info">On-Leave</button>';
                    }else{
                        if($logsrows == 0){
                            $status = '<button type="button" class="btn-xs btn btn-danger">Absent</button>';
                        }else{
                            if($logsrows >= 2){
                                $status = '<button type="button" class="btn-xs btn btn-success">Complete</button>';
                            }else{
                                $status = '<button type="button" class="btn-xs btn btn-danger">Incomplete</button>';
                            }
                        }

                    }
                }
            }

            $amtime  = array();
            $pmtime  = array();

            foreach ($qry_timelogs->result() as $trow) {

                $time_logs_arr[] = $trow->logtime;
                $timearr = substr($trow->logtime , 0 , 2);
                if($getlogcnt){
                    if($getlogcnt->logcnt == 4 && $getlogcnt->logtype == 4){

                        //ALL LOGS
                        if($timearr < 11){
                            $amtime[] = $trow->logtime;
                        }else{
                            $pmtime[] = $trow->logtime;
                        }
                        $amcount = count($amtime);
                        $pmcount = count($pmtime);

                        //AM
                        if($amcount == 0){
                            $amin = '';
                            $amout = '';
                            $amabsent = true;
                        }else{
                            $amabsent = false;
                            $amin = isset($amtime[0]) ? $amtime[0] : '00:00:00';
                            $amout = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';
                        }
                        //PM
                        if($pmcount == 1){
                            $pmin = '';
                            $pmout = '';
                            $pmabsent = true;
                        }else{
                            $pmabsent = false;
                            if($amabsent == true){
                                $pmin = isset($pmtime[0]) ? $pmtime[0] : '';
                            }else{
                                $pmin = isset($pmtime[1]) ? $pmtime[1] : '';
                            }

                            $lastlog =max($time_logs_arr);
                            $lastlogfirst = substr($lastlog , 0 , 2);

                            if($pmcount == 1 && $lastlogfirst > 16){
                                $pmin = '';
                                $pmout = $lastlog;
                            }else{
                                if($lastlogfirst > 15){
                                    $pmout = isset($lastlog) ? $lastlog : '';
                                }else{
                                    $pmout = '';
                                }
                            }
                        }

                    }else if($getlogcnt->logcnt == 2) {
                        $amin = '00:00:00';
                        $amout = '00:00:00';
                        $amlate = '00:00:00';
                        $pmin = '00:00:00';
                        $pmout = '00:00:00';
                        $pmlate = '00:00:00';
                        if($getlogcnt->logtype == 0){
                            //REGULAR OFFICE HOUR
                            $amin  = $time_logs_arr[0];
                            $amout = '/';
                            $pmin = '/';
                            if(count($time_logs_arr) > 1){
                                $pmout = get_array_last($time_logs_arr);
                            }else{
                                $pmout = '';
                            }

                        }else if($getlogcnt->logtype == 1){
                            $amin  = $time_logs_arr[0];
                            $amout = '/';
                            $pmin = '/';
                            if(count($time_logs_arr) > 1){
                                $pmout = get_array_last($time_logs_arr);
                            }else{
                                $pmout = '';
                            }

                        }else if($getlogcnt->logtype == 2){
                            $amin  = $time_logs_arr[0];

                            $aminexplode = explode(':' , $amin);
                            if($aminexplode[0] > 13){
                                $amin  = $time_logs_arr[0];
                            }else{
                                $amin = '';
                            }

                            $amout = '/';
                            $pmin = '/';

                            $data_arr = explode('-', $date);
                            $yearindex = $data_arr[0];
                            $monthindex = $data_arr[1];
                            $dayindex = $data_arr[2] + 1;
                            $newdate = $yearindex.'-'.$monthindex.'-'.$dayindex;

                            $getpmoutonnewdate = $this->db->select("logtime")
                                ->from("prime_employee_attendance_timelogs")
                                ->where(array("logdate" => $newdate,"bioid"=>$bioid))->order_by("sysid")->limit(1)->get()->row();
                            if($getpmoutonnewdate){
                                $newpmout = $getpmoutonnewdate->logtime;

                                $timeexplode = explode(':', $newpmout);
                                if($timeexplode[0] < 13){
                                    $pmout = $newpmout.'*';
                                }else{
                                    $pmout = '';
                                }
                            }else{
                                $pmout = '';
                            }

                        }else if($getlogcnt->logtype == 3){
                            $amin  = $time_logs_arr[0];
                            $amout = '/';
                            $pmin = '/';
                            //+ 1 date
                            $data_arr = explode('-', $date);
                            $yearindex = $data_arr[0];
                            $monthindex = $data_arr[1];
                            $dayindex = $data_arr[2] + 1;
                            $newdate = $yearindex.'-'.$monthindex.'-'.$dayindex;

                            $getpmoutonnewdate = $this->db->select("logtime")
                                ->from("prime_employee_attendance_timelogs")
                                ->where(array("logdate" => $newdate,"bioid"=>$bioid))->order_by("sysid")->limit(1)->get()->row();
                            if($getpmoutonnewdate){
                                $newpmout = $getpmoutonnewdate->logtime;

                                $timeexplode = explode(':', $newpmout);
                                if($timeexplode[0] < 18){
                                    $pmout = $newpmout.'*';
                                }else{
                                    $pmout = '';
                                }
                            }else{
                                $pmout = '';
                            }

                        }

                    }
                }
            }


            $specifiedTime = checkempsched($empid, $dt->format('d'), $dt->format('m') , $dt->format('Y') , 301)->sepcfiedTime;
            $specifiedTime2 = checkempsched($empid, $dt->format('d'), $dt->format('m') , $dt->format('Y') , 301)->sepcfiedTime2;

            $timeDifference = (strtotime($amin) - strtotime($specifiedTime) + 86400) % 86400;
            $timeDifference2 = (strtotime($pmin) - strtotime($specifiedTime2) + 86400) % 86400;


            // $amoutspecifiedtime1 =  checkempsched($empid, $dt->format('d'), $dt->format('m') , $dt->format('Y') , 301)->undertimeamout;
            // $pmoutspecifiedtime2 =  checkempsched($empid, $dt->format('d'), $dt->format('m') , $dt->format('Y') , 301)->undertimepmout;

            if ($timeDifference >= 360) {
                // proceed if less than 15 minutes has elapsed since specifiedTime
                $diff = strtotime($amin) - strtotime($specifiedTime);
                $hours = floor($diff / 3600);
                $mins = floor($diff / 60 % 60);
                $secs = floor($diff % 60);
                $hrlate = $hours . ':' . $mins . ':' . $secs;
                if ($hours >= 0) {
                    $amlate = $hrlate;
                } else {
                    $amlate = '00:00:00';
                }
            }

            if ($timeDifference2 >= 360) {
                // proceed if less than 15 minutes has elapsed since specifiedTime
                $diff = strtotime($pmin) - strtotime($specifiedTime2);
                $hours = floor($diff / 3600);
                $mins = floor($diff / 60 % 60);
                $secs = floor($diff % 60);
                $hrlate = $hours . ':' . $mins . ':' . $secs;
                if ($hours >= 0) {
                    $pmlate = $hrlate;
                } else {
                    $pmlate = '0:00:00';
                }
            }

            if($amabsent){
                $amlate = '00:00:00';
            }
            if($pmabsent){
                $pmlate = '00:00:00';
            }

            $totallate = sum_the_time($amlate , $pmlate);

            sscanf($totallate, "%d:%d:%d", $hourslatee, $minuteslate, $secondslate);
            $time_seconds  = isset($hourslatee) ? (double)$hourslatee * 3600 + (double)$minuteslate * 60 + (double)$secondslate : (double)$minuteslate * 60 + (double)$secondslate;
            $overalltotallate = $overalltotallate +  $time_seconds;

            $data['lateslist'][] = array(
                'date' => $dt->format("d-m-y D"),
                'amlate' => $amlate,
                'pmlate' => $pmlate,
                'totallate' => $totallate,
                'seconds' => $time_seconds
            );


            if($amlate == '0:00:00' || $amlate == '00:00:00'){
                $amlate ='';
            }
            if($pmlate == '0:00:00' || $pmlate == '00:00:00'){
                $pmlate = '';
            }
            if($totallate == '0:00:00' || $totallate == '00:00:00'){
                $totallate = '';

            }

            $data['aaData'][] = array(
                'day' => '<b>' . $dt->format("d-m-y D") . '</b>',
                'amin' => $amin,
                'amout' => $amout,
                'amlate' => $amlate,
                'pmin' => $pmin,
                'pmout' => $pmout,
                'pmlate' =>  $pmlate,
                'otin' =>  '',
                'otout' =>  '',
                'locin' =>  '',
                'locout' =>  '',
                'weekend' => $weekend,
                'stat' => $status,
                'totallate' =>$totallate,
            );

        }
        $data['totalseconds'] = $overalltotallate;

        $latehours = floor($overalltotallate / 3600);
        $lateminutes = floor($overalltotallate / 60 % 60);
        $lateseconds = floor($overalltotallate % 60);

        $timeFormat = sprintf('%02d:%02d:%02d', $latehours, $lateminutes, $lateseconds);




        $data['overalllate'] = $timeFormat;
        $data['totalot'] ='00:00:00';
        $data['daterange'] = date('Y').'-'.date('m').'-'.'1 - '.date('Y').'-'.date('m').'-'.$num_days;
        $data['input'] = $this->input->post();
        $data['interval'] = $interval_num;
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_days;
        $data['iTotalDisplayRecords'] = $num_days;
        return json_encode($data);
    }


    function get_employee_premiums_paid() {
        /* $id = $this->input->post('id');
         $qry = $this->db->select()->from('trn_payroll_deductions')
             ->where(array('deductid' => $id, 'year' => date('Y'), 'status' => 1))->get();
         $num_rows = $qry->num_rows();
         $qry_res = $qry->result();
         $i = 1;

         // GET CONTRIBUTION STAT CURRENT //
         $qry_premiums = $this->db->select('amt, value')->from('deductions')->where('deductid', $id)->get();
         $premiums_arr = array();
         if ($qry_premiums->num_rows() > 0) {
             foreach ($qry_premiums->result() as $row1) {
                 $premiums_arr[] = array('val' => $row1->amt, 'amt' => $row1->value);
             }
             $empcontr = getequivalent(10000, $premiums_arr);
         } else {
             $empcontr = 0;
         }


         foreach ($qry_res as $row) {
             $data['data'][] = array(
                 'expand' => btn_expand($row->sysid),
                 'num' => $row->year . '-' . $row->month,
                 'amtrange' => number_format($row->basic, 2),
                 'value' => number_format($row->empamt, 2),
                 'comp' => number_format($row->compamt, 2),
                 'total' => number_format(bcadd($row->empamt, $row->compamt, 2), 2),
             );
         }
         $data['empcont'] = $empcontr;
         $data['sEcho'] = 0;
         $data['iTotalRecords'] = $num_rows;
         $data['iTotalDisplayRecords'] = $num_rows;
         return json_encode($data); */
    }

    function get_employee_premiums() {
        $id = $this->input->post('id');
        $basic = 10000;
        $empid = 1;
        $qry = $this->db->select("d.sysid, d.amt, d.value, d.deductid, dc.value AS comp")
            ->from('deductions AS d')
            ->join('deductions_contribution AS dc', 'dc.dedectitemid = d.sysid', 'left')
            ->where(array('d.deductid' => $id, 'd.year' => date('Y'), 'd.status' => 1))->get();
        $num_rows = $qry->num_rows();
        $qry_res = $qry->result();
        $i = 1;
        foreach ($qry_res as $row) {
            if ($row->deductid == 2) {
                if ($basic >= 5000) {
                    $data['data'][] = array(
                        'expand' => btn_expand($row->sysid),
                        'num' => $i++,
                        'amtrange' => number_format($basic, 2),
                        'value' => $row->value,
                        'comp' => $row->comp,
                        'total' => bcadd($row->value, $row->comp, 2),
                    );
                } else {
                    $data['data'][] = array(
                        'expand' => btn_expand($row->sysid),
                        'num' => $i++,
                        'amtrange' => number_format($basic, 2),
                        'value' => bcmul($basic, 0.02, 2),
                        'comp' => bcmul($basic, 0.02, 2),
                        'total' => bcadd(bcmul($basic, 0.02, 2), bcmul($basic, 0.02, 2), 2),
                    );
                }
            } else {
                $data['data'][] = array(
                    'expand' => btn_expand($row->sysid),
                    'num' => $i++,
                    'amtrange' => number_format($row->amt, 2),
                    'value' => $row->value,
                    'comp' => $row->comp,
                    'total' => bcadd($row->value, $row->comp, 2),
                );
            }
        }
        //$data['qry'] = ($num_rows > 0) ? true : false;
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_rows;
        $data['iTotalDisplayRecords'] = $num_rows;
        return json_encode($data);
    }

    function get_employee_address() {

        $id = $this->input->post('id');

        $qry = $this->db->select("d.sysid, d.amt, d.value, d.deductid, dc.value AS comp")
            ->from('deductions AS d')
            ->join('deductions_contribution AS dc', 'dc.dedectitemid = d.sysid', 'left')
            ->where(array('d.deductid' => $id, 'd.year' => date('Y'), 'd.status' => 1))->get();
        $num_rows = $qry->num_rows();
        $qry_res = $qry->result();
    }

    function get_employee_position() {

        $id = $this->input->post('id');

        $qry = $this->db->select("param.names")
            ->from('prime_employee_main as e')
            ->join('person as p', 'p.sysid = e.personid', 'left')
            ->join('prime_employee_main_positions as pos', 'e.sysid = pos.emp_id', 'left')
            ->join('prime_types_parameter as param', 'param.sysid = pos.position_id', 'left')
            ->where('e.sysid', $id)->get();
        $num_rows = $qry->num_rows();
        $qry_res = $qry->result();
    }

    function get_employee_department() {

        $id = $this->input->post('id');

        $qry = $this->db->select("cmain.desc")
            ->from('prime_employee_main as e')
            ->join('person as p', 'p.sysid = e.personid', 'left')
            ->join('prime_employee_costcenter as cost', 'cost.empid = e.sysid', 'left')
            ->join('prime_costcenter_main as cmain', 'cmain.sysid = cost.ccid', 'left')
            ->where('e.sysid', $id)->get();
        $num_rows = $qry->num_rows();
        $qry_res = $qry->result();
    }

    function get_employee_payclass() {

        $id = $this->input->post('id');

        $qry = $this->db->select("cmain.desc")
            ->from('prime_employee_main as e')
            ->join('person as p', 'p.sysid = e.personid', 'left')
            ->join('prime_employee_main_payclass as payclass', 'e.sysid = payclass.emp_id', 'left')
            ->join('prime_types_parameter as param', 'param.sysid	= payclass.payclass_id', 'left')
            ->where('e.sysid', $id)->get();
        $num_rows = $qry->num_rows();
        $qry_res = $qry->result();
    }

    // add workshift update model
    function insert_workshift_model($data) {
        return $this->db->insert('prime_employee_main_workshift_matrix', $data);
    }

    // add checker if existing workshift
    // add insert biometrics
    function insert_biometrics_model($data) {
        return $this->db->insert('prime_employee_bioid', $data);
    }

    // end insert biometrics
    function check_data_workshift($empid) {
        $this->db->select('*');
        $this->db->from('prime_employee_main_workshift_matrix');
        $this->db->where('empid', $empid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // update existing workshift
    // function to check biometrics

    function check_data_biometrics($empid) {
        $this->db->select('*');
        $this->db->from('prime_employee_bioid');
        $this->db->where('empid', $empid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    // end function for checking biometrics
    function update_workshift_model($empid, $workshiftid) {

        $data = array(
            'empid' => $empid,
            'workshift_id' => $workshiftid
        );
        $this->db->where('empid', $empid);
        $this->db->update('prime_employee_main_workshift_matrix', $data);
    }
    function update_biometrics_model($empid, $biometricsid) {

        $data = array(
            'empid' => $empid,
            'bioid' => $biometricsid
        );
        $this->db->where('empid', $empid);
        $this->db->update('prime_employee_bioid', $data);
    }

    function check_data_schedule($empid, $schedstart, $schedend) {
        //    $query =  $this->db->query('select * from prime_employee_main_schedule_matrix where empid = '.$empid.' and '.$schedstart.'between(schedstart, schedend)');

        $query = $this->db->query('select * from prime_employee_main_schedule_matrix where empid = ' . $empid . ' and (schedstart between "' . $schedstart . '" and "' . $schedend . '" or schedend between "' . $schedstart . '" and "' . $schedend . '" )');


        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    function employee_leave_credits() {
        $data = array();
        $html = '';

        $uemp_info = get_user_employee_info();
        if($uemp_info || user_id() == 1) {

            $empid = ($uemp_info) ? $uemp_info->sysid : ((user_id() == 1) ? 159 : 0);
            $year_input = $this->input->post('year');

            $year = ($year_input && $year_input > 0) ? $year_input : date('Y');

            $qry = $this->db->query("SELECT DISTINCT tp.sysid, tp.names, tp.desc,lc.types, SUM(lc.credit) AS totalcredit
            FROM prime_employee_main_leave_credits AS lc
            LEFT JOIN prime_types_parameter AS tp ON tp.sysid = lc.types
            WHERE lc.empid = $empid  AND lc.year = $year AND lc.status = 1
            GROUP BY tp.sysid, tp.names, tp.desc,lc.types
            ");


            if ($qry->num_rows() > 0) {

                $html .= '<li class="list-group-item list-group-item-info">';
                $html .= '<span class="col-md-2 label-default " >Type</span>';
                $html .= '<span class="col-md-2 label-default number">Credit</span>';
                $html .= '<span class="col-md-4 label-default number">Spent</span>';
                $html .= '<span class="col-md-4 label-default number">Balance</span>';
                $html .= '</li>';
                $num = 0;

                foreach ($qry->result() as $row) {

                    $getleavedetails = $this->db->select("SUM(totalinminutes) AS totalminutes")->from("trn_employee_leave_requests")
                        ->where(array("empid" => $empid, "leavetype" => $row->types, "year" => $year, "status" => 301))
                        ->get()->row();

                    $totalspentminutes = ($getleavedetails) ? $getleavedetails->totalminutes : 0;


                    $totalbalance = $row->totalcredit;

                    $dayspent = 0;
                    $hourspent = 0;
                    $minutespent = 0;

                    //SPENT
                    $totalspenthours = $totalspentminutes / 60;
                    $dayspent = floor($totalspenthours / 8);
                    $hourspent = ($totalspenthours % 8);
                    $n = $totalspenthours;
                    $whole = floor($n);      // 1
                    $minutespent = ($n - $whole) * 60;

                    //TOTAL SPENT BY MINUTES
                    $totalspentminutes = $minutespent + ($hourspent * 60) + ($dayspent * 8 * 60);

                    $daybalance = 0;
                    $hourbalance = 0;
                    $minutebalance = 0;

                    //BALANCE
                    $balanceminutes = $totalbalance * 8 * 60;
                    $ramainingminutes = $balanceminutes - $totalspentminutes;

                    $totalbalancehours = $ramainingminutes / 60;
                    if (($totalbalancehours / 8) < 0) {
                        $daybalance = intval($totalbalancehours / 8);
                    } else {
                        $daybalance = floor($totalbalancehours / 8);
                    }

                    $hourbalance = ($totalbalancehours % 8);
                    $n = $totalbalancehours;
                    $whole = floor($n);      // 1
                    $minutebalance = ($n - $whole) * 60;


                    $html .= '<li class="list-group-item" id="' . $row->sysid . '">';
                    $html .= '<span class="col-md-2 label-name " >';
                    $html .= '<input type="radio" name="leavecredit" class="radio-leave-credits" value="' . $row->sysid . '" />';
                    $html .= '<a class="popovers" style="margin-left: 5px;" href="javascript:;" data-content=' . $row->desc . ' data-trigger="hover" data-original-title=' . $row->names . ' data-placement="right">' . $row->names . '</a></span>';
                    $html .= '<span class="col-md-2 label-default number credit">';
                    $html .= $row->totalcredit;
                    $html .= '</span>';
                    $html .= '<span class="col-md-4 label-default number spent" style="color: red;">' . str_pad($dayspent, 2, '0', STR_PAD_LEFT) . ' - ' . str_pad($hourspent, 2, '0', STR_PAD_LEFT) . ' - ' . str_pad(round($minutespent), 2, '0', STR_PAD_LEFT) . '</span>';
                    $html .= '<span class="col-md-4 label-default number balance">' . str_pad($daybalance, 2, '0', STR_PAD_LEFT) . ' - ' . str_pad($hourbalance, 2, '0', STR_PAD_LEFT) . ' - ' . str_pad(round($minutebalance), 2, '0', STR_PAD_LEFT) . '</span>';
                    $html .= '</li>';
                }
            }
        }


        $data['html'] = $html;
        return json_encode($data);
    }
    function get_employee_leave_credits() {
        $data = array();
        $html = '';
        $empid = $this->input->post('empid');
        $year_input = $this->input->post('year');
        $year = ($year_input && $year_input > 0) ? $year_input : date('Y');

        $qry = $this->db->query("SELECT DISTINCT tp.sysid, tp.names, tp.desc,lc.types, SUM(lc.credit) AS totalcredit
            FROM prime_employee_main_leave_credits AS lc
            LEFT JOIN prime_types_parameter AS tp ON tp.sysid = lc.types
            WHERE lc.empid = $empid  AND lc.year = $year AND lc.status = 1
            GROUP BY tp.sysid, tp.names, tp.desc,lc.types
            ");


        if($qry->num_rows() > 0) {

            $html .= '<li class="list-group-item list-group-item-info">';
            $html .= '<span class="col-md-2 label-default " >Type</span>';
            $html .= '<span class="col-md-2 label-default number">Credit</span>';
            $html .= '<span class="col-md-4 label-default number">Spent</span>';
            $html .= '<span class="col-md-4 label-default number">Balance</span>';
            $html .= '</li>';
            $num = 0;

            foreach ($qry->result() as $row){

                $getleavedetails = $this->db->select("SUM(totalinminutes) AS totalminutes")->from("trn_employee_leave_requests")
                    //->where(array("empid" => $empid , "leavetype" => $row->types , "year" => $year , "status != " => 0, "status != " => 302))
                    ->where(array("empid" => $empid , "leavetype" => $row->types , "year" => $year , "status" => 301))
                    ->get()->row();

                $totalspentminutes = ($getleavedetails) ? $getleavedetails->totalminutes : 0;


                $totalbalance =$row->totalcredit;

                $dayspent = 0;
                $hourspent = 0;
                $minutespent = 0;

                //SPENT
                $totalspenthours = $totalspentminutes / 60;
                $dayspent = floor($totalspenthours / 8);
                $hourspent = ($totalspenthours % 8);
                $n = $totalspenthours;
                $whole = floor($n);      // 1
                $minutespent = ($n - $whole) * 60;

                //TOTAL SPENT BY MINUTES
                $totalspentminutes = $minutespent + ($hourspent * 60) + ($dayspent * 8 * 60);

                $daybalance = 0;
                $hourbalance = 0;
                $minutebalance = 0;

                //BALANCE
                $balanceminutes = $totalbalance * 8 * 60;
                $ramainingminutes = $balanceminutes - $totalspentminutes;

                $totalbalancehours = $ramainingminutes / 60;
                if(($totalbalancehours / 8) < 0){
                    $daybalance = intval($totalbalancehours / 8);
                }else{
                    $daybalance = floor($totalbalancehours / 8);
                }

                $hourbalance =  ($totalbalancehours % 8);
                $n = $totalbalancehours;
                $whole = floor($n);      // 1
                $minutebalance = ($n - $whole) * 60;


                $html .= '<li class="list-group-item" id="'.$row->sysid.'">';
                $html .= '<span class="col-md-2 label-name " >';
                $html .= '<input required type="radio" name="leavecredit" class="radio-leave-credits" value="'.$row->sysid.'" />';
                $html .= '<a class="popovers" style="margin-left: 5px;" href="javascript:;" data-content='.$row->desc.' data-trigger="hover" data-original-title='.$row->names.' data-placement="right">'.$row->names.'</a></span>';
                $html .= '<span class="col-md-2 label-default number credit">';
                $html .= $row->totalcredit;
                $html .= '</span>';
                $html .= '<span class="col-md-4 label-default number spent" style="color: red;">'.str_pad($dayspent, 2, '0', STR_PAD_LEFT).' - '.str_pad($hourspent, 2, '0', STR_PAD_LEFT).' - '.str_pad(round($minutespent), 2, '0', STR_PAD_LEFT).'</span>';
                $html .= '<span class="col-md-4 label-default number balance">'.str_pad($daybalance, 2, '0', STR_PAD_LEFT).' - '.str_pad($hourbalance, 2, '0', STR_PAD_LEFT).' - '.str_pad(round($minutebalance), 2, '0', STR_PAD_LEFT).'</span>';
                $html .= '</li>';
            }
        }


        $data['html'] = $html;
        return json_encode($data);
    }

    function NumberBreakdown($number, $returnUnsigned = false)
    {
        $negative = 1;
        if ($number < 0)
        {
            $negative = -1;
            $number *= -1;
        }

        if ($returnUnsigned){
            return array(
                floor($number),
                ($number - floor($number))
            );
        }

        return array(
            floor($number) * $negative,
            ($number - floor($number)) * $negative
        );
    }
    function compute_num_days() {
        $data = array();
        $holidays = array(); //@TODO TABLE FOR HOLIDY NOT DONE YET!
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');


        $num_days = getWorkingDays($this->input->post('datefrom'), $this->input->post('dateto'), $holidays);

        if($datefrom == '' || $dateto = ''){
            $num_days = 0;
        }
        $data['numdays'] = ($num_days) ? $num_days : 0;
        return json_encode($data);
    }

    function dt_leave_for_approval_request() {
        $data = array();

        $loggedin_empid_info = get_user_employee_info(user_id());
        $loggedin_empid = ($loggedin_empid_info) ? $loggedin_empid_info->sysid : 0;

        $leavesqry = $this->db->select()
            ->from('trn_employee_leave_requests_approval')
            ->where(array('status != ' => 0, 'types != ' => 1))
            ->order_by('datecreated', 'desc')
            ->limit(100)
            ->get();

        if ($leavesqry->num_rows() > 0) {
            $empid = 0;
            foreach ($leavesqry->result() as $row) {

                $emp_name = '';
                $qry_leave_items = $this->db->select()
                    ->from('trn_employee_leave_requests')
                    ->where(array('status != ' => 0, 'groupid' => $row->sysid))
                    ->get();
                $total_mins = 0;
                if($qry_leave_items->num_rows()>0) {
                    foreach($qry_leave_items->result() as $irow) {
                        $total_mins += $irow->totalinminutes;
                        $empid = $irow->empid;
                    }
                    $emp_info = get_employee_info($empid);
                    if($emp_info) {
                        $emp_name = $emp_info->lastname . ', ' . $emp_info->firstname;
                    }
                } else {
                    $total_mins = 'N/A';
                }



                if($row->reason != '') {
                    $remarks = $row->reason;
                }else{
                    $remarks = 'None';
                }

                $duration = 0;
                if ($total_mins != 'N/A' || $total_mins != 0){
                    $day = floor($total_mins/480);
                    $hour = floor(($total_mins - $day * 480)/60);
                    $minute = $total_mins - ($day * 480) - ($hour * 60);

                    $days = ($day != 0) ? $day.'Day(s) ' : '';
                    $hours = ($hour != 0) ? $hour.'Hrs ' : '';
                    $minutes = ($minute != 0) ? $minute.'Min' : '';

                    $duration = $days.$hours.$minutes;
                }

                $created = new DateTime($row->datecreated);

                $button_head = '<code>N/A</code>';
                $button_exec = '<code>N/A</code>';
                $head_id = 0;
                $exec_id = 0;
                $ccid = 0;


                /*$cc_id = $this->db->select('ccid')
                    ->from('prime_employee_costcenter')
                    ->where(array('empid' => $empid))
                    ->get()->row();

                if ($cc_id) {
                    $qry_head = $this->db->select('empid')
                        ->from('prime_costcenter_head')
                        ->where('ccid', $cc_id->ccid)->get()->row();

                    $head_id = ($qry_head) ? $qry_head->empid : 0;

                    $qry_exec = $this->db->select('cgh.empid')
                        ->from('prime_costcenter_group_matrix as cgm')
                        ->join('prime_costcenter_group as cg','cg.sysid = cgm.groupid')
                        ->join('prime_costcenter_group_head as cgh','cgm.groupid = cgh.groupid')
                        ->where(array('cgm.ccid' => $cc_id->ccid , 'cg.level' => 2))
                        ->get()->row();

                    $exec_id = ($qry_exec) ? $qry_exec->empid : 0;
                }*/

                $qry_approvals = get_employee_request_approval($empid);

                $button_head_on = false;
                $head_approved = false;
                $exec_approved = false;
                if ($qry_approvals) {
                    $exec_id = $qry_approvals->execid;
                    $head_id_ = $qry_approvals->headid;
                    if ($empid != $head_id_) {
                        $head_id = $head_id_;
                        $button_head_on = true;
                    }
                    $ccid = $qry_approvals->ccid;

                    $qry_approval_trn = $this->db->select()
                        ->from('trn_employee_leave_approval')
                        ->where(array('groupid' => $row->sysid, 'status' => 301, 'approvalid' => $head_id_))
                        ->get()->row();
                    if ($qry_approval_trn) {
                        $head_approved = true;
                    }
                }


                $head_leave = false;


                $qry_approval_exec_trn = $this->db->select()
                    ->from('trn_employee_leave_approval')
                    ->where(array('groupid' => $row->sysid, 'status' => 301, 'approvalid' => $exec_id))
                    ->get()->row();

                if ($qry_approval_exec_trn == false) {

                    if ($button_head_on && $head_approved == false || super_admin()) {
                        if ($loggedin_empid == $head_id || super_admin()) {
                            $get_head_info = get_employee_info($head_id);
                            $get_head_name = ($get_head_info->qry) ? $get_head_info->qry->lastname . ', ' . $get_head_info->qry->firstname : '';
                            $button_head = '<button id="btn_row_approval" title="'.$get_head_name.'"  data-empid="'.$empid.'" data-groupid="' . $row->sysid . '" data-approvalid="' . $head_id . '" class="btn btn-info btn-xs inline"><i class="fa fa-check"></i> Approve</button>';
                        } else {
                            if ($exec_id == $loggedin_empid || super_admin()) {
                                //$head_leave = true;
                                if ($head_leave == true) {
                                    $button_head = '<span class="label label-info">On-leave</span>';
                                } else {
                                    $button_head = '<span class="label label-warning">Pending</span>';
                                }
                            } else {
                                $button_head = '<span class="label label-danger">N/A</span>';
                            }
                        }
                    } else {
                        $button_head = '<span class="label label-success">Approved</span>';
                    }


                    // IF USER LOGGED IN IS EQUAL TO THE EXEC ID |||||  USER LOGGED IN IS ADMIN (SUPER ADMIN ONLY USED)
                    if ((($loggedin_empid == $exec_id && $head_approved) || ($loggedin_empid == $exec_id && $head_leave == true)) || user_id() == 1) {
                        $get_exec_info = get_employee_info($exec_id);
                        $get_exec_name = ($get_exec_info->qry) ? $get_exec_info->qry->lastname . ', ' . $get_exec_info->qry->firstname : '';
                        $button_exec = '<button title="'.$get_exec_name.'" id="btn_row_approval" data-empid="'.$empid.'" data-groupid="' . $row->sysid . '" data-approvalid="' . $exec_id . '" class="btn btn-info btn-xs inline"><i class="fa fa-check"></i> Approve</button>';
                    }
                } else {
                    $button_head = '<span class="label label-success"><i class="fa fa-check"></i> Approved</span>';
                    $button_exec = '<span class="label label-success"><i class="fa fa-check"></i> Approved</span>';
                }



                $control = '';
                if(super_admin()) {
                    $control .= '<button id="btn_row_bypass" data-empid="'.$empid.'" data-groupid="' . $row->sysid . '" data-approvalid="' . $exec_id . '" class="btn btn-danger btn-xs inline"><i class="fa fa-key"></i> Bypass</button>';
                }

                if ($loggedin_empid == $head_id || $loggedin_empid == $exec_id || user_id() == 1) {
                    $data['list'][] = array(
                        'num' => btn_expand($row->sysid),
                        'name' => $empid . ' - ' . $emp_name .' ('.$row->sysid.')',
                        'remarks' => $remarks,
                        'duration' => $duration,
                        'created' => $created->format('m/d/Y'),
                        'head' => $button_head,
                        'executive' => $button_exec,
                        'control' => $control
                    );
                }

            }
        }

        return json_encode($data);
    }

    function leave_approval_details($groupid = false) {
        $data = array();
        $html = '';
        if($groupid==false) {
            $groupid = $this->input->post('id');
        }

        $datecreated = '';
        $createdby = '';
        $reason = 'Not specified.';
        $status = false;

        $get_request_approval = $this->db->select('status, reason')
            ->from('trn_employee_leave_requests_approval')
            ->where(array('sysid' => $groupid))
            ->get()->row();

        if($get_request_approval){
            $reason = $get_request_approval->reason;
            $status = $get_request_approval->status;
        }


        $total_days = 0;
        $total_hrs = 0;
        $total_min = 0;

        $qry_leavedetails = $this->db->select('
            lr.sysid,
            lr.empid,
            lr.from AS datefrom,
            lr.to AS dateto,
            lr.fromtime,
            lr.totime,
            lr.totalinminutes,
            lr.leavetype,
            lr.leavedate,
            lr.`type`,
            lr.datecreated,
            tp.`names`
        ')
            ->from('trn_employee_leave_requests AS lr')
            ->join('prime_types_parameter AS tp','lr.leavetype = tp.sysid')
            ->where('groupid', $groupid)
            ->get();

        $html_list = '';
        $td_list = '';

        if ($qry_leavedetails->num_rows() > 0 ) {
            $total_days = 0;
            $total_hrs = 0;
            $total_min = 0;

            foreach($qry_leavedetails->result() as $lrow) {
                $type = ($lrow->type == 1) ? 'Leave' : 'Locator';

                $total_time = $lrow->totalinminutes;

                $total_days = floor($total_time/480);
                $total_hrs = floor(($total_time - $total_days * 480)/60);
                $total_min = $total_time - ($total_days * 480) - ($total_hrs * 60);

                $datestart = $lrow->datefrom == '' ? $lrow->leavedate : $lrow->datefrom;
                $dateend = $lrow->dateto == '' ? $lrow->leavedate : $lrow->dateto;

                $datecreated = date('m/d/Y',strtotime($lrow->datecreated));

                $html_list .= '<li class="list-group-item">';
                $html_list .= '<span class="col-md-1 label-name">'.$type.'</span>';
                $html_list .= '<span class="col-md-3 label-name number">'.date('m/d/Y',strtotime($datestart)).'</span>';
                $html_list .= '<span class="col-md-2 label-name number">'.date('m/d/Y',strtotime($dateend)).'</span>';
                $html_list .= '<span class="col-md-2 label-name number">'.$lrow->fromtime.'</span>';
                $html_list .= '<span class="col-md-2 label-name number">'.$lrow->totime.'</span>';
                $html_list .= '<span class="col-md-2 label-name number">'.$datecreated.'</span>';
                $html_list .= '</li>';

                $td_list .= '<tr>';
                $td_list .= '<td>'.$type.'</td>';
                $td_list .= '<td>'.date('m/d/Y',strtotime($datestart)).'</td>';
                $td_list .= '<td>'.date('m/d/Y',strtotime($dateend)).'</td>';
                $td_list .= '<td>'.$lrow->fromtime.'</td>';
                $td_list .= '<td>'.$lrow->totime.'</td>';
                $td_list .= '</tr>';
            }


            $html .=  '<div class="row">';
            // DETAILS WHO CREATE THIS LEAVE AND DATES OF CREATION
            // QUERY DETAILS FROM GROUP
            $html .= '<div class="col-md-3">';
            $html .= '<h5 class="text-primary" style="margin: 5px 0px;"><i class="fa fa-tag"></i> Lists of Transactions</h5>';
            $html .= '<ul class="list-group summary column">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-6 label-name">Created By</span>';
            $html .= '<span class="col-md-6 label-default">Name</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-6 label-name">Date Created</span>';
            $html .= '<span class="col-md-6 label-default">Name</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-6 label-name">Date Updated</span>';
            $html .= '<span class="col-md-6 label-default">Name</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';


            // SUMMARY OF DETAILS
            $html .= '<div class="col-md-3">';
            $html .= '<h5 class="text-primary" style="margin: 5px 0px;"><i class="fa fa-tag"></i> Lists of Transactions</h5>';
            $html .= '<ul class="list-group summary column">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-6 label-name">Total Days</span>';
            $html .= '<span class="col-md-6 label-default number">'.$total_days.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-6 label-name">Total Hours</span>';
            $html .= '<span class="col-md-6 label-default number">'.$total_hrs.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-6 label-name">Total Min.</span>';
            $html .= '<span class="col-md-6 label-default number">'.$total_min.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';


            // DETAILS DURATIONS LISTS
            $html .= '<div class="col-md-6">';
            $html .= '<h5 class="text-primary" style="margin: 5px 0px;"><i class="fa fa-tag"></i> Lists of Transactions</h5>';
            $html .= '<ul class="list-group summary column">';

            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-1 label-default">Type</span>';
            $html .= '<span class="col-md-3 label-default number">Date Start</span>';
            $html .= '<span class="col-md-2 label-default number">Date End</span>';
            $html .= '<span class="col-md-2 label-default number">Time Start</span>';
            $html .= '<span class="col-md-2 label-default number">Time End</span>';
            $html .= '<span class="col-md-2 label-default number">Date Applied</span>';
            $html .= '</li>';

            $html .= $html_list;

            $html .= '</ul>';
            $html .= '</div>';


            $html .=  '</div>';

        }

        $data['datecreated'] = $datecreated;
        $data['createdby'] = $createdby;
        $data['totaldays'] = $total_days;
        $data['totalhrs'] = $total_hrs;
        $data['totalmins'] = $total_min;
        $data['list'] = $td_list;
        $data['reason'] = $reason;
        $data['status'] = $status;

        $data['html'] = $html;
        return json_encode($data);
    }

    function approve_leave_request($approvalid = false, $groupid = false, $empid = false){
        $data = array();

        if($approvalid == false && $groupid == false && $empid == false) {
            $approvalid = $this->input->post('approvalid');
            $groupid = $this->input->post('groupid');
            $empid = $this->input->post('empid');
        }

        $data['title'] = 'Leave Approval';

        $empname = '';

        $emp_info = get_employee_info($empid);

        if($emp_info->qry) {
            $empname = $emp_info->qry->lastname . ', ' . $emp_info->qry->firstname;
        }

        $qry_approve = $this->db->update('trn_employee_leave_approval', array('status' => 301), array('groupid' => $groupid , 'approvalid' => $approvalid));

        $get_approval_details = $this->db->select('approvalid, types')
            ->from('trn_employee_leave_approval')
            ->where(array('groupid' => $groupid , 'approvalid' => $approvalid))
            ->get()->row();


        $data['approvaldetails'] = $get_approval_details;


        /*
        if ($exec_qry) {
            if ($exec_qry->execid == $approvalid || user_id() == 1) {
                $qry_approve_group = $this->db->update('trn_employee_leave_requests_approval', array('status' => 301), array('sysid' => $groupid));
                if ($qry_approve_group){
                    $data['groupstatus'] = 'Group status updated';
                }else{
                    $data['groupstatus'] = 'Failed to update group.';
                }
            }
        }
        */

        if($get_approval_details) {
            if($get_approval_details->types == 2) {
                $qry_approve_req = $this->db->update('trn_employee_leave_requests', array('status' => 301), array('groupid' => $groupid));
                $qry_approve_group = $this->db->update('trn_employee_leave_requests_approval', array('status' => 301), array('sysid' => $groupid));
                if ($qry_approve_group){
                    $data['groupstatus'] = 'Group status updated';


                    $get_employee_info = get_employee_info($empid);
                    $get_employee_email = ($get_employee_info) ? $get_employee_info->emailcomp : false;

                    if($get_employee_email) {
                        $content = '';
                        $content .= 'Hello!, <br><br>';
                        $content .= 'Your leave request has been approved.<br>';
                        $content .= '<br><br>Thank you,';
                        $content .= '<br><br><span style="color: red">This is system generated email, please do not reply!</span>';
                        mailer($get_employee_email, $content, '[Approved] Leave Request - ' . $empname, false);
                    }



                }else{
                    $data['groupstatus'] = 'Failed to update group.';
                }
            } else {

                /*
                $exec_qry = $this->db->select('cgh.empid AS execid')
                    ->from('prime_employee_costcenter AS cc')
                    ->join('prime_costcenter_head AS ch','cc.ccid = ch.ccid')
                    ->join('prime_costcenter_group_matrix AS cgm','cc.ccid = cgm.ccid')
                    ->join('prime_costcenter_group AS cg','cgm.groupid = cg.sysid')
                    ->join('prime_costcenter_group_head AS cgh','cgh.groupid = cg.sysid')
                    ->where(array('cc.empid' => $empid, 'cg.`level`' => 2, 'cc.`status`' => 1))
                    ->get()->row();
                */

                $qry_exec = $this->db->select('cgh.empid AS execid')
                    ->from('prime_employee_costcenter AS cc')
                    ->join('prime_costcenter_group_matrix AS cgm','cc.ccid = cgm.ccid')
                    ->join('prime_costcenter_group AS cg','cgm.groupid = cg.sysid')
                    ->join('prime_costcenter_group_head AS cgh','cgh.groupid = cg.sysid')
                    ->where(array('cc.empid' => $empid, 'cc.`status`' => 1, 'cgm.status' => 1, 'cg.level != ' => 4, 'cg.level != ' => 5))
                    ->get()->row();

                $exceid = ($qry_exec) ? $qry_exec->execid : 0;

                $data['exec'] = $qry_exec;

                if($qry_exec) {
                    $get_head_info = get_employee_info($exceid);
                    $get_head_email = ($get_head_info) ? $get_head_info->emailcomp : false;

                    if ($get_head_email) {
                        $content = '';
                        $content .= 'Hello!, <br><br>';
                        $content .= 'A leave request has been submitted, and requires your attention<br>';
                        $content .= 'Please visit Employee Leave Approval, or <a href="http://erp.panayelectric.com/erp/request/leaveapprovalonline/'.$exceid.'/'.$groupid.'/'.$empid.'" target="_blank">click here</a>';
                        $content .= '<br><br>Thank you,';
                        $content .= '<br><br><span style="color: red">This is system generated email, please do not reply!</span>';
                        // mailer('lfaderon@gmail.com', $content, '[Head Approved] Leave Request - ' . $empname, false);
                        mailer($get_head_email, $content, '[Head Approved] Leave Request - ' . $empname, false);
                    }
                }
            }
        }

        if ($qry_approve){
            $data['msg'] = 'Request Approved.';
            $data['func'] = 'success';
            $data['qry'] = true;
        }else{
            $data['msg'] = 'Error updating approval.';
            $data['func'] = 'warning';
            $data['qry'] = false;
        }

        return json_encode($data);
    }

    function get_employee_logs () {
        $data = array();
        $empid = $this->input->post('empid');

        $sql = $this->db->select()
            ->from('prime_employee_main_history')
            ->where(array('dataid' => $empid,'status' => 1))
            ->get();

        $employee = $this->db->select('status')
            ->from('prime_employee_main')
            ->where(array('sysid' => $empid))
            ->get()->row();

        //print_r($this->db->last_query());
        //exit();

        if ($sql->num_rows() > 0) {
            $num = 1;
            foreach ($sql->result() AS $row) {
                $remarks = ($row->remarks == '') ? '<code>N/A</code>' : $row->remarks;
                if ($row->statusid == '') {
                    if ($employee->status != 0) {
                        $statusname = '<span class="label label-success"><i class="fa fa-check-circle-o"></i> Active</span>';
                    } else {
                        $statusname = '<span class="label label-success"><i class="fa fa-check-times-o"></i> Separated</span>';
                    }
                }else{
                    if ($row->statusid == 1) {
                        $statusname = '<span class="label label-success"><i class="fa fa-check-circle-o"></i> Active</span>';
                    } else {
                        $statusname = get_types_label_format($row->statusid);
                    }
                }

                $usr_info_arr = get_users_info($row->updatedby);
                $updated = ($usr_info_arr) ? $usr_info_arr->lastname : '<code>N/A</code>';

                $data['listlogs'][] = array(
                    'num' => $num,
                    'datecreated' => $row->datecreated,
                    'specificdate' => date_formating($row->specificdate,'Y-m-d','m/d/Y'),
                    'remarks' => $remarks,
                    'status' => $statusname,
                    'updated' => $updated,
                    'controls' => '<a href="'.base_url().'hris/delemplog" data-id="'.$row->sysid.'" id="btn_delete_emplogs" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>',
                );
                $num++;
            }
        }

        $data['empstat'] = $employee->status;
        return json_encode($data);
    }

    function add_employee_log() {
        $data = array();

        $empid = $this->input->post('userid');
        $specificdate = $this->input->post('specificdate');
        $remarks = $this->input->post('remarks');
        $empstatus = $this->input->post('empstatus');

        $ins_arr = array(
            'dataid' => $empid,
            'specificdate' => $specificdate,
            'remarks' => $remarks,
            'statusid' => $empstatus,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );

        $deactivate_array = array(3229,3230,3231);
        $this->db->trans_begin();
        if ($empstatus != '' && in_array($empstatus,$deactivate_array)){
            $this->db->where(array('sysid' => $empid, 'status' => 1));
            $this->db->update('prime_employee_main',array('status' => 0));
        } else{
            if ($empstatus != '' && $empstatus == 3233){
                $this->db->where(array('sysid' => $empid, 'status' => 1));
                $this->db->update('prime_employee_main',array('status' => $empstatus));

                $this->db->where(array('empid' => $empid, 'status' => 1));
                $this->db->update('payroll_emplist',array('status' => $empstatus));
            } else {
                $this->db->where(array('sysid' => $empid, 'status != ' => 1));
                $this->db->update('prime_employee_main',array('status' => $empstatus));

                $this->db->where(array('empid' => $empid, 'status != ' => 1));
                $this->db->update('payroll_emplist',array('status' => $empstatus));
            }
        }

        $this->db->insert('prime_employee_main_history',$ins_arr);
        $data = db_trans($this->db);

        return json_encode($data);
    }

    function del_employee_log() {
        $data = array();

        $logid = $this->input->post('emplogid');

        $this->db->trans_begin();
        $this->db->where('sysid',$logid);
        $this->db->update('prime_employee_main_history',array('status' => 0));

        $data = db_trans($this->db);

        return json_encode($data);
    }

    function generate_tardiness_data($id = false,$month=false,$year=false,$print=false) {
        $data = array();
        if ($print==false) {
            $print = $this->input->post('print');
        }

        if ($month == false) {
            $month = $this->input->post('month');
        }

        if ($year == false) {
            $year = $this->input->post('year');
        }

        if ($id == false) {
            $id = $this->input->post('id');
        }

        $inputs = $this->input->post('inputs');

        if ($inputs && count($inputs) > 0) {
            extract($inputs);
        }

        //$data['args'] = func_get_args();

        $html = '';
        $n = 1;
        $latelist = array();
        $maintable = '';
        $detailstable = '';
        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F'); // March

        $startdate = $year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'-01';
        $enddate = date("Y-m-t", strtotime($startdate));

        //$lastday = date("t", strtotime($startdate));

        $begin = new DateTime($startdate);
        $end = new DateTime($enddate);

        $data['startdate'] = $startdate;
        $data['enddate'] = $enddate;

        $end = $end->modify( '+1 day' );
        $allowed_job_cat = array(157,160);
        if ($id != 0) {
            $this->db->where('pem.sysid',$id);
        }
        $emp_qry = $this->db->select("pem.sysid ,p.lastname , p.firstname , peb.bioid , pemp.position_id")
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS empname", false)
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid && peb.status = 1" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid && pemjc.status = 1" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'" , "left")
            ->join("prime_employee_main_positions as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1" , "left")
            ->where('pem.status',1)
            ->where_in('ptp.sysid', $allowed_job_cat)
            ->order_by("p.lastname")
            ->get();

        //$data['empqry'] = $this->db->last_query();

        if ($emp_qry->num_rows() > 0) {
            $list = array();
            foreach ($emp_qry->result() AS $emp) {
                $expand = ($print) ? '' : btn_expand($emp->sysid).' ';
                $list = array(
                    'num' => $expand.$n++,
                    'id' => $emp->sysid,
                    'name' => $emp->empname,
                    'bioid' => $emp->bioid,
                    'position' => select_emp_position($emp->sysid)->names
                );

                $sched = checkempsched($emp->sysid,1,$month,$year,301);
                $list['sched'] = $sched->desc;

                $spectime = $sched->sepcfiedTime;
                $spectime2 = $sched->sepcfiedTime2;

                $interval = new DateInterval('P1D');
                $daterange = new DatePeriod($begin, $interval ,$end);
                //$data['daterange'] = $daterange;
                $latetotal = '00:00:00';
                $cntlate = 0;
                $logs = array();
                $logdetails = array();
                foreach($daterange as $date){
                    //$data['dates'][] = $date->format("Y-m-d");

                    $amlate = '0:00:00';
                    $pmlate = '0:00:00';

                    $amlatetotal = strtotime('0:00:00');
                    $pmlatetotal = strtotime('0:00:00');
                    $totallate = strtotime('0:00:00');

                    $day_name = $date->format("D");
                    $day_num = $date->format("d");
                    $datecondition = $date->format("Y-m-d");

                    $getemptimelogs = $this->db->select("logtime")->from("prime_employee_attendance_timelogs")
                        ->where(array("bioid" => $emp->bioid , "logdate" => $datecondition))
                        ->group_by("logtime")
                        ->order_by("logtime")
                        ->limit(4)
                        ->get();

                    if($getemptimelogs->num_rows() > 0){
                        $amtime  = array();
                        $pmtime  = array();
                        $logs['logdate'] = $datecondition;
                        foreach ($getemptimelogs->result() as $timelogsrow){
                            $time_logs_arr[] = $timelogsrow->logtime;
                            $timearr = substr($timelogsrow->logtime , 0 , 2);

                            //ALL LOGS
                            if($timearr < 11){
                                $amtime[] = $timelogsrow->logtime;
                            }else{
                                $pmtime[] = $timelogsrow->logtime;
                            }
                            $amcount = count($amtime);
                            $pmcount = count($pmtime);

                            //AM
                            if($amcount == 0){
                                $amin = '';
                                $amout = '';
                                $amabsent = true;
                            }else{
                                $amabsent = false;
                                $amin = isset($amtime[0]) ? $amtime[0] : '0:00:00';
                                $amout = isset($pmtime[0]) ? $pmtime[0] : '0:00:00';
                                $logs['timein'] = $amin;
                                $logs['timeout'] = $amout;
                            }

                            //PM
                            if($pmcount == 1){
                                $pmin = '';
                                $pmout = '';
                                $pmabsent = true;
                            }else{
                                $pmabsent = false;
                                if($amabsent == true){
                                    $pmin = isset($pmtime[0]) ? $pmtime[0] : '0:00:00';
                                }else{
                                    $pmin = isset($pmtime[1]) ? $pmtime[1] : '0:00:00';
                                }

                                $lastlog =max($time_logs_arr);
                                $lastlogfirst = substr($lastlog , 0 , 2);

                                if($pmcount == 1 && $lastlogfirst > 16){
                                    $pmin = '0:00:00';
                                    $pmout = $lastlog;
                                }else{
                                    if($lastlogfirst > 15){
                                        $pmout = isset($lastlog) ? $lastlog : '';
                                    }else{
                                        $pmout = '';
                                    }
                                }
                            }
                        }

                        /*$data['listofdays'][] = array(
                            'm' =>  $date->format('m'),
                            'y' =>  $date->format('Y')
                        );*/

                        $specifiedTime = checkempsched($emp->sysid, $date->format("d"), $date->format('m') , $date->format('Y') , 301)->sepcfiedTime;
                        $specifiedTime2 = checkempsched($emp->sysid, $date->format("d"), $date->format('m') , $date->format('Y') , 301)->sepcfiedTime2;

                        $timeDifference = (strtotime($amin) - strtotime($specifiedTime) + 86400) % 86400;
                        $timeDifference2 = (strtotime($pmin) - strtotime($specifiedTime2) + 86400) % 86400;


                        // $amoutspecifiedtime1 =  checkempsched($empid, $dt->format('d'), $dt->format('m') , $dt->format('Y') , 301)->undertimeamout;
                        // $pmoutspecifiedtime2 =  checkempsched($empid, $dt->format('d'), $dt->format('m') , $dt->format('Y') , 301)->undertimepmout;

                        if ($timeDifference >= 360) {
                            // proceed if less than 15 minutes has elapsed since specifiedTime
                            $diff = strtotime($amin) - strtotime($specifiedTime);
                            $hours = floor($diff / 3600);
                            $mins = floor($diff / 60 % 60);
                            $secs = floor($diff % 60);
                            $hrlate = str_pad($hours,2,"0",STR_PAD_LEFT) . ':' . str_pad($mins,2,"0",STR_PAD_LEFT) . ':' . str_pad($secs,2,"0",STR_PAD_LEFT);
                            if ($hours >= 0) {
                                $amlate = $hrlate;
                                $cntlate++;
                            } else {
                                $amlate = '0:00:00';
                            }

                            //$amlates[] =  ($diff > 0) ? $diff : 0;
                            $amlates[$datecondition] =  $amlate;
                            $logs['amlate'] = $amlate;
                            $amlatestimestamp[] =  strtotime($amlate);
                        }

                        if ($timeDifference2 >= 360) {
                            // proceed if less than 15 minutes has elapsed since specifiedTime
                            $diff = strtotime($pmin) - strtotime($specifiedTime2);
                            $hours = floor($diff / 3600);
                            $mins = floor($diff / 60 % 60);
                            $secs = floor($diff % 60);
                            $hrlate = str_pad($hours,2,"0",STR_PAD_LEFT) . ':' . str_pad($mins,2,"0",STR_PAD_LEFT) . ':' . str_pad($secs,2,"0",STR_PAD_LEFT);
                            if ($hours >= 0) {
                                $pmlate = $hrlate;
                                $cntlate++;
                            } else {
                                $pmlate = '0:00:00';
                            }
                            //$pmlates[] = ($diff > 0) ? $diff : 0;
                            $pmlates[] = $pmlate;
                            $logs['pmlate'] = $pmlate;
                            $pmlatestimestamp[] = date($diff);
                        }

                        $sumlates = sum_the_time($amlate,$pmlate);

                        $latetotal = sum_the_time($latetotal,$sumlates);
                        $lates[] = $latetotal;
                        $logs['latetotal'] = $sumlates;

                        if ($sumlates != '') {
                            $logdetails[] = $logs;
                        }
                    }
                }

                //$totallate = array_sum($amlates)+array_sum($pmlates);

                //$total_am = array_reduce($amlates, function ($c, $v) { return $c + strtotime($v) - strtotime('00:00'); }, 0);

                //$data['lates'] = end($lates);
                //$data['amlates'] = $amlates;
                //$data['pmlates'] = $pmlates;
                //$data['amlatestimestamp'] = $amlatestimestamp;
                //$data['pmlatestimestamp'] = $pmlatestimestamp;

                //$data['totalamlates'] = array_sum($amlates);
                //$data['totalpmlates'] = array_sum($pmlates);
                $list['latecount'] = $cntlate;
                $list['totallates'] = (isset($lates) && count($lates) > 0) ? end($lates) : 'N/A';
                $list['logs'] = $logdetails;

                $latelist[] = $list;
            }
        }

        if ($print){

            if (isset($latelist) && count($latelist) > 0) {
                $html .= '<table class="table table-bordered table-responsive table-hover tbl-xs">';
                $html .= '<thead>';
                $html .= '<th class="text-center" style="width:30px">#</th>';
                $html .= '<th>Employee</th>';
                $html .= '<th class="text-center">Bio ID</th>';
                $html .= '<th>Position</th>';
                $html .= '<th>Workshift Assigned</th>';
                $html .= '<th>Lates</th>';
                $html .= '<th class="text-right">Total Late</th>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($latelist as $row) {
                    $html .= '<tr>';
                    $html .= '<td>'.$row['num'].'</td>';
                    $html .= '<td>'.$row['name'].'</td>';
                    $html .= '<td>'.$row['bioid'].'</td>';
                    $html .= '<td>'.$row['position'].'</td>';
                    $html .= '<td>'.$row['sched'].'</td>';
                    $html .= '<td>'.$row['latecount'].'</td>';
                    $html .= '<td>'.$row['totallates'].'</td>';
                    $html .= '</tr>';
                    if (($print == 'details')) {
                        $html .= '<tr>';
                        $html .= '<td colspan="7">';
                        $html .= '<table class="table table-bordered table-responsive table-hover tbl-xs" style="margin-bottom: 0px !important;">';
                        $html .= '<thead>';
                        $html .= '<th>Date</th>';
                        $html .= '<th>Time In</th>';
                        $html .= '<th>Time Out</th>';
                        $html .= '<th>Total Late</th>';
                        $html .= '</thead>';
                        $html .= '<tbody>';
                        if ($row['latecount'] > 0) {
                            foreach ($row['logs'] as $logs) {
                                $html .= '<tr>';
                                $html .= '<td>'.$logs['logdate'].'</td>';
                                $html .= '<td>'.$logs['timein'].'</td>';
                                $html .= '<td>'.$logs['timeout'].'</td>';
                                $html .= '<td>'.$logs['latetotal'].'</td>';
                                $html .= '</tr>';
                            }
                        } else {
                            $html .= '<tr>';
                            $html .= '<td colspan="4" class="center"><h4><i class="fa fa-check text-success"></i> No tardiness data found!</h4></td>';
                            $html .= '</tr>';
                        }
                        $html .= '</tbody>';
                        $html .= '</table>';
                        $html .= '</td>';
                        $html .= '</tr>';
                    }

                }

                $html .= '</tbody>';
                $html .= '</table>';


            }

            $data['html'] = $html;
        } else {
            $data['list'] = $latelist;
        }

        //return $data;
        //exit();

        return json_encode($data);
    }

    //SEPARATE FUNCTION GENERATING TARDINESS WITH FLEXIBLE TIME SCHEDULE
    function generate_flexible_tardiness_data($id = false,$month=false,$year=false,$print=false) {
        $data = array();
        if ($print==false) {
            $print = $this->input->post('print');
        }

        if ($month == false) {
            $month = $this->input->post('month');
        }

        if ($year == false) {
            $year = $this->input->post('year');
        }

        if ($id == false) {
            $id = $this->input->post('id');
        }

        $inputs = $this->input->post('inputs');

        if ($inputs && count($inputs) > 0) {
            extract($inputs);
        }

        //$data['args'] = func_get_args();

        $html = '';
        $n = 1;
        $latelist = array();
        $maintable = '';
        $detailstable = '';
        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F'); // March

        $startdate = $year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'-01';
        $enddate = date("Y-m-t", strtotime($startdate));

        //$lastday = date("t", strtotime($startdate));

        $begin = new DateTime($startdate);
        $end = new DateTime($enddate);

        $data['startdate'] = $startdate;
        $data['enddate'] = $enddate;

        $end = $end->modify( '+1 day' );
        $allowed_job_cat = array(157,160);
        if ($id != 0) {
            $this->db->where('pem.sysid',$id);
        }
        $emp_qry = $this->db->select("pem.sysid ,p.lastname , p.firstname , peb.bioid , pemp.position_id")
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS empname", false)
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid && peb.status = 1" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid && pemjc.status = 1" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'" , "left")
            ->join("prime_employee_main_positions as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1" , "left")
            ->where('pem.status',1)
            ->where_in('ptp.sysid', $allowed_job_cat)
            ->order_by("p.lastname")
            ->get();

        //$data['empqry'] = $this->db->last_query();

        if ($emp_qry->num_rows() > 0) {
            $list = array();
            foreach ($emp_qry->result() AS $emp) {
                $expand = ($print) ? '' : btn_expand($emp->sysid).' ';
                $list = array(
                    'num' => $expand.$n++,
                    'id' => $emp->sysid,
                    'name' => $emp->empname,
                    'bioid' => $emp->bioid,
                    'position' => select_emp_position($emp->sysid)->names
                );

                $sched = checkempsched($emp->sysid,1,$month,$year,301);
                $list['sched'] = $sched->desc;

                $spectime = $sched->sepcfiedTime;
                $spectime2 = $sched->sepcfiedTime2;

                $interval = new DateInterval('P1D');
                $daterange = new DatePeriod($begin, $interval ,$end);
                //$data['daterange'] = $daterange;
                $latetotal = '00:00:00';
                $undertimetotal = '00:00:00';
                $totalundertime = '00:00:00';
                $cntlate = 0;
                $cntunder = 0;
                $logs = array();
                $logdetails = array();
                $lates = array();
                foreach($daterange as $date){
                    //$data['dates'][] = $date->format("Y-m-d");

                    $amlate = '0:00:00';
                    $pmlate = '0:00:00';
                    $undertime = '0:00:00';

                    $amlatetotal = strtotime('0:00:00');
                    $pmlatetotal = strtotime('0:00:00');
                    $totallate = strtotime('0:00:00');

                    $day_name = $date->format("D");
                    $day_num = $date->format("d");
                    $datecondition = $date->format("Y-m-d");

                    $getemptimelogs = $this->db->select("logtime")->from("prime_employee_attendance_timelogs")
                        ->where(array("bioid" => $emp->bioid , "logdate" => $datecondition))
                        ->group_by("logtime")
                        ->order_by("logtime")
                        ->limit(4)
                        ->get();

                    if($getemptimelogs->num_rows() > 0) {
                        $amtime = array();
                        $pmtime = array();
                        $logs['logdate'] = $datecondition;
                        foreach ($getemptimelogs->result() as $timelogsrow) {
                            $time_logs_arr[] = $timelogsrow->logtime;
                            $timearr = substr($timelogsrow->logtime, 0, 2);

                            //ALL LOGS
                            if ($timearr < 11) {
                                $amtime[] = $timelogsrow->logtime;
                            } else {
                                $pmtime[] = $timelogsrow->logtime;
                            }
                            $amcount = count($amtime);
                            $pmcount = count($pmtime);

                            //AM
                            if ($amcount == 0) {
                                $amin = '';
                                $amout = '';
                                $amabsent = true;
                            } else {
                                $amabsent = false;
                                $amin = isset($amtime[0]) ? $amtime[0] : '0:00:00';
                                $amout = isset($pmtime[0]) ? $pmtime[0] : '0:00:00';
                                $logs['timein'] = ($amin != '0:00:00') ? $amin : 'N/A';
                                $logs['timeout'] = ($amout != '0:00:00') ? $amout : 'N/A';
                            }

                            //PM
                            /*if($pmcount == 0){
                                $pmin = '';
                                $pmout = '';
                                $pmabsent = true;
                            }else{
                                $pmabsent = false;
                                if($amabsent == true){
                                    $pmin = isset($pmtime[0]) ? $pmtime[0] : '0:00:00';
                                }else{
                                    $pmin = isset($pmtime[1]) ? $pmtime[1] : '0:00:00';
                                }

                                $lastlog =max($time_logs_arr);
                                $lastlogfirst = substr($lastlog , 0 , 2);

                                if($pmcount == 1 && $lastlogfirst > 16){
                                    $pmin = '0:00:00';
                                    $pmout = $lastlog;
                                }else{
                                    if($lastlogfirst > 15){
                                        $pmout = isset($lastlog) ? $lastlog : '';
                                    }else{
                                        $pmout = '';
                                    }
                                }
                            }*/
                        }

                        /*$data['listofdays'][] = array(
                            'm' =>  $date->format('m'),
                            'y' =>  $date->format('Y')
                        );*/
                        if (!$amabsent) {
                            $empsched = checkempsched($emp->sysid, $date->format("d"), $date->format('m'), $date->format('Y'), 301);

                        $specifiedTime = $empsched->sepcfiedTime;
                        $roundedin = date('H:i:s', floor(strtotime($amin) / 60) * 60);
                        $timeDifference = (strtotime($roundedin) - strtotime($specifiedTime));

                        // Resolve scheduled timeout
                        $scheduledOut = isset($empsched->pmtimeout) && $empsched->pmtimeout ? $empsched->pmtimeout : date('H:i:s', strtotime($specifiedTime) + 32400);
                        $scheduledOutRounded = date('H:i:s', floor(strtotime($scheduledOut) / 60) * 60);

                        // Use actual timeout, fallback to scheduled out if missing
                        if ($amout == '0:00:00' || $amout == '') {
                            $roundedout = $scheduledOutRounded;
                        } else {
                            $roundedout = date('H:i:s', floor(strtotime($amout) / 60) * 60);
                        }

                        // Grace threshold for lateness: 15 minutes
                        $timeDifference = floor($timeDifference / 60) * 60;
                        $graceThreshold = 900;
                        if ($timeDifference >= $graceThreshold) {
                            $diff = $timeDifference;
                            $hours = floor($diff / 3600);
                            $mins = floor(($diff % 3600) / 60);
                            $secs = 0;
                            $hrlate = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT) . ':' . str_pad($secs, 2, '0', STR_PAD_LEFT);
                            $amlate = $hrlate;
                            $cntlate++;
                            $amlates[$datecondition] = $amlate;
                            $logs['amlate'] = $amlate;
                            $amlatestimestamp[] = strtotime($amlate);
                        }

                        // Undertime computation: scheduled out minus actual out (exclude lunch hour if before 1PM)
                        $timeoutDiff = strtotime($scheduledOutRounded) - strtotime($roundedout);
                        $hrundertime = '';
                        if ($timeoutDiff > 0) {
                            // Exclude lunch break if employee timed out before 1PM
                            if (strtotime($roundedout) < strtotime('13:00:00')) {
                                $timeoutDiff = $timeoutDiff - 3600;
                                if ($timeoutDiff < 0) { $timeoutDiff = 0; }
                            }
                            $hours = floor($timeoutDiff / 3600);
                            $mins = floor(($timeoutDiff % 3600) / 60);
                            $hrundertime = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT) . ':00';
                            $cntunder++;
                        }

                        if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $hrundertime)) {
                            $undertime = $hrundertime;
                        }
                        $logs['undertime'] = $hrundertime;

                            $sumlates = sum_the_time($amlate, $pmlate);

                            $latetotal = sum_the_time($latetotal, $sumlates);
                            $undertimetotal = sum_the_time($undertimetotal, $undertime);
                            $lates[] = $latetotal;
                            $undertimes[] = $hrundertime;
                            $logs['latetotal'] = $sumlates;
                            //$logs['undertimetotal'] = $undertimetotal;
                            $totalundertime = sum_the_time($totalundertime,$hrundertime);

                            if ($cntlate > 0 || $cntunder > 0) {
                                $logdetails[] = $logs;
                            }
                            //$logdetails[] = $logs;
                        }
                    }
                }

                //$totallate = array_sum($amlates)+array_sum($pmlates);

                //$total_am = array_reduce($amlates, function ($c, $v) { return $c + strtotime($v) - strtotime('00:00'); }, 0);

                //$data['lates'] = end($lates);
                //$data['amlates'] = $amlates;
                //$data['pmlates'] = $pmlates;
                //$data['amlatestimestamp'] = $amlatestimestamp;
                //$data['pmlatestimestamp'] = $pmlatestimestamp;

                //$data['totalamlates'] = array_sum($amlates);
                //$data['totalpmlates'] = array_sum($pmlates);
                $list['latecount'] = $cntlate;
                $list['undertimecount'] = $cntunder;
                //$list['undertimes'] = $undertimes;
                //$totalundertime = '00:00:00';
                /*if (count($undertimes) > 0) {
                    foreach ($undertimes AS $times) {
                        $totalundertime = sum_the_time($totalundertime,$times);
                    }
                }*/
                $list['totallates'] = (isset($lates) && count($lates) > 0) ? end($lates) : '00:00:00';
                $list['totalundertime'] = $totalundertime;
                $list['logs'] = $logdetails;
                $list['fordeds'] = sum_the_time(end($lates),$totalundertime);
                $latelist[] = $list;
            }
        }

        if ($print){
            $html.=peco_print_header(user_id(),'Tardiness Report', false, true);
            if (isset($latelist) && count($latelist) > 0) {
                $html .= '<table class="table table-bordered table-responsive table-hover tbl-xs">';
                $html .= '<thead>';
                $html .= '<th></th>';
                $html .= '<th>Employee</th>';
                $html .= '<th>Bio ID</th>';
                //$html .= '<th>Position</th>';
                //$html .= '<th>Workshift Assigned</th>';
                //$html .= '<th>Lates</th>';
                $html .= '<th>Total Late</th>';
                //$html .= '<th>Undertime</th>';
                $html .= '<th class="text-right">Total Undertime</th>';
                $html .= '<th class="text-right">Total for Deduction</th>';
                $html .= '<th class="text-right">Numeric (Hours)</th>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($latelist as $row) {
                    $totaltime = sum_the_time($row['totallates'],$row['totalundertime']);
                    $thours = 0;
                    $tmins = 0;
                    $tsecs = 0;
                    $tmin = 0;
                    $thour = 0;
                    $numeric = '';
                    if ($totaltime && $totaltime != '00:00:00') {
                        list($thours,$tmins,$tsecs) = explode(':',$totaltime);
                        $tmin = $tmins + ($tsecs/60);
                        $thour = $thours + ($tmin/60);
                        $numericval = round($thour,2);
                        $numeric = ((int)$numericval == $numericval)  ? $numericval : number_format($numericval,2);
                    }
                    $html .= '<tr class="avoid-break">';
                    $html .= '<td class="text-center">'.$row['num'].'</td>';
                    $html .= '<td>'.$row['name'].'</td>';
                    $html .= '<td class="text-center">'.$row['bioid'].'</td>';
                    //$html .= '<td>'.$row['position'].'</td>';
                    //$html .= '<td>'.$row['sched'].'</td>';
                    //$html .= '<td>'.$row['latecount'].'</td>';
                    $html .= '<td class="text-right">'.$row['totallates'].'</td>';
                    //$html .= '<td>'.$row['undertimecount'].'</td>';
                    $html .= '<td class="text-right">'.$row['totalundertime'].'</td>';
                    $html .= '<td class="text-right">'.$totaltime.'</td>';
                    $html .= '<td class="text-right">'.$numeric.'</td>';
                    $html .= '</tr>';
                    if (($print == 'details')) {
                        $html .= '<tr>';
                        $html .= '<td colspan="9">';
                        $html .= '<div class="avoid-break">';
                        $html .= '<table class="table table-bordered table-responsive table-hover tbl-xs" style="margin-bottom: 0px !important;">';
                        $html .= '<thead>';
                        $html .= '<th>Date</th>';
                        $html .= '<th>Time In</th>';
                        $html .= '<th>Time Out</th>';
                        $html .= '<th class="text-right">Total Late</th>';
                        $html .= '<th class="text-right">Total Undertime</th>';
                        $html .= '<th class="text-right">Total for Deduction</th>';
                        $html .= '<th class="text-right">Numeric</th>';
                        $html .= '</thead>';
                        $html .= '<tbody>';
                        if ($row['latecount'] > 0 || $row['undertimecount'] > 0) {
                            foreach ($row['logs'] as $logs) {
                                if ($logs['latetotal'] != '' || $logs['undertime'] != '') {
                                    $totallogtime = sum_the_time($logs['latetotal'],$logs['undertime']);
                                    $loghour = 0;
                                    $logmin = 0;
                                    $logsec = 0;
                                    $logmins = 0;
                                    $loghours = 0;
                                    $lognumeric = '';
                                    if ($totaltime && $totallogtime != '') {
                                        //echo $logs['logdate'].' - '.$totallogtime.'<br>';
                                        list($loghour,$logmin,$logsec) = explode(':',$totallogtime);
                                        $logmins = $logmin + ($logsec/60);
                                        $loghours = $loghour + ($logmins/60);
                                        $logval = round($loghours,2);
                                        $lognumeric = ((int)$logval ==  $logval) ? $logval : number_format($logval,2);
                                    }
                                    $html .= '<tr>';
                                    $html .= '<td>' . $logs['logdate'] . '</td>';
                                    $html .= '<td>' . $logs['timein'] . '</td>';
                                    $html .= '<td>' . $logs['timeout'] . '</td>';
                                    $html .= '<td class="text-right">' . $logs['latetotal'] . '</td>';
                                    $html .= '<td class="text-right">' . $logs['undertime'] . '</td>';
                                    $html .= '<td class="text-right">' . $totallogtime . '</td>';
                                    $html .= '<td class="text-right">' . $lognumeric . '</td>';
                                    $html .= '</tr>';
                                }
                            }
                        } else {
                            $html .= '<tr>';
                            $html .= '<td colspan="7" class="center"><h4><i class="fa fa-check text-success"></i> No tardiness data found!</h4></td>';
                            $html .= '</tr>';
                        }
                        $html .= '</tbody>';
                        $html .= '</table>';
                        $html .= '</div>';
                        $html .= '</td>';
                        $html .= '</tr>';
                    }

                }

                $html .= '</tbody>';
                $html .= '</table>';


            }

            $data['html'] = $html;
        } else {
            $data['columns'] = array(
                dt_column_array('num','#','','10px'),
                dt_column_array('name','Employee','text-danger text-bold','30px'),
                dt_column_array('bioid','Bio ID','text-primary','20px'),
                //dt_column_array('position','Position','text-danger text-bold','20%'),
                //dt_column_array('sched','Workshift Assignment','text-success text-bold','30%'),
                //dt_column_array('latecount','Lates','','10px'),
                dt_column_array('totallates','Total Lates','','10px'),
                //dt_column_array('undertimecount','Undertime','','10px'),
                dt_column_array('totalundertime','Total Undertime','','10px'),
                dt_column_array('fordeds','Total for Deduction','','10px'),
            );
            $data['list'] = $latelist;
        }

        //return $data;
        //exit();

        return json_encode($data);
    }

}

