<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');
session_start();

class Calendar extends CI_Controller {

   private $user_login;

   function __construct() {
      parent::__construct();
      $this->load->model('model_admin');
      $this->load->model('model_calendar');
      $this->user_login = $this->session->userdata('logged_in');

       // LOCK SCREEN
       if(check_user_lock()) {
           redirect(base_url(), 'refresh');
       }

   }

   function index($id = false) {
      if ( !empty($this->user_login) && in_array(1, get_users_roles_matrix_id_arr()) ) {
          init_header(false);
          $this->load->view('admin/pages/calendar/main');
          init_footer(false, '');
      } else {
         redirect(base_url() . 'admin/dashboard');
      }
   }

   function getcalendar() {

   }

}
