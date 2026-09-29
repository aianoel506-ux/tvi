
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
                <span class="btn-group" id="btn_filters" style="margin-top: 5px;">
                    <button type="button" data-id="0" class="btn btn-default btn-sm"><i class="fa fa-tag fa-fw"></i> All</button>
                    <?php
                    $query_status = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg, tp.colortxt')
                        ->from('prime_types_parameter AS tp')
                        ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
                        ->where('tssm.codes', 'CWD')
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
                        <div class="row margin-bottom-10">
                            <div class="pull-left col-md-8" id="" >
                                <button type="button" class="btn btn-default btn-sm" id="btn_refresh_list"><i class="fa fa-refresh"></i> Refresh Table</button>
                            </div>
                            <div class="col-md-4">
                            </div>

                        </div>
                        <hr style="margin:4px 0px;">


                    </div>
                    <div class="portlet-body">

                        <table class="table table-hover table-condensed table-striped table-bordered tbl-sm table-resizable table-wrap" id="tbl_ticket_list" style="width: 100%">
                            <thead>
                            <th></th>
                            <th><i class="fa fa-reorder"></i></th>
                            <th>Ticket No.</th>
                            <th>Name</th>
                            <th>Account Info</th>
                            <th>Time Lapsed</th>
                            <th>Reports</th>
                            <th>Code</th>
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


<script src="<?php echo base_url(); ?>assets/pages/rvmenu/list.js"></script>

<script>
    LIST.init();
</script>

