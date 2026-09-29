<?php
/**
 * Created by PhpStorm.
 * User: DUDEZKIE
 * Date: 6/19/2019
 * Time: 3:31 PM
 */

class Jo extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('model_jo');
        $this->load->model('model_cwdo');
        $this->load->model('model_mrd');

        require_once APPPATH.'third_party/PHPExcel.php';
        $this->excel = new PHPExcel();
    }

    function getjodetails() {
        echo $this->model_mrd->get_mtr_info();
    }

    function getjoborderlist() {

        echo $this->model_jo->get_joborder_list();
    }

    function select2joborders() {
        echo $this->model_jo->select2_joborders();
    }

    function select2jostatus() {
        echo $this->model_jo->select2_jo_status();
    }

    function savenewjo() {
        echo $this->model_jo->save_new_joborder();
    }

    function getjobordertrntrail(){
        echo $this->model_jo->get_joborder_trn_trail();
    }

    function submitmtrassignment(){
        echo $this->model_jo->submit_meter_assignment();
    }

    function submitmeterreissuerow(){
        echo $this->model_jo->sumbit_meter_reissue_row();
    }

    function submitissuancetemprow(){
        echo $this->model_jo->submit_issuance_temp_row();
    }

    function cancelmtrissuance(){
        echo $this->model_jo->cancel_meter_issuance();
    }


    function accomplishtrans(){
        echo $this->model_jo->accomplish_transaction();
    }

    function accomplishfdo(){
        echo $this->model_jo->accomplish_fdo();
    }

    function getjoborderlogs(){
        echo $this->model_jo->get_joborder_logs();
    }

    function getcustomerreadinginfo(){
        echo $this->model_jo->get_joborder_logs();
    }

    function accomplish(){
        echo $this->model_jo->accomplish();
    }

    function printorder(){
        echo $this->model_jo->print_order();
    }

    function ugetmeterinfo() {
        echo $this->model_jo->utility_get_mtrinfo();
    }

    function saveaccomplishments(){
        echo $this->model_jo->save_accomplishments();
    }

    function checkpecoapps() {
        $conn = $this->load->database('pecoappsdev', TRUE);
        $conn->initialize();
        $qry = $conn->query("SELECT COUNT(servno____) AS cnt FROM father")->row();
        echo 'Father DEV<br>';
        echo $qry->cnt . '<br>';

        $conn1 = $this->load->database('pecoapps', TRUE);
        $conn1->initialize();
        $qry = $conn1->query("SELECT COUNT(servno____) AS cnt FROM father")->row();
        echo 'Father LIVE<br>';
        echo $qry->cnt . '<br>';
    }


    function cleartrans() {
        echo $this->model_jo->clear_trans();
    }


    function test() {
        $arr = check_asset_status(87386);

        print_r($arr);
    }
}