<?php

// ##################################################
// AUTHOR : LUCKY JOHN FADERON - SE
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_query extends CI_Model {

    // USER FUNCTIONS
    function user_transaction_details() {
        $query = $this->db->query ( "SELECT * FROM transaction_request_main WHERE createdby = " . user_session ()->system_user_sessid . "" );
        return ($query->num_rows () > 0) ? $query->result () : false;
    }
    function user_assign_roles() {
        $data = array ();
        $users = $this->input->post ( 'selectuser' );
        $roles = $this->input->post ( 'selectroles' );
        $ex_users = explode ( ',', $users );
        foreach ( $ex_users as $ur ) {
            $ex_roles = explode ( ',', $roles );
            foreach ( $ex_roles as $urr ) {
                if ($this->get_user_role ( $ur, $urr ) == false) {
                    $inr = array (
                        'userid' => $ur,
                        'roleid' => $urr
                    );
                    $ins = $this->db->insert ( 'prime_system_users_roles_matrix', $inr );
                    $data ['q'] = true;
                    $data ['msg'] = 'New Added: ' . $ur . ', ' . $urr;
                } else {
                    $data ['q'] = false;
                    $data ['msg'] = 'Not Affected: ' . $ur . ', ' . $urr;
                }
            }
        }
        return json_encode ( $data );
    }

    // CHECK IF ROLE IS ALREADY ASSIGN TO USER
    function get_user_role($u, $r) {
        return ($query = $this->db->select ( 'userid' )->from ( 'prime_system_users_roles_matrix' )->where ( array (
            'userid' => $u,
            'roleid' => $r,
            'status' => 1
        ) )->get ()->row ()) ? $query : false;
    }
    function get_user_assign_status() {
        $query = $this->db->select ( '*' )->from ( 'prime_system_status_parameter' )->where ( array (
            'types' => 'USER'
        ) )->get ()->result ();
        foreach ( $query as $row ) {
            $data [] = array (
                'id' => $row->sysid,
                'text' => $row->code
            );
        }
        return json_encode ( $data );
    }

    // CALL THIS TO ANY MODEL IF NEEDED
    function get_user_data($ids, $field) {
        return ($query = $this->db->select ( 'username' )->from ( 'prime_system_users' )->where ( array (
            $field => $ids,
            'status' => 1
        ) )->get ()->row ()) ? $query : false;
    }
    function add_user() {
        $data = array ();
        $submit = true;
        $msg = "";
        if (empty ( $this->input->post ( 'firstname' ) )) {
            $submit = false;
            $msg = "First name is empty";
        }
        if (empty ( $this->input->post ( 'lastname' ) )) {
            $submit = false;
            $msg = "Last name is empty";
        }
        if (empty ( $this->input->post ( 'username' ) )) {
            $submit = false;
            $msg = "Username is empty";
        }
        if (empty ( $this->input->post ( 'password' ) )) {
            $submit = false;
            $msg = "Password is empty";
        }
        if (empty ( $this->input->post ( 'status' ) )) {
            $submit = false;
            $msg = "Status is empty";
        }
        if ($this->input->post ( 'rpassword' ) == $this->input->post ( 'password' )) {
            $ins = array (
                'username' => $this->input->post ( 'username' ),
                'password' => $this->encrypt_pass ( $this->input->post ( 'password' ) ),
                'firstname' => $this->input->post ( 'firstname' ),
                'lastname' => $this->input->post ( 'lastname' ),
                'status' => $this->input->post ( 'status' )
            );
            if ($submit == true) {
                $qry = $this->db->insert ( 'prime_system_users', $ins );
                if ($qry) {
                    $data ['qry'] = true;
                    $data ['msg'] = 'User has been added';
                } else {
                    $data ['qry'] = false;
                    $data ['msg'] = 'Error adding user';
                }
            } else {
                $data ['qry'] = false;
                $data ['msg'] = $msg;
            }
        } else {
            $data ['msg'] = 'Password did not match';
            $data ['qry'] = false;
        }
        return json_encode ( $data );
    }
    function approval_process() {
        $approval = $this->input->post ('approval');
        $trnid = $this->input->post ('trnid');
        $apprtype = 0;
        $data ['input'] = $this->input->post();
        $func = 'info';
        $msg = 'Nothing happend!';
        $qry = false;
        $button_name = '';

        //$this->db->trans_begin ();

        $button_name = get_types_name($approval)->names;

        $this->db->trans_begin();

        $logs_arr = array (
            'trailid' => $trnid,
            'activity' => $approval,
            'userid' => user_id()
        );
        $this->db->insert('transaction_request_trails_logs',  $logs_arr);
        $data['err'] = $this->db->_error_message();
        if($this->db->trans_status() == true) {
            $this->db->trans_commit();
            if($approval == 88) {
                $msg = 'Transaction has been disapproved!';
                $qry = true;
                $func = 'error';
            }else{
                $msg = 'Transaction has been approved!';
                $qry = true;
                $func = 'success';
            }
        }else{
            $this->db->trans_rollback();
            $msg = 'Query Error!';
        }
        $data ['approval'] = $approval;
        $data ['func'] = $func;
        $data ['title'] = 'Transaction Action';
        $data ['msg'] = $msg;
        $data ['qry'] = $qry;

        return $data;
    }

    // =================================================================================
    // REQUEST PROCESS SEND TASK TO FLOW STAGES
    // =================================================================================
    function insert_trn_entry() {
        // assets_main
        $moduleid = $this->input->post ( 'moduleid' );
        $codes = $this->input->post ( 'assetcode' );
        $descs = $this->input->post ( 'descriptions' );
        $this->db->trans_begin ();
        $module_flow_check = $this->model_admin->get_module_flow_start ( $moduleid );
        if ($module_flow_check) {

            $asset_main_ins = array (
                'assetcode' => $codes
            );
            $asset_main = $this->db->insert ( 'assets_main', $asset_main_ins );
            $asset_id = $this->db->insert_id ();

            $html = '';
            $ins_arr = array (
                'trn_main' => array (
                    'origid' => $moduleid,
                    'trncode' => 'TRN02',
                    'codes' => $codes,
                    'descriptions' => $codes . ' - ' . $descs,
                    'moduleid' => $moduleid,
                    'stagesidfrom' => 0,
                    'stagesid' => $module_flow_check,
                    'dataid' => $asset_id,
                    'createdby' => user_session ()->system_user_sessid,
                    'status' => 1,
                    'remarks' => 'N/A'
                ),
                'trn_log_data' => user_session ()->system_user_sessid . '/' . $html
            );

            if (task_ins_process ( $ins_arr ) && $this->db->trans_status ()) {
                $qry = true;
                $msg = 'Asset added!';
                $this->db->trans_commit ();
            } else {
                $qry = false;
                $msg = 'Error Query!';
                $this->db->trans_rollback ();
            }
        } else {
            $qry = false;
            $msg = 'No Request Flow..';
        }
        $data ['qry'] = $qry;
        $data ['msg'] = $msg;
        return json_encode ( $data );
    }

    // =================================================================================
    // REQUEST PROCESS SEND TASK TO FLOW STAGES DIRECTLY
    // =================================================================================
    function process_request_direct() {
        $data = array();

        $process = $this->input->post('process');
        $types = $this->input->post('types');
        $trnid = $this->input->post('trnid');
        $dataid = $this->input->post('dataid');
        $remarks = $this->input->post('remarks');
        $title = SYSTEM_NAME;
        $msg = SYSTEM_MSG_DEFAULT;
        $func = 'error';

        $sql_trn_main = $this->db->select('trmt.sysid, trm.descs, stage.flowid, stage.sysid AS stageid, stage.levels, stage.desc')
            ->from('transaction_request_main_trails AS trmt')
            ->join('transaction_request_main AS trm', 'trm.sysid = trmt.trnid')
            ->join('prime_transaction_flow_main_stages AS stage', 'stage.sysid = trmt.stageid')
            ->where(array('trm.sysid' => $trnid, 'trmt.status' => 1))
            ->order_by('trmt.datecreated', 'desc')
            ->get()->row();


        $data['sqltrnmain'] = $sql_trn_main;

        $first_route_id = 0;
        $last_route_id = 0;
        $next_level_num = 0;

        $route_curr_name = '';
        $route_next_name = '';
        $curr_stageid = 0;
        $current_mudolueid = 0;

        if($sql_trn_main) {
            $data['trnname'] = $sql_trn_main->descs;


            // GET FLOW DETAILS
            $sql_flow_main = $this->db->select()->from('`prime_transaction_flow_main')
                ->where(array('sysid' => $sql_trn_main->flowid))->get()->row();
            $current_mudolueid = ($sql_flow_main) ? $sql_flow_main->moduleid : '';

            // GET LAST LEVEL FROM STAGE
            $sql_stages = $this->db->select()
                ->from('prime_transaction_flow_main_stages')
                ->where(array('flowid' => $sql_trn_main->flowid, 'status' => 1))
                ->order_by('levels')
                ->get();
            if($sql_stages->num_rows()>0){
                foreach($sql_stages->result() as $index => $rrow) {
                    if($index == 0) {
                        $first_route_id = $rrow->sysid;
                    }
                    $last_route_id = $rrow->sysid;
                }
            }


            $curr_stageid = $sql_trn_main->stageid;
            if($process == 0) {
                if($sql_trn_main->levels == 1) {


                    $sql_stages_next = $this->db->select()
                        ->from('prime_transaction_flow_main_stages')
                        ->where(array('flowid' => $sql_trn_main->flowid, 'levels' => 1))
                        ->order_by('levels')
                        ->get()->row();

                    $route_curr_name = $sql_trn_main->desc;
                    $route_next_name = $sql_stages_next->desc;
                    $route_next_id = $curr_stageid;
                    $msg = 'Cannot send transaction to previous route, the current route is the first level of transaction.';
                }else{
                    $next_level_num = ($sql_trn_main->levels - 1);
                    $sql_stages_next = $this->db->select()
                        ->from('prime_transaction_flow_main_stages')
                        ->where(array('flowid' => $sql_trn_main->flowid, 'levels' => $next_level_num))
                        ->order_by('levels')
                        ->get()->row();

                    $data['next'] = true;
                    $data['next_route_id'] = $sql_stages_next->sysid;

                    if ($types == 1) {
                        $trail_arr = array(
                            'trnid' => $trnid,
                            'stageid' => $sql_stages_next->sysid,
                            'dataid' => $dataid,
                            'createdby' => user_id(),
                            //'status' => $stats
                        );

                        $task_ins_process = task_ins_process($trail_arr, NULL, NULL);
                        if ($task_ins_process) {
                            $title = $sql_stages_next->desc;
                            $msg = 'Transaction has been sent to next route';
                            $func = 'success';
                        } else {
                            $title = 'Error';
                            $msg = 'Transaction process SQL Error.';
                            $func = 'error';
                        }
                        $data['inserroutearr'] = $trail_arr;
                    } else {
                        $msg = "";
                        $msg .= "Current Route: {$sql_trn_main->desc}\n";
                        $msg .= "Next Route: {$sql_stages_next->desc}\n";
                        $route_curr_name = $sql_trn_main->desc;
                        $route_next_name = $sql_stages_next->desc;
                        $route_next_id = $sql_stages_next->sysid;
                        $data['msg'] = $msg;
                    }

                    if ($remarks && $remarks != '') {
                        $comments_arr = array(
                            'trnid' => $trnid,
                            'trailid' => $sql_stages_next->sysid,
                            'remarks' => $remarks,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('transaction_request_trails_comments', $comments_arr);
                    }
                }
            } else {
                $func = 'success';
                $next_level_num = ($sql_trn_main->levels + 1);

                $sql_stages_next = $this->db->select()
                    ->from('prime_transaction_flow_main_stages')
                    ->where(array('flowid' => $sql_trn_main->flowid, 'levels' => $next_level_num))
                    ->order_by('levels')
                    ->get()->row();

                if ($sql_stages_next && $next_level_num <= $sql_stages_next->levels) { // ALLOW NEXT
                    $data['next'] = true;
                    $data['next_route_id'] = $sql_stages_next->sysid;

                    if ($types == 1) {
                        $trail_arr = array(
                            'trnid' => $trnid,
                            'stageid' => $sql_stages_next->sysid,
                            'dataid' => $dataid,
                            'createdby' => user_id(),
                            //'status' => $stats
                        );

                        $task_ins_process = task_ins_process($trail_arr, NULL, NULL);
                        if ($task_ins_process) {
                            $title = $sql_stages_next->desc;
                            $msg = 'Transaction has been sent to next route';
                            $func = 'success';
                        } else {
                            $title = 'Error';
                            $msg = 'Transaction process SQL Error.';
                            $func = 'error';
                        }
                        $data['inserroutearr'] = $trail_arr;
                    } else {
                        $msg = "";
                        $msg .= "Current Route: {$sql_trn_main->desc}\n";
                        $msg .= "Next Route: {$sql_stages_next->desc}\n";
                        $route_curr_name = $sql_trn_main->desc;
                        $route_next_name = $sql_stages_next->desc;
                        $route_next_id = $sql_stages_next->sysid;
                        $data['msg'] = $msg;
                    }

                    if ($remarks && $remarks != '') {
                        $comments_arr = array(
                            'trnid' => $trnid,
                            'trailid' => $sql_stages_next->sysid,
                            'remarks' => $remarks,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('transaction_request_trails_comments', $comments_arr);
                    }
                } else {
                    $msg = 'Cannot send transaction to next route, the current route is the final/last route level.';
                }
            }

        }

        $data['current_flowid'] = ($sql_trn_main) ? $sql_trn_main->flowid : false;
        $data['current_level'] = ($sql_trn_main) ? $sql_trn_main->levels : false;
        $data['current_trnid'] = $trnid;
        $data['current_stageid'] = $curr_stageid;
        $data['current_moduleid'] = $current_mudolueid;
        $data['current_dataid'] = $dataid;
        $data['next_level'] = $next_level_num;
        $data['next_route_id'] = $route_next_id;
        $data['first_route_id'] = $first_route_id;
        $data['last_route_id'] = $last_route_id;
        $data['route_curr'] = $route_curr_name;
        $data['route_next'] = $route_next_name;
        $data['input'] = $this->input->post();
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
    }

    // =================================================================================
    // REQUEST PROCESS SEND TASK TO FLOW STAGES
    // =================================================================================


    function process_request() {
        $custom_message = false;
        $message = '';
        $message_func = 'error';

        // CHECK IF SESSION IS ACTIVE
        if ( user_id() > 0) {
            $user_id = user_id();
            $trnid = $this->input->post ( 'trnid' );
            $moduleid = $this->input->post ( 'moduleid' );
            $flowid = $this->input->post ( 'flowid' );
            $stageid = $this->input->post ( 'stageid' );
            $routeto = $this->input->post ( 'routeto' );
            $stats = $this->input->post ( 'stats' );
            $remarks = $this->input->post ( 'remarks' );
            $trntitle = $this->input->post ( 'trntitle' );
            $dataid = $this->input->post ( 'dataid' );
            // GET FLOW SORTING //
            $qry_flow = $this->db->select ('levels')
                ->from ( 'prime_transaction_flow_main_stages' )
                ->where ( 'flowid', $flowid )
                ->get();
            if ($routeto) {
                $next_stage_level = get_stage_details ( $stageid )->levels + 1;
                $curr_stage_level = get_stage_details ( $stageid )->levels;
                $submit_stage_level = get_stage_details ( $routeto )->levels;
                // GET FLOW OPTION //
                $get_stage_option = task_user_access ( $stageid );

                //GET STAGES PREVIOUSLY TRANSACTED
                $get_past_stages = $this->db->select('trmt.stageid')
                    ->from('transaction_request_main_trails AS trmt')
                    ->where('dataid',$dataid)
                    ->group_by('trmt.stageid')->get();

                if ($get_past_stages->num_rows() > 0) {
                    foreach ($get_past_stages->result() as $row) {
                        $previous_stages[] = $row->stageid;
                    }
                } else {
                    $previous_stages = array();
                }

                // INSERT REMARKS
                if(trim($remarks) != ''){
                    $comments_arr = array(
                        'trnid' => $trnid,
                        'trailid' => $stageid,
                        'remarks' => $remarks,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('transaction_request_trails_comments', $comments_arr);
                }

                /*
                 * $html = '';
                 * $html .= '<h3>Details</h3>';
                 * $html .= '<br>TRN ID: '.$trnid;
                 * $html .= '<br>SUBMIT ROUTE: '.$routeto;
                 * $html .= '<br>SUBMIT LEVEL: '.$submit_stage_level;
                 * $html .= '<br>CURRENT LEVEL: '.$curr_stage_level;
                 *
                 * $html .= '<br>FLOW ID: '.$flowid;
                 * $html .= '<br>STAGE ID: '.$stageid;
                 * $html .= '<br>STATS: '.$stats;
                 * $html .= '<br>TRN TITLE: '.$trntitle;
                 * $html .= '<br>DATA ID: '.$dataid;
                 *
                 *
                 *
                 * $data['qry'] = true;
                 * $data['msg'] = $html;
                 * return json_encode($data);
                 * exit();
                 */
                // ############################################ //
                // SPECIAL QUERY // FOR SPECIFIC STAGE REQUIRED //
                // APPLICABLE FOR LEGAL ####################### //
                $custom_message_func = 'warning';
                $check_flow_req = $this->db->select()
                    ->from('prime_transaction_flow_main_stages_required')
                    ->where(array('flowid' => $flowid, 'status' => 1))
                    ->get()->row();


                $check_exemptions = $this->db->select('dataid')
                    ->from('prime_transaction_flow_main_stages_required_exempt')
                    ->where(array('dataid' => $dataid, 'flowid' => $flowid))
                    ->get()->row();

                if ($check_flow_req && $check_exemptions == false) {
                    $get_app_id = $this->db->select('sysid, existlegalra')
                        ->from('application_customers_details')
                        ->where('sysid', $dataid)
                        ->get()->row();
                    if ($get_app_id) {
                        if ($get_app_id->existlegalra > 0) {
                            //  && $stageid != $check_flow_req->stageid
                            $message .= '<b><i class="fa fa-warning text-danger"></i> Legal Verification is Required</b>';
                            $custom_message = true;
                            $custom_message_func = 'warning';
                            // DISABLED STRICT ROUTE TO LEGAL
                            $routeto = $check_flow_req->stageid;
                        }
                    }
                }

                /*
                $message .= 'FLOW ID: ' . $flowid . '<br>';
                $message .= 'STAGE ID: ' . $stageid . '<br>';

                $html = '';
                $qry = true;
                $msg = $message;

                $data ['func'] = ($custom_message) ? $custom_message_func : $message_func;
                $data ['html'] = $html;
                $data ['qry'] = $qry;
                $data ['msg'] = $msg;
                return json_encode ( $data );
                */

                $trail_arr = array (
                    'trnid' => $trnid,
                    'stageid' => $routeto,
                    'dataid' => $dataid,
                    'createdby' => $user_id,
                    //'status' => $stats
                );

                if( $curr_stage_level == get_stage_details($routeto)->levels && $custom_message==true) {
                    $msg = 'Cannot send transaction if current stage has not yet verified at ';
                    $msg .= '<br><b>'.get_stage_details($routeto)->desc. '</b>';
                    $custom_message_func = 'info';
                    $qry = false;
                }else {
                    // Validation
                    if ($submit_stage_level == $curr_stage_level) {
                        $msg = 'Submit route must not the same with the current route!';
                        $qry = false;
                    } else {
                        if ($curr_stage_level < $submit_stage_level && $submit_stage_level == $next_stage_level) {
                            $task_ins_process = task_ins_process($trail_arr, NULL, NULL);
                            if ($task_ins_process->qry == true) {
                                $new_trailid = $task_ins_process->trailid;
                                $qry = true;
                                $msg = "Transaction send to : <b>" . get_stage_details($routeto)->desc . "</b>";
                                $message_func = 'success';
                            } else {
                                $qry = false;
                                $msg = "Transaction send to : <b>" . get_stage_details($routeto)->desc . "</b> Error";
                                $message_func = 'error';
                            }
                        } else {
                            if ($get_stage_option->access == true) {
                                if (($curr_stage_level < $submit_stage_level) && ($get_stage_option->canskip || in_array($submit_stage_level, $previous_stages))) {
                                    $task_ins_process = task_ins_process($trail_arr, NULL, NULL);
                                    if ($task_ins_process->qry == true) {
                                        $new_trailid = $task_ins_process->trailid;
                                        $qry = true;
                                        $msg = "Transaction skipped to : <b>" . get_stage_details($routeto)->desc . "</b>";
                                        $message_func = 'success';
                                    } else {
                                        $qry = false;
                                        $msg = "Transaction skipped to : <b>" . get_stage_details($routeto)->desc . "</b> Error";
                                        $message_func = 'error';
                                    }
                                } else {
                                    if (($curr_stage_level > $submit_stage_level) && ($get_stage_option->cansendback || in_array($submit_stage_level, $previous_stages))) {
                                        $task_ins_process = task_ins_process($trail_arr, NULL, NULL);
                                        if ($task_ins_process->qry == true) {
                                            $new_trailid = $task_ins_process->trailid;
                                            $qry = true;
                                            $msg = "Transaction send back to : <b>" . get_stage_details($routeto)->desc . "</b>";
                                            $message_func = 'success';
                                        } else {
                                            $qry = false;
                                            $msg = "Transaction send back to : <b>" . get_stage_details($routeto)->desc . "</b> Error";
                                            $message_func = 'error';
                                        }
                                    }
                                }
                            } else {
                                if (in_array(1, get_users_roles_matrix_id_arr())) {
                                    if ($curr_stage_level < $submit_stage_level) {
                                        $task_ins_process = task_ins_process($trail_arr, NULL, NULL);
                                        if ($task_ins_process->qry == true) {
                                            $new_trailid = $task_ins_process->trailid;
                                            $qry = true;
                                            $msg = "Administrator send to : <b>" . get_stage_details($routeto)->desc . "</b>";
                                            $message_func = 'success';
                                        } else {
                                            $qry = false;
                                            $msg = "Administrator send to : <b>" . get_stage_details($routeto)->desc . "</b> Error";
                                            $message_func = 'danger';
                                        }
                                    } else {
                                        $task_ins_process = task_ins_process($trail_arr, NULL, NULL);
                                        if ($task_ins_process->qry == true) {
                                            $new_trailid = $task_ins_process->trailid;
                                            $qry = true;
                                            $msg = "Administrator send back to : <b>" . get_stage_details($routeto)->desc . "</b>";
                                            $message_func = 'success';
                                        } else {
                                            $qry = false;
                                            $msg = "Administrator send back to : <b>" . get_stage_details($routeto)->desc . "</b> Error";
                                            $message_func = 'error';
                                        }
                                    }
                                } else {
                                    if ($curr_stage_level < $submit_stage_level) {
                                        $qry = false;
                                        $msg = "Cannot skip transactions";
                                        $message_func = 'warning';
                                    } else {
                                        $qry = false;
                                        $msg = "Cannot send back transactions";
                                        $message_func = 'warning';
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $msg = 'Select route to send';
                $qry = false;
                $message_func = 'info';
            }

            $access = ($get_stage_option->access == true) ? 'True' : 'False';

            $html = '';
            $html .= '<h3>Details</h3>';
            $html .= '<br>TRN ID: ' . $trnid;
            $html .= '<br>HAS ACCESS: ' . $access;
            $html .= '<br>SUBMIT ROUTE: ' . $routeto;
            $html .= '<br>FLOW ID: ' . $flowid;
            $html .= '<br>STAGE ID: ' . $stageid;
            $html .= '<br>STATS: ' . $stats;
            $html .= '<br>TRN TITLE: ' . $trntitle;
            $html .= '<br>SUBMIT LEVEL: ' . $submit_stage_level;

            $toastr_settings = '';

            // ADD LINK TO TOASR
            if ($qry) {
                $toastr_settings = toastr_link ( $new_trailid, $dataid );
                $msg_link = $toastr_settings->link;
                $msg = ($custom_message) ? $msg.'<br>'.$message : $msg;
                $msg = '<a href="' . $msg_link . '">' . $msg . '</a>';
            } else {
                $msg_link = '';
                $msg = $msg;
            }

            $data['url'] = $toastr_settings;

            $data ['func'] = ($custom_message) ? $custom_message_func : $message_func;
            $data ['new_stage'] = $submit_stage_level;
            $data ['stage_history'] = $previous_stages;
            $data ['html'] = $html;
            $data ['qry'] = $qry;
            $data ['msg'] = $msg;
            return json_encode ( $data );

        } else {

            $data ['func'] = $message_func;
            $data ['qry'] = false;
            $data ['msg'] = 'Your account has been logged out!';
            return json_encode ( $data );

        }

    }

    // =================================================================================
    // ACTIVE / LEGACY CUSTOMER
    // AUTHOR : LUCKY JOHN FADERON - SE
    // MAY 22, 2017
    // =================================================================================
    function get_active_owner($id) {
        $qry = false;

        // VARIABLES
        $types 			= 	'';
        $tin 			= 	'';
        $ownerid 		= 	'';
        $corpcodes		=	'';
        $corpdescs		=	'';
        $firstname 		= 	'';
        $lastname 		= 	'';
        $middlename 	= 	'';
        $gender 		= 	'';
        $birthdate 		= 	'';
        $coowner 		= 	'';
        $contact 		= 	'';
        $maritalid 		=	'';
        $addrspecific	=	'';
        $addrdist		=	'';
        $addrcity		=	'';
        $addrlat		=	'';
        $addrlon		=	'';
        $addralt		=	'';
        $addrgeodate	=	'';
        $addrgeouser	=	'';
        $accdatecreate	=	'';
        $acctratename 	= 	'';
        $acctratecode	=	'';
        $acctrateid		=	'';
        $contractdate 	= 	'';
        // =============================================================================
        // CHECK ACCOUNT TYPE FIREST
        $check_acct = $this->db->select()
            ->from ( 'customer_accounts_owners AS cao' )
            ->join('customer_accounts_main AS cam', 'cam.sysid = cao.accountid')
            ->where ( 'cam.sysid', $id )->get ()->row ();

        if ($check_acct) {
            $qry = true;
            // ##########################################################
            // GET LEGACY ###########################################
            if($check_acct->ownertype == 5) {

                $qry_owners = $this->db->select()
                    ->from('customer_accounts_owners')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_acctin = $this->db->select()
                    ->from('customer_accounts_tin')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_person = $this->db->select()
                    ->from('customer_accounts_name_legacy')
                    ->where(array('sysid' => $qry_owners->ownerid))
                    ->get()->row();

                $qry_addrss = $this->db->select("
						ADDR.addrspecific AS STREET,
						ADDR.district AS DIST,
						ADDR.city AS CITY,
						ADDRGEO.latitude AS LAT,
						ADDRGEO.longitude AS LON,
						ADDRGEO.altitude AS ALT,
						ADDRGEO.datecreated AS GEODATE,
						ADDRGEO.createdby AS GEOUSER
                	")
                    ->from('customer_accounts_address AS ADDR')
                    ->join('customer_accounts_subscription_geodata AS ADDRGEO', 'ADDRGEO.addressid = ADDR.sysid AND ADDRGEO.status = 1', 'left')
                    ->where(array('ADDR.acctid' => $id, 'ADDR.status' => 1))
                    ->get()->row();

                $qry_accinf = $this->db->select("
						PSRCM.classifications as RATE,
						PSRCM.codes AS RATECODE,
						PSRCM.sysid AS RATEID
					")
                    ->from('customer_accounts_subscription_rates AS RATE')
                    ->join('prime_system_rate_class_main AS PSRCM', 'PSRCM.sysid = RATE.rateid AND RATE.status = 1', 'left')
                    ->where(array('RATE.accountid' => $id, 'RATE.status' => 1))
                    ->get()->row();

                $qry_accsub = $this->db->select()
                    ->from('customer_accounts_subscription')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                // BASIC INFO
                $types 			= 	($qry_owners) ? $qry_owners->ownertype 		: '';
                $tin 			= 	($qry_acctin) ? $qry_acctin->tinnumber 		: '';
                $ownerid 		= 	($qry_person) ? $qry_person->sysid 			: '';
                $firstname 		= 	($qry_person) ? $qry_person->name 			: '';

                // ADDRESS INFO
                $addrspecific	=	($qry_addrss) ? $qry_addrss->STREET			: '';
                $addrdist		=	($qry_addrss) ? $qry_addrss->DIST			: '';
                $addrcity		=	($qry_addrss) ? $qry_addrss->CITY			: '';
                $addrlat		=	($qry_addrss) ? $qry_addrss->LAT			: '';
                $addrlon		=	($qry_addrss) ?	$qry_addrss->LON			: '';
                $addralt		=	($qry_addrss) ? $qry_addrss->ALT			: '';
                $addrgeodate	=	($qry_addrss) ? $qry_addrss->GEODATE		: '';
                $addrgeouser	=	($qry_addrss) ? $qry_addrss->GEOUSER		: '';

                // ACCOUNT INFO

                $acctratename 	= 	($qry_accinf) ? $qry_accinf->RATE			: '';
                $acctratecode	=	($qry_accinf) ? $qry_accinf->RATECODE		: '';
                $acctrateid		=	($qry_accinf) ?	$qry_accinf->RATEID			: '';


            }






            // ##########################################################
            // GET INDIVIDUAL ###########################################
            // ##########################################################
            if($check_acct->ownertype == 1) {

                $qry_owners = $this->db->select()
                    ->from('customer_accounts_owners')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_acctin = $this->db->select()
                    ->from('trn_customer_accounts_tin')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_person = $this->db->select()
                    ->from('person')
                    ->where(array('sysid' => $qry_owners->ownerid))
                    ->get()->row();

                $qry_persn2 = $this->db->select("CONCAT(P2.lastname, ', ', P2.firstname, ' ', P2.middlename) AS coowner", false)
                    ->from('customer_accounts_owners_secondary AS caos')
                    ->join('person AS P2', 'P2.sysid = caos.ownerid AND caos.status = 1', 'left')
                    ->get()->row();

                $qry_maritl = $this->db->select()
                    ->from('persons_marital_status_logs')
                    ->where(array('personid' => $qry_person->sysid, 'status' => 1))
                    ->get()->row();

                $qry_contct = $this->db->select()
                    ->from('person_contact_matrix')
                    ->where(array('personid' => $qry_person->sysid, 'status' => 1))
                    ->get()->row();

                $qry_addrss = $this->db->select("
						ADDR.addrspecific AS STREET,
						ADDR.district AS DIST,
						ADDR.city AS CITY,
						ADDRGEO.latitude AS LAT,
						ADDRGEO.longitude AS LON,
						ADDRGEO.altitude AS ALT,
						ADDRGEO.datecreated AS GEODATE,
						ADDRGEO.createdby AS GEOUSER
                	")
                    ->from('trn_customer_accounts_address AS ADDR')
                    ->join('trn_customer_accounts_subscription_geodata AS ADDRGEO', 'ADDRGEO.addressid = ADDR.sysid AND ADDRGEO.status = 1', 'left')
                    ->where(array('ADDR.acctid' => $id, 'ADDR.status' => 1))
                    ->get()->row();

                $qry_accinf = $this->db->select("
						PSRCM.classifications as RATE,
						PSRCM.codes AS RATECODE,
						PSRCM.sysid AS RATEID
					")
                    ->from('trn_customer_accounts_subscription_rates AS RATE')
                    ->join('prime_system_rate_class_main AS PSRCM', 'PSRCM.sysid = RATE.rateid AND RATE.status = 1', 'left')
                    ->where(array('RATE.accountid' => $id, 'RATE.status' => 1))
                    ->get()->row();

                $qry_accsub = $this->db->select()
                    ->from('customer_accounts_subscription')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                // BASIC INFO
                $types 			= 	($qry_owners) ? $qry_owners->ownertype 		: '';
                $tin 			= 	($qry_acctin) ? $qry_acctin->tinnumber 		: '';
                $ownerid 		= 	($qry_person) ? $qry_person->sysid 			: '';
                $firstname 		= 	($qry_person) ? $qry_person->firstname 		: '';
                $lastname 		= 	($qry_person) ? $qry_person->lastname 		: '';
                $middlename 	= 	($qry_person) ? $qry_person->middlename 	: '';
                $gender 		= 	($qry_person) ? $qry_person->gender 		: '';
                $birthdate 		= 	($qry_person) ? $qry_person->birthdate		: '';
                $coowner 		= 	($qry_persn2) ? $qry_persn2->coowner		: '';
                $contact 		= 	($qry_contct) ? $qry_contct->contactstring 	: '';
                $maritalid 		=	($qry_maritl) ? $qry_maritl->statusid		: '';

                // ADDRESS INFO
                $addrspecific	=	($qry_addrss) ? $qry_addrss->STREET			: '';
                $addrdist		=	($qry_addrss) ? $qry_addrss->DIST			: '';
                $addrcity		=	($qry_addrss) ? $qry_addrss->CITY			: '';
                $addrlat		=	($qry_addrss) ? $qry_addrss->LAT			: '';
                $addrlon		=	($qry_addrss) ?	$qry_addrss->LON			: '';
                $addralt		=	($qry_addrss) ? $qry_addrss->ALT			: '';
                $addrgeodate	=	($qry_addrss) ? $qry_addrss->GEODATE		: '';
                $addrgeouser	=	($qry_addrss) ? $qry_addrss->GEOUSER		: '';

                // ACCOUNT INFO
                $acctratename 	= 	($qry_accinf) ? $qry_accinf->RATE			: '';
                $acctratecode	=	($qry_accinf) ? $qry_accinf->RATECODE		: '';
                $acctrateid		=	($qry_accinf) ?	$qry_accinf->RATEID			: '';
            }
        }

        // GET GDLB
        $qry_glb = $this->db->select('gdlbid')
            ->from('customer_accounts_glb')
            ->where(array('accountid' => $id, 'status' => 1))
            ->get()->row();

        // GET CURRENT LOAD
        $qry_load = $this->db->select('load, datecreated')
            ->from('customer_accounts_load_logs')
            ->where(array('acctid' => $id, 'status' => 1))
            ->get()->row();


        // GET MTR
        $qry_mtr = $this->db->select()->from('customer_accounts_subscription_meter')
            ->where(array('acctid' => $id, 'status' => 1))
            ->get()->row();

        // GET RGD
        $qry_rgd = $this->db->select()->from('trn_customer_accounts_gdr_logs')
            ->where(array('accountid' => $id, 'status' => 1))
            ->get()->row();

        // GET MULTCODE
        $qry_mult = $this->db->select('am.multid, am.datecreated')->from('customer_accounts_multiplier AS am')
            ->join('billing_rates_main_multiplier AS rm', 'rm.sysid = am.multid')
            ->where(array('am.acctid' => $id, 'am.status' => 1))
            ->get()->row();



        $gdlb 			=	($qry_glb) ? $qry_glb->gdlbid : '';
        $load 			=	($qry_load) ? $qry_load->load : '';
        $loaddate 		= 	($qry_load) ? $qry_load->datecreated : '';

        $multcode 		= ($qry_mult) ? $qry_mult->multid : '';
        $multcodedate 	= ($qry_mult) ? $qry_mult->datecreated : '';

        $mtrno			= ($qry_mtr) ? $qry_mtr->mtrno 			: '';
        $mtrser			= ($qry_mtr) ? $qry_mtr->mtrserial 		: '';
        $mtrsysid		= ($qry_mtr) ? $qry_mtr->sysid 			: '';

        if($mtrsysid) {
            // GET READING
            $qry_read_pres = $this->db->select()
                ->from('customer_accounts_subscription_meter_reading')
                ->where(array('mtrid' => $mtrsysid, 'acctid' => $id))
                ->order_by('readingdate', 'desc')
                ->get()->row();

            if($qry_read_pres) {
                $this->db->where('readingdate < ', $qry_read_pres->readingdate);
                $qry_read_prev = $this->db->select()
                    ->from('customer_accounts_subscription_meter_reading')
                    ->where(array('mtrid' => $mtrsysid, 'acctid' => $id))
                    ->order_by('readingdate', 'desc')
                    ->get()->row();
            }else{
                $qry_read_prev = false;
            }

            $presread = ($qry_read_pres) ? $qry_read_pres->reading : '';
            $prevread = ($qry_read_prev) ? $qry_read_prev->reading : '';
            $presreaddate = ($qry_read_pres) ? $qry_read_pres->readingdate : '';
            $prevreaddate = ($qry_read_prev) ? $qry_read_prev->readingdate : '';


        }else{
            $presread = '';
            $prevread = '';
            $presreaddate = '';
            $prevreaddate = '';
        }


        $rgdno			= ($qry_rgd) ? $qry_rgd->rgdno			: '';
        $rgdamt			= ($qry_rgd) ? $qry_rgd->totalcost		: '';


        $accdatecreate	=	$check_acct->datecreated;
        $contractdate	=	$check_acct->datecontract;
        $conndate		=	$check_acct->dateconnected;

        if($qry) {
            $query = array(
                // BASIC INFO
                'SYSID' 			=> $id,
                'SERVNO' 			=> $check_acct->servicenumber,
                'TYPES' 			=> $types,
                'TIN' 				=> $tin,
                'OWNERSYSID' 		=> $ownerid,

                // INDIVIDUAL
                'FIRSTNAME' 		=> $firstname,
                'LASTNAME' 			=> $lastname,
                'MIDDLENAME' 		=> $middlename,
                'GENDER' 			=> $gender,
                'BIRTHDAY' 			=> $birthdate,
                'PERSON2' 			=> $coowner,
                'MARITALID' 		=> $maritalid,

                // CORPORATION
                'CORPNAME' 			=> $corpcodes,
                'CORPDESC' 			=> $corpdescs,

                // ADDRESS
                'CONTACT' 			=> $contact,
                'STREET' 			=> $addrspecific,
                'DIST' 				=> $addrdist,
                'CITY' 				=> $addrcity,
                'LAT' 				=> $addrlat,
                'LON' 				=> $addrlon,
                'ALT'				=> $addralt,
                'GEODATE' 			=> $addrgeodate,
                'GEOUSER' 			=> $addrgeouser,

                // ACCOUNT
                'DC' 				=> $accdatecreate,
                'RATE' 				=> $acctratename,
                'RATECODE' 			=> $acctratecode,
                'RATEID' 			=> $acctrateid,
                'SUBTYPE' 			=> '',
                'ACCTRATE' 			=> '',
                'CONTRACTDATE' 		=> $contractdate,
                'CONNDATE' 			=> $conndate,
                'GDLB' 				=> $gdlb,
                'LOAD' 				=> $load,
                'LOADDATE' 			=> $loaddate,
                'MULTCODE' 			=> $multcode,
                'MULTCODEDATE' 		=> $multcodedate,
                'MTRNO'				=> $mtrno,
                'MTRSER'			=> $mtrser,
                'MTRSYSID'			=> $mtrsysid,
                'RGDNO'				=> $rgdno,
                'RGDAMT'			=> $rgdamt,
                'PRESREADING'		=> $presread,
                'PREVREADING'		=> $prevread,
                'PRESREADDATE'		=> $presreaddate,
                'PREVREADDATE'		=> $prevreaddate,
            );
            $query['accttype'] = $check_acct->ownertype;
            return (object)$query;
        }else{
            return false;
        }
    }


    // =================================================================================
    // APPLICATION FUNCTIONS
    // AUTHOR : LUCKY JOHN FADERON - SE
    // MARCH 6, 2017
    // =================================================================================
    function get_owner_info($id) {
        $query = false;

        // VARIABLES
        $types 			= 	'';
        $tin 			= 	'';
        $ownerid 		= 	'';
        $corpcodes		=	'';
        $corpdescs		=	'';
        $firstname 		= 	'';
        $lastname 		= 	'';
        $middlename 	= 	'';
        $gender 		= 	'';
        $birthdate 		= 	'';
        $coowner 		= 	'';
        $contact 		= 	'';
        $maritalid 		=	'';
        $addrspecific	=	'';
        $addrdist		=	'';
        $addrcity		=	'';
        $addrlat		=	'';
        $addrlon		=	'';
        $addralt		=	'';
        $addrgeodate	=	'';
        $addrgeouser	=	'';
        $accdatecreate	=	'';
        $acctratename 	= 	'';
        $acctratecode	=	'';
        $acctrateid		=	'';
        $gdlb			= 	'';

        // CHECK ACCOUNT TYPE FIREST
        $check_acct = $this->db->select ()->from ( 'trn_customer_accounts_owners AS cao' )
            ->join('trn_customer_accounts_main AS cam', 'cam.sysid = cao.accountid')
            ->where ( 'cam.sysid', $id )->get ()->row ();

        if ($check_acct) {
            $qry = true;

            // ##########################################################
            // GET INDIVIDUAL ###########################################
            if($check_acct->ownertype == 1) {

                $qry_owners = $this->db->select()
                    ->from('trn_customer_accounts_owners')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_acctin = $this->db->select()
                    ->from('trn_customer_accounts_tin')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_person = $this->db->select()
                    ->from('person')
                    ->where(array('sysid' => $qry_owners->ownerid))
                    ->get()->row();

                $qry_persn2 = $this->db->select("CONCAT(P2.lastname, ', ', P2.firstname, ' ', P2.middlename) AS coowner", false)
                    ->from('trn_customer_accounts_owners_secondary AS caos')
                    ->join('person AS P2', 'P2.sysid = caos.ownerid AND caos.status = 1', 'left')
                    ->get()->row();

                $qry_maritl = $this->db->select()
                    ->from('persons_marital_status_logs')
                    ->where(array('personid' => $qry_person->sysid, 'status' => 1))
                    ->get()->row();

                $qry_contct = $this->db->select()
                    ->from('person_contact_matrix')
                    ->where(array('personid' => $qry_person->sysid, 'status' => 1))
                    ->get()->row();

                $qry_addrss = $this->db->select("
						ADDR.addrspecific AS STREET,
						ADDR.district AS DIST,
						ADDR.city AS CITY,
						ADDRGEO.latitude AS LAT,
						ADDRGEO.longitude AS LON,
						ADDRGEO.altitude AS ALT,
						ADDRGEO.datecreated AS GEODATE,
						ADDRGEO.createdby AS GEOUSER
                	")
                    ->from('trn_customer_accounts_address AS ADDR')
                    ->join('trn_customer_accounts_subscription_geodata AS ADDRGEO', 'ADDRGEO.addressid = ADDR.sysid AND ADDRGEO.status = 1')
                    ->where(array('ADDR.acctid' => $id, 'ADDR.status' => 1))
                    ->get()->row();

                $qry_accinf = $this->db->select("
						PSRCM.classifications as RATE,
						PSRCM.codes AS RATECODE,
						PSRCM.sysid AS RATEID
					")
                    ->from('trn_customer_accounts_subscription_rates AS RATE')
                    ->join('prime_system_rate_class_main AS PSRCM', 'PSRCM.sysid = RATE.rateid AND RATE.status = 1', 'left')
                    ->where(array('RATE.accountid' => $id, 'RATE.status' => 1))
                    ->get()->row();

                $qry_accsub = $this->db->select()
                    ->from('trn_customer_accounts_subscription')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                // BASIC INFO
                $types 			= 	($qry_owners) ? $qry_owners->ownertype 		: '';
                $tin 			= 	($qry_acctin) ? $qry_acctin->tinnumber 		: '';
                $ownerid 		= 	($qry_person) ? $qry_person->sysid 			: '';
                $firstname 		= 	($qry_person) ? $qry_person->firstname 		: '';
                $lastname 		= 	($qry_person) ? $qry_person->lastname 		: '';
                $middlename 	= 	($qry_person) ? $qry_person->middlename 	: '';
                $gender 		= 	($qry_person) ? $qry_person->gender 		: '';
                $birthdate 		= 	($qry_person) ? $qry_person->birthdate		: '';
                $coowner 		= 	($qry_persn2) ? $qry_persn2->coowner		: '';
                $contact 		= 	($qry_contct) ? $qry_contct->contactstring 	: '';
                $maritalid 		=	($qry_maritl) ? $qry_maritl->statusid		: '';

                // ADDRESS INFO
                $addrspecific	=	($qry_addrss) ? $qry_addrss->STREET			: '';
                $addrdist		=	($qry_addrss) ? $qry_addrss->DIST			: '';
                $addrcity		=	($qry_addrss) ? $qry_addrss->CITY			: '';
                $addrlat		=	($qry_addrss) ? $qry_addrss->LAT			: '';
                $addrlon		=	($qry_addrss) ?	$qry_addrss->LON			: '';
                $addralt		=	($qry_addrss) ? $qry_addrss->ALT			: '';
                $addrgeodate	=	($qry_addrss) ? $qry_addrss->GEODATE		: '';
                $addrgeouser	=	($qry_addrss) ? $qry_addrss->GEOUSER		: '';

                // ACCOUNT INFO
                $accdatecreate	=	$check_acct->datecreated;
                $acctratename 	= 	($qry_accinf) ? $qry_accinf->RATE			: '';
                $acctratecode	=	($qry_accinf) ? $qry_accinf->RATECODE		: '';
                $acctrateid		=	($qry_accinf) ?	$qry_accinf->RATEID			: '';
            }

            // ##########################################################
            // GET CORPORATIONS #########################################
            if($check_acct->ownertype == 2) {
                $qry = true;
                $qry_owners = $this->db->select()
                    ->from('trn_customer_accounts_owners')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_acctin = $this->db->select()
                    ->from('trn_customer_accounts_tin')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                $qry_corptn = $this->db->select("
                		P.sysid AS CORPSYSID, 
                		P.codes AS CORPCODES, 
                		P.descs AS CORPDESCS, 
                		PERSON.lastname AS LASTNAME,
						PERSON.middlename as MIDDLENAME,
						PERSON.firstname AS FIRSTNAME,
						PERSON.gender as GENDER
                	")
                    ->from('corporation AS P')
                    ->join('corporation_representative AS REP', 'REP.corpid = P.sysid AND REP.status = 1', 'left')
                    ->join('person AS PERSON', 'PERSON.sysid = REP.personid')
                    ->where(array('P.sysid' => $qry_owners->ownerid))
                    ->get()->row();

                $qry_contct = $this->db->select()
                    ->from('corporation_contact_matrix')
                    ->where(array('personid' => $qry_corptn->sysid, 'status' => 1))
                    ->get()->row();

                $qry_addrss = $this->db->select("
						ADDR.addrspecific AS STREET,
						ADDR.district AS DIST,
						ADDR.city AS CITY,
						ADDRGEO.latitude AS LAT,
						ADDRGEO.longitude AS LON,
						ADDRGEO.altitude AS ALT,
						ADDRGEO.datecreated AS GEODATE,
						ADDRGEO.createdby AS GEOUSER
                	")
                    ->from('trn_customer_accounts_address AS ADDR')
                    ->join('trn_customer_accounts_subscription_geodata AS ADDRGEO', 'ADDRGEO.addressid = ADDR.sysid AND ADDRGEO.status = 1')
                    ->where(array('ADDR.acctid' => $id, 'ADDR.status' => 1))
                    ->get()->row();

                $qry_accinf = $this->db->select("
						PSRCM.classifications as RATE,
						PSRCM.codes AS RATECODE,
						PSRCM.sysid AS RATEID
					")
                    ->from('trn_customer_accounts_subscription_rates AS RATE')
                    ->join('prime_system_rate_class_main AS PSRCM', 'PSRCM.sysid = RATE.rateid AND RATE.status = 1', 'left')
                    ->where(array('RATE.accountid' => $id, 'RATE.status' => 1))
                    ->get()->row();

                $qry_accsub = $this->db->select()
                    ->from('trn_customer_accounts_subscription')
                    ->where(array('accountid' => $id, 'status' => 1))
                    ->get()->row();

                // BASIC INFO
                $types 			= 	($qry_owners) ? $qry_owners->ownertype 		: '';
                $tin 			= 	($qry_acctin) ? $qry_acctin->tinnumber 		: '';
                $ownerid 		= 	($qry_corptn) ? $qry_corptn->CORPSYSID		: '';
                $corpcodes		=	($qry_corptn) ?	$qry_corptn->CORPCODES		: '';
                $corpdescs		=	($qry_corptn) ?	$qry_corptn->CORPDESCS		: '';
                $contact 		= 	($qry_contct) ? $qry_contct->contactstring 	: '';

                // CORP REP
                $firstname		= 	($qry_corptn) ? $qry_corptn->FIRSTNAME		: '';
                $lastname		= 	($qry_corptn) ? $qry_corptn->LASTNAME		: '';
                $middlename		= 	($qry_corptn) ? $qry_corptn->MIDDLENAME		: '';
                $gender			= 	($qry_corptn) ? $qry_corptn->GENDER			: '';

                // ADDRESS INFO
                $addrspecific	=	($qry_addrss) ? $qry_addrss->STREET			: '';
                $addrdist		=	($qry_addrss) ? $qry_addrss->DIST			: '';
                $addrcity		=	($qry_addrss) ? $qry_addrss->CITY			: '';
                $addrlat		=	($qry_addrss) ? $qry_addrss->LAT			: '';
                $addrlon		=	($qry_addrss) ?	$qry_addrss->LON			: '';
                $addralt		=	($qry_addrss) ? $qry_addrss->ALT			: '';
                $addrgeodate	=	($qry_addrss) ? $qry_addrss->GEODATE		: '';
                $addrgeouser	=	($qry_addrss) ? $qry_addrss->GEOUSER		: '';

                // ACCOUNT INFO
                $accdatecreate	=	$check_acct->datecreated;
                $acctratename 	= 	($qry_accinf) ? $qry_accinf->RATE			: '';
                $acctratecode	=	($qry_accinf) ? $qry_accinf->RATECODE		: '';
                $acctrateid		=	($qry_accinf) ?	$qry_accinf->RATEID			: '';
            }
            if($qry) {
                $query = (object)array(
                    // BASIC INFO
                    'SYSID' => $id,
                    'SERVNO' => $check_acct->servicenumber,
                    'TYPES' => $types,
                    'TIN' => $tin,
                    'OWNERSYSID' => $ownerid,

                    // INDIVIDUAL
                    'FIRSTNAME' => $firstname,
                    'LASTNAME' => $lastname,
                    'MIDDLENAME' => $middlename,
                    'GENDER' => $gender,
                    'BIRTHDAY' => $birthdate,
                    'PERSON2' => $coowner,
                    'MARITALID' => $maritalid,

                    // CORPORATION
                    'CORPNAME' => $corpcodes,
                    'CORPDESC' => $corpdescs,

                    // ADDRESS
                    'CONTACT' => $contact,
                    'STREET' => $addrspecific,
                    'DIST' => $addrdist,
                    'CITY' => $addrcity,
                    'LAT' => $addrlat,
                    'LON' => $addrlon,
                    'ALT' => $addralt,
                    'GEODATE' => $addrgeodate,
                    'GEOUSER' => $addrgeouser,

                    // ACCOUNT
                    'DC' => $accdatecreate,
                    'RATE' => $acctratename,
                    'RATECODE' => $acctratecode,
                    'RATEID' => $acctrateid,
                    'SUBTYPE' => '',
                    'ACCTRATE' => '',
                    'GDLB' => $gdlb,
                );
            }

            /*
            if ($check_acct->ownertype < 2) {
                $query = $this->db->query ( "
                            SELECT
                            ACCT.sysid AS SYSID, --
                            ACCT.servicenumber AS SERVNO, --
                            OWNER.ownertype AS TYPES, --
                            CAT.tinnumber AS TIN, --
                            P.sysid AS OWNERSYSID,--
                            P.firstname AS FIRSTNAME,--
                            P.lastname AS LASTNAME, --
                            P.middlename AS MIDDLENAME, --
                            P.gender AS GENDER, --
                            P.birthdate as BIRTHDAY, --
                            CM.contactstring AS CONTACT,
                            CONCAT(P2.lastname, ', ', P2.firstname, ' ', P2.middlename) AS PERSON2, --
                            MS.statusid AS MARITALID, --
                            ADDR.addrspecific AS STREET, --
                            ADDR.district AS DIST, --
                            ADDR.city AS CITY, --
                            ADDRGEO.latitude AS LAT, --
                            ADDRGEO.longitude AS LON, --
                            ADDRGEO.altitude AS ALT, --
                            ADDRGEO.datecreated AS GEODATE, --
                            ADDRGEO.createdby AS GEOUSER, --
                            PSRCM.classifications as RATE, --
                            PSRCM.codes AS RATECODE, --
                            PSRCM.sysid AS RATEID, --
                            ACCT.datecreated AS DC, --
                            SUBS.typeid AS SUBTYPE, --
                            SUBS.rateid AS ACCTRATE --
                            FROM trn_customer_accounts_main AS ACCT
                            LEFT JOIN trn_customer_accounts_owners AS OWNER ON OWNER.accountid = ACCT.sysid
                            LEFT JOIN person AS P ON P.sysid = OWNER.ownerid
                            LEFT JOIN person_contact_matrix AS CM ON P.sysid = CM.personid AND CM.status = 1
                            LEFT JOIN persons_marital_status_logs AS MS ON P.sysid = MS.personid AND MS.status = 1
                            LEFT JOIN trn_customer_accounts_address AS ADDR ON ADDR.acctid = ACCT.sysid
                            LEFT JOIN trn_customer_accounts_subscription_geodata AS ADDRGEO ON ADDRGEO.addressid = ADDR.sysid AND ADDRGEO.status = 1
                            LEFT JOIN trn_customer_accounts_subscription AS SUBS ON SUBS.accountid = ACCT.sysid
                            LEFT JOIN trn_customer_accounts_subscription_rates AS RATES ON RATES.accountid = ACCT.sysid
                            LEFT JOIN trn_customer_accounts_tin AS CAT ON CAT.accountid = ACCT.sysid AND CAT.status = 1
                            LEFT JOIN prime_system_rate_class_main AS PSRCM ON RATES.rateid = PSRCM.sysid
                            LEFT JOIN customer_accounts_owners_secondary AS CAOS ON CAOS.accountid = ACCT.sysid AND CAOS.status = 1
                            LEFT JOIN person AS P2 ON P2.sysid = CAOS.ownerid
                            WHERE ACCT.sysid = $id
                            ")->row();
                return ($query) ? $query : false;
            }
            else
            {
                $query = $this->db->query("
                            SELECT
                            ACCT.sysid AS SYSID, --
                            ACCT.servicenumber AS SERVNO, --
                            OWNER.ownertype AS TYPES,--
                            CAT.tinnumber AS TIN, --
                            P.sysid AS OWNERSYSID, --
                            P.codes AS CORPNAME, --
                            P.descs AS CORPDESC, --
                            CM.contactstring AS CONTACT, --
                            CADDR.addrspec AS STREET, --
                            CADDR.addrdist AS DIST, --
                            CADDR.addrcity AS CITY, --
                            ADDR.addrspecific AS ACCTSTREET, --
                            ADDR.district AS ACCTDIST, --
                            ADDR.city AS ACCTCITY, --
                            PERSON.lastname AS LASTNAME, --
                            PERSON.middlename as MIDDLENAME, --
                            PERSON.firstname AS FIRSTNAME, --
                            PERSON.gender as GENDER, --
                            ADDRGEO.latitude AS LAT, --
                            ADDRGEO.longitude AS LON, --
                            ADDRGEO.altitude AS ALT, --
                            ADDRGEO.datecreated AS GEODATE, --
                            ADDRGEO.createdby AS GEOUSER, --
                            PSRCM.classifications AS RATE, --
                            PSRCM.codes AS RATECODE, --
                            PSRCM.sysid AS RATEID, --
                            ACCT.datecreated AS DC, --
                            USERS.firstname as u_first_name, --
                            USERS.lastname as u_last_name, --
                            SUBS.typeid AS SUBTYPE, --
                            SUBS.rateid AS ACCTRATE --
                            FROM trn_customer_accounts_main AS ACCT
                            LEFT JOIN trn_customer_accounts_owners AS OWNER ON OWNER.accountid = ACCT.sysid
                            LEFT JOIN corporation AS P ON P.sysid = OWNER.ownerid
                            LEFT JOIN corporation_address_matrix AS CADDR ON CADDR.corpid = P.sysid
                            LEFT JOIN corporation_contact_matrix AS CM ON P.sysid = CM.corpid AND CM.status = 1
                            LEFT JOIN trn_customer_accounts_address AS ADDR ON ADDR.acctid = ACCT.sysid
                            LEFT JOIN trn_customer_accounts_subscription_geodata AS ADDRGEO ON ADDRGEO.addressid = ADDR.sysid AND ADDRGEO.status = 1
                            LEFT JOIN trn_customer_accounts_subscription AS SUBS ON SUBS.accountid = ACCT.sysid
                            LEFT JOIN trn_customer_accounts_subscription_rates AS RATES ON RATES.accountid = ACCT.sysid
                            LEFT JOIN trn_customer_accounts_tin AS CAT ON CAT.accountid = ACCT.sysid AND CAT.status = 1
                            LEFT JOIN corporation_representative AS REP ON REP.corpid = P.sysid
                            LEFT JOIN person AS PERSON ON PERSON.sysid = REP.personid
                            LEFT JOIN prime_system_rate_class_main AS PSRCM ON RATES.rateid = PSRCM.sysid
                            LEFT JOIN prime_system_users AS USERS ON OWNER.createdby = USERS.sysid
                            WHERE ACCT.sysid = $id
                            ")->row();
                return ($query) ? $query : false;
            }
            */
            return ($query) ? $query : false;
        } else {
            return false;
        }
    }

    /*
     * function get_account_owner_type($id) {
     * $query = $this->db->query("
     * SELECT parameter.names as o_names FROM customer_accounts_main as main
     * left join customer_accounts_subscription_types as o_types on (o_types.ownerid = main.sysid)
     * left join prime_types_parameter as parameter on (parameter.sysid = o_types.typeid)
     * where main.sysid = $id
     * "
     * )->row();
     * return ($query) ? $query : false;
     * }
     *
     * function get_account_owner_conn($id) {
     * $query = $this->db->query("
     * SELECT parameter.names as p_names FROM customer_accounts_main as main
     * left join customer_accounts_subscription as conn on (conn.accountid = main.sysid)
     * left join prime_types_parameter as parameter on (parameter.sysid = conn.typeid)
     * where main.sysid = $id
     *
     *
     *
     * "
     * )->row();
     * return ($query) ? $query : false;
     * }
     *
     */

    function get_account_owner_location($id) {
        $query = $this->db->query("
									SELECT '' as l_names FROM customer_accounts_main as main
									left join customer_accounts_subscription_geodata as l_types on (l_types.addressid = main.sysid)

									where main.sysid = $id
									")->row();
        return ($query) ? $query : false;
    }

    function get_account_application_requirements($id) {
        $query = $this->db->query("select trn.sysid AS SYSID, prm.names AS NAMES, trn.status AS STATUS from prime_requirement_parameters as prm 
									INNER JOIN trn_request_requirements as trn on prm.sysid = trn.reqid where accountid = $id");
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    // ENCRYPT PASSWORD TO HASH
    function encrypt_pass($pass) {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    function hist_trans($dataid) {
        //$query = $this->db->query("select * from transaction_request_main_trails where dataid = $dataid ORDER BY datecreated DESC");
        $query = $this->db->select('trmt.*,	tfms.`desc`')
            ->from('transaction_request_main_trails as trmt')
            ->join('prime_transaction_flow_main_stages as tfms','trmt.stageid = tfms.sysid','left')
            ->where('trmt.dataid',$dataid)
            ->order_by('trmt.datecreated','DESC')
            ->get();
        return $query;
    }

    // Customer Information queries start
    // Customer Information queries end
    // test for update lot and book

    function insp_query() {
        $moduleid = $this->input->post('moduleid');
        $modulehash = $this->input->post('modulehash');


        $this->datatables->select('sysid, trncode, status, datecreated, stagesidfrom, dataid, createdby');
        $this->datatables->unset_column('trncode')->add_column('trncode', '$1', 'trncode');
        $this->datatables->unset_column('createdby')->add_column('createdby', '$1', 'get_users_info_full(createdby)');
        $this->datatables->unset_column('datecreated')->add_column('datecreated', '$1', 'datecreated');
        $this->datatables->unset_column('stagesidfrom')->add_column('stagesidfrom', '$1', 'stagesidfrom');
        $this->datatables->unset_column('dataid')->add_column('dataid', '$1', 'dataid');
        $this->datatables->unset_column('status')->add_column('status', '$1', 'row_status(status)');
        $this->datatables->unset_column('sysid')->add_column('controls', '$1', 'row_btn_view(' . $modulehash . ', dataid, true)');
        $this->datatables->from("transaction_request_main");
        $this->datatables->where('moduleid', $moduleid);
        return $this->datatables->generate();
    }

    function emp_info($id) {
        $query = $this->db->select ( 'pemp.position_id,e.personid, p.firstname, p.lastname, p.middlename, p.gender, p.birthdate, e.sysid, e.empid, e.status' )
            ->from ( 'prime_employee_main AS e' )
            ->join ( 'person AS p', 'p.sysid = e.personid' )
            ->join ('prime_employee_main_positions as pemp' , 'pemp.emp_id = e.sysid AND pemp.status = 1' , 'left')
            ->where ( 'e.sysid', $id )->get ()->row ();
        return ($query) ? $query : false;
    }

    function getcurrentdata($tblname, $sysid) {
        $data = array(
            'sysid' => $sysid,
            'status' => 1
        );
        $currentdata = $this->db->select('*')->from($tblname)->where($data)->get()->row();
        return ($currentdata) ? $currentdata : false;
    }

    function getprevdata($tblname, $sysid) {
        $data = array(
            'sysid' => $sysid,
            'status' => 0
        );
        $prevdata = $this->db->select('*')->from($tblname)->where($data)->get()->row();
        return ($prevdata) ? $prevdata : false;
    }

    function insertnewdata($tblname, $arraydata = NULL) {
        if ($arraydata != NULL) {
            $this->db->set('datecreated', 'NOW()', false);
            $query = $this->db->insert($tblname, $arraydata);
            return ($query) ? $this->db->insert_id() : false;
        }
    }

    function setstatustozero($tblname, $sysid) {
        $this->db->set('status', 0);
        $this->db->where('sysid', $sysid);
        $this->db->update($tblname);
    }

    function trblcalldatatable() {
        $this->datatables->select('source_id, person_id, contact_number, addr_specific, addr_district, complaint');
        $this->datatables->unset_column('counter')->add_column('1', '$1', '1');
        $this->datatables->unset_column('source_id')->add_column('source_id', '$1', 'source_id');
        $this->datatables->unset_column('person_id')->add_column('person_id', '$1', 'person_id');
        $this->datatables->unset_column('contact_number')->add_column('contact_number', '$1', 'contact_number');
        $this->datatables->unset_column('addr_specific')->add_column('addr_specific', '$1', 'addr_specific');
        $this->datatables->unset_column('addr_district')->add_column('addr_district', '$1', 'addr_district');
        $this->datatables->unset_column('complaint')->add_column('complaint', '$1', 'complaint');
        $this->datatables->from("trouble_call_monitoring_log");
        return $this->datatables->generate();
    }

    function get_customer_near_mtr() {
        $dataid = $this->input->post('dataid');
        $data = get_acctinfo_nearmeter($dataid);
        return json_encode($data);
    }

    function search_meter() {
        $terms = $this->input->post('term');
        $data = array();
        $qry = $this->db->select('am.sysid, am.mtrno, am.servicenumber, am.types, am.ownerid')
            ->from('customer_accounts_main AS am')
            ->like('am.mtrno', $terms)
            ->limit(20)
            ->get();

        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $owner_id = $row->ownerid;
                $owner_name = 'Unknown';
                $owner_addr = 'Unknown';

                if ($row->types == 5) {
                    $qry_owner = $this->db->select('name')
                        ->from('customer_accounts_name_legacy')
                        ->where('sysid', $owner_id)->get()->row();
                    $owner_name = ($qry_owner) ? $qry_owner->name : 'Unknown';


                    $qry_acct_addr = $this->db->select()->from('customer_accounts_address')
                        ->where(array('acctid' => $row->sysid))->get()->row();
                    $owner_addr = ($qry_acct_addr) ? $qry_acct_addr->addrspecific : 'Unknown';


                }

                if ($row->types == 91) {
                    $qry_owner = $this->db->select('p.firstname, p.lastname, p.middlename, a.addrspec')->from('person AS p')->join('person_address_matrix AS a', 'a.personid = p.sysid', 'left')->where('p.sysid', $row->ownerid)->get()->row();
                    $owner_name = ($qry_owner) ? $qry_owner->lastname . ', ' . $qry_owner->firstname : 'Unknown';
                    $owner_addr = ($qry_owner) ? $qry_owner->addrspec : 'Unknown';
                }

                if ($row->types == 92) {
                    $qry_owner = $this->db->select('c.codes, a.addrspec')->from('corporation AS c')->join('corporation_address_matrix AS a', 'a.corpid = c.sysid AND a.status = 1', 'left')->where('c.sysid', $row->ownerid)->get()->row();
                    $owner_name = ($qry_owner) ? $qry_owner->codes : 'Unknown';
                    $owner_addr == ($qry_owner) ? $qry_owner->addrspec : 'Unknown';
                }

                $data [] = array(
                    'id' => $row->sysid,
                    'text' => $row->mtrno . ' - ' . $row->servicenumber,
                    'owner' => $owner_name,
                    'addr' => $owner_addr
                );
            }
        }
        return json_encode($data);
    }



    function getClosest($search, $arr) {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item [1] - $search)) {
                $closest = $item [0];
            }
        }
        return $closest;
    }
    function payroll_info($id) {
        $query = $this->db->select('*')
            ->from("prime_employee_payroll_transactions")
            ->where("empid", $id)
            ->get()->row();

        return ($query) ? $query : false;
    }


    function copy_billtrn_to_billmain() {
        $data = array();
        $qry = false;
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $err = '';
        $msg = 'Error SQL..';
        if($year && $year > 0) {
            $this->db->trans_begin();
            if ($month && $month > 0) {
                $where = " WHERE `month` = $month AND `year` = $year ";
            } else {
                $where = " WHERE `year` = $year ";
            }
            $sql = "
    		INSERT INTO billing_reports_main (
				billno,
				acctid,
				`group`,
				dist,
				lot,
				book,
				servno,
				mtr,
				mtrser,
				serial,
				bmo,
				byr,
				`month`,
				`year`,
				prvdte,
				prsdte,
				duedate,
				`load`,
				rate,
				prvrdg,
				prsrdg,
				multcd,
				kwhuse,
				surbal,
				`current`,
				overdue,
				totacc,
				totint,
				scdisc,
				dolpay,
				batch,
				dteprt,
				datecreated,
				createdby,
				schedid
				)
				SELECT 
				billno,
				acctid,
				`group`,
				dist,
				lot,
				book,
				servno,
				mtr,
				mtrser,
				serial,
				bmo,
				byr,
				`month`,
				`year`,
				prvdte,
				prsdte,
				duedate,
				`load`,
				rate,
				prvrdg,
				prsrdg,
				multcd,
				kwhuse,
				surbal,
				`current`,
				overdue,
				totacc,
				totint,
				scdisc,
				dolpay,
				batch,
				dteprt,
				datecreated,
				createdby,
				schedid
				FROM billing_reports 
				$where
    		";
            $this->db->query($sql);
            $err = $this->db->_error_message();
            if($this->db->trans_status() == true){
                $qry = true;
                $this->db->trans_commit();
                $msg = 'Query success!';
            }else{
                $this->db->trans_rollback();
                $msg = $err;
            }
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function city_query_upd()
    {
        ini_set('MAX_EXECUTION_TIME', -1);
        $data = array();
        $msg = '';
        $num_exist = 0;
        $num_insert = 0;
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $qry = $conn->select('
                LTRIM(RTRIM(f.servno____)) AS servno,
                LTRIM(RTRIM(f.group_____)) AS g,
                LTRIM(RTRIM(f.dist______)) AS d,
                LTRIM(RTRIM(f.lot_______)) AS l,
                LTRIM(RTRIM(f.book______)) AS b,
                f.mtr_______ AS mtr,
                LTRIM(RTRIM(f.class_____)) AS class,
                LTRIM(RTRIM(f.multcd____)) AS multcd,
                LTRIM(RTRIM(f.name______)) AS name,
                LTRIM(RTRIM(f.addr______)) AS addr,
                LTRIM(RTRIM(f.condte____)) AS contractdate,
                LTRIM(RTRIM(f.status____)) AS status,
                LTRIM(RTRIM(f.stadte____)) AS conndate,
                LTRIM(RTRIM(f.mtrser____)) AS mtrno,
                LTRIM(RTRIM(f.serial____)) AS mtrserial,
                f.load______ AS load,
                f.kwh1______ AS kwh_01,
                f.kwh2______ AS kwh_02,
                f.kwh3______ AS kwh_03,
                f.kwh4______ AS kwh_04,
                f.kwh5______ AS kwh_05,
                f.kwh6______ AS kwh_06,
                f.kwh7______ AS kwh_07,
                f.kwh8______ AS kwh_08,
                f.kwh9______ AS kwh_09,
                f.kwh10_____ AS kwh_10,
                f.kwh11_____ AS kwh_11,
                f.kwh12_____ AS kwh_12,
                f.amt1______ AS amt_01,
                f.amt2______ AS amt_02,
                f.amt3______ AS amt_03,
                f.amt4______ AS amt_04,
                f.amt5______ AS amt_05,
                f.amt6______ AS amt_06,
                f.amt7______ AS amt_07,
                f.amt8______ AS amt_08,
                f.amt9______ AS amt_09,
                f.amt10_____ AS amt_10,
                f.amt11_____ AS amt_11,
                f.amt12_____ AS amt_12,
                f.amt13_____ AS amt_13,
                f.bill1_____ AS bill_01,
                f.bill2_____ AS bill_02,
                f.bill3_____ AS bill_03,
                f.bill4_____ AS bill_04,
                f.bill5_____ AS bill_05,
                f.bill6_____ AS bill_06,
                f.bill7_____ AS bill_07,
                f.bill8_____ AS bill_08,
                f.bill9_____ AS bill_09,
                f.bill10____ AS bill_10,
                f.bill11____ AS bill_11,
                f.bill12____ AS bill_12
            ')->from('citygov AS f')
                ->order_by('f.servno____')
                ->get();
            $num_rows = $qry->num_rows();

            if ($num_rows > 0) {
                // CLEAR EXISTING DATA FIRST
                //$this->db->query("TRUNCATE TABLE customer_accounts_main;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_ar;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_name_legacy;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_address;");
                // #########################


                foreach ($qry->result() as $row) {
                    print_r($row) . '<br>';
                    $get_rateclassid = $this->db->select('sysid')->from('rate_class_specification')
                        ->where(array('codes' => $row->class))
                        ->get()->row();
                }
            }
        }
    }


    function city_query()
    {
        ini_set('MAX_EXECUTION_TIME', -1);
        $data = array();
        $msg = '';
        $num_exist = 0;
        $num_insert = 0;
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $qry = $conn->select('
                LTRIM(RTRIM(f.servno____)) AS servno,
                LTRIM(RTRIM(f.group_____)) AS g,
                LTRIM(RTRIM(f.dist______)) AS d,
                LTRIM(RTRIM(f.lot_______)) AS l,
                LTRIM(RTRIM(f.book______)) AS b,
                f.mtr_______ AS mtr,
                LTRIM(RTRIM(f.class_____)) AS class,
                LTRIM(RTRIM(f.multcd____)) AS multcd,
                LTRIM(RTRIM(f.name______)) AS name,
                LTRIM(RTRIM(f.addr______)) AS addr,
                LTRIM(RTRIM(f.condte____)) AS contractdate,
                LTRIM(RTRIM(f.status____)) AS status,
                LTRIM(RTRIM(f.stadte____)) AS conndate,
                LTRIM(RTRIM(f.mtrser____)) AS mtrno,
                LTRIM(RTRIM(f.serial____)) AS mtrserial,
                f.load______ AS load,
                f.kwh1______ AS kwh_01,
                f.kwh2______ AS kwh_02,
                f.kwh3______ AS kwh_03,
                f.kwh4______ AS kwh_04,
                f.kwh5______ AS kwh_05,
                f.kwh6______ AS kwh_06,
                f.kwh7______ AS kwh_07,
                f.kwh8______ AS kwh_08,
                f.kwh9______ AS kwh_09,
                f.kwh10_____ AS kwh_10,
                f.kwh11_____ AS kwh_11,
                f.kwh12_____ AS kwh_12,
                f.amt1______ AS amt_01,
                f.amt2______ AS amt_02,
                f.amt3______ AS amt_03,
                f.amt4______ AS amt_04,
                f.amt5______ AS amt_05,
                f.amt6______ AS amt_06,
                f.amt7______ AS amt_07,
                f.amt8______ AS amt_08,
                f.amt9______ AS amt_09,
                f.amt10_____ AS amt_10,
                f.amt11_____ AS amt_11,
                f.amt12_____ AS amt_12,
                f.amt13_____ AS amt_13,
                f.bill1_____ AS bill_01,
                f.bill2_____ AS bill_02,
                f.bill3_____ AS bill_03,
                f.bill4_____ AS bill_04,
                f.bill5_____ AS bill_05,
                f.bill6_____ AS bill_06,
                f.bill7_____ AS bill_07,
                f.bill8_____ AS bill_08,
                f.bill9_____ AS bill_09,
                f.bill10____ AS bill_10,
                f.bill11____ AS bill_11,
                f.bill12____ AS bill_12
            ')->from('citygov AS f')
                ->order_by('f.servno____')
                ->get();
            $num_rows = $qry->num_rows();

            if ($num_rows > 0) {
                // CLEAR EXISTING DATA FIRST
                //$this->db->query("TRUNCATE TABLE customer_accounts_main;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_ar;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_name_legacy;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_address;");
                // #########################


                foreach ($qry->result() as $row) {
                    $servno = $row->servno;
                    $mtr = $row->mtr;
                    $name_arr = explode(':', $row->name);
                    $qry_rate = $conn->select('TOP 1 rate______')
                        ->from('citymast')
                        ->where(array('servno____' => $servno, 'mtr_______' => $mtr))
                        ->order_by('dteprt', 'desc')
                        ->get()->row();
                    if($qry_rate) {
                        $acct_main = '';
                        $acct_main_id = 0;
                        if ($qry_rate->rate______ == 'C') {
                            $acct_main = 'CT-STREET';
                            $acct_main_id = 7999;
                        }
                        if ($qry_rate->rate______ == 'G') {
                            $acct_main = 'CT-OFFICE';
                            $acct_main_id = 7997;
                        }
                        if ($qry_rate->rate______ == 'S') {
                            $acct_main = 'CT-SCHOOL';
                            $acct_main_id = 7998;
                        }
                        if ($qry_rate->rate______ == 'M') {
                            $acct_main = 'CT-MARKET';
                            $acct_main_id = 86656;
                        }

                        $qry_main_check = $this->db->select()->from('customer_accounts_main')
                            ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                            ->get()->row();

                        if ($qry_main_check) {
                            // ##############################################################
                            // UPDATE EXISTING ##############################################
                            // GDLBID
                            $acctid = $qry_main_check->sysid;
                            $get_gdlb = $this->db->select('gdlb.sysid')
                                ->from('gdlb_main AS gdlb')
                                ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                                ->where(array('g' => $row->g, 'ads.codes' => $row->d, 'l' => $row->l, 'b' => $row->b))
                                ->get()->row();
                            // RATE CLASS ID
                            $get_rateclassid = $this->db->select('sysid')->from('rate_class_specification')
                                ->where(array('codes' => $row->class))
                                ->get()->row();
                            // MULTCODE ID
                            $get_multcodeid = $this->db->select('sysid')->from('billing_rates_main_multiplier')
                                ->where(array('codes' => $row->multcd))
                                ->get()->row();
                            if ($get_gdlb && $get_rateclassid && $get_multcodeid) {
                                // UPDATE
                                if(count($name_arr) > 1) {
                                    $name_2 = (isset($name_arr[1])) ? $name_arr[1] : '';
                                    $name = $name_arr[0] . ': ' . trim(ucwords(strtolower(utf8_decode($name_2))));
                                }else{
                                    $name = ucwords(strtolower(utf8_decode($row->name)));
                                }

                                $upd_own_arr = array('name' => $name);
                                $this->db->where('sysid', $qry_main_check->ownerid);
                                $upd_owner = $this->db->update('customer_accounts_name_legacy', $upd_own_arr);
                                if ($upd_owner) {
                                    // INSERT MIGRATION HERE
                                    $gdlbid = $get_gdlb->sysid;
                                    $rateclassid = 4;
                                    $multcodeid = $get_multcodeid->sysid;

                                    $contractdate = date('Y-m-d', strtotime($row->contractdate));
                                    $connectdate = date('Y-m-d', strtotime($row->conndate));

                                    $status = ($row->status == 1) ? 1 : 0;

                                    // #############################################################
                                    // UPDATE AR
                                    $upd_ar_arr = array(
                                        'amt_01' => $row->amt_01,
                                        'amt_02' => $row->amt_02,
                                        'amt_03' => $row->amt_03,
                                        'amt_04' => $row->amt_04,
                                        'amt_05' => $row->amt_05,
                                        'amt_06' => $row->amt_06,
                                        'amt_07' => $row->amt_07,
                                        'amt_08' => $row->amt_08,
                                        'amt_09' => $row->amt_09,
                                        'amt_10' => $row->amt_10,
                                        'amt_11' => $row->amt_11,
                                        'amt_12' => $row->amt_12,
                                        'amt_13' => $row->amt_13,
                                        'kwh_01' => $row->kwh_01,
                                        'kwh_02' => $row->kwh_02,
                                        'kwh_03' => $row->kwh_03,
                                        'kwh_04' => $row->kwh_04,
                                        'kwh_05' => $row->kwh_05,
                                        'kwh_06' => $row->kwh_06,
                                        'kwh_07' => $row->kwh_07,
                                        'kwh_08' => $row->kwh_08,
                                        'kwh_09' => $row->kwh_09,
                                        'kwh_10' => $row->kwh_10,
                                        'kwh_11' => $row->kwh_11,
                                        'kwh_12' => $row->kwh_12,
                                        'billno_01' => $row->bill_01,
                                        'billno_02' => $row->bill_02,
                                        'billno_03' => $row->bill_03,
                                        'billno_04' => $row->bill_04,
                                        'billno_05' => $row->bill_05,
                                        'billno_06' => $row->bill_06,
                                        'billno_07' => $row->bill_07,
                                        'billno_08' => $row->bill_08,
                                        'billno_09' => $row->bill_09,
                                        'billno_10' => $row->bill_10,
                                        'billno_11' => $row->bill_11,
                                        'billno_12' => $row->bill_12,
                                    );
                                    $this->db->where(array(
                                        'acctid' => $acctid,
                                        'mtr' => $row->mtr
                                    ));
                                    $this->db->update('customer_accounts_ar', $upd_ar_arr);
                                    $data['error']['ar'][] = array('servno' => $servno, 'msg' => $this->db->_error_message());
                                }
                            }
                        } else {
                            // ##############################################################
                            // INSERT NEW ###################################################
                            // GDLBID
                            // INSERT OWNER INFO LEGACY
                            if(count($name_arr) > 1) {
                                $name_2 = (isset($name_arr[1])) ? $name_arr[1] : '';
                                $name = $name_arr[0] . ': ' . trim(ucwords(strtolower(utf8_decode($name_2))));
                            }else{
                                $name = ucwords(strtolower(utf8_decode($row->name)));
                            }

                            $ins_own_arr = array('name' => $name);
                            $ins_owner = $this->db->insert('customer_accounts_name_legacy', $ins_own_arr);
                            $ownerid = $this->db->insert_id();
                            if ($ins_owner) {
                                // INSERT MIGRATION HERE
                                $rateclassid = 4;

                                $contractdate = date('Y-m-d', strtotime($row->contractdate));
                                $connectdate = date('Y-m-d', strtotime($row->conndate));

                                $status = ($row->status == 1) ? 1 : 0;
                                $ins_acct_arr = array(
                                    'servicenumber' => $servno,
                                    'createdby' => 1,
                                    'datecontract' => $contractdate,
                                    'dateconnected' => $connectdate,
                                    'ownerid' => $ownerid,
                                    'types' => 5,
                                    'gdlb' => 33,
                                    'mtrno' => $row->mtrno,
                                    'mtrserial' => $row->mtrserial,
                                    'mtr' => $row->mtr,
                                    'rateclassid' => $rateclassid,
                                    'multid' => 1,
                                    'status' => $status
                                );

                                //$data['acctinfo'][] = $ins_acct_arr;

                                // INSERT ACCOUNT
                                $ins_acct = $this->db->insert('customer_accounts_main', $ins_acct_arr);
                                $acctid = $this->db->insert_id();


                                if ($ins_acct) {
                                    // INSERT SUB
                                    $subm_arr = array(
                                        'acctmainid' => $acct_main_id,
                                        'acctid' => $acctid,
                                        'mtrno' => $row->mtrno,
                                        'intread' => 0
                                    );
                                    $this->db->insert('customer_accounts_main_submatrix', $subm_arr);

                                    // GET MTRSEQ
                                    $qry_mrseq = $conn->select('mrseq')
                                        ->from('seqtab')
                                        ->where(array('servno' => $servno, 'mtr' => $row->mtr, 'mrseq > ' => 0))
                                        ->get()->row();
                                    $mrseq = ($qry_mrseq) ? $qry_mrseq->mrseq : 0;

                                    // INSERT MRSEQ
                                    $this->db->insert('customer_accounts_mtrseq', array('mrseq' => $mrseq, 'acctid' => $acctid));

                                    // INSERT ADDR INFO IF ACCOUNT CREATED
                                    $ins_addr_arr = array(
                                        'acctid' => $acctid,
                                        'country' => 175,
                                        'addrspecific' => ucfirst(utf8_decode($row->addr))
                                    );
                                    $this->db->insert('customer_accounts_address', $ins_addr_arr);

                                    // INSERT LOAD
                                    $ins_load_arr = array(
                                        'acctid' => $acctid,
                                        'load' => $row->load,
                                        'createdby' => 1
                                    );
                                    $this->db->insert('customer_accounts_load_logs', $ins_load_arr);


                                    $ins_arr = array(
                                        'acctid' => $acctid,
                                        'mtr' => $row->mtr,
                                        'amt_01' => $row->amt_01,
                                        'amt_02' => $row->amt_02,
                                        'amt_03' => $row->amt_03,
                                        'amt_04' => $row->amt_04,
                                        'amt_05' => $row->amt_05,
                                        'amt_06' => $row->amt_06,
                                        'amt_07' => $row->amt_07,
                                        'amt_08' => $row->amt_08,
                                        'amt_09' => $row->amt_09,
                                        'amt_10' => $row->amt_10,
                                        'amt_11' => $row->amt_11,
                                        'amt_12' => $row->amt_12,
                                        'amt_13' => $row->amt_13,
                                        'kwh_01' => $row->kwh_01,
                                        'kwh_02' => $row->kwh_02,
                                        'kwh_03' => $row->kwh_03,
                                        'kwh_04' => $row->kwh_04,
                                        'kwh_05' => $row->kwh_05,
                                        'kwh_06' => $row->kwh_06,
                                        'kwh_07' => $row->kwh_07,
                                        'kwh_08' => $row->kwh_08,
                                        'kwh_09' => $row->kwh_09,
                                        'kwh_10' => $row->kwh_10,
                                        'kwh_11' => $row->kwh_11,
                                        'kwh_12' => $row->kwh_12,
                                        'billno_01' => $row->bill_01,
                                        'billno_02' => $row->bill_02,
                                        'billno_03' => $row->bill_03,
                                        'billno_04' => $row->bill_04,
                                        'billno_05' => $row->bill_05,
                                        'billno_06' => $row->bill_06,
                                        'billno_07' => $row->bill_07,
                                        'billno_08' => $row->bill_08,
                                        'billno_09' => $row->bill_09,
                                        'billno_10' => $row->bill_10,
                                        'billno_11' => $row->bill_11,
                                        'billno_12' => $row->bill_12,
                                    );
                                    $this->db->insert('customer_accounts_ar', $ins_arr);
                                    $data['error'][] = $this->db->_error_message();

                                    $num_exist += 1;
                                } else {
                                    $this->db->insert('customer_migrate_xexist', array('servno' => $servno, 'rem' => 'ACCT'));
                                }

                            }
                        }
                    }
                }
            }
        }

        return json_encode($data);
    }



    function citymast_query($year = false, $month = false) {
        ini_set('memory_limit', '2048M');
        $data = array();
        $err_msg = '';
        $year_input = $this->input->post('year');
        $month_input = $this->input->post('month');

        $year = ($year_input) ? $year_input : $year;
        $month = ($month_input) ? $month_input : $month;

        if($year && $month) {
            //$qry_check = $this->db->select()->from('billing_reports')
            //    ->where(array('month' => $month, 'year' => $year))
            //    ->get()->row();
            //if($qry_check==false) {

            if (pecoapps_conn()) {
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                $query = $conn->select("
                          group_____ AS 'group'
                          ,dist______ AS dist
                          ,lot_______ AS lot
                          ,book______ AS book
                          ,servno____ AS servno
                          ,name______ AS name
                          ,addr______ AS addr
                          ,mtr_______ AS mtr
                          ,m_________ AS month
                          ,yr________ AS year
                          ,prvdte____ AS prvdte
                          ,prsdte____ AS prsdte
                          ,duedate___ AS duedate
                          ,load______ AS load
                          ,rate______ AS rate
                          ,prvrdg____ AS prvrdg
                          ,prsrdg____ AS prsrdg
                          ,multcd____ AS multcd
                          ,kwhuse____ AS kwhuse
                          ,genamt____ AS genamt
                          ,genchg____ AS genchg
                          ,trnamt____ AS trnamt
                          ,trnchg____ AS trnchg
                          ,disamt____ AS disamt
                          ,dischg____ AS dischg
                          ,demamt____ AS demamt
                          ,supamt____ AS supamt
                          ,supchg____ AS supchg
                          ,supper____ AS supper
                          ,mtramt____ AS mtramt
                          ,mtrchg____ AS mtrchg
                          ,mtrper____ AS mtrper
                          ,slamt_____ AS slamt
                          ,slchg_____ AS slchg
                          ,iccamt____ AS iccamt
                          ,iccsub____ AS iccsub
                          ,llramt____ AS llramt
                          ,llrsub____ AS llrsub
                          ,lldamt____ AS lldamt
                          ,misamt____ AS misamt
                          ,mischg____ AS mischg
                          ,envamt____ AS envamt
                          ,envchg____ AS envchg
                          ,framt_____ AS framt
                          ,genvat____ AS genvat
                          ,trnvat____ AS trnvat
                          ,disvat____ AS disvat
                          ,slvat_____ AS slvat
                          ,othvat____ AS othvat
                          ,appsur____ AS appsur
                          ,surbal____ AS surbal
                          ,current___ AS 'current'
                          ,overdue___ AS overdue
                          ,totacc____ AS totacc
                          ,totint____ AS totint
                          ,dolpay____ AS dolpay
                          ,cntapp____ AS cntapp
                          ,billno
                          ,moyr
                          ,ctrlinc
                          ,genamt1___ AS genamt1
                          ,genchg1___ AS genchg1
                          ,papc
                          ,papcchg
                          ,mtrser____ AS mtrser
                          ,serial____ AS serial
                          ,npcchg____ AS npcchg
                          ,npcamt____ AS npcamt
                          ,iccschg
                          ,iccsamt
                          ,fitchg
                          ,fitamt
                          ,dteprt
                        ")
                    ->from('citymast')
                    ->where(
                        array(
                            'yr________' => $year,
                            'm_________' => $month,
                            //'ctrlinc' => 5395192
                            //'servno____' => 'J11954'
                        )
                    )->get();
                // @TODO REMOVE TRUNCATE IF SCRIPT IS FINAL
                // TRUNCATE TABLE
                // $this->db->query("TRUNCATE TABLE billing_reports");

                $data['numrows'] = $query->num_rows();

                if ($query->num_rows() > 0) {
                    $ins_num = 0;
                    $err_msg .= 'Number of Records: ' . $query->num_rows() . '<br>';

                    // DELETE RECORDS
                    $this->db->query("DELETE FROM billing_reports WHERE `year` = $year AND `month` = $month");

                    foreach ($query->result() as $row) {
                        $qry_acctid = $this->db->select('sysid')->from('customer_accounts_main')
                            ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                            ->get()->row();
                        $acctid = ($qry_acctid) ? $qry_acctid->sysid : 0;



                        $date_str_prv = trim($row->prvdte);

                        $dt_explode_prv = explode('/', $date_str_prv);
                        if(!empty($dt_explode_prv[0]) &&!empty($dt_explode_prv[1]) && !empty($dt_explode_prv[2])) {

                            $old_prvdte = DateTime::createFromFormat('m/d/y', $date_str_prv);

                            if (!empty($date_str_prv)) {
                                $old_prvdte = DateTime::createFromFormat('m/d/Y', $date_str_prv);
                                if ($old_prvdte && $old_prvdte->format('Y') >= 1900) {
                                    $prvdte = $old_prvdte->format('Y-m-d');
                                } else {
                                    $old_prvdte = DateTime::createFromFormat('m/d/y', $date_str_prv);
                                    $prvdte = $old_prvdte->format('Y-m-d');
                                }
                            } else {
                                $prvdte = '1900-01-01';
                            }
                        }else{
                            $prvdte = '1900-01-01';
                        }


                        $date_str_prs = trim($row->prsdte);

                        $dt_explode_prs = explode('/', $date_str_prs);
                        if(!empty($dt_explode_prs[0]) &&!empty($dt_explode_prs[1]) && !empty($dt_explode_prs[2])) {

                            $old_prsdte = DateTime::createFromFormat('m/d/y', $date_str_prs);

                            if (!empty($date_str_prs)) {
                                $old_prsdte = DateTime::createFromFormat('m/d/Y', $date_str_prs);
                                if ($old_prsdte && $old_prsdte->format('Y') >= 1900) {
                                    $prsdte = $old_prsdte->format('Y-m-d');
                                } else {
                                    $old_prsdte = DateTime::createFromFormat('m/d/y', $date_str_prs);
                                    $prsdte = $old_prsdte->format('Y-m-d');
                                }
                            } else {
                                $prsdte = '1900-01-01';
                            }
                        }else{
                            $prsdte = '1900-01-01';
                        }


                        if (!empty(trim($row->duedate))) {
                            $old_duedate = DateTime::createFromFormat('m/d/Y', trim($row->duedate));
                            if ($old_duedate) {
                                $duedate = $old_duedate->format('Y-m-d');
                            } else {
                                $duedate = '1900-01-01';
                            }
                        } else {
                            $duedate = '1900-01-01';
                        }

                        if (!empty(trim($row->dteprt))) {
                            $t = strtotime($row->dteprt);
                            $dteprt = date('Y-m-d H:i:s', $t);
                        } else {
                            $dteprt = '1900-01-01';
                        }

                        if (!empty(trim($row->dolpay))) {
                            $t = strtotime($row->dolpay);
                            $dolpay = date('Y-m-d', $t);
                        } else {
                            $dolpay = '1900-01-01';
                        }

                        $billno = (!empty(trim($row->billno))) ? $row->billno : 0;
                        $name = ($row->name) ? ucwords(utf8_decode(trim($row->name))) : ' ';
                        $addr = ($row->addr) ? ucwords(utf8_decode(trim($row->addr))) : ' ';
                        $moyr = trim($row->moyr);
                        if($moyr!='') {
                            $moyr_arr = explode('-', $moyr);
                            $bmo = (isset($moyr_arr[0])) ? $moyr_arr[0] : 0;
                            $byr = (isset($moyr_arr[1])) ? $moyr_arr[1] : 0;
                        }else{
                            $bmo = null;
                            $byr = null;
                        }



                        $ins_arr = array(
                            'billno' => $billno,
                            'acctid' => $acctid,
                            'group' => ($row->group) ? $row->group : 0,
                            'dist' => ($row->dist) ? trim($row->dist) : '',
                            'lot' => ($row->lot) ? $row->lot : 0,
                            'book' => (trim($row->book) != '') ? trim($row->book) : 0,
                            'servno' => trim($row->servno),
                            'mtr' => $row->mtr,
                            'mtrser' => (trim($row->mtrser) != '') ? trim($row->mtrser) : 0,
                            'serial' => trim($row->serial),
                            'name' => $name,
                            'addr' => $addr,
                            'bmo' => $bmo,
                            'byr' => $byr,
                            'month' => $row->month,
                            'year' => $row->year,
                            'prvdte' => $prvdte,
                            'prsdte' => $prsdte,
                            'duedate' => $duedate,
                            'load' => $row->load,
                            'rate' => $row->rate,
                            'prvrdg' => $row->prvrdg,
                            'prsrdg' => $row->prsrdg,
                            'multcd' => $row->multcd,
                            'kwhuse' => $row->kwhuse,
                            'genamt' => $row->genamt,
                            'genamt1' => $row->genamt1,
                            'trnamt' => $row->trnamt,
                            'disamt' => $row->disamt,
                            'demamt' => $row->demamt,
                            'supamt' => $row->supamt,
                            'supper' => $row->supper,
                            'mtramt' => $row->mtramt,
                            'slamt' => $row->slamt,
                            'iccamt' => $row->iccamt,
                            'iccsub' => $row->iccsub,
                            'llramt' => $row->llramt,
                            'llrsub' => $row->llrsub,
                            'lldamt' => $row->lldamt,
                            'misamt' => $row->misamt,
                            'envamt' => $row->envamt,
                            'framt' => $row->framt,
                            'npcamt' => $row->npcamt,
                            'iccsamt' => $row->iccsamt,
                            'papc' => $row->papc,
                            'fitamt' => $row->fitamt,
                            'genchg' => $row->genchg,
                            'genchg1' => $row->genchg1,
                            'trnchg' => $row->trnchg,
                            'dischg' => $row->dischg,
                            'demchg' => 0,
                            'supchg' => $row->supchg,
                            'mtrchg' => $row->mtrchg,
                            'mtrper' => $row->mtrper,
                            'slchg' => $row->slchg,
                            'mischg' => $row->mischg,
                            'envchg' => $row->envchg,
                            'npcchg' => $row->npcchg,
                            'iccschg' => $row->iccschg,
                            'fitchg' => $row->fitchg,
                            'papcchg' => $row->papcchg,
                            'genvat' => $row->genvat,
                            'trnvat' => $row->trnvat,
                            'disvat' => $row->disvat,
                            'slvat' => $row->slvat,
                            'othvat' => $row->othvat,
                            'appsur' => $row->appsur,
                            'surbal' => $row->surbal,
                            'current' => $row->current,
                            'overdue' => $row->overdue,
                            'totacc' => $row->totacc,
                            'totint' => $row->totint,
                            'dolpay' => $dolpay,
                            'dteprt' => $dteprt,
                            'batch' => 'CTGOV',
                            'createdby' => 1
                        );
                        $ins = true;
                        $ins = $this->db->insert('billing_reports', $ins_arr);
                        if ($ins) {
                            //$err_msg .= '<br><b>Ok</b> == ' . $this->db->insert_id() . '<br>';
                            $ins_num += 1;
                        } else {
                            $err_msg .= '<br><b>Er: </b> ' . $this->db->_error_message() . ' == ' . $this->db->insert_id() . '<br>';
                            $this->db->insert('billing_reports_ins_error', array('ctrlinc' => $row->ctrlinc));
                        }
                    }
                    $err_msg .= 'Inserted: ' . $ins_num;
                }

            } else {
                return false;
            }
            //}else{
            //    $err_msg = 'Billing period provided is existing already!';
            //}
        }else{
            $err_msg = 'Please provide Year/Month';
        }
        $data['curr'] = $year . ' / ' . $month;
        $data['msg'] = $err_msg;
        echo json_encode($data);
    }


    function update_ct_details() {
        ini_set('MAX_EXECUTION_TIME', -1);
        $data = array();
        $msg = '';
        $num_exist = 0;
        $num_insert = 0;
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $qry = $conn->select('
                LTRIM(RTRIM(f.servno____)) AS servno,
                LTRIM(RTRIM(f.group_____)) AS g,
                LTRIM(RTRIM(f.dist______)) AS d,
                LTRIM(RTRIM(f.lot_______)) AS l,
                LTRIM(RTRIM(f.book______)) AS b,
                f.mtr_______ AS mtr,
                LTRIM(RTRIM(f.class_____)) AS class,
                LTRIM(RTRIM(f.multcd____)) AS multcd,
                LTRIM(RTRIM(f.name______)) AS name,
                LTRIM(RTRIM(f.addr______)) AS addr,
                LTRIM(RTRIM(f.condte____)) AS contractdate,
                LTRIM(RTRIM(f.status____)) AS status,
                LTRIM(RTRIM(f.stadte____)) AS conndate,
                LTRIM(RTRIM(f.mtrser____)) AS mtrno,
                LTRIM(RTRIM(f.serial____)) AS mtrserial,
                f.load______ AS load,
                f.kwh1______ AS kwh_01,
                f.kwh2______ AS kwh_02,
                f.kwh3______ AS kwh_03,
                f.kwh4______ AS kwh_04,
                f.kwh5______ AS kwh_05,
                f.kwh6______ AS kwh_06,
                f.kwh7______ AS kwh_07,
                f.kwh8______ AS kwh_08,
                f.kwh9______ AS kwh_09,
                f.kwh10_____ AS kwh_10,
                f.kwh11_____ AS kwh_11,
                f.kwh12_____ AS kwh_12,
                f.amt1______ AS amt_01,
                f.amt2______ AS amt_02,
                f.amt3______ AS amt_03,
                f.amt4______ AS amt_04,
                f.amt5______ AS amt_05,
                f.amt6______ AS amt_06,
                f.amt7______ AS amt_07,
                f.amt8______ AS amt_08,
                f.amt9______ AS amt_09,
                f.amt10_____ AS amt_10,
                f.amt11_____ AS amt_11,
                f.amt12_____ AS amt_12,
                f.amt13_____ AS amt_13,
                f.bill1_____ AS bill_01,
                f.bill2_____ AS bill_02,
                f.bill3_____ AS bill_03,
                f.bill4_____ AS bill_04,
                f.bill5_____ AS bill_05,
                f.bill6_____ AS bill_06,
                f.bill7_____ AS bill_07,
                f.bill8_____ AS bill_08,
                f.bill9_____ AS bill_09,
                f.bill10____ AS bill_10,
                f.bill11____ AS bill_11,
                f.bill12____ AS bill_12
            ')->from('citygov AS f')
                ->order_by('f.servno____')
                ->get();
            $num_rows = $qry->num_rows();

            echo 'Records: ' . $num_rows . '<br>';
            echo '<hr>';

            $valid_records = 0;
            if($num_rows>0) {
                foreach($qry->result() as $row) {

                    $servno = $row->servno;
                    $mtr = $row->mtr;
                    // CHECK AND UPDATE FIRST
                    $qry_main_check = $this->db->select()->from('customer_accounts_main')
                        ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                        ->get()->row();

                    if($qry_main_check) {
                        $acctid = $qry_main_check->sysid;
                        $get_gdlb = $this->db->select('gdlb.sysid')
                            ->from('gdlb_main AS gdlb')
                            ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                            ->where(array('g' => $row->g, 'ads.codes' => $row->d, 'l' => $row->l, 'b' => $row->b))
                            ->get()->row();
                        // RATE CLASS ID
                        $get_rateclassid = $this->db->select('sysid')->from('rate_class_specification')
                            ->where(array('codes' => $row->class))
                            ->get()->row();
                        // MULTCODE ID
                        $get_multcodeid = $this->db->select('sysid')->from('billing_rates_main_multiplier')
                            ->where(array('codes' => $row->multcd))
                            ->get()->row();
                        if ($get_rateclassid && $get_multcodeid) {
                            $this->db->where(array('sysid' => $acctid));
                            $upd = $this->db->update('customer_accounts_main', array(
                                'rateclassid' => $get_rateclassid->sysid,
                                'multid' => $get_multcodeid->sysid,
                            ));
                            if($upd) {
                                $valid_records += 1;
                            }
                        }
                    }
                }
            }

            echo '<hr>';
            echo 'Valid Records: ' . $valid_records . '<br>';

        }

    }

    function select2_pecoapps_users() {
        $data = array();
        if(pecoappsdev_conn()) {
            $conn = $this->load->database('pecoappsdev', TRUE);
            $conn->initialize();
            $res = $conn->query("SELECT * FROM tblUsers WHERE password != 'D0N0TD3L3T3'");
            if ($res->num_rows() > 0) {
                foreach ($res->result() as $row) {
                    $data['list'][] = array(
                        'id' => $row->USERID,
                        'text' => $row->USERNAME . ' - ' . $row->LASTNAME
                    );
                }
            }
        }else{
            $data['db'] = 'Not connected!';
        }
        return json_encode($data);
    }

    function update_legacy_username(){
        $data = array();

        $dataid = $this->input->post('dataid');
        $telcode = $this->input->post('telcode');

        $this->db->trans_begin();
        $this->db->where(array('userid' => $dataid, 'status' => 1));
        $this->db->update('prime_system_users_legacy_code',array('status' => 0, 'updatedby' => user_id()));

        $ins_arr = array(
            'telcode' => $telcode,
            'userid' => $dataid,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert('prime_system_users_legacy_code', $ins_arr);
        $data['error'] = $this->db->_error_message();
        $data = db_trans($this->db);
        return json_encode($data);
    }

    function add_new_item() {
        $data = array();
        $title = SYSTEM_NAME;
        $msg = '';
        $func = 'success';

        $component = $this->input->post('components');
        $specification = $this->input->post('specifications');
        $corpname = $this->input->post('corpname');
        $corpbranch = $this->input->post('corpbranch');
        $amount = $this->input->post('amount');
        $unit = $this->input->post('unit');


        $corp_data = create_corporation_data();
        $corpb_id = ($corp_data && isset($corp_data->corpbid)) ? $corp_data->corpbid : null;
        $catid = create_item_category();

        $lqry = array();

        // ##############################################################
        // ################# COMPONENTS ALG
        if($component && $component != '') {
            $q = $this->db->query("
                SELECT * FROM items_main_components
                WHERE catid = $catid AND (`codes` LIKE '%$component%' OR `names` LIKE '%$component%')
            ")->row();
            $lqry[] = $this->db->last_query();

            if ($q) {
                $data['cid'] = 'x' . $q->sysid;
                $this->db->where('sysid', $q->sysid);
                $this->db->update('items_main_components',
                    array(
                        'codes' => get_acronym($component),
                        'names' => $component,
                        'desc' => $component
                    ),
                    array(
                        'sysid' => $q->sysid
                    )
                );
                $msg .= 'Component Updated! ';
            } else {
                $this->db->insert('items_main_components', array(
                        'catid' => $catid,
                        'codes' => get_acronym($component),
                        'names' => $component,
                        'desc' => $component
                    )
                );
                $cid = $this->db->insert_id();
                $data['cid'] = $cid;
                $msg .= 'New Component Updated! ';
            }
        } else {
            $func = 'error';
            $msg  = 'Component is empty!';
        }

        // ##############################################################
        // ################# ITEM SPECIFIC ALG
        $itemspecid = false;
        if($specification && $specification != '') {
            $q = $this->db->query("
                SELECT * FROM items_main_spec
                WHERE itemid = $catid AND (`codes` LIKE '%$specification%' OR `names` LIKE '%$specification%')
            ")->row();
            $lqry[] = $this->db->last_query();
            if ($q == false) {
                $this->db->insert('items_main_spec', array(
                        'itemid' => $catid,
                        'codes' => get_acronym($specification),
                        'names' => $specification,
                        'descs' => $specification,
                        'unitid' => $unit,
                    )
                );
                $itemspecid = $this->db->insert_id();
                $msg .= ' New Item\'s specification added! ';
            } else {
                $itemspecid = $q->sysid;
                $msg .= ' Same Item\'s specification! ';
            }
        } else {
            $func = 'error';
            $msg  = 'Item Specification is empty!';
        }



        // ##############################################################
        // ################# ITEM QUOTATION QUERY trn_prs_quotations
        if($itemspecid > 0) {
            $this->db->update('trn_prs_quotations',
                array(
                    'status' => 0
                ),
                array(
                    'itemspecid' => $itemspecid,
                    'suppid' => $corpb_id,
                    'status' => 1
                )
            );

            $ins_eprs_arr = array(
                'itemspecid' => $itemspecid,
                'suppid' => $corpb_id,
                'amt' => $amount,
                'createdby' => user_id()
            );
            $this->db->insert("trn_prs_quotations", $ins_eprs_arr);
            $data['err'] = $this->db->_error_message();
            $lqry[] = $this->db->last_query();
        }


        $data['input'] = $this->input->post();
        $data['corpbid'] = $corpb_id;
        $data['catid'] = $catid;
        $data['title'] = $title;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['lqry'] = $lqry;
        return json_encode($data);
    }


    function get_select2_units() {
        $data = array();
        $qry = $this->db->select()->from('prime_types_parameter')
            ->where(array('codes' => 'ITEMUNITS'))
            ->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc
                );
            }
        }
        return json_encode($data);
    }

    function get_trn_comments() {
        $data = array();
        $trnid = $this->input->post('trnid');
        $sql = $this->db->select()
            ->from('transaction_request_trails_comments')
            ->where(array('trnid' => $trnid, 'status' => 1))
            ->order_by('datecreated', 'desc')
            ->limit(8)
            ->get();
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $user_person_info = get_user_person($row->createdby);
                if($user_person_info) {
                    $person_info =  get_person_info($user_person_info->sysid);
                    if($person_info->qry == true) {
                        $pic = $person_info->info->pics;
                        $name = $person_info->info->firstname . ' ' . $person_info->info->lastname;
                    }else{
                        $user_info = get_users_info($row->createdby, true);
                        if($user_info) {
                            $pic = get_users_pic_url($row->createdby);
                            $name = $user_info->firstname . ' ' . $user_info->lastname;
                        }else {
                            $pic = base_url('assets/global/img/person_default.jpg');
                            $name = SYSTEM_NAME;
                        }
                    }
                }else{
                    $user_info = get_users_info($row->createdby, true);
                    if($user_info) {
                        $pic = get_users_pic_url($row->createdby);
                        $name = $user_info->firstname . ' ' . $user_info->lastname;
                    }else {
                        $pic = base_url('assets/global/img/person_default.jpg');
                        $name = SYSTEM_NAME;
                    }
                }

                $data['list'][] = array(
                    'id' => $row->sysid,
                    'name' => $name,
                    'pic' => $pic,
                    'date' => $row->datecreated,
                    'message' => $row->remarks,
                    'del' => ($row->createdby==user_id() || super_admin()) ? true : false
                );
            }

        }
        return json_encode($data);
    }

    function submit_trn_comment() {
        $data = array();

        $user_person_info = get_user_person(user_id());
        if($user_person_info) {
            $person_info =  get_person_info($user_person_info->sysid);
            if($person_info->qry == true) {
                $pic = $person_info->info->pics;
                $name = $person_info->info->firstname . ' ' . $person_info->info->lastname;
            }else{
                $user_info = get_users_info(user_id(), true);
                if($user_info) {
                    $pic = get_users_pic_url(user_id());
                    $name = $user_info->firstname . ' ' . $user_info->lastname;
                }else {
                    $pic = base_url('assets/global/img/person_default.jpg');
                    $name = SYSTEM_NAME;
                }
            }
        }else{
            $user_info = get_users_info(user_id(), true);
            if($user_info) {
                $pic = get_users_pic_url(user_id());
                $name = $user_info->firstname . ' ' . $user_info->lastname;
            }else {
                $pic = base_url('assets/global/img/person_default.jpg');
                $name = SYSTEM_NAME;
            }
        }
        $message = $this->input->post('remarks');
        $trnid = $this->input->post('trnid');
        $trailid = $this->input->post('trailid');

        $date = date('Y-m-d H:i:s');

        $this->db->trans_begin();
        $ins_arr = array(
            'trnid' => $trnid,
            'trailid' => $trailid,
            'remarks' => $message,
            'createdby' => user_id(),
            'updatedby' => user_id(),
        );
        $this->db->insert('transaction_request_trails_comments', $ins_arr);
        $err = $this->db->_error_message();
        $data = db_trans($this->db);

        $data['del'] = true;
        $data['err'] = $err;
        $data['pic'] = $pic;
        $data['name'] = $name;
        $data['date'] = $date;
        $data['message'] = $message;

        return json_encode($data);
    }

    function delete_trn_comment() {
        $id = $this->input->post('id');
        $this->db->trans_begin();
        $this->db->update('transaction_request_trails_comments', array('status' => 0), array('sysid' => $id));
        $data = db_trans($this->db);
        return json_encode($data);
    }

    function upload_profile_picture() {

        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $image = false;

        $moduleid = $this->input->post('moduleid');
        $ownerid = $this->input->post('ownerid');
        $dataid = $this->input->post('dataid');
        $dir = $this->input->post('dir');
        $input_remarks = $this->input->post('remarks');
        $remarks = ($input_remarks && $input_remarks!='') ? ' | ' .$input_remarks : '';

        $pic_recent = get_owner_pic($ownerid, $dir, 2);
        if(isset($_FILES["newpic"])) {
            $qry_time = $this->db->query("SELECT HOUR(NOW()) AS HRS, MINUTE(NOW()) AS MIN, SECOND(NOW()) AS SEC")->row();
            $hrs = str_pad($qry_time->HRS, 2, '0', STR_PAD_LEFT);
            $min = str_pad($qry_time->MIN, 2, '0', STR_PAD_LEFT);
            $sec = str_pad($qry_time->SEC, 2, '0', STR_PAD_LEFT);
            $hour_num = $hrs . $min . $sec;



            $temp = explode(".", $_FILES["newpic"]["name"]);
            $newfilename = 'primary_' . date('Y') . str_pad(date('m'), 2, '0', STR_PAD_LEFT) . str_pad(date('d'), 2, '0', STR_PAD_LEFT) . $hour_num . '.' . end($temp);
            $file_directory = FCPATH . "uploads/$dir/$ownerid/";
            //  $file_directory = "net use z:\\\\172.20.224.15cad\\attachedments\\" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";
            // ###############################################
            // CREATE DIRECTORY
            $config['overwrite'] = TRUE;
            $config['upload_path'] = $file_directory;
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 10000;
            $config['max_width'] = 5000;
            $config['max_height'] = 8000;
            $config['encrypt_name'] = FALSE;
            $config['file_name'] = $newfilename;
            $this->load->library('upload', $config);

            // ###############################################
            // CREATE DIRECTORY
            if (!is_dir($file_directory)) {
                mkdir($file_directory, 0777, TRUE);
                //chmod($file_directory, 0777);
            }
            // ###############################################

            if (!$this->upload->do_upload('newpic')) {
                $msg = "Upload error";
                $qry = false;
                $func = 'error';
            } else {
                $msg = "Profile Picture Updated";
                $qry = true;
                $func = 'success';
                $image = base_url() . "uploads/$dir/$ownerid/" . $newfilename;

                // ##################################
                // AUDIT LOG ########################
                $audit_ins_arr = array(
                    'dataid' => $ownerid,
                    'moduleid' => $moduleid,
                    'valueold' => $pic_recent,
                    'valuenew' => $image,
                    'createdby' => user_id(),
                    'remarks' => 'UPDATE '.strtoupper($dir).' PICTURE | DATAID: ' . $dataid . $remarks
                );
                audit_insert($audit_ins_arr);
            }

        }else{
            $msg = 'Drop the file again!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func']  = $func;
        $data['image'] = $image;
        return json_encode($data);
    }

    function send_basic_email() {
        $data = array();

        $msg = SYSTEM_MSG_DEFAULT;
        $title = SYSTEM_NAME;
        $func = 'warning';

        $to = $this->input->post('to');
        $from = $this->input->post('from');
        $message = $this->input->post('message');
        $subject = $this->input->post('subject');

        if(mailer($to, $message, $subject, false, $from)) {
            $msg = 'Message sent to : ' . $to;
            $title = $subject;
            $func = 'warning';
        }


        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function get_transaction_flow_stages() {
        $data = array();
        $input_data = $this->input->post('data');
        $flowid = $input_data['flowid'];
        $sql_stages = $this->db->select()
            ->from('prime_transaction_flow_main_stages')
            ->where(array('flowid' => $flowid, 'status' => 1))
            ->order_by('levels')
            ->get();
        if($sql_stages->num_rows()>0) {
            foreach($sql_stages->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc
                );
            }
        }

        return json_encode($data);
    }

    function select2_panel_type() {
        $data = array();
        $sql = $this->db->query("SELECT * FROM solar_panel_types WHERE status = 1");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->descs
                );
            }
        }
        return json_encode($data);
    }

    function select2_region() {
        $data = array();
        $sql = $this->db->query("SELECT * FROM address_region");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->descs
                );
            }
        }
        return json_encode($data);
    }


    function select2_province() {
        $data = array();
        $regionid = $this->input->post('regionid');

        if($regionid) {
            $region_details = $this->db->select()->from('address_region')
                ->where(array('sysid' => $regionid))
                ->get()->row();

            if($region_details) {
                $sql = $this->db->query("SELECT * FROM address_province WHERE regcode = '{$region_details->regcode}'");
                if ($sql->num_rows() > 0) {
                    foreach ($sql->result() as $row) {
                        $data['list'][] = array(
                            'id' => $row->sysid,
                            'text' => $row->descs
                        );
                    }
                }
            }
        }
        return json_encode($data);
    }


    function select2_citymun() {
        $data = array();
        $provid = $this->input->post('provid');

        if($provid) {
            $prov_details = $this->db->select()->from('address_province')
                ->where(array('sysid' => $provid))
                ->get()->row();

            if($prov_details) {
                $sql = $this->db->query("SELECT * FROM address_citymun WHERE provcode = '{$prov_details->provcode}'");
                if ($sql->num_rows() > 0) {
                    foreach ($sql->result() as $row) {
                        $data['list'][] = array(
                            'id' => $row->sysid,
                            'text' => $row->descs
                        );
                    }
                }
            }
        }
        return json_encode($data);
    }

}