
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

<script src="<?php echo base_url(); ?>assets/pages/utility/list.js"></script>

<script>
    LIST.init();
</script>

