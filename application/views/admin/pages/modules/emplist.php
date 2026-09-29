
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">


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
       
        	<div class="col-md-9">
            <div class="portlet light table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Reading History</span>
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
					<table class="table table-hover table-stipped table-condensed table-bordered" id="tbl_reading_hist">
                    	<thead>
                        	<th>#</th>
                        	<th>Seq</th>
                        	<th>Service #</th>
                        	<th>Name</th>
                        	<th>Meter #</th>
                        	<th>Reading</th>
                        	<th>Date Read</th>
                            <th>Action</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                        	<?php for($i = 1; $i<=10; $i++) { ?>
                        	<tr>
                            	<td><?php echo $i; ?></td>
                            	<td>0</td>
                            	<td>MD<?php echo str_pad($i, 4, '0', STR_PAD_LEFT); ?></td>
                            	<td>Lucky John Faderon</td>
                            	<td><?php echo str_pad($i, 4, '0', STR_PAD_LEFT); ?></td>
                            	<td><div class="input-icon left">
                                <i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>
                                <input placeholder="0" class="form-control input-xs inline" style="width: 90px;" id="readamt" /></div></td>
                            	<td><?php echo date('Y-m-d h:m:i');?></td>
                                <td>
                                <select class="form-control input-xs inline" id="readstat">
                                	<option value="">Select..</option>
                                	<option value="1">Read</option>
                                	<option value="2">Re-Read</option>
                                	<option value="3">Add Bill</option>
                                </select>
                                </td>
                                <td><span class="label label-warning">Draft</span></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    </div>		

            </div>
          </div>
          <div class="col-md-3">
          	<div class="portlet light">
               <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-wrench"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Reading Filter</span>
                        <br>
                        <span class="caption-helper">Filter per lot and book to display specific area.</span>
                    </div>
                </div>
                <div class="portlet-body">

                <div class="form-group form-md-line-input">
                	<div class="col-md-12">
                    	<label>Lot & Book: </label>
                         <input id="lot_book" name="lotbook" type="text" class="form-control  input-sm " placeholder="Specific Area Group">
                       
                    </div>
                </div>
                <div class="form-group form-md-line-input" style="margin-top: -20px">
               		<div class="col-md-12">
                    	<label>Meter Reader: </label>
                    	<input id="emp_input" name="empid" type="text" class="form-control  input-sm " placeholder="Meter Reader">
                    </div>
                </div>
                <div class="form-group form-md-line-input" style="margin-top: -20px">
               		<div class="col-md-12">
                    	<label>Reading Date: </label>
                    	<input id="reading_date" name="readdate" type="text" class="form-control  input-sm " placeholder="Reading Date">
                    </div>
                </div>
                        
               
                <input class="form-control" name="moduleid" readonly type="hidden" value="<?php echo $this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid;?>" />
                    
                </div>
                <div class="form-actions margin-top-20 clearfix">
                
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button type="submit" id="save" class="btn blue btn-block btn-lg red-stripe"><i class="fa fa-save fa-fw"></i> Save</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button type="button" id="draft" class="btn green btn-block red-stripe"><i class="fa fa-edit fa-fw"></i> Draft</button>
                        </div>
                    </div>
                    
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

<script>

$(function(){
	// CHANGE THIS TO FUNCTION THAT WILL REVIEW EACH FORM INSIDE ROWS
	$('#tbl_reading_hist').on('blur', 'tr #readamt, tr #readstat', function(e){
		e.preventDefault();
		var input = $(this);
		row_validation(input);
	});
	
	function row_validation(input){
		var value = input.val();
		var tr = input.closest('tr');
		var stat = tr.find('#readstat');
		if(stat.val()=='' && value!=''){
			stat.closest('td').addClass('danger');
			tr.addClass('has-success');
		}else{
			if((value!='' || value>0) && stat.val()!=''){
				tr.addClass('success');	
			}else{
				tr.removeClass('success');	
			}
			stat.closest('td').removeClass('danger');
			if(stat.val()!='' && value!=''){
				tr.addClass('has-success');
			}else{
				tr.removeClass('has-success');
			}
		}
	}
	
	$('#entry-form-ajaxify').submit(function(e){
		$.this_form = $(this);
		e.preventDefault();
		$.ajax({
			url: base_url+"admin/new_asset_entry",
			type:'POST',
			data: $.this_form.serialize(),
			beforeSend: function(){
				$('#stat-qry').html('Processing...');
			},
		}).done(function(data){
			setTimeout(function(){
				PECO.initAlerts(data, 'Alert: Asset', 'success');
			},1000);
		}).fail(function(data){
			console.log(data);	
		});
	});
	
 
	$("#reading_date").inputmask("y-m-d", {
            autoUnmask: true
    }); 
	
	$("#emp_input").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_users",
			dataType: 'json',
			quietMillis: 100,
			data: function (term) {
				return {
					term: term
				};
			},
			results: function (data) {
				var myResults = [];
				$.each(data, function (index, item) {
					myResults.push({
						'id': item.id,
						'text': item.text
					});
				});
				return {
					results: myResults
				};
			}
			
		},
		
    }).change(function(){
		// ADD AJAX UPDATE IF APPLICABLE //
		console.log('TYPE: '+$(this).val());
	});
	
	$("#lot_book").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_users",
			dataType: 'json',
			quietMillis: 100,
			data: function (term) {
				return {
					term: term
				};
			},
			results: function (data) {
				var myResults = [];
				$.each(data, function (index, item) {
					myResults.push({
						'id': item.id,
						'text': item.text
					});
				});
				return {
					results: myResults
				};
			}
			
		},
		
    }).change(function(){
		// ADD AJAX UPDATE IF APPLICABLE //
		console.log('TYPE: '+$(this).val());
	});

 
});
</script>
