<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
if(!function_exists('pecoapps_conn')) {
    function pecoapps_conn(){
        $ci = &get_instance();
        $conn = $ci->load->database(SYSTEM_LEGACY_INSTANCE, TRUE);
        //$conn = $ci->load->database('sqldev', TRUE);
        $connected = $conn->initialize();
        if (!$connected) {
            return false;
        } else {
            return true;
        }
    }
}

if(!function_exists('pecoappsdev_conn')) {
    function pecoappsdev_conn(){
        $ci = &get_instance();
        $conn = $ci->load->database('pecoappsdev', TRUE);
        //$conn = $ci->load->database('sqldev', TRUE);
        $connected = $conn->initialize();
        if (!$connected) {
            return false;
        } else {
            return true;
        }
    }
}
*/



if(!function_exists('audit_db')) {
    function audit_db(){
        $ci = &get_instance();
        $conn = $ci->load->database('audit', TRUE);
        $connected = $conn->initialize();
        if (!$connected) {
            return false;
        } else {
            return true;
        }
    }
}

if(!function_exists('audit_insert')) {
    function audit_insert($data)
    {
        $ci = &get_instance();
        if (audit_db()) {
            $conn = $ci->load->database('audit', TRUE);
            $conn->initialize();
            $conn->trans_begin();
            $conn->insert('erp_transactions_trails', $data);
            if($conn->trans_status() === TRUE) {
                $conn->trans_commit();
                return true;
            }else{
                $conn->trans_rollback();
                return false;
            }
        }
    }
}



if (!function_exists('dev_mode')) {
    function dev_mode() {
        $ci = &get_instance();
        $qry = $ci->db->select()->from('system_settings')->where(array('codes' => 'DEV', 'status' => 1))->get()->row();
        if($qry && $qry->status==1) {
            return true;
        }else {
            return false;
        }
    }
}

if(!function_exists('sql_time')){
    function sql_time(){
        $ci = &get_instance();
        $sql_date = $ci->db->query("SELECT NOW() AS DATENOW")->row();
        $date_obj = new DateTime($sql_date->DATENOW);
        $date_name = $date_obj->format('F d, Y');
        $date_num = $date_obj->format('Y-m-d');
        $date_y = $date_obj->format('Y');
        $date_m = $date_obj->format('m');
        $date_d = $date_obj->format('d');
        $date_time_12 = $date_obj->format('h:i:s A');
        $date_time_24 = $date_obj->format('H:i:s');
        $date_complete = $date_obj->format('l jS \of F Y h:i:s A');
        $date_time_i = $date_obj->format('Y-m-d H:i:s');
        return ($sql_date) ? (object)array(
            'Y' => $date_y,
            'M' => $date_m,
            'D' => $date_d,
            'DATENAME' => $date_name,
            'DATENUM' => $date_num,
            'TIME12' => $date_time_12,
            'TIME24' => $date_time_24,
            'DATECOMPLETE' => $date_complete,
            'DATETIME' => $date_time_i,
        ) : false;
    }
}
if(!function_exists('time_to_word')) {
    function time_to_word($date_spent_mt, $date_spent_d, $date_spent_h, $date_spent_mn, $date_spent_s)
    {
        if ($date_spent_mt > 0) {
            if ($date_spent_d > 0) {
                if ($date_spent_h > 0) {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days and ' . $date_spent_h . ' hours and ' . $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days and ' . $date_spent_h . ' hours and ' . $date_spent_mt . ' minutes ';
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days and ' . $date_spent_h . ' hours and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days' . $date_spent_h . ' hours and ';
                        }
                    }
                } else {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days and ' . $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days ' . $date_spent_s . ' seconds';
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_d . ' days';
                        }
                    }
                }
            } else {
                if ($date_spent_h > 0) {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_mn > 0) {
                            if ($date_spent_s > 0) {
                                $datespent = $date_spent_mt . ' month and ' . $date_spent_h . ' hours and ' . $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                            } else {
                                $datespent = $date_spent_mt . ' month and ' . $date_spent_h . ' hours and ' . $date_spent_mn . ' minutes';
                            }
                        } else {
                            if ($date_spent_s > 0) {
                                $datespent = $date_spent_mt . ' month and ' . $date_spent_h . ' hours and ' . $date_spent_s . ' seconds';
                            } else {
                                $datespent = $date_spent_mt . ' month and ' . $date_spent_h . ' hours';
                            }
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_h . ' hours and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_h . ' hours';
                        }
                    }
                } else {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_mn . ' minutes';
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mt . ' month and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mt . ' month';
                        }
                    }
                }
            }
        } else {
            if ($date_spent_d > 0) {
                if ($date_spent_h > 0) {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_d . ' days and ' . $date_spent_h . ' hours and ' . $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_d . ' days and ' . $date_spent_h . ' hours and ' . $date_spent_mn . ' minutes ';
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_d . ' days and ' . $date_spent_h . ' hours and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_d . ' days' . $date_spent_h . ' hours and ';
                        }
                    }
                } else {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_d . ' days and ' . $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_d . ' days ' . $date_spent_s . ' seconds';
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_d . ' days and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_d . ' days';
                        }
                    }
                }
            } else {
                if ($date_spent_h > 0) {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_mn > 0) {
                            if ($date_spent_s > 0) {
                                $datespent = $date_spent_h . ' hours and ' . $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                            } else {
                                $datespent = $date_spent_h . ' hours and ' . $date_spent_mn . ' minutes';
                            }
                        } else {
                            if ($date_spent_s > 0) {
                                $datespent = $date_spent_h . ' hours and ' . $date_spent_s . ' seconds';
                            } else {
                                $datespent = $date_spent_h . ' hours';
                            }
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_h . ' hours and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_h . ' hours';
                        }
                    }
                } else {
                    if ($date_spent_mn > 0) {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_mn . ' minutes and ' . $date_spent_s . ' seconds';
                        } else {
                            $datespent = $date_spent_mn . ' minutes';
                        }
                    } else {
                        if ($date_spent_s > 0) {
                            $datespent = $date_spent_s . ' seconds';
                        } else {
                            $datespent = '0';
                        }
                    }
                }
            }
        }
        return $datespent;
    }
}


if (!function_exists('get_uri_data')) {
    function get_uri_data () {
        $ci = &get_instance();
        $query = array();

        $segs = $ci->uri->segment_array();
        $uri_s = '';
        foreach ($segs as $segment) {
            $uri_s .= $segment;
            $uri_s .= '/';
        }

        $segment_arr = explode('/', $uri_s);
        $segment_hash = $segment_arr[1];
        // GET MODULE NAME OUT OF HASh
        $qry_module = $ci->db->select()->from('prime_module_navigations_main')
            ->where('hashcode', $segment_hash)->get()->row();
        if($qry_module) {
            $navid = $qry_module->sysid;
        }else{
            $navid = 0;
        }
        $query['uri_string'] = $uri_s;
        $query['uri_navids'] = $navid;
        return (object)$query;
    }
}


if (!function_exists('cal_days_in_month')) {

    function cal_days_in_month($calendar, $month, $year) {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }

}
if (!defined('CAL_GREGORIAN'))
    define('CAL_GREGORIAN', 1);

if (!function_exists('get_billing_passdue')) {

    function get_billing_passdue($duedate) {
        $datetime1 = new DateTime($duedate);
        $datetime2 = new DateTime(date('Y-m-d'));
        $interval = $datetime1->diff($datetime2);
        $days_pass = $interval->format('%a');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($datetime1, $interval, $datetime2);
        $int_1 = 0;
        $total_amt = 0;
        $int = 0;
        $due = false;
        foreach ($period as $dt) {
            $int_1 += 0.0224;
            $m = $dt->format("m");
            $y = $dt->format("Y");

            $ds = cal_days_in_month(CAL_GREGORIAN, $m, $y);
            $days_pass -= $ds;
            $today = date('d');
            $duedate_day = date_formating($duedate, 'Y-m-d', 'd');
            $days_pass_today = $today - $duedate_day;
            if ($days_pass > 3) {
                $due = true;
                $int = $int_1;
            } else {

                if ($days_pass_today > 3) {
                    $due = true;
                    $int = $int_1;
                } else {
                    $due = false;
                    $int = 0;
                }
            }
        }
        return $due;
    }

}




if (!function_exists('get_billing_interest')) {

    function get_billing_interest($duedate, $amt) {
        $datetime1 = new DateTime($duedate);
        $datetime2 = new DateTime(date('Y-m-d'));
        $interval = $datetime1->diff($datetime2);
        $days_pass = $interval->format('%a');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($datetime1, $interval, $datetime2);
        $int_1 = 0;
        $total_amt = 0;
        $int = 0;
        foreach ($period as $dt) {
            $int_1 += 0.0224;
            $m = $dt->format("m");
            $y = $dt->format("Y");

            $ds = cal_days_in_month(CAL_GREGORIAN, $m, $y);
            $days_pass -= $ds;
            $today = date('d');
            $duedate_day = date_formating($duedate, 'Y-m-d', 'd');
            $days_pass_today = $today - $duedate_day;
            if ($days_pass > 3) {
                $due = true;
                $int = $int_1;
                $total_amt += $amt * $int;
            } else {

                if ($days_pass_today > 3) {
                    $due = true;
                    $int = $int_1;
                    $total_amt += $amt * $int;
                } else {
                    $due = false;
                    $int = 0;
                    $total_amt += $amt;
                }
            }
        }
        return $int * $amt;
    }

}

if (!function_exists('date_formating')) {

    function date_formating($num, $format_old, $format_new) {
        if($num!='' || $num!=null || !empty($num)) {
            $dt = DateTime::createFromFormat($format_old, $num);
            $res = $dt->format($format_new); // output: 2013
            return $res;
        }else{
            return '';
        }
    }

}

