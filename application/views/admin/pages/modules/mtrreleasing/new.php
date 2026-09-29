
<div class="clearfix"></div>
<div class="btn-group" id="btn_filter">
    <button class="btn btn-default disabled">Filter: </button>
    <button data-id="300" class="btn btn-pending btn-warning">Pending</button>
    <button data-id="361" class="btn btn-pending btn-primary">Released</button>
    <button data-id="362" class="btn btn-pending btn-success">Connected</button>
</div>
<hr>
<table width="100%" class="table table-bordered table-hover table-striped table-condensed" id="customer_list">
    <thead>
    <th><i class="fa fa-reorder"></i></th>
    <th>Customer Name</th>
    <th>Address</th>
    <th>Meter Serial</th>
    <th>G/D/L/B</th>
    <th>Date Time</th>
    <th>Status</th>
    <th>Reading</th>
    <th>Control</th>
    </thead>
    <tbody>
    </tbody>
</table>
<hr>
<div class="btn-group pull-right" style="">
    <button class="btn btn-default" type="button" id="btn_print_maps"><i class="fa fa-map-marker"></i> Print Map</button>
    <button class="btn btn-default" type="button" id="btn_print_list"><i class="fa fa-print"></i> Print List</button>
    <button class="btn btn-primary" type="button" id="btn_release_list"><i class="fa fa-forward"></i> Release</button>
</div>


<script src="<?php echo base_url(); ?>assets/pages/assets-powerplant/assetspowerplant.js"></script>
<script type="text/javascript">
    POWERPLANT.init();
</script>