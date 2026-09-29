<?php
/**
 * Created by PhpStorm.
 * User: FADERON
 * Date: 3/16/2018
 * Time: 12:01 PM
 */




if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function net_on() {
    return true;
}

function super_admin() {
    if(user_id() > 0) {
        $roles_arr = get_users_roles_matrix_id_arr();
        if (in_array(1, $roles_arr) || user_id() == 1) {
            return true;
        } else {
            return false;
        }
    }else{
        return false;
    }
}


if (!function_exists('user_id')) {
    function user_id() {
        $ci = & get_instance();
        $session = (object) $ci->session->userdata('logged_in');
        return (isset($session->system_user_sessid) && $session->system_user_sessid > 0) ? $session->system_user_sessid : 0;
    }
    // system_user_sessid
}




if (!function_exists('get_users_roles_matrix_id_arr')) {

    function get_users_roles_matrix_id_arr() {
        $ci = & get_instance();
        $arr = array();
        if(user_id() > 0) {
            $user_roles = $ci->db->select('roleid')
                ->from('prime_system_users_roles_matrix')
                ->where(array('userid' => user_id(), 'status' => 1))
                ->get();
            $arr = array();
            if ($user_roles->num_rows() > 0) {
                foreach ($user_roles->result() as $row) {
                    $arr[] = $row->roleid;
                }
            }
            return $arr;
        } else {
            return false;
        }
    }

}

function setup_access() {
    $ci = &get_instance();
    if(null != user_id()) {
        $user_id = user_id();
        if($user_id == 1) {
            return true;
        }else {
            $qry_setup_access = $ci->db->query("SELECT * FROM prime_system_users_super WHERE userid = {$user_id}")->row();
            if ($qry_setup_access) {
                return true;
            } else {
                redirect(base_url(), 'refresh');
            }
        }
    }else{
        return true;
    }
}


