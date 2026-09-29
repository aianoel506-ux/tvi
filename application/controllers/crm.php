<?php
/*
 * NOV. 11, 2020
 * CREATED BY: LUCKY JOHN FADERON
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crm extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_reports');
        $this->load->model('model_crm');

        $this->load->helper('crm_helper');
    }

    public function cdesave() {
        echo $this->model_crm->cde_save();
    }

    function cdelist() {
        echo $this->model_crm->get_ticket_list();
    }

    function cdedetails() {
        echo $this->model_crm->get_ticket_details();
    }

    function loadview()
    {
        $data = array();
        $id = $this->input->post('id');
        $q = $this->db->select()->from('ticketing_details_logs')
            ->where('sysid', $id)->get()->row();
        if($q) {
            $view = $this->db->select()->from('ticketing_page_view')
                ->where(array('codes' => 'CRM', 'statusid' => $q->status))
                ->get()->row();
            if($view==false) {
                echo page_data_notfound_modal('Transaction data not found!' . $id);
            }else {
                $p = $view->viewfile;
                if ($p != null) {
                    $data['id'] = $id;
                    if (file_exists(FCPATH . 'application/views/admin/ajax/' . $p . '.php')) {
                        $this->load->view('admin/ajax/' . $p, $data);
                    } else {
                        echo page_file_notfound('Page not found!', 'error loading file :' . $p . '.php');
                    }
                } else {
                    echo page_construction();
                }
            }
        }else{
            echo page_data_notfound_modal('Transaction data not found!' . $id);
        }
    }


    function getcustalbum() {
        //$location 	= FCPATH . 'uploads/attachments/outages';
        $location 	= './uploads/attachments/crm';
        $album_name	= $this->input->post('album_name');
        $files 		= glob($location . '/' . $album_name . '/*.{jpg,gif,png}', GLOB_BRACE);
        $encoded 	= json_encode($files);
        echo $encoded;
        unset($encoded);
    }

    function assignment() {
        echo $this->model_crm->assign_crm();
    }

    function selectassignee() {
        $data = array();

        $sql = $this->db->select("pem.sysid,p.lastname,p.firstname")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid","left")
            ->where(array("pem.type" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.$row->firstname
                );
            }
        }

        echo json_encode($data);
    }

}