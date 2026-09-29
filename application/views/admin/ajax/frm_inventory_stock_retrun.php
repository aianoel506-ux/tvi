<?php
?>
<form id="frm_stock_out" method="post" action="<?php echo base_url(); ?>inventory/savestockreturn">
    <input name="return" value="1" type="hidden" />
    <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group form-group-lg">
                    <lable>Search / Scan Barcode</lable>
                    <div class="input-icon right">
                        <i class="fa fa-search"></i>
                        <input required id="search_stockout_code" class="form-control" placeholder="Search / Scan code.." name="codes" />
                        <span class="help-block text-danger" id="stock_out_stat"></span>
                    </div>
                </div>

                <div class="form-group form-group-lg">
                    <lable>Quantity</lable>
                    <input required id="stock_out_qty" class="form-control" placeholder="Number of item(s)" name="qty" />
                </div>
            </div>
            <div class="col-md-7">
                <lable>Details</lable>
                <ul class="list-group summary column">
                    <li class="list-group-item">
                        <span class="col-md-3 label-name">Description</span>
                        <span class="col-md-9 label-default" id="stockout_text_desc">Unknown</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-3 label-name">Stock On-hand</span>
                        <span class="col-md-9 label-default" id="stockout_text_stocks">0</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
