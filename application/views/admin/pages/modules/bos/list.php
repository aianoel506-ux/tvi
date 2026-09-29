
<form id="frm_budget_list" action="<?php echo base_url('bos/submitbudget'); ?>" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder font-yellow-casablanca"></i>
                        <span class="caption-subject uppercase">Budget List: <span id="budgetlabel" class="font-yellow-casablanca bold uppercase">None</span></span>
                    </div>
                    <div class="actions">
                            <div class="input-group" id="filters" style="margin-left: -15px;">
                                <input style="display: inline-block; width: 270px;" id="select2ccid" name="ccid" class="form-control" />
                                <input style="display: inline-block; width: 30%;" id="select2budgettype" name="types" class="form-control" />
                                <input style="display: inline-block; width: 20%;" id="year" name="year" class="form-control" type="text" placeholder="Year" value="<?php echo date('Y'); ?>" />
                                <div class="input-group-btn">
                                    <a id="frm_new_budget" href="frm_new_budget" title="New Budget Creation" data-toggle="ajax-modal" data-arr="" data-view=""  class="btn sbold uppercase btn-outline yellow-casablanca"><i class="fa fa-file-o"></i> New</a>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="portlet-body" >

                    <table style="widt: 100%;" id="tbl_budget_list" class="table table-hover table-condensed table-bordered table-striped tbl-sm">
                        <thead>
                        <tr>
                            <th rowspan="2"></th>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Codes</th>
                            <th rowspan="2">Descriptions</th>
                            <th rowspan="2">Account Code</th>
                            <th rowspan="2">Prev. Amount</th>
                            <th rowspan="2">Approved Amount</th>
                            <th rowspan="2">Item(s)</th>
                            <th colspan="2" class="info">Adjustments</th>
                            <th rowspan="2">Expenses</th>
                            <th rowspan="2">Balance</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2"></th>
                            <th rowspan="2"></th>
                        </tr>

                        <tr>

                            <th>Amt(+)</th>
                            <th>Amt(-)</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <ul class="list-group summary column table margin-top-20">
                    <li class="list-group-item">
                        <span class="col-md-6 label-name">Item(s)</span>
                        <span class="col-md-6 label-default number" id="total_item">0</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-6 label-name">Total Amt.</span>
                        <span class="col-md-6 label-default number" id="total_amt">0</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-6 label-name">Total Exp.</span>
                        <span class="col-md-6 label-default number" id="total_exp">0</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-6 label-name">Total Bal.</span>
                        <span class="col-md-6 label-default number" id="total_bal">0</span>
                    </li>
                </ul>

                <hr>
                <div class="form-group input-group input-icon">
                    <i class="fa fa-comment font-yellow-casablanca"></i>
                    <input class="form-control border-yellow-casablanca" name="remarks" placeholder="Remarks.." />
                    <span class="input-group-btn">
                         <button class="btn yellow-casablanca""><i class="fa fa-send-o"></i> Send For Approval</button>
                    </span>
                </div>

            </div>

        </div>
    </div>
    </div>
</form>

<script src="<?php echo base_url(); ?>assets/pages/bos/bos.js"></script>
<script>
    BOS.init();
</script>
