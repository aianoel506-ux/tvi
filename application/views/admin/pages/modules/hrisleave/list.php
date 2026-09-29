<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/6/2018
 * Time: 9:11 AM
 */

?>

<div class="btn-group pull-right">
    <button class="btn btn-danger" id="btn_clear_cadtrans"><i class="fa fa-times"></i> <i class="fa fa-warning"></i> Clear Applications Transactions</button>
    <button id="leavereportsbtn" class="btn btn-default"><i class="fa fa-print"></i> Reports</button>
</div>
<div class="row">

    <div class="portlet">
        <div class="portlet-title">
            <div class="caption">
                Leave Reports
            </div>
        </div>
        <div class="portlet-body">
            <table class="table table-bordered table-condensed table-responsive" id="leavereportstable">
                <thead>
                <th></th>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>From</th>
                <th>To</th>
                <th>Hours</th>
                <th>Date Requested</th>
                <th>Status</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

</div>
<script src="<?php echo base_url() ?>assets/pages/hrisleave/hrisleave.js"></script>
<script>
    HRISLEAVE.init();
</script>
