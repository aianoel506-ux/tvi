<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if (!function_exists('get_user_employee_info')) {
    function get_user_employee_info($userid = false) {
        $ci = &get_instance();
        $userid = ($userid) ? $userid : user_id();
        $qry = $ci->db->select('
        u.sysid as userid ,
        em.sysid ,
        p.sysid as personid, 
        p.firstname , 
        p.lastname
        ')
            ->from('prime_system_users AS u')
            ->join('person AS p', 'p.sysid = u.personid')
            ->join('prime_employee_main AS em', 'em.personid = p.sysid')
            ->where('u.sysid', $userid)
            ->get()->row();
        return ($qry) ? $qry : false;
    }
}

if (!function_exists('get_employee_info')) {

    function get_employee_info($empid = false) {
        $ci = &get_instance();
        $qry = false;
        if($empid) {
            $qry = $ci->db->select('
                    em.sysid, 
                    p.sysid AS personid,
                    p.firstname, 
                    p.lastname,
                    p.middlename,
                    p.birthdate, 
                    pg.name, 
                    p.status, 
                    em.datestart,
                    em.dateend,
                    em.empid, 
                    em.`status` as empstatus, 
                    pam.addrspec, 
                    pc.names, 
                    ac.country, 
                    ac.nationality, 
                    ad.descriptions AS dist, 
                    pes.amt AS salary
                ')
                ->from('prime_employee_main AS em')
                ->join('person AS p', 'p.sysid = em.personid', 'left')
                ->join('person_address_matrix AS pam', 'p.sysid = pam.personid', 'left')
                ->join('prime_gender AS pg', 'p.gender = pg.sysid', 'left')
                ->join('prime_districts AS pd', 'pd.sysid = pam.addrdist', 'left')
                ->join('prime_city AS pc', 'pc.sysid = pam.addrcity', 'left')
                ->join('address_districts AS ad', 'ad.sysid = pam.addrdist', 'left')
                ->join('address_country AS ac', 'ac.sysid = pam.addrcountry', 'left')
                ->join('prime_employee_costcenter as pec', 'pec.empid = em.sysid', 'left')
                ->join('prime_costcenter_main as pcm', 'pcm.sysid = pec.ccid AND pcm.status = 1 AND pcm.type = 1', 'left')
                ->join('prime_employee_salary as pes', 'pes.empid = em.sysid AND pes.status = 1', 'left')
                ->where(array('em.sysid' => $empid ))
                ->get()->row();
        }
        if($qry) {

            $qry_get_empcc = $ci->db->query("
                SELECT
                    p.ccid AS deptid,
                    pcm.codes AS deptcode,
                    pcm.NAMES AS deptname,
                    pcm.DESC AS deptdesc 
                FROM
                    prime_employee_costcenter AS p
                    JOIN prime_costcenter_main AS pcm ON pcm.sysid = p.ccid 
                WHERE
                    p.empid = {$qry->sysid} 
                    AND p.status = 1 
                    AND p.type = 1
                ORDER BY
                    p.datecreated DESC
            ")->row();

            $qry_get_userid = $ci->db->query("SELECT sysid, username FROM prime_system_users WHERE personid = {$qry->personid} AND status = 1")->row();
            $userid = ($qry_get_userid) ? $qry_get_userid->sysid : 0;
            $username = ($qry_get_userid) ? $qry_get_userid->username : 0;

            $emp_ccid = 0;
            $emp_code = 'N/A';
            $emp_desc = 'N/A';
            $emp_name = 'N/A';

            if($qry_get_empcc) {
                $emp_ccid = $qry_get_empcc->deptid;
                $emp_code = $qry_get_empcc->deptcode;
                $emp_name = $qry_get_empcc->deptname;
                $emp_desc = $qry_get_empcc->deptdesc;
            }

            $qry_get_comp_email = $ci->db->select('contactstring AS email')
                ->from('person_contact_matrix')
                ->where(array('personid' => $qry->personid, 'status' => 1, 'types' => 1057))
                ->get()->row();
            $compemail = ($qry_get_comp_email) ? $qry_get_comp_email->email : false;


            $qry_get_position = $ci->db->select('tp.names, tp.desc')
                ->from('prime_employee_main_positions AS emp')
                ->join('prime_types_parameter AS tp', 'tp.sysid = emp.position_id AND emp.status = 1')
                ->where(array('emp.emp_id' => $qry->sysid))
                ->get()->row();
            $position = ($qry_get_position) ? $qry_get_position->names : false;
            $positiondesc = ($qry_get_position) ? $qry_get_position->desc : false;


            $res = array(
                'qry' => true,
                'sysid' => $qry->sysid,
                'personid' => $qry->personid,
                'userid' => $userid,
                'username' => $username,
                'firstname' => $qry->firstname,
                'lastname' => $qry->lastname,
                'middlename' => $qry->middlename,
                'birthdate' => $qry->birthdate,
                'name' => $qry->name,
                'status' => $qry->status,
                'datestart' => $qry->datestart,
                'dateend' => $qry->dateend,
                'empid' => $qry->empid,
                'addrspec' => $qry->addrspec,
                'names' => $qry->names,
                'country' => $qry->country,
                'nationality' => $qry->nationality,
                'dist' => $qry->dist,
                'deptid' => $emp_ccid,
                'deptcode' => $emp_code,
                'deptname' => $emp_name,
                'deptdesc' => $emp_desc,
                'salary' => $qry->salary,
                'empstatus' => $qry->empstatus,
                'emailcomp' => $compemail,
                'position' => $position,
                'positiondesc' => $positiondesc
            );
        }
        $res['qry'] = $qry;
        return (object) $res;
    }

}
if (!function_exists('get_person_userinfo')) {

    function get_person_userinfo($personid = false) {
        $ci = &get_instance();
        $qry = false;
        if($personid) {
            $qry = $ci->db->select('
                    sysid
                ')
                ->from('prime_system_users')
                ->where('personid', $personid)
                ->get()->row();
        }
        return ($qry) ? $qry : false;
    }

}

function check_emp_schedule2($empid, $date) {
    $ci = &get_instance();
    $query = $ci->db->query('select * from prime_employee_main_schedule_matrix where empid = "' . $empid . '" and "' . $date . '" between schedstart and schedend ');
    if ($query->num_rows() > 0) {
        return true;
    } else {
        return false;
    }
}

function check_emp_logtime_schedule($empid, $date) {
    $ci = &get_instance();
    $query = $ci->db->query('select ws.am_start, ws.am_end, ws.pm_start, ws.pm_end from prime_employee_main_schedule_matrix sm
left join prime_employee_main_workshift ws
on sm.workshiftid = ws.sysid where sm.empid = "' . $empid . '" and "' . $date . '" between sm.schedstart and sm.schedend ');
    if ($query->num_rows() > 0) {
        $qry = $query->row();
        $am_start = $qry->am_start;
        $am_end = $qry->am_end;
        $pm_start = $qry->pm_start;
        $pm_end = $qry->pm_end;
        return (object) array('am_start' => $am_start, 'am_end' => $am_end, 'pm_start' => $pm_start, 'pm_end' => $pm_end);
    } else {
        return false;
    }
}

if (!function_exists('get_logtime')) {

    function get_logtime($logdate, $empid, $logtype) {
        $ci = &get_instance();
        $amin = '';
        $amout = '';
        $pmin = '';
        $pmout = '';
        //add query here to chech attendance independent in luckys code
        $data = array();

        $query_log = $ci->db->select('emp.empid, bio.bioid')
            ->from('prime_employee_main AS emp')
            ->join('prime_employee_bioid AS bio', 'bio.empid = emp.sysid')
            ->where('emp.status', 1)
            ->where('emp.sysid', $empid)
            ->group_by('emp.empid, bio.bioid')
            ->get();
        $num_rows = $query_log->num_rows();
        if ($num_rows > 0) {
            foreach ($query_log->result() as $row) {

                $time_logs_arr = array();

                $qry_timelogs = $ci->db->select()
                    ->from('prime_employee_attendance_timelogs')
                    ->where(array('logdate' => $logdate, 'bioid' => $row->bioid))->get();
                $timelog_num = $qry_timelogs->num_rows();
                if ($timelog_num > 0) {
                    foreach ($qry_timelogs->result() as $trow) {
                        $time_logs_arr[] = $trow->logtime;
                    }

                    $amin = ($timelog_num > 0) ? $time_logs_arr[0] : '';
                    $amout = ($timelog_num > 1) ? $time_logs_arr[1] : '';
                    $pmin = ($timelog_num > 2) ? $time_logs_arr[2] : '';
                    $pmout = ($timelog_num > 3) ? $time_logs_arr[3] : '';
                }
            }
        }
        // end query here

        $get_emp_info = $ci->db->select('ws.workshift_id')
            ->from('prime_employee_main e')
            ->join('prime_employee_main_workshift_matrix ws', 'ws.empid = e.sysid', 'left')
            ->where('e.sysid', $empid)->get()->row();
        $get_sched_workshift = $ci->db->select('ws.workshiftid')
            ->from('prime_employee_main e')
            ->join('prime_employee_main_schedule_matrix ws', 'ws.empid = e.sysid', 'left')
            ->where('e.sysid', $empid)->get()->row();
        //check in schedule table
        $check = check_emp_schedule2($empid, $logdate);
        if ($check) {
            $get_employe_sched = $ci->db->select('smx.logtime, smx.intervalmin, smx.intervalplus, smx.graceperiod')
                ->from('prime_employee_main_schedule_matrix AS sm')
                ->join('prime_employee_schedule_matrix AS smx', 'smx.schedid = sm.workshiftid', 'left')
                ->where(array('smx.logtype' => $logtype, 'smx.schedid' => $get_sched_workshift->workshiftid))
                ->get()->row();
        }
        //end check schedule table
        else {
            $get_employe_sched = $ci->db->select('smx.logtime, smx.intervalmin, smx.intervalplus, smx.graceperiod')
                ->from('prime_employee_schedule_main AS sm')
                ->join('prime_employee_schedule_matrix AS smx', 'smx.schedid = sm.sysid', 'left')
                ->where(array('smx.logtype' => $logtype, 'sm.sysid' => isset($get_emp_info->workshift_id)))
                ->get()->row();
        }


        if ($get_employe_sched) {
            if ($get_employe_sched->intervalmin > 0) {
                $qry = $ci->db->select('tl.logtime')
                    ->from('prime_employee_attendance_timelogs AS tl')
                    ->join('prime_employee_bioid AS bid', 'bid.bioid = tl.bioid', 'left')
                    ->join('prime_employee_main AS e', 'e.sysid = bid.empid', 'left')
                    // commented previosly code below, idk why - marlon 11.9.2017
                    ->order_by('tl.logdate', 'DESC')
                    ->limit(150)
                    // end comment
                    ->where('bid.empid', $empid)
                    ->where('CAST(tl.logdate AS DATE) = ', $logdate)
                    ->where("CAST(tl.logtime AS TIME) >= CAST('" . $get_employe_sched->logtime . "' AS TIME) - INTERVAL " . $get_employe_sched->intervalmin . " MINUTE")
                    ->where("CAST(tl.logtime AS TIME) <= CAST('" . $get_employe_sched->logtime . "' AS TIME) + INTERVAL " . $get_employe_sched->intervalplus . " MINUTE")
                    // commented previosly code below, replace code above  - marlon 11.9.2017
                    // ->where("CAST(tl.logtime AS TIME) >= CAST('" . $get_employe_sched->logtime . "' AS TIME)" . $get_employe_sched->intervalmin . " MINUTE")
                    //->where("CAST(tl.logtime AS TIME) <= CAST('" . $get_employe_sched->logtime . "' AS TIME)" . $get_employe_sched->intervalplus . " MINUTE")
                    // end comment
                    ->get()->row();
                if ($qry) {
                    $graceperiod = $get_employe_sched->graceperiod;
                    $logtime = $qry->logtime;
                    $schedin = $get_employe_sched->logtime;
                    $schedout = $get_employe_sched->logtime;
                } else {
                    $logtime = '';
                    $graceperiod = '';
                    $schedin = '';
                    $schedout = '';
                }
            } else {
                $logtime = 'ER: Int.';
                $graceperiod = '0';
                $schedin = '';
                $schedout = '';
            }
        } else {
            $logtime = '';
            $graceperiod = '';
            $schedin = '';
            $schedout = '';
        }

        return (object) array('amin' => $amin, 'amout' => $amout, 'pmin' => $pmin, 'pmout' => $pmout, 'logtime' => $logtime, 'graceperiod' => $graceperiod, 'schedin' => $schedin, 'schedout' => $schedout);
    }

}

if (!function_exists('check_log_late')) {

    function check_log_late($timein, $schedin, $graceperiod) {
        if ($timein > 0) {
            if ($timein <= $schedin) {
                return '0:00';
            } else {
                $endTime = strtotime(strtotime($schedin));
                $time_in_specific = date('H:i:s', $endTime);

                // CHECK IF PASSED DATE OR NOT
                $time_in_specific_1 = new DateTime($time_in_specific);
                $time_in_actual = new DateTime($timein);
                if ($time_in_actual > $time_in_specific_1) {
                    $time_in_interval = $time_in_actual->diff($time_in_specific_1);
                    $late_arr = (object) array('late' => true, 'timediff' => $time_in_interval->format('%i'));
                } else {
                    $time_in_interval = $time_in_actual->diff($time_in_specific_1);
                    $late_arr = (object) array('late' => false, 'timediff' => $time_in_interval->format('%i'));
                }

                $late_min = $late_arr->timediff;
                return ($late_arr->late) ? $late_min : 0;
            }
        } else {
            return '0:00';
        }
    }

}

if (!function_exists('find_closest')) {

    function find_closest($array, $date) {
        //$count = 0;
        foreach ($array as $day) {
            //$interval[$count] = abs(strtotime($date) - strtotime($day));
            $interval[] = abs(strtotime($date) - strtotime($day));
            //$count++;
        }

        asort($interval);
        $closest = key($interval);

        echo $array[$closest];
    }

}
if (!function_exists('convertToHoursMins')) {

    function convertToHoursMins($time, $format = '%02d:%02d') {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

}

if (!function_exists('fake_btn')) {

    function fake_btn() {
        return '
      <div class = "btn-broup">
      <button type = "button" class = "btn btn-warning btn-xs"><i class = "fa fa-pencil"></i></button>
      <button type = "button" class = "btn btn-primary btn-xs"><i class = "fa fa-search"></i></button>
      </div>
      ';
    }

}

if (!function_exists('select_department')) {

    function select_department() {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM prime_costcenter_main");
        return ($query) ? $query->result() : FALSE;
    }

}

if (!function_exists('select_position')) {

    function select_position() {
        $ci = & get_instance();
        $query = $ci->db->query("select * from prime_types_parameter 
                                          where codes = 'EMPOST'");
        return ($query) ? $query->result() : FALSE;
    }

}

if (!function_exists('select_payclass')) {

    function select_payclass() {
        $ci = & get_instance();
        $query = $ci->db->query("select * from prime_types_parameter where codes = 'EMPAYCLASS'
                                          ");
        return ($query) ? $query->result() : FALSE;
    }

}
if (!function_exists('select_country')) {

    function select_country() {
        $ci = & get_instance();
        $query = $ci->db->query("select * from address_country
                                          ");
        return ($query) ? $query->result() : FALSE;
    }

}
if (!function_exists('select_job_category')) {

    function select_job_category() {
        $ci = & get_instance();
        $query = $ci->db->query("select * from prime_types_parameter where codes = 'EMPJOBCAT'
                                          ");
        return ($query) ? $query->result() : FALSE;
    }

}

if (!function_exists('compute_net_salary')) {

    function compute_net_salary($empid) {
        $ci = & get_instance();
        $base_salary = $ci->db->query("select pem.sysid, esalary.salary from person as p
                            left join prime_employee_main as pem on pem.personid = p.sysid
                            left join prime_employee_salary as esalary on esalary.emp_id = pem.sysid
                                          ")->row();
        if ($base_salary) {
            $base_salary_amount15 = bsdiv($base_salary->salary, 2, 2);
        } else {
            $base_salary_amount15 = 0;
        }
        $sss = $ci->db->query("select pem.sysid, sss.sss_employee_share from person as p
                              left join prime_employee_main as pem on pem.personid = p.sysid
                               left join prime_employee_salary as esalary on esalary.emp_id = pem.sysid
                                left join prime_employee_sss as sss on sss.salary_lower_range<= esalary.salary and esalary.salary <=sss.salary_higher_range
                            ")->row();
        $philhealth = $ci->db->query("select pem.sysid, philheath.employee_share from person as p
                        left join prime_employee_main as pem on pem.personid = p.sysid
                        left join prime_employee_salary as esalary on esalary.emp_id = pem.sysid
                        left join prime_employee_philhealth as philheath on esalary.salary > philheath.lower_salary_range and esalary.salary < philheath.higher_salary_range
                            ")->row();
        $pagibig = $ci->db->query("select pem.sysid, philheath.employee_share from person as p
                        left join prime_employee_main as pem on pem.personid = p.sysid
                        left join prime_employee_salary as esalary on esalary.emp_id = pem.sysid
                        left join prime_employee_philhealth as philheath on esalary.salary > philheath.lower_salary_range and esalary.salary < philheath.higher_salary_range
                            ")->row();
        $net_income = ($base_salary->salary) - (($sss->sss_employee_share) + ($philhealth->employee_share) + ($pagibig->employee_share));
        //TODO add deduction for absences and lates
        return ($query) ? $query->result() : FALSE;
    }

}

if (!function_exists('select_emp_position')) {

    function select_emp_position($id) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT parameter.sysid , parameter.names FROM prime_types_parameter as parameter
left join prime_employee_main_positions as positions on parameter.sysid = positions.position_id where positions.emp_id = $id AND positions.status = 1 group by  parameter.sysid ,parameter.names,positions.emp_id")->row();
        return ($query) ? $query : FALSE;
    }

}

if (!function_exists('select_emp_payclass')) {

    function select_emp_payclass($id) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT
                parameter.names,
                parameter.sysid as payclassid
            FROM
                prime_types_parameter AS parameter
                LEFT JOIN prime_employee_main_payclass AS payclass ON parameter.sysid = payclass.payclass_id 
            WHERE
                payclass.emp_id = $id 
            GROUP BY
                parameter.names,
                payclass.emp_id,
                parameter.sysid")->row();
        return ($query) ? $query : FALSE;
    }

}

if (!function_exists('select_emp_jobcat')) {

    function select_emp_jobcat($id) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT parameter.names FROM prime_types_parameter as parameter
left join prime_employee_main_job_category as jobcat on parameter.sysid = jobcat.jobcatid where jobcat.empid = $id  group by parameter.names, jobcat.empid
                                          ")->row();
        return ($query) ? $query : FALSE;
    }

}

if (!function_exists('get_emp_datestart')) {

    function get_emp_datestart($id) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT datestart FROM prime_employee_main WHERE sysid = $id");

        return ((object) $query) ? (object) $query->row() : FALSE;
    }

}

function star_multiplier($cnt, $years) {
    $title_num = 'less than a year';
    if($years > 1) {
        $title_num = $years . ' years';
    }else{
        if($years==1) {
            $title_num = $years . ' year';
        }
    }

    $html = '';
    $html .= '<a href="javascritp:;" title="'.$title_num.' in service" data-toggle="tooltips">';
    for ($i = 1; $i <= $cnt; $i++) {
        $html .= '<i class = "fa fa-star text-warning"></i>';
    }
    $html .= '</a>';
    return $html;
}

function emp_badge_yr_service($yr) {
    switch ($yr) {
        case $yr >= 3 && $yr < 5:
            $badge = star_multiplier(1, $yr);
            break;
        case $yr >= 5 && $yr < 10:
            $badge = star_multiplier(2, $yr);
            break;
        case $yr >= 10 && $yr < 15:
            $badge = star_multiplier(3, $yr);
            break;
        case $yr >= 15 && $yr < 20:
            $badge = star_multiplier(4, $yr);
            break;
        case $yr >= 20:
            $badge = star_multiplier(5, $yr);
            break;
        default:
            $badge = '<i class = "fa fa-star text-muted"></i>';
    }
    return $badge;
}

if (!function_exists('get_emp_duration')) {

    function get_emp_duration($id) {
        $ci = & get_instance();
        $datestarted = $ci->db->query("SELECT e.datestart FROM prime_employee_main as e left join person as p on p.sysid = e.personid where e.sysid = $id")->row();
        $then = $datestarted->datestart;
        $datetime1 = new DateTime($then);
        $datetime2 = new DateTime(sql_time()->DATETIME);
        $interval = $datetime1->diff($datetime2);

        //
        $date_spent_yr = $interval->format('%Y');
        $date_spent_mt = $interval->format('%M');
        $date_spent_d = $interval->format('%d');
        $date_spent_h = $interval->format('%H');
        $date_spent_mn = $interval->format('%I');
        $date_spent_s = $interval->format('%S');

        if ($date_spent_yr > 0) {

            //$badge = emp_badge_yr_service($date_spent_yr);

            $year_spent = $date_spent_yr . ' years and ';
        } else {
            $year_spent = '';
        }

        $days_sentence = time_to_word($date_spent_mt, $date_spent_d, $date_spent_h, $date_spent_mn, $date_spent_s);
        $datespent = ($days_sentence != '0') ? $days_sentence : $days_sentence;
        return (object) array("timespent" => $year_spent . ' ' . $datespent, "numyear" => $date_spent_yr);
        //$result = array('years' => $date_spent_yr, 'months' => $date_spent_mt, 'days' => $date_spent_d, 'hours' => $date_spent_h, 'minutes' => $diff->i, 'seconds' => $diff->s);
        //return (object) $result;
    }

}

if (!function_exists('get_emp_department')) {

    function get_emp_department($id) {
        $ci = & get_instance();
        $query = $ci->db->query("select cmain.desc 
                from prime_employee_main as e
                left join prime_employee_costcenter as costcenter on costcenter.empid = e.sysid
                left join prime_costcenter_main as cmain on cmain.sysid = costcenter.ccid 
                where e.sysid = $id AND costcenter.type = 1 AND costcenter.status = 1")->row();

        return (object) $query;
    }

}
if (!function_exists('get_deptartment_info')) {
    function get_deptartment_info($id) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM prime_costcenter_main WHERE sysid = $id")->row();
        return $query;
    }
}
if (!function_exists('get_emp_basic_salary')) {

    function get_emp_basic_salary($id) {
        $data=array();
        $ci = & get_instance();
        $emp_position_id = isset(select_emp_position($id)->sysid) ? select_emp_position($id)->sysid : 0;
        $payclass = isset(select_emp_payclass($id)->names) ? select_emp_payclass($id)->names : 'Unassigned Payclass';
        $payclassid = isset(select_emp_payclass($id)->payclassid) ? select_emp_payclass($id)->payclassid : 0;
        $query = $ci->db->select("amt")->from("prime_employee_salary")->where(array("empid" => $id , "status"=> 1))->get()->row();

        $divider = 249;
        //ACCORDING TO SIR JONATHAN IF THE POSITION ARE TS - 173 / SB - 174 / MR - 164 THE DIVISOR IS 301 ELSE 249
        $onedayoffposition = array(173 , 174 , 164);
        if (in_array($emp_position_id, $onedayoffposition) && in_array($payclassid,array(3077,3078))) {
            $divider = 301;
        }

        $amt = 0;
        $daily = 0;
        $hourly = 0;

        if ($query) {
            $amt = $query->amt;
            $daily = ($amt * 12) / $divider;
            $hourly = $daily/8;
        }


        $data['amt'] = $amt;
        $data['daily'] = number_format($daily,2,'.','');
        $data['hourly'] = number_format($hourly,2,'.','');
        return (object)$data;
    }

}
//test for server lag
if (!function_exists('get_emp_timelogs')) {

    function get_emp_timelogs($id) {
        $ci = & get_instance();
        $query = $ci->db->query("select att.logdate, att.logtime from prime_employee_attendance_timelogs as att 
left join prime_employee_bioid as id on id.bioid = att.bioid
left join prime_employee_main as e on e.sysid = id.empid where e.sysid= $id and e.sysid != 1")->result();

        return $query;
    }

}
if (!function_exists('get_emp_timelogs_daily')) {

    function get_emp_timelogs_daily($id) {
        $ci = & get_instance();
        $query = $ci->db->query("select att.logdate, att.logtime from prime_employee_attendance_timelogs as att 
left join prime_employee_bioid as id on id.bioid = att.bioid
left join prime_employee_main as e on e.sysid = id.empid where e.sysid = $id order by att.logdate desc, att.logtime desc limit 1")->row();


        return (object) $query;
    }

}
if (!function_exists('get_emp_workshift')) {
    function get_emp_workshift($id) {
        $ci = & get_instance();
        $query = $ci->db->query("select 
ws.sysid ,
ws.desc,
ws.codes,
ws.logcnt,
ws.logtype,
ws.status
from prime_employee_main as e
left join prime_employee_main_workshift_matrix as matrix on matrix.empid = e.sysid
left join prime_employee_main_workshift as ws on ws.sysid = matrix.workshift_id where e.sysid = $id group by 
ws.sysid ,
ws.desc,
ws.codes,
ws.logcnt,
ws.logtype,
ws.status")->row();
        return (object) $query;
    }

}

if (!function_exists('get_employee_approval')) {

    function get_employee_approval($empid) {
        $ci = &get_instance();

        $qry_empcc = $ci->db->select('ec.ccid, cgh.empid, p.lastname, p.firstname')
            ->from('prime_employee_costcenter AS ec')
            ->join('prime_costcenter_group_matrix AS cgm', 'cgm.ccid = ec.ccid')
            ->join('prime_costcenter_group_head AS cgh', 'cgm.groupid = cgh.groupid')
            ->join('prime_costcenter_group AS cg', 'cg.sysid = cgh.groupid')
            ->join('prime_employee_main AS em', 'em.sysid = cgh.empid')
            ->join('person AS p', 'p.sysid = em.personid')
            ->where(array('ec.empid' => $empid, 'ec.status' => 1))
            ->get()->row();

        return ($qry_empcc) ? $qry_empcc : false;
    }
}
//GET EMPLOYEE HEAD
if (!function_exists('get_employee_dephead')) {
    function get_employee_dephead($empid) {
        $ci = &get_instance();

        $qry_empcc = $ci->db->select('ec.ccid, ech.empid, p.lastname, p.firstname')
            ->from('prime_employee_costcenter AS ec')
            ->join('prime_costcenter_head AS ech', 'ech.ccid = ec.ccid')
            ->join('prime_employee_main AS em', 'em.sysid = ech.empid')
            ->join('person AS p', 'p.sysid = em.personid')
            ->where(array('ec.empid' => $empid, 'ec.status' => 1))
            ->get()->row();

        return ($qry_empcc) ? $qry_empcc : false;
    }

}
//GET PICTURE
function emp_pic_draw($id, $height, $width) {
    $emp_pic = '';

    $pic_info = get_owner_pic($id, 'person');
    $emp_pic .= '<a class="popovers" data-trigger="hover" data-title="" data-content="<img src=\'' . $pic_info . '\' height=\'130px\' />" href="javascript:;">';
    $emp_pic .= '<img src="' . $pic_info . '" height="' . $height . '" width="' . $width . '" style="margin-right: 10px;" />';
    $emp_pic .= '</a>';
    return $emp_pic;
}
//GET SALARY OF EMPLOYEE
function get_employee_salary($empid) {
    // QUERY EMPLOYEE BASIC SALARY STATUS ONE FROM SALARY TABLE HERE
    $ci = &get_instance();
    $qry_empsalary = $ci->db->select('amt')
        ->from('prime_employee_salary')
        ->where(array('empid' => $empid, 'status' => 1))
        ->get()->row();

    return ($qry_empsalary) ? $qry_empsalary->amt : false;
}
//GET EMPLOYEE TRANSACTIONS
function get_employee_transactions($empid, $month, $year, $paytype, $paytypepopover, $payclass = false , $viewtype) {

    $data = array();
    $ci = &get_instance();
    // QUERY TRANSACTIONS
    $total_loans = 0;
    $total_bonus = 0;
    $total_holiday = 0;
    $total_net_1 = 0;
    $total_net_2 = 0;
    $total_loans_dist = 0;
    $total_loans_spec = 0;
    $transactions = array();
    $loansarr = array();
    $earningarr = array();
    $otherdeductarr = array();
    $total_raw_amount = 0;
    $total_taxable_amount = 0;
    $total_nontaxable_amount = 0;
    $total_others_add = 0;
    $total_others_sub = 0;

    $taxwithoutcapping = 0;

    $confi_1st_half = 0;
    $confi_2nd_half = 0;
    $confi_distributed = 0;

    $totalsssloan = 0;
    $totalpecewaloan = 0;
    $totalcooploan = 0;
    $totalpagibigadd = 0;
    $totalotherdedn = 0;
    $totalelectricbill = 0;
    $totalmemins = 0;
    $totallwop = 0;
    $totalcola = 0;
    $totaltransallw = 0;
    $totalrice = 0;
    $totalholiday = 0;
    $nightdiff = 0;
    $otwithholiday = 0;
    $otweekend = 0;
    $otweekday = 0;
    $totalhmoded = 0;
    $totalhdmfloan = 0;
    $totaldeda = 0;
    $totalactingallw = 0;
    $totalotheradd = 0;

    $add_trans_variable = array();


    $ndot8hrs = 0;

    if ($month && $year) {
        if($payclass == 128 || $payclass == 3077 || $payclass == 3078){
            $ci->db->where(array("pt.paytype" => $paytype));
        }
        // EARNINGS AND DEDUCTIONS AND LOANS ENTRY
        $qry_trans = $ci->db->select('pt.amt, pt.typesid, pt.insertamount ,  pm.codes, pm.functions, pm.effects, pm.notax, pm.capping, pt.payspec,pt.paytype, tp.names')
            ->from('payroll_transactions AS pt')
            ->join('prime_types_parameter AS tp', 'tp.sysid = pt.typesid', 'left')
            ->join('payroll_matrix AS pm', 'pm.typesid = tp.sysid', 'left')
            ->where(array('pt.empid' => $empid, 'pt.months' => $month, 'pt.years' => $year,"pt.status !=" => 302))
            ->where_not_in("pt.status",array(0, 302))
            ->order_by('pm.codes')
            ->get();

        $qry_trans_row = $qry_trans->num_rows();
        if($qry_trans_row>0) {
            foreach($qry_trans->result() as $row) {

                if($row->typesid == 254){
                    $totalpecewaloan = $row->amt;
                }else if($row->typesid == 255){
                    $totalcooploan = $row->amt;
                }else if($row->typesid == 261){
                    $totalotherdedn = $row->amt;
                }else if($row->typesid == 256){
                    $totalelectricbill = $row->amt;
                }else if($row->typesid == 3009){
                    $totalmemins = $row->amt;
                }else if($row->typesid == 262){
                    $totallwop = $row->amt;
                }else if($row->typesid == 263){
                    $totalholiday = $row->amt;
                }else if($row->typesid == 358){
                    $nightdiff = $row->amt;
                    $ndot8hrs = $row->insertamount;
                }else if($row->typesid == 3010){
                    $otwithholiday = $row->amt;
                }else if($row->typesid == 1082){
                    $otweekend = $row->amt;
                }else if($row->typesid == 359){
                    $otweekday = $row->amt;
                }else if($row->typesid == 360){
                    $totalactingallw = $row->amt;
                }else if($row->typesid == 266){
                    $totalotheradd = $row->amt;
                }

                /*else if($row->typesid == 257){
                    $totalsssloan = $row->amt;
                }else if($row->typesid == 258){
                    $totalhdmfloan = $row->amt;
                }else if($row->typesid == 259){
                    $totalpagibigadd = $row->amt;
                }else if($row->typesid == 260){
                    $totalhmoded = $row->amt;
                }else if($row->typesid == 363){
                    $totaldeda = $row->amt;
                }*/

                $raw_amt = $row->amt;
                $total_raw_amount += $raw_amt;


                if($payclass != 128){
                    if($row->payspec == 1 && $row->paytype == 1){
                        //Add to first half
                        $confi_1st_half += $raw_amt;
                    }else if($row->payspec == 2 && $row->paytype == 2){
                        //Add to second half
                        $confi_2nd_half += $raw_amt;
                    }else if($row->payspec == 0 && $row->paytype == 0){
                        //Add to both
                        $confi_distributed += $raw_amt;
                    }
                }



                if($payclass == 128  || $payclass == 3077 || $payclass == 3078){
                    if($row->paytype == 0){
                        $raw_amt = $raw_amt/ 2;
                    }
                }


                //if($paytype == $row->payspec || $row->paytype == 0){
                $transactions[] = array(
                    'amt' => $raw_amt,
                    'type' => $row->typesid,
                    'codes' => $row->codes,
                    'functions' => $row->functions,
                    'effects' => $row->effects,
                    'notax' => $row->notax,
                    'capping' => $row->capping,
                    'payspec' => $row->payspec,
                    'names' => $row->names
                );

                if($row->notax == 0){
                    $total_taxable_amount += $raw_amt;
                    if($row->capping == 0){
                        $taxwithoutcapping += $raw_amt;
                    }
                }else{
                    $total_nontaxable_amount += $raw_amt;
                }

                if($row->functions == 0 && $row->effects == 0){
                    $total_others_sub += $raw_amt;
                    $otherdeductarr[] = array(
                        'amt' => $raw_amt,
                        'type' => $row->typesid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $row->payspec,
                        'names' => $row->names
                    );
                }
                if($row->functions == 1 && $row->effects == 1){
                    $total_others_add+=$row->amt;
                    $earningarr[] = array(
                        'amt' => $raw_amt,
                        'type' => $row->typesid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $row->payspec,
                        'names' => $row->names
                    );

                }

                if($row->codes == 'loans'){
                    $total_loans += $raw_amt;
                    $loansarr[] = array(
                        'amt' => $raw_amt,
                        'type' => $row->typesid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $row->payspec,
                        'names' => $row->names
                    );
                }

                if($row->codes == 'holiday'){
                    $total_holiday+= $raw_amt;
                }
                //}
            }
        }

        //check for fix amount
        //Example COLA , RICE

        $checkfixamount =  $ci->db->select("pfa.typesid , pfa.amt , pe.codes , pe.functions , pe.effects , pe.notax , pe.capping , tp.names")->from("payroll_fix_amt as pfa")
            ->join("payroll_matrix as pe" , "pe.typesid = pfa.typesid" , "left")
            ->join('prime_types_parameter AS tp', 'tp.sysid = pfa.typesid', 'left')
            ->where(array("pfa.empid" => $empid , "pfa.status" => 1))->get();



        if($checkfixamount->num_rows() > 0){
            foreach ($checkfixamount->result() as $row){
                if($row->typesid == 259){
                    $totalpagibigadd = $row->amt;
                }else if($row->typesid == 251){
                    $totalcola = $row->amt;
                }else if($row->typesid == 252){
                    $totaltransallw = $row->amt;
                }else if($row->typesid == 253){
                    $totalrice = $row->amt;
                }else if($row->typesid == 360){
                    $totalactingallw = $row->amt;
                }
                $transactions[] = array(
                    'amt' => $row->amt,
                    'type' => $row->typesid,
                    'codes' => $row->codes,
                    'functions' => $row->functions,
                    'effects' => $row->effects,
                    'notax' => $row->notax,
                    'capping' => $row->capping,
                    'payspec' => $paytype,
                    'names' => $row->names
                );

                if($row->codes == 'loans'){
                    $loansarr[] = array(
                        'amt' => $row->amt,
                        'type' => $row->typesid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $paytype,
                        'names' => $row->names
                    );
                }
                if($row->codes =='others' && $row->functions == 0 && $row->effects == 0){
                    $total_others_sub += $row->amt;
                    $otherdeductarr[] = array(
                        'amt' => $row->amt,
                        'type' => $row->typesid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $paytype,
                        'names' => $row->names
                    );
                }
                if($row->codes =='others' && $row->functions == 1 && $row->effects == 1){
                   /* if($viewtype == 1){
                        if($payclass != 128 && $row->typesid == 264) {
                            $total_others_add+=$row->amt;
                        }else{
                            $total_others_add+=$row->amt;
                        }
                    }else if($viewtype == 4){
                        if($row->typesid == 264){
                            $total_others_add+=$row->amt;
                        }
                    }else{
                        if($row->typesid == 264){
                            $total_others_add+=$row->amt;
                        }
                    } */
                    $total_others_add+=$row->amt;
                    $earningarr[] = array(
                        'amt' => $row->amt,
                        'type' => $row->typesid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $paytype,
                        'names' => $row->names
                    );

                }

            }
        }



        //additionals transactions
        if($payclass == 128   || $payclass == 3077 || $payclass == 3078){
            $ci->db->where(array("pmtb.paytype" => $paytype));
        }
        $qry_additional_trans  = $ci->db->select("pmtb.amount, pmt.tsysid,pmtb.paytype, pm.codes, pm.functions, pm.effects, pm.notax, pm.capping, tp.names")
            ->from("payroll_manual_transactions_breakdown as pmtb")
            ->join("payroll_manual_transactions as pmt" , "pmt.sysid = pmtb.groupid","left")
            ->join('prime_types_parameter AS tp', 'tp.sysid = pmt.tsysid', 'left')
            ->join('payroll_matrix AS pm', 'pm.typesid = tp.sysid', 'left')
            ->where(array('pmtb.empid' => $empid, 'pmtb.month' => $month, 'pmtb.year' => $year, "pmtb.status" => 313))
            ->get();

        $add_trans_variable = $ci->db->last_query();
        if($qry_additional_trans->num_rows() > 0){
            foreach ($qry_additional_trans->result() as $row){

                if($row->tsysid == 257){
                    $totalsssloan = $row->amount;
                }else if($row->tsysid == 260){
                    $totalhmoded = $row->amount;
                }else if($row->tsysid == 258){
                    $totalhdmfloan = $row->amount;
                }else if($row->tsysid == 1079){
                    $totaldeda = $row->amount;
                }

                if($row->codes == 'loans'){
                    $loansarr[] = array(
                        'amt' => $row->amount,
                        'type' => $row->tsysid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $row->paytype,
                        'names' => $row->names
                    );
                }
                if($row->codes =='others' && $row->functions == 0 && $row->effects == 0){
                    $otherdeductarr[] = array(
                        'amt' => $row->amount,
                        'type' => $row->tsysid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $row->paytype,
                        'names' => $row->names
                    );
                }
                if($row->codes =='others' && $row->functions == 1 && $row->effects == 1){
                    $earningarr[] = array(
                        'amt' => $row->amount,
                        'type' => $row->tsysid,
                        'codes' => $row->codes,
                        'functions' => $row->functions,
                        'effects' => $row->effects,
                        'notax' => $row->notax,
                        'capping' => $row->capping,
                        'payspec' => $row->paytype,
                        'names' => $row->names
                    );
                }
                $transactions[] = array(
                    'amt' => $row->amount,
                    'type' => $row->tsysid,
                    'codes' => $row->codes,
                    'functions' => $row->functions,
                    'effects' => $row->effects,
                    'notax' => $row->notax,
                    'capping' => $row->capping,
                    'payspec' => $row->paytype,
                    'names' => $row->names
                );
            }
        }
    }

    $data['input']                  = array('empid' => $empid, 'month' => $month, 'year' => $year, 'paytype' => $paytype, 'payspec' => $paytypepopover);
    $data['transactions']           = $transactions;
    $data['loans']                  = $total_loans;
    $data['earnings15']             = $total_net_1;
    $data['earnings30']             = $total_net_2;
    $data['totalothersadd']         = $total_others_add;
    $data['totalotherssub']         = $total_others_sub;
    $data['totaltaxableamount']     = $total_taxable_amount;
    $data['totalnontaxableamount']  = $total_nontaxable_amount;
    $data['totalamout']             = $total_raw_amount;
    $data['payclass']               = $payclass;
    $data['totalholiday']           = $total_holiday;
    $data['loansarr']               = $loansarr;
    $data['earningarr']             = $earningarr;
    $data['otherdeductarr']         = $otherdeductarr;
    $data['taxwithoutcapping']      = $taxwithoutcapping;
    $data['totalsssloan']           = $totalsssloan;
    $data['totalhmoded']            = $totalhmoded;
    $data['totalhdmfloan']          = $totalhdmfloan;
    $data['totaldeda']              = $totaldeda;
    $data['totalpecewaloan']        = $totalpecewaloan;
    $data['totalcooploan']          = $totalcooploan;
    $data['totalpagibigadd']        = $totalpagibigadd;
    $data['totalcola']              = $totalcola;
    $data['totaltransallw']         = $totaltransallw;
    $data['totalrice']              = $totalrice;
    $data['totalotherdedn']         = $totalotherdedn;
    $data['totalelectric']          = $totalelectricbill;
    $data['totalmemins']            = $totalmemins;
    $data['totallwop']              = $totallwop;
    $data['totalholidayprev']       = $totalholiday;
    $data['nightdiff']              = $nightdiff;
    $data['otwithholiday']          = $otwithholiday;
    $data['otweekend']              = $otweekend;
    $data['otweekday']              = $otweekday;
    $data['totalactingallw']        = $totalactingallw;
    $data['totalotheradd']          = $totalotheradd;
    $data['ndot8hrs']              = $ndot8hrs;
    $data['add_trans_variable']              = $add_trans_variable;

    $data['confi15']                = $confi_1st_half;
    $data['confi30']                = $confi_2nd_half;
    $data['confi_distributed']      = $confi_distributed;

    return (object)$data;
}
//COMPUTE NETPAY OF EMPLOYEE
function compute_employee_netpay($empid, $month = false, $year = false, $paytype = false, $paytypepopover = false, $payclass = false , $viewtype)
{
    // $paytype = 2;
    // QUERY DEDUCTION MONTHLY HERE
    $ci = &get_instance();
    $data = array();
    $total_deduction = 0;
    $deduct_arr = array();
    $emp_salary = get_employee_salary($empid);
    $emp_salary = ($emp_salary) ? $emp_salary : 0;
    $salary = 0;
    $taxamt = 0;
    $status = '';
    $total_cont = 0;
    $loans = 0;
    $totalotherssub = 0;
    $totalothersadd = 0;
    $totaltaxableamount = 0;
    $totalnontaxableamount = 0;
    $deductionamount = 0;
    $earnings_amount = 0;
    $basic_taxable = 0;
    $basic_amount = 0;
    $transactions = false;
    $loansarr = false;
    $earningarr = false;
    $otherdeductarr = false;
    $additionaldeductions = 0;
    $total_holiday = 0;

    $taxablewithoutcapping = 0;
    $taxwithoutcapping = 0;
    $totaldeductionwithoutcapping = 0;

    $totalsssloan = 0;
    $totalhmoded = 0;
    $totalhdmfloan = 0;
    $totaldeda = 0;
    $totalpecewaloan = 0;
    $totalcooploan = 0;
    $totalpagibigadd = 0;
    $totalotherdedn = 0;
    $totalelectric = 0;
    $totalmemins = 0;
    $totallwop = 0;
    $totalcola = 0;
    $totaltransallw = 0;
    $totalrice = 0;
    $totalholiday = 0;
    $totalnightdiff = 0;
    $otwholiday = 0;
    $otweekend = 0;
    $otweekday = 0;
    $totalactingallw = 0;
    $totalotheradd = 0;
    $ndot8hrs = 0;

    $add_trans_variable = array();

    if ($month && $year && $payclass) {

        $qry_check = $ci->db->select()
            ->from('payroll_reports_group AS g')
            ->join('payroll_reports_main AS m', 'g.sysid = m.groupid')
            ->where(array('g.years' => $year, 'g.months' => $month, 'g.paytype' => $paytype, 'm.empid' => $empid))
            ->where_not_in('g.status',array(0,302))
            ->get()->row();

        if ($qry_check == false) {
            $basic = ($emp_salary) ? $emp_salary : 0;
            $trn_query = get_employee_transactions($empid, $month, $year, $paytype, $paytypepopover, $payclass, $viewtype);
            $data['loansintransactions'] = $trn_query->loans;

            $add_trans_variable = $trn_query->add_trans_variable;
            $trn_num_rows = count($trn_query->transactions);
            if ($trn_num_rows > 0) {
                $transactions    =  $trn_query->transactions;
                $loansarr        =  $trn_query->loansarr;
                $earningarr        =  $trn_query->earningarr;
                $otherdeductarr        =  $trn_query->otherdeductarr;
                $loans           =  $trn_query->loans;
                $totalothersadd  =  $trn_query->totalothersadd;
                $totalotherssub  =  $trn_query->totalotherssub;
                $deductionamount += $totalotherssub;
                $totaltaxableamount = $trn_query->totaltaxableamount;
                $totalnontaxableamount = $trn_query->totalnontaxableamount;
                $total_holiday = $trn_query->totalholiday;
                $taxablewithoutcapping = $trn_query->taxwithoutcapping;
                $totalpagibigadd = $trn_query->totalpagibigadd;
                $totalotherdedn = $trn_query->totalotherdedn;
                $totalelectric = $trn_query->totalelectric;
                $totalmemins = $trn_query->totalmemins;
                $totallwop = $trn_query->totallwop;
                $totalcola = $trn_query->totalcola;
                $totaltransallw = $trn_query->totaltransallw;
                $totalrice = $trn_query->totalrice;
                $totalholiday = $trn_query->totalholidayprev;
                $totalnightdiff = $trn_query->nightdiff;

                $totalsssloan = $trn_query->totalsssloan;
                $totalhmoded = $trn_query->totalhmoded;
                $totalhdmfloan = $trn_query->totalhdmfloan;
                $totaldeda = $trn_query->totaldeda;
                $totalpecewaloan = $trn_query->totalpecewaloan;
                $totalcooploan = $trn_query->totalcooploan;
                $confi15 = $trn_query->confi15;
                $confi30 = $trn_query->confi30;
                $otwholiday = $trn_query->otwithholiday;
                $otweekend = $trn_query->otweekend;
                $otweekday = $trn_query->otweekday;
                $totalactingallw = $trn_query->totalactingallw;
                $totalotheradd = $trn_query->totalotheradd;
                $ndot8hrs = $trn_query->ndot8hrs;

            } else {
                $salary = $basic;
            }

            if($payclass == 128  || $payclass == 3077 || $payclass == 3078){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check for additional deductions
            $checkadditionaldeductions = $ci->db->select("SUM(pmtb.amount) AS totaldeductions")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt", "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("payroll_matrix as pm","pm.typesid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "pm.codes" => 'others' , "pm.functions" => 0 , "pm.effects" => 0))
                ->get()->row();
            $additionaldeductions += ($checkadditionaldeductions) ? $checkadditionaldeductions->totaldeductions:'';

            if($payclass == 128  || $payclass == 3077 || $payclass == 3078){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check additional loans
            $checkadditionalloans = $ci->db->select("SUM(pmtb.amount) AS totalloans")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt", "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("payroll_matrix as pm","pm.typesid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "pm.codes" => 'loans'))
                ->get()->row();
            $loans += ($checkadditionalloans) ? $checkadditionalloans->totalloans : '';

            if($payclass == 128  || $payclass == 3077 || $payclass == 3078){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check additional cont
            $checkadditionalpremium = $ci->db->select("SUM(pmtb.amount) AS totalpremiumamount")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt" , "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("prime_types_parameter as ptp" , "ptp.sysid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "ptp.codes" => 'EMPCONT'))
                ->get()->row();
            $total_cont += ($checkadditionalpremium) ? $checkadditionalpremium->totalpremiumamount : '';

            // QUERY FROM CONTRIBUTION TABLE TABLE
            $qry_deduction_matrix = $ci->db->query("SELECT tp.names, tp.sysid
                        FROM trn_employee_deduction_matrix AS dm
                        LEFT JOIN prime_types_parameter AS tp ON tp.sysid = dm.deductid
                        WHERE dm.empid = $empid AND dm.status = 1 AND tp.sysid != 75 AND tp.sysid != 73
                   ");


            
            if ($qry_deduction_matrix) {
                foreach ($qry_deduction_matrix->result() as $row) {
                    $class = '';
                    $tpid = $row->sysid;
                    $sum_fix_amt = 0;
                    $sum_add_amt = 0;

                    if($payclass != 128 && $payclass != 3077 && $payclass != 3078){
                        $paytype = 1;
                    }

                    // ADDIONAL AMOUNT FOR CONTRIBUTION //
                    $get_cont_add = $ci->db->query("SELECT SUM(pt.amt) AS amt
                        FROM prime_contribution_add_matrix AS cam
                        LEFT JOIN payroll_transactions AS pt ON cam.earningid = pt.typesid
                        WHERE cam.typesid = $tpid
                        AND cam.status = 1
                        AND pt.status = 1
                        AND pt.months = $month
                        AND pt.years = $year
                        AND pt.paytype = $paytype
                        AND pt.empid = $empid
                        ")->row();

                    //$data['addAmt_qry'][] = $ci->db->last_query();
                    //$data['addAmt'][] = $get_cont_add;

                    $get_cont_add_fix = $ci->db->query("SELECT SUM(pt.amt) AS amt
                        FROM prime_contribution_add_matrix AS cam
                        LEFT JOIN payroll_fix_amt AS pt ON pt.typesid = cam.earningid
                        WHERE cam.typesid = $tpid 
                        AND pt.empid = $empid
                        AND cam.`status` = 1 
                        AND pt.`status` = 1
                        ")->row();

                    //$data['fixAmt_qry'][] = $ci->db->last_query();
                    //$data['fixAmt'][] = $get_cont_add_fix;

                    $emp_fix_amt = ($get_cont_add_fix) ? $get_cont_add_fix->amt : 0;

                    $emp_add_amt = ($get_cont_add) ? $get_cont_add->amt : 0;


                    $new_empsalary = $emp_fix_amt + $emp_salary + $emp_add_amt;
                    $new_add_amt = $emp_fix_amt + $emp_add_amt;

                    $data['salarycont'][] = $new_empsalary;
                    //$data['add_amt'][] = $new_add_amt;

                    $qry_cont = $ci->db->query("
                            SELECT 
                            amtcont, 
                            deductible, 
                            rateemployee, 
                            rateemployer, 
                            var 
                            FROM prime_contribution_matrix 
                            WHERE conttype = $tpid 
                            AND status = 1 
                            AND  $new_empsalary BETWEEN amtmin AND amtmax")->row();

                    //$data['qry_cont'][] = $ci->db->last_query();
                    //$data['qry_contAmt'][] = $qry_cont;

                    if ($qry_cont) {
                        $ssscont = 0;
                        $philhealthcont = 0;
                        $pagibigcont = 0;

                        if($tpid == 72){

                            $ssscont += $qry_cont->amtcont * $qry_cont->rateemployee;
                            $data['ssscont'] =  $ssscont;
                        }
                        if($tpid == 73){
                            $philhealthcont +=  $qry_cont->amtcont * $qry_cont->rateemployee;
                           // $data['philhealth'] = $philhealthcont;
                            $data['philhealth'] = 0;
                        }

                        if($tpid == 74){
                            $pagibigcont += $qry_cont->amtcont * $qry_cont->rateemployee;
                            $data['pagibig'] =  $pagibigcont;

                        }

                        if ($qry_cont->var == 1) {
                            $empe_amt = $qry_cont->amtcont * $qry_cont->rateemployee;
                            $comp_amt = $qry_cont->amtcont * $qry_cont->rateemployer;
                        } else {
                            $empe_amt = $qry_cont->amtcont;
                            $comp_amt = 0;
                        }
                        $row_amt = $empe_amt;
                        if ($trn_query->payclass == 128 || $trn_query->payclass == 3077 || $trn_query->payclass == 3078) {
                            if ($paytype == 1) {

                                if (($qry_cont->deductible == 1) || $trn_query->payclass == 3077 || $trn_query->payclass == 3078) {
                                    $total_cont += $row_amt;
                                    $class = "font-red-flamingo";
                                }
                                if($trn_query->payclass == 3077 || $trn_query->payclass == 3078){
                                    $class = "font-red-flamingo";
                                }

                                $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                                $transactions[] = array(
                                    'amt' => $empe_amt,
                                    'type' => $tpid
                                );
                            }
                        } else {
                            if ($qry_cont->deductible == 1) {
                                $total_cont += $row_amt;
                                $class = "font-red-flamingo";
                            }
                            $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                            $transactions[] = array(
                                'amt' => $empe_amt,
                                'type' => $tpid
                            );
                        }

                    } else {
                        $qry_cont = $ci->db->query("SELECT amtcont, deductible, rateemployee, rateemployer, var FROM prime_contribution_matrix WHERE conttype = $tpid AND end = 1 AND status = 1")->row();
                        if ($qry_cont) {
                            $ssscont = 0;
                            $philhealthcont = 0;
                            $pagibigcont = 0;

                            if($tpid == 72){
                                $ssscont += $qry_cont->amtcont * $qry_cont->rateemployee;
                                $data['ssscont'] =  $ssscont;
                            }
                            if($tpid == 73){
                                $philhealthcont +=  $qry_cont->amtcont * $qry_cont->rateemployee;
                                // $data['philhealth'] = $philhealthcont;
                                $data['philhealth'] = 0;
                            }

                            if($tpid == 74){
                                $pagibigcont += $qry_cont->amtcont * $qry_cont->rateemployee;
                                $data['pagibig'] =  $pagibigcont;
                            }
                            if ($qry_cont->var == 1) {
                                $empe_amt = $qry_cont->amtcont * $qry_cont->rateemployee;
                                $comp_amt = $qry_cont->amtcont * $qry_cont->rateemployer;
                            } else {
                                $empe_amt = $qry_cont->amtcont;
                                $comp_amt = 0;
                            }
                            $row_amt = $empe_amt;

                            if ($trn_query->payclass == 128 || $trn_query->payclass == 3077 || $trn_query->payclass == 3078) {
                                if ($paytype == 1) {
                                    if ($qry_cont->deductible == 1) {
                                        $total_cont += $row_amt;
                                        $class = "font-red-flamingo";
                                    }
                                    $transactions[] = array(
                                        'amt' => $empe_amt,
                                        'type' => $tpid
                                    );
                                    $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                                }
                            } else {

                                if ($qry_cont->deductible == 1) {
                                    $total_cont += $row_amt;
                                    $class = "font-red-flamingo";
                                }
                                $transactions[] = array(
                                    'amt' => $empe_amt,
                                    'type' => $tpid
                                );
                                $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                            }
                        } else {
                            $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => 0, 'amtcomp' => 0, 'class' => $class);
                        }
                    }
                }

                // RANK AND FILE
                if ($payclass == 128 || $payclass ==  3077 || $payclass== 3078) {
                    $salary = $basic;
                    $basic = $basic;
                    if ($paytype == 1) {
                        if($paytypepopover != 2){
                            $basic_amount = ($basic / 2);
                        }else{
                            $basic_amount = ($basic / 2);
                        }
                        if($paytypepopover == 0){
                            $totalotherssub = $totalotherssub / 2;
                        }
                        $basic_taxable = $basic_amount + $totaltaxableamount;
                        $earnings_amount = ($basic_amount + $totalothersadd); // NOTE: (+) $totalotherssub because negative value;
                    }else{
                        $basic_amount = ($basic / 2);
                        $basic_taxable = $basic_amount + $totaltaxableamount;
                        $earnings_amount = $basic_amount + $totalothersadd; // NOTE: (+) $totalotherssub because negative value;
                    }
                } else {
                    /*
                    $getenddate = $ci->db->select("dateend")->from("prime_employee_main")
                        ->where(array("sysid" => $empid))->get()->row();
                    if($getenddate) {
                        $date_15 = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-1'; // or your date as well
                        $earlier = new DateTime($getenddate->dateend);
                        $later = new DateTime($date_15);
                        $diff = $later->diff($earlier)->format("%a");
                        if($diff>15) {
                            $basic = $basic;
                        }else{
                            $basic = $basic / 2;
                        }
                    }else{
                        $basic = $basic;
                    }
                    */
                    $salary = $basic;
                    $basic_amount = $basic;
                    $basic_taxable = $basic + $totaltaxableamount;
                    // NOTE: (+) $totalotherssub because negative value;

                    $earnings_amount = ($basic_amount + $totalothersadd);



                }
                if($month == 1){
                    $monthlate = 12;
                    $yearlate = $year - 1;
                }else{
                    $monthlate = $month - 1;
                    $yearlate = $year;
                }
                //check for LWOP


                $checkforlwoppay = $ci->db->select("amt")->from("payroll_transactions")
                    ->where(array("typesid" => 262 , "empid" => $empid , "months" => $month,
                        "years" => $year , "payspec" => $paytype,"status" => 1))->get()->row();
                $lwoppay = ($checkforlwoppay) ? $checkforlwoppay->amt : '';


                $basic_amount = (double)$basic_amount - (double)$lwoppay;



                /*    $ceilingamt =  $basic_amount * 3;
                    if($ceilingamt > 90000){
                        $taxxable_bonus  = $ceilingamt - 90000;
                        $montlyapplication = $taxxable_bonus / 12;
                        $totaltaxableamount = $totaltaxableamount + $basic_amount + $montlyapplication;
                    }else{
                        $totaltaxableamount = $totaltaxableamount + $basic_amount;
                    } */

                $ceilingamt =  $basic_amount + $totaltaxableamount;
                if($ceilingamt > 90000){
                    $totaltaxableamount  = $ceilingamt - 90000;
                }else{
                    $totaltaxableamount = $totaltaxableamount + $basic_amount;
                }

                $taxablewithoutcapping = $taxablewithoutcapping + $basic_amount;

                $qry_tax_matrix = $ci->db->query("SELECT tp.names, tp.sysid
                    FROM trn_employee_deduction_matrix AS dm
                    LEFT JOIN prime_types_parameter AS tp ON tp.sysid = dm.deductid
                    WHERE dm.empid = $empid AND dm.status = 1 AND tp.sysid = 75
                   ")->row();
                $taxamt = 0;

                //this is temporary we must find the bonus entry and check if exceed 90k the if not then do not include in taxable
                /*  $checkforbonusexceed90k = $ci->db->select("amt")->from("payroll_transactions")
                      ->where(array("typesid" => 264 , "empid" => $empid , "months" => $month,
                          "years" => $year , "payspec" => $paytype,"status" => 1))->get()->row();
                  if($checkforbonusexceed90k){
                      if($checkforbonusexceed90k->amt < 90000){
                          $totaltaxableamount = $totaltaxableamount - $checkforbonusexceed90k->amt;
                      }
                  } */


                if ($qry_tax_matrix) {
                    if($payclass == 128   || $payclass == 3077 || $payclass == 3078){
                        //GET TAXABLE BRACKET
                        $qry_cont = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75  AND payclass= 128
                                        AND $totaltaxableamount BETWEEN amtmin AND amtmax")->row();
                        $qry_cont_wo_bonuses= '';
                    }else{
                        $confidentialtaxamount = $totaltaxableamount;
                        //GET TAXABLE BRACKET
                        $qry_cont = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75 AND payclass = 1
                                        AND $confidentialtaxamount BETWEEN amtmin AND amtmax")->row();

                        $qry_cont_wo_bonuses = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75 AND payclass = 1
                                        AND $taxablewithoutcapping BETWEEN amtmin AND amtmax")->row();

                    }

                    if ($qry_cont) {

                        $data['amtmin'] = $qry_cont->amtmin;
                        $data['employeerate'] = $qry_cont->rateemployee;
                        //PECO computation
                        $data['totaltaxcomputednow'] = $totaltaxableamount;

                        $examt = (($totaltaxableamount - $qry_cont->amtmin) * $qry_cont->rateemployee) + $qry_cont->amtcont;
                        if($qry_cont_wo_bonuses){
                            $taxwithoutcapping = (($taxablewithoutcapping - $qry_cont_wo_bonuses->amtmin) * $qry_cont_wo_bonuses->rateemployee) + $qry_cont_wo_bonuses->amtcont;
                        }else{
                            $taxwithoutcapping = 0;
                        }
                        //computation in research
                        // $examt = ((($totaltaxableamount - $total_cont)- $qry_cont->amtmin) * $qry_cont->rateemployee) + $qry_cont->amtcont;


                        if ($payclass == 128   || $payclass == 3077 || $payclass == 3078) {
                            $class = "font-red-flamingo";

                            $deduct_arr[75] = array('contname' => $qry_tax_matrix->names, 'amt' => $examt, 'amtcomp' => 0, 'class' => $class);
                            $taxamt += $examt;

                            //checking for annualization tax
                            $checkforannualtax = $ci->db->select("amount")
                                ->from("payroll_anual_tax_distribution")
                                ->where(array("month" => $month , "year" => $year , "empid" =>$empid , "status" => 313))->get()->row();

                            if($checkforannualtax){
                                $taxamt = $checkforannualtax->amount;
                            }
                            $data['totaltax']  = $taxamt;
                            $transactions[] = array(
                                'amt' => $taxamt,
                                'type' => 75
                            );
                            $deduct_arr[75] = array('contname' => 'TAX', 'amt' => $taxamt, 'amtcomp' => 0, 'class' => $class);

                        } else {
                            $taxamt += $examt;
                            $data['totaltax']  = $taxamt;
                            //checking for annualization tax
                            $checkforannualtax = $ci->db->select("amount")->from("payroll_anual_tax_distribution")
                                ->where(array("month" => $month , "year" => $year , "empid" =>$empid , "status" => 313))->get()->row();

                            if($checkforannualtax){
                                $taxamt = $checkforannualtax->amount * 2;
                            }

                            $transactions[] = array(
                                'amt' => $taxamt,
                                'type' => 75
                            );
                            $deduct_arr[75] = array('contname' => 'TAX', 'amt' => $taxamt, 'amtcomp' => 0, 'class' => $class);
                        }
                    }
                }

                $total_deduction = $deductionamount + $taxamt + $additionaldeductions + $total_cont  + $loans;
                $totaldeductionwithoutcapping = $deductionamount + $taxwithoutcapping + $additionaldeductions + $total_cont  + $loans;
            }

        } else {
            $status = '<span class="label label-success">PAID</span>';
        }
    }


    $annualtaxarr = array();
    $annualgrossarr  = array();
    $annualnetarr  = array();
    $totalannualtax = 0;
    $totalannualgross = 0;
    $totalannualnet = 0;

    $totalfirsthalf = 0;
    $totalsecondhalf = 0;
    $totalspec = 0;
    $totaldist = 0;



    $netpay = (($earnings_amount + $totalannualnet)  - $total_deduction) ;
    if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
        // RANK N FILE
        $data['netpay'] = $netpay;

    } else {
        /*
            $get_employee_employment = $this->db->query("
                    SELECT datestart FROM prime_employee_main
                    WHERE sysid = $empid
                ")->get()->row();
        */

        $net_15 = ( $netpay / 2 );
        $net_30 = ( $netpay / 2 );
        $total_net = ( $net_15 + $net_30 );

        $data['net24'] = 0;
        $data['net15'] = $net_15;
        $data['net30'] = $net_30;
        $data['netpay'] = $total_net;
    }

    $data['totalfirsthalf'] = $totalfirsthalf;
    $data['totalsecondhalf'] = $totalsecondhalf;
    $data['totalspec'] = $totalspec;
    $data['totaldist'] = $totaldist;

    $data['status'] = $status;
    $data['deductions'] = $deduct_arr;
    $data['----'] = '------------------------------------------------';
    $data['confi_neypay'] = $netpay;
    $data['confi15'] = $netpay / 2;
    $data['confi30'] = $netpay / 2;
    $data['-----'] = '------------------------------------------------';
    $data['transactions'] = $transactions;
    $data['loansarr'] = $loansarr;
    $data['earningarr'] = $earningarr;
    $data['otherdeductarr'] = $otherdeductarr;
    $data['annualgrossarr'] = $annualgrossarr;
    $data['annualtaxarr'] = $annualtaxarr;
    $data['annualnetarr'] = $annualnetarr;
    $data['payclass'] = $payclass;
    $data['basic'] = $salary;
    $data['earnings'] = $earnings_amount;
    $data['premiums'] = $total_cont;
    $data['loans'] = $loans;
    $data['otherssub'] = $totalotherssub;
    $data['taxamt'] = $taxamt + $totalannualtax;
    $data['totalothersadd'] = $totalothersadd;
    $data['totalotherssub'] = $totalotherssub;
    $data['total_holiday'] = $total_holiday;
    $data['taxableamount'] = $totaltaxableamount;
    $data['nontaxableamount'] = $totalnontaxableamount;
    $data['taxabablebasic'] = $basic_taxable;
    $data['basicamoumt'] = $basic_amount;
    $data['totalsssloan'] = $totalsssloan;
    $data['totalhmoded'] = $totalhmoded;
    $data['totalhdmfloan'] = $totalhdmfloan;
    $data['totaldeda'] = $totaldeda;
    $data['totalpecewaloan'] = $totalpecewaloan;
    $data['totalcooploan'] = $totalcooploan;
    $data['totalpagibigadd'] = $totalpagibigadd;
    $data['totalotherdedn'] = $totalotherdedn;
    $data['totalelectric'] = $totalelectric;
    $data['totalmemins'] = $totalmemins;
    $data['totallwop'] = $totallwop;
    $data['totalcola'] = $totalcola;
    $data['totaltransallw'] = $totaltransallw;
    $data['totalrice'] = $totalrice;
    $data['totalholiday'] = $totalholiday;
    $data['nightdiff'] = $totalnightdiff;
    $data['otwithholiday'] = $otwholiday;
    $data['otweekend'] = $otweekend;
    $data['otweekdays'] = $otweekday;
    $data['totalactingallw'] = $totalactingallw;
    $data['totalotheradd'] = $totalotheradd;
    $data['ndot8hrs'] = $ndot8hrs;
    $data['add_trans_variable'] = $add_trans_variable;

    //for inserting in main
    $data['deductionamount'] = $total_deduction + $totalannualtax; //$total_deduction;

    return (object)$data;
    exit();

}

function compute_final_employee_netpay($empid, $month = false, $year = false, $paytype = false, $paytypepopover = false, $payclass = false , $viewtype)
{
    // $paytype = 2;
    // QUERY DEDUCTION MONTHLY HERE
    $ci = &get_instance();
    $data = array();
    $total_deduction = 0;
    $deduct_arr = array();
    $emp_salary = get_employee_salary($empid);
    $emp_salary = ($emp_salary) ? $emp_salary : 0;
    $salary = 0;
    $taxamt = 0;
    $status = '';
    $total_cont = 0;
    $loans = 0;
    $totalotherssub = 0;
    $totalothersadd = 0;
    $totaltaxableamount = 0;
    $totalnontaxableamount = 0;
    $deductionamount = 0;
    $earnings_amount = 0;
    $basic_taxable = 0;
    $basic_amount = 0;
    $transactions = false;
    $loansarr = false;
    $earningarr = false;
    $otherdeductarr = false;
    $additionaldeductions = 0;
    $total_holiday = 0;

    $taxablewithoutcapping = 0;
    $taxwithoutcapping = 0;
    $totaldeductionwithoutcapping = 0;

    $totalsssloan = 0;
    $totalhmoded = 0;
    $totalhdmfloan = 0;
    $totaldeda = 0;
    $totalpecewaloan = 0;
    $totalcooploan = 0;
    $totalpagibigadd = 0;
    $totalotherdedn = 0;
    $totalelectric = 0;
    $totalmemins = 0;
    $totallwop = 0;
    $totalcola = 0;
    $totaltransallw = 0;
    $totalrice = 0;
    $totalholiday = 0;
    $totalnightdiff = 0;
    $otwholiday = 0;
    $otweekend = 0;
    $otweekday = 0;
    $totalactingallw = 0;
    $totalotheradd = 0;
    $ndot8hrs = 0;

    $add_trans_variable = array();

    if ($month && $year && $payclass) {

        $qry_check = $ci->db->select()
            ->from('payroll_reports_group AS g')
            ->join('payroll_reports_main AS m', 'g.sysid = m.groupid')
            ->where(array('g.years' => $year, 'g.months' => $month, 'g.paytype' => $paytype, 'm.empid' => $empid))
            ->where_not_in('g.status',array(0,302))
            ->get()->row();

        if ($qry_check != false) {
            $basic = ($emp_salary) ? $emp_salary : 0;
            $trn_query = get_employee_transactions($empid, $month, $year, $paytype, $paytypepopover, $payclass, $viewtype);
            $data['loansintransactions'] = $trn_query->loans;

            $add_trans_variable = $trn_query->add_trans_variable;
            $trn_num_rows = count($trn_query->transactions);
            if ($trn_num_rows > 0) {
                $transactions    =  $trn_query->transactions;
                $loansarr        =  $trn_query->loansarr;
                $earningarr        =  $trn_query->earningarr;
                $otherdeductarr        =  $trn_query->otherdeductarr;
                $loans           =  $trn_query->loans;
                $totalothersadd  =  $trn_query->totalothersadd;
                $totalotherssub  =  $trn_query->totalotherssub;
                $deductionamount += $totalotherssub;
                $totaltaxableamount = $trn_query->totaltaxableamount;
                $totalnontaxableamount = $trn_query->totalnontaxableamount;
                $total_holiday = $trn_query->totalholiday;
                $taxablewithoutcapping = $trn_query->taxwithoutcapping;
                $totalpagibigadd = $trn_query->totalpagibigadd;
                $totalotherdedn = $trn_query->totalotherdedn;
                $totalelectric = $trn_query->totalelectric;
                $totalmemins = $trn_query->totalmemins;
                $totallwop = $trn_query->totallwop;
                $totalcola = $trn_query->totalcola;
                $totaltransallw = $trn_query->totaltransallw;
                $totalrice = $trn_query->totalrice;
                $totalholiday = $trn_query->totalholidayprev;
                $totalnightdiff = $trn_query->nightdiff;

                $totalsssloan = $trn_query->totalsssloan;
                $totalhmoded = $trn_query->totalhmoded;
                $totalhdmfloan = $trn_query->totalhdmfloan;
                $totaldeda = $trn_query->totaldeda;
                $totalpecewaloan = $trn_query->totalpecewaloan;
                $totalcooploan = $trn_query->totalcooploan;
                $confi15 = $trn_query->confi15;
                $confi30 = $trn_query->confi30;
                $otwholiday = $trn_query->otwithholiday;
                $otweekend = $trn_query->otweekend;
                $otweekday = $trn_query->otweekday;
                $totalactingallw = $trn_query->totalactingallw;
                $totalotheradd = $trn_query->totalotheradd;
                $ndot8hrs = $trn_query->ndot8hrs;

            } else {
                $salary = $basic;
            }

            if($payclass == 128  || $payclass == 3077 || $payclass == 3078){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check for additional deductions
            $checkadditionaldeductions = $ci->db->select("SUM(pmtb.amount) AS totaldeductions")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt", "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("payroll_matrix as pm","pm.typesid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "pm.codes" => 'others' , "pm.functions" => 0 , "pm.effects" => 0))
                ->get()->row();
            $additionaldeductions += ($checkadditionaldeductions) ? $checkadditionaldeductions->totaldeductions:'';

            if($payclass == 128  || $payclass == 3077 || $payclass == 3078){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check additional loans
            $checkadditionalloans = $ci->db->select("SUM(pmtb.amount) AS totalloans")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt", "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("payroll_matrix as pm","pm.typesid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "pm.codes" => 'loans'))
                ->get()->row();
            $loans += ($checkadditionalloans) ? $checkadditionalloans->totalloans : '';

            if($payclass == 128  || $payclass == 3077 || $payclass == 3078){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check additional cont
            $checkadditionalpremium = $ci->db->select("SUM(pmtb.amount) AS totalpremiumamount")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt" , "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("prime_types_parameter as ptp" , "ptp.sysid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "ptp.codes" => 'EMPCONT'))
                ->get()->row();
            $total_cont += ($checkadditionalpremium) ? $checkadditionalpremium->totalpremiumamount : '';

            // QUERY FROM CONTRIBUTION TABLE TABLE
            $qry_deduction_matrix = $ci->db->query("SELECT tp.names, tp.sysid
                        FROM trn_employee_deduction_matrix AS dm
                        LEFT JOIN prime_types_parameter AS tp ON tp.sysid = dm.deductid
                        WHERE dm.empid = $empid AND dm.status = 1 AND tp.sysid != 75 AND tp.sysid != 73
                   ");


            if ($qry_deduction_matrix) {
                foreach ($qry_deduction_matrix->result() as $row) {
                    $class = '';
                    $tpid = $row->sysid;
                    $sum_fix_amt = 0;
                    $sum_add_amt = 0;

                    if($payclass != 128 && $payclass != 3077 && $payclass != 3078){
                        $paytype = 1;
                    }

                    // ADDIONAL AMOUNT FOR CONTRIBUTION //
                    $get_cont_add = $ci->db->query("SELECT SUM(pt.amt) AS amt
                        FROM prime_contribution_add_matrix AS cam
                        LEFT JOIN payroll_transactions AS pt ON cam.earningid = pt.typesid
                        WHERE cam.typesid = $tpid
                        AND cam.status = 1
                        AND pt.status = 1
                        AND pt.months = $month
                        AND pt.years = $year
                        AND pt.paytype = $paytype
                        AND pt.empid = $empid
                        ")->row();

                    //$data['addAmt_qry'][] = $ci->db->last_query();
                    //$data['addAmt'][] = $get_cont_add;

                    $get_cont_add_fix = $ci->db->query("SELECT SUM(pt.amt) AS amt
                        FROM prime_contribution_add_matrix AS cam
                        LEFT JOIN payroll_fix_amt AS pt ON pt.typesid = cam.earningid
                        WHERE cam.typesid = $tpid 
                        AND pt.empid = $empid
                        AND cam.`status` = 1 
                        AND pt.`status` = 1
                        ")->row();

                    //$data['fixAmt_qry'][] = $ci->db->last_query();
                    //$data['fixAmt'][] = $get_cont_add_fix;

                    $emp_fix_amt = ($get_cont_add_fix) ? $get_cont_add_fix->amt : 0;

                    $emp_add_amt = ($get_cont_add) ? $get_cont_add->amt : 0;


                    $new_empsalary = $emp_fix_amt + $emp_salary + $emp_add_amt;
                    $new_add_amt = $emp_fix_amt + $emp_add_amt;

                    $data['salarycont'][] = $new_empsalary;
                    //$data['add_amt'][] = $new_add_amt;

                    $qry_cont = $ci->db->query("
                            SELECT 
                            amtcont, 
                            deductible, 
                            rateemployee, 
                            rateemployer, 
                            var 
                            FROM prime_contribution_matrix 
                            WHERE conttype = $tpid 
                            AND status = 1 
                            AND  $new_empsalary BETWEEN amtmin AND amtmax")->row();

                    //$data['qry_cont'][] = $ci->db->last_query();
                    //$data['qry_contAmt'][] = $qry_cont;

                    if ($qry_cont) {
                        $ssscont = 0;
                        $philhealthcont = 0;
                        $pagibigcont = 0;

                        if($tpid == 72){

                            $ssscont += $qry_cont->amtcont * $qry_cont->rateemployee;
                            $data['ssscont'] =  $ssscont;
                        }
                        if($tpid == 73){
                            $philhealthcont +=  $qry_cont->amtcont * $qry_cont->rateemployee;
                           // $data['philhealth'] = $philhealthcont;
                            $data['philhealth'] = 0;
                        }

                        if($tpid == 74){
                            $pagibigcont += $qry_cont->amtcont * $qry_cont->rateemployee;
                            $data['pagibig'] =  $pagibigcont;

                        }

                        if ($qry_cont->var == 1) {
                            $empe_amt = $qry_cont->amtcont * $qry_cont->rateemployee;
                            $comp_amt = $qry_cont->amtcont * $qry_cont->rateemployer;
                        } else {
                            $empe_amt = $qry_cont->amtcont;
                            $comp_amt = 0;
                        }
                        $row_amt = $empe_amt;
                        if ($trn_query->payclass == 128 || $trn_query->payclass == 3077 || $trn_query->payclass == 3078) {
                            if ($paytype == 1) {

                                if (($qry_cont->deductible == 1) || $trn_query->payclass == 3077 || $trn_query->payclass == 3078) {
                                    $total_cont += $row_amt;
                                    $class = "font-red-flamingo";
                                }
                                if($trn_query->payclass == 3077 || $trn_query->payclass == 3078){
                                    $class = "font-red-flamingo";
                                }

                                $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                                $transactions[] = array(
                                    'amt' => $empe_amt,
                                    'type' => $tpid
                                );
                            }
                        } else {
                            if ($qry_cont->deductible == 1) {
                                $total_cont += $row_amt;
                                $class = "font-red-flamingo";
                            }
                            $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                            $transactions[] = array(
                                'amt' => $empe_amt,
                                'type' => $tpid
                            );
                        }

                    } else {
                        $qry_cont = $ci->db->query("SELECT amtcont, deductible, rateemployee, rateemployer, var FROM prime_contribution_matrix WHERE conttype = $tpid AND end = 1 AND status = 1")->row();
                        if ($qry_cont) {
                            $ssscont = 0;
                            $philhealthcont = 0;
                            $pagibigcont = 0;

                            if($tpid == 72){
                                $ssscont += $qry_cont->amtcont * $qry_cont->rateemployee;
                                $data['ssscont'] =  $ssscont;
                            }
                            if($tpid == 73){
                                $philhealthcont +=  $qry_cont->amtcont * $qry_cont->rateemployee;
                                // $data['philhealth'] = $philhealthcont;
                                $data['philhealth'] = 0;
                            }

                            if($tpid == 74){
                                $pagibigcont += $qry_cont->amtcont * $qry_cont->rateemployee;
                                $data['pagibig'] =  $pagibigcont;
                            }
                            if ($qry_cont->var == 1) {
                                $empe_amt = $qry_cont->amtcont * $qry_cont->rateemployee;
                                $comp_amt = $qry_cont->amtcont * $qry_cont->rateemployer;
                            } else {
                                $empe_amt = $qry_cont->amtcont;
                                $comp_amt = 0;
                            }
                            $row_amt = $empe_amt;

                            if ($trn_query->payclass == 128 || $trn_query->payclass == 3077 || $trn_query->payclass == 3078) {
                                if ($paytype == 1) {
                                    if ($qry_cont->deductible == 1) {
                                        $total_cont += $row_amt;
                                        $class = "font-red-flamingo";
                                    }
                                    $transactions[] = array(
                                        'amt' => $empe_amt,
                                        'type' => $tpid
                                    );
                                    $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                                }
                            } else {

                                if ($qry_cont->deductible == 1) {
                                    $total_cont += $row_amt;
                                    $class = "font-red-flamingo";
                                }
                                $transactions[] = array(
                                    'amt' => $empe_amt,
                                    'type' => $tpid
                                );
                                $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                            }
                        } else {
                            $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => 0, 'amtcomp' => 0, 'class' => $class);
                        }
                    }
                }

                // RANK AND FILE
                if ($payclass == 128 || $payclass ==  3077 || $payclass== 3078) {
                    $salary = $basic;
                    $basic = $basic;
                    if ($paytype == 1) {
                        if($paytypepopover != 2){
                            $basic_amount = ($basic / 2);
                        }else{
                            $basic_amount = ($basic / 2);
                        }
                        if($paytypepopover == 0){
                            $totalotherssub = $totalotherssub / 2;
                        }
                        $basic_taxable = $basic_amount + $totaltaxableamount;
                        $earnings_amount = ($basic_amount + $totalothersadd); // NOTE: (+) $totalotherssub because negative value;
                    }else{
                        $basic_amount = ($basic / 2);
                        $basic_taxable = $basic_amount + $totaltaxableamount;
                        $earnings_amount = $basic_amount + $totalothersadd; // NOTE: (+) $totalotherssub because negative value;
                    }
                } else {
                    /*
                    $getenddate = $ci->db->select("dateend")->from("prime_employee_main")
                        ->where(array("sysid" => $empid))->get()->row();
                    if($getenddate) {
                        $date_15 = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-1'; // or your date as well
                        $earlier = new DateTime($getenddate->dateend);
                        $later = new DateTime($date_15);
                        $diff = $later->diff($earlier)->format("%a");
                        if($diff>15) {
                            $basic = $basic;
                        }else{
                            $basic = $basic / 2;
                        }
                    }else{
                        $basic = $basic;
                    }
                    */
                    $salary = $basic;
                    $basic_amount = $basic;
                    $basic_taxable = $basic + $totaltaxableamount;
                    // NOTE: (+) $totalotherssub because negative value;

                    $earnings_amount = ($basic_amount + $totalothersadd);



                }
                if($month == 1){
                    $monthlate = 12;
                    $yearlate = $year - 1;
                }else{
                    $monthlate = $month - 1;
                    $yearlate = $year;
                }
                //check for LWOP


                $checkforlwoppay = $ci->db->select("amt")->from("payroll_transactions")
                    ->where(array("typesid" => 262 , "empid" => $empid , "months" => $month,
                        "years" => $year , "payspec" => $paytype,"status" => 1))->get()->row();
                $lwoppay = ($checkforlwoppay) ? $checkforlwoppay->amt : '';


                $basic_amount = (double)$basic_amount - (double)$lwoppay;



                /*    $ceilingamt =  $basic_amount * 3;
                    if($ceilingamt > 90000){
                        $taxxable_bonus  = $ceilingamt - 90000;
                        $montlyapplication = $taxxable_bonus / 12;
                        $totaltaxableamount = $totaltaxableamount + $basic_amount + $montlyapplication;
                    }else{
                        $totaltaxableamount = $totaltaxableamount + $basic_amount;
                    } */

                $ceilingamt =  $basic_amount + $totaltaxableamount;
                if($ceilingamt > 90000){
                    $totaltaxableamount  = $ceilingamt - 90000;
                }else{
                    $totaltaxableamount = $totaltaxableamount + $basic_amount;
                }

                $taxablewithoutcapping = $taxablewithoutcapping + $basic_amount;

                $qry_tax_matrix = $ci->db->query("SELECT tp.names, tp.sysid
                    FROM trn_employee_deduction_matrix AS dm
                    LEFT JOIN prime_types_parameter AS tp ON tp.sysid = dm.deductid
                    WHERE dm.empid = $empid AND dm.status = 1 AND tp.sysid = 75
                   ")->row();
                $taxamt = 0;

                //this is temporary we must find the bonus entry and check if exceed 90k the if not then do not include in taxable
                /*  $checkforbonusexceed90k = $ci->db->select("amt")->from("payroll_transactions")
                      ->where(array("typesid" => 264 , "empid" => $empid , "months" => $month,
                          "years" => $year , "payspec" => $paytype,"status" => 1))->get()->row();
                  if($checkforbonusexceed90k){
                      if($checkforbonusexceed90k->amt < 90000){
                          $totaltaxableamount = $totaltaxableamount - $checkforbonusexceed90k->amt;
                      }
                  } */


                if ($qry_tax_matrix) {
                    if($payclass == 128   || $payclass == 3077 || $payclass == 3078){
                        //GET TAXABLE BRACKET
                        $qry_cont = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75  AND payclass= 128
                                        AND $totaltaxableamount BETWEEN amtmin AND amtmax")->row();
                        $qry_cont_wo_bonuses= '';
                    }else{
                        $confidentialtaxamount = $totaltaxableamount;
                        //GET TAXABLE BRACKET
                        $qry_cont = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75 AND payclass = 1
                                        AND $confidentialtaxamount BETWEEN amtmin AND amtmax")->row();

                        $qry_cont_wo_bonuses = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75 AND payclass = 1
                                        AND $taxablewithoutcapping BETWEEN amtmin AND amtmax")->row();

                    }

                    if ($qry_cont) {

                        $data['amtmin'] = $qry_cont->amtmin;
                        $data['employeerate'] = $qry_cont->rateemployee;
                        //PECO computation
                        $data['totaltaxcomputednow'] = $totaltaxableamount;

                        $examt = (($totaltaxableamount - $qry_cont->amtmin) * $qry_cont->rateemployee) + $qry_cont->amtcont;
                        if($qry_cont_wo_bonuses){
                            $taxwithoutcapping = (($taxablewithoutcapping - $qry_cont_wo_bonuses->amtmin) * $qry_cont_wo_bonuses->rateemployee) + $qry_cont_wo_bonuses->amtcont;
                        }else{
                            $taxwithoutcapping = 0;
                        }
                        //computation in research
                        // $examt = ((($totaltaxableamount - $total_cont)- $qry_cont->amtmin) * $qry_cont->rateemployee) + $qry_cont->amtcont;


                        if ($payclass == 128   || $payclass == 3077 || $payclass == 3078) {
                            $class = "font-red-flamingo";

                            $deduct_arr[75] = array('contname' => $qry_tax_matrix->names, 'amt' => $examt, 'amtcomp' => 0, 'class' => $class);
                            $taxamt += $examt;

                            //checking for annualization tax
                            $checkforannualtax = $ci->db->select("amount")
                                ->from("payroll_anual_tax_distribution")
                                ->where(array("month" => $month , "year" => $year , "empid" =>$empid , "status" => 313))->get()->row();

                            if($checkforannualtax){
                                $taxamt = $checkforannualtax->amount;
                            }
                            $data['totaltax']  = $taxamt;
                            $transactions[] = array(
                                'amt' => $taxamt,
                                'type' => 75
                            );
                            $deduct_arr[75] = array('contname' => 'TAX', 'amt' => $taxamt, 'amtcomp' => 0, 'class' => $class);

                        } else {
                            $taxamt += $examt;
                            $data['totaltax']  = $taxamt;
                            //checking for annualization tax
                            $checkforannualtax = $ci->db->select("amount")->from("payroll_anual_tax_distribution")
                                ->where(array("month" => $month , "year" => $year , "empid" =>$empid , "status" => 313))->get()->row();

                            if($checkforannualtax){
                                $taxamt = $checkforannualtax->amount * 2;
                            }

                            $transactions[] = array(
                                'amt' => $taxamt,
                                'type' => 75
                            );
                            $deduct_arr[75] = array('contname' => 'TAX', 'amt' => $taxamt, 'amtcomp' => 0, 'class' => $class);
                        }
                    }
                }

                $total_deduction = $deductionamount + $taxamt + $additionaldeductions + $total_cont  + $loans;
                $totaldeductionwithoutcapping = $deductionamount + $taxwithoutcapping + $additionaldeductions + $total_cont  + $loans;
            }

        } else {
            $status = '<span class="label label-success">PAID</span>';
        }
    }


    $annualtaxarr = array();
    $annualgrossarr  = array();
    $annualnetarr  = array();
    $totalannualtax = 0;
    $totalannualgross = 0;
    $totalannualnet = 0;

    $totalfirsthalf = 0;
    $totalsecondhalf = 0;
    $totalspec = 0;
    $totaldist = 0;



    $netpay = (($earnings_amount + $totalannualnet)  - $total_deduction) ;
    if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
        // RANK N FILE
        $data['netpay'] = $netpay;

    } else {
        /*
            $get_employee_employment = $this->db->query("
                    SELECT datestart FROM prime_employee_main
                    WHERE sysid = $empid
                ")->get()->row();
        */

        $net_15 = ( $netpay / 2 );
        $net_30 = ( $netpay / 2 );
        $total_net = ( $net_15 + $net_30 );

        $data['net24'] = 0;
        $data['net15'] = $net_15;
        $data['net30'] = $net_30;
        $data['netpay'] = $total_net;
    }

    $data['totalfirsthalf'] = $totalfirsthalf;
    $data['totalsecondhalf'] = $totalsecondhalf;
    $data['totalspec'] = $totalspec;
    $data['totaldist'] = $totaldist;

    $data['status'] = $status;
    $data['deductions'] = $deduct_arr;
    $data['----'] = '------------------------------------------------';
    $data['confi_neypay'] = $netpay;
    $data['confi15'] = $netpay / 2;
    $data['confi30'] = $netpay / 2;
    $data['-----'] = '------------------------------------------------';
    $data['transactions'] = $transactions;
    $data['loansarr'] = $loansarr;
    $data['earningarr'] = $earningarr;
    $data['otherdeductarr'] = $otherdeductarr;
    $data['annualgrossarr'] = $annualgrossarr;
    $data['annualtaxarr'] = $annualtaxarr;
    $data['annualnetarr'] = $annualnetarr;
    $data['payclass'] = $payclass;
    $data['basic'] = $salary;
    $data['earnings'] = $earnings_amount;
    $data['premiums'] = $total_cont;
    $data['loans'] = $loans;
    $data['otherssub'] = $totalotherssub;
    $data['taxamt'] = $taxamt + $totalannualtax;
    $data['totalothersadd'] = $totalothersadd;
    $data['totalotherssub'] = $totalotherssub;
    $data['total_holiday'] = $total_holiday;
    $data['taxableamount'] = $totaltaxableamount;
    $data['nontaxableamount'] = $totalnontaxableamount;
    $data['taxabablebasic'] = $basic_taxable;
    $data['basicamoumt'] = $basic_amount;
    $data['totalsssloan'] = $totalsssloan;
    $data['totalhmoded'] = $totalhmoded;
    $data['totalhdmfloan'] = $totalhdmfloan;
    $data['totaldeda'] = $totaldeda;
    $data['totalpecewaloan'] = $totalpecewaloan;
    $data['totalcooploan'] = $totalcooploan;
    $data['totalpagibigadd'] = $totalpagibigadd;
    $data['totalotherdedn'] = $totalotherdedn;
    $data['totalelectric'] = $totalelectric;
    $data['totalmemins'] = $totalmemins;
    $data['totallwop'] = $totallwop;
    $data['totalcola'] = $totalcola;
    $data['totaltransallw'] = $totaltransallw;
    $data['totalrice'] = $totalrice;
    $data['totalholiday'] = $totalholiday;
    $data['nightdiff'] = $totalnightdiff;
    $data['otwithholiday'] = $otwholiday;
    $data['otweekend'] = $otweekend;
    $data['otweekdays'] = $otweekday;
    $data['totalactingallw'] = $totalactingallw;
    $data['totalotheradd'] = $totalotheradd;
    $data['ndot8hrs'] = $ndot8hrs;
    $data['add_trans_variable'] = $add_trans_variable;

    //for inserting in main
    $data['deductionamount'] = $total_deduction + $totalannualtax; //$total_deduction;

    return (object)$data;
    exit();

}

function compute_employee_netpay_temp($empid, $month = false, $year = false, $paytype = false, $paytypepopover = false, $payclass = false , $viewtype)
{
    // $paytype = 2;
    // QUERY DEDUCTION MONTHLY HERE
    $ci = &get_instance();
    $data = array();
    $total_deduction = 0;
    $deduct_arr = array();
    $emp_salary = get_employee_salary($empid);
    $emp_salary = ($emp_salary) ? $emp_salary : 0;
    $salary = 0;
    $taxamt = 0;
    $status = '';
    $total_cont = 0;
    $loans = 0;
    $totalotherssub = 0;
    $totalothersadd = 0;
    $totaltaxableamount = 0;
    $totalnontaxableamount = 0;
    $deductionamount = 0;
    $earnings_amount = 0;
    $basic_taxable = 0;
    $basic_amount = 0;
    $transactions = false;
    $loansarr = false;
    $earningarr = false;
    $otherdeductarr = false;
    $additionaldeductions = 0;
    $total_holiday = 0;

    $taxablewithoutcapping = 0;
    $taxwithoutcapping = 0;
    $totaldeductionwithoutcapping = 0;

    if ($month && $year && $payclass) {

            $basic = ($emp_salary) ? $emp_salary : 0;
            $trn_query = get_employee_transactions($empid, $month, $year, $paytype, $paytypepopover, $payclass, $viewtype);
            $data['loansintransactions'] = $trn_query->loans;
            $trn_num_rows = count($trn_query->transactions);
            if ($trn_num_rows > 0) {
                $transactions    =  $trn_query->transactions;
                $loansarr        =  $trn_query->loansarr;
                $earningarr        =  $trn_query->earningarr;
                $otherdeductarr        =  $trn_query->otherdeductarr;
                $loans           =  $trn_query->loans;
                $totalothersadd  =  $trn_query->totalothersadd;
                $totalotherssub  =  $trn_query->totalotherssub;
                $deductionamount += $totalotherssub;
                $totaltaxableamount = $trn_query->totaltaxableamount;
                $totalnontaxableamount = $trn_query->totalnontaxableamount;
                $total_holiday = $trn_query->totalholiday;
                $taxablewithoutcapping = $trn_query->taxwithoutcapping;

                $confi15 = $trn_query->confi15;
                $confi30 = $trn_query->confi30;


            } else {
                $salary = $basic;
            }

            if($payclass == 128){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check for additional deductions
            $checkadditionaldeductions = $ci->db->select("SUM(pmtb.amount) AS totaldeductions")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt", "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("payroll_matrix as pm","pm.typesid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "pm.codes" => 'others' , "pm.functions" => 0 , "pm.effects" => 0))
                ->get()->row();
            $additionaldeductions += ($checkadditionaldeductions) ? $checkadditionaldeductions->totaldeductions:'';

            if($payclass == 128){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check additional loans
            $checkadditionalloans = $ci->db->select("SUM(pmtb.amount) AS totalloans")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt", "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("payroll_matrix as pm","pm.typesid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "pm.codes" => 'loans'))
                ->get()->row();
            $loans += ($checkadditionalloans) ? $checkadditionalloans->totalloans : '';

            if($payclass == 128){
                $ci->db->where(array("paytype" => $paytype));
            }
            //check additional cont
            $checkadditionalpremium = $ci->db->select("SUM(pmtb.amount) AS totalpremiumamount")
                ->from("payroll_manual_transactions_breakdown as pmtb")
                ->join("payroll_manual_transactions as pmt" , "pmt.empid = pmtb.empid && pmt.sysid = pmtb.groupid","left")
                ->join("prime_types_parameter as ptp" , "ptp.sysid = pmt.tsysid","left")
                ->where(array("pmtb.month" => $month,"pmtb.year" => $year , "pmtb.status" => 313 , "pmtb.empid" => $empid , "ptp.codes" => 'EMPCONT'))
                ->get()->row();
            $total_cont += ($checkadditionalpremium) ? $checkadditionalpremium->totalpremiumamount : '';

            // QUERY FROM CONTRIBUTION TABLE TABLE
            $qry_deduction_matrix = $ci->db->query("SELECT tp.names, tp.sysid
                        FROM trn_employee_deduction_matrix AS dm
                        LEFT JOIN prime_types_parameter AS tp ON tp.sysid = dm.deductid
                        WHERE dm.empid = $empid AND dm.status = 1 AND tp.sysid != 75 AND tp.sysid != 73
                   ");

            if ($qry_deduction_matrix) {
                foreach ($qry_deduction_matrix->result() as $row) {
                    $class = '';
                    $tpid = $row->sysid;

                    if($payclass != 128){
                        $paytype = 1;
                    }
                    $data['salarycont'] = ($emp_salary) ? $emp_salary : 0;
                    $qry_cont = $ci->db->query("SELECT amtcont, deductible, rateemployee, rateemployer, var FROM prime_contribution_matrix WHERE conttype = $tpid AND status = 1 AND  $emp_salary BETWEEN amtmin AND amtmax")->row();
                    if ($qry_cont) {
                        $ssscont = 0;
                        $philhealthcont = 0;


                        if($tpid == 72){
                            $ssscont += $qry_cont->amtcont * $qry_cont->rateemployee;
                            $data['ssscont'] =  $ssscont;
                        }
                        if($tpid == 73){
                            $philhealthcont +=  $qry_cont->amtcont * $qry_cont->rateemployee;
                            //$data['philhealth'] = $philhealthcont;
                            $data['philhealth'] = 0;
                        }


                        if ($qry_cont->var == 1) {
                            $empe_amt = $qry_cont->amtcont * $qry_cont->rateemployee;
                            $comp_amt = $qry_cont->amtcont * $qry_cont->rateemployer;
                        } else {
                            $empe_amt = $qry_cont->amtcont;
                            $comp_amt = 0;
                        }
                        $row_amt = $empe_amt;
                        if ($trn_query->payclass == 128) {
                            if ($paytype == 1) {

                                if ($qry_cont->deductible == 1) {
                                    $total_cont += $row_amt;
                                    $class = "font-red-flamingo";
                                }
                                $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                                $transactions[] = array(
                                    'amt' => $empe_amt,
                                    'type' => $tpid,
                                    'codes' => '',
                                    'functions' => '',
                                    'effects' => '',
                                    'notax' => '',
                                    'capping' => '',
                                    'payspec' => '',
                                    'names' => '',
                                );
                            }
                        } else {
                            if ($qry_cont->deductible == 1) {
                                $total_cont += $row_amt;
                                $class = "font-red-flamingo";
                            }
                            $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                            $transactions[] = array(
                                'amt' => $empe_amt,
                                'type' => $tpid,
                                'codes' => '',
                                'functions' => '',
                                'effects' => '',
                                'notax' => '',
                                'capping' => '',
                                'payspec' => '',
                                'names' => '',
                            );
                        }

                    } else {
                        $qry_cont = $ci->db->query("SELECT amtcont, deductible, rateemployee, rateemployer, var FROM prime_contribution_matrix WHERE conttype = $tpid AND end = 1 AND status = 1")->row();
                        if ($qry_cont) {
                            $pagibigcont = 0;
                            if($tpid == 74){
                                $pagibigcont += $qry_cont->amtcont * $qry_cont->rateemployee;
                                $data['pagibig'] =  $pagibigcont;
                            }
                            if ($qry_cont->var == 1) {
                                $empe_amt = $qry_cont->amtcont * $qry_cont->rateemployee;
                                $comp_amt = $qry_cont->amtcont * $qry_cont->rateemployer;
                            } else {
                                $empe_amt = $qry_cont->amtcont;
                                $comp_amt = 0;
                            }
                            $row_amt = $empe_amt;

                            if ($trn_query->payclass == 128) {
                                if ($paytype == 1) {
                                    if ($qry_cont->deductible == 1) {
                                        $total_cont += $row_amt;
                                        $class = "font-red-flamingo";
                                    }
                                    $transactions[] = array(
                                        'amt' => $empe_amt,
                                        'type' => $tpid,
                                        'codes' => '',
                                        'functions' => '',
                                        'effects' => '',
                                        'notax' => '',
                                        'capping' => '',
                                        'payspec' => '',
                                        'names' => '',
                                    );
                                    $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                                }
                            } else {

                                if ($qry_cont->deductible == 1) {
                                    $total_cont += $row_amt;
                                    $class = "font-red-flamingo";
                                }
                                $transactions[] = array(
                                    'amt' => $empe_amt,
                                    'type' => $tpid,
                                    'codes' => '',
                                    'functions' => '',
                                    'effects' => '',
                                    'notax' => '',
                                    'capping' => '',
                                    'payspec' => '',
                                    'names' => '',
                                );
                                $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => $empe_amt, 'amtcomp' => $comp_amt, 'class' => $class);
                            }
                        } else {
                            $deduct_arr[$tpid] = array('contname' => $row->names, 'amt' => 0, 'class' => $class);
                        }
                    }
                }

                // RANK AND FILE
                if ($payclass == 128) {
                    $salary = $basic;
                    $basic = $basic;
                    if ($paytype == 1) {
                        if($paytypepopover != 2){
                            $basic_amount = ($basic / 2);
                        }else{
                            $basic_amount = ($basic / 2);
                        }
                        if($paytypepopover == 0){
                            $totalotherssub = $totalotherssub / 2;
                        }

                        $basic_taxable = $basic_amount + $totaltaxableamount;
                        $earnings_amount = ($basic_amount + $totalothersadd); // NOTE: (+) $totalotherssub because negative value;
                    }else{
                        $basic_amount = ($basic / 2);
                        $basic_taxable = $basic_amount + $totaltaxableamount;
                        $earnings_amount = $basic_amount + $totalothersadd; // NOTE: (+) $totalotherssub because negative value;
                    }
                } else {
                    /*
                    $getenddate = $ci->db->select("dateend")->from("prime_employee_main")
                        ->where(array("sysid" => $empid))->get()->row();
                    if($getenddate) {
                        $date_15 = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-1'; // or your date as well
                        $earlier = new DateTime($getenddate->dateend);
                        $later = new DateTime($date_15);
                        $diff = $later->diff($earlier)->format("%a");
                        if($diff>15) {
                            $basic = $basic;
                        }else{
                            $basic = $basic / 2;
                        }
                    }else{
                        $basic = $basic;
                    }
                    */
                    $salary = $basic;
                    $basic_amount = $basic;
                    $basic_taxable = $basic + $totaltaxableamount;
                    // NOTE: (+) $totalotherssub because negative value;

                    if($viewtype == 1){
                        $earnings_amount = ($basic_amount + $totalothersadd);
                    }else if($viewtype == 4){
                        $earnings_amount = ($totalothersadd);
                    }


                }
                if($month == 1){
                    $monthlate = 12;
                    $yearlate = $year - 1;
                }else{
                    $monthlate = $month - 1;
                    $yearlate = $year;
                }
                //check for LWOP


                $checkforlwoppay = $ci->db->select("amt")->from("payroll_transactions")
                    ->where(array("typesid" => 262 , "empid" => $empid , "months" => $month,
                        "years" => $year , "payspec" => $paytype,"status" => 1))->get()->row();
                $lwoppay = ($checkforlwoppay) ? $checkforlwoppay->amt : '';


                $basic_amount = (double)$basic_amount - (double)$lwoppay;



                /*    $ceilingamt =  $basic_amount * 3;
                    if($ceilingamt > 90000){
                        $taxxable_bonus  = $ceilingamt - 90000;
                        $montlyapplication = $taxxable_bonus / 12;
                        $totaltaxableamount = $totaltaxableamount + $basic_amount + $montlyapplication;
                    }else{
                        $totaltaxableamount = $totaltaxableamount + $basic_amount;
                    } */

                $ceilingamt =  $basic_amount + $totaltaxableamount;
                if($ceilingamt > 90000){
                    $totaltaxableamount  = $ceilingamt - 90000;
                }else{
                    $totaltaxableamount = $totaltaxableamount + $basic_amount;
                }

                $taxablewithoutcapping = $taxablewithoutcapping + $basic_amount;

                $qry_tax_matrix = $ci->db->query("SELECT tp.names, tp.sysid
                    FROM trn_employee_deduction_matrix AS dm
                    LEFT JOIN prime_types_parameter AS tp ON tp.sysid = dm.deductid
                    WHERE dm.empid = $empid AND dm.status = 1 AND tp.sysid = 75
                   ")->row();
                $taxamt = 0;

                //this is temporary we must find the bonus entry and check if exceed 90k the if not then do not include in taxable
                /*  $checkforbonusexceed90k = $ci->db->select("amt")->from("payroll_transactions")
                      ->where(array("typesid" => 264 , "empid" => $empid , "months" => $month,
                          "years" => $year , "payspec" => $paytype,"status" => 1))->get()->row();
                  if($checkforbonusexceed90k){
                      if($checkforbonusexceed90k->amt < 90000){
                          $totaltaxableamount = $totaltaxableamount - $checkforbonusexceed90k->amt;
                      }
                  } */


                if ($qry_tax_matrix) {
                    if($payclass == 128){
                        //GET TAXABLE BRACKET
                        $qry_cont = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75  AND payclass= 128
                                        AND $totaltaxableamount BETWEEN amtmin AND amtmax")->row();
                        $qry_cont_wo_bonuses= '';
                    }else{
                        $confidentialtaxamount = $totaltaxableamount;
                        //GET TAXABLE BRACKET
                        $qry_cont = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75 AND payclass = 1
                                        AND $confidentialtaxamount BETWEEN amtmin AND amtmax")->row();

                        $qry_cont_wo_bonuses = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75 AND payclass = 1
                                        AND $taxablewithoutcapping BETWEEN amtmin AND amtmax")->row();

                    }

                    if ($qry_cont) {

                        $data['amtmin'] = $qry_cont->amtmin;
                        $data['employeerate'] = $qry_cont->rateemployee;
                        //PECO computation
                        $data['totaltaxcomputednow'] = $totaltaxableamount;

                        $examt = (($totaltaxableamount - $qry_cont->amtmin) * $qry_cont->rateemployee) + $qry_cont->amtcont;
                        if($qry_cont_wo_bonuses){
                            $taxwithoutcapping = (($taxablewithoutcapping - $qry_cont_wo_bonuses->amtmin) * $qry_cont_wo_bonuses->rateemployee) + $qry_cont_wo_bonuses->amtcont;
                        }else{
                            $taxwithoutcapping = 0;
                        }
                        //computation in research
                        // $examt = ((($totaltaxableamount - $total_cont)- $qry_cont->amtmin) * $qry_cont->rateemployee) + $qry_cont->amtcont;


                        if ($payclass == 128) {
                            $class = "font-red-flamingo";

                            $deduct_arr[75] = array('contname' => $qry_tax_matrix->names, 'amt' => $examt, 'amtcomp' => 0, 'class' => $class);
                            $taxamt += $examt;

                            //checking for annualization tax
                            $checkforannualtax = $ci->db->select("amount")->from("payroll_anual_tax_distribution")
                                ->where(array("month" => $month , "year" => $year , "empid" =>$empid , "status" => 313))->get()->row();

                            if($checkforannualtax){
                                $taxamt = $checkforannualtax->amount;
                            }

                            $transactions[] = array(
                                'amt' => $taxamt,
                                'type' => 75,
                                'codes' => '',
                                'functions' => '',
                                'effects' => '',
                                'notax' => '',
                                'capping' => '',
                                'payspec' => '',
                                'names' => '',
                            );
                            $deduct_arr[75] = array('contname' => 'TAX', 'amt' => $taxamt, 'amtcomp' => 0, 'class' => $class);

                        } else {
                            $taxamt += $examt;

                            //checking for annualization tax
                            $checkforannualtax = $ci->db->select("amount")->from("payroll_anual_tax_distribution")
                                ->where(array("month" => $month , "year" => $year , "empid" =>$empid , "status" => 313))->get()->row();

                            if($checkforannualtax){
                                $taxamt = $checkforannualtax->amount * 2;
                            }

                            $transactions[] = array(
                                'amt' => $taxamt,
                                'type' => 75,
                                'codes' => '',
                                'functions' => '',
                                'effects' => '',
                                'notax' => '',
                                'capping' => '',
                                'payspec' => '',
                                'names' => '',
                            );
                            $deduct_arr[75] = array('contname' => 'TAX', 'amt' => $taxamt, 'amtcomp' => 0, 'class' => $class);
                        }
                    }
                }

                $total_deduction = $deductionamount + $taxamt + $additionaldeductions + $total_cont  + $loans;
                $totaldeductionwithoutcapping = $deductionamount + $taxwithoutcapping + $additionaldeductions + $total_cont  + $loans;
            }
    }


    $annualtaxarr = array();
    $annualgrossarr  = array();
    $annualnetarr  = array();
    $totalannualtax = 0;
    $totalannualgross = 0;
    $totalannualnet = 0;

    $totalfirsthalf = 0;
    $totalsecondhalf = 0;
    $totalspec = 0;
    $totaldist = 0;



    $netpay = (($earnings_amount + $totalannualnet)  - $total_deduction) ;
    if ($payclass != 128) {

        $net_15 = ($netpay / 2);
        $net_30 = ($netpay / 2);
        $total_net = ($net_15 + $net_30);

        $data['net24'] = 0;
        $data['net15'] = $net_15;
        $data['net30'] = $net_30;
        $data['netpay'] = $total_net;

    } else {
        // RANK N FILE
        $data['netpay'] = $netpay;
    }
    $data['totalfirsthalf'] = $totalfirsthalf;
    $data['totalsecondhalf'] = $totalsecondhalf;
    $data['totalspec'] = $totalspec;
    $data['totaldist'] = $totaldist;

    $data['status'] = $status;
    $data['deductions'] = $deduct_arr;
    $data['----'] = '------------------------------------------------';
    $data['confi_neypay'] = $netpay;
    $data['confi15'] = $netpay / 2;
    $data['confi30'] = $netpay / 2;
    $data['-----'] = '------------------------------------------------';
    $data['transactions'] = $transactions;
    $data['loansarr'] = $loansarr;
    $data['earningarr'] = $earningarr;
    $data['otherdeductarr'] = $otherdeductarr;
    $data['annualgrossarr'] = $annualgrossarr;
    $data['annualtaxarr'] = $annualtaxarr;
    $data['annualnetarr'] = $annualnetarr;
    $data['payclass'] = $payclass;
    $data['basic'] = $salary;
    $data['earnings'] = $earnings_amount + $totalannualgross;
    $data['premiums'] = $total_cont;
    $data['loans'] = $loans;
    $data['otherssub'] = $totalotherssub;
    $data['taxamt'] = $taxamt + $totalannualtax;
    $data['deductionamount'] = $total_deduction + $totalannualtax; //$total_deduction;
    $data['totalothersadd'] = $totalothersadd;
    $data['totalotherssub'] = $totalotherssub;
    $data['total_holiday'] = $total_holiday;
    $data['taxableamount'] = $totaltaxableamount;
    $data['nontaxableamount'] = $totalnontaxableamount;
    $data['taxabablebasic'] = $basic_taxable;
    $data['basicamoumt'] = $basic_amount;

    return (object)$data;
    exit();

}
//DRAW CALENDAR
if (!function_exists('draw_employee_calendar')) {
    /* draws a calendar */
    function draw_employee_calendar($month, $year, $empid) {

        $ci = &get_instance();
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
            $calendar .= '<div class="day-number">' . $list_day .'</div>';

            $date = $year.'-'.$month.'-'.$list_day;
            $this_date = date("l", strtotime($date));
            if ($this_date == "Saturday" || $this_date == "Sunday") {
                $weekend = true;
            } else {
                $weekend = false;
            }
            /// QUERY SCHEDULE FROM TIME SHIFT AND EMPID

            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! * */
            $calendar .= str_repeat('<p> </p>', 2);
            $date_set = $year.'-'.$month.'-'.str_pad($list_day, 2, '0', STR_PAD_LEFT). ' 00:00.00';

            $weekday = strtoupper(date('D', strtotime($date_set))); // note: first arg to date() is lower-case L

            $custom_day_num = custom_days_num($weekday);

            $sql = $ci->db->select("petsm.amtimein,petsm.pmtimein,petsm.amtimeout,petsm.pmtimeout,pemw.codes,pemw.logcnt")
                ->from("prime_employee_time_shift_matrix as petsm")
                ->join("prime_employee_main_workshift as pemw","pemw.sysid = petsm.shiftid","left")
                ->join("prime_employee_main_workshift_matrix as pemwm","pemwm.workshift_id = pemw.sysid","left")
                ->where(array("petsm.days" => $custom_day_num,"pemwm.empid" => $empid,"pemwm.status" => 1))
                ->get()->row();
            $dateval = $year.'-'.$month.'-'.$list_day;

            $checkschedule = $ci->db->query("SELECT tsr.sysid,tsrt.amtimein,tsrt.amtimeout,tsrt.pmtimein,tsrt.pmtimeout,tsrt.logscnt,tsrt.logtype
FROM trn_schedule_requests as tsr
LEFT JOIN trn_schedule_requests_time as tsrt ON tsrt.schedid = tsr.sysid
WHERE tsr.empid = '".$empid."' AND date('$dateval') >= tsr.fromdate  AND date('$dateval') <= tsr.todate AND tsrt.status = 301")->row();

            if($checkschedule){
                if($checkschedule->logscnt == 4){
                    if($weekend == false){
                        $amin = date_formating($checkschedule->amtimein, 'H:m:i', 'h:iA');
                        $amout = date_formating($checkschedule->amtimeout, 'H:m:i', 'h:iA');
                        $calendar .= '<span class="timelog">'.$amin.'-'.$amout.'</span>';

                        $pmin = date_formating($checkschedule->pmtimein, 'H:m:i', 'h:iA');
                        $pmout = date_formating($checkschedule->pmtimeout, 'H:m:i', 'h:iA');
                        $calendar .= '<span class="timelog">'.$pmin.'-'.$pmout.'</span>';
                    }
                }else{
                    if($checkschedule->logscnt == 2 && $checkschedule->logtype == 0){
                        //AM IN  PM OUT
                        $calendar .= '<span class="timelog">'.$checkschedule->amtimein.' - '.$checkschedule->pmtimeout.'</span>';
                    }else if($checkschedule->logscnt == 2 && $checkschedule->logtype == 1){
                        //AM IN AM OUT
                        $calendar .= '<span class="timelog">'.$checkschedule->amtimein.' - '.$checkschedule->amtimeout.'</span>';
                    }else if($checkschedule->logscnt == 2 && $checkschedule->logtype == 2){
                        // PM IN AND PM OUT
                        $calendar .= '<span class="timelog">'.$checkschedule->pmtimein.' - '.$checkschedule->pmtimeout.'</span>';
                    }
                }
            }else{

                $checkforgroupshced =$ci->db->query("SELECT pemw.am_start , pemw.am_end , pemw.pm_start , pemw.pm_end, pemw.logcnt, pemw.logtype FROM trn_employee_workshift_group as tewg 
LEFT JOIN prime_employee_main_workshift as pemw ON pemw.sysid = tewg.workshiftid
WHERE tewg.empid = '".$empid."' AND date('$dateval') >= tewg.fromdate  AND date('$dateval') <= tewg.todate AND tewg.status = 301")->row();
                $data['errorforcheckinggroupsched'] = $ci->db->_error_message();
                if($checkforgroupshced){
                    if($checkforgroupshced->logcnt == 4){
                        if($weekend == false){
                            $amin = date_formating($checkforgroupshced->am_start, 'H:m:i', 'h:iA');
                            $amout = date_formating($checkforgroupshced->am_end, 'H:m:i', 'h:iA');
                            $calendar .= '<span class="timelog">'.$amin.'-'.$amout.'</span>';

                            $pmin = date_formating($checkforgroupshced->pm_start, 'H:m:i', 'h:iA');
                            $pmout = date_formating($checkforgroupshced->pm_end, 'H:m:i', 'h:iA');
                            $calendar .= '<span class="timelog">'.$pmin.'-'.$pmout.'</span>';
                        }

                    }else{
                        if($checkforgroupshced->logcnt == 2 && $checkforgroupshced->logtype == 0){
                            //AM IN  PM OUT
                            $calendar .= '<span class="timelog">'.$checkforgroupshced->am_start.' - '.$checkforgroupshced->pm_end.'</span>';
                        }else if($checkforgroupshced->logcnt == 2 && $checkforgroupshced->logtype == 1){
                            //AM IN AM OUT
                            $calendar .= '<span class="timelog">'.$checkforgroupshced->am_start.' - '.$checkforgroupshced->am_end.'</span>';
                        }else if($checkforgroupshced->logcnt == 2 && $checkforgroupshced->logtype == 2){
                            // PM IN AND PM OUT
                            $calendar .= '<span class="timelog">'.$checkforgroupshced->pm_start.' - '.$checkforgroupshced->pm_end.'</span>';
                        }
                    }
                }else{
                    if($sql){
                        if($sql->logcnt == 4){
                            if($weekend == false){
                                $amin = $sql->amtimein;
                                $amout = $sql->amtimeout;
                                $calendar .= '<span class="timelog">'.$amin.'-'.$amout.'</span>';

                                $pmin = $sql->pmtimein;
                                $pmout = $sql->pmtimeout;
                                $calendar .= '<span class="timelog">'.$pmin.'-'.$pmout.'</span>';
                            }
                        }else{
                            $amin =$sql->amtimein;
                            $calendar .= '<span class="timelog">'.$amin.'</span>';

                            $pmout = $sql->pmtimeout;
                            $calendar .= '<span class="timelog">'.$pmout.'</span>';
                        }
                    }
                }
            }


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
//CHECK EMPLOYEE SCHED
if (!function_exists('checkempsched')) {

    function checkempsched($empid, $days, $months , $years , $stat){
        $ci = &get_instance();
        $undertimeamout = '';
        $undertimepmout = '';
        $specifiedTime = '00:00:00';
        $specifiedTime2 = '00:00:00';
        $codes = '';
        $logcount = 0;
        $desc = '';
        $daytype = 0;
        $typehalf = 0;

        $date = $years.'-'.$months.'-'.$days;
        $date_set = $years.'-'.$months.'-'.$days;
        $weekday = strtoupper(date('D', strtotime($date_set))); // WED

        if($days > 15){
            $typehalf = 2;
        }else{
            $typehalf = 1;
        }

        $day_arr = array(
            '1' => 'MON',
            '2' => 'TUE',
            '3' => 'WED',
            '4' => 'THU',
            '5' => 'FRI',
            '6' => 'SAT',
            '7' => 'SUN'
        );
        $dayindex = 0;
        foreach ($day_arr as $key => $weekdays){
            if($weekdays == $weekday){
                $dayindex = $key;
            }
        }

        //FOR SB SCHEDULE CHECKING
        $checksbschedule = $ci->db->query("SELECT  pemw.daytype , pemw.am_start, pemw.am_end , pemw.pm_start , pemw.pm_end, pemw.desc , pemw.codes,pemw.logcnt
FROM trn_employee_schedule as tes
JOIN prime_employee_main_workshift as pemw ON pemw.sysid =tes.workshiftid
WHERE tes.status = 1 AND tes.dayofweek = {$dayindex} AND tes.empid = {$empid}
AND '".$date."' BETWEEN tes.fromdate AND tes.todate")->row();

        if($checksbschedule){

            $codes = $checksbschedule->codes;
            $logcount = $checksbschedule->logcnt;
            $desc = $checksbschedule->desc;
            $daytype = $checksbschedule->daytype;

            if($checksbschedule->logcnt == 2){
                $specifiedTime = $checksbschedule->am_start;
                $specifiedTime2 = $checksbschedule->pm_end;
            }else if($checksbschedule->logcnt == 4){
                $specifiedTime = $checksbschedule->am_start;
                $specifiedTime2 = $checksbschedule->pm_start;
            }
        }

        //CHECK FOR TS

        $checkfortssched = $ci->db->select("ets.shiftid , pemw.daytype , pemw.am_start, pemw.am_end , pemw.pm_start , pemw.pm_end, pemw.desc , pemw.codes,pemw.logcnt")->from("emp_team_schedule as ets")
            ->join("prime_employee_main_workshift as pemw" , "pemw.sysid =ets.shiftid")
            ->where(array("ets.month" => $months , "ets.year" => $years,
                "ets.type" => $typehalf , strtolower($weekday) => $empid , "ets.status" => 1))
            ->get()->row();
        $data['mysqlerror'] = $ci->db->_error_message();
        if($checkfortssched){
            $codes = $checkfortssched->codes;
            $logcount = $checkfortssched->logcnt;
            $desc = $checkfortssched->desc;
            $daytype = $checkfortssched->daytype;
            if($checkfortssched->logcnt == 2){
                $specifiedTime = $checkfortssched->am_start;
                $specifiedTime2 = $checkfortssched->pm_end;
            }else if($checkfortssched->logcnt == 4){
                $specifiedTime = $checkfortssched->am_start;
                $specifiedTime2 = $checkfortssched->pm_start;
            }
        }

        if($checksbschedule == false && $checkfortssched == false){
            //get default workshift
            $getdefaultshift = $ci->db->select("petsm.amtimein,petsm.pmtimein,petsm.amtimeout,petsm.pmtimeout,pemw.codes,pemw.logcnt,pemw.logtype,pemw.desc,pemw.daytype")
                ->from("prime_employee_time_shift_matrix as petsm")
                ->join("prime_employee_main_workshift as pemw","pemw.sysid = petsm.shiftid","left")
                ->join("prime_employee_main_workshift_matrix as pemwm","pemwm.workshift_id = pemw.sysid","left")
                ->where(array("petsm.days" => $dayindex,"pemwm.empid" => $empid,"pemwm.status" => 1))
                ->get()->row();
            if($getdefaultshift){
                $codes = $getdefaultshift->codes;
                $logcount = $getdefaultshift->logcnt;
                $desc = $getdefaultshift->desc;
                $daytype = $getdefaultshift->daytype;
                if($getdefaultshift->logcnt == 2){
                    $specifiedTime = $getdefaultshift->amtimein;
                    $specifiedTime2 = $getdefaultshift->pmtimeout;
                }else if($getdefaultshift->logcnt == 4){
                    $specifiedTime = $getdefaultshift->amtimein;
                    $specifiedTime2 = $getdefaultshift->pmtimein;
                }
            }
        }

        $data['sepcfiedTime'] = $specifiedTime;
        $data['sepcfiedTime2'] = $specifiedTime2;
        $data['undertimeamout'] = $undertimeamout;
        $data['undertimepmout'] = $undertimepmout;
        $data['codes'] = $codes;
        $data['logcount'] = $logcount;
        $data['desc'] = $desc;
        $data['daytype'] = $daytype;
        return (object)$data;
    }
}
//CONVERT SECONDS TO TIME
if (!function_exists('secondsToTime')) {
    function secondsToTime($seconds , $full) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        if($full == true){
            return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes');
        }else{
            return $dtF->diff($dtT)->format('%D - %H - %I');
        }

    }
}
//CONVERT MINUTES TO TIME
if (!function_exists('convertminutetotimeformat')) {

    function convertminutetotimeformat($minutesval){
        $hours = floor($minutesval / 60);
        $minutes = ($minutesval % 60);
        $seconds = floor(($minutesval * 60) % 60) ;
        return sprintf('%02d:%02d:%02d', $hours, $minutes,$seconds);
    }
}
//CONVERTING TIME TO MINUTES
if (!function_exists('converttimetominutes')) {
    function converttimetominutes($time)
    {
        if (strstr($time, ':')) {
            # Split hours and minutes.
            $separatedData = explode(':', $time);

            $minutesInHours = $separatedData[0] * 60;
            $minutesInDecimals = $separatedData[1];

            $totalMinutes = $minutesInHours + $minutesInDecimals;
        } else {
            $totalMinutes = $time * 60;
        }
        return $totalMinutes;
    }
}

// GET PAYROLL AMOUNT
if(!function_exists('get_payroll_trn_amount')) {
    function get_payroll_trn_amount($payrollid, $trntype) {
        $ci = &get_instance();
        $sql = $ci->db->select("SUM(amt) AS amt")->from("payroll_reports_trn")
            ->where(array("payrollid" => $payrollid, "trntype" => $trntype, "status" => 1))->get()->row();
        return ($sql) ? $sql : 0;
    }
}


//GET TYPES ID AMOUNT IN PAYROLL
if(!function_exists('get_per_ccid_amts')) {
    function get_per_ccid_amts($ccid, $groupid)
    {
        $ci = &get_instance();
        $basic = 0;
        $sumdeptssscont = 0;
        $sumdepthdmfcont = 0;
        $sumdeptsssloan = 0;
        $sumdepthdmfloan = 0;
        $sumdeptcignainsurance = 0;
        $sumdeptagencyunions = 0;
        $sumdeptpecewa = 0;
        $sumdeptpecewaloan = 0;
        $sumdeptpagibigad = 0;
        $sumdepthmodedn = 0;
        $sumdeptelectbill = 0;
        $sumdeptmemins = 0;
        $sumdeptlwop = 0;
        $sumdeptpecoloan = 0;
        $sumdeptdedaloan = 0;
        $sumdeptdeda = 0;
        $sumdeptotherdedn = 0;
        $sumdeptbasetax = 0;
        $sumdeptcooploan = 0;
        $totaldeptbasetax = 0;
        $totalearnings = 0;
        $totaldepttotaldedn = 0;
        $totaldeptbasicrate = 0;
        $totalnet = 0;


        //earnings
        $cola = 0;
        $ricesubsi = 0;
        $holidaypay = 0;
        $trans_allw = 0;
        $nitediff = 0;
        $otpayweekends = 0;
        $otpayweekdays = 0;
        $otpaywithholiday = 0;
        $otpay = 0;
        $actingallw = 0;
        $otheradd = 0;
        $bonus = 0;
        $totalotheradd = 0;

        $fetchtotals = $ci->db->select("pem.sysid ,  prg.years , prg.months , prg.payclass ,prm.empid ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net, prt.payrollid , p.firstname , p.lastname ")
            ->from("payroll_reports_main as prm")
            ->join("prime_employee_costcenter as pec", "pec.empid = prm.empid", "left")
            ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
            ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
            ->join("prime_employee_main_payclass as pemp", "pemp.emp_id  = prm.empid", "left")
            ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
            ->join("person as p", "p.sysid = pem.personid", "left")
            ->where(array("pec.ccid" => $ccid,  "pec.type" => 1, "prm.groupid" => $groupid, "pec.status" => 1))
            ->group_by("prg.years , prg.months , prg.payclass ,prm.empid ,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net,prt.payrollid , p.firstname , p.lastname, prm.ccid,")
            ->order_by("p.lastname", "asc")
            ->get();

        $data['totals'][$ccid] = $ci->db->last_query();

        if ($fetchtotals->num_rows() > 0) {
            if ($fetchtotals->row()->payclass == 128) {
                $payclasstitle = 'RANK AND FILE PAYROLL';
            } else {
                $payclasstitle = 'CONFIDENTIAL PAYROLL';
            }


            foreach ($fetchtotals->result() as $totals) {

                $sumdeptpecewaloan_sum = 0;
                $arr_emp[] = $totals;

                $basic += $totals->basic;


                $totaldeptbasetax += $totals->tax;
                $totalearnings += $totals->earnings;

                $totaldeptbasicrate += $totals->basic;
                $totalnet += $totals->net;
                //  $totalnet += $totals->net;

                $sumdeptbasetax += $totals->tax;
                $totaldepttotaldedn += $totals->deductions;


                $getssscont = $ci->db->select("SUM(amt) AS sssconttotal")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 72, "status" => 1))->get()->row();
                $sumdeptssscont += ($getssscont) ? number_format(($getssscont->sssconttotal), 6, '.', '') : 0;


                $gethdmfcont = $ci->db->select("SUM(amt) AS totalhdmfcont")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 74, "status" => 1))->get()->row();
                $sumdepthdmfcont += ($gethdmfcont) ? number_format(($gethdmfcont->totalhdmfcont), 2, '.', '') : 0;

                $getsssloan = $ci->db->select("SUM(amt) AS sssloantotal")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 257, "status" => 1))->get()->row();
                $sumdeptsssloan += ($getsssloan) ? ($getsssloan->sssloantotal) : 0;

                $gethdmfloan = $ci->db->select("SUM(amt) AS totalhdmfloan")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 258, "status" => 1))->get()->row();
                $sumdepthdmfloan += ($gethdmfloan) ? number_format(($gethdmfloan->totalhdmfloan), 2, '.', '') : 0;

                // PCWEA
                $getcignainsurance = $ci->db->select("amt AS totalcignainsurance")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 3007, "status" => 1))->get()->row();
                $sumdeptcignainsurance += ($getcignainsurance) ? number_format(($getcignainsurance->totalcignainsurance), 2, '.', '') : 0;

                $getagencyunions = $ci->db->select("amt AS totalagencyunions")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 3006, "status" => 1))->get()->row();
                $sumdeptagencyunions += ($getagencyunions) ? number_format(($getagencyunions->totalagencyunions), 2, '.', '') : 0;

                $getpecewaloan = $ci->db->select("amt AS totalpecewaloan")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 254, "status" => 1))->get()->row();
                $sumdeptpecewa += ($getpecewaloan) ? number_format(($getpecewaloan->totalpecewaloan), 2, '.', '') : 0;

                $sumdeptpecewaloan = ($sumdeptcignainsurance + $sumdeptagencyunions + $sumdeptpecewa);

                $getpagibigad = $ci->db->select("SUM(amt) AS totalpagibigad")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 259, "status" => 1))->get()->row();
                $sumdeptpagibigad += ($getpagibigad) ? number_format(($getpagibigad->totalpagibigad), 2, '.', '') : 0;

                $gethmodedn = $ci->db->select("SUM(amt) AS totalhmodedn")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 260, "status" => 1))->get()->row();
                $sumdepthmodedn += ($gethmodedn) ? number_format(($gethmodedn->totalhmodedn), 2, '.', '') : 0;

                $getelectbill = $ci->db->select("SUM(amt) AS totalelectbill")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 256, "status" => 1))->get()->row();
                $sumdeptelectbill += ($getelectbill) ? number_format(($getelectbill->totalelectbill), 2, '.', '') : 0;

                $getmemins = $ci->db->select("SUM(amt) AS totalmemins")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 3009, "status" => 1))->get()->row();
                $sumdeptmemins += ($getmemins) ? number_format(($getmemins->totalmemins), 2, '.', '') : 0;

                $getlwop = $ci->db->select("SUM(amt) AS totallwop")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 262, "status" => 1))->get()->row();
                $sumdeptlwop += ($getlwop) ? number_format(($getlwop->totallwop), 2, '.', '') : 0;

                $getpecoloan = $ci->db->select("SUM(amt) AS totalpecoloan")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 1079, "status" => 1))->get()->row();
                $sumdeptpecoloan += ($getpecoloan) ? number_format(($getpecoloan->totalpecoloan), 2, '.', '') : 0;

                $getdeda = $ci->db->select("SUM(amt) AS totaldeda")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 363, "status" => 1))->get()->row();
                $sumdeptdedaloan += ($getdeda) ? number_format(($getdeda->totaldeda), 2, '.', '') : 0;

                $sumdeptdeda = $sumdeptpecoloan + $sumdeptdedaloan;

                $getotherdedn = $ci->db->select("SUM(amt) AS totalotherdedn")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 261, "status" => 1))->get()->row();
                $sumdeptotherdedn += ($getotherdedn) ? number_format(($getotherdedn->totalotherdedn), 2, '.', '') : 0;

                $getcooploan = $ci->db->select("SUM(amt) AS totalcooploan")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 255, "status" => 1))->get()->row();
                $sumdeptcooploan += ($getcooploan) ? number_format(($getcooploan->totalcooploan), 2, '.', '') : 0;


                //earnings

                $getcola = $ci->db->select("SUM(amt) AS totalcola")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 251))->get()->row();
                $cola += ($getcola) ? number_format(($getcola->totalcola), 2, '.', '') : 0;

                $getricesubsi = $ci->db->select("SUM(amt) AS totalricesubi")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 253))->get()->row();
                $ricesubsi += ($getricesubsi) ? number_format(($getricesubsi->totalricesubi), 2, '.', '') : 0;

                $getholidaypay = $ci->db->select("SUM(amt) AS totalholidaypay")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 263))->get()->row();
                $holidaypay += ($getholidaypay) ? number_format(($getholidaypay->totalholidaypay), 2, '.', '') : 0;


                $gettrans_allw = $ci->db->select("SUM(amt) AS totalgettransallw")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 252))->get()->row();
                $trans_allw += ($gettrans_allw) ? number_format(($gettrans_allw->totalgettransallw), 2, '.', '') : 0;

                $getnitediff = $ci->db->select("SUM(amt) AS totalnitediff")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 358))->get()->row();
                $nitediff += ($getnitediff) ? number_format(($getnitediff->totalnitediff), 2, '.', '') : 0;

                $getotpaywithholiday = $ci->db->select("SUM(amt) AS totalgetotpay")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 3010))->get()->row();
                $otpaywithholiday = ($getotpaywithholiday) ? number_format(($getotpaywithholiday->totalgetotpay), 2, '.', '') : 0;

                $getotpayweekends = $ci->db->select("SUM(amt) AS totalgetotpay")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 1082))->get()->row();
                $otpayweekends = ($getotpayweekends) ? number_format(($getotpayweekends->totalgetotpay), 2, '.', '') : 0;

                $getotpayweekdays = $ci->db->select("SUM(amt) AS totalgetotpay")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 359))->get()->row();
                $otpayweekdays = ($getotpayweekdays) ? number_format(($getotpayweekdays->totalgetotpay), 2, '.', '') : 0;

                $otpay += $otpayweekends + $otpayweekdays + $otpaywithholiday;

                $getactingallw = $ci->db->select("SUM(amt) AS totalactingallw")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 360))->get()->row();
                $actingallw += ($getactingallw) ? number_format(($getactingallw->totalactingallw), 2, '.', '') : 0;

                $getotheradd = $ci->db->select("SUM(amt) AS totalotheradd")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 266))->get()->row();
                $otheradd = ($getotheradd) ? number_format(($getotheradd->totalotheradd), 2, '.', '') : 0;

                $getbonus = $ci->db->select("SUM(amt) AS totalbonus")->from("payroll_reports_trn")->where(array("payrollid" => $totals->payrollid, "trntype" => 264))->get()->row();
                $bonus = ($getbonus) ? number_format(($getbonus->totalbonus), 2, '.', '') : 0;

                $totalotheradd += ($otheradd + $bonus);


            }
        }


        $data['ccid'] = $ccid;
        $data['sumdeptssscont'] = $sumdeptssscont;
        $data['sumdepthdmfcont'] = $sumdepthdmfcont;
        $data['sumdeptsssloan'] = $sumdeptsssloan;
        $data['sumdepthdmfloan'] = $sumdepthdmfloan;
        $data['sumdeptpecewaloan'] = $sumdeptpecewaloan;

        $data['sumdeptpagibigad'] = $sumdeptpagibigad;
        $data['sumdepthmodedn'] = $sumdepthmodedn;
        $data['sumdeptelectbill'] = $sumdeptelectbill;
        $data['sumdeptmemins'] = $sumdeptmemins;
        $data['sumdeptlwop'] = $sumdeptlwop;
        $data['sumdeptdeda'] = $sumdeptdeda;
        $data['sumdeptotherdedn'] = $sumdeptotherdedn;
        $data['sumdeptbasetax'] = $sumdeptbasetax;
        $data['sumdeptcooploan'] = $sumdeptcooploan;
        $data['totaldeptbasetax'] = $totaldeptbasetax;


        $data['totaldeptbasicrate'] = $totaldeptbasicrate;


        $data['cola'] = $cola;
        $data['ricesubsi'] = $ricesubsi;
        $data['holidaypay'] = $holidaypay;
        $data['trans_allw'] = $trans_allw;
        $data['nitediff'] = $nitediff;

        $data['otpay'] = $otpay;
        $data['actingallw'] = $actingallw;
        $data['otheradd'] = $totalotheradd;

        $data['totalearnings'] = $totalearnings;
        $data['totaldepttotaldedn'] = $totaldepttotaldedn;
        $data['totalnet'] = $totalnet;

        return (object)$data;
    }
}
//EXPORT BANK FILE
if(!function_exists('query_bankfile_records')) {
    function query_bankfile_records($empid, $acctno, $payclass, $paytype, $net_15, $net_30, $month, $year) {
        $ci = &get_instance();

        $data = array();

        if (in_array($payclass,array(128,3077,3078))) {
            $net_type = ($paytype - 1);
        } else {
            $net_type = 0;
        }

        $qry_bankfile_check = $ci->db->select()
            ->from('payroll_transactions_bankfile')
            ->where(array('empid' => $empid, 'years' => $year, 'months' => $month, 'nettype' => $net_type))
            ->get()->row();

        $net_amt = (in_array($payclass,array(128,3077,3078))) ? $net_15 : $net_30; // IF PAY CLASS IS 128 ALL AMT FROM NET 15

        if($qry_bankfile_check) {
            // INSERT NET TYPE, 1 NET 30
            $ci->db->where(array('empid' => $empid, 'years' => $year, 'months' => $month, 'nettype' => $net_type, 'status' => 1));
            $ci->db->update('payroll_transactions_bankfile', array('status' => 0));
        }

        $ins_arr = array(
            'empid' => $empid,
            'acctcode' => $acctno,
            'amt' => round($net_amt, 2,PHP_ROUND_HALF_UP),
            'payclass' => $payclass,
            'months' => $month,
            'years' => $year,
            'nettype' => $net_type,
            'createdby' => user_id(),
            'updatedby' => user_id(),
        );
        $ci->db->insert('payroll_transactions_bankfile', $ins_arr);
        $bankfile_insert_err = $ci->db->_error_message();
        $amount_net = $net_30;


        $data['sent'][] = array('Empid' => $empid, 'Acct#' => $acctno, 'Payclass' => $payclass, 'Paytype' => $paytype, 'Net15' => $net_15, 'Net30' => $net_30, 'Month' => $month, 'Year' => $year);
        $data['amt']        =   $amount_net;
        $data['payclass']   =   $payclass;
        $data['nettype']    =   $net_type;
        $data['empid']      =   $empid;
        $data['acctno']     =   $acctno;
        $data['inserterror']     =   $bankfile_insert_err;
        return (object)$data;
    }
}
//GET PAYROLL TRANSACTIONS
if(!function_exists('get_payslip_trn')) {
    function get_payslip_trn($empid , $month , $year  , $paytype , $payclass){

        $ci = &get_instance();
        $data = array();
        $res = false;

        if($payclass == 128 || $payclass == 3077 || $payclass == 3078){
            $ci->db->where(array("prg.paytype" => $paytype));
        }else{
            $ci->db->where(array("prg.paytype" =>1));
        }
        $basic = 0;

        $sql = $ci->db->select("
        prg.years , 
        prg.months , 
        prg.payclass , 
        prm.empid , 
        prm.ccid , 
        prm.basic , 
        prm.deductions , 
        prm.earnings , 
        prt.trntype , 
        pm.functions , 
        pm.effects , 
        ptp.desc, 
        ptp.names, 
        p.lastname , 
        p.firstname,
        SUM(prm.net) AS net , 
        SUM(prt.amt) AS amt
        ")->from("payroll_reports_group as prg")
            ->join("payroll_reports_main as prm" , "prm.groupid = prg.sysid" , "left")
            ->join("payroll_reports_trn as prt" , "prt.payrollid = prm.sysid && prt.status = 1" , "left")
            ->join("payroll_matrix as pm" , "pm.typesid = prt.trntype" , "left")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = prt.trntype" , "left")
            ->join("prime_employee_main as pem" , "pem.sysid = prm.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->where(array("prg.years" => $year , "prg.months" => $month , "prg.payclass" => $payclass , "prm.empid" => $empid  ,"prg.status" => 301))
            ->group_by("
                prg.years , 
                prg.months , 
                prg.payclass , 
                prm.empid , 
                prm.ccid , 
                prm.basic , 
                prm.deductions , 
                prm.earnings , 
                prt.trntype , 
                pm.functions , 
                pm.effects , 
                ptp.desc, 
                ptp.names, 
                p.lastname , 
                p.firstname
            ")
            ->order_by("prt.trntype" , "ASC")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){

                $basic = $row->basic;

                if($row->functions == 1 && $row->effects == 1){
                    $data['earningarr'][] = array(
                        'amt' => $row->amt,
                        'desc' => $row->desc,
                        'code' => $row->names,
                        'typesid' => $row->trntype
                    );
                }
                if($row->functions == 0 && $row->effects == 1 || $row->functions == 0 && $row->effects == 0 ||  $row->functions == 1 && $row->effects == 0){
                    $data['deductionsarr'][] = array(
                        'amt' => $row->amt,
                        'desc' => $row->desc,
                        'code' => $row->names,
                        'typesid' => $row->trntype
                    );
                }
            }
            $res = true;
        }

        $data['basic'] = $basic;
        $data['empid'] = $empid;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['paytype'] = $paytype;
        $data['payclass'] = $payclass;
        $data['res'] = $res;
        return (object) $data;
        //  return json_encode($data);
    }
}
//GET PAYSLIP PER EMPLOYEE
if(!function_exists('form_payslip_single')) {
    function form_payslip_single($empid , $month, $year, $paytype, $payclass, $single = false , $pagenum = 0) {
        $ci = &get_instance();
        $data = array();
        $res = false;
        $html = '';
        if($paytype == 1){
            $paytext = '1st Half';
        }else if($paytype == 2){
            $paytext = '2nd Half';
        }else{
            $paytext = '';
        }

        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F'); // March

        $payrolldate = $year.'-'.$monthName.' '.$paytext;
        $total_eranings_amt = 0;
        if(user_id() && $empid != '') {

            // $info = get_employee_info($empid);

            //THIS CODE IS NOT CORRECT BECAUSE IT IS STILL GETTING THE LATEST SALARY HAHA SO KUN MAG PRINT KA LIWAT PREVIOUS PAYSLIP ANG SALARY NAHALIN INSTEAD NGA DAPAT ANG OLD NGA SALARY
           /* $info = $ci->db->select("amt")->from("prime_employee_salary")
                ->where(array("empid" => $empid , "status" => 1))->get()->row(); */
           if($payclass == 128 || $payclass == 3077 || $payclass == 3078){
               $ci->db->where(array("prg.paytype" => $paytype));
           }
            $info = $ci->db->select("prm.basic")->from("payroll_reports_group as prg")
                ->join("payroll_reports_main as prm" , "prg.sysid = prm.groupid")
                ->where(array("prg.status" => 301 , "prg.months" => $month , "prg.years" => $year,
                    "prg.payclass" => $payclass , "prm.empid" => $empid))->get()->row();

           //$data['info_qry'] = $ci->db->last_query();

            if($info) {
                $payslip = get_payslip_trn($empid, $month, $year, $paytype, $payclass);
                if ($payslip->res == true) {

                    $res = true;
                    $basic_amt =  $info->basic;
                    $earnings_arr_loop_left = array();
                    $earnings_arr_loop_right = array();
                    $total_earnings = 0;
                    $total_deduction = 0;

                    $deductions_arr_loop_left = array();
                    $deductions_arr_loop_right = array();


                    $data['eloopleft'] = $earnings_arr_loop_left;
                    $data['eloopright'] = $earnings_arr_loop_right;
                    $data['dloopleft'] = $deductions_arr_loop_left;
                    $data['dloopright'] = $deductions_arr_loop_right;

                    if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
                        if ($paytype == 1) {
                            $paysliptitle = 'Payslip 15th, ' . date_formating($month, '!m', 'M') . ', ' . $year;
                        } else {
                            $paysliptitle = 'Payslip 30th, ' . date_formating($month, '!m', 'M') . ', ' . $year;
                        }
                    } else {
                        $paysliptitle = 'Payslip, ' . date_formating($month, '!m', 'M') . ', ' . $year;
                    }


                    $border_bottom = '';
                    if ($single == false) {
                        $border_bottom = 'border-bottom: 2px dashed #000';
                    }

                    $html .= '<div style="position: relative; height: 170px; white-space: nowrap; width: 100%; margin-bottom: 10px; ' . $border_bottom . ' padding-bottom: 2px;">';

                    $html .= employee_print_header($empid, 'Payslip', true);

                    $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';

                    $html .= '<div style="position: absolute; top: 35px; padding-top: 5px; left: 0px; width: 32%; height: 120px; border-right: 1px solid #ccc;">';
                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">EARNINGS</span>';
                    $html .= '</p>';


                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                    $html .= '<ul style="vertical-align: top; list-style: none; margin: 0px 0px; width: 100%; padding: 0px 0px;">';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Basic <span style="font-family: courier, monospace; position: absolute; right: 10px; width: 150px; display: inline-block; text-align: right">' . number_format($basic_amt, 2) . '</span></li>';
                    if (isset($payslip->earningarr) && count($payslip->earningarr) > 0) {
                        foreach ($payslip->earningarr as $earnings_row) {
                            $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">' . $earnings_row['code'] . '<span style="font-family: courier, monospace; position: absolute; right: 10px; width: 150px; display: inline-block; text-align: right">' . number_format($earnings_row['amt'], 2) . '</span></li>';
                            $total_earnings += $earnings_row['amt'];
                        }
                        $total_eranings_amt = ($basic_amt + $total_earnings);
                    }else{
                        $total_eranings_amt = $basic_amt;
                    }
                    $html .= '</ul>';
                    $html .= '</p>';


                    $html .= '</p>';
                    $html .= '</div>';


                    $html .= '<div style="position: absolute; top: 35px; padding-top: 5px;  left: 35%; width: 32%; height: 120px;  border-right: 1px solid #ccc;">';
                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">DEDUCTIONS</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right"></span>';
                    $html .= '</p>';


                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                    $html .= '<ul style="vertical-align: top; list-style: none; margin: 0px 0px; width: 100%; padding: 0px 0px;">';
                    if (isset($payslip->deductionsarr) && count($payslip->deductionsarr) > 0) {
                        foreach ($payslip->deductionsarr as $deduction_row) {
                            $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">' . $deduction_row['code'] . '<span style="font-family: courier, monospace; position: absolute; right: 10px; width: 150px; display: inline-block; text-align: right">' . number_format($deduction_row['amt'], 2) . '</span></li>';
                            $total_deduction += $deduction_row['amt'];
                        }
                    }
                    $html .= '</ul>';
                    $html .= '</p>';

                    $html .= '</div>';

                    if($total_eranings_amt == 0 && $total_deduction == 0){
                        $total_net = $payslip->basic;
                    }else{
                        $total_net = ($total_eranings_amt - $total_deduction);
                    }



                    $html .= '<div style="position: absolute; top: 35px; padding-top: 5px;  right: 0px; width: 30%; height: 120px;">';

                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">TOTAL</span>';
                    $html .= '</p>';


                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<ul style="vertical-align: top; list-style: none; margin: 0px 0px; width: 100%; padding: 0px 0px;">';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Total Earnings<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format($total_eranings_amt, 2) . '</span></li>';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Total Deduction<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format($total_deduction, 2) . '</span></li>';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">NET Pay<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format($total_net, 2) . '</span></li>';

                    if ($payclass  == 1 || $payclass == 129) {
                        $net15 = 0;
                        $qry_bank_file = $ci->db->select("amt")->from("payroll_transactions_bankfile")
                            ->where(array("empid" => $empid, "months" => $month, "years" => $year,
                                "payclass" => 129, "status" => 1, "nettype" => 0))->get()->row();
                        if ($qry_bank_file) {
                            $net15 = $qry_bank_file->amt;

                            $net30 = $total_net - $qry_bank_file->amt;
                        } else {
                            $net30 = $total_net;
                        }
                        $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">NET 15<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format($net15, 2) . '</span></li>';
                        $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">NET 30<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format($net30, 2) . '</span></li>';

                    }
                    $html .= '</ul>';
                    $html .= '</p>';
                    $html .= '</div>';
                    $page = '';
                    if($single == false){
                        $page = 'Page: '.$pagenum.' ';
                    }
                    $html .= '<div style="position: absolute; top: 160px; left: 0px; width: 100%; height: 30px; border-top: 1px solid #ccc;">';
                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">' .$page. date('Y-m-d h:m:i') . '</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 190px; font-weight: bold;">Printed By: ' .user_info()->lastname . '</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-weight: bold;"> Payroll Date: ' . $payrolldate .'</span>';


                    $html .= '</p>';
                    $html .= '</div>';


                    $html .= '<footer class="printout"></footer>';
                    $html .= '</div>';


                }
                $data['html'] = $html;
                $data['res'] = $res;
            }
        }
        return (object)$data;
    }
}
//PRINT HEADER
if(!function_exists('employee_print_header')) {
    function employee_print_header($empid, $reptitle, $pdf = false) {

        $info = get_employee_info($empid);

        $html = '';
        $name = $info->lastname . ', '.$info->firstname;

        if($pdf==true) {
            // ##############################
            // PDF IS TRUE = E:/xammp/htdocs/erp/
            $bgimg = FCPATH . 'assets/global/img/logo/tvi-small-logo.png';
        } else {
            // ##############################
            // PDF IS FALSE = http://localhost/erp/
            $bgimg = base_url() . 'assets/global/img/logo/tvi-small-logo.png';
        }

        //$html .= '<img style="z-index: 0; width: 120px; height: 25px;" src="' . $bgimg . '" />';
        $html .= '<div style="z-index: 0; width: 120px; height: 25px;"></div>';
        $html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; left: 0px; font-size: 12px; top: 0px; width: 300px; display: inline-block; text-align: left; font-weight: bold;">Panay Alternative Energy, Inc.</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-size: 9px; top: 14px; width: 300px; display: inline-block; text-align: left; ">Westwood Building, R4, Tabuc Suba, Jaro, Iloilo City Philippines, 5000</span>';
        $html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; right: 70px; font-size: 12px; top: 0px; width: 250px; display: inline-block; text-align: right; font-weight: bold;">' . $name . '</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 70px; font-size: 9px; top: 14px; width: 150px; display: inline-block; text-align: right">'.$reptitle.'</span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 60px; top: 5px; width: 130px; display: inline-block; border-right: 1px solid #ccc; height: 20px;"></span>';
        $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-size: 20px; top: 0px; width: 130px; display: inline-block; text-align: right; font-weight: bold;">' . $info->deptcode . '</span>';

        $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
        return $html;
    }
}
//GET EMPLOYEE NAME
if(!function_exists('get_employee_name')) {

    function get_employee_name($empid)
    {
        $info = get_employee_info($empid);
        if ($info->qry == true) {
            $name = strtoupper($info->firstname) . ' ' . strtoupper($info->middlename) . ' ' . strtoupper($info->lastname);
        } else {
            $name = '';
        }

        return $name;
    }
}
//ADD MONTHS TO DATE
if(!function_exists('add_month_to_date')) {

    function add_month_to_date($d1, $months)
    {
        $date = new DateTime($d1);

        // call second function to add the months
        $newDate = $date->add(add_months($months, $date));

        // goes back 1 day from date, remove if you want same day of month
        $newDate->sub(new DateInterval('P1D'));

        //formats final date to Y-m-d form
        $dateReturned = $newDate->format('Y-m-d');

        return $dateReturned;
    }
}

