
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" />

<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>

<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>


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
<style>
    .tabletools-dropdown-on-portlet
    {
        margin-top: 10px;
        margin-right: 10px;
    }


</style>

<?php

$countpecoemployee = $this->db->select("COUNT(sysid) AS totalpecoemp")->from("prime_employee_main")
    ->where(array("status" => 1 , "type" => 1))->get()->row();
$countagencyemp = $this->db->select("COUNT(sysid) AS totalagencyemp")->from("prime_employee_main")
    ->where(array("status" => 1 , "type" => 3))->get()->row();


?>
<div class="row" style="padding: 15px 15px;">
    <form role="form" class="form-horizontal asset-entry-form" id="entry-form-ajaxify">

        <div class="col-md-12">
            <div class="portlet light table ">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Employee List</span>
                        <span class="caption-helper"></span>


                    </div>
                    <div class="tabbable-line pull-right col-md-12 inline-block">
                        <div class="row">
                            <div class="col-md-5 tabbable-line">
                                <ul class="nav nav-tabs emp-stat-btn">
                                    <li data-stat="2" type="button" ><a href="#allemp" data-toggle="tab"><i class="fa fa-list"></i> All</a></li>
                                    <li data-stat="1" type="button" class="active"><a href="#active" data-toggle="tab"><i class="fa fa-check"></i> Active</a></li>
                                    <li data-stat="0" type="button"><a href="#inactive" data-toggle="tab"><i class="fa fa-times"></i> In-Active</a></li>
                                </ul>
                            </div>
                            <div class="col-md-7">
                                <ul class="nav nav-tabs pull-right selected_depart" style="display: inline-block !important;width: 100% !important;">
                                    <div class="input-group row" id="empcontrolfilter" style="width: 100% !important;">
                                        <input style="width: 30% !important;" type="text" name="empjobcat" id="empjobcat" class="form-control col-md-4" />
                                        <input style="width: 36% !important;" type="text" name="emppayclass" id="emppayclass" class="form-control col-md-4" />
                                        <input style="width: 33% !important;" type="text" class="form-control col-md-4" id="departmentview" />
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12" style="margin-top: 20px;">
                            <a href="<?php echo base_url() ?>module/b6692ea5df920cad691c20319a6fffd7a4a766b8/new" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Employee</a>
                            <!--  <a href="#" data-toggle="modal" data-target="#printdtr" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Print DTR</a> -->

                            <a href="<?php echo base_url() ?>module/d0e2dbb0bac1917d360aaf52c01a2a4b669e8cdb/records"   class="btn btn-primary  btn-sm"><i class="fa fa-pencil"></i> Data Entry</a>
                            <a id="reportempsbtn" class="btn btn-primary  btn-sm"><i class="fa fa-table"></i> Report</a>
                          <!--  <a id="contractual" class="btn btn-sm btn-warning pull-right"><i class="fa fa-user"></i> Agencies (<?php echo  ($countagencyemp) ? $countagencyemp->totalagencyemp: 0 ?>)</a>
                            <a id="regular" class="btn btn-sm btn-success pull-right"><i class="fa fa-user"></i> PECO (<?php echo  ($countpecoemployee) ? $countpecoemployee->totalpecoemp: 0 ?>)</a> -->

                        </div>
                    </div>
                    <hr>
                    <table width="100%" class="table table-responsive table-hover table-striped table-condensed table-bordered" id="emptable">
                        <thead>
                        <th class=""></th>

                        <th>Emp. Code</th>
                        <th><i class="fa fa-venus-mars fa-fw text-info"></i> Last Name</th>
                        <th><i class="fa fa-user fa-fw text-info"></i> First Name</th>
                        <th><i class="fa fa-user fa-fw text-info"></i> Middle Name</th>
                        <th><i class="fa fa-user fa-fw text-info"></i> Department</th>
                        <th><i class="fa fa-calendar fa-fw text-info"></i> Added</th>
                        <th><i class="fa fa-user fa-fw text-info"></i> Added By</th>
                        <th class="no-print"><i class="fa fa-gears fa-fw text-info"></i> </th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table><hr>
                </div>
            </div>
        </div>

    </form>

</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>



<script src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js"></script>



<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/portfolio.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/form-fileupload.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/hris/view.js"></script>


<script type="text/javascript">
    HRIS.list('<?php echo $this->uri->segment(2) ?>');
</script>
