<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/9/2018
 * Time: 11:02 AM
 */

class Model_cwdo extends CI_model
{

    function get_acct_info() {
        $data = array();
        $servno = $this->input->post('servno');
        $mtr = $this->input->post('mtr');
        $qry = $this->db->select('sysid')
            ->from('customer_accounts_main')
            ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
            ->get()->row();
        if($qry) {
            $data['acctid'] = $qry->sysid;
        }
        return json_encode($data);
    }

    function save_new_ticket() {
        $data = array();
        $msg = '';
        $func = 'warning';
        $dataid = 0;
        $bill = $this->input->post('bill');
        if($bill) {
            $bills = implode(',', $bill);
        }else{
            $bills = NULL;
        }


        $ins_arr = array(
            'acctid' => $this->input->post('acctid'),
            'complainants' => $this->input->post('complainants'),
            'lastname' => $this->input->post('lastname'),
            'firstname' => $this->input->post('firstname'),
            'middlename' => $this->input->post('middlename'),
            'address' => $this->input->post('address'),
            'contact' => $this->input->post('contact'),
            'remarks' => $this->input->post('remarks'),
            'tickettype' => $this->input->post('tickettype'),
            'ticketpart' => $this->input->post('ticketpart'),
            'priority' => $this->input->post('priority'),
            'reqverification' => $this->input->post('reqverification'),
            'orno' => $this->input->post('orno'),
            'empid' => $this->input->post('empid'),
            'empcomp' => $this->input->post('empcomp'),
            'bill' => $bills,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );

        $this->db->trans_begin();

        $qry_check = $this->db->select()->from('ticketing_details_logs')
            ->where(array('acctid' => $this->input->post('acctid')))
            ->get()->row();

        if($qry_check) {
            if ($qry_check->status != 300 && $qry_check->status != 314) {
                $msg = 'There is a on-going ticket for this type of complaints!';
                $func = 'warning';
            } else {

                $this->db->where(array('acctid' => $this->input->post('acctid'), 'tickettype' => $this->input->post('tickettype'), 'status' => 300));
                $this->db->update('ticketing_details_logs', array('status' => 0, 'updatedby' => user_id()));


                $this->db->insert('ticketing_details_logs', $ins_arr);
                $dataid = $this->db->insert_id();
                $data['err'] = $this->db->_error_message();
                $func = 'success';
                $msg = 'New Ticket has been created!';

            }
        }else{
            $this->db->insert('ticketing_details_logs', $ins_arr);
            $dataid = $this->db->insert_id();
            $data['err'] = $this->db->_error_message();
            $func = 'success';
            $msg = 'New Ticket has been created!';
        }

        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }


