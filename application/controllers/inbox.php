<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');
session_start();

class Inbox extends CI_Controller {

   private $user_login;

   function __construct() {
      parent::__construct();
      $this->load->model('model_admin');
      $this->user_login = $this->session->userdata('logged_in');
   }


   function index() {
      if (!empty($this->user_login)) {
         $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
         init_header($data);
         $this->load->view('admin/pages/user/newentry');
         init_footer($data, '');
      } else {
         redirect(base_url() . 'admin/dashboard');
      }
   }

}
