<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/21/2018
 * Time: 3:59 PM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Guest extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $data = array();
        if(user_id()) {
            if(check_access()) {
                $data['pagetitle'] = 'PECO - Trouble Call System';
                init_frontend_header($data);
                init_frontend_navs($data);
                $this->load->view('frontend/pages/outage/list', $data);
                init_frontend_footer($data);
            }else {
                init_frontend_header($data);
                echo '<div style="width: 50%; margin: auto 25%; padding: 30px 30px; display: inline-block; margin-top: 10%; text-align: center; background: #f1f1e3; border: #ccc;">';
                echo '<h3 style="color: red"><i class="fa fa-warning"></i> Your network is not allowed to access this page!</h3>';
                echo '<div class="cssload-content">
                    <div>
                        <div class="cssload-l1"></div>
                        <div class="cssload-l2"></div>
                        <div class="cssload-l3"></div>
                    </div>
                </div>';
                echo '</div>';
                init_frontend_footer($data);
            }
        }else {
            if(check_access()) {
                $data['pagetitle'] = 'Login @ PECO - Trouble Call System';
                init_frontend_header($data);
                $this->load->view('frontend/common/front_end_login', $data);
                init_frontend_footer($data);
            }else {
                init_frontend_header($data);
                echo '<div style="width: 50%; margin: auto 25%; padding: 30px 30px; display: inline-block; margin-top: 10%; text-align: center; background: #f1f1e3; border: #ccc;">';
                echo '<h3 style="color: red"><i class="fa fa-warning"></i> Your network is not allowed to access this page!</h3>';

                echo '<div class="cssload-content">
                    <div>
                        <div class="cssload-l1"></div>
                        <div class="cssload-l2"></div>
                        <div class="cssload-l3"></div>
                    </div>
                </div>';
                echo '</div>';
                init_frontend_footer($data);
            }
        }

    }

    public function view($id = false) {
        if(check_access()) {
            $data = array();
            $chk_outage = $this->db->select('sysid')->from("ticketing_details_logs")->where('sysid', $id)->get()->row();
            $data['id'] = ($chk_outage) ? $chk_outage->sysid : false;
            if (user_id()) {
                init_frontend_header($data);
                init_frontend_navs($data);

                $this->load->view('frontend/pages/outage/view', $data);

                init_frontend_footer($data);
            } else {
                echo 'Please login!';
            }
        }
    }

    function inquiry() {
        $data = array();
        $data['pagetitle'] = 'Customer Service';
        $data['inquiryonly'] = true;
        init_frontend_header($data);
        init_frontend_page_top($data);
        $this->load->view('frontend/pages/inquiry/view', $data);
        init_frontend_page_bottom($data);
        init_frontend_footer($data, 'frontend/pages/inquiry/scripts');
    }


}