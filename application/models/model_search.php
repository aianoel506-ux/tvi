<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 7/3/2018
 * Time: 4:37 PM
 */

class Model_search extends CI_Model
{

    function search_person() {
        $query = $this->input->get('query');
        $results = array();

        $landmarkarr = array();
        if(strlen($query) > 3){
            $query_tickets = $this->db->select('
        p.sysid AS personid,
        p.lastname,
        p.firstname,
        p.middlename
        ')
                ->from('person AS p')
                ->like("p.lastname", $query)
                ->or_like("p.firstname", $query)
                ->group_by('
                    p.sysid,
                    p.lastname,
                    p.firstname,
                    p.middlename
            ')
                ->get();
            if($query_tickets->num_rows() > 0) {
                foreach($query_tickets->result() as $row) {
                    $qry_select_complaints_data = $this->db->select(
                        '
                        dl.address,
                        dl.contact,
                        dl.district AS distid,
                        dl.barangays AS brgyid,
                        dl.landmarks AS landmarkid,
                        d.descriptions as dist
                        '
                    )
                        ->from('ticketing_details_logs AS dl')
                        ->join('address_districts as d', 'd.sysid = dl.district','left')
                        ->where(array('dl.complainants' => $row->personid))
                        ->get()->row();

                    if($qry_select_complaints_data) {
                        $districtid = $qry_select_complaints_data->distid;
                        $districtname = $qry_select_complaints_data->dist;
                        $addressspec = $qry_select_complaints_data->address;
                        $brgyid = $qry_select_complaints_data->brgyid;
                        $contactstring = $qry_select_complaints_data->contact;
                        $landmarkid = $qry_select_complaints_data->landmarkid;
                        $landmarkarr_arr = explode(',', $landmarkid);
                        if($landmarkarr_arr) {
                            $qry_landmarkarr_arr = $this->db->select('sysid, texts')->from('address_landmark')
                                ->where_in('sysid', $landmarkarr_arr)->get();
                            if($qry_landmarkarr_arr->num_rows() > 0) {
                                foreach($qry_landmarkarr_arr->result() as $landrow) {
                                    $landmarkarr[] = array(
                                        'id'=> $landrow->sysid,
                                        'text' => $landrow->texts
                                    );
                                }
                            }
                        }
                    } else {
                        $qry_person_address = $this->db->select(
                            'am.addrdist, d.descriptions, am.addrspec, am.addrbrgy'
                        )
                            ->from('person_address_matrix AS am')
                            ->join('address_districts AS d', 'am.addrdist = d.sysid', 'left')
                            ->where(array('am.personid' => $row->personid))
                            ->order_by('datecreated', 'desc')
                            ->get()->row();

                        $qry_contacts = $this->db->select('contactstring')
                            ->from('person_contact_matrix')
                            ->where(array('personid' => $row->personid))
                            ->get()->row();
                        if($qry_contacts) {
                            $contactstring = $qry_contacts->contactstring;
                        }else{
                            $contactstring = '';
                        }

                        if($qry_person_address) {
                            $districtid = $qry_person_address->addrdist;
                            $districtname = $qry_person_address->descriptions;
                            $addressspec = $qry_person_address->addrspec;
                            $brgyid = $qry_person_address->addrbrgy;
                        }else{
                            $districtid = '';
                            $districtname = '';
                            $addressspec = '';
                            $brgyid = '';
                        }

                        $landmarkid = false;
                    }


                    $middlename_txt = (trim($row->middlename) != '') ? '-'.strtolower(str_replace(' ', '_', $row->middlename)) : '';
                    $firstname_txt = strtolower(str_replace(' ', '_', $row->firstname));
                    $lastname_txt = strtolower(str_replace(' ', '_', $row->lastname));
                    $url =  $firstname_txt.$middlename_txt.'-'.$lastname_txt;
                    $pic_info = get_owner_pic($row->personid, 'person');
                    $results[] = array(
                        "sysid" => $row->personid,
                        "lastname" => $row->lastname,
                        "url" => $url,
                        "firstname" => $row->firstname,
                        "middlename" => $row->middlename,
                        "addr" => $addressspec,
                        "district" => $districtname,
                        "contact" => $contactstring,
                        "img" => $pic_info,
                        "distid" => $districtid,
                        "brgyid" => $brgyid,
                        "landmarkid" => $landmarkid,
                        "tokens" => array($query, $query . rand(1, 10)),
                        "landarr" => $landmarkarr,
                    );

                }
            }
        }

        echo json_encode($results);
    }

    function search_corporation() {
        $query = $this->input->get('query');
        // $query = 'ROB';
        $results = array();
        $qry = $this->db->select('c.sysid, c.codes, c.descs as corpname, cb.names as corpbranc, cb.address AS corpaddr')
            ->from('corporation AS c')
            ->join('corporation_branches AS cb', 'cb.corpid = c.sysid', 'left')
            ->or_like('c.codes', $query)
            ->or_like('c.descs', $query)
            ->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $info = get_corporation_info($row->sysid);
                $map = directory_map('./uploads/corporation/'.$row->sysid.'/', FALSE, TRUE);
                $pic_recent = ($map && count($map)>0) ? base_url('uploads/corporation/'.$row->sysid.'/' . $map[0]) : base_url('assets/global/img/not-available.png');

                $repfname = '';
                $replname = '';
                $repmname = '';
                if($info->qry) {
                    $repfname = $info->info->repfname;
                    $replname = $info->info->replname;
                    $repmname = $info->info->repmname;
                }

                $results[] = array(
                    'corpname' => $row->corpname,
                    'corpbranch' => ($row->corpbranc != '') ? $row->corpbranc : 'N/A',
                    'corpaddr' => ($row->corpaddr != '') ? $row->corpaddr : 'N/A',
                    'repfname' => $repfname,
                    'replname' => $replname,
                    'repmname' => $repmname,
                    'img' => $pic_recent,
                    'info' => $info,
                );
            }
        }
        return json_encode($results);
    }

