<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 8/30/2018
 * Time: 11:05 AM
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start(); // STARTING SESSION DATA


Class Ar extends CI_Controller  {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_systems');
        $this->load->model('model_admin');
        $this->load->model('model_billing');
        $this->load->model('model_ar');

        if(!user_id()) {
            return false;
        }
    }

    function getbilling() {
        echo $this->model_ar->get_billing();
    }

    function searchaccount() {
        echo $this->model_ar->search_account();
    }

    function getcustomerpayfromold() {
        echo $this->model_ar->get_customer_payment_fromold();
    }

    function getcustomerpayfromoldmonthly() {
        echo $this->model_ar->get_customer_payment_fromold_monthly();
    }

    function getarlist() {
        echo $this->model_ar->get_ar_list();
    }

    function getpayments() {
        echo $this->model_ar->get_payments();
    }

    function getcmdb() {
        echo $this->model_ar->get_credit_memo_db();
    }
}