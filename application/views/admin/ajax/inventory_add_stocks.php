<?php

?>
<div class="modal-body">
    <form id="frm_add_stock_item" action="<?php echo base_url('inventory/addstocks'); ?>" method="post">

        <input id="item_id" value="" name="itemid" type="hidden"/>
        <input id="supplier_id" value="" name="supplierid" type="hidden"/>
                <div class="form-body">
                    <div class="form-group row input-entry">
                        <div class="col-md-3">
                            <label class="control-label">Qty:</label>
                            <input id="input_qty" type="number" value="1" name="qty" placeholder="Qty.." class="form-control" onclick="this.select()">

                        </div>
                        <div class="col-md-9">
                            <label for="item_select">Select product(s)</label>
                            <div class="input-group">
                                <input class="form-control input-reset" id="item_search" placeholder="Search Item.." required name="itemtext" />
                                <span class="input-group-addon">
                                     <a href="#form_add_items" title="Add New Item(s)" data-toggle="ajax-modal" data-container="body" class=""><i class="fa fa-plus"></i> Add More</a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row input-entry">

                        <div class="col-md-3">
                            <label for="item_supplier">Brand</label>
                            <input class="form-control input-reset" id="select2brands" placeholder="Brand.." name="brand" required />
                        </div>

                        <div class="col-md-3">
                            <label for="item_supplier">Supplier</label>
                            <input class="form-control input-reset" id="search_text_supplier" placeholder="New Supplier" name="supplier"  />
                        </div>

                        <div class="col-md-3">
                            <label for="item_price">New Price</label>
                            <input class="form-control input-reset" id="item_price" placeholder="New Price" name="price"  />
                        </div>

                        <div class="col-md-3">
                            <label for="item_price">Date Purchased</label>
                            <input class="form-control input-reset" type="date" id="item_date" placeholder="Select Date" name="date" />
                        </div>

                    </div>
                    <div class="form-group ">
                        <hr>
                        <label for="item_select">Last Purchase Details</label>
                        <ul class="list-group summary column " style="padding-bottom: 0px !important; margin-bottom: 10px !important;">
                            <li class="list-group-item"><span class="col-md-4 label-name">Price</span><span class="col-md-8 label-default number" id="text_lastprice">N/A</span> </li>
                            <li class="list-group-item"><span class="col-md-4 label-name">Date</span><span class="col-md-8 label-default number" id="text_lastdate">N/A</span> </li>
                            <li class="list-group-item"><span class="col-md-4 label-name">Total</span><span class="col-md-8 label-default number" style="color: red !important;" id="text_itemtotal">0.00</span></li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <button class="btn blue btn-outline sbold"><i class="fa fa-plus"></i> Save <i class="fa fa-sign-out"></i></button>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="note note-info">
                        <strong>Note:</strong> if item cannot be found on the search box, use <i class="fa fa-plus"></i> Add More button to add an item in the database.
                    </div>
                </div>


    </form>
</div>


<script src="<?php echo base_url(); ?>assets/pages/inventory/main.js"></script>
<script type="text/javascript">
    INVENTORY.search();
</script>
