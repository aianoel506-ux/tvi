<!-- BEGIN PAGE LEVEL STYLES -->


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">



<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/clockface/css/clockface.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>


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
</style>
<div class="row">


    <div class="col-md-12">
        <div class="portlet light table">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Analysis</span>
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

            <div class="portlet-body ">
                <?php $qry_name = $this->db->select("lastname, firstname, middlename")->from('person')->where('status', 1)->get()->result(); ?>

                <hr>
                <form class="form-horizontal" id="frm_submit_analysis" method="post" action="<?php echo base_url('mrd/updatereading'); ?>">	
                    <table class="table table-condensed table-bordered tbl-sm" id="tbl_analysis_entry">
                        <thead>
                            <tr>
                                <th colspan="8" style="text-align: left !important; border-bottom: 1px #ccc solid;">Account Details</th>
                                <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;">Consumption</th>
                                <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;">Reading</th>
                                <th colspan="4"></th>
                            </tr>
                            <tr>
                                <th>
                                    <i id="btn-expand-all" class="fa fa-plus-square-o"></i>
                                </th>
                                <th>Seq</th>
                                <th>Service #</th>
                                <th width="130px">Name</th>
                                <th>MTR</th>
                                <th>MTR No.</th>
                                <th>Serial</th>
                                <th>Mult</th>
                                <th width="100px">Current</th>
                                <th width="100px">Previous</th>
                                <th width="80px">Current</th>
                                <th width="100px">Previous</th>
                                <th width=""><i class="fa fa-angle-double-up"></i><i class="fa fa-angle-double-down"></i></th>
                                <th width="90px">Findings</th>
                                <th width="90px">Remarks</th>
                                <th width="90px">Status</th>
                                <th width="40px">Chk</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <hr>
                    
                    <strong style="margin-left: 10px;">Legend:</strong>  
                    <i class="fa fa-eye text-danger"></i> No Initial Reading | 
                    <i class="fa fa-star text-warning"></i> New Reading |
                    <i class="fa fa-edit text-info"></i> Row Updated |
                    <i class="fa fa-angle-double-up text-success"></i> Consumption Increase |
                    <i class="fa fa-angle-double-down text-danger"></i> Consumption Decrease |
                    <i class="fa fa-warning text-warning"></i> Reading Anomaly |
                    <i class="fa fa-check text-success"></i> Reading Okay
                    
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <input type="hidden" class="form-control" id="trnid" value="<?php echo $dataid; ?>" />
                                <button type="button" class="btn btn-primary" id="submit_to_billing"><i class="fa fa-file"></i> Process for Billing</button>

                                <!--
                                <div class="fileinput fileinput-new " data-provides="fileinput">
                                    <span class="btn btn-primary btn-file">
                                        <span class="fileinput-new ">
                                            <i class="fa fa-history"></i> Read Bill </span>
                                        <span class="fileinput-exists">
                                            <i class="fa fa-refresh"></i> Change </span>
                                        <input type="hidden"><input type="file" name="...">
                                    </span>
                                    <span class="fileinput-filename">
                                    </span>
                                    &nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput">
                                    </a>
                                </div>
                                -->
                            </div>
                            <div class="col-md-4 pull-right">
                                <button type="button" class="btn btn-default pull-right" id="print_analysis"><i class="fa fa-print"></i> Print Analysis</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
        </div>		

    </div>
</div>



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

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>



<!-- <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script> -->
<script type="text/javascript" src="https://cdn.rawgit.com/googlemaps/v3-utility-library/master/markerwithlabel/src/markerwithlabel.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script> -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/tp/pages/jquery.googlemap.js"></script>

<script src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script> 
<script type="text/javascript">
    MRD.analysis();
</script>