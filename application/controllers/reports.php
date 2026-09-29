<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/7/2018
 * Time: 8:39 AM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Reports extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_reports');
        if(!user_id()) {
            redirect(base_url(), 'refres');
        }
    }
    // ##########################################################
    // ############## APT #######################################
    function datatableaptsummary() {
        echo $this->model_reports->datatable_apt_summary();
    }
    function chartaptaging() {
        echo $this->model_reports->chart_apt_aging();
    }

    // ##########################################################
    // ############## USER ######################################
    function getuserreports(){
        echo $this->model_reports->get_user_reports();
    }
    function usersessions(){
        echo $this->model_reports->get_user_session();
    }

    function payrollpayslip() {
        echo $this->model_reports->get_payroll_payslip();
    }

    function payslips($payclass, $year, $month , $paytype, $empid = false) {
        $payslip_data = $this->model_reports->pdf_monthly_payslip($payclass, $year, $month , $paytype, $empid);

        if ($payslip_data->qry == true) {
            $html = $payslip_data->html;
            $filename = $payslip_data->filename;

            //echo $html;
            //exit();

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
            $dompdf->stream($filename);
        } else {
            page_data_notfound_full('Payroll cycle seems not in the database!');
        }
    }

    function payslipview($payclass, $year, $month, $paytype, $empid = false)
    {
        echo $this->model_reports->pdf_monthly_payslip($payclass, $year, $month, $paytype, $empid);
    }

    function sendpayslips() {
        echo $this->model_reports->send_payroll_payslip();
    }


    function printemppayslip($empid,$from,$to) {
        $name = '';

        $from_ = explode('-',$from);
        $to_ = explode('-',$to);

        $paytypefrom = ($from_[2] > 15) ? 2 : 1;
        $paytypeto = ($to_[2] > 15) ? 2 : 1;
        $yearfrom = $from_[0];
        $yearto = $to_[0];
        $monthfrom = $from_[1];
        $monthto = $to_[1];

        $froms = implode('-',array($yearfrom,$monthfrom,$paytypefrom));
        $tos = implode('-',array($yearto,$monthto,$paytypeto));

        $payroll_qry = $this->db->select('prm.empid,prg.years,prg.months,prg.payclass,prg.paytype')
            ->from('payroll_reports_group as prg')
            ->join('payroll_reports_main as prm','prm.groupid = prg.sysid','left')
            ->where('CONCAT_WS("-",prg.years,LPAD(prg.months,2,0),prg.paytype) BETWEEN "'.$froms.'" AND "'.$tos.'"')
            ->where(array('prg.status' => 301, 'prm.empid' => $empid))
            ->get();

        if ($payroll_qry->num_rows() > 0) {
            $html = '';

            $html .= '<html>';
            $html .= '<head>';
            $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
            $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
            $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
            $html .= '</head>';
            $html .= '<body>';
            foreach ($payroll_qry->result() as $row) {
                $payroll[] = array($row->payclass, $row->years, $row->months , $row->paytype, $empid);
                $payslip_data = $this->model_reports->pdf_monthly_payslip($row->payclass, $row->years, $row->months , $row->paytype, $empid);
                if ($payslip_data->qry == true) {
                    $html .= $payslip_data->html;
                }
            }
            $html .= '</body>';
            $html .= '</html>';

            $paytypes = array('','1st Half','2nd Half');

            $emp = get_employee_info($empid);
            if ($emp) {
                $name = $emp->lastname.', '.$emp->firstname.' '.$emp->middlename[0].'.';
            }

            $monthsfrom = months_short($monthfrom);
            $monthsto = months_short($monthto);

            $file_from = implode('-',array($yearfrom,$monthsfrom,$paytypes[$paytypefrom]));
            $file_to = implode('-',array($yearto,$monthsto,$paytypes[$paytypeto]));

            $filename = 'PAYSLIP_'.$name.'_'.$file_from.'_ '.$file_to.'.pdf';

            //echo $html;
            /*echo '<pre>';
            print_r($payroll);
            echo '</pre>';*/
            //echo $html;
            //exit();

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
            $dompdf->stream($filename,array('Attachment' => false));
        } else {
            page_data_notfound_full('Payroll cycle seems not in the database!');
        }

    }
    // #######################################################################################
    // #######################################################################################
    // BILLING REPORTS
    function getbillingregister() {
        echo $this->model_reports->get_billing_register_dist();
    }

    // #######################################################################################
    // #######################################################################################
    // MRD REPORTS

    function getreadingreports() {
        echo $this->model_reports->get_report_reading();
    }
    function getmrdscheddata() {
        echo $this->model_reports->get_mrd_sched_data();
    }
    function fixmrdscheddata() {
        echo $this->model_reports->get_mrd_sched_data();
    }
    function getreadingreportsexcel($datastart, $dateend, $billmo, $billyr) {
        echo $this->model_reports->get_reading_reports_excel($datastart, $dateend, $billmo, $billyr);
    }

    // #######################################################################################
    // #######################################################################################
    // ADMIN
    function downloademployeesannualbankfile($tyesid, $year, $month, $paytype, $payclass) {
        echo $this->model_reports->download_employees_annual_bankfile($tyesid, $year, $month, $paytype, $payclass);
    }

    function testing() {
        echo '<pre>';
        $compute = compute_employee_netpay(159 , 1 ,2019,  1 , 1,  1);
        print_r($compute);
    }
    function printannualreport(){
        echo $this->model_reports->print_annual_report();
    }
    function sendannualpayslips(){
        echo $this->model_reports->send_annual_payslips();
    }
    function payrollreportdata(){
        echo $this->model_reports->payroll_report_data();
    }
    function printannualpayslip($typesid , $year , $month , $paytype , $viewtype){
        $payslip_data = $this->model_reports->pdf_annual_payslip($typesid , $year , $month , $paytype , $viewtype);

        $html = $payslip_data->html;
        $filename = $payslip_data->filename;

        //print_r($html);
        //exit();

        // echo $html;

        $this->load->library('pdf');
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $customPaper = array(0, 0, 610, 910);
        $dompdf->setPaper($customPaper, 'portrate');
        $dompdf->render();
        // Add PDF Document Information
        $dompdf->add_info('Subject', 'PECO PAYSLIP | ' . $filename);
        $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
        $dompdf->add_info('Creator', 'HRD');
        $dompdf->add_info('Keywords', 'Payslip');
        $dompdf->stream($filename);
    }

    function getemplistreport(){
        echo $this->model_reports->get_emp_list_report();
    }

    function getjobcat(){
        echo get_types_select('EMPJOBCAT',array(157,160));
    }

    function getpayrollreports($type = false, $year = false, $month = false, $costcenter = false, $payclass = false, $report = false) {
        echo $this->model_reports->get_payroll_reports($type, $year, $month, $costcenter, $payclass, $report);
    }

    function testexcel() {
        echo $this->model_reports->test_excel();
    }

    function sigtest() {
        echo draw_report_signatory(3072, 2,0.2);
    }
}