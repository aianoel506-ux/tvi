<?php
/**
 * Created by PhpStorm.
 * User: FADERON
 * Date: 3/16/2018
 * Time: 11:36 AM
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function get_contribution_amt($amt) {
    $ci = &get_instance();
    $qry_cont = $ci->db->query("SELECT amtcont FROM prime_contribution_matrix WHERE $amt BETWEEN `amtmin` AND `amtmax`")->row();
    return ($qry_cont) ? $qry_cont->amtcont : 0;
}
function getpayrolltrn($payrollid , $typesid){
    $html = '';
    $ci = &get_instance();
    $getconttrn =  $ci->db->select("trntype , amt")
        ->from("payroll_reports_trn")
        ->where(array("payrollid" =>$payrollid ))
        ->get();
    if($getconttrn->num_rows() > 0){
        foreach ($getconttrn->result() as $row){
            $trntype = $row->trntype;
            $amt = $row->amt;
            if($trntype == $typesid){

                return $amt;
            }
        }
    }
}
if(!function_exists('getpayrollpayclass')) {
    function getpayrollpayclass(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("payrollpayclass,codes,desc")->from("prime_employee_main_payclass_grouping")
            ->group_by("payrollpayclass,codes,desc")
            ->where(array("status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->payrollpayclass,
                    'text' => $row->desc.' - '.$row->codes
                );
            }
        }
        return json_encode($data);
    }
}