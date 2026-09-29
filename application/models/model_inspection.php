<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_inspection extends CI_Model {

    function get_transaction_details() {
        $query = $this->db->query("SELECT ao.firstname applicant_fn, ao.lastname applicant_ln, trm.dataid, trm.trncode, trm.codes, trm.validations, em.lastname employee_ln, em.firstname employee_fn, trm.descriptions, trm.datecreated, trm.status FROM transaction_request_main AS trm "
            . "LEFT JOIN prime_employee_main AS em "
            . "ON trm.createdby = em.sysid "
            . "LEFT JOIN prime_customer_accounts_owners AS ao "
            . "ON trm.dataid = ao.accountid "
            . "WHERE trm.moduleid = 9 ORDER BY trm.datecreated DESC");
        return ( $query ) ? $query->result() : false;
    }

    function get_hashcode($moduleid) {
        $sql = "SELECT navm.hashcode FROM transaction_request_main AS trn "
            . "LEFT JOIN prime_module_navigations_main AS navm "
            . "ON navm.sysid = trn.moduleid WHERE trn.moduleid = ?";
        $query = $this->db->query($sql, array($moduleid));
        return ( $query ) ? $query->row() : false;
    }

    //@TODO BREAK
    function get_select_equipments() {
        $data = array();
        $qry = $this->db->select()->from('prime_equipments_parameters')->get();
        $data['list'][] = array('id' => 0, 'text' => 'LOAD - With Electrical Load Specification');

        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->names . ' - ' . $row->descs);
            }

        }

        return json_encode($data);
    }

    function get_account_types() {
        $query = $this->db->query("SELECT names, sysid FROM prime_types_parameter WHERE codes = 'CAPPS'");
        return ( $query ) ? $query->result() : false;
    }

    function get_account_type_name($sysid) {
        $sql = "SELECT names FROM prime_types_parameter WHERE sysid = ?";
        $query = $this->db->query($sql, array($sysid));
        return ( $query ) ? $query->row() : false;
    }

    function get_city() {
        $query = $this->db->query("SELECT sysid, names FROM address_city");
        return ( $query ) ? $query->result() : false;
    }

    function get_city_name($sysid) {
        $sql = "SELECT names FROM prime_city WHERE sysid = ?";
        $query = $this->db->query($sql, array($sysid));
        return ( $query ) ? $query->row() : false;
    }

    function get_district() {
        $query = $this->db->query("SELECT sysid, names FROM address_districts");
        return ( $query ) ? $query->result() : false;
    }

    function get_district_name($sysid) {
        $sql = "SELECT names FROM address_districts WHERE sysid = ?";
        $query = $this->db->query($sql, array($sysid));
        return ( $query ) ? $query->row() : false;
    }

    function get_user_name($accountid = '') {
        $sql = "SELECT * FROM prime_customer_accounts_owners WHERE accountid = ?";
        $query = $this->db->query($sql, array($accountid));
        return ( $query ) ? $query->row() : false;
    }

    function add_equipment_data($data) {
        $rate_class_id = $data['rateClass'];
        $inspection_date = $data['inspDate'];
        $account_type = $data['accountType'];
        $district = $data['district'];
        $city = $data['city'];
        $specific_address = $data['specificAddress'];
        $trn = $data['trn'];
        $x = $data['latitude'];
        $y = $data['longitude'];
        $accountid = $data['accountID'];
        $total_load = 0;

        //query strings start
        $sql_eq_values = "SELECT watts, qty FROM prime_customer_accounts_equipments WHERE accountid = ? AND status = ?";
        $sql_ao = "SELECT sysid FROM prime_customer_accounts_owners WHERE accountid = ?";
        $sql_oa = "INSERT INTO prime_customer_accounts_owners_address (ownerid, district, city, country, addrspecific, addrmapx, addrmapy) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sql_am = "UPDATE prime_customer_accounts_main SET types=? WHERE sysid = ?";
        $sql_om_in = "INSERT INTO prime_customer_accounts_owners_meter (accountid, ownerid, rateid, createdby, status) VALUES (?, ?, ?, ?, ?)";
        $sql_om = "SELECT sysid FROM prime_customer_accounts_owners_meter WHERE accountid = ?";
        $sql_al = "INSERT INTO prime_customer_accounts_logs (accountid, logtype, logamount, meterid, logdate, createdby, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sql_rm = "UPDATE transaction_request_main SET status = ?, stagesid = ? WHERE trncode = ?";
        //query strings end
        $ownerid = $this->db->query($sql_ao, array($accountid))->row()->sysid;
        $eq_values = $this->db->query($sql_eq_values, array($accountid, 1))->result();
        //deposit cost computation start
        foreach ($eq_values as $val) {
            $total_load += $val->watts * $val->qty;
        }
        $row = $this->retrieve_rate_data($rate_class_id);
        $deposit_cost = $this->deposit_cost($total_load, $row->dailyops, $row->monthlyops, $row->demand, $row->rates);
        //deposit cost computation end
        $this->db->trans_start(); //using transactions means that all the queries must be successful before committing the data to database.
        //query bindings automatically escapes mysql data, no need for manually escaping data before querying
        //no need to specify that the data as string (e.g. '$specific_address') during the query.
        $this->db->query($sql_oa, array($ownerid, $district, $city, 175, $specific_address, $x, $y));
        $this->db->query($sql_am, array($account_type, $accountid));
        $this->db->query($sql_om_in, array($accountid, $ownerid, $rate_class_id, 1, 1));
        $this->db->query($sql_al, array($accountid, 12, round($deposit_cost, 2), $this->db->query($sql_om, array($accountid))->row()->sysid, $inspection_date, 1, 1));
        $this->db->query($sql_rm, array(1, 4, $trn));
        return $this->db->trans_complete();

        //return ($affected_rows > 0) ? true : false;
        //return $affected_rows;
    }

    function edit_data($dataid) {
        $sql = "SELECT logdate, tp.names account_type_name, types account_type_id, oa.sysid address_id, district district_id, d.names district_name, city city_id, c.names city_name, addrspecific, classifications rate_name, cm.sysid rate_id FROM prime_customer_accounts_logs al "
            . "LEFT JOIN prime_customer_accounts_main am "
            . "ON al.accountid = am.sysid "
            . "LEFT JOIN prime_customer_accounts_owners_address oa "
            . "ON al.accountid = (SELECT accountid FROM prime_customer_accounts_owners WHERE sysid = oa.ownerid) "
            . "LEFT JOIN prime_types_parameter tp "
            . "ON am.types = tp.sysid "
            . "LEFT JOIN address_districts d "
            . "ON oa.district = d.sysid "
            . "LEFT JOIN address_city c "
            . "ON oa.city = c.sysid "
            . "LEFT JOIN prime_system_rate_class_main cm "
            . "ON cm.sysid = (SELECT rateid FROM prime_customer_accounts_owners_meter WHERE accountid = al.accountid AND sysid = al.meterid) "
            . "WHERE al.accountid = ?";
        $query = $this->db->query($sql, array($dataid));
        return ( $query ) ? $query->row() : false;
    }

    function get_account_map() {
        $id = $this->input->post('id');
        $query = false;
        $qry = false;
        $query = $this->db->select()->from('ticketing_details_logs')
            ->where(array('sysid' => $id))
            ->get()->row();

        if($query){
            $data = array(
                'lat' => $query->maplat,
                'lon' => $query->maplng,
                'alt' => 18,
            );
            $qry = true;
        }
        $data['input'] = $this->input->post();
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function init_equipment_data() {
        $dataid = $this->input->post('dataid');
        $page = $this->input->post('page');
        $display_state = '';
        $data = array();
        if ($page == 'view') { //viewing only, editing is disabled
            $display_state = 'disabled';
        }
        $qry = $this->db->select('
            e.sysid, 
            ep.codes, 
            e.watts, 
            e.qty, 
            e.equipid, 
            ep.codes, 
            ep.descs, 
            e.remarks, 
            ep.types
            ')
            ->from("application_customers_equipments e")
            ->join('prime_equipments_parameters AS ep', 'ep.sysid = e.equipid', 'left')
            ->where(array('e.appid' => $dataid, 'e.status' => 1 , 'e.logid' => 0))
            ->get();
        $num_rows = $qry->num_rows();
        $total_qty = 0;
        $total_wtt = 0;
        $total_amp = 0;
        if ($num_rows > 0) {
            $num = 1;
            foreach ($qry->result() as $row) {
                $check_grd = check_acct_gdr($dataid);
                if($check_grd) {
                    $del_btn = '<i class="fa fa-check text-success"></i>';
                }else {
                    $del_btn = '<a class="btn btn-xs" id="btn_del" data-id="' . $row->sysid . '"><i class="fa fa-times text-danger"></i></a>';
                }
                if($row->types == 'v') {
                    $total_wtt += $row->watts;
                }
                if($row->types == 'a') {
                    $total_amp += $row->watts;
                }

                if($row->equipid == 0) {
                    $codes = 'LOAD';
                    $desc = 'With Electrical Load Specification.<br>';
                    $desc .= '<span class="text-info">'.$row->remarks.'</span>';
                }else{
                    $codes = $row->codes;
                    $desc = $row->descs.'<br>';
                    $desc .= '<span class="text-info">'.$row->remarks.'</span>';

                }
                $data['data'][] = array(
                    'num' => $num++,
                    'codes' => $codes,
                    'descs' => $desc,
                    'watts' => number_format($row->watts) . $row->types,
                    'qty' => number_format($row->qty),
                    'total' => number_format($row->qty * $row->watts),
                    'control' => $del_btn,
                );
            }
        }
        $data['totalqty'] = number_format($total_qty);
        $data['totalwtt'] = number_format($total_wtt * $total_amp);
        $data['totalqtyinput'] = $total_qty;
        $data['totalwttinput'] = $total_wtt;

        return json_encode($data);
    }

    function view_account_equipments($dataid) {
        $sql = "SELECT codes, watts, qty FROM prime_customer_accounts_equipments WHERE accountid = ?";
        $query = $this->db->query($sql, array($dataid));
        return ( $query ) ? $query->result() : false;
    }

    function get_buttons($id, $page) {
        $html = '<span class="btn-group">';
        $html .= '<input type="hidden" value="' . $id . '" name="rowid" id=rowid>';
        $html .= '<a class="btn btn-danger btn-xs stat" ><i class="fa fa-times"></i></a>';
        $html .= '</span>';
        return $html;
    }

    function updateCity($array) {
        $sql = 'UPDATE prime_customer_accounts_owners_address SET city = ? WHERE sysid = ? and status = ?';
        $query = $this->db->query($sql, array($array['newValue'], $array['identifier'], 1));
        return ( $query->num_rows > 0 ) ? true : false;
    }

    function updateDistrict($array) {
        $sql = 'UPDATE prime_customer_accounts_owners_address SET city = ? WHERE sysid = ? and status = ?';
        $query = $this->db->query($sql, array($array['newValue'], $array['identifier'], 1));
        return ( $query->num_rows > 0 ) ? true : false;
    }

    function updateInspectionDate($array) {
        $sql = 'UPDATE prime_customer_accounts_logs SET logdate = ? WHERE accountid = ?';
        $query = $this->db->query($sql, array($array['newValue'], $array['identifier']));
        return ( $query->num_rows > 0 ) ? true : false;
    }

    function updateSpecificAddres($array) {
        $sql = 'UPDATE prime_customer_accounts_owners_address SET city = ? WHERE sysid = ? and status = ?';
        $query = $this->db->query($sql, array($array['newValue'], $array['identifier'], 1));
        return ( $query->num_rows > 0 ) ? true : false;
    }

    function update_equipment_data() {
        //Review this code. Updating data can be easily made by using AJAX.
        $data = array();
        $rate_class_id = $data['rateClass'];
        $inspection_date = $data['inspDate'];
        $account_type = $data['accountType'];
        $district = $data['district'];
        $city = $data['city'];
        $specific_address = $data['specificAddress'];
        $trn = $data['trn'];
        $x = $data['latitude'];
        $y = $data['longitude'];
        $accountid = $data['accountID'];
        $address_id = $data['addressID'];

        $total_load = 0;

        //query strings start
        $sql_eq_values = "SELECT watts, qty FROM prime_customer_accounts_equipments WHERE accountid = ? AND status = ?";
        $sql_ao = "SELECT sysid FROM prime_customer_accounts_owners WHERE accountid = ?";
        $sql_oa = "UPDATE prime_customer_accounts_owners_address SET ownerid = ?, district = ?, city = ?, country = ?, addrspecific = ?, addrmapx = ?, addrmapy = ? WHERE sysid = ? AND status = ?";
        $sql_am = "UPDATE prime_customer_accounts_main SET types=? WHERE sysid = ?";
        $sql_om_up = "UPDATE prime_customer_accounts_owners_meter SET accountid = ?, ownerid = ?, rateid = ?, createdby = ?, status = ?";
        $sql_om = "SELECT sysid FROM prime_customer_accounts_owners_meter WHERE accountid = ?";
        $sql_al = "UPDATE prime_customer_accounts_logs SET logtype = ?, logamount = ?, meterid = ?, logdate = ?, createdby = ?, status = ? WHERE accountid = ?";
        $sql_rm = "UPDATE transaction_request_main SET status = ?, stagesid = ? WHERE trncode = ?";
        //query strings end
        $ownerid = $this->db->query($sql_ao, array($accountid))->row()->sysid;
        $eq_values = $this->db->query($sql_eq_values, array($accountid, 1))->result();
        foreach ($eq_values as $val) {
            $total_load += $val->watts * $val->qty;
        }
        $row = $this->retrieve_data($rate_class_id);
        $deposit_cost = $this->deposit_cost($total_load, $row->dailyops, $row->monthlyops, $row->demand, $row->rates);
        $this->db->trans_start();
        //using transactions means that all the queries must be successful before committing the data to database.
        //query bindings automatically escapes mysql data, no need for manually escaping data before querying
        //no need to specify that the data as string (e.g. '$specific_address') during the query.
        $this->db->query($sql_oa, array($ownerid, $district, $city, 175, $specific_address, $x, $y, $address_id, 1));
        $this->db->query($sql_am, array($account_type, $accountid));
        $this->db->query($sql_om_up, array($accountid, $ownerid, $rate_class_id, 1, 1));
        $this->db->query($sql_al, array(12, round($deposit_cost, 2), $this->db->query($sql_om, array($accountid))->row()->sysid, $inspection_date, 1, 1, $accountid));
        $this->db->query($sql_rm, array(1, 4, $trn));
        return $this->db->trans_complete();
    }

    function get_rate_class_name($sysid) {
        $sql = "SELECT classifications name FROM prime_system_rate_class_main WHERE sysid = ?";
        $query = $this->db->query($sql, array($sysid));
        return ( $query ) ? $query->row() : false;
    }

    function retrieve_rate_data($id) {
        /*
        //retrieves the data accompanied by rates to be used in view computation.
        $dep_cost_const_sql = "SELECT dailyops, monthlyops, demand, rates FROM prime_system_rate_class_main_quantity mq "
                . "LEFT JOIN prime_system_rate_class_main mr ON mq.rateid = mr.sysid "
                . "WHERE mq.rateid = ? AND mr.month = (SELECT MAX(month) FROM prime_system_rate_class_main_rates WHERE rateid = ? AND year = (SELECT MAX(year) FROM prime_system_rate_class_main_rates WHERE rateid = ?))";
        $query = $this->db->query($dep_cost_const_sql, array($data, $data, $data));

        $year = date('Y');
        $month = date('m');
        */


        $qry = $this->db->select("mq.dailyops, mq.monthlyops, mq.demand, gr.rates")
            ->from('rate_class_application_quantity AS mq')
            ->join('trn_application_monthly_gdr_rates AS gr', 'gr.rateid = mq.rateid AND gr.status = 1')
            ->where('mq.rateid', $id)
            ->get()->row();

        return ( $qry ) ? $qry : false;
    }

    function init_account_gdr() {
        $appid = $this->input->post('appid');
        $data['input'] = $this->input->post();
        $data = array();
        $qry = true;

        $system_size = get_system_size_range($appid);
        $system_size_desc = ($system_size) ? $system_size->descs : 'None';

        $data['qry'] = $qry;
        $data['total_load_text'] = number_format(0, 2);
        $data['deposit_cost'] = 0;
        $data['deposit_cost_text'] = $system_size_desc;
        echo json_encode($data);
    }

    function total_load($power, $qty) {
        return $power * $qty;
    }

    function deposit_cost($total_load, $daily_ops, $monthlyops, $demand, $rates) {
        return ( $total_load * $daily_ops * $monthlyops * $demand * $rates ) / 1000;
    }

    function add_equipment() {
        $this->db->trans_begin();
        $ins_arr = array(
            'appid' => $this->input->post('appid'),
            'equipid' => $this->input->post('id'),
            'watts' => $this->input->post('watts'),
            'remarks' => $this->input->post('remarks'),
            'qty' => $this->input->post('qty'),
            'createdby' => user_id()
        );
        $ins = $this->db->insert('application_customers_equipments', $ins_arr);
        $data['errmsg'] = $this->db->_error_message();
        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_commit();
            $qry = false;
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function change_equipment_status($sysid) {
        $sql_ae = "UPDATE customer_accounts_equipments SET status = ? WHERE sysid = ?";
        $this->db->query($sql_ae, array(0, $sysid));
        return ( $this->db->affected_rows() != 1 ) ? false : true;
    }

    function del_equipment() {
        $data = array();
        $sysid = $this->input->post('id');

        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $sysid , "status" => 1));
        $this->db->update("application_customers_equipments" , $updatearr);
        $qry = ( $this->db->affected_rows() != 1 ) ? false : true;
        $data['qry'] = $qry;
        return json_encode($data);
    }


    function save_gdr_payments() {
        $data = array();
        $qry = false;

        $accountid = $this->input->post('accountid');
        $dailyops = $this->input->post('dailyops');
        $monthlyops = $this->input->post('monthlyops');
        $rateclass = $this->input->post('rateclass');
        $depositcost = $this->input->post('depositcost');
        $rate = $this->input->post('rate');
        $rateclass = $this->input->post('rateclass');
        $totalload = $this->input->post('totalload');
        $demand = $this->input->post('demand');
        $chargeid = 162;

        $this->db->trans_begin();

        //SEARCH EXISTING
        $records = $this->db->select('COUNT(sysid) as cnt')->from('application_customers_gdr_logs')
            ->where(array('appid' => $accountid))->get()->row();

        // UPDATE EXISTING FIRST
        $this->db->where('appid', $accountid);
        $this->db->where('status', 1);
        $this->db->update('application_customers_gdr_logs', array('status' => 0));

        //INSERT CHARGES
        $ins_charges = insert_application_charges($chargeid, $depositcost, $accountid, 35, 2);

        // INSERT NEW
        $ins_arr = array(
            'appid' => $accountid,
            'rateclassid' => $rateclass,
            'totalwatt' => $totalload,
            'totalcost' => $depositcost,
            'dailyop' => $dailyops,
            'demand' => $demand,
            'monthlyop' => $monthlyops,
            'chargesid' => $ins_charges->chargesid,
            'createdby' => user_id(),
        );
        $this->db->insert('application_customers_gdr_logs', $ins_arr);
        $log_id = $this->db->insert_id();

        $this->db->where(array('appid' => $accountid, 'gdrid' => 0, 'status' => 1));
        $this->db->update('application_customers_equipments',array('gdrid' => $log_id));
        $grouping = $this->db->last_query();

        $data['errmsg'] = $this->db->_error_message();


        if($this->db->trans_status()===true && $ins_charges->qry) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }

        if ($records->cnt > 0) {
            $old_value = 'Rate Class: 0 / Total Load: 0 / Total Charges: 0';
            $new_value = 'Rate Class: '.get_rateclass_name($rateclass)
                . '/Total Load: '.$totalload
                . '/Total Charges: '.$depositcost;

            $audit_ins_arr = array(
                'dataid' => $accountid,
                'moduleid' => 36,
                'valueold' => $old_value,
                'valuenew' => $new_value,
                'createdby' => user_id(),
                'remarks' => 'CAD - Add GDR Computation.'
            );
            $audit_ins = audit_insert($audit_ins_arr);
        }

        $data['input'] = $this->input->post();
        $data['qry'] = $qry;
        $data['logid'] = $log_id;
        $data['grouping_qry'] = $grouping;
        return json_encode($data);
    }

    function change_gdr_payments() {
        $data = array();

        $dataid = $this->input->post('dataid');

        $data['dataid'] = $dataid;

        $gdrdata = $this->db->select('appid , chargesid , rateclassid , totalwatt , totalcost')->from('application_customers_gdr_logs')
            ->where('appid',$dataid)->get()->row();

        $old_value = 'Rate Class: '.get_rateclass_name($gdrdata->rateclassid)
            . '/Total Load: '.$gdrdata->totalwatt
            . '/Total Charges: '.$gdrdata->totalcost;

        $new_value = 'Rate Class: 0 / Total Load: 0 / Total Charges: 0';

        $this->db->where(array('appid' => $dataid , 'gdrid != ' => 0));
        $equipments = $this->db->update('application_customers_equipments',array('status' => 0,'updatedby' => user_id()));

        $this->db->where(array('appid' => $dataid , 'status' => 1));
        $gdr = $this->db->update('application_customers_gdr_logs',array('status' => 0,'updatedby' => user_id()));

        $this->db->where(array('appid' => $dataid , 'status' => 1 , 'chargeid' => 162 , 'moduleid' => 35));
        $charges = $this->db->update('application_customers_charges',array('status' => 0,'updatedby' => user_id()));

        if ($equipments && $gdr && $charges) {
            $audit_ins_arr = array(
                'dataid' => $dataid,
                'moduleid' => 36,
                'valueold' => $old_value,
                'valuenew' => $new_value,
                'createdby' => user_id(),
                'remarks' => 'CAD - Remove Current GDR Computation.'
            );
            $audit_ins = audit_insert($audit_ins_arr);
        }

        return json_encode($data);
    }

    function dt_gdr_logs() {
        $data = array();
        $dataid = $this->input->post('dataid');

        $gdr_logs_qry = $this->db->select()
            ->from('application_customers_gdr_logs')
            ->where('appid',$dataid)
            ->get();
        //$data['gdr_logs_qry'] = $this->db->last_query();

        if ($gdr_logs_qry->num_rows() > 0) {
            $num = 1;
            foreach ($gdr_logs_qry->result() AS $row){
                /*$data['list'][] = array(
                    'expand' => $row->sysid,
                    'num' => $num++,
                    'rateclass' => get_rateclass_name($row->rateclassid),
                );*/
                $rate = $this->db->select('rates')
                    ->from('trn_application_monthly_gdr_rates')
                    ->where('rateid',$row->rateclassid)->get()->row();
                $checked = ($row->status == 1) ? 'checked' : '';
                $nums_arr = array(
                    'expand' => btn_expand($row->sysid),
                    'num' => $num++,
                    'rateclass' => get_rateclass_name($row->rateclassid),
                    'rate' => $rate->rates,
                    'control' => '<input type="radio" '.$checked.' data-id="'.$row->sysid.'" name="active_gdr" id="active_gdr">'
                );
                $rows = (array)$row;
                $data['list'][] = array_merge($nums_arr, $rows);
            }
        }
        //echo '<pre>';
        //return print_r($data);
        return json_encode($data);
    }

    function dt_inspection_logs() {
        $data = array();
        $dataid = $this->input->post('dataid');
        $active = '';
        /*
        $role_main_id = array();
        $role_main = get_users_info_roles(user_id());
        if($role_main && count($role_main) > 0) {
            foreach($role_main as $rrow) {
                if($rrow->type == 1) {
                    $role_main_id[] = $rrow->roleid;
                }
            }
        }*/

        $insp_logs_qry = $this->db->select()
            ->from('application_customers_system_size')
            ->where(array('appid' => $dataid , 'status !=' => 0))
            ->get();

        if ($insp_logs_qry->num_rows() > 0) {
            $num = 1;
            $result = $insp_logs_qry->result();
            $data['result']=$result;
            $published = result_array_look_up($result,'status',305);
            $data['published']=$published;
            foreach ($result AS $row){
                $checked = ($row->status == 1) ? 'checked' : '';
                $stat = ($row->status == 305) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-lock text-primary"></i>';
                $active = ($published->result) ? $stat : '<input type="radio" '.$checked.' data-id="'.$row->sysid.'" name="active_insp" id="active_insp">';
                //$active = ($row->status != 305) ? '<input type="radio" '.$checked.' data-id="'.$row->sysid.'" name="active_insp" id="active_insp">' : '<i class="fa fa-check text-success"></i>';
                $control = ($published->result) ? $stat : '<button type="button" data-id="'.$row->sysid.'" name="btn_del_insp" id="btn_del_insp" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> </button>';
                $nums_arr = array(
                    'expand' => btn_expand($row->sysid),
                    'num' => $num++,
                    'created' => get_users_info($row->createdby)->lastname,
                    'control' => $control,
                    'active' => $active,
                    'power' => number_format($row->power, 0),
                    'nop' => number_format($row->nop,0),
                    'comment' => ($row->remarks) ? $row->remarks : 'N/A',
                    'stat' => $stat
                );
                $rows = (array)$row;
                $data['list'][] = array_merge($rows,$nums_arr);
            }

        }
        //echo '<pre>';
        //return print_r($data);
        return json_encode($data);
    }

    function get_gdr_subdetails() {
        $data = array();
        $id = $this->input->post('id');
        $html = '';
        $total_qty = 0;
        $total_watt = 0;

        $gdr_loads_query = $this->db->select('e.sysid, ep.codes, e.watts, e.qty, e.equipid, ep.descs, e.remarks, e.status, ep.types')
            ->from("application_customers_equipments e")
            ->join('prime_equipments_parameters AS ep', 'ep.sysid = e.equipid', 'left')
            ->where(array('e.logid' => $id))
            ->get();

        if ($gdr_loads_query->num_rows() > 0) {
            $html .= '<table class="table table-hover table-condensed">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th></th>';
            $html .= '<th>Codes</th>';
            $html .= '<th>Descriptions</th>';
            $html .= '<th>Watts</th>';
            $html .= '<th align="center"><i class="fa fa-wrench"></i>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $num = 1;

            $total_voltage = 0;
            $total_amp = 0;
            $total_watt = 0;
            foreach ($gdr_loads_query->result() AS $row) {
                if($row->equipid == 0) {
                    $codes = 'LOAD';
                    $desc = 'With Electrical Load Specification.<br>';
                    $desc .= '<span class="text-info">'.$row->remarks.'</span>';
                }else{
                    $codes = $row->codes;
                    $desc = $row->descs.'<br>';
                    $desc .= '<span class="text-info">'.$row->remarks.'</span>';
                }
                $stat = ($row->status == 1) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
                $html .= '<tr>';
                $html .= '<td>'.$num++.'</td>';
                $html .= '<td>'.$codes.'</td>';
                $html .= '<td>'.$desc.'</td>';
                $html .= '<td class="number">'.number_format($row->watts).'</td>';
                $html .= '<td align="center">'.$stat.'</td>';
                $html .= '<tr>';
                $total_qty += $row->qty;
                $total_watt += $row->qty * $row->watts;
                if($row->types == 'v') {
                    $total_watt += $row->watts;
                }
                if($row->types == 'a') {
                    $total_amp += $row->watts;
                }
            }

            $total_voltage = ($total_watt * $total_amp);
            $html .= '</tbody>';
            $html .= '<thead>';
            $html .= '<th></th>';
            $html .= '<th class="bold">Total</th>';
            $html .= '<th></th>';
            $html .= '<th style="text-align: right; font-size: 16px;">'.number_format($total_voltage, 2).'</th>';
            $html .= '<th  style="text-align: left; padding: 2px 0px; padding-left: 5px">Watts</th>';
            $html .= '</thead>';
            $html .= '</table>';
        } else {
            $html .= '<span><i class="fa fa-exclamation-triangle text-warning"></i> No data Found</span>';
        }

        $data['html'] = $html;
        return json_encode($data);
    }

    function switch_gdr_computation() {
        $data = array();
        $gdrid = $this->input->post('gdrid');

        $gdrdata = $this->db->select('appid , chargesid , rateclassid , totalwatt , totalcost')->from('application_customers_gdr_logs')
            ->where('sysid',$gdrid)->get()->row();

        $current_gdr = $this->db->select('rateclassid , totalwatt , totalcost')
            ->from('application_customers_gdr_logs')->where(array('appid' => $gdrdata->appid , 'status' => 1))->get()->row();

        $old_value = 'Rate Class: '.get_rateclass_name($current_gdr->rateclassid)
            . '/Total Load: '.$current_gdr->totalwatt
            . '/Total Charges: '.$current_gdr->totalcost;

        $new_value = 'Rate Class: '.get_rateclass_name($gdrdata->rateclassid)
            . '/Total Load: '.$gdrdata->totalwatt
            . '/Total Charges: '.$gdrdata->totalcost;

        //UPDATE ALL ACTIVE GDR DATA TO STATUS "0"
        //CHARGES -> STATUS "0"
        $this->db->where(array('appid' => $gdrdata->appid, 'status' => 1));
        $charges = $this->db->update('application_customers_charges',array('status' => 0, 'updatedby' => user_id()));

        //EQUIPMENT -> STATUS "0"
        $this->db->where(array('appid' => $gdrdata->appid, 'status' => 1));
        $equipment = $this->db->update('application_customers_equipments',array('status' => 0, 'updatedby' => user_id()));

        //LOGS -> STATUS "0"
        $this->db->where(array('appid' => $gdrdata->appid, 'status' => 1));
        $logs = $this->db->update('application_customers_gdr_logs',array('status' => 0, 'updatedby' => user_id()));

        //UPDATE SELECTED GDR TO STATUS "1"
        //LOGS -> STATUS 1
        $this->db->where(array('sysid' => $gdrid, 'status' => 0));
        $ulogs = $this->db->update('application_customers_gdr_logs',array('status' => 1, 'updatedby' => user_id()));

        //EQUIPMENT -> STATUS "1"
        $this->db->where(array('gdrid' => $gdrid, 'status' => 0));
        $uequipment = $this->db->update('application_customers_equipments',array('status' => 1, 'updatedby' => user_id()));

        //CHARGES -> STATUS "1"
        $this->db->where(array('sysid' => $gdrdata->chargesid, 'status' => 0));
        $ucharges = $this->db->update('application_customers_charges',array('status' => 1, 'updatedby' => user_id()));

        if ($charges && $equipment && $logs && $ulogs && $uequipment && $ucharges) {
            $audit_ins_arr = array(
                'dataid' => $gdrdata->appid,
                'moduleid' => 36,
                'valueold' => $old_value,
                'valuenew' => $new_value,
                'createdby' => user_id(),
                'remarks' => 'CAD - GDR Computation Change'
            );
            $audit_ins = audit_insert($audit_ins_arr);
        }

        return json_encode($data);
    }


    function count_dist($arraydist) {
        asort($arraydist);
        $count = 0;
        $current = null;
        $highest = 0;
        $let = null;
        for ($i = 0; $i < count($arraydist); $i ++) {
            if ($arraydist [$i] == $current) {
                $count += 1;
            } else {
                $highest = $count;
                $count = 0;
            }
            if ($count > $highest) {
                $highest = $count;
                $let = $arraydist [$i];
            }
            $current = $arraydist [$i];
        }
        return $let;
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

    function save_near_meter_bak() {
        $data = array();
        $func = 'error';
        $appid = $this->input->post('dataid');
        $mtr_inputs = $this->input->post('meters');
        $mtr_inputs_arr = explode(',', $mtr_inputs);
        // $mtr_inputs = array('023990021', '023990018', '023990020');
        // $mtr_inputs = array(12, 7, 10);
        asort($mtr_inputs_arr);
        $this->db->trans_begin();
        if ($mtr_inputs && $appid) {

            $qry_app_details = $this->db->select()->from('application_customers_details')->where('sysid', $appid)->get()->row();

            $mtr_inputs = explode(',', $mtr_inputs);

            $nmeter_upd_arr = array(
                'status' => 0
            );
            $this->db->where('appid', $appid);
            $this->db->update('application_customers_near_meters', $nmeter_upd_arr);
            foreach ($mtr_inputs as $row) {
                $nmeter_arr = array(
                    'appid' => $appid,
                    'acctid' => $row,
                    'createdby' => user_id()
                );
                $this->db->insert('application_customers_near_meters', $nmeter_arr);
                $data['errorinsertnm'][] = $this->db->_error_message();
            }
            $mtr_dist = array();

            $qry_search_near = $this->db->select('a.sysid, a.mtrno, g.d, g.l, g.b')
                ->from('customer_accounts_main AS a')
                ->join('gdlb_main AS g', 'g.sysid = a.gdlb')
                ->where_in("a.sysid", $mtr_inputs)->get();

            foreach ($qry_search_near->result() as $row) {
                array_push($mtr_dist, $row->d);
            }

            $dist = $this->count_dist($mtr_dist);

            $dist_chosen = array();
            $gdlb_chosen = array();
            $gdlb_cnt_books = array();

            // GET GDLB WITH AVAILABLE LIMIT
            $qry_gdlb_available = $this->db->select()->from('gdlb_main')->where('d', $dist)->get();

            if ($qry_gdlb_available->num_rows() > 0) {
                foreach ($qry_gdlb_available->result() as $garow) {
                    $check_this_gdlb = $this->db->select('count(g.sysid) AS CNT')
                        ->from('gdlb_main AS g')
                        ->join('customer_accounts_main AS a', 'a.gdlb = g.sysid')
                        ->where('g.sysid', $garow->sysid)->get()->row();
                    if ($check_this_gdlb->CNT < $garow->limit) {
                        array_push($dist_chosen, $garow->d);
                        array_push($gdlb_chosen, $garow->sysid);
                        array_push($gdlb_cnt_books, array(
                            $garow->sysid,
                            $check_this_gdlb->CNT
                        ));
                    }
                }
            }
            $dist_final = $this->count_dist($dist_chosen);
            $gdlbid_final = $this->getClosest(0, $gdlb_cnt_books);
            $qry_gdlb_final = $this->db->select()->from('gdlb_main')->where('sysid', $gdlbid_final)->get()->row();
            if ($qry_gdlb_final) {
                $gdlb_final_g = $qry_gdlb_final->g;
                $gdlb_final_d = $qry_gdlb_final->d;
                $gdlb_final_dcode = get_district_name($qry_gdlb_final->d) ? get_district_name($qry_gdlb_final->d)[0] : '';
                $gdlb_final_l = $qry_gdlb_final->l;
                $gdlb_final_b = $qry_gdlb_final->b;

                // GET SEQUENCE
                $qry_seq = $this->db->select_max('sysid')->from('application_customers_sequence')->get()->row();
                $new_seq = ($qry_seq) ? $qry_seq->sysid + 1 : 1;

                if($qry_gdlb_final->d==5){
                    $gdlb_final_dcode = 'S';
                }else{
                    $gdlb_final_dcode = get_district_name($qry_gdlb_final->d) ? get_district_name($qry_gdlb_final->d)[0] : '';
                }

                // SERVNO
                $servno = $gdlb_final_dcode.str_pad($new_seq, 6, "0", STR_PAD_LEFT);

                // INSERT ASSIGN GDLB (customer_accounts_glb)
                $this->db->where('sysid', $appid);
                $this->db->update('application_customers_details', array('gdlbid' => $qry_gdlb_final->sysid, 'servno' => $servno));
                $errmsg = $this->db->_error_message();


                // INSERT SEQUENCE
                $this->db->insert("application_customers_sequence", array('appid' => $appid, 'createdby' => user_id()));

                $data['gdlbfial'] = $qry_gdlb_final->sysid;
                $data['appid'] = $appid;


                $audit_ins_arr = array(
                    'dataid' => $appid,
                    'moduleid' => 35,
                    'valueold' => $qry_app_details->servno,
                    'valuenew' => $servno,
                    'createdby' => user_id(),
                    'remarks' => 'CAD - GDLB Create'
                );
                $audit_ins = audit_insert($audit_ins_arr);

                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    // $msg = '<li class="list-group-item"><b>GDLB: </b>' . '<span class="pull-right">' . $gdlb_final_g . '/' . $gdlb_final_d . '/' . $gdlb_final_l . '/' . $gdlb_final_b . "</span></li>";
                    $gdlb = $gdlb_final_g . '-' . $gdlb_final_dcode . '-' . $gdlb_final_l . '-' . $gdlb_final_b;
                    $qry = true;
                    $msg = 'Near Meter Saved / Service Number Created : ' . $gdlb . ' / ' . $servno;
                    $func = 'success';
                } else {
                    $this->db->trans_rollback();
                    $msg = 'Error Insert Query : ' . $errmsg;
                    $gdlb = '';
                    $qry = false;
                }
            } else {
                $msg = "GLDB depleted!";
                $gdlb = '';
                $qry = false;
                $func = 'waring';
            }
        } else {
            $msg = 'Input is empty!';
            $gdlb = '';
            $qry = false;
        }
        $data ['input'] = $this->input->post();
        $data ['gdlb'] = $gdlb;
        $data ['qry'] = $qry;
        $data ['msg'] = $msg;
        $data ['func'] = $func;
        return json_encode($data);
    }

    function save_near_meter() {
        $data = array();
        $func = 'error';
        $appid = $this->input->post('dataid');
        $mtr_inputs = $this->input->post('meters');
        $mtr_inputs_arr = explode(',', $mtr_inputs);
        // $mtr_inputs = array('023990021', '023990018', '023990020');
        // $mtr_inputs = array(12, 7, 10);

        $inserts = array();
        $insertIDs = array();
        $errorinsertnm = array();
        asort($mtr_inputs_arr);
        $this->db->trans_begin();
        if ($mtr_inputs && $appid) {

            $qry_app_details = $this->db->select()->from('application_customers_details')->where('sysid', $appid)->get()->row();

            $mtr_inputs = explode(',', $mtr_inputs);

            $nmeter_upd_arr = array(
                'status' => 0
            );

            $this->db->update('application_customers_near_meters', $nmeter_upd_arr,array('appid'=>$appid));
            foreach ($mtr_inputs as $row) {
                $nmeter_arr = array(
                    'appid' => $appid,
                    'acctid' => $row,
                    'createdby' => user_id()
                );
                $this->db->insert('application_customers_near_meters', $nmeter_arr);
                $inserts[] = $this->db->last_query();
                $insertIDs[] = $this->db->insert_id();
                $errorinsertnm[] = $this->db->_error_message();
            }
            $msg = 'Near Meters Successfully Saved!';
            $data = db_trans($this->db,'Failed to save Near Meters', $msg);
            $data['inserts'] = $inserts;
            $data['insertIDs'] = $insertIDs;
            $data['errorinsertnm'] = $errorinsertnm;
        } else {
            $msg = 'Input is empty!';
            $qry = false;
            $data ['qry'] = $qry;
            $data ['msg'] = $msg;
            $data ['func'] = $func;
        }

        $data ['input'] = $this->input->post();

        return json_encode($data);
    }

    function save_inspection() {
        $data = array();
        $msg = '';
        $func = '';
        $title = '';

        $appid = $this->input->post('appid');
        $roofinclination = $this->input->post('roofinclination');
        $inspectiondate = $this->input->post('inspectiondate');
        $equipmentcount = $this->input->post('equipmentcount');
        $totalload = $this->input->post('totalload');
        $remarks = $this->input->post('remarks');

        $existing = $this->db->select()->from('application_customer_inspection_logs')
            ->where(array('appid' => $appid, 'status' => 1))->get()->row();

        if ($existing) {
            $this->db->update('application_customer_inspection_logs',array('status' => 2),array('status' => 1 , 'appid' => $appid));
        }

        $insert_arr = array(
            'appid' => $appid,
            'roofinclination' => $roofinclination,
            'inspectiondate' => $inspectiondate,
            'equipmentcnt' => $equipmentcount,
            'totalload' => $totalload,
            'remarks' => $remarks,
            'createdby' => user_id(),
        );

        $this->db->insert('application_customer_inspection_logs',$insert_arr);
        $logid = $this->db->insert_id();

        if ($logid) {
            $this->db->update('application_customers_equipments',array('logid' => $logid),array('appid' => $appid, 'status' => 1));
            $error = $this->db->_error_message();

            if (!$error) {
                $msg = 'Inspection details successfully saved.';
                $func = 'success';
                $title = 'Success!';
            } else {
                $msg = 'Inspection details not saved.';
                $func = 'error';
                $title = 'Failed!';
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function dt_active_loads() {
        $dataid = $this->input->post('dataid');
        $page = $this->input->post('page');
        $display_state = '';
        $qry = false;
        $data = array();
        if ($page == 'view') { //viewing only, editing is disabled
            $display_state = 'disabled';
        }
        $q = $this->db->select('e.sysid, ep.codes, e.watts, e.qty, e.equipid, ep.codes, ep.descs, e.remarks, ep.types')
            ->from("application_customers_equipments e")
            ->join('prime_equipments_parameters AS ep', 'ep.sysid = e.equipid', 'left')
            ->join('application_customer_inspection_logs AS log', 'log.sysid = e.logid')
            ->where(array('e.appid' => $dataid, 'e.status' => 1, 'log.status' => 1))
            ->get();
        $q_qry = $this->db->last_query();
        $num_rows = $q->num_rows();
        $total_qty = 0;
        $total_wtt = 0;
        $total_amp = 0;
        if ($q->num_rows() > 0) {
            $qry = true;
            $num = 1;
            foreach ($q->result() as $row) {
                $total_qty += $row->qty;
                if($row->types == 'a') {
                    $total_wtt += $row->watts;
                }
                if($row->types == 'v') {
                    $total_amp += $row->watts;
                }

                if($row->equipid == 0) {
                    $codes = 'LOAD';
                    $desc = 'With Electrical Load Specification.<br>';
                    $desc .= '<span class="text-info">'.$row->remarks.'</span>';
                }else{
                    $codes = $row->codes;
                    $desc = $row->descs.'<br>';
                    $desc .= '<span class="text-info">'.$row->remarks.'</span>';

                }
                $data['data'][] = array(
                    'num' => $num++,
                    'codes' => $codes,
                    'descs' => $desc,
                    'watts' => number_format($row->watts),
                    'qty' => number_format($row->qty),
                    'total' => number_format($row->qty * $row->watts).' Watts',
                );
            }
        }
        $total_volts = ($total_wtt * $total_amp);
        $data['totalqty'] = number_format($total_qty);
        $data['totalqtyinput'] = $total_qty;
        $data['totalwtt'] = number_format($total_volts).' Watts';
        $data['totalwttinput'] = $total_volts;
        $data['qry'] = $qry;
        $data['query'] = $q_qry;
        $data['reinspect'] = '<button class="btn btn-success btn-sm" id="btn_reinspection"><i class="fa fa-edit"></i> Reinspect</button>';

        return json_encode($data);
    }

    function change_inspection_details() {
        $data = array();
        $appid = $this->input->post('dataid');
        $qry = false;

        $where = array('appid' => $appid , 'status' => 1);

        $logid = $this->db->select('sysid')
            ->from('application_customer_inspection_logs')
            ->where($where)->get()->row();

        $update_logs = $this->db->update('application_customer_inspection_logs',array('status' => 2),$where);
        $error_logs = $this->db->_error_message();

        if (!$error_logs || $error_logs=='') {
            $update_equip = $this->db->update('application_customers_equipments',array('status' => 0) , array('logid' => $logid->sysid));
            $error_equip = $this->db->_error_message();
            if (!$error_equip || $error_equip=='') {
                $qry = true;
                $audit_ins_arr = array(
                    'dataid' => $appid,
                    'moduleid' => 36,
                    'valueold' => $logid->sysid,
                    'valuenew' => 'None',
                    'createdby' => user_id(),
                    'remarks' => 'Deactivated Inspection Log for Re-inspection.'
                );
                $audit_ins = audit_insert($audit_ins_arr);
                $msg = 'You can now re-enter new Inspection Details.';
                $func = 'success';
                $title = 'Success!';
            } else {
                $msg = 'Failed to reset inspection details.';
                $func = 'error';
                $title = 'Failed!';
            }
        } else {
            $msg = 'Failed to reset inspection details.';
            $func = 'error';
            $title = 'Failed!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
    }

    function change_active_inspection() {
        $data = array();
        $appid = $this->input->post('dataid');
        $logid = $this->input->post('logid');
        $msg = '';
        $func = '';
        $title = '';
        $qry = false;

        $check_active = $this->db->select('sysid,remarks')
            ->from('application_customer_inspection_logs')
            ->where(array('appid' => $appid, 'status' => 1, 'sysid' => $logid))
            ->get()->row();

        $find_active = $this->db->select('sysid')
            ->from('application_customer_inspection_logs')
            ->where(array('appid' => $appid, 'status' => 1, 'sysid !=' => $logid))
            ->get()->row();

        if ($check_active) {
            $qry = false;
            $msg = 'Selected inspection data is already the current inspection data.';
            $func = 'warning';
            $title = 'Already Selected!';
        } else {
            if ($find_active) {
                $this->db->update('application_customer_inspection_logs', array('status' => 2), array('appid' => $appid, 'status' => 1, 'sysid !=' => $logid));
                $error = $this->db->_error_message();

                if (!$error || $error == '') {
                    $this->db->update('application_customers_equipments',array('status' => 0), array('appid' => $appid, 'status' => 1, 'logid !=' => $logid));
                } else {
                    $msg = $error;
                    $func = 'error';
                    $title = 'Error!!!';
                }
            }

            $this->db->update('application_customer_inspection_logs',array('status' => 1), array('appid' => $appid, 'sysid' => $logid));
            $error_update = $this->db->_error_message();
            if (!$error_update || $error_update == ''){
                $this->db->update('application_customers_equipments',array('status' => 1), array('appid' => $appid, 'logid' => $logid));
                $error_update_eq = $this->db->_error_message();
                if (!$error_update_eq || $error_update_eq == '') {
                    $msg = 'Inspection load successfully reinstated.';
                    $func = 'success';
                    $title = 'Changed';
                    $qry = true;
                }
            }
        }

        if ($qry == true) {
            $audit_ins_arr = array(
                'dataid' => $appid,
                'moduleid' => 36,
                'valueold' => ($find_active) ? $find_active->sysid : 'None',
                'valuenew' => $logid,
                'createdby' => user_id(),
                'remarks' => 'Inspection - Changed active Inspection Log.'
            );
            $audit_ins = audit_insert($audit_ins_arr);
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function delete_inspection() {
        $data = array();
        $appid = $this->input->post('appid');
        $logid = $this->input->post('logid');
        $msg = '';
        $func = '';
        $title = '';
        $qry = false;
        $active = 0;

        $delete = $this->db->update(
            'application_customers_system_size',
            array('status' => 0),
            array('appid' => $appid , 'sysid' => $logid)
        );
        $error = $this->db->_error_message();

        if (!$error || $error == '') {
            $delete_det = $this->db->update(
                'application_customers_survey_details',
                array('status' => 0),
                array('appid' => $appid , 'logid' => $logid)
            );
            $error_eq = $this->db->_error_message();

            if (!$error_eq || $error_eq == '') {
                $msg = 'Survey Log deleted';
                $func = 'success';
                $title = 'Done!';
                $qry = true;
            }

            $active_cnt = $this->db->select('COUNT(sysid) as cnt')
                ->from('application_customers_system_size')
                ->where(array('appid' => $appid,'status !=' => 0))
                ->get()->row();

            if ($active_cnt) {
                $active = $active_cnt->cnt;
            }
        } else {
            $msg = 'Failed to delete inspection log.';
            $func = 'error';
            $title = 'Failed!';
        }

        if ($qry) {
            $audit_ins_arr = array(
                'dataid' => $appid,
                'moduleid' => 92,
                'valueold' => '1 - Active',
                'valuenew' => '0 - Deleted',
                'createdby' => user_id(),
                'remarks' => 'Inspection - Deleted inspection log id '.$logid
            );
            $audit_ins = audit_insert($audit_ins_arr);
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $msg;
        $data['active'] = $active;

        return json_encode($data);
    }


    function add_team_member() {
        $dataid = $this->input->post('dataid');
        $empid = $this->input->post('empid');
        $moduleid = $this->input->post('moduleid');
        $this->db->trans_begin();
        $this->db->update('application_customers_team_assignment', array(
            'status' => 0,
            'updatedby' => user_id(),
        ), array(
            'appid' => $dataid,
            'empid' => $empid,
            'moduleid' => $moduleid,
            'status' => 1
        ));
        $this->db->insert('application_customers_team_assignment', array(
            'empid' => $empid,
            'appid' => $dataid,
            'moduleid' => $moduleid,
            'createdby' => user_id(),
            'updatedby' => user_id(),
        ));
        $data = db_trans($this->db);
        return json_encode($data);
    }
    function del_team_member() {
        $id = $this->input->post('id');
        $this->db->trans_begin();
        $this->db->update('application_customers_team_assignment', array(
            'status' => 0,
            'updatedby' => user_id(),
        ), array(
            'sysid' => $id
        ));
        $data = db_trans($this->db);
        return json_encode($data);
    }
    function get_team_member() {
        $data = array();
        $appid = $this->input->post('appid');
        $moduleid = $this->input->post('moduleid');
        $query = $this->db->query("
            SELECT
                `acts`.`sysid`,
                `emp`.`empid`,
                `ec`.`ccid`,
                CONCAT( p.firstname, ', ', ' ', `p`.`lastname` ) AS fullname,
                `acts`.`datecreated`,
                `cc`.`desc` AS deptname,
                `cc`.`names` AS deptcode 
            FROM prime_employee_main AS emp
                JOIN `application_customers_team_assignment` AS acts ON `acts`.`empid` = `emp`.`sysid`
                LEFT JOIN `prime_employee_main_payclass` AS empc ON `empc`.`emp_id` = `emp`.`sysid` 
                AND empc.STATUS = 1
                LEFT JOIN `person` AS p ON `p`.`sysid` = `emp`.`personid`
                LEFT JOIN `prime_employee_costcenter` AS ec ON `ec`.`empid` = `emp`.`sysid` 
                AND ec.STATUS = 1
                LEFT JOIN `prime_costcenter_main` AS cc ON `cc`.`sysid` = `ec`.`ccid` 
                AND ec.STATUS = 1 
            WHERE
                `acts`.moduleid = $moduleid 
                AND `acts`.appid = $appid
                AND acts.status = 1
            GROUP BY fullname 
            ORDER BY
                p.lastname ASC
        ");
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'empid' => $row->empid,
                    'name' => $row->fullname . '<span class="badge badge-info pull-right">'.$row->deptcode.'</span>',
                    'date' => $row->datecreated,
                    'control' => '<a id="btn_delete_team" data-id="'.$row->sysid.'" class="btn btn-danger inline btn-xs"><i class="fa fa-times"></i></a>'
                );
            }
        }
        return json_encode($data);
    }

    function save_inspection_report() {
        $data = array();

        $data['published'] = false;
        return json_encode($data);
    }

    function compute_inspection_report()
    {
        $data = array();
        $msg = '';
        $func = 'error';
        $inputs = $this->input->post();
        $appid = $this->input->post("appid");
        $l1l2 = $this->input->post("l1l2");
        $l1l3 = $this->input->post("l1l3");
        $l2l3 = $this->input->post("l2l3");
        $l1g = $this->input->post("l1g");
        $l2g = $this->input->post("l2g");
        $l3g = $this->input->post("l3g");
        $l1l2a = $this->input->post("l1l2a");
        $l1l3a = $this->input->post("l1l3a");
        $l2l3a = $this->input->post("l2l3a");
        $rateclass = $this->input->post("rateclass");
        $paneltype = $this->input->post("paneltype");
        $roofinclination = $this->input->post("roofinclination");
        $inspectiondate = $this->input->post("inspectiondate");
        $remarks = $this->input->post("remarks");
        $posttype = $this->input->post('posttype');
        $surveydetail = $this->input->post('surveydetail');
        $surveyid = $this->input->post('surveyid');
        $rooforientation = $this->input->post('rooforientation');
        $roofing = $this->input->post('roofing');
        $vdcondition = $this->input->post('vdcondition');
        $gensetrate = $this->input->post('gensetrate');
        $roofdimension = $this->input->post('roofdimension');
        $esplans = $this->input->post('esplans');
        $forclamping = $this->input->post('forclamping');
        $billingdetails = $this->input->post('billingdetails');
        $dtappliances = $this->input->post('dtappliances');


        $phase = '';
        $power = '';
        $nop = '';
        $system_size = '';
        $system_size_id = 0;
        $published = false;

        $qry_customer_system_size = $this->db->select()
            ->from('application_customers_system_size')
            ->where(array('status' => 305, 'appid' => $appid))
            ->order_by('sysid', 'desc')
            ->get()->row();
        //$existing_css_status = ($qry_customer_system_size) ? $qry_customer_system_size->status : false;
        if($qry_customer_system_size) {

            // GET BRACKET
            $sql_size_max = $this->db->query("
                        SELECT * FROM customer_system_size
                        WHERE sysid = {$qry_customer_system_size->sysize} AND status = 1
                        ")->row();
            if ($sql_size_max) {
                $system_size = $sql_size_max->descs;
                $system_size_id = $sql_size_max->sysid;
            }
            $published = true;
            $phase = get_rate_class_select($qry_customer_system_size->rateclass);
            $power = $qry_customer_system_size->power;
            $nop = number_format($qry_customer_system_size->nop,0);
            $func = 'success';
        } else {
            $power = 0;
            $voltage = array(
                'l1_l2_amt' => ($l1l2 && $l1l2 > 0) ? $l1l2 : 0,
                'l1_l3_amt' => ($l1l3 && $l1l3 > 0) ? $l1l3 : 0,
                'l2_l3_amt' => ($l2l3 && $l2l3 > 0) ? $l2l3 : 0,
                'l1_g_amt' => ($l1g && $l1g > 0) ? $l1g : 0,
                'l2_g_amt' => ($l2g && $l2g > 0) ? $l2g : 0,
                'l3_g_amt' => ($l3g && $l3g > 0) ? $l3g : 0,
            );

            // SOLVE POWER
            $total_voltage = array_sum($voltage);

            // CHECK IF THERE IS SAVED HERE

            // CHECK IF THERE IS SAVED PUBLISH
            if ($total_voltage > 0) {
                if ($l1l3a == 0 || $l2l3a == 0 || $rateclass == 1) {
                    // Phase 1
                    $phase = 'single phase';
                    if ($l1l2a > 0) {
                        $power = ($total_voltage * $l1l2a);
                    }
                } else {
                    // 3 Phase
                    $phase = '3 phase';
                    $amp_higher = max($l1l2a, $l1l3a, $l2l3a);
                    if ($amp_higher > 0) {
                        $power = ($total_voltage * $amp_higher) * 1.732;
                    }
                }
            } else {
                $phase = '3 phase';
            }

            // GET N.O.P.
            $get_panel_val = $this->db->select('value')->from('solar_panel_types')
                ->where('sysid', $paneltype)
                ->get()->row();
            if ($get_panel_val) {

                //$nop = round(($power / $get_panel_val->value), 0);
                $nop = ceil($power / $get_panel_val->value);

                // GET SYSTEM SIZE
                // GET equal
                $sql_size_equal = $this->db->select()
                    ->from('customer_system_size')
                    ->where(array('amtequal' => $nop, 'status' => 1))
                    ->get()->row();
                if ($sql_size_equal) {
                    $system_size = $sql_size_equal->descs;
                    $system_size_id = $sql_size_equal->sysid;
                } else {
                    // GET MAX
                    $sql_size_max = $this->db->select()
                        ->from('customer_system_size')
                        ->where(array('status' => 1))
                        ->order_by('amtmax', 'desc')
                        ->get()->row();
                    $nop_max = $sql_size_max->amtmax;
                    if ($nop > $nop_max) {
                        $system_size = (($nop * $power) / 1000) . 'kWp';
                        $system_size_id = 0;
                    } else {
                        // GET BRACKET
                        $sql_size_max = $this->db->query("
                        SELECT * FROM customer_system_size
                        WHERE $nop BETWEEN amtmin AND amtmax AND status = 1
                        ")->row();
                        if ($sql_size_max) {
                            $system_size = $sql_size_max->descs;
                            $system_size_id = $sql_size_max->sysid;
                        }
                    }
                }
            }
        }


        if($posttype>0) {

            $ins_array = array(
                'appid' => $appid,
                'sysize' => $system_size_id,
                'l1l2' => $l1l2,
                'l1l3' => $l1l3,
                'l2l3' => $l2l3,
                'l1g' => $l1g,
                'l2g' => $l2g,
                'l3g' => $l3g,
                'l1l2a' => $l1l2a,
                'l1l3a' => $l1l3a,
                'l2l3a' => $l2l3a,
                'power' => $power,
                'nop' => $nop,
                'rateclass' => $rateclass,
                'paneltype' => $paneltype,
                'inspectiondate' => $inspectiondate,
                'remarks' => $remarks,
                'createdby' => user_id()
            );

            if($posttype==1) {
                $this->db->trans_begin();
                $transtatus = array();
                if($this->db->insert('application_customers_system_size', $ins_array)) {
                    $transtatus['insert_system_size'] = true;
                    $msg = 'System compute has been saved!';
                    $func = 'info';

                    $logid = $this->db->insert_id();

                    if (isset($surveydetail) && count($surveydetail) > 0) {
                        foreach ($surveydetail AS $key => $value) {
                            if ($value['measurements'] != '' || $value['remarks'] != '') {
                                $ins_details = $value;
                                $ins_details['appid'] = $appid;
                                $ins_details['infotype'] = $key;
                                $ins_details['logid'] = $logid;
                                $ins_details['createdby'] = user_id();

                                if ($this->db->insert('application_customers_survey_details',$ins_details)) {
                                    $transtatus['insert_survey_detail'] = true;
                                } else {
                                    $transtatus['insert_survey_detail'] = false;
                                }
                            }
                        }
                    }

                    $ins_arr_info = array(
                        'logid' => $logid,
                        'rooforientation' => $rooforientation,
                        'rooftype' => $roofing,
                        'roofinclination' => $roofinclination,
                        'voltdropcondition' => $vdcondition,
                        'generatorrating' => $gensetrate,
                        'roofdimensions' => $roofdimension,
                        'electricalplan' => $esplans,
                        'loadsforclamping' => $forclamping,
                        'billingdetails' => $billingdetails,
                        'daytimeappliances' => $dtappliances,
                        'createdby' => user_id()
                    );

                    if ($this->db->insert('application_customers_survey_info',$ins_arr_info)) {
                        $transtatus['insert_survey_info'] = true;
                    } else {
                        $transtatus['insert_survey_info'] = false;
                    }

                    $data['surveyid'] = $logid;
                } else {
                    $transtatus['insert_system_size'] = false;
                    $msg = 'System compute not saved!';
                    $func = 'warning';
                }

                if (in_array(false,$transtatus)) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }
            }

            if($posttype==2) {
                $this->db->trans_begin();
                $transtatus = array();
                if ($surveyid) {
                    $process = $this->db->update('application_customers_system_size', array('status' => 305), array('sysid' => $surveyid));
                    if ($process) {
                        $transtatus['update_stataus'] = true;
                    } else {
                        $transtatus['update_stataus'] = false;
                    }
                } else {
                    $find_exist = $this->db->select('')
                        ->from('application_customers_system_size')
                        ->where($ins_array)
                        ->get()->row();

                    if ($find_exist) {
                        $process = $this->db->update('application_customers_system_size', array('status' => 305), array('sysid' => $find_exist->sysid));
                        if ($process) {
                            $transtatus['update_stataus'] = true;
                        } else {
                            $transtatus['update_stataus'] = false;
                        }
                    } else {
                        $this->db->set('status', 305);
                        $process = $this->db->insert('application_customers_system_size', $ins_array);

                        if ($process) {
                            $logid = $this->db->insert_id();
                            $transtatus['insert_published_size'] = true;
                            if (isset($surveydetail) && count($surveydetail) > 0) {
                                foreach ($surveydetail AS $key => $value) {
                                    if ($value['measurements'] != '' || $value['remarks'] != '') {
                                        $ins_details = $value;
                                        $ins_details['appid'] = $appid;
                                        $ins_details['logid'] = $logid;
                                        $ins_details['createdby'] = user_id();

                                        if ($this->db->insert('application_customers_survey_details', $ins_details)) {
                                            $transtatus['insert_survey_detail'] = true;
                                        } else {
                                            $transtatus['insert_survey_detail'] = false;
                                        }
                                    }
                                }
                            }
                        } else {
                            $transtatus['insert_published_size'] = false;
                        }
                    }
                }

                if($process) {
                    $transtatus['process'] = true;
                    $update_system_size = $this->db->update('application_customers_details',array('systemsizeid' => $system_size_id),array('sysid' => $appid));
                    if ($update_system_size) {
                        $transtatus['update_customer_details'] = true;
                    } else {
                        $transtatus['update_customer_details'] = false;
                        $msg = 'Failed to update system size in application data!';
                        $func = 'error';
                    }

                    /*$tssr = json_decode($this->print_tssr($appid));
                    if ($tssr && $tssr->html != '') {
                        $insert = array(
                            'dataid' => $appid,
                            'doctype' => 3436,
                            'html' => $tssr->html,
                            'createdby' => user_id(),
                        );
                        if ($this->db->insert('prime_documents_main', $insert)) {
                            $transtatus['finalize_document'] = true;
                        } else {
                            $transtatus['finalize_document'] = false;
                        }
                    }*/
                } else {
                    $transtatus['process'] = false;
                }

                if (in_array(false,$transtatus)) {
                    $this->db->trans_rollback();
                    $msg = 'System computation not published!';
                    $func = 'warning';
                } else {
                    $this->db->trans_commit();
                    $msg = 'System computation has been published!';
                    $func = 'success';
                }
            }
            $data['transtatus'] = $transtatus;

            if ($system_size_id == 0) {
                // @TODO: If System size is NON-STANDARD, change flowid to and stageid of application to Non-Standard application flow.
            }
        }



        $data['phase'] = $phase;
        $data['power'] = $power;
        $data['nop'] = $nop;
        $data['system_size_text'] = $system_size;
        $data['power_text'] = number_format($power, 2);
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['pub'] = $published;
        $data['surveydetail'] = $surveydetail;
        return json_encode($data);
    }

    function update_map_url() {
        $data = array();
        $appid = $this->input->post('appid');
        $googlemap = $this->input->post('url');
        $typesid = 340;
        $qry = false;

        // UPDATE FIRST
        $this->db->update('application_customers_geodata',
            array(
                'status' => 0,
                'updatedby' => user_id(),
            ),
            array(
                'appid' => $appid,
                'typesid' => $typesid,
                'status' => 1
            ));

        // MAP : application_customers_geodata
        $get_latlon = explode('@', $googlemap);
        if(is_array($get_latlon) && count($get_latlon) > 1) {
            $latlon_arr = explode(',', $get_latlon[1]);
            $lat = (isset($latlon_arr[0])) ? $latlon_arr[0] : '';
            $lon = (isset($latlon_arr[1])) ? $latlon_arr[1] : '';
            $zoom = (isset($latlon_arr[2])) ? str_replace('z', '', $latlon_arr[2]) : '';
            $ins_geodata = array(
                'appid' => $appid,
                'lat' => $lat,
                'lon' => $lon,
                'alt' => $zoom,
                'url' => $googlemap,
                'typesid' => $typesid,
                'remarks' => 'Inspection Google Map URL',
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );
            $this->db->insert("application_customers_geodata", $ins_geodata);
            $insert_id = $this->db->insert_id();
            $data['id'] = $insert_id;
            $data['lat'] = $lat;
            $data['lng'] = $lon;
            $data['id'] = $lon;
            $qry = true;
        }
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_active_survey() {
        $data = array();
        $dataid = $this->input->post('dataid');

        $selected = $this->db->select()
            ->from('application_customers_system_size')
            ->where('sysid',$dataid)
            ->get()->row();

        if ($selected) {
            //$data['selected'] = $selected;
            foreach ((array)$selected as $key => $value) {
                $data['selected'][$key] = (!is_numeric($value) || $value > 0) ? (is_numeric($value) ? (float)$value : $value) : '';
            }

            $details_qry = $this->db->select()
                ->from('application_customers_survey_details')
                ->where(array('logid' => $selected->sysid,'status' => 1))
                ->get();

            if ($details_qry->num_rows() > 0) {
                foreach ($details_qry->result() AS $details) {
                    $infotype = $details->infotype;
                    foreach ($details as $detkey=>$detval) {
                        if ($detkey == 'measurements' || $detkey == 'remarks') {
                            $data['selected']['surveydetail['.$infotype.']['.$detkey.']'] = $detval;
                        }
                    }
                }
            }

            $info_qry = $this->db->select()
                ->from('application_customers_survey_info')
                ->where(array('logid' => $selected->sysid,'status' => 1))
                ->get()->row();

            if ($info_qry) {
                $data['selected']['rooforientation'] = $info_qry->rooforientation;
                $data['selected']['roofing'] = $info_qry->rooftype;
                $data['selected']['roofinclination'] = $info_qry->roofinclination;
                $data['selected']['vdcondition'] = $info_qry->voltdropcondition;
                $data['selected']['gensetrate'] = $info_qry->generatorrating;
                $data['selected']['roofdimension'] = $info_qry->roofdimensions;
                $data['selected']['esplans'] = $info_qry->electricalplan;
                $data['selected']['forclamping'] = $info_qry->loadsforclamping;
                $data['selected']['dtappliances'] = $info_qry->daytimeappliances;
            }
        }

        return json_encode($data);
    }

    function get_sps_items_list() {
        $data = array();
        $appid = $this->input->post('appid');
        $itemtype = $this->input->post('itemtype');

        $comp = array();
        $acce = array();
        $cons = array();
        $option5y = 0;
        $option10y = 0;
        $count10yrs = 0;
        $count5yrs = 0;
        $msg = '';

        $empty = array(
            '','No system components loaded!','No system Accessories loaded!','No installation consumables loaded!'
        );

        $system_parts_qry = $this->db->select('csp.*,imd.fulldescription,u.unit_code as unit')
            ->from('customer_system_parts AS csp')
            ->join('items_main_description as imd','csp.itemid = imd.sysid and imd.status = 1','left')
            ->join('prime_unit as u','csp.unitid = u.sysid','left')
            ->where(array('csp.appid' => $appid,'csp.status !=' => 0))->get();

        if ($system_parts_qry->num_rows() > 0) {
            $compn = 1;
            foreach ($system_parts_qry->result() AS $parts) {
                $control = '';
                $totalprice = $parts->unitprice * $parts->qty;
                $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_item""><i class="fa fa-edit"></i> </a>';
                $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_item" data-id="'.$parts->sysid.'"><i class="fa fa-times"></i> </a>';
                $control .= '</div>';
                if ($parts->type == $itemtype) {
                    $comp[] = array(
                        'num' => '<input type="hidden" id="input_id" value="'.$parts->sysid.'" name="sysid" disabled>'.$compn++,
                        'item' => $parts->fulldescription,
                        'unit' => $parts->unit,
                        'qty' => dt_inline_input('qty',$parts->qty,array('width' => '50px !important')),
                        'price' => dt_inline_input('unitprice',number_format($parts->unitprice,2),array('width' => '100px !important')),
                        'total' => number_format($totalprice,2),
                        'control' => $control
                    );
                }

                if (!strpos($parts->fulldescription,'10 yrs')) {
                    $option5y += $totalprice;
                }
                if (!strpos($parts->fulldescription,'5 yrs')) {
                    $option10y += $totalprice;
                }

                if (strpos($parts->fulldescription,'10 yrs')) {
                    $count10yrs++;
                }
                if (strpos($parts->fulldescription,'5 yrs')) {
                    $count5yrs++;
                }
            }
        } else {
            $msg = $empty[$itemtype];
        }

        $data['parts'] = $comp;
        $data['total5yrplan'] = ($count5yrs > 0) ? number_format($option5y,2) : 0.00;
        $data['total10yrplan'] = ($count10yrs > 0) ? number_format($option10y,2) : 0.00;
        $data['msg'] = $msg;

        return json_encode($data);
    }

    function search_setup_template() {
        $data = array();
        $sptypeid = $this->input->post('sptypeid');
        $nos = $this->input->post('nos');
        $nop = $this->input->post('nop');
        $panelsperstring = $this->input->post('panelsperstring');
        $invertersize = $this->input->post('invertersize');
        $appid = $this->input->post('appid');

        $html = '';
        $results = array();

        if ($sptypeid != '') {
            $this->db->where('sptypeid',$sptypeid);
        }
        if ($nos != '') {
            $this->db->where('nos',$nos);
        }
        if ($nop != '') {
            $this->db->where('nop',$nop);
        }
        if ($panelsperstring != '') {
            $this->db->where('panelsperstring',$panelsperstring);
        }
        if ($invertersize != '') {
            $this->db->where('invertersize',$invertersize);
        }

        $search_qry = $this->db->select('sysid')
            ->from('customer_system_group_template')
            ->where('status',1)
            ->get();

        $result_cnt = $search_qry->num_rows();

        if ($result_cnt > 0) {
            foreach ($search_qry->result() AS $row) {
                $results[] = $row->sysid;
            }

            $result_str = implode(',',$results);

            $html .= '<div class="note note-success">';
            $html .= '<a class="bold" href="#select_template_list" title="Select Template" data-toggle="ajax-modal" data-view="'.$appid.'" data-arr="'.$result_str.'" data-container="body">';
            $html .= '<h4>Template(s) found: '.$result_cnt.'</h4>';
            $html .= '</a>';
            $html .= '</div>';
        }

        $data['html'] = $html;
        $data['result'] = $results;

        return json_encode($data);
    }

    function panels_per_string_lookup() {
        $res = array();
        $q = $this->input->get('query');
        $nos = $this->input->get('nos');
        $nop = $this->input->get('nop');
        $sptypeid = $this->input->get('sptypeid');

        $lookup = $this->db->select('*')
            ->from('customer_system_group_template')
            ->where(array(
                'sptypeid' => $sptypeid,
                'nop' => $nop,
                'nos' => $nos,
                'status' => 1
            ))
            ->where("panelsperstring LIKE '%$q%'")
            ->get();

        if ($lookup->num_rows() > 0) {
            foreach ($lookup->result() as $row) {
                $res[] = array(
                    'codes' => $row->panelsperstring,
                    'names' => $row->panelsperstring
                );

            }
        }
        return json_encode($res);
    }

    function inverter_size_lookup() {
        $res = array();
        $q = $this->input->get('query');
        $nos = $this->input->get('nos');
        $nop = $this->input->get('nop');
        $sptypeid = $this->input->get('sptypeid');
        $panelsperstring = $this->input->get('panelsperstring');

        $lookup = $this->db->select('invertersize')
            ->from('customer_system_group_template')
            ->where(array(
                'sptypeid' => $sptypeid,
                'nop' => $nop,
                'nos' => $nos,
                'panelsperstring' => $panelsperstring,
                'status' => 1
            ))
            ->where("invertersize LIKE '%$q%'")
            ->get();

        if ($lookup->num_rows() > 0) {
            foreach ($lookup->result() as $row) {
                $res[] = array(
                    'codes' => $row->invertersize,
                    'names' => $row->invertersize
                );
            }
        }
        return json_encode($res);
    }

    function template_list() {
        $data = array();
        $ids = $this->input->post('ids');

        $single = (!is_array($ids)) ? true : false;

        if ($single) {
            $this->db->where('t.sysid',$ids);
        } else {
            $this->db->where_in('t.sysid',$ids);
        }

        $template_qry = $this->db->select('t.sysid, t.name, css.descs as systemtype, spt.descs as paneltype, t.nop, t.nos, t.panelsperstring, t.invertersize')
            ->from('customer_system_group_template as t')
            ->join('customer_system_size as css','t.systypeid = css.sysid','left')
            ->join('solar_panel_types as spt','t.sptypeid = spt.sysid AND spt.status = 1','left')
            ->where('t.status',1)->get();

        $qry = $this->db->last_query();

        if ($single) {
           $template = $template_qry->row();
           $data['details'] = array(
                    'name' => $template->name,
                    'systemtype' => $template->systemtype,
                    'paneltype' => $template->paneltype,
                    'nop' => $template->nop,
                    'nos' => $template->nos,
                    'panelsperstring' => $template->panelsperstring,
                    'invertersize' => $template->invertersize,
                );
        } else {
            if ($template_qry->num_rows() > 0) {
                $num = 1;
                foreach ($template_qry->result() as $templates) {
                    $select = '<a class="btn btn-primary inline" id="btn_select_template" href="' . base_url() . 'inspection/loadselectedtemplate" data-id="' . $templates->sysid . '"><i class="fa fa-download"></i></a>';
                    $data['list'][] = array(
                        'num' => btn_expand($templates->sysid) . ' ' . $num++,
                        'name' => $templates->name,
                        'systemtype' => $templates->systemtype,
                        'paneltype' => $templates->paneltype,
                        'nop' => $templates->nop,
                        'nos' => $templates->nos,
                        'panelsperstring' => $templates->panelsperstring,
                        'invertersize' => $templates->invertersize,
                        'select' => $select
                    );
                }
            }
        }

        //$data['query'] = $qry;

        return json_encode($data);
    }

    function template_details() {
        $data = array();
        $id = $this->input->post('id');
        $html = $this->load->view('admin/pages/modules/planassess/templatedetails',array('id' => $id),true);

        $data['html'] = $html;

        return json_encode($data);
    }

    function get_sps_items_list_template() {
        $data = array();
        $groupid = $this->input->post('groupid');
        $type = $this->input->post('type');

        $comp = array();
        $acce = array();
        $cons = array();
        $option5y = 0;
        $option10y = 0;
        $count10yrs = 0;
        $count5yrs = 0;
        $msg = '';

        $empty = array(
            '','No system components loaded!','No system Accessories loaded!','No installation consumables loaded!'
        );

        $system_parts_qry = $this->db->select('csp.*,imd.fulldescription,u.unit_code as unit')
            ->from('customer_system_parts_template AS csp')
            ->join('items_main_description as imd','csp.itemid = imd.sysid and imd.status = 1','left')
            ->join('prime_unit as u','csp.unitid = u.sysid','left')
            ->where(array('csp.groupid' => $groupid,'csp.status !=' => 0))->get();

        if ($system_parts_qry->num_rows() > 0) {
            $num = 1;
            $accen = 1;
            $consn = 1;
            foreach ($system_parts_qry->result() AS $parts) {
                $totalprice = $parts->unitprice * $parts->qty;
                //load only type
                if ($parts->type == $type) {
                    $comp[] = array(
                        'num' => $num++,
                        'item' => $parts->fulldescription,
                        'unit' => $parts->unit,
                        'qty' => $parts->qty,
                        'price' => number_format($parts->unitprice,2),
                        'total' => number_format($totalprice,2)
                    );
                }
                /*if ($parts->type == 2) {
                    $acce[] = array(
                        'num' => $accen++,
                        'item' => $parts->fulldescription,
                        'unit' => $parts->unit,
                        'qty' => $parts->qty,
                        'price' => number_format($parts->unitprice,2),
                        'total' => number_format($totalprice,2)
                    );
                }
                if ($parts->type == 3) {
                    $cons[] = array(
                        'num' => $consn++,
                        'item' => $parts->fulldescription,
                        'unit' => $parts->unit,
                        'qty' => $parts->qty,
                        'price' => number_format($parts->unitprice,2),
                        'total' => number_format($totalprice,2)
                    );
                }*/

                if (!strpos($parts->fulldescription,'10 yrs')) {
                    $option5y += $totalprice;
                }
                if (!strpos($parts->fulldescription,'5 yrs')) {
                    $option10y += $totalprice;
                }

                if (strpos($parts->fulldescription,'10 yrs')) {
                    $count10yrs++;
                }
                if (strpos($parts->fulldescription,'5 yrs')) {
                    $count5yrs++;
                }

            }
        } else {
            $msg = $empty[$type];
        }


        $data['parts'] = $comp;
        $data['total5yrplan'] = ($count5yrs > 0) ? number_format($option5y,2) : 0.00;
        $data['total10yrplan'] = ($count10yrs > 0) ? number_format($option10y,2) : 0.00;
        $data['msg'] = $msg;

        return json_encode($data);
    }

    function load_selected_template() {
        $data = array();
        $templateid = $this->input->post('id');
        $appid = $this->input->post('appid');
        $groupid = 0;
        $qry = false;
        $msg = '';
        $func = 'error';
        $title = '';
        $errorcnt = 0;

        $setup_qry = $this->db->select('sysid,systypeid,sptypeid,nop,nos,panelsperstring,invertersize')
            ->from('customer_system_group_template')
            ->where(array('sysid' => $templateid,'status' => 1))
            ->get()->row();

        if ($setup_qry) {
            $gp_array = array(
                'appid' => $appid,
                'systypeid' => $setup_qry->systypeid,
                'sptypeid' => $setup_qry->sptypeid,
                'nop' => $setup_qry->nop,
                'nos' => $setup_qry->nos,
                'panelsperstring' => $setup_qry->panelsperstring,
                'invertersize' => $setup_qry->invertersize,
                'createdby' => user_id()
            );

            $insert = $this->db->insert('customer_system_group',$gp_array);
            if ($insert) {
                $groupid = $setup_qry->sysid;
                $this->db->update('application_customers_system_size',array('sysize' => $setup_qry->systypeid),array('appid' => $appid,'status' => 305));
                $this->db->update('application_customers_details',array('systemsizeid' => $setup_qry->systypeid),array('sysid' => $appid,'status' => 1));
            }
        } else {
            $msg = 'Failed to apply setup from template.';
        }

        if ($groupid > 0) {
            $items_qry = $this->db->select('itemid,unitid,qty,type,unitprice')
                ->from('customer_system_parts_template')
                ->where(array('groupid' => $groupid,'status' => 1))
                ->get();

            if ($items_qry->num_rows() > 0) {
                foreach ($items_qry->result() as $item) {
                    $item_ins_arr = array(
                        'appid' => $appid,
                        'itemid' => $item->itemid,
                        'unitid' => $item->unitid,
                        'qty' => $item->qty,
                        'type' => $item->type,
                        'unitprice' => $item->unitprice,
                        'createdby' => user_id()
                    );

                    $items_insert = $this->db->insert('customer_system_parts',$item_ins_arr);

                    if (!$items_insert) {
                        $errorcnt++;
                    }
                }
            }
        }

        if ($errorcnt > 0) {
            $msg = $errorcnt.' item(s) failed to insert.';
            $title = 'ERROR!!!';
        } else {
            $msg = 'All template items successfully applied to customer setup.';
            $func = 'success';
            $title = 'Import Success!!!';
            $qry = true;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function get_saved_system_size() {
        $data = array();
        $appid = $this->input->post('appid');
        $details = get_application_details($appid);
        $data['sizeid'] = ($details->info->systemsizeid != null) ? $details->info->systemsizeid : false;

        $setup_qry = $this->db->select('sysid,sptypeid,nop,nos,panelsperstring,invertersize')
            ->from('customer_system_group')
            ->where(array('appid' => $appid, 'status' => 1))
            ->get()->row();

        if ($setup_qry) {
            $data['details'] = $setup_qry;
        }

        return json_encode($data);
    }

    function update_installation_item() {
        $data = array();
        $sysid = $this->input->post('sysid');
        $qty = $this->input->post('qty');
        $price = $this->input->post('unitprice');

        $old_gtotal = 0;

        $old_qry = $this->db->select('qty,unitprice')
            ->from('customer_system_parts')
            ->where(array('sysid' => $sysid))
            ->get()->row();

        if ($old_qry) {
            $data['oldtotal'] = $old_qry->qty * $old_qry->unitprice;
        }

        $update = $this->db->update('customer_system_parts',array('qty' => $qty,'unitprice' => $price),array('sysid' => $sysid));

        if ($update) {
            $data['total'] = number_format($qty * $price,2);
            $data['values'] = array(
                'sysid' => $sysid,
                'qty' => number_format($qty),
                'unitprice' => number_format($price,2),
            );
        }

        return json_encode($data);
    }

    function select2_application_sps_items() {
        $data = array();
        $ids = $this->input->post('data');
        $id = explode(',',$ids);
        $appid = $id[1];
        $itemtype = $id[0];
        $list=array();

        $app_sps_qry = $this->db->select('itemid')
            ->from('customer_system_parts')
            ->where(array('appid' => $appid,'type' => $itemtype,'status' => 1))
            ->get();

        if ($app_sps_qry->num_rows() > 0) {
            $spsitems = array_column($app_sps_qry->result(),'itemid');
            $this->db->where_not_in('p.itemid',$spsitems);
        }

        $sps_items_qry = $this->db->select('p.sysid,d.fulldescription')
            ->from('application_customer_system_parts as p')
            ->join('items_main_description as d','p.itemid = d.sysid','left')
            ->where(array('p.type' => $itemtype,'p.status' => 1))
            ->get();

        if ($sps_items_qry->num_rows() > 0) {
            foreach ($sps_items_qry->result() as $item) {
                $list[] = array(
                    'id' => $item->sysid,
                    'text' => $item->fulldescription
                );
            }
        }

        $data['list'] = $list;

        return json_encode($data);
    }

    function get_sps_item_defaults() {
        $data = array();
        $itemid = $this->input->post('itemid');

        $item_qry = $this->db->select('p.sysid,p.type,d.fulldescription,u.unit_code as unit,p.unitprice')
            ->from('application_customer_system_parts AS p')
            ->join('items_main_description AS d','p.itemid = d.sysid','left')
            ->join('prime_unit AS u','p.unitid = u.sysid','left')
            ->where(array('p.sysid' => $itemid,'p.status' => 1))
            ->get()->row();

        if ($item_qry) {
            $data = $item_qry;
        }

        return json_encode($data);
    }

    function add_sps_item() {

    }

    function upload_survey_pics() {
        $data = array();
        $qry = false;
        $msg = '';
        $hascontract = false;

        $this->load->helper('directory');
        $this->load->library('upload');

        if(isset($_FILES["appfiledrop"])) {
            $dataid = $this->input->post('dataid');
            $stageid = $this->input->post('stageid');
            $filetype = $this->input->post('filetype');

            $filename = $_FILES['appfiledrop']['name'];
            $fileinfo = pathinfo($filename);

            $appinfo = get_application_details($dataid)->info;
            //$name = str_replace(' ','_',$appinfo->firstname) . '_' . str_replace(' ','_',$appinfo->lastname);

            //$type_name = ($filetype && trim($filetype) != '') ? '_TYPE-'. $filetype : '';
            $location = get_stage_specific($stageid)->desc;
            $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/".$location.'/Survey';

            $file_name = $fileinfo['filename'];
            $extract = explode('_',$file_name);
            $data['splitname'] = $extract;

            $filetype = (is_array($extract) && count($extract) > 0) ? $extract[0] : $file_name;
            $count = (is_array($extract) && count($extract) > 0) ? (($extract[1] != '') ? '_'.$extract[1] : '') : '';

            $data['filetype'] = $filetype;
            if (strpos($filetype,'PAE') === false) {
                $filename = strtoupper(strtolower($filetype)).'_PAE'.str_pad($appinfo->essrno, 6, "0", STR_PAD_LEFT).$count.'.' . strtolower($fileinfo['extension']);
            } else {
                $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/".$location.'/Docs';
            }

            $upload = sys_upload_files('appfiledrop',$file_directory,$filename);
            $data['upload'] = $upload;

            if ($upload) {
                $msg = 'Files Uploaded!';
                $qry = true;
            }
        }else{
            $msg = 'Drop the file again!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['contract'] = $hascontract;

        return json_encode($data);
    }

    function print_tssr($id = false, $save = false) {
        $id = ($id) ? $id : $this->input->post('id');
        $data = array();
        $info = array();
        $info['appid'] = $id;

        $app = application_info($id);
        $title = '';
        $data['docid'] = false;
        if ($app) {
            $info['app'] = $app;
            $title = 'PAE'.str_pad($app->essrno, 5, "0", STR_PAD_LEFT).' - '.ucwords(strtolower($app->appname)).' TSSR';
        }

        /*$saved = $this->db->select('sysid,html')
            ->from('prime_documents_main')
            ->where(array('dataid' => $id, 'doctype' => 3436, 'status' => 1))
            ->get()->row();

        if ($saved) {
            $html = $saved->html;
            $data['docid'] = $saved->sysid;
        } else {*/
            $published_qry = $this->db->select()
                ->from('application_customers_system_size')
                ->where(array('appid' => $id, 'status =' => 305))
                ->get()->row();

            if ($published_qry) {
                $survey = $published_qry;
                $info['survey'] = $survey;
                $creator = user_info($survey->createdby);
                $info['author'] = ucwords(strtolower($creator->firstname)) . (($creator->middlename) ? ' ' . $creator->middlename[0] . '.' : '') . ' ' . ucwords(strtolower($creator->lastname));
                $details_qry = $this->db->select()
                    ->from('application_customers_survey_details')
                    ->where(array('logid' => $survey->sysid, 'status' => 1))
                    ->get();

                if ($details_qry->num_rows() > 0) {
                    foreach ($details_qry->result() AS $details) {
                        $infotype = $details->infotype;
                        $details = (array)$details;
                        foreach ($details as $detkey => $detval) {
                            if ($detkey == 'measurements' || $detkey == 'remarks') {
                                $info['details'][$infotype][$detkey] = $detval;
                            }
                        }
                    }
                }

                $info_qry = $this->db->select()
                    ->from('application_customers_survey_info')
                    ->where(array('logid' => $survey->sysid, 'status' => 1))
                    ->get()->row();

                if ($info_qry) {
                    $info['info'] = $info_qry;
                }
            }

            $team_qry = $this->db->select('empid')
                ->from('application_customers_team_assignment')
                ->where(array('appid' => $id, 'moduleid' => 15, 'status' => 1))
                ->get();

            if ($team_qry->num_rows() > 0) {
                foreach ($team_qry->result() as $row) {
                    $person = get_employee_info($row->empid);
                    $info['team'][] = ucwords(strtolower($person->firstname)) . (($person->middlename) ? ' ' . $person->middlename[0] . '.' : '') . ' ' . ucwords(strtolower($person->lastname));
                }
            }

            $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/Assessment/Survey/";
            $file_url = base_url() . "uploads/attachments/cad/applications/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/Assessment/Survey/";
            $map = directory_map($file_directory, FALSE, TRUE);
            $files = array();

            if ($map && count($map) > 0) {
                foreach ($map as $file) {
                    $filename = explode('_', $file);
                    if (isset($filename[2])) {
                        $files[strtolower($filename[0])][] = $file_directory . $file;
                    } else {
                        $files[strtolower($filename[0])] = $file_directory . $file;
                    }
                }
            }

            $info['files'] = $files;

            $html = $this->load->view('custom/templates/tssr', $info, true);

        $data['title'] = $title;
        $data['info'] = $info;
        $data['html'] = $html;
        return json_encode($data);
    }

    function print_installation_setup() {
        $data = array();
        $info = array();

        $html = $this->load->view('custom/templates/installationsetup',$info,true);

        //$data['title'] = $title;
        $data['info'] = $info;
        $data['html'] = $html;
        return json_encode($data);
    }



}
