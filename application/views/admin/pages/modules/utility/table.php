
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

    .portlet.table .dataTables_filter {
        padding-right: 15px !important;
    }

    .form-control[type=date] {
        padding: 0px 5px !important;
        margin: 0px 5px !important;
    }
</style>
<div class="row">


    <div class="col-md-12">
        <div class="portlet light bordered table">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder font-green-haze"></i> Job Order List
                </div>
            </div>
            <div class="portlet-body">

                <div class="col-md-6 pull-left">
                    <button id="btn_accomplish_joborder" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Accomplish</button>
                    <button id="btn_refresh_joborder" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                    <button class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print Job Order Sheet</button>
                </div>

                <table class="table table-hover table-bordered table-condensed" style="width: 100%" id="tbl_jo_sheet">
                    <thead>
                    <th></th>
                    <th>JO#</th>
                    <th>Job Order</th>
                    <th>Accounts</th>
                    <th>Meter No.</th>
                    <th>Serial</th>
                    <th>Date Comply</th>
                    <th>Date Created</th>
                    <th>Status</th>
                    <th>Control</th>
                    </thead>
                    <tbody></tbody>

                </table>

                <hr>

            </div>


        </div>
    </div>
</div>

<hr>


<script src="<?php echo base_url(); ?>assets/pages/utility/main.js"></script>

<script>
    UTILITY.init();
    UTILITY.mtr();
    UTILITY.jo();
</script>

