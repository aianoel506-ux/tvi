<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/5/2018
 * Time: 9:43 AM
 */

class Model_ts extends CI_model
{



    function search() {
        $data = array();
        $qry = false;
        $msg = 'TC number not found';
        $func = 'error';
        $title = 'TS Search';

        $tcno = $this->input->post('searchkey');
        $searchtext = $this->input->post('searchtext');
        $searchtype = $this->input->post('searchtype');

        if($searchtext) {
            $msg = $searchtext . 'not found!';
        }

        $complainants = '';
        $district = '';
        $landmarks = '';
        $reportstated = '';
        $datecreated = '';
        $createdby = '';
        $status = '';
        $teamhtml = '';

        if($searchtype==1) {
            $query = $this->db->select()->from('ticketing_details_logs')
                ->where('sysid', $tcno)
                ->get()->row();
            if($query) {
                if($query->status != 314) {

                    $query_teams = $this->db->select()->from('ticketing_details_assignments')
                        ->where(array('ticketid' => $tcno))
                        ->get();
                    $teams_num_rows = $query_teams->num_rows();
                    if($teams_num_rows>0) {
                        $qry = true;
                        if ($query->complainants == 0) {
                            $complainants = $query->lastname . ', ' . $query->firstname . ' ' . $query->middlename;
                        } else {
                            $query_person = $this->db->select()->from('person')
                                ->where('sysid', $query->complainants)->get()->row();
                            if ($query_person) {
                                $complainants = $query_person->lastname . ', ' . $query_person->firstname . ' ' . $query_person->middlename;
                            }
                        }
                        $district = $query->district;
                        if ($query->landmarks != '') {
                            $landmarks_arr = explode(',', $query->landmarks);
                            $landmarks_html = '';
                            if ($landmarks_arr) {
                                foreach ($landmarks_arr as $lrow) {
                                    $get_landmarkname = $this->db->select()->from('address_landmark')->where('sysid', $lrow)->get()->row();

                                    $landmarks_html .= ($get_landmarkname) ? $get_landmarkname->texts : 'Unknown';
                                }
                            }
                        }
                        $landmarks = $landmarks_html;
                        $reportstated = $query->remarks;
                        $datecreated = $query->datecreated;
                        $createdby = get_users_info($query->createdby)->username;
                        $status = get_types_label_format($query->status, false, false, 'top');
                        if ($teams_num_rows > 0) {
                            foreach ($query_teams->result() as $trow) {
                                $teamhtml .= get_types_label_format($trow->typesid, false, false);
                            }
                        } else {
                            $teamhtml = '<code>Unassign</code>';
                        }
                    }else{
                        $title = 'TC : '.str_pad($tcno, '6', '0', STR_PAD_LEFT);
                        $msg = 'This TC No. not yet deployed yet!';
                        $func = 'warning';
                    }

                }else{

                    $title = 'TC : '.str_pad($tcno, '6', '0', STR_PAD_LEFT);
                    $func = 'success';
                    $msg = 'This TC No. is already accomplished!';
                }
            }
        }

        $data['complainants'] = $complainants;
        $data['district'] = get_district_name($district);
        $data['landmarks'] = $landmarks;
        $data['reportstated'] = $reportstated;
        $data['datecreated'] = $datecreated;
        $data['createdby'] = $createdby;
        $data['status'] = $status;
        $data['teams'] = $teamhtml;
        $data['tcno'] = $tcno;


        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['title'] = $title;
        $data['func'] = $func;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_ticket_details($tid = false) {
        $data = array();
        $qry = false;
        $html = '';

        $res = array();
        $trails = false;
        $qry_team = false;
        $trail_no = 0;

        $data = array();
        $html = '';
        if($tid) {
            $ticketid = $tid;
        }else {
            $ticketid = $this->input->post('id');
        }

        $inputs = $this->input->post('inputs');
        $view = ($inputs && isset($inputs['view'])) ? $inputs['view'] : 'admin';


        $qry = $this->db->select('
            tdl.sysid, 
            tdl.repsource, 
            tdl.complainants,
            tdl.compname,
            tp.desc, 
            tpr.descs AS particular, 
            tdl.remarks, 
            tdl.district, 
            tdl.barangays, 
            tdl.address, 
            tdl.contact, 
            tdl.landmarks, 
            tdl.mapurl, 
            tdl.createdby, 
            tdl.updatedby, 
            tdl.datecreated, 
            tdl.dateupdated, 
            tdl.status,
            tdl.reqverification,
            p.firstname,
            p.middlename,
            p.lastname
        ')
            ->from('ticketing_details_logs AS tdl')
            ->join('person AS p', 'p.sysid = tdl.complainants', 'left')
            ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype', 'left')
            ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart', 'left')
            ->where(array('tdl.sysid' => $ticketid))
            ->get()->row();

        if($qry) {
            $controls = '';
            if($view=='basic') {
                $controls = '<a target="_blank" href="' . base_url('outage/view/' . $ticketid) . '" class="btn btn-info inline"><i class="fa fa-search"></i></a>';
            }else {
                $controls = '<a href="' . base_url('module/d321d6f7ccf98b51540ec9d933f20898af3bd71e/view/' . $ticketid) . '" class="btn btn-info btn-xs inline"><i class="fa fa-search"></i></a>';
            }


            if($qry->complainants>0) {
                $name = $qry->lastname . ', '.$qry->firstname . ' ' . $qry->middlename;
            }else{
                $name = $qry->compname;
            }

            $landmarks = '';
            $district = 'Not specified';
            $barangay = 'Not specified';
            if($qry->district > 0) {
                $qry_district = $this->db->select('names')->from('address_districts')
                    ->where('sysid', $qry->district)->get()->row();
                $district = $qry_district->names;
            }
            if($qry->barangays > 0) {
                $qry_garangay = $this->db->select('texts')->from('address_barangay')
                    ->where('sysid', $qry->barangays)->get()->row();
                $barangay = ($qry_garangay) ? $qry_garangay->texts : '';
            }
            if($qry->landmarks!='') {
                $landmarks_arr = explode(',', $qry->landmarks);
                if(count($landmarks_arr) > 0) {
                    foreach ($landmarks_arr as $lrow) {
                        $landmar_qry = $this->db->select()->from('address_landmark')
                            ->where('sysid', $lrow)
                            ->get()->row();
                        if($landmar_qry) {
                            $landmarks .= $landmar_qry->texts;
                        }else{
                            $landmarks .= 'Unknown';
                        }
                    }
                }
            }else{
                $landmarks = '<code>None</code>';
            }

            // TRAILS DETAILS

            $last_trail_date = '';
            $last_trail_user = '';
            $last_trail_stats = '';
            $qry_trails = $this->db->select()->from('ticketing_details_trails')
                ->where(array('ticketid' => $qry->sysid, 'codes != ' => 'READ'))
                ->order_by('datecreated', 'desc')
                ->get()->row();
            if($qry_trails) {
                $last_trail_date = $qry_trails->datecreated;
                if(get_users_info($qry_trails->createdby)) {
                    $user_lastname = (isset(get_users_info($qry_trails->createdby)->lastname)) ? get_users_info($qry_trails->createdby)->lastname : '';
                    $user_firstname = (isset(get_users_info($qry_trails->createdby)->firstname)) ? get_users_info($qry_trails->createdby)->firstname : '';
                    $user_fullname = $user_firstname . ' ' . $user_lastname;
                    $last_trail_user = $user_fullname;
                    //$last_trail_user = $qry_trails->createdby;
                }else{
                    $last_trail_user = 'Unknown';
                }
                $last_trail_stats = $qry_trails->descs;
            }

            // EQUIPMENTS
            $equipments = '';
            $outagettype = '';
            $qry_equipments = $this->db->select('tp.names, tp.desc, om.outageid')
                ->from('ticketing_details_logs_equipments AS lf')
                ->join('prime_types_parameter AS tp', 'tp.sysid = lf.equipid')
                ->join('ticketing_outage_matrix AS om', 'om.equipid = lf.equipid', 'left')
                ->where(array('lf.ticketid' => $qry->sysid, 'lf.status' => 1))
                ->order_by('lf.datecreated', 'desc')
                ->get()->row();
            if($qry_equipments) {
                $equipments = $qry_equipments->names . ' ' . $qry_equipments->desc;
                $outagettype = ($qry_equipments->outageid) ? get_types_label_format($qry_equipments->outageid, false, false, false, 'javascript:;', false, true)->text : 'None';
            }

            // FINDINGS
            $findings = '';
            $qry_findings = $this->db->select('tp.names, tp.desc')
                ->from('ticketing_details_logs_findings AS lf')
                ->join('prime_types_parameter AS tp', 'tp.sysid = lf.findingid')
                ->where(array('lf.ticketid' => $qry->sysid, 'lf.status' => 1))
                ->order_by('lf.datecreated', 'desc')
                ->get()->row();

            if($qry_findings) {
                $findings = $qry_findings->names . ' ' . $qry_findings->desc;
            }
            $remarks = $qry->remarks;
            $lastt_action = 'No Accomplishment Remarks';

            //if($qry->status == 314) {

            $qry_accomplish = $this->db->select('remarks')->from('ticketing_details_trails')
                ->where(array('ticketid' => $ticketid, 'remarks != ' => ''))
                ->order_by('datecreated', 'desc')
                ->get()->row();
            if($qry_accomplish) {
                if(trim($qry_accomplish->remarks) != '') {
                    $lastt_action = $qry_accomplish->remarks;
                }
            }
            //}

            $userc_lastname = (isset(get_users_info($qry->createdby)->lastname)) ? get_users_info($qry->createdby)->lastname : '';
            $userc_firstname = (isset(get_users_info($qry->createdby)->firstname)) ? get_users_info($qry->createdby)->firstname : '';
            $userc_fullname = $userc_firstname . ' ' . $userc_lastname;
            $created_by = $userc_fullname;


            $html .= '<div class="row margin-bottom-5">';


            $location 	= './uploads/attachments/outages';
            $album_name	= str_pad($ticketid, 8, '0', STR_PAD_LEFT);
            $files 		= glob($location . '/' . $album_name . '/*.{jpg,gif,png}', GLOB_BRACE);

            $html .= '<div class="col-md-1">';
            if(count($files) >0) {
                $html .= '<span class="label label-success" style="position: absolute; top: 5px; left: 20px;">'.count($files).'</span>';
                $html .= '<img style="z-index: 100" id="album_thumb" data-id="' . str_pad($ticketid, 8, '0', STR_PAD_LEFT) . '" alt="" src="' . base_url('assets/global/img/pecoshorticon.png') . '" width="100%">';
            }else {
                $html .= '<img style="z-index: 100" id="" alt="" src="' . base_url('assets/global/img/not-available.png') . '" width="100%">';

            }

            $html .= '<br>';

            $html .= '</div>';

            $html .= '<div class="col-md-3">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Encoded Date</span>';
            $html .= '<span class="col-md-8 text-primary">'.$qry->datecreated.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Encoded By</span>';
            $html .= '<span class="col-md-8 text-primary">'.$created_by.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Source</span>';
            $html .= '<span class="col-md-8 text-primary">'.get_types_label_format($qry->repsource, false,false,false, false, true, true)->text.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Status</span>';
            $html .= '<span class="col-md-8 text-primary">'.get_types_label_format($qry->status, false, false, false, 'javascript:;', false, true)->text.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="col-md-3">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Date Updated</span>';
            $html .= '<span class="col-md-8 text-primary">'.$last_trail_date.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Updated By</span>';
            $html .= '<span class="col-md-8 text-primary">'.$last_trail_user.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Outage Type</span>';
            $html .= '<span class="col-md-8 text-primary">'.$outagettype.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Last Action</span>';
            $html .= '<span class="col-md-8 text-primary">'.$lastt_action.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="col-md-5">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            if($qry->mapurl!='') {
                $html .= '<button style="position: absolute; top: 5px; right: 10px;" href="#tc_map_lookup" data-toggle="ajax-modal" data-arr="'.$qry->mapurl.'" class="btn btn-default btn-xs pull-right"><i class="fa fa-map-marker"></i> Map</button>';
            }

            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">District</span>';
            $html .= '<span class="col-md-9 text-primary">'.$district.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Barangay</span>';
            $html .= '<span class="col-md-9 text-primary">'.$barangay.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Landmarks</span>';
            $html .= '<span class="col-md-9 text-primary">'.$landmarks.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Address Specific</span>';
            $html .= '<span class="col-md-9 text-primary">'.$qry->address.'</span>';



            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';

            $html .= '</div>';

            $html .= '<div class="row footer">';
            $html .= '<div class="">';
            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name"><i class="fa fa-comment"></i> Remarks</span>';
            $html .= '<span class="col-md-9 label-default">'.$remarks.'</span>';
            $html .= '</li">';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Equipments</span>';
            $html .= '<span class="col-md-9 label-default">'.$equipments.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '<div class="col-md-3">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Findings</span>';
            $html .= '<span class="col-md-9 label-default">'.$findings.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '<div class="col-md-1">';
            $html .= '<div class="btn-group pull-right">';
            $html .= $controls;
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '</div>';

            $res = array(
                'sysid' => $qry->sysid,
                'repsource' => $qry->repsource,
                'compname' => $qry->compname,
                'firstname' => ($qry->complainants>0) ? $qry->firstname : '',
                'middlename' =>($qry->complainants>0) ? $qry->middlename : '',
                'lastname' => ($qry->complainants>0) ? $qry->lastname : $qry->compname,
                'desc' => $qry->desc,
                'particular' => $qry->particular,
                'remarks' => $qry->remarks,
                'district' => $qry->district,
                'barangay' => $barangay,
                'landmarks' => $landmarks,
                'createdby' => $qry->createdby,
                'updatedby' => $qry->updatedby,
                'datecreated' => $qry->datecreated,
                'dateupdated' => $qry->dateupdated,
                'status' => $qry->status,
                'reqverification' => $qry->reqverification
            );

            $qry_trails_cnt = $this->db->select('COUNT(ticketid) AS CNT')
                ->from('ticketing_details_trails')
                ->where(array('ticketid' => $qry->sysid, 'codes != ' => 'READ'))
                ->get()->row();

            $qry_trails = $this->db->select()->from('ticketing_details_trails')
                ->where(array('ticketid' => $qry->sysid, 'codes != ' => 'READ'))
                ->order_by('datecreated', 'desc')
                ->limit(5)
                ->get();

            $trail_no = ($qry_trails_cnt) ? $qry_trails_cnt->CNT : 0;
            if($qry_trails->num_rows() > 0) {
                $trails = $qry_trails->result();
            }

            $qry_team = $this->db->select()
                ->from('ticketing_details_assignments')
                ->where(array('ticketid' => $qry->sysid, 'status > ' => 0))
                ->get()->row();
        }


        $data['qry'] = (object)$res;
        $data['trail'] = (object)$trails;
        $data['trailno'] = $trail_no;
        $data['team'] = $qry_team;
        $data['html'] = $html;
        $data['view'] = $view;
        return json_encode($data);
    }

    function get_group_list() {
        $data = array();
        $groupid = $this->input->post('id');
        $int = $this->input->post('int');
        $html = '';
        $query = $this->db->select('
                gm.sysid AS gtid,
                tdl.sysid, 
                tdl.complainants, 
                tdl.compname,
                tdl.remarks, 
                tdl.district, 
                tdl.address, 
                tdl.contact, 
                tdl.landmarks, 
                tdl.createdby, 
                tdl.updatedby, 
                tdl.datecreated, 
                tdl.dateupdated, 
                tdl.status,
                tdl.reqverification
            ')
            ->select("concat(p.lastname, ', ', p.firstname) AS name", false)
            ->from('ticketing_details_logs_group_matrix AS gm')
            ->join('ticketing_details_logs AS tdl', 'tdl.sysid = gm.ticketid')
            ->join('person AS p', 'p.sysid = tdl.complainants', 'left')
            ->where(array('gm.groupid' => $groupid, 'gm.status > ' => 0))
            ->get();
        if($query->num_rows() > 0) {
            $html .= '<table id="tbl_sub_details" class="table table-hover table-bordered table-striped">';
            $html .= '<thead>';
            $html .= '<th>#</th>';
            $html .= '<th>Ticket No.</th>';
            $html .= '<th>Complainants</th>';
            $html .= '<th>Address</th>';
            $html .= '<th>Contact</th>';
            $html .= '<th>Status</th>';
            if($int==1) {
                $html .= '<th>Ungroup</th>';
            }

            $html .= '</thead>';

            $html .= '<tbody>';

            foreach($query->result() as $row) {
                // EQUIPMENTS
                $equipments = '';
                $outagettype = '';
                $qry_equipments = $this->db->select('tp.names, tp.desc, om.outageid')
                    ->from('ticketing_details_logs_equipments AS lf')
                    ->join('prime_types_parameter AS tp', 'tp.sysid = lf.equipid')
                    ->join('ticketing_outage_matrix AS om', 'om.equipid = lf.equipid', 'left')
                    ->where(array('lf.ticketid' => $row->sysid, 'lf.status' => 1))
                    ->order_by('lf.datecreated', 'desc')
                    ->get()->row();
                if($qry_equipments) {
                    $equipments = $qry_equipments->names . ' ' . $qry_equipments->desc;
                    $outagettype = ($qry_equipments->outageid) ? get_types_label_format($qry_equipments->outageid) : 'None';
                }

                // FINDINGS
                $findings = '';
                $qry_findings = $this->db->select('tp.names, tp.desc')
                    ->from('ticketing_details_logs_findings AS lf')
                    ->join('prime_types_parameter AS tp', 'tp.sysid = lf.findingid')
                    ->where(array('lf.ticketid' => $row->sysid))
                    ->order_by('lf.datecreated', 'desc')
                    ->get()->row();

                if($qry_findings) {
                    $findings = $qry_findings->names . ' ' . $qry_findings->desc;
                }

                $landmarks = '';
                $district = '<code>None</code>';
                if($row->district > 0) {
                    $qry_district = $this->db->select()->from('address_districts')
                        ->where('sysid', $row->district)->get()->row();
                    $district = $qry_district->names;
                }
                if($row->landmarks!='') {
                    $landmarks_arr = explode(',', $row->landmarks);
                    if(count($landmarks_arr) > 0) {
                        foreach ($landmarks_arr as $lrow) {
                            $landmar_qry = $this->db->select()->from('address_landmark')
                                ->where('sysid', $lrow)
                                ->get()->row();
                            if($landmar_qry) {
                                $landmarks .= $landmar_qry->texts;
                            }else{
                                $landmarks .= 'Unknown';
                            }
                        }
                    }
                }else{
                    $landmarks = '<code>None</code>';
                }

                if($row->status==314) {
                    $controls = '<i class="fa fa-check text-success pull-right"></i>';
                }else{
                        $controls = '<a href="javascript:;" data-id="' . $row->sysid . '" data-group="' . $groupid . '" class="text-danger" id="btn_delete_group_list_row"><i class="fa fa-times"></i></a>';

                }

                $name = ($row->complainants>0) ? $row->name : $row->compname;
                $address = $district . ', ' . $landmarks;
                $html .= '<tr>';
                $html .= '<td><i id="btn-expand" class="fa fa-plus-square-o" data-id="'.$row->sysid.'"></i></td>';
                $html .= '<td>'.str_pad($row->sysid, 8, '0', STR_PAD_LEFT).'</td>';
                $html .= '<td>'.$name.'</td>';
                $html .= '<td>'.$address.'</td>';
                $html .= '<td>'.$row->contact.'</td>';
                $html .= '<td>'.get_types_label_format($row->status, false, false, false, 'javascript:;', false,true)->text.'</td>';

                if($int==1) {
                    $html .= '<td>' . $controls . '</td>';
                }
                $html .= '</tr>';
            }



            $html .= '</tbody>';
            $html .= '</table>';

            $func = 'info';
        }
        $data['func'] = $func;
        $data['html'] = $html;
        return json_encode($data);
    }

    function select2_teamno() {
        $data = array();
        $query = $this->db->select('tp.sysid, tp.codes, tp.names, tp.desc')
            ->from('prime_types_parameter AS tp')
            ->where(array('tp.codes' => 'TSTEAM'))
            ->group_by('tp.sysid, tp.codes, tp.names, tp.desc')
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->desc
                );
            }
        }
        $data['input'] = $this->input->post();
        $data['num'] = $num_rows;
        return json_encode($data);
    }

