<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 8/9/2018
 * Time: 1:38 PM
 */


$ids = $this->input->post('ids');
$data_arr = explode(',', $ids);
$readerid = $data_arr[0];
$billmo = $data_arr[1];
$billyr = $data_arr[2];
$date = $this->input->post('view');

?>
<style>
    .table td {
        position: relative;
    }
</style>
<form role="form" class="" id="frm_assign_sched" action="<?php echo base_url('mrd/assignreadingschedule'); ?>" method="post">

<div class="row" style="padding: 5px 10px;">
    <div class="col-md-8">
        <h4 class="text-primary">Select GDLB Assignment</h4>
        <hr>
        <div class="col-md-6 pull-left" style="margin-left: -15px;">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        DIST
                    </span>
                <input class="form-control" id="modal_select_district" placeholder="Select District.." />

                </div>
            </div>
        </div>
        <table class="table table-hover table-striped table-condensed table-bordered  " id="tbl_assign_gdlb">
            <thead>
            <th><i class="fa fa-reorder"></i></th>
            <th>GDLB</th>
            <th>Cust. No. / Limit</th>
            <th>Schedule</th>
            <th></th>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="col-md-4">
        <h4 class="text-primary">Assignment Details</h4>
        <hr>
        <input type="hidden" name="scheddate" id="entry_scheddate" value="<?php echo $date; ?>" />
        <input type="hidden" name="empid" id="entry_empid" value="<?php echo $readerid; ?>" />

        <input type="hidden" name="rmonth" id="entry_rmonth" value="<?php echo $billmo; ?>" />
        <input type="hidden" name="ryear" id="entry_ryear" value="<?php echo $billyr; ?>" />
        <div class="form-group">
            <ul class="list-group summary column">
                <li class="list-group-item">
                    <span class="col-md-6 label-name">Reading Date</span>
                    <span class="col-md-6 label-default"><?php echo $date; ?></span>
                </li>
                <li class="list-group-item">
                    <span class="col-md-6 label-name">Billing Year</span>
                    <span class="col-md-6 label-default"><?php echo $billyr; ?></span>
                </li>
                <li class="list-group-item">
                    <span class="col-md-6 label-name">Billing Month</span>
                    <span class="col-md-6 label-default"><?php echo date_formating($billmo, 'm', 'F'); ?></span>
                </li>
            </ul>
        </div>

        <div class="form-group">
            <button type="submit" class="btn blue btn-block" id="btn_assign"><i class="fa fa-save" ></i> Assign</button>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-default btn-block" id="btn_print_cust_list"><i class="fa fa-print"></i> Print Schedules</button>
        </div>
    </div>

</div>

</form>
<script src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script>
<script type="text/javascript">
    MRD.mrdreports();
</script>
