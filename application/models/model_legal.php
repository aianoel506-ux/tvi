<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_legal extends CI_Model {

    function get_apprehension_list() {
        $data = array();
        $ecalesid = $this->input->post('ecalesid');
        $qry = $this->db->select('it.sysid, ms.descs, pq.amt, it.qty')
            ->from('customer_ecales_item_trn AS it')
            ->join('items_main_spec AS ms', 'it.itemid = ms.sysid', 'left')
            ->join('trn_prs_quotations AS pq', 'it.quoteid = pq.sysid', 'left')
            ->where(array('it.ecalesid' => $ecalesid, 'it.status' => 1))
            ->order_by('ms.descs')
            ->get();
        $total_amt = 0;
        if ($qry->num_rows() > 0) {
            $i = 1;

            foreach ($qry->result() as $row) {
                $row_total_amt = $row->qty * $row->amt;
                $total_amt += $row_total_amt;
                $data['list'][] = array(
                    'num' => $i++,
                    'item' => $row->descs,
                    'amt' => number_format($row->amt, 2),
                    'qty' => number_format($row->qty),
                    'total' => number_format($row_total_amt, 2),
                    'control' => '<a data-id="' . $row->sysid . '" title="Remove Item" btn-default="btn-danger" btn-success="btn-success" btn-warning="btn-warning" id="del_btn" href="' . base_url('inspection/delecalesitem') . '" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>'
                );
            }
        }
        $data['ecalesnum'] = str_pad($ecalesid, 8, '0', STR_PAD_LEFT);
        $data['totalamt'] = number_format($total_amt, 2);
        return json_encode($data);
    }



    function get_ticket_list() {
        $data = array();
        $int = 1;
        $qry = $this->db->select('
            tdl.sysid, 
            tdl.acctid, 
            tp.names AS tcname, 
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
            p.lastname,
            am.types,
            am.servicenumber AS servno,
            am.mtr,
            a.addrspecific AS addr,
            am.types,
            am.ownerid,
            am.mtrno,
            am.mtrserial
        ')
            ->from('ticketing_details_logs AS tdl')
            ->join('person AS p', 'p.sysid = tdl.complainants', 'left')
            ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype', 'left')
            ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart', 'left')
            ->join('address_barangay AS ab', 'ab.sysid = tdl.barangays', 'left')
            ->join('customer_accounts_main AS am', 'am.sysid = tdl.acctid', 'left')
            ->join('customer_accounts_address AS a', 'a.acctid = am.sysid', 'left')
            ->where(array('tdl.status > ' => 0))
            ->where_in('tdl.repsource', 1087)
            ->get();
        if($qry->num_rows()>0) {

            foreach ($qry->result() as $row) {

                $ref = '';
                $findings_id = null;
                $findings_name = null;
                $qry_findings = $this->db->select('lf.findingid')
                    ->from('ticketing_details_logs_findings AS lf')
                    ->where(array('lf.ticketid' => $row->sysid, 'lf.status' => 1))
                    ->get()->row();
                if ($qry_findings) {


                    $ref = get_types_label_format($qry_findings->findingid, false, true, 'top');
                    /*
                    $findings_id = $qry_findings->findingid;
                    if ($row->status == 314) {
                        if(in_array(1, user_info()->roles) || user_id() == 1) {
                            $ref .= '<input type="hidden" class="form-control inline" id="select2_ref" name="ref[]" value="' . $findings_id . '" />';
                            $ref .= get_types_label_format($findings_id, false, true, 'top', 'javascript:;', false, false);
                        }else {
                            $ref = get_types_label_format($findings_id, false, true, 'top');
                        }
                    } else {

                        if ($int == 1) {
                            $ref .= '<input type="hidden" class="form-control inline" id="select2_ref" name="ref[]" value="' . $findings_id . '" />';
                            $ref .= get_types_label_format($findings_id, false, true, 'top', 'javascript:;', false, false);

                        } else {
                            $ref = get_types_label_format($findings_id);
                        }
                    }
                    */

                }else{
                    $ref = '<a class="label label-info">None</a>';
                }


                $status = '';
                if ($row->status == 314) {
                    if (in_array(1, user_info()->roles) || user_id() == 1) {
                        $status .= '<input type="hidden" class="form-control inline" id="select2_status_adm" name="status[]" value="' . $row->status . '" />';
                        $status .= get_types_label_format($row->status, false, true, 'top', 'javascript:;', false, false);
                    } else {
                        $status = get_types_label_format($row->status, false, true, 'top');
                    }
                } else {

                    if ($int == 1) {
                        $status .= '<input type="hidden" class="form-control inline" id="select2_status" name="status[]" value="' . $row->status . '" />';
                        $status .= get_types_label_format($row->status, false, true, 'top', 'javascript:;', false, false);

                    } else {
                        $status = get_types_label_format($row->status);
                    }
                }

                $acctinfo = get_active_account_info($row->acctid);
                $acctdetails = '';
                if($acctinfo) {
                    $acctdetails .= '<b>'.$acctinfo->servicenumber.'</b>';
                    $acctdetails .= ' - '. $acctinfo->name . '<br>';
                    $acctdetails .= $acctinfo->address;
                } else {
                    $acctdetails = 'None';
                }

                $complainants = '<b>'.$row->lastname.', '.$row->firstname. '</b><br>' . $row->address;

                $remarks = '';
                $remarks .= '<form id="frm_remarks" method="post" action="'.base_url('cwdo/addremarksrow').'">';
                $remarks .= '<input type="hidden" class="form-control" name="ticketid" value="'.$row->sysid.'" id="" placeholder="Ticket ID" />';
                $remarks .= '<input type="hidden" class="form-control" name="statusid" value="'.$row->status.'" id="" placeholder="Ticket ID" />';
                $remarks .= '<input class="form-control inline" name="remarks" id="remarks" placeholder="Remarks.." />';
                $remarks .= '<button type="submit" class="hidden"></button>';
                $remarks .= '</form>';

                $control = '';

                $hashlink = '667be543b02294b7624119adc3a725473df39885';

                $control .= '<a href="'.base_url('module/'.$hashlink.'/table/'.$row->sysid).'" class="btn btn-warning btn-xs inline"><i class="fa fa-history"></i></a>';
                $control .= '<a href="'.base_url('module/'.$hashlink.'/view/'.$row->sysid).'" class="btn btn-info btn-xs inline"><i class="fa fa-search"></i></a>';

                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'num' => '',
                    'ticketno' => '<input type="hidden" id="ticketid" value="' . $row->sysid . '" /></a><b>'.str_pad($row->sysid, 8, '0', STR_PAD_LEFT)  . '</b><br>' . get_types_label_format($row->repsource, false, false, false, false, false, true)->text,
                    'name' => $complainants,
                    'acctname' => $acctdetails,
                    'time' => $row->datecreated,
                    'complaints' => $row->tcname,
                    'remarks' => $remarks,
                    'status' => $status,
                    'codes' => $ref,
                    'control' => $control,
                );
            }
        }
        return json_encode($data);
    }


    function get_ticket_details_basic() {
        $data = array();
        $html = '';
        $ticketid = $this->input->post('id');
        $qry = $this->db->select('
            tdl.sysid, 
            tp.desc, 
            tpr.descs AS particular, 
            tdl.remarks, 
            tdl.createdby, 
            tdl.updatedby, 
            tdl.datecreated, 
            tdl.dateupdated, 
            tdl.status,
            tdl.reqverification
        ')
            ->from('ticketing_details_logs AS tdl')
            ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype', 'left')
            ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart', 'left')
            ->where(array('tdl.sysid' => $ticketid))
            ->get()->row();
        if($qry) {
            $verificaton = ($qry->reqverification == 1) ? 'true': 'false';
            $verfiedby = 'N/A';
            if($verificaton=='true') {
                $verfiedby = 'admin';
            }
            $html .= '<div class="row margin-bottom-5">';
            $html .= '<div class="col-md-3">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Last Update</span>';
            $html .= '<span class="col-md-8 label-default">'.$qry->dateupdated.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Updated By</span>';
            $html .= '<span class="col-md-8 label-default">'.get_users_info($qry->updatedby)->username.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="col-md-3">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Verification</span>';
            $html .= '<span class="col-md-8 label-default">'.$verificaton.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Verified</span>';
            $html .= '<span class="col-md-8 label-default">'.$verfiedby.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="col-md-2">';
            $html .= '<div class="row margin-bottom-10">';
            $html .= '<div class="col-md-12">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<div class="col-md-9 label-default">Attachments</div>';
            $html .= '<div class="col-md-3 label-default number text-danger"><i class="fa fa-search"></i></div>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="row">';
            $html .= '<span class="col-md-6"><a class="fancy-box" title="Apprehension Attachments" href="'.base_url('uploads/attachments/legal/').str_pad($qry->sysid, 8, '0', STR_PAD_LEFT).'/primary.jpg"><img src="'.base_url('uploads/attachments/legal/').str_pad($qry->sysid, 8, '0', STR_PAD_LEFT).'/primary.jpg" width="100%" height="50px"/> </a></span>';
            $html .= '<span class="col-md-6"><a class="fancy-box" title="Verification Attachments" href="'.base_url('uploads/attachments/legal/').str_pad($qry->sysid, 8, '0', STR_PAD_LEFT).'/verify.jpg"><img src="'.base_url('uploads/attachments/legal/').str_pad($qry->sysid, 8, '0', STR_PAD_LEFT).'/verify.jpg" width="100%" height="50px"/> </a></span>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name"><i class="fa fa-binoculars"></i> Findings</span>';
            $html .= '<span class="col-md-8 label-default">SML - Sealed Meter Line</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name"><i class="fa fa-calendar"></i> Verified</span>';
            $html .= '<span class="col-md-8 label-default">'.$qry->dateupdated.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';



            $html .= '<div class="row footer">';
            $html .= '<div class="col-md-9">';

            $html .= '</div>';


            $html .= '<div class="col-md-3">';
            $html .= '</div>';
            $html .= '</div>';
        }


        $data['html'] = $html;
        return json_encode($data);
    }


    function save_apprehension() {
        $data = array();
        $qry = false;
        $title = 'LIS.net';
        $msg = 'Apprehension save error!';
        $func = 'error';
        $data['input'] = $this->input->post();

        $apprid = $this->input->post('apprehension');
        $moduleid = $this->input->post('moduleid');
        $stagestart = $this->input->post('stagestart');
        $entrystyle = $this->input->post('entrystyle');
        $lastname = $this->input->post('lastname');
        $firstname = $this->input->post('firstname');
        $middlename = $this->input->post('middlename');
        $dateapprehend = $this->input->post('inspectiondate');
        $inspector = $this->input->post('inspector');

        $err = array();

        $this->db->trans_begin();
        //if($lastname && $firstname && $middlename) {
        if ($apprid) {
            //GET APPREHENSION TYPE
            if (is_numeric($lastname)) {
                $qry_person = $this->db->select()->from('person')->where('sysid', $lastname)->get()->row();
                $personid = ($qry_person) ? $qry_person->sysid : 0;

                $lastname = $qry_person->lastname;
                $firstname = $qry_person->firstname;
                $middlename = $qry_person->middlename;
            } else {
                $person_ins_arr = array(
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'middlename' => $middlename,
                );
                $this->db->insert('person', $person_ins_arr);
                $personid = $this->db->insert_id();
                $err[] = $this->db->_error_message();
            }
            $qry_apprehension_name = $this->db->select()->from('prime_types_parameter')->where('sysid', $apprid)->get()->row();


            // INSERT TO APPREHENSION
            $appr_main_arr = array(
                'moduleid' => $moduleid,
                'types' => $apprid,
                'personid' => $personid,
                'createdby' => user_id(),
                'dateapprehend' => $dateapprehend,
                'inspector' => $inspector
            );
            $this->db->insert('trn_apprehensions', $appr_main_arr);
            $dataid = $this->db->insert_id();
            $err[] = $this->db->_error_message();

            // GLOBAL FUNCTION //
            // FOR TRAIL LOGS  //
            $trans = create_transaction_trails('APPREHENSION - ' . $qry_apprehension_name->names, $qry_apprehension_name->names . ' - ' . strtoupper($firstname . ' ' . $lastname), $moduleid, $dataid);


            if ($this->db->trans_status() === true && $trans == true) {
                $this->db->trans_commit();
                $msg = 'Apprehension saved!';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_rollback();
            }
        }else{
            $msg = 'Please select apprehension type!';
        }
        //}
        $data['err'] = $err;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_apprehension_table() {
        $data = array();
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $match_cnt = 0;
        $acctex_cnt = 0;
        $html = '';

        $qry_app = $this->db->select()->from('application_customers_details as acd')
            ->join('person as p','acd.personid = p.sysid')
            ->where(array('acd.sysid' => $id))
            ->get()->row();
        if($qry_app) {
            $firstname = $qry_app->firstname;
            $lastname = $qry_app->lastname;

            $qry_accoutns_legacy = $this->db->select('sysid, pname, paddr, servno, CAST(dbdte AS DATE) AS appdte')
                ->from('legacy_ra7832')
                ->like('pname', $lastname)->get();
            $person_exists = false;
            $first_array = array();
            if ($qry_accoutns_legacy->num_rows() > 0) {
                foreach ($qry_accoutns_legacy->result() as $row) {
                    $first_array[] = array('sysid' => $row->sysid, 'firstname' => $row->pname, 'middlename' => $row->pname, 'address' => $row->paddr, 'servno' => $row->servno, 'appdate' => $row->appdte);
                    $data['personarr'] = $first_array;
                }
                $search_firstname = array_contains_key($first_array, 'firstname', 'sysid', $firstname);
                $search_firstname_cnt = count($search_firstname);
                $match_cnt = $search_firstname_cnt;
                $data['searchfirstname'] = $search_firstname;
                if ($search_firstname_cnt>0) {
                    $num = 1;
                    foreach ($first_array as $row) {
                        $name = highlightkeyword($row['firstname'], $lastname.', '.$firstname);
                        $address = $row['address'];

                        $data['list'][] = array(
                            'num' => $num++,
                            'name' => $name,
                            'address' => $address,
                            'apprehensiondate' => $row['appdate'],
                        );
                    }

                    $check_exemptions = $this->db->select('createdby')->from('application_customers_exemptions')
                        ->where(array('appid' => $id, 'status' => 1))
                        ->get()->row();
                    if($check_exemptions) {
                        $ra_box_color = 'green';
                        $search_firstname_cnt = '<span class="">(Ignored)</span>';
                    }else{
                        $ra_box_color = 'red-flamingo';
                        $search_firstname_cnt = $search_firstname_cnt;
                    }

                    $html .= verify_html_small($search_firstname_cnt, 'Apprehension match found!', 'fa-warning', $ra_box_color);

                }
            }


            $qry_accoutns_legacy = $this->db->select('nl.sysid, nl.name, gm.d , SUM(ar.amt_01 + ar.amt_02 + ar.amt_03 + ar.amt_04 + ar.amt_05 + ar.amt_06 + ar.amt_07 + ar.amt_08 + ar.amt_09 + ar.amt_10 + ar.amt_11 + ar.amt_12) AS bal')
                //  ->select("(ar.amt_01 + ar.amt_02 + ar.amt_03 + ar.amt_04 + ar.amt_05 + ar.amt_06 + ar.amt_07 + ar.amt_08 + ar.amt_09 + ar.amt_10 + ar.amt_11 + ar.amt_12) AS bal", false)
                ->from('customer_accounts_name_legacy AS nl')
                ->join('customer_accounts_main AS am', 'am.ownerid = nl.sysid')
                ->join('gdlb_main AS gm', 'gm.sysid = am.gdlb')
                ->join('customer_accounts_ar AS ar', 'am.sysid = ar.acctid')
                ->like('nl.name', $lastname)
                ->group_by('nl.sysid, nl.name, gm.d')
                ->order_by('nl.name')
                ->get();


            $data['errqrymsg'] = $this->db->_error_message();
            $acct_arr = array();
            if($qry_accoutns_legacy->num_rows()>0) {
                foreach ($qry_accoutns_legacy->result() as $row) {
                    $acct_arr[] = array('sysid' => $row->sysid, 'firstname' => $row->name, 'middlename' => $row->name, 'district' => $row->d, 'bal' => $row->bal);
                    $data['acctarr'] = $acct_arr;
                }
                $acct_firstname = array_contains_key($acct_arr, 'firstname', 'sysid', $firstname);
                $acct_firstname_cnt = count($acct_firstname);
                $data['acctfirstarr'] = $acct_firstname;
                $data['acctfirstarrcnt'] = $acct_firstname_cnt;
                if ($acct_firstname_cnt > 0) {
                    $data['personexists'] = $person_exists;
                    $html .= '<h4 class="text-primary"> <i class="fa fa-tag"></i>Matching Account [A/R]<span class="label label-danger pull-right">' . $acct_firstname_cnt . '</span></h4>';

                    foreach ($acct_firstname as $arow) {
                        $bal = (isset($arow['bal'])) ? $arow['bal'] : 0;

                        $servno = (isset($arow['servno'])) ? $arow['servno'] : '';
                        $balance = 'Balance: <b>' . number_format($bal, 2) . '</b>';
                        $html .= verify_html_small($balance, '<b>'.$servno.'</b> '.$arow['firstname'], 'fa-tags', 'blue');
                        $acctex_cnt += 1;
                    }
                }
            }
        }
        $data['html'] = $html;
        $data['matchcnt'] = $match_cnt;
        return json_encode($data);
        /*

        $get_person_info = $this->db->select()->from('person')->where('sysid', $id)->get()->row();

        if($type==2) {
            $qry_apprehension_new = $this->db->query("SELECT * FROM trn_apprehensions AS ta
                LEFT JOIN person AS p ON p.sysid = ta.personid");

            if ($qry_apprehension_new->num_rows() > 0) {
                $num = 1;
                foreach ($qry_apprehension_new->result() as $row) {
                    $name = highlightkeyword($row->lastname, $get_person_info->lastname). ', ' .highlightkeyword($row->firstname, $get_person_info->firstname);

                    $data['list'][] = array(
                        'num' => $num++,
                        'name' => $name,
                        'address' => '',
                        'apprehensiondate' => '',
                    );
                }
            }

        }else if($type==3) {
            $qry_apprehension_legacy = $this->db->select()->from('legacy_ra7832')->get();
            if ($qry_apprehension_legacy->num_rows() > 0) {
                $num = 1;
                foreach ($qry_apprehension_legacy->result() as $row) {
                    $name_arr = explode(',', $row->pname);
                    if(isset($name_arr[1])) {
                        $name = highlightkeyword($name_arr[0], $get_person_info->lastname). ', ' .highlightkeyword($name_arr[1], $get_person_info->firstname);
                    }else{
                        $name = highlightkeyword($name_arr[0], $get_person_info->lastname);
                    }
                    $data['list'][] = array(
                        'num' => $num++,
                        'name' => $name,
                        'address' => $row->paddr,
                        'apprehensiondate' => $row->pdater,
                    );
                }
            }
        }else {

            $qry_apprehension_new = verify_query(150, $id);
            if ($qry_apprehension_new->num_rows() > 0) {
                $num = 1;
                foreach ($qry_apprehension_new->result() as $row) {

                    $data['list'][] = array(
                        'num' => $num++,
                        'name' => $row->firstname,
                        'address' => '',
                        'apprehensiondate' => '',
                    );
                }
            }

            $qry_apprehension_legacy = veriry_legacy_ra7832($id);
            if ($qry_apprehension_legacy->num_rows() > 0) {
                $num = 1;
                foreach ($qry_apprehension_legacy->result() as $row) {
                    $name_arr = explode(',', $row->pname);
                    $data['list'][] = array(
                        'num' => $num++,
                        'name' => highlightkeyword($name_arr[0], $get_person_info->lastname). ', ' .highlightkeyword($name_arr[1], $get_person_info->firstname),
                        'address' => $row->paddr,
                        'apprehensiondate' => $row->pdater,
                    );
                }
            }
        }
        return json_encode($data);
        */
    }

    function save_exemption() {
        $data = array();
        $qry = false;
        $msg = 'Undefined';
        $func = 'error';
        $title = 'Legal Exemption';

        $id = $this->input->post('id');
        $stageid = 0;

        // QUERY
        $this->db->trans_begin();

        // GET FLOW ID OF DATAID
        $qry_details = $this->db->select('ms.flowid, mt.stageid')
            ->from('transaction_request_main_trails AS mt')
            ->join('prime_transaction_flow_main_stages AS ms', 'ms.sysid = mt.stageid')
            ->where('mt.dataid', $id)
            ->order_by('mt.sysid', 'desc')
            ->get()->row();


        // GET OWNER ID
        $qry_owner = $this->db->select()->from('application_customers_details')->where('sysid', $id)->get()->row();
        if($qry_owner) {
            $this->db->where(array('appid' => $id, 'status' => 1));
            $this->db->update('application_customers_exemptions', array('status' => 1));

            $stageid = $qry_details->stageid;

            $ins_arr = array(
                'appid' => $id,
                'stageid' => $stageid,
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );
            $this->db->insert('application_customers_exemptions', $ins_arr);
            $data['errmsgs'][] = $this->db->_error_message();
        }

        if($qry_details) {
            $flowid = $qry_details->flowid;
            $stageid = $qry_details->stageid;
            // UPDATE STATUS FIRST
            $this->db->where(array('dataid' => $id, 'status' => 1));
            $this->db->update('prime_transaction_flow_main_stages_required_exempt', array('status' => 0, 'updatedby' => user_id()));

            // ######################################################
            // ### INSERT EXEMPT ####################################
            // ######################################################
            $ins_arr = array(
                'flowid' => $flowid,
                'dataid' => $id,
                'stageid' => $stageid,
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );
            $this->db->insert('prime_transaction_flow_main_stages_required_exempt', $ins_arr);
            $data['errmsgs'][] = $this->db->_error_message();
        }


        $audit_ins_arr = array(
            'dataid' => $id,
            'moduleid' => $stageid,
            'valueold' => $stageid,
            'valuenew' => 0,
            'createdby' => user_id(),
            'remarks' => 'Legal Exemptions'
        );

        $audit_ins = audit_insert($audit_ins_arr);

        if($this->db->trans_status()=== TRUE && $audit_ins){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Account has been cleared!';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Unable to clear this account!';
            $func = 'warning';
        }


        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function get_tbl_ledger() {
        $data = array();
        $refid = $this->input->post('refid');
        $data_list = array();
        $total_amt = 0;
        $total_paid = 0;
        $total_balance = 0;

        $sql_ledger = $this->db->select('
                tl.sysid,
                tl.refid,
                tl.typesid,
                tl.func,
                tl.amt,
                coa.codes AS acctno,
                coa.descs AS acctdesc,
                tl.payable,
                tl.payablecnt,
                tl.payablestartdue,
                tl.datecreated
            ')
            ->from('legal_transaction_ledger AS tl')
            ->join('prime_chart_of_accounts AS coa', 'coa.sysid = tl.acctno')
            ->where(array('tl.refid' => $refid, 'tl.status' => 1))
            ->get();
        if($sql_ledger->num_rows()>0) {
            $num = 1;
            foreach($sql_ledger->result() as $lrow) {

                $payable = '<code>N/A</code>';
                $checked = '';

                if($lrow->payable == 1) {
                    $total_amt += $lrow->amt;
                    $checked = ' checked ';
                }

                $status = '';
                if($lrow->typesid == 1200) {
                    $status = '<span class="label label-danger"><i class="fa fa-warning"></i> Priority</span>';
                }


                $checkbox = '<input '.$checked.' class="icheck" id="row_icheck" data-id="'.$lrow->sysid.'" type="checkbox" />';
                $control = '';
                $control .= '<button id="btn_delete_ledger_item" class="btn btn-xs btn-danger inline" type="button" data-id="'.$lrow->sysid.'"><i class="fa fa-times"></i></button>';

                $qry_staggered = $this->db->select('COUNT(trnid) AS cnt')->from('legal_transaction_staggered')
                    ->where(array('status' => 1, 'trnid' => $lrow->sysid))
                    ->get()->row();

                $qry_staggered_nextdue = $this->db->select('duedate')
                    ->from('legal_transaction_staggered')
                    ->where(array('status' => 1, 'trnid' => $lrow->sysid))
                    ->order_by('duedate')
                    ->get()->row();

                $top = ($qry_staggered && $lrow->payable == 1) ? $qry_staggered->cnt : '<code>N/A</code>';
                $duedate = ($qry_staggered_nextdue && $lrow->payable == 1) ? $qry_staggered_nextdue->duedate : '<code>N/A</code>';


                $monthly = ($lrow->payablecnt > 0) ? ($lrow->amt / $lrow->payablecnt) : 0;

                if($top == 1) {
                    $top = '1 time pay.';
                    $monthly = $lrow->amt;
                } else {
                    if($top>1) {
                        $monthly = ($lrow->amt / $top);
                    }
                }


                $date_r = date_formating($lrow->datecreated, 'Y-m-d H:i:s', 'Y-m-d');
                $date_t = date_formating($lrow->datecreated, 'Y-m-d H:i:s', 'h:i:s A');
                $date_rf = '<a href="javascript:;" data-placement="top" class="tooltips" title="'.$date_t.'">'.$date_r.'</a>';

                $data_list[] = array(
                    'num' => $num++,
                    'datepost' => $date_rf,
                    'types' => get_types_label_format($lrow->typesid, false, false, false, false, true),
                    'amt' => number_format($lrow->amt, 2),
                    'paid' => number_format(0, 2),
                    'nextdue' => $duedate,
                    'acctno' => '<a href="javascript:;" class="tooltips" title="'.$lrow->acctdesc.'">' . $lrow->acctno . '</a>',
                    'status' => $status,
                    'top' => $top,
                    'monthly' => number_format($monthly, 2),
                    'checkbox' => $checkbox,
                    'control' => $control,
                );
            }
        }

        $total_balance = ($total_amt - $total_paid);
        $data['totalamt'] = number_format($total_amt, 2);
        $data['totalpaid'] = number_format($total_paid, 2);
        $data['totalbalance'] = number_format($total_balance, 2);
        $data['list'] = $data_list;

        return json_encode($data);
    }

    function delete_ledger_item() {
        $id = $this->input->post('id');
        $status_id = 303;

        $qry_info = $this->db->select('refid')
            ->from('legal_transaction_ledger')
            ->where(array('sysid' => $id))
            ->get()->row();
        $refid = ($qry_info) ? $qry_info->refid : 0;

        $ticket_trail_arr = array(
            'ticketid' => $refid,
            'codes' => 'Cancel Ledger',
            'descs' => 'Cancel Ledger',
            'statusid' => $status_id,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

        $this->db->where(array('sysid' => $id));
        $this->db->update('legal_transaction_ledger', array(
            'status' => $status_id,
            'updatedby' => user_id()
        ));
        $data = db_trans($this->db);
        return json_encode($data);
    }



    function add_ra_trans(){
        $data = array();
        $reftype = $this->input->post('reftype');
        $refid = $this->input->post('refid');
        $typeid = $this->input->post('trntype');
        $amt = $this->input->post('trnamt');
        $payable = $this->input->post('payable');

        $payablecnt = $this->input->post('payablecnt');
        $payablestartdue = $this->input->post('payablestartdue');
        $billmonth = $this->input->post('billmonth');
        $billyear = $this->input->post('billyear');

        $status_id = 313;

        $due_arr = array();
        $amt_permonth = 0;

        if($typeid) {

            $this->db->trans_begin();
            $ins_arr = array(
                'refid' => $refid,
                'amt' => $amt,
                'typesid' => $typeid,
                'acctno' => 198,
                'payable' => $payable,
            );

            if ($payable == 1 && $typeid != 1200) {
                $this->db->query("UPDATE legal_transaction_ledger SET payable = 0 WHERE refid = $refid AND typesid != 1200 AND status = 1");
            }

            $this->db->query("UPDATE legal_transaction_ledger SET status = 0 WHERE refid = $refid AND typesid = $typeid AND status = 1");
            $data['errupdate'] = $this->db->_error_message();

            $this->db->insert('legal_transaction_ledger', $ins_arr);
            $data['err'] = $this->db->_error_message();

            $trnid = $this->db->insert_id();


            $ticket_trail_arr = array(
                'ticketid' => $refid,
                'codes' => 'Add New Ledger Item',
                'descs' => 'Amount: '.number_format($amt, 2).' | Ledger ID No: '  . $trnid,
                'statusid' => $status_id,
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

            $amt_permonth = 0;
            if ($payablecnt > 0) {
                $amt_permonth = ($amt / $payablecnt);

                $start_month = $billmonth;
                $start_year = $billyear;

                $time = strtotime($payablestartdue);
                $due_arr = array();
                for ($m = 1; $m <= $payablecnt; $m++) {
                    $due = date('Y-m-d', $time);
                    if ($start_month > 12) {
                        $start_month = 1;
                        $start_year = $start_year + 1;
                    }

                    $due_arr_row = array(
                        'trnid' => $trnid,
                        'years' => $start_year,
                        'months' => $start_month,
                        'duedate' => $due,
                        'amt' => $amt_permonth,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('legal_transaction_staggered',  $due_arr_row);
                    $due_arr[] = $due_arr_row;

                    $time = strtotime('+30 day', $time);
                    $start_month++;
                }
            }
            $data = db_trans($this->db);
        }else{
            $data['msg'] = 'Select type';
            $data['func'] = 'warning';
            $data['qry'] = false;
        }


        $data['refid'] = $refid;
        $data['totalamt'] = $amt;
        $data['monthtopay'] = $payablecnt;
        $data['amtpermonth'] = $amt_permonth;
        $data['due'] = $due_arr;
        return json_encode($data);
    }

    function add_bp_trans(){
        $data = array();

        $tickettype = $this->input->post('tickettype');
        $inspector = $this->input->post('inspector');
        $dateinspection = $this->input->post('dateinspection');
        $intamt = $this->input->post('intamt');
        $refid = $this->input->post('refid');

        $this->db->trans_begin();
        if($tickettype == 150){
            $computedamt = $this->input->post('computedamt');
            $adjustmentamt = $this->input->post('adjustmentamt');
            $compromiseamt = $this->input->post('compromiseamt');
            $downpaymentamt = $this->input->post('downpaymentamt');

            $updatearr = array(
                'intamt' => $intamt,
                'cmpamt' => ($computedamt > 0) ? $computedamt : 0,
                'adjamt' => ($adjustmentamt > 0) ? $adjustmentamt : 0,
                'cmxamt' => ($compromiseamt > 0) ? $compromiseamt : 0,
                'dpamt' => ($downpaymentamt > 0) ? $downpaymentamt : 0,
            );
            $this->db->where(array("refid" => $refid));
            $checkifexist = $this->db->update("legal_transactions" , $updatearr);
            $data['updatearr'] = $this->db->_error_message();

            if(!$this->db->affected_rows() > 0){
                $insarr = array(
                    'refid' => $refid,
                    'empid' => $inspector,
                    'acctcodeid' => 164,
                    'intamt' => $intamt,
                    'cmpamt' => ($computedamt > 0) ? $computedamt : 0,
                    'adjamt' => ($adjustmentamt > 0) ? $adjustmentamt : 0,
                    'cmxamt' => ($compromiseamt > 0) ? $compromiseamt : 0,
                    'dpamt' => ($downpaymentamt > 0) ? $downpaymentamt : 0,
                    'irdate' => $dateinspection,
                    'irtime' => date("h:i:s"),
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert("legal_transactions" , $insarr);
                $data['insertarr'] = $this->db->_error_message();
                $ticket_trail_arr = array(
                    'ticketid' => $refid,
                    'codes' => 'LEGALAMT',
                    'descs' => 'LEGAL - Insert RA7832 Details',
                    'statusid' => 307,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

            }else{
                $ticket_trail_arr = array(
                    'ticketid' => $refid,
                    'codes' => 'LEGALAMT',
                    'descs' => 'LEGAL - Update RA7832 Details',
                    'statusid' => 307,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
            }
        }else if($tickettype == 151){

            $checkno = $this->input->post('checkno');
            $checkbankid = $this->input->post('checkbankid');
            $checkamt = $this->input->post('checkamt');
            $amtper = $this->input->post('amtper');
            $dlawper = $this->input->post('lawper');

            $checknoval = ($checkno > 0) ? $checkno : 0;
            $checkbankidval = ($checkbankid > 0) ? $checkbankid : 0;
            $checkamtval = ($checkamt > 0) ? $checkamt : 0;
            $amtperval = ($amtper > 0) ? $amtper : 0;
            $dlawperval = ($dlawper > 0) ? $dlawper : 0;



            $updatearr = array(
                'intamt' => $intamt,
                'chkno' => $checknoval,
                'chkbankid' => $checkbankidval,
                'chkamt' => $checkamtval,
                'amtper' => $amtperval,
                'lawper' => $dlawperval,
            );
            $this->db->where(array("refid" => $refid));
            $this->db->update("legal_transactions" , $updatearr);
            $data['updateerror'] =$this->db->_error_message();

            if(!$this->db->affected_rows() > 0){
                $insarr = array(
                    'refid' => $refid,
                    'empid' => $inspector,
                    'acctcodeid' => 164,
                    'intamt' => $intamt,
                    'chkno' => $checknoval,
                    'chkbankid' => $checkbankidval,
                    'chkamt' => $checkamtval,
                    'amtper' => $amtperval,
                    'lawper' => $dlawperval,
                    'irdate' => $dateinspection,
                    'irtime' => date("h:i:s"),
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert("legal_transactions" , $insarr);
                $data['insertarr'] = $this->db->_error_message();
                $ticket_trail_arr = array(
                    'ticketid' => $refid,
                    'codes' => 'LEGALAMT',
                    'descs' => 'LEGAL - Insert Bounce Check Details | Check No.: '.$checknoval.' | Amount: '.$checkamtval.' | Bank: ' . get_types_label_format($checkbankidval, false, false, false, false, false, true)->text,
                    'statusid' => 307,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
            }else{
                $ticket_trail_arr = array(
                    'ticketid' => $refid,
                    'codes' => 'LEGALAMT',
                    'descs' => 'LEGAL - Update Bounce Check Details',
                    'statusid' => 307,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
                $data['error'] = $this->db->_error_message();
            }
        }

        if($this->db->trans_status()=== TRUE){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Transaction has been saved';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Unable to save transaction!';
            $func = 'warning';
            $qry = false;
        }


        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

    function get_penalty_payments_tbl(){
        $data = array();
        $qry = false;
        $ticketid = $this->input->post('servno');

        $acct_payable = array(1200, 1202, 1203, 1204, 1205);

        // CHECK DOWN PAYMENT FIRST
        /*
        $checkdp = $this->db->select("tl.amt")
            ->from("legal_transaction_ledger AS tl")
            ->join('transaction_payments_logs AS pl', 'pl.dataid = tl.refid')
            ->where(array('tl.refid' => $ticketid, 'tl.typesid' => 1200, 'tl.status' => 1))
            ->where('pl.refid NOT NULL')
            ->where_in('tl.typesid', $acct_payable)
            ->get()->row();
        */

        $acct_payable_str = implode(', ', $acct_payable);
        $checkdp = $this->db->query("
                SELECT SUM(tl.amt) AS amt FROM legal_transaction_ledger AS tl
                LEFT JOIN transaction_payments_logs AS pl ON pl.dataid = tl.refid
                WHERE tl.refid = $ticketid AND tl.typesid = 1200 AND tl.status = 1
                AND pl.dataid IS NULL
            ")->row();


        $total_qty = 0;
        $total_amt = 0;
        $total_vat = 0;
        $total_nvat = 0;
        $total_paid = 0;
        $total_due = 0;


        $total_initdep_amt = 0;
        $total_gdrdep_amt = 0;
        $total_laborserv_amt = 0;
        $total_other_amt = 0;
        $total_num_paid = 0;

        $typesid = 0;
        if($checkdp->amt > 0) {
            $typesid = 1200;
            $topay_typesid = array(1200);
            $sql_ledger = $this->db->select('
                        tl.sysid,
                        tl.refid,
                        tl.typesid,
                        tl.func,
                        tl.amt,
                        coa.codes AS acctno,
                        coa.descs AS acctdesc,
                        tl.payable,
                        tl.payablecnt,
                        tl.payablestartdue,
                        tl.datecreated
                    ')
                ->from('legal_transaction_ledger AS tl')
                ->join('prime_chart_of_accounts AS coa', 'coa.sysid = tl.acctno')
                ->where(array('tl.refid' => $ticketid, 'tl.payable' => 1, 'tl.status' => 1))
                ->where_in('tl.typesid', $topay_typesid)
                ->get();

            if($sql_ledger->num_rows()>0) {
                $i = 1;
                foreach($sql_ledger->result() as $row) {

                    $paid = false;
                    $paid_amt = 0;
                    $vatamt = 0;
                    $amt = 0;
                    $total = 0;
                    $nonvat = 0;




                    $vatamt = $row->amt * 0.12;
                    $total = $row->amt - $paid_amt ;
                    $nonvat = $row->amt - $vatamt;


                    $total_vat += $vatamt;
                    // $total_amt += $total;
                    $total_nvat += $nonvat;


                    if($total > 0) {
                        $vattype = '<i class="fa fa-angle-double-up tooltips" data-placement="left" title="Add Up Vat"></i>';

                        $btn = false;

                        $stats = '';
                        $row_class = '';

                        $input_group = '';
                        $input_group .= '<input type="hidden" name="payable[]" value="'.$total.'" />';

                        $chk = '<input id="checkbox' . $row->sysid . '" class="icheck" type="checkbox" value="' . $total . '" name="chk[' . $row->sysid . ']">';

                        $types_name = get_types_label_format($row->typesid, false, false, false, false, true, true)->text;

                        $acct_no = ''
                            . '<a role="button" '
                            . 'class="popovers" '
                            . 'data-trigger="hover" '
                            . 'data-container="body" '
                            . 'data-placement="right" '
                            . 'data-content="' . $row->acctdesc . '<br>(' . $types_name. ')"'
                            . 'data-original-title="' . $row->acctno . '"><i class="fa fa-search"></i> ' . $row->acctno .'</a>';

                        $input_frtx = '<input style="width: 100%" name="frtx[' . $row->sysid . ']" class="form-control inline input-xs number" placeholder="0.00" />';

                        $data['list'][] = array(
                            'num' => $i++ . $input_group,
                            'acctno' => $acct_no,
                            'acctname' => $row->acctdesc,
                            'vat' => '<span class="value">' . number_format($vatamt, 2) . '</span>' . '<span class="pull-left">' . $vattype . '</span>',
                            'nonvat' => number_format($nonvat, 2),
                            'cwt' => $input_frtx,
                            'total' => number_format($total, 2),
                            'chk' => $chk,
                            'control' => $stats,
                            'rowclass' => $row_class,
                        );
                        $total_qty += 1;
                    }
                }
                $qry = true;
            }
        } else {
            $topay_typesid = array(1202, 1203, 1204, 1205);
            $sql_ledger = $this->db->select('
                        tl.sysid,
                        tl.refid,
                        tl.typesid,
                        tl.func,
                        tl.amt,
                        coa.codes AS acctno,
                        coa.descs AS acctdesc,
                        tl.payable,
                        tl.payablecnt,
                        tl.payablestartdue,
                        tl.datecreated
                    ')
                ->from('legal_transaction_ledger AS tl')
                ->join('prime_chart_of_accounts AS coa', 'coa.sysid = tl.acctno')
                ->where(array('tl.refid' => $ticketid, 'tl.payable' => 1, 'tl.status' => 1))
                ->where_in('tl.typesid', $topay_typesid)
                ->get()->row();
            if($sql_ledger) {

                $typesid = $sql_ledger->typesid;

                $qry_staggered = $this->db->query("SELECT 
                    lts.sysid,
                    lts.amt,
                    lts.years,
                    lts.months,
                    lts.duedate,
                    tpl.totalamt,
                    tpl.payform,
                    tpl.datecreated, 
                    tpl.payform
                    FROM legal_transaction_staggered AS lts
                    LEFT JOIN transaction_payments_logs AS tpl ON tpl.refid = lts.sysid
                    WHERE lts.trnid = {$sql_ledger->sysid}
                    AND lts.status = 1
                ");

                if($qry_staggered->num_rows()>0) {
                    foreach($qry_staggered->result() as $srow) {

                        if($srow->totalamt < 1) {
                            // convert timestamp back to date string
                            $date = $srow->duedate;
                            $date_due_time = strtotime($date . ' 00:00:00');
                            $date_now_time = time();
                            $due = false;

                            $paid = false;
                            $paid_amt = 0;
                            $vatamt = 0;
                            $amt = 0;
                            $total = 0;
                            $nonvat = 0;


                            $vatamt = $srow->amt * 0.12;
                            $total = $srow->amt - $paid_amt;
                            $nonvat = $srow->amt - $vatamt;


                            if ($total > 0) {
                                $vattype = '<i class="fa fa-angle-double-up tooltips" data-placement="left" title="Add Up Vat"></i>';

                                $btn = false;

                                $stats = '';
                                $total_amt += $total;

                                if ($date_now_time >= $date_due_time) {
                                    $due = true;
                                    $total_due += $total;
                                }


                                $input_group = '';
                                $row_class = '';
                                if ($due == true) {
                                    $stats = '<span class="label label-danger"><i class="fa fa-check"></i></span>';
                                    $total_vat += $vatamt;
                                    $total_amt += $total;
                                    $total_nvat += $nonvat;
                                    $row_class = 'row-danger payable';
                                    $stats .= '<input type="hidden" name="payable[' . $srow->sysid . ']" value="' . $total . '" class="" />';
                                } else {
                                    $stats = '<input type="checkbox" name="payable[' . $srow->sysid . ']" value="' . $total . '" class="icheck" id="check_payadd" />';
                                }

                                $chk = '<input id="checkbox' . $srow->sysid . '" class="icheck" type="checkbox" value="' . $total . '" name="chk[' . $srow->sysid . ']">';

                                $types_name = get_types_label_format($sql_ledger->typesid, false, false, false, false, true, true)->text;

                                $acct_no = ''
                                    . '<a role="button" '
                                    . 'class="popovers" '
                                    . 'data-trigger="hover" '
                                    . 'data-container="body" '
                                    . 'data-placement="right" '
                                    . 'data-content="' . $sql_ledger->acctdesc . '<br>(' . $types_name . ')"'
                                    . 'data-original-title="' . $sql_ledger->acctno . '"><i class="fa fa-search"></i> ' . $sql_ledger->acctno . '</a>';

                                $input_frtx = '<input style="width: 100%" name="frtx[' . $srow->sysid . ']" class="form-control inline input-xs number" placeholder="0.00" />';

                                $data['list'][] = array(
                                    'num' => $date . $input_group,
                                    'acctno' => $acct_no,
                                    'acctname' => $sql_ledger->acctdesc,
                                    'vat' => '<input type="hidden" name="vatarr[' . $srow->sysid . ']" value="' . $vatamt . '"/><span class="value">' . number_format($vatamt, 2) . '</span>' . '<span class="pull-left">' . $vattype . '</span>',
                                    'nonvat' => number_format($nonvat, 2),
                                    'cwt' => $input_frtx,
                                    'total' => number_format($total, 2),
                                    'chk' => $chk,
                                    'control' => $stats,
                                    'rowclass' => $row_class,
                                );
                            }

                            $total_qty += 1;
                        }
                    }
                    if($total_qty>0) {
                        $qry = true;
                    }
                }
            } else {
                if($sql_ledger) {

                    $typesid = $sql_ledger->typesid;

                    $paid       = false;
                    $paid_amt   = 0;
                    $vatamt     = 0;
                    $amt        = 0;
                    $total      = 0;
                    $nonvat     = 0;


                    $vatamt = $sql_ledger->amt * 0.12;
                    $total = $sql_ledger->amt - $paid_amt;
                    $nonvat = $sql_ledger->amt - $vatamt;


                    if ($total > 0) {
                        $vattype = '<i class="fa fa-angle-double-up tooltips" data-placement="left" title="Add Up Vat"></i>';

                        $btn = false;

                        $stats = '';

                        $stats = '<span class="label label-danger"><i class="fa fa-check"></i></span>';
                        $total_vat += $vatamt;
                        $total_amt += $total;
                        $total_nvat += $nonvat;
                        $row_class = 'row-danger payable';

                        $input_group = '';
                        $input_group .= '<input type="hidden" name="payable[]" value="'.$total.'" />';

                        $chk = '<input id="checkbox' . $sql_ledger->sysid . '" class="icheck" type="checkbox" value="' . $total . '" name="chk[' . $sql_ledger->sysid . ']">';

                        $types_name = get_types_label_format($sql_ledger->typesid, false, false, false, false, true, true)->text;

                        $acct_no = ''
                            . '<a role="button" '
                            . 'class="popovers" '
                            . 'data-trigger="hover" '
                            . 'data-container="body" '
                            . 'data-placement="right" '
                            . 'data-content="' . $sql_ledger->acctdesc . '<br>(' . $types_name . ')"'
                            . 'data-original-title="' . $sql_ledger->acctno . '"><i class="fa fa-search"></i> ' . $sql_ledger->acctno . '</a>';

                        $input_frtx = '<input style="width: 100%" name="frtx[' . $sql_ledger->sysid . ']" class="form-control inline input-xs number" placeholder="0.00" />';


                        $data['list'][] = array(
                            'num' => 0  . $input_group,
                            'acctno' => $acct_no,
                            'acctname' => $sql_ledger->acctdesc,
                            'vat' => '<span class="value">' . number_format($vatamt, 2) . '</span>' . '<span class="pull-left">' . $vattype . '</span>',
                            'nonvat' => number_format($nonvat, 2),
                            'cwt' => $input_frtx,
                            'total' => number_format($total, 2),
                            'chk' => $chk,
                            'control' => $stats,
                            'rowclass' => $row_class,
                        );
                        $total_qty += 1;

                        $qry = true;
                    }
                }
            }
        }


        $balance = $total_amt - $total_paid;
        $data['cashbalance'] = $total_amt;
        $data['totalamount'] = $total_amt;

        $data['fullname'] = 'N/A';
        $data['addr'] = 'N/A';

        $data['qty'] = number_format($total_qty);
        $data['servamt'] = number_format($total_amt,2);
        $data['total'] = number_format($total_amt,2);
        $data['totalvat'] = number_format($total_vat,2);
        $data['totalnvat'] = number_format($total_nvat,2);
        $data['totalpaid'] = number_format($total_paid,2);
        $data['balance'] = ($balance > 0) ? number_format($balance,2) : '<span class="label label-success"><i class="fa fa-check fa-fw"></i> Settled</span>';
        $data['balance'] = $total_due;

        $data['initdepamt'] = number_format($total_initdep_amt,2);
        $data['gdrdepamt'] = number_format($total_gdrdep_amt,2);
        $data['laborservamt'] = number_format($total_laborserv_amt,2);
        $data['otheramt'] = number_format($total_other_amt,2);
        $userinfo = get_users_info(0, true);
        $data['dataid'] = $ticketid;
        $data['printedby'] = ($userinfo) ? $userinfo->lastname.', '.$userinfo->firstname : 'N/A';
        $data['dateprinted'] = sql_time()->DATETIME;
        $data['totalpaid'] = $total_num_paid;
        $data['typesid'] = $typesid;

        $data['settled'] = ($balance > 0) ? false : true;
        $data['arbtn'] = '';
        $data['qry'] = $qry;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_inspectors(){
        $data = array();
        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname")
            ->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_positions as pemjc" , "pemjc.emp_id = pem.sysid" , "left")
            ->where(array(
                "pem.status" => 1 ,
                "pemjc.position_id" => 109 ,
                "pemjc.status" => 1
            ))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname.', '.' - '.$row->firstname
                );
            }
        }
        return json_encode($data);
    }

    function get_banksid(){
        $data = array();

        $sql = $this->db->select("sysid , codes , names")->from("prime_types_parameter")
            ->where(array("status" => 1 , "codes" => 'BANK'))->get();
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
    function submit_staggered(){
        $data = array();
        $amt = $this->input->post('amt');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $duedate = $this->input->post('duedatepayment');
        $refid = $this->input->post('refidstaggered');
        $qry = false;
        $data['amt'] = $amt;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['duedate'] = $duedate;
        $data['refid'] = $refid;
        $this->db->trans_begin();
        $insarr = array(
            'trnid' => $refid,
            'months' => $month,
            'years' => $year,
            'amt' => $amt,
            'duedate' => $duedate,
        );
        $sql = $this->db->insert("legal_transactions_staggered" , $insarr);

        $ticket_trail_arr = array(
            'ticketid' => $refid,
            'codes' => 'LEGALAMT',
            'descs' => 'LEGAL - Insert Staggered Transaction | Month: '.$month.' | Year: '.$year.' | Amount: '.$amt,
            'statusid' => 307,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

        $data['inserterr'] = $this->db->_error_message();
        if($this->db->trans_status()=== TRUE && $sql){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Amount has been added!';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Unable to add this account!';
            $func = 'error';
        }


        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }
    function get_staggered_trans(){
        $data = array();
        $ticketid = $this->input->post('ticketid');
        $typesid = $this->input->post('typesid');

        $sql = $this->db->select()->from("legal_transactions_staggered")
            ->where(array("trnid" => $ticketid , "status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['staggereddata'][] = array(
                    'month' => $row->months,
                    'year' => $row->years,
                    'duedate' => $row->duedate,
                    'amt' => $row->amt,
                    'paid' => '',
                    'paiddate' => 'N/A',
                    'status' => $row->status,
                    'control' => '<button data-id="'.$row->sysid.'" id="deletestaggeredpayment" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button>'
                );
            }
        }
        $data['typesid'] = $typesid;
        return json_encode($data);
    }
    function delete_staggered_payment(){
        $data = array();

        $stagid = $this->input->post('stagid');
        $refid = $this->input->post('refid');
        $this->db->trans_begin();

        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $stagid));
        $sql = $this->db->update("legal_transactions_staggered" , $updatearr);

        $ticket_trail_arr = array(
            'ticketid' => $refid,
            'codes' => 'LEGALAMT',
            'descs' => 'LEGAL - Delete Staggered ID: '.$stagid,
            'statusid' => 307,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

        if($this->db->trans_status()=== TRUE && $sql){
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Transaction has been deleted.';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Unable to delete this transaction!';
            $func = 'error';
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

    function get_flexi_payment() {
        $data = array();
        $refid = $this->input->post('refid');
        $sql = $this->db->query("SELECT 
                lts.sysid,
                lts.amt,
                lts.years,
                lts.months,
                lts.duedate,
                tpl.totalamt,
                tpl.payform,
                tpl.datecreated, 
                tpl.payform
                FROM legal_transaction_ledger AS ltl
                INNER JOIN legal_transaction_staggered AS lts ON lts.trnid = ltl.sysid 
                LEFT JOIN transaction_payments_logs AS tpl ON tpl.refid = lts.sysid
                WHERE ltl.refid = $refid AND ltl.payable = 1 AND ltl.typesid != 1200
                AND ltl.status = 1
            ");
        if($sql->num_rows()>0) {
            $num = 1;
            foreach($sql->result() as $row) {
                $paid_date =  '<code>N/A</code>';
                $paid_status = '<span class="label label-danger">Unpaid</span>';
                if($row->totalamt > 0) {
                    $paid_form = ($row->payform == 2) ? '<span class="label label-info pull-right">Cheque</span>' : '';
                    $paid_date = $row->datecreated;
                    $paid_status = '<span class="label label-success">Paid</span>' . $paid_form;
                }
                $data['list'][] = array(
                    'num' => $num++,
                    'year' => $row->years,
                    'month' => $row->months,
                    'duedate' => $row->duedate,
                    'amount' => number_format($row->amt,2),
                    'datepaid' => $paid_date,
                    'status' => $paid_status,
                    'control' => '<a href="javascript:;" id="btn_delete_flexipay_item" class="btn btn-danger inline"><i class="fa fa-times"></i></a>'
                );
            }
        }
        return json_encode($data);
    }
}
