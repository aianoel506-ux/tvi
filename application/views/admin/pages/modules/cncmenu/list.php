<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 9/3/2018
 * Time: 11:45 AM
 */
?>

<div class="portlet light table">
    <div class="portlet-title">
        <div class="caption">
            <h3 class="font-green-meadow"><i class="fa fa-table"></i> AR List <span style="display: none;" id="qry_stat"><span id="q_percent">0</span>% Query Complete</span></h3>
        </div>

        <div class="tools pull-right" style="width: 700px;">
            <div class="input-group">
                <div class="input-group-addon">DISTRICT</div>
                <input style="width: 33%; display:inline-block;" class="form-control" id="inputyear" placeholder="Year" />
                <input style="width: 33%; display:inline-block;" class="form-control" id="inputmonth" placeholder="Month" />
                <input style="width: 33%; display:inline-block;" class="form-control" id="select2dist" placeholder="Select Dist.." />
                <div class="input-group-btn">
                    <button id="btn_query_payment_all" class="btn btn-default">Import</button>
                </div>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <table class="table table-hover table-striped table-bordered table-condensed" id="tbl_ar_list">
            <thead>
                <th>#</th>
                <th>Servno</th>
                <th>Name</th>
                <th>Address</th>
                <th>Current</th>
                <th>Overdue</th>
                <th>Control</th>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script src="<?php echo base_url();?>assets/pages/billing/ar.js" type="text/javascript"></script>
<script>
    AR.cnclist();
</script>
