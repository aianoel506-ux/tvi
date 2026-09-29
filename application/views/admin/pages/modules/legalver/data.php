<?php
/*
$firstname = $this->model_query->get_owner_info($dataid)->FIRSTNAME;
$total_load = acct_total_loads($dataid);

$qry_account_main = $this->db->select()->from("trn_customer_accounts_main")->where('sysid', $dataid)->get()->row();
$qry_account_owner = $this->db->select()->from("trn_customer_accounts_owners")->where(array('accountid' => $dataid, 'status' => 1))->get()->row();
$qry_account_owner_addr = $this->db->select()->from("trn_customer_accounts_address")->where(array('acctid' => $qry_account_owner->sysid, 'status' => 1))->get()->row();
$qry_account_owner_geo = $this->db->select()->from("trn_customer_accounts_subscription_geodata")->where(array('addressid' => $qry_account_owner_addr->sysid, 'status' => 1))->get()->row();


$acctrate = ($this->model_query->get_owner_info($dataid)) ? $this->model_query->get_owner_info($dataid)->ACCTRATE : "";

// GET SEQUENC NO
$query_seq = $this->db->select('sequence')->from('customer_accounts_servsequence')->where('classid', $acctrate)->order_by('sequence', 'desc')->get()->row();
$last_seq = ($query_seq) ? $query_seq->sequence : "N/A";

// GET TRN ID
// TRAIL ID
$trail_id = $this->uri->segment(4);
$query_trn = $this->db->select()->from('transaction_request_main_trails')->where(array('sysid' => $trail_id, 'dataid' => $dataid))->get()->row();
$trn_id = ($query_trn) ? $query_trn->trnid : "";

$account_type = 'Regular';
if ($total_load >= 15000) {
    $account_type = 'Spacial';
}


$ownerid = $qry_account_owner->ownerid;
$verification_legal = account_verifications($dataid);
*/
$info = application_info($dataid);
$appname = ($info->q) ? $info->appname : '';
$status = ($info->q) ? $info->status : '';
$address = ($info->q) ? $info->address : '';
$datecreated = ($info->q) ? $info->datecreated : '';
$rateclassid = get_application_details($dataid)->info->rateclassid;

$landmark = ($info->q) ? $info->landmark : 'N/A';
$distname = ($info->q) ? $info->distname : 'N/A';
$gdlb = ($info->q) ? $info->gdlb : 'N/A';
$mapupdated =($info->q) ?  $info->mapupdated : 'N/A';
$mapupdatedby = ($info->q) ? $info->mapupdatedby : 'N/A';
$conntype = ($info->q) ? $info->conntype : 'N/A';
$ownertype = ($info->q) ? $info->ownertype : 'N/A';
$rateclass = ($info->q) ? $info->rateclass : 'N/A';
$landtype = ($info->q) ? $info->landtype : 'N/A';
$totalload = ($info->q) ? $info->totalload : 'N/A';
$essrno = ($info->q) ? $info->essrno : 'N/A';
$tinno = ($info->q) ? $info->tinno : 'N/A';

$phone = (($info->q && $info->phone != '') || ($info->phone > 0)) ? $info->phone : 'N/A';
$mobile = (($info->q && $info->mobile != '') || ($info->mobile > 0)) ? $info->mobile : 'N/A';
$email = (($info->q && $info->email != '') || ($info->email > 0)) ? $info->email : 'N/A';


$servno = '';



$online = $this->db->select()->from('application_customers_online_ticket_ref')
    ->where(array('appid' => $dataid, 'status' => 1))->get()->row();

$pic_recent = base_url() . 'assets/global/img/person_default.jpg';
$corpname = 'Unknown';
$corpbranch = 'Unknown';

// GET CORP INFO
$qry_corp_app = $this->db->select()
    ->from('application_customers_corporation')
    ->where(array('appid' => $dataid))
    ->get()->row();

if($qry_corp_app) {
    $info = get_corporation_info($qry_corp_app->corpid);
    $map = directory_map('./uploads/corporation/' . $qry_corp_app->corpid . '/', FALSE, TRUE);
    $pic_recent = ($map && count($map) > 0) ? base_url('uploads/corporation/' . $qry_corp_app->corpid . '/' . $map[0]) : base_url('assets/global/img/not-available.png');


    if ($info->qry) {
        $corpname = $info->info->descs;
        $qry_branch = $this->db->select()
            ->from('corporation_branches')
            ->where(array('corpid' => $qry_corp_app->corpid, 'sysid' => $qry_corp_app->branchid))
            ->get()->row();
        if ($qry_branch) {
            $corpbranch = $qry_branch->names;
        }
    }
}
?>

