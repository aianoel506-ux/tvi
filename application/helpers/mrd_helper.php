<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');

if (!function_exists('get_list_findings')){
    // ENCRYPT PASSWORD TO HASH
    function get_list_findings() {
        $ci = &get_instance();
        $qry = $ci->db->select()->get('meter_reading_findings');
        return ($qry) ? $qry->result() : false;
    }
    
}