function random_str($length = 10)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function draw_tab($code, $valact = false, $desc = false, $icons = false, $allowall = false) {
    $icon_tab = ($icons) ? 'icon' : '';
    $ci = &get_instance();
    $html = '';
    $html .= '';
    $html .= '<ul class="nav nav-tabs '.$icon_tab.'" id="tabber">';

    $qry_param = $ci->db->select(
        "tp.sysid, tp.names, tp.desc, i.icon"
    )->from('prime_types_parameter AS tp')
        ->join('system_icons AS i', 'i.sysid = tp.icons', 'left')
        ->where(array('tp.codes' => $code, 'tp.status' => 1))
        ->order_by('tp.names')
        ->get();
    if($qry_param->num_rows()>0) {

        if($allowall) {
            if ($valact == false) {
                $active = 'active';
            }
            $html .= '<li class="type ' . $active . '">';
            $html .= '<a class="#" data-id="" data-toggle="tab" aria-expanded="true">';
            if ($icons) {
                $html .= '<fa class="fa fa-search"></fa>';
            }
            $html .= '<span class="name">All</span>';
            if ($desc) {
                $html .= '<br>';
                $html .= '<small class="text-info">All records</small>';
            }
            $html .= '</a>';
            $html .= '</li>';
        }

        foreach($qry_param->result() as $row) {
            $active = '';
            if($valact) {
                if ($valact == $row->sysid) {
                    $active = 'active';
                }
            }
            $html .= '<li class="'.$active.'">';
            $html .= '<a class="conttype" data-id="'.$row->sysid.'" data-toggle="tab" aria-expanded="true">';
            if($icons) {
                $html .= '<fa class="fa ' . $row->icon . '"></fa>';
            }
            $html .= '<span class="name">'.$row->names.'</span>';
            if($desc) {
                $html .= '<br>';
                $html .= '<small class="text-info">'.$row->desc.'</small>';
            }
            $html .= '</a>';
            $html .= '</li>';
        }
    }else{
        $html .= '<li class="active">';
        $html .= '<a href="" data-id="" data-toggle="tab" aria-expanded="true"><i class="fa fa-times"></i> No tab available</a>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    return $html;
}

if(!function_exists('row_popover_a')) {
    function row_popover_a($id, $label, $contents, $title, $placements, $icon = true, $trigger = false, $button = false, $btnclass = '')
    {
        $fa = ($icon) ? '<i class="fa fa-pencil pull-left font-grey-silver"></i>' : '';
        if($button==true) {
            $btn_class = ' btn '. $btnclass;
        }else{
            $btn_class = '';
        }
        $trigger_ = '';
        if($trigger!=false) {
            $trigger_ = 'data-trigger="'.$trigger.'"';
        }
        return '<a id="' . $id . '" class="'.$btn_class.' popovers" '.$trigger_.' title="<i class=\'fa fa-edit\'></i> ' . $title . ' <button type=\'button\' aria-hidden=\'true\' class=\'close\'> &times;</button>" data-placement="' . $placements . '" data-content="' . $contents . '" href="javascript:;">' . $label . '</a>' . $fa;
    }
}
if(!function_exists('row_popover_button')) {
    function row_popover_button($id, $label, $contents, $title, $placements, $icon = true, $class)
    {
        $fa = ($icon) ? '<i class="fa fa-pencil pull-left font-grey-silver"></i>' : '';
        return '<button type="button" id="' . $id . '" class="btn '.$class.' popovers" title="<i class=\'fa fa-edit\'></i> ' . $title . ' <button type=\'button\' aria-hidden=\'true\' class=\'close\'> &times;</button>" data-placement="' . $placements . '" data-content="' . $contents . '" href="javascript:;">' . $label . '</button>' . $fa;
    }
}

if(!function_exists('pages_parent_navigation')) {
    function pages_parent_navigation($navid, $data = array()) {
        $ci = & get_instance();
        $cur_nav = $ci->db->select('parent')->from('prime_module_navigations_main')
            ->where('sysid', $navid)
            ->get()->row();
        if($cur_nav) {
            $qry_parent = $ci->db->select('pagefile')->from('prime_module_navigations_main')
                ->where(array('sysid' => $cur_nav->parent))->get()->row();
            if($qry_parent) {
                if (file_exists(FCPATH . 'application/views/admin/pages/modules/' . $qry_parent->pagefile . '/nav.php')) {
                    $ci->load->view('admin/pages/modules/' . $qry_parent->pagefile . '/nav', $data);
                }
            }
        }
    }
}

if(!function_exists('user_info')){
    function user_info($userid_ = false) {
        $ci = &get_instance();
        $query = false;
        if($userid_) {
            $userid = $userid_;
        }else{
            $userid = user_id();
        }
        $qry = $ci->db->query("SELECT * FROM prime_system_users WHERE sysid = $userid")->row();
        if($qry) {
            $res = array();
            $role_arr = array();

            $query = true;
            $lastname = $qry->lastname;
            $firstname = $qry->firstname;
            $middlename = '';

            $get_role = $ci->db->select('roleid')
                ->from('prime_system_users_roles_matrix')
                ->where(array('userid' => $userid, 'status' => 1, 'type' => 1))
                ->get();
            if($get_role->num_rows()>0){
                foreach($get_role->result() as $rrow) {
                    $role_arr[] = $rrow->roleid;
                }
            }


            if(!empty($qry->personid) && $qry->personid>0) {
                $person = $ci->db->select()->from('person')
                    ->where(array('sysid' => $qry->personid))
                    ->get()->row();
                if($person) {
                    $lastname = $person->lastname;
                    $firstname = $person->firstname;
                    $middlename= $person->middlename;
                }
            }
            $res = array(
                'sysid' => $qry->sysid,
                'username' => $qry->username,
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'personid' => $qry->personid,
                'idletime' => $qry->idletime,
                'roles' => $role_arr,
                'type' => $qry->type,
                'status' => $qry->status,
                'datecreated' => $qry->datecreated,
            );
        }
        return ($query) ? (object) $res : false;
    }
}

function windows_printer_connector($pcname = false) {
    $ci = &get_instance();
    $ci->load->library("EscPos.php", false);
    $ret = false;

    $printer_shared_name = 'Receipt'; // Receipt | Generic
    //$printer_shared_name = 'sprinter'; // Receipt | Generic

    try {
        if (PHP_OS == 'WINNT') {
            error_reporting(0);
            if($pcname==false) {
                $computer_name = $_SERVER['REMOTE_ADDR'];
            }else{
                $computer_name = $pcname;
            }
            $connector = new Escpos\PrintConnectors\FilePrintConnector("//".$computer_name."/".$printer_shared_name);
            $printer = new Escpos\Printer($connector);
            $ret = array(
                'printer' => $printer,
                'res' => true
            );
        } else {
            if($pcname==false) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $computer_name = exec("nmblookup -A $ip | grep '<00' | grep -v GROUP | awk '{print $1}'"); //get the computer name of $ip, only works when server is Linux
            }else{
                $computer_name = $pcname;
            }



            $ip = $_SERVER['REMOTE_ADDR'];
            // LINUX CHECK PRINTER
            //$conn_check = exec('smbclient -U u:p \'//'.$computer_name.'/'.$printer_shared_name.'\' -N');
            /*if(trim($conn_check) != '') {
                $message = 'Printer Host:  ' . $computer_name.'/'.$printer_shared_name;
                $ret = array(
                    'message' => $message . ' | ' . $conn_check,
                    'res' => false
                );
            }else{
            */
            $connector = new Escpos\PrintConnectors\WindowsPrintConnector("smb://".$ip."/".$printer_shared_name);
            $printer = new Escpos\Printer($connector);
            $ret = array(
                'printer' => $printer,
                'res' => true
            );
            //}
        }

    }  catch (Exception $e) {
        $ret = array(
            'message' => "Couldn't print to this printer: " . $e -> getMessage() . ' | ' . PHP_OS,
            'res' => false
        );
    }
    return (object)$ret;
}

if(!function_exists('receipt_header')) {
    function receipt_header($printer)
    {
        $logo = Escpos\EscposImage::load( FCPATH . "assets/global/img/receipt_logo.png", true);
        $printer -> bitImageColumnFormat($logo, $printer::IMG_DOUBLE_WIDTH | $printer::IMG_DOUBLE_HEIGHT);
        $printer -> setJustification($printer::JUSTIFY_CENTER);
        $printer -> setFont($printer::FONT_B);
        $printer -> setEmphasis(true);
        $printer -> text("Panay Electric Company, Inc.\n");
        $printer -> text("\"An enlightened past, a brighter future\"");
        $printer -> setEmphasis(false);
        $printer -> text("#12 GENERAL LUNA ST., ILOILO CITY\n");
        $printer -> text("VAT Reg. TIN: 001-002-833-00000\n");
        $printer -> feed();
        return $printer;
    }
}




