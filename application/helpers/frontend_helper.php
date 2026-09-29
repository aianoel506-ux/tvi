<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/29/2018
 * Time: 1:31 PM
 */

if(!function_exists('check_whitelisting')) {
    function check_whitelisting () {
        $data = array();
        $ci = &get_instance();

        $allowed = false;
        $access_types = '';

        /*
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        */

        $ip = $_SERVER['REMOTE_ADDR'];

        if($ip=='::1') {
            $new_ip = 'localhost';
        }else{
            $new_ip = $ip;
        }

        $get_white_listing = $ci->db->select('types')
            ->from('system_ip_listing')
            ->where(array('ips' => $new_ip, 'status' => 1))
            ->get()->row();


        $access = get_users_info(NULL, true);
        /*
        $user_allow_external = ($access && boolval($access->allowexternal)) ? true : false;
        if ($get_white_listing>0 || $new_ip == 'localhost' || $new_ip == '127.0.0.1' || substr($new_ip, 0, 10) == "172.20.224" || $user_allow_external == true) {
            $allowed = true;
        }
        */

        if($new_ip == 'localhost' || $new_ip == '127.0.0.1' || $get_white_listing || substr($new_ip, 0, 10) == "172.20.224") {
            if($get_white_listing) {
                $access_types = $get_white_listing->types;
            }
            $allowed = true;
        }

        $allowed = true;

        $data['accesstype'] = $access_types;
        $data['allowed'] = $allowed;
        $data['actualIP'] = $ip;
        $data['ip'] = $new_ip;

        return (object)$data;
    }
}
if(!function_exists('check_access')) {
    function check_access () {
        $res = array();
        $check = check_whitelisting();
        if ($check->allowed == true) {
            $res = $check;
        }
        return $res;
    }
}