<br>
<br>
<div class="progress progress-striped active">
    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="stat_bar">
    </div>
</div>
<div class="input-group">
    <span class="input-group-btn">
        <button id="btn_triggger" class="btn btn-success"><i class="fa fa-refresh"></i> Start</button>
    </span>
    <span class="input-group-addon" id="input_num" style="min-width: 100px;">
        Start Query
    </span>
    <input class="form-control" id="input_payclass" value="" style="width: 14%" placeholder=""  />
    <input class="form-control" id="input_month" value="" style="width: 14%" placeholder=""  />
    <input class="form-control" id="input_year" value="" style="width: 12%" placeholder=""  />
    <select class="form-control" id="input_paytype" value="" style="width: 12%">
        <option value="">Select paytype..</option>
        <option value="1">First Half</option>
        <option value="2">Second Half</option>
    </select>
    <input class="form-control" disabled id="input_start" value="" style="width: 38%" />
    <input class="form-control" disabled id="input_per" value="" style="width: 10%; text-align: right;" />
</div>

<!-- END PAGE CONTENT-->

<script src="<?php echo base_url(); ?>assets/pages/reports/payroll.js"></script>
<script type="text/javascript">
    PAYROLL.init();
</script>