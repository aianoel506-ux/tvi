<?php
/**
 * Created by PhpStorm.
 * User: DUDEZKIE
 * Date: 6/19/2019
 * Time: 3:34 PM
 */

class Model_jo extends CI_Model
{
    function get_jo_details() {
        $data = array();
        return json_encode($data);
    }



    function select2_joborders() {
        $data = array();
        $query = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'JO'))
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

    function select2_jo_status() {
        $data = array();
        $query = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg')
            ->from('prime_types_parameter AS tp')
            ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
            ->where('tssm.codes', 'JO')
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

    function get_joborder_list() {

        $data = array();
        $jo_arr = array();

        $status = $this->input->post('status');
        $types = $this->input->post('types');
        $comp = $this->input->post('complaints');
        $int = $this->input->post('int');


        $search = $this->input->post('searching');
        $sname = $this->input->post('sname');
        $saddr = $this->input->post('saddr');

        $datefilter = $this->input->post('datefilter');
        $filteryear = $this->input->post('filteryear');
        $filtermonth = $this->input->post('filtermonth');
        $filterday = $this->input->post('filterday');
        $view = $this->input->post('view');

        // #######################################################################
        // FILTER TYPES
        if($types != 0) {
            $this->db->where('jdl.tickettype', $types);
            $jo_arr = $types;
        }else {

            // #######################################################################
            // FILTER TICKET TYPES FOR JOBORDERS ONLY
            $query_ts = $this->db->select('sysid')
                ->from('prime_types_parameter')
                ->where(array('codes' => $comp, 'status' => 1))
                ->get();
            if ($query_ts->num_rows() > 0) {
                foreach ($query_ts->result() as $tsrow) {
                    $jo_arr[] = $tsrow->sysid;
                }
            }
            $this->db->where_in('jdl.tickettype', $jo_arr);
            // #######################################################################
        }




        // #######################################################################
        // FILTER STATUS
        if($status && $status > 0) {
            $this->db->where('jdl.status', $status);
        }


        // #######################################################################
        // FILTER DATE
        if($datefilter == 1) {
            if($filteryear && $filteryear > 0) {
                $this->db->where('YEAR(jdl.datecreated) = ', $filteryear);
                if($filtermonth && $filtermonth > 0) {
                    $this->db->where('MONTH(jdl.datecreated) = ', $filtermonth);
                    if($filterday && $filterday > 0) {
                        $this->db->where('DAY(jdl.datecreated) = ', $filterday);
                    }
                }
            }
        }
        // #######################################################################

        $data['TYPES'] = $jo_arr;

        if($view==2) {
            $this->db->where('jdl.status != ', 314);
        }

        $qry = $this->db->select('
            jdl.sysid, 
            tp.names AS tcname, 
            jdl.acctid,
            jdl.repsource,
            jdl.complainants,
            jdl.remarks, 
            jdl.tickettype, 
            jdl.createdby, 
            jdl.updatedby, 
            jdl.datecreated, 
            jdl.dateupdated, 
            jdl.status,
            jdl.reqverification,
            jdl.contact,
            jdl.address,
            jdl.district,
            jdl.barangays,
            ab.texts AS brgyname,
            jdl.landmarks,
            jdl.compname,
            jdl.etc,
            jdl.status,
            p.firstname,
            p.middlename,
            p.lastname
        ')
            ->from('joborders_details_logs AS jdl')
            ->join('person AS p', 'p.sysid = jdl.complainants', 'left')
            ->join('prime_types_parameter AS tp', 'tp.sysid = jdl.tickettype', 'left')
            ->join('address_barangay AS ab', 'ab.sysid = jdl.barangays', 'left')
            ->where(array('jdl.status > ' => 0))
            ->get();

        $data['custs_qry'] = $this->db->last_query();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                if($row->complainants > 0) {
                    $name = $row->lastname . ', '.$row->firstname . ' ' . $row->middlename;
                }else{
                    $name = $row->compname;
                }

                if ($row->tickettype != 322) {
                    $acct_info = $this->db->select('
                        am.types,
                        am.servicenumber AS servno,
                        am.mtr,
                        am.ownerid,
                        am.mtrno,
                        am.mtrserial,
                        a.addrspecific AS addr
                    ')
                        ->from('customer_accounts_main AS am')
                        ->join('customer_accounts_address AS a', 'a.acctid = am.sysid', 'left')
                        ->where('am.sysid',$row->acctid)
                        ->get()->row();
                } else {
                    $acct_info = (object)array(
                        'types' => 1,
                        'servno' => '',
                        'mtr' => '',
                        'ownerid' => '',
                        'mtrno' => '',
                        'mtrserial' => '',
                        'addr' => get_application_details($row->acctid)->info->addrspec,
                    );
                }

                $am_owner_type = $acct_info->types;
                $am_owner_ownerid = $acct_info->ownerid;

                $acct_name = '';
                if ($am_owner_type == 5) {
                    $qry_person = $this->db->select()
                        ->from('customer_accounts_name_legacy')
                        ->where(array('sysid' => $am_owner_ownerid))
                        ->get()->row();
                    $acct_name = $qry_person->name;
                } else {
                    $acct_name = $name;
                }


                $popcontent = '';
                $popcontent .= '</div style=\'width: 500px; display: inline-block;\'>';
                $popcontent .= '<h3>Testing Content</h3>';
                $popcontent .= '</div>';



                $moduleid_arr = get_job_order_moduleid($row->tickettype);
                //print_r($moduleid_arr);
                $moduleid = $moduleid_arr->moduleid;
                $trn_codes = $moduleid_arr->codes;
                $flowid = $moduleid_arr->flowid;

                /*
                $qry_trails_last = $this->db->query("
                    SELECT 
                    rmt.sysid, 
                    rmt.datecreated, 
                    rmt.stageid, 
                    rmt.dataid, 
                    rmt.datecreated AS logdate, 
                    fms.desc
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    LEFT JOIN prime_transaction_flow_main_stages AS fms ON fms.sysid = rmt.stageid
                    WHERE rmt.dataid = {$row->sysid} AND rm.flowid = $flowid
                    ORDER BY rmt.datecreated DESC
                ")->row();
                */

                $qry_trails_last = $this->db->select('
                    rmt.sysid, 
                    rmt.datecreated, 
                    rmt.stageid, 
                    rmt.dataid, 
                    rmt.datecreated AS logdate, 
                    fms.desc
                ')
                    ->from('transaction_request_main_trails AS rmt')
                    ->join('transaction_request_main AS rm','rm.sysid = rmt.trnid')
                    ->join('prime_transaction_flow_main_stages AS fms','fms.sysid = rmt.stageid','left')
                    ->where(array('rmt.dataid' => $row->sysid , 'rm.flowid' => $flowid))->get()->row();

                $data['qry_trails_last'][] = $this->db->last_query();
                //print_r($this->db->last_query().'<br>');
                //print_r($qry_trails_last);
                $accomplished = false;
                if ($qry_trails_last) {
                    $check_trn_disapproved = $this->db->select()
                        ->from('transaction_request_trails_logs')
                        ->where(array('trailid' => $qry_trails_last->sysid , 'activity' => 88))
                        ->get()->row();
                } else {
                    $check_trn_disapproved = false;
                }

                if($check_trn_disapproved) {
                    $status = get_types_label_format(88);
                }else {
                    $status = get_types_label_format($row->status);
                }



                $transaction_desc = '';
                $transaction_date = $row->dateupdated;
                if($qry_trails_last) {
                    $transaction_desc = $qry_trails_last->desc;
                    $transaction_date = $qry_trails_last->datecreated;
                }

                $input_mtrno = '';
                $input_serial = '';
                $input_rdg = '';

                $acctid = '<input type="hidden" name="acctid" id="acctid"/>';

                if ($row->status == 300){
                    $input_mtrno = '';
                    $input_mtrno .= '<div style="width: 200px;" class="input-group input-group-sm"><span class="input-group-addon">N</span><input type="text" class="form-control input_mtrno_' . $row->sysid . '" id="input_mtrno" placeholder="0000.." /><span class="input-group-btn"><a title="Search Meter" data-arr="' . $row->sysid . '" href="#tbl_utility_meterlist" data-toggle="ajax-modal" class="btn btn-default"><i class="fa fa-search"></i></a></span></div>';

                    $input_serial = '';
                    $input_serial .= '<div style="width: 200px;" class="input-group input-group-sm"><span class="input-group-addon">S</span><input type="text" readonly class="form-control input_serial_' . $row->sysid . '" id="input_serial" placeholder="0000.." /></div>';

                    $input_rdg = '';
                    $input_rdg .= '<div style="width: 200px;" class="input-group input-group-sm"><span class="input-group-addon">R</span><input type="text" class="form-control input_rdg_' . $row->sysid . '" id="input_rdg" placeholder="0000.." /></div>';

                }else{
                    if ($row->status == 314){
                        $input_mtrno = '';
                        $input_serial = '';
                        $input_rdg = '';
                    }
                }

                if($view == 2) {


                    $joborder = '';
                    $joborder .= str_pad($row->sysid, 6, '0', STR_PAD_LEFT);


                    $control = '';

                    $mtrno = '<code>'.$acct_info->mtrno .'</code>';
                    $serial = '<code>'.$acct_info->mtrserial.'</code>';
                    $mtrno_new = '';
                    $serial_new = '';
                    $check_stat = '';
                    $issued = false;
                    $init_rdg = '';

                    $checked = '';
                    if($row->status == 3205 || $row->status == 3211) {
                        $issued = true;
                        $qry_check_ownership = $this->db->select('am.sysid, am.labels, am.serials, moh.dateissued')
                            ->from('assets_main_owner_history AS moh')
                            ->join('assets_main AS am', 'am.sysid = moh.assetid', 'left')
                            ->where(array('moh.ownerid' => $row->acctid, 'moh.status' => 300))
                            ->order_by('moh.datecreated', 'desc')
                            ->get()->row();


                        if ($qry_check_ownership) {
                            $mtrno_new = '';
                            $mtrno_new .= 'N: <code>'.$qry_check_ownership->labels.'</code><br>';
                            $mtrno_new .= 'S: <code>'.$qry_check_ownership->serials.'</code>';
                            $assetid = $qry_check_ownership->sysid;
                            $control .= '<button id="btn_reissue" style="margin-bottom: 3px;" data-id="' . $assetid . '" data-joid="' . $row->sysid . '" class="btn btn-warning btn-xs inline"><i class="fa fa-refresh"></i> Revert</button>';

                            if($row->tickettype == 3092) {
                                $input_mtrno = '<code>N/A</code>';
                                $input_serial = '<code>N/A</code>';
                            }else {

                                $input_serial = $serial_new;
                            }
                            $input_datecomp = $qry_check_ownership->dateissued;
                            $checked = '<span class="label label-success"><i class="fa fa-check"></i></span>';

                        }
                    }else{
                        $control .= '<button id="btn_cancel" style="margin-bottom: 3px;" data-joid="' . $row->sysid . '" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i> Cancel</button>';
                        if($row->tickettype == 3092) {
                            $input_mtrno = '<code>N/A</code>';
                            $input_serial = '<code>N/A</code>';
                        } else {
                            $input_mtrno = '';
                            $input_mtrno .= '<div style="width: 200px;" class="input-group input-group-sm"><span class="input-group-addon">N</span><input type="text" class="form-control input_mtrno_' . $row->sysid . '" id="input_mtrno" placeholder="0000.." /><span class="input-group-btn"><a title="Search Meter" data-arr="' . $row->sysid . '" href="#tbl_utility_meterlist" data-toggle="ajax-modal" class="btn btn-default"><i class="fa fa-search"></i></a></span></div>';
                            $input_mtrno .= '<div style="width: 200px;" class="input-group input-group-sm"><span class="input-group-addon">S</span><input type="text" readonly class="form-control" id="input_serial" placeholder="0000.." /></div>';
                        }
                        $input_datecomp = '<input data-joid="'.$row->sysid.'" type="date" class="form-control " id="input_datecomp" placeholder="0000.." />';
                    }


                    $acct_details = '';
                    $acct_details .= '<div class="row">';
                    $acct_details .= '<div class="col-md-5">';
                    $acct_details .= '<h4 class="bold" style="margin: 0px 0px;">' . $acct_info->servno . '</h4>';
                    $acct_details .= 'N: <span id="mtrno">' . $mtrno . '</span><br>';
                    $acct_details .= 'S: <span id="mtrserial">' . $serial . '</span><br>';
                    $acct_details .= '</div>';

                    $acct_details .= '<div class="col-md-7">';
                    $acct_details .= $acct_name . '<br>' . $acct_info->addr;
                    $acct_details .= '</div>';
                    $acct_details .= '</div>';



                    $data['list'][] = array(
                        'checkbox' => $checked,
                        'expand' => btn_expand($row->acctid),
                        'type' => get_types_label_format($row->tickettype, false, false, 'left', false, false, false) ,
                        'joborder' => $joborder,
                        'requester' => $name . '<br>' . $row->contact,
                        'acctdetails' => $acct_details,
                        'mtrno' => $input_mtrno,
                        'serial' => $input_serial,
                        'reading' => $input_rdg,
                        'datecomply' => $input_datecomp,
                        'datecreated' => $row->datecreated,
                        'dateupdated' => $transaction_date,
                        'transaction' => $transaction_desc,
                        'status' => $status,
                        'control' => $control,
                        'issued' => $issued,
                        'acctid' => $acctid
                    );
                }else {


                    $acct_details = '';
                    $acct_details .= '<div class="row">';
                    $acct_details .= '<div class="col-md-4">';
                    $acct_details .= '<h4 class="bold" style="margin: 0px 0px;">' . $acct_info->servno . '</h4>';
                    $acct_details .= 'MTR No.: <span id="mtrno">' . $acct_info->mtrno . '</span><br>';
                    $acct_details .= 'Serial: <span id="mtrserial">' . $acct_info->mtrserial . '</span><br>';
                    $acct_details .= '</div>';

                    $acct_details .= '<div class="col-md-8">';
                    $acct_details .= $acct_name . '<br>' . $acct_info->addr;
                    $acct_details .= '</div>';
                    $acct_details .= '</div>';


                    $button = '<div class="btn-group btn-xs">' . btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid) . btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'task') . '</div>';

                    $control = '';
                    $control .= $button;

                    $data['list'][] = array(
                        'expand' => btn_expand($row->acctid),
                        'num' => str_pad($row->sysid, 6, '0', STR_PAD_LEFT),
                        'joborder' => get_types_label_format($row->tickettype, false, false, 'left', false, false, false),
                        'requester' => $name . '<br>' . $row->contact,
                        'acctdetails' => $acct_details,
                        'address' => '',
                        'mtrno' => $input_mtrno,
                        'serial' => $input_serial,
                        'reading' => $input_rdg,
                        'datecreated' => $row->datecreated,
                        'dateupdated' => $transaction_date,
                        'transaction' => $transaction_desc,
                        'status' => $status,
                        'control' => $control,
                        'acctid' => $acctid
                    );
                }
            }
        }

        return json_encode($data);
    }


    function save_new_joborder() {
        $data = array();
        $q = false;
        $ins = true;
        $msg = '';
        $userid = user_id();
        $func = 'error';
        $ins_err = array();
        $title = 'New Job Order';

        $joborders = $this->input->post('joborder');

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
        $appid = $this->input->post('appid');
        $personid= $this->input->post('personid');



        $moduleid_arr = get_job_order_moduleid($joborders);
        $moduleid = $moduleid_arr->moduleid;
        $trn_codes = $moduleid_arr->codes;

        $this->db->trans_begin();

        $trn_desc = '';
        if($joborders == 322) {

            $cad_info = get_application_details($appid)->info;
            if ($cad_info) {
                $complainants = $cad_info->personid;
                $trn_desc = $cad_info->lastname . ', ' . $cad_info->firstname;
                $remarks = 'New Application';
            }

            $exist_qry = $this->db->select('sysid')->from('joborders_details_logs')
                ->where(array('acctid' => $appid , 'status' => 300))
                ->get();

            if ($exist_qry->num_rows() > 0) {
                foreach ($exist_qry->result() AS $exist) {
                    $this->db->where('sysid',$exist->sysid);
                    $this->db->update('joborders_details_logs',array('status' => 303));
                }
            }

            $ticket_ins_arr = array(
                'acctid' => ($appid) ? $appid : 0,
                'repsource' => $repsource,
                'complainants' => ($cad_info->personid) ? $complainants : $personid,
                'address' => $address,
                'contact' => $contact,
                'remarks' => $remarks,
                'tickettype' => ($joborders) ? $joborders : 0,
                'priority' => 1,
                'district' => $district,
                'barangays' => $barangay,
                'mapurl' => ($mapurl) ? $mapurl : null,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );

            $this->db->insert('joborders_details_logs', $ticket_ins_arr);
            $ticket_id = $this->db->insert_id();
            $ins_err[] = $this->db->_error_message();

            if ($ticket_id > 0) {
                $jobtype = get_types_name($joborders)->desc;
                $ticket_trail_arr = array(
                    'joid' => $ticket_id,
                    'codes' => 'JONEW',
                    'descs' => 'JO - '.$jobtype.': '.$trn_desc,
                    'createdby' => user_id()
                );
                $this->db->insert('joborders_details_trails', $ticket_trail_arr);
                $ins_err[] = $this->db->_error_message();
            }

            $err_msg = '';
            if (count($ins_err) > 0) {
                $err_msg = implode(', ', $ins_err);
            }

            if ($this->db->trans_status() === TRUE) {

                $trn = create_transaction_trails($trn_codes, $trn_desc, $moduleid, $ticket_id);
                if ($trn) {
                    $this->db->trans_commit();
                    $msg = 'New Joborder Created!';
                    $func = 'success';
                    $q = true;
                } else {
                    $this->db->trans_rollback();
                    $msg = 'Error: Transaction trail!';
                    $func = 'warning';
                    $q = false;
                }
            } else {
                $this->db->trans_rollback();
                $msg = $err_msg;
                $func = 'warning';
            }
        } else {
            $qry_check = $this->db->query("SELECT sysid, acctid FROM joborders_details_logs WHERE acctid = $acctid AND status != 314")->row();
            if ($qry_check == false) {
                $qry_check_person = $this->db->select('sysid, lastname, firstname')->from('person')
                    ->where(array('lastname' => $lastname, 'firstname' => $firstname))
                    ->get()->row();
                if ($qry_check_person) {
                    $complainants = $qry_check_person->sysid;
                    $trn_desc = $qry_check_person->lastname . ', ' . $qry_check_person->firstname;
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


                    $trn_desc = $new_lastname . ', ' . $new_firstname;
                }


                $ticket_ins_arr = array(
                    'acctid' => ($acctid) ? $acctid : 0,
                    'repsource' => $repsource,
                    'complainants' => $complainants,
                    'address' => $address,
                    'contact' => $contact,
                    'remarks' => $remarks,
                    'tickettype' => ($joborders) ? $joborders : 0,
                    'priority' => $priority,
                    'district' => $district,
                    'mapurl' => ($mapurl) ? $mapurl : null,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('joborders_details_logs', $ticket_ins_arr);
                $ticket_id = $this->db->insert_id();
                $ins_err[] = $this->db->_error_message();

                if ($ticket_id > 0) {
                    $ticket_trail_arr = array(
                        'joid' => $ticket_id,
                        'codes' => 'JONEW',
                        'descs' => 'JO - data creation',
                        'createdby' => user_id()
                    );
                    $this->db->insert('joborders_details_trails', $ticket_trail_arr);
                    $ins_err[] = $this->db->_error_message();
                }


                $err_msg = '';
                if (count($ins_err) > 0) {
                    $err_msg = implode(', ', $ins_err);
                }


                $files = $_FILES;
                if ($files) {
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

                    $trn = create_transaction_trails($trn_codes, $trn_desc, $moduleid, $ticket_id);
                    if ($trn) {
                        $this->db->trans_commit();
                        $msg = 'New Joborder Created!';
                        $func = 'success';
                        $q = true;
                    } else {
                        $this->db->trans_rollback();
                        $msg = 'Error: Transaction trail!';
                        $func = 'warning';
                        $q = false;
                    }
                } else {
                    $this->db->trans_rollback();
                    $msg = $err_msg;
                    $func = 'warning';
                }

            } else {
                $title = 'Existing Job Order(s)';
                $func = 'info';
                $msg = 'There is an existing Job order (' . str_pad($qry_check->sysid, 6, '0', STR_PAD_LEFT) . ') under to this account, please accomplish or cancel the existing before creating a new one.';
            }
        }


        $data['input'] = $this->input->post();
        $data['qry'] = $q;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = $title;
        return json_encode($data);
    }

    function get_joborder_trn_trail(){

        $data = array();
        $trnid  = $this->input->post('trnid');
        // transaction_request_main_trails
        // prime_transaction_flow_main_stages

        $sql = $this->db->select("trmt.stageid,trmt.datecreated, trmt.dateupdated , trmt.createdby , trmt.updatedby, ptfms.desc ")
            ->from("transaction_request_main_trails as trmt")
            ->join("prime_transaction_flow_main_stages as ptfms" , "ptfms.sysid = trmt.stageid")
            ->where(array("trmt.trnid" => $trnid))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $user_info = get_users_info($row->createdby);
                $createdby = ($user_info) ? $user_info->lastname : '';
                $data['jotraildata'][] = array(
                    'num' => $num++,
                    'desc' => $row->desc,
                    'datecreated' => $row->datecreated,
                    'dateupdated' => $row->dateupdated,
                    'createdby' =>  $createdby,
                    'updatedby' => $row->updatedby
                );
            }
        }

        return json_encode($data);
    }

    function sumbit_meter_reissue_row() {
        $data = array();
        $joid = $this->input->post('joid');
        $assetid = $this->input->post('assetid');
        $qry = false;
        $func = 'error';
        $msg = '';

        if(user_id() > 0) {
            $userid = user_id();
            $qry_jo = $this->db->select()
                ->from('joborders_details_logs')
                ->where(array('sysid' => $joid))
                ->get()->row();

            if($qry_jo) {
                $acctid = $qry_jo->acctid;

                $moduleid_arr = get_job_order_moduleid($qry_jo->tickettype);
                $moduleid = $moduleid_arr->moduleid;
                $trn_codes = $moduleid_arr->codes;
                $flowid = $moduleid_arr->flowid;


                $qry_trails_last = $this->db->query("
                        SELECT 
                        rmt.sysid, 
                        rmt.datecreated, 
                        rmt.stageid, 
                        rmt.dataid, 
                        rmt.datecreated AS logdate, 
                        fms.desc
                        FROM transaction_request_main_trails AS rmt
                        INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                        LEFT JOIN prime_transaction_flow_main_stages AS fms ON fms.sysid = rmt.stageid
                        WHERE rmt.dataid = {$joid} AND rm.flowid = $flowid
                        ORDER BY rmt.datecreated DESC
                    ")->row();

                if ($qry_trails_last) {

                    $trailid = $qry_trails_last->sysid;


                    $this->db->trans_begin();

                    $this->db->query("UPDATE assets_main SET status = 3202 WHERE sysid = $assetid");

                    $this->db->query("UPDATE assets_main_owner_history SET status = 0, updatedby = $userid WHERE assetid = $assetid AND ownerid = $acctid AND ownertype = 3");
                    $data['msg_err']['assesthist'] = $this->db->_error_message();

                    $asset_remarks = array(
                        'assetid' => $assetid,
                        'typesid' => 300,
                        'remarks' => 'Revert',
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('assets_remarks', $asset_remarks);

                    $job_trail = array(
                        'joid' => $joid,
                        'codes' => 'CANCEL',
                        'descs' => 'Asset revert',
                        'trailid' => $trailid,
                        'statusid' => 200,
                        'createdby' => user_id(),
                    );
                    $this->db->insert('joborders_details_trails', $job_trail);
                    $data['msg_err']['jotrails'] = $this->db->_error_message();


                    $this->db->query("UPDATE joborders_details_logs SET status = 300, updatedby = $userid WHERE sysid = $joid");


                    if ($this->db->trans_status() == true) {
                        $this->db->trans_commit();
                        $msg = 'Assignment Success!, please send the transaction to utility accomplishments!';
                        $func = 'success';
                        $qry = true;
                    } else {
                        $this->db->trans_rollback();
                        $msg = 'Error: query';
                        $func = 'warning';
                    }
                } else {
                    $msg = 'Trail details not found!';
                    $func = 'warning';
                }
            }
        }else{
            $msg = 'Session timeout!';
        }

        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }



    function submit_issuance_temp_row() {
        $data = array();
        $joid = $this->input->post('joid');
        $mtrno = $this->input->post('mtrno');
        $serial = $this->input->post('serial');
        $date = $this->input->post('dateaccomp');

        $qry = false;
        $func = 'error';
        $msg = '';
        $assetid = 0;

        if(user_id() > 0) {
            $userid = user_id();
            $qry_jo = $this->db->select()
                ->from('joborders_details_logs')
                ->where(array('sysid' => $joid))
                ->get()->row();

            if($qry_jo) {
                $qry_asset = $this->db->select()->from('assets_main')
                    ->where(array('labels' => $mtrno, 'serials' => $serial))
                    ->get()->row();
                if($qry_asset) {
                    $assetid = $qry_asset->sysid;

                    $acctid = $qry_jo->acctid;

                    $moduleid_arr = get_job_order_moduleid($qry_jo->tickettype);
                    $moduleid = $moduleid_arr->moduleid;
                    $trn_codes = $moduleid_arr->codes;
                    $flowid = $moduleid_arr->flowid;


                    $qry_trails_last = $this->db->query("
                            SELECT 
                            rmt.sysid, 
                            rmt.datecreated, 
                            rmt.stageid, 
                            rmt.dataid, 
                            rmt.datecreated AS logdate, 
                            fms.desc
                            FROM transaction_request_main_trails AS rmt
                            INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                            LEFT JOIN prime_transaction_flow_main_stages AS fms ON fms.sysid = rmt.stageid
                            WHERE rmt.dataid = {$joid} AND rm.flowid = $flowid
                            ORDER BY rmt.datecreated DESC
                        ")->row();

                    if ($qry_trails_last) {

                        $trailid = $qry_trails_last->sysid;


                        $this->db->trans_begin();

                        $data['tickettype'] = $qry_jo->tickettype;

                        $this->db->query("UPDATE assets_main SET status = 3205 WHERE sysid = $assetid");

                        $this->db->query("UPDATE assets_main_owner_history SET status = 0, updatedby = $userid WHERE assetid = $assetid AND ownerid = $acctid AND ownertype = 3");
                        $data['msg_err']['assesthist'] = $this->db->_error_message();

                        $asset_owner_ins = array(
                            'assetid' => $assetid,
                            'ownerid' => $acctid,
                            'dateissued' => $date,
                            'ownertype' => 3,
                            'status' => 300,
                            'createdby' => user_id()
                        );
                        $this->db->insert('assets_main_owner_history', $asset_owner_ins);


                        $asset_remarks = array(
                            'assetid' => $assetid,
                            'typesid' => 3205,
                            'remarks' => 'Temporary Issuance',
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('assets_remarks', $asset_remarks);

                        $job_trail = array(
                            'joid' => $joid,
                            'codes' => 'ISSUED',
                            'descs' => 'Asset Temporary Issuance',
                            'trailid' => $trailid,
                            'statusid' => 3205,
                            'createdby' => user_id(),
                        );
                        $this->db->insert('joborders_details_trails', $job_trail);
                        $data['msg_err']['jotrails'] = $this->db->_error_message();


                        if($qry_jo->tickettype == 3092) {
                            $this->db->query("UPDATE joborders_details_logs SET status = 3211, updatedby = $userid WHERE sysid = $joid");
                        }else {
                            $this->db->query("UPDATE joborders_details_logs SET status = 3205, updatedby = $userid WHERE sysid = $joid");
                        }


                        if ($this->db->trans_status() == true) {
                            $this->db->trans_commit();
                            $msg = 'Assignment Success!, please send the transaction to utility accomplishments!';
                            $func = 'success';
                            $qry = true;
                        } else {
                            $this->db->trans_rollback();
                            $msg = 'Error: query';
                            $func = 'warning';
                        }
                    } else {
                        $msg = 'Trail details not found!';
                        $func = 'warning';
                    }
                }else{
                    $msg = 'Asset not found!';
                    $func = 'warning';
                }
            }
        }else{
            $msg = 'Session timeout!';
        }

        $data['joid'] = $joid;
        $data['assetid'] = $assetid;

        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }



    function submit_meter_assignment() {
        $data = array();

        $qry = false;
        $userid = user_id();

        $trailid        = $this->input->post('trailid');
        $acctid         = $this->input->post('acctid');
        $assetid        = $this->input->post('assetid');
        $label          = $this->input->post('label');
        $remarks        = $this->input->post('remarks');
        $joid           = $this->input->post('joid');
        $dateconn       = $this->input->post('date');
        $old_reading    = $this->input->post('oldreading');
        $empid          = $this->input->post('empid');

        $date_char = date_formating($dateconn, 'Y-m-d', 'm/d/Y');

        if ($acctid == 0) {
            $applicant = get_application_details($joid)->info;
            if ($applicant) {

            }
        }

        if(user_id() > 0) {


            $info = get_joborder_info($joid);
            $servno = $info->servicenumber;
            $mtr = $info->mtr;
            $qry_check_ownership = $this->db->select('assetid, ownertype, ownerid')
                ->from('assets_main_owner_history')
                ->where(array('assetid' => $assetid, 'status' => 1))
                ->get()->row();

            if ($qry_check_ownership) {
                $owner_name = '';
                if ($qry_check_ownership->ownertype == 3) {
                    $qry_owner = $this->db->select('servicenumber')
                        ->from('customer_accounts_main')
                        ->where(array('sysid' => $qry_check_ownership->ownerid))
                        ->get()->row();
                    if ($qry_owner) {
                        $owner_name = ' - ' . $qry_owner->servicenumber;
                    }
                }

                $job_trail = array(
                    'joid' => $joid,
                    'codes' => 'ATTEMPT',
                    'descs' => 'Attempt to issue the issued meter',
                    'trailid' => $trailid,
                    'statusid' => 309,
                    'createdby' => user_id(),
                    'remarks' => $remarks
                );
                $this->db->insert('joborders_details_trails', $job_trail);
                $data['msg_err']['jotrails'] = $this->db->_error_message();

                $func = 'error';
                $msg = 'This asset is already been issued to ' . $owner_name;
                $qry = false;
            } else {

                $this->db->trans_begin();

                $this->db->query("UPDATE assets_main SET status = 1, updatedby = $userid WHERE sysid = $assetid");
                $data['msg_err']['updateassetmain'] = $this->db->_error_message();

                $meter_info = get_meter_info($assetid);
                if($meter_info->qry == true) {
                    /*
                       'id' => $row->sysid,
                       'label' => $row->labels,
                       'serial' => $row->serials,
                       'status' => $status,
                       'type' => $type,
                       'brand' => $brand,
                       'volts' => $volts,
                       'ampere' => $amps,
                       'pecoseal' => $pecoseal,
                       'ercseal' => $ercseal,
                       'reading' => $reading,
                       'wiresize' => $wiresize,
                       'kh' => $kh,
                       'pics' => $pic,
                   */

                    // #################################################################################
                    // #################################################################################
                    // UPDATE CUSTOMER ACCOUNT
                    $this->db->query("UPDATE customer_accounts_main 
                      SET 
                      mtrno = {$meter_info->label}, 
                      mtrserial = '{$meter_info->serial}',
                      dateconnected = '$dateconn',
                      mtrassetid = $assetid,
                      status = 1
                      WHERE sysid = $acctid");
                    $err_msg[]['updacctmain'] = $this->db->_error_message();
                    // #################################################################################
                    // #################################################################################

                    $updatearrjologs = array(
                        'status' => 314,
                        'updatedby' => user_id()
                    );
                    $this->db->where(array("sysid" => $joid));
                    $this->db->update("joborders_details_logs" , $updatearrjologs);


                    $owner_history_ins = array(
                        'assetid' => $assetid,
                        'ownerid' => $acctid,
                        'ownertype' => 3,
                        'dateissued' => date('Y-m-d'),
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                        'status' => 1,
                    );
                    $this->db->insert('assets_main_owner_history', $owner_history_ins);
                    $data['msg_err']['assesthist'] = $this->db->_error_message();

                    $asset_remarks = array(
                        'assetid' => $assetid,
                        'typesid' => 3205,
                        'remarks' => $remarks,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('assets_remarks', $asset_remarks);
                    $data['msg_err']['assetrem'] = $this->db->_error_message();

                    $job_trail = array(
                        'joid' => $joid,
                        'codes' => 'ISSUED',
                        'descs' => 'Asset issuance: ' . $label,
                        'trailid' => $trailid,
                        'statusid' => 1,
                        'createdby' => user_id(),
                        'remarks' => $remarks,
                        'dateaccomp' => $dateconn,
                        'accompby' => $empid
                    );
                    $this->db->insert('joborders_details_trails', $job_trail);
                    $data['msg_err']['jotrails'] = $this->db->_error_message();


                    // #################################################################################
                    // #################################################################################
                    // UPDATE CUSTOMER ACCOUNT PECO APPS

                    $conn_dev = $this->load->database('pecoapps', TRUE);
                    $conn_dev->initialize();
                    $conn_dev->trans_begin();
                    $qry_father = $conn_dev->query("
                        UPDATE father
                            SET
                            mtrser____ = '{$meter_info->label}',
                            serial____ = '{$meter_info->serial}',
                            oldrdg____ = '$old_reading',
                            nirdg_____ = '{$meter_info->reading}',
                            stadte____ = '$date_char',
                            pmtrsl____ = '{$info->mtrno}',
                            status____ = 1
                            WHERE servno____ = '$servno' AND mtr_______ = $mtr
                    ");


                    // #################################################################################
                    // #################################################################################
                    // UPDATE CUSTOMER ACCOUNT
                    // INSERT INITIAL READING
                    if($meter_info->reading > 0) {
                        $reading_old_ins_arr = array(
                            'types' => 3103,
                            'acctid' => $acctid,
                            'mtrid' => $meter_info->label,
                            'reading' => $meter_info->reading,
                            'schedid' => 0,
                            'createdby' => $userid,
                            'status' => 4,
                        );
                        $this->db->insert('customer_accounts_subscription_meter_reading', $reading_old_ins_arr);
                        $err_msg[]['insertreading'] = $this->db->_error_message();
                    }

                    // INSERT OLD READING
                    if($old_reading > 0) {
                        $reading_old_ins_arr = array(
                            'types' => 3102,
                            'acctid' => $acctid,
                            'mtrid' => $meter_info->label,
                            'reading' => $old_reading,
                            'schedid' => 0,
                            'createdby' => $userid,
                            'status' => 4,
                        );
                        $this->db->insert('customer_accounts_subscription_meter_reading', $reading_old_ins_arr);
                        $err_msg[]['insertreading'] = $this->db->_error_message();
                    }
                    // #################################################################################

                    // TRAIL APPROVAL
                    $trail_approval_ins = array(
                        'trailid' => $trailid,
                        'activity' => 87,
                        'userid' => user_id()
                    );
                    $this->db->insert('transaction_request_trails_logs', $trail_approval_ins);
                    $err_msg[]['requesttrails'] = $this->db->_error_message();

                    $data['inputs'] = $this->input->post();

                    if($meter_info->reading > 0 ) {
                        if ($this->db->trans_status() == true /*&& $conn_dev->trans_status() == true*/) {
                            $this->db->trans_commit();
                            $conn_dev->trans_commit();
                            $msg = 'Assignment Success!, please send the transaction to utility accomplishments!';
                            $func = 'success';
                            $qry = true;
                        } else {
                            $this->db->trans_rollback();
                            $conn_dev->trans_rollback();
                            $msg = 'Error: query - ' . $this->db->_error_message();
                            $func = 'warning';
                        }
                    }else{
                        $msg = 'Invalid reading!';
                        $func = 'warning';
                    }
                }else{

                    $msg = 'Asset is not exists!';
                    $func = 'warning';
                }
            }
        }else{
            $msg = 'Session timeout!';
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function cancel_meter_issuance() {
        $data = array();

        $qry = false;

        $joid = $this->input->post('joid');
        $trailid = $this->input->post('trailid');
        $remarks = $this->input->post('remarks');
        $ownerid = $this->input->post('ownerid');


        $qry_check_ownership = $this->db->select('assetid, ownertype, ownerid')
            ->from('assets_main_owner_history')
            ->where(array('ownerid' => $ownerid, 'ownertype' => 3, 'status' => 300))
            ->get()->row();

        if($qry_check_ownership) {
            $this->db->trans_begin();

            $this->db->where(array('ownerid' => $ownerid, 'status' => 300));
            $this->db->update('assets_main_owner_history', array('status' => 0));
            $data['msg_err']['updateownerhist'] = $this->db->_error_message();

            $jo_trail_arr = array(
                'joid' => $joid,
                'codes' => 'CANCELED',
                'descs' => 'Cancel Issuance',
                'remarks' => $remarks,
                'trailid' => $trailid,
                'createdby' => user_id()
            );
            $this->db->insert('joborders_details_trails', $jo_trail_arr);
            $data['msg_err']['instrails'] = $this->db->_error_message();


            $asset_remarks = array(
                'assetid' => $qry_check_ownership->assetid,
                'typesid' => 303,
                'remarks' => $remarks,
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );
            $this->db->insert('assets_remarks', $asset_remarks);

            // TRAIL APPROVAL
            $trail_approval_ins = array(
                'trailid' => $trailid,
                'activity' => 87,
                'userid' => user_id()
            );
            $this->db->insert('transaction_request_trails_logs', $trail_approval_ins);
            $err_msg[]['requesttrails'] = $this->db->_error_message();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                $msg = 'Cancel Success!, cancelation of issuance is success!';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Error: query';
                $func = 'warning';
            }

        }else{
            $msg = 'Cannot cancel issuance!';
            $func = 'error';
            $qry = false;
        }


        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }


    function accomplish_transaction() {
        $data = array();

        $qry = false;

        $msg = 'PHP Error!';
        $func = 'error';

        $err_msg = array();

        $joid = $this->input->post('joid');
        $trailid = $this->input->post('trailid');
        $assetid = $this->input->post('assetid');
        $reading = $this->input->post('reading');
        $remarks = $this->input->post('remarks');
        $dateconn = $this->input->post('date');
        $empid = $this->input->post('empid');

        $date_char = date_formating($dateconn, 'Y-m-d', 'm/d/Y');

        $info = get_joborder_info($joid);

        if($info) {

            $qry_new_asset = $this->db->select('am.labels, am.serials')
                ->from('assets_main_owner_history amoh')
                ->join('assets_main AS am', 'am.sysid = amoh.assetid')
                ->where(array(
                    'amoh.assetid' => $assetid,
                    'amoh.ownerid' => $info->acctid,
                    'amoh.ownertype' => 3,
                    'amoh.status' => 300
                ))
                ->get()->row();
            if($qry_new_asset) {


                $servno = $info->servicenumber;
                $mtr = $info->mtr;
                $mtrno = $qry_new_asset->labels;
                $serial = $qry_new_asset->serials;

                $this->db->trans_begin();

                $data = array(
                    'servno' => $servno,
                    'mtr' => $mtr,
                    'mtrno' => $mtrno,
                    'serial' => $serial,
                    'oldmtrno' => $info->mtrno,
                );


                $this->db->query("
                    UPDATE joborders_details_logs SET status = 314 WHERE sysid = $joid AND status = 300
                ");

                $this->db->query("
                    UPDATE assets_main_owner_history
                    SET
                    status = 0
                    WHERE assetid = $assetid AND (status != 300 OR status != 0)
                ");

                $this->db->query("
                    UPDATE assets_main_owner_history
                    SET
                    status = 1
                    WHERE assetid = $assetid AND ownerid = {$info->acctid} AND ownertype = 3 AND status = 300
                ");



                //UPDATE CUSTOMER ACCOUNT
                $this->db->query("UPDATE customer_accounts_main 
                      SET 
                      mtrno = $mtrno, 
                      mtrserial = '$serial',
                      dateconnected = '$dateconn',
                      mtrassetid = $assetid,
                      status = 1
                      WHERE sysid = {$info->acctid}");
                $err_msg[]['updacctmain'] = $this->db->_error_message();

                $reading_old_ins_arr = array(
                    'types' => 3102,
                    'acctid' => $info->acctid,
                    'mtrid' => $info->mtrno,
                    'reading' => $reading,
                    'schedid' => 0,
                    'createdby' => user_id(),
                    'status' => 4,
                );
                $this->db->insert('customer_accounts_subscription_meter_reading', $reading_old_ins_arr);
                $err_msg[]['insertreading'] = $this->db->_error_message();

                // TRAIL APPROVAL
                $trail_approval_ins = array(
                    'trailid' => $trailid,
                    'activity' => 87,
                    'userid' => user_id()
                );
                $this->db->insert('transaction_request_trails_logs', $trail_approval_ins);
                $err_msg[]['requesttrails'] = $this->db->_error_message();

                // ASSET REMARKS
                $assets_remarks_ins = array(
                    'assetid' => $assetid,
                    'typesid' => 314,
                    'remarks' => $remarks,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('assets_remarks', $assets_remarks_ins);
                $err_msg[]['assetremarks'] = $this->db->_error_message();


                // JOB ORDER TRAILS
                $jo_trails_ins = array(
                    'joid' => $joid,
                    'statusid' => 314,
                    'codes' => 'ACCOMPLISHED',
                    'descs' => $remarks,
                    'createdby' => user_id(),
                    'dateaccomp' => $dateconn,
                    'accompby' => $empid
                );
                $this->db->insert('joborders_details_trails', $jo_trails_ins);
                $err_msg[]['jotrails'] = $this->db->_error_message();



                // UPDATE PECO APPS

                $conn_dev = $this->load->database('pecoapps', TRUE);
                $conn_dev->initialize();
                $conn_dev->trans_begin();
                $qry = $conn_dev->query("
                    UPDATE father
                    SET
                    mtrser____ = '$mtrno',
                    serial____ = '$serial',
                    oldrdg____ = '$reading',
                    stadte____ = '$date_char'
                    pmtrsl____ = '{$info->mtrno}',
                    status____ = 1
                    WHERE servno____ = '$servno' AND mtr_______ = $mtr
                ");


                if ($this->db->trans_status() == true
                    && $conn_dev->status() == true
                ) {
                    $this->db->trans_commit();
                     $conn_dev->trans_commit();
                    $msg = 'Transaction has been accomplished!';
                    $func = 'success';
                    $qry = true;
                } else {
                    $this->db->trans_rollback();

                     $conn_dev->trans_rollback();
                    $msg = 'Query Error!';
                    $func = 'warning';
                }
            }
        }

        $data['errmsg'] = $err_msg;
        $data['info'] = $info;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['msg'] = $msg;
        return json_encode($data);
    }






    function accomplish_fdo() {
        $data = array();

        $qry = false;

        $msg = 'PHP Error!';
        $func = 'error';

        $err_msg = array();

        $joid = $this->input->post('joid');
        $trailid = $this->input->post('trailid');
        // $assetid = $this->input->post('assetid');
        $reading = $this->input->post('reading');
        $remarks = $this->input->post('remarks');
        $dateconn = $this->input->post('date');
        $empid = $this->input->post('empid');

        $date_char = date_formating($dateconn, 'Y-m-d', 'm/d/Y');

        $info = get_joborder_info($joid);

        if($info) {
            $servno = $info->servicenumber;
            $mtr = $info->mtr;
            $mtrno = $info->mtrno;
            $serial = $info->mtrserial;

            $qry_asset = $this->db->select('am.sysid, am.labels, am.serials')
                ->from('assets_main_owner_history amoh')
                ->join('assets_main AS am', 'am.sysid = amoh.assetid')
                ->where(array(
                    'am.labels' => $mtrno,
                    'amoh.ownerid' => $info->acctid,
                    'amoh.ownertype' => 3
                ))
                ->get()->row();
            if($qry_asset) {

                $assetid = $qry_asset->sysid;

                $this->db->trans_begin();

                $data = array(
                    'servno' => $servno,
                    'mtr' => $mtr,
                    'mtrno' => $mtrno,
                    'serial' => $serial,
                    'oldmtrno' => $info->mtrno,
                );

                $this->db->query("
                        UPDATE joborders_details_logs SET status = 314 WHERE sysid = $joid AND status = 300
                    ");
                $err_msg[]['updatejodetailslogs'] = $this->db->_error_message();

                $this->db->query("
                        UPDATE assets_main_owner_history
                        SET
                        status = 0
                        WHERE ownerid = {$info->acctid} AND ownertype = 3 AND status = 1
                    ");
                $err_msg[]['updateownerhist'] = $this->db->_error_message();

                //UPDATE CUSTOMER ACCOUNT
                $this->db->query("UPDATE customer_accounts_main 
                      SET 
                      status = 0
                      WHERE sysid = {$info->acctid}");
                $err_msg[]['updacctmain'] = $this->db->_error_message();

                $reading_old_ins_arr = array(
                    'types' => 3102,
                    'acctid' => $info->acctid,
                    'mtrid' => $info->mtrno,
                    'reading' => $reading,
                    'schedid' => 0,
                    'createdby' => user_id(),
                    'status' => 4,
                );
                $this->db->insert('customer_accounts_subscription_meter_reading', $reading_old_ins_arr);
                $err_msg[]['insertreading'] = $this->db->_error_message();

                // TRAIL APPROVAL
                $trail_approval_ins = array(
                    'trailid' => $trailid,
                    'activity' => 87,
                    'userid' => user_id()
                );
                $this->db->insert('transaction_request_trails_logs', $trail_approval_ins);
                $err_msg[]['requesttrails'] = $this->db->_error_message();

                // ASSET REMARKS

                $assets_remarks_ins = array(
                    'assetid' => $assetid,
                    'typesid' => 314,
                    'remarks' => $remarks,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('assets_remarks', $assets_remarks_ins);
                $err_msg[]['assetremarks'] = $this->db->_error_message();



                // JOB ORDER TRAILS
                $jo_trails_ins = array(
                    'joid' => $joid,
                    'statusid' => 314,
                    'codes' => 'ACCOMPLISHED',
                    'descs' => $remarks,
                    'createdby' => user_id(),
                    'dateaccomp' => $dateconn,
                    'accompby' => $empid
                );
                $this->db->insert('joborders_details_trails', $jo_trails_ins);
                $err_msg[]['jotrails'] = $this->db->_error_message();


                // UPDATE PECO APPS
                $conn_dev = $this->load->database('pecoapps', TRUE);
                $conn_dev->initialize();
                $conn_dev->trans_begin();
                $qry = $conn_dev->query("
                    UPDATE father
                    SET
                    mtrser____ = '$mtrno',
                    serial____ = '$serial',
                    oldrdg____ = '$reading',
                    stadte____ = '$date_char',
                    pmtrsl____ = '{$info->mtrno}',
                    status____ = 4
                    WHERE servno____ = '$servno' AND mtr_______ = $mtr
                ");


                if ($this->db->trans_status() == true
                    && $conn_dev->trans_status() == true
                ) {
                    $this->db->trans_commit();
                    $conn_dev->trans_commit();
                    $msg = 'Transaction has been accomplished!';
                    $func = 'success';
                    $qry = true;
                } else {
                    $this->db->trans_rollback();

                    // $conn_dev->trans_rollback();
                    $msg = 'Query Error!';
                    $func = 'warning';
                }
            }else{

                $msg = 'Asset details is not up-to-date!';
                $func = 'warning';
            }

        }

        $data['errmsg'] = $err_msg;
        $data['info'] = $info;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['msg'] = $msg;
        return json_encode($data);
    }

    function print_order() {
        $data = array();
        $html = '';

        $joid = $this->input->post('joid');

        $qry = get_joborder_info($joid);

        if($qry) {

            if ($qry->tickettype == 322) {
                $qry_acct = get_application_details($qry->acctid)->info;
                $middlename = (isset($qry_acct->middlename[0])) ? $qry_acct->middlename[0].'.' : '';
                $ownername = ($qry_acct) ? $qry_acct->lastname.', '.$qry_acct->firstname.' '.$middlename : 'N/A';
            } else {
                if ($qry->types == 5) {
                    $qry_acctname = $this->db->select()->from('customer_accounts_name_legacy')
                        ->where(array('sysid' => $qry->ownerid))
                        ->get()->row();
                    $ownername = ($qry_acctname) ? $qry_acctname->name : 'N/A';
                } else {

                    $qry_acctname = $this->db->select()->from('person')
                        ->where(array('sysid' => $qry->ownerid))
                        ->get()->row();
                    $ownername = ($qry_acctname) ? $qry_acctname->lastname . ', ' . $qry_acctname->firstname : 'N/A';
                }
            }

            $issued = false;
            $dateissued = '';
            $issuedby = '';
            $check_issued = $this->db->select()->from('joborders_details_trails')
                ->where(array('codes' => 'ISSUED', 'joid' => $joid))
                ->order_by('sysid', 'desc')
                ->get()->row();
            if($check_issued) {
                $check_cancel = $this->db->select()->from('joborders_details_trails')
                    ->where(array('codes' => 'CANCELED', 'joid' => $joid, 'sysid > ' => $check_issued->sysid))
                    ->get()->row();
                if($check_cancel==false) {
                    $issued = true;
                    $dateissued = $check_issued->datecreated;
                    $issuedby = get_users_info($check_issued->createdby)->lastname;
                }
            }



            $html .= '<html>';
            $html .= '<head>';
            $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
            $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
            $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important;  }</style>';
            $html .= '</head>';
            $html .= '<body>';

            $jo_code = get_types_label_format($qry->tickettype, false, false, false, false, false, true)->text;

            $html .= '<div style="position: relative; height: 350px; white-space: nowrap; width: 750px; margin-bottom: 10px;  padding-bottom: 2px;">';
            // MAIN HEADER
            $html .= system_print_header($jo_code, 'Job Order', str_pad($joid, 6, '0', STR_PAD_LEFT));
            // ==========================================

            $html .= '<div style="position: absolute; top: 35px; padding-top: 5px; right: 0px; height: 200px;">';
            $html .= '<p style="font-weight: normal; line-height: 14px; height: 14px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-weight: normal;">' . date("Y-m-d H:i:s") . '</span>';
            $html .= '</p>';
            $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
            $html .= '</div>';

            $html .= '<div style="position: absolute; top: 35px; padding-top: 5px; left: 0px; height: 200px;">';
            $html .= '<p style="font-weight: normal; line-height: 14px; height: 14px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
            $html .= '<span style="font-weight: bold !important; font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
            $html .= ($qry->tickettype == 322) ? $qry_acct->servno : $qry->servicenumber;
            $html .= '</span>';
            $html .= '</p>';
            $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
            $html .= '</div>';

            $html .= '<div style="display: inline-block; position: absolute; top: 55px; padding-top: 5px; left: 0px; height: 100%; width: 750px; display: inline-block;">';
            $html .= ($qry->tickettype == 322) ? '<p></p>' : '';
            if ($qry->tickettype != 322) {
                $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
                $html .= 'Meter No.';
                $html .= '</span>';
                $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
                $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: bold; text-align: right; width: 200px;">';
                $html .= $qry->mtrno;
                $html .= '</span>';
                $html .= '</p>';

                $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px; border-top: 1px dashed #ccc;" class="charges-list-item">';
                $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
                $html .= 'Meter Serial';
                $html .= '</span>';
                $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
                $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: bold; text-align: right; width: 200px;">';
                $html .= $qry->mtrserial;
                $html .= '</span>';
                $html .= '</p>';
            }

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px; border-top: 1px dashed #ccc;" class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
            $html .= 'G-D-L-B';
            $html .= '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: bold; text-align: right; width: 200px;">';
            $html .= ($qry->tickettype == 322) ? get_gdlb_name($qry_acct->gdlbid) : $qry->gdlbcode;
            $html .= '</span>';
            $html .= '</p>';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px; border-top: 1px dashed #ccc;" class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
            $html .= 'Customer Name';
            $html .= '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 450px; font-weight: bold; text-align: right; width: 300px;">';
            $html .= $ownername;
            $html .= '</span>';
            $html .= '</p>';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px; border-top: 1px dashed #ccc;" class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
            $html .= 'Address';
            $html .= '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 520px; font-weight: bold; text-align: right; width: 200px; ">';
            $html .= ($qry->tickettype == 322) ? $qry_acct->addrspec : $qry->addrspecific;
            $html .= '</span>';
            $html .= '</p>';

            if ($qry->tickettype != 3092) {
                if($issued ==  true) {
                    $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; padding-top: 2px; margin-top: 10px; margin-left: 0px; border-top: 1px dashed #ccc;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
                    $html .= 'Asset Issuance';
                    $html .= '</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 310px; font-weight: normal; text-align: center; width: 130px; font-weight: bold;"></span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 450px; font-weight: normal; text-align: center; width: 140px; font-weight: bold;"></span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 600px; font-weight: normal; text-align: center; width: 150px; font-weight: bold;"></span>';
                    $html .= '</p>';
                    $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px; margin-left: 0px; " class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 310px; font-weight: normal; text-align: center; width: 130px; border-top: 1px solid #000">Asset Number</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 450px; font-weight: normal; text-align: center; width: 140px; border-top: 1px solid #000">Meter Number</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 600px; font-weight: normal; text-align: center; width: 150px; border-top: 1px solid #000">Meter Serial</span>';
                    $html .= '</p>';
                } else {
                    $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; padding-top: 2px; margin-top: 10px; margin-left: 0px; border-top: 1px dashed #ccc;" class="charges-list-item">';
                    $html .= ($qry->tickettype == 322) ? '<p></p>' : '';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
                    $html .= 'Asset Issuance';
                    $html .= '</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 310px; font-weight: normal; text-align: center; width: 130px; font-weight: bold;"></span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 450px; font-weight: normal; text-align: center; width: 140px; font-weight: bold;"></span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 600px; font-weight: normal; text-align: center; width: 150px; font-weight: bold;"></span>';
                    $html .= '</p>';
                    $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px; margin-left: 0px; " class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 310px; font-weight: normal; text-align: center; width: 130px; border-top: 1px solid #000">Asset Number</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 450px; font-weight: normal; text-align: center; width: 140px; border-top: 1px solid #000">Meter Number</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 600px; font-weight: normal; text-align: center; width: 150px; border-top: 1px solid #000">Meter Serial</span>';
                    $html .= '</p>';
                }
            }

            $html .= '<hr style="border: 1px solid #000; width: 100% !important;">';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 10px; margin-left: 0px; " class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
            $html .= 'Field Accomplishments';
            $html .= '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: normal; text-align: right; width: 200px;">';
            $html .= '</span>';
            $html .= '</p>';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px; " class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 320px; font-weight: normal; text-align: center; width: 200px; border-top: 1px solid #000">Complied By</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: normal; text-align: center; width: 200px; border-top: 1px solid #000">Date</span>';
            $html .= '</p>';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 20px; margin-left: 0px; " class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
            $html .= 'Meter Data';
            $html .= '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: normal; text-align: right; width: 200px;">';
            $html .= '</span>';
            $html .= '</p>';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px; " class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 320px; font-weight: normal; text-align: center; width: 200px; border-top: 1px solid #000">Reading</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: normal; text-align: center; width: 200px; border-top: 1px solid #000">Sequence</span>';
            $html .= '</p>';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px; margin-left: 0px; " class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: normal;">';
            $html .= 'Acknowledged by:';
            $html .= '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 300px; font-weight: normal;">:</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: normal; text-align: right; width: 200px;">';
            $html .= '</span>';
            $html .= '</p>';

            $html .= '<p style="font-weight: normal; line-height: 16px; height: 16px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px; " class="charges-list-item">';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 320px; font-weight: normal; text-align: center; width: 200px; border-top: 1px solid #000">Customer Name</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 550px; font-weight: normal; text-align: center; width: 200px; border-top: 1px solid #000">Signature</span>';

            $html .= '</p>';

            $html .= '</div>';

            $html .= '</body>';
            $html .= '</html>';
        }

        $data['qry'] = true;
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_joborder_logs() {
        $data = array();

        $joid = $this->input->post('joid');


        $qry = $this->db->select()
            ->from('joborders_details_trails')
            ->where(array('joid' => $joid))
            ->get();
        if($qry->num_rows() > 0) {
            $num = 1;
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    "num" => $num++,
                    "desc" => $row->descs,
                    "datecreated" => $row->datecreated,
                    "createdby" => get_users_info($row->createdby)->lastname,
                );
            }
        }


        $data['inputs'] = $this->input->post();
        return json_encode($data);
    }

    function clear_trans() {
        $data = array();

        $func = 'error';
        $err = array();


        $flow_id_arr = array(16, 17, 18, 19);

        $qry_flows = $this->db->select()->from('transaction_request_main')
            ->where_in('flowid', $flow_id_arr)->get();

        $err['TRUNC'][] = $this->db->_error_message();

        if($qry_flows->num_rows()>0) {

            $this->db->query("TRUNCATE TABLE joborders_details_assignments");
            $this->db->query("TRUNCATE TABLE joborders_details_logs");
            $this->db->query("TRUNCATE TABLE joborders_details_logs_findings");
            $this->db->query("TRUNCATE TABLE joborders_details_trails");

            foreach($qry_flows->result() as $row)
            {

                $data['trnid'][] = $row->sysid;
                $qry_flow_stages = $this->db->select()
                    ->from('transaction_request_main_trails')
                    ->where('trnid', $row->sysid)
                    ->get()->row();
                if($qry_flow_stages) {
                    $stage_id = $qry_flow_stages->sysid;
                    $data['TRAILID'][] = $stage_id;
                    $this->db->where('trailid', $stage_id);
                    $this->db->delete('transaction_request_trails_logs');
                    $err['LOGS'][] = $this->db->_error_message();

                }

                $this->db->where('trnid', $row->sysid);
                $this->db->delete('transaction_request_main_trails');
                $err['TRAILS'][] = $this->db->_error_message();

            }
            $this->db->where_in('flowid', $flow_id_arr);
            $this->db->delete('transaction_request_main');
            $err['MAIN'][] = $this->db->_error_message();


            if($this->db->trans_status()==true) {
                $this->db->trans_commit();
                $msg = 'Job Order Transactions has been cleared cleared!';
                $func = 'success';
            }else{
                $this->db->trans_rollback();
                $msg = 'Error: Query';
            }
        } else {
            $msg = 'No transactions!';
        }


        $data['err'] = $err;
        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }

    function utility_get_mtrinfo() {
        $data = array();
        $mtrno = $this->input->post('mtrno');
        $qry = false;
        $serial = '';
        $reading = '';

        if($mtrno) {
            $qry_asset = $this->db->select()
                ->from('assets_main')
                ->where(array('status' => 3202, 'labels' => $mtrno))
                ->get()->row();
            if($qry_asset) {
                //$qry = true;
                $serial = $qry_asset->serials;
                $qry_rdg = $this->db->select('reading')
                    ->from('customer_accounts_subscription_meter_reading AS casmr')
                    ->where(array('mtrid' => $mtrno , 'types' => 3102, 'status' => 1))
                    ->get()->row();
                if ($qry_rdg) {
                    $qry = true;
                    $reading = $qry_rdg->reading;
                }
            }
        }

        $data['serial'] = $serial;
        $data['qry'] = $qry;
        $data['reading'] = $reading;
        return json_encode($data);
    }

    function save_accomplishments() {
        $data=array();
        $mtrno = $this->input->post('mtrno');
        $serial = $this->input->post('serial');
        $reading = $this->input->post('reading');
        $qry = false;

        if ($mtrno && $serial && $reading) {
            $insert_readinginit = array(

            );
            $qry_init = $this->db->insert('customer_accounts_subscription_meter_reading',$insert_readinginit);
        }

        return json_encode($data);

    }


    function accomplish() {
        $data = array();
        $empid = 0;

        $qry = $this->db->select('
            jdl.sysid, 
            tp.names AS tcname, 
            jdl.acctid,
            jdl.repsource,
            jdl.complainants,
            jdl.remarks, 
            jdl.tickettype, 
            jdl.createdby, 
            jdl.updatedby, 
            jdl.datecreated, 
            jdl.dateupdated, 
            jdl.status,
            jdl.reqverification,
            jdl.contact,
            jdl.address,
            jdl.district,
            jdl.barangays,
            ab.texts AS brgyname,
            jdl.landmarks,
            jdl.compname,
            jdl.etc,
            jdl.status,
            p.firstname,
            p.middlename,
            p.lastname,
            am.types,
            am.servicenumber AS servno,
            am.mtr,
            am.types,
            am.ownerid,
            am.mtrno,
            am.mtrserial,
            am.servicenumber AS servno,
            a.addrspecific AS addr
        ')
            ->from('joborders_details_logs AS jdl')
            ->join('person AS p', 'p.sysid = jdl.complainants', 'left')
            ->join('prime_types_parameter AS tp', 'tp.sysid = jdl.tickettype', 'left')
            ->join('address_barangay AS ab', 'ab.sysid = jdl.barangays', 'left')
            ->join('customer_accounts_main AS am', 'am.sysid = jdl.acctid', 'left')
            ->join('customer_accounts_address AS a', 'a.acctid = am.sysid', 'left')
            ->or_where(array('jdl.status' => 3205))
            ->or_where(array('jdl.status' => 3211))
            ->get();

        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $joid = $row->sysid;

                $servno = $row->servno;
                $mtr = $row->mtr;
                $mtrno = $row->mtrno;
                $serial = $row->mtrserial;
                $acctid = $row->acctid;
                $assetid = 0;
                $qry_asset = $this->db->select('am.sysid, am.labels, am.serials')
                    ->from('assets_main_owner_history amoh')
                    ->join('assets_main AS am', 'am.sysid = amoh.assetid')
                    ->where(array(
                        'am.labels' => $mtrno,
                        'amoh.ownerid' => $acctid,
                        'amoh.ownertype' => 3
                    ))
                    ->get()->row();
                if ($qry_asset) {
                    $assetid = $qry_asset->sysid;
                }

                $data[] = array(
                    'assetid' => $assetid,
                    'accticd' => $acctid,
                    'joid' => $joid
                );
            }
        }

        return json_encode($data);
    }
}