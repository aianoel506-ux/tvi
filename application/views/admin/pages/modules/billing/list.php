<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.searchHighlight.css" rel="stylesheet" type="text/css" />


                <div class="portlet light box table">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Billing</span>
                            <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <form class="" id="frm_filter_billing" method="post" action="<?php echo base_url('billing/getbillingtrn'); ?>">
                                <div class="col-md-12 margin-top-10" style="padding: 5px 20px !important;">

                                    <div class="input-group pull-left col-md-9">
                                        <span class="input-group-addon"><i class="fa fa-search"></i> Filter </span>
                                        <input required name="year" style="width: 20%;" id="select_year" class="form-control" placeholder="Year" />
                                        <input required name="month" style="width: 40%;" id="select_month" class="form-control" placeholder="Month" />
                                        <input required name="gdlbid" style="width: 40%;" id="select_gdlb" class="form-control" placeholder="Select GDLB" />
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Preview</button>
                                        <button type="button" id="billing_email" class="btn btn-default"><i class="fa fa-envelope"></i> Re-Send eBills</button>
                                        <button type="button" id="billing_print" class="btn btn-default"><i class="fa fa-print"></i> Print Bills</button>
                                        </span>
                                    </div>
                                    <div class="btn-group pull-right" id="billing_btn">
                                        <button type="button" id="billing_register"  class="btn btn-primary"><i class="fa fa-print"></i> Print Register</button>
                                        <button type="button" id="btn_close_billing" class="btn btn-danger"><i class="fa fa-sign-out"></i> Close Billing</button>
                                    </div>

                                </div>
                            </form>
                            
                            <div class="col-md-12 margin-top-10">
                                <table class="table table-condensed table-hover table-bordered table-hover table-striped tbl-sm" id="tbl_billing_entry">
                                    <thead>
                                    <th width="20px"></th>
                                    <th width="30px"><i class="fa fa-reorder"></i></th>
                                    <th width="30px">Bill No.</th>
                                    <th width="18px">Servno</th>
                                    <th width="10px">MTR</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th width="65px">Due</th>
                                    <th width="65px">Previous</th>
                                    <th width="65px">Surcharge</th>
                                    <th width="65px">Current</th>
                                    <th width="75px">Total</th>
                                    <th width="35px">Status</th>
                                    <th width="35px">eBill</th>
                                    <th width="50px"><i class="fa fa-wrench"></i></th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>

                            <div class="col-md-12" style="padding: 30px 30px;">
                                <hr>
                                    <ul class="list-group summary column table ">
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-4">Customer:</span>
                                            <span class=" data col-md-8 text-align-right" id="ar_total">0</span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-4">Total Generation:</span>
                                            <span class=" data col-md-8 text-align-right" id="ar_total">0.00</span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-4">Total AR Amount:</span>
                                            <span class=" data col-md-8 text-align-right" id="ar_total">0.00</span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-4">Total Previous:</span>
                                            <span class=" data col-md-8 text-align-right" id="ar_total">0.00</span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-4">Total Current:</span>
                                            <span class=" data col-md-8 text-align-right" id="ar_total">0.00</span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-4">Total Interest:</span>
                                            <span class=" data col-md-8 text-align-right" id="ar_total">0.00</span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-4">Total VAT:</span>
                                            <span class=" data col-md-8 text-align-right" id="ar_total">0.00</span>
                                        </li>
                                    </ul>
                            </div>
                        </div>
                    </div>
                </div>
         
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>



<script src="<?php echo base_url(); ?>assets/pages/billing/main.js" type="text/javascript"></script>
<script>
    BILLING.billinginquiry();
</script>