    function team_deploy() {

        $data = array();
        $qry = false;

        $msg = '';
        $userid = user_id();
        $func = 'error';
        $title = 'Trouble Shooter Deployment';

        $ticketids = $this->input->post('ticketids');

        if($ticketids) {
            foreach($ticketids as $trow) {
                $this->db->where(array('status > ' => 1, 'sysid' => $trow));
                $this->db->update('ticketing_details_logs', array('status' => 376));

                $ticket_trail_arr = array(
                    'ticketid' => $trow,
                    'codes' => 'TSDEPLOY',
                    'descs' => 'TS - Team Deployment',
                    'statusid' => 376,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
            }
            $qry = true;
            $func = 'success';
            $msg = 'Team has been deployed!';
        }



        $data['input'] = $this->input->post();
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = $title;
        return json_encode($data);

    }


    function join_to_group() {
        $data = array();
        $qry = false;
        $msg = '';
        $func = 'error';
        $title = 'Trouble Shooter Entry';
        $insert_arr_disp = array();
        $trouble = $this->input->post('trouble');
        $groupid = $this->input->post('groupid');

        if ($trouble && is_array($trouble) && count($trouble) > 0) {
            $this->db->trans_begin();


            foreach ($trouble as $trow) {

                $insert_arr = array(
                    'ticketid' => $trow,
                    'groupid' => $groupid,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );

                $this->db->insert('ticketing_details_logs_group_matrix', $insert_arr);
                $insert_arr_disp[] = $insert_arr;


                $ticket_trail_arr = array(
                    'ticketid' => $trow,
                    'codes' => 'TSJOINGROUP',
                    'descs' => 'TS - GROUP ASSIGNMENT',
                    'statusid' => 377,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

                $get_group_data = $this->db->select()->from('ticketing_details_logs_group')
                    ->where(array('sysid' => $groupid))
                    ->get()->row();

                if($get_group_data) {

                    $this->db->where('ticketid', $trow);
                    $this->db->update('ticketing_details_logs_equipments', array('status' => 0));
                    $equip_arr = array(
                        'ticketid' => $trow,
                        'equipid' => $get_group_data->equipid,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $this->db->insert('ticketing_details_logs_equipments', $equip_arr);



                    $this->db->where('ticketid', $trow);
                    $this->db->update('ticketing_details_logs_findings', array('status' => 0));
                    $findings_arr = array(
                        'ticketid' => $trow,
                        'findingid' => $get_group_data->findingsid,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $this->db->insert('ticketing_details_logs_findings', $findings_arr);

                    $this->db->where('ticketid', $trow);
                    $this->db->update('ticketing_details_logs_circuit_level', array('status' => 0));
                    $circuit_arr = array(
                        'ticketid' => $trow,
                        'circuitid' => $get_group_data->circuitid,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $this->db->insert('ticketing_details_logs_circuit_level', $circuit_arr);

                    $this->db->where(array('status > ' => 1, 'sysid' => $trow));
                    $this->db->update('ticketing_details_logs', array('status' => $get_group_data->status));
                }
            }


            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $msg = 'Ticket Assign!';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Ticket Assign!';
                $func = 'warning';
            }

        } else {
            $msg = 'Please select an item(s)!';
            $func = 'warning';
        }


        $data['ins'] = $insert_arr_disp;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }


    function assign_to_team_row() {
        $data = array();
        $qry = false;
        $msg = '';
        $func = 'error';
        $title = 'Trouble Shooter Entry';
        $insert_arr_disp = array();
        $ticketno = $this->input->post('ticketid');
        $teamno = $this->input->post('teamno');
        $remarks = $this->input->post('remarks');



        $this->db->trans_begin();


        $ins_arr_group = array(
            'remarks' => $remarks,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert('ticketing_details_assignments_group', $ins_arr_group);
        $groupid = $this->db->insert_id();

        // UPDATE FIRST
        $upd_where = array(
            'ticketid' => $ticketno,
            'status' => 300
        );
        $this->db->where($upd_where);
        $this->db->update('ticketing_details_assignments', array('status' => 0));

        if($teamno && $teamno > 0) {

            $insert_arr = array(
                'typesid' => $teamno,
                'ticketid' => $ticketno,
                'groupid' => $groupid,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $this->db->insert('ticketing_details_assignments', $insert_arr);
            $insert_arr_disp[] = $insert_arr;


            $ticket_trail_arr = array(
                'ticketid' => $ticketno,
                'codes' => 'TSASSIGN',
                'descs' => 'TS - Team Deployment',
                'statusid' => $teamno,
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

            $this->db->where(array('status > ' => 1, 'sysid' => $ticketno));
            $this->db->update('ticketing_details_logs', array('status' => 377));

        }else {

            $this->db->where(array(
                'ticketid' => $ticketno,
            ));
            $this->db->update('ticketing_details_assignments', array('status' => 0));


            $ticket_trail_arr = array(
                'ticketid' => $ticketno,
                'codes' => 'TSASSIGN',
                'descs' => 'TS - Team Removed',
                'statusid' => 300,
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

            $this->db->where(array('status > ' => 1, 'sysid' => $ticketno));
            $this->db->update('ticketing_details_logs', array('status' => 300));
        }


        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $msg = 'Ticket Assign!';
            $func = 'success';
            $qry = true;
        } else {
            $this->db->trans_rollback();
            $msg = 'Ticket Assign!';
            $func = 'warning';
        }



        $data['ins'] = $insert_arr_disp;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }



    function assign_to_team_row_group() {
        $data = array();
        $qry = false;
        $msg = '';
        $func = 'error';
        $title = 'Trouble Shooter Entry';
        $insert_arr_disp = array();
        $groupid = $this->input->post('groupid');
        $teamno = $this->input->post('teamno');
        $remarks = $this->input->post('remarks');
        $status = $this->input->post('status');



        $this->db->trans_begin();
        $qry_group_matrix = $this->db->select('ticketid')
            ->from('ticketing_details_logs_group_matrix')
            ->where('groupid', $groupid)
            ->get();

        if($qry_group_matrix->num_rows() > 0 ) {

            $this->db->where('sysid', $groupid);
            $this->db->update('ticketing_details_logs_group', array('status' => 377, 'teamid' => $teamno));
            $data['updgrp'] = $this->db->_error_message();


            foreach ($qry_group_matrix->result() as $row) {
                $ticketno = $row->ticketid;
                $ins_arr_group = array(
                    'remarks' => $remarks,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('ticketing_details_assignments_group', $ins_arr_group);
                $groupid = $this->db->insert_id();

                // UPDATE FIRST
                $upd_where = array(
                    'ticketid' => $ticketno,
                    'status' => 300
                );
                $this->db->where($upd_where);
                $this->db->update('ticketing_details_assignments', array('status' => 0));

                if ($teamno && $teamno > 0) {

                    $insert_arr = array(
                        'typesid' => $teamno,
                        'ticketid' => $ticketno,
                        'groupid' => $groupid,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );

                    $this->db->insert('ticketing_details_assignments', $insert_arr);
                    $insert_arr_disp[] = $insert_arr;


                    $ticket_trail_arr = array(
                        'ticketid' => $ticketno,
                        'codes' => 'TSASSIGN',
                        'descs' => 'TS - Team Deployment',
                        'statusid' => 377,
                        'createdby' => user_id()
                    );
                    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

                    $this->db->where(array('status > ' => 1, 'sysid' => $ticketno));
                    $this->db->update('ticketing_details_logs', array('status' => 377));

                } else {

                    $this->db->where(array(
                        'ticketid' => $ticketno,
                    ));
                    $this->db->update('ticketing_details_assignments', array('status' => 0));


                    $ticket_trail_arr = array(
                        'ticketid' => $ticketno,
                        'codes' => 'TSASSIGN',
                        'descs' => 'TS - Team Removed',
                        'statusid' => 300,
                        'createdby' => user_id()
                    );
                    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

                    $this->db->where(array('status > ' => 1, 'sysid' => $ticketno));
                    $this->db->update('ticketing_details_logs', array('status' => 300));
                }


                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $msg = 'Ticket Assign!';
                    $func = 'success';
                    $qry = true;
                } else {
                    $this->db->trans_rollback();
                    $msg = 'Ticket Assign!';
                    $func = 'warning';
                }
            }
        }else{
            $msg = 'Cannot find group!';
            $func = 'warning';
        }



        $data['ins'] = $insert_arr_disp;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }



    function accomplish() {
        $data = array();
        $qry = false;
        $msg = 'Error PHP';
        $func = 'error';
        $title = 'TS Accomplishment';
        $tcno = $this->input->post('tcno');
        $tcremarks = $this->input->post('tsremarks');
        $tcfindings = $this->input->post('tsfindings');
        $tctype = $this->input->post('accomptype');

        if($tcno) {

            $this->db->trans_begin();

            $ticket_trail_arr = array(
                'ticketid' => $tcno,
                'codes' => 'TSACCOMPLISHMENT',
                'descs' => 'TS - Accomplishment',
                'statusid' => 314,
                'remarks' => $tcremarks,
                'findings' => $tcfindings,
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);


            $this->db->where(array('status > ' => 1, 'sysid' => $tcno));
            $this->db->update('ticketing_details_logs', array('status' => $tctype));

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $msg = 'Trouble Call Accomplished';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Trouble Call NOT Accomplished!';
                $func = 'warning';
            }
        }else{
            $msg = 'Please search TC no. first!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function accomplishrow() {
        $data = array();
        $qry = false;
        $msg = 'Error PHP';
        $func = 'error';
        $title = 'TS Accomplishment';
        $tcno = $this->input->post('ticketid');
        $statusid = $this->input->post('statusid');
        $remarks = $this->input->post('remarks');

        if($tcno) {

            $this->db->trans_begin();

            $ts_log_codes = 'TSSTATUS';
            $ts_log_desc = 'TS Status Changed';
            if($statusid==305) {
                $ts_log_codes = 'TSACCOMPLISHMENT';
                $ts_log_desc = 'TS - Accomplishment';
            }

            $ticket_trail_arr = array(
                'ticketid' => $tcno,
                'codes' => $ts_log_codes,
                'descs' => $ts_log_desc,
                'statusid' => $statusid,
                'remarks' => $remarks,
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);


            $this->db->where(array('status > ' => 1, 'sysid' => $tcno));
            $this->db->update('ticketing_details_logs', array('status' => $statusid));

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $msg = 'Trouble Call Accomplished';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Trouble Call NOT Accomplished!';
                $func = 'warning';
            }
        }else{
            $msg = 'Please search TC no. first!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }


    function accomplishrow_group() {
        $data = array();
        $qry = false;
        $msg = 'Error PHP';
        $func = 'error';
        $title = 'TS Group Accomplishment';
        $groupid = $this->input->post('groupid');
        $statusid = $this->input->post('statusid');
        $remarks = $this->input->post('remarks');

        if($groupid) {
            $this->db->trans_begin();

            $qry_group_matrix = $this->db->select('ticketid')
                ->from('ticketing_details_logs_group_matrix')
                ->where('groupid', $groupid)
                ->get();

            if($qry_group_matrix->num_rows() > 0 ) {

                $this->db->where('sysid', $groupid);
                $this->db->update('ticketing_details_logs_group', array('status' => $statusid));
                $data['updgrp'] = $this->db->_error_message();


                foreach ($qry_group_matrix->result() as $row) {
                    $tcno = $row->ticketid;

                    $ts_log_codes = 'TSSTATUS';
                    $ts_log_desc = 'TS Status Changed | ' . get_types_label_format($statusid, false, false, false, false, false);
                    if ($statusid == 305) {
                        $ts_log_codes = 'TSACCOMPLISHMENT';
                        $ts_log_desc = 'TS - Accomplishment';
                    }

                    $ticket_trail_arr = array(
                        'ticketid' => $tcno,
                        'codes' => $ts_log_codes,
                        'descs' => $ts_log_desc,
                        'statusid' => 314,
                        'remarks' => $remarks,
                        'createdby' => user_id()
                    );
                    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);


                    $this->db->where(array('status > ' => 1, 'sysid' => $tcno));
                    $this->db->update('ticketing_details_logs', array('status' => $statusid));

                    if ($this->db->trans_status() === TRUE) {
                        $this->db->trans_commit();
                        $msg = 'Trouble Call Accomplished';
                        $func = 'success';
                        $qry = true;
                    } else {
                        $this->db->trans_rollback();
                        $msg = 'Trouble Call NOT Accomplished!';
                        $func = 'warning';
                    }
                }
            }
        }else{
            $msg = 'Please search TC no. first!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['input'] = $this->input->post();

        return json_encode($data);
    }

    function select2_tc_equipments() {
        $data = array();
        $query = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'TCEQUIPMENT'))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function select2_tc_findings() {
        $data = array();
        $query = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'TCFINDINGS'))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function add_findings_row() {
        $data = array();
        $qry = false;

        $ticketid = $this->input->post('ticketid');
        $findingid = $this->input->post('findingid');


        $this->db->trans_begin();

        $findings_message = 'TS - Findings Added';
        $findings_status = 311;
        if($findingid==false || $findingid==''){
            $findings_message = 'TS Findings Removed!';
            $findings_status = 300;
        }

        $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
        $this->db->update('ticketing_details_logs_findings', array('status' => 0, 'updatedby' => user_id()));


        if($findings_status!=300) {
            $finding_ins_arr = array(
                'ticketid' => $ticketid,
                'findingid' => $findingid,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('ticketing_details_logs_findings', $finding_ins_arr);
        }

        $ticket_trail_arr = array(
            'ticketid' => $ticketid,
            'codes' => 'TSFINDINGS',
            'descs' => $findings_message,
            'statusid' => $findingid,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);



        $this->db->where(array('status > ' => 1, 'sysid' => $ticketid));
        $this->db->update('ticketing_details_logs', array('status' => $findings_status));

        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function add_findings_row_group() {
        $data = array();
        $qry = false;

        $groupid = $this->input->post('groupid');
        $findingid = $this->input->post('findingid');


        if($groupid) {

            $this->db->trans_begin();

            $qry_group_matrix = $this->db->select('ticketid')
                ->from('ticketing_details_logs_group_matrix')
                ->where('groupid', $groupid)
                ->get();

            if ($qry_group_matrix->num_rows() > 0) {

                $this->db->where('sysid', $groupid);
                $this->db->update('ticketing_details_logs_group', array('findingsid' => $findingid));
                $data['updgrp'] = $this->db->_error_message();

                foreach ($qry_group_matrix->result() as $row) {

                    $ticketid = $row->ticketid;

                    $findings_message = 'TS - Findings Added';
                    $findings_status = 311;
                    if ($findingid == false || $findingid == '') {
                        $findings_message = 'TS Findings Removed!';
                        $findings_status = 300;
                    }

                    $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
                    $this->db->update('ticketing_details_logs_findings', array('status' => 0, 'updatedby' => user_id()));


                    if ($findings_status != 300) {
                        $finding_ins_arr = array(
                            'ticketid' => $ticketid,
                            'findingid' => $findingid,
                            'createdby' => user_id(),
                            'updatedby' => user_id()
                        );
                        $this->db->insert('ticketing_details_logs_findings', $finding_ins_arr);
                    }

                    $ticket_trail_arr = array(
                        'ticketid' => $ticketid,
                        'codes' => 'TSFINDINGS',
                        'descs' => $findings_message,
                        'statusid' => 311,
                        'createdby' => user_id()
                    );
                    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);


                    $this->db->where(array('status > ' => 1, 'sysid' => $ticketid));
                    $this->db->update('ticketing_details_logs', array('status' => $findings_status));
                }


            }
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $qry = true;
            } else {
                $this->db->trans_rollback();
            }
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function add_circuit_row() {
        $data = array();
        $qry = false;
        $err = array();

        $ticketid = $this->input->post('ticketid');
        $circuitid = $this->input->post('circuitid');


        $this->db->trans_begin();

        $findings_message = 'TS - Circuit Added';
        $findings_status = $circuitid;
        if($circuitid==false || $circuitid==''){
            $findings_message = 'TS Circuit Removed!';
            $findings_status = 300;
        }

        $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
        $this->db->update('ticketing_details_logs_circuit_level', array('status' => 0, 'updatedby' => user_id()));
        $err[] = $this->db->_error_message();


        if( $findings_status != 00 ) {
            $finding_ins_arr = array(
                'ticketid' => $ticketid,
                'circuitid' => $circuitid,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('ticketing_details_logs_circuit_level', $finding_ins_arr);
            $err[] = $this->db->_error_message();
        }

        $ticket_trail_arr = array(
            'ticketid' => $ticketid,
            'codes' => 'TSFINDINGS',
            'descs' => $findings_message,
            'statusid' => 311,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
        $err[] = $this->db->_error_message();



        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
        $data['err'] = $err;
        return json_encode($data);
    }

    function add_circuit_row_group() {
        $data = array();
        $qry = false;
        $err = array();

        $groupid = $this->input->post('groupid');
        $circuitid = $this->input->post('circuitid');


        if($groupid) {

            $this->db->trans_begin();

            $qry_group_matrix = $this->db->select('ticketid')
                ->from('ticketing_details_logs_group_matrix')
                ->where('groupid', $groupid)
                ->get();

            if ($qry_group_matrix->num_rows() > 0) {

                $this->db->where('sysid', $groupid);
                $this->db->update('ticketing_details_logs_group', array('circuitid' => $circuitid));
                $data['updgrp'] = $this->db->_error_message();

                foreach ($qry_group_matrix->result() as $row) {

                    $ticketid = $row->ticketid;

                    $findings_message = 'TS - Circuit Added';
                    $findings_status = $circuitid;
                    if ($circuitid == false || $circuitid == '') {
                        $findings_message = 'TS Circuit Removed!';
                        $findings_status = 300;
                    }

                    $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
                    $this->db->update('ticketing_details_logs_circuit_level', array('status' => 0, 'updatedby' => user_id()));
                    $err[] = $this->db->_error_message();


                    if ($findings_status != 00) {
                        $finding_ins_arr = array(
                            'ticketid' => $ticketid,
                            'circuitid' => $circuitid,
                            'createdby' => user_id(),
                            'updatedby' => user_id()
                        );
                        $this->db->insert('ticketing_details_logs_circuit_level', $finding_ins_arr);
                        $err[] = $this->db->_error_message();
                    }

                    $ticket_trail_arr = array(
                        'ticketid' => $ticketid,
                        'codes' => 'TSFINDINGS',
                        'descs' => $findings_message,
                        'statusid' => 311,
                        'createdby' => user_id()
                    );
                    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
                    $err[] = $this->db->_error_message();
                }
            }
        }



        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
        $data['err'] = $err;
        return json_encode($data);
    }

    function add_equipments_row() {
        $data = array();
        $qry = false;
        $err = array();

        $ticketid = $this->input->post('ticketid');
        $findingid = $this->input->post('equipid');

        $this->db->trans_begin();

        $findings_message = 'TS - Equipment Added';
        $findings_status = 311;

        if($findingid==false || $findingid==''){
            $findings_message = 'TS Equipment Removed!';
            $findings_status = 300;
            // UPDATE FINDINGS // UPDATE CIRCUIT
            $this->db->where(array('status' => 1, 'ticketid' => $ticketid));
            $this->db->update('ticketing_details_logs_circuit_level', array('status' => 0, 'updatedby' => user_id()));

            $this->db->where(array('status' => 1, 'ticketid' => $ticketid));
            $this->db->update('ticketing_details_logs_findings', array('status' => 0, 'updatedby' => user_id()));
        }

        $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
        $this->db->update('ticketing_details_logs_equipments', array('status' => 0, 'updatedby' => user_id()));
        $err[] = $this->db->_error_message();

        if($findings_status!=300) {
            $finding_ins_arr = array(
                'ticketid' => $ticketid,
                'equipid' => $findingid,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('ticketing_details_logs_equipments', $finding_ins_arr);
            $err[] = $this->db->_error_message();
        }

        $ticket_trail_arr = array(
            'ticketid' => $ticketid,
            'codes' => 'TSFINDINGS',
            'descs' => $findings_message,
            'statusid' => $findingid,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
        $err[] = $this->db->_error_message();

        $this->db->where(array('status > ' => 1, 'sysid' => $ticketid));
        $this->db->update('ticketing_details_logs', array('status' => $findings_status));
        $err[] = $this->db->_error_message();

        $circuit_lvl = $this->db->select('typesid')->from('ticketing_outage_matrix_circuit_level')
            ->where(array('typesid' => $findingid))->get()->row();
        $circuit_lvl = ($circuit_lvl) ? true : false;

        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }
        $data['circuit'] = $circuit_lvl;
        $data['err'] = $err;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function add_equipments_row_group() {
        $data = array();
        $qry = false;
        $circuit_lvl = false;
        $err = array();

        $groupid = $this->input->post('groupid');
        $findingid = $this->input->post('equipid');


        $this->db->trans_begin();
        $qry_group_matrix = $this->db->select('ticketid')
            ->from('ticketing_details_logs_group_matrix')
            ->where('groupid', $groupid)
            ->get();

        if($qry_group_matrix->num_rows() > 0 ) {

            $this->db->where('sysid', $groupid);
            $this->db->update('ticketing_details_logs_group', array('status' => 311, 'equipid' => $findingid));
            $data['updeq'] = $this->db->_error_message();


            foreach($qry_group_matrix->result() as $row) {
                $ticketid = $row->ticketid;
                $data['ticketids'][] = $ticketid;
                $findings_message = 'TS - Equipment Group Added';
                $findings_status = 311;

                if ($findingid == false || $findingid == '') {
                    $findings_message = 'TS Equipment Removed!';
                    $findings_status = 300;
                    // UPDATE FINDINGS // UPDATE CIRCUIT
                    $this->db->where(array('status' => 1, 'ticketid' => $ticketid));
                    $this->db->update('ticketing_details_logs_circuit_level', array('status' => 0, 'updatedby' => user_id()));
                    $this->db->where(array('status' => 1, 'ticketid' => $ticketid));
                    $this->db->update('ticketing_details_logs_findings', array('status' => 0, 'updatedby' => user_id()));
                }

                $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
                $this->db->update('ticketing_details_logs_equipments', array('status' => 0, 'updatedby' => user_id()));
                $err[] = $this->db->_error_message();


                if ($findings_status != 300) {
                    $finding_ins_arr = array(
                        'ticketid' => $ticketid,
                        'equipid' => $findingid,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $this->db->insert('ticketing_details_logs_equipments', $finding_ins_arr);
                    $err[] = $this->db->_error_message();
                }

                $ticket_trail_arr = array(
                    'ticketid' => $ticketid,
                    'codes' => 'TSFINDINGS',
                    'descs' => $findings_message,
                    'statusid' => 311,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
                $err[] = $this->db->_error_message();


                $this->db->where(array('status > ' => 1, 'sysid' => $ticketid));
                $this->db->update('ticketing_details_logs', array('status' => $findings_status));
                $err[] = $this->db->_error_message();


            }


            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $qry = true;
            } else {
                $this->db->trans_rollback();
            }
        }



        $circuit_lvl = $this->db->select('typesid')->from('ticketing_outage_matrix_circuit_level')
            ->where(array('typesid' => $findingid))->get()->row();
        $circuit_lvl = ($circuit_lvl) ? true : false;


        $data['circuit'] = $circuit_lvl;
        $data['err'] = $err;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function select2_ts_status() {
        $data = array();
        $query = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg')
            ->from('prime_types_parameter AS tp')
            ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => '<span style="color: '.$row->colorbg.'; font-size: 14px;">'.$row->names.'</span>'
                );
            }
        }
        return json_encode($data);
    }

    function check_ts_outage_type() {
        $data = array();
        $typesid = $this->input->post('typesid');
        if($typesid) {
            $circuit_lvl = $this->db->select('typesid')->from('ticketing_outage_matrix_circuit_level')
                ->where(array('typesid' => $typesid))->get()->row();
            $circuit_lvl = ($circuit_lvl) ? true : false;
        }else{
            $circuit_lvl = false;
        }
        $data['circuit'] = $circuit_lvl;
        return json_encode($data);
    }

    function get_ts_logs() {
        $data = array();
        $ticketid = $this->input->post('ticketid');
        $query = $this->db->select('
             tdt.sysid,
             tdt.codes,
             tdt.descs,
             tdt.statusid,
             tdt.datecreated,
             tdt.remarks,
             su.username AS createdby
        ')
            ->from('ticketing_details_trails AS tdt')
            ->join('prime_system_users AS su', 'su.sysid = tdt.createdby', 'left')
            ->where(array('tdt.ticketid' => $ticketid))
            ->get();
        if($query->num_rows() > 0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'sysid' => $row->sysid,
                    'codes' => $row->codes,
                    'action' => $row->descs,
                    'descs' => get_types_label_format($row->statusid),
                    'datecreated' => $row->datecreated,
                    'remarks' => $row->remarks,
                    'createdby' => $row->createdby
                );
            }
        }
        return json_encode($data);
    }

    function draw_summary_table() {
        $data = array();

        $qry = false;
        $heads = '';

        $query = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg')
            ->from('prime_types_parameter AS tp')
            ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
            ->get();

        $col_cnt = $query->num_rows();
        if($col_cnt > 0) {
            $qry = true;

            $head_arr = array();

            $heads .= '<th>Finding Names</th>';
            foreach($query->result() as $hrow) {
                $heads .= '<th>'.$hrow->names.'</th>';
                $head_arr[] = $hrow;
            }


            $query_findings = $this->db->select()->from('prime_types_parameter')
                ->where(array('status' => 1, 'codes' => 'TCEQUIPMENT'))
                ->get();
            if($query_findings->num_rows() > 0) {
                foreach($query_findings->result() as $lrow) {
                    $data['list'][] = $head_arr;
                }
            }
        }

        $data['heads'] = $heads;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function select2_circuit_level() {

        $data = array();
        $query = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'TSCIRCUITLEVEL'))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function select2_outages() {

        $data = array();
        $query = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'OUTAGETYPE'))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function add_tc_info() {

        $data = array();
        $q = false;
        $ins = true;
        $msg = '';
        $userid = user_id();
        $func = 'error';
        $ins_err = array();

        $complaints = 'Information';
        $address = $this->input->post('address');
        $remarks = $this->input->post('remarks');
        $landmark = $this->input->post('landmark');
        $district = $this->input->post('district');
        $priority = $this->input->post('priority');
        $acctid = $this->input->post('acctid');
        $repsource = 390;

        //FINDINGS

        $equipments = $this->input->post('equipments');
        $findings = $this->input->post('findings');
        $circuits = $this->input->post('circuits');

        $team = $this->input->post('team');
        $status = $this->input->post('status');

        $this->db->trans_begin();

        $landmark_ids = '';
        $landmark_id = array();

        $landmark_arr = explode(',', $landmark);

        if(count($landmark_arr) > 0) {
            foreach($landmark_arr as $lrow) {
                $check_landmark = $this->db->select()->from('address_landmark')->where('sysid', $lrow)->get()->row();
                if($check_landmark) {

                    $landmark_id[] = $lrow;
                }else {

                    $ins_land_arr = array(
                        'distid' => $district,
                        'texts' => $lrow
                    );
                    $this->db->insert('address_landmark', $ins_land_arr);
                    $landmark_id_ins = $this->db->insert_id();
                    $landmark_id[] = $landmark_id_ins;
                }
            }
            $landmark_ids = implode(',', $landmark_id);
        }

        $ticket_ins_arr = array(
            'acctid' => ($acctid) ? $acctid : 0,
            'repsource' => $repsource,
            'compname' => 'PECO - Info',
            'address' => $address,
            'remarks' => $remarks,
            'priority' => $priority,
            'district' => $district,
            'tickettype' => 1004,
            'landmarks' => $landmark_ids,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => $status
        );
        $this->db->insert('ticketing_details_logs', $ticket_ins_arr);
        $ticket_id = $this->db->insert_id();
        $ins_err[] = $this->db->_error_message();

        if($ticket_id > 0) {
            $ticket_trail_arr = array(
                'ticketid'  => $ticket_id,
                'codes' => 'TSINFO',
                'descs' => 'TS - Trouble Information',
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
            $ins_err[] = $this->db->_error_message();

            // EQUIPMENT
            if ($equipments && $equipments > 0) {
                $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                $this->db->update('ticketing_details_logs_equipments', array('status' => 0));
                $equipment_arr = array(
                    'ticketid' => $ticket_id,
                    'equipid' => $equipments,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('ticketing_details_logs_equipments', $equipment_arr);
                $ins_err[] = $this->db->_error_message();
            }

            // FINDINGS
            if ($findings && $findings > 0) {
                $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                $this->db->update('ticketing_details_logs_findings', array('status' => 0));
                $findings_arr = array(
                    'ticketid' => $ticket_id,
                    'findingid' => $findings,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('ticketing_details_logs_findings', $findings_arr);
                $ins_err[] = $this->db->_error_message();
            }


            // CIRCUIT LEVEL
            if ($circuits && $circuits > 0) {
                $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                $this->db->update('ticketing_details_logs_circuit_level', array('status' => 0));
                $circuit_arr = array(
                    'ticketid' => $ticket_id,
                    'circuitid' => $circuits,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('ticketing_details_logs_circuit_level', $circuit_arr);
                $ins_err[] = $this->db->_error_message();
            }



            // TEAM
            if($team && $team > 0) {
                $group_id = 0;
                $circuit_arr = array(
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('ticketing_details_assignments_group', $circuit_arr);
                $group_id = $this->db->insert_id();

                $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                $this->db->update('ticketing_details_assignments', array('status' => 0));
                $circuit_arr = array(
                    'ticketid' => $ticket_id,
                    'typesid' => $team,
                    'groupid' => $group_id,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('ticketing_details_assignments', $circuit_arr);
                $ins_err[] = $this->db->_error_message();
            }

        }


        $err_msg = '';
        if(count($ins_err) > 0) {
            foreach($ins_err as $row) {
                $err_msg .= $row;
            }
        }

        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $msg = 'New Ticket Created!';
            $func = 'success';
            $q = true;
        }else{
            $this->db->trans_rollback();
            $func = 'warning';
        }


        $data['input'] = $this->input->post();
        $data['landmarkids'] = $landmark_ids;
        $data['qry'] = $q;
        $data['func'] = $func;
        $data['msg'] = $msg . $err_msg;
        $data['title'] = 'New Trouble Call';
        $data['err'] = implode(',', $ins_err);
        return json_encode($data);
    }

    function add_tc_entry() {
        $data = array();
        $q = false;

        $ins = true;
        $msg = '';
        $func = 'error';

        $userid = user_id();
        $ins_err = array();
        $ticket_id = 0;

        $complaints = $this->input->post('outage');
        $firstname = $this->input->post('firstname');
        $middlename = $this->input->post('middlename');
        $lastname = $this->input->post('lastname');
        $address = $this->input->post('custaddr');
        $contact = $this->input->post('contactno');
        $remarks = $this->input->post('remarks');
        $district = $this->input->post('district');
        $landmark = $this->input->post('landmark');
        $barangay = $this->input->post('barangay');
        $priority = $this->input->post('priority');
        $acctid = $this->input->post('acctid');
        $repsource = $this->input->post('repsource');
        $mapurl = $this->input->post('mapurl');
        $personid = $this->input->post('personid');

        $createdby = $userid;

        $this->db->trans_begin();

        $barangay_ids = '';
        $landmark_ids = '';

        $barangay_id = array();
        
        $barangay_arr = explode(',', $barangay);

        if(count($barangay_arr) > 0) {
            foreach($barangay_arr as $brow) {
                $check_barangay = $this->db->select()->from('address_barangay')->where('sysid', $brow)->get()->row();
                if($check_barangay) {
                    $barangay_id[] = $brow;
                }else {
                    $ins_brgy_arr = array(
                        'distid' => $district,
                        'texts' => $brow
                    );
                    $this->db->insert('address_barangay', $ins_brgy_arr);
                    $barangay_id_ins = $this->db->insert_id();
                    $barangay_id[] = $barangay_id_ins;
                    $ins_err[] = $this->db->_error_message();
                }
            }
            $barangay_ids = implode(',', $barangay_id);
        }

        if(count($barangay_id) > 0) {
            $landmar_ins_cnt = 0;
            foreach($barangay_id as $bsrow) {
                $landmark_id = array();
                $landmark_arr = explode(',', $landmark);
                if (count($landmark_arr) > 0) {
                    foreach ($landmark_arr as $lrow) {
                        $check_landmark = $this->db->select()->from('address_landmark')->where('sysid', $lrow)->get()->row();
                        if ($check_landmark) {

                            $landmark_id[] = $lrow;
                        } else {

                            $ins_land_arr = array(
                                'distid' => $district,
                                'brgyid' => $bsrow,
                                'texts' => $lrow
                            );
                            $this->db->insert('address_landmark', $ins_land_arr);
                            $landmark_id_ins = $this->db->insert_id();
                            $landmark_id[] = $landmark_id_ins;
                            $ins_err[] = $this->db->_error_message();
                        }
                    }
                    $landmark_ids = implode(',', $landmark_id);
                    $landmar_ins_cnt += 1;
                }
            }
        }

        if($landmar_ins_cnt>0) {

            if($personid) {
                $complainants = $personid;
            }else {
                $qry_check_person = $this->db->select('sysid')->from('person')
                    ->where(array('lastname' => $lastname, 'firstname' => $firstname))
                    ->get()->row();
                if ($qry_check_person) {
                    $complainants = $qry_check_person->sysid;
                } else {
                    $new_firstname = ($firstname != '') ? ucwords(strtolower($firstname)) : '';
                    $new_middlename = ($middlename != '') ? ucwords(strtolower($middlename)) : '';
                    $new_lastname = ($lastname != '') ? ucwords(strtolower($lastname)) : '';
                    $person_ins = array(
                        'firstname' => $new_firstname,
                        'middlename' => $new_middlename,
                        'lastname' => $new_lastname
                    );
                    $ins_person_qry = $this->db->insert('person', $person_ins);
                    $complainants = ($ins_person_qry) ? $this->db->insert_id() : 0;
                }
            }


            $ticket_ins_arr = array(
                'acctid' => ($acctid) ? $acctid : 0,
                'repsource' => $repsource,
                'complainants' => $complainants,
                'address' => $address,
                'contact' => $contact,
                'remarks' => $remarks,
                'tickettype' => ($complaints) ? $complaints : 0,
                'priority' => $priority,
                'district' => $district,
                'landmarks' => $landmark_ids,
                'barangays' => $barangay_ids,
                'mapurl' => ($mapurl) ? $mapurl : null,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('ticketing_details_logs', $ticket_ins_arr);
            $ticket_id = $this->db->insert_id();
            $ins_err[] = $this->db->_error_message();

            if ($ticket_id > 0) {
                $ticket_trail_arr = array(
                    'ticketid' => $ticket_id,
                    'codes' => 'TSNEW',
                    'descs' => 'TS - data creation',
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
                $ins_err[] = $this->db->_error_message();
            }


            $err_msg = '';
            if (count($ins_err) > 0) {
                $err_msg = implode(', ', $ins_err);
            }


            $files = $_FILES;
            if($files) {
                $cpt = count($_FILES['pics']['name']);
                if ($cpt > 0) {
                    $this->load->library('upload');

                    for ($pi = 0; $pi < $cpt; $pi++) {

                        $data['picsarr'][] = 'PI_' . $pi;
                        $date = new DateTime();

                        $new_name = $date->format('YmdHis');

                        $upload_path = './uploads/attachments/outages/';

                        $outage_dir = str_pad($ticket_id, 8, '0', STR_PAD_LEFT);

                        $full_path = $upload_path . $outage_dir . '/';

                        if (!is_dir($full_path)) {
                            mkdir($upload_path . $outage_dir, 0777, TRUE);
                        }

                        $_FILES['userfile']['name'] = $files['pics']['name'][$pi];
                        $_FILES['userfile']['type'] = $files['pics']['type'][$pi];
                        $_FILES['userfile']['tmp_name'] = $files['pics']['tmp_name'][$pi];
                        $_FILES['userfile']['error'] = $files['pics']['error'][$pi];
                        $_FILES['userfile']['size'] = $files['pics']['size'][$pi];

                        $this->upload->initialize($this->set_upload_options($full_path, $new_name));

                        if (!$this->upload->do_upload()) {
                            $data['picmsg'] = $this->upload->display_errors();
                        } else {
                            $data['picmsg'] = $this->upload->data();
                        }
                    }
                }
            }

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $msg = 'New Ticket Created! Ticket No: ' . $ticket_id;
                $func = 'success';
                $q = true;
            } else {
                $this->db->trans_rollback();
                $msg = $err_msg;
                $func = 'warning';
            }
        }else{
            $err_msg = '';
            $func = 'warning';
            $msg = 'Error Landmark!';
        }

        $data['input'] = $this->input->post();
        $data['landmarkids'] = $landmark_ids;
        $data['barangayids'] = $barangay_id;
        $data['qry'] = $q;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = 'New Trouble Call';
        return json_encode($data);
    }

    private function set_upload_options($full_path, $new_name)
    {
        //upload an image options
        $config = array();

        $config['upload_path'] = $full_path;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 5000;
        $config['max_width'] = 4024;
        $config['max_height'] = 3768;
        //$config['encrypt_name']         = TRUE;
        $config['file_name'] = $new_name;

        return $config;
    }



    function edit_tc_info() {

        $data = array();
        $q = false;
        $ins = true;
        $msg = '';
        $userid = user_id();
        $func = 'error';
        $ins_err = array();
        $ids = $this->input->post('ids');
        $ids_arr = explode(',', $ids);

        //FINDINGS
        $equipments = $this->input->post('equipments');
        $findings = $this->input->post('findings');
        $circuits = $this->input->post('circuits');
        $outagetype = $this->input->post('outagetype');

        $team = $this->input->post('team');
        $status = $this->input->post('status');

        $group_outage_id = 0;
        $upd_cnt = 0;
        if($userid && $userid >0) {

            if (count($ids_arr) > 0) {

                if($outagetype) {
                    /*
                    $qry_group_ids_exists = $this->db->select('groupid')
                        ->from('ticketing_details_logs_group_matrix')
                        ->where_in('ticketid', $ids_arr)->get();
                    if($qry_group_ids_exists->num_rows()>0) {
                        foreach($qry_group_ids_exists->result() as $row) {

                            $this->db->where(array('sysid' => $row->groupid, 'status > ' => 0));
                            $this->db->update('ticketing_details_logs_group', array('status' => 0, 'updatedby' => user_id()));
                            $ins_err[] = $this->db->_error_message();



                            $this->db->where('groupid', $row->groupid);
                            $this->db->update('ticketing_details_logs_group_matrix', array('status' => 0, 'updatedby' => user_id()));
                            $ins_err[] = $this->db->_error_message();
                        }
                    }
                    */

                    $ins_ticket_gorup_arr = array(
                        'typesid' => $outagetype,
                        'equipid' => ($equipments) ? $equipments : null,
                        'findingsid' => ($findings) ? $findings : null,
                        'circuitid' => ($circuits) ? $circuits : null,
                        'teamid' => ($team) ? $team : null,
                        'createdby' => $userid,
                        'updatedby' => $userid,
                        'status' => $status
                    );
                    $this->db->insert('ticketing_details_logs_group', $ins_ticket_gorup_arr);
                    $group_outage_id = $this->db->insert_id();
                    $data['groupids'][] = $group_outage_id;
                    $ins_err[] = $this->db->_error_message();

                }

                foreach ($ids_arr as $ticket_id) {

                    if($group_outage_id>0) {
                        $ins_group_matrix_arr = array(
                            'groupid' => $group_outage_id,
                            'ticketid' => $ticket_id,
                            'updatedby' => user_id(),
                            'createdby' => user_id()
                        );
                        $this->db->insert('ticketing_details_logs_group_matrix', $ins_group_matrix_arr);
                    }

                    if ($status > 0) {
                        $this->db->where(array('sysid' => $ticket_id));
                        $this->db->update('ticketing_details_logs', array('status' => $status));
                    }

                    $ticket_trail_arr = array(
                        'ticketid' => $ticket_id,
                        'codes' => 'TSUPDATES',
                        'descs' => 'TS - Trouble Updates Status',
                        'createdby' => user_id()
                    );
                    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
                    $ins_err[] = $this->db->_error_message();

                    // EQUIPMENT
                    if ($equipments && $equipments > 0) {
                        $upd_cnt += 1;
                        $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                        $this->db->update('ticketing_details_logs_equipments', array('status' => 0));
                        $equipment_arr = array(
                            'ticketid' => $ticket_id,
                            'equipid' => $equipments,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('ticketing_details_logs_equipments', $equipment_arr);
                        $ins_err[] = $this->db->_error_message();
                    }

                    // FINDINGS
                    if ($findings && $findings > 0) {
                        $upd_cnt += 1;
                        $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                        $this->db->update('ticketing_details_logs_findings', array('status' => 0));
                        $findings_arr = array(
                            'ticketid' => $ticket_id,
                            'findingid' => $findings,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('ticketing_details_logs_findings', $findings_arr);
                        $ins_err[] = $this->db->_error_message();
                    }


                    // CIRCUIT LEVEL
                    if ($circuits && $circuits > 0) {
                        $upd_cnt += 1;
                        $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                        $this->db->update('ticketing_details_logs_circuit_level', array('status' => 0));
                        $circuit_arr = array(
                            'ticketid' => $ticket_id,
                            'circuitid' => $circuits,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('ticketing_details_logs_circuit_level', $circuit_arr);
                        $ins_err[] = $this->db->_error_message();
                    }


                    // TEAM
                    if ($team && $team > 0) {
                        $upd_cnt += 1;
                        $circuit_arr = array(
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('ticketing_details_assignments_group', $circuit_arr);
                        $group_id = $this->db->insert_id();

                        $this->db->where(array('ticketid' => $ticket_id, 'status' => 1));
                        $this->db->update('ticketing_details_assignments', array('status' => 0));
                        $circuit_arr = array(
                            'ticketid' => $ticket_id,
                            'typesid' => $team,
                            'groupid' => $group_id,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('ticketing_details_assignments', $circuit_arr);
                        $ins_err[] = $this->db->_error_message();
                    }
                }
            }


            $err_msg = '';
            if (count($ins_err) > 0) {
                $err_msg = implode(', ', $ins_err);
            }

            if ($this->db->trans_status() === TRUE && $upd_cnt > 0) {
                $this->db->trans_commit();
                $msg = 'New Ticket Created!';
                $func = 'success';
                $q = true;
            } else {
                $this->db->trans_rollback();
                $func = 'warning';
            }
        }else{
            $msg = 'Session timeout!';
            $func = 'warning';
        }


        $data['input'] = $this->input->post();
        $data['qry'] = $q;
        $data['func'] = $func;
        $data['msg'] = $msg . $err_msg;
        $data['title'] = 'New Trouble Call';
        return json_encode($data);
    }


    function remove_list_group() {
        $data = array();
        $ticketid = $this->input->post('ticketid');
        $groupid = $this->input->post('groupid');
        $q = false;

        $this->db->trans_begin();
        $this->db->where(array('groupid' => $groupid, 'ticketid' => $ticketid, 'status > ' => 0));
        $this->db->update('ticketing_details_logs_group_matrix', array('status' => 0, 'updatedby' => user_id()));
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $msg = 'New Ticket Created!';
            $func = 'success';
            $q = true;
        } else {
            $this->db->trans_rollback();
            $func = 'warning';
        }

        $data['qry'] = $q;

        return json_encode($data);
    }

    function udpate_etc_row() {
        $data = array();
        $ticketid = $this->input->post('ticketid');
        $etc = $this->input->post('etc');
        $q = false;

        $this->db->trans_begin();
        $this->db->where(array('sysid' => $ticketid));
        $this->db->update('ticketing_details_logs', array('etc' => $etc, 'updatedby' => user_id()));
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $q = true;
        } else {
            $this->db->trans_rollback();
        }

        $data['qry'] = $q;
        return json_encode($data);
    }

    function udpate_etc_row_group() {
        $data = array();
        $groupid = $this->input->post('groupid');
        $etc = $this->input->post('etc');
        $q = false;

        if($groupid) {

            $this->db->trans_begin();

            $qry_group_matrix = $this->db->select('ticketid')
                ->from('ticketing_details_logs_group_matrix')
                ->where('groupid', $groupid)
                ->get();

            if ($qry_group_matrix->num_rows() > 0) {

                $this->db->where('sysid', $groupid);
                $this->db->update('ticketing_details_logs_group', array('etc' => $etc));
                $data['updgrp'] = $this->db->_error_message();

                foreach ($qry_group_matrix->result() as $row) {
                    $ticketid = $row->ticketid;

                    $this->db->where(array('sysid' => $ticketid));
                    $this->db->update('ticketing_details_logs', array('etc' => $etc, 'updatedby' => user_id()));
                }
            }
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $q = true;
            } else {
                $this->db->trans_rollback();
            }
        }

        $data['qry'] = $q;
        return json_encode($data);
    }


    function get_select2_groups() {
        $data = array();
        $query = $this->db->select()
            ->from('ticketing_details_logs_group')
            ->where(array('status > ' => 0, 'status != ' => 314))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  'GROUP_'.str_pad($row->sysid, 4, '0', STR_PAD_LEFT)
                );
            }
        }
        echo json_encode($data);
    }

    function followup_group() {
        $data = array();
        $q = false;
        $groupid = $this->input->post('groupid');
        if($groupid) {

            $this->db->trans_begin();

            $qry_group_matrix = $this->db->select('ticketid')
                ->from('ticketing_details_logs_group_matrix')
                ->where('groupid', $groupid)
                ->get();

            if ($qry_group_matrix->num_rows() > 0) {


                foreach ($qry_group_matrix->result() as $row) {
                    $ticketid = $row->ticketid;
                    $ticket_trail_arr = array(
                        'ticketid' => $ticketid,
                        'codes' => 'FOLLOWUP',
                        'descs' => 'TS - Followup',
                        'createdby' => user_id()
                    );
                    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

                }
            }
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $q = true;
            } else {
                $this->db->trans_rollback();
            }
        }

        $data['qry'] = $q;
        return json_encode($data);
    }

    function followup_ticket() {
        $data = array();
        $q = false;
        $ticketid = $this->input->post('ticketid');
        if($ticketid) {
            $ticket_trail_arr = array(
                'ticketid' => $ticketid,
                'codes' => 'FOLLOWUP',
                'descs' => 'TS - Followup',
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $q = true;
            } else {
                $this->db->trans_rollback();
            }
        }
        $data['qry'] = $q;
        return json_encode($data);
    }

    function tc_average_compute_list($export = false, $datefrom = false, $dateto = false, $status = false) {
        $data = array();

        $table = $this->input->post('table');
        if($export == 1) {
            $table = true;
        }


        $gen_sum_seconds = 0;
        $gen_num = 0;

        $shift1 = false;
        $shift1_unit = 0;
        $shift_1_sum_seconds = 0;
        $shift_1_num = 0;
        $shift2 = false;
        $shift2_unit = 0;
        $shift_2_sum_seconds = 0;
        $shift_2_num = 0;
        $shift3 = false;
        $shift3_unit = 0;
        $shift_3_sum_seconds = 0;
        $shift_3_num = 0;

        $gen = 0;
        $gen_unit = 0;


        if($export == 1) {
            //load our new PHPExcel library
            $this->load->library('excel');
            //activate worksheet number 1
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('TROUBLECALL DETAILED');
            //set cell A1 content with some text
            $this->excel->getActiveSheet()->setCellValue('A1', 'TCNO');
            $this->excel->getActiveSheet()->setCellValue('B1', 'NAME');
            $this->excel->getActiveSheet()->setCellValue('C1', 'ADDRESS');
            $this->excel->getActiveSheet()->setCellValue('D1', 'DATE ENCODED');
            $this->excel->getActiveSheet()->setCellValue('E1', 'DATE UPDATED');
            $this->excel->getActiveSheet()->setCellValue('F1', 'HOURS');
            $this->excel->getActiveSheet()->setCellValue('G1', 'INITIAL COMPLAINTS');
            $this->excel->getActiveSheet()->setCellValue('H1', 'EQUIPMENT');
            $this->excel->getActiveSheet()->setCellValue('I1', 'FINDINGS');
            $this->excel->getActiveSheet()->setCellValue('J1', 'CIRCUIT');
            $this->excel->getActiveSheet()->setCellValue('K1', 'DESC');
            $this->excel->getActiveSheet()->setCellValue('L1', 'ACTION');
            $this->excel->getActiveSheet()->setCellValue('M1', 'SHIFT');
            $this->excel->getActiveSheet()->setCellValue('N1', 'DISTRICT');
            $this->excel->getActiveSheet()->setCellValue('O1', 'REPORTED BY');
            if($status === '0' || $status > 1) {
                $this->excel->getActiveSheet()->setCellValue('P1', 'STATUS');
            }

            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('E1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('F1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('G1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('H1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('I1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('J1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('K1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('L1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('M1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('M1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('N1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('O1')->getFont()->setSize(12);
            $this->excel->getActiveSheet()->getStyle('O1')->getFont()->setBold(true);
            if($status === '0' || $status > 1) {
                $this->excel->getActiveSheet()->getStyle('P1')->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('P1')->getFont()->setBold(true);
            }
        }


        $list = array();

        $query_ts = $this->db->select('sysid')->from('prime_types_parameter')
            ->where(array('codes' => 'TS', 'status' => 1))
            ->get();
        if ($query_ts->num_rows() > 0) {
            foreach ($query_ts->result() as $tsrow) {
                $ts_arr[] = $tsrow->sysid;
            }
        }
        if($datefrom) {
            $this->db->where('tdl.datecreated >= ', $datefrom);
            if($dateto) {
                $this->db->where('tdl.datecreated <= ', $dateto);
            }
        }
        if($export == false) {
            $this->db->limit(100);
        }
        $this->db->where_in('tdl.tickettype', $ts_arr);

        $data['status'] = $status;

        if($status === '0' || $status > 1) {
            if($status != '0') {
                $this->db->where(array('tdl.status' => $status));
            }
        }else{
            $this->db->where(array('tdl.status' => 314));
        }
        $qry_gen_ave_tc = $this->db->select('tdl.sysid, tdl.datecreated, tdl.dateupdated, tdl.status, tdl.address, d.codes AS dist, tdl.tickettype, tdl.repsource, tdl.status')
            ->select("CONCAT(p.lastname, ', ', p.firstname) AS name", false)
            ->from('ticketing_details_logs AS tdl')
            ->join('person AS p', 'p.sysid = tdl.complainants', 'left')
            ->join('address_districts AS d', 'd.sysid = tdl.district', 'left')
            ->order_by('tdl.datecreated', 'desc')
            ->get();


        if($qry_gen_ave_tc->num_rows() > 0) {

            $shift_1_arr = array();
            $shift_2_arr = array();
            $shift_3_arr = array();
            $excel_num = 1;

            foreach ($qry_gen_ave_tc->result() as $row) {


                $timeFirst  = strtotime($row->datecreated);
                $timeSecond = strtotime($row->dateupdated);
                $differenceInSeconds = $timeSecond - $timeFirst;

                $gen_sum_seconds += $differenceInSeconds;
                $gen_num++;

                $shift = '';

                $status_text = get_types_label_format($row->status, false, false, false, false, false, true)->text;
                $repsource = get_types_label_format($row->repsource, false, false, false, false, false, true)->text;
                $init_complaints = get_types_label_format($row->tickettype, false, false, false, false, false, true)->text;

                $date_updated = date_formating($row->dateupdated, 'Y-m-d H:i:s', 'H:i');

                if($this->isBetween('23:00', '07:00', $date_updated)) {
                    $shift1 = true;
                    $shift = 'Shift 1';

                    $shift_1_sum_seconds += $differenceInSeconds;
                    $shift_1_num += 1;
                    $shift_1_arr[] = array('tcno' => $row->sysid, 'created' => $row->datecreated, 'updated' => $row->dateupdated);
                }

                if($this->isBetween('07:00', '15:00', $date_updated)) {
                    $shift2 = true;
                    $shift = 'Shift 2';

                    $shift_2_sum_seconds += $differenceInSeconds;
                    $shift_2_num += 1;
                    $shift_2_arr[] = array('tcno' => $row->sysid, 'created' => $row->datecreated, 'updated' => $row->dateupdated);
                }

                if($this->isBetween('15:00', '23:00', $date_updated)) {
                    $shift3 = true;
                    $shift = 'Shift 3';

                    $shift_3_sum_seconds += $differenceInSeconds;
                    $shift_3_num += 1;
                    $shift_3_arr[] = array('tcno' => $row->sysid, 'created' => $row->datecreated, 'updated' => $row->dateupdated);
                }

                $dif_min = $differenceInSeconds / 60;
                $dif_hour = $dif_min / 60;


                if($table == true) {
                    $action = '';
                    $desc = '';
                    $action_1 = '';
                    $qry_trail = $this->db->select('remarks, descs')->from('ticketing_details_trails')
                        ->where(array('ticketid' => $row->sysid, 'statusid' => 314))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();
                    if ($qry_trail) {
                        if ($qry_trail->remarks == '') {
                            $action = strip_tags_content($qry_trail->descs);
                            $desc = strip_tags_content($qry_trail->descs);
                            $action_1 = strip_tags_content($qry_trail->descs);
                        } else {
                            $action = strip_tags_content($qry_trail->descs) . '<br><small>' . $qry_trail->remarks.'</small>';
                            $action_1 = $qry_trail->remarks;
                            $desc = strip_tags_content($qry_trail->descs);
                        }
                    }

                    $tcequipments_id = null;
                    $tcequipments_name = 'N/A';
                    $qry_equipment = $this->db->select('lf.equipid, tp.desc, tp.names')
                        ->from('ticketing_details_logs_equipments AS lf')
                        ->join('prime_types_parameter AS tp', 'tp.sysid = lf.equipid', 'left')
                        ->where(array('lf.ticketid' => $row->sysid, 'lf.status' => 1))
                        ->get()->row();
                    if ($qry_equipment) {
                        $tcequipments_id = $qry_equipment->equipid;
                        $tcequipments_name = ($qry_equipment->desc != '') ? $qry_equipment->desc : $qry_equipment->names;
                    }


                    $findings_id = null;
                    $findings_name = 'N/A';
                    $qry_findings = $this->db->select('lf.findingid, tp.desc, tp.names')
                        ->from('ticketing_details_logs_findings AS lf')
                        ->join('prime_types_parameter AS tp', 'tp.sysid = lf.findingid', 'left')
                        ->where(array('lf.ticketid' => $row->sysid, 'lf.status' => 1))
                        ->get()->row();
                    if ($qry_findings) {
                        $findings_name = ($qry_findings->desc != '') ? $qry_findings->desc : $qry_findings->names;
                    }

                    // CIRCUIT LEVEL
                    $circuit_level_name = 'N/A';
                    $check_circuit_mtrx = $this->db->select()->from('ticketing_outage_matrix_circuit_level')
                        ->where('typesid', $tcequipments_id)->get()->row();
                    if ($check_circuit_mtrx) {
                        $qry_circuit = $this->db->select('cl.circuitid, tp.desc, tp.names')
                            ->from('ticketing_details_logs_circuit_level AS cl')
                            ->join('prime_types_parameter AS tp', 'tp.sysid = cl.circuitid', 'left')
                            ->where(array('cl.ticketid' => $row->sysid, 'cl.status' => 1))
                            ->get()->row();
                        if ($qry_circuit) {
                            $circuit_level_name = ($qry_circuit->desc != '') ? $qry_circuit->desc : $qry_circuit->names;
                        }
                    }


                    $list[] = array(
                        'tcno' => $row->sysid,
                        'name' => $row->name . '<br><small>'.$row->address.'</small>',
                        'created' => $row->datecreated,
                        'updated' => $row->dateupdated,
                        'diffsecond' => number_format($differenceInSeconds, 2),
                        'diffmin' => number_format($dif_min, 2),
                        'diffhour' => number_format($dif_hour, 2),
                        'equipment' => $tcequipments_name,
                        'findings' =>$findings_name,
                        'circuit' => $circuit_level_name,
                        'action' => $action,
                        'shift' => $shift,
                        'status' => $status_text
                    );

                    if($export == 1) {
                        $excel_num += 1;
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->sysid);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->name);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->address);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->datecreated);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->dateupdated);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, number_format($dif_hour, 2));
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $init_complaints);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $tcequipments_name);
                        $this->excel->getActiveSheet()->setCellValue('I' . $excel_num, $findings_name);
                        $this->excel->getActiveSheet()->setCellValue('J' . $excel_num, $circuit_level_name);
                        $this->excel->getActiveSheet()->setCellValue('K' . $excel_num, $action_1);
                        $this->excel->getActiveSheet()->setCellValue('L' . $excel_num, $desc);
                        $this->excel->getActiveSheet()->setCellValue('M' . $excel_num, $shift);
                        $this->excel->getActiveSheet()->setCellValue('N' . $excel_num, $row->dist);
                        $this->excel->getActiveSheet()->setCellValue('O' . $excel_num, $repsource);


                        if($status === '0' || $status > 1) {
                            $this->excel->getActiveSheet()->setCellValue('P' . $excel_num, $status_text);
                        }
                    }
                }

            }

            $gen_ave = (($gen_sum_seconds / $gen_num)/60/60);

            $gen = ($gen_ave>24) ? number_format($gen_ave/24, 3) : number_format($gen_ave, 3);
            $gen_unit = ($gen_ave>24) ? 'Days' : 'Hrs';

            $shift1_ave = 0;
            if($shift_1_sum_seconds>0 && $shift_1_num>0) {
                $shift1_ave = (($shift_1_sum_seconds / $shift_1_num) / 60 / 60);
            }
            $shift1 = ($shift1_ave>24) ? number_format($shift1_ave/24, 3) : number_format($shift1_ave, 3);
            $shift1_unit = ($shift1_ave>24) ? 'Days' : 'Hrs';

            $shift2_ave = (($shift_2_sum_seconds / $shift_2_num)/60/60);
            $shift2 = ($shift2_ave>24) ? number_format($shift2_ave/24, 3) : number_format($shift2_ave, 3);
            $shift2_unit = ($shift2_ave>24) ? 'Days' : 'Hrs';

            $shift3_ave = (($shift_3_sum_seconds / $shift_3_num)/60/60);
            $shift3 = ($shift3_ave>24) ? number_format($shift3_ave/24, 3) : number_format($shift3_ave, 3);
            $shift3_unit = ($shift3_ave>24) ? 'Days' : 'Hrs';
        }

        $data['general'] = array('ave' => number_format($gen, 2), 'unit' => $gen_unit, 'cnt' => number_format($gen_num, 0));

        $data['shift1'] = array('ave' => number_format($shift1, 2), 'unit' => $shift1_unit, 'cnt' => number_format($shift_1_num, 0));
        $data['shift2'] = array('ave' => number_format($shift2, 2), 'unit' => $shift2_unit, 'cnt' => number_format($shift_2_num, 0));
        $data['shift3'] = array('ave' => number_format($shift3, 2), 'unit' => $shift3_unit, 'cnt' => number_format($shift_3_num, 0));

        if($export == 1) {

            $filename='TC_ACCOMPLISHMENTS_ASOF_'.date('Y-m-d').'.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');

        }

        $data['list'] = $list;

        return json_encode($data);
    }

    function isBetween($from, $till, $input) {
        $f = DateTime::createFromFormat('!H:i', $from);
        $t = DateTime::createFromFormat('!H:i', $till);
        $i = DateTime::createFromFormat('!H:i', $input);
        if ($f > $t) $t->modify('+1 day');
        return ($f <= $i && $i <= $t) || ($f <= $i->modify('+1 day') && $i <= $t);
    }

    function tc_average_compute() {
        $data = array();
        $gen_num = 0;
        $gen_sum_seconds = 0;
        $gen = 0;
        $gen_unit = 'Hrs';
        $qry_gen_ave_tc = $this->db->select('sysid,datecreated, dateupdated, status')
            ->from('ticketing_details_logs')
            ->where(array('status' => 314))
            ->get();
        if($qry_gen_ave_tc->num_rows() > 0) {
            foreach($qry_gen_ave_tc->result() as $grow) {
                $timeFirst  = strtotime($grow->datecreated);
                $timeSecond = strtotime($grow->dateupdated);
                $differenceInSeconds = $timeSecond - $timeFirst;
                $gen_sum_seconds += $differenceInSeconds;
                $gen_num++;
            }
            $gen_ave = (($gen_sum_seconds/60)/60)/60;
            $gen = ($gen_ave>24) ? number_format($gen_ave/24, 3) : number_format($gen_ave, 3);
            $gen_unit = ($gen_ave>24) ? 'Days' : 'Hrs';
        }

        $shift1 = 0;
        $shift1_num = 0;
        $shift1_sum_seconds = 0;
        $shift1_unit = 'Hrs';
        $this->db->where("HOUR(dateupdated) >= ", 23);
        $this->db->or_where("HOUR(dateupdated) <= ", 7);
        $qry_shift1_ave_tc = $this->db->select('sysid,datecreated, dateupdated, status')
            ->from('ticketing_details_logs')
            ->where(array('status' => 314))
            ->get();
        if($qry_shift1_ave_tc->num_rows() > 0) {

            foreach($qry_shift1_ave_tc->result() as $s1row) {
                $timeFirst  = strtotime($s1row->datecreated);
                $timeSecond = strtotime($s1row->dateupdated);
                $differenceInSeconds = $timeSecond - $timeFirst;
                $shift1_sum_seconds += $differenceInSeconds;
                $shift1_num++;
            }
            $shift1_ave = (($shift1_sum_seconds/60)/60)/60;
            $shift1 = ($shift1_ave>24) ? number_format($shift1_ave/24, 3) : number_format($shift1_ave, 3);
            $shift1_unit = ($shift1_ave>24) ? 'Days' : 'Hrs';
        }

        $shift2 = 0;
        $shift2_num = 0;
        $shift2_sum_seconds = 0;
        $shift2_unit = 'Hrs';
        $this->db->where("HOUR(dateupdated) >= ", 7);
        $this->db->where("HOUR(dateupdated) <= ", 15);
        $qry_shift2_ave_tc = $this->db->select('sysid,datecreated, dateupdated, status')
            ->from('ticketing_details_logs')
            ->where(array('status' => 314))
            ->get();
        if($qry_shift2_ave_tc->num_rows() > 0) {

            foreach($qry_shift2_ave_tc->result() as $s2row) {
                $timeFirst  = strtotime($s2row->datecreated);
                $timeSecond = strtotime($s2row->dateupdated);
                $differenceInSeconds = $timeSecond - $timeFirst;
                $shift2_sum_seconds += $differenceInSeconds;
                $shift2_num++;
            }
            $shift2_ave = (($shift2_sum_seconds/60)/60)/60;
            $shift2 = ($shift2_ave>24) ? number_format($shift2_ave/24, 3) : number_format($shift2_ave, 3);
            $shift2_unit = ($shift2_ave>24) ? 'Days' : 'Hrs';
        }

        $shift3 = 0;
        $shift3_num = 0;
        $shift3_sum_seconds = 0;
        $shift3_unit = 'Hrs';
        $this->db->where("HOUR(dateupdated) >= ", 15);
        $this->db->where("HOUR(dateupdated) <= ", 23);
        $qry_shift3_ave_tc = $this->db->select('sysid,datecreated, dateupdated, status')
            ->from('ticketing_details_logs')
            ->where(array('status' => 314))
            ->get();
        if($qry_shift3_ave_tc->num_rows() > 0) {

            foreach($qry_shift3_ave_tc->result() as $s3row) {
                $timeFirst  = strtotime($s3row->datecreated);
                $timeSecond = strtotime($s3row->dateupdated);
                $differenceInSeconds = $timeSecond - $timeFirst;
                $shift3_sum_seconds += $differenceInSeconds;
                $shift3_num++;
            }
            $shift3_ave = (($shift3_sum_seconds/60)/60)/60;
            $shift3 = ($shift3_ave>24) ? number_format($shift3_ave/  24, 3) : number_format($shift1_ave, 3);
            $shift3_unit = ($shift3_ave>24) ? 'Days' : 'Hrs';
        }

        $data['shift1ave'] = $shift1;
        $data['shift1unit'] = $shift1_unit;
        $data['shift1num'] = $shift1_num;
        $data['shift2ave'] = $shift2;
        $data['shift2unit'] = $shift2_unit;
        $data['shift2num'] = $shift2_num;
        $data['shift3ave'] = $shift3;
        $data['shift3unit'] = $shift3_unit;
        $data['shift3num'] = $shift3_num;
        $data['genave'] = $gen;
        $data['genunit'] = $gen_unit;
        return json_encode($data);
    }

    function team_queue() {
        $data = array();
        $qry = false;
        $tcid = $this->input->post('tcid');
        $teamid = $this->input->post('teamid');
        $types = $this->input->post('types');
        $nums = $this->input->post('queue');

        $this->db->trans_begin();
        $this->db->where(array('tcid' => $tcid, 'teamid' => $teamid, 'status' => 1));
        $this->db->update('ticketing_queue', array('status' => 0, 'updatedby' => user_id()));
        $err[] = $this->db->_error_message();

        $ins_arr = array(
            'tcid' => $tcid,
            'types' => $types,
            'nums' => $nums,
            'teamid' => $teamid,
            'createdby' => user_id(),
            'updatedby' => user_id(),
        );
        $this->db->insert('ticketing_queue', $ins_arr);
        $err[] = $this->db->_error_message();
        if($this->db->trans_status()==true) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $data['err'] = $err;
        }

        $data['qry'] = $qry;
        $data['num'] = $nums;
        return json_encode($data);
    }

    function get_types_text () {
        $data = array();
        $id = $this->input->post('id');
        $text = get_types_label_format($id, false, true, false, false, false, false);
        $data['text'] = $text;
        return json_encode($data);
    }

    function get_server_time() {
        $data = array();
        $data['time'] = sql_time()->TIME12;
        return json_encode($data);
    }


}