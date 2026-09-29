<?php

?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-5">
            <form id="frm_stock_in" method="post" action="<?php echo base_url(); ?>inventory/draftstockin">
            <div class="form-group">
                <label>Select Stock</label>
                <input class="form-control" id="select2stock" placeholder="Select..." name="stockid"/>
            </div>
            <div class="form-group form-group-lg">
                <label>Scan Item</label>
                <div class="input-group input-group-lg">
                    <input class="form-control" id="select2stock" placeholder="Scan / Enter Barcode" name="serials"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default " type="subtmi">Enter</button>
                    </span>
                </div>
            </div>
            </form>
        </div>
        <div class="col-md-7">
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

        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" id="btn_stock_in_save" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Save</button>
</div>


<script src="<?php echo base_url(); ?>assets/pages/inventory/main.js"></script>
<script type="text/javascript">
    INVENTORY.stocksin();
</script>
