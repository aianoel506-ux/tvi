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
	position: relative !important;
}
.form-md-line-input .fileinput .input-group-addon {
	background: rgba(177,176,176,0.47) !important;
	z-index: 3000 !important;
}
.form-md-line-input .fileinput .input-group-addon .btn.red-intense {
	background: rgba(251,124,126,0.77) !important;
}
.form-md-line-input .select2-container {
	margin-bottom: 0px !important;
}
.select2-drop {
	margin-top: -15px !important;
}
</style>


    <h3 class="page-title"> <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small> </h3>
    <div class="row">
    <form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>query/savenewaccount" method="post" id="frm_newaccount">
      <div class="col-md-8">
        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption"> <i class="fa fa-edit"></i> <span class="caption-subject font-green-sharp bold uppercase">Information</span> <span class="caption-helper">New Account </span> </div>
            <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> <a href="javascript:;" class="reload" data-original-title="" title=""> </a> <a href="javascript:;" class="fullscreen" data-original-title="" title=""> </a> <a href="javascript:;" class="remove" data-original-title="" title=""> </a> </div>
          </div>
          <div class="portlet-body">
            <div class="form-body">
              <div class="form-group form-md-line-input">
                <label class="col-md-2 control-label" for="assetcode">Name</label>
                <div class="col-md-3">
                  <input name="firstname" type="text" class="form-control input-sm data-entry" id="firstname" placeholder="First Name">
                  <div class="form-control-focus"> </div>
                  <span class="help-block"></span> </div>
                <div class="col-md-3">
                  <input name="lastname" type="text" class="form-control input-sm data-entry" id="lastname" placeholder="Last Name">
                  <div class="form-control-focus"> </div>
                  <span class="help-block"></span> </div>
                <div class="col-md-3">
                  <input name="middlename" type="text" class="form-control input-sm data-entry" id="middle_initial" placeholder="Middle Name">
                  <div class="form-control-focus"> </div>
                  <span class="help-block"></span> </div>
              </div>
              <!-- gender -->
              <div class="form-group form-md-line-input">
                <label class="col-md-2 control-label" for="ponumber">Gender</label>
                <div class="col-md-10">
                  <div class="form-control-focus">
                    <div class="md-radio-inline">
                      <div class="md-radio">
                        <input id="radio_male" class="md-radiobtn" value="1" checked="" type="radio" name="gender">
                        <label for="radio_male"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Male </label>
                      </div>
                      <div class="md-radio">
                        <input id="radio_female" class="md-radiobtn" value="2" type="radio"  name="gender">
                        <label for="radio_female"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Female </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group form-md-line-input">
                <label class="col-md-2 control-label" for="ponumber">Address</label>
                <div class="col-md-10">
                  <div class="col-md-4">
                    <select id="country_select" class="form-control select2" name="addrcountry">
                      <option disabled>Country..</option>
                      <?php
							if (select_country()->num_rows() > 0) {
							   foreach (select_country()->result() as $row) {
								  $default = '';
								  if ($row->sysid == 175) {
									 $default = 'selected="selected"';
								  }
								  echo '<option ' . $default . ' value="' . $row->sysid . '">' . $row->country . '</option>';
							   }
							} else {
							   echo '<option value="0">No Country</option>';
							}
							?>
                    </select>
                    <div class="col-md-4">Country</div>
                  </div>
                  <!-- end for country -->
                  
                  <div class="col-md-4">
                    <select id="city_select" class="form-control select2" name="addrcity">
                      <option disabled>City..</option>
                      <?php
                                                if (select_city()->num_rows() > 0) {
                                                   foreach (select_city()->result() as $row) {
                                                      $default = '';
                                                      if ($row->sysid == 1) {
                                                         $default = 'selected="selected"';
                                                      }
                                                      echo '<option ' . $default . ' value="' . $row->sysid . '">' . $row->names . '</option>';
                                                   }
                                                } else {
                                                   echo '<option value="0">No City</option>';
                                                }
                                                ?>
                    </select>
                    <div class="col-md-4">City</div>
                  </div>
                  <!-- end for city -->
                  <div class="col-md-4">
                    <select id="district_select" class="form-control select2" name="addrdistrict">
                      <option disabled>District..</option>
                      <?php
                                                if (select_district()->num_rows() > 0) {
                                                   foreach (select_district()->result() as $row) {
                                                      $default = '';
                                                      //   if($row->sysid==1){
                                                      //          $default = 'selected="selected"';
                                                      //      }
                                                      echo '<option ' . $default . ' value="' . $row->sysid . '">' . $row->names . '</option>';
                                                   }
                                                } else {
                                                   echo '<option value="0">No District</option>';
                                                }
                                                ?>
                    </select>
                    <div class="col-md-4">District</div>
                  </div>
                  <!-- end for district --> 
                </div>
              </div>
              <!-- begin for details in address -->
              <div class="form-group margin-top-20 form-md-line-input"> </div>
              <!-- specific street address -->
              <div class="form-group margin-top-20 form-md-line-input">
                <label class="col-md-2 control-label" >Street Address</label>
                <div class="col-md-10">
                  <input id="addrspecific" name="addrspecific" type="text" class="form-control input-sm data-entry" placeholder="Street Address">
                </div>
              </div>
              <!-- end region street address -->
              <div class="form-group margin-top-20 form-md-line-input">
                <label class="col-md-2 control-label" for="assettype">Account Rate</label>
                <div class="col-md-10">
                  <input id="acct_rate" name="acctrate" type="text" class="form-control input-sm data-entry" placeholder="Click to add Customer's Account Rate">
                </div>
              </div>
              <div class="form-group margin-top-20 form-md-line-input">
                <label class="col-md-2 control-label" for="assettype">Status of connection</label>
                <div class="col-md-10">
                  <input id="acct_type" name="accttype" type="text" class="form-control input-sm data-entry" placeholder="Click to add Customer's Account Type">
                </div>
              </div>
              <!-- Customer Requirements Listing -->
              <div class="form-group margin-top-20 form-md-line-input">
                <label class="col-md-2 control-label" for="assettype">Type of Owner</label>
                <div class="col-md-10">
                  <input disabled id="conn_type" name="conntype" type="text" class="form-control input-sm " placeholder="Click to add Customer's Connection Type">
                </div>
              </div>
              <div class="form-group margin-top-20 form-md-line-input">
                <label class="col-md-2 control-label" for="assettype">Type of Location</label>
                <div class="col-md-10">
                  <input id="loc_type" name="loctype" type="text" class="form-control input-sm " placeholder="Click to add Customer's Location Type">
                </div>
              </div>
              <!-- END Customer Requirements Listing -->
              <div class="form-group margin-top-20 form-md-line-input">
                <label class="col-md-2 control-label" for="assettype">Requirements <a class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Override</a> </label>
                <div class="col-md-10">
                  <input id="acct_req" name="acctreq" type="text" class="form-control input-sm " placeholder="Basic Requirements">
                </div>
              </div>
              <div class="form-group margin-top-20 form-md-line-input">
                <label class="col-md-2 control-label" for="assettype">Additional Requirements <em>(Optional)</em></label>
                <div class="col-md-10">
                  <input id="acct_req_add" name="acctreqadd" type="text" class="form-control input-sm " placeholder="Additional Requirements">
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
            <div class="caption"> <i class="fa fa-wrench"></i> <span class="caption-subject font-green-sharp bold uppercase"></span> </div>
          </div>
          <div class="portlet-body">
			<div id="query-status"></div>
              <input class="form-control" value="<?php echo get_stage_start(); ?>" name="stagelevel" type="hidden"/>
              <input class="form-control disabled" name="moduleid" readonly type="hidden" value="<?php echo get_stage_start_module(); ?>" />
              
          <div class="form-group form-md-line-input">
          <div class=" col-md-12">
		 <label for="form-label" class="">Add Primary Picture</label>
         </div>
            <div class="fileinput fileinput-new col-md-12" data-provides="fileinput" style="width: 100%">
                <div class="fileinput-new thumbnail col-md-12" style="height: 180px; background: #ededed;">
                    <img class="media-object" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCI+PHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iMzIiIHk9IjMyIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEycHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9zdmc+" alt="128x128" data-src="holder.js/128x128" style="width: 100%; height: 100%;">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%; max-height: 180px; line-height: 10px;  background: #ededed;"></div>
               
               
            <div>
            	<button class="btn btn btn-primary" type="button"><i class="fa fa-camera"></i> </button>
              	<span class="btn default btn-file">
                <span class="fileinput-new">
                <i class="fa fa-image fa-fw"></i> Select image </span>
               
                <span class="fileinput-exists">
                Change </span>
                <input type="hidden" value="" name="..."><input type="file" name="">
                </span>
                
                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput">
                Remove </a>
                
            </div>
            
            </div>
            
         </div>    
          
          <div class="form-group form-md-line-input">
            <div class=" col-md-12">
              <textarea name="remarks" rows="5" class="form-control" placeholder="Remarks" title="Remarks"></textarea>
            </div>
          </div>
          
          
       
        <div class="form-actions margin-top-20 clearfix"></div>
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
  <!-- END PAGE HEADER--> 
  <!-- BEGIN PAGE CONTENT--> 
  <!-- END PAGE CONTENT--> 
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
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/newaccount.js"></script> 