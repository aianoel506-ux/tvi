<?php


class Itd extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_itd');
    }

    function gettechloglist() {
        echo $this->model_itd->get_itd_techlog_list();
    }

    function getcclist() {
        echo $this->model_itd->get_cc_table();
    }

    function getccemployees() {
        echo $this->model_itd->get_cc_employee_table();
    }

    function deleteccemployee() {
        echo $this->model_itd->delete_cc_employee();
    }

    function assignccemployee() {
        echo $this->model_itd->assign_cc_employee();
    }

    function getlogdetails() {
        echo $this->model_itd->get_log_details();
    }
}