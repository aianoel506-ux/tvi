<?php

$id = $this->input->post('view');
$conttype = $this->input->post('ids');

$sql = $this->db->select("")->from("prime_contribution_matrix")
->where(array("sysid" => $id))->get()->row();

?>
<div class="row" style="margin-left: 10px;margin-right: 10px;">
    <form action="<?php echo base_url() ?>payroll/updatecontribrates" method="post"  id="submitcontribrates">
        <input type="hidden" value="<?php echo $id; ?>" name="dataid" />
        <div class="col-md-2">
            <div class="form-group">
                <label>Base</label>
                <input type="text" value="<?php echo ($sql) ?number_format((float)$sql->amtbase, 2, '.', '')  : ''; ?>" name="base"  class="form-control" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Min</label>
                <input type="text" value="<?php echo ($sql) ?number_format((float)$sql->amtmin, 2, '.', '')  : ''; ?>" name="min"  class="form-control" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Max</label>
                <input type="text" value="<?php echo ($sql) ? number_format((float)$sql->amtmax, 2, '.', ''): ''; ?>" name="max"  class="form-control" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Amount</label>
                <input type="text" value="<?php echo ($sql) ? number_format((float)$sql->amtcont, 2, '.', '') : ''; ?>" name="amoumt"  class="form-control" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Rate Employee</label>
                <input type="text" value="<?php echo ($sql) ?number_format((float)$sql->rateemployee, 2, '.', '')  : ''; ?>" name="rateemployee"  class="form-control" />
            </div>
        </div><div class="col-md-2">
            <div class="form-group">
                <label>Rate Employer</label>
                <input type="text" value="<?php echo ($sql) ? number_format((float)$sql->rateemployer, 2, '.', '')  : ''; ?>" name="rateemployer"  class="form-control" />
            </div>
        </div>
        <button type="submit" class="btn btn-primary pull-right">Save</button>
    </form>
</div>
