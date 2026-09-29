<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// ############################################
// AUTHOR : LUCKY JOHN FADERON - SE
Class Model_mrd extends CI_Model {

    function get_list_findings() {
        $qry = $this->db->select()->get('meter_reading_findings');
        return ($qry) ? $qry->result() : false;
    }

    function get_sample_reading() {
        $this->datatables->select('p.sysid, addr.addrspec AS address');
        $this->datatables->select("CONCAT(p.lastname,', ', p.firstname, ' ', p.middlename) as name", false);
        $this->datatables->add_column('expand', '$1', 'btn_expand(sysid)');
        $this->datatables->add_column('servno', 'M00001');
        $this->datatables->add_column('mtr', '1');
        $this->datatables->add_column('mtrserial', '585565');
        $this->datatables->add_column('readinput', '$1', "tbl_input('inline', ' ', 'Reading..', true, 'fa-pencil')");
        $this->datatables->add_column('findings', '$1', "fn_meter_findings(sysid)");
        $this->datatables->add_column('control', '<input placeholder="Select.." class="form-control input-xs inline" id="findings_desc" disabled style="width:100%" />');
        $this->datatables->from("person p");
        $this->datatables->join("person_address_matrix addr", 'addr.personid = p.sysid', 'left');
        $this->datatables->where("p.sysid >= ", 10);
        return $this->datatables->generate();
    }

    function get_reading_history() {
        $data = array();
        $userid = user_session()->system_user_sessid;
        $msysid = $this->input->post('id');
        // READING CURRENT
        $qry_reading_current = $this->db->select("h.sysid AS readid, h.createdby, sm.rmonth, sm.ryear")
            ->from('trn_reading_history AS h')
            ->join('reading_schedule_main AS sm', 'sm.sysid = h.schedid')
            ->where(array('h.mtrid' => $msysid))->get()->row();
        $data['billperiod'] = ($qry_reading_current) ? months_short($qry_reading_current->rmonth) . '-' . $qry_reading_current->ryear : '';
        $data['reader'] = ($qry_reading_current) ? get_user_person($qry_reading_current->createdby)->lastname . ', ' . get_user_person($qry_reading_current->createdby)->firstname : 'N/A';
        $data['readingid'] = ($qry_reading_current) ? $qry_reading_current->readid : 0;

        if ($qry_reading_current) {
            // GET METER PICS
            $qry_mtr_pic = $this->db->select()->from('trn_reading_history_pic')->where(array('readingid' => $qry_reading_current->readid, 'status' => 1))->order_by('datecreated', 'desc')->get()->row();
            $data['mtrpic'] = ($qry_mtr_pic) ? base_url() . 'uploads/reading/meter/' . $qry_mtr_pic->readingpic : base_url('assets/global/img/not-available.png');
            $data['picby'] = ($qry_mtr_pic) ? get_user_person($qry_mtr_pic->createdby)->lastname . ', ' . get_user_person($qry_mtr_pic->createdby)->firstname : 'N/A';
            $data['picdate'] = ($qry_mtr_pic) ? $qry_mtr_pic->datecreated : 'N/A';
        } else {
            $data['mtrpic'] = base_url('assets/global/img/not-available.png');
            $data['picby'] = 'N/A';
            $data['picdate'] = 'N/A';
        }
        // GET ACCOUNT INFO
        $qry_account_info = $this->db->select('moh.ownerid AS OWNERID, moh.ownertype AS OWNERTYPE, asm.mtrno AS MTRNO, am.serialcodes AS ASSETSERIAL, am.desc AS ASSETDESC, moh.datecreated AS ASSETACQ')
            ->from('customer_accounts_subscription_meter AS asm')
            ->join('assets_main_owner_history AS moh', 'moh.assetid = asm.assetid', 'left')
            ->join('assets_main AS am', 'am.sysid = moh.assetid')
            ->where(array('moh.status' => 1, 'asm.sysid' => $msysid))
            ->get()->row();
        $owner_info = $this->model_query->get_owner_info($qry_account_info->OWNERID);
        $data['street'] = $owner_info->STREET;
        $data['ownerdist'] = (acct_gdlb($qry_account_info->OWNERID)) ? acct_gdlb($qry_account_info->OWNERID)->DISTNAME : 'N/A';
        $data['gdlb'] = (acct_gdlb($qry_account_info->OWNERID)) ? acct_gdlb($qry_account_info->OWNERID)->GDLB : 'N/A';
        $data['lat'] = $owner_info->LAT;
        $data['lon'] = $owner_info->LON;
        $data['acctid'] = $qry_account_info->OWNERID;
        $data['mtrid'] = $msysid;
        $data['mtrno'] = $qry_account_info->MTRNO;
        $data['assetserial'] = $qry_account_info->ASSETSERIAL;
        $data['assetdesc'] = $qry_account_info->ASSETDESC;
        $data['assetacq'] = $qry_account_info->ASSETACQ;
        $qry_owner_info = false;
        $ownername = '';
        if ($qry_account_info->OWNERTYPE == 91) {
            $qry_owner_info = $this->db->select()->from('person AS p')
                ->join('customer_accounts_owners AS ao', 'ao.ownerid = p.sysid')
                ->where('ao.sysid', $qry_account_info->OWNERID)
                ->get()->row();
            $ownername = $qry_owner_info->lastname . ', ' . $qry_owner_info->firstname;
        }
        if ($qry_account_info->OWNERTYPE == 92) {
            $qry_owner_info = $this->db->select()->from('corporation AS p')
                ->join('customer_accounts_owners AS ao', 'ao.ownerid = p.sysid')
                ->where('ao.sysid', $qry_account_info->OWNERID)
                ->get()->row();
            $ownername = $qry_owner_info->descs;
        }

        if ($qry_account_info && $qry_owner_info) {
            $data['ownername'] = $ownername;
        }
        // READING HISTORY
        $qry_reading_history = $this->db->select('rh.schedid AS SCHEDID, rh.readings AS READING, rh.type AS RTYPE')
            ->from('trn_reading_history AS rh')
            ->where(array('rh.mtrid' => $msysid))
            ->order_by('rh.datecreated', 'desc')
            ->get();

        if ($qry_reading_history->num_rows() > 0) {
            foreach ($qry_reading_history->result() as $row) {
                if ($row->RTYPE == 4) {
                    $period = 'INITIAL';
                } else {
                    $qry_sched_main = $this->db->select()->from('reading_schedule_main')->where(array('sysid' => $row->SCHEDID))->get()->row();
                    if ($qry_sched_main) {
                        $period = $qry_sched_main->ryear . '-' . months_short($qry_sched_main->rmonth);
                    } else {
                        $period = '';
                    }
                }
                $data['readhist'][] = array(
                    'period' => $period,
                    'reading' => $row->READING,
                );
            }
        }

        $data['qry'] = true;
        return json_encode($data);
    }

    function delete_findings_main() {
        $data = array();
        $qry = false;
        $id = $this->input->post('id');
        $this->db->trans_begin();
        $this->db->where('sysid', $id);
        $this->db->update('meter_reading_findings', array('status' => 0, 'updatedby' => user_id()));
        if($this->db->trans_status() == true) {
            $qry = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }
        $data['qry'] = $qry;
        return json_encode($data);
    }


    function get_finding_datatable() {
        $data = array();

        $qry = $this->db->query("
            SELECT * FROM 
            meter_reading_findings 
            WHERE status = 1
            ORDER BY sysid
            ");

        if($qry->num_rows() > 0) {
            foreach($qry->result() as $row) {
                $printrecheck = ($row->printrecheck) ? 'checked' : '';

                $controls = '';
                $controls .= '<a href="javascript:;" class="btn btn-warning btn-xs inline" id="btn_edit" data-id="'.$row->sysid.'" class="pull-left" /><i class="fa fa-edit"></i></a>';
                $controls .= '<a href="javascript:;" class="btn btn-danger btn-xs inline" id="btn_delete" data-id="'.$row->sysid.'" class="pull-left" /><i class="fa fa-times"></i></a>';

                $dept_ar = get_deptartment_info($row->deptid);
                $dept = ($row->deptid > 0 && $dept_ar) ? '<span class="label label-info">'.$dept_ar->names.'</span>' : '<code>Unknown</code>';
                $data['list'][] = array(
                    'sysid' => $row->sysid,
                    'codes' => $row->codes,
                    'descs' => $row->descriptions,
                    'deptid' => $dept,
                    'isrecheck' => '<input type="checkbox" '.$printrecheck.' class="icheck" id="icheck_input" value="'.$row->sysid.'" />',
                    'status' => row_status($row->status),
                    'controls' => $controls
                );
            }
        }


        return json_encode($data);
    }

    function get_findings_details_row() {
        $data = array();
        $q = false;
        $input_arr = $this->input->post();
        $qry = $this->db->select('sysid, codes, descriptions AS codes, deptid, printrecheck')
            ->from('meter_reading_findings')
            ->where($input_arr)
            ->get()->row();
        if($qry) {
            $data['codes'] = $qry->codes;
            $data['desc'] = $qry->desc;
            $data['deptid'] = $qry->deptid;
            $data['recheck'] = $qry->printrecheck;
            $q = true;
        }
        $data['qry'] = $q;
        return json_encode($data);
    }

    function add_findings_maintenance() {
        $data = array();
        $q = false;
        $codes = $this->input->post('codes');
        $descs = $this->input->post('descs');
        $dept = $this->input->post('dept');
        $recheck = $this->input->post('recheck');
        $printrecheck = ($recheck) ? 1 : 0;

        $msg = 'Error PHP';
        $func = 'error';

        $this->db->trans_begin();

        $ins_arr = array(
            'codes' => $codes,
            'descriptions' => $descs,
            'deptid' => $dept,
            'printrecheck' => $printrecheck
        );
        $this->db->insert('meter_reading_findings', $ins_arr);
        $data['err'] = $this->db->_error_message();
        if($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $q = true;
            $msg = 'Success: Updated!';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $q;
        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

    function update_findings_isrecheck() {
        $data = array();
        $id = $this->input->post('id');
        $checked = $this->input->post('checked');
        $printrecheck = ($checked == 'true') ? 1 : 0;

        $msg = 'Error PHP';
        $func = 'error';

        $this->db->trans_begin();
        $this->db->where(array('sysid' => $id));
        $this->db->update('meter_reading_findings', array('printrecheck' => $printrecheck, 'updatedby' => user_id()));

        if($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $data['qry'] = true;
            $msg = 'Success: Updated!';
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $data['qry'] = false;
            $msg = 'Failed: failed to update recheck print!';
            $func = 'warning';
        }
        $data['chk'] = $checked;
        $data['res'] = $printrecheck;
        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }

    function get_mtr_info() {
        $data = array();
        $qry = false;
        $msg = '';
        $id = $this->input->post('id');
        $schedid = $this->input->post('schedid');
        $type = $this->input->post('type');

        $qry = false;
        $html = '';
        $acct_info = get_active_account_info($id);
        $year = false;
        $month = false;

        $data['acctid'] = $id;


        $get_sched = $this->db->select('months, years')
            ->from('reading_schedule_main')
            ->where(array('sysid' => $schedid))
            ->get()->row();

        if($get_sched) {
            $month = $get_sched->months;
            $year = $get_sched->years;
            $qry = true;
        }else{
            $qry_billing_ext = $this->db->select('billmo, billyr')
                ->from('billing_reports_ext')
                ->where(array('servno' => $acct_info->servicenumber, 'mtr' => $acct_info->mtr))
                ->order_by('datecreated', 'desc')
                ->get()->row();
            if($qry_billing_ext) {
                $month = $qry_billing_ext->billmo;
                $year = $qry_billing_ext->billyr;
                $qry = true;
            }else{
                $get_sched_ = $this->db->select('r.months, r.years')
                    ->from('reading_schedule_main AS r')
                    ->join('customer_accounts_main AS am', 'am.gdlb = r.gdlbid')
                    ->where(array('am.sysid' => $id))
                    ->order_by('r.sysid', 'desc')
                    ->get()->row();
                if($get_sched_) {
                    $month = $get_sched_->months;
                    $year = $get_sched_->years;
                    $qry = true;
                }
            }

        }

        $data['year'] = $year;
        $data['month'] = $month;

        if($qry) {

            //$html .=  '<div class="col-md-3"><h5 class="text-info"><i class="fa fa-map-o fa-fw"></i> <b>Default Map</b><a id="btn_map_popup" href="#map-pop-up" style="margin-right: 0px;" type="button" class="btn btn-info btn-xs pull-right fancybox-fast-map"><i class="fa fa-search"></i></a></h5><div id="cust_gmap" class="gmaps" style="width: 100%; height: 180px"></div></div>';

            $qry_av = $this->db->query("
                SELECT month, year, prsrdg, prvrdg, prsdte, prvdte, kwhuse, batch 
                FROM billing_reports_main 
                WHERE acctid = $id AND batch != 'LATEBILL'
                ORDER BY prsdte DESC
                LIMIT 12
            ");


            $mrd_lastfindings = mrd_get_last_findings($id, $acct_info->mtrno);
            $last_findings = '';
            $last_findings_date = '';
            if($mrd_lastfindings->qry) {
                $last_findings = $mrd_lastfindings->code;
                $last_findings_date = $mrd_lastfindings->date;
            }

            // READING HISTORY TABLE
            $reading_hist_row = '';
            $kwh_av = 0;
            $kwh_total = 0;
            $kwh_cnt = 0;
            $kwh_num = $qry_av->num_rows();
            if ($kwh_num > 0) {
                foreach ($qry_av->result() as $row) {
                    $prsdte = new DateTime($row->prsdte);
                    $prvdte = new DateTime($row->prvdte);
                    $prsdte_format = $prsdte->format('Y/m/d');
                    $prvdte_format = $prvdte->format('Y/m/d');
                    $period = $prvdte_format . '-' . $prsdte_format;

                    $reading_hist_row .= '<tr>';
                    $reading_hist_row .= '<td>' . $period . '</td>';
                    $reading_hist_row .= '<td>' . $row->prvrdg . '</td>';
                    $reading_hist_row .= '<td>' . $row->prsrdg . '</td>';
                    $reading_hist_row .= '<td>' . number_format($row->kwhuse) . '</td>';
                    $reading_hist_row .= '<td>' . $row->batch . '</td>';
                    $reading_hist_row .= '</tr>';
                    if($row->kwhuse > 0) {
                        $kwh_cnt += 1;
                        $kwh_total += $row->kwhuse;
                    }
                }
                if($kwh_cnt) {
                    $kwh_av = $kwh_total / $kwh_cnt;
                } else {
                    $kwh_av = $kwh_total;
                }
            }

            $html .= '<div class="col-md-12">';

            // INFORMATION
            $gdlb = get_acct_gdlb($id);
            $html .= '<div class="col-md-3">';
            $html .= '<h5 class="text-info"><i class="fa fa-map-marker fa-fw"></i> <b>Location Details</b></h5>';
            $html .= '<ul class="list-group summary column border-top list-group-xs">';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Brgy / Streen Name</span><span class="col-md-7 label-default data">' . $gdlb->ADDR . '</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">District</span><span class="col-md-7 label-default data">' . $gdlb->DISTNAME . '</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">GDLB</span><span class="col-md-7 label-default data">' . $gdlb->GDLB . '</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Map Updated: </span><span class="col-md-7 label-default data">2016-01-01</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Ready By</span><span class="col-md-7 label-default data"></span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Year</span><span class="col-md-7 label-default data">'.$year.'</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Month</span><span class="col-md-7 label-default data">'.date_formating($month, '!m', 'M').'</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Last Findings</span><span class="col-md-7 label-default data">'.$last_findings.'</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Finding\'s Date</span><span class="col-md-7 label-default data">'.$last_findings_date.'</span></li>';
            $html .= '<li class="list-group-item"><span class="col-md-5 label-name">Ave. Kwh: </span><span class="col-md-7 label-default data">' . number_format($kwh_av) . '</span></li>';
            $html .= '</ul>';
            $html .= '</div>';

            // FILE / IMAGE UPLOAD
            $html .= '<div class="col-md-4">';
            $html .= '<h5 class="text-info"><i class="fa fa-image fa-fw"></i> <b>Latest Photo</b</h5>';
            $html .= '<div class="well bg-color-white border-blue-sharp margin-top-10" style="min-height: 200px; border: 1px dashed; padding: 10px 10px;">';
            $html .= '<form id="frm_read_pic" action="' . base_url('assets/uploadassetpic') . '" method="post" enctype="multipart/form-data">';
            $html .= '<div class="input-group margin-bottom-10">';
            $html .= '<input class="form-control" type="hidden" name="mtrno" value="' . $acct_info->mtrno . '" />';
            $html .= '<input class="form-control" type="hidden" name="type" value="' . $type . '" />';
            $html .= '<input class="form-control" type="hidden" name="acctid" value="' . $id . '"  />';
            $html .= '<input class="form-control" type="hidden" name="year" value="' . $year . '"  />';
            $html .= '<input class="form-control" type="hidden" name="month" value="' . $month . '"  />';
            $html .= '<input class="form-control" type="file" name="pics[]" multiple />';
            $html .= '<span class="input-group-btn">';
            $html .= '<button type="reset" class="btn btn-default btn-icon-only"><i class="fa fa-refresh"></i></button>';
            $html .= '<button style="margin-left: -5px !important;" type="submit" class="btn btn-default"><i class="fa fa-upload"></i> Upload</button>';
            $html .= '</div>';
            $html .= '</form>';

            $html .= '<div class="meter-thumbnail-pics" id="mtr_pics">';

            $html .= '</div>';
            $html .= '</div>';

            $html .= '</div>';
            $html .= '<div class="col-md-4">';
            $html .= '<h5 class="text-info"><i class="fa fa-search fa-fw"></i> <b>Reading History</b</h5>';
            $html .= '<table class="table table-hover table-bordered table-condensed tbl-xs">';
            $html .= '<thead>';
            $html .= '<th>Period</th>';
            $html .= '<th>PRV RDG</th>';
            $html .= '<th>PRS RDG</th>';
            $html .= '<th>KWH</th>';
            $html .= '<th>Batch</th>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $html .= $reading_hist_row;
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '<div class="col-md-1 pull-right margin-top-20">';
            $html .= '<a href="javascript:;" id="btn_acct_print" data-servno="'.$acct_info->servicenumber.'" data-mtr="'.$acct_info->mtr.'" type="button" class="btn btn-info btn-block  btn-xs margin-bottom-10"><i class="fa fa-print"></i> Account</a>';
            $html .= '<a href="#account_map" title="Account Map" data-toggle="ajax-modal" data-arr="'.$id.'" type="button" class="btn btn-default btn-block  btn-xs margin-bottom-10"><i class="fa fa-print"></i> Map</a>';

            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';
        }else{
            $html = '<h4>Not found</h4>';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['html'] = $html;
        return json_encode($data);
    }

    function get_mtr_pics () {
        $data = array();
        $html = '';
        $mtrno          = $this->input->post('mtrno');
        $acctno         = $this->input->post('acctno');
        $year_input     = $this->input->post('year');
        $month_input    = $this->input->post('month');

        if($year_input && $month_input) {
            $month = $month_input;
            $year = $year_input;
        }else{
            // QUERY FROM EXTERNAL BILLING REPORTS
            $info = get_active_account_info($acctno);
            $qry_billing_ext = $this->db->select('billmo, billyr')
                ->from('billing_reports_ext')
                ->where(array('servno' => $info->servicenumber, 'mtr' => $info->mtr))
                ->order_by('datecreated', 'desc')
                ->get()->row();
            if($qry_billing_ext) {
                $month = $qry_billing_ext->billmo;
                $year = $qry_billing_ext->billyr;
            }
        }

        // GET FILE PICS
        $home_dir = str_pad($mtrno, 8, '0', STR_PAD_LEFT);
        $file_pic_arr = glob(FCPATH.'uploads/reading/meter/'.$home_dir.'/'.$year.'/'.$month.'/*.*');
        $admin_link = (user_id()==1) ? '<a href="#tbl_deleted_mtrpics" data-toggle="ajax-modal" data-arr="./uploads/reading/meter/'.$home_dir.'/'.$year.'/'.$month.'/deleted/*.*" class="text-danger" style="display: inline; width: auto; float: right;"><i class="fa fa-trash-o"></i> Deleted</a>' : '';
        $file_cnt = count($file_pic_arr);
        if($file_cnt>0) {
            $html .= '<p>Files attahced: ' . $file_cnt . ' '.$admin_link.'</p>';
            foreach($file_pic_arr as $mtr) {
                $pic_arr = explode('/', $mtr);
                $pic_name = end($pic_arr);
                $html .= '<div class="items">';
                $html .= '<span class="view-text bg-yellow-casablanca bg-font-yellow-casablanca">View</span>';
                $html .= '<button type="button" data-dir="'.$home_dir.'" data-acct="'.$acctno.'" data-file="'.$pic_name.'" data-year="'.$year.'" data-month="'.$month.'" class="btn btn-danger btn-xs" id="btn_delete"><i class="fa fa-times"></i></button>';
                $html .= '<a target="_blank" href="' . base_url('uploads/reading/meter/'.$home_dir.'/'.$year.'/'.$month.'/'.$pic_name) . '" class="fancybox-button">';
                $html .= '<img class="img-responsive" style="" src="' . base_url('uploads/reading/meter/'.$home_dir.'/'.$year.'/'.$month.'/'.$pic_name) . '">';
                $html .= '</a>';
                $html .= '</div>';
            }
        }else{
            $html .= '<p style="padding-right: 5px;">No file attached! '.$admin_link.'</p>';
        }
        $data['html'] = $html;
        return json_encode($data);
    }

    function delete_mtr_pic() {
        $data = array();
        $qry = false;
        $homedir = $this->input->post('homedir');
        $filename = $this->input->post('file');
        $year = $this->input->post('year');
        $month = $this->input->post('month');

        if(!file_exists(FCPATH . 'uploads/reading/meter/' . $homedir . '/'.$year.'/'.$month.'/deleted')) {
            mkdir(FCPATH . 'uploads/reading/meter/' . $homedir . '/'.$year.'/'.$month.'/deleted', 0777, true);
        }else{
            chmod(FCPATH . 'uploads/reading/meter/' . $homedir . '/'.$year.'/'.$month.'/deleted', 0777);
        }

        if (audit_db()) {
            if (rename(FCPATH . 'uploads/reading/meter/' . $homedir . '/' . $year . '/' . $month . '/' . $filename, FCPATH . 'uploads/reading/meter/' . $homedir . '/' . $year . '/' . $month . '/deleted/' . $filename)) {
                $qry = true;
                $audit_ins_arr = array(
                    'dataid' => $homedir,
                    'moduleid' => 0,
                    'valueold' => 0,
                    'valuenew' => 0,
                    'createdby' => user_id(),
                    'remarks' => 'Deleted: Meter Picture | ' . $filename
                );
                audit_insert($audit_ins_arr);
            }
        }
        $data['qry'] = $qry;
        return json_encode($data);
    }


    private function set_upload_options($full_path, $new_name)
    {
        //upload an image options
        $config = array();

        $config['upload_path'] = $full_path;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 50000;
        //$config['max_width'] = 4024;
        //$config['max_height'] = 3768;
        //$config['encrypt_name']         = TRUE;
        $config['file_name'] = $new_name;

        return $config;
    }


    function get_mrd_recheck() {
        $data = array();
        /*
        $userid_arr = explode(',', user_id());
        if(user_id() != 1) {
            $this->db->where_in('rr.userid', $userid_arr);
        }
        */
        $query = $this->db->select('rm.sysid, rm.datesched')
            ->select("CONCAT(G.g, '-', D.codes, '-', G.l, '-', G.b) AS GDLBNAME", false)
            ->from('trn_reading_analysis_logs AS gdlb')
            ->join('reading_schedule_main AS rm', 'rm.gdlbid = gdlb.gdlbid')
            ->join('reading_schedule_reader AS rr', 'rr.schedid = rm.sysid', 'left')
            ->join('gdlb_main AS G', 'G.sysid = gdlb.gdlbid')
            ->join('address_districts AS D', 'D.sysid = G.d')
            ->where('gdlb.types', 2)
            ->group_by('rm.sysid, gdlb.gdlbid, rm.datesched')
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->GDLBNAME . ' - ' . $row->datesched
                );
            }
        }
        return json_encode($data);
    }

    function get_gdlb_dist() {
        $data = array();
        $qry = $this->db->query("SELECT sysid, codes, names FROM address_districts WHERE types = 1 OR sysid = 9 OR sysid = 11");
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->codes . ' - ' . $row->names);
            }
        }
        return json_encode($data);
    }

    function get_mrd_schedule() {
        $data = array();
        $query = $this->db->select('rm.sysid, rm.datesched, lc.telcode, p.lastname')
            ->select("CONCAT(G.g, '-', D.codes, '-', G.l, '-', G.b) AS GDLBNAME", false)
            ->from('customer_accounts_glb AS gdlb')
            ->join('reading_schedule_main AS rm', 'rm.gdlbid = gdlb.gdlbid')
            ->join('reading_schedule_reader AS rr', 'rr.schedid = rm.sysid AND rr.status = 1')
            ->join('gdlb_main AS G', 'G.sysid = gdlb.gdlbid')
            ->join('address_districts AS D', 'D.sysid = G.d')
            ->join('prime_system_users_legacy_code AS lc', 'lc.userid = rr.userid', 'left')
            ->join('prime_system_users AS u', 'u.sysid = rr.userid')
            ->join('person AS p', 'p.sysid = u.personid', 'left')
            ->where(array('rm.status' => 1))
            ->group_by('rm.sysid, gdlb.gdlbid, rm.datesched, lc.telcode, p.lastname')
            ->get();

        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->GDLBNAME . ' - ' . $row->datesched . ' <b>' . $row->lastname . '</b>   - ' .  $row->telcode
                );
            }
        }
        return json_encode($data);
    }

    function get_ct_group() {
        $data = array();
        $query = $this->db->select('a.sysid, a.servicenumber, count(ams.acctid) AS cnt')
            ->from('customer_accounts_main AS a')
            ->join('customer_accounts_main_submatrix AS ams', 'ams.acctmainid = a.sysid')
            ->where(array('a.gdlb' => 33))
            ->group_by('a.sysid, a.servicenumber')
            ->get();

        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $cnt_sub = $this->db->select('COUNT(acctid) AS cnt')
                    ->from('customer_accounts_main_submatrix')
                    ->where(array('acctmainid' => $row->sysid, 'status' => 1))
                    ->get()->row();
                $cnt_html = '';
                if($cnt_sub && $cnt_sub->cnt > 0) {
                    $cnt_html = '<span class="label label-danger pull-right">'.$cnt_sub->cnt.'</span>';
                }
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->servicenumber . ' ' . $cnt_html
                );
            }
        }
        return json_encode($data);
    }

    function get_employee_readers() {
        $data = array();
        $qry_emp = $this->db->query("SELECT
                                                e.sysid AS sysid,
                                                p.sysid AS personid,
                                                p.lastname,
                                                p.firstname,
                                                p.middlename,
                                                c.datecreated 
                                            FROM
                                                person AS p
                                                JOIN prime_employee_main AS e ON e.personid = p.sysid 
                                                AND e.STATUS = 1
                                                JOIN prime_employee_costcenter AS c ON c.empid = e.sysid 
                                                AND c.STATUS = 1 
                                            WHERE
                                                c.ccid = 14 AND c.status = 1 AND c.type = 1
                                            GROUP BY
                                                e.sysid,
                                                p.sysid,
                                                p.lastname,
                                                p.firstname,
                                                p.middlename,
                                                c.datecreated 
                                            ORDER BY
                                                c.datecreated DESC
                                                        
                                        ");
        if($qry_emp->num_rows() > 0) {
            foreach($qry_emp->result() as $row) {
                $data['list'] = array('id' => $row->sysid, 'text' => $row->lastname);
            }
        }
        return json_encode($data);
    }

    function get_gdlb_tagging() {
        $data = array();
        $gdlb = $this->input->post('gdlbid');
        $query = $this->db->query("
                    SELECT
                    mr.mrseq AS MRSEQ,
                    acct.sysid AS SYSID,
                    acct.gdlb AS GDLB, 
                    acct.servicenumber AS SERVNO, 
                    acct.types AS OWNERTYPE, 
                    acct.ownerid AS OWNERID, 
                    acct.mtrno AS MTRNO,
                    acct.mtrserial AS MTRSER,
                    acct.mtr AS MTR,
                    aa.addrspecific AS addrspec
                    FROM customer_accounts_main AS acct
                    LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = acct.sysid AND mr.`status` = 1
                    LEFT JOIN customer_accounts_address AS aa ON acct.sysid = aa.acctid AND aa.`status` = 1
                    WHERE acct.gdlb = {$gdlb} AND acct.`status` = 1 
                      AND (
                          acct.sysid != 86656 OR 
                          acct.sysid != 7997 OR 
                          acct.sysid != 7998 OR 
                          acct.sysid != 7999
                        )
                    ORDER BY mr.mrseq              
              ");

        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            $i = 0;
            foreach ($query->result() as $row) {

                $i++;
                $owner_arr = get_ownership_details($row->OWNERTYPE, $row->OWNERID);
                $name = ($owner_arr) ? $owner_arr->name : '';
                $mrseq = ($row->MRSEQ != '') ? $row->MRSEQ : '';
                // GE SPECIFIC
                $tagging = '';
                $qry_specific = $this->db->select('ss.userid, ss.acctid, ulc.telcode')
                    ->from('reading_schedule_specific AS ss')
                    ->join('prime_system_users_legacy_code AS ulc', 'ulc.userid = ss.userid', 'left')
                    ->where(array('ss.acctid' => $row->SYSID, 'ss.status' => 1))
                    ->get()->row();

                $telcode = '';
                $controls = '';
                $readername = '';
                if ($qry_specific) {
                    $get_userinfo = get_users_info($qry_specific->userid);
                    $telcode = $qry_specific->telcode ;
                    $readername = '<code>' . $get_userinfo->lastname . '</code>';
                    $controls .= '<a href="javascript:;" id="btn_clear" data-userid="'.$qry_specific->userid.'" data-acctid="'.$row->SYSID.'" class="btn btn-danger btn-xs inline">Clear</a>';
                }
                $tagging .= '<input class="form-control inline" data-acctid="'.$row->SYSID.'" value="'.$telcode.'" placeholder="Reader" id="input_reader" />';

                $data['data'][] = array(
                    'seq' => $mrseq,
                    'serviceno' => $row->SERVNO,
                    'name' => $name,
                    'mtr' => $row->MTR,
                    'meterno' => $row->MTRNO,
                    'meterserial' => $row->MTRSER,
                    'address' => $row->addrspec,
                    'ownertype' => '',
                    'ownerid' => '',
                    'reader' => $tagging,
                    'readername' => $readername,
                    'control' => $controls
                );
            }
        }
        return json_encode($data);
    }

    function get_meter_tagging () {
        $data = array();

        $cols_arr = array();
        $cols_arr[] = array('data' => 'num', 'text' => '<span style="width: 15px; display: inline-block;">#</span>', 'sWidth' => '15px', 'sClass' => 'zui-sticky-col');
        $cols_arr[] = array('data' => 'servno', 'text' => '<span style="width: 70px; display: inline-block;">Servno</span>', 'sWidth' => '80px', 'sClass' => 'zui-sticky-col');
        $cols_arr[] = array('data' => 'name', 'text' => '<span style="width: 150px; display: inline-block;">Name</span>', 'sWidth' => '200px', 'sClass' => 'zui-sticky-col');
        $cols_arr[] = array('data' => 'mtr', 'text' => '<span style="width: 15px; display: inline-block;">MTR</span>', 'sWidth' => '15px', 'sClass' => 'zui-sticky-col');
        $cols_arr[] = array('data' => 'mtrno', 'text' => '<span style="width: 80px; display: inline-block;">MTR No.</span>', 'sWidth' => '40px', 'sClass' => 'zui-sticky-col');
        $cols_arr[] = array('data' => 'mtrser', 'text' => '<span style="width: 90px; display: inline-block;">MTR Serial</span>', 'sWidth' => '50px', 'sClass' => 'zui-sticky-col');

        $columns = array();
        $row_arr = $this->db->select('
                acct.sysid AS SYSID,
                acct.gdlb AS GDLBID,
                acct.servicenumber AS SERVNO,
                acct.types AS OWNERTYPE,
                acct.ownerid AS OWNERID,
                acct.mtr AS MTR,
                acct.mtrno AS MTRNO,
                acct.mtrserial AS MTRSER,
                acct.rateclassid AS RATEID,
                acct.netmtr AS NETMTR,
                ml.seq AS MRSEQ
            ')->from('customer_accounts_main AS acct')
            ->join('reading_schedule_meters_logs AS ml', 'ml.acctid = acct.sysid')
            ->where(array('acct.status' => 1))
            ->group_by('
                acct.servicenumber,
                acct.sysid,
                acct.gdlb,
                acct.sysid,
                acct.types,
                acct.ownerid,
                acct.mtr,
                acct.mtrno,
                acct.mtrserial,
                acct.rateclassid,
                acct.netmtr,
                ml.seq
             ')->get();

        $qry_emp = $this->db->query("
                    SELECT
                        e.sysid AS sysid,
                        p.sysid AS personid,
                        p.lastname,
                        p.firstname,
                        p.middlename,
                        c.datecreated 
                    FROM
                        person AS p
                        JOIN prime_employee_main AS e ON e.personid = p.sysid 
                        AND e.STATUS = 1
                        JOIN prime_employee_costcenter AS c ON c.empid = e.sysid 
                        AND c.STATUS = 1 
                    WHERE
                        c.ccid = 14 AND c.status = 1 AND c.type = 1
                    GROUP BY
                        e.sysid,
                        p.sysid,
                        p.lastname,
                        p.firstname,
                        p.middlename,
                        c.datecreated 
                    ORDER BY
                        p.lastname ASC,
                        c.datecreated DESC   
                ");

        if($qry_emp->num_rows() > 0) {
            foreach($qry_emp->result() as $k => $erow) {
                $cols_arr[] = array('data' => $k, 'text' => '<span style="width: 100px; display: inline-block;">'.$erow->lastname.'</span>', 'sWidth' => '200px', 'sClass' => 'dynamic');
            }
        }

        $num = 1;
        if($row_arr->num_rows() > 0) {
            foreach ($row_arr->result() as $keys => $row) {

                $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;
                $data['list'][] = array(
                    'num' => $num++,
                    'servno' => $row->SERVNO,
                    'name' => str_replace('ñ', 'n', strtolower($name)),
                    'mtr' => $row->MTR,
                    'mtrno' => $row->MTRNO,
                    'mtrser' => $row->MTRSER,
                );


                if ($qry_emp->num_rows() > 0) {
                    foreach ($qry_emp->result() as $key => $eerow) {
                        array_push($data['list'][$keys], '<label for="check_' . $eerow->personid .'"><input type="checkbox" id="check_' . $eerow->personid .'" data-id="'.$row->MTRNO.'" /></label>');
                    }
                }
            }
        }

        $data['columns'] = $cols_arr;

        return json_encode($data);
        exit();

        $cols_arr = array();
        $dynamic_cols = array();
        $qry_emp = $this->db->query("SELECT
                                        e.sysid AS sysid,
                                        p.sysid AS personid,
                                        p.lastname,
                                        p.firstname,
                                        p.middlename,
                                        c.datecreated 
                                    FROM
                                        person AS p
                                        JOIN prime_employee_main AS e ON e.personid = p.sysid 
                                        AND e.STATUS = 1
                                        JOIN prime_employee_costcenter AS c ON c.empid = e.sysid 
                                        AND c.STATUS = 1 
                                    WHERE
                                        c.ccid = 14 AND c.status = 1 AND c.type = 1
                                    GROUP BY
                                        e.sysid,
                                        p.sysid,
                                        p.lastname,
                                        p.firstname,
                                        p.middlename,
                                        c.datecreated 
                                    ORDER BY
                                        c.datecreated DESC
                                                
                                ");



        if($qry_emp->num_rows() > 0) {
            foreach($qry_emp->result() as $key => $erow) {
                $data['columns']['th'][] = array(
                    'id' => $erow->personid,
                );
            }
            $query = $this->db->select('
                acct.sysid AS SYSID,
                acct.gdlb AS GDLBID,
                acct.servicenumber AS SERVNO,
                acct.types AS OWNERTYPE,
                acct.ownerid AS OWNERID,
                acct.mtr AS MTR,
                acct.mtrno AS MTRNO,
                acct.mtrserial AS MTRSER,
                acct.rateclassid AS RATEID,
                acct.netmtr AS NETMTR,
                ml.seq AS MRSEQ
            ')->from('customer_accounts_main AS acct')
                ->join('reading_schedule_meters_logs AS ml', 'ml.acctid = acct.sysid')
                ->where(array('acct.status' => 1))
                ->group_by('
                acct.servicenumber,
                acct.sysid,
                acct.gdlb,
                acct.sysid,
                acct.types,
                acct.ownerid,
                acct.mtr,
                acct.mtrno,
                acct.mtrserial,
                acct.rateclassid,
                acct.netmtr,
                ml.seq
             ')->get();
            $num_rows = $query->num_rows();
            if ($num_rows > 0) {
                foreach ($query->result() as $keys => $row) {

                    $dynamic_cols = array();
                    $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;
                    $data['list'][] = array(
                        'servno' => $row->SERVNO,
                        'name' => str_replace('ñ', 'n', strtolower($name)),
                        'mtr' => $row->MTR,
                        'mtrno' => $row->MTRNO,
                        'mtrser' => $row->MTRSER,
                    );



                    foreach($qry_emp->result() as $key => $erow) {
                        $dynamic_cols[] = array(
                            'id' => $erow->personid
                        );
                        array_push($data['list'][$keys], $erow->personid);
                    }

                }
            }
        }

        return json_encode($data);
    }

    function query_reader_codeinfo() {
        $data = array();
        $qry = false;
        $name = '';
        $telcode = $this->input->post('telcode');
        $qry_telcode = $this->db->select('userid')
            ->from('prime_system_users_legacy_code')
            ->where(array('telcode' => $telcode))
            ->get()->row();
        if($qry_telcode) {
            $get_userinfo = get_users_info($qry_telcode->userid);
            if($get_userinfo) {
                $qry = true;
                $name = '<code>' . $get_userinfo->lastname . '</code>';

            }
        }
        $data['name'] = $name;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function clear_mtr_tagging_row() {

        $data = array();
        $acctid = $this->input->post('acctid');
        $userid = $this->input->post('userid');

        $this->db->trans_begin();

        $this->db->where(array('acctid' => $acctid, 'status' => 1, 'userid' => $userid));
        $this->db->update('reading_schedule_specific', array('status' => 0, 'updatedby' => user_id()));
        $err = $this->db->_error_message();


        if($this->db->trans_status() == true) {
            $qry = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
            $data['err'] = $err;
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }


    function save_mtr_tagging() {
        $data = array();
        $acctid = $this->input->post('acctid');
        $telcode = $this->input->post('telcode');
        $qry = false;
        $this->db->trans_begin();

        $qry_telcode = $this->db->select('userid')
            ->from('prime_system_users_legacy_code')
            ->where(array('telcode' => $telcode))
            ->get()->row();

        $err = '';
        if($qry_telcode && $telcode && $acctid) {
            // UPDATE FIRSTN
            $this->db->where(array('acctid' => $acctid, 'status' => 1));
            $this->db->update('reading_schedule_specific', array('status' => 0, 'updatedby' => user_id()));

            $ins = array('userid' => $qry_telcode->userid, 'acctid' => $acctid, 'createdby' => user_id(), 'updatedby' => user_id());
            $this->db->insert('reading_schedule_specific', $ins);
            $err = $this->db->_error_message();
        }
        $controls = '';
        if($this->db->trans_status() == true) {
            $qry = true;
            $this->db->trans_commit();
            $controls = '<a href="javascript:;" id="btn_clear" data-userid="'.$qry_telcode->userid.'" data-acctid="'.$acctid.'" class="btn btn-danger btn-xs inline">Clear</a>';

        }else{
            $this->db->trans_rollback();
            $data['err'] = $err;
        }

        $data['control'] = $controls;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_reading_entry() {
        $num_rows = 0;
        $read_num = 0;
        $type = $this->input->post('type');
        $userid = $this->input->post('userid');

        if(user_id()==0) {
            $data['msg'] = 'Session timeout!';
            $data['qry'] = false;
            $num_rows = 0;
        }else {
            $data = array();
            $schedid = $this->input->post('schedid');

            $get_sched_details = $this->db->select('sysid, gdlbid, months, years')
                ->from('reading_schedule_main')
                ->where('sysid', $schedid)
                ->get()->row();

            if ($get_sched_details) {
                $sched_gdlb = $get_sched_details->gdlbid;
                $sched_sysid = $get_sched_details->sysid;
                $data['gdlbid'] = $sched_gdlb;

                if(super_admin()) {
                    $where_ = "sm.sysid = $sched_sysid AND sm.`status` = 1 AND am.`status` = 1";
                }else{
                    $where_ = "sm.sysid = $sched_sysid AND ss.userid = $userid AND sm.`status` = 1 AND am.`status` = 1";
                }

                $qry_specific = $this->db->query(
                    "
                    SELECT 
                    mr.mrseq AS MRSEQ,
                    am.sysid AS SYSID,
                    am.netmtr AS NETMTR,
                    am.gdlb AS GDLB, 
                    am.servicenumber AS SERVNO, 
                    am.types AS OWNERTYPE, 
                    am.ownerid AS OWNERID, 
                    am.mtrno AS MTRNO,
                    am.mtrserial AS MTRSER,
                    am.rateclassid AS RATEID,
                    am.mtr AS MTR,
                    ulc.telcode AS TELCODE
                    FROM reading_schedule_main AS sm 
                    JOIN customer_accounts_main AS am ON sm.gdlbid = am.gdlb AND sm.`status` >= 1
                    JOIN reading_schedule_specific AS ss ON ss.acctid = am.sysid AND ss.`status` = 1
                    LEFT JOIN prime_system_users_legacy_code AS ulc ON ulc.userid = ss.userid
                    LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = am.sysid AND mr.`status` = 1
                    WHERE $where_
                    GROUP BY 
                    mr.mrseq,
                    am.sysid,
                    am.netmtr,
                    am.gdlb, 
                    am.servicenumber, 
                    am.types, 
                    am.ownerid, 
                    am.mtrno,
                    am.mtrserial,
                    am.rateclassid,
                    am.mtr,
                    sm.datecreated,
                    ulc.telcode
                    "
                );
                $spec_num_rows = $qry_specific->num_rows();
                if($spec_num_rows > 0) {

                    $data['query'] = 'SPECIFIC';
                    foreach ($qry_specific->result() as $row) {
                        $check_ct_gov = $this->db->select('acctmainid')
                            ->from('customer_accounts_main_submatrix')
                            ->where(array('acctmainid' => $row->SYSID, 'status' => 1))
                            ->get()->row();

                        $check_read_submitted = check_reading_submitted($row->SYSID, $row->MTRNO, $schedid);

                        if ($check_ct_gov == false) {
                            $prevcon = '';
                            $curcon = '';
                            $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;

                            // READINGS
                            $qry_read_log = $this->db->select('sysid, reading, demand, netmtr')
                                ->from('customer_accounts_subscription_meter_reading_logs')
                                ->where(array('acctid' => $row->SYSID, 'mtrid' => $row->MTRNO, 'schedid' => $schedid, 'status' => 1))
                                ->get()->row();

                            $qry_read_temp = $this->db->select('sysid, reading, demand, netmtr')
                                ->from('customer_accounts_subscription_meter_reading_temp')
                                ->where(array('acctid' => $row->SYSID, 'mtrid' => $row->MTRNO, 'schedid' => $schedid, 'status' => 1))
                                ->get()->row();

                            if ($check_read_submitted) {
                                $demand = '';
                            } else {
                                // GET CLASS RATE GROUP
                                $qry_rateclass_group = $this->db->select('rs.descs')
                                    ->from('rate_class_specification AS rs')
                                    ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                                    ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                                    ->get()->row();

                                if ($qry_rateclass_group) {
                                    $demandval_temp = ($qry_read_temp) ? round($qry_read_temp->demand, 4) : '';
                                    $demandval = ($qry_read_log) ? round($qry_read_log->demand, 4) : $demandval_temp;
                                    $demand = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                                        '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $demandval . '"/>' .
                                        '</div>';
                                } else {
                                    $demand = '<span class="label label-danger">N/A</span>';
                                }
                            }


                            $readval_temp = ($qry_read_temp) ? round($qry_read_temp->reading, 2) : '';
                            $netmtrval_temp = ($qry_read_temp) ? round($qry_read_temp->netmtr, 2) : '';
                            $readval = ($qry_read_log) ? round($qry_read_log->reading, 2) : $readval_temp;
                            $netmtrval = ($qry_read_log) ? round($qry_read_log->netmtr, 2) : $netmtrval_temp;

                            if($qry_read_log) {
                                $read_num += 1;
                            } else {
                                if($qry_read_temp) {
                                    $read_num += 1;
                                }
                            }

                            if ($check_read_submitted) {
                                $reading = '<div class="input-icon left"><i class="fa fa-check text-success" ></i> <span style="color: #cccccc">' . $readval . '</span></div>';
                            } else {
                                $reading = '<div class="input-icon left">' .
                                    '<i class="fa fa-pencil tooltips text-warning" data-original-title="Enter Reading Amount"></i>' .
                                    '<input autocomplete="off" name="reading[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline reading" style="width: 100%;" id="reading" value="' . $readval . '"/>' .
                                    '</div>';
                            }

                            // GET MRSEQ
                            $mrseq = '';
                            $mrseqinput = '';
                            /*
                            $qry_mrseq = $this->db->select()->from('customer_accounts_mtrseq')
                                ->where('acctid', $row->SYSID)->limit(1)->get()->row();
                            */
                            $mrseq_val = $row->MRSEQ;
                            //$mrseq = '<input class="form-control inline input-small" name="" id="mrseq" placeholder="0.0 value="'.$mrseq_val.'"/>';
                            //$mrseq = $mrseq_val;


                            $reading_meter_sequence = '
                                <form id=\'frm_mtrseq_entry\' style=\'\' class=\'form-horizontal\' action=\'' . base_url() . 'mrd/updatesequence\' method=\'post\'>
    
                                    
                                    <div class=\'form-body\'>
                                        <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                            <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Sequence</label>
                                            <input required type=\'text\' name=\'sequence\' class=\'form-control input-md\' id=\'mtrsequence\' style=\'width: 100% !important;\' placeholder=\'0.0\' />
                                        </div>
                                    </div>
                                    
                                    <div class=\'form-actions bottom margin-top-20\'>
                                        <button type=\'submit\' class=\'btn blue\'>Save</button>
                                    </div>
                                </form>
                             ';

                            $mrseq .= $mrseq_val;

                            if ($check_read_submitted) {

                            } else {
                                $mrseqinput .= row_popover_a('mrseqinput', '<i class="fa fa-pencil"></i>', $reading_meter_sequence, 'Meter Sequence', 'right', false);
                            }

                            $mtrid = '<input type="hidden" name="mtrid[' . $row->SYSID . ']" value="' . $row->MTRNO . '" id="mtrid"/>';
                            $acctid_input = '<input type="hidden" name="" value="' . $row->SYSID . '" id="acctid"/>';
                            $schedid_input = '<input type="hidden" name="" value="' . $schedid . '" id="schedid"/>';


                            $reading_findings_content = '
                            <form id=\'frm_findings_entry\' style=\'\' class=\'form-horizontal\' action=\'' . base_url() . 'mrd/addreadingfindings\' method=\'post\'>

                                
                                <div class=\'form-body\'>
                                    <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                        <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Findings</label>
                                        <input required type=\'text\' name=\'findings\' class=\'form-control input-md\' id=\'findings\' style=\'width: 100% !important;\' />
                                    </div>
                                    <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                        <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Remarks</label>
                                        <input required type=\'text\' name=\'remarks\' class=\'form-control input-md\' id=\'remarks\' placeholder=\'Remarks / Comment.. \' style=\'width: 100%;\'/>
                                    </div>
                                </div>
                                
                                <div class=\'form-actions bottom margin-top-20\'>
                                    <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                                    <button type=\'submit\' class=\'btn blue\'>Save</button>
                                </div>
                            </form>
                         ';

                            $control = '';
                            if ($check_read_submitted) {
                                $control = '<i class="fa fa-check text-success"></i>';
                            } else {
                                $control .= row_popover_a('readpopovers', '<i class="fa fa-comment-o fa-fw"></i>', $reading_findings_content, 'Findings / Remarks', 'left', false);
                                //$control .= '<button id="btn_rep" type="button" data-id="' . $row->SYSID . '" class="btn btn-default btn-xs popovers" data-title="Findings / Remarks" data-content="'.$reading_findings_content.'" data-placement="left"><i class="fa fa-comment-o"></i></button>';
                                $control .= '<button id="btn_del" type="button" data-id="' . $row->SYSID . '" data-schedid="' . $schedid . '" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>';
                            }

                            // NET METERING
                            if ($check_read_submitted) {
                                $netmtr = '';
                            } else {
                                if ($row->NETMTR == 1) {
                                    $netmtr = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips" data-original-title="Enter KWH"></i>' .
                                        '<input autocomplete="off" name="netmtr[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline netmtr" style="width: 100%;" id="netmtr" value="' . $netmtrval . '"/>' .
                                        '</div>';
                                } else {
                                    $netmtr = '<span class="label label-danger">N/A</span>';
                                }
                            }

                            $data['list'][] = array(
                                'expand' => btn_expand($row->SYSID),
                                'seqin' => $mrseqinput,
                                'seq' => $mrseq . $mtrid . $acctid_input . $schedid_input,
                                'serviceno' => $row->SERVNO,
                                'meter' => $row->MTR,
                                'meterno' => $row->MTRNO,
                                'serial' => $row->MTRSER,
                                'name' => $name,
                                'demand' => $demand,
                                'netmtr' => $netmtr,
                                'curread' => $reading,
                                'controls' => $control,
                                'read' => '',
                                'submitted' => $check_read_submitted
                            );
                        }
                    }
                } else {
                    $data['query'] = 'all';
                    $query = $this->db->query("
                                SELECT
                                        acct.sysid AS SYSID,
                                        acct.gdlb AS GDLBID,
                                        acct.servicenumber AS SERVNO,
                                        acct.types AS OWNERTYPE,
                                        acct.ownerid AS OWNERID,
                                        acct.mtr AS MTR,
                                        acct.mtrno AS MTRNO,
                                        acct.mtrserial AS MTRSER,
                                        acct.rateclassid AS RATEID,
                                        acct.netmtr AS NETMTR
                            FROM customer_accounts_main AS acct
                            -- JOIN reading_schedule_meters_logs AS ml ON ml.acctid = acct.sysid
                            JOIN reading_schedule_main AS sm ON sm.gdlbid = acct.gdlb
                            JOIN reading_schedule_reader AS sr ON sm.sysid = sr.schedid
                            WHERE acct.gdlb = {$sched_gdlb}
                            AND sr.userid = {$userid} 
                            AND acct.status = 1 AND sm.status != 2
                            GROUP BY 
                            acct.servicenumber,
                            acct.sysid,
                            acct.gdlb,
                            acct.sysid,
                            acct.types,
                            acct.ownerid,
                            acct.mtr,
                            acct.mtrno,
                            acct.mtrserial,
                            acct.rateclassid,
                            acct.netmtr
                            ");
                    $num_rows = $query->num_rows();

                    $readcnt = 0;
                    $kwhtotal = 0;
                    $avkwh = 0;

                    if ($num_rows > 0) {



                        foreach ($query->result() as $row) {

                            $check_ct_gov = $this->db->select('acctmainid')
                                ->from('customer_accounts_main_submatrix')
                                ->where(array('acctmainid' => $row->SYSID, 'status' => 1))
                                ->get()->row();

                            $check_read_submitted = check_reading_submitted($row->SYSID, $row->MTRNO, $schedid);


                            if ($check_ct_gov == false) {
                                $prevcon = '';
                                $curcon = '';

                                $name_arr = get_ownership_details($row->OWNERTYPE, $row->OWNERID);
                                $name = ($name_arr) ? $name_arr->name : '';

                                // READINGS
                                $qry_read_log = $this->db->select('sysid, reading, demand, netmtr')
                                    ->from('customer_accounts_subscription_meter_reading_logs')
                                    ->where(array('acctid' => $row->SYSID, 'mtrid' => $row->MTRNO, 'schedid' => $schedid, 'status' => 1))
                                    ->get()->row();

                                $qry_read_temp = $this->db->select('sysid, reading, demand, netmtr')
                                    ->from('customer_accounts_subscription_meter_reading_temp')
                                    ->where(array('acctid' => $row->SYSID, 'mtrid' => $row->MTRNO, 'schedid' => $schedid, 'status' => 1))
                                    ->get()->row();

                                if ($check_read_submitted) {
                                    $demand = '';
                                } else {
                                    // GET CLASS RATE GROUP
                                    $qry_rateclass_group = $this->db->select('rs.descs')
                                        ->from('rate_class_specification AS rs')
                                        ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                                        ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                                        ->get()->row();

                                    if ($qry_rateclass_group) {
                                        $demandval_temp = ($qry_read_temp) ? round($qry_read_temp->demand, 4) : '';
                                        $demandval = ($qry_read_log) ? round($qry_read_log->demand, 4) : $demandval_temp;
                                        $demand = '<div class="input-icon left">' .
                                            '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                                            '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $demandval . '"/>' .
                                            '</div>';
                                    } else {
                                        $demand = '<span class="label label-danger">N/A</span>';
                                    }
                                }


                                $readval_temp = ($qry_read_temp) ? round($qry_read_temp->reading, 2) : '';
                                $netmtrval_temp = ($qry_read_temp) ? round($qry_read_temp->netmtr, 2) : '';
                                $readval = ($qry_read_log) ? round($qry_read_log->reading, 2) : $readval_temp;
                                $netmtrval = ($qry_read_log) ? round($qry_read_log->netmtr, 2) : $netmtrval_temp;

                                if($qry_read_log) {
                                    $read_num += 1;
                                } else {
                                    if($qry_read_temp) {
                                        $read_num += 1;
                                    }
                                }

                                if ($check_read_submitted) {
                                    $reading = '<div class="input-icon left"><i class="fa fa-check text-success" ></i> <span style="color: #cccccc">' . $readval . '</span></div>';
                                } else {
                                    $reading = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips text-warning" data-original-title="Enter Reading Amount"></i>' .
                                        '<input autocomplete="off" name="reading[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline reading" style="width: 100%;" id="reading" value="' . $readval . '"/>' .
                                        '</div>';
                                }

                                // GET MRSEQ
                                $mrseq = '';
                                $mrseqinput = '';

                                $qry_mrseq = $this->db->select('seq')
                                    ->from(' reading_schedule_meters_logs')
                                    ->where(array('acctid' => $row->SYSID, 'mtrno' => $row->MTRNO))
                                    ->order_by('datecreated', 'desc')
                                    ->get()->row();
                                if($qry_mrseq) {
                                    $mrseq_val = $qry_mrseq->seq;
                                }else{
                                    $mrseq_val = '0';
                                }
                                //$mrseq = '<input class="form-control inline input-small" name="" id="mrseq" placeholder="0.0 value="'.$mrseq_val.'"/>';
                                //$mrseq = $mrseq_val;


                                $reading_meter_sequence = '
                                <form id=\'frm_mtrseq_entry\' style=\'\' class=\'form-horizontal\' action=\'' . base_url() . 'mrd/updatesequence\' method=\'post\'>
    
                                    
                                    <div class=\'form-body\'>
                                        <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                            <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Sequence</label>
                                            <input required type=\'text\' name=\'sequence\' class=\'form-control input-md\' id=\'mtrsequence\' style=\'width: 100% !important;\' placeholder=\'0.0\' />
                                        </div>
                                    </div>
                                    
                                    <div class=\'form-actions bottom margin-top-20\'>
                                        <button type=\'submit\' class=\'btn blue\'>Save</button>
                                    </div>
                                </form>
                             ';

                                $mrseq .= $mrseq_val;
                                if ($check_read_submitted) {

                                } else {
                                    $mrseqinput .= row_popover_a('mrseqinput', '<i class="fa fa-pencil"></i>', $reading_meter_sequence, 'Meter Sequence', 'right', false);
                                }
                                $mtrid = '<input type="hidden" name="mtrid[' . $row->SYSID . ']" value="' . $row->MTRNO . '" id="mtrid"/>';
                                $acctid_input = '<input type="hidden" name="" value="' . $row->SYSID . '" id="acctid"/>';
                                $schedid_input = '<input type="hidden" name="" value="' . $schedid . '" id="schedid"/>';


                                $reading_findings_content = '
                            <form id=\'frm_findings_entry\' style=\'\' class=\'form-horizontal\' action=\'' . base_url() . 'mrd/addreadingfindings\' method=\'post\'>

                                
                                <div class=\'form-body\'>
                                    <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                        <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Findings</label>
                                        <input required type=\'text\' name=\'findings\' class=\'form-control input-md\' id=\'findings\' style=\'width: 100% !important;\' />
                                    </div>
                                    <div class=\'form-group\' style=\'margin: 0px 0px; width: 100%;\'>
                                        <label class=\'control-label\' style=\'width: 100%; display: inline-block;\'>Remarks</label>
                                        <input required type=\'text\' name=\'remarks\' class=\'form-control input-md\' id=\'remarks\' placeholder=\'Remarks / Comment.. \' style=\'width: 100%;\'/>
                                    </div>
                                </div>
                                
                                <div class=\'form-actions bottom margin-top-20\'>
                                    <button type=\'reset\' class=\'btn btn-default\'>Reset</button>
                                    <button type=\'submit\' class=\'btn blue\'>Save</button>
                                </div>
                            </form>
                         ';

                                $control = '';
                                if ($check_read_submitted) {
                                    $control = '<i class="fa fa-check text-success"></i>';
                                } else {
                                    $control .= row_popover_a('readpopovers', '<i class="fa fa-comment-o fa-fw"></i>', $reading_findings_content, 'Findings / Remarks', 'left', false);
                                    //$control .= '<button id="btn_rep" type="button" data-id="' . $row->SYSID . '" class="btn btn-default btn-xs popovers" data-title="Findings / Remarks" data-content="'.$reading_findings_content.'" data-placement="left"><i class="fa fa-comment-o"></i></button>';
                                    $control .= '<button id="btn_del" type="button" data-id="' . $row->SYSID . '" data-schedid="' . $schedid . '" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>';
                                }

                                // NET METERING
                                if ($check_read_submitted) {
                                    $netmtr = '';
                                } else {
                                    if ($row->NETMTR == 1) {
                                        $netmtr = '<div class="input-icon left">' .
                                            '<i class="fa fa-pencil tooltips" data-original-title="Enter KWH"></i>' .
                                            '<input autocomplete="off" name="netmtr[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline netmtr" style="width: 100%;" id="netmtr" value="' . $netmtrval . '"/>' .
                                            '</div>';
                                    } else {
                                        $netmtr = '<span class="label label-danger">N/A</span>';
                                    }
                                }

                                $data['list'][] = array(
                                    'expand' => btn_expand($row->SYSID),
                                    'seqin' => $mrseqinput,
                                    'seq' => $mrseq . $mtrid . $acctid_input . $schedid_input,
                                    'serviceno' => $row->SERVNO,
                                    'meter' => $row->MTR,
                                    'meterno' => $row->MTRNO,
                                    'serial' => $row->MTRSER,
                                    'name' => $name,
                                    'demand' => $demand,
                                    'netmtr' => $netmtr,
                                    'curread' => $reading,
                                    'controls' => $control,
                                    'read' => '',
                                    'submitted' => $check_read_submitted
                                );

                            }
                        }
                    }
                }
            }
        }
        $data['readnum'] = $read_num;
        $data['cnt'] = $num_rows;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function delete_reading_temp() {
        $data = array();
        $qry = false;
        $acctid = $this->input->post('acctid');
        $schedid = $this->input->post('schedid');
        $this->db->trans_begin();

        $this->db->where(array('acctid' => $acctid, 'schedid' => $schedid, 'status' => 1));
        $this->db->update('customer_accounts_subscription_meter_reading_temp', array('status' => 0));
        if($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $qry = true;
        }else{
            $this->db->trans_rollback();
        }
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_select2_findings() {
        $data = array();
        $term = $this->input->post('term');
        $qry_mult = $this->db->select()->from('meter_reading_findings')
            ->or_like('codes', $term)
            ->or_like('descriptions', $term)
            ->get();
        if($qry_mult->num_rows() > 0) {
            foreach($qry_mult->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes . ' - ' .$row->descriptions
                );
            }
        }
        return json_encode($data);
    }

    function get_compute_reading($schedid = false, $acctid = false, $newread = false) {

        $schedid_input = $this->input->post('schedid');
        $acctid_input = $this->input->post('acctid');
        $newread_input = $this->input->post('newread');

        $schedid = ($schedid) ? $schedid : $schedid_input;
        $acctid = ($acctid) ? $acctid : $acctid_input;
        $newread = ($newread) ? $newread : $newread_input;

        $data = array();
        $q = false;
        $msg = '';
        $new_cons_total = 0;
        $new_percent = '';
        $prev_cons = 0;
        $pres_read = 0;
        $prev_read = 0;
        $color = '';
        $recheck = true;



        if($schedid && $acctid && $newread) {

            $get_sched_details = $this->db->select('sysid, gdlbid, months, years')
                ->from('reading_schedule_main')
                ->where('sysid', $schedid)
                ->get()->row();

            if ($get_sched_details) {
                $sched_month = $get_sched_details->months;
                $sched_year = $get_sched_details->years;
                $prev_month = ($sched_month == 1) ? 12 : $sched_month - 1;
                $prev_year = ($sched_month == 1) ? $sched_year - 1 : $sched_year;

                $data['prevyr'] = $prev_year;
                $data['prevmo'] = $prev_month;

                $query = $this->db->query("
                        SELECT
                            acct.sysid AS SYSID,
                            acct.servicenumber AS SERVNO,
                            acct.types AS OWNERTYPE,
                            acct.ownerid AS OWNERID,
                            acct.mtr AS MTR,
                            acct.rateclassid AS RATEID,
                            br.prsrdg AS PREVRDG,
                            br.kwhuse AS PREVKWH
                        FROM customer_accounts_main AS acct
                        LEFT JOIN billing_reports_main AS br ON br.acctid = acct.sysid 
                        WHERE acct.sysid = $acctid AND br.year = $prev_year AND br.month = $prev_month 
                     ")->row();
                if ($query) {
                    $prev_read = $query->PREVRDG;
                    $prev_cons = $query->PREVKWH;
                    $data['prevread'] = $prev_read;
                    $data['prevkew'] = $prev_cons;

                    $new_cons = bcsub($newread, $query->PREVRDG, 0);
                    $data['diff'] = $new_cons;

                    if ($new_cons > 0) {
                        $msg = 'Computed Positive';
                        $new_cons_total = $new_cons;
                        $pres_read = $newread;
                    } else {
                        $msg = 'Computed Negative';
                        $qry_reading = $this->db->query("
                                        SELECT mrl.sysid, mrl.reading, mrl.demand
                                        FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                        WHERE mrl.acctid = $acctid AND mrl.schedid = $schedid AND mrl.status = 1 ORDER BY mrl.datecreated DESC 
                            ")->row();

                        // RETURN VALUE OF CONSUMPTION IF NEW INPUT DOES NOT HAVE POSITIVE VALUE
                        if ($qry_reading) {
                            $readid = $qry_reading->sysid;
                            $pres_read = $qry_reading->reading;
                            $pres_dems = $qry_reading->demand;
                            $old_cons = mtr_wrap_kwh($pres_read, $query->PREVRDG, $readid);
                        } else {
                            $old_cons = 0;
                        }
                        $new_cons_total = $old_cons;
                    }
                }
                $check = readcheck($new_cons_total, $prev_cons, $pres_read, $prev_read);
                $data['check'] = $check;
                $new_percent = '<span class="pull-left">'.$check->icon.'</span>' . number_format($check->per,2) . ' %';
                $color = $check->color;
                $recheck = $check->recheck;
            }

        }
        $data['recheck'] = $recheck;
        $data['color'] = $color;
        $data['msg']  = $msg;
        $data['input'] = $this->input->post();
        $data['newcons'] = $new_cons_total;
        $data['percent'] = $new_percent;
        return (object) $data;
    }

    function get_reading_analysis() {
        // set_time_limit(600000);

        $addbillproc = $this->input->post('addbillproc');
        $showall = $this->input->post('showall');
        $acctids = $this->input->post('acctids');
        $print = $this->input->post('print');

        $input_duedate = $this->input->post('duedate');
        $register = $this->input->post('register');

        $ctt = $this->input->post('ct');


        $qry = false;
        $avkwh = 0;
        $readcnt = 0;
        $totalprskwh = 0;
        $totalprvkwh = 0;
        $totalprvkwh = 0;
        $forbilling = 0;
        $totalamt = 0;
        $totalamt_legacy = 0;
        $totalamt_diff = 0;
        $zero = 0;
        $recheck = 0;
        $avkwhcurr = 0;
        $avkwhprev = 0;
        $cnterr = 0;
        $billing = false;
        $func = 'error';
        $msg = '';
        $billingcnt = 0;
        $billno_input = false;
        $print_html = '';
        $num_rows = 0;
        $num_rows_ok = 0;
        $num_rows_insert = 0;
        $gdlb_name = '';
        $sched_date = '';
        $reader = '';
        $exec = true;

        $acct_ids_arr = array();
        if($acctids && count($acctids) > 0){
            foreach($acctids as $irow) {
                $acct_ids_arr[] = $irow['checked'];
            }
        }


        if ($showall != 0) {
            if ( user_id() == false ) {
                $data['msg'] = 'Session timeout!';
                $data['qry'] = false;
                $num_rows = 0;
            } else {
                $data = array();


                $schedid = $this->input->post('schedid');
                $billing = $this->input->post('billing');

                // TERMINATE IF BILLING INPUT IS EMPTY
                if ($billing == true) {
                    $billno_input = $this->input->post('billno');
                    if ($billno_input == '') {
                        $data['func'] = 'warning';
                        $data['msg'] = 'Please input billing number';
                        $data['qry'] = false;
                        return json_encode($data);
                        exit();
                    }
                }

                if($ctt == 1) {
                    $get_sched_details = $this->db->select('sysid, gdlbid, months, years, datesched, status')
                        ->from('reading_schedule_main')
                        ->where(array('gdlbid' => 33, 'status' => 1))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();
                    $sched_id = $get_sched_details->sysid;
                }else{
                    $get_sched_details = $this->db->select('sysid, gdlbid, months, years, datesched, status')
                        ->from('reading_schedule_main')
                        ->where('sysid', $schedid)
                        ->get()->row();
                }


                if ($get_sched_details) {

                    $sched_month = $get_sched_details->months;
                    $sched_year = $get_sched_details->years;
                    $sched_gdlb = $get_sched_details->gdlbid;
                    $sched_date = $get_sched_details->datesched;

                    $data['gdlbid'] = $sched_gdlb;
                    $data['year'] = $sched_year;
                    $data['month'] = $sched_month;

                    // ##########################################################################
                    // ##########################################################################
                    // CHECK RATES
                    if ($showall == 2 || $showall == 4) {
                        $qry_rates = $this->db->select('count(sysid) AS cnt')
                            ->from('trn_billing_rates')
                            ->where(array('year' => $sched_year, 'month' => $sched_month))
                            ->get()->row();

                        if($qry_rates && $qry_rates->cnt == 0) {
                            $exec = false;
                        }
                    }

                    // GET GDLB BILLING
                    $qry_gdlb = $this->db->select('g.g AS g, d.codes AS d, g.l, g.b')
                        ->from('gdlb_main AS g')
                        ->join('address_districts as d', 'd.sysid = g.d', 'left')
                        ->where('g.sysid', $sched_gdlb)->get()->row();
                    $group = '';
                    $dist = '';
                    $lot = '';
                    $book = '';

                    if ($qry_gdlb) {
                        $group = $qry_gdlb->g;
                        $dist = $qry_gdlb->d;
                        $lot = $qry_gdlb->l;
                        $book = $qry_gdlb->b;
                        $gdlb_name = $group . '-' . $dist . '-' . $lot . '-' . $book;
                    }

                    $get_readers_details = $this->db->select('u.sysid, ulc.telcode, p.lastname')
                        ->from('reading_schedule_reader AS r')
                        ->join('prime_system_users AS u', 'r.userid = u.sysid')
                        ->join('prime_system_users_legacy_code AS ulc', 'ulc.userid = u.sysid')
                        ->join('person AS p', 'p.sysid = u.personid')
                        ->where(array('r.schedid' => $get_sched_details->sysid, 'r.status' => 1))
                        ->get()->row();

                    $reader = ($get_readers_details) ? '<span class="text-bold">'.$get_readers_details->telcode.'</span> ' . $get_readers_details->lastname : '';

                    $readerid = $get_readers_details->sysid;
                    $prev_month = ($sched_month == 1) ? 12 : $sched_month - 1;
                    $prev_year = ($sched_month == 1) ? $sched_year - 1 : $sched_year;

                    // check previous billing / reading first


                    if($exec == true) {
                        if( $ctt == 1 ) {
                            if($schedid > 0) {
                                $query = $this->db->query("
                                    SELECT
                                        acct.sysid AS SYSID,
                                        acct.servicenumber AS SERVNO,
                                        acct.types AS OWNERTYPE,
                                        acct.ownerid AS OWNERID,
                                        acct.mtr AS MTR,
                                        acct.mtrno AS MTRNO,
                                        acct.mtrserial AS MTRSER,
                                        acct.rateclassid AS RATEID,
                                        acct.netmtr AS NETMTR,
                                        acctload.load AS LOADS,
                                        bm.codes AS MULTCODE,
                                        bm.rate AS MULTIPLIER,
                                        br.prsrdg AS PREVRDG,
                                        br.prsdte AS PREVDTE,
                                        br.kwhuse AS PREVKWH,
                                        class.codes AS CLASSCODE,
                                        addr.addrspecific AS ADDRSPEC,
                                        ml.seq AS MRSEQ
                                    FROM customer_accounts_main AS acct
                                    LEFT JOIN customer_accounts_load_logs AS acctload ON acctload.acctid = acct.sysid
                                    LEFT JOIN billing_reports_main AS br ON br.acctid = acct.sysid 
                                    LEFT JOIN billing_rates_main_multiplier AS bm ON bm.sysid = acct.multid
                                    LEFT JOIN rate_class_specification AS class ON class.sysid = acct.rateclassid
                                    LEFT JOIN customer_accounts_address AS addr ON addr.acctid = acct.sysid
                                    LEFT JOIN reading_schedule_meters_logs AS ml ON ml.acctid = acct.sysid
									JOIN customer_accounts_main_submatrix AS ams ON ams.acctid = acct.sysid
                                    WHERE acct.gdlb = 33 AND br.year = $prev_year AND br.month = $prev_month AND ams.acctmainid = $schedid
                                    GROUP BY 
                                        acct.sysid,
                                        acct.servicenumber,
                                        acct.types,
                                        acct.ownerid,
                                        acct.mtr,
                                        acct.mtrno,
                                        acct.mtrserial,
                                        acct.rateclassid,
                                        acct.netmtr,
                                        acctload.load,
                                        bm.codes,
                                        bm.rate,
                                        br.prsrdg,
                                        br.prsdte,
                                        br.kwhuse,
                                        class.codes,
                                        addr.addrspecific,
                                        ml.seq
                                    ORDER BY acct.servicenumber
                                 ");

                            } else {
                                $query = $this->db->query("
                                    SELECT
                                        acct.sysid AS SYSID,
                                        acct.servicenumber AS SERVNO,
                                        acct.types AS OWNERTYPE,
                                        acct.ownerid AS OWNERID,
                                        acct.mtr AS MTR,
                                        acct.mtrno AS MTRNO,
                                        acct.mtrserial AS MTRSER,
                                        acct.rateclassid AS RATEID,
                                        acct.netmtr AS NETMTR,
                                        acctload.load AS LOADS,
                                        bm.codes AS MULTCODE,
                                        bm.rate AS MULTIPLIER,
                                        br.prsrdg AS PREVRDG,
                                        br.prsdte AS PREVDTE,
                                        br.kwhuse AS PREVKWH,
                                        class.codes AS CLASSCODE,
                                        addr.addrspecific AS ADDRSPEC,
                                        ml.seq AS MRSEQ
                                    FROM customer_accounts_main AS acct
                                    LEFT JOIN customer_accounts_load_logs AS acctload ON acctload.acctid = acct.sysid
                                    LEFT JOIN billing_reports_main AS br ON br.acctid = acct.sysid 
                                    LEFT JOIN billing_rates_main_multiplier AS bm ON bm.sysid = acct.multid
                                    LEFT JOIN rate_class_specification AS class ON class.sysid = acct.rateclassid
                                    LEFT JOIN customer_accounts_address AS addr ON addr.acctid = acct.sysid
                                    LEFT JOIN reading_schedule_meters_logs AS ml ON ml.acctid = acct.sysid
                                    WHERE acct.gdlb = 33 AND br.year = $prev_year AND br.month = $prev_month 
                                    GROUP BY 
                                        acct.sysid,
                                        acct.servicenumber,
                                        acct.types,
                                        acct.ownerid,
                                        acct.mtr,
                                        acct.mtrno,
                                        acct.mtrserial,
                                        acct.rateclassid,
                                        acct.netmtr,
                                        acctload.load,
                                        bm.codes,
                                        bm.rate,
                                        br.prsrdg,
                                        br.prsdte,
                                        br.kwhuse,
                                        class.codes,
                                        addr.addrspecific,
                                        ml.seq
                                    ORDER BY acct.servicenumber
                                 ");
                            }
                        } else {

                            // CHECK SPECIFIC
                            $query = $this->db->query("
                                SELECT
                                    am.sysid AS SYSID,
                                    am.servicenumber AS SERVNO,
                                    am.types AS OWNERTYPE,
                                    am.ownerid AS OWNERID,
                                    am.mtr AS MTR,
                                    am.mtrno AS MTRNO,
                                    am.mtrserial AS MTRSER,
                                    am.rateclassid AS RATEID,
                                    am.netmtr AS NETMTR,
                                    bm.sysid AS MULTID,
                                    bm.codes AS MULTCODE,
                                    bm.rate AS MULTIPLIER,
                                    ml.prsrdg AS PRESRDG,
                                    ml.prvrdg AS PREVRDG,
                                    ml.prsdte AS PRESDTE,
                                    ml.prvdte AS PREVDTE,
                                    (ml.prsrdg - ml.prvrdg) AS KWHUSE,
                                    class.codes AS CLASSCODE,
                                    ml.seq AS MRSEQ,
                                    ml.`status` AS MLSTATUS
                                FROM
                                    customer_accounts_main AS am
                                    JOIN reading_schedule_meters_logs AS ml ON ml.acctid = am.sysid 
                                    LEFT JOIN billing_rates_main_multiplier AS bm ON bm.sysid = am.multid
                                    LEFT JOIN rate_class_specification AS class ON class.sysid = am.rateclassid AND class.`status` = 1
                                WHERE
                                    am.`status` = 1 AND ml.`status` = 1 AND ml.schedid = $schedid
                                GROUP BY
                                    am.sysid,
                                    am.servicenumber,
                                    am.types,
                                    am.ownerid,
                                    am.mtr,
                                    am.mtrno,
                                    am.mtrserial,
                                    am.rateclassid,
                                    am.netmtr,
                                    bm.sysid,
                                    bm.codes,
                                    bm.rate,
                                    class.codes,
                                    ml.seq,
                                    ml.`status`,
                                    ml.prsrdg,
                                    ml.prvrdg,
                                    ml.prsdte,
                                    ml.prvdte
                                ORDER BY
                                    ml.seq,
                                    am.servicenumber
                             ");
                        }


                        $data['prevmonth'] = $prev_month;
                        $data['prevyear'] = $prev_year;


                        $num_rows = $query->num_rows();


                        if ($num_rows > 0) {

                            if ($billing == true) {
                                $this->db->trans_begin();
                                $billno_input = $this->input->post('billno');

                                // CHECK BILLNO IF USED
                                if ($billno_input > 0) {
                                    $qry_check_billno = $this->db->select('billno')
                                        ->from('billing_reports')
                                        ->where('billno', $billno_input)
                                        ->get()->row();
                                    if ($qry_check_billno) {
                                        $data['func'] = 'warning';
                                        $data['msg'] = 'Billing number is existed!';
                                        $data['qry'] = false;
                                        return json_encode($data);
                                        exit();
                                    }
                                } else {
                                    $data['func'] = 'warning';
                                    $data['msg'] = 'Billing number should be positive or above zero.';
                                    $data['qry'] = false;
                                    return json_encode($data);
                                    exit();
                                }

                                // @TODO create checking query here for existing billed schedid in billing reports.

                                if ($billno_input == '') {
                                    $data['func'] = 'warning';
                                    $data['msg'] = 'Please input billing number';
                                    $data['qry'] = false;
                                    return json_encode($data);
                                    exit();
                                }
                            }

                            foreach ($query->result() as $row) {
                                $sysid = $row->SYSID;
                                $stat_msg = '';
                                $contype = 1;
                                $readid = 0;

                                // OWNER INFO
                                $name_arr = get_ownership_details($row->OWNERTYPE, $row->OWNERID);
                                $name = ($name_arr) ? ucwords(strtolower($name_arr->name)) : '';
                                //$addr = '';
                                $demand = '<span class="label label-danger">N/A</span>';


                                $sched_year_input = '<input id="input_year" type="hidden" value="'.$sched_year.'" />';
                                $sched_month_input = '<input id="input_month" type="hidden" value="'.$sched_month.'" />';

                                // $name = '';

                                // GET MRSEQ
                                /*
                                $qry_mrseq = $this->db->select()->from('customer_accounts_mtrseq')
                                    ->where('acctid', $row->SYSID)->limit(1)->get()->row();
                                $mrseq = ($qry_mrseq) ? $qry_mrseq->mrseq : '';
                                */

                                $mrseq = $row->MRSEQ;


                                $prev_read = $row->PRESRDG; // PRESENT READING OF LOGS IS THE PREV READ
                                $prev_date = $row->PRESDTE;
                                $prev_cons = $row->KWHUSE;

                                if($showall == 2) { // IF BILLING ENABLED
                                    $get_acct_info = get_active_account_info($sysid);
                                    $acct_load = 0; //@TODO CRREATE QUERY LOADS ON QUERY TYPE
                                    $addr = ucwords(strtolower($get_acct_info->address)); //@TODO CRREATE QUERY ADDRESS DEPENDS ON QUERY TYPE
                                }else{
                                    if($print == 1) {
                                        $get_acct_info = get_active_account_info($sysid);
                                        $acct_load = 0;
                                        $addr = ucwords(strtolower($get_acct_info->address));
                                    }else {
                                        $acct_load = 0;
                                        $addr = ucwords(strtolower(''));
                                    }
                                }

                                $netmtrval = 0;
                                if($ctt==1) {
                                    $qry_reading = $this->db->query("
                                                SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr, CAST(mrl.datecreated AS DATE) AS rdgdte
                                                FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                                WHERE mrl.acctid = $sysid AND mrl.schedid = $sched_id AND mrl.status = 1 AND mrl.types < 2 
                                    ")->row();
                                }else {
                                    $qry_reading = $this->db->query("
                                                SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr, CAST(mrl.datecreated AS DATE) AS rdgdte
                                                FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                                WHERE mrl.acctid = $sysid AND mrl.schedid = $schedid AND mrl.status = 1 AND mrl.types < 2 
                                    ")->row();
                                }


                                $pres_read = 0;
                                $pres_date = '';
                                $pres_cons = 0;
                                $pres_dems = 0;


                                if ($qry_reading) {
                                    $pres_read = $qry_reading->reading;
                                    $pres_date = $qry_reading->rdgdte;
                                    $readid = $qry_reading->sysid;
                                    $netmtrval = $qry_reading->netmtr;
                                    $pres_dems = $qry_reading->demand;
                                    $pres_cons = mtr_wrap_kwh($pres_read, $prev_read, $readid);

                                    // GET CLASS RATE GROUP
                                    $qry_rateclass_group = $this->db->select('rs.descs')
                                        ->from('rate_class_specification AS rs')
                                        ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                                        ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                                        ->get()->row();

                                    if ($qry_rateclass_group) {
                                        $demand = '<div class="input-icon left">' .
                                            '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                                            '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $pres_dems . '"/>' .
                                            '</div>';
                                    } else {
                                        $demand = '<span class="label label-danger">N/A</span>';
                                    }
                                    $readcnt += 1;
                                }

                                $chk_read = '';
                                $add_read = '';
                                $row_color = '';
                                $row_rem = '';
                                $add_bill_proc = false;
                                // ################################################################
                                // CHECK FOR RECHECK
                                $findings = '';
                                $readrecheck = false;

                                if ($pres_cons <= 0) {
                                    if($ctt==1) {
                                        $qry_reading_recheck = $this->db->query("
                                                SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr, CAST(mrl.datecreated AS DATE) AS rdgdte
                                                FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                                WHERE mrl.acctid = $sysid AND mrl.schedid = $sched_id AND mrl.status = 1 AND mrl.types = 2
                                        ")->row();
                                    } else {
                                        $qry_reading_recheck = $this->db->query("
                                                SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr, CAST(mrl.datecreated AS DATE) AS rdgdte
                                                FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                                WHERE mrl.acctid = $sysid AND mrl.schedid = $schedid AND mrl.status = 1 AND mrl.types = 2
                                        ")->row();
                                    }

                                    if ($qry_reading_recheck) {
                                        $pres_read = $qry_reading_recheck->reading;
                                        $pres_date = (isset($qry_reading->rdgdte)) ? $qry_reading->rdgdte: '';
                                        $readid = $qry_reading_recheck->sysid;
                                        $netmtrval = $qry_reading_recheck->netmtr;
                                        $pres_dems = $qry_reading_recheck->demand;
                                        $pres_cons = mtr_wrap_kwh($pres_read, $prev_read, $readid);
                                        //$pres_cons = $readid;

                                        $readrecheck = true;
                                        $row_color = 'warning';

                                        // GET CLASS RATE GROUP
                                        $qry_rateclass_group = $this->db->select('rs.descs')
                                            ->from('rate_class_specification AS rs')
                                            ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                                            ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                                            ->get()->row();

                                        if ($qry_rateclass_group) {
                                            $demand = '<div class="input-icon left">' .
                                                '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                                                '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $pres_dems . '"/>' .
                                                '</div>';
                                        } else {
                                            $demand = '<span class="label label-danger">N/A</span>';
                                        }
                                    }
                                }
                                // ################################################################
                                if ($prev_cons > 0) {
                                    $totalprvkwh += $prev_cons;
                                }

                                // ################################################################
                                // CHECK FOR ADD BILL ENTRY
                                $addbill = false;
                                $bill_preview_stat = '<i class="fa fa-check text-success"></i>';
                                $for_bill = true;
                                if ($pres_cons <= 0) {
                                    if($ctt==1) {
                                        $qry_addbill = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                                            ->where(array('acctid' => $sysid, 'schedid' => $sched_id, 'status >= ' => 1, 'types' => 1))
                                            ->get()->row();
                                    }else {
                                        $qry_addbill = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                                            ->where(array('acctid' => $sysid, 'schedid' => $schedid, 'status >= ' => 1, 'types' => 1))
                                            ->get()->row();
                                    }
                                    if ($qry_addbill) {
                                        $addbill = true;
                                        $row_color = 'warning';
                                        $pres_cons = $qry_addbill->kwhuse;
                                        $row_rem = '<span class="label label-warning" style="width: 100%; display: inline-block">A/L</span>';
                                        $add_read = '<code>A</code>';
                                        $add_bill_proc = true;
                                        if($pres_cons == 0) {
                                            $qry_actual_read_override = $this->db->select('readid')
                                                ->from('trn_reading_override')
                                                ->where(array('readid' => $readid, 'status' => 1))
                                                ->get()->row();

                                            if($qry_actual_read_override == false) {
                                                $bill_preview_stat = '<i class="fa fa-times text-danger"></i>';
                                                $bill_preview_stat .= ' <button data-stat="1" id="btn_actual_read" type="button" class="btn btn-xs btn-primary inline tooltips" data-container="body" data-placement="left" data-original-title="Save actual reading"><i class="fa fa-save"></i></button>';
                                                $for_bill = false;
                                            }
                                        }
                                    } else {
                                        if($ctt==1) {
                                            $qry_addbill_2 = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                                                ->where(array('acctid' => $sysid, 'schedid' => $schedid, 'status >= ' => 1, 'types' => 1))
                                                ->get()->row();
                                        }else {
                                            $qry_addbill_2 = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                                                ->where(array('acctid' => $sysid, 'schedid' => $schedid, 'status >= ' => 1, 'types' => 1))
                                                ->get()->row();
                                        }
                                        if ($qry_addbill_2) {
                                            $addbill = true;
                                            $pres_cons = $qry_addbill_2->kwhuse;
                                            $row_color = 'warning';
                                            $row_rem = '<span class="label label-danger">RV CHECK</span>';
                                            $add_read = '<code>A</code>';
                                            $add_bill_proc = true;
                                            if($pres_cons == 0) {
                                                $qry_actual_read_override = $this->db->select('readid')
                                                    ->from('trn_reading_override')
                                                    ->where(array('readid' => $readid, 'status' => 1))
                                                    ->get()->row();

                                                if($qry_actual_read_override == false) {
                                                    $bill_preview_stat = '<i class="fa fa-times text-danger"></i>';
                                                    $bill_preview_stat .= ' <button data-stat="1" id="btn_actual_read" type="button" class="btn btn-xs btn-primary inline tooltips" data-container="body" data-placement="left" data-original-title="Save actual reading"><i class="fa fa-save"></i></button>';
                                                    $for_bill = false;
                                                }
                                            }
                                        }
                                    }
                                }
                                // ################################################################


                                $check = readcheck($pres_cons, $prev_cons, $pres_read, $prev_read);

                                $row_show = false;

                                // CHECK HNO
                                $hno = mrd_check_hno($row->SYSID, $row->MTRNO);
                                $lfinding = mrd_get_last_findings($row->SYSID, $row->MTRNO);
                                $findings_a = '<a style="margin:0px 0px !important;" href="javascript:;" id="findings_input" data-type="findings" data-pk="'.$row->MTRNO.'" data-value="" data-original-title="Select Findings">Findings..</a>';


                                // CHECK PERCENT INCREASE
                                $check_input = '';
                                if ($readrecheck == true) {
                                    $row_color = $row_color;
                                    $row_rem = $row_rem;
                                    if($check->recheck == true) {
                                        if($hno->qry) {
                                            $findings_a = '<a style="margin:0px 0px !important;" href="javascript:;" class="tooltips" title="'.$hno->hnodesc.'" id="findings_input" data-type="findings" data-pk="'.$row->MTRNO.'" data-value="" data-original-title="Select Findings">'.$hno->ref.'</a>';
                                        }else{
                                            if($lfinding->qry) {
                                                $findings_a = '<a style="margin:0px 0px !important;" href="javascript:;" class="tooltips" title="'.$lfinding->desc.'" id="findings_input" data-type="findings" data-pk="'.$row->MTRNO.'" data-value="" data-original-title="Select Findings">'.$lfinding->code.'</a>';
                                            }
                                        }
                                    }
                                    $row_show = true;

                                } else {
                                    if ($addbill == true) {
                                        $row_color = $row_color;
                                        $row_rem = $row_rem;
                                    } else {
                                        $row_color = $check->color;
                                        $row_rem = $check->rem;
                                        if($lfinding->qry) {
                                            $findings_a = '<a style="margin:0px 0px !important;" href="javascript:;" class="tooltips" title="'.$lfinding->desc.'" id="findings_input" data-type="findings" data-pk="'.$row->MTRNO.'" data-value="" data-original-title="Select Findings">'.$lfinding->code.'</a>';
                                        }
                                    }
                                }


                                if ($addbill === true) {
                                    $reg_check_input = 'checked';
                                    $forbilling += 1;
                                    $reg_bill = '<input type="checkbox" class="icheck" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" ' . $reg_check_input . ' value="' . $sysid . '" name="regbill[' . $sysid . ']" />';
                                } else {
                                    if ($readrecheck == false) {

                                        $chk_check_input = '';
                                        if ($check->chkread == true) {
                                            $chk_check_input = 'checked';
                                            $row_show = true;
                                        }
                                        $chk_read = '<input type="checkbox" class="icheck" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" ' . $chk_check_input . ' value="' . $sysid . '" name="chkread[' . $sysid . ']" />';

                                        $add_check_input = '';
                                        if ($check->addbill == true) {
                                            $add_check_input = 'checked';
                                            $row_show = true;
                                        }
                                        $add_read = '<input type="checkbox" class="icheck" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" ' . $add_check_input . ' value="' . $sysid . '" name="addbill[' . $sysid . ']" />';

                                    } else {
                                        $reg_check_input = 'checked';
                                        $forbilling += 1;
                                        $reg_bill = '<input type="checkbox" class="icheck" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" ' . $reg_check_input . ' value="' . $sysid . '" name="regbill[' . $sysid . ']" />';
                                    }
                                }


                                $qry_actual_read_override = $this->db->select('readid')
                                    ->from('trn_reading_override')
                                    ->where(array('readid' => $readid, 'status' => 1))
                                    ->get()->row();

                                if ($qry_actual_read_override) {
                                    $row_show = false;
                                }


                                if ($check->addbill == true && $addbill == false) {
                                    $zero += 1;
                                }


                                // NET METERING
                                if ($row->NETMTR == 1) {
                                    $netmtr = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips" data-original-title="Enter KWH"></i>' .
                                        '<input autocomplete="off" name="netmtr[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline netmtr" style="width: 100%;" id="netmtr" value="' . $netmtrval . '"/>' .
                                        '</div>';
                                } else {
                                    $netmtr = '<span class="label label-danger">N/A</span>';
                                }

                                if($pres_cons>0) {
                                    if ($check->per > 0) {
                                        $percent_tip = 'Increase consumptions!';
                                    } else {
                                        $percent_tip = 'Decrease consumptions!';
                                    }
                                }else{
                                    $percent_tip = 'Zero consumptions!';
                                }

                                // ##################################################################################
                                // ##################################################################################
                                // ANALYSIS VIEW

                                if ($showall == 1 && $row_show == true) {
                                    $cnterr += 1;
                                    $recheck += 1;

                                    $mtrid = '<input type="hidden" name="mtrid[' . $sysid . ']" value="' . $row->MTRNO . '" id="mtrid"/>';
                                    $acctid_input = '<input type="hidden" name="" value="' . $sysid . '" id="acctid"/>';
                                    $schedid_input = '<input type="hidden" name="" value="' . $schedid . '" id="schedid"/>';
                                    $read_input = '<input type="hidden" name="" value="' . $readid . '" id="readid"/>';

                                    $findings_id = 0;
                                    $qry_trn_findigns = $this->db->select('findingid')
                                        ->from('trn_reading_findings')
                                        ->where(array('readingid' => $readid, 'status' => 1))
                                        ->order_by('dateupdated', 'desc')
                                        ->get()->row();
                                    if ($qry_trn_findigns) {
                                        $findings_id = $qry_trn_findigns->findingid;
                                    }

                                    $qry_findings_list = $this->get_list_findings();
                                    $findings_list = '';
                                    if ($qry_findings_list) {
                                        foreach ($qry_findings_list as $frow) {
                                            $selected = '';
                                            if ($qry_trn_findigns) {
                                                if ($frow->sysid == $findings_id) {
                                                    $selected = 'selected';
                                                }
                                            }
                                            $findings_list .= '<option ' . $selected . ' value="' . $frow->sysid . '">' . $frow->codes . ' - ' . $frow->descriptions . '</option>';
                                        }
                                    }


                                    $reading = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips" style="color: red;" data-container="body" data-placement="left" data-original-title="Enter Reading Number"></i>' .
                                        '<input autocomplete="off" name="reading[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline reading" style="width: 100%;" id="reading" value="' . round($pres_read, 2) . '"/>' .
                                        '</div>';

                                    $prevreading = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips" style="color: red;" data-container="body" data-placement="left" data-original-title="Enter Reading Number"></i>' .
                                        '<input autocomplete="off" name="prevreading[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline prevreading" style="width: 100%;" id="prevreading" value="' . round($prev_read, 2) . '"/>' .
                                        '</div>';


                                    $qry_findings = $this->db->select('rf.codes')
                                        ->from('trn_reading_findings AS trf')
                                        ->join('meter_reading_findings AS rf', 'rf.sysid = trf.findingid')
                                        ->where(array('acctid' => $sysid, 'mtrno' => $row->MTRNO))
                                        ->get()->row();
                                    $findings_text = ($qry_findings) ? $qry_findings->codes : '';

                                    //$findings = '<a class="tooltips" style="margin:0px 0px !important;" href="javascript:;" id="findings_input" title="Findings Entry" data-placement="left" data-type="findings" data-pk="' . $exrow['G'] . '" data-value="" data-original-title="Select Findings">Findings..</a>';
                                    $findings = '<input id="input_findings" class="form-control input-xs inline" placeholder="Findings.." value="'.$findings_text.'"/>';


                                    $data['list'][] = array(
                                        'seq' => btn_expand($sysid) . $mtrid . $acctid_input . $schedid_input . $read_input . $sched_year_input . $sched_month_input,
                                        'schedid' => $schedid,
                                        'acctid' => $sysid,
                                        'serviceno' => $row->SERVNO,
                                        'readid' => $readid,
                                        'name' => $name,
                                        'meter' => $row->MTR,
                                        'meterno' => $row->MTRNO,
                                        'serial' => $row->MTRSER,
                                        'mult' => '<span class="text-bold">' . $row->MULTCODE . '</span><span class="pull-right">' . number_format($row->MULTIPLIER, 2) . '</span>',
                                        'prevread' => $prevreading,
                                        'curread' => $reading,
                                        'curdem' => $demand,
                                        'netmet' => $netmtr,
                                        'prevcon' => number_format($prev_cons),
                                        'currcon' => number_format($pres_cons),
                                        'percent' => '
                                              <a href="javascript:;" style="display: inline-block; width: 100%;" class="tooltips" data-container="body" data-placement="left" data-original-title="' . $percent_tip . '">
                                              <span class="pull-right ">' . number_format($check->per, 2) . ' %</span>
                                              <span class="pull-left">' . $check->icon . '</span>
                                              </a>
                                             ',
                                        //'rem' => '<select name="findings[' . $sysid . ']" id="findings" class="form-control inline findings" ><option value=""></option>' . $findings_list . '</select>',
                                        //'rem' => $findings_a,
                                        'rem' => $findings,
                                        'controls' => ' <button data-stat="1" id="btn_actual_read" type="button" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Save actual reading"><i class="fa fa-save"></i></button>',
                                        'chckread' => $chk_read,
                                        'addbill' => $add_read,
                                        'stats' => $stat_msg,
                                        'checkbox' => $check_input,
                                        'contype' => $contype,
                                        'rowbg' => $row_color
                                    );


                                    if($print == 1) {
                                        $incdec = $check->per;
                                        $logs_where_arr = array(
                                            'schedid' => $schedid,
                                            'acctid' => $sysid,
                                            'types' => 1,
                                            'status' => 1
                                        );
                                        $this->db->where($logs_where_arr);
                                        $this->db->update('trn_reading_analysis_logs', array('status' => 0));

                                        // INSERT INTO LOGS
                                        $logs_ins_arr = array(
                                            'schedid' => $get_sched_details->sysid,
                                            'acctid' => $sysid,
                                            'mtr' => $row->MTR,
                                            'gdlbid' => $sched_gdlb,
                                            'multid' => $row->MULTID,
                                            'mtrser' => $row->MTRSER,
                                            'mtrno' => $row->MTRNO,
                                            'prvrdg' => $prev_read,
                                            'prsrdg' => $pres_read,
                                            'prvkwh' => $prev_cons,
                                            'prskwh' => $pres_cons,
                                            'demand' => $pres_dems,
                                            'incdec' => $incdec,
                                            'createdby' => user_id(),
                                            'updatedby' => user_id(),
                                            'types' => 1
                                        );
                                        $this->db->insert('trn_reading_analysis_logs', $logs_ins_arr);



                                        $print_html .= '<tr>';
                                        $print_html .= '<td>' . $row->MRSEQ . '</td>';
                                        $print_html .= '<td>' . $row->SERVNO . '</td>';
                                        $print_html .= '<td>' . $name . '</td>';
                                        $print_html .= '<td>' . $addr . '</td>';
                                        $print_html .= '<td>' . $row->MTR . '</td>';
                                        $print_html .= '<td>' . $row->MTRNO . '</td>';
                                        $print_html .= '<td>' . $row->MTRSER . '</td>';
                                        $print_html .= '<td>' . $row->MULTCODE . '</td>';
                                        $print_html .= '<td class="number">' . number_format($prev_read) . '</td>';
                                        //$print_html .= '<td class="number">' . number_format($pres_read) . '</td>';
                                        //$print_html .= '<td class="number">' . number_format($prev_cons) . '</td>';
                                        //$print_html .= '<td class="number">' . number_format($pres_cons) . '</td>';
                                        $print_html .= '<td>' . $check->rem . '</td>';
                                        $print_html .= '<td></td>';
                                        $print_html .= '<td></td>';
                                        $print_html .= '<td></td>';
                                        $print_html .= '</tr>';
                                    }
                                }

                                if( ($print == 2 && $check->addbill == true && $addbill == false) || (in_array($sysid, $acct_ids_arr))) {


                                    $logs_where_arr = array(
                                        'schedid' => $schedid,
                                        'acctid' => $sysid,
                                        'types' => 1,
                                        'status' => 1
                                    );
                                    $this->db->where($logs_where_arr);
                                    $this->db->update('trn_reading_analysis_addbills', array('status' => 0));

                                    // INSERT INTO LOGS
                                    $logs_ins_arr = array(
                                        'schedid' => $get_sched_details->sysid,
                                        'acctid' => $sysid,
                                        'mtr' => $row->MTR,
                                        'gdlbid' => $sched_gdlb,
                                        'multid' => $row->MULTID,
                                        'mtrser' => $row->MTRSER,
                                        'mtrno' => $row->MTRNO,
                                        'prvrdg' => $prev_read,
                                        'prsrdg' => $pres_read,
                                        'prvkwh' => $prev_cons,
                                        'prskwh' => $pres_cons,
                                        'demand' => $pres_dems,
                                        'createdby' => user_id(),
                                        'updatedby' => user_id(),
                                        'types' => 1
                                    );
                                    $this->db->insert('trn_reading_analysis_addbills', $logs_ins_arr);
                                    $addbill_id = $this->db->insert_id();
                                    $data['err_addbils'][] = $this->db->_error_message();

                                    $ins_logs_arr = array(
                                        'addbillid' => $addbill_id,
                                        'remarks' => $check->rem,
                                        'createdby' => user_id()
                                    );
                                    $this->db->insert('trn_reading_analysis_addbills_logs', $ins_logs_arr);
                                    $data['err_addbils_logs'][] = $this->db->_error_message();

                                    // RETURN OUTPUT
                                    $print_html .= '<tr>';
                                    $print_html .= '<td>' . $row->MRSEQ . '</td>';
                                    $print_html .= '<td>' . $row->SERVNO . '</td>';
                                    $print_html .= '<td>' . $name . '</td>';
                                    $print_html .= '<td>' . $row->MTR . '</td>';
                                    $print_html .= '<td>' . $row->MTRNO . '</td>';
                                    $print_html .= '<td>' . $row->MTRSER . '</td>';
                                    $print_html .= '<td>' . $row->MULTCODE . '</td>';
                                    $print_html .= '<td class="number">' . number_format($prev_read) . '</td>';
                                    $print_html .= '<td class="number">' . number_format($pres_read) . '</td>';
                                    $print_html .= '<td class="number">' . number_format($prev_cons) . '</td>';
                                    $print_html .= '<td class="number">' . number_format($pres_cons) . '</td>';
                                    $print_html .= '<td>' . $check->rem . '</td>';
                                    $print_html .= '<td></td>';
                                    $print_html .= '<td></td>';
                                    $print_html .= '<td></td>';
                                    $print_html .= '</tr>';
                                }

                                // ################################################################################################
                                // ################################################################################################
                                // ################################################################################################
                                // BILLING PROCESS
                                if ($showall == 4 && $row_show == false && $for_bill == true) {
                                    $billno = $billno_input++;
                                    $pres_cons_final = ($pres_cons > 0) ? $pres_cons : 0;
                                    $comptute_bill = compute_billing($sysid, $sched_year, $sched_month, $pres_cons_final, $pres_dems, $netmtrval);
                                    //$data['billinfo'][] = $comptute_bill;
                                    $bill_info = $comptute_bill['data'];
                                    $curr = (isset($comptute_bill['curr'])) ? $comptute_bill['curr'] : 0;
                                    $prevamt = (isset($comptute_bill['previous'])) ? $comptute_bill['previous'] : 0;
                                    $totalint = (isset($comptute_bill['interest'])) ? $comptute_bill['interest'] : 0;
                                    $scdisc = (isset($comptute_bill['scdisc'])) ? $comptute_bill['scdisc'] : 0;
                                    $lldamt = (isset($comptute_bill['lldamt'])) ? $comptute_bill['lldamt'] : 0;
                                    $kwh_total = $comptute_bill['kwh'];
                                    $bill_rate = $comptute_bill['ratecode'];

                                    $dolpay = $comptute_bill['dolpay'];

                                    if($input_duedate) {
                                        $duedate = $input_duedate;
                                    } else {
                                        $date_now = date('Y-m-d');
                                        $date = strtotime($date_now);
                                        $date = strtotime("+18 day", $date);
                                        $duedate = date('Y-m-d', $date);
                                    }



                                    $check_if_billed = $this->db->select('acctid')
                                        ->from('billing_reports_main')
                                        ->where(array('acctid' => $sysid, 'schedid' => $schedid))
                                        ->get()->row();

                                    if($check_if_billed==false) {

                                        $bill_yr = date_formating($sched_year, 'Y', 'y');
                                        $bill_temp_ins_arr = array(
                                            'acctid' => $sysid,
                                            'group' => $group,
                                            'dist' => $dist,
                                            'lot' => $lot,
                                            'book' => $book,
                                            'servno' => $row->SERVNO,
                                            'mtr' => $row->MTR,
                                            'name' => $name,
                                            'addr' => $addr,
                                            'prvrdg' => $prev_read,
                                            'prvdte' => $prev_date,
                                            'prsrdg' => $pres_read,
                                            'prsdte' => $pres_date,
                                            'multcd' => $row->MULTCODE,
                                            'kwhuse' => $kwh_total,
                                            'bmo' => $sched_month,
                                            'byr' => $bill_yr,
                                            'month' => $sched_month,
                                            'year' => $sched_year,
                                            'mtrser' => $row->MTRNO,
                                            'serial' => $row->MTRSER,
                                            'load' => $acct_load,
                                            'rate' => $bill_rate,
                                            'genamt' => (isset($bill_info[1]['lists'][0]['amt'])) ? $bill_info[1]['lists'][0]['amt'] : 0,
                                            'genamt1' => 0,
                                            'trnamt' => (isset($bill_info[1]['lists'][3]['amt'])) ? $bill_info[1]['lists'][3]['amt'] : 0,
                                            'disamt' => (isset($bill_info[0]['lists'][0]['amt'])) ? $bill_info[0]['lists'][0]['amt'] : 0,
                                            'demamt' => (isset($bill_info[0]['lists'][1]['amt'])) ? $bill_info[0]['lists'][1]['amt'] : 0,
                                            'supamt' => (isset($bill_info[0]['lists'][2]['amt'])) ? $bill_info[0]['lists'][2]['amt'] : 0,
                                            'supper' => (isset($bill_info[0]['lists'][5]['amt'])) ? $bill_info[0]['lists'][5]['amt'] : 0,
                                            'mtramt' => (isset($bill_info[0]['lists'][3]['amt'])) ? $bill_info[0]['lists'][3]['amt'] : 0,
                                            'slamt' => (isset($bill_info[1]['lists'][4]['amt'])) ? $bill_info[1]['lists'][4]['amt'] : 0,
                                            'iccamt' => (isset($bill_info[2]['lists'][0]['amt'])) ? $bill_info[2]['lists'][0]['amt'] : 0, // NOT SURE HERE
                                            'iccsub' => (isset($bill_info[2]['lists'][0]['amt'])) ? $bill_info[2]['lists'][0]['amt'] : 0, // NOT SURE HERE
                                            'llramt' => (isset($bill_info[2]['lists'][1]['amt'])) ? $bill_info[2]['lists'][1]['amt'] : 0, // NOT SURE HERE
                                            'llrsub' => (isset($bill_info[2]['lists'][1]['amt'])) ? $bill_info[2]['lists'][1]['amt'] : 0, // NOT SURE HERE
                                            'lldamt' => $lldamt,
                                            'misamt' => (isset($bill_info[3]['lists'][4]['amt'])) ? $bill_info[3]['lists'][4]['amt'] : 0,
                                            'envamt' => (isset($bill_info[3]['lists'][5]['amt'])) ? $bill_info[3]['lists'][5]['amt'] : 0,
                                            'framt' => (isset($bill_info[3]['lists'][3]['amt'])) ? $bill_info[3]['lists'][3]['amt'] : 0,
                                            'npcamt' => (isset($bill_info[3]['lists'][6]['amt'])) ? $bill_info[3]['lists'][6]['amt'] : 0,
                                            'iccsamt' => (isset($bill_info[3]['lists'][7]['amt'])) ? $bill_info[3]['lists'][7]['amt'] : 0,
                                            'papc' => (isset($bill_info[1]['lists'][2]['amt'])) ? $bill_info[1]['lists'][2]['amt'] : 0,
                                            'fitamt' => (isset($bill_info[3]['lists'][8]['amt'])) ? $bill_info[3]['lists'][8]['amt'] : 0,
                                            'genchg' => (isset($bill_info[1]['lists'][0]['rate'])) ? $bill_info[1]['lists'][0]['rate'] : 0,
                                            'genchg1' => 0,
                                            'trnchg' => ((isset($bill_info[1]['lists'][3]['rate'])) && $bill_info[1]['lists'][3]['rate'] > 0) ? $bill_info[1]['lists'][3]['rate'] : 0,
                                            'dischg' => ((isset($bill_info[0]['lists'][0]['rate'])) && $bill_info[0]['lists'][0]['rate'] > 0) ? $bill_info[0]['lists'][0]['rate'] : 0,
                                            'demchg' => ((isset($bill_info[0]['lists'][1]['rate'])) && $bill_info[0]['lists'][1]['rate'] > 0) ? $bill_info[0]['lists'][1]['rate'] : 0,
                                            'supchg' => ((isset($bill_info[0]['lists'][2]['rate'])) && $bill_info[0]['lists'][2]['rate'] > 0) ? $bill_info[0]['lists'][2]['rate'] : 0,
                                            'mtrchg' => ((isset($bill_info[0]['lists'][3]['rate'])) && $bill_info[0]['lists'][3]['rate'] > 0) ? $bill_info[0]['lists'][3]['rate'] : 0,
                                            'mtrper' => ((isset($bill_info[0]['lists'][5]['rate'])) && $bill_info[0]['lists'][5]['rate'] > 0) ? $bill_info[0]['lists'][5]['rate'] : 0,
                                            'slchg' => ((isset($bill_info[1]['lists'][4]['rate'])) && $bill_info[1]['lists'][4]['rate'] > 0) ? $bill_info[1]['lists'][4]['rate'] : 0,
                                            'mischg' => ((isset($bill_info[3]['lists'][4]['rate'])) && $bill_info[3]['lists'][4]['rate'] > 0) ? $bill_info[3]['lists'][4]['rate'] : 0,
                                            'envchg' => ((isset($bill_info[3]['lists'][5]['rate'])) && $bill_info[3]['lists'][5]['rate'] > 0) ? $bill_info[3]['lists'][5]['rate'] : 0,
                                            'npcchg' => ((isset($bill_info[3]['lists'][6]['rate'])) && $bill_info[3]['lists'][6]['rate'] > 0) ? $bill_info[3]['lists'][6]['rate'] : 0,
                                            'iccschg' => ((isset($bill_info[3]['lists'][7]['rate'])) && $bill_info[3]['lists'][7]['rate'] > 0) ? $bill_info[3]['lists'][7]['rate'] : 0,
                                            'fitchg' => ((isset($bill_info[3]['lists'][3]['rate'])) && $bill_info[3]['lists'][3]['rate'] > 0) ? $bill_info[3]['lists'][3]['rate'] : 0,
                                            'papcchg' => ((isset($bill_info[1]['lists'][2]['rate'])) && $bill_info[1]['lists'][2]['rate'] > 0) ? $bill_info[1]['lists'][2]['rate'] : 0,
                                            'genvat' => (isset($bill_info[3]['lists'][0]['amt'])) ? $bill_info[3]['lists'][0]['amt'] : 0,
                                            'trnvat' => (isset($bill_info[3]['lists'][1]['amt'])) ? $bill_info[3]['lists'][1]['amt'] : 0,
                                            'disvat' => (isset($bill_info[3]['lists'][2]['amt'])) ? $bill_info[3]['lists'][2]['amt'] : 0,
                                            'slvat' => 0,  // ADD MODULE FOR THIS
                                            'othvat' => 0, // ADD MODULE FOR THIS
                                            'appsur' => 0, // ADD MODULE FOR THIS
                                            'surbal' => 0, // ADD MODULE FOR THIS
                                            'current' => $curr,
                                            'overdue' => $prevamt,
                                            'totacc' => ($curr + $prevamt + $totalint),
                                            'totint' => $totalint,
                                            'scdisc' => $scdisc, // ADD MODULE FOR THIS
                                            'dolpay' => $dolpay,
                                            'batch' => ($add_bill_proc) ? 'A' : 'REG',
                                            'billno' => $billno,
                                            'duedate' => $duedate,
                                            'createdby' => user_id(),
                                            'schedid' => $schedid
                                        );
                                        $num_rows_ok += 1;
                                        $ins = $this->db->insert('billing_reports', $bill_temp_ins_arr);
                                        $err_msg = $this->db->_error_message();
                                        if ($ins == true) {
                                            /*
                                            $gen_vat = (isset($bill_info[3]['lists'][0]['amt'])) ? $bill_info[3]['lists'][0]['amt'] : 0;
                                            $trn_vat = (isset($bill_info[3]['lists'][1]['amt'])) ? $bill_info[3]['lists'][1]['amt'] : 0;
                                            $dis_vat = (isset($bill_info[3]['lists'][2]['amt'])) ? $bill_info[3]['lists'][2]['amt'] : 0;

                                            $total_vat = ($gen_vat + $trn_vat + $dis_vat);

                                            $bill_main_ins_arr = array(
                                                'billno' => $billno,
                                                'acctid' => $sysid,
                                                'group' => $group,
                                                'dist' => $dist,
                                                'lot' => $lot,
                                                'book' => $book,
                                                'servno' => $row->SERVNO,
                                                'mtr' => $row->MTR,
                                                'mtrser' => $row->MTRNO,
                                                'serial' => $row->MTRSER,
                                                'year' => $sched_year,
                                                'month' => $sched_month,
                                                'bmo' => $sched_month,
                                                'byr' => $bill_yr,
                                                'load' => $acct_load,
                                                'rate' => $bill_rate,
                                                'prvrdg' => $prev_read,
                                                'prvdte' => $prev_date,
                                                'prsrdg' => $pres_read,
                                                'prsdte' => $pres_date,
                                                'duedate' => $duedate,
                                                'multcd' => $row->MULTCODE,
                                                'kwhuse' => $kwh_total,
                                                'surbal' => null,
                                                'current' => $curr,
                                                'overdue' => $prevamt,
                                                'totacc' => ($curr + $prevamt + $totalint),
                                                'totint' => $totalint,
                                                'scdisc' => $scdisc, // ADD MODULE FOR THIS
                                                'totalvat' => $total_vat,
                                                'dolpay' => $dolpay,
                                                'batch' => ($add_bill_proc) ? 'A' : 'REG',
                                                'createdby' => user_id(),
                                                'schedid' => $schedid
                                            );
                                            */
                                            //$this->db->insert('billing_reports_main', $bill_main_ins_arr);
                                            //$data['err_rep_main'][] = $this->db->_error_message();

                                            // UPDATE AR
                                            $upd = update_ar($sysid, $sched_month, $curr, round($kwh_total, 0), $billno);
                                        }
                                    }
                                    $data['error_message'][$sysid] = array(
                                        'val' => (isset($bill_info[0]['lists'][1]['rate'])) ? $bill_info[0]['lists'][1]['rate'] : 0,
                                        'err' => $err_msg);

                                }

                                // #################################################################################################
                                // #################################################################################################
                                // #################################################################################################
                                // BILLING VIEW
                                if ($showall == 2 && $row_show == false) {
                                    $billingcnt += 1;

                                    $mtrid = '<input type="hidden" name="mtrid[' . $sysid . ']" value="' . $row->MTRNO . '" id="mtrid"/>';
                                    $acctid_input = '<input type="hidden" name="" value="' . $sysid . '" id="acctid"/>';
                                    $schedid_input = '<input type="hidden" name="" value="' . $schedid . '" id="schedid"/>';
                                    $read_input = '<input type="hidden" name="" value="' . $readid . '" id="readid"/>';

                                    $findings_id = 0;
                                    $qry_trn_findigns = $this->db->select('findingid')
                                        ->from('trn_reading_findings')
                                        ->where(array('readingid' => $readid, 'status' => 1))
                                        ->order_by('dateupdated', 'desc')
                                        ->get()->row();
                                    if ($qry_trn_findigns) {
                                        $findings_id = $qry_trn_findigns->findingid;
                                    }
                                    $finding = get_findings_label($findings_id);
                                    $pres_cons_final = ($pres_cons > 0) ? $pres_cons : 0;

                                    $data['computedata'][] = array(
                                        'acctid' => $sysid,
                                        'year' => $sched_year,
                                        'month' => $sched_month,
                                        'kwhuse' => $pres_cons_final,
                                        'demkwh' => $pres_dems,
                                        'netmtrval' => $netmtrval
                                    );

                                    $comptute_bill = compute_billing($sysid, $sched_year, $sched_month, $pres_cons_final, $pres_dems, $netmtrval);

                                    $curr = (isset($comptute_bill['curr'])) ? $comptute_bill['curr'] : 0;
                                    $total_kwh = (isset($comptute_bill['kwh'])) ? $comptute_bill['kwh'] : $pres_cons;
                                    $scicon = (isset($comptute_bill['scdisc']) && $comptute_bill['scdisc'] < 0) ? '<span class="label label-danger pull-right">SC</span>' : '';

                                    $class = $row->CLASSCODE;

                                    // CHECK IF ALREADY BILLED
                                    $billled = '';

                                    $add_bill_stats = ($add_bill_proc) ? '<code>A</code>' : '';

                                    $check_if_billed = $this->db->select('acctid')
                                        ->from('billing_reports_main')
                                        ->where(array('acctid' => $sysid, 'schedid' => $schedid))
                                        ->get()->row();

                                    $row_color = ($check_if_billed) ? 'success' : '';

                                    $legacy_curr = 0;
                                    $diff = 0;
                                    if($register == true) {
                                        if((PHP_OS == 'Linux')) {
                                            if (pecoapps_conn()) {
                                                $conn = $this->load->database('pecoapps', TRUE);
                                                $conn->initialize();
                                                $qry_billtrn = $conn->query("
                                                SELECT SUM(ISNULL(current___, 0)) AS curr FROM billtrn 
                                                WHERE yr________ = $sched_year AND m_________ = $sched_month
                                                AND servno____ = '{$row->SERVNO}'
                                            ")->row();
                                                $legacy_curr = ($qry_billtrn) ? $qry_billtrn->curr : 0;
                                                $diff = $curr - $legacy_curr;
                                            }
                                        }
                                    }

                                    $data['list'][] = array(
                                        'num' => '',
                                        'seq' => '<i data-toggle="collapse" data-target="#expand_' . $sysid . '" data-id="' . $sysid . '" data-sched="' . $schedid . '" id="btn-expand" class="fa fa-plus-square-o"></i>' . $mtrid . $acctid_input . $schedid_input . $read_input . $sched_year_input . $sched_month_input,
                                        'serviceno' => $row->SERVNO,
                                        'name' => $name,
                                        'meter' => $row->MTR,
                                        'meterno' => $row->MTRNO,
                                        'serial' => $row->MTRSER,
                                        'mult' => '<span class="text-bold">' . $row->MULTCODE . '</span><span class="pull-right">' . number_format($row->MULTIPLIER, 2) . '</span>',
                                        'prevread' => round($prev_read, 2),
                                        'curread' => round($pres_read, 2),
                                        'curdem' => $demand,
                                        'netmet' => $netmtr,
                                        'prevcon' => round($prev_cons, 0),
                                        'currcon' => round($total_kwh, 0),
                                        'percent' => '
                                              <a href="javascript:;" style="display: inline-block; width: 100%;" class="tooltips" data-container="body" data-placement="left" data-original-title="'.$percent_tip.'">
                                              <span class="pull-right ">' . number_format($check->per, 2) . ' %</span>
                                              <span class="pull-left">' . $check->icon . '</span>
                                              </a>
                                             ',
                                        'rem' => $class . $scicon,
                                        'current' => number_format($curr, 2) . $billled,
                                        'totalamt' => number_format($curr, 2) . $billled,
                                        'controls' => $bill_preview_stat,
                                        'chckread' => $chk_read,
                                        'addbill' => $add_read,
                                        'stats' => $bill_preview_stat. ' ' .$add_bill_stats,
                                        'checkbox' => $check_input,
                                        'contype' => $contype,
                                        'rowbg' => $row_color,
                                        'legacycur' => number_format($legacy_curr, 2),
                                        'diff' => number_format($diff, 2),
                                    );
                                    $totalamt += $curr;
                                    $totalamt_legacy += $legacy_curr;
                                    $totalamt_diff += $diff;

                                }

                                if ($showall == 3) {
                                    $mtrid = '<input type="hidden" name="mtrid[' . $row->SYSID . ']" value="' . $row->MTRNO . '" id="mtrid"/>';
                                    $acctid_input = '<input type="hidden" name="" value="' . $row->SYSID . '" id="acctid"/>';
                                    $schedid_input = '<input type="hidden" name="" value="' . $schedid . '" id="schedid"/>';
                                    $read_input = '<input type="hidden" name="" value="' . $readid . '" id="readid"/>';

                                    $findings_id = 0;
                                    $qry_trn_findigns = $this->db->select('findingid')
                                        ->from('trn_reading_findings')
                                        ->where(array('readingid' => $readid, 'status' => 1))
                                        ->order_by('dateupdated', 'desc')
                                        ->get()->row();
                                    if ($qry_trn_findigns) {
                                        $findings_id = $qry_trn_findigns->findingid;
                                    }

                                    $qry_findings_list = $this->get_list_findings();
                                    $findings_list = '';
                                    if ($qry_findings_list) {
                                        foreach ($qry_findings_list as $frow) {
                                            $selected = '';
                                            if ($qry_trn_findigns) {
                                                if ($frow->sysid == $findings_id) {
                                                    $selected = 'selected';
                                                }
                                            }
                                            $findings_list .= '<option ' . $selected . ' value="' . $frow->sysid . '">' . $frow->codes . ' - ' . $frow->descriptions . '</option>';
                                        }
                                    }


                                    $reading = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips" style="color: red;" data-container="body" data-placement="left" data-original-title="Enter Reading Number"></i>' .
                                        '<input autocomplete="off" name="reading[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline reading" style="width: 100%;" id="reading" value="' . round($pres_read, 2) . '"/>' .
                                        '</div>';

                                    $prevreading = '<div class="input-icon left">' .
                                        '<i class="fa fa-pencil tooltips" style="color: red;" data-container="body" data-placement="left" data-original-title="Enter Reading Number"></i>' .
                                        '<input autocomplete="off" name="prevreading[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline prevreading" style="width: 100%;" id="prevreading" value="' . round($prev_read, 2) . '"/>' .
                                        '</div>';

                                    $data['list'][] = array(
                                        'seq' => btn_expand($sysid) . $mtrid . $acctid_input . $schedid_input . $read_input,
                                        'schedid' => $schedid,
                                        'acctid' => $sysid,
                                        'serviceno' => $row->SERVNO,
                                        'readid' => $readid,
                                        'name' => $name,
                                        'meter' => $row->MTR,
                                        'meterno' => $row->MTRNO,
                                        'serial' => $row->MTRSER,
                                        'mult' => '<span class="text-bold">' . $row->MULTCODE . '</span><span class="pull-right">' . number_format($row->MULTIPLIER, 2) . '</span>',
                                        'prevread' => $prevreading,
                                        'curread' => $reading,
                                        'curdem' => $demand,
                                        'netmet' => $netmtr,
                                        'prevcon' => number_format($prev_cons),
                                        'currcon' => number_format($pres_cons),
                                        'percent' => '
                                              <a href="javascript:;" style="display: inline-block; width: 100%;" class="tooltips" data-container="body" data-placement="left" data-original-title="Tooltip in left">
                                              <span class="pull-right ">' . number_format($check->per, 2) . ' %</span>
                                              <span class="pull-left">' . $check->icon . '</span>
                                              </a>
                                             ',
                                        //'rem' => '<select name="findings[' . $sysid . ']" id="findings" class="form-control inline findings" ><option value=""></option>' . $findings_list . '</select>',
                                        'rem' => '<a style="margin:0px 0px !important;" href="javascript:;" id="findings_input" data-type="findings" data-pk="'.$row->MTRNO.'" data-value="" data-original-title="Select Findings">Findings..</a>',
                                        'controls' => ' <button data-stat="1" id="btn_actual_read" type="button" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Save actual reading"><i class="fa fa-save"></i></button>',
                                        'chckread' => $chk_read,
                                        'addbill' => $add_read,
                                        'stats' => $stat_msg,
                                        'checkbox' => $check_input,
                                        'contype' => $contype,
                                        'rowbg' => $row_color
                                    );
                                }

                            }
                        }
                        if ($totalprskwh > 0) {
                            $avkwhcurr = $totalprskwh / $readcnt;
                        }


                        if ($totalprvkwh > 0) {
                            $avkwhprev = $totalprvkwh / $num_rows;
                        }

                        if ($billing == true
                            // && $num_rows_ok == $num_rows_insert
                            // && $billingcnt == $num_rows // COMMENT OUT THIS : TO FORCE BILL SPECIFIC ACCOUNTS W/ OK FOR BILLING
                            // add adition condition here
                        ) {
                            $this->db->trans_commit();
                            $func = 'success';
                            $msg = 'Process complete: ' . $num_rows_insert;

                        } else {
                            $cntdiff = $num_rows - $billingcnt;
                            $this->db->trans_rollback();
                            $func = 'info';
                            $msg = 'Please complete analysis process;<br>There are: ' . $cntdiff . ' RE-CHECK remaining, ' . $num_rows_ok . ' okay for billing!';
                        }

                        $qry = true;
                    }else{
                        $qry = false;
                        $msg = 'Please encode billig rate for the year ' . $sched_year . ' and month of ' . date_formating($sched_month, '!m', 'M');
                        $func = 'info';
                    }

                }else{
                    $qry = false;
                    $msg = 'Schedule not found: ' . $schedid;
                    $func = 'warning';
                }
            }

        }else{
            $msg = 'Select data to display!';
            $func = 'warning';
            $qry = false;
            $num_rows = 0;
        }

        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['reader'] = $reader;


        if($print==1) {
            $reptitle = 'Reading Analysis Reports';
            $header = peco_print_header($readerid, $reptitle, 'MRD', false);

            $userinfo = get_users_info(user_id());

            $data['header'] = $header;
            $data['dates'] = $sched_date;
            $data['gdlb'] = $gdlb_name;
            $data['html'] = $print_html;
            $data['printedby'] = $userinfo->lastname . ', ' . $userinfo->firstname;
            $data['dateprinted'] = date('Y-m-d H:i:s');
        }

        if($print==2) {
            $reptitle = 'For Addbill Reports';
            $header = peco_print_header($readerid, $reptitle, 'MRD', false);

            $userinfo = get_users_info(user_id());

            $data['header'] = $header;
            $data['dates'] = $sched_date;
            $data['gdlb'] = $gdlb_name;
            $data['html'] = $print_html;
            $data['printedby'] = $userinfo->lastname . ', ' . $userinfo->firstname;
            $data['dateprinted'] = date('Y-m-d H:i:s');
        }

        $register_html = '';
        if($register == true) {
            $rep_title = 'BILLING REGISTER';
            $register_html .= peco_print_header(user_id(), $rep_title, 'BREG', false);
            $register_html .= '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">Per District: <b>'.$gdlb_name.'</b></div>';
            $register_html .= '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">DATE: '.date("m/d/Y").'</div>';
            $register_html .= '<hr style="border: 1px dashed #333; margin: 0px 0px;">';
        }


        $data['registerheader'] = $register_html;

        $data['totalprskwh'] = number_format($totalprskwh);
        $data['totalprvkwh'] = number_format($totalprvkwh);
        $data['forbilling'] = number_format($forbilling);
        $data['recheck'] = number_format($recheck);
        $data['zero'] = number_format($zero);
        $data['totalamt'] = number_format($totalamt, 2);
        $data['totalamtlegacy'] = number_format($totalamt_legacy, 2);
        $data['totalamtdiff'] = number_format($totalamt_diff, 2);
        $data['avkwhcurr'] = number_format($avkwhcurr);
        $data['avkwhprev'] = number_format($avkwhprev);
        $data['cntread'] = number_format($readcnt);
        $data['cnt'] = number_format($num_rows);
        $data['addbill'] = number_format($zero);
        $data['billcnt'] = $billingcnt;
        $data['cnterr'] = $cnterr;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_for_addbill_list() {
        $data = array();
        $schedid = $this->input->post('schedid');
        $exec = $this->input->post('exec');
        $qry = false;
        $num_rows = 0;
        $updated = 0;
        // SCHED DETAILS
        $get_sched_details = $this->db->select('
                sm.sysid, 
                sm.gdlbid, 
                sm.months, 
                sm.years, 
                sm.datesched, 
                sm.status
            ')
            ->from('reading_schedule_main AS sm')
            ->join('reading_schedule_reader AS sr', 'sm.sysid = sr.schedid')
            ->where(array('sm.sysid' => $schedid, 'sr.status' => 1))
            ->get()->row();
        if($get_sched_details) {
            $sched_year = $get_sched_details->years;
            $sched_month = $get_sched_details->months;
            // ####################################
            // CHECK SPECIFIC
            $query = $this->db->query("
                        SELECT
                            raa.sysid AS SYSID,
                            am.sysid AS ACCTID,
                            am.servicenumber AS SERVNO,
                            am.types AS OWNERTYPE,
                            am.ownerid AS OWNERID,
                            am.mtr AS MTR,
                            am.mtrno AS MTRNO,
                            am.mtrserial AS MTRSER,
                            am.rateclassid AS RATEID,
                            am.netmtr AS NETMTR,
                            bm.sysid AS MULTID,
                            bm.codes AS MULTCODE,
                            bm.rate AS MULTIPLIER,
                            class.codes AS CLASSCODE,
                            raa.prvrdg AS PRVRDG,
                            raa.prsrdg AS PRSRDG,
							raa.demand AS DEMAND,
							raa.prvkwh AS PRVKWH,
							raa.prsKWH AS PRSKWH,
							raa.kwhuse AS KWHUSE
                        FROM
                            customer_accounts_main AS am
                            JOIN trn_reading_analysis_addbills AS raa ON raa.acctid = am.sysid
                            LEFT JOIN billing_rates_main_multiplier AS bm ON bm.sysid = am.multid
                            LEFT JOIN rate_class_specification AS class ON class.sysid = am.rateclassid AND class.`status` = 1
                        WHERE
                            am.`status` = 1 AND raa.`status` > 0 AND raa.schedid = $schedid
                        GROUP BY
                            am.sysid,
                            am.servicenumber,
                            am.types,
                            am.ownerid,
                            am.mtr,
                            am.mtrno,
                            am.mtrserial,
                            am.rateclassid,
                            am.netmtr,
                            bm.sysid,
                            bm.codes,
                            bm.rate,
                            class.codes,
							raa.sysid,
                            raa.prvrdg,
                            raa.prsrdg,
							raa.prvkwh,
							raa.prsKWH,
							raa.kwhuse
                        ORDER BY
                            am.servicenumber
                     ");
            $num_rows = $query->num_rows();
            if ($num_rows > 0) {
                $qry = true;
                foreach ($query->result() as $row) {
                    $sysid = $row->SYSID;

                    // OWNER INFO
                    $name_arr = get_ownership_details($row->OWNERTYPE, $row->OWNERID);
                    $name = ($name_arr) ? ucwords(strtolower($name_arr->name)) : '';

                    $prev_cons = $row->PRVKWH;
                    $pres_cons = $row->PRSKWH;
                    $ave_cons = $row->KWHUSE;


                    // READINGS
                    $qry_read_log = $this->db->select('sysid, reading, demand')
                        ->from('customer_accounts_subscription_meter_reading_logs')
                        ->where(array('acctid' => $row->ACCTID, 'mtrid' => $row->MTRNO, 'schedid' => $schedid, 'status' => 1, 'types >= ' => 2))
                        ->get()->row();

                    // if($qry_read_log) {

                    $readid = ($qry_read_log) ? $qry_read_log->sysid : 0;

                    $mtrid = '<input type="hidden" name="mtrid[' . $row->ACCTID . ']" value="' . $row->MTRNO . '" id="mtrid"/>';
                    $acctid_input = '<input type="hidden" name="" value="' . $row->ACCTID . '" id="acctid"/>';
                    $schedid_input = '<input type="hidden" name="" value="' . $schedid . '" id="schedid"/>';
                    $sched_year_input = '<input id="input_year" type="hidden" value="' . $sched_year . '" />';
                    $sched_month_input = '<input id="input_month" type="hidden" value="' . $sched_month . '" />';

                    $control = '';
                    $addbill = '';
                    $prsread = '';
                    if ($ave_cons > 0) {
                        $control .= '<i class="fa fa-check text-success"></i>';
                        $addbill = number_format($ave_cons);
                        $prsread = $row->PRVRDG;
                    } else {
                        if ($ave_cons == NULL) {

                            $control .= '<i class="fa fa-times text-danger"></i>';
                            $prsread = '<div class="input-icon left">' .
                                '<i class="fa fa-pencil" style="color: red; "></i>' .
                                '<input class="form-control inline input-xs text-danger" style="text-align: right !important;" value="'.$row->PRVRDG.'" id="input_addbil_kwh" />' .
                                '</div>';

                        } else {

                            $control = '<button class="btn btn-warning inline btn-xs" type="button" id="btn_save_new_read"><i class="fa fa-save"></i></button>';
                            $prsread = '<div class="input-icon left">' .
                                '<i class="fa fa-pencil" style="color: red; "></i>' .
                                '<input class="form-control inline input-xs text-danger" style="text-align: right !important;" value="'.$row->PRVRDG.'" id="input_addbil_kwh" />' .
                                '</div>';
                            $addbill = 0;

                        }
                    }

                    $av_kwh = 0;
                    if ($exec && $exec == 1 && $ave_cons == NULL) {
                        $qry_av = get_account_prevbilling($row->ACCTID, 12);
                        if ($qry_av->qry == true) {
                            $remarks = 'AVERAGE KWH';
                            $av_kwh = $qry_av->ave;
                            foreach ($qry_av->res as $arow) {
                                // UPDATE FIRST
                                $this->db->where(array('addbillid' => $sysid, 'status' => 1, 'months' => $arow->month, 'years' => $arow->year));
                                $this->db->update('trn_reading_analysis_addbills_ranges', array('status' => 0, 'updatedby' => user_id()));

                                $ins_range_arr = array(
                                    'addbillid' => $sysid,
                                    'kwhuse' => $arow->kwhuse,
                                    'months' => $arow->month,
                                    'years' => $arow->year,
                                    'createdby' => user_id(),
                                    'updatedby' => user_id(),
                                    'types' => 1,
                                );
                                $this->db->insert('trn_reading_analysis_addbills_ranges', $ins_range_arr);
                            }
                        } else {
                            $remarks = 'ZERO KWH';
                        }

                        $ins_logs_arr = array(
                            'addbillid' => $sysid,
                            'remarks' => $remarks,
                            'createdby' => user_id()
                        );
                        $this->db->insert('trn_reading_analysis_addbills_logs', $ins_logs_arr);

                        $this->db->where(array('sysid' => $sysid));
                        $upd = $this->db->update('trn_reading_analysis_addbills', array('kwhuse' => $av_kwh, 'status' => 2));
                        if ($upd) {
                            $updated += 1;
                        }

                        if ($av_kwh > 0) {
                            $control = '<i class="fa fa-check text-success"></i>';
                            $addbill = number_format($av_kwh);
                        } else {
                            if ($av_kwh > 0) {
                                $control .= '<i class="fa fa-times text-danger"></i>';
                                $prsread = '<div class="input-icon left">' .
                                    '<i class="fa fa-pencil" style="color: red; "></i>' .
                                    '<input class="form-control inline input-xs text-danger" style="text-align: right !important;" value="'.$row->PRVRDG.'" id="input_addbil_kwh" />' .
                                    '</div>';

                            } else {

                                $control = '<button class="btn btn-warning inline btn-xs" type="button" id="btn_save_new_read"><i class="fa fa-save"></i></button>';
                                $prsread = '<div class="input-icon left">' .
                                    '<i class="fa fa-pencil" style="color: red; "></i>' .
                                    '<input class="form-control inline input-xs text-danger" style="text-align: right !important;" value="'.$row->PRVRDG.'" id="input_addbil_kwh" />' .
                                    '</div>';
                                $addbill = 0;

                            }
                        }
                    }

                    $data['list'][] = array(
                        'seq' => btn_expand($row->ACCTID) . $mtrid . $acctid_input . $schedid_input . $sched_year_input . $sched_month_input,
                        'schedid' => $schedid,
                        'acctid' => $row->ACCTID,
                        'serviceno' => $row->SERVNO,
                        'name' => $name,
                        'meter' => $row->MTR,
                        'meterno' => $row->MTRNO,
                        'serial' => $row->MTRSER,
                        'mult' => '<span class="text-bold">' . $row->MULTCODE . '</span><span class="pull-right">' . number_format($row->MULTIPLIER, 2) . '</span>',
                        'prevread' => $prsread,
                        'curread' => $row->PRSRDG,
                        'curdem' => $row->DEMAND,
                        'netmet' => '<code>N/A</code>',
                        'prevcon' => number_format($prev_cons),
                        'currcon' => number_format($pres_cons),
                        'addbill' => $addbill,
                        'check' => $control,
                        'controls' => $control,
                    );
                }


                // }
            }
        }
        $data['updated'] = $updated;
        $data['cnt'] = $num_rows;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function submit_actual_reading_row() {
        $data = array();
        $inputs = $this->input->post();
        $readid = $this->input->post('readid');
        $acctid = $this->input->post('acctid');
        $schedid = $this->input->post('schedid');
        $reading = $this->input->post('value');
        $demand = $this->input->post('demand');
        $type = $this->input->post('type');
        $rem = $this->input->post('remarks');
        $rem = ($rem) ? $rem : NULL;

        if ( $readid ) {
            if ($type == 1) {
                $ins_arr = array(
                    'readid' => $readid,
                    'updatedby' => user_id(),
                    'createdby' => user_id(),
                    'remarks' => $rem
                );
                $this->db->insert('trn_reading_override', $ins_arr);
            } else {
                $where_arr = array(
                    'readid' => $readid,
                );
                $this->db->where($where_arr);
                $this->db->update('trn_reading_override', array('status' => 0));
            }
        } else {
            $data['msg']['stat'] = 'New Reading';
            $qry_logs_info = $this->db->select()
                ->from('reading_schedule_meters_logs')
                ->where(array('acctid' => $acctid, 'schedid' => $schedid, 'status' => 1))
                ->get()->row();
            if($qry_logs_info) {
                $read_id_arr = array();

                $mtrno = $qry_logs_info->mtrno;
                $this->db->where(array('status' => 1, 'acctid' => $acctid, 'mtrid' => $mtrno));
                $this->db->update('customer_accounts_subscription_meter_reading_logs', array('updatedby' => user_id(), 'status' => 0));

                $qry_prev_read = $this->db->select('sysid')
                    ->from('customer_accounts_subscription_meter_reading_logs')
                    ->where(array('acctid' => $acctid, 'mtrid' => $mtrno))
                    ->get();
                if($qry_prev_read->num_rows()) {
                    foreach($qry_prev_read->result() as $rrow) {
                        $read_id_arr[] = $rrow->sysid;
                    }
                }

                $data['readids'] = $read_id_arr;

                $ins_arr = array(
                    'reading' => $reading,
                    'demand' => ($demand) ? $demand : null,
                    'mtrid' => $mtrno,
                    'acctid' => $acctid,
                    'schedid' => $schedid,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('customer_accounts_subscription_meter_reading_logs', $ins_arr);
                $readid = $this->db->insert_id();
                $data['ins_err'] = $this->db->_error_message();
                $data['newreadid'] = $readid;

                $this->db->where_in('readid', $read_id_arr);
                $this->db->update('trn_reading_override', array('status' => 0, 'updatedby' => user_id()));

                $this->db->insert('trn_reading_override', array('readid' => $readid, 'createdby' => user_id(), 'updatedby' => user_id(), 'remarks' => $rem));
            }else{
                $data['msg']['logs'] = 'Cannot find logs!';
            }
        }

        $data['type'] = $type;
        $data['inputs'] = $inputs;
        return json_encode($data);
    }

    function get_reading_recheck() {
        $data = array();
        $schedid = $this->input->post('schedid');
        //$schedid = 4;
        $query = $this->db->select("
                    mr.mrseq AS SEQ,
                    tal.acctid AS SYSID,
                    tal.mtr AS MTR,
                    tal.mtrno AS MTRNO,
                    tal.mtrser AS MTRSER,
                    tal.prsrdg AS PRSRDG,
                    tal.demand AS DEMAND,
                    tal.remarks AS REMARKS,
                    acct.types AS OWNERTYPE,
                    acct.servicenumber AS SERVNO,
                    acct.ownerid AS OWNERID,
                    acct.rateclassid AS RATEID
                    ")
            ->from('trn_reading_analysis_logs AS tal')
            ->join('customer_accounts_main AS acct', 'acct.sysid = tal.acctid')
            ->join('customer_accounts_mtrseq AS mr', 'mr.acctid = acct.sysid AND mr.status = 1', 'left')
            ->where(array('tal.schedid' => $schedid, 'tal.types' => 2, 'tal.status' => 1))
            ->group_by('acct.servicenumber')
            ->order_by('mr.mrseq')
            ->get();
        if($query->num_rows()>0) {
            foreach ($query->result() as $row) {
                $sysid = $row->SYSID;
                $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;
                $pres_dems = $row->DEMAND;

                // READINGS
                $qry_read_log = $this->db->select('sysid, reading, demand')
                    ->from('customer_accounts_subscription_meter_reading_logs')
                    ->where(array('acctid' => $row->SYSID, 'mtrid' => $row->MTRNO, 'schedid' => $schedid, 'status' => 1, 'types' => 2))
                    ->get()->row();

                $qry_read_temp = $this->db->select('sysid, reading, demand')
                    ->from('customer_accounts_subscription_meter_reading_temp')
                    ->where(array('acctid' => $row->SYSID, 'mtrid' => $row->MTRNO, 'schedid' => $schedid, 'status' => 1, 'types' => 2))
                    ->get()->row();

                // GET CLASS RATE GROUP
                $qry_rateclass_group = $this->db->select('rs.descs')
                    ->from('rate_class_specification AS rs')
                    ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                    ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                    ->get()->row();

                if ($qry_rateclass_group) {
                    $demandval_temp = ($qry_read_temp) ? $qry_read_temp->demand : '';
                    $demandval = ($qry_read_log) ? $qry_read_log->demand : $demandval_temp;
                    $demand = '<div class="input-icon left">' .
                        '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                        '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $demandval . '"/>' .
                        '</div>';
                } else {
                    $demand = '<span class="label label-danger">N/A</span>';
                }

                $readval_temp = ($qry_read_temp) ? $qry_read_temp->reading : '';
                $readid_temp = ($qry_read_temp) ? $qry_read_temp->sysid : '';

                $readval = ($qry_read_log) ? $qry_read_log->reading : $readval_temp;
                $readid = ($qry_read_log) ? $qry_read_log->sysid : $readid_temp;

                $reading = '<div class="input-icon left">' .
                    '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                    '<input autocomplete="off" name="reading[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline reading" style="width: 100%;" id="reading" value="' . $readval . '"/>' .
                    '</div>';

                $mtrid = '<input type="hidden" name="mtrid[' . $row->SYSID . ']" value="' . $row->MTRNO . '" id="mtrid"/>';
                $acctid_input = '<input type="hidden" name="" value="' . $row->SYSID . '" id="acctid"/>';
                $schedid_input = '<input type="hidden" name="" value="' . $schedid . '" id="schedid"/>';

                $findings_id = 0;
                $qry_trn_findigns = $this->db->select('findingid')
                    ->from('trn_reading_findings')
                    ->where(array('readingid' => $readid, 'status' => 1))
                    ->order_by('dateupdated', 'desc')
                    ->get()->row();
                if ($qry_trn_findigns) {
                    $findings_id = $qry_trn_findigns->findingid;
                }

                $qry_findings_list = $this->get_list_findings();
                $findings_list = '';
                if ($qry_findings_list) {
                    foreach ($qry_findings_list as $frow) {
                        $selected = '';
                        if ($qry_trn_findigns) {
                            if ($frow->sysid == $findings_id) {
                                $selected = 'selected';
                            }
                        }
                        $findings_list .= '<option ' . $selected . ' value="' . $frow->sysid . '">' . $frow->codes . ' - ' . $frow->descriptions . '</option>';
                    }
                }

                $data['list'][] = array(
                    'seq' => $row->SEQ . $mtrid . $acctid_input . $schedid_input,
                    'servno' => $row->SERVNO,
                    'name' => $name,
                    'mtr' => $row->MTR,
                    'mtrno' => $row->MTRNO,
                    'mtrser' => $row->MTRSER,
                    'prsread' => '<span class="text text-danger">' . number_format($row->PRSRDG) . '</span>',
                    'rem' => '<span class="label label-danger">' . $row->REMARKS . '</span>',
                    'findings' => '<select name="findings[' . $sysid . ']" id="findings" class="form-control inline findings" ><option value=""></option>' . $findings_list . '</select>',
                    //'findings' => $findings_id,
                    'reading' => $reading
                );
            }
        }
        $data['input'] = $this->input->post();
        return json_encode($data);
    }


    function upload_excel_geodata() {
        $data = array();
        $servno_arr = array();
        $file_info = pathinfo($_FILES["reqfiledrop"]["name"]);
        $input = $this->input->post();
        $type = $this->input->post('display');



        if (!is_dir('uploads/temp')) {
            mkdir('uploads/temp', 0755, TRUE);
            chmod('uploads/temp', 0755);
        }
        $file_directory = "uploads/temp/";
        $cnt = 0;
        if($type>0) {

            $new_file_name = date("d-m-Y") . rand(000000, 999999) . "." . $file_info["extension"];
            if (move_uploaded_file($_FILES["reqfiledrop"]["tmp_name"], $file_directory . $new_file_name)) {
                $file_type = PHPExcel_IOFactory::identify($file_directory . $new_file_name);
                $objReader = PHPExcel_IOFactory::createReader($file_type);
                $objPHPExcel = $objReader->load($file_directory . $new_file_name);
                $sheet_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

                foreach ($sheet_data as $data) {
                    $servno = $data['A'];
                    $mtrno = $data['B'];
                    $lat = $data['C'];
                    $lon = $data['D'];
                    $alt = 12;

                    $qry_acctinfo = $this->db->select('sysid')->from('customer_accounts_main')
                        ->where(array('servicenumber' => $servno, 'mtrno' => $mtrno))
                        ->get()->row();

                    if($qry_acctinfo) {
                        $this->db->where(array('acctid' => $qry_acctinfo->sysid));
                        $this->db->update('customer_customers_geodata', array('status' => 0));

                        $ins_arr = array(
                            'acctid' => $qry_acctinfo->sysid,
                            'lat' => $lat,
                            'lon' => $lon,
                            'alt' => $alt,
                            'type' => $type
                        );
                        $this->db->insert('customer_customers_geodata', $ins_arr);
                    }


                    $cnt++;
                }
            }
        }



        $data['servnoarr'] = $servno_arr;
        $data['input'] = $input;
        $data['servnocnt'] = $cnt;
        return json_encode($data);
    }

    function get_gdlb_list() {
        $data = array();
        $dist = $this->input->post("dist");
        $schedday = $this->input->post("datesched");
        $month = $this->input->post("rmonth");
        $year = $this->input->post("ryear");
        $empid = $this->input->post("empid");
        $num_rows = 0;

        if($dist != '') {
            if($dist && $dist > 0) {
                $this->db->where('DIST.sysid', $dist);
            }
            $qry_gdlb = $this->db->select('GDLB.sysid, GDLB.limit AS LMT, COUNT(ACCT.gdlb) AS ACCTNO')
                ->select("CONCAT(GDLB.g, '-', DIST.codes, '-', GDLB.l, '-', GDLB.b) AS GDLBNAME", false)
                ->from('gdlb_main AS GDLB')
                ->join('customer_accounts_main AS ACCT', 'ACCT.gdlb = GDLB.sysid AND ACCT.status = 1')
                ->join('address_districts AS DIST', 'DIST.sysid = GDLB.d')
                ->where('GDLB.g < ', 6)
                ->group_by('ACCT.gdlb, GDLB.limit')
                ->get();


            $dist_where = '';
            if ($dist && $dist > 0) {
                $dist_where = " AND DIST.sysid = $dist";
            }
            $qry_gdlb = $this->db->query("
                SELECT 
                GDLB.sysid AS SYSID, 
                GDLB.limit AS LMT, 
                COUNT(DISTINCT(ACCT.sysid)) AS ACCTNO, 
                CONCAT(GDLB.g, '-', DIST.codes, '-', GDLB.l, '-', GDLB.b) AS GDLBNAME
                FROM gdlb_main AS GDLB
                JOIN customer_accounts_main AS ACCT ON ACCT.gdlb = GDLB.sysid AND ACCT.`status` = 1
                JOIN address_districts AS DIST ON DIST.sysid = GDLB.d
                WHERE GDLB.g < 6 {$dist_where}
                GROUP BY ACCT.gdlb, GDLB.limit
                ORDER BY CONCAT(GDLB.g, '-', DIST.codes, '-', GDLB.l, '-', GDLB.b)
            ");
            $num_rows = $qry_gdlb->num_rows();
            if ($num_rows > 0) {

                foreach ($qry_gdlb->result() as $key => $row) {
                    // GET SCHEDULE
                    $check_box = '';
                    $sched = '<span class="text-danger">Unassigned</span>';
                    $qry_sched_stats = $this->db->query("
                        SELECT m.datesched, r.userid, COUNT(DISTINCT(mrl.acctid)) AS readings
                        FROM reading_schedule_main AS m
                        LEFT JOIN reading_schedule_reader AS r ON r.schedid = m.sysid
                        LEFT JOIN reading_schedule_meters_logs AS ml ON ml.schedid = m.sysid AND ml.`status` = 1
                        LEFT JOIN customer_accounts_subscription_meter_reading_logs AS mrl ON mrl.acctid = ml.acctid AND mrl.`status` = 1
                        WHERE m.months = $month AND m.years = $year AND m.`status` = 1 AND gdlbid = {$row->SYSID}
                        GROUP BY m.datesched, r.userid
                    ")->row();
                    if ($qry_sched_stats) {
                        $sched = $qry_sched_stats->datesched;
                        if ($qry_sched_stats->readings > 0) {
                            $check_box = 'read';
                            $checkbox_input = '<i class="fa fa-warning text-warning"></i>';
                        } else {
                            $check_box = '';
                            $checkbox_input = '<input name="gdlbid[]" value="' . $row->SYSID . '" type="checkbox" id="checkbox_' . $row->SYSID . '" class="md-check checkbox">';
                        }

                        if ($qry_sched_stats->userid == $empid) {
                            $check_box = 'checked';
                            $checkbox_input = '<input name="gdlbid[]" value="' . $row->SYSID . '" type="checkbox" id="checkbox_' . $row->SYSID . '" class="md-check checkbox">';
                        }

                    } else {
                        $checkbox_input = '<input name="gdlbid[]" value="' . $row->SYSID . '" type="checkbox" id="checkbox_' . $row->SYSID . '" class="md-check checkbox">';
                        $check_box = '';
                    }

                    $bg_width = 0;
                    $bg_color = 'rgba(160, 229, 91, 0.25)';
                    if ($row->ACCTNO > 0) {
                        $bg_width = ($row->ACCTNO / $row->LMT) * 100;
                        if ($bg_width > 80) {
                            $bg_color = 'rgba(237, 109, 109, 0.30)';
                        }
                    }
                    $stats = '<span style="position: absolute; top: 0px; display: inline-block; height: 100%; float: left; margin-top: -2px !important; margin-left: -3px; width: ' . $bg_width . '%; background: ' . $bg_color . '"></span>' . $row->ACCTNO . ' / ' . $row->LMT;
                    $data['data'][] = array(
                        'assigned' => $check_box,
                        'gdlb' => '<a href="javascript:;">' . $row->GDLBNAME . '</a>',
                        'stats' => $stats,
                        'sched' => $sched,
                        'control' => $checkbox_input,
                    );
                }
            }
        }
        $data['sEcho'] = 0;
        $data['iTotalRecords'] = $num_rows;
        $data['iTotalDisplayRecords'] = $num_rows;
        return json_encode($data);
    }


    function get_mrd_calendar_dt() {
        $data = array();
        $html = '';
        $qry = false;
        $input_date_start = $this->input->post('datestart');
        $input_date_end = $this->input->post('dateend');

        $input_billmo = $this->input->post('billmo');
        $input_billyr = $this->input->post('billyr');

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

        $qry_meter_reader = $this->db->select('u.sysid, p.lastname')
            ->from('prime_system_users_roles_matrix AS rm')
            ->join('prime_system_users AS u', 'rm.userid = u.sysid AND u.status = 1')
            ->join('person AS p', 'p.sysid = u.personid')
            ->where(array('rm.roleid' => 22, 'rm.status' => 1))
            ->group_by('rm.userid')
            ->order_by('u.lastname')
            ->get();


        $cols_arr = array();
        $cols_arr[] = array('data' => 'name', 'text' => '<span style="width: 150px; display: inline-block;">Name</span>', 'sClass' => 'zui-sticky-col');
        $cols_arr[] = array('data' => 'total', 'text' => 'Assigned', 'sClass' => 'zui-sticky-col');
        $cd = 0;
        foreach ($dates as $dt) {
            $is_weekend_class = '';
            if(isWeekend($dt)) {
                $is_weekend_class = ' text-danger danger';
            }
            $is_weekend_class .= ' date-gdlb ';
            $nameOfDay = date('l', strtotime($dt));
            $th_html = '<span style="width:150px;display:inline-block">'.$dt.'<br><small>'.$nameOfDay.'</small></span>';
            $cols_arr[] = array('data' => $cd++, 'sClass' => $is_weekend_class, 'text' => $th_html);
        }


        if($qry_meter_reader->num_rows()>0) {
            $i = 0;
            foreach ($qry_meter_reader->result() as $keys => $row) {

                $telcode = '';
                $qry_user_telcode = $this->db->select('telcode')
                    ->from('prime_system_users_legacy_code')
                    ->where(array('userid' => $row->sysid))
                    ->get()->row();
                $telcode = ($qry_user_telcode) ? '<span class="label label-danger">'.$qry_user_telcode->telcode .'</span> ' : '';

                $qry_total_assignments = $this->db->select('COUNT(sm.gdlbid) AS cnt')
                    ->from('reading_schedule_main AS sm')
                    ->join('reading_schedule_reader AS sr', 'sr.schedid = sm.sysid')
                    ->where(
                        array(
                            'sm.status != ' => 0,
                            'sr.userid' => $row->sysid,
                            'sm.months' => $input_billmo,
                            'sm.years' => $input_billyr,
                        )
                    )
                    ->get()
                    ->row();
                $total_gdlb = ($qry_total_assignments) ? $qry_total_assignments->cnt : 0;

                $data['list'][] = array(
                    'name' => '<div style="display: inline-block; width: 150px" class="'.$row->sysid.'">'.$telcode.$row->lastname.'</div>',
                    'total' => $total_gdlb
                );

                // CALENDAR HORIZONTAL


                $len = count($dates);
                $ii = 0;
                foreach ($dates as $key => $dt) {
                    $is_weekend_class = '';
                    if(isWeekend($dt)) {
                        $is_weekend_class = ' text-danger danger';
                    }
                    $year_td = DateTime::createFromFormat("Y-m-d", $dt)->format("Y");
                    $month_td = DateTime::createFromFormat("Y-m-d", $dt)->format("m");
                    $day_td = DateTime::createFromFormat("Y-m-d", $dt)->format("d");


                    /*
                        $qry_assignments = $this->db->select(
                            'sm.sysid, sm.gdlbid, COUNT(DISTINCT(ml.acctid)) AS cust, COUNT(DISTINCT(mrl.acctid)) AS readings'
                        )

                            ->select("CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb", false)
                            ->from('reading_schedule_main AS sm')
                            ->join('reading_schedule_reader AS sr', 'sr.schedid = sm.sysid')
                            ->join('reading_schedule_meters_logs AS ml', 'ml.schedid = sm.sysid')
                            ->join('customer_accounts_subscription_meter_reading_logs AS mrl', 'mrl.acctid = ml.acctid AND mrl.status = 1', 'left')
                            ->join('gdlb_main AS gm', 'gm.sysid = sm.gdlbid', 'left')
                            ->join('address_districts AS d', 'd.sysid = gm.d')
                            ->where(array(
                                'sm.status != ' => 0,
                                'sm.datesched' => $dt,
                                'sr.userid' => $row->sysid,
                                'sm.months' => $input_billmo,
                                'sm.years' => $input_billyr,
                            ))
                            ->group_by('sm.sysid, sm.gdlbid')
                            ->get();
                        */


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
                            AND sr.userid = {$row->sysid}
                            AND sm.months = {$input_billmo}
                            AND sm.years = {$input_billyr}
                             -- AND CAST(mrl.datecreated AS DATE) BETWEEN '{$first_day_this_month}' AND '{$last_day_this_month}'
                            GROUP BY sm.sysid, sm.gdlbid
                        ");
                    $assignment_num = $qry_assignments->num_rows();

                    $dt_html = '';
                    $dt_html .= '<div class="data-reader " data-reader="'.$row->sysid.'" id="cell_'.$row->sysid.'_'.$year_td.'_'.$month_td.'_'.$day_td.'">';
                    $dt_html .= '<a id="btn_edit" data-arr="'.$row->sysid.','.$input_billmo.','.$input_billyr.'" data-view="'.$dt.'" href="#form_gdlb_entry" title="'.$row->lastname.' | '.$dt.'" data-toggle="ajax-modal" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>';
                    if($assignment_num>0) {
                        $dt_html .= '<a id="btn_delete_all" data-sched="'.$year_td.'-'.$month_td.'-'.$day_td.'" data-id="' . $row->sysid . '" data-month="'.$input_billmo.'" data-year="'.$input_billyr.'" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                    }
                    $dt_html .= '<ul class="list-group summary column no-border list-group-xs" style="display: inline-block; width: 160px;">';

                    if($assignment_num>0) {
                        $dt_html .= '<li class="list-group-item">';
                        $dt_html .= '<span class="col-md-5 label-name  text-primary text-bold">GDLB</span>';
                        $dt_html .= '<span class="col-md-4 label-name  text-primary text-bold">Read</span>';
                        $dt_html .= '<span class="col-md-3 text-primary text-bold">Cust.</span>';
                        $dt_html .= '</li>';
                        foreach($qry_assignments->result() as $arow) {

                            $qry_cnt_readings = $this->db->query("
                                    SELECT COUNT(DISTINCT(mrl.acctid)) AS cnt FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                    INNER JOIN reading_schedule_reader AS sr ON sr.schedid = mrl.schedid
                                    WHERE 
                                    mrl.schedid = {$arow->sysid} 
                                    AND sr.userid = {$row->sysid}
                                    AND mrl.status = 1
                                ")->row();
                            $readings = 0;
                            if($qry_cnt_readings) {
                                $readings = $qry_cnt_readings->cnt;
                            }

                            $readings_class = 'text-danger';
                            $gdlb_bg = '';
                            $gdlb_txt = '';
                            $customers = $arow->cust;
                            if($readings>=$customers) {
                                $readings_class = '';
                                $gdlb_bg = 'bg-green-turquoise';
                                $gdlb_txt = 'bg-font-green-turquoise';
                            }

                            $check_specific = get_specific_reader_sched($arow->sysid, $row->sysid);
                            $customers = ($check_specific>0) ? $check_specific : $customers;

                            $dt_html .= '<li data-id="'.$arow->sysid.'" id="sched_'.$arow->sysid.'" class="list-group-item '.$gdlb_bg.'">';
                            $dt_html .= '<span class="col-md-5 label-name '.$gdlb_txt.'">';
                            $dt_html .= '<a class="'.$gdlb_txt.' tooltips" data-placement="right" title="Show all customer under this book!" href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-arr="'.$arow->sysid.','.$row->sysid.'" data-reader="">'.$arow->gdlb.'</a>';
                            $dt_html .= '</span>';

                            $dt_html .= '<span class="col-md-4 label-name '.$gdlb_txt.'"><span class="'.$readings_class.'">'.$readings.'</span></span>';
                            $dt_html .= '<span class="col-md-3 '.$gdlb_txt.'">';
                            $dt_html .= '<a class="tooltips '.$gdlb_txt.'" title="Show all customers under this reader!" data-placement="right" href="#form_gdlb_list_reader" data-toggle="ajax-modal" data-arr="'.$arow->sysid.','.$row->sysid.'">'.$customers.'</a>';
                            if($readings<=0) {
                                $dt_html .= '<a id="btn_delete" data-id="' . $arow->sysid . '" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>';
                            }
                            $dt_html .= '</span>';
                            $dt_html .= '</li>';
                        }
                    }

                    $dt_html .= '</ul>';
                    $dt_html .= '</div>';

                    $last_td_class = '';
                    if ($ii == 0) {
                        $last_td_class = 'info';
                    }

                    $td_cont = array(
                        $dt => $dt_html
                    );

                    array_push($data['list'][$keys], $td_cont[$dt]);



                    $html .= '<div id="cell_'.$row->sysid.'_'.$year_td.'_'.$month_td.'_'.$day_td.'">';
                    $html .= '<a id="btn_edit" data-arr="'.$row->sysid.','.$input_billmo.','.$input_billyr.'" data-view="'.$dt.'" href="#form_gdlb_entry" title="'.$row->lastname.' | '.$dt.'" data-toggle="ajax-modal" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>';
                    if($assignment_num>0) {
                        $html .= '<a id="btn_delete_all" data-sched="'.$year_td.'-'.$month_td.'-'.$day_td.'" data-id="' . $row->sysid . '" data-month="'.$input_billmo.'" data-year="'.$input_billyr.'" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                    }
                    $html .= '<ul class="list-group summary column no-border list-group-xs" style="display: inline-block; width: 160px;">';


                    if($assignment_num>0) {
                        $html .= '<li class="list-group-item">';
                        $html .= '<span class="col-md-5 label-name  text-primary text-bold">GDLB</span>';
                        $html .= '<span class="col-md-4 label-name  text-primary text-bold">Read</span>';
                        $html .= '<span class="col-md-3 text-primary text-bold">Cust.</span>';
                        $html .= '</li>';
                        foreach($qry_assignments->result() as $arow) {
                            $qry_cnt_readings = $this->db->query("
                                    SELECT COUNT(DISTINCT(mrl.acctid)) AS cnt FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                    INNER JOIN reading_schedule_reader AS sr ON sr.schedid = mrl.schedid
                                    WHERE 
                                    mrl.schedid = {$arow->sysid} 
                                    AND sr.userid = {$row->sysid}
                                    AND mrl.status = 1
                                ")->row();
                            $readings = 0;
                            if($qry_cnt_readings) {
                                $readings = $qry_cnt_readings->cnt;
                            }

                            $readings_class = 'text-danger';
                            $gdlb_bg = '';
                            $gdlb_txt = '';
                            $customers = $arow->cust;
                            if($readings>=$customers) {
                                $readings_class = '';
                                $gdlb_bg = 'bg-green-turquoise';
                                $gdlb_txt = 'bg-font-green-turquoise';
                            }


                            $html .= '<li id="sched_'.$arow->sysid.'" class="list-group-item '.$gdlb_bg.'">';
                            $html .= '<span class="col-md-5 label-name '.$gdlb_txt.'">';
                            $html .= '<a class="'.$gdlb_txt.'" href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-arr="'.$arow->gdlbid.'" data-reader="">'.$arow->gdlb.'</a>';
                            $html .= '</span>';

                            $html .= '<span class="col-md-4 label-name '.$gdlb_txt.'"><span class="'.$readings_class.'">'.$readings.'</span></span>';
                            $html .= '<span class="col-md-3 '.$gdlb_txt.'">';
                            $html .= '<a href="javascript:;" class="tooltips '.$gdlb_txt.'" title="Reading status" data-placement="left">'.$customers.'</a>';
                            if($readings<=0) {
                                $html .= '<a id="btn_delete" data-id="' . $arow->sysid . '" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>';
                            }
                            $html .= '</span>';
                            $html .= '</li>';
                        }
                    }
                    /*
                     *
                    $ii_cnt = rand(2,4);
                    for( $ii=1; $ii <= $ii_cnt; $ii++ ) {
                        $lot = rand(1,4);
                        $s = substr(str_shuffle(str_repeat("ASLDMJ", 5)), 0, 1);
                        $html .= '<li class="list-group-item">';
                        $html .= '<span class="col-md-6 label-name">';
                        $html .= '<a href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-id="'.$ii.'" data-reader="'.$row->sysid.'">'.$s.'-'.$lot.'-'.str_pad($ii, 2, '0', STR_PAD_LEFT).'</a>';
                        $html .= '</span>';
                        $html .= '<span class="col-md-6">';
                        $html .= '<a href="javascript:;" class="tooltips" title="Reading status" data-placement="left"><span class="text-danger">20</span>/200</a>';
                        $html .= '</span>';
                        $html .= '</li>';
                    }
                    */
                    $html .= '</ul>';
                    $html .= '</td>';

                    $ii++;
                }
                $html .= '</tr>';
                $i++;
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $data['html'] = $html;
        $data['columns'] = $cols_arr;
        return json_encode($data);
    }
    function get_mrd_calendar() {
        $data = array();
        $html = '';
        $qry = false;
        $input_date_start = $this->input->post('datestart');
        $input_date_end = $this->input->post('dateend');

        $input_billmo = $this->input->post('billmo');
        $input_billyr = $this->input->post('billyr');

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
        $qry_meter_reader = $this->db->select('u.sysid, u.lastname')
            ->from('prime_system_users_roles_matrix AS rm')
            ->join('prime_system_users AS u', 'rm.userid = u.sysid')
            ->where(array('rm.roleid' => 22, 'rm.status' => 1))
            ->group_by('rm.userid')
            ->order_by('u.lastname')
            ->get();


        $html .= '<table class="zui-table table table-hover table-striped table-bordered" id="tbl_schedule_calendar">';
        $html .= '<thead>';
        $html .= '<th class="zui-sticky-col">Name</th>';
        $html .= '<th class="zui-sticky-col">Total</th>';
        foreach ($dates as $dt) {

            $is_weekend_class = '';
            if(isWeekend($dt)) {
                $is_weekend_class = ' text-danger danger';
            }
            $nameOfDay = date('l', strtotime($dt));
            $html .= '<th class="'.$is_weekend_class.'"><span style="width:150px;display:inline-block">'.$dt.'<br><small>'.$nameOfDay.'</small></span></th>';
        }
        $html .= '</thead>';
        $html .= '<tbody>';

        if($qry_meter_reader->num_rows()>0) {
            foreach ($qry_meter_reader->result() as $row) {

                $html .= '<tr>';
                $html .= '<td class="zui-sticky-col '.$row->sysid.'">' . $row->lastname . '</td>';

                $html .= '<td class="zui-sticky-col '.$row->sysid.'">';
                $html .= '<code>100</code>';
                $html .= '</td>';
                // CALENDAR HORIZONTAL


                $i = 0;
                $len = count($dates);
                foreach ($dates as $dt) {
                    $is_weekend_class = '';
                    if(isWeekend($dt)) {
                        $is_weekend_class = ' text-danger danger';
                    }

                    $year_td = DateTime::createFromFormat("Y-m-d", $dt)->format("Y");
                    $month_td = DateTime::createFromFormat("Y-m-d", $dt)->format("m");
                    $day_td = DateTime::createFromFormat("Y-m-d", $dt)->format("d");

                    $last_td_class = '';
                    if ($i == 0) {
                        $last_td_class = 'info';
                    }

                    $qry_assignments = $this->db->select(
                        'sm.sysid, sm.gdlbid, COUNT(ml.acctid) AS cust, COUNT(mrl.acctid) AS readings'
                    )

                        ->select("CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb", false)
                        ->from('reading_schedule_main AS sm')
                        ->join('reading_schedule_reader AS sr', 'sr.schedid = sm.sysid')
                        ->join('reading_schedule_meters_logs AS ml', 'ml.schedid = sm.sysid')
                        ->join('customer_accounts_subscription_meter_reading_logs AS mrl', 'mrl.acctid = ml.acctid AND mrl.status = 1', 'left')
                        ->join('gdlb_main AS gm', 'gm.sysid = sm.gdlbid', 'left')
                        ->join('address_districts AS d', 'd.sysid = gm.d')
                        ->where(array(
                            'sm.status != ' => 0,
                            'sm.datesched' => $dt,
                            'sr.userid' => $row->sysid,
                            'sm.months' => $input_billmo,
                            'sm.years' => $input_billyr,
                        ))
                        ->group_by('sm.sysid, sm.gdlbid')
                        ->get();
                    $assignment_num = $qry_assignments->num_rows();

                    $html .= '<td class="date-gdlb '.$last_td_class.$is_weekend_class.'" id="cell_'.$row->sysid.'_'.$year_td.'_'.$month_td.'_'.$day_td.'">';
                    $html .= '<a id="btn_edit" data-arr="'.$row->sysid.','.$input_billmo.','.$input_billyr.'" data-view="'.$dt.'" href="#form_gdlb_entry" title="'.$row->lastname.' | '.$dt.'" data-toggle="ajax-modal" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>';
                    if($assignment_num>0) {
                        $html .= '<a id="btn_delete_all" data-sched="'.$year_td.'-'.$month_td.'-'.$day_td.'" data-id="' . $row->sysid . '" data-month="'.$input_billmo.'" data-year="'.$input_billyr.'" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                    }
                    $html .= '<ul class="list-group summary column no-border list-group-xs" style="display: inline-block; width: 160px;">';


                    if($assignment_num>0) {
                        $html .= '<li class="list-group-item">';
                        $html .= '<span class="col-md-5 label-name  text-primary text-bold">GDLB</span>';
                        $html .= '<span class="col-md-4 label-name  text-primary text-bold">Read</span>';
                        $html .= '<span class="col-md-3 text-primary text-bold">Cust.</span>';
                        $html .= '</li>';
                        foreach($qry_assignments->result() as $arow) {
                            $readings_class = 'text-danger';
                            $gdlb_bg = '';
                            $gdlb_txt = '';
                            $readings = $arow->readings;
                            $customers = $arow->cust;
                            if($readings>=$customers) {
                                $readings_class = '';
                                $gdlb_bg = 'bg-green-turquoise';
                                $gdlb_txt = 'bg-font-green-turquoise';
                            }


                            $html .= '<li id="sched_'.$arow->sysid.'" class="list-group-item '.$gdlb_bg.'">';
                            $html .= '<span class="col-md-5 label-name '.$gdlb_txt.'">';
                            $html .= '<a class="'.$gdlb_txt.'" href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-arr="'.$arow->gdlbid.'" data-reader="">'.$arow->gdlb.'</a>';
                            $html .= '</span>';

                            $html .= '<span class="col-md-4 label-name '.$gdlb_txt.'"><span class="'.$readings_class.'">'.$readings.'</span></span>';
                            $html .= '<span class="col-md-3 '.$gdlb_txt.'">';
                            $html .= '<a href="javascript:;" class="tooltips '.$gdlb_txt.'" title="Reading status" data-placement="left">'.$customers.'</a>';
                            if($readings<=0) {
                                $html .= '<a id="btn_delete" data-id="' . $arow->sysid . '" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>';
                            }
                            $html .= '</span>';
                            $html .= '</li>';
                        }
                    }
                    $html .= '</ul>';
                    $html .= '</td>';

                    $i++;
                }
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $data['html'] = $html;
        return json_encode($data);
    }

    function assign_schedule() {
        $data = array();
        $data['func'] = 'error';
        $data['msg'] = 'test..';

        $cell = '';
        $html = '';

        $gdlbid_arr = $this->input->post('gdlbid');
        $sched = $this->input->post('scheddate');
        $userid = $this->input->post('empid');
        $month = $this->input->post('rmonth');
        $year = $this->input->post('ryear');
        //$gdlbid = 449;
        //$sched = '2019-02-28';
        //$userid = 119;
        //$month = 2;
        //$year = 2019;

        $logs_ids = array();
        $prev_update = 0;
        $prev_sched_ids = array();

        $upd_prev_mtrlogs = false;
        $upd_prev_schedmain = false;
        $upd_prev_reader = false;

        $cust_cnt = 0;


        // PREV MONTH YEAR
        $prev_month = ($month == 1) ? 12 : $month - 1;
        $prev_year = ($month == 1) ? $year - 1 : $year;

        if(is_array($gdlbid_arr) && count($gdlbid_arr) > 0) {
            foreach($gdlbid_arr as $grow) {
                $gdlbid = $grow;

                // UPDATE PREVIOUS EXISTING SCHEDULE DETAILS FIRST
                $get_prev_sched = $this->db->query("
                        SELECT sm.sysid, sm.gdlbid, ss.userid, sm.`status` FROM reading_schedule_specific AS ss
                        INNER JOIN reading_schedule_reader AS sr ON sr.userid = ss.userid AND ss.`status` = 1
                        INNER JOIN reading_schedule_main AS sm ON sm.sysid = sr.schedid
                        WHERE ss.userid = {$userid} AND sm.gdlbid = {$gdlbid} AND sm.years = {$year} AND sm.months = {$month} AND sm.`status` = 1
                        GROUP BY sm.sysid, sm.gdlbid, ss.userid, sm.`status` 
                    ");
                if ($get_prev_sched->num_rows() > 0) {
                    // UPDATE PREVIOUS SCHEDULE
                    foreach ($get_prev_sched->result() as $psrow) {
                        $prev_sched_ids[] = $psrow->sysid;
                        $prev_update += 1;
                    }
                    // UPDATE reading_schedule_main STATUS 0
                    $this->db->where('status', 1);
                    $this->db->where_in('sysid', $prev_sched_ids);
                    $this->db->update('reading_schedule_main', array('status' => 0, 'updatedby' => user_id()));
                    $upd_prev_schedmain = $this->db->affected_rows();

                    // UPDATE reading_schedule_meters_logs STATUS 0
                    $this->db->where('status', 1);
                    $this->db->where_in('schedid', $prev_sched_ids);
                    $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));
                    $upd_prev_mtrlogs = $this->db->affected_rows();

                    // UPDATE reading_schedule_main STATUS 0
                    $this->db->where('status', 1);
                    $this->db->where_in('schedid', $prev_sched_ids);
                    $this->db->update('reading_schedule_reader', array('status' => 0, 'updatedby' => user_id()));
                    $upd_prev_reader = $this->db->affected_rows();
                }


                // INSERT SCHED MAIN
                $sched_ins_arr = array(
                    'gdlbid' => $gdlbid,
                    'datesched' => $sched,
                    'months' => $month,
                    'years' => $year,
                    'createdby' => user_id()
                );
                $this->db->insert('reading_schedule_main', $sched_ins_arr);
                $data['err_ins_sched'] = $this->db->_error_message();
                $new_schedid = $this->db->insert_id();

                $reader_ins_arr = array(
                    'schedid' => $new_schedid,
                    'userid' => $userid
                );
                $this->db->insert('reading_schedule_reader', $reader_ins_arr);

                $qry_specific = $this->db->query(
                    "
                SELECT 
                mr.mrseq AS MRSEQ,
                am.sysid AS SYSID,
                am.netmtr AS NETMTR,
                am.gdlb AS GDLB,
                am.mtrno AS MTRNO,
                am.mtrserial AS MTRSER,
                am.mtr AS MTR
                FROM customer_accounts_main AS am 
                JOIN reading_schedule_specific AS ss ON ss.acctid = am.sysid AND ss.`status` = 1
                LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = am.sysid AND mr.`status` = 1
                WHERE am.gdlb = {$gdlbid} AND ss.userid = {$userid} AND ss.`status` = 1
                GROUP BY 
                mr.mrseq,
                am.sysid,
                am.netmtr,
                am.gdlb, 
                am.mtrno,
                am.mtrserial,
                am.mtr"
                );
                if ($qry_specific->num_rows() > 0) {
                    $data['listtype'] = 'SPECIFIC';
                    foreach ($qry_specific->result() as $crow) {

                        $prvrdg = 0;
                        $prsrdg = 0;
                        $prvdte = '1000-01-01';
                        $prsdte = '1000-01-01';

                        $get_readings_prev = $this->db->query("
                          SELECT 
                                prvrdg,
                                prsrdg,
                                prvdte,
                                prsdte
                                FROM billing_reports_main 
                                WHERE 
                                year = $prev_year 
                                AND month = $prev_month 
                                AND acctid = {$crow->SYSID}
                                AND mtrser = $crow->MTRNO
                                ORDER BY sysid DESC
                                LIMIT 1
                            ")->row();
                        if($get_readings_prev) {
                            $prvrdg = $get_readings_prev->prvrdg;
                            $prsrdg = $get_readings_prev->prsrdg;
                            $prvdte = $get_readings_prev->prvdte;
                            $prsdte = $get_readings_prev->prsdte;
                        }

                        $cust_cnt += 1;
                        $mtr_logs_ins = array(
                            'schedid' => $new_schedid,
                            'acctid' => $crow->SYSID,
                            'mtrno' => $crow->MTRNO,
                            'mtrser' => $crow->MTRSER,
                            'prvrdg' => $prvrdg,
                            'prsrdg' => $prsrdg,
                            'prvdte' => $prvdte,
                            'prsdte' => $prsdte,
                            'seq' => ($crow->MRSEQ != '') ? $crow->MRSEQ : 0,
                            'createdby' => user_id(),
                            'updatedby' => user_id()
                        );
                        $this->db->insert('reading_schedule_meters_logs', $mtr_logs_ins);
                        $logs_ids[] = $this->db->insert_id();
                    }
                } else {
                    $qry_all = $this->db->query(
                        "
                        SELECT 
                        mr.mrseq AS MRSEQ,
                        am.sysid AS SYSID,
                        am.netmtr AS NETMTR,
                        am.gdlb AS GDLB,
                        am.mtrno AS MTRNO,
                        am.mtrserial AS MTRSER,
                        am.mtr AS MTR
                        FROM customer_accounts_main AS am 
                        LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = am.sysid AND mr.`status` = 1
                        WHERE am.gdlb = {$gdlbid}
                        GROUP BY 
                        mr.mrseq,
                        am.sysid,
                        am.netmtr,
                        am.gdlb, 
                        am.mtrno,
                        am.mtrserial,
                        am.mtr"
                    );
                    if ($qry_all->num_rows() > 0) {
                        foreach ($qry_all->result() as $crow) {
                            $prvrdg = 0;
                            $prsrdg = 0;
                            $prvdte = '1000-01-01';
                            $prsdte = '1000-01-01';

                            $get_readings_prev = $this->db->query("
                          SELECT 
                                prvrdg,
                                prsrdg,
                                prvdte,
                                prsdte
                                FROM billing_reports_main 
                                WHERE 
                                year = $prev_year 
                                AND month = $prev_month 
                                AND acctid = {$crow->SYSID}
                                AND mtrser = $crow->MTRNO
                                ORDER BY sysid DESC
                                LIMIT 1
                            ")->row();
                            if($get_readings_prev) {
                                $prvrdg = $get_readings_prev->prvrdg;
                                $prsrdg = $get_readings_prev->prsrdg;
                                $prvdte = $get_readings_prev->prvdte;
                                $prsdte = $get_readings_prev->prsdte;
                            }

                            $cust_cnt += 1;
                            $mtr_logs_ins = array(
                                'schedid' => $new_schedid,
                                'acctid' => $crow->SYSID,
                                'mtrno' => $crow->MTRNO,
                                'mtrser' => $crow->MTRSER,
                                'prvrdg' => $prvrdg,
                                'prsrdg' => $prsrdg,
                                'prvdte' => $prvdte,
                                'prsdte' => $prsdte,
                                'seq' => ($crow->MRSEQ != '') ? $crow->MRSEQ : 0,
                                'createdby' => user_id(),
                                'updatedby' => user_id()
                            );
                            $this->db->insert('reading_schedule_meters_logs', $mtr_logs_ins);
                            $logs_ids[] = $this->db->insert_id();
                        }
                    }
                    $data['listtype'] = 'ALL';
                }
            }
        }


        $day_td = DateTime::createFromFormat("Y-m-d", $sched)->format("d");
        $month_td = DateTime::createFromFormat("Y-m-d", $sched)->format("m");
        $year_td = DateTime::createFromFormat("Y-m-d", $sched)->format("Y");
        $cell = '#cell_'.$userid.'_'.$year_td.'_'.$month_td.'_'.$day_td;


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data['msg'] = 'Query was not complete successfuly!';
            $data['func'] = 'warning';
            $data['qry'] = false;
        } else {
            //@TODO CREATE DELAYED CONTROLLER FOR THIS TO AVOID WAITING ON ASSIGNING SCHEDULE
            $qry_assignments = $this->db->select(
                'sm.sysid, sm.gdlbid, COUNT(ml.acctid) AS cust, COUNT(mrl.acctid) AS readings'
            )
                ->select("CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb", false)
                ->from('reading_schedule_main AS sm')
                ->join('reading_schedule_reader AS sr', 'sr.schedid = sm.sysid')
                ->join('reading_schedule_meters_logs AS ml', 'ml.schedid = sm.sysid')
                ->join('customer_accounts_subscription_meter_reading_logs AS mrl', 'mrl.acctid = ml.acctid AND mrl.status = 1', 'left')
                ->join('gdlb_main AS gm', 'gm.sysid = sm.gdlbid', 'left')
                ->join('address_districts AS d', 'd.sysid = gm.d')
                ->where(array(
                    'sm.status != ' => 0,
                    'sm.datesched' => $sched,
                    'sr.userid' => $userid,
                    'sm.months' => $month_td,
                    'sm.years' => $year_td,
                ))
                ->group_by('sm.sysid, sm.gdlbid')
                ->get();
            $assignment_num = $qry_assignments->num_rows();

            $html .= '<a id="btn_edit" data-arr="'.$userid.','.$month_td.','.$year_td.'" data-view="'.$sched.'" href="#form_gdlb_entry" title="'.user_info($userid)->lastname.' | '.$sched.'" data-toggle="ajax-modal" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>';
            if($assignment_num>0) {
                $html .= '<a id="btn_delete_all" data-sched="'.$year_td.'-'.$month_td.'-'.$day_td.'" data-id="' . $userid . '" data-month="'.$month_td.'" data-year="'.$year_td.'" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
            }
            $html .= '<ul class="list-group summary column no-border list-group-xs" style="display: inline-block; width: 160px;">';


            if($assignment_num>0) {
                $html .= '<li class="list-group-item">';
                $html .= '<span class="col-md-5 label-name  text-primary text-bold">GDLB</span>';
                $html .= '<span class="col-md-4 label-name  text-primary text-bold">Read</span>';
                $html .= '<span class="col-md-3 text-primary text-bold">Cust.</span>';
                $html .= '</li>';
                foreach($qry_assignments->result() as $arow) {
                    $readings_class = 'text-danger';
                    $gdlb_bg = '';
                    $gdlb_txt = '';
                    $readings = $arow->readings;
                    $customers = $arow->cust;
                    if($readings>=$customers) {
                        $readings_class = '';
                        $gdlb_bg = 'bg-green-turquoise';
                        $gdlb_txt = 'bg-font-green-turquoise';
                    }

                    $check_specific = get_specific_reader_sched($arow->sysid, $userid);
                    $customers = ($check_specific>0) ? $check_specific : $customers;

                    $html .= '<li id="sched_'.$arow->sysid.'" class="list-group-item '.$gdlb_bg.'">';
                    $html .= '<span class="col-md-5 label-name '.$gdlb_txt.'">';
                    $html .= '<a class="'.$gdlb_txt.'" href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-arr="'.$arow->gdlbid.'" data-reader="">'.$arow->gdlb.'</a>';
                    $html .= '</span>';

                    $html .= '<span class="col-md-4 label-name '.$gdlb_txt.'"><span class="'.$readings_class.'">'.$readings.'</span></span>';
                    $html .= '<span class="col-md-3 '.$gdlb_txt.'">';
                    $html .= '<a href="javascript:;" class="tooltips '.$gdlb_txt.'" title="Reading status" data-placement="left">'.$customers.'</a>';
                    if($readings<=0) {
                        $html .= '<a id="btn_delete" data-id="' . $arow->sysid . '" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>';
                    }
                    $html .= '</span>';
                    $html .= '</li>';
                }
            }
            $this->db->trans_commit();
            $data['msg'] = 'Query completed successfuly!';
            $data['func'] = 'success';
            $data['qry'] = true;
        }

        $data['html'] = $html;
        $data['cell'] = $cell;
        $data['readerid'] = $userid;
        $data['newchedid'] = $new_schedid;
        $data['custcnt'] = $cust_cnt;
        $data['prevupdcnt'] = $prev_update;
        $data['prevmtrlogs'] = $upd_prev_mtrlogs;
        $data['prevschedmain'] = $upd_prev_schedmain;
        $data['prevreader'] = $upd_prev_reader;
        $data['prevschedids'] = $prev_sched_ids;
        $data['empid'] = $userid;
        $data['input'] = $this->input->post();

        return json_encode($data);
    }

    function fix_sched_data_row() {
        $data = array();
        $index = $this->input->post('index');
        $gdlbid = $this->input->post('gdlbid');
        $userid = $this->input->post('userid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $schedid = $this->input->post('schedid');

        $new_userid = false;
        $datacnt = 0;

        if($userid) {
            // PREV MONTH YEAR
            $prev_month = ($month == 1) ? 12 : $month - 1;
            $prev_year = ($month == 1) ? $year - 1 : $year;

            $data['prevmonth'] = $prev_month;
            $data['prevyear'] = $prev_year;

            // UPDATE PREVIOUS EXISTING SCHEDULE DETAILS FIRST
            $get_prev_sched = $this->db->query("
                        SELECT sm.sysid, sm.gdlbid, ss.userid, sm.`status` FROM reading_schedule_specific AS ss
                        INNER JOIN reading_schedule_reader AS sr ON sr.userid = ss.userid AND ss.`status` = 1
                        INNER JOIN reading_schedule_main AS sm ON sm.sysid = sr.schedid
                        WHERE sm.sysid != {$schedid} AND ss.userid = {$userid} AND sm.gdlbid = {$gdlbid} AND sm.years = {$year} AND sm.months = {$month} AND sm.`status` = 1
                        GROUP BY sm.sysid, sm.gdlbid, ss.userid, sm.`status` 
                    ");
            if ($get_prev_sched->num_rows() > 0) {
                // UPDATE PREVIOUS SCHEDULE
                foreach ($get_prev_sched->result() as $psrow) {
                    $prev_sched_ids[] = $psrow->sysid;
                }
                $data['updprev'] = true;

                // UPDATE reading_schedule_main STATUS 0
                $this->db->where('status', 1);
                $this->db->where_in('sysid', $prev_sched_ids);
                $this->db->update('reading_schedule_main', array('status' => 0, 'updatedby' => user_id()));
                $upd_prev_schedmain = $this->db->affected_rows();
                $data['upd_prev_schedmain'] = $upd_prev_schedmain;

                // UPDATE reading_schedule_meters_logs STATUS 0
                $this->db->where('status', 1);
                $this->db->where_in('schedid', $prev_sched_ids);
                $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));
                $upd_prev_mtrlogs = $this->db->affected_rows();
                $data['upd_prev_mtrlogs'] = $upd_prev_mtrlogs;

                // UPDATE reading_schedule_main STATUS 0
                $this->db->where('status', 1);
                $this->db->where('userid', $userid);
                $this->db->update('reading_schedule_reader', array('status' => 0, 'updatedby' => user_id()));
                $upd_prev_reader = $this->db->affected_rows();
                $data['upd_prev_reader'] = $upd_prev_reader;

            } else {
                // UPDATE reading_schedule_meters_logs STATUS 0
                $this->db->where('status', 1);
                $this->db->where('schedid', $schedid);
                $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));

                // UPDATE reading_schedule_main STATUS 0
                $this->db->where('status', 1);
                $this->db->where('schedid', $schedid);
                $this->db->update('reading_schedule_reader', array('status' => 0, 'updatedby' => user_id()));

            }


            $reader_ins_arr = array(
                'schedid' => $schedid,
                'userid' => $userid
            );
            $this->db->insert('reading_schedule_reader', $reader_ins_arr);

            $qry_specific = $this->db->query(
                "
                SELECT 
                mr.mrseq AS MRSEQ,
                am.sysid AS SYSID,
                am.netmtr AS NETMTR,
                am.gdlb AS GDLB,
                am.mtrno AS MTRNO,
                am.mtrserial AS MTRSER,
                am.mtr AS MTR
                FROM customer_accounts_main AS am 
                JOIN reading_schedule_specific AS ss ON ss.acctid = am.sysid AND ss.`status` = 1
                LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = am.sysid AND mr.`status` = 1
                WHERE am.gdlb = {$gdlbid} AND ss.userid = {$userid} AND ss.`status` = 1 AND am.`status` = 1
                GROUP BY 
                mr.mrseq,
                am.sysid,
                am.netmtr,
                am.gdlb, 
                am.mtrno,
                am.mtrserial,
                am.mtr"
            );
            if ($qry_specific->num_rows() > 0) {
                foreach ($qry_specific->result() as $crow) {

                    $prvrdg = 0;
                    $prsrdg = 0;
                    $prvdte = '1000-01-01';
                    $prsdte = '1000-01-01';


                    $get_readings_prev = $this->db->query("
                      SELECT 
                            prvrdg,
                            prsrdg,
                            prvdte,
                            prsdte
                            FROM billing_reports_main 
                            WHERE 
                            year = $prev_year 
                            AND month = $prev_month 
                            AND acctid = {$crow->SYSID}
                            AND mtrser = $crow->MTRNO
                            ORDER BY sysid DESC
                            LIMIT 1
                            ")->row();
                    if($get_readings_prev) {
                        $prvrdg = $get_readings_prev->prvrdg;
                        $prsrdg = $get_readings_prev->prsrdg;
                        $prvdte = $get_readings_prev->prvdte;
                        $prsdte = $get_readings_prev->prsdte;
                    }

                    $data['prevarr'] = $get_readings_prev;

                    $this->db->where(
                        array(
                            'schedid' => $schedid,
                            'acctid' => $crow->SYSID,
                            'mtrno' => $crow->MTRNO,
                            'mtrser' => $crow->MTRSER,
                        )
                    );
                    $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));

                    $mtr_logs_ins = array(
                        'schedid' => $schedid,
                        'acctid' => $crow->SYSID,
                        'mtrno' => $crow->MTRNO,
                        'mtrser' => $crow->MTRSER,
                        'prvrdg' => $prvrdg,
                        'prsrdg' => $prsrdg,
                        'prvdte' => $prvdte,
                        'prsdte' => $prsdte,
                        'seq' => ($crow->MRSEQ != '') ? $crow->MRSEQ : 0,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );
                    $this->db->insert('reading_schedule_meters_logs', $mtr_logs_ins);
                    $logs_ids[] = $this->db->insert_id();
                    $datacnt += 1;
                    $data['SPECIFIC'][] = $mtr_logs_ins;
                }
            } else {
                $qry_all = $this->db->query(
                    "
                    SELECT 
                    mr.mrseq AS MRSEQ,
                    am.sysid AS SYSID,
                    am.netmtr AS NETMTR,
                    am.gdlb AS GDLB,
                    am.mtrno AS MTRNO,
                    am.mtrserial AS MTRSER,
                    am.mtr AS MTR
                    FROM customer_accounts_main AS am 
                    LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = am.sysid AND mr.`status` = 1
                    WHERE am.gdlb = {$gdlbid} AND am.`status` = 1
                    GROUP BY 
                    mr.mrseq,
                    am.sysid,
                    am.netmtr,
                    am.gdlb, 
                    am.mtrno,
                    am.mtrserial,
                    am.mtr
                  "
                );
                if ($qry_all->num_rows() > 0) {
                    foreach ($qry_all->result() as $crow) {

                        $prvrdg = 0;
                        $prsrdg = 0;
                        $prvdte = '1000-01-01';
                        $prsdte = '1000-01-01';


                        $get_readings_prev = $this->db->query("
                          SELECT 
                                prvrdg,
                                prsrdg,
                                prvdte,
                                prsdte
                                FROM billing_reports_main 
                                WHERE 
                                year = $prev_year 
                                AND month = $prev_month 
                                AND acctid = {$crow->SYSID}
                                AND mtrser = $crow->MTRNO
                                AND prsrdg IS NOT NULL 
                                AND prvrdg IS NOT NULL
                                ORDER BY sysid DESC
                                LIMIT 1
                            ")->row();
                        if($get_readings_prev) {
                            $prvrdg = $get_readings_prev->prvrdg;
                            $prsrdg = $get_readings_prev->prsrdg;
                            $prvdte = $get_readings_prev->prvdte;
                            $prsdte = $get_readings_prev->prsdte;
                        }

                        $data['prevarr'][] = $get_readings_prev;

                        $this->db->where(
                            array(
                                'schedid' => $schedid,
                                'acctid' => $crow->SYSID,
                                'mtrno' => $crow->MTRNO,
                                'mtrser' => $crow->MTRSER,
                            )
                        );
                        $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));

                        $mtr_logs_ins = array(
                            'schedid' => $schedid,
                            'acctid' => $crow->SYSID,
                            'mtrno' => $crow->MTRNO,
                            'mtrser' => $crow->MTRSER,
                            'prvrdg' => $prvrdg,
                            'prsrdg' => $prsrdg,
                            'prvdte' => $prvdte,
                            'prsdte' => $prsdte,
                            'seq' => ($crow->MRSEQ != '') ? $crow->MRSEQ : 0,
                            'createdby' => user_id(),
                            'updatedby' => user_id()
                        );
                        $this->db->insert('reading_schedule_meters_logs', $mtr_logs_ins);
                        $logs_ids[] = $this->db->insert_id();
                        $datacnt += 1;
                    }
                }
                $data['ALL'][] = $mtr_logs_ins;
            }
        } else {
            $get_latest_sched = $this->db->query("
                        SELECT ss.userid, sm.datecreated FROM reading_schedule_specific AS ss
                        INNER JOIN reading_schedule_reader AS sr ON sr.userid = ss.userid
                        INNER JOIN reading_schedule_main AS sm ON sm.sysid = sr.schedid
                        WHERE sm.sysid = {$schedid} AND sm.gdlbid = {$gdlbid} AND sm.years = $year AND sm.months = $month AND sm.`status` = 1
                        GROUP BY ss.userid, sm.datecreated
                        ORDER BY sm.datecreated DESC
                        LIMIT 1
                    ")->row();
            if($get_latest_sched) {
                $new_userid = $get_latest_sched->userid;


                $reader_ins_arr = array(
                    'schedid' => $schedid,
                    'userid' => $new_userid
                );
                $this->db->insert('reading_schedule_reader', $reader_ins_arr);
                $userid = $new_userid;
            }else {
                $data['err'] = 'No User ID';
                $data['gdlbid'] = $gdlbid;
                $data['schedid'] = $schedid;
                $data['year'] = $year;
                $data['month'] = $month;
            }
        }


        $check_specific = get_specific_reader_sched($schedid, $userid);
        $datacnt = ($check_specific > 0) ? $check_specific : $datacnt;

        $data['newuserid'] = $new_userid;
        $data['datacnt'] = '<span class="label label-info">'.$datacnt.'</span>';
        $data['indexn'] = $index + 1;
        return json_encode($data);
    }

    /*
    function assign_reading_schedule() {
        $data = array();
        $data['func'] = 'error';
        $data['msg'] = 'test..';

        $gdlb_arr = $this->input->post('gdlbid');
        $sched = $this->input->post('scheddate');
        $empid = $this->input->post('empid');


        $day_td = '';
        $month_td = '';
        $year_td = '';

        $html = '';

        if($empid != '' ) {

            $empinput = explode(',', $empid);

            if($gdlb_arr) {
                $m = $this->input->post('rmonth');
                $y = $this->input->post('ryear');

                $day_td = DateTime::createFromFormat("Y-m-d", $sched)->format("d");
                $month_td = DateTime::createFromFormat("Y-m-d", $sched)->format("m");
                $year_td = DateTime::createFromFormat("Y-m-d", $sched)->format("Y");

                $this->db->trans_begin();
                $schednum = 0;
                $gdlbnum = 0;
                $logs_id = array();
                $sched_ids = array();
                foreach ($gdlb_arr as $key => $row) {
                    $gdlbid = $row;

                    $gdlbnum += 1;

                    if($sched!='') {
                        $schednum += 1;
                        // UPDATE STATUS
                        //$this->db->where(array('gdlbid' => $gdlbid, 'months' => $m, 'years' => $y));
                        //$this->db->update('reading_schedule_main', array('status' => 0));



                        // INSERT SCHED MAIN
                        $sched_ins_arr = array(
                            'gdlbid' => $gdlbid,
                            'datesched' => $sched,
                            'months' => $m,
                            'years' => $y,
                            'createdby' => user_id()
                        );
                        $this->db->insert('reading_schedule_main', $sched_ins_arr);
                        $schedmainid = $this->db->insert_id();


                        $reader_ins_arr = array(
                            'schedid' => $schedmainid,
                            'userid' => $empid
                        );
                        $this->db->insert('reading_schedule_reader', $reader_ins_arr);
                        $reader_ids = $this->db->insert_id();


                        // INSERT METER LOGS
                        // reading_schedule_meters_logs,
                        $qry_from_accnts_main = $this->db->select(
                            'm.sysid, m.gdlb, m.mtrno, m.mtrserial, seq.mrseq'
                        )
                            ->from('customer_accounts_main AS m')
                            ->join('customer_accounts_mtrseq AS seq', 'seq.acctid = m.sysid AND seq.status = 1', 'left')
                            ->where(array('m.gdlb' => $gdlbid, 'm.status' => 1))
                            ->get();

                        if($qry_from_accnts_main->num_rows() > 0) {
                            // GET MONTH YEAR OF SCHDULED NEW
                            $qry_schedule_new = $this->db->select('months, years')
                                ->from('reading_schedule_main')
                                ->where('sysid', $schedmainid)
                                ->get()->row();

                            $month = $qry_schedule_new->months;
                            $year = $qry_schedule_new->years;
                            $qry_schedule_prev = $this->db->select('sysid')
                                ->from('reading_schedule_main')
                                ->where(array('years' => $year, 'months' => $month, 'gdlbid' => $gdlbid, 'sysid != ' => $schedmainid))
                                ->get();

                            $qry_schedule_prev_numrows = $qry_schedule_prev->num_rows();


                            $logs_acctid_arr = array();
                            foreach($qry_from_accnts_main->result() as $row) {
                                // CHECK SPECIFIC FIRST
                                $qry_specific = $this->db->select('userid, acctid')
                                    ->from('reading_schedule_specific')
                                    ->where(array('acctid' => $row->sysid))
                                    ->get()->row();
                                if($qry_specific) {
                                    if($empid == $qry_specific->userid && $row->sysid == $qry_specific->acctid) {
                                        // #############################################
                                        // UPDATE METER LOGS ALL SPECIFIC
                                        $logs_acctid_arr[] = $row->sysid;
                                        $mtr_logs_ins = array(
                                            'schedid' => $schedmainid,
                                            'acctid' => $row->sysid,
                                            'mtrno' => $row->mtrno,
                                            'mtrser' => $row->mtrserial,
                                            'seq' => ($row->mrseq != '') ? $row->mrseq : 0,
                                            'createdby' => user_id(),
                                            'updatedby' => user_id()
                                        );
                                        $this->db->insert('reading_schedule_meters_logs', $mtr_logs_ins);
                                        $logs_id[] = $this->db->insert_id();
                                        $sched_ids[] = $schedmainid;
                                    }
                                }else{
                                    $logs_acctid_arr[] = $row->sysid;
                                    $mtr_logs_ins = array(
                                        'schedid' => $schedmainid,
                                        'acctid' => $row->sysid,
                                        'mtrno' => $row->mtrno,
                                        'mtrser' => $row->mtrserial,
                                        'seq' => ($row->mrseq != '') ? $row->mrseq : 0,
                                        'createdby' => user_id(),
                                        'updatedby' => user_id()
                                    );
                                    $this->db->insert('reading_schedule_meters_logs', $mtr_logs_ins);
                                    $logs_id[] = $this->db->insert_id();
                                    $sched_ids[] = $schedmainid;
                                }
                            }

                            $sched_ids_u = array_unique($sched_ids);

                            $data['schedids'] = $sched_ids_u;


                            // #############################################
                            // UPDATE METER LOGS ALL RELATED TO SCHEDULE
                            if($qry_schedule_prev_numrows > 0 && count($logs_acctid_arr) > 0) {
                                foreach($qry_schedule_prev->result() as $sprow) {
                                    // UPDATE reading_schedule_meters_logs STATUS 0
                                    $this->db->where_not_in('schedid', $sched_ids_u);
                                    $this->db->where(array('schedid' => $sprow->sysid, 'status' => 1));
                                    $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));

                                    $this->db->where_not_in('sysid', $sched_ids_u);
                                    $this->db->where(array('months' => $m, 'years' => $y, 'sysid' => $sprow->sysid, 'status' => 1));
                                    $this->db->update('reading_schedule_main', array('status' => 0, 'updatedby' => user_id()));

                                    $this->db->where_not_in('schedid', $sched_ids_u);
                                    $this->db->where(array('schedid' => $sprow->sysid, 'status' => 1, 'userid != ' => $reader_ids));
                                    $this->db->update('reading_schedule_reader', array('status' => 0, 'updatedby' => user_id()));
                                }
                                $data['schedids_updated'] = $sprow->sysid;
                            }

                        }
                    }
                }


                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $data['msg'] = 'Query was not complete successfuly!';
                    $data['func'] = 'warning';
                    $data['qry'] = false;
                } else {
                    $qry_assignments = $this->db->select(
                        'sm.sysid, sm.gdlbid, COUNT(ml.acctid) AS cust, COUNT(mrl.acctid) AS readings'
                    )
                        ->select("CONCAT(gm.g, '-', d.codes, '-', gm.l, '-', gm.b) AS gdlb", false)
                        ->from('reading_schedule_main AS sm')
                        ->join('reading_schedule_reader AS sr', 'sr.schedid = sm.sysid')
                        ->join('reading_schedule_meters_logs AS ml', 'ml.schedid = sm.sysid')
                        ->join('customer_accounts_subscription_meter_reading_logs AS mrl', 'mrl.acctid = ml.acctid AND mrl.status = 1', 'left')
                        ->join('gdlb_main AS gm', 'gm.sysid = sm.gdlbid', 'left')
                        ->join('address_districts AS d', 'd.sysid = gm.d')
                        ->where(array(
                            'sm.status != ' => 0,
                            'sm.datesched' => $sched,
                            'sr.userid' => $empid,
                            'sm.months' => $month_td,
                            'sm.years' => $year_td,
                        ))
                        ->group_by('sm.sysid, sm.gdlbid')
                        ->get();
                    $assignment_num = $qry_assignments->num_rows();

                    $html .= '<a id="btn_edit" data-arr="'.$row->sysid.','.$month_td.','.$year_td.'" data-view="'.$sched.'" href="#form_gdlb_entry" title="'.user_info($empid)->lastname.' | '.$sched.'" data-toggle="ajax-modal" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>';
                    if($assignment_num>0) {
                        $html .= '<a id="btn_delete_all" data-sched="'.$year_td.'-'.$month_td.'-'.$day_td.'" data-id="' . $row->sysid . '" data-month="'.$month_td.'" data-year="'.$year_td.'" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                    }
                    $html .= '<ul class="list-group summary column no-border list-group-xs" style="display: inline-block; width: 160px;">';


                    if($assignment_num>0) {
                        $html .= '<li class="list-group-item">';
                        $html .= '<span class="col-md-5 label-name  text-primary text-bold">GDLB</span>';
                        $html .= '<span class="col-md-4 label-name  text-primary text-bold">Read</span>';
                        $html .= '<span class="col-md-3 text-primary text-bold">Cust.</span>';
                        $html .= '</li>';
                        foreach($qry_assignments->result() as $arow) {
                            $readings_class = 'text-danger';
                            $gdlb_bg = '';
                            $gdlb_txt = '';
                            $readings = $arow->readings;
                            $customers = $arow->cust;
                            if($readings>=$customers) {
                                $readings_class = '';
                                $gdlb_bg = 'bg-green-turquoise';
                                $gdlb_txt = 'bg-font-green-turquoise';
                            }

                            $check_specific = get_specific_reader_sched($arow->sysid, $empid);
                            $customers = ($check_specific>0) ? $check_specific : $customers;

                            $html .= '<li id="sched_'.$arow->sysid.'" class="list-group-item '.$gdlb_bg.'">';
                            $html .= '<span class="col-md-5 label-name '.$gdlb_txt.'">';
                            $html .= '<a class="'.$gdlb_txt.'" href="#form_gdlb_list" data-toggle="ajax-modal" id="reader_gdlb" data-arr="'.$arow->gdlbid.'" data-reader="">'.$arow->gdlb.'</a>';
                            $html .= '</span>';

                            $html .= '<span class="col-md-4 label-name '.$gdlb_txt.'"><span class="'.$readings_class.'">'.$readings.'</span></span>';
                            $html .= '<span class="col-md-3 '.$gdlb_txt.'">';
                            $html .= '<a href="javascript:;" class="tooltips '.$gdlb_txt.'" title="Reading status" data-placement="left">'.$customers.'</a>';
                            if($readings<=0) {
                                $html .= '<a id="btn_delete" data-id="' . $arow->sysid . '" href="javascript:;" class="btn btn-xs btn-danger inline"><i class="fa fa-times"></i></a>';
                            }
                            $html .= '</span>';
                            $html .= '</li>';
                        }
                    }

                    $cell = '#cell_'.$empid.'_'.$year_td.'_'.$month_td.'_'.$day_td;

                    $this->db->trans_commit();
                    $data['msg'] = 'Query completed successfuly!';
                    $data['func'] = 'success';
                    $data['qry'] = true;
                }
            }else{
                $data['msg'] = 'Select GDLB..';
                $data['func'] = 'warning';
                $data['qry'] = false;
            }
        }else{
            $data['msg'] = 'Assign reader..';
            $data['func'] = 'warning';
            $data['qry'] = false;
        }

        $data['newlogsid'] = $logs_id;

        $data['html'] = $html;
        $data['cell'] = $cell;
        $data['empid'] = $empid;
        $data['input'] = $this->input->post();

        return json_encode($data);
    }

    */

    function del_read_sched() {
        $data = array();
        $qry = false;
        $id = $this->input->post('id');
        if($id) {
            $this->db->trans_begin();
            $this->db->where('sysid', $id);
            $this->db->update('reading_schedule_main', array('status' => 0, 'updatedby' => user_id()));

            $this->db->where('schedid', $id);
            $this->db->update('reading_schedule_reader', array('status' => 0, 'updatedby' => user_id()));

            $this->db->where('schedid', $id);
            $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));

            if($this->db->trans_status() === true) {
                $this->db->trans_commit();
                $qry = true;
            }else{
                $this->db->trans_rollback();
            }
        }
        $data['qry'] = $qry;
        return json_encode($data);
    }
    function del_read_sched_all() {
        $data = array();
        $qry = false;
        $id = $this->input->post('id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $schedday = $this->input->post('sched');
        $sched_ids = array();
        $del_ids = array();
        $del_cnt = 0;
        if($id) {
            $this->db->trans_begin();
            $qry_sched_main = $this->db->select('r.sysid, r.schedid, COUNT(mrl.acctid) AS readings')
                ->from('reading_schedule_reader AS r')
                ->join('reading_schedule_main AS m', 'm.sysid = r.schedid')
                ->join('reading_schedule_meters_logs AS ml', 'ml.schedid = m.sysid', 'left')
                ->join('customer_accounts_subscription_meter_reading_logs AS mrl', 'mrl.acctid = ml.acctid AND mrl.status = 1', 'left')
                ->where(array('r.userid' => $id, 'm.datesched' => $schedday, 'm.months' => $month, 'm.years' => $year, 'm.status != ' => 0))
                ->group_by('r.sysid, r.schedid')
                ->get();
            if($qry_sched_main->num_rows()>0) {
                foreach($qry_sched_main->result() as $row) {
                    $sched_ids[] = $row->sysid;
                    if($row->readings<=0) {
                        $this->db->where('sysid', $row->schedid);
                        $this->db->update('reading_schedule_main', array('status' => 0, 'updatedby' => user_id()));

                        $this->db->where('schedid', $row->sysid);
                        $this->db->update('reading_schedule_reader', array('status' => 0, 'updatedby' => user_id()));

                        $this->db->where('schedid', $row->sysid);
                        $this->db->update('reading_schedule_meters_logs', array('status' => 0, 'updatedby' => user_id()));
                        $del_ids[] = 'sched_' . $row->schedid;
                        $del_cnt += 1;
                    }

                }
            }
            if($this->db->trans_status() === true) {
                $this->db->trans_commit();
                $qry = true;
            }else{
                $this->db->trans_rollback();
            }
        }
        $data['schedids'] = $sched_ids;
        $data['delids'] = $del_ids;
        $data['delcnt'] = $del_cnt;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function submit_manual_lot_book() {
        $gdlblist = $this->input->post('gdlblist');
        $accountid = $this->input->post('accountid');
        $qry_gdlb_main = $this->db->select("CONCAT(g.g, '-', d.codes, '-', g.l, '-', g.b) AS gdlb", false)
            ->from('gdlb_main AS g')
            ->join('address_districts AS d', 'g.d = d.sysid')
            ->where('g.sysid', $gdlblist)
            ->get()->row();
        $data = array();

        $insertmtr = $this->db->insert('customer_accounts_glb', array('accountid' => $accountid, 'gdlbid' => $gdlblist, 'status' => 1));
        if ($insertmtr) {
            $data['msg'] = 'Query completed successfuly!';
            $data['func'] = 'success';
            $data['qry'] = true;
            $data['gdlbname'] = $qry_gdlb_main->gdlb;
        } else {
            $data['msg'] = 'Query was not complete successfuly!';
            $data['func'] = 'warning';
            $data['qry'] = false;
            $data['gdlbname'] = "N/A";
        }

        return json_encode($data);
    }

    function submit_reading_row() {
        if(user_id() > 0) {
            $input = $this->input->post();
            $acctid = $this->input->post('acctid');
            $mtrid = $this->input->post('mtrid');
            $reading = $this->input->post('reading');
            $schedid = $this->input->post('schedid');
            $demand = $this->input->post('demand');
            $netmtr = $this->input->post('netmtr');
            $findings = $this->input->post('findings');
            $recheck = $this->input->post('recheck');

            $title = 'Reading entry';

            $this->db->trans_begin();
            if ($reading > 0) {
                //UPDATE FIRST
                $this->db->where(array('schedid' => $schedid, 'acctid' => $acctid, 'mtrid' => $mtrid, 'status' => 1));
                $this->db->update('customer_accounts_subscription_meter_reading_temp', array('status' => 0));
                $ins_reading_logs_arr = array(
                    'schedid' => $schedid,
                    'acctid' => $acctid,
                    'mtrid' => $mtrid,
                    'reading' => $reading,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                if ($demand > 0) {
                    $this->db->set('demand', $demand);
                }
                if ($netmtr > 0) {
                    $this->db->set('netmtr', $netmtr);
                }
                if ($recheck == true) {
                    $this->db->set('types', 2);

                } else {
                    $this->db->set('types', 1);
                }
                $this->db->insert('customer_accounts_subscription_meter_reading_temp', $ins_reading_logs_arr);
                $reading_sysid = $this->db->insert_id();
                if ($findings) {
                    // @TODO Make foreach insert here if applicable
                    $findings_ins_arr = array(
                        'findingid' => $findings,
                        'readingid' => $reading_sysid,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('trn_reading_findings', $findings_ins_arr);
                }


                if ($this->db->trans_status() == FALSE) {
                    $this->db->trans_rollback();
                    $msg = 'reading is zero!';
                    $func = 'warning';
                    $q = false;
                } else {
                    $this->db->trans_commit();
                    $msg = 'record saved!';
                    $func = 'success';
                    $q = true;
                }
            } else {
                $msg = 'reading is zero!';
                $func = 'warning';
                $q = false;
            }
        }else{
            $msg = 'Session time out!';
            $func = 'warning';
            $q = false;
        }
        $data['input'] = $this->input->post();
        $data['qry'] = $q;
        $data['input'] = $input;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = $title;
        return json_encode($data);
    }

    function edit_reading_row() {
        if(user_id() > 0) {
            $input = $this->input->post();
            $acctid = $this->input->post('acctid');
            $mtrid = $this->input->post('mtrid');
            $reading = $this->input->post('reading');
            $schedid = $this->input->post('schedid');
            $demand = $this->input->post('demand');
            $findings = $this->input->post('findings');
            $recheck = $this->input->post('recheck');

            $title = 'Reading entry';

            $this->db->trans_begin();
            if ($reading > 0) {
                //UPDATE FIRST
                $this->db->where(array('schedid' => $schedid, 'acctid' => $acctid, 'mtrid' => $mtrid, 'status' => 1));
                $this->db->update('customer_accounts_subscription_meter_reading_logs', array('status' => 0));
                $ins_reading_logs_arr = array(
                    'schedid' => $schedid,
                    'acctid' => $acctid,
                    'mtrid' => $mtrid,
                    'reading' => $reading,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                if ($demand > 0) {
                    $this->db->set('demand', $demand);
                }
                if ($recheck == true) {
                    $this->db->set('types', 2);

                } else {
                    $this->db->set('types', 1);
                }
                $this->db->insert('customer_accounts_subscription_meter_reading_logs', $ins_reading_logs_arr);
                $reading_sysid = $this->db->insert_id();
                if ($findings) {
                    // @TODO Make foreach insert here if applicable
                    $findings_ins_arr = array(
                        'schedid'   => $schedid,
                        'acctid'    => $acctid,
                        'mtrno'     => $mtrid,
                        'findingid' => $findings,
                        'readingid' => $reading_sysid,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('trn_reading_findings', $findings_ins_arr);
                }


                if ($this->db->trans_status() == FALSE) {
                    $this->db->trans_rollback();
                    $msg = 'reading is zero!';
                    $func = 'warning';
                    $q = false;
                } else {
                    $this->db->trans_commit();
                    $msg = 'record saved!';
                    $func = 'success';
                    $q = true;
                }
            } else {
                $msg = 'reading is zero!';
                $func = 'warning';
                $q = false;

            }
        }else{
            $msg = 'Session time out!';
            $func = 'warning';
            $q = false;
        }


        $computeread = $this->model_mrd->get_compute_reading($schedid, $acctid, $reading);

        $data['color'] = $computeread->color;
        $data['newcons'] = $computeread->newcons;
        $data['percent'] = $computeread->percent;

        $data['input'] = $this->input->post();
        $data['qry'] = $q;
        $data['input'] = $input;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = $title;
        return json_encode($data);
    }

    function get_reading_gdlb_sched() {

        $data = array();
        $userid = user_id();

        // THIS WILL GET THE MONTHS AND YEAR CORRESPONDING TO THE NEW SCHEDULE ENCODED
        $get_sched = $this->db->query("SELECT months, years FROM reading_schedule_main WHERE status != 0 ORDER BY datecreated DESC")->row();

        if($userid == 1) {
            if($get_sched) {
                $this->db->where(array('rm.months' => $get_sched->months, 'rm.years' => $get_sched->years));
            }
            $query = $this->db->select('rm.sysid, rm.datesched, lc.telcode, p.lastname')
                ->select("CONCAT(G.g, '-', D.codes, '-', G.l, '-', G.b) AS GDLBNAME", false)
                ->from('customer_accounts_glb AS gdlb')
                ->join('reading_schedule_main AS rm', 'rm.gdlbid = gdlb.gdlbid')
                ->join('reading_schedule_reader AS rr', 'rr.schedid = rm.sysid AND rr.status = 1', 'left')
                ->join('gdlb_main AS G', 'G.sysid = gdlb.gdlbid')
                ->join('address_districts AS D', 'D.sysid = G.d')
                ->join('prime_system_users_legacy_code AS lc', 'lc.userid = rr.userid', 'left')
                ->join('prime_system_users AS u', 'u.sysid = rr.userid', 'left')
                ->join('person AS p', 'p.sysid = u.personid', 'left')
                ->where('rm.status', 1)
                ->group_by('rm.sysid, gdlb.gdlbid, rm.datesched, lc.telcode, p.lastname')
                ->get();
        }else {
            $where_ = '';
            if($get_sched) {
                $where_ .= ' AND rm.years = ' . $get_sched->years . ' AND rm.months = ' . $get_sched->months . ' ';
            }
            $query = $this->db->query("
                    SELECT rm.sysid, rm.datesched, lc.telcode, p.lastname, CONCAT(G.g, '-', D.codes, '-', G.l, '-', G.b) AS GDLBNAME
                    FROM customer_accounts_glb AS gdlb
                    INNER JOIN reading_schedule_main AS rm ON rm.gdlbid = gdlb.gdlbid
                    LEFT JOIN reading_schedule_reader AS rr ON rr.schedid = rm.sysid AND rr.status = 1
                    LEFT JOIN gdlb_main AS G ON G.sysid = gdlb.gdlbid
                    LEFT JOIN address_districts AS D ON D.sysid = G.d
                    LEFT JOIN prime_system_users_legacy_code AS lc ON lc.userid = rr.userid
                    LEFT JOIN prime_system_users AS u ON u.sysid = rr.userid
                    LEFT JOIN person AS p ON p.sysid = u.personid
                    WHERE rr.userid = {$userid} AND rm.status = 1 $where_
                    GROUP BY rm.sysid, gdlb.gdlbid, rm.datesched, lc.telcode, p.lastname
                ");
        }

        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                if(user_id() == 1) {
                    $text = $row->GDLBNAME . ' - ' . $row->datesched . ' - ' . $row->telcode;
                }else{
                    $text = $row->GDLBNAME . ' - ' . $row->datesched;
                }
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $text
                );
            }
        }
        return json_encode($data);
    }

    function get_reading_gdlb_sched_next() {

        $reader = $this->input->post('reader');
        $admin = $this->input->post('admin');

        $userinfo = get_person_userinfo($reader);
        $qry = false;
        $data = array();
        $info = false;


        // THIS WILL GET THE MONTHS AND YEAR CORRESPONDING TO THE NEW SCHEDULE ENCODED
        $get_sched = $this->db->query("SELECT months, years FROM reading_schedule_main WHERE status != 0 ORDER BY datecreated DESC")->row();

        if(user_id() == 1) {
            if($get_sched) {
                $this->db->where(array('rm.months' => $get_sched->months, 'rm.years' => $get_sched->years));
            }
            $query = $this->db->select('rm.sysid, rm.datesched, lc.telcode, p.lastname')
                ->select("CONCAT(G.g, '-', D.codes, '-', G.l, '-', G.b) AS GDLBNAME", false)
                ->from('customer_accounts_glb AS gdlb')
                ->join('reading_schedule_main AS rm', 'rm.gdlbid = gdlb.gdlbid')
                ->join('reading_schedule_reader AS rr', 'rr.schedid = rm.sysid AND rr.status = 1', 'left')
                ->join('gdlb_main AS G', 'G.sysid = gdlb.gdlbid')
                ->join('address_districts AS D', 'D.sysid = G.d')
                ->join('prime_system_users_legacy_code AS lc', 'lc.userid = rr.userid', 'left')
                ->join('prime_system_users AS u', 'u.sysid = rr.userid')
                ->join('person AS p', 'p.sysid = u.personid', 'left')
                ->where('rm.status', 1)
                ->group_by('rm.sysid, gdlb.gdlbid, rm.datesched, lc.telcode, p.lastname')
                ->get();
        }else {
            $where_ = '';
            if($get_sched) {
                $where_ .= ' AND rm.years = ' . $get_sched->years . ' AND rm.months = ' . $get_sched->months . ' ';
            }
            $query = $this->db->query("
                        SELECT rm.sysid, rm.datesched, lc.telcode, p.lastname, CONCAT(G.g, '-', D.codes, '-', G.l, '-', G.b) AS GDLBNAME
                        FROM customer_accounts_glb AS gdlb
                        INNER JOIN reading_schedule_main AS rm ON rm.gdlbid = gdlb.gdlbid
                        LEFT JOIN reading_schedule_reader AS rr ON rr.schedid = rm.sysid AND rr.status = 1
                        LEFT JOIN gdlb_main AS G ON G.sysid = gdlb.gdlbid
                        LEFT JOIN address_districts AS D ON D.sysid = G.d
                        LEFT JOIN prime_system_users_legacy_code AS lc ON lc.userid = rr.userid
                         JOIN prime_system_users AS u ON u.sysid = rr.userid
                        LEFT JOIN person AS p ON p.sysid = u.personid
                        WHERE rr.userid = {$userinfo->sysid} AND rm.status = 1 $where_
                        GROUP BY rm.sysid, gdlb.gdlbid, rm.datesched, lc.telcode, p.lastname
                    ");
        }

        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                if(user_id() == 1) {
                    $text = $row->GDLBNAME . ' - ' . $row->datesched . ' - ' . $row->telcode;
                }else{
                    $text = $row->GDLBNAME . ' - ' . $row->datesched;
                }
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $text
                );
            }
            $qry = true;
        }


        $data['userid'] = $userinfo->sysid;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function print_reading_sched() {

        $gdlbarr = explode(',', $this->input->post('lotbook'));
        $empinput = explode(',', $this->input->post('empid'));
        $readingdate = $this->input->post('readdate');
        $readingmonth = $this->input->post('rmonth');
        $readingyear = $this->input->post('ryear');
        $moduleid = $this->input->post('moduleid');

        $dataprint = date('Y-m-d H:m:i');

        $this->db->trans_begin();

        $query = $this->db->select('amoh.assetid, glb.accountid, cao.ownertype, cao.ownerid, amoh.assetid, glb.gdlbid AS gdlbid')
            ->from('customer_accounts_glb as glb')
            ->join('customer_accounts_owners as cao', 'glb.accountid = cao.accountid', 'left')
            ->join('assets_main_owner_history AS amoh', 'amoh.ownerid = cao.sysid AND amoh.status = 1', 'left')
            ->where_in('glb.gdlbid', $gdlbarr)
            ->get();
        $data = array();
        $html = '';
        $html .= '<table class="table table-condensed table-bordered">';
        $html .= '<thead><th>Seq.</th><th>Servno</th><th>Name</th><th>Mtr</th><th>Address</th><th>Reading</th></thead>';
        $html .= '<tbody>';
        $row_num = 0;
        $qry = false;
        if ($query) {

            $i = 0;
            foreach ($query->result() as $row) {
                $get_account_main = $this->db->select()->from('customer_accounts_main')->where('sysid', $row->accountid)->get()->row();
                $get_meter_details = $this->db->select()->from('customer_accounts_subscription_meter AS asm')
                    ->where(array('asm.assetid' => $row->assetid, 'asm.glbid' => $row->gdlbid))
                    ->get()->row();
                if ($get_meter_details) {
                    $mtrno = $get_meter_details->mtrno;
                } else {
                    $mtrno = '';
                }
                $i++;
                $name = get_ownership_details($row->ownertype, $row->ownerid)->name;
                $addr = get_ownership_details($row->ownertype, $row->ownerid)->addrspec;
                if ($get_account_main->servicenumber != '') {
                    $row_num += 1;
                    $html .= '<tr>';
                    $html .= '<td>' . $i . '</td>';
                    $html .= '<td>' . $get_account_main->servicenumber . '</td>';
                    $html .= '<td>' . $name . '</td>';
                    $html .= '<td>' . $mtrno . '</td>';
                    $html .= '<td>' . $addr . '</td>';
                    $html .= '<td></td>';
                    $html .= '</tr>';
                }
            }
            if ($row_num > 0) {
                $qry = true;
            }
        } else {
            $html .= '<tr><td colspan="6">No record found!</td></tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $data['html'] = $html;
        $data['gdlb'] = $gdlbarr;
        $data['qry'] = $qry;
        $data['dateprint'] = $dataprint;
        return json_encode($data);
    }

    function submit_reading() {
        $data = array();

        $input              =   $this->input->post();
        $mtrid_arr          =   $this->input->post('mtrid');
        $reading_arr        =   $this->input->post('reading');
        $schedid            =   $this->input->post('schedid');
        $damand_arr         =   $this->input->post('demand');
        $netmtr_arr         =   $this->input->post('netmtr');
        //$damand_arr = $this->input->post('demand');
        //$schedid = $this->input->post('schedid');

        $this->db->trans_begin();
        $cnt = 0;
        if( count($reading_arr) >0 ) {
            foreach($reading_arr as $acctid => $row) {
                if($row!='') {
                    $mtrid = $mtrid_arr[$acctid];
                    $reading = $reading_arr[$acctid];
                    $demand = (isset($damand_arr[$acctid])) ? $damand_arr[$acctid] : 0;
                    $netmtr = (isset($netmtr_arr[$acctid])) ? $netmtr_arr[$acctid] : 0;

                    $cnt += 1;

                    //UPDATE FIRST
                    $this->db->where(array('schedid' => $schedid, 'acctid' => $acctid, 'mtrid' => $mtrid, 'status' => 1));
                    $this->db->update('customer_accounts_subscription_meter_reading_logs', array('status' => 0));
                    $ins_reading_logs_arr = array(
                        'schedid' => $schedid,
                        'acctid' => $acctid,
                        'mtrid' => $mtrid,
                        'reading' => $reading,
                        'createdby' => user_id(),
                        'updatedby' => user_id()
                    );

                    if( $demand > 0 ) {
                        $this->db->set('demand', $demand);
                    }
                    if( $netmtr > 0 ) {
                        $this->db->set('netmtr', $netmtr);
                    }

                    $this->db->insert('customer_accounts_subscription_meter_reading_logs', $ins_reading_logs_arr);
                    $data['submited'][] = $ins_reading_logs_arr;

                }
            }
        }
        $title = 'Reading entry';
        if ($this->db->trans_status() == FALSE && $cnt > 0) {
            $this->db->trans_rollback();
            $msg = $cnt.' record saved!';
            $func = 'warning';
            $q = false;
        } else {
            $this->db->trans_commit();
            $msg = $cnt.' record saved!';
            $func = 'success';
            $q = true;
        }

        $data['qry'] = $q;
        $data['cnt'] = $cnt;
        $data['input'] = $input;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = $title;
        return json_encode($data);
    }

    function submit_reading_recheck() {
        ini_set('max_input_vars', 5000);
        $data = array();


        $input              =   $this->input->post();
        $mtrid_arr          =   $this->input->post('mtrid');
        $reading_arr        =   $this->input->post('reading');
        $schedid            =   $this->input->post('schedid');
        $damand_arr         =   $this->input->post('demand');
        $finding_arr        =   $this->input->post('findings');
        //$damand_arr = $this->input->post('demand');
        //$schedid = $this->input->post('schedid');

        $this->db->trans_begin();
        $cnt = 0;
        if( count($reading_arr) >0 ) {
            foreach($reading_arr as $acctid => $row) {
                if($row!='') {
                    $mtrid = $mtrid_arr[$acctid];
                    $reading = $reading_arr[$acctid];
                    $demand = (isset($damand_arr[$acctid])) ? $damand_arr[$acctid] : 0;
                    $finding = (isset($finding_arr[$acctid])) ? $finding_arr[$acctid] : false;

                    $cnt += 1;

                    //UPDATE FIRST
                    $this->db->where(array('schedid' => $schedid, 'acctid' => $acctid, 'mtrid' => $mtrid, 'status' => 1));
                    $this->db->update('customer_accounts_subscription_meter_reading_logs', array('status' => 0));
                    $ins_reading_logs_arr = array(
                        'schedid' => $schedid,
                        'acctid' => $acctid,
                        'mtrid' => $mtrid,
                        'reading' => $reading,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                        'types' => 2
                    );

                    if( $demand > 0 ) {
                        $this->db->set('demand', $demand);
                    }

                    $this->db->insert('customer_accounts_subscription_meter_reading_logs', $ins_reading_logs_arr);
                    $reading_id = $this->db->insert_id();
                    if($finding>0) {
                        $findings_ins_arr = array('findingid' => $finding, 'readingid' => $reading_id, 'createdby' => user_id(), 'updatedby' => user_id());
                        $this->db->insert('trn_reading_findings', $findings_ins_arr);
                    }
                    $data['submited'][] = $ins_reading_logs_arr;

                }
            }
        }
        $title = 'Reading entry';
        if ($this->db->trans_status() == FALSE && $cnt > 0) {
            $this->db->trans_rollback();
            $msg = $cnt.' record saved!';
            $func = 'warning';
            $q = false;
        } else {
            $this->db->trans_commit();
            $msg = $cnt.' record saved!';
            $func = 'success';
            $q = true;

        }

        $data['qry'] = $q;
        $data['cnt'] = $cnt;
        $data['input'] = $input;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = $title;
        return json_encode($data);
    }

    function update_findings_row() {
        $userid = user_session()->system_user_sessid;
        $reading_id = $this->input->post('readingid');
        $finding_id = $this->input->post('id');

        $this->db->where(array('readingid' => $reading_id, 'status' => 1));
        $this->db->update('trn_reading_history_findings', array('status' => 0));
        $findings_ins_arr = array(
            'findingid' => $finding_id,
            'types' => 1,
            'readingid' => $reading_id,
            'createdby' => $userid
        );

        $this->db->insert('trn_reading_history_findings', $findings_ins_arr);
        $findings_main_id = $this->db->insert_id();

        $q = $this->input->post('id');
        $qry = $this->db->select()->from('meter_reading_findings_sub')->where('findingsid', $q)->get();
        if ($qry->num_rows() > 0) {
            $q = true;
            foreach ($qry->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->codes . ' - ' . $row->descriptions);
            }
        } else {
            $q = false;
        }

        $data['input'] = $this->input->post();
        $data['qry'] = $q;
        return json_encode($data);
    }

    function compute_add_bill() {
        $q = true;
        $data = array();
        $msg = '';
        $title = '';
        $func = 'error';
        $input_arr = $this->input->post();
        $schedid = $this->input->post('schedid');
        $acctid_addbill = $this->input->post('acctids');
        $userid = user_id();
        $acct_arr = array();
        $this->db->trans_begin();
        if($acctid_addbill) {
            if (count($acctid_addbill) > 0) {
                foreach ($acctid_addbill as $ab_row) {
                    $acctid = $ab_row['checked'];
                    $acct_arr[] = $acctid;
                }
            }
        }


        // GET BILING DETAILS
        $qry_sched = $this->db->select()
            ->from('reading_schedule_main')->where('sysid', $schedid)
            ->get()->row();

        if($qry_sched) {
            $sched_month = $qry_sched->months;
            $sched_year = $qry_sched->years;
            $curr_month_days = cal_days_in_month(CAL_GREGORIAN, $sched_month, $sched_year);
            if(count($acct_arr)>0) {
                $acct_main = $this->db->select('sysid, servicenumber AS servno, dateconnected')
                    ->from('customer_accounts_main')
                    ->where_in('sysid', $acct_arr)
                    ->get();
                $res_arr = array();
                if ($acct_main->num_rows() > 0) {
                    foreach ($acct_main->result() as $row) {
                        $billid = 0;
                        $acctid = $row->sysid;

                        $datecon = $row->dateconnected;
                        $todate = date('Y-m-d');
                        if (validateDate($datecon) === true) {

                            $prev_month = ($sched_month == 1) ? 12 : $sched_month - 1;
                            $prev_year = ($sched_month == 1) ? $sched_year - 1 : $sched_year;

                            $month_cnt = nb_mois($datecon, $todate);

                            $count_bases = '';
                            $prev_kwh = 0;
                            if ($month_cnt <= 1) {
                                $kwh_sum = 0;

                                $now = time(); // or your date as well
                                $your_date = strtotime($datecon);
                                $datediff = $now - $your_date;

                                $days_cnt = floor($datediff / (60 * 60 * 24));

                                if ($days_cnt < 1) {
                                    $rem = 'DATE RANGE ERROR';
                                } else {
                                    $rem = 'ADD BILL';
                                    $count_bases = 'Days : ' . $days_cnt . ' Current Month Days : ' . $curr_month_days;
                                    // GET PREVIOUS KWH WITH / REG BILL
                                    $this->db->where('kwhuse != ', 0);
                                    $qry_billrep = $this->db->select('kwhuse')
                                        ->from('billing_reports_main')
                                        ->where(array('acctid' => $acctid))
                                        ->order_by('prsdte', 'desc')
                                        ->get()->row();
                                    if ($qry_billrep) {
                                        $prev_kwh = $qry_billrep->kwhuse;
                                        $billid = $qry_billrep->sysid;
                                    }
                                }
                            } else {
                                $count_bases = 'Month : ' . $month_cnt;

                                $month_start = ($sched_month==1) ? 12 : ($sched_month - 1);
                                $comp = $this->model_peco->compute_acct_kwh_average($row->sysid, $month_start);

                                $kwh_sum = 0;
                                $num_month = $comp->months;
                                if ($num_month > 0) {

                                    $kwh_sum = $comp->kwh;

                                    if ($kwh_sum > 0) {
                                        $rem = 'ADD BILL';
                                        $types = 1;
                                    } else {
                                        // @TODO GET RV FOR POLICY PROCESS
                                        $rem = 'RV CHECKING 1';
                                        $types = 2;
                                    }
                                } else {
                                    // @TODO GET RV FOR POLICY PROCESS
                                    $rem = 'RV CHECKING 2';
                                    $types = 2;
                                }

                                if($kwh_sum>0) {
                                    $prev_kwh = $comp->average;
                                }else{
                                    $prev_kwh = 0;
                                }
                            }


                            // UPDATE FIRST
                            $upd_arr = array(
                                'schedid' => $schedid,
                                'status' => 1
                            );
                            $this->db->where($upd_arr);
                            $this->db->update('trn_reading_analysis_compute', array('status' => 1));

                            // RES RESPONSE
                            $ins_arr = array(
                                'acctid' => $acctid,
                                'schedid' => $schedid,
                                'datecon' => $datecon,
                                'dateprs' => $todate,
                                'ckwh' => $prev_kwh,
                                'sumkwh' => $kwh_sum,
                                'prvmonth' => $prev_month,
                                'prvyear' => $prev_year,
                                'remarks' => $rem,
                                'createdby' => $userid,
                                'types' => $types,
                                'status' => 1
                            );
                            $error_msg = '';
                            $ins_qry = $this->db->insert('trn_reading_analysis_compute', $ins_arr);
                            $error_msg = $this->db->_error_message();
                            $res_arr[] = array('ins' => $error_msg);
                            $data['resarr'] = $res_arr;
                        }
                    }
                }
                //$send_to_addbill = create_transaction_trails('Compute Billing', 'Add/Late Bills', 19, $schedid);
                if ($this->db->trans_status() === TRUE
                    //&& $send_to_addbill==true
                ) {
                    $this->db->trans_commit();
                    $msg = 'Computation Complete!';
                    $func = 'success';
                } else {
                    $this->db->trans_rollback();
                    $msg = 'Query Error';
                    $func = 'error';
                }
            }else{
                $msg = 'No selected account to compute!';
                $func = 'warning';
            }
        }else{
            $msg = 'Schedule not found!';
            $func = 'warning';
        }

        $data['msg'] = $msg;
        $data['title'] = 'Compute Add/Late Bills';
        $data['func'] = $func;
        $data['qry'] = true;
        $data['inputs'] = $input_arr;
        return json_encode($data);
    }

    function send_recheck() {
        $q = false;
        $msg = '';
        $func = 'error';
        $userid = $user_id = user_session()->system_user_sessid;
        $input_arr = $this->input->post();
        $schedid = $this->input->post('schedid');
        $moduleid = 19;
        $acctid_recheck = $this->input->post('recheckarr');
        $recheck_arr = array();
        if(count($acctid_recheck)>0) {
            foreach($acctid_recheck as $rc_row) {
                $recheck_arr[] = $rc_row['checked'];
            }
        }
        $this->db->trans_begin();
        $this->db->where(array('schedid' => $schedid, 'status' => 1));
        $this->db->where_in('acctid', $recheck_arr);
        $this->db->update('trn_reading_analysis_logs', array('types' => 2));
        $updated = $this->db->affected_rows();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $msg = 'Error sending transaction!';
            $func = 'warning';
        }else{
            if(create_transaction_trails('MRD-RECHECK', 'Analysis - Recheck Reading', $moduleid, $schedid)) {
                if($updated>0) {
                    $this->db->trans_commit();
                    $q = true;
                    $msg = $updated . ' Send for rechecking..';
                    $func = 'success';
                }else{
                    $this->db->trans_rollback();
                    $q = true;
                    $msg = 'Process analysis first!';
                    $func = 'warning';
                }
            }else{
                $this->db->trans_rollback();
                $msg = 'Error sending transaction!';
                $func = 'warning';
            }

        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['inputs'] = $input_arr;
        $data['qry'] = $q;
        return json_encode($data);
    }

    function process_analysis() {
        $q = false;
        $userid = $user_id = user_session()->system_user_sessid;
        $data = array();
        $content = '';
        $meter_readers = '';
        $gdlb_name = '';
        $input_arr = $this->input->post();
        $schedid = $this->input->post('schedid');
        $acctid_recheck = $this->input->post('recheckarr');
        $acctid_addbill = $this->input->post('zeroconarr');
        $recheck_arr = array();
        $addbill_arr = array();
        if($acctid_recheck) {
            if(count($acctid_recheck)>0) {
                foreach ($acctid_recheck as $rc_row) {
                    $recheck_arr[] = $rc_row['checked'];
                }
            }
        }
        if($addbill_arr) {
            if(count($acctid_addbill)>0) {
                foreach ($acctid_addbill as $ab_row) {
                    $addbill_arr[] = $ab_row['checked'];
                }
            }
        }

        $get_sched_details = $this->db->select('sysid, gdlbid, months, years, datesched')
            ->from('reading_schedule_main')
            ->where('sysid', $schedid)
            ->get()->row();
        if ($get_sched_details) {
            $sched_month = $get_sched_details->months;
            $sched_year = $get_sched_details->years;
            $sched_gdlb = $get_sched_details->gdlbid;
            $sched_date = $get_sched_details->datesched;

            $gdlb_name = get_gdlb_name($sched_gdlb);

            $get_readers_details = $this->db->select('r.userid, u.firstname, u.lastname')
                ->from('reading_schedule_reader AS r')
                ->join('prime_system_users AS u', 'u.sysid = r.userid', 'left')
                ->where(array('r.schedid' => $get_sched_details->sysid, 'r.status' => 1))
                ->group_by('r.userid, u.firstname, u.lastname')
                ->get();
            $reader_nums = $get_readers_details->num_rows();
            if ($reader_nums > 0) {
                if ($reader_nums > 1) {
                    foreach ($get_readers_details->result() as $row_u) {
                        $meter_readers .= $row_u->lastname . ', ' . $row_u->firstname . ', ';
                    }
                } else {
                    foreach ($get_readers_details->result() as $row_u) {
                        $meter_readers .= $row_u->lastname . ', ' . $row_u->firstname;
                    }
                }
            }

            $prev_month = ($sched_month == 1) ? 12 : $sched_month - 1;
            $prev_year = ($sched_month == 1) ? $sched_year - 1 : $sched_year;
            $where_in_merged = array_merge($recheck_arr, $addbill_arr);

            $data['arrmerge'] = $where_in_merged;

            if (is_array($where_in_merged)) {
                $query = $this->db->select("
                    mr.mrseq AS SEQ,
                    acct.sysid AS SYSID,
                    acct.servicenumber AS SERVNO,
                    acct.types AS OWNERTYPE,
                    acct.ownerid AS OWNERID,
                    acct.mtr AS MTR,
                    acct.mtrno AS MTRNO,
                    acct.mtrserial AS MTRSER,
                    acct.rateclassid AS RATEID,
                    bm.codes AS MULTCODE,
                    bm.sysid AS MULTID,
                    br.prsrdg AS PREVRDG,
                    br.kwhuse AS PREVKWH")
                    ->from('customer_accounts_main AS acct')
                    ->join('billing_reports_main AS br', 'br.acctid = acct.sysid', 'left')
                    ->join('billing_rates_main_multiplier AS bm', ' bm.sysid = acct.multid', 'left')
                    ->join('customer_accounts_mtrseq AS mr', 'mr.acctid = acct.sysid AND mr.status = 1', 'left')
                    ->where(array('acct.gdlb' => $sched_gdlb, 'br.year' => $prev_year, 'br.month' => $prev_month))
                    ->where_in('acct.sysid', $where_in_merged)
                    ->group_by('
                        mr.mrseq,
                        acct.sysid,
                        acct.servicenumber,
                        acct.types,
                        acct.ownerid,
                        acct.mtr,
                        acct.mtrno,
                        acct.mtrserial,
                        acct.rateclassid,
                        bm.codes,
                        bm.sysid,
                        br.prsrdg,
                        br.kwhuse
                    ')
                    ->order_by('mr.mrseq')
                    ->get();


                $content .= '<h4 style="width: 100%; padding: 0px 10px;">Billing: ' . $sched_month . '-' . $sched_year . ' | Reading Schedule: '.$sched_date.' <span class="pull-right">' . $gdlb_name . '</span></h4>';
                $content .= '<table class="table table-condensed tbl-sm print-table-standard">';
                $content .= '<thead>';
                $content .= '<th>Seq</th>';
                $content .= '<th>Service #</th>';
                $content .= '<th>Name</th>';
                $content .= '<th>MTR</th>';
                $content .= '<th>Meter #</th>';
                $content .= '<th>Serial</th>';
                $content .= '<th>Mult</th>';
                $content .= '<th>Prev. Rdng</th>';
                $content .= '<th>Pres. Rdng</th>';
                $content .= '<th>Prev. Kwh</th>';
                $content .= '<th>Pres. Kwh</th>';
                $content .= '<th>Remarks</th>';
                $content .= '<th width="200px">Corrections</th>';
                $content .= '</thead>';

                if ($query->num_rows() > 0) {
                    $q = true;
                    $readcnt = 0;
                    $totalprskwh = 0;
                    $totalprvkwh = 0;
                    $i = 1;
                    foreach ($query->result() as $row) {
                        $sysid = $row->SYSID;
                        $custinfo = get_ownership_details($row->OWNERTYPE, $row->OWNERID);
                        $name = ($custinfo) ? $custinfo->name : 'Unknown';
                        $prev_read = $row->PREVRDG;
                        $prev_cons = $row->PREVKWH;
                        $qry_reading = $this->db->query("
                                        SELECT mrl.reading, mrl.demand
                                        FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                        WHERE mrl.acctid = $sysid AND mrl.schedid = $schedid AND mrl.status = 1
                            ")->row();

                        $pres_read = 0;
                        $pres_cons = 0;
                        $pres_dems = 0;
                        if ($qry_reading) {
                            $pres_read = $qry_reading->reading;
                            $pres_cons = $pres_read - $prev_read;
                            $pres_dems = $qry_reading->demand;

                            $qry_rateclass_group = $this->db->select('rs.descs')
                                ->from('rate_class_specification AS rs')
                                ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                                ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                                ->get()->row();

                            if ($qry_rateclass_group) {
                                $demand = '<div class="input-icon left">' .
                                    '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                                    '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $pres_dems . '"/>' .
                                    '</div>';
                            } else {
                                $demand = '<span class="label label-danger">N/A</span>';
                            }

                            $readcnt += 1;
                            if ($pres_cons > 0) {
                                $totalprskwh += $pres_cons;
                            }
                        }
                        if ($prev_cons > 0) {
                            $totalprvkwh += $prev_cons;
                        }


                        $check = readcheck($pres_cons, $prev_cons, $pres_read, $prev_read);
                        $incdec = $check->per;
                        $prev_cons = $row->PREVKWH;
                        $prev_read = $row->PREVRDG;


                        $content .= '<tr>';
                        $content .= '<td>' . $row->SEQ . '</td>';
                        $content .= '<td>' . $row->SERVNO . '</td>';
                        $content .= '<td>' . $name . '</td>';
                        $content .= '<td>' . $row->MTR . '</td>';
                        $content .= '<td>' . $row->MTRNO . '</td>';
                        $content .= '<td>' . $row->MTRSER . '</td>';
                        $content .= '<td>' . $row->MULTCODE . '</td>';
                        $content .= '<td class="number">' . number_format($prev_read) . '</td>';
                        $content .= '<td class="number">' . number_format($pres_read) . '</td>';
                        $content .= '<td class="number">' . number_format($prev_cons) . '</td>';
                        $content .= '<td class="number">' . number_format($pres_cons) . '</td>';
                        $content .= '<td>' . $check->rem . '</td>';
                        $content .= '<td></td>';
                        $content .= '</tr>';

                        $logs_where_arr = array(
                            'schedid' => $get_sched_details->sysid,
                            'acctid' => $sysid,
                            'types' => 1,
                            'status' => 1
                        );
                        $this->db->where($logs_where_arr);
                        $this->db->update('trn_reading_analysis_logs', array('status' => 0));

                        // INSERT INTO LOGS
                        $logs_ins_arr = array(
                            'schedid' => $get_sched_details->sysid,
                            'acctid' => $sysid,
                            'mtr' => $row->MTR,
                            'gdlbid' => $sched_gdlb,
                            'multid' => $row->MULTID,
                            'mtrser' => $row->MTRSER,
                            'mtrno' => $row->MTRNO,
                            'prvrdg' => $prev_read,
                            'prsrdg' => $pres_read,
                            'prvkwh' => $prev_cons,
                            'prskwh' => $pres_cons,
                            'demand' => $pres_dems,
                            'incdec' => $incdec,
                            'remarks' => $check->rem,
                            'createdby' => $user_id,
                            'updatedby' => $user_id,
                            'types' => 1
                        );
                        $this->db->insert('trn_reading_analysis_logs', $logs_ins_arr);
                    }
                }


                $content .= '</table>';
            }
        }
        $data['qry'] = $q;


        $reptitle = 'Reading Analysis Reports';
        $header = peco_print_header('', $reptitle, 'MRD', false);

        $data['header'] = $header;
        $data['dates'] = date('Y-m-d');
        $data['gdlb'] = $gdlb_name;
        $data['content'] = $content;
        $data['gdlbid'] = $sched_gdlb;
        $data['schedid'] = $schedid;
        $data['inputs'] = $input_arr;
        return json_encode($data);
    }

    function get_analysis_logs_html($schedid = false, $gdlbid = false) {
        $data = array();
        $q = false;

        $html = '';
        if ($schedid && $gdlbid) {

            $qry = $this->db->select()
                ->from('trn_reading_analysis_logs')
                ->where(array(
                        'status' => 1,
                        'schedid' => $schedid,
                        'gdlbid' => $gdlbid
                    )
                )->get();
            if($qry->num_rows() > 0) {

                $html .= '<div style="position: absolute; top: 40px; padding-top: 5px; left: 0px; width: 100%; height: 120px;">';
                $html .= '<p style="font-weight: normal; font-size: 12px; margin: 0px 0px; padding: 0px 0px; margin: 0px 0px; padding: 0px 0px; margin-top: 0px; margin-left: 0px;" class="charges-list-item">';
                $html .= '<span style="font-family: courier, monospace; position: absolute; left: 0px; font-weight: bold;">Meter Reader(s): </span>';
                $html .= '</p>';
                $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';

                foreach($qry->result() as $row) {
                    $html .= '<div style="position: relative; height: 170px; white-space: nowrap; width: 100%; margin-bottom: 10px; padding-bottom: 2px;">';
                    $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 12px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
                    $html .= $row->acctid;
                    $html .= '</p>';

                    $html .= '<footer class="printout"></footer>';
                    $html .= '</div>';
                }
                $html .= '</p>';

                $q = true;
            }
            $html .= '</body>';
            $html .= '</html>';
        }
        $data['html'] = $html;
        $data['qry'] = $q;
        return (object)$data;
    }

    function print_analysis_reports($schedid = false, $gdlbid = false)
    {
        $html = '';
        if ($schedid && $gdlbid) {


            $html = '';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
            $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
            $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
            $html .= '</head>';
            $html .= '<body>';
            $html .= operations_print_header('M-01-01', 'Reading Analysis', 'M-01-01', true);

            $qry = $this->db->select()
                ->from('trn_reading_analysis_logs')
                ->where(array(
                        'status' => 1,
                        'schedid' => $schedid,
                        'gdlbid' => $gdlbid
                    )
                )->get();
            if($qry->num_rows() > 0) {
                $page = 1;
                foreach ($qry->result() as $row) {
                    //$form_payslip = form_payslip_single(159, 1, 2019, 1, 1, false , $page++);
                    //if($form_payslip->res) {
                    //    $html .= $form_payslip->html;
                    //}
                    $html .= '<div style="position: relative; height: 20; white-space: nowrap; width: 100%; margin-bottom: 10px; border-bottom: 1px dashed #ccc; padding-bottom: 2px;">';
                    $html .= $row->acctid;
                    $html .= '<footer class="printout"></footer>';
                    $html .= '</div>';

                }
            }

            $filename = 'TEST.pdf';
            //echo $html;
            //exit();
            $this->load->library('pdf');
            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $customPaper = array(0, 0, 610, 910);
            // $dompdf->setPaper($customPaper, 'landscape');
            $dompdf->render();
            // Add PDF Document Information
            $dompdf->add_info('Subject', 'PECO PAYSLIP | ' . $filename);
            $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
            $dompdf->add_info('Creator', 'ITD');
            $dompdf->add_info('Keywords', 'Payslip');
            $dompdf->stream($filename);

        }
    }

    function update_analysis_row() {
        $data = array();
        $findingid = $this->input->post('findingid');
        $mtrno = $this->input->post('mtrno');
        $reading = $this->input->post('reading');
        $demand = $this->input->post('demand');
        $netmtr = $this->input->post('netmtr');
        $schedid = $this->input->post('schedid');
        $acctid = $this->input->post('acctid');
        $remarks = $this->input->post('remarks');



        if($findingid > 0 && $mtrno > 0) {
            if($schedid > 0) {
                // UPDATE READING FIRST
                $this->db->where(array(
                    'mtrid' => $mtrno,
                    'acctid' => $acctid,
                    'schedid' => $schedid,
                    'status' => 1
                ));
                $this->db->update('customer_accounts_subscription_meter_reading_logs', array('status' => 0));

                // INSERT READING FIRST
                $reading_ins = array(
                    'reading' => $reading,
                    'mtrid' => $mtrno,
                    'acctid' => $acctid,
                    'schedid' => $schedid,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'types' => 2
                );
                if ($demand) {
                    $this->db->set('demand', $demand);
                }
                if ($netmtr) {
                    $this->db->set('netmtr', $netmtr);
                }
                $this->db->insert('customer_accounts_subscription_meter_reading_logs', $reading_ins);
                $data['errlogs'] = $this->db->_error_message();
                $reading_id = $this->db->insert_id();

                // INSERT FINDINGS
                $findings_ins = array(
                    'findingid' => $findingid,
                    'schedid' => $schedid,
                    'acctid' => $acctid,
                    'mtrno' => $mtrno,
                    'readingid' => $reading_id,
                    'createdby' => user_id(),
                    'updatedby' => user_id(),
                    'remarks' => $remarks,
                );
                $this->db->insert('trn_reading_findings', $findings_ins);
            }
        }

        $data['inputs'] = $this->input->post();
        return json_encode($data);
    }

    function upload_external_file() {
        $data = array();
        $qry = false;
        $func = 'error';
        $msg = SYSTEM_MSG_QRY;
        $title = PECO_MRD_A;

        $viewtype = $this->input->post('showall');

        $data = array();
        $data_arr = array();
        $data_tbl = array();
        $file_info = pathinfo($_FILES["extfile"]["name"]);

        if (!is_dir(FCPATH . 'uploads/temp')) {
            mkdir('uploads/temp', 0755, TRUE);
            chmod('uploads/temp', 0755);
        }
        $file_directory = "uploads/temp/";

        $filetype = '';

        $new_file_name = date("d-m-Y") . rand(000000, 999999) . "." . $file_info["extension"];

        $file = $file_directory.$new_file_name;

        $group_ins_arr = array(
            'createdby' => user_id(),
            'updatedby' => user_id(),
        );
        $this->db->insert('billing_reports_ext_group', $group_ins_arr);
        $group_id = $this->db->insert_id();

        if (move_uploaded_file($_FILES["extfile"]["tmp_name"], $file)) {
            $file_type = PHPExcel_IOFactory::identify($file);
            $objReader = PHPExcel_IOFactory::createReader($file_type);
            $objPHPExcel = $objReader->load($file);
            $sheet_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $i = 0;
            foreach ($sheet_data as $exrow) {
                if($i>0) {

                    $prskwh = 0;
                    if($exrow['J'] > 0) {
                        $prskwh = $exrow['J'] - $exrow['I'];
                    }



                    $ins_arr = array(
                        'billno' => $exrow['A'], // EXCEL A (billno) column
                        'billmo' => $exrow['B'], // EXCEL B (billmo) column
                        'billyr' => $exrow['C'], // EXCEL C (billyr) column
                        'servno' => $exrow['D'], // EXCEL D (servno) column
                        'mtr' => $exrow['E'], // EXCEL E (mtr) column
                        'class' => $exrow['F'], // EXCEL F (class) column
                        'mtrser' => $exrow['G'], // EXCEL G (mtrser) column
                        'serial' => $exrow['H'], // EXCEL H (serial) column
                        'prvrdg' => $exrow['I'], // EXCEL I (prvrdg) column
                        'reading' => $exrow['J'], // EXCEL J (reading) column
                        'kwh' => $exrow['K'], // EXCEL K (kwh) column
                        'prvdte' => date_formating($exrow['L'], 'm/d/Y', 'Y-m-d'),
                        'rdgdte' => date_formating($exrow['M'], 'm/d/Y H:i', 'Y-m-d H:i'),
                        'duedate' =>date_formating($exrow['N'], 'm/d/Y', 'Y-m-d'),
                        'dlvdte' => date_formating($exrow['O'],'m/d/Y H:i', 'Y-m-d H:i'),
                        'remcode' => $exrow['P'],
                        'remarks' => $exrow['Q'],
                        'reader' => $exrow['R'],
                        'isimprb' => $exrow['S'],
                        'isavg' => $exrow['T'],
                        'isreset' => $exrow['U'],
                        'isprntd' => $exrow['V'],
                        'isdlvrd' => $exrow['W'],
                        'distchg' => $exrow['X'],
                        'demchg' => $exrow['Y'],
                        'supchg' => $exrow['Z'],
                        'mtrchgkwh' => $exrow['AA'],
                        'mtrchg' => $exrow['AB'],
                        'genchg' => $exrow['AC'],
                        'prevadjmo' => $exrow['AD'],
                        'prevadjyr' => $exrow['AE'],
                        'trnchg' => $exrow['AF'],
                        'syslosschg' => $exrow['AG'],
                        'iccschgsub' => $exrow['AH'],
                        'lifelinechg' => $exrow['AI'],
                        'genvat' => $exrow['AJ'],
                        'tranvat' => $exrow['AK'],
                        'othervat' => $exrow['AL'],
                        'franchg' => $exrow['AM'],
                        'misschg' => $exrow['AN'],
                        'envchg' => $exrow['AO'],
                        'npcchg' => $exrow['AP'],
                        'iccschgadj' => $exrow['AQ'],
                        'fitamt' => $exrow['BE'],
                        'sendsc' => $exrow['AR'],
                        'curamt' => $exrow['AS'],
                        'balance' => $exrow['AT'],
                        'interest' => $exrow['AU'],
                        'amtdue' => $exrow['AV'],
                        'schedreaddate' => date_formating($exrow['AW'], 'm/d/Y H:i', 'Y-m-d H:i'),
                        'pnisrecv' => $exrow['AX'],
                        'pnrecvdte' => date_formating($exrow['AY'],'m/d/Y H:i', 'Y-m-d H:i'),
                        'pnrecvby' => $exrow['AZ'],
                        'recvby' => $exrow['BA'],
                        'hash' => $exrow['BC'],
                        'mrseq' => $exrow['BD'],
                    );
                    $ins = $this->db->insert('billing_reports_ext', $ins_arr);
                    $repid = $this->db->insert_id();
                    if($ins) {

                        $recheck = false;
                        $recheck_msg = '';

                        $acct_info = $this->db->select(
                            '
                            m.sysid, 
                            m.types, 
                            m.ownerid, 
                            m.multid, 
                            m.mtrno,
                            mult.codes AS multcode, 
                            mult.rate AS multrate,
                            m.gdlb
                            ')
                            ->from('customer_accounts_main AS m')
                            ->join('billing_rates_main_multiplier AS mult', 'mult.sysid = m.multid', 'left')
                            ->where(array('m.servicenumber' => $exrow['D'], 'm.mtr' => $exrow['E']))
                            ->get()->row();

                        if ($acct_info) {
                            $name_arr = get_ownership_details($acct_info->types, $acct_info->ownerid);
                            $name = '';

                            if($acct_info->types==5) {
                                $name = ucwords(strtolower($name_arr->name));
                            }



                            // @TODO create types info for new account.

                            //GET SCHEDULES
                            $qry_sched = $this->db->select('sysid, datesched')
                                ->from('reading_schedule_main')
                                ->where(array('status' => 1, 'years' => $exrow['C'], 'months' => $exrow['B'], 'gdlbid' => $acct_info->gdlb))
                                ->get()->row();
                            $schedid = ($qry_sched) ? $qry_sched->sysid : 0;

                            if($qry_sched) {
                                $reading_ins = array(
                                    'reading' => $exrow['J'],
                                    'mtrid' => $exrow['G'],
                                    'acctid' => $acct_info->sysid,
                                    'schedid' => $qry_sched->sysid,
                                    'createdby' => user_id(),
                                    'updatedby' => user_id(),
                                );
                                $this->db->insert('customer_accounts_subscription_meter_reading_logs', $reading_ins);
                                $data['insertreaderr'][] = $this->db->_error_message();
                            }

                            $prev_month = ($exrow['B'] == 1) ? 12 : $exrow['B'] - 1;
                            $prev_year = ($exrow['C'] == 1) ? $exrow['C'] - 1 : $exrow['C'];


                            $compute = compute_billing($acct_info->sysid, $exrow['C'], $exrow['B'], $exrow['K']);
                            if($compute) {
                                if (round($compute['current']) != round($exrow['AS'])) {
                                    $recheck_msg = 'Amount is not equal (Computed: ' . $compute['current'] . ' / FILE: ' . $exrow['AS'] . ')';
                                    $recheck = true;
                                }
                            }

                            $prvrdg_stat = true;
                            $prvkwh = 0;
                            $prvrdg = 0;
                            // GET PREVIOUS CONSUMPTION
                            $qry_previous_bill = $this->db->select('kwhuse, prsrdg')
                                ->from('billing_reports_main')
                                ->where(array('acctid' => $acct_info->sysid, 'year' => $prev_year, 'month' => $prev_month))
                                ->get()->row();
                            if($qry_previous_bill) {
                                $prvkwh = round($qry_previous_bill->kwhuse, 0);
                                if($qry_previous_bill->prsrdg != $exrow['I']) {
                                    $prvrdg_stat = false;
                                }
                                $prvrdg = $qry_previous_bill->prsrdg;
                            }


                            $demand = '<span class="label label-danger">N/A</span>';
                            $netmtr = '<span class="label label-danger">N/A</span>';
                            $multcd = '<span class="text-bold">' . $acct_info->multcode . '</span><span class="pull-right">' . number_format($acct_info->multrate, 2) . '</span>';

                            $check = readcheck($prskwh, $prvkwh, $exrow['J'], $exrow['I']);

                            if($check && $check->recheck == true) {
                                $recheck = true;
                            }

                            $incdec_tooltip = ($check->per > 0) ? 'Consumptions Increased!' : 'Consumptions Decreased!';

                            $incdec = ($check) ? $check->icon . ' ' . '<a href="javascript:;" title="'.$incdec_tooltip.'" data-placement="left" class="tooltips"><span class="pull-right">'.number_format($check->per, 2).' %</span></a>' : 'N/A';


                            $qry_findings = $this->db->select('rf.codes')
                                ->from('trn_reading_findings AS trf')
                                ->join('meter_reading_findings AS rf', 'rf.sysid = trf.findingid')
                                ->where(array('acctid' => $acct_info->sysid, 'mtrno' => $acct_info->mtrno))
                                ->get()->row();
                            $findings_text = ($qry_findings) ? $qry_findings->codes : '';

                            //$findings = '<a class="tooltips" style="margin:0px 0px !important;" href="javascript:;" id="findings_input" title="Findings Entry" data-placement="left" data-type="findings" data-pk="' . $exrow['G'] . '" data-value="" data-original-title="Select Findings">Findings..</a>';
                            $findings = '<input id="input_findings" class="form-control input-xs inline" placeholder="Findings.." value="'.$findings_text.'"/>';


                            $reading = '<div class="input-icon left">' .
                                '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                                '<input autocomplete="off" name="reading[' . $repid . ']" placeholder="0" class="form-control input-xs inline reading" style="width: 100%;" id="reading" value="' . $exrow['J'] . '"/>' .
                                '</div>';


                            $mtrid = '<input type="hidden" name="mtrid[' . $acct_info->sysid. ']" value="' . $exrow['G'] . '" id="mtrid"/>';
                            $acctid_input = '<input type="hidden" name="" value="' . $acct_info->sysid . '" id="acctid"/>';
                            $schedid_input = '<input type="hidden" name="" value="'.$schedid.'" id="schedid"/>';

                            $controls  = '';
                            $controls .= '<a href="javascript:;" title="Over-ride for billing" data-placement="left" id="btn_overide" class="btn btn-primary btn-xs inline tooltips"><i class="fa fa-save "></i></i></a>';
                            if($qry_sched) {
                                if($recheck) {
                                    $controls .= '<a href="javascript:;" title="'.$recheck_msg.'" data-placement="left" id="btn_sched_notify" class="btn btn-danger btn-xs inline tooltips"><i class="fa fa-times "></i></i></a>';
                                }else {
                                    $controls .= row_popover_a($acct_info->sysid, '<i class="fa fa-calendar text-success">', 'Date: <b>' . $qry_sched->datesched . '</b>', 'Schedule', 'left', false, 'hover', true, 'btn-xs btn-success inline');
                                }
                            }else{
                                $controls .= '<a href="javascript:;" title="No Reading Schedule, please create reading schedule for this GDLB. '.$recheck_msg.'" data-placement="left" id="btn_sched_notify" class="btn btn-danger btn-xs inline tooltips"><i class="fa fa-times "></i></i></a>';
                            }


                            $recheck_read_check = '';
                            $add_bill_check = '';
                            if($check->addbill == true) {
                                $add_bill_check = 'checked';
                            }
                            if($recheck==true) {
                                $recheck_read_check = 'checked';
                            }
                            $add_bill = '<input value="'.$acct_info->sysid.'" class="icheck checkbox" '.$add_bill_check.' type="checkbox" name="addbill['.$acct_info->sysid.']" />';
                            $recheck_read = '<input value="'.$acct_info->sysid.'" class="icheck checkbox" '.$recheck_read_check.' type="checkbox" name="chkread['.$acct_info->sysid.']" />';

                            $row_color = '';

                            if($viewtype==1 && $recheck == true) {


                                $data_tbl[] = array(
                                    'expand' => btn_expand($acct_info->sysid),
                                    'servno' => $exrow['D'] . $mtrid . $acctid_input . $schedid_input,
                                    'name' => $name,
                                    'mtr' => $exrow['E'],
                                    'mtrno' => $exrow['G'],
                                    'mtrser' => $exrow['H'],
                                    'mult' => $multcd,
                                    'prsrdg' => $reading,
                                    'prvrdg' => $exrow['I'],
                                    'prskwh' => $prskwh,
                                    'prvkwh' => $prvkwh,
                                    'demand' => $demand,
                                    'netmtr' => $netmtr,
                                    'findings' => $findings,
                                    'incdec' => $incdec,
                                    'addbill' => $add_bill,
                                    'chckread' => $recheck_read,
                                    'printed' => '',
                                    'control' => $controls,
                                    'acctid' => $acct_info->sysid,
                                    'schedid' => $schedid,
                                    'rowcolor' => $row_color,
                                );
                            }

                            if($viewtype==2 && $check->recheck == false) {
                                $data_tbl[] = array(
                                    'expand' => btn_expand($acct_info->sysid),
                                    'servno' => $exrow['D'] . $mtrid . $acctid_input . $schedid_input,
                                    'name' => $name,
                                    'mtr' => $exrow['E'],
                                    'mtrno' => $exrow['G'],
                                    'mtrser' => $exrow['H'],
                                    'mult' => $multcd,
                                    'prsrdg' => $exrow['J'],
                                    'prvrdg' => $exrow['I'],
                                    'prskwh' => $prskwh,
                                    'prvkwh' => $prvkwh,
                                    'demand' => $demand,
                                    'netmtr' => $netmtr,
                                    'findings' => '<code>N/A</code>',
                                    'incdec' => $incdec,
                                    'addbill' => $add_bill,
                                    'chckread' => $recheck_read,
                                    'printed' => '',
                                    'control' => $controls,
                                    'acctid' => $acct_info->sysid,
                                    'schedid' => $schedid,
                                    'rowcolor' => $row_color,
                                );
                            }

                            if($viewtype==3) {
                                $data_tbl[] = array(
                                    'expand' => btn_expand($acct_info->sysid),
                                    'servno' => $exrow['D'] . $mtrid . $acctid_input . $schedid_input,
                                    'name' => $name,
                                    'mtr' => $exrow['E'],
                                    'mtrno' => $exrow['G'],
                                    'mtrser' => $exrow['H'],
                                    'mult' => $multcd,
                                    'prsrdg' => $exrow['J'],
                                    'prvrdg' => $exrow['I'],
                                    'prskwh' => $prskwh,
                                    'prvkwh' => $prvkwh,
                                    'demand' => $demand,
                                    'netmtr' => $netmtr,
                                    'findings' => $findings,
                                    'incdec' => $incdec,
                                    'addbill' => $add_bill,
                                    'chckread' => $recheck_read,
                                    'printed' => '',
                                    'control' => $controls,
                                    'acctid' => $acct_info->sysid,
                                    'schedid' => $schedid,
                                    'rowcolor' => $row_color,
                                );
                            }


                            $data['check'][] = $check;
                        }
                    }
                }
                $i++;
            }
            unlink($file);
        }

        $data['extfile'] = $file_info;
        $data['dataarr'] = $data_arr;

        $data['list'] = $data_tbl;

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_account_map_arr() {
        $data = array();
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $qry = false;
        $get_acct = $this->db->select()
            ->from('customer_accounts_geodata')
            ->where(array('acctid' => $id, 'status' => 1, 'type' => $type))
            ->get()->row();
        $latlngarr = array();
        if($get_acct) {
            $qry = true;
            $latlngarr = array('lat' => floatval($get_acct->lat), 'lng' => floatval($get_acct->lon));
        }
        $data['latlngarr'] = $latlngarr;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function import_reading_to_legacy() {
        $data = array();
        $schedid = $this->input->post('schedid');
        $userid = $this->input->post('userid');
        $qry = false;

        $qry_specific = $this->db->query(
            "SELECT 
                am.servicenumber AS SERVNO,
                am.mtr AS MTR,
                sm.`years` AS BILLYR,
                sm.`months` AS BILLMO,
                ulc.telcode AS TELCODE,
                ml.mtrno AS MTRNO,
                ml.mtrser AS MTRSER,
                ml.prsrdg AS PRVRDG,
                mrl.reading AS PRSRDG,
                ml.prsdte AS PRVDTE,
                sm.datesched AS PRSDTE,
                gdlb.g AS GRP,
                dist.codes AS DIST,
                gdlb.l AS LOT,
                gdlb.b AS BOOK,
                ml.seq AS MRSEQ
            FROM reading_schedule_meters_logs AS ml
            INNER JOIN reading_schedule_main AS sm ON sm.sysid = ml.schedid
            INNER JOIN customer_accounts_main AS am ON am.sysid = ml.acctid
            INNER JOIN reading_schedule_reader AS sr ON sr.schedid = ml.schedid
            INNER JOIN prime_system_users_legacy_code AS ulc ON ulc.userid = sr.userid
            INNER JOIN gdlb_main AS gdlb ON gdlb.sysid = sm.gdlbid
            INNER JOIN address_districts AS dist ON dist.sysid = gdlb.d
            LEFT JOIN customer_accounts_subscription_meter_reading_logs AS mrl ON mrl.acctid = ml.acctid AND mrl.`status` = 1
            WHERE ml.schedid = {$schedid} AND sr.userid = {$userid} AND ml.`status` = 1
            GROUP BY 
                am.servicenumber,
                am.mtr,
                sm.`years`,
                sm.`months`,
                ulc.telcode,
                ml.mtrno,
                ml.mtrser,
                ml.prsrdg,
                mrl.reading,
                ml.prsdte,
                sm.datesched,
                gdlb.g,
                dist.codes,
                gdlb.l,
                gdlb.b,
                ml.seq
            "
        );
        $spec_num_rows = $qry_specific->num_rows();
        $inserted = 0;

        if($spec_num_rows > 0) {
            if (pecoapps_conn()) {
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                foreach ($qry_specific->result() as $row) {
                    if(trim($row->PRSRDG) != '') {
                        $where_arr = array('servno' => $row->SERVNO, 'mtr' => $row->MTR, 'prsrdg' => '.0');
                        $conn->where($where_arr);
                        $ins = $conn->update('reading', array('prsrdg' => $row->PRSRDG, 'edp_rem' => 'X',));
                        $data['err'][] = $this->db->_error_message();
                        if ($ins) {
                            $inserted += 1;
                        }
                    }
                }
            }
        }

        $data['qry'] = $qry;
        $data['inserted'] = $inserted;
        $data['numrows'] = $spec_num_rows;
        return json_encode($data);
    }

    function get_gdlb_main() {
        $data = array();

        return json_encode($data);
    }

    function truncate_test_data() {
        $data = array();
        $qry = false;
        $this->db->trans_begin();
        // ######################################################################
        // TRUNCATE SCHEDULE
        $this->db->query("TRUNCATE TABLE reading_schedule_main");
        $this->db->query("TRUNCATE TABLE reading_schedule_reader");
        $this->db->query("TRUNCATE TABLE reading_schedule_meters_logs");

        // ######################################################################
        // TRUNCATE EXTERNAL BILLING
        $this->db->query("TRUNCATE TABLE billing_reports_ext");

        // ######################################################################
        // TRUNCATE READING
        $this->db->query("TRUNCATE TABLE customer_accounts_subscription_meter_reading");
        $this->db->query("TRUNCATE TABLE customer_accounts_subscription_meter_reading_logs");
        $this->db->query("TRUNCATE TABLE customer_accounts_subscription_meter_reading_temp");

        // ######################################################################
        // TRUNCATE ANALYSIS
        $this->db->query("TRUNCATE TABLE trn_reading_analysis_compute");
        $this->db->query("TRUNCATE TABLE trn_reading_analysis_logs");
        $this->db->query("TRUNCATE TABLE trn_reading_findings");

        if($this->db->trans_status() == true) {
            $qry = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function customer_near_mtr($dataid = false) {
        $qry = false;
        $data = array();
        if (!$dataid) {
            $dataid = $this->input->post('dataid');
        }
        $nearmeter = $this->db->select('a.sysid, a.mtrno, a.ownerid, a.servicenumber')
            ->from('customer_accounts_main AS a')
            ->join('application_customers_near_meters AS anm', 'anm.acctid = a.sysid')
            ->where(array('anm.appid' => $dataid, 'anm.status' => 1))
            ->get();

        if ($nearmeter->num_rows() > 0) {
            $num = 1;
            foreach ($nearmeter->result() AS $nm) {
                /// $person = get_person_info($nm->ownerid)->info;
                // $name = $person->lastname.', '.$person->firstname.' '.$midname[0];
                $info = $this->db->select('addrspecific')
                    ->from('customer_accounts_address')
                    ->where(array('acctid' => $nm->sysid, 'status' => 1))
                    ->get()->row();
                if ($info) {
                    //$midname = $person->middlename;
                    $data['info'][] = array(
                        'num' => $num,
                        'srvno' => $nm->servicenumber,
                        'mtrno' => $nm->mtrno,
                        'address' => $info->addrspecific,
                        'name' => ''
                    );
                    $qry = true;
                    $num++;
                }
            }
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }



}