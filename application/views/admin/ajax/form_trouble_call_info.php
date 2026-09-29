<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/22/2018
 * Time: 4:35 PM
 */

$inputs = $this->input->post();

$ids = $inputs['ids'];



?>
<div class="container-fluid">
    <input type="hidden" value="<?php echo $ids; ?>" name="ids" />
    <form id="frm_info_entry" method="post" action="<?php echo base_url('ts/addtcinfo');?>">
        <div class="row margin-top-20">
            <div class="col-md-12">
                <div class="form-group">

                    <label class="control-label">Specific Address</label>
                    <input class="form-control" placeholder="Address..." name="address" />
                </div>
            </div>
            <div class="col-md-4">
                <label>Team<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="team" id="select2_teams"/>

                <label>District<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="district" id="select_district"/>

                <label>Landmarks<span class="required"></span></label>
                <input readonly placeholder="Landmark.. " class="table-group-action-input form-control" name="landmark" id="select_landmark"/>

            </div>
            <div class="col-md-4">
                <label>Equipments<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="equipments" id="select2_equipments"/>

                <label>Findings<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="findings" id="select2_findings"/>
                <label>Circuit Level<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="circuits" id="select2_circuitlevel"/>

            </div>
            <div class="col-md-4">
                <label>Status<span class="required"></span></label>
                <input class="table-group-action-input form-control" name="status" id="select2_status"/>
                <label>Remarks<span class="required"></span></label>
                <textarea class="form-control" name="remarks"></textarea>
            </div>

        </div>

        <div class="row margin-top-20 margin-bottom-20">
        </div>

        <div class="modal-footer">

            <div class="btn-group pull-right">
                <button class="btn btn-default" type="reset">Reset</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </div>
    </form>
</div>




<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.modal();
</script>
