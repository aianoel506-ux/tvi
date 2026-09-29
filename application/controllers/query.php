<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Query extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_inspection_queries');
        $this->load->model('model_query', 'query');
        $this->load->model('model_admin');
        $this->load->model('model_legal');
        $this->load->model('model_cad');
        $this->load->library('datatables');
        $this->load->model('model_bos');



        require_once APPPATH.'third_party/PHPExcel.php';
        $this->excel = new PHPExcel();
    }
    function tests() {

        $conn = $this->load->database('audit', TRUE);
        $sql = $conn->select()->from('erp_transactions_trails')->get();
        echo '<pre>';
        print_r($sql->result());
    }

    function requestprocessupdate($trnid) {
        $this->db->where(array('sysid' => $trnid));
        $query = $this->db->update('transaction_request_main', array('status' => 1));
        return ($query) ? true : false;
    }

    function getselect2chartofaccounts() {
        $data = array();
        if(get_select_chart_of_accounts(1)) {
            foreach(get_select_chart_of_accounts(1) as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes . ' - '. $row->descs
                );
            }
        }
        echo json_encode($data);
    }

    function requestprocess() {
        echo $this->query->process_request();
    }

    function requestprocessdirect() {
        echo $this->query->process_request_direct();
    }

    function inspection_search_account($moduleid) {
        echo $this->model_inspection_queries->get_hashcode($moduleid)->hashcode;
    }

    function userassignroles() {
        echo $this->query->user_assign_roles();
    }

    function userassignstatus() {
        echo $this->query->get_user_assign_status();
    }

    function checkusername() {
        $data = array();
        $i = $this->input->post('username');
        $q = $this->query->get_user_data($i, 'username');
        if ($q) {
            $data['q'] = true;
            $data['m'] = 'Username is already exist!';
        } else {
            $data['q'] = false;
        }
        echo json_encode($data);
    }

    function adduser() {
        echo $this->query->add_user();
    }

    //INSPECTION START
    function insertInspectionData() {
        $rate_class_id = $this->input->post('rate_class');
        $inspection_date = $this->input->post('inspection_date');
        $account_type = $this->input->post('account_type');
        $district = $this->input->post('district');
        $city = $this->input->post('city');
        $specific_address = $this->input->post('specific_address');
        /*
          $code = $this->input->post('equipment_code');
          $power = $this->input->post('equipment_wattage');
          $qty = $this->input->post('equipment_qty');
         */
        $x = $this->input->post('latitude');
        $y = $this->input->post('longitude');

        $accountid = $this->input->post('dataid');
        $trn = $this->input->post('trn');
        $status = $this->input->post('status');
        $page = $this->input->post('page');

        $data = array(
            'rateClass' => $rate_class_id,
            'inspDate' => $inspection_date,
            'accountType' => $account_type,
            'district' => $district,
            'city' => $city,
            'specificAddress' => $specific_address,
            'longitude' => $y,
            'latitude' => $x,
            'accountID' => $accountid,
            'trn' => $trn,
            'status' => $status,
            'page' => $page
        );

        if (!empty($rate_class_id) && !empty($inspection_date) && !empty($account_type) && !empty($district) && !empty($city) && !empty($specific_address)/* || !empty($x) || !empty($y) */) {
            $query_result = $this->model_inspection_queries->add_equipment_data($data);
            if ($query_result) {
                $this->session->set_flashdata('accountid', $accountid);
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('trn', $trn);
                $hash = "0c985ebf7e527bb1c7e3930824af352e"; //$this->model_inspection_queries->get_hashcode($this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid)->hashcode;
                redirect(base_url() . "module/" . $hash . "/data/1");
            } else {
                $this->session->set_flashdata('accountid', $accountid);
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('trn', $trn);
                echo "This is, ironically, a blank page. Sorry. Query encountered some problems, it returns: " . $query_result;
            }
        } else {
            $rate_name = $this->model_inspection_queries->get_rate_class_name($rate_class_id)->name;
            $this->session->set_flashdata('rate_class_id', $rate_name);
            $this->session->set_flashdata('rate_class_name', $rate_class_id);

            $this->session->set_flashdata('inspection_date', $inspection_date);

            $account_type_name = $this->model_inspection_queries->get_account_type_name($account_type)->names;
            $this->session->set_flashdata('account_type_id', $account_type);
            $this->session->set_flashdata('account_type_name', $account_type_name);

            $district_name = $this->model_inspection_queries->get_district_name($district)->names;
            $this->session->set_flashdata('district_id', $district);
            $this->session->set_flashdata('district_name', $district_name);

            $city_name = $this->model_inspection_queries->get_city_name($city)->names;
            $this->session->set_flashdata('city_id', $city);
            $this->session->set_flashdata('city_name', $city_name);

            $this->session->set_flashdata('specific_address', $specific_address);
            /*
              $this->session->set_flashdata('code', $code);
              $this->session->set_flashdata('power', $power);
              $this->session->set_flashdata('qty', $qty);
             * 
             */
            $this->session->set_flashdata('x', $x);
            $this->session->set_flashdata('y', $y);


            $this->session->set_flashdata('accountid', $accountid);
            $this->session->set_flashdata('status', $status);
            $this->session->set_flashdata('trn', $trn);


            $hash = "0c985ebf7e527bb1c7e3930824af352e"; //$this->model_inspection_queries->get_hashcode($this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid)->hashcode;
            redirect(base_url() . "module/" . $hash . "/$page");
        }
    }

    function ajaxInputSave() {
        $inputName = $this->input->post('formName');
        $newValue = $this->input->post('inputValue');
        $dataid = $this->input->post('dataid');
        $data = $this->model_inspection_queries->edit_data($dataid);

        $array = array(
            'newValue' => $newValue,
            'identifier' => ''
        );
        $query = 0;
        if ($inputName == 'city') {
            $array['identifier'] = $data->address_id;
            $query = $this->model_inspection_queries->updateCity($array);
        } else if ($inputName == 'inspection_date') {
            $array['identifier'] = $data->address_id;
            $query = $this->model_inspection_queries->updateInspectionDate($array);
        } else if ($inputName == 'district') {
            $array['identifier'] = $dataid;
            $query = $this->model_inspection_queries->updateDistrict($array);
        } else {
            $array['identifier'] = $data->address_id;
            $query = $this->model_inspection_queries->updateSpecificAddres($array);
        }
        $arrayReturn = array(
            'query' => $query
        );
        echo json_encode($arrayReturn);
    }

    function updateInspectionData() {
        $rate_class_id = $this->input->post('rate_class');
        $inspection_date = $this->input->post('inspection_date');
        $account_type = $this->input->post('account_type');
        $district = $this->input->post('district');
        $city = $this->input->post('city');
        $specific_address = $this->input->post('specific_address');
        /*
          $code = $this->input->post('equipment_code');
          $power = $this->input->post('equipment_wattage');
          $qty = $this->input->post('equipment_qty');
         */
        $x = $this->input->post('latitude');
        $y = $this->input->post('longitude');

        $accountid = $this->input->post('dataid');
        $trn = $this->input->post('trn');
        $status = $this->input->post('status');
        $page = $this->input->post('page');
        $address_id = $this->input->post('address_id');

        $data = array(
            'rateClass' => $rate_class_id,
            'inspDate' => $inspection_date,
            'accountType' => $account_type,
            'district' => $district,
            'city' => $city,
            'specificAddress' => $specific_address,
            'longitude' => $y,
            'latitude' => $x,
            'accountID' => $accountid,
            'trn' => $trn,
            'status' => $status,
            'page' => $page,
            'addressID' => $address_id,
        );

        if (!empty($rate_class_id) && !empty($inspection_date) && !empty($account_type) && !empty($district) && !empty($city) && !empty($specific_address)/* || !empty($x) || !empty($y) */) {
            $query_result = $this->model_inspection_queries->update_equipment_data($data);
            if ($query_result) {
                $this->session->set_flashdata('accountid', $accountid);
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('trn', $trn);
                $hash = "0c985ebf7e527bb1c7e3930824af352e"; //$this->model_inspection_queries->get_hashcode($this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid)->hashcode;
                redirect(base_url() . "module/" . $hash . "/data/1");
            } else {
                $this->session->set_flashdata('accountid', $accountid);
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('trn', $trn);
                echo "This is, ironically, a blank page. Sorry. Query encountered some problems, it returns: " . $query_result;
            }
        } else {
            $rate_name = $this->model_inspection_queries->get_rate_class_name($rate_class_id)->name;
            $this->session->set_flashdata('rate_class_id', $rate_name);
            $this->session->set_flashdata('rate_class_name', $rate_class_id);

            $this->session->set_flashdata('inspection_date', $inspection_date);

            $account_type_name = $this->model_inspection_queries->get_account_type_name($account_type)->names;
            $this->session->set_flashdata('account_type_id', $account_type);
            $this->session->set_flashdata('account_type_name', $account_type_name);

            $district_name = $this->model_inspection_queries->get_district_name($district)->names;
            $this->session->set_flashdata('district_id', $district);
            $this->session->set_flashdata('district_name', $district_name);

            $city_name = $this->model_inspection_queries->get_city_name($city)->names;
            $this->session->set_flashdata('city_id', $city);
            $this->session->set_flashdata('city_name', $city_name);

            $this->session->set_flashdata('specific_address', $specific_address);
            /*
              $this->session->set_flashdata('code', $code);
              $this->session->set_flashdata('power', $power);
              $this->session->set_flashdata('qty', $qty);
             * 
             */
            $this->session->set_flashdata('x', $x);
            $this->session->set_flashdata('y', $y);


            $this->session->set_flashdata('accountid', $accountid);
            $this->session->set_flashdata('status', $status);
            $this->session->set_flashdata('trn', $trn);


            $hash = "0c985ebf7e527bb1c7e3930824af352e"; //$this->model_inspection_queries->get_hashcode($this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid)->hashcode;
            redirect(base_url() . "module/" . $hash . "/$page");
        }
    }

    function editSubmittedData() {
        $accountid = $this->input->post('dataid');
        $trn = $this->input->post('trn');
        $status = $this->input->post('status');

        $this->session->set_flashdata('accountid', $accountid);
        $this->session->set_flashdata('status', $status);
        $this->session->set_flashdata('trn', $trn);

        $hash = "0c985ebf7e527bb1c7e3930824af352e"; //$this->model_inspection_queries->get_hashcode($this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid)->hashcode;
        redirect(base_url() . "module/" . $hash . "/edit");
    }

    function changeEquipmentStatus() {
        $sysid = $this->input->post('sysid');

        $query = $this->model_inspection_queries->change_equipment_status($sysid);

        $data = array(
            "result" => $query
        );
        echo json_encode($data);
    }

    //INSPECTION END
    function testing() {
        $sql = $this->db->query("SELECT * FROM system_users_conversation_matrix WHERE userid = 2");
        echo '<pre>';
        print_r($sql->result());
    }

    // ###########################################################################################
    // FUNCTIONS FOR APPLICATION NEW ACCOUNT
    // ###########################################################################################

    public function statreq() {
        if (isset(user_session()->system_user_sessid)) {
            $id = $this->input->post('id');
            $update = update_stats('trn_request_requirements', $id);

            // dri ma insert para sa history ang remarks
            // variables
            $moduleid = 2;
            $created_by = user_session()->system_user_sessid;

            if ($update->upd) {
                $data['qry'] = true;
                if ($update->ret == 1) {
                    $remarks = "Requirements comply";
                    $data['qry'] = true;
                    $data['msg'] = 'Requirements comply!';
                    $insert_to_trm_history = array('dataid' => $id, 'statusid' => 1, 'moduleid' => $moduleid, 'createdby' => $created_by, 'remarks' => $remarks);
                    $this->db->insert('trn_request_requirements_history', $insert_to_trm_history);
                } else {
                    $remarks = "Requirements uncomply";
                    $data['qry'] = false;
                    $data['msg'] = 'Requirements uncomply!';
                    $insert_to_trm_history = array('dataid' => $id, 'statusid' => 0, 'moduleid' => $moduleid, 'createdby' => $created_by, 'remarks' => $remarks);
                    $this->db->insert('trn_request_requirements_history', $insert_to_trm_history);
                }
            } else {
                $data['qry'] = false;
                $data['msg'] = 'Requirements uncomply!';
            }
        } else {
            $data['qry'] = false;
            $data['msg'] = 'Session Time-out!';
        }
        echo json_encode($data);
    }

    // ========================================================================
    // ====== ASSET MODULE ====================================================
    // ========================================================================
    public function new_asset_entry() {
        echo $this->query->insert_trn_entry();
    }

    // CHECK ACCOUNT / VERIFICATION QUERY //
    function accountverifications() {
        echo json_encode(account_verifications());
    }

    function legaltest() {
        //echo '<pre>';

        $personid = array(
            'lastname' => 'VARONA'
        );
        $like_statements = array();

        foreach($personid as $value) {
            $like_statements[] = " pname LIKE '%" . $value . "%' ";
        }
        $like_string = "(" . implode(' OR ', $like_statements) . ")";
        $this->db->where($like_string);
        $qry_legacy = $this->db->select()->from("legacy_ra7832")->get();
        echo $qry_legacy->num_rows();
        print_r($qry_legacy->result());
    }

    // CUSTOMER INFO
    function getcrmsumstats() {
        $data = array();
        $loc_query = $this->db->select("d.names AS TITLE, COUNT(acct.servicenumber) AS CNT")
            ->from('customer_accounts_main AS acct', 'acct.sysid = own.accountid')
            ->join('customer_accounts_glb AS ag', 'ag.accountid = acct.sysid')
            ->join('gdlb_main AS gm', 'gm.sysid = ag.gdlbid')
            ->join('address_districts AS d', 'd.sysid = gm.d')
            ->where(array('acct.servicenumber != ' => 'null'))
            ->group_by('d.names')
            ->get();
        if ($loc_query->num_rows() > 0) {
            foreach ($loc_query->result() as $row) {
                $data['dist'][] = array(
                    'TITLE' => $row->TITLE,
                    'CNT' => $row->CNT,
                );
            }
        }
        $data['title'] = 'District Stats';
        echo json_encode($data);
    }




    function getcustomersinmap() {

        $dist = $this->input->post('dist');
        $class = $this->input->post('class');
        $specs = $this->input->post('specs');
        $types = $this->input->post('display');
        $zoom = 14;

        $chart_arr = array();
        $chart_title = '';

        if ($dist == false && $class == false && $specs == false) {
            $chart_qry = $this->db->select("d.names AS TITLE, COUNT(acct.servicenumber) AS CNT")
                ->from('customer_accounts_main AS acct')
                ->join('gdlb_main AS gm', 'gm.sysid = acct.gdlb')
                ->join('address_districts AS d', 'd.sysid = gm.d')
                ->group_by('d.names')
                ->get();
            if ($chart_qry->num_rows() > 0) {

                foreach ($chart_qry->result() as $row) {
                    $chart_arr[] = array(
                        'TITLE' => $row->TITLE,
                        'CNT' => $row->CNT,
                    );
                }
            }
            $chart_title = 'All District Stats';

        }else{

            if ($dist) {
                $this->db->where('gm.d', $dist);
            }
            /*
            if ($class) {
                $this->db->where('cg.rateid', $class);
            }
            */
            $chart_qry = $this->db->select("d.names AS TITLE, COUNT(acct.servicenumber) AS CNT")
                ->from('customer_accounts_main AS acct')
                ->join('gdlb_main AS gm', 'gm.sysid = acct.gdlb')
                ->join('address_districts AS d', 'd.sysid = gm.d')
                ->join('rate_class_group AS cg', 'cg.rateid = acct.rateclassid')
                ->group_by('d.names')
                ->get();
            if ($chart_qry->num_rows() > 0) {
                foreach ($chart_qry->result() as $row) {
                    $chart_arr[] = array(
                        'TITLE' => $row->TITLE,
                        'CNT' => $row->CNT,
                    );
                }
            }

            if ($dist) {
                $qry_dist = $this->db->select('names')->from('address_districts')->where('sysid', $dist)->get()->row();
                $dist_title = ($qry_dist) ? $qry_dist->names : 'Unknown';
                $chart_title = $dist_title;
            }

            if ($class) {
                $qry_class = $this->db->select('classifications')->from('prime_system_rate_class_main')->where('sysid', $class)->get()->row();
                $class_title = ($qry_class) ? $qry_class->classifications : 'Unknown';
                $chart_title = $class_title;
            }
            if($class && $dist) {
                $chart_title = $dist_title . ' / ' . $class_title;
            }

        }

        // QUERY FOR MAPS
        if($dist > 0 || $class > 0 || $specs > 0 || $types > 0) {
            if ($dist) {
                $this->db->where('gm.d', $dist);
            }
            if ($class) {
                $this->db->where('cg.rateid', $class);
            }
            if ($specs) {
                $this->db->like('acct.servicenumber', $specs);
            }
            if ($types > 0) {
                $this->db->where('geo.type', $types);
            } else {
                $this->db->where('geo.type', 2);
            }
        }else{
            $this->db->limit(10);
            $this->db->where('gm.d', 5);
        }
        $loc_query = $this->db->select('acct.sysid AS acctid, geo.lat, geo.lon, addr.addrspecific AS spec, acct.servicenumber AS servno, acct.types, acct.ownerid, acct.mtrno, seq.mrseq'
        )
            ->select("CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb", false)
            ->from('customer_accounts_main AS acct')
            ->join('customer_accounts_geodata AS geo', 'acct.sysid = geo.acctid AND geo.status = 1')
            ->join('customer_accounts_address AS addr', 'addr.acctid = acct.sysid')
            ->join('gdlb_main AS gm', 'gm.sysid = acct.gdlb')
            ->join('address_districts AS d', 'd.sysid = gm.d')
            ->join('customer_accounts_mtrseq AS seq', 'seq.acctid = acct.sysid', 'left')
            ->join('rate_class_group AS cg', 'cg.rateid = acct.rateclassid')
            ->where(array('addr.status' => 1))
            ->group_by('acct.sysid, geo.lat, geo.lon, addr.addrspecific, acct.servicenumber, acct.types, acct.ownerid, acct.mtrno, seq.mrseq, gm.g, d.codes, gm.l, gm.b')
            ->get();

        $map_markers = array();
        $cust_list = array();
        if ($loc_query->num_rows() > 0) {
            $num = 1;
            foreach ($loc_query->result() as $row) {
                $seq = $row->mrseq;
                $owner_name = '';
                $addrspec = $row->spec;
                $mtrno = $row->mtrno;

                $dir_pic = '';

                // GET OWNER INFO
                if($row->types==1)  {
                    $person = get_person_info($row->ownerid)->info;
                    $owner_name = $person->lastname. ', '. $person->firstname. ' '.$person->middlename;
                    $owner_sysid = $person->sysid;
                    $dir_pic = 'person';
                }
                if($row->types==2)  {
                    $corp = get_corporation_info($row->ownerid);
                    $owner_name =$corp->codes. ' - '. $corp->descs;
                    $owner_sysid = $corp->sysid;
                    $dir_pic = 'corporation';
                }

                if($row->types==5)  {
                    $qry_name = $this->db->select('name')->from('customer_accounts_name_legacy')->where('sysid', $row->ownerid)->limit(1)->get()->row();
                    $owner_name = ($qry_name) ? $qry_name->name : '';
                    $dir_pic = '';
                }



                $pic_url = get_owner_pic($row->ownerid, $dir_pic);


                if ($row->servno != '' || $row->servno != NULL) {
                    $content_info = '<span style="position: absolute; bottom: 0px; right: 0px; background: red; padding: 2px 4px; display: inline-block; color: #fff;">' . $seq . '</span>'
                        . '<div class="col-md-2 " style="padding: 0px 0px;">'
                        . '<img style="width: 100%; height: 80px; display: inline-block;" src="' . $pic_url . '"/></div>'
                        . '<div class="col-md-10">'
                        . '<span class=""><span class="text-primary text-bold pull-right">' . $row->servno . '</span></span>'
                        . '<br><span class=""><span class="text-primary ">' . $owner_name . '</span></span>'
                        . '<br><em>' . $addrspec . '</em>'
                        . '<br><b>' . $mtrno . '</b>'
                        . '</div>';
                } else {
                    $content_info = '<span class="text-danger"><i class="fa fa-times"></i> Unknown!</span>';
                }

                if($row->lat>0 || $row->lon >0) {
                    $map_markers[] = array(
                        'location' => array('lat' => $row->lat, 'lng' => $row->lon),
                        'content' => $content_info,
                        'servno' => $row->servno,
                        'name' => $owner_name,
                        'spec' => $addrspec,
                        'icon' => base_url() . 'assets/global/img/peco-map-marker.png',
                    );
                }
                $cust_list[] = array(
                    'num' => $row->mrseq,
                    'acctid' => $row->acctid,
                    'servno' => $row->servno,
                    'mtrno' => $row->mtrno,
                    'name' => $owner_name,
                    'addrspec' => $addrspec,
                    'gdlb' => $row->gdlb,
                );
            }
            $qry = true;
        } else {
            //@TODO GET LEGACY ACCT
            $qry = false;
        }
        $data['input'] = $this->input->post();
        $data['qry'] = $qry;
        $data['zoom'] = $zoom;
        $data['list'] = $cust_list;
        $data['chartarr'] = $chart_arr;
        $data['charttitle'] = $chart_title;
        $data['customers'] = $map_markers;
        echo json_encode($data);
    }

    function getfilterview() {
        $dist = $this->input->post('district');
        $subs = $this->input->post('subscriptions');
        $spec = $this->input->post('specific');
        //$spec = 'M13094';
        //$dist = 1;
        if ($spec) {
            $zoom = 16;
            $loc_query = $this->db->select('addr.addrmapx AS x, addr.addrmapy AS y, addr.addrspecific As n, own.lastname AS ln, own.firstname AS fn')
                ->from('prime_customer_accounts_owners_address AS addr')
                ->join('prime_customer_accounts_owners AS own', 'own.sysid = addr.ownerid', 'left')
                ->join('prime_customer_accounts_main AS acct', 'acct.sysid = own.accountid', 'left')
                ->where(array('acct.servicenumber' => $spec))
                ->get();
        } else if ($dist == 0) {
            $zoom = 12;
            $loc_query = $this->db->select('addr.addrmapx AS x, addr.addrmapy AS y, addr.addrspecific As n, own.lastname AS ln, own.firstname AS fn')
                ->from('prime_customer_accounts_owners_address AS addr')
                ->join('prime_customer_accounts_owners AS own', 'own.sysid = addr.ownerid', 'left')
                ->join('prime_customer_accounts_main AS acct', 'acct.sysid = own.accountid', 'left')
                ->where(array('addr.addrmapx !=' => ''))
                ->get();
        } else {
            $zoom = 14;
            $loc_query = $this->db->select('addr.addrmapx AS x, addr.addrmapy AS y, addr.addrspecific As n, own.lastname AS ln, own.firstname AS fn')
                ->from('prime_customer_accounts_owners_address AS addr')
                ->join('prime_customer_accounts_owners AS own', 'own.sysid = addr.ownerid', 'left')
                ->join('prime_customer_accounts_main AS acct', 'acct.sysid = own.accountid', 'left')
                ->where(array('addr.district' => $dist, 'addr.addrmapx !=' => ''))
                ->get();
        }
        $sum_overdue = 0;
        $sum_count = 0;

        if ($loc_query->num_rows() > 0) {
            $map_arr = array();
            foreach ($loc_query->result() as $row) {
                $this_overdue = 3200;
                $sum_count += 1;
                $sum_overdue += $this_overdue;
                $map_arr[] = array('<div style="width: 250px; display: inline-block; min-height: 100px; position: relative"><img style="float: left; margin-right: 5px; width: 32px;" src="' . base_url('assets/global/img/logo/peco-ico.png') . '" /><div style="float: left; position: absolute; left: 40px; width: 200px"><b>' . $row->ln . ', ' . $row->fn . '</b> - ' . $row->n . '<br>Overdue: 3,320<br>Status: <span style="color: green">active</span></div></div>', floatval($row->x), floatval($row->y));
            }
            $qry = true;
        } else {
            $qry = false;
        }
        $data['qry'] = $qry;
        $data['zoom'] = $zoom;
        $data['overdue'] = number_format($sum_overdue, 2);
        $data['overcnt'] = number_format($sum_count);
        $data['default'] = ($loc_query->num_rows() > 0) ? $map_arr : false;
        echo json_encode($data);
    }

    function uploadpics() {

        /*
          $ds          = DIRECTORY_SEPARATOR;  //1

          $storeFolder = base_url().'uploads';   //2

          if (!empty($_FILES)) {

          $tempFile = $_FILES['file']['tmp_name'];          //3

          $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4

          $targetFile =  $targetPath. $_FILES['file']['name'];  //5

          move_uploaded_file($tempFile,$targetFile); //6

          }
          print_r($_FILES);

         */
        $config['upload_path'] = FCPATH . 'uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 5000;
        $config['max_width'] = 2048;
        $config['max_height'] = 2048;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $error = array('error' => $this->upload->display_errors());
            print_r($this->input->post());
        } else {
            $data = array('upload_data' => $this->upload->data());
            print_r($this->input->post());
        }
    }



    function testsearcharr () {
        $data = array();
        $firstname = 'LUCKY JOHN';
        $lastname = 'Faderon';
        $middlename = 'Flotildes';

        if (strpos('DELA CRUZ, LUCKY JOHN', $firstname) == TRUE) {
            echo 'FOUND!';
        }else{
            echo 'NOT FOUND!';
        }
    }


    function generateinputstohtml() {
        $data = array();
        $inputs = $this->input->post();
        $html = '';
        $lat = '';
        $lon = '';
        $zoom = '';
        $input_not_toshow = array(
            'stagelevel',
            'moduleid',
            'encodestart',
            'parnerfname',
            'acctex',
            'acctra',
            'apptype',
            'namecorpgov',
            'specificcorpgov'
        );
        if($inputs && count($inputs) > 0) {
            $html .= '<h3 class="text-primary bold">Summary</h3>';
            $html .= '<hr>';

            foreach($inputs as $key=>$row) {
                if (!in_array($key, $input_not_toshow)) {
                    if($key == 'googlemap') {
                        if($row != '') {
                            $get_latlon = explode('@', $row);
                            if(is_array($get_latlon) && count($get_latlon) > 1) {
                                $latlon_arr = explode(',', $get_latlon[1]);
                                $lat = (isset($latlon_arr[0])) ? $latlon_arr[0] : '';
                                $lon = (isset($latlon_arr[1])) ? $latlon_arr[1] : '';
                                $zoom = (isset($latlon_arr[2])) ? str_replace('z', '', $latlon_arr[2]) : '';
                                $html .= '<li class="list-group-item"><span class="label-name col-md-3">Map URL</span><span class="label label-default">'.$row.'</span></li>';
                                $html .= '<li class="list-group-item"><span class="label-name col-md-3">Lon</span><span class="label label-default">'.$lat.'</span></li>';
                                $html .= '<li class="list-group-item"><span class="label-name col-md-3">Lat</span><span class="label label-default">'.$lon.'</span></li>';
                                $html .= '<li class="list-group-item"><span class="label-name col-md-3">Zoom</span><span class="label label-default">'.$zoom.'</span></li>';
                            }
                        }
                    }else if($key == 'gender') {
                        $gender = ($row==1) ? 'Male':'Female';
                        $html .= '<li class="list-group-item"><span class="label-name col-md-3">Gender</span><span class="label label-default">'.$gender.'</span></li>';
                    }else if($key == 'marital') {
                        $marital = get_marital_name($row);
                        $html .= '<li class="list-group-item"><span class="label-name col-md-3">Marital</span><span class="label label-default">'.$marital.'</span></li>';
                    }else if($key == 'addrcity') {
                        $city = get_city_name($row);
                        $html .= '<li class="list-group-item"><span class="label-name col-md-3">City</span><span class="label label-default">'.$city.'</span></li>';
                    }else if($key == 'addrdistrict') {
                        $district = get_district_name($row);
                        $html .= '<li class="list-group-item"><span class="label-name col-md-3">District</span><span class="label label-default">'.$district.'</span></li>';
                    }else if($key == 'brgy') {
                        $brgy = get_brgy_name($row);
                        $html .= '<li class="list-group-item"><span class="label-name col-md-3">Brgy</span><span class="label label-default">'.$brgy.'</span></li>';
                    }else{
                        $value = ($row != '') ? '<span class="label label-default">' . $row . '</span>' :  '<span class="label label-default font-red">N/A</span>';
                        $html .= '<li class="list-group-item"><span class="label-name col-md-3">' . ucfirst($key) . ' </span>'.$value.'</li>';
                    }
                }
            }
        }
        $data['inputs'] = $inputs;
        $data['lat'] = $lat;
        $data['lon'] = $lon;
        $data['zoom'] = $zoom;
        $data['html'] = $html;
        echo json_encode($data);
    }


    function getnewcustaccountpreview() {
        $data = array();

        $application_type = $this->input->post('apptype');

        $essrno = $this->input->post('essrno');
        $lastname = $this->input->post('lastname');
        $firstname = $this->input->post('firstname');
        $middlename = $this->input->post('middlename');

        $gender = $this->input->post('gender');
        $phone = $this->input->post('phone');
        $mobile = $this->input->post('mobile');
        $email = $this->input->post('email');
        $district = $this->input->post('addrdistrict');

        $lastname_cnt       = 0;
        $firstname_cnt      = 0;
        $middlename_cnt     = 0;
        $district_cnt       = 0;
        $ra7832_cnt         = 0;
        $acctex_cnt         = 0;

        $person_ids = array();

        $html = '';


        if($application_type > 1) {
            // CORPORATION and GOVERNMENT

        }else{
            $person_exists = false;

            $first_array = array();
            $qry_lastnane = $this->db->select('p.sysid, p.lastname, p.middlename, p.firstname, am.addrdist')->from('person AS p')
                ->join('person_address_matrix AS am', 'am.personid = p.sysid', 'left')
                ->like('p.lastname', $lastname)
                ->group_by('p.sysid, p.lastname, p.middlename, p.firstname, am.addrdist')
                ->get();
            if($qry_lastnane->num_rows()>0) {
                foreach ($qry_lastnane->result() as $row) {
                    $first_array[] = array('sysid' => $row->sysid, 'firstname' => $row->firstname, 'middlename' => $row->middlename, 'district' => $row->addrdist);
                    $lastname_cnt += 1;
                }
                $search_firstname = array_contains_key($first_array, 'firstname', 'sysid', $firstname);
                $search_firstname_cnt = count($search_firstname);
                $person_exists = true;
                if($search_firstname_cnt >1) {
                    $firstname_cnt = $search_firstname_cnt;
                    $search_middlename = array_contains_key($search_firstname, 'middlename', 'sysid', $middlename);
                    $search_middlename_cnt = count($search_middlename);
                    $middlename_cnt = $search_middlename_cnt;
                    if($middlename_cnt>0) {
                        $person_exists = true;
                    }
                    foreach($search_firstname as $pids_row) {
                        $person_ids[] = $pids_row['sysid'];
                    }
                }

                $search_district = array_contains_key($search_firstname, 'district', 'sysid', $district);
                $search_district_cnt = count($search_district);
                $district_cnt = $search_district_cnt;
                if($district_cnt>0) {
                    $person_exists = true;
                }

            }

            $data['lastname_count'] = ($lastname_cnt>0) ? '<span class="text-danger text-bold pull-right">'.$lastname_cnt.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
            $data['firstname_count'] = ($firstname_cnt>0) ? '<span class="text-danger text-bold pull-right">'.$firstname_cnt.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
            $data['middlename_count'] = ($middlename_cnt>0) ? '<span class="text-danger text-bold pull-right">'.$middlename_cnt.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
            $data['personexists'] = $person_exists;

            $person_ids = array_unique($person_ids);

            if(count($person_ids) > 0) {
                $qry_person_matches = $this->db->select('p.sysid, p.lastname, p.middlename, p.firstname, am.addrdist')
                    ->from('person AS p')
                    ->join('person_address_matrix AS am', 'am.personid = p.sysid', 'left')
                    ->where_in('p.sysid', $person_ids)
                    ->group_by('p.sysid, p.lastname, p.middlename, p.firstname, am.addrdist')
                    ->get();

                $qry_person_matches_numrows = $qry_person_matches->num_rows();
                if ($qry_person_matches_numrows > 0) {

                    $match_title = '<p class="text-primary"> <i class="fa fa-search"></i> Name Matches <span class="label label-danger pull-right">' . $qry_person_matches_numrows . '</span></p>';

                    $message = '';
                    $message .= '<div style="margin: 0px 0px !important;" id="matches_persons_list" class="form-group form-md-line-input">';
                    $message .= '<div class="input-group">';
                    $message .= '<div class="md-radio-list">';
                    foreach ($qry_person_matches->result() as $mrow) {

                        $message .= '<div class="md-radio">';
                        $message .= '<input id="radio_' . $mrow->sysid . '" value="' . $mrow->sysid . '" type="radio" name="personid" class="icheck"> ';
                        $message .= '<label for="radio_' . $mrow->sysid . '">';
                        $message .= '<span class="inc"></span>';
                        $message .= '<span class="check"></span>';
                        $message .= '<span class="box"></span>' . $mrow->lastname . ', ' . $mrow->firstname . ' ' . $mrow->middlename . ' - ' . get_district_name($mrow->addrdist) . '</label>';
                        $message .= '</div>';
                    }
                    $message .= '</div>';
                    $message .= '</div>';
                    $message .= '</div>';

                    $html .= verify_html($match_title, $message, 'fa-user', 'alert-info');
                }
            }

            // ########################################
            // RA 7832 verification
            $name_arr = array($lastname);
            $legal_verify_ra7832 = veriry_legacy_ra7832($name_arr);
            $legal_ra7832_num_rows = $legal_verify_ra7832->num_rows();
            $first_array_ra7832 = array();

            if($legal_ra7832_num_rows>0) {
                $data['ra7832cnt'] = $legal_ra7832_num_rows;
                foreach($legal_verify_ra7832->result() as $lrow) {
                    $first_array_ra7832[] = array('sysid' => trim($lrow->sysid), 'firstname' => trim($lrow->pname), 'lastname' => trim($lrow->pname), 'district' => trim($lrow->paddr));
                }
                $data['ra7832arr'] = $first_array_ra7832;
                $search_firstname_ra = array_contains_key($first_array_ra7832, 'firstname', 'sysid', $firstname);
                $ra7832_cnt = count($search_firstname_ra);
                $data['ra7832arr_first'] = $search_firstname_ra;
            }
            // COUNT RA7832 AND DISPLAY RESULT
            if($ra7832_cnt>0) {
                $verb = ($ra7832_cnt>1) ? 'are' : 'is'; // VERB CONDITION
                $html .= verify_html('RA7832 (Legacy)', 'There '.$verb.' <b>'.$ra7832_cnt.'</b> match found, in R.A.7832 legacy file!', 'fa-warning', 'alert-warning');
            }



            $qry_accounts_legacy = $this->db->select('nl.sysid, nl.name, gm.d')
                ->select("(
                    SUM(ar.amt_01) +
                    SUM(ar.amt_02) +
                    SUM(ar.amt_03) +
                    SUM(ar.amt_04) +
                    SUM(ar.amt_05) +
                    SUM(ar.amt_06) +
                    SUM(ar.amt_07) +
                    SUM(ar.amt_08) +
                    SUM(ar.amt_09) +
                    SUM(ar.amt_10) +
                    SUM(ar.amt_11) +
                    SUM(ar.amt_12)
                 ) AS bal
                ", false)
                ->from('customer_accounts_name_legacy AS nl')
                ->join('customer_accounts_main AS am', 'am.ownerid = nl.sysid', 'left')
                ->join('gdlb_main AS gm', 'gm.sysid = am.gdlb', 'let')
                ->join('customer_accounts_ar AS ar', 'am.sysid = ar.acctid', 'left')
                ->like('nl.name', $lastname)
                ->group_by('nl.sysid, nl.name, gm.d')
                ->get();

            /*
            $qry_accounts_legacy = $this->db->query("
                SELECT nl.sysid, nl.name, gm.d,
                        SUM(ar.amt_01) + 
                        SUM(ar.amt_02) + 
                        SUM(ar.amt_03) + 
                        SUM(ar.amt_04) + 
                        SUM(ar.amt_05) + 
                        SUM(ar.amt_06) + 
                        SUM(ar.amt_07) + 
                        SUM(ar.amt_08) + 
                        SUM(ar.amt_09) + 
                        SUM(ar.amt_10) + 
                        SUM(ar.amt_11) + 
                        SUM(ar.amt_12)
                 ) AS bal
                FROM customer_accounts_name_legacy AS nl
                JOIN customer_accounts_main AS am ON am.ownerid = nl.sysid
                JOIN gdlb_main AS gm ON gm.sysid = am.gdlb
                JOIN customer_accounts_ar AS ar ON am.sysid = ar.acctid
                WHERE nl.name LIKE '%$lastname%' 
                GROUP BY nl.sysid, nl.name, gm.d
                ");
            */

            $data['errqrymsg'] = $this->db->_error_message();

            if($qry_accounts_legacy->num_rows()>0) {
                foreach ($qry_accounts_legacy->result() as $row) {
                    $first_array[] = array('sysid' => $row->sysid, 'firstname' => $row->name, 'middlename' => $row->name, 'district' => $row->d, 'bal' => $row->bal);
                    $data['personarr'] = $first_array;
                    $lastname_cnt += 1;
                }
                $search_firstname = array_contains_key($first_array, 'firstname', 'sysid', $firstname);
                $search_firstname_cnt = count($search_firstname);
                $data['searchfirstname'] = $search_firstname;
                $data['searchfirstnamecnt'] = $search_firstname_cnt;
                if($search_firstname_cnt > 0) {
                    $data['personexists'] = $person_exists;
                    $html .= '<h4 class="text-primary"> <i class="fa fa-tag"></i>Matching Account <span class="label label-danger pull-right">' . $search_firstname_cnt . '</span></h4>';

                    foreach ( $search_firstname as $arow ) {
                        $bal = (isset($arow['bal'])) ? $arow['bal'] : 0;
                        $message = '';
                        $message .= '<div style="margin: 0px 0px !important;" id="" class="form-group form-md-line-input">';
                        $message .= '<div class="input-group">';
                        $message .= '<div class="md-radio-list">';
                        $message .= '<div class="md-radio">';
                        // $message .= '<input id="radio_'.$arow['sysid'].'" value="'.$arow['sysid'].'" type="radio" name="accountid" class="icheck"> ';
                        $message .= '<label for="radio_'.$arow['sysid'].'">';
                        // $message .= '<span class="inc"></span>';
                        // $message .= '<span class="check"></span>';
                        // $message .= '<span class="box"></span>';
                        $message .= '<p>District:  ' . get_district_name($arow['district']) . '</p>';
                        $message .= '<p class="text-danger">Balance: <b>'.number_format($bal, 2).'</b></p>';
                        $message .= '</label>';
                        $message .= '</div>';
                        $message .= '</div>';
                        $message .= '</div>';
                        $message .= '</div>';
                        $html .= verify_html($arow['firstname'], $message, 'fa-user', 'alert-info');
                        $firstname_cnt += 1;
                        $acctex_cnt += 1;

                    }
                }
            }

        }

        // ################################################
        // CHECK EMAIL ADDRESS
        $qry_email = $this->db->select('COUNT(contactstring) AS CNT')->from('person_contact_matrix')->where(array('types' => 3, 'contactstring' => $email))->get()->row();
        $email_count = $qry_email->CNT;

        // ################################################
        // CHECK MOBILE
        $qry_mobile = $this->db->select('COUNT(contactstring) AS CNT')->from('person_contact_matrix')->where(array('types' => 1, 'contactstring' => $mobile))->get()->row();
        $mobile_count = $qry_mobile->CNT;

        // ################################################
        // CHECK PHONE
        $qry_phone = $this->db->select('COUNT(contactstring) AS CNT')->from('person_contact_matrix')->where(array('types' => 2, 'contactstring' => $mobile))->get()->row();
        $phone_count = $qry_phone->CNT;

        $data['personsids']     = $person_ids;
        $data['essrno']         = $essrno;
        $data['essrn_stat']     = '<i class="fa fa-check text-success  pull-right"></i>'; //@TODO create checking if ESSR Used is already existed in DB.
        $data['lastname']       = $lastname;
        $data['firstname']      = $firstname;
        $data['middlename']     = $middlename;
        $middlename             = (isset($middlename)) ? $middlename : '';
        $data['name']           = $firstname . ' ' . $middlename . '. ' . $lastname;

        $data['marital'] = select_marital($this->input->post('marital'));
        $data['phone'] = $phone;
        $data['phonecount'] = ($phone_count) ? '<span class="text-danger text-bold pull-right">'.$phone_count.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
        $data['mobile'] = $mobile;
        $data['mobilecount'] = ($mobile_count) ? '<span class="text-danger text-bold pull-right">'.$mobile_count.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
        $data['email'] = $email;
        $data['emailcount'] = ($email_count) ? '<span class="text-danger text-bold pull-right">'.$email_count.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
        $data['addrcity'] = select_city($this->input->post('addrcity'));
        $data['addrdist'] = get_district_name($district);
        $appcntnum = ($acctex_cnt + $ra7832_cnt);
        $data['addrdistcount'] = ($district_cnt) ? '<span class="text-danger text-bold pull-right">'.$district_cnt.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
        $data['appacctcntnum'] = $appcntnum;
        $data['appacctcnt'] = ($acctex_cnt > 0 || $ra7832_cnt >0 ) ? '<span class="text-danger text-bold pull-right">'.$appcntnum.'</span>' : '<span class="text-success text-bold pull-right"><i class="fa fa-check"></i></span>';
        $data['appacctmsg'] = ($acctex_cnt > 0 || $ra7832_cnt >0 ) ? '<span class="text-warning text-bold"><i class="fa fa-warning"></i> Account matched existed</span>' : '<span class="text-success text-bold"><i class="fa fa-check"></i> No account match existed</span>';
        $data['addrcountry'] = select_country($this->input->post('country'));
        $data['addrspec'] = $this->input->post('addrspecific');


        $acctrate = $this->input->post('acctrate');
        $acctreq = $this->input->post('acctreq');
        $accttype = $this->input->post('accttype');
        $conntype = $this->input->post('ownertype');
        $loctype = $this->input->post('loctype');
        $acctreqadd = $this->input->post('acctreqadd');

        $conntype = get_acct_type($conntype);
        $loctype = get_acct_type($loctype);
        $req_qry = get_requirements_name($acctreq);
        $req_html = '';
        if ($req_qry) {
            $req_html .= '<ul class="list-group">';
            foreach ($req_qry as $row) {
                $req_html .= '<li class="list-group-item-text"><span style="width: 100px; display: inline-block; ">' . $row->desc . '</span></li>';
            }
            $req_html .= '</ul>';
        }

        $rate_qry = get_account_rate($acctrate);
        $accttype = get_acct_type($accttype);
        $rate_html = '';
        if ($rate_qry) {
            foreach ($rate_qry as $row) {
                $rate_html .= '<span style="margin-right: 2px;">' . $row->classifications . '</span>';
            }
        }

        $q = true;

        $data['acctex'] = $acctex_cnt;
        $data['acctra'] = $ra7832_cnt;

        $data['gender'] = gender($gender);
        $data['reqhtml'] = $req_html;
        $data['ratehtml'] = $rate_html;
        $data['accttype'] = $accttype;
        $data['conntype'] = $conntype;
        $data['loctype'] = $loctype;
        $data['html'] = $html;
        $data['qry'] = $q;

        $data['input'] = $this->input->post();
        echo json_encode($data);

        /*
        $acctidentity = $this->input->post('acctidentity');
        $corp = $this->input->post('corpidentity');
        $prof = $this->input->post('profiletype');
        $lastname = $this->input->post('lastname');

        $data['marital'] = select_marital($this->input->post('marital'));
        $data['phone'] = $this->input->post('phone');
        $data['mobile'] = $this->input->post('mobile');
        $data['email'] = $this->input->post('email');
        $data['addrcity'] = select_city($this->input->post('addrcity'));
        $data['addrdist'] = get_district_name($this->input->post('addrdistrict'));
        $data['addrcountry'] = select_country($this->input->post('country'));
        $data['addrspec'] = $this->input->post('addrspecific');
        $corponame = $this->input->post('corponame');

        if ($corp == '1') {
            $qry1 = $this->db->select('*')->from('corporation')->where_in('sysid', $corponame)->get()->row();
            if ($qry1) {
                $q = true;
                $data['corponame'] = $qry1->codes . '-' . $qry1->descs;
            } else {
                $q = false;
            }
        } else {
            $q = true;
            //list($codes, $descs) = explode('-', $corponame);
            $data['corponame'] = $corponame;
        }
        if ($acctidentity == '1') {
            //$q = $this->input->post('q');
            $qry = $this->db->select('*')->from('person')->where_in('sysid', $lastname)->get()->row();
            if ($qry) {
                $q = true;
                $name = $qry->lastname . ', ' . $qry->firstname . ' ' . $qry->middlename;
                $data['lastname'] = $qry->lastname;
                $data['firstname'] = $qry->firstname;
                $data['middlename'] = $qry->middlename;
                $gender = $qry->gender;
                $data['birthdate'] = $qry->birthdate;
            } else {
                $q = false;
                $name = '';
            }
        } else {
            $gender = $this->input->post('gender');
            $data['prefix'] = select_person_title(71, $this->input->post('prefix'));
            //$name = $lastname . ', ' . $this->input->post('firstname') . ' ' . $this->input->post('middlename');
            $data['firstname'] = $this->input->post('firstname');
            $data['middlename'] = $this->input->post('middlename');
            $data['lastname'] = $lastname;
            $data['suffix'] = select_person_title(70, $this->input->post('suffix'));

            $data['birthdate'] = $this->input->post('birthdate');

            $data['addrspec'] = $this->input->post('addrspecific');
            $q = true;
        }

        $name = $data['lastname'] . ', ' . $data['firstname'] . ' ' . $data['middlename'];

        $fname = $data['firstname'];
        $mname = $data['middlename'];
        $lname = $data['lastname'];

        $acctrate = $this->input->post('acctrate');
        $acctreq = $this->input->post('acctreq');
        $acctreqadd = $this->input->post('acctreqadd');
        $accttype = $this->input->post('accttype');
        $conntype = $this->input->post('conntype');
        $loctype = $this->input->post('loctype');

        $conntype = get_acct_type($conntype);
        $loctype = get_acct_type($loctype);
        $req_qry = get_requirements_name($acctreq);
        $req_html = '';
        if ($req_qry) {
            $req_html .= '<ul class="list-group">';
            foreach ($req_qry as $row) {
                $req_html .= '<li class="list-group-item-text"><span style="width: 100px; display: inline-block; ">' . $row->desc . '</span></li>';
            }
            $req_html .= '</ul>';
        }

        $rate_qry = get_account_rate($acctrate);
        $accttype = get_acct_type($accttype);
        $rate_html = '';
        if ($rate_qry) {
            foreach ($rate_qry as $row) {
                $rate_html .= '<span style="margin-right: 2px;">' . $row->classifications . '</span>';
            }
        }
        $data['name'] = $name;
        $data['fname'] = $fname;
        $data['mname'] = $mname;
        $data['lname'] = $lname;
        $data['qry'] = $q;
        $data['gender'] = gender($gender);
        $data['reqhtml'] = $req_html;
        $data['ratehtml'] = $rate_html;
        $data['accttype'] = $accttype;
        $data['conntype'] = $conntype;
        $data['loctype'] = $loctype;
        $data['input'] = $this->input->post();
        */
    }

    function select2getusers() {
        //$q=$_REQUEST["q"]; 
        $q = $this->input->post('term');
        $qry = $this->db->select('p.sysid, u.sysid AS userid, u.username, p.lastname, p.firstname, p.middlename, p.birthdate, p.gender, addrm.addrspec')->from('person AS p')
            ->join('person_address_matrix AS addrm', 'addrm.personid = p.sysid', 'left')
            ->join('prime_system_users AS u', 'p.sysid = u.personid')
            ->or_like('p.lastname', $q)
            ->or_like('p.middlename', $q)
            ->or_like('p.firstname', $q)
            ->or_like('u.username', $q)
            ->where('addrm.status', 1)
            ->get();
        $res = array();
        foreach ($qry->result() as $row) {
            $birthday = strtotime($row->birthdate);
            $birthday = date("F d, Y", $birthday);
            $profile_pic_filename_last = '';
            if (file_exists(FCPATH . 'uploads/person/' . $row->sysid)) {
                $check_primary_file = glob(FCPATH . 'uploads/person/' . $row->sysid . '/primary.*');
                //usort($check_primary_file, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
                array_multisort(
                    array_map('filemtime', $check_primary_file), SORT_NUMERIC, SORT_DESC, $check_primary_file
                );
                $i = 0;
                $len = count($check_primary_file);
                if ($check_primary_file) {
                    foreach ($check_primary_file as $row_pic) {
                        if ($i == 0) {
                            // first
                            $profile_pic_filename_first = $row_pic;
                        } else if ($i == $len - 1) {
                            // last
                            $profile_pic_filename_last = $row_pic;
                        }
                        $i++;
                    }
                    $profile_pic_exist = true;
                }
            } else {
                $profile_pic_exist = false;
            }
            if ($profile_pic_exist == true) {
                if ($profile_pic_filename_last) {
                    $pic_filename = $profile_pic_filename_last;
                } else {
                    $pic_filename = $profile_pic_filename_first;
                }
                $pic = 'uploads/person/' . $row->sysid . '/' . basename($pic_filename);
            } else {
                $pic = ($row->gender == 1) ? 'assets/global/img/default_avatar_male.png' : 'assets/global/img/default_avatar_female.png';
            }


            $res[] = array(
                'id' => $row->userid,
                'text' => highlightkeyword($row->lastname, $q) . ', ' . highlightkeyword($row->firstname, $q) . ' ' . highlightkeyword($row->middlename, $q) . '<br><i class="fa fa-key text-warning"></i> <span class="text-color-blue">' . highlightkeyword($row->username, $q) . '</span>',
                'birthday' => $birthday,
                'gender' => gender_icon($row->gender),
                'address' => $row->addrspec,
                'pic' => $pic,
            );
        }
        echo json_encode($res);
    }


    function searchaccountservices() {
        $res = array();
        $type = $this->input->post('type');
        $q = $this->input->post('term');

        if($type==2) {
            $qry = $this->db->select('
                m.sysid,
                m.servicenumber AS servno,
                a.addrspecific AS addr,
                m.types,
                m.ownerid
            ')
                ->from('customer_accounts_main AS m')
                ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
                ->or_like('m.servicenumber', $q)
                ->get();

            $res = array();
            if($qry->num_rows()>0) {
                foreach ($qry->result() as $row) {

                    $pic = base_url('assets/global/img/person_default.jpg');
                    $name = '@TODO';
                    if($row->types==5) {
                        $qry_legacy = $this->db->select("name")
                            ->from('customer_accounts_name_legacy')
                            ->where("sysid", $row->ownerid)
                            ->get()->row();
                        if($qry_legacy) {
                            $name = $qry_legacy->name;
                        }
                    }
                    $res[] = array(
                        'id' => $row->sysid,
                        'text' => $row->servno,
                        'address' => $row->addr,
                    );
                }
            }
        }else{
            $qry = $this->db->select('p.sysid, p.lastname, p.firstname, p.middlename, p.birthdate, p.gender, addrm.addrspec')->from('person AS p')
                ->join('person_address_matrix AS addrm', 'addrm.personid = p.sysid AND addrm.status = 1', 'left')
                ->or_like('lastname', $q)
                ->or_like('middlename', $q)
                ->or_like('firstname', $q)
                ->get();
            $res = array();
            foreach ($qry->result() as $row) {
                $res[] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname. ', ' . $row->firstname. ' ' .$row->middlename,
                    'address' => $row->addrspec,
                );
            }
        }
        echo json_encode($res);
    }






    function select2customers() {
        //$q=$_REQUEST["q"];
        $q = $this->input->post('term');
        $qry = $this->db->select('
                m.sysid,
                m.servicenumber AS servno,
                a.addrspecific AS addr,
                m.types,
                m.ownerid
            ')
            ->from('customer_accounts_main AS m')
            ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
            ->or_like('m.servicenumber', $q)
            ->get();

        $res = array();
        if($qry->num_rows()>0) {
            foreach ($qry->result() as $row) {

                $pic = base_url('assets/global/img/person_default.jpg');
                $name = '@TODO';
                if($row->types==5) {
                    $qry_legacy = $this->db->select("name")
                        ->from('customer_accounts_name_legacy')
                        ->where("sysid", $row->ownerid)
                        ->get()->row();
                    if($qry_legacy) {
                        $name = $qry_legacy->name;
                    }
                }
                $res[] = array(
                    'id' => $row->sysid,
                    'text' => $row->servno,
                    'name' => $name,
                    'addr' => $row->addr,
                    'pics' => $pic,
                );
            }
        }else{
            $qry = $this->db->select('
                m.sysid, 
                m.servicenumber AS servno, 
                l.name,
                a.addrspecific AS addr
                ')
                ->from('customer_accounts_name_legacy AS l')
                ->join('customer_accounts_main AS m', 'm.ownerid = l.sysid', 'left')
                ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
                ->or_like('l.name', $q)
                ->where('m.types', 5)
                ->get();

            $res = array();
            if($qry->num_rows()>0) {
                foreach ($qry->result() as $row) {

                    $pic = base_url('assets/global/img/person_default.jpg');
                    $res[] = array(
                        'id' => $row->sysid,
                        'text' => $row->servno,
                        'name' => $row->name,
                        'addr' => $row->addr,
                        'pics' => $pic,
                    );
                }
            }
        }
        echo json_encode($res);
    }


    function editableselect2(){
        $q = $this->input->post('term');
        $qry = $this->db->select('p.sysid, p.lastname, p.firstname, p.middlename, p.birthdate, p.gender, addrm.addrspec')->from('person AS p')
            ->join('person_address_matrix AS addrm', 'addrm.personid = p.sysid', 'left')
            ->or_like('lastname', $q)
            ->or_like('middlename', $q)
            ->or_like('firstname', $q)
            ->where('addrm.status', 1)
            ->get();
        $res = array();
        if($qry->num_rows()>0 && !empty($q)) {
            foreach ($qry->result() as $row) {
                $res[] = array(
                    'id' => $row->sysid,
                    'text' => $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename,
                );
            }
        }
        echo json_encode($res);
    }

    function getpersoninfo() {
        //$q=$_REQUEST["q"]; 
        $q = $this->input->post('term');
        $qry = $this->db->select('p.sysid, p.lastname, p.firstname, p.middlename, p.birthdate, p.gender, addrm.addrspec')->from('person AS p')
            ->join('person_address_matrix AS addrm', 'addrm.personid = p.sysid AND addrm.status = 1', 'left')
            ->or_like('lastname', $q)
            ->or_like('middlename', $q)
            ->or_like('firstname', $q)
            ->get();
        $res = array();
        foreach ($qry->result() as $row) {
            $birthday = strtotime($row->birthdate);
            $birthday = date("F d, Y", $birthday);
            $profile_pic_filename_last = '';
            if (file_exists(FCPATH . 'uploads/person/' . $row->sysid)) {
                $check_primary_file = glob(FCPATH . 'uploads/person/' . $row->sysid . '/primary.*');
                //usort($check_primary_file, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
                array_multisort(
                    array_map('filemtime', $check_primary_file), SORT_NUMERIC, SORT_DESC, $check_primary_file
                );
                $i = 0;
                $len = count($check_primary_file);
                if ($check_primary_file) {
                    foreach ($check_primary_file as $row_pic) {
                        if ($i == 0) {
                            // first
                            $profile_pic_filename_first = $row_pic;
                        } else if ($i == $len - 1) {
                            // last
                            $profile_pic_filename_last = $row_pic;
                        }
                        $i++;
                    }
                    $profile_pic_exist = true;
                }
            } else {
                $profile_pic_exist = false;
            }
            if ($profile_pic_exist == true) {
                if ($profile_pic_filename_last) {
                    $pic_filename = $profile_pic_filename_last;
                } else {
                    $pic_filename = $profile_pic_filename_first;
                }
                $pic = 'uploads/person/' . $row->sysid . '/' . basename($pic_filename);
            } else {
                $pic = ($row->gender == 1) ? 'assets/global/img/default_avatar_male.png' : 'assets/global/img/default_avatar_female.png';
            }


            $res[] = array(
                'id' => $row->sysid,
                'text' => highlightkeyword($row->lastname, $q) . ', ' . highlightkeyword($row->firstname, $q) . ' ' . highlightkeyword($row->middlename, $q),
                'birthday' => $birthday,
                'gender' => gender_icon($row->gender),
                'address' => $row->addrspec,
                'pic' => $pic,
            );
        }
        echo json_encode($res);
    }

    function getcorpinfo() {
        //$q=$_REQUEST["q"]; 
        $q = $this->input->post('term');
        $qry = $this->db->distinct()->select("c.sysid, c.codes, c.descs")->from('corporation AS c')
            ->distinct()->select("CONCAT(p.lastname, ', ', p.firstname) AS repname", false)
            ->distinct()->select("CONCAT(addrm.addrspec) AS addr", false)
            ->join('corporation_address_matrix  AS addrm', 'addrm.corpid = c.sysid', 'left')
            ->join('corporation_representative AS rep', 'rep.corpid = c.sysid', 'left')
            ->join('person AS p', 'p.sysid = rep.personid', 'left')
            ->where('addrm.status', 1)
            ->or_like('c.codes', $q)
            ->or_like('c.descs', $q)
            ->or_like('p.lastname', $q)
            ->get();
        $res = array();
        foreach ($qry->result() as $row) {
            //$birthday = strtotime($row->birthdate);
            //$birthday = date("F d, Y", $birthday);
            $profile_pic_filename_last = '';
            if (file_exists(FCPATH . 'uploads/corporation/' . $row->sysid)) {
                $check_primary_file = glob(FCPATH . 'uploads/corporation/' . $row->sysid . '/primary.*');
                //usort($check_primary_file, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
                array_multisort(
                    array_map('filemtime', $check_primary_file), SORT_NUMERIC, SORT_DESC, $check_primary_file
                );
                $i = 0;
                $len = count($check_primary_file);
                if ($check_primary_file) {
                    foreach ($check_primary_file as $row_pic) {
                        if ($i == 0) {
                            // first
                            $profile_pic_filename_first = $row_pic;
                        } else if ($i == $len - 1) {
                            // last
                            $profile_pic_filename_last = $row_pic;
                        }
                        $i++;
                    }
                    $profile_pic_exist = true;
                }
            } else {
                $profile_pic_exist = false;
            }
            if ($profile_pic_exist == true) {
                if ($profile_pic_filename_last) {
                    $pic_filename = $profile_pic_filename_last;
                } else {
                    $pic_filename = $profile_pic_filename_first;
                }
                $pic = 'uploads/corporation/' . $row->sysid . '/' . basename($pic_filename);
            } else {
                $pic = 'assets/global/img/peco-ico.png';
            }


            $res[] = array(
                'id' => $row->sysid,
                'text' => highlightkeyword($row->codes, $q) . ', ' . highlightkeyword($row->descs, $q),
                'rep' => highlightkeyword($row->repname, $q),
                'address' => $row->addr,
                'pic' => $pic,
            );
        }
        echo json_encode($res);
    }

    function getcorpexist() {
        $q = $this->input->post('q');
        //$qry = $this->db->select()->from('corporation')->where_in('sysid', $q)->get()->row();
        $qry = $this->db->select("c.sysid, c.codes, c.descs")->from('corporation AS c')
            ->select("p.sysid, p.lastname, p.firstname, p.middlename, p.gender, p.birthdate")
            ->select("addrm.addrspec, addrm.addrdist, addrm.addrcity, addrm.addrcountry", false)
            ->select("rep.personid", false)
            ->join('corporation_address_matrix  AS addrm', 'addrm.corpid = c.sysid', 'left')
            ->join('corporation_representative AS rep', 'rep.corpid = c.sysid', 'left')
            ->join('person AS p', 'p.sysid = rep.personid', 'left')
            ->where_in('c.sysid', $q)
            ->where('addrm.status', 1)
            ->get()->row();
        if ($qry) {
            $data['corpname'] = $qry->codes;
            $data['lastname'] = $qry->lastname;
            $data['firstname'] = $qry->firstname;
            $data['middlename'] = $qry->middlename;
            $data['gender'] = $qry->gender;
            $data['birthdate'] = $qry->birthdate;
            $data['personid'] = $qry->personid;
            $data['addrcity'] = $qry->addrcity;
            $data['addrspec'] = $qry->addrspec;
            $data['addrdist'] = $qry->addrdist;
            $data['addrcountry'] = $qry->addrcountry;
        } else {
            $data['addrspec'] = $this->input->post('cas');
            $data['addrdist'] = $this->input->post('cd');
        }
        $data['qry'] = ($qry) ? true : false;
        echo json_encode($data);
    }

    function getpersonexist() {
        $q = $this->input->post('q');
        $prof = $this->input->post('prof');
        $qry = $this->db->select('*')->from('person')->where_in('sysid', $q)->get()->row();
        $qry1 = $this->db->select('*')->from('person_address_matrix')->where_in('personid', $q)->where('status', 1)->get()->row();
        /*
          $sql = "SELECT pt.personid, pt.titleid, ptm.sysid, ptm.names, ptm.descriptions, ptm.types
          FROM person_title as pt
          LEFT JOIN person_title_main as ptm ON ptm.sysid = pt.titleid
          WHERE pt.personid = ?";
          $qry2 = $this->db->query($sql, array($q))->result();
         */
        $qry2 = $this->db->select('pt.personid, pt.titleid, ptm.sysid, ptm.names, ptm.descriptions, ptm.types')
            ->from('person_title as pt')
            ->join('person_title_main as ptm', 'ptm.sysid=pt.titleid', 'left')
            ->where_in('pt.personid', $q)->get()->row();

        if ($qry) {
            $q = true;
            $name = $qry->lastname . ', ' . $qry->firstname . ' ' . $qry->middlename;
            $data['firstname'] = $qry->firstname;
            $data['middlename'] = $qry->middlename;
            $data['gender'] = $qry->gender;
            $data['birthdate'] = $qry->birthdate;
        } else {
            $q = false;
            $name = '';
        }
        if ($prof == '1' || $prof == '') {
            if ($qry1) {
                $q = true;
                $data['addrspec'] = $qry1->addrspec;
                $data['addrdist'] = $qry1->addrdist;
                $data['addrcity'] = $qry1->addrcity;
                $data['addrcountry'] = $qry1->addrcountry;
            } else {
                $q = false;
            }
        } else {
            $data['addrspec'] = $this->input->post('addrspec');
            $data['addrdist'] = $this->input->post('addrdist');
        }
        if ($qry2) {
            $data['qry2'] = $qry2;
            if ($qry2->types == '71') {
                $data['prefix'] = $qry2->titleid;
            }
            if ($qry2->types == '70') {
                $data['suffix'] = $qry2->titleid;
            }
        } else {
            $data['qry2'] = $q;
            $data['prefix'] = "";
            $data['suffix'] = "";
        }
        $data['name'] = $name;
        $data['qry'] = $q;
        //$data['qry2'] = $qry2;
        echo json_encode($data);
    }

    function test() {
        $amt = 750;
        $array = array(
            array('amt' => 4000, 'val' => 400),
            array('amt' => 1000, 'val' => 100),
            array('amt' => 3000, 'val' => 300),
            array('amt' => 2000, 'val' => 200),
            array('amt' => 6000, 'val' => 600),
            array('amt' => 10000, 'val' => 1000),
            array('amt' => 8000, 'val' => 800),
            array('amt' => 7000, 'val' => 700),
            array('amt' => 9000, 'val' => 900),
        );
        echo getequivalent($amt, $array);
    }

    function readentrysubmit() {
        $dataid = rand(300, 5000);
        $qry = $this->db->query("INSERT INTO transaction_request_main (`origid`, `trncode`, `codes`, `descriptions`, `validations`, `moduleid`, `stagesidfrom`, `stagesid`, `dataid`, `arraydata`, `datecreated`, `createdby`, `status`, `remarks`) VALUES ('18', 'TRN0210', 'Reading', 'MRD - Reading', NULL, '19', NULL, '9', '" . $dataid . "', 'Lucky John Faderon/TRN00210', NOW(), '1', '0', NULL);
");
        echo ($qry) ? true : false;
    }

    function bosentrysubmit() {
        $moduleid = 37;
        $codes = $this->model_bos->getmoduledatabos($moduleid)->codes;
        $descs = $this->model_bos->getmoduledatabos($moduleid)->desc;

        $dataid = 1;
        //$qry = $this->db->query("INSERT INTO transaction_request_main (`origid`, `trncode`, `codes`, `descriptions`, `validations`, `moduleid`, `stagesidfrom`, `stagesid`, `dataid`, `arraydata`, `datecreated`, `createdby`, `status`, `remarks`) VALUES (37, 'TRN0310', 'Budget Entry', 'BOS - Entry', NULL, 37, 11, 12, '" . $dataid . "', 'Lucky John Faderon/TRN00210', NOW(), '1', '0', NULL);");
        $qry = create_transaction_trails($codes, $descs, $moduleid, $dataid);
        echo ($qry) ? true : false;
    }

    function initcomplaintslog() {
        $this->datatables->select('');
        $this->datatables->from("trouble_call_monitoring_log t");
        $this->datatables->join("person p", 't.person_id = p.sysid', 'left');
        $this->datatables->where("p.sysid", $id);
        echo $this->datatables->generate();
    }

    function savetroublecall() {
        $ins_arr = array(
            'source_id' => $this->input->post('source'),
            'complaint' => $this->input->post('complaints'),
            'contact_number' => $this->input->post('contact'),
            'person_id' => $this->input->post('troubleshooter'),
            'addr_district' => $this->input->post('addrdist'),
            'addr_specific' => $this->input->post('addrspec'),
        );
        $this->db->trans_begin();
        $this->db->insert('trouble_call_monitoring_log', $ins_arr);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data['qry'] = false;
        } else {
            $this->db->trans_commit();
            $data['qry'] = true;
        }

        echo json_encode($data);
    }

    function troubledatatable() {
        echo $this->query->trblcalldatatable();
    }

    function getaddress() {
        $addrcity = get_address_name($this->input->post('addrcity'),'city')->addrname ;
        $addrdistrict = get_address_name($this->input->post('addrdistrict'),'district')->addrname;
        $country = ($this->input->post('country')) ? get_address_name($this->input->post('country'),'country')->addrname : 'Philippines';
        $data = array();
        $data['address'] = $addrdistrict . ', ' . $addrcity . ' City, ' . $country;
        echo json_encode($data);
    }

    // IMPORTANT QUERY //
    function approval() {
        echo json_encode($this->query->approval_process());
    }

    // QUERY FOR SOLVING DEPOSIT COST
    function getdepositcost() {
        $dops = $this->input->post('dops');
        $mops = $this->input->post('mops');
        $demand = $this->input->post('demand');
        $rate = $this->input->post('rate');
        $accountid = $this->input->post('accountid');
        $total_load = 0;
        $deposit_cost = 0;
        //$qry = $this->db->query("SELECT watts, qty FROM prime_customer_accounts_equipments WHERE accountid=? AND status=?", array($accountid, 1))->get();
        $qry = $this->db->select('watts, qty')->from('prime_customer_accounts_equipments')->where(array('accountid' => $accountid, 'status' => 1))->get();
        $data = array();
        if ($qry) {
            $data['temp_load'] = 0;
            foreach ($qry->result() as $row) {
                $total_load += $row->watts * $row->qty;
                $data['temp_load'] += $total_load;
            }
            $data['total_load'] = $total_load;
            $data['dops'] = $dops;
            $data['mops'] = $mops;
            $data['rate'] = $rate;
            $data['demand'] = $demand;
            $deposit_cost += ($total_load * $dops * $mops * $rate * $demand) / 1000;
            $data['deposit_cost'] = $deposit_cost;
            $data['qry'] = true;
        } else {
            $data['qry'] = false;
            $data['deposit_cost'] = 0;
        }

        echo json_encode($data);
    }

    //testing sum_time function
    function test_sum_time() {
        echo $this->query->sum_time(array("2016-11-09 08:51:03", "2016-11-12 09:00:33", "2016-11-09 09:01:11", "2016-11-09 09:01:30", "2016-11-09 09:01:44", "2016-11-09 14:18:55"));
    }

    function newBudgetTable() {
        //Inserts the data to the database, the query1 and query2 in the return statement is for debugging only, transaction statement were used to know if the query is successful.
        $cc = $this->input->post('costCenter');
        $year = $this->input->post('year');
        $bName = $this->input->post('budgetName');
        $bType = $this->input->post('bType');
        $employee_id = user_session()->system_user_sessid;
        $same_budget_cc = false;
        $data = array();

        $employee_budget_added_data = $this->model_bos->getUserNewlyCreatedBudget($employee_id);
        if ($employee_budget_added_data){
            /**
             * This aims to prevent adding of multiple cost-center tables during budget-creation.
             */
            $employee_budget_added_data_result = $employee_budget_added_data->result();
            foreach($employee_budget_added_data_result as $row){
                if ($row->ccid == $cc && $row->status == 1 && $row->btype == $bType){
                    $same_budget_cc = true;
                    break;
                }
            }
        }
        if (!$same_budget_cc){
            $query = $this->model_bos->newBudgetEntry($cc, $year, $bName, $bType, $employee_id);
            $data = array(
                'query1' => $query['query1'],
                'query2' => $query['query2'],
                'transaction' => $query['transaction']
            );
        }else{
            $data = array(
                'query1' => 'Cannot add multiple cost-center.',
                'query2' => 'Please refrain from adding cost-centers that were already added.',
                'transaction' => false
            );
        }

        echo json_encode($data);
    }

    function getcustnearmtr() {
        echo $this->query->get_customer_near_mtr();
    }

    function searchmeter() {
        echo $this->query->search_meter();
    }


    // ###################################
    // SELECT2 OPTIONS // ################
    // ###################################

    function prefix() {
        $data = array();
        $qry = select_person_title(71);
        if ($qry) {
            foreach ($qry as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->descriptions,
                );
            }
        }
        echo json_encode($data);
    }

    function suffix() {
        $data = array();
        $qry = select_person_title(70);
        if ($qry) {
            foreach ($qry as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->descriptions,
                );
            }
        }
        echo json_encode($data);
    }

    function marital() {
        $data = array();
        $qry = select_marital();
        if ($qry) {
            foreach ($qry as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names,
                );
            }
        }
        echo json_encode($data);
    }

    function select2nationality() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('address_country')
            ->or_like('country', $term)
            ->or_like('nationality', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->nationality) . ' - ' .$row->country,
                    'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }


    function select2city() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('address_city')
            ->or_like('names', $term)
            ->or_like('descriptions', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->names). ' - ' .ucfirst($row->descriptions),
                );
            }
        }
        echo json_encode($data);
    }

    function apprehensions() {
        $data = array();
        $qry = $this->db->select()->from('prime_types_parameter')->where('codes', 'LEGALAPP')->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc,
                );
            }
        }
        echo json_encode($data);
    }

    function legaltrntype() {
        $data = array();
        $qry = $this->db->select()->from('prime_types_parameter')->where('codes', 'LEGALTRN')->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc,
                );
            }
        }
        echo json_encode($data);
    }

    function personinfo() {
        $data = array();
        $ownerid = $this->input->post('id');
        $qry = get_ownership_details(1, $ownerid);
        $data['info'] = ($qry) ? $qry : false;
        echo json_encode($data);
    }
    // ######################################
    // TESTS PAGES ##########################
    function getdaterange() {
        $date1 = date_create("2016-03-31");
        $date2 = date_create("2017-04-11");
        $diff = date_diff($date1,$date2);
        $days = $diff->format("%a");
        $remaining_days = ($days / 365);
        echo $remaining_days;
    }

    function orlike() {
        $type = 151;
        $id = array(
            'lastname' => 'Varon',
            'middlename' => 'G',
            'firstname' => 'Marlon'
        );

        foreach($id as $key => $value) {
            if($key == 0) {
                $this->db->like($key, $value, false);
            } else {
                $this->db->or_like($key, $value, false);
            }
        }


        /*
        $like_statements = array();

        foreach($id as $key => $value) {
            echo $key;
            $like_statements[] = " p.$key LIKE '%" . $value . "%' ";
        }

        $like_string = "(" . implode(' OR ', $like_statements) . ")";
        $this->db->where($like_string);
        */
        $qry_person = $this->db->select()->from("person AS p")
            ->join('trn_apprehensions AS ta', 'ta.personid = p.sysid AND ta.types = '. $type)
            ->get();

        print_r($qry_person->result());
    }
    //start select district
    function select2district() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('address_districts')
            ->or_like('names', $term)
            ->or_like('descriptions', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->codes) . ' - ' . ucfirst($row->descriptions),
                );
            }
        }
        echo json_encode($data);
    }

    function select2civilstatus() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('marital')
            ->or_like('names', $term)
            ->or_like('descriptions', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->names) . ' - ' . ucfirst($row->descriptions),
                );
            }
        }
        echo json_encode($data);
    }
    function select2bloodtype() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select("sysid,type")->from('prime_employee_blood_type')
            ->or_like('type', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->sysid . ' - ' . $row->type,
                );
            }
        }
        echo json_encode($data);
    }
    function select2religion() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select("sysid,names")->from('prime_employee_religions')
            ->or_like('names', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->sysid . ' - ' . $row->names,
                );
            }
        }
        echo json_encode($data);
    }
    function select2educationalattainment(){
        $data = array();
        $term = $this->input->post('term');
        $qry = $this->db->select("sysid,educattainment")->from('prime_educational_attainment')
            ->or_like('educattainment', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->sysid . ' - ' . $row->educattainment,
                );
            }
        }

        echo json_encode($data);
    }
    function select2titlelicense(){
        $data = array();
        $term = $this->input->post('term');
        $qry = $this->db->select("sysid,code")->from('prime_title_license')
            ->or_like('code', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->sysid . ' - ' . $row->code,
                );
            }
        }

        echo json_encode($data);
    }
    //end select district

    //start select GDLB
    function select2gdlb() {
        $term = $this->input->post('term');
        $dataid = $this->input->post('data');
        $data = array();
        if ($dataid != 'false') {
            $where = " GDLB.g < 6 AND GDLB.d = ".$dataid;
        } else {
            $where = " concat_ws(' ',GDLB.g,'-',DIST.codes,'-',GDLB.l,'-',GDLB.b) LIKE '%$term%' AND GDLB.g < 6 ";
        }
        $qry = $this->db->query("SELECT GDLB.sysid, COUNT(DISTINCT(am.sysid)) AS cnt, CONCAT(GDLB.g,'-',DIST.codes,'-',GDLB.l,'-',GDLB.b) AS GDLBNAME from gdlb_main AS GDLB
                                  INNER JOIN address_districts AS DIST ON DIST.sysid = GDLB.d
                                  LEFT JOIN customer_accounts_main AS am ON am.gdlb = GDLB.sysid AND am.`status` = 1
                                  WHERE 
                                  $where
                                  GROUP BY GDLB.sysid, CONCAT(GDLB.g,'-',DIST.codes,'-',GDLB.l,'-',GDLB.b)
                                  ");
        //$data['query'] = $this->db->last_query();

        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->GDLBNAME) . ' - ' . ucfirst($row->GDLBNAME) . ' - ' . $row->cnt,
                );
            }
        }
        echo json_encode($data);
    }
    //end select GDLB

    function getgdlbdetails() {
        $data = array();
        $html = '';
        $gdlbid = $this->input->post('gdlbid');
        $qry = $this->db->select('ad.names AS d, gm.limit')
            ->select("COUNT(m.servicenumber) AS cust", false)
            ->from('gdlb_main AS gm')
            ->join('address_districts AS ad', 'gm.d = ad.sysid')
            ->join('customer_accounts_main AS m', 'gm.sysid = m.gdlb', 'left')
            ->where('gm.sysid', $gdlbid)
            ->group_by('ad.names, gm.limit')
            ->get()->row();
        $dist = ($qry) ? $qry->d : '';
        $limit = ($qry) ? $qry->limit : '';
        $cust = ($qry) ? $qry->cust : '';

        $data['district'] = $dist;
        $data['limit'] = $limit;
        $data['cust'] = $cust;
        $data['html'] = $html;
        echo json_encode($data);
    }

    //start select RATE
    function select2rate() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('rate_class_specification')
            ->or_like('codes', $term)
            ->or_like('descs', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->codes) . ' - ' . ucfirst($row->descs),
                );
            }
        }
        echo json_encode($data);
    }
    //end select RATE

    //start select MULTCODE
    function select2multcode() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('billing_rates_main_multiplier')
            ->or_like('codes', $term)
            ->or_like('rate', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->codes) . ' - ' . ucfirst($row->rate),
                );
            }
        }
        echo json_encode($data);
    }
    //end select MULTCODE

    // start select2 payclass
    function select2payclass() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('prime_types_parameter')
            ->where(array('codes' => 'EMPAYCLASS'))
            ->or_like('names', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->names),
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }
    //end select 2 payclass

    // start select2 jobcat
    function select2jobcat() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('prime_types_parameter')
            ->or_like('names', $term)
            ->where('codes', 'EMPJOBCAT')
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->names),
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }
    function select2agency() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select("sysid,code,desc")->from('prime_data_agencies')
            ->or_like('desc', $term)
            ->or_like('code', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->code.' - '.$row->desc,
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }
    //end select 2 job cat
    function select2position() {
        $term = $this->input->post('term');
        $data['term'] = $term;
        $data = array();
        $qry = $this->db->select()->from('prime_types_parameter')
            ->or_like('names', $term)
            ->where(array("codes" => 'EMPOST' , 'status' => 1))
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->names),
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }
    //start for department select2
    function select2department() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('prime_costcenter_main')
            ->or_like('names', $term)
            ->or_like('desc', $term)
            ->get();
        $this->db->_error_message();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->names).' - '.ucfirst($row->desc),
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }
    //end for department select2

    //start for reporttype select2
    function select2reporttype() {
        $data = array();
        $qry = $this->db->select()->from('prime_types_parameter')
            ->where('codes','REPTYPE')
            ->get();
        $this->db->_error_message();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->names).' - '.ucfirst($row->desc),
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }
    //end for reporttype select2

    function select2test() {
        $term = $this->input->post('q');
        $data = array();
        $qry = $this->db->select()->from('prime_costcenter_main')
            ->like('codes', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['results'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->desc),
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }

    function getselect2brand() {
        $term = $this->input->post('q');
        $data = array();
        $qry = $this->db->select()->from('prime_items_brands')
            ->like('codes', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes . ' - '. ucfirst($row->descs),
                    //  'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }

    function barcode($text) {
        echo generate_barcode($text);
    }
    function copybilltrntobillmain() {
        echo $this->query->copy_billtrn_to_billmain();
    }
    function getexceldata()
    {
        $data = array();
        $data_arr = array();
        $ins_num = 0;
        $file = "uploads/account_sub.xls";
        $acctmainid = 85512;
        if(file_exists(FCPATH.$file)) {
            $file_type = PHPExcel_IOFactory::identify($file);
            $objReader = PHPExcel_IOFactory::createReader($file_type);
            $objPHPExcel = $objReader->load($file);
            $sheet_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            foreach ($sheet_data as $data) {
                $servno = trim($data['A']);
                $mtrno = trim($data['B']);
                $intread = trim($data['C']);
                $qry_acct = $this->db->select('sysid, mtrno')
                    ->from('customer_accounts_main')
                    ->where(array('servicenumber' => $servno, 'mtrno' => $mtrno))
                    ->get()->row();
                if($qry_acct) {
                    $acctsubid = $qry_acct->sysid;
                    $acctmtrno = $qry_acct->mtrno;
                    $ins_arr = array(
                        'acctmainid' => $acctmainid,
                        'acctid' => $acctsubid,
                        'mtrno' => $acctmtrno,
                        'intread' => $intread,
                        'createdby' => 1,
                        'updatedby' => 1
                    );
                    if($this->db->insert('customer_accounts_main_submatrix', $ins_arr)) {
                        $ins_num += 1;
                    }else {
                        echo $this->db->_error_message();
                    }
                }
            }
        }else{
            echo 'Error: File not found!';
        }
        echo 'Inserted: ' . $ins_num;
    }
    function getemployees($dept, $createaccount = 0, $roleid = 0) {
        $data = array();
        init_header_nonav($data);
        echo '<div class="container">';
        echo '<table class="table table-hover table-bordered table-advance table-condensed">';
        echo '<thead>';
        echo '<th>Emp ID</th>';
        echo '<th>Person ID</th>';
        echo '<th>Last Name</th>';
        echo '<th>First Name</th>';
        echo '<th>Dept Code</th>';
        echo '<th>User Account</th>';
        echo '</thead>';
        echo '<tbody>';
        $qry_emp = $this->db->query("SELECT
                e.sysid AS sysid,
                p.sysid AS personid,
                p.lastname,
                p.firstname,
                p.middlename,
                c.datecreated 
            FROM
                person AS p
                JOIN prime_employee_main AS e ON e.personid = p.sysid 
                AND e.STATUS = 1
                JOIN prime_employee_costcenter AS c ON c.empid = e.sysid 
                AND c.STATUS = 1 
            WHERE
                c.ccid = $dept AND c.status = 1 AND c.type = 1
            GROUP BY
                e.sysid,
                p.sysid,
                p.lastname,
                p.firstname,
                p.middlename,
                c.datecreated 
            ORDER BY
                c.datecreated DESC
                        
        ");
        if($qry_emp->num_rows()>0) {
            foreach($qry_emp->result() as $row) {
                $empinfo = get_employee_info($row->sysid);
                $emp_dept = ($empinfo) ? $empinfo->deptdesc : 'N/A';

                $get_userinfo = $this->db->select('username')
                    ->from('prime_system_users')
                    ->where(array('personid' => $row->personid))
                    ->get()->row();

                if($get_userinfo == false) {
                    $username = 'Free';
                }else{
                    if($get_userinfo) {
                        $username = $get_userinfo->username;
                    }else{
                        $username = '';
                    }
                }
                if($createaccount == 1 && $get_userinfo == false) {
                    $username = str_replace('ñ', 'n', strtolower(str_replace(' ', '', $row->firstname[0] . $row->lastname)));
                    $lastname = $row->lastname;
                    $firstname = $row->firstname;
                    $ins_user = array(
                        'username' => $username,
                        'password' => encrypt_pass($username),
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'personid' => $row->personid,
                    );
                    if($roleid > 1) {
                        $this->db->insert('prime_system_users', $ins_user);
                        $new_userid = $this->db->insert_id();

                        $role_ins = array(
                            'userid' => $new_userid,
                            'roleid' => $roleid,
                            'type' => 1,
                            'status' => 1
                        );
                        $this->db->insert('prime_system_users_roles_matrix', $role_ins);
                    }
                }
                echo '<tr>';
                echo '<td>'.$row->sysid.'</td>';
                echo '<td>'.$row->personid.'</td>';
                echo '<td>'.$row->lastname.'</td>';
                echo '<td>'.$row->firstname.'</td>';
                echo '<td>'.$emp_dept.'</td>';
                echo '<td>'.$username.'</td>';
                echo '</tr>';
            }
        }
        echo '<tbody>';
        echo '</table>';
        echo '</div>';

        init_footer_nonav($data);
    }
    function testsubmtr() {
        $test = get_submeter_total(85512, 2018, 9);
        echo '<pre>';
        print_r($test);
    }
    function get_mrd_calendar_dt() {
        $data = array();
        $html = '';
        $qry = false;
        $input_date_start = $this->input->post('datestart');
        $input_date_end = $this->input->post('dateend');

        $input_billmo = $this->input->post('billmo');
        $input_billyr = $this->input->post('billyr');

        if($input_date_start && $input_date_end) {
            $first_day_this_month = $input_date_start; // hard-coded '01' for first day
            $last_day_this_month  = $input_date_end;
        }else{
            $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
            $last_day_this_month  = date('Y-m-t');
        }


        $begin = new DateTime($first_day_this_month);
        $end = new DateTime($last_day_this_month);

        $end = $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $days = array();
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $days[] = $dt->format("Y-m-d");
        }

        $dates = array_reverse($days);
        $qry_meter_reader = $this->db->select('u.sysid, p.lastname')
            ->from('prime_system_users_roles_matrix AS rm')
            ->join('prime_system_users AS u', 'rm.userid = u.sysid AND u.status = 1')
            ->join('person AS p', 'p.sysid = u.personid')
            ->where(array('rm.roleid' => 22, 'rm.status' => 1))
            ->group_by('rm.userid')
            ->order_by('u.lastname')
            ->get();


        $cols_arr = array();
        $cols_arr[] = array('data' => 'name', 'text' => 'name', 'sClass' => 'zui-sticky-col');
        $cols_arr[] = array('data' => 'total', 'text' => 'total', 'sClass' => 'zui-sticky-col');
        $cd = 0;
        foreach ($dates as $dt) {

            $is_weekend_class = '';
            if(isWeekend($dt)) {
                $is_weekend_class = ' text-danger danger';
            }
            $is_weekend_class .= ' date-gdlb ';
            $nameOfDay = date('l', strtotime($dt));
            $th_html = '<span style="width:150px;display:inline-block">'.$dt.'<br><small>'.$nameOfDay.'</small></span>';
            $cols_arr[] = array(
                'data' => $cd++,
                'sClass' => $is_weekend_class,
                'text' => $th_html
            );
        }


        if($qry_meter_reader->num_rows()>0) {
            $i = 0;
            foreach ($qry_meter_reader->result() as $keys => $row) {

                $qry_total_assignments = $this->db->select('COUNT(sm.gdlbid) AS cnt')
                    ->from('reading_schedule_main AS sm')
                    ->join('reading_schedule_reader AS sr', 'sr.schedid = sm.sysid')
                    ->where(
                        array(
                            'sm.status != ' => 0,
                            'sr.userid' => $row->sysid,
                            'sm.months' => $input_billmo,
                            'sm.years' => $input_billyr,
                        )
                    )
                    ->get()
                    ->row();
                $total_gdlb = ($qry_total_assignments) ? $qry_total_assignments->cnt : 0;

                $data['list'][] = array(
                    'name' => '<div style="display: inline-block; width: 100%" class="'.$row->sysid.'">'.$row->lastname.'</div>',
                    'total' => $total_gdlb
                );

                // CALENDAR HORIZONTAL


                $len = count($dates);
                $ii = 0;
                foreach ($dates as $key => $dt) {
                    $is_weekend_class = '';
                    if(isWeekend($dt)) {
                        $is_weekend_class = ' text-danger danger';
                    }
                    $year_td = DateTime::createFromFormat("Y-m-d", $dt)->format("Y");
                    $month_td = DateTime::createFromFormat("Y-m-d", $dt)->format("m");
                    $day_td = DateTime::createFromFormat("Y-m-d", $dt)->format("d");



                    $qry_assignments = $this->db->select(
                        'sm.sysid, sm.gdlbid, COUNT(ml.acctid) AS cust, COUNT(mrl.acctid) AS readings'
                    )

                        ->select("CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb", false)
                        ->from('reading_schedule_main AS sm')
                        ->join('reading_schedule_reader AS sr', 'sr.schedid = sm.sysid')
                        ->join('reading_schedule_meters_logs AS ml', 'ml.schedid = sm.sysid')
                        ->join('customer_accounts_subscription_meter_reading_logs AS mrl', 'mrl.acctid = ml.acctid AND mrl.status = 1', 'left')
                        ->join('gdlb_main AS gm', 'gm.sysid = sm.gdlbid', 'left')
                        ->join('address_districts AS d', 'd.sysid = gm.d')
                        ->where(array(
                            'sm.status != ' => 0,
                            'sm.datesched' => $dt,
                            'sr.userid' => $row->sysid,
                            'sm.months' => $input_billmo,
                            'sm.years' => $input_billyr,
                        ))
                        ->group_by('sm.sysid, sm.gdlbid')
                        ->get();
                    $assignment_num = $qry_assignments->num_rows();

                    $dt_html = '';
                    $dt_html .= '<div id="cell_'.$row->sysid.'_'.$year_td.'_'.$month_td.'_'.$day_td.'">';
                    $dt_html .= '<a id="btn_edit" data-arr="'.$row->sysid.','.$input_billmo.','.$input_billyr.'" data-view="'.$dt.'" href="#form_gdlb_entry" title="'.$row->lastname.' | '.$dt.'" data-toggle="ajax-modal" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>';
                    if($assignment_num>0) {
                        $dt_html .= '<a id="btn_delete_all" data-sched="'.$year_td.'-'.$month_td.'-'.$day_td.'" data-id="' . $row->sysid . '" data-month="'.$input_billmo.'" data-year="'.$input_billyr.'" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                    }
                    $dt_html .= '<ul class="list-group summary column no-border list-group-xs" style="display: inline-block; width: 160px;">';

                    if($assignment_num>0) {
                        $dt_html .= '<li class="list-group-item">';
                        $dt_html .= '<span class="col-md-5 label-name  text-primary text-bold">GDLB</span>';
                        $dt_html .= '<span class="col-md-4 label-name  text-primary text-bold">Read</span>';
                        $dt_html .= '<span class="col-md-3 text-primary text-bold">Cust.</span>';
                        $dt_html .= '</li>';
                        foreach($qry_assignments->result() as $arow) {
                            $readings_class = 'text-danger';
                            $gdlb_bg = '';
                            $gdlb_txt = '';
                            $readings = $arow->readings;
                            $customers = $arow->cust;
                            if($readings>=$customers) {
                                $readings_class = '';
                                $gdlb_bg = 'bg-green-turquoise';
                                $gdlb_txt = 'bg-font-green-turquoise';
                            }


                            $dt_html .= '<li id="sched_'.$arow->sysid.'" class="list-group-item '.$gdlb_bg.'">';
                            $dt_html .= '<span class="col-md-5 label-name '.$gdlb_txt.'">';
                            $dt_html .= '<a class="'.$gdlb_txt.'" href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-arr="'.$arow->gdlbid.'" data-reader="">'.$arow->gdlb.'</a>';
                            $dt_html .= '</span>';

                            $dt_html .= '<span class="col-md-4 label-name '.$gdlb_txt.'"><span class="'.$readings_class.'">'.$readings.'</span></span>';
                            $dt_html .= '<span class="col-md-3 '.$gdlb_txt.'">';
                            $dt_html .= '<a href="javascript:;" class="tooltips '.$gdlb_txt.'" title="Reading status" data-placement="left">'.$customers.'</a>';
                            if($readings<=0) {
                                $dt_html .= '<a id="btn_delete" data-id="' . $arow->sysid . '" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>';
                            }
                            $dt_html .= '</span>';
                            $dt_html .= '</li>';
                        }
                    }

                    $dt_html .= '</ul>';
                    $dt_html .= '</div>';

                    $last_td_class = '';
                    if ($ii == 0) {
                        $last_td_class = 'info';
                    }

                    $td_cont = array(
                        $dt => $dt_html
                    );

                    array_push($data['list'][$keys], $td_cont[$dt]);



                    $html .= '<div id="cell_'.$row->sysid.'_'.$year_td.'_'.$month_td.'_'.$day_td.'">';
                    $html .= '<a id="btn_edit" data-arr="'.$row->sysid.','.$input_billmo.','.$input_billyr.'" data-view="'.$dt.'" href="#form_gdlb_entry" title="'.$row->lastname.' | '.$dt.'" data-toggle="ajax-modal" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>';
                    if($assignment_num>0) {
                        $html .= '<a id="btn_delete_all" data-sched="'.$year_td.'-'.$month_td.'-'.$day_td.'" data-id="' . $row->sysid . '" data-month="'.$input_billmo.'" data-year="'.$input_billyr.'" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                    }
                    $html .= '<ul class="list-group summary column no-border list-group-xs" style="display: inline-block; width: 160px;">';


                    if($assignment_num>0) {
                        $html .= '<li class="list-group-item">';
                        $html .= '<span class="col-md-5 label-name  text-primary text-bold">GDLB</span>';
                        $html .= '<span class="col-md-4 label-name  text-primary text-bold">Read</span>';
                        $html .= '<span class="col-md-3 text-primary text-bold">Cust.</span>';
                        $html .= '</li>';
                        foreach($qry_assignments->result() as $arow) {
                            $readings_class = 'text-danger';
                            $gdlb_bg = '';
                            $gdlb_txt = '';
                            $readings = $arow->readings;
                            $customers = $arow->cust;
                            if($readings>=$customers) {
                                $readings_class = '';
                                $gdlb_bg = 'bg-green-turquoise';
                                $gdlb_txt = 'bg-font-green-turquoise';
                            }


                            $html .= '<li id="sched_'.$arow->sysid.'" class="list-group-item '.$gdlb_bg.'">';
                            $html .= '<span class="col-md-5 label-name '.$gdlb_txt.'">';
                            $html .= '<a class="'.$gdlb_txt.'" href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-arr="'.$arow->gdlbid.'" data-reader="">'.$arow->gdlb.'</a>';
                            $html .= '</span>';

                            $html .= '<span class="col-md-4 label-name '.$gdlb_txt.'"><span class="'.$readings_class.'">'.$readings.'</span></span>';
                            $html .= '<span class="col-md-3 '.$gdlb_txt.'">';
                            $html .= '<a href="javascript:;" class="tooltips '.$gdlb_txt.'" title="Reading status" data-placement="left">'.$customers.'</a>';
                            if($readings<=0) {
                                $html .= '<a id="btn_delete" data-id="' . $arow->sysid . '" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>';
                            }
                            $html .= '</span>';
                            $html .= '</li>';
                        }
                    }
                    /*
                     *
                    $ii_cnt = rand(2,4);
                    for( $ii=1; $ii <= $ii_cnt; $ii++ ) {
                        $lot = rand(1,4);
                        $s = substr(str_shuffle(str_repeat("ASLDMJ", 5)), 0, 1);
                        $html .= '<li class="list-group-item">';
                        $html .= '<span class="col-md-6 label-name">';
                        $html .= '<a href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-id="'.$ii.'" data-reader="'.$row->sysid.'">'.$s.'-'.$lot.'-'.str_pad($ii, 2, '0', STR_PAD_LEFT).'</a>';
                        $html .= '</span>';
                        $html .= '<span class="col-md-6">';
                        $html .= '<a href="javascript:;" class="tooltips" title="Reading status" data-placement="left"><span class="text-danger">20</span>/200</a>';
                        $html .= '</span>';
                        $html .= '</li>';
                    }
                    */
                    $html .= '</ul>';
                    $html .= '</td>';

                    $ii++;
                }
                $html .= '</tr>';
                $i++;
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $data['html'] = $html;
        $data['columns'] = $cols_arr;
        return json_encode($data);
    }
    function select2brands(){
        $data = array();
        $sql = $this->db->select("sysid,codes,descs")
            ->from("prime_brands")
            ->where(array("status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->descs . ' - '.$row->codes
                );
            }
        }
        echo json_encode($data);
    }

    function select2pecoappsusers() {
        echo $this->query->select2_pecoapps_users();
    }

    function updatelegacyusername() {
        echo $this->query->update_legacy_username();
    }

    function insertpositionmatrix() {
        $pos_id = array(223, 224, 225, 226);

        foreach($pos_id as $row) {
            $this->db->insert('employee_position_division_matrix', array('posid' => $row, 'groupid' => 66));
        }
    }

    function additems() {
        echo $this->query->add_new_item();
    }

    function getunits() {
        echo $this->query->get_select2_units();
    }

    function gettrncomments() {
        echo $this->query->get_trn_comments();
    }
    function subtmitrncomment() {
        echo $this->query->submit_trn_comment();
    }
    function deletetrncomment() {
        echo $this->query->delete_trn_comment();
    }

    function uploadpp() {
        echo $this->query->upload_profile_picture();
    }

    function select2stages() {
        $data = array();
        $sql = $this->db->query("SELECT
                levels
                FROM prime_transaction_flow_main_stages
                GROUP BY levels");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $stages_ids = array();
                $sql_stages = $this->db->query("SELECT
                                                sysid, `desc`
                                                FROM prime_transaction_flow_main_stages
                                                WHERE flowid IN (2, 21, 22) AND levels = {$row->levels} AND status = 1
                                                ");

                if($sql_stages->num_rows()>0) {
                    foreach ($sql_stages->result() as $srow) {
                        $stages_ids[] = $srow->sysid;
                    }
                }
                $stageids = implode(',', $stages_ids);
                $data['list'][] = array(
                    'id' => $stageids,
                    'level' => $row->levels,
                );
            }
        }
        echo '<pre>';
        print_r($data);
    }


    function getdistrictcodes() {
        $id = $this->input->post('id');
        $codes = $this->db->select("codes")
            ->from("address_districts")
            ->where(array("sysid" => $id))
            ->get()->row();
        echo ($codes) ? $codes->codes : 'Unknown';
    }

    function getapplicationparamname() {
        $id = $this->input->post('id');
        $codes = $this->db->select("names")
            ->from("prime_types_parameter")
            ->where(array("sysid" => $id))
            ->get()->row();
        echo ($codes) ? $codes->names : 'Unknown';
    }

    function getrateclassname() {
        $id = $this->input->post('id');
        $codes = $this->db->select("names")
            ->from("prime_types_parameter")
            ->where(array("sysid" => $id))
            ->get()->row();
        echo ($codes) ? $codes->names : 'Unknown';
    }

    function sendbasicemail() {
        echo $this->query->send_basic_email();
    }

    function gettrnflowstages() {
        echo $this->query->get_transaction_flow_stages();
    }

    
    function select2country() {
        $term = $this->input->post('term');
        $data = array();
        $qry = $this->db->select()->from('address_country')
            ->or_like('country', $term)
            ->or_like('nationality', $term)
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ucfirst($row->country),
                    'flag' => $row->ccode,
                );
            }
        }
        echo json_encode($data);
    }

    function select2region() {
        echo $this->query->select2_region();
    }

    function select2province() {
        echo $this->query->select2_province();
    }

    function select2citymun() {
        echo $this->query->select2_citymun();
    }

}
