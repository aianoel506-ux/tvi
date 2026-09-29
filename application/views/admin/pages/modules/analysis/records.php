<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css" />
<!-- BEGIN PAGE LEVEL STYLES -->


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/clockface/css/clockface.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>


<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
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

    .select2-drop{
        margin-top: 1px !important;
        border-color: #C8C8C8;
        box-shadow: rgba(179,179,179,0.50) 0px 5px 5px 0px;
        background: rgba(255,255,255,0.95);
    }

    .editable-input {
        width: 100% !important;
    }
    .editable-input .form-control {
        width: 100% !important;
    }
    .editable-input label {
        width: 100% !important;
    }
    .editable-buttons {
        display: inline-block;
        margin: 0px 0px;
        padding-top: 10px;
        margin-top: 10px;
        width: 100% !important;
        border-top: 1px solid #f7f7f7;
    }

    .select2-results li.select2-result {
        height: auto !important;
        overflow: visible !important;
    }
</style>
<div class="row margin-bottom-30" >

        <div class="col-md-12">
            <div class="portlet light table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Reading Entry</span>
                        <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
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
                <div class="table-toolbar">
                    <div class="col-md-12 well"  style="margin-bottom: 0px !important;">
                        <form id="frm_upload_extfile" action="<?php echo base_url('mrd/uploadextfile'); ?>" method="post">

                            <div class="col-md-4">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new"> <i class="fa fa-file fa-fw"></i> Select file </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="extfile"> </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6 pull-right">
                                <div class="input-group pull-right">
                                    <select style="width: 100%; display: inline-block;" id="showall" name="showall" type="text" class="form-control input-xs" >
                                        <option value=""></option>
                                        <option value="3">All</option>
                                        <option value="1">Re-Check</option>
                                        <option value="2">For Billing</option>
                                    </select>
                                    <span class="input-group-btn" style="padding-left: 5px;">
                                        <button id="btn_upload_file" type="submit" class="btn btn-sm btn-success"><i class="fa fa-upload"></i> Upload File </button">
                                        <button id="btn_generate_addbill" type="button" class="btn btn-sm btn-warning"><i class="fa fa-calculator"></i> Compute <span class="badge badge-danger">A</span></button>
                                        <button id="btn_print_report" type="button" class="btn btn-sm btn-default"><i class="fa fa-print"></i> Print Analysis Report</button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="portlet-body ">
                    <?php $qry_name = $this->db->select("lastname, firstname, middlename")->from('person')->where('status', 1)->get()->result();?>
                    <div class="pull-left col-md-6" style="padding: 10px 10px; margin-bottom: 10px;">
                        <span class="label label-success">(B) Regular Billing</span>
                        <span class="label label-warning">(A) Add Bill</span>
                        <span class="label label-danger">(R) Recheck Reading</span>
                    </div>
                    <div class="pull-right col-md-4" style="padding: 10px 10px; margin-bottom: 10px;">

                    </div>
                    <form id="frm_analysis_data" action="" method="post" >
                        <span class="hidden" id="total_customer">0</span>

                        <table width="100%" class="table table-condensed table-bordered table-striped table-hover tbl-sm" id="tbl_reading_entry">
                            <thead>
                            <tr>
                                <th colspan="7" style="text-align: left !important; border-bottom: 1px #ccc solid;" class="info">Account Details</th>
                                <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;" class="warning">Reading</th>
                                <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;" class="danger">Consumption / KWH</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th colspan="3" >Status</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th><i class=""></i></th>
                                <th>Servno</th>
                                <th>Name</th>
                                <th>MTR</th>
                                <th>Meter</th>
                                <th>Serial</th>
                                <th>Mult</th>
                                <th width="100px">Present</th>
                                <th width="100px">Previous</th>
                                <th width="80px">Present</th>
                                <th width="100px">Previous</th>
                                <th width="100px">Findings</th>
                                <th width="80px">Demand</th>
                                <th width="80px">Net.Mtr</th>
                                <th width="110px"><span class="pull-left"><i class="fa fa-angle-double-up"></i><i class="fa fa-angle-double-down"></i> Inc/Dec</span></th>
                                <!-- <th>B</th> -->
                                <th>A</th>
                                <th>R</th>
                                <th><i class="fa fa-print text-primary"></i></th>
                                <th width="30px"><i class="fa fa-wrench"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </form>

                    <!--
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <ul class="list-group summary">
                                    <li class="list-group-item"> Total KWH (Present): <span class="label label-default pull-right" id="total_kwh_curr">0</span> </li>
                                    <li class="list-group-item"> Total KWH (Previous): <span class="label label-default pull-right" id="total_kwh_prev">0</span> </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <ul class="list-group summary">
                                    <li class="list-group-item"> Average KWH (Present): <span class="label label-default pull-right" id="ave_kwh_curr">0</span> </li>
                                    <li class="list-group-item"> Average KWH (Previous): <span class="label label-default pull-right" id="ave_kwh_prev">0</span> </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <ul class="list-group summary">
                                    <li class="list-group-item"> Total Cust.: <span class="label label-default pull-right" id="total_customer">0</span> </li>
                                    <li class="list-group-item"> Total Cust. w/ Read: <span class="label label-default pull-right" id="total_cust_wread">0</span> </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <ul class="list-group summary">
                                    <li class="list-group-item"> Cust. w/ Zero/Recheck:
                                        <span class="label label-default pull-right" >
                                            <span id="total_cust_zero" style="color:#ffb856 ">0</span> /
                                            <span id="total_cust_recheck" style="color: #ff6456">0</span>
                                        </span>
                                    </li>
                                    <li class="list-group-item"> Cust. w/ For Billing: <span class="label label-default pull-right" id="total_cust_forbilling">0</span> </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                    <hr>

                    -->
                </div>

            </div>
        </div>

</div>

<hr>
<hr>
<hr>
<hr>
<hr>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->

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

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>



<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo google_api_key(); ?>&callback=initMap"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script>
<script type="text/javascript">
    MRD.outsource();
</script>