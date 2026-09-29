
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
</style>

				
				<h3 class="page-title">
				Asset <small>2015-08-02-CPU0221-400 </small>
                <span class="pull-right"><i class="fa fa-edit fa-fw"></i> Edit</span>
				</h3>
              <div class="row">
		<form role="form" class="form-horizontal asset-entry-form">	
       
        	<div class="col-md-8">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                        <span class="caption-helper">New Asset</span>
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
                    
									
						<div class="form-body">


                            <div class="form-group form-md-line-input has-error">
                                <label class="col-md-2 control-label" for="assetcode">Asset Code</label>
                                <div class="col-md-10">
                                    <input name="assetcode" type="text" class="form-control input-sm" id="assetcode" placeholder="Asset Code">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">Assign your desired asset code leave blank if N/A</span>
                                </div>
                            </div>


                            <div class="form-group form-md-line-input">
                                <label class="col-md-2 control-label" for="ponumber">PO Number</label>
                                <div class="col-md-10">
                                    <input name="ponumber" type="text" class="form-control input-sm" id="ponumber" placeholder="Search Po Number..">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">PO Number must be exist from EPRS</span>
                                </div>
                            </div>
                            
                            <div class="form-group form-md-line-input ">
                            	
                                <label class="col-md-2 control-label" for="ponumber">PO Date</label>
                                <div class="col-md-4">
                                    <input name="ponumber" type="text" class="form-control input-sm" id="podate" placeholder="Search Po Number..">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">(dd/mm/yyyy) Actual PO date</span>
                                </div>
                                
                                
                                
                                <label class="col-md-2 control-label" for="ponumber">Waranty</label>
                                <div class="col-md-4">
                                    <input name="ponumber" type="text" class="form-control input-sm" id="waranty" placeholder="Search Po Number..">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">(dd/mm/yyyy) Waranty date end.</span>
                                </div>
                                
                            </div>

                            
                            <div class="form-group margin-top-20 form-md-line-input">
                                <label class="col-md-2 control-label" for="assettype">Asset Type</label>
                                <div class="col-md-10">
                                    <input id="asset_type" name="assettype" type="text" class="form-control input-sm " placeholder="Asset Type">
                                </div>
                            </div>
                            
 
 
                             <div class="form-group margin-top-20 form-md-line-input">
                                <label class="col-md-2 control-label" for="assettype">Category</label>
                                <div class="col-md-10">
                                    <input id="asset_cat" name="assetcat" type="text" class="form-control input-sm " placeholder="Asset Category">
                                </div>
                            </div>
                            
                            
                            <div class="form-group margin-top-20 form-md-line-input">
                                <label class="col-md-2 control-label" for="assettype">Specification</label>
                                <div class="col-md-10">
                                    <input id="asset_spec" name="assetspec" type="text" class="form-control  input-sm " placeholder="Asset Specification">
                                </div>
                            </div>
                            
                            <div class="form-group form-md-line-input">
                                    <label class="control-label col-md-2">Primary Picture</label>
                                    <div class="col-md-10">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-sm">
                                                <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                    </span>
                                                </div>
                                                
                                                <span class="input-group-btn btn btn-primary btn-file btn-sm">
                                                <span class="fileinput-new">
                                                Select file </span>
                                                <span class="fileinput-exists">
                                                Change </span>
                                                <input type="file" name="...">
                                                </span>
                                                
                                                <a href="javascript:;" class="input-group-addon btn red-intense fileinput-exists" data-dismiss="fileinput">
                                                Remove </a>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>

						
                        
                        
                        <div class="form-group form-md-line-input">
                                <label class="col-md-2 control-label" for="mask_number">Serial Number</label>
                                <div class="col-md-10">
                                    <input name="serialcodes" type="text" class="form-control input-sm" id="mask_number" placeholder="Serial Number">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">Serial Found in Waranty</span>
                                </div>
                         </div>
  
  
                          <div class="form-group form-md-line-input">
                                <label class="col-md-2 control-label" for="mask_number">Model Number</label>
                                <div class="col-md-10">
                                    <input name="modelcodes" type="text" class="form-control input-sm" id="" placeholder="Model Codes">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">Serial Found in Waranty</span>
                                </div>
                         </div>
                       
                         
                         <div class="form-group form-md-line-input">
                                <label class="col-md-2 control-label" for="descriptions">Addtional Descriptions</label>
                                <div class="col-md-10">
                                <div class="input-icon">
                                    <textarea name="descriptions" rows="3" class="form-control input-sm" id="descriptions"></textarea>
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">Specify additional information</span>
                                  <i class="fa fa-edit"></i>
                                </div>
                           </div>
                            </div>
                         
                         
                         <div class="margin-top-20"></div>
                            
                        </div>				
					
                </div>
            </div>
          </div>
          <div class="col-md-4">
          	<div class="portlet light">
               <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-wrench"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Accountability</span>
                        
                    </div>
                </div>
                <div class="portlet-body">

                <div class="form-group form-md-line-input">
                	<div class="col-md-12">
                    	<label>Location: </label>
                        <input id="asset_loc" name="assetloc" type="text" class="form-control  input-sm " placeholder="Asset Location">
                        
                    </div>
                </div>
                <div class="form-group form-md-line-input" style="margin-top: -20px">
               		<div class="col-md-12">
                    	<label>User: </label>
                    	<input id="asset_user" name="assetuser" type="text" class="form-control  input-sm " placeholder="Asset User">
                    </div>
                </div>
                <div class="form-group form-md-line-input" style="margin-top: -20px">
               		<div class="col-md-12">
                    	<input id="asset_issued" name="assetissued" type="text" class="form-control  input-sm " placeholder="Asset Issued">
                    </div>
                </div>
                        
               
                <input class="form-control disabled" name="moduleid" disabled type="hidden" value="<?php echo $this->model_admin->get_active_navigation_specific_details($this->uri->segment(2))->moduleid; ?>" />
                    
                </div>
                <div class="form-actions margin-top-20 clearfix">
                <div class="row">
                	<div class="col-md-12">
                    	<code>Make sure all the field is filled in.</code>
                    </div>
                </div>
                <hr>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button class="btn blue btn-block btn-lg red-stripe"><i class="fa fa-save fa-fw"></i> Save</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button class="btn green btn-block red-stripe"><i class="fa fa-edit fa-fw"></i> Draft</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button class="btn btn-default btn-block red-stripe"><i class="fa fa-times fa-fw"></i> Cancel</button>
                        </div>
                    </div>
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

