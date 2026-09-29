<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Audit extends  CI_Controller{

    public function __construct(){
        parent::__construct();
    }
    function cancelor(){
        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $dataid = $this->input->post('dataid');
        $moduleid = $this->input->post('moduleid');
        $orno = $this->input->post('dataor');

        $ordatalse = $this->db->select('payforacctno')->from('transaction_payments_logs')
            ->where('sysid', $dataid)->get()->row();

        $this->db->trans_begin();

        $audit_ins_arr = array(
            'dataid' => $dataid,
            'moduleid' => $moduleid,
            'valueold' => $orno,
            'valuenew' => 0,
            'createdby' => user_id(),
            'remarks' => 'CANCELLED OR'
        );
        $audit_ins = audit_insert($audit_ins_arr);


        $updatearr = array(
            'status' => 303
        );
        $this->db->where(array("sysid"=> $dataid));
        $this->db->update("transaction_payments_logs" , $updatearr);

        if($ordatalse->payforacctno==114) {
            $this->db->where('orno', $orno);
            $this->db->update('billing_payapplied', array('status' => 0));
        }

        if($this->db->trans_status() == TRUE && $audit_ins){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'OR cancelled';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Error cancelling OR';
            $func = 'error';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function updateor(){
        $data = array();
        $datapayform = $this->input->post('datapayform');
        $dataid = $this->input->post('dataid');
        $moduleid = $this->input->post('moduleid');
        $selectval = $this->input->post('selectval');
        $qry = false;
        $msg = '';
        $func = '';

        $audit_ins_arr = array(
            'dataid' => $dataid,
            'moduleid' => $moduleid,
            'valueold' => $datapayform,
            'valuenew' => $selectval,
            'createdby' => user_id(),
            'remarks' => 'UPDATE PAYFORM'
        );
        $audit_ins = audit_insert($audit_ins_arr);


        $updatearr = array(
            'payform' => $selectval
        );
        $this->db->where(array("sysid"=> $dataid));
        $this->db->update("transaction_payments_logs" , $updatearr);

        if($this->db->trans_status() == TRUE && $audit_ins){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'OR updated';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Error updating OR';
            $func = 'error';
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;

        echo json_encode($data);
    }
    function accomplishtransaction(){
        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $dataid = $this->input->post('dataid');

       $sql = $this->db->query("UPDATE transaction_request_main AS rm INNER JOIN transaction_request_main_trails AS mt ON mt.trnid = rm.sysid SET rm.status = 2 WHERE mt.dataid = {$dataid}");

       $this->db->_error_message();
       if($sql){
            $qry = true;
            $msg = 'Transactions Accomplish';
            $func = 'success';
        }else{
           $qry = false;
           $msg = 'Fail Accomplishing Transactions';
           $func = 'error';
       }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }

}