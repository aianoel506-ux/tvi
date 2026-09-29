<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');
session_start();

class Profile extends CI_Controller {

   private $user_login;

   function __construct() {
      parent::__construct();
      $this->load->model('model_admin');
      $this->user_login = $this->session->userdata('logged_in');
   }

   function index($id = false) {
      if (!empty($this->user_login)) {
          if($id && in_array(1, get_users_roles_matrix_id_arr()) ) {
              $userid = $id;
          }else {
              $userid = user_id();
          }

          $qry_user_info = $this->db->select()
              ->from('prime_system_users')
              ->where('sysid', $userid)
              ->get()->row();

          // GET LAST PAGE VISIT
          $qry_user_logs = $this->db->select()->from('prime_system_users_logs')
              ->where(array('userid' => $userid, 'sessionsegment != ' => ''))
              ->order_by('sessiondatetime', 'desc')
              ->get()->row();

              $last_page = ($qry_user_logs) ? $qry_user_logs->sessionsegment : '';
              $segment_arr = explode('/', $last_page);
              $segment_hash = ($qry_user_logs && $segment_arr) ? $segment_arr[1] : '';
              // GET MODULE NAME OUT OF HASh
              $qry_module = $this->db->select()->from('prime_module_navigations_main')
                  ->where('hashcode', $segment_hash)->get()->row();

              if (count($segment_arr) > 0 && $qry_module) {
                  $module_name = '<i class="fa ' . $qry_module->icon . ' fa-fw"></i> ' . $qry_module->desc;
              } else {
                  if ($segment_hash != '') {
                      $segment_hash = $segment_hash;
                  } else {
                      $segment_hash = $segment_arr[0];
                  }
                  $module_name = '<i class="fa fa-tag fa-fw"></i> ' . ucfirst($segment_hash);
              }


              $last_login = ($qry_user_logs) ? $qry_user_logs->sessiondatetime : 'N/A';
              if($qry_user_logs) {
                  $date_obj = new DateTime($last_login);
                  $date_complete = $date_obj->format('l jS \of F Y h:i:s A');
              }else{
                  $date_complete = 'N/A';
              }

          $roles = $this->db->select()->from('prime_system_users_roles_main AS urm')
              ->join('prime_system_users_roles_matrix AS urmtx','urm.sysid = urmtx.roleid','left')
              ->where(array('userid' => $userid, 'urmtx.status' => 1))
              ->get();

              $data['userroles'] = $roles;

          $data['userid'] = $userid;
          $data['userinfo'] = $qry_user_info;
          $data['userlogs'] = ($date_complete) ? $qry_user_logs : false;
          $data['userllog'] = $date_complete;
          $data['userlnav'] = '<a class="text-danger" href="'.base_url($last_page).'">'.$module_name.'</a>';

          init_header(false);
          $this->load->view('admin/pages/user/profile', $data);
          init_footer(false, '');
      } else {
         redirect(base_url() . 'admin/dashboard');
      }
   }

   function person($id = false) {

   }



   function newentry() {
      if (!empty($this->user_login)) {
         $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
         init_header($data);
         $this->load->view('admin/pages/user/newentry');
         init_footer($data, '');
      } else {
         redirect(base_url() . 'admin/dashboard');
      }
   }
   function addroletouser(){
       $data = array();
       $role = $this->input->post('roleid');
       $userid = $this->input->post('userid');
       $msg = '';
       $func = '';
       $qry = false;

       $insarr = array(
           'userid' => $userid,
           'roleid' => $role
       );
       $this->db->insert("prime_system_users_roles_matrix" , $insarr);
       $msg = 'Role Added.';
       $func = 'success';
       $qry = true;

       $data['msg'] = $msg;
       $data['func'] = $func;
       $data['qry'] = $qry;

       echo json_encode($data);
   }



}
