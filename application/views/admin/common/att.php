<?php
$trnid = ($this->uri->segment(4)) ? $this->uri->segment(4) : false;
$qry_trl = $this->db->select()->from('transaction_request_main_trails')
    ->where(array('dataid' => $dataid, 'sysid' => $trnid))
    ->get()->row();

//echo $this->db->last_query();
$uploadurl = 'admin/uploadpics';

if ($qry_trl) {
    $location = get_stage_specific($qry_trl->stageid)->desc;
    $url = array(
        92 => 'inspection/uploadsurveypics',
        95 => '',
        98 => '',
        99 => ''
    );

    $uploadurl = $url[$qry_trl->stageid];
}

?>


<link href="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ;?>assets/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>

<!-- END PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css"/>

<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url() ?>assets/pages/css/portfolio.min.css" rel="stylesheet" type="text/css" />


<style>

    .modal-backdrop {
        margin-top: 50px !important;
    }

    .modal-dialog {
        margin-top: 100px !important;
        top: 100px !important;
    }

</style>
<?php /*?>
<div class="row">
    <div class="col-md-8">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption"> <i class="fa fa-edit"></i> <span class="caption-subject font-green-sharp bold uppercase">Attachment(s)</span> <span class="caption-helper"></span> </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#requirements" data-toggle="tab">Requirements</a>
                    </li>
                    <li class="">
                        <a href="#installation" data-toggle="tab">Installation</a>
                    </li>
                </ul>
                <div class="tools">

                </div>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div id="requirements" class="tab-pane fade in active">
                        <div class="col-md-3 pull-left" style="margin-left: -15px;">
                            <a class="btn btn-default inline" id='btn_add_app_req' data-toggle="ajax-modal" href="#frm_cad_add_requirements" data-arr="<?php echo $dataid;?>"><i class="fa fa-plus"></i> Add Requirements</a>
                        </div>
                        <div class="col-md-2 pull-left" style="margin-left: -15px;">
                            <a href="javascript:;" id="btn_reload_reqlist" class="btn btn-default inline"><i class="fa fa-refresh"></i> Reload</a>
                        </div>
                        <table class="table table-hover table-striped table-condensed" id="tbl_requirements_list">
                            <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Compliance</th>
                            <th><i class="fa fa-wrench"></i></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="installation" class="tab-pane fade in">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="javascript:;" id="btn_reload_attachments" class="btn btn-default inline pull-right"><i class="fa fa-refresh"></i> Reload</a>
                            </div>
                        </div>
                        <div id="box_file_explorer" class="well mt-element-card mt-element-overlay" style="display: inline-block; width: 100%; min-height: 300px; border: 4px dashed #ccc; text-align: left;">
                            <h3><i class="fa fa-warning text-warning"></i> No file uploaded yet!</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="form-group">
            <h4>Upload Documents</h4>
            <hr>
            <input type="hidden"  id="input_stageid" value="<?php echo $qry_trl->stageid; ?>" />
            File Type
            <input id="select2filetype" name="filetype" class="form-control" placeholder="(optional).." />
            <hr>
            Browse File
            <input id="reqfiledrop" placeholder="Browse file..." name="reqfiledrop" data-upload-url="<?php echo base_url('admin/uploadpics'); ?>" multiple class="file" type="file" data-preview-file-type="any"  />
        </div>
    </div>
</div>
<?php */?>

<div class="row">
    <div class="col-md-8">
        <?php if ($qry_trl && $qry_trl->stageid == 92) {
            ?>
            <div class="portlet light bordered">
                <div class="portlet-title tabbable-line">
                    <div class="caption"> <i class="fa fa-edit"></i> <span class="caption-subject font-green-sharp bold uppercase">Attachment(s)</span> <span class="caption-helper"></span> </div>

                    <div class="tools">

                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="javascript:" id="btn_reload_attachments" class="btn btn-default inline pull-right"><i class="fa fa-refresh"></i> Reload</a>
                        </div>
                    </div>
                    <div id="box_file_explorer" class="well mt-element-card mt-element-overlay" style="display: inline-block; width: 100%; min-height: 300px; border: 4px dashed #ccc; text-align: left;" data-folder="<?php echo $location;?>" >
                        <h3><i class="fa fa-warning text-warning"></i> No file uploaded yet!</h3>
                    </div>
                </div>
            </div>
        <?php }?>

    </div>
    <div class="col-md-4">
        <div class="form-group">
            <h4>Upload Documents</h4>
            <hr>
            <input type="hidden"  id="input_stageid" value="<?php echo $qry_trl->stageid; ?>" />

            File Type
            <input id="select2filetype" name="filetype" class="form-control" placeholder="(optional).." />
            <hr>
            Browse File
            <input id="appfiledrop" placeholder="Browse file..." name="appfiledrop" data-upload-url="<?php echo base_url($uploadurl); ?>" multiple class="file" type="file" data-preview-file-type="any"  />
        </div>
    </div>
</div>



<div class="modal fade draggable-modal" id="assignfilemodallist" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog" style="width: 900px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Select Files</h4>

            </div>
            <div class="modal-body table">
                <div class="row">
                    <div id="divfiles" class="well col-md-12" style="height: 280px;overflow-y: scroll;background-color: white;">
                        <input type="hidden" name="requirementshiddenval" id="requirementshiddenval" />
                        <div id="showpictures"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>




<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/portfolio.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/form-fileupload.js"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>



<script src="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/attachements/main.js"></script>


<script>
    ATTACHEMENTS.init(<?php echo $dataid; ?>);
</script>
