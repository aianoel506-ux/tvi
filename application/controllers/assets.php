<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');
session_start();

class Assets extends CI_Controller {

   private $user_login;

   function __construct() {
      parent::__construct();
      $this->load->model('model_assets');
   }


    function getassetpic() {
        echo $this->model_assets->get_asset_pics();
    }

    function uploadassetpic() {
        echo $this->model_assets->upload_asset_pic();
    }


    function index($id = false) {

   }

   function getassettbl() {
       echo $this->model_assets->get_assets_tbl();
   }

   function getassetdetails() {
       echo $this->model_assets->get_assets_details();
   }

   function getassetsfromcustomer() {
       $qry_father = $this->db->select('sysid, mtrno, mtrserial, dateconnected')->from('customer_accounts_main')->get();
       $acctno = 0;
       $this->db->query("TRUNCATE TABLE assets_main;");
       $this->db->query("TRUNCATE TABLE customer_accounts_meter_issuance;");

       foreach($qry_father->result() as $row) {
           $acctno += 1;
           $acctid = $row->sysid;
           $asset_code = 'MTR'.str_pad($row->mtrno, 8, '0', STR_PAD_LEFT);
           $date_issued = $row->dateconnected;
           $mtrno = $row->mtrno;
           $mtrserial = $row->mtrserial;
           $ins_asset_arr = array(
               'purchaseid' => 0,
               'serialcodes' => $asset_code,
               'desc' => 'Meter : '.$mtrno. ' / '.$mtrserial,
               'types' => 320
           );
           $this->db->insert('assets_main', $ins_asset_arr);
           $assetid = $this->db->insert_id();

           $ins_arr = array(
               'acctid' => $acctid,
               'assetid' => $assetid,
               'mtrno' => $mtrno,
               'mtrserial' => $mtrserial,
               'dateissued' => $date_issued,
               'issuedby' => user_id(),
               'createdby' => user_id(),
               'updatedby' => user_id(),
           );
           $this->db->insert('customer_accounts_meter_issuance', $ins_arr);
       }

       echo $acctno;
   }

