<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder font-dark"></i>
                    <span class="caption-subject font-green-haze bold uppercase">Supplier List</span>

                    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="date" class="form-control date-picker" name="from">
                        <span class="input-group-addon"> to </span>
                        <input type="date" class="form-control date-picker" name="to">
                    </div>
                </div>

                <div class="actions">
                    <a data-toggle="ajax-modal" class="btn btn-primary inline" title="Add Supplier" data-arr="" href="#form_add_supplier">
                        <i class="fa fa-plus"></i> Add
                    </a>
                    <a class="btn btn-default inline btn-refresh" href="javascript:;">
                        <i class="fa fa-refresh"></i> Refresh Table
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-hover table-striped" id="tbl_supplier">
                    <thead>
                    <th>#</th>
                    <th>Suppliers Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Email Address</th>
                    <th>Products</th>
                    <th>Purchased Qty</th>
                    <th>Purchased Amt</th>
                    <th>Control</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/purchasing/supplier.js"></script>
<script type="text/javascript">
    SUPPLIER.list();
</script>
