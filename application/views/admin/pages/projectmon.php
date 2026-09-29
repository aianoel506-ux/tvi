<style>
    .table td{
        word-wrap:break-word !important;
    }
    .table td {
        text-overflow:ellipsis;
        white-space: normal;
    }

    .amcharts-graph-g2 .amcharts-graph-stroke {
        stroke-dasharray: 3px 3px;
        stroke-linejoin: round;
        stroke-linecap: round;
        -webkit-animation: am-moving-dashes 1s linear infinite;
        animation: am-moving-dashes 1s linear infinite;
    }

    @-webkit-keyframes am-moving-dashes {
        100% {
            stroke-dashoffset: -31px;
        }
    }
    @keyframes am-moving-dashes {
        100% {
            stroke-dashoffset: -31px;
        }
    }


    .lastBullet {
        -webkit-animation: am-pulsating 1s ease-out infinite;
        animation: am-pulsating 1s ease-out infinite;
    }
    @-webkit-keyframes am-pulsating {
        0% {
            stroke-opacity: 1;
            stroke-width: 0px;
        }
        100% {
            stroke-opacity: 0;
            stroke-width: 50px;
        }
    }
    @keyframes am-pulsating {
        0% {
            stroke-opacity: 1;
            stroke-width: 0px;
        }
        100% {
            stroke-opacity: 0;
            stroke-width: 50px;
        }
    }

    .amcharts-graph-column-front {
        -webkit-transition: all .3s .3s ease-out;
        transition: all .3s .3s ease-out;
    }
    .amcharts-graph-column-front:hover {
        fill: #496375;
        stroke: #496375;
        -webkit-transition: all .3s ease-out;
        transition: all .3s ease-out;
    }

    .amcharts-graph-g3 {
        stroke-linejoin: round;
        stroke-linecap: round;
        stroke-dasharray: 500%;
        stroke-dasharray: 0 /;    /* fixes IE prob */
        stroke-dashoffset: 0 /;   /* fixes IE prob */
        -webkit-animation: am-draw 10s;
        animation: am-draw 10s;
    }
    @-webkit-keyframes am-draw {
        0% {
            stroke-dashoffset: 500%;
        }
        100% {
            stroke-dashoffset: 0%;
        }
    }
    @keyframes am-draw {
        0% {
            stroke-dashoffset: 500%;
        }
        100% {
            stroke-dashoffset: 0%;
        }
    }
    .amChartsPeriodSelector .amChartsButton {
        padding-top: 5px;
        padding-bottom: 3px;
        -moz-border-radius: 0;
        border-radius: 0;
        border: 0;
        border-bottom: 1px solid #dddddd;
        outline: none;
        background: #fff;
        color: #000;
    }

    .amChartsPeriodSelector .amChartsButton:hover {
        background-color: #eeeeee;
    }

    .amChartsPeriodSelector .amChartsButtonSelected {
        background-color: #fff;
        border: 0;
        border-bottom: 1px solid #0088CC;
        color: #000000;
        padding-bottom: 3px;
        -moz-border-radius: 0;
        border-radius: 0;
        margin: 1px;
        outline: none;
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
</style>


<?php
$changes = $xdata['changes'];
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/style.css">
<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">


        <h3 class="page-title">
            Admin <small>System Monitoring</small>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-gear"></i>
                    <a href="">Settings</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#">Project Monitor</a>
                </li>
            </ul>

        </div>
        <div class="row">
            <!-- END PAGE HEADER-->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="caption-subject font-green-sharp bold">Development Graph</span><br>
                                    <span class="caption-helper " id="graph_message"></span>
                                </div>
                                <div class="tools">
                                    <button class="btn btn-default btn-xs" id="get_git_data"><i class="fa fa-download"></i> Get GIT Data</button>
                                    <button class="btn btn-default btn-xs" id="ref_git_data"><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="chart " id="commit_graph" style="height: 450px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="caption-subject font-green-sharp bold">Developers Activities</span><br>
                                    <span class="caption-helper " id="graph_message"></span>
                                </div>
                                <div class="tools">
                                    <button class="btn btn-default btn-xs" id="get_git_data"><i class="fa fa-download"></i> Get GIT Data</button>
                                    <button class="btn btn-default btn-xs" id="ref_git_data"><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="chart " id="dev_graph" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="caption-subject font-green-sharp bold">Developers Summary</span><br>
                                    <span class="caption-helper " id="graph_message"></span>
                                </div>
                                <div class="tools">
                                    <button class="btn btn-default btn-xs" id="get_git_data"><i class="fa fa-download"></i> Get GIT Data</button>
                                    <button class="btn btn-default btn-xs" id="ref_git_data"><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="chart " id="dev_graph_summ" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                


                <div class="row">
                    <div class="col-md-12 margin-top-10">                
                        <div class="portlet light table">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-edit"></i>
                                    <span class="caption-subject font-green-sharp bold uppercase">Responsibility</span><br>
                                    <span class="caption-helper " id="responsibility_message"></span>
                                </div>
                                <div class="tools">
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-hover table-striped table-condensed table-bordered" id="tbl_responsibility" >
                                    <thead>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Files</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 margin-top-10"> 
                        <code><b>Note: </b>This is for subversioning monitoring, update of data based on daily.</code>
                    </div>
                </div>



            </div>



        </div>


    </div>

</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/input-mask/jquery.inputmask.bundle.js"></script>

<!--
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>

-->

<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amcharts.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/serial.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amstock.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/pie.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.css" type="text/css" media="all" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/themes/black.js" type="text/javascript"></script>




<script src="<?php echo base_url(); ?>assets/pages/settings/main.js"></script>


<script type="text/javascript">
    SETTINGS.initprojectmon();
</script>