<?php
$ids = $this->input->post('ids');
$ids_arr = explode(',', $ids);
$sched_id = (isset($ids_arr[0])) ? $ids_arr[0] : false;
$user_id = (isset($ids_arr[1])) ? $ids_arr[1] : false;
?>
<div style="padding: 10px 10px;">
    <div class="col-md-6 pull-left">
        <button data-id="<?php echo $sched_id;?>" data-user="<?php echo $user_id; ?>" style="margin-left: -15px;" type="button" class="btn btn-default" id="btn_print_cust_list"><i class="fa fa-print"></i> Print Schedules</button>
        <button data-id="<?php echo $sched_id;?>" style="" type="button" class="btn btn-danger" id="btn_update_sync"><i class="fa fa-refresh"></i> Sync</button>
    </div>
    <table class="table table-hover table-striped table-condensed table-bordered tbl-sm" id="tbl_gdlb_customers">
        <thead>
            <th>Seq</th>
            <th>Service #</th>
            <th>Name</th>
            <th>Address</th>
            <th>Meter No.</th>
            <th>Meter Serial</th>
            <th>Tagging</th>
            <th>Control</th>
        </thead>
        <tbody></tbody>
    </table>
</div>



<script src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script>
<script type="text/javascript">
    MRD.gdlbcustomer(<?php echo $sched_id?>, <?php echo $user_id?>);
</script>

