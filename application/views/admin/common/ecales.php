<?php
$ecales = ecales_id($dataid);
$ecales_id = ($ecales) ? $ecales->sysid : 0;
$flow_id = ($ecales) ? $ecales->flowid : 0;
?>
<?php if ($ecales) { ?>
<link rel="stylesheet" href="<?php echo base_url();?>/assets/global/plugins/icheck/skins/all.css">
    <div class="row">

        <div class="col-md-12">
            <div class="" style="padding-top: 25px !important;">
                <div class="row">
                    <div class="col-md-4 tabbable-line">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#ecales_items" data-toggle="tab" aria-expanded="true">Items</a>
                            </li>
                            <li class="">
                                <a href="#ecales_services" data-toggle="tab" aria-expanded="false">Services</a>
                            </li>
                            <li class="">
                                <a href="#ecales_summary" data-toggle="tab" aria-expanded="false">Summary</a>
                            </li>
                            <li class="">
                                <a href="#ecales_logs" data-toggle="tab" aria-expanded="false">Logs</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-8">
                        <?php if($ecales->status == 314) { ?>
                            <div class="note note-success note-bordered">
                                <a href="#form_ecales_revoke_request" title="Revoke Inventory" data-toggle="ajax-modal" data-arr="<?php echo $ecales_id.','.$flow_id;?>" class=""><i class="fa fa-refresh"></i> Request Revoke</a>
                                <p><strong><i class="fa fa-warning text-warning"></i> Accomplished</strong>: Inventory has been processed.</p>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn blue sbold" style="margin-top: 25px;" disabled>Template:</button>
                                        <button type="button" id="add_ecales_template" class="btn btn-outline blue sbold" style="margin-top: 25px;" ><i class="fa fa-plus"></i> Add</button>
                                        <a href="#tbl_ecales_templates" title="Load items from Template" data-toggle="ajax-modal" data-arr="<?php echo $ecales_id;?>" class="btn btn-outline blue sbold" style="margin-top: 25px;" ><i class="fa fa-download"></i> Load</a>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <form id="frm_process_ecales" method="post" action="<?php echo base_url(); ?>analysis/processecales">
                                        <input name="ecalesid" value="<?php echo $ecales_id; ?>" type="hidden" />
                                        <input name="appid" value="<?php echo $dataid; ?>" type="hidden" />
                                        <input name="origin" value="<?php echo $origin; ?>" type="hidden" />
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group" style="margin-right: 15px; margin-left: 15px;">
                                                    <!--
                                                    <input style="width: 30%; display:  inline-block;" required="required" class="form-control " id="rate_class_select" name="rateclass" />
                                                    -->
                                                    <div class="form-group" style="width: 39%; display:  inline-block;">
                                                        <label class="control-label">Total Load</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-edit"></i>
                                                            <input required="required" class="form-control " id="total_load" name="totalload" placeholder="Load" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="width: 60%; display:  inline-block;">
                                                        <label class="control-label">Remarks</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-edit"></i>
                                                            <input required="required" class="form-control " id="rate_class_remarks" name="remarks" placeholder="Remarks.." />
                                                        </div>
                                                    </div>
                                                    <span class="input-group-btn" style="vertical-align: top">
                                                    <button style="margin-top: 26px; margin-left: 2px;" type="submit" class="btn green-haze btn-outline sbold"><i class="fa fa-save fa-fw"></i> Process Request</button>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
                <div class="tab-content" style="padding-top: 15px !important;">
                    <div class="tab-pane active" id="ecales_items">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if($ecales->status == 314) { ?>
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-edit"></i>
                                                <span class="caption-subject font-green-sharp bold uppercase">ITEMS</span>
                                                <span class="caption-helper">details</span>
                                            </div>
                                            <div class="tools">
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <ul class="list-group summary column">
                                                <li class="list-group-item list-group-item-info">
                                                    <span class="col-md-5 label-name">Total Cost</span>
                                                    <span class="col-md-7 label-default number"><?php echo number_format($ecales->totalcost, 2);?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Total Qty</span>
                                                    <span class="col-md-7 label-default number"><?php echo number_format($ecales->totalqty, 2);?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Total Load</span>
                                                    <span class="col-md-7 label-default number"><?php echo number_format($ecales->totalload, 2);?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Remarks</span>
                                                    <span class="col-md-7 label-default number"><?php echo $ecales->remarks; ?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Processed By</span>
                                                    <span class="col-md-7 label-default number"><?php echo ($ecales->updatedby) ? get_users_info($ecales->updatedby)->lastname : 'N/A'; ?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Processed Date</span>
                                                    <span class="col-md-7 label-default number"><?php echo $ecales->dateupdated; ?></span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="portlet-footer">
                                            <button class="btn btn-default"><i class="fa fa-print fa-fw"></i> Print Inventory</button>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <form id="frm_add_item" action="<?php echo base_url('analysis/addecalesitem'); ?>" method="post">

                                        <input type="hidden" value="0" name="itemid" id="item_select" />
                                        <div class="portlet light portlet-fit portlet-form bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-edit"></i>
                                                    <span class="caption-subject font-green-sharp bold uppercase">Materials</span>
                                                    <span class="caption-helper">add item here</span>
                                                </div>
                                                <div class="tools">
                                                    <a href="#form_add_items" title="Add New Item(s)" data-toggle="ajax-modal" data-container="body" class=""><i class="fa fa-plus"></i> Add More</a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="form-body">
                                                    <div class="form-group row input-entry">
                                                        <div class="col-md-3">
                                                            <label class="control-label">Qty:</label>
                                                            <input id="input_qty" type="number" value="1" name="qty" placeholder="Qty.." class="form-control" onclick="this.select()">

                                                        </div>
                                                        <div class="col-md-9">

                                                            <label for="item_select">Select item(s)</label>
                                                            <div class="input-group">
                                                                <input class="form-control input-reset" id="item_search" placeholder="Search Item.." required name="itemtext" />
                                                                <span class="input-group-btn">
                                                    <button class="btn blue btn-outline sbold"><i class="fa fa-plus"></i> Save <i class="fa fa-sign-out"></i></button>
                                                </span>
                                                            </div>
                                                            <input type="hidden" class="form-control input-reset" id="quoteid" required name="quoteid" />
                                                            <input type="hidden" class="form-control" id="ecalesid" required name="ecalesid" value="<?php echo $ecales_id; ?>" />
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
                                                </div>

                                                <div class="form-actions">
                                                    <div class="note note-info">
                                                        <strong>Note:</strong> if item cannot be found on the search box, use <i class="fa fa-plus"></i> Add More button to add more items in the database.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                <?php } ?>
                            </div>
                            <div class="col-md-8">
                                <div class="portlet light table bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-edit"></i>
                                            <span class="caption-subject font-green-sharp bold uppercase">ITEM LIST</span>
                                            <span class="caption-helper">
                                                <b>INVENTORY</b> No:<span id="ecales_number"  class="number text-bold text-success margin-left-10" style="margin-left: 10px;">0</span>
                                            </span>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" data-id="<?php echo $ecales_id;?>" class="btn btn-danger inline" id="btn_clear_all"><i class="fa fa-times"></i> Clear</a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <hr style="margin-top: 5px; margin-bottom: 10px;">
                                        <div class="col-md-8 pull-left">
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fa fa-question-circle text-info"></i> Check if item is provided by customer.
                                        </div>
                                        <table class="table table-bordered table-hover table-striped table-condensed table-striped" id="tbl_ecales">
                                            <thead>
                                            <th><i class="fa fa-reorder pull-left"></i> </th>
                                            <th>Item Name</th>
                                            <th>Amount</th>
                                            <th>Qty</th>
                                            <th>Stock(s)</th>
                                            <th>Total</th>
                                            <th width="60px">Cust <a href="javascript:;" id="cprovided" data-toggle="tooltip" title="Check if customer provided."><i class="fa fa-question-circle"></i></a></th>
                                            <th width="60px">Control</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                        <div class="portlet-footer">
                                            <div class="row" style="padding: 0 15px !important;">
                                                <h4 class="text-danger bold" style="padding-left: 15px !important;">Total Quantity and Amounts</h4>
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label class="bold">Total</label>
                                                        <ul class="list-group summary column " style="padding-bottom: 0px !important; margin-bottom: 10px !important;">
                                                            <li class="list-group-item">
                                                                <span class="col-md-4 label-name">Amount</span>
                                                                <span class="col-md-8 label-default number" id="ecales_amt_total">0.00</span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <span class="col-md-4 label-name">Quantity</span>
                                                                <span class="col-md-8 label-default number" id="ecales_amt_qty">0</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <!--<div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label class="bold">Customer</label>
                                                        <ul class="list-group summary column " style="padding-bottom: 0px !important; margin-bottom: 10px !important;">
                                                            <li class="list-group-item">
                                                                <span class="col-md-4 label-name">Amount</span>
                                                                <span class="col-md-8 label-default number" id="ecales_cust_amt">0.00</span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <span class="col-md-4 label-name">Quantity</span>
                                                                <span class="col-md-8 label-default number" id="ecales_cust_qty">0</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>-->
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label class="bold">Customer Payable Amount</label>
                                                        <ul class="list-group summary column " style="padding-bottom: 0px !important; margin-bottom: 10px !important;">
                                                            <li class="list-group-item">
                                                                <span class="col-md-4 label-name">Amount</span>
                                                                <span class="col-md-8 label-default number" id="ecales_cust_amt">0.00</span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <span class="col-md-4 label-name">Quantity</span>
                                                                <span class="col-md-8 label-default number" id="ecales_cust_qty">0</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ecales_services">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if($ecales->status == 314) { ?>
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-edit"></i>
                                                <span class="caption-subject font-green-sharp bold uppercase">Services</span>
                                                <span class="caption-helper">details</span>
                                            </div>
                                            <div class="tools">

                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <ul class="list-group summary column">
                                                <li class="list-group-item list-group-item-info">
                                                    <span class="col-md-5 label-name">Total Cost</span>
                                                    <span class="col-md-7 label-default number"><?php echo number_format($ecales->totalcost, 2);?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Total Qty</span>
                                                    <span class="col-md-7 label-default number"><?php echo number_format($ecales->totalqty, 2);?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Total Load</span>
                                                    <span class="col-md-7 label-default number"><?php echo number_format($ecales->totalload, 2);?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Remarks</span>
                                                    <span class="col-md-7 label-default number"><?php echo $ecales->remarks; ?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Processed By</span>
                                                    <span class="col-md-7 label-default number"><?php echo ($ecales->updatedby) ? get_users_info($ecales->updatedby)->lastname : 'N/A'; ?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-5 label-name">Processed Date</span>
                                                    <span class="col-md-7 label-default number"><?php echo $ecales->dateupdated; ?></span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="portlet-footer">
                                            <button class="btn btn-default"><i class="fa fa-print fa-fw"></i> Print Inventory</button>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <form id="frm_add_service" action="<?php echo base_url('analysis/addecalesservice'); ?>" method="post">

                                        <input type="hidden" value="0" name="serviceid" id="svcs_select" />
                                        <div class="portlet light portlet-fit portlet-form bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-edit"></i>
                                                    <span class="caption-subject font-green-sharp bold uppercase">Services</span>
                                                    <span class="caption-helper">add service here</span>
                                                </div>
                                                <div class="tools">
                                                    <a href="#form_add_service" title="Add New Service(s)" data-toggle="ajax-modal" class=""><i class="fa fa-plus"></i> Add</a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="form-body">
                                                    <div class="form-group row input-entry">
                                                        <div class="col-md-3">
                                                            <label class="control-label">Days:</label>
                                                            <input id="svcs_days" type="number" value="1" name="days" placeholder="Qty.." class="form-control" step="0.5" min="0.5">

                                                        </div>
                                                        <div class="col-md-9">

                                                            <label for="item_select">Select service(s)</label>
                                                            <div class="input-group">
                                                                <input class="form-control input-reset" id="svcs_search" placeholder="Search Services.." required name="servicetext" />
                                                                <span class="input-group-btn">
                                                                    <button class="btn blue btn-outline sbold"><i class="fa fa-plus"></i> Save <i class="fa fa-sign-out"></i></button>
                                                                </span>
                                                            </div>
                                                            <input type="hidden" class="form-control" id="ecalesid" required name="ecalesid" value="<?php echo $ecales_id; ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group ">
                                                        <hr>
                                                        <label for="item_select">Last Price Details</label>
                                                        <ul class="list-group summary column " style="padding-bottom: 0px !important; margin-bottom: 10px !important;">
                                                            <li class="list-group-item"><span class="col-md-4 label-name">Price</span><span class="col-md-8 label-default number" id="svcs_lastprice">N/A</span> </li>
                                                            <li class="list-group-item"><span class="col-md-4 label-name">Date</span><span class="col-md-8 label-default number" id="svcs_lastdate">N/A</span> </li>
                                                            <li class="list-group-item"><span class="col-md-4 label-name">Total</span><span class="col-md-8 label-default number" style="color: red !important;" id="svcs_itemtotal">0.00</span></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="form-actions">
                                                    <div class="note note-info">
                                                        <strong>Note:</strong> if item cannot be found on the search box, use <i class="fa fa-plus"></i> Add More button to add more items in the database.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                <?php } ?>
                            </div>
                            <div class="col-md-8">
                                <div class="portlet light table bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-edit"></i>
                                            <span class="caption-subject font-green-sharp bold uppercase">ITEM LIST</span>
                                            <span class="caption-helper">
                                                <b>Inventory</b> No:<span id="ecales_number"  class="number text-bold text-success margin-left-10" style="margin-left: 10px;">0</span>
                                            </span>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" data-id="<?php echo $ecales_id;?>" class="btn btn-danger inline" id="btn_clear_all"><i class="fa fa-times"></i> Clear</a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <hr style="margin-top: 5px; margin-bottom: 10px;">
                                        <div class="col-md-8 pull-left">
                                        </div>
                                        <table class="table table-bordered table-hover table-striped table-condensed table-striped" id="tbl_ecales_service">
                                            <thead>
                                            <th><i class="fa fa-reorder pull-left"></i> </th>
                                            <th>Service(s)</th>
                                            <th>Amount</th>
                                            <th>Days</th>
                                            <th>Total</th>
                                            <th>Control</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                        <div class="portlet-footer">
                                            <div class="row" style="padding: 0 15px !important;">
                                                <div class="col-md-8">
                                                    <h4 class="text-danger bold" style="padding-left: 15px !important;">Total Payable Amounts: <span class="text-info number" id="ecales_service_amt">0.00</span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ecales_summary">
                        <div class="portlet light table bordered">
                            <div class="portlet-title">
                                <div class="caption col-md-6">
                                    <i class="fa fa-edit"></i>
                                    <span class="caption-subject font-green-sharp bold uppercase">Inventory ITEMS AND SERVICES SUMMARY</span>
                                    <span class="caption-helper"></span>
                                </div>
                                <div class="portlet-body">
                                    <table width="100%" class="table table-bordered table-hover table-striped table-condensed table-striped" id="tbl_ecales_summary">
                                        <thead>
                                        <th><i class="fa fa-bars"></i></th>
                                        <th>Type</th>
                                        <th>Item</th>
                                        <th>Amount</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Total</th>
                                        <th>Provided by</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="portlet-footer">
                                    <h3 class="bold text-danger">Amounts Payable by Customer</h3>
                                    <div class="form-group col-md-4">
                                        <h4 class="bold">Utilities & Services</h4>
                                        <ul class="list-group summary column col-md-12" style="pdding-bottom: 0px !important; margin-bottom: 10px !important;">
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name">Amount</span>
                                                <span class="col-md-6 label-default number" id="summary_util_amt">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name">VAT(+12%)</span>
                                                <span class="col-md-6 label-default number" id="summary_util_vat">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name text-danger bold">Total</span>
                                                <span class="col-md-6 label-default number" id="summary_util_total">0.00</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <h4 class="bold">Items</h4>
                                        <ul class="list-group summary column col-md-12" style="pdding-bottom: 0px !important; margin-bottom: 10px !important;">
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name">Amount(Ex-VAT)</span>
                                                <span class="col-md-6 label-default number" id="summary_items_amt">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name">VAT(Inc.)</span>
                                                <span class="col-md-6 label-default number" id="summary_items_vat">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name text-danger bold">Total</span>
                                                <span class="col-md-6 label-default number" id="summary_items_total">0.00</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <h4 class="bold">Total</h4>
                                        <ul class="list-group summary column col-md-12" style="pdding-bottom: 0px !important; margin-bottom: 10px !important;">
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name">Total Amount</span>
                                                <span class="col-md-6 label-default number" id="summary_total_amt">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name">Total VAT</span>
                                                <span class="col-md-6 label-default number" id="summary_total_vat">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name text-danger bold">Grand Total</span>
                                                <span class="col-md-6 label-default number" id="summary_grand_total">0.00</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ecales_logs">
                        <div class="portlet light table bordered">
                            <div class="portlet-title">
                                <div class="caption col-md-6">
                                    <i class="fa fa-copy"></i>
                                    <span class="caption-subject font-green-sharp bold uppercase">REVOKED Inventory LOGS</span>
                                    <span class="caption-helper"></span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table width="100%" class="table table-bordered table-hover table-striped table-condensed table-striped" id="tbl_revoked_ecales_logs">
                                    <thead>
                                        <th width="5%"><i class="fa fa-th-list"></i></th>
                                        <th width="10%">Total Loads</th>
                                        <th width="10%">Total Cost</th>
                                        <th width="10%">Total Qty</th>
                                        <th width="20%">Remarks</th>
                                        <th width="15%">Inspection & Design</th>
                                        <th width="24%">Reason</th>
                                        <th width="5%"><i class="fa fa-paperclip"></i> </th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="portlet-footer">
                            </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php } else { ?>
    <hr>
    <div class="m-heading-1 border-yellow m-bordered">
        <h3><strong><i class="fa fa-warning text-warning"></i> Attention</strong>: This application is not applicable to Inventory transaction.</h3>
    </div>
<?php } ?>
<script src="<?php echo base_url(); ?>/assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>/assets/pages/ecales.js" type="text/javascript"></script>
<script type="text/javascript">
    ECALES.analysis(<?php echo $ecales_id;?>,<?php echo $dataid;?>);
</script>