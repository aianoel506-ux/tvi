<?php
$ids = $this->input->post('ids');

?>
<div class="portlet-body">
    <input type="hidden" name="appid" value="<?php echo $ids;?>">
    <table width="100%" class="table table-hover table-striped table-condensed tbl-xs" id="tbl_add_requirement_list"  style="margin: 10px !important;">
        <thead>
        <th>#</th>
        <th>Req Code</th>
        <th>Requirement</th>
        <th><i class="fa fa-check-square fa-lg"></th>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>

<script type="text/javascript">
    CAD.addRequirements(<?php echo $ids;?>);
</script>