        $data['input'] = $ins_arr;
        $data['title'] = 'New Ticket';
        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }

    function acct_search() {
        $data = array();
        $input = $this->input->post();



        $acctid = $this->input->post('acctid');

        if($acctid) {
            $qry = $this->db->select('
                m.sysid,
                m.servicenumber AS servno,
                a.addrspecific AS addr,
                m.types,
                m.ownerid
            ')
                ->from('customer_accounts_main AS m')
                ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
                ->where(array('m.sysid' => $acctid, 'm.mtr' => 1))
                ->get()->row();

            if ($qry) {
                $pic = base_url('assets/global/img/person_default.jpg');
                $name = '@TODO';
                if ($qry->types == 5) {
                    $qry_legacy = $this->db->select("name")
                        ->from('customer_accounts_name_legacy')
                        ->where("sysid", $qry->ownerid)
                        ->get()->row();
                    if ($qry_legacy) {
                        $name = $qry_legacy->name;
                    }
                }
                $data = array(
                    'acctid' => $qry->sysid,
                    'servno' => $qry->servno,
                    'name' => $name,
                    'address' => $qry->addr,
                );
            }
        }

        $data['input'] = $input;
        return json_encode($data);
    }

    function get_ticket_history() {
        $data = array();
        $acctid = $this->input->post('acctid');
        $qry = $this->db->select('tdl.sysid, tp.desc, tpr.descs AS particular, tdl.remarks, tdl.createdby, tdl.updatedby, tdl.datecreated, tdl.dateupdated, tdl.status')
            ->from('ticketing_details_logs AS tdl')
            ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype')
            ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart')
            ->where(array('tdl.acctid' => $acctid, 'tdl.status > ' => 0))
            ->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'ticketno' => str_pad($row->sysid, 8, '0', STR_PAD_LEFT),
                    'complaints' => $row->desc,
                    'particular' => $row->particular,
                    'remarks' => $row->remarks,
                    'createdby' => get_users_info($row->createdby)->username,
                    'datecreated' => $row->datecreated,
                    'status' => get_types_label_format($row->status, false, true, 'top'),
                    'control' => '<a href="'.base_url('module/08a35293e09f508494096c1c1b3819edb9df50db/view/').$row->sysid.'" class="btn btn-default btn-xs inline"><i class="fa fa-search"></i></a>',
                );
            }
        }

        return json_encode($data);
    }

    function get_ticket_list() {
        $data = array();
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


        $ts_arr = array();
        $cwd_arr = array();

        $followup_cnt = 0;
        $troublecall_cnt = 0;






        if ($comp == 'ts') {
            $searching = false;
            $person_ids = array();


            // #######################################################################
            // GET PERSON ID ARRAY
            if($search == 1) {
                if($sname && trim($sname) != '') {
                    $qry_person_ids = $this->db->select('sysid')
                        ->from('person')
                        ->or_like('lastname', $sname)
                        ->or_like('firstname', $sname)
                        ->get();
                    if ($qry_person_ids->num_rows() > 0) {
                        foreach ($qry_person_ids->result() as $row) {
                            $person_ids[] = $row->sysid;
                        }
                        $searching = true;
                    }
                }
            }
            // #######################################################################


            // #######################################################################
            // FILTER TICKET TYPES FOR TS ONLY
            $query_ts = $this->db->select('sysid')->from('prime_types_parameter')
                ->where(array('codes' => 'TS', 'status' => 1))
                ->get();
            if ($query_ts->num_rows() > 0) {
                foreach ($query_ts->result() as $tsrow) {
                    $ts_arr[] = $tsrow->sysid;
                }
            }
            $this->db->where_in('tdl.tickettype', $ts_arr);
            // #######################################################################

            $data['TSQ'] = true;
            $data['TSQARR'] = $ts_arr;



            // #######################################################################
            // FILTER SEARCH BASE ON PERSON IDS ARRAY
            if ($searching) {
                $this->db->where_in('tdl.complainants', $person_ids);
                if ($saddr) {
                    $this->db->like('tdl.address', $saddr);
                }
            } else {
                if ($search == 1 && $saddr != '') {
                    if ($saddr) {
                        $this->db->like('tdl.address', $saddr);
                    }
                }
            }
            // #######################################################################


            // #######################################################################
            // FILTER STATUS
            if ($status && $status > 0 && $searching == false) {
                switch ($status) {
                    case 1:
                        $status_arr = array(307, 300, 309, 311, 361, 364, 376, 377, 1015, 1016, 1017, 1018);
                        $this->db->where_in('tdl.status', $status_arr);
                        $data['status'] = 'TS';
                        break;
                    case 300:
                        $this->db->where_in('tdl.status', ts_status_pending_where());
                        $data['status'] = 'PENDING';
                        break;
                    default:
                        $this->db->where('tdl.status', $status);
                        $data['status'] = $status;
                }
            }
            // #######################################################################



            // #######################################################################
            // FILTER DATE
            if($datefilter == 1) {
                if($filteryear && $filteryear > 0) {
                    $this->db->where('YEAR(tdl.datecreated) = ', $filteryear);
                    if($filtermonth && $filtermonth > 0) {
                        $this->db->where('MONTH(tdl.datecreated) = ', $filtermonth);
                        if($filterday && $filterday > 0) {
                            $this->db->where('DAY(tdl.datecreated) = ', $filterday);
                        }
                    }
                }
            }
            // #######################################################################


            $limits = $this->input->post('limit');
            if ($limits && $limits > 0) {
                $this->db->limit($limits);
            }

        } else {

            // #######################################################################
            // FILTER TICKET TYPES FOR TS ONLY
            $query_ts = $this->db->select('sysid')->from('prime_types_parameter')
                ->where(array('codes' => $comp, 'status' => 1))
                ->get();
            if ($query_ts->num_rows() > 0) {
                foreach ($query_ts->result() as $tsrow) {
                    $ts_arr[] = $tsrow->sysid;
                }
            }
            $this->db->where_in('tdl.tickettype', $ts_arr);
            // #######################################################################
            $data['TYPES'] = $ts_arr;
        }

        $qry = $this->db->select('
            tdl.sysid, 
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
            p.lastname
        ')
            ->from('ticketing_details_logs AS tdl')
            ->join('person AS p', 'p.sysid = tdl.complainants', 'left')
            ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype', 'left')
            ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart', 'left')
            ->join('address_barangay AS ab', 'ab.sysid = tdl.barangays', 'left')
            //->join('customer_accounts_main AS am', 'am.sysid = tdl.acctid', 'left')
            //->join('customer_accounts_address AS a', 'a.acctid = am.sysid', 'left')
            ->where(array('tdl.status > ' => 0))
            ->get();

        /*
         * SELECT `tdl`.`sysid`, `tp`.`names` AS tcname, `tpr`.`descs` AS particular, `tdl`.`repsource`, `tdl`.`complainants`, `tdl`.`remarks`, `tdl`.`tickettype`, `tdl`.`createdby`, `tdl`.`updatedby`, `tdl`.`datecreated`, `tdl`.`dateupdated`, `tdl`.`status`, `tdl`.`reqverification`, `tdl`.`contact`, `tdl`.`address`, `tdl`.`district`, `tdl`.`barangays`, `ab`.`texts` AS brgyname, `tdl`.`landmarks`, `tdl`.`compname`, `tdl`.`etc`, `p`.`firstname`, `p`.`middlename`, `p`.`lastname ` -- am.types, `--` am.servicenumber AS servno, `--` am.mtr, `--` a.addrspecific AS addr, `--` am.types, `--` am.ownerid, `--` am.mtrno, `--` am.mtrserial FROM (`ticketing_details_logs` AS tdl) LEFT JOIN `person` AS p ON `p`.`sysid` = `tdl`.`complainants` LEFT JOIN `prime_types_parameter` AS tp ON `tp`.`sysid` = `tdl`.`tickettype` LEFT JOIN `ticketing_particular` AS tpr ON `tpr`.`sysid` = `tdl`.`ticketpart` LEFT JOIN `address_barangay` AS ab ON `ab`.`sysid` = `tdl`.`barangays` WHERE `tdl`.`tickettype` IN ('365', '366', '367', '368', '369', '370', '378', '379', '380', '381', '382', '383', '384', '385', '386', '387', '388', '1004', '1005', '1027', '1044') AND `tdl`.`status` IN (307, 300, 309, 311, 361, 364, 376, 377, 1015, 1016, 1017, 1018) AND `tdl`.`status` > 0 LIMIT 50
         *
         */


        if($qry->num_rows()>0) {

            if($comp=='ts') {

                if($searching == false) {
                    if ($status && $status > 0) {
                        switch ($status) {
                            case 1:
                                $status_arr = array(307, 300, 309, 311, 361, 364, 376, 377, 1015, 1016, 1017, 1018);
                                $this->db->where_in('lg.status', $status_arr);
                                $data['status'] = 'TS';
                                break;
                            case 300:
                                $this->db->where_in('lg.status', ts_status_pending_where());
                                $data['status'] = 'PENDING';
                                break;
                            default:
                                $this->db->where('lg.status', $status);
                                $this->db->order_by('lg.dateupdated', 'desc');
                                $data['status'] = $status;
                        }
                    }

                    if($datefilter == 1) {
                        if($filteryear && $filteryear > 0) {
                            $this->db->where('YEAR(lg.datecreated) = ', $filteryear);
                            if($filtermonth && $filtermonth > 0) {
                                $this->db->where('MONTH(lg.datecreated) = ', $filtermonth);
                                if($filterday && $filterday > 0) {
                                    $this->db->where('DAY(lg.datecreated) = ', $filterday);
                                }
                            }
                        }
                    }


                    if ($limits && $limits > 0) {
                        $this->db->limit($limits);
                    }
                    $qry_group = $this->db->select('
                        lg.sysid,
                        lg.typesid,
                        lg.equipid,
                        lg.findingsid,
                        lg.circuitid,
                        lg.teamid,
                        lg.datecreated,
                        lg.dateupdated,
                        lg.createdby,
                        lg.updatedby,
                        lg.etc,
                        lg.status
                    ')
                        ->from('ticketing_details_logs_group AS lg')
                        ->join('ticketing_details_logs_group_matrix AS lgm', 'lgm.groupid = lg.sysid', 'left')
                        ->where(array('lgm.status > ' => 0))
                        ->group_by('lg.sysid, lg.typesid, lg.equipid, lg.findingsid, lg.circuitid, lg.teamid, lg.datecreated,lg.dateupdated, lg.createdby, lg.updatedby, lg.status')
                        ->get();

                    if ($qry_group->num_rows() > 0) {

                        foreach ($qry_group->result() as $grow) {
                            $etctext = '';

                            $troublecall_cnt += 1;

                            $etc_input = '';
                            $etc_input .= '<div class="input-group inline">';
                            $etc_input .= '<input value="' . $grow->etc . '" id="etc_input" class="form-control inline tooltips" title="Estimated Time of Completion" data-placement="right" placeholder="ETC.. (min)" type="text" />';
                            $etc_input .= '<span class="input-group-addon">min.</span>';
                            $etc_input .= '</div>';

                            $tcequipments = '';
                            $tcfindings = '';
                            $tccircuit = '';
                            $tcteam = '';

                            $teamid = $grow->teamid;

                            //$time = $grow->datecreated . ' | ' . time_elapsed_diff($grow->datecreated, sql_time()->DATETIME, false);
                            $time = $grow->datecreated . '<br><small class="text-info">' . timeago($grow->datecreated, sql_time()->DATETIME).'</small>';


                            $tcequipments_id = $grow->equipid;
                            $tcequipments_name = 'None';
                            $qry_equipment = $this->db->select()
                                ->from('prime_types_parameter')
                                ->where(array('sysid' => $grow->equipid))
                                ->get()->row();
                            if ($qry_equipment) {
                                $tcequipments_name = ($qry_equipment->desc != '') ? $qry_equipment->desc : $qry_equipment->names;
                            }

                            $findings_id = $grow->findingsid;
                            $findings_name = null;
                            $qry_findings = $this->db->select()
                                ->from('prime_types_parameter')
                                ->where(array('sysid' => $grow->findingsid))
                                ->get()->row();
                            if ($qry_findings) {
                                $findings_name = ($qry_findings->desc != '') ? $qry_findings->desc : $qry_findings->names;
                            }


                            // CIRCUIT LEVEL
                            $circuit_level_input = false;
                            $circuit_level_name = 'N/A';
                            $circuit_level_id = '';
                            if ($grow->circuitid > 0) {
                                $circuit_level_id = $grow->circuitid;
                                $check_circuit_mtrx = $this->db->select()->from('ticketing_outage_matrix_circuit_level')
                                    ->where('typesid', $tcequipments_id)->get()->row();
                                if ($check_circuit_mtrx) {

                                    $circuit_level_input = true;
                                    $qry_circuit = $this->db->select()
                                        ->from('prime_types_parameter')
                                        ->where(array('sysid' => $grow->circuitid))
                                        ->get()->row();
                                    if ($qry_circuit) {
                                        $circuit_level_id = $qry_circuit->sysid;
                                        $circuit_level_name = ($qry_circuit->desc != '') ? $qry_circuit->desc : $qry_circuit->names;
                                    }
                                }
                            }

                            if ($grow->status == 314) {
                                $checkbox = '';
                                $tcequipments = '<code>' . $tcequipments_name . '</code>';
                                $tcfindings = '<code>' . $findings_name . '</code>';
                                $tccircuit = '<code>' . $circuit_level_name . '</code>';
                                $tcteam = get_types_label_format($grow->teamid, false, true, 'top', base_url('module/d321d6f7ccf98b51540ec9d933f20898af3bd71e/view/' . $grow->teamid));
                                $etctext = $grow->etc . ' min.';
                            } else {
                                if (ts_status_pending($grow->status) || $grow->status == 1007 || $grow->status == 1025 || $grow->status == 1028) {
                                    $checkbox = '<input type="checkbox" class="icheck" name="trouble[]" value="' . $grow->sysid . '"/>';
                                    if ($grow->status == 300) {
                                        $tcequipments = 'Not Assign';
                                        $tcfindings = 'Not Assign';
                                        $tccircuit = 'Not Assign';
                                        $etctext = 'Not Assign';
                                    } else {
                                        if ($int == 1) {
                                            $tcequipments .= '<input type="hidden" class="form-control inline" id="select2_equipments" name="equipments[]" value="' . $tcequipments_id . '" />';
                                            $tcequipments .= get_types_label_format($tcequipments_id, false, true, 'top', 'javascript:;', false, false);
                                            $tcfindings .= '<input type="hidden" class="form-control inline" id="select2_findings" name="findings[]" value="' . $findings_id . '" />';
                                            $tcfindings .= get_types_label_format($findings_id, false, true, 'top', 'javascript:;', false, false);
                                            $etctext = $etc_input;
                                        } else {
                                            $tcequipments = get_types_label_format($tcequipments_id);
                                            $tcfindings = get_types_label_format($findings_id);
                                            $etctext = $grow->etc . ' min.';
                                        }
                                        if ($int == 1) {
                                            $tccircuit .= '<input type="hidden" class="form-control inline" placholder="Circuit Level.." id="select2_circuitlevel" name="circuitlevel[]" value="' . $circuit_level_id . '" />';
                                            $tccircuit .= get_types_label_format($circuit_level_id, false, true, 'top', 'javascript:;', false, false);

                                        } else {
                                            $tccircuit = get_types_label_format($circuit_level_id);
                                        }


                                    }

                                    if ($int == 1) {
                                        $tcteam = '<input type="hidden" class="form-control inline" id="select2_teams" name="teams[]" value="' . $teamid . '" />';
                                        $tcteam .= get_types_label_format($teamid, false, true, 'top', 'javascript:;', false, false);
                                    } else {
                                        $tcteam = get_types_label_format($teamid);
                                    }
                                }
                            }
                            $status = '';
                            if ($grow->status == 314) {
                                if (in_array(1, user_info()->roles) || user_id() == 1) {
                                    $status .= '<input type="hidden" class="form-control inline" id="select2_status_adm" name="status[]" value="' . $grow->status . '" />';
                                    $status .= get_types_label_format($grow->status, false, true, 'top', 'javascript:;', false, false);
                                } else {
                                    $status = get_types_label_format($grow->status, false, true, 'top');
                                }
                            } else {
                                if ($int == 1) {
                                    $status .= '<input type="hidden" class="form-control inline" id="select2_status" name="status[]" value="' . $grow->status . '" />';
                                    $status .= get_types_label_format($grow->status, false, true, 'top', 'javascript:;', false, false);
                                } else {
                                    $status = get_types_label_format($grow->status);
                                }
                            }

                            $followup_stat = '';

                            // GET FOLLOWUP NOTIFICATION
                            $qry_followup = $this->db->select('count(dt.ticketid) AS cnt')
                                ->from('ticketing_details_logs_group_matrix AS tg')
                                ->join('ticketing_details_trails AS dt', "dt.ticketid = tg.ticketid AND dt.codes = 'FOLLOWUP'")
                                ->where(array('tg.groupid' => $grow->sysid))
                                ->get()->row();

                            if ($int == 1) {

                                $controls = '';

                                if ($types == 1045 || $types == 1046) {

                                    $controls .= '<a data-id="' . $grow->sysid . '" href="javascript:;" class="btn btn-xs btn-success" id="btn_crw_prw_accomplished"><i class="fa fa-check"></i> Accomplish</a>';
                                } else {
                                    if ($qry_followup->cnt > 0) {
                                        $followup_cnt += $qry_followup->cnt;
                                        $followup_stat = 'danger';
                                        $controls .= '<a href="javascript:;" title="Followup" data-trigger="hover" data-content="There are ' . $qry_followup->cnt . ' followup call!" data-placement="left" class="label label-danger popovers"><i class="icon-flag"></i> ' . $qry_followup->cnt . '</span>';
                                    }
                                }

                            } else {
                                $controls = '';
                                if ($types == 1045 || $types == 1046) {

                                    $controls .= '<a data-id="' . $grow->sysid . '" href="javascript:;" class="btn btn-xs btn-success" id="btn_crw_prw_accomplished"><i class="fa fa-check"></i> Accomplish</a>';
                                } else {
                                    $class_followup_icon = 'btn-success';

                                    $followup_cnt += $qry_followup->cnt;
                                    $cnt_followup = '';
                                    if ($qry_followup->cnt > 0) {
                                        $class_followup_icon = 'btn-danger';
                                        $cnt_followup = '(' . $qry_followup->cnt . ')';
                                    }

                                    if ($grow->status != 314) {

                                        $controls .= '<button id="btn_followup" data-id="' . $grow->sysid . '" class="btn  btn-xs ' . $class_followup_icon . '"><i class="icon-flag "></i> Follow-up ' . $cnt_followup . '</span></button>';
                                    }
                                }
                            }

                            $queue_class = '';
                            if ($int == 1) {
                                $queue = '<i class="fa fa-edit"></i>';
                            } else {
                                $queue = '';
                            }
                            if ($teamid > 0) {
                                $qry_queue = $this->db->select('nums')->from('ticketing_queue')
                                    ->where(array('tcid' => $grow->sysid, 'types' => 2, 'teamid' => $teamid, 'status' => 1))
                                    ->get()->row();
                                if ($qry_queue) {
                                    $queue = $qry_queue->nums;
                                    $queue_class = 'text-bold text-danger';
                                }
                            }
                            if ($int == 1) {
                                $queue_form = ''
                                    . '<form id=\'frm_submit_queue\' action=\'' . base_url('ts/teamqueue') . '\' method=\'post\'>'
                                    . '<input type=\'hidden\' class=\'form-control\' name=\'teamid\' value=\'' . $teamid . '\'>'
                                    . '<input type=\'hidden\' class=\'form-control\' name=\'tcid\' value=\'' . $grow->sysid . '\'>'
                                    . '<input type=\'hidden\' class=\'form-control\' name=\'types\' value=\'2\'>'
                                    . '<div class=\'input-group\'>'
                                    . '<input type=\'text\' class=\'form-control\' name=\'queue\' placeholder=\'Queue Num.\'>'
                                    . '<span class=\'input-group-btn\'><button class=\'btn btn-primary\'>Save</button></span>'
                                    . '</div>'
                                    . '</form>';
                                $a_queue = '<a class="popovers queue-btn ' . $queue_class . '" href="javascript:;" title="Team Queue <button type=\'button\' aria-hidden=\'true\' class=\'close\'> &times;</button>" data-trigger="click" data-content="' . $queue_form . '" data-placement="right" data-original-title="Team Queue">' . $queue . '</a>';
                            } else {
                                $a_queue = '<span class="' . $queue_class . '">' . $queue . '</span>';
                            }

                            $data['list'][] = array(
                                'expand' => $grow->sysid,
                                'num' => '',
                                'queue' => $a_queue,
                                'ticketno' => 'GROUP_' . str_pad($grow->sysid, 4, '0', STR_PAD_LEFT) . '<input type="hidden" id="groupid" value="' . $grow->sysid . '" />',
                                'name' => get_types_label_format($grow->typesid, false, true, 'top', 'javascript:;', false, false),
                                'contact' => '<code>N/A</code>',
                                'address' => '<code>N/A</code>',
                                'time' => $time,
                                'status' => $status,
                                'statusid' => '',
                                'complaints' => 'N/A',
                                'tcequipments' => $tcequipments,
                                'tcequipmentid' => $tcequipments_id,
                                'tcfindings' => $tcfindings,
                                'tcfindingid' => $findings_id,
                                'team' => $tcteam,
                                'teamid' => $grow->teamid,
                                'circuit' => $tccircuit,
                                'circuitid' => $circuit_level_id,
                                'etc' => $etctext,
                                'followup' => $followup_stat,
                                'listtype' => 'group',
                                'control' => $controls,
                            );
                        }
                    }
                }
            } else {




                if($comp == 'JO') {
                    foreach ($qry->result() as $row) {

                    }
                }
            }


            foreach($qry->result() as $row) {
                $etctext = '';
                if($row->complainants>0) {
                    $name = $row->lastname . ', '.$row->firstname . ' ' . $row->middlename;
                }else{
                    $name = $row->compname;
                }

                if($comp=='ts') {
                    $etctext = '';
                    $troublecall_cnt += 1;

                    $etc_input = '';
                    $etc_input .= '<div class="input-group inline">';
                    $etc_input .= '<input value="'.$row->etc.'" id="etc_input" class="form-control inline tooltips" title="Estimated Time of Completion" data-placement="right" placeholder="ETC.. (min)" type="text" />';
                    $etc_input .= '<span class="input-group-addon">min.</span>';
                    $etc_input .= '</div>';

                    $qry_check_group = $this->db->select('groupid')
                        ->from('ticketing_details_logs_group_matrix')
                        ->where(array('status > ' => 0, 'ticketid' => $row->sysid))
                        ->get()->row();
                    if($qry_check_group == false) {

                        $tcequipments_id = null;
                        $tcequipments_name = 'None';
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
                        $findings_name = null;
                        $qry_findings = $this->db->select('lf.findingid, tp.desc, tp.names')
                            ->from('ticketing_details_logs_findings AS lf')
                            ->join('prime_types_parameter AS tp', 'tp.sysid = lf.findingid', 'left')
                            ->where(array('lf.ticketid' => $row->sysid, 'lf.status' => 1))
                            ->get()->row();
                        if ($qry_findings) {
                            $findings_id = $qry_findings->findingid;
                            $findings_name = ($qry_findings->desc != '') ? $qry_findings->desc : $qry_findings->names;
                        }

                        // CIRCUIT LEVEL
                        $circuit_level_input = false;
                        $circuit_level_name = 'N/A';
                        $circuit_level_id = null;
                        $check_circuit_mtrx = $this->db->select()->from('ticketing_outage_matrix_circuit_level')
                            ->where('typesid', $tcequipments_id)->get()->row();
                        if ($check_circuit_mtrx) {
                            $circuit_level_input = true;
                            $qry_circuit = $this->db->select('cl.circuitid, tp.desc, tp.names')
                                ->from('ticketing_details_logs_circuit_level AS cl')
                                ->join('prime_types_parameter AS tp', 'tp.sysid = cl.circuitid', 'left')
                                ->where(array('cl.ticketid' => $row->sysid, 'cl.status' => 1))
                                ->get()->row();
                            if ($qry_circuit) {
                                $circuit_level_id = $qry_circuit->circuitid;
                                $circuit_level_name = ($qry_circuit->desc != '') ? $qry_circuit->desc : $qry_circuit->names;
                            }
                        }


                        $get_assignment = $this->db->select('da.typesid')
                            ->from('ticketing_details_assignments AS da')
                            ->join('ticketing_details_assignments_group AS dag', 'dag.sysid = da.groupid')
                            ->where(array('da.status > ' => 0, 'da.ticketid' => $row->sysid))
                            ->order_by('da.datecreated', 'desc')
                            ->get()->row();


                        $teamid = '';
                        $team = '';
                        $tcequipments = '';
                        $tcfindings = '';
                        $circuit_level = '';
                        $checkbox = '';

                        if ($get_assignment) {
                            $teamid = $get_assignment->typesid;
                        }

                        if ($row->status == 314) {
                            $checkbox = '';
                            $tcequipments = '<code>' . $tcequipments_name . '</code>';
                            $tcfindings = '<code>' . $findings_name . '</code>';
                            $circuit_level = '<code>' . $circuit_level_name . '</code>';
                            $etctext = $row->etc . ' min.';

                            $team = get_types_label_format($teamid, false, true, 'top', base_url('module/d321d6f7ccf98b51540ec9d933f20898af3bd71e/view/' . $teamid));
                        } else {
                            if ( ts_status_pending($row->status) || $row->status == 1007 || $row->status == 1025 || $row->status == 1028) {
                                $checkbox = '<input type="checkbox" class="icheck" name="trouble[]" value="' . $row->sysid . '"/>';
                                if ($row->status == 300) {
                                    $tcequipments = 'Not Assign';
                                    $tcfindings = 'Not Assign';
                                    $circuit_level = 'Not Assign';
                                    $etctext = 'Not Assign';
                                } else {
                                    if ($int == 1) {
                                        $tcequipments .= '<input type="hidden" class="form-control inline" id="select2_equipments" name="equipments[]" value="' . $tcequipments_id . '" />';
                                        $tcequipments .= get_types_label_format($tcequipments_id, false, true, 'top', 'javascript:;', false, false);
                                        $tcfindings .= '<input type="hidden" class="form-control inline" id="select2_findings" name="findings[]" value="' . $findings_id . '" />';
                                        $tcfindings .= get_types_label_format($findings_id, false, true, 'top', 'javascript:;', false, false);
                                        $etctext = $etc_input;
                                    } else {
                                        $tcequipments = get_types_label_format($tcequipments_id);
                                        $tcfindings = get_types_label_format($findings_id);
                                        $etctext = $row->etc . ' min.';
                                    }

                                    if ($int == 1) {
                                        $circuit_level .= '<input type="hidden" type="hidden" class="form-control inline" placholder="Circuit Level.." id="select2_circuitlevel" name="circuitlevel[]" value="' . $circuit_level_id . '" />';
                                        $circuit_level .= get_types_label_format($circuit_level_id, false, true, 'top', 'javascript:;', false, false);

                                    } else {
                                        $circuit_level = get_types_label_format($circuit_level_id);
                                    }


                                }

                                if ($int == 1) {
                                    $team .= '<input type="hidden" class="form-control inline" id="select2_teams" name="teams[]" value="' . $teamid . '" />';
                                    $team .= get_types_label_format($teamid, false, true, 'top', 'javascript:;', false, false);
                                } else {
                                    $team = get_types_label_format($teamid);
                                }
                            }
                        }

                        $address = get_district_name($row->district) . '<br>' . get_landmarks_multiple($row->landmarks);

                        //$time = $row->datecreated . ' | ' . time_elapsed_diff($row->datecreated, sql_time()->DATETIME, false);
                        $time = $row->datecreated . '<br><small class="text-info">' . timeago($row->datecreated, sql_time()->DATETIME).'</small>';


                        $status = '';
                        if ($row->status == 314) {
                            if(user_id() == 1) {
                                $status .= '<input type="hidden" class="form-control inline" id="select2_status_adm" name="status[]" value="' . $row->status . '" />';
                                $status .= get_types_label_format($row->status, false, true, 'top', 'javascript:;', false, false);
                            }else {
                                $user_role_arr = get_users_roles_matrix_id_arr();
                                if(in_array(1, $user_role_arr)) {
                                    $status .= '<input type="hidden" class="form-control inline" id="select2_status_adm" name="status[]" value="' . $row->status . '" />';
                                    $status .= get_types_label_format($row->status, false, true, 'top', 'javascript:;', false, false);
                                }else {
                                    $status = get_types_label_format($row->status, false, true, 'top');
                                }
                            }
                        } else {

                            if ($int == 1) {
                                $status .= '<input type="hidden" class="form-control inline" id="select2_status" name="status[]" value="' . $row->status . '" />';
                                $status .= get_types_label_format($row->status, false, true, 'top', 'javascript:;', false, false);

                            } else {
                                $status = get_types_label_format($row->status);
                            }
                        }

                        $followup_stat = '';

                        // GET FOLLOWUP NOTIFICATION
                        $qry_followup = $this->db->select('count(ticketid) AS cnt')
                            ->from('ticketing_details_trails')
                            ->where(array('codes' => 'FOLLOWUP', 'ticketid' => $row->sysid))
                            ->get()->row();


                        $controls = '';

                        if ($int == 1) {
                            if($types==1045 || $types==1046) {

                                $controls .= '<a data-id="'.$row->sysid.'" href="javascript:;" class="btn btn-xs btn-success" id="btn_crw_prw_accomplished"><i class="fa fa-check"></i> Accomplish</a>';
                            } else {
                                $controls .= '<a href="#form_edit_ticket" title="TC No.: '.str_pad($row->sysid, 8, '0', STR_PAD_LEFT).'" data-arr="'.$row->sysid.'" data-toggle="ajax-modal" class="btn btn-warning btn-xs inline"><i class="fa fa-pencil"></i></a>';

                                if($qry_followup->cnt > 0) {

                                    $followup_cnt += $qry_followup->cnt;

                                    $followup_stat = 'danger';

                                    $controls .= '<a href="javascript:;" title="Followup" data-trigger="hover" data-content="There are ' . $qry_followup->cnt . ' followup call!" data-placement="left" class="label label-danger popovers"><i class="icon-flag"></i> ' . $qry_followup->cnt . '</span>';

                                }
                            }

                        } else {
                            if($types==1045 || $types==1046) {
                                $controls .= '<a data-id="'.$row->sysid.'" href="javascript:;" class="btn btn-xs btn-success" id="btn_crw_prw_accomplished"><i class="fa fa-check"></i> Accomplish</a>';
                            } else {

                                $followup_cnt += $qry_followup->cnt;

                                $class_followup_icon = 'btn-success';
                                $cnt_followup = '';
                                if ($qry_followup->cnt > 0) {
                                    $class_followup_icon = 'btn-danger';
                                    $cnt_followup = '(' . $qry_followup->cnt . ')';
                                }
                                if ($row->status != 314) {
                                    $controls .= '<button id="btn_followup" data-id="' . $row->sysid . '" class="btn  btn-xs ' . $class_followup_icon . '"><i class="icon-flag "></i> Follow-up ' . $cnt_followup . '</span></button>';
                                }
                            }
                        }

                        $checkbox_hid = '<input type="checkbox" class="hidden" id="ticketidhid" name="trouble[]" value="' . $row->sysid . '"/>';
                        $str_number = 8;
                        if($circuit_level_id>0) {
                            $str_number = 9;
                            $followup_stat = 'danger text-danger';
                        }

                        $queue_class = '';
                        if ($int == 1) {
                            $queue = '<i class="fa fa-edit"></i>';
                        }else{
                            $queue = '';
                        }
                        if($teamid>0) {
                            $qry_queue = $this->db->select('nums')->from('ticketing_queue')
                                ->where(array('tcid' => $row->sysid, 'types' => 1, 'teamid' => $teamid, 'status' => 1))
                                ->get()->row();
                            if($qry_queue) {
                                $queue = $qry_queue->nums;
                                $queue_class = 'text-bold text-danger';
                            }
                        }
                        if ($int == 1) {
                            $queue_form = ''
                                . '<form id=\'frm_submit_queue\' action=\'' . base_url('ts/teamqueue') . '\' method=\'post\'>'
                                . '<input type=\'hidden\' class=\'form-control\' name=\'teamid\' value=\'' . $teamid . '\'>'
                                . '<input type=\'hidden\' class=\'form-control\' name=\'tcid\' value=\'' . $row->sysid . '\'>'
                                . '<input type=\'hidden\' class=\'form-control\' name=\'types\' value=\'1\'>'
                                . '<div class=\'input-group\'>'
                                . '<input type=\'text\' class=\'form-control\' name=\'queue\' placeholder=\'Queue Num.\'>'
                                . '<span class=\'input-group-btn\'><button class=\'btn btn-primary\'>Save</button></span>'
                                . '</div>'
                                . '</form>';
                            $a_queue = '<a class="popovers queue-btn ' . $queue_class . '" href="javascript:;" title="Team Queue <button type=\'button\' aria-hidden=\'true\' class=\'close\'> &times;</button>" data-trigger="click" data-content="' . $queue_form . '" data-placement="right" data-original-title="Team Queue">' . $queue . '</a>';
                        }else{
                            $a_queue = '<span class="'.$queue_class.'">' . $queue .'</span>';
                        }

                        $data['list'][] = array(
                            'expand' => $row->sysid,
                            'num' => '',
                            'queue' => $a_queue,
                            'ticketno' => '<a class="popovers" href="javascript:;"  title="Report Source" data-trigger="hover" data-content="'.get_types_label_format($row->repsource, false, false, false, false, true, true)->text.'" data-placement="right">'.str_pad($row->sysid, $str_number, '0', STR_PAD_LEFT) . '<br>'.get_types_label_format($row->repsource, false, false, false, false, true, true)->text.'<input type="hidden" id="ticketid" value="' . $row->sysid . '" /></a>' . $checkbox_hid ,
                            'name' => '<b>'.$name.'</b>' . '<br>' . $row->contact,
                            'contact' => $row->contact,
                            'address' => $row->brgyname.', '.$row->address,
                            'time' => $time,
                            'status' => $status,
                            'statusid' => $row->status,
                            'complaints' => $row->tcname ,
                            'tcequipments' => $tcequipments,
                            'tcequipmentid' => $tcequipments_id,
                            'tcfindings' => $tcfindings,
                            'tcfindingid' => $findings_id,
                            'team' => $team,
                            'teamid' => $teamid,
                            'circuit' => $circuit_level,
                            'circuitid' => $circuit_level_id,
                            'etc' => $etctext,
                            'followup' => $followup_stat,
                            'control' => $controls,
                        );
                    }
                }
            }
        }

        $data['followupcnt'] = $followup_cnt;
        $data['tccnt'] = $troublecall_cnt;
        return json_encode($data);
    }

    function get_cwd_ticket_list() {
        $data = array();
        $int = 1;
        $cwd_arr = array(1062,1063,1064,1065,1066,1067);
        $complaints = $this->input->post('complaints');

        if($complaints === 'rv') {
            $this->db->where('tdl.reqverification > ', 0);
        }

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
            ->where_in('tdl.tickettype', $cwd_arr)
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
                if($complaints === 'rv'){
                    $hashlink = '12f0de3dc76e067d21ed85125716e02e9f1e69f0';
                }else{
                    $hashlink = '524e05dc77239f3a15dab766aaa59a9e432efde7';
                }
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

    function get_utility_ticket_list() {
        $data = array();
        $int = 1;
        $cwd_arr = array(1025);
        $this->db->where_in('tdl.status', $cwd_arr);
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
                $controls = '<a data-id="'.$row->sysid.'" href="javascript:;" class="btn btn-xs btn-success" id="btn_crw_prw_accomplished"><i class="fa fa-check"></i> Accomplish</a>';

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
                    'control' => $controls,
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
            ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype')
            ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart')
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
            $html .= '<span class="col-md-6"><a class="fancy-box" title="Complaints Attachments" href="'.base_url('uploads/attachments/cwdo/').$qry->sysid.'/comp.jpg"><img src="'.base_url('uploads/attachments/cwdo/').$qry->sysid.'/comp.jpg" width="100%" height="50px"/> </a></span>';
            $html .= '<span class="col-md-6"><a class="fancy-box" title="Verification Attachments" href="'.base_url('uploads/attachments/cwdo/').$qry->sysid.'/verify.jpg"><img src="'.base_url('uploads/attachments/cwdo/').$qry->sysid.'/verify.jpg" width="100%" height="50px"/> </a></span>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name"><i class="fa fa-binoculars"></i> Findings</span>';
            $html .= '<span class="col-md-8 label-default">SML - Sealed Meter Line</span>';
            $html .= '</li">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name"><i class="fa fa-calendar"></i> Verified</span>';
            $html .= '<span class="col-md-8 label-default">'.$qry->dateupdated.'</span>';
            $html .= '</li">';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';



            $html .= '<div class="row footer">';
            $html .= '<div class="col-md-9">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-2 label-name"><i class="fa fa-comment"></i> Remarks</span>';
            $html .= '<span class="col-md-10 label-default">'.$qry->remarks.'</span>';
            $html .= '</li">';
            $html .= '</ul>';
            $html .= '</div>';


            $html .= '<div class="col-md-3">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name"><i class="fa fa-tag"></i> Tag</span>';
            $html .= '<span class="col-md-9 label-default number"><span class="badge badge-success">B</span> > <span class="badge badge-success">A</span></span>';
            $html .= '</li">';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
        }


        $data['html'] = $html;
        return json_encode($data);
    }

    function get_ticket_info($ticketid) {
        $data = array();
        $qry = false;

        $query = $this->db->select('
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
            ->where(array('tdl.sysid' =>$ticketid))
            ->get()->row();
        if($query) {
            $verificaton = ($query->reqverification == 1) ? 'true' : 'false';
            $verfiedby = 'N/A';
            if ($verificaton == 'true') {
                $verfiedby = 'admin';
            }

            $data['datecreated'] = $query->dateupdated;
            $data['desc'] = $query->remarks;
            $data['particular'] = $query->particular;
            $data['rvno'] = $query->reqverification;
            $data['acctid'] = $query->acctid;
            $data['complainants'] = $query->complainants;
            $data['complainantsname'] = $query->lastname.', '.$query->firstname.' '.$query->middlename;
            $data['complainantsaddress'] = $query->address;
            $data['contact'] = $query->contact;
            $data['createdby'] = get_users_info($query->createdby)->username;
            $data['remarks'] = $query->remarks;
            $data['status'] = $query->status;

            $qry = true;
        }else{
            $qry = false;
        }

        $data['qry'] = $qry;

        return (object)$data;
    }


    function get_ar_view() {
        $data = array();
        $acctid = $this->input->post('acctid');
        $query = $this->db->select('b.sysid, b.month, b.year, b.current, b.kwhuse, b.prsrdg, b.prsdte, b.billno, b.totalvat, b.duedate')
            ->from('billing_reports_main AS b')
            ->where(array('b.acctid' => $acctid, 'b.year' => date('Y')))
            ->order_by('b.dteprt', 'desc')
            ->get();
        if ($query->num_rows() > 0) {
            foreach($query->result() as $row) {
                $get_ref = $this->db->select('t.codeid')
                    ->from('billing_reports_tagging_trn AS t')
                    ->where(array('t.billid' => $row->sysid, 't.status != ' => 303))
                    ->order_by('t.datecreated', 'desc')
                    ->get()->row();
                $reff = '';
                if($get_ref) {
                    $reff = get_types_label_format($get_ref->codeid, false, false);
                }
                $dt = DateTime::createFromFormat('m', $row->month);
                //$monthname = $dt->format('F');
                $monthcode = strtoupper($dt->format('M'));
                $data['list'][] = array(
                    'month' => '<span class="label label-info" style="margin-right: 5px;">'.str_pad($row->month, 2, '0', STR_PAD_LEFT).'</span> '.$monthcode,
                    'year' => $row->year,
                    'current' => number_format($row->current, 2),
                    'kwh' => $row->kwhuse,
                    'prsrdg' => $row->prsrdg,
                    'prsdte' => $row->prsdte,
                    'reff' => $reff,
                    'checkbox' => '<input name="months['.$row->month.']" value="'.$row->sysid.'" id="month" type="checkbox"/>'
                );
            }
        }
        return json_encode($data);
    }


    function save_tagging() {
        $data = array();
        $title = '';
        $qry = false;

        $moduleid       = $this->input->post('moduleid');


        $title          = 'Referral';
        $ticketid       = $this->input->post('ticketid');
        $acctid         = $this->input->post('dataid');
        $refid          = $this->input->post('ref');
        $bill           = $this->input->post('months');
        $accomulated    = $this->input->post('accomulated');
        $rvrequest      = $this->input->post('rvrequest');
        $addbill        = $this->input->post('addbill');
        $monthaccom     = $this->input->post('accomulatedmonths');
        $monthaddbill   = $this->input->post('addbillmonths');
        $remarks        = $this->input->post('remarks');
        if($bill && count($bill) > 0) {
            $this->db->where(array('acctid' => $acctid, 'status' => 300));
            $this->db->update('billing_reports_tagging_group', array('status' => 305, 'updatedby' => user_id()));

            $grp_ins_arr = array(
                'acctid' => $acctid,
                'accomonth' => ($accomulated) ? $monthaccom : 0,
                'rv' => ($rvrequest) ? 1 : 0,
                'addbillmonth' => ($addbill) ? $monthaddbill : 0,
                'remarks' => $remarks,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('billing_reports_tagging_group', $grp_ins_arr);
            $group_id = $this->db->insert_id();

            $data['err'][] = $this->db->_error_message();
            foreach($bill as $row) {
                $this->db->where(array('billid' => $row, 'status' => 300));
                $this->db->update('billing_reports_tagging_trn', array('status' => 305, 'updatedby' => user_id()));

                $trn_ins_arr = array(
                    'billid' => $row,
                    'codeid' => $refid,
                    'groupid' => $group_id,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                );
                $this->db->insert('billing_reports_tagging_trn', $trn_ins_arr);
            }
            $data['acctid'] = $acctid;

            // REFERRALS UPDATE
            $findingid = $refid;
            $findings_message = 'CWD - Referrals';
            $findings_status = 311;
            if($findingid==false || $findingid==''){
                $findings_message = 'CWD Referrals Removed!';
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
                'codes' => 'CWDREF',
                'descs' => $findings_message,
                'statusid' => $findingid,
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

            $this->db->where(array('status > ' => 1, 'sysid' => $ticketid));
            $this->db->update('ticketing_details_logs', array('status' => $findings_status));

            // IF RV UPDATE
            if($rvrequest) {
                $findings_message = 'CWD - Request for Verification';
                $findings_status = 1078;
                if($findingid==false || $findingid==''){
                    $findings_message = 'CWD Request for Verification!';
                    $findings_status = 300;
                }

                $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
                $this->db->update('ticketing_details_logs_findings', array('status' => 0, 'updatedby' => user_id()));


                if($findings_status != 300) {
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
                    'codes' => 'CWDRV',
                    'descs' => $findings_message,
                    'statusid' => $findingid,
                    'remarks' => $remarks,
                    'createdby' => user_id()
                );
                $this->db->insert('ticketing_details_trails', $ticket_trail_arr);

                // GET RV REQUEST NUMBER
                $rvno = get_new_rvno();

                $this->db->where(array('status > ' => 1, 'sysid' => $ticketid));
                $this->db->update('ticketing_details_logs', array(
                    'reqverification' => $rvno,
                    'status' => $findings_status
                ));
            }

            $qry = true;
        }


        $data['qry'] = $qry;
        $data['title'] = $title;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_select2_referrals() {

        $data = array();
        $query = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'REFERRALS'))
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

    function add_referrals_row() {
        $data = array();
        $qry = false;

        $ticketid = $this->input->post('ticketid');
        $findingid = $this->input->post('findingid');


        //$this->db->trans_begin();

        $findings_message = 'CWD - Referrals Added';
        $findings_status = 300;

        if($findingid == false || $findingid == ''){
            $findings_message = 'CWD Referrals Removed!';
            $findings_status = 307;
        }

        $this->db->where(array('ticketid' => $ticketid, 'status' => 1));
        $this->db->update('ticketing_details_logs_findings', array('status' => 0, 'updatedby' => user_id()));
        $data['update_ticketing_details_logs_findings'] = $this->db->_error_message();

        if($findings_status != 307) {
            $finding_ins_arr = array(
                'ticketid' => $ticketid,
                'findingid' => $findingid,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert('ticketing_details_logs_findings', $finding_ins_arr);
            $data['insert_ticketing_details_logs_findings'] = $this->db->_error_message();
        }

        $ticket_trail_arr = array(
            'ticketid' => $ticketid,
            'codes' => 'CWDREF',
            'descs' => $findings_message,
            'statusid' => $findingid,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
        $data['insert_ticketing_details_trails'] = $this->db->_error_message();

        $this->db->where(array('status > ' => 1, 'sysid' => $ticketid));
        $this->db->update('ticketing_details_logs', array('status' => $findings_status));
        $data['update_ticketing_details_logs'] = $this->db->_error_message();

        $data['msg'] = $findings_message;
        $data['stat'] = $findings_status;
        return json_encode($data);
    }

    function add_remarks_row() {
        $data = array();
        $qry = false;

        $ticketid = $this->input->post('ticketid');
        $statusid = $this->input->post('statusid');
        $remarks = $this->input->post('remarks');


        $this->db->trans_begin();


        $ticket_trail_arr = array(
            'ticketid' => $ticketid,
            'codes' => 'CWDREMARKS',
            'descs' => $remarks,
            'statusid' => $statusid,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);


        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
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
            tdl.acctid, 
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
                $controls = '<a href="' . base_url('module/524e05dc77239f3a15dab766aaa59a9e432efde7/view/' . $ticketid) . '" class="btn btn-info btn-xs inline"><i class="fa fa-search"></i></a>';
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
                $barangay = $qry_garangay->texts;
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
            //$remarks = $qry->remarks;
            $lastt_action = 'No Accomplishment Remarks';

            //if($qry->status == 314) {

                $qry_accomplish = $this->db->select('remarks')->from('ticketing_details_trails')
                    ->where(array('ticketid' => $ticketid, 'remarks != ' => ''))
                    ->order_by('datecreated', 'desc')
                    ->get()->row();
                if($qry_accomplish) {
                    $lastt_action = $qry_accomplish->remarks;
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
            $html .= '<span class="col-md-8 text-primary">'.$qry->dateupdated.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-4 label-name">Encoded By</span>';
            $html .= '<span class="col-md-8 text-primary">'.$created_by.'</span>';
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

            $acctinfo = get_active_account_info($qry->acctid);

            if($acctinfo) {

                $acct_status = ($acctinfo->status==1) ? 'Active' : 'Disconnected';

                // READING
                $rdg = '0';
                $rdg_dte = '0000-00-00';
                $rdg_current = '0.00';
                $rdg_overdue = '0.00';
                $qry_last_reading = $this->db->select('prvrdg, prsrdg, prvdte, prsdte, current, overdue')
                    ->from('billing_reports_main')
                    ->where(array('acctid' => $qry->acctid))
                    ->order_by('dteprt', 'desc')
                    ->get()->row();

                if($qry_last_reading) {
                    $rdg = number_format($qry_last_reading->prvrdg) . ' - ' . number_format($qry_last_reading->prsrdg);
                    $rdg_dte = $qry_last_reading->prvdte . ' - ' . $qry_last_reading->prsdte;
                    $rdg_current = $qry_last_reading->current;
                    $rdg_overdue = $qry_last_reading->overdue;
                }

                // PAYMENT
                $paid = '0.00';
                $paid_dte = '0000-00-00';
                $qry_last_payment = $this->db->select('SUM((amtpd+interest)-frtax) AS amtpd, billmo, billyr, CAST(datecreated AS date) AS datecreated')
                    ->from('billing_payapplied')
                    ->where(array('acctid' => $qry->acctid))
                    ->group_by('billmo, billyr, datecreated')
                    ->order_by('datecreated', 'desc')
                    ->get()->row();

                if($qry_last_payment) {
                    $paid = $qry_last_payment->amtpd;
                    $paid_dte = $qry_last_payment->datecreated;
                }


                $html .= '<hr style="margin-top: 2px; margin-bottom: 4px;">';


                $html .= '<div class="row margin-bottom-5">';
                $html .= '<div class="col-md-1">';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">GDLB</span>';
                $html .= '<span class="col-md-8 text-primary">'.get_gdlb_name($acctinfo->gdlb).'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Rate Class</span>';
                $html .= '<span class="col-md-8 text-primary">'.$acctinfo->classcode.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Mult</span>';
                $html .= '<span class="col-md-8 text-primary">'.$acctinfo->multiplier.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Connection</span>';
                $html .= '<span class="col-md-8 text-primary">'.$acct_status.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';


                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">MTR No.</span>';
                $html .= '<span class="col-md-8 text-primary">'.$acctinfo->mtrno.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Serial</span>';
                $html .= '<span class="col-md-8 text-primary">'.$acctinfo->mtrserial.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Reading</span>';
                $html .= '<span class="col-md-8 text-primary">'.$rdg.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Reading Date</span>';
                $html .= '<span class="col-md-8 text-primary">'.$rdg_dte.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-4">';
                $html .= '<ul class="list-group summary column no-border list-group-sm">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Overdue</span>';
                $html .= '<span class="col-md-8 text-primary number">'.number_format($rdg_current, 2).'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Current</span>';
                $html .= '<span class="col-md-8 text-primary number">'.number_format($rdg_overdue, 2).'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Date Payment</span>';
                $html .= '<span class="col-md-8 text-primary number">'.$paid_dte.'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Payment Amount</span>';
                $html .= '<span class="col-md-8 text-primary number">'.number_format($paid, 2).'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '</div>';

            }


            $html .= '<div class="row footer">';
            $html .= '<div class="">';
            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name"><i class="fa fa-comment"></i> Remarks</span>';
            $html .= '<span class="col-md-9 label-default">'.$last_trail_stats.'</span>';
            $html .= '</li">';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '<div class="col-md-6">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Referrals</span>';
            $html .= '<span class="col-md-9 label-default">'.$findings.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '<div class="col-md-1 pull-right">';
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

            $qry_team = $this->db->select()->from('ticketing_details_assignments')
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