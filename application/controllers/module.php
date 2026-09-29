<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Module extends CI_Controller {

    private $message = '';
    private $page = NULL;
    private $params = NULL;
    private $user_login;

    public function __construct() {
        parent::__construct();
        $this->page = $this->uri->segment(2);
        $this->reroute();
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_search');
        $this->load->model('model_cwdo');
        $this->load->model('model_systems');
        $this->load->model('model_cad');
        // user_id() = $this->session->userdata('logged_in');

        // IMPORTANT ADD THIS TO ALL CONTROLLERS

        if(check_user_lock()) {
            redirect(base_url(), 'refresh');
        }

        if(!user_id()) {
            if ($this->uri->total_segments() > 0) {
                $currentURL = explode('?',current_url());

                redirect(base_url().'?redirect='.$currentURL[1], 'refresh');
            } else {
                redirect(base_url(), 'refresh');
            }
        }

    }

    public function _remap($page, $params = array()) {
        if (count($params) > 0) {
            if (strlen($params[0]) > 0) {
                $this->params = $params;
            }
        }

        if ($this->params) {
            $method = strtolower(trim($this->params[0]));

            if (method_exists($this, $method)) {
                return call_user_func_array(array($this, $method), $this->params);
            } else {
                $this->index();
            }
        } else {
            $this->index();
        }
    }

    public function session() {
        echo "Session!";
        var_dump($this->params);
    }

    public function get_uri_end() {
        $segs = $this->uri->segment_array();
        $count = count($segs);
        return $segs[$count];
    }

    public function index() {
        $data = array();
        if (
            $this->does_page_dashboard($this->page) == true || $this->does_page_exist() == true
            )
        {

            $pageqry = $this->model_admin->get_navigation_specific_details($this->page);
            $segment_cnt = count($this->uri->segment_array());
            $check_page_main = $this->model_systems->check_page_main($pageqry->sysid);

            $data['userdata'] = $this->model_admin->get_user_login_info(user_id());
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_id());
            $data['usersmodule'] = $this->model_admin->select_modules();
            //$data['usersmodule'] = $this->db->select('*')->from('prime_module_navigations_main')->where(array('status' => 1, 'type' => 1))->get();
            $data['pagename'] = $this->model_admin->get_navigation_specific_details($this->page);
            $data['pagetitle'] = $pageqry->pname;
            $data['pagedesc'] = $pageqry->desc;
            $data['pageicon'] = $pageqry->icon;
            $data['pageclass'] = $pageqry->htmlclass;
            $data['navid'] = $pageqry->sysid;

            if (user_id() > 0) {
                if($pageqry) {
                    $logs = log_user_page($pageqry->sysid);
                    if($logs) {
                        $pagefilename = $pageqry->pagefile;
                        if ($pageqry && file_exists(FCPATH . 'application/views/admin/pages/modules/' . $pagefilename . '.php') && $pagefilename != "" && $this->page != "") {
                            if($check_page_main && !$this->uri->segment(3)) {
                                $data['pagetitle'] = $pageqry->pname;
                                init_header($data);
                                init_page_wrapper_top($data);
                                $this->load->view('admin/common/mainpage', $data);
                                init_page_wrapper_bottom($data);
                                init_footer($data, '');
                            }else {
                                if ($this->uri->segment(3)) {
                                    // GET SUB PAGE NAME
                                    $page_sub = $this->model_admin->get_navigation_details($pageqry->sysid);
                                    $data['pagetitle'] = ($page_sub) ? ucwords($this->uri->segment(3)) . ' | ' . $page_sub->name : $pageqry->pname;
                                    $data['navid'] = $pageqry->sysid;
                                    if ($this->uri->segment(3) == 'view' || $this->uri->segment(3) == 'table') {
                                        $record_num = $this->get_uri_end();
                                        $data['dataid'] = $record_num;
                                        // GET FLOW OF MODULE
                                        $qry_flowdetails = $this->db->select('fm.sysid AS FLOWMAINID')
                                            ->from('prime_transaction_flow_main AS fm')
                                            ->where(array('fm.moduleid' => $pageqry->sysid))
                                            ->get()->row();

                                        if ($qry_flowdetails) {
                                            $flowid = $qry_flowdetails->FLOWMAINID;
                                            $data['flowid'] = $flowid;

                                            // GET TRN ID
                                            $qry_trndetails = $this->db->select('rm.sysid')
                                                ->from('transaction_request_main AS rm')
                                                ->join('transaction_request_main_trails AS rmt', 'rmt.trnid = rm.sysid')
                                                ->where(array('rm.flowid' => $flowid, 'rmt.dataid' => $record_num))
                                                ->get()->row();
                                            if ($qry_trndetails) {

                                                // $approval = data_init($record_num, $qry_trndetails->sysid);

                                                $approval = true;

                                            } else {
                                                $approval = false;
                                            }
                                            $data['approval'] = $approval;
                                            init_content_data($record_num, $approval['origin']);
                                        }

                                        if (file_exists(FCPATH . 'application/views/admin/pages/modules/' . $pagefilename . '/' . $this->uri->segment(3) . '.php')) {
                                            init_header($data);
                                            init_page_wrapper_top($data);
                                            $this->load->view('admin/pages/modules/' . $pagefilename . '/' . $this->uri->segment(3), $data);
                                            init_page_wrapper_bottom($data);
                                            init_footer($data, '');
                                        } else {
                                            return $this->error_page();
                                        }
                                    } else {
                                        if ($this->uri->segment(4)) {
                                            if (file_exists(FCPATH . 'application/views/admin/pages/modules/' . $pagefilename . '/data.php')) {
                                                $dataid = $this->uri->segment(5);

                                                $viewfile = module_request_navigation_name();
                                                $viewpage = module_request_navigation_details();

                                                $data['dataid'] = $dataid;
                                                $data['trailsid'] = $this->uri->segment(4);
                                                if ($viewpage) {
                                                    $data['pagetitle'] = $viewpage->descs;
                                                }


                                                $data['dataid'] = $dataid;
                                                $data['trailsid'] = $this->uri->segment(4);

                                                if (module_data_exists()) {
                                                    init_header($data);
                                                    $approval = data_init($dataid, $this->uri->segment(4));
                                                    //$approval = data_init();
                                                    $data['approval'] = $approval;
                                                    init_page_wrapper_top($data);
                                                    init_content_top($approval);
                                                    init_content_data($dataid, $approval['origin']);
                                                    if (task_flows_stages()) {
                                                        $origin_id = $approval['origin'];
                                                        // DATA VIEW
                                                        data_view($pagefilename, $viewfile, $data);
                                                    } else {
                                                        $data['task_flow'] = false;
                                                        $this->load->view('admin/pages/modules/' . $pagefilename . '/data', $data);
                                                    }

                                                    init_page_wrapper_bottom($data);
                                                    init_footer($data, '');
                                                } else {
                                                    // CUSTOM PAGE
                                                    if ($this->uri->segment(5)) {
                                                        $data['dataid'] = $dataid = $this->uri->segment(4);
                                                        $data['trailsid'] = $dataid = $this->uri->segment(3);
                                                        $data['viewtype'] = $dataid = $this->uri->segment(5);
                                                        init_header($data);
                                                        init_page_wrapper_top($data);
                                                        $this->load->view('admin/pages/modules/' . $pagefilename . '/' . $this->uri->segment(3), $data);
                                                        init_page_wrapper_bottom($data);
                                                        init_footer($data, '');
                                                    } else {
                                                        return $this->error_page('<strong class="text-warning"><i class="fa fa-warning" ></i> Transaction not found !</strong>');
                                                    }
                                                }
                                            } else {
                                                return $this->error_page();
                                            }
                                        } else {
                                            //return $this->error_page();
                                            // NOTE: CHANGE THIS AS DEFAULT PAGE AS " NEW "

                                            if (file_exists(FCPATH . 'application/views/admin/pages/modules/' . $pagefilename . '/' . $this->uri->segment(3) . '.php')) {
                                                init_header($data);
                                                init_page_wrapper_top($data);
                                                $this->load->view('admin/pages/modules/' . $pagefilename . '/' . $this->uri->segment(3), $data);
                                                init_page_wrapper_bottom($data);
                                                init_footer($data, '');
                                            } else {
                                                return $this->error_page();
                                            }
                                        }
                                    }

                                } else {
                                    init_header($data);
                                    init_page_wrapper_top($data);
                                    $this->load->view('admin/pages/modules/' . $pagefilename, $data);
                                    init_page_wrapper_bottom($data);
                                    init_footer($data, '');
                                }
                            }
                        } else {
                            if (file_exists(FCPATH . 'application/views/admin/pages/' . $pagefilename . '.php')) {
                                return $this->error_page('<strong class="text-danger">PAGE FOUND: (' . $pagefilename . ')!</strong>');
                            } else {
                                if($check_page_main) {
                                    $data['pagetitle'] = $pageqry->pname;
                                    init_header($data);
                                    init_page_wrapper_top($data);
                                    $this->load->view('admin/common/mainpage', $data);
                                    init_page_wrapper_bottom($data);
                                    init_footer($data, '');
                                } else {
                                    return $this->error_page('<strong class="text-danger">PHP PAGE DOES NOT EXISTS (' . $pagefilename . ')!</strong>');
                                }
                            }
                        }
                    }else{
                        if($check_page_main) {
                            $data['pagetitle'] = $pageqry->pname;
                            init_header($data);
                            init_page_wrapper_top($data);
                            $this->load->view('admin/common/mainpage', $data);
                            init_page_wrapper_bottom($data);
                            init_footer($data, '');
                        } else {
                            return $this->error_page();
                        }
                    }
                }else{
                    if($check_page_main) {
                        $data['pagetitle'] = $pageqry->pname;
                        init_header($data);
                        init_page_wrapper_top($data);
                        $this->load->view('admin/common/mainpage', $data);
                        init_page_wrapper_bottom($data);
                        init_footer($data, '');
                    } else {
                        return $this->error_page();
                    }

                }
            } else {

                if($check_page_main) {
                    $data['pagetitle'] = $pageqry->pname;
                    init_header($data);
                    init_page_wrapper_top($data);
                    $this->load->view('admin/common/mainpage', $data);
                    init_page_wrapper_bottom($data);
                    init_footer($data, '');
                } else {
                    return $this->error_page();
                }

            }

        } else {
            $data['title'] = SYSTEM_NAME;
            $data['message'] = 'You have no access to this page, page will return to previous page!';
            echo page_session($data);
            // ############## REDIRECT USER TO LOGIN PAGE ##################/
            // redirect(base_url(), 'refresh');

        }
    }

    public function error_page($msg = NULL) {
        if ($msg == NULL) {
            if (user_id()) {
                $data['userdata'] = $this->model_admin->get_user_login_info(user_id());
                $data['profiledata'] = $this->model_admin->get_user_login_info(user_id());
                $data['usersmodule'] = $this->model_admin->select_modules();
                $data['msg'] = $msg;
                init_header($data);
                $this->load->view('admin/pages/page404', $data);
                init_footer($data, '');
            } else {
                redirect(base_url() . 'auth', 'refresh');
            }
        } else {
            $data['userdata'] = $this->model_admin->get_user_login_info(user_id());
            $data['profiledata'] = $this->model_admin->get_user_login_info(user_id());
            $data['usersmodule'] = $this->model_admin->select_modules();
            $data['msg'] = $msg;
            init_header($data);
            $this->load->view('admin/pages/page404', $data);
            init_footer($data, '');
        }
    }

    public function noroute($page = NULL) {
        if ($page) {
            echo "<h1>404 No Route Found to " . $page . "</h1>";
        } else {
            echo "<h1>404 No Route</h1>";
        }
    }

    private function reroute() {

        if ($this->page == $this->router->class) {
            if ($this->uri->total_segments() > 1) {
                $this->load->helper('url');

                $uri = substr($this->uri->uri_string, strlen($this->page) + 1);
                redirect($uri);
            } else {
                $this->noroute($this->page);
            }
        }
    }

    private function does_page_dashboard($hash) {
        return $this->model_admin->get_user_dashboard_access($hash);
    }

    private function does_page_exist() {
        $return = false;
        if ( user_id()) {
            $a = $this->model_admin->array_module_navigations();

            if ($a) {
                $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($a));
                foreach ($it as $v) {
                    $array_of_pages[] = $v;
                }
            } else {
                $array_of_pages[] = '';
            }

            if ($this->model_admin->in_array_r($this->page, $array_of_pages) || super_admin()) {
                $return = true;
            } else {
                $return =  false;
            }
        }
        return $return;
    }

}
