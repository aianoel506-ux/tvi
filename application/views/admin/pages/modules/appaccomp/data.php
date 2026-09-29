<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css"/>


<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>

<?php
// QUERIES GO TO peco_helper.php : 1114

$last = $this->uri->total_segments();
$id = $this->uri->segment($last);


$appname = $this->model_cad->application_info($id)->appname;
$status = $this->model_cad->application_info($id)->status;
$address = $this->model_cad->application_info($id)->address;
$datecreated = $this->model_cad->application_info($id)->datecreated;
$tinno = $this->model_cad->application_info($id)->tinno;
$landmark = $this->model_cad->application_info($id)->landmark;
$distname = $this->model_cad->application_info($id)->distname;
$gdlb = $this->model_cad->application_info($id)->gdlb;
$mapupdated = $this->model_cad->application_info($id)->mapupdated;
$mapupdatedby = $this->model_cad->application_info($id)->mapupdatedby;
$conntype = $this->model_cad->application_info($id)->conntype;
$ownertype = $this->model_cad->application_info($id)->ownertype;
$rateclass = $this->model_cad->application_info($id)->rateclass;
$landtype = $this->model_cad->application_info($id)->landtype;
$totalload = $this->model_cad->application_info($id)->totalload;
$essrno = $this->model_cad->application_info($id)->essrno;
?>
    <div class="row">
        <div class="col-md-8">
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
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item">
                                           <span class="label-name col-md-2">
                                               Name
                                           </span>
                                            <span class="label-default col-md-10">
                                               <?php echo $appname;?><span class="pull-right text-danger text-bold"><?php echo $servno;?></span>
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

                    <div class="row">
                        <div class="col-md-12 col-sm-12">
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


                    <div class="row">
                        <div class="col-md-6">

                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-edit"></i>
                                        <span class="caption-subject font-green-sharp bold uppercase">Assessment</span>
                                        <span class="caption-helper"></span>
                                    </div>

                                </div>
                                <div class="portlet-body">

                                    <ul class="list-group summary row no-border">
                                        <li class="list-group-item">
                                            <span class="label-name col-md-5">Total Assessment Load </span>
                                            <span class="label-default col-md-7" style="color: red !important;"><?php echo number_format($totalload,2); ?>
                                                <span class="label label-danger pull-right"><?php echo ($totalload>5000) ? 'Special' : 'Regular'; ?></span>
                                    </span>
                                        </li>

                                        <li class="list-group-item">
                                            <span class=" label-name col-md-5">Job Type </span>
                                            <span class="label label-default col-md-7 pull-right"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class=" label-name col-md-5">Deposit </span>
                                            <span class="label label-default col-md-7 pull-right">
                                        <?php echo number_format(acct_total_desposit($dataid, $moduleid), 2); ?>
                                    </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class=" label-name col-md-5">Requirements </span>

                                            <span class="label label-default col-md-7">
                                        <?php echo $requirements; ?>

                                    </span>
                                        </li>

                                    </ul>


                                </div>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-edit"></i>
                                        <span class="caption-subject font-green-sharp bold uppercase">Account Details</span>
                                        <span class="caption-helper"></span>
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
                                <span class="portlet-body">
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

                        </div>

                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-4">

            <div class="portlet light table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-tachometer"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase">Near Meters</span>
                        <span class="caption-helper">nearest meters on the location.</span>
                    </div>

                </div>
                <div class="portlet-body">
                    <table class="table table-hover table-condensed table-borderd table-stripped" id="tbl_nearmeter">
                        <thead>
                        <th><i class="fa fa-reorder"></i></th>
                        <th>Meter</th>
                        <th>Account</th>
                        </thead>
                        <tbody>
                        <?php
                        for($i = 1; $i<=3; $i++) {
                            echo '<tr>';
                            echo '<td>'.$i.'</td>';
                            echo '<td>3232'.$i.'</td>';
                            echo '<td>D1234'.$i.'</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <hr>

            <div class="portlet light table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Meter Assignment</span>
                        <span class="caption-helper">assigned by : name</span>
                    </div>

                </div>
                <div class="portlet-body">
                    <ul class="list-group summary column no-border">
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">Asset Code </span><span class="label label-default col-md-8 pull-right"><span id="assetcode">N/A</span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">Brand </span><span class="label label-default col-md-8 pull-right"><span id="brand"> N/A</span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">Amp </span><span class="label label-default col-md-8 pull-right"><span id="amp"> N/A</span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">Volts </span><span class="label label-default col-md-8 pull-right"><span id="volts"> N/A</span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">Mult Code </span><span class="label label-default col-md-8 pull-right"><span id="mult"> N/A</span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">Initl. Reading </span><span class="label label-default col-md-8 pull-right"><span id="initread"> N/A</span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">ERC Seal </span><span class="label label-default col-md-8 pull-right"><span id="ercseal"> N/A</span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-4">PECO Seal </span><span class="label label-default col-md-8 pull-right"><span id="pecoseal"> N/A</span></span>

                        </li>
                    </ul>

                    <hr>


                    <form class="form-horizontal" id="frm_execute_accomplishments" method="post" action="<?php echo base_url('cad/executeaccomplishment'); ?>">
                        <input type="hidden" name="dataid" value="<?php echo $dataid;?>" />
                        <input type="hidden" name="moduleid" value="<?php echo $origin;?>" />
                        <input type="hidden" name="trailid" value="<?php echo $trailid;?>" />
                        <!--
                        <div class="form-body">


                            <div class="form-group form-md-line-input">
                                <label class="col-md-4 control-label" for="multcode">Mult Code</label>
                                <div class="col-md-8">
                                    <input class="form-control" id="multcode" name="multcode" placeholder="Mult Code" type="text">
                                    <div class="form-control-focus"></div>
                                </div>
                            </div>

                            <div class="form-group form-md-line-input">
                                <label class="col-md-4 control-label" for="initread">Initial Reading</label>
                                <div class="col-md-8">
                                    <div class="form-control-focus"></div>
                                </div>
                            </div>

                            <div class="form-group form-md-line-input">
                                <label class="col-md-4 control-label" for="ercseal">ERC Seal</label>
                                <div class="col-md-8">
                                    <div class="form-control-focus"></div>
                                </div>
                            </div>

                            <div class="form-group form-md-line-input">
                                <label class="col-md-4 control-label" for="remarks">Remarks</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" id="remarks" name="remarks" placeholder="Remarks"></textarea>
                                    <div class="form-control-focus"></div>
                                </div>
                            </div>
                        </div>
                        -->

                        <div class="form-group form-md-line-input">
                            <label class="col-md-4 control-label" for="condate">Date Of Accomplishment</label>
                            <div class="col-md-8">
                                <input class="form-control" id="condate" name="condate" placeholder="Enter Date" type="date">
                                <div class="form-control-focus"></div>
                            </div>
                        </div>

                        <div class="form-group form-md-line-input">
                            <label class="col-md-4 control-label" for="conby">Executed By</label>
                            <div class="col-md-8">
                                <input class="form-control" id="conby" name="conby" placeholder="Enter name" type="text">
                                <div class="form-control-focus"></div>
                            </div>
                        </div>

                        <div class="form-actions margin-top-20 clearfix">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn blue">Execute</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>


<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/assets-powerplant/assetspowerplant.js" type="text/javascript"></script>


<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/accomplishment.js"></script>

<script>
    PECO.initMapSpec('#custmap', <?php echo $dataid; ?>, 0);
    ACCOMPLISHMENT.init();
    POWERPLANT.meterrealease(<?php echo $dataid ?>);
</script>
