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

    .tiles .tile:last-child{
        width: auto !important;
    }

    .tiles .tile {
        position: relative;
    }
    .tiles .tile .fa-bg {
        font-size: 200px !important;
        position: absolute;
        bottom: -20px;
        color: #fff;
        opacity: 0.2;
        -moz-opacity: 0.2;
        -webkit-opacity: 0.2;
        margin: 0px 0px !important;
        height: 100%;
    }

    .select2-data-flat .select2-search-choice {
        border: 1px solid transparent !important;
        padding: 0px 0px !important;
    }
    .select2-data-flat .select2-search-choice div {
        left: 0px !important;
    }
    .select2-data-flat .select2-search-choice-close {
        display: none;
    }

    .select2-data-flat .select2-container.select2-container-active {
        border-bottom: transparent 1px solid !important;
    }
    .select2-data-flat .select2-input.select2-default,
    .select2-data-flat .select2-search-field,
    .select2-data-flat .select2-choices{
        border: transparent 1px solid !important;
        padding: 2px 0px !important;
    }
    .select2-data-flat .select2-choices{
        width: 100% !important;
        height: 30px !important;
    }
    .select2-data-flat .select2-search-field, .select2-data-flat .select2-search-field input{
        height: 30px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
        top: -5px !important;
    }


</style>


