<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inventory extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('model_inventory', 'inventory', true);
        $this->load->model('model_assets', 'assets', true);


        //load library
        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
    }

    function tblgetdatainit() {
        echo $this->inventory->tbl_get_data_initialization();
    }
    function tblsuppliers() {
        echo $this->inventory->tbl_get_suppliers();
    }

    function dataaddinit() {
        echo $this->inventory->data_add_initialization();
    }

    function tblproducts() {
        echo $this->inventory->tbl_products();
    }

    function tblstocks(){
        echo $this->inventory->tbl_get_stocks();
    }

    function addstocks(){
        echo $this->inventory->add_stocks();
    }

    function tblgetstockin(){
        echo $this->inventory->tbl_get_stock_in();
    }

    function draftstockin(){
        echo $this->inventory->draft_stock_in();
    }

    function savestockin(){
        echo $this->inventory->save_stock_in();
    }

    function savestockout(){
        echo $this->inventory->query_stock_out();
    }

    function savestockreturn(){
        echo $this->inventory->query_stock_out();
    }

    function querystockout(){
        echo $this->inventory->query_stock_out();
    }
    function stockdetails(){
        echo $this->inventory->stock_details();
    }
    function generatebarcode($stockid = false, $codestart = false, $codecount = false){
        echo $this->inventory->generate_barcode($stockid, $codestart, $codecount);
    }
    function page($page = null){
        $data = array();
        $data['pagetitle'] = 'Inventory | ' . strtoupper($page);

        init_header_nonav($data);
        $this->load->view('admin/pages/modules/inventory/' . $page, $data);
        init_footer_nonav($data);
    }
    function materialrequest() {
        init_header_nonav();
        $this->load->view('admin/pages/modules/inventory/material_requets');
        init_footer_nonav();
    }
}