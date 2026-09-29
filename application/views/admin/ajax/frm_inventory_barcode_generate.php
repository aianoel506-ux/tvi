<?php

?>

<form id="frm_generate_codes" method="post" action="<?php echo base_url(); ?>inventory/generatebarcode">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>Select Stock</label>
                    <input class="form-control" id="select2stock" placeholder="Select..." name="stockid"/>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="input-label">Serial Code Starts</label>
                        <input class="form-control" name="codestart" id="input_codestart" />
                    </div>
                    <div class="col-md-6">
                        <label class="input-label">Serial Code Count</label>
                        <input class="form-control" name="codecount" id="input_codecount" />
                    </div>
                </div>

            </div>
            <div class="col-md-7">
                <div id="barcode_content"><h3>Select stock!</h3></div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-default">Generate <i class="fa fa-angle-double-right"></i></button>
        <button type="button" id="btn_print_codes" class="btn btn-primary"><i class="fa fa-save"></i> Print</button>
    </div>
</form>


<script src="<?php echo base_url(); ?>assets/pages/inventory/main.js"></script>
<script type="text/javascript">
    INVENTORY.stockgeneratecode();
</script>
