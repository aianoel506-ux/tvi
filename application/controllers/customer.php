<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller {

	private $user_login;
	function __construct() {
	parent::__construct();
		$this->load->library('Datatables');
        $this->load->library('table');
		$this->load->database();
		$this->load->model('model_admin');
		$this->user_login = $this->session->userdata('logged_in');
		// ######################### #############
        // LOCK SCREEN ############# #############
        // ######################### #############
        if(check_user_lock()) {
            redirect(base_url(), 'refresh');
        }
    }
	
	function index($ids = NULL)
	{
		if(!empty($this->user_login)){
			if(!empty($ids)){
				$data['ids'] = $ids;
				$data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
				$this->load->view('admin/common/head');
				$this->load->view('admin/common/topnav'						, $data);
				$this->load->view('admin/common/leftnav'					, $data);
				$this->load->view('admin/pages/customer/profile'			, $data);			
				$this->load->view('admin/common/footer');	
				$this->load->view('admin/common/scripts');	
			}else{
				$data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
				$this->load->view('admin/common/head');
				$this->load->view('admin/common/topnav'						, $data);
				$this->load->view('admin/common/leftnav'					, $data);
				$this->load->view('admin/pages/page404');	
				$this->load->view('admin/common/footer');	
				$this->load->view('admin/common/scripts');					
			}
		}else{
			redirect(base_url().'admin/dashboard');
		}
	}


	function newentry()
	{
		if(!empty($this->user_login)){
			$data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
			$this->load->view('admin/common/head');
			$this->load->view('admin/common/topnav'						, $data);
			$this->load->view('admin/common/leftnav'					, $data);
			$this->load->view('admin/pages/customer/newentry');				
			$this->load->view('admin/common/footer');	
			$this->load->view('admin/common/scripts');					
		}else{
			redirect(base_url().'admin/dashboard');
		}
	}

}

