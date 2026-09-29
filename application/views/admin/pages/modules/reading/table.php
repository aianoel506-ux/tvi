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


    <div class="select2-inline">

        <h3 class="page-title">
            <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
        </h3>
        <div class="row">
            <form role="form" class="form-horizontal asset-entry-form" id="entry-form-ajaxify">	

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
                            <div class="form-group form-md-line-input col-md-4">
                                <div class="col-md-12">
                                    <label class="text-danger"><i class="fa fa-search"></i> Filter Area (Scheduled)</label>
                                    <input id="district" name="lotbook" type="text" class="form-control  input-sm " placeholder="Disrict">

                                </div>

                                <div class="col-md-12">
                                    <input id="lot_book" name="lotbook" type="text" class="form-control  input-sm " placeholder="Lot & Book">

                                </div>
                            </div>
                            <div class="form-group form-md-line-input col-md-3">
                                <div class="col-md-12">
                                    <label>Meter Reader: </label>
                                    <input id="emp_input" name="empid" type="text" class="form-control  input-sm " placeholder="Meter Reader">
                                </div>
                                <div class="col-md-12 margin-top-10"><button class="btn btn-default"><i class="fa fa-search"></i> Search</button></div>
                            </div>
                            <div class="form-group form-md-line-input col-md-3">
                                <div class="col-md-12">
                                    <label>Reading Date: </label>
                                    <input id="reading_date" name="readdate" type="date" class="form-control input-lg" placeholder="Reading Date">
                                </div>
                            </div>
                            <div class="col-md-2 pull-right margin-top-20">
                                <button class="btn blue btn-block"><i class="fa fa-save"></i> Save</button>
                                <button class="btn btn-xs btn-default btn-block margin-top-10"><i class="fa fa-file-text"></i> Draft</button>
                                <button class="btn btn-xs btn-default btn-block margin-top-10"><i class="fa fa-print"></i> Schedule</button>

                            </div>
                        </div>

                        <div class="portlet-body ">
                            <?php $qry_name = $this->db->select("lastname, firstname, middlename")->from('person')->where('status', 1)->get()->result();?>
                            
                            <hr>
                            <table class="table table-hover table-striped table-condensed table-bordered tbl-sm" id="tbl_analysis_entry">
                                <thead>
                                    <tr>
                                        <th colspan="6" style="text-align: left !important; border-bottom: 1px #ccc solid;">Account Details</th>
                                        <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;">Reading</th>
                                        <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;">Consumption</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>Seq</th>
                                        <th>Service #</th>
                                        <th>Name</th>
                                        <th>Meter #</th>
                                        <th>Meter</th>
                                        <th>Serial</th>
                                        <th width="100px">Previous</th>
                                        <th width="100px">Current</th>
                                        <th width="100px">Previous</th>
                                        <th width="80px">Current</th>
                                        <th width=""><i class="fa fa-arrow-up"></i><i class="fa fa-arrow-down"></i></th>

                                        <th>Status</th>
                                        <th width="100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    for ($i = 1; $i <= 15; $i++) {
                                        $read_rand = rand(10, 1000);
                                        $meter_rand = rand(10000, 99999);
                                        $servno_rand = rand(1000, 9999);
                                        $meter_norand = rand(1000, 99999);
                                        $qry_rand = array_rand($qry_name);
                                        $increase_rand = rand(-60, 100);
                                        $increase_percent = $increase_rand/100;
                                        $rand_meter_no = rand(22333, 33425);
                                        
                                        // reading
                                        $reading_actual_previous = rand(133520, 988298);
                                        $reading_actual_amt_increase = ($reading_actual_previous*$increase_percent);
                                        $reading_actual_present = abs($reading_actual_previous+$reading_actual_amt_increase);
                                        
                                        
                                        $increase_hundreds = $increase_percent*100;
                                        if( $increase_percent > 0 ) {
                                            $bg_color = 'rgba(65,177,38,0.50)';
                                        } else {
                                            $bg_color = 'rgba(252,124,127,0.50)';
                                        }
                                        ?>
                                        <tr class="">
                                            <td>
                                                <i data-id="10" id="btn-expand" class="fa fa fa-plus-square-o"></i>
                                            </td>
                                            <td ><?php echo $i; ?></td>

                                            <td>MD<?php echo str_pad($servno_rand, 5, '0', STR_PAD_LEFT); ?></td>
                                            <td><b><?php echo $qry_name[$qry_rand]->lastname; ?>, <?php echo $qry_name[$qry_rand]->firstname; ?></b></td>
                                            <td><?php echo str_pad( $rand_meter_no, 6, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo str_pad($meter_norand, 6, '0', STR_PAD_LEFT); ?></td>
                                            <td class="text-info center"><?php echo str_pad($meter_rand, 8, '0', STR_PAD_LEFT); ?></td>
                                            <td><div class="input-icon left">
                                                    <i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>
                                                    <input placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="readamt" value="<?php echo $reading_actual_previous; ?>"/>
                                                </div></td>
                                            <td><div class="input-icon left">
                                                    <i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>
                                                    <input placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="readamt" value="<?php echo $reading_actual_present; ?>"/>
                                                </div></td>
                                            <td class="number"><?php echo $read_rand; ?></td>
                                            <td class="number">
                                                <div class="input-icon left">
                                                    <i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>
                                                    <input placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="readamt" value="<?php echo number_format(abs($reading_actual_amt_increase)); ?>"/>
                                                </div>
                                            </td>
                                            <td style="position: relative">
                                                <div style="position: absolute; background-color: <?php echo $bg_color; ?>; width: 100%; height: 100%;
                                                        background: <?php echo $bg_color; ?>; /* For browsers that do not support gradients */
                                                        background: -webkit-linear-gradient(left, <?php echo $bg_color; ?> <?php echo abs($increase_hundreds); ?>%, rgba(255,255,255,0.05)); /* For Safari 5.1 to 6.0 */
                                                        background: -o-linear-gradient(left, <?php echo $bg_color; ?> <?php echo abs($increase_hundreds); ?>%, rgba(255,255,255,0.05)); /* For Opera 11.1 to 12.0 */
                                                        background: -moz-linear-gradient(left, <?php echo $bg_color; ?> <?php echo abs($increase_hundreds); ?>%, rgba(255,255,255,0.05)); /* For Firefox 3.6 to 15 */
                                                        background: linear-gradient(left, <?php echo $bg_color; ?> <?php echo abs($increase_hundreds); ?>%, rgba(255,255,255,0.05));
                                                     "></div>
                                                
                                                <?php echo ($increase_rand>0) ? '<i class="fa fa-angle-double-up text-success fa-fw"></i>' : '<i class="fa fa-angle-double-down text-danger fa-fw"></i>'; ?>
                                                <span class="pull-right"><?php echo $increase_hundreds; ?>% </span>
                                            </td>
                                            
                                            <td>
                                               <?php
                                               if($increase_percent>=0.4 || $increase_percent<=-0.4){
                                                   echo '<i class="fa fa-warning fa-fw text-warning"></i>';
                                               }                                               
                                               ?>
                                            </td>
                                            <td>
                                                <select class="form-control inline" id="sub_findings" data-placeholder="Select.." style="width: 100%">
                                                    <option></option>
                                                    <option>Add Bill - Computed Additional Bill</option>
                                                    <option>Read - Reading Valid</option>
                                                </select>   
                                                
                                            </td>



                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                    <div class="col-md-4">
                                        <ul class="list-group summary">
                                            <li class="list-group-item"> Total KWH (Current): <span class="label label-default pull-right" id="daterange">0</span> </li>
                                            <li class="list-group-item"> Total KWH (Previous): <span class="label label-default pull-right" id="daterange">0</span> </li>

                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group summary">
                                            <li class="list-group-item"> Total Customer: <span class="label label-default pull-right" id="total_customer">0</span> </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary pull-right"><i class="fa fa-reply-all"></i> Send For Billing</button>
                                        <button class="btn btn-default pull-right"><i class="fa fa-print"></i> Print Analysis</button>
                                        
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>		

                    </div>
                </div>

            </form>
        </div>
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


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script> 
<script type="text/javascript">
    MRD.analysis();
</script>