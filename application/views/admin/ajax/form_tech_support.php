<style type="text/css">
    .tt-input  {
        color: #000 !important;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/22/2018
 * Time: 4:42 PM
 */
// $view = $this->input->post("view");


$info = get_user_employee_info();
// $info = false;
if($info) {
    $empinfo = get_employee_info($info->sysid);
    ?>
    <form id="frm_techlog_entry" method="post" action="<?php echo base_url('ts/submitticket');?>" enctype="multipart/form-data">
        <div class="modal-body">
            <input name="repsource" type="hidden" value="<?php echo $empinfo->deptid; ?>" />
            <input name="personid" type="hidden" value="<?php echo $empinfo->personid; ?>" />

            <div class="row">
                <div class="col-md-4 complainants">
                    <div class="well">
                        <div class="form-group">
                            <label>Last Name</label>
                            <h4 class="bold font-blue">
                                <?php echo $info->firstname . ' ' . $info->lastname;?>
                            </h4>
                        </div>

                        <div class="form-group">
                            <label>Department</label>
                            <h4 class="bold font-blue">
                                <?php echo $empinfo->deptname;?>
                            </h4>
                        </div>

                        <div class="form-group">
                            <label>Position</label>
                            <h4 class="bold font-blue">
                                <?php echo ucwords(strtolower($empinfo->position));?>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <h4 class="text-primary text-bold">Nature of Report</h4>
                    <label>Issue Type<span class="required"></span></label>
                    <input style="width: 100%;" class="table-group-action-input form-control input-medium" name="outage" id="select2issuetype"/>

                    <label style="margin-top: 10px;">Date and Time Issue Occured<span class="required"></span></label>
                    <div class="input-icon">
                        <i class="fa fa-calendar font-blue"></i>
                        <input style="width: 90%; display" type="text" size="16" value="<?php echo date('Y-m-d H:i'); ?>" class="form-control" name="issuedatetime" id="techlogdatetime">
                    </div>

                    <label style="margin-top: 10px;">Priority<span class="required"></span></label>
                    <input style="width: 100%;" class="table-group-action-input form-control input-medium" name="priority" id="select_priority"/>
                </div>

                <div class="col-md-4">
                    <h4 class="text-primary text-bold">More Details</h4>
                    <label class="">Elaborate<span class="required"></span></label>
                    <div class="input-icon">
                        <i class="fa fa-comment-o font-blue"></i>
                        <textarea rows="8" cols="" class="form-control" name="remarks" placeholder="Describe the event and the cause of the issue.. "></textarea>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <div class="col-md-12">
            <span class="pull-left" id="">
                <div class="col-md-5">
                    <i class="fa fa-files-o pull-left"></i> Attachedments:
                </div>
                <div class="col-md-7">
                    <input class="" type="file" name="pics[]" multiple />
                </div>
            </span>
                <button id="submit_btn" type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save Ticket</button>

                <button type="reset" class="btn btn-default">Reset</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
<?php
} else {
    page_data_notfound_modal('<b class="text-danger">Error:</b> Employee information is not found on this account!');
}
?>

<script type="text/javascript" src="<?php echo base_url();?>assets/pages/itd/techlog.js"></script>

<script type="text/javascript">
    TECHLOG.init();
</script>


