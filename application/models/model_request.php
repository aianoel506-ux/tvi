<?php


class Model_request extends CI_Model
{
    function get_check_credits() {
        $data = array();
        $empid = $this->input->post('empid');
        $leavetype = $this->input->post('leavetype');

        $sql = $this->db->select("sysid,credit")
            ->from("prime_employee_main_leave_credits")
            ->where(array("empid"=>$empid , "types"=>$leavetype , "status" => 1))
            ->get()->row();
        if($sql){
            $data['credit'] = $sql->credit;
            $data['sysid'] = $sql->sysid;
        }else{
            $data['credit'] = 0;
        }
        return json_encode($data);
    }

    function get_leave_type() {
        $data = array();
        $empid = $this->input->post('data');

        $sql = $this->db->select("pemlc.types , ptp.names , ptp.desc")
            ->from("prime_employee_main_leave_credits as pemlc")
            ->join("prime_types_parameter as ptp" , "ptp.sysid = pemlc.types" , "left")
            ->where(array('ptp.codes' => 'LEAVECREDITS' , "pemlc.empid" => $empid))
            ->group_by("pemlc.types , ptp.names , ptp.desc")
            ->get();
        $data['errorgetleavetype'] = $this->db->_error_message();
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->types,
                    'text' => $row->names . ' - ' . $row->desc,
                );
            }
        }
        return json_encode($data);
    }

    function process_leave_form() {
        $data = array();
        $hiddenempid = $this->input->post('hiddenempid');
        $leavetype = $this->input->post('leavetype');
        $hiddenleavedays = $this->input->post('hiddenleavedays');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $selectleavetype = $this->input->post('selectleavetype');
        $userid = $this->input->post('userloggedin');
        //  $reason = $this->input->post('reason');
        $moduleid = $this->input->post('moduleid');
        $yearleave = $this->input->post('yearleave');

        $fromhours = $this->input->post('fromhours');
        $fromminutes = $this->input->post('fromminutes');
        $fromseconds = $this->input->post('fromseconds');
        $fromampm = $this->input->post('fromampm');

        $tohours = $this->input->post('tohours');
        $tominutes = $this->input->post('tominutes');
        $toseconds = $this->input->post('toseconds');
        $toampm = $this->input->post('toampm');

        if(strlen($fromhours) == 1){
            $fromhours =   str_pad($fromhours,2,"0",STR_PAD_LEFT);
        }
        if(strlen($fromminutes) == 1){
            $fromminutes =   str_pad($fromminutes,2,"0",STR_PAD_LEFT);
        }
        if(strlen($fromseconds) == 1){
            $fromseconds =   str_pad($fromseconds,2,"0",STR_PAD_LEFT);
        }
        if(strlen($tohours) == 1){
            $tohours =   str_pad($tohours,2,"0",STR_PAD_LEFT);
        }
        if(strlen($tominutes) == 1){
            $tominutes =   str_pad($tominutes,2,"0",STR_PAD_LEFT);
        }
        if(strlen($toseconds) == 1){
            $toseconds =   str_pad($toseconds,2,"0",STR_PAD_LEFT);
        }

        /*   if($fromhours == '' || $fromhours == null){
               $fromhours = '00';
           }
           if($fromminutes == '' || $fromminutes == null){
               $fromminutes = '00';
           }
           if($fromseconds == '' || $fromseconds == null){
               $fromseconds = '00';
           }
           if($tohours == '' || $tohours == null){
               $tohours = '00';
           }
           if($tominutes == '' || $tominutes == null){
               $tominutes = '00';
           }
           if($toseconds == '' || $toseconds == null){
               $toseconds = '00';
           } */

        $data['fromhours'] = $fromhours;
        $data['fromminutes'] = $fromminutes;
        $data['fromseconds'] = $fromseconds;
        $data['tohours'] = $tohours;
        $data['tominutes'] = $tominutes;
        $data['toseconds'] = $toseconds;
        $data['fromampm'] = $fromampm;
        $data['toampm'] = $toampm;

        if($fromhours == '' && $fromminutes == '' && $fromseconds == '' && $tohours == '' && $tominutes == '' && $toseconds == ''){
            $fromhours = '00';
            $fromminutes = '00';
            $fromseconds = '00';
            $tohours = '00';
            $tominutes = '00';
            $toseconds = '00';
            $fromtime = $fromhours.':'.$fromminutes.':'.$fromseconds;
            $totime = $tohours.':'.$tominutes.':'.$toseconds;
        }else{
            $fromtime = $fromhours.':'.$fromminutes.':'.$fromseconds.' '.$fromampm;
            $totime = $tohours.':'.$tominutes.':'.$toseconds.' '.$toampm;
        }



        $data['fromtime'] = $fromtime;
        $data['totime'] = $totime;

        $empinfo = get_employee_info($hiddenempid);


        $holidays = array();

        $startTime = new DateTime($fromtime);
        $endTime = new DateTime($totime);
        $duration = $startTime->diff($endTime); //$duration is a DateInterval object

        $getsysid = $this->db->select("sysid")
            ->from("prime_employee_main_leave_credits")
            ->where(array("types"=>$selectleavetype , "status" => 1))
            ->get()->row();

        if($fromdate == ''){
            $fromdate = null;
        }
        if($todate == ''){
            $todate = null;

        }
        if($fromdate != '' && $todate != ''){
            $totaldays =  getWorkingDays($fromdate, $todate, $holidays);
            $totalinminutes = (($totaldays) * 24 * 60);
        }else{
            $totaldays = 0;
            $totalinminutes = 0;
        }

        $data['totaldays'] = $totaldays;
        $data['totalinminutes'] = $totalinminutes;
        $data['minutesonly'] = converttimetominutes($duration->format("%H:%I:%S"));
        $totalinminutes  = ($totalinminutes + converttimetominutes($duration->format("%H:%I:%S")));



        $this->db->trans_begin();
        $insrarr = array(
            'creditid' => $getsysid->sysid,
            'empid' => $hiddenempid,
            'from' => $fromdate,
            'to' => $todate,
            'fromtime' => ($fromtime) ? date("H:i:s", strtotime($fromtime)) : '',
            'totime' => ($totime) ? date("H:i:s", strtotime($totime)) : '',
            'totalinminutes' => $totalinminutes,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 301,
            // 'reason' => $reason,
            'leavetype' => $selectleavetype,
            'type' => $leavetype,
            'year' => $yearleave
        );
        $this->db->insert("trn_employee_leave_requests" , $insrarr);
        $data['errorleave']  = $this->db->_error_message();
        $lastid = $this->db->insert_id();


        /*  $qry_select_requests = $this->db->select()->from('trn_employee_leave_requests')
              ->where(array("empid" => $hiddenempid, "status" => 301))->get();
          if ($qry_select_requests->num_rows() > 0) {
              foreach ($qry_select_requests->result() as $row) { */
        $ins_arr = array(
            'trnreqid' => $lastid,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 301,
        );
        $this->db->insert('trn_employee_leave_requests_approval', $ins_arr);
        $data['leaverequestapprovalerr'] = $this->db->_error_message();
        //  }
        //    $flow_ins = create_transaction_trails('LEAVEAPP', 'LEAVE - ' . $emp_lastname, 92, $hiddenempid);
        // }



        if($this->db->trans_status() === TRUE ){
            $this->db->trans_commit();
            $msg = 'Leave form submitted';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Leave form failed to submit.';
            $func = 'error';
            $qry = false;
        }
        $data['empid'] = $hiddenempid;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    ///###########################################################################
    ///###########################################################################
    ///###########################################################################
    ///###########################################################################
    /// PER EMPLOYEE REQUESTS
    function add_leave_items() {
        $this->db->trans_begin();
        $data = array();

        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $leavetype = $this->input->post('leavetype');
        $remarks = $this->input->post('remarks');
        $leavecredit = $this->input->post('leavecredit');
        $datefrom = $this->input->post('datefrom');
        $dateend = ($this->input->post('dateend') != '') ? $this->input->post('dateend') != '' : $datefrom;
        $timestart = $this->input->post('timestart');
        $timeend = $this->input->post('timeend');



        $totalinminutes = 0;
        $totaltime = '';

        $getworkshift = $this->db->select("pemw.am_start , pemw.am_end , pemw.pm_start , pemw.pm_end , pemw.logcnt")
            ->from("prime_employee_main_workshift AS pemw")
            ->join("prime_employee_main_workshift_matrix AS pemwm" , "pemwm.workshift_id = pemw.sysid" , "left")
            ->where(array("pemwm.empid" => $empid , "pemwm.status" => 1))
            ->get()->row();
        $data['error'] = $this->db->_error_message();
        if($getworkshift){
            if($getworkshift->logcnt == 4){
                $amcutoff = $getworkshift->am_end;
                $pmcutoff = $getworkshift->pm_start;
            }else{
                $amcutoff = '12:00:00 PM';
                $pmcutoff = '1:00:00 PM';
            }

        }

        $holidays = array();


        $fromtime = $timestart;
        $totime = $timeend;

        if($datefrom == ''){
            $fromdate = null;
        }
        if($dateend == ''){
            $todate = null;
        }

        if($datefrom != '' && $dateend != ''){
            $weekcounter = 0;
            $datetime1 = new DateTime($datefrom);

            $datetime2 = new DateTime($dateend);
            $datetime2->modify('+1 day');
            //   $datetime2->format('Y-m-d H:i:s');

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($datetime1, $interval, $datetime2);

            foreach ($period as $dt) {

                $data['loopdates'][] = array(
                    'date' => $dt->format("l Y-m-d H:i:s\n")
                );
                /*   $difinicio=strtotime($fromdate.' 00:00:00');
                   $diffim=strtotime($todate.' 00:00:00');

                   if (date('w', strtotime($dt->format("l Y-m-d H:i:s"))))
                   {
                       $weekcounter++;
                   } */

                //CHECK IF WEEK END
                $date = $dt->format("Y-m-d");

                $this_date = date("l", strtotime($date));
                if ($this_date == "Saturday" || $this_date == "Sunday") {
                    $weekcounter++;
                }
            }


            //  $difinicio=  strtotime('+1 day',strtotime(date("Y",$difinicio).'-'.date("m",$difinicio).'-'.date("d",$difinicio).' 00:00:00'));
            $data['weekendcount'] = $weekcounter;
            $data['datetime1'] = $datetime1;
            $data['datetime2'] = $datetime2;

            // $difference = $datetime1->diff($datetime2);
            //  $totaldays =  $difference->d + 1;
            $totaldays = $datetime1->diff($datetime2)->format("%a");
            $data['totaldays'] = $totaldays;
            $totalinminutes = (($totaldays) * 8 * 60);
            $data['aftercomputed'] = $totalinminutes;

            $fromtime = $timestart;
            $totime = $timestart;
            $leavedate = null;
        }else{

            $startTime = new DateTime($fromtime);
            $amCutoffd = new DateTime($amcutoff);
            $endTime = new DateTime($totime);
            $pmCutoffd = new DateTime($pmcutoff);

            if($endTime > $startTime  && $amCutoffd <  $endTime && $startTime < $amCutoffd){
                $startampm = $startTime;
                $endammpm = $endTime;
                $data['startampm'] = $startampm;
                $data['endampm'] = $endammpm;

                $first = $startTime->diff($amCutoffd); //$duration is a DateInterval object
                $second = $endTime->diff($pmCutoffd);
                $totaltime =  converttimetominutes($first->format("%H:%I:%S")) + converttimetominutes($second->format("%H:%I:%S"));
                $data[] = 'Between';
            }else{
                $totaltime =   converttimetominutes($startTime->diff($endTime)->format("%H:%I:%S"));
                $data[] = 'Not Between';
            }
            $data['empty'] = 'empty';
        }

        if($datefrom == '' && $dateend == '') {
            $totalinminutes  = ($totaltime);
        }

        $insrarr = array(
            'empid' => $empid,
            'from' => $datefrom,
            'to' => $dateend,
            'fromtime' => ($fromtime) ? date("H:i:s", strtotime($fromtime)) : '',
            'totime' => ($totime) ? date("H:i:s", strtotime($totime)) : '',
            'totalinminutes' => $totalinminutes,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1,
            'leavetype' => $leavecredit,
            'type' => $leavetype,
            'year' => $year,
            'leavedate' => $datefrom,
            'status' => 300
        );
        $this->db->insert("trn_employee_leave_requests" , $insrarr);
        $data = db_trans($this->db);
        $data['errorleave']  = $this->db->_error_message();
        $data['input'] = $this->input->post();
        $data['empid'] = $empid;
        $data['year'] = $year;
        return json_encode($data);
    }

    function tbl_leave_items() {
        $data = array();
        $empid = $this->input->post('empid');

        $sql = $this->db->select("telr.sysid, pemlc.types ,telr.from , telr.to, telr.fromtime , telr.totime, ptp.names , telr.type")
            ->from("trn_employee_leave_requests AS telr")
            ->join("prime_types_parameter AS ptp" , "telr.status = ptp.sysid" , "left")
            ->join("prime_employee_main_leave_credits AS pemlc" , "pemlc.empid = telr.empid AND pemlc.types = telr.leavetype" , "left")
            ->where(array("telr.empid" => $empid, 'telr.status' => 300))
            ->group_by('telr.sysid, pemlc.types ,telr.`from`, telr.`to`, telr.fromtime , telr.totime, ptp.`names`, telr.type')
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            $type_arr = array('','RL','LL');
            foreach ($sql->result() as $row){
                $data['leaverequestedlist'][] = array(
                    'num' => $num++,
                    'leavetype' => get_types_label_format($row->types, false, false, false, false, false, true)->text,
                    'from' => $row->from,
                    'to' => $row->to,
                    'fromtime' => $row->fromtime,
                    'totime' => $row->totime,
                    'type' => $type_arr[$row->type],
                    'status' => $row->names,
                    'control' => '<button id="deleteleavedraft" type="button" data-id="'.$row->sysid.'" class="btn btn-xs btn-danger inline"><i class="fa fa-trash"></i></button>'
                );
            }
        }
        return json_encode($data);
    }

    ///###########################################################################
    ///###########################################################################
    ///###########################################################################


    function fetch_leave_requested() {
        $data = array();
        $empid = $this->input->post('empid');
        $data_id = $this->input->post('data_id');

        $sql = $this->db->select("pemlc.types ,telr.from , telr.to  , ptp.names")
            ->from("trn_employee_leave_requests AS telr")
            ->join("prime_types_parameter AS ptp" , "telr.status = ptp.sysid" , "left")
            ->join("prime_employee_main_leave_credits AS pemlc" , "pemlc.empid = telr.empid AND pemlc.types = telr.leavetype" , "left")
            ->where(array("telr.empid" => $empid , "telr.status" => 301, "telr.groupid" => $data_id))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['leaverequestedlist'][] = array(
                    'num' => $num++,
                    'leavetype' => get_types_label_format($row->types, false, false, false, false, false, true)->text,
                    'from' => $row->from,
                    'to' => $row->to,
                    'status' => $row->names
                );
            }
        }
        return json_encode($data);
    }


    // PERSONAL REQUEST
    function submit_leave_request() {

        $data = array();
        $msg = '';
        $func = '';
        $qry = false;

        $dataid = $this->input->post('dataid');
        $empid = $this->input->post('empid');
        $moduleid = $this->input->post('moduleid');
        $empinfo = get_employee_info($empid);


        if($empinfo) {
            $emp_lastname = $empinfo->lastname;

            $updatearr = array(
                'status'=>300
            );
            $this->db->where(array("status" => 307));
            $this->db->update("trn_employee_leave_requests" , $updatearr);

            // GROUP LIST OF REQUEST
            $flow_ins = false;
            $qry_select_requests = $this->db->select()->from('trn_employee_leave_requests')
                ->where(array("empid" => $empid, "status" => 300))->get();
            if ($qry_select_requests->num_rows() > 0) {
                foreach ($qry_select_requests->result() as $row) {
                    $ins_arr = array(
                        'trnreqid' => $row->sysid,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('trn_employee_leave_requests_approval', $ins_arr);
                    $ins_err = $this->db->_error_message();
                    $data['inserr'][] = $ins_err;
                }
                $flow_ins = create_transaction_trails('LEAVEAPP', 'LEAVE - ' . $emp_lastname, $moduleid, $dataid);
            }


            if ($this->db->trans_status() == TRUE && $flow_ins) {
                $this->db->trans_commit();
                $msg = 'Leave request has been submitted';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Failed to submit leave requested';
                $func = 'error';
                $qry = false;
            }
        }else{
            $qry = false;
            $msg = 'Employee info not found!';
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function fetch_pending_leave_requested() {
        $data = array();
        $holidays = array();
        $empid = $this->input->post('empid');
        $stat = $this->input->post('stat');
        $totalnohours = 0;
        $totalnodays = 0;
        $totalmins = 0;
        $mins = 0;

        $sql = $this->db->select("pemlc.types,telr.sysid,telr.from,telr.to, telr.status, telr.totalinminutes")
            ->from("trn_employee_leave_requests AS telr")
            ->join("prime_types_parameter AS tp" , "telr.status = tp.sysid" , "left")
            ->join("prime_employee_main_leave_credits AS pemlc" , "pemlc.empid = telr.empid AND pemlc.types = telr.leavetype" , "left")
            ->where(array("telr.status"=> $stat, 'telr.groupid' => $empid))
            ->get();

        $query = $this->db->last_query();

        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){

                $mins += $row->totalinminutes;

                $holidays = array();
                if($row->from != '' && $row->to != ''){
                    $totalnodays +=  getWorkingDays($row->from, $row->to, $holidays);
                }

                $control = '';

                if($row->status==307) {
                    $btn_type_value = 303;
                }else{
                    $btn_type_value = 302;
                }

                $del_btn = '<button id="del_btn" data-type-value="'.$btn_type_value.'" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>';

                $status = get_types_label_format($row->status);

                if($row->status==300) {
                    $control =  $del_btn;
                }
                if($row->status==307) {
                    $control = '<div class="btn-group">'.$del_btn.' </div>';
                }

                $data['pendingleaverequestedlist'][] = array(
                    'num' => $num++,
                    'leavetype' => get_types_label_format($row->types, false, false, false, false, false, true)->text,
                    'fromdate' => $row->from,
                    'todate' => $row->to,
                    'hours' => ($row->totalinminutes)/60,
                    'status' => $status,
                    'control' => $control
                );
            }
        }
        $totalnohours = $mins/60;
        //$totalmins = $mins - ($totalnohours);
        $data['totalnodays'] = $totalnodays;
        $data['totalnohours'] = $totalnohours;
        //$data['totalmins'] = $totalmins;
        $data['empid'] = $empid;
        $data['stats'] = $stat;
        $data['query'] = $query;
        return json_encode($data);
    }

    function delete_pending_leave_request() {
        $data = array();
        $msg = '';
        $func = '';
        $qry = false;

        $dataid = $this->input->post('dataid');
        $status = $this->input->post('status');
        $this->db->trans_begin();
        $updatearr = array(
            "status" => $status
        );
        $this->db->where(array("sysid" => $dataid));
        $this->db->update("trn_employee_leave_requests" , $updatearr);

        if($this->db->trans_status() == TRUE){
            $this->db->trans_commit();
            $msg = 'Leave request removed';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Error deleting request';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function approve_leave_request() {
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();

        $this->db->set('telr.status', '301');
        $this->db->set('telra.status', '301');

        $this->db->where('telr.empid', $dataid);
        $this->db->where('telr.status', '300');
        $this->db->where('telr.groupid = telra.sysid');
        $this->db->update('trn_employee_leave_requests as telr, trn_employee_leave_requests_approval as telra');
        $data['qryerr'] = $this->db->_error_message();
        if($this->db->trans_status() == TRUE){
            $this->db->trans_commit();
            $msg = 'All request has been approved';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->roll_back();
            $msg = 'Failed to approved request';
            $func = 'error';
            $qry = false;
        }


        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function disapprove_leave_request() {
        $data = array();
        $dataid = $this->input->post('dataid');
        $msg = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();
        $this->db->set('telr.status', '302');
        $this->db->set('telra.status', '302');
        $this->db->where('telr.empid', $dataid);
        $this->db->where('telr.status', '300');
        $this->db->where('telr.sysid = telra.trnreqid');
        $this->db->update('trn_employee_leave_requests as telr, trn_employee_leave_requests_approval as telra');
        $data['qryerr'] = $this->db->_error_message();
        if($this->db->trans_status() == TRUE){
            $this->db->trans_commit();
            $msg = 'All request has been disapproved';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->roll_back();
            $msg = 'Failed to disapproved request';
            $func = 'error';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function draft_leave_request() {
        $data = array();

        $hiddenempid = $this->input->post('hiddenempid');
        $leavetype = $this->input->post('leavetype');
        $hiddenleavedays = $this->input->post('hiddenleavedays');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $selectleavetype = $this->input->post('selectleavetype');
        $userid = $this->input->post('userloggedin');
        //  $reason = $this->input->post('reason');
        $moduleid = $this->input->post('moduleid');
        $yearleave = $this->input->post('yearleave');

        $fromhours = $this->input->post('fromhours');
        $fromminutes = $this->input->post('fromminutes');
        $fromseconds = $this->input->post('fromseconds');
        $fromampm = $this->input->post('fromampm');

        $tohours = $this->input->post('tohours');
        $tominutes = $this->input->post('tominutes');
        $toseconds = $this->input->post('toseconds');
        $toampm = $this->input->post('toampm');
        $leavedate = $this->input->post('leavedate');
        $remarks = $this->input->post('remarks');
        $totaltime = '';

        $getworkshift = $this->db->select("pemw.am_start , pemw.am_end , pemw.pm_start , pemw.pm_end , pemw.logcnt")
            ->from("prime_employee_main_workshift AS pemw")
            ->join("prime_employee_main_workshift_matrix AS pemwm" , "pemwm.workshift_id = pemw.sysid" , "left")
            ->where(array("pemwm.empid" => $hiddenempid , "pemwm.status" => 1))
            ->get()->row();
        $data['error'] = $this->db->_error_message();
        if($getworkshift){
            if($getworkshift->logcnt == 4){
                $amcutoff = $getworkshift->am_end;
                $pmcutoff = $getworkshift->pm_start;
            }else{
                $amcutoff = '12:00:00 PM';
                $pmcutoff = '1:00:00 PM';
            }

        }

        if(strlen($fromhours) == 1){
            $fromhours =   str_pad($fromhours,2,"0",STR_PAD_LEFT);
        }
        if(strlen($fromminutes) == 1){
            $fromminutes =   str_pad($fromminutes,2,"0",STR_PAD_LEFT);
        }
        if(strlen($fromseconds) == 1){
            $fromseconds =   str_pad($fromseconds,2,"0",STR_PAD_LEFT);
        }
        if(strlen($tohours) == 1){
            $tohours =   str_pad($tohours,2,"0",STR_PAD_LEFT);
        }
        if(strlen($tominutes) == 1){
            $tominutes =   str_pad($tominutes,2,"0",STR_PAD_LEFT);
        }
        if(strlen($toseconds) == 1){
            $toseconds =   str_pad($toseconds,2,"0",STR_PAD_LEFT);
        }


        if($fromhours == '' && $fromminutes == '' && $fromseconds == '' && $tohours == '' && $tominutes == '' && $toseconds == ''){
            $fromhours = '00';
            $fromminutes = '00';
            $fromseconds = '00';
            $tohours = '00';
            $tominutes = '00';
            $toseconds = '00';
            $fromtime = $fromhours.':'.$fromminutes.':'.$fromseconds;
            $totime = $tohours.':'.$tominutes.':'.$toseconds;

        }else{
            $fromtime = $fromhours.':'.$fromminutes.':'.$fromseconds.' '.$fromampm;
            $totime = $tohours.':'.$tominutes.':'.$toseconds.' '.$toampm;
        }

        $holidays = array();


        if($fromdate == ''){
            $fromdate = null;
        }
        if($todate == ''){
            $todate = null;

        }
        if($fromdate != '' && $todate != ''){
            $weekcounter = 0;
            $datetime1 = new DateTime($fromdate);

            $datetime2 = new DateTime($todate);
            $datetime2->modify('+1 day');
            //   $datetime2->format('Y-m-d H:i:s');

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($datetime1, $interval, $datetime2);

            foreach ($period as $dt) {

                $data['loopdates'][] = array(
                    'date' => $dt->format("l Y-m-d H:i:s\n")
                );
                /*   $difinicio=strtotime($fromdate.' 00:00:00');
                   $diffim=strtotime($todate.' 00:00:00');

                   if (date('w', strtotime($dt->format("l Y-m-d H:i:s"))))
                   {
                       $weekcounter++;
                   } */

                //CHECK IF WEEK END
                $date = $dt->format("Y-m-d");

                $this_date = date("l", strtotime($date));
                if ($this_date == "Saturday" || $this_date == "Sunday") {
                    $weekcounter++;
                }



            }


            //  $difinicio=  strtotime('+1 day',strtotime(date("Y",$difinicio).'-'.date("m",$difinicio).'-'.date("d",$difinicio).' 00:00:00'));
            $data['weekendcount'] = $weekcounter;
            $data['datetime1'] = $datetime1;
            $data['datetime2'] = $datetime2;

            // $difference = $datetime1->diff($datetime2);
            //  $totaldays =  $difference->d + 1;
            $totaldays = $datetime1->diff($datetime2)->format("%a");
            $data['totaldays'] = $totaldays;
            $totalinminutes = (($totaldays) * 8 * 60);
            $data['aftercomputed'] = $totalinminutes;


            $fromhours = '00';
            $fromminutes = '00';
            $fromseconds = '00';
            $tohours = '00';
            $tominutes = '00';
            $toseconds = '00';
            $fromtime = $fromhours.':'.$fromminutes.':'.$fromseconds;
            $totime = $tohours.':'.$tominutes.':'.$toseconds;

            $leavedate = null;
        }else{

            $startTime = new DateTime($fromtime);
            $amCutoffd = new DateTime($amcutoff);
            $endTime = new DateTime($totime);
            $pmCutoffd = new DateTime($pmcutoff);



            if($endTime > $startTime  && $amCutoffd <  $endTime && $startTime < $amCutoffd){
                $startampm = $startTime;
                $endammpm = $endTime;
                $data['startampm'] = $startampm;
                $data['endampm'] = $endammpm;

                $first = $startTime->diff($amCutoffd); //$duration is a DateInterval object
                $second = $endTime->diff($pmCutoffd);
                $totaltime =  converttimetominutes($first->format("%H:%I:%S")) + converttimetominutes($second->format("%H:%I:%S"));
                $data[] = 'Between';
            }else{
                $totaltime =   converttimetominutes($startTime->diff($endTime)->format("%H:%I:%S"));
                $data[] = 'Not Between';
            }

            $data['empty'] = 'empty';

        }


        if($fromdate == '' && $todate == '') {
            $totalinminutes  = ($totaltime);
        }


        $this->db->trans_begin();
        $insrarr = array(
            'empid' => $hiddenempid,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'fromtime' => ($fromtime) ? date("H:i:s", strtotime($fromtime)) : '',
            'totime' => ($totime) ? date("H:i:s", strtotime($totime)) : '',
            'totalinminutes' => $totalinminutes,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1,
            'leavetype' => $selectleavetype,
            'type' => $leavetype,
            'year' => $yearleave,
            'leavedate' => $leavedate
        );
        $this->db->insert("trn_employee_leave_draft_request" , $insrarr);
        $data['errorleave']  = $this->db->_error_message();

        if($this->db->trans_status() === TRUE ){
            $this->db->trans_commit();
            $msg = 'Leave form submitted';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Leave form failed to submit.';
            $func = 'error';
            $qry = false;
        }
        $data['empid'] = $hiddenempid;
        $data['year'] = $yearleave;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function add_employee_leave_draft() {
        $this->db->trans_begin();
        $data = array();

        $empid = $this->input->post('empid');
        $year = $this->input->post('year');
        $leavetype = $this->input->post('leavetype');
        $remarks = $this->input->post('remarks');
        $leavecredit = $this->input->post('leavecredit');
        $datefrom = $this->input->post('datefrom');
        $dateend = $this->input->post('dateend');
        $timestart = $this->input->post('timestart');
        $timeend = $this->input->post('timeend');

        $totalinminutes = 0;
        $totaltime = '';

        $getworkshift = $this->db->select("pemw.am_start , pemw.am_end , pemw.pm_start , pemw.pm_end , pemw.logcnt")
            ->from("prime_employee_main_workshift AS pemw")
            ->join("prime_employee_main_workshift_matrix AS pemwm" , "pemwm.workshift_id = pemw.sysid" , "left")
            ->where(array("pemwm.empid" => $empid , "pemwm.status" => 1))
            ->get()->row();
        $data['error'] = $this->db->_error_message();
        if($getworkshift){
            if($getworkshift->logcnt == 4){
                $amcutoff = $getworkshift->am_end;
                $pmcutoff = $getworkshift->pm_start;
            }else{
                $amcutoff = '12:00:00 PM';
                $pmcutoff = '1:00:00 PM';
            }

        }

        $holidays = array();


        $fromtime = $timestart;
        $totime = $timeend;

        if($datefrom == ''){
            $fromdate = null;
        }
        if($dateend == ''){
            $todate = null;
        }

        if($datefrom != '' && $dateend != ''){
            $weekcounter = 0;
            $datetime1 = new DateTime($datefrom);

            $datetime2 = new DateTime($dateend);
            $datetime2->modify('+1 day');
            //   $datetime2->format('Y-m-d H:i:s');

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($datetime1, $interval, $datetime2);

            foreach ($period as $dt) {

                $data['loopdates'][] = array(
                    'date' => $dt->format("l Y-m-d H:i:s\n")
                );
                /*   $difinicio=strtotime($fromdate.' 00:00:00');
                   $diffim=strtotime($todate.' 00:00:00');

                   if (date('w', strtotime($dt->format("l Y-m-d H:i:s"))))
                   {
                       $weekcounter++;
                   } */

                //CHECK IF WEEK END
                $date = $dt->format("Y-m-d");

                $this_date = date("l", strtotime($date));
                if ($this_date == "Saturday" || $this_date == "Sunday") {
                    $weekcounter++;
                }
            }


            //  $difinicio=  strtotime('+1 day',strtotime(date("Y",$difinicio).'-'.date("m",$difinicio).'-'.date("d",$difinicio).' 00:00:00'));
            $data['weekendcount'] = $weekcounter;
            $data['datetime1'] = $datetime1;
            $data['datetime2'] = $datetime2;

            // $difference = $datetime1->diff($datetime2);
            //  $totaldays =  $difference->d + 1;
            $totaldays = $datetime1->diff($datetime2)->format("%a");
            $data['totaldays'] = $totaldays;
            $totalinminutes = (($totaldays) * 8 * 60);
            $data['aftercomputed'] = $totalinminutes;

            $fromtime = $timestart;
            $totime = $timestart;
            $leavedate = null;
        }else{

            $startTime = new DateTime($fromtime);
            $amCutoffd = new DateTime($amcutoff);
            $endTime = new DateTime($totime);
            $pmCutoffd = new DateTime($pmcutoff);

            if($endTime > $startTime  && $amCutoffd <  $endTime && $startTime < $amCutoffd){
                $startampm = $startTime;
                $endammpm = $endTime;
                $data['startampm'] = $startampm;
                $data['endampm'] = $endammpm;

                $first = $startTime->diff($amCutoffd); //$duration is a DateInterval object
                $second = $endTime->diff($pmCutoffd);
                $totaltime =  converttimetominutes($first->format("%H:%I:%S")) + converttimetominutes($second->format("%H:%I:%S"));
                $data[] = 'Between';
            }else{
                $totaltime =   converttimetominutes($startTime->diff($endTime)->format("%H:%I:%S"));
                $data[] = 'Not Between';
            }
            $data['empty'] = 'empty';
        }

        if($datefrom == '' && $dateend == '') {
            $totalinminutes  = ($totaltime);
        }

        $insrarr = array(
            'empid' => $empid,
            'fromdate' => $datefrom,
            'todate' => $dateend,
            'fromtime' => ($fromtime) ? date("H:i:s", strtotime($fromtime)) : '',
            'totime' => ($totime) ? date("H:i:s", strtotime($totime)) : '',
            'totalinminutes' => $totalinminutes,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 1,
            'leavetype' => $leavecredit,
            'type' => $leavetype,
            'year' => $year,
            'leavedate' => $datefrom
        );
        $this->db->insert("trn_employee_leave_draft_request" , $insrarr);
        $data = db_trans($this->db);

        $data['errorleave']  = $this->db->_error_message();
        $data['input'] = $this->input->post();
        $data['empid'] = $empid;
        $data['year'] = $year;

        return json_encode($data);
    }
}