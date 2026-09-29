<?php


class Model_itd extends CI_Model
{

    function get_itd_techlog_list() {
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

        $followup_cnt = 0;
        $troublecall_cnt = 0;
        $ts_arr = array();

        // #######################################################################
        // FILTER TICKET TYPES FOR TS ONLY
        $query_ts = $this->db->select('sysid')->from('prime_types_parameter')
            ->where(array('codes' => 'TECHLOGTYPE', 'status' => 1))
            ->get();
        if ($query_ts->num_rows() > 0) {
            foreach ($query_ts->result() as $tsrow) {
                $ts_arr[] = $tsrow->sysid;
            }
        }
        $this->db->where_in('tdl.tickettype', $ts_arr);
        // #######################################################################
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
            ->where(array('tdl.status > ' => 0))
            ->get();

        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $etctext = '';
                if($row->complainants>0) {
                    $name = $row->lastname . ', '.$row->firstname . ' ' . $row->middlename;
                }else{
                    $name = $row->compname;
                }

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
                            if ($int == 1) {
                                $tcequipments .= '<input type="hidden" class="form-control inline" id="select2_equipments" name="equipments[]" value="' . $tcequipments_id . '" />';
                                $tcequipments .= get_types_label_format($tcequipments_id, false, true, 'top', 'javascript:;', false, false);
                                $tcfindings .= '<input type="hidden" class="form-control inline" id="select2_findings" name="findings[]" value="' . $findings_id . '" />';
                                $tcfindings .= get_types_label_format($findings_id, false, true, 'top', 'javascript:;', false, false);
                                $etctext = $etc_input;
                            } else {
                                $tcequipments = get_types_label_format($tcequipments_id);
                                $tcfindings =  get_types_label_format($findings_id);
                                $etctext = $row->etc . ' min.';
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
                            // $controls .= '<a href="#form_edit_ticket" title="TC No.: '.str_pad($row->sysid, 8, '0', STR_PAD_LEFT).'" data-arr="'.$row->sysid.'" data-toggle="ajax-modal" class="btn btn-warning btn-xs inline"><i class="fa fa-pencil"></i></a>';
                            $controls .= '<a href="#form_view_techlog" title="TL No.: '.str_pad($row->sysid, 8, '0', STR_PAD_LEFT).'" data-arr="'.$row->sysid.'" data-toggle="ajax-modal" class="btn btn-info btn-xs inline"><i class="fa fa-search"></i></a>';

                            if($qry_followup->cnt > 0) {
                                $followup_cnt += $qry_followup->cnt;
                                $followup_stat = 'danger';
                                $controls .= '<a href="javascript:;" title="Followup" data-trigger="hover" data-content="There are ' . $qry_followup->cnt . ' followup call!" data-placement="left" class="label label-danger popovers"><i class="icon-flag"></i> ' . $qry_followup->cnt . '</span>';
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



                    $deparment = '<code>N/A</code>';
                    $qry_employee = $this->db->select('sysid')->from('prime_employee_main')
                        ->where(array('personid' => $row->complainants))
                        ->get()->row();
                    if($qry_employee) {
                        $empinfo = get_employee_info($qry_employee->sysid);
                        if($empinfo->qry==true) {
                            $deparment = $empinfo->deptname;
                        }
                    }


                    $data['list'][] = array(
                        'expand' => $row->sysid,
                        'num' => '',
                        'queue' => $a_queue,
                        'ticketno' => str_pad($row->sysid, $str_number, '0', STR_PAD_LEFT),
                        'name' => '<b>'.$name.'</b>',
                        'department' => $deparment,
                        'address' => $row->brgyname.', '.$row->address,
                        'remarks' => $row->remarks,
                        'time' => $time,
                        'status' => $status,
                        'statusid' => $row->status,
                        'complaints' => $row->tcname ,
                        'equipment' => $tcequipments,
                        'equipmentid' => $tcequipments_id,
                        'findings' => $tcfindings,
                        'findingsid' => $findings_id,
                        'team' => $team,
                        'teamid' => $teamid,
                        'etc' => $etctext,
                        'followup' => $followup_stat,
                        'control' => $controls,
                    );
                }
            }
        }