if(!function_exists('receipt_footer')) {
    function receipt_footer($printer, $orno, $trnno)
    {
        $printer -> setJustification($printer::JUSTIFY_LEFT);
        $printer -> setFont($printer::FONT_B);
        $printer -> text(two_cols("Date: " .sql_time()->DATENAME, sql_time()->TIME12));
        $printer -> text("Cashier Code: ".user_info()->sysid." - ".trim(user_info()->firstname)." ".trim(user_info()->lastname)."\n");
        $printer -> text("Permit No.: 04-2015-123-0011-000\n");
        $printer -> text("Date of Issue: March 25, 2015\n");
        $printer -> text("Range of S/N: 00000001 to 99999999\n");
        $printer -> text("Transaction No.: ".str_pad($trnno, 8, '0', STR_PAD_LEFT)." \n");
        $printer -> text("Receipt No: ".str_pad($orno, 8, '0', STR_PAD_LEFT)."\n");
        $printer -> setEmphasis(true);
        $printer -> setJustification($printer::JUSTIFY_CENTER);
        $printer -> setUnderline(true);
        $printer -> text(space_both_sides(" "));
        $printer -> setUnderline(false);
        $printer -> text("This is a system generated receipt.\n");
        $printer -> text("Thank You!\n");
        return $printer;
    }
}

function two_cols($col_1 = '', $col_2 = '', $pesoSign = false){
    $col_2 = ($pesoSign ? 'PhP'.$col_2 : $col_2);
    //$space = 56 - strlen($col_1.$col_2);//Font_B has 56 characters/line
    if(strlen($col_1 . $col_2) < 40) {
        $space = 40 - strlen($col_1 . $col_2);//Font_B has 50 characters/line for Dot Matrix Printer
    }else{
        $space = 40;
    }
    return $col_1.str_repeat(" ", $space).$col_2."\n";
}
function two_cols_a($col_1 = '', $col_2 = '', $check = false){
    $col_2 = $col_2.($check?" /":"  ");
    //$space = 56 - strlen($col_1.$col_2);//Font_B has 56 characters/line
    if(strlen($col_1 . $col_2) < 40) {
        $space = 40 - strlen($col_1 . $col_2);//Font_B has 50 characters/line for Dot Matrix Printer
    }else{
        $space = 40;
    }
    return $col_1.str_repeat(" ", $space).$col_2."\n";

}
function three_cols($col_1 = '', $col_2 = '', $col_3 = ''){
    $space = str_repeat(" ", floor((40 - strlen($col_1.$col_2.$col_3))/2));//Font_B has 56 characters/line
    return $col_1.$space.$col_2.$space.$col_3."\n";
}
function three_cols_a($col_1 = '', $col_2 = '', $col_3 = '', $check = false){
    /*
        $col_3 = $col_3.($check?"  /":"   ");
        $space = str_repeat(" ", floor((40 - strlen($col_1.$col_2.$col_3." "))/2));//Font_B has 56 characters/line
        return $col_1.$space.$col_2.$space.$col_3."\n";
    */

    /*Objective: four columns with properly aligned data.*/
    $col_1 = $col_1.(str_repeat(" ", 8-strlen($col_1)));
    $col_2 = $col_2.(str_repeat(" ", 14-strlen($col_2)));
    $col_3 = (str_repeat(" ", 16-strlen($col_3))).$col_3.($check?" /":"  ");
    return $col_1.$col_2.$col_3."\n";

}
function four_cols($col_1 = '', $col_2 = '', $col_3 = '', $col_4 = '', $check = false){
    /*Objective: four columns with properly aligned data.*/
    $col_1 = $col_1.(str_repeat(" ", 6-strlen($col_1)));
    $col_2 = $col_2.(str_repeat(" ", 17-strlen($col_2)));
    $col_3 = $col_3.(str_repeat(" ", 6-strlen($col_3)));
    $col_4 = (str_repeat(" ", 25-strlen($col_4))).$col_4.($check?" /":"  ");

    return $col_1.$col_2.$col_3.$col_4."\n";
}
function four_cols_br($col_1 = '', $col_2 = '', $col_3 = '', $col_4 = '', $check = false){
    /*Objective: four columns with properly aligned data.*/
    $col_1 = $col_1.(str_repeat(" ", 8-strlen($col_1)));
    $col_2 = $col_2.(str_repeat(" ", 12-strlen($col_2)));
    $col_3 = $col_3.(str_repeat(" ", 4-strlen($col_3)));
    $col_4 = (str_repeat(" ", 14-strlen($col_4))).$col_4.($check?" /":"  ");

    return $col_1.$col_2.$col_3.$col_4."\n";
}
function space_both_sides($string = ''){
    /*we fill empty spaces with string spaces in order to span our underline across the width of the receipt.*/
    $space = str_repeat(" ",floor((40-strlen($string))/2));//56 is the total number of FONT_B (smallest font of Epson TM-T88IV) characters per line
    return $space.$string.$space."\n";
}
function space_right($string = ''){
    /*we fill empty spaces with string spaces in order to span our underline across the width of the receipt.*/
    $space = str_repeat(" ",40-strlen($string));//56 is the total number of FONT_B (smallest font of Epson TM-T88IV) characters per line
    return $string.$space."\n";
}
function add_leading_zero($string = ''){
    $zero = str_repeat("0", 10-strlen($string));
    return $zero.$string;//does not have \n, so that it can be used in combination with functions with new lines at the end.
}

