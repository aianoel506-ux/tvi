<?php
$type = $this->input->post('ids');
$type_arr = explode(',',$type)
?>
<form class="form-horizontal" method="post" action="<?php echo base_url('inventory/dataaddinit'); ?>" id="frm_add_types">
    <input type="hidden" name="table" value="<?php echo $type_arr[1]; ?>" />
    <input type="hidden" name="codes" value="<?php echo $type_arr[0]; ?>" />
    <div class="modal-body">
        <div class="form-group row">
            <label class="control-label col-md-3">
                Codes
            </label>
            <div class="col-md-9">
                <div class="input-icon">
                    <i class="fa fa-edit"></i>
                    <input class="form-control" name="names" placeholder="Codes..." />
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-md-3">
                Description
            </label>
            <div class="col-md-9">
                <div class="input-icon">
                    <i class="fa fa-edit"></i>
                    <textarea class="form-control" name="desc" placeholder="Descriptions..."></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="reset" class="btn btn-default"><i class="fa fa-refresh fa-fw"></i> Reset</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save</button>
    </div>
</form>