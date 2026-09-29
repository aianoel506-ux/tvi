<?php
$ids = $this->input->post('ids');
$appid = $this->input->post('view');
?>
<style type="text/css">
    td {
        max-width: 40px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<div class="modal-body">
    <input type="hidden" name="appid" value="<?php echo $appid;?>" id="input_app_id">
    <table class="table table-striped table-hover types" id="tbl_sps_template_list" width="100%">
        <thead>
        <th>#</th>
        <th>Name</th>
        <th>System Type</th>
        <th>Panel Type</th>
        <th>#Panels</th>
        <th>#Strings</th>
        <th>Panels/String</th>
        <th>Inverter Size</th>
        <th>Select</th>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<script src="<?php echo base_url(); ?>assets/pages/inspection/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/ellipsis.js" type="text/javascript"></script>
<script type="text/javascript">
    INSPECTION.templates('<?php echo $ids;?>');
</script>