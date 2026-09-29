<!-- BEGIN PAGE LEVEL STYLES -->


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>


<!-- END PAGE LEVEL STYLES -->


<style>
    .form-md-line-input {
        position: relative !important;
    }
    .form-md-line-input .fileinput .input-group-addon{
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }
    .form-md-line-input .select2-container{
        margin-bottom: 0px !important;
    }
    .select2-drop{
        margin-top: -15px !important;
    }
    .portlet.table {
        padding: 0px 0px !important;
    }

    .table-condensed .md-checkbox.checkonly {
        width: 20px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
    .table-condensed .md-checkbox.checkonly label {
        width: 20px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
    .table thead {

        background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjMwJSIgc3RvcC1jb2xvcj0iI2Y2ZjZmNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjMwJSIgc3RvcC1jb2xvcj0iI2Y2ZjZmNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNlNWU1ZTUiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+) !important;
        background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 30%, #f6f6f6 30%, #e5e5e5 100%) !important;
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(30%,#f6f6f6), color-stop(30%,#f6f6f6), color-stop(100%,#e5e5e5)) !important;
        background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ) !important;

    }
    .table tr.odd td.zui-sticky-col
    {
        background: rgba(73,169,255,0.30) !important;
    }
    .table tr.even td.zui-sticky-col
    {
        background: rgba(73,169,255,0.15) !important;
    }
    td.highlight {
        background-color: whitesmoke !important;
    }
    .table td {
        position: relative;
        min-height: 50px !important;
    }
    .table td .list-group-item{
        position: relative;
    }

    .DTFC_LeftBodyWrapper {
        z-index: 200;
        background: #fff;
        bottom: -20px !important;
        margin-bottom: -20px !important;
    }

    .table#tbl_schedule_calendar td .btn {
        display: none;
        z-index: 100;
    }

    .table#tbl_schedule_calendar td .btn#btn_edit {
        position: absolute;
        top: 0px;
        right: -20px;
        padding: 1px 2px !important;
    }
    .table#tbl_schedule_calendar td .btn#btn_delete_all {
        position: absolute;
        top: 20px;
        right: -20px;
        padding: 1px 2px !important;
    }

    .table#tbl_schedule_calendar td .btn#btn_delete {
        position: absolute;
        top: -3px;
        right: -25px;
    }
    .table td.date-gdlb:hover {
        border-color: #FFF;
        -webkit-box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        -moz-box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        -o-box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        z-index: 100;
    }
    .table td:hover .btn#btn_delete_all,
    .table td:hover .btn#btn_edit{
        display: inline-block;
    }
    .table td .list-group-item:hover .btn#btn_delete {
        display: inline-block;
    }
    .dataTables_scrollHead {
        overflow: hidden !important;
    }
    .table .list-group.list-group-xs .list-group-item {
        height: 20px !important;
    }
    .list-group.list-group-xs .list-group-item span,
    .list-group.list-group-xs .list-group-item span.label-name{
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }

    .list-group.list-group-xs .list-group-item span.label-name::after {
        top: -2px !important;
    }

    .custom-menu {
        display: none;
        z-index: 1000;
        position: absolute;
        overflow: hidden;
        border: 1px solid #CCC;
        white-space: nowrap;
        font-family: sans-serif;
        background: #FFF;
        color: #333;
        border-radius: 5px;
        padding: 0;
    }

    /* Each of the items in the list */
    .custom-menu li {
        padding: 8px 12px;
        cursor: pointer;
        list-style-type: none;
        transition: all .3s ease;
        user-select: none;
    }

    .custom-menu li:hover {
        background-color: #DEF;
    }
</style>

<?php
$first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
$last_day_this_month  = date('Y-m-t');
?>