if(!function_exists('get_server_memory_usage')) {
    function get_server_memory_usage()
    {
        $ci = &get_instance();
        $flatform = $ci->agent->platform();
        if($flatform=='LINUX') {
            $free = shell_exec('free');
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            $mem = explode(" ", $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_merge($mem);
            $memory_usage = $mem[2] / $mem[1] * 100;
            return $memory_usage;
        }else{
            return false;
        }
    }
}

if(!function_exists('get_server_cpu_usage')) {
    function get_server_cpu_usage()
    {
        $load = sys_getloadavg();
        if($load) {
            return $load[0];
        }else{
            return '0%';
        }
    }
}


if(!function_exists('isWeekend')) {
    function isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }
}


if(!function_exists('strip_tags_content')) {
    function strip_tags_content($string)
    {

        // ----- remove HTML TAGs -----
        $string = preg_replace ('/<[^>]*>/', ' ', $string);

        // ----- remove control characters -----
        $string = str_replace("\r", '', $string);    // --- replace with empty space
        $string = str_replace("\n", ' ', $string);   // --- replace with space
        $string = str_replace("\t", ' ', $string);   // --- replace with space

        // ----- remove multiple spaces -----
        $string = trim(preg_replace('/ {2,}/', ' ', $string));

        return $string;
    }
}

