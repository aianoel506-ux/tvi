<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/22/2018
 * Time: 4:35 PM
 */

$inputs = $this->input->post();

$ids = $inputs['ids'];

$selected_cnt = ($ids) ? count(explode(',', $ids)) : 0;

?>
<div class="container-fluid">
    <?php if($ids > 0) { ?>
    <form id="frm_info_entry" method="post" action="<?php echo base_url('ts/updatetcinfo');?>">
        <input type="hidden" class="form-control" name="ids" value="<?php echo $ids; ?>" />
        <div class="row margin-top-20">
            <div class="col-md-6">
                <label>Outage Type<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="outagetype" id="select_outages"/>

                <label>Equipments<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="equipments" id="select2_equipments"/>

                <label>Findings<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="findings" id="select2_findings"/>
                <label>Circuit Level<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="circuits" id="select2_circuitlevel"/>

            </div>
            <div class="col-md-6">

                <label>Team Assignment<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="team" id="select2_teams"/>

                <label>Status<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="status" id="select2_status"/>

                <label>Remarks<span class="required"></span></label>
                <textarea class="form-control" name="remarks"></textarea>
            </div>

        </div>

        <div class="row margin-top-20 margin-bottom-20">
        </div>

        <div class="modal-footer">
            <span class="pull-left"><?php echo $selected_cnt; ?> items(s) selected.</span>
            <div class="btn-group">
                <button class="btn btn-default" type="reset">Reset</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </div>
    </form>
    <?php }else { ?>
        <h3 class="alert alert-warning"><b>Warning</b> Please select Trouble Call item(s)!</h3>
    <?php } ?>
</div>




<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.modal();
</script>