    function search_government() {
        $query = $this->input->get('query');
        // $query = 'ROB';
        $results = array();
        $qry = $this->db->select('c.sysid, c.codes, c.descs as govname, cb.names as govbname, cb.address AS govaddr')
            ->from('government_main AS c')
            ->join('government_main_branches AS cb', 'cb.govid = c.sysid', 'left')
            ->or_like('c.codes', $query)
            ->or_like('c.descs', $query)
            ->get();
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                // $info = get_corporation_info($row->sysid);
                $info = false;
                $map = directory_map('./uploads/government/'.$row->sysid.'/', FALSE, TRUE);
                $pic_recent = ($map && count($map)>0) ? base_url('uploads/government/'.$row->sysid.'/' . $map[0]) : base_url('assets/global/img/not-available.png');

                $repfname = '';
                $replname = '';
                $repmname = '';
                if($info && $info->qry) {
                    $repfname = $info->info->repfname;
                    $replname = $info->info->replname;
                    $repmname = $info->info->repmname;
                }

                $results[] = array(
                    'govname' => $row->govname,
                    'govbranch' => ($row->govbname != '') ? $row->govbname : 'N/A',
                    'govaddr' => ($row->govaddr != '') ? $row->govaddr : 'N/A',
                    'repfname' => $repfname,
                    'replname' => $replname,
                    'repmname' => $repmname,
                    'img' => $pic_recent,
                    'info' => $info,
                );
            }
        }
        return json_encode($results);
    }


    function search_account() {

        $q = $this->input->get('query');

        $results = array();

        if(strlen($q) > 4) {
            $qry = $this->db->select('
                m.sysid,
                m.servicenumber AS servno,
                a.addrspecific AS addr,
                m.types,
                m.ownerid
            ')
                ->from('customer_accounts_main AS m')
                ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
                ->or_like('m.servicenumber', $q)
                ->get();

            $res = array();
            if ($qry->num_rows() > 0) {
                foreach ($qry->result() as $row) {

                    $pic = base_url('assets/global/img/person_default.jpg');
                    $name = '@TODO';
                    if ($row->types == 5) {
                        $qry_legacy = $this->db->select("name")
                            ->from('customer_accounts_name_legacy')
                            ->where("sysid", $row->ownerid)
                            ->get()->row();
                        if ($qry_legacy) {
                            $name = $qry_legacy->name;
                        }
                    }
                    $results[] = array(
                        'id' => $row->sysid,
                        'text' => $row->servno,
                        'name' => $name,
                        'addr' => $row->addr,
                        'pics' => $pic,
                    );
                }
            } else {
                $qry = $this->db->select('
                m.sysid, 
                m.servicenumber AS servno, 
                l.name,
                a.addrspecific AS addr
                ')
                    ->from('customer_accounts_name_legacy AS l')
                    ->join('customer_accounts_main AS m', 'm.ownerid = l.sysid', 'left')
                    ->join('customer_accounts_address AS a', 'a.acctid = m.sysid', 'left')
                    ->or_like('l.name', $q)
                    ->where('m.types', 5)
                    ->get();

                if ($qry->num_rows() > 0) {
                    foreach ($qry->result() as $row) {

                        $pic = base_url('assets/global/img/person_default.jpg');
                        $results[] = array(
                            'id' => $row->sysid,
                            'text' => $row->servno,
                            'name' => $row->name,
                            'addr' => $row->addr,
                            'pics' => $pic,
                        );
                    }
                }
            }
        }

        echo json_encode($results);
    }

    function search_item() {
        $data = array();
        $query = $this->input->get('query');
        $qry = $this->db->query("
            SELECT ms.sysid, m.codes, ms.descs 
            FROM items_main_spec AS ms
            LEFT JOIN items_main_category AS m ON m.sysid = ms.itemid
            WHERE (ms.names LIKE '%$query%' OR ms.descs LIKE '%$query%' OR m.codes LIKE '%$query%' OR m.codes LIKE '%$query%' OR m.desc LIKE '%$query%')
            -- (m.`types` = 2 AND ms.`status` = 1) 
        ");
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {

                $img = base_url() . 'assets/global/img/not-available.png';
                $item_info = get_item_info($row->sysid);
                $amt = 0;
                $dte = 'Unknown';
                $quoteid = 'Unknown';

                if ($item_info) {
                    $amt = $item_info->amt;
                    $dte = $item_info->dateapproved;
                    $quoteid = $item_info->quoteid;
                }

                $data[] = array(
                    'id' => $row->sysid,
                    'img' => $img,
                    'code' => ($row->codes != null) ? $row->codes : 'Not specified',
                    'desc' => $row->descs,
                    'date' => $dte,
                    'amts' => $amt,
                    'amts_text' => number_format($amt, 2),
                    'quoteid' => $quoteid,
                    'supp' => '',
                );
            }
        }
        return json_encode($data);
    }

    function search_service() {
        $data = array();
        $query = $this->input->get('query');
        $qry = $this->db->query("
            SELECT sm.sysid, sm.codes, sm.names 
            FROM prime_services_main AS sm
            WHERE (sm.names LIKE '%$query%' OR sm.names LIKE '%$query%' OR sm.codes LIKE '%$query%' OR sm.codes LIKE '%$query%' OR sm.desc LIKE '%$query%')
            -- (m.`types` = 2 AND ms.`status` = 1) 
        ");
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {

                $img = base_url() . 'assets/global/img/services-default.png';
                $svcs_info = get_service_info($row->sysid);
                $amt = 0;
                $dte = 'Unknown';
                $quoteid = 'Unknown';

                if ($svcs_info) {
                    $amt = $svcs_info->servicerate;
                    $dte = date_format(date_create($svcs_info->datecreated),"F j, Y");
                }

                $data[] = array(
                    'id' => $row->sysid,
                    'img' => $img,
                    'code' => ($row->codes != null) ? $row->codes : 'Not specified',
                    'desc' => $row->names,
                    'date' => $dte,
                    'amts' => $amt,
                    'amts_text' => number_format($amt, 2),
                );
            }
        }
        return json_encode($data);
    }

    function search_employee_id() {
        $res = array();
        $query = $this->input->get('query');
        if($query) {
            $sql = $this->db->query("
                    SELECT
                        e.sysid AS sysid,
                        p.sysid AS personid,
                        u.sysid AS userid,
                        p.lastname,
                        p.firstname,
                        p.middlename,
                        e.empid
                    FROM person AS p
                        JOIN prime_employee_main AS e ON e.personid = p.sysid AND e.status = 1
                        LEFT JOIN prime_employee_costcenter AS c ON c.empid = e.sysid AND c.status = 1 
                        LEFT JOIN prime_system_users AS u ON u.personid = p.sysid  
                    WHERE 
                        e.empid LIKE '%$query%' OR p.lastname LIKE '%$query%'
                    GROUP BY e.sysid,p.sysid,u.sysid, p.lastname,p.firstname,p.middlename
                ");
            if ($sql->num_rows() > 0) {
                foreach ($sql->result() as $row) {
                    $img = $user_pic_url = get_owner_pic($row->personid, 'person');
                    $emp_info =get_employee_info($row->sysid);
                    $position = ($emp_info->qry) ? $emp_info->position: 'N/A';
                    $department = ($emp_info->qry) ? $emp_info->deptdesc: 'N/A';
                    $res[] = array(
                        'name' => $row->lastname . ', ' . $row->firstname,
                        'department' => $department,
                        'position' => $position,
                        'empid' => $row->empid,
                        'img' => $img
                    );
                }
            }
        }
        return json_encode($res);
    }

    function search_employee() {
        $query = $this->input->get('query');
        $dept = $this->input->get('dept');
        $results = array();

        $landmarkarr = array();

        if($dept && $dept > 0) {
            $sql = "
                    SELECT
                        e.sysid AS sysid,
                        p.sysid AS personid,
                        u.sysid AS userid,
                        p.lastname,
                        p.firstname,
                        p.middlename,
                        ulc.telcode
                    FROM person AS p
                        JOIN prime_employee_main AS e ON e.personid = p.sysid AND e.status = 1
                        JOIN prime_employee_costcenter AS c ON c.empid = e.sysid AND c.status = 1 
                        LEFT JOIN prime_system_users AS u ON u.personid = p.sysid  
                        LEFT JOIN prime_system_users_legacy_code AS ulc ON ulc.userid = u.sysid  
                    WHERE 
                        (c.ccid = $dept AND p.lastname LIKE '%$query%') OR 
                        (c.ccid = $dept  AND p.firstname LIKE '%$$query%')
                    GROUP BY e.sysid,p.sysid,p.lastname,p.firstname,p.middlename
                ";
        }else{
            $sql = "
                    SELECT
                        e.sysid AS sysid,
                        p.sysid AS personid,
                        u.sysid AS userid,
                        p.lastname,
                        p.firstname,
                        p.middlename,
                        ulc.telcode
                    FROM person AS p
                        JOIN prime_employee_main AS e ON e.personid = p.sysid  AND e.STATUS = 1
                        JOIN prime_employee_costcenter AS c ON c.empid = e.sysid  AND c.STATUS = 1 
                        LEFT JOIN prime_system_users AS u ON u.personid = p.sysid  
                        LEFT JOIN prime_system_users_legacy_code AS ulc ON ulc.userid = u.sysid  
                        WHERE  p.lastname LIKE '%$query%' OR p.firstname LIKE '%$query%'
                    GROUP BY e.sysid,p.sysid,p.lastname,p.firstname,p.middlename
                ";
        }
        $query_tickets = $this->db->query($sql);
        if($query_tickets->num_rows() > 0) {
            foreach($query_tickets->result() as $row) {
                $qry_select_complaints_data = $this->db->select(
                    '
                        dl.address,
                        dl.contact,
                        dl.district AS distid,
                        dl.barangays AS brgyid,
                        dl.landmarks AS landmarkid,
                        d.descriptions as dist
                        '
                )
                    ->from('ticketing_details_logs AS dl')
                    ->join('address_districts as d', 'd.sysid = dl.district','left')
                    ->where(array('dl.complainants' => $row->personid))
                    ->get()->row();

                if($qry_select_complaints_data) {
                    $districtid = $qry_select_complaints_data->distid;
                    $districtname = $qry_select_complaints_data->dist;
                    $addressspec = $qry_select_complaints_data->address;
                    $brgyid = $qry_select_complaints_data->brgyid;
                    $contactstring = $qry_select_complaints_data->contact;
                    $landmarkid = $qry_select_complaints_data->landmarkid;
                    $landmarkarr_arr = explode(',', $landmarkid);
                    if($landmarkarr_arr) {
                        $qry_landmarkarr_arr = $this->db->select('sysid, texts')->from('address_landmark')
                            ->where_in('sysid', $landmarkarr_arr)->get();
                        if($qry_landmarkarr_arr->num_rows() > 0) {
                            foreach($qry_landmarkarr_arr->result() as $landrow) {
                                $landmarkarr[] = array(
                                    'id'=> $landrow->sysid,
                                    'text' => $landrow->texts
                                );
                            }
                        }
                    }
                } else {
                    $qry_person_address = $this->db->select(
                        'am.addrdist, d.descriptions, am.addrspec, am.addrbrgy'
                    )
                        ->from('person_address_matrix AS am')
                        ->join('address_districts AS d', 'am.addrdist = d.sysid', 'left')
                        ->where(array('am.personid' => $row->personid))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();

                    $qry_contacts = $this->db->select('contactstring')
                        ->from('person_contact_matrix')
                        ->where(array('personid' => $row->personid))
                        ->get()->row();
                    if($qry_contacts) {
                        $contactstring = $qry_contacts->contactstring;
                    }else{
                        $contactstring = '';
                    }

                    if($qry_person_address) {
                        $districtid = $qry_person_address->addrdist;
                        $districtname = $qry_person_address->descriptions;
                        $addressspec = $qry_person_address->addrspec;
                        $brgyid = $qry_person_address->addrbrgy;
                    }else{
                        $districtid = '';
                        $districtname = '';
                        $addressspec = '';
                        $brgyid = '';
                    }

                    $landmarkid = false;
                }


                $middlename_txt = (trim($row->middlename) != '') ? '-'.strtolower(str_replace(' ', '_', $row->middlename)) : '';
                $firstname_txt = strtolower(str_replace(' ', '_', $row->firstname));
                $lastname_txt = strtolower(str_replace(' ', '_', $row->lastname));
                $url =  $firstname_txt.$middlename_txt.'-'.$lastname_txt;

                $pic_info = get_owner_pic($row->personid, 'person');
                $emp_info = get_employee_info($row->sysid);

                $results[] = array(
                    "empid" => $row->sysid,
                    "sysid" => $row->personid,
                    "userid" => $row->userid,
                    "lastname" => $row->lastname,
                    "url" => $url,
                    "firstname" => $row->firstname,
                    "middlename" => $row->middlename,
                    "addr" => $addressspec,
                    "district" => $districtname,
                    "contact" => $contactstring,
                    "img" => $pic_info,
                    "dept" => $emp_info->deptdesc,
                    "tokens" => array($query, $query . rand(1, 10)),
                    "landarr" => $landmarkarr,
                    "telcode" => $row->telcode
                );

            }
        }

        echo json_encode($results);
    }

    function search_mrd_findings() {
        $results = array();
        $input = $this->input->get('query');
        $sql = $this->db->query("SELECT * FROM meter_reading_findings WHERE status = 1 AND (codes LIKE '%$input%' OR descriptions LIKE '%$input%')");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $results[] = array(
                    'sysid' => $row->sysid,
                    'codes' => $row->codes,
                    'descs' => $row->descriptions
                );
            }
        }
        return json_encode($results);
    }

    function get_meter_info($sysid = false) {
        $results = array();
        $q = $this->input->post('mtrno');
        $qry = false;
        $row = false;
        if($sysid) {
            $row = $this->db->query("
                SELECT * FROM assets_main WHERE sysid = $sysid
            ")->row();
        } else {
            if (strlen($q) > 3) {
                $row = $this->db->query("
                SELECT * FROM assets_main WHERE labels = '$q'
            ")->row();
            }
        }
        if($row) {
            $qry = true;
            $status = '';
            $owner_name = '';
            $status_arr = check_asset_status($row->sysid);
            $status = get_types_label_format($status_arr->status_id, false, false, false, false, false, true)->text;


            $type = 'N/A';
            $volts = 'N/A';
            $amps = 'N/A';
            $ercseal = 'N/A';
            $pecoseal = 'N/A';
            $kh = 'N/A';
            $reading = 'N/A';
            $wiresize = 'N/A';


            if ($status_arr->status_available == 1 || $sysid) {

                $get_asset_specs = $this->db->select()
                    ->from('assets_main_specifications_matrix')
                    ->where(array('assetid' => $row->sysid, 'status' => 1))
                    ->get();
                if ($get_asset_specs->num_rows() > 0) {
                    foreach ($get_asset_specs->result() as $srow) {
                        if ($srow->specid == 3098) {
                            $type = $srow->specval;
                        }
                        if ($srow->specid == 3097) {
                            $volts = $srow->specval;
                        }
                        if ($srow->specid == 3096) {
                            $amps = $srow->specval;
                        }
                        if ($srow->specid == 3095) {
                            $pecoseal = $srow->specval;
                        }
                        if ($srow->specid == 3094) {
                            $ercseal = $srow->specval;
                        }
                        if ($srow->specid == 3208) {
                            $kh = $srow->specval;
                        }
                        if ($srow->specid == 3206) {
                            $reading = $srow->specval;
                        }
                        if ($srow->specid == 3207) {
                            $wiresize = $srow->specval;
                        }
                    }
                }

                if ($row->brand > 0) {
                    $qry_brand = $this->db->select()
                        ->from('prime_brands')
                        ->where(array('sysid' => $row->brand))
                        ->get()->row();
                    if ($qry_brand) {
                        $brand = $qry_brand->codes;
                    } else {
                        $brand = 'N/A';
                    }
                } else {
                    $brand = 'N/A';
                }


                $pic = base_url('assets/global/img/person_default.jpg');


                $results = array(
                    'id' => $row->sysid,
                    'label' => $row->labels,
                    'serial' => $row->serials,
                    'status' => $status,
                    'type' => $type,
                    'brand' => $brand,
                    'volts' => $volts,
                    'ampere' => $amps,
                    'pecoseal' => $pecoseal,
                    'ercseal' => $ercseal,
                    'reading' => $reading,
                    'wiresize' => $wiresize,
                    'kh' => $kh,
                    'pics' => $pic,
                );
            }
        }

        $results['qry'] = $qry;
        return json_encode($results);
    }

    function search_meter() {
        $results = array();
        $q = $this->input->get('query');

        if(strlen($q) > 3) {
            $qry_asset = $this->db->query("
                SELECT * FROM assets_main WHERE labels LIKE '$q%' OR serials LIKE '$q%'
            ");
            if($qry_asset->num_rows() > 0) {
                foreach($qry_asset->result() as $row) {
                    $status = '';
                    $owner_name = '';
                    $status_arr = check_asset_status($row->sysid);
                    $status = get_types_label_format($status_arr->status_id, false, false, false, false, false, true)->text;


                    $type = 'N/A';
                    $volts = 'N/A';
                    $amps = 'N/A';
                    $ercseal = 'N/A';
                    $pecoseal = 'N/A';
                    $kh = 'N/A';
                    $reading = 'N/A';
                    $wiresize = 'N/A';


                    //if ($status_arr->status_available == 1) {

                        $get_asset_specs = $this->db->select()
                            ->from('assets_main_specifications_matrix')
                            ->where(array('assetid' => $row->sysid, 'status' => 1))
                            ->get();
                        if ($get_asset_specs->num_rows() > 0) {
                            foreach ($get_asset_specs->result() as $srow) {
                                if ($srow->specid == 3098) {
                                    $type = $srow->specval;
                                }
                                if ($srow->specid == 3097) {
                                    $volts = $srow->specval;
                                }
                                if ($srow->specid == 3096) {
                                    $amps = $srow->specval;
                                }
                                if ($srow->specid == 3095) {
                                    $pecoseal = $srow->specval;
                                }
                                if ($srow->specid == 3094) {
                                    $ercseal = $srow->specval;
                                }
                                if ($srow->specid == 3208) {
                                    $kh = $srow->specval;
                                }
                                if ($srow->specid == 3206) {
                                    $reading = $srow->specval;
                                }
                                if ($srow->specid == 3207) {
                                    $wiresize = $srow->specval;
                                }
                            }
                        }

                        if ($row->brand > 0) {
                            $qry_brand = $this->db->select()
                                ->from('prime_brands')
                                ->where(array('sysid' => $row->brand))
                                ->get()->row();
                            if ($qry_brand) {
                                $brand = $qry_brand->codes;
                            } else {
                                $brand = 'N/A';
                            }
                        } else {
                            $brand = 'N/A';
                        }


                        $pic = base_url('assets/global/img/person_default.jpg');


                        /*$getowner = $this->db->select("ownerid,ownertype")->from("assets_main_owner_history")
                            ->where(array("assetid" => $row->sysid , "status" => 1))
                            ->order_by("sysid" , "desc")
                            ->limit(1)
                            ->get()->row();

                        if($getowner) {
                            $ownerid = $getowner->ownerid;
                            if ($getowner->ownertype == 3) {
                                $getaccountdetails = $this->db->select("")
                                    ->from("customer_accounts_main")
                                    ->where(array("sysid" => $getowner->ownerid))
                                    ->get()->row();
                                if ($getaccountdetails) {
                                    if ($getaccountdetails->types == 5) {
                                        $legacyid = $getaccountdetails->ownerid;
                                        //GET TO THE CUSTOMER LEGACY
                                        $getlegacyname = $this->db->select("name")->from("customer_accounts_name_legacy")
                                            ->where(array("sysid" => $legacyid))
                                            ->get()->row();
                                        if ($getlegacyname) {


                                            $info = get_active_account_info($getaccountdetails->sysid);
                                            $servno = $info->servicenumber;
                                        }
                                    }
                                }
                            }
                        }*/

                        $results[] = array(
                            'id' => $row->sysid,
                            //'acctid' => $ownerid,
                            'label' => $row->labels,
                            'serial' => $row->serials,
                            'status' => $status,
                            'type' => $type,
                            'brand' => $brand,
                            'volts' => $volts,
                            'ampere' => $amps,
                            'pecoseal' => $pecoseal,
                            'ercseal' => $ercseal,
                            'reading' => $reading,
                            'wiresize' => $wiresize,
                            'kh' => $kh,
                            'pics' => $pic,
                            //'servno' => $servno
                        );
                    //}
                }
            }
        }

        return json_encode($results);
    }

    function search_item_category() {
        $res = array();
        $q = $this->input->get('query');
        $qry = $this->db->query("
                SELECT m.codes, m.names, m.desc
                FROM items_main_category AS m
                WHERE (m.codes LIKE '%$q%' OR m.names LIKE '%$q%')
            ");
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $res[] = array(
                    'codes' => (trim($row->codes)) ? $row->codes : 'Undefined',
                    'names' => $row->names
                );
            }
        }

        return json_encode($res);
    }

    function search_item_component($key = false, $cat = false) {
        $res = array();
        if($key == false) {
            $key = $this->input->get('query');
            $cat = $this->input->get('cat');
        }

        $cat_where = '';

        if($cat && $cat != '') {
            // CHECK CATEGORY EXISTS
            $qry_ = $this->db->query("
                SELECT sysid FROM items_main_category 
                WHERE 
                (`codes` LIKE '%$category%') 
                OR (`names` LIKE '%$category%') 
                OR (`desc` LIKE '%$category%') 
                AND `status` = 1 LIMIT 0,10 
            ")->row();
            if($qry_) {
                $catid = $qry_->sysid;
                $cat_where = ' AND catid = ' . $catid;
            }
        }

        $qry = $this->db->query("
                SELECT `codes`, `names`, `desc`
                FROM items_main_components
                WHERE (`codes` LIKE '%$key%' OR `names` LIKE '%$key%') $cat_where
            ");

        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $res[] = array(
                    'codes' => (trim($row->codes)) ? $row->codes : 'Undefined',
                    'names' => $row->names
                );
            }
        }

        return json_encode($res);
    }

    function search_customer_application() {
        $res = array();
        $query = $this->input->get('query');
        $qry_details = $this->db->query("
            SELECT
                CD.sysid,
                CD.personid,
                CD.essrno,
                CD.gdlbid,
                CD.servno,
                CD.multid,
                CD.rateclassid,
                CD.addrspec, 
                CD.distid, 
                CD.contactmobile, 
                CD.contactphone, 
                CD.contactemail, 
                CD.moduleid,
                CS.classid,
                CS.connid,
                CS.owntypeid,
                CS.loctypeid,
                p.lastname,
                p.firstname,
                p.middlename,
                CONCAT(p.lastname, ', ', p.firstname) AS appname            
            FROM application_customers_details AS CD
            LEFT JOIN person AS p ON p.sysid = CD.personid
            LEFT JOIN application_customers_geodata AS CG ON CG.appid = CD.sysid AND CG.status = 1
            LEFT JOIN application_customers_subscriptions AS CS ON CS.appid = CD.sysid AND CS.status = 1
            WHERE
            CD.essrno LIKE '%$query%'
            OR p.lastname LIKE '%$query%'
            OR p.firstname LIKE '%$query%'
            GROUP BY
                CD.sysid,
                CD.personid,
                CD.essrno,
                CD.gdlbid,
                CD.servno,
                CD.multid,
                CD.rateclassid,
                CD.addrspec, 
                CD.distid, 
                CD.contactmobile, 
                CD.contactphone, 
                CD.contactemail, 
                CD.moduleid,
                CS.classid,
                CS.connid,
                CS.owntypeid,
                CS.loctypeid,
                p.lastname,
                p.firstname,
                p.middlename
            ");
        if($qry_details->num_rows() > 0) {
            foreach($qry_details->result() as $row) {
                $pic = get_owner_pic($row->personid, 'person');
                $res[] = array(
                    'sysid' => $row->sysid,
                    'essrno' => $row->essrno,
                    'appname' => $row->appname,
                    'addrspec' => $row->addrspec,
                    'pic' => $pic
                );
            }
        }

        return json_encode($res);
    }

    function search_suppliers() {
        $data = array();
        $q = $this->input->get('query');

        $sql = $this->db->query("SELECT
                                    s.sysid, 
                                    s.descs, 
                                    isa.address, 
                                    isc.contact,
                                    GROUP_CONCAT(CONCAT(isc.typesid, '-', isc.contact)) AS contact_arr
                                FROM inventory_suppliers AS s
                                    LEFT JOIN inventory_suppliers_address AS isa ON  s.sysid = isa.supplierid AND isa.`status` = 1
                                    LEFT JOIN inventory_suppliers_contact AS isc ON s.sysid = isc.supplierid
                                WHERE s.`status` = 1 AND (s.codes LIKE '%$q%' OR s.descs LIKE '%$q%')"
        );
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $email = '';
                $phone = '';
                $contact_arr = explode(',', $row->contact_arr);
                if(is_array($contact_arr) & count($contact_arr)>0) {
                    foreach($contact_arr as $crow) {
                        $contact_arr_1 = explode('-', $crow);
                        if($contact_arr_1[0] == 1053) { // EMAIL CODE
                            $email = $contact_arr_1[1];
                        }else{
                            $phone = $contact_arr_1[1];
                        }
                    }
                }

                $pic = base_url('assets/global/img/person_default.jpg');
                $data[] = array(
                    'id' => $row->sysid,
                    'names' => $row->descs,
                    'address' => $row->address,
                    'phone' => $phone,
                    'email' => $email,
                    'picture' => $pic
                );
            }
        }
        return json_encode($data);
    }
}