<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/9/2018
 * Time: 11:00 AM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    session_start(); // STARTING SESSION DATA

class Cwdo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_billing');
        $this->load->model('model_query');
        $this->load->model('model_cwdo');
        $this->load->model('model_ar');
    }

    function newticket() {
        echo $this->model_cwdo->save_new_ticket();
    }

    function acctsearch() {
        echo $this->model_cwdo->acct_search();
    }

    function getacctinfo() {
        echo $this->model_cwdo->get_acct_info();
    }

    function gettickethistory() {
        echo $this->model_cwdo->get_ticket_history();
    }

    function getticketlist() {
        echo $this->model_cwdo->get_cwd_ticket_list();
    }

    function gettcutility() {
        echo $this->model_cwdo->get_utility_ticket_list();
    }

    function getticketdetailsbasic() {
        echo $this->model_cwdo->get_ticket_details_basic();
    }


    function savetagging() {
        echo $this->model_cwdo->save_tagging();
    }

    function select2referrals() {
        echo $this->model_cwdo->get_select2_referrals();
    }

    function addreferralsrow() {
        echo $this->model_cwdo->add_referrals_row();
    }
    function addremarksrow() {
        echo $this->model_cwdo->add_remarks_row();
    }
    function getticketdetails() {
        echo $this->model_cwdo->get_ticket_details();
    }
    function getviewar() {
        echo $this->model_cwdo->get_ar_view();
    }
}