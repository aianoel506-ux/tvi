<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pages extends CI_Controller
{
	 private $user_login;
     public function __construct()
     {
          parent::__construct();
		  $this->load->model('model_admin');
		  $this->user_login = $this->session->userdata('logged_in');
    }
     public function index()
    {

	}

	
	public function error404(){
		if($this->user_login)
		{
			$data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
			$data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
			$data['usersmodule'] = $this->model_admin->select_modules();
			$this->load->view('admin/common/head');
			$this->load->view('admin/common/topnav'						, $data);
			$this->load->view('admin/common/leftnav'					, $data);
			$this->load->view('admin/pages/page404'						, $data);
			$this->load->view('admin/common/footer');	
			$this->load->view('admin/common/scripts');	
			$this->load->view('includes/scripts/dashboard');
			$this->load->view('admin/common/end');	
		}else{
			redirect(base_url().'auth', 'refresh');
		}
	}

    function load($folder = false, $file = false, $module = true) {
        $this->load->model('model_cad');
        $this->load->model('model_systems');
	    if($module) {
            if ($folder && $file) {
                print $this->load->view('admin/pages/modules/' . $folder . '/' . $file);
            } else {
                print $this->load->view('admin/pages/modules/' . $folder);
            }
        }else{
            print $this->load->view('admin/pages/'.$folder);
        }
    }

    function loader() {
        $page = $this->input->post('page');
        $this->load->model('model_systems');
        print $this->load->view('admin/pages/'.$page);
    }
	
}