if(!function_exists('get_tax_amt')) {
    function get_tax_amt($amt)
    {
        $ci = &get_instance();
        if($amt>90000) {
            $taxable_amt = $amt - 90000;
        }else{
            $taxable_amt = $amt;
        }
        $qry_cont = $ci->db->query("SELECT amtcont, amtmin, rateemployee FROM prime_contribution_matrix WHERE conttype = 75 AND payclass = 1
                                   AND $taxable_amt BETWEEN amtmin AND amtmax")->row();
        $examt = 0;
        if($qry_cont ) {
            $examt = (($taxable_amt - $qry_cont->amtmin) * $qry_cont->rateemployee) + $qry_cont->amtcont;
        }

        return $examt;
    }
}


if(!function_exists('log_user_page')) {
    function log_user_page($moduleid)
    {
        $ci = &get_instance();
        $ins_page_logs = array(
            'moduleid' => $moduleid,
            'userid' => user_id()
        );
        $ins = $ci->db->insert('prime_module_users_logs', $ins_page_logs);
        return ($ins) ? true : false;
    }
}



if(!function_exists('get_trail_name')) {
    function get_trail_name($stageid)
    {
        $ci = &get_instance();
        $qry = $ci->db->select()
            ->from('prime_transaction_flow_main_stages')
            ->where(array('sysid' => $stageid))
            ->get()->row();

        return ($qry) ? $qry->desc : 'Unknown';
    }
}



if(!function_exists('sys_setup_dev')) {
    function sys_setup_dev()
    {
        return true;
    }
}



function make_thumb($src, $dest, $desired_width) {

    /* read the source image */
    $source_image = imagecreatefromjpeg($src);
    $width = imagesx($source_image);
    $height = imagesy($source_image);

    /* find the "desired height" of this thumbnail, relative to the desired width  */
    $desired_height = floor($height * ($desired_width / $width));

    /* create a new, "virtual" image */
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

    /* copy source image at a resized size */
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

    /* create the physical thumbnail image to its destination */
    imagejpeg($virtual_image, $dest);
}


function btn_back_to_list($list, $class = false,  $title = false, $icon = '') {

    $ci = &get_instance();
    $html = '';

    if($class) {
        $class = $class;
    }else{
        $class = '';
    }

    if($title) {
        $title = ' ' . $title;
    }else{
        $title = '';
    }

    if($icon != '') {
        $icon = ' <i class="'.$icon.'"></i> ';
    }else{
        $icon = '';
    }
    $total_segment = $ci->uri->total_segments();

    if($total_segment > 3) {
        $url = base_url() . 'module/'. $ci->uri->segment(2) . '/' . $list;
    }else{
        $url = base_url();
    }

    $html .= '<a href="'.$url.'" title="'.$title.'" class="btn-view-back '.$class.'">'.$icon.'Back'.$title.'</a>';

    return $html;

}

if(!function_exists('check_asset_status')) {
    function check_asset_status($assetid) {
        $ci = &get_instance();
        $data = array();
        $available = false;

        $qry_owner_hist = $ci->db->query("
            SELECT CAST(dateissued AS DATE) AS dateissued, ownerid FROM assets_main_owner_history 
            WHERE assetid = $assetid AND (status = 1 OR status = 300)
            ORDER BY sysid DESC
        ")->row();

        if($qry_owner_hist) {
            $status = '<span class="label label-danger">Issued</span>';
            $dateissued = $qry_owner_hist->dateissued;
            $types_id = 3205;
        }else {
            $qry_asset_status = $ci->db->query("
                        SELECT ar.typesid, ar.datecreated FROM assets_remarks AS ar 
                        INNER JOIN assets_status_matrix AS asm ON ar.typesid = asm.typesid
                        WHERE asm.codes = 'METER' AND ar.assetid = $assetid AND asm.available = 0
                        ORDER BY ar.sysid DESC
                    ")->row();
            if($qry_asset_status) {
                $qry_asset_status_a = $ci->db->query("
                        SELECT ar.typesid, ar.datecreated FROM assets_remarks AS ar 
                        INNER JOIN assets_status_matrix AS asm ON ar.typesid = asm.typesid
                        WHERE asm.codes = 'METER' AND ar.assetid = $assetid AND asm.available = 1
                        ORDER BY ar.sysid DESC
                    ")->row();
                if($qry_asset_status_a) {
                    $status = get_types_label_format($qry_asset_status_a->typesid);
                    $dateissued = '';
                    $available = true;
                    $types_id = 3204;
                }else {
                    $status = get_types_label_format($qry_asset_status->typesid);
                    $dateissued = $qry_asset_status->datecreated;
                    $types_id = 3205;
                }
            }else {
                $status = '<span class="label label-success">Available</span>';
                $dateissued = '';
                $available = true;
                $types_id = 3204;
            }
        }

        $data['status_id'] = $types_id;
        $data['status_text'] = $status;
        $data['status_date'] = $dateissued;
        $data['status_available'] = $available;

        return (object) $data;
    }
}


//PRINT SYSTEM HEADER
if(!function_exists('system_print_header')) {
    function system_print_header($depcode, $reptitle, $repnum, $pdf = false, $text = true) {
        $html = '';
        if($pdf==true) {
            // PDF IS TRUE = E:/xammp/htdocs/erp/
            $bgimg = FCPATH . 'assets/global/img/logo/tvi-logo-login.png';
        } else {
            // PDF IS FALSE = http://localhost/erp/
            $bgimg = base_url() . 'assets/global/img/logo/tvi-logo-login.png';
        }

        $html .= '<img style="width: 120px; height: 25px;" src="' . convert_base64_img($bgimg) . '" />';
        if($text) {
            //$html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; left: 150px; font-size: 12px; top: 0px; width: 300px; display: inline-block; text-align: center; font-weight: bold;">Panay Electric Company, Inc.</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 150px; font-size: 9px; top: 14px; display: inline-block; text-align: left; ">Emperor Cement Compound,Coastal Rd., Balabago, Jaro, Iloilo City</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; right: 90px; font-size: 9px; top: 18px; width: 150px; display: inline-block; text-align: right">' . $reptitle . '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; right: 60px; top: 5px; width: 130px; display: inline-block; border-right: 1px solid #ccc; height: 20px;"></span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; right: 5px; top: 0px; width: 130px; display: inline-block; font-weight: bold;">' . $repnum . '</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; right: 0px; font-size: 20px; top: 0px; width: 130px; display: inline-block; text-align: right; font-weight: bold;">' . $depcode . '</span>';
            $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
        } else {
            //$html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; left: 230px; font-size: 12px; top: 0px; width: 300px; display: inline-block; text-align: center; font-weight: bold;">Panay Electric Company, Inc.</span>';
            $html .= '<span style="font-family: courier, monospace; position: absolute; left: 230px; font-size: 9px; top: 14px; display: inline-block; text-align: left; ">Emperor Cement Compound,Coastal Rd., Balabago, Jaro, Iloilo City</span>';
            $html .= '<span style="font-family: Arial, Verdana, sans-serif !important; position: absolute; left: 230px; font-size: 9px; top: 40px; width: 300px; display: inline-block; text-align: center; ">'.$reptitle.'</span>';
            $html .= '<hr style="border: 1px dashed #ccc; margin: 5px 0px;">';
        }
        return $html;
    }
}



function db_trans($db, $msg_f = false, $msg_t = false, $session = true) {
    $data = array();

    $msg_false  = ($msg_f) ? $msg_f : 'Query error!';
    $msg_true   = ($msg_t) ? $msg_t : 'Query successs!';
    $data['title'] = 'PECO.net';

    if($db->trans_status() == true) {
        if(user_id() > 0) {
            $data['qry'] = true;
            $data['func'] = 'success';
            $data['msg'] = $msg_true;
            $db->trans_commit();
        }else{
            if($session == false) {
                $data['qry'] = true;
                $data['func'] = 'success';
                $data['msg'] = $msg_true;
                $db->trans_commit();
            }else {
                $data['qry'] = false;
                $data['func'] = 'error';
                $data['msg'] = 'Session Timeout';
                $db->trans_rollback();
            }
        }
    }else{
        $data['qry'] = false;
        $data['func'] = 'warning';
        $data['msg'] = $msg_false;
        $db->trans_rollback();
    }
    return $data;
}



if(!function_exists('echo_dbf')) {
    function echo_dbf($dbfname)  {
        $data = array();
        $fdbf = fopen($dbfname, 'r');
        $fields = array();
        $buf = fread($fdbf, 32);
        $header = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));
        $goon = true;
        $unpackString = '';
        while ($goon && !feof($fdbf)) { // read fields:
            $buf = fread($fdbf, 32);
            if (substr($buf, 0, 1) == chr(13)) {
                $goon = false;
            } // end of field list
            else {
                $field = unpack("a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf, 0, 18));
                $unpackString .= "A$field[fieldlen]$field[fieldname]/";
                array_push($fields, $field);
            }
        }
        fseek($fdbf, $header['FirstRecord'] + 1); // move back to the start of the first record (after the field definitions)
        for ($i = 1; $i <= $header['RecordCount']; $i++) {
            $buf = fread($fdbf, $header['RecordLength']);
            $record = unpack($unpackString, $buf);
            $data['rec'][] = $record;
            //$ret .=  $i . $buf . '<br/>';
        } //raw record
        return $data;
        fclose($fdbf);
    }
}


if (!function_exists('google_api_key')) {
    function google_api_key() {
        return 'AIzaSyDqC5lmJR1TtWTnySj2psx8-3JynOFUyYE';
    }
}

function time_elapsed_diff($timestart, $timeend) {
    return false;
}

function timeago($timestart, $timeend)
{
    $timestamp = strtotime($timestart);

    $strTime = array("second", "minute", "hour", "day", "month", "year");
    $length = array("60","60","24","30","12","10");

    //$currentTime = time();
    $currentTime = strtotime($timeend);
    if($currentTime >= $timestamp) {
        $diff     = time()- $timestamp;
        for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
            $diff = $diff / $length[$i];
        }

        $diff = round($diff);
        return $diff . " " . $strTime[$i] . "(s) ago ";
    }else{
        return 'Just now.';
    }
}

function is_image($path)
{
    $a = getimagesize($path);
    $image_type = $a[2];

    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
    {
        return true;
    }
    return false;
}


if (!function_exists('query_session_res')) {
    function query_session_res() {
        $data = array();
        $data['qry'] = false;
        $data['msg'] = 'Session timeout!';
        $data['func'] = 'error';
        $data['title'] = 'PECO.net';
        return $data;
    }
}

if (!function_exists('query_msg')) {
    function query_msg($qry = false, $func = false, $msg = false) {
        $data = array();
        $data['qry'] = $qry;
        $data['msg'] = ($msg) ? $msg : 'Check query!';
        $data['func'] = ($func) ? $func : 'info';
        $data['title'] = 'PECO.net';
        return $data;
    }
}

if(!function_exists('mailer')) {
    function mailer($email, $content, $subject, $file = false, $from = false)
    {
        $ci = &get_instance();
        $ci->load->library('email');
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'noreply.peco@gmail.com',
            'smtp_pass' => 'P3C02019',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'validation' => TRUE
        );

        $ci->email->initialize($config);
        $ci->email->set_mailtype("html");
        $ci->email->set_newline("\r\n");

        //Email Content
        $ci->email->to($email);
        //$ci->email->bcc('admin@panayelectric.com');
        if($from && $from != '') {
            $ci->email->from($from, $subject);
        }else {
            $ci->email->from('no-reply@panayelectric.com', $subject);
        }
        $ci->email->subject('PECO | ' . $subject);
        $ci->email->message($content);

        if ($file) {
            $ci->email->attach($file);
        }

        //Send Mail
        $sent = $ci->email->send();
        $ci->email->clear(true);
        return $sent;
    }
}


if(!function_exists('get_types_select')) {
    if(!function_exists('get_types_select')) {
        function get_types_select($codes, $ids = array()) {
            $data = array();
            $ci = &get_instance();
            if(count($ids) > 0) {
                $ci->db->where_in('sysid', $ids);
            }
            $sql = $ci->db->select("sysid,names,desc")
                ->from('prime_types_parameter')
                ->where(array('codes' => $codes,'status' => 1))
                ->get();

            if ($sql->num_rows() > 0){
                foreach ($sql->result() AS $row){
                    $data['list'][] = array(
                        'id' => $row->sysid,
                        'text' => $row->names . ' - ' . $row->desc
                    );
                }
            } else {
                $data['list'][] = array(
                    'id' => 0,
                    'text' => 'Error | 404 '
                );
            }
            return json_encode($data);
        }
    }
}


function remove_number_format($text){
    $text = str_replace(",", "", $text);
    return $text;
}

if(!function_exists('upper_ent_quotes')) {
    function upper_ent_quotes($title) {
        $title = ucwords(strtolower(trim($title, ENT_QUOTES)));
        return preg_replace_callback('/[\(\[].*?[\)\]]/', function ($m) {
            return strtoupper($m[0]);
        }, $title);
    }
}

if (!function_exists('color_inverse')) {
    function color_inverse($color)
    {
        $color = str_replace('#', '', $color);
        if (strlen($color) != 6) {
            return '000000';
        }
        $rgb = '';
        for ($x = 0; $x < 3; $x++) {
            $c = 255 - hexdec(substr($color, (2 * $x), 2));
            $c = ($c < 0) ? 0 : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0' . $c : $c;
        }
        return '#' . $rgb;
    }
}

function isValidChar($str) {
    return !preg_match('/[^A-Za-z0-9.#\\-$]/', $str);
}

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;

}

