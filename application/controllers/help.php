<?php
/**
 * Created by PhpStorm.
 * User: DUDEZ
 * Date: 5/28/2018
 * Time: 4:50 PM
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Help extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('model_admin');
        $this->load->model('model_auth');

    }

    function index()
    {
        if(user_id()) {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_id());
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_id());
            $data['usersmodule'] = $this->model_admin->select_modules();
            $data['pagetitle'] = 'Help';
            init_header(false);
            $this->load->view('admin/pages/help', $data);
            init_footer(false, '');
        }else{
            $data['title'] = 'PECO.net';
            $data['message'] = 'Your access is time out or you have no access to this page, please return to previous page!';
            echo page_session($data);
        }
    }
}