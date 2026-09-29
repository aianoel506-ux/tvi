
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

    <div class="portlet-body">

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


<div id="ps_overlay" class="ps_overlay" style="display:none;"></div>
<a id="ps_close" class="ps_close" style="display:none;"></a>
<div id="ps_container" class="ps_container" style="display:none;">
</div>



<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.lineteam();
</script>

