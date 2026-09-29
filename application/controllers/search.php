<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Search extends CI_Controller {

    private $user_login;

    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_tellering');
        $this->load->model('model_search', 'search', true);
        $this->user_login = user_login();
    }

    public function index() {
        if ($this->user_login) {
            $data['userdata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['profiledata'] = $this->model_admin->get_user_login_info($this->user_login['system_user_sessid']);
            $data['usersmodule'] = $this->model_admin->select_modules();
            init_header($data);
            $search_type = $this->input->post('searchtype');

            if($search_type==1) {
                $servno = $this->input->post('keyword');
                $qry_acct = $this->db->select()->from('customer_accounts_main')->where('servicenumber', $servno)->get()->row();
                $data['dataid'] = ($qry_acct) ? $qry_acct->sysid : false;
                $this->load->view('admin/pages/modules/acctinfo/data', $data);
            }else{
                $this->load->view('redirects/search/basic', $data);
            }
            init_footer($data, '');
        }
    }


    function personsearch() {
        echo $this->search->search_person();
    }

    function corpsearch() {
        echo $this->search->search_corporation();
    }

    function govsearch() {
        echo $this->search->search_government();
    }

    function accountsearch() {
        echo $this->search->search_account();
    }

    function itemsearch() {
        echo $this->search->search_item();
    }

    function servicesearch() {
        echo $this->search->search_service();
    }

    function itemcomponentsearch($key = false, $cat = false) {
        echo $this->search->search_item_component($key, $cat);
    }

    function itemcategory() {
        echo $this->search->search_item_category();
    }

    function metersearch() {
        echo $this->search->search_meter();
    }

    function getmeterinfo() {
        echo $this->search->get_meter_info();
    }

    function employeesearch() {
        echo $this->search->search_employee();
    }

    function employeesearchid() {
        echo $this->search->search_employee_id();
    }


    function customerapplication() {
        echo $this->search->search_customer_application();
    }

    function mrdfindings() {
        echo $this->search->search_mrd_findings();
    }


    function testing() {
        $due_amt = compute_billing(85512, 2018, 7, 5, 0, 0);
        echo '<pre>';
        print_r($due_amt);
    }

    function general() {
        $keyword = $this->input->post('key');
        $data = array();
        $html = '';
        $resnum = 0;
        $boxnum = 0;
        $html .= '<div class="row"><button id="search_btn_close" type="button" class="btn btn-danger btn-xs pull-right" style="margin-right: 15px; margin-top: -30px;"><i class="fa fa-times"></i></button>';


        $qry_cust_accnt = $this->db->select('ao.sysid AS ownersid, am.status, am.servicenumber, am.datecreated, ao.ownerid, ao.ownertype')
                ->from('customer_accounts_main AS am')
                ->join('customer_accounts_owners AS ao', 'ao.accountid = am.sysid')
                ->where('am.servicenumber', $keyword)
                ->get()->row();
        if($qry_cust_accnt) {
            $boxnum += 1;
            $resnum = 1;
            if($qry_cust_accnt->ownertype == 1) {
                $qry_acct_info = $this->db->select()
                ->from('person AS p')
                ->where('p.sysid', $qry_cust_accnt->ownerid)
                ->get()->row();
                $owner_name = $qry_acct_info->lastname.', '.$qry_acct_info->firstname;
                $qry_owner_addr = $this->db->select()->from('person_address_matrix')->where('personid', $qry_acct_info->sysid)->get()->row();
                if($qry_owner_addr) {
                    $owner_addr = $qry_owner_addr->addrspec;
                } else {
                    $owner_addr = 'N/A';
                }
            }
            if($qry_cust_accnt->ownertype == 2) {
                $owner_name = '';
            }

            if($qry_cust_accnt->ownertype == 5) {
                $qry_owner_legacy = $this->db->select()->from('customer_accounts_name_legacy')
                    ->where('sysid', $qry_cust_accnt->ownerid)
                    ->limit(2)
                    ->get()->row();
                if($qry_owner_legacy) {
                    $owner_name = $qry_owner_legacy->name;
                    $qry_owner_addr = $this->db->select()->from('customer_accounts_address')->where('acctid', $qry_cust_accnt->ownersid)->get()->row();
                    if($qry_owner_addr) {
                        $owner_addr = $qry_owner_addr->addrspecific;
                    }else{
                        $owner_addr = '';
                    }
                }else{
                    $owner_name = '';
                    $owner_addr = '';
                }
            }
            $acct_stat = ($qry_cust_accnt->status == 1) ? 'fa-check' : 'fa-times';
            $acct_stat_color = ($qry_cust_accnt->status == 1) ? 'green' : 'red';
            $html .= '<div class="col-md-12">
                            <div class="top-news">
                                <a href="javascript:;" class="btn '.$acct_stat_color.'">
                                <span>
                                <i class="fa fa-tag"></i> <b>'.$qry_cust_accnt->servicenumber.'</b> - '.$owner_name.'</span>
                                <em>
                                <i class="fa fa-calendar"></i>
                                '.$qry_cust_accnt->datecreated.'</em>
                                <em>
                                <i class="fa fa-tags"></i>
                                '.$owner_addr.'</em>
                                <i class="fa '.$acct_stat.' top-news-icon"></i>
                                </a>
                            </div>
                        </div>';
        }


        $qry_cust_person = $this->db->select()
                ->from('person AS p')
                ->or_like('p.firstname', $keyword)
                ->or_like('p.lastname', $keyword)
                ->get();
        $num_cust_person = $qry_cust_person->num_rows();

         if($num_cust_person>0) {
            $boxnum += 1;
            $resnum += $num_cust_person;
            if($num_cust_person>1) {
                $html .= '<div class="col-md-12">
                                <div class="top-news search_res_box">
                                    <a href="javascript:;" class="btn blue sub" >
                                    <span>Customer Individuals</span>
                                    <em>There are '.$num_cust_person.' person found!</em>
                                    <i class="fa fa-users top-news-icon"></i>
                                    </a>
                                </div>
                            </div>';
            }else{
                $html .= '<div class="col-md-12">
                                <div class="top-news">
                                    <a href="javascript:;" class="btn red">
                                    <span>Lucky John Faderon</span>
                                    <em>West Timawa Molo, Iloilo City</em>
                                    <i class="fa fa-user top-news-icon"></i>
                                    </a>
                                </div>
                            </div>';
            }
        }

        $qry_cust_corp = $this->db->select()
                ->from('corporation AS c')
                ->or_like('c.codes', $keyword)
                ->or_like('c.descs', $keyword)
                ->get();
        $num_cust_corp = $qry_cust_corp->num_rows();

        if($num_cust_corp>0) {
            $boxnum += 1;
            $resnum += $num_cust_corp;
            if($num_cust_person>1) {
                $html .= '<div class="col-md-12">
                                <div class="top-news search_res_box"">
                                    <a href="javascript:;" class="btn green sub">
                                    <span>Customer Corporations</span>
                                    <em>There are '.$num_cust_corp.' corporation found!</em>
                                    <i class="fa fa-building top-news-icon"></i>
                                    </a>
                                </div>
                            </div>';
            }else{
                $html .= '<div class="col-md-12">
                                <div class="top-news">
                                    <a href="javascript:;" class="btn green">
                                    <span>Robinsons Place</span>
                                    <em>Iloilo City</em>
                                    <i class="fa fa-home top-news-icon"></i>
                                    </a>
                                </div>
                            </div>';
            }
        }

        /*
        $html .= '
									<div class="col-md-12">
										<div class="top-news">
											<a href="javascript:;" class="btn red">
											<span>
											Metronic News </span>
											<em>Posted on: April 16, 2013</em>
											<em>
											<i class="fa fa-tags"></i>
											Money, Business, Google </em>
											<i class="fa fa-briefcase top-news-icon"></i>
											</a>
										</div>
									</div>
									<div class="col-md-12">
										<div class="top-news">
											<a href="javascript:;" class="btn green">
											<span>
											Top Week </span>
											<em>Posted on: April 15, 2013</em>
											<em>
											<i class="fa fa-tags"></i>
											Internet, Music, People </em>
											<i class="fa fa-music top-news-icon"></i>
											</a>
										</div>
									</div>
									<div class="col-md-12">
										<div class="top-news">
											<a href="javascript:;" class="btn blue">
											<span>
											Gold Price Falls </span>
											<em>Posted on: April 14, 2013</em>
											<em>
											<i class="fa fa-tags"></i>
											USA, Business, Apple </em>
											<i class="fa fa-globe top-news-icon"></i>
											</a>
										</div>
									</div>
									<div class="col-md-12">
										<div class="top-news">
											<a href="javascript:;" class="btn yellow">
											<span>
											Study Abroad </span>
											<em>Posted on: April 13, 2013</em>
											<em>
											<i class="fa fa-tags"></i>
											Education, Students, Canada </em>
											<i class="fa fa-book top-news-icon"></i>
											</a>
										</div>
									</div>';
         *
         */
		$html .= '</div>';
        $data['keyword'] = $keyword;
        $data['resnum'] = $resnum;
        $data['html'] = $html;
        echo json_encode($data);
    }

    function searchmtrasset() {
        $q = $this->input->post('q');
        $qry = $this->db->select()->from('assets_main')->like('serialcodes', $q)->get();
        $res = array();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $res[] = array('id' => $row->sysid, 'text' => $row->desc . ' - ' . $row->serialcodes);
            }
        }
        echo json_encode($res);
    }

    function meterasset() {
        $assetid = $this->input->post('asset_id');
        $query = $this->db->select('pib.codes as brand, pib.descs as bname, am.desc as amdesc, am.serialcodes as sc')->from('assets_main am')
                        ->join('assets_main_details as amd', 'am.sysid = amd.assetid', 'left')
                        ->join('prime_items_brands as pib', 'pib.sysid = amd.brandid', 'left')
                        ->where('am.sysid', $assetid)
                        ->get()->row();
        $data = array(

            'brand' => $query->brand,
            'amdesc' => $query->amdesc,
            'serialcodes' => $query->sc,
            'desc' => $query->bname
        );
        echo json_encode($data);
    }

    function submitasset() {
        $assetid = $this->input->post('asset_id');
        $dataid = $this->input->post('dataid');
        $userid = user_session()->system_user_sessid;
        $qryacct = $this->db->select('ownertype')
                        ->from('customer_accounts_owners')
                        ->where('accountid', $dataid)->get()->row();
        if ($qryacct->ownertype == 1) {
            $ownertype = 91;
        } else if ($qryacct->ownertype == 2) {
            $ownertype = 92;
        } else {
            $ownertype = 93;
        }

        $searchasset = $this->db->select()->from('assets_main_owner_history')->where('assetid', $assetid)->get();
        if ($searchasset->num_rows() > 0) {
            foreach ($searchasset->result() as $row) {
                $this->db->where('assetid', $row->sysid);
                $this->db->update('assets_main_owner_history', array('status' => 0));
            }
        }
        $updateasset = $this->db->select()->from('assets_main_owner_history')->where(array('ownerid' => $dataid, 'ownertype' => $ownertype, 'status' => 1))->get();
        if ($updateasset->num_rows() > 0) {
            foreach ($updateasset->result() as $row) {
                $this->db->where(array('sysid' => $row->sysid, 'status' => 1));
                $this->db->set('dateupdated', 'NOW()', false);
                $this->db->update('assets_main_owner_history', array('status' => 0, 'updatedby' => $userid));
            }
        }
        $dataasset = array('assetid' => $assetid,
            'ownerid' => $dataid,
            'ownertype' => $ownertype,
            'status' => 1,
            );
        $this->db->set('datecreated', 'NOW()', false);
        $insertasset = $this->db->insert('assets_main_owner_history', $dataasset);
        $gldbid = $this->db->select()->from('customer_accounts_glb')->where('accountid', $dataid)->get()->row();
        $assetsysid = $this->db->insert_id();
        if ($insertasset && $gldbid) {
            $this->db->set('datecreated', 'NOW()', false);
            $this->db->insert('customer_accounts_subscription_meter', array('assetid' => $assetsysid, 'glbid' => $gldbid->gdlbid, 'mtr' => 1, 'createdby' => user_session()->system_user_sessid, 'status' => 1));
        }
        $dataasset['ret'] = ($insertasset) ? true : false;
        $ownership = $this->db->select('names')->from('prime_types_parameter')->where('sysid', $ownertype)->get()->row();
        $dataasset['ownership'] = $ownership->names;
        echo json_encode($dataasset);
    }

    function loadasset() {
        $dataid = $this->input->post('dataid');
        $qry = $this->input->post('query');
		$query = false;
        if($qry=='init'){
        	$query = $this->db->select('am.assetid, param.names AS param, b.codes AS brand, d.codes AS amps, d.descs AS volts')
                        ->select("CONCAT(m.desc, ' - ', m.serialcodes) AS codes", false)
                        ->from('assets_main_owner_history as am')
                        ->join('assets_main AS m', 'am.assetid = m.sysid', 'left')
                        ->join('assets_main_details AS d', 'm.sysid = d.assetid', 'left')
                        ->join('prime_items_brands AS b', 'd.brandid = b.sysid', 'left')
						->join('prime_types_parameter AS param', 'm.types = param.sysid', 'left')
                        ->where(array('am.ownerid' => $dataid, 'am.status' => 1))->get()->row();
		}

        if($qry=='select'){
            $query = $this->db->select('am.sysid as assetid, param.names AS param, pib.codes as brand, amd.codes as amps, amd.descs as volts')
                        ->select("CONCAT(am.desc, ' - ', am.serialcodes) as codes", false)
                        ->from('assets_main am')
                        ->join('assets_main_details as amd', 'am.sysid = amd.assetid', 'left')
                        ->join('prime_items_brands as pib', 'pib.sysid = amd.brandid', 'left')
						->join('prime_types_parameter AS param', 'am.types = param.sysid', 'left')
                        ->where(array('am.sysid' => $dataid, 'am.status' => 1))->get()->row();
        }

        $data = array();
        if ($query) {
            $data = array(
                'id' => $query->assetid,
                'text' => $query->codes,
                'assetcode' => $query->param.str_pad($query->assetid, 6, '0', STR_PAD_LEFT),
                'brand' => $query->brand,
                'volts' => $query->volts,
                'amps' => $query->amps,
				'desc' => $query->codes,
            );
        }
		$data['input'] = $this->input->post();
        echo json_encode($data);
    }


    function customersgeolocation() {
        $data = array();
        $input_arr = $this->input->post();
        $acctid = $this->input->post('acctid');
        $type = $this->input->post('type');
        $acctid_arr = explode(',', $acctid);

        $qry_geo = $this->db->select("
                g.lat,
                g.lon,
                m.servicenumber AS servno,
                m.ownerid,
                m.types,
                a.addrspecific AS addr
            ")
            ->from('customer_accounts_geodata AS g')
            ->join('customer_accounts_main AS m', 'm.sysid = g.acctid', 'left')
            ->join('customer_accounts_address AS a', 'a.acctid = g.acctid', 'left')
            ->where_in('g.acctid', $acctid_arr)
            ->where('g.type', $type)
            ->get();
        if($qry_geo->num_rows()>0) {
            foreach($qry_geo->result() as $row) {
                $pic = base_url('assets/global/img/person_default.jpg');
                $name = '';
                if($row->types==5) {
                    $qry_legacy = $this->db->select("name")
                        ->from('customer_accounts_name_legacy')
                        ->where("sysid", $row->ownerid)
                        ->get()->row();
                    if($qry_legacy) {
                        $name = $qry_legacy->name;
                    }
                }

                $content = '
                    <div style="width: 300px; font-size: 11px;">
                    <div style="width: 70px; display: inline-block;float: left">
                    <img src="'.$pic.'" width="100%" style="margin-top: 5px;" />
                    </div>
                    <div style="width: 230px; display: inline-block;">
                    <ul class="list-group summary column no-border">
                    <li class="list-group-item">
                    <span class="col-md-4 label-name">Servno.</span>
                    <span class="col-md-8 label-default" style="font-size: 11px !important;;">'.$row->servno.'</span>
                    </li>
                    <li class="list-group-item">
                    <span class="col-md-4 label-name">Name</span>
                    <span class="col-md-8 label-default" style="font-size: 11px !important;;">'.$name.'</span>
                    </li>
                    <li class="list-group-item">
                    <span class="col-md-4 label-name">Address</span>
                    <span class="col-md-8 label-default" style="font-size: 11px !important;">'.$row->addr.'</span>
                    </li>
                    </ul>                    
                    </div>            
                    </div>
                ';

                $data['geo'][] = array(
                    'lat' => $row->lat,
                    'lon' => $row->lon,
                    'servno' => $row->servno,
                    'content' => $content
                );
            }
        }

        $data['inputs'] = $input_arr;
        echo json_encode($data);
    }


    function suppliers() {
        echo $this->search->search_suppliers();
    }
}
