
<link href="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ;?>assets/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" />

<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>

<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>


<?php
$info           = application_info($dataid);
$appname        = ($info->q) ? $info->appname : 'N/A';
$status         = ($info->q) ? $info->status : 'N/A';
$address        = ($info->q) ? $info->address : 'N/A';
$citymun        = ($info->q) ? $info->citymun : 'N/A';
$province       = ($info->q) ? $info->province : 'N/A';
$datecreated    = ($info->q) ? $info->datecreated : 'N/A';
$tinno          = ($info->q) ? $info->tinno : 'N/A';
$landmark       = ($info->q) ? $info->landmark : 'N/A';
$distname       = ($info->q) ? $info->distname : 'N/A';
//$gdlb           = ($info->q) ? $info->gdlb : 'N/A';
$mapupdated     = ($info->q) ? $info->mapupdated : 'N/A';
$mapupdatedby   = ($info->q) ? $info->mapupdatedby : 'N/A';
$maplink        = ($info->q) ? $info->maplink : 'javascript:;';
$conntype       = ($info->q) ? $info->conntype : 'N/A';
$ownertype      = ($info->q) ? $info->ownertype : 'N/A';
$rateclass      = ($info->q) ? $info->rateclass : 'N/A';
$landtype       = ($info->q) ? $info->landtype : 'N/A';
$distid         = ($info->q) ? $info->distid : 0;
$totalload      = ($info->q) ? $info->totalload : 0;
$essrno         = ($info->q) ? $info->essrno : false;
//$gdlbid         = ($info->q) ? $info->gdlbid : 0;
$moduleid       = ($info->q) ? $info->moduleid : 0;
$apptype        = ($info->q) ? $info->apptype : 0;
$systemsize     = ($info->q) ? $info->systemsize : 0;
$phone          = (($info->q && $info->phone != '') || ($info->phone > 0)) ? $info->phone : 'N/A';
$mobile         = (($info->q && $info->mobile != '') || ($info->mobile > 0)) ? $info->mobile : 'N/A';
$email          = (($info->q && $info->email != '') || ($info->email > 0)) ? $info->email : 'N/A';
//$servno         = (($info->q && $info->servno != '') || ($info->servno > 0)) ? $info->servno : 'N/A';

$check_contract = $this->model_cad->check_contract($dataid);

if($essrno != 0){
    $essrnoview = $essrno;
}else{
    $essrnoview = 'Enter ESSR No.';
}
$requirements = '';

$online = $this->db->select()->from('application_customers_online_ticket_ref')
    ->where(array('appid' => $dataid, 'status' => 1))->get()->row();

$trans = $this->db->select('trmt.*,	tfms.`desc`')
    ->from('transaction_request_main_trails as trmt')
    ->join('prime_transaction_flow_main_stages as tfms','trmt.stageid = tfms.sysid','left')
    ->where('trmt.dataid',$dataid)
    ->order_by('trmt.datecreated','DESC')
    ->get()->row();

$cadtrans = $this->db->select('sysid')
    ->from('prime_transaction_flow_main_stages')
    ->where(
        array(
            'flowid' => $flowid,
            'desc' => 'CAD-Transmital',
            'status' => 1
        )
    )
    ->get()->row();

$trans_level = ($cadtrans) ? $cadtrans->sysid : 0;

if ($trans) {
    $curr_level = $trans->stageid;
}

$corpname = 'Unknown';
$corpbranch = '';

// GET CORP INFO
$qry_corp_app = $this->db->select()
    ->from('application_customers_corporation')
    ->where(array('appid' => $dataid, 'types' => $apptype))
    ->get()->row();


$pic_recent = base_url() . 'assets/global/img/person_default.jpg';
$pic_id = $info->personid;
$pic_dir = 'person';

if($qry_corp_app) {
    $info = array();
    if($apptype == 2) {
        $info = get_corporation_info($qry_corp_app->corpid);
        $pic_dir = 'corporation';
    } else {
        $info = get_government_info($qry_corp_app->corpid);
        $pic_dir = 'government';
    }
    $pic_id = $qry_corp_app->corpid;
    if ($info->qry) {
        $corpname = $info->info->descs;


        if($apptype == 2) {
            $qry_branch = $this->db->select()
                ->from('corporation_branches')
                ->where(array('corpid' => $qry_corp_app->corpid, 'sysid' => $qry_corp_app->branchid))
                ->get()->row();
            if ($qry_branch) {
                $corpbranch = $qry_branch->names;
            }
        }else{
            $corpbranch = ($info) ? $info->info->names : '';
        }
    }
}