<div class="row margin-bottom-30">

    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">

                <div class="form-group" style="float: left; display:inline-block;">
                    Date Range
                    <div class="input-group input-large date-picker input-daterange" id="schedule_date_range" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="text" class="form-control" name="from" id="schedule_date_start" value="<?php echo $first_day_this_month; ?>">
                        <span class="input-group-addon"> to </span>
                        <input type="text" class="form-control"  name="to" id="schedule_date_end" value="<?php echo $last_day_this_month; ?>">
                    </div>
                </div>
                <div class="form-group" style="float: left; margin-left: 10px; display:inline-block;">

                    Billing Date
                    <div class="input-group input-large" id="schedule_date_billing">
                        <span class="input-group-addon"> Yr. </span>
                        <input type="text" class="form-control" style="width: 80px;" name="billyr" id="schedule_billyr" value="<?php echo date('Y');?>">
                        <span class="input-group-addon"> Mo. </span>
                        <select style="width: 150px;" class="form-control select2" name="billmo" id="schedule_billmo">
                            <?php

                            for($i=1; $i<=12; $i++) {
                                if($i==date('m')) {
                                    $selected = 'selected';
                                }else{
                                    $selected = '';
                                }
                                echo '<option '.$selected.' value="'.$i.'">'.date_formating($i, 'm', 'F').'</option>';
                            }
                            ?>
                        </select>
                        <span class="input-group-btn">

                            <button id="btn_get" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group" style="float: left; margin-left: 10px; display:inline-block;">

                    <button id="btn_toggle_weekend" class="btn btn-default active" style="margin-top: 20px;"><i class="fa fa-search"></i> Weekend</button>
                </div>

                <div class="form-group" style="float: right; margin-left: 10px; display:inline-block;">
                    Copy Schedules
                    <div class="input-group input-large" id="schedule_date_billing_replicate">
                        <span class="input-group-addon"> Yr. </span>
                        <input style="width: 80px;" type="text" class="form-control" name="billyr" id="schedule_billyr" value="<?php echo date('Y');?>">
                        <span class="input-group-addon"> Mo. </span>
                        <select style="width: 150px;" class="form-control select2" name="billmo" id="schedule_billmo">
                            <?php

                            for($i=1; $i<=12; $i++) {
                                if($i==date('m')) {
                                    $selected = 'selected';
                                }else{
                                    $selected = '';
                                }
                                echo '<option '.$selected.' value="'.$i.'">'.date_formating($i, 'm', 'F').'</option>';
                            }
                            ?>
                        </select>
                        <span class="input-group-btn">
                            <button class="btn btn-default"><i class="fa fa-files-o"></i> Copy</button>
                        </span>
                    </div>
                </div>

            </div>
            <div class="portlet-body">
                <div class="col-md-6 pull-left">
                    <h4><i class="fa fa-calendar text-success"></i> Schedule Calendar</h4>
                </div>
                <table class="zui-table table table-hover table-striped table-bordered" id="tbl_schedule_calendar">
                    <thead>
                    <tr></tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--
                <div id="container_schedule_calendar">

                </div>
                -->


                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon">
                                Extract
                            </span>
                            <input class="form-control" id="input_user_mrd" placeholder="Select user..." />
                            <span class="input-group-btn">
                                <a class="btn btn-default"><i class="fa fa-cloud-download"></i></a>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <?php
                        if(user_id() == 1) {
                            echo '<a href="javascript:;" title="Admin function only! Delete all development testing data!" type="button" id="btn_truncate_temp_data" class="btn btn-danger pull-right" data-placement="left" data-toggle="tooltips"><i class="fa fa-times"></i> Truncate Testing Data</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->



    <div class="modal fade draggable-modal" id="edit_reader_spec" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo base_url('mrd/assignacctmeterreader'); ?>" method="post" id="frm_assign_specific_reader">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Account Specific Reader</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Assigned Reader(s): </label>
                            <input type="hidden" class="form-control" id="acctid" value="" name="acctid" />
                            <input class="form-control" id="meter_reader_input" value="" placeholder="Select meter reader" name="users"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn blue">Assign</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>


<hr>
<hr>
<hr>

<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>




<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>



<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>



<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>


<script src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script>
<script type="text/javascript">
    MRD.scheduler();
</script>
