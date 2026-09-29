<?php
    $ids = $this->input->post('ids');
    $ids_arr = explode(',', $ids);
    $dataid = $ids_arr[0];
?>
<div class="tabbable-line">
    <ul class="nav nav-tabs">
        <li class="">
            <a href="#" data-id="0" data-toggle="tab"> Released </a>
        </li>
        <li class="active">
            <a href="#" data-id="1" data-toggle="tab"> On-Hand </a>
        </li>
    </ul>
</div>
<table class="table table-hover table-striped" id="tbl_stocks_in_list">
    <thead>
    <th>#</th>
    <th>Serial Number</th>
    <th>Date</th>
    <th>Status</th>
    <th>Control</th>
    </thead>
    <tbody></tbody>
</table>


<script src="<?php echo base_url(); ?>assets/pages/inventory/main.js"></script>
<script type="text/javascript">
    INVENTORY.stocks(<?php echo $dataid;?>);
</script>