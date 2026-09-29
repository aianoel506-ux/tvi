<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_cad extends CI_Model {



    function del_services_fee() {
        $data = array();
        $id = $this->input->post('id');
        $this->db->trans_begin();
        $upd_arr = array('status' => 0);
        $this->db->where('sysid', $id);
        $this->db->update('application_customers_charges', $upd_arr);
        if($this->db->trans_status()===true) {
            $qry = true;
            $this->db->trans_commit();
        }else{
            $qry = false;
            $this->db->trans_rollback();
        }
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_new_connection_lists() {
        $data = array();

        $route = $this->input->post('route');
        $district = $this->input->post('district');

        $stages_ids = array();
        $where = '';


        $app_flow_ids_arr = flow_id_arr('APPLICATIONS');
        $app_flow_ids = ($app_flow_ids_arr) ? implode(',', $app_flow_ids_arr) : false;
        $where_trails_last = ($app_flow_ids_arr) ? " AND rm.flowid IN ($app_flow_ids) " : "";
        $where_stages = ($app_flow_ids_arr) ? " AND flowid IN ($app_flow_ids) " : "";
        $data['traillast'] = $where_trails_last;

        if($route && $route > 0) {

            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE levels = $route AND `status` = 1 $where_stages
                ");


            if($sql_stages->num_rows()>0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        }
        if($district && $district > 0) {
            $where .= ' AND cd.distid = ' . $district;
        }


        $qry_details = $this->db->query("
            SELECT 
            cd.sysid, 
            cd.rateclassid, 
            rmt.trnid, 
            rmt.stageid, 
            cd.essrno, 
            cd.datecreated, 
            cd.personid, 
            cd.status, 
            cd.apptype
            FROM application_customers_details AS cd 
            INNER JOIN transaction_request_main_trails AS rmt ON rmt.dataid = cd.sysid 
            WHERE rmt.`status` = 1 AND cd.`status` > 0 $where
            GROUP BY cd.sysid, cd.rateclassid, rmt.trnid, rmt.stageid, cd.essrno, cd.datecreated, cd.personid, cd.apptype
        ");


        /*
        $qry_details = $this->db->query("
                SELECT 
                cd.sysid, 
                cd.essrno, 
                cd.personid, 
                cd.apptype, 
                cd.rateclassid, 
                cd.datecreated, 
                cd.`status`
                FROM application_customers_details AS cd 
                INNER JOIN transaction_request_main_trails AS rmt ON rmt.dataid = cd.sysid 
                $where
                GROUP BY cd.sysid, cd.essrno, cd.personid, cd.apptype, cd.rateclassid, cd.datecreated, cd.`status`
                ORDER BY cd.essrno
            ");
        */

        $data['sql'] = $this->db->last_query();

        if($qry_details->num_rows()>0) {
            foreach($qry_details->result() as $row) {
                $appid = $row->sysid;
                $reqtrnid = $row->trnid;
                $stageid = $row->stageid;
                $rateclass = $row->rateclassid;
                $personid = $row->personid;
                $apptype = $row->apptype;
                $rateclassname = ($rateclass > 0) ? '<a class="tooltips" href="javascript:;" title="'.get_rateclass_name($rateclass).'" data-content="body" data-placement="right" style="font-size: 18px; font-weight: bold;">'.get_rateclass_name($rateclass)[0].'</a>' : '<code>N/A</code>';

                $info = get_person_info($personid);
                $middle_in = (isset($info->info->middlename[0])) ? $info->info->middlename[0] : '';



                // GET CORP INFO
                $qry_corp_app = $this->db->select()
                    ->from('application_customers_corporation')
                    ->where(array('appid' => $appid, 'types' => $apptype))
                    ->get()->row();


                $pic_recent = base_url() . 'assets/global/img/person_default.jpg';
                $pic_id = $personid;
                $pic_dir = 'person';



                $name = ($info->qry) ?  '<span  class="font-green bold">' . $info->info->lastname . ', ' . $info->info->firstname . ' ' . $middle_in . '</span>' : 'Unknown';
                if($row->apptype == 2 && $qry_corp_app) {

                    $pic_dir = 'corporation';
                    $pic_id = $qry_corp_app->corpid;


                    $qry_corp = $this->db->select(
                        'c.descs, cb.names AS branch'
                    )
                        ->from('application_customers_corporation AS acc')
                        ->join('corporation AS c', 'c.sysid = acc.corpid')
                        ->join('corporation_branches AS cb', 'cb.sysid = acc.branchid','left')
                        ->where(array('acc.appid' => $row->sysid))
                        ->get()->row();
                    if($qry_corp) {
                        $branch = (trim($qry_corp->branch) != '') ? ' - ' .$qry_corp->branch : '';
                        $name = '<span  class="font-red-flamingo bold">' . $qry_corp->descs . $branch . '</span>';
                        if($info->qry && trim($info->info->lastname) != '') {
                            $name .= '<br><span style="font-weight: normal; font-size: 13px; line-height: 15px; color: #03a9fc;">' . (($info->qry) ? $info->info->lastname . ', ' . $info->info->firstname . ' ' . $middle_in : 'Unknown') . '</span>';
                        }
                        $name .= '<i class="fa fa-star" style="position: absolute; right: 5px; top: 5px; color: #fca503;"></i>';
                    }
                }

                if($row->apptype == 3 && $qry_corp_app) {
                    if($qry_corp_app) {
                        $gov_arr = get_government_info($qry_corp_app->corpid);

                        $pic_dir = 'government';
                        $pic_id = $qry_corp_app->corpid;

                        $branch = (trim($gov_arr->info->names) != '') ? ' - ' . $gov_arr->info->names : '';
                        $name = '<span  class="font-red-flamingo bold">' . $gov_arr->info->descs . $branch . '</span>';
                        if ($personid > 0 && trim($info->info->lastname) != '') {
                            $name .= '<br><span style="font-weight: normal; font-size: 13px; line-height: 15px; color: #03a9fc;">' . (($info->qry) ? $info->info->lastname . ', ' . $info->info->firstname . ' ' . $middle_in : 'Unknown') . '</span>';
                        }
                        $name .= '<i class="fa fa-star" style="position: absolute; right: 5px; top: 5px; color: #fca503;"></i>';
                    }
                }


                $pic_recent = get_owner_pic($pic_id, $pic_dir, 2);



                $comment_cnt = '';
                $comment_msg = '';
                $qry_comments_cnt = $this->db->select('count(tc.trnid) AS cnt')
                    ->from('transaction_request_trails_comments AS tc')
                    ->where(array('tc.trnid' => $reqtrnid, 'status' => 1))
                    ->get()->row();
                if($qry_comments_cnt && $qry_comments_cnt->cnt>0) {

                    $qry_comments_msg = $this->db->select('remarks')
                        ->from('transaction_request_trails_comments AS tc')
                        ->where(array('tc.trnid' => $reqtrnid, 'status' => 1))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();
                    $comment_msg = ($qry_comments_msg) ? $qry_comments_msg->remarks : '';
                    $max_length = 45;

                    if (strlen($comment_msg) > $max_length)
                    {
                        $offset = ($max_length - 3) - strlen($comment_msg);
                        $comment_msg = substr($comment_msg, 0, strrpos($comment_msg, ' ', $offset)) . ' ...';
                    }
                    $comment_cnt = '<span class="badge badge-danger pull-right" style="margin-left: 5px;">'.$qry_comments_cnt->cnt.'</span>';
                }

                $creation_date = '';
                $qry_trails_last = $this->db->query("
                    SELECT rm.sysid AS trnmid, rmt.sysid, rmt.datecreated, rmt.createdby, rmt.stageid, rmt.dataid, rmt.datecreated AS logdate
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    WHERE rmt.dataid = $appid 
                    AND rmt.`status` = 1
                    $where_trails_last
                    ORDER BY rmt.datecreated DESC
                ")->row();


                $show = true;
                if($route && $route > 0) {
                    if($qry_trails_last && $qry_trails_last->stageid != $stageid) {
                        $show = false;
                    }
                }

                $trn_name = 'Unknown';
                $updated_date = 'None';
                $button = '';
                $from_created_by = 'None';


                if($qry_trails_last) {
                    $creation_date = $row->datecreated;
                    $updated_date = $qry_trails_last->datecreated;

                    $user_info = get_users_info($qry_trails_last->createdby);
                    $from_created_by = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : '';


                    $trn_name = '<a href="javascript:;" title="Current" class="label label-info">C</a> ' . get_trail_name($qry_trails_last->stageid);
                    $button .= '<div class="btn-group btn-xs">';
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, false, '_blank');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'task', '_blank');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'profile', '_blank');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'comments', '_blank');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'back');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'send');

                    $button .= '</div>';

                }

                $trn_elapse = time_elapsed_diff($creation_date, $updated_date, true);
                $ovr_elapse = time_elapsed_diff($creation_date, date('Y-m-d h:m:s'));

                $time = $row->datecreated . '<br><small class="text-info">' . timeago($row->datecreated, sql_time()->DATETIME).'</small>';
                $time_updated = $updated_date . '<br><small class="text-info">' . timeago($updated_date, sql_time()->DATETIME).'</small>';




                $details = '';
                $details .= '<div class="media" style="width: 300px; display: inline-block; margin: 0px 0px; margin-right: 10px;">';
                $details .= '<a class="pull-left row-pic" title="" data-container=\'body\' data-trigger=\'hover\' data-content="<img style=\'width: 200px; height: 200px;\' src=\''.$pic_recent.'\' />" href="javascript:;">';
                $details .= '<img class="media-object" src="'.$pic_recent.'" alt="32x32" style="width: 30px; height: 30px; margin: 2px 2px;">';
                $details .= '</a>';
                $details .= '<div class="media-body" style="font-size: 16px;">'.$name.'</div>';
                $details .= '</div>';

                if($row->status==1) {
                    $status = 'Pending';
                }else{
                    $status = get_types_label_format($row->status);
                }

                if($show) {
                    $essrno = ($row->essrno>0) ? $row->essrno : $row->sysid;
                    $data['data'][] = array(
                        'expand' => btn_expand($appid),
                        'essrno' => '<h4 class="text-danger bold" style="margin: 0px 0px;">' .'PAE'.str_pad($essrno,6,'0',STR_PAD_LEFT). ' </h4> ',
                        'created' => $time,
                        'rateclass' => $rateclassname,
                        'from' => $from_created_by,
                        'updated' => $time_updated,
                        'dataid' => '',
                        'origid' => '',
                        'details' => $details,
                        'control' => $button,
                        'trn' => $trn_name,
                        'status' => $status,
                        'remarks' => $comment_msg . $comment_cnt
                    );
                }
            }
        }
        return json_encode($data);
    }

    function set_status() {
        $dataid = $this->input->post('dataid');
        $status = $this->input->post('status');
        $this->db->trans_begin();
        $this->db->update('application_customers_details',
            array('status' => $status),
            array('sysid' => $dataid)
        );
        return json_encode(db_trans($this->db));
    }

    function get_legacy_apt() {
        $data = array();

        return json_encode($data);
    }


    function save_new_application() {

        // ################################################
        // VARIABLES

        $personid = 0;
        $insert = false;
        $qry = false;
        $msg = false;
        $data = array();
        $func = 'error';
        $inputs_arr = $this->input->post();
        $err_msg = array();
        $insert_qry = array();


        $stagelevel = $this->input->post('stagelevel');
        $moduleid = $this->input->post('moduleid');
        $encodestart = $this->input->post('encodestart');
        $apptype = $this->input->post('apptype');
        $jobtype = $this->input->post('jobtype');
        $essrno = $this->input->post('tssr');
        $lastname = $this->input->post('lastname');
        $firstname = $this->input->post('firstname');
        $middlename = $this->input->post('middlename');
        $suffix = $this->input->post('suffix');
        $prefix = $this->input->post('prefix');
        $gender = $this->input->post('gender');
        $birthdate = $this->input->post('birthdate');
        $marital = $this->input->post('marital');
        $phone = $this->input->post('phone');
        $mobile = $this->input->post('mobile');
        $email = $this->input->post('email');

        $country = $this->input->post('country');
        $region = $this->input->post('region');
        $province = $this->input->post('province');
        $addrcity = $this->input->post('city');

        $addrdistrict = $this->input->post('addrdistrict');
        $addrspecific = $this->input->post('addrspecific');
        $tinno = $this->input->post('tin');
        $sss = $this->input->post('sss');

        $brgy = $this->input->post('brgy');

        $acctrate = $this->input->post('accttype');


        $accttype = $this->input->post('accttype');
        $ownertype = $this->input->post('ownertype');
        $paytype = $this->input->post('paytype');

        $loctype = $this->input->post('loctype');
        $acctreq = $this->input->post('acctreq');

        $googlemap = $this->input->post('googlemap');

        $parnerfname = $this->input->post('parnerfname');
        $checkcust = $this->input->post('checkcust');
        $custphone = $this->input->post('custphone');
        $custmobile = $this->input->post('custmobile');
        $custemail = $this->input->post('custemail');
        $custaddrcity = $this->input->post('custaddrcity');
        $custaddrdistrict = $this->input->post('custaddrdistrict');
        $custcountry = $this->input->post('custcountry');

        $custcountry = $this->input->post('');

        //INPUT EXISTING ACCOUNTS
        $acctra = $this->input->post('acctra');
        $acctex = $this->input->post('acctex');


        $this->db->trans_begin();

        //$person_qry = create_person_data();

        /*
        $person_qry = $this->db->select('sysid')->from('person p')
            ->where(array(
                'lastname' => $lastname ,
                'firstname' => $firstname,
                'status' => 1
            ))
            ->like('middlename' , $middlename)
            ->get()->row();

        /*$data['person_qry'] = $this->db->last_query();
        $data['person_res'] = $person_qry;

        return json_encode($data);
        exit();
        */

        $person_id = false;
        if ($firstname && $lastname) {
            $person = create_person_data();
            $person_id = $person->personid;
        }

        //$person_id = (isset($person_qry->personid)) ? $person_qry->sysid : null;
        $ins_qry = false;
        $corp_info_arr = array();

        //CHECK FOR EXISTING APPLICATIONS WITH THE SAME NAME
        $duplicate = check_application_duplicate();

        if ($duplicate) {
            $ins_qry = false;
            $qry = false;
            $msg = 'Application for this account already exist. TSSR#: PAE'.str_pad($duplicate->essrno,5,'0',STR_PAD_LEFT);
            $func = 'warning';
        } else {
            if ($apptype == 1 || $apptype == 0) {
                $ins_qry = true;
            }

            // INSERT CORPORATION DATA IF APPLICABLE
            if ($apptype == 2) {
                $corp_info_arr = create_corporation_data();
                $data['corpinfo'] = $corp_info_arr;
                if($corp_info_arr->qry == true) {
                    $ins_qry = true;
                }
            }

            // INSERT GOVERNMENT DATA IF APPLICABLE
            if ($apptype == 3) {
                $corp_info_arr = create_government_data();
                $data['govinfo'] = $corp_info_arr;
                if($corp_info_arr->qry == true) {
                    $ins_qry = true;
                }
            }
        }

        // MAKE SURE IF INDIVIDUAL APPLICATION DOES NOT EXIST
        /*if ($apptype == 1) {
            $find_qry = $this->db->select('sysid')
                ->from('application_customers_details')
                ->where(array('personid' => $person_id, 'status' => 1))
                ->get()->row();

            if ($find_qry) {
                $ins_qry = false;
                $qry = false;
                $msg = 'Application for this account already exist.';
                $func = 'warning';
            } else {
                $ins_qry = true;
            }
        }

        // INSERT CORPORATION DATA IF APPLICABLE
        if ($apptype == 2) {
            $corpdesc = $this->input->post('corpname');
            $corpbranch = $this->input->post('corpbranch');

            if ($corpbranch != '') {
                $this->db->where('cb.names = '.$corpbranch);
            }
            $find_qry = $this->db->select('acc.sysid')
                ->from('application_customers_corporation AS acc')
                ->join('corporation AS c','acc.corpid = c.sysid','left')
                ->join('corporation_branches AS cb','c.sysid = cb.corpid','left')
                ->where(array('c.descs' => $corpdesc,'acc.status' => 1 ))
                ->get()->row();

            if ($find_qry) {
                $ins_qry = false;
                $qry = false;
                $msg = 'Application for this account already exist.';
                $func = 'warning';
            } else {
                $corp_info_arr = create_corporation_data();
                $data['corpinfo'] = $corp_info_arr;
                if($corp_info_arr->qry == true) {
                    $ins_qry = true;
                }
            }

        }

        // INSERT GOVERNMENT DATA IF APPLICABLE
        if($apptype == 3) {
            $govdesc = $this->input->post('corpname');
            $govbranch = $this->input->post('corpbranch');

            if ($govbranch != '') {
                $this->db->where('gb.names = '.$govbranch);
            }
            $find_qry = $this->db->select('acc.sysid')
                ->from('application_customers_corporation AS acc')
                ->join('government_main AS g','acc.corpid = g.sysid','left')
                ->join('government_main_branches AS gb','g.sysid = gb.corpid','left')
                ->where(array('g.descs' => $govdesc,'acc.status' => 1 ))
                ->get()->row();

            if ($find_qry) {
                $ins_qry = false;
                $qry = false;
                $msg = 'Application for this account already exist.';
                $func = 'warning';
            } else {
                $corp_info_arr = create_government_data();
                $data['govinfo'] = $corp_info_arr;
                if($corp_info_arr->qry == true) {
                    $ins_qry = true;
                }
            }
        }*/

        $insert_app = false;
        if($ins_qry == true) {
            $ins_app_arr = array(
                'essrno' => $essrno,
                'personid' => $person_id,
                'addrspec' => $addrspecific,
                'rateclassid' => $accttype,
                'distid' => $addrdistrict,
                'country' => $country,
                'region' => $region,
                'province' => $province,
                'city' => $addrcity,
                'barangay' => $brgy,
                'marital' => ($marital) ? $marital : 0,
                'gender' => $gender,
                'suffix' => ($suffix) ? $suffix : 0,
                'prefix' => ($prefix) ? $prefix : 0,
                'sss' => ($sss) ? $sss : 0,
                'tinno' => ($tinno) ? $tinno : 0,
                'contactmobile' => $mobile,
                'contactphone' => $phone,
                'contactemail' => $email,
                'apptype' => ($apptype) ? $apptype : 0,
                'jobtype' => ($jobtype) ? $jobtype : 0,
                'existaccount' => $acctex,
                'existlegalra' => $acctra,
                'existperson' => ($personid) ? $personid : 0,
                'blacklisted' => 0,
                'createdby' => user_id(),
                'encodestart' => $encodestart,
                'moduleid' => $moduleid,
                'types' => 1
            );
            $insert_app = $this->db->insert('application_customers_details', $ins_app_arr);
            if ($insert_app) {
                $insert_app_id = $this->db->insert_id();
            } else {
                $err_msg['appdetails_insert'] = 'Error: ' . $this->db->_error_message();
                $insert_qry['appdetails_qry'] = $this->db->last_query();
            }

            if($insert_app) {

                $insert_final = true;

                $ins_subs_arr = array(
                    'appid' => $insert_app_id,
                    'classid' => $acctrate,
                    'connid' => $accttype,
                    'owntypeid' => $ownertype,
                    'loctypeid' => $loctype,
                );
                $insert_subs = $this->db->insert('application_customers_subscriptions', $ins_subs_arr);
                if (!$insert_subs) {
                    $err_msg['appsubs_insert'] = 'Error: ' . $this->db->_error_message();
                    $insert_qry['appsubs_qry'] = $this->db->last_query();
                }

                // MAP : application_customers_geodata
                $get_latlon = explode('@', $googlemap);
                if(is_array($get_latlon) && count($get_latlon) > 1) {
                    $latlon_arr = explode(',', $get_latlon[1]);
                    $lat = (isset($latlon_arr[0])) ? $latlon_arr[0] : '';
                    $lon = (isset($latlon_arr[1])) ? $latlon_arr[1] : '';
                    $zoom = (isset($latlon_arr[2])) ? str_replace('z', '', $latlon_arr[2]) : '';
                    $ins_geodata = array(
                            'appid' => $insert_app_id,
                            'lat' => $lat,
                            'lon' => $lon,
                            'alt' => $zoom,
                            'url' => $googlemap,
                            'typesid' => 340,
                            'inspdate' => date("Y-m-d"),
                            'remarks' => 'Initial Geodata',
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                    );
                    $this->db->insert("application_customers_geodata", $ins_geodata);

                }

                $this->db->insert('customer_ecales_logs',array('dataid' => $insert_app_id , 'flowid' => 2 , 'createdby' => user_id()));



                if ($apptype > 1) {
                    if($corp_info_arr->qry == true) {

                        if($apptype == 2) {
                            $corpid = $corp_info_arr->corpid;
                            $corpbid = $corp_info_arr->corpbid;
                            $types = 2;
                        }
                        if($apptype == 3) {
                            $corpid = $corp_info_arr->govid;
                            $corpbid = $corp_info_arr->govbid;
                            $types = 3;
                        }
                        $app_corp_ins = array(
                            'appid' => $insert_app_id,
                            'corpid' => $corpid,
                            'branchid' => $corpbid,
                            'types' => $types
                        );
                        if (!$this->db->insert('application_customers_corporation', $app_corp_ins) == true) {
                            $err_msg['appcorp_insert'] = 'Error: ' . $this->db->_error_message();
                            $insert_qry['appcorp_qry'] = $this->db->last_query();
                            $insert_final = false;
                        }
                    }
                }

                $req_array = explode(',', $acctreq);

                $req_array = json_decode($this->get_requirements_list($ownertype, $accttype, $loctype));
                if (isset($req_array->list) && count($req_array->list) > 0) {
                    foreach ($req_array->list as $rrow) {
                        $insert_req = array(
                            'appid' => $insert_app_id,
                            'reqid' => $rrow->sysid
                        );
                        $add_requirements = $this->db->insert('application_customers_requirements', $insert_req);
                        if (!$add_requirements) {
                            $err_msg['appcorp_insert'][] = 'Error: ' . $this->db->_error_message();
                            $insert_qry['appcorp_qry'][] = $this->db->last_query();
                        }
                    }
                }


                if ($insert_final == true) {
                    $transactiondesc = strtoupper($firstname . ' ' . $middlename . ' ' . $lastname);
                    //$insert_init_deposit = insert_application_charges(163, 250, $insert_app_id, $moduleid, 1); // INITIAL DEPOSIT
                    $insert_trns_trail = create_transaction_trails('CUST-NEW', $transactiondesc, $moduleid, $insert_app_id);

                    $insert = true;

                    /*
                    if ($insert_init_deposit->qry != true) {
                        $insert = false;
                        $msg .= ' Error Insert: Initial Deposit! ';
                    }
                    */

                    if ($insert_trns_trail != true) {
                        $insert = false;
                        $msg .= ' Error Insert: Transaction Trails! ';
                    }

                    if ($insert == true) {
                        $this->db->trans_commit();
                        $qry = true;
                        $msg .= ' New Application saved! ';
                        $func = 'success';
                    } else {
                        $this->db->trans_rollback();
                        $msg .= ' Error inserting trail details! ';
                    }

                } else {
                    $this->db->trans_rollback();
                    $msg .= 'Error inserting application details, corporation data!';
                }
            } else {
                $this->db->trans_rollback();
                $msg .= 'Error inserting application details, corporation data!';
            }
        }

        $data['person'] = $person_id;
        $data['errors'] = $err_msg;
        $data['queries'] = $insert_qry;
        //$data['duplicate'] = $duplicate;

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['input'] = $inputs_arr;
        return json_encode($data);
    }

    function get_application_info() {
        $data = array();
        $html = '';
        $id = $this->input->post('id');
        // GET TRAILS MAIN
        $qry_trail = $this->db->select()
            ->from('prime_transaction_flow_main_stages')
            ->where('flowid', 2)
            ->order_by('sysid', 'desc')
            ->get();

        // TRAIL TOTAL
        $trail_total = $qry_trail->num_rows();

        // GET TRAILS MAIN
        $qry_request_main = $this->db->select()->from('transaction_request_main')
            ->where('sysid', $id)
            ->get()->row();

        if($qry_request_main) {

            // GET TRAILS
            $qry_trail_last = $this->db->select('MS.levels, SM.dataid, SM.datecreated, MS.desc AS stagename')
                ->from('transaction_request_main_trails AS SM')
                ->join('prime_transaction_flow_main_stages AS MS', 'MS.sysid = SM.stageid')
                ->join('prime_module_navigations_main AS NM', 'NM.sysid = MS.moduleid', 'left')
                ->where('SM.trnid', $qry_request_main->sysid)
                ->order_by('SM.sysid', 'desc')
                ->get()->row();

            // DATES
            $first_date = $qry_request_main->datecreated;

            //$last_date = sql_time()->DATETIME;

            $datetime1 = new DateTime($first_date);
            $datetime2 = new DateTime(sql_time()->DATETIME);
            $interval = $datetime1->diff($datetime2);
            $datespent = $interval->format('%m Month | Day %d | Hour %H:%I:%S');
            $date_spent_mt = $interval->format('%M');
            $date_spent_d = $interval->format('%d');
            $date_spent_h = $interval->format('%H');
            $date_spent_mn = $interval->format('%I');
            $date_spent_s = $interval->format('%S');
            $days_sentence = time_to_word($date_spent_mt, $date_spent_d, $date_spent_h, $date_spent_mn, $date_spent_s);
            $datespent = ($days_sentence!='0') ? $days_sentence . ' ago.' : $days_sentence;


            $last_date = $qry_trail_last->datecreated;
            $datetime1b = new DateTime($first_date);
            $datetime2b = new DateTime($last_date);
            $intervalb = $datetime1b->diff($datetime2b);
            $date_spent_mt = $intervalb->format('%M');
            $date_spent_d = $intervalb->format('%d');
            $date_spent_h = $intervalb->format('%H');
            $date_spent_mn = $intervalb->format('%I');
            $date_spent_s = $intervalb->format('%S');
            $days_sentenceb = time_to_word($date_spent_mt, $date_spent_d, $date_spent_h, $date_spent_mn, $date_spent_s);

            $datespent_last = ($days_sentenceb!="0") ? $days_sentenceb . ' ago.' : 'no update yet.';

            $html .= '<div class="col-md-6">';
            $html .= '<ul class="list-group summary column no-border">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Time Lapse <br><code>Start to Now</code></span>';
            $html .= '<span class="col-md-9 label-default">';
            $html .= $datespent;
            $html .= '</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="col-md-6">';
            $html .= '<ul class="list-group summary column no-border">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Time Lapse <br><code>Start to Last Update</code></span>';
            $html .= '<span class="col-md-9 label-default">';
            $html .= $datespent_last;
            $html .= '</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';

            $qry = true;
        }else{
            $qry = false;
            $html = 'No Information found!';
        }

        $data['qry'] = $qry;
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_applications_report() {
        $data = array();
        $qry_request_main = $this->db->select()->from('transaction_request_main')
            ->where('flowid', 2)
            ->get();

        // GET TRAILS MAIN
        $qry_trail = $this->db->select()
            ->from('prime_transaction_flow_main_stages')
            ->where('flowid', 2)
            ->order_by('sysid', 'desc')
            ->get();

        if($qry_request_main->num_rows()>0) {
            foreach ($qry_request_main->result() as $row) {
                $desc_arr = explode(' - ', $row->descs);
                $name = (isset($desc_arr[0])) ? $desc_arr[0] : 'Unknown';
                $addr = (isset($desc_arr[1])) ? $desc_arr[1] : 'Unknown';



                // TRAIL TOTAL
                $trail_total = $qry_trail->num_rows();

                // GET TRAILS
                $qry_trail_last = $this->db->select('MS.levels, SM.dataid, SM.datecreated, MS.desc AS stagename')
                    ->from('transaction_request_main_trails AS SM')
                    ->join('prime_transaction_flow_main_stages AS MS', 'MS.sysid = SM.stageid')
                    ->join('prime_module_navigations_main AS NM', 'NM.sysid = MS.moduleid', 'left')
                    ->where('SM.trnid', $row->sysid)
                    ->order_by('SM.sysid', 'desc')
                    ->get()->row();

                // DATES
                $first_date = $row->datecreated;
                $last_date = $qry_trail_last->datecreated;
                //$last_date = sql_time()->DATETIME;

                $trn_percent_total = $qry_trail_last->levels / $trail_total * 100;

                // GET FROWARD BACK
                $qry_back = $this->db->query(" SELECT COUNT(stageid) AS CNT
                                                FROM transaction_request_main_trails 
                                                WHERE trnid = $row->sysid
                                                GROUP BY stageid 
                                                HAVING COUNT(stageid) > 1
                                                ")->row();

                $percent_backward = ($qry_back) ? $qry_back->CNT : 0;
                $trn_percent_backward = ($percent_backward>0) ? $percent_backward / $trail_total * 100 : 0;

                // GET REQUIREMENTS STATS
                $qry_req_cnt = $this->db->select('COUNT(reqid) AS CNT')
                    ->from('trn_request_requirements')
                    ->where('dataid', $qry_trail_last->dataid)
                    ->group_by('reqid')
                    ->get()->row();
                $total_req = ($qry_req_cnt) ? $qry_req_cnt->CNT : 0;

                // GER REQUIREMENTS COMPLY
                $qry_req_comp = $this->db->select('COUNT(h.dataid) AS CNT')
                    ->from('trn_request_requirements AS r')
                    ->join('trn_request_requirements_history AS h', 'h.dataid = r.sysid')
                    ->where(array('r.dataid' => $qry_trail_last->dataid, 'h.statusid' => 1))
                    ->group_by('r.reqid')
                    ->get()->row();
                $total_req_comp = ($qry_req_comp) ? $qry_req_comp->CNT : 0;

                $persent_req_comp = ($total_req_comp>0) ? $total_req_comp / $total_req * 100 : 0;
                $color_req_bar = 'progress-bar-danger';
                if($persent_req_comp>50) {
                    $color_req_bar = 'progress-bar-warning';
                }else{
                    if($persent_req_comp>=100) {
                        $color_req_bar = 'progress-bar-success';
                    }
                }



                if($qry_trail_last) {
                    $stacks = '
                        <div class="progress" style="margin: 2px 1px; background: transparent; height: 15px; border: 1px solid rgba(0,0,0,0.1)">
                            <div class="progress-bar progress-bar-success tooltips" title="Forwarded frequency ' .number_format($trn_percent_total, 2).'%" style="width: '.$trn_percent_total.'%">
                            <span class="sr-only"></span>
                            </div>
                            <div class="progress-bar progress-bar-danger tooltips" title="Backward frequency ' .number_format($trn_percent_backward, 2).'%" style="width: '.$trn_percent_backward.'%">
                            <span class="sr-only" ></span>
                            </div>
                        </div>
                    ';
                }else{
                    $stacks = '';
                }

                $data['list'][] = array(
                    'expand' => '<i data-toggle="collapse" data-target="#expand_'.$row->sysid.'" data-id="'.$row->sysid.'" id="btn-expand" class="fa fa-plus-square-o"></i> APT',
                    'name' => ucwords(strtolower($name)),
                    'address' => ucwords(strtolower($addr)),
                    'pending' => $qry_trail_last->stagename,
                    'datestart' => $first_date,
                    'dateend' => $last_date,
                    'reqstat' => '
                        <div class="row">
                        <div class="col-md-4">'.$total_req_comp.' / '.$total_req.'</div>
                        <div class="col-md-8">
                        <div class="progress progress-striped " style="margin: 2px 1px; background: transparent; height: 15px; border: 1px solid rgba(0,0,0,0.1)" >
                            <div class="progress-bar '.$color_req_bar.'" role="progressbar" aria-valuenow="'.number_format($persent_req_comp,2).'" aria-valuemin="0" aria-valuemax="100" style="width: '.$persent_req_comp.'%">
                            <span class="sr-only"></span>
                            </div>
                        </div>
                        </div>
                        </div>
                    ',
                    'status' => $stacks,
                );
            }
        }

        return json_encode($data);
    }

    function process_account_receivable() {
        $data = array();
        $id = $this->input->post('id');
        $msg = 'No Query Found!';
        $func = 'error';
        $qry = false;

        $this->db->trans_begin();

        $qry_trn_charges = $this->db->select()
            ->from('trn_customer_charges')
            ->where(array('dataid' => $id, 'status' => 1, 'group != ' => 1))
            ->get();
        $trn_num_rows = $qry_trn_charges->num_rows();
        $ins_cnt = 0;
        if($trn_num_rows>0) {
            foreach($qry_trn_charges->result() as $row) {
                // CHECK EXISTING
                $where_arr = array(
                    'clientid' => $id,
                    'accountid' => $row->acctid,
                    'entrytype' => 68,
                    'status' => 1,
                );
                $qry_check = $this->db->select()
                    ->from('trn_ledger_customers_applications')
                    ->where($where_arr)
                    ->get()->row();

                if($qry_check == false) {
                    $trn_ledger_arr = array(
                        'clientid' => $id,
                        'accountid' => $row->acctid,
                        'entrytype' => 68,
                        'amt' => $row->amt,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $trn_ins = $this->db->insert('trn_ledger_customers_applications', $trn_ledger_arr);
                    if ($trn_ins) {
                        $ins_cnt += 1;
                    }
                }
            }
        }
        if($this->db->trans_status()==true) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Account Receivable Processed!';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Error Query';
            $func = 'warning';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function customer_list_html($info, $lastclass, $firstclass) {
        $customer_id = ($info->id != '') ? $info->id : '';
        $customer_name = ($info->name != '') ? $info->name : 'Undefined';
        $customer_address = ($info->address != '') ? $info->address: 'Undefined';
        $html = '';
        $html .= '<div class="media '.$lastclass.' '. $firstclass .'">
                    <a class="pull-left" href="javascript:;">
                    <p class="media-number">'.$info->num.'</p>
                    <img class="media-object" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCI+PHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iMzIiIHk9IjMyIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEycHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9zdmc+" alt="64x64" data-src="holder.js/64x64" style="width: 64px; height: 64px;">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading text-bold text-primary">'.$customer_name.'</h4>
                        <p>'.$customer_address.'</p>
                        <div class="btn-group pull-right">
                        <a href="./view/'.$customer_id.'" class="btn btn-primary btn-xs">View</a>
                        <a href="javascript:;" class="btn btn-danger btn-xs">Billing</a>
                        <a href="javascript:;" class="btn btn-warning btn-xs">Payments</a>
</div>
                    </div>
                </div>';

        return $html;
    }

    function get_customers_lists() {
        $data = array();

        $offset = $_COOKIE['cust_list_offset'];

        if($offset<1) {
            $limit = $this->input->post('limit');
            setcookie('cust_list_offset', $limit, time() + (86400 * 30), "/"); // 86400 = 1 day
        }else{
            $limit = $offset;
        }

        $class = $this->input->post('class');


        $loc_query = $this->db->query("SELECT acct.sysid, own.ownerid, own.ownertype, addr.addrspecific
            FROM customer_accounts_main AS acct
            LEFT JOIN customer_accounts_owners AS own ON own.accountid = acct.sysid
            LEFT JOIN customer_accounts_address AS addr ON acct.sysid = addr.acctid
            LIMIT $limit
        ");


        $num_rows = $loc_query->num_rows();
        $html = '';
        if($num_rows>0) {
            $i = 0;
            $num = 1;
            foreach($loc_query->result() as $row) {
                $ownername = '';
                $owneraddr = $row->addrspecific;
                $last_class = '';
                $first_class = '';
                if ($i == 0) {
                    // first
                    $first_class = '';
                } else if ($i == $num_rows - 1) {
                    // last
                    $last_class = 'warning';
                }
                // …

                if($row->ownertype==5) {
                    $qry_name = $this->db->select('name')->from('customer_accounts_name_legacy')->where('sysid', $row->ownerid)->limit(1)->get()->row();
                    $ownername = ($qry_name) ? $qry_name->name : '';

                }

                if($row->ownertype==1) {
                    $qry_name = $this->db->select()->from('person')->where('sysid', $row->ownerid)->get()->row();
                    $ownername = ($qry_name) ? $qry_name->firstname : '';
                }
                //if($ownername!='') {
                $info = (object) array(
                    'id' => $row->sysid,
                    'num' => $num++,
                    'name' => $ownername,
                    'address' => $owneraddr,
                );
                $html .= $this->customer_list_html($info, $last_class, $first_class);
                //}
            }
        }
        $data['limit'] = $limit;
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_ranged_customers_list() {
        $data = array();
        $limit = 5;

        $data = array();

        $offset = $_COOKIE['cust_list_offset'];

        $end_num = $limit-1;
        $new_offset = bcadd($offset , 5); //

        setcookie('cust_list_offset', $new_offset, time() + (86400 * 30), "/"); // 86400 = 1 day

        $offset = $_COOKIE['cust_list_offset'];


        $class = $this->input->post('class');
        $loc_query = $this->db->query("SELECT acct.sysid, own.ownerid, own.ownertype, addr.addrspecific
        FROM customer_accounts_main AS acct
        LEFT JOIN customer_accounts_owners AS own ON own.accountid = acct.sysid
        LEFT JOIN customer_accounts_address AS addr ON acct.sysid = addr.acctid
        LIMIT $limit OFFSET $offset
        ");

        $html = '';
        $num_rows = $loc_query->num_rows();
        if($num_rows>0) {
            $i = 0;
            $num = $offset - 5;
            foreach($loc_query->result() as $row) {
                $owneraddr = $row->addrspecific;

                $last_class = '';
                $first_class = '';
                if ($i == 0) {
                    // first
                    $first_class = '';
                } else if ($i == $num_rows - 1) {
                    // last
                    $last_class = 'warning';
                }
                $i++;

                $num += 1;

                if($row->ownertype==5) {
                    $qry_name = $this->db->select('name')->from('customer_accounts_name_legacy')->where('sysid', $row->ownerid)->limit(1)->get()->row();
                    $ownername = ($qry_name) ? $qry_name->name : '';

                }
                if($row->ownertype==1) {
                    $qry_name = $this->db->select()->from('person')->where('sysid', $row->ownerid)->get()->row();
                    $ownername = ($qry_name) ? $qry_name->firstname : '';

                }
                if($ownername!='') {
                    $info = (object) array(
                        'id' => $row->sysid,
                        'num' => $num,
                        'name' => $ownername,
                        'address' => $owneraddr,
                    );
                    $html .= $this->customer_list_html($info, $last_class, $first_class);
                }
            }
        }
        $data['rows'] = $num_rows;
        $data['offset'] = $offset;
        $data['html'] = $html;
        return json_encode($data);
    }

    function setcookie_customer_lists_up () {
        $limit = $this->input->post('limit');

        $data = array();
        $new_start = 1;

        $end = $_COOKIE['limit_end'];
        if($end<1) {
            setcookie('limit_start', 1, time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie('limit_end', 50, time() + (86400 * 30), "/"); // 86400 = 1 day
        }
        $end = $_COOKIE['limit_end'];

        $end_num = $limit-1;
        $new_start = bcadd($end , 1); // 51
        $new_end = bcadd($new_start, $end_num);
        setcookie('limit_start', $new_start, time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie('limit_end', $new_end, time() + (86400 * 30), "/"); // 86400 = 1 day

        $start = $_COOKIE['limit_start'];
        $end = $_COOKIE['limit_end'];


        $data['nstart'] = $new_start;
        $data['nend'] = $new_end;

        $data['limit'] = $limit;
        $data['start'] = $start;
        $data['end'] = $end;

        return json_encode($data);
    }

    function update_customer_info() {
        $userid = user_id();
        $data = array();
        $mode = $this->input->post('mode');
        $id = $this->input->post('id');
        $todate = sql_time()->DATENUM;
        $func = 'error';
        $title = 'Error';
        $msg = 'Nothing happens..';
        if( isset( $userid ) ) {
            if ($mode == 1) {
                $this->db->trans_begin();
                // UPDATE ADDRESS
                $get_old_addr = $this->db->select()
                    ->from('customer_accounts_address')
                    ->where('acctid', $id)
                    ->get()->row();
                if ($get_old_addr) {
                    $ins_arr = array(
                        'acctid' => $get_old_addr->acctid,
                        'addrtype' => $get_old_addr->addrtype,
                        'district' => $get_old_addr->district,
                        'city' => $get_old_addr->city,
                        'country' => $get_old_addr->country,
                        'addrspecific' => $get_old_addr->addrspecific,
                        'createdby' => user_id()
                    );
                    $this->db->insert('customer_accounts_address_history', $ins_arr);

                    $upd_arr = array(
                        'district' => $this->input->post('district'),
                        'city' => $this->input->post('city'),
                        'country' => 175,
                        'addrspecific' => $this->input->post('addrspec')
                    );
                    $this->db->where('acctid', $id);
                    $this->db->update('customer_accounts_address', $upd_arr);
                } else {
                    $upd_arr = array(
                        'acctid' => $id,
                        'district' => $this->input->post('district'),
                        'city' => $this->input->post('city'),
                        'country' => 175,
                        'addrspecific' => $this->input->post('addrspec')
                    );
                    $this->db->insert('customer_accounts_address', $upd_arr);
                }

                // UPDATE RATE
                $get_old_rate = $this->db->select()
                    ->from('customer_accounts_subscription_rates')
                    ->where('accountid', $id)
                    ->get()->row();
                if ($get_old_rate) {
                    $ins_arr = array(
                        'accountid' => $get_old_rate->accountid,
                        'rateid' => $get_old_rate->rateid,
                        'createdby' => user_id()
                    );
                    $this->db->insert('customer_accounts_subscription_rates_history', $ins_arr);
                    $upd_arr = array(
                        'rateid' => $this->input->post('rate'),
                    );
                    $this->db->where('accountid', $id);
                    $this->db->update('customer_accounts_subscription_rates', $upd_arr);
                } else {
                    $inser_new_arr = array(
                        'accountid' => $id,
                        'rateid' => $this->input->post('rate'),
                        'createdby' => user_id()
                    );
                    $this->db->insert('customer_accounts_subscription_rates', $inser_new_arr);
                }

                // UPDATE GDLB
                $get_old_gdlb = $this->db->select()
                    ->from('customer_accounts_glb')
                    ->where('accountid', $id)
                    ->get()->row();

                if ($get_old_gdlb) {
                    $gdlb_ins_hist_arr = array(
                        'accountid' => $get_old_gdlb->accountid,
                        'gdlbid' => $get_old_gdlb->gdlbid,
                        'createdby' => user_id()
                    );
                    $this->db->insert('customer_accounts_glb_history', $gdlb_ins_hist_arr);
                    $upd_gdlb_arr = array(
                        'gdlbid' => $this->input->post('gdlb'),
                        'status' => 1
                    );
                    $this->db->where('accountid', $id);
                    $this->db->update('customer_accounts_glb', $upd_gdlb_arr);
                } else {
                    $gdlb_ins_arr = array(
                        'accountid' => $id,
                        'gdlbid' => $this->input->post('gdlb'),
                        'createdby' => user_id(),
                        'status' => 1
                    );
                    $this->db->insert('customer_accounts_glb', $gdlb_ins_arr);
                }


                if ($this->db->trans_status() === TRUE) {
                    $msg = '<i class="fa fa-check"></i> Basic info updated!';
                    $func = 'success';
                    $this->db->trans_commit();
                } else {
                    $msg = '<i class="fa fa-check"></i> Basic info update error!';
                    $func = 'warning';
                    $this->db->trans_rollback();
                }
                $title = 'Update Basic Info';
            }

            if ($mode == 2) {
                $this->db->trans_begin();

                // RGD UPDATE
                $this->db->where('accountid', $id);
                $this->db->update('trn_customer_accounts_gdr_logs', array('status' => 0));
                $ins_gdr = array(
                    'rgdno'     => $this->input->post('rgdno'),
                    'accountid' => $id,
                    'createdby' => user_id(),
                    'totalcost' => $this->input->post('rgdamt'),
                );
                $this->db->insert('trn_customer_accounts_gdr_logs', $ins_gdr);

                $mtrsysid = $this->input->post('mtrsysid');
                // INSERT READING PREVIOUS
                $this->db->where(array('mtrid' => $mtrsysid, 'types' => 5, 'status' => 1));
                $this->db->update('customer_accounts_subscription_meter_reading', array('status' => 0));

                $ins_arr_read_prev = array(
                    'mtrid' => $mtrsysid,
                    'reading' => $this->input->post('prevread'),
                    'readingdate' => $this->input->post('prevreaddate'),
                    'types' => 5,
                    'createdby' => user_id()
                );
                $this->db->insert('customer_accounts_subscription_meter_reading', $ins_arr_read_prev);

                // INSERT READING PRESENT
                $ins_arr_read_pres = array(
                    'mtrid' => $mtrsysid,
                    'reading' => $this->input->post('presread'),
                    'readingdate' => $this->input->post('presreaddate'),
                    'types' => 5,
                    'createdby' => user_id()
                );
                $this->db->insert('customer_accounts_subscription_meter_reading', $ins_arr_read_pres);

                // INSERT MULT CODE
                // UPDATE first
                $this->db->where('acctid', $id);
                $this->db->update('customer_accounts_multiplier', array('status' => 0, 'updatedby' => user_id()));
                // INSERT next
                $ins_mult_arr = array(
                    'acctid' => $id,
                    'multid' => $this->input->post('nultcode'),
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('customer_accounts_multiplier', $ins_mult_arr);

                if ($this->db->trans_status() === TRUE) {
                    $msg = '<i class="fa fa-check"></i> Basic info updated!';
                    $func = 'success';
                    $this->db->trans_commit();
                } else {
                    $msg = '<i class="fa fa-check"></i> Basic info update error!';
                    $func = 'warning';
                    $this->db->trans_rollback();
                }
                $title = 'Update Other Info';
            }
        }else{
            $msg = 'Session timeout!';
        }
        $data['date'] = $todate;
        $data['input'] = $this->input->post();
        $data['title'] = $title;
        $data['func'] = $func;
        $data['msg'] = $msg;

        return json_encode($data);
    }

    function get_application_services() {
        $appid = $this->input->post('appid');
        $moduleid = $this->input->post('moduleid');
        $type = $this->input->post('type');
        $viewtype = $this->input->post('viewtype');
        $data = array();
        $qry = false;
        $personid = 0;
        $address = '';

        if($moduleid == 106) {
            $app_info = $this->db->select()
                ->from('service_customers')
                ->where(array('sysid' => $appid))->get()->row();
            $personid = $app_info->personid;
        }else {
            $joarr = array(160, 161, 162, 163);
            if(in_array($moduleid  , $joarr) ){
                $info = get_joborder_info($appid);
                $accinfo = get_active_account_info($info->acctid);
                $data['fullname'] = ($info) ? $accinfo->name : 'N/A';
                $address = ($info) ? $accinfo->address : 'N/A';
            }else{
                $info = application_info($appid);
                $data['fullname'] = ($info->q == true) ? $info->appname : 'N/A';
                $data['essrno'] = ($info->essrno != '') ? '<b>' . $info->essrno . '</b>' : '';
                $address = ($info->q == true) ? $info->address : 'N/A';
                $personid = $info->personid;
            }
        }

        $charges_list = '';

        $query = $this->db->select('c.sysid, c.chargeid, a.codes, a.descs, c.amt, c.vatamt, c.vattype, a.groups')
            ->from('application_customers_charges AS c')
            ->join('prime_chart_of_accounts AS a', 'a.sysid = c.chargeid')
            ->where(array('c.appid' => $appid, 'c.status' => 1, 'c.moduleid' => $moduleid))
            ->group_by('c.sysid, c.chargeid, a.codes, a.descs, c.amt, c.vatamt, c.vattype, a.groups')
            ->order_by('CAST(c.datecreated AS DATE)', 'asc')
            ->order_by('a.groups')
            ->order_by('a.codes')
            ->get();

        //$data['chqry'] = $this->db->last_query();

        $data['errmsg'] = $this->db->_error_message();

        $total_qty = 0;
        $total_amt = 0;
        $total_vat = 0;
        $total_nvat = 0;
        $total_paid = 0;

        $total_initdep_amt = 0;
        $total_gdrdep_amt = 0;
        $total_laborserv_amt = 0;
        $total_other_amt = 0;

        if($query->num_rows()>0) {
            $i = 1;
            $charges_list = '';
            foreach($query->result() as $row) {

                $vatamt = $row->vatamt;
                $amt = $row->amt;

                if($row->vattype==1) {
                    $nonvat = bcsub($amt, $vatamt, 2);
                    $total = $amt;
                    $vattype = '<i class="fa fa-angle-double-left tooltips" data-placement="left" title="VAT Inclusive"></i>';
                }else{
                    $nonvat = $row->amt;
                    $total = $amt + $vatamt;
                    $vattype = '<i class="fa fa-angle-double-up tooltips" data-placement="left" title="Add Up Vat"></i>';
                }

                $total_vat += $vatamt;
                $total_amt += $total;
                $total_nvat += $nonvat;
                $btn = false;

                $qry_payments =  $this->db->select(
                    'SUM(totalamt) + SUM(franchisetax) AS payments'
                )->from('transaction_payments_logs')
                    ->where(array('moduleid' => $moduleid, 'payforacctno' => $row->chargeid , 'dataid' => $appid , 'status' => 1))
                    ->get()->row();
                $payments = 0;
                if($qry_payments) {
                    $payments = $qry_payments->payments;
                }


                $data['payments'][] = $payments;

                $stats = '';
                $row_class = '';
                if( $payments > 0 && $payments >= $amt ) {
                    $stats = '<span class="btn label label-success"><i class="fa fa-check"></i></span>';
                    $row_class = 'success';
                    $total_paid += $total;
                    $chk = '';
                    $stat_label = '<label class="label label-success label-sm pull-right" style="margin-right: 13px;">Paid</label>';
                }else{
                    /*
                    $chk = '<div class="md-checkbox">
                            <input id="checkbox'.$row->sysid.'" class="md-check" type="checkbox" value="'.$total.'" name="chk['.$row->sysid.']">
                            <label for="checkbox'.$row->sysid.'">
                            <span class="inc"></span>
                            <span class="check"></span>
                            <span class="box"></span></label>
                        </div>';
                    */

                    $chk = '<input id="checkbox'.$row->sysid.'" class="icheck" type="checkbox" value="'.$total.'" name="chk['.$row->chargeid.']">';
                    if($viewtype != 2) {
                        if ($type == 1) {
                            $stats = ' <label class="btn label label-danger"><i class="fa fa-check"></i></label>';
                            $stat_label = '<label class="label label-success label-sm pull-right" style="margin-right: 13px;">Paid</label>';
                        } else {
                            $stats = '<button data-id="' . $row->sysid . '" class="btn btn-danger btn-xs btn_del"><i class="fa fa-times"></i></button>';
                            $stat_label = '<label class="label label-danger label-sm pull-right" style="margin-right: 13px;">Unpaid</label>';
                        }
                    } else {
                        $stat_label = '<code>N/A</code>';
                    }
                    $row_class = 'danger';
                }

                if($row->groups==1) {
                    $total_initdep_amt += $total;
                }
                if($row->groups==2) {
                    $total_gdrdep_amt += $total;
                }
                if($row->groups==3) {
                    $total_laborserv_amt += $total;
                }
                if($row->groups!=1 && $row->groups!=2 && $row->groups!=3) {
                    $total_other_amt += $total;
                }

                $acct_no = ''
                    . '<a role="button" '
                    . 'class="popovers" '
                    . 'data-trigger="hover" '
                    . 'data-container="body" '
                    . 'data-placement="right" '
                    . 'data-content="'.$row->descs.'"'
                    . 'data-original-title="'.$row->codes.'"><i class="fa fa-search"></i> '.$row->codes.'</a>';

                $input_frtx = '<input style="width: 100%" name="frtx['.$row->chargeid.']" class="form-control inline input-xs number" placeholder="0.00" />';

                $data['list'][] = array(
                    'num' => $i++,
                    'acctno' => $acct_no,
                    'acctname' => $row->descs,
                    'vat' => '<span class="value">'.number_format($vatamt, 2).'</span>' . '<span class="pull-left">'.$vattype.'</span>',
                    'nonvat' => number_format($nonvat, 2),
                    'cwt' => $input_frtx,
                    'total' => number_format($total, 2) ,
                    'chk' => $chk,
                    'control' => $stats,
                    'rowclass' => $row_class,
                    'acctnames' => upper_ent_quotes($row->descs),
                    'statlabel' => $stat_label,
                );

                $charges_list .= '<li class="list-group-item" style="margin: 0px 0px">';
                $charges_list .= '<span class="col-md-9 label-name"><span class="row" style="margin: 0px 0px"><span class="col-md-9">' . upper_ent_quotes($row->descs) . '</span>  ' . $stat_label . '</span></span>';
                $charges_list .= '<span class="col-md-3 label-default number" style="font-weight: normal !important; font-size: 13px !important;">'.number_format($total, 2).'</span>';
                $charges_list .= '</li>';

                $overide_btn = (strpos($stat_label,'PAID') && (super_admin() == false || !in_array(8,get_user_info_main_role()))) ? '' : ' <a href="#frm_overide_amt" data-toggle="ajax-modal" data-arr="'.$row->sysid.'" class="btn btn-primary inline btn-xs tooltips" data-content="body" data-original-title="Override Amount" id="overide_amt" title="Override Amount"><i class="fa fa-edit"></i></a>';

                $data['chargelist'][] = array(
                    'desc' => upper_ent_quotes($row->descs),
                    'amt' => number_format($total, 2).$overide_btn,
                    'status' => $stat_label,
                );

                $total_qty += 1;
            }
            $qry = true;
        }

        $check_gdr = check_acct_gdr($appid);

        $balance = $total_amt - $total_paid;



        $data['addr'] = $address;


        $data['qty'] = number_format($total_qty);
        $data['servamt'] = number_format($total_amt,2);
        $data['total'] = number_format($total_amt,2);
        $data['totalvat'] = number_format($total_vat,2);
        $data['totalnvat'] = number_format($total_nvat,2);
        $data['totalpaid'] = number_format($total_paid,2);
        $data['balance'] = ($balance > 0) ? number_format($balance,2) : '<span class="label label-success"><i class="fa fa-check fa-fw"></i> Settled</span>';

        $data['initdepamt'] = number_format($total_initdep_amt,2);
        $data['gdrdepamt'] = number_format($total_gdrdep_amt,2);
        $data['laborservamt'] = number_format($total_laborserv_amt,2);
        $data['otheramt'] = number_format($total_other_amt,2);
        $userinfo = get_users_info(0, true);

        $appid_code = str_pad($appid, 6, '0', STR_PAD_LEFT);

        $data['printhead'] = customer_print_header($personid, $address, $appid_code);

        $data['dataid'] = $appid;
        $data['printedby'] = ($userinfo) ? $userinfo->lastname.', '.$userinfo->firstname : 'N/A';
        $data['dateprinted'] = sql_time()->DATETIME;

        $data['settled'] = ($balance > 0) ? false : true;
        $data['arbtn'] = '';
        $data['qry'] = $qry;
        $data['input'] = $this->input->post();
        $data['charges'] = $charges_list;
        $data['query'] = $query;
        return json_encode($data);
    }

    function get_application_details() {
        $data = array();

        $dataid = $this->input->post('id');



        $mode = $this->input->post('mode');
        $info = application_info($dataid);

        $appname        = ($info->q) ? $info->appname : 'N/A';
        $address        = ($info->q) ? $info->address : 'N/A';
        $landmark       = ($info->q) ? $info->landmark : 'N/A';
        $distname       = ($info->q) ? $info->distname : 'N/A';
        $gdlb           = ($info->q) ? $info->gdlb : 'N/A';
        $mapupdated     = ($info->q) ? $info->mapupdated : 'N/A';
        $mapupdatedby   = ($info->q) ? $info->mapupdatedby : 'N/A';


        $conntype       = 'N/A';
        $ownertype      = 'N/A';
        $rateclass      = 'N/A';
        $landtype       = 'N/A';
        $distid         = ($info->q) ? $info->distid : 0;
        $totalload      = ($info->q) ? $info->totalload : 0;
        $essrno         = ($info->q) ? $info->essrno : 'N/A';
        $gdlbid         = ($info->q) ? $info->gdlbid : 0;
        $moduleid       = ($info->q) ? $info->moduleid : 0;
        $apptype        = ($info->q) ? $info->apptype : 0;

        $phone = (($info->q && isset($info->phone)) || (isset($info->phone) && $info->phone != '')) ? $info->phone : 'N/A';
        $mobile = (($info->q && isset($info->mobile)) || (isset($info->mobile) && $info->mobile > 0)) ? $info->mobile : 'N/A';
        $email = (($info->q && isset($info->email)) || (isset($info->email) && $info->email > 0)) ? $info->email : 'N/A';
        $servno = '';

        $corpname = 'Unknown';
        $corpbranch = '';

        // GET CORP INFO
        $qry_corp_app = $this->db->select()
            ->from('application_customers_corporation')
            ->where(array('appid' => $dataid, 'types' => $apptype))
            ->get()->row();


        $pic_recent = base_url() . 'assets/global/img/person_default.jpg';
        $pic_id = ($info->q) ? $info->personid : '';
        $pic_dir = 'person';

        if($qry_corp_app) {
            $info = array();
            if($apptype == 2) {
                $info = get_corporation_info($qry_corp_app->corpid);
                $pic_dir = 'corporation';
            } else {
                $info = get_government_info($qry_corp_app->corpid);
                $pic_dir = 'government';
            }
            $pic_id = $qry_corp_app->corpid;
            if ($info->qry) {
                $corpname = $info->info->descs;


                if($apptype == 2) {
                    $qry_branch = $this->db->select()
                        ->from('corporation_branches')
                        ->where(array('corpid' => $qry_corp_app->corpid, 'sysid' => $qry_corp_app->branchid))
                        ->get()->row();
                    if ($qry_branch) {
                        $corpbranch = $qry_branch->names;
                    }
                }else{
                    $corpbranch = ($info) ? $info->info->names : '';
                }
            }
        }

        $pic_recent = get_owner_pic($pic_id, $pic_dir, 2);
        $html = '';

        if($mode == 'data') {
            if ($info == true) {

                $sql_trn_list = $this->db->select('trmt.sysid, trmt.datecreated, trmt.createdby, tfms.`desc`')
                    ->from('transaction_request_main_trails as trmt')
                    ->join('prime_transaction_flow_main_stages as tfms','trmt.stageid = tfms.sysid','left')
                    ->where('trmt.dataid',$dataid)
                    ->order_by('trmt.datecreated','DESC')
                    ->get();

                $data['info'] = $info;
                if($sql_trn_list->num_rows()>0) {
                    foreach($sql_trn_list->result() as $trn_row) {
                        $user_arr = get_users_info($trn_row->createdby);
                        $user_name = ($user_arr) ? $user_arr->lastname . ', ' . $user_arr->firstname : 'Uknown';

                        $time_updated = $trn_row->datecreated . '<small class="text-info pull-right">' . timeago($trn_row->datecreated, sql_time()->DATETIME).'</small>';

                        $data['trns'][] = array(
                            'num' => $trn_row->sysid,
                            'trans' => $trn_row->desc,
                            'datecreated' => $time_updated,
                            'createdby' => $user_name
                        );
                    }
                }
            }
        }else {
            if ($info == true) {
                $html .= '<div class="col-md-1">';
                $html .= '<form style="margin-top: 20px;" id="frm_upload_pic" method="post" action="" enctype="multipart/form-data">
                            <input name="remarks" type="hidden" value="CUSTOMER APPLICATION" />
                            <input name="moduleid" type="hidden" value="' . $moduleid . '" />
                            <input name="ownerid" type="hidden" value="' . $pic_id . '" />
                            <input name="dataid" type="hidden" value="' . $dataid . '" />
                            <input name="dir" type="hidden" value="' . $pic_dir . '" />
                            <div class="fileinput fileinput-new fileinput-custom" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" data-trigger="fileinput">
                                <img alt="" class="fileinput-new" src="' . $pic_recent . '">
                                <div class="fileinput-preview fileinput-exists thumbnail" >
                                </div>
                                </div>
                                    <span class="btn-file">
                                    <input type="file" id="emppic" name="newpic">
                                    </span>
                                    <a id="btn_upload_pic" href="javascript:;" class="btn btn-xs btn-circle blue btn-upload fileinput-exists">
                                    <i class="fa fa-upload"></i></a>
                                    <a href="javascript:;" class="btn btn-xs btn-circle btn-remove red fileinput-exists" data-dismiss="fileinput">
                                    <i class="fa fa-times"></i> </a>
                            </div>
                        </form>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<h5 class="font-red-flamingo bold" style="margin-top: 0px;">Basic Information</h5>';
                $html .= '<ul class="list-group summary column list-group-sm no-border">';
                if ($qry_corp_app) {
                    $html .= '<li class="list-group-item">';
                    $html .= '<span class="col-md-3 label-name">Business</span>';
                    $html .= '<span class="col-md-9 text-primary">' . $corpname . '</span>';
                    $html .= '</li>';
                    if(trim($corpbranch)  != '') {
                        $html .= '<li class="list-group-item">';
                        $html .= '<span class="col-md-3 label-name">Branch</span>';
                        $html .= '<span class="col-md-9 text-primary">' . $corpbranch . '</span>';
                        $html .= '</li>';
                    }
                }
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Address</span>';
                $html .= '<span class="col-md-9 text-primary">' . $address . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">District</span>';
                $html .= '<span class="col-md-9 text-primary">' . $distname . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">GDLB</span>';
                $html .= '<span class="col-md-9 text-primary">' . $gdlb . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Landmark</span>';
                $html .= '<span class="col-md-9 text-primary">' . $landmark . '</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';


                $html .= '<div class="col-md-4">';
                $html .= '<h5 class="font-red-flamingo bold" style="margin-top: 0px;">Connecting Application</h5>';
                $html .= '<ul class="list-group summary column list-group-sm no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Account Type</span>';
                $html .= '<span class="col-md-9 text-primary">' . $rateclass . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Type of Roof</span>';
                $html .= '<span class="col-md-9 text-primary">Concrete</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Payment Type</span>';
                $html .= '<span class="col-md-9 text-primary">' . $ownertype . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Roof Inclination</span>';
                $html .= '<span class="col-md-9 text-primary">30deg</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-2">';
                $html .= '<h5 class="font-red-flamingo bold" style="margin-top: 0px;">Contacts</h5>';
                $html .= '<ul class="list-group summary column list-group-sm no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Email</span>';
                $html .= '<span class="col-md-9 text-primary">' . $email . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Phone</span>';
                $html .= '<span class="col-md-9 text-primary">' . $phone . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-3 label-name">Mobile</span>';
                $html .= '<span class="col-md-9 text-primary ">' . $mobile . '</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';


                $html .= '<div class="col-md-2" style="padding-left: 50px;">';
                $html .= '<h5 class="font-red-flamingo bold" style="margin-top: 0px;">Views</h5>';
                $html .= '<ul class="list-group summary column list-group-sm no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-2 label-name"><i class="fa fa-map-o fa-fw font-dark"></i></span>';
                $html .= '<span class="col-md-10 text-primary"><a class="" data-toggle="ajax-modal-map" data-id="' . $dataid . '" title="Application Map: ' . $appname . '" href="#cad_map_lookup">Map</a></span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-2 label-name"><i class="fa fa-reorder fa-fw font-dark"></i></span>';
                $html .= '<span class="col-md-10 text-primary"><a class="" data-toggle="ajax-modal" data-arr="' . $dataid . '" title="Application Requirements: ' . $appname . '" href="#frm_cad_customer_requirements_list">Requirements</a></span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-2 label-name"><i class="fa fa-search fa-fw font-dark"></i></span>';
                $html .= '<span class="col-md-10 text-primary"><a class="" data-toggle="ajax-modal" data-arr="' . $dataid . '" title="Inspection Logs: ' . $appname . '" href="#tbl_app_inspection_logs">Inspection</a></span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-2 label-name"><i class="fa fa-cart-arrow-down fa-fw font-dark"></i></span>';
                $html .= '<span class="col-md-10 text-primary"><a class="" data-toggle="ajax-modal" data-arr="' . $dataid . ',' . $moduleid . '" title="Assessments: ' . $appname . '" href="#tbl_app_assessments">Assessments</a></span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';


                $qry_trn_details = $this->db->query("
                    SELECT 
                    rmt.trnid
                    FROM application_customers_details AS cd 
                    INNER JOIN transaction_request_main_trails AS rmt ON rmt.dataid = cd.sysid 
                    WHERE cd.sysid = $dataid
                    ORDER BY rmt.datecreated DESC
                ")->row();

                $comment_cnt = '';
                $comment_msg = '';
                if($qry_trn_details) {
                    $qry_comments_cnt = $this->db->select('count(tc.trnid) AS cnt')
                        ->from('transaction_request_trails_comments AS tc')
                        ->where(array('tc.trnid' => $qry_trn_details->trnid, 'status' => 1))
                        ->get()->row();
                    if ($qry_comments_cnt && $qry_comments_cnt->cnt > 0) {

                        $qry_comments_msg = $this->db->select('remarks')
                            ->from('transaction_request_trails_comments AS tc')
                            ->where(array('tc.trnid' => $qry_trn_details->trnid, 'status' => 1))
                            ->order_by('datecreated', 'desc')
                            ->get()->row();
                        $comment_msg = ($qry_comments_msg) ? $qry_comments_msg->remarks : '';
                        $max_length = 45;

                        if (strlen($comment_msg) > $max_length) {
                            $offset = ($max_length - 3) - strlen($comment_msg);
                            $comment_msg = substr($comment_msg, 0, strrpos($comment_msg, ' ', $offset)) . ' ...';
                        }
                        $comment_cnt = '<span class="badge badge-danger pull-right" style="margin-left: 5px;">' . $qry_comments_cnt->cnt . '</span>';
                    }
                }

                $html .= '<div class="row footer" style="padding-bottom: 10px;">';
                $html .= '<div class="col-md-12">';
                $html .= '<ul class="list-group summary column list-group-sm no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-1 label-name text-align-center">Last Remarks</span>';
                $html .= '<span class="col-md-11 text-primary">'.$comment_msg.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</div>';

            } else {
                $html = html_view_notfound('info', '503', '<code>' . $dataid . '</code>This view is under maintenance!');
            }
            $data['html'] = $html;
        }
        return json_encode($data);
    }

    function get_special_services_info($servno) {
        $data = array();

        $qry = $this->db->select(
            '   
                sc.sysid,
                p.lastname,
                p.firstname,
                p.middlename,
                am.addrspec
            '
        )
            ->from('service_customers AS sc')
            ->join('person AS p', 'p.sysid = sc.personid')
            ->join('person_address_matrix AS am', 'p.sysid = am.personid', 'left')
            ->where(array('sc.sysid' => $servno))
            ->get()->row();
        if($qry) {
            $lastname       = $qry->lastname;
            $firstname      = $qry->firstname;
            $middlename     = $qry->middlename;
            $address        = $qry->addrspec;
            $name           = $lastname. ', '.$firstname;
            $servno         = 'X'.str_pad($qry->sysid, 6, 0, STR_PAD_LEFT);
        }else{
            $name           = '';
            $lastname       = '';
            $firstname      = '';
            $middlename     = '';
            $address        = '';
            $servno         = '';
        }

        $data['appname'] = $name;
        $data['appaddr'] = $address;
        $data['firstname'] = $firstname;
        $data['lastname'] = $lastname;
        $data['middlename'] = $middlename;
        $data['addrspec'] = $address;
        $data['servno'] = $servno;

        return (object) $data;
    }

    function get_services_starts() {
        $data = array();
        $qry = false;
        $msg = '';
        $func = 'error';
        $personid = $this->input->post('personid');
        $moduleid = $this->input->post('moduleid');
        $types = $this->input->post('types');

        $lastname = '';
        $firstname = '';
        $middlename = '';
        $address = '';
        $sysid = '';
        $dataid = '';

        $person = create_person_data();
        $personname = $person->personname;
        $address = $person->personaddress;
        $sysid = $person->personid;


        $qry_check_tempserv = $this->db->select()
            ->from('service_customers')
            ->where(array('personid' => $sysid))->get()->row();

        if ($qry_check_tempserv) {
            $dataid = $qry_check_tempserv->sysid;
            $servno = 'X'.str_pad($dataid, 6, '0', STR_PAD_LEFT);
            $temp_acct_msg = ' and with temporary service no.: ' . $servno;
        } else {
            $this->db->insert('service_customers', array('personid' => $sysid, 'types' => 1, 'createdby' => user_id()));
            $dataid = $this->db->insert_id();
            $servno = 'X'.str_pad($dataid, 6, '0', STR_PAD_LEFT);
            $temp_acct_msg = ' and created temporary service no.: ' . $servno;
        }


        $msg = 'Existing Person Selected, ' . $temp_acct_msg;
        $func = 'info';
        $qry = true;

        $data['lastname']       = $lastname;
        $data['firstname']      = $firstname;
        $data['middlename']     = $middlename;
        $data['custname']       = $personname;
        $data['address']        = $address;
        $data['sysid']          = $sysid;
        $data['dataid']         = $dataid;
        $data['moduleid']       = $moduleid;
        $data['servno']         = $servno;
        $data['qry']            = $qry;
        $data['msg']            = $msg;
        $data['func']           = $func;


        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_services_lastname() {
        $data = array();
        $select_arr = $this->input->post('selectarr');
        $exists = false;
        if(is_array($select_arr)) {
            if(count($select_arr) > 0) {
                if(isset($select_arr[0]) && is_numeric($select_arr[0])) {
                    $exists = true;

                    $qry_person = $this->db->select("p.sysid, p.firstname, p.middlename, am.addrspec")->from('person AS p')
                        ->join('person_address_matrix AS am', 'am.personid = p.sysid', 'left')
                        ->where(array('p.sysid' => $select_arr[0]))->get()->row();
                    if ($qry_person) {
                        $data['firstname'] = $qry_person->firstname;
                        $data['middlename'] = $qry_person->middlename;
                        $data['address'] = $qry_person->addrspec;
                        $data['sysid'] = $qry_person->sysid;
                        $data['type'] = 1;
                    }

                }
            }
        }
        $data['exists'] = $exists;
        $data['input'] = $this->input->post();

        return json_encode($data);
    }


    function execute_accomplishement() {
        $data = array();
        $inputs = $this->input->post();
        $dataid = $this->input->post('dataid');
        $moduleid = $this->input->post('moduleid');
        $trailid = $this->input->post('trailid');

        $conby = $this->input->post('conby');
        $condate = $this->input->post('condate');
        $contractdate = $this->input->post('contractdate');
        $multcode = $this->input->post('multcode');
        $initread = $this->input->post('initread');
        $ercseal = $this->input->post('ercseal');
        $remarks = $this->input->post('remarks');

        $name = '';
        $qry = false;
        $msg = 'Nothing happends!';
        $func = 'warning';
        $exec = true;

        $this->db->trans_begin();
        $qry_app = application_info($dataid);

        if($qry_app) {

            //@TODO METER DETAILS BELOW
            $initread = 1;
            $multcode = 1;

            // GET METER INFO ISSUEANCE
            $qry_issueance = $this->db->select()->from('customer_accounts_meter_issuance')
                ->where(array('acctid' => $dataid, 'status' => 1))->get()->row();

            $data['info'] = $qry_app;

            // CHECK PAYMENTS INITIAL DEPOSIT
            $qry_payments_init = $this->db->select('totalamt')->from('transaction_payments_logs')
                ->where(array('moduleid' => $moduleid, 'dataid' => $dataid, 'payforacctno' => 163))->get()->row();

            if($qry_payments_init==false) {
                $exec = false;
                $msg = 'Initial Deposit was not made!';
                $func = 'warning';
            }

            // CHECK PAYMENTS GDR
            // GET CAD PAYMENT ACCOUNTS
            $payments_accounts = pay_account_array();
            $qry_payments_init = $this->db->select('SUM(totalamt) AS AMT')->from('transaction_payments_logs')
                ->where(array('moduleid' => $moduleid, 'dataid' => $dataid, 'payforacctno != ' => 163, 'status' => 1))
                ->where_in('payforacctno', $payments_accounts)
                ->get()->row();

            if($qry_payments_init && $qry_payments_init->AMT <= 0) {
                $exec = false;
                $msg = 'Guaranty Deposit was not made!';
                $func = 'warning';
            }

            if($qry_issueance==false) {
                $exec = false;
                $msg = 'No meter issued yet!';
                $func = 'warning';
            }

            if($conby==false) {
                $exec = false;
                $msg = 'Execute by is empty!';
                $func = 'warning';
            }

            if($condate==false) {
                $exec = false;
                $msg = 'Connection date is empty!';
                $func = 'warning';
            }

            if($multcode==false) {
                $exec = false;
                $msg = 'Multcode is empty!';
                $func = 'warning';
            }

            if($initread==false) {
                $exec = false;
                $msg = 'Please provide initial reading!';
                $func = 'warning';
            }

            if ($qry_app->servno == '') {
                $exec = false;
                $msg = 'Service number is not generated!';
                $func = 'warning';
            }

            if ($qry_app->gdlbid == '') {
                $exec = false;
                $msg = 'GDLB Not assigned!';
                $func = 'warning';
            }

            if ($qry_app->rateclassid == '') {
                $exec = false;
                $msg = 'Rate Class Not assigned!';
                $func = 'warning';
            }

            // CHECK SERVICE NUMBER IF EXISTED
            $qry_acct_ex = $this->db->select('servicenumber')->from('customer_accounts_main')
                ->where(array('servicenumber' => $qry_app->servno, 'status' => 1))->get()->row();

            if ($qry_acct_ex) {
                $exec = false;
                $msg = 'Service Number is already in used!';
                $func = 'warning';
            }

            if ($exec == true) {
                $owner_id = 0;

                if($qry_app->personexist < 1) {
                    $person_ins_arr = array(
                        'firstname' => $qry_app->firstname,
                        'lastname' => $qry_app->lastname,
                        'middlename' => $qry_app->middlename,
                    );
                    $this->db->insert('person', $person_ins_arr);
                    $owner_id = $this->db->insert_id();
                }else{
                    $owner_id = $qry_app->personexist;
                }

                $acct_ins_arr = array(
                    'servicenumber' => $qry_app->servno,
                    'createdby' => user_id(),
                    'datecontract' => $contractdate,
                    'dateconnected' => $condate,
                    'ownerid' => $owner_id,
                    'types' => 1,
                    'gdlb' =>  $qry_app->gdlbid,
                    'mtrno' =>  $qry_issueance->mtrno,
                    'mtrserial' => $qry_issueance->mtrserial,
                    'mtr' => 1,
                    'rateclassid' => $qry_app->rateclassid,
                    'multid' => $multcode,
                    'mtrassetid' => $qry_issueance->assetid
                );
                $this->db->insert('customer_accounts_main', $acct_ins_arr);
                $acct_id = $this->db->insert_id();

                $ar_ins_arr = array(
                    'acctid' => $acct_id,
                    'mtr' => 1
                );
                $this->db->insert('customer_accounts_ar', $ar_ins_arr);

                $audit_ins_arr = array(
                    'dataid' => $dataid,
                    'moduleid' => $moduleid,
                    'valueold' => '',
                    'valuenew' => $name,
                    'createdby' => user_id(),
                    'remarks' => 'CAD - New Accomplishment'
                );
                //$audit_ins = audit_insert($audit_ins_arr);
                $audit_ins = true;

                // UPDATE APPLICATION TO ACCOMPLISHED
                if($audit_ins) {
                    $upd_arr = array(
                        'updatedby' => user_id(),
                        'status' => 2
                    );
                    $this->db->where(array('sysid' => $dataid, 'status' => 1));
                    $this->db->update('application_customers_details', $upd_arr);

                    $trans_logs_ins = array(
                        'trailid' => $trailid,
                        'activity' => 321,
                        'userid' => user_id(),
                    );
                    $this->db->insert('transaction_request_trails_logs', $trans_logs_ins);
                }

                if ($this->db->trans_status() === TRUE && $audit_ins) {
                    $this->db->trans_commit();
                    $qry = true;
                    $msg = 'Account Created!';
                    $func = 'success';
                } else {
                    $this->db->trans_rollbakc();
                    $msg = 'Error DB';
                    $func = 'error';
                }
            }
        }else{
            $msg = 'Cannot find the application information!';
            $func = 'warning';
        }

        $data['moduleid'] = $moduleid;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['inputs'] = $inputs;
        return json_encode($data);
    }

    function get_applicaton_meter_connections() {
        $data       = array();
        $viewtype   = $this->input->post('viewtype');
        $status     = $this->input->post('status');
        $status     = ($status==300) ? 1 : $status;
        $html       = '';

        $qry_appasset = $this->db->select(
            '
                cd.sysid,
                cd.lastname,
                cd.firstname,
                cd.addrspec,
                cd.gdlbid,
                cd.status,
                oh.dateupdated,
                am.desc
            '
        )
            ->from('application_customers_details AS cd')
            ->join('assets_main_owner_history AS oh', 'oh.ownerid = cd.sysid AND oh.ownertype = 320')
            ->join('assets_main AS am', 'am.sysid = oh.assetid', 'left')
            ->where(array('cd.status' => $status))
            ->get();
        if($qry_appasset->num_rows()>0) {
            $html .= '<table class="table table-condensed table-hover table-bordered tbl-sm">';
            $html .= '<thead>';
            $html .= '<th>No.</th>';
            $html .= '<th>Customer Name</th>';
            $html .= '<th>Address</th>';
            $html .= '<th>Meter Serial</th>';
            $html .= '<th>GDLB</th>';
            $html .= '<th>Date Time</th>';
            $html .= '<th>Reading</th>';
            $html .= '<th>Remarks</th>';
            $html .= '</thead>';
            $num = 1;
            foreach($qry_appasset->result() as $row) {

                if($viewtype==0) {
                    $status = ($row->status==1) ? 300: $row->status;
                    $reading = ($row->status==361) ? '<input class="form-control input-sm inline" name="reading[]" placeholder="Init. Read"/>' : 'N/A';
                    $check = ($row->status==362) ? '<button class="btn btn-success btn-xs"><i class="fa fa-link"></i></button>' : '<button class="btn btn-default btn-xs"><i class="fa fa-unlink"></i></button>';
                    $check .= ($row->status==361) ? '<button class="btn btn-default btn-xs" data-id="'.$row->sysid.'" id="btn_unrelease"><i class="fa fa-reply"></i></button>' : '<button class="btn btn-default btn-xs" data-id="'.$row->sysid.'" id="btn_releasethis"><i class="fa fa-check"></i></button>';
                    $data['list'][] = array(
                        'num' => $num++,
                        'custname' => $row->lastname . ', ' . $row->firstname,
                        'address' => $row->addrspec,
                        'mtrserial' => $row->desc,
                        'gdlb' => get_gdlb_name($row->gdlbid),
                        'date' => $row->dateupdated,
                        'status' => get_types_label_format($status),
                        'reading' => $reading,
                        'check' => $check,
                    );
                }

                if($viewtype==1) {
                    $html .= '<tr>';
                    $html .= '<td>'.$num++.'</td>';
                    $html .= '<td>'.$row->lastname . ', ' . $row->firstname.'</td>';
                    $html .= '<td>'.$row->addrspec.'</td>';
                    $html .= '<td>'.$row->desc.'</td>';
                    $html .= '<td>'.get_gdlb_name($row->gdlbid).'</td>';
                    $html .= '<td>'.$row->dateupdated.'</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '</tr>';
                }

                if($viewtype==2) {
                    $data['updid'][] = $row->sysid;
                    $update_arr = array('status' => 361);

                    $this->db->where("sysid", $row->sysid);
                    $this->db->update('application_customers_details', $update_arr);
                    $data['err'][] = $this->db->_error_message();
                }
            }
            $html .= '</table>';
            $data['html'] = $html;
        }
        return json_encode($data);
    }

    function unrelease_meter() {
        $sysid = $this->input->post('sysid');
        $this->db->where('sysid', $sysid);
        $upd = $this->db->update('application_customers_details', array('status'=>1));
        return $upd;
    }


    function release_this_meter() {
        $sysid = $this->input->post('sysid');
        $this->db->where('sysid', $sysid);
        $upd = $this->db->update('application_customers_details', array('status'=>361));
        return $upd;
    }

    function edit_owner() {
        $data = array();

        $type = $this->input->post('type');
        $requestid = $this->input->post('requestid');
        $lastname = $this->input->post('lastname');
        $firstname = $this->input->post('firstname');
        $middlename = $this->input->post('middlename');
        $birthdate = date('Y-m-d', strtotime($this->input->post('birthdate')));
        $gender = $this->input->post('gender');
        $marital = $this->input->post('marital');
        $contactno = $this->input->post('contactno');
        $district = $this->input->post('district');
        $barangay = $this->input->post('barangay');
        $addrspec = $this->input->post('addrspec');
        $landmark = $this->input->post('landmark');
        $city = $this->input->post('city');
        $zipcode = $this->input->post('zipcode');

        $customer = $this->db->select('sysid')->from('person p')
            ->where(array(
                'lastname' => $lastname ,
                'firstname' => $firstname,
                'status' => 1
            ))->get()->row();

        if ($customer) {
            $data['Person'] = 'Existing';
            $person_id = $customer->sysid;
        } else {
            $insert_arr = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'middlename' => $middlename,
                'birthdate' => $birthdate,
                'gender' => $gender,
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );
            $insert = $this->db->insert('person',$insert_arr);
            if ($insert) {
                $data['New Person'] = 'Added';
                $person_id = $this->db->insert_id();
                $address_array = array(
                    'personid' => $person_id,
                    'addrspec' => $addrspec,
                    'addrdist' => $district,
                    'addrcity' => $city,
                    'addrbrgy' => $barangay,
                    'addrlandmark' => $landmark,
                    'addrcountry' => 175,
                    'zipcode' => $zipcode,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('person_address_matrix',$address_array);

                $contact_arr = array(
                    'personid' => $person_id,
                    'contactstring' => $contactno,
                    'types' => 1051,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('person_contact_matrix',$contact_arr);
            }
        }

        $data['type'] = $type;
        if ($type == 1) {
            $data['task'] = 'Change Owner';
            $update_arr = array(
                'personid' => $person_id,
                'contactmobile' => $contactno,
                'contactphone' => $contactno,
                //'addrspec' => $addrspec,
                //'distid' => $district,
                //'city' => $city,
                //'barangay' => $barangay,
                'marital' => $marital,
                'gender' => $gender,
                'updatedby' => user_id(),
            );
            $this->db->where('sysid',$requestid);
            $update = $this->db->update('application_customers_details',$update_arr);

            if ($update) {
                $info_arr = get_application_details($requestid);

                if($info_arr->info) {
                    $info = $info_arr->info;
                    $person = get_person_info($info->personid);
                    $marital = select_marital($person->info->marital);
                    $marital_text = '<span class="label " style="background: ' . $marital->color . ' !important; font-size: 14px !important; padding: 2px 2px !important;">' . $marital->text . '</span>';
                    $appname = $info->lastname . ', ' . $info->firstname;
                    $status = ($person->qry) ? gender($person->info->genderid) . ', ' . $person->info->birthdate . ' ' . $marital_text : '';
                    $address = get_district_name($info->distid) . ', ' . $info->addrspec;
                    $contact = ($person->info->mobilephone != 'None') ? $person->info->mobilephone : (($person->info->telephone != 'None') ? $person->info->telephone : 'None');

                    $data['owner'] = array(
                        'appname' => $appname,
                        'status' => $status,
                        'address' => $address,
                        'contact' => $contact,
                    );
                }
            }
        }

        if ($type == 2) {
            $data['task'] = 'Add Owner';
            $acs_array = array(
                'appid' => $requestid,
                'personid' => $person_id,
            );
            $acs_instert = $this->db->insert('application_customers_subowners',$acs_array);

            if ($acs_instert) {
                $subownerid = $this->db->insert_id();

                $sub_owner = $this->db->select('personid')
                    ->from('application_customers_subowners')
                    ->where(array('sysid' => $subownerid, 'status' => 1))->get()->row();

                if ($sub_owner) {
                    $subowner = get_person_info($sub_owner->personid);
                    if ($subowner->info) {
                        $so = $subowner->info;
                        $data['subowners'][] = array(
                            'name' => $so->lastname . ', ' . $so->firstname,
                            'address' => $so->district . ', ' . $so->addrspec,
                            'contact' => $so->lastname . ', ' . $so->firstname,
                            'remove' => '<a href="javascript:;" id="remove_owner" data-id="'.$subownerid.'"><i class="fa fa-minus-circle text-danger"></i>',
                        );
                    }
                }
            }
        }
        $data['dataid'] = $requestid;
        return json_encode($data);
    }

    function remove_subowner () {
        $data = array();
        $s_ownerid = $this->input->post('id');

        $data['remove'] = false;
        $this->db->where('sysid',$s_ownerid);
        $remove_qry = $this->db->update('application_customers_subowners',array('status' => 0,'updatedby' => user_id()));

        if ($remove_qry) {
            $data['Removal'] = 'Success';
            $data['remove'] = true;
        }

        return json_encode($data);
    }

    function dt_sub_owners () {
        $data = array();
        $appid = $this->input->post('appid');

        $sub_owner_qry = $this->db->select('sysid, personid')
            ->from('application_customers_subowners')
            ->where(array('appid' => $appid, 'status' => 1))->get();

        if ($sub_owner_qry->num_rows() > 0) {
            $num = 1;
            foreach ($sub_owner_qry->result() AS $row) {
                $subowner = get_person_info($row->personid);
                if ($subowner->info) {
                    $so = $subowner->info;
                    //$tell = ($so->telephone) ? $so->telephone : '#';
                    //$cell = ($so->mobilephone) ? $so->telephone : '#';
                    $data['list'][] = array(
                        'num' => $num++,
                        'name' => $so->lastname . ', ' . $so->firstname,
                        'address' => $so->district . ', ' . $so->addrspec,
                        'contact' => $so->telephone . ' / ' . $so->mobilephone,
                        'remove' => '<a href="javascript:;" class="btn btn-danger btn-xs inline" id="remove_owner" data-id="'.$row->sysid.'"><i class="fa fa-times text-danger"></i>',
                    );
                }
            }
        }
        $data['appid'] = $appid;

        return json_encode($data);
    }

    function uploadFile()
    {
        $resultArr = array();
        $file = $this->input->post('file');
        $ticket = $this->input->post('ticket');
        $fileName = $this->input->post('filename');

        $upload_path = FCPATH . 'uploads/applications/' . $ticket . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path,0777,true);
        }
        $config = array(
            'max_size' => '1024000',
            'allowed_types' => 'csv',
            'upload_path' => $upload_path,
            'file_name' => $fileName
        );

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if(!$this->upload->do_upload($file))
        {
            $resultArr['success'] = false;
            $resultArr['error'] = $this->upload->display_errors();
        }   else    {
            $resArr = $this->upload->data();
            $resultArr['success'] = true;
            $resultArr['path'] = $upload_path.$resArr['file_name'];
        }
        return $resultArr;
    }

    function upload_online_application_bak() {
        $data = array();
        $uploaded = array();
        $qry = false;
        $msg = '';

        if(isset($_FILES["appfiledrop"])) {
            $new_name = $_FILES["appfiledrop"]['name'];
            $data['newname'] = $new_name;

            $filenamexplode = explode("." , $new_name);

            $new_name = $filenamexplode[0].'.'.strtolower($filenamexplode[1]);
            $data['filename'] = $new_name;
            $dataid = $this->input->post('dataid');


            $file_directory = FCPATH . "uploads/attachments/cad/online/";
            //  $file_directory = "net use z:\\\\172.20.224.15cad\\attachedments\\" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";

            // ###############################################
            // CREATE DIRECTORY
            $config['upload_path'] = $file_directory;
            $config['allowed_types'] = 'text/comma-separated-values|application/csv|application/excel|application/vnd.ms-excel|application/vnd.msexcel|text/anytext';
            $config['max_size'] = 100000;
            $config['max_width'] = 5000;
            $config['max_height'] = 8000;
            $config['encrypt_name'] = FALSE;
            $config['file_name'] = str_replace(' ', '_', trim($new_name));
            $this->load->library('upload', $config);

            // ###############################################
            // CREATE DIRECTORY

            if (!is_dir($file_directory)) {
                mkdir($file_directory, 0777, TRUE);
                chmod($file_directory, 0777);
            } else {
                chmod($file_directory, 0777);
            }

            // ###############################################

            if (!$this->upload->do_upload('reqfiledrop')) {
                $msg = array('error' => $this->upload->display_errors() .  ' ' .$file_directory) ;
            } else {
                $msg = array('upload_data' => $this->upload->data());
                $qry = true;
                $csv = array_map('str_getcsv', file($file_directory.$new_name));
                $uploaded = array_combine($csv[0],$csv[1]);
            }
        }else{
            $msg = 'Drop the file again!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['content'] = $uploaded;

        return json_encode($data);
    }

    function load_csv($file) {
        $data = array();

        $file_directory = FCPATH . "uploads/attachments/cad/online/";
        $csv = array_map('str_getcsv', file($file));
        $uploaded = array_combine($csv[0],$csv[1]);

        $data['content'] = $uploaded;

        return $uploaded;
    }

    function upload_online_application()
    {
        $data = array();
        $msg = '';
        $qry = false;
        $func = 'error';
        $num_exist = 0;
        $num_not_exist = 0;
        $totalrecords = 0;
        $file_data = array();
        $file_details = array();
        $file_content = array();

        $cols_name = array();

        $html_details = '';
        $content_html = '';

        if (isset($_FILES["datafile"])) {

            $file_info = pathinfo($_FILES["datafile"]["name"]);
            $filetype = $file_info["extension"];
            if ($filetype == 'csv') {
                $temp = explode(".", $_FILES["datafile"]["name"]);
                $file_directory = FCPATH . "uploads/attachments/cad/online/" . $temp[0] . "/";

                $qry_time = $this->db->query("SELECT HOUR(NOW()) AS HRS, MINUTE(NOW()) AS MIN, SECOND(NOW()) AS SEC")->row();
                $hrs = str_pad($qry_time->HRS, 2, '0', STR_PAD_LEFT);
                $min = str_pad($qry_time->MIN, 2, '0', STR_PAD_LEFT);
                $sec = str_pad($qry_time->SEC, 2, '0', STR_PAD_LEFT);
                $hour_num = $hrs . $min . $sec;

                $newfilename = $temp[0] . '_' . date('Y') . str_pad(date('m'), 2, '0', STR_PAD_LEFT) . str_pad(date('d'), 2, '0', STR_PAD_LEFT) . $hour_num . '.' . end($temp);

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


                if (!is_dir(FCPATH . "uploads/attachments/cad/online/" . $temp[0] . "/")) {
                    mkdir(FCPATH . "uploads/attachments/cad/online/" . $temp[0] . "/", 0755, TRUE);
                    chmod(FCPATH . "uploads/attachments/cad/online/" . $temp[0] . "/", 0755);
                }


                if ($this->upload->do_upload('datafile')) {

                    $file_content = $this->load_csv($file_directory . $newfilename);
                    $content_header = array_keys($file_content);
                    foreach ($content_header as $col) {
                        $content_html .= '<input class="hidden" name="'.$col.'" value="'.$file_content[$col].'" />';
                    }

                    $file_data = (object)$file_content;

                    //Detailed names
                    $suffix = ($file_data->suffix) ? ', '.select_person_title(70,$file_data->suffix) : '';
                    $prefix = ($file_data->prefix) ? select_person_title(71,$file_data->prefix).' ' : '';
                    $name = $prefix.$file_data->lastname.', '.$file_data->firstname.' '.$file_data->middlename.$suffix;
                    $address = address_name('brgy',$file_data->addressbrgy).', '.address_name('dist',$file_data->addressdistrict).', '.address_name('city',$file_data->addresscity);
                    $accaddress = address_name('brgy',$file_data->accountbrgy).', '.address_name('dist',$file_data->accountdistrict).', '.address_name('city',$file_data->accountcity);


                    $file_details = array(
                        'Full Name' => $name,
                        'Date of Birth' => date_format(date_create($file_data->dateofbirth),'F j, Y'),
                        'Marital Status' => select_marital($file_data->maritalstatus)->text,
                        'Landline' => $file_data->contactlandline,
                        'Mobile' => $file_data->contactmobile,
                        'E-Mail' => $file_data->contactemail,
                        'Address' => $address,
                        'Address Spec' => $file_data->addressfull,
                        'TIN' => $file_data->tinno,
                        'SSS' => $file_data->sssno,
                        'Purpose' => get_rate_type($file_data->connpurpose),
                        'Connection Type' => get_types_name($file_data->conntype)->names,
                        'Owner Type' => get_types_name($file_data->ownertype)->names,
                        'Location Type' => get_types_name($file_data->propertyowner)->names,
                        'account address' => $accaddress,
                        'account address spec' => $file_data->accountaddressfull,
                        'Account landline' => $file_data->accountlandline,
                        'account mobile' => $file_data->accountmobile,
                        'account e-mail' => $file_data->accountemail,
                    );

                    $cols_name = array_keys($file_details,0);
                    //$data['columns'] = $cols_name;

                    $html_details .= '<ul class="list-group summary column no-border">';
                    foreach($cols_name as $crow) {
                        $html_details .= '<li class="list-group-item">';
                        $html_details .= '<span class="col-md-4 label-name">'.strtoupper($crow).'</span>';
                        $html_details .= '<span class="col-md-8 label-default">'.$file_details[$crow].'</span>';
                        $html_details .= '</li>';
                    }
                    $html_details .= '</ul>';

                    $msg = 'Moved!';
                    $qry = true;
                    $func = 'success';
                } else {
                    $msg = 'Moved Error: ' . $this->upload->display_errors('<p>', '</p>');
                    $qry = false;
                    $func = 'warning';

                }

                $data['filename'] = $newfilename;
            } else {
                $msg = 'Not a CSV file.';
            }
        } else {
            $msg = 'File not found!';
        }


        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;

        $data['content'] = $file_content;
        $data['details'] = $html_details;
        $data['form'] = $content_html;

        return json_encode($data);

    }

    /*
    function save_online_application() {

        // ################################################
        // VARIABLES

        $personid = 0;
        $insert = true;
        $qry = false;
        $data = array();
        $func = 'error';
        $inputs_arr = $this->input->post();

        $essrno = $this->input->post('essrno');
        $ticketno = $this->input->post('ticketno');
        $tickethash = $this->input->post('tickethash');
        $lastname = $this->input->post('lastname');
        $firstname = $this->input->post('firstname');
        $middlename = $this->input->post('middlename');
        $suffix = $this->input->post('suffix');
        $prefix = $this->input->post('prefix');
        $gender = $this->input->post('gender');
        $dateofbirth = $this->input->post('dateofbirth');
        $maritalstatus = $this->input->post('maritalstatus');
        $contactlandline = $this->input->post('contactlandline');
        $contactmobile = $this->input->post('contactmobile');
        $contactemail = $this->input->post('contactemail');
        $addresscity = $this->input->post('addresscity');
        $addressdistrict = $this->input->post('addressdistrict');
        $addressbrgy = $this->input->post('addressbrgy');
        $addressfull = $this->input->post('addressfull');
        $tinno = $this->input->post('tinno');
        $sssno = $this->input->post('sssno');
        $connpurpose = $this->input->post('connpurpose');
        $conntype = $this->input->post('conntype');
        $ownertype = $this->input->post('ownertype');
        $propertyowner = $this->input->post('propertyowner');
        $accountlandline = $this->input->post('accountlandline');
        $accountmobile = $this->input->post('accountmobile');
        $accountemail = $this->input->post('accountemail');
        $accountcity = $this->input->post('accountcity');
        $accountdistrict = $this->input->post('accountdistrict');
        $accountbrgy = $this->input->post('accountbrgy');
        $accountaddressfull = $this->input->post('accountaddressfull');
        $datecreated = $this->input->post('datecreated');
        $dateupdated = $this->input->post('dateupdated');
        $createdby = $this->input->post('createdby');
        $updatedby = $this->input->post('updatedby');
        $status = $this->input->post('status');

        //INPUT EXISTING ACCOUNTS
        $acctra = $this->input->post('acctra');
        $acctex = $this->input->post('acctex');

        $this->db->trans_begin();

        $ticket_qry = $this->db->select('atr.tickethash, acd.`status` appstatus, acd.essrno')
            ->from('application_customers_online_ticket_ref as atr')
            ->join('application_customers_details as acd','acd.sysid = atr.appid and acd.`status` != 0','left')
            ->where('atr.`status`',1)
            ->get()->row();

        if ($ticket_qry) {
            $insert = false;
            $msg = 'Application already uploaded with ESSR# '.$ticket_qry->essrno.'.';
        } else {
            $qry_person = $this->db->select('p.sysid, p.firstname, p.middlename, p.lastname, pt.titleid')
                ->from('person as p')
                ->join('person_title as pt', 'pt.personid = p.sysid', 'left')
                ->where(array(
                    'p.lastname' => $lastname,
                    'p.middlename' => $middlename,
                    'p.firstname' => $firstname,
                ))->get()->row();
            $data['add_person'] = $this->db->last_query();

            if ($qry_person) {
                $person_id = $qry_person->sysid;
                $personid = $qry_person->sysid;
            } else {
                $ins_person_arr = array(
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'birthdate' => $dateofbirth,
                    'gender' => $gender,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('person', $ins_person_arr);
                $person_id = $this->db->insert_id();
                if ($suffix != '') {
                    $title_array = array(
                        'personid' => $person_id,
                        'titleid' => $suffix
                    );
                    $this->db->insert('person_title', $title_array);
                }

                if ($prefix != '') {
                    $title_array = array(
                        'personid' => $person_id,
                        'titleid' => $prefix
                    );
                    $this->db->insert('person_title', $title_array);
                }

                $address_array = array(
                    'personid' => $person_id,
                    'addrspec' => $addressfull,
                    'addrdist' => $addressdistrict,
                    'addrbrgy' => $addressbrgy,
                    //'addrlandmark' => $landmark,
                    'addrcountry' => 175,
                    //'zipcode' => $zipcode,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $address = $this->db->insert('person_address_matrix', $address_array);

                if ($contactlandline != '') {
                    $contact_arr = array(
                        'personid' => $person_id,
                        'contactstring' => $contactlandline,
                        'types' => 1049,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $contact = $this->db->insert('person_contact_matrix', $contact_arr);
                }

                if ($contactmobile != '') {
                    $contact_arr = array(
                        'personid' => $person_id,
                        'contactstring' => $contactmobile,
                        'types' => 1051,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $contact = $this->db->insert('person_contact_matrix', $contact_arr);
                }

                if ($contactemail != '') {
                    $contact_arr = array(
                        'personid' => $person_id,
                        'contactstring' => $contactemail,
                        'types' => 1053,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $contact = $this->db->insert('person_contact_matrix', $contact_arr);
                }
            }

            $ins_app_arr = array(
                'essrno' => $essrno,
                'personid' => $person_id,
                'addrspec' => $addressfull,
                'rateclassid' => $connpurpose,
                'distid' => $accountdistrict,
                'city' => $accountcity,
                'barangay' => $accountbrgy,
                'marital' => ($maritalstatus) ? $maritalstatus : 0,
                'gender' => $gender,
                'suffix' => ($suffix) ? $suffix : 0,
                'prefix' => ($prefix) ? $prefix : 0,
                'sss' => ($sssno) ? $sssno : 0,
                'tinno' => ($tinno) ? $tinno : 0,
                'contactmobile' => $accountmobile,
                'contactphone' => $accountlandline,
                'contactemail' => $accountemail,
                'apptype' => ($conntype) ? $conntype : 0,
                'jobtype' => 0,
                'existaccount' => ($acctex) ? $acctex : 0,
                'existlegalra' => ($acctra) ? $acctra : 0,
                'existperson' => ($personid) ? $personid : 0,
                'blacklisted' => 0,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'encodestart' => $datecreated,
                'types' => 1
            );
            $insert_app = $this->db->insert('application_customers_details', $ins_app_arr);
            $err_msg = ' | Details Qry.: ' . $this->db->_error_message();

            $err_msg_arr = array();

            if ($insert_app) {
                $insert_app_id = $this->db->insert_id();

                $app_ticket_arr = array(
                    'appid' => $insert_app_id,
                    'ticketno' => $ticketno,
                    'tickethash' => $tickethash,
                    'createdby' => user_id(),
                );

                $this->db->insert('application_customers_online_ticket_ref', $app_ticket_arr);

                $ins_subs_arr = array(
                    'appid' => $insert_app_id,
                    'classid' => $connpurpose,
                    'connid' => $conntype,
                    'owntypeid' => $ownertype,
                    'loctypeid' => $propertyowner,
                );
                $insert_subs = $this->db->insert('application_customers_subscriptions', $ins_subs_arr);
                $err_msg_arr[] = 'Subscription Qry.: ' . $this->db->_error_message();

                if ($insert_subs) {
                    $appreq = $this->db->select('reqid')
                        ->from('requirements_parameters')
                        ->where(array(
                            'statusid' => $conntype,
                            'typeid' => $ownertype,
                            'locid' => $propertyowner,
                            'status' => 1,
                        ))->get();
                    if ($appreq->num_rows() > 0) {
                        foreach ($appreq->result() as $reqid) {
                            $insert_req = array(
                                'appid' => $insert_app_id,
                                'reqid' => $reqid->reqid
                            );
                            $this->db->insert('application_customers_requirements', $insert_req);
                            $err_msg_arr[] = $this->db->_error_message();
                        }
                    }

                    $transactiondesc = strtoupper($firstname . ' ' . $middlename . ' ' . $lastname);
                    $insert_init_deposit = insert_application_charges(163, 250, $insert_app_id, 35, 1); // INITIAL DEPOSIT
                    $insert_trns_trail = create_transaction_trails('CUST-NEW', $transactiondesc, 35, $insert_app_id);


                    $msg_imp = implode(', ', $err_msg_arr);

                    if ($insert_init_deposit->qry != true) {
                        $insert = false;
                        $msg = 'Error Insert: Initial Deposit! | ' . $msg_imp;
                    }

                    if ($insert_trns_trail != true) {
                        $insert = false;
                        $msg = 'Error Insert: Transaction Trails! | ' . $msg_imp;
                    }

                    $data['chrgarr'] = $insert_init_deposit;
                }
            } else {
                $msg = 'Error saving details!' . $err_msg;
                $insert = false;
            }
        }

        if ($insert) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'New Application saved!';
            $func = 'success';
        } else {
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['input'] = $inputs_arr;
        $data['ticket'] = $ticket_qry;
        echo json_encode($data);
    }
    */

    function auto_assign_requirements() {
        $data = array();
        $appid = $this->input->post('dataid');

        $app_path = 'uploads/attachments/cad/applications/'.str_pad($appid, 6, "0", STR_PAD_LEFT);

        $upload_path = FCPATH . $app_path;

        $files = glob($app_path.'/*.{gif,jpg,png,pdf,doc,docx}', GLOB_BRACE);

        //$list = ($files) ? true : false;

        //echo '<pre>';
        //print_r($list);
        //exit();

        $count = count($files);

        if ($count > 0) {
            $success = 0;
            foreach ($files as $file) {
                $file_list = explode('/', $file);
                $filename = end($file_list);
                $isreq = strpos(strtolower($filename),strtolower('REQ'));
                //$data['filelist'][] = $file_list;
                $data['filenames'][] = $filename;
                //$data['isreq'][] = $isreq;
                if ($isreq !== false) {
                    $file_req = explode('_', $filename);
                    $reqcode = $file_req[0];
                    //$data['file_req'][] = $file_req;
                    //$data['reqcode'][] = $reqcode;


                    $attachment_id = $this->db->select('acr.sysid')
                        ->from('application_customers_requirements AS acr')
                        ->join('prime_requirement_parameters AS prp', 'acr.reqid = prp.sysid', 'inner')
                        ->join('application_customers_details AS acd', 'acr.appid = acd.sysid', 'inner')
                        ->where(array('prp.codes' => $reqcode, 'acd.sysid' => $appid))->get()->row();

                    if ($attachment_id) {
                        $attid = $attachment_id->sysid;
                        $this->db->where(array("comply" => 0, "sysid" => $attid));
                        $updaterequirements = $this->db->update("application_customers_requirements", array('comply' => 1));
                        //$data['requirements'][] = $this->db->last_query();


                        $insarr = array(
                            'attachmentid' => $attid,
                            'fileurl' => $file,
                            'complydate' => date('Y-m-d h:i:s'),
                            'complyby' => user_id(),
                            'createdby' => user_id(),
                            'updatedby' => user_id()
                        );
                        $addattachments = $this->db->insert("application_customers_attachments", $insarr);
                        //$data['attachments'][] = $this->db->last_query();

                        if ($updaterequirements && $addattachments) {
                            $success++;
                        }
                    }
                }
            }

            if ($success == $count) {
                $msg = 'All attachments has been tagged successfully.';
                $func = 'success';
                $qry = true;
            }

            if ($success < $count) {
                $msg = $success.' attachment(s) has been tagged successfully.';
                $func = 'warning';
                $qry = true;
            }

            if ($success == 0) {
                $msg = 'None of the attachments were tagged.';
                $func = 'error';
                $qry = true;
            }
        } else {
            $this->db->trans_status();
            $msg = 'No attachments uploaded';
            $func = 'info';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['files'] = $files;
        $data['id'] = $appid;
        $data['upath'] = $upload_path;
        return json_encode($data);
    }

    function check_online_ticket_status() {
        $data = array();
        $ticketno = $this->input->post('ticketno');
        $qry_check = $this->db->select()
            ->from('application_customers_online_ticket_ref')
            ->where(array('ticketno' => $ticketno, 'status' => 1))
            ->get()->row();
        $qry = false;
        if($qry_check) {
            $qry = true;
            $data['res'] = $qry_check;
        }
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function submit_online_row_data() {
        $res_input = $this->input->post('res');

        $ticket_no = $this->input->post('ticketno');
        $ticket_hash = $this->input->post('tickethash');

        $person_arr = create_person_data();

        $err_msg_arr = array();

        $req_array = array();

        $moduleid = 35;
        $person_id = $person_arr->personid;
        $encodestart = date('Y-m-d H:i:s');
        $apptype = $this->input->post('apptype');
        $jobtype = $this->input->post('jobtype');
        $essrno = $this->input->post('essrno');
        $suffix = $this->input->post('suffix');
        $prefix = $this->input->post('prefix');
        $gender = $this->input->post('gender');
        $marital = $this->input->post('marital');
        $phone = $this->input->post('contactlandline');
        $mobile = $this->input->post('contactmobile');
        $email = $this->input->post('contactemail');
        $addrcity = $this->input->post('addrcity');
        $addrdistrict = $this->input->post('addrdistrict');
        $addrbrgy = $this->input->post('addressbrgy');

        $acctrate = $this->input->post('connpurpose');
        $accttype = $this->input->post('conntype');
        $ownertype = $this->input->post('ownertype');
        $loctype = $this->input->post('propertyowner');

        $addrspecific = $this->input->post('addressfull');
        $tinno = $this->input->post('tin');
        $sss = $this->input->post('sss');

        $this->db->trans_begin();
        if( $person_id > 0 ) {
            // INSERT APPLICATION DETAILS
            $ins_app_arr = array(
                'essrno' => $essrno,
                'personid' => $person_id,
                'addrspec' => $addrspecific,
                'rateclassid' => $acctrate,
                'distid' => $addrdistrict,
                'city' => $addrcity,
                'barangay' => $addrbrgy,
                'marital' => ($marital) ? $marital : 0,
                'gender' => $gender,
                'suffix' => ($suffix) ? $suffix : 0,
                'prefix' => ($prefix) ? $prefix : 0,
                'sss' => ($sss) ? $sss : 0,
                'tinno' => ($tinno) ? $tinno : 0,
                'contactmobile' => $mobile,
                'contactphone' => $phone,
                'contactemail' => $email,
                'apptype' => ($apptype) ? $apptype : 0,
                'jobtype' => ($jobtype) ? $jobtype : 0,
                // 'existaccount' => $acctex,
                // 'existlegalra' => $acctra,
                // 'existperson' => ($personid) ? $personid : 0,
                'blacklisted' => 0,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'encodestart' => $encodestart,
                'moduleid' => 35,
                'types' => 1
            );
            $insert_app = $this->db->insert('application_customers_details', $ins_app_arr);
            $insert_app_id = $this->db->insert_id();
            $err_msg_arr[] = ' | Details Qry.: ' . $this->db->_error_message();

            if($insert_app) {
                $ins_subs_arr = array(
                    'appid' => $insert_app_id,
                    'classid' => $acctrate,
                    'connid' => $accttype,
                    'owntypeid' => $ownertype,
                    'loctypeid' => $loctype,
                );
                $insert_subs = $this->db->insert('application_customers_subscriptions', $ins_subs_arr);
                $err_msg_arr[] = 'Subscription Qry.: ' . $this->db->_error_message();

                $req_array = json_decode($this->get_requirements_list($ownertype, $accttype, $loctype));
                if (isset($req_array->list) && count($req_array->list) > 0) {
                    foreach ($req_array->list as $rrow) {
                        $insert_req = array(
                            'appid' => $insert_app_id,
                            'reqid' => $rrow->sysid
                        );
                        $this->db->insert('application_customers_requirements', $insert_req);
                        $err_msg_arr[] = $this->db->_error_message();
                    }
                }

                $transactiondesc = strtoupper($person_arr->personname);
                $insert_init_deposit = insert_application_charges(163, 250, $insert_app_id, $moduleid, 1); // INITIAL DEPOSIT
                $insert_trns_trail = create_transaction_trails('CUST-NEW', $transactiondesc, $moduleid, $insert_app_id);

                if($insert_trns_trail && $insert_init_deposit) {
                    $ins_arr = array(
                        'appid' => $insert_app_id,
                        'ticketno' => $ticket_no,
                        'tickethash' => $ticket_hash,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $this->db->insert('application_customers_online_ticket_ref', $ins_arr);


                    $data = db_trans($this->db);
                }
            }
        }

        $data['errmsg'] = $err_msg_arr;
        $data['req'] = $req_array;
        $data['person'] = $person_arr;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_additional_requirements() {
        $data = array();
        $app_reqs = array();
        $appid = $this->input->post('data');
        $app_req_qry = $this->db->select('reqid')
            ->from('application_customers_requirements')
            ->where(array('appid' => $appid , 'status' => 1))
            ->get();

        if ($app_req_qry->num_rows() > 0) {
            foreach ($app_req_qry->result() as $row) {
                $app_reqs[] = $row->reqid;
            }
            $this->db->where_not_in('sysid',$app_reqs);
        }

        $req_qry = $this->db->select('sysid,names')
            ->from('prime_requirement_parameters')
            ->get();

        if ($req_qry->num_rows() > 0) {
            foreach ($req_qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names,
                );
            }
        }

        return json_encode($data);
    }

    function add_requirement() {
        $data = array();
        $appid = $this->input->post('appid');
        $reqid = $this->input->post('reqid');
        $msg = '';
        $title = '';
        $func = '';
        $qry = false;

        if(is_array($reqid)) {
            $req_data = 'Is an Array';
            foreach($reqid as $rrow) {
                $ins_arr = array(
                    'appid' => $appid,
                    'reqid' => $rrow
                );

                $insert = $this->db->insert('application_customers_requirements', $ins_arr);
            }
        }else {
            $req_data = 'Is not an Array';
            $ins_arr = array(
                'appid' => $appid,
                'reqid' => $reqid
            );

            $insert = $this->db->insert('application_customers_requirements', $ins_arr);
        }
        if ($insert) {
            $msg = 'Requirement(s) added.';
            $title = 'Success!';
            $func = 'success';
            $qry = true;
        } else {
            $msg = 'Failed to add requirement(s).';
            $title = 'Failed!';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['title'] = $title;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['inputs'] = $this->input->post();
        $data['reqdata'] = $req_data;

        return json_encode($data);
    }

    function check_contract($appid) {
        $comply_date = 'None';
        $attachment_id = $this->db->select('acr.sysid')
            ->from('application_customers_requirements AS acr')
            ->join('prime_requirement_parameters AS prp', 'acr.reqid = prp.sysid', 'inner')
            ->join('application_customers_details AS acd', 'acr.appid = acd.sysid', 'inner')
            ->where(array('prp.codes' => 'CONTRACT', 'acd.sysid' => $appid))->get()->row();

        if ($attachment_id) {
            $existing_cont = $this->db->select('sysid , complydate')
                ->from('application_customers_attachments')
                ->where(array('attachmentid' => $attachment_id->sysid , 'status' => 1))
                ->get()->row();

            if ($existing_cont) {
                $comply_date = date_formating($existing_cont->complydate, 'Y-m-d H:i:s', 'F j, Y');
            }
        }
        return $comply_date;
    }

    function get_customer_requirements()
    {
        $dataid = $this->input->post('dataid');
        //$dataid = $this->input->post('upload');

        $app_info = application_info($dataid);

        $q = $this->db->select('
                prp.sysid AS PRPSYSID, 
                prp.codes AS PRPCODES, 
                prp.names AS PRPNAMES, 
                prp.desc AS PRPDESC, 
                cr.comply AS COMPLY,
                cr.sysid AS CRSYSID
                ')
            ->from('application_customers_requirements AS cr')
            ->join('prime_requirement_parameters AS prp', 'prp.sysid = cr.reqid','left')
            ->where(array('cr.appid' => $dataid, 'cr.status' => 1))
            ->get();
        $res_num = $q->num_rows();

        if ($res_num > 0) {
            foreach ($q->result() as $row) {
                //$assign = '<a class="btn btn-default inline" id="btn_add_app_req" data-toggle="ajax-modal" href="#frm_cad_assignfile" data-arr="dataid='.$dataid.',code='.$row->PRPCODES.'"><i class="fa fa-plus"></i> Assign</a>';
                $assign = '<a class="btn btn-default inline" id="btn_assign_app_req" data-toggle="ajax-modal" href="#frm_cad_assignfile" data-arr="'.$row->CRSYSID.'" data-view="'.$dataid.'"><i class="fa fa-plus"></i> Assign</a>';
                $status = ($row->COMPLY == 1) ? '<i class="fa fa-check text-success"></i>' : $assign;
                $control = '';
                $iscontract = ($row->PRPSYSID == 500 && !super_admin()) ? true : false;
                $control .= ($iscontract) ? '<i class="fa fa-check-circle"></i>' : (($row->COMPLY == 1) ? '<i class="fa fa-check-circle"></i>' : get_module_list_delete($app_info->moduleid, $row->PRPSYSID,'delete_requirement', true));

                if ($row->COMPLY == 1) {
                    $getattachments = $this->db->select("sysid,attachmentid,fileurl")
                        ->from("application_customers_attachments")
                        ->where(array("attachmentid" => $row->CRSYSID, "status" => 1))
                        ->get()->row();

                    if ($getattachments) {
                        $location = base_url().$getattachments->fileurl;
                        $text = '<a href="'.$location.'" class="cbp-caption cbp-lightbox iframe" data-title="Bolt UI<br>by Tiberiu Neamu">'.$row->PRPNAMES.'</a>';
                    }
                } else {
                    $text = $row->PRPNAMES;
                }

                $data['list'][] = array(
                    'num' => $row->PRPSYSID,
                    'text' => $text,
                    'status' => $status,
                    'control' => $control,
                );
            }
        }

        $data['res'] = $res_num;
        echo json_encode($data);
    }

    function get_application_param() {
        $data_arr = $this->input->post('data');
        $str = $data_arr['codes'];
        $q = $this->db->select('sysid, names, desc')
            ->from('prime_types_parameter')
            ->where(array('codes' => $str))
            ->get();
        $data = array();
        foreach ($q->result() as $row) {
            $codes = (trim($row->names) != '') ? get_acronym($row->names) . ' - ' : 'N/A - ';
            $desc = $row->desc;
            $data['list'][] = array('id' => $row->sysid, 'codes' => $codes, 'text' => $desc);
        }
        return json_encode($data);
    }

    function delete_requirement() {
        $data = array();
        $appid = $this->input->post('appid');
        $reqid = $this->input->post('reqid');
        $qry = false;

        $remove_req = $this->db->update('application_customers_requirements',array('status' => 0),array('appid' => $appid , 'reqid' => $reqid));

        if ($remove_req) {
            $qry = true;
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_requirements_list($acctype = false, $ownertype = false, $paytype = false) {

        $data = array();

        $acctype = ($acctype) ? $acctype : $this->input->post('acctype');
        $ownertype = ($ownertype) ? $ownertype : $this->input->post('ownertype');
        $paytype = ($paytype) ? $paytype : $this->input->post('paytype');


        if( $acctype > 0 ) {
            if ($acctype) { $this->db->where_in('pcar.acctype', $acctype); }
            if ($ownertype) { $this->db->where_in('pcar.ownertype', $ownertype); }
            if ($paytype) { $this->db->where_in('pcar.paytype', $paytype); }

            $q = $this->db->select('prp.sysid AS PRPSYSID, prp.names AS PRPNAMES, prp.desc AS PRPDESC')
                ->from('requirements_parameters AS pcar')
                ->join('prime_requirement_parameters AS prp', 'prp.sysid = pcar.reqid')
                ->group_by('prp.sysid')
                ->get();

            $res_num = $q->num_rows();

            if ($res_num > 0) {
                $num = 1;
                foreach ($q->result() as $row) {
                    $reqid = $row->PRPSYSID;
                    $data['list'][] = array(
                        'num' => $num++,
                        'sysid' => $reqid,
                        'name' => $row->PRPNAMES
                    );
                }
            }
        }

        $data['req_typeid'] = $acctype;
        $data['req_statid'] = $ownertype;
        $data['req_locsid'] = $paytype;
        return json_encode($data);
    }

    function add_requirement_list() {
        $data = array();
        $appid = $this->input->post('dataid');
        $app_reqs = array();

        $app = get_application_details($appid);

        $existing_reqs = $this->db->select('reqid')
            ->from('application_customers_requirements')
            ->where(array('appid' => $appid, 'status' => 1))
            ->get();

        if ($existing_reqs->num_rows() > 0) {
            foreach ($existing_reqs->result() as $row) {
                $app_reqs[] = $row->reqid;
            }

            if (count($app_reqs) > 0) {
                $this->db->where_not_in('prp.sysid',$app_reqs);
            }
        }

        $requirementslist = $this->db->select('
                prm.order,
                prm.type,
                prp.sysid,
                prp.codes,
                prp.names,
                prp.desc
        ')
            ->from('prime_requirement_parameters AS prp')
            ->join('prime_requirement_matrix AS prm','prp.sysid = prm.reqid AND prm.status = 1','left')
            ->where(array('prm.type' => $app->info->apptype,'prp.sysid != ' => 500, 'prp.status' => 1))
            ->order_by('prm.order ASC')
            ->get();

        if ($requirementslist->num_rows() > 0) {
            $num = 1;
            foreach ($requirementslist->result() AS $row) {
                $data['list'][] = array(
                    'num' => $num,
                    'code' => $row->codes,
                    'names' => $row->names,
                    'select' => '<button id="add_req_row" class="btn btn-info btn-xs inline" data-id="'.$row->sysid.'"><i class="fa fa-plus"></i> Add</button>'
                );
                $num++;
            }
        }

        return json_encode($data);
    }

    function send_final_list_requirements() {
        $data = array();
        $func = 'error';
        $msg = 'PHP: Error 404';
        $dataid = $this->input->post('dataid');

        $html = '';
        $info = application_info($dataid);

        if($info->q) {

            if($info->email != '') {
                $email = $info->email;

                $html .= '<html>';
                $html .= '<body>';
                $html .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
                $html .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
                $html .= '</div>';
                $html .= '<p>Hi, '.$info->firstname.',</p>';
                $html .= '<p>Bellow is the list of requirements you need to comply for your application request:</p>';
                $html .= '<ol type="1">';
                $qry = $this->db->select(
                    '
                        cr.reqid,
                        rp.codes,
                        rp.names
                    '
                )
                    ->from('application_customers_requirements AS cr')
                    ->join('prime_requirement_parameters AS rp', 'rp.sysid = cr.reqid')
                    ->where(array('cr.appid' => $dataid, 'cr.status' => 1))
                    ->get();
                if ($qry->num_rows() > 0) {
                    foreach ($qry->result() as $row) {
                        $html .= '<li>' . $row->codes . ' - ' . $row->names . '</li>';
                    }
                }
                $html .= '</ol>';
                $html .= '<p>Kindly reply and attach the documents.</p><br>';
                $html .= '<p><br>Thank you,</p>';
                $html .= '<div style="background: #ef582d; color: #fff; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; padding: 10px 10px;">';
                $html .= 'An enlightened past, a brighter future.';
                $html .= '</div>';
                $html .= '</body>';
                $html .= '</html>';

                $mail = mailer($email, $html, 'Customer Application', false, 'apply@panayelectric.com');
                if($mail) {
                    $msg = 'Email has been sent!';
                    $func = 'success';
                }else{
                    $msg = 'Error sending mail';
                    $func = 'warning';
                }
            }else{
                $msg = 'Email is not found!';
            }
        }

        $data['html'] = $html;
        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }

    function print_requirements($appid) {
        $list = '';
        $html = '';
        $info = application_info($appid);
        $requirements = $this->db->select('prp.codes, prp.names')
            ->from('application_customers_requirements AS pcr')
            ->join('prime_requirement_parameters AS prp','prp.sysid = pcr.reqid','left')
            ->where(array('pcr.appid' => $appid, 'pcr.status' => 1))
            ->order_by('pcr.reqid','ASC')
            ->get();

        if ($requirements->num_rows() > 0) {
            $num = 1;
            foreach ($requirements->result() AS $row) {
                $list .= '<li class="list-group-item">';
                $list .= '<span class="col-md-2" style="text-align: right">'.$num.'.</span><span class="col-md-10">'.$row->codes.' - '.$row->names.'</span>';
                $list .= '</li>';
                $num++;
            }
        }

        $user = user_info()->lastname.', '.user_info()->firstname;
        //echo get_user_role();

        $html .= '<html>';
        $html .= '<title>Application Requirements | '.$info->essrno.'</title>';
        $html .= '<link href="'.base_url().'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>';
        $html .= '<link href="'.base_url().'assets/global/css/components.css" rel="stylesheet" type="text/css"/>';
        $html .= '<link href="'.base_url().'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>';
        $html .= '<link href="'.base_url().'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>';
        $html .= '<link href="'.base_url().'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>';
        $html .= '<link href="'.base_url().'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>';
        $html .= '<style>body{margin: 0px 0px !important;  font-family: arial; background: #fff;}</style>';
        $html .= '<body>';
        $html .= peco_print_header(false, "Application Requirements", "", false);
        $html .= '<p>Hi, '.$info->firstname.',</p>';
        $html .= '<p>This is the list of requirements you need to comply for your application requests:</p>';
        $html .= '<ol class="list-group summary column no-border">';
        $html .= $list;
        $html .= '</ol>';
        $html .= '<p>Kindly submit all above listed to proceed with your application.</p>';
        $html .= '<p>Thank you very much</p>';
        $html .= '<br>';
        $html .= '<p>Respectfully,</p>';
        $html .= '<p>'.$user.'<br>'.get_user_role_info(user_id()).'</p>';

        print_r($html);
    }

    function add_customer_charges() {
        $data = array();
        $acctcode = $this->input->post('acctcode');
        $acctamt = str_replace(',', '', $this->input->post('acctamt'));
        $dataid = $this->input->post('dataid');
        $moduleid = $this->input->post('origin');
        $msg = '';
        $qry = false;
        $func = 'error';

        $ins_charges = insert_application_charges($acctcode, $acctamt, $dataid, $moduleid, 2);
        if($ins_charges->qry) {
            $qry    = true;
            $func   = 'success';
            $msg    = 'Services/Materials Added!';
        } else {
            $msg    = $ins_charges->errmsg;
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['dataid'] = $dataid;
        $data['moduleid'] = $moduleid;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }


    function print_inspection_list() {
        $data = array();

        $route = $this->input->post('route');
        $district = $this->input->post('district');
        $html = '';
        $list = '<tr><td colspan="5"><h1>No data to Print.</h1></td></tr>';

        if ($district && $district > 0) {
            $this->db->where('cd.distid',$district);
        }

        $list_qry = $this->db->select('cd.sysid, cd.essrno, cd.datecreated, cd.personid, cd.apptype')
            ->from('application_customers_details AS cd')
            ->join('transaction_request_main_trails AS rmt','rmt.dataid = cd.sysid','inner')
            ->where('rmt.stageid',$route)
            ->group_by('cd.sysid, cd.essrno, cd.datecreated, cd.personid, cd.apptype')
            ->get();

        if ($list_qry->num_rows() > 0) {
            $list = '';
            $num = 1;
            foreach ($list_qry->result() AS $row) {
                $qry_trails_last = $this->db->query("
                    SELECT rmt.sysid, rmt.datecreated, rmt.createdby, rmt.stageid, rmt.dataid, rmt.datecreated AS logdate
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    WHERE rmt.dataid = {$row->sysid} 
                    -- AND rm.flowid = 2
                    ORDER BY rmt.datecreated DESC
                ")->row();

                $show  = true;
                if ($qry_trails_last->stageid != $route) {
                    $show = false;
                }

                if ($show) {
                    $trstyle = ($num % 4 == 0) ? 'style="page-break-after: always;"' : '';
                    $info = application_info($row->sysid);
                    $name = ($info) ? $info->appname : 'N/A';
                    $address = ($info) ? $info->address : 'N/A';
                    $list .= '<tr>';
                    $list .= '<td align="center">' . $num++ . '</td>';
                    $list .= '<td style="margin-left: 25px">' . $name . '</td>';
                    $list .= '<td align="center">' . $row->essrno . '</td>';
                    $list .= '<td>' . $address . '</td>';
                    $list .= '<td align="center">_____________</td>';
                    $list .= '</tr>';
                    $list .= '<tr ' . $trstyle . '>';
                    $list .= '<td colspan="3" align="center">';
                    $list .= '<p>Load Inspection Details<br>Main Switch: _____ Amp</p>';
                    $list .= '<table width="100%" class="con_load" style="border: none !important;">';
                    $list .= '<thead>';
                    $list .= '<th>Equipment</th>';
                    $list .= '<th>Remarks</th>';
                    $list .= '<th>Wattage</th>';
                    $list .= '<th>Qty.</th>';
                    $list .= '<th>Total Watts</th>';
                    $list .= '</thead>';
                    $list .= '<tbody>';
                    for ($cnt = 0; $cnt < 9; $cnt++) {
                        $list .= '<tr>';
                        $list .= '<td style="padding-top: 10px !important;"></td>';
                        $list .= '<td style="padding-top: 10px !important;"></td>';
                        $list .= '<td style="padding-top: 10px !important;"></td>';
                        $list .= '<td style="padding-top: 10px !important;"></td>';
                        $list .= '<td style="padding-top: 10px !important;"></td>';
                        $list .= '</tr>';
                    }
                    $list .= '<tr>';
                    $list .= '<td colspan="3"><b>Total</b></td>';
                    $list .= '<td></td>';
                    $list .= '<td></td>';
                    $list .= '</tr>';
                    $list .= '</tbody>';
                    $list .= '</table>';
                    /*
                    $list .= '<span style="width: 15% !important;">Count</span>';
                    $list .= '<span style="width: 30% !important">Equipment/Code</span>';
                    $list .= '<span style="width: 20% !important;">Remarks</span>';
                    $list .= '<span style="width: 10% !important;">Wattage</span>';
                    $list .= '<span style="width: 10% !important;">Qty.</span>';
                    $list .= '<span style="width: 15% !important;">Total</span>';
                    for ($cnt = 0; $cnt < 10; $cnt++) {
                        $list .= '<span style="width: 15px !important; border-bottom: #0a0a0a solid 1px; margin: 0px"></span>';
                        $list .= '<span style="width: 30% !important; border-bottom: #0a0a0a solid 1px; margin: 0px"></span>';
                        $list .= '<span style="width: 20% !important; border-bottom: #0a0a0a solid 1px; margin: 0px"></span>';
                        $list .= '<span style="width: 10% !important; border-bottom: #0a0a0a solid 1px; margin: 0px"></span>';
                        $list .= '<span style="width: 10% !important; border-bottom: #0a0a0a solid 1px; margin: 0px"></span>';
                        $list .= '<span style="width: 10% !important; border-bottom: #0a0a0a solid 1px; margin: 0px"></span>';
                        $list .= '<span style="width: 5% !important;">Watts</span><br><br>';
                    }*/
                    $list .= '</td>';
                    $list .= '<td colspan="2" align="center">';
                    $list .= '<span><b>Sketch</b></span>';
                    $list .= '</td>';
                    $list .= '</tr>';
                }
            }
        }


        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style type="text/css">';
        $html .= 'table, th, td {border: 1px solid black;}';
        $html .= 'table {border-collapse: collapse;}';
        $html .= 'body {font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $html .= 'td {vertical-align: top;}';
        $html .= 'tr {page-break-inside:avoid; page-break-after:auto }';
        $html .= 'thead { display:table-header-group }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= peco_print_header(user_id(),'Application Inspection List',date('m/d'),false);
        $html .= '<table width="100%" style="border: #0a0a0a solid 1px">';
        $html .= '<thead>';
        $html .= '<th>#</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>ESSR #</th>';
        $html .= '<th>Address</th>';
        $html .= '<th>Inspector</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= $list;
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</html>';

        $data['html'] = $html;
        return json_encode($data);
    }


    function submit_editable() {
        $data = array();
        $input  = $this->input->post();
        $name   = $input['name'];
        $value  = $input['value'];
        $pk     = $input['pk'];

        if($pk>0) {
            $app_arr = array(
                'essrno' => array('essrno','application_customers_details','sysid'),
                'phone' => array('contactphone','application_customers_details','sysid'),
                'mobile' => array('contactmobile','application_customers_details','sysid'),
                'email' => array('contactemail','application_customers_details','sysid'),
                'addressspec' => array('addrspec','application_customers_details','sysid'),
                'district' => array('distid','application_customers_details','sysid'),
                'servno' => array('servno','application_customers_details','sysid'),
                'gender' => array('gender','application_customers_details','sysid'),
                'marital' => array('marital','application_customers_details','sysid'),
            );

            if (array_key_exists($name,$app_arr)) {
                $person = $this->db->select('personid')
                    ->from('application_customers_details')
                    ->where(array('sysid' => $pk, 'status' => 1))
                    ->get()->row();

                if ($person) {
                    if ($name == 'gender') {
                        $this->db->update('person',array('gender' => $value,'updatedby' => user_id()),array('sysid' => $person->personid, 'status' => 1));
                    }

                    if ($name == 'marital') {
                        $marital_qry = $this->db->select('sysid')->from('persons_marital_status_logs')
                            ->where(array('personid' => $person->personid, 'status' => 1))
                            ->get()->row();

                        if ($marital_qry) {
                            $this->db->update('persons_marital_status_logs',array('status' => 0,'updatedby' => user_id()),array('sysid' => $marital_qry->sysid, 'status' => 1));
                            if ($this->db->affected_rows() > 0) {
                                $this->db->insert('persons_marital_status_logs',array('personid' => $person->personid, 'marital_status_id' => $value, 'createdby' => user_id()));
                            }
                        } else {
                            $this->db->insert('persons_marital_status_logs',array('personid' => $person->personid, 'marital_status_id' => $value, 'createdby' => user_id()));
                        }
                    }
                }

                $column_index = $app_arr[$name][2];
                $column_update = $app_arr[$name][0];
                $table = $app_arr[$name][1];
                $oldval = $this->db->select($column_update.', moduleid')
                    ->from($table)
                    ->where(array($column_index => $pk, 'status' => 1))
                    ->get()->row();
                $valueold = ($oldval && $oldval->$column_update) ? $oldval->$column_update : 'N/A';
                $moduleid = ($oldval && $oldval->moduleid) ? $oldval->moduleid : 'N/A';
                $this->db->trans_begin();
                $this->db->set('updatedby',user_id());
                $this->db->update($table,
                    array($column_update => $value),
                    array($column_index => $pk)
                );
                $data = db_trans($this->db);
                if ($data['qry'] == true) {
                    $audit_ins_arr = array(
                        'dataid' => $pk,
                        'moduleid' => $moduleid,
                        'valueold' => $valueold,
                        'valuenew' => $value,
                        'createdby' => user_id(),
                        'remarks' => 'CAD - Application info changed: '.$column_update
                    );
                    //$audit_ins = audit_insert($audit_ins_arr);
                    $audit_ins = true;
                }


            } else {
                $data['qry'] = false;
                $data['func'] = 'error';
                $data['msg'] = 'Error!';
            }
        }

        $data['inputs'] = $input;
        $data['audit'] = $audit_ins;
        return json_encode($data);
    }

    function override_amt() {
        $data = array();
        $sysid = $this->input->post('chargesid');
        $appid = $this->input->post('appid');
        $chargeid = $this->input->post('chargeid');
        $moduleid = $this->input->post('moduleid');
        $newamt = $this->input->post('newamt');
        $oldamt = $this->input->post('oldamt');
        $msg = '';
        $func = '';

        if (!$this->db->update('application_customers_charges',array('status'=>0),array('sysid' => $sysid))) {
            $msg = 'Failed to override charge amount.';
            $func = 'error';
        } else {
            $ins_charges = insert_application_charges($chargeid, $newamt, $appid, $moduleid, 1);
            $data['params'] = $ins_charges->params;
            if ($ins_charges->qry) {
                $logs_arr = array(
                    'appid' => $appid,
                    'moduleid' => $moduleid,
                    'oldchargeid' => $sysid,
                    'newchargeid' => $ins_charges->chargesid,
                    'createdby' => user_id(),
                );

                $this->db->insert('charges_override_log',$logs_arr);

                $audit_ins_arr = array(
                    'dataid' => $appid,
                    'moduleid' => $moduleid,
                    'valueold' => $oldamt,
                    'valuenew' => $newamt,
                    'createdby' => user_id(),
                    'remarks' => 'AM - Override charge amount.'
                );
                // audit_insert($audit_ins_arr);

                $msg = 'Successfully overridden charge amount.';
                $func = 'success';
            } else {
                $msg = 'Failed to override charge amount. '.$ins_charges->errmsg;
                $func = 'error';
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['inputs'] = $this->input->post();
        return json_encode($data);
    }


    function get_installation_team() {
        $data = array();
        $appid = $this->input->post('dataid');
        $sql = $this->db->query("SELECT
                p.firstname,
                p.lastname,
                p.middlename,
                CONCAT(
                    p.lastname,
                    ', ',
                    p.firstname,
                    ' ',
                LEFT ( p.middlename, 1 )) AS `name`,
                tp.`names` AS `position`,
                em.empid AS empcode 
            FROM
                prime_employee_main AS em
                INNER JOIN person AS p ON em.personid = p.sysid
                INNER JOIN prime_employee_main_positions AS emp ON em.sysid = emp.emp_id
                INNER JOIN prime_types_parameter AS tp ON emp.position_id = tp.sysid
                INNER JOIN application_customers_team_assignment AS apta ON em.sysid = apta.empid 
            WHERE
                apta.appid = $appid 
                AND apta.moduleid = 47 
                AND apta.`status` = 1");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'num' => $row->empcode,
                    'name' => $row->name,
                    'position' => $row->position,
                    'control' => '',
                );
            }
        }
        return json_encode($data);
    }

    function get_referrals_details() {
        $data = array();
        $dataid = $this->input->post('dataid');
        $reftype = $this->input->post('reftype');
        $input_refamt = $this->input->post('refamt');
        $refpaycnt = $this->input->post('refcnt');
        $refstartyear = $this->input->post('startyear');
        $refstartmonth = $this->input->post('startmonth');



        $project_amt = 210000;

        if($reftype==2) {
            $refamt = ($project_amt * 0.06);
        } else {
            $refamt = $input_refamt;
        }

        $refaramt = $refamt / $refpaycnt;

        // We create a new object with year and month format
        $date = DateTime::createFromFormat('Y m', $refstartyear . ' ' . $refstartmonth);
        $this_day = date('d');


        for ($i = 1; $i <= $refpaycnt; $i++){
            $month_num = $date->format('m');
            $month = $date->format('F');
            $year = $date->format('Y');

            // DO NOT DELETE MIGHT USE IN THE FUTURE CODE
            $due_date_str = strtotime($year.'-'.$month_num.'-'.$this_day);
            $duedate = date("Y-m-d", strtotime("+1 month", $due_date_str));

            $attachment = '';
            $status = '';
            if($month_num>=1 && $month_num<=9) {

                $status .= '<span class="label label-success"><i class="fa fa-check"></i> Paid</span>';
                $attachment .= '<a href="#" class="btn btn-info btn-xs inline"><i class="fa fa-file-pdf-o"></i></a>';
                if($month_num==9) {
                    $status = '<span class="label label-warning"><i class="fa fa-check"></i> Pending</span> Check did not send.';
                }
            }else {
                $status .= '<span class="label label-danger"><i class="fa fa-times"></i> Not Paid</span> RCP not approved yet.';
                if($month_num>10 || $year>date("Y")){
                    $status = '<span class="label label-danger"><i class="fa fa-times"></i> Not schedule</span>';
                }
            }

            $control = '';
            $control .= '<div class="btn-group">';
            $control .= '<a href="#" class="btn btn-warning btn-xs inline"><i class="fa fa-edit"></i></a>';
            $control .= '<a href="#" class="btn btn-info btn-xs inline"><i class="fa fa-upload"></i></a>';
            $control .= '</div>';


            $data['list'][] = array(
                'num' => $i,
                'amt' => number_format($refaramt, 2),
                'year' => $year,
                'month' => '<code>'.$month_num . '</code> ' . $month,
                'status' => $status,
                'attachment' => $attachment,
                'control' => $control,
            );

            $date->add(new DateInterval('P1M'));
        }



        $data['refpaycnt'] = ($reftype == 2) ? 12 : 1;
        $data['refamt'] = $refamt;

        return json_encode($data);
    }

    function get_assessment_details() {
        $data = array();
        $dataid = $this->input->post('dataid');
        $generate = $this->input->post('generate');
        $input_paytype = $this->input->post('paytype');
        $input_startyear = $this->input->post('startyear');
        $input_startmonth = $this->input->post('startmonth');
        $paycnt = 1;
        $intrate = 0;
        $project_amt = 210000;
        $project_total_amt = $project_amt;
        $project_amt_interest = 0;
        $project_amt_permonth = 0;
        $get_paytype_att = $this->db->query("SELECT * FROM application_customers_paytype_rates WHERE typesid = $input_paytype")->row();

        if($get_paytype_att) {
            $paycnt = $get_paytype_att->monthcnt;
            $intrate = $get_paytype_att->rates;
            $project_amt_interest = ($project_amt*$intrate);
            $project_total_amt = ($project_amt + $project_amt_interest);
            $project_amt_permonth = ($project_total_amt/$paycnt);
        }

        $date = DateTime::createFromFormat('Y m', $input_startyear . ' ' . $input_startmonth);
        $this_day = date('d');

        $data['date'] = $date;


        if($generate == 1) {
            $this->db->update('billing_pae_trn', array('status' => 0, 'updatedby' => user_id()), array('appid' => $dataid));
        }

        for ($i = 1; $i <= $paycnt; $i++) {
            $month_num = $date->format('m');
            $month = $date->format('F');
            $year = $date->format('Y');

            $due_date = $year . '-' . $month_num . '-' . $this_day;
            $due_date_str = strtotime($due_date);
            $duedate = date("Y-m-d", strtotime("+1 month", $due_date_str));


            $sql_check_billed = $this->db->select('sysid')->from('billing_pae_trn')
                ->where(array('appid' => $dataid, 'months' => $month_num, 'years' => $year))
                ->get()->row();

            $status = '';

            if($sql_check_billed) {
                $status .= '<span class="label label-info">Processed</span>';
            }else{
                $status .= 'Printed';
            }

            $paid = '';
            $paid .= '<i class="fa fa-times text-danger"></i> No';

            $emailed = '';
            $emailed .= '<i class="fa fa-times text-danger"></i> No';

            $control = '';
            $control .= '<div class="btn-group">';
            $control .= '<a href="#" class="btn btn-warning btn-xs inline"><i class="fa fa-edit"></i></a>';
            $control .= '<a href="#" class="btn btn-success btn-xs inline"><i class="fa fa-envelope"></i></a>';
            $control .= '<a target="_blank" href="'.base_url('billing/singleprintbill/'.$i).'" class="btn btn-info btn-xs inline"><i class="fa fa-print"></i></a>';
            $control .= '</div>';


            $data['list'][] = array(
                'num' => $i,
                'amt' => number_format($project_amt_permonth, 2),
                'year' => $year,
                'month' => '<code>'.$month_num . '</code> ' . $month,
                'duedate' => $duedate,
                'paid' => $paid,
                'status' => $status,
                'emailed' => $emailed,
                'control' => $control
            );

            if($generate == 1) {
                $this->db->insert('billing_pae_trn',
                    array(
                        'appid' => $dataid,
                        'years' => $year,
                        'months' => $month_num,
                        'duedate' => $duedate,
                        'amount' => $project_amt_permonth,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    )
                );
            }

            $date->add(new DateInterval('P1M'));
        }


        $data['paycnt'] = $paycnt;
        $data['intrate'] = round($intrate, 2);
        $data['totalamt'] = round($project_total_amt, 2);
        $data['totalamttext'] = number_format($project_total_amt, 2);
        $data['amtpermonth'] = round($project_amt_permonth, 2);
        return json_encode($data);
    }

    function get_proposal_pdf($id = false,$finalize = false) {
        $data = array();
        $html = '';

        $app = application_info($id);
        $data['docid'] = false;
        $saved = $this->db->select('sysid,html')
            ->from('prime_documents_main')
            ->where(array('dataid' => $id, 'doctype' => 3433, 'status' => 1))
            ->get()->row();

        if ($saved) {
            $html = rehash_pdf_img($saved->html);
            $data['docid'] = $saved->sysid;
        } else {
            if ($app->duid) {
                $durate = $app->durate;
                $distutility = get_dist_utility_list($app->duid)->name;
                $pae_letter_head = ($finalize) ? FCPATH . 'assets/global/img/pae_letter_head.png' : convert_base64_img(FCPATH . 'assets/global/img/pae_letter_head.png');
                $pae_letter_foot = ($finalize) ? FCPATH . 'assets/global/img/pae_letter_foot.png' : convert_base64_img(FCPATH . 'assets/global/img/pae_letter_foot.png');
                $get_system_rates = $this->db->select()
                    ->from('customer_standard_system_rates')
                    ->where(array('systemsizeid' => $app->systemsizeid, 'status' => 1))
                    ->get()->row();

                $outright = 'N/A';
                $twoyrs = 'N/A';
                $fiveyrs = 'N/A';
                $tenyrs = 'N/A';
                $monthlyave = 'N/A';
                $summerave = 'N/A';

                if ($get_system_rates) {
                    $outright = $get_system_rates->outright;
                    $twoyrs = $get_system_rates->twoyrs;
                    $fiveyrs = $get_system_rates->fiveyrs;
                    $monthlyave = $get_system_rates->monthlyave;
                    $tenyrs = $get_system_rates->tenyrs;
                    $summerave = $get_system_rates->summerave;
                }

                $monthlycost = $durate * $monthlyave;
                $peso = '<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>';
                $pvdir = FCPATH . 'uploads/attachments/cad/applications/' . str_pad($id, 6, '0', STR_PAD_LEFT) . '/Assessment/Docs/';
                $bullet = ($finalize) ? FCPATH . 'assets/global/img/check-list.png' : convert_base64_img(FCPATH . 'assets/global/img/check-list.png');
                $files = scandir($pvdir);
                $pvl = array();
                foreach ($files as $file) {
                    if (strpos($file, 'PV_Layout') !== false) {
                        $pvl[] = ($finalize) ? $pvdir . $file : convert_base64_img($pvdir . $file);
                    }
                }

                $html .= '<html>';
                $html .= '<head>';
                $html .= '<title></title>';
                $html .= '<style> 
            html { 
            margin-right: 48px;
            margin-left: 48px;
            }
            
            header {
                position: fixed;
                top: 0px;
                height: 60px;
                background-color: transparent;
                color: white;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed;
                bottom: 0px;
                height: 50px;
                background-color: transparent;
                color: white;
                text-align: center;
                line-height: 35px;
            }
            
            main {
                margin-top: 110px;
            }
            
            .page_break { page-break-before: always; margin-top: 120px; }
        </style>';
                $html .= '</head>';
                $html .= '<body>';
                $html .= '<header>';
                $html .= '<img src="' . $pae_letter_head . '" width="100%"/>';
                $html .= '</header>';
                $html .= '<footer>';
                $html .= '<img src="' . $pae_letter_foot . '" width="100%"/>';
                $html .= '</footer>';
                $html .= '<main>';
                $html .= '<hr>';
                $html .= '<div style="display: block;">';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    <b>' . ucwords(strtolower($app->firstname . ' ' . $app->lastname)) . '</b><br>
                    ' . ucwords(strtolower($app->address)) . '</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">Good day,</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    Based on the result of our assessment, we estimate you would need a <b> ' . $app->systemsizename . ' system</b>. This allows you to
                    harness solar energy directly for your daytime use. Your consumption is seamlessly switched to your electric utility
                    provider for your night time power use so you save on the cost of your daytime use.</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    The ' . strstr($app->systemsizename, ' ', true) . ' system will generate conservatively <b>' . number_format($monthlyave, 2) . 'kWh/month</b>; multiplied by a rate of ' . $peso . number_format($durate, 2) . ' per kWh this
                    translates to ' . $peso . '<b>' . number_format($monthlycost, 2) . '/month</b>. That gives you a conservative energy production of ' . $peso . '<b>' . number_format($monthlycost * 12, 2) . '</b> per year.</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    The cost of the system is ' . $peso . number_format($outright, 2) . ' and can be financed via installment plans with <b>' . $peso . '0.00 down payment</b> of up to 10
                    years for as low as ' . $peso . number_format($tenyrs, 2) . '/month. That cost includes the materials, installation and the monitoring app that allows
                    you to track your solar power production. Please see details below for all payment options.
                    </p>';
                $html .= '<br>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>';
                $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
                $html .= '<tbody>';
                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; background: #c5d9f1; width: 150px; text-align: center;">Outright Purchase</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; background: #8db4e2; text-align: center;">2 Years</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; background: #538dd5; text-align: center;">5 Years</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;">10 Years</td>';
                $html .= '</tr>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #000; color: #fff; width: 150px; text-align: center;">' . strstr($app->systemsizename, ' ', true) . ' System</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #c5d9f1; text-align: center;">' . $peso . number_format($outright, 2) . '</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #8db4e2; text-align: center;">' . $peso . number_format($twoyrs, 2) . '/month</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #538dd5; text-align: center;">' . $peso . number_format($fiveyrs, 2) . '/month</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;">' . $peso . number_format($tenyrs, 2) . '/month</td>';
                $html .= '</tr>';
                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '<br>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>';
                $html .= '<p style="font-weight: bold; font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px;">
                  Premium product, better warranty:</p>
                    <ul style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; list-style-image: url(' . $bullet . '); margin-left: 0.5em">
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> 5 years replacement warranty for inverters. (Outright purchase, 2 years program and 5 years program)</li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Free inverter replacement for the entire duration of 10 years program. </li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Free maintenance and Acts of God insurance for 10 years program. </li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Premium panels are guaranteed to be at least 80% efficient or more for 25 years.</li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> With very low degradation rate per annum compared to conventional panels that has a major drop in efficiency every year. </li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> FREE replacement of solar panels if efficiency rate falls below 80% within 25 years.</li>
                    </ul>
                  ';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">We will be waiting for your confirmation. Once an agreement is signed, we will schedule the date of your installation. The estimated time for completion of the system installation will be 1 day.</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">If you need any further details about your system, please feel free to contact us.</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">Thank you for choosing PA Energy!</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"><br><br><br><br><br><br></p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px;">
                    <span style="width: 50%; text-align: center; margin-left: 2%; font-weight: bold; z-index: -1">MARCELO U. CACHO</span>
                    <img class="signature" src="">
                    <span style="margin-left: 55%">______________________</span>
                    <br>
                    <span style="width: 50%;text-align: center; border-top: 1px solid #000; z-index: -1">Head of Corporate Strategy</span><span style="margin-left: 62%">Conforme</span></p>';
                $html .= '</div>';
                $html .= '<br>';
                $html .= '<br>';
                $html .= '<div class="page_break" style="display: block">';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; font-weight: bold; color: #FF6700">';
                $html .= 'How do I calculate my solar payback period?';
                $html .= '</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    <i>
                    The cost to install a <b>' . strstr($app->systemsizename, ' ', true) . ' system is ' . $peso . number_format($outright, 2) . '</b> and it only takes <b>' . number_format($outright / ($monthlycost * 12), 1) . ' years to recover your investment.</b> We computed your recovery using the average ' . $distutility . ' distribution rate for your area of ' . $peso . number_format($durate, 2) . '/kWh over the past 3 months multiplied by the yearly solar production of ' . number_format($monthlyave * 12, 2) . 'kWh. This results in a yearly savings of <b>' . $peso . number_format($monthlycost * 12, 2) . '</b>.
                    </i>
                  </p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    <i>
                     If you divide the yearly savings by the system cost you will get a ' . number_format((($monthlycost * 12) / $outright) * 100, 2) . '% yearly return on your investment. Essentially, after ' . number_format($outright / ($monthlycost * 12), 1) . ' years, anything generated will be “free power.”
                    </i>
                  </p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; font-weight: bold; color: #FF6700">';
                $html .= 'Actual savings on a 10-year program during the rainy season.';
                $html .= '</p>';
                $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
                $html .= '<tbody>';
                $html .= '<tr>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #92CDDC; text-align: center; line-height: 20px">Cost of Utility Purchased Power</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #C3E4FB; text-align: center; line-height: 20px">*Utility Comparative Rate</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #FFC299; text-align: center; line-height: 20px">Fixed Monthly Payment</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #B8CCE4; text-align: center; line-height: 20px">PA Energy Comparative Rate</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 10px; border: 1px solid #000; background: #FEA022; text-align: center; line-height: 20px">Actual Savings</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #92CDDC; text-align: center;">' . $peso . number_format($monthlycost, 2) . '/month</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #C3E4FB; text-align: center;">' . $peso . number_format($durate, 2) . '/kWh</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FFC299; text-align: center;">' . $peso . number_format($tenyrs, 2) . '/month</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #B8CCE4; text-align: center;">' . $peso . number_format($tenyrs / $monthlyave, 2) . '/kWh</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FEA022; text-align: center;">' . $peso . number_format($monthlycost - (round($tenyrs / $monthlyave, 2, PHP_ROUND_HALF_UP) * $monthlyave), 2) . '/month</td>';
                $html .= '</tr>';
                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; font-weight: bold; color: #FF6700">';
                $html .= 'Actual savings on a 10-year program during the rainy season.';
                $html .= '</p>';
                $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
                $html .= '<tbody>';
                $html .= '<tr>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #92CDDC; text-align: center; line-height: 20px">Cost of Utility Purchased Power</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #C3E4FB; text-align: center; line-height: 20px">*Utility Comparative Rate</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #FFC299; text-align: center; line-height: 20px">Fixed Monthly Payment</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #B8CCE4; text-align: center; line-height: 20px">PA Energy Comparative Rate</th>';
                $html .= '<th style="font-family: Arial, Verdana, sans-serif; font-size: 10px; border: 1px solid #000; background: #FEA022; text-align: center; line-height: 20px">Actual Savings</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #92CDDC; text-align: center;">' . $peso . number_format($summerave * $durate, 2) . '/month</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #C3E4FB; text-align: center;">' . $peso . number_format($durate, 2) . '/kWh</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FFC299; text-align: center;">' . $peso . number_format($tenyrs, 2) . '/month</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #B8CCE4; text-align: center;">' . $peso . number_format($tenyrs / $summerave, 2) . '/kWh</td>';
                $html .= '<td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FEA022; text-align: center;">' . $peso . number_format(($summerave * $durate) - (round($tenyrs / $summerave, 2, PHP_ROUND_HALF_UP) * $summerave), 2) . '/month</td>';
                $html .= '</tr>';
                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; font-weight: bold; color: #ff0000">';
                $html .= '*Based on the average ' . $distutility . ' distribution rate in your area of ' . $peso . number_format($durate, 2) . '/kWh over the past 3 months. ' . $distutility . ' kwh rate is subject to change monthly.';
                $html .= '</p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    <i>
                     Your ' . strstr($app->systemsizename, ' ', true) . ' system can get an energy production of up to ' . number_format($summerave, 2) . 'kwh/month during summer. You also have a fixed effective kWh rate for the duration of the 10 years program. As such, the cost of energy produced by your system for both rainy and summer seasons is greater than your fixed monthly payment.
                    </i>
                  </p>';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; color: #6A7BCD">
                    <i>
                     Note: Savings are based on consumption within the property, and are an annual average. Some months are lower and some months are higher.
                    </i>
                  </p>';

                $html .= '<p style="font-weight: bold; font-family: Arial, Verdana, sans-serif; font-size: 16px; line-height: 15px; color: #FF6700">
                  Benefits of Going Solar:</p>
                    <ul style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; list-style-image: url(' . $bullet . '); margin-left: 0.5em">
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Drastically reduces your electric bills</li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Protects your business against rising energy costs </li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> No worries about unpredictable rate increases </li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Increases your property value</li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Earn a great return on your investment </li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Cools your house’s temperature</li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Very low maintenance. Less hassle</li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Reduces your carbon footprint</li>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.25em"> Environmentally friendly</li>
                    </ul>
                  ';
                $html .= '</div>';
                $html .= '<div class="page_break">';
                $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 15px; text-align: center; font-weight: bold; color: #FF6700">';
                $html .= 'SOLAR PANEL ROOF LAYOUT';
                $html .= '</p>';
                $html .= '<br><br><br><br>';
                //$html .= $pvl;
                foreach ($pvl as $pvimg) {
                    $html .= '<p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 15px; text-align: center; font-weight: bold; color: #FF6700">';
                    $html .= '<img src="' . $pvimg . '" width="100%"/>';
                    $html .= '</p>';
                    $data['pvi'][] = $pvimg;
                }
                $html .= '</div>';
                $html .= '</main>';
                $html .= '</body>';
            } else {
                $html .= '<h1>Distribution Utility not set.</h1>';
                $html .= '<h3>Kindly set DU and refresh page.</h3>';
            }
        }

        $data['html'] = $html;
        $data['name'] = ucwords(strtolower($app->appname));
        $data['title'] = 'Proposal - '.ucwords(strtolower($app->appname));

        return (object)$data;
    }

    function select2_du() {
        $data = array();

        $select2 = get_dist_utility_list();

        if ($select2) {
            foreach ($select2 as $rows) {
                $data['list'][] = array(
                    'id' => $rows->sysid,
                    'text' => $rows->name . ' - ' . $rows->fullname
                );
            }
        }

        return json_encode($data);
    }

    function update_dist_utility() {
        $data = array();
        $distutility = $this->input->post('distutility');
        $durate = $this->input->post('durate');
        $id = $this->input->post('id');

        $msg = '';
        $func = '';
        $qry = false;

        if ($this->db->update('application_customers_details',array('duid' => $distutility, 'durate' => $durate),array('sysid' => $id))) {
            $msg = 'Distribution Utility and Rate has been updated.';
            $func = 'success';
            $qry = true;
        } else {
            $msg = 'Failed to update Distribution Utility and Rate.';
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function finalize_proposal() {
        $data = array();
        $id = $this->input->post('id');
        $finalize = $this->input->post('finalize');
        $proposal = $this->get_proposal_pdf($id,$finalize);

        $qry = false;
        $msg = '';
        $func = '';

        $buttons = '';

        if ($proposal && $proposal->html != '') {
            if ($finalize == true) {
                $insert = array(
                    'dataid' => $id,
                    'doctype' => 3433,
                    'html' => $proposal->html,
                    'createdby' => user_id(),
                );

                if ($this->db->insert('prime_documents_main', $insert)) {
                    $data['save'] = true;
                    $qry = true;
                    $msg = 'Document has been finalized!';
                    $func = 'success';
                    $buttons .= '<button type="button" class="btn btn-danger pull-right" id="btn_regenerate_proposal"><i class="fa fa-undo"></i> Regenerate</button>';
                } else {
                    $data['save'] = false;
                    $qry = false;
                    $msg = 'Failed to finalize document!';
                    $func = 'error';
                    $buttons .= '<button type="button" class="btn btn-primary" id="btn_finalize_proposal"><i class="fa fa-save"></i> Finalize</button>';
                }
            } else {
                if ($proposal->docid) {
                    $buttons .= '<button type="button" class="btn btn-danger pull-right" id="btn_regenerate_proposal"><i class="fa fa-undo"></i> Regenerate</button>';
                } else {
                    $buttons .= '<button type="button" class="btn btn-primary" id="btn_finalize_proposal"><i class="fa fa-save"></i> Finalize</button>';
                }
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['buttons'] = $buttons;

        return json_encode($data);
    }

    function delete_proposal() {
        $data = array();
        $id = $this->input->post('id');
        $proposal = $this->get_proposal_pdf($id);
        $buttons = '';

        $qry = false;
        $msg = '';
        $func = '';

        if ($proposal && $proposal->docid) {
            if ($this->db->update('prime_documents_main',array('status' => 0),array('sysid' => $proposal->docid))) {
                $buttons .= '<button type="button" class="btn btn-primary" id="btn_finalize_proposal"><i class="fa fa-save"></i> Finalize</button>';
                $qry = true;
                $msg = 'Previous document has been deleted!';
                $func = 'success';
            } else {
                $qry = false;
                $msg = 'Failed to generate new proposal!';
                $func = 'error';
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['buttons'] = $buttons;

        return json_encode($data);
    }

}