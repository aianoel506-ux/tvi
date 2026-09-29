<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cnc extends  CI_Controller{

    public function __construct(){
        parent::__construct();
    }
     function getpaymentdetails(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $sql = $this->db->select("tpl.sysid,tro.orid , tro.types, tpl.payform, tpl.orno , tpl.totalamt , tpl.vatsales ,tpl.franchisetax , pcoa.codes, pcoa.descs")
            ->from("trn_request_orvoid AS tro")
            ->join("transaction_payments_logs AS tpl","tpl.sysid = tro.orid" , "left")
            ->join("prime_chart_of_accounts AS pcoa" , "pcoa.sysid = tpl.payforacctno","left") // lucky was here
            ->where(array("groupid"=>$dataid , "tpl.status"=> 1))
            ->get();
         $this->db->_error_message();
        $num_rows = $sql->num_rows();
        if($num_rows > 0){
            foreach ($sql->result() as $row){
                $paytype_opt = '';
                $qry_paytype = $this->db->select('sysid,names')->from('prime_types_parameter')
                    ->where("codes", 'PAYTYPE')->get();
                if($qry_paytype->num_rows()>0) {
                    foreach($qry_paytype->result() as $prow) {
                        if($row->payform==$prow->sysid) {
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }
                        $paytype_opt .= '<option '.$selected.' value="'.$prow->sysid.'">'.$prow->names.'</option>';

                    }
                }
                $control = '';
                if($row->types == 328){
                    $control = '<button type="button" data-or="'.$row->orno.'" data-id="'.$row->sysid.'" id="btn_cancel" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></button>';
                    //status 0
                }
                if($row->types == 329){
                    $control = '<button type="button" data-payform="'.$row->payform.'" data-id="'.$row->sysid.'" id="btn_update" class="btn btn-xs btn-primary"><i class="fa fa-save"></i></button>';
                    //update payform closest tr select
                }
                $data['list'][] = array(
                    'orid' => $row->orid,
                    'type' => $this->getvoidtype($row->types),
                    'orno' => $row->orno,
                    'amttotal' => number_format($row->totalamt , 2).'<input type="hidden" id="totalamt" value="'.$row->totalamt.'">',
                    'amtvar' => number_format($row->vatsales, 2).'<input type="hidden" id="totalvatsales" value="'.$row->vatsales.'">',
                    'amtfrtx' => number_format($row->franchisetax, 2).'<input type="hidden" id="totalfrtx" value="'.$row->franchisetax.'">',
                    'descs' => '<a href="javascript:;" data-trigger="hover" data-toggle="popover" data-placement="left" data-title="'.$row->codes.'" data-content="'.$row->descs.'">'.$row->codes.'</a>', // LUCKY WaS HERE
                    'select' => '<select class="form-control inline" id="selectpayform" name="selectpayform">
                                <option></option>'.$paytype_opt.'
                                </select>',
                    'control' => $control
                );
            }
        }
        echo json_encode($data);
    }
    function getvoidtype($data){
         $names = '';
        $sql =  $this->db->select("names")
             ->from("prime_types_parameter")
         ->where(array("sysid"=>$data))
         ->get()->row();
        if($sql){
            $names =  $sql->names;
        }else{
            $names = '';
        }
        return $names;
    }
}