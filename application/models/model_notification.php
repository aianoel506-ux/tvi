<?php
// ############################################
// AUTHOR : LUCKY JOHN FADERON - SE
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_notification extends CI_Model {
	function get_menu_usertrn() {
		// GET TRN MAIN FIRST
		$qry_trn = $this->db->select()->from('transaction_request_main')->where('createdby', $userid)->get();
	}
}

?>