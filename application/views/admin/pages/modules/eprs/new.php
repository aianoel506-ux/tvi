<div class="row">
    <div class="col-md-2">
        <h4><i class="fa fa-comment-o"></i> Remarks</h4>
        <hr>
        <div class="form-group">
            <textarea rows="10" name="remarks" class="form-control" placeholder="Enter justification here."></textarea>
        </div>
    </div>
    <div class="col-md-8">
        <div class="portlet light table">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-pin font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> Breakdown </span>
                    <span class="caption-helper">more details..</span>
                </div>

                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#portlet_tab3" data-toggle="tab"> Item(s)</a>
                    </li>
                    <li>
                        <a href="#portlet_tab2" data-toggle="tab">Documents</a>
                    </li>
                    <li class="">
                        <a href="#portlet_tab1" data-toggle="tab"> Notes </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body ">
                <div class="well">
                    <div class="row">
                        <div class="col-md-3">

                            Search Item
                            <div class="form-group">
                                <div class="input-group">
                                    <input class="form-control" placeholder="Item name / code" />
                                    <div class="input-group-btn">
                                        <button class="btn btn-default"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-3">

                            Brand
                            <input class="form-control" placeholder="Brand name.." readonly />
                        </div>
                        <div class="col-md-3">
                            Details
                            <input class="form-control" placeholder="Full details.." readonly />
                        </div>
                        <div class="col-md-3">
                            Price
                            <input class="form-control" placeholder="Full details.." readonly />
                        </div>
                    </div>
                    <div class="row margin-top-10">
                        <div class="col-md-3">

                            <label class="control-label">
                                Discount
                            </label>
                            <input class="form-control" placeholder="0.3 " />
                        </div>
                        <div class="col-md-6">

                            <label class="control-label">
                                Remarks
                            </label>
                            <input class="form-control" placeholder="Enter remarks.." />
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group pull-right margin-top-20">
                                <button class="btn btn-default">Reset</button>
                                <button class="btn btn-default">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover table-striped" id="tbl_po_items">
                    <thead>
                    <th>#</th>
                    <th>Particular</th>
                    <th>Brand</th>
                    <th>Dimension</th>
                    <th>Qty</th>
                    <th>Est Price</th>
                    <th>Remove</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>TSM-26-PD05 (Monocrystalline)</td>
                        <td>Panasonic</td>
                        <td>65"x39" 1.37" Depth</td>
                        <td>95</td>
                        <td class="number">2,421.05</td>
                        <td class="text-align-center"><i class="fa fa-times text-danger"></i></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <h4>Save as Template</h4>
            <div class="input-group">
                <input class="form-control" placeholder="Template name.." name="templatename" />
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-save"></i></button>
                </span>
            </div>
            <hr>
        </div>
        <div class="btn-group">
            <a href="#form_load_prs" data-toggle="ajax-modal" title="Load Saved / Templates" class="btn btn-default btn-block">
                <i class="fa fa-download"></i> Load Saved
            </a>
            <button class="btn btn-primary btn-block">
                Save to Draft
            </button>
            <button class="btn btn-success btn-block">
                Send for Approval
            </button>
            <button class="btn btn-danger btn-block">
                Discard
            </button>
        </div>
        <hr>
        <h4>Summary</h4>
        <ul class="list-group summary column">
            <li class="list-group-item">
                <span class="col-md-4">Total Qty</span>
                <span class="col-md-8 number font-blue-steel ">130</span>
            </li>
            <li class="list-group-item">
                <span class="col-md-4">Gross Amt</span>
                <span class="col-md-8 number font-blue-steel ">230,000.00</span>
            </li>
            <li class="list-group-item">
                <span class="col-md-4">VAT Total</span>
                    <span class="col-md-8 number font-blue-steel ">27,600.00</span>
            </li>
            <li class="list-group-item">
                <span class="col-md-4">Net Total</span>
                <span class="col-md-8 number font-blue-steel bold">202,400.00</span>
            </li>
        </ul>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/eprs/new.js"></script>
<script>
    /**
     * LOGIC: load the budgets approved so please refer to these tables `bos_capex_sp` and `bos_opex`. You will use the `transaction_id` if you want to
     * refer to the whole transaction or the `budget_data_id` if you want to refer to a budget only and the `job_order_id` if you want to refer to the permanent
     * job order of the budget.
     * Difference from the BOS new:
     * 1. There's no add budget.
     * 2. all available budgets and items were there but they're uneditable for CAPEX.
     * 3. An ability to add items inside a budget when using SP.
     * 4. An ability to add items inside an OPEX budget.
     * 5. An ability to delete newly added budgets.
     * Procedure:
     * 1. Load the approved budgets based on the chosen budget-type.
     */
    $('#tbl_po_items', document).DataTable();
</script>
