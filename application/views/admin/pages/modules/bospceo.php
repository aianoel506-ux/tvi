<!-- TESTING --->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">

<style>
.form-md-line-input {
	posiotn: relative !important;	
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

</style>
 

				
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
                        <span class="caption-subject font-green-sharp bold uppercase">PCEO Approved Budgets [modules\bospceo.php]</span>
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
                <div class="portlet-body">
                    <table class="table table-hover table-condensed table-stiped table-bordered tbl-sm table_budget" id="budgetListTable">
                            <thead>
                                <tr>
                                    <th>COST Center</th>
                                    <th>Job-order #</th>
                                    <th>Budget Description</th>
                                    <th>Account Code</th>
                                    <th>Budget Type</th>
                                    <th>Year</th>
                                    <th>Approved Budget</th>
                                    <th>Budget Balance</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>		
                </div>		

            </div>
          </div>
          
          </form>
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/bos/bos.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/bos/list.js"></script>
<script>
    BOS.viewBudgets();
</script>