if(!function_exists('get_acronym')){
    function get_acronym($longname)
    {
        $longname = preg_replace('/[^\p{L}\p{N}\s]/u', '', $longname);
        $letters=array();
        $words=explode(' ', $longname);
        foreach($words as $word)
        {
            $word = (strlen($word) > 3) ? (substr($word, 0, 1)) : '';
            array_push($letters, $word);
        }
        $shortname = strtoupper(implode($letters));
        return $shortname;
    }
}

if(!function_exists('get_module_mods_access')){
    function get_module_mods_access($moduleid, $typesid) // TYPE ID MODS DLETE/RESTORE/EDIT
    {
        $ci = &get_instance();
        // prime_system_roles_module_access_matrix
        $users_roles = get_users_roles_matrix_id_arr();
        $qry_roles_access = $ci->db->select()
            ->from('prime_system_roles_module_access_matrix')
            ->where_in('roleid', $users_roles)
            ->where(array('typesid' => $typesid, 'status' => 1))
            ->get()->row();
        return ($qry_roles_access) ? true : false;
    }
}

if(!function_exists('get_module_list_delete')){
    function get_module_list_delete($moduleid, $dataid, $elid = false, $inline = false) // TYPE ID MODS DLETE/RESTORE/EDIT
    {
        $elid_ = ($elid) ? $elid : 'btn_delete_item';
        $inline_ = ($inline) ? 'inline' : '';
        $ci = &get_instance();
        $users_roles = get_users_roles_matrix_id_arr();
        $qry_roles_access = $ci->db->select()
            ->from('prime_system_roles_module_access_matrix')
            ->where_in('roleid', $users_roles)
            ->where(array('typesid' => 96, 'status' => 1))
            ->get()->row();
        if(super_admin() || $qry_roles_access) {
            return '<button type="button" class="btn btn-xs btn-danger '.$inline_.'" id="'.$elid_.'" data-id="' . $dataid . '" ><i class="fa fa-times"></i></button>';
        }else{
            return '';
        }
    }
}