if(!function_exists('task_notify')) {
    function task_notify($flowid, $dataid) {
        $ci = &get_instance();
        $qry = false;
        $msg = '';
        // SPECIAL CASES LIKE LEGAL APPREHENSIOn
        if($flowid == 2 || $flowid == 8) {
            // TEMP
            $qry_owner_temp = $ci->db->select()->from('application_customers_details')->where('sysid', $dataid)->get()->row();
            if($qry_owner_temp) {

                if($qry_owner_temp->existlegalra > 0) {
                    $qry_person_exempt = $ci->db->select()->from('application_customers_exemptions')->where(array('appid' => $dataid, 'status' => 1))->get()->row();
                    if($qry_person_exempt==false) {
                        $msg = 'This application needs to forwarded in Legal Department for verification.';
                        $qry = true;
                    }
                }
            }
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return (object)$data;
    }
}

// @TODO create maintenance for strict stage.
// STRICT STAGES
if (!function_exists('strict_flow_arr')) {
    function strict_flow_arr() {
        $data_arr = array();
        return $data_arr;
    }
}

if (!function_exists('create_transaction_trails')) {

    function create_transaction_trails($codes, $descs, $moduleid, $dataid, $type = 1) {
        $ci = & get_instance();
        $userid = user_id();
        $check_flow = $ci->db->select()->from('prime_transaction_flow_main')->where('moduleid', $moduleid)->get()->row();
        if ($check_flow) {
            $ci->db->trans_begin();
            $flowid = $check_flow->sysid;

            $ins_trn = $ci->db->insert('transaction_request_main',
                array(
                    'flowid' => $flowid,
                    'codes' => $codes,
                    'descs' => $descs,
                    'createdby' => $userid,
                    'type' => $type
                ));
            $trn_id = $ci->db->insert_id();

            // GETSTAGES DETAILS
            $qry_stages = $ci->db->select()->from('prime_transaction_flow_main_stages')->where(array('flowid' => $flowid, 'status' => 1))->order_by('levels')->get()->row();
            $stage_id = $qry_stages->sysid;

            // ############################################ //
            // SPECIAL QUERY // FOR SPECIFIC STAGE REQUIRED //
            // APPLICABLE FOR LEGAL ####################### //
            $check_flow_req = $ci->db->select()->from('prime_transaction_flow_main_stages_required')
                ->where('flowid', $flowid)->get()->row();
            if ($check_flow_req) {
                // GET PERSON ID
                $qry_owners = $ci->db->select()->from('customer_accounts_owners')
                    ->where(array('accountid' => $dataid, 'ownertype' => 1))
                    ->get()->row();
                if ($qry_owners) {
                    $check_legal = account_verifications($qry_owners->ownerid);
                    if($check_legal['num']>0) {
                        if(in_array($flowid, strict_flow_arr())) {
                            $stage_id = $check_flow_req->stageid;
                        }
                    }
                }
            }


            // INSERT FIRST TRAIL
            $ins_trail = $ci->db->insert('transaction_request_main_trails', array('trnid' => $trn_id, 'stageid' => $stage_id, 'dataid' => $dataid, 'createdby' => $userid));
            $trail_id = $ci->db->insert_id();

            // INSERT FIRST TRAIL LOGS
            $ins_trail_log = $ci->db->insert('transaction_request_trails_logs', array('trailid' => $trail_id, 'activity' => 85, 'userid' => $userid));
            if ($ci->db->trans_status() === FALSE) {
                $ci->db->trans_rollback();
                return false;
            } else {
                $ci->db->trans_commit();
                return true;
            }
        } else {
            return false;
        }
    }

}

if (!function_exists('limit_text_tooltip')) {

    function limit_text_tooltip($str, $limit, $head) {
        $string = strip_tags($str);

        if (strlen($string) > $limit) {

            // truncate string
            $stringCut = substr($string, 0, $limit);

            // make sure it ends in a word so assassinate doesn't become ass...
            $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . ' ... <a class="pull-right popovers" data-trigger="hover" data-container="body" data-placement="right" data-content="' . $str . '" data-original-title="' . $head . '" href="javascript:;"><i class="fa fa-search"></i></a>';
        }
        return $string;
    }

}

if (!function_exists('highlightkeyword')) {

    function highlightkeyword($str, $search) {
        $occurrences = substr_count(strtolower($str), strtolower($search));
        $newstring = $str;
        $match = array();

        for ($i = 0; $i < $occurrences; $i++) {
            $match[$i] = stripos($str, $search, $i);
            $match[$i] = substr($str, $match[$i], strlen($search));
            $newstring = str_replace($match[$i], '[#]' . $match[$i] . '[@]', strip_tags($newstring));
        }

        $newstring = str_replace('[#]', '<span class="bg-blue" id="search_highlights">', $newstring);
        $newstring = str_replace('[@]', '</span>', $newstring);
        return $newstring;
    }
}

if (!function_exists('stringslice')) {

    function stringslice($str, $search) {
        $occurrences = substr_count(strtolower($str), strtolower($search));
        $newstring = $str;
        $match = array();
        $textleft = '';

        for ($i = 0; $i < $occurrences; $i++) {
            $match[$i] = stripos($str, $search, $i);
            $match[$i] = substr($str, $match[$i], strlen($search));
            $textleft = str_replace($match[0], '', strip_tags($newstring));

        }
        return (object)array('match' => $match[0], 'textleft' => $textleft);
    }
}

if (!function_exists('module_allow_url')) {
    function module_allow_url() {
        return array('new', 'list', 'view', 'edit', 'file', 'table', 'entry', 'validate', 'inquiry');
    }
}


if (!function_exists('get_module_name')) {

    function get_module_name($id) {
        $ci = & get_instance();
        $qry = $ci->db->select()->from('prime_module_navigations_main')->where('sysid', $id)->get()->row();
        return ( $qry ) ? $qry : false;
    }

}


if (!function_exists('module_request_navigation_name')) {

    function module_request_navigation_name() {
        $ci = & get_instance();
        $nav_view = $ci->uri->segment(3);
        $qry = $ci->db->select()->from("transaction_request_navigations")->where('namehash', $nav_view)->get()->row();
        return ( $qry ) ? $qry->names : 'data';
    }

}

if (!function_exists('module_request_navigation_details')) {

    function module_request_navigation_details() {
        $ci = & get_instance();
        $nav_view = $ci->uri->segment(3);
        $qry = $ci->db->select()->from("transaction_request_navigations")->where('namehash', $nav_view)->get()->row();
        return ( $qry ) ? $qry : '';
    }

}
if (!function_exists('nav_sub')) {

    function nav_sub($id) {
        $ci = & get_instance();
        $qry = $ci->db->select("COUNT(*) AS CNT")->from('prime_module_navigations_main')->where(array("parent" => $id, 'status' => 1))->get()->row();
        return ( $qry ) ? $qry->CNT : false;
    }

}

if (!function_exists('nav_children')) {

    function nav_children($parent, $level) {
        $ci = & get_instance();
        //$result = mysql_query("SELECT a.id, a.label, a.link, Deriv1.Count FROM `menu` a  LEFT OUTER JOIN (SELECT parent, COUNT(*) AS Count FROM `menu` GROUP BY parent) Deriv1 ON a.id = Deriv1.parent WHERE a.parent=" . $parent);
        $result = $ci->db->select('sysid, parent AS PARENT, htmlclass, icon, name, desc, htmlid, url, hashcode, levels, pagefile, type')
            ->from('prime_module_navigations_main')
            ->where(array('parent' => $parent, 'levels' => $level, 'status' => 1))
            ->order_by('sorting')->get();
        $html = '';
        if ($result->num_rows() > 0) {
            $html .= '<ul  class="sub-menu">';
            foreach ($result->result() as $row) {
                if (check_user_nav_access($row->sysid)) {
                    $module_url = '';
                    $new_level = ( $row->levels + 1 );
                    $qry_child_num = $ci->db->select('COUNT(*) AS CNT')->from('prime_module_navigations_main')->where(array('parent' => $row->sysid, 'levels' => $new_level, 'status' => 1))->get()->row();
                    if ($qry_child_num->CNT > 0) {
                        if($row->type==3) {
                            $target_ = '_blank';
                        }else{
                            $target_ = '';
                        }
                        if ($row->type < 3) {
                            $sub_menu_class = $ci->model_admin->init_navigation_open_sub($ci->uri->segment(2), $row->sysid);
                            $htmlclass = $row->htmlclass;
                            $navicon = $row->icon;
                            $navdesc = $row->desc;
                            $navhash = $row->hashcode;
                            $url_str = ($row->url!='') ? '/' . $row->url : '';
                            $url = 'module/' . $navhash . $url_str;
                            $html .= '<li class="nav-item ' . $sub_menu_class->class . '">';
                            $html .= '<a href="'.base_url($url).'"> <i class="fa ' . $row->icon . ' text-' . $row->htmlclass . '"></i> <span class="title">' . $row->name . '</span><span class="arrow"></span></a>';
                            $html .= nav_children($row->sysid, $new_level);
                            $html .= '</li>';
                        } else {

                            if ($row->hashcode == "" || $row->pagefile == "") {
                                $htmlclass = 'danger';
                                $navicon = 'fa-warning';
                                $navdesc = 'This page is under maintenance';
                                $navhash = sha1($row->sysid);
                            } else {
                                $htmlclass = $row->htmlclass;
                                $navicon = $row->icon;
                                $navdesc = $row->desc;
                                $navhash = $row->hashcode;
                            }
                            $html .= '<li class=" tooltips' . $ci->model_admin->init_navigation_active_link($ci->uri->segment(2), $row->sysid) . '" data-container="body" data-placement="right" data-html="true" data-original-title="' . $navdesc . '"><a target="'.$target_.'" href="' . base_url($row->url) . '"><i class="fa ' . $navicon . ' text-' . $htmlclass . '"></i><span class="menu-name">' . $row->name . '</span></a></li>';
                        }
                    } else {
                        if($row->type==3) {
                            $target_ = '_blank';
                        }else{
                            $target_ = '';
                        }
                        if ($row->type < 3) {
                            if ($row->hashcode == "" || $row->pagefile == "") {
                                $htmlclass = 'danger';
                                $navicon = 'fa-warning';
                                $navdesc = 'This page is under maintenance';
                                $navhash = sha1($row->sysid);
                            } else {
                                $htmlclass = $row->htmlclass;
                                $navicon = $row->icon;
                                $navdesc = $row->desc;
                                $navhash = $row->hashcode;
                            }
                            // GET IF MODULE LINK HAS URL
                            if (in_array($row->url, module_allow_url())) {
                                $module_url = $row->url;
                            }else{
                                if (file_exists(FCPATH . 'application/views/admin/pages/modules/' . $row->pagefile . '/' . $row->url . '.php')) {
                                    $module_url = $row->url;
                                }
                            }
                            $html .= '<li class=" tooltips ' . $ci->model_admin->init_navigation_active_link($ci->uri->segment(2), $row->sysid) . '" data-container="body" data-placement="right" data-html="true" data-original-title="' . $navdesc . '"><a href="' . base_url('module/' . $navhash . '') . '/' . $module_url . '"><i class="fa ' . $navicon . ' text-' . $htmlclass . '"></i><span class="menu-name">' . $row->name . '</span></a></li>';
                        } else {
                            if ($row->hashcode == "" || $row->pagefile == "") {
                                $htmlclass = 'danger';
                                $navicon = 'fa-warning';
                                $navdesc = 'This page is under maintenance';
                                $navhash = sha1($row->sysid);
                            } else {
                                $htmlclass = $row->htmlclass;
                                $navicon = $row->icon;
                                $navdesc = $row->desc;
                                $navhash = $row->hashcode;
                            }
                            $html .= '<li class=" tooltips " data-container="body" data-placement="right" data-html="true" data-original-title="' . $navdesc . '"><a target="'.$target_.'" href="' . base_url($row->url) . '"><i class="fa ' . $navicon . ' text-' . $htmlclass . '"></i><span class="menu-name">' . $row->name . '</span></a></li>';
                        }
                    }
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }

}

if (!function_exists('nav_children_dashboards')) {

    function nav_children_dashboards($parent, $level) {
        $ci = & get_instance();
        //$result = mysql_query("SELECT a.id, a.label, a.link, Deriv1.Count FROM `menu` a  LEFT OUTER JOIN (SELECT parent, COUNT(*) AS Count FROM `menu` GROUP BY parent) Deriv1 ON a.id = Deriv1.parent WHERE a.parent=" . $parent);
        $result = $ci->db->select('sysid, parent AS PARENT, htmlclass, icon, name, desc, htmlid, url, hashcode, levels, pagefile, type')
            ->from('prime_module_navigations_main')
            ->where(array('parent' => $parent, 'levels' => $level, 'status' => 1))
            ->order_by('sorting')->get();

        $qry_dashboard_arr = array();
        $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();
        if( super_admin() ) {
            $qry_dashboard = $ci->db->select()
                ->from('prime_system_roles_dashboards')
                ->get();
        }else {
            $qry_dashboard = $ci->db->select()
                ->from('prime_system_roles_dashboards')
                ->where_in('roleid', $user_access_matrix_id_arr)
                ->get();
        }
        if($qry_dashboard->num_rows()>0) {
            foreach ($qry_dashboard->result() as $drows) {
                $qry_dashboard_arr[] = $drows->navids;
            }
        }

        $html = '';
        if ($result->num_rows() > 0) {
            foreach ($result->result() as $row) {
                $level = $row->levels + 1;
                $checking = nav_children_dashboards($row->sysid, $level);
                if($checking) {
                    $html .= $checking;
                }else {
                    if (in_array($row->sysid, $qry_dashboard_arr)) {
                        $htmlclass = $row->htmlclass;
                        $navicon = $row->icon;
                        $navdesc = $row->desc;
                        $navhash = $row->hashcode;
                        $url = 'module/'.$navhash.'/'.$row->url;
                        $html .= '<li class=" tooltips " data-container="body" data-placement="right" data-html="true" data-original-title="' . $navdesc . '"><a target="" href="' . base_url($url) . '"><i class="fa ' . $navicon . ' text-' . $htmlclass . '"></i><span class="menu-name">' . $row->name . '</span></a></li>';
                    }
                }
            }
        }
        return $html;
    }

}

if (!function_exists('module_request_navigation')) {

    function module_request_navigation($approval = NULL) {
        $ci = & get_instance();
        $data = array();
        $qry_curr_trn = '';
        $qry_curr_lvl = '';
        $nav_view = $ci->uri->segment(3);
        $trn_id = $ci->uri->segment(4);

        // GET STAGE DETAILS
        $qry_trn = $ci->db->select('t.stageid')->from('transaction_request_main_trails AS t')->where('t.sysid', $trn_id)->get()->row();
        $stages_arr = array();
        $navs_arr = array();
        if ($qry_trn) {
            $qry_stg = $ci->db->select('sysid')->from('prime_transaction_flow_main_stages')->where('sysid', $qry_trn->stageid)->get();
            if ($qry_stg->num_rows() > 0) {
                foreach ($qry_stg->result() as $row) {
                    $stages_arr[] = $row->sysid;
                }
            }
            // QUERY NAVIGATIONS
            $qry_navs = $ci->db->select('navid')
                ->from('prime_transaction_flow_main_stages_navigations')
                ->where_in('stageid', $stages_arr)
                ->where('status', 1)
                ->get();
            if ($qry_navs->num_rows() > 0) {
                foreach ($qry_navs->result() as $row) {
                    $navs_arr[] = $row->navid;
                }
            }
        }

        $navs_arr[] = 6;
        $navs_arr[] = 1;

        // GET CURRENT TRN DETAILS
        $qry_curr_trn = $ci->db->select('t.trnid, s.levels, s.desc, t.datecreated, u.firstname, t.dataid, s.flowid')
            ->from('transaction_request_main_trails AS t')
            ->join('prime_transaction_flow_main_stages AS s', 's.sysid = t.stageid', 'left')
            ->join('prime_system_users AS u', 'u.sysid = t.createdby')
            ->where('t.sysid', $trn_id)
            ->get()->row();

        $flowid = ($qry_curr_trn) ? $qry_curr_trn->flowid : false;

        $trnid = ($qry_curr_trn) ? $qry_curr_trn->trnid : false;

        $dataid = ($qry_curr_trn) ? $qry_curr_trn->dataid : false;
        if ($qry_curr_trn) {
            $qry_curr_trn = (object) array('desc' => $qry_curr_trn->desc, 'lvl' => $qry_curr_trn->levels, 'date' => $qry_curr_trn->datecreated, 'name' => $qry_curr_trn->firstname);
        }

        $q = false;
        $trn_nav_view = '';
        $req_view = '';
        $trn_nav_view .= '<ul class="nav nav-tabs">';
        if ($nav_view) {
            if ($trn_id) {
                if ($approval != 1) {
                    $ci->db->where('codes !=', 'APPROVAL');
                }
                $qry = $ci->db->select("*")->from("transaction_request_navigations")->get();
                if ($qry->num_rows() > 0) {
                    foreach ($qry->result() as $row) {
                        if ($row->sysid == 1 || $row->sysid == 2 || in_array($row->sysid, $navs_arr)) {
                            $nav_active = ( $nav_view == $row->namehash ) ? 'active' : '';
                            $comment_cnt = '';
                            if($row->sysid == 6) {
                                $qry_comments_cnt = $ci->db->select('count(tc.trnid) AS cnt')
                                    ->from('transaction_request_trails_comments AS tc')
                                    ->where(array('tc.trnid' => $trnid,  'tc.status' => 1))
                                    ->get()->row();

                                if($qry_comments_cnt && $qry_comments_cnt->cnt>0) {
                                    $comment_cnt = '<span id="nav_comments_cnt" class="badge badge-info" style="margin-left: 5px;">'.$qry_comments_cnt->cnt.'</span>';
                                }
                            }
                            $trn_nav_view .= '<li class="' . $nav_active . '"><a href="' . base_url() . 'module/' . $ci->uri->segment(2) . '/' . $row->namehash . '/' . $trn_id . '/' . $ci->uri->segment(5) . '" class=""><i class="fa ' . $row->icons . ' fa-fw"></i> ' . $row->descs . $comment_cnt . '</a></li>';
                        }
                        // CHECK ICALES NAV
                        $qry_ecales = $ci->db->select('jobtype')->from('application_customers_details')
                            ->where(array('sysid' => $dataid))
                            ->get()->row();
                        if (($qry_ecales && $qry_ecales->jobtype == 323) && $row->sysid == 9) {
                            $nav_active = ( $nav_view == $row->namehash ) ? 'active' : '';
                            $trn_nav_view .= '<li class="' . $nav_active . '"><a href="' . base_url() . 'module/' . $ci->uri->segment(2) . '/' . $row->namehash . '/' . $trn_id . '/' . $ci->uri->segment(5) . '" class=""><i class="fa ' . $row->icons . ' fa-fw"></i> ' . $row->descs . '</a></li>';
                        }
                    }
                } else {
                    $trn_nav_view .= '<li class=""><a href="' . base_url() . 'module/' . $ci->uri->segment(2) . '/335ce16b3fe40346cc3af2a4efce2ef04bc4ea55/' . '/' . $trn_id . '/' . $ci->uri->segment(5) . '" class=""><i class="fa fa-file fa-fw"></i> Subject</a></li>';
                }
                $q = true;
            }

        }
        $trn_nav_view .= '</ul>';
        // $trn_nav_view .= '<a href="' . base_url() . 'module/' . $ci->uri->segment(2) . '/list" class="font-red-flamingo btn pull-right"><i class="fa fa-backward fa-fw"></i> Back</a>';

        $data['currtrn'] = (object) ($qry_curr_trn) ? $qry_curr_trn : false;

        $data['qry'] = $q;
        $data['html'] = $trn_nav_view;
        return (object) $data;
    }

}

if (!function_exists('module_data_exists')) {

    function module_data_exists() {
        $ci = & get_instance();
        $trnid = $ci->uri->segment(4);
        $dataid = $ci->uri->segment(5);
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;

        $qry_stages = $ci->db->select()->from('prime_transaction_flow_main_stages')->where('moduleid', $moduleid)->get()->row();


        //$query = $ci->db->select()->from('transaction_request_main_trails')->where(array('stageid' => $qry_stages->levels, 'dataid' => $dataid))->get()->row();
        $query = $ci->db->select()->from('transaction_request_main_trails')->where(array('sysid' => $trnid, 'dataid' => $dataid))->get()->row();


        return ( $query ) ? $query : false;
    }

}

if (!function_exists('module_data_status')) {

    function module_data_status() {
        $ci = & get_instance();
        $dataid = $ci->uri->segment(5);
        $trnid = $ci->uri->segment(4);
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;

        $qry_trl = $ci->db->select()->from('transaction_request_main_trails')->where(array('sysid' => $trnid, 'dataid' => $dataid))->get()->row();

        $data = array();
        if ($qry_trl) {
            $data['stagestat'] = true;
            $data['stagestatcode'] = '';
        } else {
            $data['stagestat'] = false;
            $data['stagestatcode'] = 'Transaction not found!';
        }
        return (object) $data;
    }

}

// ####################################################################
// DETERMINE THE STAGE STAGE ID OF THE CREATION OF NEW DATE
// ####################################################################

if (!function_exists('get_stage_details')) {

    function get_stage_details($id) {
        $ci = & get_instance();
        $qry = $ci->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $id)->get()->row();
        return ( $qry ) ? $qry : false;
    }

}

if (!function_exists('get_stage_start')) {

    function get_stage_start() {
        $ci = & get_instance();
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;
        $moduleid = $ci->db->select('*')->from('prime_transaction_flow_main')->where('moduleid', $moduleid)->get()->row()->sysid;
        $moduleid = $ci->db->select('*')->from('prime_transaction_flow_main_stages')->where('flowid', $moduleid)->order_by('levels', 'asc')->get()->row()->sysid;
        return ( $moduleid ) ? $moduleid : false;
    }

}
if (!function_exists('get_stage_start_module')) {

    function get_stage_start_module() {
        $ci = & get_instance();
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;
        return ( $moduleid ) ? $moduleid : false;
    }

}

if (!function_exists('get_stage_trn_info')) {

    function get_stage_trn_info($routeto, $trnid) {
        $ci = & get_instance();
        $query_stages = $ci->db->select('*')->from('prime_transaction_flow_main_stages')->where('sysid', $routeto)->get()->row();
        $moduleid = ( $query_stages ) ? $query_stages->moduleid : false;
        $query_stages_info = $ci->db->select('sysid, trnid, stageid, dataid, createdby, datecreated')->from('transaction_request_main_trails')->get()->row();
        $query_module_info = $ci->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $query_stages_info->sysid)->get()->row();

        /*
          $query = $ci->db->query("
          SELECT TRM.origid AS ORIGIN, TRM.codes CODES, TFMS.`desc` DESCS, TFMS.moduleid AS MODULEID, TRM.stagesid AS STAGEID FROM transaction_request_main AS TRM
          INNER JOIN prime_transaction_flow_main AS TFM ON TFM.moduleid = TRM.origid
          INNER JOIN prime_transaction_flow_main_stages AS TFMS ON  TFMS.flowid = TFM.sysid
          WHERE TFMS.sysid = $routeto AND TRM.sysid = $trnid")->row();
         */

        $query = array('MODULEID' => $moduleid, 'ORIGIN' => $query_module_info->moduleid, 'CODES' => $query_module_info->desc, 'DESCS' => $query_module_info->desc, 'STAGEID' => $query_module_info->sysid);

        return ( $query ) ? (object) $query : false;
    }

}
if (!function_exists('row_trn_flow')) {

    function row_trn_flow($origid, $dataid) {
        $ci = & get_instance();
        $html = '<div class="panel-group accordion">';
        $where_arr = array('origid' => $origid, 'dataid' => $dataid);

        $qry_top = $ci->db->select("*")->from("transaction_request_main")->where($where_arr)->order_by('datecreated', 'desc')->get()->row();
        $qry_top_num = count($qry_top);
        if ($qry_top) {
            $username = get_users_info($qry_top->createdby)->username;
            $firstname = get_users_info($qry_top->createdby)->firstname;
            $stat_info_top = ( $qry_top->status == 1 ) ? 'Done' : 'Pending';
            $html .= '<div class="row"><div class="col-md-12 row-flow-prime " style="margin-bottom: 5px; padding-right: 20px !important;">
					  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion' . $dataid . '" href="#collapse_' . $dataid . '" style="border-bottom: 1px transparent solid !important; height: 25px; display: inline-block !important; width: 100%; margin-bottom: 0px;">
					  <span class="pull-left btn" style="margin-right: 10px"><i class="pull fa fa-plus-square-o "></i></span> 
					  <strong>' . $qry_top->codes . ' - ' . get_module_name($qry_top->moduleid)->desc . '</strong><span class="text-info pull-right">' . $stat_info_top . '</span><br>
					  <em><i class="fa fa-user"></i> ' . $username . ' / <strong>' . $firstname . '</strong><em class="pull-right" style="font-size: 9px">' . $qry_top->datecreated . '</em></em></a>
					  </div></div>';

            $qry_flow = $ci->db->select("*")->from("transaction_request_main")->where($where_arr)->order_by('datecreated', 'desc')->get();

            $html .= '<div id="collapse_' . $dataid . '"  class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">';
            if ($qry_flow->num_rows() > 0) {
                $num = $qry_flow->num_rows();
                foreach ($qry_flow->result() as $row) {
                    $row_num = $num--;
                    if ($qry_top->sysid != $row->sysid) {
                        $stat_info = ( $row->status == 1 ) ? 'Done' : 'Pending';
                        $html .= '<div class="row popovers" data-container="body" data-trigger="hover" data-placement="top" data-content="Popover body goes here! Popover body goes here!" data-original-title="Popover in top"><div class="col-md-10 col-md-offset-1" style="border-bottom: 1px transparent solid !important; height: 25px; display: inline-block !important; width: 90%;"><span class="label label-info" style="width: 50px; text-align: right; display: inline-block;">' . $row_num . '</span><span class="label label-default" style="  text-align: left !important;">' . $row->codes . '</span><em class="pull-right" style="font-size: 9px">' . $row->datecreated . '</em></div></div>';
                    }
                }
            }
        }
        $html .= '</div>';
        return $html;
    }

}
// ####################################################################


if (!function_exists('get_stage_specific')) {

    function get_stage_specific($stageid) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM prime_transaction_flow_main_stages WHERE sysid = $stageid")->row();
        return ( $query ) ? $query : false;
    }

}


if (!function_exists('task_ins_process')) {

    function task_ins_process($trail = NULL, $logs = NULL, $comm = NULL) {
        $data = array();
        $ci = & get_instance();
        $ci->db->trans_begin();
        if (!empty($trail)) {
            $ci->db->update('transaction_request_main_trails', array('status' => 0), array('trnid' => $trail['trnid'] , 'dataid' => $trail['dataid'] , 'status' => 1));
            $trail = $ci->db->insert('transaction_request_main_trails', $trail);
            $trail_id = $ci->db->insert_id();
        }

        if (!empty($logs) && $trail_id) {
            $ci->db->set('trailid', $trail_id);
            $logs = $ci->db->insert('transaction_request_trails_logs', $logs);
        }

        if (!empty($comm)) {
            $comm = $ci->db->insert('transaction_request_trails_logs', $comm);
        }


        if ($ci->db->trans_status()) {
            $ci->db->trans_commit();
            $data['trailid'] = $trail_id;
            $q = true;
        } else {
            $ci->db->trans_rollback();
            $data['trailid'] = '';
            $q = false;
        }

        $data['qry'] = $q;
        return (object) $data;
    }

}

if (!function_exists('task_user_access')) {

    function task_user_access($levelid) {
        $ci = & get_instance();
        $user_group_arr = get_users_roles_matrix_id_arr();
        $user_id = user_session()->system_user_sessid;
        $data = array();
        $query_specific = $ci->db->select('canskip, cansendback')->from('prime_transaction_flow_main_stages_owners')->where('levelid', $levelid)->where('ownerspecific', $user_id)->get()->row();
        if ($query_specific) {
            $data['access'] = true;
            $data['canskip'] = $query_specific->canskip;
            $data['cansendback'] = $query_specific->cansendback;
        } else {
            $query_group = $ci->db->select('canskip, cansendback')->from('prime_transaction_flow_main_stages_owners')->where('levelid', $levelid)->where_in('ownergroup', $user_group_arr)->get()->row();
            if ($query_group) {
                $data['access'] = true;
                $data['canskip'] = $query_group->canskip;
                $data['cansendback'] = $query_group->cansendback;
            } else {
                $data['access'] = false;
                $data['canskip'] = false;
                $data['cansendback'] = false;
            }
        }
        return (object) $data;
    }

}

if (!function_exists('task_flows_details')) {

    function task_flows_details($trnid) {
        $ci = & get_instance();
        $user_group_arr = explode(',', user_session()->system_user_sessroles);
        $user_id = user_session()->system_user_sessid;
        $get_module_trn = $ci->db->query("SELECT * FROM transaction_request_main WHERE $trnid")->row();
        $get_module_details = $ci->db->query("SELECT * FROM prime_transaction_flow_main WHERE moduleid = {$get_module_trn->moduleid}")->row();
        $get_module_details_stages = $ci->db->query("SELECT * FROM prime_transaction_flow_main_stages WHERE flowid = {$get_module_details->sysid} AND sysid = {$get_module_trn->stagesid}")->row();
        $get_module_details_stages_owner = $ci->db->select("*")->from('prime_transaction_flow_main_stages_owners')->where_in('ownergroup', $user_group_arr)->where('levelid', $get_module_details_stages->sysid)->get()->row();
        return ( $get_module_details_stages_owner ) ? $get_module_details_stages_owner : false;
    }

}

if (!function_exists('task_flow_stage_sps')) {

    function task_flow_stage_sps($dataid, $moduleid) {
        $ci = & get_instance();
        $query_flow = $ci->db->query("
			SELECT * FROM transaction_request_main AS TRN
			INNER JOIN prime_transaction_flow_main AS MAIN ON MAIN.moduleid = TRN.origid
			INNER JOIN prime_transaction_flow_main_stages AS STAGES ON STAGES.flowid = MAIN.sysid AND STAGES.moduleid = TRN.moduleid
			WHERE TRN.dataid = $dataid AND TRN.moduleid = $moduleid AND STAGES.types = 0 ORDER BY TRN.sysid DESC LIMIT 1 
			")->row();
        return ( count($query_flow) > 0 ) ? $query_flow : false;
    }

}

if (!function_exists('task_flows_stages')) {
    function task_flows_stages() {
        $ci = & get_instance();
        $dataid = $ci->uri->segment(5);
        $trnid = $ci->uri->segment(4);
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;

        $qry_trn = $ci->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
        $qry_stg = $ci->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();

        $qry = $ci->db->select('sysid AS STID, desc, levels AS LEVEL')->from('prime_transaction_flow_main_stages')->where(array('flowid' => $qry_stg->flowid, 'status' => 1))->order_by('levels')->get();

        //$qry_trl = $ci->db->select()->from('transaction_request_main_trails')->where(array('dataid' => $dataid, 'stageid' => $qry_stg->sysid))->get()->row();

        return ( $qry->num_rows() > 0 ) ? $qry->result() : false;
    }
}


if (!function_exists('task_views')) {

    function task_views() {
        $ci = & get_instance();
        $dataid = $ci->uri->segment(4);
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;
        $data['trnqry'] = $ci->db->query("SELECT * FROM transaction_request_main WHERE dataid = $dataid AND moduleid = $moduleid ORDER BY sysid DESC")->row();
        $ci->load->view('admin/common/task', $data);
    }

}
if (!function_exists('inv_views')) {

    function inv_views() {
        $ci = & get_instance();
        $data = array();
        $ci->load->view('admin/common/inventory', $data);
    }

}
if (!function_exists('bdg_views')) {

    function bdg_views() {
        $ci = & get_instance();
        $data = array();
        $ci->load->view('admin/common/budgets', $data);
    }

}

if (!function_exists('com_views')) {

    function com_views() {
        $ci = & get_instance();
        $data = array();
        $ci->load->view('admin/common/comm', $data);
    }

}

if (!function_exists('att_views')) {

    function att_views() {
        $ci = & get_instance();
        $data = array();
        $ci->load->view('admin/common/comm', $data);
    }

}

if (!function_exists('flow_id')) {

    function flow_id($mid) {
        $ci = &get_instance();

        $qry_flow = $ci->db->select('*')->from('prime_transaction_flow_main')->where('moduleid', $mid)->get()->row();
        return ( $qry_flow ) ? $qry_flow->sysid : false;
    }

}

if (!function_exists('init_content_data')) {
    function init_content_data($dataid, $origin)
    {
        $ci = &get_instance();
        $data = array();

        // #####################################
        // CAD APPLICATION DATA ################
        if ($origin==35) {
            $ci->load->model('model_cad');
            $data = application_info($dataid); // OPERATIONS_HELPER;
        }
        // #####################################

        $ci = & get_instance();
        $ci->load->view('admin/common/datacontent', $data); // DUMMY FILE TO LOAD ADITIONAL DATA ARRAY
    }
}

if(!function_exists('get_current_nav')) {
    function get_current_nav($id) {
        $ci = get_instance();
        $qry = $ci->db->select('sysid')->from('prime_module_navigations_main')  //
        ->where('hashcode', $id)                         //
        ->get()->row();

        return $qry;
    }
}

if (!function_exists('data_init')) {

    function data_init($dataid = false, $trnid = false) {
        $ci = & get_instance();
        if($dataid==false && $trnid==false) {
            $trnid = $ci->uri->segment(4);
            $dataid = $ci->uri->segment(5);
        }
        $userid = user_id();

        // SET TO READ TRN DETAILS
        $ci->db->insert('transaction_request_trails_logs', array('trailid' => $trnid, 'activity' => 86, 'userid' => $userid));

        $qry_trails = $ci->db->select()
            ->from('transaction_request_main_trails')
            ->where(array('sysid' => $trnid, 'dataid' => $dataid))
            ->get()->row();
        $trnflowid = ($qry_trails) ? $qry_trails->trnid : 0;

        $data['trnflowid'] = $trnflowid;
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;

        // GET FLOW ID
        $qry_stages = $ci->db->select()
            ->from('prime_transaction_flow_main_stages')
            ->where(array('moduleid' => $moduleid, 'sysid' => $qry_trails->stageid))
            ->get()->row();


        $data['trnqry'] = $ci->db->select()->from('transaction_request_main')
            ->where('flowid', $qry_stages->flowid)
            ->get()->row();

        $data['task_flow'] = true;
        // ORIGIN QUERY
        // GET FLOW ID FROM STAGES
        $stages_qry = $ci->db->select('sysid, flowid')->from('prime_transaction_flow_main_stages')
            ->where(array('flowid' => $qry_stages->flowid, 'moduleid' => $moduleid))->get()->row();
        // SEND FLOW ID TO DATA
        $data['flowid'] = $stages_qry->flowid;
        $data['trailid'] = $trnid;
        // SEND STAGE ID TO DATA
        $data['stageid'] = ($qry_trails) ? $qry_trails->stageid : 0;
        // =======================================================
        // GET START OF FLOW FROM STAGES
        $stages_start = $ci->db->select('moduleid')
            ->from('prime_transaction_flow_main_stages')
            ->where('flowid', $stages_qry->flowid)
            ->where('status', 1)
            ->order_by('levels', 'asc')
            ->get()->row();
        // =======================================================
        // GET ORGIN DETAILS
        $data['origin'] = $stages_start->moduleid;
        // =======================================================
        // GET LAST STAGE
        $trn_current = $ci->db->select()->from('transaction_request_main_trails')->where('trnid', $trnflowid)
            ->order_by('sysid', 'desc')->get()->row();
        $trn_last = ( $trn_current ) ? $trn_current->stageid : false;

        // =======================================================
        // GET STAGE IF APPROVAL
        $qry_approval = $ci->db->select('approval')->from('prime_transaction_flow_main_stages_owners')
            ->where(array('approval' => 1, 'levelid' => $trn_last))
            ->get()->row();


        $data['moduleid'] = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;
        $data['trnlast'] = $trn_last;
        $data['trnid'] = $trnflowid;

        $data['approval'] = ( $qry_approval ) ? true : false;
        // END ORIGIN QUERY
        return $data;
    }

}

if (!function_exists('data_view')) {

    function data_view($pagefilename, $page, $data = array()) {
        $ci = & get_instance();

        if ($page == 'sbj') {
            $ci->load->view('admin/pages/modules/' . $pagefilename . '/data', $data);
        } else {
            if (file_exists(FCPATH . 'application/views/admin/common/' . $page . '.php')) {
                $ci->load->view('admin/common/' . $page, $data);
            } else {
                echo 'View cant find!';
            }
        }
    }

}

if (!function_exists('status_label')) {

    function status_label($st, $id = 0) {
        $ci = & get_instance();
        $dataid = $ci->uri->segment(4);
        $moduleid = $ci->model_admin->get_navigation_specific_details($ci->uri->segment(2))->sysid;
        $check_trn_stat_query = $ci->db->query("SELECT * FROM transaction_request_main WHERE moduleid = $moduleid AND dataid = $dataid")->row();

        if ($check_trn_stat_query) {
            $t = $check_trn_stat_query->status;
            if ($t == 1) {
                $return['res'] = true;
                $return['label'] = ( $st == 1 ) ? '<span class="label label-success"><i class="fa fa-check fa-fw"></i></span>' : '<span class="label label-danger"><i class="fa fa-times fa-fw"></i></span>';
            } else {
                $return['res'] = false;
                $return['label'] = ( $st == 1 ) ? '
				<div class="md-checkbox md-checkbox-inline" style="margin: 0px 0px">
				<input checked type="checkbox" id="' . $id . '" class="md-check">
				<label for="' . $id . '">
				<span class="inc"></span>
				<span class="check"></span>
				<span class="box"></span>
				</label>
				</div>
				' : '
				<div class="md-checkbox md-checkbox-inline" style="margin: 0px 0px">
				<input type="checkbox" id="' . $id . '" class="md-check">
				<label for="' . $id . '">
				<span class="inc"></span>
				<span class="check"></span>
				<span class="box"></span>
				</label>
				</div>
				';
            }
        } else {
            $return['res'] = false;
            $return['label'] = '<span class="label label-warning tooltips " data-container="body" data-placement="left" data-html="true" data-original-title="Data is compromise, please check TRN table."><i class="fa fa-warning fa-fw"></i></span>';
        }

        return (object) $return;
    }

}

if (!function_exists('update_stats')) {

    function update_stats($tbl, $id) {
        $ci = & get_instance();
        $get_stat = $ci->db->query("SELECT status FROM $tbl WHERE sysid = $id")->row();
        $ci_stat = $get_stat->status;
        if ($ci_stat == 1) {
            $stat = 0;
        } else {
            $stat = 1;
        }
        $arr = array('status' => $stat);
        $ci->db->where('sysid', $id);
        $up = $ci->db->update($tbl, $arr);
        $ret = (object) array('ret' => $stat, 'upd' => $up);
        return ($get_stat && $up) ? $ret : false;
    }

}

if (!function_exists('row_stats_basic')) {

    function row_stats_basic($val) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM prime_system_status_parameter WHERE sysid = $val")->row();
        if ($query) {
            return '<span class="label label-' . $query->colors . '"><i class="fa fa-file"></i> ' . $query->code . '</span>';
        } else {
            return '<span class="label label-info"><i class="fa fa-question"></i> N/A</span>';
        }
    }

}
if (!function_exists('row_btn_view')) {

    function row_btn_view($val, $dataid, $basic = false, $title = NULL) {
        $ci = & get_instance();
        if ($basic == true) {
            return '<a href="' . base_url('module/' . $val . '/view/' . $dataid) . '" class="btn btn-primary hidden-print btn-xs pull-right"><i class="fa fa-info-circle fa-fw"></i> ' . $title . '</a>';
        } else {

            // GET THE ORIG FLOW DETAILS
            $check_trn_stat_query = $ci->db->query("SELECT * FROM transaction_request_main WHERE sysid = $val")->row();

            // GET THE ORIGIN ID
            $check_module_details_orgin = $ci->db->query("SELECT * FROM transaction_request_main WHERE dataid = " . $check_trn_stat_query->dataid . " ORDER BY sysid ASC")->row();

            // GET THE MODULE DETAILS
            $check_module_details = $ci->db->query("SELECT * FROM prime_module_navigations_main WHERE sysid = " . $check_module_details_orgin->moduleid)->row();


            if ($check_trn_stat_query) {
                // GET HASH DATA NAVIGATION
                $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
                $nav_hash = ( $data_query ) ? $data_query->namehash : '';
                return '<a href="' . base_url('module/' . $check_module_details->hashcode . '/' . $nav_hash . '/' . $check_module_details_orgin->dataid) . '" class="btn btn-primary btn-xs hidden-print"><i class="fa fa-file fa-fw"></i></a>';
            } else {
                return '<span class="label label-danger"><i class="fa fa-times"></i></span>';
            }
        }
    }

}
if (!function_exists('row_btn_basic')) {

    function row_btn_basic($val, $dataid) {
        $ci = & get_instance();

        // GET THE LAST FLOW DETAILS
        $last_trails = $ci->db->select()->from('transaction_request_main_trails')->where('sysid', $val)->order_by('datecreated', 'desc')->get()->row();

        $last_modules = $ci->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $last_trails->stageid)->get()->row();

        //return 'TRAILID:'.$last_trails->sysid . '-TRNID:' . $last_trails->trnid . '-STAGEID:' . $last_trails->stageid;
        //exit();
        // GET THE MODULE DETAILS
        $check_module_details = $ci->db->select()->from('prime_module_navigations_main')->where('sysid', $last_modules->moduleid)->get()->row();


        if ($check_module_details) {
            // GET HASH DATA NAVIGATION
            $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
            $nav_hash = ( $data_query ) ? $data_query->namehash : ''; // return '<a href="' . base_url('module/' . $check_module_details->hashcode . '/7fbb727db4b2b6715b092505673cb5922a0d63a8/' . $check_trn_stat_query->dataid) . '" class="btn btn-info btn-sm"><i class="fa fa-search fa-fw"></i></a>';
            return '<a href="' . base_url('module/' . $check_module_details->hashcode . '/' . $nav_hash . '/' . $last_trails->sysid) . '/' . $last_trails->dataid . '" class="btn btn-info btn-xs"><i class="fa fa-search fa-fw"></i></a>';
        } else {
            return '<span class="label label-danger"><i class="fa fa-times"></i></span>';
        }
    }

}

if (!function_exists('toastr_link')) {

    function toastr_link($val, $dataid) {
        $ci = & get_instance();

        // GET THE LAST FLOW DETAILS
        $last_trails = $ci->db->select()->from('transaction_request_main_trails')->where('sysid', $val)->order_by('datecreated', 'desc')->get()->row();

        $last_modules = $ci->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $last_trails->stageid)->get()->row();

        //return 'TRAILID:'.$last_trails->sysid . '-TRNID:' . $last_trails->trnid . '-STAGEID:' . $last_trails->stageid;
        //exit();
        // GET THE MODULE DETAILS
        $check_module_details = $ci->db->select()->from('prime_module_navigations_main')->where('sysid', $last_modules->moduleid)->get()->row();


        if ($check_module_details) {
            $q = true;
            // GET HASH DATA NAVIGATION
            $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
            $nav_hash = ( $data_query ) ? $data_query->namehash : ''; // return '<a href="' . base_url('module/' . $check_module_details->hashcode . '/7fbb727db4b2b6715b092505673cb5922a0d63a8/' . $check_trn_stat_query->dataid) . '" class="btn btn-info btn-sm"><i class="fa fa-search fa-fw"></i></a>';
            $link = base_url('module/' . $check_module_details->hashcode . '/' . $nav_hash . '/' . $last_trails->sysid) . '/' . $last_trails->dataid;
        } else {
            $q = false;
            $link = '';
        }
        $data['qry'] = $q;
        $data['link'] = $link;
        return (object) $data;
    }

}

if (!function_exists('btn_view_trn')) {

    function btn_view_trn($trnid, $dataid, $view = NULL, $target = '_top') {
        $ci = & get_instance();
        if ($view == 'profile') {
            $qry_trail_details = $ci->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
            $qry_trail_starts = $ci->db->select()->from('transaction_request_main_trails')->where('trnid', $qry_trail_details->trnid)->order_by('datecreated')->get()->row();
            $qry_stage_details = $ci->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $qry_trail_starts->stageid)->get()->row();
            $check_module_details = $ci->db->select()->from('prime_module_navigations_main')->where('sysid', $qry_stage_details->moduleid)->get()->row();
        } else {
            $qry_trail_details = $ci->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
            $qry_stage_details = $ci->db->select()->from('prime_transaction_flow_main_stages')->where('sysid', $qry_trail_details->stageid)->get()->row();
            $check_module_details = $ci->db->select()->from('prime_module_navigations_main')->where('sysid', $qry_stage_details->moduleid)->get()->row();
        }
        $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
        $nav_hash = ( $data_query ) ? $data_query->namehash : ''; // return '<a href="' . base_url('module/' . $check_module_details->hashcode . '/7fbb727db4b2b6715b092505673cb5922a0d63a8/' . $check_trn_stat_query->dataid) . '" class="btn btn-info btn-sm"><i class="fa fa-search fa-fw"></i></a>';
        if ($check_module_details) {
            // GET HASH DATA NAVIGATION
            if ($view == 'task') {
                $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'TASK')->get()->row();
                $nav_hash = ( $data_query ) ? $data_query->namehash : '';
                $btn_icon = 'fa-sign-out';
                $btn_type = 'btn-default';
            } else if ($view == 'profile') {
                $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
                $nav_hash = ( $data_query ) ? $data_query->namehash : '';
                $btn_icon = 'fa-user';
                $btn_type = 'btn-primary';
            }  else if ($view == 'comments') {
                $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'COMMENTS')->get()->row();
                $nav_hash = ( $data_query ) ? $data_query->namehash : '';
                $btn_icon = 'fa-comments';
                $btn_type = 'btn-success';
            }   else if ($view == 'send' || $view == 'back') {
                $nav_hash = '';
                $btn_icon = 'fa-paper-plane';
                $btn_type = 'btn-primary';
                $app_flow_ids_arr = flow_id_arr($qry_stage_details->flowid);
                $app_flow_ids = ($app_flow_ids_arr) ? implode(',', $app_flow_ids_arr) : false;
                $flow_ids_where = ($app_flow_ids_arr) ? " AND rm.flowid IN ($app_flow_ids) " : "";

                $qry_trails_last = $ci->db->query("
                    SELECT rm.sysid AS trnmid, rmt.sysid, rmt.datecreated, rmt.createdby, rmt.stageid, rmt.dataid, rmt.datecreated AS logdate
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    WHERE rmt.dataid = $dataid 
                    AND rmt.`status` = 1
                    $flow_ids_where
                    ORDER BY rmt.datecreated DESC
                ")->row();

                $trnid = ($qry_trails_last) ? $qry_trails_last->trnmid: 0;
            } else {
                $data_query = $ci->db->select()->from('transaction_request_navigations')->where('codes', 'DATA')->get()->row();
                $nav_hash = ( $data_query ) ? $data_query->namehash : '';
                $btn_icon = 'fa-search';
                $btn_type = 'btn-info';
            }
            if ($view == 'profile') {
                return '<a target="'.$target.'" title="Send Profile." data-content="body" href="' . base_url('module/' . $check_module_details->hashcode . '/view/' . $dataid) . '" class="btn ' . $btn_type . ' btn-xs inline tooltips"><i class="fa ' . $btn_icon . ' fa-fw"></i></a>';
            } else if ($view == 'comments') {
                return '<a target="'.$target.'" title="View comments." data-content="body"href="' . base_url('module/' . $check_module_details->hashcode . '/' . $nav_hash . '/' . $trnid) . '/' . $dataid . '" class="btn ' . $btn_type . ' btn-xs inline tooltips"><i class="fa ' . $btn_icon . ' fa-fw"></i></a>';
            } else if ($view == 'send') {
                return '<a target="'.$target.'" title="Send to next route." data-content="body" data-placement="right" id="btn_send_trn_next" data-id="'.$dataid.'" data-trnid="'.$trnid.'" href="javascript:;" class="btn ' . $btn_type . ' btn-xs inline tooltips"><i class="fa ' . $btn_icon . ' fa-fw"></i></a>';
            } else if ($view == 'back') {
                return '<a target="'.$target.'" title="Send back." data-content="body" data-placement="left" id="btn_send_trn_back" data-id="'.$dataid.'" data-trnid="'.$trnid.'" href="javascript:;" class="btn btn-warning btn-xs inline tooltips"><i class="fa fa-reply fa-fw"></i></a>';
            } else {
                return '<a target="'.$target.'" title="View Subject." data-content="body" href="' . base_url('module/' . $check_module_details->hashcode . '/' . $nav_hash . '/' . $trnid) . '/' . $dataid . '" class="btn ' . $btn_type . ' btn-xs inline tooltips"><i class="fa ' . $btn_icon . ' fa-fw"></i></a>';
            }
        } else {
            return '<span class="label label-danger"><i class="fa fa-times"></i></span>';
        }
    }

}


if (!function_exists('user_session')) {

    function user_session() {
        $ci = & get_instance();
        return (object) $ci->session->userdata('logged_in');
    }

    // system_user_sessid
}

if (!function_exists('user_login')) {

    function user_login() {
        $ci = & get_instance();
        return $ci->session->userdata('logged_in');
    }

}

if (!function_exists('hashing')) {

    function hashing($str) {
        return password_hash($str, PASSWORD_DEFAULT);
    }

}