<div class="tab-pane fade in" id="data">
    <div class="row">
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light" data-toggle="editable">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Profile</span>
                                <span class="caption-helper">person's basic information</span>
                            </div>
                            <div class="tools">

                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3 text-align-center">
                                    <img src="<?php echo base_url(); ?>assets/global/img/person_default.jpg" height="130px" />
                                </div>
                                <div class="col-md-9">
                                    <ul class="list-group summary row no-border">
                                        <li class="list-group-item">
                                           <span class="label-name col-md-3">
                                               Name
                                           </span>
                                            <span class="label-default col-md-9">
                                               <?php echo $appname;?><span class="pull-right text-danger text-bold"><?php echo $servno;?></span>
                                           </span>
                                        </li>
                                        <li class="list-group-item">
                                           <span class="label-name col-md-3">
                                               Status
                                           </span>
                                            <span class="label-default col-md-9">
                                               <?php echo $status;?>
                                           </span>
                                        </li>
                                        <li class="list-group-item">
                                           <span class="label-name col-md-3">
                                               Address
                                           </span>
                                            <span class="label-default col-md-9">
                                               <?php echo $address;?>
                                           </span>
                                        </li>
                                        <li class="list-group-item">
                                           <span class="label-name col-md-3">
                                               Date Posted
                                           </span>
                                            <span class="label-default col-md-9">
                                               <?php echo $datecreated;?>
                                           </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="portlet light bordered ">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-edit"></i>
                                        <span class="caption-subject font-green-sharp bold uppercase">Subscription Location</span>
                                        <span class="caption-helper">Location of the new account.</span>
                                    </div>
                                    <div class="tools">
                                        <a data-toggle="ajax-modal" title="Application Map: <?php echo $appname; ?>" href="#frm_cad_customer_map"><i class="fa fa-map"></i> View Map</a>
                                    </div>
                                </div>
                                <div  class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-5">


                                            <ul class="list-group summary row no-border">
                                                <li class="list-group-item">
                                                    <span class="label-name col-md-5">Rate </span>
                                                    <span class="label-default col-md-7"><?php echo $rateclass; ?></span>
                                                </li>

                                                <li class="list-group-item">
                                                    <span class="label-name col-md-5">Connection </span>
                                                    <span class="label-default col-md-7"><?php echo $conntype; ?></span>
                                                </li>

                                                <li class="list-group-item">
                                                    <span class="label-name col-md-5">Ownership </span>
                                                    <span class="label-default col-md-7"><?php echo $ownertype; ?></span>
                                                </li>

                                                <li class="list-group-item">
                                                    <span class="label-name col-md-5">Land </span>
                                                    <span class="label-default col-md-7"><?php echo $landtype; ?></span>
                                                </li>
                                            </ul>

                                        </div>


                                        <div class="col-md-7">


                                            <h5 class="text-info"><i class="fa fa-map-marker fa-fw"></i> <b>Location Details</b></h5>
                                            <ul class="list-group summary row no-border">

                                                <li class="list-group-item">
                                                    <span class=" label-name col-md-3">Tin #: </span>
                                                    <span class="label label-default col-md-9 pull-right">
                                                            <?php echo $tinno; ?>
                                                        </span>

                                                </li>


                                                <li class="list-group-item">
                                                    <span class=" label-name col-md-3">Landmark </span>
                                                    <span class="label label-default col-md-9 pull-right">
                                                        <?php echo $landmark; ?>
                                                    </span>
                                                </li>

                                                <li class="list-group-item">
                                                    <span class=" label-name col-md-3">District </span>
                                                    <span class="label label-default col-md-9 pull-right">
                                                        <?php echo $distname; ?>
                                                    </span>
                                                </li>

                                                <li class="list-group-item">
                                                    <span class=" label-name col-md-3">GDLB </span>
                                                    <span class="label label-default col-md-9 pull-right">
                                                         <?php echo $gdlb; ?>
                                                    </span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class=" label-name col-md-3">Map Updated </span>
                                                    <span class="label label-default col-md-9 pull-right">
                                                        <?php echo $mapupdated; ?>
                                                    </span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class=" label-name col-md-3">Updated By </span>
                                                    <span class="label label-default col-md-9 pull-right">
                                                        <?php echo $mapupdatedby; ?>
                                                    </span>
                                                </li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="portlet light box table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Apprehension List</span>
                        <span class="caption-helper">General</span>
                    </div>
                    <div class="pull-right" style="padding-top: 10px;">
                        <!--
                        <?php if($exempt==false) { ?>
                            <a href="<?php echo base_url('legal/exemptapprehension'); ?>" class="btn btn-danger pull-right btn-sm" data-id="<?php echo $dataid; ?>" id="btn_exempt"><i class="fa fa-check"></i> Clear</a>
                        <?php }else{ ?>
                            <h4 class="text-success text-bold pull-right"><i class="fa fa-check"></i> Exemption</h4>
                        <?php } ?>
                        -->
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12"  style="padding: 10px 30px;">
                            <ul id="verfiy_result">

                            </ul>
                        </div>
                    </div>
                    <h4 class="col-md-6 pull-left" id="table_title">Table Title</h4>
                    <table class="table table-hover table-striped table-condensed table-advance" id="apprehension_match">
                        <thead>
                        <th>#</th>
                        <th>Name Match</th>
                        <th>Address</th>
                        <th>Apprehension Date</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<hr>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>


<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>



<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/legal/main.js"></script>

<script>
    LEGAL.verification(<?php echo $dataid; ?>);
</script>