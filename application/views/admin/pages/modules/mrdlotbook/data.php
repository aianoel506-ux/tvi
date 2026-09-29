<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/fixedcolumn/css/fixedColumns.bootstrap.css"/>

<style>
    .asset-pic {
        display: inline-block;
    }

    .asset-pic .main {
        width: 100%;
    }

    .asset-pic .sub {
        width: 30%;
        height: 90px;
    }

    .asset-pic .sub.more {
        border: 1px solid #ccc;
    }
</style>
<?php

$country = '';
$city = '';
$district = '';
$total_load = acct_total_loads($dataid);
$info = application_info($dataid);
$distid = ($info->q) ? $info->distid : 0;
$gdlbid = ($info->q) ? $info->gdlbid : 0;

$account_type = 'Regular';
if ($total_load >= 5000) {
    $account_type = 'Spacial';
}


?>

<div class="row">
    <div class="col-md-8">
        <?php
        customer_application_basicinfo($dataid, true);
        ?>
    </div>

    <div class="col-md-4">


        <div class="row">
            <div class="col-md-12">
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-map-marker"></i>
                            <span class="caption-subject">GDLB Assignment</span>
                            <span class="caption-helper"></span>
                        </div>
                    </div>
                    <div class="portlet-body">

                        <ul class="list-group summary column no-border">

                            <li class="list-group-item">
                                <span class=" label-name col-md-4">Total Load</span>
                                <span class="label label-default col-md-4 number">
                                     <?php echo number_format($total_load); ?>
                                </span>
                                <span class="col-md-4 number text-danger bold"><?php echo ($total_load>5000) ? 'Special' : 'Regular'; ?></span>

                            </li>

                        </ul>

                        <hr>
                        <h4 class="font-blue" style="margin: 10px 0px !important;">GDLB Assignment</h4>


                        <div class="form-group">
                            <input type="hidden" value="<?php echo $distid;?>" id="district_id">
                                <div class="input-group" style="width: 100%;">
                                    <label class="input-group-addon" for="input_lnb_all">
                                        Show All
                                        <input type="checkbox" id="input_lnb_all" class="form-control input-lg">
                                    </label>
                                    <input class="form-control" id="custom_gdlb" placeholder="GDLB Select.." />

                                    <div class="input-group-btn">
                                        <button class="btn btn-primary" app-id="<?php echo $dataid; ?>" id="changegdlb">Save</button>
                                    </div>
                                </div>
                        </div>

                        <ul class="list-group summary column ">
                            <li class="list-group-item">
                                <span class="label-name col-md-4">District</span>
                                <span class="label-default col-md-8 number" id="gdlbdist"></span>
                            </li>
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Limit</span>
                                <span class="label-default col-md-8 number" id="gdlblimit"></span>
                            </li>
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Customer</span>
                                <span class="label-default col-md-8 number" id="gdlbcust"></span>
                            </li>

                        </ul>

                        <div class="note note-danger" style="margin: 0px 0px; margin-top: 15px;">
                            <span class="text-danger" id="">Re-assign GDLB / Do not changed if not applicable</span>
                        </div>
                    </div>
                </div>
            </div>
        </div><div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Near Meters</span>
                            <span class="caption-helper"></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table width="100%" class="table table-condensed table-striped table-hover" id="tbl_near_meter_list">
                            <thead>
                            <th>#</th>
                            <th>Meter No.</th>
                            <th>Service No.</th>
                            <th>Address</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!--
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Location</span>
                            <span class="caption-helper"></span>
                        </div>

                    </div>


                    <div class="portlet-body">
                        <div id="custmap" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
        -->


    </div>
</div>




<!--

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

-->
<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/mrd/mrd.js"></script>
<script>
    MRD.init();
    MRD.nearmtr(<?php echo $dataid; ?>);
    MRD.selectgdlb($('#custom_gdlb'), <?php echo $gdlbid; ?>);
    CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid;?>);
</script>

