
<div class="tabbable-line">
    <div class="actions pull-right">
        <div class="btn-group">
            <a class="btn green-haze btn-outline" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true"> Actions
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#frm_inventory_stock_out" title="Stock Out" data-toggle="ajax-modal"> Stock Out Item(s)</a>
                </li>
                <li>
                    <a href="#frm_inventory_stock_in" title="Stock In" data-toggle="ajax-modal">Stock In Item(s)</a>
                </li>
                <li>
                    <a href="#frm_inventory_stock_retrun" title="Stock In" data-toggle="ajax-modal">Stock Return Item(s)</a>
                </li>
                <li class="divider"> </li>
                <li>
                    <a href="#frm_inventory_stock_out" title="Stock Out" data-toggle="ajax-modal">Stock Reorder Item(s)</a>
                </li>
                <li>
                    <a href="#frm_inventory_barcode_generate" title="Generate Barcode" data-toggle="ajax-modal">Stock Generate Barcode</a>
                </li>
            </ul>
        </div>
    </div>
    <ul id="inventory_tab" class="nav nav-tabs ">
        <li class="">
            <a href="#initialization" data-toggle="tab" aria-expanded="true"> Initialization </a>
        </li>
        <li class="">
            <a href="#products" data-toggle="tab" aria-expanded="false"> Products </a>
        </li>
        <li class="">
            <a href="#suppliers" data-toggle="tab" aria-expanded="false"> Suppliers </a>
        </li>
        <li class="active">
            <a href="#stocks" data-toggle="tab" aria-expanded="false"> Stocks </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade in" id="initialization">
            <div class="row">
                <div class="col-md-4">
                    <div class="portlet light bordered table">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-reorder font-dark"></i>
                                <span class="caption-subject font-green-haze bold uppercase">Category</span>
                            </div>

                            <div class="actions">
                                <a data-toggle="ajax-modal" class="btn btn-primary inline" title="Add Category" data-arr="ITEMCATEGORIES,tbl_category" href="#inventory_add_initialization">
                                    <i class="fa fa-plus"></i> Add Stocks
                                </a>
                                <a class="btn btn-default inline btn-refresh" href="javascript:;">
                                    <i class="fa fa-refresh"></i> Refresh Table
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-hover types" data-code="ITEMCATEGORIES" data-title="Category" id="tbl_category">
                                <thead>
                                <th>#</th>
                                <th>Codes</th>
                                <th>Descriptions</th>
                                <th>Control</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="portlet light bordered table">
                        <div class="portlet-title">
                            <div class="caption">
                            <i class="fa fa-reorder font-dark"></i>
                            <span class="caption-subject font-green-haze bold uppercase">Brands</span>
                            </div>

                            <div class="actions">
                                <a data-toggle="ajax-modal" class="btn btn-primary inline" title="Add Brand" data-arr="ITEMBRANDS,tbl_brand" href="#inventory_add_initialization">
                                    <i class="fa fa-plus"></i> Add Stocks
                                </a>
                                <a class="btn btn-default inline btn-refresh" href="javascript:;">
                                    <i class="fa fa-refresh"></i> Refresh Table
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">

                            <table class="table table-striped table-hover types" data-code="ITEMBRANDS" data-title="Brands" id="tbl_brand">
                                <thead>
                                <th>#</th>
                                <th>Codes</th>
                                <th>Descriptions</th>
                                <th>Control</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="portlet light bordered table">
                        <div class="portlet-title">
                            <div class="caption">
                            <i class="fa fa-reorder font-dark"></i>
                            <span class="caption-subject font-green-haze bold uppercase">Units</span>
                            </div>

                            <div class="actions">
                                <a data-toggle="ajax-modal" class="btn btn-primary inline" title="Add Unit" data-arr="ITEMUNITS,tbl_units" href="#inventory_add_initialization">
                                    <i class="fa fa-plus"></i> Add Units
                                </a>
                                <a class="btn btn-default inline btn-refresh" href="javascript:;">
                                    <i class="fa fa-refresh"></i> Refresh Table
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-hover types" data-code="ITEMUNITS" data-title="Units" id="tbl_units">
                                <thead>
                                <th>#</th>
                                <th>Codes</th>
                                <th>Descriptions</th>
                                <th>Control</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="tab-pane fade in" id="products">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered table">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-reorder font-dark"></i>
                                <span class="caption-subject font-dark bold uppercase">Products</span>
                            </div>

                            <div class="actions">
                                <a data-toggle="ajax-modal" class="btn btn-primary inline" data-arr="product" href="#form_add_products">
                                    <i class="fa fa-plus"></i> Add Products
                                </a>
                                <a class="btn btn-default inline" id="btn_product_refresh" href="javascript:;">
                                    <i class="fa fa-refresh"></i> Refresh Table
                                </a>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <table class="table table-striped table-hover" id="tbl_products">
                                <thead>
                                <th>#</th>
                                <th>Supplier</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Qty</th>
                                <th>Control</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="suppliers">
            <div class="portlet light bordered table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-users font-dark"></i>
                        <span class="caption-subject font-dark bold uppercase">Supplier</span>
                    </div>

                    <div class="actions">
                        <a data-toggle="ajax-modal" class="btn btn-primary inline" data-arr="product" href="#form_add_products">
                            <i class="fa fa-plus"></i> Add Supplier
                        </a>
                        <a class="btn btn-default inline" id="btn_product_refresh" href="javascript:;">
                            <i class="fa fa-refresh"></i> Refresh Table
                        </a>
                    </div>
                </div>

                <div class="portlet-body">
                    <table id="tbl_supplier" class="table table-striped table-hover table-bordered" id="tbl_products">
                        <thead>
                        <tr>

                            <th rowspan="2">#</th>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">Address</th>
                            <th colspan="3" class="info" style="text-align: center; letter-spacing: 20px;">CONTACT</th>
                            <th rowspan="2">Control</th>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Cellphone</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in active" id="stocks">

            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered table">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-reorder font-dark"></i>
                                <span class="caption-subject font-dark bold uppercase">Stocks</span>
                            </div>
                            <div class="actions">
                                <a data-toggle="ajax-modal" title="Add Stocks (Admin)" class="btn btn-primary inline" data-arr="stocks" href="#inventory_add_stocks">
                                    <i class="fa fa-plus"></i> Add Stocks
                                </a>
                                <a class="btn btn-warning inline" href="#form_add_items" title="Add New Item(s)" data-toggle="ajax-modal" data-container="body" class=""><i class="fa fa-plus"></i> Add Item(s)</a>
                                <a class="btn btn-default inline btn-refresh" id="btn_refresh_stocks" href="javascript:;">
                                    <i class="fa fa-refresh"></i> Refresh Table
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table style="width: 100%;" class="table table-striped table-hover table-bordered table-condensed" id="tbl_stocks">
                                <thead>
                                    <tr>
                                        <th rowspan="2"></th>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">Storage</th>
                                        <th rowspan="2">Supplier</th>
                                        <th rowspan="2">Product</th>
                                        <th rowspan="2">Brand</th>
                                        <th colspan="5" class="info" style="text-align: center; letter-spacing: 20px;">QUANTITY</th>
                                        <th rowspan="2">Price</th>
                                        <th rowspan="2">Unit</th>
                                        <th rowspan="2">Date Purchased</th>
                                        <th rowspan="2">Status</th>
                                        <th rowspan="2">Actions</th>
                                    </tr>
                                    <tr>
                                        <th>Purchased</th>
                                        <th>Requested <a href="#" data-toggle="tooltip" class="tooltips" data-placement="right" data-attachement="body" title="Pre-proposed from CAD customers"><i class="fa fa-question"></i></a></th>
                                        <th>Released</th>
                                        <th>Returned</th>
                                        <th>On Hand</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/inventory/main.js"></script>
<script>
    INVENTORY.init();
    INVENTORY.products();
</script>
