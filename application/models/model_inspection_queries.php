<?php
if ( !defined( 'BASEPATH' ) )exit( 'No direct script access allowed' );

Class Model_inspection_queries extends CI_Model {
	function get_transaction_details() {
		$query = $this->db->query( "SELECT ao.firstname applicant_fn, ao.lastname applicant_ln, trm.dataid, trm.trncode, trm.codes, trm.validations, em.lastname employee_ln, em.firstname employee_fn, trm.descriptions, trm.datecreated, trm.status FROM transaction_request_main AS trm "
			. "LEFT JOIN prime_employee_main AS em "
			. "ON trm.createdby = em.sysid "
			. "LEFT JOIN prime_customer_accounts_owners AS ao "
			. "ON trm.dataid = ao.accountid "
			. "WHERE trm.moduleid = 9 ORDER BY trm.datecreated DESC" );
		return ( $query ) ? $query->result() : false;
	}

	function get_hashcode( $moduleid ) {
		$sql = "SELECT navm.hashcode FROM transaction_request_main AS trn "
			. "LEFT JOIN prime_module_navigations_main AS navm "
		. "ON navm.sysid = trn.moduleid WHERE trn.moduleid = ?";
		$query = $this->db->query( $sql, array( $moduleid ) );
		return ( $query ) ? $query->row() : false;
	}

	function get_equipments() {
		$query = $this->db->query( "SELECT codes, sysid, descriptions FROM prime_equipments_parameters" );
		return ( $query ) ? $query->result() : false;
	}

	function get_account_types() {
		$query = $this->db->query( "SELECT names, sysid FROM prime_types_parameter WHERE codes = 'CAPPS'" );
		return ( $query ) ? $query->result() : false;
	}

	function get_account_type_name( $sysid ) {
		$sql = "SELECT names FROM prime_types_parameter WHERE sysid = ?";
		$query = $this->db->query( $sql, array( $sysid ) );
		return ( $query ) ? $query->row() : false;
	}

	function get_city() {
		$query = $this->db->query( "SELECT sysid, names FROM address_city" );
		return ( $query ) ? $query->result() : false;
	}

	function get_city_name( $sysid ) {
		$sql = "SELECT names FROM prime_city WHERE sysid = ?";
		$query = $this->db->query( $sql, array( $sysid ) );
		return ( $query ) ? $query->row() : false;
	}

	function get_district() {
		$query = $this->db->query( "SELECT sysid, names FROM address_districts" );
		return ( $query ) ? $query->result() : false;
	}

	function get_district_name( $sysid ) {
		$sql = "SELECT names FROM address_districts WHERE sysid = ?";
		$query = $this->db->query( $sql, array( $sysid ) );
		return ( $query ) ? $query->row() : false;
	}

	function get_user_name( $accountid = '' ) {
		$sql = "SELECT * FROM prime_customer_accounts_owners WHERE accountid = ?";
		$query = $this->db->query( $sql, array( $accountid ) );
		return ( $query ) ? $query->row() : false;
	}

	function add_equipment_data( $data ) {
		$rate_class_id = $data[ 'rateClass' ];
		$inspection_date = $data[ 'inspDate' ];
		$account_type = $data[ 'accountType' ];
		$district = $data[ 'district' ];
		$city = $data[ 'city' ];
		$specific_address = $data[ 'specificAddress' ];
		$trn = $data[ 'trn' ];
		$x = $data[ 'latitude' ];
		$y = $data[ 'longitude' ];
		$accountid = $data[ 'accountID' ];
		$total_load = 0;

		//query strings start
		$sql_eq_values = "SELECT watts, qty FROM prime_customer_accounts_equipments WHERE accountid = ? AND status = ?";
		$sql_ao = "SELECT sysid FROM prime_customer_accounts_owners WHERE accountid = ?";
		$sql_oa = "INSERT INTO prime_customer_accounts_owners_address (ownerid, district, city, country, addrspecific, addrmapx, addrmapy) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$sql_am = "UPDATE prime_customer_accounts_main SET types=? WHERE sysid = ?";
		$sql_om_in = "INSERT INTO prime_customer_accounts_owners_meter (accountid, ownerid, rateid, createdby, status) VALUES (?, ?, ?, ?, ?)";
		$sql_om = "SELECT sysid FROM prime_customer_accounts_owners_meter WHERE accountid = ?";
		$sql_al = "INSERT INTO prime_customer_accounts_logs (accountid, logtype, logamount, meterid, logdate, createdby, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$sql_rm = "UPDATE transaction_request_main SET status = ?, stagesid = ? WHERE trncode = ?";
		//query strings end
		$ownerid = $this->db->query( $sql_ao, array( $accountid ) )->row()->sysid;
		$eq_values = $this->db->query( $sql_eq_values, array( $accountid, 1 ) )->result();
		//deposit cost computation start
		foreach ( $eq_values as $val ) {
			$total_load += $val->watts * $val->qty;
		}
		$row = $this->retrieve_rate_data( $rate_class_id );
		$deposit_cost = $this->deposit_cost( $total_load, $row->dailyops, $row->monthlyops, $row->demand, $row->rates );
		//deposit cost computation end
		$this->db->trans_start(); //using transactions means that all the queries must be successful before committing the data to database.
		//query bindings automatically escapes mysql data, no need for manually escaping data before querying
		//no need to specify that the data as string (e.g. '$specific_address') during the query.
		$this->db->query( $sql_oa, array( $ownerid, $district, $city, 175, $specific_address, $x, $y ) );
		$this->db->query( $sql_am, array( $account_type, $accountid ) );
		$this->db->query( $sql_om_in, array( $accountid, $ownerid, $rate_class_id, 1, 1 ) );
		$this->db->query( $sql_al, array( $accountid, 12, round( $deposit_cost, 2 ), $this->db->query( $sql_om, array( $accountid ) )->row()->sysid, $inspection_date, 1, 1 ) );
		$this->db->query( $sql_rm, array( 1, 4, $trn ) );
		return $this->db->trans_complete();

		//return ($affected_rows > 0) ? true : false;
		//return $affected_rows;
	}

	function edit_data( $dataid ) {
		$sql = "SELECT logdate, tp.names account_type_name, types account_type_id, oa.sysid address_id, district district_id, d.names district_name, city city_id, c.names city_name, addrspecific, classifications rate_name, cm.sysid rate_id FROM prime_customer_accounts_logs al "
			. "LEFT JOIN prime_customer_accounts_main am "
		. "ON al.accountid = am.sysid "
		. "LEFT JOIN prime_customer_accounts_owners_address oa "
		. "ON al.accountid = (SELECT accountid FROM prime_customer_accounts_owners WHERE sysid = oa.ownerid) "
		. "LEFT JOIN prime_types_parameter tp "
		. "ON am.types = tp.sysid "
		. "LEFT JOIN address_districts d "
		. "ON oa.district = d.sysid "
		. "LEFT JOIN address_city c "
		. "ON oa.city = c.sysid "
		. "LEFT JOIN prime_system_rate_class_main cm "
		. "ON cm.sysid = (SELECT rateid FROM prime_customer_accounts_owners_meter WHERE accountid = al.accountid AND sysid = al.meterid) "
		. "WHERE al.accountid = ?";
		$query = $this->db->query( $sql, array( $dataid ) );
		return ( $query ) ? $query->row() : false;
	}

	/*
	function edit_equipment_data(){
	    $dataid = $this->input->post('dataid');
	    $page = $this->input->post('page');
	    $this->datatables->select("e.sysid, ep.codes, e.watts, e.qty");
	    $this->datatables->unset_column('sysid');
	    $this->datatables->unset_column('watts');
	    $this->datatables->unset_column('qty');
	    $display_state = '';
	    if ($page == 'view'){//viewing only, editing is disabled
	       $display_state = 'disabled';
	    }
	    $this->datatables->add_column('sysid', '<input style="width: 80px" type="hidden" name="equipment_id[]" value="$1" class="form-control input-sm" '.$display_state.'/>', 'sysid');
	    $this->datatables->add_column('watts', '<input style="width: 80px" type="number" name="equipment_wattage[]" value="$1" class="form-control input-sm" '.$display_state.'/>', 'watts');
	    $this->datatables->add_column('qty', '<input style="width: 80px" type="number" name="equipment_qty[]" value="$1" class="form-control input-sm" '.$display_state.'/>', 'qty');
	    $this->datatables->add_column('control', $display_state=='disabled' ? '<p>X</p>' : $this->get_buttons('$1', '2'), 'sysid');
	    $this->datatables->from('prime_customer_accounts_equipments e');
	    $this->datatables->join('prime_equipments_parameters AS ep', 'ep.sysid = e.equipid', 'left');
	    $this->datatables->where(array('e.accountid' => $dataid, 'e.status' => 1));
	    return $this->datatables->generate();
	}
	 * 
	 */

	function edit_equipment_data() {
		$dataid = $this->input->post( 'dataid' );
		$page = $this->input->post( 'page' );
		$display_state = '';
		$data = array();
		if ( $page == 'view' ) { //viewing only, editing is disabled
			$display_state = 'disabled';
		}
		$qry = $this->db->select( 'e.sysid, ep.codes, e.watts, e.qty, e.equipid, ep.codes' )->from( "customer_accounts_equipments e" )->join( 'prime_equipments_parameters AS ep', 'ep.sysid = e.equipid', 'left' )->where( array( 'e.accountid' => $dataid, 'e.status' => 1 ) )->get();
		$num_rows = $qry->num_rows();
		if ( $num_rows > 0 ) {
			foreach ( $qry->result() as $row ) {
				$data[ 'data' ][] = array(
					'codes' => $row->codes,
					'watts' => $row->watts,
					'qty' => $row->qty,
					'control' => $display_state == 'disabled' ? '<p>X</p>' : $this->get_buttons( $row->sysid, '2' )
				);
			}
		}
		return json_encode( $data );
	}

	function view_account_equipments( $dataid ) {
		$sql = "SELECT codes, watts, qty FROM prime_customer_accounts_equipments WHERE accountid = ?";
		$query = $this->db->query( $sql, array( $dataid ) );
		return ( $query ) ? $query->result() : false;
	}

	function get_buttons( $id, $page ) {
		$html = '<span class="btn-group">';
		$html .= '<input type="hidden" value="' . $id . '" name="rowid" id=rowid>';
		$html .= '<a class="btn btn-danger btn-xs stat" ><i class="fa fa-times"></i></a>';
		$html .= '</span>';
		return $html;
	}

	function updateCity( $array ) {
		$sql = 'UPDATE prime_customer_accounts_owners_address SET city = ? WHERE sysid = ? and status = ?';
		$query = $this->db->query( $sql, array( $array[ 'newValue' ], $array[ 'identifier' ], 1 ) );
		return ( $query->num_rows > 0 ) ? true : false;
	}

	function updateDistrict( $array ) {
		$sql = 'UPDATE prime_customer_accounts_owners_address SET city = ? WHERE sysid = ? and status = ?';
		$query = $this->db->query( $sql, array( $array[ 'newValue' ], $array[ 'identifier' ], 1 ) );
		return ( $query->num_rows > 0 ) ? true : false;
	}

	function updateInspectionDate( $array ) {
		$sql = 'UPDATE prime_customer_accounts_logs SET logdate = ? WHERE accountid = ?';
		$query = $this->db->query( $sql, array( $array[ 'newValue' ], $array[ 'identifier' ] ) );
		return ( $query->num_rows > 0 ) ? true : false;
	}

	function updateSpecificAddres( $array ) {
		$sql = 'UPDATE prime_customer_accounts_owners_address SET city = ? WHERE sysid = ? and status = ?';
		$query = $this->db->query( $sql, array( $array[ 'newValue' ], $array[ 'identifier' ], 1 ) );
		return ( $query->num_rows > 0 ) ? true : false;
	}

	function update_equipment_data() {
		//Review this code. Updating data can be easily made by using AJAX.
		$rate_class_id = $data[ 'rateClass' ];
		$inspection_date = $data[ 'inspDate' ];
		$account_type = $data[ 'accountType' ];
		$district = $data[ 'district' ];
		$city = $data[ 'city' ];
		$specific_address = $data[ 'specificAddress' ];
		$trn = $data[ 'trn' ];
		$x = $data[ 'latitude' ];
		$y = $data[ 'longitude' ];
		$accountid = $data[ 'accountID' ];
		$address_id = $data[ 'addressID' ];

		$total_load = 0;

		//query strings start
		$sql_eq_values = "SELECT watts, qty FROM prime_customer_accounts_equipments WHERE accountid = ? AND status = ?";
		$sql_ao = "SELECT sysid FROM prime_customer_accounts_owners WHERE accountid = ?";
		$sql_oa = "UPDATE prime_customer_accounts_owners_address SET ownerid = ?, district = ?, city = ?, country = ?, addrspecific = ?, addrmapx = ?, addrmapy = ? WHERE sysid = ? AND status = ?";
		$sql_am = "UPDATE prime_customer_accounts_main SET types=? WHERE sysid = ?";
		$sql_om_up = "UPDATE prime_customer_accounts_owners_meter SET accountid = ?, ownerid = ?, rateid = ?, createdby = ?, status = ?";
		$sql_om = "SELECT sysid FROM prime_customer_accounts_owners_meter WHERE accountid = ?";
		$sql_al = "UPDATE prime_customer_accounts_logs SET logtype = ?, logamount = ?, meterid = ?, logdate = ?, createdby = ?, status = ? WHERE accountid = ?";
		$sql_rm = "UPDATE transaction_request_main SET status = ?, stagesid = ? WHERE trncode = ?";
		//query strings end
		$ownerid = $this->db->query( $sql_ao, array( $accountid ) )->row()->sysid;
		$eq_values = $this->db->query( $sql_eq_values, array( $accountid, 1 ) )->result();
		foreach ( $eq_values as $val ) {
			$total_load += $val->watts * $val->qty;
		}
		$row = $this->retrieve_data( $rate_class_id );
		$deposit_cost = $this->deposit_cost( $total_load, $row->dailyops, $row->monthlyops, $row->demand, $row->rates );
		$this->db->trans_start();
		//using transactions means that all the queries must be successful before committing the data to database.
		//query bindings automatically escapes mysql data, no need for manually escaping data before querying
		//no need to specify that the data as string (e.g. '$specific_address') during the query.
		$this->db->query( $sql_oa, array( $ownerid, $district, $city, 175, $specific_address, $x, $y, $address_id, 1 ) );
		$this->db->query( $sql_am, array( $account_type, $accountid ) );
		$this->db->query( $sql_om_up, array( $accountid, $ownerid, $rate_class_id, 1, 1 ) );
		$this->db->query( $sql_al, array( 12, round( $deposit_cost, 2 ), $this->db->query( $sql_om, array( $accountid ) )->row()->sysid, $inspection_date, 1, 1, $accountid ) );
		$this->db->query( $sql_rm, array( 1, 4, $trn ) );
		return $this->db->trans_complete();
	}

	function rate_class() {
		$query = $this->db->query( "SELECT sysid, classifications FROM prime_system_rate_class_main" );
		return ( $query ) ? $query->result() : false;
	}

	function get_rate_class_name( $sysid ) {
		$sql = "SELECT classifications name FROM prime_system_rate_class_main WHERE sysid = ?";
		$query = $this->db->query( $sql, array( $sysid ) );
		return ( $query ) ? $query->row() : false;
	}

	function retrieve_rate_data( $data ) {
		//retrieves the data accompanied by rates to be used in view computation.
		$dep_cost_const_sql = "SELECT dailyops, monthlyops, demand, rates FROM prime_system_rate_class_main_quantity mq "
			. "LEFT JOIN prime_system_rate_class_main_rates mr ON mq.rateid = mr.rateid "
		. "WHERE mq.rateid = ? AND mr.month = (SELECT MAX(month) FROM prime_system_rate_class_main_rates WHERE rateid = ? AND year = (SELECT MAX(year) FROM prime_system_rate_class_main_rates WHERE rateid = ?))";
		$query = $this->db->query( $dep_cost_const_sql, array( $data, $data, $data ) );
		return ( $query ) ? $query->row() : false;
	}

	function total_load( $power, $qty ) {
		return $power * $qty;
	}

	function deposit_cost( $total_load, $daily_ops, $monthlyops, $demand, $rates ) {
		return ( $total_load * $daily_ops * $monthlyops * $demand * $rates ) / 1000;
	}

	function add_equipments( $accountid, $code, $power, $qty ) {
		$sql_ae = "INSERT INTO customer_accounts_equipments (accountid, equipid, watts, qty) VALUES (?, ?, ?, ?)";
		$this->db->query( $sql_ae, array( $accountid, $code, $power, $qty ) );
		return ( $this->db->affected_rows() != 1 ) ? false : true;
	}

	function change_equipment_status( $sysid ) {
		$sql_ae = "UPDATE customer_accounts_equipments SET status = ? WHERE sysid = ?";
		$this->db->query( $sql_ae, array( 0, $sysid ) );
		return ( $this->db->affected_rows() != 1 ) ? false : true;
	}

	function delete_equipment( $sysid ) {
		$sql_ae = "DELETE FROM customer_accounts_equipments WHERE sysid = $sysid";
		$this->db->query( $sql_ae, array( $sysid ) );
		return ( $this->db->affected_rows() != 1 ) ? false : true;
	}
        
}