<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">

<div class="row">
    <div class="col-md-6">
    <div class="portlet light bordered" >
        <div class="portlet-title">
            <div class="caption">
                <h3>File Upload</h3>
            </div>
        </div>
        <div class="portlet-body">
            <form id="frm_upload_application" method="post" action="<?php echo base_url();?>cad/uploadonlineapplication">
                <div class="input-group">
                    <!--<input type="hidden" name="ticket" id="online_ticket_number" class="form-control">
                    <input type="hidden" name="filename" id="upload_filename" class="form-control">
                    <input name="file" class="form-control border border-blue" accept="csv" type="file" id="file" placeholder="Select file. 12345678 - Name of Applicant.csv">
                    <span class="input-group-btn">
                    <button type="submit" class="btn btn-info" id="upload_csv"><i class="fa fa-cloud-download"></i> Upload</button>
                    </span>-->
                </div>
            </form>
            <input id="datafile" name="datafile" class="file" type="file" data-preview-file-type="any" data-upload-url="<?php echo base_url('cad/uploadonlineapplication');?>">
        </div>
    </div>
    </div>
    <div class="col-md-6">
    <div class="portlet light bordered" >
        <div class="portlet-title">
            <div class="caption">
                <b><i class="fa fa-eye"></i> Preview</b>
            </div>
            <span class="pull-right">
                <form method="post" action="<?php echo base_url();?>cad/saveonlineaccount" id="frm_save_online_application">
                    <div class="hidden" id="frm_data">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="input-group-addon bg-red-pink bg-font-red-pink">
                                    ESSR No.
                                </div>
                                <input class="form-control" name="essrno" required>
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </span>
        </div>
        <div class="portlet-body" id="csv_contents">
        </div>
        <div class="portlet-footer" id="csv_footer">
        </div>
    </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/cad/upload.js" type="text/javascript"></script>
<script type="text/javascript">
    UPLOAD.app();
</script>