$pic_recent = get_owner_pic($pic_id, $pic_dir, 2);

$user_roles = get_users_roles_matrix_id_arr();

$roles_can_edit = array(1, 18, 19);
//1: Admin, 18: CAD (Staff) 19: CADH (Head)
//If any of these roles is assigned to current user, return TRUE
$can_edit = (array_intersect($roles_can_edit,(array)$user_roles)) ? true : false;

?>
<div class="portlet light bordered" data-toggle="editable">
    <div class="portlet-title">
        <div class="caption">
            <?php /*if(($editable==true && $can_edit==true) || super_admin()) { ?>
            <a class="text-danger" href="javascript:;" id="enable_edit"><i class="fa fa-edit"></i></a>
            <?php  }*/ ?>
            <span class="caption-subject font-green-sharp ">
                PAE<?php echo str_pad(($essrno) ? $essrno : $dataid, 6, '0',  STR_PAD_LEFT); ?>
            </span>
            <span class="">
                <?php if(($editable==true && $can_edit==true) || super_admin()) {
                    $profile = ($apptype > 1) ? 'Authorized Representative' : 'Owner';
                ?>
                    <a href="#frm_cad_change_ownership" data-arr="<?php echo $dataid;?>" data-toggle="ajax-modal" title="Profile of <?php echo $profile;?>" class="btn btn-danger btn-xs inline"><i class="fa fa-edit"></i> Edit/Change Profile of <?php echo $profile;?></a>
                <?php  } ?>
            </span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <!--  <?php echo base_url(); ?>uploads/attachments/cad/applications/002855/CONTRACT_71711.pdf -->
            <div class="col-md-3 text-align-center">

                <form id="frm_upload_pic" method="post" action="" enctype="multipart/form-data">
                    <input name="remarks" type="hidden" value="CUSTOMER APPLICATION" />
                    <input name="moduleid" type="hidden" value="<?php echo $moduleid; ?>" />
                    <input name="ownerid" type="hidden" value="<?php echo $pic_id; ?>" />
                    <input name="dataid" type="hidden" value="<?php echo $dataid; ?>" />
                    <input name="dir" type="hidden" value="<?php echo $pic_dir; ?>" />

                    <div class="fileinput fileinput-new fileinput-custom" data-provides="fileinput">
                        <div class="fileinput-new thumbnail" data-trigger="fileinput">
                            <img alt="" class="fileinput-new" src="<?php echo $pic_recent; ?>">
                            <div class="fileinput-preview fileinput-exists thumbnail" >
                            </div>
                        </div>
                        <span class="btn-file">
                        <input type="file" id="emppic" name="newpic">
                        </span>
                        <a id="btn_upload_pic" href="javascript:" class="btn btn-xs btn-circle blue btn-upload fileinput-exists"><i class="fa fa-upload"></i></a>
                        <a href="javascript:" class="btn btn-xs btn-circle btn-remove red fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> </a>
                    </div>
                </form>

                <?php

                if($qry_corp_app) {
                    ?>
                    <center>
                        <hr style="margin: 5px 0px;">
                        <h4 style="margin: 0px; 3px;" class="bold font-red-flamingo">
                            <?php echo $corpname; ?>
                        </h4>
                        <p style="font-size: 12px; margin-top: 5px;">
                            <?php echo $corpbranch; ?>
                        </p>
                    </center>
                    <?php
                }
                ?>
            </div>
            <div class="col-md-9">
                <ul class="list-group summary column no-border">
                    <?php
                    if(trim($appname) != '') {
                        ?>

                        <li class="list-group-item">
                            <?php
                            if($qry_corp_app) {
                                ?>
                                <span class="label-name col-md-3">Signatory</span>
                            <?php } else { ?>
                                <span class="label-name col-md-3">Name</span>
                            <?php  } ?>
                            <span class="label-default col-md-7"><?php echo $appname;?></span>
                            <span class="col-md-2">
                            <?php if ($online) {?>
                                <label class="label label-success pull-right"></label>
                            <?php }?>
                        </span>
                        </li>
                    <?php } ?>

                    <?php
                    if($qry_corp_app == false) {
                        ?>
                        <li class="list-group-item">
                            <span class="label-name col-md-3">
                                Status
                            </span>
                            <span class="label-default col-md-9">
                                <?php echo $status;?>
                            </span>
                        </li>
                    <?php } ?>
                    <li class="list-group-item">
                        <span class="label-name col-md-3">
                            Specific Address
                        </span>
                        <span class="label-default col-md-9">
                            <a href="javascript:" data-type="textarea" data-value="<?php echo $address; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Address" class="" style="display: inline;">  <?php echo $address; ?></a>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-3">
                            Province
                        </span>
                        <span class="label-default col-md-9">
                            <a href="javascript:" id="" data-type="select2" data-value="<?php echo $province; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="District" style="display: inline;">  <?php echo $province; ?></a>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-3">
                            City / Municipality
                        </span>
                        <span class="label-default col-md-9">
                            <a href="javascript:" id="" data-type="select2" data-value="<?php echo $citymun; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Municipality" style="display: inline;">  <?php echo $citymun; ?></a>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-3">
                            Location
                        </span>
                        <span class="label-default col-md-9">
                            <a href="<?php echo $maplink; ?>" id="" data-type="select2" data-value="<?php echo $maplink; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Location" style="display: inline;" target="_blank">  <?php echo ($maplink != 'javascript:;') ? 'Click' : 'N/A' ?></i></a>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-3 label-name">Phone No.</span>
                        <span class="col-md-9 label-default">
                            <a href="javascript:" id="input_phone" data-type="text" data-value="<?php echo $phone; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Phone" style="display: inline;">  <?php echo $phone; ?></a>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-3 label-name">Mobile No.</span>
                        <span class="col-md-9 label-default">
                            <a href="javascript:" id="input_mobile" data-type="text" data-value="<?php echo $mobile; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Mobile" style="display: inline;">  <?php echo $mobile; ?></a>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-3 label-name">Email</span>
                        <span class="col-md-9 label-default">
                            <a href="javascript:" id="input_email" data-type="text" data-value="<?php echo $email; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Email" style="display: inline;">  <?php echo $email; ?></a>
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
                    <li class="list-group-item">
                        <span class="label-name col-md-3">
                            System Size
                        </span>
                        <span class="label-default col-md-9">
                            <?php echo $systemsize;?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="portlet-footer">

        <?php if($editable == true) { ?>

            <div class="btn-group pull-right">
                <?php
                if($qry_corp_app) {
                    if(($editable==true && $can_edit==true) || super_admin()) {
                        ?>
                        <a class="btn btn-danger btn-group inline" href="#form_application_change_corp"
                           title="Change Corporation" data-toggle="ajax-modal"><i class="fa fa-edit"></i> Edit Corporation</a>
                        <?php
                    }
                }
                ?>

                <?php
                if($email != '') {
                    ?>
                    <a href="#form_send_email" title="Send Email: <?php echo $appname; ?>" data-toggle="ajax-modal" data-arr="apply@panayelectric.com,<?php echo $email; ?>" class="btn btn-default btn-group inline"><i class="fa fa-envelope"></i> Send Email</a>
                    <?php
                }
                ?>
                <?php if ($curr_level == $trans_level) {
                    $tno = $this->db->select('sysid,status')
                        ->from('joborders_details_logs')
                        ->where(array('acctid' => $dataid, 'tickettype' => 322, ))
                        ->get()->row();
                    if ($tno) {
                        if ($tno->status == 300)
                            echo '<a href="javascript:;" class="btn btn-warning btn-group inline"><i class="fa fa-exclamation-triangle"></i> With pending TNO</a>';
                    } else {
                        $pinfo = application_info($dataid);
                        $pcontact = ($pinfo && $pinfo->mobile != 'N/A') ?
                            $pinfo->mobile : (($pinfo && $pinfo->phone != 'N/A') ?
                                $pinfo->phone : (($pinfo && $pinfo->email != 'N/A') ?
                                    $pinfo->email : 'N/A'));

                        ?>
                        <form action="<?php echo base_url() ?>jo/savenewjo" class="inline pull-right" method="post"
                              id="frm_jo_newconn">
                            <input class="hidden" name="joborder" value="322">
                            <input class="hidden" name="appid" value="<?php echo $dataid; ?>">
                            <input class="hidden" name="personid" value="<?php echo ($pinfo) ? $pinfo->personid : 0; ?>">
                            <input class="hidden" name="lastname" value="<?php echo ($pinfo) ? $pinfo->lastname : ''; ?>">
                            <input class="hidden" name="firstname" value="<?php echo ($pinfo) ? $pinfo->firstname : ''; ?>">
                            <input class="hidden" name="middlename" value="<?php echo ($pinfo) ? $pinfo->middlename : ''; ?>">
                            <input class="hidden" name="address" value="<?php echo ($pinfo) ? $pinfo->address : ''; ?>">
                            <input class="hidden" name="district" value="<?php echo ($pinfo) ? $pinfo->distid : ''; ?>">
                            <input class="hidden" name="brgy" value="<?php echo ($pinfo) ? $pinfo->barangay : ''; ?>">
                            <input class="hidden" name="contact" value="<?php echo $pcontact; ?>">
                            <input class="hidden" name="repsource" value="3003">
                            <button type="submit" class="btn btn-info inline" style="width: 100%">
                                <i class="fa fa-reply"></i> Proceed to Job Order
                            </button>
                        </form>
                    <?php }
                }
                ?>
            </div>
        <?php } ?>
    </div>
