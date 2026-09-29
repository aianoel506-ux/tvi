<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/18/2018
 * Time: 4:17 PM
 */

?>
<style>
    .dataTables_wrapper > .row .col-sm-6 {
        width: 35% !important;
    }
    .dataTables_wrapper > .row .col-sm-6:last-child {
        float: right !important;
    }
</style>
<div class="page-title">
    <h3>Ticket No.: <span class="pull-right text-primary text-bold"><?php echo str_pad($dataid, '6', '0', STR_PAD_LEFT); ?></span></h3>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="pull-left" style="width: 25%; display: inline-block;">
            <a class="btn btn-default" href="<?php echo base_url('module/eb4ac3033e8ab3591e0fcefa8c26ce3fd36d5a0f/view/'.$dataid); ?>"><i class="fa fa-search"></i> Back To Details</a>
            <a class="btn btn-primary" href="<?php echo base_url('module/eb4ac3033e8ab3591e0fcefa8c26ce3fd36d5a0f/list'); ?>"><i class="fa fa-reorder"></i> Back To List</a>
        </div>
        <table class="table table-hover table-bordered table-condensed" id="tbl_ticket_log">
            <thead>
            <th>#</th>
            <th>Codes</th>
            <th>Action</th>
            <th>Descriptions</th>
            <th>Comments</th>
            <th>Time</th>
            <th>User</th>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>



<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.logs(<?php echo $dataid; ?>);
</script>
