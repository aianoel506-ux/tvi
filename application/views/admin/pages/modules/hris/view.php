<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/admin/pages/css/profile.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/clockface/css/clockface.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/dropzone/css/dropzone.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css" />
<!-- END PLUGINS USED BY X-EDITABLE -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.buttons.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>


<link href="<?php echo base_url() ;?>assets/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>


<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>
<!-- END PAGE LEVEL STYLES -->

<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>




<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.9/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

<link href="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />


<style>


    .tabletools-dropdown-on-portlet
    {
        margin-top: 10px;
        margin-right: 10px;
    }

    #monthly .calendar-row td .timelog{
        font-size: 10px;
        display: inline-block;
        width: 100%;
    }

    .editable-click, a.editable-click, a.editable-click:hover {
        border-bottom: none !important;

    }

    .tiles .tile {
        position: relative;
    }
    .tiles .item-control {
        position: absolute;
        top: 5px;
        right: 5px;
    }
</style>

<?php

$emp_info = $this->model_query->emp_info($dataid);
$person_info = get_person_info($emp_info->personid);
$addspec = $person_info->info->addrspec;
$birthdate = $person_info->info->birthdate;
$addspec = ($addspec != '') ? $addspec : 'Add Address';
$birthdate = ($birthdate != '') ? $birthdate : 'Add Birthday';
$district = $person_info->info->district;
$city = $person_info->info->city;
$country = $person_info->info->country;
$nationality = $person_info->info->nationality;
$person_pic = get_owner_pic($emp_info->personid, 'person');
$person_credentials = get_person_credentials($emp_info->personid);
$passport = isset($person_credentials->info->passport_num) ? $person_credentials->info->passport_num : 'Add Passport';
$driver = isset($person_credentials->info->driver) ? $person_credentials->info->driver : 'Add Driver\'s License Number';
$license = isset($person_credentials->info->driver_license_expiry) ? $person_credentials->info->driver_license_expiry : 'Add Driver\'s License Expiry';
$sss = isset($person_credentials->info->sss_num) ? $person_credentials->info->sss_num : 'Add SSS Number';
$tin = isset($person_credentials->info->tin_num) ? $person_credentials->info->tin_num : 'Add TIN Number';
$bank = isset($person_credentials->info->bank_name) ? $person_credentials->info->bank_name : 'Add Bank Name';
$bank_id = isset($person_credentials->info->bank_details) ? $person_credentials->info->bank_details : 'Add Bank ID';
$otherid = isset($person_credentials->info->other_ids) ? $person_credentials->info->other_ids : 'Add Other ID Name';
$otherid_id = isset($person_credentials->info->other_ids_id) ? $person_credentials->info->other_ids_id : 'Add Other ID Number';
$philhealth = isset($person_credentials->info->philhealth) ? $person_credentials->info->philhealth : 'Add Philhealth Number';
$pagibig = isset($person_credentials->info->pagibig) ? $person_credentials->info->pagibig : 'Add Pagibig Number';
$datestart = isset(get_emp_datestart($emp_info->sysid)->datestart) ? get_emp_datestart($emp_info->sysid)->datestart : 'Add Date Start';
$department = isset(get_emp_department($emp_info->sysid)->desc) ? get_emp_department($emp_info->sysid)->desc : 'Add Department' ;
$lastdate = isset(get_emp_timelogs_daily($emp_info->sysid)->logdate) ? get_emp_timelogs_daily($emp_info->sysid)->logdate : '';
$lasttime = isset(get_emp_timelogs_daily($emp_info->sysid)->logtime) ? get_emp_timelogs_daily($emp_info->sysid)->logtime : '';
$emp_position = isset(select_emp_position($emp_info->sysid)->names) ? select_emp_position($emp_info->sysid)->names : 'Unassigned Position';
$emp_position_id = isset(select_emp_position($emp_info->sysid)->sysid) ? select_emp_position($emp_info->sysid)->sysid : 0;
$payclass = isset(select_emp_payclass($emp_info->sysid)->names) ? select_emp_payclass($emp_info->sysid)->names : 'Unassigned Payclass';
$empjobcat = isset(select_emp_jobcat($emp_info->sysid)->names) ? select_emp_jobcat($emp_info->sysid)->names : 'Unassigned Job Category';


$check_employment_ended = $this->db->select('tp.sysid, tp.names, tp.desc, mh.specificdate, mh.remarks')
    ->from('prime_employee_main_history AS mh')
    ->join('prime_types_parameter AS tp', 'tp.sysid = mh.statusid')
    ->where(array('mh.dataid' => $dataid, 'tp.codes' => 'EMPSTATUS'))
    ->get()->row();

$employment_status = ($check_employment_ended) ? get_types_label_format($check_employment_ended->sysid) : false;
$employment_ended = ($check_employment_ended) ? $check_employment_ended->specificdate : false;

$biometricid = $this->db->select("bioid")
    ->from("prime_employee_bioid")
    ->where(array("empid" => $dataid, "status" => 1))
    ->get()->row();

/*$checkifemphaveschedule = $this->db->select("pemw.desc")->from("trn_employee_workshift_group as tewg")
    ->join("prime_employee_main_workshift as pemw" ,"pemw.sysid = tewg.workshiftid","left")
    ->where(array("tewg.status" => 301))
    ->order_by("tewg.sysid","desc")
    ->limit(1)
    ->get()->row();
if($checkifemphaveschedule){
    $workshiftcode = ($checkifemphaveschedule) ? $checkifemphaveschedule->desc : 'Unassigned';
}else{ */
$workshift = $this->db->select("pemwm.workshift_id , pemw.desc , pemw.codes")
    ->from("prime_employee_main_workshift_matrix AS pemwm")
    ->join("prime_employee_main_workshift AS pemw" , "pemw.sysid = pemwm.workshift_id" , "left")
    ->where(array("pemwm.empid" => $dataid,  "pemwm.status" => 1))
    ->get()->row();

$workshiftcode = ($workshift) ? $workshift->desc : 'Unassigned';
//}



$getmaritalstatus  = $this->db->select("m.names")->from("persons_marital_status_logs as pmsl")
    ->join("marital as m" , "m.sysid = pmsl.marital_status_id" , "left")
    ->where(array("pmsl.personid" => $emp_info->personid, "pmsl.status" => 1))
    ->get()->row();

$civilstatus = ($getmaritalstatus) ? $getmaritalstatus->names : '';

$getdatestart = $this->db->select("datestart")->from("prime_employee_main")
    ->where(array("personid" =>$emp_info->personid , "status" => 1 ))
    ->get()->row();

$startdate = ($getdatestart) ? $getdatestart->datestart : '';

$gethomephone = $this->db->select("contactstring")->from("person_contact_matrix")
    ->where(array("types" => 1049 , "status" => 1 , "personid" =>$emp_info->personid ))->get()->row();
$getworkphone = $this->db->select("contactstring")->from("person_contact_matrix")
    ->where(array("types" => 1050 , "status" => 1, "personid" =>$emp_info->personid))->get()->row();
$getcellphone = $this->db->select("contactstring")->from("person_contact_matrix")
    ->where(array("types" => 1051 , "status" => 1, "personid" =>$emp_info->personid))->get()->row();
$getemailaddress = $this->db->select("contactstring")->from("person_contact_matrix")
    ->where(array("types" => 1053 , "status" => 1, "personid" =>$emp_info->personid))->get()->row();
