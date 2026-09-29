<?php
$ids = $this->input->post('ids');
$emails_arr = explode(',', $ids);
$user_info = get_user_employee_info();
if($user_info || super_admin()) {

    if($user_info) {
        $emp_info = get_employee_info($user_info->sysid);
    } else {
        $emp_info = false;
    }

    if ($user_info || super_admin()) {
        if (count($emails_arr) > 1) {
            $email_from = $emails_arr[0];
            $email_to = $emails_arr[1];
        } else {
            if($emp_info && $emp_info->qry == true) {
                $email_from = $emp_info->emailcomp;
            }else{
                $email_from = 'no-reply@panayelectric.com';
            }
            $email_to = $ids;
        }
        ?>
        <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-summernote/summernote.css" rel="stylesheet"
              type="text/css"/>

        <form id="frm_submit_email" method="post" action="<?php echo base_url() . 'query/sendbasicemail'; ?>">
            <input type="hidden" name="from" value="<?php echo $email_from; ?>" />
            <input type="hidden" name="to" value="<?php echo $email_to; ?>" />

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <ul class="list-group summary column list-group-sm no-border">
                            <li class="list-group-item">
                                <span class="col-md-3 label-name">From</span>
                                <span class="col-md-9 label-default"><?php echo $email_from; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-3 label-name">To</span>
                                <span class="col-md-9 label-default"><?php echo $email_to; ?></span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-default btn-block" style="margin-right: 30px; height: 45px;"><i class="fa fa-send"></i> Send</button>
                    </div>
                </div>
                <hr style="margin: 10px 0px;">
                <div style="margin-top: 10px;" class="form-group">
                    <input required name="subject" class="form-control" placeholder="Subject" />
                </div>
                <div style="margin-top: 10px;" class="form-group">
                    <textarea id="input_email_message" class="form-control" placeholder="Message.." name="message" id="input_email_message"/>
                </div>
            </div>
        </form>

        <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-summernote/summernote.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $("#input_email_message", document).summernote({height: 200});
        </script>
        <?php
    } else {
        page_data_notfound_modal('User is not allowed to access this function.');
    }
}else{
    page_data_notfound_modal('User is not allowed to access this function.');
}
?>