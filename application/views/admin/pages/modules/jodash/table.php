
<link rel='stylesheet'  href='<?php echo base_url(); ?>assets/global/plugins/amcharts_v3/style.css' type='text/css' media='all'/>
<style>

    #btn_filters button {
        margin-bottom: 5px;
    }
    #btn_filters button.active {
        border-bottom: 5px solid #000;
        margin-bottom: 0px;
    }

</style>
<div class="row">
    <div class="col-md-12">
        <div class="tabbable-line">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a href="#list" data-toggle="tab"><i class="fa fa-table fa-fw"></i> List</a></li>
                <li class=""><a href="#accomp" data-toggle="tab"><i class="fa fa-table fa-fw"></i> Accomplishments</a></li>
                <li class=""><a href="#summary" data-toggle="tab"><i class="fa fa-bar-chart-o fa-fw"></i> Summary</a></li>
            </ul>
        </div>


        <div class="tab-content">

            <div class="tab-pane fade in active" id="list">
                <span class="btn-group" id="btn_filters" style="margin-top: 5px;">
                    <button type="button" data-id="0" class="btn btn-default btn-sm"><i class="fa fa-tag fa-fw"></i> All</button>
                    <?php
                    $query_status = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg, tp.colortxt')
                        ->from('prime_types_parameter AS tp')
                        ->where('tp.codes = "JO" OR sysid = 322')
                        ->get();
                    if($query_status->num_rows()>0) {
                        foreach ($query_status->result() as $srow) {
                            if (!ts_status_pending($srow->sysid) || $srow->sysid==300) {
                                echo '<button ';
                                $class = '';
                                if($srow->sysid==300) {
                                    $class = ' active';
                                }
                                echo ' type="button" data-id="' . $srow->sysid . '" class="btn btn-sm '.$class.'" style="background: ' . $srow->colorbg . '; color: ' . $srow->colortxt . '"><i class="fa fa-tag fa-fw"></i> ' . $srow->names . '</button>';
                            }
                        }
                    }
                    ?>
                </span>


                <input style="width: 300px;" id="select2status" class="form-control" placeholder="Select Status"/>


                <div class="portlet light bordered">
                    <div class="portlet-body">


                        <div class="col-md-9" id="filters">
                            <div class="input-group pull-left" style="margin-left: -15px;">
                                <span class="input-group-addon">List Limit</span>
                                <input id="list_limit" class="form-control " value="50" placeholder="Limit View.." />
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" id="btn_list_limit">Go</button>
                                </span>
                                <span class="input-group-addon">Fast Search</span>
                                <input style="width: 50%; display: inline-block;" id="search_name" class="form-control search-submit" value="" placeholder="Name" />
                                <input style="width: 50%; display: inline-block;" id="search_addr" class="form-control search-submit" value="" placeholder="Address" />
                                <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" id="btn_search"><i class="fa fa-search"></i></button><button type="button" class="btn btn-default" id="btn_refresh_list"><i class="fa fa-refresh"></i> Refresh Table</button>
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
                        <table class="table table-hover table-condensed table-striped table-bordered tbl-sm" id="tbl_jo_list" style="width: 100%">
                            <thead>
                            <th></th>
                            <th>JO.#</th>
                            <th>JO.Type</th>
                            <th>Requested By</th>
                            <th>Account Details</th>
                            <th>Date Created</th>
                            <th>Date Updated</th>
                            <th>Transaction</th>
                            <th>Status</th>
                            <th>Control</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr>
                        <?php if(user_id() == 1) { ?>
                            <a href="jo/cleartrans" title="Clear Job Order Transactions and its logs?" class="btn btn-danger" id="btn_clear_trans"><i class="fa fa-times"></i> Clear Transactions</a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade in" id="accomp">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">

                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-check font-green-haze"></i> Accomplishment Table
                                </div>

                                <div class="tools">
                                    <div class="col-md-12 pull-right">
                                        <form target="_blank" action="<?php echo base_url('ts/gettcaverageexcel'); ?>" method="post">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Export</span>
                                                    <input type="date" class="form-control datepicker" style="width: 30%;" name="datefrom" />
                                                    <input type="date" class="form-control datepicker" style="width: 30%;" name="dateto" value="<?php echo date('Y-m-d'); ?>"/>
                                                    <input type="text" class="form-control" style="width: 40%;" name="status" id="select2_export_status" />
                                                    <span class="input-group-btn">
                                                <button class="btn green-haze" type="submit"><i class="fa fa-file-excel-o"></i> Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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

        </div>

    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/assets/main.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/jo/main.js"></script>

<script>
    JO.dashboard(1);
</script>

