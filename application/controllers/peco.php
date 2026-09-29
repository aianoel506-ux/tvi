<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Peco extends CI_Controller {

    private $user_login;

    function __construct() {
        parent::__construct();
        $this->load->model('model_peco');
        $this->load->model('model_admin');
        $this->load->model('model_systems');
    }


    function index() {
        $data = array();
        if (user_id() > 0) {
            if((check_access() && check_access()->accesstype < 5) || user_id()==1){
                if (check_user_lock()) {
                    $data['pagetitle'] = 'Login';
                    $this->load->helper(array('form'));
                    $this->load->view('admin/common/head', $data);
                    $this->load->view('redirects/forms/view_lock');
                    $this->load->view('admin/common/scripts');
                    //$this->load->view('includes/scripts/lock');

                } else {
                    redirect(base_url() . 'admin/dashboard');
                }
            }else{
                redirect(base_url() . 'guest');
            }
        } else {
            if((check_access() && check_access()->accesstype < 5) || user_id()==1){
                $this->load->helper(array('form'));
                $data['pagetitle'] = 'Welcome | Login';
                $this->load->view('admin/common/head', $data);
                $this->load->view('redirects/forms/view_login');
                $this->load->view('admin/common/scripts');
            }else {
                redirect(base_url() . 'guest');
            }
        }
    }

    function registration() {
        $data = array();
        if (!user_id()) {
            if((check_access() && check_access()->accesstype < 5) || user_id() == 1){
                $this->load->helper(array('form'));
                $data['pagetitle'] = 'Registration';
                $this->load->view('admin/common/head', $data);
                $this->load->view('redirects/forms/view_registration');
                $this->load->view('admin/common/scripts');
            }else {
                redirect(base_url() . 'guest');
            }
        } else {
            redirect(base_url());
        }
    }

    function forgotpassword() {
        $data = array();
        if (!user_id()) {
            if((check_access() && check_access()->accesstype < 5) || user_id() == 1){
                $this->load->helper(array('form'));
                $data['pagetitle'] = 'Forgot Password';
                $this->load->view('admin/common/head', $data);
                $this->load->view('redirects/forms/view_forgotpassword');
                $this->load->view('admin/common/scripts');
            }else {
                redirect(base_url() . 'guest');
            }
        } else {
            redirect(base_url());
        }
    }

    function sendforgotpasswordlink() {
        echo $this->model_systems->send_forgot_password_link();
    }


    function gencaptcha() {
        echo $this->model_systems->generate_captcha();
    }

    function registeremployee() {
        echo $this->model_systems->register_employee();
    }


    function getaccountinfo() {
        echo $this->model_peco->get_account_info();
    }

    function customer($acctid = false) {
        $data = array();


        $info = get_active_account_info($acctid);


        /*
        $data['name'] = $info->name;
        $data['addr'] = $info->address;
        $data['servno'] = $info->servicenumber;
        $data['mtrno'] = $info->mtrno;
        $data['serial'] = $info->mtrserial;
        $data['infoarr'] = $info;
        */

        $data['acct_arr'] = $info;
        $data['pagetitle'] = $info->servicenumber;
        $data['acctid'] = $acctid;
        if($info) {
            init_header($data);
            $this->load->view('peco/customer', $data);
            init_footer($data);
        }else{
            page_construction();
        }
    }

    function getcustomerreadingdata () {
        echo $this->model_peco->get_customer_reading_data();
    }
    function getcustomerreadinginfo () {
        echo $this->model_peco->get_customer_reading_info();
    }


}