        $data['query'] = $this->db->last_query();
        return json_encode($data);
    }


    function get_cc_table() {
        $data = array();
        $query = $this->db->select('sysid, codes, names, desc')
            ->from('prime_costcenter_main')
            ->where(array('status' => 1))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {

                $qry_heads = $this->db->select('ch.empid AS headid')
                    ->from('prime_employee_costcenter AS cc')
                    ->join('prime_costcenter_head AS ch','cc.ccid = ch.ccid AND ch.`status` = 1')
                    ->where(array('cc.ccid' => $row->sysid, 'cc.`status`' => 1))
                    ->get()->row();

                $qry_exec = $this->db->select('cgh.empid AS execid')
                    ->from('prime_employee_costcenter AS cc')
                    ->join('prime_costcenter_group_matrix AS cgm','cc.ccid = cgm.ccid')
                    ->join('prime_costcenter_group AS cg','cgm.groupid = cg.sysid')
                    ->join('prime_costcenter_group_head AS cgh','cgh.groupid = cg.sysid')
                    ->where(array('cc.ccid' => $row->sysid, 'cc.`status`' => 1, 'cgm.status' => 1, 'cg.level != ' => 4, 'cg.level != ' => 5))
                    ->get()->row();

                $headid = ($qry_heads) ? $qry_heads->headid : 0;
                $exceid = ($qry_exec) ? $qry_exec->execid : 0;

                $get_head_info = false;
                if($headid>0) {
                    $get_head_info = get_employee_info($headid);
                }
                $get_head_name = ($get_head_info) ? '<span class="text-color-blue">'.$get_head_info->lastname . ', ' . $get_head_info->firstname.'</span>' : '<code>N/A</code>';

                $get_exec_info = false;
                if($exceid>0) {
                    $get_exec_info = get_employee_info($exceid);
                }
                $get_exec_name = ($get_exec_info) ? '<span class="text-color-blue">'.$get_exec_info->lastname . ', ' . $get_exec_info->firstname.'</span>' : '<code>N/A</code>';

                $data['list'][] = array(
                    'id' => $row->sysid,
                    'ccid' => $row->codes,
                    'code' => $row->names,
                    'name' => $row->desc,
                    'head' => $get_head_name,
                    'exec' => $get_exec_name,
                );
            }
        }
        return json_encode($data);
    }


    function get_cc_employee_table() {
        $data = array();
        $ccid = $this->input->post('ccid');

        $qry_heads = $this->db->select('ch.empid AS headid')
            ->from('prime_employee_costcenter AS cc')
            ->join('prime_costcenter_head AS ch','cc.ccid = ch.ccid AND ch.`status` = 1')
            ->where(array('cc.ccid' => $ccid, 'cc.`status`' => 1))
            ->get()->row();

        $qry_exec = $this->db->select('cgh.empid AS execid')
            ->from('prime_employee_costcenter AS cc')
            ->join('prime_costcenter_group_matrix AS cgm','cc.ccid = cgm.ccid')
            ->join('prime_costcenter_group AS cg','cgm.groupid = cg.sysid')
            ->join('prime_costcenter_group_head AS cgh','cgh.groupid = cg.sysid')
            ->where(array('cc.ccid' => $ccid, 'cc.`status`' => 1, 'cgm.status' => 1, 'cg.level != ' => 4, 'cg.level != ' => 5))
            ->get()->row();

        $headid = ($qry_heads) ? $qry_heads->headid : 0;
        $exceid = ($qry_exec) ? $qry_exec->execid : 0;

        $get_head_info = false;
        if($headid>0) {
            $get_head_info = get_employee_info($headid);
        }
        $get_head_name = ($get_head_info) ? $get_head_info->lastname . ', ' . $get_head_info->firstname : 'N/A';
        $data['headname'] = $get_head_name;


        $get_exec_info = false;
        if($exceid>0) {
            $get_exec_info = get_employee_info($exceid);
        }
        $get_exec_name = ($get_exec_info) ? $get_exec_info->lastname . ', ' . $get_exec_info->firstname : 'N/A';
        $data['execname'] = $get_exec_name;

        $query = $this->db->query("
            SELECT
                em.sysid,
                ecc.ccid,
                CONCAT( p.lastname, ', ', p.firstname, ' ', p.middlename ) AS empname,
                tp.`names` AS posname,
                em.empid 
            FROM
                prime_employee_main AS em
                INNER JOIN prime_employee_main_positions AS emp ON emp.emp_id = em.sysid
                INNER JOIN person AS p ON p.sysid = em.personid
                INNER JOIN prime_types_parameter AS tp ON emp.position_id = tp.sysid
                INNER JOIN prime_employee_costcenter AS ecc ON ecc.empid = em.sysid 
                AND ecc.`status` = 1 
            WHERE
                ecc.ccid = $ccid 
                AND ecc.`status` = 1 
                AND em.`status` = 1 
                AND emp.`status` = 1 
                AND ecc.`type` = 1 
                AND em.sysid != $headid
            ORDER BY
                empname ASC
        ");
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $control = '';
                $control .= '<a class="btn btn-info btn-xs inline" href=""><i class="fa fa-search"></i></a>';
                if(super_admin()) {
                    $control .= '<a id="btn_del_emp" data-ccid="'.$row->ccid.'" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs inline" href="javascript:;"><i class="fa fa-times"></i></a>';
                }
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'code' => $row->empid,
                    'name' => $row->empname,
                    'pos' => $row->posname,
                    'status' => $control,
                );
            }
        }
        return json_encode($data);
    }

    function delete_cc_employee() {
        $this->db->trans_begin();
        $empid = $this->input->post('empid');
        $ccid = $this->input->post('ccid');
        $this->db->update('prime_employee_costcenter', array('status' => 0), array('ccid' => $ccid, 'empid' => $empid, 'status' => 1));
        $data = db_trans($this->db);
        return json_encode($data);
    }

    function assign_cc_employee() {
        $data = array();
        $this->db->trans_begin();
        $data['title'] = 'CC Maintenance';

        $ccid = $this->input->post('ccid');
        $designation = $this->input->post('designation');
        $type = $this->input->post('type');
        $lastname = $this->input->post('lastname');
        $firstname = $this->input->post('firstname');
        $qry_person = $this->db->select('sysid')->from('person')
            ->where(array('lastname' => $lastname, 'firstname' => $firstname, 'status' => 1))
            ->get()->row();
        if($qry_person) {
               $qry_employee = $this->db->select()->from('prime_employee_main')
                   ->where(array('personid' => $qry_person->sysid, 'status' => 1))
                   ->get()->row();
               if($qry_employee) {
                   $empid = $qry_employee->sysid;
                   $data['empid'] = $empid;
                   if($designation == 1 || $designation == 2) {
                       $qry_check_member = $this->db->select('empid')
                           ->from('prime_employee_costcenter')
                           ->where(array('empid' => $empid, 'ccid' => $ccid, 'status' => 1))
                           ->get()->row();
                       if($qry_check_member) {
                           if($designation == 2) {
                               $this->db->update('prime_costcenter_head', array('status' => 0, 'updatedby' => user_id()), array('ccid' => $ccid, 'status' => 1));
                               $this->db->insert('prime_costcenter_head', array('ccid' => $ccid, 'empid' => $empid, 'status' => 1, 'type' => 1, 'createdby' => user_id(), 'updatedby' => user_id()));
                               $data = db_trans($this->db);
                           }else {
                               $data['msg'] = 'Employee is already exists!';
                               $data['func'] = 'warning';
                               $data['qry'] = false;
                           }
                       }else {
                           $ins_empcc = array('empid' => $qry_employee->sysid, 'ccid' => $ccid, 'type' => $type, 'status' => 1);
                           $this->db->insert('prime_employee_costcenter', $ins_empcc);
                           if($designation == 2) {
                               $this->db->update('prime_costcenter_head', array('status' => 0, 'updatedby' => user_id()), array('ccid' => $ccid, 'status' => 1));
                               $this->db->insert('prime_costcenter_head', array('ccid' => $ccid, 'empid' => $empid, 'status' => 1, 'type' => 1, 'createdby' => user_id(), 'updatedby' => user_id()));
                           }
                           $data = db_trans($this->db);
                       }
                   }else{
                       $get_group_id = $this->db->select('groupid')
                           ->from('prime_costcenter_group_head')
                           ->where(array('empid' => $empid, 'status' => 1))
                           ->get()->row();
                       if($get_group_id) {
                           $this->db->update('prime_costcenter_group_matrix', array('status' => 0, 'updatedby' => user_id()), array('ccid' => $ccid, 'status' => 1));
                           $err[] = $this->db->_error_message();
                           $this->db->insert('prime_costcenter_group_matrix', array('ccid' => $ccid, 'groupid' => $get_group_id->groupid, 'status' => 1, 'createdby' => user_id(), 'updatedby' => user_id()));
                           $err[] = $this->db->_error_message();
                           $data = db_trans($this->db);
                           $data['err'] = $err;
                       }else{
                           $data['msg'] = 'Employee is not Exectutive!';
                           $data['func'] = 'info';
                           $data['qry'] = false;
                       }
                   }
               }else{
                   $data['msg'] = 'Employee not found!';
                   $data['func'] = 'warning';
                   $data['qry'] = false;
               }
        }else{
            $data['msg'] = 'Person not found!';
            $data['func'] = 'error';
            $data['qry'] = false;
        }
        return json_encode($data);
    }


    function get_log_details($tid = false) {
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
                $controls = '<a href="' . base_url('module/25293f2761d658cc70c19515861842d712751bdc/view/' . $ticketid) . '" class="btn btn-info btn-xs inline pull-right"><i class="fa fa-search"></i> View</a>';
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


            $html .= '<div class="col-md-3">';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="col-md-3 label-name">Remarks</span>';
            $html .= '<span class="col-md-9 text-primary">'.$last_trail_stats.'</span>';
            $html .= '</li">';
            $html .= '</ul>';
            $html .= '</div>';


            $html .= '<div class="col-md-2">';
            $html .= $controls;
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