<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 2/21/2019
 * Time: 11:56 AM
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/PHPExcel.php";

class Excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}