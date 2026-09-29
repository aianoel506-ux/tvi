<?php

$last = $this->uri->total_segments();
$id = $this->uri->segment($last);


$appname = application_info($id)->appname;
$status = application_info($id)->status;
$address = application_info($id)->address;
$datecreated = application_info($id)->datecreated;
$tinno = application_info($id)->tinno;
$landmark = application_info($id)->landmark;
$distname = application_info($id)->distname;
$gdlb = application_info($id)->gdlb;
$mapupdated = application_info($id)->mapupdated;
$mapupdatedby = application_info($id)->mapupdatedby;
$conntype = application_info($id)->conntype;
$ownertype = application_info($id)->ownertype;
$rateclass = application_info($id)->rateclass;
$landtype = application_info($id)->landtype;
$totalload = application_info($id)->totalload;
$essrno = application_info($id)->essrno;

?>

<div class="tab-pane fade in" id="data">
    <div class="row">
        <div class="col-md-7">

            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                        <span class="caption-helper">General</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="" title="">
                        </a>


                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                        </a>


                        <a href="javascript:;" class="reload" data-original-title="" title="">
                        </a>


                        <a href="javascript:;" class="fullscreen" data-original-title="" title="">
                        </a>


                        <a href="javascript:;" class="remove" data-original-title="" title="">
                        </a>


                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-3 text-align-center">
                            <img src="<?php echo base_url(); ?>assets/global/img/person_default.jpg" height="130px" />
                        </div>
                        <div class="col-md-9">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                           <span class="label-name col-md-2">
                                               Name
                                           </span>
                                    <span class="label-default col-md-10">
                                               <?php echo $appname;?>
                                           </span>
                                </li>
                                <li class="list-group-item">
                                           <span class="label-name col-md-2">
                                               Status
                                           </span>
                                    <span class="label-default col-md-10">
                                               <?php echo $status;?>
                                           </span>
                                </li>
                                <li class="list-group-item">
                                           <span class="label-name col-md-2">
                                               Address
                                           </span>
                                    <span class="label-default col-md-10">
                                               <?php echo $address;?>
                                           </span>
                                </li>
                                <li class="list-group-item">
                                           <span class="label-name col-md-2">
                                               Date Posted
                                           </span>
                                    <span class="label-default col-md-10">
                                               <?php echo $datecreated;?>
                                           </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>


            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Subscription Location</span>
                        <span class="caption-helper">mapping and specific geodata</span>
                    </div>

                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-5">
                            <h5 class="text-info"><i class="fa fa-map-o fa-fw"></i> <b>Default Map</b></h5>

                            <div id="custmap" style="width: 100%; height: 250px;"></div>
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

        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <h3 class="font-red-haze">Installation Function</h3>
                            </div>
                        </div>
                        <div class="portlet-body install-btns">

                            <a data-id="376" id="send_out_installation" data-func="info" title="Send out installation procedure" href="javascript:;" class="btn btn-primary install-function">Send out</a>
                            <a data-id="311" id="send_ongoing_installation" data-func="warning" title="Set status to on-going installation" class="btn bg-red-flamingo font-white  install-function" href="javascript:;">On-Going</a>
                            <a data-id="314" id="send_done_installation" data-func="success" title="Set status to done installation" class="btn btn-success  install-function" href="javascript:;">Finished</a>
                            <a title="Item Returned Data Entry" href="#frm_installation_item_returned" data-toggle="ajax-modal" class="btn btn-default">Returned</a>
                            <a title="Print Trip Ticket" href="javascript:;" class="btn btn-default pull-right"><i class="fa fa-print"></i> Trip Ticket</a>
                            <hr>
                            <h3>
                                <div class="btn-group pull-right">
                                    <a title="Team Assignment" data-arr="<?php echo $dataid; ?>, 47" href="#frm_add_team_member" data-toggle="ajax-modal" class="btn btn-primary inline"><i class="fa fa-refresh"></i> Add Team</a>
                                    <a id="btn_refresh_team_list" title="Refresh" class="btn btn-danger inline"><i class="fa fa-refresh"></i> Reload List</a>
                                </div>

                                Team Assigned</h3>

                            <table class="table table-striped table-hover" id="tbl_inspection_team">
                                <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Del</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <?php
                        echo customer_application_view_right($dataid);
                    ?>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/assets-powerplant/assetspowerplant.js" type="text/javascript"></script>
<script>
    PECO.initMapSpec('#custmap', <?php echo $dataid; ?>, 0);
    POWERPLANT.installation(<?php echo $dataid; ?>, 47);
</script>