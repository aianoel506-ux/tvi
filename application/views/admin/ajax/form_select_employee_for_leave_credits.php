<?php
$ids = $this->input->post('ids');
$ids_arr = explode(',', $ids);

$leavetype = $ids_arr[0];
$year = $ids_arr[1];
$days = $ids_arr[2];
$hours = $ids_arr[3];
if($ids != '' && count($ids_arr) == 4) {
    ?>


    <div class="row">
        <div class="col-md-4">
            <input class="form-control col-md-4" id="select_payclass"/>
        </div>
    </div>

    <form action="<?php echo base_url('hris/addleavecreditselected');?>" method="post" id="frm_submit_selected_employee_leave_credits">
        <input type="hidden" name="types" id="creditval" value="<?php echo $leavetype ?>" />
        <input type="hidden" name="year" id="yearval" value="<?php echo $year ?>" />
        <input type="hidden" name="nodays" id="nodaysval" value="<?php echo $days ?>" />
        <input type="hidden" name="nohours" id="nohoursval" value="<?php echo $hours ?>" />

        <table class="table table-bordered" id="employeelist">
            <thead>
            <th></th>
            <th>Lastname</th>
            <th>Firstname</th>
            <th>Middlename</th>
            <th></th>
            </thead>
            <tbody></tbody>
        </table>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="applyselectedemp" class="btn btn-primary" >Apply</button>
        </div>
    </form>


    <script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/hrmain.js"></script>
    <script type="text/javascript">
        MAINTENACE.initleavecreditsentrymodal();
        var id = $('#creditsysid', document).val();
        var year = $('#yeartxt', document).val();
        var days = $('#nodays', document).val();
        var hours = $('#nohours', document).val();
        $(document).find('#creditval').val(id);
        $(document).find('#yearval').val(year);
        $(document).find('#nodaysval').val(days);
        $(document).find('#nohoursval').val(hours);
    </script>
<?php


}else{
    page_data_notfound();
}
?>