if(!function_exists('html_view_notfound')){
    function html_view_notfound($class = false, $errcode = false, $msg = false) {
        $msg_ = ($msg) ? $msg : 'This page is not ready yet!';
        $errcode_ = ($errcode) ? $errcode : '404';
        $class_ = ($class) ? $class : 'danger';
        return '<h4><b class="text-'.$class_.'">'.$errcode_.'</b> : '.$msg_.'</h4>';
    }
}

if(!function_exists('input_res')){
    function input_res($inp) { // $inp post | get
        $ci = &get_instance();
        $input = $ci->input->$inp();

        $res = '';
        if(is_array($input) && count($input) > 0) {
            foreach($input as $i => $r){
                $columnsNames = array_keys($input, $r);
                $res .= "\n\r". $columnsNames[0] . ': ' . $r;
            }
        }
        return $res;
    }
}

if(!function_exists('unit_query')){
    function unit_query() { // $inp post | get
        $ci = &get_instance();
        $qry = $ci->db->select()->from('prime_unit')->get();

        if($qry->num_row()>0) {
            return $qry->result();
        }else{
            return false;
        }
    }
}

if(!function_exists('search_file')) {
    function search_file($dir, $file_to_search)
    {

        $files = scandir($dir);

        foreach ($files as $key => $value) {

            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

            if (!is_dir($path)) {

                if ($file_to_search == $value) {
                    echo "file found<br>";
                    echo $path;
                    break;
                }

            } else if ($value != "." && $value != "..") {

                search_file($path, $file_to_search);

            }
        }
    }
}

function draw_file_icon($filename) {
    $data = array();
    $icon = 'fa-file';
    $color = '';
    $filename_arr = pathinfo($filename,PATHINFO_EXTENSION);
    switch (strtolower($filename_arr)) {
        case "pdf":
            $icon = 'fa-file-pdf-o';
            $color = 'font-red-flamingo';
            break;
        case "xls":
            $icon = 'fa-file-excel-o';
            $color = 'font-green-meadow';
            break;
        case "xlsx":
            $icon = 'fa-file-excel-o';
            $color = 'font-green-meadow';
            break;
        case "doc":
            $icon = 'fa-file-word-o';
            $color = 'font-blue-sharp';
            break;
        case "docx":
            $icon = 'fa-file-word-o';
            $color = 'font-blue-sharp';
            break;
        default:
            $icon = 'fa-file';
    }

    $data['icon'] = $icon;
    $data['color'] = $color;
    return (object)$data;
}
if (!function_exists('count_menu_children')) {
    function count_menu_children($parent){
        $ci = &get_instance();
        $subm_qry = $ci->db->select()
            ->from('prime_module_navigations_main AS m')
            ->where(array('m.parent' => $parent, 'm.status' => 1))
            ->get();

        $numrows = $subm_qry->num_rows();

        if ($numrows > 0) {
            /*$num = 0;
            foreach ($subm_qry->result() as $subm) {
                if (get_user_access($subm->sysid)) {
                    $num++;
                }
                $num++;
            }*/
            return $numrows;
        } else {
            return false;
        }
    }
}

