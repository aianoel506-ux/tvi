<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 3/13/2018
 * Time: 2:06 PM
 */

?>
<div class="animated fadeInUp fast">
    <div class="row widget-row margin-top-30">
        <div class="col-md-3">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">Today's Sales</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-green icon-bulb"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">PhP</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="0">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        <div class="col-md-3">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">Weekly Sales</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-red icon-layers"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">PhP</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="0">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        <div class="col-md-3">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">This Month Sales</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-purple icon-screen-desktop"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">PHP</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="0">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        <div class="col-md-3">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">This Year Sales</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-blue icon-bar-chart"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">PhP</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="0">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption ">
                        <span class="caption-subject font-dark bold uppercase">Collection</span>
                        <span class="caption-helper">weekly..</span>
                    </div>
                    <div class="actions">
                        <a href="#" class="btn btn-circle green btn-outline btn-sm">
                            <i class="fa fa-pencil"></i> Export </a>
                        <a href="#" class="btn btn-circle green btn-outline btn-sm">
                            <i class="fa fa-print"></i> Print </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="dashboard_amchart_3" style="width: 100%; height: 150px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xs-12 col-sm-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-cursor font-dark hide"></i>
                        <span class="caption-subject font-dark bold uppercase">General Stats</span>
                    </div>
                    <div class="actions">
                        <a href="javascript:;" class="btn btn-sm btn-circle red easy-pie-chart-reload">
                            <i class="fa fa-repeat"></i> Reload </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="easy-pie-chart">
                                <div class="number transactions" data-percent="55">
                                    <span>+55</span>% <canvas height="75" width="75"></canvas></div>
                                <a class="title" href="javascript:;"> Transactions
                                    <i class="icon-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="margin-bottom-10 visible-sm"> </div>
                        <div class="col-md-4">
                            <div class="easy-pie-chart">
                                <div class="number visits" data-percent="85">
                                    <span>+85</span>% <canvas height="75" width="75"></canvas></div>
                                <a class="title" href="javascript:;"> New Visits
                                    <i class="icon-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="margin-bottom-10 visible-sm"> </div>
                        <div class="col-md-4">
                            <div class="easy-pie-chart">
                                <div class="number bounce" data-percent="46">
                                    <span>-46</span>% <canvas height="75" width="75"></canvas></div>
                                <a class="title" href="javascript:;"> Bounce
                                    <i class="icon-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xs-12 col-sm-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-equalizer font-dark hide"></i>
                        <span class="caption-subject font-dark bold uppercase">Server Stats</span>
                        <span class="caption-helper">monthly stats...</span>
                    </div>
                    <div class="tools">
                        <a href="" class="collapse" data-original-title="" title=""> </a>
                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                        <a href="" class="reload" data-original-title="" title=""> </a>
                        <a href="" class="remove" data-original-title="" title=""> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="sparkline-chart">
                                <div class="number" id="sparkline_bar5"><canvas style="display: inline-block; width: 113px; height: 55px; vertical-align: top;" width="113" height="55"></canvas></div>
                                <a class="title" href="javascript:;"> Network
                                    <i class="icon-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="margin-bottom-10 visible-sm"> </div>
                        <div class="col-md-4">
                            <div class="sparkline-chart">
                                <div class="number" id="sparkline_bar6"><canvas style="display: inline-block; width: 107px; height: 55px; vertical-align: top;" width="107" height="55"></canvas></div>
                                <a class="title" href="javascript:;"> CPU Load
                                    <i class="icon-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="margin-bottom-10 visible-sm"> </div>
                        <div class="col-md-4">
                            <div class="sparkline-chart">
                                <div class="number" id="sparkline_line"><canvas style="display: inline-block; width: 100px; height: 55px; vertical-align: top;" width="100" height="55"></canvas></div>
                                <a class="title" href="javascript:;"> Load Rate
                                    <i class="icon-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>