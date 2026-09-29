<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/admin/layout/css/activities.css" rel="stylesheet" type="text/css" />


<style>
    .row-flow-prime a {
        text-decoration: none;
    }
    .row-flow-prime em {
        font-size: 10px;
        padding-bottom: 10px;
    }
    .panel-group {
        padding-bottom: 5px !important;
        margin-bottom: 5px !important;
    }
    .tiles .tile:last-child{
        width: auto !important;	
    }

    .tiles {
        display: flex !important;
        flex-wrap: wrap !important;
    }

    .tiles .tile {
        flex-grow: 1;
        min-width: 10%;
    }

    .tiles .tile {
        position: relative;	
    }
    .tiles .tile .fa-bg {
        font-size: 200px !important;
        position: absolute;
        bottom: -20px;
        color: #fff;
        opacity: 0.2;
        -moz-opacity: 0.2;
        -webkit-opacity: 0.2;
        margin: none !important;
        height: 100%;
    }
</style>
<div class="row">
    <div class="col-md-12">




        <div class="portlet light active table" id="">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-edit"></i> <span class="caption-subject font-green-sharp bold uppercase">Activities</span> <span class="caption-helper">list of your activities involved</span> </div>
                <div class="tools">
                    <div class="row">
                        <label class="input-label col-md-3">Filter:</label>
                        <div class="col-md-9">
                            <input class="form-control select2" id="activity_filter" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet-body">

                <div class="row">

                    <div class="col-md-12">
                        <table class="table table-hover table-striped table-condensed table-bordered" id="trn-list">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><i class="fa fa-reorder"></i></th>
                                    <th width="20%">Last Update</th>
                                    <th width="25%">Data</th>
                                    <th width="40%">Transaction</th>
                                    <th width="80px"><i class="fa fa-gear"></i> View</th>
                                </tr>
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

<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/pages/activities.js" type="text/javascript"></script>
<script>
    ACTIVITIES.user();
</script> 
