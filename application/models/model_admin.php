<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// ############################################
// AUTHOR : LUCKY JOHN FADERON - SE
Class Model_admin extends CI_Model {

    function get_user_login_info($sessid) {
        $query = $this->db->select('su.sysid, su.username, su.type, su.idletime, suim.firstname, suim.lastname, suim.middlename, suim.gender')
            ->from('prime_system_users su')
            ->join('prime_system_users_info_main AS suim', 'suim.userid = su.sysid', 'left')
            ->join('prime_system_users_info_img AS suii', 'suii.userid = su.sysid', 'left')
            ->where(array('suim.status' => 1, 'su.sysid' => $sessid))
            ->group_by('su.sysid, su.username, su.type, su.idletime, suim.firstname, suim.lastname, suim.middlename, suim.gender')->get()->row();
        return ( $query ) ? $query : false;
    }

    function get_user_login_access() {
        return get_users_info_navigation_ids();
    }

    public function get_users_select2() {
        $term = $this->input->post('term');
        $this->db->like('firstname', $term);
        $q = $this->db->select('sysid, firstname')->from('prime_system_users')->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->firstname);
            }
        }
        $data['input'] = $this->input->post();
        return json_encode($data);
    }
    public function get_services_select2() {
        $term = $this->input->post('term');
        $this->db->like('codes', $term);
        $this->db->like('descs', $term);
        $q = $this->db->select()->from('prime_chart_of_accounts')
            ->where(array('types' => 1, 'status' => 1, 'groups' => 3))
            ->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->codes . ' - ' .$row->descs);
            }
        }
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_user_login_access_navigation() {
        return get_users_info_navigation_ids();
    }

    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (( $strict ? $item === $needle : $item == $needle ) || ( is_array($item) && $this->in_array_r($needle, $item, $strict) )) {
                return true;
            }
        }
        return false;
    }

    function select_modules() {
        $query = $this->db->select('sysid, code, name, desc, icon, type')->from('prime_module_main')->order_by('sorting', 'asc')->get();
        return ( $query->num_rows() > 0 ) ? $query->result() : false;
    }

    function select_modules_navigation($mid) {
        $query = $this->db->select('sysid, code, name, desc, icon, htmlclass, pagefile, type, hashcode')->from('prime_module_navigations_main')->where(array('parent' => $mid))->order_by('sorting', 'asc')->get();
        return ( $query->num_rows() > 0 ) ? $query->result() : false;
    }

    function check_modules_navigation($mid) {
        $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('parent' => $mid))->order_by('sorting', 'asc')->get();
        return ( $query->num_rows() > 0 ) ? true : false;
    }

    function array_module_navigations() {
        $modules = array();
        $navid = get_users_info_navigation_ids();
        if($navid) {
            foreach ($navid as $row) {
                $modules[] = $this->get_navigation_specific_navhash($row);
            }
        }
        return $modules;
    }

    function get_user_dashboard_access($hash) {
        if(get_users_roles_matrix_id_arr()) {
            $role_arr = get_users_roles_matrix_id_arr();

            $navid_public = $this->db->select('n.sysid')
                ->from('prime_module_navigations_main AS n')
                ->join('prime_module_navigations_public As np', 'np.navid = n.sysid')
                ->where(array('n.hashcode' => $hash, 'n.status' => 1))
                ->get()->row();
            if($navid_public) {
                $navid = $navid_public;
            }else {
                $navid = $this->db->select('n.sysid')
                    ->from('prime_module_navigations_main AS n')
                    ->join('prime_system_roles_dashboards AS d', 'd.navids = n.sysid AND d.status = 1')
                    ->where(array('n.hashcode' => $hash, 'n.status' => 1))
                    ->where_in('d.roleid', $role_arr)
                    ->get()->row();
            }
            return $navid;
        }else{
            return true;
        }
    }

    function get_navigation_general_parent($hash) {
        $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where('hashcode', $hash)->get()->row();
        return ( $query ) ? $this->get_navigation_general_parent_fn($query->sysid) : false;
    }

    function get_navigation_general_parent_fn($id) {
        $query = $this->db->select('sysid, parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $id)->get()->row();

        if($query) {
            $parent = $query->PARENT;
            // GET PARENT IF ZERO
            $query_parent = $this->db->select('parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $parent)->get()->row();
            if ($query_parent->PARENT == 0) {
                return $parent;
            } else {
                return $this->get_navigation_general_parent_fn($parent);
            }
        }else{
            return false;
        }
    }

    function get_navigation_details($id) {
        $query = $this->db->select()
            ->from('prime_module_navigations_main')
            ->where(array('status' => 1, 'sysid' => $id))
            ->get()->row();
        return ( $query ) ? $query : false;
    }

    function get_navigation_specific_navhash($id) {
        $query = $this->db->select('hashcode')->from('prime_module_navigations_main')->where(array('status' => 1, 'sysid' => $id))->get()->row();
        return ( $query ) ? $query : false;
    }

    function get_navigation_specific_details($hash) {
        $query = $this->db->select('sysid, name AS pname, parent AS PARENT, desc, pagefile, icon, htmlclass')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash))->get()->row();
        return ( $query ) ? $query : false;
    }

    function get_active_navigation_specific_details($hash) {
        $query = $this->db->select('nm.sysid AS pageid, nm.name AS pname, nm.desc, nm.pagefile, mm.sysid AS moduleid, COUNT(fms.levels) AS levels')->from('prime_module_navigations_main nm')->join('prime_module_main mm', 'mm.sysid = nm.parent')->join('prime_transaction_flow_main_stages fms', 'fms.moduleid = nm.sysid', 'left')->join('prime_transaction_flow_main fm', 'fm.sysid = fms.flowid', 'left')->where(array('nm.status' => 1, 'nm.hashcode' => $hash))->get()->row();
        return ( $query ) ? $query : false;
    }

    function init_navigation_info($hash) {
        if (!empty($hash)) {
            $query = $this->db->select('sysid, name AS pname, desc, pagefile')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash))->get()->row();
            return ( $query ) ? 'active' : '';
        } else {
            return '';
        }
    }

    function init_navigation_module_active_link($hash, $moduleid) {
        if (!empty($hash)) {
            $query = $this->db->select('name AS pname, desc, pagefile')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash, 'parent' => $moduleid))->get()->row();
            return ( $query ) ? 'active' : '';
        } else {
            return '';
        }
    }

    function init_navigation_active_link($hash, $navid) {
        if (!empty($hash)) {
            $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash, 'sysid' => $navid))->get()->row();
            return ( $query ) ? 'active open' : '';
        } else {
            return '';
        }
    }

    function init_navigation_open_sub($hash, $parent) {
        $data = array();
        $class = '';
        $mode = '';
        if (!empty($hash)) {
            $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash, 'parent' => $parent))->get()->row();
            if ($query) {
                $class = 'active open';
                $mode = 'open';
            } else {
                $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash))->get()->row();
                if ($query) {
                    $int = $this->init_navigation_open_sub_fn($query->sysid);
                    if ($int == $parent) {
                        $class = 'active open';
                        $mode = 'open';
                    }
                }
            }
        }
        $data['class'] = $class;
        $data['mode'] = $mode;
        return (object) $data;
    }

    function init_navigation_open_sub_fn($id) {
        $query = $this->db->select('sysid, parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $id)->get()->row();
        $parent = $query->PARENT;
        // GET PARENT IF ZERO
        if($parent!=0) {
            $query_parent = $this->db->select('parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $parent)->get()->row();
            if ( $query_parent && $query_parent->PARENT == 0 ) {
                return $parent;
            } else {
                return $this->get_navigation_general_parent_fn($parent);
            }
        }else{
            return $parent;
        }
    }

    function get_module_flow_start($moduleid) {
        $query = $this->db->select("pt_fm_s.sysid AS stageid")->from('prime_transaction_flow_main_stages pt_fm_s')->where(array('pt_fm_s.moduleid' => $moduleid))->order_by('pt_fm_s.levels', 'asc')->get()->row();
        return ( $query ) ? $query->stageid : false;
    }

    function insert_asset_data($assetdata) {
        return $this->db->insert('transaction_request_main', $trndata);
    }
    function save_manual_earnings(){
        $data = array();
        $gross = $this->input->post('gross');
        $tax = $this->input->post('tax');
        $deduction = $this->input->post('deduction');
        $typesid = $this->input->post('typesid');
        $empid = $this->input->post('empid');

        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $paytype = $this->input->post('paytype');


        $this->db->trans_begin();

        $checkforexistingvalue = $this->db->select("sysid")->from("payroll_manual_earnings")
            ->where(array("typesid" => $typesid , "empid" => $empid , "status" => 307,"month" => $month , "year" => $year , "paytype" => $paytype))
            ->get()->row();
        if($checkforexistingvalue){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("sysid" => $checkforexistingvalue->sysid));
            $this->db->update("payroll_manual_earnings" , $updatearr);
        }
        $insarr = array(
            'typesid' => $typesid,
            'empid' => $empid,
            'gross' => $gross,
            'tax' => $tax,
            'deduction' => $deduction,
            'month' => $month,
            'year' => $year,
            'paytype' => $paytype,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 307
        );
        $sql = $this->db->insert("payroll_manual_earnings" , $insarr);
        $data['insert'] = $this->db->last_query();

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Transaction has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_status();
            $msg = 'Failed to save transaction';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function populate_requirement_list() {
        $data = array();

        // $info = get_application_details($dataid);
        $dataid = $this->input->post('dataid');
        $origin = $this->input->post('origin');
        $sql = $this->db->select("sysid, reqid, appid, comply, status")
            ->from("application_customers_requirements")
            ->where(array('appid' => $dataid, 'status' => 1))
            ->get();
        $num_rows = $sql->num_rows();
        if($num_rows>0) {
            $num =1;
            $stat = '<span class="stat label label-danger"><i class="fa fa-times"></i></span>';
            foreach ($sql->result() as $row) {

                $control = '';
                if($row->reqid == 8){
                    $sqlcheck = $this->db->select("(SUM(totalamt) + SUM(franchisetax)) AS totalamt")
                        ->from("transaction_payments_logs")
                        ->where(array("payforacctno" => 163 , "dataid"=> $dataid , "moduleid" => $origin, 'status' => 1))
                        ->get()->row();

                    if($sqlcheck && $sqlcheck->totalamt > 0){
                        $stat= '<span class="stat label label-success"><i class="fa fa-check"></i></span>';
                    }else{
                        $stat= '<span class="stat label label-danger"><i class="fa fa-times"></i></span>';
                    }
                }else{
                    if($row->comply == 1){
                        $stat= '<span class="stat label label-success"><i class="fa fa-check"></i></span>';
                        $location = '';
                        $control = '<a  href="#form_view_cad_attachments" data-toggle="ajax-modal" data-view="'.$dataid.'" data-arr="'.$row->sysid.'" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>';
                    }else if($row->comply == 0){
                        $stat= '<span class="stat label label-danger"><i class="fa fa-times"></i></span>';
                        // $control = '<button id="assignfilebtn" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="assignfilebtn btn btn-default btn-xs"><i class="fa fa-search"></i>Assign File</button>';
                        // $control = '<a  href="#form_assignfile" data-toggle="ajax-modal" data-view="'.$dataid.'" data-arr="'.$row->sysid.'" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="assignfilebtn btn btn-default btn-xs"><i class="fa fa-search"></i>Assign File</a>';
                        $control = '<a  href="#form_assignfile" data-toggle="ajax-modal" data-view="'.$dataid.'" data-arr="'.$row->sysid.'" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="btn btn-default btn-xs"><i class="fa fa-search"></i>Assign File</a>';

                    }
                }
                $data['requirementslist'][] = array(
                    'num' => $num++,
                    'requirements' => get_requirement_name($row->reqid)->names,
                    'comply' => $stat,
                    'control' => $control
                );
            }
        }
        return json_encode($data);
    }

    function dt_docs_list() {
        $data = array();
        $folder = $this->input->post('folder');

        $file_directory = FCPATH.'uploads/attachments/'.$folder;
        $file_url = base_url().'uploads/attachments/'.$folder;

        $map = directory_map($file_directory, FALSE, TRUE);
        $files = array();

        if ($map && count($map) > 0) {
            $count = 0;
            foreach ($map as $file) {
                $control = '';
                $count++;
                $icon = draw_file_icon(basename($file));
                if (@is_array(getimagesize($file_url . $file))) {
                    $link = '<a class="btn btn-primary btn-sm preview" href="' . $file_url . $file . '"><i class="icon-magnifier"></i></a>';
                    $target = '';
                } else {
                    $link = '<a href="'.$file_url . $file.'" class="btn btn-primary btn-sm preview iframe" target="_blank"><i class="icon-magnifier"></i></a>';
                    $target = 'target="_blank"';
                }
                $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                $control .= '<a href="' . $file_url . $file . '" class="btn btn-sm btn-primary inline preview" id="btn_view_item" '.$target.'><i class="fa fa-search"></i> </a>';
                $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_delete_file" data-file="' . $file_url . $file . '"><i class="fa fa-trash"></i> </a>';
                $control .= '</div>';

                $data['list'][] = array(
                    'count' => $count,
                    'name' => '<i class="fa '.$icon->icon.' '.$icon->color.' "></i> '.basename($file),
                    'control' => $control
                );
            }
        }

        return json_encode($data);
    }

    function lookup_docs_otp() {
        $data = array();
        $dataid = $this->input->post('id');
        $doctype = $this->input->post('doctype');

        $docid = false;
        $otpid = false;

        $docs_qry = $this->db->select('sysid')
            ->from('prime_documents_main')
            ->where(array('dataid' => $dataid, 'doctype' => $doctype, 'status' => 1))
            ->get()->row();

        if ($docs_qry) {
            $data = $this->input->post();
            $docid = $docs_qry->sysid;

            $otp_qry = $this->db->select('sysid')
                ->from('prime_documents_signature_otp')
                ->where(array('docid' => $docs_qry->sysid, 'status' => 1))
                ->get()->row();

            if ($otp_qry) {
                $otpid = $otp_qry->sysid;
            }
        }

        $data['docid'] = $docid;
        $data['otpid'] = $otpid;

        return json_encode($data);
    }

    function generate_docs_otp() {
        $data = array();
        $dataid = $this->input->post('id');
        $docid = $this->input->post('docid');
        $existing = array();

        $otp_qry = $this->db->select('otpvalue')
            ->from('prime_documents_signature_otp')
            ->where(array('status !=' => 0))
            ->get();

        if ($otp_qry->num_rows() > 0) {
            foreach ($otp_qry->result() as $otp) {
                $existing[] = $otp->otpvalue;
            }
        }

        

        return json_encode($data);
    }

    function sign_document() {
        $data = array();
        $id = $this->input->post('id');
        $doctype = $this->input->post('doctype');

        $msg = '';
        $title = '';
        $func = '';
        $qry = false;
        $name = '';

        $signature = $this->db->select('imgdata')
            ->from('prime_user_signature')
            ->where(array('userid' => user_id(), 'status' => 1))
            ->get()->row();

        if ($signature) {
            $find_doc = $this->db->select('d.sysid,d.doctype,t.names,t.desc,d.signed')
                ->from('prime_documents_main as d')
                ->join('prime_types_parameter as t', 'd.doctype = t.sysid', 'left')
                ->where(array('d.status' => 1, 'dataid' => $id, 'doctype' => $doctype))
                ->get()->row();

            if ($find_doc) {
                $name = strtolower($find_doc->names);
                $data = (array)$find_doc;
                if ($this->db->update('prime_documents_main', array('signed' => 1), array('sysid' => $find_doc->sysid))) {
                    $msg = 'Your signature has been applied in ' . $find_doc->names . '.';
                    $func = 'success';
                    $title = 'Signed!';
                    $qry = true;
                } else {
                    $msg = 'Failed to apply signature in ' . $find_doc->names . '.';
                    $func = 'error';
                    $title = 'Failed!';
                    $qry = false;
                }
            }
            $data['signature'] = true;
        } else {
            $data['signature'] = false;
            $msg = 'You have requested to apply your signature. But, no sample has been found. Kindly update your signature in your account profile.';
            $func = 'warning';
            $title = 'Signature not found!';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;
        $data['name'] = $name;

        return json_encode($data);
    }
}

?>
