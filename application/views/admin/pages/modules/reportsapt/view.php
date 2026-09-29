<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>

<link rel='stylesheet'  href='<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/style.css' type='text/css' media='all'/>
<style>
    #chart_aging {
        width: 100%;
        height: 500px;
    }
</style>
<div class="tabbable-line">
    <ul class="nav nav-tabs ">
        <li class="active">
            <a href="#tab_summary" data-toggle="tab"> <i class="fa fa-pie-chart"></i> Summary </a>
        </li>
        <li>
            <a href="#tab_details" data-toggle="tab">  <i class="fa fa-bar-chart-o"></i> Details </a>
        </li>
        <li>
            <a href="#tab_aging" data-toggle="tab">  <i class="fa fa-line-chart"></i> Aging </a>
        </li>
        <li class="pull-right"><h4>APT Reports</h4></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab_summary">
            <div class="row">
                <div class="col-md-3">
                    <h4>Filter</h4>
                    <div class="form-group">
                        <label>Date From</label>
                        <input class="form-control" type="date" placeholder="From" />
                    </div>
                    <div class="form-group">
                        <label>Date To</label>
                        <input class="form-control" type="date" placeholder="From" />
                    </div>
                    <div class="form-group">
                        <button type="reset" class="btn btn-default pull-right"><i class="fa fa-search"></i> Go</button>
                        <button type="submit" class="btn btn-default pull-right"><i class="fa fa-refresh"></i> Reset</button>
                    </div>
                </div>
                <div class="col-md-9">

                    <h4>Result</h4>
                    <table class="table table-striped table-hover table-bordered" id="tbl_summary_res">
                        <thead>
                        <th>Types</th>
                        <th>Accomplished</th>
                        <th>Unaccomplished</th>
                        <th>Total</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div class="col-md-6 pull-right">
                        <ul class="list-group summary column no-border">
                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Total Accomplished</span>
                                <span class="col-md-6 label-default number">0</span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Total Unaccomplished</span>
                                <span class="col-md-6 label-default number">0</span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Grand Total</span>
                                <span class="col-md-6 label-default number">0</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="tab_details">
            <!-- START PAGE CONTENT-->
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bar-chart-o"></i>
                        <span class="caption-subject font-green-sharp bold">Application</span>
                        <span class="caption-helper " id="graph_message">statistics</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse">
                        </a>
                        <a href="#portlet-config" data-toggle="modal" class="config">
                        </a>
                        <a href="javascript:;" class="reload">
                        </a>
                        <a href="javascript:;" class="remove">
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="chart " id="app_chart" style="height: 450px;"></div>
                </div>
            </div>



            <div class="portlet box grey">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>
                        <span class="caption-subject font-green-sharp bold">Applications</span>
                        <span class="caption-helper " id="">pending transactions</span>
                    </div>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-hover tbl-sm" id="tbl_app_list">
                        <thead>
                        <tr>
                            <th><i class="fa fa-navicon"></i> </th>
                            <th>Name</th>
                            <th class="">Address</th>
                            <th class="">Pending to</th>
                            <th class="">Date Created</th>
                            <th class="">Last Update</th>
                            <th class="">Requirements Status</th>
                            <th class="">Status</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="tab-pane fade in" id="tab_aging">
            <div class="row">
            <div class="col-md-2">
                <h4>Filter</h4>
                <div class="form-group">
                    <label>Year</label>
                    <input class="form-control" type="text" placeholder="Year" />
                </div>
                <div class="form-group">
                    <button type="reset" class="btn btn-default pull-right"><i class="fa fa-search"></i> Go</button>
                    <button type="submit" class="btn btn-default pull-right"><i class="fa fa-refresh"></i> Reset</button>
                </div>
            </div>
            <div class="col-md-10">
            <div id="chart_aging"></div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amcharts.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/serial.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amstock.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.css" type="text/css" media="all" />
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/themes/light.js"></script>

<script src="<?php echo base_url(); ?>assets/pages/reports/apt.js"></script>
<script>
    APT.applied();
</script>

