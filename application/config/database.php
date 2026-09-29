<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

$active_group               = getenv('CI_DB_GROUP') ?: 'tvi';
$active_group_audit         = 'audit';
$connect                    = getenv('CI_CONNECT') ?: 'online';

if( $connect == 'dev' ) {
    // LOCALHOST
    $host_server            = getenv('DB_HOST') ?: 'localhost';
    $host_user              = getenv('DB_USER') ?: 'root';
    $host_db                = getenv('DB_NAME') ?: 'tvi_erp';
    $host_pass              = getenv('DB_PASS') ?: '';

    $audit_server           = getenv('AUDIT_DB_HOST') ?: 'localhost';
    $audit_user             = getenv('AUDIT_DB_USER') ?: 'root';
    $audit_db               = getenv('AUDIT_DB_NAME') ?: 'tvi_erp_audit';
    $audit_pass             = getenv('AUDIT_DB_PASS') ?: '';

} else if( $connect == 'online' ) {
    // GoDaddy / Cloud / Vercel (Configurable via Environment Variables)
    $host_server            = getenv('DB_HOST') ?: 'localhost';
    $host_user              = getenv('DB_USER') ?: 'uub4rmw23inpzxn9_pae_root';
    $host_db                = getenv('DB_NAME') ?: 'uub4rmw23inpzxn9_tvi_erp';
    $host_pass              = getenv('DB_PASS') ?: '';

    $audit_server           = getenv('AUDIT_DB_HOST') ?: (getenv('DB_HOST') ?: 'localhost');
    $audit_user             = getenv('AUDIT_DB_USER') ?: (getenv('DB_USER') ?: 'uub4rmw23inpzxn9_pae_root');
    $audit_db               = getenv('AUDIT_DB_NAME') ?: 'uub4rmw23inpzxn9_tvi_erp_audit';
    $audit_pass             = getenv('AUDIT_DB_PASS') ?: (getenv('DB_PASS') ?: '');

} else {
    // PAE SERVER
    $host_server            = getenv('DB_HOST') ?: '172.20.224.5';
    $host_user              = getenv('DB_USER') ?: 'lucky';
    $host_db                = getenv('DB_NAME') ?: 'pae';
    $host_pass              = getenv('DB_PASS') ?: '';

    $audit_server           = getenv('AUDIT_DB_HOST') ?: '172.20.224.5';
    $audit_user             = getenv('AUDIT_DB_USER') ?: 'lucky';
    $audit_db               = getenv('AUDIT_DB_NAME') ?: 'pae_audit';
    $audit_pass             = getenv('AUDIT_DB_PASS') ?: '';
}



$query_builder = TRUE;

$db[$active_group]['sysmode'] = $active_group;
$db[$active_group]['sysaudit'] = $active_group_audit;


// ###################################################
// ############### ERP ###############################
// ###################################################
$db['tvi']['hostname'] = $host_server;
$db['tvi']['username'] = $host_user;
$db['tvi']['password'] = $host_pass;
$db['tvi']['database'] = $host_db;
$db['tvi']['port']     = getenv('DB_PORT') ?: '3306';
$db['tvi']['dbdriver'] = 'mysqli';
$db['tvi']['dbprefix'] = '';
$db['tvi']['pconnect'] = FALSE;
$db['tvi']['db_debug'] = TRUE;
$db['tvi']['cache_on'] = FALSE;
$db['tvi']['cachedir'] = '';
$db['tvi']['char_set'] = 'utf8';
$db['tvi']['dbcollat'] = 'utf8_general_ci';
$db['tvi']['swap_pre'] = '';
$db['tvi']['autoinit'] = TRUE;
$db['tvi']['stricton'] = FALSE;

// ###################################################
// ############### ERP AUDIT #########################
// ###################################################
$db['audit']['hostname'] = $audit_server;
$db['audit']['username'] = $audit_user;
$db['audit']['password'] = $audit_pass;
$db['audit']['database'] = $audit_db;
$db['audit']['port']     = getenv('AUDIT_DB_PORT') ?: '3306';
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