<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Request extends  CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('hris_helper');
        $this->load->model('model_request');
        $this->load->model('model_hris');
    }
    function getcheckcredit(){
        echo $this->model_request->get_check_credits();
    }
    function getleavetype(){
        echo $this->model_request->get_leave_type();
    }
    function processleaveform(){
        echo $this->model_request->process_leave_form();
    }
    function fetchleaverequested(){
        echo $this->model_request->fetch_leave_requested();
    }
    function submiteleaverequest(){
        echo $this->model_request->submit_leave_request();
    }
    function fetchpendingleaverequested(){
        echo $this->model_request->fetch_pending_leave_requested();
    }

    function deletependingrequest(){
        echo $this->model_request->delete_pending_leave_request();
    }

    function approvedrequest(){
        echo $this->model_request->approve_leave_request();
    }

    function disapprovedrequest(){
        echo $this->model_request->disapprove_leave_request();
    }

    function draftleaverequest(){
        echo $this->model_request->draft_leave_request();
    }

    function addemployeeleavedraft() {
        echo $this->model_request->add_employee_leave_draft();
    }

    function addleaveitem() {
        echo $this->model_request->add_leave_items();
    }

    function tblleaveitem() {
        echo $this->model_request->tbl_leave_items();
    }

    function leaveapprovalonline($approvalid,$groupid,$empid) {
        $data = array();
        $data['pagetittle'] = 'Leave Approval';
        init_header_nonav($data);
        // $approva_arr = $this->model_hris->approve_leave_request($approvalid, $groupid, $empid);
        // $approva_arr_dec = json_decode($approva_arr);

        $leave_details_arr = json_decode($this->model_hris->leave_approval_details($groupid));
        $data['details'] = $leave_details_arr;
        $data['approvalid'] = $approvalid;
        $data['empid'] = $empid;
        $data['groupid'] = $groupid;
        $this->load->view('frontend/pages/employee/leaveapproval', $data);
        init_footer_nonav($data);
    }

    function approvelonlineleave() {
        echo $this->model_hris->approve_leave_request();
    }

}