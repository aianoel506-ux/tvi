<?php
// @TODO SET QUERY TO FILTER Power / Admin / Stand User view.
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class User extends CI_Controller {

    private $user_login;

    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_auth');
        $this->load->model('model_user');
        $this->load->model('model_cwdo');

        require_once APPPATH.'third_party/PHPExcel.php';
        $this->excel = new PHPExcel();

        // $this->user_login = user_session();
    }

    public function index() {
        //echo row_btn_basic(21);
    }

    public function shortcut() {
        if(user_id() == 0) {
            redirect(base_url(), 'refresh');
        }
        $data = array();
        $data['pagetitle'] = 'User\'s Shortcut';
        init_header($data);
        $this->load->view('user/shortcut', $data);
        init_footer($data);
    }

    function randomstr() {
        $a=array("online","away","disturb");
        $random_keys=array_rand($a,2);
        return $a[$random_keys[0]];
    }
    function chatuserlist() {
        $search = $this->input->post('search');
        $html = '';
        $html .= '<ul class="media-list list-items">';
        if(user_id() > 0) {
            if ($search && strlen($search) > 3) {
                $this->db->like('username', $search);
            }
            $qry_users = $this->db->select()
                ->from('prime_system_users')
                ->where(array('status' => 1))
                ->get();
            $qry_num = $qry_users->num_rows();
            if ($qry_num > 0) {

                foreach ($qry_users->result() as $row) {
                    $person = boolval($row->personid);
                    if ($person) {
                        $personid = $row->personid;
                    } else {
                        $personid = $row->sysid;
                    }
                    $stat_rand = $this->randomstr();

                    $user_info = get_users_info($row->sysid);
                    $name = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : $row->firstname;

                    $user_pic = get_users_pic_url($personid, $person, true);
                    $html .= '<li class="media ' . $stat_rand . '" id="chat_user_lists" data-id="' . $row->sysid . '" data-stat="' . $stat_rand . '">
                            <div class="media-status">
                               
                            </div>
                            <img class="media-object tooltips" title="' . $stat_rand . '" data-placement="right" src="' . $user_pic . '" alt="...">
                            <div class="media-body">
                                <h4 class="media-heading">' . $name . '</h4>
                                <div class="media-heading-sub">
                                     ' . get_users_default_pos($row->sysid)->code . '
                                </div>
                            </div>
                        </li>';
                }
            } else {

                $html .= '<li class="media">
                            <div class="media-status">
                               
                            </div>
                            <h4 class="media-heading">'.SYSTEM_NAME.'</h4>
                            <div class="media-body">
                                <h4 class="media-heading"><i class="fa fa-times text-danger"></i>Not found!</h4>
                            </div>
                        </li>';
            }
        }else{
            $html .= '<li class="media">
                            <div class="media-status">
                               
                            </div>
                            <img class="media-object tooltips" title="' . SYSTEM_NAME . '" data-placement="right" src="' . base_url('assets/global/img/admin_pic.png') . '" alt="...">

                            <h4 class="media-heading">'.SYSTEM_NAME.'</h4>
                            <div class="media-body">
                                <h4 class="media-heading"><i class="fa fa-times text-danger"></i>Session timeout!</h4>
                            </div>
                        </li>';
            $qry_num = 0;
        }

        $html .= '</ul>';
        $data['html'] = $html;
        $data['usernum'] = $qry_num;
        echo json_encode($data);
    }

    function updateaccount() {
        $data = array();
        $this->db->trans_begin();
        $userid = $this->input->post('userid');

        $passwordold = trim($this->input->post('passwordold'));
        $passwordnew = trim($this->input->post('passwordnew'));
        $passwordcon = trim($this->input->post('passwordcon'));

        if (hashvalidate($passwordold, $this->model_auth->get_user_data($userid)->password)) {
            if( $passwordnew == $passwordcon ) {
                if (in_array(1, get_users_roles_matrix_id_arr())) {
                    $upd_arr = array(
                        'password' => hashing($passwordnew)
                    );

                    $this->db->where('sysid', $userid);
                    $this->db->update('prime_system_users', $upd_arr);
                    $qry = true;
                    $msg = '<b class="text-warning">Admin:</b> New Password Saved!';
                    $func = 'success';
                }else {
                    if ($passwordnew == $passwordold) {
                        $qry = false;
                        $msg = 'You cannot use old password as new.';
                        $func = 'warning';
                    } else {
                        $upd_arr = array(
                            'password' => hashing($passwordnew)
                        );
                        $this->db->where('sysid', $userid);
                        $this->db->update('prime_system_users', $upd_arr);
                        $qry = true;
                        $msg = 'New Password Saved!';
                        $func = 'success';

                        $userinfo = get_users_info($userid);
                        if($userinfo) {
                            $this->db->query("UPDATE prime_system_users_confirmation SET status = 0 
                                            WHERE personid = {$userinfo->pid} AND status = 2");
                        }
                    }
                }
            }else{
                $qry = false;
                $msg = 'New Password not match!';
                $func = 'warning';
            }
        }else{
            $qry = false;
            $msg = 'Wrong old password';
            $func = 'error';
        }

        if($this->db->trans_status()===true) {
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }

        $data['data'] = $this->input->post();
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;

        echo json_encode($data);
    }

    function gettrndetails() {
        $trnid = $this->input->post('id');
        $qry = $this->db->select()->from('transaction_request_main_trails')->where('trnid', $trnid)->order_by('datecreated', 'asc')->get();
        $num_rows = $qry->num_rows();
        $time_details = array();
        $total_comm = 0;
        if ($num_rows > 0) {
            $i = 1;
            foreach ($qry->result() as $row) {
                $ii = $i++;

                // GET COMMENT //
                $qry_logs = $this->db->select()->from('transaction_request_trails_logs')->where(array('trailid' => $row->sysid, 'activity' => 90))->get();
                $comm = ($qry_logs->num_rows() > 0) ? '<a href="javascript:;" class="btn btn-default btn-xs"><span class="label label-danger">' . $qry_logs->num_rows() . '</span> <i class="fa fa-search"></i> View</a>' : '';
                $total_comm += $qry_logs->num_rows();
                $qry_stages = $this->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $row->stageid)->get()->row();
                $data['data'][] = array(
                    'num' => $ii,
                    'lastupd' => $qry_stages->desc,
                    'details' => '',
                    'trn' => '',
                    'createdby' => get_users_info($row->createdby)->firstname . ' ' . get_users_info($row->createdby)->lastname,
                    'date' => $row->datecreated,
                    'comm' => $comm
                );
                $time_details[] = array("sysid" => $ii, "date" => $row->datecreated);
            }
        }

        $data['totalcomm'] = ($total_comm > 1) ? $total_comm . 'comments' : $total_comm . ' comment';
        $data['totalroutes'] = $num_rows;
        $data['timespent'] = sum_time($time_details);
        $data['input'] = $this->input->post();
        $data['qry'] = ($num_rows > 0) ? true : false;
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_rows;
        $data['iTotalDisplayRecords'] = $num_rows;
        echo json_encode($data);
    }


    function inittrnsummary() {
        // SELECT2 SUMMARY ACTIVITIES FOR USER
        $qry_summary = $this->db->select('t.trnid')
            ->from('transaction_request_main_trails AS t')
            ->join('transaction_request_main AS m', 'm.sysid = t.trnid')
            ->where('m.status', 1)
            ->group_by('t.trnid')->get();
          //  ->group_by('t.trnid')->get();
        $num_rows = $qry_summary->num_rows();
        if ($qry_summary->num_rows() > 0) {
            foreach ($qry_summary->result() as $row) {
                $trn_cnt = $this->db->select('trnid, dataid')->from('transaction_request_main_trails')->where('trnid', $row->trnid)->group_by('dataid')->get()->num_rows();
                $qry_module = $this->db->select()->from('transaction_request_main')->where('sysid', $row->trnid)->get()->row();
                $qry_flow = $this->db->select()->from('prime_transaction_flow_main')->where('sysid', $qry_module->flowid)->get()->row();
                $res_summary[] = array('id' => $qry_flow->sysid, 'text' => get_module_name($qry_flow->moduleid)->code . ' - ' . get_module_name($qry_flow->moduleid)->desc . ' - ' . $trn_cnt);
                ;
            }
        } else {
            $res_summary = array();
        }
        $data['actsumary'] = $res_summary;
        echo json_encode($data);


    }

    function inittrn() {

        $stat = $this->input->post('origid');
        // DATATABLE


        if ($stat > 0) {
            $this->db->where('flowid', $stat);
        }

        $qry = $this->db->select("m.sysid, m.codes, m.descs, m.flowid, m.createdby, m.datecreated")
            ->from('transaction_request_main AS m')
            ->where('m.status', 1)
            ->get();

        $num_rows = $qry->num_rows();
        if ($num_rows > 0) {
            $i = 1;
            foreach ($qry->result() as $row) {
                $trnid = $row->sysid;
                $qry_last_trails = $this->db->select()->from('transaction_request_main_trails')->where('trnid', $trnid)->order_by('datecreated', 'desc')->get()->row();
                $qry_arr_trails = $this->db->select('sysid, datecreated')->from('transaction_request_main_trails')->where('trnid', $trnid)->order_by('datecreated', 'asc')->get();
                $qry_last_stage = $this->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $qry_last_trails->stageid)->get()->row();
                $num_rows = $qry->num_rows();
                $time_details = array();
                $trn_elapse = '';
                $ovr_elapse = '';

                $cnt_text = 0;
                if ($num_rows > 1) {
                    $ii = 0;
                    foreach ($qry_arr_trails->result() as $row_arr_el => $value) {
                        $iii = $ii++;
                        $row_num = $row_arr_el;
                        $cnt_text += $row_num;
                        if ($row_arr_el == 0) {
                            $firstdate = $value->datecreated;
                            $lastdate = $value->datecreated;
                        } else {
                            if ($iii == $row_num) {
                                $lastdate = $value->datecreated;
                            } else {
                                $lastdate = date('Y-m-d h:m:s');
                            }
                        }
                    }
                    $trn_elapse = time_elapsed_diff($firstdate, $lastdate, true);
                    $ovr_elapse = time_elapsed_diff($firstdate, date('Y-m-d h:m:s'));
                    //$trn_elapse = $lastdate;
                }
                $getessrno = $this->db->select("apd.essrno")
                    ->from("transaction_request_main_trails as trmt")
                    ->join("application_customers_details as apd" , "apd.sysid = trmt.dataid")
                    ->where(array("trmt.trnid" => $row->sysid))
                    ->get()->row();
                $essrno = ($getessrno) ? $getessrno->essrno : '0';
                $data['data'][] = array(
                    'expand' => btn_expand($trnid),
                    'num' => $i++,
                    'lastupd' => $qry_last_trails->datecreated . '<span class="label label-danger pull-right">' . $essrno . '</span><br>' ,
                       // '<span class="text-success">' . get_users_info($qry_last_trails->createdby)->firstname . ' ' . get_users_info($qry_last_trails->createdby)->firstname . '</span><br>',
                    'dataid' => '',
                    'origid' => '',
                    'details' => '<span class="text-danger pull-right">' . $qry_last_stage->desc . '</span>' .
                        $row->datecreated . '<br>' .
                        '<span class="text-success">' . $row->descs . '</span>',
                    'control' => '<div class="btn-group btn-xs">' . btn_view_trn($qry_last_trails->sysid, $qry_last_trails->dataid) . btn_view_trn($qry_last_trails->sysid, $qry_last_trails->dataid, 'task') . btn_view_trn($qry_last_trails->sysid, $qry_last_trails->dataid, 'profile') . '</div>',
                    'trn' => 'Route Lapse: ' . $trn_elapse . '<br>Overall Lapse: ' . $ovr_elapse,
                );

                /*

                  $qry_flow = $this->db->select()->from('prime_transaction_flow_main')->where('sysid', $row->flowid)->get()->row();
                  $qry_module = $this->db->select()->from('prime_module_navigations_main')->where('sysid', $qry_flow->moduleid)->get()->row();
                  $qry_first_trail = $this->db->select()->from('transaction_request_main_trails')->where('trnid', $row->sysid)->get()->row();

                  $moduleid = $qry_module->sysid;
                  $dataid = $qry_first_trail->dataid;

                  // GET LAST UPDATE FROM TRN
                  $qry_last_trn = $this->db->select()->from('transaction_request_main_trails')->where('trnid', $row->sysid)->order_by('datecreated', 'desc')->get()->row();
                  $stage_details = get_stage_details($qry_last_trn->stageid);
                  $data['data'][] = array(
                  'expand' => btn_expand($row->sysid),
                  'num' => $i++,
                  'lastupd' => $qry_last_trn->datecreated . '<br><em class="text-info">By: ' . get_users_info($qry_last_trn->createdby)->firstname . '</em>',
                  'codes' => $row->codes . ' - ' . get_module_name($moduleid)->name,
                  'dataid' => $moduleid,
                  'origid' => $moduleid,
                  'details' => $row->codes . '<br><span class="text-info">' . $row->descs . '</span>',
                  'control' => '<div class="btn-group">' . row_btn_basic($qry_last_trn->sysid, $dataid) . '</div>',
                  // row_btn_view($row->sysid, $dataid, false, '')
                  'trn' => $stage_details->desc . '<br><em>' . get_users_info($qry_last_trn->createdby)->firstname . ' ' . get_users_info($qry_last_trn->createdby)->lastname . '</em><span class="pull-right text-danger">' . $qry_last_trn->datecreated . '</span>',
                  );

                 */
            }
        }
        $data['input'] = $this->input->post();
        $data['qry'] = ($num_rows > 0) ? true : false;
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_rows;
        $data['iTotalDisplayRecords'] = $num_rows;

        echo json_encode($data);
    }

    function mycalendar() {
        if (user_session()) {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_session()->system_user_sessid);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('user/mycalendar', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    function stats() {
        $data = array();
        $this->load->view('user/stats', $data);
    }

    function system() {
        $data = array();
        $this->load->view('user/system', $data);
    }

    function drawmycalendar() {
        $month              = $this->input->post('month');
        $year               = $this->input->post('year');
        $dateObj            = DateTime::createFromFormat('!m', $month);
        $monthName          = $dateObj->format('F'); // March
        $data['month']      = $monthName;
        $data['year']       = $year;
        $data['calendar']   = draw_calendar($month, $year);
        echo json_encode($data);
    }

    function activities() {
        $data = array();
        if(user_id()) {
            $data['trnqry'] = $this->model_query->user_transaction_details();
            $this->load->view('user/activities', $data);
        }else{
            $data['message'] = 'Session time out!';
            echo page_session($data);
        }
    }

    function calendar() {
        $this->load->view('user/calendar');
    }

    function messages() {
        page_construction();
    }

    function notifications() {
        $data['html'] = 'Notifications';
        $data['title'] = 'Notifications';
        $this->load->view('user/notifications', $data);
    }

    function tellering() {
        $data = array();
        if (isset(user_session()->system_user_sessid)) {
            $data['pagetitle'] = "Tellering";
            $this->load->view('admin/common/head', $data);
            $this->load->view('admin/pages/modules/tellering');
            $this->load->view('admin/common/scripts');
            log_user_page(8);
        } else {
            $this->load->helper(array('form'));
            $this->load->view('admin/common/head');
            $this->load->view('includes/css/login');
            $this->load->view('redirects/forms/view_login_teller');
            $this->load->view('admin/common/scripts');
            $this->load->view('includes/scripts/login');
        }
    }

    function testapt() {
        $jobtype = 'MRO';

        //init_header(false);
        $testdb = $this->load->database('testdb', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        //$testdb->where('MONTH(trndate)', 1, false);
        if($jobtype=='ECALES' || $jobtype=='MRO' ) {
            $query = $testdb->select('a.essr_no AS ESSR, a.servno AS SERVNO, a.tname AS NAME, a.taddr AS ADDR, a.jobtype AS JOB')
                ->select("GROUP_CONCAT(CONCAT(b.ref, ' / ', b.tdate) ORDER BY b.tdate DESC SEPARATOR '|') AS TRN", false)
                ->from('apt a')
                ->join('apt AS b', "a.essr_no = b.essr_no", 'left')
                ->where(
                    array(
                        'a.tname != ' => '',
                        'a.taddr != ' => '',
                        'a.ref != ' => 'CSD-REQ',
                        'a.essr_no != ' => 62453,
                        'a.jobtype' => $jobtype,
                    )
                )
                ->group_by('a.servno, a.tname, a.taddr, a.jobtype, b.jobtype')
                //->limit(100)
                ->get();
        }else{
            $query = $testdb->select('essr_no AS ESSR, servno AS SERVNO, tname AS NAME, taddr AS ADDR, jobtype AS JOB')
                ->select("GROUP_CONCAT(CONCAT(ref, ' / ', tdate) ORDER BY trndate DESC SEPARATOR '|') AS TRN", false)
                ->from('apt')
                ->where(
                    array(
                        'tname != ' => '',
                        'taddr != ' => '',
                        'ref != ' => 'CSD-REQ',
                        'essr_no != ' => 62453,
                        'jobtype' => $jobtype
                    )
                )
                ->group_by(' servno, tname, taddr, jobtype')
                //->limit(100)
                ->get();
        }

        $data['jobtype'] = $jobtype;
        $data['qry'] = $query;
        $this->load->view('admin/pages/testapt', $data);
    }

    function querequest($type = null) {
        //data
        $data = array();
        $data['pagetitle'] = 'Queue Request';

        $data['paymentscount'] = $this->db->select('types')->from('customer_queue')->where(array('types' => '274'))->get();
        $data['legalcounts'] = $this->db->select('types')->from('customer_queue')->where(array('types' => '275'))->get();
        $data['custservcounts'] = $this->db->select('types')->from('customer_queue')->where(array('types' => '276'))->get();
        $data['complaintscounts'] = $this->db->select('types')->from('customer_queue')->where(array('types' => '277'))->get();

        $this->load->view('admin/common/head', $data);
        $this->load->view('user/queue/header', $data);
        $this->load->view('user/queue/loadcountstemplate', $data);
        $data['requesttype'] = $type;

        if($type == null) {

            $data['queboxarr'] = $this->db->select('tp.names, tp.desc, tp.sysid, si.icon')
                ->from('prime_types_parameter AS tp')
                ->join('system_icons AS si', 'si.sysid = tp.icons')
                ->where(array('tp.codes' => 'QUEUE', 'tp.status' => 1))
                ->get();

            $this->load->view('user/querequest', $data);
        }else{

            $this->load->view('user/queue/requestform', $data);
            $this->load->view('user/queue/backBtn', $data);
            $this->load->view('user/queue/keyboard');

        }
        $this->load->view('admin/common/scripts', $data);
        $this->load->view('user/queue/footer', $data);

    }
    function submitrequest(){
        $customerData = array(
            'names' => $this->input->post('name'),
            'servno' => $this->input->post('servNo'),
            'types' => $_POST['types']
        );
        $this->db->insert('customer_queue',$customerData);
        echo "Data has been inserted.";
    }

    function getNumberToPrint(){
        $sql = $this->db->select("sysid")->from("customer_queue")->order_by("sysid","desc")->limit(1)->get();
        echo json_encode($sql->result());
    }

    function queuenumberprinting(){

        $data = array();
        $data['pagetitle'] = 'Queue Request';
        $data['queboxarr'] = $this->db->select('tp.names, tp.desc, tp.sysid, si.icon')
            ->from('prime_types_parameter AS tp')
            ->join('system_icons AS si', 'si.sysid = tp.icons')
            ->where(array('tp.codes' => 'QUEUE', 'tp.status' => 1))
            ->get();

        $this->load->view('admin/common/head', $data);
        $this->load->view('user/queue/header', $data);
        $this->load->view('user/queuenumberprinting' , $data);
        $this->load->view('admin/common/scripts', $data);

    }

    function ticketing(){
        $data = array();
        $data['pagetitle'] = 'Ticketing System';

        $this->load->view('admin/common/head',$data);
        $this->load->view('user/tsmenu/header',$data);
        $this->load->view('user/tsmenu/ticketing_system',$data);
        $this->load->view('admin/common/scripts',$data);
        $this->load->view('user/tsmenu/footer',$data);
    }

    function getticketselect() {
        $data = array();
        $query = $this->db->select('sysid, desc, names')
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'TICKET'))
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc
                );
            }
        }
        echo json_encode($data);
    }

    function getbarangay() {
        $data = array();
        $term = $this->input->post('term');
        $dist = $this->input->post('dist');
        $query = $this->db->select('l.sysid, l.texts')
            ->from('address_barangay AS l')
            ->like('l.texts', $term)
            ->where('l.distid', $dist)
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->texts
                );
            }
        }else{
            $data['list'] = array();
        }
        echo json_encode($data);
    }

    function getlandmark() {
        $data = array();
        $term = $this->input->post('term');
        $brgy = $this->input->post('brgy');
        $dist = $this->input->post('dist');
        $query = $this->db->select('l.sysid, l.texts, d.names')
            ->from('address_landmark AS l')
            ->join('address_districts AS d', 'l.distid = d.sysid')
            ->like('l.texts', $term)
            ->where('l.distid', $dist)
            ->where('l.brgyid', $brgy)
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->texts
                );
            }
        }else{
            $data['list'] = array();
        }
        echo json_encode($data);
    }

    function getoutage() {
        $data = array();
        $query = $this->db->select('sysid, desc, names')
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'TS'))
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }
        echo json_encode($data);
    }

    function select2concerns() {
        $data = array();
        $query = $this->db->select('sysid, desc, names')
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'CDC'))
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }
        echo json_encode($data);
    }

    function getcomplaints() {
        $data = array();
        $query = $this->db->select('sysid, desc, names')
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'CWDFINDINGS'))
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names
                );
            }
        }
        echo json_encode($data);
    }
    function getdepartments(){
        $data = array();

        $query = $this->db->select("sysid,codes,desc")
            ->from("prime_costcenter_main")
            ->where(array('status' => 1))
            ->get();

        if($query->num_rows() > 0){
            foreach ($query->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc . ' - ' . $row->codes
                );
            }
        }
        echo json_encode($data);
    }

    function getdistrictselect() {
        echo get_dist_list_select();
    }
    function getpriorityselect(){
        $data = array();
        $query = $this->db->select('sysid,codes, names')
            ->from('prime_types_parameter')
            ->where(array('codes' => 'PRIOR'))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->codes
                );
            }
        }
        $data['input'] = $this->input->post();
        $data['num'] = $num_rows;
        echo json_encode($data);
    }
    function getstatusselect(){
        $data = array();
        $query = $this->db->select('sysid,codes, names')
            ->from('prime_types_parameter')
            ->where(array('codes' => 'STATUS'))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->codes
                );
            }
        }
        $data['input'] = $this->input->post();
        $data['num'] = $num_rows;
        echo json_encode($data);
    }



    function getticketpartselect() {
        $data = array();
        $ticketid = $this->input->post('id');
        $query = $this->db->select('sysid, codes, descs')
            ->from('ticketing_particular')
            ->where(array('status' => 1, 'ticketid' => $ticketid))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->codes . ' - ' .$row->descs
                );
            }
        }
        $data['input'] = $this->input->post();
        $data['num'] = $num_rows;
        echo json_encode($data);
    }

    function updateticket(){
        $data = array();
        $q = false;
        $ins = true;
        $msg = '';
        $userid = user_id();
        $func = '';

        $sysid = $this->input->post('ticket_id');
        $status = $this->input->post('ticketstatus');
        $priority = $this->input->post('ticketpriority');
        $department = $this->input->post('ticketdepart');
        $address = $this->input->post('ticketaddr');
        $findings = $this->input->post('ticketfindings');

        $this->db->trans_begin();

        $update_arr = array(
            'status' => $status ,
            'priority' => $priority,
            'department' => $department,
            'address' => $address,
            'findings' => $findings,
            'updatedby' => user_id()
        );

        $this->db->where('sysid', $sysid);
        $this->db->update('trn_ticketing_logs', $update_arr);

        if($this->db->trans_status()===TRUE && $ins==true) {
            $this->db->trans_commit();
            $q = true;
            $func = 'success';
            $msg = 'Ticket Updated!!';
        }else{
            $this->db->trans_rollback();
            $q = false;
            $func = 'error';
            $msg = 'Ticket Not Saved!';
        }

        $data['input'] = $this->input->post();
        $data['qry'] = $q;
        $data['func'] = $func;
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function getdata(){
        $data = array();
        $stat = $this->input->post('stat');

        if($stat >0) {
            $this->db->where('ttl.status', $stat);
        }
        $sql = $this->db->select("ttl.sysid, ptp.names,tp.descs,ttl.servno,ttl.name,ttl.address,pd.descriptions,ttl.remarks,ttl.datecreated,psu.lastname, pcm.desc , ttl.findings , ttl.status,ttl.priority")
            ->from("trn_ticketing_logs AS ttl")
            ->join("prime_types_parameter AS ptp" , 'ttl.compid = ptp.sysid' , 'left')
            ->join("ticketing_particular AS tp" , 'ttl.compspecid=tp.sysid'   , 'left')
            ->join("prime_districts AS pd" , 'ttl.district=pd.sysid' , 'left')
            ->join("prime_system_users AS psu" , 'ttl.createdby=psu.sysid' , 'left')
            ->join("prime_costcenter_main AS pcm" , 'pcm.sysid = ttl.department','left')
            ->where(array('ptp.status' => '1'))
            ->order_by('datecreated', 'desc')
            ->get();

        $num_rows = $sql->num_rows();
        if($num_rows>0) {
            $num = 1;
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'num' => $num++,
                    'name' => $row->name,
                    'names' => $row->names,
                    'descs' => $row->descs,
                    'servno' => '<span style="color: #ee1100;">'.$row->servno.'</span>',
                    'descriptions' => $row->descriptions,
                    'department' => $row->desc,
                    'status' => get_types_label_format($row->status),
                    'priority' => get_types_label_format($row->priority),
                    'buttons' => '<a href="javascript:;"  data-id="'.$row->sysid.'" class="viewBtn btn btn-xs primary"><i class="fa fa-search"></i> View</a>'
                );
            }
        }
        echo json_encode($data);
    }


    function getticketdetails() {
        $data = array();
        $id = $this->input->post('id');
        $name = '';
        $address = '';
        $priority = '';
        $complain = '';
        $particular = '';
        $serviceno = '';
        $district = '';
        $remarks = '';
        $date_created = '';
        $created_by = '';
        $department = '';
        $findings  = '';
        $status  = '';

        $qry_ticket = $this->db->select("ttl.sysid, ptp.names, tp.descs,ttl.servno, ttl.name, ttl.address , ttl.department,ttl.findings, pd.descriptions,ttl.remarks,ttl.datecreated,psu.lastname,ttl.status,ttl.priority")
            ->from("trn_ticketing_logs AS ttl")
            ->join("prime_types_parameter AS ptp" , 'ttl.compid = ptp.sysid' , 'left')
            ->join("ticketing_particular AS tp" , 'ttl.compspecid=tp.sysid'   , 'left')
            ->join("prime_districts AS pd" , 'ttl.district = pd.sysid' , 'left')
            ->join("prime_system_users AS psu" , 'ttl.createdby = psu.sysid' , 'left')
            ->where(array('ttl.sysid' => $id))
            ->order_by('datecreated', 'desc')
            ->get()->row();
        if($qry_ticket) {
            $name = $qry_ticket->name;
            $address = $qry_ticket->address;
            $priority = $qry_ticket->priority;
            $complain = $qry_ticket->names;
            $particular = $qry_ticket->descs;
            $serviceno = $qry_ticket->servno;
            $district = $qry_ticket->descriptions;
            $remarks = $qry_ticket->remarks;
            $date_created = $qry_ticket->datecreated;
            $created_by = $qry_ticket->lastname;
            $department = $qry_ticket->department;
            $findings = $qry_ticket->findings;
            $status = $qry_ticket->status;
        }
        $data['name'] = $name;
        $data['address'] = $address;
        $data['priority'] = $priority;
        $data['complain'] = $complain;
        $data['particular'] = $particular;
        $data['serviceno'] = $serviceno;
        $data['district'] = $district;
        $data['remarks'] = $remarks;
        $data['date_created'] = $date_created;
        $data['created_by'] = $created_by;
        $data['department'] = $department;
        $data['findings'] = $findings;
        $data['status'] = $status;

        echo json_encode($data);
    }

    function select_customer_ticket(){

        $data = array();

        $id = $this->input->post("id");

        $sql =  $this->db->select()
            ->from("trn_ticketing_logs")
            ->where(array("sysid"=>$id))
            ->get()->row();

        $data['name'] = $sql->name;
        $data['compid'] = $sql->compid;
        $data['compspecid'] = $sql->compspecid;
        $data['servno'] = $sql->servno;
        $data['address'] = $sql->address;
        $data['district'] = $sql->district;
        $data['department'] = $sql->department;
        $data['remarks'] = $sql->remarks;
        $data['findings'] = $sql->findings;
        $data['datecreated'] = $sql->datecreated;
        $data['createdby'] = $sql->createdby;
        $data['status'] = $sql->status;
        $data['priority'] = $sql->priority;

        echo json_encode($data);
    }

    function insert_customer_ticket(){

        $this->db->trans_begin();
        $data_insert = array(
            'compid' => $this->input->post('compid'),
            'compspecid' => $this->input->post('compspecid'),
            'servno' => $this->input->post('servno'),
            'name' => $this->input->post('name'),
            'address' => $this->input->post('address'),
            'district' => $this->input->post('district'),
            'department' => $this->input->post('department'),
            'remarks' => $this->input->post('remarks'),
            'findings' => $this->input->post('findings'),
            'datecreated' => $this->input->post('datecreated'),
            'createdby' => $this->input->post('createdby'),
            'status' => $this->input->post('status'),
            'priority' => $this->input->post('priority'),
        );

        $this->db->insert('trn_ticketing_logs_history' , $data_insert);
        //$this->db->_error_message();
        if($this->db->trans_status()===TRUE) {
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }
    }

    function getdailyrep(){

        $data = array();
        $daily_arr = array();
        $district_arr = array();
        $depart_arr = array();

        $qry_dte = $this->db->select("CAST(datecreated AS DATE) AS DATE")
            ->from("trn_ticketing_logs")
            ->group_by('CAST(datecreated AS DATE)')
            ->get();

        if($qry_dte->num_rows() > 0) {
            foreach($qry_dte->result() as $row) {

                $donecount = 0;
                $pendingcount = 0;
                $cancelledcount = 0;

                $this->db->where('CAST(datecreated AS DATE) = ', $row->DATE);
                $done = $this->db->select("COUNT(status) AS done")
                    ->from("trn_ticketing_logs")
                    ->where(array('status' => 305))
                    ->get()->row();

                $this->db->where('CAST(datecreated AS DATE) = ', $row->DATE);
                $pending = $this->db->select("COUNT(status) AS pending")
                    ->from("trn_ticketing_logs")
                    ->where(array('status' => 300))
                    ->get()->row();

                $this->db->where('CAST(datecreated AS DATE) = ', $row->DATE);
                $cancelled = $this->db->select("COUNT(status) AS cancelled")
                    ->from("trn_ticketing_logs")
                    ->where(array('status' => 303))
                    ->get()->row();

                if ($done) {
                    $donecount = $done->done;
                }
                if ($pending) {
                    $pendingcount = $pending->pending;
                }
                if ($cancelled) {
                    $cancelledcount = $cancelled->cancelled;
                }

                $daily_arr[] = array(
                    'date' => $row->DATE,
                    'done' => $donecount,
                    'pending' => $pendingcount,
                    'canceled' => $cancelledcount
                );
            }
        }

        $qry_dist = $this->db->select("COUNT(district) AS COUNT, pd.names AS DIST")
            ->from("trn_ticketing_logs AS ttl")
            ->join("prime_districts AS pd" , 'pd.sysid = ttl.district' , 'left')
            ->group_by('district')
            ->get();

        if($qry_dist->num_rows()>0) {
            foreach($qry_dist->result() as $row) {
                $district_arr[] = array(
                    'name' => $row->DIST,
                    'count' => $row->COUNT,
                    'color' => '#'.$this->random_color()
                );
            }
        }

        $sql_depart = $this->db->select("desc")
            ->from("prime_costcenter_main")
            ->get();

        if($sql_depart->num_rows()>0){
            foreach ($sql_depart->result() as $row){

                $departcount = $this->db->select("COUNT(ttl.department) AS deptcount")
                    ->from("trn_ticketing_logs as ttl")
                    ->join("prime_costcenter_main AS pcm",'pcm.sysid=ttl.department')
                    ->where(array('pcm.desc'=>$row->desc))
                    ->get()->row();

                $depart_arr[] = array(
                    'name' => $row->desc,
                    'departmentcount' => $departcount->deptcount,
                    'color' => '#'.$this->random_color()
                );

            }
        }

        $data['dailyarr'] = $daily_arr;
        $data['districtarr'] = $district_arr;
        $data['depart_arr'] = $depart_arr;

        echo json_encode($data);
    }

    function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }

    function random_color() {
        return $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }

    function testing() {

        $date_str = trim('  /  /    ');

        $dt_explode = explode('/', $date_str);

        if(!empty($dt_explode[0]) &&!empty($dt_explode[1]) && !empty($dt_explode[2])) {

            $old_prvdte = DateTime::createFromFormat('m/d/y', $date_str);

            if (!empty(trim($date_str))) {
                $old_prvdte = DateTime::createFromFormat('m/d/Y', $date_str);
                if ($old_prvdte && $old_prvdte->format('Y') > 1900) {
                    $prvdte = $old_prvdte->format('Y-m-d');
                } else {
                    $old_prvdte = DateTime::createFromFormat('m/d/y', $date_str);
                    $prvdte = $old_prvdte->format('Y-m-d');
                }
            } else {
                $prvdte = '1900-01-01';
            }
        }else{
            $prvdte = '1900-01-01';
        }

        echo $prvdte;
    }


    // IMPORT DATA
    function importusers() {
        $data = array();
        $data_arr = array();
        $file_info = pathinfo($_FILES["datafile"]["name"]);
        $roleid = $this->input->post('roleid');
        $filetype = '';
        $list = array();
        $qry = false;

        $this->db->trans_begin();

        if (!is_dir('temp')) {
            mkdir('temp', 0755, TRUE);
            chmod('temp', 0755);
        }
        $file_directory = "temp/";

        $new_file_name = date("d-m-Y") . rand(000000, 999999) . "." . $file_info["extension"];
        if (move_uploaded_file($_FILES["datafile"]["tmp_name"], $file_directory . $new_file_name)) {

            $filetype = $file_info["extension"];
            $file_type = PHPExcel_IOFactory::identify($file_directory . $new_file_name);
            $objReader = PHPExcel_IOFactory::createReader($file_type);
            $objPHPExcel = $objReader->load($file_directory . $new_file_name);
            $sheet_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            foreach ($sheet_data as $data) {
                $lastname = ($data['A']) ? ucwords(strtolower($data['A'])) : '';
                $firstname = ($data['B']) ? ucwords(strtolower($data['B'])) : '';
                $middlename = ($data['C']) ? ucwords(strtolower($data['C'])) : '';
                $unformated_username = trim(strtolower($firstname[0].$lastname));
                $username = str_replace(' ', '', $unformated_username);
                $password = encrypt_pass($username);

                $users_arr = array(
                    'username' => $username,
                    'password' => $password,
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'status' => 1,
                );
                $this->db->insert('prime_system_users', $users_arr);
                $this_user_id = $this->db->insert_id();
                $data['err'][] = $this->db->_error_message();

                $this->db->insert('prime_system_users_roles_matrix', array('roleid' => $roleid, 'userid' => $this_user_id));
                $data['err'][] = $this->db->_error_message();

                $name_full = $lastname . ', '. $firstname . ' ' . $middlename;
                $list[] = array(
                    'userid' => $this_user_id,
                    'username' => $username,
                    'name' => $name_full,
                    'password' => $username
                );
            }
        }

        if($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }
        $data['list'] = $list;
        echo json_encode($data);
    }

    function addshortuct() {
        echo $this->model_user->add_shortcut();
    }

    function delshortuct() {
        echo $this->model_user->dell_shortcut();
    }

    function getusershortcut() {
        echo $this->model_user->get_user_access();
    }

    function tellerapps() {
        $data = array();
        $data['pagetitle'] = 'Teller Check';
        init_header_nonav($data);
        echo '<style>body{background: #000 !important; color: #fff !important; padding: 20px 20px !important;}</style>';
        $conn = $this->load->database('pecoapps', TRUE);
        $conn->initialize();
        echo '<table class="table">';
        echo '<thead>';
        echo '<th>Code</th>';
        echo '<th>Name</th>';
        echo '<th class="hidden-xs number" style="text-align: right !important;">Cash</th>';
        echo '<th class="hidden-xs number" style="text-align: right !important;">Cheque</th>';
        echo '<th class="number" style="text-align: right !important;">Count</th>';
        echo '<th class="number" style="text-align: right !important;">Total</th>';
        echo '</thead>';
        echo '</tbody>';


        $total_chk = 0;
        $total_csh = 0;
        $total_amt = 0;
        $total_cnt = 0;

        $total_offc = 0;
        $total_bank = 0;

        $query = $conn->query("
                SELECT teller, COUNT(servno) AS CNT FROM teller GROUP BY teller ORDER BY teller
			");


        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $tcode = $row->teller;
                $qry_coll_row_chk = $conn->select('SUM(ISNULL(amtpd,0)) + SUM(ISNULL(intrst,0)) AS AMT')->from('teller')
                    ->where(array('teller' => $tcode, 'chk' => '/'))->get()->row();
                if($qry_coll_row_chk->AMT==0) {
                    $qry_coll_row_csh = $conn->select('SUM(ISNULL(amtpd,0)) + SUM(ISNULL(intrst,0)) AS AMT')->from('teller')
                        ->where(array('teller' => $tcode))->get()->row();
                }else{
                    $qry_coll_row_csh = $conn->select('SUM(ISNULL(amtpd,0)) + SUM(ISNULL(intrst,0)) AS AMT')->from('teller')
                        ->where(array('teller' => $tcode, 'chk <>' => '/'))->get()->row();
                }

                if($tcode>=50) {
                    $qry_tcode = $conn->select()->from('tellcode')->where('bcode', $tcode)->get()->row();
                    $tname = ($qry_tcode) ? $qry_tcode->bdesc : 'Unknown';
                }else{
                    $qry_tcode = $conn->select()->from('tblUsers')->where('USERID', $tcode)->get()->row();
                    $tname = ($qry_tcode) ? $qry_tcode->FIRSTNAME . ' ' . $qry_tcode->LASTNAME : 'Unknown';
                }

                $check_amt = $qry_coll_row_chk->AMT;
                $cash_amt = $qry_coll_row_csh->AMT;
                $total_amt = $check_amt + $cash_amt;

                $total_csh += $cash_amt;
                $total_chk += $check_amt;
                $total_cnt += $row->CNT;

                if($tcode<50) {
                    $total_offc += $total_amt;
                }
                if($tcode>=50) {
                    $total_bank += $total_amt;
                }

                echo '<tr>';
                echo '<td>'.$tcode.'</td>';
                echo '<td>'.$tname.'</td>';
                echo '<td class="number hidden-xs">'. number_format($cash_amt,2).'</td>';
                echo '<td class="number hidden-xs">'.number_format($check_amt,2).'</td>';
                echo '<td class="number">'.number_format($row->CNT).'</td>';
                echo '<td class="number">'.number_format($total_amt, 2).'</td>';
                echo '<tr>';
            }
        }

        $total_amt = ($total_chk+$total_csh);
        echo '<tr>';
        echo '<td></td>';
        echo '<td>Total</td>';
        echo '<td class="number hidden-xs" style="text-align: right !important;">'. number_format($total_chk,2).'</td>';
        echo '<td class="number hidden-xs" style="text-align: right !important;">'.number_format($total_csh,2).'</td>';
        echo '<td class="number" style="text-align: right !important;">'.number_format($total_cnt).'</td>';
        echo '<td class="number" style="text-align: right !important;">'.number_format($total_amt, 2).'</td>';
        echo '</tr>';
        echo '<tr>';


        echo '</tbody>';
        echo '</table>';

        echo '<hr>';
        echo '<p>Total Collection Office: <b>' . number_format($total_offc,2).'</b></p>';
        echo '<p>Total Collection Bank: <b>' . number_format($total_bank,2).'</b></p>';
    }

}
