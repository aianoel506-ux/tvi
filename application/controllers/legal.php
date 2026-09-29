<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Legal extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_legal');
        $this->load->model('model_admin');
        $this->load->model('model_query');
    }

    public function apprehensionlist() {
		echo $this->model_legal->get_apprehension_list();
	}
    
    public function saveapprehension() {
        echo $this->model_legal->save_apprehension();
    }

    function apprehensionmatch() {
        echo $this->model_legal->get_apprehension_table();
    }

    function exemptapprehension() {
        echo $this->model_legal->save_exemption();
    }

    function getlegalirlist() {
        echo $this->model_legal->get_ticket_list();
    }


    function getlegalappdetails() {
        echo $this->model_legal->get_ticket_details_basic();
    }

    function tblledger() {
        echo $this->model_legal->get_tbl_ledger();
    }

    function deleteledgeritem() {
        echo $this->model_legal->delete_ledger_item();
    }


    function addratrans(){
        echo $this->model_legal->add_ra_trans();
    }

    function getflexipayment(){
        echo $this->model_legal->get_flexi_payment();
    }

    function addtrans(){
        echo $this->model_legal->addtrans();
    }

    function getpenaltypaymentstbl(){
        echo $this->model_legal->get_penalty_payments_tbl();
    }
    function getinspectors(){
        echo $this->model_legal->get_inspectors();
    }
    function getbanksid(){
        echo $this->model_legal->get_banksid();
    }
    function submitstaggered(){
        echo $this->model_legal->submit_staggered();
    }
    function getstaggeredtrans(){
        echo $this->model_legal->get_staggered_trans();
    }
    function deletestaggeredpayment(){
        echo $this->model_legal->delete_staggered_payment();
    }
    function getdtsync(){
        echo $this->model_legal->get_dt_sync();
    }
    function testing() {

    }
}