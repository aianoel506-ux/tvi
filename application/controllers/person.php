<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 7/3/2018
 * Time: 4:46 PM
 */

class Person extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_search');
        $this->load->model('model_person');
    }

    function index($id = false) {
        $data = array();
        if(user_id() > 0 && $id) {
            $person_info = $this->model_person->get_person_info($id);
            $get_person_name = ($person_info) ? $person_info->fullname : 'Unknown';
            $get_person_id = ($person_info) ? $person_info->sysid : 'Unknown';
            $data['pagetitle'] = 'Profile: ' . $get_person_name;
            $data['personid'] = $get_person_id;
            $data['fullname'] = $get_person_name;
            init_header($data);
            $this->load->view('admin/pages/person/profile', $data);
            init_footer(false, '');
        } else {
            redirect(base_url() . 'admin/dashboard');
        }
    }
}