<div  id="form_wizard_1">
    <div class="row">
        <div class="col-md-12 form-wizard">



            <ul class="nav nav-pills nav-justified steps">
                <li>
                    <a href="#tab1" data-toggle="tab" class="step ">
                            <span class="number">
                                1 </span>
                        <span class="desc">
                                <i class="fa fa-check"></i> Profile Setup </span>
                    </a>
                </li>
                <li>
                    <a href="#tab2" data-toggle="tab" class="step">
                            <span class="number">
                                2 </span>
                        <span class="desc">
                                <i class="fa fa-check"></i> Address Setup </span>
                    </a>
                </li>
                <li>
                    <a href="#tab3" data-toggle="tab" class="step active">
                            <span class="number">
                                3 </span>
                        <span class="desc">
                                <i class="fa fa-check"></i> Account Setup </span>
                    </a>
                </li>
                <!--
                <li>
                    <a href="#tab4" data-toggle="tab" class="step">
                            <span class="number">
                                4 </span>
                        <span class="desc">
                                <i class="fa fa-check"></i> Verification </span>
                    </a>
                </li>
                -->
                <li>
                    <a href="#tab5" data-toggle="tab" class="step">
                            <span class="number">
                                5 </span>
                        <span class="desc">
                                <i class="fa fa-check"></i> Confirm </span>
                    </a>
                </li>
            </ul>
            <div id="bar" class="progress progress-striped active" role="progressbar" style="height: 10px">
                <div class="progress-bar progress-bar-success">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>cad/savenewaccount" method="post" id="frm_newaccount">
            <input class="form-control" value="<?php echo get_stage_start(); ?>" id="stagelevel" name="stagelevel" type="hidden"/>
            <input class="form-control" id="moduleid" name="moduleid" readonly type="hidden" value="<?php echo get_stage_start_module(); ?>" />
            <input type="hidden" name="encodestart" value="<?php echo sql_time()->DATETIME; ?>" id="encodestart" />
            <input type="hidden" name="acctex" value="0" id="input_acctex"/>
            <input type="hidden" name="acctra" value="0" id="input_acctra"/>
            <input type="hidden" name="apptype" value="0" id="input_apptype" value="1"/>
            <div class="col-md-12">
                <div class="portlet box light" id="">
                    <div class="portlet-body form">
                        <div id="query-status"></div>
                        <div class="form-wizard">
                            <div class="">
                                <div class="col-md-12">

                                    <div class="tab-content margin-top-20">
                                        <div class="alert alert-danger display-none">
                                            <button class="close" data-dismiss="alert"></button>
                                            You have some form errors. Please check below.
                                        </div>
                                        <div class="alert alert-success display-none">
                                            <button class="close" data-dismiss="alert"></button>
                                            Your form validation is successful!
                                        </div>

                                        <div class="tab-pane active" id="tab1">
                                            <div class="portlet light">
                                                <div class="portlet-title">

                                                    <div class="caption">
                                                        <i class="fa fa-edit"></i>
                                                        <span class="caption-subject font-green-sharp bold uppercase">Profile Setup</span>
                                                        <span class="caption-helper">select the type of profile</span>
                                                    </div>


                                                </div>
                                                <div class="portlet-body">
                                                    <div class="form-group form-md-line-input">

                                                        <div class="form-group form-md-line-input animated fadeInUp fast" id="corpandgovinfo" style="border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 20px;">

                                                            <label class="col-md-2 control-label" id="typename"></label>

                                                            <div class="col-md-3 select2-data-flat">
                                                                <input name="namecorpgov" type="text" class="form-control data-entry" id="namecorpgov" placeholder="Name" />
                                                                <div class="form-control-focus"> </div>
                                                            </div>
                                                            <label class="col-md-2 control-label" for="addresscorpgov">Address:</label>
                                                            <div class="col-md-2">
                                                                <select disabled name="addresscorpgov" type="text" class="form-control data-entry" id="addresscorpgov">
                                                                    <?php
                                                                    /* if (select_district()->num_rows() > 0) {
                                                                         foreach (select_district()->result() as $row) {
                                                                             $default = '';
                                                                             //   if($row->sysid==1){
                                                                             //          $default = 'selected="selected"';
                                                                             //      }
                                                                             echo '<option ' . $default . ' value="' . $row->sysid . '">' . $row->names . '</option>';
                                                                         }
                                                                     } else {
                                                                         echo '<option value="0">No District</option>';
                                                                     } */
                                                                    ?>

                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input disabled name="specificcorpgov" type="text" class="form-control data-entry" id="specificcorpgov" placeholder="Specific" />
                                                            </div>

                                                        </div>

                                                        <label class="col-md-2 control-label person-name" for="name">Name</label>
                                                        <div class="col-md-3 select2-data-flat">
                                                            <input name="lastname" type="text" class="form-control data-entry" id="lastname" placeholder="Last Name" data-toggle="autocomplete" col-name="lastname" value>
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span> </div>
                                                        <div class="col-md-3">
                                                            <input name="firstname" type="text" class="form-control data-entry" id="firstname" placeholder="First Name" data-toggle="autocomplete" col-name="firstname">
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span> </div>
                                                        <div class="col-md-2">
                                                            <input name="middlename" type="text" class="form-control data-entry" id="middle_initial" placeholder="Middle Name" data-toggle="autocomplete" col-name="middlename">
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span> </div>

                                                        <div class="col-md-2">
                                                            <select name="suffix"class="form-control data-entry" id="suffix">
                                                                <option value=""></option>
                                                                <?php foreach (select_person_title(70) as $row) { ?>
                                                                    <option value="<?php echo $row->sysid; ?>"><?php echo $row->names; ?> - <?php echo $row->descriptions; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block">SUFFIX / Person's Degree / After Name</span>
                                                        </div>
                                                    </div>
                                                    <!-- gender -->
                                                    <div class="form-group form-md-line-input">
                                                        <label class="col-md-2 control-label" for="ponumber">Status</label>

                                                        <div class="col-md-2">
                                                            Prefix
                                                            <select name="prefix" class="form-control data-entry" id="prefix">
                                                                <option value=""></option>
                                                                <?php foreach (select_person_title(71) as $row) { ?>
                                                                    <option value="<?php echo $row->sysid; ?>"><?php echo $row->names; ?></option>
                                                                <?php } ?>

                                                            </select>
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span>
                                                        </div>

                                                        <div class="col-md-3">
                                                            Gender
                                                            <div class="form-control-focus">
                                                                <div class="md-radio-inline" id="md_gender">
                                                                    <div class="md-radio">
                                                                        <input id="radio_male" name="gender" class="md-radiobtn" value="1" checked="" type="radio">
                                                                        <label for="radio_male"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Male </label>
                                                                    </div>
                                                                    <div class="md-radio">
                                                                        <input id="radio_female" name="gender" class="md-radiobtn" value="2" type="radio">
                                                                        <label for="radio_female"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Female </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            Date of Birth
                                                            <input name="birthdate" type="date" class="form-control data-entry" id="date_birth" placeholder="Date of Birth">
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span>
                                                        </div>


                                                        <div class="col-md-3">
                                                            Marital Status:
                                                            <select name="marital" class="form-control data-entry" id="marital">
                                                                <option value=""></option>
                                                                <?php foreach (select_marital() as $row) { ?>
                                                                    <option value="<?php echo $row->sysid; ?>"><?php echo $row->descriptions; ?></option>
                                                                <?php } ?>

                                                            </select>
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span>
                                                        </div>

                                                    </div>

                                                    <div id="partner_info" class="form-group form-md-line-input hidden animated fadeIn fast">
                                                        <label class="col-md-2 control-label" for="">Partner Name</label>
                                                        <div class="col-md-3">
                                                            <input placeholder="Last Name" class="form-control" id="partnerfname" name="parnerfname" />
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input placeholder="First Name" class="form-control" id="partnerfname" name="parnerfname" />
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input placeholder="Middle Name" class="form-control" id="partnerfname" name="parnerfname" />
                                                            <div class="form-control-focus"> </div>
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2">
                                            <div class="row">
                                                <h3 class="block col-md-8 col-lg-8 col-xs-4">Provide your profile details</h3>
                                                <h4 class="right block col-md-4"><input type="checkbox" id="editprofile" name="editprofile" value="1"/><label for="editprofile">Check to Edit</label></h4>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Contacts <span class="required">
                                                            * </span>
                                                </label>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control data-entry" id="phone" name="phone" placeholder="Ex: 3290002"/>
                                                    <span class="help-block">
                                                            Provide your phone number </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control data-entry" id="mobile" name="mobile" placeholder="Ex: 09179999988"/>
                                                    <span class="help-block">
                                                            Provide your mobile number </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="email" class="form-control data-entry" id="email" name="email" placeholder="Ex: yourname@email.com"/>
                                                    <span class="help-block">
                                                            Provide your email address </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Address <span class="required">
                                                            * </span>
                                                </label>


                                                <!--<label class="control-label col-md-3">City/Town <span class="required">
                                                        * </span>
                                                </label>-->
                                                <div class="col-md-3">
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


                                                <div class="col-md-3">
                                                    <select id="district_select" class="form-control select2" name="addrdistrict">
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
                                                    <div class="col-md-4">District</div>
                                                </div>

                                                <!--<label class="control-label col-md-3">Country</label>-->
                                                <div class="col-md-3">
                                                   <!-- <select name="country" id="country_list" class="form-control" placeholder="Country">
                                                        <option value=""></option>
                                                        <?php
                                                      /*  foreach (select_country() as $row) {
                                                            $default = '';
                                                            if ($row->sysid == 175) {
                                                                $default = 'selected="selected"';
                                                            }
                                                            echo '<option ' . $default . ' value="' . $row->sysid . '">' . $row->country . '</option>';
                                                        } */
                                                        ?>
                                                    </select> -->

                                                    <input type="text" disabled class="form-control" name="brgy" id="brgy" />


                                                    <span class="help-block">
                                                            Select Barangay
                                                        </span>
                                                </div>
                                            </div>

                                            <div class="form-group  form-md-line-input  margin-bottom-20">
                                                <label class="control-label col-md-2">Specific / Landmarks <span class="required">
                                                            * </span></label>
                                                <div class="col-md-6">

                                                    <div class="input-icon">

                                                    <i class="fa fa-edit"></i>
                                                    <input class="form-control" rows="3" id="addrspecific" name="addrspecific" placeholder="Ex: #12 General Luna Street / PECO"/>
                                                    <span class="help-block">Input specific street address, blk, house number and landmark  </span>
                                                    </div>

                                                </div>
                                                <!--
                                                <div class="col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            TIN:
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control data-entry" id="tin" name="tin" placeholder="Ex: 306-5856-558-0000"/>
                                                            <span class="help-block"> * Required! </span>
                                                        </div>
                                                    </div>

                                                    <div class="row margin-top-30">
                                                        <div class="col-md-2">
                                                            SSS:
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control data-entry" id="sss" name="sss" placeholder="Ex: 07-5856685-06"/>
                                                            <span class="help-block"> (optional) </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                -->

                                            </div>
                                            <div class="form-group  form-md-line-input  margin-bottom-20">
                                                <label class="control-label col-md-2">Google Map Location<span class="required">
                                                            * </span></label>

                                                <div class="col-md-6">
                                                    <div class="input-group input-icon">

                                                        <i class="fa fa-map-marker"></i>
                                                        <input class="form-control" rows="3" id="addrgmap" name="googlemap" placeholder="Paste Google Map here!"/>
                                                        <span class="help-block">Ex: https://www.google.com/maps/@10.8459772,122.6544582,11.75z</span>

                                                        <span class="input-group-addon">
                                                            <i class="fa fa-question"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <!--
                                                <div class="col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            TIN:
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control data-entry" id="tin" name="tin" placeholder="Ex: 306-5856-558-0000"/>
                                                            <span class="help-block"> * Required! </span>
                                                        </div>
                                                    </div>

                                                    <div class="row margin-top-30">
                                                        <div class="col-md-2">
                                                            SSS:
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control data-entry" id="sss" name="sss" placeholder="Ex: 07-5856685-06"/>
                                                            <span class="help-block"> (optional) </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                -->

                                            </div>
                                        </div>


                                        <div class="tab-pane" id="tab3">

                                            <div class="col-md-6">
                                                <h3 class="block">Account / Subscription Setup</h3>

                                                <div class="form-group margin-top-20 form-md-line-input crit">
                                                    <label class="col-md-2 control-label" for="owner_type">Roof Type</label>
                                                    <div class="col-md-10">
                                                        <!-- conntype -->
                                                        <input id="roof_type" name="rooftype" type="text" class="form-control input-sm " placeholder="Input Roof type">
                                                    </div>
                                                </div>


                                                <div class="form-group margin-top-20 form-md-line-input crit">
                                                    <label class="col-md-2 control-label " for="acct_type">Account Type</label>
                                                    <div class="col-md-10">
                                                        <input id="acct_type" name="accttype" type="text" class="form-control input-sm data-entry" placeholder="Click to add Customer's Account Rate">
                                                    </div>
                                                </div>
                                                <!-- Customer Requirements Listing -->

                                                <!-- Customer Requirements Listing -->
                                                <div class="form-group margin-top-20 form-md-line-input crit">
                                                    <label class="col-md-2 control-label" for="owner_type">Type of Owner</label>
                                                    <div class="col-md-10">
                                                        <!-- conntype -->
                                                        <input disabled id="owner_type" name="ownertype" type="text" class="form-control input-sm " placeholder="Click to add Customer's Owner Type">
                                                    </div>
                                                </div>
                                                <div class="form-group margin-top-20 form-md-line-input crit">
                                                    <label class="col-md-2 control-label" for="loc_type">Payment Type</label>
                                                    <div class="col-md-10">
                                                        <input id="pay_type" name="paytype" type="text" class="form-control input-sm " placeholder="Click to add Customer's Payment Type">
                                                    </div>
                                                </div>
                                                <!-- END Customer Requirements Listing -->
                                                <div class="form-group margin-top-20 form-md-line-input">
                                                    <label class="col-md-2 control-label" for="assettype">Requirements </label>
                                                    <div class="col-md-10">
                                                        <table id="tbl_basic_req" class="table table-hover table-condensed">
                                                            <thead>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--
                                                <div class="form-group margin-top-20 form-md-line-input">
                                                    <label class="col-md-2 control-label" for="assettype">Additional Requirements <em>(Optional)</em></label>
                                                    <div class="col-md-10">
                                                        <input id="acct_req_add" name="acctreqadd" type="text" class="form-control input-sm " placeholder="Additional Requirements">
                                                    </div>

                                                </div>
                                                -->
                                            </div>
                                            <div class="col-md-6">
                                                <h3 class="block">Account Contact/Address</h3>
                                                <input type="checkbox" id="checkcust" name="checkcust"/><label for="checkcontact">Check if contact or address is the same as in tab 3</label>

                                                <div class="col-md-12">
                                                    <div class="row form-group margin-top-15 hidden"  id="checkedcust">
                                                        <ul class="list-group summary margin-top-20 column no-border list-group-sm">
                                                            <li class="list-group-item"><span class="label-name col-md-3">Phone Number </span><span class="label label-default pull-right col-md-9" id="phonecust"></span></li>
                                                            <li class="list-group-item"><span class="label-name col-md-3">Mobile Number</span><span class="label label-default pull-right col-md-9" id="mobilecust"></span></li>
                                                            <li class="list-group-item"><span class="label-name col-md-3">Email Address</span> <span class="label label-default pull-right col-md-9" id="emailcust"></span></li>
                                                            <li class="list-group-item"><span class="label-name col-md-3">Address</span><span class="label label-default pull-right col-md-9" id="custaddress"></span></li>
                                                            <li class="list-group-item"><span class="label-name col-md-3">Address Specific</span><span class="label label-default pull-right col-md-9" id="custspecific"></span></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="row" id="uncheckedcust">


                                                        <div class="form-group margin-top-20">
                                                            <div class="col-md-12">
                                                                <input type="text" class="form-control data-entry" id="custphone" name="custphone" placeholder="Ex: 3290002"/>
                                                                <span class="help-block">
                                                                    Provide your phone number </span>
                                                            </div>
                                                        </div>

                                                        <div class="form-group margin-top-20">
                                                            <div class="col-md-12">
                                                                <input type="text" class="form-control data-entry" id="custmobile" name="custmobile" placeholder="Ex: 09179999988"/>
                                                                <span class="help-block">
                                                                    Provide your mobile number </span>
                                                            </div>
                                                        </div>

                                                        <div class="form-group margin-top-20">
                                                            <div class="col-md-12">
                                                                <input type="email" class="form-control data-entry" id="custemail" name="custemail" placeholder="Ex: yourname@email.com"/>
                                                                <span class="help-block">
                                                                    Provide your email address </span>
                                                            </div>
                                                        </div>

                                                        <div class="form-group margin-top-20 form-md-line-input">
                                                            <div class="col-md-12">
                                                                <select id="cust_city_select" class="form-control select2" name="custaddrcity">
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
                                                        </div>

                                                        <div class="form-group margin-top-20 form-md-line-input">
                                                            <div class="col-md-12">
                                                                <select id="cust_district_select" class="form-control select2" name="custaddrdistrict">
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
                                                                <div class="col-md-4">District</div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group margin-top-20 form-md-line-input">
                                                            <div class="col-md-12">
                                                                <select name="custcountry" id="cust_country_list" class="form-control" placeholder="Country">
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
                                                                </select>
                                                                <span class="help-block">
                                                                    Select Country
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="form-group  form-md-line-input  margin-bottom-20">

                                                            <div class="col-md-12">
                                                                <textarea class="form-control" rows="3" id="custaddrspecific" name="custaddrspecific" placeholder="Ex: #12 General Luna Street / PECO"></textarea>
                                                                <span class="help-block">
                                                                    Input specific street address, blk, house number and landmark
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="tab-pane" id="tab5">
                                            <div class="col-md-6">
                                                <ul class="list-group summary column no-border border-bottom" id="input_summary">
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h3 class="text-primary bold">Google Map</h3>
                                                <hr>
                                                <div id="google_map_preview" style="width: 100%; height: 45vh">
                                                    <h4 class="text-info">Loading map....</h4>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">

                                <div class="row">
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                    <div class="col-md-offset-3 col-md-9">
                                        <a href="javascript:;" class="btn default button-previous">
                                            <i class="m-icon-swapleft"></i> Back </a>
                                        <div id="pulsate-regular" style="width: auto; display: inline-block; padding: 5px; outline: 0px; box-shadow: rgba(57, 155, 195, 0) 0px 0px 13px; outline-offset: 20px;">
                                            <a href="javascript:;" class="btn blue button-next">
                                                Continue <i class="fa fa-forward"></i>
                                            </a>
                                        </div>
                                        <!--<a href="javascript:;" class="btn green button-submit">
                                            Submit <i class="m-icon-swapright m-icon-white"></i>
                                        </a>-->
                                        <button class="btn btn-primary button-submit" type="submit"><i class="fa fa-save"></i> Save</button>
                                        <button class="btn btn-danger button-submit pull-right" type="reset" style="margin-right: 20px;"><i class="fa fa-refresh"></i> Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </form>

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
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-bootpag/jquery.bootpag.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/holder.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.pulsate.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.pulsate.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>


<script>
    CAD.application();
</script>

<script>
    $(window).ready(updateWidth);
    $(window).resize(updateWidth);

    function updateWidth()
    {
        var square = $('.fileinput-new');
        var size = square.width();

        square.css('height', size);
    }

    var x = $('.fileinput-new').width();
    $('.fileinput-new').css(
        {'height': x + 'px'}
    );
</script>
