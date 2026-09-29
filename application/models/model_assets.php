<?php

class Model_assets extends CI_model{


    function get_assets_tbl() {
        $data = array();
        $status = $this->input->post('status');
        $search = $this->input->post('searchtxt');
        $view = $this->input->post('view');
        $dataid = $this->input->post('dataid');
        $datestart = $this->input->post('datestart');
        $dateend = $this->input->post('dateend');

        if($view && $view == 'utility') {
            $qry = $this->db->select("
                am.sysid,
                am.labels,
                am.serials,
                am.dateupdated,
                am.brand,
                am.`status` AS STATUS")
                ->from("assets_main AS am")
                ->join("prime_types_parameter AS tp", "tp.sysid = am.types", "left")
                ->where(array("am.types" => 320, "am.status" => $status))
                ->order_by("am.sysid", "desc")
                ->get();
        }else {
            if($status == 1){
                if ($search && $search != ''){
                    $qry = $this->db->query("SELECT
                    am.sysid,
                    am.labels,
                    am.serials,
                    am.brand,
                    tp.`desc` AS ownership,
                    am.`status` AS STATUS   ,
                    am.dateupdated
                FROM
                    assets_main AS am
                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = am.types 
                WHERE
                am.types = 320 
                AND am.status = 1 
                AND (am.serials LIKE '" . $search . "%'  OR am.labels LIKE '" . $search . "%')
                ORDER BY am.sysid DESC
                LIMIT 100");
                    $data['datasearch'] = $qry->result();
                } else {
                    //with date filter
                    if ($datestart && $dateend) {

                        if ($dataid > 0) {
                            if ($dataid == 3204) {
                                $dataid = 1;
                            }
                            $qry = $this->db->query("SELECT
                            am.sysid,
                            am.labels,
                            am.serials,
                            am.brand,
                            tp.`desc` AS ownership,
                            am.`status` AS STATUS   ,
                            am.dateupdated
                        FROM
                            assets_main AS am
                        LEFT JOIN prime_types_parameter AS tp ON tp.sysid = am.types 
                        WHERE
                        am.types = 320 
                        AND (am.status = '$dataid') 
                        AND ((DATE(am.datecreated) BETWEEN '$datestart' AND '$dateend') OR (DATE(am.dateupdated) BETWEEN '$datestart' AND '$dateend'))
                        AND am.sysid NOT IN (SELECT assetid FROM assets_main_owner_history WHERE `status` = 1) AND (am.serials LIKE '" . $search . "%'  OR am.labels LIKE '" . $search . "%')
                        ORDER BY am.sysid DESC
                        LIMIT 100");
                        } else {
                            $qry = $this->db->query("SELECT
                            am.sysid,
                            am.labels,
                            am.serials,
                            am.brand,
                            tp.`desc` AS ownership,
                            am.`status` AS STATUS   ,
                            am.dateupdated
                        FROM
                            assets_main AS am
                        LEFT JOIN prime_types_parameter AS tp ON tp.sysid = am.types 
                        WHERE
                        am.types = 320 
                        AND (am.status = 1 OR am.status = 3202) 
                        AND ((DATE(am.datecreated) BETWEEN '$datestart' AND '$dateend') OR (DATE(am.dateupdated) BETWEEN '$datestart' AND '$dateend'))
                        AND am.sysid NOT IN (SELECT assetid FROM assets_main_owner_history WHERE `status` = 1) AND (am.serials LIKE '" . $search . "%'  OR am.labels LIKE '" . $search . "%')
                        ORDER BY am.sysid DESC
                        LIMIT 100");
                        }


                    } else {
                        $qry = $this->db->query("SELECT
                    am.sysid,
                    am.labels,
                    am.serials,
                    am.brand,
                    tp.`desc` AS ownership,
                    am.`status` AS STATUS   ,
                    am.dateupdated
                FROM
                    assets_main AS am
                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = am.types 
                WHERE
                am.types = 320 
                AND am.status = 1 
                AND am.sysid NOT IN (SELECT assetid FROM assets_main_owner_history WHERE `status` = 1)
                ORDER BY am.sysid DESC
                LIMIT 100");
                    }
                }


            }else if($status == 2){
                if($datestart && $dateend){
                    $qry = $this->db->query("SELECT
                    am.sysid,
                    am.labels,
                    am.serials,
                    am.brand,
                    tp.`desc` AS ownership,
                    am.`status` AS STATUS  ,
                    am.dateupdated
                FROM
                    assets_main AS am
                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = am.types 
                WHERE
                am.types = 320 
                AND am.status = 1 
                AND ((DATE(am.datecreated) BETWEEN '$datestart' AND '$dateend') OR (DATE(am.dateupdated) BETWEEN '$datestart' AND '$dateend'))
                AND am.sysid  IN (SELECT assetid FROM assets_main_owner_history WHERE `status` = 1)
                ORDER BY am.sysid DESC
                LIMIT 100");
                }else{
                    $qry = $this->db->query("SELECT
                    am.sysid,
                    am.labels,
                    am.serials,
                    am.brand,
                    tp.`desc` AS ownership,
                    am.`status` AS STATUS ,
                    am.dateupdated
                FROM
                    assets_main AS am
                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = am.types 
                WHERE
                am.types = 320 
                AND am.status = 1 
                AND am.sysid  IN (SELECT assetid FROM assets_main_owner_history WHERE `status` = 1)
                ORDER BY am.sysid DESC
                LIMIT 100");
                }

            }
        }
        $num_rows = $qry->num_rows();
        if($num_rows > 0) {
            foreach($qry->result() as $row) {
                $sysid = $row->sysid;
                $control = '';

                if($view == 'utility') {
                    $control .= '<a class="btn btn-primary inline" id="btn_get_mtr" data-val="'.$row->labels.'" href="#input_mtrno_'.$dataid.'"><i class="fa fa-download"></i></a>';
                }

                $control .= '<a class="btn btn-info inline" href="' . base_url('module/6052521b7625e31d4ee9cc706732484fcf850877/view/' . $sysid) . '"><i class="fa fa-search"></i></a>';


                $user_role_arr = get_users_roles_matrix_id_arr();

                if(in_array(1, $user_role_arr)) {
                    $control .= '<a class="btn btn-danger inline" href="javascript:;" id="btn_del" data-id="'.$row->sysid.'"><i class="fa fa-times"></i></a>';
                }

                $spec_arr = array();

                $status_arr = check_asset_status($sysid);
                $statustext = $status_arr->status_text;
                $dateissued = $status_arr->status_date;

                $qry_spec = $this->db->select('msm.specval, tp.names')
                    ->from('assets_main_specifications_matrix AS msm')
                    ->join('prime_types_parameter AS tp', 'tp.sysid = msm.specid')
                    ->where(array('msm.assetid' => $sysid, 'msm.status' => 1))
                    ->get();

                $type = '';
                $ercseal = '';
                $pecoseal = '';
                $ampere = '';
                $volts = '';
                $wiresize = '';

                if($qry_spec->num_rows() > 0) {
                    foreach($qry_spec->result() as $srow) {

                        if(strtolower(str_replace(' ', '', $srow->names)) == 'type') {
                            $type = $srow->specval;
                        }

                        if(strtolower(str_replace(' ', '', $srow->names)) == 'ercseal') {
                            $ercseal = $srow->specval;
                        }

                        if(strtolower(str_replace(' ', '', $srow->names)) == 'pecoseal') {
                            $pecoseal = $srow->specval;
                        }

                        if(strtolower(str_replace(' ', '', $srow->names)) == 'ampere') {
                            $ampere = $srow->specval;
                        }

                        if(strtolower(str_replace(' ', '', $srow->names)) == 'volts') {
                            $volts = $srow->specval.'V';
                        }
                        if(strtolower(str_replace(' ', '', $srow->names)) == 'wire size') {
                            $wiresize = $srow->specval;
                        }
                    }
                }
                $checkstat = check_asset_status($sysid);
                $data['statues'][] = $checkstat;

                $getreading = $this->db->select("specval")->from("assets_main_specifications_matrix")
                    ->where(array("status" => 1 , "assetid" => $sysid , "specid" => 3206))
                    ->get()->row();
                $reading = ($getreading) ? $getreading->specval : '';
                $getmult = $this->db->select("specval")->from("assets_main_specifications_matrix")
                    ->where(array("status" => 1 , "assetid" => $sysid , "specid" => 3214))
                    ->get()->row();
                $mult = ($getmult) ? $getmult->specval : '';
                $getbrand = $this->db->select('descs')->from('prime_brands')
                    ->where('sysid',$row->brand)->get()->row();
                $make = ($getbrand) ? $getbrand->descs : '';
                //  if($status == 1 && $checkstat->status_available == true){
                    $releasestat =($row->STATUS != 3202) ? '<input type="checkbox" name="assetsarr['.$row->sysid.']" class="icheck" value="'.$row->sysid.'">' : '<a class="btn btn-default inline tooltips"  data-placement="right" title="Release date: '.$row->dateupdated.'"><i class="fa fa-search"></i></a>';
                    $asset_mrow = array(
                        'expand' => $releasestat,
                        'modified' => ($dateissued == '') ? $row->dateupdated : $dateissued,
                        'types' => $type,
                        'assetnumber' => $row->labels,
                        'assetserial' => $row->serials,
                        'status' => ($row->STATUS == 3202) ? '<span class="label label-info">Released</span>' : $statustext,
                        'type' => $type,
                        'ercseal' => $ercseal,
                        'pecoseal' => $pecoseal,
                        'ampere' => '<input value="'.$ampere.'" data-id="'.$sysid.'"  type="text" style="width: 100% !important;" class="form-control inline inpu-xs" id="reading" name="reading" placeholder="0" />',
                        'make' => $make,
                        'reading' => '<input value="'.$reading.'" data-id="'.$sysid.'"  type="text" style="width: 100% !important;" class="form-control inline inpu-xs" id="reading" name="reading" placeholder="0" />',
                        'mult' => '<input value="'.$mult.'" data-id="'.$sysid.'"  type="text" style="width: 100% !important;" class="form-control inline inpu-xs" id="mult" name="mult" placeholder="0" />',
                        'volts' => '<input value="'.$volts.'" data-id="'.$sysid.'"  type="text" style="width: 100% !important;" class="form-control inline inpu-xs" id="volts" name="volts" placeholder="0" />',
                        'wiresize' => '<input value="'.$wiresize.'" data-id="'.$sysid.'"  type="text" style="width: 100% !important;" class="form-control inline inpu-xs" id="wiresize" name="wiresize" placeholder="0" />',
                        'control' => $control

                    );
               // }else if($status == 2 && $checkstat->status_available == false){

                $row_arr = array_merge($asset_mrow, $spec_arr);

                $data['list'][] = $row_arr;
            }
        }
        // $data["input"] = $this->input->post();
        // $data["draw"] = 1;
        // $data["recordsTotal"] = $num_rows;
        // $data["recordsFiltered"] = $num_rows;
        return json_encode($data);
    }

    function sync_mtr_asset() {
        $data = array();
        $cust_num = 0;
        $cust_num_erp = 0;
        $cust_num_insert = 0;
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();

            $qry_f = $conn->query("SELECT 
                LTRIM(RTRIM(f.servno____)) AS servno,
                f.mtr_______ AS mtr,
                LTRIM(RTRIM(f.condte____)) AS contractdate,
                LTRIM(RTRIM(f.status____)) AS status,
                LTRIM(RTRIM(f.stadte____)) AS conndate,
                LTRIM(RTRIM(f.mtrser____)) AS mtrno,
                LTRIM(RTRIM(f.serial____)) AS mtrserial
                FROM father AS f");

            if($qry_f->num_rows() > 0) {
                foreach($qry_f->result() as $row) {
                    $cust_num += 1;

                    $servno = $row->servno;
                    $mtr = $row->mtr;

                    $check_asset_main = $this->db->select('labels')
                        ->from('assets_main')
                        ->where(array(
                            'labels' => $row->mtrno,
                            'serials' => $row->mtrserial,
                        ))->get()->row();

                    if($check_asset_main == false) {

                        $qry_main_check = $this->db->select()
                            ->from('customer_accounts_main')
                            ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                            ->get()->row();


                        $qry_meter_db = $conn->query("SELECT 
                                                        Type AS [type],
                                                        Erc_seal AS [ercseal],
                                                        Peco_seal AS [pecoseal],
                                                        Ampere AS [ampere],
                                                        Volts AS [volts],
                                                        Kh AS [kh],
                                                        Date AS [dateissue],
                                                        Make AS [brand]
                                                  FROM mis_meter_data 
                                                  WHERE Meter_no = '{$row->mtrno}' AND Serial_no = '{$row->mtrserial}'")->row();


                        $ins_asset_main_arr = array(
                            'labels' => $row->mtrno,
                            'serials' => $row->mtrserial,
                            'types' => 320,
                        );
                        $asset_ins = $this->db->insert('assets_main', $ins_asset_main_arr);
                        $asset_id = $this->db->insert_id();

                        if ($qry_meter_db) {
                            if (trim($qry_meter_db->brand) != '') {
                                $get_brand_id = $this->db->select()
                                    ->from('prime_brands')
                                    ->where(array('codes' => trim($qry_meter_db->brand)))
                                    ->get()->row();
                                if ($get_brand_id) {
                                    $brand_id = $get_brand_id->sysid;
                                } else {
                                    $insert_brand_arr = array(
                                        'codes' => trim($qry_meter_db->brand),
                                        'descs' => trim($qry_meter_db->brand),
                                    );
                                    $this->db->insert('prime_brands', $insert_brand_arr);
                                    $brand_id = $this->db->insert_id();

                                    $insert_brand_cat = array(
                                        'brandid' => $brand_id,
                                        'catid' => 2
                                    );
                                    $this->db->insert('prime_brands_category_matrix', $insert_brand_cat);
                                }
                                $this->db->query("UPDATE assets_main SET brand = $brand_id WHERE sysid = $asset_id");
                            }

                            // ######################################
                            // TYPE SEAL
                            if (trim($qry_meter_db->type) != '') {
                                $ins_meter_spec = array(
                                    'assetid' => $asset_id,
                                    'specid' => 3098,
                                    'specval' => trim($qry_meter_db->type)
                                );
                                $this->db->insert('assets_main_specifications_matrix', $ins_meter_spec);
                            }

                            // ######################################
                            // ERC SEAL
                            if (trim($qry_meter_db->ercseal) != '') {
                                $ins_meter_spec = array(
                                    'assetid' => $asset_id,
                                    'specid' => 3094,
                                    'specval' => trim($qry_meter_db->ercseal)
                                );
                                $this->db->insert('assets_main_specifications_matrix', $ins_meter_spec);
                            }

                            // ######################################
                            // PECO SEAL
                            if (trim($qry_meter_db->pecoseal) != '') {
                                $ins_meter_spec = array(
                                    'assetid' => $asset_id,
                                    'specid' => 3095,
                                    'specval' => trim($qry_meter_db->pecoseal)
                                );
                                $this->db->insert('assets_main_specifications_matrix', $ins_meter_spec);
                            }


                            // ######################################
                            // AMPERE
                            if (trim($qry_meter_db->ampere) != '') {
                                $ins_meter_spec = array(
                                    'assetid' => $asset_id,
                                    'specid' => 3096,
                                    'specval' => trim($qry_meter_db->ampere)
                                );
                                $this->db->insert('assets_main_specifications_matrix', $ins_meter_spec);
                            }

                            // ######################################
                            // VOLTS
                            if (trim($qry_meter_db->volts) != '') {
                                $ins_meter_spec = array(
                                    'assetid' => $asset_id,
                                    'specid' => 3097,
                                    'specval' => trim($qry_meter_db->volts)
                                );
                                $this->db->insert('assets_main_specifications_matrix', $ins_meter_spec);
                            }


                            // ######################################
                            // KH
                            if (trim($qry_meter_db->kh) != '') {
                                $ins_meter_spec = array(
                                    'assetid' => $asset_id,
                                    'specid' => 3208,
                                    'specval' => trim($qry_meter_db->kh)
                                );
                                $this->db->insert('assets_main_specifications_matrix', $ins_meter_spec);
                            }

                            // ######################################
                            // DATE ISSUE
                            if (trim($qry_meter_db->dateissue) != '') {
                                $ins_meter_spec = array(
                                    'assetid' => $asset_id,
                                    'specid' => 3210,
                                    'specval' => trim($qry_meter_db->dateissue)
                                );
                                $this->db->insert('assets_main_specifications_matrix', $ins_meter_spec);
                            }
                        }


                        if ($qry_main_check) {

                            $cust_num_erp += 1;
                            $owner_id = $qry_main_check->sysid;
                            $contractdate = date('Y-m-d', strtotime($row->contractdate));
                            $status = ($row->status == 4) ? 0 : 1;

                            if ($asset_ins) {
                                $ins_asset_main_owner = array(
                                    'assetid' => $asset_id,
                                    'ownerid' => $owner_id,
                                    'dateissued' => $contractdate,
                                    'ownertype' => 3,
                                    'createdby' => user_id(),
                                    'updatedby' => user_id(),
                                    'status' => $status
                                );
                                $this->db->insert('assets_main_owner_history', $ins_asset_main_owner);
                                $cust_num_insert += 1;
                            }
                        }
                    }
                }
            }
        }

        $data['customers'] = $cust_num;
        $data['customerserp'] = $cust_num_erp;
        $data['customersassets'] = $cust_num_insert;
        return json_encode($data);
    }

    function get_assets_info() {
        $data = array();

        $number = $this->input->post('number');
        $serial = $this->input->post('serial');


        $qry = $this->db->query("
                SELECT 
                am.sysid,
                am.labels,
                am.serials
                FROM assets_main AS am
                WHERE am.labels = '$number' AND am.serials = '$serial'
           ")->row();

        if($qry) {
            $id = $qry->sysid;
            $info = get_asset_info($id);
            $pictures = get_asset_pic($id);

            $data['qry'] = true;
            $data['acctno'] = $info->acctno;
            $data['pictures'] = $pictures;
            $data['name'] = $info->name;
            $data['address'] = $info->address;
            $data['specs'] = $info->specs;
            $data['dateissued'] = $info->dateissued;
            $data['issuedby'] = $info->issuedby;
        } else {
            $data['qry'] = false;
        }

        return json_encode($data);
    }

    function get_assets_details() {
        $data = array();
        $html = '';

        $html .= '<div class="col-md-4">';
        $html .= '<p class="font-blue-sharp text-bold">Asset Information</p>';
        $html .= '<ul class="list-group summary no-border">';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5">Asset Code</span>';
        $html .= '<span class="label-default col-md-7 number">';
        $html .= '000';
        $html .= '</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5">Descriptions</span>';
        $html .= '<span class="label-default col-md-7 number">';
        $html .= '000';
        $html .= '</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5">Asset Serial</span>';
        $html .= '<span class="label-default col-md-7 number">';
        $html .= '000';
        $html .= '</span>';
        $html .= '</li>';

        $html .= '</ul>';
        $html .= '</div>';

        $html .= '<div class="col-md-4">';
        $html .= '<p class="font-blue-sharp text-bold">Asset Ownership</p>';
        $html .= '<ul class="list-group summary no-border">';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5">Asset Code</span>';
        $html .= '<span class="label-default col-md-7 number">';
        $html .= '000';
        $html .= '</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5">Descriptions</span>';
        $html .= '<span class="label-default col-md-7 number">';
        $html .= '000';
        $html .= '</span>';
        $html .= '</li>';

        $html .= '<li class="list-group-item">';
        $html .= '<span class="label-name col-md-5">Asset Serial</span>';
        $html .= '<span class="label-default col-md-7 number">';
        $html .= '000';
        $html .= '</span>';
        $html .= '</li>';

        $html .= '</ul>';
        $html .= '</div>';

        $data['html'] = $html;
        return json_encode($data);
    }
    function submit_meter_issuance(){
        $data = array();



        $assetno = $this->input->post('assetno');
        $serialno = $this->input->post('serialno');
        $assetbrand = $this->input->post('assetbrand');

        $checkifexistassetandserial =
            $this->db->query("SELECT sysid FROM assets_main
                              WHERE labels = '$assetno' OR serials = '$serialno'")->row();
        if($checkifexistassetandserial){
            $data['msg'] = 'Assets No. / Serial No. already exist!';
            $data['func'] = 'warning';
            $data['qry'] = false;
        }else{
            $insarr = array(
                'labels' => $assetno,
                'serials' => $serialno,
                'types' => 320,
                'brand' => $assetbrand
            );
            $insertmainass = $this->db->insert("assets_main" ,$insarr);
            $lastid  = $this->db->insert_id();

            if($lastid > 0){

                //insert brand history
                $brandarr = array(
                    'assetid' => $lastid,
                    'brandid' => $assetbrand,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("assets_brand_history" , $brandarr);

                $getassetspec = $this->db->select("sysid,names")->from("prime_types_parameter")
                    ->where(array("codes" => 'MISSPEC' , "status" => 1))
                    ->get();
                if($getassetspec->num_rows() > 0){
                    foreach ($getassetspec->result() as $row) {
                        $assetspecval =  $this->input->post($row->sysid);
                        if($assetspecval != ''){
                            $assetspecarr = array(
                                'assetid' => $lastid,
                                'specid' =>  $row->sysid,
                                'specval' =>  $assetspecval,
                                'createdby' => user_id(),
                                'updatedby' => user_id()
                            );
                            $this->db->insert("assets_main_specifications_matrix" , $assetspecarr);
                            $data['errqry'] = $this->db->_error_message();
                        }
                    }
                }
                if($insertmainass){
                    $data['msg'] = 'Asset has been saved.';
                    $data['func'] = 'success';
                    $data['qry'] = true;
                }else{
                    $data['msg'] = 'Failed to save asset.';
                    $data['func'] = 'error';
                    $data['qry'] = false;
                }
            }
        }
        return json_encode($data);
    }
    function get_brands(){
        $data = array();

        $sql = $this->db->select("sysid,codes,descs")->from("prime_brands")
            ->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->descs. ' - '. $row->codes
                );
            }
        }

        return json_encode($data);
    }
    function get_asset_types(){
        $data = array();

        $sql = $this->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1 , "codes" => 'ASSET'))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names. ' - '. $row->codes
                );
            }
        }

        return json_encode($data);
    }
    function upload_asset_pic() {
        $data = array();
        $toupload = 0;
        $uploaded = 0;
        $mtrno = $this->input->post('mtrno');
        $acctid = $this->input->post('acctid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $type = $this->input->post('type');

        $qry = false;
        $msg = '';
        $func = 'error';

        $asset_id = 0;

        $acctid_format = str_pad($acctid, 11, '0', STR_PAD_LEFT);
        $files = $_FILES;

        if($files) {

            $qry_asset = $this->db->select()
                ->from('assets_main')
                ->where(array('labels' => $mtrno, 'status' => 1, 'types' => 320))
                ->get()->row();

            $cpt = count($_FILES['pics']['name']);
            if ($cpt > 0 && $qry_asset) {
                $this->load->library('upload');

                $asset_id = $qry_asset->sysid;

                for ($pi = 0; $pi < $cpt; $pi++) {
                    $toupload += 1;

                    $data['picsarr'][] = 'PI_' . $pi;
                    $date = new DateTime();

                    $new_name = strtolower($acctid_format.'_'.$year.'_'.str_pad($month, 2, '0', STR_PAD_LEFT));


                    if($type == 'all') {
                        $upload_path = FCPATH.'uploads/images/assets/' . $asset_id . '/';
                        $url_path = base_url('uploads/images/assets/' . $asset_id . '/');
                    } else {
                        $upload_path = FCPATH.'uploads/images/assets/' . $asset_id . '/' . $type . '/';
                        $url_path = base_url('uploads/images/assets/' . $asset_id . '/'. $type . '/');
                    }

                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0777, true);
                    }else{
                        chmod($upload_path, 0777);
                    }

                    $_FILES['userfile']['name'] = $files['pics']['name'][$pi];
                    $_FILES['userfile']['type'] = $files['pics']['type'][$pi];
                    $_FILES['userfile']['tmp_name'] = $files['pics']['tmp_name'][$pi];
                    $_FILES['userfile']['error'] = $files['pics']['error'][$pi];
                    $_FILES['userfile']['size'] = $files['pics']['size'][$pi];

                    $this->upload->initialize($this->set_upload_options($upload_path, $new_name));

                    if (!$this->upload->do_upload()) {
                        $data['picmsg'][] = $this->upload->display_errors();
                    } else {
                        $data['picmsg'][] = $this->upload->data();
                        $uploaded += 1;
                    }
                }
            }

            if($uploaded >= $toupload) {
                $qry = true;
                $msg = 'All pictures uploaded!';
                $func = 'success';
            }else{
                if($uploaded>0) {
                    $msg = 'Some files are uploaded!';
                    $func = 'info';
                }else{
                    $msg = 'No file uploaded!';
                    $func = 'warning';
                }
            }
        }else{
            $msg = 'No file selected!';
        }
        $data['assetid'] = $asset_id;
        $data['year'] = $year;
        $data['month'] = $month;
        $data['mtrno'] = $mtrno;
        $data['acctid'] = $acctid;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }
    function get_asset_pics () {
        $data = array();
        $html = '';
        $mtrno = $this->input->post('mtrno');
        $acctno = $this->input->post('acctno');
        $year_input= $this->input->post('year');
        $month_input = $this->input->post('month');
        $type = $this->input->post('type');

        if($year_input && $month_input) {
            $month = $month_input;
            $year = $year_input;
        }else{
            // QUERY FROM EXTERNAL BILLING REPORTS
            $info = get_active_account_info($acctno);
            $qry_billing_ext = $this->db->select('billmo, billyr')
                ->from('billing_reports_ext')
                ->where(array('servno' => $info->servicenumber, 'mtr' => $info->mtr))
                ->order_by('datecreated', 'desc')
                ->get()->row();
            if($qry_billing_ext) {
                $month = $qry_billing_ext->billmo;
                $year = $qry_billing_ext->billyr;
            }
        }



        $qry_asset = $this->db->select()
            ->from('assets_main')
            ->where(array('labels' => $mtrno, 'status' => 1, 'types' => 320))
            ->get()->row();


        if($qry_asset) {

            $asset_id = $qry_asset->sysid;
            $data['assetid'] = $asset_id;

            if($type=='all') {
                $upload_path = FCPATH.'uploads\images\assets\\' . $asset_id . '\\';
                $dir = './uploads/images/assets/'.$asset_id.'/';
                $upload_path = FCPATH . 'uploads/images/assets/'.$asset_id.'/';
                $url_path = base_url('uploads/images/assets/' . $asset_id . '/');
            }else{

                // $upload_path = FCPATH.'uploads\images\assets\\' . $asset_id . '\\' . $type . '\\';
                $dir = './uploads/images/assets/'.$asset_id.'/' . $type . '/';
                $upload_path = FCPATH . 'uploads/images/assets/'.$asset_id.'/' . $type . '/';
                $url_path = base_url('uploads/images/assets/' . $asset_id . '/'. $type . '/');
            }

            // GET FILE PICS
            $home_dir = str_pad($mtrno, 8, '0', STR_PAD_LEFT);
            $file_pic_arr = glob($upload_path . '*.*');

            $admin_link = (user_id() == 1) ? '<a href="#tbl_deleted_mtrpics" data-toggle="ajax-modal" data-arr="./uploads/reading/meter/' . $home_dir . '/' . $year . '/' . $month . '/deleted/*.*" class="text-danger" style="display: inline; width: auto; float: right;"><i class="fa fa-trash-o"></i> Deleted</a>' : '';
            $file_cnt = count($file_pic_arr);

            if ($file_cnt > 0) {
                $html .= '<p>Files attahced: ' . $file_cnt . ' ' . $admin_link . '</p>';
                foreach ($file_pic_arr as $mtr) {
                    $pic_arr = explode('/', $mtr);
                    $pic_name = end($pic_arr);
                    $html .= '<div class="items">';
                    $html .= '<span class="view-text bg-yellow-casablanca bg-font-yellow-casablanca">View</span>';
                    $html .= '<button type="button" data-dir="' . $home_dir . '" data-acct="' . $acctno . '" data-file="' . $pic_name . '" data-year="' . $year . '" data-month="' . $month . '" class="btn btn-danger btn-xs" id="btn_delete"><i class="fa fa-times"></i></button>';
                    $html .= '<a target="_blank" href="' . $url_path . '/' . $pic_name . '" class="fancybox-button">';
                    $html .= '<img class="img-responsive" style="" src="' . $url_path . '/' . $pic_name . '">';
                    $html .= '</a>';
                    $html .= '</div>';
                }
            } else {
                $html .= '<p style="padding-right: 5px;">No file attached! </p>';
            }
        }else{
            $html .= '<p style="padding-right: 5px;">Asset not found </p>';
        }
        $data['html'] = $html;
        return json_encode($data);
    }


    private function set_upload_options($full_path, $new_name)
    {
        //upload an image options
        $config = array();

        $config['upload_path'] = $full_path;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 50000;
        //$config['max_width'] = 4024;
        //$config['max_height'] = 3768;
        //$config['encrypt_name']         = TRUE;
        $config['file_name'] = $new_name;

        return $config;
    }
    function get_mis_rem_types(){
        $data = array();

        $sql = $this->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1 , "codes" => 'MISREMTYPE'))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names.' - '.$row->codes
                );
            }
        }

        return json_encode($data);
    }
    function submit_remarks(){
        $data = array();
        $remarksdataid = $this->input->post('remarksdataid');
        $remarkstype = $this->input->post('remarkstype');
        $remarkstxt = $this->input->post('remarkstxt');

        $this->db->trans_begin();

        $insarr = array(
            'assetid' => $remarksdataid,
            'typesid' => $remarkstype,
            'remarks' => $remarkstxt,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $sql = $this->db->insert("assets_remarks" , $insarr);
        $data['qryerr'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $data['msg'] = "Asset remarks has been saved.";
            $data['func'] = "success";
            $data['qry'] = true;
            $data['dataid'] = $remarksdataid;
        }else{
            $this->db->trans_rollback();
            $data['msg'] = "Failed to add asset remarks.";
            $data['func'] = "error";
            $data['qry'] = false;
        }

        return json_encode($data);
    }
    function get_remarks_table(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $getremarks = $this->db->query("SELECT
ar.remarks,
ar.datecreated,
ptp.`names`
FROM
assets_remarks AS ar
INNER JOIN prime_types_parameter AS ptp ON ar.typesid = ptp.sysid
WHERE
ar.assetid = $dataid
ORDER BY ar.datecreated DESC
");
        if($getremarks->num_rows() > 0){
            foreach ($getremarks->result() as $row){
                $data['remarksdata'][] = array(
                    'name' => $row->names,
                    'remarks' => $row->remarks,
                    'datecreated' => $row->datecreated,
                );
            }
        }

        return json_encode($data);
    }
    function edit_info(){
        $data = array();
        $input = $this->input->post();
        $inputname = $input['name'];
        $ids = $input['pk'];
        $val = $input['value'];

        if($inputname == 'brand'){
            $getexistbrand = $this->db->select("brand")->from("assets_main")
                ->where(array("sysid" => $ids , "status" => 1))->get()->row();
            if($getexistbrand){
                $brandhistarr = array(
                    'assetid' => $ids,
                    'brandid' => $getexistbrand->brand,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );

                $this->db->insert("assets_brand_history" , $brandhistarr);
            }
            $updatebrandarr = array(
                'brand' => $val,
                'updatedby' => user_id()
            );
            $this->db->where(array("sysid" => $ids));
            $this->db->update("assets_main" , $updatebrandarr);
        }

        $checktypes = $this->db->select("amsm.sysid , amsm.specval , ptp.names")->from("assets_main_specifications_matrix as amsm")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = amsm.specid" , "left")
            ->where(array("ptp.codes" => 'MISSPEC' , "amsm.specid" =>$inputname , "amsm.assetid" => $ids , "amsm.status" => 1))
            ->get()->row();
        if($checktypes){
            $udpatearr = array(
                'updatedby' => user_id(),
                'status' => 0
            );
            $data['idtoupdate'] = $checktypes->sysid;
            $this->db->where(array("sysid" => $checktypes->sysid));
            $this->db->update("assets_main_specifications_matrix" , $udpatearr);

            $insarr = array(
                'assetid' =>  $ids,
                'specid' =>  $inputname,
                'specval' =>   $val,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'status' => 1
            );
            $this->db->insert("assets_main_specifications_matrix" , $insarr);

            $remarksinsarr = array(
                'assetid' => $ids,
                'typesid' => $inputname,
                'remarks' => $checktypes->names.' value has been changed from '.$checktypes->specval.' to '.$val.' .',
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'status' =>  1
            );
            $this->db->insert("assets_remarks" , $remarksinsarr);
            $data['dataid'] = $ids;

        }
        return json_encode($data);
    }
    function get_assets_specifications(){
        $data = array();
        $dataid = $this->input->post('dataid');

        $sql = $this->db->select("amsm.specid , ptp.names")->from("assets_main_specifications_matrix as amsm")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = amsm.specid" , "left")
            ->where(array("amsm.assetid" => $dataid , "amsm.status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['specid'][] = array(
                    'val' => $row->specid,
                    'names' => $row->names
                );
            }
        }

        return json_encode($data);
    }
    function add_new_brands(){
        $data = array();
        $brandcodes = $this->input->post('brandcodes');
        $brandname = $this->input->post('brandname');

        $insarr = array(
            'codes' => $brandcodes,
            'descs' => $brandname,
            'status' => 1
        );

        $sql = $this->db->insert("prime_brands" , $insarr);
        if($sql){
            $data['msg'] = "Brand has been added.";
            $data['func'] = "success";
            $data['qry'] = true;
        }else{
            $data['msg'] = "Failed to add brand.";
            $data['func'] = "error";
            $data['qry'] = false;
        }

        return json_encode($data);
    }

    function release_status() {
        $data = array();
        $msg = 'Query Error!';
        $func = 'error';
        $qry = false;


        $assetid = $this->input->post('assetid');
        $assetarr = $this->input->post('assetsarr');

        $this->db->trans_begin();
        if($assetarr && count($assetarr) > 0) {
            $data['array'] = true;
            foreach($assetarr as $ar) {

                $this->db->where(array('sysid' => $ar));
                $this->db->update('assets_main', array('status' => 3202, 'updatedby' => user_id()));

            }
        }else {
            $this->db->where(array('sysid' => $assetid));
            $this->db->update('assets_main', array('status' => 3202, 'updatedby' => user_id()));

        }

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $msg = 'Asset has been release!';
            $func = 'success';
            $qry = true;
        }

        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }


    function upload_mis_data()
    {
        $data = array();
        $msg = '';
        $qry = false;
        $func = 'error';
        $num_exist = 0;
        $num_not_exist = 0;
        $totalrecords = 0;


        if(isset($_FILES["datafile"])) {

            $file_info = pathinfo($_FILES["datafile"]["name"]);
            $file_directory =  FCPATH . "uploads/temp/";

            $qry_time = $this->db->query("SELECT HOUR(NOW()) AS HRS, MINUTE(NOW()) AS MIN, SECOND(NOW()) AS SEC")->row();
            $hrs = str_pad($qry_time->HRS, 2, '0', STR_PAD_LEFT);
            $min = str_pad($qry_time->MIN, 2, '0', STR_PAD_LEFT);
            $sec = str_pad($qry_time->SEC, 2, '0', STR_PAD_LEFT);
            $hour_num = $hrs.$min.$sec;

            $temp = explode(".", $_FILES["datafile"]["name"]);
            $newfilename = 'MIS_'.date('Y').str_pad(date('m'), 2, '0', STR_PAD_LEFT).str_pad(date('d'), 2, '0', STR_PAD_LEFT).$hour_num.'.' . end($temp);


            // CREATE DIRECTORY
            $config['overwrite'] = TRUE;
            $config['upload_path'] = $file_directory;
            $config['allowed_types'] = '*';
            $config['max_size'] = 100000;
            $config['max_width'] = 5000;
            $config['max_height'] = 8000;
            $config['encrypt_name'] = FALSE;
            $config['file_name'] = $newfilename;
            $this->load->library('upload', $config);


            if (!is_dir( FCPATH . "uploads/temp")) {
                mkdir( FCPATH . "uploads/temp", 0755, TRUE);
                chmod( FCPATH . "uploads/temp", 0755);
            }


            if ($this->upload->do_upload('datafile')) {

                $filetype = $file_info["extension"];
                if ($filetype== 'xls' || $filetype == 'xlsx') {

                }else{
                    if (strtoupper($file_info["extension"]) == 'DBF') {

                        $dbf_arr = echo_dbf($file_directory . $newfilename);
                        if (count($dbf_arr) > 0) {
                            foreach ($dbf_arr['rec'] as $row) {
                                $totalrecords++;

                                $mtrno = trim($row['METER_NO']);
                                $mtrser = trim($row['SERIAL_NO']);
                                $type = (isset($row['TYPE']))? $row['TYPE'] : NULL;
                                $brand = (isset($row['MAKE']))? $row['MAKE'] : NULL;
                                $amps = (isset($row['AMPERE']))? $row['AMPERE'] : NULL;
                                $volts = (isset($row['VOLTS']))? $row['VOLTS'] : NULL;
                                $kh = (isset($row['KH']))? $row['KH'] : NULL;
                                $ercseal = (isset($row['ERC_SEAL']))? $row['ERC_SEAL'] : NULL;
                                $pecoseal = (isset($row['PECO_SEAL']))? $row['PECO_SEAL'] : NULL;
                                $smemo = (isset($row['S_MEMO']))? $row['S_MEMO'] : NULL;
                                $mult = (isset($row['MULT']))? $row['MULT'] : NULL;
                                $reading =  (isset($row['READING']))? $row['READING'] : NULL;
                                $wiresize =  (isset($row['WIRESIZE']))? $row['WIRESIZE'] : NULL;
                                $date =  (isset($row['DATE']))? $row['DATE'] : NULL;

                              /*  $qry_main_check = $this->db->select('sysid, dateconnected, status')
                                    ->from('customer_accounts_main')
                                    ->where(array('mtrno' => $mtrno, 'mtrserial' => $mtrser))
                                    ->get()->row(); */


                              $data['meter_serial_list'][] = array(
                                  'meterno' => $mtrno,
                                  'meterserial' => $mtrser
                              );

                                $check_mtr_db = $this->db->select('sysid, labels, serials')
                                    ->from('assets_main')
                                    ->where(array('labels' => $mtrno, 'serials' => $mtrser))
                                    ->get()->row();

                                if ($check_mtr_db) {

                                    $this->db->query("UPDATE assets_main_owner_history SET status = 0 WHERE assetid = {$check_mtr_db->sysid}");

                                    $num_exist += 1;
                                    $asset_id = $check_mtr_db->sysid;

                                    $date=date_create($date);

                                    $updatearr = array(
                                        'dateupdated' => date_format($date,"Y-m-d H:i:s"),
                                        'updatedby' => user_id()
                                    );
                                    $this->db->where(array("sysid" => $check_mtr_db->sysid));
                                    $this->db->update("assets_main" , $updatearr);
                                    $data['existing'][] = array(
                                        'mtrno' => $check_mtr_db->labels,
                                        'serials' => $check_mtr_db->serials,
                                        'where_sysid' => $check_mtr_db->sysid,
                                        'dateval' => date_format($date,"Y-m-d H:i:s")
                                    );
                                }else{
                                    $data['not_existing'][] = array(
                                        'mtrno' => $mtrno,
                                        'serials' => $mtrser
                                    );
                                    $num_not_exist += 1;
                                    $get_brand_id = $this->db->select('sysid')
                                        ->from('prime_brands')
                                        ->like('descs', $brand)
                                        ->get()->row();
                                    $brand_id = ($get_brand_id) ? $get_brand_id->sysid : 0;

                                    $date = date_formating($date, 'Ymd', 'Y-m-d');



                                    $this->db->insert('assets_main', array(
                                        'labels' => $mtrno,
                                        'serials' => $mtrser,
                                        'brand' => $brand_id,
                                        'types' => 320,
                                        'createdby' => user_id(),
                                        'updatedby' => user_id(),
                                        'datecreated' => $date.' 00:00:00',
                                        'dateupdated' => $date.' 00:00:00',
                                        'status' => 1,
                                    ));
                                    $asset_id = $this->db->insert_id();
                                    $data['error_qry'] = $this->db->_error_message();
                                    $data['dateupdate'][] = array(
                                        'dateupdated' => $date.' 00:00:00',
                                        'datecreated' => $date.' 00:00:00'
                                    );
                                }
                                $user_id = user_id();

                                if($asset_id>0) {

                                   /* if($qry_main_check) {

                                        $acct_stat      = $qry_main_check->status;
                                        $acct_id        = $qry_main_check->status;
                                        $contractdate   = $qry_main_check->dateconnected;

                                        if($acct_id > 0) {
                                            $ins_asset_main_owner = array(
                                                'assetid' => $asset_id,
                                                'ownerid' => $acct_id,
                                                'dateissued' => $contractdate,
                                                'ownertype' => 3,
                                                'createdby' => user_id(),
                                                'updatedby' => user_id(),
                                                'status' => $acct_stat
                                            );
                                            $this->db->insert('assets_main_owner_history', $ins_asset_main_owner);
                                        }
                                    } */



                                    // ERC SEAL
                                    if(trim($ercseal) != '' && isset($ercseal)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3094");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3094, 'specval' => $ercseal, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // PECO SEAL
                                    if(trim($pecoseal) != '' && isset($pecoseal)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3095");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3095, 'specval' => $pecoseal, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // AMPS
                                    if(trim($amps) != '' && isset($amps)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3096");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3096, 'specval' => str_replace('A', '', $amps), 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // VOLTS
                                    if(trim($volts) != '' && isset($volts)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3097");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3097, 'specval' => str_replace('V', '', $volts), 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // TYPE
                                    if(trim($type) != '' && isset($type)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3098");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3098, 'specval' => $type, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // KH
                                    if(trim($kh) != '' && isset($kh)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3208");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3208, 'specval' => $kh, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // SMEMO
                                    if(trim($smemo) != '' && isset($smemo)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3209");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3209, 'specval' => $smemo, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // MULT
                                    if(trim($mult) != '' && isset($mult)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3214");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3214, 'specval' => $mult, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }

                                    // READING
                                    if(trim($reading) != '' && isset($reading)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3206");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3206, 'specval' => $reading, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }
                                    // WIRE SIZE
                                    if(trim($wiresize) != '' && isset($wiresize)) {
                                        $this->db->query("UPDATE assets_main_specifications_matrix SET status = 0, updatedby = $user_id WHERE assetid = $asset_id AND specid = 3207");
                                        $this->db->insert('assets_main_specifications_matrix', array(
                                            'assetid' => $asset_id, 'specid' => 3207, 'specval' => $wiresize, 'createdby' => $user_id, 'updatedby' => $user_id
                                        ));
                                    }
                                }

                            }
                        }
                    }
                }

                $msg = 'Moved!';
                $qry = true;
                $func = 'success';
            } else {
                $msg = 'Moved Error: ' . $this->upload->display_errors('<p>', '</p>');
                $qry = false;
                $func = 'warning';

            }

            $data['filename'] = $newfilename;
        }else{
            $msg = 'File not found!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;

        $data['exist'] = $num_exist;
        $data['not_exist'] = $num_not_exist;
        $data['totalrec'] = $totalrecords;

        return json_encode($data);

    }

    function deactivate_asset() {
        $this->db->trans_begin();
        $id = $this->input->post('id');
        $this->db->where(array('sysid' => $id));
        $this->db->update('assets_main', array('status' => 0));
        $data = db_trans($this->db);
        return json_encode($data);
    }
    function submit_row_reading(){
        $this->db->trans_begin();
        $asset_id = $this->input->post('asset_id');
        $reading = $this->input->post('reading');

        $this->db->where(array("assetid" => $asset_id , "status" => 1 , "specid" => 3206));
        $this->db->update("assets_main_specifications_matrix" , array("status" => 0 , "updatedby" => user_id()));

        $insarr = array(
            'assetid' => $asset_id,
            'specid' => 3206,
            'specval' => $reading,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $this->db->insert("assets_main_specifications_matrix" , $insarr);
        $data['err'] = $this->db->_error_message();
        $data = db_trans($this->db);

        return json_encode($data);
    }
    function submit_row_mult(){
        $this->db->trans_begin();
        $asset_id = $this->input->post('asset_id');
        $mult = $this->input->post('mult');

        $this->db->where(array("assetid" => $asset_id , "status" => 1 , "specid" => 3214));
        $this->db->update("assets_main_specifications_matrix" , array("status" => 0 , "updatedby" => user_id()));

        $insarr = array(
            'assetid' => $asset_id,
            'specid' => 3214,
            'specval' => $mult,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $this->db->insert("assets_main_specifications_matrix" , $insarr);
        $data['err'] = $this->db->_error_message();
        $data = db_trans($this->db);

        return json_encode($data);
    }
    function submit_row_volts(){
        $this->db->trans_begin();
        $asset_id = $this->input->post('asset_id');
        $volts = $this->input->post('volt');

        $this->db->where(array("assetid" => $asset_id , "status" => 1 , "specid" => 3097));
        $this->db->update("assets_main_specifications_matrix" , array("status" => 0 , "updatedby" => user_id()));

        $insarr = array(
            'assetid' => $asset_id,
            'specid' => 3097,
            'specval' => $volts,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $this->db->insert("assets_main_specifications_matrix" , $insarr);
        $data['err'] = $this->db->_error_message();
        $data = db_trans($this->db);

        return json_encode($data);
    }
    function submit_row_wiresize(){
        $this->db->trans_begin();
        $asset_id = $this->input->post('asset_id');
        $wiresize = $this->input->post('wiresize');

        $this->db->where(array("assetid" => $asset_id , "status" => 1 , "specid" => 3207));
        $this->db->update("assets_main_specifications_matrix" , array("status" => 0 , "updatedby" => user_id()));

        $insarr = array(
            'assetid' => $asset_id,
            'specid' => 3207,
            'specval' => $wiresize,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $this->db->insert("assets_main_specifications_matrix" , $insarr);
        $data['err'] = $this->db->_error_message();
        $data = db_trans($this->db);

        return json_encode($data);
    }

    function save_mts_reading() {
        $data = array();

        $assetid = $this->input->post('assetid');
        $reading = $this->input->post('reading');
        $acctid = $this->input->post('acctid');
        $reading_dt = $this->input->post('datereturned');
        $tfdo = $this->input->post('tfdo');
        $mtr = $this->input->post('mtr');
        $servno = $this->input->post('acctsearch');
        $this->db->trans_begin();

        // GET acctid base sa assetid in asset_owner_history status 1
        // GET mtrno based sa assetid -> labels
        // INSERT TO reading values (acctid, labels -> mtrid, types => 3102)

        $sql_am = $this->db->select()->from("assets_main")
            ->where(array("sysid" => $assetid))->get()->row();
        $mtr_no = $sql_am->labels;
        $mtr_serial = $sql_am->serials;

        // #############################################################################
        // insert the new owner of the asset based on the submited acctid from mts

        // UPDATE FIRST
        // UPDATE assets_main_owner_history all status to zero where assetid is equal to $assetid
        $this->db->query("UPDATE assets_main_owner_history SET status = 0 WHERE assetid = $assetid AND status != 0");


        // #############################################################################
        // NEXT INSERT THE NEW owner based on the submitted acctid and tag to $assetid
        $ins_owner_arr = array(
            'assetid' => $assetid,
            'ownerid' => $acctid,
            'updatedby' => user_id(),
            'createdby' => user_id()
        );
        $this->db->insert('assets_main_owner_history', $ins_owner_arr);

        $insert_array = array(
            'acctid' => $acctid,
            'reading' => $reading,
            'types' => 3102,
            'readingdate' => $reading_dt,
            'mtrid' => $mtr_no,
            'createdby' => user_id()
        );

        $this->db->insert('customer_accounts_subscription_meter_reading', $insert_array);
        $data['customer_accounts_subscription_meter_reading'] = $this->db->_error_message();

        // #############################################################################
        // CHECK IF tfdo is true THEN UPDATE customer_accounts_main status to zero (0)
        if($tfdo && $tfdo == 1 && $servno != '' && $mtr != '') {
            $this->db->query("UPDATE customer_accounts_main SET status = 0 WHERE sysid = $acctid");
            // UPDATE PECO APPS
            $conn_dev = $this->load->database('pecoapps', TRUE);
            $conn_dev->initialize();
            $conn_dev->trans_begin();
            $conn_dev->query("
                    UPDATE father
                    SET
                        oldrdg____ = '$reading',
                        stadte____ = '$reading_dt',
                        pmtrsl____ = '$mtr_no',
                        status____ = 4
                    WHERE servno____ = '$servno' AND mtr_______ = $mtr
                ");
            if($conn_dev->trans_status() == true) {
                $conn_dev->trans_commit();
                $data['father'] = 'Updated: ' . $servno . ' - ' . $mtr;
            }else{
                $conn_dev->trans_rollback();
            }
        }

        $data = db_trans($this->db);


        //$data['acctid'] = $ownerid;
        $data['reading'] = $reading;
        $data['readingdate'] = $reading_dt;
        $data['mtrid'] = $mtr_no;
        return json_encode($data);

    }

    function tbl_meter_readings(){
        $data = array();

        $date_ret = $this->input->post('datereturned');

        if(user_id() > 0) {
            if($date_ret != '') {
                $sql_rdg = $this->db->query(" SELECT 
                    -- casmr.sysid,
                    casmr.mtrid,
                    am.serials,
                    casmr.acctid
                    -- casmr.reading,
                    -- casmr.readingdate ,
                    -- casmr.createdby
                    FROM `customer_accounts_subscription_meter_reading` AS casmr
                    LEFT JOIN customer_accounts_main AS c ON c.sysid = casmr.acctid
                    LEFT JOIN assets_main AS am ON am.labels = c.mtrno AND am.serials = c.mtrserial                    WHERE CAST(casmr.readingdate AS DATE) = '$date_ret'
                    AND casmr.types = 3102 
					GROUP BY
                    casmr.mtrid,
                    am.serials,
                    casmr.acctid,
                    casmr.readingdate
                    ORDER BY casmr.readingdate DESC
				
                ");
                if($sql_rdg->num_rows()>0) {
                    foreach($sql_rdg->result() as $row) {
                        $name = 'N/A';
                        $servno = 'N/A';
                        if($row->acctid != '') {
                            $acct_info = get_active_account_info($row->acctid);
                            if($acct_info) {
                                $name = $acct_info->name;
                                $servno = $acct_info->servicenumber;
                            }
                        }

                        $num = 1;

                        $qry_latest_reading = $this->db->select('sysid, reading, readingdate, createdby')
                            ->from('customer_accounts_subscription_meter_reading')
                            ->where(array('acctid' => $row->acctid, 'mtrid' => $row->mtrid))
                            ->order_by('datecreated', 'desc')
                            ->get()->row();
                        $reading = ($qry_latest_reading) ? $qry_latest_reading->reading : 0;
                        $user_info = ($qry_latest_reading) ? get_users_info($qry_latest_reading->createdby) : 'None';
                        $readingid = ($qry_latest_reading) ? $qry_latest_reading->sysid : 0;
                        $readingdate = ($qry_latest_reading) ? $qry_latest_reading->readingdate : 0;

                        $data['list'][] = array(
                            'num'               => $num++,
                            'expand'            => btn_expand($readingid),
                            'mtrno'             => $row->mtrid,
                            'serial'            => $row->serials,
                            'servno'            => $servno,
                            'ownername'         => $name,
                            'encodedby'         => $user_info->lastname,
                            'encodeddate'       => $readingdate,
                            'reading'           => number_format($reading)
                        );
                    }
                }else{
                    $data = query_msg(false, 'info','No data found!');
                }
            }else{
                $data = query_msg(false, 'info','Fill-in the date returned!');
            }
        } else {
            $data = query_session_res();
        }

        return json_encode($data);
    }

    function tbl_meter_readings_details() {



        $data['html'] = '<h2>Test output</h2>';
        return json_encode($data);
    }

    function select2_stocks() {
        $data = array();

        $sql_stocks = $this->db->query("
            
            SELECT
                ims.serials,
                ims.names AS `desc`,
                supp.descs AS supplier,
                ism.itemid,
                ism.brandid,
                ism.qty,
                ism.price,
                ism.purchasedate,
                ism.sysid 
            FROM
                inventory_stocks_main AS ism
                LEFT JOIN items_main_spec AS ims ON ims.sysid = ism.itemid
                LEFT JOIN inventory_suppliers AS supp ON ism.suppid = supp.sysid
                WHERE ism.`status` = 1
        ");
        if($sql_stocks->num_rows()>0) {
            foreach ($sql_stocks->result() as $row) {

                $brand_sql = get_types_name($row->brandid);
                $brand = ($brand_sql) ? $brand_sql->names : 'Unknown';
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->sysid . ' - ' . $row->desc . ' - ' . $brand
                );
            }
        }
        return json_encode($data);
    }

}
