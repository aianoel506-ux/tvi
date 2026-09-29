<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/clockface/css/clockface.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
<!-- END PAGE LEVEL STYLES -->

<style>
    .form-md-line-input {
        position: relative !important;
        margin-top: 0px !important;
        padding-top: 0px !important;
    }
    .datepicker-dropdown:before, .datepicker-dropdown:after {
        margin-left: 35px !important;
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
    .datepicker {
        position: absolute;
        margin-left: -50px !important;
        z-index: 1000;
    }
</style>


    <div class="portlet light" style="padding: 15px 15px">

        <div class="portlet-body">
            <form role="form"   action="<?php echo base_url(); ?>hris/savenewemployee" method="post" id="frm_newaccount">
            <div class="row">
                    <div class="col-md-3">

                        <div class="form-group">
                            <label>Lastname<span class="text-danger">*</span></label>
                            <input  name="lastname" type="text" class="form-control input-sm data-entry" id="lastname" placeholder="Last Name">
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
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
                        <div class="form-group">
                            <label>Current Address</label>
                            <input  id="addrspecific" name="addrspecific" type="text" class="form-control  data-entry" placeholder="Current Address">
                        </div>
                        <div class="form-group">
                            <label>Marital Status<span class="text-danger">*</span></label>
                            <input  type="text" name="marital" id="marital" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Job Category<span class="text-danger">*</span></label>
                            <input type="text" name="search_job_cat" id="search_job_cat" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Cost Group<span class="text-danger">*</span></label>
                            <input type="text" name="costgroup" id="costgroup" class="form-control" />
                        </div>
                        <!--<div class="alert alert-info" role="alert"></div>-->
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Firstname<span class="text-danger">*</span></label>
                            <input  name="firstname" type="text" class="form-control data-entry" id="firstname" placeholder="First Name">
                        </div>
                        <div class="form-group">
                            <label>Birthdate<span class="text-danger">*</span></label>
                            <input  name="bday" type="date" class="form-control data-entry" id="bday" placeholder="">
                        </div>
                        <div class="form-group">
                            <label>City<span class="text-danger"></span></label>
                            <input type="text" name="addrcity" id="addrcity" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Department<span class="text-danger">*</span></label>
                            <input type="text" name="searchdept" id="searchdept" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Agency<span class="text-danger">*</span></label>
                            <input  name="agencyfield" type="text" class="form-control" id="agencyfield" placeholder="Select Agency" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Middlename<span class="text-danger">*</span></label>
                            <input name="middlename" id="middlename" type="text" class="form-control  data-entry" id="middle_initial" placeholder="Middle Name">
                        </div>
                        <div class="form-group">
                            <label>Nick Name</label>
                            <input name="nickname" type="text" class="form-control  data-entry" id="nickname" placeholder="Nick Name">
                        </div>
                        <div class="form-group">
                            <label>District<span class="text-danger"></span></label>
                            <input type="text" name="addrdistrict" id="addrdistrict" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Position<span class="text-danger">*</span></label>
                            <input type="text" name="searchpos" id="searchpos" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Bank Account<span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-1" style="margin-top: 5px;">
                                    000
                                </div>
                                <div class="col-md-10">
                                    <input name="accountno" type="text" class="form-control data-entry number" id="accountno" placeholder="9 digits only without (-)" maxlength="9">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Employment Date<span class="text-danger">*</span></label>
                            <input type="date" class="form-control " id="employmentdate" placeholder="Date Start" name ="datestart" value = "<?php print(date("Y-m-d"))?>">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Suffix</label>
                            <select name="suffix"class="form-control data-entry" id="suffix" class="input-sm">
                                <option value=""></option>
                                <?php foreach (select_person_title(70) as $row) { ?>
                                    <option value="<?php echo $row->sysid; ?>"><?php echo $row->names; ?> - <?php echo $row->descriptions; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Zip Code<span class="text-danger">*</span></label>
                            <input  id="zipcode" name="zipcode" type="text" class="form-control data-entry" placeholder="Zipcode">
                        </div>
                        <div class="form-group">
                            <label>Nationality<span class="text-danger">*</span></label>
                            <input type="text" name="nationality" id="nationality" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Payclass<span class="text-danger">*</span></label>
                            <input type="text" name="searchpay" id="searchpay" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Salary<span class="text-danger">*</span></label>
                            <input  name="salary" type="text" class="form-control data-entry" id="salary" placeholder="Enter salary">
                        </div>
                        <div class="form-group">
                            <div class="btn-group" style="margin-top: 25px !important;">
                                <button class="btn btn-default" type="reset" name="reset"><i class="fa fa-refresh"></i> Reset</button>
                                <button id="save_button" class="btn btn-primary"  type="submit">
                                    <i class="fa fa-save fa-fw"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<hr/><hr/><hr/>


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
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/newemployee.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">

    PECO.select2Basic($('#workshiftselect2'), 'hris/getselect2workshift', 'Select Workshift', true);
    PECO.select2Basic($('#agencyfield'), 'hris/select2agencies', 'Select Agency', true);
    PECO.select2Basic($('#searchdept'), 'hris/select2department', 'Select Department', true);
    PECO.select2Basic($('#searchpos'), 'hris/select2pos', 'Select Position', false);
    PECO.select2Basic($('#searchpay'), 'hris/select2payclass', 'Select Payclass', false);
    PECO.select2Basic($('#search_job_cat'), 'hris/select2jobcat', 'Select Job Category', false);
    PECO.select2Basic($('#marital'), 'hris/select2marital', 'Select Marital', false);
    PECO.select2Basic($('#addrcity'), 'hris/select2city', 'Select City', false);
    PECO.select2Basic($('#addrdistrict'), 'hris/select2district', 'Select District', false);
    PECO.select2Basic($('#nationality'), 'hris/select2nationality', 'Select Nationality', false);
    PECO.select2Basic($('#costgroup'), 'hris/select2costgroup', 'Select Cost Group', false);



   /* if ( $('#bday')[0].type != 'date' ) $('#bday').datepicker();
    if ( $('#employmentdate')[0].type != 'date' ) $('#employmentdate').datepicker(); */


    $('#suffix').select2({'placeholder': 'Suffix', allowClear: true,});


    function setInputFilter(textbox, inputFilter) {
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
            textbox.addEventListener(event, function() {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    this.value = this.oldValue;
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                }
            });
        });
    }

    setInputFilter(document.getElementById("accountno"), function(value) {
        return /^\d*\.?\d*$/.test(value);
    });
</script>