</div>


<?php if($showrate==true) { ?>
    <div class="portlet light bordered ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-edit"></i>
                <span class="caption-subject font-green-sharp bold uppercase">Subscription Location</span>
                <span class="caption-helper">Location of the new account.</span>
            </div>
            <div class="tools">
                <a data-toggle="ajax-modal" data-arr="<?php echo $dataid; ?>" title="Inspection Logs: <?php echo $appname; ?>" href="#tbl_app_inspection_logs"><i class="fa fa-tasks"></i> Inspection Logs</a>
                <a data-toggle="ajax-modal-map" data-id="<?php echo $dataid; ?>" title="Application Map: <?php echo $appname; ?>" href="#showmap"><i class="fa fa-map"></i> View Map</a>
            </div>
        </div>
        <div  class="portlet-body">
            <div class="row">
                <div class="col-md-5">


                    <ul class="list-group summary row no-border">
                        <li class="list-group-item">
                            <span class="label-name col-md-5">Account Type </span>
                            <span class="label-default col-md-7"><?php echo $rateclass; ?></span>
                        </li>

                        <li class="list-group-item">
                            <span class="label-name col-md-5">Payment Type </span>
                            <span class="label-default col-md-7"><?php echo $conntype; ?></span>
                        </li>

                        <li class="list-group-item">
                            <span class="label-name col-md-5">Ownership </span>
                            <span class="label-default col-md-7"><?php echo $ownertype; ?></span>
                        </li>

                    </ul>

                </div>


                <div class="col-md-7">

                    <h5 class="text-info"><i class="fa fa-map-marker fa-fw"></i> <b>Location Details</b></h5>
                    <ul class="list-group summary row no-border">
                        <li class="list-group-item">
                            <span class=" label-name col-md-3">Landmark </span>
                            <span class="label label-default col-md-9 pull-right"><?php echo $landmark; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-3">City / Municipality </span>
                            <span class="label label-default col-md-9 pull-right"><?php echo $citymun; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-3">Map Updated </span>
                            <span class="label label-default col-md-9 pull-right"><?php echo $mapupdated; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-3">Updated By </span>
                            <span class="label label-default col-md-9 pull-right"><?php echo $mapupdatedby; ?> </span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>

<?php } ?>