if (!function_exists('hashvalidate')) {

    function hashvalidate($str, $pass) {
        if (password_verify($str, $pass)) {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('return_message_ajax')) {

    function return_message_ajax($class, $icon, $msg) {
        return '<span class="text-' . $class . '"><i class="fa ' . $icon . '"></i> ' . $msg . '</span>';
    }

}

if (!function_exists('check_nav_uri')) {

    function check_nav_uri($controller) {
        $ci = & get_instance();
        $class = $ci->router->fetch_class();
        return ( $class == $controller ) ? 'active' : '';
    }

}

if (!function_exists('check_nav_sub')) {

    function check_nav_sub($controller) {
        $ci = & get_instance();
        $class = $ci->router->fetch_method();
        return ( $class == $controller ) ? 'active' : '';
    }

}

if (!function_exists('check_user_lock')) {

    function check_user_lock() {
        $ci = & get_instance();
        $check_lock = $ci->model_auth->get_user_logs(user_id());
        if ( $check_lock && $check_lock->sessionlogtype == 2 )
        {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('init_userpage_data')) {
    function init_userpage_data() {
        $ci = & get_instance();
        // ##########################
        $query_userinfo = $ci->db->select('su.sysid, su.username, su.type, su.idletime, suim.firstname, suim.lastname, suim.middlename, suim.gender')
            ->from('prime_system_users su')
            ->join('prime_system_users_info_main AS suim', 'suim.userid = su.sysid', 'left')
            ->join('prime_system_users_info_img AS suii', 'suii.userid = su.sysid', 'left')
            ->where(array('suim.status' => 1, 'su.sysid' => user_id()))
            ->group_by('su.sysid, su.sysid, su.username, su.type, su.idletime, suim.firstname, suim.lastname, suim.middlename, suim.gender')
            ->get()->row();

        // ###########################
        $userinfo = ( $query_userinfo ) ? $query_userinfo : false;
        $query_modules = $ci->db->select('sysid, code, name, desc, icon, type')->from('prime_module_main')->order_by('sorting', 'asc')->get();
        $select_modules = ( $query_modules->num_rows() > 0 ) ? $query_modules->result() : false;

        // ###########################
        $data['userdata'] = $userinfo;
        $data['profiledata'] = $userinfo;
        $data['usersmodule'] = $select_modules;
        return $data;
    }
}

if (!function_exists('init_header')) {

    function init_header($data = false) {
        $ci = & get_instance();
        if($data == false) {
            $data = init_userpage_data();
        }
        $ci->load->view('admin/common/head', $data);
        $ci->load->view('admin/common/topnav', $data);
        $ci->load->view('admin/common/leftnav', $data);
    }

}

if (!function_exists('init_header_nonav')) {

    function init_header_nonav($data = false) {
        $ci = & get_instance();
        if($data == false) {
            $data = init_userpage_data();
        }
        $ci->load->view('admin/common/head', $data);
    }

}
if (!function_exists('init_frontend_header')) {

    function init_frontend_header($data = false) {
        $ci = & get_instance();
        if($data == false) {
            $data = init_userpage_data();
        }
        $ci->load->view('admin/common/head', $data);
    }

}

if (!function_exists('init_frontend_navs')) {

    function init_frontend_navs($data = false) {
        $ci = & get_instance();
        if($data == false) {
            $data = init_userpage_data();
        }
        $ci->load->view('frontend/common/navs', $data);
    }

}

if (!function_exists('init_page_wrapper_top')) {

    function init_page_wrapper_top($data = false) {
        $ci = & get_instance();
        $ci->load->view('admin/common/modulepagewrappertop', $data);
    }

}

if (!function_exists('init_page_wrapper_bottom')) {
    function init_page_wrapper_bottom($data = false) {
        $ci = & get_instance();
        $ci->load->view('admin/common/modulepagewrapperbottom', $data);
    }
}

if (!function_exists('init_frontend_page_top')) {
    function init_frontend_page_top($data = false) {
        $ci = & get_instance();
        $ci->load->view('frontend/common/pagetop', $data);
    }
}

if (!function_exists('init_frontend_page_bottom')) {
    function init_frontend_page_bottom($data = false) {
        $ci = & get_instance();
        $ci->load->view('frontend/common/pagebottom', $data);
    }
}

if (!function_exists('init_footer')) {

    function init_footer($data = false, $addscript = false) {
        $ci = & get_instance();
        if($data == false) {
            $data = init_userpage_data();
        }
        $ci->load->view('admin/common/rightbar', $data);
        $ci->load->view('admin/common/footer', $data);
        $ci->load->view('admin/common/scripts', $data);
        if (!empty($addscript) || $addscript != false) {
            $ci->load->view('includes/scripts/' . $addscript);
        }
        $ci->load->view('admin/common/end');
    }

}

if (!function_exists('init_footer_nonav')) {
    function init_footer_nonav($data = false) {
        $ci = & get_instance();
        $ci->load->view('admin/common/scripts', $data);
        $ci->load->view('admin/common/end');
    }
}

if (!function_exists('init_frontend_footer')) {

    function init_frontend_footer($data = false, $addscript = false) {
        $ci = & get_instance();
        $ci->load->view('admin/common/scripts', $data);
        if($addscript != '') {
            $ci->load->view($addscript, $data);
        }
        $ci->load->view('admin/common/end');
    }
}

if (!function_exists('init_content_top')) {

    function init_content_top($data) {
        $ci = & get_instance();
        $ci->load->view('admin/common/datacontenttop', $data);
    }

}
if (!function_exists('init_content_bottom')) {

    function init_content_bottom() {
        $ci = & get_instance();
        $ci->load->view('admin/common/datacontentbottom');
    }

}

if (!function_exists('get_item_type')) {

    function get_item_type($str) {
        $ci = & get_instance();
        $q = $ci->db->select('sysid, names, desc')
            ->from('prime_types_parameter')
            ->where('codes', $str)
            ->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data['list'][] = array('id' => $row->sysid, 'text' => $row->desc);
        }
        return json_encode($data);
    }

}

if (!function_exists('get_acct_type')) {

    function get_acct_type($id) {
        $ci = & get_instance();
        $q = $ci->db->select('sysid, names')->from('prime_types_parameter')->where('sysid', $id)->get()->row();
        return ( $q ) ? $q->names : false;
    }

}

if (!function_exists('check_module_process_flow')) {

    function check_module_process_flow($moduleid) {
        $ci = & get_instance();
        $q = $ci->db->select('*')->from('prime_transaction_flow_main_stages_modules')->order_by('levelid', 'asc')->get()->row();
        return ( $q ) ? $q : false;
    }

}


if (!function_exists('get_requirement_name')) {
    function get_requirement_name($id) {
        $ci = & get_instance();
        $q = $ci->db->select()
            ->from("prime_requirement_parameters")
            ->where(array('sysid'=>$id , 'status'=>1))
            ->get()->row();
        return ( $q ) ?: false;
    }
}

if (!function_exists('get_item_type_requirements')) {

    function get_item_type_requirements($str, $statid, $locid) {

        $str = explode(',', $str);
        $statid = explode(',', $statid);
        $locid = explode(',', $locid);
        $ci = & get_instance();
        $q = $ci->db->select('prp.sysid AS PRPSYSID, prp.names AS PRPNAMES, prp.desc AS PRPDESC')
            ->from('requirements_parameters AS pcar')
            ->join('prime_requirement_parameters AS prp', 'prp.sysid = pcar.reqid')
            ->where_in('pcar.statusid', $statid);

        if (in_array('67', $str)) {
            $str = array(0);
            $q = $ci->db->where_in('pcar.typeid', $str);
        } else {
            $q = $ci->db->where_in('pcar.typeid', $str);
        }

        if (in_array('66', $locid)) {
            $locid = array(0);
            $q = $ci->db->where_in('pcar.locid', $locid);
        } else {
            $q = $ci->db->where_in('pcar.locid', $locid);
        }

        $q = $ci->db->group_by('prp.sysid')->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data[] = array('id' => $row->PRPSYSID, 'text' => $row->PRPNAMES);
        }
        return json_encode($data);
    }

}
if (!function_exists('get_item_type_add_requirements')) {

    function get_item_type_add_requirements($ignore) {
        $ignore = explode(',', $ignore);
        $ci = & get_instance();
        $q = $ci->db->select('sysid AS PRPSYSID, names AS PRPNAMES')->from('prime_requirement_parameters')->where_not_in('sysid', $ignore)->group_by('sysid')->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data[] = array('id' => $row->PRPSYSID, 'text' => $row->PRPNAMES);
        }
        return json_encode($data);
    }

}
if (!function_exists('get_class_rate')) {

    function get_class_rate() {
        $ci = & get_instance();
        $q = $ci->db->select('sysid, codes, classifications')
            ->from('prime_system_rate_class_main')
            ->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data[] = array('id' => $row->sysid, 'text' => $row->codes . ' - ' . $row->classifications);
        }
        return json_encode($data);
    }
}


if (!function_exists('select2_rate_class')) {

    function selec2_accttype($arr = array()) {
        $ci = & get_instance();
        if($arr) {
            $ci->db->where_in('sysid', $arr);
        }
        $q = $ci->db->select('sysid, codes, classifications')
            ->from('prime_system_rate_class_main')
            ->where('status', 1)
            ->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data['list'][] = array('id' => $row->sysid, 'text' => $row->codes . ' - ' . $row->classifications);
        }
        return json_encode($data);
    }

}

if (!function_exists('get_account_rate')) {

    function get_account_rate($ids) {
        $ci = & get_instance();
        $ids = explode(',', $ids);
        $ci->db->where_in('sysid', $ids);
        $qry = $ci->db->select('*')->from('prime_system_rate_class_main')->get();
        return ( $qry->num_rows() > 0 ) ? $qry->result() : false;
    }

}

if (!function_exists('get_item_category')) {

    function get_item_category() {
        $ci = & get_instance();
        $q = $ci->db->select('sysid, names')->from('prime_category_parameter')->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data[] = array('id' => $row->sysid, 'text' => $row->names);
        }
        return json_encode($data);
    }

}

if (!function_exists('get_item_specification')) {

    function get_item_specification() {
        $ci = & get_instance();
        $q = $ci->db->select('sysid, names')->from('prime_category_specification_parementer')->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data[] = array('id' => $row->sysid, 'text' => $row->names);
        }
        return json_encode($data);
    }

}

if (!function_exists('get_trn_arr_specific')) {

    function get_trn_arr_specific($arr, $details, $val) {
        $ci = & get_instance();
        $val_arr = explode(':', $arr[$details]);
        return $val_arr[$val];
    }

}

if (!function_exists('get_users_info')) {

    function get_users_info($id = NULL, $force = false) {
        $ci = & get_instance();
        if( $force == true) {
            if ($id == NULL) {
                $userid = user_id();
                $q = $ci->db->select('u.sysid, u.allowexternal, u.landing, p.sysid as pid, p.firstname, p.lastname, u.username')
                    ->from('prime_system_users AS u')
                    ->join('person AS p', 'p.sysid = u.personid', 'left')
                    ->where('u.sysid', $userid)
                    ->get()->row();
                if($q && ($q->firstname == '' || $q->lastname == '')) {
                    $q = $ci->db->select('u.sysid, u.allowexternal, u.landing, u.firstname, u.lastname, u.username')
                        ->from('prime_system_users AS u')
                        ->where('u.sysid', $userid)
                        ->get()->row();
                }
            } else {
                $q = $ci->db->select('u.sysid, u.allowexternal, u.landing, p.sysid as pid, p.firstname, p.lastname, u.username')
                    ->from('prime_system_users AS u')
                    ->join('person AS p', 'p.sysid = u.personid', 'left')
                    ->where('u.sysid', $id)
                    ->get()->row();
                if($q && ($q->firstname == '' || $q->lastname == '')) {
                    $q = $ci->db->select('u.sysid, u.allowexternal, u.landing, u.firstname, u.lastname, u.username')
                        ->from('prime_system_users AS u')
                        ->where('u.sysid', $id)
                        ->get()->row();
                }
            }
        }else{
            $q = $ci->db->select('u.sysid, u.allowexternal, u.landing, p.sysid as pid, p.firstname, p.lastname, u.username')
                ->from('prime_system_users AS u')
                ->join('person AS p', 'p.sysid = u.personid', 'left')
                ->where('u.sysid', $id)->get()->row();
            if($q && ($q->firstname == '' || $q->lastname == '')) {
                $q = $ci->db->select('u.sysid, u.allowexternal, u.landing, u.firstname, u.lastname, u.username')
                    ->from('prime_system_users AS u')
                    ->where('u.sysid', $id)
                    ->get()->row();
            }
        }
        return ( $q ) ? $q : false;
    }

}
if (!function_exists('get_person_info')) {

    function get_person_info($id) {
        $ci = & get_instance();
        $q = $ci->db->select('p.sysid, p.firstname, '
            . 'p.middlename, '
            . 'p.lastname, '
            . 't.titleid as suffixid, '
            . 'tm.names as suffix, '
            . 'g.name AS gender, '
            . 'p.gender AS genderid, '
            . 'p.birthdate, '
            . 'p.nickname, '
            . 'addr.addrspec, '
            . 'd.names as district, '
            . 'c.names as city, '
            . 'cc.country as country, '
            . 'cc.nationality as nationality, '
            . 'cont.contactstring AS contact ')
            ->from('person AS p')
            ->join('person_title AS t', 't.personid = p.sysid', 'left')
            ->join('person_title_main AS tm', 't.titleid = tm.sysid AND tm.types = 70', 'left')
            ->join('person_address_matrix AS addr', 'addr.personid = p.sysid', 'left')
            ->join('address_districts AS d', 'addr.addrdist = d.sysid', 'left')
            ->join('address_city AS c', 'addr.addrcity = c.sysid', 'left')
            ->join('address_country AS cc', 'addr.addrcountry = cc.sysid', 'left')
            ->join('prime_gender AS g', 'g.sysid = p.gender', 'left')
            ->join('person_contact_matrix AS cont', 'cont.personid = p.sysid', 'left')
            ->where('p.sysid', $id)
            ->group_by('p.sysid, p.firstname, p.middlename, '
                . 'p.lastname, '
                . 'g.name, '
                . 'p.gender, '
                . 'p.birthdate, '
                . 'p.nickname, '
                . 'addr.addrspec, '
                . 'd.names, '
                . 'c.names, '
                . 'cc.country, '
                . 'cc.nationality, '
                . 'cont.contactstring ')->get()->row();

        if ($q) {

            $marital = $ci->db->select('marital_status_id')
                ->from('persons_marital_status_logs')
                ->where(array('personid' => $id, 'status' => 1))
                ->order_by('datecreated', 'desc')
                ->get()->row();



            $map = directory_map('./uploads/person/'.$q->sysid.'/', FALSE, TRUE);
            if($map && count($map)){
                $pics = base_url('uploads/person/'.$q->sysid.'/'.$map[0]);
            } else {
                if($q->genderid==1) {
                    $pics = base_url('assets/global/img/default_avatar_male.png');
                }else if ($q->genderid==2){
                    $pics = base_url('assets/global/img/default_avatar_female.png');
                }else{
                    $pics = base_url('assets/global/img/person_default.jpg');
                }
            }

            //$pic_recent = ($map && count($map) > 0) ? base_url('uploads/corporation/' . $qry_corp_app->corpid . '/' . $map[0]) : base_url('assets/global/img/not-available.png');



            $data['info'] = (object)array(
                'sysid' => $q->sysid,
                'pics' => $pics,
                'firstname' => $q->firstname,
                'middlename' => $q->middlename,
                'lastname' => $q->lastname,
                'suffix' => $q->suffix,
                'suffixid' => $q->suffixid,
                'gender' => $q->gender,
                'genderid' => $q->genderid,
                'birthdate' => $q->birthdate,
                'nickname' => $q->nickname,
                'addrspec' => $q->addrspec,
                'city' => $q->city,
                'district' => $q->district,
                'country' => $q->country,
                'nationality' => $q->nationality,
                'contact' => $q->contact,
                'marital' => ($marital) ? $marital->marital_status_id : 1,
                'companyemail' => get_person_contact($q->sysid, 1057),
                'personalemail' => get_person_contact($q->sysid, 1053),
                'telephone' => get_person_contact($q->sysid, 2),
                'mobilephone' => get_person_contact($q->sysid, 1051),
            );
            $data['qry'] = true;
        }else{
            $data['qry'] = false;
        }
        return (object) $data;
    }

}

if (!function_exists('get_person_contact')) {
    function get_person_contact($id, $type) {
        $ci = &get_instance();
        $qry = $ci->db->select('contactstring')->from('person_contact_matrix')
            ->where(array('personid' => $id, 'types' => $type, 'status'=> 1))
            ->order_by('datecreated', 'desc')->get()->row();
        return ($qry) ? $qry->contactstring : 'None';
    }
}

if (!function_exists('get_owner_pic')) {
    function get_owner_pic($id, $dir, $types = 1) {
        $ci = &get_instance();
        $pic = '';
        $profile_pic_filename_last = '';
        $profile_pic_filename_first = '';
        $gender = false;

        if($types == 1) {
            $get_info = get_person_info($id);
            $gender = ($get_info && isset($get_info->info)) ? $get_info->info->genderid : 1;
        }

        if (file_exists(FCPATH . 'uploads/' . $dir . '/' . $id)) {

            /*
            if( $types == 2 ) {
                $map = directory_map('./uploads/corporation/' . $id . '/', FALSE, TRUE);
                //$pic = ($map && count($map) > 0) ? 'uploads/corporation/' . $id . '/' . $map[0] : 'assets/global/img/not-available.png';
            }else{
                $map = directory_map('./uploads/person/' . $id . '/', FALSE, TRUE);
                //$pic = ($map && count($map) > 0) ? 'uploads/person/' . $id . '/' . $map[0] : 'assets/global/img/person_default.jpg';
            }
            */


            //$map = directory_map('./uploads/'.$dir.'/' . $id . '/', FALSE, TRUE);

            $map = glob(FCPATH . 'uploads/' . $dir . '/' . $id . '/primary*.*');
            //usort($check_primary_file, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
            array_multisort(
                array_map('filemtime', $map), SORT_NUMERIC, SORT_DESC, $map
            );

            $files_arr = array();
            foreach($map as $row) {
                $files_arr[] = array(
                    'filedate' => date ("Y-m-d H:i:s.", filemtime($row)),
                    'filename' => $row,
                );
            }
            //rsort($files_arr);
            $i = 0;
            $len = count($files_arr);
            if ($files_arr) {
                foreach ($files_arr as $row_pic) {
                    if ($i == 0) {
                        // first
                        $profile_pic_filename_first = $row_pic['filename'];
                    } else if ($i == $len - 1) {
                        // last
                        $profile_pic_filename_last = $row_pic['filename'];
                    }
                    $i++;
                }
                $profile_pic_exist = true;
                if ($profile_pic_filename_first) {
                    $pic_filename = $profile_pic_filename_first;
                } else {
                    $pic_filename = $profile_pic_filename_last;
                }
                $pic = 'uploads/'.$dir.'/' . $id . '/' . basename($pic_filename);
            } else {
                if($types == 2) {
                    $pic = 'assets/global/img/not-available.png';
                }else {
                    $pic = ($gender == 1) ? 'assets/global/img/default_avatar_male.png' : 'assets/global/img/default_avatar_female.png';
                }
            }


        } else {

            if($types == 2) {
                $pic = 'assets/global/img/not-available.png';
            }else {
                if ($gender == 1) {
                    $pic = 'assets/global/img/default_avatar_male.png';
                } else if ($gender == 2) {
                    $pic = 'assets/global/img/default_avatar_female.png';
                } else {
                    $pic = 'assets/global/img/person_default.jpg';
                }
            }
        }

        return base_url() . $pic;
    }
}



if (!function_exists('create_person_data')) {
    function create_person_data($persondata = array()) {
        $ci = &get_instance();
        $data = array();
        $post = (count($persondata) > 0) ? $persondata :  $ci->input->post();
        $lastname = $post['lastname'];
        $firstname = $post['firstname'];
        $middlename = $post['middlename'];
        $gender = isset($post['gender']) ?? false;
        $birthdate = $post['birthdate'] ?? false;
        $marital = $post['marital'] ?? false;

        $prefix = $post['prefix'] ?? false;
        $suffix = $post['suffix'] ?? false;

        $addrspecific = $post['addrspecific'] ?? false;
        $district = $post['addrdistrict'] ?? false;
        $city = $post['addrcity'] ?? false;
        $country = $post['country'] ?? false;
        $phonenumber = $post['phone'] ?? false;
        $mobile = $post['mobile'] ?? false;
        $email = $post['email'] ?? false;

        // BEGIN TRANSACTION
        $ci->db->trans_begin();
        $person_id = null;

        $person_arr = array();

        // CHECK PERSON EXISTS
        $qry_person = $ci->db->query("
            SELECT * FROM person
            WHERE lastname = '$lastname'
            AND firstname = '$firstname'
            AND middlename LIKE '%$middlename%'
        ")->row();
        if($qry_person) {
            $person_id = $qry_person->sysid;
            $qry_select_complaints_data = $ci->db->select(
                '
                    dl.address,
                    dl.contact,
                    dl.district AS distid,
                    dl.barangays AS brgyid,
                    dl.landmarks AS landmarkid,
                    d.descriptions as dist
                '
                )
                ->from('ticketing_details_logs AS dl')
                ->join('address_districts as d', 'd.sysid = dl.district','left')
                ->where(array('dl.complainants' => $qry_person->sysid))
                ->get()->row();
            if($qry_select_complaints_data) {
                $addrspecific = $qry_select_complaints_data->address;
            }
        }else{
            // CREATE PERSON DATA MAIN
            if(trim($firstname) != '' && trim($lastname) != '') {

                $gender_ = ($gender) ? $gender : 1;

                $person_arr = array(
                    'firstname' => ucwords($firstname),
                    'lastname' => ucwords($lastname),
                    'gender' => $gender_,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                if ($birthdate) {
                    $person_arr['birthdate'] = ucwords($middlename);
                }
                if ($middlename && trim($middlename) != '') {
                    $person_arr['middlename'] = $birthdate;
                }
                $ci->db->insert('person', $person_arr);
                $person_id = $ci->db->insert_id();


                if ($addrspecific || $district || $city || $country) {
                    $addr_arr = array(
                        'personid' => $person_id,
                        'addrspec' => ucwords($addrspecific),
                        'addrdist' => NULL,
                        'addrcity' => $city,
                        'addrcountry' => $country,
                        'createdby' => user_id(),
                    );
                    $ci->db->insert('person_address_matrix', $addr_arr);
                    //$data['addr_insert_qry'] = $ci->db->last_query();
                }

                if ($marital) {
                    $marital_arr = array(
                        'personid' => $person_id,
                        'marital_status_id' => $marital,
                        'createdby' => user_id(),
                    );
                    $ci->db->insert('persons_marital_status_logs', $marital_arr);
                }

                if ($phonenumber) {
                    $contact_arr = array(
                        'personid' => $person_id,
                        'contactstring' => trim($phonenumber),
                        'types' => 1049,
                        'createdby' => user_id(),
                        'status' => 2,
                    );
                    $ci->db->insert('person_contact_matrix', $contact_arr);
                }

                if ($mobile) {
                    $contact_arr = array(
                        'personid' => $person_id,
                        'contactstring' => trim($phonenumber),
                        'types' => 1051,
                        'createdby' => user_id(),
                        'status' => 2,
                    );
                    $ci->db->insert('person_contact_matrix', $contact_arr);
                }

                if ($email) {
                    $contact_arr = array(
                        'personid' => $person_id,
                        'contactstring' => trim($phonenumber),
                        'types' => 1056,
                        'createdby' => user_id(),
                        'status' => 2,
                    );
                    $ci->db->insert('person_contact_matrix', $contact_arr);
                }

                if ($prefix) {
                    $title_arr = array(
                        'personid' => $person_id,
                        'titleid' => $prefix,
                    );
                    $ci->db->insert('person_title', $title_arr);
                }

                if ($suffix) {
                    $title_arr = array(
                        'personid' => $person_id,
                        'titleid' => $suffix,
                    );
                    $ci->db->insert('person_title', $title_arr);
                }
            }
        }

        if($ci->db->trans_status()===true) {
            $personid = $person_id;
            $ci->db->trans_commit();
        }else{
            $personid = false;
            $ci->db->trans_rollback();
        }
        $data['personaddress'] = $addrspecific;
        $data['personname'] = $lastname .', '. $firstname . ' ' . $middlename;
        $data['person_insert_val'] = $person_arr;
        $data['personid'] = $personid;
        return (object)$data;
    }
}


if (!function_exists('get_corporation_info')) {
    function get_corporation_info($id) {
        $ci = &get_instance();
        $q = $ci->db->select(
            'c.sysid, ' .
            'c.codes, ' .
            'c.descs, ' .
            'addr.addrspec, ' .
            'p.firstname AS repfname, ' .
            'p.lastname AS replname, ' .
            'p.middlename AS repmname '
        )
            ->from('corporation AS c')
            ->join('corporation_address_matrix AS addr', 'addr.corpid = c.sysid AND addr.status = 1', 'left')
            ->join('corporation_contact_matrix AS cont', 'cont.corpid = c.sysid AND cont.status = 1', 'left')
            ->join('corporation_representative AS rep', 'rep.corpid = c.sysid AND rep.status = 1', 'left')
            ->join('person AS p', 'p.sysid = rep.personid', 'left')
            ->where('c.sysid', $id)
            ->get()->row();
        if ($q) {
            $data['info'] = (object)array(
                'sysid' => $q->sysid,
                'codes' => $q->codes,
                'descs' => $q->descs,
                'addrspec' => $q->addrspec,
                'repfname' => $q->repfname,
                'replname' => $q->replname,
                'repmname' => $q->repmname,
            );
            $data['qry'] = true;
        }else{
            $data['qry'] = false;
        }
        return (object)$data;
    }
}

if (!function_exists('get_government_info')) {
    function get_government_info($id) {
        $ci = &get_instance();
        $q = $ci->db->select(
            'c.sysid, c.codes, c.descs, b.sysid as branchid, b.names, b.address'
        )
            ->from('government_main AS c')
            ->join('government_main_branches AS b', 'c.sysid = b.govid', 'left')
            ->where('c.sysid', $id)
            ->get()->row();
        if ($q) {
            $data['info'] = (object)array(
                'sysid' => $q->sysid,
                'codes' => $q->codes,
                'names' => $q->names,
                'branchid' => $q->branchid,
                'descs' => $q->descs,
                'address' => $q->address
            );
            $data['qry'] = true;
        }else{
            $data['qry'] = false;
        }
        return (object)$data;
    }
}

if (!function_exists('create_corporation_data')) {
    function create_corporation_data() {
        $ci = &get_instance();
        $data = array();

        $corpdesc = $ci->input->post('corpname');
        $corpbranch = $ci->input->post('corpbranch');
        $corpbranchaddr = $ci->input->post('addrspecific');
        $phone = $ci->input->post('phone');
        $mobile = $ci->input->post('mobile');
        $email = $ci->input->post('email');

        if($corpdesc) {
            // BEGIN TRANSACTION $corpdesc
            $ci->db->trans_begin();

            // CHECK EXISTING CORP
            $corp_exs = $ci->db->query('
            SELECT * FROM corporation
            WHERE descs = "'.$corpdesc.'" AND status = 1
        ')->row();

            if ($corp_exs) {
                $corp_id = $corp_exs->sysid;
                // GET BRANCH
                if ($corpbranch) {
                    $corp_bexs = $ci->db->select()
                        ->from('corporation_branches')
                        ->where(array('names' => $corpbranch, 'corpid' => $corp_id))
                        ->get()->row();
                    if ($corp_bexs) {
                        $corpb_id = $corp_bexs->sysid;
                    } else {
                        $corpb_ins = array(
                            'names' => $corpbranch,
                            'address' => $corpbranchaddr,
                            'corpid' => $corp_id
                        );
                        $ci->db->insert('corporation_branches', $corpb_ins);
                        $corpb_id = $ci->db->insert_id();
                    }
                } else {
                    $corpb_id = false;
                }
            } else {
                $corp_ins = array(
                    'codes' => get_acronym($corpdesc),
                    'descs' => $corpdesc,
                    'createdby' => user_id(),
                );
                $ci->db->insert('corporation', $corp_ins);

                $corp_id = $ci->db->insert_id();
                if ($corpbranch) {
                    $corpb_ins = array(
                        'names' => $corpbranch,
                        'address' => $corpbranchaddr,
                        'corpid' => $corp_id
                    );
                    $ci->db->insert('corporation_branches', $corpb_ins);
                    $corpb_id = $ci->db->insert_id();
                } else {
                    $corpb_id = false;
                }
            }

            $data = db_trans($ci->db);

            if ($data['qry'] == true) {
                $data['corpid'] = $corp_id;
                $data['corpbid'] = $corpb_id;
                $data['corp'] = $corpdesc;
                $data['corpinput'] = $ci->input->post();
            }
        }
        return (object)$data;
    }
}

if (!function_exists('create_government_data')) {
    function create_government_data() {
        $ci = &get_instance();
        $data = array();

        $corpdesc = $ci->input->post('corpname');
        $corpbranch = $ci->input->post('corpbranch');
        $corpbranchaddr = $ci->input->post('addrspecific');
        $phone = $ci->input->post('phone');
        $mobile = $ci->input->post('mobile');
        $email = $ci->input->post('email');

        if($corpdesc && $corpbranch) {
            // BEGIN TRANSACTION $corpdesc
            $ci->db->trans_begin();

            // CHECK EXISTING CORP
            $corp_exs = $ci->db->query('
            SELECT * FROM government_main
            WHERE descs = "'.$corpdesc.'" AND status = 1
        ')->row();

            if ($corp_exs) {
                $corp_id = $corp_exs->sysid;
                // GET BRANCH
                $corp_bexs = $ci->db->select()
                    ->from('government_main_branches')
                    ->where(array('names' => $corpbranch, 'govid' => $corp_id))
                    ->get()->row();
                if ($corp_bexs) {
                    $corpb_id = $corp_bexs->sysid;
                } else {
                    $corpb_ins = array(
                        'names' => $corpbranch,
                        'address' => $corpbranchaddr,
                        'govid' => $corp_id
                    );
                    $ci->db->insert('government_main_branches', $corpb_ins);
                    $corpb_id = $ci->db->insert_id();
                }
            } else {
                $corp_ins = array(
                    'codes' => get_acronym($corpdesc),
                    'descs' => $corpdesc,
                    'createdby' => user_id(),
                );
                $ci->db->insert('government_main', $corp_ins);
                $corp_id = $ci->db->insert_id();
                $corpb_ins = array(
                    'names' => $corpbranch,
                    'address' => $corpbranchaddr,
                    'govid' => $corp_id
                );
                $ci->db->insert('government_main_branches', $corpb_ins);
                $corpb_id = $ci->db->insert_id();
            }

            $data = db_trans($ci->db);

            if ($data['qry'] == true) {
                $data['govid'] = $corp_id;
                $data['govbid'] = $corpb_id;
                $data['gov'] = $corpdesc;
                $data['govinput'] = $ci->input->post();
            }
        }
        return (object)$data;
    }
}


if (!function_exists('get_users_default_pos')) {
    function get_users_default_pos ($id) {
        $ci  = & get_instance();
        $qry = $ci->db->select('RM.code, RM.descriptions')->from('prime_system_users_roles_matrix AS URM')
            ->join('prime_system_users_roles_main AS RM', 'URM.roleid = RM.sysid')
            ->where('URM.userid', $id)
            ->get()->row();
        if($qry) {
            $ret = array(
                'code' => $qry->code,
                'desc' => $qry->descriptions,
            );
        }else{
            $ret = array(
                'code' => 'USER',
                'desc' => 'Regular User',
            );
        }
        return (object)$ret;
    }
}

if (!function_exists('get_users_pic_url')) {

    function get_users_pic_url($id = NULL, $person = false, $user = true) {
        $ci = & get_instance();
        $profile_pic_filename_last = '';
        $profile_pic_filename_first = '';

        if($id != NULL) {

            if ($user) {
                if ($person == true) {
                    $q = $ci->db->select('p.sysid')
                        ->from('prime_system_users AS u')
                        ->join('person AS p', 'p.sysid = u.personid')
                        ->where('p.sysid', $id)
                        ->get()->row();
                } else {
                    if ($id == NULL) {
                        $userid = user_id();
                        $q = $ci->db->select('p.sysid')->from('prime_system_users AS u')->join('person AS p', 'p.sysid = u.personid')->where('u.sysid', $userid)->get()->row();
                    } else {
                        $q = $ci->db->select('p.sysid')->from('prime_system_users AS u')->join('person AS p', 'p.sysid = u.personid')->where('u.sysid', $id)->get()->row();
                    }
                }
            } else {
                $q = $ci->db->select("sysid")->from('person')->where('sysid', $id)->get()->row();
            }

            if ($q) {
                $person_id = $q->sysid;
                if (file_exists(FCPATH . 'uploads/person/' . $person_id)) {

                    $check_primary_file = glob(FCPATH . 'uploads/person/' . $person_id . '/primary.*');
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
                    } else {
                        $profile_pic_exist = false;
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
                    $pic = base_url('uploads/person/' . $person_id . '/' . basename($pic_filename));
                } else {
                    if (user_id() == $q->sysid || $person == false) {
                        $pic = base_url('assets/global/img/admin_pic.png');
                    } else {
                        $pic = ((isset($qry_user_logs)) ? $qry_user_logs->gender == 1 : 1) ? base_url('assets/global/img/default_avatar_male.png') : base_url('assets/global/img/default_avatar_female.png');
                    }
                }
            } else {
                if ($user == false) {
                    if (super_admin()) {
                        $pic = base_url('assets/global/img/admin_pic.png');
                    } else {
                        $pic = base_url('assets/global/img/person_default.jpg');
                    }
                } else {
                    $pic = base_url('assets/global/img/person_default.jpg');
                }
            }
        }else{
            if (super_admin()) {
                $pic = base_url('assets/global/img/admin_pic.png');
            } else {
                $userinfo = get_users_info(user_id());
                if(isset($userinfo->pid)) {
                    $user_pic_url = get_owner_pic($userinfo->pid, 'person');
                }
                $pic = (isset($userinfo->pid)) ? $user_pic_url : base_url('assets/global/img/admin_pic.png');
            }
        }

        return $pic;
    }

}

// CHECK NAVIGATION //
if (!function_exists('check_user_nav_access')) {

    function check_user_nav_access($navid) {
        $ci = & get_instance();
        $return = false;
        if(user_id()) {
            if (super_admin()) {
                $return = true;
            } else {
                $nav_ids_arr = get_users_info_navigation_ids();
                $qry_dashboard_arr = array();
                $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();
                $qry_dashboard = $ci->db->select()
                    ->from('prime_system_roles_dashboards')
                    ->where_in('roleid', $user_access_matrix_id_arr)
                    ->get();
                if ($qry_dashboard->num_rows() > 0) {
                    foreach ($qry_dashboard->result() as $drows) {
                        $qry_dashboard_arr[] = $drows->navids;
                    }
                }

                if ($nav_ids_arr) {
                    if (in_array($navid, $nav_ids_arr) && !in_array($navid, $qry_dashboard_arr)) {
                        return true;
                    } else {
                        $qry_nav = $ci->db->select()
                            ->from('prime_module_navigations_main')
                            ->where('parent', $navid)
                            ->where_in('sysid', $nav_ids_arr)
                            //->where_not_in('sysid', $qry_dashboard_arr)
                            ->get();
                        if ($qry_nav->num_rows() > 0) {
                            $return = true;
                        } else {
                            $return = false;
                        }
                    }
                } else {
                    $return = false;
                }
            }
        }
        return $return;
    }
}

if(!function_exists(('check_nav_parent'))) {
    function check_nav_parent($nav_curr_id)
    {
        $ci = &get_instance();

        $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();
        $nav_ids = array();
        if ($user_access_matrix_id_arr) {
            $qry = $ci->db->select()->from('prime_system_users_roles_matrix_access')
                ->where_in('roleid', $user_access_matrix_id_arr)->get();
            if ($qry->num_rows() > 0) {
                foreach ($qry->result() as $row) {
                    $nav_ids[] = $row->navid;
                }
            }
        }

        $qry = $ci->db->select('levels, sysid')
            ->from('prime_module_navigations_main')
            ->where('parent', $nav_curr_id)
            ->get();

        $qry_dashboard_arr = array();
        $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();
        $qry_dashboard = $ci->db->select()
            ->from('prime_system_roles_dashboards')
            ->where_in('roleid', $user_access_matrix_id_arr)
            ->get();
        if($qry_dashboard->num_rows()>0) {
            foreach ($qry_dashboard->result() as $drows) {
                $qry_dashboard_arr[] = $drows->navids;
            }
        }

        $res_num = 0;

        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                if (in_array($row->sysid, $nav_ids) && !in_array($row->sysid, $qry_dashboard_arr)) {
                    $res_num += 1;
                } else {
                    if (check_nav_parent($row->sysid) > 0) {
                        $res_num += 1;
                    }
                }
            }
        }
        return $res_num;
    }
}


if(!function_exists(('check_nav_parent_dashboards'))) {
    function check_nav_parent_dashboards($nav_curr_id)
    {
        $ci = &get_instance();

        $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();

        $nav_ids = array();
        if ($user_access_matrix_id_arr) {
            $qry = $ci->db->select()->from('prime_system_users_roles_matrix_access')
                ->where_in('roleid', $user_access_matrix_id_arr)->get();
            if ($qry->num_rows() > 0) {
                foreach ($qry->result() as $row) {
                    $nav_ids[] = $row->navid;
                }
            }
        }

        $qry_dashboard_arr = array();
        $qry_dashboard = $ci->db->select()
            ->from('prime_system_roles_dashboards')
            ->where_in('roleid', $user_access_matrix_id_arr)
            ->get();
        if($qry_dashboard->num_rows()>0) {
            foreach ($qry_dashboard->result() as $drows) {
                $qry_dashboard_arr[] = $drows->navids;
            }
        }

        $qry = $ci->db->select('levels, sysid')
            ->from('prime_module_navigations_main')
            ->where('parent', $nav_curr_id)
            ->get();

        $res_num = 0;
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                if (in_array($row->sysid, $nav_ids) && in_array($row->sysid, $qry_dashboard_arr)) {
                    $res_num += 1;
                } else {
                    if (check_nav_parent_dashboards($row->sysid) > 0) {
                        $res_num += 1;
                    }
                }
            }
        }
        return $res_num;
    }
}

