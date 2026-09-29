<?php

//TODO make comments work with proper user_id of the commentor and comment refresh using ajax.
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bos extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_bos');
    }

    function select2ccid() {
        echo $this->model_bos->select2_ccid();
    }
    function select2budgettype() {
        echo $this->model_bos->select2_budget_types();
    }
    function getbudget() {
        echo $this->model_bos->get_budget();
    }
    function submitbudget() {
        echo $this->model_bos->submit_budget();
    }
    function testing() {
        $test = $this->model_bos->get_budget_approved_amount(1);
        print_r($test);
    }
    function subdetails() {
        echo $this->model_bos->get_subdetails();
    }
    function getbudgettypesgroup(){
        echo $this->model_bos->get_budget_types_group();
    }
    function submittransaction(){
        echo $this->model_bos->submit_transaction();
    }
    function getacctcodelist(){
        echo $this->model_bos->getacct_codelist();
    }
    function updateledgerrefid(){
        echo $this->model_bos->update_ledger_refid();
    }
    function getitems(){
        echo $this->model_bos->get_all_items();
    }
    function submititemdetails(){
        echo $this->model_bos->submititemdetails();
    }
    function getbositems(){
        echo $this->model_bos->getbositems();
    }
    function deleteitem(){
        echo $this->model_bos->deleteitem();
    }
    function removebudget(){
        echo $this->model_bos->removebudget();
    }
    function subdetailsapproval(){
        echo $this->model_bos->subdetailsapproval();
    }
    function getapprovalitems(){
        echo $this->model_bos->getapprovalitems();
    }

}