<!--<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption" bis_skin_checked="1">
            <i class="fa fa-map-o"></i>
            <span class="caption-subject font-red-flamingo bold uppercase"><span class="label label-danger"></span> File Tags</span>
        </div>
        <div class="tools">
                <a href="javascript:;" id="refresh_file_tags"><i class="fa fa-refresh"></i> Refresh</a>
                <?php
if(user_id() == 1) {
    echo '<a href="javascript:;" id="clear_file_tags"><i class="fa fa-times text-danger"></i> Clear</a>';
}
?>
        </div>
    </div>
    <div class="portlet-body">
        <div  id="file_tags" class="mt-element-card mt-element-overlay" >
            <h4><i class="fa fa-refresh fa-spin text-info"></i> Loading files...</h4>
        </div>
    </div>
</div> -->




<?php if($editable==true) { ?>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>



    <script src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
    <script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js"></script>


    <script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

    <script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
    <script src="<?php echo base_url() ;?>assets/admin/pages/scripts/portfolio.js"></script>
    <script src="<?php echo base_url() ;?>assets/admin/pages/scripts/form-fileupload.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
    <script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<?php } ?>

<script>
    /*function myMap() {
        return PECO.handleCustomerMap(<?php echo $dataid; ?>);
    }*/

    $(document).on('submit', '#frm_upload_pic', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url:PECO.base_url() + 'query/uploadpp',
            data: new FormData(form[0]),
            dataType: 'json',
            type: 'post',
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData: false,        // To send DOMDocument or non processed data file it is set to false
        }).done(function(d){
            PECO.initAlerts(d.msg, 'Picture Upload', d.func);
        }).fail(function(){
            alert("ERROR PHP");
        });
    });

    $(document).on('click', '#btn_upload_pic', function(e) {
        e.preventDefault();
        $('#frm_upload_pic', document).trigger('submit');
    });

    CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid;?>);
</script>