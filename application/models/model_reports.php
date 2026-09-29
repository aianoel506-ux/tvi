<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/7/2018
 * Time: 8:41 AM
 */
class Model_reports extends CI_model
{

    function datatable_apt_summary()
    {
        $data = array();
        $tbl_arr = array(
            array('types' => 'TNO', 'accomp' => 0, 'unaccomp' => '0', 'total' => 0),
            array('types' => 'CMO', 'accomp' => 0, 'unaccomp' => '0', 'total' => 0),
            array('types' => 'OIMR', 'accomp' => 0, 'unaccomp' => '0', 'total' => 0),
            array('types' => 'FDO', 'accomp' => 0, 'unaccomp' => '0', 'total' => 0),
            array('types' => 'CRF', 'accomp' => 0, 'unaccomp' => '0', 'total' => 0),
            array('types' => 'ECALES', 'accomp' => 0, 'unaccomp' => '0', 'total' => 0),
        );
        $data['list'] = $tbl_arr;
        return json_encode($data);
    }

    /**
     * @return string
     */
    function chart_apt_aging()
    {
        $data = array();
        $min = 5;
        for ($m = 1; $m <= 12; $m++) {
            $min += 5;
            $rand_accomp = rand(50, 150);
            if ($min > $rand_accomp) {
                $rand_accomp_new = $min;
            } else {
                $rand_accomp_new = $rand_accomp - $min;
            }
            $rand_unaccomp = rand(1, 20);
            $rand_unaccomp_new = $rand_unaccomp + $min;
            $total = $rand_unaccomp_new + $rand_accomp_new;
            $month_name = date_formating($m, 'm', 'M');
            $data['months'][] = array(
                "month" => "$month_name-2017",
                "accomp" => $rand_accomp_new,
                "unaccomp" => $rand_unaccomp_new,
                "totalText" => $total,
                "unAccompColor" => '#fc536f',
                "AccompColor" => '#00ff99',
            );

        }
        return json_encode($data);
    }

    function get_user_reports()
    {
        $data = array();

        $sql = $this->db->select("psu.sysid,psu.firstname,psu.lastname,MAX(psul.sessiondatetime) AS sessdatetime")
            ->from("prime_system_users as psu")
            ->join("prime_system_users_logs as psul", "psul.userid = psu.sysid", "left")
            ->group_by("psu.sysid")
            ->order_by("sessdatetime", "DESC")
            ->get();


        if ($sql->num_rows() > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {
                $getlogincount = $this->db->select("COUNT(userid) AS cnt")
                    ->from("prime_system_users_logs")
                    ->where(array("userid" => $row->sysid, "sessionlogtype" => 1))
                    ->get()->row();
                $data['usersreports'][] = array(
                    "expand" => $row->sysid,
                    "num" => $num++,
                    "fname" => $row->firstname,
                    "lname" => $row->lastname,
                    "sessdatetime" => $row->sessdatetime,
                    "logcount" => ($getlogincount) ? $getlogincount->cnt : 0,
                );
            }
        }

        return json_encode($data);
    }

