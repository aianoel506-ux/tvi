
<?php
$info = get_employee_info($empid);
if($info->qry) {
    $employee_name = $info->qry->firstname . ' ' . $info->qry->lastname;
?>

<div class="row">
    <div class="col-sm-12 col-sm-offset-0 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
        <h3 class="font-white">Leave Approval</h3>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-haze bold"><i class="fa fa-user"></i> <?php echo $employee_name; ?></div>
                <form action="<?php echo base_url(); ?>request/approvelonlineleave" method="post" id="frm_approve_online_leave">
                    <input id="input_leave_approvalid" value="<?php echo $approvalid;?>" type="hidden" name="approvalid"/>
                    <input id="input_leave_groupid" value="<?php echo $groupid;?>" type="hidden" name="groupid"/>
                    <input id="input_leave_empid" value="<?php echo $empid;?>" type="hidden" name="empid"/>
                    <div class="pull-right btn-group">
                        <?php if($details->status == 300) { ?>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Approve</button>
                        <a href="javascript:;" class="btn btn-danger"><i class="fa fa-times"></i></a>
                        <?php
                            } else {
                                if($details->status == 0) {
                                    echo '<span class="label label-danger"><i class="fa fa-times"></i> Disapproved</span>';
                                }else {
                                    echo get_types_label_format($details->status);
                                }
                            }
                        ?>
                    </div>
                </form>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <hr style="margin: 2px 0px;">
                        <h5 class="text-primary" style="margin: 5px 0px;"><i class="fa fa-tag"></i> Details</h5>
                        <ul class="list-group summary column">
                            <li class="list-group-item">
                                <span class="col-md-5 col-sm-6 label-name">Date Created</span>
                                <span class="col-md-7 col-sm-6 label-default number"><?php echo $details->datecreated; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-5 col-sm-6 label-name">Created By</span>
                                <span class="col-md-7 col-sm-6 label-default number"><?php echo $details->createdby; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-5 col-sm-6 label-name">Reason</span>
                                <span class="col-md-7 col-sm-6 label-default number"><?php echo $details->reason; ?></span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <hr style="margin: 2px 0px;">
                        <h5 class="text-primary" style="margin: 5px 0px;"><i class="fa fa-tag"></i> Consumption</h5>
                        <ul class="list-group summary column">
                            <li class="list-group-item">
                                <span class="col-md-5 col-sm-6 label-name">Total Day(s)</span>
                                <span class="col-md-7 col-sm-6 label-default number"><?php echo $details->totaldays; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-5 col-sm-6 label-name">Total Hour(s)</span>
                                <span class="col-md-7 col-sm-6 label-default number"><?php echo $details->totalhrs; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-5 col-sm-6 label-name">Total Minute(s)</span>
                                <span class="col-md-7 col-sm-6 label-default number"><?php echo $details->totalmins; ?></span>
                            </li>
                        </ul>
                    </div>


                    <div class="col-md-12 col-sm-12">
                        <hr style="margin: 2px 0px;">
                        <h5 class="text-primary" style="margin: 5px 0px;"><i class="fa fa-tag"></i> Lists of Transactions</h5>
                        <table class="table table-hover table-bordered table-condensed">

                            <thead>
                                <th>Type</th>
                                <th>Date Start</th>
                                <th>Date End</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                            </thead>
                            <tbody>

                            <?php echo $details->list;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php } else {
    echo page_data_notfound_full('Employee details not found!');
}
?>

<script type="text/javascript">
    var ONLINELEAVE = function() {

        var fn_online_leave_approval = function() {
            $(document).on('submit', '#frm_approve_online_leave', function(e) {
                e.preventDefault();
                var form = $(this);
                swal({
                    title: "Please confirm leave approval",
                    text: 'Approve Leave',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            dataType: 'json',
                        }).done(function(d) {
                            swal(d.msg, "Leave Approval", d.func);
                        });
                    }else{
                        swal.close();
                    }
                });
            });
        };

        return {
            init: function() {
                fn_online_leave_approval();
            }
        }
    }();

    ONLINELEAVE.init();
</script>