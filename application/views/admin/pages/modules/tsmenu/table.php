
<link rel='stylesheet'  href='<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/style.css' type='text/css' media='all'/>
<style>
    #districtpie {
        width: 100%;
        height: 500px;
        font-size: 11px;
    }
    #chartbarangay {
        width: 100%;
        height: 700px;
        font-size: 11px;
    }
    #btn_filters button {
        margin-bottom: 5px;
    }
    #btn_filters button.active {
        border-bottom: 5px solid #000;
        margin-bottom: 0px;
    }

    .form-control[type=date] {
        padding: 0px 5px !important;
    }

</style>
<div class="row">
    <div class="col-md-12">
        <div class=" tab-content">


            <div class="tabbable-line">
                <ul class="nav nav-tabs pull-right">
                    <li class="active"><a href="#list" data-toggle="tab"><i class="fa fa-table fa-fw"></i> List</a></li>
                    <li class=""><a href="#accomp" data-toggle="tab"><i class="fa fa-table fa-fw"></i> Accomplishments</a></li>
                    <li class=""><a href="#summary" data-toggle="tab"><i class="fa fa-bar-chart-o fa-fw"></i> Summary</a></li>
                </ul>
            </div>
            <div class="tab-pane fade in" id="accomp">

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">

                            <div class="portlet-title">

                                <div class="col-md-6 pull-left">
                                <form target="_blank" action="<?php echo base_url('ts/gettcaverageexcel'); ?>" method="post">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Export</span>
                                            <input type="date" class="form-control datepicker" style="width: 30%;" name="datefrom" />
                                            <input type="date" class="form-control datepicker" style="width: 30%;" name="dateto" value="<?php echo date('Y-m-d'); ?>"/>
                                            <input type="text" class="form-control" style="width: 40%;" name="status" id="select2_export_status" />
                                            <span class="input-group-btn">
                                                <button class="btn green-haze" type="submit"><i class="fa fa-file-excel-o"></i> Save</button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                            <div class="portlet-body">

                                <table class="table table-hover table-condensed table-bordered tbl-sm" id="tbl_accomp_tc_list">
                                    <thead>
                                    <th>TCNO</th>
                                    <th>NAME</th>
                                    <th>CRATED</th>
                                    <th>UPDATED</th>
                                    <th>(SECS)</th>
                                    <th>(MINS)</th>
                                    <th>(HOURS)</th>
                                    <th>EQUIPMENT</th>
                                    <th>FINDINGS</th>
                                    <th>CIRCUIT</th>
                                    <th>ACTION</th>
                                    <th>SHIFT</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="tab-pane fade in active" id="list">
                <span class="btn-group" id="btn_filters" style="margin-top: 5px;">
                    <button type="button" data-id="0" class="btn btn-default btn-sm"><i class="fa fa-tag fa-fw"></i> All</button>
                    <button type="button" data-id="1" class="btn btn-info btn-sm"><i class="fa fa-tag fa-fw"></i> TS</button>
                    <?php
                    $query_status = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg, tp.colortxt')
                        ->from('prime_types_parameter AS tp')
                        ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
                        ->where('tssm.codes', 'TS')
                        ->get();
                    if($query_status->num_rows()>0) {
                        foreach ($query_status->result() as $srow) {
                            if (!ts_status_pending($srow->sysid) || $srow->sysid==300) {
                                echo '<button ';
                                $class = '';
                                if($srow->sysid==300) {
                                    $class = ' active';
                                }
                                echo ' type="button" data-id="' . $srow->sysid . '" class="btn btn-sm '.$class.'" style="background: ' . $srow->colorbg . '; color: ' . $srow->colortxt . '"><i class="fa fa-tag fa-fw"></i> ' . $srow->desc . '</button>';
                            }
                        }
                    }
                    ?>
                </span>


                <div class="portlet light bordered">
                    <div class="portlet-title">

                        <div class="row">
                            <div class="col-md-2">
                                <ul class="list-group summary column no-border list-group-sm">
                                    <li class="list-group-item">
                                        <span class="col-md-6 label-name">Trouble Call</span>
                                        <span class="col-md-6 label-default number" id="trouble_call_count">0</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <ul class="list-group summary column no-border list-group-sm">
                                    <li class="list-group-item">
                                        <span class="col-md-6 label-name">Follow-up</span>
                                        <span class="col-md-6 label-default number" id="followupcnt">0</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <ul class="list-group summary column no-border list-group-sm">
                                    <li class="list-group-item">
                                        <span class="col-md-6 label-name">General Average</span>
                                        <span class="col-md-6 label-default number" id="general_average">0</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <ul class="list-group summary column no-border list-group-sm">
                                    <li class="list-group-item">
                                        <span class="col-md-6 label-name">Shift 1 Average<br><small class="text-success">11 PM - 7 AM</small></span>
                                        <span class="col-md-6 label-default number" id="shift1_average">0</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <ul class="list-group summary column no-border list-group-sm">
                                    <li class="list-group-item">
                                        <span class="col-md-6 label-name">Shift 2 Average<br><small class="text-success">7 AM - 3 PM</small></span>
                                        <span class="col-md-6 label-default number" id="shift2_average">0</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <ul class="list-group summary column no-border list-group-sm">
                                    <li class="list-group-item">
                                        <span class="col-md-6 label-name">Shift 3 Average<br><small class="text-success">3 PM - 11 PM</small></span>
                                        <span class="col-md-6 label-default number" id="shift3_average">0</span>
                                    </li>
                                </ul>
                            </div>
                        </div>                    </div>
                    <div class="portlet-body">


                        <div class="col-md-9">
                            <div class="input-group pull-left" style="margin-left: -15px;">

                                    <span class="input-group-addon">
                                    List Limit
                                    </span>
                                <input id="list_limit" class="form-control " value="50" placeholder="Limit View.." />
                                <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" id="btn_list_limit">Go</button>
                                    </span>

                                <span class="input-group-addon">
                                    Fast Search
                                    </span>
                                <input style="width: 50%; display: inline-block;" id="search_name" class="form-control search-submit" value="" placeholder="Name" />
                                <input style="width: 50%; display: inline-block;" id="search_addr" class="form-control search-submit" value="" placeholder="Address" />
                                <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" id="btn_search"><i class="fa fa-search"></i></button>
                                     <button type="button" class="btn btn-default" id="btn_refresh_list"><i class="fa fa-refresh"></i> Refresh Table</button>
                                   </span>

                                <span class="input-group-addon" style="background: #fff;">
                                    <label for="icheckdatefilter" style="margin: 0px 0px;">
                                    <input class="checkbox icheck" id="icheckdatefilter"  type="checkbox" value="1" />
                                        <i class="fa fa-calendar"></i> Filter
                                    </label>
                                </span>
                                <input class="form-control filter-dates disabled-submit" id="filteryear" style="width: 34%;" placeholder="Year" value="<?php echo date('Y'); ?>" />
                                <input class="form-control filter-dates disabled-submit" id="filtermonth" style="width: 33%;" placeholder="Month" />
                                <input class="form-control filter-dates disabled-submit" id="filterday" style="width: 33%;" placeholder="Day" />

                            </div>
                        </div>
                        <table class="table table-hover table-condensed table-striped table-bordered tbl-sm table-resizable table-wrap" id="tbl_ticket_list" style="width: 100%">
                            <thead>
                            <th></th>
                            <th><i class="fa fa-reorder"></i></th>
                            <th>Q</th>
                            <th>TC No.</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Time Lapsed</th>
                            <th>Reports</th>
                            <th>Team</th>
                            <th>Equipment</th>
                            <th>Findings</th>
                            <th>Circuit Lvl</th>
                            <th>ETC</th>
                            <th>Status</th>
                            <th></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>


                </div>
            </div>

            <div class="tab-pane fade in" id="summary">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-anchor font-green-sharp"></i>
                                    <span class="caption-subject font-green-sharp bold uppercase" id="">Status
                                        Month of <?php echo date("F"); ?>
                            </span>
                                </div>
                                <div class="actions">
                                    <button type="button" id="btn_refresh_chart_status" class="btn btn-default btn-sm" id=""><i class="fa fa-refresh"></i> Refresh Charts</button>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <h3><i class="fa fa-calendar font-green-haze"></i> Filter Date</h3>
                                        <div class="form-group">
                                            <label>Date From</label>
                                            <input class="form-control" type="date" value="<?php echo date("Y-m").'-01'; ?>" id="input_satus_from" />
                                        </div>
                                        <div class="form-group">
                                            <label>Date End</label>
                                            <input class="form-control" type="date" value="<?php echo date("Y-m-t");?>"  id="input_satus_to" />
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-default pull-right" id="btn_get_status">Get</button>
                                        </div>

                                    </div>
                                    <div class="col-md-10">
                                        <div id="statuspie" style="width: 100%; height: 550px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-anchor font-green-sharp"></i>
                                    <span class="caption-subject font-green-sharp bold uppercase" id="">
                               District Stats
                            </span>
                                </div>
                                <div class="actions">
                                    <button type="button" class="btn btn-default btn-sm" id="btn_refresh_chart_dist"><i class="fa fa-refresh"></i> Refresh Charts</button>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="districtpie"></div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-anchor font-green-sharp"></i>
                                    <span class="caption-subject font-green-sharp bold uppercase" id="">
                               Barangay Clusters
                            </span>
                                </div>
                                <div class="actions">
                                    <button type="button" class="btn btn-default btn-sm" id="btn_refresh_chart_barangay"><i class="fa fa-refresh"></i> Refresh Charts</button>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-12 tabbable-line">
                                        <h4 class="pull-left"><i class="fa fa-search"></i> Filter by District</h4>
                                        <ul class="nav nav-tabs pull-right" id="filter_barangay_dist">
                                            <li class="active"><a href="#" data-toggle="tab">All Top 50</a></li>
                                            <?php

                                            $qry_dist = $this->db->select('sysid, names')
                                                ->from('address_districts')
                                                ->where('types', 1)
                                                ->get();
                                            if($qry_dist->num_rows() > 0) {
                                                foreach ($qry_dist->result() as $row) {
                                                    echo '<li>';
                                                    echo '<a data-id="'.$row->sysid.'" href="#' . strtolower($row->names) . '" data-toggle="tab">' . $row->names . '</a>';
                                                    echo '</li>';
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div id="chartbarangay"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amcharts.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/serial.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/amstock.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/pie.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/plugins/export/export.css" type="text/css" media="all" />
<script src="<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/themes/light.js"></script>

<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.list();
    TS.summary();
</script>

