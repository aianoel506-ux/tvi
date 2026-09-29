<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<style>
    .panel-collapse.table .table.sub{
        border-left: 1px solid rgba(0,0,0,0.10) !important;
        z-index: 1;
    }
</style>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet box tabbable">
                    <div class="portlet-title margin-top-20">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Budget Creation <?php echo user_session()->system_user_sessid; ?></span>
                            <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#76" data-toggle="tab" class="budgetDataToggle">
                                    CAPEX</a>
                            </li>
                            <li class="">
                                <a href="#77" data-toggle="tab" class="budgetDataToggle">
                                    OPEX</a>
                            </li>
                            <li class="">
                                <a href="#78" data-toggle="tab" class="budgetDataToggle">
                                    SP</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content budget-table">
                            <div class="row">
                                <div class="col-md-6 margin-top-10">
                                    <h4> <i class="fa fa-edit"></i> Data Entry

                                    </h4> <code> Note: Review all the details before saving</code>
                                </div>
                                <div class="col-md-6">
                                    <span class="btn-group pull-right margin-top-10">
                                        <button id="save_budget" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Send For Approval</button>
                                        <button id="clear_budget" class="btn btn-default"><i class="fa fa-refresh fa-fw"></i> Clear</button>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <button type="button" class="btn btn-primary pull-right" id="addNewTable" value="76"><i class="fa fa-plus fa-fw"></i>New CC Table</button>
                                        <select id="cc_id" class="form-control" style="width: 20%; display: inline-block;">
                                        </select>
                                        <input id="year" class="form-control" style="width: 20%; display: inline-block;" placeholder="Year" value="<?php echo date('Y', strtotime('+1 year')); ?>" />
                                        <input id="description" class="form-control" style="width: 40%; display: inline-block;" placeholder="Budget Description" />
                                        <input type="hidden" id="module_id" value="3"/>
                                    </h4> 
                                </div>
                            </div>
                            <div class="tab-pane fade in active">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default" id="budget_row_main">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <ul class="list-group summary">
                                                    <li class="list-group-item"> Costcenters: <span class="label label-default pull-right" id="daterange"><code>400</code><code>300</code><code>301</code></span> </li>
                                                    <li class="list-group-item"> Total Budget: <span class="label label-default pull-right" id="totalut">0</span> </li>
                                                    <li class="list-group-item"> Total Previous Budget: <span class="label label-default pull-right" id="totallate">0</span> </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-4">
                                                <ul class="list-group summary">
                                                    <li class="list-group-item"> Total Item(s) Budget: <span class="label label-default pull-right" id="daterange">3</span> </li>
                                                    <li class="list-group-item"> Costcenter Count: <span class="label label-default pull-right" id="totalut">0</span> </li>
                                                    <li class="list-group-item"> Total Budget Increase: <span class="label label-default pull-right" id="totallate">0</span> </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <p><strong>Confirm Deletion</strong></p>
            </div>
            <div class="modal-body">
                <p>Delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-ok"  data-dismiss="modal">Yiss!</button>
            </div>
        </div>
    </div>
</div>-->



<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<!--Additional Scripts for testing - Edrian-->
<!--<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.jeditable.mini.js"></script>--><!--The script below is for editable dataTable plugin-->

<script src="<?php echo base_url(); ?>assets/pages/bos/bos.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        var innerTable;
        var dt;
        BOS.capex();
    });
   
    //BOS.itemSelectTagging($('#itemname'), true, false);
    //} );
</script>
