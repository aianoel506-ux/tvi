<?php


class Analysis extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('model_analysis');
    }

    function ecaleslog() {
        echo $this->model_analysis->save_ecales_log();
    }

    function processecales() {
        echo $this->model_analysis->process_ecales();
    }

    function addecalesitem() {
        echo $this->model_analysis->add_ecales_item();
    }

    function addecalesservice() {
        echo $this->model_analysis->add_ecales_service();
    }

    function getcustomerecalestable() {
        echo $this->model_analysis->get_customer_ecales_table();
    }

    function delecalesitem() {
        echo $this->model_analysis->del_ecales_item();
    }

    function updateecalesitems() {
        echo $this->model_analysis->update_ecales_items();
    }

    function updateecalesservice() {
        echo $this->model_analysis->update_ecales_service();
    }

    function changeecalespayable() {
        echo $this->model_analysis->change_ecales_payable();
    }

    function getcustomerecalesservices() {
        echo $this->model_analysis->get_customer_ecales_services();
    }

    function delecalesservice() {
        echo $this->model_analysis->del_ecales_service();
    }

    function getecalessummary() {
        echo $this->model_analysis->get_ecales_summary();
    }

    function getecalesinfo() {
        echo $this->model_analysis->get_ecales_info();
    }

    function revokeecales() {
        echo $this->model_analysis->revoke_ecales();
    }

    function ecalesrevokedlogs() {
        echo $this->model_analysis->ecales_revoked_logs();
    }

    function getecalessubdetails() {
        echo $this->model_analysis->get_ecales_subdetails();
    }

    function saveecalestemplate() {
        echo $this->model_analysis->save_ecales_template();
    }

    function dtecalestemplates() {
        echo $this->model_analysis->dt_ecales_templates();
    }

    function getecalestemplatedetails() {
        echo $this->model_analysis->get_ecales_template_details();
    }

    function applytemplate() {
        echo $this->model_analysis->apply_template();
    }
}