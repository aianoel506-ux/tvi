<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if(!function_exists('get_findings_label')) {
    function get_findings_label($id)
    {
        $ci = &get_instance();
        $qry = $ci->db->select('codes')->from('meter_reading_findings AS f')
            ->where('f.sysid', $id)
            ->get()->row();
        return ($qry) ? $qry->codes : '';
    }
}

if (!function_exists('check_acct_gdr')) {

    function check_acct_gdr($dataid) {
        $ci = &get_instance();
        $qr = $ci->db->select("
                rl.totalwatt,
                rl.totalcost,
                rl.dailyop,
                rl.demand,
                rl.monthlyop,
                mgr.rates
            ")
            ->select("CONCAT(cm.codes, ' - ', cm.classifications) AS rateclassname", false)
            ->from('application_customers_gdr_logs AS rl')
            ->join('prime_system_rate_class_main AS cm', 'cm.sysid = rl.rateclassid')
            ->join('trn_application_monthly_gdr_rates AS mgr', 'mgr.rateid = cm.sysid AND mgr.status = 1')
            ->where(array('rl.appid' => $dataid, 'rl.status' => 1))
            ->get()->row();
        $return = ($qr) ? $qr : false;
        return $return;
    }

    if (!function_exists('check_acct_app')) {

        function check_acct_app($dataid) {
            $data = array();
            $ecales = array();
            $ci = &get_instance();
            $ecales = $ci->db->select()
                ->from('customer_ecales_logs')
                ->where(array('dataid' => $dataid))
                ->get()->row();
            $data['type'] = ($ecales) ? 2 : 1;
            $data['ecales'] = (object) array(
                'logged' => ($ecales && $ecales->status == 0) ? true : false,
                'status' => ($ecales && $ecales->status == 2) ? 2 : 0,
            );
            return (object) $data;
        }

    }
    if (!function_exists('items_select_list')) {

        function items_select_list() {
            $data = array();
            $ci = &get_instance();
            $qry = $ci->db->select('ms.sysid, m.codes, ms.descs')
                ->from('items_main_spec AS ms')
                ->join('items_main_category AS m', 'm.sysid = ms.itemid')
                ->where(array('m.types' => 2, 'ms.status' => 1))
                ->get();
            if ($qry->num_rows() > 0) {
                foreach ($qry->result() as $row) {
                    $data['list'][] = array(
                        'id' => $row->sysid,
                        'text' => $row->codes . ' - ' . $row->descs,
                    );
                }
            }
            return json_encode($data);
        }

    }

    if (!function_exists('get_item_info')) {

        function get_item_info($itemspecid) {
            $ci = &get_instance();
            $qry = $ci->db->select('
                ms.sysid, 
                ms.descs, 
                pq.amt, 
                CAST(pqa.datecreated AS DATE) AS dateapproved, 
                cb.names, 
                cb.sysid as suppid, 
                pq.sysid AS quoteid
            ')
                ->from('items_main_spec AS ms')
                ->join('items_main_category AS m', 'm.sysid = ms.itemid', 'left')
                ->join('trn_prs_quotations AS pq', 'pq.itemspecid = ms.sysid', 'left')
                ->join('trn_prs_quotations_approval AS pqa', 'pqa.quoteid = pq.sysid AND pqa.status = 1', 'left')
                ->join('corporation_branches AS cb', 'cb.sysid = pq.suppid', 'left')
                ->where(array('ms.sysid' => $itemspecid))
                ->order_by('pq.datecreated', 'desc')
                ->get()->row();
            return ($qry) ? $qry : false;
        }

    }

    if (!function_exists('get_service_info')) {

        function get_service_info($serviceid) {
            $ci = &get_instance();
            $qry = $ci->db->select()
                ->from('prime_service_rate_history')
                ->where(array('serviceid' => $serviceid , 'status' => 1))
                ->order_by('sysid','DESC')->get()->row();
            return ($qry) ? $qry : false;
        }

    }

    if (!function_exists('ecales_id')) {
        function ecales_id($dataid) {
            $ci = &get_instance();
            $qry = $ci->db->select()->from('customer_ecales_logs')
                ->where(array('dataid' => $dataid))
                ->where_not_in('status',array(0,303))->get()->row();
            return ($qry) ? $qry : false;
        }
    }


    if (!function_exists('customer_mapping')) {

        function customer_mapping($dataid) {
            $ci = &get_instance();
            $data = array();

            $qry_account_owner_addr = $ci->db->select('addrspec')
                ->from('application_customers_details')
                ->where(array('sysid' => $dataid))
                ->get()->row();

            $qry_account_geodata_meter = $ci->db->select()
                ->from('application_customers_geodata')
                ->where(array('appid' => $dataid, 'status' => 1, 'typesid' => 320))
                ->get()->row();

            $qry_account_geodata_house = $ci->db->select()
                ->from('application_customers_geodata')
                ->where(array('appid' => $dataid, 'status' => 1, 'typesid' => 340))
                ->get()->row();


            $lat_meter = ($qry_account_geodata_meter && $qry_account_geodata_meter->lat != '') ? $qry_account_geodata_meter->lat : 10.7386362;
            $lon_meter = ($qry_account_geodata_meter && $qry_account_geodata_meter->lon != '') ? $qry_account_geodata_meter->lon : 122.500639;
            $sysid_meter = ($qry_account_geodata_meter) ? $qry_account_geodata_meter->sysid : 1;
            $alt_meter = ($qry_account_geodata_meter) ? $qry_account_geodata_meter->alt : 12;
            $meter = ($qry_account_geodata_meter) ? true : false;

            $lat_house = ($qry_account_geodata_house) ? $qry_account_geodata_house->lat : $qry_account_geodata_meter->lat;
            $lon_house = ($qry_account_geodata_house) ? $qry_account_geodata_house->lon : $qry_account_geodata_meter->lon;
            $map_url = ($qry_account_geodata_house) ? $qry_account_geodata_house->url : '';
            $sysid_house = ($qry_account_geodata_house) ? $qry_account_geodata_house->sysid : 1;
            $alt_house = ($qry_account_geodata_house) ? $qry_account_geodata_house->alt : 12;
            $house = ($qry_account_geodata_house) ? true : false;

            $addr_spec = ($qry_account_owner_addr) ? $qry_account_owner_addr->addrspec : '';

            $data['lathouse'] = $lat_house;
            $data['lnghouse'] = $lon_house;
            $data['althouse'] = $alt_house;
            $data['mapurl'] = $map_url;
            $data['sysidhouse'] = $sysid_house;
            $data['house'] = $house;

            $data['latmeter'] = $lat_meter;
            $data['lngmeter'] = $lon_meter;
            $data['altmeter'] = $alt_meter;
            $data['sysidmeter'] = $sysid_meter;
            $data['meter'] = $meter;

            $data['spec'] = $addr_spec;
            return $data;
        }

    }

    function account_verifications($person = false) {
        $ci = &get_instance();
        $personid = $ci->input->post('personid');
        $profiletype = $ci->input->post('profiletype');
        if($personid==false) {
            if($person) {
                $personid = $person;
            }else{
                $personid = false;
            }
        }
        $data = array();
        $html = '';
        $html_num = 0;
        if($personid) {
            // 1st Check : RA7832
            $query = verify_query(150, $personid);
            if ($query) {
                $title = 'R.A. 7832';
                $icon = 'fa-times';
                $func = 'alert-danger';

                if ($query->num_rows() > 0) {
                    if (is_array($personid)) {
                        $message = 'There is ' . $query->num_rows() . ' match found in apprehension record!';
                        $html_num += 1;
                        $html .= verify_html($title, $message, $icon, $func);
                    } else {
                        foreach ($query->result() as $row) {
                            $message = 'There is ' . $query->num_rows() . ' match found in apprehension record!';
                            $html_num += 1;
                            $html .= verify_html($title, $message, $icon, $func);
                        }
                    }
                } else {
                    $html .= verify_html('R.A. 7832 (New)', 'RA 7832 Clear', 'fa-check', 'alert-success');
                }
            }
            // 2nd Check : RA7832 LEGACY QUERY
            $query = veriry_legacy_ra7832($personid);
            if ($query) {
                $title = 'R.A. 7832 (Legacy)';
                $icon = 'fa-times';
                $func = 'alert-warning';
                if (is_array($personid) && $query->num_rows() > 0) {
                    $message = 'There is a ' . $query->num_rows() . ' match found in our R.A.7832 database!';
                    $html_num += 1;
                    $html .= verify_html($title, $message, $icon, $func);
                } else {
                    if ($query->num_rows() > 0) {
                        $message = 'There is a ' . $query->num_rows() . ' match found in our R.A.7832 database!';
                        $html_num += 1;
                        $html .= verify_html($title, $message, $icon, $func);
                    } else {
                        $html .= verify_html('R.A. 7832 (Legacy)', 'RA 7832 (legacy is clear)', 'fa-check', 'alert-success');
                    }
                }
            }

            // SEARCH FOR EXISTING ACCOUNT UNDER THE NAME STRING / ID
            // DOUBLE CHECK LASTNAME
            if ($profiletype==0 && is_array($personid)) {
                // @TODO CREATE CHECKING EXISTING TEMP APPLICATION

                $profiletype = 'New Person';
                foreach ($personid as $key => $value) {
                    if ($key == 0) {
                        $ci->db->like('p.' . $key, $value);
                    } else {
                        $ci->db->or_like('p.' . $key, $value);
                    }
                }
                $query = $ci->db->select('cam.servicenumber AS servno')
                    ->from("person AS p")
                    ->join('customer_accounts_owners AS cao', 'cao.ownerid = p.sysid')
                    ->join('customer_accounts_main AS cam', 'cam.sysid = cao.accountid')
                    ->where('cam.servicenumber != ', '')
                    ->get();
                if ($query) {
                    $title = 'Existing Accounts';
                    $icon = 'fa-info';
                    $func = 'alert-info';

                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $row) {
                            $message = $row->servno;
                            //$html_num += 1;
                            $html .= verify_html($title, $message, $icon, $func);
                        }
                    } else {
                        $html .= verify_html('No Existing Accounts(s)', 'No existing account found!', 'fa-search', 'alert-info');
                    }
                } else {
                    $html .= verify_html('No Existing Accounts(s)', 'No existing account found!', 'fa-search', 'alert-info');
                }
            } else {
                // @TODO CREATE CHECKING EXISTING TEMP APPLICATION

                $profiletype = 'Existing Person';
                $query = $ci->db->query("SELECT cam.servicenumber AS servno FROM customer_accounts_owners AS cao
								LEFT JOIN customer_accounts_main AS cam ON cam.sysid = cao.accountid
								LEFT JOIN person AS p ON p.sysid = cao.ownerid
								WHERE cam.ownerid = $personid AND cam.types = 1 AND cam.servicenumber != ''");

                $title = 'Existing Accounts';
                $icon = 'fa-info';
                $func = 'alert-info';

                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $row) {
                        $message = $row->servno;
                        // $html_num += 1;
                        $html .= verify_html($title, $message, $icon, $func);
                    }
                } else {
                    $html .= verify_html('No Existing Accounts(s)', 'No existing account found!', 'fa-search', 'alert-info');
                }
            }
            $q = true;
        }else{
            $q = false;
        }

        $html .= '<span class="stretch"></span>';
        $data['profiletype'] = $profiletype;
        $data['qry'] = $q;
        $data['input'] = $ci->input->post();
        $data['num'] = $html_num;
        $data['html'] = $html;
        return $data;
    }

    function verify_html($title, $message, $icon, $func) {
        return
            '<li class="verification-box">'.
            '<div class="alert '.$func.' fade in ">'.
            '<h4 class="alert-heading">'.$title.'</h4>'.
            '<p>'.$message.'</p>'.
            '<i class="fa '.$icon.' fa-bg"></i>'.
            '</div>'.
            '</li>';
    }

    function verify_html_small($number, $message, $icon, $color) {
        return
            '<div class="dashboard-stat '.$color.'">' .
            '<div class="visual">' .
            '<i class="fa '.$icon.'"></i>' .
            '</div>' .
            '<div class="details" style="padding: 0px 30px;">' .
            '<div class="number">' . $number. '</div>' .
            '<div class="desc">' .
            $message .
            '</div>' .
            ' </div>' .
            '</div>';
    }

    function verify_query($type, $id) {
        $ci = &get_instance();
        if(is_array($id)) {
            // @TODO Need to find out if this algorithm works!
            foreach($id as $key => $value) {
                if($key == 0) {
                    $ci->db->like($key, $value);
                } else {
                    $ci->db->or_like($key, $value);
                }
            }
            $qry_person = $ci->db->select()->from("person AS p")
                ->join('trn_apprehensions AS ta', 'ta.personid = p.sysid AND ta.types = '. $type)
                ->get();
            if($qry_person->num_rows()>0) {
                $return = $qry_person;
            } else {
                $return = false;
            }
        }else{
            $return = $ci->db->query("SELECT * FROM trn_apprehensions AS ta
                LEFT JOIN person AS p ON p.sysid = ta.personid
                WHERE ta.types = $type AND ta.personid = $id");
        }
        return $return;
    }

    function veriry_legacy_ra7832($personid) {
        $ci = &get_instance();
        if(is_array($personid)){
            /*
            foreach($personid as $key => $value) {
                if($key == 0) {
                    $ci->db->like('pname', $value);
                } else {
                    $ci->db->or_like('pname', $value);
                }
            }
            $qry_legacy = $ci->db->select()->from("legacy_ra7832")->get();
            */
            $like_statements = array();

            foreach($personid as $value) {
                $like_statements[] = " pname LIKE '%" . $value . "%' ";
            }
            $like_string = "(" . implode(' OR ', $like_statements) . ")";
            $ci->db->where($like_string);
            $qry_legacy = $ci->db->select()->from("legacy_ra7832")->get();
            //print_r($qry_legacy->result());

        }else {
            $qry_person = $ci->db->query("SELECT * FROM person WHERE sysid = $personid")->row();
            if ($qry_person) {
                $firstname = $qry_person->firstname;
                $lastname = $qry_person->lastname;
                $middlename = $qry_person->firstname;
                $qry_legacy = $ci->db->query("SELECT * FROM legacy_ra7832 WHERE pname LIKE '%$firstname%' OR pname LIKE '%$lastname%' OR pname LIKE '%$firstname%'");
            } else {
                $qry_legacy = false;
            }
        }
        return $qry_legacy;
    }

    if(!function_exists('create_temp_account')) {
        function create_temp_account($ownerid, $apptype) {
            $ci = &get_instance();
            $data = array();
            $acctrate = $ci->input->post('acctrate');
            $accttype = $ci->input->post('statconn');
            $conntype = $ci->input->post('ownertype');
            $loctype = $ci->input->post('loctype');

            $acctreq = $ci->input->post('acctreq');
            $checkcust = $ci->input->post('checkcust');

            // TRANSACTION BEGIN
            $ci->db->trans_begin();

            // CREATE TEMPORARY ACCOUNT
            $account_arr = array(
                'servicenumber' => NULL,
                'createdby' => user_id(),
                'status' => 1
            );
            $ci->db->insert('trn_customer_accounts_main', $account_arr);
            $acct_id = $ci->db->insert_id();

            // CREATE TEMPORARY ACCOUNT OWNER
            $owner_arr = array(
                'accountid' => $acct_id,
                'ownerid' => $ownerid,
                'ownertype' => $apptype,
                'createdby' => user_id(),
                'status' => 1
            );
            $ins_owner = $ci->db->insert('trn_customer_accounts_owners', $owner_arr);
            $onwer_id = $ci->db->insert_id();

            // CREATE TEMPORARY ACCOUNT ADDRESS
            if ($checkcust) {
                // SAME ADDRESS OF AN ACCOUNT TO THE OWNER ADDRESS
                $addrspecific = $ci->input->post('addrspecific');
                $district = $ci->input->post('addrdistrict');
                $city = $ci->input->post('addrcity');
                $country = $ci->input->post('country');

                // THE SAME CONTACT NUMBER
                $phonenumber = $ci->input->post('phone');
                $mobile = $ci->input->post('mobile');
                $email = $ci->input->post('email');

                $acct_addr_arr = array(
                    'acctid' => $onwer_id,
                    'addrtype' => 1,
                    'district' => $district,
                    'city' => $city,
                    'country' => $country,
                    'addrspecific' => $addrspecific,
                    'createdby' => user_id(),
                    'status' => 1
                );
            } else {
                // DIFFERENT ADDRESS OF AN ACCOUNT
                $custaddrcity = $ci->input->post('custaddrcity');
                $custaddrdistrict = $ci->input->post('custaddrdistrict');
                $custcountry = $ci->input->post('custcountry');
                $custaddrspecific = $ci->input->post('custaddrspecific');

                // INSERT DIFFERENT CONTACT
                $custphone = $ci->input->post('custphone');
                $custmobile = $ci->input->post('custmobile');
                $custemail = $ci->input->post('custemail');

                $acct_addr_arr = array(
                    'acctid' => $onwer_id,
                    'addrtype' => 2,
                    'district' => $custaddrdistrict,
                    'city' => $custaddrcity,
                    'country' => $custcountry,
                    'addrspecific' => $custaddrspecific,
                    'createdby' => user_id(),
                    'status' => 1);
            }
            $ci->db->insert('trn_customer_accounts_address', $acct_addr_arr);
            $addr_id = $ci->db->insert_id();

            // CREATE TEMPORARY SUBSCRIPTION
            // explosion for the data
            $rate_array = explode(',', $acctrate);
            $type_array = explode(',', $accttype);
            $conn_array = explode(',', $conntype);
            $loc_array = explode(',', $loctype);

            // count variables
            $rate_array_count = count($rate_array);
            $type_array_count = count($type_array);
            $conn_array_count = count($conn_array);
            $loc_array_count = count($loc_array);

            // SUBSCRIPTION RATE
            if(!empty($acctrate) && $rate_array_count>0) {
                for ($h = 0; $h < $rate_array_count; $h++) {
                    // insert into rates
                    $insert_into_rates = array(
                        'accountid' => $acct_id,
                        'rateid' => $rate_array[$h],
                        'createdby' => user_id(),
                        'status' => 1
                    );
                    $ci->db->insert('trn_customer_accounts_subscription_rates', $insert_into_rates);
                }
            }
            // SUBSCRIPTION TYPE
            if(!empty($accttype) && $type_array_count>0) {
                for ($j = 0; $j < $type_array_count; $j++) {
                    $insert_to_owner_types = array(
                        'ownerid' => $onwer_id,
                        'typeid' => $type_array[$j],
                        'createdby' => user_id(),
                        'status' => 1);
                    $ci->db->insert('trn_customer_accounts_subscription_types', $insert_to_owner_types);
                }
            }
            // CUSTOMER SUBSCRIPTION
            if(!empty($conntype) && $conn_array_count>0) {
                for ($k = 0; $k < $conn_array_count; $k++) {
                    $insert_to_owner_connections = array(
                        'accountid' => $acct_id,
                        'typeid' => $conn_array[$k],
                        'createdby' => user_id(),
                        'status' => 1
                    );
                    $ci->db->insert('customer_accounts_subscription', $insert_to_owner_connections);
                }
            }

            if($ci->db->trans_status()===true){
                $ci->db->trans_commit();
            }else{
                $ci->db->trans_rollback();
            }

            return $acct_id;
        }
    }

    if(!function_exists('create_account_temp_requirements')) {
        function create_account_temp_requirements($accountid, $moduleid)
        {
            $ci = &get_instance();
            $data = array();
            $ins_req_count = 0;
            $req = $ci->input->post('acctreq');
            if ($req != '') {
                $req_arr = explode(',', $req);
                $req_cnt = count($req_arr);
                if ($req_cnt > 0) {
                    for ($i = 0; $i < $req_cnt; $i++) {
                        $insert_req = array(
                            'reqid' => $req_arr[$i],
                            'dataid' => $accountid,
                            'type' => 1,
                            'status' => 0
                        );
                        $ins_req = $ci->db->insert('trn_request_requirements', $insert_req); // no datecreated
                        $req_last_insert_id = $ci->db->insert_id();
                        //Inserting for the history
                        $insert_to_trm_history = array(
                            'dataid' => $accountid,
                            'statusid' => 0,
                            'moduleid' => $moduleid,
                            'createdby' => user_id(),
                            'remarks' => NULL,
                        );
                        $trnreq_id = $ci->model_query->insertnewdata('trn_request_requirements_history', $insert_to_trm_history);
                        if ($ins_req) {
                            $ins_req_count += 1;
                        } else {
                            $ins_req_count += 0;
                        }
                    }
                }
            }
            return $ins_req_count;
        }
    }

    function check_customer_application_balance($dataid) {
        $ci = &get_instance();
        $debit = 0;
        $credit = 0;
        $ledger = 0;
        $trn = 0;
        $qry = false;
        $data = array();

        $query_trn = $ci->db->select()
            ->from('application_customers_charges')
            ->where(array('appid' => $dataid, 'status' => 1))
            ->get();


        if($query_trn->num_rows()>0) {
            foreach($query_trn->result() as $row) {

                $query_ledger_debit = $ci->db->select('SUM(amt) AS AMT')
                    ->from('trn_ledger_customers_applications')
                    ->where(array('clientid' => $dataid, 'status' => 1, 'entrytype' => 68, 'accountid' => $row->acctid))
                    ->get()->row();


                $query_ledger_credit = $ci->db->select('SUM(amt) AS AMT')
                    ->from('trn_ledger_customers_applications')
                    ->where(array('clientid' => $dataid, 'status' => 1, 'entrytype' => 69, 'accountid' => $row->acctid))
                    ->get()->row();

                $amt_debit = ($query_ledger_debit) ? $query_ledger_debit->AMT : 0;
                $amt_credit = ($query_ledger_credit) ? $query_ledger_credit->AMT : 0;

                $trn += $row->amt - $amt_credit;
                $ledger += ($query_ledger_debit) ? $query_ledger_debit->AMT - $amt_credit : 0;

                $amt_debit = ($query_ledger_debit) ? $query_ledger_debit->AMT : 0;
                $amt_credit = ($query_ledger_credit) ? $query_ledger_credit->AMT : 0;

                $debit += $amt_debit;
                $credit += $amt_credit;
            }
        }
        $data['trn'] = $trn;
        $data['ledger'] = $ledger;
        $data['debit'] = $debit;
        $data['credit'] = $credit;
        return (object)$data;
    }
    // start credentials help for hris
    function get_person_credentials($id) {
        $ci = & get_instance();
        $q = $ci->db->select('
            p.sysid,
                cred.passport_num,
                cred.drivers_license,
                cred.driver_license_expiry,
                cred.sss_num,
                cred.tin_num,
                cred.bank_name,
                cred.bank_details,
                cred.other_ids,
                cred.other_ids_id,
                cred.philhealth,
                cred.pagibig
                
                
')
            ->from('prime_employee_main AS e')
            ->join('person p', 'e.personid = p.sysid', 'left')
            ->join('person_credentials cred', 'cred.emp_id = p.sysid', 'left')
            ->where('p.sysid', $id)->get()->row();
        //->group_by('p.sysid')

        if ($q) {
            $data['info'] = (object)array(
                'sysid' => $q->sysid,
                'passport_num' => $q->passport_num,
                'driver' => $q->drivers_license,
                'driver_license_expiry' => $q->driver_license_expiry,
                'sss_num' => $q->sss_num,
                'tin_num' => $q->tin_num,
                'bank_name' => $q->bank_name,
                'bank_details' => $q->bank_details,
                'other_ids' => $q->other_ids,
                'other_ids_id' => $q->other_ids_id,
                'philhealth' => $q->philhealth,
                'pagibig' => $q->pagibig

            );
            $data['qry'] = true;
        }else{
            $data['qry'] = false;
        }
        return (object) $data;
    }




}

// end credentials helper for hris 


if (!function_exists('get_dependents'))
{
    function get_dependents() {
        $ci = & get_instance();
        $query = $ci->db->query( "select empid from prime_employee_dependents
                                          " );
        foreach ($query->result() as $row)
        {
            echo $row->empid;

        }

    }
}

if(!function_exists('insert_application_charges')) {
    function insert_application_charges($chargeid = false, $amt = false, $dataid = false, $moduleid = false, $group = false) {
        $ci = &get_instance();
        $ci->db->trans_begin();
        $data = array();
        $qry = false;
        $err_msg = '';

        if($chargeid == false || $amt == false || $dataid == false || $moduleid == false || $group == false) {
            $id = $ci->input->post('item');
            $amt = $ci->input->post('amt');
            $dataid = $ci->input->post('dataid');
            $moduleid = $ci->input->post('moduleid');
            $group = 3;
        }

        // IF ENTRY IS EXISTS //
        /* $qry_check = $ci->db->select()->from('application_customers_charges')
            ->where(array('group' => 1, 'dataid' => $dataid, 'chargeid' => $chargeid))
            ->get()->row(); */

        $query = $ci->db->select('a.sysid, a.chargeid, c.codes, c.descs, a.vattype')
            ->from('prime_chart_of_accounts AS c')
            ->join('customer_charges AS a', 'a.chargeid = c.sysid', 'left')
            ->where(array('c.sysid' => $chargeid))->get()->row();

        if($query) {
            if ($query->vattype==1) {
                $vat_type = 1;
                $nonvat_amt = bcdiv($amt, 1.12, 2);
                $vat_amt = bcsub($amt, $nonvat_amt, 2);
                $amt = bcadd($nonvat_amt, $vat_amt, 2);
            } else {
                $nonvat_amt = $amt;
                $vat_amt = bcmul($amt, 0.12, 2);
                $amt = $amt;
                $vat_type = 0;
            }

            $ins_arr = array(
                'appid' => $dataid,
                'chargeid' => $chargeid,
                'moduleid' => $moduleid,
                'vatamt' => $vat_amt,
                'amt' => $amt,
                'vattype' => $vat_type,
                'group' => $group,
                'createdby' => user_id(),
            );

            $ci->db->insert('application_customers_charges', $ins_arr);
            $err_msg = $ci->db->_error_message() . ' | ' . $chargeid;
            $data['chargesid'] = $ci->db->insert_id();

            if($ci->db->trans_status()===TRUE) {
                $ci->db->trans_commit();
                $qry = true;
            }else{
                $ci->db->trans_rollback();
            }
        }else{
            $err_msg = 'Account Code not found: ' . $chargeid . ' ' . $err_msg;
        }

        $data['errmsg'] = $err_msg;
        $data['input'] = $ci->input->post();
        $data['params'] = func_get_args();
        $data['qry'] = $qry;
        return (object)$data;
    }
}


if(!function_exists('get_application_details')) {
    function get_application_details($appid) {
        $data = array();
        $ci = &get_instance();
        $qry_details = $ci->db->select("
                CD.personid,
                CD.apptype,
                CD.essrno,
                CD.multid,
                CD.rateclassid,
                CD.addrspec, 
                CD.country, 
                CD.region, 
                CD.province, 
                CD.distid, 
                CD.barangay, 
                CD.city, 
                CD.marital, 
                CD.gender, 
                CD.suffix, 
                CD.prefix, 
                CD.contactmobile, 
                CD.contactphone, 
                CD.contactemail, 
                CD.seniorid, 
                CD.seniordatefrom, 
                CD.seniordateto, 
                CD.contractdate, 
                CD.existaccount, 
                CD.existlegalra, 
                CD.existperson,
                CD.blacklisted,
                CD.tinno,
                CD.sss,
                CD.datecreated, 
                CD.dateupdated, 
                CD.createdby,
                CD.moduleid,
                CD.apptype,
                CD.systemsizeid,
                CD.duid,
                CD.durate,
                CG.dateupdated AS mapupdated,
                CG.updatedby AS mapupdatedby,
                CG.url AS geolink,
                CS.classid,
                CS.connid,
                CS.owntypeid,
                CS.loctypeid,
                p.lastname,
                p.firstname,
                p.middlename,
                p.birthdate
            ")
            ->from('application_customers_details AS CD')
            ->join('person AS p', 'p.sysid = CD.personid', 'left')
            ->join('application_customers_geodata AS CG', 'CG.appid = CD.sysid AND CG.status = 1', 'left')
            ->join('application_customers_subscriptions AS CS', 'CS.appid = CD.sysid AND CS.status = 1', 'left')
            ->where(array('CD.sysid' => $appid))
            ->get()->row();


        $req_comply_cnt = 0;
        $req_res = false;
        $qry_req = $ci->db->select("sysid,reqid, comply, status")
            ->from('application_customers_requirements')
            ->where(array('appid' => $appid, 'status' => 1))
            ->get();
        $qry_req_cnt = $qry_req->num_rows();
        if ($qry_req_cnt > 0) {
            foreach ($qry_req->result() as $rrow) {
                if ($rrow->comply == 1) {
                    $req_comply_cnt += 1;
                }
            }
            $req_res = $qry_req->result();
        }

        $qry_details = (array)$qry_details;
        $qry_details['essrno'] = ($qry_details['essrno'] != 0) ? $qry_details['essrno'] : $appid;
        $data['info'] = (object)$qry_details;
        $data['totalreq'] = $qry_req_cnt;
        $data['totalreqcomp'] = $req_comply_cnt;
        $data['reqres'] = $req_res;

        return (object) $data;

    }
}



function application_info($dataid) {
    $ci = &get_instance();
    $data = array();
    $info = get_application_details($dataid);
    if($info->info) {

        $personexist = ($info->info && $info->info->existperson > 0) ? '<span class="pull-right"><i class="fa fa-star text-warning"></i></span>' : '';

        $person = get_person_info($info->info->personid);

        $appname = ($person->qry) ? $person->info->lastname.', '.$person->info->firstname.' '.$person->info->middlename : '';
        $lastname = ($person->qry) ? $person->info->lastname : '';
        $middlename = ($person->qry) ? $person->info->middlename : '';
        $firstname = ($person->qry) ? $person->info->firstname : '';

        $marital = ($person->qry) ? select_marital($person->info->marital) : '';

        if ($person->qry) {
            $marital_text = '';
            $marital_text .= '<span class="label " style="background: '.$marital->color.' !important; font-size: 14px !important; padding: 2px 2px !important;"><a href="javascript:;" id="input_marital" data-type="select2" data-value="'.$marital->text.'" data-pk="'.$dataid.'" data-original-title="Marital Status" class="editable editable-click" style="display: inline;">';
            $marital_text .= $marital->text;
            $marital_text .= '</a></span>';

            $gender = '';
            $gender .= '<a href="javascript:;" id="gender" data-type="radiolist" data-value="'.$person->info->genderid.'" data-pk="'.$dataid.'" data-original-title="Select Gender" data-url="'.base_url().'cad/submiteditable" class="editable editable-click" style="display: inline;">';
            $gender .= gender($person->info->genderid);
            $gender .= '</a>';
        } else {
            $marital_text = '';
            $gender = '';
        }


        $status = ($person->qry) ? $gender.', '.$person->info->birthdate.' '. $marital_text : '';

        $district = ($info->info) ? $info->info->distid : '';
        $address = ($info->info) ? $info->info->addrspec : '';

        $datecreated = ($info->info) ? $info->info->datecreated : '';
        $landmark = ($info->info) ? $info->info->addrspec : '';
        $mapupdated = ($info->info) ? $info->info->mapupdated : '';
        $maplink = ($info->info->geolink) ? $info->info->geolink : 'javascript:;';
        $tinno = ($info->info) ? $info->info->tinno : '';
        $distname = ($info->info) ? get_district_name($info->info->distid) : 'N/A';
        $citymun = ($info->info) ? get_address_name_new($info->info->city, 'city') : 'N/A';
        $province = ($info->info) ? get_address_name_new($info->info->province, 'province') : 'N/A';
        $distid = ($info->info) ? $info->info->distid : 0;
        $brgy = ($info->info) ? $info->info->barangay : 0;
        //$gdlb = ($info->info) ? get_gdlb_name($info->info->gdlbid) : 'N/A';
        $durate = ($info->info) ? $info->info->durate : 0;
        $duid = ($info->info) ? $info->info->duid : '';
        $multcode = ($info->info) ? get_district_name($info->info->multid) : 'N/A';
        $multid = ($info->info) ? $info->info->multid : 0;
        $essrno = ($info->info) ? $info->info->essrno : 0;
        $phone = ($info->info) ? $info->info->contactphone : 'N/A';
        $mobile = ($info->info) ? $info->info->contactmobile : 'N/A';
        $email = ($info->info) ? $info->info->contactemail : 'N/A';
        $moduleid = ($info->info) ? $info->info->moduleid : 0;
        $apptype = ($info->info) ? $info->info->apptype : 0;

        $seniorid = ($info->info) ? $info->info->seniorid : 0;
        $seniordatefrom = ($info->info) ? $info->info->seniordatefrom : 0;
        $seniordateto = ($info->info) ? $info->info->seniordateto : 0;

        if ($info->info) {
            $get_map_updatername = get_users_info($info->info->mapupdatedby);
            if ($get_map_updatername) {
                $mapupdatedby = $get_map_updatername->username;
            } else {
                $mapupdatedby = '';
            }
        } else {
            $mapupdatedby = '';
        }
        $rateclass = ($info->info && $info->info->classid > 0) ? json_decode(get_rate_class_select($info->info->classid)) : '';
        $rateclassid = ($info->info) ? $info->info->classid : 0;
        $conntype = ($info->info) ? get_acct_type($info->info->connid) : '';
        $ownertype = ($info->info) ? get_acct_type($info->info->owntypeid) : '';
        $landtype = ($info->info) ? get_acct_type($info->info->loctypeid) : '';

        $requirements = ($info) ? $info->totalreqcomp . '/' . $info->totalreq : '';

        $reqres = $info->reqres;

        $check_exemptions = $ci->db->select()->from('application_customers_exemptions')
            ->where(array('appid' => $dataid, 'status' => 1))
            ->get()->row();

        $total_load_qry = acct_total_loads($dataid);

        if($info->info->systemsizeid) {
            $sql_system_size = $ci->db->query("SELECT * FROM customer_system_size WHERE sysid = {$info->info->systemsizeid} AND status = 1")->row();
            $system_size_name = ($sql_system_size) ? $sql_system_size->descs . ' <span class="badge badge-success" style="padding: 2px 5px !important; width: auto!important;"><i class="fa fa-check"></i> Saved</span>' : '';
            $system_size_name_raw = $sql_system_size->descs;
        }else{
            $get_system_size = get_system_size_range($dataid);

            if ($get_system_size) {
                $system_size_name = $get_system_size->descs . ' <span class="badge badge-danger" style="padding: 2px 5px !important; width: auto!important;">RECOMMENDED</span>';
                $system_size_name_raw = $get_system_size->descs;
            } else {
                $published_qry = $this->db->select('power')
                    ->from('application_customers_system_size')
                    ->where(array('appid' => $dataid,'status' => 305))
                    ->get()->row();

                if ($published_qry) {
                    $power = (($published_qry->power/1000) > 1) ? $published_qry->power/1000 : $published_qry->power;
                    $unit = (($published_qry->power/1000) > 1) ? 'kWp' : 'Wp';
                    $system_size_name = $power.$unit.' Grid-Tied <span class="badge badge-primary" style="padding: 2px 5px !important; width: auto!important;"><i class="fa fa-check"></i> Non-Standard</span>';
                    $system_size_name_raw = $power.$unit.' Grid-Tied';
                } else {
                    $system_size_name = false;
                    $system_size_name_raw = false;
                }
            }
        }


        $data['personid'] = $info->info->personid;
        $data['seniorid'] = $seniorid;
        $data['seniordatefrom'] = $seniordatefrom;
        $data['seniordateto'] = $seniordateto;
        $data['essrno'] = $essrno;
        $data['origin'] = 35;
        //$data['gdlb'] = $gdlb;
        $data['durate'] = $durate;
        $data['multcode'] = $multcode;
        $data['multid'] = $multid;
        $data['duid'] = $duid;
        $data['personexist'] = $personexist;
        $data['rateclass'] = $rateclass;
        $data['rateclassid'] = $rateclassid;
        $data['appname'] = $appname;
        $data['lastname'] = $lastname;
        $data['middlename'] = $middlename;
        $data['firstname'] = $firstname;
        $data['address'] = $address;
        $data['citymun'] = $citymun;
        $data['province'] = $province;
        $data['district'] = $district;
        $data['status'] = $status;
        $data['tinno'] = $tinno;
        $data['distname'] = $distname;
        $data['distid'] = $distid;
        $data['barangay'] = $brgy;
        $data['datecreated'] = $datecreated;
        $data['landmark'] = $landmark;
        $data['mapupdated'] = $mapupdated;
        $data['mapupdatedby'] = $mapupdatedby;
        $data['maplink'] = $maplink;
        $data['conntype'] = $conntype;
        $data['ownertype'] = $ownertype;
        $data['landtype'] = $landtype;
        $data['requirements'] = $requirements;
        $data['reqres'] = $reqres;
        $data['exempt'] = $check_exemptions;
        $data['totalload'] = ($total_load_qry) ? $total_load_qry : 0;
        $data['phone'] = $phone;
        $data['mobile'] = $mobile;
        $data['email'] = $email;
        $data['moduleid'] = $moduleid;
        $data['apptype'] = $apptype;
        $data['systemsizeid'] = $info->info->systemsizeid;
        $data['systemsize'] = $system_size_name;
        $data['systemsizename'] = $system_size_name_raw;
        $data['q'] = true;
    }else{
        $data['q'] = false;
    }

    return (object) $data;
}


if(!function_exists('get_ar_billing')) {
    function get_ar_billing($acctid, $ar_year) {
        $ci = &get_instance();
        $qry_billtrn = false;

        $check_bill_row = $ci->db->select('sysid')
            ->from('billing_reports')
            ->where(array('acctid' => $acctid))
            ->get()->row();

        if($check_bill_row) {
            $qry_billtrn = $ci->db->select('kwhuse, duedate, schedid, byr, totalvat, current')
                ->from('billing_reports_main')
                ->where(array('acctid' => $acctid, 'year' => $ar_year, 'kwhuse >' => 0))
                //->group_by('kwhuse, duedate, schedid')
                ->get();
            if ($qry_billtrn->num_rows() > 0) {
                return $qry_billtrn;
            } else {
                $ar_year = $ar_year - 1;
                $qry_billtrn = get_ar_billing($acctid, $ar_year);
                if ($qry_billtrn->num_rows() > 0) {
                    return $qry_billtrn;
                }
            }
        }else{
            return false;
        }
    }
}

if(!function_exists('get_ar_billing_details')) {
    function get_ar_billing_details($acctid, $mtr, $year, $month) {
        $ci = &get_instance();
        $qry = $ci->db->select('totalvat, current')
            ->from('billing_reports_main')
            ->where(
                array(
                    'acctid' => $acctid,
                    'mtr' => $mtr,
                    'year' => $year,
                    'month' => $month
                )
            )
            ->get()->row();
        return ($qry) ? $qry : false;
    }
}

if(!function_exists('check_ar_billing')) {
    function check_ar_billing($acctid, $mtr, $year, $month) {
        $ci = &get_instance();
        $qry = $ci->db->select('acctid')
            ->from('billing_reports_main')
            ->where(
                array(
                    'acctid' => $acctid,
                    'mtr' => $mtr,
                    'year' => $year,
                    'month' => $month
                )
            )
            ->get()->row();
        return ($qry) ? $qry : false;
    }
}
if(!function_exists('get_billing_refcode')) {
    function get_billing_refcode($billid) {
        $ci = &get_instance();
        $code = '';
        $qry_ref_code = $ci->db->select('tp.sysid, tp.names, tp.desc')
            ->from('billing_reports_tagging_trn AS rtt')
            ->join('prime_types_parameter AS tp', 'tp.sysid = rtt.codeid', 'left')
            ->where(array('rtt.billid' => $billid, 'rtt.status' => 300))
            ->get()->row();
        if($qry_ref_code) {
            $code = get_types_label_format($qry_ref_code->sysid, $qry_ref_code->names, false, 'left');
        }
        return $code;
    }
}



if(!function_exists('mrd_check_hno')) {
    function mrd_check_hno($acctid, $mtrno)
    {
        $data = array();
        $ci = &get_instance();
        $q = false;
        $qry = $ci->db->select('CAST(datecreated AS date) AS hnodate')
            ->from('trn_reading_findings')
            ->where(array('findingid' => 3, 'acctid' => $acctid, 'mtrno' => $mtrno))
            ->get()->row();
        if($qry) {
            $q = true;
            $hno_date = new DateTime($qry->hnodate);
            $today = new DateTime(date('Y-m-d'));

            $diff = $today->diff($hno_date)->format("%a");
            if($diff>1) {
                $hno_num =  $diff . ' days';
                if($diff>=60) {
                    $hno_num_class = 'label-danger';
                }else {
                    $hno_num_class = 'label-info';
                }
            } else {
                $hno_num = 'New';
                $hno_num_class = 'label-success';
            }
            $data['ref'] = '<span class="label '.$hno_num_class.'">'.$hno_num.'</span> HNO';
            $data['hnodays'] = $diff;
            $data['hnodesc'] = 'House not occupied since ' . $qry->hnodate . ', ' .$diff.' days.';
        }
        $data['qry'] = $q;
        return (object) $data;
    }
}

if(!function_exists('mrd_get_last_findings')) {
    function mrd_get_last_findings($acctid, $mtrno)
    {
        $data = array();
        $ci = &get_instance();
        $q = false;
        $qry = $ci->db->query("
                    SELECT mrf.codes, mrf.descriptions AS descs, rf.datecreated
                    FROM trn_reading_findings AS rf
                    JOIN meter_reading_findings AS mrf ON mrf.sysid = rf.findingid
                    WHERE rf.acctid = $acctid AND rf.mtrno = $mtrno
                    ORDER BY rf.datecreated desc
              ")->row();
        if($qry) {
            $q = true;
            $data['code'] = $qry->codes;
            $data['desc'] = $qry->descs;
            $data['date'] = $qry->datecreated;
        }
        $data['qry'] = $q;
        return (object) $data;
    }
}


if(!function_exists('get_meter_info')) {

    function get_meter_info($sysid)
    {
        $ci = &get_instance();
        $results = array();
        $qry = false;
        $row = $ci->db->query("
            SELECT * FROM assets_main WHERE sysid = $sysid
        ")->row();
        if ($row) {
            $qry = true;
            $status = '';
            $owner_name = '';
            $status_arr = check_asset_status($row->sysid);
            $status = get_types_label_format($status_arr->status_id, false, false, false, false, false, true)->text;


            $type = 'N/A';
            $volts = 'N/A';
            $amps = 'N/A';
            $ercseal = 'N/A';
            $pecoseal = 'N/A';
            $kh = 'N/A';
            $reading = 'N/A';
            $wiresize = 'N/A';


            if ($status_arr->status_available == 1 || $sysid) {

                $get_asset_specs = $ci->db->select()
                    ->from('assets_main_specifications_matrix')
                    ->where(array('assetid' => $row->sysid, 'status' => 1))
                    ->get();
                if ($get_asset_specs->num_rows() > 0) {
                    foreach ($get_asset_specs->result() as $srow) {
                        if ($srow->specid == 3098) {
                            $type = $srow->specval;
                        }
                        if ($srow->specid == 3097) {
                            $volts = $srow->specval;
                        }
                        if ($srow->specid == 3096) {
                            $amps = $srow->specval;
                        }
                        if ($srow->specid == 3095) {
                            $pecoseal = $srow->specval;
                        }
                        if ($srow->specid == 3094) {
                            $ercseal = $srow->specval;
                        }
                        if ($srow->specid == 3208) {
                            $kh = $srow->specval;
                        }
                        if ($srow->specid == 3206) {
                            $reading = $srow->specval;
                        }
                        if ($srow->specid == 3207) {
                            $wiresize = $srow->specval;
                        }
                    }
                }

                if ($row->brand > 0) {
                    $qry_brand = $ci->db->select()
                        ->from('prime_brands')
                        ->where(array('sysid' => $row->brand))
                        ->get()->row();
                    if ($qry_brand) {
                        $brand = $qry_brand->codes;
                    } else {
                        $brand = 'N/A';
                    }
                } else {
                    $brand = 'N/A';
                }


                $pic = base_url('assets/global/img/person_default.jpg');


                $results = array(
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
                );
            }
        }

        $results['qry'] = $qry;
        return (object)$results;
    }
}

if(!function_exists('get_joborder_info')) {
    function get_joborder_info($dataid) {
        $ci = &get_instance();
        $qry = $ci->db->query("
          SELECT
            jdl.acctid,
            am.servicenumber,
            aa.addrspecific,
            am.datecontract,
            am.dateconnected,
            am.ownerid,
            am.types,
            am.gdlb,
            am.mtrno,
            am.mtrserial,
            am.mtr,
            am.rateclassid,
            am.multid,
            am.`status` AS acctstats,
            CONCAT(gdlb.g,'-',ad.codes,'-',gdlb.l,'-',gdlb.b) AS gdlbcode,
            jdl.repsource,
            jdl.complainants,
            jdl.datecreated,
            jdl.dateupdated,
            jdl.createdby,
            jdl.updatedby,
            jdl.tickettype,
            jdl.`status`,
            ptp.`desc`,
            ad.sysid AS distid,
            ad.descriptions AS distname
            FROM
            joborders_details_logs AS jdl
            INNER JOIN customer_accounts_main AS am ON jdl.acctid = am.sysid
            INNER JOIN customer_accounts_address AS aa ON jdl.acctid = aa.acctid
            INNER JOIN gdlb_main AS gdlb ON am.gdlb = gdlb.sysid AND am.gdlb = gdlb.sysid
            INNER JOIN prime_types_parameter AS ptp ON ptp.sysid = jdl.`status`
            INNER JOIN address_districts AS ad ON gdlb.d = ad.sysid
            WHERE
        jdl.sysid = $dataid")->row();

        return $qry;
    }
}


if(!function_exists('customer_application_basicinfo')) {
    function customer_application_basicinfo($dataid, $editable = false, $showrate = true) {
        $data = array();
        $ci = &get_instance();
        $data['dataid'] = $dataid;
        $data['editable'] = $editable;
        $data['showrate'] = $showrate;
        $ci->load->view('admin/pages/customer/appinfo', $data);
    }
}


if(!function_exists('customer_application_requirements_list')) {
    function customer_application_requirements_list($dataid, $editable = false) {
        $data = array();
        $ci = &get_instance();
        $data['dataid'] = $dataid;
        $data['editable'] = $editable;
        $ci->load->view('admin/pages/customer/appreqlist', $data);
    }
}

if(!function_exists('customer_application_charges_list')) {
    function customer_application_charges_list($dataid, $editable = false) {
        $data = array();
        $ci = &get_instance();
        $data['dataid'] = $dataid;
        $data['editable'] = $editable;
        $ci->load->view('admin/pages/customer/appcharges', $data);
    }
}


if(!function_exists('customer_application_view_right')) {
    function customer_application_view_right($dataid, $editable = false, $showrate = true) {
        $data = array();
        $ci = &get_instance();
        $data['dataid'] = $dataid;
        $ci->load->view('admin/pages/customer/appviewright', $data);
    }
}
if(!function_exists('customer_application_inspection_list')) {
    function customer_application_inspection_list($dataid, $editable = false) {
        $data = array();
        $ci = &get_instance();
        $data['dataid'] = $dataid;
        $ci->load->view('admin/pages/customer/appinspection', $data);
    }
}



if(!function_exists('crm_gallery')) {
    function crm_gallery($dataid, $editable = false) {
        $data = array();
        $ci = &get_instance();
        $data['dataid'] = $dataid;
        $data['editable'] = $editable;
        $ci->load->view('admin/pages/customer/crm_gallery', $data);
    }
}

if(!function_exists('crm_transaction')) {
    function crm_transaction($dataid, $editable = false) {
        $data = array();
        $ci = &get_instance();
        $data['dataid'] = $dataid;
        $data['editable'] = $editable;
        $ci->load->view('admin/pages/customer/crm_transaction', $data);
    }
}


if(!function_exists('get_system_size_range')) {
    function get_system_size_range($appid) {
        $ci = &get_instance();

        // GET THE APPLICATION
        $q = $ci->db->select('e.sysid, ep.codes, e.watts, e.qty, e.equipid, ep.codes, ep.descs, e.remarks, ep.types')
            ->from("application_customers_equipments e")
            ->join('prime_equipments_parameters AS ep', 'ep.sysid = e.equipid', 'left')
            ->join('application_customers_system_size AS log', 'log.sysid = e.logid')
            ->where(array('e.appid' => $appid, 'e.status' => 1, 'log.status !=' => 0))
            ->get();

        $total_qty = 0;
        $total_wtt = 0;
        $total_amp = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $total_qty += $row->qty;
                if($row->types == 'a') {
                    $total_wtt += $row->watts;
                }
                if($row->types == 'v') {
                    $total_amp += $row->watts;
                }
            }
        }

        $total_voltage = ($total_wtt * $total_amp);

        $sql = $ci->db->query("SELECT * FROM customer_system_size WHERE
                                $total_voltage BETWEEN amtmin AND amtmax")->row();
        $sql->voltage = $total_voltage;
        return ($sql) ? $sql : false;
    }
}

if(!function_exists('get_dist_utility_list')) {
    function get_dist_utility_list($id = false) {
        $ci = &get_instance();
        $data = array();
        if ($id) {
            $ci->db->where('sysid',$id);
        }

        $qry = $ci->db->select()
            ->from('distribution_utilities_main')
            ->where('status',1)->get();

        if ($id) {
            $data = $qry->row();
        } else {
            if ($qry->num_rows() > 0) {
                $data = $qry->result();
            }
        }

        return $data;
    }
}


