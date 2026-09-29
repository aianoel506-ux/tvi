<!-- TESTING --->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">


<form id="frm_save_apprehension" action="<?php echo base_url('legal/saveapprehension'); ?>" method="post">

    <div class="row">

        <div class="col-md-8">
            <div class="portlet light portlet-input">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file-archive-o"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Person Info</span>
                        <span class="caption-helper">April 06, 2017</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">
                        <input type="hidden" class="form-control" id="entry_style" name="entrystyle" />

                        <div class="form-group form-md-line-input">
                            <label class="col-md-2 control-label person-name" for="name">Name</label>
                            <div class="col-md-3 select2-data-flat">
                                <input name="lastname" type="text" class="form-control data-entry alter-attr" id="lastname" placeholder="Last Name" data-toggle="autocomplete" col-name="lastname" value>
                                <div class="form-control-focus"> </div>
                                <span class="help-block"></span>
                            </div>
                            <div class="col-md-4">
                                <input name="firstname" type="text" class="form-control data-entry alter-attr" id="firstname" placeholder="First Name" data-toggle="autocomplete" col-name="firstname">
                                <div class="form-control-focus"> </div>
                                <span class="help-block"></span> </div>
                            <div class="col-md-3">
                                <input name="middlename" type="text" class="form-control data-entry alter-attr" id="middle_initial" placeholder="Middle Name" data-toggle="autocomplete" col-name="middlename">
                                <div class="form-control-focus"> </div>
                                <span class="help-block"></span> </div>


                        </div>
                        <!-- gender -->
                        <div class="form-group form-md-line-input">
                            <label class="col-md-2 control-label" for="ponumber">Status</label>

                            <div class="col-md-3">
                                Prefix
                                <input name="prefix" class="form-control data-entry inline alter-attr" id="prefix"/>
                                <div class="form-control-focus"> </div>
                                <span class="help-block"></span>
                            </div>

                            <div class="col-md-3">
                                Suffix
                                <input name="suffix"class="form-control data-entry inline alter-attr" id="suffix" />
                                <div class="form-control-focus"> </div>
                                <span class="help-block">SUFFIX / Person's Degree / After Name</span>
                            </div>


                            <div class="col-md-4">
                                Marital Status:
                                <input name="marital" class="form-control data-entry inline alter-attr" id="marital"/>
                                <div class="form-control-focus"></div>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group form-md-line-input">
                            <label class="input-label col-md-2">

                            </label>
                            <div class="col-md-3">
                                Gender
                                <div class="form-control-focus">
                                    <div class="md-radio-inline" id="md_gender">
                                        <div class="md-radio">
                                            <input id="radio_male" name="gender" class="md-radiobtn alter-attr" value="1" checked="" type="radio">
                                            <label for="radio_male"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Male </label>
                                        </div>
                                        <div class="md-radio">
                                            <input id="radio_female" name="gender" class="md-radiobtn alter-attr" value="2" type="radio">
                                            <label for="radio_female"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Female </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                Date of Birth
                                <input name="birthdate" type="date" class="form-control data-entry alter-attr" id="date_birth" placeholder="Date of Birth">
                                <div class="form-control-focus"> </div>
                                <span class="help-block"></span>
                            </div>
                        </div>

                    </div>



                    <div class="row ">
                        <div class="col-md-12">
                            <div class="form-group form-md-line-input">
                                <label class="control-label col-md-2">Contacts <span class="required">
                                                * </span>
                                </label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control data-entry alter-attr" id="phone" name="phone" placeholder="Ex: 3290002"/>
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">Provide your phone number </span>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control data-entry alter-attr" id="mobile" name="mobile" placeholder="Ex: 09179999988"/>
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">Provide your mobile number </span>
                                </div>
                                <div class="col-md-4">
                                    <input type="email" class="form-control data-entry alter-attr" id="email" name="email" placeholder="Ex: yourname@email.com"/>
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">Provide your email address </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-top-20">
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group form-md-line-input">
                                <label class="control-label col-md-2">Address <span class="required">
                                                * </span>
                                </label>


                                <div class="col-md-3">
                                    City
                                    <select id="city_select" class="form-control select2 inline alter-attr" name="addrcity">
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
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">Provide City</span>
                                </div>


                                <div class="col-md-3">
                                    District
                                    <select id="district_select" class="form-control select2 inline alter-attr" name="addrdistrict">
                                        <option disabled>District..</option>
                                        <?php
                                        if (select_district_only()->num_rows() > 0) {
                                            foreach (select_district_only()->result() as $row) {
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
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">Provide your email address </span>
                                </div>

                                <!--<label class="control-label col-md-3">Country</label>-->
                                <div class="col-md-4">
                                    Country
                                    <select name="country" id="country_list" class="form-control inline alter-attr" placeholder="Country">
                                        <option value=""></option>
                                        <?php
                                        foreach (select_country() as $row) {
                                            $default = '';
                                            if ($row->sysid == 175) {
                                                $default = 'selected="selected"';
                                            }
                                            echo '<option ' . $default . ' value="' . $row->sysid . '">' . $row->country . '</option>';
                                        }
                                        ?>
                                    </select><div class="form-control-focus"> </div>
                                    <span class="help-block">Provide your email address </span>
                                </div>
                            </div>

                            <div class="row margin-bottom-20">
                                <div class="col-md-12">
                                    <div class="form-group  form-md-line-input  margin-bottom-20">
                                        <label class="control-label col-md-2">Specific / Landmarks <span class="required">
                                                        * </span></label>
                                        <div class="col-md-10">
                                            <textarea class="form-control alter-attr" rows="3" id="addrspecific" name="addrspecific" placeholder="Ex: #12 General Luna Street / PECO"></textarea>
                                            <span class="help-block">
                                                        Input specific street address, blk, house number and landmark
                                                    </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>


        <div class="col-md-4">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-binoculars"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Apprehension Entry</span>
                        <span class="caption-helper">April 06, 2017</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <input type="hidden" name="stagestart" value="<?php echo get_stage_start(); ?>" />
                    <input type="hidden" name="moduleid" value="<?php echo get_stage_start_module(); ?>" />
                    <div class="form-group form-md-line-input select2-data-flat margin-top-0 margin-bottom-0 padding-top-0 padding-bottom-0">
                        Apprehended By
                        <input name="inspector" type="text" class="form-control data-entry" id="inspector" placeholder="Inspector" value>
                        <div class="form-control-focus"> </div>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input margin-top-0 margin-bottom-0 padding-top-0  padding-bottom-10">
                        Apprehended Date
                        <input name="inspectiondate" type="text" class="form-control data-entry date-picker" id="dateinspection" placeholder="Inspection date"  value>
                        <div class="form-control-focus"> </div>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input margin-top-0 padding-top-0  padding-bottom-10">
                        Apprehension Type
                        <input name="apprehension" type="text" class="form-control data-entry inline" id="apprehensions" placeholder="Inspection type" value>
                        <div class="form-control-focus"> </div>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input margin-top-0 padding-top-0  padding-bottom-10">
                        Remarks
                        <textarea name="remarks" class="form-control data-entry" id="remarks" placeholder="Remarks.. "></textarea>
                        <div class="form-control-focus"> </div>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <button type="button" data-id="<?php echo user_id(); ?>" class="btn btn-danger" id="btn_clear_trans">Clear Transactions</button>

                        <div class="btn-group pull-right">
                            <button type="reset" class="btn btn-default">Reset</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>

</form>




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

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/legal/main.js"></script>


<script>
    LEGAL.apprehensions();
</script>