    function get_user_session()
    {
        $data = array();
        $id = $this->input->post('id');
        $html = '';

        $cnt = $this->db->select('COUNT(userid) AS CNT')->from('prime_system_users_logs')
            ->where(array('userid' => $id))
            ->get()->row();

        $sql = $this->db->select("
                sessiondatetime,
                sessionip, 
                sessiondevice, 
                sessiondevicename, 
                sessionagent, 
                sessionlogtype, 
                sessionlogresponse, 
                sessionsegment,
                sessionmoduleid
            ")
            ->from("prime_system_users_logs")
            ->order_by("sessiondatetime", "desc")
            ->where(array("userid" => $id))
            ->get()->row();

        if ($sql) {
            if ($cnt->CNT >= 1) {

                $module_name = 'Dashboard';
                $qry_ougout = $this->db->select("
                    sessionsegment,
                    sessionmoduleid
                ")
                    ->from("prime_system_users_logs")
                    ->order_by("sessiondatetime", "desc")
                    ->where(array("userid" => $id, 'sessionlogtype' => 0))
                    ->get()->row();
                if ($qry_ougout) {
                    if ($qry_ougout->sessionsegment == '') {
                        $module_name = 'Dashboard';
                    } else {
                        $last_page = ($qry_ougout) ? $qry_ougout->sessionsegment : '';

                        $segment_arr = explode('/', $last_page);
                        $segment_hash = ($qry_ougout && $segment_arr) ? $segment_arr[1] : '';
                        // GET MODULE NAME OUT OF HASh
                        $qry_module = $this->db->select()->from('prime_module_navigations_main')
                            ->where('hashcode', $segment_hash)->get()->row();

                        if (count($segment_hash) > 0 && $qry_module) {
                            $module_name = '<i class="fa ' . $qry_module->icon . ' fa-fw"></i> ' . $qry_module->desc;
                        } else {
                            if ($segment_hash != '') {
                                $segment_hash = $segment_hash;
                            } else {
                                $segment_hash = $segment_arr[0];
                            }
                            $module_name = '<i class="fa fa-tag fa-fw"></i> ' . ucfirst($segment_hash);
                        }
                    }
                }

                $html .= '<div class="row" >';
                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Session IP</span>';
                $html .= '<span class="col-md-8 label-default">' . $sql->sessionip . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Session Device</span>';
                $html .= '<span class="col-md-8 label-default">' . $sql->sessiondevice . '</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Device Name</span>';
                $html .= '<span class="col-md-8 label-default">' . $sql->sessiondevicename . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Session Agent</span>';
                $html .= '<span class="col-md-8 label-default">' . $sql->sessionagent . '</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Session Logtype</span>';
                $html .= '<span class="col-md-8 label-default">' . $sql->sessionlogtype . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Session Log Response</span>';
                $html .= '<span class="col-md-8 label-default">' . $sql->sessionlogresponse . '</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group summary column no-border">';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Last Page Visit</span>';
                $html .= '<span class="col-md-8 label-default">' . $module_name . '</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-4 label-name">Module ID</span>';
                $html .= '<span class="col-md-8 label-default">' . $sql->sessionmoduleid . '</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '</div>';
            } else {
                $html .= '<h4>No User Logs Found.</h4>';
            }
        } else {
            $html .= '<h4>No User Logs Found.</h4>';
        }
        $data['html'] = $html;
        return json_encode($data);
    }


    function tc_daily_trends()
    {
        $data = array();
        $begin = new DateTime('2010-05-01');
        $end = new DateTime('2010-05-10');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $qry_logs = $this->db->select(
            "
            COUNT(sysid) AS cnt,
            CAST(datecreated AS date) AS dte
            "
        )
            ->from('ticketing_details_logs')
            ->where(array('YEAR(datecreated)' => date('Y')))
            ->group_by('CAST(datecreated AS date)')
            ->get();

        if ($qry_logs->num_rows() > 0) {
            foreach ($qry_logs->result() as $row) {
                $dt = new DateTime($row->dte);
                $this->db->where('CAST(datecreated AS date) = ', $row->dte);
                $qry_logs_stats = $this->db->select(
                    "
                        COUNT(sysid) AS cnt,
                        status AS stats
                        "
                )
                    ->from('ticketing_details_logs')
                    ->group_by('status')
                    ->get();
                $accomp = 0;
                $pending = 0;
                if ($qry_logs_stats->num_rows() > 0) {
                    foreach ($qry_logs_stats->result() as $srow)
                        if (ts_status_pending($srow->stats) || $srow->stats == 300) {
                            $pending += $srow->cnt;
                        } else {
                            $accomp += $srow->cnt;
                        }
                }

                $data['trends'][] = array(
                    'date' => $dt->format("Y-m-d H:i:s"),
                    'accomp' => $pending,
                    'pending' => $accomp,
                    'reports' => $row->cnt
                );
            }
        }
        return json_encode($data);
    }

    function tc_district_pie()
    {
        $data = array();
        $qry_dist = $this->db->select('count(dl.district) AS cnt, d.names AS dist')
            ->from('address_districts AS d')
            ->join('ticketing_details_logs AS dl', 'dl.district = d.sysid')
            ->group_by('d.names')
            ->get();
        if ($qry_dist->num_rows() > 0) {
            foreach ($qry_dist->result() as $row) {
                $data['districts'][] = array(
                    'dist' => $row->dist,
                    'cnt' => $row->cnt,
                );
            }
        }
        return json_encode($data);
    }

    function tc_status_pie()
    {
        $month = date('M');
        $data = array();

        $from = $this->input->post('from');
        $to = $this->input->post('to');

        if($from && $to) {
            if($to > $from) {
                $this->db->where('CAST(tdl.datecreated AS DATE) >= ', $from);
                $this->db->where('CAST(tdl.datecreated AS DATE) <= ', $to);
            }
        } else {
            $this->db->where('MONTH(tdl.datecreated)', date('m'));
        }
        $qry_dist = $this->db->select('COUNT(tdl.sysid) AS CNT, tp.`names` AS `STATUS`, tp.colorbg AS COLOR')
            ->from('ticketing_details_logs AS tdl')
            ->join("prime_types_parameter AS tp", 'tp.sysid = tdl.`status`')
            ->group_by('tdl.`status`')
            ->get();

        /*
        $qry_dist = $this->db->query("
            SELECT COUNT(tdl.sysid) AS CNT, tp.`names` AS `STATUS`, tp.colorbg AS COLOR
            FROM ticketing_details_logs AS tdl
            LEFT JOIN prime_types_parameter AS tp ON tp.sysid = tdl.`status`
            WHERE MONTH(datecreated) = MONTH(NOW())
            GROUP BY tdl.`status`
        ");
        */

        if ($qry_dist->num_rows() > 0) {
            foreach ($qry_dist->result() as $row) {
                $data['status'][] = array(
                    'status' => $row->STATUS,
                    'color' => $row->COLOR,
                    'cnt' => $row->CNT,
                );
            }
        }

        $data['month'] = $month;
        return json_encode($data);
    }

    function tc_barangay_cluster()
    {
        $data = array();
        $dist = $this->input->post('dist');
        if ($dist > 0) {
            $this->db->where('b.distid', $dist);
        } else {
            $this->db->limit(50);
        }
        $qry_dist = $this->db->select('count(dl.barangays) AS cnt, b.texts, b.sysid')
            ->from('address_barangay AS b')
            ->join('ticketing_details_logs AS dl', 'dl.barangays = b.sysid')
            ->where('b.texts != ', '')
            ->group_by('b.texts, b.sysid')
            ->order_by('count(dl.barangays)', 'desc')
            ->get();
        if ($qry_dist->num_rows() > 0) {
            foreach ($qry_dist->result() as $row) {
                $qry_accomp = $this->db->select('COUNT(sysid) AS cnt')->from('ticketing_details_logs')
                    ->where(array('barangays' => $row->sysid, 'status' => 314))->get()->row();
                $accomp = ($qry_accomp) ? $qry_accomp->cnt : 0;
                $data['barangays'][] = array(
                    'texts' => $row->texts,
                    'accomp' => $accomp,
                    'cnt' => $row->cnt,
                );
            }
        }
        return json_encode($data);
    }

    function get_payroll_payslip()
    {
        $data = array();

        $payclass = $this->input->post('payclass');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $filename = '';

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
        $html .= '</head>';
        $html .= '<body>';

        $check_exists = $this->db->select()
            ->from('payroll_reports_group')
            ->where(array(
                'status' => 301,
                'payclass' => $payclass,
                'years' => $year,
                'months' => $month
            ))->get()->row();

        if ($check_exists) {
            $sql = $this->db->select("pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass")
                ->from("payroll_reports_main as prm")
                ->join("payroll_emplist as payrollemp", "payrollemp.empid = prm.empid", "left")
                ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                ->join("person as p", "p.sysid = pem.personid", "left")
                ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid", "left")
                ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid", "left")
                ->join("prime_employee_main_payclass as pemp", "pemp.emp_id = pem.sysid", "left")
                ->where(array(
                    'prg.sysid' => $check_exists->sysid,
                    'pec.type' => 1,
                    'pem.status' => 1))
                ->group_by("pem.sysid,payrollemp.accntno,prg.months,prg.years , pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net, prg.paytype , prg.payclass")
                ->order_by("p.lastname")
                ->get();
            if ($sql->num_rows() > 0) {
                foreach ($sql->result() as $row) {
                    $form_payslip = form_payslip_single($row->sysid, $month, $year, $row->paytype, $payclass);
                    $html .= $form_payslip->html;
                }


            }

            if ($payclass == 128) {
                if ($check_exists->paytype == 1) {
                    $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_RANKF.pdf';
                } else {
                    $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_RANKF.pdf';
                }
            } else {
                $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-00_CONFI.pdf';
            }

            // Load library
            $this->load->library('pdf');

            $dompdf = new Dompdf\Dompdf();

            $dompdf->loadHtml($html);

            $customPaper = array(0, 0, 610, 910);
            $dompdf->setPaper($customPaper, 'portrate');
            $dompdf->render();
            // Add PDF Document Information
            $dompdf->add_info('Subject', 'PECO PAYSLIP | ' . $filename);
            $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
            $dompdf->add_info('Creator', 'ITD');
            $dompdf->add_info('Keywords', 'Payslip');

            $output = $dompdf->output();

            $upload_path = FCPATH . 'uploads/employee/payslips/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
                $year_path = $upload_path . $year;
                if (!is_dir($year_path)) {
                    mkdir($upload_path . '/' . $year, 0777, true);
                    $month_path = $year_path . '/' . $month . '/';
                    if (!is_dir($month_path)) {
                        mkdir($year_path . '/' . $month, 0777, true);
                    } else {
                        chmod($year_path . '/' . $month, 0777);
                    }
                    file_put_contents($month_path . $filename, $output);
                } else {
                    $month_path = $year_path . '/' . $month . '/';
                    if (!is_dir($month_path)) {
                        mkdir($year_path . '/' . $month, 0777, true);
                    } else {
                        chmod($year_path . '/' . $month, 0777);
                    }
                    file_put_contents($month_path . $filename, $output);
                }
            } else {
                $year_path = $upload_path . $year;
                if (!is_dir($year_path)) {
                    mkdir($upload_path . '/' . $year, 0777, true);
                    $month_path = $year_path . '/' . $month . '/';
                    if (!is_dir($month_path)) {
                        mkdir($year_path . '/' . $month, 0777, true);
                    } else {
                        chmod($year_path . '/' . $month, 0777);
                    }
                    file_put_contents($month_path . $filename, $output);
                } else {
                    $month_path = $year_path . '/' . $month . '/';
                    if (!is_dir($month_path)) {
                        mkdir($year_path . '/' . $month, 0777, true);
                    } else {
                        chmod($year_path . '/' . $month, 0777);
                    }
                    file_put_contents($month_path . $filename, $output);
                }
            }


            $data['pdf'] = base_url($month_path . $filename);
        }

        $html .= '</body>';
        $html .= '</html>';


        $data['filename'] = $filename;
        $data['payclass'] = $payclass;
        $data['html'] = $html;
        return json_encode($data);
    }

    function send_payslip()
    {
        $empid = $this->input->post('empid');
        $num = $this->input->post('num');
        $cnt = $this->input->post('cnt');

        $payclass = $this->input->post('payclass');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $paytype = $this->input->post('paytype');

        $check_exists = $this->db->select()
            ->from('payroll_reports_group')
            ->where(array(
                'status' => 301,
                'payclass' => $payclass,
                'years' => $year,
                'months' => $month
            ))->get()->row();
        if ($check_exists) {
            if ($num > 0) {
                $this->db->where('pem.sysid > ', $empid);
            }
            $row = $this->db->select("pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass")
                ->from("payroll_reports_main as prm")
                ->join("payroll_emplist as payrollemp", "payrollemp.empid = prm.empid", "left")
                ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                ->join("person as p", "p.sysid = pem.personid", "left")
                ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid", "left")
                ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid", "left")
                ->join("prime_employee_main_payclass as pemp", "pemp.emp_id = pem.sysid", "left")
                ->where(array(
                    'prg.sysid' => $check_exists->sysid,
                    'pec.type' => 1,
                    'pec.status' => 1,
                    'pem.status' => 1
                ))
                ->group_by("pem.sysid,payrollemp.accntno,prg.months,prg.years , pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net, prg.paytype , prg.payclass")
                ->order_by("pem.sysid", 'asc')
                ->get()->row();
            if ($row) {

            }
        }
    }

    function get_payslip_data_bak() {

        $data = array();
        $msg = '';
        $func = 'danger';
        $qry = false;
        $payclass = $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $period = $this->input->post('payrollperiod');
        // $paytype = $this->input->post('paytype');
        $specific = $this->input->post('specific');

        if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
            $this->db->where(array("paytype" => $period));
        }
        $check_exists = $this->db->select('
                sysid,
                years,
                months,
                payclass,
                paytype    
            ')
            ->from('payroll_reports_group')
            ->where(
                array(
                    'status != ' => 302,
                    'payclass' => $payclass,
                    'years' => $year,
                    'months' => $month
                )
            )
            ->get()->row();

        if ($check_exists) {
            $sql = $this->db->select("pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass")
                ->from("payroll_reports_main as prm")
                ->join("payroll_emplist as payrollemp", "payrollemp.empid = prm.empid", "left")
                ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                ->join("person as p", "p.sysid = pem.personid", "left")
                ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid", "left")
                ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid", "left")
                ->join("prime_employee_main_payclass as pemp", "pemp.emp_id = pem.sysid", "left")
                ->where(array(
                    'prg.sysid' => $check_exists->sysid,
                    'pec.type' => 1,
                    'pec.status' => 1,
                    'prg.status != ' => 302))
                ->group_by("pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass")
                ->order_by("p.lastname")
                ->get();

            if ($sql->num_rows() > 0) {
                $num = 1;
                foreach($sql->result() as $row) {
                    $empid = $row->sysid;
                    $basic = $row->basic;
                    $tax = $row->tax;
                    $totaldeductions = $row->deductions;
                    $totalearnings = $row->earnings;
                    $netpay = $row->net;
                    $data['payrollreportdata'][] = array(
                        "expand" => $empid,
                        "num" => $num++,
                        "empcode" => $row->empid,
                        "name" => $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename,
                        "basic" => number_format($tax, 2),
                        "tax" => number_format($basic, 2),
                        "deductions" => number_format($totaldeductions, 2),
                        "earnings" => number_format($totalearnings, 2),
                        "netpay" => number_format($netpay, 2)
                    );
                }
            } else {
                $msg = 'Payroll transaction not found!';
                $func = 'warning';
                $qry = true;
            }
        }else{
            $msg = 'Payroll not processed yet!';
            $func = 'warning';
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_payslip_data(){
        $data = array();
        $msg = '';
        $func = 'danger';
        $qry = false;
        $payclass = $this->input->post('payclass');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $period = $this->input->post('payrollperiod');
        $nettype = $period - 1;
        $paytype = '';
        $non_confi = array(128,3077,3078);
        if (in_array($payclass,$non_confi)) {
            $paytype = 'AND prg.paytype = '. $period;
        }
        $sql = $this->db->query("
            SELECT
                em.sysid,
                em.empid,
                CONCAT(p.lastname,', ',p.firstname) as empname, 
                prm.ccid, 
                tp.names payclass,  
                SUM(prm.basic) as basic,
                SUM(prm.earnings) AS earnings,
                SUM(prm.deductions) AS deductions,
                SUM(prm.tax) AS tax,
                SUM(prm.net) AS net
            FROM
                prime_employee_main AS em
            INNER JOIN `payroll_reports_main` AS prm ON prm.empid = em.sysid
            INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
            INNER JOIN person AS p ON em.personid = p.sysid
            LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
            LEFT JOIN prime_types_parameter AS tp ON prg.payclass = tp.sysid
            WHERE prg.`status` IN (301)
                AND prg.payclass = $payclass
                AND prg.years = $year
                AND prg.months = $month
                $paytype		
            GROUP BY 
                em.sysid, 
                em.empid, 
                empname, 
                prm.ccid,
                tp.names,  
                ''
            ORDER BY p.lastname ASC
        ");

        if ($sql->num_rows() > 0) {
            $msg = '';
            $func = 'success';
            $qry = true;
            $num = 1;
            $index = 0;
            foreach ($sql->result() AS $row){
                $payclass_ = ($payclass == 1) ? 129 : $payclass;
                $payslip_sent = $this->db->select('empid')
                    ->from('payroll_transactions_bankfile')
                    ->where(array('empid' => $row->sysid, 'payclass' => $payclass_, 'years' => $year, 'months' =>$month, 'nettype' => $nettype, 'mailsent' => 1, 'status' => 1))
                    ->get()->row();

                //$data['sent'][$row->empname.':'.$row->sysid]['return'] = $payslip_sent;
                //$data['sent'][$row->empname.':'.$row->sysid]['query'] = $this->db->last_query();

                $control = '';
                $control .= '<a class="btn btn-danger btn-xs inline" href="'.base_url('reports/payslips/' . $payclass . '/' .$year . '/' .$month . '/' . $period . '/' . $row->sysid).'" target="_blank" class="btn btn-default btn-xs inline"><i class="fa fa-print"></i></a>';
                if ($payslip_sent) {
                    $control .= '<span id="btn_send_payslip" class="btn btn-info btn-xs inline" data-status="sent" data-index="' . $index++ . '" data-empid="' . $row->sysid . '" data-payclass="' . $payclass . '" data-year="' . $year . '" data-month="' . $month . '" data-period="' . $period . '"><i class="fa fa-check"></i></span>';
                } else {
                    $control .= '<a id="btn_send_payslip" class="btn btn-info btn-xs inline" href="javascript:;" data-status="xsent" data-index="' . $index++ . '" data-empid="' . $row->sysid . '" data-payclass="' . $payclass . '" data-year="' . $year . '" data-month="' . $month . '" data-period="' . $period . '"><i class="fa fa-envelope"></i></a>';
                }

                $data['payrollreportdata'][] = array(
                    'expand' => btn_expand($row->sysid),
                    'key' => $row->sysid,
                    'num' => $num++,
                    'empcode' => $row->empid,
                    'name' => $row->empname,
                    'department' => get_costcenter_name($row->ccid,true),
                    'basic' => number_format($row->basic,2),
                    'earnings' => number_format($row->earnings,2),
                    'deductions' => number_format($row->deductions,2),
                    'tax' => number_format($row->tax,2),
                    'netpay' => number_format($row->net,2),
                    'print' => $control
                );
            }
        } else {
            $msg = 'No payroll is approved under these parameters.';
            $func = 'error';
            $qry = true;
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }


    function send_payroll_payslip()
    {
        $data = array();
        $qry = false;
        $sent_num = 0;
        $payclass = $this->input->post('payclass');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $paytype = $this->input->post('paytype');

        if ($payclass == 128) {
            $this->db->where(array("paytype" => $paytype));
        }

        $check_exists = $this->db->select()
            ->from('payroll_reports_group')
            ->where(array(
                'status' => 301,
                'payclass' => $payclass,
                'years' => $year,
                'months' => $month
            ))->get()->row();

        if ($check_exists) {
            $qry = true;
            $sql = $this->db->select("pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass")
                ->from("payroll_reports_main as prm")
                ->join("payroll_emplist as payrollemp", "payrollemp.empid = prm.empid", "left")
                ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                ->join("person as p", "p.sysid = pem.personid", "left")
                ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid", "left")
                ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid", "left")
                ->join("prime_employee_main_payclass as pemp", "pemp.emp_id = pem.sysid", "left")
                ->where(array(
                    'prg.sysid' => $check_exists->sysid,
                    'pec.type' => 1,
                    'pec.status' => 1))
                ->group_by("pem.sysid,payrollemp.accntno,prg.months,prg.years , pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net, prg.paytype , prg.payclass")
                ->order_by("p.lastname")
                ->get();
            if ($sql->num_rows() > 0) {
                foreach ($sql->result() as $row) {
                    $check_mailsent = $this->db->select('empid')
                        ->from('payroll_transactions_bankfile')
                        ->where(
                            array(
                                'empid' => $row->sysid,
                                'years' => $year,
                                'months' => $month,
                                'mailsent' => 1
                            )
                        )
                        ->get()->row();
                    if ($check_mailsent == false) {


                        $form_payslip = form_payslip_single($row->sysid, $month, $year, $row->paytype, $payclass, true);

                        $html = '';
                        $html .= '<html>';
                        $html .= '<head>';
                        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
                        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
                        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
                        $html .= '</head>';
                        $html .= '<body>';
                        $html .= $form_payslip->html;
                        $html .= '</body>';
                        $html .= '</html>';


                        if ($payclass == 128) {
                            if ($check_exists->paytype == 1) {
                                $payslip_term_text = '<u>first half of the month</u>';
                                $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_RANKF.pdf';
                            } else {
                                $payslip_term_text = '<u>second half of the month</u>';
                                $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_RANKF.pdf';
                            }
                        } else {
                            $payslip_term_text = '';
                            $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '_CONFI.pdf';
                        }

                        // Load library
                        $this->load->library('pdf');

                        $dompdf = new Dompdf\Dompdf();

                        $dompdf->loadHtml($html);


                        $customPaper = array(0, 0, 610, 205);
                        $dompdf->setPaper($customPaper, 'portrate');
                        $dompdf->render();
                        // Add PDF Document Information
                        $dompdf->add_info('Subject', 'PECO PAYSLIP | ' . $filename);
                        $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
                        $dompdf->add_info('Creator', 'ITD');
                        $dompdf->add_info('Keywords', 'Payslip');

                        $output = $dompdf->output();

                        $upload_path = FCPATH . 'uploads/employee/payslips/' . $row->sysid . '/';
                        if (!is_dir($upload_path)) {
                            mkdir($upload_path, 0777, true);
                            $year_path = $upload_path . $year;
                            if (!is_dir($year_path)) {
                                mkdir($upload_path . '/' . $year, 0777, true);
                                $month_path = $year_path . '/' . $month . '/';
                                if (!is_dir($month_path)) {
                                    mkdir($year_path . '/' . $month, 0777, true);
                                } else {
                                    chmod($year_path . '/' . $month, 0777);
                                }
                                file_put_contents($month_path . $filename, $output);
                            } else {
                                $month_path = $year_path . '/' . $month . '/';
                                if (!is_dir($month_path)) {
                                    mkdir($year_path . '/' . $month, 0777, true);
                                } else {
                                    chmod($year_path . '/' . $month, 0777);
                                }

                                file_put_contents($month_path . $filename, $output);
                            }
                        } else {
                            $year_path = $upload_path . $year;
                            if (!is_dir($year_path)) {
                                mkdir($upload_path . '/' . $year, 0777, true);
                                $month_path = $year_path . '/' . $month . '/';
                                if (!is_dir($month_path)) {
                                    mkdir($year_path . '/' . $month, 0777, true);
                                } else {
                                    chmod($year_path . '/' . $month, 0777);
                                }

                                file_put_contents($month_path . $filename, $output);
                            } else {
                                $month_path = $year_path . '/' . $month . '/';
                                if (!is_dir($month_path)) {
                                    mkdir($year_path . '/' . $month, 0777, true);
                                } else {
                                    chmod($year_path . '/' . $month, 0777);
                                }

                                file_put_contents($month_path . $filename, $output);
                            }
                        }


                        $emp_arr = get_employee_info($row->sysid);

                        if ($emp_arr && $emp_arr->qry == true) {
                            $qry_contacts = $this->db->select('contactstring')
                                ->from('person_contact_matrix')
                                ->where(array(
                                    'types' => 1057,
                                    'personid' => $emp_arr->personid,
                                    'status' => 1,
                                ))
                                ->get()->row();

                            if ($qry_contacts) {
                                $email = $qry_contacts->contactstring;


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


                                $content = '';
                                $content .= '<html>';
                                $content .= '<head>';
                                $content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
                                $content .= '<title>TVI | Tiedra Ventures, Inc.</title>';
                                $content .= '<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" />';
                                $content .= '
                                        <style type="text/css">
                                        html { 
                                            -webkit-text-size-adjust:none; 
                                            -ms-text-size-adjust: none;
                                        }
                                        body{
                                            font-family: Arial !important;
                                            overflow: visible !important;
                                        }		
                                        </style>
                                        ';
                                $content .= '</head>';
                                $content .= '<body>';

                                $content .= '<div class="container" style="width: 95%; display:inline-block; margin-top: 5px; margin-bottom: 5px; padding: 20px 20px;">';
                                $content .= '<div class="col-xs-12">';

                                $content .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
                                $content .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
                                $content .= '</div>';
                                $content .= '<h4>Hi, ' . $emp_arr->firstname . '!</h4>';
                                $content .= '<br>';

                                $month_code = date_formating($month, '!m', 'M');

                                $content .= '<p>Your payslip for the month of <b>' . $month_code . '</b>, year <b>' . $year . '</b> ' . $payslip_term_text . ' is now available.</p>';
                                $content .= '<p>Please see attached file(s).</p>';
                                $content .= '<br>';
                                $content .= '<p>Thank you,</p>';
                                $content .= '<br>';

                                $content .= '<div style="color: #FFF; font-size: 9px; display: inline-block; background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px; padding: 10px 20px;">';
                                $content .= 'This is a system generated message, please do not reply';
                                $content .= '</div>';

                                $content .= '</div>';
                                $content .= '</div>';
                                $content .= '</body>';
                                $content .= '</html>';

                                //Email content
                                $this->email->to($email);
                                $this->email->from('no-reply@panayelectric.com', 'PECO PAYSLIP | ' . strtoupper($month_code) . '-' . $year);
                                $this->email->subject('PECO PAYSLIP | ' . strtoupper($month_code) . '-' . $year);
                                $this->email->message($content);
                                $this->email->attach($upload_path . '/' . $year . '/' . $month . '/' . $filename);

                                $sent = false;
                                // Send email
                                $sent = $this->email->send();
                                $this->email->clear(true);
                                if ($sent) {
                                    $sent_num += 1;
                                    $this->db->where(array('empid' => $row->sysid, 'years' => $year, 'months' => $month, 'mailsent' => 0));
                                    $this->db->update('payroll_transactions_bankfile', array('mailsent' => 1));
                                }
                            }
                        }

                    } else {
                        $data['message'][$row->sysid] = 'Already sent!';
                    }
                }
            }
        }
        $data['sent'] = $sent_num;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function pdf_monthly_payslip($payclass, $year, $month, $paytype, $empid = false)
    {
        $data = array();
        $qry = false;
        $filename = '';

        if ($payclass == 128 || $payclass == 3077 || $payclass == 3078) {
            $this->db->where(array("paytype" => $paytype));
        }
        $check_exists = $this->db->select('
                sysid,
                years,
                months,
                payclass,
                paytype    
            ')
            ->from('payroll_reports_group')
            ->where(
                array(
                    'status' => 301,
                    'payclass' => $payclass,
                    'years' => $year,
                    'months' => $month
                )
            )
            ->get()->row();

        $data['check_exists'] = $this->db->last_query();

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
        $html .= '</head>';
        $html .= '<body>';

        if ($check_exists) {
            $qry = true;
            if($empid && $empid > 0) {
                $this->db->where('prm.empid', $empid);
            }
            $sql = $this->db->select("pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass")
                ->from("payroll_reports_main as prm")
                ->join("payroll_emplist as payrollemp", "payrollemp.empid = prm.empid AND payrollemp.status = 1", "left")
                ->join("payroll_reports_trn as prt", "prt.payrollid = prm.sysid", "left")
                ->join("payroll_reports_group as prg", "prg.sysid = prm.groupid", "left")
                ->join("prime_employee_main as pem", "pem.sysid = prm.empid", "left")
                ->join("person as p", "p.sysid = pem.personid", "left")
                ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid", "left")
                ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid", "left")
                ->join("prime_employee_main_payclass as pemp", "pemp.emp_id = pem.sysid", "left")
                ->where(array(
                    'prg.sysid' => $check_exists->sysid,
                    'pec.type' => 1,
                    'pec.status' => 1,
                    'prg.status' => 301))
                ->group_by("pem.sysid, payrollemp.accntno, pem.empid,p.firstname,p.lastname,p.middlename,prt.payrollid,pcm.names,prm.basic, prm.deductions , prm.earnings , prm.tax , prm.net , prg.paytype , prg.payclass")
                ->order_by("p.lastname")
                ->get();

            $details = $this->db->last_query();
            if ($sql->num_rows() > 0) {
                $num = 1;
                foreach ($sql->result() as $row) {
                    $form_payslip = form_payslip_single($row->sysid, $month, $year, $row->paytype, $payclass, false, $num++);
                    if ($form_payslip->res) {
                        $html .= $form_payslip->html;
                    }
                }
            }


            if($empid && $empid > 0) {
                $emp_name = get_employee_info($empid);
                $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '-' . $emp_name->lastname . $emp_name->firstname . '.pdf';
            }else {
                if ($payclass == 128) {
                    if ($check_exists->paytype == 1) {
                        $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_RANKF.pdf';
                    } else {
                        $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_RANKF.pdf';
                    }
                } else if ($payclass == 3077) {
                    if ($check_exists->paytype == 1) {
                        $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_TIER1.pdf';
                    } else {
                        $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_TIER1.pdf';
                    }
                } else if ($payclass == 3078) {
                    if ($check_exists->paytype == 1) {
                        $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_TIER2.pdf';
                    } else {
                        $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '-' . str_pad($check_exists->paytype, 2, "0", STR_PAD_LEFT) . '_TIER2.pdf';
                    }
                } else {
                    $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '_CONFI.pdf';
                }
            }
        }
        $html .= '</body>';
        $html .= '</html>';



        $data['filename'] = $filename;
        $data['payclass'] = $payclass;
        $data['html'] = $html;
        $data['details'] = $details;
        $data['qry'] = $qry;
        return (object)$data;
        //print_r($data);
        //echo $html;
        //echo '<br>';
        //print_r('check_exists = '.$data['check_exists']);
        //echo '<br>';
        //print_r('details = '.$details);

    }



    // #######################################################################################
    // #######################################################################################
    // BILLING REPORTS

    function get_billing_register_dist()
    {
        $data = array();

        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $html = '';

        $html .= '<table class="table table-condensed tbl-xs print-table-standard">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>BILLING: ' . $year . '-' . date_formating($month, '!m', 'M') . '</th>';
        $html .= '<th colspan="4" style="text-align: center !important;">REGULAR BILLING</th>';
        $html .= '<th colspan="3" style="text-align: center !important;">LATE BILLING OFFICE</th>';
        $html .= '<th colspan="3" style="text-align: center !important;">LATE BILLING FSI</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th>G-D-L-B</th>';
        $html .= '<th style="text-align: center !important;">KWH</th>';
        $html .= '<th style="text-align: center !important;">AMOUNT</th>';
        $html .= '<th style="text-align: center !important;">CNT</th>';
        $html .= '<th style="text-align: center !important;">SC</th>';
        $html .= '<th style="border-left: 1px solid #000 !important; text-align:center !important;">KWH</th>';
        $html .= '<th style="text-align: center !important;">AMOUNT</th>';
        $html .= '<th style="text-align: center !important;">CNT</th>';
        $html .= '<th style="border-left: 1px solid #000 !important; text-align: center !important;">KWH</th>';
        $html .= '<th style="text-align: center !important;">AMOUNT</th>';
        $html .= '<th style="text-align: center !important;">CNT</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $total_kwh = 0;
        $total_amt = 0;
        $total_cnt = 0;
        $total_cs = 0;

        $qry_group_d = $this->db->query("
            SELECT
            gdlb.g,
            dist.codes
            FROM
            address_districts AS dist
            INNER JOIN gdlb_main AS gdlb ON dist.sysid = gdlb.d
            GROUP BY 
            gdlb.g,
            dist.codes
        ");

        if($qry_group_d->num_rows() > 0) {
            foreach($qry_group_d->result() as $grow) {
                $qry_billing = $this->db->query("
                            SELECT 
                            CONCAT(`group`, '-', dist, '-', lot, '-', book) AS gdlb,
                            
                            SUM(CASE WHEN batch = 'LATEBILL' THEN `current` ELSE 0 END) AS amt_late, 
                            SUM(CASE WHEN batch = '' THEN `current` ELSE 0 END) AS amt_reg, 
                            SUM(kwhuse) AS kwh, SUM(scdisc) AS sc, COUNT(acctid) AS cnt
                            FROM billing_reports_main
                            WHERE `year` = $year AND `month` = $month AND `group` = {$grow->g} AND `dist` = '{$grow->codes}'
                            GROUP BY CONCAT(`group`, '-', dist, '-', lot, '-', book)
                            ORDER BY CONCAT(`group`, '-', dist, '-', lot, '-', book)
                            -- LIMIT 20
                        ");
                if ($qry_billing->num_rows() > 0) {

                    $html .= '<tr>';
                    $html .= '<td colspan="11">'.str_pad($grow->g, 2, '0', STR_PAD_LEFT).'-'.$grow->codes.'</td>';
                    $html .= '</tr>';

                    $class_total_kwh = 0;
                    $class_total_amt = 0;
                    $class_total_cnt = 0;
                    $class_total_cs = 0;
                    foreach ($qry_billing->result() as $brow) {

                        $reg_amt = 0;

                        $html .= '<tr>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;"><span style="width: 120px; display: inline-block;">' . $brow->gdlb . '</span></td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->kwh, 0) . '</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->amt_reg, 2) . '</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->cnt, 0) . '</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->sc, 2) . '</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">000,000</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">'.number_format($brow->amt_late, 2).'</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">00,000</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">000,000</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">000,000,000.00</td>';
                        $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">00,000</td>';
                        $html .= '</tr>';

                        $class_total_kwh += $brow->kwh;
                        $class_total_amt += $brow->amt_reg;
                        $class_total_cnt += $brow->cnt;
                        $class_total_cs += $brow->sc;

                        $total_kwh += $brow->kwh;
                        $total_amt += $brow->amt_reg;
                        $total_cnt += $brow->cnt;
                        $total_cs += $brow->sc;
                    }

                    $html .= '<tr>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;">Sub-Total</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_kwh, 0) . '</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_amt, 2) . '</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_cnt, 0) . '</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_cs, 2) . '</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">000,000</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000,000.00</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">000,000</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000,000.00</td>';
                    $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000</td>';
                    $html .= '</tr>';
                }
            }


        }
        $html .= '<tr>';
        $html .= '<td colspan="11"></td>';
        $html .= '</td>';
        $html .= '<tr>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;">Total</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_kwh, 0) . '</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_amt, 2) . '</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_cnt, 0) . '</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_cs, 2) . '</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">000,000</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000,000.00</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">000,000</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000,000.00</td>';
        $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000</td>';
        $html .= '</tr>';


        $html .= '</tbody>';
        $html .= '</table>';

        $reptitle = 'Billing Registers';
        $header = peco_print_header(user_id(), $reptitle, 'MRD', false);

        $data['header'] = $header;
        $data['datenow'] = date('Y-m-d');
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_billing_register_class() {
        $data = array();

        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $qry_class_main = $this->db->select()
            ->from('prime_system_rate_class_main')
            //->where_in('sysid', array(1, 2, 4))
            ->get();

        $html = '';

        $html .= '<table class="table table-condensed tbl-xs print-table-standard">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>BILLING: '.$year.'-'.date_formating($month, '!m', 'M').'</th>';
        $html .= '<th colspan="4" style="text-align: center !important;">REGULAR BILLING</th>';
        $html .= '<th colspan="3" style="text-align: center !important;">LATE BILLING OFFICE</th>';
        $html .= '<th colspan="3" style="text-align: center !important;">LATE BILLING FSI</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th>G-D-L-B</th>';
        $html .= '<th style="text-align: center !important;">KWH</th>';
        $html .= '<th style="text-align: center !important;">AMOUNT</th>';
        $html .= '<th style="text-align: center !important;">CNT</th>';
        $html .= '<th style="text-align: center !important;">SC</th>';
        $html .= '<th style="border-left: 1px solid #000 !important; text-align:center !important;">KWH</th>';
        $html .= '<th style="text-align: center !important;">AMOUNT</th>';
        $html .= '<th style="text-align: center !important;">CNT</th>';
        $html .= '<th style="border-left: 1px solid #000 !important; text-align: center !important;">KWH</th>';
        $html .= '<th style="text-align: center !important;">AMOUNT</th>';
        $html .= '<th style="text-align: center !important;">CNT</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        if($qry_class_main->num_rows()> 0) {

            $total_kwh = 0;
            $total_amt = 0;
            $total_cnt = 0;
            $total_cs = 0;

            foreach($qry_class_main->result() as $row) {

                $qry_rate_class_group = $this->db->select('cs.codes, cs.descs')
                    ->from('rate_class_group AS cg')
                    ->join("rate_class_specification AS cs", "cs.sysid = cg.classid")
                    ->where(array('rateid' => $row->sysid))
                    ->get();
                if($qry_rate_class_group->num_rows() > 0) {




                    foreach($qry_rate_class_group->result() as $rrow) {

                        $class_name = ($row->classifications!='') ? $row->classifications : $rrow->descs;
                        $class_desc = ($rrow->descs!='' && $rrow->descs != $row->classifications) ? ' ('.$rrow->descs.')' : '';


                        $qry_billing = $this->db->query("
                            SELECT 
                            CONCAT(`group`, '-', dist, '-', lot, '-', book) AS gdlb,
                            SUM(current) AS amt, sum(kwhuse) AS kwh, SUM(scdisc) AS sc, COUNT(acctid) AS cnt
                            FROM billing_reports_main
                            WHERE `year` = $year AND `month` = $month AND rate = '$rrow->codes'
                            GROUP BY CONCAT(`group`, '-', dist, '-', lot, '-', book)
                            -- LIMIT 20
                        ");
                        if($qry_billing->num_rows() > 0) {

                            $html .= '<tr>';
                            $html .= '<td colspan="11">'.$row->sysid. ' - ' . $class_name . $class_desc . '</td>';
                            $html .= '</tr>';

                            $class_total_kwh = 0;
                            $class_total_amt = 0;
                            $class_total_cnt = 0;
                            $class_total_cs = 0;
                            foreach($qry_billing->result() as $brow) {

                                $reg_amt = 0;

                                $html .= '<tr>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;"><span style="width: 120px; display: inline-block;">' . $brow->gdlb . '</span></td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->kwh, 0) . '</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->amt, 2) . '</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->cnt, 0) . '</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">' . number_format($brow->sc, 2) . '</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">000,000</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">000,000,000.00</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">00,000</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">000,000</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">000,000,000.00</td>';
                                $html .= '<td style="font-size: 12px; font-family: Courier, monospace;" class="number">00,000</td>';
                                $html .= '</tr>';

                                $class_total_kwh += $brow->kwh;
                                $class_total_amt += $brow->amt;
                                $class_total_cnt += $brow->cnt;
                                $class_total_cs += $brow->sc;

                                $total_kwh += $brow->kwh;
                                $total_amt += $brow->amt;
                                $total_cnt += $brow->cnt;
                                $total_cs += $brow->sc;
                            }

                            $html .= '<tr>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;">Sub-Total</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_kwh, 0) . '</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_amt, 2) . '</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_cnt, 0) . '</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">' . number_format($class_total_cs, 2) . '</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">000,000</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000,000.00</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">000,000</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000,000.00</td>';
                            $html .= '<td style="font-weight: bold; font-family: Courier, monospace;" class="number">00,000</td>';
                            $html .= '</tr>';

                        }
                    }
                }
            }
            $html .= '<tr>';
            $html .= '<td colspan="11"></td>';
            $html .= '</td>';
            $html .= '<tr>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;">Total</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_kwh, 0) . '</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_amt, 2) . '</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_cnt, 0) . '</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">' . number_format($total_cs, 2) . '</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">000,000</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000,000.00</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">000,000</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000,000.00</td>';
            $html .= '<td style="font-weight: bold; font-family: Courier, monospace; padding-top: 5px !important;" class="number">00,000</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $reptitle = 'Billing Registers';
        $header = peco_print_header(user_id(), $reptitle, 'MRD', false);

        $data['header'] = $header;
        $data['datenow'] = date('Y-m-d');
        $data['html'] = $html;
        return json_encode($data);
    }



// #######################################################################################
// #######################################################################################
// MRD REPORTS

    function get_report_reading() {
        $data = array();
        $html = '';
        $footer_html = '';
        $qry = false;
        $input_date_start = $this->input->post('datestart');
        $input_date_end = $this->input->post('dateend');

        $input_billmo = $this->input->post('billmo');
        $input_billyr = $this->input->post('billyr');
        $input_types = $this->input->post('types');


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

        $qry_meter_reader = $this->db->select('u.sysid, ul.telcode, p.lastname')
            ->from('prime_system_users_roles_matrix AS rm')
            ->join('prime_system_users AS u', 'rm.userid = u.sysid AND u.status = 1')
            ->join('prime_system_users_legacy_code AS ul', 'ul.userid = u.sysid', 'left')
            ->join('person AS p', 'p.sysid = u.personid')
            ->where(array('rm.roleid' => 22, 'rm.status' => 1))
            ->group_by('u.sysid, ul.telcode, p.lastname')
            ->order_by('ul.telcode')
            ->get();
        if($qry_meter_reader->num_rows() > 0) {
            $total_unread = 0;
            $total_read = 0;
            $total_customer = 0;
            $total_recheck = 0;

            foreach($qry_meter_reader->result() as $rrow) {
                if($rrow->telcode!='') {
                    $username = $rrow->telcode.' - '.$rrow->lastname;
                }else{
                    $username = $rrow->lastname;
                }
                if($input_types==1) {
                    $html .= '<tr style="background: #a6c8e6">';
                    $html .= '<td>' . $username . '</td>';
                    $html .= '<td colspan="6"></td>';
                    $html .= '</tr>';
                }


                $reader_total_read = 0;
                $reader_total_unread = 0;
                $reader_total_recheck = 0;
                $reader_total_customer = 0;



                foreach ($dates as $dt) {
                    // CHECK SCHEDULE

                    $qry_assignments = $this->db->query("
                        SELECT sm.sysid, sm.gdlbid, COUNT(DISTINCT(ml.acctid)) AS cust, 
                        -- COUNT(DISTINCT(mrl.acctid)) AS readings,
                        CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb
                        FROM reading_schedule_main AS sm
                        INNER JOIN reading_schedule_reader AS sr ON sr.schedid = sm.sysid
                        INNER JOIN reading_schedule_meters_logs AS ml ON ml.schedid = sm.sysid
                        -- LEFT JOIN customer_accounts_subscription_meter_reading_logs AS mrl ON mrl.acctid = ml.acctid AND mrl.status = 1
                        LEFT JOIN gdlb_main AS gm ON gm.sysid = sm.gdlbid 
                        LEFT JOIN address_districts AS d ON d.sysid = gm.d
                        WHERE
                        sm.status != 0
                        AND sm.datesched = '{$dt}'
                        AND sr.userid = {$rrow->sysid}
                        AND sm.months = {$input_billmo}
                        AND sm.years = {$input_billyr}
                         -- AND CAST(mrl.datecreated AS DATE) BETWEEN '{$first_day_this_month}' AND '{$last_day_this_month}'
                        GROUP BY sm.sysid, sm.gdlbid
                    ");
                    $assignment_num = $qry_assignments->num_rows();

                    if($assignment_num>0) {
                        $is_weekend_class = '';
                        if(isWeekend($dt)) {
                            $is_weekend_class = ' text-danger danger';
                        }
                        $is_weekend_class .= ' date-gdlb ';
                        $nameOfDay = date('l', strtotime($dt));

                        // SHOW IF TYPES is 1 (Detailed)
                        if($input_types==1) {
                            $html .= '<tr class="' . $is_weekend_class . '">';
                            $html .= '<td>&nbsp;</td>';
                            $html .= '<td colspan="6">' . $dt . ' - ' . $nameOfDay . '</td>';
                            $html .= '</tr>';
                        }
                        foreach($qry_assignments->result() as $arow) {
                            $qry_cnt_readings = $this->db->query("
                                SELECT COUNT(DISTINCT(mrl.acctid)) AS cnt FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                INNER JOIN reading_schedule_reader AS sr ON sr.schedid = mrl.schedid
                                WHERE 
                                mrl.schedid = {$arow->sysid} 
                                AND sr.userid = {$rrow->sysid}
                                AND mrl.status = 1
                                AND CAST(mrl.datecreated AS DATE) BETWEEN '{$first_day_this_month}' AND '{$last_day_this_month}'
                            ")->row();


                            $recheck = 0;
                            $qry_cnt_recheck = $this->db->query("
                                SELECT COUNT(DISTINCT(ral.acctid)) AS cnt FROM trn_reading_analysis_logs AS ral
                                INNER JOIN reading_schedule_reader AS sr ON sr.schedid = ral.schedid
                                WHERE 
                                ral.schedid = {$arow->sysid} 
                                AND sr.userid = {$rrow->sysid}
                                AND ral.status = 1
                                AND CAST(ral.datecreated AS DATE) BETWEEN '{$first_day_this_month}' AND '{$last_day_this_month}'
                            ")->row();
                            if($qry_cnt_recheck) {
                                $recheck = $qry_cnt_recheck->cnt;
                            }

                            $readings = 0;
                            if($qry_cnt_readings) {
                                $readings = $qry_cnt_readings->cnt;
                            }

                            $readings_class = '';
                            $gdlb_bg = '';
                            $gdlb_txt = '';
                            $customers = $arow->cust;
                            if ($readings >= $customers) {
                                $readings_class = '';
                                $gdlb_bg = '';
                                $gdlb_txt = 'text-success';
                            }

                            $check_specific = get_specific_reader_sched($arow->sysid, $rrow->sysid);
                            $customers = ($check_specific > 0) ? $check_specific : $customers;


                            $diff = ($customers - $readings);
                            $unread = ($diff < 0) ? 0 : $diff;
                            $read = ($readings > $customers) ? $customers : $readings;

                            $total_unread += $unread;
                            $total_read += $read;
                            $total_recheck += $recheck;
                            $total_customer += $customers;

                            $reader_total_read += $read;
                            $reader_total_unread += $unread;
                            $reader_total_recheck += $recheck;
                            $reader_total_customer += $customers;

                            // SHOW IF TYPES is 1 (Detailed)
                            if($input_types==1) {
                                $html .= '<tr class="' . $gdlb_txt . ' ' . $gdlb_bg . '">';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>' . $arow->gdlb . '</td>';
                                $html .= '<td style="padding-right: 30px;" class="number ' . $readings_class . '">' . number_format($readings). '</td>';
                                $html .= '<td style="padding-right: 30px;" class="number ' . $readings_class . '">' . number_format($unread). '</td>';
                                $html .= '<td style="padding-right: 30px;" class="number  text-danger ' . $readings_class . '">' . number_format($recheck). '</td>';
                                $html .= '<td style="padding-right: 30px;" class="number ' . $readings_class . '">' . number_format($customers). '</td>';
                                $html .= '</tr>';
                            }
                        }
                    }
                }

                if($input_types==2) {

                    if($input_types==2) {
                        $html .= '<tr>';
                        $html .= '<td colspan="3">' . $username . '</td>';
                        $html .= '<td style="padding-right: 30px;" class="number text-success">'.number_format($reader_total_read).'</td>';
                        $html .= '<td style="padding-right: 30px;" class="number">'.number_format($reader_total_unread).'</td>';
                        $html .= '<td style="padding-right: 30px;" class="number text-danger">'.number_format($reader_total_recheck).'</td>';
                        $html .= '<td style="padding-right: 30px;" class="number">'.number_format($reader_total_customer).'</td>';
                        $html .= '</tr>';
                    }
                }

                // SHOW IF TYPES is 1 (Detailed)
                if($input_types==1) {
                    // DIVIDER
                    $html .= '<tr>';
                    $html .= '<td colspan="7">&nbsp;</td>';
                    $html .= '</tr>';
                }
            }

            $html .= '<tr class="total">';
            $html .= '<td class=" text-bold">Total</td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td style="padding-right: 30px;" class="number text-success text-bold">'.number_format($total_read).'</td>';
            $html .= '<td style="padding-right: 30px;" class="number  text-bold">'.number_format($total_unread).'</td>';
            $html .= '<td style="padding-right: 30px;" class="number  text-danger text-bold">'.number_format($total_recheck).'</td>';
            $html .= '<td style="padding-right: 30px;" class="number  text-bold">'.number_format($total_customer).'</td>';
            $html .= '</tr>';

            $reading_precentage = 0;
            if($total_customer > 0) {
                $reading_precentage = round((($total_read / $total_customer) * 100), 0);
            }

            if($input_types==2) {
                $footer_html .= '<hr style="border: 1px dashed #333; margin: 20px 0px;">';
                $footer_html .= 'A total of <b>'.number_format($total_read).'</b> ('.$reading_precentage.'%) readings out of <b>'.number_format($total_customer).'</b> customers, with <b>'.number_format($total_unread).'</b> unread, as of date '.$first_day_this_month .' to '. $last_day_this_month.'.';
            }
        }
        $reptitle = 'Reading Reports';
        $header = peco_print_header('', $reptitle, 'MRD', false);


        $data['header'] = $header;
        $data['footer'] = $footer_html;
        $data['daterange'] = $first_day_this_month .' - '. $last_day_this_month;
        $data['datenow'] = date('Y-m-d');

        $data['html'] = $html;
        return json_encode($data);
    }

    function get_reading_reports_excel($datastart, $dateend, $billmo, $billyr) {

        //load our new PHPExcel library
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('READING REPORTS');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'CODE');
        $this->excel->getActiveSheet()->setCellValue('B1', 'LASTNAME');
        $this->excel->getActiveSheet()->setCellValue('C1', 'DATE');
        $this->excel->getActiveSheet()->setCellValue('D1', 'GDLB');
        $this->excel->getActiveSheet()->setCellValue('E1', 'READ');
        $this->excel->getActiveSheet()->setCellValue('F1', 'UNREAD');
        $this->excel->getActiveSheet()->setCellValue('G1', 'RECHECK');
        $this->excel->getActiveSheet()->setCellValue('H1', 'TOTAL');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('E1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('F1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('G1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('H1')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);


        $qry_reading_reps = $this->db->query("
            SELECT CAST(sm.datesched AS DATE) AS datesched, sm.sysid, sm.gdlbid, sr.userid, COUNT(DISTINCT(ml.acctid)) AS cust, p.lastname, ul.telcode,
            CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb
            FROM reading_schedule_main AS sm
            INNER JOIN reading_schedule_reader AS sr ON sr.schedid = sm.sysid
            INNER JOIN reading_schedule_meters_logs AS ml ON ml.schedid = sm.sysid
            LEFT JOIN prime_system_users AS u ON u.sysid = sr.userid
            LEFT JOIN prime_system_users_legacy_code AS ul ON ul.userid = sr.userid
            LEFT JOIN person AS p ON p.sysid = u.personid
            LEFT JOIN gdlb_main AS gm ON gm.sysid = sm.gdlbid 
            LEFT JOIN address_districts AS d ON d.sysid = gm.d
            WHERE
            sm.status != 0
            AND CAST(sm.datesched AS DATE) BETWEEN '{$datastart}' AND '{$dateend}'
            AND sm.months = $billmo
            AND sm.years = $billyr
            GROUP BY CAST(sm.datesched AS DATE), sm.sysid, sm.gdlbid, sr.userid,  p.lastname, ul.telcode
            ORDER BY ul.telcode
        ");

        $excel_num = 1;
        if($qry_reading_reps->num_rows() > 0) {
            foreach($qry_reading_reps->result() as $row) {

                $qry_cnt_readings = $this->db->query("
                                SELECT COUNT(DISTINCT(mrl.acctid)) AS cnt 
                                FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                INNER JOIN reading_schedule_reader AS sr ON sr.schedid = mrl.schedid
                                WHERE 
                                mrl.schedid = {$row->sysid} 
                                AND sr.userid = {$row->userid}
                                AND mrl.status = 1
                                AND CAST(mrl.datecreated AS DATE) BETWEEN '{$datastart}' AND '{$dateend}'
                            ")->row();
                $readings = 0;
                if($qry_cnt_readings) {
                    $readings = $qry_cnt_readings->cnt;
                }
                $customers = $row->cust;
                $check_specific = get_specific_reader_sched($row->sysid, $row->userid);
                $customers = ($check_specific > 0) ? $check_specific : $customers;
                $diff = ($customers - $readings);
                $unread = ($diff < 0) ? 0 : $diff;
                $read = ($readings > $customers) ? $customers : $readings;
                $recheck = 0; //@TODO create recheck query;
                $excel_num += 1;
                $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->telcode);
                $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->lastname);
                $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->datesched);
                $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->gdlb);
                $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $read);
                $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $unread);
                $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $recheck);
                $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $customers);
            }
        }



        $month_name = strtoupper(date_formating($billmo, 'm', 'M'));
        $filename='READING_REPORTS_OF_BILLING'.$month_name.$billyr.'_ASOF_'.$datastart.'_TO_'.$dateend.'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }


    function get_mrd_sched_data() {
        $data = array();
        $q = false;
        $data_arr = $this->input->post('data');
        $exec = $this->input->post('exec');
        $month = $data_arr['month'];
        $year = $data_arr['year'];
        $dist = $data_arr['dist'];

        if($dist != '') {
            $dist_where = '';
            if ($dist && $dist > 0) {
                $dist_where = " AND GDLB.d = $dist";
            }
            $qry_gdlb = $this->db->query("
                SELECT 
                GDLB.sysid AS SYSID, 
                GDLB.limit AS LMT, 
                COUNT(DISTINCT(ACCT.sysid)) AS ACCTNO, 
                CONCAT(GDLB.g, '-', DIST.codes, '-', GDLB.l, '-', GDLB.b) AS GDLBNAME,
                SR.userid,
                SM.sysid AS SCHEDID,
                SM.datesched AS SCHEDULE
                FROM gdlb_main AS GDLB
                JOIN customer_accounts_main AS ACCT ON ACCT.gdlb = GDLB.sysid AND ACCT.`status` = 1
                JOIN address_districts AS DIST ON DIST.sysid = GDLB.d
                LEFT JOIN reading_schedule_main AS SM on SM.gdlbid = GDLB.sysid AND SM.`status` = 1
                LEFT JOIN reading_schedule_reader AS SR ON SR.schedid = SM.sysid AND SR.`status` = 1
                WHERE GDLB.g < 6 {$dist_where} AND SM.years = {$year} AND SM.months = {$month}
                GROUP BY ACCT.gdlb, GDLB.`limit`, SR.userid, SM.sysid
                ORDER BY CONCAT(GDLB.g, '-', DIST.codes, '-', GDLB.l, '-', GDLB.b)
            ");
            if ($qry_gdlb->num_rows() > 0) {
                foreach ($qry_gdlb->result() as $row) {
                    $fix = false;
                    $status = '';
                    $rowclass = '';
                    $readings = 0;
                    $userid = $row->userid;
                    $info = get_users_info_full($userid);

                    $info_user = ($info) ? '<span class="label label-danger">'.$info->telcode.'</span> ' . $info->lastname : $userid;
                    $bg_width = 0;
                    $bg_color = 'rgba(160, 229, 91, 0.25)';
                    if ($row->ACCTNO > 0) {
                        $bg_width = ($row->ACCTNO / $row->LMT) * 100;
                        if ($bg_width > 80) {
                            $bg_color = 'rgba(237, 109, 109, 0.30)';
                        }
                    }

                    if(trim($userid) == '') {
                        $fix = true;
                    }

                    if($row->SCHEDID > 0 && $row->userid > 0) {
                        $check_specific = get_specific_reader_sched($row->SCHEDID, $row->userid);
                        $customers = ($check_specific > 0) ? $check_specific : $row->ACCTNO;
                    }else{
                        $customers = $row->ACCTNO;
                    }


                    $gdlb_stats = '<span style="position: absolute; letter-spacing: -1px;top: 0px; left: 0px; display: inline-block; height: 100%; float: left; margin-top: -2px !important; margin-left: 0px; width: ' . $bg_width . '%; background: ' . $bg_color . '"></span>' . $row->ACCTNO . ' / ' . $row->LMT;


                    $qry_cnt_readings = $this->db->query("
                            SELECT COUNT(DISTINCT(mrl.acctid)) AS cnt 
                            FROM customer_accounts_subscription_meter_reading_logs AS mrl
                            WHERE 
                            mrl.schedid = {$row->SCHEDID} 
                            AND mrl.status = 1
                        ")->row();
                    $readings = 0;
                    if ($qry_cnt_readings) {
                        $readings = $qry_cnt_readings->cnt;
                    }

                    if($row->SCHEDID == NULL) {
                        $status .=  '<span class="label label-warning">Schedule</span>';
                        $fix = true;
                    }


                    if($readings > $customers) {
                        $readings_stat_width = 100;
                    }else{
                        $readings_stat_width = ($readings / $customers) * 100;
                    }
                    $assignment_stat_width = 100 - $readings_stat_width;

                    $reading_stats = '<span style="position: absolute; letter-spacing: -1px;top: 0px; left: 0px; display: inline-block; height: 100%; float: left; margin-top: 0px !important; margin-left: 0px; width: ' . $readings_stat_width . '%; background: rgba(255, 106, 38, 0.3)"></span>';
                    $assign_stats = '<span style="position: absolute; letter-spacing: -1px;top: 0px; right: 0px; display: inline-block; height: 100%; float: right; margin-top: 0px !important; margin-left: 0px; width: ' . $assignment_stat_width . '%; background: rgba(38, 171, 255, 0.3)"></span>';

                    $assignment_stats = $readings . '/' . $customers .$reading_stats.$assign_stats;

                    $mtr_logs_stat = '';

                    if($info && $userid > 0) {
                        $qry_assignments_logs = $this->db->query("
                            SELECT COUNT(DISTINCT(ml.acctid)) AS cnt
                            FROM reading_schedule_meters_logs AS ml
                            INNER JOIN reading_schedule_reader AS sr ON sr.schedid = ml.schedid AND sr.`status` = 1
                            WHERE ml.schedid = {$row->SCHEDID} 
                            AND sr.userid = {$userid} 
                            AND ml.`status` = 1 
                        ")->row();
                        if ($qry_assignments_logs) {
                            if ($qry_assignments_logs->cnt > $customers) {
                                $mtr_logs_stat = 'Logs <span class="label label-info">' . $qry_assignments_logs->cnt . '</span>';
                                $fix = true;
                            }
                        }

                        $qry_assignments_logs_null = $this->db->query("
                            SELECT COUNT(DISTINCT(ml.acctid)) AS cnt
                            FROM reading_schedule_meters_logs AS ml
                            INNER JOIN reading_schedule_reader AS sr ON sr.schedid = ml.schedid AND sr.`status` = 1
                            WHERE ml.schedid = {$row->SCHEDID} 
                            AND sr.userid = {$userid} AND (ml.prvrdg IS NULL OR ml.prsrdg IS NULL)
                            AND ml.`status` = 1 
                        ")->row();
                        if ($qry_assignments_logs_null) {
                            if ($qry_assignments_logs_null->cnt > 0) {
                                $mtr_logs_stat = 'Prev Rdg. <span class="label label-info">' . $qry_assignments_logs_null->cnt . '</span>';
                                $fix = true;
                            }
                        }
                    }

                    $fix_btn = '';
                    if($fix==true) {
                        $fix_btn = '<a id="btn_row_fix" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-wrench"></i> Fix</a>';
                        $rowclass = 'danger';
                    }


                    $input_hidden = '';
                    $input_hidden .= '<input id="gdlbid" value="'.$row->SYSID.'" type="hidden" />';
                    $input_hidden .= '<input id="schedid" value="'.$row->SCHEDID.'" type="hidden" />';
                    $input_hidden .= '<input id="userid" value="'.$userid.'" type="hidden" />';

                    $data['list'][] = array(
                        'sysid' => $row->SCHEDID . $input_hidden,
                        'gdlb' => $row->GDLBNAME,
                        'gdlbstat' => $gdlb_stats,
                        'assignments' => $info_user,
                        'schedule' => $row->SCHEDULE,
                        'reading' => $assignment_stats,
                        'status' => $status . $mtr_logs_stat,
                        'fix' => $fix_btn,
                        'rowclass' => $rowclass
                    );
                }
                $q = true;
            }
        }

        $data['qry'] = $q;
        return json_encode($data);

    }

    function download_employees_annual_bankfile($tyesid, $year, $month, $paytype, $payclass) {

        $confi_payclass = array();
        $scurrent = '';
        $textname = 'IX7';
        $annual_ename = get_types_label_format($tyesid, false, false, false, false, false, true)->text;

        $upload_path = FCPATH.'uploads/payroll/annualearnings/'.$annual_ename;
        $rank_nfile_folder = $upload_path;
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0775, true);
        }
        $downloadpath = FCPATH . "uploads\\payroll\\annualearnings\\".$annual_ename."\\".$textname.str_pad($month,2,"0",STR_PAD_LEFT).date('d').substr( $year, -2)."01.txt";
        $systempath = FCPATH . "uploads/payroll/annualearnings/".$annual_ename."/".$textname.str_pad($month,2,"0",STR_PAD_LEFT).date('d').substr( $year, -2)."01.txt";


        //fopen($filepath, "w");
        fopen($systempath, "w");
        file_put_contents($systempath, "H         10810132451       IX7".$year.str_pad($month,2,"0",STR_PAD_LEFT).date('d')."\r\n");

        $scurrent = file_get_contents($systempath);
        /* $qry_annual_e = $this->db->query("
             SELECT
             pme.sysid,
             pme.empid,
             pme.gross,
             pme.tax,
             pme.deduction,
             pme.sent,
             pe.accntno
             FROM
             payroll_manual_earnings AS pme
             INNER JOIN payroll_emplist AS pe ON pe.empid = pme.empid
             JOIN prime_employee_main AS pem ON pem.sysid = pe.empid
             WHERE
             pme.`year` = $year AND
             pme.`month` = $month AND
             pme.sent = 0 AND
             pme.`status` = 305 AND
             pme.`typesid` = $tyesid AND
             pem.status = 1

         ");
        if($payclass > 0){
            $this->db->where(array("pemp.payclass_id" => $payclass , "pemp.status" => 1));
        }
        $qry_annual_e = $this->db->select("pme.sysid,
            pme.empid,
            pme.gross,
            pme.tax,
            pme.deduction,
            pme.sent,
            pe.accntno")
            ->from("payroll_manual_earnings AS pme")
            ->join("payroll_emplist AS pe","pe.empid = pme.empid","inner")
            ->join("prime_employee_main AS pem","pem.sysid = pe.empid")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid")
            ->where(array("pme.year" => $year,"pme.`month`" => $month,
                "pme.sent" => 0,"pme.`status`" => 305,"pme.`typesid`" => $tyesid,
                "pem.status" => 1))
            ->get();
        */

        if ($payclass > 0) {
            if ($payclass == 1) {
                //GET CONFIDENTIAL PAYCLASS UNDER NORMAL PAYROLL
                $confi = $this->db->select('payclass')->from('prime_employee_main_payclass_grouping')
                    ->where(array('payrollpayclass' => 1, 'status' => 1))->get();

                //echo $this->db->last_query();
                if ($confi->num_rows() > 0) {
                    foreach ($confi->result() AS $rows) {
                        $confi_payclass[] = $rows->payclass;
                    }
                    $payclass_id = 'AND pemp.payclass_id IN (' . implode(',', $confi_payclass) . ')';
                }
            } else {
                $payclass_id = 'AND pemp.payclass_id = ' . $payclass;
            }
        } else {
            $payclass_id = '';
        }

        $qry_annual_e = $this->db->query("
            SELECT
                pe.accntno,
                pe.empid
            FROM
            payroll_emplist AS pe
            JOIN prime_employee_main AS pem ON pem.sysid = pe.empid
            JOIN prime_employee_main_payclass AS pemp ON pemp.emp_id = pem.sysid AND pemp.`status` = 1
            WHERE
            pe.`status` != 0
            $payclass_id
        ");

        //echo $this->db->last_query().'<br><br>';
        //exit();
        if($qry_annual_e->num_rows()>0) {

            $empcount = 0;
            $totalnetpayall = 0;
            foreach($qry_annual_e->result() as $row) {
                $qry_earnings_annual = $this->db->query("
                    SELECT 
                    `pme`.`gross`,
                    `pme`.`tax`,
                    `pme`.`deduction`,
                    `pme`.`sent`,
                    `pme`.`status`,
                    `pme`.`paytype`
                    FROM payroll_manual_earnings AS pme
                    WHERE 
                        `pme`.empid = {$row->empid} 
                    AND `pme`.`year` = '$year' 
                    AND `pme`.`month` = '$month' 
                    AND `pme`.`sent` = 0 
                    AND `pme`.`status` = 305 
                    AND `pme`.`typesid` = '$tyesid'
                    AND `pme`.`paytype` = $paytype
                    ORDER BY `pme`.sysid DESC
                ")->row();

                //echo $empcount++.' : '.$row->empid.' : '.$this->db->last_query().'<br><br>';
                //exit();

                $totalnetpay = ($qry_earnings_annual) ? ($qry_earnings_annual->gross - ($qry_earnings_annual->deduction + $qry_earnings_annual->tax)) : 0;

                if($totalnetpay>0) { // ADDED FOR ERROR ZERO AMOUNT
                    $scurrent .= '0' . $row->accntno . "      " . number_format($totalnetpay, 2, '.', '') . "\r\n";
                    // Write the contents back to the file
                    file_put_contents($systempath, $scurrent);
                    $totalnetpayall += $totalnetpay;
                    $empcount++;
                }
            }

            $scurrent .= "T        " . number_format($empcount . $totalnetpayall, 2, '.', '') . "\r\n";


            file_put_contents($systempath, $scurrent);


            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($systempath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($systempath));
            readfile($systempath);
        }


        /*exit();


        if($qry_annual_e->num_rows() > 0) {
            $empcount = 0;
            $totalnetpayall = 0;
            foreach($qry_annual_e->result() as $row) {
                $totalnetpay = ($row->gross - ($row->deduction + $row->tax));
                $scurrent .= '0'.$row->accntno . "      " . number_format($totalnetpay, 2, '.', '') . "\r\n";
                // Write the contents back to the file
                file_put_contents($systempath, $scurrent);
                $totalnetpayall += $totalnetpay;
                $empcount++;
            }
            $scurrent .="T        ".number_format($empcount.$totalnetpayall, 2, '.', '')."\r\n";


            file_put_contents($systempath, $scurrent);


            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($systempath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($systempath));
            readfile($systempath);
        }*/
    }
    function print_annual_report(){
        $data = array();
        $html = '';
        $typesid = $this->input->post('typesid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $paytype = $this->input->post('paytype');
        $viewtype = $this->input->post('viewtype');

        $viewtype_arr = array();

        $gettitle = $this->db->select("desc")->from("prime_types_parameter")
            ->where(array("sysid" => $typesid))
            ->get()->row();

        $html .= peco_print_header(user_id(), ($gettitle) ? $gettitle->desc : '', 0, false);

        $jobcatarr = array(157, 160);

        //GET CONFIDENTIAL PAYCLASS UNDER NORMAL PAYROLL
        $confi = $this->db->select('payclass')->from('prime_employee_main_payclass_grouping')
            ->where(array('payrollpayclass' => 1, 'status' => 1))->get();

        if ($confi->num_rows() > 0) {
            foreach ($confi->result() AS $rows) {
                $viewtype_arr[] = $rows->payclass;
            }
        }

        if ($viewtype > 0) {
            if ($viewtype == 1) {
                $this->db->where_in('pemp.payclass_id', $viewtype_arr);
            } else {
                $this->db->where('pemp.payclass_id', $viewtype);
            }
        }

        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname, pemp.payclass_id , pe.accntno")
            ->from("prime_employee_main as pem")
            ->join("payroll_emplist as pe" , "pe.empid = pem.sysid")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid")
            ->where(array("pem.status != " => 0 , "pe.status != " => 0 , "pemjc.status != " => 0))
            ->where_in('pemjc.jobcatid', $jobcatarr)
            ->order_by("p.lastname")
            ->get();

        $data['empqry'] = $this->db->last_query();

        if($sql->num_rows() > 0){

            $html .= '<table class="table table-bordered table-condensed">';
            $html .= '<thead>';
            $html .= '<th>Account No</th>';
            $html .= '<th>Name</th>';
            $html .= '<th>Gross</th>';
            $html .= '<th>Deduction</th>';
            $html .= '<th>Tax</th>';
            $html .= '<th>Net</th>';
            $html .= '</thead>';
            $html .= '<tbody>';

            $num = 1;
            $grosstotal = 0;
            $deductiontotal = 0;
            $taxtotal = 0;
            $nettotal = 0;

            foreach ($sql->result() as $row){
                $statusarr = array(305, 307);
                $getencodedvalue = $this->db->select("pme.gross,pme.tax,pme.status , pme.deduction")->from("payroll_manual_earnings as pme")
                    ->where(array("pme.typesid" => $typesid , "pme.empid" => $row->sysid  , "pme.month" => $month , "pme.year" => $year ,
                        "pme.paytype" => $paytype))
                    ->where_in('pme.status' , $statusarr)
                    ->get()->row();
                if($getencodedvalue){
                    if($getencodedvalue->status == 305){
                        $stat = '<span class="label label-sm label-success"> Done </span>';
                    }else if($getencodedvalue->status == 307){
                        $stat = '<span class="label label-sm label-primary"> Draft </span>';
                    }
                    $grossencoded = $getencodedvalue->gross;
                    $taxencoded = $getencodedvalue->tax;
                    $netautocompute = $getencodedvalue->gross - ($getencodedvalue->tax + $getencodedvalue->deduction);
                    $deduction = $getencodedvalue->deduction;
                }else{
                    $grossencoded = 0;
                    $taxencoded = 0;
                    $netautocompute =0;
                    $deduction=0;
                    $stat = '<div id="setstat"></div>';
                }
                if($netautocompute != 0){
                    $html .= '<tr>';
                    $html .= '<td>'.$row->accntno.'</td>';
                    $html .= '<td>'.$row->lastname.', '.$row->firstname.'</td>';
                    $html .= '<td class="number">'.number_format($grossencoded , 2).'</td>';
                    $html .= '<td class="number">'.number_format($deduction , 2).'</td>';
                    $html .= '<td class="number">'.number_format($taxencoded , 2).'</td>';
                    $html .= '<td class="number">'.number_format($netautocompute , 2).'</td>';
                    $html .= '</tr>';

                    $grosstotal += $grossencoded;
                    $deductiontotal += $deduction;
                    $taxtotal += $taxencoded;
                    $nettotal += $netautocompute;
                }

            }
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td class="number bold">'.number_format($grosstotal , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($deductiontotal , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($taxtotal , 2).'</td>';
            $html .= '<td class="number bold">'.number_format($nettotal , 2).'</td>';
            $html .= '</tr>';
            $html .= '</tbody>';





            $html .= '</table>';
            $html .= '<hr>';
            $special_pay_arr = array(3072,264,265);
            if(in_array($typesid,$special_pay_arr)){
                $html .= '<div class="row" style="margin-top: 50px;">';
                $html .= '<div class="col-md-6">';
                $html .= '<span>Note: This is to authorize you to debit PECO Savings Account 0810-13245-1 in the amount stated here representing payroll of PECO employees indicated in this list. THANK YOU!';
                $html .= '</div>';
                $html .= '</div>';
            }


            $html .= draw_report_signatory($typesid, 2,0.2);

            $html .= '</div>';
        }


        $data['html'] = $html;
        return json_encode($data);
    }
    function send_annual_payslips(){
        $data = array();
        $typesid = $this->input->post('typesid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $paytype = $this->input->post('paytype');

        $jobcatarr = array(157 , 160);
        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname, pemp.payclass_id , pe.accntno")
            ->from("payroll_manual_earnings as pme")
            ->join("prime_employee_main as pem","pem.sysid = pme.empid")
            ->join("payroll_emplist as pe" , "pe.empid = pem.sysid")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid")
            ->where(array("pem.status" => 1 , "pe.status" => 1 , "pemjc.status" => 1,"pme.status" => 305))
            ->where_in('pemjc.jobcatid', $jobcatarr)
            ->order_by("p.lastname")
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['data'][] = array(
                    'empid' => $row->sysid,
                    'name' => $row->lastname.', '.$row->firstname,
                    'payclass' => $row->payclass,
                    'accntno' => $row->accntno
                );
            }
        }


        //return json_encode($data);
    }
    function payroll_report_data() {
        $data = array();
        $html = '';
        $payclass = $this->input->post('class');
        $ccid = $this->input->post('dept');
        $moduleid = $this->input->post('moduleid');

        $process = $this->input->post('process');
        $clear = $this->input->post('clear');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $preview = $this->input->post('preview');
        $parentid = 0;
        $i = 1;
        $stat = $this->input->post('status');
        $hashcode =  $this->input->post('modulehash');
        $view_type = $this->input->post('viewtype');
        $dept = $this->input->post('dept');
        $typestat = $this->input->post('typestat');
        $payclass_filter = $this->input->post('payclass');
        $jobcat = $this->input->post('jobcat');

        /* $year = $this->input->post('year');
         $month = $this->input->post('month');
         $payclass = $this->input->post('payclass');
         $table_view = $this->input->post('viewtype');
         $paytype = $this->input->post('paytype');
         $process = 1;
         $view_type = 1; */

        $year = 2019;
        $month = 7;
        $payclass = 128;
        $table_view = 1;
        $paytype = 1;
        $process = 1;
        $view_type = 1;

        if($payclass>0) {
            if($payclass==1) {
                $this->db->where('empc.payclass_id != ', 128);
                $this->db->where('empc.payclass_id != ', 131);
            }else {
                $this->db->where('empc.payclass_id', $payclass);
            }
        }
        if($ccid > 0) {
            $this->db->where('ec.ccid', $ccid);
        }

        $jobcatarr = array(157 , 160);
        if($view_type == 1 || $view_type == 4){
            $this->db->where(array('emp.status' => 1,"pe.status" => 1,"ec.type" => 1,"peb.status" => 1));
            $this->db->where_in('pemjc.jobcatid' , $jobcatarr);
            $typestat = 1;
        }else{
            //2 is ALL
            if($stat != 2){
                $this->db->where(array("emp.status" => $stat));
            }
            if($jobcat > 0){
                $this->db->where(array("pemjc.jobcatid" => $jobcat));
            }
            if($payclass_filter > 0){
                $this->db->where(array('empc.payclass_id' =>$payclass_filter));
            }
            if($dept > 0){
                $this->db->where(array('cc.sysid' =>$dept));
            }
        }

        if($typestat > 0){
            $this->db->where('emp.type', $typestat);
        }

        $query = $this->db->select('
                    emp.sysid, 
                    emp.empid,
                    emp.status,
                    emp.personid,
                    emp.datecreated,
                    emp.createdby,
                    ec.ccid,
                    p.firstname,
                    p.lastname,
                    p.middlename,
                    p.gender,
                    p.birthdate,
                    cc.desc AS deptname,
                    cc.names AS deptcode,
                    cc.codes,
                    empc.payclass_id AS payclassid,
                    peb.bioid,
                    pe.accntno
                ')
            ->from('prime_employee_main AS emp')
            ->join('prime_employee_main_payclass AS empc', 'empc.emp_id = emp.sysid AND empc.status = 1', 'left')
            ->join('person AS p', 'p.sysid = emp.personid', 'left')
            ->join('prime_employee_costcenter AS ec', 'ec.empid = emp.sysid AND ec.status = 1', 'left')
            ->join('prime_costcenter_main AS cc', 'cc.sysid = ec.ccid AND ec.status = 1', 'left')
            ->join('payroll_emplist as pe','pe.empid = emp.sysid' , 'left')
            ->join('prime_employee_main_job_category as pemjc' , "pemjc.empid = emp.sysid && pemjc.status = 1" , "left")
            ->join("prime_employee_bioid as peb" , "peb.empid = emp.sysid && peb.status = 1" , "left")
            ->where(array("ec.type" => 1 , "ec.status" => 1 ))
            ->group_by('emp.empid,
                    emp.sysid,   
                    emp.status,  
                    emp.personid, 
                    emp.datecreated,
                    emp.createdby,
                    ec.ccid,
                    p.firstname,
                    p.lastname,
                    p.middlename,
                    p.gender,
                    p.birthdate,
                    cc.desc,
                    cc.names,
                    cc.codes,
                    empc.payclass_id,
                    peb.bioid,
                    pe.accntno')
            ->order_by('p.lastname',"ASC")
            ->get();

        $list_arr = array();

        foreach ($query->result() as $row) {
            $empid = $row->sysid;
            $payclass = $row->payclassid;
            $emp_trn_arr = array();

            if($view_type==1) {
                $salary = 0;

                //check if there is distributed
                $checkfordistributedamt = $this->db->select("paytype")->from("payroll_transactions")
                    ->where(array("empid" => $empid , "months" => $month , "years" => $year , "paytype" => 0))
                    ->get()->row();
                $popover = ($checkfordistributedamt) ? $checkfordistributedamt->paytype : $paytype;

                //  $compute = compute_employee_netpay_temp($empid, $month, $year, $paytype, $popover, $payclass , $view_type);
                $compute = compute_employee_netpay_temp(551, 7, 2019,1, 0, 128 , 1);


                //if($salary > 0 ) {
                if($compute->transactions) {
                    foreach($compute->transactions as $trn_row) {

                        // CHECK TRANSACTIONS
                        $trn_amt = $trn_row['amt'];
                        $trn_type = $trn_row['type'];


                        $typeis = 0;
                        $typeisname = '';
                        $typeidname = '';
                        $gettype  = $this->db->select("functions , effects , names")
                            ->from("payroll_matrix")
                            ->where(array("typesid" => $trn_type))
                            ->get()->row();
                        if($gettype){
                            $typeidname = $gettype->names;
                            if($gettype->functions == 1 && $gettype->effects == 1){
                                $typeis = 1; // earnings
                                $typeisname = 'earnings';
                            }
                            if($gettype->functions == 0 && $gettype->effects == 0){
                                $typeis = 2;// deductions
                                $typeisname = 'deductions';
                            }
                            if($gettype->functions == 0 && $gettype->effects == 1){
                                $typeis = 3;// loans
                                $typeisname = 'loans';
                            }
                            if($gettype->functions == 1 && $gettype->effects == 0){
                                $typeis = 4;// contributions
                                $typeisname = 'contributions';
                            }
                        }
                        $emp_trn_arr[$empid][] = array(
                            'payrollid' => 0,
                            'trntype' => $trn_type,
                            'amt' => $trn_amt,
                            'payspec' => $paytype,
                            'type' => $typeis,
                            'typename' => $typeisname,
                            'name' => $typeidname,
                            'functions' => $gettype->functions,
                            'effects' => $gettype->effects,
                        );
                    }


                    // }
                }
            }

            $basic = 0;

            $getbasic = $this->db->select("amt")->from("prime_employee_salary")
                ->where(array("status" => 1 , "empid" => 551))
                ->get()->row();

            if($table_view == 1){ // PAYROLL REGISTER

                $earning = 0;
                $deduction = 0;
                $col_n_arr = array();
                $col_arr = array(263, 254,257,259,264,261,253,252,251,265,260,258,262,
                    255,256,266,358, 375,360,74,72,73,75,1010,1079,1082,3006,3007,319,3010,
                    3072);
                if(isset($emp_trn_arr[$empid])) {
                    foreach($col_arr as $crow) {
                        $col_n_arr[$crow] =  '';
                        foreach($emp_trn_arr[$empid] as $trn_row) {
                            if($trn_row['trntype'] == $crow){
                                $col_n_arr[$crow] = $trn_row['trntype'].' '.$trn_row['name'];
                            }
                        }
                    }

                    /*  foreach($emp_trn_arr[$empid] as $trn_row) {
                         if($trn_row['typename'] == 'earnings') {
                             $earning += $trn_row['amt'];
                         }
                         if($trn_row['typename'] == 'deductions' || $trn_row['typename'] == 'loans' || $trn_row['typename'] == 'contributions' ) {
                             $deduction += $trn_row['amt'];
                         }
                         foreach($col_arr as $crow) {
                             //  $col_n_arr[$trn_row['name']] = $trn_row['amt'].' - '.$trn_row['trntype'];
                            if($crow == $trn_row['trntype']) {
                                 // $col_n_arr[strtolower(str_replace(" ","",$trn_row['name']))] =  $crow;
                                 $col_n_arr[$trn_row['name']] = $trn_row['amt'];
                             }else{
                                 $col_n_arr[$trn_row['name']] = $trn_row['amt'];
                             }

                            $col_n_arr[$trn_row['name']] = $crow;
                        }
                    }*/
                }
                if($payclass == 128){
                    $basic =(($getbasic) ? $getbasic->amt / 2 : 0);
                }else{
                    $basic =(($getbasic) ? $getbasic->amt : 0);
                }
                $gross = $earning + $basic;

                $fix_arr = array(
                    'empid' => $empid,
                    'ccid' => $row->ccid,
                    'gross' => $gross,
                    'deductions' => $deduction,
                    'net' => ($gross-$deduction),
                    // 'table' => 'payrollreg'
                );
                $data['addedarr'] = $col_n_arr;
                /*    $list_arr_r = array_merge($fix_arr, $col_n_arr);
                    $list_arr[] = $list_arr_r;
                    $data['emp'][] = $list_arr; */
            }else if($table_view == 2){

                $col_n_arr = array();
                $col_arr = array(251,252,253,263,358,359,1082,3010,360,266);
                if(isset($emp_trn_arr[$empid]) && count($emp_trn_arr[$empid])) {
                    foreach($emp_trn_arr[$empid] as $trn_row) {
                        foreach($col_arr as $crow) {
                            if($crow == $trn_row['trntype']) {
                                $col_n_arr[strtolower(str_replace(" ","",$trn_row['name']))] =  $trn_row['amt'];
                            }
                        }
                    }
                }
                $fix_arr = array(
                    'empid' => $empid,
                    'ccid' => $row->ccid,
                    'basic' => $basic
                );
                $list_arr_r = array_merge($fix_arr, $col_n_arr);
                $list_arr[] = $list_arr_r;
                $data['emp'][] = $list_arr;
            }else if($table_view == 3){
                $col_n_arr = array();
                $col_arr = array(72,257,74,258,254,255,259,261,260,1079,256,262,75);
                if(isset($emp_trn_arr[$empid]) && count($emp_trn_arr[$empid])) {
                    foreach($emp_trn_arr[$empid] as $trn_row) {
                        foreach($col_arr as $crow) {
                            if($crow == $trn_row['trntype']) {
                                $col_n_arr[strtolower(str_replace(" ","",$trn_row['name']))] =  $trn_row['amt'];
                            }
                        }
                    }
                }

                $fix_arr = array(
                    'empid' => $empid,
                    'ccid' => $row->ccid,
                );
                $list_arr_r = array_merge($fix_arr, $col_n_arr);
                $list_arr[] = $list_arr_r;
                $data['emp'][] = $list_arr;
            }else if($table_view == 4){
                $col_n_arr = array();
                $col_arr = array(358);
                if(isset($emp_trn_arr[$empid]) && count($emp_trn_arr[$empid])) {
                    foreach($emp_trn_arr[$empid] as $trn_row) {
                        foreach($col_arr as $crow) {
                            if($crow == $trn_row['trntype']) {
                                $col_n_arr[strtolower(str_replace(" ","",$trn_row['name']))] =  $trn_row['amt'];
                            }
                        }
                    }
                }

                $fix_arr = array(
                    'empid' => $empid,
                    'ccid' => $row->ccid,
                );

                $list_arr_r = array_merge($fix_arr, $col_n_arr);
                $list_arr[] = $list_arr_r;
                $data['emp'][] = $list_arr;
            }

        }

        $cols_arr_ = array();
        if(count($list_arr) > 0) {
            foreach ($list_arr as $darrow) {
                $cols_arr_[] = array_keys($darrow);
            }
        }
        $cols_str = '';
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($cols_arr_));
        foreach($it as $v) {
            $cols_str .= $v . ',';
        }
        $cols_new_arr = explode(',', $cols_str);
        $cols_new_u_arr = array();
        foreach($cols_new_arr as $ddarrow) {
            if(!empty($ddarrow)) {
                $cols_new_u_arr[] = $ddarrow;
            }
        }

        $cols_arr = array_unique($cols_new_u_arr);
        $data['cols'] = $cols_arr;

        $qry_department = $this->db->query("
            SELECT
            pec.ccid, 
            pcm.codes
            FROM
            payroll_transactions AS pt
            INNER JOIN prime_employee_costcenter AS pec ON pec.empid = pt.empid
            INNER JOIN prime_costcenter_main AS pcm ON pec.ccid = pcm.sysid
            WHERE
            pt.`status` = 1 AND
            pt.years = $year AND
            pt.months = $month
            GROUP BY pec.ccid
            ORDER BY pcm.codes
        ");

        if($qry_department->num_rows() > 0) {
            foreach($qry_department->result() as $drow) {
                $dept_arr_key = array();
                if(count($list_arr) > 0) {
                    foreach($cols_arr as $dkkey => $dkrow) {
                        if($dkkey > 1) {
                            $amt = 0;
                            $dkrow_name = $dkrow;
                            foreach($list_arr as $dekrow) {
                                if($dekrow['ccid'] == $drow->ccid) {
                                    if(isset($dekrow[$dkrow_name]) && is_numeric($dekrow[$dkrow_name]) && $dekrow[$dkrow_name]>0) {
                                        $amt += $dekrow[$dkrow_name];
                                    }
                                }
                            }
                            $dept_arr_key[$dkrow_name] = $amt;
                        }
                    }
                    $fix_arr = array(
                        'deptid' => $drow->ccid,
                        'deptcode' => $drow->codes,
                    );
                    $data['dept'][] = array_merge($fix_arr, $dept_arr_key);
                }
            }
        }
        $data['tableview'] = $table_view;
        echo '<pre>';
        print_r($data['addedarr']);
        // return json_encode($data);
    }
    function pdf_annual_payslip($typesid , $year , $month , $paytype , $viewtype){

        $data = array();
        $confi = array();

        $gettrnname = $this->db->select("desc")->from("prime_types_parameter")
            ->where(array("sysid" => $typesid))->get()->row();
        $trnname = ($gettrnname) ? $gettrnname->desc : '';

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
        $html .= '</head>';
        $html .= '<body>';

        if($viewtype > 0){
            if ($viewtype == 1) {
                $confi_ids = $this->db->select('payclass')
                    ->from('prime_employee_main_payclass_grouping')
                    ->where(array('payrollpayclass' => 1, 'status' => 1))
                    ->get();

                if ($confi_ids->num_rows() > 0) {
                    foreach ($confi_ids->result() AS $row) {
                        $confi[] = $row->payclass;
                    }
                }

                $this->db->where_in('pemp.payclass_id',$confi);
            } else {
                $this->db->where(array("pemp.payclass_id" => $viewtype));
            }
        }

        $jobcatarr = array(157 , 160);
        $sql = $this->db->select("pem.sysid , p.lastname , p.firstname, pemp.payclass_id, pe.accntno")
            ->from("prime_employee_main as pem")
            ->join("payroll_emplist as pe" , "pe.empid = pem.sysid")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->join("prime_employee_main_job_category as pemjc" , "pemjc.empid = pem.sysid")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemjc.jobcatid && ptp.codes = 'EMPJOBCAT'")
            ->join("prime_employee_main_payclass as pemp" , "pemp.emp_id = pem.sysid")
            ->join("payroll_manual_earnings as pme" , "pme.empid = pem.sysid")
            ->where(array("pem.status" => 1 , "pe.status" => 1 , "pemjc.status" => 1 , "pme.status" => 305))
            ->where_in('pemjc.jobcatid', $jobcatarr)
            ->order_by("p.lastname")
            ->group_by('pem.sysid , p.lastname , p.firstname, pemp.payclass_id, pe.accntno')
            ->get();
        if($sql->num_rows() > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {
                $form_payslip = form_annual_payslip_single($row->sysid, $month, $year, $paytype, $typesid, false, $num++ , $viewtype);
                if ($form_payslip->res) {
                    $html .= $form_payslip->html;
                }
            }
        }




        $html .= '</body>';
        $html .= '</html>';

        $filename = $year . '-' . strtoupper(date_formating($month, '!m', 'M')) . '_'.$trnname.'.pdf';


        $data['filename'] = $filename;
        $data['html'] = $html;

        return (object)$data;

    }

    function get_emp_list_report(){
        $data = array();
        $jobcat = $this->input->post('jobcat');
        $command = $this->input->post('command');

        $title = 'List of Employees';

        if ($jobcat && $jobcat != ''){
            $jobcat_desc = $this->db->select('names')
                ->from('prime_types_parameter')
                ->where('sysid',$jobcat)
                ->get()->row();

            $title = $jobcat_desc->names.' Employees';

            $this->db->where('pemjc.jobcatid',$jobcat);
        }

        $sql = $this->db->select("p.lastname , p.firstname,p.birthdate,pem.datestart,ptp.names, pcm.codes, p.gender,pc.sss_num , pc.tin_num , pc.pagibig , pc.philhealth,pam.addrspec , peb.bioid , payc.names as payclass , pemjc.jobcatid")
            ->from("prime_employee_main as pem")
            ->join("person as p", "p.sysid = pem.personid")
            ->join("prime_employee_main_positions as pemp", "pemp.emp_id = pem.sysid && pemp.status = 1", "left")
            ->join("prime_types_parameter as ptp", "ptp.sysid = pemp.position_id")
            ->join("prime_employee_costcenter as pec", "pec.empid = pem.sysid && pec.type = 1 && pec.status = 1")
            ->join("prime_costcenter_main as pcm", "pcm.sysid = pec.ccid")
            ->join("person_credentials as pc", "pc.emp_id = p.sysid", "left")
            ->join("person_address_matrix as pam", "pam.personid = p.sysid", "left")
            ->join("prime_employee_bioid as peb", "peb.empid = pem.sysid && peb.status = 1", "left")
            ->join("prime_employee_main_payclass as pempclass", "pempclass.emp_id = pem.sysid", "left")
            ->join("prime_types_parameter as payc", "payc.sysid = pempclass.payclass_id && payc.codes = 'EMPAYCLASS'", "left")
            ->join("prime_employee_main_job_category pemjc", "pemjc.empid = pem.sysid && pemjc.jobcatid != 159")
            ->where(array("pem.status" => 1))
            ->order_by("p.lastname")
            ->get();

        if ($sql->num_rows() > 0) {
            $num = 1;
            $male_reg = 0;
            $male_prob = 0;
            $female_reg = 0;
            $female_prob = 0;
            $exec_cnt = 0;
            $rf_cnt = 0;
            $confi_cnt = 0;
            $sa_cnt = 0;
            $probi = 0;
            $trd_cnt = 0;
            if ($command) {
                $title = 'SUMMARY OF EMPLOYEE';
                foreach ($sql->result() as $row){
                    if ($row->gender == 1 && $row->jobcatid == 157){
                        $male_reg++;
                    }
                    if ($row->gender == 2 && $row->jobcatid == 157){
                        $female_reg++;
                    }
                    if ($row->gender == 1 && $row->jobcatid == 160){
                        $male_prob++;
                    }
                    if ($row->gender == 2 && $row->jobcatid == 160){
                        $female_prob++;
                    }
                    if (strpos($row->payclass,'EXECUTIVE') !==false && $row->jobcatid == 157){
                        $exec_cnt++;
                    }
                    if ($row->payclass == 'RF' && $row->jobcatid == 157){
                        $rf_cnt++;
                    }
                    if ($row->payclass == 'CA' && $row->jobcatid == 157){
                        $confi_cnt++;
                    }
                    if ($row->payclass == 'SA' && $row->jobcatid == 157){
                        $sa_cnt++;
                    }
                    if (($row->payclass == 'T1' || $row->payclass == 'T2') && $row->jobcatid == 157){
                        $trd_cnt++;
                    }
                    if ($row->jobcatid == 160){
                        $probi++;
                    }
                }
                $retired_res = $this->db->select('count(specificdate) AS cnt')
                    ->from('prime_employee_main_history')
                    ->where(array('status' => 1, 'year(specificdate)' => date('Y')))
                    ->get()->row();

                $data['ret_res'] = $this->db->last_query();
                $data['totals'] = array(
                    'mreg' => $male_reg,
                    'mprob' => $male_prob,
                    'freg' => $female_reg,
                    'fprob' => $female_prob,
                    'execcnt' =>  $exec_cnt,
                    'rfcnt' => $rf_cnt,
                    'conficnt' => $confi_cnt,
                    'sacnt' => $sa_cnt,
                    'tieredcnt' => $trd_cnt,
                    'probi' => $probi,
                    'ret_res' => $retired_res->cnt,
                    'total_mf' => array_sum(array($male_reg,$male_prob,$female_reg,$female_prob)),
                    'subt_reg' => array_sum(array($exec_cnt,$rf_cnt,$confi_cnt,$sa_cnt,$trd_cnt)),
                );
            } else {
                foreach ($sql->result() as $row) {

                    $now_year = date("Y");

                    if ($row->datestart != '' && $row->datestart != null) {
                        $dstart = date("Y", strtotime($row->datestart));
                        $yos = $now_year - $dstart;
                    } else {
                        $yos = '';
                    }
                    if ($row->birthdate != '' && $row->birthdate != null) {
                        $bdate = date("Y", strtotime($row->birthdate));
                        $age = $now_year - $bdate;
                    } else {
                        $age = '';
                    }
                    if ($row->gender == 1) {
                        $gender = 'M';
                    } else if ($row->gender == 2) {
                        $gender = 'F';
                    } else {
                        $gender = 'N/A';
                    }

                    $data['emplist'][] = array(
                        'num' => $num++,
                        'name' => ucwords(strtolower($row->lastname)) . ', ' . ucwords(strtolower($row->firstname)),
                        'dob' => date_formating($row->birthdate,'Y-m-d','m/d/Y'),
                        'age' => $age,
                        'dh' => date_formating($row->datestart,'Y-m-d','m/d/Y'),
                        'yos' => $yos,
                        'pos' => upper_ent_quotes($row->names),
                        'cc' => $row->codes,
                        'payclass' => str_replace('null', 'N/A', $row->payclass),
                        'mopp' => '',
                        'mf' => $gender,
                        'idno' => str_replace('null', 'None', $row->bioid),
                        'tin' => str_replace('null', 'None', $row->tin_num),
                        'sss' => str_replace('null', 'None', $row->sss_num),
                        'phil' => str_replace('null', 'None', $row->philhealth),
                        'addr' => str_replace('null', 'None', $row->addrspec),
                        'hdmf' => str_replace('null', 'None', $row->pagibig),
                    );
                }
            }
        }


        $data['printheader'] = peco_print_header(false, $title);
        return json_encode($data);
    }

    function test_excel() {

        //load our new PHPExcel library
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('READING REPORTS');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'CODE');

        $this->excel->getActiveSheet()->setCellValue('A2', 'CODE');

        $filename='EMPLOYEE_PAYROLL_REPORT'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    function search_for_index($id, $array) {
        foreach ($array as $key => $val) {
            if ($val['data'] === $id) {
                return $key;
            }
        }
        return null;
    }

    function get_payroll_reports_bak($month = false, $year = false, $type = false, $payclass = false, $costcenter = false, $report = null) {
        $formpost = $this->input->post('formpost');
        if($formpost == 1) {
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            $type = $this->input->post('reptype');
            $payclass = $this->input->post('payclass');
            $costcenter = $this->input->post('costcenter');
        }

        if($report == 'excel') {
            //load our new PHPExcel library
            $this->load->library('excel');
            //activate worksheet number 1
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('READING REPORTS');
        }

        $cols_def = array(
            array('data' => 'empid','title' => 'EmpID','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('data' => 'ccid','title' => 'CCID','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('data' => 'selectval','title' => 'Payclass','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('data' => 'months','title' => 'Month','sClass' => 'text-danger','sWidth' => '100px', 'format' => 'month'),
            array('data' => 'years','title' => 'Year','sClass' => 'text-info','sWidth' => '100px', 'format' => ''),
            array('data' => 'cnt','title' => 'Count','sClass' => 'number','sWidth' => '', 'format' => 'number'),
            array('data' => 'basic','title' => 'Basic','sClass' => 'text-primary','sWidth' => '100px', 'format' => 'number'),
            array('data' => 'earnings','title' => 'Earnings','sClass' => 'text-primary','sWidth' => '100px', 'format' => 'number'),
            array('data' => 'deductions','title' => 'Deductions','sClass' => 'text-danger','sWidth' => '100px', 'format' => 'number'),
            array('data' => 'tax','title' => 'TAX','sClass' => 'text-danger','sWidth' => '100px', 'format' => 'number'),
            array('data' => 'net','title' => 'NET','sClass' => 'text-primary bold','sWidth' => '100px', 'format' => 'number'),
        );


        // PER EMPLOYEE QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if($type == 3225) {


            if(($month && $year) && ($month > 0 && $year > 0)) {


                if($report == 'excel') {
                    //set cell A1 content with some text
                    $this->excel->getActiveSheet()->setCellValue('A1', 'EMPID');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'NAME');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTIONS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>EMPID</th>';
                $dt_head .= '<th>NAME</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';
                $dt_head .= '<th>STATUS</th>';

                $dt_columns = array(
                    array(
                        'title' => 'EMPID', 'data' => 'empid', 'sClass' => ''
                    ), array(
                        'title' => 'NAME', 'data' => 'name', 'sClass' => '', 'sWidth' => '250px'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ), array(
                        'title' => 'STATUS', 'data' => 'status', 'sClass' => 'text-align-center'
                    ),
                );

                $qry_list = $this->db->query("
                    SELECT
                        em.sysid,
                        em.empid,
                        p.firstname,
                        p.lastname,
                        prm.ccid,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM
                        prime_employee_main AS em
                    INNER JOIN `payroll_reports_main` AS prm ON prm.empid = em.sysid
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    INNER JOIN person AS p ON em.personid = p.sysid
                    WHERE prg.years = $year AND prg.months = $month AND prg.`status` = 301
                    GROUP BY em.sysid, em.empid, p.firstname, p.lastname, prm.ccid
                    ORDER BY p.lastname
                ");
                if($qry_list->num_rows() > 0) {
                    $excel_num = 0;
                    foreach($qry_list->result() as $row) {
                        $name = $row->lastname . ', ' . $row->firstname;
                        $info = get_employee_info($row->sysid);
                        $datestart = ($info->qry) ? $info->datestart : '';
                        $dateend = ($info->qry) ? $info->dateend : '';
                        $status = ($info->qry) ? $info->empstatus : '';
                        $empid = $row->empid;
                        if ($report == 'excel') {
                            $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->sysid);
                            $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $name);
                            $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                            $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                            $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                            $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                            $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);
                        } else {

                            if ($status == 1) {
                                $status_ = '<span class="label label-success">Active</span>';
                            } else {
                                if (validateDate($dateend)) {
                                    $status_ = '<span class="label label-danger">Date End: ' . $dateend . '</span>';
                                } else {
                                    $status_ = '<span class="label label-danger">Resigned</span>';
                                }
                            }

                            $empid_ = ($empid != '') ? $empid : '<code>None</code>';

                            $data['list'][] = array(
                                'empid' => $empid_,
                                'name' => '<span class="label label-info" style="display: inline-block; width: 40px;">' . $row->sysid . '</span> ' . $name,
                                'basic' => number_format($row->basic, 2),
                                'earnings' => number_format($row->earnings, 2),
                                'deductions' => number_format($row->deductions, 2),
                                'tax' => number_format($row->tax, 2),
                                'net' => number_format($row->net, 2),
                                'started' => $datestart,
                                'ended' => $dateend,
                                'status' => $status_,
                            );
                        }
                        $excel_num++;
                    }
                }
            }else {

                if($year && $year > 0) {

                    if($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A1', 'EMPID');
                        $this->excel->getActiveSheet()->setCellValue('B1', 'NAME');
                        $this->excel->getActiveSheet()->setCellValue('C1', 'MONTH');
                        $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                        $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                        $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTIONS');
                        $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                        $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                    }

                    $dt_head = '';
                    $dt_head .= '<th>EMPID</th>';
                    $dt_head .= '<th>NAME</th>';
                    $dt_head .= '<th>MONTH</th>';
                    $dt_head .= '<th>BASIC</th>';
                    $dt_head .= '<th>EARNINGS</th>';
                    $dt_head .= '<th>DEDUCTION</th>';
                    $dt_head .= '<th>TAX</th>';
                    $dt_head .= '<th>NET</th>';
                    $dt_head .= '<th>STATUS</th>';

                    $dt_columns = array(
                        array(
                            'title' => 'EMPID', 'data' => 'empid', 'sClass' => ''
                        ),array(
                            'title' => 'NAME', 'data' => 'name', 'sClass' => '', 'sWidth' => '250px'
                        ),array(
                            'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                        ),array(
                            'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                        ),array(
                            'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                        ),array(
                            'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                        ),array(
                            'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                        ),array(
                            'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                        ),array(
                            'title' => 'STATUS', 'data' => 'status', 'sClass' => 'text-align-center'
                        ),
                    );

                    $qry_list = $this->db->query("
                        SELECT
                            em.sysid,
                            em.empid,
                            p.firstname,
                            p.lastname,
                            prm.ccid,
                            prg.months,
                            SUM(prm.basic) as basic,
                            SUM(prm.earnings) AS earnings,
                            SUM(prm.deductions) AS deductions,
                            SUM(prm.tax) AS tax,
                            SUM(prm.net) AS net
                        FROM
                            prime_employee_main AS em
                        INNER JOIN `payroll_reports_main` AS prm ON prm.empid = em.sysid
                        INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                        INNER JOIN person AS p ON em.personid = p.sysid
                        WHERE prg.years = $year AND prg.`status` = 301
                        GROUP BY prg.months, em.sysid, em.empid, em.sysid, p.firstname, p.lastname, prm.ccid
                        ORDER BY p.lastname ASC, prg.months DESC
                    ");
                    if ($qry_list->num_rows() > 0) {
                        $excel_num = 2;
                        foreach($qry_list->result() as $row) {
                            $name = $row->lastname . ', ' . $row->firstname;
                            $info = get_employee_info($row->sysid);
                            $datestart = ($info->qry) ? $info->datestart : '';
                            $dateend = ($info->qry) ? $info->dateend : '';
                            $status = ($info->qry) ? $info->empstatus : '';
                            $empid = $row->empid;
                            if ($report == 'excel') {
                                $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->sysid);
                                $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $name);
                                $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->months);
                                $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                                $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                                $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                                $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                                $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                            } else {

                                if ($status == 1) {
                                    $status_ = '<span class="label label-success">Active</span>';
                                } else {
                                    if (validateDate($dateend)) {
                                        $status_ = '<span class="label label-danger">Date End: ' . $dateend . '</span>';
                                    } else {
                                        $status_ = '<span class="label label-danger">Resigned</span>';
                                    }
                                }

                                $empid_ = ($empid != '') ? $empid : '<code>None</code>';

                                $data['list'][] = array(
                                    'empid' => $empid_,
                                    'name' => '<span class="label label-info" style="display: inline-block; width: 40px;">' . $row->sysid . '</span> ' . $name,
                                    'month' => date_formating($row->months, '!m', 'F'),
                                    'basic' => number_format($row->basic, 2),
                                    'earnings' => number_format($row->earnings, 2),
                                    'deductions' => number_format($row->deductions, 2),
                                    'tax' => number_format($row->tax, 2),
                                    'net' => number_format($row->net, 2),
                                    'started' => $datestart,
                                    'ended' => $dateend,
                                    'status' => $status_,
                                );
                            }
                            $excel_num++;
                        }
                    }
                }
            }
        }
        // ###################################################################
        // END OF EMPLOYEE QUERY
        // ###################################################################


        // PER COSTCENTER QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if($type == 3226) {
            $dt_head = '';
            if(($year && $month) && ($year > 0 && $month > 0)) {
                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'CCID');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'NAME');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTIONS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'NET');
                }
                $dt_head .= '<th>CCID</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'CCID', 'data' => 'ccid', 'sClass' => 'bold'
                    ),array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ),array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ),array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ),array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ),array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );
                $qry_list = $this->db->query("
                    SELECT
                    SUM(prm.basic) as basic,
                    SUM(prm.earnings) AS earnings,
                    SUM(prm.deductions) AS deductions,
                    SUM(prm.tax) AS tax,
                    SUM(prm.net) AS net, 
                    prm.ccid
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    WHERE prg.years = $year AND prg.months = $month AND prg.`status` = 301
                    GROUP BY prm.ccid
                    ORDER BY prm.ccid
                    ");
                if ($qry_list->num_rows() > 0) {

                    $excel_num = 2;
                    foreach ($qry_list->result() as $row) {
                        $ccname = get_costcenter_name($row->ccid);
                        if ($report == 'excel') {
                            $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->ccid);
                            $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $ccname);
                            $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                            $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                            $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                            $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                            $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);
                        } else {
                            $data['list'][] = array(
                                'ccid' => '<span class="label label-info" style="min-width: 30px; display: inline-block;">'.$row->ccid.'</span> ' . $ccname,
                                'basic' => number_format($row->basic,2),
                                'earnings' => number_format($row->earnings, 2),
                                'deductions' => number_format($row->deductions,2),
                                'tax' => number_format($row->tax,2),
                                'net' => number_format($row->net,2),
                            );
                        }
                        $excel_num++;
                    }
                }
            }else {
                if($year && $year > 0) {

                    if($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A1', 'CCID');
                        $this->excel->getActiveSheet()->setCellValue('B1', 'NAME');
                        $this->excel->getActiveSheet()->setCellValue('C1', 'MONTH');
                        $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                        $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                        $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTIONS');
                        $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                        $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                    }

                    $dt_head .= '<th>CCID</th>';
                    $dt_head .= '<th>MONTH</th>';
                    $dt_head .= '<th>BASIC</th>';
                    $dt_head .= '<th>EARNINGS</th>';
                    $dt_head .= '<th>DEDUCTION</th>';
                    $dt_head .= '<th>TAX</th>';
                    $dt_head .= '<th>NET</th>';

                    $dt_columns = array(
                        array(
                            'title' => 'CCID', 'data' => 'ccid', 'sClass' => 'bold'
                        ),array(
                            'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                        ),array(
                            'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                        ),array(
                            'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                        ),array(
                            'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                        ),array(
                            'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                        ),array(
                            'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                        ),
                    );
                    $qry_list = $this->db->query("
                            SELECT
                            SUM(prm.basic) as basic,
                            SUM(prm.earnings) AS earnings,
                            SUM(prm.deductions) AS deductions,
                            SUM(prm.tax) AS tax,
                            SUM(prm.net) AS net, 
                            prg.months,
                            prm.ccid
                            FROM payroll_reports_main AS prm
                            INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                            WHERE prg.years = $year AND prg.`status` = 301
                            GROUP BY prm.ccid, prg.months
                            ORDER BY prm.ccid, prg.months DESC
                    ");
                    if ($qry_list->num_rows() > 0) {
                        $excel_num = 2;
                        foreach ($qry_list->result() as $row) {
                            $ccname = get_costcenter_name($row->ccid);

                            if ($report == 'excel') {
                                $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->ccid);
                                $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $ccname);
                                $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->months);
                                $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                                $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                                $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                                $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                                $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                            } else {
                                $data['list'][] = array(
                                    'ccid' => '<span class="label label-info" style="min-width: 30px; display: inline-block;">'.$row->ccid.'</span> ' . $ccname,
                                    'month' => date_formating($row->months,'!m','F'),
                                    'basic' => number_format($row->basic,2),
                                    'earnings' => number_format($row->earnings, 2),
                                    'deductions' => number_format($row->deductions,2),
                                    'tax' => number_format($row->tax,2),
                                    'net' => number_format($row->net,2),
                                );
                            }
                            $excel_num++;
                        }
                    }
                }
            }
        }
        // ###################################################################
        // END OF COSTCENTER QUERY
        // ###################################################################


        // PER MONTH QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if($type == 3227) {
            $dt_head = '';
            $dt_head .= '<th>BASIC</th>';
            $dt_head .= '<th>EARNINGS</th>';
            $dt_head .= '<th>DEDUCTION</th>';
            $dt_head .= '<th>TAX</th>';
            $dt_head .= '<th>NET</th>';

            $dt_columns = array(
                array(
                    'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                ), array(
                    'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number'
                ), array(
                    'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number'
                ), array(
                    'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                ), array(
                    'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                ),
            );

            if($year && $month) {
                $where = '';
                if( ($costcenter && $costcenter > 0) && $payclass && $payclass > 0) {
                    if($payclass == 1) {
                        // GET IDS OF CONFI AN CA
                        $where .= ' AND prm.ccid = ' . $costcenter . ' AND prg.payclass = 1';
                    } else {
                        // GET IDS OF RF TIERED
                        $payclass_id = array();
                        $payclassqry = $this->db->select('typesid')
                            ->from('prime_employee_payclass_ref_matrix')
                            ->where('selectval',2)->get();
                        if($payclassqry){
                            foreach ($payclassqry->result() AS $row){
                                $payclass_id[] = $row->typesid;
                            }
                        }
                        $payclass_in_id = implode(', ', $payclass_id);
                        $where .= ' AND prm.ccid = ' . $costcenter . ' AND prg.payclass IN (' . $payclass_in_id . ')';
                    }
                } else {
                    if(($costcenter && $costcenter > 0)) {
                        $where .= ' AND prm.ccid = ' . $costcenter;
                    } else {

                        if($payclass == 1) {
                            // GET IDS OF CONFI AN CA
                            $where .= ' AND prg.payclass = 1';
                        } else {
                            // GET IDS OF RF TIERED
                            $payclass_id = array();
                            $payclassqry = $this->db->select('typesid')
                                ->from('prime_employee_payclass_ref_matrix')
                                ->where('selectval',2)->get();
                            if($payclassqry){
                                foreach ($payclassqry->result() AS $row){
                                    $payclass_id[] = $row->typesid;
                                }
                            }
                            $payclass_in_id = implode(', ', $payclass_id);
                            $where .= ' AND prg.payclass IN (' . $payclass_in_id . ')';
                        }
                    }
                }

                $qry_list = $this->db->query("
                    SELECT
                            prg.months,
                            SUM(prm.basic) as basic,
                            SUM(prm.earnings) AS earnings,
                            SUM(prm.deductions) AS deductions,
                            SUM(prm.tax) AS tax,
                            SUM(prm.net) AS net
                    FROM `payroll_reports_main` AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    WHERE prg.years = $year AND prg.months = $month AND prg.`status` = 301 
                    $where
                    GROUP BY prg.months
                    ORDER BY prg.months
                ");
                if($qry_list->num_rows() > 0) {
                    $excel_num = 2;
                    foreach($qry_list->result() as $row) {
                        if ($report == 'excel') {
                            $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->basic);
                            $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->earnings);
                            $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->deductions);
                            $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->tax);
                            $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->net);
                        } else {
                            $data['list'][] = array(
                                'basic' => number_format($row->basic,2),
                                'earnings' => number_format($row->earnings, 2),
                                'deductions' => number_format($row->deductions,2),
                                'tax' => number_format($row->tax,2),
                                'net' => number_format($row->net,2),
                            );
                        }
                        $excel_num++;
                    }
                }
            } else {
                if($year) {
                    $dt_head = '';
                    $dt_head .= '<th>MONTH</th>';
                    $dt_head .= '<th>BASIC</th>';
                    $dt_head .= '<th>EARNINGS</th>';
                    $dt_head .= '<th>DEDUCTION</th>';
                    $dt_head .= '<th>TAX</th>';
                    $dt_head .= '<th>NET</th>';

                    $dt_columns = array(
                        array(
                            'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                        ), array(
                            'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                        ), array(
                            'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number'
                        ), array(
                            'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number'
                        ), array(
                            'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                        ), array(
                            'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                        ),
                    );
                    $where = '';
                    if( ($costcenter && $costcenter > 0) && $payclass && $payclass > 0) {
                        if($payclass == 1) {
                            // GET IDS OF CONFI AN CA
                            $where .= ' AND prm.ccid = ' . $costcenter . ' AND prg.payclass = 1';
                        } else {
                            // GET IDS OF RF TIERED
                            $payclass_id = array();
                            $payclassqry = $this->db->select('typesid')
                                ->from('prime_employee_payclass_ref_matrix')
                                ->where('selectval',2)->get();
                            if($payclassqry){
                                foreach ($payclassqry->result() AS $row){
                                    $payclass_id[] = $row->typesid;
                                }
                            }
                            $payclass_in_id = implode(', ', $payclass_id);
                            $where .= ' AND prm.ccid = ' . $costcenter . ' AND prg.payclass IN (' . $payclass_in_id . ')';
                        }
                    } else {
                        if(($costcenter && $costcenter > 0)) {
                            $where .= ' AND prm.ccid = ' . $costcenter;
                        } else {

                            if($payclass == 1) {
                                // GET IDS OF CONFI AN CA
                                $where .= ' AND prg.payclass = 1';
                            } else {
                                // GET IDS OF RF TIERED
                                $payclass_id = array();
                                $payclassqry = $this->db->select('typesid')
                                    ->from('prime_employee_payclass_ref_matrix')
                                    ->where('selectval',2)->get();
                                if($payclassqry){
                                    foreach ($payclassqry->result() AS $row){
                                        $payclass_id[] = $row->typesid;
                                    }
                                }
                                $payclass_in_id = implode(', ', $payclass_id);
                                $where .= ' AND prg.payclass IN (' . $payclass_in_id . ')';
                            }
                        }
                    }

                    $qry_list = $this->db->query("
                        SELECT
                                prg.months,
                                COUNT(distinct (prm.empid)) AS cnt,
                                SUM(prm.basic) as basic,
                                SUM(prm.earnings) AS earnings,
                                SUM(prm.deductions) AS deductions,
                                SUM(prm.tax) AS tax,
                                SUM(prm.net) AS net
                        FROM `payroll_reports_main` AS prm
                        INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                        WHERE prg.years = $year AND prg.`status` = 301 
                        $where
                        GROUP BY prg.months
                        ORDER BY prg.months
                    ");
                    $excel_num = 2;
                    foreach($qry_list->result() as $row) {
                        if ($report == 'excel') {
                            $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->months);
                            $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->basic);
                            $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->earnings);
                            $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->deductions);
                            $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->tax);
                            $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->net);
                        } else {
                            $data['list'][] = array(
                                'month' => $row->months.' - '.date_formating($row->months,'!m','F'),
                                'cnt' => $row->cnt,
                                'basic' => number_format($row->basic,2),
                                'earnings' => number_format($row->earnings, 2),
                                'deductions' => number_format($row->deductions,2),
                                'tax' => number_format($row->tax,2),
                                'net' => number_format($row->net,2),
                            );
                        }
                        $excel_num++;
                    }
                }
            }
        }
        // ###################################################################
        // END OF MONTH QUERY
        // ###################################################################

        // PER PAYCLASS QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if ($type == 3228){

            $year_col = 'prg.years, ';
            $month_col = 'prg.months, ';
            $ccid_col = 'prm.ccid, ';
            $selectval_col = 'eprm.selectval, ';

            $where_year = '';
            $where_month = '';
            $where_ccid = '';
            $where_payclass = '';

            $year_ = 'prg.years, ';
            $month_ = 'prg.months, ';
            $ccid_ = 'prm.ccid, ';
            $selectval_ = 'eprm.selectval, ';

            if ($year || $year != 0) {
                $where_year = 'AND prg.years = '.$year;
                $year_col = '';
                $year_ = '';
            }

            if ($month || $month != 0) {
                $where_month = 'AND prg.months = '.$month;
                $month_col = '';
                $month_ = '';
            }

            if ($costcenter || $costcenter != 0) {
                $where_ccid = 'AND prm.ccid = '.$costcenter;
                $ccid_col = '';
                $ccid_ = '';
            }

            if ($payclass || $payclass != 0) {
                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where_payclass = ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where_payclass = ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }
                $selectval_col = '';
                $selectval_ = '';
            }

            $qry_list = $this->db->query("
                    SELECT
                        $year_col
                        $month_col
                        $ccid_col
                        $selectval_col
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE prg.status = 301
                        $where_year
                        $where_month
                        $where_ccid
                        $where_payclass
                    GROUP BY 
                        $year_
                        $month_
                        $ccid_
                        $selectval_
                        ''
                    ORDER BY prm.ccid, prg.months DESC
                ");

            if ($qry_list){
                $cols = $qry_list->list_fields();
                $columns = array();
                $let = 'A';
                foreach ($cols as $col_row) {
                    $col_index = $this->search_for_index($col_row, $cols_def);

                    $title = $cols_def[$col_index]['title'];
                    if($col_index != null) {
                        $columns[] = $cols_def[$col_index];
                    }
                    if($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue($let.'1', $title);
                        $let++;
                    }
                }

                $excel_num = 2;
                $data['cols'] = $cols;
                foreach ($qry_list->result() AS $index => $row){
                    $value = array();
                    $let = 'A';
                    foreach($cols as $field) {
                        $col_index_ = $this->search_for_index($field, $cols_def);
                        $format = '';
                        if($col_index_ != null) {
                            $format = $cols_def[$col_index_]['format'];
                        }

                        if($format == 'number') {
                            $value[] = number_format($row->$field,2);
                        } else {
                            if ($format == 'month') {
                                $value[] = date_formating($row->$field, '!m', 'F');
                            }else{
                                if ($field == 'selectval'){
                                    if ($row->$field == 2){
                                        $value[] = 'RF/Tiered';
                                    }else{
                                        $value[] = 'Confi/SA';
                                    }
                                }else{
                                    $value[] = $row->$field;
                                }
                            }
                        }
                        if($report == 'excel') {
                            $this->excel->getActiveSheet()->setCellValue($let.$excel_num, $row->$field);
                            $let++;
                        }
                    }
                    $data['list'][] = array_combine($cols, $value);


                    $excel_num++;
                }
            }
            if($report == 'excel') {
                $as_off = date('Y-m-d');
                $filename='EMPLOYEE_PAYROLL_REPORT_ASOF_'.$as_off; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache

                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');
            }else {
                $data['th'] = $dt_head;
                $data['cols'] = $dt_columns;
                return json_encode($data);
            }

            exit();
            // Year(True) & Month(True) & CC(True) & PC(True)
            if ($year && $month && $costcenter && $payclass){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $where = '';

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where .= ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where .= ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    WHERE 
                        prg.years = $year AND
                        prg.months = $month AND
                        prm.ccid = $costcenter
                        $where
                    AND prg.`status` = 301
                    GROUP BY prm.ccid, prg.months
                    ORDER BY prm.ccid, prg.months DESC
                ");

                $excel_num = 2;
                foreach($qry_list->result() as $row) {
                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }

            }

            // Year(True) & Month(True) & CC(False) & PC(False)
            if ($year && $month && $costcenter == false && $payclass == false){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $qry_list = $this->db->query("
                        SELECT
                                COUNT(distinct (prm.empid)) AS cnt,
                                SUM(prm.basic) as basic,
                                SUM(prm.earnings) AS earnings,
                                SUM(prm.deductions) AS deductions,
                                SUM(prm.tax) AS tax,
                                SUM(prm.net) AS net,
                                eprm.selectval
                        FROM payroll_reports_main AS prm
                        INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                        LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                        WHERE 
                                prg.years = $year AND
                                prg.months = $month
                                AND prg.`status` = 301
                        GROUP BY eprm.selectval
                        ORDER BY prg.months DESC
                    ");

                $excel_num = 2;
                foreach($qry_list->result() as $row) {
                    $payclass_name = '';

                    if($row->selectval == 2){
                        $payclass_name = 'RF/Tiered';
                    }else{
                        $payclass_name = 'Confi/SA';
                    }

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $payclass_name);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'payclass' => $payclass_name,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(True) & Month(True) & CC(True) & PC(False)
            if ($year && $month && $costcenter && $payclass == false){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $qry_list = $this->db->query("
                            SELECT
                                COUNT(distinct (prm.empid)) AS cnt,
                                SUM(prm.basic) as basic,
                                SUM(prm.earnings) AS earnings,
                                SUM(prm.deductions) AS deductions,
                                SUM(prm.tax) AS tax,
                                SUM(prm.net) AS net,
                                eprm.selectval
                            FROM payroll_reports_main AS prm
                            INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                            LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                            WHERE 
                                prg.years = $year AND
                                prg.months = $month
                                AND prg.`status` = 301
                                AND prm.ccid = $costcenter
                            GROUP BY eprm.selectval
                            ORDER BY prg.months DESC
                        ");
                $excel_num = 2;
                foreach($qry_list->result() as $row) {
                    $payclass_name = '';

                    if($row->selectval == 2){
                        $payclass_name = 'RF/Tiered';
                    }else{
                        $payclass_name = 'Confi/SA';
                    }

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $payclass_name);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'payclass' => $payclass_name,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(True) & Month(False) & CC(True) & PC(False)
            if ($year && $month == false && $costcenter && $payclass == false){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'MONTH');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>MONTH</th>';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                    ), array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $qry_list = $this->db->query("
                                SELECT
                                        COUNT(distinct (prm.empid)) AS cnt,
                                        SUM(prm.basic) as basic,
                                        SUM(prm.earnings) AS earnings,
                                        SUM(prm.deductions) AS deductions,
                                        SUM(prm.tax) AS tax,
                                        SUM(prm.net) AS net,
                                        eprm.selectval,
                                        prg.months
                                FROM payroll_reports_main AS prm
                                INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                                LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                                WHERE 
                                    prg.years = $year AND
                                    prg.`status` = 301 AND
                                    prm.ccid = $costcenter
                                GROUP BY eprm.selectval, prg.months, eprm.selectval
                                ORDER BY prg.months DESC 
                            ");

                $excel_num = 2;
                foreach($qry_list->result() as $row) {
                    $payclass_name = '';

                    if($row->selectval == 2){
                        $payclass_name = 'RF/Tiered';
                    }else{
                        $payclass_name = 'Confi/SA';
                    }

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $payclass_name);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, date_formating($row->months,'!m','F'));
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'payclass' => $payclass_name,
                            'month' => $row->months.' - '.date_formating($row->months,'!m','F'),
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(True) & Month(False) & CC(False) & PC(False)
            if ($year && $month == False && $costcenter == False && $payclass == False){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'MONTH');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>MONTH</th>';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                    ), array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $qry_list = $this->db->query("
                            SELECT
                                COUNT(distinct (prm.empid)) AS cnt,
                                SUM(prm.basic) as basic,
                                SUM(prm.earnings) AS earnings,
                                SUM(prm.deductions) AS deductions,
                                SUM(prm.tax) AS tax,
                                SUM(prm.net) AS net,
                                eprm.selectval,
                                prg.months
                            FROM payroll_reports_main AS prm
                            INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                            LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                            WHERE 
                                prg.years = $year AND
                                prg.`status` = 301
                            GROUP BY eprm.selectval, prg.months, eprm.selectval
                            ORDER BY prg.months DESC
                        ");

                $excel_num = 2;

                foreach ($qry_list->result() as $row) {
                    $payclass_name = '';

                    if ($row->selectval == 2) {
                        $payclass_name = 'RF/Tiered';
                    } else {
                        $payclass_name = 'Confi/SA';
                    }

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'month' => $row->months.' - '.date_formating($row->months, '!m', 'F'),
                            'payclass' => $payclass_name,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic, 2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions, 2),
                            'tax' => number_format($row->tax, 2),
                            'net' => number_format($row->net, 2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(True) & Month(False) & CC(False) & PC(True)
            if ($year && $month == False && $costcenter == False && $payclass){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'MONTH');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>MONTH</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where = ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where = ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net,
                        prg.months
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.years = $year AND
                        prg.`status` = 301
                        $where
                    GROUP BY prg.months, eprm.selectval
                    ORDER BY prg.months DESC
                ");

                $excel_num = 2;

                foreach ($qry_list->result() as $row) {

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, date_formating($row->months, '!m', 'F'));
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'month' => $row->months.' - '.date_formating($row->months, '!m', 'F'),
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic, 2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions, 2),
                            'tax' => number_format($row->tax, 2),
                            'net' => number_format($row->net, 2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(False) & Month(True) & CC(True) & PC(False)
            if ($year == False && $month && $costcenter && $payclass == False){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $qry_list = $this->db->query("
                        SELECT
                            prg.years,
                            COUNT(distinct (prm.empid)) AS cnt,
                            SUM(prm.basic) as basic,
                            SUM(prm.earnings) AS earnings,
                            SUM(prm.deductions) AS deductions,
                            SUM(prm.tax) AS tax,
                            SUM(prm.net) AS net,
                            eprm.selectval
                        FROM payroll_reports_main AS prm
                        INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                        LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                        WHERE 
                            prg.months = $month
                            AND prg.`status` = 301
                            AND prm.ccid = $costcenter
                    GROUP BY eprm.selectval, prg.years
                    ORDER BY prg.months DESC
                    ");
                $excel_num = 2;
                foreach($qry_list->result() as $row) {
                    $payclass_name = '';

                    if($row->selectval == 2){
                        $payclass_name = 'RF/Tiered';
                    }else{
                        $payclass_name = 'Confi/SA';
                    }

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $payclass_name);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'payclass' => $payclass_name,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }

            }

            // Year(False) & Month(True) & CC(False) & PC(True)
            if ($year == False && $month && $costcenter == False && $payclass){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $where = '';

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where .= ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where .= ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        prg.years,
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.months = $month
                        AND prg.`status` = 301
                        $where
                    GROUP BY eprm.selectval, prg.years
                    ORDER BY prg.months DESC
                ");

                $excel_num = 2;
                foreach($qry_list->result() as $row) {

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }

            }

            // Year(False) & Month(False) & CC(True) & PC(True)
            if ($year == False && $month == False && $costcenter && $payclass){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'MONTH');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>MONTH</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'MONTH', 'data' => 'month', 'sClass' => 'number'
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $where = '';

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where .= ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where .= ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        prg.years,
                        prg.months,
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.`status` = 301
                        AND prm.ccid = $costcenter
                        $where
                    GROUP BY eprm.selectval, 
                        prg.years,
                        prg.months
                    ORDER BY prg.months DESC
                ");

                $excel_num = 2;
                foreach($qry_list->result() as $row) {

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->months);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'month' => date_formating($row->months,'!m','F'),
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(False) & Month(True) & CC(False) & PC(False)
            if ($year == False && $month && $costcenter == False && $payclass == False){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $qry_list = $this->db->query("
                    SELECT
                        prg.years,
                        eprm.selectval,
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.`status` = 301
                        AND prg.months = $month
                    GROUP BY eprm.selectval, 
                        prg.years,
                        prg.months
                    ORDER BY prg.months DESC
                ");

                $excel_num = 2;
                if ($qry_list) {
                    foreach ($qry_list->result() as $row) {

                        $payclass_name = '';

                        if ($row->selectval == 2) {
                            $payclass_name = 'RF/Tiered';
                        } else {
                            $payclass_name = 'Confi/SA';
                        }
                        if ($report == 'excel') {
                            $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                            $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $payclass_name);
                            $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->cnt);
                            $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                            $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                            $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                            $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                            $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                        } else {
                            $data['list'][] = array(
                                'year' => $row->years,
                                'payclass' => $payclass_name,
                                'cnt' => $row->cnt,
                                'basic' => number_format($row->basic, 2),
                                'earnings' => number_format($row->earnings, 2),
                                'deductions' => number_format($row->deductions, 2),
                                'tax' => number_format($row->tax, 2),
                                'net' => number_format($row->net, 2),
                            );
                        }
                        $excel_num++;
                    }
                }
            }

            // Year(False) & Month(False) & CC(True) & PC(False)
            if ($year == False && $month == False  && $costcenter && $payclass == False) {

                if ($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );
                $qry_list = $this->db->query("
                    SELECT
                        prg.years,
                        eprm.selectval,
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.`status` = 301
                        AND prm.ccid = $costcenter
                    GROUP BY eprm.selectval, 
                        prg.years
                    ORDER BY eprm.selectval
                ");

                $excel_num = 2;

                foreach ($qry_list->result() as $row) {

                    $payclass_name = '';

                    if ($row->selectval == 2) {
                        $payclass_name = 'RF/Tiered';
                    } else {
                        $payclass_name = 'Confi/SA';
                    }
                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $payclass_name);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'payclass' => $payclass_name,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic, 2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions, 2),
                            'tax' => number_format($row->tax, 2),
                            'net' => number_format($row->net, 2),
                        );
                    }
                    $excel_num++;
                }

            }

            // Year(False) & Month(False) & CC(False) & PC(True)
            if ($year == False && $month == False  && $costcenter == False && $payclass) {

                if ($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('A1', 'MONTH');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>MONTH</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $where = '';

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where .= ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where .= ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        prg.years,
                        prg.months,
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.`status` = 301
                        $where
                    GROUP BY eprm.selectval, 
                        prg.years,
                        prg.months
                    ORDER BY eprm.selectval, prg.months DESC
                ");

                $excel_num = 2;

                foreach ($qry_list->result() as $row) {
                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, date_formating($row->months,'!m','F'));
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'month' => date_formating($row->months,'!m','F'),
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic, 2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions, 2),
                            'tax' => number_format($row->tax, 2),
                            'net' => number_format($row->net, 2),
                        );
                    }
                    $excel_num++;
                }

            }

            // Year(True) & Month(True) & CC(False) & PC(True)
            if ($year && $month && $costcenter == false && $payclass){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                $where = '';

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where .= ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where .= ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                            SELECT
                                prg.years,
                                COUNT(distinct (prm.empid)) AS cnt,
                                SUM(prm.basic) as basic,
                                SUM(prm.earnings) AS earnings,
                                SUM(prm.deductions) AS deductions,
                                SUM(prm.tax) AS tax,
                                SUM(prm.net) AS net
                            FROM payroll_reports_main AS prm
                            INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                            LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                            WHERE 
                                prg.`status` = 301
                                AND prg.years = $year 
                                AND prg.months = $month
                                $where
                            GROUP BY eprm.selectval
                            ORDER BY prg.months DESC
                        ");
                $excel_num = 2;
                foreach($qry_list->result() as $row) {

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);

                    } else {

                        $data['list'][] = array(
                            'year' => $row->years,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic,2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions,2),
                            'tax' => number_format($row->tax,2),
                            'net' => number_format($row->net,2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(True) & Month(False) & CC(True) & PC(True)
            if ($year && $month == False && $costcenter && $payclass){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'MONTH');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>MONTH</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where = ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where = ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net,
                        prg.years,
                        prg.months
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.years = $year AND
						prm.ccid = $costcenter AND
                        prg.`status` = 301
                        $where
                    GROUP BY eprm.selectval, prm.ccid, prg.months
                    ORDER BY prg.months DESC
                ");

                $excel_num = 2;

                foreach ($qry_list->result() as $row) {

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->months);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'month' => date_formating($row->months,'!m','F'),
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic, 2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions, 2),
                            'tax' => number_format($row->tax, 2),
                            'net' => number_format($row->net, 2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(False) & Month(True) & CC(True) & PC(True)
            if ($year == False && $month && $costcenter && $payclass){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where = ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where = ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net,
                        prg.years
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE 
                        prg.months = $month AND
						prm.ccid = $costcenter AND
                        prg.`status` = 301
                        $where
                    GROUP BY eprm.selectval, prm.ccid, prg.years
                    ORDER BY prm.ccid DESC
                ");

                $excel_num = 2;

                foreach ($qry_list->result() as $row) {

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic, 2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions, 2),
                            'tax' => number_format($row->tax, 2),
                            'net' => number_format($row->net, 2),
                        );
                    }
                    $excel_num++;
                }
            }

            // Year(False) & Month(True) & CC(True) & PC(True)
            if ($year == False && $month == False && $costcenter == False && $payclass == False){

                if($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue('A1', 'YEAR');
                    $this->excel->getActiveSheet()->setCellValue('B1', 'MONTH');
                    $this->excel->getActiveSheet()->setCellValue('C1', 'PAYCLASS');
                    $this->excel->getActiveSheet()->setCellValue('D1', 'EMP.COUNT');
                    $this->excel->getActiveSheet()->setCellValue('E1', 'BASIC');
                    $this->excel->getActiveSheet()->setCellValue('F1', 'EARNINGS');
                    $this->excel->getActiveSheet()->setCellValue('G1', 'DEDUCTION');
                    $this->excel->getActiveSheet()->setCellValue('H1', 'TAX');
                    $this->excel->getActiveSheet()->setCellValue('I1', 'NET');
                }

                $dt_head = '';
                $dt_head .= '<th>YEAR</th>';
                $dt_head .= '<th>MONTH</th>';
                $dt_head .= '<th>PAYCLASS</th>';
                $dt_head .= '<th>EMP.COUNT</th>';
                $dt_head .= '<th>BASIC</th>';
                $dt_head .= '<th>EARNINGS</th>';
                $dt_head .= '<th>DEDUCTION</th>';
                $dt_head .= '<th>TAX</th>';
                $dt_head .= '<th>NET</th>';

                $dt_columns = array(
                    array(
                        'title' => 'YEAR', 'data' => 'year', 'sClass' => ''
                    ), array(
                        'title' => 'MONTH', 'data' => 'month', 'sClass' => ''
                    ), array(
                        'title' => 'PAYCLASS', 'data' => 'payclass', 'sClass' => ''
                    ), array(
                        'title' => 'EMP.COUNT', 'data' => 'cnt', 'sClass' => 'number'
                    ), array(
                        'title' => 'BASIC', 'data' => 'basic', 'sClass' => 'number text-primary'
                    ), array(
                        'title' => 'EARNINGS', 'data' => 'earnings', 'sClass' => 'number text-success'
                    ), array(
                        'title' => 'DEDUCTION', 'data' => 'deductions', 'sClass' => 'number text-danger'
                    ), array(
                        'title' => 'TAX', 'data' => 'tax', 'sClass' => 'number'
                    ), array(
                        'title' => 'NET', 'data' => 'net', 'sClass' => 'number text-primary bold'
                    ),
                );

                if($payclass == 1) {
                    // GET IDS OF CONFI AN CA
                    $where = ' AND prg.payclass = 1';
                } else {
                    // GET IDS OF RF TIERED
                    $payclass_id = array();
                    $payclassqry = $this->db->select('typesid')
                        ->from('prime_employee_payclass_ref_matrix')
                        ->where('selectval',2)->get();
                    if($payclassqry){
                        foreach ($payclassqry->result() AS $row){
                            $payclass_id[] = $row->typesid;
                        }
                    }
                    $payclass_in_id = implode(', ', $payclass_id);
                    $where = ' AND prg.payclass IN (' . $payclass_in_id . ')';
                }

                $qry_list = $this->db->query("
                    SELECT
                        prg.years,
                        prg.months,
                        prm.ccid,
                        eprm.selectval,
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    GROUP BY eprm.selectval, prm.ccid, prg.years, prg.months
                    ORDER BY prm.ccid, prg.months DESC
                ");

                $excel_num = 2;

                foreach ($qry_list->result() as $row) {

                    $payclass_name = '';

                    if ($row->selectval == 2) {
                        $payclass_name = 'RF/Tiered';
                    } else {
                        $payclass_name = 'Confi/SA';
                    }

                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue('A' . $excel_num, $row->years);
                        $this->excel->getActiveSheet()->setCellValue('B' . $excel_num, date_formating($row->months,'!m','F'));
                        $this->excel->getActiveSheet()->setCellValue('C' . $excel_num, $payclass_name);
                        $this->excel->getActiveSheet()->setCellValue('D' . $excel_num, $row->cnt);
                        $this->excel->getActiveSheet()->setCellValue('E' . $excel_num, $row->basic);
                        $this->excel->getActiveSheet()->setCellValue('F' . $excel_num, $row->earnings);
                        $this->excel->getActiveSheet()->setCellValue('G' . $excel_num, $row->deductions);
                        $this->excel->getActiveSheet()->setCellValue('H' . $excel_num, $row->tax);
                        $this->excel->getActiveSheet()->setCellValue('I' . $excel_num, $row->net);
                    } else {
                        $data['list'][] = array(
                            'year' => $row->years,
                            'month' => date_formating($row->months,'!m','F'),
                            'payclass' => $payclass_name,
                            'cnt' => $row->cnt,
                            'basic' => number_format($row->basic, 2),
                            'earnings' => number_format($row->earnings, 2),
                            'deductions' => number_format($row->deductions, 2),
                            'tax' => number_format($row->tax, 2),
                            'net' => number_format($row->net, 2),
                        );
                    }
                    $excel_num++;
                }
            }
        }

        // ###################################################################
        // ###################################################################
        // END OF PAYCLASS QUERY
        // ###################################################################
        // ###################################################################

        if($report == 'excel') {
            $as_off = date('Y-m-d');
            $filename='EMPLOYEE_PAYROLL_REPORT_ASOF_'.$as_off; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
        }else {
            $data['th'] = $dt_head;
            $data['cols'] = $dt_columns;
            return json_encode($data);
        }
    }

    function get_payroll_reports($type = false, $year = false, $month = false, $costcenter = false, $payclass = false, $report = null) {
        $formpost = $this->input->post('formpost');
        if($formpost == 1) {
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            $type = $this->input->post('reptype');
            $payclass = $this->input->post('payclass');
            $costcenter = $this->input->post('costcenter');
        }


        $columns = array();
        $qry_list = array();

        if($report == 'excel') {
            //load our new PHPExcel library
            $this->load->library('excel');
            //activate worksheet number 1
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('READING REPORTS');
        }

        $cols_def = array(
            array('sort' => 0, 'data' => 'empid','title' => 'EmpID','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('sort' => 1, 'data' => 'empname','title' => 'EmpName','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('sort' => 2, 'data' => 'ccid','title' => 'CCID','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('sort' => 3, 'data' => 'selectval','title' => 'Payclass','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('sort' => 4, 'data' => 'months','title' => 'Month','sClass' => '','sWidth' => '100px', 'format' => 'month'),
            array('sort' => 5, 'data' => 'years','title' => 'Year','sClass' => '','sWidth' => '100px', 'format' => ''),
            array('sort' => 6, 'data' => 'cnt','title' => 'Count','sClass' => 'number','sWidth' => '100px', 'format' => 'number'),
            array('sort' => 7, 'data' => 'basic','title' => 'Basic','sClass' => 'text-primary','sWidth' => '100px', 'format' => 'number'),
            array('sort' => 8, 'data' => 'earnings','title' => 'Earnings','sClass' => 'text-success','sWidth' => '100px', 'format' => 'number'),
            array('sort' => 9, 'data' => 'deductions','title' => 'Deductions','sClass' => 'text-danger','sWidth' => '100px', 'format' => 'number'),
            array('sort' => 10, 'data' => 'tax','title' => 'TAX','sClass' => 'text-info','sWidth' => '100px', 'format' => 'number'),
            array('sort' => 11, 'data' => 'net','title' => 'NET','sClass' => 'text-primary bold','sWidth' => '100px', 'format' => 'number'),
            array('sort' => 12, 'data' => 'sysid','title' => 'ID','sClass' => '','sWidth' => '10px', 'format' => ''),
            array('sort' => 13, 'data' => 'payclass','title' => 'PC','sClass' => '','sWidth' => '10px', 'format' => ''),
        );
        //asort($cols_def);

        //QUERY VARIABLES
        $year_col = 'prg.years, ';
        $month_col = 'prg.months, ';
        $ccid_col = 'prm.ccid, ';
        $selectval_col = 'eprm.selectval, ';
        $payclass_col = 'tp.names payclass, ';

        $where_year = '';
        $where_month = '';
        $where_ccid = '';
        $where_payclass = '';

        $year_ = 'prg.years, ';
        $month_ = 'prg.months, ';
        $ccid_ = 'prm.ccid, ';
        $selectval_ = 'eprm.selectval, ';
        $payclass_ = 'tp.names, ';

        if ($year || $year > 0) {
            $where_year = 'AND prg.years = ' . $year;
            $year_col = '';
            $year_ = '';
        }

        if ($month || $month > 0) {
            $where_month = 'AND prg.months = ' . $month;
            $month_col = '';
            $month_ = '';
        }

        if ($costcenter || $costcenter > 0) {
            $where_ccid = 'AND prm.ccid = ' . $costcenter;
            $ccid_col = '';
            $ccid_ = '';
        }

        if ($payclass || $payclass > 0) {
            if ($payclass == 1) {
                // GET IDS OF CONFI AND SA
                $where_payclass = ' AND prg.payclass = 1';
            } else {
                // GET IDS OF RF TIERED
                $payclass_id = array();
                $payclassqry = $this->db->select('typesid')
                    ->from('prime_employee_payclass_ref_matrix')
                    ->where('selectval', 2)->get();
                if ($payclassqry) {
                    foreach ($payclassqry->result() AS $row) {
                        $payclass_id[] = $row->typesid;
                    }
                }
                $payclass_in_id = implode(', ', $payclass_id);
                $where_payclass = ' AND prg.payclass IN (' . $payclass_in_id . ')';
            }
            $selectval_col = '';
            $selectval_ = '';
        }


        // PER EMPLOYEE QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if($type == 3225) {

            $qry_list = $this->db->query("
                SELECT
                    em.sysid,
                    em.empid,
                    CONCAT(p.lastname,', ',p.firstname) as empname, 
                    $ccid_col
                    $payclass_col 
                    $month_col
                    $year_col
                    SUM(prm.basic) as basic,
                    SUM(prm.earnings) AS earnings,
                    SUM(prm.deductions) AS deductions,
                    SUM(prm.tax) AS tax,
                    SUM(prm.net) AS net
                FROM
                    prime_employee_main AS em
                INNER JOIN `payroll_reports_main` AS prm ON prm.empid = em.sysid
                INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                INNER JOIN person AS p ON em.personid = p.sysid
                LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                LEFT JOIN prime_types_parameter AS tp ON prg.payclass = tp.sysid
                WHERE prg.`status` = 301
                    $where_year
                    $where_month
                    $where_ccid
                    $where_payclass
                GROUP BY 
                    em.sysid, 
                    em.empid, 
                    empname, 
                    prm.ccid,
                    $payclass_ 
                    $month_
                    $year_
                    ''
                ORDER BY p.lastname ASC
            ");
        }

        //print_r($this->db->last_query());
        //exit();
        // ###################################################################
        // END OF EMPLOYEE QUERY
        // ###################################################################


        // PER COSTCENTER QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if($type == 3226) {

            $qry_list = $this->db->query("
                SELECT 
                    $year_col
                    $month_col
                    $ccid_col
                    SUM(prm.basic) as basic,
                    SUM(prm.earnings) AS earnings,
                    SUM(prm.deductions) AS deductions,
                    SUM(prm.tax) AS tax,
                    SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                WHERE prg.`status` = 301
                    $where_year
                    $where_month
                    $where_ccid
                    $where_payclass
                GROUP BY
                    $year_
                    $month_
                    $ccid_
                    ''
                ORDER BY prm.ccid
            ");
            //print_r($this->db->last_query());
            //exit();
        }

        // ###################################################################
        // END OF COSTCENTER QUERY
        // ###################################################################


        // PER MONTH QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if($type == 3227) {
            $qry_list = $this->db->query("
                SELECT
                    $year_col
                    $month_col
                    SUM(prm.basic) as basic,
                    SUM(prm.earnings) AS earnings,
                    SUM(prm.deductions) AS deductions,
                    SUM(prm.tax) AS tax,
                    SUM(prm.net) AS net
                FROM `payroll_reports_main` AS prm
                INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                WHERE prg.`status` = 301                     
                    $where_year
                    $where_month
                    $where_ccid
                    $where_payclass
                GROUP BY 
                    $year_
                    $month_
                    ''
                ORDER BY prg.months
            ");

            //print_r($this->db->last_query());
            //exit();
        }

        // ###################################################################
        // END OF MONTH QUERY
        // ###################################################################

        // PER PAYCLASS QUERY
        // ###################################################################
        // ###################################################################
        // ###################################################################
        if ($type == 3228) {

            $qry_list = $this->db->query("
                    SELECT
                        $year_col
                        $month_col
                        $ccid_col
                        $selectval_col
                        COUNT(distinct (prm.empid)) AS cnt,
                        SUM(prm.basic) as basic,
                        SUM(prm.earnings) AS earnings,
                        SUM(prm.deductions) AS deductions,
                        SUM(prm.tax) AS tax,
                        SUM(prm.net) AS net
                    FROM payroll_reports_main AS prm
                    INNER JOIN `payroll_reports_group` AS prg ON prg.sysid = prm.groupid 
                    LEFT JOIN prime_employee_payclass_ref_matrix AS eprm ON eprm.typesid = prg.payclass
                    WHERE prg.status = 301
                        $where_year
                        $where_month
                        $where_ccid
                        $where_payclass
                    GROUP BY 
                        $year_
                        $month_
                        $ccid_
                        $selectval_
                        ''
                    ORDER BY prm.ccid, prg.months DESC
                ");

        }
        //$data['sql'] = $this->db->last_query();
        // ###################################################################
        // ###################################################################
        // END OF PAYCLASS QUERY
        // ###################################################################
        // ###################################################################

        if ($qry_list) {
            //LIST RESULT COLUMN HEADERS
            $cols = $qry_list->list_fields();

            //POPULATE COLUMN HEADERS
            $let = 'A';
            foreach ($cols as $col_row) {
                $col_index = $this->search_for_index($col_row, $cols_def);
                $title = $cols_def[$col_index]['title'];
                if ($col_index != null) {
                    $columns[] = $cols_def[$col_index];
                }
                //EXCEL COLUMN HEADERS
                if ($report == 'excel') {
                    $this->excel->getActiveSheet()->setCellValue($let . '1', $title);
                    $let++;
                }
            }

            //POPULATE TABLE DATA
            $excel_num = 2;
            $data['cols'] = $cols;
            foreach ($qry_list->result() AS $index => $row) {
                $value = array();
                $let = 'A';
                foreach ($cols as $field) {
                    $col_index_ = $this->search_for_index($field, $cols_def);
                    $format = '';
                    if ($col_index_ != null) {
                        $format = $cols_def[$col_index_]['format'];
                    }

                    if ($format == 'number') {
                        $value[] = number_format($row->$field, 2);
                    } else {
                        if ($format == 'month') {
                            $value[] = $row->$field.' - '.date_formating($row->$field, '!m', 'F');
                        } else {
                            if ($field == 'selectval') {
                                if ($row->$field == 2) {
                                    $value[] = 'RF/Tiered';
                                } else {
                                    $value[] = 'Confi/SA';
                                }
                            } else {
                                if ($field == 'ccid'){
                                    $value[] = get_costcenter_name($row->$field,true);
                                }else {
                                    if ($field == 'payclass' && $row->$field == 'Cash'){
                                        $value[] = 'CONFI';
                                    } else {
                                        $value[] = $row->$field;
                                    }
                                }
                            }
                        }
                    }
                    //WRITE DATA RESULT TO EXCEL
                    if ($report == 'excel') {
                        $this->excel->getActiveSheet()->setCellValue($let . $excel_num, $row->$field);
                        $let++;
                    }
                }
                $data['list'][] = array_combine($cols, $value);


                $excel_num++;
            }
        }

        if ($report == 'excel') {
            $as_off = date('Y-m-d');
            $type_qry = $this->db->select('names')
                ->from('prime_types_parameter')
                ->where('sysid',$type)->get()->row();
            $payclass_arr = array('Confi-SA','RF-Tiered');
            $excelmonth = ($month) ? '_'.date('M',mktime(0,0,0,$month,1)) : '';
            $excelyear = ($year) ? '_'.$year : '';
            $excelccid = ($costcenter) ? '_'.get_costcenter_name($costcenter,true) : '';
            $excelpayclass = ($payclass) ? '_'.$payclass_arr[$payclass] : '';
            $excelreptype = str_replace(' ','',$type_qry->names);

            $filename = 'PAYROLL_REPORT_'.$excelreptype.$excelmonth.$excelyear.$excelccid.$excelpayclass.'_ASOF_'.$as_off; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
        } else {
            $data['columns'] = $columns;
            //$data['sql'] = $this->db->last_query();
            return json_encode($data);
        }
    }

    function get_cc_children($groupid, $emp = false) {
        $child_1 = array();
        $qry_child_1 = $this->db->select()
            ->from('prime_costcenter_group_matrix')
            ->where(array('groupid' => $groupid))
            ->get();

        $next_level = $groupid + 1;

        if($qry_child_1->num_rows() > 0) {
            foreach($qry_child_1->result() as $ch1_row) {
                $child = $this->get_cc_children($next_level);
                $child_1[] = array(
                    'name' => $ch1_row->ccid,
                    'title' => $ch1_row->ccid . ' - ' . $groupid,
                    'children' => $child,
                );
            }
        }
        return $child_1;
    }

    function get_cc_employee($ccid) {
        $data = array();
        /*
        $qry = $this->db->select('ec.empid')
            ->from('prime_employee_costcenter ec')
            ->join('prime_employee_main AS em', 'em.sysid = ec.empid')
            ->where(array('ec.ccid' => $ccid, 'ec.status' => 1, 'em.status' => 1, 'ec.type' => 1))
            ->get();
        */

        $qry = $this->db->query("
            SELECT
                em.sysid,
                ecc.ccid,
                CONCAT( p.lastname, ', ', p.firstname, ' ', p.middlename ) AS empname,
                tp.`names` AS posname,
                em.empid 
            FROM
                prime_employee_main AS em
                INNER JOIN prime_employee_main_positions AS emp ON emp.emp_id = em.sysid
                INNER JOIN person AS p ON p.sysid = em.personid
                INNER JOIN prime_types_parameter AS tp ON emp.position_id = tp.sysid
                INNER JOIN prime_employee_costcenter AS ecc ON ecc.empid = em.sysid 
                LEFT OUTER JOIN prime_costcenter_head AS pch ON pch.empid = em.sysid AND pch.status = 1
                AND ecc.`status` = 1 
            WHERE
                ecc.ccid = $ccid 
                AND ecc.`status` = 1 
                AND em.`status` = 1 
                AND emp.`status` = 1 
                AND ecc.`type` = 1 
                AND pch.empid IS NULL
            ORDER BY
                empname ASC
        ");
        if($qry->num_rows()>0) {
            $staf_list = '';
            foreach($qry->result() as $row) {
                if($row->empid!=1) {
                    //$empname_arr = get_employee_info($row->empid);
                    $staf_list .= $row->empname . '<br>';
                }
            }
            $data[] = array(
                'name' => 'Staff',
                'title' => $staf_list,
                'children' => array(),
            );
        }
        return $data;
    }

    function get_company_org_charts() {

        $data = array();
        $qry_group_level = $this->db->select('empid')
            ->from('prime_costcenter_group_head')
            ->where(array('groupid' => 1))
            ->get()->row();
        if($qry_group_level) {

            // DIRECT DEPARTMENT TO P-CEO
            $child_1 = array();
            $qry_child_1 = $this->db->select()
                ->from('prime_costcenter_group_matrix')
                ->where(array('groupid' => 1))
                ->get();

            if($qry_child_1->num_rows() > 0) {
                foreach($qry_child_1->result() as $ch1_row) {
                    $qry_cc_1_headinfo = $this->db->select('empid')->from('prime_costcenter_head')
                        ->where(array('ccid' => $ch1_row->ccid, 'status' => 1))
                        ->get()->row();
                    $head_name = '';
                    if($qry_cc_1_headinfo) {
                        $head_info_arr = get_employee_info($qry_cc_1_headinfo->empid);
                        $head_name = $head_info_arr->qry->firstname . ' ' . $head_info_arr->qry->lastname;
                    }

                    $ccinfo_full = get_costcenter_name($ch1_row->ccid, true);
                    if($qry_cc_1_headinfo->empid != 1) {
                        $child_1[] = array(
                            'name' => $head_name,
                            'title' => $ccinfo_full,
                            'children' => $this->get_cc_employee($ch1_row->ccid),
                        );
                    } else {
                        $child_1[] = array(
                            'name' => 'Main Office Admin',
                            'title' => $ccinfo_full,
                            'children' => $this->get_cc_employee($ch1_row->ccid),
                        );
                    }
                }
            }

            // EXEC
            $qry_execs = $this->db->query("
                SELECT pcg.sysid, pcg.codes, pcg.descs, pcgh.empid
                FROM prime_costcenter_group AS pcg
                LEFT JOIN prime_costcenter_group_head AS pcgh ON pcgh.groupid = pcg.sysid
                WHERE pcg.level IN (2, 3) AND pcg.status = 1
            ");
            if($qry_execs->num_rows() > 0) {
                foreach($qry_execs->result() as $erow) {
                    $exec_info_arr = get_employee_info($erow->empid);
                    $exec_name = $exec_info_arr->qry->firstname . ' ' . $exec_info_arr->qry->lastname;
                    $exec_children = array();
                    $qry_exec_children = $this->db->select('pcgm.ccid, pch.empid')
                        ->from('prime_costcenter_group_matrix AS pcgm')
                        ->join('prime_costcenter_head AS pch', 'pch.ccid = pcgm.ccid')
                        ->where(array('pcgm.groupid' => $erow->sysid, 'pch.status' => 1))
                        ->get();
                    if($qry_exec_children->num_rows()>0) {
                        foreach($qry_exec_children->result() as $exerow) {

                            $ccinfo_full = get_costcenter_name($exerow->ccid, true);


                            $cc_emp_info_arr = get_employee_info($exerow->empid);
                            $cc_emp_name = $cc_emp_info_arr->qry->firstname . ' ' . $cc_emp_info_arr->qry->lastname;

                            $exec_cc_emp = $this->get_cc_employee($exerow->ccid);
                            $exec_children[] = array(
                                'name' => $cc_emp_name,
                                'title' => $ccinfo_full,
                                'children' => $exec_cc_emp,
                            );
                        }
                    }
                    $child_1[] = array(
                        'name' => $exec_name . ' - ' . $erow->sysid,
                        'title' => $erow->descs,
                        'children' => $exec_children,
                    );
                }
            }

            $pceo_name_arr = get_employee_info($qry_group_level->empid);
            $pceo_name = $pceo_name_arr->firstname .  ' ' . $pceo_name_arr->lastname;
            $data = array(
                'name' => $pceo_name,
                'title' => 'P-CEO',
                'children' => $child_1
            );
        }
        /*

        $data = array(
            'name' => 'Luis Miguel Cacho',
            'title' => 'President - CEO',
            'children' => array(
                array('name' => 'Emmanuel Lubis', 'title' => 'FM',
                         'children' => array(
                             array('name' => 'Marcelo Cacho', 'title' => 'Administrative Manager',
                                    'children' => array(
                                        array('name' => 'John Tadifa', 'title' => 'ITD-H')
                                    )
                             ),
                             array('name' => 'Mikel Afzuilious', 'title' => 'CAM')
                         )
                    ),
                array('name' => 'Aldren Deleste', 'title' => 'VP-OP',
                        'children' => array()
                    ),
            )
        );

        */

        return json_encode($data);
    }
}