if (!function_exists('convert_base64_img')) {
    function convert_base64_img($img_path, $img_type = 'png', $img_width = false, $img_height = false){
        $temp_location = FCPATH.'uploads/temp';
        if (!is_dir($temp_location)) {
            mkdir($temp_location, 0777, TRUE);
            chmod($temp_location, 0777);
        } else {
            chmod($temp_location, 0777);
        }

        if ($img_path) {
            $filesize = filesize($img_path) / 1024;
            if ($filesize > 500) {
                $img_width = '75%';
                $img_height = '75%';
            }

            if ($img_width || $img_height || $filesize > 500) {
                // This little part under depend if you wanna keep the ratio of the image or not
                $img_info = pathinfo($img_path);

                $width = (strpos('%',$img_width) !== 0) ? floatval($img_width)/100 : $img_width;
                $height = (strpos('%',$img_height) !== 0) ? floatval($img_height)/100 : $img_height;
                list($width_orig, $height_orig) = getimagesize($img_path);
                $new_width = ($width <= 1) ? $width_orig * $width : $width;
                $new_height = ($height <= 1) ? $height_orig * $height : $height;
                $ratio_orig = $width_orig/$height_orig;
                if ($new_width/$new_height > $ratio_orig) {
                    $new_width = $new_height*$ratio_orig;
                } else {
                    $new_height = $new_width/$ratio_orig;
                }

                if ($img_type == "png") {
                    $image = imagecreatefrompng($img_path);
                } else {
                    $image = imagecreatefromjpeg($img_path);
                }

                $bg = imagecreatetruecolor($new_width, $new_height);
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagealphablending($bg, TRUE);
                imagecopyresampled($bg, $image, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                imagedestroy($image);
                imagejpeg($bg, $temp_location.'/'.$img_info['filename'], 75);
                $bin_string_little = file_get_contents($temp_location.'/'.$img_info['filename']);
                unlink($temp_location.'/'.$img_info['filename']);
                imagedestroy($bg);
                $img_src = "data:image/" . $img_type . ";base64," . str_replace("\n", "", base64_encode($bin_string_little));
            } else {
                //convert image into Binary data
                $img_data = fopen($img_path, 'rb');
                $img_size = filesize($img_path);
                $binary_image = fread($img_data, $img_size);
                fclose($img_data);

                //Build the src string to place inside your img tag
                $img_src = "data:image/" . $img_type . ";base64," . str_replace("\n", "", base64_encode($binary_image));

            }

            return $img_src;
        } else {
            return false;
        }
    }
}

if (!function_exists('result_array_look_up')) {
    function result_array_look_up($array, $key, $val) {
        $result = array();
        $items = array();
        $id = '';
        $array = (is_array($array)) ? (object)$array : $array;
        foreach ($array as $item) {
            if (isset($item->$key) && $item->$key == $val) {
                $id = (isset($item->sysid)) ? $item->sysid : false;
                $result[] = true;
                $items[] = $item->$key;
            } else {
                $result[] = false;
            }
        }

        $data = array(
            'result' => (in_array(true,$result)),
            'sysid' => $id,
            'items' => $items
        );

        return (object)$data;
    }
}

if (!function_exists('sys_upload_files')) {
    function sys_upload_files($fieldname,$uploadpath,$filename){
        $ci = &get_instance();
        $data = array();
        $msg = '';
        $qry = false;

        $ci->load->helper('directory');
        $ci->load->library('upload');

        $config['upload_path'] = $uploadpath;
        $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|jpeg';
        $config['max_size'] = 100000;
        $config['max_width'] = 5000;
        $config['max_height'] = 8000;
        $config['encrypt_name'] = FALSE;
        $config['file_name'] = $filename;


        $ci->upload->initialize($config);

        // ###############################################
        // CREATE DIRECTORY

        if (!is_dir($uploadpath)) {
            mkdir($uploadpath, 0777, TRUE);
            chmod($uploadpath, 0777);
        } else {
            chmod($uploadpath, 0777);
        }

        // ###############################################
        if (!$ci->upload->do_upload($fieldname)) {
            $msg = array('error' => $ci->upload->display_errors() .  ' ' .$uploadpath) ;
        } else {
            $uploaddata = $ci->upload->data();
            $msg = array('upload_data' => $uploaddata);
            $qry = true;
        }

        $data['msg'] = $msg;
        $data['uploaded'] = $qry;

        return $data;
    }

    if (!function_exists('dt_inline_input')) {
        function dt_inline_input($name,$value,$style = array()) {
            $css = '';
            $style_str = '';
            if (count($style) > 0) {
                foreach ($style AS $prop => $val) {
                    $style_str .= $prop.': '.$val.';';
                }
                $css .= 'style="'.$style_str.'"';
            }
            $input = '<input id="input_'.$name.'" value="'.$value.'" name="'.$name.'" class="form-control inline " '.$css.' disabled>';

            return $input;
        }
    }
}

if (!function_exists('dt_column_array')) {
    function dt_column_array($data,$sTitle = false,$sClass = false,$sWidth = false) {
        $column = array();
        $column['data'] = $data;
        if ($sTitle && $sTitle != '') {
            $column['sTitle'] = $sTitle;
        }
        if ($sClass && $sClass != '') {
            $column['sClass'] = $sClass;
        }
        if ($sWidth && $sWidth != '') {
            $column['sWidth'] = $sWidth;
        }

        return $column;
    }
}