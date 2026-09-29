<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mrd extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_mrd');
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_peco');
        $this->load->model('model_settings');
        $this->load->helper('peco_helper', TRUE);
        $this->load->helper('operations_helper', TRUE);
        // $this->load->library('datatables');
        // $this->load->helper('bos_helper', TRUE);


        require_once APPPATH . 'third_party/PHPExcel.php';
        $this->excel = new PHPExcel();
        $this->config->set_item('language', 'english');
    }

    public function listfindings()
    {
        $q = $this->input->post('term');
        $qry = $this->db->select()->from('meter_reading_findings')->like('codes', $q)->get();
        $res = array();
        foreach ($qry->result() as $row) {
            $res[] = array(
                'id' => $row->sysid,
                'text' => highlightkeyword($row->codes . ' - ' . $row->descriptions, $q),
            );
        }
        echo json_encode($res);
    }

    function samplereading()
    {
        echo $this->model_mrd->get_sample_reading();
    }

    function getfindingssub()
    {
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
        $data['qry'] = $q;
        echo json_encode($data);
    }

    function getgdlb()
    {
        $distid = $this->input->post('term');
        if (!empty($distid)) {
            $distid = explode(',', $distid);
            $this->db->where_in('GDLB.d', $distid);
        }
        $qry_gdlb = $this->db->select('GDLB.sysid AS GDLBID, GDLB.limit AS LMT, COUNT(AGDLB.gdlbid) AS ACCTNO')
            ->select("CONCAT(GDLB.g, '-', DIST.codes, '-', GDLB.l, '-', GDLB.b) AS GDLBNAME", false)
            ->from('gdlb_main AS GDLB')
            ->join('customer_accounts_glb AS AGDLB', 'AGDLB.gdlbid = GDLB.sysid')
            ->join('address_districts AS DIST', 'DIST.sysid = GDLB.d')
            ->group_by('GDLB.sysid, AGDLB.gdlbid, GDLB.limit')
            ->get();
        $res = array();
        $num_rows = $qry_gdlb->num_rows();
        if ($num_rows > 0) {
            foreach ($qry_gdlb->result() as $row) {
                // ADD QUERY TO FILTER READING IS ALREADY SUBMITED
                $res['list'][] = array(
                    'id' => $row->GDLBID,
                    'text' => $row->GDLBNAME,
                    'cnt' => $row->ACCTNO
                );
            }
        }
        $res['input'] = $this->input->post();
        echo json_encode($res);
    }

    function test()
    {
        //echo fn_meter_findings(1);
        echo '<h2>Testing Only</h2>';
        echo user_id();
    }

    function getmrdacctlist()
    {
        $data = array();
        $schedid = $this->input->post('schedid');
        $userid = $this->input->post('userid');
        $num_rows = 0;
        $schedule = '';
        $reader = '';

        $user_info = get_users_info($userid);

        $user_telcode = $this->db->select('telcode')
            ->from('prime_system_users_legacy_code')
            ->where(array('userid' => $userid))
            ->get()->row();
        $user_code = ($user_telcode) ? $user_telcode->telcode . ' - ' : '';
        $user_telcode = ($user_telcode) ? $user_telcode->telcode : false;

        $reader = ($user_info) ? $user_code . $user_info->lastname : '';


        $get_sched = $this->db->select('gdlbid, months, years, datesched')
            ->from('reading_schedule_main')
            ->where(array('sysid' => $schedid))
            ->get()->row();
        if($get_sched) {
            $gdlbid = $get_sched->gdlbid;
            $schedule = $get_sched->datesched;

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
                    addr.addrspecific AS ADDRSPECIFIC,
                    ulc.telcode AS TELCODE
                    FROM reading_schedule_main AS sm 
                    JOIN customer_accounts_main AS am ON sm.gdlbid = am.gdlb AND sm.status >= 1
                    JOIN customer_accounts_address AS addr ON addr.acctid = am.sysid AND addr.status = 1
                    JOIN reading_schedule_specific AS ss ON ss.acctid = am.sysid AND ss.status = 1
                    LEFT JOIN prime_system_users_legacy_code AS ulc ON ulc.userid = ss.userid
                    LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = am.sysid AND mr.status = 1
                    WHERE sm.sysid = $schedid AND ss.userid = $userid AND sm.status = 1
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
                    addr.addrspecific,
                    ulc.telcode
                    "
            );
            $num_rows = $qry_specific->num_rows();
            if($num_rows > 0) {
                foreach ($qry_specific->result() as $row) {
                    $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;
                    $mrseq = ($row->MRSEQ != '') ? $row->MRSEQ : '';

                    $data['data'][] = array(
                        'seq' => $mrseq,
                        'serviceno' => $row->SERVNO,
                        'name' => $name,
                        'mtr' => $row->MTR,
                        'meterno' => $row->MTRNO,
                        'meterserial' => $row->MTRSER,
                        'address' => $row->ADDRSPECIFIC,
                        'ownertype' => '',
                        'ownerid' => '',
                        'control' => ''
                    );
                }
            }else {

                $query = $this->db->select('
                    mr.mrseq AS MRSEQ,
                    acct.sysid AS SYSID,
                    acct.gdlb AS GDLB, 
                    acct.servicenumber AS SERVNO, 
                    acct.types AS OWNERTYPE, 
                    acct.ownerid AS OWNERID, 
                    acct.mtrno AS MTRNO,
                    acct.mtrserial AS MTRSER,
                    acct.mtr AS MTR,
                    addr.addrspecific AS ADDRSPECIFIC
                ')
                    ->from('customer_accounts_main AS acct')
                    ->join('customer_accounts_address AS addr', 'addr.acctid = acct.sysid AND addr.status = 1', 'left')
                    ->join('customer_accounts_mtrseq AS mr', 'mr.acctid = acct.sysid AND mr.status = 1', 'left')
                    ->where('acct.gdlb', $gdlbid)
                    ->where('acct.status', 1)
                    ->order_by('mr.mrseq, acct.servicenumber')
                    ->get();
                $num_rows = $query->num_rows();
                if ($num_rows > 0) {
                    $i = 0;
                    foreach ($query->result() as $row) {
                        $i++;
                        $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;
                        $mrseq = ($row->MRSEQ != '') ? $row->MRSEQ : '';

                        $data['data'][] = array(
                            'seq' => $mrseq,
                            'serviceno' => $row->SERVNO,
                            'name' => $name,
                            'mtr' => $row->MTR,
                            'meterno' => $row->MTRNO,
                            'meterserial' => $row->MTRSER,
                            'address' => $row->ADDRSPECIFIC,
                            'ownertype' => '',
                            'ownerid' => '',
                            'control' => ''
                        );
                    }
                }
            }
        }
        $gdlb_darr = array();
        $gdlb = '';
        $qry_gdlb_details = $this->db->select()
            ->select("CONCAT(G.g, '/', D.codes, '/', G.l, '/', G.b) AS GDLB", false)
            ->from('gdlb_main AS G')
            ->join('address_districts AS D', 'D.sysid = G.d', 'left')
            ->where_in('G.sysid', $gdlbid)
            ->get();
        if ($qry_gdlb_details->num_rows() > 0) {
            foreach ($qry_gdlb_details->result() as $row) {
                $gdlb_darr[] = $row->GDLB;
            }
            $gdlb = implode(', ', $gdlb_darr);
        }

        $reptitle = 'Reading Sheet';
        $header = peco_print_header($userid, $reptitle, $user_telcode, false);

        $data['header']     = $header;
        $data['dates']      = 'Reading Schedule: ' . $schedule;
        $data['gdlb']       = $gdlb;
        $data['reader']     = $reader;
        $data['num']        = $num_rows;
        $data['input']      = $this->input->post();
        echo json_encode($data);
    }

    function getgdlbcustomersspecific() {

        $data = array();
        $schedid = $this->input->post('schedid');
        $userid = $this->input->post('userid');

        $get_sched_details = $this->db->select('sm.sysid, sm.gdlbid, sm.months, sm.years')
            ->from('reading_schedule_main AS sm')
            ->join('reading_schedule_reader AS sr', 'sm.sysid = sr.schedid')
            ->where(array('sm.sysid' => $schedid, 'sr.userid' => $userid))
            ->get()->row();
        if ($get_sched_details) {
            $sched_gdlb = $get_sched_details->gdlbid;
            $sched_sysid = $get_sched_details->sysid;
            $data['gdlbid'] = $sched_gdlb;

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
                        ulc.telcode AS TELCODE,
                        aa.addrspecific AS ADDRSPEC
                        FROM reading_schedule_main AS sm 
                        JOIN customer_accounts_main AS am ON sm.gdlbid = am.gdlb AND sm.status >= 1
                        JOIN reading_schedule_specific AS ss ON ss.acctid = am.sysid AND ss.status = 1
                        LEFT JOIN prime_system_users_legacy_code AS ulc ON ulc.userid = ss.userid
                        LEFT JOIN customer_accounts_mtrseq AS mr ON mr.acctid = am.sysid AND mr.status = 1
                        LEFT JOIN customer_accounts_address AS aa ON aa.acctid = am.sysid AND aa.status = 1
                        WHERE sm.sysid = $sched_sysid AND ss.userid = $userid
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
                        ulc.telcode,
                        aa.addrspecific
                    "
            );
            $num_rows = $qry_specific->num_rows();
            if($num_rows > 0) {
                $data['sched'] = 'SPECIFIC';
                $i = 0;
                foreach ($qry_specific->result() as $row) {

                    $i++;
                    $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;
                    $mrseq = ($row->MRSEQ != '') ? $row->MRSEQ : '';

                    $assign_select2 = '';
                    $assign_select2 .= '<input class="form-control inline input-small" placeholder="Tag Reader.." id="input_tagread" />';

                    $controls = '';
                    $controls .= '<a href="save_read_tagging" class="btn btn-default btn-xs inline">Save</a>';

                    $data['data'][] = array(
                        'seq' => $mrseq,
                        'serviceno' => $row->SERVNO,
                        'name' => $name,
                        'mtr' => $row->MTR,
                        'meterno' => $row->MTRNO,
                        'meterserial' => $row->MTRSER,
                        'address' => $row->ADDRSPEC,
                        'ownertype' => '',
                        'ownerid' => '',
                        'tagging' => $assign_select2,
                        'control' => $controls
                    );
                }
            }
        }

        echo json_encode($data);
    }



    function getgdlbcustomers()
    {
        $data = array();
        $schedid = $this->input->post('schedid');
        $userid = $this->input->post('userid');

        $reader_arr = array();
        $gdlb_darr = array();
        $gdlb = '';
        $scheddate = '';

        if($userid) {
            $get_sched_details = $this->db->select('sm.sysid, sm.gdlbid, sm.months, sm.years')
                ->from('reading_schedule_main AS sm')
                ->join('reading_schedule_reader AS sr', 'sm.sysid = sr.schedid')
                ->where(array('sm.sysid' => $schedid, 'sr.userid' => $userid))
                ->get()->row();
            if ($get_sched_details) {
                $sched_gdlb = $get_sched_details->gdlbid;
                $data['gdlbid'] = $sched_gdlb;

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
                    WHERE acct.gdlb = $sched_gdlb AND acct.`status` = 1 
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
                        $name_arr = get_ownership_details($row->OWNERTYPE, $row->OWNERID);
                        $name = ($name_arr) ? $name_arr->name : '';
                        $mrseq = ($row->MRSEQ != '') ? $row->MRSEQ : '';
                        // GE SPECIFIC
                        $tagging = '';
                        $qry_specific = $this->db->select('ss.userid, ss.acctid, ulc.telcode')
                            ->from('reading_schedule_specific AS ss')
                            ->join('prime_system_users_legacy_code AS ulc', 'ulc.userid = ss.userid', 'left')
                            ->where(array('ss.acctid' => $row->SYSID, 'ss.status' => 1))
                            ->get()->row();
                        if ($qry_specific) {
                            $get_userinfo = get_users_info($qry_specific->userid);
                            $username = ($get_userinfo) ? $get_userinfo->username : '';
                            $tagging .= '<span class="label label-danger">' . $qry_specific->telcode . '</span> ' . $username;
                        }

                        $controls = '';
                        $controls .= '<a href="save_read_tagging" class="btn btn-default btn-xs inline">Save</a>';
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
                            'tagging' => $tagging,
                            'control' => $controls
                        );
                    }
                }

            }
        }else{
            $get_sched_details = $this->db->select('sm.sysid, sm.gdlbid, sm.months, sm.years')
                ->from('reading_schedule_main AS sm')
                ->join('reading_schedule_reader AS sr', 'sm.sysid = sr.schedid')
                ->where(array('sm.sysid' => $schedid))
                ->get()->row();
            if($get_sched_details) {
                $query = $this->db->select('
                    mr.mrseq AS MRSEQ,
                    acct.sysid AS SYSID,
                    acct.gdlb AS GDLB, 
                    acct.servicenumber AS SERVNO, 
                    acct.types AS OWNERTYPE, 
                    acct.ownerid AS OWNERID, 
                    acct.mtrno AS MTRNO,
                    acct.mtrserial AS MTRSER,
                    acct.mtr AS MTR,
                    addr.addrspecific AS ADDRSPECIFIC
                ')
                    ->from('customer_accounts_main AS acct')
                    ->join('customer_accounts_address AS addr', 'addr.acctid = acct.sysid AND addr.status = 1', 'left')
                    ->join('customer_accounts_mtrseq AS mr', 'mr.acctid = acct.sysid AND mr.status = 1', 'left')
                    ->where('acct.gdlb', $get_sched_details->sysid)
                    ->where('acct.status', 1)
                    ->order_by('mr.mrseq, acct.servicenumber')
                    ->get();
                $num_rows = $query->num_rows();
                if ($num_rows > 0) {
                    $i = 0;
                    foreach ($query->result() as $row) {
                        $i++;
                        $name = get_ownership_details($row->OWNERTYPE, $row->OWNERID)->name;
                        $mrseq = ($row->MRSEQ != '') ? $row->MRSEQ : '';

                        $data['data'][] = array(
                            'seq' => $mrseq,
                            'serviceno' => $row->SERVNO,
                            'name' => $name,
                            'mtr' => $row->MTR,
                            'meterno' => $row->MTRNO,
                            'meterserial' => $row->MTRSER,
                            'address' => $row->ADDRSPECIFIC,
                            'ownertype' => '',
                            'ownerid' => '',
                            'control' => ''
                        );
                    }
                }
            }
        }

        echo json_encode($data);
        /*

            $data['readers'] = $reader_arr;


            $query = $this->db->select('
                    mr.mrseq,
                    acct.sysid,
                    acct.gdlb, 
                    acct.servicenumber AS servno, 
                    acct.types AS ownertype, 
                    acct.ownerid, 
                    acct.mtrno,
                    acct.mtrserial,
                    acct.mtr,
                    addr.addrspecific
                ')
                ->from('customer_accounts_main AS acct')
                ->join('customer_accounts_address AS addr', 'addr.acctid = acct.sysid AND addr.status = 1', 'left')
                ->join('customer_accounts_mtrseq AS mr', 'mr.acctid = acct.sysid AND mr.status = 1', 'left')
                ->where(array('acct.gdlb' => $gdlbid, 'acct.status' => 1))
                ->order_by('mr.mrseq, acct.servicenumber')
                ->get();
            $num_rows = $query->num_rows();
            if ($num_rows > 0) {
                $i = 0;
                foreach ($query->result() as $row) {

                    $i++;
                    $name = get_ownership_details($row->ownertype, $row->ownerid)->name;
                    $mrseq = ($row->mrseq != '') ? $row->mrseq : '';
                    // GE SPECIFIC
                    $qry_specific = $this->db->select('userid, acctid')->from('reading_schedule_specific')
                        ->where(array('acctid' => $row->sysid, 'status' => 1))
                        ->get()->row();
                    $spec_color = ($qry_specific) ? 'text-success' : 'text-default';

                    $assign_select2 = '';
                    $assign_select2 .= '<input class="form-control inline input-small" placeholder="Tag Reader.." id="input_tagread" />';

                    $controls = '';
                    $assigned_specific = false;
                    if($qry_specific) {
                        if (in_array($qry_specific->userid, $reader_arr)) {
                            $readers_specs = implode(', ', $reader_arr);
                            $controls .= $readers_specs;
                            $assigned_specific = true;
                        } else {
                            $readers_specs = 'Specified but not array: ' . $qry_specific->userid;
                            $controls .= $readers_specs;
                        }
                    }else{
                        $controls .= '<a href="save_read_tagging" class="btn btn-default btn-xs inline">Save</a>';
                    }

                    if($assigned_specific) {
                        $data['data'][] = array(
                            'seq' => $mrseq,
                            'serviceno' => $row->servno,
                            'name' => $name,
                            'mtr' => $row->mtr,
                            'meterno' => $row->mtrno,
                            'meterserial' => $row->mtrserial,
                            'address' => $row->addrspecific,
                            'ownertype' => '',
                            'ownerid' => '',
                            'tagging' => $assign_select2,
                            'control' => $controls
                        );
                    }
                }
            }
            $qry_gdlb_details = $this->db->select()
                ->select("CONCAT(G.g, '/', D.codes, '/', G.l, '/', G.b) AS GDLB", false)
                ->from('gdlb_main AS G')
                ->join('address_districts AS D', 'D.sysid = G.d', 'left')
                ->where('G.sysid', $gdlbid)
                ->get();
            if ($qry_gdlb_details->num_rows() > 0) {
                foreach ($qry_gdlb_details->result() as $row) {
                    $gdlb_darr[] = $row->GDLB;
                }
                $gdlb = implode(', ', $gdlb_darr);
            }
        }

        $data['printdate']  = $scheddate; // @TODO SCHEDULE DATE HERE
        $data['gdlb']       = $gdlb;
        $data['num']        = $num_rows;
        $data['input']      = $this->input->post();
        echo json_encode($data);
        */
    }


    function getacctreaders() {
        $data = array();
        $q = false;
        $acctid = $this->input->post('acctid');
        $qry = $this->db->select('r.userid, u.username')
            ->from('reading_schedule_specific AS r')
            ->join('prime_system_users AS u', 'u.sysid = r.userid')
            ->where('r.acctid', $acctid)
            ->get();
        if($qry->num_rows()>0) {
            $q = true;
            foreach($qry->result() as $row) {
                $data['users'][] = array('id' => $row->userid, 'text' => $row->username);
            }
        }
        $data['qry'] = $q;
        echo json_encode($data);
    }

    function assignacctmeterreader() {
        $data = array();
        $q = false;
        $acctid = $this->input->post('acctid');
        $users = $this->input->post('users');
        $users_arr = explode(',', $users);
        $this->db->trans_begin();
        if(count($users_arr)>0) {
            $q = true;
            $this->db->where('acctid', $acctid);
            $this->db->update('reading_schedule_specific', array('status' => 0, 'updatedby' => user_id()));
            foreach($users_arr as $row) {
                $ins_arr = array(
                    'userid' => $row,
                    'acctid' => $acctid,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert('reading_schedule_specific', $ins_arr);
            }
        }
        $title = 'Assign Readers';
        if($q == true && $this->db->trans_status()===true) {
            $this->db->trans_commit();
            $msg = 'Readers assigned';
            $q = true;
            $func = 'success';
        }else{
            $this->db->trans_rollback();
            $msg = 'Readers not assigned!';
            $q = false;
            $func = 'error';
        }
        $data['func'] = $func;
        $data['title'] = $title;
        $data['msg'] = $msg;
        $data['qry'] = $q;
        echo json_encode($data);
    }


    function _remove_empty_internal($value) {
        return !empty($value) || $value === 0;
    }

    function mrdlb() {
        echo $this->model_mrd->get_gdlb_main();
    }
    function insplb() {
        echo $this->model_query->insp_query();
    }


    function getgdlblist() {
        echo $this->model_mrd->get_gdlb_list();
    }
    function assignreadingschedule() {
        echo $this->model_mrd->assign_schedule();
    }
    function assignreadingscheduletest() {
        echo $this->model_mrd->assign_schedule();
    }
    function printreadingsched() {
        echo $this->model_mrd->print_reading_sched();
    }
    function getgdlbdist() {
        echo $this->model_mrd->get_gdlb_dist();
    }
    function getgdlbsched() {
        echo $this->model_mrd->get_mrd_schedule();
    }
    function getctgroup() {
        echo $this->model_mrd->get_ct_group();
    }
    function getgdlbrecheck() {
        echo $this->model_mrd->get_mrd_recheck();
    }
    function getreadergdlbsched() {
        echo $this->model_mrd->get_reading_gdlb_sched();
    }
    function getnextreadergdlbsched() {
        echo $this->model_mrd->get_reading_gdlb_sched_next();
    }
    function processforbilling() {
        echo $this->model_mrd->get_reading_analysis();
    }
    function readinganalysis()  {
        echo $this->model_mrd->get_reading_analysis();
    }
    function getforaddbill()  {
        echo $this->model_mrd->get_for_addbill_list();
    }
    function readingentry() {
        echo $this->model_mrd->get_reading_entry();
    }
    function metertagging() {
        echo $this->model_mrd->get_gdlb_tagging();
    }
    function queryreadercodeinfo() {
        echo $this->model_mrd->query_reader_codeinfo();
    }
    function savemtrtagging() {
        echo $this->model_mrd->save_mtr_tagging();
    }
    function clearmtrtagging() {
        echo $this->model_mrd->clear_mtr_tagging_row();
    }
    function getmtrinfo() {
        echo $this->model_mrd->get_mtr_info();
    }
    function readinghistory() {
        echo $this->model_mrd->get_reading_history();
    }
    function deletetempread() {
        echo $this->model_mrd->delete_reading_temp();
    }
    function getselect2findings() {
        echo $this->model_mrd->get_select2_findings();
    }
    function updateanalysisrow() {
        echo $this->model_mrd->update_analysis_row();
    }
    function uploadmtrpic() {
        echo $this->model_mrd->upload_mtr_pic();
    }
    function getmtrpics() {
        echo $this->model_mrd->get_mtr_pics();
    }
    function fixscheddatarow() {
        echo $this->model_mrd->fix_sched_data_row();
    }

    function getfindingslist() {
        echo $this->model_mrd->get_finding_datatable();
    }

    function deletefindingsmain() {
        echo $this->model_mrd->delete_findings_main();
    }

    function addmainreadingfindings() {
        echo $this->model_mrd->add_findings_maintenance();
    }
    function updfindingsrecheck() {
        echo $this->model_mrd->update_findings_isrecheck();
    }

    function deletemtrpic() {
        echo $this->model_mrd->delete_mtr_pic();
    }

    function manualupdatereadingmtr() {
        $query = $this->db->select('sysid, servicenumber, mtr, mtrno')
            ->from('customer_accounts_main')
            ->where(array('gdlb' => 33))
            ->get();
        if($query->num_rows()>0) {
            foreach($query->result() as $row) {
                $upd_arr = array(
                    'mtrid' => $row->mtrno,
                );
                $this->db->where(array('acctid' => $row->sysid));
                $this->db->update('customer_accounts_subscription_meter_reading_logs', $upd_arr);

                $this->db->where(array('acctid' => $row->sysid));
                $this->db->update('customer_accounts_subscription_meter_reading_temp', $upd_arr);
            }
        }
    }

    /*
    function readingentry_backup() {
        $userid = user_session()->system_user_sessid;
        $edit = $this->input->post('edit');
        $query = $this->db->select('cag.gdlbid, rsr.schedid, am.serialcodes, am.sysid AS ASSETSYSID, cam.servicenumber, cao.ownertype, cao.ownerid, cao.accountid')
            ->from('reading_schedule_reader as rsr')
            ->join('reading_schedule_gdlb as rsg', 'rsr.schedid = rsg.schedid', 'left')
            ->join('reading_schedule_main AS rsm', 'rsr.schedid = rsm.sysid', 'left')
            ->join('customer_accounts_glb as cag', 'cag.gdlbid = rsg.gdlbid', 'left')
            ->join('customer_accounts_owners as cao', 'cao.accountid=cag.accountid', 'left')
            ->join('customer_accounts_main as cam', 'cam.sysid=cao.accountid', 'left')
            ->join('assets_main_owner_history as amoh', 'amoh.ownerid = cao.sysid', 'left')
            ->join('assets_main AS am', 'am.sysid = amoh.assetid', 'left')
            ->where(array('rsr.userid' => user_session()->system_user_sessid, 'cam.servicenumber !=' => '', 'amoh.status' => 1, 'rsm.status' => 1))
            ->group_by('cam.sysid')
            ->order_by('rsr.schedid', 'desc')
            ->get();




        $data = array();
        $i = 0;
        $findings_options = '';
        $qry_findings = $this->db->select()->from('meter_reading_findings')->get();
        if ($qry_findings->num_rows() > 0) {
            foreach ($qry_findings->result() as $frow) {
                $findings_options .= '<option value="' . $frow->sysid . '">' . $frow->codes . ' - ' . $frow->descriptions . '</option>';
            }
        }
        foreach ($query->result() as $row) {
            // GET METER DETAILS
            $get_account_main = $this->db->select()->from('customer_accounts_main')->where('sysid', $row->accountid)->get()->row();
            $get_meter_details = $this->db->select()->from('customer_accounts_subscription_meter AS asm')
                ->where(array('asm.assetid' => $row->ASSETSYSID, 'asm.glbid' => $row->gdlbid))
                ->get()->row();
            $get_subscriptions = $this->db->select()->from('customer_accounts_subscription')->where('accountid', $row->accountid)->get()->row();
            if ($get_meter_details) {
                $mtrsysid = $get_meter_details->sysid;
                $mtrno = $get_meter_details->mtrno;
                $mtr = $get_meter_details->mtr;
            } else {
                $mtrsysid = '';
                $mtrno = '';
                $mtr = 0;
            }

            if ($get_subscriptions) {
                if ($get_subscriptions->rateid > 1) {
                    $demstat = true;
                    $demicon = 'fa-pencil';
                    $demclass = "has-success";
                } else {
                    $demstat = false;
                    $demicon = 'fa-times';
                    $demclass = "has-error";
                }
            } else {
                $demstat = false;
                $demicon = 'fa-time';
                $demclass = "has-error";
            }

            // DEMAND ENTRY
            $demand = '<div class="form-group ' . $demclass . '" style="padding-left: 15px"><div class="input-icon left">' .
                '<i class="fa ' . $demicon . ' tooltips" data-original-title="Enter Reading Amount"></i>' .
                '<input disabled name="demand[]" placeholder="0" class="form-control input-xs inline disabled" style="width: 100%;" id="demand" value=""/>' .
                '</div></div>';

            // GET READING INITIAL
            $qry_reading_init = $this->db->select('h.readings')->from('trn_reading_history AS h')
                ->where(array('h.type' => 4, 'h.status' => 1, 'h.mtrid' => $mtrsysid))
                ->order_by('h.datecreated', 'desc')
                ->get()->row();
            if ($qry_reading_init) {
                $initreadstat = true;
                $initread = $qry_reading_init->readings;
            } else {
                $initreadstat = false;
                $initread = '<div class="input-icon left">' .
                    '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                    '<input name="prevread[]" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="prevread" value=""/>' .
                    '</div>';
            }

            // GET PREVIOUS READING
            $qry_reading_prev = $this->db->select('h.readings')->from('trn_reading_history AS h')
                ->where(array('h.type' => 1, 'h.status' => 1, 'h.mtrid' => $mtrsysid, 'h.schedid != ' => $row->schedid))
                ->order_by('h.datecreated', 'desc')
                ->get()->row();
            if ($qry_reading_prev) {
                if ($edit) {
                    $prevreadstat = true;
                    $prevread = $qry_reading_prev->readings;
                    $prevreadtxt = '<div class="input-icon left">' .
                        '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                        '<input name="reading[]" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="prevread" value="' . $prevread . '"/>' .
                        '</div>';
                } else {
                    $prevreadstat = true;
                    $prevread = $qry_reading_prev->readings;
                    $prevreadtxt = $qry_reading_prev->readings;
                }
            } else {
                $prevreadstat = false;
                if ($initreadstat) {
                    $prevread = $initread;
                    $prevreadtxt = $qry_reading_prev->readings;
                } else {
                    $prevread = $initread;
                    $prevreadtxt = '<div class="input-icon left">' .
                        '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                        '<input autocomplete="off" name="prevread[]" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="prevread" value=""/>' .
                        '</div>';
                }
            }


            // CHECK READING HISTORY
            $qry_reading_hist = $this->db->select('h.sysid, h.readings, fm.sysid AS findingid, fm.codes AS findings, fms.codes AS findingsub')
                ->from('trn_reading_history AS h')
                ->join('meter_reading_findings AS fm', 'fm.sysid = h.findings', 'left')
                ->join('meter_reading_findings_sub AS fms', 'fms.sysid = h.findingsub', 'left')
                ->where(array('h.mtrid' => $mtrsysid, 'h.status' => 1, 'h.schedid' => $row->schedid))->get()->row();
            if ($qry_reading_hist) {
                $attr = true;
                if ($edit) {
                    $curread = $qry_reading_hist->readings;
                    $curreadtxt = '<div class="input-icon left">' .
                        '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                        '<input autocomplete="off" name="reading[]" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="reading" value="' . $curread . '"/>' .
                        '</div>';
                    $findings = '<select name="findings[]" class="form-control inline" id="findings" data-placeholder="Select.." style="width: 100%">' .
                        '<option></option>' . $findings_options . '</select>';
                    $findingsub = '<input placeholder="Remarks.." readonly name="findingsub[]" class="form-control inline" id="findingsub" style="width: 98%" />'
                        . '<input type="hidden" name="mtrid[]" value="' . $mtrsysid . '" />';
                } else {
                    $curread = $qry_reading_hist->readings;
                    $curreadtxt = $qry_reading_hist->readings;
                    $findings = $qry_reading_hist->findings;
                    $findingsub = $qry_reading_hist->findingsub;
                }
            } else {
                $attr = false;
                $curread = 0;
                $curreadtxt = '<div class="input-icon left">' .
                    '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                    '<input name="reading[]" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="reading" value=""/>' .
                    '</div>';
                $findings = '<select name="findings[]" class="form-control inline" id="findings" data-placeholder="Select.." style="width: 100%">' .
                    '<option></option>' . $findings_options . '</select>';
                $findingsub = '<input placeholder="Remarks.." readonly name="findingsub[]" class="form-control inline" id="findingsub" style="width: 98%" />'
                    . '<input type="hidden" name="mtrid[]" value="' . $mtrsysid . '" />';
            }
            $stat_1 = '';
            if ($prevreadstat == true) {
                if ($prevread < $curread) {
                    $stat_1 = '';
                    $rowbg = '';
                    $curcon = ($curread - $prevread);
                    if ($initreadstat) {
                        $prevcon = ($prevread - $initread);
                    } else {
                        $prevcon = 0;
                    }
                } else {
                    $rowbg = '';
                    $stat_1 = '';
                    $curcon = 0;
                    $prevcon = 0;
                }
            } else {
                if ($initreadstat) {
                    $rowbg = '';
                    $stat_1 = 'New Reading';
                    $curcon = ($curread - $initread);
                    $prevcon = 0;
                } else {
                    $rowbg = 'danger';
                    $stat_1 = 'No Init Reading';
                    $prevcon = 0;
                    $curcon = 0;
                }
            }

            // CHECK IF CONSUMPTION IS DECREASE OR INCREASE
            if ($prevcon) {
                $con_diff = ($curcon - $prevcon);
                $per_con = ($con_diff / $prevcon) * 100;
                if ($curcon > $prevcon) {
                    $per_con_class = 'text-success';
                    $per_con_icon = '<i class="fa fa-angle-double-up"></i> ';
                } else {
                    $per_con_class = 'text-danger';
                    $per_con_icon = '<i class="fa fa-angle-double-down"></i> ';
                }

                $abs_per_con = abs($per_con);
                if ($abs_per_con > 60) {
                    $stat_icon = '<i class="fa fa-warning text-warning"></i> ';
                } else {
                    $stat_icon = '<i class="fa fa-check text-success"></i> ';
                }
            } else {
                $per_con = 0;
                $per_con_class = '';
                $per_con_icon = '';
                $stat_icon = '';
            }

            $readingsysid = ($qry_reading_hist) ? $qry_reading_hist->sysid : 0;

            $i++;
            $name = get_ownership_details($row->ownertype, $row->ownerid)->name;
            $data['data'][] = array(
                "expand" => '<i data-toggle="collapse" data-target="#expand_' . $mtrsysid . '" data-id="' . $mtrsysid . '" id="btn-expand" class="fa fa-plus-square-o"></i>',
                "seq" => $i . '<input name="schedid[]" type="hidden" value="' . $row->schedid . '" />'
                    . '<input name="readid[]" type="hidden" value="' . $readingsysid . '" />',
                "serviceno" => ($get_account_main) ? $get_account_main->servicenumber : '',
                "name" => "<strong>" . $name . "</strong>",
                "meter" => $mtr,
                "meterno" => $mtrno,
                "serial" => $row->serialcodes,
                "mult" => 1,
                "demand" => $demand,
                "prevread" => $prevreadtxt,
                "curread" => $curreadtxt,
                "findings" => $findings,
                "findingsub" => $findingsub,
                "prevcon" => $prevcon,
                "currcon" => $curcon,
                "percent" => '<span class="' . $per_con_class . '">' . $per_con_icon . number_format($per_con, 2) . '% </span>',
                "status" => $stat_icon . $stat_1,
                "controls" => $stat_1 . $stat_icon,
                "rowbg" => $rowbg,
                "demstat" => $demstat,
            );
        }
        echo json_encode($data);
    }
    */
    function computereading()  {
        echo json_encode($this->model_mrd->get_compute_reading());
    }
    function manualmrdlotbook() {
        echo $this->model_mrd->submit_manual_lot_book();
    }
    function uploadreadingpic() {
        echo $this->model_mrd->upload_reading_pic();
    }
    function submitactualreadingrow() {
        echo $this->model_mrd->submit_actual_reading_row();
    }
    function testwrap() {
        echo mtr_wrap_kwh(80, 1751, 1950);
    }
    function submitreadingrow() {
        echo $this->model_mrd->submit_reading_row();
    }
    function checkreatespec() {
        $rates = get_spec_rates(2016, 8, 1, 5, 100)->RATES;
        var_dump($rates);
    }
    function editreadingrow() {
        echo $this->model_mrd->edit_reading_row();
    }
    function submitreading() {
        // ini_set('max_input_vars', 5000);
        echo $this->model_mrd->submit_reading();
    }
    function submitreadingrecheck() {
        echo $this->model_mrd->submit_reading_recheck();
    }
    function importreadingtolegacy() {
        echo $this->model_mrd->import_reading_to_legacy();
    }
    function updaterowfindings() {
        echo $this->model_mrd->update_findings_row();
    }
    function validatedate() {
        $d1 = new DateTime("2009-09-01");
        $d2 = new DateTime("2010-05-01");

        var_dump($d1->diff($d2)->d);
    }
    function readingrecheck() {
        echo $this->model_mrd->get_reading_recheck();
    }
    function computeaddbill() {
        echo $this->model_mrd->compute_add_bill();
    }
    function sendrecheck() {
        echo $this->model_mrd->send_recheck();
    }

    function processanalysis() {
        echo $this->model_mrd->process_analysis();
    }

    function printanalysis($schedid, $gdlbid) {
        //echo $this->model_mrd->print_analysis_reports($schedid, $gdlbid);

        $html = '';
        if ($schedid && $gdlbid) {

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

            $html .= '<script type="text/php">
                        if ( isset($pdf) ) {
                            $font = Font_Metrics::get_font("helvetica", "bold");
                            $pdf->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
                        }
                    </script> ';

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

    function trundatetestdata() {
        echo $this->model_mrd->truncate_test_data();
    }

    function getacctmaparr() {
        echo $this->model_mrd->get_account_map_arr();
    }

    function uploadextfile() {
        echo $this->model_mrd->upload_external_file();
    }


    function computeint() {
        $curr = 3;
        $amt = 250;
        $due = false;
        $bills = array(
            array('month' => 1, 'amt' => 0),
            array('month' => 2, 'amt' => 0),
            array('month' => 3, 'amt' => 0),
            array('month' => 4, 'amt' => 0),
            array('month' => 5, 'amt' => 500),
            array('month' => 6, 'amt' => 300),
            array('month' => 7, 'amt' => 200),
            array('month' => 8, 'amt' => 500),
            array('month' => 9, 'amt' => 0),
            array('month' => 10, 'amt' => 0),
            array('month' => 11, 'amt' => 0),
            array('month' => 12, 'amt' => 0)
        );

        echo $curr . ' | '.$amt. '<br>';

        $iter = new ArrayIterator($bills);
        $i = 1;
        $ii = 0;
        $int_cnt = 0;
        $int_amt_total = 0;
        $arr_month_withint = array();


        foreach($bills as $keys => $amt_num_row) {
            if($amt_num_row['amt']>0) {
                $amt_due[] = array('amt' => $amt_num_row, 'month' => $keys + 1);
            }
        }

        $num_loop = count($amt_due);
        $last_due_month = 0;
        foreach($amt_due as $row_due) {
            if ($ii == $num_loop - 1 ) {
                $last_due_month = $row_due['month'];
            }
            $ii++;
        }

        $compute_last = false;
        foreach($bills as $keys => $bills_row) {
            // get next key and value...
            $iter->next();
            $nextKey        = $iter->key();
            $nextValue      = $iter->current();
            $val_next       = $i++;
            $last = '';
            if($nextValue['month'] - 1 == $last_due_month && $due == true) {
                $last = 'last';
                $compute_last = true;
            }

            if ($nextValue['month'] > $curr && $curr < $val_next && $bills_row['amt'] > 0 ) {
                $int_cnt += 1;
                $int_amt = $amt * ($int_cnt * 0.0224);
                echo $bills_row['month'] . ' : ' . $bills_row['amt'] . ' : ';
                echo $int_amt;
                echo ' INT CNT: ' . $int_cnt . ' - ' . $last;
                echo '<br>';
                $arr_month_withint[$bills_row['month']] = $int_amt;
                $int_amt_total += $int_amt;
            }


            // if the next item has a 'tipo' key with a specific value

        }
        echo $last_due_month . '<br>';
        if($compute_last==true) {
            $total_int_compute = $int_cnt * 0.0224;
        }else{
            $total_int_compute = ($int_cnt-1) * 0.0224;
        }
        $int_amt_total = $total_int_compute * $amt;
        echo $int_amt_total;

    }

    function checkdatetest() {
        $date = new DateTime('2017-08-30');
        $now = new DateTime();
        if($date < $now) {
            echo 'passed';
        }
    }


    function custstats() {
        $data = array();
        $data['test'] = $this->input->post('test');
        $this->load->view('admin/pages/modules/custinfo/stats', $data);
    }

    function uploadexcelgeodata() {
        echo $this->model_mrd->upload_excel_geodata();
    }

    function getmrdcalendardt() {
        echo $this->model_mrd->get_mrd_calendar_dt();
    }
    function getmrdcalendar() {
        echo $this->model_mrd->get_mrd_calendar();
    }
    function delreadsched() {
        echo $this->model_mrd->del_read_sched();
    }
    function delreadschedall() {
        echo $this->model_mrd->del_read_sched_all();
    }
    function getmrdseqtab() {
        require_once APPPATH.'third_party/PHPExcel.php';

        init_header_nonav();
        echo '<div class="container" style="margin-top: 20px; margin-bottom: 20px; background: #fff;">';
        $ins_num = 0;
        $file = "local/mrd_seqtab.xls";
        $no_info_arr = '';
        if(file_exists(FCPATH.$file)) {
            $file_type = PHPExcel_IOFactory::identify($file);
            $objReader = PHPExcel_IOFactory::createReader($file_type);
            $objPHPExcel = $objReader->load($file);
            $sheet_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $i = 0;
            $num_rows = count($sheet_data);
            foreach ($sheet_data as $data) {
                $servno = trim($data['A']);
                $mtr = trim($data['B']);
                $mtrno = trim($data['C']);
                $ref = trim($data['D']);

                $qry_ref_reader = $this->db->select('userid')
                    ->from('prime_system_users_legacy_code')
                    ->where('telcode', $ref)
                    ->get()->row();
                if($i>0 && $qry_ref_reader) {
                    $userinfo = get_users_info($qry_ref_reader->userid);
                    $qry_acctinfo = $this->db->select('sysid')
                        ->from('customer_accounts_main')
                        ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                        ->get()->row();
                    if($qry_acctinfo) {
                        $qry_insert_check = $this->db->select('userid, acctid')
                            ->from('reading_schedule_specific')
                            ->where(array('userid' => $userinfo->sysid, 'acctid' => $qry_acctinfo->sysid, 'status' => 1))
                            ->get()->row();
                        if($qry_insert_check==false) {
                            $r_ins_arr = array(
                                'userid' => $userinfo->sysid,
                                'acctid' => $qry_acctinfo->sysid
                            );
                            $ins = $this->db->insert('reading_schedule_specific', $r_ins_arr);
                            $err_ins = $this->db->_error_message();
                            if ($ins) {
                                $ins_num += 1;
                            } else {
                                $no_info_arr .= '<tr>';
                                $no_info_arr .= '<td colspan="4">'.$err_ins.'</td>';
                                $no_info_arr .= '</tr>';
                            }
                        }else{

                            $no_info_arr .= '<tr>';
                            $no_info_arr .= '<td colspan="4">User ID: '.$ref.' not found!</td>';
                            $no_info_arr .= '</tr>';
                        }

                    }else{
                        $no_info_arr .= '<tr>';
                        $no_info_arr .= '<td>'.$servno.'</td>';
                        $no_info_arr .= '<td>'.$mtr.'</td>';
                        $no_info_arr .= '<td>'.$mtrno.'</td>';
                        $no_info_arr .= '<td>'.$ref.'</td>';
                        $no_info_arr .= '</tr>';
                    }
                }
                $i++;
            }
        }
        echo '<h3>Inserted: ' . $ins_num . ' / Total Valid Records: ' . $i . ' / Num Rows: '. $num_rows. '</h3>';
        echo '<hr>';
        echo '<h4>Not Inserted</h4>';
        echo '<table class="table table-hover table-bordered table-condensed">';
        echo '<thead>';
        echo '<th>Servno</th>';
        echo '<th>MTR</th>';
        echo '<th>MTRNO</th>';
        echo '<th>Ref</th>';
        echo '</thead>';
        echo '<tbody>';
        echo $no_info_arr;
        echo '</tbody>';
        echo '</table>';

        echo '</div>';
        init_footer_nonav();
    }


    function getlegacyseqtab() {
        echo $this->model_settings->get_legacy_seqtab();
    }


    function updatefromlegacyseqtab() {
        echo $this->model_settings->update_from_legacy_seqtab();
    }


    function updatemetersequence() {
        echo $this->model_settings->update_sequence_from_legacy();
    }

    function testave(){

        $comp = $this->model_peco->compute_acct_kwh_average(75301, 6);
        echo '<pre>';
        print_r($comp);
    }

    function customernearmtr($dataid = false) {
        echo $this->model_mrd->customer_near_mtr($dataid);
    }

}