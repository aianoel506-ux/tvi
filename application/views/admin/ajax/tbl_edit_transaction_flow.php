<?php
$id = $this->input->post('ids');
?>
<style type="text/css">
    .dataTables_scrollBody{
        scrollbar-width: none !important;
    }

    ::-webkit-scrollbar {
        width: 0px;
    }
</style>

<div class="modal-body">
    <table class="table table-condensed table-bordered table-hover table-hover tbl-sm" id="trnflowstagestbl">
        <thead>
            <th>Level</th>
            <th>Descriptions</th>
            <th>Module ID</th>
            <th>Move</th>
            <th>Control</th>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
<script src="<?php echo base_url(); ?>assets/pages/settings/trnflow.js"></script>
<script type="text/javascript">
    TRANSACTIONSFLOW.stages($('#trnflowstagestbl',document),<?php echo $id;?>);
</script>