if (!function_exists('get_users_list_control')) {

    function get_users_list_control($userid) {
        $ci = & get_instance();
        // CHECK IF USER IS SUPER ADMIN //
        $chk = $ci->db->select()
            ->from('prime_system_users')
            ->where(array('sysid' => $userid))
            ->get()->row();
        $html = '';
        if ($chk) {
            $html .= '<div class="btn-group">'.
                '<a href="javascript:;" id="deletebtn" data-id="'.$userid.'" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>'.
                '<a href="javascript:;" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>'.
                '<a href="'.base_url('profile').'/'.$userid.'" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>';
        } else {
            $html .= '<div class="btn-group">'.
                '<a href="javascript:;" id="deletebtn" data-id="'.$userid.'" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>'.
                '<a href="javascript:;" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>'.
                '<a href="'.base_url('profile').'/'.$userid.'" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>';
        }
        return $html;
    }

}

if (!function_exists('get_users_info_roles_control')) {

    function get_users_info_roles_control($userid , $roleid) {

        $ci = & get_instance();
        // CHECK IF USER IS SUPER ADMIN //
        $chk = $ci->db->select()->from('prime_system_users')->where(array('sysid' => $userid, 'type' => 1))->get()->row();
        $html = '';
        if ($chk) {
            $html .= '<code>N/A</code>';
        } else {
            $q = $ci->db->select('mx.sysid AS SID , mx.userid,mx.roleid, rm.code AS ROLENAME, rm.descriptions AS ROLEDESC, rm.color AS ROLECOLOR')
                ->from('prime_system_users_roles_matrix AS mx')
                ->join('prime_system_users_roles_main AS rm', 'mx.roleid = rm.sysid')
                ->where(array('mx.userid' => $userid, 'mx.status' => 1))
                ->get();

            $q_num = $q->num_rows();
            if ($q_num > 0) {
                $popover = "";
                $adduserrolesform = "";

                $adduserrolesform .= "<input id='selectuserrole' name='selectuserrole' type='text' class='form-control' placeholder='Select Role' />";
                $adduserrolesform .= "<button data-id='" . $q->row()->userid . "' style='margin-top: 10px;margin-bottom: 10px;' class='btn btn-primary btn-sm pull-right' id='saverolebtn'>Save</button>";


                $popover .= "<ul class='list-group roles-pop'>";
                foreach ($q->result() as $row) {
                    $popover .= "<li class='list-group-item' >";
                    $popover .= "<a data-id='" . $row->SID . "' href='" . base_url('settings/removerole') . "' class='btn  btn-xs pull-right '><i class='fa fa-times'></i></a>";
                    $popover .= "<span class='text-bold' style='color: " . $row->ROLECOLOR . "'>" . $row->ROLENAME . "</span>";
                    $popover .= "<em class='small-text'>" . $row->ROLEDESC . "</em>";


                    $popover .= "</li>";
                }

                $popover .= '</ul>';
                $html .= "<a class=\"btn btn-default btn-xs popovers\" data-trigger=\"click\" data-container=\"body\" data-placement=\"right\" data-content=\"" . $popover . "\" data-original-title=\"User Roles (" . $q->num_rows() . ")\"><span class=\"badge badge-success\">" . $q->num_rows() . "</span> User Roles <i class=\"fa fa-angle-down\"></i></a>";
                if($roleid){
                    $html .= "<button class='btn btn-primary btn-xs pull-right popovers' data-trigger='click' data-container='body' data-placement='right' data-content=\"" . $adduserrolesform . "\" data-original-title='Add User Roles'><i class='fa fa-plus'></i></button>";
                }
            }

            $loans_popovers = '
                    <form id=\'frm_transaction_loan_entry\' style=\'\' class=\'form-horizontal\' action=\''.base_url().'payroll/addpayrolltransactions\' method=\'post\'>
                        
                        <input value=\'0\' type=\'hidden\' name=\'trntype\' class=\'form-control input-md\' id=\'year\' />
                        <div class=\'form-body\'>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Loan Type</label>
                                <input required type=\'text\' name=\'type\' class=\'form-control input-md\' id=\'type\' style=\'width: 100% !important;\' />
                            </div>
                            <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Loan Amount</label>
                                <input required type=\'text\' name=\'amt\' class=\'form-control input-md\' id=\'amt\' placeholder=\'Amount this month\' style=\'width: 100%;\'/>
                            </div>
                        </div>
                        
                        <div class=\'form-actions bottom margin-top-20\'>
                            <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                            <button type=\'submit\' class=\'btn blue\'>Save</button>
                        </div>
                    </form>
                 ';

            $html .= '</div>';
        }
        return $html;
    }

}

if (!function_exists('get_users_info_roles')) {

    function get_users_info_roles($id = NULL) {
        $ci = & get_instance();
        if ($id == NULL) {
            $userid = $user_id = user_session()->system_user_sessid;
            $q = $ci->db->select('sysid, roleid, type')->from('prime_system_users_roles_matrix')->where(array('userid' => $userid, 'status' => 1))->get();
        } else {
            $q = $ci->db->select('sysid, roleid, type')->from('prime_system_users_roles_matrix')->where(array('userid' => $id, 'status' => 1))->get();
        }

        return ( $q->num_rows() > 0 ) ? $q->result() : false;
    }

}

if (!function_exists('get_user_info_main_role')) {

    function get_user_info_main_role($id = NULL) {
        $ci = & get_instance();
        if ($id == NULL) {
            $userid = $user_id = user_session()->system_user_sessid;
            $q = $ci->db->select('sysid, roleid, type')
                ->from('prime_system_users_roles_matrix')
                ->where(array('userid' => $userid, 'type' => 1))->get();
        } else {
            $q = $ci->db->select('sysid, roleid, type')
                ->from('prime_system_users_roles_matrix')
                ->where(array('userid' => $id, 'type' => 1))->get();
        }
        return ( $q->num_rows() > 0 ) ? $q->result() : false;
    }

}


if (!function_exists('get_users_info_navigation_ids')) {

    function get_users_info_navigation_ids() {
        $ci = & get_instance();
        $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();
        if($user_access_matrix_id_arr) {
            $qry = $ci->db->select()
                ->from('prime_system_users_roles_matrix_access')
                ->where_in('roleid', $user_access_matrix_id_arr)
                ->where('status',1)
                ->get();
            $arr = array();
            if ($qry->num_rows() > 0) {
                foreach ($qry->result() as $row) {
                    $arr[] = $row->navid;
                }
            } else {
                $arr = false;
            }
            return $arr;
        }else{
            return false;
        }
    }
}


if (!function_exists('get_users_info_navigation_matrix')) {

    function get_users_info_navigation_matrix() {
        $ci = & get_instance();
        $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();
        if($user_access_matrix_id_arr) {
            $qry = $ci->db->select()->from('prime_system_users_roles_matrix_access')->where_in('roleid', $user_access_matrix_id_arr)->get();
            return ($qry->num_rows() > 0) ? $qry->result() : false;
        }else{
            return false;
        }
    }

}

if (!function_exists('get_users_info_full')) {

    function get_users_info_full($id = NULL) {
        $ci = & get_instance();
        if ($id == NULL) {
            $userid = $user_id = user_session()->system_user_sessid;
        } else {
            $userid = $id;
        }
        $q = $ci->db->query("
            SELECT
                u.sysid,
                u.username,
                u.personid,
                person.firstname,
                person.lastname,
                person.middlename,
                person.gender,
                ulc.telcode
              FROM
                prime_system_users AS u
              LEFT JOIN prime_system_users_legacy_code AS ulc ON ulc.userid = u.sysid
              INNER JOIN person ON u.personid = person.sysid
              WHERE
                u.sysid = {$userid}
        ")->row();
        return ( $q ) ? $q : false;
    }

}
if (!function_exists('get_users')) {

    function get_users() {
        $ci = & get_instance();
        $q = $ci->db->select('sysid, firstname')->from('prime_system_users')->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data[] = array('id' => $row->sysid, 'text' => $row->firstname);
        }
        return json_encode($data);
    }

}



if (!function_exists('get_user_person')) {

    function get_user_person($userid) {
        $ci = & get_instance();
        $qry = $ci->db->select('p.sysid, p.firstname, p.lastname, p.middlename')
            ->from('prime_system_users AS u')
            ->join('person AS p', 'p.sysid = u.personid', 'left')
            ->where('u.sysid', $userid)
            ->get()->row();
        return ($qry) ? $qry : false;
    }

}

if (!function_exists('get_user_role')) {

    function get_user_role() {
        $ci = & get_instance();

        $term = $ci->input->post('term');
        $q = $ci->db->select('sysid, code, descriptions')
            ->from('prime_system_users_roles_main')
            ->like('code', $term)
            ->or_like('descriptions', $term)
            ->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data['list'][] = array('id' => $row->sysid, 'text' => $row->code . ' - ' . $row->descriptions);
        }
        return json_encode($data);
    }

}

if (!function_exists('get_user_roles_tag')) {
    function get_user_role_info($userid) {
        $ci = & get_instance();
        $qry = $ci->db->select('r.descriptions, r.color')
            ->from('prime_system_users_roles_matrix AS rm')
            ->join('prime_system_users_roles_main AS r', 'r.sysid = rm.roleid')
            ->where(array('rm.userid' => $userid, 'rm.status' => 1))
            ->group_by('r.descriptions, r.color')
            ->get();
        $html = '';
        if($qry->num_rows() > 0) {
            $i = 0;
            foreach($qry->result() as $row) {
                if($i<=3) {
                    $html .= '<span style="background: rgba(255,255,255, 0.3); font-size: 10px; color: ' . $row->color . '; padding: 2px 5px; margin-right: 2px; display: inline;">' . $row->descriptions . '</span>';
                }
                $i++;
            }
        }else{
            $html .='<code>No Rank</code>';
        }
        return $html;
    }
}

if (!function_exists('get_costcenter')) {

    function get_costcenter() {
        $ci = & get_instance();
        $q = $ci->db->select('sysid, codes, names, desc, address')->from('prime_costcenter_main')->get();
        $data = array();
        foreach ($q->result() as $row) {
            $data[] = array('id' => $row->sysid, 'text' => $row->codes);
        }
        return json_encode($data);
    }

}
if (!function_exists('get_costcenter_name')) {

    function get_costcenter_name($id, $full = false) {
        $ci = & get_instance();
        $q = $ci->db->select('codes, names')
            ->from('prime_costcenter_main')
            ->where('sysid',$id)
            ->get()->row();
        $name = 'N/A';
        if($q) {
            if($full==true) {
                $name = $q->codes . ' (' . $q->names . ') ';
            }else{
                $name = $q->codes;
            }
        }
        return $name;
    }

}

if (!function_exists('array_user_navigation')) {

    function array_user_navigation() {
        $ci = & get_instance();
        $user_login = $ci->session->userdata('logged_in');
        $a = $ci->model_admin->array_module_navigations($user_login['system_user_sessid']);
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($a));
        foreach ($it as $v) {
            $array_of_pages[] = $v;
        }
        return $array_of_pages;
    }

}

if (!function_exists('get_last_segment')) {

    function get_last_segment() {
        $ci = & get_instance();
        return end($ci->uri->segment_array());
    }

}

if (!function_exists('get_buttons')) {

    function get_buttons($id, $page, $target) {
        $ci = & get_instance();
        $html = '<span class="btn-group">';
        $html .= '<a target="' . $target . '" class="btn btn-info btn-xs view" href="' . base_url() . $ci->router->fetch_class() . '/' . $ci->router->fetch_method() . '/view/' . $id . '"><i class="fa fa-search"></i></a>';
        $html .= '<a target="' . $target . '" class="btn btn-warning btn-xs edit" href="' . base_url() . $ci->router->fetch_class() . '/' . $ci->router->fetch_method() . '/edit/' . $id . '"><i class="fa fa-pencil"></i></a>';
        $html .= '<a target="' . $target . '" class="btn btn-danger btn-xs stat" href="' . base_url() . $ci->router->fetch_class() . '/' . $ci->router->fetch_method() . '/stat/' . $id . '"><i class="fa fa-refresh"></i></a>';
        $html .= '</span>';
        return $html;
    }

}

if (!function_exists('create_breadcrumb_modules')) {

    function create_breadcrumb_modules($dataname = NULL) {
        $ci = & get_instance();
        $i = 1;
        $uri = $ci->uri->segment($i);
        $link = '<ul style="width: 98% !important;" class="page-breadcrumb">';
        $link .= '<li><a href="' . base_url() . '"><i class="fa fa-home"></i> Home</a></li>';

        if (!empty($dataname)) {
            $last_segment = count($ci->uri->segment_array());
            $link .= '<li><i class="fa fa-angle-right"></i>';
            $link .= '<a class="text-info" href="' . base_url('module/' . $ci->uri->segment(2) . '/' . $ci->uri->segment($last_segment)) . '">';
            $link .= '<b class="text-info">';
            $link .= ucfirst($dataname);
            $link .= '</b></a></li> ';
        } else {
            while ($uri != '') {
                $prep_link = '';
                for ($j = 1; $j <= $i; $j++) {
                    $prep_link .= $ci->uri->segment($j) . '/';
                }

                // GET MODULE NAME
                $qry_module = $ci->db->select('name, desc')->from('prime_module_navigations_main')->where('hashcode', $ci->uri->segment($i))->get()->row();

                if ($ci->uri->segment($i + 1) == '') {
                    $link .= '<li><i class="fa fa-angle-right"></i> <a href="' . base_url($prep_link) . '"><b class="text-info">';
                    $cname = ( $i == 2 ) ? '<b>' . $qry_module->name . '</b>' : $ci->uri->segment($i);
                    $link .= ucfirst($cname) . '</b></a></li> ';
                } else {
                    $link .= '<li><i class="fa fa-angle-right"></i> <a href="' . base_url($prep_link) . '">';
                    $cname = ( $i == 2 ) ? '<b>' . $qry_module->name . '</b>' : $ci->uri->segment($i);
                    $link .= ucfirst($cname) . '</a></li> ';
                }
                $i++;
                $uri = $ci->uri->segment($i);
            }
        }

        $link .= '</ul>';

        return $link;
    }

}

if (!function_exists('fixed_ip')) {

    function fixed_ip($ip)
    {
        $ip_addr = 'localhost';

        $validIP = explode(':', $ip);
        if(count($validIP) > 0) {
            if($validIP[0] != '' && $validIP[1] != '') {
                $ip_addr = $ip;
            }else{

                $val_ip_last = (isset($validIP[2])) ? $validIP[2] : $validIP[1];
                $ip_addr = '127:0:0' . $val_ip_last;

            }
        }else{
            $ip_addr = $ip;
        }
        return $ip_addr;
    }
}

if (!function_exists('create_breadcrumb')) {

    function create_breadcrumb($dataname = NULL) {
        $ci = & get_instance();
        $i = 1;
        $uri = $ci->uri->segment($i);
        $link = '<ul class="page-breadcrumb">';
        $link .= '<li><a href="' . base_url() . '"><i class="fa fa-home"></i> Home</a></li>';

        if (!empty($dataname)) {
            $last_segment = count($ci->uri->segment_array());
            $link .= '<li><i class="fa fa-angle-right"></i> <a href="' . base_url($ci->uri->segment(1).'/'.$ci->uri->segment($last_segment)) . '">';
            $link .= ucfirst($dataname) . '</a></li> ';
        } else {
            while ($uri != '') {
                $prep_link = '';
                for ($j = 1; $j <= $i; $j++) {
                    $prep_link .= $ci->uri->segment($j) . '/';
                }

                if ($ci->uri->segment($i + 1) == '') {
                    $link .= '<li><i class="fa fa-angle-right"></i> <a href="' . base_url($prep_link) . '"><b class="text-info">';
                    $link .= ucfirst($ci->uri->segment($i)) . '</b></a></li> ';
                } else {
                    $link .= '<li><i class="fa fa-angle-right"></i> <a href="' . base_url($prep_link) . '">';
                    $link .= ucfirst($ci->uri->segment($i)) . '</a></li> ';
                }
                $i++;
                $uri = $ci->uri->segment($i);
            }
        }
        $link .= '</ul>';
        return $link;
    }

}

if (!function_exists('init_user_device_ip')) {

    function init_user_device_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        $validIPnum = 3;
        $validIP = explode(':', $ipaddress);
        $count = 0;
        foreach ($validIP as $item) {
            if ($item >= 0 && $item != "") {
                $count++;
            }
        }

        //return fixed_ip($ipaddress);
        return $ipaddress;
    }

}

if (!function_exists('init_user_device_info')) {

    function init_user_device_info() {
        //$u_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $u_agent = '';
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE ([0-9]{1,}[\.0-9]{0,})/', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform
        );
    }

}

/*
if (!function_exists('date_formating')) {

    function date_formating($date, $format) {
        if ($date != "") {
            $new_date = date($format, strtotime($date));
        } else {
            $new_date = 'N/A';
        }
        return $new_date;
    }

}
*/


if (!function_exists('select_costcenter')) {

    function select_costcenter($id) {
        $ci = & get_instance();
        if ($id) {
            $query = $ci->db->select()->from('prime_costcenter_main')->where('sysid', $id)->get()->row();
            return $query->names;
        } else {
            $query = $ci->db->select()->from('prime_costcenter_main')->get();
            return ( $query ) ? $query->result() : false;
        }
    }

}


if (!function_exists('select_country')) {

    function select_country($id = false) {
        $ci = & get_instance();
        if ($id) {
            $query = $ci->db->select()->from('address_country')->where('sysid', $id)->get()->row();
            return $query->country;
        } else {
            $query = $ci->db->query("SELECT * FROM address_country");
            return ( $query ) ? $query->result() : false;
        }
    }

}



if (!function_exists('select_person_title')) {

    function select_person_title($types, $id = false) {
        $id = ($id) ? $id : false;
        $ci = & get_instance();
        if ($id) {
            $query = $ci->db->select()->from('person_title_main')->where('sysid', $id, 'types', $types)->get()->row();
            return $query->names;
        } else {
            $query = $ci->db->query("SELECT * FROM person_title_main WHERE types = $types");
            return ( $query ) ? $query->result() : false;
        }
    }

}


if (!function_exists('select_marital')) {

    function select_marital($id = false) {
        $ci = & get_instance();
        $data = array();
        if ($id == true) {
            $query = $ci->db->select()->from('marital')->where('sysid', $id)->get()->row();

            if($query) {
                $data['text'] = $query->names;
                $data['color'] = $query->color;
                return (object) $data;
            }

        }else {
            $query = $ci->db->query("SELECT * FROM marital");
            return ( $query ) ? $query->result() : false;
        }
    }

}


if (!function_exists('get_country')) {

    function get_country($ids, $addr) {
        $ci = & get_instance();
        switch ($addr) {
            case 'country':
                $query = $ci->db->select('country AS addrname')->where('sysid', $ids)->get('address_country')->row();
                break;
            case 'city':
                $query = $ci->db->select('names AS addrname')->where('sysid', $ids)->get('address_city')->row();
                break;
            case 'district':
                $query = $ci->db->select('names AS addrname')->where('sysid', $ids)->get('address_districts')->row();
                break;
            default:
                $query = false;
        }

        return ( $query ) ? $query : false;
    }

}

if (!function_exists('get_address_name')) {

    function get_address_name($ids, $addr) {
        $ci = & get_instance();
        switch ($addr) {
            case 'country':
                $query = $ci->db->select('country AS addrname')->where('sysid', $ids)->get('address_country')->row();
                break;
            case 'city':
                $query = $ci->db->select('names AS addrname')->where('sysid', $ids)->get('address_city')->row();
                break;
            case 'district':
                $query = $ci->db->select('names AS addrname')->where('sysid', $ids)->get('address_districts')->row();
                break;
            default:
                $query = false;
        }

        return ( $query ) ? $query : false;
    }

}


if (!function_exists('get_address_name_new')) {

    function get_address_name_new($ids, $addr) {
        $ci = & get_instance();
        switch ($addr) {
            case 'country':
                $query = $ci->db->select('country AS addrname')->where('sysid', $ids)->get('address_country')->row();
                break;
            case 'region':
                $query = $ci->db->select('descs AS addrname')->where('sysid', $ids)->get('address_region')->row();
                break;
            case 'province':
                $query = $ci->db->select('descs AS addrname')->where('sysid', $ids)->get('address_province')->row();
                break;
            case 'city':
                $query = $ci->db->select('descs AS addrname')->where('sysid', $ids)->get('address_citymun')->row();
                break;
            default:
                $query = false;
        }

        return ( $query ) ? $query->addrname : 'Unknown';
    }

}

if (!function_exists('select_city')) {

    function select_city($id = false) {
        $ci = & get_instance();
        if ($id) {
            $query = $ci->db->select()->from('address_city')->where('sysid', $id)->get()->row();
            return $query->names;
        }
        $query = $ci->db->query("SELECT * FROM address_city");
        return $query;
    }

}

if (!function_exists('select_district')) {

    function select_district() {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM address_districts");
        return $query;
    }

}
if (!function_exists('select_district_only')) {

    function select_district_only() {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM address_districts WHERE types = 1");
        return $query;
    }

}

if (!function_exists('select_district_only')) {

    function select_district_only() {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM address_districts WHERE types = 1");
        return $query;
    }
}

if (!function_exists('get_district_name')) {

    function get_district_name($id) {
        $ci = & get_instance();
        $query = $ci->db->select()->from('address_districts')->where('sysid', $id)->get()->row();
        return ( $query ) ? $query->names : false;
    }

}
if (!function_exists('get_brgy_name')) {

    function get_brgy_name($id) {
        $ci = & get_instance();
        $query = $ci->db->select()->from('address_barangay')->where('sysid', $id)->get()->row();
        return ( $query ) ? $query->texts : false;
    }

}

if (!function_exists('get_landmarks_multiple')) {

    function get_landmarks_multiple($ids) {
        $ci = & get_instance();
        $html = false;
        $ids_arr = explode(',', $ids);
        $query = $ci->db->select()->from('address_landmark')->where_in('sysid', $ids_arr)->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $html .= '<span class="text-danger">'.$row->texts.'</span>';
            }
        }
        return $html;
    }

}
if (!function_exists('get_city_name')) {

    function get_city_name($id) {
        $ci = & get_instance();
        $query = $ci->db->select()->from('address_city')->where('sysid', $id)->get()->row();
        return ( $query ) ? $query->names : false;
    }

}

if (!function_exists('get_marital_name')) {

    function get_marital_name($id) {
        $ci = & get_instance();
        $query = $ci->db->select()->from('marital')->where('sysid', $id)->get()->row();
        return ( $query ) ? $query->names : false;
    }

}