    function addbrand(){
        $data = array();
        $msg = '';
        $func = '';
        $qry = false;

        $brandcode = $this->input->post('brandcode');
        $braddesc = $this->input->post('branddesc');
        $this->db->trans_begin();

        $checkbrand = $this->db->select("codes , descs")
            ->from("prime_items_brands")
            ->where(array("codes" => $brandcode , "descs" => $braddesc))
            ->get()->row();
        if(!$checkbrand){
            $arrins = array(
                'codes' => $brandcode,
                'descs' => $braddesc
            );
            $this->db->insert("prime_items_brands" , $arrins);
            if($this->db->trans_status() === TRUE){
                $this->db->trans_commit();
                $msg = 'Brand Added';
                $func = 'success';
                $qry = true;
            }else{
                $this->db->trans_rollback();
                $msg = 'Erorr Adding';
                $func = 'error';
            }

        }else{
            $msg = 'Brand already exist';
            $func = 'error';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }
    function savenewasset(){
        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $dataid = $this->input->post('dataid');
        $assettype = $this->input->post('assettype');
        $meterno = $this->input->post('meterno');
        $amps = $this->input->post('amps');
        $volts = $this->input->post('volts');
        $newbrand = $this->input->post('newbrand');
        $serial = $this->input->post('serial');
        $desc = $this->input->post('description');

        $this->db->trans_begin();

        $assetsmainins = array(
            'serialcodes' => $serial,
            'types' => $assettype,
            'desc' => $desc,
            'status' => 1
        );

        $this->db->insert("assets_main" , $assetsmainins);

        if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Items Added';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Error Adding Item';
            $func = 'error';
        }

        $assetid = $this->db->select("sysid")
            ->from("assets_main")
            ->order_by("sysid", "desc")
            ->get()->row();

        $assetsbrandhistory = array(
            'assetid' => $assetid->sysid,
            'brandid' => $newbrand,
            'createdby' => user_id()
        );

        $this->db->insert("assets_brand_history" , $assetsbrandhistory);

        $assetsmainownerhistory = array(
            'assetid' => $assetid->sysid,
            'ownertype'=>$assettype,
            'createdby' => user_id()
        );

        $this->db->insert("assets_main_owner_history" , $assetsmainownerhistory);

        if($assettype == 320){
            $customeraccountsmeterissued = array(
                'status' => 1,
                'assetid' => $assetid->sysid,
                'mtrno' => $meterno,
                'amps' => $amps,
                'volts' => $volts,
                'mtrserial' => $serial,
                'createdby' => user_id()
            );
            $this->db->insert("customer_accounts_meter_issuance" , $customeraccountsmeterissued);

            if($this->db->trans_status() === TRUE){
                $this->db->trans_commit();
                $qry = true;
                $msg = 'Items Added';
                $func = 'success';
            }else{
                $this->db->trans_rollback();
                $qry = false;
                $msg = 'Error Adding Item';
                $func = 'error';
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function getitemdetails(){
        $data = array();
        $id = $this->input->post('id');
        $amps = 0;
        $volts = 0;
        $sql = $this->db->select("AM.serialcodes ,AM.desc , AM.types , AM.status , ABH.brandid ,CAMI.mtrno , CAMI.mtrserial , CAMI.amps , CAMI.volts")
            ->from("assets_main AS AM")
            ->join("assets_brand_history AS ABH" , "ABH.assetid = AM.sysid" , "left")
            ->join("customer_accounts_meter_issuance AS CAMI" , "CAMI.assetid = AM.sysid" , "left")
            ->where(array("AM.sysid" => $id , 'AM.status' => 1))
            ->get()->row();
            if($sql){
                if($sql->amps === null){
                    $amps = 0;
                }else{
                    $amps = $sql->amps;
                }
                if($sql->volts === null){
                    $volts = 0;
                }else{
                    $volts = $sql->volts;
                }
                $data['assetcode'] = $sql->serialcodes;
                $data['brand'] = $this->getbrand($sql->brandid);
                $data['amp'] = $amps;
                $data['volts'] = $volts;
                $data['descriptions'] = $sql->desc;
            }
            echo json_encode($data);
    }
    function getbrand($data){
        $sql = $this->db->select("descs")
            ->from("prime_items_brands")
            ->where(array('sysid' => $data))
            ->get()->row();
        return $sql->descs;
    }
    function updatetagitem(){
        $data = array();
        $id = $this->input->post('id');
        $moduleid = $this->input->post('originid');
        $dataid = $this->input->post('dataid');
        $qry = false;
        $msg = '';
        $func = '';
        $this->db->trans_begin();
        $updateassetmain = array(
            'status' => 2
        );
        $this->db->where(array("sysid"=>$id , "status" => 1));
        $this->db->update("assets_main" , $updateassetmain);
        $updatecustomerissuancevals = array(
            'acctid' =>$dataid ,
            'dateissued' =>date("Y-m-d"),
            'updatedby' => user_id(),
            'status' => 1,
            'issuedby' =>user_id()
        );
        $this->db->where(array("assetid"=>$id , "status" => 1));
        $this->db->update("customer_accounts_meter_issuance" , $updatecustomerissuancevals);

        $updateassetsmainownerhistory = array(
            'ownerid' => $dataid,
            'updatedby' => user_id(),
            'dateupdated' => date("Y-m-d")
        );
        $this->db->where(array("assetid"=>$id , "status" => 1));
        $this->db->update("assets_main_owner_history" , $updateassetsmainownerhistory);
        $audit_ins_arr = array(
            'dataid' => $id,
            'moduleid' => $moduleid,
            'valueold' => 0,
            'valuenew' => 0,
            'createdby' => user_id(),
            'remarks' => 'TAG ASSET'
        );
        $audit_ins = audit_insert($audit_ins_arr);

        if($this->db->trans_status() === TRUE && $audit_ins){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Asset has been tagged';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Failed tagging asset';
            $func = 'error';
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['sysid'] = $id;

        echo json_encode($data);
    }

    function checkuseriftagged(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $originid = $this->input->post('originid');
        $tagged = false;
        if($originid == 35){
            $sql = $this->db->select("cami.assetid , am.serialcodes , cami.amps , cami.volts , am.desc AS decriptions , abh.brandid ")
                ->from("customer_accounts_meter_issuance AS cami")
                ->join("assets_brand_history AS abh" , "abh.assetid = cami.assetid" , "left")
                ->join("assets_main AS am" , "am.sysid = cami.assetid" , "left")
                ->where(array("cami.acctid" => $dataid , "cami.status" => 1))
                ->get()->row();
            $this->db->_error_message();
            if($sql){
                $tagged = true;
                $data['mtrserial'] = $sql->serialcodes;
                $data['amps'] = $sql->amps;
                $data['volts'] = $sql->volts;
                $data['desc'] = $sql->decriptions;
                $data['brand'] = $this->getbrand($sql->brandid);
                $data['assetid'] = $sql->assetid;
            }
        }
        $data['tagged'] = $tagged;
        echo json_encode($data);
    }

    function test() {
        echo '<pre>';
        print_r(pay_account_array());
    }
    function getmeterassignment(){
        $data = array();
        $dataid = $this->input->post('dataid');
        //customer_accounts_meter_issuance assets_brand_history  assets_main
        $sql = $this->db->select("cami.assetid ,am.serialcodes , cami.amps , cami.volts , am.desc AS decriptions , abh.brandid ")
            ->from("customer_accounts_meter_issuance AS cami")
            ->join("assets_brand_history AS abh" , "abh.assetid = cami.assetid" , "left")
            ->join("assets_main AS am" , "am.sysid = cami.assetid" , "left")
            ->where(array("cami.acctid" => $dataid , "cami.status" => 1))
            ->get()->row();
        $this->db->_error_message();
        if($sql){
                $data['mtrserial'] = $sql->serialcodes;
                $data['amps'] = $sql->amps;
                $data['volts'] = $sql->volts;
                $data['desc'] = $sql->decriptions;
                $data['brand'] = $this->getbrand($sql->brandid);
                $data['assetid'] = $sql->assetid;
        }

        echo json_encode($data);
    }
    function untagasset(){
        $data = array();
        $id = $this->input->post('assetid');
        $moduleid = $this->input->post('originid');
        $dataid = $this->input->post('dataid');
        $qry = false;
        $msg = '';
        $func = '';
        $this->db->trans_begin();
        $updateassetmain = array(
            'status' => 1
        );
        $this->db->where(array("sysid"=>$id , "status" => 2));
        $this->db->update("assets_main" , $updateassetmain);
        $updatecustomerissuancevals = array(
            'dateissued' => null,
            'updatedby' => null,
            'createdby' => user_id(),
            'acctid' => null
        );
        $this->db->where(array("assetid"=>$id , "status" => 1));
        $this->db->update("customer_accounts_meter_issuance" , $updatecustomerissuancevals);

        $updatebrandhistory = array(
            'updatedby' => null
        );
        $this->db->where(array("assetid"=>$id , "status" => 1));
        $this->db->update("assets_brand_history" , $updatebrandhistory);

        $updateassetsmainownerhistory = array(
            'ownerid' => 0,
            'dateupdated' => null,
            'updatedby' => null
        );
        $this->db->where(array("assetid"=>$id , "status" => 1));
        $this->db->update("assets_main_owner_history" , $updateassetsmainownerhistory);
        $audit_ins_arr = array(
            'dataid' => $id,
            'moduleid' => $moduleid,
            'valueold' => $id,
            'valuenew' => 0,
            'createdby' => user_id(),
            'remarks' => 'UNTAG ASSET'
        );
        $audit_ins = audit_insert($audit_ins_arr);

        if($this->db->trans_status() === TRUE && $audit_ins){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Asset has been Untagged';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Failed untagging asset';
            $func = 'error';
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['sysid'] = $id;
        echo json_encode($data);
    }
    function getassetreports(){
       $data = array();
     //  $status = $this->input->post('status');
       $sql = $this->db->select("am.sysid , am.serialcodes , am.desc, am.status , ptp.names , amoh.ownerid")
            ->from("assets_main as am")
           ->join('prime_types_parameter as ptp' ,"ptp.sysid = am.types" , "left")
           ->join("assets_main_owner_history as amoh" , "amoh.assetid = am.sysid" , "left")
           ->limit(100)
           ->get();
       if($sql->num_rows() > 0){
           $num = 1;
           foreach ($sql->result() as $row){
               if($row->status == 1){
                   $stat = '<span class="label label-success">Active</span>';
               }else if($row->status == 2){
                   $stat = '<span class="label label-success">Issued</span>';
               }
               $data['assetreportdata'][] = array(
                    "num" => $num++,
                    "assetcode" => $row->serialcodes,
                    "assetdesc" => $row->desc,
                    "assetowner" => $row->ownerid,
                    "assetloc" => '',
                    "assetstat" => $stat,
                    "assettype" => $row->names,
                    "control" => ''
               );
           }
       }

       echo json_encode($data);
    }


    function getassetlist() {
       echo $this->model_assets->get_assets_tbl();
    }

    function syncmtrasset() {
       echo $this->model_assets->sync_mtr_asset();
    }

    function assetinfo() {
       echo $this->model_assets->get_assets_info();
    }
    function submitmeterissuance(){
       echo $this->model_assets->submit_meter_issuance();
    }
    function getbrands(){
       echo $this->model_assets->get_brands();
    }
    function getassettypes(){
       echo $this->model_assets->get_asset_types();
    }
    function getmisremtypes(){
       echo $this->model_assets->get_mis_rem_types();
    }
    function submitremarks(){
       echo $this->model_assets->submit_remarks();
    }
    function getremarkstable(){
       echo $this->model_assets->get_remarks_table();
    }
    function editinfo(){
       echo $this->model_assets->edit_info();
    }
    function getassetsspecifications(){
       echo $this->model_assets->get_assets_specifications();
    }
    function addnewbrands(){
       echo $this->model_assets->add_new_brands();
    }

    function deactivateasset(){
       echo $this->model_assets->deactivate_asset();
    }
    function uploadmisdata(){
       echo $this->model_assets->upload_mis_data();
    }

    function release(){
       echo $this->model_assets->release_status();
    }
    function submitrowreading(){
       echo $this->model_assets->submit_row_reading();
    }
    function submitrowmult(){
       echo $this->model_assets->submit_row_mult();
    }
    function submitrowvolts(){
       echo $this->model_assets->submit_row_volts();
    }
    function submitrowwiresize(){
       echo $this->model_assets->submit_row_wiresize();
    }
    function savemtsreading(){
       echo $this->model_assets->save_mts_reading();
    }
    function tblmtsreading(){
       echo $this->model_assets->tbl_meter_readings();
    }
    function mtsreadingdetails(){
       echo $this->model_assets->tbl_meter_readings_details();
    }


    // STOCKS
    function select2stocks() {
       echo $this->model_assets->select2_stocks();
    }
}
