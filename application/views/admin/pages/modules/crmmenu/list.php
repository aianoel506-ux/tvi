<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />

<link rel='stylesheet'  href='<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/FixedColumns/css/dataTables.fixedColumns.css' type='text/css' media='all'/>

<style>
    .dataTables_wrapper > .row .col-sm-6:last-child {
        width: 30% !important;
        float: right;
    }
    #btn_filters button {
        margin-bottom: 5px;
    }
    #btn_filters button.active {
        border-bottom: 5px solid #000;
        margin-bottom: 0px;
    }

    .table tbody tr td.tcno,
    .table tbody tr td.tcname,
    .table tbody tr td.tcaddress
    {
        cursor: pointer;
    }

    .table tbody tr {
        position: relative;
    }
    .table tbody tr.selected td:first-child:before {
        font-family: FontAwesome;
        content: '\f005';
        position: absolute;
        left: 0px;
        top: 30%;
        color: red;
    }

    .select2-selection--single {
        height: 100% !important;
    }
    .select2-selection__rendered,
    .select2-choice,
    .select2-choice .select2-chosen
    {
        word-wrap: break-word !important;
        text-overflow: inherit !important;
        white-space: normal !important;
    }
    li.select2-selection__choice,
    .select2-choice,
    .select2-choice .select2-chosen
    {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis; //use this if you want to shorten
    }
    ul.select2-selection__rendered {
        padding-right: 12px !important; //overrides select2 style
    }

    table tr.text-dager td * {
        color: #FF3F00 !important;
    }

    td.ticket-no {
        font-size: 13px;
        font-weight: bold;
        vertical-align: middle;
        text-align: center;
    }

</style>

<form id="frm_join_togroup" action="<?php echo base_url('ts/jointogroup'); ?>" method="post">
    <div class="row">
        <div class="col-md-12">

            <div class="portlet light bordered">
                <div class="portlet-title">

                    <div class="row">
                        <div class=" pull-left col-md-7">
                            <span class="btn-group" id="btn_filters" style="margin-top: 5px;">
                                <button type="button" data-id="0" class="btn btn-default btn-sm"><i class="fa fa-tag fa-fw"></i> All</button>
                                <button type="button" data-id="1" class="btn btn-info btn-sm"><i class="fa fa-tag fa-fw"></i> Field</button>
                                <?php
                                $query_status = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg, tp.colortxt')
                                    ->from('prime_types_parameter AS tp')
                                    ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
                                    ->where('tssm.codes', 'TS')
                                    ->get();
                                if($query_status->num_rows()>0) {
                                    foreach ($query_status->result() as $srow) {
                                        if ((!ts_status_pending($srow->sysid) || $srow->sysid==300) && $srow->sysid != 1025 && $srow->sysid != 1028) {
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

                        </div>

                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-addon" style="background: #fff;">
                                    <label for="icheckdatefilter" style="margin: 0px 0px;">
                                    <input class="icheck" id="icheckdatefilter" type="checkbox" value="1" />
                                        <i class="fa fa-calendar"></i> Filter
                                    </label>
                                </span>
                                <input class="form-control filter-dates disabled-submit" id="filteryear" style="width: 34%;" placeholder="Year" value="<?php echo date('Y'); ?>" />
                                <input class="form-control filter-dates disabled-submit" id="filtermonth" style="width: 33%;" placeholder="Month" />
                                <input class="form-control filter-dates disabled-submit" id="filterday" style="width: 33%;" placeholder="Day" />

                            </div>
                        </div>
                    </div>
                    <hr style="margin:4px 0px;">
                    <div class="row">
                        <div class="col-md-2">
                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="col-md-6 label-name">Inquiries</span>
                                    <span class="col-md-6 label-default number" id="trouble_call_count">0</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-2">
                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="col-md-6 label-name">Applications</span>
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
                                    <span class="col-md-6 label-name">Team 1 Average<br><small class="text-success">11 PM - 7 AM</small></span>
                                    <span class="col-md-6 label-default number" id="shift1_average">0</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-2">
                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="col-md-6 label-name">Team 2 Average<br><small class="text-success">7 AM - 3 PM</small></span>
                                    <span class="col-md-6 label-default number" id="shift2_average">0</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-2">
                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="col-md-6 label-name">Team 3 Average<br><small class="text-success">3 PM - 11 PM</small></span>
                                    <span class="col-md-6 label-default number" id="shift3_average">0</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">

                        <div class="col-md-7 ">
                            <div class="input-group pull-left" style="margin-left: -15px;">
                                    <span class="input-group-btn">
                                    <button type="button" id="btn_select_all_tickets" class="btn btn-default btn-sm"><i class="fa fa-square-o"></i> Select All</button>
                                    </span>

                                <span class="input-group-addon">
                                    List Limit
                                    </span>
                                <input id="list_limit" class="form-control input-sm" value="50" placeholder="Limit View.." />
                                <span class="input-group-btn">
                                        <button class="btn btn-primary btn-sm" type="button" id="btn_list_limit">Go</button>
                                    </span>

                                <span class="input-group-addon">
                                    Fast Search
                                    </span>
                                <input style="width: 50%; display: inline-block;" id="search_name" class="form-control input-sm search-submit" value="" placeholder="Name" />
                                <input style="width: 50%; display: inline-block;" id="search_addr" class="form-control input-sm search-submit" value="" placeholder="Address" />
                                <span class="input-group-btn">
                                        <button class="btn btn-primary btn-sm" type="button" id="btn_search"><i class="fa fa-search"></i></button>
                                     <button type="button" class="btn btn-default btn-sm" id="btn_refresh_list"><i class="fa fa-refresh"></i> Refresh Table</button>
                                   </span>
                            </div>
                        </div>
                        <table width="100%" class="table table-hover table-condensed table-striped table-bordered tbl-sm table-resizable table-wrap" id="tbl_ticket_list" style="width: 100%">
                            <thead>
                            <th></th>
                            <th>Ticket#</th>
                            <th>Transaction</th>
                            <th>Information</th>
                            <th>Address</th>
                            <th>Time Lapsed</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="">
        <div class="row">
            <div class="col-md-5">
            </div>
            <div class="col-md-3">
            </div>
            <div class="col-md-4 pull-right">
                <div class="input-group">
                    <span class="input-group-btn">
                        <a id="btn_team_members" data-title="Team Members" data-content="" data-placement="right" data-toggle="popovers" type="button" class="btn btn-default"><i class="fa fa-search"></i></a>
                    </span>
                    <input type="text" class="form-control" name="groupid" id="select_group" placeholder=""  style="text-transform: uppercase">
                    <span class="input-group-btn">
                         <button type="submit" class="btn btn-default" id="btn_assign_team"><i class="fa fa-tag"></i> Join</button>
                    </span>
                </div>
            </div>
        </div>

    </div>
    <hr>
</form>

<div id="ps_overlay" class="ps_overlay" style="display:none;"></div>
<a id="ps_close" class="ps_close" style="display:none;"></a>
<div id="ps_container" class="ps_container" style="display:none;">
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js "></script>

<script src="<?php echo base_url(); ?>assets/pages/crmmenu/crm.js"></script>

<script>
    CRM.init(1);
</script>
