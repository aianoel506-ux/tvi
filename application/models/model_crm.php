<?php
/*
 * NOV. 11, 2020
 * CREATED BY: LUCKY JOHN FADERON
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_crm extends CI_Model
{
    function cde_save() {
        $data = array();
        $q = false;

        $ins = true;
        $msg = '';
        $func = 'error';

        $userid = user_id();
        $ins_err = array();
        $ticket_id = 0;

        $concern = $this->input->post('concern');
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


            $maplat = null;
            $maplng = null;

            if($mapurl && $mapurl != '') {
                $map_latlang_arr1 = explode('@', $mapurl);
                $map_latlang_arr2 = explode(',', $map_latlang_arr1[1]);
                $maplat = $map_latlang_arr2[0];
                $maplng = $map_latlang_arr2[1];
            }

            $ticket_ins_arr = array(
                'acctid' => ($acctid) ? $acctid : 0,
                'repsource' => $repsource,
                'complainants' => $complainants,
                'address' => $address,
                'contact' => $contact,
                'remarks' => $remarks,
                'tickettype' => ($concern) ? $concern : 0,
                'priority' => $priority,
                'district' => $district,
                'landmarks' => $landmark_ids,
                'barangays' => $barangay_ids,
                'mapurl' => ($mapurl) ? $mapurl : null,
                'maplat' => $maplat,
                'maplng' => $maplng,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('ticketing_details_logs', $ticket_ins_arr);
            $ticket_id = $this->db->insert_id();
            $ins_err[] = $this->db->_error_message();

            if ($ticket_id > 0) {
                $ticket_trail_arr = array(
                    'ticketid' => $ticket_id,
                    'codes' => 'CDNEW',
                    'descs' => 'CD - data creation',
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

                        $upload_path = './uploads/attachments/crm/';

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

                        $this->upload->initialize(upload_options($full_path, $new_name));

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
        $data['title'] = 'New Customer Data';
        return json_encode($data);
    }


    function get_ticket_list() {
        $data = array();
        $int = $this->input->post('int');
        $status = $this->input->post('status');


        if($status && $status > 0) {
            $this->db->where('tdl.status', $status);
        }
        $qry = $this->db->select('
            tdl.sysid, 
            tp.names AS cdname, 
            tpr.descs AS particular, 
            tdl.repsource,
            tdl.complainants,
            tdl.remarks, 
            tdl.tickettype, 
            tdl.createdby, 
            tdl.updatedby, 
            tdl.datecreated, 
            tdl.dateupdated, 
            tdl.status,
            tdl.reqverification,
            tdl.contact,
            tdl.address,
            tdl.district,
            tdl.barangays,
            ab.texts AS brgyname,
            tdl.landmarks,
            tdl.compname,
            tdl.etc,
            p.firstname,
            p.middlename,
            p.lastname
        ')
            ->from('ticketing_details_logs AS tdl')
            ->join('ticketing_details_trails AS tdt', 'tdt.ticketid = tdl.sysid AND tdt.status = 1', 'left')
            ->join('person AS p', 'p.sysid = tdl.complainants', 'left')
            ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype', 'left')
            ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart', 'left')
            ->join('address_barangay AS ab', 'ab.sysid = tdl.barangays', 'left')
            ->join('customer_accounts_main AS am', 'am.sysid = tdl.acctid', 'left')
            ->join('customer_accounts_address AS a', 'a.acctid = am.sysid', 'left')
            ->where(array('tdl.status > ' => 0))
            ->group_by('
            tdl.sysid, 
            tp.names, 
            tpr.descs, 
            tdl.repsource,
            tdl.complainants,
            tdl.remarks, 
            tdl.tickettype, 
            tdl.createdby, 
            tdl.updatedby,             tdl.datecreated, 
            tdl.dateupdated, 
            tdl.status,
            tdl.reqverification,
            tdl.contact,
            tdl.address,
            tdl.district,
            tdl.barangays,
            ab.texts,
            tdl.landmarks,
            tdl.compname,
            tdl.etc,
            p.firstname,
            p.middlename,
            p.lastname')
            ->get();


        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {


                if($row->complainants>0) {
                    $name = $row->lastname . ', '.$row->firstname . ' ' . $row->middlename;
                }else{
                    $name = $row->compname;
                }
                $time = $row->datecreated . '<br><small class="text-info">' . timeago($row->datecreated, sql_time()->DATETIME).'</small>';



                $status = get_types_label_format($row->status);
                $cdname = get_types_label_format($row->tickettype);
                $remarks = 'N/A';

                $html_control = '';
                $html_control .= '<a href="'.base_url('module/eb4ac3033e8ab3591e0fcefa8c26ce3fd36d5a0f/view/'.$row->sysid).'" class="btn btn-info btn-xs inline"><i class="fa fa-search"></i> View</a></a>';
                $html_control .= '<a href="javascript:;" class="btn btn-success btn-xs inline"><i class="fa fa-send"></i> Send</a></a>';

                $html_address = '';
                $html_address .= $row->brgyname.', '.$row->address;
                $html_address .= '<a href="" id="addr_map" class="btn btn-default inline btn-xs pull-right"><i class="fa fa-map-marker"></i> Map</a>';


                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'num' => str_pad($row->sysid, 8, '0', STR_PAD_LEFT),
                    'cdname' => $cdname,
                    'info' => $name . '<br>' . $row->contact,
                    'address' => $html_address,
                    'timelapse' => $time,
                    'remarks' => $remarks,
                    'status' => $status,
                    'control' => $html_control,
                );
            }
        }

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


            $location 	= './uploads/attachments/crm';
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
                $html .= '<button type="button" title="'.$name.'" style="position: absolute; top: 5px; right: 10px;" href="#tc_map_lookup" data-toggle="ajax-modal-map" data-id="'.$qry->sysid.'" class="btn btn-default btn-xs pull-right"><i class="fa fa-map-marker"></i> Map</button>';
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


}