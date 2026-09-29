<style>
    .trn-details .btn.disabled{
        font-weight: 550;
    }
    .trn-details .btn.btn-default.disabled{
        color: #002166 !important;
        font-weight: 550;
    }
</style>
<?php
$task_notify = task_notify($flowid, $dataid);


if ($task_notify->qry) {
    $task_color = 'red';
    $task_notify_message = $task_notify->msg;
} else {
    $task_color = 'green';
    $task_notify_message = '';
}

$module_request = module_request_navigation($approval);
$level = ($module_request->qry) ? $module_request->currtrn->lvl : 'Unknown';
$desc = ($module_request->qry) ? $module_request->currtrn->desc : 'Unknown';
$date = ($module_request->qry) ? $module_request->currtrn->date : 'Unknown';
$name = ($module_request->qry) ? $module_request->currtrn->name : 'Unknown';
?>
<div class="row margin-bottom-10">
    <div class="col-md-12">
        <div class="page-toolbar pull-left trn-details">
            <a href="<?php echo base_url() . 'module/' . $this->uri->segment(2) . '/list'; ?>" class="btn btn-danger pull-left"><i class="fa fa-backward fa-fw"></i> Back</a>
            <div class="btn-group">
                <a class="btn btn-primary  btn-fit-height disabled"><i class="fa fa-clock-o fa-fw"></i> APT </a>
                <a class="btn btn-warning  btn-fit-height disabled"><?php echo $level; ?></a>
                <a class="btn btn-default  btn-fit-height disabled"><b><?php echo $desc; ?></b></a>
                <a class="btn btn-default  btn-fit-height disabled"><?php echo $date; ?></a>
                <a class="btn btn-default  btn-fit-height disabled"><?php echo $name; ?></a>
            </div>
        </div>

        <div class="page-toolbar">
            <div class="tabbable-line pull-right" id="process-btn">
                <?php echo module_request_navigation($approval)->html; ?>
            </div>
        </div>

        <?php if($task_notify->qry) { ?>
            <div class="note note-danger" style="margin-top: 50px; margin-bottom: 0px;">
                <h4 class="block" style="margin-bottom: 0px;"><span class="font-red-flamingo"><i class="fa fa-warning"></i> <b>Attention!</b></span> <?php echo $task_notify_message; ?></h4>
            </div>
        <?php } ?>
    </div>
</div>
