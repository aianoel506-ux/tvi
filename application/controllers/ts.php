<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/5/2018
 * Time: 9:37 AM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ts extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_cwdo');
        $this->load->model('model_ts');
        $this->load->model('model_reports');
    }
    function index (){
        redirect(base_url(), 'refresh');
    }
    function gettroublecalllist() {
        echo $this->model_cwdo->get_ticket_list();
    }
    function getticketdetails() {
        echo $this->model_ts->get_ticket_details();
    }
    function gettypestext() {
        echo $this->model_ts->get_types_text();
    }
    function getgrouplist() {
        echo $this->model_ts->get_group_list();
    }
    function teamdeploy() {
        echo $this->model_ts->team_deploy();
    }
    function search() {
        echo $this->model_ts->search();
    }
    function gettsteamno(){
        echo $this->model_ts->select2_teamno();
    }
    function jointogroup() {
        echo $this->model_ts->join_to_group();
    }
    function assigntoteamrow() {
        echo $this->model_ts->assign_to_team_row();
    }
    function assigntoteamrowgroup() {
        echo $this->model_ts->assign_to_team_row_group();
    }
    function accomplish() {
        echo $this->model_ts->accomplish();
    }
    function accomplishrow() {
        echo $this->model_ts->accomplishrow();
    }
    function accomplishrowgroup() {
        echo $this->model_ts->accomplishrow_group();
    }
    function selecttcequipments() {
        echo $this->model_ts->select2_tc_equipments();
    }
    function selecttcfindings() {
        echo $this->model_ts->select2_tc_findings();
    }
    function gettsstatus() {
        echo $this->model_ts->select2_ts_status();
    }
    function addequipmentsrow() {
        echo $this->model_ts->add_equipments_row();
    }
    function addequipmentsrowgroup() {
        echo $this->model_ts->add_equipments_row_group();
    }
    function addfindingsrow() {
        echo $this->model_ts->add_findings_row();
    }
    function addfindingsrowgroup() {
        echo $this->model_ts->add_findings_row_group();
    }
    function addcircuitrow() {
        echo $this->model_ts->add_circuit_row();
    }
    function addcircuitrowgroup() {
        echo $this->model_ts->add_circuit_row_group();
    }
    function gettslogs() {
        echo $this->model_ts->get_ts_logs();
    }
    function drawsummarytable() {
        echo $this->model_ts->draw_summary_table();
    }
    function select2circuitlevel() {
        echo $this->model_ts->select2_circuit_level();
    }
    function select2outages() {
        echo $this->model_ts->select2_outages();
    }
    function addtcinfo() {
        echo $this->model_ts->add_tc_info();
    }
    function submitticket() {
        echo $this->model_ts->add_tc_entry();
    }
    function updatetcinfo() {
        echo $this->model_ts->edit_tc_info();
    }
    function removelistgroup() {
        echo $this->model_ts->remove_list_group();
    }
    function getselect2group() {
        echo $this->model_ts->get_select2_groups();
    }
    function updateetcrow() {
        echo $this->model_ts->udpate_etc_row();
    }
    function updateetcrowgroup() {
        echo $this->model_ts->udpate_etc_row_group();
    }
    function getservtime() {
        echo $this->model_ts->get_server_time();
    }
    function followupticket() {
        echo $this->model_ts->followup_ticket();
    }
    function followupgroup() {
        echo $this->model_ts->followup_group();
    }
    function teamqueue() {
        echo $this->model_ts->team_queue();
    }
    function checkoutagetype() {
        echo $this->model_ts->check_ts_outage_type();
    }
    function getdailytrends() {
        echo $this->model_reports->tc_daily_trends();
    }
    function getdistrictpie() {
        echo $this->model_reports->tc_district_pie();
    }
    function getstatuspie() {
        echo $this->model_reports->tc_status_pie();
    }
    function getbarangaycluster() {
        echo $this->model_reports->tc_barangay_cluster();
    }

    function gettcaverage() {
        echo $this->model_ts->tc_average_compute();
    }

    function gettcaveragelist() {
        echo $this->model_ts->tc_average_compute_list();
    }
    function gettcaverageexcel() {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        echo $this->model_ts->tc_average_compute_list(1, $datefrom, $dateto, $status);
    }

    function testing()
    {
        echo timeago('2019-09-04 09:33:00', sql_time()->DATETIME);
    }

    function saveteamshiftingassignment(){
        $data =array();

        $teamarr = $this->input->post('teamarr');
        $teamshift = $this->input->post('teamshift');
        $branchid = $this->input->post('branchid');
        $day = $this->input->post('day');
        $month = $this->input->post('month');
        $type = $this->input->post('type');
        $msg = '';
        $func = '';
        $qry = false;
        $this->db->trans_begin();
        foreach ($teamarr as $team){

            $this->db->set('a.status', 0);
            $this->db->set('b.status', 0);

            $this->db->where(array("b.teamid" => $team , "b.day" => $day,"b.branch" => $branchid));
            $this->db->where('a.sysid = b.schedid');
            $this->db->update('trn_schedule_requests as a, trn_schedule_requests_time as b');

            $getempinteam = $this->db->select("empid")->from("prime_employee_team_assignments")
                ->where(array("teamid" => $team  , "status" => 1))->get();
            if($getempinteam->num_rows() > 0){
                foreach ($getempinteam->result() as $row){

                    $schedreqarr = array(
                        'empid' => $row->empid,
                        'status' => 301,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                        'month' =>$month,
                        'type' => $type
                    );

                    $insertschedreq = $this->db->insert("trn_schedule_requests" , $schedreqarr);
                    $data['error1'] = $this->db->_error_message();
                    $lastid = $this->db->insert_id();

                    $getteamworkshift=  $this->db->select("sysid,logcnt,logtype,am_start,am_end,pm_start,pm_end")
                        ->from("prime_employee_main_workshift")->where(array("sysid" => $teamshift))->get()->row();
                    if($getteamworkshift){
                        $timereqarr = array(
                            'schedid' => $lastid,
                            'logscnt' => $getteamworkshift->logcnt,
                            'logtype' => $getteamworkshift->logtype,
                            'branch' => $branchid,
                            'amtimein' => $getteamworkshift->am_start,
                            'amtimeout' => $getteamworkshift->am_end,
                            'pmtimein' => $getteamworkshift->pm_start,
                            'pmtimeout' => $getteamworkshift->pm_end,
                            'status' => 301,
                            'day' => $day,
                            'teamid' => $team,
                        );
                        $insertschedtimereq = $this->db->insert("trn_schedule_requests_time" , $timereqarr);
                        $data['error2'] = $this->db->_error_message();
                    }



                }
            }
        }
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Team assignment has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to assign team.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        $data['teamarr'] = $teamarr;
        $data['teamshift'] = $teamshift;
        $data['branchid'] = $branchid;
        echo json_encode($data);
    }
    function getmonth(){
        $data = array();

        $sql = $this->db->select("sysid,desc")->from("prime_months")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc
                );
            }
        }

        echo json_encode($data);
    }
    function getempassignteam(){
        $data = array();
        $weekday = $this->input->post('weekday');
        $month = $this->input->post('month');
        $typehalf = $this->input->post('typedata');
        $year = $this->input->post('year');
        $typeshift = $this->input->post('typeshift');
      /*  $getemployee = $this->db->select("pem.sysid , p.lastname,p.firstname")->from("prime_employee_main as pem")
            ->join("person as p" , "p.sysid = pem.personid","left")
            ->where(array("pem.type " => 2 , "pem.status" => 1))
            ->get();

      $getemployee = $this->db->select("teso.empid , p.lastname , p.firstname")->from("trn_emp_schedule_operation as teso")
          ->join("prime_employee_main as pem" , "pem.sysid = teso.empid" , "left")
          ->join("person as p" , "p.sysid = pem.personid" , "left")
          ->where(array("teso.under" => user_id() , "teso.status" => 1))
          ->get();


        if($getemployee->num_rows() > 0){
            $num = 1;
            $mainindex = 0;
            foreach ($getemployee->result() as $row1){
                $data['assignteamdata'][] = array(
                    'num' => $num++,
                    'name' => strtoupper($row1->lastname),
                );
                $looprows = $this->db->select("sysid,names")->from("prime_types_parameter")
                    ->where(array("codes" => 'TSTEAM' , "status" => 1))
                    ->get();
                if($looprows->num_rows() > 0){
                    $i = 0;
                    $index = 0;

                    foreach ($looprows->result() as $row){
                        $checkifalreadyassign = $this->db->select("peta.sysid")->from("prime_employee_team_assignments as peta")
                            ->where(array("peta.teamid" => $row->sysid , "peta.empid" => $row1->empid , "peta.status" => 1,"peta.type" => $typehalf , "peta.year" => $year , "peta.month" => $month , "peta.typeshift" => $typeshift , "weekday" => $weekday))->get()->row();
                        $class = ($checkifalreadyassign) ? $class = 'checked' : '';

                        $checkboxes = array(

                            $index => '<input  '.$class.' name="'.$row1->empid.'" value="' . $row1->empid . '" type="checkbox" data-id="' . $row1->empid . '" data-team="'.$row->sysid.'" id="teamicheck' . $row1->empid . '" class="icheck" />'
                        );
                        array_push($data['assignteamdata'][$mainindex],$checkboxes[$i]);
                        $i++;
                        $index++;

                    }
                }
                $mainindex++;
            }

        }
        $columns1[] = array(
            'data' => 'num',
        );
        $columns1[] = array(
            'data' => 'name',
        );

        $getbranchcount = $this->db->select("COUNT(sysid) as count")->from("prime_types_parameter")
            ->where(array("codes" => 'TSTEAM'))
            ->get()->row();
        $data['branchcount'] = ($getbranchcount) ? $getbranchcount->count: 0;
        $getbranchcount= ($getbranchcount) ? $getbranchcount->count: 0;
        for($i=0;$i<$getbranchcount;$i++){
            $columns1[] = array(
                'data' => $i,
            );
        }
        $data['columns'][] = $columns1;*/
        echo json_encode($data);
    }
    function getdatasched(){
        $data = array();
        $typedata = $this->input->post('typedata');
        $month = $this->input->post('month');
        $report = $this->input->post('report');
        $date = date('Y').'-'.$month.'-'.'16';

        if($typedata == 1){
            $fromdate = date('Y').'-'.$month.'-'.'1';
            $todate = date('Y').'-'.$month.'-'.'15';
        }else{
            $fromdate = date('Y').'-'.$month.'-'.'16';
            $todate = date('Y').'-'.$month.'-'.date("t", strtotime($date));
        }
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;

        $schedbtn = '';
        $tabledata = '';
        $monday  = '';
        $tuesday  = '';
        $wednesday  = '';
        $thursday  = '';
        $friday  = '';
        $saturday  = '';
        $sunday  = '';

        $weekname = array(
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday',
        );
        $tabledata.= '<div class="row">';


        $tabledata.= '<div class="col-md-12 col-sm-12 col-xs-12" >';
        if($report == true){
            $tabledata.= peco_print_header(user_id(), "SUBSTATION OPERATION SCHEDULE FROM ".$fromdate." TO ".$todate, "", false);
        }
        $tabledata.= '<table class="table zui-table table-fixed  table-bordered table-responsive  tbl-xs shifttablesched" id="shifttablesched" >';
        $tabledata.= '<thead>';

        $tabledata.= '<th></th>';
        $tabledata.= '<th class="zui-sticky-col">Branch</th>';

        foreach ($weekname as $key => $value){
            $tabledata.= '<th></th>';
        }

        $tabledata.= '</thead>';
        $tabledata.= '<tbody>';

        if(user_id() != 1){
            $this->db->where(array("su.userid" => user_id()));
        }

       $getbranches = $this->db->select("pemw.sysid,pcb.desc,pemw.desc as timedesc,pcbwm.branchid,pcbwm.workshiftid")->from("prime_company_branch_workshift_matrix as pcbwm")
            ->join("prime_company_branch as pcb","pcb.sysid = pcbwm.branchid","left")
            ->join("shifting_users as su" , "su.branchid = pcb.sysid" , "left")
            ->join("prime_employee_main_workshift as pemw","pemw.sysid = pcbwm.workshiftid","left")
            ->where(array("pcb.status" => 1))
            ->order_by("pcbwm.sysid")
            ->get();
        if($getbranches->num_rows() > 0){
            $num = 1;
            $x = 1;
            foreach ($getbranches->result() as $row){
                if($x == 1){
                    $tabledata.= '<tr>';
                    $tabledata.= '<td  width="10px" ></td>';
                    $tabledata.= '<td style="width: 65px !important;" class="zui-sticky-col bold text-align-right">'.$row->desc.'</td>';
                    $tabledata.= '<td style="width: 65px !important;"  class="zui-sticky-col text-center bold">MON</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center bold">TUE</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center bold">WED</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center bold">THU</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center bold">FRI</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center bold">SAT</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center bold">SUN</td>';
                    $tabledata.= '</tr>';
                }
                $x++;

                $checkformonday = $this->db->select("sysid,empid,dayofweek")->from("trn_employee_schedule")
                    ->where(array("branchid" => $row->branchid , "workshiftid" => $row->workshiftid,"status" => 1 , "dayofweek" => 1,"fromdate" => $fromdate , "todate" => $todate))
                    ->group_by("sysid,empid,dayofweek")
                    ->get();
                $data['errorquery'] = $this->db->_error_message();
                if($checkformonday->num_rows() > 0){
                    foreach ($checkformonday->result() as $schedrow){
                            if($report){
                                $monday .=  $this->getlastname($schedrow->empid).' <br>';
                            }else{
                                $monday .=  $this->getlastname($schedrow->empid).' <button id="removeonschedbtn" data-id="'.$schedrow->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button><br>';
                            }
                    }
                }else{
                    $monday = '';
                }

                $checkfortuesday = $this->db->select("sysid,empid,dayofweek")->from("trn_employee_schedule")
                    ->where(array("branchid" => $row->branchid , "workshiftid" => $row->workshiftid,"status" => 1 , "dayofweek" =>2 ,"fromdate" => $fromdate , "todate" => $todate))
                    ->group_by("sysid,empid,dayofweek")
                    ->get();
                if($checkfortuesday->num_rows() > 0){
                    foreach ($checkfortuesday->result() as $schedrow){
                        if($report){
                            $tuesday .=  $this->getlastname($schedrow->empid).' <br>';
                        }else{
                            $tuesday .=  $this->getlastname($schedrow->empid).' <button id="removeonschedbtn" data-id="'.$schedrow->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button><br>';
                        }
                    }
                }else{
                    $tuesday = '';
                }

                $checkforwednesday = $this->db->select("sysid,empid,dayofweek")->from("trn_employee_schedule")
                    ->where(array("branchid" => $row->branchid , "workshiftid" => $row->workshiftid,"status" => 1 , "dayofweek" => 3,"fromdate" => $fromdate , "todate" => $todate))
                    ->group_by("sysid,empid,dayofweek")
                    ->get();
                if($checkforwednesday->num_rows() > 0){
                    foreach ($checkforwednesday->result() as $schedrow){
                        if($report){
                            $wednesday .=  $this->getlastname($schedrow->empid).' <br>';
                        }else{
                            $wednesday .=  $this->getlastname($schedrow->empid).' <button id="removeonschedbtn" data-id="'.$schedrow->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button><br>';
                        }
                    }
                }else{
                    $wednesday = '';
                }

                $checkforthursday = $this->db->select("sysid,empid,dayofweek")->from("trn_employee_schedule")
                    ->where(array("branchid" => $row->branchid , "workshiftid" => $row->workshiftid,"status" => 1 , "dayofweek" => 4,"fromdate" => $fromdate , "todate" => $todate))
                    ->group_by("sysid,empid,dayofweek")
                    ->get();
                if($checkforthursday->num_rows() > 0){
                    foreach ($checkforthursday->result() as $schedrow){
                        if($report){
                            $thursday .=  $this->getlastname($schedrow->empid).' <br>';
                        }else{
                            $thursday .=  $this->getlastname($schedrow->empid).' <button id="removeonschedbtn" data-id="'.$schedrow->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button><br>';
                        }
                    }
                }else{
                    $thursday = '';
                }

                $checkforfriday = $this->db->select("sysid,empid,dayofweek")->from("trn_employee_schedule")
                    ->where(array("branchid" => $row->branchid , "workshiftid" => $row->workshiftid,"status" => 1 , "dayofweek" => 5,"fromdate" => $fromdate , "todate" => $todate))
                    ->group_by("sysid,empid,dayofweek")
                    ->get();
                if($checkforfriday->num_rows() > 0){
                    foreach ($checkforfriday->result() as $schedrow){
                        if($report){
                            $friday .=  $this->getlastname($schedrow->empid).' <br>';
                        }else{
                            $friday .=  $this->getlastname($schedrow->empid).' <button id="removeonschedbtn" data-id="'.$schedrow->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button><br>';
                        }
                    }
                }else{
                    $friday = '';
                }

                $checkforsaturday = $this->db->select("sysid,empid,dayofweek")->from("trn_employee_schedule")
                    ->where(array("branchid" => $row->branchid , "workshiftid" => $row->workshiftid,"status" => 1 , "dayofweek" => 6,"fromdate" => $fromdate , "todate" => $todate))
                    ->group_by("sysid,empid,dayofweek")
                    ->get();
                if($checkforsaturday->num_rows() > 0){
                    foreach ($checkforsaturday->result() as $schedrow){
                        if($report){
                            $saturday .=  $this->getlastname($schedrow->empid).' <br>';
                        }else{
                            $saturday .=  $this->getlastname($schedrow->empid).' <button id="removeonschedbtn" data-id="'.$schedrow->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button><br>';
                        }
                    }
                }else{
                    $saturday = '';
                }

                $checkforsunday = $this->db->select("sysid,empid,dayofweek")->from("trn_employee_schedule")
                    ->where(array("branchid" => $row->branchid , "workshiftid" => $row->workshiftid,"status" => 1 , "dayofweek" => 7,"fromdate" => $fromdate , "todate" => $todate))
                    ->group_by("sysid,empid,dayofweek")
                    ->get();
                if($checkforsunday->num_rows() > 0){
                    foreach ($checkforsunday->result() as $schedrow){
                        if($report){
                            $sunday .=  $this->getlastname($schedrow->empid).' <br>';
                        }else{
                            $sunday .=  $this->getlastname($schedrow->empid).' <button id="removeonschedbtn" data-id="'.$schedrow->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></button><br>';
                        }
                    }
                }else{
                    $sunday = '';
                }

                    $tabledata.= '<tr>';
                    $tabledata.= '<td  width="10px" >'.$num++.'</td>';
                    //  $tabledata.= '<td  class="zui-sticky-col">'.$row->desc.'-'.$row->timedesc.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col  text-align-right">'.'('.$row->timedesc.')'.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center">'.$monday.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center">'.$tuesday.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center">'.$wednesday.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center">'.$thursday.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center">'.$friday.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center">'.$saturday.'</td>';
                    $tabledata.= '<td  style="width: 65px !important;"  class="zui-sticky-col text-center">'.$sunday.'</td>';
                    $tabledata.= '</tr>';

                if($x == 4){
                 /*   $tabledata.= '<tr>';
                    $tabledata.= '<td class="space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col bold space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col text-center bold space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col text-center bold space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col text-center bold space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col text-center bold space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col text-center bold space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col text-center bold space">&nbsp;</td>';
                    $tabledata.= '<td class="zui-sticky-col text-center bold space">&nbsp;</td>';
                    $tabledata.= '</tr>'; */
                    $x = 1;

                }

                $monday = '';
                $tuesday = '';
                $wednesday = '';
                $thursday = '';
                $friday = '';
                $saturday = '';
                $sunday = '';
            }

        }

        $tabledata.= '</tbody>';
        $tabledata.= '</table>';
        $tabledata.= '</div>';

        if($report){


            $empsig = $this->db->select("sysid , empid")->from("employee_signatory")
                ->where(array("moduleid" => 112 , "status" => 1))
                ->get();
            if($empsig->num_rows() > 0){
                foreach ($empsig->result() as $row){

                }
            }


            $tabledata.= '<div class="container" >';
            $tabledata.= '<div class="row">';

            $tabledata.= '<div class="col-md-3"  style="margin-top: 35px !important;">';
            $tabledata.= '<span>Prepared by:<br> <b>ENGR. F. SONZA JR</b></span><br><b>IE</b>';
            $tabledata.= '</div>';

            $tabledata.= '<div class="col-md-3" style="margin-top: 35px !important;">';
            $tabledata.= '<span><br> <b>ENGR. FINES</b></span><br><b>OCE</b>';
            $tabledata.= '</div>';

            $tabledata.= '<div class="col-md-3" style="margin-top: 35px !important;">';
            $tabledata.= '<span>Noted by:<br> <b>ENGR. A.R DELESTE</b></span><br><b>DE</b>';
            $tabledata.= '</div>';

            $tabledata.= '<div class="col-md-3" style="margin-top: 35px !important;">';
            $tabledata.= '<span>Approved by:<br> <b>ENGR. RANDY S. PASTOLERO</b></span><br><b>AVP-O</b>';
            $tabledata.= '</div>';

            $tabledata.= '</div>';
            $tabledata.= '</div>';
        }


        $tabledata.= '</div>';

        $data['tabledata'] = $tabledata;


        echo json_encode($data);
    }
    function getlastname($empid){

        $sql = $this->db->select("p.lastname")->from("prime_employee_main as pem")
            ->join("person as p","p.sysid = pem.personid", "left")
            ->where(array("pem.status" => 1 , "pem.sysid" => $empid))
            ->get()->row();
        return ($sql) ? $sql->lastname : '';
    }
    function getbranches(){
        $data = array();

        $sql = $this->db->select("sysid,code,desc,address,contactno")->from("prime_company_branch")
            ->where(array("status" => 1))
            ->get();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['brancheslist'][] = array(
                    'num' => $num++,
                    'code' => $row->code,
                    'desc' => $row->desc,
                    'address' => $row->address,
                    'contactno' => $row->contactno,
                    'control' => '<button id="deletebranchbtn" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-trash"></i></button>'
                 );
            }
        }

        echo json_encode($data);
    }
    function addbranch() {
        $data = array();

        $codetxt = $this->input->post('codetxt');
        $desctxt = $this->input->post('desctxt');
        $addresstxt = $this->input->post('addresstxt');
        $contacttxt = $this->input->post('contacttxt');
        $typebranch = $this->input->post('typebranch');
        $this->db->trans_begin();
        $insarr = array(
            'code' => $codetxt,
            'desc' => $desctxt,
            'address' => $addresstxt,
            'contactno' => $contacttxt,
            'type' => $typebranch,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1
        );
        $sql = $this->db->insert("prime_company_branch" , $insarr);
        $lastid = $this->db->insert_id();
        $gettssched = $this->db->select("sysid")->from("prime_employee_main_workshift")
            ->where(array("weekend" => 1))->get();
        if($gettssched->num_rows() > 0){
            foreach ($gettssched->result() as $row){
                $insbranchmatrix =  array(
                    'branchid' => $lastid,
                    'workshiftid' => $row->sysid,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'status' => 1
                );
                $this->db->insert("prime_company_branch_workshift_matrix" , $insbranchmatrix);
            }
        }
        $shiftinguserarr = array(
            'userid' => user_id(),
            'branchid' => $lastid,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert("shifting_users" , $shiftinguserarr);

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Branch has been added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to add branch.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function removeonsched(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("trn_employee_schedule" , $updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Employee has been removed from schedule';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Fail to remove employee from schedule.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function getcompanybranches(){
        $data = array();
        if(user_id() != 1){
            $this->db->where(array("su.userid" => user_id()));
        }
        $sql = $this->db->select("pcbwm.sysid,pcb.desc,pcb.code , pemw.desc as timedesc")->from("prime_company_branch as pcb")
            ->join("prime_company_branch_workshift_matrix as pcbwm" , "pcbwm.branchid = pcb.sysid" , "left")
            ->join("prime_employee_main_workshift as pemw" , "pemw.sysid = pcbwm.workshiftid" , "left")
            ->join("shifting_users as su" , "su.branchid = pcb.sysid" , "left")
            ->where(array("pcbwm.status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc.' '.$row->timedesc.' - '.''
                );
            }
        }

        echo json_encode($data);
    }
    function getdatesched(){
        $data = array();
        $typedata = $this->input->post('type');
        $month = $this->input->post('month');

        $date = date('Y').'-'.$month.'-'.'1';

        if($typedata == 1){
            $fromdate = date('Y').'-'.$month.'-'.'1';
            $todate = date('Y').'-'.$month.'-'.'15';
        }else{
            $fromdate = date('Y').'-'.$month.'-'.'16';
            $todate = date('Y').'-'.$month.'-'.date("t", strtotime($date));
        }

        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;

        echo json_encode($data);
    }
    function getworkshiftid(){
        $data = array();
        $branchid = $this->input->post('branchid');

        $sql = $this->db->select("branchid,workshiftid")->from("prime_company_branch_workshift_matrix")
            ->where(array("sysid" => $branchid , "status" => 1))->get()->row();

        $data['workshiftid'] = $sql->workshiftid;
        $data['branchid'] = $sql->branchid;
        echo json_encode($data);
    }
    function gettypesched(){
        $data = array();
        $typearr = array(
            '1' => '1st Half',
            '2' => '2nd Half'
        );

        foreach ( $typearr as $key => $value ){
            $data['list'][] = array(
                'id' => $key,
                'text' => $value . ' - ' . $key
            );
        }

        echo json_encode($data);
    }
    function removebranch(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();

        $updatearr = array(
            'status' => 0
        );

        $this->db->where(array("sysid" => $dataid));
        $this->db->update("prime_company_branch" , $updatearr);

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Branch has been removed.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to remove branch.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        echo json_encode($data);
    }
    function submitemptosched(){
        $data = array();

        $employeelist = $this->input->post('employeelist');
        $emptype = $this->input->post('emptype');

        $this->db->trans_begin();

        $insarr = array(
            'empid' => $employeelist,
            'type' => $emptype,
            'under' => user_id(),
            'status' => 1,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $sql = $this->db->insert("trn_emp_schedule_operation" , $insarr);
        $data['error']  = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Employee has been added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add employee.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['userid'] = user_id();

        echo json_encode($data);
    }
    function fetchoperationemp(){
        $data = array();
        $dataid = $this->input->post('dataid');
        if($dataid != 1){
            $this->db->where(array("teso.under" => $dataid));
        }
        $sql = $this->db->select("teso.empid , p.lastname, p.firstname")->from("trn_emp_schedule_operation as teso")
            ->join("prime_employee_main as pem" , "pem.sysid = teso.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->where(array("teso.status" => 1))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row) {
                $data['empdata'][] = array(
                    'num' => $num++,
                    'emp' => ucwords(strtolower($row->lastname)).', '.ucwords(strtolower($row->firstname)),
                    'control' => '<button data-id="'.$row->empid.'" id="deleteemp" class="btn btn-xs btn-danger inline"><i class="fa fa-trash"></i></button>'
                );
            }
        }

        echo json_encode($data);
    }

    function getbranchtype(){
        $data = array();
        $typearr = array(
            'SB' => 'SUBSTATION',
            'TS' => 'TROUBLE SHOOTER'
        );

        foreach ( $typearr as $key => $value ){
            $data['list'][] = array(
                'id' => $key,
                'text' => $value
            );
        }

        echo json_encode($data);
    }
    function getshiftype(){
        $data = array();

        $sql = $this->db->select("sysid , desc")->from("prime_employee_main_workshift")
            ->where(array("weekend" => 1))->order_by("sysid")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid ,
                    'text' => $row->desc
                );
            }
        }

        echo json_encode($data);
    }
    function getweekday(){
        $data = array();

        $weekdayarr = array(
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday',
        );

        foreach ($weekdayarr as $key => $value){
            $data['list'][] = array(
                'id' => $key,
                'text' => $value.' - '.$value
            );
        }

        echo json_encode($data);
    }
    function gettsreport(){
        $data = array();
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $type = $this->input->post('type');
        $report = $this->input->post('report');

        $weekarr = array(
            '1' => 'Mon',
            '2' => 'Tue',
            '3' => 'Wed',
            '4' => 'Thu',
            '5' => 'Fri',
            '6' => 'Sat',
            '7' => 'Sun',
        );
        if($type == 1){
            $typehalftext = '01-15, ';
        }else{
            $typehalftext = '16-31, ';
        }

        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');
        $html = '';



        $html .= '<div class="container">';
        $html .= '<div class="row">';
        if($report){
            $html .= peco_print_header(user_id(), "Schedule of Operation - TS ".$monthName.' '.$typehalftext.date('Y'), "", false);
        }
        $html .= '<div class="col-md-12 col-xs-12 col-sm-12">';
            $html .= '<table style="width: 1000px !important;" class="table table-bordered table-condensed table-responsive tbl-xs" id="tstablesched">';
            $html .= '<thead>';
            $html .= '<th  style="text-align: center !important;font-size: 10px !important;">Shift</th>';

            foreach ($weekarr as $key => $value){
                $html .= '<th  style="text-align: center !important;font-size: 10px !important;">'.$value.'</th>';
            }
            $html .= '<th></th>';
            $html .= '</thead>';

            $html .= '<tbody>';

            $sql = $this->db->select("sysid , desc")->from("prime_employee_main_workshift")
                ->where(array("weekend" => 1))->get();
            if($sql->num_rows() > 0){
                foreach ($sql->result() as $row){
                    $html .= '<tr>';
                    $html .= '<td colspan="8"  style="text-align: center !important;font-size: 10px !important;">Time Schedule('.$row->desc.')</td>';
                    $html .= '</tr>';
                    $data['shift'][] = array(
                        'shiftid' => $row->sysid
                    );
                    $getcontent = $this->db->select("ets.sysid , ptp.names ,ets.mon,ets.tue,ets.wed,ets.thu,ets.fri,ets.sat,ets.sun")->from("emp_team_schedule as ets")
                        ->where(array("ets.month" => $month , "ets.year" => $year , "ets.type" => $type , "ets.shiftid" => $row->sysid , "ets.status" => 1))
                        ->join("prime_types_parameter as ptp" , "ptp.sysid = ets.teamid" , "left")
                        ->get();
                    $data['error'] = $this->db->_error_message();
                    if($getcontent->num_rows() > 0){
                        foreach ($getcontent->result() as $val){

                            $html .= '<tr>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$val->names.'</td>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$this->getlastname($val->mon).'</td>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$this->getlastname($val->tue).'</td>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$this->getlastname($val->wed).'</td>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$this->getlastname($val->thu).'</td>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$this->getlastname($val->fri).'</td>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$this->getlastname($val->sat).'</td>';
                            $html .= '<td style="text-align: center !important;font-size: 10px !important;">'.$this->getlastname($val->sun).'</td>';
                            if($report){
                                $html .= '<td></td>';
                            }else{
                                $html .= '<td> <a href="#edittssched" id="edittssched" data-toggle="ajax-modal" data-view="'.$val->sysid.'" data-arr="" class="btn btn-info btn-xs"><i class="fa fa-edit"></i> </a>
                                            <a  id="deletesched" data-id="'.$val->sysid.'"  class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> </a>
                                            </td>';
                            }
                            $html .= '</tr>';
                        }
                    }
                }
            }



            $html .= '</tbody>';
            $html .= '</table>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<table class="table table-bordered table-responsive tbl-xs">';
        $html .= '<thead>';
        $html .= '<th  style="text-align: center !important;font-size: 10px !important;"></th>';
        $html .= '<th  style="text-align: center !important;font-size: 10px !important;">No. of days</th>';
        $html .= '<th  style="text-align: center !important;font-size: 10px !important;">No. of OT</th>';
        $html .= '<th  style="text-align: center !important;font-size: 10px !important;">Rest Day</th>';
        $html .= '</thead>';
        $html .= '<tbody>';


        $getemployees =$this->db->select("teso.empid , p.lastname , p.firstname")->from("trn_emp_schedule_operation teso")
            ->join("prime_employee_main as pem" , "pem.sysid = teso.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->where(array("teso.type" => 'ts'))
            ->order_by("p.lastname")
            ->get();
        if($getemployees->num_rows() > 0){
            foreach ($getemployees->result() as $emprow){
                $nodays= 0;
                $restdayarr = array();
                $loopweek = array(
                    'MON' => 'mon',
                    'TUE' => 'tue',
                    'WED' => 'wed',
                    'THU' => 'thu',
                    'FRI' => 'fri',
                    'SAT' => 'sat',
                    'SUN' => 'sun',
                );
                foreach ($loopweek as $key =>  $value){
                    $getinfo = $this->db->select($value)->from("emp_team_schedule")
                        ->where(array("month" => $month , "year" => $year,
                            "type" => $type , $value =>$emprow->empid))->get()->row();
                    if($getinfo){
                        $nodays += 1;
                    }else{
                        $restdayarr[] = $key;
                    }
                }

                if($nodays != 0){
                    $rd = implode(" & ",$restdayarr);
                }else{
                    $rd = '';
                }


                $html .= '<tr>';
                $html .= '<td  style="text-align: center !important;font-size: 10px !important;">'.$emprow->lastname .'</td>';
                $html .= '<td  style="text-align: center !important;font-size: 10px !important;">'.$nodays.'</td>';
                $html .= '<td  style="text-align: center !important;font-size: 10px !important;"></td>';
                $html .= '<td  style="text-align: center !important;font-size: 10px !important;">'.$rd.'</td>';
                $html .= '</tr>';
            }
        }


        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<span  style="font-size: 10px !important;">*G. BALGOS @ Transformer Shop - Monday - Friday</span>';
        $html .= '</div>';


        $html .= '</div>';
        $html .= '</div>';


        if($report){

            $html .= '<div class="row" style="margin: 0px 10px;">';

            $html .= '<div class="col-md-1 col-xs-1 col-sm-1">';
            $html .= '<p    style="font-size: 10px !important;text-align: center !important;">Prepared by:</p>';
            $html .= '</div>';

            $html .= '<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top: 40px;">';
            $html .= '<div   style="font-size: 10px !important;text-align: center !important;">F.C Sonza Jr.</div>';
            $html .= '<div  style="font-size: 10px !important;text-align: center !important;">IE</div>';
            $html .= '</div>';

            $html .= '<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top: 40px;">';
            $html .= '<div  style="font-size: 10px !important;text-align: center !important;">M.J. G. Fines</div>';
            $html .= '<div  style="font-size: 10px !important;text-align: center !important;">MT-OCE</div>';
            $html .= '</div>';

            $html .= '<div class="col-md-1 col-xs-1 col-sm-1">';
            $html .= '<p  style="font-size: 10px !important;text-align: center !important;">Noted by:</p>';
            $html .= '</div>';

            $html .= '<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top: 40px;">';
            $html .= '<div  style="font-size: 10px !important;text-align: center !important;">A.R DELESTE</div>';
            $html .= '<div  style="font-size: 10px !important;text-align: center !important;">DE</div>';
            $html .= '</div>';

            $html .= '<div class="col-md-1 col-xs-1 col-sm-1">';
            $html .= '<p  style="font-size: 10px !important;text-align: center !important;">Approved by:</p>';
            $html .= '</div>';

            $html .= '<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top: 40px;">';
            $html .= '<div  style="font-size: 10px !important;text-align: center !important;">R. S. PASTOLERO</div>';
            $html .= '<div  style="font-size: 10px !important;text-align: center !important;">AVP-O</div>';
            $html .= '</div>';

            $html .= '</div>';

            $html .= '</div>';

        }


        $data['html'] = $html;
        echo json_encode($data);
    }
    function getteams(){
        $data = array();

        $sql = $this->db->select("sysid , names")->from("prime_types_parameter")
            ->where(array("codes" => 'TSTEAM' , "status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names .' - '.$row->names
                );
            }
        }

        echo json_encode($data);
    }
    function getshift(){
        $data = array();

        $sql = $this->db->select("sysid , desc")->from("prime_employee_main_workshift")
            ->where(array("weekend" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->desc .' - '.$row->desc
                );
            }
        }
        echo json_encode($data);
    }
    function gettsemp(){
        $data = array();

        $sql = $this->db->select("teso.empid , p.lastname , p.firstname")->from("trn_emp_schedule_operation teso")
            ->join("prime_employee_main as pem" , "pem.sysid = teso.empid" , "left")
            ->join("person as p" , "p.sysid = pem.personid" , "left")
            ->where(array("teso.type" => 'ts'))
            ->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->empid,
                    'text' => $row->lastname .' - '.$row->firstname
                );
            }
        }

        echo json_encode($data);
    }
    function submitsched(){
        $data = array();

        $team = $this->input->post('team');
        $shift = $this->input->post('shift');
        $mon = $this->input->post('mon');
        $tue = $this->input->post('tue');
        $wed = $this->input->post('wed');
        $thu = $this->input->post('thu');
        $fri = $this->input->post('fri');
        $sat = $this->input->post('sat');
        $sun = $this->input->post('sun');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $type = $this->input->post('type');
        $this->db->trans_begin();

        //check first if exist

      /*  $exist = $this->db->select("sysid")->from("emp_team_schedule")
            ->where(array("month" => $month , "year" => $year , "type" => $type ,
                "teamid" => $team , "shiftid" => $shift , "status" => 1))
            ->get()->row();
        if($exist){
            if(!$mon){
                $mon = null;
            }
            if(!$tue){
                $tue = null;
            }
            if(!$wed){
                $wed = null;
            }
            if(!$thu){
                $thu = null;
            }
            if(!$fri){
                $fri = null;
            }
            if(!$sat){
                $sat = null;
            }
            if(!$sun){
                $sun = null;
            }

            $updatearr = array(
                'mon' => $mon,
                'tue' => $tue,
                'wed' => $wed,
                'thu' => $thu,
                'fri' => $fri,
                'sat' => $sat,
                'sun' => $sun,
            );
            $this->db->where(array("month" => $month , "year" => $year , "type" => $type ,
                "teamid" => $team , "shiftid" => $shift , "status" => 1));
            $sql = $this->db->update("emp_team_schedule" , $updatearr); */

            if(!$mon){
                $mon = null;
            }
            if(!$tue){
                $tue = null;
            }
            if(!$wed){
                $wed = null;
            }
            if(!$thu){
                $thu = null;
            }
            if(!$fri){
                $fri = null;
            }
            if(!$sat){
                $sat = null;
            }
            if(!$sun){
                $sun = null;
            }

            $insarr =array(
                'month' => $month,
                'year' => $year,
                'type' => $type,
                'teamid' => $team,
                'shiftid' => $shift,
                'mon' => $mon,
                'tue' => $tue,
                'wed' => $wed,
                'thu' => $thu,
                'fri' => $fri,
                'sat' => $sat,
                'sun' => $sun,
                'status' => 1,
                'createdby' => user_id(),
                'updatedby' => user_id(),
            );

            $sql = $this->db->insert("emp_team_schedule" , $insarr);


        if($this->db->trans_status() == TRUE && $sql){
            $this->db->trans_commit();
            $msg = 'Schedule has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to save schedule.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);

    }
    function updatetssched(){
        $data = array();
        $sysid = $this->input->post('sysid');
        $mon = $this->input->post('mon');
        $tue = $this->input->post('tue');
        $wed = $this->input->post('wed');
        $thu = $this->input->post('thu');
        $fri = $this->input->post('fri');
        $sat = $this->input->post('sat');
        $sun = $this->input->post('sun');

        $updatearr =array(
            'mon' => $mon,
            'tue' => $tue,
            'wed' => $wed,
            'thu' => $thu,
            'fri' => $fri,
            'sat' => $sat,
            'sun' => $sun,
            'status' => 1,
            'createdby' => user_id(),
            'updatedby' => user_id(),
        );
        $this->db->where(array("sysid" => $sysid));
        $sql = $this->db->update("emp_team_schedule" , $updatearr);

        if($sql){
            $msg = 'Schedule has been updated.';
            $func = 'success';
            $qry = true;
        }else{
            $msg = 'Failed to update schedule.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function deletetssched(){
        $data = array();
        $schedid = $this->input->post('schedid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("status" => 1 , "sysid" => $schedid));
        $sql = $this->db->update("emp_team_schedule" , $updatearr);
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $data['msg'] = 'Schedule has been deleted.';
            $data['func'] = 'success';
            $data['qry'] = true;
        }else{
            $this->db->trans_rollback();
            $data['msg'] = 'Failed to delete schedule.';
            $data['func'] = 'error';
            $data['qry'] = false;
        }
        echo json_encode($data);
    }
    function select2day(){
        $data = array();
        for($i = 1; $i <= 31; $i++){
            $data['list'][] = array(
                'id' => $i,
                'text' => $i.' - '.''
            );
        }
        echo json_encode($data);
    }
}