<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_payroll extends CI_Model {

    function emplist_per_class() {

        $dept = $this->input->post('dept');
        $hashcode = $this->input->post('hashcode');
        $viewtype = $this->input->post('viewtype');
        $qry = $this->db->select('e.sysid, e.empid, e.status, p.firstname, p.lastname, p.middlename, p.gender, p.birthdate, pes.salary, sss.total_employee_share, philheath.employee_share')
            ->select('pagibig.employee_share * pagibig.max as pagibigEE', false)
            ->select('bir.base_tax + (((pes.salary-sss.total_employee_share-philheath.employee_share-(pagibig.employee_share * pagibig.max))-bir.lower_range)*bir.per_ex) as withtax', false)
            ->from("prime_employee_main e")
            ->join("person p", "e.personid = p.sysid", 'left')
            ->join("prime_employee_salary pes", "pes.emp_id = e.sysid", 'left')
            ->join('prime_employee_pagibig as pagibig', 'pes.salary >=  pagibig.lower_range and pes.salary <= pagibig.higher_range', 'left')
            ->join('prime_employee_sss as sss', 'pes.salary > sss.salary_lower_range and pes.salary < sss.salary_higher_range', 'left')
            ->join('prime_employee_philhealth as philheath', 'pes.salary >= philheath.lower_salary_range and pes.salary <= philheath.higher_salary_range', 'left')
            ->join('prime_employee_bir as bir', 'pes.salary >= bir.lower_range and pes.salary <= bir.higher_range', 'left')
            ->join('prime_employee_main_job_category AS jc', 'jc.empid = e.sysid', 'left')
            ->where('e.status', 1)
            ->where('jc.jobcatid', 157)
            ->group_by('e.sysid')
            ->get();

        $num_rows = $qry->num_rows();
        if ($num_rows > 0) {

            $total_net = 0;
            $total_deduction = 0;
            foreach ($qry->result() as $row) {
                $deduct = ($row->pagibigEE + $row->total_employee_share + $row->withtax);



                $netpay = $row->salary - $deduct;
                $total_net += $netpay;
                $total_deduction += $deduct;
                $data['list'][] = array(
                    'expand' => btn_expand($row->sysid),
                    'firstname' => $row->firstname,
                    'lastname' => gender_icon($row->gender) . ' ' . $row->lastname,
                    'middlename' => $row->middlename,
                    'gender' => gender_icon($row->gender),
                    'birthdate' => $row->birthdate,
                    'empid' =>  emp_pic_draw($row->sysid, '20px', '20px') . data_empty($row->empid),
                    'netpay' => number_format($netpay, 2),
                    'basic' => number_format($row->salary, 2),
                    'loans' => '0.00',
                    'deduct' => number_format($deduct, 2),
                    'control' => row_btn_view($hashcode, $row->sysid, true, 'View') . '<div class="md-checkbox pull-right">
											<input name="empid[]" value="' . $row->sysid . '" type="checkbox" id="checkbox_' . $row->sysid . '" class="md-check" checked="">
											<label for="checkbox_' . $row->sysid . '">
											<span class="inc"></span>
											<span class="check"></span>
											<span class="box"></span></label>
										</div>'
                );
            }
        }

        // COUNTS TABLE 
        $count_male = $this->db->select('p.gender')->from('prime_employee_main AS e')->join("person p", "e.personid = p.sysid")->where('p.gender', 1)->get();
        $count_female = $this->db->select('p.gender')->from('prime_employee_main AS e')->join("person p", "e.personid = p.sysid")->where('p.gender', 2)->get();

        $data['totaldeduct'] = number_format($total_deduction, 2);
        $data['totalnet'] = number_format($total_net, 2);
        $data['malecnt'] = $count_male->num_rows();
        $data['femalecnt'] = $count_female->num_rows();
        $data['input'] = $this->input->post();
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_rows;
        $data['iTotalDisplayRecords'] = $num_rows;
        return json_encode($data);
    }

    function payroll_info() {
        $id = $this->input->post('id');
        $month_input = $this->input->post('month');
        $year_input = $this->input->post('year');
        $paytype = $this->input->post('paytype');
        $payclass = $this->input->post('payclass');

        $month = ($month_input) ? $month_input : (int)date('m');
        $year = ($year_input) ? $year_input : (int)date('y');
        $data = array();
        $qry_emp = $this->db->select('e.sysid, e.empid, e.personid, e.status, p.firstname, p.lastname, p.middlename, p.gender, p.birthdate')
            ->from("prime_employee_main e")
            ->join("person p", "e.personid = p.sysid")
            ->where(array('e.sysid' => $id, 'e.status' => 1))
            ->get()->row();
        $data_details = '';

        $qry_getpayclass = $this->db->select('payclass_id AS payclassid')
            ->from('prime_employee_main_payclass')
            ->where(array('emp_id' => $id, 'status' => 1))
            ->get()->row();

        if ($qry_emp && $qry_getpayclass) {
            $empid = $qry_emp->sysid;
            $empclass = $qry_getpayclass->payclassid;
            $person_info = get_person_info($qry_emp->personid);

            if ($person_info && isset($person_info->info)) {
                $info = $person_info->info;
                $compute = compute_employee_netpay($empid, $month, $year, $paytype, $paytype, $payclass , 1);
                $data['compute'] = $compute;
                $data['add_trans_variable'] = $compute->add_trans_variable;

                if($compute) {
                    $func = 'success';

                    $data_details .= '<div class="row">';
                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    $data_details .= '<li class="list-group-item"><span class="label label-default col-md-7  col-xs-7 col-sm-7 col-lg-7">Descriptions</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">Amount</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Premiums</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->premiums, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Basic Salary</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->basic, 2) . '</span></li>';
                    if( ( is_array($compute->earningarr) || is_object($compute->earningarr))){
                        foreach ($compute->earningarr as $earningrow) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">' . $earningrow['names'] . '</span>';
                            $data_details .= '<span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default data number">' . number_format($earningrow['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }
                    if(count($compute->annualgrossarr) > 0) {
                        $data_details .= '<li class="list-group-item"><span class="label label-default col-md-5 col-xs-5 col-sm-5 col-lg-5">Annual Gross</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">Amount</span></li>';
                        foreach ($compute->annualgrossarr as $annualgrossarr) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">'.$annualgrossarr['name'].'</span>';
                            $data_details .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default data number">' . number_format($annualgrossarr['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }


                    //   $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Holiday Pay</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->total_holiday, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Earnings</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number font-green">' . number_format($compute->earnings, 2) . '</span></li>';
                    $data_details .= '</ul>';
                    $data_details .= '</div>';

                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    $data_details .= '<li class="list-group-item"><span class="label label-default col-md-7 col-xs-7 col-sm-7 col-lg-7">Descriptions</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">Amount</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Taxable Amount</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->taxableamount, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Non-Taxable Amount</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->nontaxableamount, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Other (-)</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->totalotherssub, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Other (+)</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->totalothersadd, 2) . '</span></li>';
                    $data_details .= '</ul>';
                    $data_details .= '</div>';

                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    //  if ($compute->deductions) {
                    $data_details .= '<li class="list-group-item">';
                    $data_details .= '<span class="label label-default col-md-4 col-xs-4 col-sm-4 col-lg-4">Ded</span>';
                    $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number">Employee</span>';
                    $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number">Employer</span>';
                    $data_details .= '</li>';
                    foreach ($compute->deductions as $drow) {
                        $data_details .= '<li class="list-group-item">';
                        $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">' . $drow['contname'] . '</span>';
                        $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number ' . $drow['class'] . '">' . number_format($drow['amt'], 2) . '</span>';
                        $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number ' . $drow['class'] . '">' . number_format($drow['amtcomp'], 2) . '</span>';
                        $data_details .= '</li>';
                    }
                    if($compute->otherdeductarr){
                        foreach($compute->otherdeductarr as $otherdedrow){
                            $data['otherdedarr'][] = array(
                                'name' => $otherdedrow['names'],
                                'amt' => number_format($otherdedrow['amt'], 2)
                            );
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">' . $otherdedrow['names'] . '</span>';
                            $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number  font-red-flamingo">' . number_format($otherdedrow['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }

                    $data_details .= '<hr>';
                    if($compute->loansarr){
                        foreach ($compute->loansarr as $loansrow) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">' . $loansrow['names'] . '</span>';
                            $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($loansrow['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }

                    $data_details .= '<hr>';

                    // }



                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">Total Loans</span><span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default number font-red-flamingo">' . number_format($compute->loans, 2) . '</span></li>';


                    if(count($compute->annualtaxarr)  > 0 ) {
                        $data_details .= '<li class="list-group-item"><span class="label label-default col-md-4 col-xs-4 col-sm-4 col-lg-4">Annual Tax</span><span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default number">Amount</span></li>';
                        foreach ($compute->annualtaxarr as $annualtaxarr) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">'.$annualtaxarr['name'].'</span>';
                            $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($annualtaxarr['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                        $data_details .= '<li class="list-group-item">';
                        $data_details .= '<hr>';
                        $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">Total Tax</span>';
                        $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($compute->taxamt, 2) . '</span>';
                        $data_details .= '</li>';

                    }



                    $data_details .= '<li class="list-group-item">';
                    $data_details .= '<hr>';
                    $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">Total</span>';
                    $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($compute->deductionamount, 2) . '</span>';
                    $data_details .= '</li>';


                    $data_details .= '</ul>';
                    $data_details .= '</div>';

                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    $data_details .= '<li class="list-group-item"><span class="label label-default col-md-5 col-xs-5 col-sm-5 col-lg-5">Descriptions</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">Amount</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net Pay</span><span class="col-md-7 col-xs-7 col-sm-57 col-lg-7 label-default number">' . number_format($compute->netpay, 2) . '</span></li>';


                    if($empclass == 1 || $empclass == 129 || $empclass == 130) {
                        $getenddate = $this->db->select("dateend")
                            ->from("prime_employee_main")
                            ->where(array("sysid" => $id))->get()
                            ->row();

                        // ##########################################
                        // added by: lucky john faderon 4/13/2020 ###
                        $getstatushist_active = $this->db->select()
                            ->from('prime_employee_main_history')
                            ->where(array('dataid' => $empid, 'status' => 1))
                            ->order_by('sysid', 'desc')
                            ->get()->row();
                        $inactive_emp_arr = array(3229,3230,3231,3232,3233);
                        $emp_status_active = ($getstatushist_active && !in_array($getstatushist_active->statusid, $inactive_emp_arr)) ? true : false;
                        // ###########################################

                        //if($getenddate && $emp_status_active == false && $getenddate->dateend != null) {
                        if($getenddate && $getenddate->dateend != null) {
                            $date_15 = $year_input. '-'. str_pad($month_input, 2, '0', STR_PAD_LEFT) . '-1'; // or your date as well
                            $earlier = new DateTime($getenddate->dateend);
                            $later = new DateTime($date_15);

                            $diff = $later->diff($earlier)->format("%a");
                            if($diff>15) {
                                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 15</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number"> ' . number_format($compute->net15, 2) . '</span></li>';
                                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 30</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($compute->net30, 2) . '</span></li>';
                            }else{
                                //  $new_basic = ($compute->basic/2);
                                $new_net15 = (($compute->earnings) - $compute->deductionamount);
                                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 15</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($new_net15, 2) . '</span></li>';
                            }
                        }else {
                            $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 15</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($compute->net15, 2) . '</span></li>';
                            $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 30</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($compute->net30, 2) . '</span></li>';
                        }

                        if(count($compute->annualnetarr) > 0 ) {
                            $data_details .= '<li class="list-group-item"><span class="label label-default col-md-5 col-xs-5 col-sm-5 col-lg-5">Annual Net</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">Amount</span></li>';
                            foreach ($compute->annualnetarr as $annualnetarr) {
                                $data_details .= '<li class="list-group-item">';
                                $data_details .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">'.$annualnetarr['name'].'</span>';
                                $data_details .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default data number">' . number_format($annualnetarr['amt'], 2) . '</span>';
                                $data_details .= '</li>';
                            }
                        }
                    }

                    $data_details .= '</ul>';
                    $data_details .= '</div>';
                    $data_details .= '</div>';
                }else{
                    $func = 'warning';
                    $data_details .= '<div class="col-md-12"><h4><i class="fa fa-warning"></i> Compute netpay error!</h4></div>';
                }
            } else {
                $func = 'warning';
                $data_details .= '<div class="col-md-12"><h4><i class="fa fa-warning"></i> Unable to retrive person\'s data!</h4></div>';
            }
        } else {
            $func = 'warning';
        }

        $data_details .= '';
        $data['year'] = $year;
        $data['month'] = $month;
        $data['func'] = $func;
        $data['inputs'] = $this->input->post();
        $data['html'] = $data_details;


        return json_encode($data);
    }

    function payslip_info() {
        $id = $this->input->post('id');
        $month_input = $this->input->post('month');
        $year_input = $this->input->post('year');
        $paytype = $this->input->post('paytype');
        $payclass = $this->input->post('payclass');

        $month = ($month_input) ? $month_input : (int)date('m');
        $year = ($year_input) ? $year_input : (int)date('y');
        $data = array();
        $qry_emp = $this->db->select('e.sysid, e.empid, e.personid, e.status, p.firstname, p.lastname, p.middlename, p.gender, p.birthdate')
            ->from("prime_employee_main e")
            ->join("person p", "e.personid = p.sysid")
            ->where(array('e.sysid' => $id, 'e.status' => 1))
            ->get()->row();
        $data_details = '';

        $qry_getpayclass = $this->db->select('payclass_id AS payclassid')
            ->from('prime_employee_main_payclass')
            ->where(array('emp_id' => $id, 'status' => 1))
            ->get()->row();

        if ($qry_emp && $qry_getpayclass) {
            $empid = $qry_emp->sysid;
            $empclass = $qry_getpayclass->payclassid;
            $person_info = get_person_info($qry_emp->personid);

            if ($person_info && isset($person_info->info)) {
                $info = $person_info->info;
                $compute = compute_final_employee_netpay($empid, $month, $year, $paytype, $paytype, $payclass , 1);
                $data['compute'] = $compute;
                $data['add_trans_variable'] = $compute->add_trans_variable;

                if($compute) {
                    $func = 'success';

                    $data_details .= '<div class="row">';
                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    $data_details .= '<li class="list-group-item"><span class="label label-default col-md-7  col-xs-7 col-sm-7 col-lg-7">Descriptions</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">Amount</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Premiums</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->premiums, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Basic Salary</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->basic, 2) . '</span></li>';
                    if( ( is_array($compute->earningarr) || is_object($compute->earningarr))){
                        foreach ($compute->earningarr as $earningrow) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">' . $earningrow['names'] . '</span>';
                            $data_details .= '<span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default data number">' . number_format($earningrow['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }
                    if(count($compute->annualgrossarr) > 0) {
                        $data_details .= '<li class="list-group-item"><span class="label label-default col-md-5 col-xs-5 col-sm-5 col-lg-5">Annual Gross</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">Amount</span></li>';
                        foreach ($compute->annualgrossarr as $annualgrossarr) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">'.$annualgrossarr['name'].'</span>';
                            $data_details .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default data number">' . number_format($annualgrossarr['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }


                    //   $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Holiday Pay</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->total_holiday, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Earnings</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number font-green">' . number_format($compute->earnings, 2) . '</span></li>';
                    $data_details .= '</ul>';
                    $data_details .= '</div>';

                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    $data_details .= '<li class="list-group-item"><span class="label label-default col-md-7 col-xs-7 col-sm-7 col-lg-7">Descriptions</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">Amount</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Taxable Amount</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->taxableamount, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Non-Taxable Amount</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->nontaxableamount, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Other (-)</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->totalotherssub, 2) . '</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Other (+)</span><span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number">' . number_format($compute->totalothersadd, 2) . '</span></li>';
                    $data_details .= '</ul>';
                    $data_details .= '</div>';

                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    //  if ($compute->deductions) {
                    $data_details .= '<li class="list-group-item">';
                    $data_details .= '<span class="label label-default col-md-4 col-xs-4 col-sm-4 col-lg-4">Ded</span>';
                    $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number">Employee</span>';
                    $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number">Employer</span>';
                    $data_details .= '</li>';
                    foreach ($compute->deductions as $drow) {
                        $data_details .= '<li class="list-group-item">';
                        $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">' . $drow['contname'] . '</span>';
                        $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number ' . $drow['class'] . '">' . number_format($drow['amt'], 2) . '</span>';
                        $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number ' . $drow['class'] . '">' . number_format($drow['amtcomp'], 2) . '</span>';
                        $data_details .= '</li>';
                    }
                    if($compute->otherdeductarr){
                        foreach($compute->otherdeductarr as $otherdedrow){
                            $data['otherdedarr'][] = array(
                                'name' => $otherdedrow['names'],
                                'amt' => number_format($otherdedrow['amt'], 2)
                            );
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">' . $otherdedrow['names'] . '</span>';
                            $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number  font-red-flamingo">' . number_format($otherdedrow['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }

                    $data_details .= '<hr>';
                    if($compute->loansarr){
                        foreach ($compute->loansarr as $loansrow) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">' . $loansrow['names'] . '</span>';
                            $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($loansrow['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                    }

                    $data_details .= '<hr>';

                    // }



                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">Total Loans</span><span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default number font-red-flamingo">' . number_format($compute->loans, 2) . '</span></li>';


                    if(count($compute->annualtaxarr)  > 0 ) {
                        $data_details .= '<li class="list-group-item"><span class="label label-default col-md-4 col-xs-4 col-sm-4 col-lg-4">Annual Tax</span><span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default number">Amount</span></li>';
                        foreach ($compute->annualtaxarr as $annualtaxarr) {
                            $data_details .= '<li class="list-group-item">';
                            $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">'.$annualtaxarr['name'].'</span>';
                            $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($annualtaxarr['amt'], 2) . '</span>';
                            $data_details .= '</li>';
                        }
                        $data_details .= '<li class="list-group-item">';
                        $data_details .= '<hr>';
                        $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">Total Tax</span>';
                        $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($compute->taxamt, 2) . '</span>';
                        $data_details .= '</li>';

                    }



                    $data_details .= '<li class="list-group-item">';
                    $data_details .= '<hr>';
                    $data_details .= '<span class="label label-name col-md-4 col-xs-4 col-sm-4 col-lg-4">Total</span>';
                    $data_details .= '<span class="col-md-4 col-xs-4 col-sm-4 col-lg-4 label-default data number font-red-flamingo">' . number_format($compute->deductionamount, 2) . '</span>';
                    $data_details .= '</li>';


                    $data_details .= '</ul>';
                    $data_details .= '</div>';

                    $data_details .= '<div class="col-md-3 col-xs-3 col-sm-3 col-lg-3">';
                    $data_details .= '<ul class="list-group summary column no-border list-group-sm">';
                    $data_details .= '<li class="list-group-item"><span class="label label-default col-md-5 col-xs-5 col-sm-5 col-lg-5">Descriptions</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">Amount</span></li>';
                    $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net Pay</span><span class="col-md-7 col-xs-7 col-sm-57 col-lg-7 label-default number">' . number_format($compute->netpay, 2) . '</span></li>';


                    if($empclass == 1 || $empclass == 129 || $empclass == 130) {
                        $getenddate = $this->db->select("dateend")->from("prime_employee_main")
                            ->where(array("sysid" => $id))->get()->row();
                        if($getenddate && $getenddate->dateend != null) {
                            $date_15 = $year_input. '-'. str_pad($month_input, 2, '0', STR_PAD_LEFT) . '-1'; // or your date as well
                            $earlier = new DateTime($getenddate->dateend);
                            $later = new DateTime($date_15);

                            $diff = $later->diff($earlier)->format("%a");
                            if($diff>15) {
                                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 15</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number"> ' . number_format($compute->net15, 2) . '</span></li>';
                                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 30</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($compute->net30, 2) . '</span></li>';
                            }else{
                                //  $new_basic = ($compute->basic/2);
                                $new_net15 = (($compute->earnings) - $compute->deductionamount);
                                $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 15</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($new_net15, 2) . '</span></li>';
                            }
                        }else {
                            $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 15</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($compute->net15, 2) . '</span></li>';
                            $data_details .= '<li class="list-group-item"><span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Net 30</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">' . number_format($compute->net30, 2) . '</span></li>';
                        }

                        if(count($compute->annualnetarr) > 0 ) {
                            $data_details .= '<li class="list-group-item"><span class="label label-default col-md-5 col-xs-5 col-sm-5 col-lg-5">Annual Net</span><span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default number">Amount</span></li>';
                            foreach ($compute->annualnetarr as $annualnetarr) {
                                $data_details .= '<li class="list-group-item">';
                                $data_details .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">'.$annualnetarr['name'].'</span>';
                                $data_details .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default data number">' . number_format($annualnetarr['amt'], 2) . '</span>';
                                $data_details .= '</li>';
                            }
                        }
                    }

                    $data_details .= '</ul>';
                    $data_details .= '</div>';
                    $data_details .= '</div>';
                }else{
                    $func = 'warning';
                    $data_details .= '<div class="col-md-12"><h4><i class="fa fa-warning"></i> Compute netpay error!</h4></div>';
                }
            } else {
                $func = 'warning';
                $data_details .= '<div class="col-md-12"><h4><i class="fa fa-warning"></i> Unable to retrive person\'s data!</h4></div>';
            }
        } else {
            $func = 'warning';
        }

        $data_details .= '';
        $data['year'] = $year;
        $data['month'] = $month;
        $data['func'] = $func;
        $data['inputs'] = $this->input->post();
        $data['html'] = $data_details;


        return json_encode($data);
    }

    function tax_table_info() {
        $hashcode = $this->input->post('hashcode');
        $qry = $this->db->select('*')
            ->from("prime_employee_bir")
            ->get();
        $num_rows = $qry->num_rows();
        if ($num_rows > 0) {
            foreach ($qry->result() as $row) {
                $data['data'][] = array(
                    'sysid' => ($row->sysid),
                    'type' => $row->type,
                    'lower_range' => $row->lower_range,
                    'higher_range' => $row->higher_range,
                    'base_tax' => ($row->base_tax),
                    'per_ex' => $row->per_ex,
                    'datecreated' => ($row->datecreated),
                    'status' => $row->status
                );
            }
        }
        return json_encode($data);
    }

    // begin create new function for payroll transaction model to view
    function populate_payslip() {
        $hashcode = $this->input->post('hashcode');
        $empid = $this->input->post('empid');
        $qry = $this->db->select('*')
            ->from("prime_employee_payroll_transactions")
            ->where("empid", $empid)
            ->get();
        $num_rows = $qry->num_rows();
        if ($num_rows > 0) {
            foreach ($qry->result() as $row) {
                $data['data'][] = array(
                    'sysid' => ($row->sysid),
                    'empid' => $row->empid,
                    'tdate' => $row->tdate,
                    'current_salary' => $row->current_salary,
                    'payroll_type' => ($row->payroll_type),
                    'basic' => $row->basic,
                    'cola' => ($row->cola),
                    'other_earnings' => $row->other_earnings,
                    'holidaypay' => $row->holidaypay,
                    'otpay' => $row->otpay,
                    'ndpay' => $row->ndpay,
                    'adjustment_earnings' => $row->adjustment_earnings,
                    'sss_employee' => $row->sss_employee,
                    'sss_employeer' => $row->sss_employeer,
                    'sss_loan' => $row->sss_loan,
                    'pagibig_employee' => $row->pagibig_employee,
                    'pagibig_employeer' => $row->pagibig_employeer,
                    'pagibig_loan' => $row->pagibig_loan,
                    'hmo_employee' => $row->hmo_employee,
                    'hmo_employeer' => $row->hmo_employeer,
                    'union_fees' => $row->union_fees,
                    'union_loan' => $row->union_loan,
                    'coop_fees' => $row->coop_fees,
                    'coop_loan' => $row->coop_loan,
                    'electric_bills' => $row->electric_bills,
                    'other_deductions' => $row->other_deductions,
                    'leave_withoutpay' => $row->leave_withoutpay,
                    'withholding_tax' => $row->withholding_tax,
                    'netpay' => $row->netpay,
                    'status' => $row->status
                );
            }
        }
        return json_encode($data);
    }
//  new function for insertion other deductions and earnings in payroll
    function insert_other_deduction_and_earnings($data) {
        return $this->db->insert('payroll_other_earnings_and_deductions_trn', $data);
    }

    // // end new function for insertion other deductions and earnings in payroll
    // select existing data from other earning and deduction table
    function check_data_other_deduction_and_earnings($empid, $trntype, $month, $year, $day) {
        $this->db->select('*');
        $this->db->from('payroll_other_earnings_and_deductions_trn');
        $this->db->where('empid', $empid);
        $this->db->where('transaction_type', $trntype);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where('day', $day);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //e end select for existing data from other earning and deduction
    //start function to check payroll trn if already processed
    function check_data_payroll($month, $year) {
        $this->db->select('*');
        $this->db->from('prime_employee_payroll_transactions');
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //end functio of payroll check
    // new function for generating dependents


    function generate_dependents_count() {
        $query = $this->db->query('select p.firstname, p.lastname, ecount.count from prime_employee_main as e left join person as p on p.sysid = e.personid left join prime_employee_dependents_count as ecount on ecount.empid = e.sysid');
        return $query;
    }

    function send_payslips() {
        $data = array();
        $msg = '';
        $num = $this->input->post('num');
        $sysid = $this->input->post('sysid');
        $payclass = $this->input->post('payclass');
        $months = $this->input->post('months');
        $years = $this->input->post('years');
        $paytype = $this->input->post('paytype');
        $non_confi = array(128,3077,3078);
        if($paytype == 1){
            $nettype = 0;
        }else if($paytype == 2){
            $nettype = 1;
        }
        $per = 0;
        $end = false;
        $ins_arr = '';

        $emp_arr = false;

        if($num==0) {
            $this->db->where('em.sysid > ', 0);
        }else{
            $this->db->where('em.sysid < ', $sysid);
        }

        if(in_array($payclass,$non_confi)) {
            $this->db->where('pemp.payclass_id', $payclass);
        }else{
            $this->db->where_not_in('pemp.payclass_id', $non_confi);
        }

        $qry_emp = $this->db->select('em.sysid')
            ->from('prime_employee_main AS em')
            ->join('prime_employee_main_payclass as pemp', 'pemp.emp_id = em.sysid')
            ->join('payroll_emplist AS pe', 'pe.empid = em.sysid')
            ->where(array('em.status' => 1))
            ->order_by('em.sysid', 'desc')
            ->get()->row();

        $data['qry_emp'] = $this->db->last_query();
        $data['emp'] = $qry_emp;
        if(in_array($payclass,$non_confi)) {
            $this->db->where('pemp.payclass_id', $payclass);
        }else{
            $this->db->where_not_in('pemp.payclass_id', $non_confi);
        }

        $qry_emp_cnt = $this->db->select('COUNT(em.sysid) AS cnt')
            ->from('prime_employee_main AS em')
            ->join('prime_employee_main_payclass as pemp', 'pemp.emp_id = em.sysid')
            ->join('payroll_emplist AS pe', 'pe.empid = em.sysid')
            ->where(array('em.status' => 1))
            ->get()->row();
        $data['qry_emp_cnt'] = $this->db->last_query();
        $data['emp_cnt'] = $qry_emp_cnt;

        if($qry_emp) {
            $emp_arr = get_employee_info($qry_emp->sysid);

            // SENDING EMAIL
            if(in_array($payclass,$non_confi)){
                $this->db->where(array("paytype" => $paytype));
            }

            $check_exists = $this->db->select()
                ->from('payroll_reports_group')
                ->where(array(
                    'status' => 301 ,
                    'payclass' => $payclass,
                    'years' => $years,
                    'months' => $months
                ))->get()->row();

            $data['check_exists'] = $check_exists;
            $data['check_exists_qry'] = $this->db->last_query();
            //print_r($this->db->last_query());

            if($check_exists) {
                //GET EMPLOYEE PAY INFO
                $row = $this->db->query("
                    SELECT pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass
                    FROM payroll_reports_main as prm
                    LEFT JOIN payroll_emplist as payrollemp ON payrollemp.empid = prm.empid
                    LEFT JOIN payroll_reports_trn as prt ON prt.payrollid = prm.sysid
                    LEFT JOIN payroll_reports_group as prg ON prg.sysid = prm.groupid
                    LEFT JOIN prime_employee_main as pem ON pem.sysid = prm.empid
                    LEFT JOIN person as p ON p.sysid = pem.personid
                    LEFT JOIN prime_employee_costcenter as pec ON pec.empid = pem.sysid
                    LEFT JOIN prime_costcenter_main as pcm ON pcm.sysid = pec.ccid
                    LEFT JOIN prime_employee_main_payclass as pemp ON pemp.emp_id = pem.sysid
                    WHERE prg.sysid = {$check_exists->sysid}
                    AND pec.type = 1
                    AND pec.`status` = 1
                    AND pem.`status` = 1
                    AND pem.sysid = {$emp_arr->sysid}
                    GROUP BY pem.sysid,payrollemp.accntno,prg.months,prg.years , pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net, prg.paytype , prg.payclass
                    ORDER BY pem.sysid
                    LIMIT 1
                ")->row();
                if($row) {
                    $filename = '';
                    if(in_array($payclass,$non_confi)) {
                        $this->db->where(array("nettype" => $nettype));
                    }
                    //CHECK IF EMAIL HAS BEEN SENT
                    $check_mailsent = $this->db->select('empid')
                        ->from('payroll_transactions_bankfile')
                        ->where(
                            array(
                                'empid' => $row->sysid,
                                'years' => $years,
                                'months' => $months,
                                'mailsent' => 1,
                                'status' => 1
                            )
                        )
                        ->get()->row();

                    //IF RETURNS NO RESULT
                    if($check_mailsent == false) {


                        $form_payslip = form_payslip_single($row->sysid, $months, $years, $row->paytype, $payclass, true);

                        $html = '';
                        $html .= '<html>';
                        $html .= '<head>';
                        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
                        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
                        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
                        $html .= '</head>';
                        $html .= '<body>';
                        $html .= $form_payslip->html;
                        $html .= '</body>';
                        $html .= '</html>';


                        if(in_array($payclass, array(128, 3077, 3078))){
                            if($check_exists->paytype == 1){
                                $payslip_term_text = '<u>first half of the month</u>';
                                $filename = $years.'-'.strtoupper(date_formating($months, '!m', 'M')).'-'.str_pad($check_exists->paytype,2,"0" , STR_PAD_LEFT).'_RANKF.pdf';
                            }else{
                                $payslip_term_text = '<u>second half of the month</u>';
                                $filename = $years.'-'.strtoupper(date_formating($months, '!m', 'M')).'-'.str_pad($check_exists->paytype,2,"0" , STR_PAD_LEFT).'_RANKF.pdf';
                            }
                        }else{
                            $payslip_term_text = '';
                            $filename = $years.'-'.strtoupper(date_formating($months, '!m', 'M')).'_CONFI.pdf';
                        }



                        $upload_path = FCPATH.'uploads/employee/payslips/' .$row->sysid . '/';

                        /*
                        $data['file'] = $upload_path  . '/' . $years . '/' . $months . '/' . $filename;
                        return json_encode($data);
                        exit();
                        */

                        if(!file_exists($upload_path  . '/' . $years . '/' . $months . '/' . $filename)) {

                            // Load library
                            $this->load->library('pdf');

                            $dompdf = new Dompdf\Dompdf();

                            $dompdf->loadHtml($html);



                            $customPaper = array(0, 0, 610, 205);
                            $dompdf->setPaper($customPaper, 'portrate');
                            $dompdf->render();
                            // Add PDF Document Information
                            $dompdf->add_info('Subject', 'PECO PAYSLIP | ' . $filename);
                            $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
                            $dompdf->add_info('Creator', 'ITD');
                            $dompdf->add_info('Keywords', 'Payslip');

                            $output = $dompdf->output();
                            $data['output'] = $output;

                            if (!is_dir($upload_path)) {
                                mkdir($upload_path, 0777, true);
                                $year_path = $upload_path . $years;
                                if (!is_dir($year_path)) {
                                    mkdir($upload_path . '/' . $years, 0777, true);
                                    $month_path = $year_path . '/' . $months . '/';
                                    if (!is_dir($month_path)) {
                                        mkdir($year_path . '/' . $months, 0777, true);
                                    } else {
                                        chmod($year_path . '/' . $months, 0777);
                                    }
                                    file_put_contents($month_path . $filename, $output);
                                } else {
                                    $month_path = $year_path . '/' . $months . '/';
                                    if (!is_dir($month_path)) {
                                        mkdir($year_path . '/' . $months, 0777, true);
                                    } else {
                                        chmod($year_path . '/' . $months, 0777);
                                    }

                                    file_put_contents($month_path . $filename, $output);
                                }
                            } else {
                                $year_path = $upload_path . $years;
                                if (!is_dir($year_path)) {
                                    mkdir($upload_path . '/' . $years, 0777, true);
                                    $month_path = $year_path . '/' . $months . '/';
                                    if (!is_dir($month_path)) {
                                        mkdir($year_path . '/' . $months, 0777, true);
                                    } else {
                                        chmod($year_path . '/' . $months, 0777);
                                    }

                                    file_put_contents($month_path . $filename, $output);
                                } else {
                                    $month_path = $year_path . '/' . $months . '/';
                                    if (!is_dir($month_path)) {
                                        mkdir($year_path . '/' . $months, 0777, true);
                                    } else {
                                        chmod($year_path . '/' . $months, 0777);
                                    }

                                    file_put_contents($month_path . $filename, $output);
                                }
                            }
                        }


                        $emp_arr = get_employee_info($row->sysid);

                        if($emp_arr && $emp_arr->qry == true) {
                            $qry_contacts = $this->db->select('contactstring')
                                ->from('person_contact_matrix')
                                ->where(array(
                                    'types' => 1057,
                                    'personid' => $emp_arr->personid,
                                    'status' => 1,
                                ))
                                ->get()->row();

                            if($qry_contacts) {
                                $email = $qry_contacts->contactstring;


                                //SMTP & mail configuration
                                $this->load->library('email');
                                $config = array(
                                    'protocol' => 'smtp',
                                    'smtp_host' => 'ssl://smtp.googlemail.com',
                                    'smtp_port' => 465,
                                    'smtp_user' => 'noreply.peco@gmail.com',
                                    'smtp_pass' => 'P3C02019',
                                    'mailtype' => 'html',
                                    'charset' => 'utf-8'
                                );

                                $this->email->initialize($config);
                                $this->email->set_mailtype("html");
                                $this->email->set_newline("\r\n");


                                $content = '';
                                $content .= '<html>';
                                $content .= '<head>';
                                $content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
                                $content .= '<title>TVI | Tiedra Ventures, Inc.</title>';
                                $content .= '<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" />';
                                $content .= '
                                        <style type="text/css">
                                        html { 
                                            -webkit-text-size-adjust:none; 
                                            -ms-text-size-adjust: none;
                                        }
                                        body{
                                            font-family: Arial !important;
                                            overflow: visible !important;
                                        }		
                                        </style>
                                        ';
                                $content .= '</head>';
                                $content .= '<body>';

                                $content .= '<div class="container" style="width: 95%; display:inline-block; margin-top: 5px; margin-bottom: 5px; padding: 20px 20px;">';
                                $content .= '<div class="col-xs-12">';

                                $content .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
                                $content .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
                                $content .= '</div>';
                                $content .= '<h4>Hi, '.$emp_arr->firstname.'!</h4>';
                                $content .= '<br>';

                                $month_code = date_formating($months, '!m', 'M');

                                $content .= '<p>Your payslip for the month of <b>'.$month_code.'</b>, year <b>'.$years.'</b> '.$payslip_term_text.' is now available.</p>';
                                $content .= '<p>Please see attached file(s).</p>';
                                $content .= '<br>';
                                $content .= '<p>Thank you,</p>';
                                $content .= '<br>';

                                $content .= '<div style="color: #FFF; font-size: 9px; display: inline-block; background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; padding: 10px 20px;">';
                                $content .= 'This is a system generated message, please do not reply';
                                $content .= '</div>';

                                $content .= '</div>';
                                $content .= '</div>';
                                $content .= '</body>';
                                $content .= '</html>';

                                //Email content
                                $this->email->to($email);
                                $this->email->from('no-reply@paenergy.ph', 'PAE PAYSLIP | ' . strtoupper($month_code) . '-' . $years);
                                $this->email->subject('PAE PAYSLIP | ' . strtoupper($month_code) . '-' . $years);
                                $this->email->message($content);
                                $this->email->attach($upload_path . '/' . $years . '/' . $months . '/' . $filename);

                                // $sent = true;
                                // Send email
                                /*$sent = $this->email->send();
                                $this->email->clear(true);
                                if ($sent) {
                                    $this->db->where(array('empid' => $row->sysid, 'years' => $years, 'months' => $months , 'mailsent' => 0));
                                    $this->db->update('payroll_transactions_bankfile', array('mailsent' => 1));
                                }*/
                            } else {
                                $msg .= 'Employee has no registered email!';
                                $end = false;
                            }
                        }

                    } else {
                        $msg .= 'Already sent!';
                    }
                }
            }else{
                $msg .= 'payroll_reports_group';
            }

        }else{
            $emp_arr = false;
        }
        $data['err'] = array('num' => $sysid, 'msg' => $msg);

        $total_cnt = $qry_emp_cnt->cnt;
        $percent_ind = $num / $total_cnt;
        if ($qry_emp) {
            $num = $num + 1;
            $sysid = $qry_emp->sysid;
            $per = ($percent_ind * 100);
            $end = false;
        } else {
            $end = true;
            $per = 100;
        }

        $data['empname'] = ($emp_arr) ? $emp_arr->lastname.', '.$emp_arr->firstname : 'Done!';


        $data['insarr'] = $emp_arr;
        $data['end'] = $end;
        $data['per'] = round($per, 2);
        $data['sysid'] = $sysid;
        $data['num'] = $num;

        return json_encode($data);
    }

    function email_payslip() {
        $data = array();

        $empid = $this->input->post('empid');
        $payclass = $this->input->post('payclass');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $period = $this->input->post('period');

        $emp_arr = get_employee_info($empid);
        if(in_array($payclass, array(128, 3077, 3078))){
            if($period == 1){
                $message = '<p>Your payslip for the <u>first half</u> of <b>' . date_formating($month, '!m', 'F') . '</b>, <b>' . $year . '</b> is now available.</p>';
                $filename = $year.'-'.strtoupper(date_formating($month, '!m', 'M')).'-'.str_pad($period,2,"0" , STR_PAD_LEFT).'_RANKF.pdf';
            }else{
                $message = '<p>Your payslip for the <u>second half</u> of <b>' . date_formating($month, '!m', 'F') . '</b>, <b>' . $year . '</b> is now available.</p>';
                $filename = $year.'-'.strtoupper(date_formating($month, '!m', 'M')).'-'.str_pad($period,2,"0" , STR_PAD_LEFT).'_RANKF.pdf';
            }
        }else{
            $message = '<p>Your payslip for the month of <b>' . date_formating($month, '!m', 'F') . '</b>, <b>' . $year . '</b> is now available.</p>';
            $filename = $year.'-'.strtoupper(date_formating($month, '!m', 'M')).'_CONFI.pdf';
        }

        $nettype = $period - 1;
        $qry = false;
        $email = '';

        //return json_encode($data);

        //exit();

        // query from bank file count sent = 1
        $sent_items = $this->db->select('COUNT(sysid) as cnt')
            ->from('payroll_transactions_bankfile')
            ->where(array(
                'payclass' => $payclass,
                'years' => $year,
                'months' => $month,
                'nettype' => $nettype,
                'mailsent' => 1,
                'status' => 1
            ))
            ->get()->row();
        $sent = $sent_items->cnt;

        //get payslip PDF data
        $check_mailsent = $this->db->select('empid')
            ->from('payroll_transactions_bankfile')
            ->where(
                array(
                    'empid' => $empid,
                    'years' => $year,
                    'months' => $month,
                    'nettype' => $nettype,
                    'mailsent' => 1,
                    'status' => 1
                )
            )
            ->get()->row();
        //$data['check_mailsent'] = $this->db->last_query();
        //return json_encode($data);
        //exit();

        if ($check_mailsent == false) {
            //$payslip_data = $this->model_reports->pdf_monthly_payslip($payclass, $year, $month , $period, $empid);
            $payslip_data = form_payslip_single($empid, $month, $year, $period, $payclass, true);

            $html = '';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
            $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
            $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
            $html .= '</head>';
            $html .= '<body>';
            $html .= $payslip_data->html;
            $html .= '</body>';
            $html .= '</html>';

            $upload_path = FCPATH.'uploads/employee/payslips/' .$empid . '/';

            /*
            $data['file'] = $upload_path  . '/' . $years . '/' . $months . '/' . $filename;
            return json_encode($data);
            exit();
            */

            if(!file_exists($upload_path  . '/' . $year . '/' . $month . '/' . $filename)) {

                // Load library
                $this->load->library('pdf');
                $dompdf = new Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $customPaper = array(0, 0, 610, 910);
                $dompdf->setPaper($customPaper, 'portrate');
                $dompdf->render();
                // Add PDF Document Information
                $dompdf->add_info('Subject', 'PECO PAYSLIP | ' . $filename);
                $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
                $dompdf->add_info('Creator', 'ITD');
                $dompdf->add_info('Keywords', 'Payslip');

                $output = $dompdf->output();
                $data['output'] = $output;

                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                    $year_path = $upload_path . $year;
                    if (!is_dir($year_path)) {
                        mkdir($upload_path . '/' . $year, 0777, true);
                        $month_path = $year_path . '/' . $month . '/';
                        if (!is_dir($month_path)) {
                            mkdir($year_path . '/' . $month, 0777, true);
                        } else {
                            chmod($year_path . '/' . $month, 0777);
                        }
                        file_put_contents($month_path . $filename, $output);
                    } else {
                        $month_path = $year_path . '/' . $month . '/';
                        if (!is_dir($month_path)) {
                            mkdir($year_path . '/' . $month, 0777, true);
                        } else {
                            chmod($year_path . '/' . $month, 0777);
                        }

                        file_put_contents($month_path . $filename, $output);
                    }
                } else {
                    $year_path = $upload_path . $year;
                    if (!is_dir($year_path)) {
                        mkdir($upload_path . '/' . $year, 0777, true);
                        $month_path = $year_path . '/' . $month . '/';
                        if (!is_dir($month_path)) {
                            mkdir($year_path . '/' . $month, 0777, true);
                        } else {
                            chmod($year_path . '/' . $month, 0777);
                        }

                        file_put_contents($month_path . $filename, $output);
                    } else {
                        $month_path = $year_path . '/' . $month . '/';
                        if (!is_dir($month_path)) {
                            mkdir($year_path . '/' . $month, 0777, true);
                        } else {
                            chmod($year_path . '/' . $month, 0777);
                        }

                        file_put_contents($month_path . $filename, $output);
                    }
                }
            }


            if($emp_arr && $emp_arr->qry == true) {
                $qry_contacts = $this->db->select('contactstring')
                    ->from('person_contact_matrix')
                    ->where(array(
                        'types' => 1057,
                        'personid' => $emp_arr->personid,
                        'status' => 1,
                    ))
                    ->get()->row();

                if($qry_contacts) {
                    $email = $qry_contacts->contactstring;


                    //SMTP & mail configuration
                    $this->load->library('email');
                    $config = array(
                        //'protocol' => 'smtp',
                        //'smtp_host' => 'ssl://smtp.googlemail.com',
                        //'smtp_port' => 465,
                        //'smtp_user' => 'noreply.peco@gmail.com',
                        //'smtp_pass' => 'P3C02019',
                        'mailtype' => 'html',
                        'charset' => 'utf-8'
                    );

                    $this->email->initialize($config);
                    $this->email->set_mailtype("html");
                    $this->email->set_newline("\r\n");


                    $content = '';
                    $content .= '<html>';
                    $content .= '<head>';
                    $content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
                    $content .= '<title>PAE | Panay Alternative Energy, Inc.</title>';
                    $content .= '<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" />';
                    $content .= '
                                    <style type="text/css">
                                    html {
                                        -webkit-text-size-adjust:none;
                                        -ms-text-size-adjust: none;
                                    }
                                    body{
                                        font-family: Arial !important;
                                        overflow: visible !important;
                                    }
                                    </style>
                                    ';
                    $content .= '</head>';
                    $content .= '<body>';

                    $content .= '<div class="container" style="width: 95%; display:inline-block; margin-top: 5px; margin-bottom: 5px; padding: 20px 20px;">';
                    $content .= '<div class="col-xs-12">';

                    $content .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
                    //$content .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
                    $content .= '</div>';
                    $content .= '<h4>Hi, ' . $emp_arr->firstname . '!</h4>';
                    $content .= '<br>';

                    $month_code = date_formating($month, '!m', 'M');

                    $content .= $message;
                    $content .= '<p>Please see attached file(s).</p>';
                    $content .= '<br>';
                    $content .= '<p>Thank you,</p>';
                    $content .= '<br>';

                    $content .= '<div style="color: #FFF; font-size: 9px; display: inline-block; background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; padding: 10px 20px;">';
                    $content .= 'This is a system generated message, please do not reply';
                    $content .= '</div>';

                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</body>';
                    $content .= '</html>';

                    //Email content
                    $this->email->to($email);
                    $this->email->from('no-reply@panayelectric.com', 'PECO PAYSLIP | ' . strtoupper($month_code) . '-' . $year);
                    $this->email->subject('PECO PAYSLIP | ' . strtoupper($month_code) . '-' . $year);
                    $this->email->message($content);
                    $this->email->attach($upload_path . '/' . $year . '/' . $month . '/' . $filename);

                    // $sent = true;
                    // Send email
                    $sent = $this->email->send();
                    $this->email->clear(true);
                    if ($sent) {
                        $this->db->where(array('empid' => $empid, 'years' => $year, 'months' => $month, 'nettype' => $nettype, 'mailsent' => 0));
                        $this->db->update('payroll_transactions_bankfile', array('mailsent' => 1));
                        $qry = true;
                        $msg = '';
                        $title = '';
                    }
                } else {
                    $msg = $emp_arr->firstname.' has no registered email address.';
                    $title = 'No Email!';
                }
            }
        } else {
            $title = 'Already Sent!';
            $msg = 'Payslip already sent to '.$emp_arr->firstname.'\'s email.';
        }

        $data['msg'] = $msg;
        $data['title'] = $title;
        $data['emailsent'] = $check_mailsent;
        $data['email'] = $email;
        $data['qry'] = $qry;
        $data['sent'] = $sent;
        $data['inputs'] = $this->input->post();
        //echo '<pre>';
        //print_r($data);
        return json_encode($data);
    }

    function get_payroll_register_data($dataid = false, $payrollyear = false, $payrollmonth = false, $payrollpayclass = false, $payrollpaytype = false){
        $data = array();

        if($payrollyear == false && $payrollmonth == false && $payrollpayclass == false && $payrollpaytype == false) {
            $dataid = $this->input->post('dataid');
            $payrollyear = $this->input->post('payrollyear');
            $payrollmonth = $this->input->post('payrollmonth');
            $payrollpayclass = $this->input->post('payrollpayclass');
            $payrollpaytype = $this->input->post('payrollpaytype');
        }

        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass, "paytype" => $payrollpaytype, "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ? $sql->sysid : $dataid;

        $data['dataid'] = $groupid;

        $count  = 0;
        $header = '';


        $resultdeptgrossearnings = 0;
        $resultdepttotaldedn = 0;
        $resultdeptnet = 0;
        $resultdeptssscont = 0;
        $resultdeptsssloan = 0;
        $resultdepthdmfcont = 0;
        $resultdepthdmfloan = 0;
        $resultdeptpecewaloan = 0;
        $resultdeptcooploan = 0;
        $resultdeptpagibigadd = 0;
        $resultdeptotherdeduction = 0;
        $resultdepthmodedn = 0;
        $resultdeptdeda = 0;
        $resultdeptelectricbill = 0;
        $resultdeptdeda = 0;
        $resultdeptmemins = 0;
        $resultdeptlwop = 0;
        $resultdeptbasetax = 0;

        $paytype = 0;
        $payclass = 0;
        $getpaypaytype = $this->db->select("payclass,paytype")->from("payroll_reports_group")
            ->where(array("sysid" => $groupid))->get()->row();
        $paytype = ($getpaypaytype) ? $getpaypaytype->paytype : $payrollpaytype;
        $payclass = ($getpaypaytype) ? $getpaypaytype->payclass : $payrollpayclass;

        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;


        $sql = $this->db->select("prm.ccid , pcm.codes ")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_costcenter as pec", "pec.ccid = prm.ccid", "left")
            ->join("prime_employee_main as pem", "pem.sysid = pec.empid", "left")
            ->join("prime_costcenter_main as pcm", "pcm.sysid = prm.ccid", "left")
            ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
            ->where(array("pcm.status" => 1, "pcm.codes !=" => 0, "prm.groupid" => $groupid, "pec.type" => 1))
            ->group_by("prm.ccid , pcm.codes")
            ->order_by("pcm.codes", "ASC")
            ->get();

        if ($sql->num_rows() == 0) {
            return array('datacount' => 0);
        }


        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $count++;
                $totals = get_per_ccid_amts($row->ccid, $groupid);
                $data['totals'][] = $totals;


                $data['payrollregisterdata'][] = array(
                    "expand" => $row->ccid,
                    "deptcode" => $row->codes,
                    "grossearnings"=> number_format($totals->totalearnings,2, '.', ''),
                    "totaldedn"=> number_format($totals->totaldepttotaldedn,2, '.', ''),
                    "totalnet"=> number_format($totals->totalnet, 2, '.', ''),
                    "ssscont"=> number_format($totals->sumdeptssscont,2),
                    "sssloan"=> number_format($totals->sumdeptsssloan,2),
                    "hdmfcont"=> number_format($totals->sumdepthdmfcont,2),
                    "hdmfloan"=> number_format($totals->sumdepthdmfloan,2),
                    "pecewaloan"=> number_format($totals->sumdeptpecewaloan,2),
                    "cooploan"=> number_format($totals->sumdeptcooploan,2),
                    "pagibigadd"=> number_format($totals->sumdeptpagibigad,2),
                    "otherdeductions"=> number_format($totals->sumdeptotherdedn,2),
                    "hmodedn"=> number_format($totals->sumdepthmodedn,2),
                    "deda"=> number_format($totals->sumdeptdeda,2),
                    "electricbill"=> number_format($totals->sumdeptelectbill,2),
                    "memins"=> number_format($totals->sumdeptmemins,2),
                    "lwop"=> number_format($totals->sumdeptlwop,2),
                    "basetax"=> number_format($totals->sumdeptbasetax,2),
                );


                $resultdeptgrossearnings += $totals->totalearnings;
                $resultdeptnet += $totals->totalnet;
                $resultdepttotaldedn  +=  $totals->totaldepttotaldedn;
                $resultdeptssscont += $totals->sumdeptssscont;
                $resultdeptsssloan += $totals->sumdeptsssloan;
                $resultdepthdmfcont += $totals->sumdepthdmfcont;
                $resultdepthdmfloan += $totals->sumdepthdmfloan;
                $resultdeptpecewaloan += $totals->sumdeptpecewaloan;
                $resultdeptcooploan += $totals->sumdeptcooploan;
                $resultdeptpagibigadd += $totals->sumdeptpagibigad;
                $resultdeptotherdeduction += $totals->sumdeptotherdedn;
                $resultdepthmodedn += $totals->sumdepthmodedn;
                $resultdeptdeda += $totals->sumdeptdeda;
                $resultdeptelectricbill +=$totals->sumdeptelectbill;
                $resultdeptmemins += $totals->sumdeptmemins;
                $resultdeptlwop += $totals->sumdeptlwop;
                $resultdeptbasetax += $totals->sumdeptbasetax;

            }
        }



        if($payclass == 128){
            $rep_type_code = 'RF';
            $rep_type = 'RANK AND FILE PAYROLL';
        }else if ($payclass == 3078){
            $rep_type_code = 'T2';
            $rep_type = 'TIER 2 PAYROLL';
        }else if ($payclass == 3077){
            $rep_type_code = 'T1';
            $rep_type = 'TIER 1 PAYROLL';
        }else{
            $rep_type_code = 'C';
            $rep_type = 'CONFIDENTIAL PAYROLL';
        }

        $rep_title = 'PAYROLL REGISTER';

        $header .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);
        $header .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">'.$rep_type.'</b></div>';
        $header .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: '.date("m/d/Y").'</div>';
        $header .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';


        $data['header'] = $header;
        $data['datacount'] = $count;
        $data["resultdeptgrossearnings"] = number_format($resultdeptgrossearnings,2);
        $data["resultdepttotaldedn"] = number_format($resultdepttotaldedn,2);
        $data["resultdeptnet"] = number_format($resultdeptnet,2);
        $data["resultdeptssscont"] = number_format($resultdeptssscont,2);
        $data["resultdeptsssloan"] = number_format($resultdeptsssloan,2);
        $data["resultdepthdmfcont"] = number_format($resultdepthdmfcont,2);
        $data["resultdepthdmfloan"] = number_format($resultdepthdmfloan,2);
        $data["resultdeptpecewaloan"] = number_format($resultdeptpecewaloan,2);
        $data["resultdeptcooploan"] = number_format($resultdeptcooploan,2);
        $data["resultpagibigadd"] = number_format($resultdeptpagibigadd,2);
        $data["resultotherdeduction"] = number_format($resultdeptotherdeduction,2);
        $data["resultdepthmodedn"] = number_format($resultdepthmodedn,2);
        $data["resultdeptdeda"] = number_format($resultdeptdeda,2);
        $data["resultdeptelectricbill"] = number_format($resultdeptelectricbill,2);
        $data["resultdeptmemins"] = number_format($resultdeptmemins,2);
        $data["resultdeptlwop"] = number_format($resultdeptlwop,2);
        $data["resultdeptbasetax"] = number_format($resultdeptbasetax,2);

        return $data;
    }

    function get_earnings_report($dataid = false, $payrollyear = false, $payrollmonth = false, $payrollpayclass = false, $payrollpaytype = false){
        $data = array();
        $count = 0;

        if($payrollyear == false && $payrollmonth == false && $payrollpayclass == false && $payrollpaytype == false) {
            $dataid = $this->input->post('dataid');
            $payrollyear = $this->input->post('payrollyear');
            $payrollmonth = $this->input->post('payrollmonth');
            $payrollpayclass = $this->input->post('payrollpayclass');
            $payrollpaytype = $this->input->post('payrollpaytype');
        }

        if(in_array($payrollpayclass,array(128,3077))){
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass , "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ?  $sql->sysid : $dataid;
        $group_meta = $this->db->select("years, months, paytype")->from("payroll_reports_group")->where(array("sysid" => $groupid))->get()->row();
        $group_year = ($group_meta) ? (int)$group_meta->years : (int)$payrollyear;
        $group_month = ($group_meta) ? (int)$group_meta->months : (int)$payrollmonth;
        $group_paytype = ($group_meta) ? (int)$group_meta->paytype : (int)$payrollpaytype;

        $sumdeptbasicrate = 0;
        $payclass= '';

        $totalbasicrate = 0;
        $totalcola = 0;
        $totaltransallw = 0;
        $totalricesubsi = 0;
        $totalholidaypay = 0;
        $totalnitediff =0;
        $totalotpay = 0;
        $totalactingallw = 0;
        $totalotheradd = 0;

        $sumdeptbasicrate = 0;
        $sumdeptcola = 0;
        $sumdepttransallw = 0;
        $sumdeptricesubsi = 0;
        $sumdeptholidaypay = 0;
        $sumdeptnitediff =0;
        $sumdeptotpay = 0;
        $sumdeptactingallw = 0;
        $sumdeptotheradd = 0;
        $sumdeptbonus= 0;
        $fetchotpayweekend = 0;
        $fetchotpayweekdays = 0;


        $sql = $this->db->select("prm.ccid , pcm.codes")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_costcenter as pec" , "pec.ccid =prm.ccid" , "left")
            ->join("prime_employee_main as pem" , "pem.sysid = pec.empid" , "left")
            ->join("prime_costcenter_main as pcm" , "pcm.sysid = prm.ccid" , "left")
            ->join("prime_employee_main_payclass as pemp" ,"pemp.emp_id  = prm.empid" , "left")
            ->where(array("pcm.status" => 1 , "pcm.codes !=" => 0, "prm.groupid" => $groupid , "pec.type" => 1 , "pec.status" => 1))
            ->order_by("pcm.codes" , "ASC")
            ->group_by("prm.ccid , pcm.codes")
            ->get();

        if($sql->num_rows() > 0){
            $count = 0;
            foreach ($sql->result() as $row){
                $count++;
                $fetchearningregistersum = $this->db->select("prm.empid , pemp.payclass_id ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
                    ->from("payroll_reports_main as prm")
                    ->join("prime_employee_costcenter as pec" , "pec.empid = prm.empid" , "left")
                    ->join("prime_employee_main as pem" , "pem.sysid = prm.empid" , "left")
                    ->join("prime_employee_main_payclass as pemp" ,"pemp.emp_id  = prm.empid" , "left")
                    ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                    ->join("person as p" , "p.sysid = pem.personid" , "left")
                    ->where(array("pec.ccid" => $row->ccid , "pem.status" => 1  , "pec.type" => 1, "prm.groupid" => $groupid , "pec.status" => 1))
                    ->order_by("p.lastname" , "asc")
                    ->group_by("prm.empid  , pemp.payclass_id ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
                    ->get();

                if($fetchearningregistersum->num_rows()>0){
                    foreach ($fetchearningregistersum->result() as $totals){
                        $payclass= $totals->payclass_id;
                        $sumdeptbasicrate += $totals->basic;
                    }
                }

                $getpayrollid = $this->db->select("sysid")->from("payroll_reports_main")
                    ->where(array("groupid" => $groupid , "ccid" => $row->ccid))
                    ->get();
                if($getpayrollid->num_rows() > 0){
                    foreach ($getpayrollid->result() as $payrollid){


                        $fetchcolatotal = $this->db->select("SUM(prt.amt) AS colatotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 251, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $sumdeptcola += ($fetchcolatotal) ? $fetchcolatotal->colatotal : 0;

                        $fetchtransallwtotal = $this->db->select("SUM(prt.amt) AS transallwtotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 252, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $sumdepttransallw += ($fetchtransallwtotal) ? $fetchtransallwtotal->transallwtotal : 0;

                        $fetchricesubsitotal = $this->db->select("SUM(prt.amt) AS ricesubsitotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 253, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $sumdeptricesubsi += ($fetchricesubsitotal) ? $fetchricesubsitotal->ricesubsitotal : 0;

                        $fetchholidaypay = $this->db->select("SUM(prt.amt) AS holidaypaytotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 263, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $holiday_amt = ($fetchholidaypay && $fetchholidaypay->holidaypaytotal !== null) ? (float)$fetchholidaypay->holidaypaytotal : 0;
                        if($holiday_amt == 0){
                            $prm_emp = $this->db->select("empid")->from("payroll_reports_main")->where(array("sysid" => $payrollid->sysid))->get()->row();
                            if($prm_emp){
                                $pt_select = ($group_paytype != 0) ? "SUM(CASE WHEN paytype = 0 THEN amt/2 ELSE amt END) AS holidaypaytotal" : "SUM(amt) AS holidaypaytotal";
                                $pt_holiday = $this->db->select($pt_select, false)->from("payroll_transactions")
                                    ->where(array("empid" => $prm_emp->empid, "months" => $group_month, "years" => $group_year, "typesid" => 263))
                                    ->where_not_in("status", array(0, 302))
                                    ->where_in("paytype", array(0, $group_paytype))
                                    ->get()->row();
                                $holiday_amt = ($pt_holiday && $pt_holiday->holidaypaytotal !== null) ? (float)$pt_holiday->holidaypaytotal : 0;
                                if($holiday_amt == 0){
                                    $pmtb_select = ($group_paytype != 0) ? "SUM(CASE WHEN pmtb.paytype = 0 THEN pmtb.amount/2 ELSE pmtb.amount END) AS holidaypaytotal" : "SUM(pmtb.amount) AS holidaypaytotal";
                                    $pmtb_holiday = $this->db->select($pmtb_select, false)->from("payroll_manual_transactions_breakdown as pmtb")
                                        ->join("payroll_manual_transactions as pmt", "pmt.sysid = pmtb.groupid", "left")
                                        ->where(array("pmtb.empid" => $prm_emp->empid, "pmtb.month" => $group_month, "pmtb.year" => $group_year, "pmt.tsysid" => 263))
                                        ->where_in("pmtb.status", array(312, 313))
                                        ->where_in("pmtb.paytype", array(0, $group_paytype))
                                        ->get()->row();
                                    $holiday_amt = ($pmtb_holiday && $pmtb_holiday->holidaypaytotal !== null) ? (float)$pmtb_holiday->holidaypaytotal : 0;
                                }
                            }
                        }
                        $sumdeptholidaypay += $holiday_amt;


                        $fetchnitediff = $this->db->select("SUM(prt.amt) AS nitedifftotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 358, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $sumdeptnitediff += ($fetchnitediff) ? $fetchnitediff->nitedifftotal : 0;

                        $fetchotpaywithholiday = $this->db->select("SUM(prt.amt) AS otpaytotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 3010, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();

                        $fetchotpayweekend = $this->db->select("SUM(prt.amt) AS otpaytotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 1082, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();

                        $fetchotpayweekdays = $this->db->select("SUM(prt.amt) AS otpaytotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 359, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $weekdays = ($fetchotpayweekdays) ? $fetchotpayweekdays->otpaytotal : 0;
                        $weekend = ($fetchotpayweekend) ? $fetchotpayweekend->otpaytotal : 0;
                        $withholiday = ($fetchotpaywithholiday) ? $fetchotpaywithholiday->otpaytotal : 0;
                        $sumdeptotpay +=$weekdays + $weekend + $withholiday;

                        $fetchactingallw = $this->db->select("SUM(prt.amt) AS actingallwtotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 360, "prm.groupid" => $groupid, "pec.type" => 1, "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $sumdeptactingallw += ($fetchactingallw) ? $fetchactingallw->actingallwtotal : 0;

                        $fetchotheradd = $this->db->select("SUM(prt.amt) AS otheraddtotal")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 266, "prm.groupid" => $groupid, "pec.type" => 1 , "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $sumotheradd = ($fetchotheradd) ? $fetchotheradd->otheraddtotal : 0;

                        $get13thmonth = $this->db->select("SUM(prt.amt) AS bonus13th")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 264, "prm.groupid" => $groupid, "pec.type" => 1 , "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $month13th = ($get13thmonth) ? $get13thmonth->bonus13th : 0;

                        $get14thmonth = $this->db->select("SUM(prt.amt) AS bonus14th")
                            ->from("prime_costcenter_main as pcm")
                            ->join("prime_employee_costcenter as pec" , "pec.ccid = pcm.sysid" ,"left")
                            ->join("payroll_reports_main as prm" , "prm.empid = pec.empid" , "left")
                            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
                            ->where(array("pcm.sysid" => $row->ccid , "prt.trntype" => 3072, "prm.groupid" => $groupid, "pec.type" => 1 , "prt.payrollid" => $payrollid->sysid , "pec.status" => 1))
                            ->get()->row();
                        $month14th = ($get14thmonth) ? $get14thmonth->bonus14th : 0;

                        $sumdeptotheradd += ($sumotheradd + $month13th + $month14th) ;

                    }
                }
                $totalbasicrate += $sumdeptbasicrate;

                $data['earningsdata'][] = array(
                    "expand" => $row->ccid,
                    "deptcode" => $row->codes,
                    "basicrate" => number_format($sumdeptbasicrate , 2),
                    "cola" => number_format($sumdeptcola , 2),
                    "transallw" => number_format($sumdepttransallw , 2),
                    "ricesubsi" => number_format($sumdeptricesubsi , 2),
                    "holidaypay" => number_format($sumdeptholidaypay , 2),
                    "nitediff" => number_format($sumdeptnitediff , 2),
                    "otpay" => number_format($sumdeptotpay , 2),
                    "actingallw" => number_format($sumdeptactingallw , 2),
                    "otheradd" => number_format($sumdeptotheradd , 2)
                );
                $totalcola += $sumdeptcola;
                $totaltransallw += $sumdepttransallw;
                $totalricesubsi += $sumdeptricesubsi;
                $totalholidaypay += $sumdeptholidaypay;
                $totalnitediff +=$sumdeptnitediff;
                $totalotpay += $sumdeptotpay;
                $totalactingallw += $sumdeptactingallw;
                $totalotheradd += $sumdeptotheradd;

                $sumdeptbasicrate = 0;
                $sumdeptcola = 0;
                $sumdepttransallw = 0;
                $sumdeptricesubsi = 0;
                $sumdeptholidaypay = 0;
                $sumdeptnitediff =0;
                $sumdeptotpay = 0;
                $sumdeptactingallw = 0;
                $sumdeptotheradd = 0;
            }

        }
        $header = '';
        if($payclass == 128){
            $rep_type_code = 'RF';
            $rep_type = 'RANK AND FILE PAYROLL';
        }else if ($payclass == 3078){
            $rep_type_code = 'T2';
            $rep_type = 'TIER 2 PAYROLL';
        }else if ($payclass == 3077){
            $rep_type_code = 'T1';
            $rep_type = 'TIER 1 PAYROLL';
        }else{
            $rep_type_code = 'C';
            $rep_type = 'CONFIDENTIAL PAYROLL';
        }

        $rep_title = 'EARNINGS REGISTER';

        $header .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);
        $header .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">'.$rep_type.'</b></div>';
        $header .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: '.date("m/d/Y").'</div>';
        $header .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';





        $data['header'] = $header;
        $data['datacount'] = $count;
        $data['totalbasicrate'] = number_format($totalbasicrate, 2);
        $data['totalcola'] = number_format($totalcola, 2);
        $data['totaltransallw'] = number_format($totaltransallw, 2);
        $data['totalricesubsi'] = number_format($totalricesubsi, 2);
        $data['totalholidaypay'] = number_format($totalholidaypay, 2);
        $data['totalnitediff'] = number_format($totalnitediff, 2);
        $data['totalotpay'] = number_format($totalotpay, 2);
        $data['totalactingallw'] = number_format($totalactingallw, 2);
        $data['totalotheradd'] = number_format($totalotheradd, 2);
        $data['payclass'] = $payclass;
        return $data;
    }

    function get_deductions_report($dataid = false, $payrollyear = false, $payrollmonth = false, $payrollpayclass = false, $payrollpaytype = false){
        $data = array();
        $count= 0;

        if($payrollyear == false && $payrollmonth == false && $payrollpayclass == false && $payrollpaytype == false) {
            $dataid = $this->input->post('dataid');
            $payrollyear = $this->input->post('payrollyear');
            $payrollmonth = $this->input->post('payrollmonth');
            $payrollpayclass = $this->input->post('payrollpayclass');
            $payrollpaytype = $this->input->post('payrollpaytype');
        }

        if(in_array($payrollpayclass,array(128,3077))){
            $this->db->where(array("paytype" => $payrollpaytype));
        }

        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass , "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ?  $sql->sysid : $dataid;
        $data['groupid'] = $groupid;

        $totalssscont = 0;
        $totalsssloan = 0;
        $totalhdmfcont = 0;
        $totalhdmfloan = 0;
        $totalpecewaloan = 0;
        $totalcooploan = 0;
        $totalpagibigad = 0;
        $totalotherdedn = 0;
        $totalhmodedn = 0;
        $totaldeda = 0;
        $totalelectbill = 0;
        $totalmemins = 0;
        $totallwop = 0;
        $totalbasetax= 0;

        $totalnet = 0;
        $totaldeductions = 0;
        $cola = 0;
        $ricesubsi = 0;
        $holidaypay = 0;
        $trans_allw = 0;
        $nitediff = 0;
        $otpay = 0;
        $actingallw = 0;
        $otheradd = 0;
        $basic = 0;
        $gross = 0;

        $paytype = 0;
        $payclass = 0;
        $getpaypaytype = $this->db->select("payclass,paytype")->from("payroll_reports_group")
            ->where(array("sysid" => $groupid))->get()->row();
        $paytype = ($getpaypaytype) ? $getpaypaytype->paytype : 0;
        $payclass = ($getpaypaytype) ? $getpaypaytype->payclass : 0;

        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;

        $sql = $this->db->select("prm.ccid, pcm.codes")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_costcenter as pec" , "pec.ccid =prm.ccid" , "left")
            ->join("prime_employee_main as pem" , "pem.sysid = pec.empid" , "left")
            ->join("prime_costcenter_main as pcm" , "pcm.sysid = prm.ccid" , "left")
            ->join("prime_employee_main_payclass as pemp" ,"pemp.emp_id  = prm.empid" , "left")
            ->where(array("pcm.status" => 1 , "pcm.codes !=" => 0 , "prm.groupid" => $groupid, "pec.type" => 1 , "pec.status" => 1))
            ->group_by("prm.ccid , pcm.codes")
            ->order_by("pcm.codes" , "ASC")
            ->get();

        $data['sql'] = $this->db->last_query();
        if($sql->num_rows() > 0){
            $count = 0;
            foreach ($sql->result() as $row){
                $arr_emp = array();
                $arr_emp_amt = array();
                $count++;

                $totals = get_per_ccid_amts($row->ccid, $groupid);
                //$data['totals'][] = $totals->totals;

                $totaldeductions += $totals->totaldepttotaldedn;
                $totalnet += $totals->totalnet;
                $basic += $totals->totaldeptbasicrate;
                $cola += $totals->cola ;
                $ricesubsi += $totals->ricesubsi ;
                $holidaypay += $totals->holidaypay ;
                $trans_allw += $totals->trans_allw ;
                $nitediff += $totals->nitediff ;
                $otpay += $totals->otpay ;
                $actingallw += $totals->actingallw ;
                $otheradd += $totals->otheradd;


                $totalssscont += $totals->sumdeptssscont;
                $totalsssloan += $totals->sumdeptsssloan;
                $totalhdmfcont += $totals->sumdepthdmfcont;
                $totalhdmfloan += $totals->sumdepthdmfloan;
                $totalpecewaloan += $totals->sumdeptpecewaloan;
                $totalcooploan += $totals->sumdeptcooploan;
                $totalpagibigad += $totals->sumdeptpagibigad;
                $totalotherdedn += $totals->sumdeptotherdedn ;
                $totalhmodedn += $totals->sumdepthmodedn;
                $totaldeda += $totals->sumdeptdeda;
                $totalelectbill += $totals->sumdeptelectbill;
                $totalmemins += $totals->sumdeptmemins;
                $totallwop += $totals->sumdeptlwop;
                $totalbasetax += $totals->sumdeptbasetax ;

                $data['ccarr'][] = array('CCID' => $row->ccid, 'EMPARR' => $arr_emp, 'PCWASUM' => $arr_emp_amt);

                $data['deductionsdata'][] = array(
                    "expand" => $row->ccid,
                    "deptcode" => $row->codes,
                    "ssscont" => number_format($totals->sumdeptssscont , 2),
                    "sssloan" => number_format($totals->sumdeptsssloan , 2),
                    "hdmfcont" => number_format($totals->sumdepthdmfcont , 2),
                    "hdmfloan" => number_format($totals->sumdepthdmfloan , 2),
                    "pecewaloan" => number_format($totals->sumdeptpecewaloan, 2),
                    "cooploan" => number_format($totals->sumdeptcooploan , 2),
                    "pagibigad" => number_format($totals->sumdeptpagibigad , 2),
                    "otherdeduct" => number_format($totals->sumdeptotherdedn , 2),
                    "hmodeduct" => number_format($totals->sumdepthmodedn , 2),
                    "deda" => number_format($totals->sumdeptdeda , 2),
                    "electbill" => number_format($totals->sumdeptelectbill , 2),
                    "memins" => number_format($totals->sumdeptmemins , 2),
                    "lwop" => number_format($totals->sumdeptlwop , 2),
                    "basetax" => number_format($totals->sumdeptbasetax , 2)
                );




            }


        }

        $header = '';
        if($payclass == 128){
            $rep_type_code = 'RF';
            $rep_type = 'RANK AND FILE PAYROLL';
        }else if ($payclass == 3078){
            $rep_type_code = 'T2';
            $rep_type = 'TIER 2 PAYROLL';
        }else if ($payclass == 3077){
            $rep_type_code = 'T1';
            $rep_type = 'TIER 1 PAYROLL';
        }else{
            $rep_type_code = 'C';
            $rep_type = 'CONFIDENTIAL PAYROLL';
        }

        $rep_title = 'DEDUCTIONS REGISTER';

        $header .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);
        $header .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">'.$rep_type.'</b></div>';
        $header .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: '.date("m/d/Y").'</div>';
        $header .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';


        $data['header'] = $header;
        $data['totaldeductionamt'] = number_format($totaldeductions, 2);
        $data['datacount'] = $count;
        $data["totalssscont"] = number_format($totalssscont,2);
        $data["totalsssloan"] =  number_format($totalsssloan,2);
        $data["totalhdmfcont"] = number_format($totalhdmfcont,2);
        $data["totalhdmfloan"] = number_format($totalhdmfloan,2);
        $data["totalpecewaloan"] = number_format($totalpecewaloan,2);
        $data["totalcooploan"] = number_format($totalcooploan,2);
        $data["totalpagibigad"] = number_format($totalpagibigad,2);
        $data["totalotherdedn"] = number_format($totalotherdedn,2);
        $data["totalhmodedn"] = number_format($totalhmodedn,2);
        $data["totaldeda"] = number_format($totaldeda,2);
        $data["totalelectbill"] = number_format($totalelectbill,2);
        $data["totalmemins"] = number_format($totalmemins,2);
        $data["totallwop"] = number_format($totallwop,2);
        $data["totalbasetax"] = number_format($totalbasetax,2);

        $gross = number_format($basic + $cola + $ricesubsi + $holidaypay + $trans_allw + $nitediff + $otpay + $actingallw + $otheradd, 2, '.', '');
        $data['totalgross'] = $gross;
        $data['deductionslist'][] = array(
            'ssscont' => number_format($totalssscont, 2, '.', ''),
            'sssloan' => number_format($totalsssloan, 2, '.', ''),
            'hdmfcont' => number_format($totalhdmfcont, 2, '.', ''),
            'hdmfloan' => number_format($totalhdmfloan, 2, '.', ''),
            'pecewaloan' => number_format($totalpecewaloan, 2, '.', ''),
            'cooploan' => number_format($totalcooploan, 2, '.', ''),
            'totalpagibigadd' => number_format($totalpagibigad, 2, '.', ''),
            'totalotherdedn' => number_format($totalotherdedn, 2, '.', ''),
            'totalhmo' => number_format($totalhmodedn, 2, '.', ''),
            'deda' => number_format($totaldeda, 2, '.', ''),
            'electbill' => number_format($totalelectbill, 2, '.', ''),
            'totalmemins' => number_format($totalmemins, 2, '.', ''),
            'lwop'  => number_format($totallwop, 2, '.', ''),
            'tax' => number_format($totalbasetax, 2, '.', '')
        );

        $data['earningsdata'][] = array(
            'net' => $totalnet
        );
        $data['totalnetamt'] = number_format($totalnet, 2);

        return $data;
    }

    function get_overtime_report($dataid = false, $payrollyear = false, $payrollmonth = false, $payrollpayclass = false, $payrollpaytype = false){
        $data = array();

        if($payrollyear == false && $payrollmonth == false && $payrollpayclass == false && $payrollpaytype == false) {
            $dataid = $this->input->post('dataid');
            $payrollyear = $this->input->post('payrollyear');
            $payrollmonth = $this->input->post('payrollmonth');
            $payrollpayclass = $this->input->post('payrollpayclass');
            $payrollpaytype = $this->input->post('payrollpaytype');
        }

        if(in_array($payrollpayclass,array(128,3077))){
            $this->db->where(array("paytype" => $payrollpaytype));
        }

        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass , "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ?  $sql->sysid : $dataid;
        $nitediff = 0;
        $nitediffpay = 0;

        $otwithholiday = 0;
        $otweekends = 0;
        $otweekdays = 0;
        $othrs = 0;

        $one25 = 0;
        $one30 = 0;
        $one50 = 0;
        $one60 = 0;
        $one80 = 0;
        $two10 = 0;
        $two30 = 0;
        $two60 = 0;

        $totalndothrs = 0;
        $totalndotpay = 0;
        $totalothrs = 0;
        $totalone25 = 0;
        $totalone30 = 0;
        $totalone50 = 0;
        $totalone60 = 0;
        $totalone80 = 0;
        $totaltwo10 = 0;
        $totaltwo30 = 0;
        $totaltwo60 = 0;

        $getpayclass =$this->db->select("payclass")->from("payroll_reports_group")->where(array("sysid" => $groupid))->get()->row();
        $payclass = ($getpayclass) ? $getpayclass->payclass : 0;

        $sql = $this->db->select("prm.ccid  , pcm.codes , prg.years , prg.months , prg.paytype , prg.payclass")->from("payroll_reports_main as prm")
            ->join("prime_costcenter_main as pcm" , "pcm.sysid = prm.ccid" , "left")
            ->join("payroll_reports_group as prg" , "prg.sysid = prm.groupid" , "left")
            ->where(array("prm.groupid" => $groupid))
            ->group_by("prm.ccid , pcm.codes , prg.years , prg.months , prg.paytype  , prg.payclass")
            ->order_by("pcm.codes" , "ASC")
            ->get();

        $data['query'] = $this->db->last_query();
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

        if($payclass == 128){
            $rep_type_code = 'RF';
            $rep_type = 'RANK AND FILE PAYROLL';
        }else if ($payclass == 3078){
            $rep_type_code = 'T2';
            $rep_type = 'TIER 2 PAYROLL';
        }else if ($payclass == 3077){
            $rep_type_code = 'T1';
            $rep_type = 'TIER 1 PAYROLL';
        }else{
            $rep_type_code = 'C';
            $rep_type = 'CONFIDENTIAL PAYROLL';
        }

        $rep_title = 'OVERTIME REGISTER';

        $html .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);
        $html .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">'.$rep_type.'</b></div>';
        $html .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: '.date("m/d/Y").'</div>';
        $html .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';

        $html .= '<table class="table table-condensed tbl-xs print-table-standard">';
        $html .= '<thead>';
        $html .= '<th>DEPT CODE</th>';
        $html .= '<th>NDOT 8 Hrs</th>';
        $html .= '<th>NDOT 8 Pay</th>';
        $html .= '<th>OT Hrs</th>';
        $html .= '<th>125%</th>';
        $html .= '<th>130%</th>';
        $html .= '<th>150%</th>';
        $html .= '<th>160%</th>';
        $html .= '<th>180%</th>';
        $html .= '<th>210%</th>';
        $html .= '<th>230%</th>';
        $html .= '<th>260%</th>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $this->db->_error_message();
        if($sql->num_rows() > 0){


            foreach ($sql->result() as $row){

                $getemployee = $this->db->select("prm.empid")->from("payroll_reports_main as prm")
                    ->where(array("prm.ccid" => $row->ccid , "prm.groupid" => $groupid))
                    ->get();

                if($getemployee->num_rows() > 0){
                    foreach ($getemployee->result() as $emprow){

                        if($row->payclass == 128 || $row->payclass == 3077){
                            $this->db->where(array("pt.paytype" => $row->paytype));
                        }

                        $ot_arr = array(358, 3010, 1082, 359);

                        $getnightdiffpay = $this->db->select("SUM(pt.amt) as nitepayamt  , SUM(pt.insertamount) as enteredamt")->from("payroll_transactions as pt")
                            ->where(array("pt.empid" =>$emprow-> empid , "pt.typesid" => 358 , "pt.months" => $row->months , "pt.years" => $row->years, 'pt.paytype' => $payrollpaytype , 'pt.status' => 1))
                            ->get()->row();

                        $data['nightdiffpay'][] = $this->db->last_query();
                        $nitediff += ($getnightdiffpay) ? number_format($getnightdiffpay->enteredamt, 2, '.', '') : 0;
                        $nitediffpay += ($getnightdiffpay) ? number_format($getnightdiffpay->nitepayamt, 2, '.', '') : 0;

                        if($row->payclass == 128 || $row->payclass == 3077){
                            $this->db->where(array("pt.paytype" => $row->paytype));
                        }
                        $getotwithholiday =$this->db->select("SUM(pt.amt) as otwithholidayamt  , SUM(pt.insertamount) as enteredamt")->from("payroll_transactions as pt")
                            ->where(array("pt.empid" =>$emprow-> empid , "pt.typesid" => 3010 , "pt.months" => $row->months , "pt.years" => $row->years, 'pt.paytype' => $payrollpaytype , 'pt.status' => 1))
                            ->get()->row();
                        $otwithholiday += ($getotwithholiday) ? $getotwithholiday->enteredamt : 0;
                        $one60 += ($getotwithholiday) ? number_format($getotwithholiday->otwithholidayamt, 2, '.', '') : 0;

                        if($row->payclass == 128 || $row->payclass == 3077){
                            $this->db->where(array("pt.paytype" => $row->paytype));
                        }
                        $getotweekends =$this->db->select("SUM(pt.amt) as otweekends  , SUM(pt.insertamount) as enteredamt")->from("payroll_transactions as pt")
                            ->where(array("pt.empid" =>$emprow-> empid , "pt.typesid" => 1082 , "pt.months" => $row->months , "pt.years" => $row->years, 'pt.paytype' => $payrollpaytype , 'pt.status' => 1))
                            ->get()->row();
                        $otweekends += ($getotweekends) ? number_format($getotweekends->enteredamt, 2, '.', '') : 0;
                        $one30 += ($getotweekends) ? number_format($getotweekends->otweekends, 2, '.', '')  : 0;

                        if($row->payclass == 128 || $row->payclass == 3077){
                            $this->db->where(array("pt.paytype" => $row->paytype));
                        }
                        $getotweekdays =$this->db->select("SUM(pt.amt) as otweekdays  , SUM(pt.insertamount) as enteredamt")->from("payroll_transactions as pt")
                            ->where(array("pt.empid" =>$emprow-> empid , "pt.typesid" => 359 , "pt.months" => $row->months , "pt.years" => $row->years, 'pt.paytype' => $payrollpaytype , 'pt.status' => 1))
                            ->get()->row();
                        $otweekdays += ($getotweekdays) ? number_format($getotweekdays->enteredamt, 2, '.', '') : 0;
                        $one25 += ($getotweekdays) ? number_format($getotweekdays->otweekdays, 2, '.', '')  : 0;

                        $othrs =  number_format($otwithholiday + $otweekends + $otweekdays, 2, '.', '');


                    }
                }

                $html .= '<tr>';
                $html .= '<td>'.$row->codes.'</td>';
                $html .= '<td>'.$nitediff.'</td>';
                $html .= '<td>'.number_format($nitediffpay ,2).'</td>';
                $html .= '<td>'.$othrs.'</td>';
                $html .= '<td>'.number_format($one25 , 2).'</td>';
                $html .= '<td>'.number_format($one30 , 2).'</td>';
                $html .= '<td>'.number_format($one50 , 2).'</td>';
                $html .= '<td>'.number_format($one60 , 2).'</td>';
                $html .= '<td>'.number_format($one80 , 2).'</td>';
                $html .= '<td>'.number_format($two10 , 2).'</td>';
                $html .= '<td>'.number_format($two30 , 2).'</td>';
                $html .= '<td>'.number_format($two60 , 2).'</td>';
                $html .= '</tr>';
                $data['overtimedata'][] = array(
                    "expand" => $row->ccid,
                    "deptcode" => $row->codes,
                    "ndot8hrs" => $nitediff,
                    "ndotpay" => number_format($nitediffpay ,2),
                    "othrs" => $othrs,
                    "125%" => number_format($one25 , 2),
                    "130%" => number_format($one30,2),
                    "150%" => number_format($one50, 2),
                    "160%" => number_format($one60,2),
                    "180%" => number_format($one80, 2),
                    "210%" => number_format($two10,2),
                    "230%" => number_format($two30,2),
                    "260%" => number_format($two60 , 2)
                );

                //last totals
                $totalndothrs += $nitediff;
                $totalndotpay += $nitediffpay;
                $totalothrs += $othrs;
                $totalone25 += $one25;
                $totalone30 += $one30;
                $totalone50 += $one50;
                $totalone60 += $one60;
                $totalone80 += $one80;
                $totaltwo10 += $two10;
                $totaltwo30 += $two30;
                $totaltwo60 += $two60;

                $nitediff = 0;
                $nitediffpay = 0;
                $otwithholiday = 0;
                $otweekends = 0;
                $otweekdays = 0;
                $othrs = 0;


                $one25 = 0;
                $one30 = 0;
                $one50 = 0;
                $one60 = 0;
                $one80 = 0;
                $two10 = 0;
                $two30 = 0;
                $two60 = 0;
            }
        }

        $html .= '<tfoot>';
        $html .= '<tr>';
        $html .= '<td>Total</td>';
        $html .= '<td class="bold">'.$totalndothrs.'</td>';
        $html .= '<td class="bold">'.$totalndotpay.'</td>';
        $html .= '<td class="bold">'.$totalothrs.'</td>';
        $html .= '<td class="bold">'.number_format( $totalone25, 2).'</td>';
        $html .= '<td class="bold">'.number_format($totalone30, 2).'</td>';
        $html .= '<td class="bold">'.number_format($totalone50, 2).'</td>';
        $html .= '<td class="bold">'.number_format($totalone60, 2).'</td>';
        $html .= '<td class="bold">'.number_format($totalone80, 2).'</td>';
        $html .= '<td class="bold">'.number_format($totaltwo10, 2).'</td>';
        $html .= '<td class="bold">'.number_format($totaltwo30, 2).'</td>';
        $html .= '<td class="bold">'.number_format($totaltwo60, 2).'</td>';
        $html .= '</tr>';
        $html .= '</tfoot>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';

        $html.= '<div class="row">';
        $html.= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
        $html.= '<div>Encoded by:</div>';
        $html.= '<div>____________</div>';
        $html.= '<div>HRDH</div>';
        $html.= '</div>';
        $html.= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
        $html.= '<div>Checked by:</div>';
        $html.= '<div>____________</div>';
        $html.= '<div>GA</div>';
        $html.= '</div>';
        $html.= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
        $html.= '<div>Noted by:</div>';
        $html.= '<div>____________</div>';
        $html.= '<div>FM</div>';
        $html.= '</div>';
        $html.= '<div class="col-md-3  col-sm-3 col-xs-3 col-lg-3">';

        $html.= '</div>';
        $html.= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';
        $html.= '<div>Approved by:</div>';
        $html.= '<div>____________</div>';
        $html.= '<div>P-CEO</div>';
        $html.= '</div>';
        $html.= '</div>';


        $data['html'] = $html;

        $data['totalndothrs'] = number_format($totalndothrs , 2);
        $data['totalndotpay'] = number_format($totalndotpay , 2);
        $data['totalothrs'] = number_format($totalothrs , 2);
        $data['totalone25'] = number_format($totalone25 , 2);
        $data['totalone30'] = number_format($totalone30 , 2);
        $data['totalone50'] = number_format($totalone50 , 2);
        $data['totalone60'] = number_format($totalone60 , 2);
        $data['totalone80'] = number_format($totalone80 , 2);
        $data['totaltwo10'] = number_format($totaltwo10 , 2);
        $data['totaltwo30'] = number_format($totaltwo30 , 2);
        $data['totaltwo60'] = number_format($totaltwo60 , 2);

        return $data;
    }

    function tbl_contribs() {
        $data = array();

        $contribs_qury = $this->db->select('names, desc, sysid')
            ->from('prime_types_parameter')
            ->where('codes','EMPCONT')
            ->where_in('sysid',array(72,73,74,75))
            ->get();

        if ($contribs_qury->num_rows() > 0) {
            foreach ($contribs_qury->result() AS $row) {
                $data['contribs_list'][] = array(
                    'name' => $row->names.' ('.$row->desc.')',
                    'select' => '<input type="radio" class="form-control" value="'.$row->sysid.'" id="contrib_radio" name="contrib_radio" >',
                );
            }
        }

        return json_encode($data);
    }

    function tbl_earnings() {
        $data = array();
        $typesid = $this->input->post('id');

        if ($typesid && $typesid != '') {

            $contribs_qury = $this->db->select('tp.`names`, tp.`desc` , tp.sysid')
                ->from('payroll_matrix AS pm')
                ->join('prime_types_parameter AS tp', 'tp.sysid = pm.typesid', 'left')
                ->where(array('pm.functions' => 1, 'pm.status' => 1, 'pm.effects' => 1))
                ->order_by('tp.sysid ASC')->get();

            if ($contribs_qury->num_rows() > 0) {
                foreach ($contribs_qury->result() AS $row) {
                    $added = $this->db->select('COUNT(earningid),earningid')
                        ->from('prime_contribution_add_matrix')
                        ->where(array('typesid' => $typesid, 'earningid' => $row->sysid, 'status' => 1))
                        ->get()->row();

                    if ($added) {
                        $selected = ($added->earningid == $row->sysid) ? 'checked' : '';
                        $status = ($added->earningid == $row->sysid) ? 1 : 0;
                    }

                    $data['earnings_list'][] = array(
                        'select' => '<input type="checkbox" class="form-control" ' . $selected . ' value="' . $row->sysid . '" data-id="' . $typesid . '" data-status="' . $status . '" id="earnings_" name="earnings_" >',
                        'name' => $row->names,
                        'desc' => $row->desc,
                    );
                }
            }
        }

        return json_encode($data);
    }

    function update_add_matrix () {
        $typesid = $this->input->post('typesid');
        $earningid = $this->input->post('earningid');
        $status = $this->input->post('status');
        $this->db->trans_begin();

        if ($status == 1) {
            $this->db->where(array('typesid' => $typesid, 'earningid' => $earningid));
            $this->db->update('prime_contribution_add_matrix', array('status' => 0, 'updatedby' => user_id()));
        } else {
            $this->db->insert('prime_contribution_add_matrix', array('typesid' => $typesid, 'earningid' => $earningid, 'createdby' => user_id()));
        }

        $data = db_trans($this->db);
        return json_encode($data);
    }

    function get_emp_payslip_preview()
    {
        $data = array();
        $empid = $this->input->post('empid');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $payclass = 1;

        $emppayclass = select_emp_payclass($empid)->payclassid;

        $payclass_qry = $this->db->select('payrollpayclass')
            ->from('prime_employee_main_payclass_grouping')
            ->where(array('payclass' => $emppayclass, 'status' => 1))
            ->get()->row();

        if ($payclass_qry) {
            $payclass = $payclass_qry->payrollpayclass;
        }

        $from_ = explode('-', $datefrom);
        $to_ = explode('-', $dateto);

        $paytypefrom = ($from_[2] > 15) ? 2 : 1;
        $paytypeto = ($to_[2] > 15) ? 2 : 1;
        $yearfrom = $from_[0];
        $yearto = $to_[0];
        $monthfrom = $from_[1];
        $monthto = $to_[1];

        $from = implode('-', array($yearfrom, $monthfrom, $paytypefrom));
        $to = implode('-', array($yearto, $monthto, $paytypeto));

        $payroll_qry = $this->db->select('prm.empid,prg.sysid as prgid,prg.years,prg.months,prg.payclass,prg.paytype,prm.sysid as prmid,prm.basic,prm.deductions,prm.earnings,prm.tax,prm.net')
            ->from('payroll_reports_group as prg')
            ->join('payroll_reports_main as prm', 'prm.groupid = prg.sysid', 'left')
            ->where('CONCAT_WS("-",prg.years,LPAD(prg.months,2,0),prg.paytype) BETWEEN "' . $from . '" AND "' . $to . '"')
            ->where(array('prg.status' => 301, 'prm.empid' => $empid))
            ->order_by('prg.years desc,prg.months desc,prg.paytype desc')
            ->get();

        $data['qry'] = $this->db->last_query();

        if ($payroll_qry->num_rows() > 0) {
            $num = 1;
            foreach ($payroll_qry->result() as $row) {
                $paytypes = array('', '1st Half', '2nd Half');
                if ($row->payclass == 1) {
                    $paytype = 'CF';
                } else {
                    $paytype = $paytypes[$row->paytype];
                }
                $data['list'][] = array(
                    'num' => $num++,
                    //'expand' => btn_expand($row->empid),
                    'year' => $row->years . ' <input type="hidden" id="pryear" value="' . $row->years . '">',
                    'month' => date('F', mktime(0, 0, 0, $row->months, 1)) . ' <input type="hidden" id="prmonth" value="' . $row->months . '">',
                    'paytype' => $paytype . ' <input type="hidden" id="prpaytype" value="' . $row->paytype . '"> <input type="hidden" id="prpayclass" value="' . $row->payclass . '">',
                    'basic' => $row->basic,
                    'deductions' => $row->deductions,
                    'earnings' => $row->earnings,
                    'tax' => $row->tax,
                    'net' => $row->net,
                );
            }
        }

        return json_encode($data);
    }
}
