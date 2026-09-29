<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.dataTables.min.css ">
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
        <div class="tabbable-line">
            <ul class="nav nav-tabs ">
                <li class="active">
                    <a href="#users" data-toggle="tab" aria-expanded="false"> Users List </a>
                </li>
                <li class="">
                    <a href="#usersgroup" data-toggle="tab" aria-expanded="true"> Users Group </a>
                </li>
                <li class="">
                    <a href="#usersupload" data-toggle="tab" aria-expanded="true"> <i class="fa fa-cloud-upload"></i> Upload Data</a>
                </li>
            </ul>
            <div class="tab-content" style="padding: 0px 0px;">
                <div class="tab-pane active" id="users">
                    <!-- BEGIN PAGE CONTENT-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light table">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <a href="#form_add_users" title="Add User" class="btn btn-primary" data-toggle="ajax-modal"><i class="fa fa-plus"></i> Add User</a>
                                    </div>
                                    <div class="actions tabbable-line">
                                        <ul class="nav nav-tabs" id="user_stat">
                                            <li class="">
                                                <a href="#" data-id="2" data-toggle="tab">All</a>
                                            </li>
                                            <li class="active">
                                                <a href="#" data-id="1"  data-toggle="tab">Active</a>
                                            </li>
                                            <li>
                                                <a href="#" data-id="0" data-toggle="tab">In-Active</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="" style="position:relative">
                                        <table class="table table-hover table-condensed table-bordered" id="users_list">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User ID</th>
                                                <th>Legacy Uname</th>
                                                <th>Firstname</th>
                                                <th>Roles Description</th>
                                                <th>Last Active</th>
                                                <th>Date Created</th>
                                                <th>Date Updated</th>
                                                <th>Status</th>
                                                <th>Control</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>

                                        <a href="#" title="Add User" class="btn btn-primary"  id="enable"><i class="fa fa-plus"></i> Enable</a>
                                        <a href="#" title="Add User" class="btn btn-primary"  id="disable"><i class="fa fa-plus"></i> Disable</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade in" id="usersgroup">
                    <table class="table table-bordered table-condensed table-hover tbl-sm" id="usersgrouptable">
                        <thead>
                        <th></th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="tab-pane fade in" id="usersupload">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <h4>Upload From File</h4>
                                    </div>
                                </div>
                                <div class="portlet-body">

                                    <div class="form-group form-md-line-input" style="margin-top: -20px">
                                        <label>Roles: </label>
                                        <input id="select2rolesupload" name="roleid" type="text" class="form-control  input-sm " placeholder="Roles">
                                    </div>
                                    <div class="form-group">
                                        <input id="datafile" name="datafile" class="file" type="file" data-preview-file-type="any" data-upload-url="<?php echo base_url('user/importusers');?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <h4>Uploaded Data</h4>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-hover table-bordered table-striped" id="tbl_users_uploads">
                                        <thead>
                                        <th>User ID</th>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Password</th>
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



<script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/global/scripts/datatable.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/access.js" ></script>
<script>
    ACCESS.init();
</script>