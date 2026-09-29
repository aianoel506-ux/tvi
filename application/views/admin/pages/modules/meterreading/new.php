<!-- TESTING --->
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
    <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
    </h3>
    <div class="row">
		<form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>query/savenewaccount" method="post" id="frm_newaccount">

        	<div class="col-md-8">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                        <span class="caption-helper">New Account</span>
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


                            <div class="form-group form-md-line-input">
                                <label class="col-md-2 control-label" for="assetcode">Name</label>
                                <div class="col-md-3">
                                    <input name="firstname" type="text" class="form-control input-sm data-entry" id="firstname" placeholder="First Name">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block"></span>
                                </div>
                                <div class="col-md-3">
                                    <input name="lastname" type="text" class="form-control input-sm data-entry" id="lastname" placeholder="Last Name">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block"></span>
                                </div>
                                <div class="col-md-3">
                                    <input name="middlename" type="text" class="form-control input-sm data-entry" id="middle_initial" placeholder="Middle Name">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block"></span>
                                </div>
                            </div>
							<!-- gender -->
							<div class="form-group form-md-line-input">

							<label class="col-md-2 control-label" for="ponumber">Gender</label>
								<div class="col-md-10">
									<div class="form-control-focus">
										<div class="md-radio-inline">
											<div class="md-radio">
												<input id="radio_male" class="md-radiobtn" value="1" checked="" type="radio" name="gender">
												<label for="radio_male">
													<span class="inc"></span>
													<span class="check"></span>
													<span class="box"></span>
													Male
												</label>
											</div>
											<div class="md-radio">
												<input id="radio_female" class="md-radiobtn" value="2" type="radio"  name="gender">
												<label for="radio_female">
													<span class="inc"></span>
													<span class="check"></span>
													<span class="box"></span>
													Female
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<!-- test for -->
								<table class="table table-hover table-light">
									<tr>
										<th>Customer #</th>
										<th>Meter Description</th>
										<th>Serial</th>
										<th>Status</th>
									 </tr>
									 <tr>
										<td>1</td>
										<td>Sample</td>
										<td>1001</td>
										<td>Active</td>
									 </tr>
									 <tr>
										<td>2</td>
										<td>Sample</td>
										<td>1002</td>
										<td>Active</td>
									 </tr>
									 <tr>
										<td>3</td>
										<td>Sample</td>
										<td>1003</td>
										<td>Active</td>
									 </tr>
									 
									 </table>
			 
			 
          <div class="col-md-4">
          	<div class="portlet light">
               <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-wrench"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"></span>
                        
                    </div>
                </div>
                <div class="portlet-body">
					<div id="query-status"></div>
                <input class="form-control disabled" name="moduleid" readonly type="hidden" value="<?php echo $this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid; ?>" />
                    
                </div>
                <div class="form-actions margin-top-20 clearfix">
                
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button id="save_button" class="btn blue btn-block btn-lg red-stripe"  type="submit"><i class="fa fa-save fa-fw"></i> Save</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button id="draft_button" class="btn green btn-block red-stripe" type="button"><i class="fa fa-edit fa-fw"></i> Draft</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button id="cancel_button" class="btn btn-default btn-block red-stripe"  type="button"><i class="fa fa-times fa-fw"></i> Cancel</button>
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


<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/newaccount.js"></script>

