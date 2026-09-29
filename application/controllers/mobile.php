<?php
/**
 * Created by PhpStorm.
 * User: DUDEZ
 * Date: 7/23/2018
 * Time: 4:32 PM
 */

class Mobile extends CI_Controller
{
    function __construct() {
        parent::__construct();
    }
    function index()
    {
        if (user_id() > 0) {
            $data['pagetitle'] = 'Mobile';
            init_frontend_header($data);
            $this->load->view('mobile/common/navs');

            $this->load->view('mobile/pages/home');
            $this->load->view('mobile/common/scripts');
            $this->load->view('mobile/common/footer');

            init_frontend_footer($data);
        } else {
            $data['pagetitle'] = 'Login';
            init_frontend_header($data);
            $this->load->helper(array('form'));
            $data['mobileview'] = true;
            $this->load->view('redirects/forms/view_login', $data);
            $this->load->view('admin/common/scripts');
            init_frontend_footer($data);
        }
    }
}