if(!function_exists('get_employee_evaluation_data')) {
    function get_employee_evaluation_data($empid,$evaltype,$year,$ratedby,$questid)
    {
        $ci = &get_instance();
        $data = array();

        $getinfo = $ci->db->select("")->from("evaluation_main")
            ->where(array("empid" => $empid , "evaltype" => $evaltype , "year" => $year , "createdby" => $ratedby,"status" => 1))
            ->get()->row();
        if($getinfo){
            $data['groupid'] = $getinfo->groupid;
            $data['strength'] = $getinfo->strength;
            $data['weakness'] = $getinfo->weakness;
            $data['evaldiscussed'] = $getinfo->evaldiscussed;
            $data['qry'] = true;
        }else{
            $data['qry'] = false;
        }

        if($getinfo){
            $ci->db->where(array("groupid" => $getinfo->groupid));
        }
        $getjustification = $ci->db->select("justification")->from("evaluation_justifications")
            ->where(array("empid" => $empid,"questid" => $questid , "evaltype" => $evaltype,
                "year" => date('Y') ,"createdby" => $ratedby,"status" => 1))->get()->row();
        if($getjustification){
            $data['justification'] = $getjustification->justification;
        }else{
            $data['justification'] = '';
        }

        return (object) $data;
    }
}

