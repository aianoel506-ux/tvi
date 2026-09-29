<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>


				
    <h3 class="page-title">
    <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
    </h3>
				
        <div class="row">
        	<div class="col-md-8">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Entry</span>
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
                    <form role="form" class="form-horizontal">
									<div class="form-body">
										
										<div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="assetcode">Asset Code</label>
											<div class="col-md-10">
												<input name="assetcode" type="text" class="form-control" id="assetcode" placeholder="Asset Code">
												<div class="form-control-focus">
												</div>
												<span class="help-block">Assign your desired asset code leave blank if N/A</span>
											</div>
										</div>
                                        
                                        <div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="ponumber">PO Number</label>
											<div class="col-md-10">
												<input name="ponumber" type="text" class="form-control" id="ponumber" placeholder="Search Po Number..">
												<div class="form-control-focus">
												</div>
												<span class="help-block">PO Number must be exist from EPRS</span>
											</div>
										</div>
                                        
										
										<div class="form-group form-md-line-input has-warning">
											<label class="col-md-2 control-label" for="form_control_1">Warning Input</label>
											<div class="col-md-10">
												<input type="text" class="form-control" id="form_control_1" placeholder="Warning state">
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input has-error">
											<label class="col-md-2 control-label" for="form_control_1">Error State</label>
											<div class="col-md-10">
												<input type="text" class="form-control" id="form_control_1" placeholder="Error state">
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="form_control_1">Dropdown Input</label>
											<div class="col-md-10">
												<select class="form-control" id="form_control_1">
													<option value=""></option>
													<option value="">Option 1</option>
													<option value="">Option 2</option>
													<option value="">Option 3</option>
													<option value="">Option 4</option>
												</select>
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input has-success">
											<label class="col-md-2 control-label" for="form_control_1">Textarea</label>
											<div class="col-md-10">
												<textarea class="form-control" rows="3" placeholder="Enter more text"></textarea>
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="form_control_1">Disabled</label>
											<div class="col-md-10">
												<input type="text" class="form-control" disabled id="form_control_1" placeholder="Placeholder...">
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="form_control_1">Readonly</label>
											<div class="col-md-10">
												<input type="text" class="form-control" readonly value="Some value" id="form_control_1" placeholder="Placeholder...">
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="form_control_1">Readonly</label>
											<div class="col-md-10">
												<div class="form-control form-control-static">
													email@example.com
												</div>
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="form_control_1">Small</label>
											<div class="col-md-10">
												<input type="text" class="form-control input-sm" id="form_control_1" placeholder=".input-sm">
												<div class="form-control-focus">
												</div>
											</div>
										</div>
										<div class="form-group form-md-line-input">
											<label class="col-md-2 control-label" for="form_control_1">Large</label>
											<div class="col-md-10">
												<input type="text" class="form-control input-lg" id="form_control_1" placeholder=".input-lg">
												<div class="form-control-focus">
												</div>
											</div>
										</div>
									</div>
									
								</form>
                </div>
            </div>
          </div>
          <div class="col-md-4">
          	<div class="portlet light">
               <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-wrench"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Controls</span>
                        
                    </div>
                </div>
                <div class="portlet-body">
   				<div class="row">
                <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-hoki">
                        <div class="visual">
                            <i class="fa fa-edit"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 TRN201500001
                            </div>
                            <div class="desc">
                                 TRN REF
                            </div>
                        </div>
                        
                    </div>
                </div>
                </div>
                
                <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 margin-top-10">
                    <div class="dashboard-stat blue-hoki">
                        <div class="visual">
                            <i class="fa fa-edit"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 <?php echo $this->model_admin->get_active_navigation_specific_details($this->uri->segment(2))->levels; ?>
                            </div>
                            <div class="desc">
                                 TRN STAGES
                            </div>
                        </div>
                        
                    </div>
                </div>
                </div>
                <input class="form-control disabled" name="moduleid" disabled type="hidden" value="<?php echo $this->model_admin->get_active_navigation_specific_details($this->uri->segment(2))->moduleid; ?>" />
                    
                </div>
                <div class="form-actions margin-top-20 clearfix">
                
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
                
        </div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
	

<script>
$('#select2_sample2').select2({
	placeholder: "Select a State",
	allowClear: true
});
</script>