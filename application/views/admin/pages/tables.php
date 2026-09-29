<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">
        <div class="page-bar">
            <?php echo create_breadcrumb(); ?>
            <div class="page-toolbar">
                <button class="btn btn-default inline"><i class="fa fa-refresh"></i></button>
            </div>
        </div>
        <div id="parameter_pages" class="tab-content">
            <div class="tab-pane active fade in" id="parameters">
                <div class="row">
                    <div class="col-md-6">
                        <div class="portlet light portlet-fit portlet-form bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" icon-layers font-green"></i>
                                    <span class="caption-subject font-green sbold uppercase">Validation States</span>
                                </div>
                                <div class="actions">
                                    <a class="btn btn-circle btn-default" href="javascript:;" id="btn_add_field">
                                        <i class="icon-plus"></i> Add Field
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <form action="#" class="form-horizontal">
                                    <div class="form-body">
                                        <table class="table table-bordered table-hover" id="fields">
                                            <thead>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Length</th>
                                            <th>Decimals</th>
                                            <th>Not Null</th>
                                            <th>Key</th>
                                            <th>Comment</th>
                                            <th>Del</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input class="form-control input-sm inline" /></td>
                                                    <td><input class="form-control input-sm inline" /></td>
                                                    <td><input class="form-control input-sm inline" /></td>
                                                    <td><input class="form-control input-sm inline" /></td>
                                                    <td><input type="checkbox" class="form-control" /></td>
                                                    <td><input type="checkbox" class="form-control" /></td>
                                                    <td><input class="form-control input-sm inline" /></td>
                                                    <td>
                                                        <a href="javascript:;" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <a href="javascript:;" class="btn green">Submit</a>
                                                <a href="javascript:;" class="btn default">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- END FORM-->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="<?php echo base_url(); ?>assets/pages/maintenance.js"></script>
<script>

    MAIN.tables();
</script>