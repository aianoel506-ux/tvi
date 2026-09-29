<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 3/15/2018
 * Time: 12:46 PM
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Model_systems extends CI_Model
{
    function get_user_notifications()
    {
        $qry = false;
        $data = array();
        $html = '';

        $cnt_unread = 0;
        $cnt_all = 0;

        $list_arr = array();
        $route = false;

        $data['userid'] = user_id();
        $app_flow_ids_arr = flow_id_arr('APPLICATIONS');
        $role_main_id = array();
        $role_main = get_users_info_roles(user_id());
        if(user_id() != 1) {
            if ($role_main && count($role_main) > 0) {
                foreach ($role_main as $rrow) {
                    if ($rrow->type == 1) {
                        $role_main_id[] = $rrow->roleid;
                    }

                    $role_main_id_imp = implode(',', $role_main_id);
                    $app_flow_ids_imp = implode(',', $app_flow_ids_arr);

                }
                $where = '';
                if(trim($role_main_id_imp) != '') {
                    $where .= " AND so.ownergroup IN ($role_main_id_imp) ";
                }
                if(trim($app_flow_ids_imp) != '') {
                    $where .= " AND ms.flowid IN ($app_flow_ids_imp) ";
                }
                $qry_flow_level = $this->db->query("
                    SELECT ms.levels FROM 
                    prime_transaction_flow_main_stages_owners AS so
                    INNER JOIN prime_transaction_flow_main_stages AS ms ON ms.sysid = so.levelid
                    WHERE so.`status` = 1 
                    $where
                ")->row();
                $route = ($qry_flow_level) ? $qry_flow_level->levels : '';

            }
        }
        $data['route'] = $route;
        $data['userid'] = user_id();

        $stages_ids = array();
        $where = '';


        $app_flow_ids_arr = flow_id_arr('APPLICATIONS');
        $app_flow_ids = ($app_flow_ids_arr) ? implode(',', $app_flow_ids_arr) : false;
        $where_trails_last = ($app_flow_ids_arr) ? " AND rm.flowid IN ($app_flow_ids) " : "";
        $where_stages = ($app_flow_ids_arr) ? " AND flowid IN ($app_flow_ids) " : "";
        $data['traillast'] = $where_trails_last;

        if($route && $route > 0) {

            $sql_stages = $this->db->query("SELECT
                                                sysid
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

        if(user_id()) {
            $qry_details = $this->db->query("
                    SELECT 
                    cd.sysid, 
                    cd.rateclassid, 
                    rmt.trnid, 
                    rmt.stageid, 
                    cd.essrno, 
                    cd.datecreated, 
                    cd.personid, 
                    cd.apptype
                    FROM application_customers_details AS cd 
                    INNER JOIN transaction_request_main_trails AS rmt ON rmt.dataid = cd.sysid 
                    WHERE rmt.`status` = 1 $where
                    GROUP BY cd.sysid, cd.rateclassid, rmt.trnid, rmt.stageid, cd.essrno, cd.datecreated, cd.personid, cd.apptype
                    LIMIT 100
                ");
            if($qry_details->num_rows()>0) {

                $num_ = 0;
                $num_txt = '';
                foreach($qry_details->result() as $row_trn) {
                    $num_ += 1;

                    $appid = $row_trn->sysid;
                    $reqtrnid = $row_trn->trnid;
                    $stageid = $row_trn->stageid;
                    $rateclass = $row_trn->rateclassid;
                    $personid = $row_trn->personid;
                    $apptype = $row_trn->apptype;

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
                    if($row_trn->apptype == 2 && $qry_corp_app) {

                        $pic_dir = 'corporation';
                        $pic_id = $qry_corp_app->corpid;


                        $qry_corp = $this->db->select(
                            'c.descs, cb.names AS branch'
                        )
                            ->from('application_customers_corporation AS acc')
                            ->join('corporation AS c', 'c.sysid = acc.corpid')
                            ->join('corporation_branches AS cb', 'cb.sysid = acc.branchid')
                            ->where(array('acc.appid' => $row_trn->sysid))
                            ->get()->row();
                        if($qry_corp) {
                            $branch = (trim($qry_corp->branch) != '') ? ' - ' .$qry_corp->branch : '';
                            $name = '<span  class="font-red-flamingo bold">' . $qry_corp->descs . $branch . '</span>';
                            if(trim($info->info->lastname) != '') {
                                $name .= '<br><span style="font-weight: normal; font-size: 12px; color: #03a9fc;">' . (($info->qry) ? $info->info->lastname . ', ' . $info->info->firstname . ' ' . $middle_in : 'Unknown') . '</span>';
                            }
                            $name .= '<i class="fa fa-star" style="position: absolute; right: 5px; top: 5px; color: #fca503;"></i>';
                        }
                    }

                    if($row_trn->apptype == 3 && $qry_corp_app) {
                        if($qry_corp_app) {
                            $gov_arr = get_government_info($qry_corp_app->corpid);

                            $pic_dir = 'government';
                            $pic_id = $qry_corp_app->corpid;

                            $branch = (trim($gov_arr->info->names) != '') ? ' - ' . $gov_arr->info->names : '';
                            $name = '<span  class="font-red-flamingo bold">' . $gov_arr->info->descs . $branch . '</span>';
                            if ($personid > 0 && trim($info->info->lastname) != '') {
                                $name .= '<br><span style="font-weight: normal; font-size: 12px; color: #03a9fc;">' . (($info->qry) ? $info->info->lastname . ', ' . $info->info->firstname . ' ' . $middle_in : 'Unknown') . '</span>';
                            }
                            $name .= '<i class="fa fa-star" style="position: absolute; right: 5px; top: 5px; color: #fca503;"></i>';
                        }
                    }


                    $pic_recent = get_owner_pic($pic_id, $pic_dir, 2);


                    $qry_trails_last = $this->db->query("
                            SELECT 
                                    rmt.sysid,
                                    fms.desc,
                                    rmt.datecreated,
                                    rmt.dataid,
                                    fms.moduleid,
                                    fms.sysid AS stageid,
                                    rmt.trnid,
                                    rmt.createdby,
                                    rm.codes,
                                    tc.remarks
                            FROM transaction_request_main_trails AS rmt
                            INNER JOIN prime_transaction_flow_main_stages AS fms ON fms.sysid = rmt.stageid
                            INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                            LEFT JOIN transaction_request_trails_comments AS tc ON tc.trnid = rmt.trnid
                            WHERE rmt.dataid = $appid 
                            AND rmt.`status` = 1
                            $where_trails_last
                            ORDER BY rmt.datecreated DESC
                        ")->row();

                    if($qry_trails_last) {
                        $creation_date = $row_trn->datecreated;
                        $updated_date = $qry_trails_last->datecreated;
                        $qry_read = $this->db->select('trailid, datecreated')
                            ->from('transaction_request_trails_logs')
                            ->where(array(
                                'trailid' => $qry_trails_last->sysid,
                                'userid' => user_id(),
                                'activity' => 86))
                            ->get()->row();




                        $user_info = get_users_info($qry_trails_last->createdby);
                        $created_by = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : '';




                            $cnt_all += 1;


                            if ($qry_read) {
                                $tbl_seen = '<i class="fa fa-eye"></i>';
                                $data_position = 'data-position="20' . $num_++ . '"';
                                $fa_icon = 'fa-check';
                                $stat_text = '<span title="' . $qry_read->datecreated . '">seen</span>';
                                $label_class = 'label-default';
                                $title_font = '';
                                $num_txt = '2' . str_pad($num_++, 3, '0', STR_PAD_LEFT);

                            } else {
                                $label_class = 'label-success';
                                $stat_text = '';
                                $fa_icon = 'fa-user';
                                $title_font = 'bold';
                                $cnt_unread += 1;
                                $data_position = 'data-position="10' . $num_++ . '"';
                                $num_txt = '1' . str_pad($num_++, 3, '0', STR_PAD_LEFT);
                                $tbl_seen = '';
                            }

                            $remarks = (trim($qry_trails_last->remarks) != '') ? '<br><p style="margin-top: 4px;">Remarks: ' . $qry_trails_last->remarks . '</p>' : '';

                            $qry_trail_starts = $this->db->select('stageid')->from('transaction_request_main_trails')->where('trnid', $row_trn->trnid)->order_by('datecreated')->get()->row();
                            $check_module_details = $this->db->select()->from('prime_module_navigations_main')
                                ->where('sysid', $qry_trails_last->moduleid)->get()->row();

                            $nav_hash = $check_module_details->hashcode;

                            $data_query = $this->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
                            $nav_trn_hash = ($data_query) ? $data_query->namehash : '';
                            $li_html = '';

                            $li_html .= '<li class="notification-list ' . $num_ . '" data-row="' . $num_ . '">';
                            $li_html .= '<a href="' . base_url('module/' . $nav_hash . '/' . $nav_trn_hash . '/' . $qry_trails_last->sysid) . '/' . $qry_trails_last->dataid . '" data-id="'.$appid.'" data-trnid="'.$qry_trails_last->trnid.'">';
                            $li_html .= '<span class="time"> ' . $qry_trails_last->datecreated . ' </span>';
                            //$li_html .= '<span class="label label-sm label-icon ' . $label_class . '">';
                            $li_html .= '<img src="' . $pic_recent . '" />';
                            //$li_html .= '</span>';
                            $li_html .= '<span class="seen">' . $stat_text . '</span>';
                            $li_html .= '<span class="details">';
                            $li_html .= '<span class="title font-red-flamingo ' . $title_font . '"> ' . $name . ' </span>';
                            $li_html .= '<span class="description "> '. $qry_trails_last->codes . ' - ' . $qry_trails_last->desc . $remarks . '</span>';
                            $li_html .= '<span class="from">Fr: ' . $created_by . '</span>';
                            $li_html .= '</span>';
                            $li_html .= '</a>';
                            $li_html .= '</li>';

                                $list_arr[] = array(
                                    'num' => $qry_trails_last->datecreated,
                                    'li' => $li_html,
                                );

                                $control = '';
                                $control .= '<div class="btn-group">';
                                $control .= '<a class="btn btn-info btn-xs inline" target="_blank" href="' . base_url('module/' . $nav_hash . '/' . $nav_trn_hash . '/' . $qry_trails_last->sysid) . '/' . $qry_trails_last->dataid . '"><i class="fa fa-search"></i></a>';
                                $control .= '<a class="btn btn-danger btn-xs inline" href="javascript:;"><i class="fa fa-times"></i></a>';
                                $control .= '<a class="btn btn-success btn-xs inline" href="javascript:;"><i class="fa fa-eye"></i></a>';
                                $control .= '</div>';

                                $data['list'][] = array(
                                    'seen' => $tbl_seen,
                                    'title' => $name,
                                    'code' => $qry_trails_last->codes,
                                    'desc' => $qry_trails_last->desc,
                                    'from' => $created_by,
                                    'remarks' => '',
                                    'control' => $control
                                );



                    }

                }

                // SORT UNREAD TO TOP
                array_multisort(array_column($list_arr, 'num'), SORT_ASC, $list_arr);
                foreach($list_arr as $lrow) {
                    $html .= $lrow['li'];
                }
            }

        }else{

        }



        $data['stagesid'] = $stages_ids;
        $data['arr'] = $list_arr;
        $data['qry'] = true;
        $data['unread'] = $cnt_unread;
        $data['allnotif'] = $cnt_all;
        $data['html'] = $html;
        return json_encode($data);
        exit();


        /*
            for($i = 1; $i<=30; $i++) {
                $html .= '<li>';
                $html .= '<a href="javascript:;">';
                $html .= '<span class="time">just now</span>';
                $html .= '<span class="details">';
                $html .= '<span class="label label-sm label-icon label-success">';
                $html .= '<i class="fa fa-user"></i>';
                $html .= '</span> New user registered. </span>';
                $html .= '</a>';
                $html .= '</li>';
            }
        */

        if(user_id()) {


            $user_group = array();
            $user_group_qry = get_user_info_main_role();

            if ($user_group_qry) {
                foreach ($user_group_qry as $ugrow) {
                    $user_group[] = $ugrow->roleid;
                }
            }

            $roles_imp = implode(', ', $user_group);

            $from_year = ((date('Y') - 1));


            if ($user_group_qry) {
                $qry_user_trn_notif = $this->db->select('
                    trm.sysid,
                    trm.codes,
                    trm.descs
                ')
                        ->from('transaction_request_main AS trm')
                        ->join('prime_transaction_flow_main_stages AS fms', 'trm.flowid = fms.flowid')
                        ->join('prime_transaction_flow_main_stages_owners AS mso', 'fms.sysid = mso.levelid')
                        ->where_in('mso.ownergroup', $user_group)
                        ->where(array('YEAR(trm.datecreated) >= ' => $from_year, 'trm.status' => 1, 'trm.type' => 1))
                        ->order_by('trm.datecreated', 'desc')
                        ->group_by('
                    trm.sysid,
                    trm.codes,
                    trm.descs
                ')->get();


                if ($qry_user_trn_notif->num_rows() > 0) {
                    $num_ = 0;
                    $num_txt = '';
                    foreach ($qry_user_trn_notif->result() as $row_trn) {

                            $qry_trails_last = $this->db->select(
                                '
                                    rmt.sysid,
                                    fms.desc,
                                    rmt.datecreated,
                                    rmt.dataid,
                                    fms.moduleid,
                                    fms.sysid AS stageid,
                                    rmt.trnid,
                                    rmt.createdby,
                                    tc.remarks
                                '
                                    )
                                ->from('transaction_request_main_trails AS rmt')
                                ->join('prime_transaction_flow_main_stages AS fms', 'fms.sysid = rmt.stageid')
                                ->join('prime_transaction_flow_main_stages_owners AS mso', 'fms.sysid = mso.levelid')
                                ->join('transaction_request_trails_comments AS tc', 'tc.trnid = rmt.trnid', 'left')
                                ->where(array('rmt.trnid' => $row_trn->sysid))
                                ->where_in('mso.ownergroup', $user_group)
                                ->order_by('rmt.datecreated', 'desc')
                                ->get()->row();

                        if ($qry_trails_last) {


                            $cnt_all += 1;


                            $user_info = get_users_info($qry_trails_last->createdby);
                            $created_by = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : '';

                            $qry_trail_details = $this->db->select('trnid')
                                ->from('transaction_request_main_trails')
                                ->where(array('sysid' => $qry_trails_last->trnid))
                                ->get()->row();


                            $show = true;

                            if($qry_trail_details) {

                                $qry_read = $this->db->select('trailid, datecreated')
                                    ->from('transaction_request_trails_logs')
                                    ->where(array(
                                        'trailid' => $qry_trails_last->sysid,
                                        'userid' => user_id(),
                                        'activity' => 86))
                                    ->get()->row();

                                if ($qry_read) {
                                    $tbl_seen = '<i class="fa fa-eye"></i>';
                                    $data_position = 'data-position="20'.$num_++.'"';
                                    $fa_icon = 'fa-check';
                                    $stat_text = '<span title="' . $qry_read->datecreated . '">seen</span>';
                                    $label_class = 'label-default';
                                    $title_font = '';
                                    $num_txt = '2'.str_pad($num_++, 3, '0', STR_PAD_LEFT);

                                } else {
                                    $label_class = 'label-success';
                                    $stat_text = '';
                                    $fa_icon = 'fa-user';
                                    $title_font = 'bold';
                                    $cnt_unread += 1;
                                    $data_position = 'data-position="10'.$num_++.'"';
                                    $num_txt = '1'.str_pad($num_++, 3, '0', STR_PAD_LEFT);
                                    $tbl_seen = '';
                                }

                                $remarks = (trim($qry_trails_last->remarks) != '') ? '<br><p style="margin-top: 4px;">Remarks: '.$qry_trails_last->remarks . '</p>' : '';


                                $qry_trail_starts = $this->db->select('stageid')->from('transaction_request_main_trails')->where('trnid', $qry_trail_details->trnid)->order_by('datecreated')->get()->row();
                                $qry_stage_details = $this->db->select('moduleid')->from('prime_transaction_flow_main_stages')->where('sysid', $qry_trail_starts->stageid)->get()->row();
                                $check_module_details = $this->db->select()->from('prime_module_navigations_main')
                                    ->where('sysid', $qry_trails_last->moduleid)->get()->row();

                                $nav_hash = $check_module_details->hashcode;

                                $data_query = $this->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
                                $nav_trn_hash = ($data_query) ? $data_query->namehash : '';
                                $li_html = '';

                                $li_html .= '<li class="notification-list">';
                                $li_html .= '<a href="' . base_url('module/' . $nav_hash . '/' . $nav_trn_hash . '/' . $qry_trails_last->sysid) . '/' . $qry_trails_last->dataid . '">';
                                $li_html .= '<span class="time"> ' . $qry_trails_last->datecreated . ' </span>';
                                $li_html .= '<span class="label label-sm label-icon ' . $label_class . '">';
                                $li_html .= '<i class="fa ' . $fa_icon . '"></i>';
                                $li_html .= '</span>';
                                $li_html .= '<span class="seen">' . $stat_text . '</span>';
                                $li_html .= '<span class="details">';
                                $li_html .= '<span class="title ' . $title_font . '"> ' . $row_trn->descs . ' </span>';
                                $li_html .= '<span class="description"> ' . $row_trn->codes . ' - ' . $qry_trails_last->desc . $remarks. '</span>';
                                $li_html .= '<span class="from">Fr: ' . $created_by . '</span>';
                                $li_html .= '</span>';
                                $li_html .= '</a>';
                                $li_html .= '</li>';

                                if($show == true) {
                                    $list_arr[] = array(
                                        'num' => $num_txt,
                                        'li' => $li_html,
                                    );

                                    $control = '';
                                    $control .= '<div class="btn-group">';
                                    $control .= '<a class="btn btn-info btn-xs inline" target="_blank" href="' . base_url('module/' . $nav_hash . '/' . $nav_trn_hash . '/' . $qry_trails_last->sysid) . '/' . $qry_trails_last->dataid . '"><i class="fa fa-search"></i></a>';
                                    $control .= '<a class="btn btn-danger btn-xs inline" href="javascript:;"><i class="fa fa-times"></i></a>';
                                    $control .= '<a class="btn btn-success btn-xs inline" href="javascript:;"><i class="fa fa-eye"></i></a>';
                                    $control .= '</div>';

                                    $data['list'][] = array(
                                        'seen' => $tbl_seen,
                                        'title' => $row_trn->descs,
                                        'code' => $row_trn->codes,
                                        'desc' => $qry_trails_last->desc,
                                        'from' => $created_by,
                                        'remarks' => '',
                                        'control' => $control
                                    );
                                }
                            }
                        }
                    }

                    // SORT UNREAD TO TOP
                    array_multisort(array_column($list_arr, 'num'), SORT_ASC, $list_arr);
                    foreach($list_arr as $lrow) {
                        $html .= $lrow['li'];
                    }
                }

                $hid = 30;

                /*
                $hidden_notif = ($cnt_all - $hid);
                if ($cnt_all > $hid) {
                    $html .= '<li>';
                    $html .= '<a class="link" href="' . base_url('user/notifications') . '" data-id="' . user_id() . '">';
                    $html .= 'View all ' . $hidden_notif . ' notifications';
                    $html .= '</a>';
                    $html .= '</li>';
                }
                */
            }

            if ($cnt_all == 0) {
                $html .= '<li>';
                $html .= '<a href="javascript:;" data-id="' . user_id() . '">';
                $html .= '<span class="photo"><img src="' . base_url() . 'assets/global/img//logo/peco-small-logo-compress.png" class="img-circle" alt=""> </span></span>';
                $html .= '<span class="time"></span>';
                $html .= '<span class="details">';
                $html .= '<span class="">';
                $html .= '</span> No item to display!</span>';
                $html .= '</a>';
                $html .= '</li>';
            } else {
                /*
                if($cnt_unread == 0) {
                    $html .= '<li>';
                    $html .= '<a href="javascript:;" data-id="' . user_id() . '">';
                    $html .= '<span class="photo"><img src="' . base_url() . 'assets/global/img//logo/peco-small-logo-compress.png" class="img-circle" alt=""> </span></span>';
                    $html .= '<span class="time"></span>';
                    $html .= '<span class="details">';
                    $html .= '<span class="">';
                    $html .= '</span> No item to display!</span>';
                    $html .= '</a>';
                    $html .= '</li>';
                }
                */
            }
        }else{
            $html .= '<li>';
            $html .= '<a href="javascript:;" data-id="' . user_id() . '">';
            $html .= '<span class="photo"><img src="' . base_url() . 'assets/global/img//logo/peco-small-logo-compress.png" class="img-circle" alt=""> </span></span>';
            $html .= '<span class="time"></span>';
            $html .= '<span class="details">';
            $html .= '<span class="">';
            $html .= '<h5 class="font-red">Session timeout!</h5>';
            $html .= '</a>';
            $html .= '</li>';
        }

        $data['arr'] = $list_arr;
        $data['qry'] = true;
        $data['unread'] = $cnt_unread;
        $data['allnotif'] = $cnt_all;
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_user_inbox()
    {
        $qry = false;
        $data = array();

        $html = '';

        /*
        $html .= '<li>';
        $html .= '<a href="#">';
        $html .= '<span class="photo">';
        $html .= '<img src="'.base_url().'assets/admin/layout/img/avatar1.jpg" class="img-circle" alt=""> </span>';
        $html .= '<span class="subject">';
        $html .= '<span class="from"> John Tadifa </span>';
        $html .= '<span class="time">Just Now </span>';
        $html .= '</span>';
        $html .= '<span class="message"> send your report before 3PM.. </span>';
        $html .= '</a>';
        $html .= '</li>';
        */

        $html .= '<li>';
        $html .= '<a href="#tbl_messages" title="Lucky John F. Faderon" data-toggle="ajax-modal" data-id="'.user_id().'">';
        $html .= '<span class="photo"><img src="'.base_url().'assets/global/img//logo/peco-small-logo-compress.png" class="img-circle" alt=""> </span></span>';
        $html .= '<span class="subject">';
        $html .= '<span class="from">PECO.net</span>';
        $html .= '<span class="time">Just now</span>';
        $html .= '</span>';
        $html .= '<span class="message">No message yet!</span>';
        $html .= '</a>';
        $html .= '</li>';

        $data['qry'] = true;
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_user_task()
    {
        $qry = false;
        $data = array();
        $html = '';
        /*
        $html .= '<li>';
        $html .= '<a href="javascript:;">';
        $html .= '<span class="task">';
        $html .= '<span class="desc">New release v1.2 </span>';
        $html .= '<span class="percent">30%</span>';
        $html .= '</span>';
        $html .= '<span class="progress">';
        $html .= '<span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">';
        $html .= '<span class="sr-only">40% Complete</span>';
        $html .= '</span>';
        $html .= '</span>';
        $html .= '</a>';
        $html .= '</li>';
        */


        $html .= '<li>';
        $html .= '<a href="javascript:;">';
        $html .= '<span class="photo"><img src="'.base_url().'assets/global/img//logo/peco-small-logo-compress.png" class="img-circle" alt=""> </span></span>';
        $html .= '<span class="task">';
        $html .= '<span class="desc">No item to display!</span>';
        $html .= '</span>';
        $html .= '</span>';
        $html .= '</a>';
        $html .= '</li>';

        $data['qry'] = true;
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_module_tagging() {
        $moduleid = $this->input->post('moduleid');
        $data = array();
        $html = '';
        $qry_tagging = $this->db->select('t.sysid, t.moduleid, t.codes, t.descs, t.txtcolor, t.bgcolor, t.icon')
            ->from('prime_system_tags_module AS tm')
            ->join('prime_system_tags AS t', 't.sysid = tm.tagid', 'left')
            ->where(array('tm.moduleid' => $moduleid, 'tm.status' => 1))
            ->get();


        $tagcnt = 0;

        if($qry_tagging->num_rows()>0) {

            $qry_tagging_nav = $this->db->select('name, parent, pagefile')
                ->from('prime_module_navigations_main')
                ->where(array('sysid' => $moduleid))
                ->get()->row();

            if($qry_tagging) {
                $qry_tagging_cnt = false;
                $acctid = false;
                if($qry_tagging_nav->parent==1) {
                    $servno = $this->input->post('servno');
                    $mtr = $this->input->post('mtr');
                    $qry_customer = $this->db->select('sysid')
                        ->from('customer_accounts_main')
                        ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                        ->get()->row();
                    $qry_tagging_cnt = true;
                    $acctid = ($qry_customer) ? $qry_customer->sysid : 0;
                }

                foreach ($qry_tagging->result() as $row) {
                    if($qry_tagging_cnt) {
                        $qry_tagging = $this->db->select('COUNT(st.tagid) AS CNT')
                            ->from('prime_system_tagging AS st')
                            //->join('prime_types_paramenter AS tp', 'tp.sysid = st.status', 'left')
                            ->where(array('st.tagid' => $row->sysid, 'st.moduleid' => $moduleid, 'st.acctid' => $acctid))
                            ->get()->row();

                        $tagcnt = ($qry_tagging) ? $qry_tagging->CNT : 0;
                    }

                    $popover_contents = '
                        <table class=\'table table-hover table-bordered table-condensed\' id=\'tbl_taglist\'>
                            <thead>
                                <th><i class=\'fa fa-reorder\'></i></th>
                                <th>By</th>
                                <th>Date</th>
                                <th>Status</th>
                            </thead>
                        </table>
                    ';

                    $html .= '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
                    $html .= '<a  class="cad-stat cad-stat-v2 tagging margin-top-10 ' . $row->bgcolor . ' ' . $row->txtcolor . ' " href="#">';
                    $html .= '<div class="visual">';
                    $html .= '<i class="fa ' . $row->icon . ' bg"></i>';
                    $html .= '</div>';
                    $html .= '<div class="details">';
                    $html .= '<div class="number">';
                    $html .= '<span data-id="'.$row->sysid.'" data-module="'.$row->moduleid.'" data-acctid="'.$acctid.'" data-counter="counterup" data-value="' . $tagcnt . '" class="popovers" data-trigger="click" data-content="'.$popover_contents.'" title="<i class=\'fa fa-edit\'></i> Tag List <button type=\'button\' aria-hidden=\'true\' class=\'close\'> &times;</button>" data-placement="left">' . $tagcnt . '</span>';
                    $html .= '</div>';
                    $html .= '<div class="desc "> ' . $row->descs . ' </div>';
                    $html .= '</div>';
                    //$html .= '<span data-id="' . $row->sysid . '" data-module="' . $row->moduleid . '" class="btn btn-primary btn-xs add"><i class="fa fa-plus"></i></span>';
                    $html .= '</a>';
                    $html .= '</div>';
                }
            }
        }
        $data['html'] = $html;
        return json_encode($data);

    }

    function get_tagging_table() {
        $data = array();
        $tagid = $this->input->post('tagid');
        $moduleid = $this->input->post('moduleid');
        $acctid = $this->input->post('acctid');

        $qry_tagging = $this->db->select('u.username, st.datecreated, st.status')
            ->from('prime_system_tagging AS st')
            ->join('prime_types_parameter AS tp', 'tp.sysid = st.status', 'left')
            ->join('prime_system_users AS u', 'u.sysid = st.createdby', 'left')
            ->where(array('st.tagid' => $tagid, 'st.moduleid' => $moduleid, 'st.acctid' => $acctid))
            ->get();

        if($qry_tagging->num_rows()>0) {
            $i = 1;
            foreach($qry_tagging->result() as $row) {
                $data['list'][] = array(
                    'num' => $i++,
                    'user' => $row->username,
                    'date' => $row->datecreated,
                    'status' => get_types_label_format($row->status),
                );
            }
        }
        $data['inputs'] = $this->input->post();
        return json_encode($data);
    }

    function tag_this_module() {
        $data = array();
        $page = 'page404construction';
        $title = 'Error 404!';
        $moduleid = $this->input->post('moduleid');
        $tagid = $this->input->post('tagid');
        $dataid = 0;
        $acctno = 0;

        if($moduleid) {
            $qry_tagging = $this->db->select('name, parent, pagefile')
                ->from('prime_module_navigations_main')
                ->where(array('sysid' => $moduleid))
                ->get()->row();

            if($qry_tagging) {
                $data['pagefile'] = $qry_tagging->pagefile;
                if(file_exists(FCPATH . 'application/views/admin/pages/modules/' . $qry_tagging->pagefile . '/tagging.php')) {



                    if($qry_tagging->parent==1) {
                        $servno = $this->input->post('servno');
                        $mtr = $this->input->post('mtr');
                        $qry_customer = $this->db->select('m.sysid, m.types, m.ownerid')
                            ->from('customer_accounts_main AS m')
                            ->where(array('m.servicenumber' => $servno, 'm.mtr' => $mtr))
                            ->get()->row();
                        $data['error'] = $this->db->_error_message();
                        $data['inputs'] = $this->input->post();

                        if($qry_customer) {
                            $dataid = $qry_customer->sysid;
                            $acctno = $servno;
                            $acctname = '';
                            $acctaddr = '';

                            // GET LEGACY INFO
                            if($qry_customer->types==5) {
                                $qry_legacy = $this->db->select('name')
                                    ->from('customer_accounts_name_legacy')
                                    ->where('sysid', $qry_customer->ownerid)
                                    ->get()->row();
                                $acctname = ($qry_legacy) ? $qry_legacy->name : '';
                            }
                            // GET ADDRES
                            $qry_addr = $this->db->select('addrspecific')
                                ->from('customer_accounts_address')
                                ->where(array('acctid' => $dataid,'status' => 1))
                                ->get()->row();
                            $acctaddr = ($qry_addr) ? $qry_addr->addrspecific : '';


                            $qry_check = $this->db->select('tagid')->from('prime_system_tagging')
                                ->where(array(
                                    'acctid' => $dataid,
                                    'moduleid' => $moduleid,
                                    'tagid' => $tagid,
                                    'status' => 307
                                ))
                                ->get()->row();

                            if($qry_check==false) {
                                $tagging_ins_arr = array(
                                    'acctid' => $dataid,
                                    'moduleid' => $moduleid,
                                    'tagid' => $tagid,
                                    'createdby' => user_id(),
                                    'updatedby' => user_id()
                                );
                                $this->db->insert('prime_system_tagging', $tagging_ins_arr);
                            }
                            $title = '<strong class="font-green">'.$acctname.'</strong> <small>'.$acctaddr.'</small><span class="pull-right text-bold font-blue">'. strtoupper($acctno). ' </span>';

                        }
                    }


                    $page = 'modules/' . $qry_tagging->pagefile . '/tagging';


                }else{
                    $title = '<span class="text-danger">tagging.php not found!</span>';
                }
            }else{
                $title = 'navid not found!';
            }
        }
        $data['input'] = $this->input->post();
        $data['dataid'] = $dataid;
        $data['page'] = $page;
        $data['title'] = $title;
        return json_encode($data);
    }


    function select2_bank_list() {
        $data = array();
        $query = $this->db->select()->from('bank_list')->get();
        if($query->num_rows() > 0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->acro . ' - ' . $row->descs
                );
            }
        }
        return json_encode($data);
    }

    /**
     * Return RAM Total in Bytes.
     *
     * @return int Bytes
     */
    public function getRamTotal()
    {
        $result = 0;
        if (PHP_OS == 'WINNT') {
            $lines = null;
            $matches = null;
            exec('wmic ComputerSystem get TotalPhysicalMemory /Value', $lines);
            $res = explode('=', $lines[2]);
            $result = $res[1] * 1024;
        } else {
            $fh = fopen('/proc/meminfo', 'r');
            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                    $result = $pieces[1];
                    // KB to Bytes
                    $result = $result * 1024;
                    break;
                }
            }
            fclose($fh);
        }
        // KB RAM Total
        return $result;
    }
    /**
     * Return free RAM in Bytes.
     *
     * @return int Bytes
     */
    public function getRamFree()
    {
        $result = null;
        if (PHP_OS == 'WINNT') {
            $lines = null;
            $matches = null;
            exec('wmic OS get FreePhysicalMemory /Value', $lines);
            $res = explode('=', $lines[2]);
            $result = $res[1] * 1024;
            /*
            if (preg_match('/^FreePhysicalMemory\=(\d+)$/', $lines[2], $matches)) {
                $result = $matches[1] * 1024;
            }
            */
        } else {
            $fh = fopen('/proc/meminfo', 'r');
            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
                    // KB to Bytes
                    $result = $pieces[1] * 1024;
                    break;
                }
            }
            fclose($fh);
        }
        // KB RAM Total
        return $result;
    }
    /**
     * Return harddisk infos.
     *
     * @param sring $path Drive or path
     * @return array Disk info
     */
    public function getDiskSize($path = '/')
    {
        $result = array();
        $result['size'] = 0;
        $result['free'] = 0;
        $result['used'] = 0;
        $res = array();
        if (PHP_OS == 'WINNT') {
            $lines = null;
            exec('wmic logicaldisk get FreeSpace^,Name^,Size /Value', $lines);
            foreach ($lines as $index => $line) {
                if ($line != "Name=$path") {
                    continue;
                }
                $result['free'] = explode('=', $lines[$index - 1])[1];
                $result['size'] = explode('=', $lines[$index + 1])[1];
                $result['used'] = $result['size'] - $result['free'];
                break;
            }
        } else {
            $lines = null;
            exec(sprintf('df /P %s', $path), $lines);
            foreach ($lines as $index => $line) {
                if ($index != 1) {
                    continue;
                }
                $values = preg_split('/\s{1,}/', $line);
                $result['size'] = $values[1] * 1024;
                $result['free'] = $values[3] * 1024;
                $result['used'] = $values[2] * 1024;
                break;
            }
        }
        return $result;
    }
    /**
     * Get CPU Load Percentage.
     *
     * @return float load percentage
     */
    public function getCpuLoadPercentage()
    {
        $result = -1;
        $lines = null;
        if (PHP_OS == 'WINNT') {
            $matches = null;
            exec('wmic.exe CPU get loadpercentage /Value', $lines);

            $res = explode('=', $lines[2]);
            $result = $res[1];
            /*
            if (preg_match('/^LoadPercentage\=(\d+)$/', $lines[2], $matches)) {
                $result = $matches[1];
            }
            */
        } else {
            // https://github.com/Leo-G/DevopsWiki/wiki/How-Linux-CPU-Usage-Time-and-Percentage-is-calculated
            //$tests = array();
            //$tests[] = 'cpu  3194489 5224 881924 305421192 603380 76 52143 106209 0 0';
            //$tests[] = 'cpu  3194490 5224 881925 305422568 603380 76 52143 106209 0 0';
            $checks = array();
            foreach (array(0, 1) as $i) {
                $cmd = '/proc/stat';
                #$cmd = 'grep \'cpu \' /proc/stat <(sleep 1 && grep \'cpu \' /proc/stat) | awk -v RS="" \'{print ($13-$2+$15-$4)*100/($13-$2+$15-$4+$16-$5) "%"}\'';
                #exec($cmd, $lines);
                $lines = array();
                $fh = fopen($cmd, 'r');
                while ($line = fgets($fh)) {
                    $lines[] = $line;
                }
                fclose($fh);
                //$lines = array($tests[$i]);
                foreach ($lines as $line) {
                    $ma = array();
                    if (!preg_match('/^cpu  (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+)$/', $line, $ma)) {
                        continue;
                    }
                    /**
                     * The meanings of the columns are as follows, from left to right:
                    1st column : user = normal processes executing in user mode
                    2nd column : nice = niced processes executing in user mode
                    3rd column : system = processes executing in kernel mode
                    4th column : idle = twiddling thumbs
                    5th column : iowait = waiting for I/O to complete
                    6th column : irq = servicing interrupts
                    7th column : softirq = servicing softirqs
                    8th column:
                    9th column:
                    Calculation:
                    sum up all the columns in the 1st line "cpu" :
                    ( user + nice + system + idle + iowait + irq + softirq )
                    this will yield 100% of CPU time
                    calculate the average percentage of total 'idle' out of 100% of CPU time :
                    ( user + nice + system + idle + iowait + irq + softirq ) = 100%
                    ( idle ) = X %
                    TOTAL USER = %user + %nice
                    TOTAL CPU = %user + %nice + %system
                    TOTAL IDLE = %iowait + %steal + %idle
                     */
                    $total = $ma[1] + $ma[2] + $ma[3] + $ma[4] + $ma[5] + $ma[6] + $ma[7] + $ma[8] + $ma[9];
                    //$totalCpu = $ma[1] + $ma[2] + $ma[3];
                    //$result = (100 / $total) * $totalCpu;
                    $ma['total'] = $total;
                    $checks[] = $ma;
                    break;
                }
                if ($i == 0) {
                    // Wait before checking again.
                    sleep(1);
                }
            }
            // Idle - prev idle
            $diffIdle = $checks[1][4] - $checks[0][4];
            // Total - prev total
            $diffTotal = $checks[1]['total'] - $checks[0]['total'];
            // Usage in %
            $diffUsage = (1000 * ($diffTotal - $diffIdle) / $diffTotal + 5) / 10;
            $result = $diffUsage;
        }
        return (float) $result;
    }


    function get_tbl_parameters() {
        $data = array();
        $status = $this->input->post('status');
        if($status != '') {
            $status = $status;
        }else{
            $status = 1;
        }
        $query = $this->db->select("tp.sysid, tp.codes, tp.names, tp.desc, tp.colortxt, tp.colorbg, tp.icons AS iconid, si.icon AS icons")
            ->from('prime_types_parameter AS tp')
            ->join('system_icons AS si', 'tp.icons = si.sysid', 'left')
            ->where(array('status' => $status))
            ->order_by('tp.sysid')
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows > 0) {
            foreach($query->result() as $row) {
                $controls = '';
                if($status==0) {
                    $controls .= '<a data-id="'.$row->sysid.'" href="javascript:;" id="btn_delete_permanent" class="btn btn-danger inline"><i class="fa fa-times"></i></a>';
                }else{
                    $controls .= '<a data-id="'.$row->sysid.'" href="javascript:;" id="btn_delete" class="btn btn-danger inline"><i class="fa fa-times"></i></a>';
                }
                $controls .= '<a data-id="'.$row->sysid.'" href="javascript:;" id="btn_edit" class="btn btn-warning inline"><i class="fa fa-pencil"></i></a>';

                $colortxt_box = '
                    <div class="colorbox-main">
                    <span class="color-box" style="background: '.$row->colortxt.'">
                    <span class="box-sprite"></span>
                    </span>
                    <span class="color-label">'.$row->colortxt.'</span>
                    </div>
                    ';

                $colorbg_box = '
                    <div class="colorbox-main">
                    <span class="color-box" style="background: '.$row->colorbg.'">
                    <span class="box-sprite"></span>
                    </span>
                    <span class="color-label">'.$row->colorbg.'</span>
                    </div>
                    ';

                $icon_box = '<span id="icon-view" data-id="'.$row->iconid.'"><i class="fa '.$row->icons.' text-info"></i> - '. $row->icons.'</span>';

                $data['data'][] = array(
                    'sysid' => $row->sysid,
                    'codes' => $row->codes,
                    'names' => $row->names,
                    'desc' => $row->desc,
                    'colortxt' => $colortxt_box. '<input type="hidden" class="form-control inline" style="width: 100%;" value="'.$row->colortxt.'"/>',
                    'colorbg' => $colorbg_box . '<input type="hidden" class="form-control inline" style="width: 100%;" value="'.$row->colorbg.'"/>',
                    'icons' => $icon_box . '<input type="hidden" class="form-control icheck inline" />',
                    'iconid' => $row->iconid,
                    'control' => $controls
                );
            }
        }
        $data['draw'] = 0;
        $data['recordsTotal'] = $num_rows;
        $data['recordsFiltered'] = $num_rows;
        return json_encode($data);
    }

    function select2_icons() {
        $data = array();
        $qry = $this->db->select()->from('system_icons')->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => '<i class="fa '.$row->icon.'"></i>  - ' . $row->icon
                );
            }
        }
        echo json_encode($data);
    }

    function update_parameter_row() {
        $data = array();
        $id = $this->input->post('id');
        $val = $this->input->post('val');
        $col_in = $this->input->post('col');

        $col_arr = explode(' ', $col_in);
        if($col_arr[0] != '') {
            $col = $col_arr[0];
        }else{
            $col = $col_in;
        }

        $qry = false;
        $func = 'error';
        $msg = 'Error PHP';

        if($id && $col) {
            $val_ = ($val) ? $val : null;

            $this->db->trans_begin();
            $this->db->where(array('sysid' => $id));
            $this->db->update('prime_types_parameter', array(trim($col) => $val_));
            $err = $this->db->_error_message();

            if($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                $qry = true;
                $func = 'success';
                $msg = 'Color updated!';
            }else{
                $this->db->trans_rollback();
                $msg = 'Query Error : ' . $err;
                $func = 'warning';
            }
        }

        $data['inputs'] = $this->input->post();
        $data['col'] = $col;

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    //@TODO add restore status 0 to 1 query

    function delete_parameters() {

        $data = array();
        $id = $this->input->post('id');
        $qry = false;
        if($id) {

            $this->db->trans_begin();
            $this->db->where(array('sysid' => $id));
            $this->db->update('prime_types_parameter', array('status' => 0));
            $err = $this->db->_error_message();

            if($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                $qry = true;
            }else{
                $this->db->trans_rollback();
            }
        }

        $data['qry'] = $qry;

        return json_encode($data);
    }

    function delete_parameters_permanent() {

        $data = array();
        $id = $this->input->post('id');
        $qry = false;
        if($id) {

            $this->db->trans_begin();
            $this->db->where(array('sysid' => $id, 'status' => 0));
            $this->db->delete('prime_types_parameter');
            $err = $this->db->_error_message();

            if($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                $qry = true;
            }else{
                $this->db->trans_rollback();
            }
        }

        $data['qry'] = $qry;

        return json_encode($data);
    }

    function add_parameters() {
        $data = array();
        $inputs = $this->input->post();
        $qry = false;
        $msg = '';
        $func = 'error';
        $title = 'Add Parameter';
        if($inputs) {
            $check = $this->db->select()->from('prime_types_parameter')
                ->where(array('status' => 1, 'codes' => $inputs['codes']))
                ->like('codes', $inputs['codes'])
                ->like('names', $inputs['names'])
                ->like('desc', $inputs['desc'])
                ->get()->row();
            if( $check==false ) {
                $ins_arr = array(
                    'codes' => $inputs['codes'],
                    'names' => $inputs['names'],
                    'desc' => $inputs['desc'],
                    'colortxt' => $inputs['colortxt'],
                    'colorbg' => $inputs['colorbg'],
                    'icons' => ($inputs['icons'] && $inputs['icons'] > 0) ? $inputs['icons'] : null,
                );
                $this->db->trans_begin();
                $this->db->insert('prime_types_parameter', $ins_arr);
                $err = $this->db->_error_message();

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    $qry = true;
                    $msg = 'New parameter added!';
                    $func = 'success';
                } else {
                    $this->db->trans_rollback();
                    $msg = 'Error Query: adding parameter | ' . $err;
                    $func = 'warning';
                }
            }else{
                $msg = 'Exists: parameter exists!' ;
                $func = 'warning';
            }
        }

        $data['input'] = $inputs;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;

        return json_encode($data);
    }


    function bill_form($bill, $readhist = false) {
        // GOOGLE CHROME MINIMUM MARGIN
        $history_html = '';
        $history_html .= '<p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">Reading History</p>';
        if($readhist) {

            foreach($readhist as $hrow) {
                $prsdte = date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
                $prvdte = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y');
                $blmonthname = date_formating($hrow->bmo, 'm', 'M');
                $blyearname = date_formating($hrow->byr, 'y', 'Y');


                $history_html .= '
                    <p style="font-weight: bold; font-size: 7px; margin: 0px 0px padding: 0px 0px; line-height: 6px; height: 6px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">
                    <span class="" style="position: absolute; left: 0px; width: 100px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$prvdte.'-'.$prsdte.'</span> 
                    <span class="" style="position: absolute; left: 100px; width: 80px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$blmonthname.' - '.$blyearname.'</span> 
                    <span class="" style="position: absolute; left: 130px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prvrdg.'</span> 
                    <span class="" style="position: absolute; left: 200px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prsrdg.'</span>0 
                    <span class="" style="position: absolute; left: 260px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->kwhuse). '</span>
                    <span class="" style="position: absolute; left: 320px; width: 60px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->current,2). '</span>
                    </p>
                ';
            }
        }

        $gdlb = $bill->group.$bill->dist.' '.str_pad($bill->lot,2,"0",STR_PAD_LEFT).'-'.$bill->book;
        $moyr = $bill->year . '-' . $bill->month;


        $current = $bill->current;
        $gdlb = $bill->group.'-'.$bill->dist.'-'.$bill->lot.'-'.$bill->book;
        // ##############################################
        // PECO RELATED CHARGES
        // AMT
        $disamt = round($bill->disamt, 2);
        $demamt = round($bill->demamt, 2);
        $supamt = round($bill->supamt, 2);
        $supper = round($bill->supper, 2);
        $mtramt = round($bill->mtramt, 2);
        // CHARGES
        $dischg = $bill->dischg;
        $demchg = 0;
        $supchg = $bill->supchg;
        $mtrchg = $bill->mtrchg;
        $mtrper = $bill->mtrper;
        // @TODO SOLVE 5 PESOS
        if($mtrper>0) {
            $mtrcharge = $mtrper;
        }else {
            $mtrcharge = 5;
        }

        $total_peco_charges = round(($disamt + $demamt + $supamt + $supper + $mtramt + $mtrcharge), 2);
        if($total_peco_charges != 0) {
            if($current > 0) {
                if($total_peco_charges > $current) {
                    $total_peco_charges_percent = ($total_peco_charges / $current) * 100;
                }else {
                    $total_peco_charges_percent = ($total_peco_charges / $current) * 100;
                }
            }else{
                $total_peco_charges_percent = 0;
            }
        }else{
            $total_peco_charges_percent = 0;
        }
        // ##############################################
        // SUPPLIER RELATED CHARGES (PPC, DEPC)
        // AMT
        $genamt = round($bill->genamt, 2);
        $genamt1 = round($bill->genamt1, 2);
        $trnamt = round($bill->trnamt, 2);
        $slamt = round($bill->slamt, 2);
        $papc = round($bill->papc, 2);
        // CHARGES
        $genchg = $bill->genchg;
        $genchg1 = $bill->genchg1;
        $trnchg = $bill->trnchg;
        $slchg = $bill->slchg;
        $papcchg = $bill->papcchg;
        $total_supplier_charges = round(($genamt + $genamt1 + $trnamt + $slamt + $papc), 2);
        if($total_supplier_charges != 0) {
            if($current > 0) {
                if($total_supplier_charges > $current){
                    $total_supplier_charges_percent = ($total_supplier_charges / $current) * 100;
                }else {
                    $total_supplier_charges_percent = ($total_supplier_charges / $current) * 100;
                }
            }else{
                $total_supplier_charges_percent = 0;
            }
        }else{
            $total_supplier_charges_percent = 0;
        }
        // ##############################################
        // SUBSIDIES
        // AMT
        $iccamt = round($bill->iccamt, 2);
        $iccsub = round($bill->iccsub, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $llramt = round($bill->llramt, 2);
        $llrsub = round($bill->llrsub, 2);
        $lldamt = round($bill->llramt, 2);
        // CHARGES
        $iccschg = $bill->iccschg;
        $total_subsidies_charges = round(($iccamt + $lldamt), 2);
        if($total_subsidies_charges != 0) {
            if( $current > 0) {
                if($total_subsidies_charges>$current) {
                    $total_subsidies_charges_percent = ($total_subsidies_charges / $current) * 100;
                }else {
                    $total_subsidies_charges_percent = ($total_subsidies_charges / $current) * 100;
                }
            }else{
                $total_subsidies_charges_percent = 0;
            }
        }else{
            $total_subsidies_charges_percent = 0;
        }
        // ##############################################
        // TAXES AND UNIVERSAL CHARGES
        // AMT
        $genvat = round($bill->genvat, 2);
        $trnvat = round($bill->trnvat, 2);
        $disvat = round($bill->disvat, 2);
        $slvat = round($bill->slvat, 2);
        $othvat = round(($bill->othvat + $slvat + $disvat), 2);
        $misamt = round($bill->misamt, 2);
        $envamt = round($bill->envamt, 2);
        $framt = round($bill->framt, 2);
        $npcamt = round($bill->npcamt, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $fitamt = round(($bill->fitchg * $bill->kwhuse), 2);

        // CHARGES
        $mischg = $bill->mischg;
        $envchg = $bill->envchg;
        $npcchg = $bill->npcchg;
        $fitchg = $bill->fitchg;
        $total_tax_universal_charges = round(($genvat + $trnvat + $othvat + $misamt + $envamt + $framt + $npcamt + $iccsamt + $fitamt), 2);
        if($total_tax_universal_charges != 0) {
            if($current > 0) {
                if($total_tax_universal_charges > $current) {
                    $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current) * 100;
                }else {
                    $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current) * 100;
                }
            }else{
                $total_tax_universal_charges_percent = 0;
            }
        }else{
            $total_tax_universal_charges_percent = 0;
        }
        // #######################################
        $appsur = $bill->appsur;
        $surbal = $bill->surbal;
        $overdue = $bill->overdue;
        $totacc = $bill->totacc;
        $totint = $bill->totint;
        $scdisc = $bill->scdisc;

        $dt1 = DateTime::createFromFormat('Y-m-d', $bill->prvdte);
        $newdte_prvdte = $dt1->format('m/d/Y');

        $dt2 = DateTime::createFromFormat('Y-m-d', $bill->prsdte);
        $newdte_prsdte = $dt2->format('m/d/Y');

        $billing_period = $newdte_prvdte.'-'.$newdte_prsdte;
        // $billing_period = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y'). '-' .date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
        // BILLING FORM
        // $bgimg = base_url('assets/global/img/bill_form_bg.jpg');


        // CURRENT
        $total_charges = round(($total_peco_charges + $total_supplier_charges + $total_subsidies_charges + $total_tax_universal_charges), 2);

        $bgimg = FCPATH . 'assets/global/img/bill_form_bg.jpg';
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<img style="z-index: 0;" src="'.$bgimg.'" width="100%" />';
        $html .= '<div class="bill-form" style="z-index: 1; display: inline-block; min-height: 800px;">';
        $html .= '<div class="rep-content" style="width: 650px; display: inline-block; top: 0px; left: 0px; ">';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 65px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->name.'</span>';
        $html .= '<span style="position: absolute; left: 620px; width: 100px; display: inline-block;">'.$bill->servno.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->addr.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 45px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 5px;">'.$gdlb.'</span>';
        $html .= '<span style="position: absolute; left: 65px;">'.$bill->servno.'</span>';
        $html .= '<span style="position: absolute; left: 160px;">'.$bill->mtr.'</span>';
        $html .= '<span style="position: absolute; left: 190px;">'.$moyr.'</span>';
        $html .= '<span style="position: absolute; left: 260px;">'.$billing_period.'</span>';
        $html .= '<span style="position: absolute; left: 410px;">'.$bill->duedate.'</span>';
        $html .= '<span style="position: absolute; left: 540px;">'.number_format($current,2).'</span>';
        $html .= '</p>';


        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 50px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 5px;">'.$bill->mtrser.'</span>';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->serial.'</span>';
        $html .= '<span style="position: absolute; left: 150px;">'.number_format($bill->load).'</span>';
        $html .= '<span style="position: absolute; left: 245px;">'.$bill->rate.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 5px;">'.round($bill->prsrdg).'</span>';
        $html .= '<span style="position: absolute; left: 70px;">'.round($bill->prvrdg).'</span>';
        $html .= '<span style="position: absolute; left: 125px;">'.$bill->multcd.'</span>';
        $html .= '<span style="position: absolute; left: 150px;">'.number_format($bill->kwhuse).'</span>';
        $html .= '</p>';


        // ##### UNPAID BILLS #####################
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 35px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($current, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 15px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->overdue, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 15px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->totint, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->totacc, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 15px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 100px; text-align: right; display: inline-block; width: 70px;">'.$bill->dolpay.'</span>';
        $html .= '</p>';

        // #### CUSTOM MESSAGE
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 90px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px; text-align: left; display: inline-block; width: 200px;">';
        $html .= 'Hello valued customer! <br>Custom message, custom message, custom message, custom message, custom message, custom message, custom message.';
        $html .= '</span>';
        $html .= '</p>';

        // #### BILLING NUMBER -->
        $html .= '<p style="font-weight: normal; font-size: 8px; padding: 0px 0px; line-height: 12px; height: 10px;margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; position: absolute; top: 700px; left: 5px; text-align: left; display: inline-block; width: 600px;">';
        $html .= 'BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>';
        $html .= 'SOA SERIES: 00000001-99999999';
        $html .= '</span>';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 710px; left: 550px; text-align: left; display: inline-block; width: 600px;">'.$bill->billno.'</span>';
        $html .= '</p>';

        // #### FIRST STUB #####################
        $html .= '<div style="position: absolute; top: 775px; left: 0px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 8px; text-align: left; display: inline-block; width: 100px;">';
        $html .= $bill->servno;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 88px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->mtr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 115px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->name;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 115px; margin-top: 12px; text-align: left; display: inline-block;width: 200px;">';
        $html .= $bill->addr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 380px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $moyr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 490px; text-align: right; margin-top: 10px; display: inline-block; width: 70px;">';
        $html .= number_format($bill->totint, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 620px; text-align: right; margin-top: 5px; display: inline-block; width: 70px;">';
        $html .= number_format($current, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 620px; text-align: right; margin-top: 17px; display: inline-block; width: 70px;">';
        $html .= number_format($bill->overdue, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 490px; margin-top: 40px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->totint, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 620px; margin-top: 40px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->overdue + $current), 2);
        $html .= '</span>';

        // ### GRAND TOTAL ############
        $html .= '<span style="position: absolute; left: 620px; margin-top: 65px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totacc+$bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 30px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 130px; text-align: left; margin-top: 0px; display: inline-block; width: 70px;">';
        $html .= $bill->dolpay;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 335px; text-align: right; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->duedate;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        // #### BILLING NUMBER #############
        $html .= '<p style="font-weight: normal; font-size: 8px; padding: 0px 0px; line-height: 12px; height: 10px;margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; position: absolute; top: 885px; left: 5px; text-align: left; display: inline-block; width: 600px;">';
        $html .= 'BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>';
        $html .= 'SOA SERIES: 00000001-99999999';
        $html .= '</span>';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 890px; left: 552px; text-align: left; display: inline-block; width: 600px;">'.$bill->billno.'</span>';
        $html .= '</p>';

        // #### SECOND STUB ##############
        $html .= '<div style="position: absolute; top: 965px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->current, 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->overdue, 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totacc+$bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';
        $html .= '<div style="position: absolute; top: 965px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 130px; text-align: left; margin-top: 58px; display: inline-block; width: 70px;">';
        $html .= $bill->dolpay;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 310px; text-align: right; margin-top: 0px; display: inline-block; width: 70px;">';
        $html .= $bill->duedate;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        $html .= '<div style="position: absolute; top: 970px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 0px; text-align: left; display: inline-block; width: 70px;">';
        $html .= trim($gdlb);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 58px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->servno;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 60px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $bill->mtr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 150px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->name;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 267px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $bill->mtrser;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 345px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $bill->serial;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 400px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $moyr;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 150px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->addr;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';


        // #### BILLING NUMBER ##############
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 1092px; left: 550px; text-align: left; display: inline-block; width: 600px;">';
        $html .= $bill->billno;
        $html .= '</span>';
        $html .= '</p>';

        // #### PAGE NUMBER ###############
        $html .= '<div style="position: absolute; top: 1130px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 600px; text-align: right; display: inline-block; width: 100px;">';
        $html .= 'Page: '.str_pad(1,9,"0",STR_PAD_LEFT);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        // ############ START CHARGES TABLE ####################################
        $html .= '<div style="position: absolute; left: 280px; width: 400px; top: 170px;">';
        // ########## PECO RELATED CHARGES ###############
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">PECO RELEATED CHARGES';
        $html .= '<span class="charges-header-rate" style="position: absolute; left: 220px; width: 80px; text-align: right">PER KWH</span>';
        $html .= '<span class="charges-header-amt" style="position: absolute; left: 290px; width: 100px; text-align: right">AMOUNT</span> </p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Distribution Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$dischg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($disamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Demand Charge ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($demamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Supply Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$supchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($supamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Metering Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$mtrchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($mtramt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Retail Custom Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($mtrcharge, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_peco_charges, 2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_peco_charges_percent, 2).'%</span>';
        $html .= '</p>';

        // ########## SUPLIER RELATED CHARGES ###############
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">SUPLIER RELATED CHARGES (PPC, PEDC)</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Generation Charge ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$genchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($genamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Previous Months Adjustment on Generation Cost ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">0.00</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Previous Years\' Adjustment on Power Cost';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$papcchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($papc, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '(ERC Case No. 2001-333) ';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Transmission Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$trnchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($trnamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'System Loss Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$slchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($slamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_supplier_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_supplier_charges_percent,2).'%</span>';
        $html .= '</p>';


        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">SUBSIDIES</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Inter-class scross-subsidy ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($iccamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Lifeline rate subsidy';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.number_format($llrsub, 4).'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($lldamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_subsidies_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_subsidies_charges_percent,2).'%</span>';
        $html .= '</p>';

        // ########## TAX  ##############################
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">TAX AND UNIVERSAL CHARGES</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Generation';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($genvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Transmission';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($trnvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Other Charges';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($othvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Franchise Tax';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($framt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Missionary';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$mischg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($misamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px ;margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Environmental';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$envchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($envamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'NPC Stranded Cntract Cost';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$npcchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($npcamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'ICCS Adjustment';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$iccschg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($iccsamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'FIT - Allowance';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$fitchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($fitamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_tax_universal_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_tax_universal_charges_percent,2).'%</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($current,2).'</span>';
        $html .= '</p>';

        $html .= $history_html;

        $html .= '<h3 style="font-size: 9px; font-weight: bold; margin-top: 35px;">THIS IS A SYSTEM GENERATED STATEMENT OF ACCOUNT. NO SIGNATURE IS REQUIRED.</H3>';
        $html .= '</div>';
        // ##### END OF CHARGES TABLE ################################
        $html .= '<footer></footer>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }


    function bill_html($data) {

        $dt = DateTime::createFromFormat('m', $data->month);
        $monthname = $dt->format('F');


        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" />';
        $html .= '
	    <style type="text/css">
		html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
		body{
		    font-family: Arial !important;
		    overflow: visible !important;
		}		
		</style>
	    ';
        $html .= '</head>';
        $html .= '<body>';

        $html .= '<div class="container" style="width: 95%; display:inline-block; margin-top: 5px; margin-bottom: 5px; padding: 20px 20px;">';
        $html .= '<div class="col-xs-12">';

        $html .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
        $html .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
        $html .= '</div>';
        $html .= '<br>';
        $html .= '<p>Dear Customer <b>'.$data->name.'</b></p>';
        $html .= '<p>Good day! Your billing statement for <b>'.$monthname.' '.$data->year.'</b> is now available.</p>';
        $html .= '<p>Below is the summary of your monthly billing:</p>';
        $html .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; height: 10px;"></div>';
        $html .= '<address>';
        $html .= '<strong style="display: inline-block; width: 30%;">Bill #:</strong><span style="display: inline-block; width: 70%;">'.$data->billno.'</span>';
        $html .= '<strong style="display: inline-block; width: 30%;">Billed To:</strong><span style="display: inline-block; width: 70%;">'.$data->name.'</span>';
        $html .= '<strong style="display: inline-block; width: 30%;">Billing Period:</strong><span style="display: inline-block; width: 70%;">'.$data->prvdte. ' to '.$data->prsdte.'</span>';
        $html .= '<strong style="display: inline-block; width: 30%;">Address:</strong><span style="display: inline-block; width: 70%;">'.$data->addr.'</span><br>';
        $html .= '<h3>Current Amount: <span style="float: right">'.number_format($data->current, 2).'</span></h3>';
        $html .= '<p>Due Date: <span style="float: right">'.$data->duedate.'</span></p>';
        $html .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; height: 10px;"></div>';
        $html .= '<p>Overdue Amount: <span style="float: right">'.number_format($data->overdue, 2).'</span></p>';
        $html .= '<p>Interest Amount: <span style="float: right">'.number_format($data->totint, 2).'</span></p>';
        $html .= '<h4>Total Amount: <span style="float: right">'.number_format(($data->totacc + $data->totint), 2).'</span></h4>';
        $html .= '</address>';
        $html .= '</div>';
        $html .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; height: 10px;"></div>';
        $html .= '<br>';
        $html .= '<p>Attached herewith is your billing statement.</p>';
        $html .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; height: 10px;"></div>';
        $html .= '<p style="font-family: Courier; color: red;">This is a system generated email. Please do not reply.</p>';
        $html .= '<br>';
        $html .= '<p style="">Thank you!</p>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<p style="">Best Regards,</p>';
        $html .= '<p style="">Panay Eletric Company, Inc.</p>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }


    function generate_captcha() {
        session_start();
        $random_alpha = md5(rand());
        $captcha_code = substr($random_alpha, 0, 6);
        $this->session->set_userdata(array('captcha_code' => $captcha_code));
        $target_layer = imagecreatetruecolor(70,30);
        $captcha_background = imagecolorallocate($target_layer, 220, 220, 220);
        imagefill($target_layer,0,0, $captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        imagestring($target_layer, 5, 8, 6, $captcha_code, $captcha_text_color);
        header("Content-type: image/jpeg");
        imagejpeg($target_layer);
    }

    function register_employee() {
        $data = array();

        $msg = 'Error PHP';
        $func = 'danger';
        $qry = false;

        $captcha_sess = $this->session->userdata('captcha_code');
        $empid = $this->input->post('empid');
        $email = $this->input->post('email');
        $captcha = $this->input->post('captcha');
        $errtype = 0;

        if($captcha == $captcha_sess) {
            $get_employee = $this->db->select()
                ->from('prime_employee_main')
                ->where(array('empid' => $empid))
                ->get()->row();
            if( $get_employee ) {
                if($get_employee->status == 0) {
                    $msg = 'Employee is already resigned or retired!';
                    $func = 'warning';
                    $errtype = 4;

                    $data['msg'] = $msg;
                    $data['func'] = $func;
                    $data['errtype'] = $errtype;
                    $data['qry'] = $qry;
                } else {
                    $emp_info = get_employee_info($get_employee->sysid);
                    if($emp_info->qry) {
                        if($emp_info->emailcomp == $email) {

                            $personid = $emp_info->personid;
                            $empsysid = $emp_info->sysid;
                            $check_user = $this->db->select('personid')
                                ->from('prime_system_users')
                                ->where(array('personid' => $personid, 'status' => 1))
                                ->get()->row();
                            if($check_user) {
                                $msg = 'This person has already an account!';
                                $func = 'warning';
                                $errtype = 5;

                                $data['msg'] = $msg;
                                $data['func'] = $func;
                                $data['errtype'] = $errtype;
                                $data['qry'] = $qry;
                            } else {
                                $username = strtoupper($empid);
                                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                                $confirmation_code = strtoupper(substr(str_shuffle($permitted_chars), 0, 10));

                                $this->db->trans_begin();

                                $this->db->where(array('personid' => $personid, 'status' => 1));
                                $this->db->update('prime_system_users_confirmation', array('status' => 0));

                                $confirm_ins = array(
                                    'personid' => $personid,
                                    'codes' => $confirmation_code,
                                    'createdby' => 0,
                                    'updatedby' => 0
                                );
                                // EMAIL CODE FIRST
                                $email = $this->email_confirmation($empsysid, $confirmation_code) ;
                                if($email) {
                                    // CREATE ACCOUNT REQUEST
                                    $this->db->insert('prime_system_users_confirmation', $confirm_ins);
                                    $data = db_trans($this->db, 'Registration fail, SQL Error', 'Your account is ready please get confirmation code from your email!', false);
                                    $data['errtype'] = false;
                                }else{
                                    $msg = $email;
                                    $func = 'warning';
                                    $errtype = 6;
                                    $data['msg'] = $msg;
                                    $data['func'] = $func;
                                    $data['errtype'] = $errtype;
                                }
                            }
                        } else {
                            $msg = 'Email is not correct, please check the email you entered is correct and match to the email assigned.';
                            $func = 'info';
                            $errtype = 1;

                            $data['msg'] = $msg;
                            $data['func'] = $func;
                            $data['errtype'] = $errtype;
                            $data['qry'] = $qry;
                        }
                    }
                }
            } else {
                $msg = 'Employee ID not found!';
                $func = 'warning';
                $errtype = 2;

                $data['msg'] = $msg;
                $data['func'] = $func;
                $data['errtype'] = $errtype;
                $data['qry'] = $qry;
            }
        }else{
            $msg = 'Captcha don\'t match!';
            $func = 'warning';
            $errtype = 3;

            $data['msg'] = $msg;
            $data['func'] = $func;
            $data['errtype'] = $errtype;
            $data['qry'] = $qry;
        }

        return json_encode($data);
    }

    function email_resetpassword($empid, $codes) {
        $empinfo = get_employee_info($empid);
        $email = $empinfo->emailcomp;

        if($codes) {
            $content = '';
            $content .= '<html>';
            $content .= '<head>';
            $content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
            $content .= '<title>TVI | Tiedra Ventures, Inc.</title>';
            $content .= '<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" />';
            $content .= '
                        <style type="text/css">
                        html { 
                            -webkit-text-size-adjust:none; 
                            -ms-text-size-adjust: none;
                        }
                        body{
                            font-family: Arial !important;
                            overflow: hidden !important;
                        }		
                        </style>
                        ';
            $content .= '</head>';
            $content .= '<body>';

            $content .= '<div class="container" style="width: 95%; display:inline-block; margin-top: 5px; margin-bottom: 5px; padding: 20px 20px;">';
            $content .= '<div class="row">';
            $content .= '<div class="col-xs-12">';
            $content .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
            $content .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
            $content .= '</div>';
            $content .= '<h4>Hi, ' . $empinfo->firstname . '!</h4>';
            $content .= '<br>';
            $content .= '<p>You request for you ERP Account password reset, here is your temporary password.</p>';
            $content .= '<p>Your password: <b>'.$codes.'</b></p>';
            $content .= '<br>';
            $content .= '<p style="color: red">Note: Please do change your password after login.</p>';
            $content .= '<p>Thank you,</p>';
            $content .= '<br>';
            $content .= '<div style="color: #FFF; font-size: 9px; display: inline-block; background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; padding: 10px 20px;">';
            $content .= 'This is a system generated message, please do not reply';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</body>';
            $content .= '</html>';

            $data = mailer($email, $content, 'Password Reset');
        }else{
            $data = false;
        }

        return (object)$data;
    }
    function email_confirmation($empid, $codes) {
        $data = array();
        $empinfo = get_employee_info($empid);
        $email = $empinfo->emailcomp;

        if($codes) {
            $content = '';
            $content .= '<html>';
            $content .= '<head>';
            $content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
            $content .= '<title>TVI | Tiedra Ventures, Inc.</title>';
            $content .= '<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" />';
            $content .= '
                        <style type="text/css">
                        html { 
                            -webkit-text-size-adjust:none; 
                            -ms-text-size-adjust: none;
                        }
                        body{
                            font-family: Arial !important;
                            overflow: hidden !important;
                        }		
                        </style>
                        ';
            $content .= '</head>';
            $content .= '<body>';

            $content .= '<div class="container" style="width: 95%; display:inline-block; margin-top: 5px; margin-bottom: 5px; padding: 20px 20px;">';
            $content .= '<div class="row">';
            $content .= '<div class="col-xs-12">';
            $content .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
            $content .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
            $content .= '</div>';
            $content .= '<h4>Hi, ' . $empinfo->firstname . '!</h4>';
            $content .= '<br>';
            $content .= '<p>Your account is pending for confirmation please login using the account bellow.</p>';
            $content .= '<>Your username: '.$empinfo->empid.'<br>';
            $content .= 'Your password: '.$codes.'</p>';
            $content .= '<br>';
            $content .= '<br>';
            $content .= '<p style="color: red">Note: Please do change your password after login.</p>';
            $content .= '<p>Thank you,</p>';
            $content .= '<br>';
            $content .= '<div style="color: #FFF; font-size: 9px; display: inline-block; background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; padding: 10px 20px;">';
            $content .= 'This is a system generated message, please do not reply';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</body>';
            $content .= '</html>';

            $data = mailer($email, $content, 'Confirmation');
        }else{
            $data = false;
        }

        return (object)$data;
    }


    function quick_launch_list() {
        $data = array();
        $qry_list = $this->db->query("
        SELECT * FROM
        system_quick_launch_main
        ORDER BY sorting, types
        ");
        $html = '';
        if($qry_list->num_rows() > 0) {
            $nav_num = 0;
            foreach($qry_list->result() as $row) {
                $nav_s = false;
                $qry_roles_arr = $this->db->select('roleid')
                    ->from('system_quick_launch_role_matrix')
                    ->where(array('navid' => $row->sysid))
                    ->get();
                if($qry_roles_arr->num_rows() > 0) {
                    foreach($qry_roles_arr->result()  as $rrow) {
                        if(in_array($rrow->roleid, get_users_roles_matrix_id_arr())) {
                            $nav_s = true;
                        }
                    }
                }

                $get_user_emp = get_user_employee_info();
                $data['emp'][] = $get_user_emp;

                if(user_id() == 1 || $nav_s == true || $row->types == 0 || $row->sysid == 9) {
                    $data_toggle = ($row->toggle != '') ? 'data-toggle="' . $row->toggle . '"' : '';
                    $data_target = ($row->target != '') ? 'target="' . $row->target . '"' : '';
                    $href = $row->href;

                    $nav_num += 1;
                    if ($row->types == 0) {
                        if($nav_num > 0) {
                            $html .= '<li class="divider items"></li>';
                        }
                    } else {


                            $nav_num += 1;
                            if ($row->types == 1) {
                                $href = base_url($row->href);
                            }
                            $html .= '<li id="ql_' . $row->sysid . '" class="items animated fadeIn fast">';
                            $html .= '<a data-placement="left" href="' . $href . '" ' . $data_toggle . ' ' . $data_target . ' title="' . $row->titles . '">';
                            $html .= '<i class="' . $row->icons . '"></i> ' . $row->texts . ' <code class="quick-button-label">' . $row->labels . '</code>';
                            $html .= '</a>';
                            $html .= '</li>';

                    }
                } else if($get_user_emp && $row->sysid == 12){
                    $data_toggle = ($row->toggle != '') ? 'data-toggle="' . $row->toggle . '"' : '';
                    $data_target = ($row->target != '') ? 'target="' . $row->target . '"' : '';
                    $href = $row->href;

                    $nav_num += 1;
                    if ($row->types == 1) {
                        $href = base_url($row->href);
                    }
                    $html .= '<li id="ql_' . $row->sysid . '" class="items animated fadeIn fast">';
                    $html .= '<a data-placement="left" href="' . $href . '" ' . $data_toggle . ' ' . $data_target . ' title="' . $row->titles . '">';
                    $html .= '<i class="' . $row->icons . '"></i> ' . $row->texts . ' <code class="quick-button-label">' . $row->labels . '</code>';
                    $html .= '</a>';
                    $html .= '</li>';
                }
            }
        }
        $data['html'] = $html;
        return json_encode($data);
    }

    function send_forgot_password_link() {
        $data = array();

        $qry = false;

        $captcha_sess = $this->session->userdata('captcha_code');
        $empid = $this->input->post('username');
        $email = $this->input->post('email');
        $captcha = $this->input->post('captcha');
        if($captcha == $captcha_sess) {
            $get_employee = $this->db->select()
                ->from('prime_employee_main')
                ->where(array('empid' => $empid))
                ->get()->row();
            if ($get_employee) {
                if ($get_employee->status == 0) {
                    $msg = 'Employee is already resigned or retired!';
                    $func = 'warning';
                    $errtype = 4;

                    $data['msg'] = $msg;
                    $data['func'] = $func;
                    $data['errtype'] = $errtype;
                    $data['qry'] = $qry;
                } else {
                    $emp_info = get_employee_info($get_employee->sysid);
                    if($emp_info->qry) {
                        if ($emp_info->emailcomp == $email) {
                            $personid = $emp_info->personid;
                            $empsysid = $emp_info->sysid;
                            $check_user = $this->db->select('personid')
                                ->from('prime_system_users')
                                ->where(array('personid' => $personid, 'status' => 1))
                                ->get()->row();
                            if ($check_user) {
                                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                                $confirmation_code = strtoupper(substr(str_shuffle($permitted_chars), 0, 6));

                                // EMAIL CODE FIRST
                                $email = $this->email_resetpassword($empsysid, $confirmation_code);
                                if ($email) {
                                    $this->db->trans_begin();
                                    $password_hash = hashing($confirmation_code);

                                    $this->db->query("UPDATE prime_system_users SET password = '$password_hash' WHERE personid = {$check_user->personid} AND status = 1");
                                    $upd_err = $this->db->_error_message();
                                    $data = db_trans($this->db, 'Password reset fail, SQL Error', 'Your new password was sent to your email, please check your inbox and use the temporary password provided!', false);
                                    $data['errorqry'] = $upd_err;
                                    $data['errtype'] = false;
                                }
                            }else{

                                $msg = 'User not found, please contact the system administrator!';
                                $func = 'warning';
                                $errtype = 8;
                                $data['msg'] = $msg;
                                $data['err'] = $emp_info;
                                $data['func'] = $func;
                                $data['errtype'] = $errtype;
                                $data['qry'] = $qry;
                            }
                        }else{
                            $msg = 'Email is wrong';
                            $func = 'warning';
                            $errtype = 7;
                            $data['msg'] = $msg;
                            $data['err'] = $emp_info;
                            $data['func'] = $func;
                            $data['errtype'] = $errtype;
                            $data['qry'] = $qry;
                        }
                    }else{
                        $msg = 'This account is not yet active!';
                        $func = 'warning';
                        $errtype = 5;
                        $data['msg'] = $msg;
                        $data['func'] = $func;
                        $data['errtype'] = $errtype;
                        $data['qry'] = $qry;
                    }
                }
            } else {
                $msg = 'Employee ID not found!';
                $func = 'warning';
                $errtype = 2;

                $data['msg'] = $msg;
                $data['func'] = $func;
                $data['errtype'] = $errtype;
                $data['qry'] = $qry;
            }
        }else{
            $msg = 'Captcha don\'t match!';
            $func = 'warning';
            $errtype = 3;

            $data['msg'] = $msg;
            $data['func'] = $func;
            $data['errtype'] = $errtype;
            $data['qry'] = $qry;
        }


        return json_encode($data);
    }


    function get_users_list_access() {
        $data = array();
        $sql = $this->db->query("
                SELECT su.username, su.lastname, su.firstname, sur.descriptions as `roles`, nm.`desc` AS `modules`, nm.parent FROM prime_system_users AS su
                INNER JOIN prime_system_users_roles_matrix AS surm ON surm.userid = su.sysid
                INNER JOIN prime_system_users_roles_main AS sur ON sur.sysid = surm.roleid
                INNER JOIN prime_system_users_roles_matrix_access AS surma ON surm.roleid = surma.roleid
                INNER JOIN prime_module_navigations_main AS nm ON surma.navid = nm.sysid
                WHERE su.`status` = 1
                AND su.lastname IS NOT NULL AND su.firstname IS NOT NULL AND nm.levels > 1
            ");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $name = $row->lastname. ', ' .$row->firstname;

                if($row->parent!= '') {
                    $get_module_parent = $this->db->select('desc')
                        ->from('prime_module_navigations_main')
                        ->where(array('sysid' => $row->parent))
                        ->get()->row();
                    if($get_module_parent) {
                        $module = $get_module_parent->desc . ' - ' .$row->modules;
                    }else{

                        $module = $row->modules;
                    }
                }else{
                    $module = $row->modules;
                }

                $data['list'][] = array(
                    'username' => $row->username,
                    'name' => $name,
                    'roles' => $row->roles,
                    'modules' => $module
                );
            }
        }
        return json_encode($data);
    }

    function check_page_main($id) {
        $sql = $this->db->select('COUNT(sysid) AS cnt')->from('prime_module_navigations_main')
            ->where(array('parent' => $id))
            ->get()->row();
        if($sql && $sql->cnt > 0) {
            return true;
        }else{
            return false;
        }
    }

    function tbl_roles_list() {
        $data = array();
        $sql = $this->db->select()
            ->from('prime_system_users_roles_main')
            ->where('sysid !=',1)
            ->get();

        if ($sql->num_rows() > 0){
            foreach ($sql->result() AS $rows){

                $data['tblroles'][] = array(
                    'radio' => '<input type="radio" class="form-control icheck" value="'.$rows->sysid.'" id="radio_select_role" name="select_role">',
                    'code'  => $rows->code,
                    'desc'  => $rows->descriptions,
                    'colorbox' => '<input class="form-control inline" id="input_role_color" data-id="'.$rows->sysid.'" style="width: 100%;" value="'.$rows->color.'"/>',
                );
            }

            return json_encode($data);
        }
    }

    function update_roles_color() {
        $data = array();
        $sysid = $this->input->post('sysid');
        $color = $this->input->post('color');


        $this->db->where(array('sysid' => $sysid));

        $update = $this->db->update('prime_system_users_roles_main',array('color' => $color));

        $data['query'] = $this->db->last_query();

        if ($update){
            $data['update'] = true;
        }else{
            $data['update'] = false;
        }

        $data['sysid'] = $sysid;
        $data['color'] = $color;


        return json_encode($data);
    }

    function tbl_nav_list() {
        $data = array();
        $roleid = $this->input->post('id');

        $nav_qry = $this->db->select('sysid,code,name,desc')->from('prime_module_navigations_main')
            ->where(array('status' => 1))
            ->order_by('sysid')
            ->get();

        if ($nav_qry->num_rows() > 0) {
            $num = 1;
            $checked = '';
            foreach ($nav_qry->result() AS $nav_row) {
                $access_qry = $this->db->select('navid')
                    ->from('prime_system_users_roles_matrix_access')
                    ->where(array('roleid' => $roleid , 'status' => 1, 'navid' => $nav_row->sysid))
                    ->get()->row();

                if ($access_qry) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }

                $data['nav_list'][] = array(
                    'num'=> $num++,
                    'modid'=> $nav_row->sysid,
                    'code'=> $nav_row->code,
                    'name'=> $nav_row->desc,
                    'control'=> '<input type="checkbox" class="form-control icheck pull-left" id="select_navigation" '.$checked.' data-id="'.$roleid.'" value="'.$nav_row->sysid.'"> ',
                );
            }
        }

        return json_encode($data);
    }

    function update_nav_matrix() {
        $data = array();
        $role = $this->input->post('roleid');
        $navid = $this->input->post('navid');
        $msg = '';
        $func = '';
        $qry = false;

        $nav_matrix_qry = $this->db->select()
            ->from('prime_system_users_roles_matrix_access')
            ->where(array('roleid' => $role,'navid' => $navid,'status' => 1))
            ->get();

        if ($nav_matrix_qry->num_rows() > 0){
            $this->db->where(array('roleid' => $role,'navid' => $navid,'status' => 1));
            $this->db->update('prime_system_users_roles_matrix_access',array('status' => 0));
            $msg = 'Role removed.';
            $func = 'success';
            $qry = true;
        }else {
            $nav_matrix_update = $this->db->select()
                ->from('prime_system_users_roles_matrix_access')
                ->where(array('roleid' => $role,'navid' => $navid,'status' => 0))
                ->get();
            if ($nav_matrix_update->num_rows() > 0){
                $this->db->where(array('roleid' => $role,'navid' => $navid,'status' => 0));
                $this->db->update('prime_system_users_roles_matrix_access',array('status' => 1));
                $msg = 'Role Added.';
                $func = 'success';
                $qry = true;
            } else {
                $insarr = array(
                    'roleid' => $role,
                    'navid' => $navid,
                );
                $this->db->insert("prime_system_users_roles_matrix_access", $insarr);
                $msg = 'Role Added.';
                $func = 'success';
                $qry = true;
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }


    // ########################################################################
    // GLOBAL SETUP FOR SELECT2 PRIME TYPES PARAMETER OPTIONS
    // ########################################################################
    function select2_types_option($codes = false)  {
        $data = array();
        $_input_codes = $this->input->post('codes');
        if($codes == false) {
            $codes = $_input_codes;
        }
        $sql = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('codes' => $codes, 'status' => 1))
            ->get();
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc
                );
            }
        }
        return json_encode($data);
    }

}