if (!function_exists('get_requirements_name')) {

    function get_requirements_name($ids) {
        $ci = & get_instance();
        $ids = explode(',', $ids);
        $ci->db->where_in('sysid', $ids);
        $qry = $ci->db->select('*')->from('prime_types_parameter')->where(array('codes' => 'SAPPS'))->get();
        return ( $qry->num_rows() > 0 ) ? $qry->result() : false;
    }

}

if (!function_exists('data_empty')) {

    function data_empty($str) {
        return ( $str ) ? '<span class="text-info">' . $str . '</span>' : '<code>N/A</code>';
    }

}

if (!function_exists('gender')) {

    function gender($str) {
        return ( $str == 1 ) ? '<span class="text-info"><i class="fa fa-male"></i> Male</span>' : '<span class="text-danger"><i class="fa fa-female"></i> Female</span>';
    }

}

if (!function_exists('gender_icon')) {

    function gender_icon($str) {
        return ( $str == 1 ) ? '<span class="text-info hidden-print"><i class="fa fa-male fa-fw"></i></span>' : '<span class="text-danger hidden-print"><i class="fa fa-female fa-fw"></i></span>';
    }

}

if (!function_exists('row_status')) {

    function row_status($str) {
        return ( $str == 1 ) ? '<span class="label label-success"><i class="fa fa-check"></i> Active</span>' : '<span class="label label-danger"><i class="fa fa-times"></i> In-Active</span>';
    }

}

if (!function_exists('trn_details')) {

    function trn_details($trnid) {
        $ci = & get_instance();
        $qry = $ci->db->select()->from('transaction_request_main_logs')->where('trnid', $trnid)->get()->row();
        return ( $qry ) ? $qry->arraydata : false;
    }

}


if (!function_exists('page_permission')) {

    function page_permission() {
        $ci = & get_instance();
        $ci->load->view('admin/pages/pagepermission');
    }

}

if (!function_exists('page_construction')) {
    function page_construction() {
        $ci = & get_instance();
        $ci->load->view('admin/pages/page404construction');
    }
}


if (!function_exists('page_data_notfound')) {
    function page_data_notfound($msg = false) {
        $ci = & get_instance();
        $data = array();
        $data['message'] = ($msg) ? $msg : false;
        $ci->load->view('frontend/common/page404datanotfound', $data);
    }
}


if (!function_exists('page_data_notfound_full')) {
    function page_data_notfound_full($msg = false) {
        $ci = & get_instance();
        $data = array();
        $data['message'] = ($msg) ? $msg : false;
        init_header_nonav($data);
        $ci->load->view('frontend/common/page404datanotfoundfull', $data);
    }
}


if (!function_exists('page_data_notfound_modal')) {
    function page_data_notfound_modal($msg = false) {
        $ci = & get_instance();
        $data = array();
        $data['message'] = ($msg) ? $msg : false;
        $ci->load->view('frontend/common/page404datanotfoundmodal', $data);
    }
}

if (!function_exists('page_file_notfound')) {

    function page_file_notfound($title, $msg) {
        $ci = & get_instance();
        $data = array();
        $data['msg'] = $msg;
        $data['title'] = $title;
        $ci->load->view('admin/pages/page404notfound', $data);
    }

}

if (!function_exists('page_session')) {

    function page_session($data, $addscript = null) {
        $ci = &get_instance();
        $ci->load->view('admin/common/head', $data);
        $ci->load->view('admin/pages/page404session', $data);

        $ci->load->view('admin/common/scripts', $data);
        if (!empty($addscript) || $addscript != false) {
            $ci->load->view('includes/scripts/' . $addscript);
        }
        $ci->load->view('admin/common/end');
    }

}

if (!function_exists('page_construction_full')) {

    function page_construction_full() {
        $ci = & get_instance();
        $ci->load->view('admin/pages/page404constructionfull');
    }

}

if (!function_exists('encrypt_pass')) {

    // ENCRYPT PASSWORD TO HASH
    function encrypt_pass($pass) {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

}



if (!function_exists('getequivalent')) {

    function getequivalent($amt, $array) {
        asort($array);
        $equiv = 0;
        foreach ($array as $sub_array) {
            $curr = $sub_array['val'];
            if ($curr > $amt) {
                return $equiv;
            } else if ($amt >= $curr) {
                $ret = $sub_array['amt'];
            }
            $equiv = $sub_array['amt'];
        }
        return $ret;
    }

}

if (!function_exists('btn_expand')) {

    function btn_expand($id) {
        return '<i data-toggle="collapse" data-target="#expand_' . $id . '" data-id="' . $id . '" id="btn-expand" class="fa bold fa-angle-right"></i> ';
    }

}

if (!function_exists('btn_comment')) {

    function btn_comment($id,$count = false) {
        $counter = ($count && $count > 0) ? '<span id="cnt" class="badge badge-primary bg-red-flamingo bold" style=" color: white !important; position: absolute !important; top: 10% !important; left: 45% !important; font-size: 8px !important; height: 13px !important;">'.$count.'</span>' : '';
        return '<a class="btn btn-primary inline" style="position: relative !important;" data-toggle="collapse" data-target="#expand_' . $id . '" data-id="' . $id . '" id="btn-comment" title="show/hide comments"><i class="fa bold fa-comment"></i>'.$counter.'</a>';
    }

}

if (!function_exists('tbl_input')) {

    function tbl_input($class, $val, $placeholder, $icon = false, $iconclass = NULL) {
        if ($icon == true) {
            return '
            <div class="input-icon left">
                <i class="fa ' . $iconclass . ' tooltips" data-original-title="Enter Reading Amount"></i>
                <input placeholder="' . $placeholder . '" class="form-control input-xs ' . $class . '" style="width: 100%;" id="" value="' . $val . '">
            </div> ';
        } else {
            return '<input placeholder="' . $placeholder . '" class="form-control ' . $class . '" value="' . $val . '" style="width: 100%;" />';
        }
    }

}


if (!function_exists('check_reading_submitted')) {

    function check_reading_submitted($acctid, $mtrno, $schedid) {
        $ci = & get_instance();
        $qry = $ci->db->select('reading')
            ->from('customer_accounts_subscription_meter_reading_logs')
            ->where(array(
                    'mtrid' => $mtrno,
                    'acctid' => $acctid,
                    'schedid' => $schedid,
                    'status' => 1
                )
            )
            ->get()->row();
        return ($qry) ? true : false;
    }

}

if (!function_exists('fn_meter_findings')) {

    function fn_meter_findings($sysid) {
        $ci = & get_instance();
        $qry = $ci->db->select('*')->from('meter_reading_findings')->get();
        $html = '';
        if ($qry->num_rows() > 0) {
            $html .= '<select class="form-control inline" style="width: 100%" id="findings">';
            $html .= '<option value=""></option>';
            foreach ($qry->result() as $row) {
                $html .= '<option value="' . $row->sysid . '">' . $row->codes . ' - ' . $row->descriptions . '</option>';
            }
            $html .= '</select>';
        } else {
            $html .= 'No Option';
        }
        return $html;
    }

}


if (!function_exists('draw_calendar')) {
    /* draws a calendar */

    function draw_calendar($month, $year) {
        $userid = user_id();
        $cur_day = date('d');
        $cur_month = date('m');
        $cur_year = date('Y');

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="table table-bordered table-condensed calendar">';

        /* table headings */
        $headings = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $calendar .= '<thead><th class="calendar-day-head">' . implode('</th><th class="calendar-day-head">', $headings) . '</th></thead>';

        /* days and weeks vars now ... */
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar .= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++):
            $calendar .= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
            if ($cur_day == $list_day && $cur_month == $month && $cur_year == $year) {
                $cur_day_bg = 'info';
            } else {
                $cur_day_bg = '';
            }
            $calendar .= '<td class="calendar-day ' . $cur_day_bg . '">';
            /* add in the day number */
            $calendar .= '<div class="day-number">' . $list_day . '</div>';

            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! * */
            $calendar .= str_repeat('<p> </p>', 2);

            $calendar .= '</td>';
            if ($running_day == 6):
                $calendar .= '</tr>';
                if (( $day_counter + 1 ) != $days_in_month):
                    $calendar .= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if ($days_in_this_week < 8):
            for ($x = 1; $x <= ( 8 - $days_in_this_week ); $x++):
                $calendar .= '<td class="calendar-day-np"> </td>';
            endfor;
        endif;

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /* all done, return result */
        return $calendar;
    }

}


if (!function_exists('time_elapsed_string')) {

    function time_elapsed_string($datetime, $full = false) {

        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $weeks = floor($diff->d / 7);
        $diff->d -= $weeks * 7;

        $units = array(
            'y' => array($diff->y, 'year'),
            'm' => array($diff->m, 'month'),
            'w' => array($weeks, 'week'),
            'd' => array($diff->d, 'day'),
            'h' => array($diff->h, 'hour'),
            'i' => array($diff->i, 'minute'),
            's' => array($diff->s, 'second'),
        );
        $string = array();
        foreach ($units as $k => $u) {
            $count = $u[0];
            $label = $u[1];
            if ($count) {
                $string[$k] = $count . ' ' . $label . ( $count > 1 ? 's' : '' );
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

}

if (!function_exists('get_time_unit')) {

    function get_time_unit($num) {
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'min',
            1 => 'sec'
        );
        foreach ($tokens as $unit => $text) {
            if ($num < $unit)
                continue;
            $numberOfUnits = floor($num / $unit);
            $modunit = $num % $unit;
            return $modunit . ' ' . $numberOfUnits . ' ' . $text . ( ( $numberOfUnits > 1 ) ? 's' : '' );
        }
    }

}

if (!function_exists('sum_time')) {

    function sum_time($timearr) {
        asort($timearr);
        $prev = $timearr[0]['date'];
        $diff = 0;
        foreach ($timearr as $datatime) {
            $data_date = $datatime['date'];
            $datadiff = strtotime($data_date) - strtotime($prev);
            $diff += $datadiff;
            $prev = $data_date;
        }
        $textval = "";
        while (1) {
            $term = explode(" ", $diff);
            $diff = get_time_unit($term[0]);
            $text = explode(" ", $diff);

            $textval .= $text[1] . ' ' . $text[2] . ' ';
            if (( in_array("secs", $text) ) || ( in_array("sec", $text) ) || $text[1] == 0)
                break;
        }
        return $textval;
    }

}

if (!function_exists('maxgdlbid')) {

    // Find maximum number of gdlbid in array
    function maxgdlbid($array, $max) {
        foreach ($array as $key => $val) {
            if ($val == $max)
                return $key;
        }
    }

}

if (!function_exists('get_gdlb_name')) {
    function get_gdlb_name($id) {
        $ci = &get_instance();
        $qry = $ci->db->select("CONCAT(cm.g, '-', d.codes, '-', cm.l, '-', cm.b) AS GDLB", false)
            ->from('gdlb_main AS cm')
            ->join('address_districts AS d', 'd.sysid = cm.d')
            ->where(array('cm.sysid' => $id))
            ->get()->row();
        return ($qry) ? $qry->GDLB : 'N/A';
    }
}

if (!function_exists('get_rateclass_name')) {
    function get_rateclass_name($id) {
        $ci = &get_instance();
        $qry = $ci->db->select("classifications AS name")
            ->from('prime_system_rate_class_main')
            ->where(array('sysid' => $id))
            ->get()->row();
        return ($qry) ? $qry->name : '';
    }
}

if (!function_exists('get_multcode_name')) {
    function get_multcode_name($id) {
        $ci = &get_instance();
        $qry = $ci->db->select("codes AS name")
            ->from('billing_rates_main_multiplier')
            ->where(array('sysid' => $id))
            ->get()->row();
        return ($qry) ? $qry->name : '';
    }
}

if (!function_exists('get_gdlb_list')) {
    function get_gdlb_list() {
        $ci = &get_instance();
        $qry = $ci->db->select("cm.sysid,CONCAT(cm.g, '-', d.codes, '-', cm.l, '-', cm.b) AS GDLB", false)
            ->from('gdlb_main AS cm')
            ->join('address_districts AS d', 'd.sysid = cm.d')
            ->get();
        return ($qry->num_rows()>0) ? $qry->result() : false;
    }
}

if (!function_exists('get_acct_gdlb')) {
    function get_acct_gdlb($id) {
        $ci = &get_instance();
        $qry_get_gdlb = $ci->db->select('a.sysid, d.names AS DISTNAME, d.sysid AS DIST, ad.addrspecific AS ADDR')
            ->select("CONCAT(cm.g, '-', d.codes, '-', cm.l, '-', cm.b) AS GDLB", false)
            ->from('customer_accounts_main AS a')
            ->join('customer_accounts_address AS ad', 'ad.acctid = a.sysid AND ad.status = 1', 'left')
            ->join('gdlb_main AS cm', 'a.gdlb = cm.sysid', 'left')
            ->join('address_districts AS d', 'd.sysid = cm.d', 'left')
            ->where(array('a.sysid' => $id))
            ->group_by('a.sysid, d.names, d.sysid, ad.addrspecific')
            ->get()->row();
        return ( $qry_get_gdlb ) ? $qry_get_gdlb : false;
    }
}

if (!function_exists('acct_gdlb')) {
    function acct_gdlb($dataid) {
        $ci = &get_instance();
        $qry_get_gdlb = $ci->db->select('cm.sysid, d.names AS DISTNAME, d.sysid AS DIST')
            ->select("CONCAT(cm.g, '-', d.codes, '-', cm.l, '-', cm.b) AS GDLB", false)
            ->from('trn_customer_accounts_glb AS cg')
            ->join('gdlb_main AS cm', 'cg.glbid = cm.sysid')
            ->join('address_districts AS d', 'd.sysid = cm.d')
            ->where(array('cg.accountid' => $dataid, 'cg.status' => 1))
            ->get()->row();
        return ( $qry_get_gdlb ) ? $qry_get_gdlb : false;
    }
}

if (!function_exists('acct_total_loads')) {

    function acct_total_loads($dataid) {
        $ci = &get_instance();
        $total_load = 0;
        $qry_load = $ci->db->select("watts, qty")->from('application_customers_equipments')->where(array('appid' => $dataid, 'status' => 1))->get();
        if ($qry_load->num_rows() > 0) {
            foreach ($qry_load->result() as $row) {
                $total_load += ( $row->watts * $row->qty );
            }
        }
        return $total_load;
    }

}

if (!function_exists('acct_total_desposit')) {

    function acct_total_desposit($dataid, $moduleid) {
        $ci = &get_instance();
        $total_deps = 0;
        $qry = $ci->db->select('sum(totalamt) AS AMT')
            ->from('transaction_payments_logs')
            ->where(array('payforacctno' => 162, 'dataid' => $dataid, 'moduleid' => $moduleid, 'status' => 1))
            ->get()->row();
        return ($qry) ? $qry->AMT : 0;
    }

}

if (!function_exists('get_types_name')) {

    function get_types_name($id) {
        $ci = &get_instance();
        $qry = $ci->db->select()->from('prime_types_parameter')->where('sysid', $id)->get()->row();
        return ($qry) ? $qry : false;
    }

}

if (!function_exists('get_types_label_format')) {
    function get_types_label_format($id, $txt = false, $icon = true, $tippos = 'top', $url = false, $labelfull = false, $arr = false) {
        $ci = &get_instance();

        $url = ($url) ? $url : 'javascript:;';

        $qry = $ci->db->select('tp.names, tp.desc, tp.colorbg, tp.colortxt, i.icon')
            ->from('prime_types_parameter AS tp')
            ->join('system_icons AS i', 'i.sysid = tp.icons', 'left')
            ->where('tp.sysid', $id)
            ->get()->row();

        $label_name = 'N/A';
        $label_colorbg = '#CCC';
        $label_colortxt = '#FFF';
        $label_icon = 'fa-question';
        $label_desc = '';
        if($qry) {
            $label_name = $qry->names;
            $label_colorbg = $qry->colorbg;
            $label_colortxt = $qry->colortxt;
            $label_desc = $qry->desc;
            if($qry->colortxt!='') {

                $label_icon = $qry->icon;
            }

            if($labelfull==true) {
                $label_name = $qry->names . ' - ' . $qry->desc;
            }

            if($txt) {
                $label_name = $txt;
            }
        }

        if($icon==true) {
            $label_icon = $label_icon;
        }else{
            $label_icon = '';
        }
        if($arr==true) {
            return (object) array('text' => $label_name, 'background' => $label_colorbg, 'color' => $label_colortxt, 'icon' => $label_icon);
        }else {
            return '<a href="' . $url . '" class="label tooltips" data-placement="' . $tippos . '" title="' . $label_desc . '" style="background: ' . $label_colorbg . '; color: ' . $label_colortxt . '"><i class="fa ' . $label_icon . '"></i> ' . $label_name . ' </a>';
        }
    }
}
function custom_days_num($dayname) {
    $day_arr = array(
        'SUN' => 1,
        'MON' => 2,
        'TUE' => 3,
        'WED' => 4,
        'THU' => 5,
        'FRI' => 6,
        'SAT' => 7
    );
    return $day_arr[$dayname];
}

if (!function_exists('get_ownership_details')) {
    function get_ownership_details($ownertype, $ownerid) {
        $ci = &get_instance();

        if ($ownertype == 1) {
            $selectperson = $ci->db->select('p.firstname, p.lastname, middlename, p.birthdate, p.gender')
                ->select("CONCAT(p.lastname, ', ', p.firstname) AS name", false)
                ->from('person as p')
                ->where('p.sysid', $ownerid)->get()->row();
            return ($selectperson) ? $selectperson : false;
        }

        if ($ownertype == 2) {
            $selectcorp = $ci->db->select('c.descs as name')
                ->from('corporation as c')
                ->where('c.sysid', $ownerid)->get()->row();
            return ($selectcorp) ? $selectcorp : false;
        }

        if ($ownertype == 5) {
            $legacy = $ci->db->select('l.name as name')
                ->from('customer_accounts_name_legacy as l')
                ->where('l.sysid', $ownerid)->get()->row();
            return ($legacy) ? $legacy : false;
        }
    }

}

if (!function_exists('months_short')) {

    function months_short($i) {
        $month_arr = array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC');
        return $month_arr[$i - 1];
    }

}

if (!function_exists('get_name_rates')) {

    function get_name_rates($id) {
        $ci = &get_instance();
        $qry = $ci->db->select()->from('billing_rates_main')->where('sysid', $id)->get()->row();
        return ($qry) ? $qry->names : false;
    }

}

if (!function_exists('get_rate_class_select')) {

    function get_rate_class_select($id = false) {
        $ci = &get_instance();
        $data = array();
        if($id) {
            $qry = $ci->db->query("SELECT sysid, codes, classifications FROM prime_system_rate_class_main WHERE sysid = $id")->row();
            $data = ($qry) ? $qry->classifications : '';
        }else{
            $qry = $ci->db->query("SELECT sysid, codes, classifications FROM prime_system_rate_class_main WHERE status = 1");
            if ($qry->num_rows() > 0) {
                foreach ($qry->result() as $row) {
                    $data['list'][] = array('id' => $row->sysid, 'text' => $row->classifications);
                }
            }
        }
        return json_encode($data);
    }

}

if (!function_exists('get_dist_list_select')) {
    function get_dist_list_select() {
        $ci = &get_instance();
        $data = array();
        $qry = $ci->db->query("SELECT sysid, codes, names FROM address_districts WHERE types = 1");
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->codes . ' - ' . $row->names);
            }
        }
        return json_encode($data);
    }
}

if (!function_exists('get_reading_analysis_stats')) {
    function get_reading_analysis_stats($prvread, $prsread, $prevcon) {

        $incdec_percent = 60;
        $kwh = 0;
        $curcon = '';

        if($prsread>0 && $prvread>0) {
            $curcon = bcsub($prsread, $prvread);
        }

        if($curcon!='') {
            $con_diff = bcsub($curcon, $prevcon, 2);
            if($con_diff>0 && $prevcon>0) {
                $con_diff_div = bcdiv($con_diff, $prevcon, 2);
                $con_diff_per = bcmul($con_diff_div, 100, 2);
                if($con_diff_per>100) {
                    $con_diff_per = 100;
                }
            }else{
                $con_diff_per = 100.00;
            }
        }else{
            $con_diff = 0;
            $con_diff_per = '';
        }

        if( $con_diff > 0 ) {
            $bg_color = 'rgba(65,177,38,0.50)';
        } else {
            $bg_color = 'rgba(252,124,127,0.50)';
        }

        $con_diff_abs = abs($con_diff_per);

        if($prsread==0) {
            $checked = '';
        }else{
            if($con_diff_abs>$incdec_percent) {
                $checked = '';
            }else {
                $checked = 'checked';
            }
        }


        if($con_diff_per!='') {
            $con_diff_icon = ($con_diff>0) ? '<i class="fa fa-angle-double-up text-success fa-fw pull-left"></i>' : '<i class="fa fa-angle-double-down text-danger fa-fw pull-left"></i>';
            $con_diff_html = '<div style="position: absolute; background-color: ' . $bg_color . '; width: 100%; height: 100%;
                                        background: ' . $bg_color . '; /* For browsers that do not support gradients */
                                        background: -webkit-linear-gradient(left, ' . $bg_color . ' ' . abs($con_diff_per) . '%, rgba(255,255,255,0.05)); /* For Safari 5.1 to 6.0 */
                                        background: -o-linear-gradient(left, ' . $bg_color . ' ' . abs($con_diff_per) . '%, rgba(255,255,255,0.05)); /* For Opera 11.1 to 12.0 */
                                        background: -moz-linear-gradient(left, ' . $bg_color . ' ' . abs($con_diff_per) . '%, rgba(255,255,255,0.05)); /* For Firefox 3.6 to 15 */
                                        background: linear-gradient(left, ' . $bg_color . ' ' . abs($con_diff_per) . '%, rgba(255,255,255,0.05));
                                     "></div>' . $con_diff_icon . '<span class="pull-right">' . number_format($con_diff_per, 2) . '%</span>';
        }else{
            $con_diff_html = '';
        }

        $arr['checked'] = $checked;
        $arr['abs'] = $con_diff_abs;
        $arr['html'] = $con_diff_html;
        $arr['curcon'] = $curcon;
        return (object)$arr;
    }
}

if (!function_exists('get_spec_rates')) {

    function get_spec_rates($year = false, $month = false, $class = false, $rateid = false, $unitid = false) {
        $ci = &get_instance();
        $data = array();
        if ($year == false && $month == false) {
            $year = date('Y');
            $month = date('m');
        }
        if ($class && $rateid && $unitid) {

            $qry_rates_list = $ci->db->select('rm.sysid RMID, tbr.classid AS CLASSID, tbr.rates AS RATES,  rm.names AS RATENAME, tp.sysid AS UNITID')
                ->from('trn_billing_rates AS tbr')
                ->join('billing_rates_main AS rm', 'rm.sysid = tbr.brateid')
                ->join('prime_types_parameter AS tp', 'tp.sysid = tbr.units')
                ->where(array('year' => $year, 'month' => $month, 'tbr.classid' => $class, 'tbr.brateid' => $rateid, 'tbr.status' => 2, 'tp.sysid' => $unitid))
                ->group_by('rm.sysid, tp.sysid, tbr.classid, rm.names, tp.sysid, tbr.rates')
                ->get()->row();
            if ($qry_rates_list) {
                return (object) $qry_rates_list;
            } else {
                return false;
            }
        } else {
            $qry_rates_list = $ci->db->select()
                ->from('trn_billing_rates')
                ->where(array('year' => $year, 'month' => $month))
                ->get()->row();
            return ($qry_rates_list) ? true : false;
        }
    }

}
if (!function_exists('floorp')) {

    function floorp($val, $precision) {
        $half = 0.01 / pow(10, $precision);
        return round($val + $half, $precision);
    }

}

if (!function_exists('get_acctinfo_mtr')) {

    function get_acctinfo_mtr($mtrid) {
        $ci = &get_instance();
        $qry_acct_sub_mtr = $ci->db->select('moh.ownerid, asm.mtr, asm.mtrno, a.serialcodes')
            ->from('customer_accounts_subscription_meter AS asm')
            ->join('assets_main_owner_history AS moh', 'moh.assetid = asm.assetid')
            ->join('assets_main AS a', 'a.sysid = moh.assetid')
            ->where(array('asm.sysid' => $mtrid, 'moh.status' => 1))
            ->get()->row();
        return ($qry_acct_sub_mtr) ? $qry_acct_sub_mtr : false;
    }

}

if (!function_exists('get_acctinfo_nearmeter')) {

    function get_acctinfo_nearmeter($dataid) {
        $ci = &get_instance();
        $data = array();
        $gdlb = '';
        $gdlb_active = false;
        $gdlb_assigned = false;
        $gdlb_servno = '';
        $qry = $ci->db->select('a.sysid, a.mtrno, a.ownerid, a.servicenumber')
            ->from('customer_accounts_main AS a')
            ->join('application_customers_near_meters AS anm', 'anm.acctid = a.sysid')
            ->where(array('anm.appid' => $dataid, 'anm.status' => 1))
            ->get();
        if ($qry->num_rows()) {
            $q = true;
            foreach ($qry->result() as $row) {
                $data['options'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->mtrno
                );

                $data['mtrlist'][] = array(
                    'mrtno' => $row->mtrno,
                    'ownerid' => $row->ownerid,
                    'acctid' => $row->sysid,
                    'srvno' => $row->servicenumber
                );
            }
            $qry_gdlb_details = $ci->db->select('cg.servno, cg.gdlbid, g.g, ad.codes AS d, g.l, g.b')
                ->from('application_customers_details AS cg')
                ->join('gdlb_main AS g', 'g.sysid = cg.gdlbid')
                ->join('address_districts AS ad', 'ad.sysid = g.d')
                ->where(array('cg.sysid' => $dataid))
                ->get()->row();
            $gdlb = ($qry_gdlb_details) ? '<li class="list-group-item"><b>GDLB: </b><span class="pull-right">' . $qry_gdlb_details->g . '-' . $qry_gdlb_details->d . '-' . $qry_gdlb_details->l . '-' . $qry_gdlb_details->b . "</span></li>" : 'Unknown';
            $gdlb_active = ($qry_gdlb_details && $qry_gdlb_details->gdlbid > 0) ? true : false;
            $gdlb_assigned = ($qry_gdlb_details && $qry_gdlb_details->gdlbid == 0) ? true : false;
            $gdlb_servno = ($qry_gdlb_details) ? $qry_gdlb_details->servno : '';
        } else {
            $q = false;
        }
        $data['servno'] = $gdlb_servno;
        $data['gdlbactive'] = $gdlb_active;
        $data['gdlbassigned'] = $gdlb_assigned;
        $data['gdlb'] = $gdlb;
        $data['qry'] = $q;
        return (object) $data;
    }

}


if (!function_exists('get_reading_kwh')) {

    function get_reading_kwh($prsrdg, $prvrdg) {
        $ci = &get_instance();
        $qry_cur = $ci->db->select('readings')->from('trn_reading_history')->where('sysid', $prsrdg)->get()->row();
        $qry_prv = $ci->db->select('readings')->from('trn_reading_history')->where('sysid', $prvrdg)->get()->row();
        $curr = ($qry_cur) ? $qry_cur->readings : 0;
        $prev = ($qry_prv) ? $qry_prv->readings : 0;
        $kwh = $curr - $prev;
        return $kwh;
    }

}

