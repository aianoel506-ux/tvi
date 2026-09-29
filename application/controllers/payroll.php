<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_active_record|CI_DB_sqlsrv_driver|CI_DB_mysql_driver $db
 * @property Model_reports $model_reports
 * @property Model_payroll $model_payroll
 * @property Model_hris $model_hris
 * @property Datatables $datatables
 * @property PHPExcel $excel
 * @property CI_URI $uri
 */
class Payroll extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_reports');
        $this->load->model('model_payroll');
        $this->load->model('model_hris');
        $this->load->library('datatables');
        $this->load->helper('hris_helper', TRUE);
    }

    public function emplist()
    {
        echo $this->model_hris->employee_list();
    }

    public function cleartrans()
    {
        echo $this->model_hris->employee_list();
    }

    public function payrollinfo()
    {
        echo $this->model_payroll->payroll_info();
    }

    public function payslipinfo()
    {
        echo $this->model_payroll->payslip_info();
    }

    public function getpayslipinfo()
    {

        $data['dayswork'] = number_format(26);
        $data['hrsot'] = 0;
        $data['hrslate'] = 0;
        $data['hrsflexy'] = 0;

        $data['sss'] = 0;
        $data['sssloans'] = 0;
        $data['pagibig'] = 0;
        $data['pagibigloans'] = 0;

        $data['qry'] = true;
        echo json_encode($data);
    }

    public function process_payroll()
    {

        $data['dayswork'] = number_format(26);
        $data['hrsot'] = 0;
        $data['hrslate'] = 0;
        $data['hrsflexy'] = 0;

        $data['sss'] = 0;
        $data['sssloans'] = 0;
        $data['pagibig'] = 0;
        $data['pagibigloans'] = 0;

        $data['qry'] = true;
        echo json_encode($data);
    }

    function gettaxtable()
    {
        echo $this->model_payroll->tax_table_info();
    }

    //start function for process payroll
    function processpayroll2()
    {
        // initialize variables
        // variables for message
        //check if payroll already processed
        $month = $this->input->post('month_payroll_process_data');
        $year = $this->input->post('year_payroll_process_data');

        $check = $this->model_payroll->check_data_payroll($month, $year);
        if ($check) {
            $title = "Payroll Already processed";
            $message = 'Payroll already processed for this month and year!';
            $func = 'warning';
        } else {
            $message = '';
            $title = "Can't Process Payroll";
            $func = 'error';
            // end variables for message
            $cola = 0;
            $other_earnings_special = 0;
            $holidaypay = 0;
            $bonus = 0;
            $ps = 0;
            //payclass_id is 128 for tank and file
            $query = $this->db->query("select e.sysid from prime_employee_main as e left join prime_employee_main_payclass as payclass on e.sysid = payclass.emp_id left join prime_employee_main_job_category jc
on jc.empid = e.sysid  where e.status = 1 and payclass.payclass_id = 128 and jc.jobcatid = 157");

            foreach ($query->result() as $row) {
                //initialize shit here
                $day = 15;
                $cola = 0;
                $transpo = 0;
                $other_earnings_special = 0;
                $rice = 0;
                $pecewa = 0;
                $coop = 0;
                $elec = 0;
                $sssloan = 0;
                $hdmfloan = 0;
                $pagibigadd = 0;
                $hmo_deduction = 0;
                $other_deduction = 0;
                $lwop = 0;
                $bonus_application = 0;
                $bonus_application_seg = 0;
                $other_earnings_adjustment = 0;
                // end initialize
                $empid = $row->sysid;
                $qry = $this->db->query("SELECT salary FROM prime_employee_salary where emp_id = $empid");
                foreach ($qry->result() as $row) {
                    $salary = $row->salary;
                    if ($salary) {
                        $basic = $salary / 2;
                        $bonus_application = $salary * 3;
                        if ($bonus_application > 82000) {
                            // seggregate in 12 month semi
                            $bonus_application_seg = ($bonus_application - 82000) / 24;
                        } else {
                            $bonus_application_seg = 0;
                        }
                    } else {
                        $salary = 0;
                        $basic = 0;
                        $bonus_application = 0;
                        $bonus_application_seg = 0;
                    }
                }
                // other manual earnings and deduction

                $qrycola = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 251 and month = $month and year = $year and day = $day");
                foreach ($qrycola->result() as $row) {
                    $cola = $row->amount;
                }
                $qrytranspo = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 252 and month = $month and year = $year and day = $day");
                foreach ($qrytranspo->result() as $row) {
                    $transpo = $row->amount;
                }
                $qryrice = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 253 and month = $month and year = $year and day = $day");
                foreach ($qryrice->result() as $row) {
                    $rice = $row->amount;
                }
                $qrypecewa = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 254 and month = $month and year = $year and day = $day");
                foreach ($qrypecewa->result() as $row) {
                    $pecewa = $row->amount;
                }
                $qrycoop = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 255 and month = $month and year = $year and day = $day");
                foreach ($qrycoop->result() as $row) {
                    $coop = $row->amount;
                }
                $qryelec = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 256 and month = $month and year = $year and day = $day");
                foreach ($qryelec->result() as $row) {
                    $elec = $row->amount;
                }
                $qrysssloan = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 257 and month = $month and year = $year and day = $day");
                foreach ($qrysssloan->result() as $row) {
                    $sssloan = $row->amount;
                }
                $qryhdmfloan = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 258 and month = $month and year = $year and day = $day");
                foreach ($qryhdmfloan->result() as $row) {
                    $hdmfloan = $row->amount;
                }
                $qrypagibigadd = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 259 and month = $month and year = $year and day = $day");
                foreach ($qrypagibigadd->result() as $row) {
                    $pagibigadd = $row->amount;
                }
                $qryhmo_deduction = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 260 and month = $month and year = $year and day = $day");
                foreach ($qryhmo_deduction->result() as $row) {
                    $hmo_deduction = $row->amount;
                }
                $qryother_deduction = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 261 and month = $month and year = $year and day = $day");
                foreach ($qryother_deduction->result() as $row) {
                    $other_deduction = $row->amount;
                }
                $qrylwop = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 262 and month = $month and year = $year and day = $day");
                foreach ($qrylwop->result() as $row) {
                    $lwop = $row->amount;
                }
                $qryhdpay = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 263 and month = $month and year = $year and day = $day");
                foreach ($qryhdpay->result() as $row) {
                    $holidaypay = $row->amount;
                }
                $qrybonus = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 264 and month = $month and year = $year and day = $day");
                foreach ($qrybonus->result() as $row) {
                    $bonus = $row->amount;
                }
                $qryps = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 265 and month = $month and year = $year and day = $day");
                foreach ($qryps->result() as $row) {
                    $ps = $row->amount;
                }
                //
                $qry_payclass = $this->db->query("SELECT payclass_id FROM prime_employee_main_payclass where emp_id = $empid");
                foreach ($qry_payclass->result() as $row) {
                    $payclass = $row->payclass_id;
                }
                // sss employee computation
                $qry_sss = $this->db->query("select total_employee_share from prime_employee_sss where  $salary > salary_lower_range and $salary < salary_higher_range");
                foreach ($ssstest = $qry_sss->result() as $row) {
                    if ($ssstest) {
                        $sss_e = $row->total_employee_share;
                    } else {
                        $sss_e = 0;
                    }
                }
                // sss employer computation
                $qry_sss_e = $this->db->query("select total_employer_share from prime_employee_sss where  $salary >= salary_lower_range and $salary <= salary_higher_range");
                foreach ($qry_sss_e->result() as $row) {
                    if ($qry_sss_e) {
                        $sss_ee = $row->total_employer_share;
                    } else {
                        $sss_ee = 0;
                    }
                }
                // insert philhealth computation
                $qry_philhealth = $this->db->query("select employee_share from prime_employee_philhealth where  $salary >= lower_salary_range and $salary <= higher_salary_range");
                foreach ($qry_philhealth->result() as $row) {
                    $philhealth_e = $row->employee_share;
                }
                $qry_philhealth2 = $this->db->query("select employer_share from prime_employee_philhealth where  $salary >= lower_salary_range and $salary <= higher_salary_range");
                foreach ($qry_philhealth2->result() as $row) {
                    $philhealth_ee = $row->employer_share;
                }

                $qry_pagibig = $this->db->query("select (employee_share * max) as pagibig_e  from prime_employee_pagibig where $salary >=  lower_range and $salary <= higher_range");
                foreach ($qry_pagibig->result() as $row) {
                    $pagibig_e = $row->pagibig_e;
                }
                $qry_pagibig2 = $this->db->query("select (employer_share * max) as pagibig_ee from prime_employee_pagibig where $salary >=  lower_range and $salary <= higher_range");
                foreach ($qry_pagibig2->result() as $row) {
                    $pagibig_ee = $row->pagibig_ee;
                }
                $premium_deduct = $sss_e + $pagibig_e;
                $other_deduct2 = $pecewa + $coop + $elec + $sssloan + $hdmfloan + $pagibigadd + $other_deduction + $hmo_deduction;
                $other_earnings = $rice + $cola + $transpo + $holidaypay;

                $netwt = $salary - ($premium_deduct + $other_deduct2);
                $gross_earning = $basic + $cola + $rice + $transpo;
                $taxable_income = ($salary + $cola + $bonus_application_seg) - $lwop;
                // check count dependents and applied to tax
                $check_dependents = $this->db->query("select count from prime_employee_dependents_count where empid = $empid ");
                foreach ($check_dependents->result() as $row) {
                    $numdependents = $row->count;
                }
                // end check count

                //$withholding_tax = $this->db->query("select (base_tax + (($taxable_income - lower_range)* per_ex)) as withtax from prime_employee_bir where $taxable_income >= lower_range and $taxable_income <= higher_range and type = 's' and level = $numdependents ");
                if ($payclass == 1) {
                    $payclass_ = 1;
                } else {
                    $payclass_ = 128;
                }
                //BASED ON PAYCLASS, SEARCH FOR
                $withholding_tax = $this->db->query("
                    select 
                    sysid,
                    (amtcont + (($taxable_income - amtmin)* rateemployee)) as withtax 
                    from prime_contribution_matrix 
                    where $taxable_income >= amtmin 
                    and $taxable_income <= amtmax 
                    and conttype = 75
                    and `status` = 1
                    and payclass = $payclass_
                    ");

//
                foreach ($withholding_tax->result() as $row) {
                    $wt = $row->withtax;
                    $wt15 = $wt / 2;
                }
                $other_earnings_special = $bonus + $ps;
                $net = ($gross_earning + $other_earnings_special) - ($premium_deduct + $other_deduct2 + $wt15 + $lwop);
                $total_earnings = ($basic + $other_earnings + $other_earnings_special) - $lwop;
                $total_deductions = $other_deduct2 + $premium_deduct;
                //insert into table
                $data_payroll15 = array(
                    'empid' => $empid,
                    'current_salary' => $salary,
                    'basic' => $basic,
                    'payroll_type' => $payclass,
                    'sss_employee' => $sss_e,
                    'sss_employeer' => $sss_ee,
                    'hmo_employee' => $philhealth_e,
                    'hmo_employeer' => $philhealth_ee,
                    'pagibig_employee' => $pagibig_e,
                    'pagibig_employer' => $pagibig_ee,
                    'withholding_tax' => $wt15,
                    'total_earnings' => $total_earnings,
                    'total_deductions' => $total_deductions,
                    'netpay' => $net,
                    'cola' => $cola,
                    'transpo' => $transpo,
                    'rice' => $rice,
                    'sss_loan' => $sssloan,
                    'pagibig_loan' => $hdmfloan,
                    'union_fees' => $pecewa,
                    'coop_loan' => $coop,
                    'hmo_deductions' => $hmo_deduction,
                    'other_deductions' => $other_deduction,
                    'leave_withoutpay' => $lwop,
                    'month' => $month,
                    'year' => $year,
                    'day' => $day,
                    'holidaypay' => $holidaypay,
                    'other_earnings' => $other_earnings_special,
                    'bonus_application' => $bonus_application_seg,
                    'adjustment_earnings' => $other_earnings_adjustment,
                    'electric_bills' => $elec,
                    'pagibig_add' => $pagibigadd
                );


                //insert to payroll table

                $success_insert_payroll15 = $this->db->insert('prime_employee_payroll_transactions', $data_payroll15);

                if ($success_insert_payroll15) {
                    $title = "Processed Payroll";
                    $func = 'success';
                }
            }
            // add 30 here
            foreach ($query->result() as $row) {
                //initialize shit here
                $holidaypay = 0;
                $other_earnings_special = 0;
                $day = 30;
                $cola = 0;
                $transpo = 0;
                $rice = 0;
                $pecewa = 0;
                $coop = 0;
                $elec = 0;
                $sssloan = 0;
                $hdmfloan = 0;
                $pagibigadd = 0;
                $hmo_deduction = 0;
                $other_deduction = 0;
                $lwop = 0;
                $bonus = 0;
                $ps = 0;
                $bonus_application_seg = 0;
                $bonus_application = 0;
// end initialize
                $empid = $row->sysid;
                $qry = $this->db->query("SELECT salary FROM prime_employee_salary where emp_id = $empid");
                foreach ($qry->result() as $row) {
                    $salary = $row->salary;
                    $basic = $salary / 2;
                    $bonus_application = $salary * 3;
                    if ($bonus_application > 82000) {
                        // seggregate in 12 month semi
                        $bonus_application_seg = ($bonus_application - 82000) / 24;
                    } else {
                        $bonus_application_seg = 0;
                    }
                }
                // other manual earnings and deduction

                $qrycola = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 251 and month = $month and year = $year and day = $day");
                foreach ($qrycola->result() as $row) {
                    $cola = $row->amount;
                }
                $qrytranspo = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 252 and month = $month and year = $year and day = $day");
                foreach ($qrytranspo->result() as $row) {
                    $transpo = $row->amount;
                }
                $qryrice = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 253 and month = $month and year = $year and day = $day");
                foreach ($qryrice->result() as $row) {
                    $rice = $row->amount;
                }
                $qrypecewa = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 254 and month = $month and year = $year and day = $day");
                foreach ($qrypecewa->result() as $row) {
                    $pecewa = $row->amount;
                }
                $qrycoop = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 255 and month = $month and year = $year and day = $day");
                foreach ($qrycoop->result() as $row) {
                    $coop = $row->amount;
                }
                $qryelec = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 256 and month = $month and year = $year and day = $day");
                foreach ($qryelec->result() as $row) {
                    $elec = $row->amount;
                }
                $qrysssloan = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 257 and month = $month and year = $year and day = $day");
                foreach ($qrysssloan->result() as $row) {
                    $sssloan = $row->amount;
                }
                $qryhdmfloan = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 258 and month = $month and year = $year and day = $day");
                foreach ($qryhdmfloan->result() as $row) {
                    $hdmfloan = $row->amount;
                }
                $qrypagibigadd = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 259 and month = $month and year = $year and day = $day");
                foreach ($qrypagibigadd->result() as $row) {
                    $pagibigadd = $row->amount;
                }
                $qryhmo_deduction = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 260 and month = $month and year = $year and day = $day");
                foreach ($qryhmo_deduction->result() as $row) {
                    $hmo_deduction = $row->amount;
                }
                $qryother_deduction = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 261 and month = $month and year = $year and day = $day");
                foreach ($qryother_deduction->result() as $row) {
                    $other_deduction = $row->amount;
                }
                $qrylwop = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 262 and month = $month and year = $year and day = $day");
                foreach ($qrylwop->result() as $row) {
                    $lwop = $row->amount;
                }
                $qryhdpay = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 263 and month = $month and year = $year and day = $day");
                foreach ($qryhdpay->result() as $row) {
                    $holidaypay = $row->amount;
                }
                $qrybonus = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 264 and month = $month and year = $year and day = $day");
                foreach ($qrybonus->result() as $row) {
                    $bonus = $row->amount;
                }
                $qryps = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 265 and month = $month and year = $year and day = $day");
                foreach ($qryps->result() as $row) {
                    $ps = $row->amount;
                }
                //
                $qry_payclass = $this->db->query("SELECT payclass_id FROM prime_employee_main_payclass where emp_id = $empid");
                foreach ($qry_payclass->result() as $row) {
                    $payclass = $row->payclass_id;
                }
                // sss employee computation
                $sss_e = 0;
                // sss employer computation
                $sss_ee = 0;
                // insert philhealth computation
                $philhealth_e = 0;
                $philhealth_ee = 0;
                $pagibig_e = 0;
                $pagibig_ee = 0;
                $premium_deduct = $sss_e + $pagibig_e;
                $other_deduct2 = $pecewa + $coop + $elec + $sssloan + $hdmfloan + $pagibigadd + $other_deduction + $hmo_deduction;
                $other_earnings = $rice + $cola + $transpo + $holidaypay;
                $netwt = $salary - ($premium_deduct + $other_deduct2);
                $gross_earning = $basic + $cola + $rice + $transpo;
                $taxable_income = ($salary + $cola + $bonus_application_seg) - $lwop;
                // check count dependents and applied to tax
                $check_dependents = $this->db->query("select count from prime_employee_dependents_count where empid = $empid ");
                foreach ($check_dependents->result() as $row) {
                    $numdependents = $row->count;
                }
                // end check count

                $withholding_tax = $this->db->query("select (base_tax + (($taxable_income - lower_range)* per_ex)) as withtax from prime_employee_bir where $taxable_income >= lower_range and $taxable_income <= higher_range and type = 's' and level = $numdependents ");

//
                foreach ($withholding_tax->result() as $row) {
                    $wt = $row->withtax;
                    $wt15 = $wt / 2;
                }
                $other_earnings_special = $bonus + $ps;
                $net = ($gross_earning + $other_earnings_special + $holidaypay) - ($premium_deduct + $other_deduct2 + $wt15 + $lwop);
                $total_earnings = ($basic + $other_earnings + $other_earnings_special) - $lwop;
                $total_deductions = $other_deduct2 + $premium_deduct;
                //insert into table
                $data_payroll30 = array(
                    'empid' => $empid,
                    'current_salary' => $salary,
                    'basic' => $basic,
                    'payroll_type' => $payclass,
                    'sss_employee' => $sss_e,
                    'sss_employeer' => $sss_ee,
                    'hmo_employee' => $philhealth_e,
                    'hmo_employeer' => $philhealth_ee,
                    'pagibig_employee' => $pagibig_e,
                    'pagibig_employer' => $pagibig_ee,
                    'withholding_tax' => $wt15,
                    'total_earnings' => $total_earnings,
                    'total_deductions' => $total_deductions,
                    'netpay' => $net,
                    'cola' => $cola,
                    'transpo' => $transpo,
                    'rice' => $rice,
                    'sss_loan' => $sssloan,
                    'pagibig_loan' => $hdmfloan,
                    'union_fees' => $pecewa,
                    'coop_loan' => $coop,
                    'hmo_deductions' => $hmo_deduction,
                    'other_deductions' => $other_deduction,
                    'leave_withoutpay' => $lwop,
                    'month' => $month,
                    'year' => $year,
                    'day' => $day,
                    'holidaypay' => $holidaypay,
                    'other_earnings' => $other_earnings_special,
                    'bonus_application' => $bonus_application_seg,
                    'adjustment_earnings' => $other_earnings_adjustment,
                    'electric_bills' => $elec,
                    'pagibig_add' => $pagibigadd
                );


                //insert to payroll table

                $success_insert_payroll30 = $this->db->insert('prime_employee_payroll_transactions', $data_payroll30);

                if ($success_insert_payroll30) {
                    $title = "Processed Payroll";
                    $func = 'success';
                }
            }
            // end 30 here
            // add for confidential process here
            $query_confid = $this->db->query("select e.sysid from prime_employee_main as e left join prime_employee_main_payclass as payclass on e.sysid = payclass.emp_id left join prime_employee_main_job_category as jc
on jc.empid = e.sysid where e.status = 1 and payclass.payclass_id != 128 and jc.jobcatid = 157");

            foreach ($query_confid->result() as $row) {
                //initialize shit here
                $holidaypay = 0;
                $other_earnings_special = 0;
                $day = 30;
                $cola = 0;
                $transpo = 0;
                $rice = 0;
                $pecewa = 0;
                $coop = 0;
                $elec = 0;
                $sssloan = 0;
                $hdmfloan = 0;
                $pagibigadd = 0;
                $hmo_deduction = 0;
                $other_deduction = 0;
                $lwop = 0;
                $bonus = 0;
                $ps = 0;
                $bonus_application_seg = 0;
                $bonus_application = 0;
                $other_earnings_adjustment = 0;
// end initialize
                $empid = $row->sysid;
                $qry = $this->db->query("SELECT salary FROM prime_employee_salary where emp_id = $empid");
                foreach ($qry->result() as $row) {
                    $salary = $row->salary;
                    $basic = $salary;
                    $bonus_application = $salary * 3;
                    if ($bonus_application > 82000) {
                        // seggregate in 12 month semi
                        $bonus_application_seg = ($bonus_application - 82000) / 12;
                    } else {
                        $bonus_application_seg = 0;
                    }
                }
                // other manual earnings and deduction

                $qrycola = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 251 and month = $month and year = $year and day = $day");
                foreach ($qrycola->result() as $row) {
                    $cola = $row->amount;
                }
                $qrytranspo = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 252 and month = $month and year = $year and day = $day");
                foreach ($qrytranspo->result() as $row) {
                    $transpo = $row->amount;
                }
                $qryrice = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 253 and month = $month and year = $year and day = $day");
                foreach ($qryrice->result() as $row) {
                    $rice = $row->amount;
                }
                $qrypecewa = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 254 and month = $month and year = $year and day = $day");
                foreach ($qrypecewa->result() as $row) {
                    $pecewa = $row->amount;
                }
                $qrycoop = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 255 and month = $month and year = $year and day = $day");
                foreach ($qrycoop->result() as $row) {
                    $coop = $row->amount;
                }
                $qryelec = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 256 and month = $month and year = $year and day = $day");
                foreach ($qryelec->result() as $row) {
                    $elec = $row->amount;
                }
                $qrysssloan = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 257 and month = $month and year = $year and day = $day");
                foreach ($qrysssloan->result() as $row) {
                    $sssloan = $row->amount;
                }
                $qryhdmfloan = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 258 and month = $month and year = $year and day = $day");
                foreach ($qryhdmfloan->result() as $row) {
                    $hdmfloan = $row->amount;
                }
                $qrypagibigadd = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 259 and month = $month and year = $year and day = $day");
                foreach ($qrypagibigadd->result() as $row) {
                    $pagibigadd = $row->amount;
                }
                $qryhmo_deduction = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 260 and month = $month and year = $year and day = $day");
                foreach ($qryhmo_deduction->result() as $row) {
                    $hmo_deduction = $row->amount;
                }
                $qryother_deduction = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 261 and month = $month and year = $year and day = $day");
                foreach ($qryother_deduction->result() as $row) {
                    $other_deduction = $row->amount;
                }
                $qrylwop = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 262 and month = $month and year = $year and day = $day");
                foreach ($qrylwop->result() as $row) {
                    $lwop = $row->amount;
                }
                $qryhdpay = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 263 and month = $month and year = $year and day = $day");
                foreach ($qryhdpay->result() as $row) {
                    $holidaypay = $row->amount;
                }
                $qrybonus = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 264 and month = $month and year = $year and day = $day");
                foreach ($qrybonus->result() as $row) {
                    $bonus = $row->amount;
                }
                $qryps = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 265 and month = $month and year = $year and day = $day");
                foreach ($qryps->result() as $row) {
                    $ps = $row->amount;
                }
                $qryother_additional = $this->db->query("select amount from payroll_other_earnings_and_deductions_trn where empid = $empid and transaction_type = 266 and month = $month and year = $year and day = $day");
                foreach ($qryother_additional->result() as $row) {
                    $other_earnings_adjustment = $row->amount;
                }
                //
                $qry_payclass = $this->db->query("SELECT payclass_id FROM prime_employee_main_payclass where emp_id = $empid");
                foreach ($qry_payclass->result() as $row) {
                    $payclass = $row->payclass_id;
                }
                // sss employee computation
                $qry_sss = $this->db->query("select total_employee_share from prime_employee_sss where  $salary > salary_lower_range and $salary < salary_higher_range");
                foreach ($qry_sss->result() as $row) {
                    $sss_e = $row->total_employee_share;
                }
                // sss employer computation
                $qry_sss_e = $this->db->query("select total_employer_share from prime_employee_sss where  $salary >= salary_lower_range and $salary <= salary_higher_range");
                foreach ($qry_sss_e->result() as $row) {
                    $sss_ee = $row->total_employer_share;
                }
                // insert philhealth computation
                $qry_philhealth = $this->db->query("select employee_share from prime_employee_philhealth where  $salary >= lower_salary_range and $salary <= higher_salary_range");
                foreach ($qry_philhealth->result() as $row) {
                    $philhealth_e = $row->employee_share;
                }
                $qry_philhealth2 = $this->db->query("select employer_share from prime_employee_philhealth where  $salary >= lower_salary_range and $salary <= higher_salary_range");
                foreach ($qry_philhealth2->result() as $row) {
                    $philhealth_ee = $row->employer_share;
                }

                $qry_pagibig = $this->db->query("select (employee_share * max) as pagibig_e  from prime_employee_pagibig where $salary >=  lower_range and $salary <= higher_range");
                foreach ($qry_pagibig->result() as $row) {
                    $pagibig_e = $row->pagibig_e;
                }
                $qry_pagibig2 = $this->db->query("select (employer_share * max) as pagibig_ee from prime_employee_pagibig where $salary >=  lower_range and $salary <= higher_range");
                foreach ($qry_pagibig2->result() as $row) {
                    $pagibig_ee = $row->pagibig_ee;
                }
                $premium_deduct = $sss_e + $pagibig_e;
                $other_deduct2 = $pecewa + $coop + $elec + $sssloan + $hdmfloan + $pagibigadd + $other_deduction + $hmo_deduction;
                $other_earnings = $rice + $cola + $transpo + $holidaypay;
                $netwt = $salary - ($premium_deduct + $other_deduct2);
                $gross_earning = $basic + $cola + $rice + $transpo;
                $taxable_income = ($salary + $cola + $bonus_application_seg + $other_earnings_adjustment) - $lwop;
                // check count dependents and applied to tax
                $check_dependents = $this->db->query("select count from prime_employee_dependents_count where empid = $empid ");
                foreach ($check_dependents->result() as $row) {
                    $numdependents = $row->count;
                }
                // end check count

                $withholding_tax = $this->db->query("select (base_tax + (($taxable_income - lower_range)* per_ex)) as withtax from prime_employee_bir where $taxable_income >= lower_range and $taxable_income <= higher_range and type = 's' and level = $numdependents ");

//
                foreach ($withholding_tax->result() as $row) {
                    $wt = $row->withtax;
                    $wt15 = $wt;
                }
                $other_earnings_special = $bonus + $ps;
                $net = ($gross_earning + $other_earnings_special + $holidaypay + $other_earnings_adjustment) - ($premium_deduct + $other_deduct2 + $wt15 + $lwop);
                $total_earnings = ($basic + $other_earnings + $other_earnings_special + $other_earnings_adjustment) - $lwop;
                $total_deductions = $other_deduct2 + $premium_deduct;
                //insert into table
                $data_payroll_confid = array(
                    'empid' => $empid,
                    'current_salary' => $salary,
                    'basic' => $basic,
                    'payroll_type' => $payclass,
                    'sss_employee' => $sss_e,
                    'sss_employeer' => $sss_ee,
                    'hmo_employee' => $philhealth_e,
                    'hmo_employeer' => $philhealth_ee,
                    'pagibig_employee' => $pagibig_e,
                    'pagibig_employer' => $pagibig_ee,
                    'withholding_tax' => $wt15,
                    'total_earnings' => $total_earnings,
                    'total_deductions' => $total_deductions,
                    'netpay' => $net,
                    'cola' => $cola,
                    'transpo' => $transpo,
                    'rice' => $rice,
                    'sss_loan' => $sssloan,
                    'pagibig_loan' => $hdmfloan,
                    'union_fees' => $pecewa,
                    'coop_loan' => $coop,
                    'hmo_deductions' => $hmo_deduction,
                    'other_deductions' => $other_deduction,
                    'leave_withoutpay' => $lwop,
                    'month' => $month,
                    'year' => $year,
                    'day' => $day,
                    'holidaypay' => $holidaypay,
                    'other_earnings' => $other_earnings_special,
                    'bonus_application' => $bonus_application_seg,
                    'adjustment_earnings' => $other_earnings_adjustment,
                    'electric_bills' => $elec,
                    'pagibig_add' => $pagibigadd
                );


                //insert to payroll table

                $success_insert_payroll_confid = $this->db->insert('prime_employee_payroll_transactions', $data_payroll_confid);

                if ($success_insert_payroll_confid) {
                    $title = "Processed Payroll";
                    $func = 'success';
                }
            }
            //end for confidential process here
        }
        $data['message'] = $message;
        $data['title'] = $title;
        $data['func'] = $func;

        echo json_encode($data);
    }

    // end function for process payroll


    function printpayslip()
    {
        // temp set month will query later
        $day = '';
        $month = '';
        $year = '';

        //end select table data
        // select table data
        $result = $this->db->query("select * from prime_employee_payroll_transactions");

        $this->load->library('fpdf/fpdf');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 6);
        $slip_cnt = 1;
        foreach ($result->result() as $row) {
            //query payclass
            $empid = $row->empid;
            $qry_payclass = $this->db->query("SELECT payclass_id FROM prime_employee_main_payclass where emp_id = $empid");
            foreach ($qry_payclass->result() as $row2) {
                $payclass = $row2->payclass_id;
            }
            //end query payclass


            $pdf->Cell(10, 6, 'Tiedra Ventures, INC.', 0);
            $pdf->Cell(40);
            $pdf->Cell(10, 6, 'PAYSLIP', 0);
            $pdf->Cell(10);
            $pdf->Cell(10, 6, $row->month . ' - ' . $row->day . ' - ' . $row->year, 0);
            $pdf->Ln(3);
            $pdf->Cell(10, 6, 'EMPNO:', 0);
            $pdf->Cell(10);

            $pdf->Cell(10, 6, $row->empid, 0);
            $pdf->Cell(10);
            $pdf->Cell(10, 6, $payclass, 0);
            $pdf->Ln(3);
            $pdf->Cell(10, 6, 'EARNINGS', 0);
            $pdf->Cell(80);
            $pdf->Cell(10, 6, 'DEDUCTION', 0);
            $pdf->Ln(3);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Basic:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->basic, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Overtime:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->otpay, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'SSS Premium:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->sss_employee, 2), 0);
            $pdf->Cell(10, 6, 'Cooperative:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->coop_fees, 2), 0);
            $pdf->Ln(3);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'COLA:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->cola, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Night Differential:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->ndpay, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'SSS Loans:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->sss_loan, 2), 0);
            $pdf->Cell(10, 6, 'Cooperative Loan:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->coop_loan, 2), 0);
            $pdf->Ln(3);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Others:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->other_earnings, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Adjustment Earnings:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->adjustment_earnings, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Pagibig Premium:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->pagibig_employee, 2), 0);
            $pdf->Cell(10, 6, 'HMO Deduction:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->hmo_deductions, 2), 0);
            $pdf->Ln(3);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Holiday Pay:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->holidaypay, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Transportation Allowance:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->transpo, 2), 0);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Pagibig Loans:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->pagibig_loan, 2), 0);
            $pdf->Cell(10, 6, 'Electric Bills:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->electric_bills, 2), 0);
            //
            ///
            $pdf->Ln(3);
            $pdf->Cell(5);
            $pdf->Cell(10, 6, 'Rice Allowance:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->rice, 2), 0);
            $pdf->Cell(50);
            $pdf->Cell(10, 6, 'PECEWA:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->union_fees, 2), 0);
            $pdf->Cell(10, 6, 'Others:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->other_deductions, 2), 0);
            $pdf->Ln(3);
            //

            $pdf->Cell(10, 6, 'Total Earnings:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->total_earnings, 2), 0);
            $pdf->Cell(55);
            $pdf->Cell(10, 6, 'PECEWA Loan:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->union_loan, 2), 0);
            $pdf->Cell(10, 6, 'Leave Without Pay:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->leave_withoutpay, 2), 0);
            $pdf->Ln(3);
            //
            $pdf->Cell(10, 6, 'Total Deduction:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->total_deductions, 2), 0);
            $pdf->Cell(55);
            $pdf->Cell(10, 6, 'Withholding Tax:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->withholding_tax, 2), 0);
            //add pagibig additional here
            $pdf->Cell(10, 6, 'Pagibig Additional:', 0);
            $pdf->Cell(20);
            $pdf->Cell(10, 6, number_format($row->pagibig_add, 2), 0);
            // end pagibig additional here
            $pdf->Ln(3);
            //
            //add additional print for confid
            if ($payclass > 128) {

                $pdf->Cell(10, 6, 'NET 15:', 0);
                $pdf->Cell(20);
                $pdf->Cell(10, 6, number_format(($row->netpay) / 2, 2), 0);
                $pdf->Cell(10, 6, 'NET 30:', 0);
                $pdf->Cell(20);
                $pdf->Cell(10, 6, number_format(($row->netpay) / 2, 2), 0);
            } else {
                $pdf->Cell(10, 6, 'NET PAY:', 0);
                $pdf->Cell(20);
                $pdf->Cell(10, 6, number_format($row->netpay, 2), 0);
            }
            //

            $pdf->Ln(4.5);
            //  $pdf->Cell(10, 6, '<hr>');
            $pdf->Cell(10, 6, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0);
            $pdf->Ln(3);

            if ($slip_cnt % 7 == 0) {
                $pdf->Ln(3);
            }
            $slip_cnt++;
        }

        $pdf->Output();
    }

    // function for insert additional earnings and deduction
    function insert_other_deductions_and_earnings()
    {
        $message = '';
        $title = 'Add Earning and Deductions';
        $func = '';
        //Including validation library
        //Setting values for tabel columns
        //initialize value
        $empid = $this->input->post('empid');
        $trntype = $this->input->post('transaction_type');
        $amount = $this->input->post('amount');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $day = $this->input->post('day');

        if ($trntype == 262) {
           $dailyrate = get_emp_basic_salary($empid)->daily;
           $amount = $amount * $dailyrate;
        }

        //end initialize
        $check = $this->model_payroll->check_data_other_deduction_and_earnings($empid, $trntype, $month, $year, $day);
        if ($check) {
            $message = 'Record Exists!';
            $func = 'warning';
        } else {

            $data = array(
                'empid' => $empid,
                'transaction_type' => $trntype,
                'amount' => $amount,
                'day' => $day,
                'month' => $month,
                'year' => $year
            );
            //Transfering data to Model
            $ins = $this->model_payroll->insert_other_deduction_and_earnings($data);

            if ($ins) {
                $message = 'Added';
                $func = 'success';
            } else {
                $message = 'Not Added';
                $func = 'error';
            }
        }

        $data['message'] = $message;
        $data['title'] = $title;
        $data['func'] = $func;

        echo json_encode($data);
    }

    // end function for insert additional earnings and deduction
    // generate dependents
    // end generate dependents

    function fetchmeterreaderemp()
    {
        $data = array();
        $regular = array('1', '3', '5', '6', '7', '8');
        $special = array('11');
        $getregval = 0;
        $getspval = 0;
        $getregdeduct = 0;
        $getspdeduct = 0;
        $getregtotal = 0;
        $getsptotal = 0;
        $done = false;
        $gdlbsum = 0;
        $totalamountsummary = 0;
        $totalamount = 0;
        $totaldeduction = 0;
        $overalltotal = 0;
        $groupid = 1;

        $getlastgroupid = $this->db->select("groupid")
            ->from("trn_reading_accomplishments_manual")
            ->order_by("groupid", "desc")
            ->limit(1)
            ->get()->row();
        if ($getlastgroupid) {
            $groupid = $getlastgroupid->groupid;
        } else {
            $groupid = 1;
        }

        $getlatestpayrollstart = $this->db->select("MAX(payrollstart) AS payrollstart")
            ->from("trn_reading_accomplishments_manual")
            ->get()->row();
        $getlatestpayrollend = $this->db->select("MAX(payrollend) AS payrollend")
            ->from("trn_reading_accomplishments_manual")
            ->get()->row();

        $sql = $this->db->select("pem.sysid,pem.empid,p.lastname,p.firstname, p.middlename")
            ->from("prime_employee_main pem")
            ->join("person p", "pem.sysid=p.sysid")
            ->join("prime_employee_main_positions pos", "pos.emp_id=pem.sysid AND pos.position_id=164")
            ->join("trn_reading_accomplishments_manual as tram", "tram.empid = pem.sysid", "left")
            ->where(array("payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend))
            ->group_by("pem.sysid")
            ->get();
        $num_rows = $sql->num_rows();
        if ($num_rows > 0) {

            $num = 1;
            foreach ($sql->result() as $row) {

                $regularvalinput = $this->db->select("rate, total ,deduction")
                    ->from("trn_reading_payroll_logs")
                    ->where(array("logsid" => $row->sysid, "ratetype" => 7, "status" => 306, "groupid" => $groupid))
                    ->get()->row();

                if ($regularvalinput) {
                    $getregdeduct = $regularvalinput->deduction;
                    $getregval = $regularvalinput->rate;
                    $done = true;
                    if ($getregval != null) {
                        $getregtotal = $regularvalinput->total * $getregval;
                    } else {
                        $getregtotal = 0;
                    }
                } else {
                    $getregdeduct = null;
                    $getregval = null;
                    $done = false;
                }

                $specialvalinput = $this->db->select("rate , total ,  deduction")
                    ->from("trn_reading_payroll_logs")
                    ->where(array("logsid" => $row->sysid, "ratetype" => 8, "status" => 306, "groupid" => $groupid))
                    ->get()->row();

                if ($specialvalinput) {
                    $getspdeduct = $specialvalinput->deduction;
                    $getspval = $specialvalinput->rate;
                    $done = true;
                    if ($getspval != null) {
                        $getsptotal = $specialvalinput->total * $getspval;
                    } else {
                        $getsptotal = 0;
                    }
                } else {
                    $getspdeduct = null;
                    $getspval = null;
                    $done = false;
                }

                $totalamountsummary += $getregtotal + $getsptotal;

                $gdlbcount = $this->db->select("COUNT(gdlbid) AS gdlbcount")
                    ->from("trn_reading_accomplishments_manual")
                    ->where(array("empid" => $row->sysid, "status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend, "groupid" => $groupid))
                    ->get()->row();

                if ($gdlbcount) {
                    $gdlbsum += $gdlbcount->gdlbcount;
                }

                $regtotal = $this->db->select("SUM(tram.readingcnt) AS regtotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $regular)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend, "groupid" => $groupid))
                    //add where date = latest date
                    ->get()->row();

                $sptotal = $this->db->select("SUM(tram.readingcnt) AS sptotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $special)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend, "groupid" => $groupid))
                    //add where date = latest date
                    ->get()->row();

                $errorregularcount = $this->db->select("SUM(tram.errors) AS regularerrortotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $regular)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend))
                    ->get()->row();

                $errorspecialcount = $this->db->select("SUM(tram.errors) AS specialerrortotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $special)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend))
                    ->get()->row();

                $totalamount = ($regtotal->regtotal * $getregval) + ($sptotal->sptotal * $getspval) - (($getregdeduct) + ($getspdeduct));
                $totalamountsummary = ($totalamountsummary) - (($getregdeduct) + ($getspdeduct));
                $totaldeduction += ($getregdeduct) + ($getspdeduct);
                $overalltotal += $totalamount;
                $data['meterreaderpayroll'][] = array(
                    'num' => $num++ . '<input id="dataid" type="hidden" value="' . $row->sysid . '"/>',
                    'empid' => $row->empid,
                    'fullname' => $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename,
                    'gdlb' => '<span class="gdlbtotalclass">' . $gdlbcount->gdlbcount . '</span>',
                    'regtotal' => number_format($regtotal->regtotal, 2) . '<input id="reghiddenval" type="hidden" value="' . $regtotal->regtotal . '"/>',
                    'sptotal' => number_format($sptotal->sptotal, 2) . '<input id="sphiddenval" type="hidden" value="' . $sptotal->sptotal . '"/>',
                    'regrate' => '<input class="form-control inline input-xs" type="text" style="width: 100%;" value="' . $getregval . '" id="regrateinputs" placeholder="Edit"  />',
                    'sprate' => '<input class="form-control inline input-xs" type="text" style="width: 100%;" value="' . $getspval . '" id="sprateinputs" placeholder="Edit"  />',
                    'regdeduct' => '<span id="totalregdeduct">' . number_format($errorregularcount->regularerrortotal * 50, 2) . '</span> <input id="errorregularcount" type="hidden" value="' . ($errorregularcount->regularerrortotal * 50) . '" />',
                    'spdeduct' => '<span id="totalspdeduct">' . number_format($errorspecialcount->specialerrortotal * 50, 2) . '</span> <input id="errorspecialcount" type="hidden" value="' . ($errorspecialcount->specialerrortotal * 50) . '" />',
                    'total' => '<span id="totaltext">' . number_format($totalamount, 2) . '</span> <input type="hidden" value="' . $totalamount . '"  id="totalinput" />',
                    'done' => $done
                );
                $getsptotal = 0;
                $getregtotal = 0;
            }
        }
        $data['overalltotal'] = $overalltotal;
        $data['regulardeduct'] = $getregdeduct;
        $data['specialdeduct'] = $getspdeduct;
        $data['totaldeduction'] = $totaldeduction;
        $data['totalamountsummary'] = $totalamountsummary;
        $data['gdlbsum'] = $gdlbsum;
        echo json_encode($data);
    }

    function getdistrictcodes($data)
    {
        $codes = $this->db->select("codes")
            ->from("address_districts")
            ->where(array("sysid" => $data))
            ->get()->row();
        return $codes->codes;
    }

    function insertpayrolllogs()
    {
        $data = array();
        $q = false;
        $msg = '';
        $groupid = 1;

        $logsid = $this->input->post('logsid');
        $regrateinput = $this->input->post('regrateinput');
        $sprateinput = $this->input->post('sprateinput');
        $regdeductinput = $this->input->post('regdeduct');
        $spdeductinput = $this->input->post('spdeduct');
        $regval = $this->input->post('regval');
        $spval = $this->input->post('spval');

        $totalamount = 0;
        $totaldeduct = 0;
        $overalltotal = 0;
        $indexval = 0;

        $this->db->trans_begin();

        $getlastgroupid = $this->db->select("groupid")
            ->from("trn_reading_accomplishments_manual")
            ->order_by("groupid", "desc")
            ->limit(1)
            ->get()->row();
        if ($getlastgroupid) {
            $groupid = $getlastgroupid->groupid;
        } else {
            $groupid = 1;
        }

        $this->db->where(array("logsid" => $logsid, "status" => 306));
        $this->db->update("trn_reading_payroll_logs", array("updatedby" => user_id(), "status" => 0));


        if ($regrateinput === '') {
            $regrateinput = 0;
        }
        if ($sprateinput === '') {
            $sprateinput = 0;
        }
        if ($regval === '') {
            $regval = 0;
        }
        if ($spval === '') {
            $spval = 0;
        }
        if ($regdeductinput === '') {
            $regdeductinput = 0;
        }
        if ($spdeductinput === '') {
            $spdeductinput = 0;
        }

        //  $totalamount = (($regrateinput * $regval) + ($sprateinput * $spval));
        // $totaldeduct = (($regdeductinput) + ($spdeductinput));
        //  $overalltotal = $totalamount - $totaldeduct;

        $insertval = array(
            array(
                'groupid' => $groupid,
                'logsid' => $logsid,
                'rate' => $regrateinput,
                'deduction' => $regdeductinput,
                'total' => $regval,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'ratetype' => 7
            ), array(
                'groupid' => $groupid,
                'logsid' => $logsid,
                'rate' => $sprateinput,
                'deduction' => $spdeductinput,
                'total' => $spval,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'ratetype' => 8
            )
        );

        $this->db->insert_batch("trn_reading_payroll_logs", $insertval);
        $msg = $this->db->_error_message();

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $q = true;
        } else {
            $this->db->trans_rollback();
            $data['msg'] = $msg;
        }

        $getdeduction = $this->db->select("SUM(deduction) AS totaldeduct")
            ->from("trn_reading_payroll_logs")
            ->where(array("status" => 306))
            ->get()->row();
        $totaldeduct = $getdeduction->totaldeduct;

        $totalamount = $this->db->select("rate , total")
            ->from("trn_reading_payroll_logs")
            ->where(array("status" => 306))
            ->get();

        foreach ($totalamount->result() as $row) {
            $indexval += ($row->rate * $row->total);
        }
        $totalamount = $indexval;
        $overalltotal = $indexval - $totaldeduct;

        $data['totaldeduct'] = $totaldeduct;
        $data['totalamount'] = $totalamount;
        $data['overalltotal'] = $overalltotal;
        $data['qry'] = $q;
        echo json_encode($data);
    }

    function fetchotherearninganddeduct()
    {
        $data = array();

        $sql = $this->db->select("po.sysid, po.empid,pt.desc , po.amount")
            ->from("payroll_other_earnings_and_deductions_trn AS po")
            ->join("prime_types_parameter AS pt", "pt.sysid = po.transaction_type", "left")
            ->where(array("po.status" => 1))
            ->get();

        if ($sql->num_rows() > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {
                $data['otherearningsanddeductdata'][] = array(
                    "num" => $num++,
                    "empid" => $row->empid,
                    "type" => $row->desc,
                    "amount" => $row->amount
                );
            }
        }

        echo json_encode($data);
    }

    function getemplist()
    {
        $data = array();
        if (select_employee()->num_rows() > 0) {
            foreach (select_employee()->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->sysid . ' - ' . $row->lastname . ', ' . $row->firstname
                );
            }
        }
        echo json_encode($data);
    }

    function gettypes()
    {
        $data = array();
        if (select_earning_deduction_type()->num_rows() > 0) {
            foreach (select_earning_deduction_type()->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes . ' - ' . $row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function getconttypetable()
    {
        $data = array();
        $conttype = $this->input->post('conttype');

        $sql = $this->db->select('pcm.sysid,pcm.amtbase,pcm.amtmin,pcm.amtmax,pcm.amtcont,pcm.rateemployee,pcm.rateemployer,pcm.var ,pcm.types , pcm.status , pcm.datecreated , pcm.createdby')
            ->from("prime_contribution_matrix AS pcm")
            ->join("prime_types_parameter AS ptp", "ptp.sysid = pcm.conttype", "left")
            ->where(array("pcm.conttype" => $conttype, "pcm.status" => 1))
            ->get();

        if ($sql->num_rows() > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {
                $data['contributiondata'][] = array(
                    "num" => $num++,
                    "base" => number_format($row->amtbase, 2),
                    "min" => number_format($row->amtmin, 2),
                    "max" => number_format($row->amtmax, 2),
                    "amtcont" => number_format($row->amtcont, 2),
                    "rateemployee" => $row->rateemployee,
                    "rateemployer" => $row->rateemployer,
                    "var" => $row->var,
                    "types" => $row->types,
                    "datecreated" => $row->datecreated,
                    "createdby" => $row->createdby,
                    "control" => '<div class="btn-group">
                                  <button class="btn btn-danger inline  btn-xs" data-id="' . $row->sysid . '" id="delbtn"><i class="fa fa-trash"></i></button>
                                   <a href="#form_edit_contribution" id="editcontributionbtn" data-toggle="ajax-modal" data-arr="' . $conttype . '" data-view="' . $row->sysid . '" class="btn btn-primary  btn-xs inline"><i class="fa fa-pencil"></i></a>
                                  </div>
                                 '
                );
            }
        }

        echo json_encode($data);
    }

    function deletecontribution()
    {
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("prime_contribution_matrix", array("status" => 0));
        if ($this->db->trans_status() == true && $sql) {
            $this->db->trans_commit();
            $msg = 'Item has been deleted.';
            $func = 'success';
            $qry = true;
        } else {
            $this->db->trans_rollback();
            $msg = 'Fail to delete this item';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function addpayrolltransactions()
    {
        $data = array();
        $type = $this->input->post('type');
        $amt = $this->input->post('amt');
        $empid = $this->input->post('empid');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $trntype = $this->input->post('trntype');
        $paytype = $this->input->post('paytype');
        $paytypepopover = $this->input->post('payspec');
        $payclass = $this->input->post('payclass');
        $insertedamount = $amt;
        
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("empid" => $empid, "typesid" => $type, "months" => $month, "years" => $year, "paytype" => $paytype, "status" => 1));
        $this->db->update("payroll_transactions", $updatearr);

        //for holiday
        /*  if($type == 263){
              $empsalary = $this->db->select("amt")->from("prime_employee_salary")
                  ->where(array("empid" => $empid , "status" => 1))->get()->row();
              if($empsalary){
                  $amt = $amt * (($empsalary->amt * 12) / $divider);
              }
          } */

        //for OT Weekdays
        if ($type == 359) {
            $hourlyrate = get_emp_basic_salary($empid)->hourly;
            $amt = $hourlyrate * 1.25 * $amt;
        }
        //for OT Weekend
        if ($type == 1082) {
            $hourlyrate = get_emp_basic_salary($empid)->hourly;
            $amt = $hourlyrate * 1.30 * $amt;
        }
        // for OT w/ Holiday
        if ($type == 3010) {
            $hourlyrate = get_emp_basic_salary($empid)->hourly;
            $amt = $hourlyrate * 1.60 * $amt;
        }

        //for ND
        if ($type == 358) {
            $hourlyrate = get_emp_basic_salary($empid)->hourly;
            $amt = $amt * 0.25 * $hourlyrate;
        }
        //for LWOP
        if ($type == 262) {
            $dailyrate = get_emp_basic_salary($empid)->daily;
            $amt = $amt * $dailyrate;
             //Hardcode for Segula, Marianito Jr.
            if($empid == 988 || $empid == '988'){
               $amt = 1107.75;
            }
        }

        $data['amountcomputed'] = $amt;
        $insarr = array(
            'empid' => $empid,
            'typesid' => $type,
            'amt' => $amt,
            'insertamount' => $insertedamount,
            'months' => $month,
            'years' => $year,
            'paytype' => $paytypepopover,
            'payspec' => ($paytype) ? $paytype : 0,
            'payclass' => $payclass,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );

        $this->db->insert("payroll_transactions", $insarr);
        $data['transactioninserterror'] = $this->db->_error_message();

        $data['empid'] = $empid;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['paytype'] = $paytype;
        $data['popover'] = $paytypepopover;

        $compute = compute_employee_netpay($empid, $month, $year, $paytype, $paytypepopover, $payclass, 1);
        $data['taxamt'] = $compute->taxamt;
        if ($paytype == $paytypepopover || $paytypepopover == 0) {
            if ($trntype == 0) {
                $data['loans'] = $compute->loans;
                $data['deductions'] = $compute->deductionamount;
            }
            if ($trntype == 1) {
                $data['earnings'] = $compute->earnings;
                $data['deductions'] = $compute->deductionamount;
            }
            if ($trntype == 2) {
                $data['deductions'] = $compute->deductionamount;
            }
            $data['transactiontype'] = $trntype;
            $data['netpay'] = $compute->netpay;
        }
        echo json_encode($data);
    }

    function getpayslippreview()
    {
        echo $this->model_reports->get_payslip_data();
    }

    function getreportsdata()
    {

        $data = array();
        $payclass = $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $period = $this->input->post('payrollperiod');
        // $paytype = $this->input->post('paytype');
        $specific = $this->input->post('specific');
        $export = $this->input->post('exporttxt');
        $textname = '';
        $earningsarr = array();
        $deductionsarr = array();
        $empcount = 0;
        $data_details = '';
        $payslip = true;
        $prtgstatus = 0;

        $data = array();
        $qry = false;
        $filename = '';


        /*  if($period > 0 ){
              $period = $period;
          }else{
              $period = $paytype;
          } */

        $payrollperiod = '';
        $msg = '';
        $func = '';
        $process = false;
        $paytext = '';
        $scurrent = '';
        $systempath = '';
        $downloadpath = '';

        if (user_id() > 0) {
            if (empty($payclass) || empty($month) || empty($year)) {
                $validation = false;
            } else {

                $validation = true;
                $data['validation'] = $validation;
                if ($month) {
                    $dateObj = DateTime::createFromFormat('!m', $month);
                    $monthName = $dateObj->format('F');
                    $data['month'] = $monthName;
                }


                if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                    $prtgstatus = 1;
                    //rankfile
                    $data['prtgstatus'] = $prtgstatus;
                    // $this->db->where(array("prg.years" => $year , "prg.months" => $month , "prg.payclass" => $payclass , "prg.paytype" => $period , "prg.status" => 301));
                    $this->db->where(array("prg.years" => $year, "prg.months" => $month, "prg.payclass" => $payclass, "prg.paytype" => $period));
                    if ($period == 1) {
                        $payrollperiod = 15;
                    } else if ($period == 2) {
                        $payrollperiod = 30;
                    }
                } else if ($payclass == 1) {
                    $prtgstatus = 301;
                    $payrollperiod = '';
                    //confidential
                    // $this->db->where(array("prg.years" => $year , "prg.months" => $month , "prg.payclass" => $payclass ,"prg.status" => 301));
                    $this->db->where(array("prg.years" => $year, "prg.months" => $month, "prg.payclass" => $payclass));
                }
                if ($specific > 0) {
                    $this->db->where(array("prm.empid" => $specific));
                }
                $data['prtgstatus'] = $prtgstatus;
                $data['year'] = $year;
                $data['months'] = $month;
                $data['paytype'] = $period;
                $data['payclass'] = $payclass;
                $sql = $this->db->select("pem.sysid, payrollemp.accntno, prg.months,prg.years, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net")
                    ->from("payroll_reports_main as prm")
                    ->join("payroll_emplist as payrollemp", "payrollemp.empid = prm.empid", "left")
                    ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                    ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                    ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                    ->join("person as p", "p.sysid = pem.personid", "left")
                    ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid", "left")
                    ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid", "left")
                    ->join("prime_employee_main_payclass as pemp", "pemp.emp_id = pem.sysid", "left")
                    ->where(array("prg.status != " => 302, 'pec.type' => 1, "pec.status" => 1, "payrollemp.status" => 1))
                    ->group_by("pem.sysid,payrollemp.accntno,prg.months,prg.years , pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net")
                    ->order_by("p.lastname")
                    ->get();
                $data['error'] = $this->db->_error_message();
                $data['query'] = $this->db->last_query();
                if ($sql->num_rows() > 0) {
                    $data['result'] = $sql->result();

                    $totalnetpayall = 0;
                    if ($export == true) {
                        if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                            if ($period == 1) {
                                $paytext = '1st Half';
                            } else {
                                $paytext = '2nd Half';
                            }
                            $textname = 'IX7';
                        } else {
                            $textname = 'IX7';
                            $paytext = '';
                        }
                        $loc = "D:\Payroll Bank Files\\";
                        //$filepath = $loc.$textname.str_pad($month,2,"0",STR_PAD_LEFT).date('d').substr( $year, -2)."01.txt";
                        $upload_path = FCPATH . 'uploads/payroll/';
                        if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                            $rank_nfile_folder = $upload_path . 'rankandfile';
                            if (!is_dir($upload_path)) {
                                mkdir($upload_path, 0775, true);
                                if (!is_dir($rank_nfile_folder)) {
                                    mkdir($rank_nfile_folder, 0775, true);
                                } else {
                                    //     chmod($upload_path, 0777);
                                }
                            } else {
                                //   chmod($upload_path, 0777);
                                if (!is_dir($rank_nfile_folder)) {
                                    mkdir($rank_nfile_folder, 0775, true);
                                } else {
                                    // chmod($upload_path, 0777);
                                }
                            }
                            if ($payclass == 128) {
                                $downloadpath = FCPATH . "uploads\\payroll\\rankandfile\\" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                                $systempath = FCPATH . "uploads/payroll/rankandfile/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                            } else if ($payclass == 3077) {
                                $downloadpath = FCPATH . "uploads\\payroll\\tierd1\\" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                                $systempath = FCPATH . "uploads/payroll/tierd1/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                            } else if ($payclass == 3078) {
                                $downloadpath = FCPATH . "uploads\\payroll\\tierd2\\" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                                $systempath = FCPATH . "uploads/payroll/tierd2/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                            }
                        } else {

                            $confi_folder = $upload_path . 'confidential';
                            if (!is_dir($upload_path)) {
                                mkdir($upload_path, 0775, true);
                                if (!is_dir($confi_folder)) {
                                    mkdir($confi_folder, 0775, true);
                                } else {
                                    //  chmod($upload_path, 0777);
                                }
                            } else {
                                if (!is_dir($confi_folder)) {
                                    mkdir($confi_folder, 0775, true);
                                } else {
                                    //     chmod($upload_path, 0777);
                                }
                            }
                            $systempath = FCPATH . "uploads/payroll/confidential/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                            $downloadpath = FCPATH . "uploads\\payroll\\confidential\\" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
                        }
                        //fopen($filepath, "w");
                        fopen($systempath, "w");
                        file_put_contents($systempath, "H         10810132451       IX7" . $year . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . "\r\n");
                    }
                    if ($export == true) {
                        $scurrent = file_get_contents($systempath);
                    }
                    $num = 1;
                    $process = true;
                    foreach ($sql->result() as $row) {
                        $empid = $row->sysid;
                        $basic = $row->basic;
                        /* start of payslip */

                        $data['empids'][] = array(
                            'id' => $empid
                        );

                        $qry_emp = $this->db->select('e.sysid, e.empid, e.personid, e.status, p.firstname, p.lastname, p.middlename, p.gender, p.birthdate , pes.amt')
                            ->from("prime_employee_main e")
                            ->join("person p", "e.personid = p.sysid")
                            ->join("prime_employee_salary as pes", "pes.empid = e.sysid", "left")
                            ->where(array('e.sysid' => $empid, 'e.status' => 1, "pes.status" => 1))
                            ->get()->row();


                        $qry_getpayclass = $this->db->select('payclass_id AS payclassid')
                            ->from('prime_employee_main_payclass')
                            ->where(array('emp_id' => $empid, 'status' => 1))
                            ->get()->row();

                        if ($qry_emp && $qry_getpayclass) {
                            $empid = $qry_emp->sysid;
                            $empclass = $qry_getpayclass->payclassid;
                            $person_info = get_person_info($qry_emp->personid);


                            if ($person_info && isset($person_info->info)) {
                                $info = $person_info->info;
                                $compute = get_payslip_trn($empid, $month, $year, $period, $payclass);
                                $data['compute'][] = $compute;

                            } else {
                                $func = 'warning';
                                $data_details .= '<div class="col-md-12"><h4><i class="fa fa-warning"></i> Unable to retrive person\'s data!</h4></div>';
                            }
                        } else {
                            $func = 'warning';
                        }
                        if ($payslip == true) {
                            $data_details .= '<hr>';
                        }


                        /* start of payslip */
                        if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                            if ($export == true) {
                                $basic = $basic / 2;
                            }
                        }

                        $getcola = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 251))->get()->row();
                        $cola = ($getcola) ? (number_format($getcola->amt, 2, '.', '')) : 0;

                        $getotheradd = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 266))->get()->row();
                        $otheradd = ($getotheradd) ? (number_format($getotheradd->amt, 2, '.', '')) : 0;

                        $getholidaypay = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 263))->get()->row();
                        $holidaypay = ($getholidaypay) ? (number_format($getholidaypay->amt, 2, '.', '')) : 0;

                        $getotpayweekends = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 1082))->get()->row();
                        $otpayweekends = ($getotpayweekends) ? (number_format($getotpayweekends->amt, 2, '.', '')) : 0;

                        $getotpayweekdays = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 359))->get()->row();
                        $otpayweekdays = ($getotpayweekdays) ? (number_format($getotpayweekdays->amt, 2, '.', '')) : 0;

                        $otpay = $otpayweekends + $otpayweekdays;

                        $getnitediff = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 358))->get()->row();
                        $nitediff = ($getnitediff) ? (number_format($getnitediff->amt, 2, '.', '')) : 0;

                        $getadjustments = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 375))->get()->row();
                        $adjustments = ($getadjustments) ? (number_format($getadjustments->amt, 2, '.', '')) : 0;

                        //trans allw
                        $gettransallw = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 252))->get()->row();
                        $otheradd += ($gettransallw) ? (number_format($gettransallw->amt, 2, '.', '')) : 0;


                        //rice subsi
                        $getrice = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 253))->get()->row();
                        $otheradd += ($getrice) ? (number_format($getrice->amt, 2, '.', '')) : 0;

                        //acting allw
                        $getactingallw = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 360))->get()->row();
                        $otheradd += ($getactingallw) ? (number_format($getactingallw->amt, 2, '.', '')) : 0;


                        $getssscont = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 72))->get()->row();
                        $ssscont = ($getssscont) ? (number_format($getssscont->amt, 2, '.', '')) : 0;

                        $getsssloan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 257))->get()->row();
                        $sssloan = ($getsssloan) ? (number_format($getsssloan->amt, 2, '.', '')) : 0;

                        $gethdmfcont = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 74))->get()->row();
                        $hdmfcont = ($gethdmfcont) ? (number_format($gethdmfcont->amt, 2, '.', '')) : 0;

                        $gethdmfloan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 258))->get()->row();
                        $hdmfloan = ($gethdmfloan) ? (number_format($gethdmfloan->amt, 2, '.', '')) : 0;

                        $getagencyfee = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 1009))->get()->row();
                        $agencyfee = ($getagencyfee) ? (number_format($getagencyfee->amt, 2, '.', '')) : 0;

                        $getpecewaloan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 254))->get()->row();
                        $pecewa = ($getpecewaloan) ? (number_format($getpecewaloan->amt, 2, '.', '')) : 0;

                        $getcooploan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 255))->get()->row();
                        $coop = ($getcooploan) ? (number_format($getcooploan->amt, 2, '.', '')) : 0;

                        $gethmodedn = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 260))->get()->row();
                        $hmodedn = ($gethmodedn) ? (number_format($gethmodedn->amt, 2, '.', '')) : 0;

                        $getelectbill = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 256))->get()->row();
                        $electbill = ($getelectbill) ? (number_format($getelectbill->amt, 2, '.', '')) : 0;

                        $getotherdedn = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 261))->get()->row();
                        $otherdedn = ($getotherdedn) ? (number_format($getotherdedn->amt, 2, '.', '')) : 0;

                        $getlwop = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 262))->get()->row();
                        $lwop = ($getlwop) ? (number_format($getlwop->amt, 2, '.', '')) : 0;
                        $withholdingtax = $row->tax;
                        $totalearnings = number_format($row->earnings, 2, '.', '');
                        // $totaldeductions = number_format($row->deductions, 2, '.', '');
                        $totaldeductions = number_format($row->deductions, 2, '.', '');
                        $totalnetpay = $row->net;
                        $tax = $row->tax;

                        $data['payrollreportdata'][] = array(
                            "expand" => $empid,
                            "num" => $num++,
                            "empcode" => $row->empid,
                            "name" => $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename,
                            "department" => $row->names,
                            "basic" => number_format($basic, 2),
                            "cola" => number_format($cola, 2),
                            "others" => number_format($otheradd, 2),
                            "holidays" => number_format($holidaypay, 2),
                            "otpay" => number_format($otpay, 2),
                            "nitediff" => number_format($nitediff, 2),
                            "adjustments" => number_format($adjustments, 2),
                            "ssscont" => number_format($ssscont, 2),
                            "sssloan" => number_format($sssloan, 2),
                            "hdmfcont" => number_format($hdmfcont, 2),
                            "hdmfloan" => number_format($hdmfloan, 2),
                            "pecewa" => number_format($pecewa, 2),
                            "corporate" => number_format($coop, 2),
                            "hmodedn" => number_format($hmodedn, 2),
                            "electbills" => number_format($electbill, 2),
                            "othersdedn" => number_format($otherdedn, 2),
                            "leavewithoutpay" => number_format($lwop, 2),
                            "withholdingtax" => number_format($withholdingtax, 2),
                            "tax" => number_format($tax, 2),
                            "agencyfee" => number_format($agencyfee, 2),
                            "deductions" => number_format($totaldeductions, 2),
                            "earnings" => number_format($totalearnings, 2),
                            "netpay" => number_format($totalnetpay, 2)
                        );


                        if ($export == true) {
                            if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {

                                $qry_bank_file = query_bankfile_records($empid, $row->accntno, $payclass, $period, $totalnetpay, 0, $month, $year);
                                $scurrent .= '0' . $row->accntno . "      " . number_format($totalnetpay, 2, '.', '') . "\r\n";
                                // Write the contents back to the file
                                file_put_contents($systempath, $scurrent);

                                $data['rankandfilenetpay'][] = array(
                                    'accntno' => $row->accntno,
                                    'netpay' => $totalnetpay
                                );

                                $empcount++;
                                $totalnetpayall += $totalnetpay;

                            } else {

                                // @TODO create record thant 15 is already release to get the 30..

                                $getenddate = $this->db->select("dateend")->from("prime_employee_main")
                                    ->where(array("sysid" => $empid))->get()->row();

                                if ($getenddate && $getenddate->dateend != null && $getenddate->dateend != '0000-00-00') {


                                    $date_15 = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-1'; // or your date as well
                                    $earlier = new DateTime($getenddate->dateend);
                                    $later = new DateTime($date_15);

                                    $diff = $later->diff($earlier)->format("%a");
                                    if ($diff > 15) {
                                        $firsthalftype = array(266, 265);
                                        $qry_other_ernings = $this->db->select()
                                            ->from('payroll_transactions')
                                            ->where(array('paytype' => 1, 'empid' => $empid, "months" => $month, "years" => $year))
                                            ->where_in('typesid', $firsthalftype)
                                            ->get()->row();

                                        if ($qry_other_ernings) {
                                            $other_eraning_15 = $qry_other_ernings->amt;
                                            $other_earning_half = $other_eraning_15 / 2;
                                            $net_15 = (($totalnetpay / 2) - $other_earning_half) + $other_eraning_15;
                                            $net_30 = ($totalnetpay / 2) - $other_earning_half;
                                        } else {

                                            $net_15 = ($totalnetpay / 2);
                                            //$net_30 = ($totalnetpay / 2);
                                            $net_30 = ($totalnetpay - $net_15);
                                        }

                                        $qry_bank_file = query_bankfile_records($empid, $row->accntno, 129,1, $net_15, $net_30, $month, $year);
                                        if ($qry_bank_file->inserterror == '') {
                                            $totalnetpay_final = $net_30;
                                            $totalnetpayall += $net_30;
                                        }
                                    } else {
                                        //  KUNG INDI SYA KA LAB-OT SA 30
                                        $qry_bank_file = query_bankfile_records($empid, $row->accntno, 129, 1, $totalnetpay, 0, $month, $year);
                                        if ($qry_bank_file->inserterror == '') {
                                            $totalnetpay_final = $totalnetpay;
                                            $totalnetpayall += $totalnetpay;
                                        }
                                    }
                                } else {
                                    $firsthalftype = array(266);
                                    $qry_other_ernings = $this->db->select()->from('payroll_transactions')
                                        ->where(array('paytype' => 1, 'empid' => $row->sysid, "months" => $month, "years" => $year))
                                        ->where_in('typesid', $firsthalftype)
                                        ->get()->row();

                                    if ($qry_other_ernings) {
                                        $other_eraning_15 = $qry_other_ernings->amt;
                                        $other_earning_half = $other_eraning_15 / 2;
                                        $net_15 = (($totalnetpay / 2) - $other_earning_half) + $other_eraning_15;
                                        $net_30 = ($totalnetpay / 2) - $other_earning_half;
                                    } else {
                                        $net_15 = ($totalnetpay / 2);
                                        //$net_30 = ($totalnetpay / 2);
                                        $net_30 = ($totalnetpay - $net_15);
                                    }

                                    $qry_bank_file = query_bankfile_records($empid, $row->accntno, 129, $period, $net_15, $net_30, $month, $year);
                                    if ($qry_bank_file->inserterror == '') {
                                        $totalnetpay_final = round($net_30, 2);
                                        $totalnetpayall += $totalnetpay_final;
                                        $data['totalnetpayall'][] = $totalnetpayall;
                                    }
                                }

                                if ($totalnetpay_final > 0) {
                                    $scurrent .= '0' . $row->accntno . "      " . number_format($totalnetpay_final, 2, '.', '') . "\r\n";
                                    // Write the contents back to the file

                                    $data['confidentialnetpay'][] = array(
                                        'accntno' => $row->accntno,
                                        'netpay' => $totalnetpay_final
                                    );
                                    file_put_contents($systempath, $scurrent);
                                }
                                $empcount++;
                            }
                        }
                    }
                    if ($export == true) {
                        if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                            $scurrent .= "T        " . number_format($empcount . $totalnetpayall, 2, '.', '') . "\r\n";
                        } else {
                            $scurrent .= "T        " . number_format($empcount . $totalnetpayall, 2, '.', '') . "\r\n";
                        }

                        file_put_contents($systempath, $scurrent);
                    }


                    $data['count'] = $num;
                } else {
                    $process = false;
                    $msg = 'No payroll to process or payroll not yet approve.';
                    $func = 'info';

                }
            }
            if ($period == 1) {
                $payrollperiod = 1 . '-' . 15;
            } else if ($period == 2) {
                $payrollperiod = 16 . '-' . 30;
            }

            if ($payclass == 128) {
                $text = 'R';

            } else if ($payclass == 3077) {
                $text = 'T1';
            } else if ($payclass == 3078) {
                $text = 'T2';
            } else {
                $text = 'C';
            }
            $data['text'] = $text;

            //$data['filenamebank'] = $text.$year.'-'.$this->convertmonth($month).'-'.$payhalftext.'.txt';
            if ($payclass == 128) {
                $data['filenamebank'] = base_url() . "payroll/downloadbankfile/rankandfile/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
            } else if ($payclass == 3077) {
                $data['filenamebank'] = base_url() . "payroll/downloadbankfile/tierd1/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
            } else if ($payclass == 3078) {
                $data['filenamebank'] = base_url() . "payroll/downloadbankfile/tierd2/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";
            } else {
                $data['filenamebank'] = base_url() . "payroll/downloadbankfile/confidential/" . $textname . str_pad($month, 2, "0", STR_PAD_LEFT) . date('d') . substr($year, -2) . "01.txt";

            }
        } else {
            $validation = false;
            $process = false;
            $msg = 'Sessino timeout!';
            $func = 'warning';
        }
        $data['systempath'] = $downloadpath;
        $data['month'] = $this->getmonthname($month);
        $data['year'] = $year;
        $data['payclass'] = $payclass;
        $data['paytype'] = $period;
        $data['process'] = $process;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['period'] = ($payrollperiod) ? $payrollperiod : false;
        $data['validation'] = $validation;
        $data['earningssarr'] = $earningsarr;
        $data['deductionsarr'] = $deductionsarr;
        $data['html'] = $data_details;
        $data['inserterror'] = (isset($qry_bank_file) && $qry_bank_file->inserterror != '') ? $qry_bank_file->inserterror : '';
        echo json_encode($data);
    }

    function exportexceldata($payclass, $month, $year, $period)
    {

        $data = array();
        $specific = $this->input->post('specific');

        //load our new PHPExcel library
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet


        $specific = $this->input->post('specific');
        $export = $this->input->post('exporttxt');
        $textname = '';
        $earningsarr = array();
        $deductionsarr = array();
        $empcount = 0;
        $data_details = '';
        $payslip = true;
        $prtgstatus = 0;

        $data = array();

        $excel_data_num = 3;
        $qry = false;
        $filename = '';

        $num = 0;
        $total_net = 0;
        $excel_arr = array();
        if (user_id() > 0) {
            if (empty($payclass) || empty($month) || empty($year)) {
                $validation = false;
            } else {

                $validation = true;
                $data['validation'] = $validation;
                if ($month) {
                    $dateObj = DateTime::createFromFormat('!m', $month);
                    $monthName = $dateObj->format('F');
                    $data['month'] = $monthName;
                }


                if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                    $prtgstatus = 1;
                    //rankfile
                    $data['prtgstatus'] = $prtgstatus;
                    // $this->db->where(array("prg.years" => $year , "prg.months" => $month , "prg.payclass" => $payclass , "prg.paytype" => $period , "prg.status" => 301));
                    $this->db->where(array("prg.years" => $year, "prg.months" => $month, "prg.payclass" => $payclass, "prg.paytype" => $period));
                    if ($period == 1) {
                        $payrollperiod = 15;
                    } else if ($period == 2) {
                        $payrollperiod = 30;
                    }
                } else if ($payclass == 1) {
                    $prtgstatus = 301;
                    $payrollperiod = '';
                    //confidential
                    // $this->db->where(array("prg.years" => $year , "prg.months" => $month , "prg.payclass" => $payclass ,"prg.status" => 301));
                    $this->db->where(array("prg.years" => $year, "prg.months" => $month, "prg.payclass" => $payclass));
                }
                if ($specific > 0) {
                    $this->db->where(array("prm.empid" => $specific));
                }
                $data['prtgstatus'] = $prtgstatus;
                $data['year'] = $year;
                $data['months'] = $month;
                $data['paytype'] = $period;
                $data['payclass'] = $payclass;
                $sql = $this->db->select("pem.sysid, payrollemp.accntno, prg.months,prg.years, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net")
                    ->from("payroll_reports_main as prm")
                    ->join("payroll_emplist as payrollemp", "payrollemp.empid = prm.empid", "left")
                    ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                    ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                    ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                    ->join("person as p", "p.sysid = pem.personid", "left")
                    ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid", "left")
                    ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid", "left")
                    ->join("prime_employee_main_payclass as pemp", "pemp.emp_id = pem.sysid", "left")
                    ->where(array("prg.status = " => 301, 'pec.type' => 1, "pec.status" => 1, "payrollemp.status" => 1))
                    ->group_by("pem.sysid,payrollemp.accntno,prg.months,prg.years , pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net")
                    ->order_by("p.lastname")
                    ->get();
                $data['error'] = $this->db->_error_message();
                $data['query'] = $this->db->last_query();
                if ($sql->num_rows() > 0) {
                    //$data['result'] = $sql->result();

                    $totalnetpayall = 0;

                    foreach ($sql->result() as $row) {
                        $empid = $row->sysid;
                        $basic = $row->basic;
                        /* start of payslip */

                        $data['empids'][] = array(
                            'id' => $empid
                        );



                        $qry_emp = $this->db->select('e.sysid, e.empid, e.personid, e.status, p.firstname, p.lastname, p.middlename, p.gender, p.birthdate , pes.amt')
                            ->from("prime_employee_main e")
                            ->join("person p", "e.personid = p.sysid")
                            ->join("prime_employee_salary as pes", "pes.empid = e.sysid", "left")
                            ->where(array('e.sysid' => $empid, 'e.status' => 1, "pes.status" => 1))
                            ->get()->row();


                        $qry_getpayclass = $this->db->select('payclass_id AS payclassid')
                            ->from('prime_employee_main_payclass')
                            ->where(array('emp_id' => $empid, 'status' => 1))
                            ->get()->row();

                        if ($qry_emp && $qry_getpayclass) {
                            $empid = $qry_emp->sysid;
                            $empclass = $qry_getpayclass->payclassid;
                            $person_info = get_person_info($qry_emp->personid);


                            if ($person_info && isset($person_info->info)) {
                                $info = $person_info->info;
                                $compute = get_payslip_trn($empid, $month, $year, $period, $payclass);
                                $data['compute'][] = $compute;

                            } else {
                                $func = 'warning';
                                $data_details .= '<div class="col-md-12"><h4><i class="fa fa-warning"></i> Unable to retrive person\'s data!</h4></div>';
                            }
                        } else {
                            $func = 'warning';
                        }
                        if ($payslip == true) {
                            $data_details .= '<hr>';
                        }


                        /* start of payslip */
                        if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                            if ($export == true) {
                                $basic = $basic / 2;
                            }
                        }

                        $getcola = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 251))->get()->row();
                        $cola = ($getcola) ? (number_format($getcola->amt, 2, '.', '')) : 0;

                        $getotheradd = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 266))->get()->row();
                        $otheradd = ($getotheradd) ? (number_format($getotheradd->amt, 2, '.', '')) : 0;

                        $getholidaypay = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 263))->get()->row();
                        $holidaypay = ($getholidaypay) ? (number_format($getholidaypay->amt, 2, '.', '')) : 0;

                        $getotpayweekends = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 1082))->get()->row();
                        $otpayweekends = ($getotpayweekends) ? (number_format($getotpayweekends->amt, 2, '.', '')) : 0;

                        $getotpayweekdays = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 359))->get()->row();
                        $otpayweekdays = ($getotpayweekdays) ? (number_format($getotpayweekdays->amt, 2, '.', '')) : 0;

                        $otpay = $otpayweekends + $otpayweekdays;

                        $getnitediff = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 358))->get()->row();
                        $nitediff = ($getnitediff) ? (number_format($getnitediff->amt, 2, '.', '')) : 0;

                        $getadjustments = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 375))->get()->row();
                        $adjustments = ($getadjustments) ? (number_format($getadjustments->amt, 2, '.', '')) : 0;

                        //trans allw
                        $gettransallw = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 252))->get()->row();
                        $otheradd += ($gettransallw) ? (number_format($gettransallw->amt, 2, '.', '')) : 0;


                        //rice subsi
                        $getrice = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 253))->get()->row();
                        $otheradd += ($getrice) ? (number_format($getrice->amt, 2, '.', '')) : 0;

                        //acting allw
                        $getactingallw = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 360))->get()->row();
                        $otheradd += ($getactingallw) ? (number_format($getactingallw->amt, 2, '.', '')) : 0;


                        $getssscont = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 72))->get()->row();
                        $ssscont = ($getssscont) ? (number_format($getssscont->amt, 2, '.', '')) : 0;

                        $getsssloan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 257))->get()->row();
                        $sssloan = ($getsssloan) ? (number_format($getsssloan->amt, 2, '.', '')) : 0;

                        $gethdmfcont = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 74))->get()->row();
                        $hdmfcont = ($gethdmfcont) ? (number_format($gethdmfcont->amt, 2, '.', '')) : 0;

                        $gethdmfloan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 258))->get()->row();
                        $hdmfloan = ($gethdmfloan) ? (number_format($gethdmfloan->amt, 2, '.', '')) : 0;

                        $getagencyfee = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 1009))->get()->row();
                        $agencyfee = ($getagencyfee) ? (number_format($getagencyfee->amt, 2, '.', '')) : 0;

                        $getpecewaloan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 254))->get()->row();
                        $pecewa = ($getpecewaloan) ? (number_format($getpecewaloan->amt, 2, '.', '')) : 0;

                        $getcooploan = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 255))->get()->row();
                        $coop = ($getcooploan) ? (number_format($getcooploan->amt, 2, '.', '')) : 0;

                        $gethmodedn = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 260))->get()->row();
                        $hmodedn = ($gethmodedn) ? (number_format($gethmodedn->amt, 2, '.', '')) : 0;

                        $getelectbill = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 256))->get()->row();
                        $electbill = ($getelectbill) ? (number_format($getelectbill->amt, 2, '.', '')) : 0;

                        $getotherdedn = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 261))->get()->row();
                        $otherdedn = ($getotherdedn) ? (number_format($getotherdedn->amt, 2, '.', '')) : 0;

                        $getlwop = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 262))->get()->row();
                        $lwop = ($getlwop) ? (number_format($getlwop->amt, 2, '.', '')) : 0;
                        $withholdingtax = $row->tax;
                        $totalearnings = number_format($row->earnings, 2, '.', '');
                        // $totaldeductions = number_format($row->deductions, 2, '.', '');
                        $totaldeductions = number_format($row->deductions, 2, '.', '');
                        $totalnetpay = $row->net;
                        $tax = $row->tax;


                        $net_amt = $totalnetpay;
                        $rem = 'RNF';

                        if ($payclass != 128 || $payclass != 3077 || $payclass != 3078) {
                            $net_amt = ($totalnetpay / 2);
                            $rem = 'CONFIDENTIAL';
                        }

                        $excel_arr[] = array(
                            'exnum' => $excel_data_num,
                            'name' => $row->firstname . ' ' . $row->lastname . ' ' . $row->middlename,
                            'bankno' => $row->accntno,
                            'net' => $net_amt,
                            'rem' => $rem
                        );
                        $total_net += $net_amt;
                        $excel_data_num++;
                        $num++;


                    }

                    $this->excel->getActiveSheet()->setTitle('Sheet1');
                    //set cell A1 content with some text
                    $this->excel->getActiveSheet()->setCellValue('A1', 'H');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'Payroll Date');
                    $this->excel->getActiveSheet()->setCellValue('C1', date('F j, Y'));
                    $this->excel->getActiveSheet()->setCellValue('D1', 'Payroll Time');
                    $this->excel->getActiveSheet()->setCellValue('E1', '');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'Total Amount');
                    $this->excel->getActiveSheet()->setCellValue('G1', $total_net);
                    $this->excel->getActiveSheet()->setCellValue('H1', 'Total Count');
                    $this->excel->getActiveSheet()->setCellValue('I1', $num);
                    $this->excel->getActiveSheet()->setCellValue('J1', 'FUNDING ACCOUNT');
                    $this->excel->getActiveSheet()->setCellValue('K1', '1070007262');

                    $this->excel->getActiveSheet()->getStyle('G1')->getNumberFormat()->setFormatCode('#,##0.00');


                    $style = array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        )
                    );
                    $this->excel->getActiveSheet()->getStyle("A1:K1")->applyFromArray($style);

                    $this->excel->getActiveSheet()->setCellValue('A2', 'DETAILS CONSTANT');
                    $this->excel->getActiveSheet()->setCellValue('B2', 'EMPLOYEE NAME');
                    $this->excel->getActiveSheet()->setCellValue('D2', 'AMOUNT');
                    $this->excel->getActiveSheet()->setCellValue('E2', 'REMARKS');

                    foreach($excel_arr as $ex) {
                        $this->excel->getActiveSheet()->setCellValue('A' . $ex['exnum'], 'D');
                        $this->excel->getActiveSheet()->setCellValue('B' . $ex['exnum'], $ex['name']);
                        $this->excel->getActiveSheet()->setCellValue('C' . $ex['exnum'], $ex['bankno']);
                        $this->excel->getActiveSheet()->setCellValue('D' . $ex['exnum'], $ex['net']);
                        $this->excel->getActiveSheet()->setCellValue('E' . $ex['exnum'], $ex['rem']);

                        $this->excel->getActiveSheet()->getStyle('D'.$ex['exnum'])->getNumberFormat()->setFormatCode('#,##0.00');



                        $style = array(
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            )
                        );
                        $this->excel->getActiveSheet()->getStyle('A' . $ex['exnum'].':E'.$ex['exnum'])->applyFromArray($style);

                    }



                    $filename = 'PAYROLL_BANK_UPLOAD_ASOF_'.date('Y-m-d').'.xls'; //save our workbook as this file name
                    header('Content-Type: application/vnd.ms-excel'); //mime type
                    header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0'); //no cache
                    //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                    //if you want to save it as .XLSX Excel 2007 format
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                } else {
                    echo '<h1>THIS TRANSACTION EITHER DOES NOT EXIST OR IS NOT APPROVED!</h1>';
                }
            }
        }
    }

    function convertmonth($monthid)
    {
        $text = '';
        if ($monthid == 1) {
            $text = 'JANUARY';
        } else if ($monthid == 2) {
            $text = 'FEBRUARY';
        } else if ($monthid == 3) {
            $text = 'MARCH';
        } else if ($monthid == 4) {
            $text = 'APRIL';
        } else if ($monthid == 5) {
            $text = 'MAY';
        } else if ($monthid == 6) {
            $text = 'JUNE';
        } else if ($monthid == 7) {
            $text = 'JULY';
        } else if ($monthid == 8) {
            $text = 'AUGUST';
        } else if ($monthid == 9) {
            $text = 'SEPTEMBER';
        } else if ($monthid == 10) {
            $text = 'OCTOBER';
        } else if ($monthid == 11) {
            $text = 'NOVEMBER';
        } else if ($monthid == 12) {
            $text = 'DECEMBER';
        }
        return $text;
    }

    function downloadbankfile()
    {
        $filename = $this->uri->segment(4);
        $payclass = $this->uri->segment(3);

        $file = "uploads/payroll/" . $payclass . "/" . $filename;
        // $origpath = str_replace("\\", "/", $file);
        if (file_exists(FCPATH . 'uploads/payroll/' . $payclass . '/' . $filename)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            flush(); // Flush system output buffer
            readfile($file);
            exit;
        } else {
            echo 'File does not exist!';
        }


    }

    function getmonthname($monthid)
    {
        $montharr = array(
            '1' => 'JANUARY',
            '2' => 'FEBRUARY',
            '3' => 'MARCH',
            '4' => 'APRIL',
            '5' => 'MAY',
            '6' => 'JUNE',
            '7' => 'JULY',
            '8' => 'AUGUST',
            '9' => 'SEPTEMBER',
            '10' => 'OCTOBER',
            '11' => 'NOVEMBER',
            '12' => 'DECEMBER',
        );

        return $montharr[$monthid];
    }

    /*
    function getearningsreport(){
        $data = array();
        $count = 0;

        $dataid = $this->input->post('dataid');

        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if($payrollpayclass == 128){
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass , "status" => 1))
            ->get()->row();

        $groupid = ($sql) ?  $sql->sysid : $dataid;

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
                        $sumdeptholidaypay += ($fetchholidaypay) ? $fetchholidaypay->holidaypaytotal : 0;


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
        echo json_encode($data);
    }
    */

    function getempdeptearnings()
    {
        $data = array();
        //sysid of prime cost center main
        $id = $this->input->post('id');

        $html = '';
        $inputs = $this->input->post('inputs');
        $dataid = (is_array($inputs) && in_array('dataid',$inputs)) ? $inputs['dataid'] : false;

        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if (in_array($payrollpayclass,array(128,3077,3078))) {
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear, "months" => $payrollmonth, "payclass" => $payrollpayclass, "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ? $sql->sysid : $dataid;
        $group_meta = $this->db->select("paytype")->from("payroll_reports_group")->where(array("sysid" => $groupid))->get()->row();
        $group_paytype = ($group_meta) ? (int)$group_meta->paytype : (int)$payrollpaytype;

        //initialization
        $grossearnings = 0;
        $cola = 0;
        $trans_allw = 0;
        $ricesubsi = 0;
        $holidaypay = 0;
        $nitediff = 0;
        $otpay = 0;
        $actingallw = 0;
        $otheradd = 0;

        //totals
        $totalbasicrate = 0;
        $totalcola = 0;
        $totaltransallw = 0;
        $totalricesubsi = 0;
        $totalholidaypay = 0;
        $totalnitediff = 0;
        $totalotpay = 0;
        $totalactingw = 0;
        $totalotheradd = 0;
        $totalgrossearnings = 0;

        $testcount = 0;


        $sql = $this->db->select("prm.empid ,prm.basic,prg.payclass,prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_costcenter as pec", "pec.empid = prm.empid", "left")
            ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
            ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
            ->join("prime_employee_main_payclass as pemp", "pemp.emp_id  = prm.empid", "left")
            ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
            ->join("person as p", "p.sysid = pem.personid", "left")
            ->where(array("pec.ccid" => $id, "pem.status" => 1, "pec.type" => 1, "prm.groupid" => $groupid, "pec.status" => 1))
            ->order_by("p.lastname", "asc")
            ->group_by("prm.empid ,prm.basic,prg.payclass,prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
            ->get();


        if ($sql->num_rows() > 0) {

            $html .= '<table class="table table-bordered table-condensed table-hover tbl-xs">';
            $html .= '<thead>';
            $html .= '<tr>';

            $html .= '<th>NAME</th>';
            $html .= '<th>BASIC RATE</th>';
            $html .= '<th>COLA</th>';
            $html .= '<th>TRANS ALLW</th>';
            $html .= '<th>RICE SUBSI</th>';
            $html .= '<th>HOLIDAY PAY</th>';
            $html .= '<th>NITE DIFF</th>';
            $html .= '<th>OT PAY</th>';
            $html .= '<th>ACTING ALLW</th>';
            $html .= '<th>OTHER ADD</th>';
            $html .= '<th>GROSS EARNINGS</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($sql->result() as $row) {
                //initialization

                $testcount++;
                $getcola = $this->db->select("SUM(amt) AS totalcola")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 251))->get()->row();
                $cola = ($getcola) ? ($getcola->totalcola) : 0;

                $getricesubsi = $this->db->select("SUM(amt) AS totalricesubi")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 253))->get()->row();
                $ricesubsi = ($getricesubsi) ? ($getricesubsi->totalricesubi) : 0;

                $getholidaypay = $this->db->select("SUM(amt) AS totalholidaypay")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 263))->get()->row();
                $holidaypay = ($getholidaypay) ? ($getholidaypay->totalholidaypay) : 0;


                $basicrate = $row->basic;


                $gettrans_allw = $this->db->select("SUM(amt) AS totaltransallw")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 252))->get()->row();
                $trans_allw = ($gettrans_allw) ? ($gettrans_allw->totaltransallw) : 0;

                $getnitediff = $this->db->select("SUM(amt) AS totalnitediff")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 358))->get()->row();
                $nitediff = ($getnitediff) ? ($getnitediff->totalnitediff) : 0;

                $getotwithholiday = $this->db->select("SUM(amt) AS totalotpay")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3010))->get()->row();
                $otwithholiday = ($getotwithholiday) ? ($getotwithholiday->totalotpay) : 0;

                $getotpayweekends = $this->db->select("SUM(amt) AS totalotpay")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 1082))->get()->row();
                $otweekends = ($getotpayweekends) ? ($getotpayweekends->totalotpay) : 0;

                $getotpayweekdays = $this->db->select("SUM(amt) AS totalotpay")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 359))->get()->row();
                $otweekdays = ($getotpayweekdays) ? ($getotpayweekdays->totalotpay) : 0;
                $otpay = ($otweekends + $otweekdays + $otwithholiday);
                $getactingallw = $this->db->select("SUM(amt) AS totalactingallw")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 360))->get()->row();
                $actingallw = ($getactingallw) ? ($getactingallw->totalactingallw) : 0;

                $getotheradd = $this->db->select("SUM(amt) AS totalotheradd")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 266))->get()->row();
                $otheradd = ($getotheradd) ? ($getotheradd->totalotheradd) : 0;

                $get13th = $this->db->select("SUM(amt) AS totalbonus")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 264))->get()->row();
                $month13th = ($get13th) ? ($get13th->totalbonus) : 0;

                $get14th = $this->db->select("SUM(amt) AS totalbonus")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3072))->get()->row();
                $month14th = ($get14th) ? ($get14th->totalbonus) : 0;

                $otheradd = ($otheradd + $month13th + $month14th);


                if ($row->payclass == 128) {
                    $grossearnings = ($basicrate) + $cola + $trans_allw + $ricesubsi + $holidaypay + $nitediff + $otpay + $actingallw + $otheradd;
                } else {
                    $grossearnings = $basicrate + $cola + $trans_allw + $ricesubsi + $holidaypay + $nitediff + $otpay + $actingallw + $otheradd;
                }


                //totals

                $totalbasicrate += $basicrate;
                $totalcola += $cola;
                $totaltransallw += $trans_allw;
                $totalricesubsi += $ricesubsi;
                $totalholidaypay += $holidaypay;
                $totalnitediff += $nitediff;
                $totalotpay += $otpay;
                $totalactingw += $actingallw;
                $totalotheradd += $otheradd;
                $totalgrossearnings += $grossearnings;

                $html .= '<tr>';
                $html .= '<td>' . $row->lastname . ', ' . $row->firstname . '</td>';
                $html .= '<td class="number">' . number_format($basicrate, 2) . '</td>';
                $html .= '<td class="number">' . number_format($cola, 2) . '</td>';
                $html .= '<td class="number">' . number_format($trans_allw, 2) . '</td>';
                $html .= '<td class="number">' . number_format($ricesubsi, 2) . '</td>';
                $html .= '<td class="number">' . number_format($holidaypay, 2) . '</td>';
                $html .= '<td class="number">' . number_format($nitediff, 2) . '</td>';
                $html .= '<td class="number">' . number_format($otpay, 2) . '</td>';
                $html .= '<td class="number">' . number_format($actingallw, 2) . '</td>';
                $html .= '<td class="number">' . number_format($otheradd, 2) . '</td>';
                $html .= '<td class="number text-info">' . number_format($grossearnings, 2) . '</td>';
                $html .= '</tr>';

                $grossearnings = 0;
                $cola = 0;
                $holidaypay = 0;
                $trans_allw = 0;
                $ricesubsi = 0;
                $nitediff = 0;
                $otpay = 0;
                $actingallw = 0;
                $otheradd = 0;
            }

            $data['testcount'] = $testcount;
            $html .= '</tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td class="number text-info"></td>';
            $html .= '<td class="number text-info  bold">' . number_format($totalbasicrate, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalcola, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totaltransallw, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalricesubsi, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalholidaypay, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalnitediff, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalotpay, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalactingw, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalotheradd, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($totalgrossearnings, 2) . '</td>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';
        }


        $data['html'] = $html;
        echo json_encode($data);
    }

    function getempdeptdeductions()
    {
        $data = array();
        //sysid of prime cost center main
        $id = $this->input->post('id');

        $html = '';

        $inputs = $this->input->post('inputs');
        $dataid = (is_array($inputs) && in_array('dataid',$inputs)) ? $inputs['dataid'] : false;

        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if (in_array($payrollpayclass,array(128,3077,3078))) {
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear, "months" => $payrollmonth, "payclass" => $payrollpayclass, "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ? $sql->sysid : $dataid;
        //initialization
        $ssscont = 0;
        $sssloan = 0;
        $hdmfcont = 0;
        $hdmfloan = 0;
        $pecewaloan = 0;
        $cooploan = 0;
        $pagibigad = 0;
        $otherdedn = 0;
        $hmodedn = 0;
        $deda = 0;
        $electbill = 0;
        $memins = 0;
        $lwop = 0;
        $basetax = 0;
        $deduction = 0;

        //totals
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
        $totalbasetax = 0;
        $totaldeduction = 0;

        $paytype = 0;
        $payclass = 0;
        $getpaypaytype = $this->db->select("payclass,paytype")->from("payroll_reports_group")
            ->where(array("sysid" => $groupid))->get()->row();
        $paytype = ($getpaypaytype) ? $getpaypaytype->paytype : 0;
        $payclass = ($getpaypaytype) ? $getpaypaytype->payclass : 0;

        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;

        $sql = $this->db->select("pem.sysid,prm.empid ,prg.months , prg.years ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_costcenter as pec", "pec.empid = prm.empid", "left")
            ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
            ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
            ->join("prime_employee_main_payclass as pemp", "pemp.emp_id  = prm.empid", "left")
            ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
            ->join("person as p", "p.sysid = pem.personid", "left")
            ->where(array("pec.ccid" => $id, "pem.status" => 1, "pec.type" => 1, "prm.groupid" => $groupid, "pec.status" => 1))
            ->group_by("prm.empid,prg.months , prg.years ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
            ->order_by("p.lastname", "asc")
            ->get();

        if ($sql->num_rows() > 0) {

            $html .= '<table class="table table-bordered table-condensed table-hover tbl-xs">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>NAME</th>';
            $html .= '<th>SSS CONT</th>';
            $html .= '<th>SSS LOAN</th>';
            $html .= '<th>HDMF CONT</th>';
            $html .= '<th>HDMF LOAN</th>';
            $html .= '<th>PECEWA LOAN</th>';
            $html .= '<th>COOP LOAN</th>';
            $html .= '<th>PAGIBIG AD</th>';
            $html .= '<th>OTHER DEDN</th>';
            $html .= '<th>HMO DEDN</th>';
            $html .= '<th>DED A</th>';
            $html .= '<th>ELECT BILL</th>';
            $html .= '<th>MEM INS</th>';
            $html .= '<th>LWOP</th>';
            $html .= '<th>BASE TAX</th>';
            $html .= '<th>TOTAL DEDN</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($sql->result() as $row) {

                $getssscont = $this->db->select("SUM(amt) AS totalssscont")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 72))->get()->row();
                $ssscont = ($getssscont) ? number_format(($getssscont->totalssscont), 2, '.', '') : 0;

                $getsssloan = $this->db->select("SUM(amt) AS totalsssloan")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 257))->get()->row();
                $sssloan = ($getsssloan) ? number_format(($getsssloan->totalsssloan), 2, '.', '') : 0;

                $gethdmfcont = $this->db->select("SUM(amt) AS totalhdmfcont")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 74))->get()->row();
                $hdmfcont = ($gethdmfcont) ? number_format(($gethdmfcont->totalhdmfcont), 2, '.', '') : 0;

                $gethdmfloan = $this->db->select("SUM(amt) AS totalhdmfloan")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 258))->get()->row();
                $hdmfloan = ($gethdmfloan) ? number_format(($gethdmfloan->totalhdmfloan), 2, '.', '') : 0;

                $getpecewaloan = $this->db->select("SUM(amt) AS totalpecewaloan")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 254))->get()->row();
                $pecewaloan = ($getpecewaloan) ? number_format(($getpecewaloan->totalpecewaloan), 2, '.', '') : 0;

                $getagencyunion = $this->db->select("SUM(amt) AS totalagencyunion")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3006))->get()->row();
                $pecewaloan += ($getagencyunion) ? number_format(($getagencyunion->totalagencyunion), 2, '.', '') : 0;

                $getcigna = $this->db->select("SUM(amt) AS totalcigna")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3007))->get()->row();
                $pecewaloan += ($getcigna) ? number_format(($getcigna->totalcigna), 2, '.', '') : 0;

                $getcooploan = $this->db->select("SUM(amt) AS totalcooploan")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 255))->get()->row();
                $cooploan = ($getcooploan) ? number_format(($getcooploan->totalcooploan), 2, '.', '') : 0;

                $getpagibigad = $this->db->select("SUM(amt) AS totalpagibigadd")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 259))->get()->row();
                $pagibigad = ($getpagibigad) ? number_format(($getpagibigad->totalpagibigadd), 2, '.', '') : 0;

                $getotherdedn = $this->db->select("SUM(amt) AS totalotherdedn")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 261))->get()->row();
                $otherdedn = ($getotherdedn) ? number_format(($getotherdedn->totalotherdedn), 2, '.', '') : 0;

                $gethmodedn = $this->db->select("SUM(amt) AS totalhmodedn")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 260))->get()->row();
                $hmodedn = ($gethmodedn) ? number_format(($gethmodedn->totalhmodedn), 2, '.', '') : 0;

                $getdeda = $this->db->select("SUM(amt) AS totaldeda")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 0))->get()->row();
                $deda = ($getdeda) ? number_format(($getdeda->totaldeda), 2, '.', '') : 0;

                $getelectbill = $this->db->select("SUM(amt) AS totalelectbill")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 256))->get()->row();
                $electbill = ($getelectbill) ? number_format(($getelectbill->totalelectbill), 2, '.', '') : 0;

                $getmemins = $this->db->select("SUM(amt) AS totalmemins")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 0))->get()->row();
                $memins = ($getmemins) ? number_format(($getmemins->totalmemins), 2, '.', '') : 0;

                $getlwop = $this->db->select("SUM(amt) AS totallwop")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 262))->get()->row();
                $lwop = ($getlwop) ? number_format(($getlwop->totallwop), 2, '.', '') : 0;

                $basetax = number_format($row->tax, 2, '.', '');
                $deduction = number_format($row->deductions, 2, '.', '');


                $totalssscont += number_format($ssscont, 2, '.', '');
                $totalsssloan += number_format($sssloan, 2, '.', '');
                $totalhdmfcont += number_format($hdmfcont, 2, '.', '');
                $totalhdmfloan += number_format($hdmfloan, 2, '.', '');
                $totalpecewaloan += number_format($pecewaloan, 2, '.', '');
                $totalcooploan += number_format($cooploan, 2, '.', '');
                $totalpagibigad += number_format($pagibigad, 2, '.', '');
                $totalotherdedn += number_format($otherdedn, 2, '.', '');
                $totalhmodedn += number_format($hmodedn, 2, '.', '');
                $totaldeda += number_format($deda, 2, '.', '');
                $totalelectbill += number_format($electbill, 2, '.', '');
                $totalmemins += number_format($memins, 2, '.', '');
                $totallwop += number_format($lwop, 2, '.', '');
                $totalbasetax += number_format($basetax, 2, '.', '');
                $totaldeduction += number_format($deduction, 2, '.', '');

                $html .= '<tr>';
                $html .= '<td>' . $row->lastname . ', ' . $row->firstname . '</td>';
                $html .= '<td class="number">' . number_format($ssscont, 2) . '</td>';
                $html .= '<td class="number">' . number_format($sssloan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($hdmfcont, 2) . '</td>';
                $html .= '<td class="number">' . number_format($hdmfloan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($pecewaloan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($cooploan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($pagibigad, 2) . '</td>';
                $html .= '<td class="number">' . number_format($otherdedn, 2) . '</td>';
                $html .= '<td class="number">' . number_format($hmodedn, 2) . '</td>';
                $html .= '<td class="number">' . number_format($deda, 2) . '</td>';
                $html .= '<td class="number">' . number_format($electbill, 2) . '</td>';
                $html .= '<td class="number">' . number_format($memins, 2) . '</td>';
                $html .= '<td class="number">' . number_format($lwop, 2) . '</td>';
                $html .= '<td class="number">' . number_format($basetax, 2) . '</td>';
                $html .= '<td class="number text-danger">' . number_format($deduction, 2) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';

            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td class="number"></td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalssscont, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalsssloan, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalhdmfcont, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalhdmfloan, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalpecewaloan, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalcooploan, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalpagibigad, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalotherdedn, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalhmodedn, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totaldeda, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalelectbill, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalmemins, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totallwop, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totalbasetax, 2) . '</td>';
            $html .= '<td class="number text-danger bold">' . number_format($totaldeduction, 2) . '</td>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';
        }


        $data['html'] = $html;
        echo json_encode($data);
    }


    function getpayrollregisterdata()
    {
        $sql = $this->model_payroll->get_payroll_register_data();
        echo json_encode($sql);


        //// REDUNDANT !!! REDUNDANT!!! REDUNDANT!!!
        //// REDUNDANT !!! REDUNDANT!!! REDUNDANT!!!
        //// REDUNDANT !!! REDUNDANT!!! REDUNDANT!!!
        /// SE: 09-25-2019
        /// @TODO remove this redundant if revision is operational
        /*
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
        $paytype = ($getpaypaytype) ? $getpaypaytype->paytype : 0;
        $payclass = ($getpaypaytype) ? $getpaypaytype->payclass : 0;

        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;
        if(count($sql['payrollregisterdata']) > 0){
            foreach ($sql['payrollregisterdata'] as $row){
                $count++;

                $data['payrollregisterdata'][] = array(
                    "expand" => $row['expand'],
                    "deptcode" => $row['deptcode'],
                    "grossearnings"=> $row['grossearnings'],
                    "totaldedn"=> $row['totaldedn'],
                    "totalnet"=> $row['totalnet'],
                    "ssscont"=> $row['ssscont'],
                    "sssloan"=> $row['sssloan'],
                    "hdmfcont"=> $row['hdmfcont'],
                    "hdmfloan"=> $row['hdmfloan'],
                    "pecewaloan"=> $row['pecewaloan'],
                    "cooploan"=> $row['cooploan'],
                    "pagibigadd"=> $row['pagibigadd'],
                    "otherdeductions"=> $row['otherdeductions'],
                    "hmodedn"=> $row['hmodedn'],
                    "deda"=> $row['deda'],
                    "electricbill"=> $row['electricbill'],
                    "memins"=> $row['memins'],
                    "lwop"=> $row['lwop'],
                    "basetax"=> $row['basetax'],
                );

                $resultdeptgrossearnings += remove_number_format($row['grossearnings']);
                $resultdeptnet += remove_number_format($row['totaldedn']);
                $resultdepttotaldedn  += remove_number_format($row['totalnet']);
                $resultdeptssscont += remove_number_format($row['ssscont']);
                $resultdeptsssloan += remove_number_format($row['sssloan']);
                $resultdepthdmfcont += remove_number_format($row['hdmfcont']);
                $resultdepthdmfloan += remove_number_format($row['hdmfloan']);
                $resultdeptpecewaloan += remove_number_format($row['pecewaloan']);
                $resultdeptcooploan += remove_number_format($row['cooploan']); //
                $resultdeptpagibigadd += remove_number_format($row['pagibigadd']);
                $resultdeptotherdeduction += remove_number_format($row['otherdeductions']);
                $resultdepthmodedn += remove_number_format($row['hmodedn']);
                $resultdeptdeda += remove_number_format($row['deda']);
                $resultdeptelectricbill += remove_number_format($row['electricbill']);
                $resultdeptmemins += remove_number_format($row['memins']);
                $resultdeptlwop += remove_number_format($row['lwop']);
                $resultdeptbasetax += remove_number_format($row['basetax']);
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

        echo json_encode($data);
        */
    }


    function getempdeptpayrollregister()
    {
        $data = array();
        //sysid of prime cost center main
        $id = $this->input->post('id');

        $inputs = $this->input->post('inputs');
        $dataid = (is_array($inputs) && in_array('dataid',$inputs)) ? $inputs['dataid'] : false;


        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if (in_array($payrollpayclass,array(128,3077,3078))) {
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear, "months" => $payrollmonth, "payclass" => $payrollpayclass, "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ? $sql->sysid : $dataid;

        $html = '';


        //totals
        $overalltotalgrossearnings = 0;
        $overalltotaldedn = 0;
        $overalltotalnet = 0;
        $overalltotalssscont = 0;
        $overalltotalsssloan = 0;
        $overalltotalhdmfcont = 0;
        $overalltotalhdmfloan = 0;
        $overalltotalpecewaloan = 0;
        $overalltotalcooploan = 0;
        $overalltotalpagibigadd = 0;
        $overalltotalotherdeductions = 0;
        $overalltotalhmodedn = 0;
        $overalltotalelectricbill = 0;
        $overalltotalmemins = 0;
        $overalltotallwop = 0;
        $overalltotalbasetax = 0;

        $paytype = 0;
        $payclass = 0;
        $getpaypaytype = $this->db->select("payclass,paytype")->from("payroll_reports_group")
            ->where(array("sysid" => $groupid))->get()->row();
        $paytype = ($getpaypaytype) ? $getpaypaytype->paytype : 0;
        $payclass = ($getpaypaytype) ? $getpaypaytype->payclass : 0;

        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;


        $this->db->distinct();
        $sql = $this->db->select("pem.sysid ,prg.payclass,prg.months,prg.years, prm.empid ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , p.firstname , p.lastname,prt.payrollid")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_costcenter as pec", "pec.empid = prm.empid", "left")
            ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
            ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
            ->join("prime_employee_main_payclass as pemp", "pemp.emp_id  = prm.empid", "left")
            ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
            ->join("person as p", "p.sysid = pem.personid", "left")
            ->where(array("pec.ccid" => $id, "pec.type" => 1, "prm.groupid" => $groupid, "pec.status" => 1))
            ->group_by("pem.sysid ,prg.payclass, prm.empid ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , p.firstname , p.lastname,prt.payrollid ")
            ->order_by("p.lastname", "asc")
            ->get();

        if ($sql->num_rows() > 0) {

            $html .= '<table class="table table-bordered table-condensed table-hover tbl-xs">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>NAME</th>';
            $html .= '<th>GROSS EARNINGS</th>';
            $html .= '<th>TOTAL DEDN</th>';
            $html .= '<th>TOTAL NET</th>';
            $html .= '<th>SSS CONT</th>';
            $html .= '<th>SSS LOAN</th>';
            $html .= '<th>HDMF CONT</th>';
            $html .= '<th>HDMF LOAN</th>';
            $html .= '<th>PECEWA LOAN</th>';
            $html .= '<th>COOP LOAN</th>';
            $html .= '<th>PAGIBIG ADD</th>';
            $html .= '<th>OTHER DEDUCTIONS</th>';
            $html .= '<th>HMO DEDN</th>';
            $html .= '<th>ELECTRIC BILL</th>';
            $html .= '<th>MEM INS</th>';
            $html .= '<th>LWOP</th>';
            $html .= '<th>BASE TAX</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($sql->result() as $row) {

                $fetchtotals = $this->db->select("basic,earnings,deductions,net , tax")->from("payroll_reports_main")->where(array("empid" => $row->sysid, "groupid" => $groupid))->get()->row();

                // FOR GROSS EARNINGS
                $getcola = $this->db->select("SUM(amt) AS totalcola")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 251))->get()->row();
                $cola = ($getcola) ? ($getcola->totalcola) : 0;

                $getricesubsi = $this->db->select("SUM(amt) AS totalrice")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 253))->get()->row();
                $ricesubsi = ($getricesubsi) ? ($getricesubsi->totalrice) : 0;

                $getholidaypay = $this->db->select("SUM(amt) AS totalholiday")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 263))->get()->row();
                $holidaypay = ($getholidaypay) ? ($getholidaypay->totalholiday) : 0;

                $gettrans_allw = $this->db->select("SUM(amt) AS totaltransallw")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 0))->get()->row();
                $trans_allw = ($gettrans_allw) ? ($gettrans_allw->totaltransallw) : 0;

                $getnitediff = $this->db->select("SUM(amt) AS nitediff")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 358))->get()->row();
                $nitediff = ($getnitediff) ? ($getnitediff->nitediff) : 0;

                $getotwithholiday = $this->db->select("SUM(amt) AS otpay")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3010))->get()->row();
                $otwithholiday = ($getotwithholiday) ? ($getotwithholiday->otpay) : 0;

                $getotpayweekends = $this->db->select("SUM(amt) AS otpay")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 1082))->get()->row();
                $otpayweekends = ($getotpayweekends) ? ($getotpayweekends->otpay) : 0;

                $getotpayweekdays = $this->db->select("SUM(amt) AS otpay")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 359))->get()->row();
                $otpayweekdays = ($getotpayweekdays) ? ($getotpayweekdays->otpay) : 0;

                $otpay = $otpayweekdays + $otpayweekends + $otwithholiday;

                $getactingallw = $this->db->select("SUM(amt) AS actingallw")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 360))->get()->row();
                $actingallw = ($getactingallw) ? ($getactingallw->actingallw) : 0;

                $getotheradd = $this->db->select("SUM(amt) AS otheradd")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 266))->get()->row();
                $otheradd = ($getotheradd) ? ($getotheradd->otheradd) : 0;

                $get13th = $this->db->select("SUM(amt) AS bonus")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 264))->get()->row();
                $month13 = ($get13th) ? ($get13th->bonus) : 0;
                $otheradd = $otheradd + $month13;

                $get14th = $this->db->select("SUM(amt) AS bonus")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3072))->get()->row();
                $month14 = ($get14th) ? ($get14th->bonus) : 0;
                $otheradd = $otheradd + $month14;


                $getps = $this->db->select("SUM(amt) AS ps")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 265))->get()->row();
                $ps = ($getps) ? ($getps->ps) : 0;
                $otheradd = $otheradd + $ps;
                //initialization

                if ($row->payclass == 128 || $row->payclass == 3077 || $row->payclass == 3078) {
                    $totalgrossearnings = ($fetchtotals->basic) + $cola + $ricesubsi + $holidaypay + $trans_allw + $nitediff + $otpay + $actingallw + $otheradd;
                } else {
                    $totalgrossearnings = $fetchtotals->basic + $cola + $ricesubsi + $holidaypay + $trans_allw + $nitediff + $otpay + $actingallw + $otheradd;
                }


                $totaldedn = ($fetchtotals) ? $fetchtotals->deductions : 0;
                $totalnet = $totalgrossearnings - $totaldedn;

                $getssscont = $this->db->select("SUM(amt) AS sssconttotal")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 72))->get()->row();
                $totalssscont = ($getssscont) ? ($getssscont->sssconttotal) : 0;

                $getsssloan = $this->db->select("SUM(amt) AS sssloantotal")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 257))->get()->row();
                $totalsssloan = ($getsssloan) ? ($getsssloan->sssloantotal) : 0;

                $gethdmfcont = $this->db->select("SUM(amt) AS totalhdmfcont")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 74))->get()->row();
                $totalhdmfcont = ($gethdmfcont) ? ($gethdmfcont->totalhdmfcont) : 0;

                $gethdmfloan = $this->db->select("SUM(amt) AS hdmfloan")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 258))->get()->row();
                $totalhdmfloan = ($gethdmfloan) ? ($gethdmfloan->hdmfloan) : 0;

                $getpecewaloan = $this->db->select("SUM(amt) AS pecewaloan")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 254))->get()->row();
                $totalpecewaloan = ($getpecewaloan) ? ($getpecewaloan->pecewaloan) : 0;

                $getagencyunion = $this->db->select("SUM(amt) AS totalagencyunion")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3006))->get()->row();
                $totalpecewaloan += ($getagencyunion) ? ($getagencyunion->totalagencyunion) : 0;

                $getcigna = $this->db->select("SUM(amt) AS totalcigna")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 3007))->get()->row();
                $totalpecewaloan += ($getcigna) ? ($getcigna->totalcigna) : 0;

                $getcooploan = $this->db->select("SUM(amt) AS cooploan")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 255))->get()->row();
                $totalcooploan = ($getcooploan) ? ($getcooploan->cooploan) : 0;

                $getpagibigad = $this->db->select("SUM(amt) AS pagibigadd")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 259))->get()->row();
                $totalpagibigadd = ($getpagibigad) ? ($getpagibigad->pagibigadd) : 0;

                $getotherdeductions = $this->db->select("SUM(amt) AS otherdeductions")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 261))->get()->row();
                $totalotherdeductions = ($getotherdeductions) ? ($getotherdeductions->otherdeductions) : 0;

                $gethmodedn = $this->db->select("SUM(amt) AS hmodedn")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 260))->get()->row();
                $totalhmodedn = ($gethmodedn) ? ($gethmodedn->hmodedn) : 0;

                $getelectbill = $this->db->select("SUM(amt) AS electbill")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 256))->get()->row();
                $totalelectricbill = ($getelectbill) ? ($getelectbill->electbill) : 0;

                $getmemins = $this->db->select("SUM(amt) AS memins")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 0))->get()->row();
                $totalmemins = ($getmemins) ? ($getmemins->memins) : 0;

                $getlwop = $this->db->select("SUM(amt) AS lwop")->from("payroll_reports_trn")->where(array("payrollid" => $row->payrollid, "trntype" => 262))->get()->row();
                $totallwop = ($getlwop) ? ($getlwop->lwop) : 0;


                $totalbasetax = ($fetchtotals) ? $fetchtotals->tax : 0;

                //overalltotals
                $overalltotalgrossearnings += number_format($totalgrossearnings, 2, '.', '');
                $overalltotaldedn += number_format($totaldedn, 2, '.', '');
                $overalltotalnet += number_format($totalnet, 2, '.', '');
                $overalltotalssscont += number_format($totalssscont, 2, '.', '');
                $overalltotalsssloan += number_format($totalsssloan, 2, '.', '');
                $overalltotalhdmfcont += number_format($totalhdmfcont, 2, '.', '');
                $overalltotalhdmfloan += number_format($totalhdmfloan, 2, '.', '');
                $overalltotalpecewaloan += number_format($totalpecewaloan, 2, '.', '');
                $overalltotalcooploan += number_format($totalcooploan, 2, '.', '');
                $overalltotalpagibigadd += number_format($totalpagibigadd, 2, '.', '');
                $overalltotalotherdeductions += number_format($totalotherdeductions, 2, '.', '');
                $overalltotalhmodedn += number_format($totalhmodedn, 2, '.', '');
                $overalltotalelectricbill += number_format($totalelectricbill, 2, '.', '');
                $overalltotalmemins += number_format($totalmemins, 2, '.', '');
                $overalltotallwop += number_format($totallwop, 2, '.', '');
                $overalltotalbasetax += number_format($totalbasetax, 2, '.', '');


                $html .= '<tr>';
                $html .= '<td>' . $row->lastname . ', ' . $row->firstname . '</td>';
                $html .= '<td class="number">' . number_format($totalgrossearnings, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totaldedn, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalnet, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalssscont, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalsssloan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalhdmfcont, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalhdmfloan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalpecewaloan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalcooploan, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalpagibigadd, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalotherdeductions, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalhmodedn, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalelectricbill, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalmemins, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totallwop, 2) . '</td>';
                $html .= '<td class="number">' . number_format($totalbasetax, 2) . '</td>';
                $html .= '</tr>';

                $totalgrossearnings = 0;
                $totaldedn = 0;
                $totalnet = 0;
                $totalssscont = 0;
                $totalsssloan = 0;
                $totalhdmfcont = 0;
                $totalhdmfloan = 0;
                $totalpecewaloan = 0;
                $totalcooploan = 0;
                $totalpagibigadd = 0;
                $totalotherdeductions = 0;
                $totalhmodedn = 0;
                $totalelectricbill = 0;
                $totalmemins = 0;
                $totallwop = 0;
                $totalbasetax = 0;

                $cola = 0;
                $ricesubsi = 0;
                $holidaypay = 0;
                $trans_allw = 0;
                $nitediff = 0;
                $otpay = 0;
                $actingallw = 0;
                $otheradd = 0;
            }

            $html .= '</tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td class="number text-info">TOTAL RESULT</td>';
            $html .= '<td class="number text-info  bold">' . number_format($overalltotalgrossearnings, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotaldedn, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalnet, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalssscont, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalsssloan, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalhdmfcont, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalhdmfloan, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalpecewaloan, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalcooploan, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalpagibigadd, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalotherdeductions, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalhmodedn, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalelectricbill, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalmemins, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotallwop, 2) . '</td>';
            $html .= '<td class="number text-info bold">' . number_format($overalltotalbasetax, 2) . '</td>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';
        }


        $data['html'] = $html;
        echo json_encode($data);
    }

    function getprintpayregbyemp()
    {

        $data = array();

        $dataid = $this->input->post('dataid');
        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if (in_array($payrollpayclass,array(128,3077,3078))) {
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear, "months" => $payrollmonth, "payclass" => $payrollpayclass))
            ->where_in('status', array(1, 301))
            ->get()->row();

        $groupid = ($sql) ? $sql->sysid : $dataid;

        //for totals
        $totalearnings = 0;
        $totalbasic = 0;
        $totalgross = 0;
        $totaldedn = 0;
        $totalnet = 0;
        $totalssscont = 0;
        $totalsssloan = 0;
        $totalhdmfcont = 0;
        $totalhdmfloan = 0;
        $totalpecewaloan = 0;
        $totalcooploan = 0;
        $totalpagibigadd = 0;
        $totalotherdedn = 0;
        $totalhmodedn = 0;
        $totalelectbill = 0;
        $totalmemins = 0;
        $totallwop = 0;
        $totalbasetax = 0;
        $dedasubtotal = 0;

        //by row

        $overallgross = 0;
        $overalldedn = 0;
        $overallnet = 0;
        $overallssscont = 0;
        $overallsssloan = 0;
        $overallhdmfcont = 0;
        $overallhdmfloan = 0;
        $overallpecewaloan = 0;
        $overallcooploan = 0;
        $overallpagibigadd = 0;
        $overallotherdeductions = 0;
        $overallhmodedn = 0;
        $overalldeda = 0;
        $overallelectbill = 0;
        $overallmemins = 0;
        $overalllwop = 0;
        $overallbasetax = 0;


        $getpaypaytype = $this->db->select("payclass,paytype")->from("payroll_reports_group")
            ->where(array("sysid" => $groupid))->get()->row();
        $paytype = ($getpaypaytype) ? $getpaypaytype->paytype : 0;
        $payclass = ($getpaypaytype) ? $getpaypaytype->payclass : 0;

        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;

        $html = '';
        if ($payclass == 128) {
            $rep_type_code = 'RF';
            $rep_type = 'RANK AND FILE PAYROLL';
        } else if ($payclass == 3078) {
            $rep_type_code = 'T2';
            $rep_type = 'TIER 2 PAYROLL';
        } else if ($payclass == 3077) {
            $rep_type_code = 'T1';
            $rep_type = 'TIER 1 PAYROLL';
        } else {
            $rep_type_code = 'C';
            $rep_type = 'CONFIDENTIAL PAYROLL';
        }

        $rep_title = 'PAYROLL REGISTER';

        $html .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);
        $html .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">' . $rep_type . '</b></div>';
        $html .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: ' . date("m/d/Y") . '</div>';
        $html .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';


        $html .= '<div class="row">';
        $html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
        $html .= '<table class="table table-condensed tbl-xs print-table-standard">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>NAME</th>';
        $html .= '<th>GROSS EARNINGS</th>';
        $html .= '<th>TOTAL DEDN</th>';
        $html .= '<th>TOTAL NET</th>';
        $html .= '<th>SSS CONT</th>';
        $html .= '<th>SSS LOAN</th>';
        $html .= '<th>HDMF CONT</th>';
        $html .= '<th>HDMF LOAN</th>';
        $html .= '<th>PECEWA LOAN</th>';
        $html .= '<th>COOP LOAN</th>';
        $html .= '<th>PAGIBIG ADD</th>';
        $html .= '<th>OTHER DEDUCTIONS</th>';
        $html .= '<th>HMO DEDN</th>';
        $html .= '<th>DED A</th>';
        $html .= '<th>ELECTRIC BILL</th>';
        $html .= '<th>MEM INS</th>';
        $html .= '<th>LWOP</th>';
        $html .= '<th>BASE TAX</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $sql = $this->db->select("pcm.sysid")
            ->from("prime_costcenter_main as pcm")
            ->where(array("codes !=" => 0, "status" => 1))
            ->order_by("codes", "ASC")
            ->get();
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {

                $fetchpayregbyemp = $this->db->select("pem.sysid ,prg.months,prg.years, prg.payclass, prm.empid ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
                    ->from("payroll_reports_main as prm")
                    ->join("prime_employee_costcenter as pec", "pec.empid = prm.empid", "left")
                    ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                    ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                    ->join("prime_employee_main_payclass as pemp", "pemp.emp_id  = prm.empid", "left")
                    ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                    ->join("person as p", "p.sysid = pem.personid", "left")
                    ->where(array("pec.ccid" => $row->sysid, "prm.groupid" => $groupid, "pec.type" => 1, "pec.status" => 1))
                    ->group_by("pem.sysid ,prg.months,prg.years, prg.payclass, prm.empid ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
                    ->order_by("p.lastname", "ASC")
                    ->get();

                $data['fetchpayregbyemp_qry'] = $this->db->last_query();

                if ($fetchpayregbyemp->num_rows() > 0) {
                    foreach ($fetchpayregbyemp->result() as $payregrow) {

                        $gross = (float)$payregrow->earnings;
                        $dedn = (float)$payregrow->deductions;
                        $net = (float)$payregrow->net;

                        $getholidaypay = $this->db->select("SUM(amt) AS totalholidaypay")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 263, 'status' => 1))->get()->row();
                        $holiday_report_amt = ($getholidaypay && $getholidaypay->totalholidaypay !== null) ? (float)$getholidaypay->totalholidaypay : 0;
                        $holiday_amt = $holiday_report_amt;
                        if($holiday_amt == 0){
                            $pt_select = ($paytype != 0) ? "SUM(CASE WHEN paytype = 0 THEN amt/2 ELSE amt END) AS totalholidaypay" : "SUM(amt) AS totalholidaypay";
                            $pt_holiday = $this->db->select($pt_select, false)->from("payroll_transactions")
                                ->where(array("empid" => $payregrow->empid, "months" => (int)$payregrow->months, "years" => (int)$payregrow->years, "typesid" => 263))
                                ->where_not_in("status", array(0, 302))
                                ->where_in("paytype", array(0, $paytype))
                                ->get()->row();
                            $holiday_amt = ($pt_holiday && $pt_holiday->totalholidaypay !== null) ? (float)$pt_holiday->totalholidaypay : 0;
                            if($holiday_amt == 0){
                                $pmtb_select = ($paytype != 0) ? "SUM(CASE WHEN pmtb.paytype = 0 THEN pmtb.amount/2 ELSE pmtb.amount END) AS totalholidaypay" : "SUM(pmtb.amount) AS totalholidaypay";
                                $pmtb_holiday = $this->db->select($pmtb_select, false)->from("payroll_manual_transactions_breakdown as pmtb")
                                    ->join("payroll_manual_transactions as pmt", "pmt.sysid = pmtb.groupid", "left")
                                    ->where(array("pmtb.empid" => $payregrow->empid, "pmtb.month" => (int)$payregrow->months, "pmtb.year" => (int)$payregrow->years, "pmt.tsysid" => 263))
                                    ->where_in("pmtb.status", array(312, 313))
                                    ->where_in("pmtb.paytype", array(0, $paytype))
                                    ->get()->row();
                                $holiday_amt = ($pmtb_holiday && $pmtb_holiday->totalholidaypay !== null) ? (float)$pmtb_holiday->totalholidaypay : 0;
                            }
                        }
                        if($holiday_report_amt == 0 && $holiday_amt > 0){
                            $gross += $holiday_amt;
                            $net += $holiday_amt;
                        }

                        $getssscont = $this->db->select("SUM(amt) AS totalssscont")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 72, 'status' => 1))->get()->row();
                        $ssscont = ($getssscont) ? ($getssscont->totalssscont) : 0;

                        $getsssloan = $this->db->select("SUM(amt) AS sssloantotal")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 257, 'status' => 1))->get()->row();
                        $sssloan = ($getsssloan) ? ($getsssloan->sssloantotal) : 0;

                        $gethdmfcont = $this->db->select("SUM(amt) AS totalhdmfcont")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 74, 'status' => 1))->get()->row();
                        $hdmfcont = ($gethdmfcont) ? ($gethdmfcont->totalhdmfcont) : 0;

                        $gethdmfloan = $this->db->select("SUM(amt) AS hdmfloan")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 258, 'status' => 1))->get()->row();
                        $hdmfloan = ($gethdmfloan) ? ($gethdmfloan->hdmfloan) : 0;

                        $getagency = $this->db->select("SUM(amt) AS totalagency")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 3006, 'status' => 1))->get()->row();
                        $agency = ($getagency) ? ($getagency->totalagency) : 0;

                        $getcigna = $this->db->select("SUM(amt) AS totalcigna")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 3007, 'status' => 1))->get()->row();
                        $cigna = ($getcigna) ? ($getcigna->totalcigna) : 0;

                        $getpecewaloan = $this->db->select("SUM(amt) AS totalpecewaloan")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 254, 'status' => 1))->get()->row();
                        $pecewaloan = ($getpecewaloan) ? ($getpecewaloan->totalpecewaloan) : 0;

                        $pecewaloan = ($pecewaloan + $cigna + $agency);

                        $getcooploan = $this->db->select("SUM(amt) AS totalcooploan")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 255, 'status' => 1))->get()->row();
                        $cooploan = ($getcooploan) ? ($getcooploan->totalcooploan) : 0;

                        $getpagibigad = $this->db->select("SUM(amt) AS totalpagibigadd")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 259, 'status' => 1))->get()->row();
                        $pagibigadd = ($getpagibigad) ? ($getpagibigad->totalpagibigadd) : 0;

                        $getotherdedn = $this->db->select("SUM(amt) AS totalotherdedn")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 261, 'status' => 1))->get()->row();
                        $otherdeductions = ($getotherdedn) ? ($getotherdedn->totalotherdedn) : 0;

                        $gethmodedn = $this->db->select("SUM(amt) AS totalhmodedn")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 260, 'status' => 1))->get()->row();
                        $hmodedn = ($gethmodedn) ? ($gethmodedn->totalhmodedn) : 0;

                        $getelectbill = $this->db->select("SUM(amt) AS totalelectbill")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 256, 'status' => 1))->get()->row();
                        $electbill = ($getelectbill) ? ($getelectbill->totalelectbill) : 0;

                        $getmemins = $this->db->select("SUM(amt) AS totalmemins")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 3009, 'status' => 1))->get()->row();
                        $memins = ($getmemins) ? ($getmemins->totalmemins) : 0;

                        $getlwop = $this->db->select("SUM(amt) AS totallwop")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 262, 'status' => 1))->get()->row();
                        $lwop_report_amt = ($getlwop && $getlwop->totallwop !== null) ? (float)$getlwop->totallwop : 0;
                        $lwop_amt = $lwop_report_amt;
                        if($lwop_amt == 0){
                            $pt_select = ($paytype != 0) ? "SUM(CASE WHEN paytype = 0 THEN amt/2 ELSE amt END) AS totallwop" : "SUM(amt) AS totallwop";
                            $pt_lwop = $this->db->select($pt_select, false)->from("payroll_transactions")
                                ->where(array("empid" => $payregrow->empid, "months" => (int)$payregrow->months, "years" => (int)$payregrow->years, "typesid" => 262))
                                ->where_not_in("status", array(0, 302))
                                ->where_in("paytype", array(0, $paytype))
                                ->get()->row();
                            $lwop_amt = ($pt_lwop && $pt_lwop->totallwop !== null) ? (float)$pt_lwop->totallwop : 0;
                            if($lwop_amt == 0){
                                $pmtb_select = ($paytype != 0) ? "SUM(CASE WHEN pmtb.paytype = 0 THEN pmtb.amount/2 ELSE pmtb.amount END) AS totallwop" : "SUM(pmtb.amount) AS totallwop";
                                $pmtb_lwop = $this->db->select($pmtb_select, false)->from("payroll_manual_transactions_breakdown as pmtb")
                                    ->join("payroll_manual_transactions as pmt", "pmt.sysid = pmtb.groupid", "left")
                                    ->where(array("pmtb.empid" => $payregrow->empid, "pmtb.month" => (int)$payregrow->months, "pmtb.year" => (int)$payregrow->years, "pmt.tsysid" => 262))
                                    ->where_in("pmtb.status", array(312, 313))
                                    ->where_in("pmtb.paytype", array(0, $paytype))
                                    ->get()->row();
                                $lwop_amt = ($pmtb_lwop && $pmtb_lwop->totallwop !== null) ? (float)$pmtb_lwop->totallwop : 0;
                            }
                        }
                        $lwop = $lwop_amt;
                        if($lwop_report_amt == 0 && $lwop > 0){
                            $dedn += $lwop;
                            $net -= $lwop;
                        }

                        $getpecoloan = $this->db->select("SUM(amt) AS totalpecoloan")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 1079, 'status' => 1))->get()->row();
                        $pecoloan = ($getpecoloan) ? ($getpecoloan->totalpecoloan) : 0;

                        $getdeda = $this->db->select("SUM(amt) AS totaldeda")->from("payroll_reports_trn")->where(array("payrollid" => $payregrow->payrollid, "trntype" => 363, 'status' => 1))->get()->row();
                        $deda = ($getdeda) ? ($getdeda->totaldeda) : 0;
                        $totaldeda = $pecoloan + $deda;

                        $basetax = $payregrow->tax;


                        $html .= '<tr>';
                        $html .= '<td class="name-data"><span style="width: 200px !important;">' . ucwords(strtolower($payregrow->lastname)) . ', ' . ucwords(strtolower($payregrow->firstname)) . '</span></td>';
                        $html .= '<td class="number">' . number_format($gross, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($dedn, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($net, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($ssscont, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($sssloan, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($hdmfcont, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($hdmfloan, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($pecewaloan, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($cooploan, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($pagibigadd, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($otherdeductions, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($hmodedn, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($totaldeda, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($electbill, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($memins, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($lwop, 2) . '</td>';
                        $html .= '<td class="number">' . number_format($basetax, 2) . '</td>';
                        $html .= '</tr>';

                        $totalgross += $gross;
                        $totaldedn += $dedn;
                        $totalnet += $net;
                        $totalssscont += $ssscont;
                        $totalsssloan += $sssloan;
                        $totalhdmfcont += $hdmfcont;
                        $totalhdmfloan += $hdmfloan;
                        $totalpecewaloan += $pecewaloan;
                        $totalcooploan += $cooploan;
                        $totalpagibigadd += $pagibigadd;
                        $totalotherdedn += $otherdeductions;
                        $totalhmodedn += $hmodedn;
                        $totalelectbill += $electbill;
                        $totalmemins += $memins;
                        $totallwop += $lwop;
                        $totalbasetax += $basetax;
                        $dedasubtotal += $totaldeda;


                    }

                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td class="bold number">' . number_format($totalgross, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totaldedn, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalnet, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalssscont, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalsssloan, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalhdmfcont, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalhdmfloan, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalpecewaloan, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalcooploan, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalpagibigadd, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalotherdedn, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalhmodedn, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($dedasubtotal, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalelectbill, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalmemins, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totallwop, 2) . '</td>';
                    $html .= '<td class="bold number">' . number_format($totalbasetax, 2) . '</td>';
                    $html .= '<tr>';

                    $overallgross += $totalgross;
                    $overallnet += $totalnet;
                    $overallssscont += $totalssscont;
                    $overallsssloan += $totalsssloan;
                    $overallhdmfcont += $totalhdmfcont;
                    $overallhdmfloan += $totalhdmfloan;
                    $overallpecewaloan += $totalpecewaloan;
                    $overallcooploan += $totalcooploan;
                    $overallpagibigadd += $totalpagibigadd;
                    $overallotherdeductions += $totalotherdedn;
                    $overallhmodedn += $totalhmodedn;
                    $overalldeda += $dedasubtotal;
                    $overallelectbill += $totalelectbill;
                    $overallmemins += $totalmemins;
                    $overalllwop += $totallwop;
                    $overallbasetax += $totalbasetax;
                    $overalldedn += $totaldedn;

                    $totalgross = 0;
                    $totalbasic = 0;
                    $totalearnings = 0;
                    $totaldedn = 0;
                    $totalnet = 0;
                    $totalssscont = 0;
                    $totalsssloan = 0;
                    $totalhdmfcont = 0;
                    $totalhdmfloan = 0;
                    $totalpecewaloan = 0;
                    $totalcooploan = 0;
                    $totalpagibigadd = 0;
                    $totalotherdedn = 0;
                    $totalhmodedn = 0;
                    $totalelectbill = 0;
                    $totalmemins = 0;
                    $totallwop = 0;
                    $totalbasetax = 0;
                    $dedasubtotal = 0;
                }
            }


            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td class="bold number">' . number_format($overallgross, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overalldedn, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallnet, 2, '.', '') . '</td>';
            $html .= '<td class="bold number">' . number_format($overallssscont, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallsssloan, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallhdmfcont, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallhdmfloan, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallpecewaloan, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallcooploan, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallpagibigadd, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallotherdeductions, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallhmodedn, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overalldeda, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallelectbill, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallmemins, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overalllwop, 2) . '</td>';
            $html .= '<td class="bold number">' . number_format($overallbasetax, 2) . '</td>';
            $html .= '<tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="row">';
            $html .= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html .= '<div>Encoded by:</div>';
            $html .= '<div>____________</div>';
            $html .= '<div>HRDH</div>';
            $html .= '</div>';
            $html .= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html .= '<div>Checked by:</div>';
            $html .= '<div>____________</div>';
            $html .= '<div>GA</div>';
            $html .= '</div>';
            $html .= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html .= '<div>Noted by:</div>';
            $html .= '<div>____________</div>';
            $html .= '<div>FM</div>';
            $html .= '</div>';
            $html .= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';

            $html .= '</div>';
            $html .= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';
            $html .= '<div>Approved by:</div>';
            $html .= '<div>____________</div>';
            $html .= '<div>P-CEO</div>';
            $html .= '</div>';
            $html .= '</div>';
        }


        $data['html'] = $html;
        echo json_encode($data);

    }

    function payregbyempytd($years = false,$type = false)
    {
        if ($years == false) {
            $years = date('Y');
        }

        $earns_head = '';
        $earns_head .= '<th>COLA</th>';
        $earns_head .= '<th>TRANS ALLW</th>';
        $earns_head .= '<th>RICE SUBSI</th>';
        $earns_head .= '<th>HOLIDAY PAY</th>';
        $earns_head .= '<th>NITE DIFF</th>';
        $earns_head .= '<th>OT PAY</th>';
        $earns_head .= '<th>ACTING ALLW</th>';
        $earns_head .= '<th>OTHER ADD</th>';
        $earns_head .= '<th>PS GROSS</th>';
        $earns_head .= '<th>SL GROSS</th>';

        $deds_head = '';
        $deds_head .= '<th>SSS CONT</th>';
        $deds_head .= '<th>SSS LOAN</th>';
        $deds_head .= '<th>HDMF CONT</th>';
        $deds_head .= '<th>HDMF LOAN</th>';
        $deds_head .= '<th>PECEWA LOAN</th>';
        $deds_head .= '<th>COOP LOAN</th>';
        $deds_head .= '<th>PAGIBIG ADD</th>';
        $deds_head .= '<th>OTHER DEDUCTIONS</th>';
        $deds_head .= '<th>HMO DEDN</th>';
        $deds_head .= '<th>DED A</th>';
        $deds_head .= '<th>ELECTRIC BILL</th>';
        $deds_head .= '<th>MEM INS</th>';
        $deds_head .= '<th>LWOP</th>';
        $deds_head .= '<th>BASE TAX</th>';
        $deds_head .= '<th>PS DEDS</th>';
        $deds_head .= '<th>SL CON DEDS</th>';
        $deds_head .= '<th>PS TAX</th>';
        $deds_head .= '<th>SL CON TAX</th>';


        $data = array();
        $reg_trntypes = array(72, 257, 74, 258, 3006, 3007, 254, 255, 259, 261, 260, 256, 3009, 262, 1079);

        //for totals
        $totalearnings = 0;
        $totalbasic = 0;
        $totalgross = 0;
        $totaldedn = 0;
        $totalnet = 0;
        $totalssscont = 0;
        $totalsssloan = 0;
        $totalhdmfcont = 0;
        $totalhdmfloan = 0;
        $totalpecewaloan = 0;
        $totalcooploan = 0;
        $totalpagibigadd = 0;
        $totalotherdedn = 0;
        $totalhmodedn = 0;
        $totalelectbill = 0;
        $totalmemins = 0;
        $totallwop = 0;
        $totalbasetax = 0;
        $dedasubtotal = 0;

        //by row

        $ssscont = 0;
        $sssloan = 0;
        $hdmfcont = 0;
        $hdmfloan = 0;
        $pecewa_ = 0;
        $cooploan = 0;
        $pagibigadd = 0;
        $otherdeductions = 0;
        $hmodedn = 0;
        $pecoloan = 0;
        $electbill = 0;
        $memins = 0;
        $lwop = 0;
        $agency = 0;
        $cigna = 0;
        $pecewaloan = 0;

        $overallgross = 0;
        $overalldedn = 0;
        $overallnet = 0;
        $overallssscont = 0;
        $overallsssloan = 0;
        $overallhdmfcont = 0;
        $overallhdmfloan = 0;
        $overallpecewaloan = 0;
        $overallcooploan = 0;
        $overallpagibigadd = 0;
        $overallotherdeductions = 0;
        $overallhmodedn = 0;
        $overalldeda = 0;
        $overallelectbill = 0;
        $overallmemins = 0;
        $overalllwop = 0;
        $overallbasetax = 0;
        $overallpsdeds = 0;
        $overallpstax = 0;
        $overallsldeds = 0;
        $overallsltax = 0;

        $totalcola = 0;
        $totaltransallw = 0;
        $totalricesubsi = 0;
        $totalholidaypay = 0;
        $totalnitediff = 0;
        $totalotpay = 0;
        $totalactingw = 0;
        $totalotheradd = 0;
        $totalslgross = 0;
        $totalpsgross = 0;

        $html = '';
        $rep_title = 'Payroll Register YTD';
        if ($type != false){
            if ($type == 'earnings') {
                $rep_title = 'Earnings Register YTD';
            }else{
                if ($type == 'deductions') {
                    $rep_title = 'Deductions Register YTD';
                }
            }
        }

        $html .= '<html>';
        $html .= '<head>' .
            '<title>' . $rep_title . '</title>' .
            '<link href="' . base_url() . 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' .
            '<link href="' . base_url() . 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' .
            '<link href="' . base_url() . 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' .
            '<link href="' . base_url() . 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' .
            '<link href="' . base_url() . 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' .
            '<link href="' . base_url() . 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' .
            '<style>body{margin: 0px 0px !important;  font-family: arial; background: #fff; overflow: auto !important;}</style>' .
            '</head>';

        $html .= peco_print_header(0, $rep_title, 'YTD', false);
        $html .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;"></b></div>';
        $html .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: ' . date("m/d/Y") . '</div><br>';
        $html .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';


        $html .= '<div class="row">';
        $html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
        $html .= '<table class="table table-condensed tbl-xs print-table-standard">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>NAME</th>';
        if ($type != false){
            if ($type == 'earnings') {
                $html .= '<th>BASIC PAY</th>';
                $html .= $earns_head;
                $html .= '<th>GROSS EARNINGS</th>';
            }else{
                if ($type == 'deductions') {
                    $html .= $deds_head;
                    $html .= '<th>TOTAL DEDN</th>';
                }
            }
        }else{
            $html .= '<th>BASIC PAY</th>';
            $html .= $earns_head;
            $html .= '<th>GROSS EARNINGS</th>';
            $html .= $deds_head;
            $html .= '<th>TOTAL DEDN</th>';
            $html .= '<th>TOTAL NET</th>';
        }
        $html .= '<th>EMP. STATUS</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $fetchpayregbyemp = $this->db->select("pem.sysid ,p.lastname,p.firstname,prg.years ,sum(prm.basic) basic, sum(prm.deductions) deductions, sum(prm.earnings) earnings, sum(prm.tax) tax, sum(prm.net) net , pem.status")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
            ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
            ->join("person as p", "p.sysid = pem.personid", "left")
            ->where(array("prg.years" => $years, "prg.status" => 301,))
            ->group_by("`pem`.`sysid`, `prg`.`years`, p.lastname,p.firstname")
            ->order_by("p.lastname", "ASC")
            ->get();

        //print_r($this->db->last_query());

        if ($fetchpayregbyemp->num_rows() > 0) {
            foreach ($fetchpayregbyemp->result() as $payregrow) {

                $basic = $payregrow->basic;
                $gross = $payregrow->earnings;
                $dedn = $payregrow->deductions;
                $net = $payregrow->net;
                $stat = ($payregrow->status == 1) ? 'ACT' : 'INACT';

                $getssscont =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 72,'prt.status' => 1))
                    ->get()->row();
                $ssscont = ($getssscont) ? ($getssscont->total) : 0;

                $getsssloan =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 257,'prt.status' => 1))
                    ->get()->row();
                $sssloan = ($getsssloan) ? ($getsssloan->total) : 0;

                $gethdmfcont =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 74,'prt.status' => 1))
                    ->get()->row();
                $hdmfcont = ($gethdmfcont) ? ($gethdmfcont->total) : 0;

                $gethdmfloan =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 258,'prt.status' => 1))
                    ->get()->row();
                $hdmfloan = ($gethdmfloan) ? ($gethdmfloan->total) : 0;

                $getagency =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 3006,'prt.status' => 1))
                    ->get()->row();
                $agency = ($getagency) ? ($getagency->total) : 0;

                $getcigna =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 3007,'prt.status' => 1))
                    ->get()->row();
                $cigna = ($getcigna) ? ($getcigna->total) : 0;

                $getpecewaloan =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 254,'prt.status' => 1))
                    ->get()->row();
                $pecewaloan = ($getpecewaloan) ? ($getpecewaloan->total) : 0;

                $getcooploan =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 255,'prt.status' => 1))
                    ->get()->row();
                $cooploan = ($getcooploan) ? ($getcooploan->total) : 0;

                $getpagibigadd =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 259,'prt.status' => 1))
                    ->get()->row();
                $pagibigadd = ($getpagibigadd) ? ($getpagibigadd->total) : 0;

                $getotherdeductions =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 261,'prt.status' => 1))
                    ->get()->row();
                $otherdeductions = ($getotherdeductions) ? ($getotherdeductions->total) : 0;

                $gethmodedn =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 260,'prt.status' => 1))
                    ->get()->row();
                $hmodedn = ($gethmodedn) ? ($gethmodedn->total) : 0;

                $getelectbill =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 256,'prt.status' => 1))
                    ->get()->row();
                $electbill = ($getelectbill) ? ($getelectbill->total) : 0;

                $getmemins = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 3009,'prt.status' => 1))
                    ->get()->row();
                $memins = ($getmemins) ? ($getmemins->total) : 0;

                $getlwop =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 262,'prt.status' => 1))
                    ->get()->row();
                $lwop = ($getlwop) ? ($getlwop->total) : 0;

                $getpecoloan =$this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 1079,'prt.status' => 1))
                    ->get()->row();
                $pecoloan = ($getpecoloan) ? ($getpecoloan->total) : 0;

                $pecewa_ = $agency + $cigna + $pecewaloan;

                $getcola = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 251,'prt.status' => 1))
                    ->get()->row();
                $cola = ($getcola) ? ($getcola->total) : 0;

                $getricesubsi = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 253,'prt.status' => 1))
                    ->get()->row();
                $ricesubsi = ($getricesubsi) ? ($getricesubsi->total) : 0;

                $getholidaypay = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 263,'prt.status' => 1))
                    ->get()->row();
                $holidaypay = ($getholidaypay) ? ($getholidaypay->total) : 0;

                $gettrans_allw = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 252,'prt.status' => 1))
                    ->get()->row();
                $trans_allw = ($gettrans_allw) ? ($gettrans_allw->total) : 0;

                $getnitediff = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 358,'prt.status' => 1))
                    ->get()->row();
                $nitediff = ($getnitediff) ? ($getnitediff->total) : 0;

                $getotpaywithholiday = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 3010,'prt.status' => 1))
                    ->get()->row();
                $otwithholiday = ($getotpaywithholiday) ? ($getotpaywithholiday->total) : 0;

                $getotpayweekends = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 1082,'prt.status' => 1))
                    ->get()->row();
                $otweekends = ($getotpayweekends) ? ($getotpayweekends->total) : 0;

                $getotpayweekdays = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 359,'prt.status' => 1))
                    ->get()->row();
                $otpayweekdays = ($getotpayweekdays) ? ($getotpayweekdays->total) : 0;

                $otpay =  $otpayweekdays + $otweekends + $otwithholiday;

                $getactingallw = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 360,'prt.status' => 1))
                    ->get()->row();
                $actingallw = ($getactingallw) ? ($getactingallw->total) : 0;

                $getotheradd = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 266,'prt.status' => 1))
                    ->get()->row();
                $otheradd = ($getotheradd) ? ($getotheradd->total) : 0;

                $get13thmonth = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 264,'prt.status' => 1))
                    ->get()->row();
                $month13 = ($get13thmonth) ? ($get13thmonth->total) : 0;

                $get14thmonth = $this->db->select('SUM(amt) AS total  , prm.empid')->from('payroll_reports_trn as prt')
                    ->join('payroll_reports_main as prm', 'prt.payrollid = prm.sysid', 'left')
                    ->join('payroll_reports_group as prg', 'prm.groupid = prg.sysid', 'left')
                    ->where(array('prm.empid' => $payregrow->sysid, 'prg.years' => $years, 'prg.status' => 301, 'prt.trntype' => 3072,'prt.status' => 1))
                    ->get()->row();
                $month14 = ($get14thmonth) ? ($get14thmonth->total) : 0;

                $otheradd = ($otheradd + $month13 + $month14);

                $getprofitshare = $this->db->select('SUM(gross) AS gross, SUM(deduction) AS deds, SUM(tax) AS tax,empid')
                    ->from('payroll_manual_earnings')
                    ->where(array('year' => $years , 'typesid' => 265 , 'status' => 305 , 'empid' => $payregrow->sysid))
                    ->get()->row();

                $psgross = ($getprofitshare) ? $getprofitshare->gross : 0;
                $psdeds = ($getprofitshare) ? $getprofitshare->deds : 0;
                $pstax = ($getprofitshare) ? $getprofitshare->tax : 0;

                $getslconv = $this->db->select('SUM(gross) AS gross, SUM(deduction) AS deds, SUM(tax) AS tax,empid')
                    ->from('payroll_manual_earnings')
                    ->where(array('year' => $years , 'typesid' => 319 , 'status' => 305 , 'empid' => $payregrow->sysid))
                    ->get()->row();

                $slgross = ($getslconv) ? $getslconv->gross : 0;
                $sldeds = ($getslconv) ? $getslconv->deds : 0;
                $sltax = ($getslconv) ? $getslconv->tax : 0;

                $deduct = $dedn + $psdeds + $pstax + $sldeds + $sltax;
                $gross_ = $gross + $psgross + $slgross;
                $net_ = $net + $gross_ - $deduct;

                $overallgross += $gross_;
                $overalldedn += $deduct;
                $overallnet += $net_;
                $totalbasic += $basic;


                //deductions
                $overallssscont += $ssscont;
                $overallsssloan += $sssloan;
                $overallhdmfcont += $hdmfcont;
                $overallhdmfloan += $hdmfloan;
                $overallpecewaloan += $pecewa_;
                $overallcooploan += $cooploan;
                $overallpagibigadd += $pagibigadd;
                $overallotherdeductions += $otherdeductions;
                $overallhmodedn += $hmodedn;
                $overalldeda += $pecoloan;
                $overallelectbill += $electbill;
                $overallmemins += $memins;
                $overalllwop += $lwop;
                $overallpsdeds += $psdeds;
                $overallpstax += $pstax;
                $overallsldeds += $sldeds;
                $overallsltax += $sltax;

                //earnings
                $totalcola += $cola;
                $totaltransallw += $trans_allw;
                $totalricesubsi += $ricesubsi;
                $totalholidaypay += $holidaypay;
                $totalnitediff += $nitediff;
                $totalotpay += $otpay;
                $totalactingw += $actingallw;
                $totalotheradd += $otheradd;
                $totalpsgross += $psgross;
                $totalslgross += $slgross;

                $basetax = $payregrow->tax;
                $overallbasetax += $basetax;

                $html .= '<tr>';
                $html .= '<td class="name-data"><span style="width: 200px !important;">' . ucwords(strtolower($payregrow->lastname)) . ', ' . ucwords(strtolower($payregrow->firstname)) . '</span></td>';

                $deds = '';
                $deds .= '<td class="number">' . number_format($ssscont, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($sssloan, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($hdmfcont, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($hdmfloan, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($pecewa_, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($cooploan, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($pagibigadd, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($otherdeductions, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($hmodedn, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($pecoloan, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($electbill, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($memins, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($lwop, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($basetax, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($psdeds, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($sldeds, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($pstax, 2) . '</td>';
                $deds .= '<td class="number">' . number_format($sltax, 2) . '</td>';

                $earns = '';
                $earns .= '<td class="number">'.number_format($cola, 2).'</td>';
                $earns .= '<td class="number">'.number_format($trans_allw, 2).'</td>';
                $earns .= '<td class="number">'.number_format($ricesubsi, 2).'</td>';
                $earns .= '<td class="number">'.number_format($holidaypay , 2).'</td>';
                $earns .= '<td class="number">'.number_format($nitediff, 2).'</td>';
                $earns .= '<td class="number">'.number_format($otpay , 2).'</td>';
                $earns .= '<td class="number">'.number_format($actingallw , 2).'</td>';
                $earns .= '<td class="number">'.number_format($otheradd, 2).'</td>';
                $earns .= '<td class="number">'.number_format($psgross , 2).'</td>';
                $earns .= '<td class="number">'.number_format($slgross, 2).'</td>';

                if ($type != false){
                    if ($type == 'earnings') {
                        $html .= '<td class="number">' . number_format($basic, 2) . '</td>';
                        $html .= $earns;
                        $html .= '<td class="number">' . number_format($gross_, 2) . '</td>';
                        $html .= '<td>' . $stat . '</td>';
                    }else{
                        if ($type == 'deductions') {
                            $html .= $deds;
                            $html .= '<td class="number">' . number_format($deduct, 2) . '</td>';
                            $html .= '<td>' . $stat . '</td>';
                        }
                    }
                }else{
                    $html .= '<td class="number">' . number_format($basic, 2) . '</td>';
                    $html .= $earns;
                    $html .= '<td class="number">' . number_format($gross_, 2) . '</td>';
                    $html .= $deds;
                    $html .= '<td class="number">' . number_format($deduct, 2) . '</td>';
                    $html .= '<td class="number">' . number_format($net_, 2) . '</td>';
                    $html .= '<td>' . $stat . '</td>';
                }

                $html .= '</tr>';

            }


            $html .= '<tr>';
            $html .= '<td class="bold">Total</td>';

            $deds_foot = '';
            $deds_foot .= '<td class="bold number">' . number_format($overallssscont, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallsssloan, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallhdmfcont, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallhdmfloan, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallpecewaloan, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallcooploan, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallpagibigadd, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallotherdeductions, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallhmodedn, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overalldeda, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallelectbill, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallmemins, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overalllwop, 2) . '</td>';
            $deds_foot .= '<td class="bold number">' . number_format($overallbasetax, 2) . '</td>';

            $earns_foot = '';
            $earns_foot .= '<td class="number bold">'.number_format($totalcola, 2).'</td>';
            $earns_foot .= '<td class="number bold">'.number_format($totaltransallw, 2).'</td>';
            $earns_foot .= '<td class="number bold">'.number_format($totalricesubsi, 2).'</td>';
            $earns_foot .= '<td class="number bold">'.number_format($totalholidaypay , 2).'</td>';
            $earns_foot .= '<td class="number bold">'.number_format($totalnitediff, 2).'</td>';
            $earns_foot .= '<td class="number bold">'.number_format($totalotpay , 2).'</td>';
            $earns_foot .= '<td class="number bold">'.number_format($totalactingw , 2).'</td>';
            $earns_foot .= '<td class="number bold">'.number_format($totalotheradd, 2).'</td>';

            if ($type != false){
                if ($type == 'earnings') {
                    $html .= '<td class="bold number">' . number_format($totalbasic, 2) . '</td>';
                    $html .= $earns_foot;
                    $html .= '<td class="bold number">' . number_format($overallgross, 2) . '</td>';
                    $html .= '<td></td>';
                }else{
                    if ($type == 'deductions') {
                        $html .= $deds_foot;
                        $html .= '<td class="bold number">' . number_format($overalldedn, 2) . '</td>';
                        $html .= '<td></td>';
                    }
                }
            }else{
                $html .= '<td class="bold number">' . number_format($totalbasic, 2) . '</td>';
                $html .= $earns_foot;
                $html .= '<td class="bold number">' . number_format($overallgross, 2) . '</td>';
                $html .= $deds_foot;
                $html .= '<td class="bold number">' . number_format($overalldedn, 2) . '</td>';
                $html .= '<td class="bold number">' . number_format($overallnet, 2, '.', '') . '</td>';
                $html .= '<td></td>';
            }
            $html .= '<tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</html>';

        }

        echo $html;
    }


    function getprintearregbyemp()
    {
        $data = array();
        $html = '';


        $dataid = $this->input->post('dataid');
        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if(in_array($payrollpayclass,array(128,3077,3078))){
            $this->db->where(array("paytype" => $payrollpaytype));
        }

        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass))
            ->where_in('status', array(301, 1))
            ->get()->row();

        $groupid = ($sql) ? $sql->sysid : $dataid;
        $group_meta = $this->db->select("paytype")->from("payroll_reports_group")->where(array("sysid" => $groupid))->get()->row();
        $group_paytype = ($group_meta) ? (int)$group_meta->paytype : (int)$payrollpaytype;

        $basicrate = 0;
        $grossearnings = 0;
        $cola = 0;
        $trans_allw = 0;
        $ricesubsi = 0;
        $holidaypay = 0;
        $nitediff = 0;
        $otpay = 0;
        $actingallw = 0;
        $otheradd = 0;

        //totals
        $totalbasicrate = 0;
        $totalcola = 0;
        $totaltransallw = 0;
        $totalricesubsi = 0;
        $totalholidaypay = 0;
        $totalnitediff = 0;
        $totalotpay = 0;
        $totalactingw = 0;
        $totalotheradd = 0;
        $totalgrossearnings = 0;

        //totals
        $resultbasicrate = 0;
        $resultcola = 0;
        $resulttransallw = 0;
        $resultricesubsi = 0;
        $resultholidaypay = 0;
        $resultnitediff = 0;
        $resultotpay = 0;
        $resultactingw = 0;
        $resultotheradd = 0;
        $resultgrossearnings = 0;

        $getpayclass =$this->db->select("payclass")
            ->from("payroll_reports_group")
            ->where(array("sysid" => $groupid))->get()->row();

        if ($getpayclass) {

            if ($getpayclass->payclass == 128) {
                $rep_type_code = 'RF';
                $rep_type = 'RANK AND FILE PAYROLL';
            } else if ($getpayclass->payclass == 3078) {
                $rep_type_code = 'T2';
                $rep_type = 'TIER 2 PAYROLL';
            } else if ($getpayclass->payclass == 3077) {
                $rep_type_code = 'T1';
                $rep_type = 'TIER 1 PAYROLL';
            } else {
                $rep_type_code = 'C';
                $rep_type = 'CONFIDENTIAL PAYROLL';
            }
        } else {
            $rep_type_code = 'N/A';
            $rep_type = 'N/A';
        }

        $rep_title = 'EARNINGS REGISTER';

        $html .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);
        $html .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">'.$rep_type.'</b></div>';
        $html .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: '.date("m/d/Y").'</div>';
        $html .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';

        $html .= '<table class="table table-condensed tbl-xs print-table-standard">';
        $html .= '<thead>';
        $html .= '<tr>';

        $html .= '<th>NAME</th>';
        $html .= '<th>BASIC RATE</th>';
        $html .= '<th>COLA</th>';
        $html .= '<th>TRANS ALLW</th>';
        $html .= '<th>RICE SUBSI</th>';
        $html .= '<th>HOLIDAY PAY</th>';
        $html .= '<th>NITE DIFF</th>';
        $html .= '<th>OT PAY</th>';
        $html .= '<th>ACTING ALLW</th>';
        $html .= '<th>OTHER ADD</th>';
        $html .= '<th>GROSS EARNINGS</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';


        $sql = $this->db->select("pcm.sysid")
            ->from("prime_costcenter_main as pcm")
            ->where(array("codes !=" => 0 , "status" => 1))
            ->order_by("pcm.codes" , "ASC")
            ->get();

        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){

                $fetchearregbyemp = $this->db->select("prm.empid , pemp.payclass_id ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
                    ->from("payroll_reports_main as prm")
                    ->join("prime_employee_costcenter as pec" , "pec.empid = prm.empid" , "left")
                    ->join("prime_employee_main as pem" , "pem.sysid = prm.empid" , "left")
                    ->join("prime_employee_main_payclass as pemp" ,"pemp.emp_id  = prm.empid" , "left")
                    ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid AND prt.status = 1" , "left")
                    ->join("person as p" , "p.sysid = pem.personid" , "left")
                    ->where(array("pec.ccid" => $row->sysid , "pem.status" => 1, "prm.groupid" => $groupid , "pec.type" => 1 , "pec.status" => 1))
                    ->order_by("p.lastname" , "asc")
                    ->group_by("prm.empid , pemp.payclass_id,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
                    ->get();

                if($fetchearregbyemp->num_rows() > 0){
                    foreach ($fetchearregbyemp->result() as $earregrow){

                        $basicrate = $earregrow->basic;



                        $getcola = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 251,'status' => 1))->get()->row();
                        $cola = ($getcola) ? ($getcola->amt) : 0;

                        $getricesubsi = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 253,'status' => 1))->get()->row();
                        $ricesubsi = ($getricesubsi) ? ($getricesubsi->amt) : 0;

                        $getholidaypay = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 263,'status' => 1))->get()->row();
                        $holiday_amt = ($getholidaypay && $getholidaypay->amt !== null) ? (float)$getholidaypay->amt : 0;
                        if($holiday_amt == 0){
                            $pt_select = ($group_paytype != 0) ? "SUM(CASE WHEN paytype = 0 THEN amt/2 ELSE amt END) AS holidaypaytotal" : "SUM(amt) AS holidaypaytotal";
                            $pt_holiday = $this->db->select($pt_select, false)->from("payroll_transactions")
                                ->where(array("empid" => $earregrow->empid, "months" => (int)$payrollmonth, "years" => (int)$payrollyear, "typesid" => 263))
                                ->where_not_in("status", array(0, 302))
                                ->where_in("paytype", array(0, $group_paytype))
                                ->get()->row();
                            $holiday_amt = ($pt_holiday && $pt_holiday->holidaypaytotal !== null) ? (float)$pt_holiday->holidaypaytotal : 0;
                            if($holiday_amt == 0){
                                $pmtb_select = ($group_paytype != 0) ? "SUM(CASE WHEN pmtb.paytype = 0 THEN pmtb.amount/2 ELSE pmtb.amount END) AS holidaypaytotal" : "SUM(pmtb.amount) AS holidaypaytotal";
                                $pmtb_holiday = $this->db->select($pmtb_select, false)->from("payroll_manual_transactions_breakdown as pmtb")
                                    ->join("payroll_manual_transactions as pmt", "pmt.sysid = pmtb.groupid", "left")
                                    ->where(array("pmtb.empid" => $earregrow->empid, "pmtb.month" => (int)$payrollmonth, "pmtb.year" => (int)$payrollyear, "pmt.tsysid" => 263))
                                    ->where_in("pmtb.status", array(312, 313))
                                    ->where_in("pmtb.paytype", array(0, $group_paytype))
                                    ->get()->row();
                                $holiday_amt = ($pmtb_holiday && $pmtb_holiday->holidaypaytotal !== null) ? (float)$pmtb_holiday->holidaypaytotal : 0;
                            }
                        }
                        $holidaypay = $holiday_amt;

                        $gettrans_allw = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 252,'status' => 1))->get()->row();
                        $trans_allw = ($gettrans_allw) ? ($gettrans_allw->amt) : 0;

                        $getnitediff = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 358,'status' => 1))->get()->row();
                        $nitediff = ($getnitediff) ? ($getnitediff->amt) : 0;

                        $getotpaywithholiday = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 3010,'status' => 1))->get()->row();
                        $otwithholiday = ($getotpaywithholiday) ? ($getotpaywithholiday->amt) : 0;

                        $getotpayweekends = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 1082,'status' => 1))->get()->row();
                        $otweekends = ($getotpayweekends) ? ($getotpayweekends->amt) : 0;

                        $getotpayweekdays = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 359,'status' => 1))->get()->row();
                        $otpayweekdays = ($getotpayweekdays) ? ($getotpayweekdays->amt) : 0;

                        $otpay =  $otpayweekdays + $otweekends + $otwithholiday;

                        $getactingallw = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 360,'status' => 1))->get()->row();
                        $actingallw = ($getactingallw) ? ($getactingallw->amt) : 0;

                        $getotheradd = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 266,'status' => 1))->get()->row();
                        $otheradd = ($getotheradd) ? ($getotheradd->amt) : 0;

                        $get13thmonth = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 264,'status' => 1))->get()->row();
                        $month13 = ($get13thmonth) ? ($get13thmonth->amt) : 0;

                        $get14thmonth = $this->db->select("amt")->from("payroll_reports_trn")->where(array("payrollid" => $earregrow-> payrollid , "trntype" => 3072,'status' => 1))->get()->row();
                        $month14 = ($get14thmonth) ? ($get14thmonth->amt) : 0;

                        $otheradd = ($otheradd + $month13 + $month14);


                        $grossearnings =  $earregrow->earnings;

                        $html .= '<tr>';
                        $html .= '<td>'.ucwords(strtolower($earregrow->lastname)).', '.ucwords(strtolower($earregrow->firstname)).'</td>';
                        $html .= '<td class="number">'.number_format($basicrate , 2).'</td>';
                        $html .= '<td class="number">'.number_format($cola, 2).'</td>';
                        $html .= '<td class="number">'.number_format($trans_allw, 2).'</td>';
                        $html .= '<td class="number">'.number_format($ricesubsi, 2).'</td>';
                        $html .= '<td class="number">'.number_format($holidaypay , 2).'</td>';
                        $html .= '<td class="number">'.number_format($nitediff, 2).'</td>';
                        $html .= '<td class="number">'.number_format($otpay , 2).'</td>';
                        $html .= '<td class="number">'.number_format($actingallw , 2).'</td>';
                        $html .= '<td class="number">'.number_format($otheradd, 2).'</td>';
                        $html .= '<td class="number">'.number_format($grossearnings, 2).'</td>';
                        $html .= '</tr>';

                        $totalbasicrate += $basicrate;
                        $totalcola += $cola;
                        $totaltransallw += $trans_allw;
                        $totalricesubsi += $ricesubsi;
                        $totalholidaypay += $holidaypay;
                        $totalnitediff += $nitediff;
                        $totalotpay += $otpay;
                        $totalactingw += $actingallw;
                        $totalotheradd += $otheradd;
                        $totalgrossearnings += $grossearnings;

                        $basicrate = 0;
                        $cola = 0;
                        $trans_allw = 0;
                        $ricesubsi = 0;
                        $holidaypay = 0;
                        $nitediff = 0;
                        $otpay = 0;
                        $actingallw = 0;
                        $otheradd = 0;
                        $grossearnings = 0;

                    }
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td class="number bold">'.number_format($totalbasicrate , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalcola, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totaltransallw, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalricesubsi, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalholidaypay , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalnitediff, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalotpay , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalactingw , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalotheradd, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalgrossearnings, 2).'</td>';
                    $html .= '</tr>';

                    $resultbasicrate += $totalbasicrate;
                    $resultcola += $totalcola;
                    $resulttransallw += $totaltransallw;
                    $resultricesubsi += $totalricesubsi;
                    $resultholidaypay += $totalholidaypay;
                    $resultnitediff += $totalnitediff;
                    $resultotpay += $totalotpay;
                    $resultactingw += $totalactingw;
                    $resultotheradd += $totalotheradd;
                    $resultgrossearnings += $totalgrossearnings;

                    $totalbasicrate = 0;
                    $totalcola = 0;
                    $totaltransallw = 0;
                    $totalricesubsi = 0;
                    $totalholidaypay = 0;
                    $totalnitediff = 0;
                    $totalotpay = 0;
                    $totalactingw = 0;
                    $totalotheradd = 0;
                    $totalgrossearnings = 0;
                }
            }
            $html .= '<tr>';
            $html .= '<td>Total</td>';
            $html .= '<td class="number bold">'.number_format($resultbasicrate , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultcola, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resulttransallw, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultricesubsi, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultholidaypay , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultnitediff, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultotpay , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultactingw , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultotheradd, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultgrossearnings, 2).'</td>';
            $html .= '</tr>';

            $html .= '</tbody>';
            $html .= '</table>';


            $html.= '<div class="row">';
            $html.= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html.= '<div>Encoded by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>HRDH</div>';
            $html.= '</div>';
            $html.= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html.= '<div>Checked by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>GA</div>';
            $html.= '</div>';
            $html.= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html.= '<div>Noted by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>FM</div>';
            $html.= '</div>';
            $html.= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';

            $html.= '</div>';
            $html.= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';
            $html.= '<div>Approved by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>P-CEO</div>';
            $html.= '</div>';
            $html.= '</div>';
        }

        $data['html'] = $html;
        echo json_encode($data);
    }


    function getprintdednregbyemp(){
        $data = array();

        $html = '';

        $dataid = $this->input->post('dataid');
        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if(in_array($payrollpayclass,array(128,3077,3078))){
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass, 'status != ' => 302))
            ->get()->row();

        $groupid = ($sql) ?  $sql->sysid : $dataid;
        $ssscont = 0;
        $sssloan = 0;
        $hdmfcont = 0;
        $hdmfloan = 0;
        $pecewaloan = 0;
        $cooploan = 0;
        $pagibigad = 0;
        $otherdedn = 0;
        $hmodedn = 0;
        $deda = 0;
        $electbill = 0;
        $memins = 0;
        $lwop = 0;
        $basetax= 0;
        $deduction = 0;

        //totals
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
        $totaldeduction = 0;


        $resultssscont = 0;
        $resultsssloan = 0;
        $resulthdmfcont = 0;
        $resulthdmfloan = 0;
        $resultpecewaloan = 0;
        $resultcooploan = 0;
        $resultpagibigad = 0;
        $resultotherdedn = 0;
        $resulthmodedn = 0;
        $resultdeda = 0;
        $resultelectbill = 0;
        $resultmemins = 0;
        $resultlwop = 0;
        $resultbasetax= 0;
        $resultdeduction = 0;
        $paytype = 0;
        $payclass = 0;
        $getpaypaytype = $this->db->select("payclass,paytype")->from("payroll_reports_group")
            ->where(array("sysid" => $groupid))->get()->row();
        $paytype = ($getpaypaytype) ? $getpaypaytype->paytype : 0;
        $payclass = ($getpaypaytype) ? $getpaypaytype->payclass : 0;

        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;


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

        $html .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);
        $html .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">'.$rep_type.'</b></div>';
        $html .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: '.date("m/d/Y").'</div>';
        $html .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';


        $html .= '<div class="row">';
        $html .= '<div class="col-md-12 col-sm-12 col-xs-12">';
        $html .= '<table class="table table-condensed tbl-xs print-table-standard">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>NAME</th>';
        $html .= '<th>SSS CONT</th>';
        $html .= '<th>SSS LOAN</th>';
        $html .= '<th>HDMF CONT</th>';
        $html .= '<th>HDMF LOAN</th>';
        $html .= '<th>PECEWA LOAN</th>';
        $html .= '<th>COOP LOAN</th>';
        $html .= '<th>PAGIBIG AD</th>';
        $html .= '<th>OTHER DEDN</th>';
        $html .= '<th>HMO DEDN</th>';
        $html .= '<th>DED A</th>';
        $html .= '<th>ELECT BILL</th>';
        $html .= '<th>MEM INS</th>';
        $html .= '<th>LWOP</th>';
        $html .= '<th>BASE TAX</th>';
        $html .= '<th>TOTAL DEDN</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';


        $sql = $this->db->select("pcm.sysid")
            ->from("prime_costcenter_main as pcm")
            ->where(array("codes !=" => 0 , "status" => 1))
            ->order_by("codes" , "ASC")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $fetchdednregbyemp = $this->db->select("prm.empid ,pem.sysid,prg.months,prg.years,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname ")
                    ->from("payroll_reports_main as prm")
                    ->join("prime_employee_costcenter as pec" , "pec.empid = prm.empid" , "left")
                    ->join("prime_employee_main as pem" , "pem.sysid = prm.empid" , "left")
                    ->join("payroll_reports_group as prg" , "prg.sysid = prm.groupid" , "left")
                    ->join("prime_employee_main_payclass as pemp" ,"pemp.emp_id  = prm.empid" , "left")
                    ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid AND prt.status = 1" , "left")
                    ->join("person as p" , "p.sysid = pem.personid" , "left")
                    ->where(array("pec.ccid" => $row->sysid , "pem.status" => 1, "prm.groupid" => $groupid , "pec.type" => 1 , "pec.status" => 1))
                    ->order_by("p.lastname" , "asc")
                    ->group_by("prm.empid ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname")
                    ->get();
                if($fetchdednregbyemp->num_rows() > 0){
                    foreach ($fetchdednregbyemp->result() as $dednrow){


                        $resultdeduction += $dednrow->deductions;

                        $getssscont = $this->db->select("SUM(amt) AS totalssscont")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 72, 'status' => 1))->get()->row();
                        $ssscont = ($getssscont) ? number_format(($getssscont->totalssscont), 6, '.', '') : 0;

                        $getsssloan = $this->db->select("SUM(amt) AS totalsssloan")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 257, 'status' => 1))->get()->row();
                        $sssloan = ($getsssloan) ? number_format(($getsssloan->totalsssloan), 6, '.', '') : 0;

                        $gethdmfcont = $this->db->select("SUM(amt) AS totalhdmfcont")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 74, 'status' => 1))->get()->row();
                        $hdmfcont = ($gethdmfcont) ? number_format(($gethdmfcont->totalhdmfcont), 6, '.', '') : 0;

                        $gethdmfloan = $this->db->select("SUM(amt) AS totalhdmfloan")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 258, 'status' => 1))->get()->row();
                        $hdmfloan = ($gethdmfloan) ? number_format(($gethdmfloan->totalhdmfloan), 6, '.', '') : 0;

                        $getcignainsurance =  $this->db->select("SUM(amt) AS totalcignainsurance")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 3007, 'status' => 1))->get()->row();
                        $cignainsurance = ($getcignainsurance) ? number_format(($getcignainsurance->totalcignainsurance), 6, '.', '') : 0;

                        $getagencyunions =  $this->db->select("SUM(amt) AS totalagencyunions")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 3006, 'status' => 1))->get()->row();
                        $agencyunions = ($getagencyunions) ? number_format(($getagencyunions->totalagencyunions), 6, '.', '') : 0;

                        $getpecewaloan = $this->db->select("SUM(amt) AS totalpecewaloan")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 254, 'status' => 1))->get()->row();
                        $totalloanpecewa = ($getpecewaloan) ? number_format(($getpecewaloan->totalpecewaloan), 6, '.', '') : 0;

                        $pecewaloan = ($cignainsurance + $agencyunions + $totalloanpecewa);

                        $getcooploan = $this->db->select("SUM(amt) AS totalcooploan")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 255, 'status' => 1))->get()->row();
                        $cooploan = ($getcooploan) ? number_format(($getcooploan->totalcooploan), 6, '.', '') : 0;

                        $getpagibigad = $this->db->select("SUM(amt) AS totalpagibigadd")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 259, 'status' => 1))->get()->row();
                        $pagibigad = ($getpagibigad) ? number_format(($getpagibigad->totalpagibigadd), 6, '.', '') : 0;

                        $getotherdedn = $this->db->select("SUM(amt) AS totalotherdedn")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 261, 'status' => 1))->get()->row();
                        $otherdedn = ($getotherdedn) ? number_format(($getotherdedn->totalotherdedn), 6, '.', '') : 0;

                        $gethmodedn = $this->db->select("SUM(amt) AS totalhmodedn")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 260, 'status' => 1))->get()->row();
                        $hmodedn = ($gethmodedn) ? number_format(($gethmodedn->totalhmodedn), 6, '.', '') : 0;

                        $getpecoloans = $this->db->select("SUM(amt) AS totalpecoloan")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 1079, 'status' => 1))->get()->row();
                        $pecoloan = ($getpecoloans) ? number_format(($getpecoloans->totalpecoloan), 6, '.', '') : 0;

                        $getdeda = $this->db->select("SUM(amt) AS totalded")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 363, 'status' => 1))->get()->row();
                        $deda = ($getdeda) ? number_format(($getdeda->totalded), 6, '.', '') : 0;

                        $deda = $deda + $pecoloan;

                        $getelectbill = $this->db->select("SUM(amt) AS totalelectbill")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 256, 'status' => 1))->get()->row();
                        $electbill = ($getelectbill) ? number_format(($getelectbill->totalelectbill), 6, '.', '') : 0;

                        $getmemins = $this->db->select("SUM(amt) AS totalmemins")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 3009, 'status' => 1))->get()->row();
                        $memins = ($getmemins) ? number_format(($getmemins->totalmemins), 6, '.', '') : 0;

                        $getlwop = $this->db->select("SUM(amt) AS totallwop")->from("payroll_reports_trn")->where(array("payrollid" => $dednrow-> payrollid , "trntype" => 262, 'status' => 1))->get()->row();
                        $lwop_amt = ($getlwop && $getlwop->totallwop !== null) ? (float)$getlwop->totallwop : 0;
                        if($lwop_amt == 0){
                            $pt_select = ($paytype != 0) ? "SUM(CASE WHEN paytype = 0 THEN amt/2 ELSE amt END) AS totallwop" : "SUM(amt) AS totallwop";
                            $pt_lwop = $this->db->select($pt_select, false)->from("payroll_transactions")
                                ->where(array("empid" => $dednrow->empid, "months" => (int)$dednrow->months, "years" => (int)$dednrow->years, "typesid" => 262))
                                ->where_not_in("status", array(0, 302))
                                ->where_in("paytype", array(0, $paytype))
                                ->get()->row();
                            $lwop_amt = ($pt_lwop && $pt_lwop->totallwop !== null) ? (float)$pt_lwop->totallwop : 0;
                            if($lwop_amt == 0){
                                $pmtb_select = ($paytype != 0) ? "SUM(CASE WHEN pmtb.paytype = 0 THEN pmtb.amount/2 ELSE pmtb.amount END) AS totallwop" : "SUM(pmtb.amount) AS totallwop";
                                $pmtb_lwop = $this->db->select($pmtb_select, false)->from("payroll_manual_transactions_breakdown as pmtb")
                                    ->join("payroll_manual_transactions as pmt", "pmt.sysid = pmtb.groupid", "left")
                                    ->where(array("pmtb.empid" => $dednrow->empid, "pmtb.month" => (int)$dednrow->months, "pmtb.year" => (int)$dednrow->years, "pmt.tsysid" => 262))
                                    ->where_in("pmtb.status", array(312, 313))
                                    ->where_in("pmtb.paytype", array(0, $paytype))
                                    ->get()->row();
                                $lwop_amt = ($pmtb_lwop && $pmtb_lwop->totallwop !== null) ? (float)$pmtb_lwop->totallwop : 0;
                            }
                        }
                        $lwop = $lwop_amt;

                        $basetax += $dednrow->tax;

                        $deduction = $dednrow->deductions;


                        $html .= '<tr>';
                        $html .= '<td>'.ucwords(strtolower($dednrow->lastname)).', '.ucwords(strtolower($dednrow->firstname)).'</td>';
                        $html .= '<td class="number">'.number_format($ssscont , 2).'</td>';
                        $html .= '<td class="number">'.number_format($sssloan, 2).'</td>';
                        $html .= '<td class="number">'.number_format($hdmfcont, 2).'</td>';
                        $html .= '<td class="number">'.number_format($hdmfloan, 2).'</td>';
                        $html .= '<td class="number">'.number_format($pecewaloan , 2).'</td>';
                        $html .= '<td class="number">'.number_format($cooploan, 2).'</td>';
                        $html .= '<td class="number">'.number_format($pagibigad , 2).'</td>';
                        $html .= '<td class="number">'.number_format($otherdedn , 2).'</td>';
                        $html .= '<td class="number">'.number_format($hmodedn, 2).'</td>';
                        $html .= '<td class="number">'.number_format($deda, 2).'</td>';
                        $html .= '<td class="number">'.number_format($electbill, 2).'</td>';
                        $html .= '<td class="number">'.number_format($memins, 2).'</td>';
                        $html .= '<td class="number">'.number_format($lwop, 2).'</td>';
                        $html .= '<td class="number">'.number_format($basetax, 2).'</td>';
                        $html .= '<td class="number">'.number_format($deduction, 2).'</td>';
                        $html .= '</tr>';


                        $totalssscont += $ssscont;
                        $totalsssloan += $sssloan;
                        $totalhdmfcont += $hdmfcont;
                        $totalhdmfloan += $hdmfloan;
                        $totalpecewaloan += $pecewaloan;
                        $totalcooploan += $cooploan;
                        $totalpagibigad += $pagibigad;
                        $totalotherdedn += $otherdedn;
                        $totalhmodedn += $hmodedn;
                        $totaldeda += $deda;
                        $totalelectbill += $electbill;
                        $totalmemins += $memins;
                        $totallwop += $lwop;
                        $totalbasetax += $basetax;
                        $totaldeduction += $deduction;




                        $ssscont = 0;
                        $sssloan = 0;
                        $hdmfcont = 0;
                        $hdmfloan = 0;
                        $pecewaloan = 0;
                        $cooploan = 0;
                        $pagibigad = 0;
                        $otherdedn = 0;
                        $hmodedn = 0;
                        $deda = 0;
                        $electbill = 0;
                        $memins = 0;
                        $lwop = 0;
                        $basetax= 0;
                        $deduction = 0;
                    }
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td class="number bold">'.number_format($totalssscont , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalsssloan, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalhdmfcont, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalhdmfloan, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalpecewaloan , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalcooploan, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalpagibigad , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalotherdedn , 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalhmodedn, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totaldeda, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalelectbill, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalmemins, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totallwop, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totalbasetax, 2).'</td>';
                    $html .= '<td class="number bold">'.number_format($totaldeduction, 2).'</td>';
                    $html .= '</tr>';

                    $resultssscont += $totalssscont;
                    $resultsssloan += $totalsssloan;
                    $resulthdmfcont += $totalhdmfcont;
                    $resulthdmfloan += $totalhdmfloan;
                    $resultpecewaloan += $totalpecewaloan;
                    $resultcooploan += $totalcooploan;
                    $resultpagibigad += $totalpagibigad;
                    $resultotherdedn += $totalotherdedn;
                    $resulthmodedn += $totalhmodedn;
                    $resultdeda += $totaldeda;
                    $resultelectbill += $totalelectbill;
                    $resultmemins += $totalmemins;
                    $resultlwop += $totallwop;
                    $resultbasetax+= $totalbasetax;

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
                    $totaldeduction = 0;
                }
            }

            $html .= '<tr>';
            $html .= '<td>Total</td>';
            $html .= '<td class="number bold">'.number_format($resultssscont , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultsssloan, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resulthdmfcont, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resulthdmfloan, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultpecewaloan , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultcooploan, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultpagibigad , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultotherdedn , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resulthmodedn, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultdeda, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultelectbill, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultmemins, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultlwop, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultbasetax, 2).'</td>';
            $html .= '<td class="number bold">'.number_format($resultdeduction, 2).'</td>';
            $html .= '</tr>';


            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';


            $html.= '<div class="row">';
            $html.= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html.= '<div>Encoded by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>HRDH</div>';
            $html.= '</div>';
            $html.= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html.= '<div>Checked by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>GA</div>';
            $html.= '</div>';
            $html.= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
            $html.= '<div>Noted by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>FM</div>';
            $html.= '</div>';
            $html.= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';

            $html.= '</div>';
            $html.= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';
            $html.= '<div>Approved by:</div>';
            $html.= '<div>____________</div>';
            $html.= '<div>P-CEO</div>';
            $html.= '</div>';
            $html.= '</div>';
        }
        $data['html'] = $html;
        echo json_encode($data);
    }
    function approvepayroll(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func ='';
        $qry = false;
        $title = '';
        //301 approved
        $this->db->trans_begin();

        $gettransactiondetails = $this->db->select("prg.years,prg.months,prg.payclass,prg.paytype , prm.empid")
            ->from("payroll_reports_group as prg")
            ->join("payroll_reports_main as prm" , "prm.groupid = prg.sysid" , "left")
            ->where(array("prg.sysid" => $dataid))
            ->get();
        if($gettransactiondetails->num_rows() > 0){
            foreach ($gettransactiondetails->result() as $trnrow){
                $updatestatarr = array(
                    'status' => 312
                );
                $getmonth = $trnrow->months;
                $getyear = $trnrow->years;
                $payclass = $trnrow->payclass;
                $paytype =  $trnrow->paytype;

                /*   $this->db->where(array("status" => 313 , "month" => $getmonth , "year" => $getyear));
                   $this->db->update("payroll_manual_transactions_breakdown", $updatestatarr); */

                if($payclass == 128){
                    $this->db->where(array("paytype" => $paytype));
                }
                $this->db->set('b.status',312);
                $this->db->where(array("b.status" => 313 , "b.month" => $getmonth , "b.year" => $getyear , "b.empid" => $trnrow->empid));
                $this->db->where('a.empid = b.empid');
                $this->db->where('a.sysid = b.groupid');
                $this->db->update('payroll_manual_transactions as a, payroll_manual_transactions_breakdown as b');

                if($payclass == 128){
                    $this->db->where(array("a.paytype" => $paytype));
                }

                $this->db->set('a.status',312);
                $this->db->where(array("a.status" => 313 , "a.month" => $getmonth , "a.year" => $getyear , "a.payclass" => $payclass , "a.empid" => $trnrow->empid));
                $this->db->update('payroll_anual_tax_distribution as a');
                $data['error'] = $this->db->_error_message();
            }

        }

        $this->db->where(array("sysid" => $dataid));
        $updatearr = array(
            'status' => 301,
            'updatedby' => user_id()
        );
        $this->db->update("payroll_reports_group" , $updatearr);

        $this->db->_error_message();
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Payroll has been approved.';
            $func ='success';
            $qry = true;
            $title = 'Success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Error processing payroll!';
            $func ='error';
            $qry = false;
            $title = 'Failed';
        }
        $data['id']  = $dataid;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;
        echo json_encode($data);
    }
    function disapprovepayroll(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $msg = '';
        $func ='';
        $qry = false;
        $title = '';
        //301 approved
        $this->db->trans_begin();

        $gettrail = $this->db->select("trmt.trnid")->from("transaction_request_main_trails as trmt")
            ->join("transaction_request_main as trm"  , "trm.sysid = trmt.trnid")
            ->where(array("trmt.dataid" => $dataid , "trmt.status" => 1 , "trm.flowid" => 10))->get()->row();

        if($gettrail){
            $trnid = $gettrail->trnid;
            $this->db->where(array("dataid" => $dataid , "status" => 1 , "trnid" => $trnid));
            $this->db->update("transaction_request_main_trails" , array("status" => 0 , "updatedby" => user_id()));

            $data['trnid'] = $trnid;

            $this->db->where(array("sysid" => $trnid , "status" => 1 , "flowid" => 10));
            $this->db->update("transaction_request_main" , array("status" => 0 , "updatedby" => user_id()));
            $data['trailmainerr'] = $this->db->_error_message();


            /* $this->db->set('a.status', '302');
 $this->db->set('c.status', '302');

 $this->db->where('a.sysid', $dataid);
 $this->db->where('a.sysid = b.groupid');
 $this->db->where('b.sysid = c.payrollid');

 $this->db->update('payroll_reports_group as a, payroll_reports_main as b,payroll_reports_trn as c'); */

            $this->db->where(array("sysid" => $dataid));
            $updatearr = array(
                'status' => 302,
                'updatedby' => user_id()
            );
            $this->db->update("payroll_reports_group" , $updatearr);

            $this->db->_error_message();
        }


        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Payroll has been disapproved.';
            $func ='success';
            $qry = true;
            $title = 'Success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Error processing payroll!';
            $func ='error';
            $qry = false;
            $title = 'Failed';
        }
        $data['id']  = $dataid;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;
        echo json_encode($data);
    }
    function gettardinessdata(){
        $data = array();
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $totallate = 0;

        $sql = $this->db->select("pem.sysid,pem.empid , p.firstname , p.lastname , peb.bioid")
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid" , "left")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $getworkshift  = $this->db->select("pemwm.empid , pemwm.workshift_id , pemw.am_start , pemw.am_end , pemw.pm_start , pemw.pm_end")
                    ->from("prime_employee_main_workshift_matrix as pemwm")
                    ->join("prime_employee_main_workshift as pemw" , "pemw.sysid = pemwm.workshift_id","left")
                    ->where(array("pemwm.empid" => $row->sysid))
                    ->get()->row();
                /*  $data['empworkshift'][] = array(
                      'am_start' => ($getworkshift) ? $getworkshift->am_start : '',
                      'am_end' => ($getworkshift) ?  $getworkshift->am_end : '',
                      'pm_start' => ($getworkshift) ?  $getworkshift->pm_start : '',
                      'pm_end' => ($getworkshift) ?  $getworkshift->pm_end : ''
                  ); */

                $getlate = $this->db->query("SELECT sysid, bioid,logdate , logtime FROM prime_employee_attendance_timelogs
                        WHERE bioid = '".$row->bioid."' AND logdate BETWEEN '".$fromdate."' AND '".$todate."'");

                foreach ($getlate->result() as $logtimerow){

                    /* $totallate +=  $logtimerow->logtime - $empworkshift[$key];
                       $data['late'][] = array(
                           'bioid' => $logtimerow->bioid,
                           'logtime' => $logtimerow->logtime,
                           'workshift' => $empworkshift[$key],
                           'totallate' => $totallate
                       ); */
                }


                if($getworkshift){
                    $data['tardinessdata'][] = array(
                        'id' => $num++,
                        'empid' => $row->empid,
                        'name' => $row->lastname.', '.$row->firstname,
                        'bioid' => $row->bioid,
                        'amstart' => $getworkshift->am_start,
                        'amend' => $getworkshift->am_end,
                        'pmstart' => $getworkshift->pm_start,
                        'pmend' => $getworkshift->pm_end,
                    );
                }


            }
        }

        echo json_encode($data);
    }
    function printtemppayslip(){
        $employeearr = [];
        $employerarr = [];
        $id = $this->input->post('id');
        $month_input = $this->input->post('month');
        $year_input = $this->input->post('year');
        $paytype = $this->input->post('paytype');
        $payclass = $this->input->post('payclass');
        $html = '';
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
                $compute = compute_employee_netpay($empid, $month, $year, $paytype, 1, $payclass, 1);
                $data['compute'] = $compute;
                if($compute) {
                    $func = 'success';
                    $index = 0;
                    foreach ($compute->deductions as $drow) {
                        $employeearr[$index] = $drow['amt'];
                        $employerarr[$index] = $drow['amtcomp'];
                        $index++;
                    }

                    $sssemployee = ($employeearr[0]) ? number_format($employeearr[0] , 2) : 0;
                    $philhealthemployee = ($employeearr[1]) ? number_format($employeearr[1] , 2) : 0;
                    $hdmfemployee = ($employeearr[2]) ? number_format($employeearr[2] , 2) : 0;
                    $taxemployee = ($employeearr[3]) ? number_format($employeearr[3] , 2) : 0;

                    $sssemployer = ($compute->deductions) ? number_format($employerarr[0] , 2) : 0;
                    $philhealthemployer = ($compute->deductions) ? number_format($employerarr[1] , 2) : 0;
                    $hdmfemployer = ($compute->deductions) ? number_format($employerarr[2] , 2) : 0;
                    $taxemployer = ($compute->deductions) ? number_format($employerarr[3] , 2) : 0;

                    $html .= '<table class="table table-responsive table-bordered  tbl-xs">';
                    $html .= '<thead>';
                    $html .= '<th>Descriptions</th>';
                    $html .= '<th>Amount</th>';
                    $html .= '<th>Descriptions</th>';
                    $html .= '<th>Amount</th>';
                    $html .= '<th>Ded</th>';
                    $html .= '<th>Employee</th>';
                    $html .= '<th>Employer</th>';
                    $html .= '<th>Descriptions</th>';
                    $html .= '<th>Amount</th>';
                    $html .= '</thead>';
                    $html .= '<tbody>';

                    $html .= '<tr>';
                    $html .= '<td>Premiums</td>';
                    $html .= '<td>'.number_format($compute->premiums, 2).'</td>';
                    $html .= '<td>Taxable Amount</td>';
                    $html .= '<td>' . number_format($compute->taxableamount, 2) . '</td>';
                    $html .= '<td>SSS</td>';
                    $html .= '<td>'.$sssemployee.'</td>';
                    $html .= '<td>'.$sssemployer.'</td>';
                    $html .= '<td>Net Pay</td>';
                    $html .= '<td>' . number_format($compute->netpay, 2) . '</td>';
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td>Basic Salary</td>';
                    $html .= '<td>' . number_format($compute->basic, 2) . '</td>';
                    $html .= '<td>Non-Taxable Amount</td>';
                    $html .= '<td>' . number_format($compute->nontaxableamount, 2) . '</td>';
                    $html .= '<td>PHILHEALTH</td>';
                    $html .= '<td>'.$philhealthemployee.'</td>';
                    $html .= '<td>'.$philhealthemployer.'</td>';
                    if($payclass != 128){
                        $html .= '<td>Net 15</td>';
                        $html .= '<td>' . number_format($compute->net15, 2) . '</td>';
                    }

                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td>Holiday Pay</td>';
                    $html .= '<td>' . number_format($compute->total_holiday, 2) . '</td>';
                    $html .= '<td>Other (-)</td>';
                    $html .= '<td>' . number_format($compute->totalotherssub, 2) . '</td>';
                    $html .= '<td>HDMF</td>';
                    $html .= '<td>'.$hdmfemployee.'</td>';
                    $html .= '<td>'.$hdmfemployer.'</td>';
                    if($payclass != 128) {
                        $html .= '<td>Net 30</td>';
                        $html .= '<td>' . number_format($compute->net30, 2) . '</td>';
                    }
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td>Earnings</td>';
                    $html .= '<td>' . number_format($compute->earnings, 2) . '</td>';
                    $html .= '<td>Other (+)</td>';
                    $html .= '<td>' . number_format($compute->totalothersadd, 2) . '</td>';
                    $html .= '<td>TAX</td>';
                    $html .= '<td>'.$taxemployee.'</td>';
                    $html .= '<td>'.$taxemployer.'</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td>Loans</td>';
                    $html .= '<td>' . number_format($compute->loans, 2) . '</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td>Total</td>';
                    $html .= '<td>' . number_format($compute->deductionamount, 2) . '</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '</tr>';
                    $html .= '</tbody>';

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
        $data['html'] = $html;

        echo json_encode($data);
    }
    function addaddictionalprem(){
        $data = array();
        $hiddenpremval = $this->input->post('hiddenpremval');
        $amountprem = $this->input->post('amountprem');
        $empid = $this->input->post('hiddenempidprem');
        $paytype = $this->input->post('prempaytype');
        $monthdevide = $this->input->post('monthdevide');
        $amountpermonth = $this->input->post('amountpermonth');
        $monthprem = $this->input->post('monthprem');
        $yearprem = $this->input->post('yearprem');

        $this->db->trans_begin();

        $checkpayclass = $this->db->select("payclass_id")->from("prime_employee_main_payclass")
            ->where(array("status" => 1, "emp_id" => $empid))->get()->row();
        $payclass = ($checkpayclass) ? $checkpayclass->payclass_id : '';


        if($payclass == 128){
            $insarr = array(
                'empid' => $empid,
                'tsysid' => $hiddenpremval,
                'amount' => $amountprem,
                'monthdevide' => $monthdevide,
                'amountpermonth' => $amountpermonth,
                'createdby' => user_id()
            );
            $sql = $this->db->insert("payroll_manual_transactions" , $insarr);
            $getlastid = $this->db->select("sysid")->from("payroll_manual_transactions")->limit(1)
                ->order_by("sysid", "desc")->get()->row();
            $paytype = 1;
            for($index=1;$index<=$monthdevide * 2;$index++){

                if($paytype == 3){
                    $paytype = 1;
                    $monthprem++;
                }
                if($monthprem == 13){
                    $monthprem = 1;
                    $yearprem++;
                }

                $monthlycharges = array(
                    'empid' => $empid,
                    'groupid' => ($getlastid) ? $getlastid->sysid : 0,
                    'amount' => $amountpermonth / 2,
                    'month' => $monthprem,
                    'year' => $yearprem,
                    'createdby' => user_id(),
                    'paytype' => $paytype
                );
                $this->db->insert("payroll_manual_transactions_breakdown" , $monthlycharges);

                $paytype = $paytype + 1;
            }
        }else{
            $insarr = array(
                'empid' => $empid,
                'tsysid' => $hiddenpremval,
                'amount' => $amountprem,
                'monthdevide' => $monthdevide,
                'amountpermonth' => $amountpermonth,
                'createdby' => user_id()
            );
            $sql = $this->db->insert("payroll_manual_transactions" , $insarr);
            $getlastid = $this->db->select("sysid")->from("payroll_manual_transactions")->limit(1)
                ->order_by("sysid", "desc")->get()->row();
            $paytype = 1;
            for($index=1;$index<=$monthdevide;$index++){
                if($monthprem == 13){
                    $monthprem = 1;
                    $yearprem++;
                }
                if($paytype == 3){
                    $paytype = 1;
                }
                $monthlycharges = array(
                    'empid' => $empid,
                    'groupid' => ($getlastid) ? $getlastid->sysid : 0,
                    'amount' => $amountpermonth,
                    'month' => $monthprem,
                    'year' => $yearprem,
                    'createdby' => user_id(),
                    'paytype' => $paytype
                );
                $this->db->insert("payroll_manual_transactions_breakdown" , $monthlycharges);
                $monthprem++;
                $paytype = $paytype + 1;
            }
        }

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Premiums added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Adding Premiums Fail.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['tabid'] = $hiddenpremval;

        echo json_encode($data);
    }

    function addaddictionalded(){
        $data = array();


        $hiddenempidded = $this->input->post('hiddenempidded');
        $deducttype = $this->input->post('deducttype');
        $amountded = $this->input->post('amountded');
        $monthded = $this->input->post('monthded');
        $monthdevide = $this->input->post('monthdevide');
        $yearded = $this->input->post('yearded');
        $amountpermonth = $this->input->post('amountpermonth');
        $empid = $this->input->post('hiddenempidded');

        $data['monthstart'] = $monthded;

        $this->db->trans_begin();
        $checkpayclass = $this->db->select("payclass_id")->from("prime_employee_main_payclass")
            ->where(array("status" => 1, "emp_id" => $empid))->get()->row();
        $payclass = ($checkpayclass) ? $checkpayclass->payclass_id : '';
        $data['payclass'] = $payclass;

        if($payclass == 128){
            $insarr = array(
                'empid' => $hiddenempidded,
                'tsysid' => $deducttype,
                'amount' => $amountded,
                'monthdevide' => $monthdevide,
                'amountpermonth' => $amountpermonth,
                'createdby' => user_id()
            );
            $sql = $this->db->insert("payroll_manual_transactions" , $insarr);
            $data['error1'] = $this->db->_error_message();

            $getlastid = $this->db->select("sysid")->from("payroll_manual_transactions")->limit(1)
                ->order_by("sysid", "desc")->get()->row();
            $paytype = 1;
            for($index=1;$index<=$monthdevide * 2;$index++){

                if($paytype == 3){
                    $paytype = 1;
                    $monthded++;
                }
                if($monthded == 13){
                    $monthded = 1;
                    $yearded++;
                }

                $monthlycharges = array(
                    'empid' => $hiddenempidded,
                    'groupid' => ($getlastid) ? $getlastid->sysid : 0,
                    'amount' => $amountpermonth / 2,
                    'month' => $monthded,
                    'year' => $yearded,
                    'createdby' => user_id(),
                    'paytype' => $paytype
                );
                $this->db->insert("payroll_manual_transactions_breakdown" , $monthlycharges);
                $data['error2'] = $this->db->_error_message();

                $paytype = $paytype + 1;
            }
        }else{
            $insarr = array(
                'empid' => $hiddenempidded,
                'tsysid' => $deducttype,
                'amount' => $amountded,
                'monthdevide' => $monthdevide,
                'amountpermonth' => $amountpermonth,
                'createdby' => user_id()
            );
            $sql = $this->db->insert("payroll_manual_transactions" , $insarr);
            $data['error1'] = $this->db->_error_message();
            $getlastid = $this->db->select("sysid")->from("payroll_manual_transactions")->limit(1)
                ->order_by("sysid", "desc")->get()->row();
            $paytype = 1;
            for($index=1;$index<=$monthdevide;$index++){
                if($monthded == 13){
                    $monthded = 1;
                    $yearded++;
                }
                if($paytype == 3){
                    $paytype = 1;
                }
                $monthlycharges = array(
                    'empid' => $hiddenempidded,
                    'groupid' => ($getlastid) ? $getlastid->sysid : 0,
                    'amount' => $amountpermonth,
                    'month' => $monthded,
                    'year' => $yearded,
                    'createdby' => user_id(),
                    'paytype' => $paytype
                );
                $this->db->insert("payroll_manual_transactions_breakdown" , $monthlycharges);
                $data['error2'] = $this->db->_error_message();
                $monthded++;
                $paytype = $paytype + 1;
            }
        }




        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Deductions added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Adding Deductions Fail.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;


        echo json_encode($data);
    }
    function addadditionalloans(){
        $data = array();
        $hiddenloansval = $this->input->post('hiddenloansval');
        $amountloans = $this->input->post('amountloans');
        $empid = $this->input->post('hiddenempidloans');
        $monthdevide = $this->input->post('monthdevide');
        $amountpermonth = $this->input->post('amountpermonth');
        $monthloans = $this->input->post('monthloans');
        $yearloans = $this->input->post('yearloans');
        $typeofloans = $this->input->post('typeofloans');


        $this->db->trans_begin();

        $checkpayclass = $this->db->select("payclass_id")->from("prime_employee_main_payclass")
            ->where(array("status" => 1, "emp_id" => $empid))->get()->row();
        $payclass = ($checkpayclass) ? $checkpayclass->payclass_id : '';


        $this->db->set('a.status', 312);
        $this->db->set('b.status', 312);
        if($typeofloans > 0){
            $this->db->where('a.subtype', $typeofloans);
        }
        $this->db->where('a.tsysid', $hiddenloansval);
        $this->db->where('a.sysid = b.groupid');
        $this->db->where('a.status',313);
        $this->db->where('b.status',313);
        $this->db->where('a.empid',$empid);
        $this->db->where('b.empid',$empid);
        $this->db->update('payroll_manual_transactions as a, payroll_manual_transactions_breakdown as b');
        if($payclass == 128){
            $insarr = array(
                'empid' => $empid,
                'tsysid' => $hiddenloansval,
                'subtype' => ($typeofloans  > 0) ? $typeofloans  : null,
                'amount' => $amountloans,
                'monthdevide' => $monthdevide,
                'amountpermonth' => $amountpermonth,
                'createdby' => user_id()
            );
            $sql = $this->db->insert("payroll_manual_transactions" , $insarr);

            $getlastid = $this->db->select("sysid")->from("payroll_manual_transactions")->limit(1)
                ->order_by("sysid", "desc")->get()->row();
            $paytype = 1;
            for($index=1;$index<=$monthdevide * 2;$index++){

                if($paytype == 3){
                    $paytype = 1;
                    $monthloans++;
                }
                if($monthloans == 13){
                    $monthloans = 1;
                    $yearloans++;
                }
                $monthlycharges = array(
                    'empid' => $empid,
                    'groupid' => ($getlastid) ? $getlastid->sysid : 0,
                    'amount' => $amountpermonth / 2,
                    'month' => $monthloans,
                    'year' => $yearloans,
                    'createdby' => user_id(),
                    'paytype' => $paytype
                );
                $this->db->insert("payroll_manual_transactions_breakdown" , $monthlycharges);


                $paytype = $paytype + 1;
            }
        }else{
            $insarr = array(
                'empid' => $empid,
                'tsysid' => $hiddenloansval,
                'amount' => $amountloans,
                'monthdevide' => $monthdevide,
                'amountpermonth' => $amountpermonth,
                'createdby' => user_id()
            );
            $sql = $this->db->insert("payroll_manual_transactions" , $insarr);

            $getlastid = $this->db->select("sysid")->from("payroll_manual_transactions")->limit(1)
                ->order_by("sysid", "desc")->get()->row();
            $paytype = 1;
            for($index=1;$index<=$monthdevide;$index++){
                if($monthloans == 13){
                    $monthloans = 1;
                    $yearloans++;
                }
                if($paytype == 3){
                    $paytype = 1;
                }
                $monthlycharges = array(
                    'empid' => $empid,
                    'groupid' => ($getlastid) ? $getlastid->sysid : 0,
                    'amount' => $amountpermonth,
                    'month' => $monthloans,
                    'year' => $yearloans,
                    'createdby' => user_id(),
                    'paytype' =>1
                );
                $this->db->insert("payroll_manual_transactions_breakdown" , $monthlycharges);
                $monthloans++;
                $paytype = $paytype + 1;
            }
        }

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Loans added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Adding Loans Fail.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['tabid'] = $hiddenloansval;

        echo json_encode($data);
    }
    function getemployeetaxes(){
        $data = array();
        $payclass=  $this->input->post('payclass');
        $month=  $this->input->post('month');
        $year = $this->input->post('year');
        $print = $this->input->post('print');
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $html .= '<table class="table table-bordered table-responsive tbl-sm">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Employee</th>';
        $html .= '<th>WTAX</th>';
        $html .= '</thead>';
        $html .= '<tbody>';

        if($payclass > 0){
            $this->db->where(array("pe.payclass" => $payclass));
        }

        $sql = $this->db->select("pe.empid,pe.payclass,p.lastname,p.firstname,SUM(prm.tax) as totaltax")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem","pem.sysid = pe.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("payroll_reports_main as prm","prm.empid = pe.empid","left")
            ->join("payroll_reports_group as prg","prg.sysid = prm.groupid","left")
            ->where(array("pe.status" => 1,"prg.years" => $year , "prg.months" => $month , "prg.status" => 301))
            ->order_by("p.lastname")
            ->group_by("pe.empid,pe.payclass,p.lastname,p.firstname,prm.tax")
            ->get();
        $data['taxerror'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            $rankandfilenum = 1;
            $confidentialnum = 1;
            foreach ($sql->result() as $row){
                if($payclass <= 0){
                    if($row->payclass == 128){
                        $data['rankandfiletaxdata'][]=  array(
                            "id" => $rankandfilenum++,
                            "empname" => $row->lastname.', '.$row->firstname,
                            "tax" => $row->totaltax
                        );
                    }else{
                        $data['confidentialtaxdata'][]=  array(
                            "id" => $confidentialnum++,
                            "empname" => $row->lastname.', '.$row->firstname,
                            "tax" => $row->totaltax
                        );
                    }
                }else if($payclass == 128){
                    if($print > 0){
                        $html .= '<tr>';
                        $html .= '<td>'.$rankandfilenum.'</td>';
                        $html .= '<td>'.$row->lastname.', '.$row->firstname.'</td>';
                        $html .= '<td>'.number_format($row->totaltax , 2).'</td>';
                        $html .= '</tr>';
                    }
                    $data['rankandfiletaxdata'][]=  array(
                        "id" => $rankandfilenum++,
                        "empname" => $row->lastname.', '.$row->firstname,
                        "tax" => $row->totaltax
                    );
                }else if($payclass != 128){
                    if($print > 0){
                        $html .= '<tr>';
                        $html .= '<td>'.$confidentialnum.'</td>';
                        $html .= '<td>'.$row->lastname.', '.$row->firstname.'</td>';
                        $html .= '<td>'.number_format($row->totaltax , 2).'</td>';
                        $html .= '</tr>';
                    }
                    $data['confidentialtaxdata'][]=  array(
                        "id" => $confidentialnum++,
                        "empname" => $row->lastname.', '.$row->firstname,
                        "tax" => $row->totaltax
                    );
                }


            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        if($print > 0){
            $data['print'] = true;
            $data['html'] = $html;
            if($print == 128){
                $data['title']  = "RANK AND FILE MONTHLY TAX REPORT AS OF ".$month.'/'.$year;
            }else{
                $data['title']  = "CONFIDENTIAL & SUPERVISOR MONTHLY TAX REPORT AS OF ".$month.'/'.$year;
            }
        }else{
            $data['print'] = false;
            $data['html'] = '';
        }

        echo json_encode($data);
    }
    function getexistingvalue(){
        $data = array();

        $trntype = $this->input->post('trntype');
        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $paytype = $this->input->post('paytype');

        if($paytype > 0){
            $this->db->where(array("paytype" => $paytype));
        }

        $checkforexistingvalue = $this->db->select("amt")->from("payroll_transactions")
            ->where(array("typesid" => $trntype,"empid" => $empid ,"years" => $year,"months" => $month,"status" => 1))
            ->get()->row();
        if($checkforexistingvalue){
            $data['amt'] = $checkforexistingvalue->amt;
        }else{
            $checkforfixamt = $this->db->select("amt")->from("payroll_fix_amt")
                ->where(array("status" => 1 , "empid" => $empid , "typesid" => $trntype))->get()->row();
            if($checkforfixamt){
                $data['amt'] = $checkforfixamt->amt;
            }
        }

        echo json_encode($data);
    }
    function fetchconfidential(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $paytype = $this->input->post('paytype');
        $approve = false;
        $allnot_confi_sa_array = array(128,3077,3078);
        $jobcatarr = array(157,160);
        $sql = $this->db->select("pem.sysid,UPPER(p.lastname) as lastname,UPPER(p.firstname) as firstname,p.middlename,pemp.payclass_id")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem","pem.sysid = pe.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_employee_main_payclass as pemp","pemp.emp_id = pe.empid","left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pe.empid")
            ->where(array("pe.status" => 1, "pem.status" => 1))
            ->where_in('pemjc.jobcatid' , $jobcatarr)
            ->where_not_in('pe.payclass',$allnot_confi_sa_array)
            ->order_by("p.lastname")
            ->get();


        if($sql->num_rows() > 0){

            $checkifprocess = $this->db->select("sysid")->from("payroll_reports_group")
                ->where(array("years" => $year ,"months" => $month , "payclass" => 1 , "status" => 301 ))
                ->get()->row();

            $num = 1;
            $mainindex = 0;

            $cols_arr = array();
            $employeeCol = '<span style="width:200px;display:inline-block">Employee Name</span>';
            $cols_arr[] = array('data' => 'name', 'text' => $employeeCol, 'sClass' => 'zui-sticky-col');

            $colquery = $this->db->select("ptp.names")->from("payroll_process_owner as ppo")
                ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                ->where(array("ppo.status" => 1 , "pm.manual" => 1,"ppo.payclass" => 129))
                ->group_by("ptp.names")
                ->order_by("ptp.names" , "ASC")
                ->get();
            if($colquery->num_rows() > 0){
                $cd = 0;
                foreach ($colquery->result() as $row3){
                    $th_html = '<span style="width:150px;display:inline-block">'.$row3->names.'</span>';
                    $cols_arr[] = array('data' => $cd++, 'sClass' => '', 'text' => $th_html);
                }
            }
            $empindex = 0;
            foreach ($sql->result() as $row){
                $data['confidentialdata'][] = array(
                    'name' => '<div style="display: inline-block; width: 100%" >'.$row->lastname.', '.$row->firstname.'</div>',
                );


                $trntypes = $this->db->select("ptp.sysid,ptp.names")->from("payroll_process_owner as ppo")
                    ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                    ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                    ->where(array("ppo.status" => 1 , "pm.manual" => 1,"ppo.payclass" => 129))
                    ->group_by("ptp.sysid,ptp.names")
                    ->order_by("ptp.names" , "ASC")
                    ->get();
                if($trntypes->num_rows() > 0){
                    $i = 0;
                    $index = 0;

                    foreach ($trntypes->result() as $row1){


                        if($checkifprocess){
                            $approve = true;
                            $inputs = array(
                                //   $index => '<input  '.$isreadonly.' type="text" id="confidentialinput" value="'.$amount.'" data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs '.$class.'" />'
                                $index => '<input   style="background-color: #00CC66 !important;" disabled  type="text" id="confidentialinput"  data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs"  />'
                            );
                        }else{
                            $checkforexistingloans = $this->db->select("pmtb.amount")->from("payroll_manual_transactions_breakdown as pmtb")
                                ->join("payroll_manual_transactions as pmt","pmt.sysid = pmtb.groupid","left")
                                ->where(array("pmtb.empid" => $row->sysid, "pmtb.month" => $month , "pmtb.year" => $year ,"pmtb.status" => 313
                                ,"pmt.tsysid" => $row1->sysid))
                                ->get()->row();

                            $isreadonly = ($checkforexistingloans) ?  'readonly' : '';
                            $class = ($checkforexistingloans) ?  'text-danger' : '';
                            if($checkforexistingloans == false){
                                $checkforencodeddata = $this->db->select("typesid,amt")->from("payroll_transactions")
                                    ->where(array("status" => 1,"empid" => $row->sysid,"typesid" => $row1->sysid,
                                        "months" => $month,"years" => $year))->get()->row();
                                $amount = ($checkforencodeddata) ? number_format($checkforencodeddata->amt , 2) : '';

                            }else{
                                $amount = number_format($checkforexistingloans->amount , 2);
                            }
                            $inputs = array(
                                //   $index => '<input  '.$isreadonly.' type="text" id="confidentialinput" value="'.$amount.'" data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs '.$class.'" />'
                                $index => '<input  '.$isreadonly.' type="text" id="confidentialinput" value="'.$amount.'"  data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs '.$class.'" />'
                            );
                        }



                        array_push($data['confidentialdata'][$empindex],$inputs[$i]);
                        $i++;
                        $index++;
                    }

                }
                $empindex++;
            }
        }
        $data['columns'] = $cols_arr;
        $data['approve'] = $approve;

        //  $data['rankandfilecolumns'][] = $arrcolums;
        echo json_encode($data);
    }
    function fetchrankandfile(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $typehalf = $this->input->post('typehalf');
        $approve = false;


        $jobcatarr = array(157 , 160);
        $sql = $this->db->select("pem.sysid,UPPER(p.lastname) as lastname,UPPER(p.firstname) as firstname,p.middlename,pemp.payclass_id")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem","pem.sysid = pe.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_employee_main_payclass as pemp","pemp.emp_id = pe.empid","left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pe.empid")
            ->where(array("pe.status" => 1,"pe.payclass" => 128 , "pem.status" => 1))
            ->where_in('pemjc.jobcatid' , $jobcatarr)
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $checkifprocess = $this->db->select("sysid")->from("payroll_reports_group")
                ->where(array("years" => $year ,"months" => $month , "payclass" => 128 , "status" => 301 , "paytype" => $typehalf ))
                ->get()->row();

            $cols_arr = array();
            $employeeCol = '<span style="width:200px;display:inline-block">Employee Name</span>';
            $cols_arr[] = array('data' => 'name', 'text' => $employeeCol, 'sClass' => 'zui-sticky-col');

            $colquery = $this->db->select("ptp.names")->from("payroll_process_owner as ppo")
                ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                ->where(array("ppo.status" => 1 , "pm.manual" => 1,"ppo.payclass" => 128))
                ->group_by("ptp.names")
                ->order_by("ptp.names" , "ASC")
                ->get();
            if($colquery->num_rows() > 0){
                $cd = 0;
                foreach ($colquery->result() as $row3){
                    $th_html = '<span style="width:150px;display:inline-block">'.$row3->names.'</span>';
                    $cols_arr[] = array('data' => $cd++, 'sClass' => '', 'text' => $th_html);
                }
            }
            $num = 1;
            $mainindex = 0;
            $empindex = 0;
            foreach ($sql->result() as $row){
                $data['rankandfiledata'][] = array(
                    'name' => '<div style="display: inline-block; width: 100%" >'.$row->lastname.', '.$row->firstname.'</div>',
                );
                /* $data['rankandfiledata'][] = array(
                     'name' => '<div style="display: inline-block; width: 100%" >'.ucwords(strtolower($row->lastname)).', '.ucwords(strtolower($row->firstname)).'</div>',
                 ); */


                $trntypes = $this->db->select("ptp.sysid,ptp.names")->from("payroll_process_owner as ppo")
                    ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                    ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                    ->where(array("ppo.status" => 1 , "pm.manual" => 1,"ppo.payclass" => 128))
                    ->group_by("ptp.sysid,ptp.names")
                    ->order_by("ptp.names" , "ASC")
                    ->get();
                $data['error1'] = $this->db->_error_message();
                if($trntypes->num_rows() > 0){
                    $i = 0;
                    $index = 0;
                    foreach ($trntypes->result() as $row1){

                        if($checkifprocess){
                            $approve = true;
                            $inputs = array(
                                $index => '<input style="background-color: #00CC66 !important;" disabled  type="text" id="ranknfileinput"  data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs" />'
                            );
                        }else{
                            $checkforexistingloans = $this->db->select("pmtb.amount")->from("payroll_manual_transactions_breakdown as pmtb")
                                ->join("payroll_manual_transactions as pmt","pmt.sysid = pmtb.groupid","left")
                                ->where(array("pmtb.empid" => $row->sysid, "pmtb.month" => $month , "pmtb.year" => $year ,"pmtb.status" => 313,"pmt.tsysid" => $row1->sysid,"pmtb.paytype" => $typehalf))
                                ->get()->row();
                            $data['typehalf'] = $typehalf;
                            $data['month'] = $month;
                            $data['year'] = $year;
                            $isreadonly = ($checkforexistingloans) ?  'readonly' : '';
                            $class = ($checkforexistingloans) ?  'text-danger' : '';
                            if($checkforexistingloans == false){
                                $checkforencodeddata = $this->db->select("amt")->from("payroll_transactions")
                                    ->where(array("status" => 1,"empid" => $row->sysid,"typesid" => $row1->sysid,
                                        "months" => $month,"years" => $year , "payspec" => $typehalf))->get()->row();
                                $amount = ($checkforencodeddata) ? number_format($checkforencodeddata->amt, 2, '.', '') : '';
                            }else{
                                $amount = number_format($checkforexistingloans->amount, 2, '.', '');
                            }
                            $inputs = array(
                                $index => '<input '.$isreadonly.' type="text" id="ranknfileinput" value="'.$amount.'" data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs '.$class.'" />'
                            );
                        }


                        array_push($data['rankandfiledata'][$empindex],$inputs[$i]);
                        $i++;
                        $index++;
                    }
                }
                $empindex++;

            }
        }

        $data['columns'] = $cols_arr;
        $data['approve'] = $approve;
        echo json_encode($data);
    }
    function getconfidentialmonth(){
        $data = array();
        for ($i = 1; $i <= 12; $i++) {
            $dt = DateTime::createFromFormat('!m', $i);
            $mname = $dt->format('F');
            $mcode = $dt->format('M');
            $data['list'][] = array(
                'id' => $i,
                'text' => strtoupper($mcode) . ' - ' . $mname
            );
        }
        echo json_encode($data);
    }
    function getranknfilemonth(){
        $data = array();
        for ($i = 1; $i <= 12; $i++) {
            $dt = DateTime::createFromFormat('!m', $i);
            $mname = $dt->format('F');
            $mcode = $dt->format('M');
            $data['list'][] = array(
                'id' => $i,
                'text' => strtoupper($mcode) . ' - ' . $mname
            );
        }
        echo json_encode($data);
    }
    function getconfidentialyear(){
        $data = array();

        for($year = 2018;$year <= 2050;$year++){
            $data['list'][] = array(
                'id' => $year,
                'text' => ''.$year
            );
        }

        echo json_encode($data);
    }
    function getranknfileyear(){
        $data = array();

        for($year = 2018;$year <= 2050;$year++){
            $data['list'][] = array(
                'id' => $year,
                'text' => ''.$year
            );
        }

        echo json_encode($data);
    }
    function applyholiday(){
        $data = array();
        $noofdays = $this->input->post('noofdays');
        $payclass = $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $half = $this->input->post('half');
        $insertholiday = 0;
        $allowedpayclass = array(3077 , 3078 , 128 , 129);
        if($payclass == 129){
            $paytype = 1;
            $payspec = 1;
            $this->db->where_in('pe.payclass',array(129,130,3073));
        }else{
            $paytype = $half;
            $payspec = $half;
            $this->db->where('pe.payclass',$payclass);
        }
        $this->db->trans_begin();
        $sql = $this->db->select("pe.empid,pes.amt,pes.salary_type,pe.payclass")
            ->from("payroll_emplist as pe")
            ->join("prime_employee_salary as pes","pes.empid = pe.empid","left")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid" , "left")
            ->where(array("pe.status" => 1 , "pes.status" => 1 , "pem.status" => 1))
            ->get();
        $data['lastqry'] = $this->db->last_query();
        $data['filteremperror'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){

                $emp_position_id = isset(select_emp_position($row->empid)->sysid) ? select_emp_position($row->empid)->sysid : 0;
                $divider = 249;
                $onedayoffposition = array(173 , 174 , 164);

                /* ------------------------------------------------------------
                | CHECK IF SELECTED POSITION IN ARRAY THEN DIVIDER IS 301 AND
                | PAYCLASS IS TIERED
                |______________________________________________________________
                */

                if (in_array($emp_position_id, $onedayoffposition) && $row->payclass == 3077) {
                    $divider = 301;
                }

                $checkforexist = $this->db->select("sysid")->from("payroll_transactions")
                    ->where(array("empid" =>$row->empid,
                        "typesid" => 263 ,
                        "months" => $month,
                        "years" => $year,
                        "paytype" => $paytype,
                        "payspec" => $payspec,
                        "payclass" => $payclass,
                        "status" => 1))
                    ->get()->row();
                if($checkforexist){
                    $updatearr = array(
                        'status' => 0,
                        'updatedby' => user_id()
                    );
                    $this->db->where(array("sysid" => $checkforexist->sysid,"status" => 1));
                    $this->db->update("payroll_transactions" ,$updatearr);
                }

                $val = $noofdays * (($row->amt * 12) / $divider);
                $insarr = array(
                    'empid' => $row->empid,
                    'typesid' => 263,
                    'months' => $month,
                    'years' => $year,
                    'paytype' => $paytype,
                    'payspec' => $payspec,
                    'amt' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 1,
                    'payclass' => $payclass,
                    'insertamount' => $noofdays
                );
                $insert_holiday = $this->db->insert("payroll_transactions" , $insarr);
                if($insert_holiday) {
                    $insertholiday += 1;
                }
                $data['insertholidayerror'] = $this->db->_error_message();
            }
        }
        if($this->db->trans_status() == true && $sql->num_rows() > 0 && $insertholiday > 0){
            $this->db->trans_commit();
            $msg = 'Holiday has been applied.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Applying failed.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['nodays'] = $noofdays;
        echo json_encode($data);
    }


    function getpayrollemployee(){
        $data = array();
        $sql = $this->db->select("pe.empid,p.lastname,p.firstname")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem","pem.sysid = pe.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->where(array("pe.status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->empid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }
        echo json_encode($data);
    }
    function gettransactiondetails(){
        $data = array();

        $employeesearch = $this->input->post('employeesearch');
        $monthsearch = $this->input->post('monthsearch');
        $yearsearch = $this->input->post('yearsearch');
        $typesearch = $this->input->post('typesearch');
        if($typesearch > 0){
            $this->db->where(array("paytype" => $typesearch));
        }

        $checkforloansmanual = $this->db->select("ptp.names,pmtb.amount")->from("payroll_manual_transactions_breakdown as pmtb")
            ->join("payroll_manual_transactions as pmb","pmb.sysid = pmtb.groupid","left")
            ->join("prime_types_parameter as ptp","ptp.sysid = pmb.tsysid","left")
            ->where(array("pmtb.month" => $monthsearch,"pmtb.year" => $yearsearch,"pmtb.empid"=>$employeesearch))->get();
        if($checkforloansmanual->num_rows() > 0){
            $num = 1;
            foreach ($checkforloansmanual->result() as $row){
                $data['breakdownloan'][] = array(
                    "num" => $num++,
                    "type" => $row->names,
                    "amount" => $row->amount
                );
            }
        }
        if($typesearch > 0){
            $this->db->where(array("paytype" => $typesearch));
        }
        $checkforpayrolltransaction = $this->db->select("ptp.names , pe.amt")->from("payroll_transactions as pe")
            ->join("prime_types_parameter as ptp","ptp.sysid = pe.typesid","left")
            ->where(array("pe.empid" => $employeesearch,"pe.months" => $monthsearch,"pe.years" => $yearsearch))->get();
        if($checkforpayrolltransaction->num_rows() > 0){
            $num = 1;
            foreach ($checkforpayrolltransaction->result() as $row){
                $data['payrolltransactiondata'][] = array(
                    "num" => $num++,
                    "type" => $row->names,
                    "amount" => $row->amt
                );
            }
        }
        echo json_encode($data);
    }
    function updatecontribrates(){
        $data = array();
        $dataid =  $this->input->post('dataid');
        $base =  $this->input->post('base');
        $min =  $this->input->post('min');
        $max =  $this->input->post('max');
        $amoumt =  $this->input->post('amoumt');
        $rateemployee =  $this->input->post('rateemployee');
        $rateemployer =  $this->input->post('rateemployer');

        $this->db->trans_begin();
        $updatearr = array(
            'amtbase' => $base,
            'amtmin' => $min,
            'amtmax' => $max,
            'amtcont' => $amoumt,
            'rateemployer' => $rateemployer,
            'rateemployee' => $rateemployee
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("prime_contribution_matrix" , $updatearr);
        $data['errormessage'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Contribution rates has been updated.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to update rates.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function gethdmfloan(){
        $data = array();
        $payclass= $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $print = $this->input->post('print');
        $html = '';
        $html .= '<table class="table table-bordered table-responsive table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Name</th>';
        $html .= '<th>HDMF LOAN</th>';
        $html .= '</thead>';
        $html .= '<tbody>';


        if($payclass != 128){
            $this->db->where(array("pe.payclass !=" => 128));
        }else{
            $this->db->where(array("pe.payclass" => 128));
        }

        $sql = $this->db->select("p.lastname,p.firstname , prt.amt")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid","left")
            ->join("payroll_reports_main as prm" , "prm.empid = pe.empid" , "left")
            ->join("payroll_reports_group as prg" , "prg.sysid = prm.groupid" , "left")
            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
            ->where(array("pe.status" => 1 , "prt.trntype" => 258 , "prg.status" => 301 , "years" => $year , "months" => $month))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $rankandfilenum = 1;
            $confidentialnum = 1;
            foreach ($sql->result() as $row){
                if($payclass != 128){
                    $html .= '<tr>';
                    $html .= '<td>'.$confidentialnum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.$row->amt.'</td>';
                    $html .= '</tr>';
                    $data['confidentialhdmfloan'][] =  array(
                        'num' => $confidentialnum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => $row->amt,
                    );

                }else{
                    $html .= '<tr>';
                    $html .= '<td>'.$rankandfilenum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.$row->amt.'</td>';
                    $html .= '</tr>';
                    $data['rankandfilehdmfloan'][] =  array(
                        'num' => $rankandfilenum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $data['payclass'] = $payclass;
        $data['html'] = $html;
        $data['print'] = $print;

        echo json_encode($data);
    }
    function getsssloan(){
        $data = array();
        $payclass= $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $print = $this->input->post('print');
        $html = '';
        $html .= '<table class="table table-bordered table-responsive table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Name</th>';
        $html .= '<th>SSS LOAN</th>';
        $html .= '</thead>';
        $html .= '<tbody>';


        if($payclass != 128){
            $this->db->where(array("pe.payclass !=" => 128));
        }else{
            $this->db->where(array("pe.payclass" => 128));
        }

        $sql = $this->db->select("p.lastname,p.firstname , prt.amt")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid","left")
            ->join("payroll_reports_main as prm" , "prm.empid = pe.empid" , "left")
            ->join("payroll_reports_group as prg" , "prg.sysid = prm.groupid" , "left")
            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
            ->where(array("pe.status" => 1 , "prt.trntype" => 257 , "prg.status" => 301 , "years" => $year , "months" => $month))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $rankandfilenum = 1;
            $confidentialnum = 1;
            foreach ($sql->result() as $row){
                if($payclass != 128){
                    $html .= '<tr>';
                    $html .= '<td>'.$confidentialnum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.$row->amt.'</td>';
                    $html .= '</tr>';
                    $data['confidentialsssloan'][] =  array(
                        'num' => $confidentialnum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }else{
                    $html .= '<tr>';
                    $html .= '<td>'.$rankandfilenum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.$row->amt.'</td>';
                    $html .= '</tr>';
                    $data['rankandfilesssloan'][] =  array(
                        'num' => $rankandfilenum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $data['payclass'] = $payclass;
        $data['html'] = $html;
        $data['print'] = $print;

        echo json_encode($data);
    }

    function getssscont(){
        $data = array();
        $payclass= $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $print = $this->input->post('print');
        $html = '';
        $html .= '<table class="table table-bordered table-responsive table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Name</th>';
        $html .= '<th>SSS Cont</th>';
        $html .= '</thead>';
        $html .= '<tbody>';


        if($payclass != 128){
            $this->db->where(array("pe.payclass !=" => 128));
        }else{
            $this->db->where(array("pe.payclass" => 128));
        }

        $sql = $this->db->select("p.lastname,p.firstname , prt.amt")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid","left")
            ->join("payroll_reports_main as prm" , "prm.empid = pe.empid" , "left")
            ->join("payroll_reports_group as prg" , "prg.sysid = prm.groupid" , "left")
            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
            ->where(array("pe.status" => 1 , "prt.trntype" => 72 , "prg.status" => 301 , "years" => $year , "months" => $month))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $rankandfilenum = 1;
            $confidentialnum = 1;
            foreach ($sql->result() as $row){
                if($payclass != 128){
                    $html .= '<tr>';
                    $html .= '<td>'.$confidentialnum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.number_format($row->amt , 2).'</td>';
                    $html .= '</tr>';
                    $data['confidentialssscont'][] =  array(
                        'num' => $confidentialnum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }else{
                    $html .= '<tr>';
                    $html .= '<td>'.$rankandfilenum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.$row->amt.'</td>';
                    $html .= '</tr>';
                    $data['rankandfilessscont'][] =  array(
                        'num' => $rankandfilenum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $data['payclass'] = $payclass;
        $data['html'] = $html;
        $data['print'] = $print;

        echo json_encode($data);
    }
    function getpecewa(){
        $data = array();
        $payclass= $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $print = $this->input->post('print');
        $html = '';
        $html .= '<table class="table table-bordered table-responsive table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Name</th>';
        $html .= '<th>PECEWA</th>';
        $html .= '</thead>';
        $html .= '<tbody>';


        if($payclass != 128){
            $this->db->where(array("pe.payclass !=" => 128));
        }else{
            $this->db->where(array("pe.payclass" => 128));
        }

        $sql = $this->db->select("p.lastname,p.firstname , prt.amt")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid","left")
            ->join("payroll_reports_main as prm" , "prm.empid = pe.empid" , "left")
            ->join("payroll_reports_group as prg" , "prg.sysid = prm.groupid" , "left")
            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
            ->where(array("pe.status" => 1 , "prt.trntype" => 254 , "prg.status" => 301 , "years" => $year , "months" => $month))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $rankandfilenum = 1;
            $confidentialnum = 1;
            foreach ($sql->result() as $row){
                if($payclass != 128){
                    $html .= '<tr>';
                    $html .= '<td>'.$confidentialnum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.number_format($row->amt , 2).'</td>';
                    $html .= '</tr>';
                    $data['confidentialssscont'][] =  array(
                        'num' => $confidentialnum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }else{
                    $html .= '<tr>';
                    $html .= '<td>'.$rankandfilenum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.$row->amt.'</td>';
                    $html .= '</tr>';
                    $data['rankandfilessscont'][] =  array(
                        'num' => $rankandfilenum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $data['payclass'] = $payclass;
        $data['html'] = $html;
        $data['print'] = $print;

        echo json_encode($data);
    }
    function getcoop(){
        $data = array();
        $payclass= $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $print = $this->input->post('print');
        $html = '';
        $html .= '<table class="table table-bordered table-responsive table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Name</th>';
        $html .= '<th>COOP</th>';
        $html .= '</thead>';
        $html .= '<tbody>';


        if($payclass != 128){
            $this->db->where(array("pe.payclass !=" => 128));
        }else{
            $this->db->where(array("pe.payclass" => 128));
        }

        $sql = $this->db->select("p.lastname,p.firstname , prt.amt")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid","left")
            ->join("payroll_reports_main as prm" , "prm.empid = pe.empid" , "left")
            ->join("payroll_reports_group as prg" , "prg.sysid = prm.groupid" , "left")
            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid" , "left")
            ->where(array("pe.status" => 1 , "prt.trntype" => 255 , "prg.status" => 301 , "years" => $year , "months" => $month))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $rankandfilenum = 1;
            $confidentialnum = 1;
            foreach ($sql->result() as $row){
                if($payclass != 128){
                    $html .= '<tr>';
                    $html .= '<td>'.$confidentialnum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.number_format($row->amt , 2).'</td>';
                    $html .= '</tr>';
                    $data['confidentialcoop'][] =  array(
                        'num' => $confidentialnum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }else{
                    $html .= '<tr>';
                    $html .= '<td>'.$rankandfilenum.'</td>';
                    $html .= '<td>'. $row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td>'.$row->amt.'</td>';
                    $html .= '</tr>';
                    $data['rankandfilecoop'][] =  array(
                        'num' => $rankandfilenum++,
                        'name' => $row->lastname.', '.$row->firstname,
                        'amount' => number_format($row->amt , 2),
                    );

                }
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $data['payclass'] = $payclass;
        $data['html'] = $html;
        $data['print'] = $print;

        echo json_encode($data);
    }
    function submitannualtax(){
        $data = array();
        $employees = $this->input->post('employees');
        $amount = $this->input->post('amount');
        $typehalf = $this->input->post('typehalf');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $this->db->trans_begin();



        if (is_array($employees)) {
            foreach($employees as $value){
                $explodearr =   explode(',', $value);
                foreach ($explodearr as $emplist){

                    $payclass = $this->db->select("payclass")->from("payroll_emplist")
                        ->where(array("status" => 1 , "empid" => $emplist))->get()->row();

                    $insarr = array(
                        'empid' => $emplist,
                        'payclass' => ($payclass) ? $payclass->payclass : '',
                        'month' => $month,
                        'year' => $year,
                        'paytype' => $typehalf,
                        'amount' => $amount
                    );
                    $sql = $this->db->insert("payroll_anual_tax_distribution" , $insarr);
                    $data['error'] = $this->db->_error_message();

                }

            }
        } else {
            $sql = false;
        }
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Annual tax has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save annual tax.';
            $func = '';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function getpayrollemployees(){
        $data = array();
        $term = $this->input->post('term');
        $sql = $this->db->select("pe.empid , p.lastname,p.firstname")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid" , "left")
            ->join("person as p","p.sysid = pem.personid" , "left")
            ->where(array("pe.status" => 1))
            ->or_like('p.lastname', $term)
            ->or_like('p.firstname', $term)
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->empid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }

        echo json_encode($data);
    }
    function getfixtypesid(){
        $data = array();

        $sql = $this->db->select("pm.typesid ,ptp.codes, ptp.names")->from("payroll_matrix as pm")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pm.typesid" , "left")
            ->where(array("pm.fix" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->typesid,
                    'text' => $row->names.' - '.$row->codes
                );
            }
        }

        echo json_encode($data);
    }
    function addpayrollfixamt(){
        $data = array();

        $empid = $this->input->post('empid');
        $amt = $this->input->post('amt');
        $types = $this->input->post('types');

        $this->db->trans_begin();

        $insarr = array(
            'empid' => $empid,
            'typesid' => $types,
            'amt' => $amt,
            'status' => 1,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("payroll_fix_amt" , $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Amount has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save amount.';
            $func = '';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['empid'] = $empid;

        echo json_encode($data);
    }
    function getemppayrollfixamt(){
        $data = array();

        $empid = $this->input->post('empid');

        $sql = $this->db->select("pfa.sysid , pfa.empid , pfa.amt , pfa.datecreated , ptp.names")->from("payroll_fix_amt as pfa")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pfa.typesid" , "left")
            ->where(array("pfa.status" => 1 , "pfa.empid" => $empid))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['payrollfixamtdata'][] = array(
                    "num" => $num++,
                    "types" => $row->names,
                    "amt" => $row->amt,
                    "datecreated" => $row->datecreated,
                    "control" => '<button data-id="'.$row->sysid.'" id="deletefixamt" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>'
                );
            }
        }

        echo json_encode($data);
    }
    function savepayrollentry(){
        $data = array();

        $typehalf = $this->input->post('typehalf');

        $this->db->trans_begin();

        $insarr = array(
            'paytype' => $typehalf,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $this->db->insert("payroll_transactions_main" , $insarr);
        $lastid =  $this->db->insert_id();

        $updatearr = array(
            'groupid' => $lastid
        );
        $this->db->where(array("groupid" => null));
        $updategroupid = $this->db->update("payroll_transactions" , $updatearr);
        if($this->db->trans_status() == true && $updategroupid){
            $this->db->trans_commit();
            $msg = 'Payroll has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save payroll.';
            $func = '';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function getempdeptovertime(){
        $id = $this->input->post('id');

        $inputs = $this->input->post('inputs');
        $dataid = $inputs['dataid'];

        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if(in_array($payrollpayclass,array(128,3077,3078))){
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass , "status !=" => 302))
            ->get()->row();

        $groupid = ($sql) ?  $sql->sysid : $dataid;
        $ndothrs = 0;
        $ndotpay = 0;
        $otwithholiday =0;
        $otweekend = 0;
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

        $html = '';
        $html .= '<table class="table table-bordered table-condensed table-hover tbl-xs">';
        $html .= '<thead>';
        $html .= '<th>Name</th>';
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

        $sql = $this->db->select("prg.years , prg.months , prg.payclass , prg.paytype , prm.empid , prm.ccid , p.lastname , p.firstname")->from("payroll_reports_group as prg")
            ->join("payroll_reports_main as prm" , "prm.groupid = prg.sysid" , "left")
            ->join("prime_employee_main as pem" , "pem.sysid = prm.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->where(array("prg.sysid" => $groupid,"prg.status" => 1 , "prm.groupid"=> $groupid , "prm.ccid" => $id))
            ->get();

        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                if($row->payclass == 128){
                    $this->db->where(array("paytype" => $row->paytype));
                }
                $getothrs = $this->db->select("insertamount , amt")->from("payroll_transactions")
                    ->where(array("typesid" => 358 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1))
                    ->get()->row();
                $ndothrs = ($getothrs) ? $getothrs->insertamount : 0;
                $ndotpay =  ($getothrs) ? $getothrs->amt : 0;


                $getotwithholiday = $this->db->select("insertamount , amt")->from("payroll_transactions")
                    ->where(array("typesid" => 3010 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1))
                    ->get()->row();
                $otwithholiday =  ($getotwithholiday) ? $getotwithholiday->insertamount : 0;
                $one60 = ($getotwithholiday) ? $getotwithholiday->amt : 0;
                $getweekends = $this->db->select("insertamount , amt")->from("payroll_transactions")
                    ->where(array("typesid" => 1082 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1))
                    ->get()->row();
                $otweekend =  ($getweekends) ? $getweekends->insertamount : 0;
                $one30 = ($getweekends) ? $getweekends->amt : 0;
                $getweekday = $this->db->select("insertamount , amt")->from("payroll_transactions")
                    ->where(array("typesid" => 359 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1))
                    ->get()->row();
                $otweekdays =  ($getweekday) ? $getweekday->insertamount : 0;
                $one25  = ($getweekday) ? $getweekday->amt : 0;
                $othrs = ($otwithholiday + $otweekend + $otweekdays);

                $html .= '<tr>';
                $html .= '<td>'.$row->lastname.', '.$row->firstname.'</td>';
                $html .= '<td>'.$ndothrs.'</td>';
                $html .= '<td>'.$ndotpay.'</td>';
                $html .= '<td>'.$othrs.'</td>';
                $html .= '<td>'.$one25.'</td>';
                $html .= '<td>'.$one30.'</td>';
                $html .= '<td>'.$one50.'</td>';
                $html .= '<td>'.$one60.'</td>';
                $html .= '<td>'.$one80.'</td>';
                $html .= '<td>'.$two10.'</td>';
                $html .= '<td>'.$two30.'</td>';
                $html .= '<td>'.$two60.'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $data['html'] = $html;
        echo json_encode($data);
    }

    function printotregisterbyemp(){
        $inputarr = $this->input->post('inputs');


        $dataid  = $this->input->post('dataid');
        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');


        if(in_array($payrollpayclass,array(128,3077,3078))){
            $this->db->where(array("paytype" => $payrollpaytype));
        }

        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass , "status != " => 302))
            ->get()->row();

        $groupid = ($sql) ?  $sql->sysid : $dataid;

        $html = '';

        $one50 = 0;
        $one80 = 0;
        $two10 = 0;
        $two30 = 0;
        $two60 = 0;

        $ndothrssub = 0;
        $ndotpaysub = 0;
        $othrssub = 0;
        $one25sub = 0;
        $one30sub = 0;
        $one50sub = 0;
        $one60sub = 0;
        $one80sub = 0;
        $two10sub = 0;
        $two30sub = 0;
        $two60sub = 0;

        $ndothrsfinal = 0;
        $ndotpayfinal = 0;
        $othrsfinal = 0;
        $one25final = 0;
        $one30final = 0;
        $one50final = 0;
        $one60final = 0;
        $one80final = 0;
        $two10final = 0;
        $two30final = 0;
        $two60final = 0;

        $getpayclass =$this->db->select("payclass")->from("payroll_reports_group")->where(array("sysid" => $groupid))->get()->row();
        $payclass = ($getpayclass) ? $getpayclass->payclass : 0;
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
        $html .= '<th>Name</th>';
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


        $loopccid = $this->db->select("ccid")->from("payroll_reports_main")
            ->where(array("groupid" => $groupid))
            ->group_by("ccid")
            ->order_by("ccid" , "asc")
            ->get();
        if($loopccid->num_rows() > 0){
            foreach ($loopccid->result() as $ccrow){

                $sql = $this->db->select("prg.years , prg.months , prg.payclass , prg.paytype , prm.empid , prm.ccid , p.lastname , p.firstname")->from("payroll_reports_group as prg")
                    ->join("payroll_reports_main as prm" , "prm.groupid = prg.sysid" , "left")
                    ->join("prime_employee_main as pem" , "pem.sysid = prm.empid" , "left")
                    ->join("person as p" , "p.sysid = pem.personid" , "left")
                    ->where(array("prg.sysid" => $groupid , "prm.groupid"=> $groupid , "prm.ccid" => $ccrow->ccid))
                    ->get();

                if($sql->num_rows() > 0){
                    foreach ($sql->result() as $row){
                        if($row->payclass == 128){
                            $this->db->where(array("paytype" => $row->paytype));
                        }
                        $getothrs = $this->db->select("insertamount , amt")->from("payroll_transactions")
                            ->where(array("typesid" => 358 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1, 'paytype' => $payrollpaytype))
                            ->get()->row();
                        $ndothrs = ($getothrs) ? $getothrs->insertamount : 0;
                        $ndotpay =  ($getothrs) ? $getothrs->amt : 0;



                        if($row->payclass == 128){
                            $this->db->where(array("paytype" => $row->paytype));
                        }
                        $getotwithholiday = $this->db->select("insertamount , amt")->from("payroll_transactions")
                            ->where(array("typesid" => 3010 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1, 'paytype' => $payrollpaytype))
                            ->get()->row();
                        $otwithholiday =  ($getotwithholiday) ? $getotwithholiday->insertamount : 0;
                        $one60 = ($getotwithholiday) ? $getotwithholiday->amt : 0;

                        if($row->payclass == 128){
                            $this->db->where(array("paytype" => $row->paytype));
                        }
                        $getweekends = $this->db->select("insertamount , amt")->from("payroll_transactions")
                            ->where(array("typesid" => 1082 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1, 'paytype' => $payrollpaytype))
                            ->get()->row();
                        $otweekend =  ($getweekends) ? $getweekends->insertamount : 0;
                        $one30 = ($getweekends) ? $getweekends->amt : 0;

                        if($row->payclass == 128){
                            $this->db->where(array("paytype" => $row->paytype));
                        }
                        $getweekday = $this->db->select("insertamount , amt")->from("payroll_transactions")
                            ->where(array("typesid" => 359 , "empid" => $row->empid , "months" => $row->months , "years" => $row->years , "status" => 1, 'paytype' => $payrollpaytype))
                            ->get()->row();
                        $otweekdays =  ($getweekday) ? $getweekday->insertamount : 0;
                        $one25  = ($getweekday) ? $getweekday->amt : 0;
                        $othrs = ($otwithholiday + $otweekend + $otweekdays);

                        $html .= '<tr>';
                        $html .= '<td>'.ucwords(strtolower($row->lastname)).', '.ucwords(strtolower($row->firstname)).'</td>';
                        $html .= '<td>'.$ndothrs.'</td>';
                        $html .= '<td>'.$ndotpay.'</td>';
                        $html .= '<td>'.$othrs.'</td>';
                        $html .= '<td>'.number_format($one25 , 2).'</td>';
                        $html .= '<td>'.number_format($one30, 2).'</td>';
                        $html .= '<td>'.number_format($one50, 2).'</td>';
                        $html .= '<td>'.number_format($one60, 2).'</td>';
                        $html .= '<td>'.number_format($one80, 2).'</td>';
                        $html .= '<td>'.number_format($two10, 2).'</td>';
                        $html .= '<td>'.number_format($two30, 2).'</td>';
                        $html .= '<td>'.number_format($two60, 2).'</td>';
                        $html .= '</tr>';


                        $ndothrssub += $ndothrs;
                        $ndotpaysub += $ndotpay;
                        $othrssub += $othrs;
                        $one25sub += $one25;
                        $one30sub += $one30;
                        $one50sub += $one50;
                        $one60sub += $one60;
                        $one80sub += $one80;
                        $two10sub += $two10;
                        $two30sub += $two30;
                        $two60sub += $two60;
                    }
                }
                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td class="bold">'.$ndothrssub.'</td>';
                $html .= '<td class="bold">'.$ndotpaysub.'</td>';
                $html .= '<td class="bold">'.$othrssub.'</td>';
                $html .= '<td class="bold">'.number_format($one25sub,2).'</td>';
                $html .= '<td class="bold">'.number_format($one30sub,2).'</td>';
                $html .= '<td class="bold">'.number_format($one50sub,2).'</td>';
                $html .= '<td class="bold">'.number_format($one60sub,2).'</td>';
                $html .= '<td class="bold">'.number_format($one80sub,2).'</td>';
                $html .= '<td class="bold">'.number_format($two10sub,2).'</td>';
                $html .= '<td class="bold">'.number_format($two30sub,2).'</td>';
                $html .= '<td class="bold">'.number_format($two60sub,2).'</td>';
                $html .= '</tr>';


                $ndothrsfinal += $ndothrssub;
                $ndotpayfinal += $ndotpaysub;
                $othrsfinal += $othrssub;
                $one25final += $one25sub;
                $one30final += $one30sub;
                $one50final += $one50sub;
                $one60final += $one60sub;
                $one80final += $one80sub;
                $two10final += $two10sub;
                $two30final += $two30sub;
                $two60final += $two60sub;

                $ndothrssub = 0;
                $ndotpaysub = 0;
                $othrssub = 0;
                $one25sub = 0;
                $one30sub = 0;
                $one50sub = 0;
                $one60sub = 0;
                $one80sub = 0;
                $two10sub = 0;
                $two30sub = 0;
                $two60sub = 0;
            }
        }


        $html .= '<tr>';
        $html .= '<td class="bold">Total</td>';
        $html .= '<td class="bold">'.$ndothrsfinal.'</td>';
        $html .= '<td class="bold">'.$ndotpayfinal.'</td>';
        $html .= '<td class="bold">'.$othrsfinal.'</td>';
        $html .= '<td class="bold">'.number_format($one25final,2).'</td>';
        $html .= '<td class="bold">'.number_format($one30final,2).'</td>';
        $html .= '<td class="bold">'.number_format($one50final,2).'</td>';
        $html .= '<td class="bold">'.number_format($one60final,2).'</td>';
        $html .= '<td class="bold">'.number_format($one80final,2).'</td>';
        $html .= '<td class="bold">'.number_format($two10final,2).'</td>';
        $html .= '<td class="bold">'.number_format($two30final,2).'</td>';
        $html .= '<td class="bold">'.number_format($two60final,2).'</td>';
        $html .= '</tr>';


        $html .= '</tbody>';
        $html .= '</table>';

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
        echo json_encode($data);
    }
    function fetchencoded(){
        $data = array();
        $monthencoded = $this->input->post('monthencoded');
        $yearencoded = $this->input->post('yearencoded');
        $payclassencoded = $this->input->post('payclassencoded');
        $paytypeencoded = $this->input->post('paytypeencoded');
        if($payclassencoded != 129 || $payclassencoded != 1){
            $this->db->where(array("pt.paytype" => $paytypeencoded));
            $this->db->where(array("pemp.payclass_id" => $payclassencoded));
        }else{
            $this->db->where(array("pemp.payclass_id" => 129));
        }


        $sql = $this->db->select("p.lastname , p.firstname , ptp.names , pt.insertamount , pt.amt , psu.firstname AS fcreated , psu.lastname AS lcreated , pm.functions , pm.effects , pt.datecreated")->from("payroll_transactions as pt")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pt.empid" , "left")
            ->join("prime_employee_main as pem" , "pem.sysid = pt.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pt.typesid" , "left")
            ->join("payroll_matrix as pm" , "pm.typesid = pt.typesid" , "left")
            ->join("prime_system_users as psu" , "psu.sysid = pt.createdby" , "left")
            ->where(array("pt.status" => 1  ,"pt.months" => $monthencoded
            ,"pt.years" => $yearencoded))
            ->get();
        if($sql->num_rows()  > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                if($row->functions == 1 && $row->effects == 1){
                    $label = '<span class="label label-sm label-success"> Earnings </span>';
                }else{
                    $label = '<span class="label label-sm label-danger"> Deductions </span>';
                }
                $data['encodeddata'][] = array(
                    'num' => $num++,
                    'name' =>$row->lastname.', '.$row->firstname,
                    'type' => $row->names,
                    'inserted' => $row->insertamount,
                    'computed' => $row->amt,
                    'createdby' => $row->lcreated .', '.$row->fcreated,
                    'datecreated' => $row->datecreated,
                    'stat' =>$label,
                );
            }
        }


        echo json_encode($data);
    }
    function getreports(){
        $data = array();
        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        $button = '';

        if($payrollpayclass != 1){
            $this->db->where(array("paytype" => $payrollpaytype));
        }
        $sql = $this->db->select("sysid, status")
            ->from("payroll_reports_group")
            ->where(array("years" => $payrollyear , "months" => $payrollmonth , "payclass" => $payrollpayclass))
            ->where_in('status', array(1,301))
            ->get()->row();

        $data['query'] = $this->db->last_query();

        if($sql){
            $data['registers']      = $this->model_payroll->get_payroll_register_data($sql->sysid, $payrollyear, $payrollmonth, $payrollpayclass, $payrollpaytype);
            $data['earnings']       = $this->model_payroll->get_earnings_report($sql->sysid, $payrollyear, $payrollmonth, $payrollpayclass, $payrollpaytype);
            $data['deductions']     = $this->model_payroll->get_deductions_report($sql->sysid, $payrollyear, $payrollmonth, $payrollpayclass, $payrollpaytype);
            $data['overtimes']      = $this->model_payroll->get_overtime_report($sql->sysid, $payrollyear, $payrollmonth, $payrollpayclass, $payrollpaytype);

            $data['userid'] = user_id();
            $data['qry'] = true;
            $data['groupid'] = $sql->sysid;
            $data['payclass'] = $payrollpayclass;



            if(in_array(36, get_users_roles_matrix_id_arr()) || super_admin()) {
                $data['access'] = true;
                if (!in_array($sql->status,array(301,302))) {
                    $button .= '<button id="approvebtn" class="btn btn-primary"><i class="fa fa-check"></i> Approve</button>';
                    $button .= '<button id="disapprovebtn" class="btn btn-danger pull-right"><i class="fa fa-times"></i> Disapprove</button>';
                }else{
                    if ($sql->status == 301){
                        $button .= '<h4 class="text-success pull-right"><i class="fa fa-check"></i> Approved</h4>';
                    }
                }
            }else{
                $data['access'] = false;
            }

        }else{
            $data['qry'] = false;
            $data['msg'] = 'No record found!';
        }



        $data['button'] = $button;
        echo json_encode($data);
    }

    function sendpayslips() {
        echo $this->model_payroll->send_payslips();
    }

    function emailpayslip() {
        echo $this->model_payroll->email_payslip();
    }

    function getapprovedreports(){
        $data = array();
        $year = $this->input->post('payrollyear');
        $month = $this->input->post('payrollmonth');
        $payclass = $this->input->post('payrollpayclass');
        $paytype = $this->input->post('payrollpaytype');

        $this->db->trans_begin();
        if($payclass == 128){
            $this->db->where(array("paytype" => $paytype));
        }
        $getdataid = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $year , "months" => $month , "payclass" => $payclass , "status" => 301))
            ->get()->row();
        if($getdataid){
            $dataid = $getdataid->sysid;
            $data['dataid'] = $dataid;
            $getemployees = $this->db->select("sysid , empid , ccid , net ")->from("payroll_reports_main")
                ->where(array("groupid" => $dataid))
                ->order_by("ccid")
                ->get();

            $data['emp_error'] = $this->db->_error_message();
            if($getemployees->num_rows() > 0){
                $totalnet = 0;
                foreach ($getemployees->result() as $row){
                    $data['employee'][] = array(
                        'payrollid' => $row->sysid,
                        'empid' => $row->empid,
                        'ccid' => $row->ccid,
                        'amt' => number_format($row->net / 2, 2, '.', '')
                    );
                    $payrollregisterarr = array(72,257,74,258,254,255,259,261,260,1079,256,3009,262,268);

                    foreach ($payrollregisterarr as $trnid){

                    }

                }
            }



        }else{
            $msg = 'No payroll found.';
            $func = 'info';
            $qry = false;
        }

        echo json_encode($data);
    }
    function getpayrollcontributions(){
        $data = array();
        $sql = $this->db->select("sysid , names , desc")->from("prime_types_parameter")
            ->where(array("codes" => 'EMPCONT'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid ,
                    'text' => $row->names.' - '.$row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function submitcontribution(){
        $data = array();
        $conttype = $this->input->post('conttype');
        $fromrange = $this->input->post('fromrange');
        $torange = $this->input->post('torange');
        $monthlysalcredit = $this->input->post('monthlysalcredit');
        $ercont = $this->input->post('ercont');
        $eecont = $this->input->post('eecont');
        $totalcont = $this->input->post('totalcont');
        $monthcont = $this->input->post('monthcont');
        $yearcont = $this->input->post('yearcont');


        if($torange == 0){
            $end = 1;
        }else{
            $end = 0;
        }

        $this->db->trans_begin();
        $insarr = array(
            'conttype' => $conttype,
            'amtbase' => $monthlysalcredit,
            'amtmin' => $fromrange,
            'amtmax' => $torange,
            'amtcont' => $totalcont,
            'rateemployer' => $ercont / $totalcont,
            'rateemployee' => $eecont / $totalcont,
            'amtcap' => 0,
            'deductible' => 1,
            'var' => 1,
            'types' => 1,
            'end' => $end,
            'spec' => 0,
            'year' => $yearcont,
            'month' => $monthcont,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1,
        );
        $sql = $this->db->insert("prime_contribution_matrix" , $insarr);
        $data['qry_error'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Rate has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save rate.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function deletecontrates(){
        $data = array();
        $type = $this->input->post('type');
        $year = $this->input->post('year');
        $months= $this->input->post('months');

        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        if($months > 0){
            $this->db->where(array("month" => $months));
        }
        $this->db->where(array("conttype" => $type , "year" => $year , "status" => 1));
        $sql = $this->db->update("prime_contribution_matrix" , $updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Rate has been deleted.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to delete rates.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function removepayrollemp(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0,
            'updatedby' => user_id()
        );
        $this->db->where(array("sysid" => $dataid , "status" => 1));
        $sql = $this->db->update("payroll_emplist" , $updatearr);

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Employee has been removed.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove employee.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function addnewpayrollemp(){
        $data = array();
        $payrollemployee = $this->input->post('payrollemployee');
        $payrollaccountno = $this->input->post('payrollaccountno');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrolltype = $this->input->post('payrolltype');
        if($payrollpayclass == 1){
            $payrollpayclass = 128;
        }else if($payrollpayclass == 2){
            $payrollpayclass = 129;
        }else if($payrollpayclass == 3){
            $payrollpayclass = 3078;
        }else if($payrollpayclass == 4){
            $payrollpayclass = 3077;
        }
        $this->db->trans_begin();
        $insarr = array(
            'empid' => $payrollemployee,
            'accntno' => $payrollaccountno,
            'payclass' => $payrollpayclass,
            'costgroup' => $payrolltype,
            'status' => 1,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert("payroll_emplist" , $insarr);

        $contrarr = array(72,73,74,75);

        foreach ($contrarr as $arrval){
            $dedinsarr = array(
                'empid' => $payrollemployee,
                'deductid' => $arrval,
                'status' => 1
            );
            $this->db->insert("trn_employee_deduction_matrix" , $dedinsarr);
        }
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Employee has been added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add employee.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function fetchemployeeforbonuses(){
        $data = array();
        $confi = array();
        $typesid = $this->input->post('typesid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $paytype = $this->input->post('paytype');
        $payclass = $this->input->post('payclass');
        $viewtype = $this->input->post('viewtype');
        $getbasic = false;
        $editable = '';
        if($payclass > 0){
            if ($payclass == 1) {
                $confi_qry = $this->db->select('payclass')->from('prime_employee_main_payclass_grouping')
                    ->where(array('payrollpayclass' => $payclass, 'status' => 1))
                    ->get();
                if ($confi_qry->num_rows() > 0) {
                    foreach ($confi_qry->result() as $row) {
                        $confi[] = $row->payclass;
                    }
                    $this->db->where_in('pemp.payclass_id',$confi);
                }
            } else {
                $this->db->where(array("pemp.payclass_id" => $payclass));
            }
        }

        $jobcatarr = array(157 , 160);
        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname, pemp.payclass_id, pe.accntno , pes.amt")
            ->from("prime_employee_main as pem")
            ->join("payroll_emplist as pe" , "pe.empid = pem.sysid")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid")
            ->join("prime_employee_salary as pes" , "pes.empid  = pem.sysid && pes.status = 1")
            ->where(array("pem.status != " => 0 , "pe.status != " => 0 , "pemjc.status != " => 0))
            ->where_in('pemjc.jobcatid', $jobcatarr)
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $statusarr = array(305, 307);
                $getencodedvalue = $this->db->select("pme.gross,pme.tax,pme.status , pme.deduction")
                    ->from("payroll_manual_earnings as pme")
                    ->where(array(
                        "pme.typesid" => $typesid ,
                        "pme.empid" => $row->sysid  ,
                        "pme.month" => $month ,
                        "pme.year" => $year ,
                        "pme.paytype" => $paytype,
                        //"pme.status " =>
                    ))
                    ->where_in('pme.status' , $statusarr)
                    ->get()->row();
                $data['encoded'][$row->sysid] = $this->db->last_query();
                if($getencodedvalue){
                    if($getencodedvalue->status == 305){
                        $stat = '<span class="label label-sm label-success"> Done </span>';
                        $editable = 'disabled';
                    }else if($getencodedvalue->status == 307){
                        $stat = '<span class="label label-sm label-primary"> Draft </span>';
                    }
                    $grossencoded = $getencodedvalue->gross;
                    $taxencoded = $getencodedvalue->tax;
                    $netautocompute = $getencodedvalue->gross - ($getencodedvalue->tax + $getencodedvalue->deduction);
                    $deduction = $getencodedvalue->deduction;
                }else{
                    $grossencoded = '';
                    $taxencoded = '';
                    $netautocompute = '';
                    $deduction= '';
                    $stat = '<div id="setstat"></div>';
                }
                $rowcolor = ($row->accntno == '' || $row->accntno == 0) ? 'danger' : '';
                $data['databonuses'][] = array(
                    'num' => $num++,
                    'accntno' => $row->accntno,
                    'name' => $row->lastname.', '.$row->firstname,
                    'gross' => '<input '.$editable.' placeholder="Enter gross" value="'.$grossencoded.'" id="gross" data-type="'.$typesid.'" data-empid="'.$row->sysid.'" style="width: 100%" type="text" class="form-control inline" />',
                    'tax' => '<input '.$editable.' placeholder="Enter tax" value="'.$taxencoded.'" id="tax" data-type="'.$typesid.'" data-empid="'.$row->sysid.'" style="width: 100%" type="text" class="form-control inline" />',
                    'deduction' => '<input '.$editable.' placeholder="Enter deduction" value="'.$deduction.'" id="deduction" data-type="'.$typesid.'" data-empid="'.$row->sysid.'" style="width: 100%" type="text" class="form-control inline" />',
                    'net' => ($netautocompute != '') ? number_format($netautocompute , 2) : $netautocompute,
                    'type' => get_types_label_format($row->payclass_id),
                    'status' => $stat,
                    'rowcolor' => $rowcolor
                );


                if($viewtype && $viewtype == 1) {
                    $insarr = array(
                        'month' => $month,
                        'year' => $year,
                        'paytype' => $paytype,
                        'typesid' => $typesid,
                        'empid' => $row->sysid,
                        'gross' => $row->amt,
                        'tax' => 0,
                        'sent' => 0,
                        'deduction' => 0,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                        'status' => 307
                    );
                    $this->db->insert("payroll_manual_earnings" , $insarr);
                    $getbasic = true;
                }
            }
        }
        $data['getbasic'] = $getbasic;
        echo json_encode($data);
    }
    function getannualreport(){
        $data = array();
        $payrollyear = $this->input->post('payrollyear');
        $payrollmonth = $this->input->post('payrollmonth');
        $payrollpayclass = $this->input->post('payrollpayclass');
        $payrollpaytype = $this->input->post('payrollpaytype');

        if(in_array($payrollpayclass,array(128,3077,3078))){
            $this->db->where(array("pme.paytype" => $payrollpaytype , "emp.payclass_id" => $payrollpayclass));
        }else{
            $this->db->where(array("emp.payclass_id != " => 128));
        }

        $sql = $this->db->select("pcm.sysid , pcm.codes")
            ->from("payroll_manual_earnings as pme")
            ->join('prime_employee_main_payclass AS emp', 'emp.emp_id = pme.empid')
            ->join("prime_employee_costcenter as pec" , "pec.empid = pme.empid")
            ->join("prime_costcenter_main as pcm" , "pcm.sysid  = pec.ccid")
            ->where(array("pec.status" => 1 , "pec.type" => 1,"pme.month" => $payrollmonth ,
                "pme.year" => $payrollyear  , "emp.status" => 1))
            ->group_by("pcm.sysid , pcm.codes")
            ->order_by("pcm.codes")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $jobcat = array(157 , 160);
                $totalbasic = 0;
                $totaltax =0;
                $totalgross = 0;
                $totaldeduction = 0;
                $totalnet = 0;
                if(in_array($payrollpayclass,array(128,3077,3078))){
                    $this->db->where(array("pemp.payclass_id" => $payrollpayclass));
                }else{
                    $this->db->where_not_in("pemp.payclass_id" , array(128,3077,3078));
                }
                $getbasic = $this->db->select("pec.empid,pes.amt,pme.gross,pme.tax , pme.deduction")->from("prime_employee_costcenter as pec")
                    ->join("prime_employee_main as pem" , "pem.sysid = pec.empid")
                    ->join("payroll_emplist as pe" , "pe.empid = pec.empid")
                    ->join("prime_employee_main_job_category as pemjc","pemjc.empid = pec.empid")
                    ->join("prime_employee_salary as pes" , "pes.empid = pec.empid")
                    ->join("prime_employee_main_payclass as pemp" ,"pemp.emp_id = pec.empid")
                    ->join("payroll_manual_earnings as pme" , "pme.empid = pec.empid && pme.status = 305" , "left")
                    ->where(array("pec.ccid" => $row->sysid,"pec.status" => 1 , "pec.type" => 1
                    ,"pem.status" => 1, "pe.status" => 1 , "pes.status" => 1))
                    ->where_in("pemjc.jobcatid" , $jobcat)
                    ->get();
                if($getbasic->num_rows() > 0){
                    foreach ($getbasic->result() as $emprow){
                        $totalbasic += $emprow->amt;
                        $totaltax += $emprow->tax;
                        $totalgross += $emprow->gross;
                        $totaldeduction += $emprow->deduction;

                        $data['employees'][] = array(
                            'ccid' => $row->sysid,
                            'empid' => $emprow->empid,
                            'salary' => $emprow->amt,
                            'gross' => $emprow->gross,
                            'tax' => $emprow->tax,
                            'deduction' => $emprow->deduction,
                        );
                    }
                }

                $data['annualregdata'][] = array(
                    'expand' => '',
                    'code' => $row->codes,
                    'basic' => $totalbasic,
                    'gross' => $totalgross,
                    'tax' => $totaltax,
                    'deduction' => $totaldeduction,
                    'net' =>  $totalnet = ($totalgross - ($totaltax + $totaldeduction))
                );
            }
        }
        echo json_encode($data);
    }
    function fetchtierd1(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $typehalf = $this->input->post('tierd1typehalf');
        $approve = false;
        $cols_arr = array();

        $empcount = 0;

        $jobcatarr = implode(',', array(157 , 160));
        $sql = $this->db->select("pem.sysid,UPPER(p.lastname) as lastname,UPPER(p.firstname) as firstname,p.middlename,pemp.payclass_id")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem","pem.sysid = pe.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_employee_main_payclass as pemp","pemp.emp_id = pe.empid","left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pe.empid")
            ->where(array("pe.status" => 1,"pe.payclass" => 3077 , "pem.status" => 1))
            ->where_in('pemjc.jobcatid' , $jobcatarr)
            ->order_by("p.lastname")
            ->get();
        // CORRECTED BY LUCKY JOHN FADERON 4/7/2020
        $sql = $this->db->query("SELECT `pem`.`sysid`, UPPER(p.lastname) as lastname, UPPER(p.firstname) as firstname, `p`.`middlename`, `pemp`.`payclass_id`
            FROM (`prime_employee_main` as pem)
            LEFT JOIN `payroll_emplist` as pe ON `pem`.`sysid` = `pe`.`empid`
            LEFT JOIN `person` as p ON `p`.`sysid` = `pem`.`personid`
            LEFT JOIN `prime_employee_main_payclass` as pemp ON `pemp`.`emp_id` = `pem`.`sysid`
            LEFT JOIN `prime_employee_main_job_category` as pemjc ON `pemjc`.`empid` = `pem`.`sysid`
            WHERE `pemp`.`payclass_id` =  3077
            AND `pem`.`status` =  1
            AND `pemjc`.`jobcatid` IN ($jobcatarr) 
            ORDER BY `p`.`lastname`");
        if($sql->num_rows() > 0){
            $empcount = 1;
            $checkifprocess = $this->db->select("sysid")->from("payroll_reports_group")
                ->where(array("years" => $year ,"months" => $month , "payclass" => 3077 , "status" => 301 , "paytype" => $typehalf ))
                ->get()->row();


            $employeeCol = '<span style="width:200px;display:inline-block">Employee Name</span>';
            $cols_arr[] = array('data' => 'name', 'text' => $employeeCol, 'sClass' => 'zui-sticky-col');

            $colquery = $this->db->select("ptp.names")->from("payroll_process_owner as ppo")
                ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                ->where(array("ppo.status" => 1 , "pm.manual" => 1))
                ->group_by("ptp.names")
                ->order_by("ptp.names" , "ASC")
                ->get();
            if($colquery->num_rows() > 0){
                $cd = 0;
                foreach ($colquery->result() as $row3){
                    $th_html = '<span style="width:150px;display:inline-block">'.$row3->names.'</span>';
                    $cols_arr[] = array('data' => $cd++, 'sClass' => '', 'text' => $th_html);
                }
            }
            $num = 1;
            $mainindex = 0;
            $empindex = 0;
            foreach ($sql->result() as $row){
                $data['tierd1data'][] = array(
                    'name' => '<div style="display: inline-block; width: 100%" >'.$row->lastname.', '.$row->firstname.'</div>',
                );
                /* $data['rankandfiledata'][] = array(
                     'name' => '<div style="display: inline-block; width: 100%" >'.ucwords(strtolower($row->lastname)).', '.ucwords(strtolower($row->firstname)).'</div>',
                 ); */


                $trntypes = $this->db->select("ptp.sysid,ptp.names")->from("payroll_process_owner as ppo")
                    ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                    ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                    ->where(array("ppo.status" => 1 , "pm.manual" => 1))
                    ->group_by("ptp.sysid,ptp.names")
                    ->order_by("ptp.names" , "ASC")
                    ->get();
                $data['error1'] = $this->db->_error_message();
                if($trntypes->num_rows() > 0){
                    $i = 0;
                    $index = 0;
                    foreach ($trntypes->result() as $row1){

                        if($checkifprocess){
                            $approve = true;
                            $inputs = array(
                                $index => '<input style="background-color: #00CC66 !important;" disabled  type="text" id="tierd1input"  data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs" />'
                            );
                        }else{
                            $checkforexistingloans = $this->db->select("pmtb.amount")->from("payroll_manual_transactions_breakdown as pmtb")
                                ->join("payroll_manual_transactions as pmt","pmt.sysid = pmtb.groupid","left")
                                ->where(array("pmtb.empid" => $row->sysid, "pmtb.month" => $month , "pmtb.year" => $year ,"pmtb.status" => 313,"pmt.tsysid" => $row1->sysid,"pmtb.paytype" => $typehalf))
                                ->get()->row();
                            $data['typehalf'] = $typehalf;
                            $data['month'] = $month;
                            $data['year'] = $year;
                            $isreadonly = ($checkforexistingloans) ?  'readonly' : '';
                            $class = ($checkforexistingloans) ?  'text-danger' : '';
                            if($checkforexistingloans == false){
                                $checkforencodeddata = $this->db->select("amt")->from("payroll_transactions")
                                    ->where(array("status" => 1,"empid" => $row->sysid,"typesid" => $row1->sysid,
                                        "months" => $month,"years" => $year , "payspec" => $typehalf))->get()->row();
                                $amount = ($checkforencodeddata) ? $checkforencodeddata->amt : '';
                            }else{
                                $amount = $checkforexistingloans->amount;
                            }
                            $inputs = array(
                                $index => '<input '.$isreadonly.' type="text" id="tierd1input" value="'.$amount.'" data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs '.$class.'" />'
                            );
                        }


                        array_push($data['tierd1data'][$empindex],$inputs[$i]);
                        $i++;
                        $index++;
                    }
                }
                $empindex++;

            }
        }

        $data['dataempcount'] = $empcount;
        $data['columns'] = $cols_arr;
        $data['approve'] = $approve;
        echo json_encode($data);
    }
    function fetchtierd2(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $typehalf = $this->input->post('tierd2typehalf');
        $approve = false;
        $cols_arr = array();

        $empcount = 0;
        $jobcatarr = array(157 , 160);
        $sql = $this->db->select("pem.sysid,UPPER(p.lastname) as lastname,UPPER(p.firstname) as firstname,p.middlename,pemp.payclass_id")->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem","pem.sysid = pe.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_employee_main_payclass as pemp","pemp.emp_id = pe.empid","left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pe.empid")
            ->where(array("pe.status" => 1,"pe.payclass" => 3078 , "pem.status" => 1))
            ->where_in('pemjc.jobcatid' , $jobcatarr)
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $empcount = 1;
            $checkifprocess = $this->db->select("sysid")->from("payroll_reports_group")
                ->where(array("years" => $year ,"months" => $month , "payclass" => 3078 , "status" => 301 , "paytype" => $typehalf ))
                ->get()->row();


            $employeeCol = '<span style="width:200px;display:inline-block">Employee Name</span>';
            $cols_arr[] = array('data' => 'name', 'text' => $employeeCol, 'sClass' => 'zui-sticky-col');

            $colquery = $this->db->select("ptp.names")->from("payroll_process_owner as ppo")
                ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                ->where(array("ppo.status" => 1 , "pm.manual" => 1))
                ->group_by("ptp.names")
                ->order_by("ptp.names" , "ASC")
                ->get();
            if($colquery->num_rows() > 0){
                $cd = 0;
                foreach ($colquery->result() as $row3){
                    $th_html = '<span style="width:150px;display:inline-block">'.$row3->names.'</span>';
                    $cols_arr[] = array('data' => $cd++, 'sClass' => '', 'text' => $th_html);
                }
            }
            $num = 1;
            $mainindex = 0;
            $empindex = 0;
            foreach ($sql->result() as $row){
                $data['tierd2data'][] = array(
                    'name' => '<div style="display: inline-block; width: 100%" >'.$row->lastname.', '.$row->firstname.'</div>',
                );
                /* $data['rankandfiledata'][] = array(
                     'name' => '<div style="display: inline-block; width: 100%" >'.ucwords(strtolower($row->lastname)).', '.ucwords(strtolower($row->firstname)).'</div>',
                 ); */

                $trntypes = $this->db->select("ptp.sysid,ptp.names")->from("payroll_process_owner as ppo")
                    ->join("prime_types_parameter as ptp","ptp.sysid = ppo.typesid","left")
                    ->join("payroll_matrix as pm" , "pm.typesid = ppo.typesid" , "left")
                    ->where(array("ppo.status" => 1 , "pm.manual" => 1))
                    ->group_by("ptp.sysid,ptp.names")
                    ->order_by("ptp.names" , "ASC")
                    ->get();
                $data['error1'] = $this->db->_error_message();
                if($trntypes->num_rows() > 0){
                    $i = 0;
                    $index = 0;
                    foreach ($trntypes->result() as $row1){

                        if($checkifprocess){
                            $approve = true;
                            $inputs = array(
                                $index => '<input style="background-color: #00CC66 !important;" disabled  type="text" id="tierd2input"  data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs" />'
                            );
                        }else{
                            $checkforexistingloans = $this->db->select("pmtb.amount")->from("payroll_manual_transactions_breakdown as pmtb")
                                ->join("payroll_manual_transactions as pmt","pmt.sysid = pmtb.groupid","left")
                                ->where(array("pmtb.empid" => $row->sysid, "pmtb.month" => $month , "pmtb.year" => $year ,"pmtb.status" => 313,"pmt.tsysid" => $row1->sysid,"pmtb.paytype" => $typehalf))
                                ->get()->row();
                            $data['typehalf'] = $typehalf;
                            $data['month'] = $month;
                            $data['year'] = $year;
                            $isreadonly = ($checkforexistingloans) ?  'readonly' : '';
                            $class = ($checkforexistingloans) ?  'text-danger' : '';
                            if($checkforexistingloans == false){
                                $checkforencodeddata = $this->db->select("amt")->from("payroll_transactions")
                                    ->where(array("status" => 1,"empid" => $row->sysid,"typesid" => $row1->sysid,
                                        "months" => $month,"years" => $year , "payspec" => $typehalf))->get()->row();
                                $amount = ($checkforencodeddata) ? $checkforencodeddata->amt : '';
                            }else{
                                $amount = $checkforexistingloans->amount;
                            }
                            $inputs = array(
                                $index => '<input '.$isreadonly.' type="text" id="tierd2input" value="'.$amount.'" data-payclass="'.$row->payclass_id.'" data-empid="'.$row->sysid.'" data-id="'.$row1->sysid.'" name="'.$row1->sysid.'" class="form-control inline input-xs '.$class.'" />'
                            );
                        }


                        array_push($data['tierd2data'][$empindex],$inputs[$i]);
                        $i++;
                        $index++;
                    }
                }
                $empindex++;

            }
        }

        $data['empcount'] = $empcount;
        $data['columns'] = $cols_arr;
        $data['approve'] = $approve;
        echo json_encode($data);
    }
    function submitnet15report(){
        $data = array();
        $year = $this->input->post('net15year');
        $month = $this->input->post('net15month');
        $payclass = $this->input->post('net15payclass');
        $paytype = $this->input->post('net15paytype');
        $report = $this->input->post('report');
        $payrolldate = $this->input->post('payrolldate');
        $namesig = $this->input->post('namesig');
        $possig = $this->input->post('possig');
        $totalnet = 0;
        $net = 0;
        $html = '';
        if($report > 0){
            if($payclass == 128){
                $rep_title = 'RANK AND FILE PAYROLL';
                $rep_type_code = '';
            }else if($payclass == 3077){
                $rep_title = 'TIER 1';
                $rep_type_code = '';
            }else if($payclass == 3078){
                $rep_title = 'TIER 2';
                $rep_type_code = '';
            }else{
                $rep_title = 'CONFIDENTIAL PAYROLL';
                $rep_type_code = '';
            }


            $html .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);;
            //   $html .= '<div>CONFIDENTIAL</div>';
            $html .= '<div>PAYROLL PERIOD: '.date("F j, Y", strtotime($payrolldate)).'</div>';
            $html .= '<br>';
            $html .= '<table class="table table-bordered table-condensed">';
            $html .= '<thead>';
            $html .= '<th>EMP NAME</th>';
            $html .= '<th>ACCOUNT NO.</th>';

            if($payclass == 1 && $paytype == 3085){
                $html .= '<th>NET 15</th>';
            }else if($payclass == 1 && $paytype == 3086){
                $html .= '<th>NET 30</th>';
            }else if($payclass == 128 && $paytype == 3085){
                $html .= '<th>NET 15</th>';
            }else if($payclass == 128 && $paytype == 3086){
                $html .= '<th>NET 30</th>';
            }else if($payclass == 3077 && $paytype == 3085){
                $html .= '<th>NET 15</th>';
            }else if($payclass == 3077 && $paytype == 3086){
                $html .= '<th>NET 30</th>';
            }else if($payclass == 3078 && $paytype == 3085){
                $html .= '<th>NET 15</th>';
            }else if($payclass == 3078 && $paytype == 3086){
                $html .= '<th>NET 30</th>';
            }else{
                $html .= '<th>N/A</th>';
            }


            $html .= '</thead>';
            $html .= '<tbody>';
        }

        if($paytype == 3085){
            $paytype = 1;
        }else if(3086){
            $paytype = 2;
        }else{
            $paytype = 0;
        }

        $payclassarr = array(128, 3077 , 3078);
        if(in_array($payclass , $payclassarr)){
            $this->db->where(array("paytype" => $paytype));
        }
        $statuswhere = 0;
        if($payclass == 128 || $payclass == 3077 || $payclass == 3078){
            $statuswhere = 1;
        }else if($payclass == 1 && $paytype == 1){
            $statuswhere = 1;
        }else if($payclass == 1 && $paytype == 2){
            $statuswhere = 301;
        }

        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("years" => $year,"months" => $month,"payclass" => $payclass))
            ->where_in("status",array(1,301))
            ->get()->row();

        $data['groupid_qry'] = $this->db->last_query();
        $groupid = ($sql) ? $sql->sysid : '';
        $data['groupid'] = $groupid;
        $data['statuswhere'] = $statuswhere;
        $data['payclass'] = $payclass;
        $data['paytype'] = $paytype;
        if($groupid != '' || $groupid != 0){
            $getneypays = $this->db->select("prm.empid,prm.net , pe.accntno , p.lastname,p.firstname")->from("payroll_reports_main as prm")
                ->join("prime_employee_main as pem","pem.sysid = prm.empid")
                ->join("person as p" , "p.sysid = pem.personid")
                ->join("payroll_emplist as pe","pe.empid = prm.empid")
                ->where(array("prm.groupid" => $groupid , "pem.status" => 1 , "pe.status" => 1))
                ->order_by("p.lastname")->get();
            if($getneypays->num_rows() > 0){
                foreach ($getneypays->result() as $netrow){
                    $net = $netrow->net;
                    if($payclass == 1){
                        if($paytype == 1){
                            $checkdateend = $this->db->select("dateend")->from("prime_employee_main")
                                ->where(array("sysid" => $netrow->empid))->get()->row();
                            if($checkdateend && $checkdateend->dateend != '' && $checkdateend->dateend != null && $checkdateend->dateend != '0000-00-00'){
                                $net = number_format($netrow->net, 2, '.', '');
                            }else{
                                $net = number_format($netrow->net / 2, 2, '.', '');
                            }

                        }else if($paytype == 2){
                            $data['sulod'] = 'ok';
                            $firsthalf = number_format(($netrow->net / 2), 2, '.', '');
                            $net = ($netrow->net - $firsthalf);
                        }
                    }

                    $totalnet += $net;
                    $data['netpaylist'][] = array(
                        'empid' => $netrow->empid,
                        'name' => $netrow->lastname.', '.$netrow->firstname,
                        'accntno' => $netrow->accntno,
                        'netpay' => number_format($net ,2)
                    );
                    if($report > 0){
                        $html .= '<tr>';
                        $html .= '<td>'.$netrow->lastname.', '.$netrow->firstname.'</td>';
                        $html .= '<td>'.$netrow->accntno.'</td>';
                        $html .= '<td class="number">'.number_format($net , 2).'</td>';
                        $html .= '</tr>';
                    }
                }
            }
        }
        if($report > 0){
            $html .= '</tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td class="bold">TOTAL</td>';
            $html .= '<td></td>';
            $html .= '<td class="number bold">'.$totalnet.'</td>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';
            $html .= '<div style="margin-top: 40px;">';
            $html .= '<div>Note: This is to authorize you to debit TVI Savings Account 9240021169</div>';
            $html .= '<div>in the amount stated here representing payroll of TVI employees</div>';
            $html .= '<div>indicated in this list. THANK YOU!</div>';
            $html .= '</div>';

            $html .= '<div style="margin-top: 40px;">';
            $html .= '<div>APPROVED BY:</div>';
            $html .= '<div>TIEDRA VENTURES, INC</div>';
            $html .= '</div>';

            $html .= '<div class="row"  style="margin-top: 40px;">';
            $html .= '<div class="col-md-6 col-sm-6 col-xs-6">';
            $html .= '<div>____________________________________</div>';
            $html .= '<div>LUIS MIGUEL A. CACHO</div>';
            $html .= '<div>PRESIDENT/CHIEF EXECUTIVE OFFICER</div>';
            $html .= '</div>';
            $html .= '<div class="col-md-6 col-sm-6 col-xs-6">';
            $html .= '<div>____________________________________</div>';
            $html .= '<div>'.$namesig.'</div>';
            $html .= '<div>'.$possig.'</div>';
            $html .= '</div>';
            $html .= '</div>';




            $data['html'] = $html;
        }
        echo json_encode($data);
    }
    function getpayrollpaytype(){
        echo getpayrollpayclass();
    }
    function submitnet1530report(){

        $data = array();
        $year = $this->input->post('net1530year');
        $month = $this->input->post('net1530month');
        $payclass = $this->input->post('net1530payclass');
        $report = $this->input->post('report');
        $payrolldate = $this->input->post('payrolldate');
        $html = '';
        if($report > 0){
            $rep_type_code = '';
            $rep_title = 'CONFIDENTIAL PAYROLL';

            $html .= peco_print_header(user_id(), $rep_title, $rep_type_code, false);;
            //   $html .= '<div>CONFIDENTIAL</div>';
            $html .= '<div>PAYROLL PERIOD: '.date("F j, Y", strtotime($payrolldate)).'</div>';
            $html .= '<br>';
            $html .= '<table class="table table-bordered table-condensed">';
            $html .= '<thead>';
            $html .= '<th>EMP NAME</th>';
            $html .= '<th>ACCOUNT NO.</th>';
            $html .= '<th>NET 15</th>';
            $html .= '<th>NET 30</th>';
            $html .= '<th>NET TOTAL</th>';
            $html .= '</thead>';
            $html .= '<tbody>';
        }

        $net15 = 0;
        $net30 = 0;
        $total15 = 0;
        $total30 = 0;
        $totalnet1530 = 0;
        $total15no = 0;
        $total30no = 0;
        $sql = $this->db->select("sysid")->from("payroll_reports_group")
            ->where(array("status" => 1,"years" => $year,"months" => $month,"payclass" => $payclass))
            ->get()->row();
        $groupid = ($sql) ? $sql->sysid : '';
        if($groupid != '' || $groupid != 0) {
            $data['groupid'] = $groupid;
            $getneypays = $this->db->select("prm.empid, prm.net, pe.accntno , p.lastname, p.firstname")
                ->from("payroll_reports_main as prm")
                ->join("prime_employee_main as pem", "pem.sysid = prm.empid")
                ->join("person as p", "p.sysid = pem.personid")
                ->join("payroll_emplist as pe", "pe.empid = prm.empid AND pe.`status` = 1")
                ->where(array("prm.groupid" => $groupid))
                ->order_by("p.lastname")
                ->get();
            if ($getneypays->num_rows() > 0) {
                foreach ($getneypays->result() as $netrow) {

                    $net = $netrow->net;
                    $total15no = ($netrow->net / 2);
                    $total30no = ($netrow->net  - $total15no);


                    $checkdateend = $this->db->select("dateend")->from("prime_employee_main")
                        ->where(array("sysid" => $netrow->empid))->get()->row();
                    if($checkdateend && $checkdateend->dateend != '' && $checkdateend->dateend != null && $checkdateend->dateend != '0000-00-00'){
                        $net15 = number_format($netrow->net , 2, '.', '');
                        $net30 = number_format(0, 2, '.', '');
                    }else{
                        $net15 = number_format($netrow->net / 2, 2, '.', '');
                        $net30 = number_format($netrow->net - $net15, 2, '.', '');
                    }

                    $data['netpaylist'][] = array(
                        'empid' => $netrow->empid,
                        'name' => $netrow->lastname.', '.$netrow->firstname,
                        'accntno' => $netrow->accntno,
                        'net15' => number_format($net15 , 2) ,
                        'net30' => number_format($net30 , 2) ,
                        'totalnet' => number_format($net ,2)
                    );
                    $total15  += floatval($net15);
                    $total30 += floatval($net30);
                    $totalnet1530 += floatval($net);

                    if($report > 0){
                        $html .= '<tr>';
                        $html .= '<td>'.$netrow->lastname.', '.$netrow->firstname.'</td>';
                        $html .= '<td>'.$netrow->accntno.'</td>';
                        $html .= '<td class="number">'.number_format($net15 , 2) .'</td>';
                        $html .= '<td class="number">'.number_format($net30 , 2).'</td>';
                        $html .= '<td class="number">'.number_format($net ,2).'</td>';
                        $html .= '</tr>';
                    }

                }
            }
        }
        if($report > 0){
            $html .= '</tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td class="bold">TOTAL</td>';
            $html .= '<td></td>';
            $html .= '<td class="number bold">'.$total15.'</td>';
            $html .= '<td class="number bold">'.$total30.'</td>';
            $html .= '<td class="number bold">'.$totalnet1530.'</td>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';


            $html .= '<div style="margin-top: 40px;">';
            $html .= '<div>APPROVED BY:</div>';
            $html .= '<div>TIEDRA VENTURES, INC</div>';
            $html .= '</div>';

            $html .= '<div class="row"  style="margin-top: 40px;">';
            $html .= '<div class="col-md-6">';
            $html .= '<div>____________________________________</div>';
            $html .= '<div>LUIS MIGUEL A. CACHO</div>';
            $html .= '<div>PRESIDENT/CHIEF EXECUTIVE OFFICER</div>';
            $html .= '</div>';
            $html .= '</div>';

            $data['html'] = $html;
        }
        $data['total15'] = number_format($total15, 2, '.', '');
        $data['total30'] = number_format($total30, 2, '.', '');
        $data['total1530'] = number_format($totalnet1530, 2, '.', '');
        echo json_encode($data);
    }
    function getpreviewtrn(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $paytype = $this->input->post('paytype');
        $payclass = $this->input->post('payclass');

        /* PAYROLL REGISTER */
        $overalltotalearnings = 0;
        $overalltotaldeductions = 0;
        $overalltotalnetpay = 0;
        $overalltotalsscont = 0;
        $overalltotalsssloan = 0;
        $overalltotalhdmfcont = 0;
        $overalltotalhdmfloan = 0;
        $overalltotalpecewaloan = 0;
        $overalltotalcooploan = 0;
        $overalltotalpagibigadd = 0;
        $overalltotalotherdedn = 0;
        $overalltotalhmoded = 0;
        $overalltotaldeda = 0;
        $overalltotalelectric = 0;
        $overalltotalmemins = 0;
        $overalltotallwop = 0;
        $overalltotaltax = 0;
        /* ------------------- */

        /* EARNING REGISTER */
        $overalltotalbasic = 0;
        $overalltotalcola  = 0;
        $overalltotaltransallw = 0;
        $overalltotalrice  = 0;
        $overalltotalholiday  = 0;
        $overalltotalnightdiff  = 0;
        $overalltotalot  = 0;
        $overalltotalactingallw = 0;
        $overalltotalotheradd  = 0;
        /* ------------------- */

        /*OVER TIME */
        $overallndot8hrs  = 0;
        $overallndot8pay  = 0;
        $overallothrs  = 0;
        $overallone25  = 0;
        $overallone30  = 0;
        $overallone50  = 0;
        $overallone60  = 0;
        $overallone80  = 0;
        $overalltwo10  = 0;
        $overalltwo30  = 0;
        $overalltwo60 = 0;
        /* ------------------- */



        $getccids = $this->db->select("pec.ccid,pcm.codes")->from('payroll_emplist as pe')
            ->join("prime_employee_main as pem","pem.sysid = pe.empid")
            ->join("prime_employee_costcenter as pec","pec.empid = pe.empid && pec.type = 1 && pec.status = 1")
            ->join("prime_costcenter_main as pcm","pcm.sysid = pec.ccid")
            ->where(array("pe.status" => 1 , "pem.status" => 1 , "pe.payclass" => $payclass ))
            ->order_by("pcm.codes")
            ->group_by("pec.ccid,pcm.codes")
            ->get();
        if($getccids->num_rows() > 0){
            foreach ($getccids->result() as $row){
                $totalearnings = 0;
                $totaldeductions = 0;
                $totalsscont = 0;
                $totalsssloan = 0;
                $totalhmoded = 0;
                $totalhdmfloan = 0;
                $totaldeda = 0;
                $totalhdmfcont = 0;
                $totalpecewaloan = 0;
                $totalcooploan = 0;
                $totalpagibigadd = 0;
                $totalotherdedn = 0;
                $totalelectric = 0;
                $totalmemins = 0;
                $totallwop = 0;
                $totaltax = 0;
                $totalbasic = 0;
                $totalcola = 0;
                $totaltransallw = 0;
                $totalrice = 0;
                $totalholiday = 0;
                $totalnightdiff = 0;
                $totalot = 0;
                $totalactingallw = 0;
                $totalotheradd = 0;
                $ndot8hrs = 0;
                $ndot8pay = 0;
                $othrs = 0;
                $one25 = 0;
                $one30 = 0;
                $one50 = 0;
                $one60 = 0;
                $one80 = 0;
                $two10 = 0;
                $two30 = 0;
                $two60 = 0;

                $getemps = $this->db->select("pe.empid")->from('payroll_emplist as pe')
                    ->join("prime_employee_main as pem","pem.sysid = pe.empid")
                    ->join("prime_employee_costcenter as pec","pec.empid = pe.empid && pec.type = 1 && pec.status = 1")
                    ->join("prime_costcenter_main as pcm","pcm.sysid = pec.ccid")
                    ->where(array("pe.status" => 1 , "pem.status" => 1 , "pe.payclass" => $payclass,"pec.ccid" =>$row->ccid))
                    ->order_by("pcm.codes")
                    ->group_by("pe.empid")
                    ->get();
                if($getemps->num_rows() > 0){
                    foreach ($getemps->result() as $emprow){

                        $data['emplist2'][] = array(
                            'empid' => $emprow->empid,
                            'month' => $month,
                            'year' => $year,
                            'paytype' => $paytype,
                            'payclass' => $payclass,
                        );
                        $compute = compute_employee_netpay($emprow->empid, $month, $year, $paytype, 1, $payclass , 1);
                        $data['empdata'][] = $compute;
                        if($compute) {
                            $totalsscont += (isset($compute->ssscont)) ? $compute->ssscont : 0;
                            $totalhdmfcont += (isset($compute->pagibig)) ? $compute->pagibig : 0;
                            $totalsssloan += $compute->totalsssloan;
                            $totalhmoded += $compute->totalhmoded;
                            $totalhdmfloan += $compute->totalhdmfloan;
                            $totaldeda += $compute->totaldeda;
                            $totalpecewaloan += $compute->totalpecewaloan;
                            $totalcooploan += $compute->totalcooploan;
                            $totalpagibigadd += $compute->totalpagibigadd;
                            $totalotherdedn += $compute->totalotherdedn;
                            $totalelectric += $compute->totalelectric;
                            $totalmemins += $compute->totalmemins;
                            $totallwop += $compute->totallwop;
                            $totaltax += $compute->taxamt;
                            $totalbasic += $compute->basic;
                            $totalcola += $compute->totalcola;
                            $totaltransallw += $compute->totaltransallw;
                            $totalrice += $compute->totalrice;
                            $totalholiday += $compute->totalholiday;
                            $totalnightdiff += $compute->nightdiff;
                            $totalactingallw += $compute->totalactingallw;
                            $totalotheradd += $compute->totalotheradd;
                            $totalot += ($compute->otwithholiday + $compute->otweekend + $compute->otweekdays);

                            $ndot8hrs += $compute->ndot8hrs;
                            $ndot8pay += $compute->nightdiff;
                            $othrs    += ($compute->otwithholiday + $compute->otweekend + $compute->otweekdays);
                            $one25 += $compute->otweekdays;
                            $one30 += $compute->otweekend;
                            $one50 = 0;
                            $one60 += $compute->otwithholiday;
                            $one80 = 0;
                            $two10 = 0;
                            $two30 = 0;
                            $two60 = 0;


                            if( ( is_array($compute->earningarr) || is_object($compute->earningarr))){
                                foreach ($compute->earningarr as $earningrow) {
                                    $name =  $earningrow['names'];
                                    $amt = number_format($earningrow['amt'], 2, '.', '');
                                    $totalearnings += $amt;
                                }
                            }
                        }
                        if ($compute->deductions) {
                            foreach ($compute->deductions as $drow) {
                                $costname =  $drow['contname'];
                                $empamt =  number_format($drow['amt'], 2, '.', '');
                                $compamt = number_format($drow['amtcomp'], 2, '.', '');
                                $totaldeductions += $empamt;
                            }
                            if($compute->otherdeductarr){
                                foreach($compute->otherdeductarr as $otherdedrow){
                                    $otherdedname =  $otherdedrow['names'];
                                    $otherdedamt = number_format($otherdedrow['amt'], 2, '.', '');
                                    $totaldeductions += $otherdedamt;
                                }
                            }
                            if($compute->loansarr){
                                foreach ($compute->loansarr as $loansrow) {
                                    $loanname =  $loansrow['names'];
                                    $loanamt =  number_format($loansrow['amt'], 2, '.', '');
                                    $totaldeductions += $loanamt;
                                }
                            }
                        }
                        $getbasic = $this->db->select("amt")->from("prime_employee_salary")
                            ->where(array("status" => 1 , "empid" =>$emprow->empid ))->get()->row();
                        $totalearnings += ($getbasic) ? $getbasic->amt : 0;
                    }
                }

                $data['payrollreg'][] = array(
                    'expand' => $row->ccid,
                    'codes' => $row->codes,
                    'grossear' => number_format($totalearnings , 2),
                    'totalded' =>  number_format($totaldeductions , 2),
                    'totalnet' => number_format(($totalearnings - $totaldeductions ), 2),
                    'ssscont' => number_format($totalsscont , 2),
                    'sssloan' => number_format($totalsssloan , 2),
                    'hdmfcont' => number_format($totalhdmfcont , 2),
                    'hdmfloan' => number_format($totalhdmfloan , 2),
                    'pecewaloan' => number_format($totalpecewaloan , 2),
                    'cooploan' => number_format($totalcooploan , 2),
                    'pagibigadd' => number_format($totalpagibigadd , 2),
                    'otherded' => number_format($totalotherdedn , 2),
                    'hmoded' => number_format($totalhmoded , 2),
                    'deda' => number_format($totaldeda , 2),
                    'electbill' =>number_format($totalelectric , 2),
                    'memins' => number_format($totalmemins , 2),
                    'lwop' => number_format($totallwop , 2),
                    'tax' => number_format($totaltax , 2),
                );

                $overalltotalearnings += $totalearnings;
                $overalltotaldeductions += $totaldeductions;
                $overalltotalnetpay += ($totalearnings - $totaldeductions );
                $overalltotalsscont += $totalsscont;
                $overalltotalsssloan += $totalsssloan;
                $overalltotalhdmfcont += $totalhdmfcont;
                $overalltotalhdmfloan += $totalhdmfloan;
                $overalltotalpecewaloan += $totalpecewaloan;
                $overalltotalcooploan += $totalcooploan;
                $overalltotalpagibigadd += $totalpagibigadd;
                $overalltotalotherdedn += $totalotherdedn;
                $overalltotalhmoded += $totalhmoded;
                $overalltotaldeda += $totaldeda;
                $overalltotalelectric += $totalelectric;
                $overalltotalmemins += $totalmemins;
                $overalltotallwop += $totallwop;
                $overalltotaltax += $totaltax;

                $data['earningreg'][] = array(
                    'expand' => $row->ccid,
                    'codes' => $row->codes,
                    'basicrate' => number_format($totalbasic , 2),
                    'cola' =>  number_format($totalcola , 2),
                    'transallw' => number_format($totaltransallw, 2),
                    'ricesubsi' => number_format($totalrice , 2),
                    'holiday' => number_format($totalholiday , 2),
                    'nitediff' => number_format($totalnightdiff , 2),
                    'otpay' => number_format($totalot , 2),
                    'actingallw' => number_format($totalactingallw , 2),
                    'otheradd' => number_format($totalotheradd , 2)
                );

                $overalltotalbasic += $totalbasic;
                $overalltotalcola  += $totalcola;
                $overalltotaltransallw += $totaltransallw;
                $overalltotalrice  += $totalrice;
                $overalltotalholiday  += $totalholiday;
                $overalltotalnightdiff  += $totalnightdiff;
                $overalltotalot  += $totalot;
                $overalltotalactingallw += $totalactingallw;
                $overalltotalotheradd  += $totalotheradd;

                $data['deductionreg'][] = array(
                    'expand' => $row->ccid,
                    'codes' => $row->codes,
                    'ssscont' => number_format($totalsscont , 2),
                    'sssloan' => number_format($totalsssloan , 2),
                    'hdmfcont' => number_format($totalhdmfcont , 2),
                    'hdmfloan' => number_format($totalhdmfloan , 2),
                    'pecewaloan' => number_format($totalpecewaloan , 2),
                    'cooploan' => number_format($totalcooploan , 2),
                    'pagibigadd' => number_format($totalpagibigadd , 2),
                    'otherded' => number_format($totalotherdedn , 2),
                    'hmodedn' => number_format($totalhmoded , 2),
                    'deda' => number_format($totaldeda , 2),
                    'electbill' =>number_format($totalelectric , 2),
                    'memins' => number_format($totalmemins , 2),
                    'lwop' => number_format($totallwop , 2),
                    'basetax' => number_format($totaltax , 2),
                );

                $data['overtimereg'][] = array(
                    'expand' => $row->ccid,
                    'codes' => $row->codes,
                    'ndot8hrs' => number_format($ndot8hrs ,2),
                    'ndot8pay' => number_format($ndot8pay ,2),
                    'othrs' => number_format($othrs ,2),
                    '125%' => number_format($one25 , 2),
                    '130%' => number_format($one30 , 2),
                    '150%' => number_format($one50 , 2),
                    '160%' => number_format($one60 , 2),
                    '180%' => number_format($one80 , 2),
                    '210%' => number_format($two10 , 2),
                    '230%' => number_format($two30 , 2),
                    '260%' =>number_format($two60 , 2)
                );

                $overallndot8hrs  += $ndot8hrs;
                $overallndot8pay  += $ndot8pay;
                $overallothrs  += $othrs;
                $overallone25  += $one25;
                $overallone30  += $one30;
                $overallone50  += $one50;
                $overallone60  += $one60;
                $overallone80  += $one80;
                $overalltwo10  += $two10;
                $overalltwo30  += $two30;
                $overalltwo60 += $two60;
            }
        }
        $data['payrollres'][] = array(
            'resultgrossearningsprev' => $overalltotalearnings,
            'resulttotaldednprev' => $overalltotaldeductions,
            'resulttotalnetprev' => $overalltotalnetpay,
            'resultssscontprev' => $overalltotalsscont,
            'resultsssloanprev' => $overalltotalsssloan,
            'resulthdmfcontprev' => $overalltotalhdmfcont,
            'resulthdmfloanprev' => $overalltotalhdmfloan,
            'resultpecewaloanprev' => $overalltotalpecewaloan,
            'resultcooploanprev' => $overalltotalcooploan,
            'resultpagibigaddprev' => $overalltotalpagibigadd,
            'resultotherdeductionprev' => $overalltotalotherdedn,
            'resulthmodednprev' => $overalltotalhmoded,
            'resultdedaprev' => $overalltotaldeda,
            'resultelectricbillprev' => $overalltotalelectric,
            'resultmeminsprev' => $overalltotalmemins,
            'resultlwopprev' => $overalltotallwop,
            'resultbasetaxprev' => $overalltotaltax,
            'totaleaningbasicrateprev' => $overalltotalbasic,
            'totalearningcolaprev' => $overalltotalcola,
            'totalearningtransallwprev' => $overalltotaltransallw,
            'totalearningricesubsiprev' => $overalltotalrice,
            'totalearningholidaypayprev' => $overalltotalholiday,
            'totalearningnitediffprev' => $overalltotalnightdiff,
            'totalearningotpayprev' => $overalltotalot,
            'totalearningactingallwprev' => $overalltotalactingallw,
            'totalearningotheraddprev' => $overalltotalotheradd,
            'totaldeductionssscontprev' => $overalltotalsscont,
            'totaldeductionsssloanprev' => $overalltotalsssloan,
            'totaldeductionhdmfcontprev' => $overalltotalhdmfcont,
            'totaldeductionhdmfloanprev' => $overalltotalhdmfloan,
            'totaldeductionpecewaloanprev' => $overalltotalpecewaloan,
            'totaldeductioncooploanprev' => $overalltotalcooploan,
            'totaldeductionpagibigadprev' => $overalltotalpagibigadd,
            'totaldeductionotherdednprev' => $overalltotalotherdedn,
            'totaldeductionhmodednprev' => $overalltotalhmoded,
            'totaldeductiondedaprev' => $overalltotaldeda,
            'totaldeductionelectbillprev' => $overalltotalelectric,
            'totaldeductionmeminsprev' => $overalltotalmemins,
            'totaldeductionlwopprev' => $overalltotallwop,
            'totaldeductionbasetaxprev' => $overalltotaltax,
            'totalndothrsprev' => $overallndot8hrs,
            'totalndotpayprev' => $overallndot8pay,
            'totalothrsprev' => $overallothrs,
            'totalone25prev' => $overallone25,
            'totalone30prev' => $overallone30,
            'totalone50prev' => $overallone50,
            'totalone60prev' => $overallone60,
            'totalone80prev' => $overallone80,
            'totaltwo10prev' => $overalltwo10,
            'totaltwo30prev' => $overalltwo30,
            'totaltwo60prev' => $overalltwo60,

        );
        echo json_encode($data);
    }
    function addcontributiontoemp(){
        $data = array();
        $employeecont = $this->input->post('employeecont');
        $conttype = $this->input->post('conttype');

        $selectifexist = $this->db->select("")->from("trn_employee_deduction_matrix")
            ->where(array('empid' => $employeecont , "deductid" => $conttype, "status" => 1))
            ->get()->row();
        if($selectifexist){
            $this->db->where(array("empid" => $selectifexist->empid,"deductid" => $selectifexist->deductid , "status" => 1));
            $this->db->update("trn_employee_deduction_matrix" , array("status" => 0));
        }

        $this->db->trans_begin();
        $insarr = array(
            'empid' => $employeecont,
            'deductid' => $conttype,
            'status' => 1
        );
        $sql = $this->db->insert("trn_employee_deduction_matrix" , $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $data['msg'] = 'Contribution has been added to employee.';
            $data['func'] = 'success';
            $data['qry'] = true;
        }else{
            $this->db->trans_rollback();
            $data['msg'] = 'Failed to add contribution.';
            $data['func'] = 'error';
            $data['qry'] = false;
        }


        echo json_encode($data);
    }
    function getempconttbl(){
        $data = array();



        $sql = $this->db->select("p.lastname , p.firstname , GROUP_CONCAT(ptp.names ORDER BY ptp.names) AS contribs ")
            ->from("trn_employee_deduction_matrix as tedm")
            ->join("prime_employee_main as pem" , "pem.sysid = tedm.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = tedm.deductid AND ptp.codes = 'EMPCONT'")
            ->where(array("tedm.status" => 1 , "pem.status" => 1))
            ->group_by('p.lastname , p.firstname')
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['empcontdata'][] = array(
                    "num" => $num++,
                    "emp" => $row->lastname.', '.$row->firstname,
                    "type" => $row->contribs
                );
            }
        }

        echo json_encode($data);
    }


    function getdeductionsreport() {
        $data = $this->model_payroll->get_deductions_report();
        echo json_encode($data);
    }

    function getearningsreport() {
        $data = $this->model_payroll->get_earnings_report();
        echo json_encode($data);
    }

    function getovertimereport() {
        $data = $this->model_payroll->get_overtime_report();
        echo json_encode($data);
    }

    function tblcontribs() {
        echo $this->model_payroll->tbl_contribs();
    }

    function tblearnings() {
        echo $this->model_payroll->tbl_earnings();
    }

    function updateaddmatrix() {
        echo $this->model_payroll->update_add_matrix();
    }

    function getemppayslippreview() {
        echo $this->model_payroll->get_emp_payslip_preview();
    }

}
