<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Notification extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_notification');
        if (!$this->input->is_ajax_request()) {
			// COMMENT IF DEVELOPMENT MODE
			// FOR ERROR CHECKKING //
            // redirect(base_url());
        }
    }

    public function index() {
        
    }
    
	function menutrn() {
		echo json_encode($this->model_notification->get_menu_usertrn());
	}	

    function all() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            init_header($data);
            page_construction_full();
            init_footer($data, false);
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

}