if (!function_exists('draw_report_signatory')) {
    function draw_report_signatory($typesid, $col, $margin)
    {
        $ci = &get_instance();
        $html = '';

        $cols_width_a   =   (100 / $col);
        $margin_per     =   (($margin * 100) / $col);
        $cols_width_b   =   ($cols_width_a - ($margin *100));

        $qry_signatury = $ci->db->query("
                SELECT
                s.moduleid,
                s.codes,
                si.empid,
                si.title,
                si.pos
                FROM
                employee_signatory AS s
                INNER JOIN employee_signatory_items AS si ON s.sysid = si.sigid AND si.`status` = 1
                WHERE
                s.codes = $typesid AND
                s.`status` = 1
            ");


        $html .= '<div class="row" style="margin-top: 50px;">';
        if($qry_signatury->num_rows()>0) {
            foreach($qry_signatury->result() as $row) {
                $info = get_employee_info($row->empid);
                $name = '';
                $post = '';
                if ($info){
                    $name = strtoupper($info->firstname.' '.$info->middlename.' '.$info->lastname);
                    $post = strtoupper($info->positiondesc);
                }
                $html .= '<div style="display: inline-block; width: '.$cols_width_b.'%; margin-left: '.$margin_per.'%;">';
                $html .= '<div style="text-align:center; width: 100%; border-bottom: 1px solid #000;">'.$name.'</div>';
                $html .= '<div style="text-align:center; width: 100%;">'.$post.'</div>';
                $html .= '</div>';
            }
        }

        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('get_account_prevbilling')) {
    function get_account_prevbilling($acctid, $limit = false) {
        $ci = &get_instance();
        $data = array();
        $q = false;
        $res = array();
        $total_kwh = 0;
        $av_kwh = 0;

        if($limit) {
            $limit_qry = ' LIMIT ' . $limit;
        }else{
            $limit_qry = ' LIMIT 12 ';
        }

        $qry = $ci->db->query("
            SELECT month, year, prsrdg, prvrdg, prsdte, prvdte, kwhuse, batch 
            FROM billing_reports_main 
            WHERE acctid = $acctid AND batch != 'LATEBILL' AND kwhuse > 0
            ORDER BY prsdte DESC
            $limit_qry
        ");

        $qry_average_kwh_num = $qry->num_rows();
        if($qry_average_kwh_num > 0) {
            $res = $qry->result();
            foreach($res as $row) {
                $total_kwh += $row->kwhuse;
            }
            $q = true;
        }
        if($qry_average_kwh_num > 0) {
            $av_kwh = $total_kwh / $qry_average_kwh_num;
        }
        $data['qry'] = $q;
        $data['num'] = $qry_average_kwh_num;
        $data['res'] = $res;
        $data['ave'] = $av_kwh;
        return (object)$data;
    }
}

if (!function_exists('get_active_account_info')) {
    function get_active_account_info($id, $servno = false, $mtr = false) {
        $ci = &get_instance();
        $info = array();

        if($servno) {
            $ci->db->where(array('m.servicenumber' => $id, 'm.mtr' => $mtr));
        }else{
            $ci->db->where('m.sysid', $id);
        }

        $qry = $ci->db->select('
                        m.servicenumber,
                        m.createdby,
                        m.datecontract,
                        m.dateconnected,
                        m.ownerid,
                        m.types,
                        m.gdlb,
                        m.mtrno,
                        m.mtrserial,
                        m.mtr,
                        m.rateclassid,
                        m.multid,
                        m.netmtr,
                        m.status,
                        mm.rate AS multiplier,
                        mm.codes AS multcode,
                        cls.codes AS classcode,
                        tp.sysid AS discode,
                        tp.names AS discounts,
                        ad.datecreated AS discdate,
                        caa.addrspecific AS addrspec
            ')->from('customer_accounts_main AS m')
            ->join('customer_accounts_address AS caa', 'caa.acctid = m.sysid AND caa.status = 1', 'left')
            ->join('billing_rates_main_multiplier AS mm', 'mm.sysid = m.multid', 'left')
            ->join('rate_class_specification AS cls', 'cls.sysid = m.rateclassid', 'left')
            ->join('customer_accounts_discounts AS ad', 'ad.acctid = .m.sysid AND ad.status = 1', 'left')
            ->join('prime_types_parameter AS tp', 'tp.sysid = ad.disctype', 'left')
            ->get()->row();

        if($qry) {
            $acctname = '';

            if($qry->types==5) {
                $qry_legacy = $ci->db->select('name')->from('customer_accounts_name_legacy')
                    ->where(array('sysid' => $qry->ownerid))
                    ->get()->row();
                $acctname = ($qry_legacy) ? $qry_legacy->name : 'N/A';
            }else{
                $qry_person = $ci->db->select('firstname, lastname')->from('person')
                    ->where(array('sysid' => $qry->ownerid))
                    ->get()->row();
                $acctname = ($qry_person) ? $qry_person->firstname.' '.$qry_person->lastname : 'N/A';
            }


            $info = array(
                'servicenumber' => $qry->servicenumber,
                'address' => $qry->addrspec,
                'name' => $acctname,
                'createdby' => $qry->createdby,
                'datecontract' => $qry->datecontract,
                'dateconnected' => $qry->dateconnected,
                'ownerid' => $qry->ownerid,
                'types' => $qry->types,
                'gdlb' => $qry->gdlb,
                'mtrno' => $qry->mtrno,
                'mtrserial' => $qry->mtrserial,
                'mtr' => $qry->mtr,
                'rateclassid' => $qry->rateclassid,
                'multid' => $qry->multid,
                'multcode' => $qry->multcode,
                'netmtr' => $qry->netmtr,
                'status' => $qry->status,
                'multiplier' => $qry->multiplier,
                'classcode' => $qry->classcode,
                'discode' => $qry->discode,
                'discounts' => $qry->discounts,
                'discdate' => $qry->discdate
            );
        }

        return ($info) ? (object)$info : false;
    }
}

if (!function_exists('compute_bill')) {
    function compute_bill($acctid, $year, $month, $kwh = 0, $dkwh = 0, $netkwh = 0) {
        $ci = &get_instance();

        $data = array();
        $arr_charges_grp = array();
        $qry = false;
        $msg = '';


        $ownerinfo = get_active_account_info($acctid);
        if($ownerinfo) {
            $rateid = ($ownerinfo) ? $ownerinfo->rateclassid : 0;
            $check_rates = get_spec_rates($year, $month);
            if($check_rates) {
                $genchrg = get_spec_rates($year, $month, $rateid, 1, 100);
                $genchrg = ($genchrg) ? $genchrg->RATES : 0;
                $genamt = ($kwh * $genchrg);

                // ############################################################
                // GET RATE GROUP AND CHARGES
                $qry_charges_grp = $ci->db->select()->from('trn_billing_rates_group_charges')->get();
                if($qry_charges_grp->num_rows() > 0) {
                    $qry = true;
                    foreach($qry_charges_grp->result() as $charges_main) {
                        $charges_list_arr = array();
                        $qry_list_charges = $ci->db->select()->from('trn_billing_rates_group_list')->where(array('groupid' => $charges_main->sysid, 'parentid' => 0))->get();
                        if ($qry_list_charges->num_rows() > 0) {
                            foreach ($qry_list_charges->result() as $rrow) {
                                // GET IF HAVING SUB
                                $qry_list_subs = $ci->db->select()->from('trn_billing_rates_group_list')->where(array('groupid' => $charges_main->sysid, 'parentid' => $rrow->sysid, 'status' => 1))->get();
                                if ($qry_list_subs->num_rows() > 0) {
                                    $arr_list_subs = array();
                                    $amt_total_subs = 0;
                                    foreach ($qry_list_subs->result() as $rsrow) {
                                        // GET PARENT NAME
                                        $qry_list_parent = $ci->db->select()->from('trn_billing_rates_group_list')->where('sysid', $rsrow->parentid)->get()->row();
                                        if ($rsrow->groupinc != '') {

                                        }
                                    }
                                    $charges_list_arr[] = $rrow;
                                } else {
                                    $charges_list_arr[] = $rrow;
                                }
                            }
                        }
                        $data['charges'][] = array(
                            'header' => $charges_main->descs,
                            'chargeslists' => $charges_list_arr
                        );
                    }
                }else{
                    $msg = 'Billing Group Maintenance (Header)';
                }
            }else{
                $msg = 'Rates for this month ('.$month.'-'.$year.') is not available!';
            }
        }else{
            $msg = 'Account not found!';
        }


        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return (object) $data;
    }
}
if (!function_exists('update_ar')) {
    function update_ar($acctid, $month, $curr, $kwh, $billno) {
        $ci = &get_instance();
        $col_num = str_pad($month, 2, "0", STR_PAD_LEFT);
        $upd_arr = array(
            'amt_'.$col_num => $curr,
            'kwh_'.$col_num => $kwh,
            'billno_'.$col_num => $billno,
        );
        $ci->db->where('acctid', $acctid);
        return $ci->db->update('customer_accounts_ar', $upd_arr);
    }
}
if (!function_exists('compute_billing')) {

    function compute_billing($acctid, $year, $month, $kwh, $dkwh = 0, $netmtrkwh = 0)
    {
        $ci = &get_instance();
        //$ownerinfo = $ci->model_query->get_active_owner($acctid);
        $ownerinfo = get_active_account_info($acctid);


        // ##############################################
        // ###### VARIABLES #############################
        // ##############################################
        $genamt                 =   0;
        $genchrg                =   0;
        $rate_exempt_cnt        =   0;
        $vat_amt_total          =   0;
        $footnote               =   '';
        $scdisc                 =   0;
        $disname                =   '';
        $rate_disc_amt          =   0;
        $multcode = '';

        if ($ownerinfo) {
            $data = array();
            $arr_charges_grp = array();
            $servno = ($ownerinfo) ? $ownerinfo->servicenumber : 0;
            $rateid = ($ownerinfo) ? $ownerinfo->rateclassid : 0;
            $gdlbid = ($ownerinfo) ? $ownerinfo->gdlb : 0;
            $gdlb = ($ownerinfo) ? get_gdlb_name($ownerinfo->gdlb) : 0;
            $name = ($ownerinfo) ? 'UNDER MAINTENANCE' : '';
            $addr = ($ownerinfo) ? 'UNDER MAINTENANCE' : '';
            $multcode = $ownerinfo->multcode;

            $multiplier = $ownerinfo->multiplier;
            $netmtr = $ownerinfo->netmtr;
            $kwh = $kwh * $multiplier;
            //$dkwh = $dkwh * $multiplier;

            // GET BILLING RATE GROUP
            $rategroup_id = $ci->db->select('rateid')->from('rate_class_group')
                ->where(array('classid' => $rateid, 'status' => 1))
                ->group_by('rateid')
                ->get()->row();

            if ($rategroup_id) {
                $rate = $rategroup_id->rateid;
                $qry_rate_code = $ci->db->select('codes')
                    ->from('rate_class_specification')
                    ->where(array('sysid' => $rateid, 'status' => 1))->get()->row();

                $qry_rate_main_code = $ci->db->select('classifications')
                    ->from('prime_system_rate_class_main')
                    ->where('sysid', $rate)->get()->row();

                if($qry_rate_main_code){
                    $billrate = $qry_rate_main_code->classifications;
                }else{
                    $billrate = '';
                }

                if($qry_rate_code) {
                    $ratecode = $qry_rate_code->codes;
                }else{
                    $ratecode = '';
                }

                $check_rates = get_spec_rates($year, $month, $rate);
                $qry = false;
                if ($check_rates) {
                    $qry = true;
                    // GET SUB METER KWH HR
                    $sub_kwh = get_submeter_total($acctid, $year, $month);
                    if($sub_kwh->sub) {
                        $kwh = $kwh - $sub_kwh->totalkwh;
                    }

                    // ############################################################
                    // GET RATE GENERATION CHARGE
                    $genchrg = get_spec_rates($year, $month, $rate, 1, 100);
                    $genchrg = ($genchrg) ? $genchrg->RATES : 0;
                    $genamt = ($kwh * $genchrg);

                    // ############################################################
                    // GET RATE GROUP AND CHARGES
                    $qry_charges_grp = $ci->db->select()
                        ->from('trn_billing_rates_group_charges')
                        ->get();
                    $html = '';
                    $demand = false;
                    $vat_amt_arr = array();

                    if ($qry_charges_grp->num_rows()) {

                        // #### LIST GROUP CHARGES #####
                        foreach ($qry_charges_grp->result() as $row) {
                            $arr_list_charges = array();
                            $qry_list_charges = $ci->db->select()
                                ->from('trn_billing_rates_group_list')
                                ->where(array('groupid' => $row->sysid, 'parentid' => 0, 'status' => 1))->get();
                            if ($qry_list_charges->num_rows() > 0) {
                                foreach ($qry_list_charges->result() as $rrow) {
                                    // CHECK LIST IF HAVING SUBS
                                    $qry_list_subs = $ci->db->select()
                                        ->from('trn_billing_rates_group_list')
                                        ->where(array('groupid' => $row->sysid, 'parentid' => $rrow->sysid, 'status' => 1))->get();
                                    if ($qry_list_subs->num_rows() > 0) {
                                        $arr_list_subs = array();
                                        $amt_total_subs = 0;
                                        $amt_total_subs_lg = 0;

                                        foreach ($qry_list_subs->result() as $rsrow) {

                                            // GET PARENT NAME
                                            $qry_list_parent = $ci->db->select()->from('trn_billing_rates_group_list')->where('sysid', $rsrow->parentid)->get()->row();
                                            if ($rsrow->groupinc != '') {
                                                $group_inc_list = $ci->db->select()->from('trn_billing_rates_group_list')->where('parentid', $rsrow->groupinc)->get();
                                                $incgroup_arr = array();
                                                $incgroup_arr_rate = array();
                                                if ($group_inc_list->num_rows() > 0) {
                                                    foreach ($group_inc_list->result() as $glrow) {

                                                        if ($glrow->units == 102) {
                                                            // METERING CHARGES ***
                                                            $rates = get_spec_rates($year, $month, $rate, $glrow->rateid, 102);
                                                            $rates = ($rates) ? $rates->RATES : 0;
                                                            $amt = round($rates, 2);
                                                            $rates = customize_rates_visibility($glrow->rateid, $rates);
                                                            $incgroup_arr[] = array('ratename' => get_name_rates($glrow->rateid), 'rate' => $rates, 'amt' => $amt);
                                                        } else {
                                                            if ($rate == 3 && $glrow->rateid == 4) {
                                                                // IF GDLB = 5-S-7-1 (gdlb_main SYSID: 434)
                                                                if ($gdlbid == 434) {
                                                                    $rates = get_spec_rates($year, $month, $rate, $glrow->rateid, 101);
                                                                    $rates = ($rates) ? $rates->RATES : 0;
                                                                    $total_amt = $dkwh * $rates;
                                                                    $amt = round($total_amt, 2);
                                                                    $amt_total_subs += $amt;
                                                                    //$demand = true;
                                                                } else {
                                                                    $rates = get_spec_rates($year, $month, $rate, $glrow->rateid, 101);
                                                                    $rates = ($rates) ? $rates->RATES : 0;
                                                                    $total_amt = (($dkwh / 159) * $rates);
                                                                    $amt = round($total_amt, 2);
                                                                    $amt_total_subs += $amt;
                                                                    // wala d 01-24-2018
                                                                    //$demand = true;
                                                                }
                                                                // DEMAND | VALUE ADDED TAX-OTHER CHARGES //
                                                                $arr_list_subs[] = array('ratename' => get_name_rates($glrow->rateid), 'rate' => $rates, 'amt' => $total_amt);
                                                            } else {

                                                                $rates = get_spec_rates($year, $month, $rate, $glrow->rateid, 100);
                                                                $rates = ($rates) ? $rates->RATES : 0;

                                                                if ($glrow->wdisc == 1  && $rateid == 1) {
                                                                    $ldisc = get_lifeline_discount($year, $month, $rate, $kwh);
                                                                    if ($ldisc->qry == true) {
                                                                        $amt = round($ldisc->amt, 2);
                                                                        //$amt_total_subs_lg += $amt;
                                                                    } else {
                                                                        $amt = round(($rates * $kwh), 2);
                                                                        //$amt_total_subs_lg += $amt;
                                                                    }
                                                                } else {
                                                                    $amt = round(($rates * $kwh), 2);
                                                                    //$amt_total_subs_lg += $amt;
                                                                }

                                                                $incgroup_arr[] = array('ratename' => get_name_rates($glrow->rateid), 'rate' => $rates, 'amt' => $amt);
                                                            }
                                                        }
                                                        $amt_total_subs_lg += $amt;

                                                    }
                                                }

                                                // LOCAL FRANCHISE TAX INSIDE SUB *****
                                                $list_groupinc_total = $amt_total_subs_lg * 0.00825;
                                                $amt_total_subs += $list_groupinc_total;
                                                $rates = get_spec_rates($year, $month, $rate, $rsrow->rateid, 103);
                                                $rates = ($rates) ? $rates->RATES : '';
                                                $rates = customize_rates_visibility($rsrow->rateid, $rates);
                                                // BILLING RATE EXEMPTIONS
                                                /* ##
                                                if(get_billing_rate_exempt($acctid, $rsrow->rateid)) {
                                                    $rates = 0;
                                                    $amt = 0;
                                                } ##
                                                */

                                                $arr_list_subs[] = array('ratename' => get_name_rates($rsrow->rateid), 'rate' => $rates, 'amt' => $list_groupinc_total, 'incgroup' => $incgroup_arr);
                                            } else {

                                                if ($rsrow->units == 102) {
                                                    $rates = get_spec_rates($year, $month, $rate, $rsrow->rateid, 102);
                                                    $rates = ($rates) ? $rates->RATES : 0;
                                                    $amt_total_subs += $rates; // *** //
                                                    $amt = $rates;
                                                    $rates = customize_rates_visibility($rsrow->rateid, $rates);
                                                    // BILLING RATE EXEMPTIONS

                                                    if (get_billing_rate_exempt($acctid, $rsrow->rateid)) {
                                                        $rate_exempt_cnt += 1;
                                                        $rates = 0;
                                                        $amt = 0;
                                                    }


                                                    // SUB TAX | Metering Charge, Supply Charge
                                                    $arr_list_subs[] = array('ratename' => get_name_rates($rsrow->rateid), 'rate' => $rates, 'amt' => $amt);
                                                } else {
                                                    if ($rate == 3 && $rsrow->rateid == 4) {
                                                        // IF GDLB = 5-S-7-1 (gdlb_main SYSID: 8)
                                                        if ($gdlbid == 434) {
                                                            $rates = get_spec_rates($year, $month, $rate, $rsrow->rateid, 101);
                                                            $rates = ($rates) ? $rates->RATES : 0;
                                                            $total_amt = $dkwh * $rates;
                                                            $amt = round($total_amt, 2) ;
                                                            $amt_total_subs += $amt;
                                                            $demand = true;
                                                        } else {
                                                            $rates = get_spec_rates($year, $month, $rate, $rsrow->rateid, 101);
                                                            $rates = ($rates) ? $rates->RATES : 0;
                                                            $total_amt = (($dkwh / 159) * $rates);
                                                            $amt_total_subs += round($total_amt, 2);
                                                            $demand = true;
                                                            $amt = $total_amt;
                                                        }

                                                        // BILLING RATE EXEMPTIONS
                                                        if (get_billing_rate_exempt($acctid, $rsrow->rateid)) {
                                                            $rate_exempt_cnt += 1;
                                                            $rates = 0;
                                                            $amt = 0;
                                                        }
                                                        // LOCAL FRANCHISE TAX | Demand
                                                        $arr_list_subs[] = array('ratename' => get_name_rates($rsrow->rateid), 'rate' => $rates, 'amt' => $amt);
                                                    } else {


                                                        $rates = get_spec_rates($year, $month, $rate, $rsrow->rateid, 100);
                                                        $rates = ($rates) ? $rates->RATES : 0;
                                                        $amt = $rates * $kwh;
                                                        if ($rsrow->wdisc == 1 && $rateid == 1) {
                                                            $ldisc = get_lifeline_discount($year, $month, $rate, $kwh);
                                                            if ($ldisc->qry == true) {
                                                                $amt_total_subs += round($ldisc->amt, 2);
                                                                $sub_amt = round($ldisc->amt, 2);
                                                            } else {
                                                                /*
                                                                if(get_billing_rate_exempt($acctid, $rsrow->rateid)) {
                                                                    $rates = 0;
                                                                    $sub_amt = 0;
                                                                }else {
                                                                    $amt_total_subs += floorp($amt, 2);
                                                                    $sub_amt = (($rates * $kwh));
                                                                }
                                                                */

                                                                $amt_total_subs += round($amt, 2);
                                                                $sub_amt = round(($rates * $kwh), 2);
                                                            }
                                                        } else {
                                                            /*
                                                            if(get_billing_rate_exempt($acctid, $rsrow->rateid)) {
                                                                $rates = 0;
                                                                $sub_amt = 0;
                                                            }else{
                                                                $sub_amt = (($rates * $kwh));
                                                                $amt_total_subs += floorp($amt, 2);
                                                            }
                                                            */

                                                            $sub_amt = (($rates * $kwh));
                                                            $amt_total_subs += round($sub_amt, 2);

                                                        }


                                                        // BILLING RATE EXEMPTIONS
                                                        /*
                                                        if(get_billing_rate_exempt($acctid, $rsrow->rateid)) {
                                                            $rates = 0;
                                                            $sub_amt = 0;
                                                        }
                                                        */


                                                        // $rate_amt = customize_rates_visibility($rsrow->rateid, $rates);
                                                        $arr_list_subs[] = array('ratename' => get_name_rates($rsrow->rateid), 'rate' => $rates, 'amt' => $sub_amt);
                                                    }   /// HERE #############
                                                }
                                            }
                                        }

                                        $sub_ids = '';
                                        $amt_percent = $rrow->percent;
                                        if ($amt_percent > 0) {
                                            $amt_total_subs = $amt_total_subs * $amt_percent;
                                        } else {
                                            $rate_with_sub_arr = array(14, 18);
                                            if(in_array($rrow->rateid, $rate_with_sub_arr)) {

                                                $qry_list_subs_id = $ci->db->select()
                                                    ->from('trn_billing_rates_group_list')
                                                    ->where(array('groupid' => $row->sysid, 'rateid' => $rrow->sysid, 'parentid' => 0, 'status' => 1))->get()->row();
                                                $rate_id = ($qry_list_subs_id) ? $qry_list_subs_id->sysid : false;
                                                if($rate_id) {
                                                    $qry_list_subs_s = $ci->db->select()
                                                        ->from('trn_billing_rates_group_list')
                                                        ->where(array('groupid' => $row->sysid, 'parentid' => $rate_id, 'status' => 1))->get();
                                                    if ($qry_list_subs_s->num_rows() > 0) {
                                                        foreach ($qry_list_subs_s->result() as $rsrow_rr) {
                                                            $rate_s_amt = get_spec_rates($year, $month, $rate, $rsrow_rr->rateid, 100);
                                                            $rate_s_amt = ($rate_s_amt) ? $rate_s_amt->RATES : 0;
                                                            $amt_total_subs += $rate_s_amt;
                                                        }
                                                    }
                                                }
                                            }else {
                                                $rate_s_amt = get_spec_rates($year, $month, $rate, $rrow->rateid, 103);
                                                $rate_s_amt = ($rate_s_amt) ? $rate_s_amt->RATES : 0;
                                                $amt_total_subs = ($amt_total_subs * $rate_s_amt);
                                            }
                                        }


                                        if ($rrow->rateid == 11 || $rrow->rateid == 12 || $rrow->rateid == 13) {
                                            $vat_amt_arr[] = array('rateid' => $rrow->rateid, 'amt' => $amt_total_subs);
                                            $vat_amt_total += round($amt_total_subs, 2);
                                        }

                                        // @TODO FOR MONITORING
                                        $rate_s_bs = get_spec_rates($year, $month, $rate, $rrow->rateid, 103);
                                        $rate_s_bs = ($rate_s_bs) ? $rate_s_bs->RATES : 0;

                                        $rate_amt = customize_rates_visibility($rrow->rateid, $rate_s_bs);

                                        if (get_billing_rate_exempt($acctid, $rrow->rateid)) {
                                            $rates_amt = 0;
                                            $amt_total_subs = 0;
                                            $rate_exempt_cnt += 1;
                                        }
                                        /* ----------------------------------------------------------------
                                        | CHARGES WITH SUBS
                                        |__________________________________________________________________
                                        */

                                        $arr_list_charges[] = array('subs' => true, 'ratename' => get_name_rates($rrow->rateid), 'rate' => $rate_amt, 'amt' => $amt_total_subs, 'bradedown' => $arr_list_subs);
                                        //$total_charges += $amt_total_subs;
                                    } else {

                                        if ($rrow->units == 102) {
                                            $rates = get_spec_rates($year, $month, $rate, $rrow->rateid, 102);
                                            $rates = ($rates) ? $rates->RATES : 0;
                                            /*
                                            $qry_discounts_ll = $ci->db->query("
                                                                          SELECT discount FROM billing_lifeline_discount_matrix
                                                                          WHERE $kwh BETWEEN kwhstart AND kwhend")->row();
                                            if ($qry_discounts_ll) {
                                                $amt = round($rates, 2);
                                            } else {
                                                $amt = round($rates, 2);
                                            }
                                            */


                                            $amt = round($rates, 2);

                                            //$amt_total_subs += $rates;
                                            //$arr_list_subs[] = array('ratename' => get_name_rates($rrow->rateid), 'rate' => 0, 'amt' => $rates);
                                        } else {
                                            if ($rrow->groupid == 1 && $rate == 3 && $rrow->rateid == 4) {
                                                // IF GDLB = 5-S-7-1 (gdlb_main SYSID: 8)
                                                if ($gdlbid == 434) {
                                                    $rates = get_spec_rates($year, $month, $rate, $rrow->rateid, 101);
                                                    $rates = ($rates) ? $rates->RATES : 0;
                                                    $total_amt = $dkwh * $rates;
                                                    //$amt_total_subs += $amt;
                                                    /*
                                                    $demand = true;

                                                    $rates = get_spec_rates($year, $month, $rate, $rrow->rateid, 101);
                                                    $rates = ($rates) ? $rates->RATES : 0;
                                                    $total_amt = $dkwh * $rates;
                                                    */
                                                    /*
                                                    $qry_discounts_ll = $ci->db->query("
                                                                          SELECT discount FROM billing_lifeline_discount_matrix
                                                                          WHERE $kwh BETWEEN kwhstart AND kwhend")->row();
                                                    if ($qry_discounts_ll) {
                                                        $amt = round($total_amt, 2);
                                                    } else {
                                                        $amt = round($total_amt, 2);
                                                    }
                                                    */

                                                    $amt = round($total_amt, 2);

                                                    $demand = true;
                                                } else {

                                                    $rates = get_spec_rates($year, $month, $rate, $rrow->rateid, 101);
                                                    $rates = ($rates) ? $rates->RATES : 0;
                                                    $total_amt = (($dkwh / 159) * $rates);

                                                    /*
                                                    $qry_discounts_ll = $ci->db->query("
                                                                          SELECT discount FROM billing_lifeline_discount_matrix
                                                                          WHERE $kwh BETWEEN kwhstart AND kwhend")->row();
                                                    if ($qry_discounts_ll) {
                                                        $amt = round($total_amt, 2);
                                                    } else {
                                                        $amt = round($total_amt, 2);
                                                    }
                                                    */

                                                    $amt = round($total_amt, 2);
                                                    $demand = true;
                                                }
                                            } else {
                                                $rates = get_spec_rates($year, $month, $rate, $rrow->rateid, 100);
                                                $rates = ($rates) ? $rates->RATES : 0;
                                                $total_amt = ($rates * $kwh);
                                                /*
                                                $qry_discounts_ll = $ci->db->query("
                                                                          SELECT discount FROM billing_lifeline_discount_matrix
                                                                          WHERE $kwh BETWEEN kwhstart AND kwhend")->row();
                                                if ($qry_discounts_ll) {
                                                    $amt = $total_amt;
                                                } else {
                                                    $amt = floorp($total_amt, 2);
                                                }
                                                */

                                                $amt = floorp($total_amt, 2);
                                                //$amt_total_subs += $amt;
                                                //$sub_amt = (($rates * $kwh));
                                                //$arr_list_subs[] = array('ratename' => get_name_rates($rrow->rateid), 'rate' => number_format(0, 4), 'amt' => $amt);
                                            }
                                        }
                                        /*
                                          $rates = get_spec_rates($year, $month, $rate, $rrow->rateid, 100);
                                          $rates = ($rates) ? $rates->RATES : 0;
                                          $amt = $rates * $kwh;
                                         *
                                         */
                                        //$total_charges += $amt;

                                        // --------------------------------------------------------------------------
                                        // DISPLAY RATES DEPENDS ON THE CUSTOMIZE PARAMETERS
                                        $rates_amt = customize_rates_visibility($rrow->rateid, $rates);

                                        // @TODO GET DISCOUNT
                                        if ($rrow->wdisc == 1  && $rateid == 1) {
                                            $ldisc = get_lifeline_discount($year, $month, $rate, $kwh);
                                            if ($ldisc->qry == true) {
                                                $amt = $ldisc->amt;
                                                $lldamt = $amt;
                                            }
                                        }

                                        if (get_billing_rate_exempt($acctid, $rrow->rateid)) {
                                            $rate_exempt_cnt += 1;
                                            $rates_amt = 0;
                                            $amt = 0;
                                        }

                                        if ($ownerinfo->discode == 269) {
                                            $qry_rate_disc = $ci->db->select('disc')
                                                ->from('billing_rate_discounts')
                                                ->where(array('types' => 269, 'rateid' => $rrow->rateid, 'status' => 1))
                                                ->get()->row();
                                            if ($qry_rate_disc && $kwh < 100) {

                                                $rate_disc_amt += $amt * $qry_rate_disc->disc;
                                            }
                                        }

                                        /* -----------------------------------------------------------------------
                                        | RATE GROUPING - CHARGES - NO SUB COMPUTES
                                        --------------------------------------------------------------------------
                                        */
                                        $arr_list_charges[] = array('subs' => false, 'ratename' => get_name_rates($rrow->rateid), 'rate' => $rates_amt, 'amt' => $amt);
                                    }
                                }

                            }
                            if ($demand == true) {
                                //$arr_list_charges[] = array('subs' => false, 'ratename' => 'Demand KWH', 'rate' => '', 'amt' => $dkwh);
                            }
                            /* -----------------------------------------------------------------------
                            | RATE GROUPING
                            --------------------------------------------------------------------------
                            */
                            $arr_charges_grp[] = array('groupid' => $row->sysid, 'groupname' => $row->descs, 'lists' => $arr_list_charges);
                        }
                    }

                    // #########################################################
                    $total_charges = 0;
                    foreach ($arr_charges_grp as $row) {
                        if (isset($row['lists']) && count($row['lists']) > 0) {
                            foreach ($row['lists'] as $lrow) {
                                $total_charges += round($lrow['amt'], 2);
                            }
                        }
                    }

                    // #########################################################
                    if($rate_exempt_cnt > 0) {
                        $footnote .= '<span class="label label-danger">ARCOA</span>';
                    }

                    if($ownerinfo->discode==269) {
                        // ADD COMPUTATION HERE
                        $date1 = new DateTime(date('Y-m-d', strtotime($ownerinfo->discdate)));
                        $date2 = new DateTime(date('Y-m-d', strtotime(date('Y-m-d h:m:i'))));
                        $years  = $date2->diff($date1)->y; // IF ZERO YEAR THEN ACTIVIVATE
                        if($years == 0) {
                            $scdisc = -($rate_disc_amt);
                            $total_charges = $total_charges + $scdisc;
                            $disname .= '<span style="margin-left: 2px;" class="label label-success">' . $ownerinfo->discounts .'</span> ';
                        }
                    }

                    $netmtramt = 0;
                    if($netmtr == 1) {
                        if($netmtrkwh > 0) {
                            $rates = get_spec_rates($year, $month, $rate, 1, 100);
                            $rates = ($rates) ? $rates->RATES : 0;
                            $netmtramt = $netmtrkwh * $rates;
                            $total_charges = $total_charges - $netmtramt;
                        }
                        $footnote .= '<span style="margin-left: 2px;" class="label label-info">NET NETERING</span>';
                    }




                    $ar_qry = get_customer_overdues($acctid, 1);

                    $billcnt = 1;
                    $dolpay = '';
                    $total_prevvat = 0;
                    $total_previous = 0;
                    $total_interest = 0;
                    if($ar_qry) {
                        $total_prevvat = 0;
                        $total_previous = $ar_qry->amtdue;
                        $total_interest = $ar_qry->amtint;
                        $dolpay = $ar_qry->dolpay;
                    }

                    $total = $total_charges + $total_previous;
                    $data['lldamt'] = (isset($lldamt)) ? $lldamt : 0; // APPLY FOR SUBSIDY DISCOUNT MATRIX
                    $data['scdisc'] = $scdisc;
                    $data['discounts'] = $disname;
                    $data['billcnt'] = $billcnt;
                    $data['total'] = $total;
                    $data['prevvat'] = $total_prevvat;
                    $data['previous'] = $total_previous;
                    $data['interest'] = $total_interest;
                    $data['totalcharges'] = $total_charges;
                    $data['totalvat'] = $vat_amt_total;
                    $data['genamt'] = $genamt;
                    $data['netmtr'] = $netmtr;
                    $data['netmtramt'] = $netmtramt;
                    $data['ratecode'] = $ratecode;
                    $data['billratecode'] = $billrate;
                    $data['gdlbid'] = $gdlbid;
                    $data['kwh'] = $kwh;
                    $data['curr'] = $total_charges;
                    $data['name'] = $name;
                    $data['addr'] = $addr;
                    $data['servno'] = $servno;
                    $data['gdlb'] = $gdlb;
                    $data['mo'] = $month;
                    $data['moyr'] = $year . '-' . $month;
                    $data['duedate'] = $year . '-' . (date('m') + 1) . '-' . date('d');
                    $data['footnote'] = $footnote;
                    $data['mult'] = $multcode;
                    $data['dolpay'] = $dolpay;
                    // #############################################
                    // BILLING DETAILS #############################
                    $col_num = count($arr_charges_grp);
                    $html = '';
                    $amt_curr_total = 0;
                    if ($col_num > 0) {
                        $col_width = (100 / $col_num);
                        foreach ($arr_charges_grp as $row) {
                            $html .= '<b style="font-size: 10px;">' . $row['groupname'] . '</b>';
                            $group_id = $row['groupid'];
                            ${'total_charges_' . $group_id} = 0;
                            if (isset($row['lists']) && count($row['lists']) > 0) {
                                $html .= '<ul style="list-style: none; margin: 0px 0px; padding: 0px 0px; font-size: 8px;">';
                                foreach ($row['lists'] as $lrow) {
                                    $rate = ($lrow['rate']>0) ? number_format($lrow['rate'], 4) : '';

                                    $ratename = $lrow['ratename'];

                                    //$total_charges += $lrow['amt'];
                                    ${'total_charges_' . $group_id} += round($lrow['amt'], 2);
                                    $html .= '<li class="" style="margin-top: 1px; margin-bottom: 1px; width: 100%; "><span style="width: 60%; display: inline-block;"><span style="width: 100%; display: inline-block; word-wrap: break-word; font-size: 9px;">' . $ratename . '</span> </span><span style="width: 17%; display: inline-block; text-align: right;">' . $rate . '</span> <span style="width: 20%; display:inline-block; text-align: right;">' . number_format($lrow['amt'], 2) . '</span></span>';
                                    /*
                                     * if ($lrow['subs'] == true) {
                                      $html .= '<ul class="list-group sub hidden" style="list-style-type: circle !important;">';
                                      foreach ($lrow['bradedown'] as $slrow) {
                                      $html .= '<li class="list-group-item" style="list-style: circle inside !important; font-size: 11px !important;"> <span class="col-md-7" style="padding-left: 12px !important;"><span style="padding-left: 15px; display: inline-block; ">' . $slrow['ratename'] . ':</span></span><span class="col-md-2 data text-align-right" >' . number_format($slrow['rate'], 4) . '</span> <span class="col-md-3 data pull-right text-align-right">' . number_format($slrow['amt'], 2) . '</span></li>';
                                      }
                                      $html .= '</ul>';
                                      }
                                      $html .= '</li>';
                                     *
                                     */

                                }
                                $amt_curr_total += ${'total_charges_' . $group_id};
                                $html .= '<li class="" style="margin-top: 1px; margin-bottom: 2px; font-size: 9px;"><span style="width: 69%; display: inline-block; margin-left: 10%; text-align: right;">Sub Total</span><span style="width: 20%; display:inline-block; text-align: right">' . number_format(${'total_charges_' . $group_id}, 2) . '</span></span></li>';
                                $html .= '</ul>';
                            }
                        }
                        $html .= '<span class="pull-right" style="font-size: 10px;"><b>' . number_format($total_charges, 2) . '</b></span>';
                    }
                    $data['current'] = $amt_curr_total;
                    $data['rep'] = $html;
                } else {
                    $qry = false;
                    $arr_charges_grp['err'] = 'Error: A102 | '.$rate.' (Rates Table) | Year: ' . $year . ' Month: ' . $month;
                }
            } else {
                $qry = false;
                $arr_charges_grp['err'] = 'Error: A101';
            }
        }else{
            $arr_charges_grp[] = 'RATE GROUP ERROR!';
        }
        $data['data'] = $arr_charges_grp;
        $data['qry'] = $qry;
        return $data;
    }

}

if(! function_exists('get_customer_overdues')) {
    function get_customer_overdues($acctid, $mtr)
    {

        $ci = &get_instance();
        $query = $ci->db->select('b.sysid, b.month, b.year, b.current, b.kwhuse, b.billno, b.totalvat, b.duedate')
            ->from('billing_reports_main AS b')
            ->where(array('b.acctid' => $acctid, 'b.mtr' => $mtr))
            ->order_by('b.prsdte', 'desc')
            ->limit(12)
            ->get();

        $legacy_amt_prev = 0;
        $amt_total_balance = 0;
        $amt_total_interest = 0;
        $amt_total_overdue = 0;
        $amt_total_pay = 0;
        $ar_nobills = 0;

        // GET LEGACY AR
        // LEGACY PREVAMT
        $legacy_amt_prev = 0;
        $qry_legacy_ar = $ci->db->select("amt_13 AS amt13")
            ->from('customer_accounts_ar AS ar')
            ->where(array('ar.acctid' => $acctid))
            ->get()->row();
        if($qry_legacy_ar) {
            $legacy_amt_prev = $qry_legacy_ar->amt13;
        }
        if ($query->num_rows() > 0) {
            $i = 0;
            $len = $query->num_rows();
            foreach ($query->result() as $row) {

                $amt_bal = 0;
                $qry_pay = $ci->db->select('SUM(p.amtpd) AS amtpd, SUM(p.interest) AS interest, SUM(p.frtax) AS frtax')
                    ->from('billing_payapplied AS p')
                    ->where(array('p.acctid' => $acctid, 'p.billyr' => $row->year, 'p.billmo' => $row->month, 'p.status' => 1))
                    ->get()->row();

                $amt_paid = 0;
                $datepaid = '';
                $duedate = $row->duedate;
                $amt_current = $row->current;
                $amt_int = 0;


                $todate = date("Y-m-d");

                $int_per = 0.0224;
                if (validateDate($duedate, 'Y-m-d')) {
                    // GET HOW MANYDUES
                    $qry_dues_bill = $ci->db->select('duedate')
                        ->from('billing_reports_main')
                        ->where(array('duedate > ' => $duedate, 'acctid' => $acctid, 'mtr' => 1))
                        ->get();
                    $num_rows_d = $qry_dues_bill->num_rows();
                    $duedate_dte = new DateTime($duedate);
                    $today = new DateTime($todate);
                    if ($today > $duedate_dte) {
                        $num_rows_d = $num_rows_d + 1;
                    }
                    $num_dues = $num_rows_d;
                    $int_total_per = $int_per * $num_dues;
                    $amt_int = $num_rows_d;
                    $amt_int = round(($amt_current * $int_total_per), 2);
                }


                if($qry_pay) {
                    $amt_paid = $qry_pay->amtpd + $qry_pay->interest;
                    $amt_bal = ($amt_current + $qry_pay->interest) - $amt_paid;
                    $amt_total_pay += $amt_paid;
                }

                $qry_pay_dt = $ci->db->select('CAST(p.datecreated AS DATE) AS datepd')
                    ->from('billing_payapplied AS p')
                    ->where(array('p.acctid' => $acctid, 'p.billyr' => $row->year, 'p.billmo' => $row->month, 'p.status' => 1))
                    ->get()->row();

                if($qry_pay_dt) {
                    $datepaid = $qry_pay_dt->datepd;
                }else{
                    $datepaid = '';
                }

                $total_amt_row = $amt_current + $amt_int;
                $intamt = 0;

                $paid = false;
                if($amt_paid > 0) {
                    $intamt =  $qry_pay->interest;
                    $paid = true;
                }else{
                    $intamt = $amt_int;
                    $amt_total_balance += (($amt_current + $amt_int) + $amt_bal);
                    $amt_total_interest += $amt_int;
                    $amt_total_overdue += $amt_current;
                    $ar_nobills += 1;
                }
                $check_box = '';
                if($i==0) {
                    if($qry_pay) {
                        if($amt_bal>0) {
                            $amt_total_current = $amt_bal;
                        }else{
                            $amt_total_current = 0;
                        }
                    }else {
                        $amt_total_current = ($amt_current + $intamt);
                    }

                }

                $data['list'][] = array(
                    'expand' => btn_expand($row->sysid),
                    'month' => '<span id="month_id" data-id="' . $row->sysid . '" class="label label-info">' . str_pad($row->month, 2, '0', STR_PAD_LEFT) . '</span> ' . strtoupper(date_formating($row->month, '!m', 'M')),
                    'year' => $row->year,
                    'billno' => $row->billno,
                    'kwh' => round($row->kwhuse),
                    'current' => number_format($amt_current, 2),
                    'duedate' => $duedate,
                    'interest' => number_format($intamt, 2),
                    'amtpaid' => number_format($amt_paid, 2),
                    'balance' => number_format($amt_bal, 2),
                    'datepaid' => $datepaid,
                    'remarks' => '',
                    'paid' => $paid
                );

            }
        }

        // LAST PAY DATE
        $ar_lastpay = null;
        $qry_last_pay = $ci->db->select('CAST(datecreated AS date) AS paydte')
            ->from('billing_payapplied')
            ->where(array('acctid' => $acctid, 'status' => 1))
            ->order_by('datecreated', 'desc')
            ->limit(1)
            ->get()->row();
        if($qry_last_pay) {
            $ar_lastpay = $qry_last_pay->paydte;
        }

        $data['dolpay'] = $ar_lastpay;
        $data['amtbal'] = $amt_total_balance;
        $data['amtprev'] = $legacy_amt_prev;
        $data['amtint'] = $amt_total_interest;
        $data['amtdue'] = $amt_total_overdue;
        $data['arnobill'] = $ar_nobills;

        return (object) $data;
    }
}

if(! function_exists('customize_rates_visibility')) {
    function customize_rates_visibility($rateid, $rates) {
        $rateid_not_showrate = array(4, 5, 6, 8, 11, 12, 13);
        if(!in_array($rateid, $rateid_not_showrate)) {
            $rates_amt = $rates;
        }else{
            $rates_amt = '';
        }
        return $rates_amt;
    }
}

if(!function_exists('get_billing_rate_exempt')) {
    function get_billing_rate_exempt($acctid, $rateid) {
        $ci = &get_instance();
        $qry = $ci->db->select('acctid')->from('billing_rate_exemptions')
            ->where(array('acctid' => $acctid, 'rateid' => $rateid))
            ->get()->row();
        return ($qry) ? true : false;
    }
}
if(!function_exists('get_lifeline_discount')) {
    function get_lifeline_discount($year, $month, $rate, $kwh) {
        $ci = &get_instance();
        $data = array();
        $qry_discounts_ll = $ci->db->query("
                              SELECT discount FROM billing_lifeline_discount_matrix
                              WHERE $kwh BETWEEN kwhstart AND kwhend")->row();
        if($qry_discounts_ll) {
            $gen_subs = get_spec_rates($year, $month, $rate, 1, 100);
            $trn_subs = get_spec_rates($year, $month, $rate, 2, 100);
            $dis_subs = get_spec_rates($year, $month, $rate, 3, 100);
            $sup_subs = get_spec_rates($year, $month, $rate, 5, 100);
            $mtr_subs = get_spec_rates($year, $month, $rate, 6, 100);
            $slr_subs = get_spec_rates($year, $month, $rate, 9, 100);
            $slc_subs = get_spec_rates($year, $month, $rate, 6, 102);


            $sum_disc = (($gen_subs) ? $gen_subs->RATES * $kwh : 0) +
                (($trn_subs) ? (round($trn_subs->RATES * $kwh,2)) : 0) +
                (($sup_subs) ? (round($sup_subs->RATES * $kwh,2)) : 0) +
                (($mtr_subs) ? (round($mtr_subs->RATES * $kwh,2)) : 0) +
                (($slr_subs) ? (round($slr_subs->RATES * $kwh,2)) : 0) +
                (($slc_subs) ? (round($slc_subs->RATES, 2)) : 0) +
                (($dis_subs) ? (round($dis_subs->RATES * $kwh,2)) : 0);

            //$disc_amt = $sum_disc * $qry_discounts_ll->discount;
            $disc_amt = $gen_subs;
            // $amt = -($sum_disc - $disc_amt);
            $amt = - ($sum_disc * $qry_discounts_ll->discount);
            $qry = true;
        }else{
            $qry = false;
            $amt = false;
        }

        $data['amt'] = $amt;
        $data['qry'] = $qry;
        return (object)$data;
    }
}

function dev_name_case($name)
{
    $name = strtolower($name);
    switch ($name) {
        case 'se':
            return 'Lucky John Faderon<br><em class="text-danger">PECO-SE</em>';
            break;
        case 'luckyjohn':
            return 'Lucky John Faderon<br><em class="text-info">Laptop</em>';
            break;
        case 'iceberg':
            return 'Mark Edrian Gillermo<br><em>Laptop</em>';
            break;
        case 'marlon':
            return 'Marlon John Varon<br><em>PECO-SA</em>';
            break;
        default:
            return 'Administrator';
    }

}

// new function for search employee in payroll
if (!function_exists('select_employee')) {

    function select_employee()
    {
        $ci = &get_instance();

        $query = $ci->db->query("SELECT e.sysid, p.lastname, p.firstname FROM prime_employee_main as e 
left join person as p on e.personid = p.sysid where e.status = 1 group by e.sysid, p.lastname, p.firstname order by p.lastname asc");
        return $query;
    }

}
// search from prime types parameter for manual earnings and deduction entry
if (!function_exists('select_earning_deduction_type')) {

    function select_earning_deduction_type()
    {
        $ci = &get_instance();

        $query = $ci->db->query("select * from prime_types_parameter where codes = 'PRTRNTYPE'");
        return $query;
    }

}
if (!function_exists('select_workshift')) {

    function select_workshift()
    {
        $ci = &get_instance();

        $query = $ci->db->query("select * from prime_employee_main_workshift");
        return $query;
    }

}


if (!function_exists('readcheck')) {
    function readcheck($preskwh, $prevkhw, $presread, $prevread)
    {
        $ci = &get_instance();
        // VAR
        $data = array();
        $readrem = '';
        $incdecstat = false;
        $increase = 0;
        $decrease = 0;
        $id = 0;
        $inc_dec_icon = '';
        $inc_dec_bg = '';

        $read_bill = true; // FOR REGULAR BILLING
        $read_check = false; // FOR RECHECK
        $read_add = false; // FOR ADD BILLING COMPUTE

        $max = false;

        // GET THE DIFFERENCE OF PRESENT AND PREVIOUS
        $diff = bcsub($preskwh, $prevkhw, 2);
        if ($diff != 0 && $prevkhw > 0) {
            $incdec = bcmul(bcdiv($diff, $prevkhw, 2), 100, 2);
        } else {
            $incdec = 0;
            $readrem = 'NO INC.';
        }
        if($presread == 0) {
            $readrem = 'NO READING';
            $incdecstat = true;
            $inc_dec_bg = 'info';
            $read_check = true;
            $inc_dec_icon = '<i class="fa fa-times" style="color: red"></i>';
        }else {
            if ($preskwh == 0) {
                $readrem = 'ZERO READ';
                $incdecstat = true;
                $inc_dec_bg = 'warning';
                $read_add = true;
                $inc_dec_icon = '<i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-right"></i>';
            } else {
                if ($prevread < $presread) {
                    if ( $preskwh != 0 ) {
                        $npreswh = abs($preskwh);
                        $qry_get_end = $ci->db->select('sysid, kwhfrom, increase, decrease')->from('reading_analysis_matrix')
                            ->where(array('end' => 1, 'kwhfrom <= ' => $npreswh))
                            ->order_by('kwhfrom', 'desc')
                            ->get()->row();
                        if ($qry_get_end) {
                            $id = $qry_get_end->sysid;
                            $increase = $qry_get_end->increase;
                            $decrease = $qry_get_end->decrease;
                            if ($npreswh >= $qry_get_end->kwhfrom) {
                                $max = true;
                                // GET INCREASE CONDITION (-/+)
                                if ($diff > 0) {
                                    // CHECK INCREASE IF TRUE
                                    if ($incdec >= $qry_get_end->increase) {
                                        $incdecstat = true;
                                        $readrem = 'RECHECK';
                                        $inc_dec_bg = 'danger';
                                        $read_check = true;
                                    } else {
                                        $inc_dec_bg = 'success';
                                    }
                                    $inc_dec_icon = '<i class="fa fa-angle-double-up text-success"></i> ';

                                } else {
                                    if (abs($incdec) >= $qry_get_end->decrease) {
                                        $incdecstat = true;
                                        $readrem = 'RECHECK';
                                        $inc_dec_bg = 'danger';
                                        $read_check = true;
                                    } else {
                                        $inc_dec_bg = 'success';
                                    }
                                    $inc_dec_icon = '<i class="fa fa-angle-double-down text-danger"></i> ';

                                }
                            }
                        } else {
                            $qry_matrix = $ci->db->query("
                              SELECT sysid, decrease, increase FROM reading_analysis_matrix
                              WHERE $npreswh BETWEEN kwhfrom AND kwhend")->row();

                            if ($qry_matrix) {
                                $increase = $qry_matrix->increase;
                                $decrease = $qry_matrix->decrease;
                                $id = $qry_matrix->sysid;
                                if ($diff > 0) {
                                    if ($incdec >= $qry_matrix->increase) {
                                        $incdecstat = true;
                                        $readrem = 'RECHECK';
                                        $inc_dec_bg = 'danger';
                                        $read_check = true;
                                    } else {
                                        $inc_dec_bg = 'success';
                                    }
                                    $inc_dec_icon = '<i class="fa fa-angle-double-up text-success"></i> ';
                                } else {
                                    if (abs($incdec) >= $qry_matrix->decrease) {
                                        $incdecstat = true;
                                        $readrem = 'RECHECK';
                                        $inc_dec_bg = 'danger';
                                    } else {
                                        $inc_dec_bg = 'success';
                                    }
                                    $inc_dec_icon = '<i class="fa fa-angle-double-down text-danger"></i> ';

                                }
                            }
                        }

                    } else {
                        $readrem = 'ZERO READING';
                        $incdecstat = true;
                        $inc_dec_bg = 'warning';
                        $read_add = true;
                    }
                } else {
                    $readrem = 'RECHECK';
                    $incdecstat = true;
                    $inc_dec_bg = 'danger';
                    $read_check = true;
                    $inc_dec_icon = '<i class="fa fa-angle-double-down text-danger"></i> ';
                }
            }
        }


        // READING RECHECK IS FALSE AND ADD BILL ZERO CONSUMPTION IS FALSE
        $read_bill = ($read_check == false && $read_add == false) ? true : false;

        $data['increase'] = $increase;
        $data['decrease'] = $decrease;
        $data['icon'] = $inc_dec_icon;
        $data['color'] = $inc_dec_bg;

        $data['regbill'] = $read_bill;
        $data['chkread'] = $read_check;
        $data['addbill'] = $read_add;

        $data['recheck'] = $incdecstat;
        $data['per'] = $incdec;
        $data['max'] = $max;
        $data['prskwh'] = $preskwh;
        $data['prvkwh'] = $prevkhw;
        $data['rem'] = $readrem;
        $data['tblid'] = $id;
        return (object)$data;

    }
}

if(!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

if(!function_exists('nb_mois')) {
    function nb_mois($date1, $date2) {
        $begin = new DateTime($date1);
        $end = new DateTime($date2);
        $end = $end->modify('+1 month');

        $interval = DateInterval::createFromDateString('1 month');

        $period = new DatePeriod($begin, $interval, $end);
        $counter = 0;
        foreach ($period as $dt) {
            $counter++;
        }
        return $counter;
    }
}


if(!function_exists('count_digit')){
    function count_digit($number) {
        return strlen($number);
    }
}
if(!function_exists('mtr_wrap_kwh')) {
    function mtr_wrap_kwh($pres_read = 0, $prev_read = 0, $readid)
    {
        $ci = &get_instance();
        $get_findings = $ci->db->select('tf.findingid, rf.codes')
            ->from('trn_reading_findings AS tf')
            ->join('meter_reading_findings AS rf', 'rf.sysid = tf.findingid')
            ->where(array('tf.status' => 1, 'tf.readingid' => $readid))
            ->order_by('tf.dateupdated', 'desc')
            ->get()->row();

        $pres_cons = bcsub($pres_read, $prev_read, 2);
        $findingid = 0;
        $num = strlen(trim(round($prev_read,0)));

        if($get_findings) {
            $findingid = $get_findings->findingid;
            $max_num = (int)str_pad(9, $num, "9", STR_PAD_LEFT);
            $new_num = bcadd(bcadd($max_num, 1, 2), $pres_read, 2);
            if ($findingid == 20) {
                $pres_cons = bcsub($new_num, $prev_read, 2);
            }
        }

        return $pres_cons;
    }
}

if(!function_exists('count_months')) {
    function count_months($datestart, $dateend)
    {
        if(validateDate($datestart)) {
            $d1 = strtotime($datestart);
            $d2 = strtotime($dateend);
            $min_date = min($d1, $d2);
            $max_date = max($d1, $d2);
            $i = 0;
            while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
                $i++;
            }
            return $i;
        }else{
            return 0;
        }
    }
}

if(!function_exists('compute_interest')) {
    function compute_interest($curr, $amt, $due, $arr)
    {
        $iter = new ArrayIterator($arr);
        $i = 1;
        $ii = 0;
        $int_cnt = 0;
        $int_amt_total = 0;
        $arr_month_withint = array();


        foreach ($arr as $keys => $amt_num_row) {
            if ($amt_num_row['amt'] > 0) {
                $amt_due[] = array('amt' => $amt_num_row, 'month' => $keys + 1);
            }
        }

        $num_loop = count($amt_due);
        $last_due_month = 0;
        foreach ($amt_due as $row_due) {
            if ($ii == $num_loop - 1) {
                $last_due_month = $row_due['month'];
            }
            $ii++;
        }

        $compute_last = false;
        foreach ($arr as $keys => $bills_row) {
            // get next key and value...
            $iter->next();
            $nextKey = $iter->key();
            $nextValue = $iter->current();
            $val_next = $i++;
            $last = '';
            if ($nextValue['month'] - 1 == $last_due_month && $due == true) {
                $last = 'last';
                $compute_last = true;
            }

            /*
            if ($nextValue['month'] > $curr && $curr < $val_next && $bills_row['amt'] > 0) {
                $int_cnt += 1;

                $int_amt = $amt * ($int_cnt * 0.0224);
                echo $bills_row['month'] . ' : ' . $bills_row['amt'] . ' : ';
                echo $int_amt;
                echo ' INT CNT: ' . $int_cnt . ' - ' . $last;
                echo '<br>';
                $arr_month_withint[$bills_row['month']] = $int_amt;
                $int_amt_total += $int_amt;

            }
            */

            if($nextValue['month'] == $last_due_month && $due==true) {
                $int_cnt += 1;
            }else{
                if($nextValue['month'] > $curr && $curr < $val_next && $bills_row['amt'] > 0) {
                    if($nextValue['month'] == $last_due_month) {
                        $int_cnt += ($int_cnt + 1);
                    }else {
                        $int_cnt += 1;
                    }
                }
            }
        }
        $total_int_compute = $int_cnt * 0.0224;
        $int_amt_total = $total_int_compute * $amt;
        return (object)array('month' => $curr, 'intamt' => $int_cnt);
    }
}


if(!function_exists('getWorkingDays')) {
    function getWorkingDays($startDate, $endDate, $holidays)
    {
        // do strtotime calculations just once
        $endDate = strtotime($endDate);
        $startDate = strtotime($startDate);

        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = ($endDate - $startDate) / 86400 + 1;

        $no_full_weeks = floor($days / 7);
        $no_remaining_days = fmod($days, 7);

        //It will return 1 if it's Monday,.. ,7 for Sunday
        $the_first_day_of_week = date("N", $startDate);
        $the_last_day_of_week = date("N", $endDate);

        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($the_first_day_of_week <= $the_last_day_of_week) {
            if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
            if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
        } else {
            // (edit by Tokes to fix an edge case where the start day was a Sunday
            // and the end day was NOT a Saturday)

            // the day of the week for start is later than the day of the week for end
            if ($the_first_day_of_week == 7) {
                // if the start date is a Sunday, then we definitely subtract 1 day
                $no_remaining_days--;

                if ($the_last_day_of_week == 6) {
                    // if the end date is a Saturday, then we subtract another day
                    $no_remaining_days--;
                }
            } else {
                // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
                // so we skip an entire weekend and subtract 2 days
                $no_remaining_days -= 2;
            }
        }

        //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
        $workingDays = $no_full_weeks * 5;
        if ($no_remaining_days > 0) {
            $workingDays += $no_remaining_days;
        }

        //We subtract the holidays
        foreach ($holidays as $holiday) {
            $time_stamp = strtotime($holiday);
            //If the holiday doesn't fall in weekend
            if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N", $time_stamp) != 6 && date("N", $time_stamp) != 7)
                $workingDays--;
        }

        return $workingDays;
    }
}




if(!function_exists('computer_client_name')) {
    function computer_client_name()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        return "ITD-SE"; //used only for testing
        // return exec("nmblookup -A $ip | grep '<00' | grep -v GROUP | awk '{print $1}'"); //get the computer name of $ip, only works when server is Linux
    }
}
if(!function_exists('get_select_chart_of_accounts')) {
    function get_select_chart_of_accounts($types) {
        $ci = &get_instance();
        $qry = $ci->db->select('sysid, codes, descs')->from('prime_chart_of_accounts')->where(array('types' => $types, 'status' => 1))->get();
        if($qry->num_rows()>0) {
            return $qry->result();
        }else{
            return false;
        }
    }
}
if(!function_exists('get_name_chart_of_accounts')) {
    function get_name_chart_of_accounts($id) {
        $ci = &get_instance();
        $qry = $ci->db->select('sysid, codes, descs')->from('prime_chart_of_accounts')->where(array('sysid' => $id))->get()->row();
        return ($qry) ? $qry : $id;
    }
}
if(!function_exists('array_contains_key')) {
    function array_contains_key($search_array, $search_key, $search_res, $search_value)
    {
        $return = array();
        foreach ($search_array as $key => $val) {

            if ($val[$search_key] == $search_value) {
                $return[] = $val;
            }

            // SEARCH WILD CARDS
            if (strpos(strtolower($val[$search_key]), strtolower($search_value)) == TRUE) {
                $return[] = $val;
            }
        }
        return $return;
    }
}

if(!function_exists('check_account_payment')) {
    function check_account_payement($dataid, $moduleid, $acctid) {
        $ci = &get_instance();
        $qry = $ci->db->select('totalamt')->from('transaction_payments_logs')
            ->where(array('dataid' => $dataid, 'moduleid' => $moduleid, 'payforacctno' => $acctid, 'status' => 1) )
            ->get()->row();
        return ($qry) ? $qry->totalamt : false;
    }
}
if(!function_exists('pay_account_array')) {
    function pay_account_array() {
        $ci = &get_instance();

        $pay_acct_arr = array();

        $qry_check = $ci->db->select()
            ->from('prime_chart_of_accounts')
            ->where('types', 1)->get();

        if($qry_check->num_rows()>0) {
            foreach($qry_check->result() as $row) {
                $pay_acct_arr[] = $row->sysid;
            }
        }
        return $pay_acct_arr;
    }
}

if(!function_exists('insert_paylogs')) {
    function insert_paylogs($ins) {
        $data = array();
        $ci = &get_instance();
        $ci->db->insert('transaction_payments_logs', $ins);
        $sysid = $ci->db->insert_id();
        $err_msg = $ci->db->_error_message();
        $data['ins'] = $ins;
        $data['err'] = $err_msg;
        $data['id'] = $sysid;
        return $data;
    }
}

if(!function_exists('get_orno')) {
    function get_orno() {
        $ci = &get_instance();
        $qry_orno = $ci->db->select_max('orno')->from('transaction_payments_logs')
            ->get()->row();
        return ($qry_orno) ? $qry_orno->orno + 1 : 1;
    }
}

if(!function_exists('get_trnno')) {
    function get_trnno() {
        $ci = &get_instance();
        $d = date('d');
        $m = date('m');
        $y = date('Y');
        $qry_orno = $ci->db->select_max('trnno')
            ->from('transaction_payments_logs')
            ->where(array(
                'YEAR(datecreated)' => $y,
                'MONTH(datecreated)' => $m,
                'DAY(datecreated)' => $d,
                'createdby' => user_id()
            ))
            ->get()->row();
        return ($qry_orno) ? $qry_orno->trnno + 1 : 1;
    }
}

if(!function_exists('check_paymode')) {
    function check_paymode($orno) {
        $ci = &get_instance();
        $chk = 0;
        $cash = 0;

        $amt_check = 0;
        $amt_cash = 0;
        $qry = $ci->db->select('payform, totalamt')->from('transaction_payments_logs')
            ->where(array('orno' => $orno))->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                if($row->payform==1) {
                    $cash += 1;
                    $amt_cash += $row->totalamt;
                }
                if($row->payform==2) {
                    $chk += 1;
                    $amt_check += $row->totalamt;
                }
            }
        }
        $mode = '';
        $class = '';
        if($chk>=1 && $cash>=1) {
            $mode = 'Check/Cash';
            $class = 'label label-warning';
        }else{
            if($cash>=1) {
                $mode = 'Cash';
                $class = 'label label-success';
            }else{
                $mode = 'Check';
                $class = 'label label-danger';
            }
        }
        $data['amtcheck'] = $amt_check;
        $data['amtcash'] = $amt_cash;
        $data['text'] = $mode;
        $data['class'] = $class;
        return (object)$data;
    }

}


if(!function_exists('convert_year')) {
    function convert_year($oldformat, $newformat, $year) {
        $dt = DateTime::createFromFormat($oldformat, $year);
        return $dt->format($newformat);
    }
}


if(!function_exists('convert_date_number')) {
    function convert_date_number($oldformat, $newformat, $date) {
        $dt = DateTime::createFromFormat($oldformat, $date);
        return $dt->format($newformat);
    }
}

if(!function_exists('cleanData')) {
    function cleanData($a)
    {
        return floatval(str_replace(',', '', $a));
    }
}

function get_array_last($arr) {
    return end($arr);
}

function sum_the_time($time1, $time2) {
    if(!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $time1)){
        $time1 = '00:00:00';
    } else {
        $time1 = date('H:i:s', floor(strtotime($time1)/60)*60);
    }
    if(!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $time2)){
        $time2 = '00:00:00';
    } else {
        $time2 = date('H:i:s', floor(strtotime($time2)/60)*60);
    }
    $times = array($time1, $time2);
    $seconds = 0;
    foreach ($times as $time)
    {
        list($hour,$minute,$second) = explode(':', $time);
        $seconds += $hour*3600;
        $seconds += $minute*60;
        $seconds += $second;
    }
    $hours = floor($seconds/3600);
    $seconds -= $hours*3600;
    $minutes  = floor($seconds/60);
    $seconds -= $minutes*60;
    if($seconds !== false)
    {
        $seconds = str_pad($seconds,2,'0',STR_PAD_LEFT);
    }
    if($minutes !== false)
    {
        $minutes = str_pad($minutes,2,'0',STR_PAD_LEFT);;
    }
    if($hours !== false)
    {
        $hours = str_pad($hours,2,'0',STR_PAD_LEFT);;
    }
    if("{$hours}:{$minutes}:{$seconds}" == '00:00:00' || "{$hours}:{$minutes}:{$seconds}" == '0:00:00'){
        return '';
    }else{
        return "{$hours}:{$minutes}:{$seconds}";
    }

}

function generate_barcode($text) {
    $ci = &get_instance();
    // Including all required classes
    include_once(FCPATH.'application/views/plugins/barcode/class/BCGFontFile.php');
    include_once(FCPATH.'application/views/plugins/barcode/class/BCGColor.php');
    include_once(FCPATH.'application/views/plugins/barcode/class/BCGDrawing.php');


// Including the barcode technology
    include_once(FCPATH.'application/views/plugins/barcode/class/BCGcode39.barcode.php');

// Loading Font
    $font = new BCGFontFile(FCPATH.'application/views/plugins/barcode/font/Arial.ttf', 18);

    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);

    $drawException = null;
    try {
        $code = new BCGcode39();
        $code->setScale(2); // Resolution
        $code->setThickness(30); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch(Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
    1 - Filename (empty : display on screen)
    2 - Background color */
    $drawing = new BCGDrawing('', $color_white);
    if($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Header that says it is an image (remove it if you save the barcode to a file)
    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="barcode.png"');

    // Draw (or save) the image into PNG format.
    return $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

function ts_status_pending($stat) {
    $ret = false;
    $stat_arr = array(1018,1017,1016,1015,300,377,376,311);
    if(in_array($stat, $stat_arr)) {
        $ret = true;
    }
    return $ret;
}

function ts_status_pending_where() {
    // 1007 1028
    return array(307, 300, 309, 311, 361, 364, 376, 377, 1015, 1016, 1017, 1018);
}

function generate_qrcode($text) {
    $ci = &get_instance();
    // Including all required classes
    include_once(FCPATH.'application/views/plugins/phpqrcode/qrlib.php');
    return QRcode::png($text);
}

if(!function_exists('insert_system_logs')) {
    function insert_system_logs($logtype, $value, $remarks, $moduleid)
    {
        $ci = &get_instance();
        if ($value) {
            $hour = date('H');
            $day = date('d');
            $month = date('m');
            $year = date('Y');
            $qry_ch = $ci->db->select('typesid')
                ->from('system_logs')
                ->where(
                    array(
                        'typesid' => $logtype,
                        'HOUR(datecreated) = ' => $hour,
                        'YEAR(datecreated) = ' => $year,
                        'MONTH(datecreated) = ' => $month,
                        'DAY(datecreated) = ' => $day
                    )
                )
                ->get()->row();
            if ($qry_ch == false) {
                $ins_arr = array(
                    'typesid' => $logtype,
                    'moduleid' => $moduleid,
                    'value' => $value,
                    'remarks' => $remarks
                );
                $ci->db->insert('system_logs', $ins_arr);
            }
        }
    }
}

if(!function_exists('hold_payments')) {
    function hold_payments($codeid) {
        $arr = array(351, 352, 353, 354, 356);
        if(in_array($codeid, $arr)){
            return true;
        } else{
            return false;
        }
    }
}

if(!function_exists('get_new_rvno')) {
    function get_new_rvno() {
        $ci = &get_instance();
        $query = $ci->db->select("MAX(reqverification) AS rvno")
            ->from('ticketing_details_logs')
            ->get()->row();
        return ($query) ? ($query->rvno + 1) : 1;
    }
}

if(!function_exists('get_submeter_total')) {
    function get_submeter_total($acctmainid, $year, $month) {
        $data = array();
        $ci = &get_instance();

        $total_kwh = 0;
        $sub = false;
        $query = $ci->db->select()->from('customer_accounts_main_submatrix')
            ->where(array('acctmainid' => $acctmainid))
            ->get();
        if($query->num_rows() > 0) {
            $sub = true;
            foreach($query->result() as $row) {


                $ownerinfo = get_active_account_info($row->acctid);

                $presread = 0;
                $demandkwh = 0;
                $netmtrkwh = 0;


                // #########################################
                // GET PRESENT READING #####################
                // GET GDLB OF ACCT
                $qry_sub_acct = $ci->db->select('gdlb')
                    ->from('customer_accounts_main')
                    ->where('sysid', $row->acctid)
                    ->get()->row();
                $new_presread = false;

                if($qry_sub_acct) {
                    // ######################################
                    // GET SCHED ID #########################
                    $qry_sched = $ci->db->select('sysid')
                        ->from('reading_schedule_main')
                        ->where(array('gdlbid' => $qry_sub_acct->gdlb, 'years' => $year, 'months' => $month))
                        ->get()->row();

                    $schedid = $qry_sched->sysid;


                    if($qry_sched) {

                        $qry_read = $ci->db->select('sysid, reading, demand, netmtr')
                            ->from('customer_accounts_subscription_meter_reading')
                            ->where(array('acctid' => $row->acctid, 'mtrid' => $row->mtrno, 'schedid' => $qry_sched->sysid, 'status' => 1))
                            ->get()->row();

                        if($qry_read) {
                            $presread = $qry_read->reading;
                            $demandkwh = $qry_read->demand;
                            $netmtrkwh = $qry_read->netmtr;
                            $new_presread = true;
                        }else {
                            $qry_read_log = $ci->db->select('sysid, reading, demand, netmtr')
                                ->from('customer_accounts_subscription_meter_reading_logs')
                                ->where(array('acctid' => $row->acctid, 'mtrid' => $row->mtrno, 'schedid' => $qry_sched->sysid, 'status' => 1))
                                ->get()->row();
                            if($qry_read_log) {
                                $presread = $qry_read_log->reading;
                                $demandkwh = $qry_read_log->demand;
                                $netmtrkwh = $qry_read_log->netmtr;
                                $new_presread = true;
                            }else{
                                $qry_read_temp = $ci->db->select('sysid, reading, demand, netmtr')
                                    ->from('customer_accounts_subscription_meter_reading_temp')
                                    ->where(array('acctid' => $row->acctid, 'mtrid' => $row->mtrno, 'schedid' => $qry_sched->sysid, 'status' => 1))
                                    ->get()->row();
                                if($qry_read_temp) {
                                    $presread = $qry_read_temp->reading;
                                    $demandkwh = $qry_read_temp->demand;
                                    $netmtrkwh = $qry_read_temp->netmtr;
                                    $new_presread = true;
                                }
                            }
                        }
                    }
                }

                // #########################################
                // GET PREVIOUS READING ####################
                $prvrdg_qry = $ci->db->select('
                        prvrdg, 
                        prsrdg, 
                        (prsrdg - prvrdg) AS kwhuse
                        '
                )
                    ->from('reading_schedule_meters_logs')
                    ->where(array('acctid' => $row->acctid, 'schedid' => $schedid, 'status' => 1))
                    ->order_by('dteprt', 'desc')
                    ->get()->row();
                if($prvrdg_qry) {
                    $prevread = $prvrdg_qry->prsrdg;
                }else{
                    $prevread = $row->intread;
                }


                // SUM TOTAL HERE
                if($new_presread) {
                    $kwh = ($presread - $prevread) * $ownerinfo->multiplier;
                    $total_kwh += $kwh;
                }else{
                    $kwh = 0;
                }

                $data['readings'][] = array(
                    'acctid' => $row->acctid,
                    'prevread' => $prevread,
                    'presread' => $presread,
                    'demandkwh' => $demandkwh,
                    'netmtrkwh' => $netmtrkwh,
                    'preskwh' => $kwh,
                    'mtrno' => $row->mtrno,
                    'gdlbid' => $qry_sub_acct->gdlb
                );

            }
        }
        $data['totalkwh'] = $total_kwh;
        $data['sub'] = $sub;
        return (object)$data;
    }



}

if(!function_exists('employee_print_header')) {
    function peco_print_header($userid, $reptitle, $code = false, $pdf = false) {

        $info = user_info($userid);

        $html = '';
        $name = $info->lastname . ', '.$info->firstname;

        $qry_empinfo = get_instance()->db->select('sysid')
            ->from('prime_employee_main')
            ->where(array('personid' => $info->personid, 'status' => 1))
            ->get()->row();

        $deptcode = '';
        if($code) {
            $deptcode = $code;
        }else {
            if ($qry_empinfo) {
                $emp_info = get_employee_info($qry_empinfo->sysid);
                $deptcode = $emp_info->deptcode;
            }
        }

        if($pdf==true) {
            $pref = FCPATH . 'assets/global/img/logo/';
            $candidates = array(
                $pref . 'tvi-small-logo.png',
                $pref . 'tvi-small-logo.png',
                $pref . 'pae-small-logo.png',
                $pref . 'peco-logo-login.png'
            );
            $pref_alt = FCPATH . 'assets/global/img/';
            $candidates[] = $pref_alt . 'tvi-small-logo.png';
            foreach ($candidates as $cand) {
                if (file_exists($cand)) { $bgimg = $cand; break; }
            }
            if (!isset($bgimg)) { $bgimg = $pref . 'peco-logo-login.png'; }
        } else {
            $pref = base_url() . 'assets/global/img/logo/';
            $bgimg = $pref . 'tvi-small-logo.png';
        }

        if($pdf==true) {
            $html .= '<img style="z-index: 0; width: 120px; height: 25px;" src="' . convert_base64_img($bgimg) . '" />';
        }else{
            $html .= '<img style="z-index: 0; width: 120px; height: 25px;" src="' . $bgimg . '" />';
        }
        $html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; left: 150px; font-size: 12px; top: 0px; width: 300px; display: inline-block; text-align: center; font-weight: bold;">Tiedra Ventures, Inc.</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; left: 150px; font-size: 9px; top: 14px; width: 300px; display: inline-block; text-align: center; f">Emperor Cement Compound, Coastal Rd., Balabago, Jaro, Iloilo City</span>';
        $html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; right: 70px; font-size: 12px; top: 0px; width: 250px; display: inline-block; text-align: right; font-weight: bold;">' . $name . '</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 70px; font-size: 11px; top: 16px; width: 250px; display: inline-block; text-align: right">'.$reptitle.'</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 60px; top: 5px; width: 130px; display: inline-block; border-right: 1px solid #ccc; height: 20px;"></span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-size: 20px; top: 0px; width: 130px; display: inline-block; text-align: right; font-weight: bold;">' . $deptcode . '</span>';
        $html .= '<div style="height: 40px;"></div>';

        $html .= '<hr style="border: 1px dashed #ccc; margin: 10px 0px;">';
        return $html;
    }
}

if(!function_exists('customer_print_header')) {
    function customer_print_header($personid, $reptitle, $code = false, $pdf = false) {

        $person = get_person_info($personid);

        $html = '';
        $name = $person->info->lastname . ', '.$person->info->firstname;

        $code = ($code) ? $code : 'XO';

        if($pdf==true) {
            // PDF IS TRUE = E:/xammp/htdocs/erp/
            $bgimg = FCPATH . 'assets/global/img/logo/pae-small-logo.png';
        } else {
            // PDF IS FALSE = http://localhost/erp/
            $bgimg = base_url() . 'assets/global/img/logo/pae-small-logo.png';
        }

        $html .= '<img style="z-index: 0; width: 120px; height: 25px;" src="' . $bgimg . '" />';
        $html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; left: 150px; font-size: 12px; top: 0px; width: 300px; display: inline-block; text-align: center; font-weight: bold;">Panay Alternative Energy, Inc.</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; left: 150px; font-size: 9px; top: 14px; width: 300px; display: inline-block; text-align: center; f">Emperor Cement Compound, Coastal Rd., Balabago, Jaro, Iloilo City</span>';
        $html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; right: 90px; font-size: 12px; top: 0px; width: 250px; display: inline-block; text-align: right; font-weight: bold;">' . $name . '</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 90px; font-size: 11px; top: 16px; width: 250px; display: inline-block; text-align: right">'.$reptitle.'</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 80px; top: 5px; width: 130px; display: inline-block; border-right: 1px solid #ccc; height: 20px;"></span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-size: 20px; top: 0px; width: 130px; display: inline-block; text-align: right; font-weight: bold;">' . $code . '</span>';

        $html .= '<hr style="border: 1px dashed #ccc; margin: 10px 0px;">';
        return $html;
    }
}



// #####################################################################
// #####################################################################
// ASSET MODULE
if(!function_exists('get_asset_info')) {
    function get_asset_info($id) {
        $ci = &get_instance();
        $data = array();


        $acctno = '';
        $name = '';
        $address = '';
        $spec = '';

        $dateissued = '0000-00-00';
        $issuedby = 'Unknown';

        $q = false;

        $number = '';
        $serial = '';
        $status = '<span class="label label-success">Available</span>';

        $qry = $ci->db->query("
                SELECT am.sysid,
                am.labels,
                am.serials,
                pib.descs,
                equiptype.desc,
                tp.`desc` AS ownership,
                am.`status` AS status
                FROM assets_main AS am 
                LEFT JOIN prime_types_parameter AS tp ON tp.sysid = am.types
                LEFT JOIN prime_brands as pib ON pib.sysid = am.brand
                LEFT JOIN prime_types_parameter AS equiptype ON equiptype.sysid = am.types
                WHERE am.sysid = $id
           ")->row();
        if($qry) {

            $number = $qry->labels;
            $serial = $qry->serials;
            $q = true;
            $qry_owner_hist = $ci->db->select('CAST(dateissued AS DATE) AS dateissued, ownerid, ownertype, createdby, status')
                ->from('assets_main_owner_history')
                ->where(array('assetid' => $qry->sysid))
                ->order_by('dateissued', 'desc')
                ->get()->row();
            if($qry_owner_hist) {
                $dateissued = $qry_owner_hist->dateissued;
                $issuedby = get_users_info($qry_owner_hist->createdby)->lastname;

                if($qry_owner_hist->ownertype == 3) {
                    $qry_main_check = $ci->db->select()
                        ->from('customer_accounts_main')
                        ->where(array('sysid' => $qry_owner_hist->ownerid))
                        ->get()->row();

                    if($qry_main_check) {
                        $acctno = $qry_main_check->servicenumber;
                        if($qry_main_check->types == 5) {
                            $qry_name = $ci->db->select()->from('customer_accounts_name_legacy')
                                ->where(array('sysid' => $qry_main_check->ownerid))
                                ->get()->row();
                            if($qry_name) {
                                $name = $qry_name->name;
                            }
                        }

                        $qry_address = $ci->db->select()->from('customer_accounts_address')
                            ->where(array('acctid' => $qry_owner_hist->ownerid))
                            ->get()->row();
                        if($qry_address) {
                            $address = $qry_address->addrspecific;
                        }
                    }

                    $qry_meter_data = $ci->db->select()
                        ->from('mis_meter_data')
                        ->where(array('mtrno' => $number, 'serial' => $serial))
                        ->get()->row();
                    if($qry_meter_data) {
                        $spec = 'Type: ' . $qry_meter_data->type
                            . ' | Brand: ' . $qry_meter_data->brand
                            . ' | ERC Seal: ' . $qry_meter_data->ercseal
                            . ' | PECO Seal: ' . $qry_meter_data->pecoseal
                            . ' | Ampere: ' . $qry_meter_data->amp
                            . ' | Volts: ' . $qry_meter_data->volts;
                    }else{
                        $spec = '<span class="text-danger">No Meter Data!';
                    }
                }

                if($qry_owner_hist->status == 1) {
                    $status = '<span class="label label-danger">Issued</span>';
                }
            }

            $getassetspecificatios = $ci->db->select("amsm.specid,amsm.specval , ptp.desc")
                ->from("assets_main_specifications_matrix as amsm")
                ->join("prime_types_parameter as ptp" , "ptp.sysid = amsm.specid")
                ->where(array("amsm.assetid" => $id , "amsm.status" => 1))
                ->get();
            if($getassetspecificatios->num_rows() > 0){
                foreach ($getassetspecificatios->result() as $specrow){
                    $data['assets_spec_data'][] = array(
                        'spec' => $specrow->desc,
                        'val' => $specrow->specval,
                        'typesid' => $specrow->specid
                    );
                }
            }

            $data['brand'] = $qry->descs;
            $data['types'] = $qry->desc;
        }


        $data['number'] = $number;
        $data['serial'] = $serial;
        $data['acctno'] = $acctno;
        $data['name'] = $name;
        $data['address'] = $address;
        $data['specs'] = $spec;
        $data['dateissued'] = $dateissued;
        $data['issuedby'] = $issuedby;
        $data['status'] = $status;
        $data['qry'] = $q;
        return (object)$data;
    }
}

if(!function_exists('get_asset_pic')) {
    function get_asset_pic($dataid) {

        $html = '';
        $dir = "./uploads/images/assets/".$dataid."/";

        $map = directory_map($dir);

        $img_main = '';
        $img_subs = '';

        if(file_exists(FCPATH . 'uploads/images/assets/'.$dataid)) {
            foreach ($map as $i => $image) {
                if (!is_array($image)) {

                    if(!file_exists(FCPATH . 'uploads/images/assets/'.$dataid.'/thumbnails/' . $image)) {
                        make_thumb(FCPATH . 'uploads\images\assets\\' . $dataid . '\\' . $image, FCPATH . 'uploads\images\assets\\' . $dataid . '\thumbnails\\' . $image, 100);
                    }

                    if ($i == 0) {
                        $img_main .= '<a href="' . base_url($dir) . $image . '" class="fancybox-button "><img class="" src="' . base_url($dir) . $image . '" /></a>';
                    } else {
                        $img_subs .= '<a href="' . base_url($dir) . $image . '" class="fancybox-button "><img class="sub" src="' . base_url($dir . 'thumbnails/') . $image . '" /></a>';
                    }
                }
            }
            $html .= '<div class="img-main">'.$img_main.'</div>';
            $html .= '<div class="img-subs">'.$img_subs.'</div>';
        }else{
            $html .= '<img style="width: 90%;" src="'.base_url('assets/global/img/not-available.png').'"/>';
        }
        return $html;
    }
}


if(!function_exists('create_item_category')) {
    function create_item_category($category = false) {
        $ci = &get_instance();
        if($category == false) {
            $category = $ci->input->post('category');
        }

        // CHECK CATEGORY EXISTS
        $qry_ = $ci->db->query("
                SELECT sysid FROM items_main_category 
                WHERE 
                (`codes` LIKE '%$category%') 
                OR (`names` LIKE '%$category%') 
                OR (`desc` LIKE '%$category%') 
                AND `status` = 1 LIMIT 0,10 
            ")->row();
        if($qry_) {
            $catid = $qry_->sysid;
        } else {
            $codes = get_acronym($category);
            $ins_arr = array(
                'codes' => $codes,
                'names' => $category,
                'desc' => $category,
            );
            $ci->db->insert('items_main_category', $ins_arr);
            $catid = $ci->db->insert_id();
        }

        return $catid;
    }
}




// #####################################################################
// #####################################################################
// TICKET LOGS
if(!function_exists('get_ticket_logs_details')) {
    function get_ticket_logs_details($id) {
        $ci = &get_instance();
        $qry = $ci->db->select('
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
            ->where(array('tdl.sysid' => $id))
            ->get()->row();

        return ($qry) ? $qry : false;
    }
}



// #####################################################################
// #####################################################################
// JOB ORDER MODULE

if(!function_exists('get_job_order_moduleid')) {
    function get_job_order_moduleid($joborders) {


        // 3090	JO         CMO
        // 3091	JO	       OIMR
        // 3092	JO	       TFDO
        // 3093	JO	       MRO
        //  322 APPJOBTYPE TNO


        $ci = &get_instance();



        switch ($joborders) {
            case 3090:
                $moduleid = 160; // CMO
                $trn_codes = 'CMO - New';
                break;
            case 3091:
                $moduleid = 161; // OIMR
                $trn_codes = 'OIMR - New';
                break;
            case 3092:
                $moduleid = 162; // TFDO
                $trn_codes = 'TFDO - New';
                break;
            case 322:
                $moduleid = 184; // TNO
                $trn_codes = 'TNO - New';
                break;
            default:
                $moduleid = 163; // MRO
                $trn_codes = 'MRO - New';
        }
        $qry_flowid = $ci->db->select('sysid')
            ->from('prime_transaction_flow_main')
            ->where(array('status' => 1, 'moduleid' => $moduleid))
            ->get()->row();
        $qry = ($qry_flowid) ? true : false;
        $flowid = ($qry_flowid) ? $qry_flowid->sysid: false;

        return (object) array('qry' => $qry, 'moduleid' => $moduleid, 'codes' => $trn_codes, 'flowid' => $flowid);
    }
}


// #####################################################################
// #####################################################################
// MRD MODULE
if(!function_exists('get_specific_reader_sched')) {
    function get_specific_reader_sched($schedid, $empuserid) {
        $qry_specific = get_instance()->db->query("
                                    SELECT 
                                    COUNT(DISTINCT(am.sysid)) AS cnt
                                    FROM reading_schedule_main AS sm 
                                    JOIN customer_accounts_main AS am ON sm.gdlbid = am.gdlb AND sm.status >= 1
                                    JOIN reading_schedule_specific AS ss ON ss.acctid = am.sysid AND am.status = 1
                                    WHERE sm.sysid = {$schedid} AND sm.status > 0 AND ss.userid = {$empuserid}
                                ")->row();
        if($qry_specific->cnt > 0) {
            return $qry_specific->cnt;
        }else{
            return false;
        }
    }
}


function flow_id_arr($codes) {
    $ci = &get_instance();
    $ret = array();

    if (is_numeric($codes)) {
        $ci->db->where('sysid',$codes);
    } else {
        $ci->db->where('codes',$codes);
    }

    if($codes) {
        $sql = $ci->db->select('sysid')
            ->from('prime_transaction_flow_main')
            ->where('status',1)
            ->get();
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $ret[] = $row->sysid;
            }
        }
    }
    return $ret;
}

function getContrastColor($hexColor) {
    // hexColor RGB
    $R1 = hexdec(substr($hexColor, 1, 2));
    $G1 = hexdec(substr($hexColor, 3, 2));
    $B1 = hexdec(substr($hexColor, 5, 2));

    // Black RGB
    $blackColor = "#000000";
    $R2BlackColor = hexdec(substr($blackColor, 1, 2));
    $G2BlackColor = hexdec(substr($blackColor, 3, 2));
    $B2BlackColor = hexdec(substr($blackColor, 5, 2));

    // Calc contrast ratio
    $L1 = 0.2126 * pow($R1 / 255, 2.2) +
        0.7152 * pow($G1 / 255, 2.2) +
        0.0722 * pow($B1 / 255, 2.2);

    $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
        0.7152 * pow($G2BlackColor / 255, 2.2) +
        0.0722 * pow($B2BlackColor / 255, 2.2);

    $contrastRatio = 0;
    if ($L1 > $L2) {
        $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
    } else {
        $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
    }

    // If contrast is more than 5, return black color
    if ($contrastRatio > 5) {
        return '#000000';
    } else {
        // if not, return white color.
        return '#FFFFFF';
    }
}
if (!function_exists('amountInWords')) {
    function amountInWords($num,$currency = false){
        $decones = array(
            '00' => " ",
            '01' => "One",
            '02' => "Two",
            '03' => "Three",
            '04' => "Four",
            '05' => "Five",
            '06' => "Six",
            '07' => "Seven",
            '08' => "Eight",
            '09' => "Nine",
            10 => "Ten",
            11 => "Eleven",
            12 => "Twelve",
            13 => "Thirteen",
            14 => "Fourteen",
            15 => "Fifteen",
            16 => "Sixteen",
            17 => "Seventeen",
            18 => "Eighteen",
            19 => "Nineteen"
        );
        $ones = array(
            0 => " ",
            1 => "One",
            2 => "Two",
            3 => "Three",
            4 => "Four",
            5 => "Five",
            6 => "Six",
            7 => "Seven",
            8 => "Eight",
            9 => "Nine",
            10 => "Ten",
            11 => "Eleven",
            12 => "Twelve",
            13 => "Thirteen",
            14 => "Fourteen",
            15 => "Fifteen",
            16 => "Sixteen",
            17 => "Seventeen",
            18 => "Eighteen",
            19 => "Nineteen"
        );
        $tens = array(
            0 => "",
            2 => "Twenty",
            3 => "Thirty",
            4 => "Forty",
            5 => "Fifty",
            6 => "Sixty",
            7 => "Seventy",
            8 => "Eighty",
            9 => "Ninety"
        );
        $hundreds = array(
            "Hundred",
            "Thousand",
            "Million",
            "Billion",
            "Trillion",
            "Quadrillion"
        ); //limit t quadrillion
        $num = number_format($num,2,".",",");
        $num_arr = explode(".",$num);
        $wholenum = $num_arr[0];
        $decnum = (int)$num_arr[1];
        $whole_arr = array_reverse(explode(",",$wholenum));
        krsort($whole_arr);
        $rettxt = "";
        foreach($whole_arr as $key => $i){
            if($i < 20){
                $rettxt .= $ones[(int)$i];
            }
            elseif($i < 100){
                $rettxt .= $tens[substr($i,(strlen($i) > 2 ? 1 : 0),1)];
                $rettxt .= " ".$ones[substr($i,(strlen($i) > 2 ? 2 : 1),1)];
            }
            else{
                $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
                if (substr($i, 1, 1) > 1) {
                    $rettxt .= " " . $tens[substr($i, 1, 1)];
                    $rettxt .= " " . $ones[substr($i, 2, 1)];
                } else {
                    $rettxt .= " " . $decones[substr($i, 1, 2)];
                }
            }
            if($key > 0){
                $rettxt .= " ".$hundreds[$key]." ";
            }

        }
        $rettxt = $rettxt.' '.(($currency) ? $currency.'s ' : ' ').'& '.$decnum.'/100 only';

        return $rettxt;
    }
}

if (!function_exists('textToImage')) {
    function textToImage($text, $outputFile = false,$rotate=false,$fontPath=false,$fontSize = 16,$padding = 10,$align='left') {
        if (!$fontPath) {
            $fontPath = FCPATH.'assets/global/plugins/fonts/arial.ttf';
        }

        // Normalize line breaks
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $lines = explode("\n", $text);

        $maxWidth = 0;
        $lineHeights = [];
        $lineWidths = [];
        $totalHeight = 0;

        // Measure each line
        foreach ($lines as $line) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $line);
            if (!$bbox) throw new RuntimeException("Failed to measure: \"$line\"");

            $width  = abs($bbox[2] - $bbox[0]);
            $height = abs($bbox[5] - $bbox[1]);

            $lineWidths[] = $width;
            $lineHeights[] = $height;
            $totalHeight += $height;

            if ($width > $maxWidth) $maxWidth = $width;
        }

        // Total dimensions
        $imgWidth = $maxWidth + $padding * 2;
        $imgHeight = $totalHeight + $padding * 2 + (count($lines) - 1) * 4;

        // Create image
        $image = imagecreatetruecolor($imgWidth, $imgHeight);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        // Draw text
        $y = $padding;
        foreach ($lines as $i => $line) {
            $lineWidth = $lineWidths[$i];
            $lineHeight = $lineHeights[$i];

            // Determine horizontal starting point based on alignment
            switch (strtolower($align)) {
                case 'center':
                    $x = ($imgWidth - $lineWidth) / 2;
                    break;
                case 'right':
                    $x = $imgWidth - $lineWidth - $padding;
                    break;
                default: // left
                    $x = $padding;
            }

            $y += $lineHeight;
            imagettftext($image, $fontSize, 0, (int)$x, (int)$y, $black, $fontPath, $line);
            $y += 4; // spacing
        }

        // Rotate 90° CCW
        if ($rotate) {
            $image = imagerotate($image, $rotate, $white);
        }

        if (!$outputFile) {
            // Create temp file path
            $tempFile = tempnam(sys_get_temp_dir(), 'rotated_');
            $tempFilePng = $tempFile . '.png';
            rename($tempFile, $tempFilePng); // add extension
            imagepng($image,$tempFilePng);
            $returnImg = $tempFilePng;
        } else {
            imagepng($image,$outputFile);
            $returnImg = $outputFile;
        }

        return '<img src="' . htmlspecialchars($returnImg) . '">';
    }
}