//ADD MONTHS TO DATE
 if(!function_exists('add_months')) {
    function add_months($months, DateTime $dateObject)
    {
        $next = new DateTime($dateObject->format('Y-m-d'));
        $next->modify('last day of +' . $months . ' month');

        if ($dateObject->format('d') > $next->format('d')) {
            return $dateObject->diff($next);
        } else {
            return new DateInterval('P' . $months . 'M');
        }
    }
}
//GET ANNUAL PAYSLIP PER EMPLOYEE
if(!function_exists('form_annual_payslip_single')) {
    function form_annual_payslip_single($empid, $month, $year, $paytype, $typesid, $single = false , $pagenum = 0 , $viewtype) {
        $ci = &get_instance();
        $data = array();
        $res = false;
        $html = '';
        $total_eranings_amt = 0;
        if(user_id() && $empid != '') {

            // $info = get_employee_info($empid);

                    $gettrnname = $ci->db->select("desc")->from("prime_types_parameter")
                        ->where(array("sysid" => $typesid))->get()->row();
                    $trnname = ($gettrnname) ? $gettrnname->desc : '';

                    $res = true;

                    $getamounttrn = $ci->db->select("gross , tax , deduction")->from("payroll_manual_earnings")
                        ->where(array("empid" => $empid , "month" => $month , "year" => $year , "typesid" => $typesid , "status" => 305))
                        ->get()->row();
                    $gross = ($getamounttrn) ? $getamounttrn->gross : 0;
                    $tax = ($getamounttrn) ? $getamounttrn->tax : 0;
                    $deduction  = ($getamounttrn) ? $getamounttrn->deduction : 0;
                    $totalamt  = ($gross - ($tax + $deduction));

                    $earnings_arr_loop_left = array();
                    $earnings_arr_loop_right = array();
                    $total_earnings = 0;
                    $total_deduction = 0;

                    $deductions_arr_loop_left = array();
                    $deductions_arr_loop_right = array();


                    $data['eloopleft'] = $earnings_arr_loop_left;
                    $data['eloopright'] = $earnings_arr_loop_right;
                    $data['dloopleft'] = $deductions_arr_loop_left;
                    $data['dloopright'] = $deductions_arr_loop_right;


                    $paysliptitle = 'Bonus Payslip, ' . date_formating($month, '!m', 'M') . ', ' . $year;



                    $border_bottom = '';
                    if ($single == false) {
                        $border_bottom = 'border-bottom: 2px dashed #000';
                    }

                    $html .= '<div style="position: relative; height: 170px; white-space: nowrap; width: 100%; margin-bottom: 10px; ' . $border_bottom . ' padding-bottom: 2px;">';

                    $html .= employee_print_header($empid, 'Payslip', true);

                    $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';

                    $html .= '<div style="position: absolute; top: 35px; padding-top: 5px; left: 0px; width: 32%; height: 120px; border-right: 1px solid #ccc;">';
                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">EARNINGS</span>';
                    $html .= '</p>';


                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                    $html .= '<ul style="vertical-align: top; list-style: none; margin: 0px 0px; width: 100%; padding: 0px 0px;">';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Basic <span style="font-family: courier, monospace; position: absolute; right: 10px; width: 150px; display: inline-block; text-align: right">' . number_format(0, 2) . '</span></li>';


                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">'.$trnname.'<span style="font-family: courier, monospace; position: absolute; right: 10px; width: 150px; display: inline-block; text-align: right">' . number_format($gross, 2) . '</span></li>';
                    $html .= '</ul>';
                    $html .= '</p>';


                    $html .= '</p>';
                    $html .= '</div>';


                    $html .= '<div style="position: absolute; top: 35px; padding-top: 5px;  left: 35%; width: 32%; height: 120px;  border-right: 1px solid #ccc;">';
                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">DEDUCTIONS</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right"></span>';
                    $html .= '</p>';


                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                    $html .= '<ul style="vertical-align: top; list-style: none; margin: 0px 0px; width: 100%; padding: 0px 0px;">';

                    if($tax > 0){
                        $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Tax<span style="font-family: courier, monospace; position: absolute; right: 10px; width: 150px; display: inline-block; text-align: right">' . number_format($tax, 2) . '</span></li>';
                    }
                    if($deduction > 0){
                        $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Other deduction<span style="font-family: courier, monospace; position: absolute; right: 10px; width: 150px; display: inline-block; text-align: right">' . number_format($deduction, 2) . '</span></li>';
                    }

                    $html .= '</ul>';
                    $html .= '</p>';

                    $html .= '</div>';

                   /* if($total_eranings_amt == 0 && $total_deduction == 0){
                        $total_net = $payslip->basic;
                    }else{
                        $total_net = ($total_eranings_amt - $total_deduction);
                    } */



                    $html .= '<div style="position: absolute; top: 35px; padding-top: 5px;  right: 0px; width: 30%; height: 120px;">';

                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">TOTAL</span>';
                    $html .= '</p>';


                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<ul style="vertical-align: top; list-style: none; margin: 0px 0px; width: 100%; padding: 0px 0px;">';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Total Earnings<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format($gross, 2) . '</span></li>';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">Total Deduction<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format(($tax + $deduction), 2) . '</span></li>';
                    $html .= '<li style="font-family: courier, monospace; font-size: 9px; ">NET Pay<span style="font-family: courier, monospace; position: absolute; right: 0px; width: 150px; display: inline-block; text-align: right">' . number_format(($gross - ($tax + $deduction)), 2) . '</span></li>';

                    $html .= '</ul>';
                    $html .= '</p>';
                    $html .= '</div>';
                    $page = '';
                    if($single == false){
                        $page = 'Page: '.$pagenum.' ';
                    }
                    $html .= '<div style="position: absolute; top: 160px; left: 0px; width: 100%; height: 30px; border-top: 1px solid #ccc;">';
                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">' .$page. date('Y-m-d h:m:i') . '</span>';
                    $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-weight: bold;">Printed By: ' . user_info()->lastname . '</span>';
                    $html .= '</p>';
                    $html .= '</div>';


                    $html .= '<footer class="printout"></footer>';
                    $html .= '</div>';



                $data['html'] = $html;
                $data['res'] = $res;

        }
        return (object)$data;
    }
}
//GET PAYCLASS TYPES
if(!function_exists('getpayclass')) {
    function getpayclass(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,desc,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'EMPAYCLASS'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names.' - '.$row->desc
                );
            }
        }
        return json_encode($data);
    }
}
//GET POSITIONS TYPES
if(!function_exists('getpositions')) {
    function getpositions(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'EMPOST'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//COST GROUP TYPES - MAIN OFFICE - POWER PLANT
if(!function_exists('getcostgroup')) {
    function getcostgroup(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'COSTGROUP'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//GET SALARY INCREASE TYPE
if(!function_exists('getsalinctype')) {
    function getsalinctype(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'SALINC'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//GET CONTACT TYPES
if(!function_exists('getcontacttypes')) {
    function getcontacttypes(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'CONTACT'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//GET LOG TYPES
if(!function_exists('getlogtypes')) {
    function getlogtypes(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'TIMELOGTYPE'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//GET TS TEAM TYPES
if(!function_exists('gettsteam')) {
    function gettsteam(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'TSTEAM'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//GET ALL LEAVE TYPES
if(!function_exists('getleavecreditstypes')) {
    function getleavecreditstypes(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'LEAVECREDITS'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//GET JOB STATUS REGULAR, CONTRACTUAL, PROBATIONARY
if(!function_exists('getjobcatlist')) {
    function getjobcatlist(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'EMPJOBCAT'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.' - '.$row->names
                );
            }
        }
        return json_encode($data);
    }
}
//GET PAYROLL TYPE
if(!function_exists('getpayrollpaytype')) {
    function getpayrollpaytype(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,names")->from("prime_types_parameter")
            ->where(array("status" => 1,"codes" => 'PRLPAYTYPE'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names.' - '.$row->codes
                );
            }
        }
        return json_encode($data);
    }
}
//GET COSTCENTERS
if(!function_exists('getcostcenters')) {
    function getcostcenters(){
        $ci = &get_instance();
        $data = array();

        $sql = $ci->db->select("sysid,codes,desc")->from("prime_costcenter_main")
            ->where(array("status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc.' - '.$row->codes
                );
            }
        }

        return json_encode($data);
    }
}

// #############################################
// LOGGING
if(!function_exists('update_logs')) {
    function update_logs($moduleid, $dataid, $statusid, $remarks, $specificdate) {
        $ci = &get_instance();
        if(user_id() > 0) {
            $ins_arr = array(
                'dataid' => $dataid,
                'statusid' => $statusid,
                'moduleid' => $moduleid,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'specificdate' => $specificdate,
                'remarks' => $remarks
            );
            return $ci->db->insert('prime_employee_main_history', $ins_arr);
        }else{
            return false;
        }
    }
}

// ###############################################
// GET EMPLOYEE APPROVAL
if(!function_exists(('get_employee_request_approval'))) {
    function get_employee_request_approval($empid) {
        $ci = &get_instance();
        $qry_approvals = $ci->db->query("
                    SELECT
                    cc.empid AS empid,
                    cc.ccid,
                    ch.empid AS headid,
                    cg.codes,
                    cgh.empid AS execid
                    FROM
                    prime_employee_costcenter AS cc
                    INNER JOIN prime_costcenter_head AS ch ON cc.ccid = ch.ccid
                    INNER JOIN prime_costcenter_group_matrix AS cgm ON cc.ccid = cgm.ccid
                    INNER JOIN prime_costcenter_group AS cg ON cgm.groupid = cg.sysid
                    INNER JOIN prime_costcenter_group_head AS cgh ON cgh.groupid = cg.sysid
                    WHERE
                    cc.empid = $empid AND
                    cg.`level` = 2 AND
                    cc.`status` = 1
                    ")->row();

        return ($qry_approvals) ? $qry_approvals : false;
    }
}