$getcompanyemail = $this->db->select("contactstring")->from("person_contact_matrix")
    ->where(array("types" => 1057 , "status" => 1, "personid" =>$emp_info->personid))->get()->row();

$homephone = ($gethomephone) ? $gethomephone->contactstring : '';
$workphone = ($getworkphone) ? $getworkphone->contactstring : '';
$cellphone = ($getcellphone) ? $getcellphone->contactstring : '';
$emailaddress = ($getemailaddress) ? $getemailaddress->contactstring : '';
$companyemailadd  = ($getcompanyemail) ? $getcompanyemail->contactstring : '';
$employeelastname =  $emp_info->lastname;
$employeefirstname = $emp_info->firstname;
$employeemiddlename = $emp_info->middlename;

$getotherinfo = $this->db->select("peoi.height,peoi.weight,peoi.placeofbirth,pebt.type , per.names,pea.educattainment,peoi.license")
    ->from("prime_employee_other_info as peoi")->where(array("peoi.empid" => $emp_info->sysid , "peoi.status" => 1))
    ->join("prime_employee_blood_type as pebt" , "pebt.sysid = peoi.bloodtype" , "left")
    ->join("prime_employee_religions as per" , "per.sysid = peoi.religion" , "left")
    ->join("prime_educational_attainment as pea","pea.sysid = peoi.educattainment","left")
    ->get()->row();
if($getotherinfo){
    $height = ( $getotherinfo->height != '') ?  $getotherinfo->height: 'N/A';
    $weight = ( $getotherinfo->weight != '') ?  $getotherinfo->weight: 'N/A';
    $placeofbirth = ( $getotherinfo->placeofbirth != '') ?  $getotherinfo->placeofbirth: 'N/A';
    $religion =( $getotherinfo->names != '') ?   $getotherinfo->names: 'N/A';
    $bloodtype = ( $getotherinfo->type != '') ?  $getotherinfo->type: 'N/A';
    $educationalattainment =( $getotherinfo->educattainment != '') ?  $getotherinfo->educattainment: 'N/A';
    $titlelicense = ( $getotherinfo->license != '' && $getotherinfo->license!= null) ?  $getotherinfo->license: 'N/A';
}else{
    $height ='N/A';
    $weight ='N/A';
    $placeofbirth = 'N/A';
    $religion ='N/A';
    $bloodtype ='N/A';
    $educationalattainment ='N/A';
    $titlelicense ='N/A';
}

$getagency = $this->db->select("da.sysid,da.code,da.desc")->from("prime_employee_agency_matrix as peam")
    ->join("prime_data_agencies as da","da.sysid = peam.agencyid","left")
    ->where(array("peam.empid"=>$emp_info->sysid , "peam.status" => 1))->get()->row();
$agency = ($getagency) ? $getagency->code : 'N/A';


$getemployeeid = $this->db->select("empid")->from("prime_employee_main")
    ->where(array("sysid" => $emp_info->sysid,"status" => 1))->get()->row();
