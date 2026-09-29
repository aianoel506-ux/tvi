<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/22/2018
 * Time: 4:11 PM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Ajax extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_systems');
        $this->load->model('model_admin');
    }

    public function index($p = null)
    {
        if($p!=null) {
            if (file_exists(FCPATH . 'application/views/admin/ajax/' . $p . '.php')) {
                $this->load->view('admin/ajax/' . $p);
            }else{
                echo page_file_notfound('Page not found!', 'error loading file :' .$p.'.php');
            }
        }else{
            echo page_construction();
        }
    }
}