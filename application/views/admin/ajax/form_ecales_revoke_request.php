<?php
$ids = $this->input->post('ids');
$data_arr = explode(',',$ids);
$ecalesid = $data_arr[0];
$origin = $data_arr[1];
?>
<form action="<?php echo base_url();?>analysis/revokeecales" method="post" id="revoke_ecales">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="tabbable-line">
                            <h4 class="pull-left">Inventory Information</h4>
                            <ul class="nav nav-tabs">
                                <li class="active pull-right">
                                    <a href="#paysum" data-toggle="tab" aria-expanded="true">
                                        <i class="fa fa-file"></i> Details</a>
                                </li>
                            </ul>
                            <div class="tab-content"  style="min-height: 200px;">
                                <div class="tab-pane fade in active" id="paysum">
                                    <ul class="list-group summary">
                                        <li class="list-group-item"> Total Load: <span class="label label-default pull-right" id="total_load">0.00</span> </li>
                                        <li class="list-group-item"> Total Cost: <span class="label label-default pull-right" id="total_amt">0.00</span> </li>
                                        <li class="list-group-item"> Total Quantity: <span class="label label-default pull-right" id="total_qty">0.00</span> </li>
                                    </ul>
                                    <h4>Remarks</h4>
                                    <ul class="list-group summary">
                                        <li class="list-group-item" id="ecales_remarks">N/A</li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-md-radios">
                            <input type="hidden" id="dataid" name="ecalesid" value="<?php echo $ecalesid;?>" />
                            <input type="hidden" id="origin" name="origin" value="<?php echo $origin;?>" />
                            <label><i class="fa fa-eye"></i> Inspection & Design</label>
                            <div class="md-radio-inline">
                                <div class="md-radio">
                                    <input type="radio" id="pay_labor" name="indaction" class="md-radiobtn" value="1" required>
                                    <label for="pay_labor">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                        Pay First </label>
                                </div>
                                <div class="md-radio">
                                    <input type="radio" id="add_labor" name="indaction" class="md-radiobtn" value="2" required>
                                    <label for="add_labor">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                        Modify Inventory Items </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group form-md-line-input">

                            <textarea class="form-control" name="reason" id="reason" placeholder="write you reason of transaction void here." required></textarea>
                            <label for="reason"><i class="fa fa-edit"></i> Reason</label>
                            <span class="help-block">what is the reason of revoking this transaction?</span>

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button id="btn_submit_void" type="submit" class="btn blue"><i class="fa fa-save fa-fw"></i> Revoke ECALES</button>
    </div>
</form>
<script src="<?php echo base_url(); ?>/assets/pages/ecales.js" type="text/javascript"></script>
<script>
    ECALES.revoke(<?php echo $ids;?>);
</script>