$employeeid = ($getemployeeid) ? $getemployeeid->empid : '';
$getpayclassid_for_send = $this->db->select("payclass_id")->from("prime_employee_main_payclass")->where(array("emp_id" => $emp_info->sysid , "status" => 1))->get()->row();
$emp_payclass_id = ($getpayclassid_for_send) ? $getpayclassid_for_send->payclass_id : 0;
?>
<div class="row">
    <div class="col-md-3" >
        <div class="btn-group" role="group">
            <a href="<?php echo base_url(); ?>module/f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59/list" class="btn btn-sm btn-primary ">Back  <i class="fa fa-arrow-left"></i></a>
            <button class="btn btn-sm btn-primary pull-right" id="exportbtn">Export  <i class="fa fa-download"></i></button>
        </div>
        <div class="portlet profile-sidebar-portlet" style="position:relative;padding-top: 0px !important;">
            <!-- SIDEBAR USERPIC -->
            <div class="tabbable-line">
                <ul class="id='navs' nav nav-tabs" style="margin-bottom: 0px;">
                    <li class="active"> <a id="profiletab" href="#profilepic" data-toggle="tab" aria-expanded="true"> <i class="fa fa-user fa-fw"></i></a> </li>
                    <li class=""> <a href="#uploadpic" data-toggle="tab" aria-expanded="true"> <i class="fa fa-camera fa-fw"></i></a> </li>
                    <li class="">
                        <a type="button" id="message"
                           class="btn btn-primary popovers"
                           data-container="body"
                           data-trigger="click"
                           data-placement="bottom"
                           data-html="true"
                           data-content="
                                            <textarea class='form-control' rows='8' cols='50' id='messagetxt'></textarea><br /><button class='btn btn-primary' id='sendbtn'>Send</button>
                                            "
                           data-original-title="Message"><i class="fa fa-envelope"></i></a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body tab-content" style="background: #fff; padding-top: 10px;">
                <div class="tab-pane fade in active" id="profilepic">
                    <a href="<?php echo $person_pic; ?>" class="cbp-caption cbp-lightbox iframe"
                       data-title="HRIS">
                        <img id="emppic" src="<?php echo $person_pic; ?>" class="img-responsive center-block" alt="" />
                    </a>
                </div>


                <div class="tab-pane fade in" id="uploadpic">
                    <div class="form-group form-md-line-input" style="padding: 10px 10px;">
                        <form id="uploadform" method="post" enctype="multipart/form-data">

                            <div class="fileinput fileinput-new fileinput-custom" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" data-trigger="fileinput">
                                    <img  id="newemppic" alt="" class="fileinput-new" src="<?php echo get_owner_pic($dataid, 'person'); ?>" />
                                    <div class="fileinput-preview fileinput-exists thumbnail" >
                                    </div>
                                </div>
                                <span class="btn-file">
                                            <input type="file" id="newpic" name="newpic">
                                        </span>
                                <input type="hidden" value="<?php echo $dataid ?>" id="userid" name="userid" />


                            </div>

                        </form>

                    </div>

                </div>

                <div class="profile-usertitle">
                    <?php echo emp_badge_yr_service(get_emp_duration($emp_info->sysid)->numyear);?>
                    <br>
                    <br>
                    <div class="profile-usertitle-name">
                        <?php $pos_info = get_types_label_format($emp_info->position_id, false, true, false, false, false, true); ?>
                        <a href="javascript:;" id="empost" data-type="select2" data-value="<?php echo $emp_position; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Job Position" class="editable editable-click" style="padding: 4px 8px;display: inline; color: <?php echo $pos_info->color; ?>; background: <?php echo $pos_info->background; ?>">  <?php echo get_types_label_format($emp_info->position_id, false, true, false, false, false, true)->text;  //$emp_position; ?></a>
                        <h4><?php echo $employeeid; ?></h4>
                        <h4>P-ID: <?php echo $emp_info->personid; ?></h4>
                    </div>
                    <br>
                </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <ul class="ver-inline-menu margin-top-10">
                    <li class="active"> <a href="#overview" data-toggle="tab" aria-expanded="true"> <i class="fa fa-user fa-fw"></i> Profile </a> </li>
                    <li class=""> <a href="#employment" data-toggle="tab" aria-expanded="true"> <i class="fa fa-building fa-fw"></i> Employment / Rate </a> </li>
                    <li class=""> <a href="#attendance" data-toggle="tab" aria-expanded="true"> <i class="fa fa-calendar fa-fw"></i> Attendance </a> </li>
                    <li class=""> <a href="#scheduletab" data-toggle="tab" aria-expanded="true"> <i class="fa fa-tag fa-fw"></i> Schedule </a> </li>
                    <?php if(user_id() == 20 || user_id() == 1){ ?>
                        <li class=""> <a href="#loans" data-toggle="tab" aria-expanded="true"> <i class="fa fa-tag fa-fw"></i> Loans & Deductions</a> </li>
                        <!--  <li class=""> <a href="#deductions" data-toggle="tab" aria-expanded="true"> <i class="fa fa-tag fa-fw"></i> Deductions </a> </li> -->
                        <!--    <li class=""> <a href="#premiums" data-toggle="tab" aria-expanded="true"> <i class="fa fa-tag fa-fw"></i> Premiums </a> </li> -->
                        <li class=""> <a href="#otherstab" data-toggle="tab" aria-expanded="true"> <i class="fa fa-tag fa-fw"></i> Fix Amount </a> </li>
                    <?php } ?>
                </ul>
            </div>
            <!-- END MENU -->
        </div>
    </div>


    <div class="col-md-9" id="pagescrollid" style="min-height:500px;">
        <!-- PERSONAL INFORMATION CONTENT -->
        <div class="tab-content">
            <h4 class="pull-left">
                <div id="scrolltoid"></div>

                <?php if ($emp_info->status == 1) { ?>
                    <span class=" text-success"><i class="fa fa-check"></i></span>
                <?php } else { ?>
                    <span class=" text-danger"><i class="fa fa-times"></i></span>
                <?php } ?>
                <a href="javascript:;" id="employeelastname" data-type="text" data-value="<?php echo $employeelastname; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Employee Last Name" class="editable editable-click" style="display: inline;">  <?php echo $employeelastname; ?></a>,
                <a href="javascript:;" id="employeefirstname" data-type="text" data-value="<?php echo $employeefirstname; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Employee First Name" class="editable editable-click" style="display: inline;">  <?php echo $employeefirstname; ?></a>
                <a href="javascript:;" id="employeemiddlename" data-type="text" data-value="<?php echo $employeemiddlename; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Employee Middle Name" class="editable editable-click" style="display: inline;">  <?php echo $employeemiddlename; ?></a>

                <input type="hidden" value="<?php echo $dataid; ?>" id="dataid" name="dataid" />
                <input type="hidden" value="<?php echo $emp_info->lastname.', '.$emp_info->firstname; ?>" id="loggeduser" name="loggeduser" />
            </h4>



            <div class="tab-pane active fade in" id="overview" >
                <div class="portlet  box tabbable">

                    <div class="portlet-title tabbable-line" style="margin-top: 0px; padding-top: 0px;">
                        <div class="caption">

                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#personal" data-toggle="tab">
                                    <i class="fa fa-user-circle-o"></i> Personal</a>
                            </li>

                            <li class="">
                                <a href="#accomplishements" data-toggle="tab">
                                    <i class="fa fa-file"></i> Attachments</a>
                            </li>
                            <li class="">
                                <a href="#credintials" data-toggle="tab">
                                    <i class="fa fa-file-archive-o"></i> Credentials</a>
                            </li>
                            <li class="">
                                <a href="#otherinfo" data-toggle="tab">
                                    <i class="fa fa-file-text"></i> Other Information</a>
                            </li>
                            <li class="">
                                <a href="#dependents" data-toggle="tab">
                                    <i class="fa fa-user-secret"></i> Dependents</a>
                            </li>
                            <li class="">
                                <a href="#logs" data-toggle="tab">
                                    <i class="fa fa-history"></i> Logs</a>
                            </li>
                            <!--  <li class="">
                                  <a href="#payslip" data-toggle="tab">
                                      Payslip</a>
                              </li> -->

                        </ul>
                    </div>

                    <div class="portlet-body">


                        <div class="tab-content" style="min-height: 400px; overflow: visible !important;">
                            <div class="tab-pane fade in " id="payslip">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            Payslip Generator
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row">
                                            <form id="submitpayslip" action="<?php echo base_url() ?>payroll/getreportsdata" method="post">

                                                <input type="hidden" value="<?php echo $dataid; ?>" name="specific" />

                                                <?php
                                                $getpayclassid = $this->db->select("payclass_id")
                                                    ->from("prime_employee_main_payclass")
                                                    ->where(array("emp_id" => $dataid , "status" => 1))->get()->row();
                                                ?>

                                                <input type="hidden" value="<?php echo ($getpayclassid) ? 1 : ''; ?>" name="payclass" />
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Month</label>
                                                        <input type="text" name="month" id="monthselect2" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Year</label>
                                                        <input type="text" name="year" id="yearselect2" class="form-control" />
                                                    </div>
                                                </div>
                                                <?php

                                                if($getpayclassid->payclass_id == 128){
                                                    ?>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Type</label>
                                                            <select class="form-control" name="paytype" id="typeselect2">
                                                                <option value="1">1st Half</option>
                                                                <option value="2">2nd Half</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <input type="hidden" name="paytype" value="1" />
                                                    <?php
                                                }

                                                ?>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <button style="margin-top: 23px;" type="submit" class="btn btn-primary"><i class="fa fa-copy"></i> Generate</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade in " id="dependents">
                                <div class="row">
                                    <div class="col-md-3">
                                        <form id="submitempdependents" action="<?php echo base_url() ?>hris/adddependents" method="post">
                                            <input type="hidden" value="<?php echo $dataid ?>" id="userid" name="userid" />
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input required type="text" name="dependentsname" id="dependentsname" class="form-control"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Birthdate</label>
                                                <input required type="date" name="birthdatedependents" id="birthdatedependents" class="form-control" />
                                            </div>
                                            <div class="form-group">
                                                <label>Relation</label>
                                                <input required type="text" name="relationdependents" id="relationdependents" class="form-control"/>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-primary" type="submit">Add Dependents</button>
                                            </div>
                                        </form>

                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered table-hover table-responsive" id="empdependentstable">
                                            <thead>
                                            <th></th>
                                            <th>Name</th>
                                            <th>Birthdate</th>
                                            <th>Relation</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade in active" id="personal">
                                <div class="row">

                                    <div class="col-md-12">
                                        <ul class="list-group summary column">
                                            <li class="list-group-item list-group-item-heading list-group-item-info" style="margin-bottom: 5px !important;"><h4><i class="fa fa-user"></i> Basic Information</h4></li>

                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3"> Birthday<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <!--<a href="javascript:;" id="birthdate" data-type="combodate" data-value="<?php echo $birthdate; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Birthday" class="editable editable-click" style="display: inline;"> <div class="uppercase profile-stat-title"> <?php echo $birthdate; ?></div></a> -->
                                                        <a href="javascript:;" id="birthdate" data-type="combodate" data-value='<?php echo $birthdate; ?>' data-pk="<?php echo $dataid; ?>" data-original-title="Birthday" class="editable editable-click" style="display: inline;">  <?php echo $birthdate; ?></a>
                                                    </div>
                                                    <div class="col-md-3"> Civil Status<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="civilstatus" data-type="select2" data-value="<?php echo $civilstatus; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Civil Status" class="editable editable-click" style="display: inline;">  <?php echo $civilstatus; ?></a>
                                                    </div>

                                                </div>
                                            </li>

                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3"> Gender<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="gender" data-type="select2" data-value='<?php echo gender($emp_info->gender); ?>' data-pk="<?php echo $dataid; ?>" data-original-title="Gender" class="editable editable-click" style="display: inline;">  <?php echo gender($emp_info->gender); ?></a>
                                                    </div>
                                                    <div class="col-md-3"> Nationality<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="nationality" data-type="select2" data-value="<?php echo $nationality; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Nationality" class="editable editable-click" style="display: inline;">  <?php echo $nationality; ?></a>
                                                    </div>

                                                </div>
                                            </li>
                                            <li class="list-group-item list-group-item-heading list-group-item-info" style="margin-bottom: 5px !important;"><h4><i class="fa fa-map-marker"></i>Address & Contacts</h4></li>


                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3">  Address Specific<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="addrspec" data-type="text" data-value="<?php echo $addspec; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Specific Address" class="editable editable-click" style="display: inline;">  <?php echo $addspec; ?></a>
                                                    </div>
                                                    <div class="col-md-3">  Home Phone<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="homephone" data-type="text" data-value="<?php echo $homephone; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Home Phone" class="editable editable-click" style="display: inline;">  <?php echo $homephone; ?></a>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3"> District<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="district" data-type="select2" data-value="<?php echo $district; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="District" class="editable editable-click" style="display: inline;">  <?php echo $district; ?></a>
                                                    </div>
                                                    <div class="col-md-3">  Work Phone<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="workphone" data-type="text" data-value="<?php echo $workphone; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Work Phone" class="editable editable-click" style="display: inline;">  <?php echo $workphone; ?></a>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3"> City<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="city" data-type="select2" data-value="<?php echo $city; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="City" class="editable editable-click" style="display: inline;">  <?php echo $city; ?></a>
                                                    </div>
                                                    <div class="col-md-3">  Cell Phone<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="cellphone" data-type="text" data-value="<?php echo $cellphone; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Cell Phone" class="editable editable-click" style="display: inline;">  <?php echo $cellphone; ?></a>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3"> Country<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="country" data-type="select2" data-value="<?php echo $country; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Country" class="editable editable-click" style="display: inline;">  <?php echo $country; ?></a>
                                                    </div>

                                                    <?php if($employment_status!= false) {?>
                                                        <div class="col-md-3"> Start Date<span class="pull-right">:</span> </div>
                                                        <div class="col-md-3">
                                                            <a href="javascript:;" id="startdate" data-type="combodate" data-value='<?php echo $datestart; ?>' data-pk="<?php echo $dataid; ?>" data-original-title="Date Start" class="editable editable-click" style="display: inline;">  <?php echo $datestart; ?></a>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </li>

                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3">Personal Email<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="emailaddress" data-type="text" data-value="<?php echo $emailaddress; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Email Address" class="editable editable-click" style="display: inline;">  <?php echo $emailaddress; ?></a>
                                                    </div>


                                                    <?php if($employment_ended!= false) {?>
                                                        <div class="col-md-3"> Date Ended<span class="pull-right">:</span> </div>
                                                        <div class="col-md-3">
                                                            <?php echo $employment_ended;?>
                                                        </div>
                                                    <?php } ?>


                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">


                                                    <div class="col-md-3">  Company Email<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="companyemail" data-type="text" data-value="<?php echo $companyemailadd; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Company Email Address" class="editable editable-click" style="display: inline;">  <?php echo $companyemailadd; ?></a>
                                                    </div>

                                                    <?php if($employment_status!= false) {?>
                                                        <div class="col-md-3"> Status<span class="pull-right">:</span> </div>
                                                        <div class="col-md-3">
                                                            <?php echo $employment_status;?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3"> Send Payslip<span class="pull-right">:</span> </div>
                                                    <div class="col-md-9">
                                                        <form id="form_send_payslip" action="<?php echo base_url() ?>payroll/emailpayslip" data-action="<?php echo base_url() ?>payroll/emailpayslip" method="post" class="form-inline" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                                                            <input type="hidden" name="empid" value="<?php echo $dataid; ?>" />
                                                            <input type="hidden" name="payclass" id="send_payslip_payclass" value="<?php echo $emp_payclass_id; ?>" />
                                                            <select id="send_payslip_month" name="month" class="form-control" style="min-width:160px;">
                                                                <?php
                                                                    $currentMonth = (int)date('n');
                                                                    for ($m = 1; $m <= 12; $m++) {
                                                                        $selected = ($m === $currentMonth) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $m . '"' . $selected . '>' . date('F', mktime(0, 0, 0, $m, 1)) . '</option>';
                                                                    }
                                                                ?>
                                                            </select>
                                                            <select id="send_payslip_year" name="year" class="form-control" style="min-width:160px;">
                                                                <?php
                                                                    $currentYear = (int)date('Y');
                                                                    for ($y = $currentYear; $y >= ($currentYear - 10); $y--) {
                                                                        $selected = ($y === $currentYear) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $y . '"' . $selected . '>' . $y . '</option>';
                                                                    }
                                                                ?>
                                                            </select>
                                                            <span id="group_period">
                                                                <select id="send_payslip_period" name="period" class="form-control" style="min-width:160px;">
                                                                    <option value="">Select Period</option>
                                                                    <option value="1" selected="selected">1st Half</option>
                                                                    <option value="2">2nd Half</option>
                                                                </select>
                                                            </span>
                                                            <button type="submit" class="btn btn-success btn-sm" id="btn_send_payslip_submit">Send Payslip</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="list-group-item list-group-item-heading list-group-item-info" style="margin-bottom: 5px !important;"><h3><i class="fa fa-user"></i> USER Last Biometrics Information : <?php
                                                    echo $lastdate;
                                                    echo ' ';
                                                    echo  $lasttime;
                                                    ?></h3></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade in" id="otherinfo">
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="list-group summary column">
                                            <li class="list-group-item list-group-item-heading list-group-item-info" style="margin-bottom: 5px !important;"><h3><i class="fa fa-user"></i> Other Information</h3></li>
                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3">  Height<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="height" data-type="text" data-value="<?php echo $height; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Enter Height" class="editable editable-click" style="display: inline;">  <?php echo $height; ?></a>
                                                    </div>
                                                    <div class="col-md-3">  Weight<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="weight" data-type="text" data-value="<?php echo $weight; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Enter Weight" class="editable editable-click" style="display: inline;">  <?php echo $weight; ?></a>
                                                    </div>


                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3"> Blood Type<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="bloodtype" data-type="select2" data-value="<?php echo $bloodtype; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Blood Type" class="editable editable-click" style="display: inline;">  <?php echo $bloodtype; ?></a>
                                                    </div>
                                                    <div class="col-md-3"> Religion<span class="pull-right">:</span> </div>
                                                    <div class="col-md-3">
                                                        <a href="javascript:;" id="religion" data-type="select2" data-value="<?php echo $religion; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Religion" class="editable editable-click" style="display: inline;">  <?php echo $religion; ?></a>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3">  Place of Birth<span class="pull-right">:</span> </div>
                                                    <div class="col-md-9">
                                                        <a href="javascript:;" id="placeofbirth" data-type="text" data-value="<?php echo $placeofbirth; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Place of Birth" class="editable editable-click" style="display: inline;">  <?php echo $placeofbirth; ?></a>
                                                    </div>


                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3">  Educational Attainment<span class="pull-right">:</span> </div>
                                                    <div class="col-md-9">
                                                        <a href="javascript:;" id="educattainment" data-type="select2" data-value="<?php echo $educationalattainment; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Educational Attainment" class="editable editable-click" style="display: inline;">  <?php echo $educationalattainment; ?></a>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="list-separated profile-stat">
                                                    <div class="col-md-3">  License<span class="pull-right">:</span> </div>
                                                    <div class="col-md-9">
                                                        <a href="javascript:;" id="license" data-type="select2" data-value="<?php echo $titlelicense; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="License title" class="editable editable-click" style="display: inline;">  <?php echo $titlelicense; ?></a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- end dependents -->
                            <!--ACCOMPLISHMENTS BEGIN-->
                            <div class="tab-pane fade in" id="accomplishements">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4><i class="fa fa-search"></i> Explorer
                                            <a title="Upload Attachment : <?php echo $employeefirstname; ?>" data-arr="<?php echo $dataid; ?>" class="btn btn-default btn-xs pull-right" data-toggle="ajax-modal" href="#frm_upload_employee_attachment">
                                                <i class="fa fa-cloud-upload"></i> Upload
                                            </a>
                                        </h4>
                                        <div class="well" id="attachement_container">
                                            <h4><i class="fa fa-times text-danger"></i> No file found!</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--ACCOMPLISHMENTS END-->

                            <!-- CREDENTIALS BEGIN -->
                            <div class="tab-pane fade in" id="credintials">
                                <div class ="row">
                                    <div class="col-md-12">

                                        <ul class="list-group summary column">
                                            <li class="list-group-item list-group-item-heading list-group-item-info" style="margin-bottom: 5px !important;"><h3><i class="fa fa-user"></i> Credential Information</h3></li>
                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                SSS
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="sss" data-type="text" data-value="<?php echo $sss; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill SSS" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $sss; ?></a>
                                                            </span>
                                            </li>

                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                Philhealth
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="philhealth" data-type="text" data-value="<?php echo $philhealth; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill Philhealth" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $philhealth; ?></a>
                                                            </span>
                                            </li>
                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                Pag-ibig
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="pagibig" data-type="text" data-value="<?php echo $pagibig; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill Pagibig" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $pagibig; ?></a>
                                                            </span>
                                            </li>
                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                TIN
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="tin" data-type="text" data-value="<?php echo $tin; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill TIN" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $tin; ?></a>
                                                            </span>
                                            </li>

                                            <li class="list-group-item list-group-item-heading list-group-item-info" style="margin-bottom: 5px !important;"><h3><i class="fa fa-map-marker"></i>Other Credentials</h3></li>


                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                Passport
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="passport" data-type="text" data-value="<?php echo $passport; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill Passport" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $passport; ?></a>
                                                            </span>
                                            </li>

                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                 Licence
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="driver" data-type="text" data-value="<?php echo $driver; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill Drivers License" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $driver; ?></a>
                                                            </span>
                                            </li>

                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                Licence Expiry
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="driverexp" data-type="text" data-value="<?php echo $license; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill Drivers Expiry" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $license; ?></a>
                                                            </span>
                                            </li>

                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                Bank Account
                                                            </span>
                                                <span class="col-md-4 label label-default">
                                                                <a href="javascript:;" id="bank" data-type="text" data-value="<?php echo $bank; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill Bank Name" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $bank; ?></a> :
                                                                <a maxlength="9" href="javascript:;" id="bankid" data-type="text" data-value="<?php echo $bank_id; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Example if the Account No. is 000-810-123-123 then you must input 810123123 without (-) dashed " class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $bank_id; ?></a>
                                                            </span>
                                                <span class="col-md-6 text-danger">
                                                        <span class="bold "> NOTE! </span>
                                                        Enter numbers only<br>
                                                    </span>
                                            </li>

                                            <li class="list-group-item">
                                                            <span class="col-md-2 label-name">
                                                                Other ID
                                                            </span>
                                                <span class="col-md-5 label label-default">
                                                                <a href="javascript:;" id="other" data-type="text" data-value="<?php echo $otherid; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill SSS" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $otherid; ?></a> :

                                                                <a href="javascript:;" id="otherid" data-type="text" data-value="<?php echo $otherid_id; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Fill SSS" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo $otherid_id; ?></a>

                                                            </span>
                                            </li>


                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <!-- CREDENTIALS END -->



                            <!-- LOGS BEGIN -->

                            <div class="tab-pane fade in " id="logs">
                                <div class="row">
                                    <div class="col-md-3">
                                        <form id="addemployeelog" action="<?php echo base_url() ?>hris/addemployeelog" method="post">
                                            <input type="hidden" value="<?php echo $dataid ?>" id="userid" name="userid" />
                                            <input type="hidden" value="<?php echo $navid ?>" id="navid" name="navid" />
                                            <div class="form-group">
                                                <label>Specific Date</label>
                                                <input required type="text" name="specificdate" id="input_specificdate" class="form-control" placeholder="<?php echo date('Y-m-d')?>" value="<?php echo date('Y-m-d')?>"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea required rows="3" name="remarks" id="input_remarks" class="form-control" placeholder="Input Remarks..."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Employee Status</label>
                                                <input type="text" name="empstatus" data-id="<?php echo $dataid;?>" id="select2empstatus" class="form-control"/>
                                            </div>
                                            <div class="form-group">
                                                <!-- <button class="btn btn-primary" type="submit">Add Log</button> -->
                                                <input type="submit" class="btn btn-primary" value="Add Log">
                                            </div>
                                        </form>

                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered table-hover table-responsive" id="dt_logs">
                                            <thead>
                                            <th>#</th>
                                            <th>Created</th>
                                            <th>Specific</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- LOGS END -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- end overview tab -->



            <div class="tab-pane fade in" id="premiums" >
                <div class="portlet  box tabbable table">

                    <div class="portlet-title tabbable-line">
                        <div class="caption">

                        </div>

                        <ul class="nav nav-tabs" id="tab_premiums">

                            <?php
                            $qry_empcont = $this->db->select()->from('prime_types_parameter')
                                ->where(array('codes' => 'EMPCONT', 'status' => 1))->get();
                            if($qry_empcont->num_rows()>0) {
                                foreach($qry_empcont->result() as $crow) {
                                    $active = '';
                                    if($crow->sysid == 72) {
                                        $active = 'active';
                                    }
                                    if($crow->sysid == 1009){
                                        $checkemppayclass = $this->db->select("payclass_id")->from("prime_employee_main_payclass")
                                            ->where(array("emp_id" => $dataid))->get()->row();
                                        if($checkemppayclass){
                                            if($checkemppayclass->payclass_id == 128){
                                                echo '<li class="'.$active.'"><a data-toggle="tab" href="#'.$crow->names.'" data-id="'.$crow->sysid.'">'.$crow->names.'</a></li>';
                                            }
                                        }

                                    }else{
                                        echo '<li class="'.$active.'"><a data-toggle="tab" href="#'.$crow->names.'" data-id="'.$crow->sysid.'">'.$crow->names.'</a></li>';
                                    }

                                }
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="portlet-body" style="margin-top: -30px !important">
                        <div class="tab-content" style="min-height: 400px;">

                            <div class="tab-pane fade in active">

                                <hr>
                                <h4 id="conttitle">SSS</h4>
                                <input type="hidden" id="hiddentabid" />
                                <span class="pull-right text-info" style="margin-right: 10px;">Contribution History</span>
                                <table class="table table-hover table-condensed table-bordered table-striped tbl-sm" id="tbl_premiums">
                                    <thead>
                                    <th width="20px"></th>
                                    <th><i class="fa fa-reorder"></i> Amount</th>
                                    <th>For</th>
                                    <th>Per Month</th>
                                    <th>Date Created</th>
                                    <th>Control</th>
                                    </thead>
                                </table>
                                <a style="margin-left: 10px" href="#form_add_premium_entry" id="btn_add_premium" data-toggle="ajax-modal" data-view="72" data-arr="<?php echo $emp_info->sysid; ?>" class="btn btn-primary pull-left btn-sm"><i class="fa fa-plus"></i>Add SSS</a>
                                <div class="row">
                                    <hr>
                                    <span class="col-md-4 pull-right text-info" style="margin-right: 10px;">

                                                    <ul class="list-group summary">
                                                        <li class="list-group-item"> TOTAL PAID: <span class="label label-default pull-right" id="totalpaidpremium">00.00</span> </li>
                                                        <li class="list-group-item"> TOTAL UNPAID: <span class="label label-default pull-right" id="totalunpaidpremium">00.00</span> </li>
                                                    </ul>
                                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="tab-pane fade in" id="otherstab" >
                <div class="portlet  box tabbable table">

                    <div class="portlet-title">
                        <div class="caption">

                        </div>

                    </div>

                    <div class="portlet-body" style="margin-top: -30px !important">
                        <div class="tab-content" style="min-height: 400px;">
                            <div class="tab-pane fade in active" id="">

                                <div class="tab-pane active fade in" id="otherstab" >
                                    <div class="row">
                                        <div class="col-md-4">

                                            <form id="submitpayrollfixamt" action="<?php echo base_url() ?>payroll/addpayrollfixamt" method="post">
                                                <input required type="hidden" name="empid" value="<?php echo $dataid; ?>" />
                                                <div class="form-group">
                                                    <label>Type</label>
                                                    <input required type="text" id="fixtype" name="types" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input required type="text" id="fixamt" name="amt" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</button>
                                                </div>
                                            </form>

                                        </div>
                                        <div class="col-md-8">
                                            <table class="table table-hover table-bordered" id="payrollfixamttable">
                                                <thead>
                                                <th></th>
                                                <th>Types</th>
                                                <th>Amount</th>
                                                <th>Date Created</th>
                                                <th></th>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade in" id="loans" >
                <div class="portlet  box tabbable table">

                    <div class="portlet-title  tabbable-line">
                        <div class="caption">

                        </div>
                        <ul class="nav nav-tabs" id="tab_loans">
                            <?php
                            $qry_emploans = $this->db->select("ptp.sysid,ptp.names")->from('payroll_matrix as pm')
                                ->join("prime_types_parameter as ptp" , "ptp.sysid = pm.typesid","left")
                                ->where(array('pm.distributed' => 1, 'ptp.status' => 1))->get();
                            if($qry_emploans->num_rows()>0) {
                                foreach($qry_emploans->result() as $crow) {
                                    $active = '';
                                    if($crow->sysid == 257) {
                                        $active = 'active';
                                    }
                                    echo '<li class="'.$active.'"><a data-toggle="tab" href="#'.$crow->names.'" data-id="'.$crow->sysid.'">'.$crow->names.'</a></li>';
                                }
                            }
                            ?>

                        </ul>
                    </div>

                    <div class="portlet-body" style="margin-top: -30px !important">
                        <div class="tab-content" style="min-height: 400px;">

                            <div class="tab-pane fade in active">

                                <hr>
                                <h4 id="loanstitle">SSS</h4>
                                <input type="hidden" id="hiddentabid" />
                                <span class="pull-right text-info" style="margin-right: 10px;">Loans History</span>
                                <table class="table table-hover table-condensed table-bordered table-striped tbl-sm" id="tbl_loans">
                                    <thead>
                                    <th width="20px"></th>
                                    <th>Amount</th>
                                    <th>For</th>
                                    <th>Per Month</th>
                                    <th>Loan type</th>
                                    <th>Date Created</th>
                                    <th>Control</th>
                                    </thead>
                                </table>
                                <a style="margin-left: 10px" href="#form_loans_entry" id="btn_add_loans" data-toggle="ajax-modal" data-view="257" data-arr="<?php echo $emp_info->sysid; ?>" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>Add SSS</a>

                                <div class="row">
                                    <hr>
                                    <span class="col-md-4 pull-right text-info" style="margin-right: 10px;">

                                                    <ul class="list-group summary">
                                                        <li class="list-group-item"> TOTAL PAID: <span class="label label-default pull-right" id="totalpaidloans">00.00</span> </li>
                                                        <li class="list-group-item"> TOTAL UNPAID: <span class="label label-default pull-right" id="totalunpaidloans">00.00</span> </li>
                                                    </ul>
                                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="tab-pane fade in" id="employment">
                <div class="portlet  box tabbable">
                    <div class="portlet-body">
                        <!-- STAT -->
                        <div class="row list-separated profile-stat">
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <a href="javascript:;" id="datestart" data-type="combodate" data-value="<?php echo $datestart; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Date Start" class="editable editable-click" style="display: inline;"> <div class="uppercase profile-stat-title"> <?php echo $datestart; ?></div></a>

                                <div class="uppercase profile-stat-text"> Date Employed </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <a href="javascript:;" id="payclass" data-type="select2" data-value="<?php echo $payclass; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Payclass" class="editable editable-click" style="display: inline;"> <div class="uppercase profile-stat-title"> <?php echo $payclass; ?></div></a>

                                <div class="uppercase profile-stat-text"> Pay Class </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-3">

                                <a href="javascript:;" id="jobcat" data-type="select2" data-value="<?php echo $empjobcat; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Job Category" class="editable editable-click" style="display: inline;"> <div class="uppercase profile-stat-title"> <?php echo $empjobcat; ?></div></a>


                                <div class="uppercase profile-stat-text"> Status </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <a href="javascript:;" id="agency" data-type="select2" data-value="<?php echo $agency; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Agency" class="editable editable-click" style="display: inline;"> <div class="uppercase profile-stat-title"> <?php echo $agency; ?></div></a>
                                <div class="uppercase profile-stat-text"> Agency </div>
                            </div>
                        </div>
                        <!-- END STAT -->
                        <div class="row list-separated profile-stat">
                            <div class="col-md-3"> Duration <span class="pull-right">:</span> </div>
                            <div class="col-md-9">
                                <?php echo  get_emp_duration($emp_info->sysid)->timespent; ?>

                            </div>

                        </div>
                        <div class="row list-separated profile-stat">
                            <div class="col-md-3"> Department<span class="pull-right">:</span> </div>
                            <div class="col-md-9">
                                <a href="javascript:;" id="department" data-type="select2" data-value="<?php echo $department; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Department" class="editable editable-click" style="display: inline;">  <?php echo $department; ?></a>
                            </div>
                        </div>



                        <!-- add for positio -->
                        <div class="row list-separated profile-stat">
                            <div class="col-md-3"> Position<span class="pull-right">:</span> </div>
                            <div class="col-md-9">
                                <a href="javascript:;" id="position" data-type="select2" data-value="<?php echo $emp_position; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Position" class="editable editable-click" style="display: inline;">  <?php echo $emp_position; ?></a>

                            </div>
                        </div> <!-- add for positio -->
                        <?php
                        $divider = 249;
                        //ACCORDING TO SIR JONATHAN IF THE POSITION ARE TS / SB / MR THE DIVISOR IS 301 ELSE 249
                        $onedayoffposition = array(173 , 174 , 164);
                        if (in_array($emp_position_id, $onedayoffposition) && in_array($payclass,array(3077,3078))) {
                            $divider = 301;
                        }

                        ?>

                        <div class="row list-separated profile-stat">
                            <div class="col-md-3"> Daily Rate<span class="pull-right">:</span> </div>
                            <div class="col-md-9">
                                <a href="javascript:;"  data-type="select2" data-value="<?php echo get_emp_basic_salary($emp_info->sysid)->daily; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Position" class="editable editable-click" style="display: inline;">  <?php echo get_emp_basic_salary($emp_info->sysid)->daily; ?></a>

                            </div>
                        </div> <!-- add for positio -->
                        <div class="row list-separated profile-stat">
                            <div class="col-md-3"> Hourly Rate<span class="pull-right">:</span> </div>
                            <div class="col-md-9">
                                <a href="javascript:;" data-type="select2" data-value="<?php echo get_emp_basic_salary($emp_info->sysid)->hourly; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Position" class="editable editable-click" style="display: inline;">  <?php echo get_emp_basic_salary($emp_info->sysid)->hourly; ?></a>

                            </div>
                        </div>
                        <!-- end for position -->

                        <?php
                        if(user_id() == 1 || user_id() == 20){

                            ?>
                            <div class="row  list-separated profile-stat">
                                <div class="col-md-3"> Basic<span class="pull-right">:</span></div>
                                <div class="col-md-9">
                                    <div class="portlet">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <a href="javascript:;" id="salary" data-type="text" data-value="<?php echo get_emp_basic_salary($emp_info->sysid)->amt; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Enter Salary" class="editable editable-click" style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo get_emp_basic_salary($emp_info->sysid)->amt; ?></a>
                                            </div>
                                            <div class="tools">
                                                <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body portlet-collapsed">
                                            <div class="table-scrollable">
                                                <table class="table table-striped table-bordered table-advance table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="hidden-xs">Amount</th>
                                                        <th class="hidden-xs">Increased</th>
                                                        <th class="hidden-xs">Purpose</th>
                                                        <th class="hidden-xs">Remarks</th>

                                                        <th></th>
                                                        <th>Date Applied </th>


                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <?php
                                                    $getsalaryhist = $this->db->select("amt,datecreated,status,trnid")->from("prime_employee_salary")
                                                        ->where(array("empid" => $emp_info->sysid))->order_by("datecreated" ,"desc")->get();
                                                    if($getsalaryhist->num_rows() > 0){
                                                        $num = 1;
                                                        foreach ($getsalaryhist->result() as $row){

                                                            $getsalarytransactions = $this->db->select("tes.increase,tes.purpose,tes.remarks,ptp.names")
                                                                ->from("trn_employee_salary as tes")
                                                                ->join("prime_types_parameter as ptp","ptp.sysid = tes.purpose","left")
                                                                ->where(array("tes.empid" => $emp_info->sysid , "tes.status" => 1 , "tes.trnid" => $row->trnid))->get()->row();

                                                            $stat = ($row->status == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> Current</span>' : '';
                                                            $increase = ($getsalarytransactions) ? $getsalarytransactions->increase : '';
                                                            $purpose = ($getsalarytransactions) ? $getsalarytransactions->names : '';
                                                            $remarks = ($getsalarytransactions) ? $getsalarytransactions->remarks : '';
                                                            echo '
                                                                           <tr>
                                                                                <td class="highlight">'.$num++.'</td>
                                                                                <td class="highlight">'.$row->amt.'</td>
                                                                                <td class="hidden-xs">'.$increase.'</td>
                                                                                <td class="hidden-xs">'.$purpose.'</td>
                                                                                <td class="hidden-xs">'.$remarks.'</td>
                                                                    
                                                                                <td class="hidden-xs">'.$stat.'</td>
                                                                                <td class="hidden-xs">'.$row->datecreated.'</td>
                                                                         
                                                                            </tr>
                                                                        ';
                                                        }
                                                    }

                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade in" id="scheduletab" >
                <div class="row">
                    <div class="col-md-12">

                        <div class="portlet">
                            <div class="portlet-title">

                                <div class="caption">
                                    <div class="page-toolbar">
                                        <div class="btn-group">
                                            <div class="tabbable-line">

                                                <ul class="nav nav-tabs pull-right">
                                                    <li class="active">
                                                        <a href="#weekly" data-toggle="tab" aria-expanded="true">
                                                            <i class="fa fa-bar-chart"></i> Weekly</a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#monthly" data-toggle="tab" aria-expanded="true">
                                                            <i class="fa fa-calendar"></i> Monthly</a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#reqsched" data-toggle="tab" aria-expanded="true">
                                                            <i class="fa fa-calendar"></i> Request Schedule</a>
                                                    </li>
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                        <a href="javascript:;" id="workshift" data-type="select2" data-pk="<?php echo $dataid; ?>" data-value="<?php echo $workshiftcode; ?>" data-original-title="Workshift" class="editable editable-click pull-right"> <i class="fa fa-plus fa-fw"></i> <?php echo $workshiftcode; ?></a>
                                    </div> -->
                                <div class="col-md-2 pull-right">
                                    Workshift:
                                    <a href="javascript:;" id="workshift" data-type="select2" data-value="<?php echo $workshiftcode; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Workshift" class="editable editable-click" style="display: inline;">  <?php echo $workshiftcode; ?></a>

                                </div>

                            </div>

                            <div class="portlet-body">
                                <div class="tab-content">
                                    <div class="tab-pane" id="reqsched">
                                        <div class="row">
                                            <form id="submitreqsched" action="<?php echo base_url() ?>hris/submitreqsched" method="post">

                                                <div class="col-md-3">

                                                    <div class="form-group">
                                                        <label>AM TIME IN</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-clock-o"></i>
                                                            <input name="amtimein" class="form-control timepicker timepicker-default" type="text" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>AM TIME OUT</label>

                                                        <div class="input-icon">
                                                            <i class="fa fa-clock-o"></i>
                                                            <input name="amtimeout" class="form-control timepicker timepicker-default" type="text" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>PM TIME IN</label>

                                                        <div class="input-icon">
                                                            <i class="fa fa-clock-o"></i>
                                                            <input name="pmtimein" class="form-control timepicker timepicker-default" type="text" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>PM TIME OUT</label>

                                                        <div class="input-icon">
                                                            <i class="fa fa-clock-o"></i>
                                                            <input name="pmtimeout" class="form-control timepicker timepicker-default" type="text" >
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>From</label>
                                                        <input type="date" name="fromdate" id="reqdate" class="form-control"  required/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>To</label>
                                                        <input type="date" name="todate" id="reqdate" class="form-control"  required/>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Team Assignment</label>
                                                        <input name="teamassign" id="teamassign" class="form-control" type="text">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Branch</label>
                                                        <input name="branch" id="branch" class="form-control" type="text">
                                                    </div>

                                                </div>

                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Logs Count</label>
                                                                <input name="logscount" class="form-control" type="text" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Log Type</label>
                                                                <input name="logtype" class="form-control" type="text" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Remarks</label>
                                                                <textarea class="form-control" rows="4" cols="50" name="remarkstxt" placeholder="Enter remarks here ..." required></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-primary pull-right">Send</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                <input type="hidden" value="<?php echo $dataid; ?>" name="empid" class="form-control" />

                                            </form>
                                        </div>

                                    </div>

                                    <div class="tab-pane active" id="weekly">

                                        <table class="table table-bordered table-hover table-responsive" id="scheduletable">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>TIME</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th>WEEKLY</th>
                                                <th>AM</th>
                                                <th>PM</th>
                                                <th>STATUS</th>
                                            </tr>

                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="monthly">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Month</label>
                                                    <input type="text" class="form-control" value="<?php echo date('m') ?>" name="month" id="schedmonth"/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Year</label>
                                                    <input type="text" class="form-control" value="<?php echo date('Y') ?>" name="year" id="schedyear"/>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-default" id="btn_filter_monthly_sched" >Filter</button>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div id="empcalendar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade in" id="attendance" >
                <div class="row">
                    <div class="portlet box tabbable table">
                        <div class="portlet-title">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a id="btn-dtr-generate" data-id ="<?php echo $dataid ?>" href="#dtr" data-toggle="tab"><i class="fa fa-search"></i>Generate DTR</a>
                                </li>

                                <li>
                                    <a href="#leavehistory" data-toggle="tab"><i class="fa fa-history"></i>Leave History</a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body " style="padding: 15px 15px !important; margin-top: -20px !important">

                            <div class="tab-content">

                                <div class="tab-pane fade in active" id="dtr">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="portlet solid bg-color-lighten">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-calendar"></i>Attendance Time Range
                                                    </div>
                                                    <div class="tools">
                                                        <a href="javascript:;" class="collapse" data-original-title="" title="">
                                                        </a>
                                                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                                        </a>
                                                        <a href="" class="fullscreen" data-original-title="" title="">
                                                        </a>
                                                        <a href="javascript:;" class="reload" data-original-title="" title="">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="portlet-body" style="min-height: 280px;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p style="padding: 5px 5px; padding-bottom: 0px;"><i class="fa fa-calendar fa-fw"></i>From:</p>
                                                            <div class="date-picker-start" id="date-picker-start" data-date="yyyy-mm-01" style="display: block;"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p style="padding: 5px 5px; padding-bottom: 0px;"><i class="fa fa-calendar fa-fw"></i>To:</p>
                                                            <div class="date-picker-end" id="date-picker-end" data-date="new Date()" style="display: block;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 ">
                                            <div class="portlet box green-meadow">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-file-text-o"></i>Attendance Summary
                                                    </div>
                                                    <div class="tools">
                                                        <a href="javascript:;" class="collapse" data-original-title="" title="">
                                                        </a>
                                                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                                        </a>
                                                        <a href="" class="fullscreen" data-original-title="" title="">
                                                        </a>
                                                        <a href="javascript:;" class="reload" data-original-title="" title="">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="portlet-body" style="min-height: 280px;">

                                                    <ul class="list-group summary column">
                                                        <li class="list-group-item">
                                                                        <span class="col-md-6 label-name">
                                                                             Biometric ID
                                                                        </span>
                                                            <span class="col-md-6 label label-default"><a id="bioid" name="bioid" data-type="text" data-value="<?php echo ($biometricid) ?  $biometricid->bioid : ''; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Modify Biometric ID" class="editable editable-click " style="display: inline;"> <i class="fa fa-plus fa-fw"></i> <?php echo  ($biometricid) ?  $biometricid->bioid : ''; ?></a></span>
                                                        </li>

                                                        <li class="list-group-item">
                                                            <span class="col-md-6 label-name">DTR Date Range</span>
                                                            <span class="col-md-6 label-default pull-right" id="daterangesum"></span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span class="col-md-6 label-name">Total UT</span>
                                                            <span class="col-md-6 label label-default pull-right" id="totalutsum"></span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span class="col-md-6 label-name">Total Late</span>
                                                            <span class="col-md-6 label label-default pull-right" id="totallatesum"></span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span class="col-md-6 label-name">Total Overtime</span>
                                                            <span class="col-md-6 label label-default pull-right" id="totalotsum"></span>
                                                        </li>
                                                        <button class="btn btn-primary pull-right" id="printtimelogs">Print</button>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <div class="portlet blue box">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-calendar"></i>Time Logs

                                                </div>
                                                <div class="tools">
                                                    <a href="javascript:;" class="collapse" data-original-title="" title="">
                                                    </a>
                                                    <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                                    </a>
                                                    <a href="" class="fullscreen" data-original-title="" title="">
                                                    </a>
                                                    <a href="javascript:;" class="reload" data-original-title="" title="">
                                                    </a>
                                                </div>
                                                <br>
                                                <h3 style="color: white !important;">Default Workshift: <?php echo $workshiftcode; ?> </h3>
                                            </div>
                                            <div class="portlet-body">
                                                <table class="table table-responsive table-striped table-condensed table-bordered tbl-xs" id="tbl_dtr">
                                                    <thead>
                                                    <tr>
                                                        <th rowspan="2" valign="middle"><center>
                                                                LOG DATE
                                                            </center></th>
                                                        <th colspan="3">AM</th>
                                                        <th colspan="3">PM</th>
                                                        <th colspan="2">OT</th>
                                                        <th colspan="2">LOCATOR</th>
                                                        <th rowspan="2"><center>
                                                                STATUS
                                                            </center></th>
                                                        <th rowspan="2"><center>
                                                                LATE / UT
                                                            </center></th>
                                                    </tr>
                                                    <tr>
                                                        <th>IN</th>
                                                        <th>OUT</th>
                                                        <th>Late</th>
                                                        <th>IN</th>
                                                        <th>OUT</th>
                                                        <th>Late</th>
                                                        <th>IN</th>
                                                        <th>OUT</th>
                                                        <th>IN</th>
                                                        <th>OUT</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade in" id="leavehistory">
                                    <ul class="list-group summary column" id="list_leave_credits"></ul>
                                    <hr>
                                    <table class="table table-hover table-hover table-advance" id="tbl_leave_history">
                                        <thead>
                                        <th><i class="fa fa-reorder"></i></th>
                                        <th>Date Application</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>From Time</th>
                                        <th>To Time</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<hr>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>


<script src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/dropzone/dropzone.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/pages/scripts/form-dropzone.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.zh-CN.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
 
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.buttons.min.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/buttons/buttons.flash.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/buttons/jszip.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/buttons/pdfmake.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/buttons/vfs_fonts.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/buttons/buttons.html5.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/buttons/buttons.print.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>

<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/form-fileupload.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>

<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js"></script>


<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/portfolio.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>


<script src="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>


<!--  <script src="<?php echo base_url(); ?>assets/admin/pages/scripts/components-form-tools.js"></script> -->

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
 
<script src="<?php echo base_url(); ?>assets/pages/hris/view.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/hris/form-editable.js"></script>
<script>
    FormEditable.init();
    HRIS.init('<?php echo $this->uri->segment(1); ?>', <?php echo $dataid; ?>);
    HRIS.init_data(<?php echo $dataid; ?>);

</script>
