<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ############################################
// AUTHOR : LUCKY JOHN FADERON - SE
// MODIFIED : JOCEL ALIBAY - SPD
Class Model_auth extends CI_Model
{
    private $device_info;
    public function __construct()
    {
        parent::__construct();
        $this->device_info = init_user_device_info();
    }
    function login($username, $password)
    {
        $data['segs'] = '';
        $data['message'] = '';
        $data['num'] = '';
        $data['user'] = '';

        $mobileview_input = $this->input->post('mobileview');
        $redirect = $this->input->post('redirect');


        if(trim($username) !='') {

            $check_user = $this->check_user_exists($username);
            if ($check_user) {
                $session_response = false;
                if (hashvalidate($password, $this->get_user_data($check_user->sysid)->password)) {

                    $default_landing = '';

                    $remember = $this->input->post('rememberme');
                    if ($remember) {
                        $this->session->set_userdata('system_user_rememberme', TRUE);
                    }
                    $user_role_arr = get_users_info_roles($check_user->sysid);
                    $sess_array = array(
                        'system_user_sessid' => $this->get_user_data($check_user->sysid)->sysid,
                        'system_user_sessname' => $this->get_user_data($check_user->sysid)->username,
                        'system_user_sesstype' => $this->get_user_data($check_user->sysid)->type,
                        'system_user_sesslog' => 1,
                    );

                    $this->session->set_userdata('logged_in', $sess_array);

                    // GET LAST PAGE VISIT
                    $qry_user_logs = $this->db->select()->from('prime_system_users_logs')
                        ->where(array('userid' => $check_user->sysid, 'sessionsegment != ' => ''))
                        ->order_by('sessiondatetime', 'desc')
                        ->get()->row();

                    $default_landing = ($mobileview_input==1) ? 'mobile' : $this->get_user_data($check_user->sysid)->landing;

                    //$last_url = ($qry_user_logs) ? $qry_user_logs->sessionsegment : '';
                    $last_url = ($redirect != '') ? $redirect : (($qry_user_logs) ? $qry_user_logs->sessionsegment : '');
                    $data['segs'] = ($default_landing!='') ? $default_landing : $last_url;
                    $data['message'] = return_message_ajax('success', 'fa-check', 'Authentication accepted!');
                    $data['num'] = true;
                    $data['user'] = $check_user;
                    $data['username'] = ucfirst($this->get_user_data($check_user->sysid)->firstname);
                    $session_response = true;

                } else {
                    $data['message'] = return_message_ajax('danger', 'fa-times', 'Authentication denied!');
                    $data['num'] = false;
                    $data['user'] = false;
                    $session_response = false;
                }
                // SET USER LOGS : IF PASSWORD VALIDATE TRUE OR FALSE //
                $logs_array = array(
                    'userid' => $this->get_user_data($check_user->sysid)->sysid,
                    'sessionip' => init_user_device_ip(),
                    'sessiondevice' => $this->device_info['platform'],
                    'sessiondevicename' => gethostname(),
                    'sessionagent' => $this->device_info['name'],
                    'sessionlogtype' => 1,
                    'sessionlogresponse' => $session_response
                );
                $this->insert_user_logs($logs_array);
            } else {
                // CHECK REGISTRATION
                $check_registration = $this->check_user_registration($username, $password);
                if(isset($check_registration->qry) && $check_registration->qry == true) {
                    $default_landing = '';

                    $remember = $this->input->post('rememberme');
                    if ($remember) {
                        $this->session->set_userdata('system_user_rememberme', TRUE);
                    }
                    $user_role_arr = get_users_info_roles($check_registration->userid);
                    $sess_array = array(
                        'system_user_sessid' => $check_registration->userid,
                        'system_user_sessname' => $this->get_user_data($check_registration->userid)->username,
                        'system_user_sesstype' => $this->get_user_data($check_registration->userid)->type,
                        'system_user_sesslog' => 1,
                    );

                    $this->session->set_userdata('logged_in', $sess_array);

                    // GET LAST PAGE VISIT
                    $qry_user_logs = $this->db->select()->from('prime_system_users_logs')
                        ->where(array('userid' => $check_registration->userid, 'sessionsegment != ' => ''))
                        ->order_by('sessiondatetime', 'desc')
                        ->get()->row();

                    $default_landing = ($mobileview_input==1) ? 'mobile' : $this->get_user_data($check_registration->userid)->landing;

                    //$last_url = ($qry_user_logs) ? $qry_user_logs->sessionsegment : '';
                    $last_url = ($redirect != '') ? $redirect : (($qry_user_logs) ? $qry_user_logs->sessionsegment : '');
                    $data['segs'] = ($default_landing!='') ? $default_landing : $last_url;
                    $data['message'] = return_message_ajax('success', 'fa-check', 'Authentication accepted!');
                    $data['num'] = true;
                    $data['user'] = $check_user;
                    $data['username'] = ucfirst($this->get_user_data($check_registration->userid)->firstname);
                    $session_response = true;
                }else {
                    $msg = "";
                    $msg .= "Account does not exists!\n";
                    if(isset($check_registration->msg) && $check_registration->msg != '') {
                        $msg .= $check_registration->msg;
                    }
                    $data['message'] = return_message_ajax('warning', 'fa-warning', $msg);
                    $data['num'] = false;
                    $data['user'] = false;
                }
            }
        }else{
            $data['message'] = return_message_ajax('warning', 'fa-warning', 'Username is empty!');
            $data['num'] = 0;
            $data['user'] = false;
        }
        return $data;
    }

    function check_user_registration($username, $password) {
        $data = array();
        $qry = false;
        $qry_registration = $this->db->query("
            SELECT
            suc.sysid,
            em.personid
            FROM
            prime_employee_main AS em
            INNER JOIN prime_system_users_confirmation AS suc ON em.personid = suc.personid AND suc.`status` = 1
            WHERE
            suc.codes = '$password' 
            AND em.empid = '$username' AND suc.`status` = 1
        ")->row();
        if($qry_registration) {
            $message = '';
            $this->db->trans_begin();
            $password_hash = hashing($password);
            $ins_arr = array(
                'username' => $username,
                'password' => $password_hash,
                'personid' => $qry_registration->personid,
                'type' => 2,
                'status' => 1
            );
            $this->db->insert('prime_system_users', $ins_arr);
            $user_id = $this->db->insert_id();
            $message .= 'Error Insert System Users : ' . $this->db->_error_message();

            $ins_role_arr = array(
                'userid' => $user_id,
                'roleid' => 2,
                'type' => 1
            );
            $this->db->insert('prime_system_users_roles_matrix', $ins_role_arr);
            $message .= 'Error Insert System Users Roles : ' . $this->db->_error_message();

            $this->db->where(array('status' => 1, 'sysid' => $qry_registration->sysid));
            $this->db->update('prime_system_users_confirmation', array('status' => 2, 'updatedby' => $user_id));
            $message .= 'Error Update Confirmation : ' . $this->db->_error_message();

            $data = db_trans($this->db, $message, false, false);
            $data['userid'] = $user_id;
        } else {
            $data['qry'] = $qry;
        }
        return (object) $data;
    }

    function logout() {
        // SET USER LOGS : IF PASSWORD VALIDATE TRUE OR FALSE //
        $data = array();
        $qry = false;
        $userid = user_id();
        $segs = $this->input->post('segs');
        $navid = $this->input->post('navid');
        $msg = '';
        $user_landing = '';

        // GET USER LANDING //
        $qry_users = $this->db->select('landing')->from('prime_system_users')
            ->where(array('sysid' => $userid))
            ->get()->row();

        $this->db->trans_begin();
        if($qry_users) {
            $data = array(
                'userid' => $userid,
                'sessionip' => init_user_device_ip(),
                'sessiondevice' => $this->device_info['platform'],
                'sessiondevicename' => gethostname(),
                'sessionagent' => $this->device_info['name'],
                'sessionlogtype' => 0,
                'sessionlogresponse' => 0,
                'sessionsegment' => $segs,
                'sessionmoduleid' => $navid
            );

            $this->db->set('sessiondatetime', 'NOW()', false);
            $this->db->insert('prime_system_users_logs', $data);
            $err = $this->db->_error_message();

            if($this->db->trans_status()==true) {
                $qry = true;
                $this->db->trans_commit();
                $msg = return_message_ajax('success', '', 'Goodbye!');
                $user_landing = $qry_users->landing;
                $this->session->unset_userdata('logged_in');
                session_destroy();

            }else{
                $this->db->trans_rollback();
                $msg = 'Error PHP : ' . $err;
            }
        }
        $data['landing'] = $user_landing;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function check_user_exists($username){
        $query_user = $this->db->select('sysid')
            ->from('prime_system_users')
            ->where(array('username' => $username, 'status' => 1))
            ->get()->row();
        if($query_user) {
            return $query_user;
        } else {
            $query_employee = $this->db->select('u.sysid')
                ->from('prime_employee_main AS e')
                ->join('prime_system_users AS u', 'u.personid = e.personid')
                ->where(array('e.empid' => $username, 'e.status' => 1))
                ->get()->row();
            if($query_employee) {
                return $query_employee;
            }else{
                return false;
            }
        }

    }

    function get_user_data($id){
        if($id>0) {
            $get_user = $this->db->select('sysid, username, personid, password, firstname, landing, type')
                ->from('prime_system_users')
                ->where('sysid', $id)
                ->get()
                ->row();
            if ($get_user->personid > 0) {
                $userinfo = get_users_info($get_user->sysid);
                $firstname = ($userinfo) ? $userinfo->firstname : $get_user->firstname;
            } else {
                $firstname = $get_user->firstname;
            }
            $sysid = $get_user->sysid;
            $username = $get_user->username;
            $password = $get_user->password;
            $personid = $get_user->personid;
            $landing = $get_user->landing;
            $type = $get_user->type;
            $data = array(
                'sysid' => $sysid,
                'username' => $username,
                'personid' => $personid,
                'password' => $password,
                'firstname' => $firstname,
                'landing' => $landing,
                'type' => $type
            );
        }else{
            $data = false;
        }
        return (object) $data;
    }

    function insert_user_logs($data){
        return $this->db->set('sessiondatetime', 'NOW()', false)
            ->insert('prime_system_users_logs', $data);
    }

    function get_user_logs($sysid){
        return ( $query = $this->db->select('sessionlogtype, sessionip, sessionagent')
            ->from('prime_system_users_logs')
            ->where(array('userid' => $sysid, 'sessionlogresponse' => 1))
            ->order_by('sessiondatetime', 'desc')
            ->get()->row() ) ? $query : false;
    }

    function get_user_multi_logs_tempts($userid){
        $hr = date('h');
        $mn = date('i');
        $day = date('d');
        $month = date('m');
        $year = date('Y');
        return ( $query = $this->db->query('SELECT sessionlogtype FROM prime_system_users_logs 
		WHERE userid = '.$userid.' 
		AND sessionlogresponse = 0 
		AND sessiondatetime BETWEEN DATE_SUB(NOW(), INTERVAL 1 MINUTE) AND NOW() 
		ORDER BY sessiondatetime DESC')->row() ) ? $query : false;
    }

    function lock_user_log($userid = false) {
        $this->db->trans_begin();
        $data = array();
        if($userid==false) {
            $userid = user_id();
        }
        $segs = $this->input->post('segs');
        $navid = $this->input->post('navid');
        $logs_array = array(
            'userid' => $userid,
            'sessionip' => init_user_device_ip(),
            'sessiondevice' => $this->device_info['platform'],
            'sessiondevicename' => gethostname(),
            'sessionagent' => $this->device_info['name'],
            'sessionlogtype' => 2,
            'sessionlogresponse' => 1,
            'sessionsegment' => $segs,
            'sessionmoduleid' => $navid
        );
        $this->insert_user_logs($logs_array);
        if($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Your account has been lock!';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $msg = '';
            $func = 'warning';
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return $data;
    }

    function unlock_user_log() {
        $user_info = $this->db->select('username')
            ->from('prime_system_users')
            ->where('sysid', user_id())
            ->get()->row();
        $username = $user_info->username;
        $password = $this->input->post('password');
        $data = $this->login($username, $password);
        return json_encode($data);
    }


}