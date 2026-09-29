<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 8/30/2018
 * Time: 1:38 PM
 */


$uemp_info = get_user_employee_info();
if($uemp_info || user_id() == 1) {
    $empid = ($uemp_info) ? $uemp_info->sysid : ((user_id()==1) ? 159 : 0);
    ?>
    <style>
        .input-label {
            font-size: 10px !important;
        }
    </style>
    <form method="post" action="<?php echo base_url(); ?>request/addleaveitem" id="frm_add_employee_leave_draft">
        <input type="hidden" name="empid" value="<?php echo $empid; ?>">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-4">
                    <label class="text-primary">Create Leave Draft</label>
                    <div class="form-group row leave-input">
                        <div class="col-md-4">
                            <label class="input-label">Year</label>
                            <input id="input_leave_year" class="form-control  input-sm" name="year" placeholder="Year.." value="<?php echo date('Y');?>"/>
                        </div>
                        <div class="col-md-8">
                            <label class="input-label">Type</label>
                            <select class="form-control input-sm" name="leavetype" id="typeofleave">
                                <option value="1">Regular Leave</option>
                                <option value="2">Locator Leave</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group leave-input">
                        <label class="input-label">Remarks</label>
                        <textarea maxlength="225" rows="1" class="form-control" name="leaveremarks" id="input_leave_remarks" placeholder="Remarks.." ></textarea>
                    </div>

                    <a href="javascript:;" class="tooltips" data-placement="right" title="Applicable only if the leave of employee is over a days!" >Days Leave <i class="fa fa-question text-info"></i></a>
                    <div class="form-group row leave-input">

                        <div class="col-md-6">
                            <label class="input-label">Date From</label>
                            <input id="input_date_from" value="<?php echo date('Y-m-d');?>" class="form-control input-sm" name="datefrom" placeholder="Date from.." />
                        </div>

                        <div class="col-md-6">
                            <label class="input-label">Date End</label>
                            <input id="input_date_end" class="form-control input-sm" name="dateend" placeholder="Date end.." />
                        </div>
                    </div>
                    <a href="javascript:;" class="tooltips" data-placement="right" title="Applicable only if the leave of employee is in the hourly bases!" >Hourly Leave <i class="fa fa-question text-info"></i></a>
                    <div class="form-group row leave-input">
                        <div class="col-md-6">
                            <label class="input-label">Time Start</label>
                            <input id="input_time_start" class="form-control timepicker timepicker-default input-sm" name="timestart" placeholder="Time from.." />
                        </div>
                        <div class="col-md-6">
                            <label class="input-label">Time End</label>
                            <input id="input_time_start" class="form-control timepicker timepicker-default input-sm" name="timeend" placeholder="Time end.." />
                        </div>
                    </div>

                    <div class="form-group ">
                        <button class="btn btn-default pull-right" type="submit"><i class="fa fa-plus"></i> Add</button>
                    </div>

                </div>

                <div class="col-md-8">
                    <label class="text-primary">Leave Credit</label>
                    <ul class="list-group summary column no-border" id="tbl_leave_credits_status"></ul>

                    <label class="text-primary">Draft List</label>
                    <table class="table table-bordered table-hover tbl-xs" id="tbl_employee_leave_draft">
                        <thead>
                        <tr><th></th>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>From Time</th>
                            <th>To Time</th>
                            <th>Type</th>
                            <th></th>
                        </tr></thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-primary" id="btn_submit_leave"><i class="fa fa-save"></i> Submit</button>
            </div>
        </div>

    </form>

    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/pages/hris/employeerequest.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/pages/request.js"></script>

    <script type="text/javascript">
        EMPLOYEEREQ.init(<?php echo $empid; ?>);
    </script>


    <?php
} else {
    echo '<div class="modal-body">';
    page_data_notfound_modal('Employee data for this login was not found!');
    echo '</div>';
}
?>
