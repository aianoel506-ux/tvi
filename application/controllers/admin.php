<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Admin extends CI_Controller {

    private $user_login;

    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_auth');
        $this->user_login = $this->session->userdata('logged_in');

        // LOCK SCREEN
        if(check_user_lock()) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {
        if (user_id() && user_id() > 0) {
            return $this->dashboard();
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function checksession() {
        $data = array();
        $auth_login = array();
        $id = $this->input->post('userid');
        $segs = $this->input->post('segs');

        if(empty($this->session->userdata('logged_in'))) {
            $userid = false;
            $qry = false;
            $qry_users = $this->db->select()->from('prime_system_users')->where('sysid', $id)->get()->row();
            $sess_array = array(
                'system_user_sessid' => $this->model_auth->get_user_data($id)->sysid,
                'system_user_sessname' => $this->model_auth->get_user_data($id)->username,
                'system_user_sesstype' => $this->model_auth->get_user_data($id)->type,
                'system_user_sesslog' => 1,
            );
            $this->session->set_userdata('logged_in', $sess_array);

            $auth_lock = $this->model_auth->lock_user_log($id);
            $msg = $auth_lock['msg'];
            $seg = $segs;
        }else{
            $userid = user_id();
            $qry = true;
            $msg = '';
            $seg = '';
        }
        $data['seg'] = $seg;
        $data['msg'] = $msg;
        $data['input'] = $this->input->post();
        $data['uid'] = $userid;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    public function dashboard() {
        if ($this->user_login) {
            if( user_id() == 1 || in_array(1, user_info()->roles)) {
                $data['pagetitle'] = 'Dashboard';
                init_header($data);
                $this->load->view('admin/pages/activities');
                init_footer(false,false);
            }else{
                // GET USER DASHBOARD
                $data['pagetitle'] = 'Dashboard';
                init_header($data);
                $this->load->view('admin/dashboard/user_view', $data);
                init_footer(false,false);
                //redirect(base_url() . 'profile');
            }
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function error404() {
        if ($this->user_login) {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['usersmodule'] = $this->model_admin->select_modules();
            init_header($data);
            $this->load->view('admin/pages/page404', $data);
            init_footer($data, '');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function maintenance() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['usersmodule'] = $this->model_admin->select_modules();
            init_header($data);
            $this->load->view('admin/pages/maintenance', $data);
            init_footer($data, '');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function modules() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['usersmodule'] = $this->model_admin->select_modules();
            init_header($data);
            $this->load->view('admin/pages/modules', $data);
            init_footer($data, '');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function access() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['usersmodule'] = $this->model_admin->select_modules();
            init_header($data);
            $this->load->view('admin/pages/access', $data);
            init_footer($data, '');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function database() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['usersmodule'] = $this->model_admin->select_modules();
            init_header($data);
            $this->load->view('admin/pages/database', $data);
            init_footer($data, '');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function select2accttype() {
        echo selec2_accttype(array(1, 2, 3, 4));
    }

    public function get_rate_class_corp() {
        echo select2_rate_class(array(1, 2, 3, 4));
    }

    public function get_types($str = NULL) {
        echo get_item_type($str);
    }

    public function get_item_type_requirements($str, $statid = NULL, $locid = NULL) {
        echo get_item_type_requirements($str, $statid, $locid);
    }

    public function get_item_type_add_requirements($ignore = NULL) {
        echo get_item_type_add_requirements($ignore);
    }

    public function get_item_category() {
        echo get_item_category();
    }

    public function get_item_specification() {
        echo get_item_specification();
    }

    public function get_user_basic() {
        echo get_user_basic();
    }

    public function get_users() {
        echo get_users();
    }

    public function select2getusers() {
        echo $this->model_admin->get_users_select2();
    }

    public function select2getservices() {
        echo $this->model_admin->get_services_select2();
    }

    public function getuserrole() {
        echo get_user_role();
    }

    public function get_costcenter() {
        echo get_costcenter();
    }

    public function get_start_flow($moduleid) {
        print_r($this->model_admin->get_module_flow_start($moduleid));
    }

    function cleartrans() {
        $data = array();
        $msg = '';
        $func = 'error';
        $err = array();

        $flowid = 6;

        if($flowid==2) {
            $this->db->query("TRUNCATE TABLE application_customers_charges");
            $this->db->query("TRUNCATE TABLE application_customers_details");
            $this->db->query("TRUNCATE TABLE application_customers_equipments");
            $this->db->query("TRUNCATE TABLE application_customers_exemptions");
            $this->db->query("TRUNCATE TABLE application_customers_gdr_logs");
            $this->db->query("TRUNCATE TABLE application_customers_geodata");
            $this->db->query("TRUNCATE TABLE application_customers_near_meters");
            $this->db->query("TRUNCATE TABLE application_customers_requirements");
            $this->db->query("TRUNCATE TABLE application_customers_sequence");
            $this->db->query("TRUNCATE TABLE application_customers_subscriptions");
            $this->db->query("TRUNCATE TABLE application_encoding_stats");
            $this->db->query("TRUNCATE TABLE application_customer_inspection_logs");
            $this->db->query("TRUNCATE TABLE application_customers_referrals");
            $this->db->query("TRUNCATE TABLE application_customers_referrals_main");
            $this->db->query("TRUNCATE TABLE application_customers_referrals_person");
            $this->db->query("TRUNCATE TABLE application_customers_system_size");
            $this->db->query("TRUNCATE TABLE application_customers_team_assignment");
            $this->db->query("TRUNCATE TABLE application_customers_referrals_trn");
        }

        if($flowid==6 ){
            $this->db->query("TRUNCATE TABLE trn_request_orvoid");
        }

        if($flowid==7 ){
            $this->db->query("TRUNCATE TABLE trn_apprehensions");
            $this->db->query("TRUNCATE TABLE trn_apprehensions_logs");
            $this->db->query("TRUNCATE TABLE trn_apprehensions_logs_amts");
        }

        if($flowid==9 ){
            $this->db->query("TRUNCATE TABLE trn_employee_leave_requests");
            $this->db->query("TRUNCATE TABLE trn_employee_leave_requests_approval");
        }


        $err['TRUNC'][] = $this->db->_error_message();

        $qry_flows = $this->db->select()->from('transaction_request_main')
            ->where(array('flowid' => $flowid))->get();
        if($qry_flows->num_rows()>0) {
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
            $this->db->where('flowid', $flowid);
            $this->db->delete('transaction_request_main');
            $err['MAIN'][] = $this->db->_error_message();
        }
        $this->db->trans_commit();
        $msg = 'Transactions cleared!';
        $func = 'success!';

        $data['err'] = $err;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function events() {
        $data = array();
        $start_date = $this->input->post('start');
        $end_date = $this->input->post('end');
        $id = 0;

        // YEALRY HOLIDAY
        $qry_holiday = $this->db->select(
            '
                 ET.sysid AS TRNID,
                 ET.codes AS TITLES, 
                 ET.descs AS DESCS,
                 ET.links AS LINKS,
                 ET.datestart AS STARTS,
                 ET.dateend AS ENDS,
                 ET.allday AS ALLDAY,
                 ET.holiday AS HOLIDAY,
                 ET.repeats AS REPEATS,
                 ET.editable AS EDITABLE,
                 EM.textcolor AS TXTCOLOR,
                 EM.backgroundColor AS BGCOLOR,
                '
            )
            ->from('calendar_event_trn ET')
            ->join('calendar_event_main EM', 'EM.sysid = ET.eventid')
            ->where('ET.types', 1)
            ->get();
        if ($qry_holiday->num_rows() > 0) {
            foreach ($qry_holiday->result() as $row) {
                $id += 1;
                $data['list'][] = array(
                    'id' => $id.'-'.$row->TRNID.'-EVENTS',
                    'title' => $row->TITLES,
                    'description' => $row->DESCS,
                    'allDay' => boolval($row->ALLDAY),
                    'start' => $row->STARTS,
                    //'end' => $row->ENDS,
                    'url' => $row->LINKS,
                    'className' => 'pulsate',
                    'editable' => boolval($row->EDITABLE),
                    'startEditable' => boolval($row->EDITABLE),
                    'durationEditable' => boolval($row->EDITABLE),
                    'resourceEditable' => boolval($row->EDITABLE),
                    'backgroundColor' => $row->BGCOLOR,
                    'textColor' => $row->TXTCOLOR,
                    'repeat' => intval($row->REPEATS),
                    'holiday' => boolval($row->HOLIDAY),
                );
            }
        }

        // EVENTS
        $qry_holiday = $this->db->select(
            '
                 ET.sysid AS TRNID,
                 ET.codes AS TITLES, 
                 ET.descs AS DESCS,
                 ET.links AS LINKS,
                 ET.datestart AS STARTS,
                 ET.dateend AS ENDS,
                 ET.allday AS ALLDAY,
                 ET.holiday AS HOLIDAY,
                 ET.repeats AS REPEATS,
                 ET.editable AS EDITABLE,
                 ET.classname AS CLASSNAME,
                 ET.dow AS DOW,
                 EM.textColor AS TXTCOLOR,
                 EM.backgroundColor AS BGCOLOR,
                '
        )
            ->from('calendar_event_trn ET')
            ->join('calendar_event_main EM', 'EM.sysid = ET.eventid')
            ->where('ET.types', 2)
            ->get();
        if ($qry_holiday->num_rows() > 0) {
            foreach ($qry_holiday->result() as $row) {
                $id += 1;
                $data['list'][] = array(
                    'id' => $id.'-'.$row->TRNID.'-EVENTS',
                    'title' => $row->TITLES,
                    'description' => $row->DESCS,
                    'allDay' => boolval($row->ALLDAY),
                    'start' => $row->STARTS,
                    'end' => $row->ENDS,
                    'url' => $row->LINKS,
                    'className' => $row->CLASSNAME,
                    'editable' => boolval($row->EDITABLE),
                    'startEditable' => boolval($row->EDITABLE),
                    'durationEditable' => boolval($row->EDITABLE),
                    'resourceEditable' => boolval($row->EDITABLE),
                    'backgroundColor' => $row->BGCOLOR,
                    'textColor' => $row->TXTCOLOR,
                    'repeat' => intval($row->REPEATS),
                    'holiday' => boolval($row->HOLIDAY),
                    'dow' => $row->DOW,
                );
            }
        }


        // EMPLOYEE BIRTH DAY
        $qry_emp = $this->db->select('e.sysid, p.birthdate')
            ->select("CONCAT(p.firstname, ' ', p.lastname) AS name", false)
            ->from('prime_employee_main AS e')
            ->join('person AS p', 'p.sysid = e.personid')
            ->where(array('e.status' => 1))
            ->get();

        if ($qry_emp->num_rows() > 0) {
            foreach ($qry_emp->result() as $row) {
                $id += 1;

                $yearBegin = date("Y");
                $yearEnd = $yearBegin + 10; // edit for your needs
                $years = range($yearBegin, $yearEnd, 1);
                $birth_arr = explode('-', $row->birthdate);

                $birthDate = $row->birthdate;
                //explode the date to get month, day and year
                $birthDate = explode("-", $birthDate);
                //get age from date or birthdate
                $age = $age = date_diff(date_create($row->birthdate), date_create('now'))->y;

                $birth_arr1 = (isset($birth_arr[1])) ? $birth_arr[1] : 0;
                $birth_arr2 = (isset($birth_arr[2])) ? $birth_arr[2] : 0;
                $data['list'][] = array(
                    'id' => $id.'-'.$row->sysid.'-BDAY',
                    'title' => $row->name,
                    'description' => '<i class="fa fa-gift"></i> Birthday ' . $row->birthdate . ' Age: '.($age).'',
                    'allDay' => true,
                    'start' => $yearBegin . "-" . $birth_arr1 . "-" . $birth_arr2,
                    //'start' => $row->birthdate,
                    //'end' => $row->birthdate,
                    'url' => 'module/f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59/view/'.$row->sysid,
                    'className' => '',
                    'editable' => false,
                    'startEditable' => false,
                    'durationEditable' => false,
                    'resourceEditable' => false,
                    'backgroundColor' => '#FF56B6',
                    'textColor' => '#FFF',
                    'repeat' => 2,
                    'dow' => ''
                );
            }
        }

        $data['input'] = $this->input->post();
        echo json_encode($data);
    }

    // CAD MAINTENANCE
    function searchrequirements() {
        $term = $this->input->post('term');
        $q = $this->db->select('prp.sysid AS PRPSYSID, prp.names AS PRPNAMES, prp.desc AS PRPDESC')
            ->from('prime_requirement_parameters AS prp')
            ->group_by('prp.sysid')
            ->like('prp.names', $term)->get();
        $res_num = $q->num_rows();
        if($res_num>0) {
            foreach ($q->result() as $row) {
                $data['list'][] = array('id' => $row->PRPSYSID, 'text' => $row->PRPNAMES);
            }
        }else{
            $data['list'][] = array();
        }

        $data['res'] = $res_num;
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }

    function deletecadrequirements() {
        $id = $this->input->post('id');
        $this->db->trans_begin();
        $del_where = array(
            'sysid' => $id,
        );
        $this->db->where($del_where);
        $this->db->delete('requirements_parameters');
        if($this->db->trans_status()===true) {
            $qry = true;
            $func = 'success';
            $msg = 'Requirement Deleted!';
            $this->db->trans_commit();
        }else{
            $qry = false;
            $func = 'error';
            $msg = 'Requirement not deleted!';
            $this->db->trans_rollback();
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function addcadrequirements() {
        $loctype = $this->input->post('loctype');
        $ownertype = $this->input->post('ownertype');
        $statconn = $this->input->post('statconn');
        $reqid = $this->input->post('reqid');

        if($loctype && $ownertype && $statconn && $reqid) {
            $this->db->trans_begin();
            $ins_arr = array(
                'typeid' => $ownertype,
                'statusid' => $statconn,
                'locid' => $loctype,
                'reqid' => $reqid,
            );
            $this->db->insert('requirements_parameters', $ins_arr);
            if ($this->db->trans_status() === true) {
                $qry = true;
                $func = 'success';
                $msg = 'New Requirements Added!';
                $this->db->trans_commit();
            } else {
                $qry = false;
                $func = 'error';
                $msg = 'Requirement not added!';
                $this->db->trans_rollback();
            }
        }else{
            $qry = false;
            $func = 'warning';
            $msg = 'Please fill all the field required!';
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function getcadrequirements() {
        $data = array();

        $loctype = $this->input->post('loctype');
        $ownertype = $this->input->post('ownertype');
        $statconn = $this->input->post('statconn');

        $qry = $this->db->select('p.sysid, r.codes, r.names')
            ->from('requirements_parameters AS p')
            ->join('prime_requirement_parameters AS r', 'r.sysid = p.reqid')
            ->where(
                array(
                    'typeid' => $ownertype,
                    'statusid' => $statconn,
                    'locid' => $loctype,
                )
            )->get();

        if($qry->num_rows()>0) {
            $num = 1;
            foreach($qry->result() as $row) {
                $data['list'][] = array(
                    'num' => $num++,
                    'code' => $row->codes,
                    'desc' => $row->names,
                    'control' => '<a id="del_btn" href="javascript:;" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>',
                );
            }
        }

        $data['input'] = $this->input->post();
        echo json_encode($data);
    }

    public function selectdistrict() {
        $qry = select_district();
        $data = array();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data[] = array('id' => $row->sysid, 'text' => $row->names);
            }
        }
        echo json_encode($data);
    }

    // TO BE UPDATE
    public function getlotbooktest($d) {
        $d_arr = explode(',', $d);
        if (in_array(1, $d_arr)) {
            $data = array(
                array('id' => 1, 'text' => 'M-1-01'),
                array('id' => 2, 'text' => 'M-1-02'),
                array('id' => 3, 'text' => 'M-1-03'),
            );
        } else {
            $data = array(
                array('id' => 1, 'text' => 'J-1-01'),
                array('id' => 2, 'text' => 'J-1-02'),
                array('id' => 3, 'text' => 'J-1-03'),
            );
        }

        echo json_encode($data);
    }

    // SAMPLES //



    public function pages_month_hitpoints() {
        return $this->model_pages->get_page_visit_month();
    }

    public function month_hitpoints() {
        return $this->model_dashboard->count_hit_month();
    }

    public function getcostcenter() {

        $data = array(
            array('id' => 1, 'name' => 'LUCKY'),
            array('id' => 2, 'name' => 'JOHN'),
            array('id' => 3, 'name' => 'FADERON'),
        );
        echo json_encode($data);
    }

    public function samplenearmeter() {

        $data = array(
            array('id' => 1, 'text' => '352225'),
            array('id' => 2, 'text' => '556633'),
            array('id' => 3, 'text' => '855688'),
            array('id' => 4, 'text' => '255556'),
        );
        echo json_encode($data);
    }

    public function sample_flot() {

        $data['example'] = array(
            array('01/2015', 4000000),
            array('02/2015', 8000000),
            array('03/2015', 1000000),
            array('04/2015', 1900000),
            array('05/2015', 2000000),
            array('06/2015', 3000000),
            array('07/2015', 1200000)
        );
        echo json_encode($data);
    }

    public function sample_calendar() {
        $data['events'] = array(
            array('title' => 'Long Event',
                'start' => '2015-02-12 21:32:12',
                'end' => '2015-02-12 21:32:12',
                'allDay' => false,
                'backgroundColor' => '#cccccc'
            ),
            array('title' => 'Long Event',
                'start' => '2015-02-12 21:32:12',
                'end' => '2015-02-12 21:32:12',
                'allDay' => false,
                'backgroundColor' => '#cccccc'
            ),
        );
        echo json_encode($data);
    }

    function check_session() {
        echo user_session()->system_user_sessid;
    }

    function generatehash() {
        $data = array();
        $this->load->view('admin/common/head');
        $this->load->view('admin/common/genhash');
        $this->load->view('admin/common/scripts');
    }

    function gensha() {
        $sha = ($this->input->post('str')) ? sha1($this->input->post('str')) : '';
        $data['sha'] = $sha;
        echo json_encode($data);
    }

    function test() {
        echo '<h1>TEST</h1>';
        $datenow = $this->db->query('SELECT NOW() AS NOW')->row();
        $seconds  = strtotime($datenow->NOW) - strtotime('2016-11-07 13:00:10');

        $months = floor($seconds / (3600*24*30));
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);

        if($seconds < 60)
            $time = $secs." seconds ago";
        else if($seconds < 60*60 )
            $time = $mins." min ago";
        else if($seconds < 24*60*60)
            $time = $hours." hours ago";
        else if($seconds < 24*60*60)
            $time = $day." day ago";
        else
            $time = $months." month ago";

        echo $time;

    }

    function testprint() {
        $this->load->view('admin/common/head');
        $data['dataid'] = $this->input->post('dataid');
        $this->load->view('admin/pages/testprint', $data);
    }

    function mtrprintlist() {
        $this->load->view('admin/common/head');
        $this->load->view('admin/pages/mtrprintlist');
    }



    function testing1() {
        $trnid = 2;
        $qry = $this->db->select()->from('transaction_request_main_trails')->where('trnid', $trnid)->order_by('datecreated', 'asc')->get();
        $num_rows = $qry->num_rows();
        $time_details = array();
        $data = array();
        if ($num_rows > 0) {
            $i = 0;
            foreach ($qry->result() as $row) {
                $ii = $i++;
                $qry_stages = $this->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $row->stageid)->get()->row();
                $data['data'][] = array('num' => $ii, 'lastupd' => $qry_stages->desc, 'details' => '', 'trn' => '', 'createdby' => get_users_info($row->createdby)->firstname . ' ' . get_users_info($row->createdby)->lastname, 'date' => $row->datecreated);
                $time_details[] = array("sysid" => $ii, "date" => $row->datecreated);
            }
        }
        echo '<pre>';
        print_r($time_details);

        $time_arr = array(
            array("sysid" => 1, "date" => "2016-11-05 08:55:03"),
            array("sysid" => 2, "date" => "2016-11-09 09:00:33"),
            array("sysid" => 3, "date" => "2016-11-09 09:01:11"),
            array("sysid" => 4, "date" => "2016-11-09 09:01:30"),
            array("sysid" => 5, "date" => "2016-11-09 13:02:44"),
            array("sysid" => 6, "date" => "2016-11-09 14:18:55")
        );
        echo sum_time($time_details);

        print_r($time_arr);
    }


    function checkdisck() {
        $Bytes = disk_free_space("/");
        $Total = disk_total_space("/");

        // TOTAL SPACE
        $Type1 = array(
            array("", ""),
            array("red", "KB"),
            array("orange", "MB"),
            array("blue", "GB"),
            array("green", "TB"),
            array("orange", "PB"),
            array("orange", "EB"),
            array("orange", "ZB"),
            array("orange", "YB")
        );
        $Index1 = 0;
        while ($Total >= 1024) {
            $Total/=1024;
            $Index1++;
        }


        // FREE SPACE
        $Type = array(
            array("", ""),
            array("red", "KB"),
            array("orange", "MB"),
            array("blue", "GB"),
            array("green", "TB"),
            array("orange", "PB"),
            array("orange", "EB"),
            array("orange", "ZB"),
            array("orange", "YB")
        );
        $Index = 0;
        while ($Bytes >= 1024) {
            $Bytes/=1024;
            $Index++;
        }

        $data['total'] = number_format($Total, 2);
        $data['color'] = $Type[$Index][0];
        $data['size'] = number_format($Bytes, 2);
        $data['name'] = $Type[$Index][1];
        echo json_encode($data);
    }

    function validateusername() {
        $data = array();
        $msg = '';
        $q = false;
        $username = $this->input->post('username');
        if($username) {
            $qry = $this->db->select()->from('prime_system_users')->where(array('username' => $username))->get()->row();
            if ($qry) {
                if ($qry->status == 1) {
                    $q = true;
                } else {
                    $msg = 'Account is in-active!';
                }
            } else {
                $msg = 'Username is not found!';
            }
        }else{
            $msg = 'Username is blank!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $q;
        echo json_encode($data);
    }

    function validatelogin() {
        $data = array();
        $msg = '';
        $q = false;
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $qry = $this->db->select()->from('prime_system_users')->where(array('username' => $username))->get()->row();
        if (hashvalidate($password, $qry->password)) {
            $q = true;
        }else{
            $msg = 'Wrong password';
        }
        $data['msg'] = $msg;
        $data['qry'] = $q;
        echo json_encode($data);
    }

    function lock() {
        // init_header();
        $sess_array = array(
            'system_user_sessid' => user_id(),
            'system_user_sessname' => 'admin',
            'system_user_sesstype' => 2,
            'system_user_sesslog' => 1,
        );

        $this->session->set_userdata('logged_in', $sess_array);

        print_r($this->session->userdata('logged_in'));
        // init_footer();
    }

    function uploadpics(){
        $data = array();
        $qry = false;
        $msg = '';
        $hascontract = false;

        $this->load->helper('directory');
        $this->load->library('upload');

        if(isset($_FILES["reqfiledrop"])) {
            $dataid = $this->input->post('dataid');
            $stageid = $this->input->post('stageid');
            $filetype = $this->input->post('filetype');

            $new_name = $_FILES["reqfiledrop"]['name'];
            $data['newname'] = $new_name;
            $uploaddata = array();

            $type_name = ($filetype && trim($filetype) != '') ? '_TYPE-'. $filetype : '';

            //$filenamexplode = explode("." , $new_name);
            $filenamexplode = pathinfo($new_name);
            $appinfo = get_application_details($dataid)->info;
            $name = str_replace(' ','_',$appinfo->firstname) . '_' . str_replace(' ','_',$appinfo->lastname);
            // $name = str_pad(rand(0,9999), 6, '0', STR_PAD_LEFT);
            //$filename = str_replace('.'.$filenamexplode['extension'],'',$filenamexplode['basename']);
            $filename = $filenamexplode['filename'];
            $isreq = strpos($filename,'REQ');
            //$file_req = explode('_', $filename);
            //$reqcode = $file_req[0];

            $iscontract = strpos($new_name,'CONTRACT');
            if ($iscontract !== false) {
                $signedby = (in_array($stageid,array(6,73,84))) ? 'AM' : 'Consumer';
                $new_name = 'CONTRACT_' . $name . '_' . $signedby . '_Signed.' . strtolower($filenamexplode['extension']);
            } else {
                if ($isreq !== false) {
                    $reqcode = substr($filename,0,6);
                    $req = $this->db->select()
                        ->from('prime_requirement_parameters')
                        ->where(array('codes' => $reqcode,'status' => 1))
                        ->get()->row();

                    if ($req) {
                        $newfilename = $req->codes.' '.$appinfo->essrno.' '.$req->shortname;
                        $new_name = $newfilename . '.' . strtolower($filenamexplode['extension']);
                    }
                } else {
                    //$newfilename = $reqcode.'_'.$filename.'_'.$name;
                    $newfilename = $filename . '_' . $name;
                    $new_name = $newfilename . $type_name . '.' . strtolower($filenamexplode['extension']);
                }
            }
            $data['filename'] = $new_name;

            $location = ($isreq !== false || $iscontract !== false) ? 'requirements' : 'installation';

            $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/".$location;

            //  $file_directory = "net use z:\\\\172.20.224.15cad\\attachedments\\" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";

            // ###############################################
            // CREATE DIRECTORY
            $config['upload_path'] = $file_directory;
            $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|jpeg';
            $config['max_size'] = 100000;
            $config['max_width'] = 5000;
            $config['max_height'] = 8000;
            $config['encrypt_name'] = FALSE;
            //$config['file_name'] = str_replace(' ', '_', trim($new_name));
            $config['file_name'] = $new_name;

            $this->upload->initialize($config);

            // ###############################################
            // CREATE DIRECTORY

            if (!is_dir($file_directory)) {
                mkdir($file_directory, 0777, TRUE);
                chmod($file_directory, 0777);
            } else {
                chmod($file_directory, 0777);
            }

            // ###############################################

            if (!$this->upload->do_upload('reqfiledrop')) {
                $msg = array('error' => $this->upload->display_errors() .  ' ' .$file_directory) ;
            } else {
                $uploaddata = $this->upload->data();
                $msg = array('upload_data' => $uploaddata);
                $qry = true;
                //Auto-assign if uploaded file is contract
                $folder = "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";

                $attachment_id = $this->db->select('acr.sysid')
                    ->from('application_customers_requirements AS acr')
                    ->join('prime_requirement_parameters AS prp', 'acr.reqid = prp.sysid', 'inner')
                    ->join('application_customers_details AS acd', 'acr.appid = acd.sysid', 'inner')
                    ->where(array('prp.codes' => $reqcode, 'acd.sysid' => $dataid))->get()->row();

                if ($attachment_id) {
                    $attid = $attachment_id->sysid;
                } else {
                    if ($iscontract !== false) {
                        $req_cont = array(
                            'appid' => $dataid,
                            'reqid' => 500,
                        );
                        $this->db->insert('application_customers_requirements', $req_cont);
                        $attid = $this->db->insert_id();
                    }
                }

                if (isset($attid)) {
                    $this->db->update(
                        'application_customers_attachments',
                        array('status' => 0, 'updatedby' => user_id()),
                        array('attachmentid' => $attid)
                    );

                    $insarr = array(
                        'attachmentid' => $attid,
                        'fileurl' => $folder . $uploaddata['file_name'],
                        'complydate' => date('Y-m-d h:i:s'),
                        'complyby' => user_id(),
                        'createdby' => user_id()
                    );
                    $this->db->insert("application_customers_attachments", $insarr);
                    //$data['attachments'][] = $this->db->last_query();
                    if ($this->db->insert_id()) {
                        $this->db->update(
                            "application_customers_requirements",
                            array('comply' => 1),
                            array("comply" => 0, "sysid" => $attid)
                        );
                        //$data['requirements'][] = $this->db->last_query();
                    }

                }
            }
        }else{
            $msg = 'Drop the file again!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['contract'] = $hascontract;
        echo json_encode($data);

    }

    function fetchfiletype() {
        $dataid = $this->input->post('dataid');
        $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/installation/";
        $file_url = base_url() . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/installation/";

        $total_cnt = 0;
        $html = '';
        $sql_file_types = $this->db->select()->from('prime_types_parameter')->where(array('codes' => 'INSFILETYPES'))->get();
        if($sql_file_types->num_rows()>0) {
            foreach ($sql_file_types->result() as $row) {
                $row_html = '';
                $row_file_html = '';
                $file_num = 0;
                foreach(glob($file_directory. '*'.$row->sysid.'.*') as $file) {


                    $file_arr = explode('/', $file);
                    $file_name = end($file_arr);
                    $file_ext = explode('.', $file_name);

                    $file_size = 'Size: ' . round((filesize($file) / 1000), 2) . ' Kb';
                    $btn_delete = '';
                    if(super_admin() ) {
                        $btn_delete = '<li>
                                <a class="btn default btn-outline btn_delete" href="#" data-file="'.$file_url.$file.'">
                                    <i class="icon-trash"></i>
                                </a>
                            </li>';
                    }



                    if(@is_array(getimagesize($file_url.$file_name))){
                        $row_file_html .= '
                    <div class="col-md-3">
                        <div class="mt-card-item">
                            <div class="mt-card-avatar mt-overlay-1">
                                <img alt="'.$file_name.'" src="' . $file_url . $file_name . '"/>
                                <div class="mt-overlay">
                                    <ul class="mt-info">
                                        <li>
                                            <a class="btn default btn-outline preview" href="'.$file_url . $file_name.'">
                                                <i class="icon-magnifier"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a target="_blank" class="btn default btn-outline" href="javascript:;">
                                                <i class="icon-link"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-card-content">
                                <span class="mt-card-name small">' . basename($file) . '</span>
                                <!-- <p class="mt-card-desc font-grey-mint">'.$file_size.'</p> -->
                            </div>
                        </div>
                    </div>
                ';
                    } else {
                        $file_specs = draw_file_icon(basename($file));
                        $file_icon = $file_specs->icon;
                        $file_color = $file_specs->color;
                        $row_file_html .= '<div class="col-md-3">
                            <div class="mt-card-item">
                                <div class="mt-card-avatar mt-overlay-1" style="min-height: 170px;">
                                    <i style="margin-top: 60px;" class="fa '.$file_icon.' fa-5x '.$file_color.'"></i>
                                    <div class="mt-overlay">
                                        <ul class="mt-info">
                                            <li>
                                                <a target="_blank" class="btn default btn-outline" href="'.$file_url.$file_name.'">
                                                    <i class="icon-link"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-card-content">
                                    <span class="mt-card-name small">' . basename($file) . '</span>
                                    <!-- <p class="mt-card-desc font-grey-mint">'.$file_size.'</p> -->
                                </div>
                            </div>
                        </div> ';
                    }

                    $file_num++;
                }
                if($file_num>0) {
                    $row_html .= '<div class="row">';
                    $row_html .= '<div class="col-md-12">';
                    $row_html .= '<h3 style="margin: 5px 0px;">'.$row->names.'</h3>';
                    $row_html .= '<div class="well" style="display: inline-block; padding: 10px 10px; margin-bottom: 10px; width: 100%; min-height: 100px; border: 4px dashed #ccc; text-align: left;">';
                    $row_html .= $row_file_html;
                    $row_html .= '</div>';
                    $row_html .= '</div>';
                    $row_html .= '</div>';
                    $total_cnt += $file_num;
                }

                $html .= $row_html;

            }
        }

        if($total_cnt>0) {

            $html .= '</div>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">';
            $html .= '<div class="alert alert-info" style="margin-top: 0px; margin-bottom: 0px;">
                        <strong>Download</strong> all attachment as zip.
                        <a href="' . base_url('admin/downloadallfiles/') . $dataid . '" class="alert-link"> Click Here </a>
                  </div>';
            $html .= '</div>';
            $html .= '</div>';
        }else{
            $html .= '<h4><i class="fa fa-warning text-warning"></i> No file uploaded yet!</h4>';
        }


        $data['dataid'] = $dataid;
        $data['html'] = $html;
        echo json_encode($data);
    }

    function deleteallfiles() {
        $dataid = $this->input->post('dataid');
        $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";
        $files = glob($file_directory . '*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    function fetchcadpictures(){
        $data = array();
        $folderloc = $this->input->post('location');
        $dataid = $this->input->post('dataid');
        $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/".$folderloc."/";
        $file_url = base_url() . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/".$folderloc."/";

        $html = '';
        $html .= '<div class="row">';
        $map = directory_map($file_directory, FALSE, TRUE);
        if ($map && count($map) > 0) {

            $data['map'] = $map;
            foreach($map as $sub => $file) {
                if (is_array($file)) {
                    $subdirectory = $file_directory.$sub.'/';
                    $suburl = $file_url.$sub.'/';

                    foreach ($file as $subfile) {
                        $file_size = 'Size: ' . round((filesize($subdirectory . $subfile) / 1000), 2) . ' Kb';

                        $btn_delete = '<li>
                                <a class="btn default btn-outline btn_delete" href="#" data-file="' . $suburl . $subfile . '">
                                    <i class="icon-trash"></i>
                                </a>
                            </li>';

                        if (@is_array(getimagesize($suburl . $subfile))) {
                            $html .= '
                    <div class="col-md-3">
                        <div class="mt-card-item">
                            <div class="mt-card-avatar mt-overlay-1">
                                <img src="' . $suburl . $subfile . '">
                                <div class="mt-overlay">
                                    <ul class="mt-info">
                                        <li>
                                            <a class="btn default btn-outline preview" href="' . $suburl . $subfile . '">
                                                <i class="icon-magnifier"></i>
                                            </a>
                                        </li>
                                        ' . $btn_delete . '
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-card-content">
                                <h3 class="mt-card-name">' . basename($subfile) . '</h3>
                                <p class="mt-card-desc font-grey-mint">' . $file_size . '</p>
                            </div>
                        </div>
                    </div>
                ';
                        } else {
                            $file_specs = draw_file_icon(basename($subfile));
                            $file_icon = $file_specs->icon;
                            $file_color = $file_specs->color;
                            $preview = '<a href="' . $suburl . $subfile . '" class="btn default btn-outline cbp-caption cbp-lightbox iframe" target="_blank" data-title="Bolt UI<br>by Tiberiu Neamu"><i class="icon-magnifier"></i></a>';
                            $html .= '<div class="col-md-3">
                            <div class="mt-card-item">
                                <div class="mt-card-avatar mt-overlay-1 flex-row" style="max-height: 170px;">
                                    <i style="margin-top: 60px;" class="fa ' . $file_icon . ' fa-5x ' . $file_color . '"></i>
                                    <div class="mt-overlay">
                                        <ul class="mt-info">
                                            <li>
                                                ' . $preview . '
                                            </li>
                                            ' . $btn_delete . '
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-card-content">
                                    <h3 class="mt-card-name">' . basename($subfile) . '</h3>
                                    <p class="mt-card-desc font-grey-mint">' . $file_size . '</p>
                                </div>
                            </div>
                        </div> ';
                        }
                    }
                } else {
                    $file_size = 'Size: ' . round((filesize($file_directory . $file) / 1000), 2) . ' Kb';

                    $btn_delete = '<li>
                                <a class="btn default btn-outline btn_delete" href="#" data-file="' . $file_url . $file . '">
                                    <i class="icon-trash"></i>
                                </a>
                            </li>';

                    if (@is_array(getimagesize($file_url . $file))) {
                        $html .= '
                    <div class="col-md-3">
                        <div class="mt-card-item">
                            <div class="mt-card-avatar mt-overlay-1">
                                <img src="' . $file_url . $file . '">
                                <div class="mt-overlay">
                                    <ul class="mt-info">
                                        <li>
                                            <a class="btn default btn-outline preview" href="' . $file_url . $file . '">
                                                <i class="icon-magnifier"></i>
                                            </a>
                                        </li>
                                        ' . $btn_delete . '
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-card-content">
                                <h3 class="mt-card-name">' . basename($file) . '</h3>
                                <p class="mt-card-desc font-grey-mint">' . $file_size . '</p>
                            </div>
                        </div>
                    </div>
                ';
                    } else {
                        $file_specs = draw_file_icon(basename($file));
                        $file_icon = $file_specs->icon;
                        $file_color = $file_specs->color;
                        $preview = '<a href="' . $file_url . $file . '" class="btn default btn-outline cbp-caption cbp-lightbox iframe" data-title="Bolt UI<br>by Tiberiu Neamu"><i class="icon-magnifier"></i></a>';
                        $html .= '<div class="col-md-3">
                            <div class="mt-card-item">
                                <div class="mt-card-avatar mt-overlay-1 flex-row" style="max-height: 170px;">
                                    <i style="margin-top: 60px;" class="fa ' . $file_icon . ' fa-5x ' . $file_color . '"></i>
                                    <div class="mt-overlay">
                                        <ul class="mt-info">
                                            <li>
                                                <!--<a class="btn default btn-outline" href="' . $file_url . $file . '" target="_blank">
                                                    <i class="icon-link"></i>
                                                </a>-->
                                                ' . $preview . '
                                            </li>
                                            ' . $btn_delete . '
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-card-content">
                                    <h3 class="mt-card-name">' . basename($file) . '</h3>
                                    <p class="mt-card-desc font-grey-mint">' . $file_size . '</p>
                                </div>
                            </div>
                        </div> ';
                    }
                }
            }
            $html .= '</div>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">';
            $html .= '<div class="alert alert-info" style="margin-top: 0px; margin-bottom: 0px;">
                        <strong>Download</strong> all attachment as zip.
                        <a href="'.base_url('admin/downloadallfiles/').$dataid.'" class="alert-link"> Click Here </a>
                  </div>';
            $html .= '</div>';
        } else {
            $html .= '<h3><i class="fa fa-warning text-warning"></i> No file uploaded yet!</h3>';
        }
        $html .= '</div>';
        $data['dataid'] = $dataid;
        $data['htmllayout'] = $html;
        echo json_encode($data);
    }

    function downloadallfiles($id = false) {
        if($id) {
            $this->load->library('zip');
            $upload_path = "uploads/attachments/cad/applications/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/";
            $file_directory = FCPATH . $upload_path;
            $file_url = base_url() . $upload_path;

            $this->zip->compression_level = 2;
            $files = glob($file_directory .'*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF,pdf,doc,docx,xls,xlsx}', GLOB_BRACE);
            foreach($files as $file) {
                $this->zip->read_file($file);
            }

            $appinfo = get_application_details($id)->info;
            $name = str_replace(' ','_',$appinfo->firstname) . '_' . str_replace(' ','_',$appinfo->lastname);
            $filename = $name."_Attachments_".time().".zip";
            $this->zip->archive($file_url.$filename);
            $this->zip->download($filename);
        }
        echo "<script>window.close();</script>";
    }

    function deletefile() {
        $data = array();
        $file = $this->input->post('file');
        $qry = false;
        $msg = '';
        $func = 'error';
        $title = '';

        $file = str_replace(base_url(),FCPATH,$file);

        chmod($file,0777);

        if (unlink($file)) {
            $qry = true;
            $msg = 'File has been deleted.';
            $func = 'success';
            $title = 'Deleted!';
        } else {
            $qry = false;
            $msg = 'Failed to delete selected file.';
            $func = 'error';
            $title = 'Failed';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['title'] = $title;

        echo json_encode($data);
    }

    function updatecustomerrequirements(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $url = $this->input->post('url');
        $id = $this->input->post('id');
        $dataupdate = array(
            'fileurl' => $url,
            'complyby' => $dataid,
            'comply' => 1,
            'complydate' => date("Y-m-d H:i:s")
        );
        $this->db->where('sysid', $id);
        $this->db->update("application_customers_requirements" , $dataupdate);
        $data['id'] = $id;
        $data['url'] = $url;
        echo json_encode($data);
    }
    function populaterequirementslist(){
        echo $this->model_admin->populate_requirement_list();
    }


    function computenetpay() {
        $data = array();
        $data['compute'] = array('alert' => 'Process...');
        init_header($data);
        $this->load->view('user/computenetpay', $data);
        init_footer($data);
    }

    function computenetpayprocess($empid = false, $year = false, $month = false, $paytype = false) {
        if($empid == false || $year== false || $month== false || $paytype == false) {
            $empid      = $this->input->post('empid');
            $month      = $this->input->post('month');
            $year       = $this->input->post('year');
            $paytype    = $this->input->post('paytype');
            $payclass   = $this->input->post('payclass');
        }
        $emp_arr = explode(',', $empid);
        $data['inputs'] = $this->input->post();
        if(count($emp_arr) > 1) {
            foreach($emp_arr as $row) {
                $deduct_arr[] = compute_employee_netpay($row, $month, $year, $paytype, 1, $payclass);
            }
        }else{
            $deduct_arr[] = compute_employee_netpay($empid, $month, $year, $paytype, 1, $payclass);
        }
        $data['compute'] = $deduct_arr;
        echo print_r($data);
    }
    function testing() {
        $deduct_arr = get_employee_transactions(260, 3, 2018, 1, 1, 128);
        echo '<pre>';
        print_r($deduct_arr);

    }

    function paymentstrans() {
        $data = array();
        $userid = $this->input->post('userid');
        $this->db->trans_begin();

        $tables = array(
            'transaction_payments_logs',
            'billing_payapplied',
            'trn_transactions_validation'
        );


        $this->db->where(array('createdby' => $userid));
        $this->db->delete($tables);
        $err[] = $this->db->_error_message();

        if($this->db->trans_status() == TRUE) {
            $this->db->trans_commit();
            $data['qry'] = true;
            $data['err'] = $err;
        } else {
            $this->db->trans_rollback();
            $data['qry'] = false;
            $data['err'] = $err;
        }

        echo json_encode($data);
    }

    function clearapprehension() {
        $data = array();
        $userid = $this->input->post('userid');
        $this->db->trans_begin();

        // ADMIN AND DEV ONLY
        $this->db->trans_begin();
        $this->db->query("TRUNCATE TABLE trn_apprehensions;");
        $this->db->query("TRUNCATE TABLE trn_apprehensions_logs;");
        $this->db->query("TRUNCATE TABLE trn_apprehensions_logs_amts;");

        $stages = $this->db->select('sysid')->from('prime_transaction_flow_main_stages')
            ->where('flowid', 7)
            ->get();

        $this->db->delete('transaction_request_main', array('flowid' => 7));
        if($stages->num_rows()>0) {
            foreach($stages->result() as $row) {
                $this->db->delete('transaction_request_main_trails', array('sysid' => $row->sysid));
                $this->db->delete('transaction_request_trails_logs', array('trailid' => $row->sysid));
            }
        }
        if($this->db->trans_status()===TRUE) {
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }

        echo json_encode($data);
    }


    function testrole() {
        print_r( get_users_roles_matrix_id_arr() );
    }

    function testbill() {
        print_r(compute_billing(74779, 2018, 6, 100, 0, 0));
    }
    function savemanualearnings(){
        echo $this->model_admin->save_manual_earnings();
    }
    function getpaytype(){
        $data = array();
        $typearr = array(
            '1' => '1st Half',
            '2' => '2nd Half',
            '3' => 'Specific'
        );
        foreach ($typearr  as $key => $value) {
            $data['list'][] = array(
                'id' => $key,
                'text' => $value
            );
        }
        echo json_encode($data);
    }
    function getquarter(){
        $data = array();
        $typearr = array(
            '1' => '1st Quarter',
            '2' => '2nd Quarter',
            '3' => '3rd Quarter',
            '4' => '4th Quarter'
        );
        foreach ($typearr  as $key => $value) {
            $data['list'][] = array(
                'id' => $key,
                'text' => $value
            );
        }

        echo json_encode($data);
    }
    function submitpstransactions(){
        $data = array();
        $typesid = $this->input->post('typesid');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $paytype = $this->input->post('paytype');

        $this->db->trans_begin();

        $updatearr = array(
            'status' => 305,
            'updatedby' => user_id()
        );
        $this->db->where(array("status" => 307, "typesid" => $typesid , "month" => $month , "year" => $year));
        $this->db->update("payroll_manual_earnings" , $updatearr);
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Transaction has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save transaction.';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        $data['typesid'] = $typesid;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['paytype'] = $paytype;

        echo json_encode($data);
    }


    // ####################################################
    // ####################################################
    // CUSTOM CODES
    function getps() {
        init_header_nonav(false);
        $this->load->view('custom/getps');
        init_footer_nonav(false);
    }

    function qryps() {
        $data = array();


        $conn = $this->load->database('erp', TRUE);
        $connected = $conn->initialize();


        $post_ids = $this->input->post('sentval');
        $post_ids_ar = explode(', ', $post_ids);
        $post_ids_ar_u = array_unique(array_filter($post_ids_ar));

        $new_ids = array();

        $emp_arr = array(159, 270, 260, 551);

        $qry = false;

        if($connected) {
            if (count($post_ids_ar_u) > 0) {
                $conn->where_not_in('empid', $post_ids_ar_u);
            }
            $qry_annual = $conn->select('empid, gross, tax, deduction')
                ->from('payroll_manual_earnings')
                ->where_in('empid', $emp_arr)
                ->get();


            if ($qry_annual->num_rows() > 0) {

                $html = '';

                $html .= '<html>';
                $html .= '<head>';
                $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
                $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
                $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
                $html .= '</head>';
                $html .= '<body>';

                $html .= '<table style="width: 100%;" border="1">';
                $html .= '<thead><th>EmpID</th><th>Gross</th><th>Tax</th><th>Deduction</th><th>Net</th></thead>';
                $html .= '<tbody>';
                foreach ($qry_annual->result() as $row) {
                    $new_ids[] = $row->empid;
                    $net = ($row->gross - ($row->tax + $row->deduction));
                    $html .= '<tr>';
                    $html .= '<td>' . get_employee_info($row->empid)->lastname . '</td>';
                    $html .= '<td style="text-align: right;">' . number_format($row->gross, 2) . '</td>';
                    $html .= '<td style="text-align: right;">' . number_format($row->tax, 2) . '</td>';
                    $html .= '<td style="text-align: right;">' . number_format($row->deduction, 2) . '</td>';
                    $html .= '<td style="text-align: right; font-weight: bold;">' . number_format($net, 2) . '</td>';
                    $html .= '</tr>';
                }
                $qry = true;


                $html .= '</body>';
                $html .= '</html>';
                $data['html'] = $html;


                //SMTP & mail configuration
                $this->load->library('email');
                $config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'ssl://smtp.googlemail.com',
                    'smtp_port' => 465,
                    'smtp_user' => 'bills.peco@gmail.com',
                    'smtp_pass' => 'P3C02018!!',
                    'mailtype' => 'html',
                    'charset' => 'utf-8'
                );

                $this->email->initialize($config);
                $this->email->set_mailtype("html");
                $this->email->set_newline("\r\n");

                //Email content
                $this->email->to('lfaderon@gmail.com');
                $this->email->from('no-reply@panayelectric.com', 'PS-Update');
                $this->email->subject('PS - Update');
                $this->email->message($html);

                $sent = false;
                // Send email
                $sent = $this->email->send();
                $this->email->clear(true);
            }
        }




        $new_ids_str = implode(', ', $new_ids);

        $data['new_ids_arr'] = $post_ids_ar_u;
        $data['new_ids'] = $new_ids_str;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function getpayclass(){
        $data = array();
        $sql = $this->db->select("sysid,names")->from("prime_types_parameter")
            ->where(array("status" => 1 , "codes" => 'EMPAYCLASS'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }
        echo json_encode($data);
    }

    function select2payclass(){
        $data = array();
        $data['list'][] = array(
            'id' => 1,
            'text' => 'CONFIDENTIAL'
        );
        $non_confi = $this->db->select('payclass')->from('prime_employee_main_payclass_grouping')
            ->where(array('status' => 1, 'payrollpayclass != ' => 1))->get();

        if ($non_confi->num_rows() > 0) {
            foreach ($non_confi->result() AS $row){
                $nonconfi[] = $row->payclass;
            }
            $sql = $this->db->select("sysid,desc")->from("prime_types_parameter")
                ->where(array("status" => 1 , "codes" => 'EMPAYCLASS'))
                ->where_in('sysid',$nonconfi)
                ->get();

            if($sql->num_rows() > 0){
                foreach ($sql->result() as $row){
                    $data['list'][] = array(
                        'id' => $row->sysid,
                        'text' => $row->desc
                    );
                }
            }
        }
        echo json_encode($data);
    }

    // FIGURE 1 SEARCH FOR AN INDEX USING VALUE
    function searchForId($id, $array) {
        foreach ($array as $key => $val) {
            if ($val['data'] === $id) {
                return $key;
            }
        }
        return null;
    }
    function test101() {
        // FIGURE 2 GIVEN CONSTANT HEADERS FOR ALL CONDITIONS
        $cols_def = array(
            array('data' => 'empid','title' => 'EmpID','sClass' => '','sWidth' => '100px'), // INDEX 0
            array('data' => 'ccid','title' => 'CCID','sClass' => '','sWidth' => '100px'),
            array('data' => 'payclass','title' => 'Payclass','sClass' => '','sWidth' => '100px'),
            array('data' => 'month','title' => 'Month','sClass' => 'text-danger','sWidth' => '100px'),
            array('data' => 'year','title' => 'Year','sClass' => 'text-info','sWidth' => '100px' ),
            array('data' => 'basic','title' => 'Basic','sClass' => 'text-primary','sWidth' => '100px' ),
            array('data' => 'earnings','title' => 'Earnings','sClass' => 'text-primary','sWidth' => '100px' ),
            array('data' => 'deductions','title' => 'Deductions','sClass' => 'text-danger','sWidth' => '100px' ),
            array('data' => 'tax','title' => 'TAX','sClass' => 'text-danger','sWidth' => '100px' ),
            array('data' => 'net','title' => 'NET','sClass' => 'text-primary bold','sWidth' => '100px' ),
        );
        echo '<pre>';
        // SQL SAMPLE ARRAY || PER EMPLOYEE QUERY
        $arr_sql = array(
            array('empid'=>159, 'basic'=>27000, 'earnings'=>30000, 'deductions'=>4000, 'tax'=>2000, 'net'=>25000),
            array('empid'=>270, 'basic'=>30000, 'earnings'=>32000, 'deductions'=>3000, 'tax'=>2100, 'net'=>27000),
            array('empid'=>156, 'basic'=>32000, 'earnings'=>35000, 'deductions'=>3300, 'tax'=>2200, 'net'=>28000),
        );

        $columns = array();
        foreach($arr_sql as $index => $row) { // SQL LOOP
            if($index == 0) {
                $col_loop = array_keys($row);
                foreach ($col_loop as $col_row) {
                    $col_index = $this->searchForId($col_row, $cols_def);
                    $columns[] = $cols_def[$col_index];
                }
            }
            $data['list'][] = $row;
        }
        $data['columns'] = $columns;
        print_r($data);
    }

    function dtdocslist() {
        echo $this->model_admin->dt_docs_list();
    }

    function lookupdocsotp() {
        echo $this->model_admin->lookup_docs_otp();
    }

    function generatedocsotp() {
        echo $this->model_admin->generate_docs_otp();
    }

    function signdocument() {
        echo $this->model_admin->sign_document();
    }

}

