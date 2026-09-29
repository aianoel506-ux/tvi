<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Settings extends CI_Controller {

    private $user_login;
    private $xml_mon;

    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_settings');
        $this->load->model('model_systems');
        $this->load->model('model_billing');
        $this->load->library('datatables');
        $this->user_login = $this->session->userdata('logged_in');
        //$this->xml_mon = "assets/db/statistics.xml";
        $this->xml_mon = "http://172.20.224.25/gitinspector/statistics.xml";
    }

    public function index() {
        redirect(base_url());
    }

    function systemcheck($var) {
        $data = array();
        $qry = false;

        if($var=='mode') {
            $qry_sys_settings = $this->db->select()
                ->from('system_settings')->where('codes', 'DEV')->get()->row();
            if($qry_sys_settings) {
                $qry = true;
                $data['dev'] = ($qry_sys_settings->status==1) ? true : false;
            }
        }

        $data['qry'] = $qry;
        echo json_encode($data);
    }

    public function flow() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/flow', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function attributes() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/attributes', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }


    public function tables() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/tables', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function types() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/types', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function icons() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/icons', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function modules() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $data['accesses'] = $this->model_settings->get_list_access_roles();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/modules', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }
    function getmodulesmain() {
        $data = array();
        $query = $this->model_settings->get_list_modules();
        if($query) {
            foreach($query as $row) {
                $qry_sub_num = $this->db->select()->from('prime_module_navigations_main')->where('parent', $row->sysid)->get()->num_rows();
                $stat = ($row->status==1) ? '<span class="text-success">Active</span>' : '<span class="text-danger">In-Active</span>';
                $data['list'][] = array(
                    'statint' => $row->status,
                    'expand' => '<i data-toggle="collapse" data-target="#expand_'.$row->sysid.'" data-id="'.$row->sysid.'" id="btn-expand" class="fa fa-plus-square-o"></i>',
                    'codes' => $row->code,
                    'names' => $row->name,
                    'descs' => $row->desc,
                    'icons' => '<i class="fa '.$row->icon.' text-'.$row->htmlclass.'"></i>',
                    'stats' => '<span class="label label-info">Sub: <span class="badge badge-danger">'.$qry_sub_num.'</span></span>' . '<span class="pull-right">'.$stat.'</span>',
                    'control' => '<button class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></button>'
                );
            }
        }
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }

    function moduleinfo() {
        echo $this->model_settings->get_module_info();
    }

    function dtmoduleslist() {
        echo $this->model_settings->dt_modules_list();
    }

    function addmodulenav() {
        echo $this->model_settings->add_module_nav();
    }

    function getsubnavs() {
        $id = $this->input->post('id');
        $qry = $this->db->select()->from('prime_module_navigations_main')->where('parent', $id)->get();
        $def = $this->db->select()->from('prime_module_navigations_main')->where('sysid', $id)->get()->row();
        if($qry->num_rows()>0 && $def) {
            $data[] = array('id' => $def->sysid, 'text' => $def->desc);
            foreach($qry->result() as $row) {
                $data[] = array('id' => $row->sysid, 'text' => $row->desc);
            }
        }
        echo json_encode($data);
    }


    public function access() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/access', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }


    function adduser() {
        echo $this->model_settings->add_user();
    }

    public function userlists() {
        echo $this->model_settings->get_users_lists();
    }

    function removerole() {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $this->db->where(array('sysid' => $id));
            $qry = $this->db->update('prime_system_users_roles_matrix',array('status' => 0));
            if ($qry) {
                $data['q'] = true;
                $data['msg'] = "Role has been remove!";
            } else {
                $data['q'] = false;
            }
            echo json_encode($data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function get_buttons($id, $page) {
        $html = '<span class="btn-group">';
        $html .= '<a target="_blank" class="btn btn-info btn-xs view" href="' . base_url() . 'page/view/' . $id . '"><i class="fa fa-search"></i></a>';
        $html .= '<a class="btn btn-warning btn-xs edit" href="' . base_url() . 'edit/' . $page . '/' . $id . '"><i class="fa fa-pencil"></i></a>';
        $html .= '<a class="btn btn-danger btn-xs stat" href="' . base_url() . 'edit/stats/' . $page . '/' . $id . '"><i class="fa fa-refresh"></i></a>';
        $html .= '</span>';
        return $html;
    }

    public function projectmon() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();

            $xmlfile = $this->xml_mon;
            $xmlRaw = file_get_contents($xmlfile);
            $xdata = $this->simplexml->xml_parse($xmlRaw);
            $data['xdata'] = $xdata;

            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/projectmon', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    function updatemarlon() {
        $qry = $this->db->select()->from('system_monitoring_version_details')->where('authid', 5)->get();
        foreach($qry->result() as $row) {
            $rand_commits = rand(90, 500);
            $rand_deletions = rand(90, 400);
            $rand_insertions = rand(90, 400);
            $rand_changes = rand(3.9, 40.99);
            $upd_arr = array(
                'commits' => $rand_commits,
                'insertion' => $rand_deletions,
                'deletion' => $rand_insertions,
                'changes' => $rand_changes
            );
            $this->db->where(array('authid' => 5, 'sysid' => $row->sysid));
            $this->db->update('system_monitoring_version_details', $upd_arr);
        }
    }


    function getprojmondata() {
        $msg = '';
        $data = array();
        $xmlfile = $this->xml_mon;
        $xmlRaw = file_get_contents($xmlfile);
        $xdata = $this->simplexml->xml_parse($xmlRaw);
        $version_code = $xdata['version'];
        $version_date = date_formating($xdata['report-date'], 'Y/m/d', 'Y-m-d');

        $git_qry_main = $xdata['changes']['authors']['author'];

        foreach ($git_qry_main as $row) {
            $author_new = false;
            $version_new = false;
            $auth_name = strtolower($row['name']);

            // AUTHOR QUERY
            $check_auth = $this->db->select()->from('system_monitoring_authors')->where('codes', $auth_name)->get()->row();
            if ($check_auth) {
                $auth_sysid = $check_auth->sysid;
            } else {
                // INSERT NEW AUTHOR
                $new_auth_arr = array(
                    'codes' => $auth_name,
                    'descs' => $auth_name,
                );
                $this->db->insert('system_monitoring_authors', $new_auth_arr);
                $auth_sysid = $this->db->insert_id();
                $author_new = true;
            }

            // VERSION QUERY
            $check_ver = $this->db->select()->from('system_monitoring_authors_versions')
                ->where(array('authid' => $auth_sysid, 'verdate' => $version_date))->get()->row();
            if ($check_ver) {
                $ver_id = $check_ver->sysid;
            } else {
                // INSERT NEW VERSION
                $new_ver_arr = array(
                    'authid' => $auth_sysid,
                    'verdate' => $version_date,
                );
                $this->db->insert('system_monitoring_authors_versions', $new_ver_arr);
                $ver_id = $this->db->insert_id();
                $version_new = true;
            }
            if ($version_new == true || $author_new == true) {
                // INSERT VERION DETAILS
                $ver_details_arr = array(
                    'verid' => $ver_id,
                    'authid' => $auth_sysid,
                    'commits' => $row['commits'],
                    'insertion' => $row['insertions'],
                    'deletion' => $row['deletions'],
                    'changes' => $row['percentage-of-changes']
                );
                $this->db->insert('system_monitoring_version_details', $ver_details_arr);
                $ver_details_id = $this->db->insert_id();
            } else {
                if ($check_auth && $check_ver && $version_new == true) {
                    // INSERT VERION DETAILS
                    $ver_details_arr = array(
                        'verid' => $ver_id,
                        'authid' => $auth_sysid,
                        'commits' => $row['commits'],
                        'insertion' => $row['insertions'],
                        'deletion' => $row['deletions'],
                        'changes' => $row['percentage-of-changes']
                    );
                    $this->db->insert('system_monitoring_version_details', $ver_details_arr);
                    $ver_details_id = $this->db->insert_id();
                    $msg = 'INSERT NEW DETAILS VER EXIST AUTH EXIST';
                } else {
                    if ($version_new == true) {
                        // INSERT VERION DETAILS
                        $ver_details_arr = array(
                            'verid' => $ver_id,
                            'authid' => $auth_sysid,
                            'commits' => $row['commits'],
                            'insertion' => $row['insertions'],
                            'deletion' => $row['deletions'],
                            'changes' => $row['percentage-of-changes']
                        );
                        $this->db->insert('system_monitoring_version_details', $ver_details_arr);
                        $ver_details_id = $this->db->insert_id();
                        $msg = 'INSERT NEW DETAILS VER EXIST AUTH EXIST';
                    }
                }
            }
        }
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function getprojmongraph() {
        $toDay = date('Y-m-d');
        $data = array();
        $xmlfile = $this->xml_mon;
        $xmlRaw = file_get_contents($xmlfile);
        $xdata = $this->simplexml->xml_parse($xmlRaw);
        $qry = $xdata['changes'];

        foreach ($qry['authors']['author'] as $row) {
            $data['commits'][] = array(dev_name_case($row['name']), $row['commits']);
            $data['insertions'][] = array(dev_name_case($row['name']), $row['insertions']);
            $data['deletions'][] = array(dev_name_case($row['name']), $row['deletions']);
        }

        $qry_versions = $this->db->select('v.verdate, SUM(d.commits) AS comm, SUM(d.changes) AS chg, SUM(d.insertion) AS ins, SUM(d.deletion) AS del')
            ->from('system_monitoring_authors_versions AS v')
            ->join('system_monitoring_version_details AS d', 'v.sysid = d.verid', 'left')
            ->group_by('v.verdate')->get();
        $ver_nums = $qry_versions->num_rows();
        if ($ver_nums > 0) {
            $i = 0;
            $len = $ver_nums;
            foreach ($qry_versions->result() as $row) {
                //$bulletClass = ($toDay>=$row->verdate) ? 'lastBullet' : '';
                if ($i == 0) {
                    // first
                    $bulletClass = '';
                } else if ($i == $len - 1) {
                    // last
                    $bulletClass = 'lastBullet';
                }
                // …
                $i++;
                $data['versions'][] = array(
                    'date' => $row->verdate,
                    'value' => ($row->comm > 0) ? $row->comm : 0,
                    'insertions' => $row->ins,
                    'deletions' => ($row->del > 0) ? $row->del : 0,
                    'changes' => ($row->chg > 0) ? $row->chg : 0,
                    'bulletClass' => $bulletClass
                );
            }
        }


        $qry_dev = $this->db->select('a.sysid, '
            . 'a.descs, '
            . 'SUM(d.commits) AS acts, '
            . 'SUM(d.deletion) AS dels, '
            . 'SUM(d.insertion) AS ins, '
            . 'SUM(d.changes) AS chg ')
            ->from('system_monitoring_authors AS a')
            ->join('system_monitoring_version_details AS d', 'd.authid = a.sysid', 'left')
            ->group_by('a.sysid, a.descs')
            ->get();
        $auth_nums = $qry_dev->num_rows();
        $auth_res = $qry_dev->result();
        if ($auth_nums > 0) {
            $total_deletion = 0;
            $total_insertion = 0;
            foreach ($auth_res as $row) {
                $total_deletion += $row->dels;
                $total_insertion += $row->ins;
            }

            foreach ($auth_res as $trow) {
                if ($row->dels >= 1000000) {
                    $del_per = ($trow->dels / $total_deletion);
                } else {
                    $del_per = $trow->dels;
                }
                if ($row->dels >= 1000000) {
                    $ins_per = ($trow->ins / $total_insertion);
                } else {
                    $ins_per = $trow->ins;
                }
                $qry_auth_details = $this->db->select()->from('system_monitoring_authors')
                    ->where('sysid', $trow->sysid)->get()->row();
                $colors = ($qry_auth_details) ? $qry_auth_details->colors : '#CCFF00';
                $pics = ($qry_auth_details) ? $qry_auth_details->pics : '';

                $data['dev'][] = array(
                    'authid' => $trow->sysid,
                    'name' => $trow->descs,
                    'activities' => $trow->acts,
                    'commits' => $trow->acts,
                    'deletion' => $del_per,
                    'insertion' => $ins_per,
                    'changes' => $trow->chg,
                    'color' => $colors,
                    'pics' => $pics,
                );
            }
        }

        echo json_encode($data);
    }

    function getprojmonchanges() {
        $data = array();
        $xmlfile = $this->xml_mon;
        $xmlRaw = file_get_contents($xmlfile);
        $xdata = $this->simplexml->xml_parse($xmlRaw);
        $qry = $xdata['changes'];

        foreach ($qry['authors']['author'] as $row) {
            $data['list'][] = array(
                'pic' => '<img src="' . $row['gravatar'] . '" width="30px" />',
                'name' => dev_name_case($row['name']),
                'commits' => number_format($row['commits']),
                'insertion' => number_format($row['insertions']),
                'deletion' => number_format($row['deletions']),
                'percent' => number_format($row['percentage-of-changes']),
            );
        }

        $data['message'] = $qry['message'];
        echo json_encode($data);
    }

    function getprojmonaging() {
        $data = array();
        $xmlfile = $this->xml_mon;
        $xmlRaw = file_get_contents($xmlfile);
        $xdata = $this->simplexml->xml_parse($xmlRaw);
        $qry = $xdata['blame'];

        foreach ($qry['authors']['author'] as $row) {
            $data['list'][] = array(
                'pic' => '<img src="' . $row['gravatar'] . '" width="30px" />',
                'name' => dev_name_case($row['name']),
                'rows' => number_format($row['commits']),
                'age' => number_format($row['age']),
                'percent' => number_format($row['percentage-in-comments']),
            );
        }

        $data['message'] = $qry['message'];
        echo json_encode($data);
    }

    function getprojmonmatrics() {
        $data = array();
        $xmlfile = $this->xml_mon;
        $xmlRaw = file_get_contents($xmlfile);
        $xdata = $this->simplexml->xml_parse($xmlRaw);
        $qry = $xdata['metrics'];


        foreach ($qry['violations']['estimated-lines-of-code'] as $row) {
            $data['list'][] = array(
                'name' => $row['file-name'],
                'line' => number_format($row['value']),
            );
        }

        $data['message'] = '';
        echo json_encode($data);
    }

    function getprojmonresponsibility() {
        $data = array();
        $xmlfile = $this->xml_mon;
        $xmlRaw = file_get_contents($xmlfile);
        $xdata = $this->simplexml->xml_parse($xmlRaw);
        $qry = $xdata['responsibilities'];


        foreach ($qry['authors']['author'] as $row) {
            $html_files = '';
            $html_files .= '<ul class="list-group no">';
            foreach ($row['files']['file'] as $frow) {
                $html_files .= '<li class="list-group-item">' . $frow['rows'] . ' - ' . $frow['name'] . '</li>';
            }
            $html_files .= '</ul>';
            $data['list'][] = array(
                'pic' => '<img src="' . $row['gravatar'] . '" width="30px" />',
                'name' => dev_name_case($row['name']),
                'files' => $html_files
            );
        }

        $data['message'] = $qry['message'];
        echo json_encode($data);
    }

    public function database() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/database', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function roles() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/roles', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function testing() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/modules/testing', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function migratefather() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $data['pagetitle'] = 'Father Update';
            $this->load->view('admin/common/head', $data);
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/settings/migrate_father', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }
    public function migratebilltrn() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $data['pagetitle'] = 'Father Update';
            $this->load->view('admin/common/head', $data);
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/pages/settings/migrate_billtrn', $data);
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    function testquery() {
        echo $this->model_settings->test_query();
    }


    public function debug() {
        if ($this->user_login && $this->user_login['system_user_sesstype'] == 1) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            $this->load->view('admin/common/head');
            $this->load->view('admin/common/topnav', $data);
            $this->load->view('admin/common/leftnav', $data);
            $this->load->view('admin/common/debug');
            $this->load->view('admin/common/footer');
            $this->load->view('admin/common/scripts');
            $this->load->view('admin/common/end');
        } else {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    // TEST QUERIES
    function person() {

        $this->datatables->select('p.sysid, addr.addrspec');
        $this->datatables->select("CONCAT(p.lastname,', ', p.firstname, ' ', p.middlename) as name", false);
        $this->datatables->add_column('mtr', '1');
        $this->datatables->add_column('servno', 'M000001');
        $this->datatables->add_column('due', date('Y-m-d'));
        $this->datatables->add_column('interest', '0.00');
        $this->datatables->add_column('prevamt', '0.00');
        $this->datatables->add_column('current', '0.00');
        $this->datatables->add_column('total', '0.00');
        $this->datatables->add_column('status', '<span class="label label-success">Printed</span>');
        $this->datatables->add_column('expand', '$1', 'btn_expand(sysid)');
        $this->datatables->add_column('control', '<button class="btn btn-info btn-xs"><i class="fa fa-search"></i></button><button class="btn btn-default btn-xs"><i class="fa fa-book"></i></button>');
        $this->datatables->from("person p");
        $this->datatables->join("person_address_matrix addr", 'addr.personid = p.sysid', 'left');
        $this->datatables->where("p.sysid >= ", 10);
        echo $this->datatables->generate();
    }

    function subtable() {
        $id = $this->input->post('id');
        $this->datatables->select('p.sysid, addr.addrspec');
        $this->datatables->select("CONCAT(p.lastname,', ', p.firstname, ' ', p.middlename) as name", false);
        $this->datatables->unset_column('name')->add_column('name', '$1', "tbl_input('inline', 'name', 'Editable..')");
        $this->datatables->add_column('mtr', '1');
        $this->datatables->add_column('servno', 'M000001');
        $this->datatables->add_column('due', date('Y-m-d'));
        $this->datatables->add_column('current', '0.00');
        $this->datatables->add_column('total', '0.00');
        $this->datatables->add_column('status', '<span class="label label-success">Printed</span>');
        $this->datatables->add_column('expand', '$1', 'btn_expand(sysid)');
        $this->datatables->add_column('control', '<button class="btn btn-info btn-xs"><i class="fa fa-search"></i></button><button class="btn btn-default btn-xs"><i class="fa fa-book"></i></button>');
        $this->datatables->from("person p");
        $this->datatables->join("person_address_matrix addr", 'addr.personid = p.sysid', 'left');
        $this->datatables->where("p.sysid", $id);
        echo $this->datatables->generate();
    }

    function getinfo() {
        $id = $this->input->post('id');
        $q = $this->db->select()->from('person')->where('sysid', $id)->get()->row();
        $rate = 0;
        $kwh = 0;
        $genamt = 0;
        $genchrg = 0;
        $prevbill = 0;
        $prevint = 0;
        $prevvat = 0;
        $prevtotal = 0;
        $curamt = 0;
        $curint = 0;
        $curvat = 0;
        $curtotal = 0;
        $total = 0;
        $billdate = 0;
        $billno = 0;
        $billcount = 0;
        if ($q) {
            $increas = rand(0.01, 0.05);
            $charges = rand(2000, 6000);

            $qry = true;
            $name = $q->firstname;
            $kwh = rand(200, 1200);
            $genchrg = 7.9009;
            $genamt = ($kwh * $genchrg);

            $cur_amt = ($genamt + $charges);

            $prevbill = $cur_amt;


            $billcount = 2;

            $prevint = rand(95, 120);
            $prevtotal = ($prevbill + $prevint);

            $amt_increase = ($prevtotal * $increas);

            $curamt = ($prevtotal + $amt_increase + rand(100, 500));
            $curint = 0;
            $curtotal = ($curamt + $curint);
            $total = ($prevtotal + $curtotal);
            $billdate = date('Y-m-d');
            $billno = '0000001';
        } else {
            $qry = false;
            $name = '';
        }
        $data['dateprint'] = date('Y-m-d');
        $data['datedelevered'] = date('Y-m-d');
        $data['datebilled'] = date('Y-m-d');
        $data['rate'] = $rate;
        $data['kwh'] = number_format($kwh);
        $data['genamt'] = number_format($genamt);
        $data['genchrg'] = number_format($genchrg, 5);
        $data['prevbill'] = number_format($prevbill, 2);
        $data['prevint'] = number_format($prevint, 2);
        $data['prevvat'] = number_format($prevvat, 2);
        $data['prevtotal'] = number_format($prevtotal, 2);
        $data['curamt'] = number_format($curamt, 2);
        $data['curint'] = number_format($curint, 2);
        $data['curvat'] = number_format($curvat, 2);
        $data['curtotal'] = number_format($curtotal, 2);
        $data['total'] = number_format($total, 2);
        $data['billdate'] = $billdate;
        $data['billno'] = $billno;
        $data['billcount'] = $billcount;
        $data['qry'] = $qry;
        $data['name'] = $name;
        echo json_encode($data);
    }
    function getmainflowtrndata(){
        $data = array();
        $sql = $this->db->select("sysid,codes,names,desc")
            ->from("prime_transaction_flow_main")
            ->where(array("status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $control = '<a class="btn btn-info inline" href="#tbl_edit_transaction_flow" title="Edit Transaction Flow" data-arr="'.$row->sysid.'" data-toggle="ajax-modal"><i class="fa fa-edit"></i> Edit</a>';
                $data['trnlist'][] = array(
                    "expand" => $row->sysid,
                    "flowid" => $row->sysid,
                    'codes' => $row->codes,
                    'names' => $row->names,
                    'desc' => $row->desc,
                    'control' => $control
                );
            }
        }
        echo json_encode($data);
    }

    function gettrnflowmainstages(){
        $data = array();
        $id = $this->input->post('id');
        $html = '';
        $sql = $this->db->select()
            ->from("prime_transaction_flow_main_stages")
            ->where(array("flowid" => $id , "status" => 1))
            ->order_by('levels ASC')
            ->get();

        $numrows = $sql->num_rows();
        if($numrows > 0){
            $html .= '<table class="table table-condensed table-bordered table-hover tbl-sm">';
            $html .= '<thead>';
            $html .= '<th>Levels</th>';
            $html .= '<th>Descriptions</th>';
            $html .= '<th>Module ID</th>';
            $html .= '<th>Types</th>';
            $html .= '<th>Status</th>';
            $html .= '</thead>';

            $html .= '<tbody>';

            $num = 1;
            foreach ($sql->result() as $row){
                $html .= '<tr>';
                $html .= '<td>'.$row->levels.'</td>';
                $html .= '<td>'.$row->desc.'</td>';
                $html .= '<td>'.$row->moduleid.'</td>';
                $html .= '<td>'.$row->types.'</td>';
                $html .= '<td>'.$row->status.'</td>';
                $html .= '</tr>';

                $move = '';
                /*if ($num != 1) {
                    $move .= '<button type="button" class="btn btn-info btn-xs inline" id="move_top" data-action="1"><i class="fa fa-fast-backward fa-rotate-90"></i></button>';
                    $move .= '<button type="button" class="btn btn-success btn-xs inline" id="move_up" data-action="2"><i class="fa fa-step-backward fa-rotate-90"></i> Up</button>';
                }

                if ($num != $numrows) {
                    $move .= '<button type="button" class="btn btn-warning btn-xs inline" id="move_down" data-action="3"><i class="fa fa-step-forward fa-rotate-90"></i> Down</button>';
                    $move .= '<button type="button" class="btn btn-danger btn-xs inline" id="move_bottom" data-action="4"><i class="fa fa-fast-forward fa-rotate-90"></i></button>';
                }*/
                $top_hide = ($num == 1) ? 'hidden' : '';
                $bottom_hide = ($num == $numrows) ? 'hidden' : '';

                $move .= '<button data-flowid="'.$row->flowid.'" data-id="'.$row->sysid.'" data-level="'.$row->levels.'" type="button" class="btn btn-info btn-xs inline btn_'.$row->sysid.' '.$top_hide.'" id="move_top" data-action="1"><i class="fa fa-fast-backward fa-rotate-90"></i> Top</button>';
                $move .= '<button data-flowid="'.$row->flowid.'" data-id="'.$row->sysid.'" data-level="'.$row->levels.'" type="button" class="btn btn-success btn-xs inline btn_'.$row->sysid.' '.$top_hide.'" id="move_up" data-action="2"><i class="fa fa-step-backward fa-rotate-90"></i> Up</button>';
                $move .= '<button data-flowid="'.$row->flowid.'" data-id="'.$row->sysid.'" data-level="'.$row->levels.'" type="button" class="btn btn-warning btn-xs inline btn_'.$row->sysid.' '.$bottom_hide.'" id="move_down" data-action="3"><i class="fa fa-step-forward fa-rotate-90"></i> Down</button>';
                $move .= '<button data-flowid="'.$row->flowid.'" data-id="'.$row->sysid.'" data-level="'.$row->levels.'" type="button" class="btn btn-danger btn-xs inline btn_'.$row->sysid.' '.$bottom_hide.'" id="move_bottom" data-action="4"><i class="fa fa-fast-forward fa-rotate-90"></i> Last</button>';

                $control = '<button type="button" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button>';
                $data['stagelist'][] = array(
                    'moduleid' => $row->moduleid . ' - ' . get_module_name($row->moduleid)->name.'<input type="hidden" value="'.$row->moduleid.'" id="module_id">',
                    'levels' => $row->levels /*.'<input type="hidden" value="'.$row->levels.'" id="stage_level">'*/,
                    'desc' => $row->desc,
                    'move' => $move,
                    'control' => $control,
                );

                $num++;
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        $data['html'] = $html;
        echo json_encode($data);
    }

    function gettransactionflowdetails() {
        $data = array();
        $html = $this->load->view('admin/pages/settings/flowdetails', $this->input->post(),true);

        $data['html'] = $html;
        echo json_encode($data);
    }

    function getuserroles(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $data['dataid'] = $dataid;
        $num = 0;
        $controls = '';
        $match = 0;
        $initsystemroles = $this->db->select("sysid,code,descriptions")
            ->from("prime_system_users_roles_main")
            ->get();
        if($initsystemroles->num_rows() > 0){
            foreach ($initsystemroles->result() as $rolesrow){

                $checkrole = $this->db->select('userid,roleid,status')
                    ->from("prime_system_users_roles_matrix")
                    ->where(array("status" => 1 , "userid" => $dataid))
                    ->get();
                $controls = '<input class="checked icheck checkboxrole"  id="checkboxrole" type="checkbox" data-id="'.$rolesrow->sysid.'" name="userole[' . $rolesrow->sysid . ']" value="' . $rolesrow->sysid . '"/>';
                foreach ($checkrole->result() as  $row){
                    if($row->roleid == $rolesrow->sysid){
                        $match++;
                        $controls = '<input checked="checked" class="checked icheck checkboxrole" id="checkboxrole" data-id="'.$rolesrow->sysid.'"  type="checkbox" name="userole[' . $rolesrow->sysid . ']" value="' . $rolesrow->sysid . '"/>';
                    }
                }

                $num++;
                $data['roleslist'][] = array(
                    'num' => $num,
                    'code' => $rolesrow->code,
                    'descs' => $rolesrow->descriptions,
                    'control' => $controls
                );
            }
        }
        $data['match'] = $match;
        echo json_encode($data);
    }

    function fetchusergroupdata(){
        $data = array();

        $sql = $this->db->select("firstname,lastname")
            ->from("prime_system_users")
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['usergroupdata'][] = array(
                    'id' => $num++,
                    'firstname' => $row->firstname,
                    'lastname' => $row->lastname,
                    'test' => ''
                );
            }
        }
        echo json_encode($data);
    }

    function deleteuser(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func  = '';
        $title = '';
        $qry = false;
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid));
        $this->db->update("prime_system_users" , $updatearr);
        if($this->db->trans_status() === true){
            $this->db->trans_commit();
            $msg = 'User has been deleted.';
            $func  = 'success';
            $title = 'Delete';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to delete user.';
            $func  = 'error';
            $title = 'Error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;
        echo json_encode($data);
    }


    function userpages() {
        echo '<br>nav ids<br>';
        $a = get_users_info_navigation_ids();
        print_r($a);
        echo '<br>role ids<br>';
        $a = get_users_roles_matrix_id_arr();
        print_r($a);
    }

    function gettemp() {

        $obj = new stdClass();
        $cmd = "cat /sys/class/thermal/thermal_zone0/temp";
        exec($cmd . "  2>&1", $output, $return_val);
        $obj->unit = '°';
        if ($return_val !== 0) {
            $obj->error = "Get t°  ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "t° Raspberry";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
            $obj->percent = 0;
            $obj->percent = $output[0]/1000;
            // $obj->percent = intval($this->getServerLoad());
            $value = $output[0]/1000;
            if($value>75) {
                insert_system_logs(1077, $value, 'Server Logs: Temperature', 1);
            }
        }
        echo json_encode($obj);
    }
    function getcpu()
    {

        $obj = new stdClass();
        $cmd = "cat /proc/cpuinfo";
        exec($cmd . "  2>&1", $output, $return_val);
        $obj->unit = '%';
        if ($return_val !== 0) {
            $obj->error = "Get CPU ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
            $obj->percent = intval($this->getServerLoad());
            // find model name
            foreach ($output as $value) {
                if (preg_match("/model name.+:(.*)/i", $value, $match)) {
                    $obj->title = $match[1];
                    break;
                }
            }
            $value = intval($this->getServerLoad());
            if($value>75) {
                insert_system_logs(1074, $value, 'Server Logs: CPU', 1);
            }
        }
        echo json_encode($obj);
    }
    function getmem()
    {
        $obj = new stdClass();
        $cmd = "free";
        exec($cmd . "  2>&1", $output, $return_val);
        $obj->unit = '%';
        if ($return_val !== 0) {
            $obj->error = "Get Memmory ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
            $obj->memTotalBytes = 0;
            $obj->memUsedBytes = 0;
            $obj->memFreeBytes = 0;
            if (preg_match("/Mem: *([0-9]+) *([0-9]+) *([0-9]+) */i", $output[1], $match)) {
                $obj->memTotalBytes = $match[1]*1024;
                $obj->memUsedBytes = $match[2]*1024;
                $obj->memFreeBytes = $match[3]*1024;
                $onePc = $obj->memTotalBytes / 100;
                $obj->memTotal = $this->humanFileSize($obj->memTotalBytes);
                $obj->memUsed = $this->humanFileSize($obj->memUsedBytes);
                $obj->memFree = $this->humanFileSize($obj->memFreeBytes);
                $obj->percent = intval($obj->memUsedBytes / $onePc);
                $obj->title = "Total: {$obj->memTotal} | Free: {$obj->memFree} | Used: {$obj->memUsed}";

                $value = intval($obj->memUsedBytes / $onePc);
                if($value>75) {
                    insert_system_logs(1075, $value, 'Server Logs: RAM', 1);
                }
            }
        }
        echo json_encode($obj);
    }

    function getdisk() {
        $obj = new stdClass();
        $cmd = "df -h";
        exec($cmd . "  2>&1", $output, $return_val);
        $obj->unit = '%';
        if ($return_val !== 0) {
            $obj->error = "Get Disk ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->percent = 0;
            foreach ($output as $value) {
                if (preg_match("/([0-9]+)% \/$/i", $value, $match)) {
                    $obj->percent = intval($match[1]);

                    $value = intval($match[1]);
                    if($value>75) {
                        insert_system_logs(1076, $value, 'Server Logs: Disk', 1);
                    }
                    break;
                }
            }
            $obj->title = "Usage of {$obj->percent}%";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
        }
        echo json_encode($obj);
    }

    function humanFileSize($size, $unit = "") {
        if ((!$unit && $size >= 1 << 30) || $unit == "GB")
            return number_format($size / (1 << 30), 2) . "GB";
        if ((!$unit && $size >= 1 << 20) || $unit == "MB")
            return number_format($size / (1 << 20), 2) . "MB";
        if ((!$unit && $size >= 1 << 10) || $unit == "KB")
            return number_format($size / (1 << 10), 2) . "KB";
        return number_format($size) . " bytes";
    }

    function _getServerLoadLinuxData() {
        if (is_readable("/proc/stat")) {
            $stats = @file_get_contents("/proc/stat");

            if ($stats !== false) {
                // Remove double spaces to make it easier to extract values with explode()
                $stats = preg_replace("/[[:blank:]]+/", " ", $stats);

                // Separate lines
                $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
                $stats = explode("\n", $stats);

                // Separate values and find line for main CPU load
                foreach ($stats as $statLine) {
                    $statLineData = explode(" ", trim($statLine));

                    // Found!
                    if
                    (
                        (count($statLineData) >= 5) &&
                        ($statLineData[0] == "cpu")
                    ) {
                        return array(
                            $statLineData[1],
                            $statLineData[2],
                            $statLineData[3],
                            $statLineData[4],
                        );
                    }
                }
            }
        }

        return null;
    }

    // Returns server load in percent (just number, without percent sign)
    function getServerLoad() {
        $load = null;

        if (stristr(PHP_OS, "win")) {
            $cmd = "wmic cpu get loadpercentage /all";
            @exec($cmd, $output);

            if ($output) {
                foreach ($output as $line) {
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $load = $line;
                        break;
                    }
                }
            }
        } else {
            if (is_readable("/proc/stat")) {
                // Collect 2 samples - each with 1 second period
                // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
                $statData1 = $this->_getServerLoadLinuxData();
                sleep(1);
                $statData2 = $this->_getServerLoadLinuxData();

                if
                (
                    (!is_null($statData1)) &&
                    (!is_null($statData2))
                ) {
                    // Get difference
                    $statData2[0] -= $statData1[0];
                    $statData2[1] -= $statData1[1];
                    $statData2[2] -= $statData1[2];
                    $statData2[3] -= $statData1[3];

                    // Sum up the 4 values for User, Nice, System and Idle and calculate
                    // the percentage of idle time (which is part of the 4 values!)
                    $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

                    // Invert percentage to get CPU time, not idle time
                    $load = 100 - ($statData2[3] * 100 / $cpuTime);
                }
            }
        }

        return $load;
    }

    function pdfgentest() {
        $result = false;
        $data = array();
        // @TODO get address book from customer emails.
        $email = 'lucky.faderon@panayelectric.com';
        $email = 'marlon.varon@panayelectric.com';
        $email = 'lfaderon@gmail.com';
        $id = $this->input->post('id');
        $id = 7012608;


        $qry_bill_main = $this->db->select('month, year, acctid')
            ->from('billing_reports_main')
            ->where(array('sysid' => $id))
            ->get()->row();
        if($qry_bill_main) {
            $acctid = $qry_bill_main->acctid;
            $month = $qry_bill_main->month;
            $year = $qry_bill_main->year;
            $qry_bill = $this->db->select(
                '
                billno,
                acctid,
                group,
                dist,
                lot,
                book,
                servno,
                mtr,
                mtrser,
                serial,
                name,
                addr,
                bmo,
                byr,
                month,
                year,
                prvdte,
                prsdte,
                duedate,
                load,
                rate,
                prvrdg,
                prsrdg,
                multcd,
                kwhuse,
                genamt,
                genamt1,
                trnamt,
                disamt,
                demamt,
                supamt,
                supper,
                mtramt,
                slamt,
                iccamt,
                iccsub,
                llramt,
                llrsub,
                lldamt,
                misamt,
                envamt,
                framt,
                npcamt,
                iccsamt,
                papc,
                fitamt,
                genchg,
                genchg1,
                trnchg,
                dischg,
                demchg,
                supchg,
                mtrchg,
                mtrper,
                slchg,
                mischg,
                envchg,
                npcchg,
                iccschg,
                fitchg,
                papcchg,
                genvat,
                trnvat,
                disvat,
                slvat,
                othvat,
                appsur,
                surbal,
                current,
                overdue,
                totacc,
                totint,
                scdisc,
                dolpay
                '
                )
                ->from('billing_reports')
                ->where(array('acctid' => $acctid, 'year' => $year, 'month' => $month))
                ->get()->row();

            $content = $this->model_systems->bill_form($qry_bill);

            $filename = $qry_bill->servno . '_' . $qry_bill->year . '-' . str_pad($qry_bill->month, 2, '0', STR_PAD_LEFT) . '.pdf';

            // Load all views as normal
            // $this->load->view('sample_tcpdf.php');
            // Get output html
            // $html = $this->output->get_output();

            $bill_month = $qry_bill->month;
            $bill_year = $qry_bill->year;

            // Load library
            $this->load->library('pdf');

            $dompdf = new Dompdf\Dompdf();

            $dompdf->loadHtml($content);

            $customPaper = array(0, 0, 610, 910);
            $dompdf->setPaper($customPaper, 'portrate');
            $dompdf->render();
            // Add PDF Document Information
            $dompdf->add_info('Subject', 'PECO BILL | ' . $filename);
            $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
            $dompdf->add_info('Creator', 'Lucky John Faderon - SE');
            $dompdf->add_info('Keywords', 'BILL');

            //$output = $dompdf->output();

            $dt = DateTime::createFromFormat('m', $bill_month);
            $monthcode = strtoupper($dt->format('M'));

            //file_put_contents(FCPATH . 'uploads/billing/' . $filename, $output);

            //SMTP & mail configuration
            $this->load->library('email');
            $smtp = 'local';

            $config = array();

            if($smtp=='google') {
                $config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'ssl://smtp.googlemail.com',
                    'smtp_port' => 465,
                    'smtp_user' => 'bills.peco@gmail.com',
                    'smtp_pass' => 'P3C02018!!',
                    'mailtype' => 'html',
                    'charset' => 'utf-8'
                );
            }

            if($smtp=='yahoo') {
                // YAHOO DON'T ALLOW THIRD PARTY SUCH PHP
                $config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'ssl://smtp.bizmail.yahoo.com',
                    'smtp_port' => 465,
                    'smtp_user' => 'billing@panayelectric.com',
                    'smtp_pass' => 'p3c0.@dm!n2k17',
                    // 'smtp_pass' => 'warpniryzjrajcrg',
                    'mailtype' => 'html',
                    'charset' => 'utf-8'
                );
            }

            if($smtp=='local') {
                // YAHOO DON'T ALLOW THIRD PARTY SUCH PHP
                $config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'mailto.panayelectric.com',
                    'smtp_port' => 25,
                    'smtp_user' => 'lucky',
                    'smtp_from_name' => 'billing@panayelectric.com',
                    'smtp_pass' => 'P3C02018',
                    // 'smtp_pass' => 'warpniryzjrajcrg',
                    'mailtype' => 'html',
                    'charset' => 'utf-8'
                );
            }

            $data['sending'] = $smtp;
            $data['to'] = $email;

            $this->email->initialize($config);
            $this->email->set_mailtype("html");
            $this->email->set_newline("\r\n");

            // Email content
            $this->email->to($email);
            $this->email->from('billing@panayelectric.com', 'PECO BILL | ' . $monthcode . '-' . $bill_year);
            $this->email->subject('PECO BILL | ' . $monthcode . '-' . $bill_year);
            $this->email->message($this->model_systems->bill_html($qry_bill));
            //$this->email->attach(FCPATH . 'uploads/billing/' . $filename);

            // Send email
            $result = $this->email->send();
            // $result = true;
            // $data['err'] = $this->email->print_debugger();


        }
        $data['qry'] = $result;
        echo json_encode($data);
    }

    function getbillformsample(){
        $qry_bill = $this->db->select(
            '
            billno,
            acctid,
            group,
            dist,
            lot,
            book,
            servno,
            mtr,
            mtrser,
            serial,
            name,
            addr,
            bmo,
            byr,
            month,
            year,
            prvdte,
            prsdte,
            duedate,
            load,
            rate,
            prvrdg,
            prsrdg,
            multcd,
            kwhuse,
            genamt,
            genamt1,
            trnamt,
            disamt,
            demamt,
            supamt,
            supper,
            mtramt,
            slamt,
            iccamt,
            iccsub,
            llramt,
            llrsub,
            lldamt,
            misamt,
            envamt,
            framt,
            npcamt,
            iccsamt,
            papc,
            fitamt,
            genchg,
            genchg1,
            trnchg,
            dischg,
            demchg,
            supchg,
            mtrchg,
            mtrper,
            slchg,
            mischg,
            envchg,
            npcchg,
            iccschg,
            fitchg,
            papcchg,
            genvat,
            trnvat,
            disvat,
            slvat,
            othvat,
            appsur,
            surbal,
            current,
            overdue,
            totacc,
            totint,
            scdisc,
            dolpay
            '
        )
            ->from('billing_reports')
            ->where(array('sysid' => 5000))
            ->get()->row();

        $content = $this->model_systems->bill_form($qry_bill);
        echo $content;
    }


    function fahterupdatedloop() {
        echo $this->model_settings->get_father_update_loop();
    }


    function billtrnupdateloop() {
        echo $this->model_settings->get_billtrn_update_loop();
    }

    function servercheck() {
        $data = array();
        if (pecoapps_conn()) {
            $conn = true;
        }else{
            $conn = false;
        }
        $data['conn'] = $conn;
        echo json_encode($data);
    }

    function getfatherrecordscount() {
        $data = array();
        $q = false;
        $total_count = 0;
        $msg = '';
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $qry_father_cnt = $conn->select('COUNT(servno____) AS cnt')
                ->from('father')
                ->get()->row();

            $total_count = ($qry_father_cnt) ? $qry_father_cnt->cnt : 0;
            $q = true;
        }else{
            $msg = 'Connection Failed!';
        }

        $check_logs = $this->db->select('dataid, num')
            ->from('prime_data_migration_logs')
            ->where('desc', 'fatherupdate')
            ->get()->row();

        $last_servno = ($check_logs) ? $check_logs->dataid : '';
        $last_num = ($check_logs) ? $check_logs->num : '';

        $data['msg'] = $msg;
        $data['servno'] = $last_servno;
        $data['num'] = $last_num;
        $data['qry'] = $q;
        $data['cnt'] = $total_count;

        echo json_encode($data);
    }

    function getbilltrncount() {
        $data = array();
        $q = false;
        $total_count = 0;
        $msg = '';

        $year = $this->input->post('year');
        $month = $this->input->post('month');

        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $qry_father_cnt = $conn->select('COUNT(servno____) AS cnt')
                ->from('billtrn')
                ->where(array('yr________' => $year, 'm_________' => $month))
                ->get()->row();

            $total_count = ($qry_father_cnt) ? $qry_father_cnt->cnt : 0;
            $q = true;
        }else{
            $msg = 'Connection Failed!';
        }

        $check_logs = $this->db->select('dataid, num')
            ->from('prime_data_migration_logs')
            ->where('desc', 'fatherupdate')
            ->get()->row();

        $last_servno = ($check_logs) ? $check_logs->dataid : '';
        $last_num = ($check_logs) ? $check_logs->num : '';

        $data['msg'] = $msg;
        $data['servno'] = $last_servno;
        $data['num'] = $last_num;
        $data['qry'] = $q;
        $data['cnt'] = $total_count;

        echo json_encode($data);
    }

    function updatecustomernames() {
        $num_rows = 0;
        $upd_num = 0;
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();

            $query = $this->db->select('sysid, servicenumber AS servno, mtr, mtrno')
                ->from('customer_accounts_main')
                ->order_by('sysid', 'asc')
                ->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {

                    $father = $conn->select('LTRIM(RTRIM(f.name______)) AS name')
                        ->from('father AS f')
                        ->where(array('servno____' => $row->servno, 'mtrser____' => $row->mtrno, 'mtr_______' => $row->mtr))
                        ->get()->row();

                    if ($father) {

                        $num_rows += 1;
                        $name = utf8_decode($father->name);
                        $ins_own_arr = array('name' => $name);
                        $this->db->insert('customer_accounts_name_legacy', $ins_own_arr);
                        $ownerid = $this->db->insert_id();

                        $acctid = $row->sysid;

                        $update_acct_main = array(
                            'ownerid' => $ownerid,
                        );
                        $this->db->where('sysid', $acctid);
                        $upd = $this->db->update('customer_accounts_main', $update_acct_main);

                        if ($upd) {
                            $upd_num += 1;
                        }
                    }
                }
            }

        }

        echo 'Num rows:  ' . $num_rows . ' Updated : ' . $upd_num;
    }


    function checkprinter() {
        $computername = $this->input->post('computername');
        $data = array();
        $qry = false;

        if(user_id() > 0) {
            $conn = windows_printer_connector($computername);
            // CHECK IF CONNECTION STATUS IS TRUE
            if ($conn->res == true) {
                $msg = 'Printer is connected!';
                $func = 'success';
                $qry = true;
            }else{
                $func = 'warning';
                $msg = $conn->message;
                $qry = false;
            }
        }else{
            $msg = 'Please login!';
            $func = 'warning';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function testprint()
    {

        echo '<h1>EscPos : Printing test....</h1>';
        $computername = 'DUDEZKIE';


        $conn = windows_printer_connector($computername);
        if ($conn->res == true) {
            $printer = $conn->printer;
            $printer->feed();

            $printer->setJustification($printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setUnderline(true);
            $printer->text(space_both_sides("T E S T I N G - P R I N T"));
            $printer->setEmphasis(false);
            $printer->setUnderline(false);


            $printer->feed();
            $printer->cut();
            $printer->pulse();
            $printer->close();
        } else {
            print_r($conn);
        }

    }

    function testgetfile() {
        $data = file_get_contents('/usr/created_from_websql_server.txt');

        print_r($data);
    }

    function updaterole() {
        $data = array();
        $role = $this->input->post('roleid');
        $userid = $this->input->post('userid');
        $msg = '';
        $func = '';
        $qry = false;

        $user_roles_qry = $this->db->select()
            ->from('prime_system_users_roles_matrix')
            ->where(array('userid' => $userid, 'roleid' => $role,'status' => 1))
            ->get();

        if ($user_roles_qry->num_rows() > 0){
            $this->db->where(array('userid' => $userid, 'roleid' => $role,'status' => 1));
            $this->db->update('prime_system_users_roles_matrix',array('status' => 0));
            $msg = 'Role removed.';
            $func = 'success';
            $qry = true;
        }else {
            $insarr = array(
                'userid' => $userid,
                'roleid' => $role
            );
            $this->db->insert("prime_system_users_roles_matrix", $insarr);
            $msg = 'Role Added.';
            $func = 'success';
            $qry = true;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
}
