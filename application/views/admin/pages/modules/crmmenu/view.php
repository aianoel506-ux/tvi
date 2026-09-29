<style>
    .mt-list-item {
        margin-bottom: 0px !important;
        padding-bottom: 10px !important;
    }
    .mt-element-list, .mt-list-container, .list-trails{
        border: none !important;
    }
    .mt-list-container {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    .mt-list-item {
        margin-top: 0px !important;
        padding-top: 10px !important;
    }
</style>

<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 5/30/2018
 * Time: 4:24 PM
 */
$ci = &get_instance();
$ci->load->model('model_crm');

$crm_qry = $ci->model_crm->get_ticket_details($dataid);
$ts_decode = json_decode($crm_qry);

if($ts_decode) {
    $ts_info = $ts_decode->qry;
    $ts_traill = $ts_decode->trail;
    $ts_trail_cnt = $ts_decode->trailno;
    $ts_team = $ts_decode->team;

    $user_fullname = get_users_info($ts_info->createdby)->firstname. ' ' . get_users_info($ts_info->createdby)->lastname;

    $middlename = (isset($ts_info->middlename[0])) ? strtoupper($ts_info->middlename[0]) .'.' : '';
    $complainants = ($ts_info->firstname!='') ? $ts_info->lastname . ', '.$ts_info->firstname. ' ' . $middlename  : $ts_info->compname;
    $tcsource = get_types_label_format($ts_info->repsource, false, false, false, false, true);
    $createdby = '('. get_users_info($ts_info->createdby)->username . ') ' . $user_fullname;

    if($ts_info->status!=305) {
        $ticket_trail_arr = array(
            'ticketid' => $ts_info->sysid,
            'codes' => 'READ',
            'descs' => 'By: ' . user_info(user_id())->username,
            'createdby' => user_id()
        );
        $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
    }

    $qry_trail_lastread = $this->db->select()->from('ticketing_details_trails')
        ->where(array('ticketid' => $ts_info->sysid, 'codes' => 'READ'))
        ->order_by('datecreated', 'desc')
        ->get()->row();

    $qry_trail_accomp = $this->db->select()->from('ticketing_details_trails')
        ->where(array('ticketid' => $ts_info->sysid, 'codes' => 'TSACCOMPLISHMENT'))
        ->order_by('datecreated', 'desc')
        ->get()->row();

    $lastt_action = 'No Accomplishment Remarks';

    if($ts_info->status == 314) {
        $qry_accomplish = $this->db->select('remarks')->from('ticketing_details_trails')
            ->where(array('ticketid' => $ts_info->sysid, 'remarks != ' => ''))
            ->order_by('datecreated', 'desc')
            ->get()->row();
        if($qry_accomplish) {
            $lastt_action = $qry_accomplish->remarks;
        }
    }

    $qry_trail_findings = $this->db->select()->from('ticketing_details_logs_findings')
        ->where(array('ticketid' => $ts_info->sysid, 'status' => 1))
        ->get()->row();

    $qry_trail_equipments = $this->db->select()->from('ticketing_details_logs_equipments')
        ->where(array('ticketid' => $ts_info->sysid, 'status' => 1))
        ->get();

    ?>

    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" icon-layers font-green"></i>
                            <span class="caption-subject font-green bold uppercase">Report Details</span>
                            <div class="caption-desc font-grey-cascade"></div>
                        </div>

                        <div class="actions">
                            <div class="btn-group btn-group-devided">
                                <button class="btn btn-default btn-sm" id="btn_new_ticket"><i class="fa fa-print"></i> Print Ticket Reports</button>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body ">
                        <ul class="list-group summary column">
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Source</span>
                                <span class="col-md-8 label-default number"><?php echo $tcsource;?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Complainants</span>
                                <span class="col-md-8 label-default number"><?php echo $complainants;?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">District</span>
                                <span class="col-md-8 label-default number"><?php echo get_district_name($ts_info->district); ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Barangay</span>
                                <span class="col-md-8 label-default number"><?php echo $ts_info->barangay; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Landmarks</span>
                                <span class="col-md-8 label-default number"><?php echo $ts_info->landmarks; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Report Stated</span>
                                <span class="col-md-8 label-default number"><?php echo $ts_info->remarks; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Date Created</span>
                                <span class="col-md-8 label-default number"><?php echo $ts_info->datecreated; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Created By</span>
                                <span class="col-md-8 label-default number"><?php echo $createdby; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Status</span>
                                <span class="col-md-8 label-default number"><?php echo get_types_label_format($ts_info->status); ?></span>
                            </li>
                        </ul>

                        <hr>

                        <button id="btn_refresh_view" type="button" class=" btn btn-default pull-right">Reload</button>
                        <div class="btn-group">
                            <a class="btn btn-default" href="<?php echo base_url('module/eb4ac3033e8ab3591e0fcefa8c26ce3fd36d5a0f/list'); ?>">Back To List</a>
                            <a class="btn btn-primary">Send to Next</a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="portlet light portlet-fit bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-layers font-green"></i>
                                <span class="caption-subject font-green bold uppercase">Trail</span>
                                <div class="caption-desc font-grey-cascade" style="display: inline-block; width: 100%;">
                                    Last Read: <?php echo ($qry_trail_lastread) ? $qry_trail_lastread->datecreated : ''; ?> <span class="pull-right text-danger"><?php echo ($qry_trail_lastread) ? $qry_trail_lastread->descs : ''; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-default">
                                    <ul class="list-trails">
                                        <?php if($ts_traill) {
                                            foreach ($ts_traill as $trow) {
                                                $user_fullname = get_users_info($trow->createdby)->firstname.' '.get_users_info($trow->createdby)->lastname;
                                                ?>
                                                <li class="mt-list-item done">
                                                    <div class="list-icon-container">
                                                        <a href="javascript:;">
                                                            <i class="icon-check"></i>
                                                        </a>
                                                    </div>
                                                    <div class="list-datetime" style="margin-right: 10px;"><?php echo $trow->datecreated; ?></div>
                                                    <div class="list-item-content">
                                                        <h3 class="uppercase">
                                                            <?php if($trow->statusid == '') { ?>
                                                                <b><a href="javascript:;"><?php echo $trow->codes; ?></a></b>
                                                            <?php }else { ?>
                                                                <b><a href="javascript:;"><?php echo get_types_label_format($trow->statusid, false, false, false, 'javascript:;', false, true)->text; ?></a></b>
                                                            <?php } ?>
                                                        </h3>
                                                        <p><?php echo $trow->descs; ?></p>
                                                        <small class="font-green-haze">By: <?php echo $user_fullname; ?></small>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                            if($ts_trail_cnt>5) {
                                                echo '<li class="mt-list-item done">';
                                                echo '<a class="pull-right" href="'.base_url('module/d321d6f7ccf98b51540ec9d933f20898af3bd71e/data/'.$dataid.'/logs').'">View All ('.$ts_trail_cnt.')</a>';
                                                echo '</li>';
                                            }

                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>


        <div class="col-md-8">
            <div id="trn_container" style="min-height: 200px;">
                <h3 style="padding: 10px 10px;">Loading content...</h3>
            </div>
        </div>

    </div>
    <script src="<?php echo base_url(); ?>assets/pages/crmmenu/crm.js"></script>

    <script>
        CRM.view(<?php echo $dataid; ?>);
    </script>
    <?php
} else {
    echo page_construction();
}
?>