<script>
$(function(){
	$("#mask_number").inputmask({
		"mask": "9",
		"repeat": 10,
		"greedy": false
    }); 
	
	$("#podate").inputmask("d/m/y", {
            autoUnmask: true
    });  
	$("#waranty").inputmask("d/m/y", {
            autoUnmask: true
    });  
	$("#asset_issued").inputmask("d/m/y", {
            autoUnmask: true
    }); 
	$("#asset_type").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_types/ASSET",
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


	$("#asset_cat").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_item_category/",
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
		initSelection: function (element, callback) {
			$.ajax({
				url: base_url+"admin/get_item_category/",
				dataType: 'json',
			}).done(function(data){
				var selections = [];
				$.each(data, function (index, item) {
					selections.push({
						'id': item.id,
						'text': item.text
					});
				});
				
				callback(selections)
				
			});
			
		},
		
    }).select2('val', []).change(function(){
		// ADD AJAX UPDATE IF APPLICABLE //
		console.log('CATEGORY: '+$(this).val());
	});  
  
 
 	$("#asset_spec").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_item_specification/",
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
		console.log('SPECS: '+$(this).val());
	});
	
	
	$("#asset_loc").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_costcenter/",
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
		console.log('LOC: '+$(this).val());
	});
	
	$("#asset_user").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_user_basic/",
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
		console.log('USER: '+$(this).val());
	});

 
 	$("#asset_type_selection").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_types",
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
		initSelection: function (element, callback) {
			$.ajax({
				url: base_url+"admin/get_types",
				dataType: 'json',
			}).done(function(data){
				var selections = [];
				$.each(data, function (index, item) {
					selections.push({
						'id': item.id,
						'text': item.text
					});
				});
				
				callback(selections)
				
			});
			
		},
		
    }).change(function(){
		console.log($(this).val());
		// ADD AJAX UPDATE IF APPLICABLE //
	});
 
});
</script>
