<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/style.css">

<style>
    .table td{
        word-wrap:break-word !important;
    }
    .table td {
        text-overflow:ellipsis;
        white-space: normal;
    }

    .amcharts-pie-slice {
        transform: scale(1);
        transform-origin: 50% 50%;
        transition-duration: 0.3s;
        transition: all .3s ease-out;
        -webkit-transition: all .3s ease-out;
        -moz-transition: all .3s ease-out;
        -o-transition: all .3s ease-out;
        cursor: pointer;
        box-shadow: 0 0 30px 0 #000;
    }

    .amcharts-pie-slice:hover {
        transform: scale(1.1);
        filter: url(#shadow);
    }

    .cost-center .portlet-body{
        width:100% !important;
        display: table !important;
    }

    .cost-center .portlet-body .btn-group{
        display: table-row !important;
    }

    .cost-center .portlet-body .btn-group button{
        display: table-cell  !important;
        text-align: center !important;
        min-width: 33.33% !important;
        border: none;
    }

    .cost-center .portlet-body .btn-group button:hover {
        border: none !important;
    }

</style>
 

        <h3 class="page-title">
            <i class="fa <?php echo $pageicon; ?> fa-fw text-<?php echo $pageclass; ?>"></i><span class="text-<?php echo $pageclass; ?>"><?php echo $pagetitle; ?></span><small> view</small>
        </h3>

		<!-- START PAGE CONTENT-->
        <div class="row">
            <div class="col-md-2">
                <div class="portlet light cost-center">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-building"></i>
                            <span class="caption-subject font-green-sharp bold">Cost Center</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm tooltips" title="Display All" data-placement="right">All</button>
                            <?php
                            $qry_cc = $this->db->select()->from('prime_costcenter_main')->get();
                            if($qry_cc->num_rows()>0) {
                                foreach($qry_cc->result() as $row) {
                                    echo '<button class="btn btn-default btn-sm tooltips" title="'.$row->desc.'" data-placement="right">'.$row->codes.'</button>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-bar-chart-o"></i>
                            <span class="caption-subject font-green-sharp bold">Budget</span>
                            <span class="caption-helper " id="graph_message">projection</span>
                        </div>
                        <div class="tools">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="chart " id="budget_projection" style="height: 450px;"></div>
                    </div>
                </div>

                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-bar-chart-o"></i>
                            <span class="caption-subject font-green-sharp bold">Cost Center</span>
                            <span class="caption-helper " id="graph_message">statistics</span>
                        </div>
                        <div class="tools">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="chart " id="cost_center_stats" style="height: 350px;"></div>
                    </div>
                </div>

                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-bar-chart-o"></i>
                            <span class="caption-subject font-green-sharp bold">Cost Center</span>
                            <span class="caption-helper " id="graph_message">statistics</span>
                        </div>
                        <div class="tools">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="chart " id="cc_pie_chart" style="height: 400px;"></div>
                    </div>
                </div>

            </div>
        
        
        <!-- END PAGE CONTENT-->
    </div> 





<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amcharts.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/serial.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amstock.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/pie.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.css" type="text/css" media="all" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/themes/black.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/animate/animate.min.js"></script>

<script src="<?php echo base_url(); ?>assets/pages/reports/budget.js"></script>


<script type="text/javascript">
    BUDGET.projection();
</script>