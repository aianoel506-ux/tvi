<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

$active_group               = 'tvi';
$active_group_audit         = 'audit';
$connect                    = 'dev';

if( $connect == 'dev' ) {
    // LOCALHOST
    $host_server            = 'localhost';
    $host_user              = 'root';
    $host_db                = 'tvi_erp';
    $host_pass              = '';

    $audit_server           = 'localhost';
    $audit_user             = 'root';
    $audit_db               = 'tvi_erp_audit';
    $audit_pass             = '';

} else if( $connect == 'online' ) {
    // GoDaddy / Staging
    $host_server            = 'localhost';
    $host_user              = 'your_db_user';
    $host_db                = 'your_db_name';
    $host_pass              = 'your_db_password';

    $audit_server           = 'localhost';
    $audit_user             = 'your_audit_user';
    $audit_db               = 'your_audit_db';
    $audit_pass             = 'your_audit_password';

} else {
    // PRODUCTION SERVER
    $host_server            = '127.0.0.1';
    $host_user              = 'your_db_user';
    $host_db                = 'your_db_name';
    $host_pass              = 'your_db_password';

    $audit_server           = '127.0.0.1';
    $audit_user             = 'your_audit_user';
    $audit_db               = 'your_audit_db';
    $audit_pass             = 'your_audit_password';
}



$query_builder = TRUE;

$db[$active_group]['sysmode'] = $active_group;
$db[$active_group]['sysaudit'] = $active_group_audit;


// ###################################################
// ############### ERP LOCAL #########################
// ###################################################
$db['tvi']['hostname'] = $host_server;
$db['tvi']['username'] = $host_user;
$db['tvi']['password'] = $host_pass;
$db['tvi']['database'] = $host_db;
$db['tvi']['port']     = '3306';
$db['tvi']['dbdriver'] = 'mysqli';
$db['tvi']['dbprefix'] = '';
$db['tvi']['pconnect'] = TRUE;
$db['tvi']['db_debug'] = TRUE;
$db['tvi']['cache_on'] = FALSE;
$db['tvi']['cachedir'] = '';
$db['tvi']['char_set'] = 'utf8';
$db['tvi']['dbcollat'] = 'utf8_general_ci';
$db['tvi']['swap_pre'] = '';
$db['tvi']['autoinit'] = TRUE;
$db['tvi']['stricton'] = FALSE;

// ###################################################
// ############### ERP AUDIT LOCAL ###################
// ###################################################
$db['audit']['hostname'] = $audit_server;
$db['audit']['username'] = $audit_user;
$db['audit']['password'] = $audit_pass;
$db['audit']['database'] = $audit_db;
$db['audit']['dbdriver'] = "mysqli";
$db['audit']['dbprefix'] = "";
$db['audit']['pconnect'] = FALSE;
$db['audit']['db_debug'] = TRUE;
$db['audit']['cache_on'] = FALSE;
$db['audit']['cachedir'] = "";
$db['audit']['char_set'] = "utf8";
$db['audit']['dbcollat'] = "utf8_general_ci";
$db['audit']['swap_pre'] = "";
$db['audit']['autoinit'] = TRUE;
$db['audit']['stricton'] = FALSE;