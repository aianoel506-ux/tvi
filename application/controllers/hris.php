<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Hris extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_hris');
        $this->load->model('model_reports');
        $this->load->helper('hris_helper', TRUE);
    }

    public function emplist() {
        try {
            header('Content-Type: application/json');
            $res = $this->model_hris->employee_list();
            // If the model returned non-json or empty, wrap into a safe response
            if ($res === false || $res === null || $res === '') {
                echo json_encode(array('func' => 'error', 'msg' => 'Empty response from server'));
            } else {
                echo $res;
            }
        } catch (Exception $e) {
            // Log the exception and return a friendly JSON error so frontend doesn't get HTTP 500
            log_message('error', 'emplist exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            header('Content-Type: application/json');
            echo json_encode(array('func' => 'error', 'msg' => 'Server error while fetching employee list'));
        }
    }

    public function empinfo() {
        echo $this->model_hris->get_employee_info();
    }

    public function gettimelogs() {
        echo $this->model_hris->emptime_logs();
    }

    public function gettimelogreps() {
        echo '<pre>';
        print_r($this->model_hris->emptime_logs_range());
    }

    public function gettimelogsdaily() {
        echo $this->model_hris->emptime_logs_daily();
    }

    public function getpremiums() {
        echo $this->model_hris->get_employee_premiums();
    }

    public function getemployeepremiumspaid() {
        echo $this->model_hris->get_employee_premiums_paid();
    }

    public function getemployeeleavecredits() { // FOR HR
        echo $this->model_hris->get_employee_leave_credits();
    }
    public function employeeleavecredits() { // FOR INDIVIDUAL LEAVE APPLICATION
        echo $this->model_hris->employee_leave_credits();
    }
    public function computenumdays() {
        echo $this->model_hris->compute_num_days();
    }
    public function getemployeeteammember() {
        echo $this->model_hris->get_employee_teammembers();
    }
    public function testdays() {
        $holidays = array();
        echo getWorkingDays('2018-02-01', '2018-02-05', $holidays);
    }

    function select2dept() {
        $data = array();
        $qry = $this->db->select()->from('prime_costcenter_main')->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes . ' - ' . $row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function select2earnings() {
        $data = array();
        $qry = $this->db->select('tp.sysid, tp.names, tp.desc')->from('prime_types_parameter AS tp')
            ->join('payroll_matrix AS pm', 'pm.typesid = tp.sysid')
            ->where(array('tp.status' => 1, 'tp.codes' => 'PRTRNTYPE', 'pm.functions' => 1))
            ->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function select2loans() {
        $data = array();
        $qry = $this->db->select('tp.sysid, tp.names, tp.desc')->from('prime_types_parameter AS tp')
            ->join('payroll_matrix AS pm', 'pm.typesid = tp.sysid')
            ->where(array('tp.status' => 1, 'tp.codes' => 'PRTRNTYPE', 'pm.codes' => 'loans'))
            ->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function select2deductions() {
        $data = array();
        $qry = $this->db->select('tp.sysid, tp.names, tp.desc')->from('prime_types_parameter AS tp')
            ->join('payroll_matrix AS pm', 'pm.typesid = tp.sysid')
            ->where(array('tp.status' => 1, 'pm.codes' => 'others' , 'pm.functions' => 0))
            ->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc
                );
            }
        }
        echo json_encode($data);
    }

    public function savenewemployee() {

        $data = array();
        $this->db->trans_begin();
        $zipcode = $this->input->post('zipcode');
        $agency = $this->input->post('agencyfield');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $middlename = $this->input->post('middlename');
        $bday = $this->input->post('bday');
        $datestart = $this->input->post('datestart');
        $gender = $this->input->post('gender');
        $addrcity = $this->input->post('addrcity');
        $addrdistrict = $this->input->post('addrdistrict');
        $addrspecific = $this->input->post('addrspecific');
        $nickname = $this->input->post('nickname');
        $marital_status = $this->input->post('marital');
        $nationality = $this->input->post('nationality');
        $account_number = $this->input->post('accountno');
        $department = $this->input->post('searchdept');
        $position = $this->input->post('searchpos');
        $pay_class = $this->input->post('searchpay');
        $job_cat = $this->input->post('search_job_cat');
        $salary = $this->input->post('salary');
        $costgroup = $this->input->post('costgroup');

        $inserttoperson = false;
        $inserttomainemp  = false;
        $inserttocostcenter  = false;
        $inserttoposition  = false;
        $inserttopayclass  = false;
        $inserttojobcat  = false;
        $inserttomarital  = false;
        $inserttonationality  = false;
        $inserttocredentials  = false;
        $inserttoaddresses  = false;
        $insertdefaultsalary  = false;
        $insertpayrollemplist = false;
        $msg = '';
        $empid = '';
        $empidarr = explode("-",$datestart);

        if($agency != 5){
            $type = 3;
            $empid = '';
        }else{
            $type = 1;
            $lastinc = $this->db->select("sysid")->from("prime_employee_main")
                ->where(array("type" => 1 , "status" => 1))
                ->order_by("sysid","DESC")
                ->limit(1)
                ->get()->row();
            if($lastinc){
                $lastno =  str_pad($lastinc->sysid + 1, 4, '0', STR_PAD_LEFT );
                $empid = $empidarr[0].$empidarr[1].$lastno;
            }
        }

        $getcheckperson = $this->db->select("sysid")->from("person")
            ->where(array("lastname" => $lastname , "firstname" => $firstname , "middlename" => $middlename))
            ->get()->row();
        if($getcheckperson){
            $personid = $getcheckperson->sysid;
        }else {
            $personarr = array(

                'firstname' => ucfirst(strtolower($firstname)),
                'lastname' => ucfirst(strtolower($lastname)),
                'middlename' => ucfirst(strtolower($middlename)),
                'birthdate' => ($bday != '') ? $bday : '1970-01-01',
                'gender' => $gender,
                'nickname' => $nickname,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $inserttoperson = $this->db->insert("person", $personarr);
            $data['person'] = $this->db->_error_message();
            $personid =  $this->db->insert_id();
        }
        $checkpersonid = $this->db->select("personid")->from("prime_employee_main")
            ->where(array("personid" => $personid , "status" => 1))->get()->row();
        $newemp  = false;
        if(!$checkpersonid){
            $employeemainarr = array(
                'personid' => $personid,
                'schedid' => 1,
                'status' => 1,
                'empid' => $empid,
                'type' => $type,
                'datestart' => $datestart,
                'createdby' => user_id(),
                'datecreated' => date('Y-m-d H:i:s')
            );
            $inserttomainemp = $this->db->insert("prime_employee_main" , $employeemainarr);
            $data['prime_employee_main'] = $this->db->_error_message();
            $lastempid =  $this->db->insert_id();
            $newemp = true;
        }else{
            $msg = 'Employee already exist!';
        }
        if($agency == 5){
            $data['sulod'] = 'true';
            $data['valuekaaccoutnumber'] = $account_number;
            $payrollemplistarr = array(
                'empid' => $lastempid,
                'accntno' => $account_number,
                'payclass' => ($pay_class == 128) ?  128 : 129,
                'costgroup' => $costgroup,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $this->db->insert("payroll_emplist" , $payrollemplistarr);
            $data['payrollemplist'] =  $this->db->_error_message();
        }
        if($newemp){

            $insarr = array(
                'empid' => $lastempid,
                'agencyid' => $agency,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_agency_matrix" , $insarr);
            $data['agencyinserterror'] = $this->db->_error_message();

            $costcenterarr = array(
                'empid' => $lastempid,
                'ccid' => $department,
                'type' => 1,
                'status' => 1,
                'datecreated' => date('Y-m-d H:i:s'),
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $inserttocostcenter = $this->db->insert("prime_employee_costcenter", $costcenterarr);
            $data['prime_employee_costcenter'] = $this->db->_error_message();

            $positionarr = array(
                'status' => 1,
                'datecreated' => date('Y-m-d H:i:s'),
                'emp_id' => $lastempid,
                'position_id' => $position,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $inserttoposition = $this->db->insert("prime_employee_main_positions" , $positionarr);
            $data['prime_employee_main_positions'] = $this->db->_error_message();


            $payclassarr = array(
                'datecreated' => date('Y-m-d H:i:s'),
                'emp_id' => $lastempid,
                'payclass_id' => ($pay_class != '') ? $pay_class : 0,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $inserttopayclass = $this->db->insert("prime_employee_main_payclass" , $payclassarr);
            $data['prime_employee_main_payclass'] = $this->db->_error_message();


            $jobcatarr = array(
                'empid' => $lastempid,
                'jobcatid' => $job_cat,
                'datecreated' => date('Y-m-d H:i:s'),
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $inserttojobcat = $this->db->insert("prime_employee_main_job_category" , $jobcatarr);
            $data['prime_employee_main_job_category'] = $this->db->_error_message();


            $maritalarr = array(
                'status' => 1,
                'datecreated' => date('Y-m-d H:i:s'),
                'personid' => $personid,
                'marital_status_id' => ($marital_status) ? $marital_status : 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $inserttomarital = $this->db->insert("persons_marital_status_logs",$maritalarr);
            $data['persons_marital_status_logs'] = $this->db->_error_message();

            $nationalarr = array(
                'status' => 1,
                'datecreated' => date('Y-m-d H:i:s'),
                'personid' => $personid,
                'nationality' => $nationality,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $inserttonationality = $this->db->insert("persons_nationality_logs",$nationalarr);
            $data['persons_nationality_logs'] = $this->db->_error_message();

            $credentialsarr = array(
                'bank_details' => $account_number,
                'emp_id' => $personid,
                'datecreated' => date('Y-m-d H:i:s'),
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $inserttocredentials = $this->db->insert("person_credentials" , $credentialsarr);
            $data['personcredentials'] = $this->db->_error_message();



            $personaddrarr = array(
                'personid' => $personid,
                'addrspec' => ucfirst(strtolower($addrspecific)),
                'addrdist' => ($addrdistrict == 0 || $addrdistrict == '' || $addrdistrict == null) ?  null  :$addrdistrict,
                'addrcity' =>  ($addrcity == 0 || $addrcity == '' || $addrcity == null) ? null : $addrcity,
                'status' => 1,
                'zipcode' => $zipcode,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $inserttoaddresses = $this->db->insert("person_address_matrix" , $personaddrarr);
            $data['personaddress'] = $this->db->_error_message();

            $salaryarr = array(
                'empid' => $lastempid,
                'amt' => $salary,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $insertdefaultsalary = $this->db->insert("prime_employee_salary" , $salaryarr);
            $data['errordefaultsalary'] = $this->db->_error_message();

            $ptp_deductions = $this->db->select('sysid')
                ->from('prime_types_parameter')
                ->where(array('codes' => 'EMPCONT', 'status' => 1 ))
                ->get();

            if ($ptp_deductions->num_rows() > 0){
                foreach ($ptp_deductions->result() AS $row){
                    $ins_array = array(
                        'empid' => $lastempid,
                        'deductid' => $row->sysid
                    );
                    $this->db->insert('trn_employee_deduction_matrix',$ins_array);
                    $data['error_deductions'] = $this->db->_error_message();
                }
            }
        }


        if($this->db->trans_status() == true  && $inserttoperson && $inserttomainemp && $inserttocostcenter && $inserttoposition && $inserttopayclass &&
            $inserttojobcat && $inserttomarital && $inserttonationality && $inserttocredentials && $inserttoaddresses  && $insertdefaultsalary){
            $this->db->trans_commit();
            $msg = 'New employee added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            if($msg == ''){
                $msg = 'Failed to add new employee';
            }

            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    // FORM AJAX EDITABLE
    function editinfo(){
        $data = array();
        $input = $this->input->post();
        $inputname = $input['name'];
        $ids = $input['pk'];
        $val = $input['value'];

        // GET PERSON ID
        $qry_person = $this->db->select('sysid, personid')->from('prime_employee_main')->where('sysid', $ids)->get()->row();
        if ($qry_person) {
            $empid = $qry_person->sysid;
            $personid = $qry_person->personid;
        } else {
            $personid = false;
            $empid = false;
        }
        $data['personid'] = $personid;

        $dayval = array(
            'MON' => 2,
            'TUE' => 3,
            'WED' => 4,
            'THU' => 5,
            'FRI' => 6
        );
        //insert time shift if not exist
        $getbasetime = $this->db->select("am_start , am_end, pm_start , pm_end,logcnt,logtype")->from("prime_employee_main_workshift")
            ->where(array("sysid" => $val , "status" => 1))->get()->row();
        if($getbasetime){
            $checkiftimeshiftexist = $this->db->select("shiftid")->from("prime_employee_time_shift_matrix")
                ->where(array("shiftid" => $val))->get()->row();
            if($checkiftimeshiftexist == false){
                if($getbasetime->logcnt == 4){
                    foreach ($dayval as $dayindex){
                        $insarr = array(
                            'shiftid' => $val,
                            'days' => $dayindex,
                            'amtimein' => $getbasetime->amstart,
                            'amtimeout' => $getbasetime->am_end,
                            'pmtimein' => $getbasetime->pm_start,
                            'pmtimeout' => $getbasetime->pm_end,
                        );
                        $this->db->insert("prime_employee_time_shift_matrix" , $insarr);
                    }
                }else if($getbasetime->logcnt == 2){
                    if($getbasetime->logtype == 0){
                        foreach ($dayval as $dayindex){
                            $insarr = array(
                                'shiftid' => $val,
                                'days' => $dayindex,
                                'amtimein' =>  $getbasetime->am_start,
                                'pmtimeout' => $getbasetime->pm_end,
                            );
                            $this->db->insert("prime_employee_time_shift_matrix" , $insarr);
                        }
                    }else if($getbasetime->logtype == 1){
                        foreach ($dayval as $dayindex){
                            $insarr = array(
                                'shiftid' => $val,
                                'days' => $dayindex,
                                'amtimein' => $getbasetime->amstart,
                                'amtimeout' => $getbasetime->am_end,
                            );
                            $this->db->insert("prime_employee_time_shift_matrix" , $insarr);
                        }
                    }else if($getbasetime->logtype == 2){
                        foreach ($dayval as $dayindex){
                            $insarr = array(
                                'shiftid' => $val,
                                'days' => $dayindex,
                                'pmtimein' => $getbasetime->pm_start,
                                'pmtimeout' => $getbasetime->pm_end,
                            );
                            $this->db->insert("prime_employee_time_shift_matrix" , $insarr);
                        }
                    }
                }
            }
        }

        if($inputname == 'birthdate') {
            $this->db->where('sysid', $personid);
            $this->db->update('person', array('birthdate' => $val , "updatedby" => user_id()));
        }
        if($inputname == 'bioid') {
            $this->db->where(array("empid" => $ids));
            $this->db->update('prime_employee_bioid' , array('status' => 0 , "updatedby" => user_id()));

            $insarr = array(
                'empid' => $ids,
                'bioid' => $val,
                'types' => 0,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_bioid" , $insarr);
            $data['error1'] = $this->db->_error_message();
        }
        if($inputname == 'workshift') {
            $this->db->where('empid', $ids);
            $this->db->update('prime_employee_main_workshift_matrix', array('status' => 0 , 'updatedby' => user_id()));

            $insarr = array(
                'empid' => $empid,
                'workshift_id' => $val,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_main_workshift_matrix" , $insarr);
        }
        if($inputname == 'addrspec') {
            //check if district exist
            $checkdist = $this->db->select("personid")->from("person_address_matrix")->where(array("personid"=>$personid))->get()->row();
            if($checkdist){
                // UPDATE EXISTING specific addr
                $this->db->where('personid', $personid);
                $this->db->update('person_address_matrix', array('addrspec' => $val , "updatedby" => user_id()));
            }else{
                //insert district
                $insarr = array(
                    'personid' => $personid,
                    'addrspec' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_address_matrix" , $insarr);
            }
        }
        if($inputname == 'homephone'){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("personid" => $personid, "types" => 1049 ));
            $this->db->update("person_contact_matrix" , $updatearr);
            $insarr = array(
                'personid' => $personid,
                'contactstring' => $val,
                'types' => 1049,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("person_contact_matrix" , $insarr);
        }
        if($inputname == 'workphone'){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("personid" => $personid, "types" => 1050));
            $this->db->update("person_contact_matrix" , $updatearr);
            $insarr = array(
                'personid' => $personid,
                'contactstring' => $val,
                'types' => 1050,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("person_contact_matrix" , $insarr);
        }
        if($inputname == 'cellphone'){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("personid" => $personid, "types" => 1051));
            $this->db->update("person_contact_matrix" , $updatearr);
            $insarr = array(
                'personid' => $personid,
                'contactstring' => $val,
                'types' => 1051,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("person_contact_matrix" , $insarr);
        }
        if($inputname == 'emailaddress'){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("personid" => $personid, "types" => 1053));
            $this->db->update("person_contact_matrix" , $updatearr);
            $insarr = array(
                'personid' => $personid,
                'contactstring' => $val,
                'types' => 1053,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("person_contact_matrix" , $insarr);
        }
        if($inputname == 'companyemail'){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("personid" => $personid, "types" => 1057));
            $this->db->update("person_contact_matrix" , $updatearr);
            $insarr = array(
                'personid' => $personid,
                'contactstring' => $val,
                'types' => 1057,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("person_contact_matrix" , $insarr);
        }
        if($inputname == 'height'){
            $updateheight = array(
                'height' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $ids));
            $this->db->update("prime_employee_other_info" , $updateheight);
            $data['errorupdateheight'] = $this->db->_error_message();
            if($this->db->affected_rows() == 0){
                $insarr = array(
                    'height' => $val,
                    'empid' => $ids,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_other_info" , $insarr);
                $data['errorinsertheight'] = $this->db->_error_message();
            }
        }
        if($inputname == 'weight'){
            $updateweight = array(
                'weight' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $ids));
            $this->db->update("prime_employee_other_info" , $updateweight);
            $data['errorupdateweight'] = $this->db->_error_message();
            if($this->db->affected_rows() == 0){
                $insarr = array(
                    'weight' => $val,
                    'empid' => $ids,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_other_info" , $insarr);
                $data['errorinsertweight'] = $this->db->_error_message();
            }
        }
        if($inputname == 'placeofbirth'){
            $updateplaceofbirth = array(
                'placeofbirth' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $ids));
            $this->db->update("prime_employee_other_info" , $updateplaceofbirth);
            $data['errorupdateplaceofbirth'] = $this->db->_error_message();
            if($this->db->affected_rows() == 0){
                $insarr = array(
                    'placeofbirth' => $val,
                    'empid' => $ids,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_other_info" , $insarr);
                $data['errorinsertplaceofbirth'] = $this->db->_error_message();
            }
        }
        if($inputname == 'gender') {
            $this->db->where('sysid', $personid);
            $this->db->update('person', array('gender' => $val , "updatedby" => user_id()));
        }
        if($inputname == 'nationality') {
            // UPDATE EXISTING TAG STATUS 0
            $this->db->where('personid', $personid);
            $this->db->update('persons_nationality_logs', array('status' => 0 , "updatedby" => user_id()));

            // INSERT NEW
            $ins_arr = array(
                'personid' => $personid,
                'createdby' => user_id(),
                'nationality' => $val,
                'updatedby' => user_id()
            );
            $this->db->insert('persons_nationality_logs', $ins_arr);
        }
        if($inputname == 'country') {
            //check if country exist
            $checkdist = $this->db->select("personid")->from("person_address_matrix")->where(array("personid"=>$personid))->get()->row();
            if($checkdist){
                // UPDATE EXISTING ADDRESS
                $this->db->where('personid', $personid);
                $this->db->update('person_address_matrix', array('addrcountry' => $val , "updatedby" => user_id()));
            }else{
                //insert district
                $insarr = array(
                    'personid' => $personid,
                    'addrcountry' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_address_matrix" , $insarr);
            }
        }
        if($inputname == 'city') {
            //check if city exist
            $checkdist = $this->db->select("personid")->from("person_address_matrix")->where(array("personid"=>$personid))->get()->row();
            if($checkdist){
                // UPDATE EXISTING city
                $this->db->where('personid', $personid);
                $this->db->update('person_address_matrix', array('addrcity' => $val , "updatedby" => user_id()));
            }else{
                //insert district
                $insarr = array(
                    'personid' => $personid,
                    'addrcity' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_address_matrix" , $insarr);
            }

        }
        if($inputname == 'district') {
            //check if district exist
            $checkdist = $this->db->select("personid")->from("person_address_matrix")->where(array("personid"=>$personid))->get()->row();
            if($checkdist){
                // UPDATE EXISTING district
                $this->db->where('personid', $personid);
                $this->db->update('person_address_matrix', array('addrdist' => $val , "updatedby" => user_id()));
            }else{
                //insert district
                $insarr = array(
                    'personid' => $personid,
                    'addrdist' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_address_matrix" , $insarr);
            }
        }
        if($inputname == 'civilstatus'){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("personid" => $personid));
            $this->db->update("persons_marital_status_logs" , $updatearr);

            $insertarr = array(
                'status' => 1,
                'personid' => $personid,
                'createdby' => user_id(),
                'marital_status_id' => $val,
                'updatedby' => user_id()
            );
            $this->db->insert("persons_marital_status_logs" , $insertarr);
        }
        if($inputname == 'bloodtype'){
            $updatearr = array(
                'bloodtype' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $ids));
            $this->db->update("prime_employee_other_info" , $updatearr);
            if($this->db->affected_rows() == 0){
                $insertarr = array(
                    'bloodtype' => $val,
                    'empid' => $ids,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_other_info" , $insertarr);
            }

        }
        if($inputname == 'religion'){

            $updatearr = array(
                'religion' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $ids));
            $this->db->update("prime_employee_other_info" , $updatearr);
            if($this->db->affected_rows() == 0){
                $insertarr = array(
                    'religion' => $val,
                    'empid' => $ids,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_other_info" , $insertarr);
            }
        }
        if($inputname == 'educattainment'){
            $updatearr = array(
                'educattainment' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $ids));
            $this->db->update("prime_employee_other_info" , $updatearr);
            if($this->db->affected_rows() == 0){
                $insertarr = array(
                    'educattainment' => $val,
                    'empid' => $ids,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_other_info" , $insertarr);
            }
        }
        if($inputname == 'license'){
            $updatearr = array(
                'license' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $ids));
            $this->db->update("prime_employee_other_info" , $updatearr);
            if($this->db->affected_rows() == 0){
                $insertarr = array(
                    'license' => $val,
                    'empid' => $ids,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_other_info" , $insertarr);
            }
        }
        if($inputname == 'sss') {
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('sss_num' => $val , "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            $data['sss1'] = $this->db->_error_message();
            if($count <= 0){
                $sssarr = array(
                    'emp_id' => $personid,
                    'sss_num' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_credentials" , $sssarr);
                $data['sss2'] = $this->db->_error_message();
            }
        }
        if($inputname == 'philhealth') {
            // UPDATE EXISTING philhealth
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('philhealth' => $val , "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $philhealtharr = array(
                    'emp_id' => $personid,
                    'philhealth' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_credentials" , $philhealtharr);
            }
        }
        if($inputname == 'pagibig') {
            // UPDATE EXISTING pagibig
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('pagibig' => $val , "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $pagibigarr = array(
                    'emp_id' => $personid,
                    'pagibig' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_credentials" , $pagibigarr);
            }
        }
        if($inputname == 'tin') {
            // UPDATE EXISTING tin
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('tin_num' => $val , "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $tinarr = array(
                    'emp_id' => $personid,
                    'tin_num' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                if($this->db->insert("person_credentials" , $tinarr)) {
                    update_logs(34, $ids, null, 'Updated TIN #', null);
                }
            }
        }
        if($inputname == 'passport') {
            // UPDATE EXISTING passport
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('passport_num' => $val, "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $tinarr = array(
                    'emp_id' => $personid,
                    'tin_num' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                if($this->db->insert("person_credentials" , $tinarr)) {
                    update_logs(34, $ids, null, 'Updated Passport #', null);
                }
            }
        }
        if($inputname == 'driver') {
            // UPDATE EXISTING driver
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('drivers_license' => $val, "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $driverarr = array(
                    'emp_id' => $personid,
                    'drivers_license' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                if($this->db->insert("person_credentials" , $driverarr)) {
                    update_logs(34, $ids, null, 'Updated Driver\'s License # to '.$val, null);
                }
            }
        }
        if($inputname == 'driverexp') {
            // UPDATE EXISTING driverexp
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('driver_license_expiry' => $val, "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $driverexp = array(
                    'emp_id' => $personid,
                    'driver_license_expiry' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                if ($this->db->insert("person_credentials" , $driverexp)){
                    update_logs(34, $ids, null, 'Updated Passport #', null);
                }
            }
        }
        if($inputname == 'bank') {
            // UPDATE EXISTING bank
            $this->db->where('emp_id', $personid);
            $sql  = $this->db->update('person_credentials', array('bank_name' => $val, "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $banknamearr = array(
                    'emp_id' => $personid,
                    'bank_name' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_credentials" , $banknamearr);
            }
        }
        if($inputname == 'bankid') {
            // UPDATE EXISTING bankid
            $this->db->where('emp_id', $personid);
            $this->db->update('person_credentials', array('bank_details' => $val , "updatedby" => user_id()));
            $count = $this->db->affected_rows();

            //UPDATE THE BANK ID TO THE PAYROLL EMPLIST TABLE
            $updatebankid = array(
                'accntno' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $empid , "status" => 1));
            $this->db->update("payroll_emplist" , $updatebankid);
            $count1 = $this->db->affected_rows();

            $getotherinfo = $this->db->select("payclass_id")->from("prime_employee_main_payclass")
                ->where(array("status" => 1 , "emp_id" => $empid))
                ->get()->row();


            if($count1 <= 0){
                $payrollemplistarr = array(
                    'empid' => $empid,
                    'accntno' => $val,
                    'status' => 1,
                    'payclass' => ($getotherinfo) ? $getotherinfo->payclass_id : 0,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),

                );
                $this->db->insert("payroll_emplist" , $payrollemplistarr);
            }

            if($count <= 0){
                $bankidarr = array(
                    'emp_id' => $personid,
                    'bank_details' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_credentials" , $bankidarr);
            }
        }
        if($inputname == 'salary') {
            // UPDATE EXISTING salary
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("status" => 1 , "empid" => $empid));
            $this->db->update("prime_employee_salary" , $updatearr);
            $insarr = array(
                'empid' => $empid,
                'amt' => $val,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_salary" , $insarr);
        }
        if($inputname == 'other') {
            // UPDATE EXISTING other
            $this->db->where('emp_id', $personid);
            $this->db->update('person_credentials', array('other_ids' => $val , "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $otherarr = array(
                    'emp_id' => $personid,
                    'other_ids' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_credentials" , $otherarr);
            }
        }
        if($inputname == 'otherid') {
            // UPDATE EXISTING otherid
            $this->db->where('emp_id', $personid);
            $sql = $this->db->update('person_credentials', array('other_ids_id' => $val , "updatedby" => user_id()));
            $count = $this->db->affected_rows();
            if($count <= 0){
                $otherids = array(
                    'emp_id' => $personid,
                    'other_ids_id' => $val,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("person_credentials" , $otherids);
            }
        }
        if($inputname == 'datestart') {
            // UPDATE EXISTING datestart
            $this->db->where('sysid', $empid);
            $this->db->update('prime_employee_main', array('datestart' => $val , "updatedby" => user_id()));
        }
        if($inputname == 'payclass') {
            // UPDATE EXISTING payclass


            //FOR TRAILING PAYCLASS
            $getexistingpayclass = $this->db->select("payclass_id")->from("prime_employee_main_payclass")
                ->where(array("status" => 1 , "emp_id" => $empid))->get()->row();
            if($getexistingpayclass){
                $insarrtrail = array(
                    'empid' => $empid,
                    'payclassold' => $getexistingpayclass->payclass_id,
                    'payclassnew' => $val,
                    'status' => 0,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("prime_employee_main_payclass_matrix" , $insarrtrail);
            }

            //UPDATING PAYCLASS OF PAYROLL EMPLOYEES -- table name = payroll_emplist
            $updatepayrollpayclassarr = array(
                'payclass' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("status" => 1 , "empid" => $empid));
            $this->db->update("payroll_emplist"  , $updatepayrollpayclassarr);

            $payclassarr = array(
                'payclass_id' => $val,
                'updatedby' => user_id()
            );
            $this->db->where('emp_id', $empid);
            $this->db->update('prime_employee_main_payclass' , $payclassarr);

        }
        if($inputname == 'jobcat') {
            // UPDATE EXISTING jobcat
            $this->db->where('empid', $empid);
            $this->db->update('prime_employee_main_job_category', array('jobcatid' => $val , "updatedby" => user_id()));
        }
        if($inputname == 'agency'){
            $updateagencyarr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("status" => 1 , "empid" => $empid));
            $this->db->update("prime_employee_agency_matrix" , $updateagencyarr);

            $insarr = array(
                'empid' => $empid,
                'agencyid' => $val,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_agency_matrix" , $insarr);
            if($val == 5){
                $updatetypearr = array(
                    'type' => 1,
                    'updatedby' => user_id()
                );
                $this->db->where(array("sysid" => $empid));
                $this->db->update("prime_employee_main" , $updatetypearr);
            }else{
                $updatetypearr = array(
                    'type' => 3,
                    'updatedby' => user_id()
                );
                $this->db->where(array("sysid" => $empid));
                $this->db->update("prime_employee_main" , $updatetypearr);
            }
        }
        if($inputname == 'emp_salary') {
            // UPDATE EXISTING emp_salary
            $data['empid'] = $empid;

            $this->db->where(array("empid" => $empid, "status" => 1));
            $this->db->update('prime_employee_salary', array('status' => 0 , "updatedby" => user_id()));

            $this->db->insert('prime_employee_salary', array("empid" => $empid, 'amt' => $val, 'status' => 1));
        }
        if($inputname == 'position') {
            // UPDATE EXISTING empost
            $this->db->where('emp_id', $ids);
            $this->db->update('prime_employee_main_positions', array('status' => 0 , "updatedby" => user_id()));

            $empostarr = array(
                'emp_id' => $empid,
                'position_id' => $val,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_main_positions" , $empostarr);
            $data['positionerror'] = $this->db->_error_message();
        }
        if($inputname == 'department') {
            // UPDATE EXISTING department

            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $empid  , "status" => 1));
            $this->db->update("prime_employee_costcenter", $updatearr);

            $insarr = array(
                'empid' => $empid,
                'ccid' => $val,
                'status' => 1,
                'type' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_costcenter" , $insarr);
        }
        if($inputname == 'employeelastname'){
            $updatearr = array(
                'lastname' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("sysid" => $personid));
            $this->db->update("person" , $updatearr);
        }
        if($inputname == 'employeefirstname'){
            $updatearr = array(
                'firstname' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("sysid" => $personid));
            $this->db->update("person" , $updatearr);
        }
        if($inputname == 'employeemiddlename'){
            $updatearr = array(
                'middlename' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("sysid" => $personid));
            $this->db->update("person" , $updatearr);
        }
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }


    function update_workshift() {
        $message = '';
        $title = 'Add Workshift';
        $func = 'error';
        //Including validation library
        //Setting values for tabel columns
        //initialize value
        $empid = $this->input->post('empid');
        $workshiftid = $this->input->post('workshiftid');

        //end initialize
        $check = $this->model_hris->check_data_workshift($empid);
        if ($check) {
            $message = 'Record Updated!';
            $func = 'warning';

            $udt = $this->model_hris->update_workshift_model($empid, $workshiftid);
        } else {

            $data = array(
                'empid' => $empid,
                'workshift_id' => $workshiftid
            );
            //Transfering data to Model
            $ins = $this->model_hris->insert_workshift_model($data);

            if ($ins) {
                $message = 'Added';
                $func = 'success';
            } else {
                $message = 'Not Added';
            }
        }

        $data['message'] = $message;
        $data['title'] = $title;
        $data['func'] = $func;

        echo json_encode($data);
    }
    function update_biometrics() {
        $message = '';
        $title = 'Add Biometrics';
        $func = 'error';
        //Including validation library
        //Setting values for tabel columns
        //initialize value
        $empid = $this->input->post('empid');
        $biometricsid = $this->input->post('biometricsid');

        //end initialize
        $check = $this->model_hris->check_data_biometrics($empid);
        if ($check) {
            $message = 'Record Updated!';
            $func = 'warning';

            $udt = $this->model_hris->update_biometrics_model($empid, $biometricsid);
        } else {


            $data = array(
                'empid' => $empid,
                'workshift_id' => $biometricsid
            );
            //Transfering data to Model
            $ins = $this->model_hris->insert_biometrics_model($data);

            if ($ins) {
                $message = 'Added';
                $func = 'success';
            } else {
                $message = 'Not Added';
            }
        }

        $data['message'] = $message;
        $data['title'] = $title;
        $data['func'] = $func;

        echo json_encode($data);
    }

    function add_employee_schedule() {
        $message = '';
        $title = 'Add Schedule';
        $func = 'error';
        //
        $empid = $this->input->post('empid');
        $workshiftid = $this->input->post('workshiftid');
        $schedstart = $this->input->post('schedstart');
        $schedend = $this->input->post('schedend');
        $check = $this->model_hris->check_data_schedule($empid, $schedstart, $schedend);
        if ($schedstart == "" || $schedend == "") {
            $message = 'Cannot process, missing data!';
            $func = 'error';
            $title = 'Data is Empty!';
        } else {
            if ($schedstart > $schedend) {
                $message = 'Schedule Start is greater than Schedule End!';
                $func = 'error';
                $title = 'Schedule Mismatch!';
            } else {
                if ($check) {
                    $message = 'Record Exists!';
                    $func = 'warning';
                    $title = 'Schedule Conflict!';
                } else {

                    //insert to schedule table
                    $insert_to_schedule_table = array('empid' => $empid, 'workshiftid' => $workshiftid, 'schedstart' => $schedstart, 'schedend' => $schedend);
                    $ins_sched = $this->db->insert('prime_employee_main_schedule_matrix', $insert_to_schedule_table);

                    if ($ins_sched) {
                        $message = 'Added';
                        $func = 'success';
                    } else {
                        $message = 'Not Added';
                    }
                }
            }
        }
        $data['message'] = $message;
        $data['title'] = $title;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function getallemployees() {
        $data = array();

        $sql = $this->db->select("pem.type , pem.schedid , pem.status , pem.empid , pem.datestart ,p.sysid,p.firstname,p.lastname,p.middlename,p.birthdate,p.gender,p.status,p.datecreated,p.nickname")
            ->from("person p")
            ->join("prime_employee_main pem", 'p.sysid = pem.personid', 'left')
            ->get();

        foreach ($sql->result() as $row) {

            $data['employeelist'][] = array(
                'firstname' => $row->firstname,
                'lastname' => $row->lastname,
                'middlename' => $row->middlename,
                'birthdate' => $row->birthdate,
                'gender' => $row->gender,
                'status' => $row->status,
                'datecreated' => $row->datecreated,
                'nickname' => $row->nickname,
                'type' => $row->type,
                'schedid' => $row->schedid,
                'empid' => $row->empid
            );
        }

        echo json_encode($data);
    }

    function get_employee_username($posid = false) {
        $data = array();
        if ($posid) {
            $this->db->where("pos.position_id", $posid);
        }
        $sql = $this->db->select("pem.sysid,pem.empid,p.lastname,p.firstname")
            ->from("prime_employee_main pem")
            ->join("person p", "pem.sysid = p.sysid")
            ->join("prime_employee_main_positions pos", "pos.emp_id=pem.sysid")
            ->get();

        $num_rows = $sql->num_rows();
        if ($num_rows > 0) {
            foreach ($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->empid . ' - ' . $row->lastname . ', ' . $row->firstname
                );
            }
        }
        $data['input'] = $this->input->post();
        $data['num'] = $num_rows;
        echo json_encode($data);
    }

    function showmeterreadingaccomplishment() {
        $data = array();
        $regular = array('1', '3', '5', '6', '7', '8');
        $special = array('11');
        $totalreg = 0;
        $totalsp = 0;
        $types = '';
        $empid = $this->input->post('empid');

        $sql = $this->db->select("tram.sysid,gm.g,gm.d,gm.l,gm.b,tram.readingdte,tram.readingcnt, tram.errors")
            ->from("trn_reading_accomplishments_manual AS tram")
            ->join("gdlb_main AS gm", "gm.sysid=tram.gdlbid", "left")
            ->where(array("empid" => $empid, "status" => 1))
            ->order_by("tram.sysid" , "desc")
            ->get();
        //$this->db->_error_message();
        $num_rows = $sql->num_rows();
        if ($num_rows > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {

                if (in_array($row->d, $regular)) {
                    $totalreg += $row->readingcnt;
                    $types = "Regular";
                } else if (in_array($row->d, $special)) {
                    $totalsp += $row->readingcnt;
                    $types = "Special";
                } else {
                    $totalsp += 0;
                    $totalreg += 0;
                }
                $data['accomplishments'][] = array(
                    'num' => $num++,
                    'gdlbid' => $row->g . '-' . $this->getdistrictcodes($row->d) . '-' . $row->l . '-' . $row->b,
                    'readingdte' => $row->readingdte,
                    'readingcnt' => $row->readingcnt,
                    'errors' => '<input type="hidden" value="' . $row->sysid . '" class="errorid" /><input class="form-control inline input-xs" type="text" style="width: 100%;" value="' . $row->errors . '"  id="errors" />',
                    'status' => $types,
                    'buttons' => '<a href="javascript:;" id="delete_btn"  data-id="' . $row->sysid . '" class="viewBtn btn btn-xs btn-danger"><i class="fa fa-times"></i></a>'
                );
            }
        } else {
            $totalreg = 0;
            $totalsp = 0;
        }
        $data['reg'] = $totalreg;
        $data['sp'] = $totalsp;
        echo json_encode($data);
    }

    function getdistrictcodes($data) {
        $codes = $this->db->select("codes")
            ->from("address_districts")
            ->where(array("sysid" => $data))
            ->get()->row();
        return $codes->codes;
    }

    function addreadingaccomplishment() {
        $data = array();
        $msg = '';
        $func = '';
        $qry = false;
        $employee = $this->input->post('select_employee');
        $gdlb = $this->input->post('select_gdlb');
        $reading_count = $this->input->post('reading_count');
        $date_reading = $this->input->post('date_reading');
        $createdby = user_id();
        $updateby = user_id();

        if (empty($employee) || empty($gdlb) || empty($reading_count) || empty($date_reading)) {
            $func = 'info';
            $msg = 'Please fill up all the required fields.';
            $qry = false;
        } else {

            $this->db->trans_begin();
            $ins = array(
                'empid' => $employee,
                'gdlbid' => $gdlb,
                'readingcnt' => $reading_count,
                'readingdte' => $date_reading,
                'createdby' => $createdby,
                'updatedby' => $updateby
            );

            $this->db->insert("trn_reading_accomplishments_manual", $ins);

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $func = 'success';
                $msg = 'Accomplishment has been saved!';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $func = 'error';
                $msg = 'Accomplishment not saved!';
                $qry = false;
            }
        }

        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function deleteaccomplishment() {
        $data = array();
        $func = '';
        $msg = '';
        $qry = false;
        $accomid = $this->input->post('accomid');
        $this->db->trans_begin();
        $stat = array(
            'status' => 0
        );
        $this->db->where(array('sysid' => $accomid));
        $this->db->update('trn_reading_accomplishments_manual', $stat);
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $func = 'success';
            $msg = 'Accomplishment has been removed successfully!';
            $qry = true;
        } else {
            $this->db->trans_rollback();
            $func = 'error';
            $msg = 'Accomplishment failed to delete!';
            $qry = false;
        }
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function get_gdlb() {
        $data = array();
        $list = get_gdlb_list();
        if ($list) {
            foreach ($list as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->GDLB . ' - ' . $row->GDLB
                );
            }
        }
        echo json_encode($data);
    }

    function updateaccomplishment() {
        $data = array();
        $func = '';
        $msg = '';
        $totalval = 0;
        $regtotal = 0;
        $sptotal = 0;
        $regular = array('1', '3', '5', '6', '7', '8');
        $special = array('11');
        $totalreg = 0;
        $totalsp = 0;
        $overalltotal = 0;
        $counts = 0;
        $groupid = 0;


        $payrollstart = $this->input->post('payrollstart');
        $payrollend = $this->input->post('payrollend');

        if (empty($payrollstart) || empty($payrollend)) {
            $func = 'error';
            $msg = 'Please select a payroll start and payroll end';
        } else {
            $this->db->trans_begin();

            $getlastgroupid = $this->db->select("groupid")
                ->from("trn_reading_accomplishments_manual")
                ->order_by("groupid" , "desc")
                ->limit(1)
                ->get()->row();
            if($getlastgroupid){
                $groupid = $getlastgroupid->groupid + 1;
            }else{
                $groupid = 1;
            }

            $updateempstat = array(
                "groupid" => $groupid,
                'status' => 2,
                'payrollstart' => $payrollstart,
                'payrollend' => $payrollend
            );
            $this->db->where(array("groupid" => null,"status" => 1));
            $this->db->update("trn_reading_accomplishments_manual", $updateempstat);

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $func = 'success';
                $msg = 'Accomplishments has been submitted successfully!';
            } else {
                $this->db->trans_rollback();
                $func = 'error';
                $msg = 'Failed to submit accomplishments';
            }
        }

        $data['func'] = $func;
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function getaccomplishmentreport() {
        $data = array();
        $totalsp = 0;
        $usercounts = 0;
        $totalreadbyemp = 0;
        $totalread = 0;
        $totalreg = 0;
        $overalltotal = 0;
        $regular = array('1', '3', '5', '6', '7', '8');
        $special = array('11');
        $count = 0;
        $maxcount = 0;
        $groupid = 1;

        $getlastgroupid = $this->db->select("groupid")
            ->from("trn_reading_accomplishments_manual")
            ->order_by("groupid" ,"desc")
            ->limit(1)
            ->get()->row();
        $data['lastgroupid'] = ($getlastgroupid) ? $getlastgroupid->groupid : 1;
        if($getlastgroupid){
            $groupid = $getlastgroupid->groupid;
        }else{
            $groupid = 1;
        }

        $fromaccomp = $this->input->post('fromaccomp');
        $toaccomp = $this->input->post('toaccomp');

        $getlatestpayrollstart = $this->db->select("MAX(payrollstart) AS payrollstart")
            ->from("trn_reading_accomplishments_manual")
            ->get()->row();
        $getlatestpayrollend = $this->db->select("MAX(payrollend) AS payrollend")
            ->from("trn_reading_accomplishments_manual")
            ->get()->row();

        $html = '';

        $html .= '<div class="row">';

        $html .= '<div class="col-lg-2 col-md-2 col-xs-2"  style="font-size: 15px !important;">';
        $html .= 'TO <br>';
        $html .= 'SUBJECT<br>';
        $html .= 'DATE<br>';
        $html .= '</div>';
        $html .= '<div class="col-lg-10 col-md-10 col-xs-10"  style="font-size: 15px !important;">';
        $html .= ': HUMAN RESOURCE DEPT.<br>';
        $html .= ': Total Meters Read by Contractual Meter Readers<br>';
        $html .= ': ' . date("M.d, Y") . '<br>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<br />';
        $html .= '<span  style="margin-left: 150px !important; font-size: 15px !important;">Assignment to contractual meter readers for reading are the kilowatthour meters in the following lot and book for period covered <span>' . date("M.d, Y", strtotime($fromaccomp)) . '-' . date("M.d, Y", strtotime($toaccomp)) . ' </span>';

        $html .= '<div class="mtreader-accomplishment">';
        $sql = $this->db->select("pem.sysid,pem.empid,p.lastname,p.firstname, p.middlename")
            ->from("prime_employee_main pem")
            ->join("person p", "pem.sysid=p.sysid")
            ->join("prime_employee_main_positions pos", "pos.emp_id=pem.sysid AND pos.position_id=164")
            ->get();

        $num_rows = $sql->num_rows();
        if ($num_rows > 0) {

            $cols1 = array_chunk($sql->result(), ceil(count($sql->result()) / 2));
            $data['cols'] = $cols1;
            foreach ($cols1 as $sql) {
                $count ++;
                $usercounts++;

                foreach ($sql as $reader) {

                    $html .= '<div class="col-md-6 col-xs-6 col-sm-6 col-lg-6" style="margin: 0px !important;padding: 0px !important;">';

                    $getgdlb = $this->db->select("tram.sysid,tram.gdlbid,tram.empid,tram.readingcnt , gm.g , gm.d , gm.l , gm.b")
                        ->from("trn_reading_accomplishments_manual AS tram")
                        ->join("gdlb_main AS gm", "tram.gdlbid = gm.sysid", "left")
                        ->where(array("tram.empid" => $reader->sysid, "tram.status" => 2, "tram.payrollstart" => $getlatestpayrollstart->payrollstart, "tram.payrollend" => $getlatestpayrollend->payrollend , "tram.groupid" => $groupid))
                        ->get();

                    $gettotalreadingperemployee = $this->db->select("SUM(tram.readingcnt) AS total")
                        ->from("trn_reading_accomplishments_manual as tram")
                        ->where(array("tram.empid" => $reader->sysid, "tram.payrollstart" => $getlatestpayrollstart->payrollstart, "tram.payrollend" => $getlatestpayrollend->payrollend , "tram.groupid" => $groupid))
                        ->get()->row();
                    $totalreadbyemp = ($gettotalreadingperemployee) ? $gettotalreadingperemployee->total : 0;

                    $html .= '<div class="col-md-12 col-sm-12 col-xs-12">';

                    if($getgdlb->num_rows() > 0){

                        $cols = array_chunk($getgdlb->result(), ceil(count($getgdlb->result()) / 2));
                        $html .= '<h5>' . $reader->lastname . " = " . number_format($totalreadbyemp, 2) . '</h5>';
                        foreach ($cols as $getgdlb) {
                            $html .= '<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6" style="margin: 0px !important;padding: 0px !important;">';
                            $html .= '<table class="table table-condensed table-bordered table-responsive tbl-xs">';

                            $html .= '<thead><th style="font-size: 9px !important;">LOT & BOOK</th><th style="font-size: 9px !important;">NO. OF MTRS.</th></thead>';
                            foreach ($getgdlb as $gdlb) {

                                if (in_array($gdlb->d, $regular)) {
                                    $totalreg += $gdlb->readingcnt;
                                } else if (in_array($gdlb->d, $special)) {
                                    $totalsp += $gdlb->readingcnt;
                                } else {

                                }
                                $overalltotal += $gdlb->readingcnt;
                                $html .= '<tr>';
                                $html .= '<td style="width:70% !important;font-size: 12px;">' . $gdlb->g . "-" . $this->getdistrictcodes($gdlb->d) . "-" . $gdlb->l . "-" . $gdlb->b . '</td>';
                                $html .= '<td style="width:30% !important;font-size: 12px;text-align: right;">' . $gdlb->readingcnt . '</td>';
                                $html .= '</tr>';
                            }

                            $html .= '<tr>';
                            $html .= '<td style="font-size: 12px;">TOTAL (REGULAR)</td>';
                            $html .= '<td class="bold" style="font-size: 12px;text-align: right;">' . number_format($totalreg, 2) . '</td>';
                            $html .= '</tr>';

                            $html .= '<tr>';
                            $html .= '<td style="font-size: 12px;">TOTAL (SPECIAL)</td>';
                            $html .= '<td class="bold" style="font-size: 12px;text-align: right;">' . number_format($totalsp, 2) . '</td>';
                            $html .= '</tr>';

                            $html .= '</table>';

                            $totalreg = 0;
                            $totalsp = 0;

                            $html .= '</div>';

                        }
                    }

                    $html .= '</div>';


                    $html .= '</div>';
                }

                $html .= '<div class="col-md-12 col-sm-12 col-xs-12">';

                $html .= '</div>';
            }
            $data['counts'] = $count;

            $html .= '<div class="row">';
            $html .= '<div class="col-lg-4 col-md-4 col-xs-4" style="clear: both !important;">';
            $html .= '<p style="font-size: 15px !important;">Grand Total' . '<b style="margin-left: 40px !important;">' . number_format($overalltotal, 2) . '</b>' . '</p>';
            $html .= '<p style="font-size: 15px !important;">Submitted by:</p>';
            $html .= '<p style="font-size: 15px !important;">Reynaldo D. Esteves<br>';
            $html .= 'SAFO</p>';
            $html .= '<p style="font-size: 15px !important;">Received by: ___________<br>';
            $html .= 'Date: ___________</p>';
            $html .= '</div>';

            $html .= '<div class="col-lg-8 col-md-8 col-xs-8" style="padding-top: 40px !important;">';
            $html .= '<p style="font-size: 15px !important;">Noted by:</p>';
            $html .= '<p style="font-size: 15px !important;">JOSE MARIA A. CACHO JR.<br>';
            $html .= 'VP-MCC</p>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $data['html'] = $html;
        $data['maxcount'] = $maxcount;
        echo json_encode($data);
    }

    function showmeterreaderemployees() {
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
        $groupid = 1;

        $getlastgroupid = $this->db->select("groupid")
            ->from("trn_reading_accomplishments_manual")
            ->order_by("groupid" ,"desc")
            ->limit(1)
            ->get()->row();
        if($getlastgroupid){
            $groupid = $getlastgroupid->groupid;
        }else{
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
            ->get();

        $num_rows = $sql->num_rows();
        if ($num_rows > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {

                $regularvalinput = $this->db->select("rate, total ,deduction")
                    ->from("trn_reading_payroll_logs")
                    ->where(array("logsid" => $row->sysid, "ratetype" => 7, "status" => 306 , "groupid" => $groupid))
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
                    ->where(array("empid" => $row->sysid, "status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend , "groupid" => $groupid))
                    ->get()->row();

                if ($gdlbcount) {
                    $gdlbsum += $gdlbcount->gdlbcount;
                }

                $regtotal = $this->db->select("SUM(tram.readingcnt) AS regtotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $regular)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend , "groupid" => $groupid))
                    //add where date = latest date
                    ->get()->row();

                $sptotal = $this->db->select("SUM(tram.readingcnt) AS sptotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $special)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend , "groupid" => $groupid))
                    //add where date = latest date
                    ->get()->row();

                $errorregularcount = $this->db->select("SUM(tram.errors) AS regularerrortotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $regular)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2))
                    ->get()->row();

                $errorspecialcount = $this->db->select("SUM(tram.errors) AS specialerrortotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $special)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2))
                    ->get()->row();


                $totalamount = ($regtotal->regtotal * $getregval) + ($sptotal->sptotal * $getspval) - (($getregdeduct) + ($getspdeduct));
                $totalamountsummary = ($totalamountsummary) - (($getregdeduct) + ($getspdeduct));

                $totaldeduction += ($getregdeduct) + ($getspdeduct);

                $data['meter_reader_table'][] = array(
                    'num' => $num++ . '<input id="dataid" type="hidden" value="' . $row->sysid . '"/>',
                    'empid' => $row->empid,
                    'fullname' => $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename,
                    'gdlb' => '<span class="gdlbtotalclass">' . $gdlbcount->gdlbcount . '</span>',
                    'regtotal' => number_format($regtotal->regtotal, 2) . '<input id="reghiddenval" type="hidden" value="' . $regtotal->regtotal . '"/>',
                    'sptotal' => number_format($sptotal->sptotal, 2) . '<input id="sphiddenval" type="hidden" value="' . $sptotal->sptotal . '"/>',
                    'regrate' => $getregval,
                    'sprate' => $getspval,
                    'regdeduct' => '<span id="totalregdeduct">' . number_format($errorregularcount->regularerrortotal * 50, 2) . '</span> <input id="errorregularcount" type="hidden" value="' . ($errorregularcount->regularerrortotal * 50) . '" />',
                    'spdeduct' => '<span id="totalspdeduct">' . number_format($errorspecialcount->specialerrortotal * 50, 2) . '</span> <input id="errorspecialcount" type="hidden" value="' . ($errorspecialcount->specialerrortotal * 50) . '" />',
                    'total' => '<span id="totaltext">' . number_format($totalamount, 2) . '</span> <input type="hidden" value="' . $totalamount . '"  id="totalinput" />',
                    'done' => $done
                );
                $getsptotal = 0;
                $getregtotal = 0;
            }
        }

        $data['totaldeduction'] = $totaldeduction;
        $data['totalamountsummary'] = $totalamountsummary;
        $data['gdlbsum'] = $gdlbsum;
        echo json_encode($data);
    }

    function getemployeetotalreadingcount($data) {
        $sql = $this->db->select("SUM(tram.readingcnt) AS totalreading")
            ->from("trn_reading_accomplishments_manual AS tram")
            ->join("prime_employee_main AS pem", "tram.empid = pem.sysid", "left")
            ->where(array("pem.empid" => $data))
            ->get()->row();
        return $sql->totalreading;
    }

    function meterreadingchart() {
        $totalamount = 0;
        $data = array();
        $meterreadingarr = array();
        $regular = array('1', '3', '5', '6', '7', '8');
        $special = array('11');
        $getregval = 0;
        $getspval = 0;
        $getregtotal = 0;
        $getsptotal = 0;

        $gdlbsum = 0;
        $totalamountsummary = 0;
        $groupid = 1;

        $getlastgroupid = $this->db->select("groupid")
            ->from("trn_reading_accomplishments_manual")
            ->order_by("groupid" ,"desc")
            ->limit(1)
            ->get()->row();
        $data['lastgroupid'] = $getlastgroupid->groupid;
        if($getlastgroupid){
            $groupid = $getlastgroupid->groupid;
        }else{
            $groupid = 1;
        }

        $getlatestpayrollstart = $this->db->select("MAX(payrollstart) AS payrollstart")
            ->from("trn_reading_accomplishments_manual")
            ->where(array("groupid"=>$groupid))
            ->get()->row();
        $getlatestpayrollend = $this->db->select("MAX(payrollend) AS payrollend")
            ->from("trn_reading_accomplishments_manual")
            ->where(array("groupid"=>$groupid))
            ->get()->row();

        $sql = $this->db->select("pem.sysid,pem.empid,p.lastname,p.firstname, p.middlename")
            ->from("prime_employee_main pem")
            ->join("person p", "pem.sysid=p.sysid")
            ->join("prime_employee_main_positions pos", "pos.emp_id=pem.sysid AND pos.position_id=164")
            ->join("trn_reading_accomplishments_manual as tram" , "tram.empid = pem.sysid" ,"left")
            ->where(array("payrollstart" => $getlatestpayrollstart->payrollstart , "payrollend" => $getlatestpayrollend->payrollend , "tram.groupid" =>$groupid))
            ->group_by("pem.sysid")
            ->get();
        $num_rows = $sql->num_rows();
        if ($num_rows > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {

                $regularvalinput = $this->db->select("rate, total")
                    ->from("trn_reading_payroll_logs")
                    ->where(array("logsid" => $row->sysid, "ratetype" => 7, "status" => 306 , "groupid" =>$groupid))
                    ->get()->row();

                if ($regularvalinput) {
                    $getregval = $regularvalinput->rate;

                    if ($getregval != null) {
                        $getregtotal = $regularvalinput->total * $getregval;
                    } else {
                        $getregtotal = 0;
                    }
                } else {
                    $getregval = null;
                }

                $specialvalinput = $this->db->select("rate , total")
                    ->from("trn_reading_payroll_logs")
                    ->where(array("logsid" => $row->sysid, "ratetype" => 8, "status" => 306 , "groupid" =>$groupid))
                    ->get()->row();

                if ($specialvalinput) {
                    $getspval = $specialvalinput->rate;

                    if ($getspval != null) {
                        $getsptotal = $specialvalinput->total * $getspval;
                    } else {
                        $getsptotal = 0;
                    }
                } else {
                    $getspval = null;
                }

                $totalamountsummary += $getregtotal + $getsptotal;

                $gdlbcount = $this->db->select("COUNT(gdlbid) AS gdlbcount")
                    ->from("trn_reading_accomplishments_manual")
                    ->where(array("empid" => $row->sysid, "status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend , "groupid" =>$groupid))
                    ->get()->row();

                if ($gdlbcount) {
                    $gdlbsum += $gdlbcount->gdlbcount;
                }


                $regtotal = $this->db->select("SUM(tram.readingcnt) AS regtotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $regular)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend, "groupid" =>$groupid))
                    ->get()->row();

                $sptotal = $this->db->select("SUM(tram.readingcnt) AS sptotal")
                    ->from("trn_reading_accomplishments_manual AS tram")
                    ->join("gdlb_main AS gm", "gm.sysid = tram.gdlbid", "left")
                    ->where_in("gm.d", $special)
                    ->where(array("tram.empid" => $row->sysid, "tram.status" => 2, "payrollstart" => $getlatestpayrollstart->payrollstart, "payrollend" => $getlatestpayrollend->payrollend, "groupid" =>$groupid))
                    ->get()->row();

                $getdeduction = $this->db->select("SUM(deduction) AS totaldeduct")
                    ->from("trn_reading_payroll_logs")
                    ->where(array("logsid" => $row->sysid, "status" => 306, "groupid" =>$groupid))
                    ->get()->row();

                $totalamount = (($regtotal->regtotal * $getregval) + ($sptotal->sptotal * $getspval)) - $getdeduction->totaldeduct;
                $meterreadingarr[] = array(
                    'fullname' => $row->lastname . ', ' . $row->firstname,
                    'total' => $totalamount,
                    'color' => '#' . $this->random_color(),
                    'regtotal' => $regtotal->regtotal,
                    'sptotal' => $sptotal->sptotal
                );
                $getsptotal = 0;
                $getregtotal = 0;
            }
        }

        $data['meterreadingarr'] = $meterreadingarr;
        echo json_encode($data);
    }

    function random_color_part() {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    function random_color() {
        return $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }

    function updateerrorreading() {

        $data = array();
        $func = '';
        $msg = '';
        $qry = false;
        $this->db->trans_begin();

        $id = $this->input->post('id');
        $errordata = array(
            'errors' => $this->input->post('value')
        );

        $this->db->where(array("sysid" => $id));
        $this->db->update("trn_reading_accomplishments_manual", $errordata);
        $this->db->_error_message();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $qry = true;
            $func = 'success';
            $msg = 'Error reading saved!';
        } else {
            $this->db->trans_rollback();
            $qry = false;
            $func = 'error';
            $msg = 'Failed to update reading errors';
        }
        $data['qry'] =  $qry;
        $data['func'] = $func;
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function getlatestdate() {
        $data = array();
        $getlatestpayrollstart = $this->db->select("MAX(payrollstart) AS payrollstart")
            ->from("trn_reading_accomplishments_manual")
            ->get()->row();
        $getlatestpayrollend = $this->db->select("MAX(payrollend) AS payrollend")
            ->from("trn_reading_accomplishments_manual")
            ->get()->row();
        if($getlatestpayrollstart->payrollstart == null || $getlatestpayrollstart->payrollstart == ''){
            $data['latestfrom'] = 'N/A';
        }else{
            $data['latestfrom'] = $getlatestpayrollstart->payrollstart;
        }

        if($getlatestpayrollend->payrollend == null || $getlatestpayrollend->payrollend == ''){
            $data['latestto'] = 'N/A';
        }else{
            $data['latestto'] = $getlatestpayrollend->payrollend;
        }
        echo json_encode($data);
    }

    function fetchleavecredits(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $sql = $this->db->select("datecreated , `from` , `to` , fromtime , totime , totalinminutes , status")
            ->from("trn_employee_leave_requests")
            ->where(array("empid" => $dataid))
            ->order_by("datecreated","desc")
            ->get();
        if($sql->num_rows() > 0){
            $num=1;
            foreach ($sql->result() as $row){

                //SPENT
                $totalspenthours = $row->totalinminutes / 60;
                $dayspent = floor($totalspenthours / 8);
                $hourspent = ($totalspenthours % 8);
                $n = $totalspenthours;
                $whole = floor($n);      // 1
                $minutespent = ($n - $whole) * 60;

                $data['leavecreditsprofile'][] = array(
                    'num' => $num++,
                    'dateapplication' => $row->datecreated,
                    'fromdate' => $row->from,
                    'todate' => $row->to,
                    'fromtime' => date("g:i A", strtotime($row->fromtime)),
                    'totime' =>  date("g:i A", strtotime($row->totime)),
                    'total' =>  $dayspent.' - '.$hourspent.' - '.round($minutespent),
                    'status' =>  get_types_label_format($row->status)
                );
            }
        }
        echo json_encode($data);
    }
    function fetchcredittypes(){
        $data = array();
        $sql = $this->db->select("sysid,names,desc")
            ->from("prime_types_parameter")
            ->where(array("codes" => 'LEAVECREDITS'))
            ->get();
        $numrows = $sql->num_rows();
        if($numrows > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['creditlist'][] = array(
                    'num' => $num++,
                    'types' => $row->names.' - '.$row->desc,
                    'radio' => '<input data-id="'.$row->sysid.'" class="radioselected" id="'.$row->sysid.'" value="'.$row->sysid.'" type="radio" name="credits"/>'
                );
            }
        }
        echo json_encode($data);
    }
    function applycredits(){
        $data = array();
        $func = '';
        $msg = '';
        $qry = false;
        $this->db->trans_begin();
        $payclassarr = array(128 , 129 , 130 , 156 , 250 , 267);
        $sql = $this->db->select("pem.sysid")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid" , "left")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid" , "left")
            ->where(array("pem.status" => 1 , "pemjc.jobcatid" => 157 , "pemjc.status" => 1))
            ->where_in("pemp.payclass_id" , $payclassarr)
            ->order_by("p.lastname")
            ->get();

        $numrows = $sql->num_rows();
        if($numrows > 0){
            $credits = $this->input->post('credits');
            $year = $this->input->post('year');
            $types = $this->input->post('types');

            foreach ($sql->result() as $row){
                $data['sysid'][] = $row->sysid;

                $insarr = array(
                    'empid' => $row->sysid,
                    'credit' => $credits,
                    'types' => $types,
                    'year' => $year,
                    'datecreated' => date("Y-m-d"),
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 1
                );

                $insertcredits = $this->db->insert("prime_employee_main_leave_credits" , $insarr);
            }
            if($this->db->trans_status() === TRUE  && $insertcredits){
                $this->db->trans_commit();
                $func = 'success';
                $msg = 'Credits applied';
                $qry = true;
            }else{
                $this->db->trans_rollback();
                $func = 'error';
                $msg = 'Failed to apply credits';
                $qry = false;
            }
        }
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function getlatestyearcredits(){
        $data = array();
        $sql = $this->db->select("year")
            ->from("prime_employee_main_leave_credits")
            ->order_by("year" , "desc")
            ->get()->row();
        if($sql){
            $data['latestyear'] = $sql->year + 1;
        }
        echo json_encode($data);
    }

    function copytonextyearcredits(){
        $data = array();

        $this->db->trans_begin();
        $year = $this->input->post('year');
        $prevyear = $year - 1;
        $getprevyearcredits = $this->db->select()
            ->from("prime_employee_main_leave_credits")
            ->where(array("year" => $prevyear))
            ->get();
        $data['error1'] = $this->db->_error_message();

        if($getprevyearcredits->num_rows() > 0){
            foreach ($getprevyearcredits->result() as $row){
                $insarr = array(
                    'empid' => $row->empid,
                    'credit' => $row->credit,
                    'types' => $row->types,
                    'year' => $year,
                    'datecreated' => date("Y-m-d"),
                    'createdby' => user_id(),
                    'status' => 1
                );
                $this->db->insert("prime_employee_main_leave_credits" , $insarr);
                $data['error2'] = $this->db->_error_message();
            }

        }
        if($this->db->trans_status() === TRUE  && $getprevyearcredits->num_rows() > 0){
            $this->db->trans_commit();
            $func = 'success';
            $msg = 'Copy credits applied';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $func = 'error';
            $msg = 'Failed to copy credits, please make sure there are previous record to copy.';
            $qry = false;
        }
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function fetchemployees(){
        $data = array();
        $payclass = $this->input->post('payclass');

        $jobcat = array(157 , 160);
        if ($payclass) {
            if ($payclass == 1) {
                $payclassarr = array(129, 130, 156, 250, 267, 3073);
            } else {
                $payclassarr = array($payclass);
            }
        } else {
            $payclassarr = array(128, 129, 130, 156, 250, 267, 3077, 3078);
        }
        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname , p.middlename")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid" , "left")
            ->where(array("pemjc.status" => 1 , "pem.status" => 1))
            ->where_in("pemjc.jobcatid" , $jobcat)
            ->where_in("pemp.payclass_id" , $payclassarr)
            ->order_by("p.lastname")
            ->get();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['emplist'][] = array(
                    'num' => $num++,
                    'lastname' => $row->lastname,
                    'firstname' => $row->firstname,
                    'middlename' => $row->middlename,
                    'control' => '<input data-id="'.$row->sysid.'" class="checkboxselected icheck" id="'.$row->sysid.'" value="'.$row->sysid.'" type="checkbox" name="selectedemp['.$row->sysid.']">'
                );
            }
        }
        echo json_encode($data);
    }
    function fetchempmaintenance(){
        $data = array();
        $sql = $this->db->select("pem.sysid , pem.empid ,p.firstname , p.lastname,p.middlename ,  cc.desc as department")
            ->from("prime_employee_main AS pem")
            ->join("person AS p" ,"pem.personid = p.sysid" , "left")
            ->join('prime_employee_costcenter AS ccm', 'ccm.empid = pem.sysid and ccm.status = 1', 'left')
            ->join('prime_costcenter_main AS cc', 'ccm.ccid = cc.sysid', 'left')
            ->where(array("pem.status" => 1))
            ->group_by('pem.sysid')
            ->get();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['empmaintenance'][] = array(

                    "empcode" => $row->empid,
                    "lastname" => $row->lastname,
                    "firstname" => $row->firstname,
                    "middlename" => $row->middlename,
                    "department" => $row->department,
                    "control" => '<button id="updateempbtn" data-id="'.$row->sysid.'" class="btn btn-primary btn-xs inline"><i class="fa fa-wrench"></i></button>',

                );
            }
        }
        echo json_encode($data);
    }
    function addleavecreditselected(){
        $data = array();
        $selectedemp = $this->input->post('selectedemp');
        $days = $this->input->post('nodays');
        $types = $this->input->post('types');
        $year = $this->input->post('year');
        $hours = $this->input->post('nohours');
        $this->db->trans_begin();
        $count = 0;
        $hours = $hours / 8;
        $days = ($days + $hours);
        foreach($selectedemp as $row) {
            $ins_arr = array(
                'empid' => $row,
                'credit' => $days,
                'types' => $types,
                'year' => $year,
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );

            $this->db->where(
                array(
                    'empid' => $row,
                    'types' => $types,
                    'year' => $year,
                    'status' => 1
                )
            );
            $this->db->update('prime_employee_main_leave_credits', array('updatedby' => user_id(), 'status' => 0));
            $ins = $this->db->insert('prime_employee_main_leave_credits', $ins_arr);
            $err = $this->db->_error_message();
            $data['err'] = $err;
            if($ins) {
                $count += 1;
            }
            $data['ins'][] = $ins_arr;
        }

        if($this->db->trans_status() == TRUE){
            $this->db->trans_commit();
            $func = 'success';
            $msg = 'Credits applied';
            $qry = true;

        }else{
            $this->db->trans_rollback();
            $func = 'error';
            $msg = 'Failed to apply credits';
            $qry = false;
        }
        $data['count'] = $count;
        $data['credits'] = $days;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function addeavetype(){
        $data =array();
        $func = '';
        $msg = '';
        $qry = false;
        $names = $this->input->post('names');
        $desc = $this->input->post('desc');

        $this->db->trans_begin();

        $insarr = array(
            'codes' => 'LEAVECREDITS',
            'names' => $names,
            'desc' => $desc
        );
        $addleavetype = $this->db->insert("prime_types_parameter" , $insarr);
        if($this->db->trans_status() === TRUE && $addleavetype){
            $this->db->trans_commit();
            $func = 'success';
            $msg = 'Credits added';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $func = 'error';
            $msg = 'Failed to add credits';
            $qry = false;
        }

        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function select2workshift(){


        $data = array();
        $qry = $this->db->select("sysid,codes,desc")->from('prime_employee_main_workshift')
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->codes) . ' - ' . ucfirst($row->desc),
                );
            }
        }
        echo json_encode($data);
    }
    function printempdetails(){
        $data = array();
        $html = '';
        $dataid = $this->input->post('dataid');
        $info =  get_employee_info($dataid);
        $payclass = select_emp_payclass($dataid)->names;
        $empjobcat = select_emp_jobcat($dataid)->names;
        $duration = get_emp_duration($dataid)->timespent;
        $department = get_emp_department($dataid)->desc;
        $emp_position = isset(select_emp_position($dataid)->names) ? select_emp_position($dataid)->names : 'Unassigned Position';
        $person_pic = get_owner_pic($dataid, 'person');

        $html .= '<div class="row">';

        $html .= '<div class="col-md-3 col-sm-3 col-xs-3">';

        $html .= '<img src="'.$person_pic.'" class="img-responsive" alt="" style="display:block !important;margin:auto !important;" />';
        $html .=  '<h3 style="text-align: center !important;">'.$info->empid.'</h3>';

        $html .= '</div>';

        $html .= '<div class="col-md-9 col-sm-9 col-xs-9">';

        $html .= '<ul class="list-group summary column no-border list-group-xs">';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Name</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->lastname.', '.$info->firstname.'</span>';
        $html .= '<li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Address</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->addrspec.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Gender</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->name.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Birthday</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->birthdate.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">District</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->dist.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">City</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->names.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Country</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->country.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Nationality</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$info->nationality.'</span>';
        $html .= '</li>';

        $html .= '</ul>';

        $html .= '</div>';

        $html .= '</div>';

        //---------------------------------------

        $html .= '<div class="row">';

        $html .= '<div class="col-md-3 col-sm-3 col-xs-3">';
        $html .= '</div>';

        $html .= '<div class="col-md-9 col-sm-9 col-xs-9" style="border:1px solid gray !important;">';

        $html .= '<div class="col-md-4 col-sm-4 col-xs-4 box" style="text-align: center !important;">';

        $html .= '<h3>'.$info->datestart.'</h3>';
        $html .= '<label>Date Employed</label>';

        $html .= '</div>';

        $html .= '<div class="col-md-4 col-sm-4 col-xs-4 box" style="text-align: center !important;border-right: 1px solid gray;border-left: 1px solid gray !important;">';

        $html .= '<h3>'.$payclass.'</h3>';
        $html .= '<label>Pay Class</label>';

        $html .= '</div>';

        $html .= '<div class="col-md-4 col-sm-4 col-xs-4 box" style="text-align: center !important;">';

        $html .= '<h3>'.$empjobcat.'</h3>';
        $html .= '<label>Status</label>';

        $html .= '</div>';

        $html .= '</div>';

        $html .= '</div>';  // end row

        //---------------------------------------

        $html .= '<div class="row">';

        $html .= '<div class="col-md-3 col-sm-3 col-xs-3">';
        $html .= '</div>';

        $html .= '<div class="col-md-9 col-sm-9 col-xs-9">';

        $html .= '<ul class="list-group summary column no-border list-group-xs">';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Duration</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$duration.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Department</span>';
        $html .= '<span class="label-default col-md-7 col-sm-7 col-xs-7">'.$department.'</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5 col-sm-5 col-xs-5">Position</span>';
        $html .= '<span class="label-default col-md-7  col-sm-7 col-xs-7">'.$emp_position.'</span>';
        $html .= '</li>';

        $html .= '</ul>';

        $html .= '</div>';

        $html .= '</div>';  // end row

        $data['html'] = $html;
        echo json_encode($data);
    }

    function gebarcode() {
        echo generate_qrcode('M13049');
    }

    function uploadprofilepic(){
        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $image = false;
        $dataid = $this->input->post('personid');
        if(isset($_FILES["newpic"])) {

            $qry_time = $this->db->query("SELECT HOUR(NOW()) AS HRS, MINUTE(NOW()) AS MIN, SECOND(NOW()) AS SEC")->row();
            $hrs = str_pad($qry_time->HRS, 2, '0', STR_PAD_LEFT);
            $min = str_pad($qry_time->MIN, 2, '0', STR_PAD_LEFT);
            $sec = str_pad($qry_time->SEC, 2, '0', STR_PAD_LEFT);
            $hour_num = $hrs . $min . $sec;

            $temp = explode(".", $_FILES["newpic"]["name"]);
            $newfilename = 'primary_' . date('Y') . str_pad(date('m'), 2, '0', STR_PAD_LEFT) . str_pad(date('d'), 2, '0', STR_PAD_LEFT) . $hour_num . '.' . end($temp);
            $file_directory = FCPATH . "uploads/person/" . $dataid . "/";
            //  $file_directory = "net use z:\\\\172.20.224.15cad\\attachedments\\" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";
            // ###############################################
            // CREATE DIRECTORY
            $config['overwrite'] = TRUE;
            $config['upload_path'] = $file_directory;
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 10000;
            $config['max_width'] = 5000;
            $config['max_height'] = 8000;
            $config['encrypt_name'] = FALSE;
            $config['file_name'] = $newfilename;
            $this->load->library('upload', $config);

            // ###############################################
            // CREATE DIRECTORY
            if (!is_dir($file_directory)) {
                mkdir($file_directory, 0777, TRUE);
                //chmod($file_directory, 0777);
            }
            // ###############################################

            if (!$this->upload->do_upload('newpic')) {
                $msg = "Upload error";
                $qry = false;
                $func = 'error';
            } else {
                $msg = "Profile Picture Updated";
                $qry = true;
                $func = 'success';
                $image = base_url() . "uploads/person/" . $dataid . "/" . $newfilename;
            }

        }else{
            $msg = 'Drop the file again!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func']  = $func;
        $data['image'] = $image;
        echo json_encode($data);
    }

    function updatestatusemp(){
        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            "status" => 0
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("prime_employee_main" , $updatearr);
        if($this->db->trans_status() === true && $sql){
            $this->db->trans_commit();
            $msg = 'Employee status has been updated';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to update employee status';
            $qry = false;
            $func = 'error';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func']  = $func;
        echo json_encode($data);
    }
    function adddependes(){
        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $dataid = $this->input->post('dataid');
        $fnametxt = $this->input->post('fnametxt');
        $birthdate = $this->input->post('birthdate');
        $lastname = $this->input->post('lnametxt');
        $gender = $this->input->post('gendercombo');
        $middlename = $this->input->post('mnametxt');
        $nickname = $this->input->post('nickname');

        $this->db->trans_begin();



        $insertarr = array(
            "firstname" => $fnametxt,
            "lastname" => $lastname,
            "middlename" => $middlename,
            "birthdate" => $birthdate,
            "gender" => $gender,
            "nickname" => $nickname,
        );

        $sql = $this->db->insert("person" , $insertarr);

        if($this->db->trans_status() === true && $sql){
            $this->db->trans_commit();
            $msg = 'Dependents added.';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to add dependents.';
            $qry = false;
            $func = 'error';
        }

        $getlastdependent = $this->db->select("sysid")
            ->from("person")
            ->order_by("sysid" , "desc")
            ->get()->row();
        if($getlastdependent){
            $dependents = array(
                'empid' => $dataid,
                'dependentid' =>$getlastdependent->sysid,
                'types' => '1',
                'status' => '1',
                'createdby' => user_id()
            );
            $this->db->insert("prime_employee_dependents" , $dependents);
        }




        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func']  = $func;
        echo json_encode($data);
    }
    function getgender(){
        $data = array();
        $sql = $this->db->select("sysid, code , name")
            ->from("prime_gender")
            ->get();
        $num_rows = $sql->num_rows();
        if ($num_rows > 0) {
            foreach ($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->sysid . ' - ' . $row->code . ', ' . $row->name
                );
            }
        }

        echo json_encode($data);
    }

    function uploadaccomplishments(){
        $data = array();
        $qry = false;
        $msg = '';

        if(isset($_FILES["accomplishments"])) {
            $new_name = $_FILES["accomplishments"]['name'];
            $dataid = $this->input->post('dataid');
            $data['dataid'] = $dataid;
            $file_directory = FCPATH . "uploads/employeeaccomp/".$dataid."/";

            // CREATE DIRECTORY
            $config['upload_path'] = $file_directory;
            $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|pdf|doc|docx';
            // $config['max_size'] = 10000;
            // $config['max_width'] = 50000;
            // $config['max_height'] = 80000;
            $config['encrypt_name'] = FALSE;
            $config['file_name'] = $new_name;
            $this->load->library('upload', $config);

            //Create Directory
            if (!is_dir($file_directory)) {
                mkdir($file_directory, 0777, TRUE);
                chmod($file_directory, 0777);
            } else {
                chmod($file_directory, 0777);
            }
            // ###############################################

            if (!$this->upload->do_upload('accomplishments')) {
                $msg = array('error' => $this->upload->display_errors());
            } else {
                $msg = array('upload_data' => $this->upload->data());
                $qry = true;

            }
        }else{
            $msg = 'Drop the file again!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;

        echo json_encode($data);

    }
    function getuploadedaccomplishments(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $files = glob('uploads/person/'.$dataid.'/accomplishments/*.{jpg,png,gif,docx,pdf}', GLOB_BRACE);

        foreach($files as $file) {
            $data['images'][] =  base_url().$file;
        }

        echo json_encode($data);
    }
    function deleteaccomp(){
        $data = array();
        $msg = '';
        $func  ='';
        $qry = false;
        $src = $this->input->post('src');
        $dataid = $this->input->post('dataid');
        $files = glob('uploads/person/'.$dataid.'/accomplishments/*.{jpg,png,gif,docx,pdf}', GLOB_BRACE);

        foreach($files as $file) {
            if(base_url().$file == $src){
                unlink(getcwd().'/'.$file);
                $qry = true;
            }
        }
        if($qry == true){
            $msg = 'File has been deleted';
            $func  ='success';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;

        echo json_encode($data);
    }
    /* function fetchempdeduct(){
      $data = array();
      $dataid = $this->input->post('dataid');

      $sql = $this->db->select("sss_employee,sss_loan,pagibig_employee,pagibig_loan,hmo_employee,withholding_tax,month,year,status")
          ->from("prime_employee_payroll_transactions")
          ->where(array("empid" => $dataid))
          ->get();

      if($sql->num_rows() > 0){
         $num = 1;
         foreach ($sql->result() as $row){
            $data['deductionsdata'][] = array(
                  "num" => $num++,
                  "withholdingtax" => $row->withholding_tax,
                  "yearmonth" => $row->year.'-'.$row->month,
                  "status" => $row->status
            );
         }
      }

      echo json_encode($data);
   }
   function fetchloan(){
      $data = array();
      $dataid = $this->input->post('dataid');
      $tabid = $this->input->post('tabid');
      $sql = '';

      if($tabid == 1){
          $sql = $this->db->select("sysid,sss_loan,month,year,status")->from("prime_employee_payroll_transactions")->where(array("empid" => $dataid))->get();

      }else if($tabid == 2){
          $sql = $this->db->select("sysid,pagibig_loan,month,year,status")->from("prime_employee_payroll_transactions")->where(array("empid" => $dataid))->get();

      }else if($tabid == 3){
          $sql = $this->db->select("sysid,union_loan,month,year,status")->from("prime_employee_payroll_transactions")->where(array("empid" => $dataid))->get();

      }else if($tabid == 4){
          $sql = $this->db->select("sysid,coop_loan,month,year,status")->from("prime_employee_payroll_transactions")->where(array("empid" => $dataid))->get();
      }
      if($sql->num_rows() > 0){
         $num = 1;
         foreach ($sql->result() as $row){
             if($tabid == 1){
                 $data['loansdata'][] = array(
                     'num'=> $num++,
                     'amount'=> $row->sss_loan,
                     'yearmonth'=>$row->year.'-'.$row->month,
                     'status'=>$row->status
                 );
             }else if($tabid == 2){
                 $data['loansdata'][] = array(
                     'num'=> $num++,
                     'amount'=> $row->pagibig_loan,
                     'yearmonth'=>$row->year.'-'.$row->month,
                     'status'=>$row->status
                 );
             }else if($tabid == 3){
                 $data['loansdata'][] = array(
                     'num'=> $num++,
                     'amount'=> $row->union_loan,
                     'yearmonth'=>$row->year.'-'.$row->month,
                     'status'=>$row->status
                 );
             }else if($tabid == 4){
                 $data['loansdata'][] = array(
                     'num'=> $num++,
                     'amount'=> $row->coop_loan,
                     'yearmonth'=>$row->year.'-'.$row->month,
                     'status'=>$row->status
                 );
             }

         }
      }
      echo json_encode($data);
   }*/
    function fetchholidays(){
        $data = array();

        $sql = $this->db->select("descs,from,to,datecreated")
            ->from("prime_main_holiday")
            ->get();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['holidaysdata'][] = array(
                    "num" => $num++,
                    "descs" => $row->descs,
                    "fromdate" => $row->from,
                    "todate" => $row->to,
                    "datecreated" => $row->datecreated
                );
            }
        }

        echo json_encode($data);
    }
    function addholidays(){

        $nameofholiday = $this->input->post('nameofholiday');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $msg = '';
        $func = '';
        $err = '';

        $this->db->trans_begin();
        $insarr = array(
            'descs' => $nameofholiday,
            'from' => $fromdate,
            'to' => $todate,
            'createdby' => user_id(),
            'status' => 1
        );
        $sql = $this->db->insert("prime_main_holiday" , $insarr);

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Holiday has been added';
            $func = 'success';
        }else{
            $this->db->rollback();
            $msg = 'Fail to add holiday';
            $func = 'error';
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function getpersoninfo() {
        $q = $this->input->post('term');

        $qry = $this->db->select('p.sysid, p.lastname, p.firstname, p.middlename, p.birthdate, p.gender, addrm.addrspec')
            ->from('person AS p')
            ->join('person_address_matrix AS addrm', 'addrm.personid = p.sysid', 'left')
            ->join('prime_employee_main AS e', 'e.personid = p.sysid')
            ->or_like('p.lastname', $q)
            ->or_like('p.middlename', $q)
            ->or_like('p.firstname', $q)
            //->where('addrm.status', 1)
            ->get();
        $res = array();
        if($qry->num_rows()>0) {
            foreach ($qry->result() as $row) {
                $birthday = strtotime($row->birthdate);
                $birthday = date("F d, Y", $birthday);
                $profile_pic_filename_last = '';
                if (file_exists(FCPATH . 'uploads/person/' . $row->sysid)) {
                    $check_primary_file = glob(FCPATH . 'uploads/person/' . $row->sysid . '/primary.*');
                    //usort($check_primary_file, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
                    array_multisort(
                        array_map('filemtime', $check_primary_file), SORT_NUMERIC, SORT_DESC, $check_primary_file
                    );
                    $i = 0;
                    $len = count($check_primary_file);
                    if ($check_primary_file) {
                        foreach ($check_primary_file as $row_pic) {
                            if ($i == 0) {
                                // first
                                $profile_pic_filename_first = $row_pic;
                            } else if ($i == $len - 1) {
                                // last
                                $profile_pic_filename_last = $row_pic;
                            }
                            $i++;
                        }
                        $profile_pic_exist = true;
                    } else {

                        $profile_pic_exist = false;
                    }
                } else {
                    $profile_pic_exist = false;
                }
                if ($profile_pic_exist == true) {
                    if ($profile_pic_filename_last) {
                        $pic_filename = $profile_pic_filename_last;
                    } else {
                        $pic_filename = $profile_pic_filename_first;
                    }
                    $pic = 'uploads/person/' . $row->sysid . '/' . basename($pic_filename);
                } else {
                    $pic = ($row->gender == 1) ? 'assets/global/img/default_avatar_male.png' : 'assets/global/img/default_avatar_female.png';
                }


                $res[] = array(
                    'id' => $row->sysid,
                    'text' => highlightkeyword($row->lastname, $q) . ', ' . highlightkeyword($row->firstname, $q) . ' ' . highlightkeyword($row->middlename, $q),
                    'birthday' => $birthday,
                    'gender' => gender_icon($row->gender),
                    'address' => $row->addrspec,
                    'pic' => $pic,
                );

            }
        }
        echo json_encode($res);
    }

    function addschedule(){
        $data = array();

        $employee = $this->input->post('employee');
        $workshift = $this->input->post('workshift');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $msg = '';
        $func = '';
        $qry = false;
        $title = 'Employee Scheduling';
        $data['employee'] = $employee;
        $this->db->trans_begin();

        $insarr = array(
            'empid' => $employee,
            'workshiftid' => $workshift,
            'schedstart' => $fromdate,
            'schedend' => $todate,
            'status' => 1
        );
        $this->db->insert("prime_employee_main_schedule_matrix" , $insarr);

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Schedule added';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to add schedule';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;
        echo json_encode($data);
    }
    function empschedlist(){
        $data = array();

        $sql = $this->db->select("pem.sysid,p.lastname , p.firstname")
            ->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][]  = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }

        echo json_encode($data);
    }
    function empschedworkshift(){
        $data = array();

        $sql = $this->db->select()
            ->from("prime_employee_main_workshift")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][]  = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.', '.$row->desc
                );
            }
        }

        echo json_encode($data);
    }
    function fetchtardinessdata($today = false, $qtype = 0, $payclass = 0, $ccid = false) {
        //sir lucky codes :)
        $data = array();
        if($today==false) {
            $today_input = $this->input->post('today');
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
                //$today = '2018-03-01';
            }
        }
        if($payclass>0) {
            $this->db->where('empc.payclass_id', $payclass);
        }
        if($ccid > 0) {
            $this->db->where('ec.ccid', $ccid);
        }
        $query = $this->db->select('emp.sysid, emp.empid, bio.bioid, mw.codes AS wcode, mw.desc AS wdesc, mw.logcnt, ec.ccid')
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS empname", false)
            ->from('prime_employee_main AS emp')
            ->join('prime_employee_main_payclass AS empc', 'empc.emp_id = emp.sysid')
            ->join('prime_employee_bioid AS bio', 'bio.empid = emp.sysid')
            ->join('prime_employee_main_workshift_matrix AS mwm', 'mwm.empid = emp.sysid AND mwm.status = 1', 'left')
            ->join('prime_employee_main_workshift AS mw', 'mw.sysid = mwm.workshift_id ', 'left')
            ->join('person AS p', 'p.sysid = emp.personid')
            ->join('prime_employee_costcenter AS ec', 'ec.empid = emp.sysid AND ec.status = 1', 'left')
            ->where(array('emp.status' => 1))
            ->group_by('emp.sysid, emp.empid, bio.bioid, mw.codes, mw.desc, mw.logcnt, ec.ccid')
            ->get();

        $num_rows = $query->num_rows();

        $present = false;

        if ($num_rows > 0) {
            foreach ($query->result() as $row) {

                $time_logs_arr = array();

                $qry_timelogs = $this->db->select()
                    ->from('prime_employee_attendance_timelogs')
                    ->where(array('logdate' => $today, 'bioid' => $row->bioid))->get();
                $timelog_num = $qry_timelogs->num_rows();
                $time_logs_details = '';
                if ($timelog_num > 0) {
                    foreach ($qry_timelogs->result() as $trow) {
                        $time_logs_arr[] = $trow->logtime;
                        $time_logs_details .= '<li>'.$trow->logtime.'</li>';
                    }


                    if($row->logcnt>2) {
                        $amin = ($timelog_num > 0) ? $time_logs_arr[0] : '';
                        $amout = ($timelog_num > 1) ? $time_logs_arr[1] : '';
                        $pmin = ($timelog_num > 2) ? $time_logs_arr[2] : '';
                        $pmout = ($timelog_num > 3) ? get_array_last($time_logs_arr) : '';
                    }else{
                        if($row->logcnt>0) {
                            $amin = ($timelog_num > 0) ? $time_logs_arr[0] : '';
                            $amout = '<code>N/A</code>';
                            $pmin = '<code>N/A</code>';
                            $pmout = ($timelog_num > 3) ? get_array_last($time_logs_arr) : '';
                        }else{
                            $amin = ($timelog_num > 0) ? $time_logs_arr[0] : '';
                            $amout = ($timelog_num > 1) ? $time_logs_arr[1] : '';
                            $pmin = ($timelog_num > 2) ? $time_logs_arr[2] : '';
                            $pmout = ($timelog_num > 3) ? get_array_last($time_logs_arr) : '';
                        }
                    }


                    $amlate = '0:00:00';
                    $pmlate = '0:00:00';

                    $row_late_pm = false;
                    $row_late_am = false;


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


                    $specifiedTime2 = '13:00';
                    $timeDifference2 = (strtotime($pmin) - strtotime($specifiedTime2) + 86400) % 86400;


                    if ($timeDifference >= 360) {
                        // proceed if less than 15 minutes has elapsed since specifiedTime
                        $diff = strtotime($amin) - strtotime($specifiedTime);
                        $hours = floor($diff / 3600);
                        $mins = floor($diff / 60 % 60);
                        $secs = floor($diff % 60);
                        $hrlate = $hours . ':' . $mins . ':' . $secs;
                        if ($hours >= 0) {
                            $amlate = $hrlate;
                            $row_late_am = true;
                        } else {
                            $amlate = '0:00:00';
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
                            $row_late_pm = true;
                        } else {
                            $pmlate = '0:00:00';
                        }
                    }

                    $info_popover = '<a class="btn inline btn-info btn-xs " title="" data-toggle="popover" 
                                        data-content="<ol style=\'width: 200px;\'>'.$time_logs_details.'</ol>"
                                        data-placement="left" data-trigger="hover" data-original-title="Time Logs"> 
                                        <i class="fa fa-print fa-fw"></i>
                                     </a>';



                    $status = '';
                    $control = '';
                    $today_leave = false;

                    $this->db->where( "'$today' BETWEEN lr.from AND lr.to", NULL, FALSE );
                    $qry_leave = $this->db->select("tp.names")
                        ->from('trn_employee_leave_requests AS lr')
                        ->join('prime_employee_main_leave_credits AS lc', 'lc.sysid = lr.creditid')
                        ->join('prime_types_parameter AS tp', 'tp.sysid = lc.types', 'left')
                        ->where(array('lr.empid' => $row->sysid, 'lr.status' => 301))
                        ->get()->row();

                    if($qry_leave) {
                        $today_leave = true;
                        $status .= get_types_label_format(310, $qry_leave->names);
                    }

                    $control .= '<a id="btn-edit" data-id="'.$row->sysid.'" class="btn inline btn-warning btn-xs "><i class="fa fa-pencil"></i></a>';
                    $control .= $info_popover;
                    $control .= '<a class="btn inline btn-primary btn-xs " target="_blank" href="'.base_url('module/f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59/view/'.$row->sysid).'"><i class="fa fa-search"></i></a>';

                    $status .= ($row_complete) ? '' : get_types_label_format(309);

                    $latetotal = sum_the_time($amlate, $pmlate);

                    if($qtype==1) {
                        $ins_arr = array(
                            'empid' => $row->sysid,
                            'days' => $today,
                            'totallate' => $latetotal,
                            'totallocator' => '0:00:00',
                            'totalot' => '0:00:00',
                            'types' => 1,
                        );

                        $qry_check = $this->db->select()->from('trn_employee_attendance')
                            ->where(array('empid' => $row->sysid, 'days' => $today))
                            ->get()->row();
                        if($qry_check==false) {
                            $this->db->insert('trn_employee_attendance', $ins_arr);
                            $data['insert'][] = $ins_arr;
                            $data['error'][] = $this->db->_error_message();
                        }
                    }

                    if($qtype==2) {
                        $ins_arr = array(
                            'empid' => $row->sysid,
                            'days' => $today,
                            'totallate' => $latetotal,
                            'totallocator' => '0:00:00',
                            'totalot' => '0:00:00',
                            'types' => 1,
                            'status' => 1,
                        );
                    }

                    if($qtype==0) {
                        if($latetotal == '00:00:00'){
                            $latetotal = '';
                        }else{
                            if($amlate == '0:00:00'){
                                $amlate = '';
                            }
                            if($pmlate  == '0:00:00'){
                                $pmlate = '';
                            }
                            $data['data'][] = array(
                                'expand' => btn_expand($row->sysid),
                                'empid' => $row->empid . '<span data-placement="right" title="'.$row->wdesc.'" class="pull-right label label-success tooltips">'.$row->wcode.'</span>',
                                'empname' => $row->empname,
                                'amlate' => $amlate,
                                'pmlate' => $pmlate,
                                'complete' => $row_complete,
                                'lateam' => $row_late_am,
                                'latepm' => $row_late_pm,
                                'latetotal' => $latetotal,
                            );
                        }
                    }
                }
            }
        }
        $data['date'] = $today;
        echo json_encode($data);
    }
    function changeuserstatus(){
        $data = array();
        $sysid = $this->input->post('sysid');
        $stat = $this->input->post("stat");

        $sql = '';
        $this->db->trans_begin();
        $this->db->where(array("sysid" => $sysid));
        if($stat == 1){
            $updatearr = array(
                "status" => 1,
                'updatedby' => user_id()
            );
            $sql = $this->db->update("prime_employee_main" , $updatearr);
        }else if($stat == 0){
            $updatearr = array(
                "status" => 0,
                'updatedby' => user_id(),
                'dateend' => date('Y-m-d')
            );
            $sql = $this->db->update("prime_employee_main" , $updatearr);

            //DEACTIVATE TO PAYROLL AND UPDATE DATE END
            $payrollarr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("empid" => $sysid , "status" => 1));
            $this->db->update("payroll_emplist" , $payrollarr);


        }

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            if($stat == 1){
                $msg = 'Employee has been activated';
            }else{
                $msg = 'Employee has been deactivated';
            }


            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Error updating employee.';
            $qry = false;
            $func = 'error';
        }

        if ($sql) {
            $status = ($stat == 0) ? 3229 : null;
            $ins_arr = array(
                'dataid' => $sysid,
                'remarks' => $msg,
                'statusid' => $status,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('prime_employee_main_history',$ins_arr);
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }

    function gettimeshift(){
        $data = array();
        $day_arr = array();
        $empid = $this->input->post('empid');

        $workshift = $this->db->select("pemw.weekend")->from("prime_employee_main_workshift_matrix as pemwm")
            ->join("prime_employee_main_workshift as pemw","pemwm.workshift_id = pemw.sysid","left")
            ->where(array("pemwm.empid" => $empid , "pemwm.status" => 1))->get()->row();
        if($workshift){
            if($workshift->weekend == 0){

                $day_arr = array(
                    '2' => 'MON',
                    '3' => 'TUE',
                    '4' => 'WED',
                    '5' => 'THU',
                    '6' => 'FRI'
                );
            }else if($workshift->weekend == 1){
                $day_arr = array(
                    '1' => 'SUN',
                    '2' => 'MON',
                    '3' => 'TUE',
                    '4' => 'WED',
                    '5' => 'THU',
                    '6' => 'FRI',
                    '7' => 'SAT'
                );
            }
            $data['weekend'] = $workshift->weekend;
        }




        $num = 1;
        $amtime = '';
        $pmtime = '';
        foreach ($day_arr as $key => $days){
            $sql = $this->db->select("pemw.am_start,pemw.am_end,pemw.pm_start,pemw.pm_end,pemw.codes,pemw.logcnt,pemw.logtype")
                ->from("prime_employee_time_shift_matrix as petsm")
                ->join("prime_employee_main_workshift as pemw","pemw.sysid = petsm.shiftid","left")
                ->join("prime_employee_main_workshift_matrix as pemwm","pemwm.workshift_id = pemw.sysid","left")
                ->where(array("petsm.days" => $key,"pemwm.empid" => $empid,"pemwm.status" => 1))
                ->get()->row();

            if($sql){
                if($sql->logcnt == 4){
                    $amtime = $sql->am_start.'-'.$sql->am_end;
                    $pmtime = $sql->pm_start.'-'.$sql->pm_end;
                }

                if($sql->logcnt == 2){
                    $amtime = $sql->am_start;
                    $pmtime = $sql->pm_end;
                }
            }else{
                $amtime = '';
                $pmtime = '';
            }

            $data['timeshiftdata'][] = array(
                "num" => $num++,
                "week" => $days,
                "amtime" =>  $amtime,
                "pmtime" =>  $pmtime,
                "status" =>  ($sql) ? $sql->codes : ''
            );
        }
        echo json_encode($data);
    }
    function getemployeecalendar(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $empid = $this->input->post('empid');
        $html = draw_employee_calendar($month,$year,$empid);

        $data['html'] = $html;
        echo json_encode($data);
    }
    function submitreqsched(){
        $data = array();




        $msg = '';
        $func = '';
        $qry = false;
        $empid = $this->input->post('empid');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $amtimein = $this->input->post('amtimein');
        $amtimeout = $this->input->post('amtimeout');
        $pmtimein = $this->input->post('pmtimein');
        $pmtimeout = $this->input->post('pmtimeout');
        $logscount = $this->input->post('logscount');
        $remarkstxt = $this->input->post('remarkstxt');
        $teamassign = $this->input->post('teamassign');
        $logtype = $this->input->post('logtype');
        $branch = $this->input->post('branch');

        $this->db->trans_begin();

        if(!empty($teamassign)){
            $this->db->where(array("empid" => $empid , "status" => 1));
            $this->db->update("prime_employee_team_assignments" , array("status" => 0));
            $data['error1'] = $this->db->_error_message();
            $teamarr = array(
                'teamid' => $teamassign,
                'empid' => $empid,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("prime_employee_team_assignments" , $teamarr);
            $data['teamid'] = $teamassign;
            $data['empid'] = $empid;
        }


        $insarr = array(
            'empid' => $empid,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'remarks' =>$remarkstxt,
            'createdby' => user_id()
        );

        $insreq = $this->db->insert("trn_schedule_requests",$insarr);

        $getsysid = $this->db->select("sysid")->from("trn_schedule_requests")->order_by("sysid","DESC")->limit(1)->get()->row();
        if($getsysid){
            $insreqtime = array(
                "schedid" => $getsysid->sysid,
                "logscnt" => $logscount,
                "logtype" => $logtype,
                "amtimein" => ($amtimein) ? date("H:i:s", strtotime($amtimein)) : '',
                "amtimeout" =>($amtimeout) ? date("H:i:s", strtotime($amtimeout)) : '',
                "pmtimein" =>($pmtimein) ? date("H:i:s", strtotime($pmtimein)) : '',
                "pmtimeout" => ($pmtimeout) ? date("H:i:s", strtotime($pmtimeout)) : '',
                "status" => 300,
                "branch" => $branch
            );

            $this->db->insert("trn_schedule_requests_time",$insreqtime);
        }else{
            $data['No Found'] = 'No request found.';
        }

        if($this->db->trans_status() == true && $insreq){

            $msg = 'Request schedule successfull';
            $func = 'success';
            $qry = true;
            $data['Found'] = 'Request found.';

            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed requesting schedule.';
            $func = 'error';
            $qry = false;
        }

        $data['msg']  = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function fetchempworkshft(){
        $data = array();
        $sql = $this->db->select("pem.empid ,p.firstname,p.lastname,p.middlename,pemw.desc")
            ->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_employee_main_workshift_matrix as pemwm" , "pemwm.empid = pem.sysid", "left")
            ->join("prime_employee_main_workshift as pemw","pemw.sysid = pemwm.workshift_id","left")
            ->where(array("pem.status" => 1,"pemwm.status" => 1))
            ->group_by("pem.empid ,p.firstname,p.lastname,p.middlename,pemw.desc")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['empworkshiftdata'][] = array(
                    'num' => $num++,
                    'empid' => $row->empid,
                    'name' => $row->lastname.', '.$row->firstname,
                    'workshift' => $row->desc,
                    'control' => ''
                );
            }
        }

        echo json_encode($data);
    }
    function fetchempschedworkshift(){
        $data = array();

        $sql = $this->db->select("tsr.sysid,pem.empid,p.firstname,p.lastname,tsr.day,tsr.month,tsr.year,tsrt.amtimein,tsrt.amtimeout,tsrt.pmtimein,tsrt.pmtimeout")
            ->from("trn_schedule_requests as tsr")
            ->join("prime_employee_main as pem","pem.sysid = tsr.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("trn_schedule_requests_time as tsrt","tsrt.schedid = tsr.sysid","left")
            ->where(array("tsr.status" => 300 , "tsrt.status" => 300))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['empscheddata'][] = array(
                    'num' => $num++,
                    'empid' => $row->empid,
                    'name'=> $row->lastname.', '.$row->firstname,
                    'date'=> $row->years.'-'.$row->months.'-'.$row->days,
                    'amin'=> $row->amtimein,
                    'amout'=> $row->amtimeout,
                    'pmin'=> $row->pmtimein,
                    'pmout'=> $row->pmtimeout,
                    'control'=> '
                                <div class="btn-group">
                                    <button data-id="'.$row->sysid.'" id="approveschedreq" class="btn btn-primary btn-xs"><i class="fa fa-check"></i> </button>
                                    <button data-id="'.$row->sysid.'" id="disapproveschedreq" class="btn btn-danger btn-xs pull-right"><i class="fa fa-times"></i> </button>
                                </div>
                                '
                );
            }
        }

        echo json_encode($data);
    }
    function approvesched(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;
        $title = '';

        $this->db->trans_begin();

        $this->db->set('a.status', 301);
        $this->db->set('b.status', 301);

        $this->db->where('a.sysid', $dataid);
        $this->db->where('a.sysid = b.schedid');
        $approvesched = $this->db->update('trn_schedule_requests as a, trn_schedule_requests_time as b');

        if($this->db->trans_status() == true && $approvesched){
            $this->db->trans_commit();
            $msg = 'Schedule has been approved';
            $func = 'success';
            $qry = true;
            $title = 'Approved';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to approved sched';
            $func = 'error';
            $qry = false;
            $title = 'Failed';
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;
        echo json_encode($data);
    }
    function disapprovesched(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();

        $this->db->set('a.status', 302);
        $this->db->set('b.status', 302);

        $this->db->where('a.sysid', $dataid);
        $this->db->where('a.sysid = b.schedid');
        $approvesched = $this->db->update('trn_schedule_requests as a, trn_schedule_requests_time as b');

        if($this->db->trans_status() == true && $approvesched){
            $this->db->trans_commit();
            $msg = '';
            $func = '';
            $qry = false;
        }else{
            $this->db->trans_rollback();
            $msg = '';
            $func = '';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function fetchworkshiftlist(){
        $data = array();

        $sql = $this->db->select("")->from("prime_employee_main_workshift")->get();
        if($sql->num_rows() > 0){
            $num = 1;

            foreach ($sql->result() as $row){
                $statbtn = '';
                if($row->status == 1){
                    $statbtn = '<span class="label label-sm label-success">Active</span>';
                }
                if($row->am_start != null && $row->am_start != null){
                    $amstart = date("g:i A", strtotime($row->am_start));
                }else{
                    $amstart = '';
                }
                if($row->am_end != null && $row->am_end != null){
                    $amend =  date("g:i A", strtotime($row->am_end));
                }else{
                    $amend = '';
                }
                if($row->pm_start != null && $row->pm_start != null){
                    $pmstart =  date("g:i A", strtotime($row->pm_start));
                }else{
                    $pmstart = '';
                }
                if($row->pm_end != null && $row->pm_end != null){
                    $pmend =  date("g:i A", strtotime($row->pm_end));
                }else{
                    $pmend = '';
                }


                $data['schedlist'][] = array(
                    "num" => $num++,
                    "codes" => $row->codes,
                    "desc" => $row->desc,
                    "logcnt"=> $row->logcnt,
                    "amstart"=> $amstart,
                    "amend"=> $amend,
                    "pmstart"=> $pmstart,
                    "pmend"=> $pmend,
                    "status"=> $statbtn,
                    "datecreated"=> $row->datecreated
                );
            }
        }

        echo json_encode($data);
    }
    function addworkshift(){
        $data = array();
        $logtype = 4;
        $logcount = 4;
        $amstartbol = true;
        $amendbol = true;
        $pmstartbol = true;
        $pmendbol = true;
        $this->db->trans_begin();
        $codes = $this->input->post('codes');
        $workshiftdesc = $this->input->post('workshiftdesc');


        $amstarthour = $this->input->post('amstarthour');
        $amstartminutes = $this->input->post('amstartminutes');
        $amstartampm = $this->input->post('amstartampm');

        $amendhour = $this->input->post('amendhour');
        $amendminutes = $this->input->post('amendminutes');
        $amendampm = $this->input->post('amendampm');

        $pmstarthour = $this->input->post('pmstarthour');
        $pmstartminutes = $this->input->post('pmstartminutes');
        $pmstartampm = $this->input->post('pmstartampm');

        $pmendhour = $this->input->post('pmendhour');
        $pmendminutes = $this->input->post('pmendminutes');
        $pmendampm = $this->input->post('pmendampm');


        $amstart =$amstarthour.':'.$amstartminutes.':00 '.$amstartampm;
        $amend =$amendhour.':'.$amendminutes.':00 '.$amendampm;
        $pmstart = $pmstarthour.':'.$pmstartminutes.':00 '.$pmstartampm;
        $pmend = $pmendhour.':'.$pmendminutes.':00 '.$pmendampm;

        if($amstarthour == '' || $amstartminutes == ''){
            $amstart = null;
            $logcount--;
            $amstartbol = false;
        }
        if($amendhour == '' || $amendminutes == ''){
            $amend = null;
            $logcount--;
            $amendbol = false;
        }
        if($pmstarthour == '' || $pmstartminutes == ''){
            $pmstart = null;
            $logcount--;
            $pmstartbol = false;
        }
        if($pmendhour == '' || $pmendminutes == ''){
            $pmend = null;
            $logcount--;
            $pmendbol = false;
        }
        if($amstartbol == true && $amendbol == true && $pmstartbol == true && $pmendbol == true){
            $logtype = 4;
        }else if($amstartbol == true && $amendbol == true && $pmstartbol == false && $pmendbol == false){
            $logtype = 1;
        }else if($amstartbol == false && $amendbol == false && $pmstartbol == true && $pmendbol == true){
            $logtype = 2;
        }else if($amstartbol == true && $amendbol == false && $pmstartbol == false && $pmendbol == true){
            $logtype = 0;
        }
        $data['amstartbol'] = $amstartbol;
        $data['amendbol'] = $amendbol;
        $data['pmstartbol'] = $pmstartbol;
        $data['pmendbol'] = $pmendbol;

        $logcnt = $logcount;


        $amstart24 = null;
        $amend24 = null;
        $pmstart24 = null;
        $pmend24 = null;

        if($amstart != null){
            $amstart24 = date("H:i", strtotime($amstart));
        }
        if($amend != null){
            $amend24 = date("H:i", strtotime($amend));
        }

        if($pmstart != null){
            $pmstart24 = date("H:i", strtotime($pmstart));
        }

        if($pmend != null){
            $pmend24 = date("H:i", strtotime($pmend));
        }


        $data['amstart'] = $amstart;
        $data['amend'] = $amend;
        $data['pmstart'] = $pmstart;
        $data['pmend'] = $pmend;

        $data['amstart24'] = $amstart24;
        $data['amend24'] = $amend24;
        $data['pmstart24'] = $pmstart24;
        $data['pmend24'] = $pmend24;

        $getlastsysid = $this->db->select("sysid")->from("prime_employee_main_workshift")
            ->order_by("sysid" , "desc")->limit(1)->get()->row();

        $insarr = array(
            'desc' =>$workshiftdesc,
            'codes' => ($getlastsysid) ? str_pad($getlastsysid->sysid + 1,6,"0" , STR_PAD_LEFT) : 0,
            'logcnt' => $logcnt,
            'logtype' => $logtype,
            'am_start' => $amstart24,
            'am_end' =>$amend24 ,
            'pm_start' =>$pmstart24,
            'pm_end' => $pmend24
        );

        $sql = $this->db->insert("prime_employee_main_workshift" , $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();

            $getshiftid = $this->db->select("sysid")->from("prime_employee_main_workshift")
                ->order_by("sysid","DESC")->limit(1)->get()->row();
            if($getshiftid){
                $i = 1;
                $days = 1;
                for($i = 1;$i <= 7; $i++){
                    $timeshiftarr = array(
                        'shiftid' => $getshiftid->sysid,
                        'days' => $days++,
                        'amtimein' => $amstart24,
                        'amtimeout' => $amend24,
                        'pmtimein' => $pmstart24,
                        'pmtimeout' => $pmend24,
                    );
                    $this->db->insert("prime_employee_time_shift_matrix" , $timeshiftarr);
                }
            }

            $msg = 'Workshift Added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add workshift';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function fetchtimeshift(){
        $data = array();

        $sql = $this->db->select("petsm.days,petsm.amtimein,petsm.amtimeout,petsm.pmtimein,petsm.pmtimeout,pemw.codes")
            ->from("prime_employee_time_shift_matrix as petsm")
            ->join("prime_employee_main_workshift as pemw","pemw.sysid  = petsm.shiftid" , "left")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['timeshiftdata'][] = array(
                    "num" => $num++,
                    "shiftid" => $row->codes,
                    "days" => $this->getdayname($row->days),
                    "amtimein" => $row->amtimein,
                    "amtimeout" => $row->amtimeout,
                    "pmtimein" => $row->pmtimein,
                    "pmtimeout" => $row->pmtimeout,
                );
            }
        }

        echo json_encode($data);
    }
    function getdayname($data){
        $dayname = '';
        if($data == 2) {
            $dayname = 'MON';
        }else if($data == 3){
            $dayname = 'TUE';
        }else if($data == 4){
            $dayname = 'WED';
        }else if($data == 5){
            $dayname = 'THU';
        }else if($data == 6){
            $dayname = 'FRI';
        }
        return $dayname;
    }

    function generateattendance($today = false, $qtype = 0, $payclass = 0, $ccid = false) {

        $data = array();

        $paytype = $this->input->post('paytype');

        $logcnt = '';
        $logtype = '';
        $codes = '';
        $desc = '';

        $status = '';
        $workshift = 0;
        $row_complete = false;

        $amin = '00:00:00';
        $amout = '00:00:00';
        $pmin = '00:00:00';
        $pmout = '00:00:00';
        $amabsent = false;
        $pmabsent = false;

        $amlate = '00:00:00';
        $pmlate = '00:00:00';


        if($today==false) {
            $today_input = $this->input->post('today');
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
                //$today = '2018-03-01';
            }
        }

        if($payclass>0) {
            $this->db->where('empc.payclass_id', $payclass);
        }
        if($ccid > 0) {
            $this->db->where('ec.ccid', $ccid);
        }
        $data['today'] = $today;
        $data['payclass'] = $payclass;
        $data['ccid'] = $ccid;
        $query = $this->db->select('emp.sysid,empc.payclass_id ,emp.empid, bio.bioid, mw.codes AS wcode, mw.desc AS wdesc, mw.logcnt, ec.ccid,mw.logtype,pes.amt,mw.sysid as workshiftid')
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS empname", false)
            ->from('prime_employee_main AS emp')
            ->join('prime_employee_main_payclass AS empc', 'empc.emp_id = emp.sysid')
            ->join('prime_employee_bioid AS bio', 'bio.empid = emp.sysid')
            ->join('prime_employee_main_workshift_matrix AS mwm', 'mwm.empid = emp.sysid AND mwm.status = 1', 'left')
            ->join('prime_employee_main_workshift AS mw', 'mw.sysid = mwm.workshift_id ', 'left')
            ->join('person AS p', 'p.sysid = emp.personid')
            ->join('prime_employee_costcenter AS ec', 'ec.empid = emp.sysid AND ec.status = 1', 'left')
            ->join("prime_employee_salary as pes","pes.empid = emp.sysid","left")
            ->where(array('emp.status' => 1 ,"emp.type !=" => 2 , "pes.status" => 1 , "mwm.status" => 1 , "ec.type" => 1 ,"bio.status" => 1))
            ->group_by('emp.sysid, empc.payclass_id,emp.empid, bio.bioid, mw.codes, mw.desc, mw.logcnt, ec.ccid, mw.logtype, pes.amt,mw.sysid')
            ->order_by('p.lastname','asc')
            ->get();
        $data['queryerror'] = $this->db->_error_message();
        $num_rows = $query->num_rows();

        if ($num_rows > 0) {
            $presentid = array();
            foreach ($query->result() as $row) {

                $time_logs_arr = array();
                $qry_timelogs = $this->db->select()
                    ->from('prime_employee_attendance_timelogs')
                    ->where(array('logdate' => $today, 'bioid' => $row->bioid))->get();
                $timelog_num = $qry_timelogs->num_rows();
                $time_logs_details = '';
                if ($timelog_num > 0) {
                    $presentid[] = $row->sysid;
                    $amtime = array();
                    $pmtime = array();

                    $dateval = explode('-',$today);
                    $year = $dateval[0];
                    $month = $dateval[1];
                    $day = $dateval[2];

                    $sched = checkempsched($row->sysid, $day, $month , $year , 301)->sched;
                    $specifiedTime = checkempsched($row->sysid, $day, $month , $year , 301)->sepcfiedTime;
                    $specifiedTime2 = checkempsched($row->sysid, $day, $month , $year , 301)->sepcfiedTime2;
                    if($sched == true){
                        $codes = checkempsched($row->sysid, $day, $month , $year , 301)->codes;
                        $logcnt = checkempsched($row->sysid, $day, $month , $year , 301)->logcount;
                        $logtype = checkempsched($row->sysid, $day, $month , $year , 301)->logtype;
                        $desc = checkempsched($row->sysid, $day, $month , $year , 301)->desc;
                        $workshift = checkempsched($row->sysid, $day, $month , $year , 301)->workshift;
                    }


                    foreach ($qry_timelogs->result() as $trow) {

                        $time_logs_arr[] = $trow->logtime;
                        $time_logs_details .= '<li>'.$trow->logtime.'</li>';
                        $timearr = substr($trow->logtime , 0 , 2);

                        if($row->logcnt == 4 && $row->logtype == 4){
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
                                $amin = '00:00:00';
                                $amout = '00:00:00';
                                $amabsent = true;
                            }else{
                                $amabsent = false;
                                $amin = isset($amtime[0]) ? $amtime[0] : '00:00:00';
                                $amout = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';
                            }
                            //PM
                            if($pmcount == 1){
                                $pmin = '00:00:00';
                                $pmout = '00:00:00';
                                $pmabsent = true;
                            }else{
                                $pmabsent = false;
                                if($amabsent == true){
                                    $pmin = isset($pmtime[0]) ? $pmtime[0] : '00:00:00';
                                }else{
                                    $pmin = isset($pmtime[1]) ? $pmtime[1] : '00:00:00';
                                }
                                $lastlog = get_array_last($time_logs_arr);
                                $lastlogfirst = substr($lastlog , 0 , 2);
                                if($pmcount == 1 && $lastlogfirst > 16){
                                    $pmin = '00:00:00';
                                    $pmout = $lastlog;
                                }else{
                                    if($lastlogfirst > 15){
                                        $pmout = isset($lastlog) ? $lastlog : '00:00:00';
                                    }else{
                                        $pmout = '00:00:00';
                                    }
                                }
                            }

                        }else if($row->logcnt == 2) {
                            $amin = '00:00:00';
                            $amout = '00:00:00';
                            $amlate = '00:00:00';
                            $pmin = '00:00:00';
                            $pmout = '00:00:00';
                            $pmlate = '00:00:00';
                            if($row->logtype == 0){
                                //REGULAR OFFICE HOUR
                                $amin  = $time_logs_arr[0];
                                $amout = '';
                                $pmin = '';
                                if(count($time_logs_arr) > 1){
                                    $pmout = get_array_last($time_logs_arr);
                                }else{
                                    $pmout = '00:00:00';
                                }


                            }else if($row->logtype == 1){
                                $amin  = $time_logs_arr[0];
                                $amout = '00:00:00';
                                $pmin = '00:00:00';
                                if(count($time_logs_arr) > 1){
                                    $pmout = get_array_last($time_logs_arr);
                                }else{
                                    $pmout = '00:00:00';
                                }

                            }else if($row->logtype == 2){
                                $amin  = $time_logs_arr[0];

                                $aminexplode = explode(':' , $amin);
                                if($aminexplode[0] > 13){
                                    $amin  = $time_logs_arr[0];
                                }else{
                                    $amin = '00:00:00';
                                }

                                $amout = '00:00:00';
                                $pmin = '00:00:00';

                                $data_arr = explode('-', $today);
                                $yearindex = $data_arr[0];
                                $monthindex = $data_arr[1];
                                $dayindex = $data_arr[2] + 1;
                                $newdate = $yearindex.'-'.$monthindex.'-'.$dayindex;

                                $getpmoutonnewdate = $this->db->select("logtime")
                                    ->from("prime_employee_attendance_timelogs")
                                    ->where(array("logdate" => $newdate,"bioid"=>$row->bioid))->order_by("sysid")->limit(1)->get()->row();
                                if($getpmoutonnewdate){
                                    $newpmout = $getpmoutonnewdate->logtime;

                                    $timeexplode = explode(':', $newpmout);
                                    if($timeexplode[0] < 13){
                                        $pmout = $newpmout;
                                    }else{
                                        $pmout = '00:00:00';
                                    }
                                }else{
                                    $pmout = '00:00:00';
                                }

                            }else if($row->logtype == 3){
                                $amin  = $time_logs_arr[0];
                                $amout = '00:00:00';
                                $pmin = '00:00:00';
                                //+ 1 date
                                $data_arr = explode('-', $today);
                                $yearindex = $data_arr[0];
                                $monthindex = $data_arr[1];
                                $dayindex = $data_arr[2] + 1;
                                $newdate = $yearindex.'-'.$monthindex.'-'.$dayindex;

                                $getpmoutonnewdate = $this->db->select("logtime")
                                    ->from("prime_employee_attendance_timelogs")
                                    ->where(array("logdate" => $newdate,"bioid"=>$row->bioid))->order_by("sysid")->limit(1)->get()->row();
                                if($getpmoutonnewdate){
                                    $newpmout = $getpmoutonnewdate->logtime;

                                    $timeexplode = explode(':', $newpmout);
                                    if($timeexplode[0] < 18){
                                        $pmout = $newpmout;
                                    }else{
                                        $pmout = '00:00:00';
                                    }
                                }else{
                                    $pmout = '00:00:00';
                                }
                            }
                        }
                    }
                    //check for forgotten timelogs
                    $checkforamin = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("logtype" => 1021,"status" => 301,"logdate"=> $today,"bioid"=> $row->bioid))
                        ->get()->row();
                    ($checkforamin) ? $amin = $checkforamin->logtime : '';

                    $checkforamout = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("logtype" => 1022,"status" => 301,"logdate"=> $today,"bioid"=> $row->bioid))
                        ->get()->row();
                    ($checkforamout) ? $amout = $checkforamout->logtime : '';

                    $checkforpmin = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("logtype" => 1023,"status" => 301,"logdate"=> $today,"bioid"=> $row->bioid))
                        ->get()->row();
                    ($checkforpmin) ? $pmin = $checkforpmin->logtime : '';

                    $checkforpmout = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("logtype" => 1024,"status" => 301,"logdate"=> $today,"bioid"=> $row->bioid))
                        ->get()->row();
                    ($checkforpmout) ? $pmout = $checkforpmout->logtime : '';
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

                    if ($timeDifference >= 360) {
                        // proceed if less than 15 minutes has elapsed since specifiedTime
                        $diff = strtotime($amin) - strtotime($specifiedTime);
                        $hours = floor($diff / 3600);
                        $mins = floor($diff / 60 % 60);
                        $secs = floor($diff % 60);
                        $hrlate = $hours . ':' . $mins . ':' . $secs;
                        if ($hours >= 0) {
                            $amlate = $hrlate;
                            $row_late_am = true;
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
                            $row_late_pm = true;
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

                    if($row->sysid == 142){
                        $data['specifiedtime'] = $specifiedTime;
                        $data['amin'] = $amin;
                    }

                    $getlocatorout = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("status" => 301 , "logtype" => 1030 , "bioid" => $row->bioid
                        ,"logdate" => $today))->get()->row();
                    $locatorout = ($getlocatorout) ? $getlocatorout->logtime : '';

                    $getlocatorin = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("status" => 301 , "logtype" => 1029 , "bioid" => $row->bioid
                        ,"logdate" => $today))->get()->row();
                    $locatorin = ($getlocatorin) ? $getlocatorin->logtime : '';

                    $getotin = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("status" => 301 , "logtype" => 1031 , "bioid" => $row->bioid
                        ,"logdate" => $today))->get()->row();
                    $otin = ($getotin) ? $getotin->logtime : '';

                    $getotout = $this->db->select("logtime")->from("trn_attendance_timelogs")
                        ->where(array("status" => 301 , "logtype" => 1032 , "bioid" => $row->bioid
                        ,"logdate" => $today))->get()->row();
                    $otout = ($getotout) ? $getotout->logtime : '';

                    //get totallate
                    $info_popover = '<a class="btn inline btn-info btn-xs " title="" data-toggle="popover" 
                                        data-content="<ol style=\'width: 200px;\'>'.$time_logs_details.'</ol>"
                                        data-placement="left" data-trigger="hover" data-original-title="Time Logs"> 
                                        <i class="fa fa-print fa-fw"></i>
                                     </a>';

                    $latetotal = sum_the_time($amlate, $pmlate);
                    $control = '';
                    $control .= '<a href="#form_edit_attendance" id="editattlogsbtn" data-toggle="ajax-modal" data-arr="'.$today.'" data-view="'.$row->bioid.'" class="btn inline btn-warning btn-xs "><i class="fa fa-pencil"></i></a>';
                    $control .= $info_popover;
                    $control .= '<a class="btn inline btn-primary btn-xs " target="_blank" href="'.base_url('module/f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59/view/'.$row->sysid).'"><i class="fa fa-search"></i></a>';

                    $status .= ($row_complete) ? get_types_label_format(308) : get_types_label_format(309);

                    $minutesperday = 8 * 60;
                    $dailyrate = ($row->amt * 12) / 261;
                    $totallateinminutes = converttimetominutes($latetotal);
                    $charge = ($dailyrate * $totallateinminutes) / $minutesperday;

                    if($qtype==0) {
                        $shiftcode = ($sched) ? $codes : $row->wcode;
                        $logcnt = ($sched) ? $logcnt : $row->logcnt;
                        $logtype = ($sched) ? $logtype : $row->logtype;
                        $desc = ($sched) ? $desc : $row->wdesc;
                        $workshiftid = ($sched) ? $workshift : $row->workshiftid;
                        $data['data'][] = array(
                            'expand' => btn_expand($row->sysid),
                            'empid' => $row->empid . '<span data-placement="right" title="'.$desc.'" class="pull-right label label-success tooltips">'.$shiftcode.'</span>'.' - '.$logcnt.'-'.$logtype,
                            'empname' => $row->empname,
                            'time' => $row->bioid,
                            'amin' =>  $amin,
                            'amout' => $amout,
                            'amlate' => $amlate,
                            'pmin' => $pmin,
                            'pmout' => $pmout,
                            'pmlate' => $pmlate,
                            'otin' => $otin,
                            'otout' => $otout,
                            'locatorout' => $locatorout,
                            'locatorin' => $locatorin,
                            'complete' => '',
                            'lateam' => '',
                            'latepm' => '',
                            'totalot' => '',
                            'totallocator' => '',
                            'latetotal' =>   $latetotal,
                            'status' => '',
                            'control' => $control,
                        );
                        if($logcnt == 2){
                            $amout = '00:00:00';
                            $pmin = '00:00:00';
                        }
                        $reportarr = array(
                            'empid' => $row->sysid,
                            'month' => $month,
                            'year' => $year,
                            'attdate' => $today,
                            'paytype' => $paytype,
                            'workshift' => $workshiftid,
                            'amin' => $amin,
                            'amout' => $amout,
                            'amlate' => $amlate,
                            'pmin' => $pmin,
                            'pmout' => $pmout,
                            'pmlate' => $pmlate,
                            'totallate' => $latetotal,
                            'charge' => $charge,
                            'paid' => 0,
                            'status' => 1,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                            'logcount' => $logcnt,
                            'payclass' => $row->payclass_id
                        );
                        $this->db->insert("attendance_reports",  $reportarr);
                        $data['error1'] = $this->db->_error_message();
                        $amlate = '00:00:00';
                        $pmlate = '00:00:00';
                    }
                }
            }
            $getallabsentemployee = $this->db->select("pem.sysid")->from("prime_employee_main as pem")
                ->where(array("pem.status" => 1 , "pem.type" => 1))
                ->where_not_in('pem.sysid', $presentid)
                ->get();
            $data['errorgetabsent'] = $this->db->_error_message();
            if($getallabsentemployee->num_rows() > 0){
                foreach ($getallabsentemployee->result() as $absentemployees){

                    $checkforleave = $this->db->query("SELECT telr.sysid FROM trn_employee_leave_requests as telr
LEFT JOIN trn_employee_leave_requests_approval as telra ON telr.sysid = telra.trnreqid
WHERE telr.status = 301 AND telra.status = 301 AND empid = ".$absentemployees->sysid." AND ('".$today."' BETWEEN telr.`from` AND telr.`to` OR '".$today."' BETWEEN telr.`from` AND telr.`to`)")->row();

                    if(!$checkforleave){
                        $absentinsarr = array(
                            'empid' => $absentemployees->sysid,
                            'typeofabsent' => 0,
                            'date_absent' => $today,
                            'status' => 1,
                            'createdby' => user_id(),
                            'updatedby' => user_id()
                        );
                        $this->db->insert("absent_reports" , $absentinsarr);
                    }
                }
            }

        }

        $data['date'] = $today;
        echo json_encode($data);
    }

    function clearleaverequests() {
        $data = array();
        $msg = '';
        $func = 'error';
        $err = array();
        $this->db->query("TRUNCATE TABLE trn_employee_leave_requests");
        $this->db->query("TRUNCATE TABLE trn_employee_leave_requests_approval");

        $err['TRUNC'][] = $this->db->_error_message();
        $flowid = 9;

        $qry_flows = $this->db->select()->from('transaction_request_main')
            ->where(array('flowid' => $flowid))->get();
        if($qry_flows->num_rows()>0) {
            foreach($qry_flows->result() as $row)
            {

                $data['trnid'][] = $row->sysid;
                $qry_flow_stages = $this->db->select()
                    ->from('transaction_request_main_trails')
                    ->where('trnid', $row->sysid)
                    ->get()->row();
                if($qry_flow_stages) {
                    $stage_id = $qry_flow_stages->sysid;
                    $data['TRAILID'][] = $stage_id;
                    $this->db->where('trailid', $stage_id);
                    $this->db->delete('transaction_request_trails_logs');
                    $err['LOGS'][] = $this->db->_error_message();

                }

                $this->db->where('trnid', $row->sysid);
                $this->db->delete('transaction_request_main_trails');
                $err['TRAILS'][] = $this->db->_error_message();

            }
            $this->db->where('flowid', $flowid);
            $this->db->delete('transaction_request_main');
            $err['MAIN'][] = $this->db->_error_message();
        }
        $this->db->trans_commit();
        $msg = 'Leave Requests has been cleared!';
        $func = 'success!';

        $data['err'] = $err;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }


    function gettimelogsformodify(){
        $data = array();
        $userid = $this->input->post('userid');
        $todate = $this->input->post('todate');
        $timetype = $this->input->post('timetype');

        $getlogtime = $this->db->select("pemwm.workshift_id,pemw.am_start,pemw.am_end,pemw.pm_start,pemw.pm_end,peat.logdate,peat.logtime,pemw.logcnt,pemw.logtype")
            ->from("prime_employee_main as pem")
            ->join("prime_employee_main_workshift_matrix as pemwm","pemwm.empid = pem.sysid","left")
            ->join("prime_employee_main_workshift as pemw","pemw.sysid = pemwm.workshift_id","left")
            ->join("prime_employee_bioid as peb","peb.empid = pem.sysid" , "left")
            ->join("prime_employee_attendance_timelogs as peat" ,"peat.bioid = peb.bioid","left")
            ->where(array("pemwm.empid" => $userid , "pemwm.status" => 1,"peat.logdate" => $todate))
            ->get()->result();


        if($getlogtime){
            if($timetype == 0){
                $data['logtime'] =(isset($getlogtime[0])) ? $getlogtime[0]->logtime : null;
            }else if($timetype == 1){
                if($getlogtime[0]->logcnt == 4){
                    $data['logtime'] =(isset($getlogtime[1])) ? $getlogtime[1]->logtime : null;
                }else{
                    if($getlogtime[0]->logtype == 0){
                        $data['logtime'] = '00:00:00';
                    }else if($getlogtime[0]->logtype == 1){
                        $data['logtime'] =(isset($getlogtime[1])) ? $getlogtime[1]->logtime : null;
                    }else if($getlogtime[0]->logtype == 2){
                        $data['logtime'] = '00:00:00';
                    }
                }
            }else if($timetype == 2){
                if($getlogtime[0]->logcnt == 4){
                    $data['logtime'] =(isset($getlogtime[2])) ? $getlogtime[2]->logtime : null;
                }else{
                    if($getlogtime[0]->logtype == 0){
                        $data['logtime'] = '00:00:00';
                    }else if($getlogtime[0]->logtype == 1){
                        $data['logtime'] = '00:00:00';
                    }else if($getlogtime[0]->logtype == 2){
                        $data['logtime'] =(isset($getlogtime[0])) ? $getlogtime[0]->logtime : null;
                    }
                }
            }else if($timetype == 3){
                if($getlogtime[0]->logcnt == 4){
                    $data['logtime'] =(isset($getlogtime[3])) ? $getlogtime[3]->logtime : null;
                }else{
                    if($getlogtime[0]->logtype == 0){
                        $data['logtime'] =(isset($getlogtime[1])) ? $getlogtime[1]->logtime : null;
                    }else if($getlogtime[0]->logtype == 1){
                        $data['logtime'] =(isset($getlogtime[1])) ? $getlogtime[1]->logtime : null;
                    }else if($getlogtime[0]->logtype == 2){
                        $data['logtime'] =(isset($getlogtime[1])) ? $getlogtime[1]->logtime : null;
                    }
                }
            }else{
                $data['logtime'] = '00:00:00';
            }
        }else{
            $data['logtime'] = '00:00:00';
        }
        echo json_encode($data);
    }
    function modifyattendance(){
        $data = array();

        $userid = $this->input->post('userid');
        $today = $this->input->post('today');
        $timetype = $this->input->post('timetype');
        $oldtimelog = $this->input->post('oldtimelog');
        $newtimelog = date("H:i", strtotime($this->input->post('newtimelog')));
        $reason = $this->input->post('reason');
        $insarr = array(
            'empid' => $userid,
            'attdate' => $today,
            'type' => $timetype,
            'oldlogs' => $oldtimelog,
            'newlogs' => $newtimelog,
            'reason' => $reason,
            'status' => 300
        );
        $this->db->insert("trn_timelogs_manual"  , $insarr);
        echo json_encode($data);
    }
    function fetchrequestattendance(){
        $data = array();

        $sql = $this->db->select("tat.sysid,tat.bioid,tat.logdate,ptp.names,tat.logtype,tat.logtime,tat.remarks,tat.status,p.lastname,p.firstname,pem.empid")
            ->from("trn_attendance_timelogs as tat")
            ->join("prime_employee_bioid as peb","peb.bioid = tat.bioid","left")
            ->join("prime_employee_main as pem","pem.sysid = peb.empid","left")
            ->join("person as p","p.sysid = pem.personid" , "left")
            ->join("prime_types_parameter as ptp","ptp.sysid = tat.logtype" , "left")
            ->where(array("tat.status" => 300))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            $stat = '';
            foreach ($sql->result() as $row){
                if($row->status == 300){
                    $stat = 'Pending';
                }
                $data['requestlogsdata'][] = array(
                    "num" => $num++,
                    "empid" => $row->empid,
                    "name" => $row->lastname.', '.$row->firstname,
                    "attdate" => $row->logdate,
                    "type" => $row->names,
                    "timelogs" => $row->logtime,
                    "reason" => $row->remarks,
                    "status" =>$stat,
                    "control" => '<div class="btn-group">
                    <button class="btn btn-primary btn-xs" data-id="'.$row->sysid.'" id="approveattbtn"><i class="fa fa-check" ></i></button>
                    <button class="btn btn-danger btn-xs" data-id="'.$row->sysid.'" id="disapproveattbtn"><i class="fa fa-times" ></i></button>
                    </div>'
                );
            }
        }

        echo json_encode($data);
    }
    function gettimetype($data){
        $label = '';
        if($data == 0){
            $label = 'AM IN';
        }else if($data == 1){
            $label = 'AM OUT';
        }else if($data == 2){
            $label = 'PM IN';
        }else if($data == 3){
            $label = 'PM OUT';
        }
        return $label;
    }
    function approvetimelogsrequest(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        $this->db->where(array("sysid" => $dataid));
        $updatearr = array(
            'status' => 301
        );
        $sql = $this->db->update("trn_timelogs_manual",$updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Timelog Request has been approved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to approve request.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function disapprovetimelogsrequest(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        $this->db->where(array("sysid" => $dataid));
        $updatearr = array(
            'status' => 302
        );
        $sql = $this->db->update("trn_timelogs_manual",$updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Timelog Request has been disapproved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to disapprove request.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    /* function getemploymenthistory(){
       $data = array();

       $empid = $this->input->post('empid');

       $sql = $this->db->select("position,company,year")->from("prime_employee_employment_history")->where(array("empid"=>$empid))->get();
       if($sql->num_rows() > 0){
           $num = 1;
           foreach ($sql->result() as $row){
               $data['employmenthistorydata'][] = array(
                    "num" => $num++,
                    "position" => $row->position,
                    "company" => $row->company,
                    "year" => $row->year
               );
           }
       }

       echo json_encode($data);
    } */
    function getemployeecont(){
        $data = array();
        $empid = $this->input->post('empid');
        $tabid = $this->input->post('tabid');
        $type = 0;
        $type = $tabid;
        $totalpaidamount = 0;
        $totalunpaidamount = 0;

        $getbalance = $this->db->select("pmtb.amount,pmtb.status")->from("payroll_manual_transactions_breakdown as pmtb")
            ->join("payroll_manual_transactions as pmt","pmt.sysid = pmtb.groupid && pmt.empid = pmtb.empid","left")
            ->where(array("pmtb.empid" => $empid,"pmt.tsysid" => $tabid))->get();
        if($getbalance){
            foreach ($getbalance->result() as $row){
                if($row->status == 312){
                    $totalpaidamount += $row->amount;
                }else if($row->status == 313){
                    $totalunpaidamount += $row->amount;
                }
            }
        }

        $sql = $this->db->select("sysid,empid,tsysid,amount,monthdevide,amountpermonth,status,datecreated")
            ->from("payroll_manual_transactions")->where(array("empid" => $empid , "tsysid" => $tabid,"status !=" => 0))
            ->get();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                if($row->monthdevide == 1){
                    $monthtext = ' Month';
                }else{
                    $monthtext = ' Months';
                }

                $data['employeecontdata'][] = array(
                    "expand" => $row->sysid,
                    "amount" => number_format($row->amount , 2),
                    "for" => $row->monthdevide.' '.$monthtext,
                    "amtpermonth" => number_format($row->amountpermonth , 2),
                    "datecreated" => $row->datecreated,
                    "control" =>'<button class="btn btn-xs btn-danger" data-empid="'.$row->empid.'" data-id="'.$row->sysid.'" id="deletecontributionbtn"><i class="fa fa-trash"></i></button>'
                );
            }
        }
        $data['totalpaidamount'] = $totalpaidamount;
        $data['totalunpaidamount'] = $totalunpaidamount;
        echo json_encode($data);
    }
    function getemployeeloans(){
        $data = array();
        $empid = $this->input->post('empid');
        $tabid = $this->input->post('tabid');
        $type = 0;
        $stat = '';

        if($tabid == 1){
            $type = 257;
        }else if($tabid == 2){
            $type = 258;
        }else if($tabid == 3){
            $type = 254;
        }else if($tabid == 4){
            $type = 255;
        }

        $sql = $this->db->select("amount,month,year,status")->from("payroll_transactions_manual")
            ->where(array("empid" => $empid , "transaction_item_type" => $type))->get();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                if($row->status == 1000){
                    $stat = 'PAID';
                }else if($row->status == 1001){
                    $stat = 'UNPAID';
                }
                $data['employeeloansdata'][] = array(
                    "num" => $num++,
                    "amount" => $row->amount,
                    "month" => $row->month,
                    "year" => $row->year,
                    "status" => $stat
                );
            }
        }
        echo json_encode($data);
    }
    function getpremiummonth(){
        $data = array();
        $premiumsmonth = array(
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        );
        foreach ($premiumsmonth as $key => $value){
            $data['list'][] = array(
                'id' => $key,
                'text' => $value
            );
        }
        echo json_encode($data);
    }
    function getpayrollmatrix(){
        $data = array();
        $sql = $this->db->select()->from("payroll_matrix")->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['payrollmatrixdata'][] = array(
                    "num" => $num++,
                    "codes" => $row->codes,
                    "types"=> $this->gettypename($row->typesid),
                    "functions"=> $row->functions,
                    "effects"=> $row->effects,
                    "notax"=> $row->notax,
                    "capping"=> $row->capping
                );
            }
        }
        echo json_encode($data);
    }
    function gettypename($id){
        $sql = $this->db->select("names")->from("prime_types_parameter")->where(array("sysid"=>$id))->get()->row();
        return ($sql) ? $sql->names: '';
    }
    function addpayrollmatrix(){
        $data = array();
        $codes = $this->input->post('codes');
        $types = $this->input->post('typesnames');
        $functions = $this->input->post('functions');
        $effects = $this->input->post('effects');
        $notax = $this->input->post("notax");
        $capping = $this->input->post('capping');
        $func = '';
        $msg = '';
        $qry = false;

        $this->db->trans_begin();
        $insarr = array(
            'codes' => $codes,
            'typesid' => $types,
            'functions' => $functions,
            'effects' => $effects,
            'notax' => $notax,
            'capping' => $capping
        );
        $sql = $this->db->insert("payroll_matrix", $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $func = 'success';
            $msg = 'Item has been added.';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $func = 'error';
            $msg = 'Failed to add item.';
            $qry = false;
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }
    function getparametersname(){
        $data = array();
        $sql = $this->db->select("sysid,codes,names")->from("prime_types_parameter")->where(array("codes" => 'PRTRNTYPE',"status"=>1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){

                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }
        echo json_encode($data);
    }
    function getempbreakdowns(){
        $data = array();
        $groupid = $this->input->post('id');
        $inputarr = $this->input->post('inputs');
        $empid = $inputarr["dataid"];
        $html = '';
        $html .= '<table class="table table-bordered table-condensed table-responsive table-striped tbl-xs">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Amount</th>';
        $html .= '<th>Month</th>';
        $html .= '<th>Year</th>';
        $html .= '<th>Status</th>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $fetchbreakdown = $this->db->select("amount,month,year,status,paytype")->from("payroll_manual_transactions_breakdown")
            ->where(array("empid" => $empid , "groupid" => $groupid))->get();
        if($fetchbreakdown->num_rows() > 0){
            $num = 1;
            foreach ($fetchbreakdown->result() as $row){
                $stat = '';
                $year = $row->year;
                $month = $row->month;
                $paytype = $row->paytype;
                $qry_group_info = $this->db->select('tsysid')->from('payroll_manual_transactions')
                    ->where(array('sysid' => $groupid))
                    ->get()->row();
                if($qry_group_info) {
                    $typesid = $qry_group_info->tsysid;

                    $reported_ded = $this->db->select('SUM(rt.amt) AS amt')
                        ->from('payroll_reports_group as rg')
                        ->join('payroll_reports_main as rm','rm.groupid = rg.sysid','left')
                        ->join('payroll_reports_trn as rt','rm.sysid = rt.payrollid','left')
                        ->where(array(
                            'rg.years' => $year,
                            'rg.months' => $month,
                            'rm.empid' => $empid,
                            'rt.trntype' => $typesid,
                            'rg.status' => 301,
                            'rg.paytype' => $paytype))->get()->row();

                    if ($reported_ded->amt >= $row->amount) {
                        $stat = '<span class="label label-xs label-success">PAID</span>';
                    } else if ($row->status == 303) {
                        $stat = '<span class="label label-xs label-danger">CANCELLED</span>';
                    } else {
                        $stat = '<span class="label label-xs label-warning">UNPAID</span>';
                    }

                }else {
                    if ($row->status == 313) {
                        $stat = '<span class="label label-xs label-warning">UNPAID</span>';
                    } else if ($row->status == 312) {
                        $stat = '<span class="label label-xs label-success">PAID</span>';
                    } else if ($row->status == 303) {
                        $stat = '<span class="label label-xs label-danger">CANCELLED</span>';
                    }
                }
                $html .= '<tr>';
                $html .= '<td>'.$num++.'</td>';
                $html .= '<td class="number text-danger">'.number_format($row->amount , 2).'</td>';
                $html .= '<td>'.$this->getmonthname($row->month).'</td>';
                $html .= '<td>'.$row->year.'</td>';
                $html .= '<td>'.$stat.'</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $data['html'] = $html;
        echo json_encode($data);
    }
    function getmonthname($monthid){

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
    function getdeductionstype(){
        $data = array();

        $getdeductions = $this->db->select("pm.typesid,ptp.codes,ptp.names")->from("payroll_matrix as pm")
            ->join("prime_types_parameter as ptp","ptp.sysid  = pm.typesid" , "left")
            ->where(array("pm.codes" => 'others' , "pm.effects" => 0))
            ->get();
        if($getdeductions->num_rows() > 0){
            foreach ($getdeductions->result() as $row){
                $data['list'][] = array(
                    'id' => $row->typesid,
                    'text' => ''.'-'.$row->names
                );
            }
        }

        echo json_encode($data);
    }
    function getdeductions(){
        $data = array();
        $ids = array();
        $totalpaidamount  = 0 ;
        $totalunpaidamount = 0;
        $dataid = $this->input->post('dataid');

        $getdeductionstype = $this->db->select("pm.typesid")->from("payroll_matrix as pm")
            ->join("prime_types_parameter as ptp","ptp.sysid  = pm.typesid" , "left")
            ->where(array("pm.codes" => 'others' , "pm.effects" => 0))
            ->get();
        if($getdeductionstype->num_rows() > 0){
            foreach ($getdeductionstype->result() as $row){
                $ids[] = $row->typesid;
                $data['ids'][] = array(
                    'id' => $row->typesid
                );
            }
        }


        $getbalance = $this->db->select("pmtb.amount,pmtb.status")->from("payroll_manual_transactions_breakdown as pmtb")
            ->join("payroll_manual_transactions as pmt","pmt.sysid = pmtb.groupid && pmt.empid = pmtb.empid","left")
            ->where(array("pmtb.empid" => $dataid))
            ->where_in('tsysid' , $ids)->get();
        if($getbalance){
            foreach ($getbalance->result() as $row){
                if($row->status == 312){
                    $totalpaidamount += $row->amount;
                }else if($row->status == 313){
                    $totalunpaidamount += $row->amount;
                }
            }
        }


        $getdata = $this->db->select("sysid,empid,tsysid,amount , monthdevide , amountpermonth  ,status")
            ->from("payroll_manual_transactions")
            ->where(array("empid" => $dataid,"status !=" => 0))
            ->where_in('tsysid' , $ids)
            ->get();
        if($getdata->num_rows() > 0){
            $num = 1;
            foreach ($getdata->result() as $row){

                if($row->monthdevide == 1){
                    $monthtext = ' Month';
                }else{
                    $monthtext = ' Months';
                }
                $data['deductionsdata'][] = array(
                    'expand' => $row->sysid,
                    'type' => $this->gettypename($row->tsysid),
                    'amount' => $row->amount,
                    'for' => $row->monthdevide.$monthtext,
                    'permonth' => $row->amountpermonth,
                    'control' => '<button class="btn btn-xs btn-danger" id="deletedeductionbtn" data-empid="'.$row->empid.'" data-id="'.$row->sysid.'" ><i class="fa fa-trash"></i></button>',
                );
            }
        }
        $data['totalpaidamount'] = $totalpaidamount;
        $data['totalunpaidamount'] = $totalunpaidamount;
        echo json_encode($data);
    }

    function getloans(){
        $data = array();
        $empid = $this->input->post('empid');
        $tabid = $this->input->post('tabid');
        $type = 0;
        $type = $tabid;
        $totalpaidamount = 0;
        $totalunpaidamount = 0;

        $getbalance = $this->db->select("pmtb.amount,pmtb.status")->from("payroll_manual_transactions_breakdown as pmtb")
            ->join("payroll_manual_transactions as pmt","pmt.sysid = pmtb.groupid && pmt.empid = pmtb.empid","left")
            ->where(array("pmtb.empid" => $empid,"pmt.tsysid" => $tabid))->get();
        if($getbalance){
            foreach ($getbalance->result() as $row){
                if($row->status == 312){
                    $totalpaidamount += $row->amount;
                }else if($row->status == 313){
                    $totalunpaidamount += $row->amount;
                }
            }
        }

        $sql = $this->db->select("pmt.sysid,pmt.empid,pmt.tsysid,pmt.amount,pmt.monthdevide,pmt.amountpermonth,pmt.status,pmt.datecreated , pmts.subtype")
            ->from("payroll_manual_transactions as pmt")->where(array("pmt.empid" => $empid , "pmt.tsysid" => $tabid,"pmt.status " => 313 ))
            ->join("payroll_manual_transactions_subtypes as pmts" , "pmts.sysid = pmt.subtype" , "left")
            ->get();

        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                if($row->monthdevide == 1){
                    $monthtext = ' Month';
                }else{
                    $monthtext = ' Months';
                }

                $data['emploansdata'][] = array(
                    "expand" => $row->sysid,
                    "amount" => number_format($row->amount, 2),
                    "for" => $row->monthdevide.$monthtext,
                    "amtpermonth" => number_format($row->amountpermonth , 2),
                    "loantype" => $row->subtype,
                    "datecreated" =>$row->datecreated,
                    "control" => '<button class="btn btn-xs btn-danger" data-empid="'.$row->empid.'" data-id="'.$row->sysid.'" id="deleteloans"><i class="fa fa-trash"></i></button>'
                );
            }
        }
        $data['totalpaidamount'] = $totalpaidamount;
        $data['totalunpaidamount'] = $totalunpaidamount;
        echo json_encode($data);
    }
    function empsalaryinc(){
        $data = array();

        $payclasstype = $this->input->post('payclasstype');
        if($payclasstype > 0){
            $this->db->where(array("pemp.payclass_id" => $payclasstype));
        }

        $sql = $this->db->select("pem.sysid,pem.empid,p.lastname,p.firstname,pcm.names,pes.amt")
            ->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_employee_costcenter as pec","pec.empid = pem.sysid","left")
            ->join("prime_costcenter_main as pcm" ,"pcm.sysid = pec.ccid" , "left")
            ->join("prime_employee_salary as pes" ,"pes.empid = pem.sysid","left")
            ->join("payroll_emplist as pe","pe.empid = pem.sysid" , "left")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1")
            ->where(array("pem.status" => 1 , "pec.type" => 1 , "pec.status" => 1 , "pes.status" => 1,"pe.status" => 1))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $getpendingincrease = $this->db->select("increase,newamt,purpose,remarks")->from("trn_employee_salary")
                    ->where(array("empid" => $row->sysid , "status" => 0))
                    ->get()->row();
                $increasepending = ($getpendingincrease) ? $getpendingincrease->increase : '';
                $newamtpending = ($getpendingincrease) ? $getpendingincrease->newamt : 0;
                $remarks = ($getpendingincrease) ? $getpendingincrease->remarks :'';
                $purpose = ($getpendingincrease) ? $getpendingincrease->purpose :'';

                if($row->sysid != 1) {
                    $input_inc = '';
                    $input_inc .= '<div class="input-icon left">';
                    $input_inc .= '<i class="fa fa-pencil"></i>';
                    $input_inc .= '<input id="input_increase" value="'.$increasepending.'" type="text" style="width: 100%;" class="form-control inline salaryincinput number" placeholder="Enter increase here..."/>';
                    $input_inc .= '</div>';
                    $data['empsalaryincdata'][] = array(
                        "num" => $num++,
                        "empcode" => $row->empid . '<input type="hidden" id="empid" value="'.$row->sysid.'" />',
                        "name" => $row->lastname.', '.$row->firstname,
                        "department" => $row->names,
                        "basic" => '<span id="basictext">'.number_format($row->amt , 2) .'</span>' . '<input type="hidden" id="basic" value="'.$row->amt.'" />',
                        "inputs" => $input_inc,
                        "purpose" =>'<input  type="text" class="form-control inline incpurpose" id="incpurpose" />',
                        'purposeval' => $purpose,
                        "remarks" =>'<input type="text" name="remarks" value="'.$remarks.'" id="remarks" class="form-control inline" placeholder="Enter remarks here" />',
                        "pendingamt" =>' <span id="pendingnewamt">'.number_format($newamtpending ,2).'</span>',
                    );
                }
            }
        }

        echo json_encode($data);
    }

    function addemployeesalarytrn(){
        $data = array();

        $this->db->trans_begin();
        $empid = $this->input->post('empid');
        $salinc = $this->input->post('salinc');
        $purpose = $this->input->post('purpose');
        $remarks = $this->input->post('remarks');
        $newamt = 0;
        $msg = '';
        $qry = false;
        $func = '';

        $updatestat = array(
            'status' => 5
        );
        $this->db->where(array("status" => 0, "empid" => $empid ));
        $this->db->update("trn_employee_salary" , $updatestat);

        $getcurbasic = $this->db->select("amt")
            ->from("prime_employee_salary")
            ->where(array("empid" => $empid , "status" => 1))
            ->order_by("datecreated" , "desc")
            ->get()->row();
        if($getcurbasic){
            $newamt = $getcurbasic->amt + $salinc;
            $insarr = array(
                'empid' => $empid,
                'curamt' => $getcurbasic->amt,
                'newamt' => $newamt,
                'increase' => $salinc,
                'purpose' => $purpose,
                'remarks' => $remarks,
                'createdby' => user_id()
            );
            $insertsalinctrn = $this->db->insert("trn_employee_salary" , $insarr);
            if($this->db->trans_status() == true && $insertsalinctrn){
                $this->db->trans_commit();
                $msg = 'Increase has been save.';
                $qry = true;
                $func = 'success';
            }else{
                $this->db->trans_rollback();
                $msg = 'Fail to save increase.';
                $qry = false;
                $func = 'error';
            }
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['pendingnewamt'] = number_format($newamt ,2);
        echo json_encode($data);
    }
    function savesalaryincreasetrn(){
        $data = array();
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();

        $insarr = array(
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("trn_employee_salary_group", $insarr);
        $groupid = $this->db->insert_id();
        if($groupid) {
            $updatearr = array(
                'groupid' => $groupid,
                'status' => 1,
                'trnid' => $groupid,
                'updatedby' => user_id()
            );
            $this->db->where(array("status " => 0));
            $this->db->update("trn_employee_salary", $updatearr);
        }

        $getlastid = $this->db->select("MAX(groupid) AS lastgroupid")->from("trn_employee_salary")->limit(1)->get()->row();
        if($getlastid){
            $lastgroupid =  $getlastid->lastgroupid;
            $getemployees = $this->db->select("empid , newamt")->from("trn_employee_salary")
                ->where(array("groupid" => $lastgroupid , "status" => 1))
                ->get();
            if($getemployees->num_rows() > 0){
                foreach ($getemployees->result() as $row){
                    $data['empidtoupdate'][] = array(
                        'ids' => $row->empid
                    );
                    $this->db->where(array("empid" =>  $row->empid));
                    $this->db->update("prime_employee_salary", array("status" => 0));

                    $inssalarr = array(
                        'empid' => $row->empid,
                        'amt' => $row->newamt,
                        'status' => 1,
                        'trnid' => $groupid
                    );

                    $this->db->insert("prime_employee_salary" ,$inssalarr);
                }
            }
        }

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Salaries has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save salaries.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function pceoempsalaryinc(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $sql = $this->db->select("tes.empid,tes.curamt,tes.increase,tes.newamt, pem.empid AS empcode,p.lastname,p.firstname,pcm.desc")
            ->from("trn_employee_salary as tes")
            ->join("prime_employee_main as pem" , "pem.sysid = tes.empid","left")
            ->join("person as p" ,"p.sysid = tes.empid","left")
            ->join("prime_employee_costcenter as pec" , "pec.empid = tes.empid","left")
            ->join("prime_costcenter_main as pcm" , "pcm.sysid = pec.ccid" , "left")
            ->join("trn_employee_salary_group as tesg" ,"tesg.sysid = tes.groupid && tesg.sysid = ".$dataid , "left")
            ->where(array("tes.groupid" => $dataid , "tes.status" => 1 , "tesg.status" => 300 ,"pec.type" => 1))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['pceoempsalaryincdata'][] = array(
                    "num" => $num++,
                    "empcode" => $row->empcode,
                    "lastname" => $row->lastname,
                    "firstname" => $row->firstname,
                    "department" =>  $row->desc,
                    "basic" =>  number_format($row->curamt ,2 ),
                    "increase" => number_format($row->increase , 2),
                    "total" => number_format($row->newamt , 2),
                    "control" =>'<button id="disapproveempsalinc" data-id="'.$row->empid.'" class="btn btn-default btn-sm"><i class="fa fa-minus"></i> Disapprove</button>'
                );
            }
        }

        echo json_encode($data);
    }
    function disapproveempincsal(){
        $data = array();
        $empid = $this->input->post('empid');
        $groupid = $this->input->post('groupid');
        $this->db->trans_begin();

        $getempinfo = $this->db->select("p.lastname,p.firstname")
            ->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->where(array("pem.sysid" => $empid , "pem.status" => 1))
            ->get()->row();


        $this->db->where(array("empid" => $empid , "groupid" => $groupid , "status" => 1));
        $disapproveempsalinc = $this->db->update("trn_employee_salary" , array("status" => 302,"updatedby" => user_id()));
        if($disapproveempsalinc && $this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Employee '.$getempinfo->lastname.', '.$getempinfo->firstname.' salary increase has been disapprove';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to disapprove salary increase';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function pceoapprovesalaryinc(){
        $data = array();
        $groupid = $this->input->post('groupid');

        $this->db->trans_begin();

        $this->db->set('a.status', 301);
        $this->db->set('b.status', 301);
        $this->db->set('a.updatedby', user_id());
        $this->db->set('b.updatedby', user_id());


        $this->db->where('a.status', 1);
        $this->db->where('a.groupid', $groupid);
        $this->db->where('b.sysid', $groupid);
        $this->db->where('b.sysid = a.groupid');
        $sql = $this->db->update('trn_employee_salary as a, trn_employee_salary_group as b');

        $updateempsalary  = $this->db->select("empid,newamt")->from("trn_employee_salary")
            ->where(array("status" => 301))
            ->order_by("groupid" ,"desc")
            ->limit(1)
            ->get();
        if($updateempsalary->num_rows() > 0) {
            foreach ($updateempsalary->result() as $row) {

                $this->db->where(array("empid" => $row->empid , "status" => 1));
                $updatesalarystat = $this->db->update("prime_employee_salary",array("status" => 0));

                $insarr = array(
                    'empid' => $row->empid,
                    'amt' =>  $row->newamt,
                    'status' => 1
                );
                $updatenewsalary = $this->db->insert("prime_employee_salary" , $insarr);
            }

            if ($sql && $this->db->trans_status() == true && $updatesalarystat && $updatenewsalary) {
                $this->db->trans_commit();
                $msg = 'Salary increase applied.';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Fail to save salary increase';
                $func = 'error';
                $qry = false;
            }

            $data['msg'] = $msg;
            $data['func'] = $func;
            $data['qry'] = $qry;
            echo json_encode($data);
        }
    }
    function fetchaccomplishments(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $files = glob('uploads/employeeaccomp/'.$dataid.'/*.{JPG,jpg,PNG,png,gif,GIF,pdf}', GLOB_BRACE);


        foreach($files as $file) {
            $filename = explode('/',$file);
            $data['files'][] = array(
                'file' => base_url().'uploads/employeeaccomp/'.$dataid.'/'.$filename[3]
            );
        }

        echo json_encode($data);
    }
    function getpurposeofsalaryinc(){
        $data = array();
        $sql = $this->db->select("sysid,names")->from("prime_types_parameter")->where(array("codes" => 'SALINC',"status"=>1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }
        echo json_encode($data);
    }
    function getempcurrentworkshift(){
        $data = array();

        $sql = $this->db->select("pem.sysid,p.firstname,p.lastname,pem.empid,pemwm.workshift_id")
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_workshift_matrix as pemwm" , "pemwm.empid = pem.sysid" , "left")
            ->join("prime_employee_main_workshift as pemw" , "pemw.sysid = pemwm.workshift_id" , "left")
            ->join("payroll_emplist as pe","pe.empid = pem.sysid","left")
            ->where(array("pem.status" => 1 , "pemwm.status" => 1 , "pe.status" => 1))
            ->order_by("p.lastname" , "asc")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $checkifhasdraftworkshift = $this->db->select("workshiftid")
                    ->from("trn_employee_workshift_group")
                    ->where(array("empid" => $row->sysid , "status !=" => 301 , "status !=" => 0))
                    ->get()->row();

                $data['currentworkshiftdata'][] = array(
                    "num" => $num++,
                    "name"  => $row->lastname.', '.$row->firstname,
                    "workshift" => '<input type="text" name="workshift" data-id="'.$row->sysid.'" id="currentworkshift" class="form-control inline" />',
                    "workshiftid" => ($checkifhasdraftworkshift) ? $checkifhasdraftworkshift->workshiftid : $row->workshift_id
                );
            }
        }

        echo json_encode($data);
    }
    function getselect2workshift(){
        $data = array();
        $sql = $this->db->select("sysid, desc,codes")->from("prime_employee_main_workshift")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function insertworkshiftrequest(){
        $data = array();
        $workshiftid =  $this->input->post('workshiftid');
        $empid =  $this->input->post('empid');
        $msg = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();

        $insertarr = array(
            'empid' => $empid,
            'workshiftid' => $workshiftid,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("trn_employee_workshift_group" , $insertarr);
        $data['error'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Workshift has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save workshift';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function sentworkshiftforapproval(){
        $data = array();
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;

        //insert group id
        $insarr = array(
            'status' => 300,
            'createdby' => user_id(),
            'updateby' => user_id()
        );
        $insertgroupid = $this->db->insert("trn_employee_workshift_groupid" , $insarr);
        $data['error1'] = $this->db->_error_message();
        $groupid =  $this->db->insert_id();

        $draftworkshift = $this->db->select("sysid")->from("trn_employee_workshift_group")
            ->where(array("status" => 307))->get();
        $data['error2'] = $this->db->_error_message();
        if($draftworkshift->num_rows() > 0){
            foreach ($draftworkshift->result() as $row){
                $matrixarr = array(
                    'trnid' => $row->sysid,
                    'groupid' => $groupid,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $inserttoworkshiftmatrix = $this->db->insert("trn_employee_workshift_matrix", $matrixarr);
                $data['error3'] = $this->db->_error_message();
            }
        }

        $updatearr = array(
            'status' => 300,
            'fromdate' => $fromdate,
            'todate'  => $todate
        );
        $this->db->where(array("status" => 307));
        $sendforapproval = $this->db->update("trn_employee_workshift_group" , $updatearr);
        $data['error4'] = $this->db->_error_message();

        create_transaction_trails('WORKSHIFT', 'SHIFTING' ,74, $groupid);

        if($this->db->trans_status() == true && $sendforapproval && $insertgroupid){
            $this->db->trans_commit();
            $msg = 'Workshift has been sent for approval.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to send for approval';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function getempattendancelogs(){
        $data = array();

        $bioid = $this->input->post('bioid');
        $dateval = $this->input->post('dateval');

        $sql = $this->db->select("peat.bioid,peat.logdate,peat.logtime")
            ->from("prime_employee_attendance_timelogs as peat")
            ->where(array("peat.bioid" => $bioid , "peat.logdate" => $dateval))
            ->group_by("peat.bioid,peat.logdate,peat.logtime")->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $getattendancetrn = $this->db->select("logtype")->from("trn_attendance_timelogs")
                    ->where(array("logdate" => $row->logdate , "bioid" => $row->bioid , "logtime" => $row->logtime , "status" => 301 ))
                    ->get()->row();

                $data['emplogstime'][] = array(
                    "num" => $num++,
                    "bioid" => $row->bioid,
                    "logtime" => $row->logtime,
                    "remarks" => '<input type="text" name="remarks" id="remarks" placeholder="Enter remarks" class="form-control inline" />',
                    "desc" => '<input type="text" name="timedesc" id="timedesc" class="form-control inline" />',
                    "logtype" => ($getattendancetrn) ? $getattendancetrn->logtype : ''
                );
            }
        }

        echo json_encode($data);
    }
    function gettimelogsselect2(){
        $data = array();

        $sql = $this->db->select("sysid,names")->from("prime_types_parameter")
            ->where(array("codes" => 'TIMELOGTYPE'))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }
        echo json_encode($data);
    }
    function updateattendancetimelogs(){
        $data = array();

        $bioid = $this->input->post('bioid');
        $logdate = $this->input->post('logdate');
        $logtype = $this->input->post('logtype');
        $logtime = $this->input->post('logtime');
        $remarks = $this->input->post('remarks');
        $msg = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();

        $updatearr = array(
            'status' => 0
        );

        $this->db->where(array("bioid" => $bioid , "logdate" => $logdate , "logtime" => $logtime));
        $updateold = $this->db->update("trn_attendance_timelogs" , $updatearr);

        if($logtype != '' || $logtype != null){
            $insarr = array(
                'bioid' => $bioid,
                'logdate' => $logdate,
                'logtype' => $logtype,
                'logtime' => $logtime,
                'remarks' => $remarks,
                'createdby' => user_id(),
                'updateby' => user_id()
            );

            $sql = $this->db->insert("trn_attendance_timelogs" , $insarr);
            $data['error'] = $this->db->_error_message();
        }

        if($this->db->trans_status() == true && $updateold){
            $this->db->trans_commit();
            $msg = 'Time log has been updated.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to update time log.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function getpendingworkshift(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $sql = $this->db->select("tewg.sysid,tewm.groupid,pemw.desc,p.lastname,p.firstname,pem.empid")
            ->from("trn_employee_workshift_matrix as tewm")
            ->join("trn_employee_workshift_group as tewg","tewg.sysid = tewm.trnid","left")
            ->join("prime_employee_main_workshift as pemw","pemw.sysid = tewg.workshiftid" , "left")
            ->join("prime_employee_main as pem","pem.sysid = tewg.empid" , "left")
            ->join("person as p","p.sysid = pem.personid" , "left")
            ->where(array("groupid" => $dataid ,"tewm.status" => 300))->get();
        $data['error1'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['emppendingworkshift'][] = array(
                    "num" => $num++,
                    "empid" => $row->empid,
                    "name" => $row->lastname.', '.$row->firstname,
                    "shift" => $row->desc,
                    "control" => '<button type="button" data-id="'.$row->sysid.'" data-groupid="'.$row->groupid.'" class="btn btn-default btn-sm" id="workshiftdisapprovebtn">Disapprove</button>'
                );
            }
        }
        echo json_encode($data);
    }
    function disapprovependingworkshift(){
        $data = array();
        $msg = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();
        $dataid = $this->input->post('dataid');
        $groupid = $this->input->post('groupid');

        $updatestat = array(
            'status' => 302
        );
        $this->db->where(array("sysid" => $dataid));
        $updatetrnworkshift = $this->db->update("trn_employee_workshift_group" , $updatestat);
        $this->db->where(array("trnid" => $dataid , "groupid" => $groupid));
        $updatetrnworkshiftmatrix = $this->db->update("trn_employee_workshift_matrix" , $updatestat);

        if($this->db->trans_status() ==  true && $updatetrnworkshift && $updatetrnworkshiftmatrix){
            $this->db->trans_commit();
            $msg = 'Workshift has been disapproved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to disapproved workshift.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function approveallempworkshift(){
        $data = array();

        $groupid = $this->input->post('groupid');

        $this->db->trans_begin();

        $this->db->set('a.status',301);
        $this->db->set('b.status',301);
        $this->db->set('c.status',301);

        $this->db->where('c.status' , 300);
        $this->db->where('b.status' , 300);
        $this->db->where('a.status' , 300);
        $this->db->where('c.sysid' , $groupid);
        $this->db->where('b.groupid' , $groupid);
        $this->db->where('c.sysid = b.groupid');
        $this->db->where('a.sysid = b.trnid');
        $sql = $this->db->update('trn_employee_workshift_group as a, trn_employee_workshift_matrix as b , trn_employee_workshift_groupid as c');
        $data['error1'] = $this->db->_error_message();


        if($this->db->trans_status() == true && $sql ){
            $this->db->trans_commit();
            $msg = 'Workshift has been approved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to approve workshift.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function approveattendancereq(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 301
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("trn_attendance_timelogs",$updatearr);

        /*   $gettimeloginfo  = $this->db->select("")->from("trn_attendance_timelogs")
            ->where(array("sysid" => $dataid))->get()->row();

        if($gettimeloginfo){
            if($gettimeloginfo->logtype == 1021 || $gettimeloginfo->logtype == 1023){
                $status = 0;
            }else{
                $status = 1;
            }
            $insarr = array(
                'bioid' => $gettimeloginfo->bioid,
                'logdate' =>$gettimeloginfo->logdate,
                'logtime' =>$gettimeloginfo->logtime,
                'status' =>$status
            );
            $data['bioid'] = $gettimeloginfo->bioid;
            $data['logdate'] =$gettimeloginfo->logdate;
            $data['logtime'] =$gettimeloginfo->logtime;
            $data['status'] = $status;
            $inserttotimelogs = $this->db->insert("prime_employee_attendance_timelogs" , $insarr);
        } */

        if($sql && $this->db->trans_status() == true) {
            $this->db->trans_commit();
            $msg = 'Attendance has been approve.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to approve attendance';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function disapproveattendancereq(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 302
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("trn_attendance_timelogs",$updatearr);

        if($sql && $this->db->trans_status() == true) {
            $this->db->trans_commit();
            $msg = 'Attendance has been disapprove.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to disapprove attendance';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function addtimelogsattendance(){
        $data = array();

        $timelogsinput = $this->input->post('timelogsinput');
        $remarksinput  = $this->input->post('remarksinput');
        $logtypeinput = $this->input->post('logtypeinput');
        $hiddendate = $this->input->post('hiddendate');
        $hiddenbioid = $this->input->post('hiddenbioid');
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        $insarr = array(
            'bioid' => $hiddenbioid,
            'logdate' => $hiddendate,
            'logtime' => $timelogsinput,
            'remarks' => $remarksinput,
            'logtype' => $logtypeinput,
            'status' => 300,
            'createdby' => user_id(),
            'updateby' => user_id()
        );
        $sql = $this->db->insert("trn_attendance_timelogs" , $insarr);
        if($sql && $this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Time log has been added, time log will be updated upon approval.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to add time log.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function getcompanybranches(){
        $data = array();
        $value = '';
        $mondayval = '';
        $tuesdayval = '';
        $wednesdayval = '';
        $thursdayval = '';
        $fridayval = '';
        $saturdayval = '';
        $sundayval = '';
        $month = $this->input->post('month');
        $type = $this->input->post('type');
        $year = $this->input->post('year');
        $data['month'] = $month;
        $data['type'] = $type;
        $data['year'] = $year;

        $sql = $this->db->select("pcbwm.sysid,pcbwm.branchid,pcb.code,pcb.desc,pemw.desc as timedesc , pemw.sysid as shiftid")
            ->from("prime_company_branch_workshift_matrix as pcbwm")
            ->join("prime_company_branch as pcb","pcb.sysid = pcbwm.branchid","left")
            ->join("prime_employee_main_workshift as pemw","pemw.sysid = pcbwm.workshiftid" , "left")
            ->get();
        $data['error1'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                for($index=1;$index<=7;$index++){
                    if($month > 0){
                        $data['monthtrue'] = 'monthtrue';
                        $this->db->where(array("tsr.month" => $month));
                    }
                    if($type > 0){
                        $this->db->where(array("tsr.type" => $type));
                    }
                    if($year > 0){
                        $this->db->where(array("tsr.year" => $year));
                    }
                    $check = $this->db->select("ptp.names , tsrt.teamid , tsrt.day , tsrt.branch , tsr.type")->from("trn_schedule_requests_time as tsrt")
                        ->join("prime_types_parameter as ptp", "ptp.sysid = tsrt.teamid" , "left")
                        ->join("trn_schedule_requests as tsr" ,"tsr.sysid = tsrt.schedid","left")
                        ->where(array("tsrt.day" => $index , "tsrt.branch" => $row->sysid,"tsrt.status" => 301 , "tsr.status" => 301))
                        ->group_by("tsrt.teamid")
                        ->get();
                    $data['error2'] = $this->db->_error_message();
                    if($check->num_rows() > 0){
                        foreach ($check->result() as $row1){
                            $value .= '<code>'.$row1->names.'</code><button data-teamid="'.$row1->teamid.'" data-day="'.$row1->day.'" data-branch="'.$row1->branch.'" data-type="'.$row1->type.'" id="removeteamshift" type="button" class="btn btn-xs btn-danger">x</button><br>';
                        }
                    }else{
                        $value = '';
                    }

                    if($index == 1){
                        $mondayval = $value;
                    }else if($index == 2){
                        $tuesdayval = $value;
                    }else if($index == 3){
                        $wednesdayval = $value;
                    }else if($index  == 4){
                        $thursdayval = $value;
                    }else if($index == 5){
                        $fridayval = $value;
                    }else if($index == 6){
                        $saturdayval = $value;
                    }else if($index == 7){
                        $sundayval = $value;
                    }
                    $value = '';
                }


                $data['shiftdata'][] = array(
                    'num' => $num++,
                    'brach' => $row->desc,
                    'time' =>  $row->timedesc,
                    'mon' => $mondayval.' '.'<input type="hidden" placeholder="Select.." data-branch="'.$row->sysid.'" data-class="1" data-shift="'.$row->shiftid.'" class="form-control input-days" style="width: 120px !important;"  name="mondayselect2['.$row->shiftid.']" id="mondayselect" value="" />',
                    'tue' => $tuesdayval.' '.'<input type="hidden" placeholder="Select.." data-branch="'.$row->sysid.'" data-class="2" data-shift="'.$row->shiftid.'" class="form-control input-days" style="width: 120px !important;"  name="tuesdayselect2['.$row->shiftid.']" id="tuesdayselect" value=""/>',
                    'wed' => $wednesdayval.' '.'<input type="hidden" placeholder="Select.." data-branch="'.$row->sysid.'" data-class="3" data-shift="'.$row->shiftid.'"  class="form-control input-days" style="width: 120px !important;"  name="wednesdayselect2['.$row->shiftid.']" id="wednesdayselect" value=""/>',
                    'thu' => $thursdayval.' '.'<input type="hidden" placeholder="Select.." data-branch="'.$row->sysid.'" data-class="4" data-shift="'.$row->shiftid.'"  class="form-control input-days" style="width: 120px !important;"  name="thursdayselect2['.$row->shiftid.']" id="thursdayselect" value=""/>',
                    'fri' => $fridayval.' '.'<input type="hidden" placeholder="Select.." data-branch="'.$row->sysid.'" data-class="5" data-shift="'.$row->shiftid.'"  class="form-control input-days" style="width: 120px !important;"  name="fridayselect2['.$row->shiftid.']" id="fridayselect" value=""/>',
                    'sat' => $saturdayval.' '.'<input type="hidden" placeholder="Select.." data-branch="'.$row->sysid.'" data-class="6" data-shift="'.$row->shiftid.'"  class="form-control input-days" style="width: 120px !important;"  name="saturdayselect2['.$row->shiftid.']" id="saturdayselect" value=""/>',
                    'sun' => $sundayval.' '.'<input type="hidden" placeholder="Select.." data-branch="'.$row->sysid.'" data-class="7" data-shift="'.$row->shiftid.'"  class="form-control input-days" style="width: 120px !important;"  name="sundayselect2['.$row->shiftid.']" id="sundayselect" value=""/>',
                    'control' => '<a href="javascript:;" id="btn_edit_shift" class="btn inline btn-btn btn-xs "><i class="fa fa-edit"></i></a>'
                );
                $value = '';
                $mondayval = '';
                $tuesdayval = '';
                $wednesdayval = '';
                $thursdayval = '';
                $fridayval = '';
                $saturdayval = '';
                $sundayval = '';

            }
        }
        echo json_encode($data);
    }
    function getbranches(){
        $data = array();

        $sql = $this->db->select("sysid,code,desc")
            ->from("prime_company_branch")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text'=> $row->code.' - '.$row->desc
                );
            }
        }

        echo json_encode($data);
    }
    function getteamassign(){
        $data = array();
        $month = $this->input->post('month');
        $typehalf = $this->input->post('typehalf');
        $sql = $this->db->select("ptp.sysid,ptp.names")->from("prime_employee_team_assignments as peta")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = peta.teamid" , "left")
            ->where(array("ptp.codes" => 'TSTEAM',"peta.status" => 1 , "peta.month" => $month , "peta.type" => $typehalf))
            // ->where(array("ptp.codes" => 'TSTEAM',"peta.status" => 1 ))
            ->group_by("peta.teamid")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }

        echo json_encode($data);
    }
    function fetchempteam(){
        $data = array();
        $sql = $this->db->select("pem.sysid , p.lastname,p.firstname")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid","left")
            ->where(array("pem.type !=" => 0 , "pem.status" => 1))
            ->or_where('pem.type' , 1)
            ->or_where('pem.type' , 2)
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['empshiftassignmentdata'][] = array(
                    'num' => $num++,
                    'name' => $row->lastname.', '.$row->firstname
                );
            }
        }

        echo json_encode($data);
    }
    function updateteamassignment(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $datateam = $this->input->post('datateam');
        $status = $this->input->post('status');

        $weekday = $this->input->post('weekday');
        $month = $this->input->post('month');
        $typeshift = $this->input->post('typeshift');
        $typehalf = $this->input->post('type');
        $year = $this->input->post('year');
        $day = $this->input->post('day');

        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();

        if($status == 0){
            $this->db->set('a.status',0);
            $this->db->set('b.status',0);

            $this->db->where('a.type', $typehalf);
            $this->db->where('a.empid', $dataid);
            $this->db->where('a.sysid = b.schedid');
            $this->db->where('b.teamid' , $datateam);
            $this->db->where('a.status' , 301);
            $this->db->where('b.status' , 301);
            $this->db->update('trn_schedule_requests as a, trn_schedule_requests_time as b');
        }else{
            $checkshiftifexist = $this->db->select("tsrt.*")
                ->from("trn_schedule_requests_time as tsrt")
                ->join("trn_schedule_requests as tsr","tsr.sysid = tsrt.schedid" , "left")
                ->where(array("tsrt.teamid" => $datateam , "tsr.month" => $month , "tsr.year" => $year , "tsr.type" => $typehalf))
                ->get()->row();
            if($checkshiftifexist){
                $tsrarray = array(
                    'empid' => $dataid,
                    'month' => $month,
                    'day' => $day,
                    'year' => $year,
                    'type' => $typehalf,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 301
                );
                $this->db->insert("trn_schedule_requests" , $tsrarray);
                $schedid = $this->db->insert_id();
                $tsrtarray = array(
                    'schedid' => $schedid,
                    'logscnt' => $checkshiftifexist->logscnt,
                    'teamid' => $checkshiftifexist->teamid,
                    'day' => $checkshiftifexist->day,
                    'logtype' => $checkshiftifexist->logtype,
                    'branch' => $checkshiftifexist->branch,
                    'amtimein' => $checkshiftifexist->amtimein,
                    'amtimeout' => $checkshiftifexist->amtimeout,
                    'pmtimein' => $checkshiftifexist->pmtimein,
                    'pmtimeout' => $checkshiftifexist->pmtimeout,
                    'status' => 301,
                );
                $this->db->insert("trn_schedule_requests_time" ,$tsrtarray);
            }
        }

        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("empid" => $dataid , "teamid" => $datateam , "status" => 1 , "type"=> $typehalf , "month"=> $month , "typeshift" => $typeshift , "weekday" => $weekday));
        $this->db->update("prime_employee_team_assignments" , $updatearr);
        $data['error1'] = $this->db->_error_message();
        if($status == 1){
            if(date('j') > 15){
                $type = 2;
            }else{
                $type = 1;
            }
            $insarr = array(
                'teamid' => $datateam,
                'empid' => $dataid,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'day' => date('j'),
                'month' => $month,
                'year' => date('Y'),
                'type' => $typehalf,
                'typeshift' => $typeshift,
                'weekday' => $weekday
            );

            $this->db->insert("prime_employee_team_assignments" , $insarr);
            $data['error2'] = $this->db->_error_message();
        }


        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Team assign has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to assign team.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['status'] = $status;
        echo json_encode($data);
    }
    function updateworkshift(){
        $data = array();
        $empid = $this->input->post('empid');
        $val = $this->input->post('val');
        $msg = '';
        $func = '';
        $query = false;
        $this->db->trans_begin();
        $updatestat = array(
            'status' => 0,
            'updatedby' => user_id()
        );
        $this->db->where(array("empid" => $empid));
        $this->db->update("prime_employee_main_workshift_matrix" , $updatestat);
        $insarr = array(
            "empid" => $empid,
            "workshift_id" => $val,
            "status" => 1,
            "createdby" => user_id(),
            "updatedby" => user_id()
        );
        $insworkshift = $this->db->insert("prime_employee_main_workshift_matrix" , $insarr);
        if($this->db->trans_status() == true && $insworkshift){
            $this->db->trans_commit();
            $msg = 'Workshift has been updated.';
            $func = 'success';
            $query = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to update workshift.';
            $func = 'error';
            $query = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['query'] = $query;
        echo json_encode($data);
    }
    function fetchteamtableassign(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $day = $this->input->post('day');
        /* $half = 1;
       if($day > 15){
           $half = 2;
       }
       $data['half'] = $half;
        $sql = $this->db->select("ptp.sysid,ptp.names")->from("prime_employee_team_assignments as peta")
            ->join("prime_types_parameter as ptp","ptp.sysid = peta.teamid","left")
            ->where(array("peta.status" => 1,"peta.type" => $half , "peta.year" => $year , "peta.month" => $month))
            ->group_by("ptp.sysid,ptp.names")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $checkifteamisassigned  = $this->db->select("tsrt.sysid")->from("trn_schedule_requests_time as tsrt")
                    ->join("trn_schedule_requests as tsr","tsr.sysid = tsrt.schedid","left")
                    ->where(array("tsr.month" => $month,  "tsr.day" => $day , "tsr.year" => $year , "tsr.type" => $half,"tsrt.teamid" => $row->sysid,"tsr.status" => 301 , "tsrt.status" => 301))
                    ->get()->row();
                $class = '';
                $class = ($checkifteamisassigned) ? $class = 'checked' : '';

                $data['branchesdata'][] = array(
                    'id' => $num++,
                    'team' => $row->names,
                    'control' => '<input '.$class.'  name="'.$row->sysid.'" value="' . $row->sysid . '" type="checkbox" data-id="' . $row->sysid . '" data-team="'.$row->sysid.'" id="teamicheck' . $row->sysid . '" class="icheck" />',
                    'class' => $class
                );
            }
        }*/

        echo json_encode($data);
    }
    function fetchemptableassign(){
        $data = array();
        $branchid = $this->input->post('branchid');
        $shiftid = $this->input->post('shiftid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $day = $this->input->post('day');

        $sql = $this->db->select("pem.sysid,p.lastname,p.firstname")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->where(array("pem.type" => 2))->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $checkifalreadyassigned = $this->db->select("tsr.sysid")->from("trn_schedule_requests as tsr")
                    ->join("trn_schedule_requests_time as tsrt","tsrt.schedid = tsr.sysid","left")
                    ->where(array("tsr.empid" => $row->sysid,"tsrt.teamid" => null,"tsr.month"=>$month,"tsr.day"=>$day,"tsr.year"=>$year,"tsrt.branch"=>$branchid , "tsrt.shiftid" => $shiftid,"tsrt.status" => 301 , "tsr.status" => 301))
                    ->get()->row();
                $class = '';
                ($checkifalreadyassigned) ? $class = 'checked' : '';
                $data['empdata'][] = array(
                    'id' => $num++,
                    'empname' => $row->lastname,
                    'control' => '<input '.$class.'  name="'.$row->sysid.'" value="' . $row->sysid . '" type="checkbox" data-id="' . $row->sysid . '"  id="teamicheck" class="icheck" />'
                );
            }
        }

        echo json_encode($data);
    }
    function assignempsched(){
        $data = array();
        $empid = $this->input->post('empid');
        $branchid = $this->input->post('branchid');
        $shiftid = $this->input->post('shiftid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $day = $this->input->post('day');
        $msg = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();

        $schedreq = array(
            'empid' => $empid,
            'month' => $month,
            'day' => $day,
            'year' => $year,
            'type' => 2,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 301
        );
        $this->db->insert("trn_schedule_requests" , $schedreq);
        $schedid = $this->db->insert_id();

        $getworkshift  = $this->db->select("")->from("prime_employee_main_workshift")
            ->where(array("sysid" => $shiftid))->get()->row();

        $schedreqtime = array(
            'schedid' => $schedid,
            'logscnt' => ($getworkshift) ? $getworkshift->logcnt : '',
            'logtype' => ($getworkshift) ? $getworkshift->logtype : '',
            'branch' => $branchid,
            'shiftid' => $shiftid,
            'amtimein' => ($getworkshift) ? $getworkshift->am_start : '',
            'amtimeout' => ($getworkshift) ? $getworkshift->am_end : '',
            'pmtimein' => ($getworkshift) ? $getworkshift->pm_start : '',
            'pmtimeout' => ($getworkshift) ? $getworkshift->pm_end : '',
            'status' => 301
        );
        $this->db->insert("trn_schedule_requests_time" , $schedreqtime);
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Employee schedule saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save employee schedule.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function unassignempsched(){
        $data = array();
        $msg = '';
        $func ='';
        $qry = false;
        $this->db->trans_begin();
        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $day = $this->input->post('day');
        $this->db->set('a.status', 0);
        $this->db->set('b.status', 0);
        $this->db->where('a.empid', $empid);
        $this->db->where('a.month', $month);
        $this->db->where('a.day', $day);
        $this->db->where('a.year', $year);
        $this->db->where('a.status', 301);
        $this->db->where('b.status', 301);
        $this->db->where('a.sysid = b.schedid');
        $updatesched = $this->db->update('trn_schedule_requests as a, trn_schedule_requests_time as b');
        if($this->db->trans_status() == true  && $updatesched){
            $this->db->trans_commit();
            $msg = 'Employee sched has been removed.';
            $func ='success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove employee schedule.';
            $func ='error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    /*  function assignteamsched(){
        $data = array();
        $teamid = $this->input->post('teamid');
        $branchid = $this->input->post('branchid');
        $shiftid = $this->input->post('shiftid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $day = $this->input->post('day');

        $msg = '';
        $func ='';
        $qry = false;

        $this->db->trans_begin();

        if($day > 15){
            $half = 2;
        }else{
            $half = 1;
        }

        $getempfromteam = $this->db->select("")->from("prime_employee_team_assignments")
            ->where(array("status" => 1, "type" => $half , "month" => $month , "year" => $year,"teamid" => $teamid))
            ->get();
        $data['error1'] = $this->db->_error_message();
        if($getempfromteam->num_rows() > 0){
            foreach ($getempfromteam->result() as $row){

                $schedreq = array(
                    'empid' => $row->empid,
                    'month' => $month,
                    'day' => $day,
                    'year' => $year,
                    'type' => $half,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 301
                );
                $insertrnreq = $this->db->insert("trn_schedule_requests" , $schedreq);
                $data['error2'] = $this->db->_error_message();
                $schedid = $this->db->insert_id();

                $getworkshift  = $this->db->select("")->from("prime_employee_main_workshift")
                    ->where(array("sysid" => $shiftid))->get()->row();
                $data['error3'] = $this->db->_error_message();

                $schedreqtime = array(
                    'schedid' => $schedid,
                    'logscnt' => ($getworkshift) ? $getworkshift->logcnt : '',
                    'logtype' => ($getworkshift) ? $getworkshift->logtype : '',
                    'branch' => $branchid,
                    'shiftid' => $shiftid,
                    'amtimein' => ($getworkshift) ? $getworkshift->am_start : '',
                    'amtimeout' => ($getworkshift) ? $getworkshift->am_end : '',
                    'pmtimein' => ($getworkshift) ? $getworkshift->pm_start : '',
                    'pmtimeout' => ($getworkshift) ? $getworkshift->pm_end : '',
                    'status' => 301,
                    'teamid' => $teamid
                );
                $insertrnreqtime = $this->db->insert("trn_schedule_requests_time" , $schedreqtime);
                $data['error4'] = $this->db->_error_message();
            }
        }
        if($this->db->trans_status() == true && $insertrnreq && $insertrnreqtime){
            $this->db->trans_commit();
            $msg = 'Team has been assigned.';
            $func ='success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to assign team.';
            $func ='error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function unassignteamsched(){
        $data =array();
        $teamid= $this->input->post('teamid');
        $branchid = $this->input->post('branchid');
        $shiftid = $this->input->post('shiftid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $day = $this->input->post('day');

        $msg = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();

        $this->db->set('a.status', 0);
        $this->db->set('b.status', 0);

        $this->db->where('b.teamid', $teamid);
        $this->db->where('b.branch', $branchid);
        $this->db->where('b.shiftid', $shiftid);
        $this->db->where('a.month', $month);
        $this->db->where('a.day', $day);
        $this->db->where('a.year', $year);
        $this->db->where('a.status', 301);
        $this->db->where('b.status', 301);
        $this->db->where('a.sysid = b.schedid');
        $updatestat = $this->db->update('trn_schedule_requests as a, trn_schedule_requests_time as b');

        if($this->db->trans_status() == true && $updatestat){
            $this->db->trans_commit();
            $msg = 'Team has been removed.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove team.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    } */
    function assignempschedgroup(){
        $data = array();
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $workshift = $this->input->post('workshift');
        $branchid = $this->input->post('branchid');


        // Set timezone
        date_default_timezone_set('UTC');

        // Start date
        $date = $fromdate;
        // End date
        $end_date = $todate;

        while (strtotime($date) <= strtotime($end_date)) {

            $data['empgroupsched'][]  = array(
                "date" => $date,
                "empname" => '<input type="text" class="form-control" id="sbtsemployee" />',
                "control" => '<button class="btn btn-primary btn-xs">Add <i class="fa fa-plus"></i></button>'
            );

            $date = date ("Y-m-d l", strtotime("+1 day", strtotime($date)));
        }

        echo json_encode($data);
    }
    function getbranchesandworkshift(){
        $data = array();
        $dayofweek = $this->input->post('dayofweek');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');

        $sql = $this->db->select("pcbwm.sysid , pcbwm.branchid , pcbwm.workshiftid , pcb.desc , pemw.desc as workshift")->from("prime_company_branch_workshift_matrix as pcbwm")
            ->join("prime_company_branch as pcb" , "pcb.sysid = pcbwm.branchid",  "left")
            ->join("prime_employee_main_workshift as pemw" , "pemw.sysid = pcbwm.workshiftid","left")
            ->where(array("pcbwm.status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['branchdata'][] = array(
                    "num" => $num++,
                    "branch" => $row->desc.' '.$row->workshift,
                    "control" => '<input data-id="'.$row->sysid.'" data-branchid="'.$row->branchid.'" data-workshiftid="'.$row->workshiftid.'" class="radioselected icheck"  value="'.$row->sysid.'" type="radio" name="credits"/>'
                );
            }
        }
        echo json_encode($data);
    }
    function getallsbtsemployee(){
        $data = array();
        $dataid = $this->input->post('data');

        if(user_id() != 1){
            $this->db->where(array("teso.under" => $dataid));
        }

        $sql = $this->db->select("teso.empid , p.lastname , p.firstname")->from("trn_emp_schedule_operation as teso")
            ->join("prime_employee_main as pem" , "pem.sysid = teso.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->where(array("teso.status" => 1))->get();
        $data['error'] = $this->db->_error_message();

        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->empid,
                    'text' => strtoupper($row->lastname.', '.$row->firstname)
                );
            }
        }

        echo json_encode($data);
    }

    function getallsbtsteam(){
        $data = array();
        $passdata = $this->input->post('data');
        $sql = $this->db->select("ptp.sysid,ptp.names")->from("prime_employee_team_assignments as peta")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = peta.teamid" , "left")
            ->where(array("peta.status" => 1,"ptp.codes" => 'TSTEAM' , "peta.type" => $passdata))
            ->group_by("ptp.sysid,ptp.names")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }

        echo json_encode($data);
    }

    function addempschedule(){
        $data = array();
        $dayofweek = $this->input->post('dayofweek');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $branchidhidden = $this->input->post('branchidhidden');
        $workshiftidhidden = $this->input->post('workshiftidhidden');
        $sbtsemployee = $this->input->post('empid');
        $sbtsteam = $this->input->post('team');


        $this->db->trans_begin();

        $fromdatearr = explode('-', $fromdate);
        $todatearr = explode('-', $todate);
        if($fromdatearr[2] == 1 && $todatearr[2] == 15){
            $type = 1;
        }else{
            $type = 2;
        }


        if($sbtsteam > 0){

            $insertgroupidarr = array(
                'teamid' => $sbtsteam,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("trn_employee_schedule_group" , $insertgroupidarr);
            $groupid = $this->db->insert_id();


            $data['team'] = true;
            $data['type'] = $type;
            $data['month'] = $fromdatearr[1];
            $data['year'] = date('Y');
            $data['teamid'] = $sbtsteam;
            $getallemployeeintheteam = $this->db->select("empid")->from("prime_employee_team_assignments")
                ->where(array("status" => 1 , "type"=> $type , "month" => $fromdatearr[1] ,"year" => date('Y') , "teamid" => $sbtsteam))->get();
            $data['gettingids'] = $this->db->_error_message();
            if($getallemployeeintheteam->num_rows() > 0){
                foreach ($getallemployeeintheteam->result() as $row){
                    $empteamarr = array(
                        'empid' => $row->empid,
                        'dayofweek' => $dayofweek,
                        'fromdate' => $fromdate,
                        'todate' => $todate,
                        'branchid' => $branchidhidden,
                        'workshiftid' => $workshiftidhidden,
                        'status' => 1,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                        'groupid' => $groupid
                    );
                    $this->db->insert("trn_employee_schedule" , $empteamarr);
                    $data['loopadding'] = $this->db->_error_message();
                }
            }
        }

        if($sbtsemployee > 0){
            $insarr = array(
                'empid' => $sbtsemployee,
                'dayofweek' => $dayofweek,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'branchid' => $branchidhidden,
                'workshiftid' => $workshiftidhidden,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("trn_employee_schedule" , $insarr);
            $data['employeeadding'] = $this->db->_error_message();
        }
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Team/Employee has been added to a schedule.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add team/employee to a schedule.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function export201file(){
        $data = array();
        $html = '';


        $empid = $this->input->post('empid');
        $sql = $this->db->select("pem.sysid,pem.personid,p.lastname,p.firstname,pem.empid,pam.addrspec,p.birthdate,pem.status,pg.name as gender,ptp.names as jobposition,pcm.desc as department,ac.descriptions as provincialcity , m.names as maritalstatus,peb.bioid,pem.datestart , pem.dateend,acntry.country , acntry.nationality,ad.names as district,pam.zipcode")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("person_address_matrix as pam" , "pam.personid = p.sysid" , "left")
            ->join("prime_gender as pg" , "pg.sysid = p.gender" , "left")
            ->join("prime_employee_main_positions as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1" ,"left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = position_id" , "left")
            ->join("prime_employee_costcenter as pec","pec.empid = pem.sysid" , "left")
            ->join("prime_costcenter_main as pcm" , "pcm.sysid = pec.ccid","left")
            ->join("address_city as ac" , "ac.sysid = pam.addrcity" , "left")
            ->join("persons_marital_status_logs as pmsl" , "pmsl.personid = p.sysid && pmsl.status = 1" , "left")
            ->join("marital as m" , "m.sysid =pmsl.marital_status_id" , "left")
            ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid" , "left")
            ->join("persons_nationality_logs as pnl" , "pnl.personid = p.sysid && pnl.status = 1" , "left")
            ->join("address_country as acntry","acntry.sysid = pnl.nationality" , "left")
            ->join("address_districts as ad" , "ad.sysid = pam.addrdist && ad.types = 1" , "left")
            ->where(array( "pem.sysid" => $empid , "pem.status" => 1))
            ->get()->row();

        $data['exporerror'] = $this->db->_error_message();
        $homephone = '--';
        $workphone = '--';
        $cellphone = '--';
        $emailaddress = '--';
        if($sql){
            $getpersoncontact = $this->db->select("types,contactstring")->from("person_contact_matrix")
                ->where(array("personid" => $sql->personid , "status" => 1))
                ->get();
            if($getpersoncontact->num_rows() > 0){
                foreach ($getpersoncontact->result() as $contactrow){
                    if($contactrow->types == 1049){
                        $homephone = $contactrow->contactstring;
                    }
                    if($contactrow->types == 1050){
                        $workphone = $contactrow->contactstring;
                    }
                    if($contactrow->types == 1051){
                        $cellphone = $contactrow->contactstring;
                    }
                    if($contactrow->types == 1053){
                        $emailaddress = $contactrow->contactstring;
                    }
                }
            }
        }

        $getpositions = $this->db->select("ptp.names,pemp.status")->from("prime_employee_main_positions as pemp")
            ->join("prime_types_parameter as ptp","ptp.sysid = pemp.position_id","left")
            ->where(array("pemp.emp_id" => $empid))
            ->get();

        $getdepartments = $this->db->select("pcm.desc")->from("prime_employee_costcenter as pec")
            ->join("prime_costcenter_main as pcm","pcm.sysid = pec.ccid","left")
            ->where(array("pec.empid" => $empid))
            ->group_by("pec.ccid")
            ->get();


        $dateend = ($sql && $sql->dateend != '') ? $sql->dateend: 'N/A';
        $nationality = ($sql && $sql->nationality != '') ? $sql->nationality: 'N/A';
        $datestart = ($sql && $sql->datestart != '') ?$sql->datestart: 'N/A';
        $country = ($sql && $sql->country != '') ? $sql->country: 'N/A';
        $district = ($sql && $sql->district != '') ? $sql->district: 'N/A';
        $bioid = ($sql && $sql->bioid != '') ? $sql->bioid: 'N/A';
        $maritalstat = ($sql && $sql->maritalstatus != '') ? $sql->maritalstatus: 'N/A';
        $provincialcity = ($sql && $sql->provincialcity != '') ? $sql->provincialcity: 'N/A';
        $gender = ($sql && $sql->gender != '') ? $sql->gender: 'N/A';
        $jobpos = ($sql && $sql->jobposition != '') ? $sql->jobposition: 'N/A';
        $department = ($sql && $sql->department != '') ? $sql->department: 'N/A';
        $birthdate = ($sql && $sql->birthdate != '') ? $sql->birthdate: 'N/A';
        $addressspec = ($sql && $sql->addrspec != '') ? $sql->addrspec: 'N/A';
        $empid = ($sql && $sql->empid != '') ? $sql->empid: 'N/A';
        $status =($sql && $sql->status == 1) ? 'Active' : 'Inactive';
        $duration = ($sql &&get_emp_duration( $sql->sysid)->timespent) ? get_emp_duration($sql->sysid)->timespent : 'N/A';
        $jobcat = ($sql && select_emp_jobcat( $sql->sysid)->names) ? select_emp_jobcat($sql->sysid)->names : 'N/A';
        $empclass = ($sql && select_emp_payclass( $sql->sysid)->names != '' && select_emp_payclass( $sql->sysid)->names != null) ? select_emp_payclass($sql->sysid)->names : 'N/A';
        $name = ($sql) ? $sql->lastname.','.$sql->firstname : '';
        $zipcode = ($sql && $sql->zipcode != '') ? $sql->zipcode: 'N/A';
        $person_pic = get_owner_pic($sql->personid, 'person');

        $html .= '<div class="container">';

        $html .= '<div class="row">';
        $html .= '<h4 style="text-align: center;" class="bold">'.$name.'</h4>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="container">';
        $html .= '<div class="row">';
        $html .= '<div class="col-xs-3 col-sm-3 col-md-3">';
        $html .= '<div class="tab-pane fade in active" id="profilepic">
                        <div style="padding: 10px 10px;">
                            <img id="emppic" src="'.$person_pic.'" class="img-responsive" alt="">
                        </div>
                      </div>';
        $html .= '</div>';
        $html .= '<div class="col-xs-9 col-sm-9 col-md-9">';

        $html .= '<div class="row">';
        $html .= '<div class="col-xs-4 col-sm-4 col-md-4">';
        $html .= '<div><label>Employee Number:</label></div>';
        $html .= '<div><label>Address:</label></div>';
        $html .= '<div><label>Birthdate:</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>Home Phone:</label></div>';
        $html .= '<div><label>Work Phone:</label></div>';
        $html .= '<div><label>Cell Phone:</label></div>';
        $html .= '<div><label>Fax:</label></div>';
        $html .= '<div><label>Email Address:</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>Department:</label></div>';
        $html .= '<div><label>Position:</label></div>';
        $html .= '<div><label>Gender:</label></div>';
        $html .= '<div><label>Status:</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>Provincial Address:</label></div>';
        $html .= '<div><label>Civil Status:</label></div>';
        $html .= '<div><label>FingerPrint Assigned:</label></div>';
        $html .= '<div><label>Date Hired:</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>Payclass:</label></div>';
        $html .= '<div><label>Nationality:</label></div>';
        $html .= '<div><label>Country:</label></div>';
        $html .= '<div><label>District:</label></div>';
        $html .= '<div><label>Status:</label></div>';
        $html .= '<div><label>Tenure:</label></div>';
        $html .= '<div><label>Zip code:</label></div>';
        $html .= '</div>';
        $html .= '<div class="col-xs-8 col-sm-8 col-md-8">';
        $html .= '<div><label>'.$empid.'</label></div>';
        $html .= '<div><label>'.$addressspec.'</label></div>';
        $html .= '<div><label>'.$birthdate.'</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>'.$homephone.'</label></div>';
        $html .= '<div><label>'.$workphone.'</label></div>';
        $html .= '<div><label>'.$cellphone.'</label></div>';
        $html .= '<div><label>--</label></div>';
        $html .= '<div><label>'.$emailaddress.'</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>'.$department.'</label></div>';
        $html .= '<div><label>'.$jobpos.'</label></div>';
        $html .= '<div><label>'.$gender.'</label></div>';
        $html .= '<div><label>'.$status.'</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>'.$provincialcity.'</label></div>';
        $html .= '<div><label>'.$maritalstat.'</label></div>';
        $html .= '<div><label>'.$bioid.'</label></div>';
        $html .= '<div><label>'.$datestart.'</label></div>';
        $html .= '<hr>';
        $html .= '<div><label>'.$empclass.'</label></div>';
        $html .= '<div><label>'.$nationality.'</label></div>';
        $html .= '<div><label>'.$country.'</label></div>';
        $html .= '<div><label>'.$district.'</label></div>';
        $html .= '<div><label>'.$jobcat.'</label></div>';
        $html .= '<div><label>'.$duration.'</label></div>';
        $html .= '<div><label>'.$zipcode.'</label></div>';
        $html .= '<hr>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="row">';
        $html .= '<div class="col-md-3 col-md-offset-3">';
        $html .= '<table class="table table-responsive table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Positions</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        if($getpositions->num_rows() > 0){
            $num = 1;
            foreach ($getpositions->result() as $row){
                $data['postlist'][] = array(
                    'list' => $row->names
                );
                if($row->status == 1){
                    $names = '<span class="label label-sm label-success">'.$row->names.'</span>';
                }else{
                    $names = $row->names;
                }
                $html .= '<tr>';
                $html .= '<td>'.$num++.'</td>';
                $html .= '<td>'.$names.'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '<div class="col-md-4">';
        $html .= '<table class="table table-responsive table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Departments</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        if($getdepartments->num_rows() > 0){
            $num = 1;
            foreach ($getdepartments->result() as $row){
                $data['postlist'][] = array(
                    'list' => $row->desc
                );
                $html .= '<tr>';
                $html .= '<td>'.$num++.'</td>';
                $html .= '<td>'.$row->desc.'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';



        $html .= '</div>';

        $data['Employee Number'] = $empid;
        $data['Address'] = $addressspec;
        $data['Birthdate'] = $birthdate;
        $data['Department'] = $department;
        $data['Position'] = $jobpos;
        $data['Gender'] = $gender;
        $data['Status'] = $status;
        $data['Provincial Address'] = $provincialcity;
        $data['Civil Status'] = $maritalstat;
        $data['Finger Print'] = $bioid;
        $data['Start Date'] = $datestart;
        $data['End Date'] = $dateend;
        $data['Payclass'] = $empclass;
        $data['Nationality'] = $nationality;
        $data['Country'] = $country;
        $data['District'] = $district;
        $data['Job Cat'] = $jobcat;
        $data['Duration'] = $duration;
        $data['html'] = $html;

        echo json_encode($data);
    }
    function getapprovedrequest(){
        $data = array();
        $empid = $this->input->post("empid");
        /*
        $sql = $this->db->select("ptp.desc ,telr.from , telr.to, telra.status, telra.reason")
            ->from("trn_employee_leave_requests as telr")
            ->join("trn_employee_leave_requests_approval as telra" , "telra.sysid = telr.groupid" , "left")
            ->join("prime_employee_main_leave_credits as pemlc" , "pemlc.empid  = telr.empid" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid  = pemlc.types" , "left")
            ->where(array("telr.groupid" => $empid , "telr.status !=" => 307 ))
            ->get();
        */

        $sql = $this->db->select('ptp.desc ,telr.from , telr.to, telra.status, telra.reason')
            ->from('trn_employee_leave_requests as telr')
            ->join("trn_employee_leave_requests_approval as telra" , "telra.sysid = telr.groupid" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid  = telr.leavetype" , "left")
            ->where(array("telr.groupid" => $empid , "telr.status !=" => 307 ))
            ->get();

        $qry = $this->db->last_query();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                if($row->status == 301){
                    $status = '<a href="javascript:;" class="label tooltips" data-placement="top" title="Approved" style="background: #b7ffb1; color: #170405"><i class="fa fa-check"></i> Approved </a>';
                }else if($row->status == 302){
                    $status = '<a href="javascript:;" class="label tooltips" data-placement="top" title="Disapproved" style="background: #ff0000; color: #FFFFFF"><i class="fa fa-times-circle"></i> Disapproved </a>';
                }else if($row->status == 300){
                    $status = '<a href="javascript:;" class="label tooltips" data-placement="top" title="Pending" style="background: #ffce4e; color: #FFFFFF"><i class="fa fa-warning"></i> Pending </a>';
                }
                $data['approvereqdata'][] = array(
                    "num" => $num++,
                    "leavetype" => $row->desc,
                    "from" => $row->from,
                    "to" => $row->to,
                    "reason" => $row->reason,
                    "status" => $status
                );
            }
        }

        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function activateemployee(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearray = array(
            'status' => 1
        );
        $this->db->where(array("sysid" => $dataid,"status" => 0));
        $sql = $this->db->update("prime_employee_main" , $updatearray);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Employee has been activated.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to activate employee.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function filteremployee(){
        $data = array();
        $html = '';
        $searchval = $this->input->post('searchval');
        $sql = $this->db->select("pem.sysid,p.lastname,p.firstname , ptp.names")->from("person as p")
            ->join("prime_employee_main as pem" , "pem.personid = p.sysid" , "left")
            ->join("prime_employee_main_positions as pemp" , "pemp.emp_id = pem.sysid","left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemp.position_id","left")
            ->where(array("pem.status" => 1 ,"ptp.codes" => 'EMPOST' , "pemp.status" => 1))
            ->or_like('p.firstname', $searchval)
            ->limit(5)
            ->get();
        if($sql->num_rows() > 0){
            $html .= ' <dl>';
            foreach ($sql->result() as $row){
                $html .= '
                <a id="searchlinkclick" data-id="'.$row->sysid.'"><dt>'.$row->lastname.', '.$row->firstname.'</dt>
  <dd>- '.$row->names.'</dd></a>
                ';
            }
            $html .= '</dl>';
        }
        if($searchval == ''){
            $html = '';
        }
        $data['html'] = $html;
        echo json_encode($data);
    }
    function getallemployeeschedule(){
        $data = array();
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $html .= '<table class="table table-bordered table-responsive tbl-sm">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Workshift Schedule</th>';
        $html .= '<th>From</th>';
        $html .= '<th>To</th>';
        $html .= '<th>Date Created</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $monthdata = $this->input->post('monthdata');
        $yeardata = $this->input->post('yeardata');
        $sql = $this->db->select("tewg.empid,p.lastname,p.firstname,pemw.desc,tewg.fromdate, tewg.todate, tewg.datecreated")->from("trn_employee_workshift_group as tewg")
            ->join("prime_employee_main as pem" , "pem.sysid = tewg.empid" , "left")
            ->join("person as p","p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_workshift as pemw" , "pemw.sysid = tewg.workshiftid" , "left")
            ->where(array("tewg.status" => 301 , "pem.status" => 1,"MONTH(tewg.fromdate)" => $monthdata , "YEAR(tewg.fromdate)" => $yeardata))
            ->order_by("tewg.datecreated" , "DESC")
            ->get();
        if($sql->num_rows() > 0){
            $num  = 1;


            foreach ($sql->result() as $row){
                $html .= '<tr>';
                $html .= '<td>'.$num.'</td>';
                $html .= '<td>'.$row->lastname.', '.$row->firstname.'</td>';
                $html .= '<td>'.$row->desc.'</td>';
                $html .= '<td>'.$row->fromdate.'</td>';
                $html .= '<td>'.$row->todate.'</td>';
                $html .= '<td>'.$row->datecreated.'</td>';
                $html .= '</tr>';

                $data['regularempscheddata'][] = array(
                    "num" => $num++,
                    "name" => $row->lastname.', '.$row->firstname,
                    "shift" => $row->desc,
                    "fromdate" => $row->fromdate,
                    "todate" => $row->todate,
                    "datecreated" => $row->datecreated
                );

            }

        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        $data['html'] = $html;
        echo json_encode($data);
    }
    function fetchleavereports(){
        $data = array();
        $status = $this->input->post('status');
        $sql = $this->db->select("
            ptp.desc,
            p.lastname,
            p.firstname,
            telr.from,
            telr.to,
            telr.hours,
            telr.datecreated,
            telr.status
        ")->from("trn_employee_leave_requests as telr")
            ->join("prime_employee_main_leave_credits as pemlc","pemlc.sysid = telr.creditid","left")
            ->join("prime_types_parameter as ptp","ptp.sysid = pemlc.types","left")
            ->join("prime_employee_main as pem","pem.sysid = telr.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->where(array("telr.status" => $status))
            ->get();
        $data['leaveerror'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                if($row->status == 301){
                    $statuslabel = '<span class="label label-sm label-success"> Approved </span>';
                }else{
                    $statuslabel = '';
                }
                $data['hrisleavedata'][] = array(
                    "num" => $num++,
                    "empid" => $row->lastname.', '.$row->firstname,
                    "leave" => $row->desc,
                    "fromdate" => $row->from,
                    "todate" => $row->to,
                    "hours" => $row->hours,
                    "datecreated" =>$row->datecreated,
                    "status" =>$statuslabel
                );
            }
        }
        echo json_encode($data);
    }
    function getalltardinessrep(){
        $data =array();
        $monthdata = $this->input->post('monthdata');
        $yeardata = $this->input->post('yeardata');
        $data['month'] = $monthdata;
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
        $html .= '<table class="table table-bordered table-responsive table-striped tbl-sm">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Employee</th>';
        $html .= '<th>Workshift Assigned</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>AM IN</th>';
        $html .= '<th>AM OUT</th>';
        $html .= '<th>AM LATE</th>';
        $html .= '<th>PM IN</th>';
        $html .= '<th>PM OUT</th>';
        $html .= '<th>PM LATE</th>';
        $html .= '<th>TOTAL LATE</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        if($monthdata > 0){
            $this->db->where(array("ar.month" => $monthdata));
        }
        if($yeardata > 0){
            $this->db->where(array("ar.year" => $yeardata));
        }
        $sql = $this->db->select("p.lastname,pemw.desc,p.firstname,ar.attdate,ar.amin,ar.amout,ar.pmin,ar.pmout,ar.totallate,ar.amlate,ar.pmlate")->from("attendance_reports as ar")
            ->join("prime_employee_main as pem","pem.sysid = ar.empid","left")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("prime_employee_main_workshift as pemw" , "pemw.sysid = ar.workshift","left")
            ->where(array("ar.charge !=" => 0,"ar.totallate !=" => '00:00:00'))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $html .= '<tr>';
                $html .= '<td>'.$num.'</td>';
                $html .= '<td>'.$row->lastname.', '.$row->firstname.'</td>';
                $html .= '<td>'.$row->desc.'</td>';
                $html .= '<td>'.$row->attdate.'</td>';
                $html .= '<td>'.$row->amin.'</td>';
                $html .= '<td>'.$row->amout.'</td>';
                $html .= '<td>'.$row->amlate.'</td>';
                $html .= '<td>'.$row->pmin.'</td>';
                $html .= '<td>'.$row->pmout.'</td>';
                $html .= '<td>'.$row->pmlate.'</td>';
                $html .= '<td>'.$row->totallate.'</td>';
                $html .= '</tr>';
                $data['tardinessdata'][] = array(
                    "num" => $num++,
                    "empid" => $row->lastname.', '.$row->firstname,
                    "workshiftass" => $row->desc,
                    "datelog" => $row->attdate,
                    "amin" => $row->amin,
                    "amout"=> $row->amout,
                    "amlate"=> $row->amlate,
                    "pmin"=> $row->pmin,
                    "pmout"=> $row->pmout,
                    "pmlate"=> $row->pmlate,
                    "total"=> $row->totallate,
                );
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        $data['html'] = $html;
        echo json_encode($data);
    }
    function select2year(){
        $data = array();

        for($year = 2018;$year <= 4000;$year++){
            $data['list'][] = array(
                'id' => $year,
                'text' =>''.'-'.$year
            );

        }

        echo json_encode($data);
    }
    function getrelations(){
        $data = array();

        $sql = $this->db->select("sysid,code,descriptions")->from("prime_data_relations")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id'=>$row->sysid,
                    'text'=>$row->code.'-'.$row->descriptions
                );
            }
        }

        echo json_encode($data);
    }
    function adddependents(){
        $data = array();
        $firstname = $this->input->post('dependentsname');
        $birthdate = $this->input->post('birthdatedependents');
        $relation = $this->input->post('relationdependents');
        $userid = $this->input->post('userid');
        $this->db->trans_begin();
        $insarr = array(
            'empid' => $userid,
            'name' => $firstname,
            'birthdate' => $birthdate,
            'relation' => $relation,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert("prime_employee_dependents" , $insarr);
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Dependent saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add dependent.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function fetchdependents(){
        $data = array();
        $userid = $this->input->post('userid');
        $sql = $this->db->select("d.sysid,d.name,d.birthdate,r.descriptions")
            ->from("prime_employee_dependents as d")
            ->join("prime_data_relations as r","r.sysid = d.relation","left")
            ->where(array("d.status" => 1,"d.empid" => $userid))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['dependentsdata'][] = array(
                    "num" => $num++,
                    "name" => $row->name,
                    "birthdate" => $row->birthdate,
                    "relation" => $row->descriptions
                );
            }
        }
        echo json_encode($data);
    }
    function select2agencies(){
        $data = array();
        $sql = $this->db->select("sysid,code,desc")->from("prime_data_agencies")->order_by("sysid","desc")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id'=>$row->sysid,
                    'text'=>$row->code.'-'.$row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function getalldepartments(){
        $data = array();
        $sql = $this->db->select("sysid,codes,names,desc,address")->from("prime_costcenter_main")
            ->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['departmentsdata'][] = array(
                    "num" => $num++,
                    "codes" => $row->codes,
                    "names" => $row->names,
                    "desc" => $row->desc,
                    "address" => $row->address
                );
            }
        }
        echo json_encode($data);
    }
    function fetchpositions(){
        $data = array();
        $sql=  $this->db->select("sysid,codes,names,desc")->from("prime_types_parameter")
            ->where(array("codes" => 'EMPOST',"status" => 1))->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['positionsdata'][] = array(
                    "num" => $num++,
                    "codes" => $row->codes,
                    "names" => $row->names,
                    "desc" => $row->desc,
                    "control" => '<button id="deleteposbtn" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-trash"></i></button>'
                );
            }
        }
        echo json_encode($data);
    }
    function adddepartment(){
        $data = array();
        $codes = $this->input->post('codes');
        $name = $this->input->post('name');
        $desc = $this->input->post('desc');
        $floor = $this->input->post('floor');

        $this->db->trans_begin();
        $insarr = array(
            'codes' => $codes,
            'names' => $name,
            'desc' => $desc,
            'address' => $floor,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("prime_costcenter_main" , $insarr);
        $data['errordepartment'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Department has been added';
            $func = 'success';
            $qry =  true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add department';
            $func = 'error';
            $qry =  false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function addpositions(){
        $data = array();

        $name = $this->input->post('names');
        $desc = $this->input->post('descriptions');


        $this->db->trans_begin();
        $insarr = array(
            'codes' => 'EMPOST',
            'names' => $name,
            'desc' => $desc,

        );
        $sql = $this->db->insert("prime_types_parameter" , $insarr);
        $data['errorposition'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Position has been added';
            $func = 'success';
            $qry =  true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add position';
            $func = 'error';
            $qry =  false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function select2department(){
        $data = array();
        $sql = $this->db->select("sysid,codes,desc")->from("prime_costcenter_main")
            ->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function select2pos(){
        $data = array();
        $sql = $this->db->select("sysid,codes,desc")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'EMPOST'))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function select2payclass(){
        $data = array();
        $sql = $this->db->select("sysid,codes,desc")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'EMPAYCLASS'))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ''.'-'.$row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function select2jobcat(){
        $data = array();
        $sql = $this->db->select("sysid,codes,desc")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'EMPJOBCAT'))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ''.'-'.$row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function select2marital(){
        $data =array();

        $sql = $this->db->select("sysid,codes,names")->from("marital")->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.'-'.$row->names
                );
            }
        }

        echo json_encode($data);
    }
    function select2city(){
        $data =array();

        $sql = $this->db->select("sysid,codes,names")->from("address_city")->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.'-'.$row->names
                );
            }
        }
        echo json_encode($data);
    }
    function select2district(){
        $data =array();

        $sql = $this->db->select("sysid, codes, names")
            ->from("address_districts")
            ->where(array("status" => 1,"types" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        echo json_encode($data);
    }
    function select2nationality(){
        $data =array();

        $sql = $this->db->select("sysid,ccode,nationality")->from("address_country")->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->ccode.'-'.$row->nationality
                );
            }
        }
        echo json_encode($data);
    }
    function saveholiday(){
        $data = array();
        $dateholiday = $this->input->post('dateholiday');
        $holidaydesc = $this->input->post('holidaydesc');
        $this->db->trans_begin();
        $insarr = array(
            'descs' => $holidaydesc,
            'dateholiday' => $dateholiday,
            'createdby' => user_id(),
            'status' => 1
        );
        $sql = $this->db->insert("prime_main_holiday" , $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Holiday has been saved';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add holiday';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function fetchholidaysentry(){
        $data = array();

        $sql = $this->db->select("")->from("prime_main_holiday")
            ->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['holidaydata'][] = array(
                    'num' => $num++,
                    'date' => $row->dateholiday,
                    'desc' => $row->descs,
                    'datecreated' => $row->datecreated,
                    'createdby' =>$row->createdby,
                    'control' => '<button type="button" data-id="'.$row->sysid.'" class="btn btn-xs btn-danger" id="deleteholidays"><i class="fa fa-trash"></i></button>',
                );
            }
        }
        echo json_encode($data);
    }
    function removeholiday(){
        $data = array();

        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("prime_main_holiday" , $updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Holiday has been removed';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove holiday';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function getleavereports(){
        $data = array();
        /*   $html = '';
        $html = '<div class="row">';
        $html = '<div class="col-md-12">';
        $html = '<table>';
        $html = '<thead>';
        $html = '<th></th>';
        $html = '<th>Employee</th>';
        $html = '<th>Leave Type</th>';
        $html = '<th>Credits</th>';
        $html = '<th>Spent</th>';
        $html = '<th>Balance</th>';
        $html = '</thead>';
        $html = '<tbody>'; */
        $sql = $this->db->select("p.lastname,p.firstname,pemlc.credit,telr.totalinhours")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->join("trn_employee_leave_requests as telr" , "telr.empid = pem.sysid","left")
            ->join("trn_employee_leave_requests_approval as telra","telra.trnreqid = telr.sysid","left")
            ->join("prime_employee_main_leave_credits as pemlc","pemlc.empid = pem.sysid","left")
            ->where(array("pem.type" => 1 , "pem.status" => 1,"telr.status" => 301,"telra.status" => 301,"pemlc.status" => 1))
            ->order_by("p.lastname")
            ->get();
        $data['error'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                /* $html = '<tr>';
                $html = '<td></td>';
                $html = '<td></td>';
                $html = '<td></td>';
                $html = '<td></td>';
                $html = '<td></td>';
                $html = '<td></td>';
                $html = '</tr>'; */
                $data['list of employee'][] = array(
                    'name' => $row->lastname.', '.$row->firstname,
                    'credit' => $row->credit,
                    'totalinhours' => $row->totalinhours,
                );
            }
        }
        /*   $html = '</tbody>';
        $html = '</table>';
        $html = '</div>';
        $html = '</div>';
        $data['html'] = $html; */
        echo json_encode($data);
    }
    function deletepremiums(){
        $data = array();
        $empid = $this->input->post('empid');
        $premiumid = $this->input->post('premiumid');
        $this->db->trans_begin();
        $this->db->set('a.status', 0);
        $this->db->set('b.status', 0);

        $this->db->where('a.sysid', $premiumid);
        $this->db->where('a.empid', $empid);
        $this->db->where('b.empid', $empid);
        $this->db->where('a.sysid = b.groupid');
        $sql = $this->db->update('payroll_manual_transactions as a, payroll_manual_transactions_breakdown as b');
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Premium has been deleted successfully.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to delete premium';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function deleteloans(){
        $data = array();
        $empid = $this->input->post('empid');
        $loanid = $this->input->post('loanid');
        $this->db->trans_begin();
        $this->db->set('a.status', 0);
        $this->db->set('b.status', 0);

        $this->db->where('a.sysid', $loanid);
        $this->db->where('a.empid', $empid);
        $this->db->where('b.empid', $empid);
        $this->db->where('a.sysid = b.groupid');
        $sql = $this->db->update('payroll_manual_transactions as a, payroll_manual_transactions_breakdown as b');
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Loan has been deleted successfully.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to delete loan';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function deletededuction(){
        $data = array();
        $empid = $this->input->post('empid');
        $deductionid = $this->input->post('deductionid');
        $this->db->trans_begin();

        $this->db->set('a.status', 0);
        $this->db->set('b.status', 0);

        $this->db->where('a.sysid', $deductionid);
        $this->db->where('a.empid', $empid);
        $this->db->where('b.empid', $empid);
        $this->db->where('a.sysid = b.groupid');
        $sql = $this->db->update('payroll_manual_transactions as a, payroll_manual_transactions_breakdown as b');
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Deduction has been deleted successfully.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to delete deduction';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function leaveemployee(){
        $data = array();

        $sql = $this->db->select("pem.sysid,p.lastname,p.firstname")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->where(array("pem.type" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }

        echo json_encode($data);
    }

    function select2empstatus()
    {
        $empid = $this->input->post('data');

        $stats = get_employee_info($empid)->empstatus;

        if ($stats == 1) {
            echo get_types_select('EMPSTATUS');
        } else {
            $data = array();

            $active[] = array('id' => 1, 'text' => 'Activate - Activate');
            $empstatus = get_types_select('EMPSTATUS');
            $list = json_decode($empstatus)->list;
            $data['list'] = array_merge($active,$list);

            echo json_encode($data);
        }
    }

    function getemployeelogs() {
        echo $this->model_hris->get_employee_logs();
    }

    function addemployeelog() {
        echo $this->model_hris->add_employee_log();
    }

    function delemplog() {
        echo $this->model_hris->del_employee_log();
    }

    // ######## SELECT2 MONTH w/ CODES ###############
    function selectpayslipmonth()
    {
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
    function submitlocator(){
        $data = array();
        $locatordate = $this->input->post('locatordate');
        $locatorfor = $this->input->post('locatorfor');
        $locatorbreakout = $this->input->post('locatorbreakout');
        $locatorbreakin = $this->input->post('locatorbreakin');
        $locatorreason = $this->input->post('locatorreason');
        $locatortype = $this->input->post('locatortype');
        $empid = $this->input->post('empid');
        $this->db->trans_begin();
        $insarr = array(
            'locatortype' => $locatortype,
            'empid' => $empid,
            'date' => $locatordate,
            'for' => $locatorfor,
            'breakout' => $locatorbreakout,
            'breakin' => $locatorbreakin,
            'purpose' => $locatorreason,
            'createdby' => user_id(),
            'updatedby' => user_id(),
        );
        $sql = $this->db->insert("trn_employee_locator_requests" , $insarr);
        $data['error'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Locator request has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to request locator.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function exportnewempid() {
        $qry = $this->db->query("
            SELECT pem.sysid, p.lastname , p.firstname, pem.empid, YEAR(pem.datestart) AS yearstart, MONTH(pem.datestart) AS monthstart FROM person as p 
            LEFT JOIN prime_employee_main as pem ON pem.personid = p.sysid 
            WHERE pem.status = 1 AND pem.type = 1
            ORDER BY pem.sysid
        ");
        $html = '';
        $html .= '<table>';
        $html .= '<thead><th>Lastname</th><th>Firstname</th><th>Old Emp ID</th><th>New Emp ID</th></thead>';
        $html .= '<tbody>';
        foreach($qry->result() as $row) {
            $new_empid = $row->yearstart .'--'. str_pad($row->monthstart, 2, '0', STR_PAD_LEFT) .'--'. str_pad($row->sysid, 4, '0', STR_PAD_LEFT);
            $html .= '<tr>';
            $html .= '<td>'.$row->lastname.'</td>';
            $html .= '<td>'.$row->firstname.'</td>';
            $html .= '<td>'.$row->empid.'</td>';
            $html .= '<td>'.$new_empid.'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        echo $html;
    }
    function getallemployeesfordtr(){
        $data = array();
        $term = $this->input->post('term');
        $sql = $this->db->select("pem.sysid , p.lastname,p.firstname,p.middlename")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->or_like('p.lastname', $term)
            ->or_like('p.firstname', $term)
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }
        echo json_encode($data);
    }
    function getallemployeesforexeceval(){
        $data = array();
        $term = $this->input->post('term');
        $sql = $this->db->select("pem.sysid , p.lastname,p.firstname,p.middlename")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->where(array("pem.status" => 1))
            ->or_like('p.lastname', $term)
            ->or_like('p.firstname', $term)
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }
        echo json_encode($data);
    }
    function submitdtr(){
        $data = array();
        $emparr = $this->input->post('employees');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $from = $this->input->post('from');
        $counter = $from;
        $to = $this->input->post('to');
        $spacing = $this->input->post('spacing');
        $fromdate = $year.'-'.$month.'-'.$from;
        $todate = $year.'-'.$month.'-'.$to;
        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');
        $html = '';
        $html .= '<div class="container-fluid">';
        $html .= '<div class="row">';

        foreach ($emparr as $value){
            $explodearr =   explode(',', $value);
            for($i = 0; $i < count($explodearr) ; $i++){
                $empid = $explodearr[$i];
                $getempinfo = $this->db->select("p.lastname,p.firstname , ptp.names as position , peb.bioid")->from("prime_employee_main as pem")
                    ->join("person as p","p.sysid = pem.personid" , "left")
                    ->join("prime_employee_main_positions as pemp" , "pemp.emp_id = pem.sysid" , "left")
                    ->join("prime_types_parameter as ptp" , "ptp.sysid = pemp.position_id","left")
                    ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid" , "left")
                    ->where(array("pem.sysid" => $empid , "peb.status" => 1 , "pemp.status" => 1 ))->get()->row();
                $data['error1'] = $this->db->_error_message();
                if($getempinfo){
                    $name = $getempinfo->lastname.', '.$getempinfo->firstname;
                    $position = $getempinfo->position;
                    $bioid = $getempinfo->bioid;
                }else{
                    $name = '';
                    $position = '';
                    $bioid = '';
                }
                if($spacing){
                    if($month != 2){
                        $html .= '<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6" style="margin-bottom: 350px !important;">';
                    }else{
                        $html .= '<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6" style="margin-bottom: 360px !important;">';
                    }

                }else{
                    $html .= '<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">';
                }

                $html .= '<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">';
                $html .= '<div class="form-group">';
                $html .= '<label>Name: </label>';
                $html .= '<span>'.$name.'</span>';
                $html .= '</div>';


                $html .= '<div class="form-group">';
                $html .= '<label>Position: </label>';
                $html .= '<span style="font-size: 10px;">'.$position.'</span>';
                $html .= '</div>';

                $html .= '<div class="form-group pull-right">';
                $html .= '<label>BIOID: </label>';
                $html .= '<span>'.$bioid.'</span>';
                $html .= '</div>';


                $html .= '<div class="form-group">';
                $html .= '<label>Date: </label>';
                $html .= '<span>'.$monthName.' '.$from.'-'.$to.' '.$year.'</span>';
                $html .= '</div>';

                $html .= '</div>';
                $html .= '<table class="table   table-responsive tbl-xs">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th rowspan="2" width="10px;"></th>';
                $html .= '<th colspan="2" class="info"></th>';
                $html .= '<th colspan="2" class="warning"></th>';
                $html .= '<th colspan="4" class="warning"></th>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>TIME 1</th>';
                $html .= '<th>TIME 2</th>';
                $html .= '<th>TIME 3</th>';
                $html .= '<th>TIME 4</th>';
                $html .= '<th>  </th>';
                $html .= '<th>  </th>';
                $html .= '<th>  </th>';
                $html .= '<th>  </th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';

                $begin = new DateTime($fromdate);
                $end = new DateTime($todate);
                $end->modify('+1 day');

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
                $num = 1;
                foreach ($period as $dt) {
                    $datelist = $dt->format("Y-m-d");
                    $this_date = date("l", strtotime($datelist));
                    $html .= '<tr>';
                    $html .= '<td>'.$counter++.'</td>';
                    $gettime = $this->db->select("logtime")->from("prime_employee_attendance_timelogs")
                        ->where(array("bioid" => $bioid , "logdate" => $datelist))->limit(4)
                        ->order_by("logtime")
                        ->get();
                    if($gettime->num_rows() > 0){

                        foreach ($gettime->result() as $timelist){
                            $html .= '<td>'.date('g:i', strtotime($timelist->logtime)).'</td>';
                        }
                    }else{
                        if ($this_date == "Saturday" || $this_date == "Sunday") {
                            // $html .= '<td style="background-color: #f6c1c4"></td>';
                            //  $html .= '<td style="background-color: #f6c1c4;"></td>';
                            // $html .= '<td style="background-color: #f6c1c4;"></td>';
                            //  $html .= '<td style="background-color: #f6c1c4;"></td>';
                        }
                    }

                    $html .= '</tr>';

                }
                $counter = $from;
                $html .= '</tbody>';

                $html .= '</table>';
                $html .= '<br>';



                $html .= '</div>';
            }

        }


        $html .= '</div>';
        $html .= '</div>';

        $data['html'] =  $html;

        echo json_encode($data);
    }

    function testpicture() {
        //echo get_owner_pic(159, 'person');
        //exit();

        $check_primary_file = glob(FCPATH . 'uploads/person/159/primary*.*');
        $files_arr = array();
        $i = 0;
        $pic = '';

        rsort($check_primary_file);
        foreach($check_primary_file as $row) {
            $files_arr[] = array(
                'filedate' => date ("Y-m-d H:i:s.", filemtime($row)),
                'filename' => $row,
            );

            if($i==0) {
                $pic = $row;
            }
            $i++;
        }
        rsort($files_arr);
        echo $pic;
        echo '<pre>';
        print_r($files_arr);
    }
    function getlocatorapporval(){
        $data= array();
        $empid = user_id();
        $sql = $this->db->select("tela.approval,psu.lastname , psu.firstname")->from("trn_employee_locator_approval as tela")
            ->join("prime_system_users as psu" , "psu.sysid = tela.approval" , "left")
            ->where(array("tela.empid" => $empid , "tela.status" => 1))
            ->get();
        $data['err'] = $this->db->_error_message();
        $data['empid'] = $empid;
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->approval,
                    'text' => $row->lastname.'-'.$row->firstname
                );
            }
        }
        echo json_encode($data);
    }
    function getemployees(){
        $data=  array();
        $active = $this->input->post('data');
        if($active > 0){
            $this->db->where(array("pem.status" => 1));
        }
        $sql = $this->db->select("pem.sysid ,p.lastname,p.firstname")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            // ->where(array("pem.status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.' - '.$row->firstname
                );
            }
        }

        echo json_encode($data);
    }

    function getleavetype(){
        $data = array();

        $leavetypearr = array(
            '1' => 'Regular Leave',
            '2' => 'Locator Leave'
        );

        foreach ($leavetypearr as $key => $value) {
            $data['list'][] = array(
                'id' => $key,
                'text' => $value
            );
        }

        echo json_encode($data);
    }
    function getleaveminutes(){
        $data = array();

        $empid = $this->input->post('empid');

        $sql = $this->db->select("SUM(totalinminutes) AS totalmin")->from("trn_employee_leave_requests")
            ->where(array("empid" => $empid , "YEAR(datecreated)" => date('Y') , "status" => 301))
            ->get()->row();
        if($sql){
            if($sql->totalmin != null){
                $data['minutes'] = $sql->totalmin;
            }else{
                $data['minutes'] = 0;
            }

        }else{
            $data['minutes'] = 0;
        }

        echo json_encode($data);
    }
    function getempcredits(){
        $data = array();


        $print = $this->input->post('print');
        $payclassarr = array(128, 129, 130, 156, 250, 267, 3073, 3077, 3078);
        $year_input = $this->input->post('year');
        $ccid = $this->input->post('ccid');
        $jobcat = $this->input->post('jobcat');
        $year = ($year_input && $year_input > 0) ? $year_input : date('Y');

        if ($jobcat) {
            if ($jobcat == 1) {
                $leavereparr = array(330, 331, 332, 333, 334, 335);
            } else {
                $leavereparr = array(3213, 333, 334, 335);
            }
        }

        if($print > 0){
            $html = '';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">';
            $html .= '<table class="table table-condensed table-sm">';
            $html .= '<thead>';
            $html .= '<th></th>';
            $html .= '<th>Employee</th>';

            $head = $this->db->select("sysid , names")->from("prime_types_parameter")
                ->where(array("codes" => 'LEAVECREDITS' , "status" => 1))
                ->where_in("sysid" , $leavereparr)
                ->get();
            if($head->num_rows() > 0){
                foreach ($head->result() as $row){
                    $html .= '<th>'.$row->names.'</th>';
                }
            }
            $html .= '</thead>';
            $html .= '<tbody>';

        }
        if($ccid > 0){
            $this->db->where(array("pec.ccid" => $ccid));
        }
        $jobcatarr = array(157);
        if ($jobcat) {
            if ($jobcat == 1) {
                $payclassarr = array(128, 129, 130, 156, 250, 267, 3073);

            } else {
                if ($jobcat == 2) {
                    $payclassarr = array(3077, 3078);
                }
            }
        }
        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname")
            ->from("prime_employee_main_leave_credits as pemlc")
            ->join("prime_employee_main as pem" , "pem.sysid = pemlc.empid")
            ->join("person as p" , "p.sysid = pem.personid")
            ->join("prime_employee_costcenter as pec","pec.empid = pemlc.empid && pec.type = 1 && pec.status = 1")
            ->join("prime_employee_main_job_category as pemjc","pemjc.empid = pem.sysid" , "left")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid" , "left")
            ->where(array("pemlc.year" => $year , "pemlc.status" => 1))
            ->where_in("pemjc.jobcatid" , $jobcatarr)
            ->where_in("pemp.payclass_id" , $payclassarr)
            ->group_by("pem.sysid , p.lastname , p.firstname")
            ->order_by("p.lastname")
            ->get();

        $query = $this->db->last_query();

        if($sql->num_rows() > 0){
            $num = 1;
            $vl = '';
            $sl = '';
            $el = '';
            $ml = '';
            $pl = '';
            $ol = '';

            foreach ($sql->result() as $row){

                $getleavecolval = $this->db->select("sysid , names")->from("prime_types_parameter")
                    ->where(array("codes" => 'LEAVECREDITS' , "status" => 1))
                    ->where_in("sysid" , $leavereparr)
                    ->get();
                if($getleavecolval->num_rows()> 0){
                    foreach ($getleavecolval->result() as $rowval){

                        $empcredits = $this->db->select("SUM(credit) AS totalcredit")->from("prime_employee_main_leave_credits")
                            ->where(array("year" => date('Y') , "empid" => $row->sysid,"types" => $rowval->sysid , "status" => 1 , "year" => $year))
                            ->get()->row();


                        $getleavedetails = $this->db->select("SUM(totalinminutes) AS totalminutes")->from("trn_employee_leave_requests")
                            ->where(array("empid" => $row->sysid , "leavetype" => $rowval->sysid , "year" => $year , "status" => 301))
                            ->get()->row();

                        $totalbalance =$empcredits->totalcredit;

                        $dayspent = 0;
                        $hourspent = 0;
                        $minutespent = 0;

                        //SPENT
                        $totalspenthours = $getleavedetails->totalminutes / 60;
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
                        $daybalance = floor($totalbalancehours / 8);
                        $hourbalance =  ($totalbalancehours % 8);
                        $n = $totalbalancehours;
                        $whole = floor($n);      // 1
                        $minutebalance = ($n - $whole) * 60;

                        $daybalance_ = ($daybalance < 0 ) ? '('.($daybalance*(-1)).')' : $daybalance;
                        $hourbalance_ = ($hourbalance < 0 ) ? '('.($hourbalance*(-1)).')' : $hourbalance;
                        $minutebalance_ = (round($minutebalance) < 0 ) ? '('.(round($minutebalance)*(-1)).')' : round($minutebalance);

                        $totalbalanceleft = $daybalance_.'-'.$hourbalance_.'-'.$minutebalance_;

                        if($rowval->names == 'VL'){
                            $vl = $totalbalanceleft;
                        }else if($rowval->names == 'SL'){
                            $sl =$totalbalanceleft;
                        }else if($rowval->names == 'EL'){
                            $el = $totalbalanceleft;
                        }else if($rowval->names == 'ML'){
                            $ml = $totalbalanceleft;
                        }else if($rowval->names == 'PL'){
                            $pl =$totalbalanceleft;
                        }else if($rowval->names == 'OL'){
                            $ol = $totalbalanceleft;
                        }
                    }
                }

                if($print > 0) {
                    $html .= '<tr>';
                    $html .= '<td>' . $num . '</td>';
                    $html .= '<td>' . $row->lastname . ', ' . $row->firstname . '</td>';
                    $html .= '<td>' . $vl . '</td>';
                    $html .= '<td>' . $sl . '</td>';
                    $html .= '<td>' . $el . '</td>';
                    $html .= '<td>' . $ml . '</td>';
                    $html .= '<td>' . $pl . '</td>';
                    $html .= '<td>' . $ol.'</td>';
                    $html .= '</tr>';
                }
                $data['empcredits'][] = array(
                    'num' => $num++,
                    'name' => $row->lastname.', '.$row->firstname,
                    'VL' => $vl,
                    'SL' => $sl,
                    'EL' => $el,
                    'ML' => $ml,
                    'PL' => $pl,
                    'OL' => $ol,
                    'control' => '',
                );

                $vl = '';
                $sl = '';
                $el = '';
                $ml = '';
                $pl = '';
                $ol = '';

            }
        }
        $columns[] = array(
            'data' => 'num'
        );
        $columns[] = array(
            'data' => 'name'
        );

        $getleavecol = $this->db->select("sysid , names")->from("prime_types_parameter")
            ->where(array("codes" => 'LEAVECREDITS' , "status" => 1))
            ->where_in("sysid" , $leavereparr)
            ->get();
        if($getleavecol->num_rows() > 0){
            foreach ($getleavecol->result() as $row){
                $columns[] = array(
                    'data' => $row->names
                );
            }
        }
        $columns[] = array(
            'data' => 'control'
        );

        if($print > 0){
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
            $data['html']= $html;
        }

        $data['print'] = $print;
        $data['datacolumns'] = $columns;
        $data['query'] = $query;

        echo json_encode($data);
    }


    function fetchdraftrequested(){
        $data =array();
        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $year = $this->input->post('year');

        $sql = $this->db->select("")
            ->from("trn_employee_leave_draft_request")
            ->where(array("status" => 1 , "empid" => $empid , "year" => $year))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                if($row->type == 1){
                    $typesname = 'RR';
                }else if($row->type == 2){
                    $typesname = 'LL';
                }else{
                    $typesname = 'NA';
                }
                $data['draftrequesteddata'][] = array(
                    "num" => $num++,
                    "leavetype" => get_types_label_format($row->leavetype),
                    "fromdate"=> $row->fromdate,
                    "todate"=> $row->todate,
                    "fromtime"=> date("h:i A", strtotime($row->fromtime)),
                    "totime"=> date("h:i A", strtotime($row->totime)),
                    "type"=> $typesname,
                    "datecreated"=> $row->datecreated,
                    "control"=> '<button id="deleteleavedraft" type="button" data-id="'.$row->sysid.'" class="btn btn-xs btn-danger inline"><i class="fa fa-trash"></i></button>',
                );
            }
        }

        echo json_encode($data);
    }

    function submitleaveform(){
        $data = array();
        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $remarks = $this->input->post('remarks');

        if ($empid == false){
            $emp_user_array = get_user_employee_info();
            if ($emp_user_array){
                $empid = $emp_user_array->sysid;
            }
        }
        $insarr = array(
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 301,
            'reason' => $remarks
        );
        $this->db->insert("trn_employee_leave_requests_approval" , $insarr);
        $lastid = $this->db->insert_id();

        $this->db->trans_begin();

        $getdraftreq = $this->db->select("")->from("trn_employee_leave_draft_request")
            ->where(array("empid" => $empid , "year" => $year , "status" => 1))
            ->get();

        if($getdraftreq->num_rows() > 0){
            foreach ($getdraftreq->result() as $row){
                $leavereqsavearr = array(
                    'groupid' => $lastid,
                    'empid' => $row->empid,
                    'from' => $row->fromdate,
                    'to' => $row->todate,
                    'fromtime' => $row->fromtime,
                    'totime' => $row->totime,
                    'totalinminutes' => $row->totalinminutes,
                    'year' => $row->year,
                    'leavetype' => $row->leavetype,
                    'leavedate' => $row->leavedate,
                    'type' => $row->type,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 301,
                );
                $this->db->insert("trn_employee_leave_requests" , $leavereqsavearr);
            }
        }


        $updatearr = array(
            'status' => 301,
            'groupid' => $lastid
        );
        $this->db->where(array("empid" => $empid , "year" => $year , "status" => 1));
        $this->db->update("trn_employee_leave_draft_request" , $updatearr);

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Leave form has been submitted!';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save leave form.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['empid'] = $empid;



        echo json_encode($data);
    }

    function submitleaveformpersonal(){

        $empid = false;
        $data = array();
        $year = $this->input->post('year');
        $remarks = $this->input->post('remarks');

        $create_flow = false;
        $create_flow_err = '';

        $test = false;
        $test_string = ($test) ? '[TEST] ' : '';

        $empname = '';
        $emp_user_array = get_user_employee_info();
        if ($emp_user_array){
            $empid = $emp_user_array->sysid;
            $empname = $emp_user_array->lastname . ', ' . $emp_user_array->firstname;
        }

        if ($empid) {

            $insarr = array(
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'status' => 300,
                'types' => 2,
                'reason' => $remarks
            );
            $this->db->insert("trn_employee_leave_requests_approval", $insarr);
            $lastid = $this->db->insert_id();

            if ($lastid) {
                $this->db->where(array("empid" => $empid, "year" => $year, "status" => 300));
                $this->db->update('trn_employee_leave_requests',array('groupid' => $lastid));
            }

            $this->db->trans_begin();

            $getdraftreq = $this->db->select("")->from("trn_employee_leave_requests")
                ->where(array("empid" => $empid, "year" => $year, "status" => 300))
                ->get();


            $qry_approvals = $this->db->query("
                    SELECT
                    cc.empid AS empid,
                    cc.ccid,
                    ch.empid AS headid,
                    cg.codes,
                    cgh.empid AS execid
                    FROM
                    prime_employee_costcenter AS cc
                    INNER JOIN prime_costcenter_head AS ch ON cc.ccid = ch.ccid
                    INNER JOIN prime_costcenter_group_matrix AS cgm ON cc.ccid = cgm.ccid
                    INNER JOIN prime_costcenter_group AS cg ON cgm.groupid = cg.sysid
                    INNER JOIN prime_costcenter_group_head AS cgh ON cgh.groupid = cg.sysid
                    WHERE
                    cc.empid = $empid AND
                    cg.`level` = 2 AND
                    cc.`status` = 1
                    ")->row();

            if ($qry_approvals) {
                if($empid == $qry_approvals->headid) { // HEAD LEAVE

                    if ($qry_approvals->execid > 0) {
                        $ins_exec_arr = array(
                            'groupid' => $lastid,
                            'approvalid' => $qry_approvals->execid,
                            'types' => 2,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );

                        $this->db->insert('trn_employee_leave_approval', $ins_exec_arr);

                        $get_head_info = get_employee_info($qry_approvals->execid);
                        $get_head_email = ($get_head_info) ? $get_head_info->emailcomp : false;

                        if ($get_head_email) {
                            $content = '';
                            $content .= 'Hello!, <br><br>';
                            $content .= 'A leave request has been submitted, and requires your attention<br>';
                            $content .= 'Please visit Employee Leave Approval, or <a href="http://erp.panayelectric.com/erp/request/leaveapprovalonline/' . $qry_approvals->headid . '/' . $lastid . '/' . $empid . '" target="_blank">click here</a>';
                            $content .= '<br><br>Thank you,';
                            $content .= '<br><br><span style="color: red">This is system generated email, please do not reply!</span>';
                            mailer($get_head_email, $content, 'Leave Request - ' . $empname, false);
                        }
                    }

                }else {
                    if ($qry_approvals->headid > 0) {
                        $ins_head_arr = array(
                            'groupid' => $lastid,
                            'approvalid' => $qry_approvals->headid,
                            'types' => 1,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );

                        $get_head_info = get_employee_info($qry_approvals->headid);
                        $get_head_email = ($get_head_info) ? $get_head_info->emailcomp : false;

                        if ($get_head_email) {
                            $content = '';
                            $content .= 'Hello!, <br><br>';
                            $content .= 'A leave request has been submitted, and requires your attention<br>';
                            $content .= 'Please visit Employee Leave Approval, or <a href="http://erp.panayelectric.com/erp/request/leaveapprovalonline/' . $qry_approvals->headid . '/' . $lastid . '/' . $empid . '" target="_blank">click here</a>';
                            $content .= '<br><br>Thank you,';
                            $content .= '<br><br><span style="color: red">This is system generated email, please do not reply!</span>';
                            mailer($get_head_email, $content, $test_string.'Leave Request - ' . $empname, false);
                        }

                        $this->db->insert('trn_employee_leave_approval', $ins_head_arr);
                    }

                    if ($qry_approvals->execid > 0) {
                        $ins_exec_arr = array(
                            'groupid' => $lastid,
                            'approvalid' => $qry_approvals->execid,
                            'types' => 2,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );

                        $this->db->insert('trn_employee_leave_approval', $ins_exec_arr);
                    }
                }
            }


            $emp = get_employee_info($empid);
            $create_flow = create_transaction_trails('EMPLEAVE', 'EMPLEAVE - '.$emp->lastname.', '.$emp->firstname, 92, $lastid);
            $data['flwoerr'] = $create_flow;

            /*if ($getdraftreq->num_rows() > 0) {
                foreach ($getdraftreq->result() as $row) {
                    $leavereqsavearr = array(
                        'groupid' => $lastid,
                        'empid' => $row->empid,
                        'from' => $row->fromdate,
                        'to' => $row->todate,
                        'fromtime' => $row->fromtime,
                        'totime' => $row->totime,
                        'totalinminutes' => $row->totalinminutes,
                        'year' => $row->year,
                        'leavetype' => $row->leavetype,
                        'leavedate' => $row->leavedate,
                        'type' => $row->type,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                        'status' => 301,
                    );
                    $this->db->insert("trn_employee_leave_requests", $leavereqsavearr);
                }
            }*/


            $updatearr = array(
                'status' => 300,
                'groupid' => $lastid
            );
            $this->db->where(array("empid" => $empid, "year" => $year, "status" => 1));
            $this->db->update("trn_employee_leave_draft_request", $updatearr);
            if($create_flow) {
                $data = db_trans($this->db);
            }else{
                $data['qry'] = false;
                $data['msg'] = 'Transaction flow error!';
                $data['func'] = 'warning';
            }
        }

        echo json_encode($data);
    }
    function printleaveform(){
        $data = array();
        $emp = $this->input->post('employeeprint');
        $yearprint = $this->input->post('yearprint');
        $trntype = $this->input->post('trntype');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $tempsupp = $this->input->post('tempsupp');

        if($trntype == 1){
            if(!empty($fromdate) && !empty($todate)){
                $this->db->where('DATE(telra.datecreated) >=', $fromdate);
                $this->db->where('DATE(telra.datecreated) <=', $todate);
            }
            $sql = $this->db->select("telra.sysid ,telr.empid, telra.createdby , telra.updatedby ,telra.datecreated , telra.dateupdated , telra.status ")
                ->from("trn_employee_leave_requests as telr")
                ->join("trn_employee_leave_requests_approval as telra" , "telra.sysid = telr.groupid" , "left")
                ->where(array("telr.empid" => $emp , "telr.year" => $yearprint ))
                ->group_by("telra.sysid ,telr.empid ,telra.createdby , telra.updatedby ,telra.datecreated , telra.dateupdated , telra.status")
                // ->order_by("telr.groupid", "desc")
                ->get();
            $data['errorqry1'] = $this->db->_error_message();
        }else if($trntype == 2){
            $sql = $this->db->select("flm.sysid ,flt.empid, flm.createdby , flm.updatedby ,flm.datecreated , flm.dateupdated , flm.status  ")
                ->from("flexi_leave_transaction as flt")
                ->join("flexi_leave_main as flm" , "flm.sysid = flt.groupid" , "left")
                ->where(array("flt.empid" => $emp , "flt.year" => $yearprint ))
                ->group_by("flm.sysid ,flt.empid ,flm.createdby , flm.updatedby ,flm.datecreated , flm.dateupdated , flm.status")
                // ->order_by("flt.groupid", "desc")
                ->get();
            $data['errorqry2'] = $this->db->_error_message();
        }else if($trntype == 3){
            $sql = $this->db->select("ulm.sysid ,ult.empid, ulm.createdby , ulm.updatedby ,ulm.datecreated , ulm.dateupdated , ulm.status")->from("union_leave_transaction as ult")
                ->join("union_leave_main as ulm" , "ulm.sysid = ult.groupid" , "left")
                ->where(array("ult.empid" => $emp , "ult.year" => $yearprint ))
                ->group_by("ulm.sysid ,ult.empid ,ulm.createdby , ulm.updatedby ,ulm.datecreated , ulm.dateupdated , ulm.status")
                // ->order_by("ult.groupid", "desc")
                ->get();
            $data['errorqry2'] = $this->db->_error_message();
        }


        if($sql->num_rows() > 0){

            $num = 1;
            foreach ($sql->result() as $row){
                $print_btn_html  = '';
                $print_btn_html  .= '<button  id="printleaveformbtn" data-printype="1"  data-trntype="'.$trntype.'"  data-emp="'.$row->empid.'" data-id="'.$row->sysid.'" class="btn btn-xs btn-primary inline tooltips" title="Laser Printer" data-placement="left"><i class="fa fa-print"></i></button>';
                $print_btn_html  .= '<button id="printleaveformbtn" data-printype="2" data-trntype="'.$trntype.'"  data-emp="'.$row->empid.'" data-id="'.$row->sysid.'" class="btn btn-xs btn-danger inline tooltips" title="Dot Matrix Printer" data-placement="left"><i class="fa fa-print"></i></button>';
                if($row->status == 301){
                    $data['leaveempdata'][] = array(
                        "expand" => btn_expand($row->sysid)."<input type='hidden' id='hiddenval' value='".$trntype."' />",
                        "createdby" => $this->getuserinfo($row->createdby),
                        "updatedby" => $this->getuserinfo($row->updatedby),
                        "datecreated" => $row->datecreated,
                        "dateupdated" => $row->dateupdated,
                        "print" => $print_btn_html,
                        "cancel" => '<button id="cancelleaveformbtn" data-trntype="'.$trntype.'"  data-emp="'.$row->empid.'" data-id="'.$row->sysid.'" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></button>'
                    );
                }else if($row->status == 0){
                    $data['cancelledleave'][] = array(
                        "expand" => btn_expand($row->sysid)."<input type='hidden' id='hiddenval' value='".$trntype."' />",
                        "createdby" => $this->getuserinfo($row->createdby),
                        "updatedby" => $this->getuserinfo($row->updatedby),
                        "datecreated" => $row->datecreated,
                        "dateupdated" => $row->dateupdated,
                        //  "print" => '<button id="printleaveformbtn"  data-trntype="'.$trntype.'"  data-emp="'.$row->empid.'" data-id="'.$row->sysid.'" class="btn btn-xs btn-primary inline"><i class="fa fa-print"></i></button>',
                        "print" => '',
                        //   "cancel" => '<button id="cancelleaveformbtn" data-trntype="'.$trntype.'"  data-emp="'.$row->empid.'" data-id="'.$row->sysid.'" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></button>'
                        "cancel" => ''
                    );
                }

            }
        }
        $data['empid'] = $emp;
        $data['year'] = $yearprint;
        $data['trntype'] = $trntype;
        echo json_encode($data);
    }
    function getuserinfo($id){
        $sql=  $this->db->select("lastname")->from("prime_system_users")
            ->where(array("sysid" => $id,  "status" => 1))->get()->row();
        return ($sql) ? $sql->lastname : '';
    }
    function getleavesub(){
        $data = array();
        $id = $this->input->post('id');
        $inputs = $this->input->post('inputs');
        $stat = $this->input->post('stat');
        $html  = '';
        $data['inputs'] =$inputs;
        $totalinminutes = 0;
        if($inputs == 1){
            $sql = $this->db->select("telr.from , telr.to , telr.fromtime , telr.totime , telr.leavetype , telr.year , telr.leavedate, telr.type, telr.totalinminutes")
                ->from("trn_employee_leave_requests as telr")
                ->where(array("telr.groupid" => $id ,"telr.status" => $stat))
                ->order_by("telr.datecreated")
                ->get();
        }else if($inputs == 2){
            $sql = $this->db->select("")->from("flexi_leave_transaction")
                ->where(array("groupid" => $id))->get();
        }else if($inputs == 3){
            $sql = $this->db->select("")->from("union_leave_transaction")
                ->where(array("groupid" => $id))->get();
        }


        if($sql->num_rows() > 0){

            foreach ($sql->result() as $row){


                $totalinminutes += $row->totalinminutes;

                $totalbalancehours = $row->totalinminutes / 60;
                $subdaybalance = floor($totalbalancehours / 8);
                $subhourbalance =  ($totalbalancehours % 8);
                $n = $totalbalancehours;
                $whole = floor($n);      // 1
                $subminutebalance = ($n - $whole) * 60;
                $subtotal = $subdaybalance.' - '.$subhourbalance.' - '.round($subminutebalance);
                if($row->type == 1){
                    $leavetype = 'Regular Leave';
                }else if($row->type == 2){
                    $leavetype = 'Locator Leave';
                }else if($row->type == 3){
                    $leavetype = 'Flexi Leave';
                }else if($row->type == 4){
                    $leavetype = 'Union Leave';
                }

                $data['leavetype'] = $row->leavetype;
                if($inputs == 1){
                    $fromval = $row->from;
                    $toval = $row->to;
                }else if($inputs == 2){
                    $fromval = $row->fromdate;
                    $toval = $row->todate;
                }else if($inputs == 3){
                    $fromval = $row->fromdate;
                    $toval = $row->todate;
                }

                if($row->fromtime == '00:00:00'){
                    $fromtime = '';
                }else{
                    $fromtime = date('h:i:s A', strtotime($row->fromtime));
                }

                if($row->totime == '00:00:00'){
                    $totime = '';
                }else{
                    $totime = date('h:i:s A', strtotime($row->totime));
                }


                $html .= '<div class="row">';

                $html .= '<div class="col-md-2">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Leave Type</span>';
                $html .= '<span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default" id="totalloanssum">'.get_types_name($row->leavetype)->names.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Year</span>';
                $html .= '<span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default" id="totalpremiumssum">'.$row->year.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">From</span>';
                $html .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default " id="totalloanssum">'.$fromval.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">To</span>';
                $html .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default" id="totalpremiumssum">'.$toval.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';
                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">From Time</span>';
                $html .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default" id="totalloanssum">'.$fromtime.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">To Time</span>';
                $html .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default" id="totalpremiumssum">'.$totime.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-2">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Date</span>';
                $html .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default " >'.$row->leavedate.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Type</span>';
                $html .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default " >'.$leavetype.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-2">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="label label-name col-md-5 col-xs-5 col-sm-5 col-lg-5">Sub Total</span>';
                $html .= '<span class="col-md-7 col-xs-7 col-sm-7 col-lg-7 label-default " >'.$subtotal.'</span>';
                $html .= '</li>';

                $html .= '</ul>';
                $html .= '</div>';




                $html .= '</div>';
                $html .= '<hr>';

            }
            $totalbalancehours = $totalinminutes / 60;
            $totaldaybalance = floor($totalbalancehours / 8);
            $totalhourbalance =  ($totalbalancehours % 8);
            $n = $totalbalancehours;
            $whole = floor($n);      // 1
            $totalminutebalance = ($n - $whole) * 60;

            $totalspent = $totaldaybalance.' - '.$totalhourbalance.' - '.$totalminutebalance;

            $html .= '<h3 style="margin-right: 10px;" class="pull-right">Total : '.$totalspent.'</h3>';
        }
        $data['html'] = $html;
        echo json_encode($data);
    }
    function printleave(){
        $data = array();
        $id = $this->input->post('id');
        $sup = $this->input->post('sup');
        $exec = $this->input->post('exec');
        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $trntype = $this->input->post('trntype');
        $printtype = $this->input->post('printtype');
        $check = $this->input->post('check');
        $consultant = $this->input->post('consultant');
        $remarksval = '';
        $totalspenthours1 = '';

        if($trntype == 1){
            $getremarks = $this->db->select("reason")
                ->from("trn_employee_leave_requests_approval")
                ->where(array("sysid" => $id , "status" => 301))->get()->row();

            /*  $sql = $this->db->select("telr.groupid ,telr.empid , telr.from , telr.to , telr.fromtime , telr.totime , telr.year  , telr.leavetype , telr.leavedate ")->from("trn_employee_leave_requests as telr")
                  ->join("trn_employee_leave_draft_request as teldr" , "teldr.groupid = telr.groupid" , 'left')
                  ->where(array("telr.groupid" => $id , "teldr.status" => 301 , "telr.status" => 301))
                //  ->order_by("teldr.leavedate")
                  ->group_by("telr.groupid ,telr.empid , telr.from , telr.to , telr.fromtime , telr.totime , telr.year , telr.leavetype , telr.leavedate")
                  ->get(); */


            $sql = $this->db->select("telr.groupid ,telr.empid , telr.from , telr.to , telr.fromtime , telr.totime , telr.year  , telr.leavetype , telr.leavedate ")
                ->from("trn_employee_leave_requests as telr")
                ->where(array("groupid" => $id , "telr.status" => 301))
                ->group_by("telr.groupid ,telr.empid , telr.from , telr.to , telr.fromtime , telr.totime , telr.year , telr.leavetype , telr.leavedate")
                ->get();


            $qry = $this->db->query("SELECT DISTINCT tp.sysid, tp.names, tp.desc,lc.types, SUM(lc.credit) AS totalcredit
                                FROM prime_employee_main_leave_credits AS lc
                                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = lc.types
                                WHERE lc.empid = $empid  AND lc.year = $year AND lc.status = 1
                                GROUP BY tp.sysid, tp.names, tp.desc,lc.types");


            $gettotalavailed = $this->db->select("SUM(totalinminutes) AS totalavailed")
                ->from("trn_employee_leave_requests")
                ->where(array("groupid" => $id , "status" => 301))
                ->get()->row();
        }else if($trntype == 2){
            $getremarks = $this->db->select("remarks")->from("flexi_leave_transaction")
                ->where(array("sysid" => $id , "status" => 301))->get()->row();

            $sql = $this->db->select("flt.groupid , flt.empid , flt.fromdate , flt.todate , flt.fromtime , flt.totime ,flt.year  , flt.leavetype , flt.leavedate ")
                ->from("flexi_leave_transaction as flt")
                ->where(array("flt.groupid" => $id , "flt.status" => 301))
                ->group_by("flt.groupid , flt.empid , flt.fromdate , flt.todate , flt.fromtime , flt.totime ,flt.year  , flt.leavetype , flt.leavedate")
                ->get();

            $qry = $this->db->query("SELECT DISTINCT tp.sysid, tp.names, tp.desc,fc.types, SUM(fc.totalinminutes) AS totalcredit
                                FROM flexi_credits AS fc
                                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = fc.types
                                WHERE fc.empid = $empid AND fc.status = 1
                                GROUP BY tp.sysid, tp.names, tp.desc,fc.types");

            $gettotalavailed = $this->db->select("SUM(totalinminutes) AS totalavailed")
                ->from("flexi_leave_transaction")
                ->where(array("groupid" => $id , "status" => 301))
                ->get()->row();
        }else if($trntype == 3){
            $getremarks = $this->db->select("remarks")->from("union_leave_transaction")
                ->where(array("sysid" => $id , "status" => 301))->get()->row();

            $sql = $this->db->select("ult.groupid , ult.empid , ult.fromdate , ult.todate , ult.fromtime , ult.totime ,ult.year  , ult.leavetype , ult.leavedate ")
                ->from("union_leave_transaction as ult")
                ->where(array("ult.groupid" => $id , "ult.status" => 301))
                ->group_by("ult.groupid , ult.empid , ult.fromdate , ult.todate , ult.fromtime , ult.totime ,ult.year  , ult.leavetype , ult.leavedate")
                ->get();

            $qry = $this->db->query("SELECT DISTINCT tp.sysid, tp.names, tp.desc,uc.types, SUM(uc.credit) AS totalcredit
                                FROM union_credits AS uc
                                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = uc.types
                                WHERE  uc.status = 1
                                GROUP BY tp.sysid, tp.names, tp.desc,uc.types");


            $gettotalavailed = $this->db->select("SUM(totalinminutes) AS totalavailed")
                ->from("union_leave_transaction")
                ->where(array("groupid" => $id , "status" => 301))
                ->get()->row();
        }


        if($printtype == 2){
            $div_width_left  = "60%";
            $div_width_right  = "40%";
            $div_main_width = '100%';
            $font_size = '17px';
            $leave_prop_arr = array(
                'width' => array(10, 120, 120, 100, 100 , 100),
                'left' => array(5, 80, 200, 320, 480 , 610),
            );

            $leave_balance_left = '320px';
            $main_font_spacing = '3px';
        }else{
            $div_width_left  = "55%";
            $div_width_right  = "45%";
            $div_main_width = '730px';
            $font_size = '10px';
            $leave_prop_arr = array(
                'width' => array(10, 100, 100, 70, 70 , 100),
                'left' => array(10, 40, 100, 180, 240 , 330),
            );
            $leave_balance_left = '210px';
            $main_font_spacing = '0px';
        }


        $html = '';
        $border_bottom = '';

        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important;letter-spacing: '.$main_font_spacing.' !important; }</style>';
        $html .= '</head>';
        $html .= '<body>';

        $html .= '<div style="position: relative; height: 350px; white-space: nowrap; width: '.$div_main_width.'; margin-bottom: 10px; ' . $border_bottom . ' padding-bottom: 2px;">';
        // MAIN HEADER
        $html .= employee_print_header($empid, 'Leave Request');
        // ==========================================


        // LEFT COLUMN
        $html .= '<div style="position: absolute; top: 35px; padding-top: 5px; left: 0px; width: '.$div_width_left.'; height: 30px;">';
        $html .= '<p style="font-weight: normal; font-size: '.$font_size.'; line-height: 14px; height: 14px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 0px; font-weight: normal;">REQUESTING LEAVE DETAILS</span>';
        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
        $html .= '</p>';
        $html .= '</div>';


        $html .= '<div style="position: absolute; top: 60px; padding-top: 5px; left: 0px; width: '.$div_width_left.'; height: 160px; border-right: 1px dashed #ccc;">';

        // HEADER OF LOOP
        $html .= '<p style="position: relative; font-family: courier, monospace; font-weight: bold; font-size: '.$font_size.'; line-height: 16px; height: 16px;  margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][0].'px; width: '.$leave_prop_arr['width'][0].'px;font-weight: normal !important;">Type</span>';
        $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][1].'px; width: '.$leave_prop_arr['width'][1].'px;font-weight: normal !important;">From</span>';
        $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][2].'px; width: '.$leave_prop_arr['width'][2].'px;font-weight: normal !important;">To</span>';
        $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][3].'px; width: '.$leave_prop_arr['width'][3].'px;font-weight: normal !important;">Time From</span>';
        $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][4].'px; width: '.$leave_prop_arr['width'][4].'px;font-weight: normal !important;">Time To</span>';
        $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][5].'px; width: '.$leave_prop_arr['width'][5].'px;font-weight: normal !important;">Date</span>';
        $html .= '</p>';

        if($sql->num_rows() > 0){
            foreach ($sql->result() as $leavedates){

                if($trntype == 1){
                    $fromdateleave = $leavedates->from;
                    $todateleave = $leavedates->to;
                    $getdate = $this->db->select("leavedate")->from("trn_employee_leave_draft_request")
                        ->where(array("groupid" => $leavedates->groupid , "fromdate" => $fromdateleave,
                            "todate" => $todateleave , "fromtime" => $leavedates->fromtime ,
                            "totime" => $leavedates->totime))
                        // ->order_by("leavedate")
                        ->get()->row();
                }else if($trntype == 2){
                    $fromdateleave = $leavedates->fromdate;
                    $todateleave = $leavedates->todate;
                    $getdate = $this->db->select("leavedate")->from("flexi_leave_transaction")
                        ->where(array("groupid" => $leavedates->groupid , "fromdate" => $fromdateleave,
                            "todate" => $todateleave , "fromtime" => $leavedates->fromtime ,
                            "totime" => $leavedates->totime))
                        // ->order_by("leavedate")
                        ->get()->row();
                }else if($trntype == 3){
                    $fromdateleave = $leavedates->fromdate;
                    $todateleave = $leavedates->todate;
                    $getdate = $this->db->select("leavedate")->from("union_leave_transaction")
                        ->where(array("groupid" => $leavedates->groupid , "fromdate" => $fromdateleave,
                            "todate" => $todateleave , "fromtime" => $leavedates->fromtime ,
                            "totime" => $leavedates->totime))
                        // ->order_by("leavedate")
                        ->get()->row();
                }



                $leavedate = $leavedates->leavedate;

                $fromdateval  = ($fromdateleave != '') ? date('M d', strtotime($fromdateleave)) : '';
                $todateval = ($todateleave != '') ? date('M d', strtotime($todateleave)) : '';

                $fromtime  = date("h:i a", strtotime($leavedates->fromtime));
                $totime = date("h:i A", strtotime($leavedates->totime));
                if($leavedates->fromtime == '00:00:00'){
                    $fromtime = '';
                }
                if($leavedates->totime == '00:00:00'){
                    $totime = '';
                }
                $html .= '<p style="position: relative; font-family: courier, monospace; font-weight: normal; font-size: '.$font_size.'; line-height: 16px; height: 16px;  margin:5px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 3px; margin-left: 0px;" class="charges-list-item">';
                $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][0].'px; width: '.$leave_prop_arr['width'][0].'px;">'.get_types_name($leavedates->leavetype)->names.'</span>';
                $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][1].'px; width: '.$leave_prop_arr['width'][1].'px;">'.$fromdateval.'</span>';
                $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][2].'px; width: '.$leave_prop_arr['width'][2].'px;">'.$todateval.'</span>';
                $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][3].'px; width: '.$leave_prop_arr['width'][3].'px;">'.$fromtime.'</span>';
                $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][4].'px; width: '.$leave_prop_arr['width'][4].'px;">'.$totime.'</span>';
                $html .= '<span style="position: absolute; left: '.$leave_prop_arr['left'][5].'px; width: '.$leave_prop_arr['width'][5].'px;">'.$leavedate.'</span>';
                $html .= '</p>';

            }
        }



        $html .= '</div>';
        $html .= '<div style="position: absolute; top: 180px; padding-top: 5px; left: 0px; width: '.$div_width_left.'; height: 30px;">';
        $html .= '<p style="position: absolute; bottom: 10px; font-weight: normal;line-height: 14px; height: 14px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
        $html .= '<span style="font-size: '.$font_size.'; position: absolute; left: 10px; width: 200px; font-weight: lighter;"><span style=" vertical-align: middle;width: 15px; height: 15px; display: inline-block; border: 1px solid #ccc;"></span> Approved</span>';
        $html .= '<span style="font-size: '.$font_size.'; position: absolute; left: 210px; width: 250px; font-weight: lighter;"><span style=" vertical-align: middle;width: 15px; height: 15px; display: inline-block; border: 1px solid #ccc;"></span> Disapproved</span>';
        $html .= '</p>';
        $html .= '</div>';
        // END OF LEFT COLUMN


        // RIGHT COLUMN
        $html .= '<div style="position: absolute; top: 35px; padding-top: 5px; right: 0px; width: '.$div_width_right.'; height: 200px;">';
        $html .= '<p style="font-weight: normal; font-size: '.$font_size.'; line-height: 14px; height: 14px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-weight: normal;">'.date("Y-m-d H:i:s").'</span>';
        $html .= '</p>';
        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';

        $html .= '<p style="position: relative; font-family: courier, monospace; font-weight: normal; font-size: '.$font_size.'; margin: 0px 0px; line-height: 14px; height: 14px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 3px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px; width: 200px; font-weight: normal;">Balance after Availment</span>';
        $html .= '<span style="position: absolute; left: '.$leave_balance_left.'; width: 250px; font-weight: lighter;">DD - HH - MM</span>';
        $html .= '</p>';

        if($qry->num_rows() > 0) {
            foreach ($qry->result() as $row){



                $totalbalance =$row->totalcredit;




                $daybalance = 0;
                $hourbalance = 0;
                $minutebalance = 0;
                $balanceminutes = 0;

                //BALANCE
                if($trntype == 1){
                    $balanceminutes = $totalbalance * 8 * 60;

                    $getleavedetails = $this->db->select("SUM(totalinminutes) AS totalminutes")->from("trn_employee_leave_requests")
                        ->where(array("empid" => $empid , "leavetype" => $row->types , "year" => $year , "status" => 301))
                        ->get()->row();
                    $totalspentminutes = ($getleavedetails) ? $getleavedetails->totalminutes : 0;
                }else if($trntype == 2){
                    $balanceminutes = $totalbalance;

                    $getleavedetails = $this->db->select("SUM(totalinminutes) AS totalminutes")->from("flexi_leave_transaction")
                        ->where(array("empid" => $empid , "leavetype" => $row->types , "year" => $year , "status" => 301))
                        ->get()->row();
                    $totalspentminutes = ($getleavedetails) ? $getleavedetails->totalminutes : 0;
                }else if($trntype == 3){
                    $balanceminutes = $totalbalance * 8 * 60;

                    $getunionspent = $this->db->select("SUM(totalinminutes) AS totalunionspent")->from("union_leave_transaction")
                        ->where(array("year" => $year))->get()->row();
                    $totalspentminutes = ($getunionspent) ? $getunionspent->totalunionspent : 0;

                }

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

                $ramainingminutes = $balanceminutes - $totalspentminutes;

                $totalbalancehours = $ramainingminutes / 60;
                $daybalance = floor($totalbalancehours / 8);
                $hourbalance =  ($totalbalancehours % 8);
                $n = $totalbalancehours;
                $whole = floor($n);      // 1
                $minutebalance = ($n - $whole) * 60;

                $html .= '<p style="position: relative; font-family: courier, monospace; font-weight: normal;  line-height: 14px; height: 14px; font-size: '.$font_size.'; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 3px; margin-left: 0px;" class="charges-list-item">';
                $html .= '<span style="position: absolute; left: 10px; width: 200px; font-weight: lighter;">'.$row->names.'</span>';
                $html .= '<span style="position: absolute; left: '.$leave_balance_left.'; width: 250px; font-weight: lighter;">'.str_pad($daybalance , 2 , "0" , STR_PAD_LEFT).' - '.str_pad($hourbalance , 2 , "0" , STR_PAD_LEFT).' - '.str_pad(round($minutebalance) , 2 , "0" , STR_PAD_LEFT).'</span>';
                $html .= '</p>';

            }
        }
        // END OF RIGHT COLUMN

        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';

        $totalavailed = ($gettotalavailed) ? $gettotalavailed->totalavailed : 0;

        //SPENT
        $totalspenthours1 = $totalavailed / 60;
        $dayavailedspent = floor($totalspenthours1 / 8);
        $houravailedspent = ($totalspenthours1 % 8);
        $n1 = $totalspenthours1;
        $whole1 = floor($n1);      // 1
        $minuteavailedspent = ($n1 - $whole1) * 60;

        $html .= '<p style="position: relative; font-family: courier, monospace; font-weight: normal; font-size: '.$font_size.'; line-height: 14px; height: 14px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px; width: 200px; font-weight: lighter;">Availment</span>';
        $html .= '<span style="position: absolute; left: '.$leave_balance_left.'; width: 250px; font-weight: lighter;">'.str_pad($dayavailedspent , 2 , "0" , STR_PAD_LEFT).' - '.str_pad($houravailedspent , 2 , "0" , STR_PAD_LEFT).' - '.str_pad(round($minuteavailedspent) , 2 , "0" , STR_PAD_LEFT).'</span>';
        $html .= '</p>';

        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';

        $html .= '<p style="position: relative; font-family: courier, monospace; font-weight: normal; font-size: '.$font_size.'; line-height: 14px; height: 14px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px; width: 80px; font-weight: lighter;">Remarks: </span>';
        if($trntype == 1){
            $remarksval  = ($getremarks) ? $getremarks->reason : '';
        }else if($trntype == 2){
            $remarksval  = ($getremarks) ? $getremarks->remarks : '';
        }
        if($printtype == 1){
            $html .= '<span style="position: absolute; left: 70px; width: 230px; display:inline-block; overflow: hidden; white-space: initial; word-wrap: break-word; font-weight: lighter; line-height: 12px;">'.$remarksval.'</span>';
        }else if($printtype == 2){
            $html .= '<span style="position: absolute; left: 130px; width: 420px; display:inline-block; overflow: hidden; white-space: initial; word-wrap: break-word; font-weight: lighter; line-height: 16px;">'.$remarksval.'</span>';
        }
        $html .= '</p>';

        $html .= '</div>';

        $html .= '<div style="position: absolute; top: 210px; padding-top: 5px; left: 0px; width: 100%; height: 10px;">';
        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
        $html .= '</div>';

        // BOTTOM DETAILS
        // SIGNATURES / NOTES
        $html .= '<div style="position: absolute; top: 300px; padding-top: 5px; left: 0px; width: 100%; height: 30px;">';
        if($check == 1){
            $suppval = 'SANDRA UGARTE CACHO';
        }else{
            $suppval = ($sup) ? get_employee_name($sup) : '';
        }
        if($consultant > 0){
            $widthsize = '33%';
        }else{
            $widthsize = '33%';
        }
        $html .= '<p style="position: relative; font-family: Arial, Verdana, sans-serif !important; font-weight: normal; font-size: '.$font_size.'; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 0%; width: '.$widthsize.'; font-weight: normal; text-align: center;"><span style="border-bottom: 1px solid black !important;">'.get_employee_name($empid).'</span><br>EMPLOYEE NAME</span>';

        if($trntype == 3){
            $html .= '<span style="position: absolute; left: 33% ; width: '.$widthsize.'; font-weight: normal; text-align: center; "><span style="border-bottom: 1px solid black !important;">'.$suppval.'</span><br>UNION PRESIDENT</span>';
            $html .= '<span style="position: absolute; left: 66% ; width: '.$widthsize.'; font-weight: normal; text-align: center; "><span style="border-bottom: 1px solid black !important;">'.get_employee_name($exec).'</span><br>PRESIDENT & CEO</span>';
        }else{
            $html .= '<span style="position: absolute; left: 33%; width: '.$widthsize.'; font-weight: normal; text-align: center; "><span style="border-bottom: 1px solid black !important;">'.$suppval.'</span><br>SUPERVISOR</span>';
            if($consultant > 0){
                $data['test 1']  = $consultant;
                $html .= '<span style="position: absolute; left: 66%; width: '.$widthsize.'; font-weight: normal; text-align: center; "><span style="border-bottom: 1px solid black !important;">NILO C. MADRIAL</span><br>CONSULTANT</span>';
            }else{
                $data['test 2']  = $consultant;
                $html .= '<span style="position: absolute; left: 66%; width: '.$widthsize.'; font-weight: normal; text-align: center; "><span style="border-bottom: 1px solid black !important;">'.get_employee_name($exec).'</span><br>EXECUTIVE</span>';
            }
            $data['consultantval']  = $consultant;
        }



        $html .= '</p>';
        $html .= '</div>';
        $html .= '<div style="position: absolute; top: 340px; padding-top: 5px; left: 0px; width: 100%; height: 10px; ">';
        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
        $html .= '<p style="position: relative; font-family: courier, monospace; font-weight: normal; font-size: '.$font_size.'; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="text-center">';
        $html .= '<span style="position: absolute; left: 0%; width: 100%; font-weight: lighter; text-align: center;">';
        $html .= 'Please return to HRD within 8 hours from filling';
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';


        $html .= '</body>';
        $html .= '</html>';

        $data['html'] = $html;
        echo json_encode($data);
    }

    function getemployeesdept(){
        $data =array();

        $sql = $this->db->select("pem.sysid as empid, p.lastname , p.firstname , pcm.sysid as ccid , pcm.codes , pcm.desc")
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_costcenter as pec" , "pec.empid = pem.sysid && pec.type = 1"  , "left")
            ->join("prime_costcenter_main as pcm" , "pcm.sysid = pec.ccid" , "left")
            ->where(array("pem.status" => 1 , "pec.status" => 1))
            ->order_by("p.lastname" , "asc")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['empdept'][] = array(
                    "num" => $num++,
                    "name" => $row->lastname.', '.$row->firstname,
                    "dept" => '<input data-empid="'.$row->empid.'" style="width:100%;" type="text" value="'.$row->ccid.'" class="form-control" id="empdeptselect2" />',
                    "ccid" => $row->ccid
                );
            }
        }

        echo json_encode($data);
    }
    function getempdept(){
        $data =array();

        $sql = $this->db->select("sysid, codes , desc")->from("prime_costcenter_main")
            ->where(array("status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc.' - '.$row->codes
                );
            }
        }

        echo json_encode($data);
    }
    function updateempdept(){
        $data = array();
        $id = $this->input->post('id');
        $empid = $this->input->post('empid');

        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("empid" => $empid));
        $this->db->update("prime_employee_costcenter" , $updatearr);
        $this->db->trans_begin();
        $insarr = array(
            'empid' => $empid,
            'ccid' => $id,
            'type' => 1,
            'status' => 1
        );
        $sql = $this->db->insert("prime_employee_costcenter" , $insarr);
        $data['errinsert'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Department has been updated.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Error adding department!';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function getcostmain(){
        $data = array();

        $sql = $this->db->select("pcm.sysid , pcm.codes , pcm.desc as costdesc")->from("prime_costcenter_main as pcm")
            ->where(array("pcm.status" => 1))->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $gethead = $this->db->select("pcch.empid , p.lastname , p.firstname")->from("prime_costcenter_head as pcch")
                    ->join("prime_employee_main as pem" , "pem.sysid = pcch.empid")
                    ->join("person as p" , "p.sysid = pem.personid" , "left")
                    ->where(array("pcch.type" => 1 , "pcch.status" => 1 , "pcch.ccid" => $row->sysid ))
                    ->get()->row();
                $data['headerrquery'] = $this->db->_error_message();

                $getexecutive = $this->db->select("pcgh.empid , p.lastname , p.firstname")
                    ->from("prime_costcenter_group_matrix as pcgm")
                    ->join("prime_costcenter_group_head as pcgh" , "pcgh.groupid = pcgm.groupid")
                    ->join("prime_employee_main as pem" , "pem.sysid = pcgh.empid")
                    ->join("person as p" , "p.sysid = pem.personid")
                    ->where(array("ccid" => $row->sysid))
                    ->get()->row();
                $head = ($gethead) ? $gethead->lastname.', '.$gethead->firstname : '';
                $executive = ($getexecutive) ? $getexecutive->lastname.', '.$getexecutive->firstname : '';
                $headID = ($gethead) ? $gethead->empid: '';
                $execID = ($getexecutive) ? $getexecutive->empid: '';
                $data['costcenterdata'][] = array(
                    'num' => $num++,
                    'code' => $row->codes,
                    'desc' => ucwords(strtolower($row->costdesc)),
                    'head' => $head,
                    'exec' => $executive,
                    'control' => '<a href="#form_edit_cost_main"  data-toggle="ajax-modal" data-view="'.$row->sysid.'" data-arr="'.$headID.'-'.$execID.'-'.$row->costdesc.'" class="btn btn-primary  btn-xs inline"><i class="fa fa-edit"></i>Edit</a>'

                );
            }
        }

        echo json_encode($data);
    }
    function cancelleavetrn(){
        $data = array();
        $empid = $this->input->post('empid');
        $trnid = $this->input->post('trnid');
        $trntype = $this->input->post('trntype');
        if($trntype == 1){
            $this->db->trans_begin();

            $this->db->set('a.status', 0);
            $this->db->set('b.status', 0);
            $this->db->set('c.status', 0);


            $this->db->where('a.sysid = b.groupid');
            $this->db->where('b.empid', $empid);
            $this->db->where('a.sysid' , $trnid);
            $this->db->where('b.groupid = c.groupid');
            $sql = $this->db->update('trn_employee_leave_requests_approval as a, trn_employee_leave_requests as b , trn_employee_leave_draft_request as c');
            if($this->db->trans_status() == true && $sql){
                $this->db->trans_commit();
                $msg = 'Leave has been cancelled';
                $func = 'success';
                $qry = true;
            }else{
                $this->db->trans_rollback();
                $msg = 'Failed to cancel leave!';
                $func = 'error';
                $qry = false;
            }
        }else if($trntype == 2){
            $this->db->trans_begin();

            $this->db->set('a.status', 0);
            $this->db->set('b.status', 0);

            $this->db->where('a.sysid = b.groupid');
            $this->db->where('b.empid', $empid);
            $this->db->where('a.sysid' , $trnid);
            $sql = $this->db->update('flexi_leave_main as a, flexi_leave_transaction as b');
            if($this->db->trans_status() == true && $sql){
                $this->db->trans_commit();
                $msg = 'Leave has been cancelled';
                $func = 'success';
                $qry = true;
            }else{
                $this->db->trans_rollback();
                $msg = 'Failed to cancel leave!';
                $func = 'error';
                $qry = false;
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function updatelastnameformat(){


        $sql = $this->db->select("p.lastname , p.firstname")->from("person as p")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                echo $num++.' ) ';
                echo $row->lastname.', '.$row->firstname.'<br>';
            }
        }
    }
    function fetchempattlogs(){
        $data = array();
        $employeeattlogs = $this->input->post('employeeattlogs');
        $monthattlogs = $this->input->post('monthattlogs');
        $yearattlogs = $this->input->post('yearattlogs');
        $fromday = $this->input->post('fromday');
        $today = $this->input->post('today');
        $fromdate  = $yearattlogs.'-'.$monthattlogs.'-'.$fromday;
        $todate = $yearattlogs.'-'.$monthattlogs.'-'.$today;

        $html = '';
        $html .= ' <div class="list-group">';


        $getbioid = $this->db->select("bioid")->from("prime_employee_bioid")
            ->where(array("empid" => $employeeattlogs , "status" => 1))
            ->get()->row();
        if($getbioid){
            $begin = new DateTime( $fromdate);
            $end   = new DateTime( $todate);

            for($i = $begin; $i <= $end; $i->modify('+1 day')){

                //Get the day that this particular date falls on.
                $day = date("D", strtotime($i->format("Y-m-d")));

                //Check to see if it is equal to Sat or Sun.
                if($day == 'Sat' || $day == 'Sun'){
                    //Set our $weekendDay variable to TRUE.
                    $html .= '<a href="#" style="background-color: #3997ff;border: none;" class="list-group-item active">' .$i->format("Y-m-d").'</a>';
                }else{
                    $html .= '<a href="#" style="background-color: #ff5b12;border: none;" class="list-group-item active">'.$i->format("Y-m-d").'</a>';
                }



                $gettimelogs = $this->db->select("logtime")->from("prime_employee_attendance_timelogs")
                    ->where(array("bioid" => $getbioid->bioid , "logdate" => $i->format("Y-m-d")))
                    ->get();
                if($gettimelogs->num_rows() > 0){
                    foreach ($gettimelogs->result() as $timelogsrow){
                        $data['timelogsdata'][] = array(
                            'date' => $i->format("Y-m-d"),
                            'logtime' => $timelogsrow->logtime
                        );
                        $html .= ' <a href="#" class="list-group-item">'.$timelogsrow->logtime.'</a>';
                    }
                }

            }
        }
        $html .= '</div>';
        $data['html'] = $html;
        echo json_encode($data);
    }
    function getmonthlydtrreport(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $employeeregdtr = $this->input->post('employeeregulardtr');

        $html = '';
        $emparraycount =0;
        foreach ($employeeregdtr as $value) {
            $explodearr = explode(',', $value);
            $data['explodearr'] = $explodearr;
            for ($i = 0; $i < count($explodearr); $i++) {
                $emparraycount++;
            }
            if(empty($explodearr[0])){
                $emparraycount = 0;
            }
        }


        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F'); // March

        $startdate = $year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'-01';
        $enddate = date("Y-m-t", strtotime($startdate));

        $lastday = date("t", strtotime($startdate));

        $begin = new DateTime($startdate);
        $end = new DateTime($enddate);

        $end = $end->modify( '+1 day' );
        $data['arraycount'] = $emparraycount;
        if($emparraycount > 0){
            $data['specific'] = 'specific';
            foreach ($employeeregdtr as $value) {
                $explodearr = explode(',', $value);
                for ($i = 0; $i < count($explodearr); $i++) {
                    $empid = $explodearr[$i];
                    $getcount = $this->db->select("COUNT(peat.sysid) AS totalcnt")
                        ->from("prime_employee_bioid as peb")
                        ->join("prime_employee_attendance_timelogs as peat" , "peat.bioid = peb.bioid")
                        ->where(array("peb.status" => 1 , "peb.empid" => $empid , "YEAR(peat.logdate)" => $year , "MONTH(peat.logdate)" => $month))
                        ->get()->row();
                    if($getcount->totalcnt != 0){
                        $data['count'] = $getcount->totalcnt;
                        $allowed_job_cat = array(157,160);
                        $row = $this->db->select("pem.sysid ,p.lastname , p.firstname , peb.bioid , pemp.position_id")->from("prime_employee_main as pem")
                            ->join("person as p" , "p.sysid = pem.personid" , "left")
                            ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid && peb.status = 1" , "left")
                            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid && pemjc.status = 1" , "left")
                            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'" , "left")
                            ->join("prime_employee_main_positions as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1" , "left")
                            ->where(array("pem.sysid" => $empid ))
                            ->where_in('ptp.sysid', $allowed_job_cat)
                            ->order_by("p.lastname")
                            ->get()->row();
                        if($row){
                            $data['data'][] = array(
                                'id'            => $row->sysid,
                                'lastname'      => $row->lastname,
                                'firstname'     => $row->firstname,
                                'bioid'         => $row->bioid
                            );

                            // $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>';
                            $html .= '<div class="col-xs-6" style="font-size:10px !important;width: 50%; display: inline-block; padding: 5px 15px !important;">';

                            $html .= '<div class="row">';
                            $html .= '<div class="col-md-8">Name: ' . strtoupper($row->lastname).', '.strtoupper($row->firstname).'</div>';
                            $html .= '<div class="col-md-4">ID: ' . strtoupper($row->bioid).'</div>';
                            $html .= '</div>';
                            $html .= '<div class="row">';
                            $html .= '<div class="col-md-8">Position: ' .$this->gettypesparam($row->position_id).'</div>';
                            $html .= '<div class="col-md-4">Date: ' .$monthName.' 1 - '.$lastday.', '.$year.'</div>';
                            $html .= '</div>';
                            if($month == 2){
                                $html .= '<table class="table table-condensed  tbl-xs"  style="margin-bottom: 85px !important;">';
                            }else{
                                if($lastday == 30){
                                    $html .= '<table class="table table-condensed table-bordered tbl-xs" style="margin-bottom: 35px !important;">';
                                }else{
                                    $html .= '<table class="table table-condensed table-bordered tbl-xs" >';
                                }

                            }

                            $html .= '<thead>';
                            $html .= '<th></th>';
                            $html .= '<th>IN</th>';
                            $html .= '<th>OUT</th>';
                            $html .= '<th>IN</th>';
                            $html .= '<th>OUT</th>';
                            $html .= '<th>IN</th>';
                            $html .= '<th>OUT</th>';
                            $html .= '<th>IN</th>';
                            $html .= '<th>OUT</th>';
                            $html .= '<th>AM</th>';
                            $html .= '<th>PM</th>';
                            $html .= '<th>TOTAL</th>';
                            $html .= '</thead>';
                            $html .= '<tbody>';

                            $interval = new DateInterval('P1D');
                            $daterange = new DatePeriod($begin, $interval ,$end);

                            foreach($daterange as $date){


                                $amin = '';
                                $amout = '';
                                $pmin = '';
                                $pmout = '';
                                $amlate = '0:00:00';
                                $pmlate = '0:00:00';

                                $day_name = $date->format("D");
                                $day_num = $date->format("d");
                                $datecondition = $date->format("Y-m-d");
                                $html .= '<tr>';
                                $html .= '<td style="font-size: 11px !important;">'.str_pad($day_num, 2, '0', STR_PAD_LEFT).'</td>';

                                $getemptimelogs = $this->db->select("logtime")->from("prime_employee_attendance_timelogs")
                                    ->where(array("bioid" => $row->bioid , "logdate" => $datecondition))
                                    ->group_by("logtime")
                                    ->order_by("logtime")
                                    ->limit(4)
                                    ->get();
                                $logscount = $getemptimelogs->num_rows();
                                if($getemptimelogs->num_rows() > 0){
                                    $amtime  = array();
                                    $pmtime  = array();
                                    foreach ($getemptimelogs->result() as $timelogsrow){
                                        $html .= '<td  style="font-size: 11px !important;">'.date("g:i A", strtotime($timelogsrow->logtime)).'</td>';

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
                                    }
                                }
                                $data['listofdays'][] = array(
                                    'm' =>  $date->format('m'),
                                    'y' =>  $date->format('Y')
                                );

                                $specifiedTime = checkempsched($row->sysid, $date->format("d"), $date->format('m') , $date->format('Y') , 301)->sepcfiedTime;
                                $specifiedTime2 = checkempsched($row->sysid, $date->format("d"), $date->format('m') , $date->format('Y') , 301)->sepcfiedTime2;

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
                                        $amlate = '0:00:00';
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

                                $totallate = sum_the_time($amlate , $pmlate);

                                if($amlate == '0:00:00' || $amlate == '00:00:00'){
                                    $amlate ='';
                                }
                                if($pmlate == '0:00:00' || $pmlate == '00:00:00'){
                                    $pmlate = '';
                                }
                                if($totallate == '0:00:00' || $totallate == '00:00:00'){
                                    $totallate = '';

                                }

                                if($logscount == 4){
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$amlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$pmlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$totallate.'</td>';
                                }else if($logscount == 3){
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$amlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$pmlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$totallate.'</td>';
                                }else if($logscount == 2){
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$amlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$pmlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$totallate.'</td>';
                                }else if($logscount == 1){
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$amlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$pmlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$totallate.'</td>';
                                }else if($logscount == 0){
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">&nbsp;</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$amlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$pmlate.'</td>';
                                    $html .= '<td style="font-size: 11px !important;">'.$totallate.'</td>';
                                }

                                $html .= '</tr>';
                            }

                            $html .= '</tbody>';
                            $html .= '</table>';

                            $html .= '</div>';
                            //$html .= '</div>';
                        }
                    }
                }
            }
        }else if($emparraycount == 0){
            $data['nospecific'] = 'nospecific';
            $allowed_job_cat = array(157,160);
            $sql = $this->db->select("pem.sysid ,p.lastname , p.firstname , peb.bioid , pemp.position_id")->from("prime_employee_main as pem")
                ->join("person as p" , "p.sysid = pem.personid" , "left")
                ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid && peb.status = 1" , "left")
                ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid && pemjc.status = 1" , "left")
                ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'" , "left")
                ->join("prime_employee_main_positions as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1 && pemp.position_id != 116")
                ->join("prime_employee_main_payclass as pemc" , "pemc.emp_id = pem.sysid && pemc.payclass_id != 131")
                ->where_in('ptp.sysid', $allowed_job_cat)
                ->order_by("p.lastname")
                ->get();
            if($sql->num_rows() > 0){
                $num = 1;
                foreach ($sql->result() as $row){
                    $getcount = $this->db->select("COUNT(peat.sysid) AS totalcnt")
                        ->from("prime_employee_bioid as peb")
                        ->join("prime_employee_attendance_timelogs as peat" , "peat.bioid = peb.bioid")
                        ->where(array("peb.status" => 1 , "peb.empid" => $row->sysid , "YEAR(peat.logdate)" => $year , "MONTH(peat.logdate)" => $month))
                        ->get()->row();
                    if($getcount->totalcnt != 0){
                        $data['data'][] = array(
                            'id'            => $row->sysid,
                            'lastname'      => $row->lastname,
                            'firstname'     => $row->firstname,
                            'bioid'         => $row->bioid
                        );

                        //  $html .= '<div class="col-md-6 col-xs-6 col-sm-6">';
                        $html .= '<div class="col-xs-6" style="font-size:11px !important;width: 50%; display: inline-block; padding: 0px 15px !important;">';

                        $html .= '<div class="col-md-8">'.$num++.'- Name: ' . strtoupper($row->lastname).', '.strtoupper($row->firstname).'</div>';
                        $html .= '<div class="col-md-4">ID: ' . strtoupper($row->bioid).'</div>';
                        $html .= '<div class="col-md-8">Position: ' .$this->gettypesparam($row->position_id).'</div>';
                        $html .= '<div class="col-md-4">Date: ' .$monthName.' 1 - '.$lastday.', '.$year.'</div>';


                        if($month == 2){
                            $html .= '<table class="table table-condensed table-bordered tbl-xs"  style="margin-bottom: 85px !important;">';
                        }else{
                            $data['lastday'] = $lastday;
                            if($lastday == 30){
                                $html .= '<table class="table table-condensed table-bordered tbl-xs" style="margin-bottom: 35px !important;">';
                            }else if ($lastday == 31){
                                $html .= '<table class="table table-condensed table-bordered tbl-xs" >';
                            }

                        }
                        $html .= '<thead>';
                        $html .= '<th></th>';
                        $html .= '<th>IN</th>';
                        $html .= '<th>OUT</th>';
                        $html .= '<th>IN</th>';
                        $html .= '<th>OUT</th>';
                        $html .= '<th>IN</th>';
                        $html .= '<th>OUT</th>';
                        $html .= '<th>IN</th>';
                        $html .= '<th>OUT</th>';
                        $html .= '<th>AM</th>';
                        $html .= '<th>PM</th>';
                        $html .= '<th>TOTAL</th>';
                        $html .= '</thead>';
                        $html .= '<tbody>';

                        $interval = new DateInterval('P1D');
                        $daterange = new DatePeriod($begin, $interval ,$end);

                        foreach($daterange as $date){


                            $amin = '';
                            $amout = '';
                            $pmin = '';
                            $pmout = '';
                            $amlate = '0:00:00';
                            $pmlate = '0:00:00';

                            $day_name = $date->format("D");
                            $day_num = $date->format("d");
                            $datecondition = $date->format("Y-m-d");
                            $html .= '<tr style="border: 1px solid black;">';
                            $html .= '<td style="border:1px solid black;font-size: 11px !important;">'.str_pad($day_num, 2, '0', STR_PAD_LEFT).'</td>';

                            $getemptimelogs = $this->db->select("logtime")->from("prime_employee_attendance_timelogs")
                                ->where(array("bioid" => $row->bioid , "logdate" => $datecondition))
                                ->group_by("logtime")
                                ->order_by("logtime")
                                ->limit(4)
                                ->get();
                            $logscount = $getemptimelogs->num_rows();
                            if($getemptimelogs->num_rows() > 0){
                                $amtime  = array();
                                $pmtime  = array();
                                foreach ($getemptimelogs->result() as $timelogsrow){
                                    $html .= '<td  style="border:1px solid black;font-size: 11px !important;">'.date("g:i A", strtotime($timelogsrow->logtime)).'</td>';

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
                                }
                            }
                            $data['listofdays'][] = array(
                                'm' =>  $date->format('m'),
                                'y' =>  $date->format('Y')
                            );

                            $specifiedTime = checkempsched($row->sysid, $date->format("d"), $date->format('m') , $date->format('Y') , 301)->sepcfiedTime;
                            $specifiedTime2 = checkempsched($row->sysid, $date->format("d"), $date->format('m') , $date->format('Y') , 301)->sepcfiedTime2;

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
                                    $amlate = '0:00:00';
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

                            $totallate = sum_the_time($amlate , $pmlate);

                            if($amlate == '0:00:00' || $amlate == '00:00:00'){
                                $amlate ='';
                            }
                            if($pmlate == '0:00:00' || $pmlate == '00:00:00'){
                                $pmlate = '';
                            }
                            if($totallate == '0:00:00' || $totallate == '00:00:00'){
                                $totallate = '';

                            }

                            if($logscount == 4){
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$amlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$pmlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$totallate.'</td>';
                            }else if($logscount == 3){
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$amlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$pmlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$totallate.'</td>';
                            }else if($logscount == 2){
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$amlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$pmlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$totallate.'</td>';
                            }else if($logscount == 1){
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$amlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$pmlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$totallate.'</td>';
                            }else if($logscount == 0){
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">&nbsp;</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$amlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$pmlate.'</td>';
                                $html .= '<td style="border:1px solid black;font-size: 10px;">'.$totallate.'</td>';
                            }

                            $html .= '</tr>';
                        }

                        $html .= '</tbody>';
                        $html .= '</table>';

                        $html .= '</div>';
                        //   $html .= '</div>';
                    }
                }
            }
        }


        $data['html'] = $html;
        echo json_encode($data);
    }
    function gettypesparam($id){
        $sql = $this->db->select("names")->from("prime_types_parameter")
            ->where(array("sysid" => $id , "codes" => 'EMPOST'))->get()->row();
        if($sql){
            return $sql->names;
        }else{
            return '';
        }
    }
    function getphilhealth(){
        init_header_nonav();

        $html = '';
        $html .= '<div class="container" style="background: #fff">';
        $html .= '<table class="table table-hover table-bordered table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Lastname</th>';
        $html .= '<th>Firstname</th>';
        $html .= '<th>Payclass</th>';
        $html .= '<th>Salary</th>';
        $html .= '<th>CONT</th>';
        $html .= '<th>Cont. Employeer</th>';
        $html .= '<th>Cont. EMployee</th>';

        $html .= '</thead>';
        $html .= '<tbody>';

        $sql = $this->db->select("p.lastname , p.firstname , pes.amt , ptp.names")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_salary as pes" , "pes.empid = pem.sysid && pes.status = 1")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemp.payclass_id && ptp.codes = 'EMPAYCLASS'" , "left")
            ->where(array("pem.status" => 1))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $sql_cont = $this->db->query("
                    SELECT amtcont, rateemployer, rateemployee, amtcap FROM prime_contribution_matrix
                    WHERE status = 1 AND conttype = 73 AND {$row->amt} BETWEEN amtmin AND amtmax 
                ")->row();
                $amt_cont = 0;
                $amt_cont_1 = ($sql_cont) ? ($sql_cont->amtcont + ($row->amt * 0.0275)) : 0;

                if($sql_cont->amtcap > 0) {
                    if($amt_cont_1 > $sql_cont->amtcap) {
                        $amt_cont = $sql_cont->amtcap;
                    }else{
                        $amt_cont = $amt_cont_1;
                    }
                }

                $amt_cont_empe = $amt_cont * $sql_cont->rateemployee;
                $amt_cont_empr = $amt_cont - $amt_cont_empe;
                $html .= '<tr>';
                $html .= '<td>'.$num++.'</td>';
                $html .= '<td>'.$row->lastname.'</td>';
                $html .= '<td>'.$row->firstname.'</td>';
                $html .= '<td>'.$row->names.'</td>';
                $html .= '<td class="number">'.number_format($row->amt ,  2).'</td>';
                $html .= '<td class="number">'.number_format($amt_cont , 2).'</td>';
                $html .= '<td class="number">'.number_format($amt_cont_empr , 2).'</td>';
                $html .= '<td class="number">'.number_format($amt_cont_empe , 2).'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        echo $html;

        init_footer_nonav();
    }

    function gethdmf(){
        init_header_nonav();

        $html = '';
        $html .= '<div class="container" style="background: #fff">';
        $html .= '<table class="table table-hover table-bordered table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Lastname</th>';
        $html .= '<th>Firstname</th>';
        $html .= '<th>Payclass</th>';
        $html .= '<th>Cont. Employeer</th>';
        $html .= '<th>Cont. EMployee</th>';

        $html .= '</thead>';
        $html .= '<tbody>';

        $sql = $this->db->select("p.lastname , p.firstname , pes.amt , ptp.names")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_salary as pes" , "pes.empid = pem.sysid && pes.status = 1")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemp.payclass_id && ptp.codes = 'EMPAYCLASS'" , "left")
            ->where(array("pem.status" => 1))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $html .= '<tr>';
                $html .= '<td>'.$num++.'</td>';
                $html .= '<td>'.$row->lastname.'</td>';
                $html .= '<td>'.$row->firstname.'</td>';
                $html .= '<td>'.$row->names.'</td>';
                $html .= '<td class="number">100</td>';
                $html .= '<td class="number">100</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        echo $html;

        init_footer_nonav();
    }


    function getsss(){
        init_header_nonav();

        $html = '';
        $html .= '<div class="container" style="background: #fff">';
        $html .= '<table class="table table-hover table-bordered table-condensed">';
        $html .= '<thead>';
        $html .= '<th></th>';
        $html .= '<th>Lastname</th>';
        $html .= '<th>Firstname</th>';
        $html .= '<th>Payclass</th>';
        $html .= '<th>Salary</th>';
        $html .= '<th>SSS. Employeer</th>';
        $html .= '<th>SSS. EMployee</th>';

        $html .= '</thead>';
        $html .= '<tbody>';

        $sql = $this->db->select("p.lastname , p.firstname , pes.amt , ptp.names")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_salary as pes" , "pes.empid = pem.sysid && pes.status = 1")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid && pemp.status = 1" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemp.payclass_id && ptp.codes = 'EMPAYCLASS'" , "left")
            ->where(array("pem.status" => 1))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $employeesss = 0;
                $employersss = 0;
                $salary = $row->amt;

                $selectbracket =  $this->db->query("SELECT amtcont , rateemployer , rateemployee FROM prime_contribution_matrix
                WHERE status = 1 AND  ({$salary} BETWEEN amtmin AND amtmax || {$salary} > amtmin && amtmax = 0 )")->row();
                if($selectbracket){
                    $employeesss = ($selectbracket->amtcont * $selectbracket->rateemployee);
                    $employersss = ($selectbracket->amtcont * $selectbracket->rateemployer);
                }


                $html .= '<tr>';
                $html .= '<td>'.$num++.'</td>';
                $html .= '<td>'.$row->lastname.'</td>';
                $html .= '<td>'.$row->firstname.'</td>';
                $html .= '<td>'.$row->names.'</td>';
                $html .= '<td class="number">'.number_format($salary , 2).'</td>';
                $html .= '<td class="number">'.number_format($employeesss , 2).'</td>';
                $html .= '<td class="number">'.number_format($employersss , 2).'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        echo $html;

        init_footer_nonav();
    }
    function submitflexi(){
        $data = array();
        $employee = $this->input->post('empsubmitflexiloyee');
        $trntype = $this->input->post('trntype');
        $totalinminutes = 0;
        // $amcutoff = '12:00:00';
        // $pmcutoff = '13:00:00';


        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $expiration = $this->input->post('expiration');
        $fromflexihour = $this->input->post('fromflexihour');
        $fromfleximinutes = $this->input->post('fromfleximinutes');
        $fromflexiampm = $this->input->post('fromflexiampm');
        $toflexihour = $this->input->post('toflexihour');
        $tofleximinutes = $this->input->post('tofleximinutes');
        $toflexiampm = $this->input->post('toflexiampm');
        $purpose = $this->input->post('purpose');
        $startofflexi = $this->input->post('startofflexi');

        $fromtime = $fromflexihour.':'.$fromfleximinutes.':00 '.$fromflexiampm;
        $totime = $toflexihour.':'.$tofleximinutes.':00 '.$toflexiampm;

        $data['fromtime'] = $fromtime;
        $data['totime'] = $totime;
        if($trntype == 1){
            $fromflexihour = null;
            $fromfleximinutes = null;
            $fromflexiampm= null;
            $toflexihour = null;
            $tofleximinutes = null;
            $toflexiampm = null;

            $fromtime = '00:00:00';
            $totime = '00:00:00';


            $datetime1 = new DateTime($fromdate);
            $datetime2 = new DateTime($todate);
            $datetime2->modify('+1 day');
            $totaldays = $datetime1->diff($datetime2)->format("%a");
            $data['totaldays'] = $totaldays;
            $totalinminutes = (($totaldays) * 8 * 60);
        }else if($trntype == 2){
            $fromdate = null;
            $todate = null;



            $startTime = new DateTime($fromtime);
            //  $amCutoffd = new DateTime($amcutoff);
            $endTime = new DateTime($totime);
            //  $pmCutoffd = new DateTime($pmcutoff);

            /* if($endTime > $startTime  && $amCutoffd <  $endTime && $startTime < $amCutoffd){
                 $startampm = $startTime;
                 $endammpm = $endTime;
                 $data['startampm'] = $startampm;
                 $data['endampm'] = $endammpm;

                 $first = $startTime->diff($amCutoffd); //$duration is a DateInterval object
                 $second = $endTime->diff($pmCutoffd);
                 $totalinminutes =  converttimetominutes($first->format("%H:%I:%S")) + converttimetominutes($second->format("%H:%I:%S"));
                 $data[] = 'Between';
             }else{ */
            $totalinminutes =   converttimetominutes($startTime->diff($endTime)->format("%H:%I:%S"));
            $data[] = 'Not Between';
            //  }
        }

        $this->db->trans_begin();
        $date = date("Y-m-d");
        $insarr = array(
            'empid' => $employee,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'fromtime' =>  ($fromtime) ? date("H:i:s", strtotime($fromtime)) : '',
            'totime' => ($totime) ? date("H:i:s", strtotime($totime)) : '',
            'totalinminutes' => $totalinminutes,
            'purpose' => $purpose,
            'startofflexi' => $startofflexi,
            'expmonth' => add_month_to_date(date('Y-m-d H:i:s', strtotime($startofflexi . ' +1 day')) , 1).' '.date('h:i:s'),
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );

        $sql = $this->db->insert("flexi_credits" , $insarr);
        $data['errormsg'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Flexi credit has been added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Error adding flexi credit.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function submitflexitrn(){
        $data = array();
        $employee = $this->input->post('employee');
        $flexitype = $this->input->post('flexitype');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $fromhours = $this->input->post('fromhours');
        $fromminutes = $this->input->post('fromminutes');
        $fromampm = $this->input->post('fromampm');
        $tohours = $this->input->post('tohours');
        $tominutes = $this->input->post('tominutes');
        $toampm = $this->input->post('toampm');
        $remarks = $this->input->post('remarks');
        $leavedate = $this->input->post('leavedate');
        $flexiyear = $this->input->post('flexiyear');
        $fromtime = $fromhours.':'.$fromminutes.':00 '.$fromampm;
        $totime = $tohours.':'.$tominutes.':00 '.$toampm;
        $totalinminutes = 0;
        $amcutoff = '12:00:00';
        $pmcutoff = '13:00:00';

        $msg = '';
        $qry = false;
        $func = '';

        $totalbalanceminutes = 0;
        $this->db->trans_begin();

        if($employee == '' || $employee == null){
            $this->db->trans_rollback();
            $msg = 'Please select employee';
            $qry = false;
            $func = 'info';
        }else{
            if($flexitype == 1){
                $fromtime = '00:00:00';
                $totime = '00:00:00';

                $datetime1 = new DateTime($fromdate);
                $datetime2 = new DateTime($todate);
                $datetime2->modify('+1 day');

                $totaldays = $datetime1->diff($datetime2)->format("%a");
                $data['totaldays'] = $totaldays;
                $totalinminutes = (($totaldays) * 8 * 60);
            }else if($flexitype == 2){
                $fromdate = null;
                $todate = null;
                $startTime = new DateTime($fromtime);
                $amCutoffd = new DateTime($amcutoff);
                $endTime = new DateTime($totime);
                $pmCutoffd = new DateTime($pmcutoff);



                if($endTime > $startTime  && $amCutoffd <  $endTime && $startTime < $amCutoffd){
                    $startampm = $startTime;
                    $endammpm = $endTime;
                    $data['startampm'] = $startampm;
                    $data['endampm'] = $endammpm;

                    $first = $startTime->diff($amCutoffd); //$duration is a DateInterval object
                    $second = $endTime->diff($pmCutoffd);
                    $totalinminutes =  converttimetominutes($first->format("%H:%I:%S")) + converttimetominutes($second->format("%H:%I:%S"));
                    $data[] = 'Between';
                }else{
                    $totalinminutes =   converttimetominutes($startTime->diff($endTime)->format("%H:%I:%S"));
                    $data[] = 'Not Between';
                }
            }


            //PERFORM CHECKING IF CREDITS IS OK
            $flexibalance = $this->db->select("SUM(totalinminutes) AS totalbal")
                ->from("flexi_credits")
                ->where(array("empid" => $employee , "status" => 1))
                ->get()->row();
            $defaultbal = ($flexibalance) ? $flexibalance->totalbal : 0;
            $totalbal = ($flexibalance) ? $flexibalance->totalbal : 0;

            $approvedflexi = $this->db->select("SUM(totalinminutes) AS totalapprovedflexi")
                ->from("flexi_leave_transaction")
                ->where(array("status" => 301 , "empid" => $employee))
                ->get()->row();
            $approvedflexitotal = ($approvedflexi) ? $approvedflexi->totalapprovedflexi : 0;

            $totalbal = ($totalbal  - $approvedflexitotal);



            $flexitransactionbalance = $this->db->select("SUM(totalinminutes) AS totalpending")
                ->from("flexi_leave_transaction")
                ->where(array("status" => 307 , "empid" => $employee))
                ->get()->row();
            $totalpending = ($flexitransactionbalance) ? $flexitransactionbalance->totalpending + $totalinminutes : 0;


            $data['totalbal'] = $defaultbal;
            $data['approvespent'] = $approvedflexi->totalapprovedflexi;
            $data['totalpending'] = $totalpending;

            if($totalpending > $totalbal){
                $this->db->trans_rollback();
                $msg = 'Not enough credits.';
                $qry = false;
                $func = 'info';
            }else{
                $data['fromtime'] = $fromtime;
                $data['totime'] = $totime;
                $insarr = array(
                    'empid' => $employee,
                    'fromdate' => $fromdate,
                    'todate' => $todate,
                    'fromtime' => ($fromtime) ? date("H:i:s", strtotime($fromtime)) : '',
                    'totime' => ($totime) ? date("H:i:s", strtotime($totime)) : '',
                    'totalinminutes' => $totalinminutes,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 307,
                    'leavedate' => ($leavedate) ? $leavedate : null,
                    'year' => $flexiyear,
                    'remarks' => $remarks,
                    'type' => 3
                );
                $sql = $this->db->insert("flexi_leave_transaction" , $insarr);
                $data['flexitrnerr'] = $this->db->_error_message();
                if($this->db->trans_status() == true && $sql){
                    $this->db->trans_commit();
                    $msg = 'Flexi transaction added.';
                    $qry = true;
                    $func = 'success';
                }else{
                    $this->db->trans_rollback();
                    $msg = 'Failed to add flexi transaction.';
                    $qry = false;
                    $func = 'error';
                }
            }





        }



        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['empid'] = $employee;
        echo json_encode($data);
    }
    function getflexibalancerecord(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $totalminutes = 0;



        $sql = $this->db->select("totalinminutes , purpose , expmonth , datecreated")->from("flexi_credits")
            ->where(array('status' => 1 , "empid" => $dataid))->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $totalminutes += $row->totalinminutes;

                $totalspenthours = $row->totalinminutes / 60;
                $dayspent = floor($totalspenthours / 8);
                $hourspent = ($totalspenthours % 8);
                $n = $totalspenthours;
                $whole = floor($n);      // 1
                $minutespent = ($n - $whole) * 60;

                $totalflexibal = $dayspent.'-'.$hourspent.'-'.$minutespent;
                $data['empflexibalancedata'][] = array(
                    'num' => $num++,
                    'totalinminutes' => $totalflexibal,
                    'purpose' => $row->purpose,
                    'expiry' => $row->expmonth,
                    'datecreated' => $row->datecreated
                );
            }
        }

        $getspentflexi = $this->db->select("SUM(flt.totalinminutes) AS totalmins")
            ->from("flexi_leave_transaction as flt")
            ->join("flexi_leave_main as flm" , "flm.sysid = flt.groupid")
            ->where(array("flt.status" => 301 , "flm.status" => 301 , "flt.empid" => $dataid))
            ->get()->row();
        if($getspentflexi){
            $totalminutes = $totalminutes - $getspentflexi->totalmins;
        }

        $totalspenthours = $totalminutes / 60;
        $dayspent = floor($totalspenthours / 8);
        $hourspent = ($totalspenthours % 8);
        $n = $totalspenthours;
        $whole = floor($n);      // 1
        $minutespent = ($n - $whole) * 60;
        $data['totalbalance'] = $dayspent.'-'.$hourspent.'-'.$minutespent;
        echo json_encode($data);
    }
    function getflexipendingtrn(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $totalminutes= 0;

        $sql = $this->db->select("sysid , empid , fromdate,todate,fromtime, totime,totalinminutes")->from("flexi_leave_transaction")
            ->where(array("status" => 307 , "empid" => $dataid))
            ->order_by("sysid","desc")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $totalminutes += $row->totalinminutes;

                $totalspenthours =  $row->totalinminutes / 60;
                $dayspent = floor($totalspenthours / 8);
                $hourspent = ($totalspenthours % 8);
                $n = $totalspenthours;
                $whole = floor($n);      // 1
                $minutespent = ($n - $whole) * 60;

                $data['flexipendingtrndata'][] = array(
                    'num' => $num++,
                    'fromdate' => $row->fromdate,
                    'todate' => $row->todate,
                    'fromtime' => $row->fromtime,
                    'totime' => $row->totime,
                    'total' => $dayspent.'-'.$hourspent.'-'.$minutespent,
                    'control' => '<button id="deleteflexibtn" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs btn-inline"><i class="fa fa-trash"></i></button>',
                );
            }
        }

        $totalspenthours =  $totalminutes / 60;
        $dayspent = floor($totalspenthours / 8);
        $hourspent = ($totalspenthours % 8);
        $n = $totalspenthours;
        $whole = floor($n);      // 1
        $minutespent = ($n - $whole) * 60;
        $data['total'] = $dayspent.'-'.$hourspent.'-'.$minutespent;
        echo json_encode($data);
    }
    function savependingflexitrn(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $this->db->trans_begin();

        $flexibalance = $this->db->select("SUM(totalinminutes) AS totalbal")
            ->from("flexi_credits")
            ->where(array("empid" => $dataid , "status" => 1))
            ->get()->row();
        $totalbal = ($flexibalance) ? $flexibalance->totalbal : 0;

        $flexitransactionbalance = $this->db->select("SUM(totalinminutes) AS totalpending")
            ->from("flexi_leave_transaction")
            ->where(array("status" => 307 , "empid" => $dataid))
            ->get()->row();
        $totalpending = ($flexitransactionbalance) ? $flexitransactionbalance->totalpending : 0;


        $data['totalbal'] = $totalbal;
        $data['totalpending'] = $totalpending;

        if($totalpending > $totalbal) {
            $this->db->trans_rollback();
            $msg = 'Not enough credits.';
            $qry = false;
            $func = 'info';
        }else{
            $insarr = array(
                'status' => 301,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $sql = $this->db->insert("flexi_leave_main" , $insarr);
            $lastid =  $this->db->insert_id();

            if($sql && $lastid > 0){
                $updatearr = array(
                    'groupid' => $lastid,
                    'status' => 301
                );
                $this->db->where(array("empid" => $dataid , "status" => 307));
                $updateleave =$this->db->update("flexi_leave_transaction" , $updatearr);
                if($this->db->trans_status() == true && $updateleave){
                    $this->db->trans_commit();
                    $msg = 'Flexi Leave has been saved.';
                    $qry = true;
                    $func = 'success';
                }else{
                    $this->db->rollback();
                    $msg = 'Failed to save flexi transaction.';
                    $qry = false;
                    $func = 'error';
                }
            }else{
                $this->db->rollback();
                $msg = 'Failed to save flexi main. Please contact your administrator.';
                $qry = false;
                $func = 'error';
            }
        }




        $data['empid'] = $dataid;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;

        echo json_encode($data);
    }
    function getemployeeworkshift(){
        $data = array();
        $sql = $this->db->select("pem.sysid as empid, penw.sysid , p.lastname , p.firstname,penw.am_start , penw.am_end , penw.pm_start , penw.pm_end")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_workshift_matrix as pemwm" , "pemwm.empid = pem.sysid" , "left")
            ->join("prime_employee_main_workshift as penw" , "penw.sysid = pemwm.workshift_id")
            ->where(array("pemwm.status" => 1 , "pem.status" => 1))
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $amstart =$row->am_start;
                $amend =  $row->am_end;
                $pmstart =$row->pm_start;
                $pmend =$row->pm_end;
                if($amstart != null && $amstart != ''){
                    $amstart = date("g:i A", strtotime($amstart));
                }else{
                    $amstart = '';
                }
                if($amend != null && $amend != ''){
                    $amend = date("g:i A", strtotime($amend));
                }else{
                    $amend = '';
                }
                if($pmstart != null && $pmstart != ''){
                    $pmstart = date("g:i A", strtotime($pmstart));
                }else{
                    $pmstart = '';
                }
                if($pmend != null && $pmend != ''){
                    $pmend = date("g:i A", strtotime($pmend));
                }else{
                    $pmend = '';
                }
                $data['empworkshiftdata'][] = array(
                    'num' => $num++,
                    'empname' => $row->lastname.', '.$row->firstname,
                    'amstart' => $amstart,
                    'amend' => $amend,
                    'pmstart' => $pmstart,
                    'pmend' => $pmend,
                    'control' => '<input style="width:100% !important;" value="'.$row->sysid.'" data-id="'.$row->empid.'" type="text" class="form-control" id="empcurrentworkshift" />',
                    'workshiftid' => $row->sysid
                );
            }
        }
        echo json_encode($data);
    }
    function deletefixamt(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0,
            'updatedby' => user_id()
        );
        $this->db->where(array("status" => 1 , "sysid" => $dataid));
        $sql = $this->db->update("payroll_fix_amt" , $updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Transaction amount has been deleted.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to delete transaction amount.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function deleteflexitrn(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid , "status" => 307));
        $sql = $this->db->update("flexi_leave_transaction" , $updatearr);

        if($this->db->trans_status() && $sql){
            $this->db->trans_commit();
            $msg = 'Transaction has been removed.';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove transaction.';
            $qry = false;
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        echo json_encode($data);
    }
    function removepos(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatestat = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid , "codes" => 'EMPOST' , "status" => 1));
        $sql = $this->db->update("prime_types_parameter" , $updatestat);
        if($this->db->trans_status() && $sql){
            $this->db->trans_commit();
            $msg = 'Position has been removed.';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove position.';
            $qry = false;
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;

        echo json_encode($data);
    }
    function deleteleavedraft(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid , "status" => 1));
        $sql = $this->db->update("trn_employee_leave_draft_request" , $updatearr);
        $data['qryerr'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Transaction has been removed';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove transaction';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function deleteleavedraft_(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid , "status" => 1));
        $sql = $this->db->update("trn_employee_leave_draft_request" , $updatearr);
        $data['qryerr'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Transaction has been removed';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove transaction';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function getpayrollemployee(){
        $data = array();
        $jobcatarr = array(157 , 160);
        $sql = $this->db->select("pe.sysid , p.lastname , p.firstname , pe.accntno , pe.payclass , ptp.names , pem.status")
            ->from("payroll_emplist as pe")
            ->join("prime_employee_main as pem" , "pem.sysid = pe.empid")
            ->join("person as p" , "p.sysid = pem.personid")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pe.costgroup")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pe.empid")
            ->where(array("pe.status" => 1))
            ->where_in('pemjc.jobcatid' , $jobcatarr)
            ->order_by("pem.status")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){


                $getpayclass = $this->db->select("names")->from("prime_types_parameter")
                    ->where(array("status" => 1 , "sysid" => $row->payclass))
                    ->get()->row();
                $payclass = ($getpayclass) ? $getpayclass->names : '';
                if($row->status == 0){
                    $status = '<span class="label label-sm label-danger">Deactivated</span>';
                }else{
                    $status = '<span class="label label-sm label-success">Active</span>';
                }
                $data['payrollemplist'][]  =array(
                    'num' => $num++ ,
                    'lastname' => $row->lastname ,
                    'firstname' => $row->firstname,
                    'accntno' => $row->accntno,
                    'payclass' =>$payclass,
                    'status' => $status,
                    'group' =>$row->names,
                    'control' => '<button id="payrollempremovebtn" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i> Remove</button>'
                );
            }
        }
        echo json_encode($data);
    }
    function getempencodedcredits(){
        $data = array();
        $year = $this->input->post('year');

        $vl = '';
        $sl = '';
        $el = '';
        if($year > 0){
            $this->db->where(array("pemlc.year" => $year));
        }else{
            $this->db->where(array("pemlc.year" => date('Y')));
        }
        $getemps = $this->db->select("pemlc.empid,p.lastname , p.firstname")->from("prime_employee_main_leave_credits as pemlc")
            ->join("prime_employee_main as pem" , "pem.sysid = pemlc.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->group_by("pemlc.empid")
            ->get();
        if($getemps->num_rows() > 0){
            $num = 1;
            foreach ($getemps->result() as $row){
                $lastname = $row->lastname;
                $firstname = $row->firstname;
                $empid = $row->empid;


                if($year > 0){
                    $this->db->where(array("year" => $year));
                }else{
                    $this->db->where(array("year" => date('Y')));
                }
                $getcredits = $this->db->select("credit , types")
                    ->from("prime_employee_main_leave_credits")
                    ->where(array("status" => 1 , "empid" => $empid))
                    ->limit(3)
                    ->get();
                $data['qryerr'] = $this->db->_error_message();
                if($getcredits->num_rows() > 0){
                    foreach ($getcredits->result() as $credits) {
                        if($credits->types == 330){
                            $vl = $credits->credit;
                        }else if($credits->types == 331){
                            $sl = $credits->credit;
                        }else if($credits->types == 332){
                            $el = $credits->credit;
                        }
                    }
                    $data['listdata'][] = array(
                        'num' => $num++,
                        'empid' =>$empid,
                        'lastname' => $lastname,
                        'firstname' => $firstname,
                        'vl' => $vl,
                        'sl' => $sl,
                        'el' => $el
                    );
                }
            }
        }

        echo json_encode($data);
    }
    function getempleavehistory(){
        $data = array();

        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $report = $this->input->post('report');
        $leavetype = $this->input->post('leavetype');
        $html = '';


        if($report > 0){

            $getinfo = $this->db->select("p.lastname , p.firstname , p.middlename")
                ->from("prime_employee_main as pem")
                ->join("person as p" , "p.sysid = pem.personid")
                ->where(array("pem.sysid" => $empid))
                ->get()->row();
            $empname = ($getinfo) ? $getinfo->lastname.', '.$getinfo->firstname.' '.$getinfo->middlename : '';

            $getleavedes = $this->db->select("desc")->from("prime_types_parameter")
                ->where(array("sysid" => $leavetype))->get()->row();
            $leavedesc = ($getleavedes) ? $getleavedes->desc : 'N/A';

            $qry = $this->db->query("SELECT DISTINCT tp.sysid, tp.names, tp.desc,lc.types, SUM(lc.credit) AS totalcredit
FROM prime_employee_main_leave_credits AS lc
LEFT JOIN prime_types_parameter AS tp ON tp.sysid = lc.types
WHERE lc.empid = $empid  AND lc.year = $year AND lc.status = 1 AND lc.types = $leavetype
GROUP BY tp.sysid, tp.names, tp.desc,lc.types")->row();

            $beginning = ($qry) ? $qry->totalcredit : '';
            $types = ($qry) ? $qry->types : '';


            $getleavedetails = $this->db->select("SUM(totalinminutes) AS totalminutes")->from("trn_employee_leave_requests")
                ->where(array("empid" => $empid , "leavetype" => $types , "year" => $year , "status" => 301))
                ->get()->row();

            $totalspentminutes = ($getleavedetails) ? $getleavedetails->totalminutes : 0;

            $totalspenthours = $totalspentminutes / 60;
            $dayspentfoot = floor($totalspenthours / 8);
            $hourspentfoot = ($totalspenthours % 8);
            $n = $totalspenthours;
            $whole = floor($n);      // 1
            $minutespentfoot = ($n - $whole) * 60;


            $totalspentminutes = $minutespentfoot + ($hourspentfoot * 60) + ($dayspentfoot * 8 * 60);


            $daybalance = 0;
            $hourbalance = 0;
            $minutebalance = 0;


            //BALANCE
            $balanceminutes = $beginning * 8 * 60;
            $ramainingminutes = $balanceminutes - $totalspentminutes;

            $totalbalancehours = $ramainingminutes / 60;
            $daybalancefoot = floor($totalbalancehours / 8);
            $hourbalancefoot =  ($totalbalancehours % 8);
            $n = $totalbalancehours;
            $whole = floor($n);      // 1
            $minutebalancefoot = ($n - $whole) * 60;


            $html .= employee_print_header($empid, 'Leave Details');

            $html .= '<div class="row">';
            $html .= '<div class="col-md-3">';
            $html .= '<span>Leave Type: '.$leavedesc.'</span>';
            $html .= '</div>';
            $html .= '<br>';
            $html .= '<div class="col-md-7">';
            $html .= '</div>';
            $html .= '<div class="col-md-2 pull-right">';
            $html .= '<span>Date: '.date('Y-m-d h:i:s').'</span>';
            $html .= '</div>';
            $html .= '</div>';

        }

        $html .= '<table class="table table-bordered table-striped table-condensed table-responsive print-table-standard" id="empleavehistorytbl">';
        $html .= '<thead>';
        $html .= ' <th></th>
                <th>Date of Application</th>
                <th>From</th>
                <th>To</th>
                <th>From Time</th>
                <th>To Time</th>
                <th>IDa</th>
                <th>IHr</th>
                <th>IMin</th>
                <th>L-type</th>
                <th>Date Encoded</th>
                <th>Status</th>';
        $html .= '</thead>';
        $html .= '<tbody>';

        if($leavetype > 0){
            $this->db->where(array("telr.leavetype" => $leavetype));
        }

        $sql = $this->db->select("telr.leavedate , telr.datecreated , telr.from , telr.to 
                , telr.fromtime , telr.totime ,telr.totalinminutes , telr.type , ptp.names , DATE(telr.datecreated) , telr.status")
            ->from("trn_employee_leave_requests as telr")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = telr.leavetype" , "left")
            ->where(array("telr.empid" => $empid , "telr.year" => $year))
            ->order_by("telr.leavedate" , "desc")
            ->get();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                if($row->fromtime == '00:00:00' && $row->totime == '00:00:00'){
                    $fromtime = '';
                    $totime = '';
                }else{
                    $fromtime = date("g:i A", strtotime($row->fromtime));
                    $totime = date("g:i A", strtotime($row->totime));
                }
                //SPENT
                $totalspenthours = $row->totalinminutes / 60;
                $dayspent = floor($totalspenthours / 8);
                $hourspent = ($totalspenthours % 8);
                $n = $totalspenthours;
                $whole = floor($n);      // 1
                $minutespent = ($n - $whole) * 60;

                $status = ($row->status == 301) ? '<span class="text-success">Approved</span>' : '<span class="text-danger">Canceled</span>';

                $html .= '
                          <tr>
                            <td>'.$num++.'</td>
                            <td>'.$row->leavedate.'</td>
                            <td>'.$row->from.'</td>
                            <td>'.$row->to.'</td>
                            <td>'.$fromtime.'</td>
                            <td>'.$totime.'</td>
                            <td>'.$dayspent.'</td>
                            <td>'.$hourspent.'</td>
                            <td>'.$minutespent.'</td>
                            <td>'.$row->names.'</td>
                            <td>'.date('Y-m-d', strtotime($row->datecreated)).'</td>
                            <td>'.$status.'</td>
                        </tr>
                        ';
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';
        if($report > 0) {
            $beginning = $beginning * 8 * 60;
            $totalbeginhours = $beginning / 60;
            $daybegin = floor($totalbeginhours / 8);
            $hourbegin = ($totalbeginhours % 8);
            $n = $totalbeginhours;
            $whole = floor($n);      // 1
            $minutebegin = ($n - $whole) * 60;

            $html .= '<div class="row" style="margin: 5px 5px">';
            $html .= '<div class="col-md-4 col-sm-4 col-xs-4">';
            $html .= '<span>Beginning: ' .  $daybegin . ' - ' . $hourbegin . ' - ' . round($minutebegin) . '</span>';
            $html .= '</div>';
            $html .= '<div class="col-md-4 col-sm-4 col-xs-4">';
            $html .= '<span>Total Incurred: ' . $dayspentfoot . ' - ' . $hourspentfoot . ' - ' . round($minutespentfoot) . '</span>';
            $html .= '</div>';
            $html .= '<div class="col-md-4 col-sm-4 col-xs-4">';
            $html .= '<span>Total Ending Balance: '.$daybalancefoot.' - '.$hourbalancefoot.' - '.round($minutebalancefoot).'</span>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $data['html'] = $html;
        $data['report'] = $report;

        echo json_encode($data);
    }
    function testing(){
        $sql = $this->db->query("CALL FLEXI_CHECKER()");
        print_r($sql->result());
    }
    function getflexispent(){
        $data = array();
        $empid = $this->input->post('empid');
        $totalminutes = 0;
        $sql = $this->db->select("fromdate,todate,fromtime,totime,leavedate,datecreated,totalinminutes")
            ->from("flexi_leave_transaction")
            ->where(array("empid" => $empid , "status" => 301))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $totalminutes += $row->totalinminutes;
                $data['flexiincurreddata'][] = array(
                    'num' => $num++,
                    'fromdate' => $row->fromdate,
                    'todate' => $row->todate,
                    'fromtime' => $row->fromtime,
                    'totime' => $row->totime,
                    'leavedate' => $row->leavedate,
                    'datecreated' => $row->datecreated
                );
            }
        }
        $totalspenthours =  $totalminutes / 60;
        $dayspent = floor($totalspenthours / 8);
        $hourspent = ($totalspenthours % 8);
        $n = $totalspenthours;
        $whole = floor($n);      // 1
        $minutespent = ($n - $whole) * 60;
        $data['total'] = $dayspent.'-'.$hourspent.'-'.$minutespent;
        echo json_encode($data);
    }
    function addunioncredits(){
        $data = array();
        $unionyear = $this->input->post('unionyear');
        $uniondays = $this->input->post('uniondays');
        $unionhours = $this->input->post('unionhours');
        $this->db->trans_begin();
        $unionhours = $unionhours / 8;
        $days = ($uniondays + $unionhours);
        $insarr = array(
            'credit' =>$days,
            'types' => 3070,
            'year' => $unionyear,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("union_credits" , $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Union credits has been added';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add union credits';
            $qry = false;
            $func = 'error';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        echo json_encode($data);
    }
    function submitunionleave(){
        $data = array();

        $unionempname  = $this->input->post('unionempname');
        $unionyear  = $this->input->post('unionyear');
        $uniontype  = $this->input->post('uniontype');
        $fromdate  = $this->input->post('fromdate');
        $todate  = $this->input->post('todate');
        $fromhours  = $this->input->post('fromhours');
        $fromminutes  = $this->input->post('fromminutes');
        $fromampm  = $this->input->post('fromampm');
        $tohours  = $this->input->post('tohours');
        $tominutes  = $this->input->post('tominutes');
        $toampm  = $this->input->post('toampm');
        $leavedate  = $this->input->post('leavedate');
        $remarks  = $this->input->post('remarks');
        $fromtime = $fromhours.':'.$fromminutes.':00 '.$fromampm;
        $totime = $tohours.':'.$tominutes.':00 '.$toampm;
        $totalinminutes = 0;
        $amcutoff = '12:00:00';
        $pmcutoff = '13:00:00';

        $msg = '';
        $qry = false;
        $func = '';

        $totalbalanceminutes = 0;
        $this->db->trans_begin();

        if($unionempname == '' || $unionempname == null){
            $this->db->trans_rollback();
            $msg = 'Please select employee';
            $qry = false;
            $func = 'info';
        }else{
            if($uniontype == 1){
                $fromtime = '00:00:00';
                $totime = '00:00:00';

                $datetime1 = new DateTime($fromdate);
                $datetime2 = new DateTime($todate);
                $datetime2->modify('+1 day');

                $totaldays = $datetime1->diff($datetime2)->format("%a");
                $data['totaldays'] = $totaldays;
                $totalinminutes = (($totaldays) * 8 * 60);
            }else if($uniontype == 2){
                $fromdate = null;
                $todate = null;
                $startTime = new DateTime($fromtime);
                $amCutoffd = new DateTime($amcutoff);
                $endTime = new DateTime($totime);
                $pmCutoffd = new DateTime($pmcutoff);



                if($endTime > $startTime  && $amCutoffd <  $endTime && $startTime < $amCutoffd){
                    $startampm = $startTime;
                    $endammpm = $endTime;
                    $data['startampm'] = $startampm;
                    $data['endampm'] = $endammpm;

                    $first = $startTime->diff($amCutoffd); //$duration is a DateInterval object
                    $second = $endTime->diff($pmCutoffd);
                    $totalinminutes =  converttimetominutes($first->format("%H:%I:%S")) + converttimetominutes($second->format("%H:%I:%S"));
                    $data[] = 'Between';
                }else{
                    $totalinminutes =   converttimetominutes($startTime->diff($endTime)->format("%H:%I:%S"));
                    $data[] = 'Not Between';
                }
            }


            //PERFORM CHECKING IF CREDITS IS OK
            $unionbalance = $this->db->select("SUM(credit) AS totalbal")
                ->from("union_credits")
                ->where(array("status" => 1))
                ->get()->row();
            $defaultbal = ($unionbalance) ? $unionbalance->totalbal * 8 * 60 : 0;
            $totalbal = ($unionbalance) ? $unionbalance->totalbal * 8 * 60 : 0;

            $approvedunion = $this->db->select("SUM(totalinminutes) AS totalapprovedunion")
                ->from("flexi_leave_transaction")
                ->where(array("status" => 301 , "empid" => $unionempname))
                ->get()->row();
            $approveduniontotal = ($approvedunion) ? $approvedunion->totalapprovedunion : 0;

            $totalbal = ($totalbal  - $approveduniontotal);



            $uniontransactionbalance = $this->db->select("SUM(totalinminutes) AS totalpending")
                ->from("union_leave_transaction")
                ->where(array("status" => 307 , "empid" => $unionempname))
                ->get()->row();
            $totalpending = ($uniontransactionbalance) ? $uniontransactionbalance->totalpending + $totalinminutes : 0;


            $data['totalbal'] = $defaultbal;
            $data['approvespent'] = $approvedunion->totalapprovedunion;
            $data['totalpending'] = $totalpending;

            if($totalpending > $totalbal){
                $this->db->trans_rollback();
                $msg = 'Not enough credits.';
                $qry = false;
                $func = 'info';
            }else{
                $data['fromtime'] = $fromtime;
                $data['totime'] = $totime;
                $insarr = array(
                    'empid' => $unionempname,
                    'fromdate' => $fromdate,
                    'todate' => $todate,
                    'fromtime' => ($fromtime) ? date("H:i:s", strtotime($fromtime)) : '',
                    'totime' => ($totime) ? date("H:i:s", strtotime($totime)) : '',
                    'totalinminutes' => $totalinminutes,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 307,
                    'leavedate' => ($leavedate) ? $leavedate : null,
                    'year' => $unionyear,
                    'remarks' => $remarks,
                    'type' => 4
                );
                $sql = $this->db->insert("union_leave_transaction" , $insarr);
                $data['flexitrnerr'] = $this->db->_error_message();
                if($this->db->trans_status() == true && $sql){
                    $this->db->trans_commit();
                    $msg = 'Union transaction added.';
                    $qry = true;
                    $func = 'success';
                }else{
                    $this->db->trans_rollback();
                    $msg = 'Failed to add union transaction.';
                    $qry = false;
                    $func = 'error';
                }
            }
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['empid'] = $unionempname;
        $data['year'] = $unionyear;
        echo json_encode($data);
    }
    function getunionbalance(){
        $data = array();


        $year = $this->input->post('year');



        $sql = $this->db->select("")->from("union_credits")->where(array("status" => 1 , "year" => $year))
            ->get();
        $data['error'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $gettotalspent = $this->db->select("SUM(totalinminutes) as Totalspent")->from("union_leave_transaction")
                    ->where(array("year" => $year , "status" => 301))
                    ->get()->row();
                $totalspent = ($gettotalspent) ? $gettotalspent->Totalspent : 0;


                //SPENT
                $totalspenthours = $totalspent / 60;
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
                $balanceminutes = $row->credit * 8 * 60;
                $ramainingminutes = $balanceminutes - $totalspentminutes;

                $totalbalancehours = $ramainingminutes / 60;
                $daybalance = floor($totalbalancehours / 8);
                $hourbalance =  ($totalbalancehours % 8);
                $n = $totalbalancehours;
                $whole = floor($n);      // 1
                $minutebalance = ($n - $whole) * 60;


                $data['unionleavedata'][] = array(
                    'num' => $num++,
                    'credit' =>$daybalance.' - '.$hourbalance.' - '.round($minutebalance),
                    'year' => $row->year,
                    'datecreated' => $row->datecreated,
                    'createdby' => $row->createdby
                );
            }
        }

        echo json_encode($data);
    }
    function getpendinguniontrn(){
        $data = array();
        $empid = $this->input->post('empid');
        $year = $this->input->post('year');

        $sql = $this->db->select("sysid , fromdate , todate , fromtime , totime , totalinminutes")
            ->from("union_leave_transaction")
            ->where(array("status" => 307 , "empid" => $empid , "year" => $year))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $totalspenthours = $row->totalinminutes / 60;
                $dayspent = floor($totalspenthours / 8);
                $hourspent = ($totalspenthours % 8);
                $n = $totalspenthours;
                $whole = floor($n);      // 1
                $minutespent = ($n - $whole) * 60;

                $data['unionpendingtrndata'][] = array(
                    'num' => $num++,
                    'fromdate' => $row->fromdate,
                    'todate' => $row->todate,
                    'fromtime' => $row->fromtime,
                    'totime' => $row->totime,
                    'total' => $dayspent.' - '.$hourspent.' - '.round($minutespent),
                    'control' => '<button  id="deletependinguniontrn" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>'
                );
            }
        }

        echo json_encode($data);
    }
    function deletependinguniontrn(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid , "status" => 307));
        $sql = $this->db->update("union_leave_transaction" , $updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Union transaction has been removed.';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove union transaction.';
            $qry = false;
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        echo json_encode($data);
    }
    function saveunionpendingtrn(){
        $data = array();
        $empid = $this->input->post('empid');
        $year = $this->input->post('year');

        $this->db->trans_begin();

        $insarr = array(
            'status' => 301,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $main = $this->db->insert("union_leave_main" , $insarr);
        $lastid = $this->db->insert_id();


        $updatearr = array(
            'status' => 301,
            'groupid' => $lastid
        );
        $this->db->where(array("empid" => $empid , "year" => $year , "status " => 307));
        $sql = $this->db->update("union_leave_transaction" , $updatearr);
        if($this->db->trans_status() == true && $sql && $main){
            $this->db->trans_commit();
            $msg = 'Union transaction has been saved';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save union transacitons.';
            $qry = false;
            $func = 'error';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['empid'] = $empid;
        $data['year'] = $year;
        echo json_encode($data);
    }
    function getunioncredits(){
        $data= array();
        $sql = $this->db->select("uc.sysid , uc.credit , uc.year , uc.status , ptp.desc")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = uc.types")
            ->from("union_credits as uc")
            ->where(array("uc.status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                if($row->status == 1){
                    $status = '<span class="label label-sm label-success"> Active </span>';
                }else{
                    $status = '<span class="label label-sm label-danger"> Inactive </span>';
                }

                $data['unioncreditsdata'][] = array(
                    'num' => $num++,
                    'credit' => $row->credit,
                    'year' => $row->year,
                    'status' => $status,
                    'control' => ''
                );
            }
        }
        echo json_encode($data);
    }
    /* function test(){
         $date = '04/16/2019';
 // parse about any English textual datetime description into a Unix timestamp
         $ts = strtotime($date);
 // calculate the number of days since Monday
         $dow = date('w', $ts);
         $offset = $dow - 1;
         if ($offset < 0) {
             $offset = 6;
         }
 // calculate timestamp for the Monday
         $ts = $ts - $offset*86400;
 // loop from Monday till Sunday
         for ($i = 0; $i < 7; $i++, $ts += 86400){
             print date("m/d/Y l", $ts) . "<br>";
         }
     } */
    function getsupexec(){
        $data = array();
        $empid = $this->input->post('empid');

        $sql = $this->db->select("pec.ccid")->from("prime_employee_costcenter as pec")
            ->where(array("pec.status" => 1 , "pec.type" => 1 , "pec.empid" => $empid))
            ->get()->row();
        if($sql){
            $ccid = $sql->ccid;

            $gethead = $this->db->select("empid")->from("prime_costcenter_head")
                ->where(array("status" => 1 , "ccid" => $ccid))->get()->row();
            if($gethead){
                if($gethead->empid != $empid){
                    $data['head'] = $gethead->empid;
                }
            }

            $getexecutive = $this->db->select("pcgh.empid")->from("prime_costcenter_group_matrix as pcgm")
                ->join("prime_costcenter_group_head as pcgh" , "pcgm.groupid = pcgh.groupid")
                ->where(array("pcgm.ccid" => $ccid))
                ->get()->row();
            if($getexecutive){
                $data['executive'] = $getexecutive->empid;
            }
        }
        echo json_encode($data);
    }
    function submitcostheadexec(){
        $data = array();
        $heademp = $this->input->post('heademp');
        $execemp = $this->input->post('execemp');
        $ccid = $this->input->post('ccid');

        $this->db->trans_begin();
        $updatearr = array(
            'empid' => $heademp
        );
        $this->db->where(array("ccid" => $ccid  , "status" => 1 , "type" => 1));
        $headupdate = $this->db->update("prime_costcenter_head" , $updatearr);
        if($this->db->affected_rows() == 0){
            $headarr = array(
                'ccid' => $ccid,
                'empid' => $heademp,
                'type' => 1,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );
            $this->db->insert("prime_costcenter_head" , $headarr);
            $data['insert_head_error'] =  $this->db->_error_message();

        }

        /*   $getgroupid = $this->db->select("pcg.sysid")->from("prime_costcenter_group as pcg")
               ->join("prime_costcenter_group_head as pcgh" , "pcgh.groupid = pcg.sysid")
               ->where(array("pcgh.empid" => $execemp , "pcgh.status" => 1))
               ->get()->row();
           $groupid = ($getgroupid) ? $getgroupid->sysid : 0;

            $updategrouparr = array(
                'groupid' => $groupid
            );
            $this->db->where(array("ccid" => $ccid));
            $this->db->update("prime_costcenter_group_matrix" , $updategrouparr);
            */
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Data has been saved';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save data.';
            $qry = false;
            $func = 'error';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;

        echo json_encode($data);
    }
    function getheads(){
        $data = array();

        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname")
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid")
            ->where(array("pem.status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }


        echo json_encode($data);
    }
    function getexecutives(){
        $data = array();
        $term = $this->input->post('term');
        $sql = $this->db->select("pcgh.empid , p.lastname , p.firstname")
            ->from("prime_costcenter_group_head as pcgh")
            ->join("prime_employee_main as pem" , "pem.sysid = pcgh.empid")
            ->join("person as p" , "p.sysid = pem.personid", "left")
            ->where(array("pcgh.status" => 1))
            ->group_by("pcgh.empid , p.lastname , p.firstname")
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
    function getonleaveemp(){
        $data = array();
        $date = $this->input->post('date');

        $sql = $this->db->query("SELECT p.lastname , p.firstname , telr.from , telr.to , telr.fromtime,
       telr.totime , telr.leavedate , ptp.desc  , telr.datecreated , telr.type
FROM trn_employee_leave_requests as telr
JOIN trn_employee_leave_requests_approval as telra ON telra.sysid = telr.groupid
JOIN prime_employee_main as pem ON pem.sysid = telr.empid
JOIN person as p ON p.sysid = pem.personid
JOIN prime_types_parameter as ptp ON ptp.sysid = telr.leavetype
WHERE telr.status = 301 AND telr.leavedate = '".$date."' ");

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                if($row->type == 1){
                    $stat = 'Regular';
                }else if($row->type == 2){
                    $stat = 'Locator';
                }

                $data['dataemp'][] = array(
                    'num' => $num++,
                    'lastname' => $row->lastname,
                    'firstname' => $row->firstname,
                    'fromdate' => $row->from,
                    'todate' => $row->to,
                    'fromtime' => date("g:i A", strtotime($row->fromtime)),
                    'totime' => date("g:i A", strtotime($row->totime)),
                    'leavedate' => $row->leavedate,
                    'leavedesc' => $row->desc,
                    'type' => $stat,
                    'datecreated' => $row->datecreated
                );
            }
        }
        echo json_encode($data);
    }
    function getattendancedetails(){
        $data = array();
        $empid = $this->input->post('id');
        $date = $this->input->post('inputs');
        $html = '';
        $sql = $this->db->select("peat.bioid")->from("prime_employee_attendance_timelogs as peat")
            ->join("prime_employee_bioid as peb" , "peb.bioid = peat.bioid")
            ->where(array("peb.empid" => $empid , "peat.logdate" => $date))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                //$html .= '<h1>'.$row->bioid.'</h1>';
            }
        }

        $data['html'] = $html;
        echo json_encode($data);
    }
    function deletesbtsemp(){
        $data = array();

        $this->db->trans_begin();
        $dataid = $this->input->post('dataid');
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("empid" => $dataid));
        $sql = $this->db->update("trn_emp_schedule_operation" , $updatearr);

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Employee has been removed';
            $qry = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove employee.';
            $qry = false;
            $func = 'error';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;

        echo json_encode($data);
    }
    function getuniontrn(){
        $data = array();
        $year = $this->input->post('year');

        if($year > 0){
            $this->db->where(array("ult.year" => $year));
        }
        $sql = $this->db->select("p.lastname , p.firstname,ult.fromdate,ult.todate ,
        ult.fromtime , ult.totime , ult.totalinminutes , ult.leavedate , ult.year,
        ult.datecreated , ult.createdby")
            ->from("union_leave_transaction as ult")
            ->join("prime_employee_main as pem" , "pem.sysid = ult.empid")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->where(array("ult.status" => 301))
            ->get();
        $data['qry_err'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['uniondatatrn'][] = array(
                    "emp" => $row->lastname.', '.$row->firstname,
                    "fromdate" => $row->fromdate,
                    "todate" => $row->todate,
                    "fromtime" => $row->fromtime,
                    "totime" => $row->totime,
                    "total" => $row->totalinminutes,
                    "leavedate" => $row->leavedate,
                    "year" =>  $row->year,
                    "datecreated" => $row->datecreated,
                    "createdby" => $row->createdby
                );
            }
        }
        echo json_encode($data);
    }
    function select2costgroup(){
        $data = array();

        $sql = $this->db->select("sysid,names,desc")->from("prime_types_parameter")
            ->where(array("codes" => 'COSTGROUP' , "status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names.' - '.$row->desc
                );
            }
        }

        echo json_encode($data);
    }
    function getworkshifts(){
        $data = array();

        $sql = $this->db->select("sysid , codes , desc")
            ->from("prime_employee_main_workshift")
            ->where(array("status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc.' - '.$row->codes
                );
            }
        }

        echo json_encode($data);
    }
    function getemployeepayclass(){
        $data = array();
        $sql = $this->db->select("pem.sysid , p.lastname , firstname,ptp.desc  ,pemp.dateupdated, pec.ccid")
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid" , "left")
            ->join("prime_employee_costcenter as pec" , "pec.empid = pem.sysid" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemp.payclass_id" , "left")
            ->where(array("pem.status" => 1,"pemp.status" => 1 , "pec.type" => 1 , "pec.status" => 1))
            ->group_by("pem.sysid , p.lastname , firstname,pemp.payclass_id ,pemp.dateupdated, pec.ccid")
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['emppayclassdata'][] = array(
                    'num' => $num++,
                    'lastname' => ucwords($row->lastname),
                    'firstname' => ucwords($row->firstname),
                    'dept' => $row->ccid,
                    'payclass' => '<input type="text" id="payclass" value="'.$row->desc.'">',
                    'dateupdated' => $row->dateupdated
                );
            }
        }
        echo json_encode($data);
    }
    function getevaluationselections(){
        $data = array();

        $evaltype = $this->input->post('evaltype');
        $empid = $this->input->post('empid');
        $ratedby = $this->input->post('ratedby');
        $evalinfo = get_employee_evaluation_data($empid,$evaltype,date('Y') , $ratedby,false);


        $getshaded = '';
        $sql = $this->db->select("")->from("evaluation_selections")
            ->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                switch ($evaltype) {
                    case 1:
                        $colors =  "danger";
                        break;
                    case 2:
                        $colors =  "blue";
                        break;
                    case 3:
                        $colors =  "green";
                        break;
                    case 5:
                        $colors = "warning";
                        break;
                    case 6:
                        $colors = "info";
                        break;
                    default:
                        $colors =  "";
                }
                if($evaltype == 1){
                    $getshaded = $this->db->select("rate")->from("evaluation_employee_rates")
                        ->where(array("empid" => user_id() , "questid" => $row->sysid ,
                            "createdby" => user_id() , "year" => date('Y')
                        , "status" => 1 , "evaltype" => $evaltype ))->get()->row();
                    if($getshaded == false){
                        $colors =  "";
                    }
                }
                $url =  base_url().'hris/submitevaluationjustification';
                $pop_over_form = '
                    <form target=\'row_id_'.$num.'\' id=\'frm_justification_entry\' style=\'\' class=\'form-horizontal\' action=\''.$url.'\' method=\'post\'>
                        <input value=\''.$empid.'\' type=\'hidden\' name=\'empid\' class=\'form-control input-md\' id=\'\' />
                        <input value=\''.$row->sysid.'\' type=\'hidden\' name=\'questid\' class=\'form-control input-md\' id=\'\' />
                        <input value=\''.$evaltype.'\' type=\'hidden\' name=\'eval\' class=\'form-control input-md\' id=\'\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px;\'>
                                <textarea style=\'resize: none !important;\' rows=\'15\' cols=\'20\' placeholder=\'Remarks\'  type=\'text\' name=\'remarks\' class=\'form-control input-md\' id=\'remarkstxt\'   ></textarea>
                            </div>
                        </div>
                        <div class=\'form-actions bottom\' style=\'margin-top: 20px\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button type=\'submit\' class=\'btn btn-primary\'>Save</button>
                        </div>
                    </form>
                 ';
                if($evalinfo->qry == true){
                    $data['eval'] = true;
                    $justification = get_employee_evaluation_data($empid,$evaltype,date('Y') , $ratedby,$row->sysid)->justification;
                }else{
                    $data['eval'] = false;
                    $justification = '<a id="justificationpopover"  style="max-width: 100%;" data-id="'.$row->sysid.'" class="btn btn-default btn-sm popovers" href="#" data-title="Justification" data-trigger="click" data-placement="left" data-content="'.$pop_over_form.'"><i class="fa fa-comment"></i> </a>';
                }
                $data['selectionsdata'][] = array(
                    'num' => $num++.'<span id="row_id_'.$row->sysid.'"></span><input type="hidden" value="'.$row->sysid.'" class="questid"/>',
                    'persontraits' =>$row->personal_traits,
                    'desc' => $row->personal_traits_desc,
                    'unsatisfactory' => $row->unsatisfactory,
                    'somedeficiencies' => $row->somedefiencies,
                    'satisfactory' => $row->satisfactory,
                    'exceptional' => $row->exceptional,
                    'clearlyoutstanding' => $row->clearlyoutstanding,
                    'comments' => $justification,
                    "voterate" => ($getshaded) ? $getshaded->rate : '',
                    "colors" => $colors
                );
            }
        }
        $data['sClass2'] = ($evalinfo && $evalinfo->qry == true) ? '' : 'rate ';
        echo json_encode($data);
    }

    /**
     *
     */
    function castvote(){
        $data = array();

        $empid = $this->input->post('empid');
        $evaluationtype = $this->input->post('evaluationtype');
        $questionaireid = $this->input->post('questionaireid');
        $voterate = $this->input->post('voterate');

        $this->db->trans_begin();

        $updatestat = array(
            'status' => 0,
            'updatedby' => user_id()
        );
        $this->db->where(array("empid" => $empid, "questid" => $questionaireid,
            "evaltype" => $evaluationtype,"year" => date('Y') , "status" => 1,
            "createdby" => user_id()));
        $this->db->update("evaluation_employee_rates" , $updatestat);

        $insarr=  array(
            'groupid' => 0,
            'empid' => $empid,
            'questid' => $questionaireid,
            'rate' => $voterate,
            'evaltype' => $evaluationtype,
            'year' => date('Y'),
            'status' => 1,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("evaluation_employee_rates" , $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $data['msg'] = "Rate saved.";
            $data['func'] = "success";
            $data['qry'] = true;

            switch ($evaluationtype) {
                case 1:
                    $class =  "danger";
                    break;
                case 2:
                    $class =  "blue";
                    break;
                case 3:
                    $class =  "green";
                    break;
                case 4:
                    $class = "warning";
                    break;
                case 5:
                    $class = "info";
                    break;
                default:
                    $class =  "";
            }

            $data['class'] = $class;
            $data['rate'] = $voterate;
        }else{
            $this->db->trans_rollback();
            $data['msg'] = "Failed to save rate.";
            $data['func'] = "error";
            $data['qry'] = false;
        }

        echo json_encode($data);
    }
    function fetchempscore(){
        $data = array();
        $html = '';
        $totalofchoices = 4;
        $totalofselections =0;
        $score = 0;


        $empid = $this->input->post('empid');
        $eval = $this->input->post('eval');

        $gettotalofquestions  = $this->db->query("SELECT count(sysid) AS totalquestions FROM evaluation_selections")
            ->row();

        if($gettotalofquestions){
            $totalofselections = $gettotalofquestions->totalquestions;
        }

        $gettotalratescore = $this->db->select("SUM(rate) as totalscore")->from("evaluation_employee_rates")
            ->where(array("status" => 1,"empid" => $empid,"evaltype" => $eval,
                "year" => date('Y'),"createdby" => user_id()))
            ->get()->row();

        if($gettotalratescore){
            $score = $gettotalratescore->totalscore;
        }

        $total_items = $totalofchoices * $totalofselections;

        $textsec = array(
            '0' => 'UNSATISFACTORY',
            '1' => 'SOME DEFICIENCES EVIDENT',
            '2' => 'SATISFACTORY',
            '3' => 'EXCEPTIONAL',
            '4' => 'CLEARLY OUTSTANDING'
        );
        $index = 0;
        for($i = 0; $i<=$total_items; $i++) {
            $text = '';
            $class = '';
            $background = '';
            $score_seq_curr = '';
            $score_seq_curr_title = '';
            $labelscore = '';


            if($score == $i) {
                $class = 'textdesc score-curr';
                $text = $score;
                $score_seq_curr = ' your-score';
                $score_seq_curr_title = ' title="Your score" data-placement="top"';
            }

            if($i % 5 == 0)
            {
                $height = '200';
            }

            if($i % $totalofselections == 0)
            {
                $labelscore = $textsec[$index];
                $height = '300';
                $background = 'background: #000; height: 70px;';
                $text = $i;
                $index++;

            }

            $html .=  '<span class="labelscore" style="color: black;margin-top: 100px!important;position: absolute;">'.$labelscore.'</span>';
            $html .=  '<div class="score-bar '. $class. '" style="'.$background.'" >';

            $html .=  '<span class="score-seq '.$score_seq_curr.'" '.$score_seq_curr_title.'>'.$text.'</span>';
            $html .=  '</div>';
        }


        $data['score'] = $score;
        $data['html'] = $html;

        echo json_encode($data);
    }
    function ratevote($totalvote){
        $rate = '';
        if($totalvote == 0){
            $rate = "N/A";
        } else if ($totalvote >= 1 && $totalvote <= 14) {
            $rate = "UNSATISFACTORY";
        } else if ($totalvote >= 15 && $totalvote <= 29) {
            $rate = "SOME DEFICIENCIES EVIDENT";
        } else if ($totalvote >= 30 && $totalvote <= 44) {
            $rate = "SATISFACTORY";
        } else if ($totalvote >= 45 && $totalvote <= 59) {
            $rate = "EXCEPTIONAL";
        } else {
            $rate = "CLEARLY OUTSTANDING";
        }
        return $rate;
    }
    function submitevaluationjustification(){
        $data = array();

        $empid = $this->input->post('empid');
        $questid = $this->input->post('questid');
        $eval = $this->input->post('eval');
        $remarks = $this->input->post('remarks');

        $this->db->trans_begin();
        $insarr = array(
            'empid' => $empid,
            'questid' => $questid,
            'justification' => $remarks,
            'evaltype' => $eval,
            'year' => date('Y'),
            'status' => 1,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("evaluation_justifications" , $insarr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $data['msg'] = "Justification has been saved.";
            $data['func'] = "success";
            $data['qry'] = true;
        }else{
            $this->db->trans_rollback();
            $data['msg'] = "Failed to save justification";
            $data['func'] = "error";
            $data['qry'] = false;
        }
        echo json_encode($data);
    }
    function submitevaluation(){
        $data = array();
        $comps = $this->input->post('comps');
        $compw = $this->input->post('compw');
        $approve = $this->input->post('approvechoices');
        $remarks = $this->input->post('remarks');
        $empid = $this->input->post('empid');
        $fromcov = $this->input->post('fromcov');
        $tocov = $this->input->post('tocov');
        $evaltype = $this->input->post('evaltype');



        $this->db->trans_begin();

        $groupidarr = array(
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $getgroupid = $this->db->insert("evaluation_group" , $groupidarr);
        $groupid = $this->db->insert_id();

        $index = 0;
        $getotherinfo = $this->db->select("sysid")->from("prime_types_parameter")
            ->where(array("status" => 1 , "codes" => 'EVAL'))
            ->order_by("sysid","asc")->get();
        if($getotherinfo->num_rows() > 0){
            foreach ($getotherinfo->result() as $row){
                $otherinfovalfix = array(
                    'empid' => $empid,
                    'groupid' => $groupid,
                    'types' => $row->sysid,
                    'remarks' => $remarks[$index],
                    'status' => 1,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );

                $this->db->insert("evaluation_other_info" , $otherinfovalfix);
                $data['loopinserror'] = $this->db->_error_message();
                $index++;
            }
        }
        $evalratearr = array(
            'groupid' => $groupid
        );
        $this->db->where(array("empid" => $empid, "evaltype" => $evaltype,"year" => date('Y') ,"createdby" => user_id()));
        $updateemprated = $this->db->update("evaluation_employee_rates" , $evalratearr);

        $evaljustificationarr = array(
            'groupid' => $groupid
        );
        $this->db->where(array("empid" => $empid,"evaltype" => $evaltype,"year" => date('Y'),"createdby" => user_id()));
        $updatejustification = $this->db->update("evaluation_justifications" , $evaljustificationarr);


        $insarr = array(
            'empid' => $empid,
            'evaltype' => $evaltype,
            'groupid' => $groupid,
            'covfrom' => $fromcov,
            'covto' => $tocov,
            'strength' => $comps,
            'weakness' => $compw,
            'evaldiscussed' => $approve,
            'year' => date('Y'),
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("evaluation_main" , $insarr);
        $data['error_mes'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql && $getgroupid && $updateemprated && $updatejustification){
            $this->db->trans_commit();
            $data['msg'] = "Evaluation has been saved.";
            $data['func'] = "success";
            $data['qry'] = true;
        }else{
            $this->db->trans_rollback();
            $data['msg'] = "Failed to save evaluation.";
            $data['func'] = "error";
            $data['qry'] = false;
        }
        echo json_encode($data);
    }

    function getpayclasslist(){
        echo getpayclass();
    }
    function getpositionslist(){
        echo getpositions();
    }
    function getcostgrouplist(){
        echo getcostgroup();
    }
    function getsalaryincreasetype(){
        echo getsalinctype();
    }
    function getcontacttypelist(){
        echo getcontacttypes();
    }
    function getlogtypeslist(){
        echo getlogtypes();
    }
    function gettsteamlist(){
        echo gettsteam();
    }
    function getleavecredittypes(){
        echo getleavecreditstypes();
    }
    function getjobcategorylist(){
        echo getjobcatlist();
    }
    function getpayrollpaytypelist(){
        echo getpayrollpaytype();
    }
    function getexecutiveslist(){
        $data = array();

        $sql = $this->db->select("pem.sysid,p.lastname,p.firstname,ptp.desc as position")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid")
            ->join("prime_employee_main_payclass as pemp","pemp.emp_id = pem.sysid && pemp.status = 1")
            ->join("prime_employee_main_positions as emppos" , "emppos.emp_id = pem.sysid")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = emppos.position_id && emppos.status = 1")
            ->where(array("pem.status" => 1 , "pemp.payclass_id" => 131))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $getcoexec = $this->db->select("pem.sysid,p.lastname,p.firstname")
                    ->from("prime_employee_main as pem")
                    ->join("person as p", "p.sysid = pem.personid")
                    ->join("prime_employee_main_payclass as pemp","pemp.emp_id = pem.sysid  && pemp.status = 1")
                    ->where(array("pem.status" => 1,"pemp.payclass_id" => 131 , "pem.sysid !=" => $row->sysid ))
                    ->get();
                if($getcoexec->num_rows() > 0){
                    foreach ($getcoexec->result() as $row1){
                        $data['executives'][] = array(
                            'num' => $num++,
                            'name' => $row->lastname.', '.$row->firstname,
                            'position' => $row->position,
                            'coexec' => $row1->lastname.', '.$row1->firstname . '<button class="btn btn-primary inline">View</button>',
                            'self' => '<button class="btn btn-primary inline">View</button>',
                            'comm' => '<button class="btn btn-primary inline">View</button>',
                            'pceo' => '<button class="btn btn-primary inline">Evaluate</button>',
                        );
                    }
                }


            }
        }

        echo json_encode($data);
    }
    function getselect2exec(){
        $data = array();

        $sql = $this->db->select("pem.sysid,p.lastname,p.firstname,ptp.desc as position")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid")
            ->join("prime_employee_main_payclass as pemp","pemp.emp_id = pem.sysid && pemp.status = 1")
            ->join("prime_employee_main_positions as emppos" , "emppos.emp_id = pem.sysid")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = emppos.position_id && emppos.status = 1")
            ->where(array("pem.status" => 1 , "pemp.payclass_id" => 131))
            ->get();
        if($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }
        echo json_encode($data);
    }
    function getselect2execj(){
        $data = array();

        $sql = $this->db->select("pem.sysid,p.lastname,p.firstname")
            ->from("prime_costcenter_group as pcg")
            ->join("prime_costcenter_group_head as pcgh" , "pcgh.groupid = pcg.sysid")
            ->join("prime_employee_main as pem" , "pem.sysid = pcgh.empid")
            ->join("person as p" , "p.sysid = pem.personid")
            ->where(array("pcg.level" => 4 , "pcg.status" => 1))
            ->get();
        if($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }
        echo json_encode($data);
    }
    function sumitemployeefilter(){
        $data = array();
        $select2execforemp = $this->input->post('select2execforemp');
        $select2execjforemp = $this->input->post('select2execjforemp');
        $select2deptforemp = $this->input->post('select2deptforemp');


        $getexec = $this->db->select("pcgh.groupid , pcgh.empid")->from("prime_costcenter_group_head as pcgh")
            ->join("prime_costcenter_group as pcg","pcg.sysid = pcgh.groupid")
            ->where(array("pcgh.empid" =>$select2execforemp , "pcg.level" => 2 ))
            ->get()->row();
        $execid = ($getexec) ? $getexec->empid : '';
        $execgid = ($getexec) ? $getexec->groupid : '';

        $getexecj = $this->db->select("pcgh.groupid , pcgh.empid")->from("prime_costcenter_group_head as pcgh")
            ->join("prime_costcenter_group as pcg","pcg.sysid = pcgh.groupid")
            ->where(array("pcgh.empid" =>$select2execjforemp , "pcg.level" => 4 ))
            ->get()->row();
        $execjid = ($getexecj) ? $getexecj->empid : '';
        $execjgid = ($getexecj) ? $getexecj->groupid : '';

        $data['execgid'] = $execgid;
        $data['execjgid'] = $execjgid;
        $data['select2deptforemp'] = $select2deptforemp;


        $getexeccostcenters = $this->db->query("SELECT groupid,ccid FROM prime_costcenter_group_matrix
        WHERE ccid = $select2deptforemp AND (groupid = $execgid) AND 
        status = 1");
        if($getexeccostcenters->num_rows() > 0){
            foreach ($getexeccostcenters->result() as $row){
                $data['data'][] = array(
                    'groupid' => $row->groupid,
                    'ccid' => $row->ccid
                );
            }
        }

        $getexecjcostcenters = $this->db->query("SELECT groupid,ccid FROM prime_costcenter_group_matrix
        WHERE ccid = $select2deptforemp AND (groupid = $execjgid) AND 
        status = 1");
        if($getexecjcostcenters->num_rows() > 0){
            foreach ($getexecjcostcenters->result() as $row){
                $data['data'][] = array(
                    'groupid' => $row->groupid,
                    'ccid' => $row->ccid
                );
            }
        }


        echo json_encode($data);
    }
    function get_cost_centers(){
        echo getcostcenters();
    }

    function dtleaveforapprovalrequest() {
        echo $this->model_hris->dt_leave_for_approval_request();
    }

    function approveleaverequest(){
        echo $this->model_hris->approve_leave_request();
    }

    function leaveapprovaldetails() {
        echo $this->model_hris->leave_approval_details();
    }

    function generatetardiness($id=false,$month=false,$year=false,$print = false) {
        echo $this->model_hris->generate_tardiness_data($id,$month,$year,$print);
        /*echo "<pre>";
        print_r ($this->model_hris->generate_tardiness_data($id,$month,$year));
        echo "</pre>";*/

    }

    function generateflexibletardiness($id=false,$month=false,$year=false,$print = false,$printype = false) {
        if (!$printype) {
            echo $this->model_hris->generate_flexible_tardiness_data($id, $month, $year, $print);
        } else {
            if ($printype == 'pre') {
                echo "<pre>";
                print_r(json_decode($this->model_hris->generate_flexible_tardiness_data($id, $month, $year,$print)));
                echo "</pre>";
            }

            if ($printype == 'html') {
                echo json_decode($this->model_hris->generate_flexible_tardiness_data($id, $month, $year,$print))->html;
            }
        }
    }

}