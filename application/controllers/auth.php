<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property Model_auth $model_auth
 */
class Auth extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('model_auth', '', TRUE);

        // CHECK IF THIS PAGE IS NOT TRIGGER WITH AJAX THEN REDIRECT BACK TO HOME // 
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }
    }

    function index() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
        
        if ($this->form_validation->run() == FALSE) {
            // For AJAX requests, return JSON response
            if ($this->input->is_ajax_request()) {
                $error_message = $this->session->flashdata('login_error');
                if (empty($error_message)) {
                    $error_message = return_message_ajax('warning', 'fa-warning', 'Invalid username or password!');
                }
                $data = array(
                    'message' => $error_message,
                    'num' => 0
                );
                echo json_encode($data);
                return;
            }
            return false;
        } else {
            // For AJAX requests, return success JSON response
            if ($this->input->is_ajax_request()) {
                // Get the login data from model
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $data = $this->model_auth->login($username, $password);
                echo json_encode($data);
                return;
            }
            return true;
        }
    }

    function check_database() {
        $data = array();
        $field = false;
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (!empty($username) && !empty($password)) {
            $field = true;
        }

        //query the database
        if ($field == true) {
            $data = $this->model_auth->login($username, $password);
        } else {
            $data['message'] = return_message_ajax('warning', 'fa-warning', 'Username / Password is empty!');
            $data['num'] = 0;
        }
        
        // Return true/false for form validation, but store data for AJAX response
        if ($data['num'] > 0) {
            return true;
        } else {
            $this->session->set_flashdata('login_error', $data['message']);
            return false;
        }
    }

    function logout() {
        echo $this->model_auth->logout();
    }

    function lock() {
        echo json_encode($this->model_auth->lock_user_log());
    }

    function unlock() {
        echo $this->model_auth->unlock_user_log();
    }

    function destroysession() {

        $this->session->unset_userdata('logged_in');
        session_destroy();
    }

}
