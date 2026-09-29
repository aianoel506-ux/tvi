<!-- END PAGE LEVEL STYLES -->

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">



<style>
    .form-md-line-input {
        position: relative !important;
    }
    .form-md-line-input .fileinput .input-group-addon{
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }
    .table .select2-container *{
        padding: 2px 2px !important;
        margin: 0px 0px !important;
        height: 28px !important;
        font-size: 14px !important;
        top: 0px !important;
    }

    .portlet.table {
        padding: 0px 0px !important;
    }

    .table-condensed .md-checkbox.checkonly {
        width: 20px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
    .table-condensed .md-checkbox.checkonly label {
        width: 20px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
    .select2-drop{
        margin-top: 1px !important;
        border-color: #C8C8C8;
        box-shadow: rgba(179,179,179,0.50) 0px 5px 5px 0px;
        background: rgba(255,255,255,0.95);
    }
</style>

<form role="form" class="form-horizontal asset-entry-form" id="frm_submit_reading" action="<?php echo base_url('mrd/submitreading'); ?>" method="post">
    <div class="row">
        <div class="col-md-12 well" >
            <div class="row">
                <input  id="reader_id" name="userid" type="hidden" class="form-control" placeholder="Select Reader" />

                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-addon" id="gdlb_label" style="min-width: 100px;">
                            READER
                        </span>
                        <input style="width: 100%;" id="reader_lastname" name="lastname" type="text" class="form-control" placeholder="Select Reader" />
                        <span class="input-group-addon">
                            GDLB
                        </span>
                        <input style="width: 100%;" id="reader_schedid" name="schedid" type="text" class="form-control" placeholder="No schedule yet." />
                        <span class="input-group-btn">
                        <button id="get_mrd_list" class="btn btn-info btn-sm "><i class="fa fa-search"></i> Get <span class="badge badge-default">2</span></span></button>
                        </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="btn-group pull-right">
                        <button class="btn btn-primary btn-sm" type="submit" id="btn-submit-read"><i class="fa fa-reply-all"></i> Submit Reading <span class="badge badge-default">3</span></button>
                        <button id="btn_import_to_legacy" class="btn btn-danger btn-sm "><i class="fa fa-refresh"></i> Sync To Legacy <span class="badge badge-default">4</span></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="portlet light">
            <div class="portlet-body" style="min-height: 400px;">
                <div class="col-md-6" style="padding-top: 10px;">
                    <div class="row margin-bottom-10">
                        <div class="col-md-6">
                            <ul class="list-group summary no-border">
                                <li class="list-group-item">
                                    <span class="col-md-5 label-name">
                                    Meter Reader
                                    </span>
                                    <span class="col-md-7 label-default" id="reader_name">
                                        N/A
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <input id="custcnt" type="hidden" />
                            <input id="custread" type="hidden" />
                            <ul class="list-group summary no-border">
                                <li class="list-group-item">
                                    <span class="col-md-6 label-name">
                                    Reading
                                    </span>
                                    <span class="col-md-6 label label-default" id="readstat">
                                    0 / 00
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>



                <table class="table table-hover table-condensed table-bordered tbl-zoom margin-top-10" id="tbl_reading_entry">
                    <thead>
                    <!-- <th></th> -->
                    <th width="40px">Seq</th>
                    <th width="40px"></th>
                    <th width="60px">Servno</th>
                    <th width="40px">MTR</th>
                    <th>Name</th>
                    <th width="80px">Meter Serial</th>
                    <th width="40px">MTR No.</th>
                    <th width="80px">Reading</th>
                    <th width="80px">Demand</th>
                    <th width="80px">Net Mtr</th>
                    <!-- <th>Address</th> -->
                    <th width="140px">Control</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <hr>
            </div>

        </div>


    </div>
    <div id="map" class="gmaps margin-bottom-40" style="height:400px; display: none;"></div>

</form>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->

<!-- END PAGE CONTENT-->

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script>

<script type="text/javascript">
    MRD.encoding();
</script>