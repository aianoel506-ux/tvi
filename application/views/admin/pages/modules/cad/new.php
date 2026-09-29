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
    .form-group {
        margin-bottom: 0px !important;
    }


    div.input-icon {
        background: #fff !important;
    }

    .help-block {
        color: #ccc;
        font-size: 11px;
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
        <form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>cad/savenewaccount" method="post" id="frm_newaccount">
            <input class="form-control" value="<?php echo get_stage_start(); ?>" id="stagelevel" name="stagelevel" type="hidden"/>
            <input class="form-control" id="moduleid" name="moduleid" readonly type="hidden" value="<?php echo get_stage_start_module(); ?>" />
            <input type="hidden" name="encodestart" value="<?php echo sql_time()->DATETIME; ?>" id="encodestart" />
            <input type="hidden" name="acctex" value="0" id="input_acctex"/>
            <input type="hidden" name="acctra" value="0" id="input_acctra"/>
            <input type="hidden" name="apptype" id="input_apptype" value="1"/>
            <div class="col-md-12">
                <div class="portlet light form" id="" >

                    <div class="portlet-title">
                        <div class="btn-group pull-right">
                            <a href="<?php echo base_url();?>module/50336bc687eb161ee9fb0ddb8cf2b7e65bad865f/list" class="btn btn-danger inline" type="reset" style="margin-right: 20px;"><i class="fa fa-long-arrow-left"></i> Back to Dashboard</a>
                        </div>
                        <div class="caption">
                            <i class="fa fa-check"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">New Customer</span>
                        </div>
                        <div class="form-group margin-bottom-10 margin-top-10">
                            <label class="col-md-2 control-label person-name" for="name">TSSR <span class="required"></span></label>
                            <div class="col-md-3 select2-data-flat">
                                <input name="tssr" type="text" class="form-control data-entry" id="tssr" placeholder="TSSR #" />
                                <div class="form-control-focus"> </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body ">
                        <br>
                        <div id="query-status"></div>
                        <div class="row">
                            <div class="portlet box green col-md-6 col-md-offset-3 margin-right-5">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-check-square-o"></i>
                                        <span class="caption-subject bold uppercase">Type</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label col-md-1 center"> <span class="required"></span></label>
                                                <div class="col-md-11">
                                                    <div class="icheck-inline">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label><input name="apptype" value="1" type="radio" data-radio="iradio_square-red" class="icheck" required/> Residential</label>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label><input name="apptype" value="2" type="radio" data-radio="iradio_square-red" class="icheck" required/> Commercial</label>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label><input name="apptype" value="3" type="radio" data-radio="iradio_square-red" class="icheck" required/> Government</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet box red-sunglo col-md-6 col-md-offset-3 margin-right-5">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-user"></i>
                                        <span class="caption-subject bold uppercase">Name</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="non_residential">

                                    </div>
                                    <div id="person_info">
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label"> Last Name <span class="required"></span></label>
                                            <div class="col-md-9">
                                                <input name="lastname" type="text" class="form-control data-entry" id="lastname" placeholder="Last Name" data-toggle="autocomplete" col-name="lastname" value>
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label">First Name <span class="required"></span></label>
                                            <div class="col-md-9">
                                                <input name="firstname" type="text" class="form-control data-entry" id="firstname" placeholder="First Name" data-toggle="autocomplete" col-name="firstname">
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label">Middle Name <span class="required"></span></label>
                                            <div class="col-md-9">
                                                <input name="middlename" type="text" class="form-control data-entry" id="middle_initial" placeholder="Middle Name" data-toggle="autocomplete" col-name="middlename">
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label">Suffix</label>
                                            <div class="col-md-9">
                                                <select name="suffix" class="form-control data-entry" id="suffix">
                                                    <option value=""></option>
                                                    <?php foreach (select_person_title(70) as $row) { ?>
                                                        <option value="<?php echo $row->sysid; ?>"><?php echo $row->names; ?> - <?php echo $row->descriptions; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet box blue col-md-6 col-md-offset-3 margin-left-5">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-location-arrow"></i>
                                        <span class="caption-subject bold uppercase">Address and Location</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Country</label>
                                        <div class="col-md-9">
                                            <input id="select2_country" class="form-control" name="country" />
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Region</label>
                                        <div class="col-md-9">
                                            <input id="select2_region" class="form-control" name="region"  placeholder="Select region.."/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Province</label>
                                        <div class="col-md-9">
                                            <input id="select2_province" class="form-control" name="province" placeholder="Select province.."/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Municipal / City</label>
                                        <div class="col-md-9">
                                            <input id="select2_citymun" class="form-control" name="city"  placeholder="Select Municipal / City.."/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Specific Address</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" rows="2" id="addrspecific" name="addrspecific" placeholder="Ex: Blk9 Lot20, DECA Homes Subd., Red Gate, Near Security Guard Outpost"></textarea>
                                            <span class="help-block">Provide specific street address, blk, house number and landmark.</span>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Google Map Locaton</label>
                                        <div class="col-md-9">
                                            <div class="input-icon">
                                                <i class="fa fa-map-marker"></i>
                                                <input class="form-control" rows="3" id="addrgmap" name="googlemap" placeholder="Paste Google Map here!"/>
                                            </div>
                                            <span class="help-block">Ex: https://www.google.com/maps/@10.8459772,122.6544582,11.75z</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet box blue-hoki col-md-6 col-md-offset-3 margin-left-5">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-phone"></i>
                                        <span class="caption-subject bold uppercase">Contact Information</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Phone</label>
                                        <div class="col-md-9">
                                            <input name="phone" type="text" class="form-control data-entry" id="phone" placeholder="Phone Number" data-toggle="autocomplete" col-name="phone" value>
                                            <div class="form-control-focus"> </div>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label"><span class="required"></span> Mobile</label>
                                        <div class="col-md-9">
                                            <input name="mobile" type="text" class="form-control data-entry" id="mobile" placeholder="Mobile Number" data-toggle="autocomplete" col-name="mobile">
                                            <div class="form-control-focus"> </div>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label">E-Mail Address</label>
                                        <div class="col-md-9">
                                            <input name="email" type="email" class="form-control data-entry" id="email" placeholder="E-Mail Address" data-toggle="autocomplete" col-name="email">
                                            <div class="form-control-focus"> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-footer">
                        <div class="btn-group pull-right">
                            <button class="btn btn-default inline" type="reset" style="margin-right: 20px;"><i class="fa fa-refresh"></i> Reset</button>
                            <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/address.js"></script>


<script type="text/javascript">

    ADDRESS.init();
    CAD.application();
</script>
