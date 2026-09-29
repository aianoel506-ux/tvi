<?php
$ids = $this->input->post('ids');
$ids_arr = explode(',', $ids);
$dataid = $ids_arr[0];
$origin = $ids_arr[1];
?>
<div class="row margin-top-10">
    <div class="col-md-12">
        <ul class="list-group summary column table no-border">
            <li class="list-group-item">
                <span class="col-md-5 label-name">Project Amt.</span>
                <span class="col-md-7 label-default number" id="assesstmentgdramt">0.00</span>
            </li>
            <li class="list-group-item">
                <span class="col-md-5 label-name">Monthly Amt.</span>
                <span class="col-md-7 label-default number" id="assesstmentinitamt">0.00</span>
            </li>
            <li class="list-group-item">
                <span class="col-md-5 label-name">Materials Amt.</span>
                <span class="col-md-7 label-default number" id="assesstmentservicelaboramt">0.00</span>
            </li>
            <li class="list-group-item">
                <span class="col-md-5 label-name">Total</span>
                <span class="col-md-7 label-default number" id="servgrandtotal">0.00</span>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-advance table-borderd table-hover table-striped table-bordered table-condensed table-advance" id="tbl_assesstments">
            <thead>
            <th><i class="fa fa-reorder"></i></th>
            <th>Account Code</th>
            <th>Account Name</th>
            <th>No-VAT Amt.</th>
            <th>VAT Amt.</th>
            <th>Total</th>
            <th width="60px">Stat</th>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script src="<?php echo base_url(); ?>/assets/pages/tellering/assessments.js" type="text/javascript"></script>

<script>
    // @TODO change this to query view only.
    ASSESSMENTS.list(<?php echo $dataid; ?>, <?php echo $origin; ?>);
</script>
