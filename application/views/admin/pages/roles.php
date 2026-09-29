<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style>

    .table > td {
        white-space: nowrap;
    }


    .popover-content{
        padding: 0px !important;
        display: inline-block !important;
        height: auto !important;
    }

    ul.roles-pop{
        padding: 0px 0px !important;
        margin: 0px 0px !important;
    }
    ul.roles-pop li{
        padding: 2px 2px !important;
        margin: 0px 0px !important;
        list-style: none !important;
        display: inline-block;
        width: 100%;
        position: relative;
    }

    ul.roles-pop li > span{
        position: relative;
        display: inline-block !important;
        width: 100%;
        line-height: 10px;
    }
    ul.roles-pop li > em.small-text{
        font-size: 9px !important;
    }
    ul.roles-pop li:last-child > span{
        border-bottom: none !important;
    }
    ul.roles-pop li > .btn {
        position: absolute;
        right: 5px;
        margin: 0px 0px !important;
        height: 100% !important;
        width: 20px;
        padding: 2px 4px !important;
        color: #FFA3A4;
        background: transparent;
    }

    ul.roles-pop .label.roles .fa {
        margin: 0px 0px !important;
        position: absolute;
        top: 2px;
        left: 5px;
    }

    .label.roles > .name {
        position: relative;
        display: inline-block;
    }

</style>



<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">
        <div class="page-bar"> <?php echo create_breadcrumb(); ?></div>
        <!-- END PAGE HEADER-->

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light table">
                    <div class="portlet-title">
                        <div class="caption">
                            <h3 class="text-success bold"><i class="fa fa-unlock"></i> Roles Management</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light table">
                    <div class="portlet-title">
                        <div class="caption">
                            <h4 class="text-success bold"><i class="fa fa-user-secret"></i> Roles</h4>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="" style="position:relative">
                            <div class="row">
                                <div class="col-md-11">
                                    <table width="100%" class="table table-condensed tbl-sm" id="roles_list">
                                        <thead>
                                            <th></th>
                                            <th>CODE</th>
                                            <th>DESCRIPTION</th>
                                            <th>COLOR</th>
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
            <div class="col-md-6">
                <div class="portlet light table">
                    <div class="portlet-title">
                        <div class="caption">
                            <h4 class="text-success bold"><i class="fa fa-toggle-on"></i> Access</h4>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="" style="position:relative">
                            <div class="row">
                                <div class="col-md-12">
                                    <table width="100%" class="table table-condensed" id="nav_list">
                                        <thead>
                                        <th>Ctrl</th>
                                        <th>Module ID</th>
                                        <th>CODE</th>
                                        <th>NAME</th>
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

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/global/scripts/datatable.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/maintenance.js" ></script>
<script>
    MAIN.roles();
</script>