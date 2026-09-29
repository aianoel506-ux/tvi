<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');

if (!function_exists('encrypt_pass')){
    // ENCRYPT PASSWORD TO HASH
    function encrypt_pass($pass) {
        return password_hash($pass, PASSWORD_DEFAULT);
    }
}