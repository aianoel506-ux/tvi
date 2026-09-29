<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inspection extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_inspection', 'inspection', true);
        $this->load->model('model_query');
    }

    function getselectequipment() {
        echo $this->inspection->get_select_equipments();
    }

    function addequipment() {
        echo $this->inspection->add_equipment();
    }

    function delequipment() {
        echo $this->inspection->del_equipment();
    }

    function savenearmeter() {
        echo $this->inspection->save_near_meter();
    }

    function initequipmentlist() {
        echo $this->inspection->init_equipment_data();
    }

    function initrateclasslist() {
        echo get_rate_class_select();
    }
    
    function initdistrictlist() {
        echo get_dist_list_select();
    }

    function initaccountgdr() {
        echo $this->inspection->init_account_gdr();
    }

    function savegdrpayments() {
        echo $this->inspection->save_gdr_payments();
    }

    function changegdrpayments() {
        echo $this->inspection->change_gdr_payments();
    }

    function dtgdrlogs() {
        echo $this->inspection->dt_gdr_logs();
    }

    function dtinspectionlogs() {
        echo $this->inspection->dt_inspection_logs();
    }

    function getgdrsubdetails() {
        echo $this->inspection->get_gdr_subdetails();
    }

    function switchgdr() {
        echo $this->inspection->switch_gdr_computation();
    }

    function getactivegdrdata($dataid=false) {
        if ($dataid == false) {
            $dataid = $this->input->post('dataid');
        }
        $qrd = array();
        $gdracct = check_acct_gdr($dataid);
        if ($gdracct) {
            $qrd = array(
                'totalwatt' => number_format($gdracct->totalwatt, 0),
                'totalcost' => number_format($gdracct->totalcost, 2),
                'dailyop' => $gdracct->dailyop,
                'demand' => $gdracct->demand,
                'monthlyop' => $gdracct->monthlyop,
                'rates' => $gdracct->rates,
                'rateclassname' => $gdracct->rateclassname
            );
        }
        $return = ($gdracct) ? $qrd : false;
        echo json_encode($return);
    }

    function getitemselect() {
        echo items_select_list();
    }

    function getaccountmapping() {
        $dataid = $this->input->post('id');
        $data = customer_mapping($dataid);
        echo json_encode($data);
    }

    function removegoemarker() {
        $id = $this->input->post('id');
        $this->db->trans_begin();
        $this->db->where(array('sysid' => $id));
        $this->db->update('application_customers_geodata', array('status' => 0, 'updatedby' => user_id()));
        $data = db_trans($this->db);
        echo json_encode($data);
    }

    function updategeodata() {
        // @TODO check if any changes
        $x = $this->input->post('x');
        $y = $this->input->post('y');
        $a = $this->input->post('a');
        $i = $this->input->post('i');
        $moduleid = $this->input->post('moduleid');
        $inspdate = $this->input->post('inspdate');
        $remarks = $this->input->post('remarks');
        $types = $this->input->post('types');
        $app_tbl = 'application_customers_details';
        $geo_tbl = 'application_customers_geodata';
        $msg = '';
        $qry = false;
        $func = 'error';
        $insert_id = 0;

        $new_map_url = "https://www.google.com/maps/@$x,$y,".$i."z";

        $this->db->trans_begin();
        // GET OLD DATA ADDRESS SPECIFIC
        $qry_addr = $this->db->select('addrspec')
            ->from($app_tbl)
            ->where('sysid', $i)->get()->row();
        if($qry_addr) {
            if($qry_addr->addrspec != $a) {
                $audit_ins_arr = array(
                    'dataid' => $i,
                    'moduleid' => $moduleid,
                    'valueold' => $qry_addr->addrspec,
                    'valuenew' => $a,
                    'createdby' => user_id(),
                    'remarks' => 'CAD - CHANGE MAP LOCATION'
                );
                /*
                $audit_ins = audit_insert($audit_ins_arr);
                if ($audit_ins) {
                    // UPDATE ADDR SPEC
                    //$this->db->where('sysid', $i);
                    //$this->db->update($app_tbl, array('addrspec' => $a));
                    $msg = 'Data updated!';
                    $qry = true;
                    $func = 'success';
                }
                */
            } else {
                $msg = 'Nothing has changed!';
                $qry = false;
                $func = 'warning';
            }
        }

        // GET OLD DATA GEO DATA
        $qry_geo = $this->db->select('lat, lon, alt')
            ->from($geo_tbl)
            ->where(array('appid' => $i, 'status' => 1))
            ->get()->row();
        $data['errmsg'] = $this->db->_error_message();

        // UPDATE CURRENT GEODATA TO STATUS ZERO
        $this->db->where('appid', $i);
        $this->db->where('status', 1);
        $this->db->where('typesid', $types);
        $this->db->update($geo_tbl, array('status' => 0));
        $audit_ins_arr = array(
            'dataid' => $i,
            'moduleid' => $moduleid,
            'valueold' => $qry_geo->lat . '/' . $qry_geo->lon . '/' . $qry_geo->alt,
            'valuenew' => $x . '/' . $y.'/12',
            'createdby' => user_id(),
            'remarks' => 'CAD - CHANGE MAP GEODATA'
        );
        // $audit_ins = audit_insert($audit_ins_arr);
        //if ($audit_ins) {
            // INSERT NEW GEODATA
            $ins_arr = array(
                'appid' => $i,
                'lat' => $x,
                'lon' => $y,
                'alt' => 12,
                'url' => $new_map_url,
                'inspdate' => ($inspdate) ? $inspdate : null,
                'remarks' => $remarks,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'typesid' => $types
            );
            $this->db->insert($geo_tbl, $ins_arr);
            $data['err_geo'] = $this->db->_error_message();
            $insert_id = $this->db->insert_id();
            $msg = 'Data updated!';
            $qry = true;
            $func = 'success';
        /*
        } else {
            $qry = false;
        }
        */

        if ($this->db->trans_status() === FALSE && $qry==true) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        $data['msg'] = $msg;
        $data['input'] = $this->input->post();
        $data['qry'] = $qry;
        $data['newid'] = $insert_id;
        $data['url'] = $new_map_url;
        echo json_encode($data);
    }

    function testiteminfo() {
        print_r(get_item_info(2));
    }

    function updatemapurl() {
        echo $this->inspection->update_map_url();
    }

    function saveinspection() {
        echo $this->inspection->save_inspection();
    }

    function dtactiveloads() {
        echo $this->inspection->dt_active_loads();
    }

    function changeinspectiondetails() {
        echo $this->inspection->change_inspection_details();
    }

    function changeactiveinspection() {
        echo $this->inspection->change_active_inspection();
    }

    function deleteinspection() {
        echo $this->inspection->delete_inspection();
    }

    function initgdrcompute() {
        echo $this->inspection->init_gdr_compute();
    }

    function testing() {
        print_r( get_item_info(10) );
    }

    function tagecales() {
        echo $this->inspection->tag_ecales();
    }

    function getaccountmap() {
        echo $this->inspection->get_account_map();
    }

    function addteammember() {
        echo $this->inspection->add_team_member();
    }
    function getteamassignment() {
        echo $this->inspection->get_team_member();
    }
    function deleteteam() {
        echo $this->inspection->del_team_member();
    }
    function saveinspectionreport() {
        echo $this->inspection->save_inspection_report();
    }
    function computeinspection() {
        echo $this->inspection->compute_inspection_report();
    }
    function select2paneltype() {
        echo $this->model_query->select2_panel_type();
    }
    function getactivesurvey() {
        echo $this->inspection->get_active_survey();
    }
    function getspsitemslist() {
        echo $this->inspection->get_sps_items_list();
    }
    function searchsetuptemplate() {
        echo $this->inspection->search_setup_template();
    }
    function getsavedsystemsize() {
        echo $this->inspection->get_saved_system_size();
    }
    function panelsperstringlookup() {
        echo $this->inspection->panels_per_string_lookup();
    }
    function invertersizelookup() {
        echo $this->inspection->inverter_size_lookup();
    }
    function templatelist() {
        echo $this->inspection->template_list();
    }
    function templatedetails() {
        echo $this->inspection->template_details();
    }
    function getspsitemslisttemplate() {
        echo $this->inspection->get_sps_items_list_template();
    }
    function loadselectedtemplate() {
        echo $this->inspection->load_selected_template();
    }
    function updateinstallationitem() {
        echo $this->inspection->update_installation_item();
    }
    function select2applicationspsitems() {
        echo $this->inspection->select2_application_sps_items();
    }
    function getspsitemdefaults() {
        echo $this->inspection->get_sps_item_defaults();
    }
    function addspsitem() {
        echo $this->inspection->add_sps_item();
    }

    function uploadsurveypics() {
        echo $this->inspection->upload_survey_pics();
    }

    function printtssr() {
        echo $this->inspection->print_tssr();
    }

    function printinstallationsetup() {
        echo $this->inspection->print_installation_setup();
    }

    function tssr() {
        $id = $this->input->post('id');
        $html = $this->load->view('custom/templates/tssr',array('appid'=>$id),true);
        echo $html;
    }

    function select2rooftypes() {
        $data = array();
        $data['list'] = array(
            array('id' => 1,'text' => 'Long Span'),
            array('id' => 2,'text' => 'GI Sheets'),
            array('id' => 3,'text' => 'GI Sheets (Corrugated)'),
            array('id' => 4,'text' => 'Ceramic Tiles'),
            array('id' => 5,'text' => 'Roof Deck'),
            array('id' => 6,'text' => 'Others'),
        );

        echo json_encode($data);
    }

}
