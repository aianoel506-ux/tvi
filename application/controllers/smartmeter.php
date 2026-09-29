<?php


class Smartmeter extends CI_Controller
{

    public function __construct() {
        parent::__construct();
    }

    function index() {

        @$save_to_meter_data = $_POST['save_to_meter_data'];
        if($save_to_meter_data!='') {
            @$meter_id = $_POST['meter_id'];

            @$location = $_POST['location'];
            @$_datetime = $_POST['datetime'];
            @$datetime = str_replace('%20', ' ', $_datetime);
            @$datetime = date('Y-m-d H:i:s');
            @$vrms_a = $_POST['vrms_a'];
            @$vrms_b = $_POST['vrms_b'];
            @$vrms_c = $_POST['vrms_c'];

            @$irms_a = $_POST['irms_a'];
            @$irms_b = $_POST['irms_b'];
            @$irms_c = $_POST['irms_c'];

            @$freq = $_POST['freq'];
            @$pf = $_POST['pf'];
            @$watt = $_POST['watt'];

            @$va = $_POST['va'];
            @$var = $_POST['var'];

            @$wh_del = $_POST['wh_del'];
            @$wh_rec = $_POST['wh_rec'];
            @$wh_net = $_POST['wh_net'];
            @$wh_total = $_POST['wh_total'];

            @$varh_neg = $_POST['varh_neg'];
            @$varh_pos = $_POST['varh_pos'];
            @$varh_net = $_POST['varh_net'];
            @$varh_total = $_POST['varh_total'];
            @$vah_total = $_POST['vah_total'];

            @$max_rec_kw_dmd = $_POST['max_rec_kw_dmd'];
            @$max_rec_kw_dmd_time = str_replace('%20', ' ', $_POST['max_rec_kw_dmd_time']);
            @$max_del_kw_dmd = $_POST['max_del_kw_dmd'];
            @$max_del_kw_dmd_time = str_replace('%20', ' ', $_POST['max_del_kw_dmd_time']);

            @$max_pos_kvar_dmd = $_POST['max_pos_kvar_dmd'];
            @$max_pos_kvar_dmd_time = str_replace('%20', ' ', $_POST['max_pos_kvar_dmd_time']);
            @$max_neg_kvar_dmd = $_POST['max_neg_kvar_dmd'];
            @$max_neg_kvar_dmd_time = str_replace('%20', ' ', $_POST['max_neg_kvar_dmd_time']);

            @$relay_status = $_POST['relay_status'];

            $ins_arr = array(
                'location' => $location,
                'meter_id' => $meter_id,
                'datetime' => $datetime,
                'vrms_a' => $vrms_a,
                'vrms_b' => $vrms_b,
                'vrms_c' => $vrms_c,
                'irms_a' => $irms_a,
                'irms_b' => $irms_b,
                'irms_c' => $irms_c,
                'freq' => $freq,
                'pf' => $pf,
                'watt' => $watt,
                'va' => $va,
                'var' => $var,
                'wh_del' => $wh_del,
                'wh_rec' => $wh_rec,
                'wh_net' => $wh_net,
                'wh_total' => $wh_total,
                'varh_neg' => $varh_neg,
                'varh_pos' => $varh_pos,
                'varh_net' => $varh_net,
                'varh_total' => $varh_total,
                'vah_total' => $vah_total,
                'max_rec_kw_dmd' => $max_rec_kw_dmd,
                'max_rec_kw_dmd_time' => $max_rec_kw_dmd_time,
                'max_del_kw_dmd' => $max_del_kw_dmd,
                'max_del_kw_dmd_time' => $max_del_kw_dmd_time,
                'max_pos_kvar_dmd' => $max_pos_kvar_dmd,
                'max_pos_kvar_dmd_time' => $max_pos_kvar_dmd_time,
                'max_neg_kvar_dmd' => $max_neg_kvar_dmd,
                'max_neg_kvar_dmd_time' => $max_neg_kvar_dmd_time,
                'relay_status' => $relay_status
            );
            $this->db->insert('meter_data', $ins_arr);
            echo $this->db->_error_message();
        }
        //RETURN DATETIME
        $server_time=date('Y-m-d H:i:s');
        echo "OK, $server_time";
    }
}