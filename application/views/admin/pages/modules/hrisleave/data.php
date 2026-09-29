<?php


$get_emp_info = $this->db->select()->from('trn_employee_leave_requests')
    ->where(array('groupid' => $dataid, 'status != ' => 0))
    ->get()->row();

if($get_emp_info) {

    $empid = $get_emp_info->empid;

    $info = get_employee_info($empid);

    if ($info->qry == true) {
        $lvl = module_request_navigation($approval)->currtrn->lvl;
        $empid = $info->sysid;
        $emp_approval = false;
        if ($approval) {
            $emp_approval = get_employee_approval($empid);
        } else {
            $emp_approval = get_employee_dephead($empid);
        }
        $emp_approval_lastname = '';
        if ($emp_approval) {
            $emp_approval_lastname = $emp_approval->lastname . ', ' . $emp_approval->firstname;
        }


        $stat = 300;

        if (($emp_approval && $info->sysid == $emp_approval->empid) || user_id() == 1) {
            ?>
            <input type="hidden" id="data_id" value="<?php echo $dataid;?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">
                        <input type="hidden" value="<?php echo $info->sysid; ?>" id="hiddenempid"/>
                        <?php echo $info->lastname . ', ' . $info->firstname; ?>
                    </span>
                                <br>
                                <span class="caption-helper"
                                      style="margin-left: 25px;"><?php echo(select_emp_position($info->sysid)->names); ?></span>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <ul class="list-group summary column" id="tbl_leave_credits_status"></ul>
                        </div>
                    </div>
                </div>


                <div class="col-md-8">
                    <div class="portlet light table">
                        <div class="portlet-title">

                            <div class="caption">
                                <i class="fa fa-table"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">
                        Requests
                    </span>

                                <br>
                                <span class="caption-helper" style="margin-left: 25px;">lists</span>
                            </div>
                        </div>

                        <div class="portlet-body form">
                            <table class="table table-hover table-condensed table-responsive table-checkbox"
                                   id="tbl_leave_requests">
                                <thead>
                                <th></th>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th>Control</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class="form-actions" style="padding-left: 20px; padding-right: 20px;">
                                <div class="col-md-4">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item">
                                            <span class="col-md-6 label-name">Total No Days</span>
                                            <span class="col-md-6 label-default number" id="total_no_days">0.0</span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-6 label-name">Total No Hours</span>
                                            <span class="col-md-6 label-default number" id="total_no_hours">0.0</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item">
                                            <span class="col-md-6 label-name">Approval</span>
                                            <span class="col-md-6 label-default number"
                                                  id=""><?php echo $emp_approval_lastname; ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="btn-group pull-right margin-top-20">

                                    <?php
                                    if ($stageid == 39) { ?>
                                        <button data-id="<?php echo $info->sysid; ?>" id="approved_btn" type="button"
                                                class="btn btn-primary"><i class="fa fa-check"></i> Approve
                                        </button>
                                        <button data-id="<?php echo $info->sysid; ?>" id="disapproved_btn" type="button"
                                                class="btn btn-danger"><i class="fa fa-times"></i> Disapprove
                                        </button>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else {
            page_permission();
        }
    } else {
        page_data_notfound('Request not found!');
    }
}else{
    page_data_notfound('Request not found!');
}
?>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/pages/request.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/view.js" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/employeerequest.js" ></script>
<script>


    HRIS.init();
    HRIS.leavecredits($('#list_leave_credits'), <?php echo $dataid?>);
    REQUEST.init(<?php echo $dataid ?>);
    REQUEST.initreqtbl(<?php echo $dataid?>, 300);
    EMPLOYEEREQ.init(<?php echo $empid?>);

</script>