
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

</style>
<div class="row">
    <div class="col-md-12">
        <div class=" tab-content">

            <div class="tab-pane fade in active" id="list">



                <div class="portlet light bordered table">
                    <div class="portlet-title">
                        <div class="caption">
                            <h3><i class="fa fa-book font-green-haze"></i> <b>Tech.</b> Logs</h3>
                        </div>
                        <div class="tools tabbable-line">
                            <ul class="nav nav-tabs" id="btn_filters" style="margin-top: 5px;">
                                <li class="active">
                                    <a data-id="0" data-toggle="tab"><span class="badge badge-info" id="log_total">0</span> All</a>
                                </li>
                                <?php
                                $query_status = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg, tp.colortxt')
                                    ->from('prime_types_parameter AS tp')
                                    ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
                                    ->where('tssm.codes', 'IT')
                                    ->get();
                                if($query_status->num_rows()>0) {
                                    foreach ($query_status->result() as $srow) {
                                        echo '
                                                <li>
                                                    <a data-id="' . $srow->sysid . '" data-toggle="tab"><span class="badge badge-info">0</span> ' . $srow->desc . '</a>
                                                </li>
                                            ';
                                    }
                                }
                                ?>
                            </ul>
                        </div>


                    </div>
                    <div class="portlet-body">

                        <div class="pull-left col-md-6">
                            <button type="button" class="btn btn-default btn-sm" id="btn_refresh_list"><i class="fa fa-refresh"></i> Refresh Table</button>
                        </div>
                        <table class="table table-hover table-condensed table-striped table-bordered tbl-sm table-resizable table-wrap" id="tbl_ticket_list" style="width: 100%">
                            <thead>
                            <th></th>
                            <th><i class="fa fa-reorder"></i></th>
                            <th>Ticket No.</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Time Lapsed</th>
                            <th>Reports</th>
                            <th>Remarks</th>
                            <th>Findings</th>
                            <th>Status</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/itd/techlog.js"></script>

<script>
    TECHLOG.list(1);
</script>

