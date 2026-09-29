<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/FixedColumns/css/dataTables.fixedColumns.css"/>


        <div class="portlet box tabbable">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-green-sharp bold uppercase">User View</span>
                    <span class="caption-helper"> as of <?php echo date('F d, Y'); ?></span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="<?php echo $main_office; ?>"><a style="font-size: 18px; padding-left: 20px; padding-right: 20px;" href="#moTab" data-toggle="tab"><i class="fa fa-tag fa-fw"></i>Main Office</a></li>
                    <li class="<?php echo $operation; ?>"><a style="font-size: 18px; padding-left: 20px; padding-right: 20px;" href="#opTab" data-toggle="tab"><i class="fa fa-tag fa-fw"></i>Operations</a></li>
                </ul>
            </div>  
            <div class="portlet-body">
                <!--div class="caption">
                    <span class="caption-subject font-green-sharp bold uppercase">Budget Summary</span>
                    <span class="caption-helper">Per Cost Center</span>
                </div>
                <hr>
                <table class="table table-hover table-condensed table-striped table-bordered tbl-sm margin-top-10 table_budget" id="budgetSummary">
                    <thead>
                        <tr>
                            <th rowspan="2">Cost Center</th>
                            <th rowspan="2">Budget Type</th>
                            <th rowspan="2">Account Code</th>
                            <th rowspan="2">Previous Budget</th>
                            <th rowspan="2">Proposed Budget <?php echo date('Y'); ?></th>
                            <th colspan="2" style="text-align: center">Variance INCREASE(DECREASE)</th>
                            <th rowspan="2">Control</th>
                        </tr>
                        <tr>
                            <th>AMOUNT</th>
                            <th>Percent</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <!--tfoot class="emphasize" id="budget_summary_footer">
                    <td></td>
                    <td colspan="2">Total Budget</td>
                    <td class="number prevtotal"></td>
                    <td class="number projtotal"></td>
                    <td class="number proptotal" id="currbudget_"></td>
                    <td class="number amttotal"></td>
                    <td class="number percenttotal">%</td>
                    <td></td>
                    </tfoot>
                </table>
                <hr-->
                <div class="tab-content">
                    <div class="tab-pane fade in <?php echo $operation; ?>" id="opTab">
                        <?php //TODO 1 hide this dynamically pls. 
                        ?>
                        <!--<a class="btn btn-info btn-xs">View All</a><a class="btn btn-default btn-xs">100</a><a class="btn btn-default btn-xs">102</a><a class="btn btn-default btn-xs">103</a>-->
                        <div class="opCapex">
                            <div class="caption">
                                <h4 class="caption-subject font-green-sharp bold uppercase">Operations</h4>
                                <span class="caption-helper"></span>
                            </div>
                            <hr>
                            <div class="caption">
                                <span class="caption-subject font-green-sharp bold uppercase">CAPEX</span>
                                <span class="caption-helper">Capitalize Expenditure</span>
                            </div>
                            <table class="table table-hover table-condensed table-striped table-bordered tbl-sm table_budget" id="opcapextable" btype="76" cctype="0">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Expand</th>
                                        <th rowspan="2">COST Center</th>
                                        <th rowspan="2">Budget Description</th>
                                        <th rowspan="2">Proposed Budget <?php echo date('Y'); ?></th>
                                        <th rowspan="2">Last Year's Budget</th>
                                        <th colspan="2" style="text-align: center">Variance INCREASE(DECREASE)</th>
                                        <th rowspan="2">Control</th>
                                    </tr>
                                    <tr>
                                        <th>AMOUNT</th>
                                        <th>Percent</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="emphasize" id="tfootopcapex">
                                    <td colspan="3">Total Capex (Operations)</td>
                                    <td class="number proptotal" id="currbudget_"></td>
                                    <td class="number prevtotal"></td>
                                    <td class="number amttotal"></td>
                                    <td class="number percenttotal">%</td>
                                    <td></td>
                                </tfoot>
                            </table>
                            <hr>
                        </div>
                        <div class="opSp">
                            <div class="caption">
                                <span class="caption-subject font-green-sharp bold uppercase">SP</span>
                                <span class="caption-helper">Special Projects</span>
                            </div>
                            <table class="table table-hover table-condensed table-striped table-bordered tbl-sm table_budget" id="opsptable" btype="78" cctype="0">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Expand</th>
                                        <th rowspan="2">COST Center</th>
                                        <th rowspan="2">Description</th>
                                        <th rowspan="2">Proposed Budget <?php echo date('Y'); ?></th>
                                        <th rowspan="2">Last Year's Budget</th>
                                        <th colspan="2" style="text-align: center">Variance INCREASE(DECREASE)</th>
                                        <th rowspan="2">Control</th>
                                    </tr>
                                    <tr>
                                        <th>AMOUNT</th>
                                        <th>Percent</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="emphasize" id="tfootopsp">
                                <td colspan="3">Total SP (Operations)</td>
                                <td class="number proptotal"></td>
                                <td class="number prevtotal"></td>
                                <td class="number amttotal"></td>
                                <td class="number percenttotal">%</td>
                                <td></td>
                                </tfoot>
                            </table>
                        </div>
                        <div class="opOpex">
                                <div class="caption">
                                    <span class="caption-subject font-green-sharp bold uppercase">OPEX</span>
                                    <span class="caption-helper">Operation Expenditure</span>
                                </div>
                                <hr>
                                <table class="table table-hover table-condensed table-striped table-bordered tbl-sm margin-top-10 table_budget" id="opopextable" btype="77" cctype="0">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Collapse/Extend</th>
                                            <th rowspan="2">Cost Center</th>
                                            <th rowspan="2">Account Code</th>
                                            <th rowspan="2">Last Year's Budget</th>
                                            <th rowspan="2">Projected Expense</th>
                                            <th rowspan="2">Proposed Budget <?php echo date('Y'); ?></th>
                                            <th colspan="2" style="text-align: center">Variance INCREASE(DECREASE)</th>
                                            <th rowspan="2">Control</th>
                                        </tr>
                                        <tr>
                                            <th>AMOUNT</th>
                                            <th>Percent</th>
                                        </tr>
                                    </thead>
                                    <tfoot class="emphasize" id="tfootopopex">
                                    <td></td>
                                        <td colspan="2">Total Budget (Operations)</td>
                                        <td class="number prevtotal"></td>
                                        <td class="number projtotal"></td>
                                        <td class="number proptotal" id="currbudget_"></td>
                                        <td class="number amttotal"></td>
                                        <td class="number percenttotal">%</td>
                                    <td></td>
                                    </tfoot>
                                </table>
                            </div>
                    </div>
                    <div class="tab-pane fade in <?php echo $main_office; ?>" id="moTab">
                        <?php //TODO 2 hide this dynamically pls. 
                        ?>
                        <div class="moCapex">
                            <div class="caption">
                                <h4 class="caption-subject font-green-sharp bold uppercase">Main Office</h4>
                                <span class="caption-helper"></span>
                            </div>
                            <hr>
                            <div class="caption">
                                <span class="caption-subject font-green-sharp bold uppercase">CAPEX</span>
                                <span class="caption-helper">Capitalize Expenditure</span>
                            </div>
                            <table class="table table-hover table-condensed table-striped table-bordered tbl-sm table_budget" id="mocapextable" btype="76" cctype="1">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Expand</th>
                                        <th rowspan="2">COST Center</th>
                                        <th rowspan="2">Description</th>
                                        <th rowspan="2">Proposed Budget <?php echo date('Y'); ?></th>
                                        <th rowspan="2">Last Year's Budget</th>
                                        <th colspan="2" style="text-align: center">Variance INCREASE(DECREASE)</th>
                                        <th rowspan="2">Control</th>
                                    </tr>
                                    <tr>
                                        <th>AMOUNT</th>
                                        <th>Percent</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="emphasize" id="tfootmocapex">
                                <td colspan="3">Total CAPEX (Main Office)</td>
                                <td class="number proptotal" id="currbudget_"></td>
                                <td class="number prevtotal"></td>
                                <td class="number amttotal"></td>
                                <td class="number percenttotal">%</td>
                                <td></td>
                                </tfoot>
                            </table>
                            <hr>
                        </div>
                        <div class="moSp">
                            <div class="caption">
                                <span class="caption-subject font-green-sharp bold uppercase">SP</span>
                                <span class="caption-helper">Special Projects</span>
                            </div>
                            <table class="table table-hover table-condensed table-striped table-bordered tbl-sm table_budget" id="mosptable" btype="78" cctype="1">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Expand/Collapse Budget</th>
                                        <th rowspan="2">COST Center</th>
                                        <th rowspan="2">Description</th>
                                        <th rowspan="2">Proposed Budget <?php echo date('Y'); ?></th>
                                        <th rowspan="2">Last Year's Budget</th>
                                        <th colspan="2" style="text-align: center">Variance INCREASE(DECREASE)</th>
                                        <th rowspan="2">Control</th>
                                    </tr>
                                    <tr>
                                        <th>AMOUNT</th>
                                        <th>Percent</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="emphasize" id="tfootmosp">
                                <td colspan="3">Total SP (Main Office)</td>
                                <td class="number proptotal" id="currbudget_"></td>
                                <td class="number prevtotal"></td>
                                <td class="number amttotal"></td>
                                <td class="number percenttotal">%</td>
                                <td></td>
                                </tfoot>
                            </table>
                            <hr>
                        </div>
                        <div class="moOpex">
                            <div class="caption">
                                <span class="caption-subject font-green-sharp bold uppercase">OPEX</span>
                                <span class="caption-helper">Operation Expenditure</span>
                            </div>
                            <hr>
                            <table class="table table-hover table-condensed table-striped table-bordered tbl-sm margin-top-10 table_budget" id="moopextable" btype="77" cctype="1">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Collapse/Extend</th>
                                        <th rowspan="2">Cost Center</th>
                                        <th rowspan="2">Account Code</th>
                                        <th rowspan="2">Last Year's Budget</th>
                                        <th rowspan="2">Projected Expense</th>
                                        <th rowspan="2">Proposed Budget <?php echo date('Y'); ?></th>
                                        <th colspan="2" style="text-align: center">Variance INCREASE(DECREASE)</th>
                                        <th rowspan="2">Control</th>
                                    </tr>
                                    <tr>
                                        <th>AMOUNT</th>
                                        <th>Percent</th>
                                    </tr>
                                </thead>
                                <tfoot class="emphasize" id="tfootmoopex">

                                <td></td>
                                <td colspan="2">Total Budget (Main Office)</td>
                                <td class="number prevtotal"></td>
                                <td class="number projtotal"></td>
                                <td class="number proptotal" id="currbudget_"></td>
                                <td class="number amttotal"></td>
                                <td class="number percenttotal">%</td>
                                <td></td>
                                </tfoot>
                            </table>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-md-4">
                                <div class="portlet table light">
                                    <div class="portlet-body">
                                        <ul class="list-group summary no-border list-group-sm">
                                            <li class="list-group-item">
                                                <div class="caption">
                                                    <span class="caption-subject font-green-sharp bold uppercase">Summary (Operations)</span>
                                                    <span class="caption-helper"></span>
                                                </div>
                                            </li>
                                            <li class="list-group-item"> Total Proposed Budget <span class="label label-default pull-right" id="total_prop_bud">0</span> </li>
                                            <li class="list-group-item"> Previously Approved Budget (<span class="prev_year">0</span>): <span class="label label-default pull-right" id="prev_budget">0</span> </li>
                                            <li class="list-group-item"> Variance (Increase/Decrease): <span class="label label-default pull-right" id="bud_variance">0</span> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="portlet table light">
                                    <div class="portlet-body">
                                        <ul class="list-group summary no-border list-group-sm">
                                            <li class="list-group-item"><div class="caption">
                                                    <span class="caption-subject font-green-sharp bold uppercase">Summary (Main Office)</span>
                                                    <span class="caption-helper"></span>
                                                </div> </li>
                                            <li class="list-group-item"> Total Proposed Budget <span class="label label-default pull-right" id="mo_prop_budget">0</span> </li>

                                            <li class="list-group-item"> Previously Approved Budget (<span class="prev_year">0</span>): <span class="label label-default pull-right" id="mo_prev_bud">0</span> </li>
                                            <li class="list-group-item"> Variance (Increase/Decrease): <span class="label label-default pull-right" id="mo_var">0</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 pull-right">
                                <div class="portlet table light">
                                    <div class="portlet-body">
                                        <ul class="list-group summary no-border list-group-sm">
                                            <li class="list-group-item">
                                                <div class="caption">
                                                    <span class="caption-subject font-green-sharp bold uppercase">Summary Grand Total</span>
                                                    <span class="caption-helper"></span>
                                                </div> 
                                            </li>
                                            <li class="list-group-item"> Total Amount Proposed: <span class="label label-default pull-right" id="daterange">0</span> </li>
                                            <li class="list-group-item"> Total Amount Approved: <span class="label label-default pull-right" id="totalut">0</span> </li>
                                            <li class="list-group-item"> Variance (Increase/Decrease): <span class="label label-default pull-right" id="totallate">0</span> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div>
            <?php echo (isset($recipientSelection)) ? $recipientSelection : ''; ?>
            <input type="hidden" id="transaction_id" value="<?php echo $bos_transaction_id; ?>"/>
            <input type="hidden" id="branch_id" value="<?php echo (isset($branch_id)) ? $branch_id : false; ?>"/>

            <input type="hidden" id="currbudget_" value="0.00" />
            <input type="hidden" id="hid_total_prop_bud" value="0" />
            <input type="hidden" id="hid_prev_appr_bud" value="0" />
            <input type="hidden" id="hid_var" value="0" />
        </div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script> 

<!--Additional Scripts for testing-->
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.jeditable.mini.js"></script><!--The script below is for editable dataTable plugin-->
<script src="http://cdn.datatables.net/keytable/2.1.2/js/dataTables.keyTable.min.js"></script><!--The script below is for keys dataTable plugin-->

<script src="<?php echo base_url(); ?>assets/pages/bos/bos.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        BOS.capex();
        //data the needs to be initialized.
        $('#hid_total_prop_bud').val(0);
        $('#hid_prev_appr_bud').val(0);
        $('#hid_var').val(0);

        /*$("body").on("click", function (a) {
         $('[data-toggle="popover"]').each(function () {
         $(this).is(a.target) || 0 !== $(this).has(a.target).length || 0 !== $(".popover").has(a.target).length || $(this).popover("hide");
         });
         });*/

        /**
         * Change the row of the disapproved budget to red. Remove the redness when the disapproval is revoked.
         * @param {jquery-object} buttonId
         * @param {tynyint} statusValue
         * @return {none} this does not return anything as this just modifies the row-color that contains the clicked button.
         */
        function approvalIndicator(buttonId, statusValue) {
            var buttonRow = buttonId.closest("tr");
            if (statusValue == '0' || statusValue == 0) {
                if (!buttonRow.hasClass("danger")) {
                    buttonRow.addClass("danger");
                }
            } else {
                if (buttonRow.hasClass("danger")) {
                    buttonRow.removeClass("danger");
                }
                //$(buttonId).closest("tr").find('td').each(function(){ $(this).addClass("success"); });
            }
        }
        /**
         * When the budget is disapproved, the button will turn green with Revoke value. When the budget is not disapproved the button turns into a red "Disapprove" button.
         * @param {jquery-object} buttonId
         * @param {tinyint} statusValue
         * @return {none} this function only modifies the color and the value of a button.
         */
        function buttonChangeColorAndValue(buttonId, statusValue) {
            //buttonId = '#' + buttonId;
            //alert(buttonId+" statusValue:"+statusValue);
            if (statusValue == '1' || statusValue == 1) {
                if (buttonId.hasClass("btn-success")) {
                    buttonId.removeClass("btn-success");
                }
                buttonId.addClass("btn-danger");
                buttonId.val("Disapprove");
            } else {
                if (buttonId.hasClass("btn-danger")) {
                    buttonId.removeClass("btn-danger");
                }
                buttonId.addClass("btn-success");
                buttonId.val("Revoke");
            }
        }
        $('body').on('click', '.toggleApproval', function () {
            var button_clicked = $(this);
            var budgetDataId_buttonId = button_clicked.attr('id');
            var status = toggleBudget(budgetDataId_buttonId);
            approvalIndicator(button_clicked, status);
            buttonChangeColorAndValue(button_clicked, status);
        });
        /**
         * When called, this function updates the status of the budget using the budgetDataId..
         * @param {int} budgetDataId This is the ID of the budget that we want to modify.
         * @return {d.status} Returns the resulting status value.
         */
        function toggleBudget(budgetDataId) {
            var statusValue = null;
            var branch_id = $('#branch_id').val();
            $.ajax({
                async: false, //we set async to false in order to wait this ajax to finish and return the value that the next function with consume.
                url: PECO.base_url() + 'bos/perBudgetDisapproval/' + branch_id,
                type: 'post',
                data: {'budgetDataId': budgetDataId},
                dataType: 'json',
                success: function (d) {
                    statusValue = d.budgetStatus;
                    //console.log("status:" + statusValue + " d.msg:" + d.msg + " func:" + d.func);
                    //PECO.initAlerts(d.msg, 'Information', d.func);
                    //PECO.initAlerts(d.logmsg, 'Information', d.insert_status);
                },
                error: function (d) {
                    //console.log("Error: perBudgetDisapproval, status:" + d.budgetStatus + " d.msg:" + d.msg + " d.func:" + d.func + " d.bdataid:" + d.bdataid + " d:" + JSON.stringify(d));
                    //PECO.PhpError();
                    PECO.initAlerts('perBudgetDisapproval ajax encountered an error.', 'Information', 'warning');
                    PECO.initAlerts(d.logmsg, 'Information', d.insert_status);
                    //alert(d.toSource());
                }
            });
            return statusValue;
        }

        //TODO createdatatable the approval and disapproval indicator should change depending on the current status of the request after first loading.
        function createdatatable(tableid, tfootid) {
            //TODO createdatatable view only the budgets under cost center on branch viewing.
            //TODO proposed budget randomly generated?
            var btype = tableid.attr('btype');
            
            var cctype = tableid.attr('cctype');//cctype detemines if it's main office (1) or operations (0).
            var transaction_id = $('#transaction_id').val();
            console.log('budget_type: '+btype);
            var branch_id = $('#branch_id').val();
            //alert(branch_id+' - '+btype+' - '+cctype+' - '+transaction_id);
            var powerpriv = false;//false for normal users
            $.ajax({
                url: PECO.base_url() + 'bos/getdatatabledata/' + branch_id,
                type: 'post',
                dataType: 'json',
                data: {'btype': btype, 'cctype': cctype, 'powerpriv': powerpriv, 'transaction_id': transaction_id}
            }).done(function (data) {
                tableid.dataTable({
                    autoWidth: true,
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: data['data'],
                    order: [[1, "asc"]],
                    /**
                     * Additional row processing were done in here. 
                     * @param {type} row This the the row that is newly created.
                     * @param {type} data
                     * @param {type} dataIndex
                     * @return {undefined}
                     */
                    createdRow: function (row, data, dataIndex) {
                        if (data['budget_status'] == 0) {
                            if (!$(row).hasClass('danger'))
                                $(row).addClass('danger');
                        } else if (data['budget_status'] == 1) {
                            if ($(row).hasClass('danger'))
                                $(row).removeClass('danger');
                        }
                    },
                    language: {
                        "emptyTable": '<h5><i class="fa fa-search text-info"></i> No request for this budget-type.</h5>'
                    },
                    aoColumns: [
                        {"data": "expand", sClass: 'withsub'},
                        {"data": "costcenter"},
                        {"data": "desc"},
                        {"data": "propbudget"},
                        {"data": "prevbudget"},
                        {"data": "amt"},
                        {"data": "percent"},
                        {"data": "control"}
                    ],
                    // set the initial value
                    pageLength: 10,
                    fnDrawCallback: function () {
                        //console.log('Table drawn..');
                    }
                });
                /*tfootid.find('.proptotal').text(data.proptotal);
                 tfootid.find('.prevtotal').text(data.prevtotal);
                 tfootid.find('.amttotal').text(data.amttotal);
                 tfootid.find('.percenttotal').html(data.percenttotal);*/
                var proposed_total = data.raw_proptotal;
                var prev_total = data.raw_prevtotal;
                var percent_total = data.raw_variance;

                tfootid.find('.proptotal').text(data.proptotal);
                tfootid.find('.prevtotal').text(data.prevtotal);
                tfootid.find('.amttotal').text(data.amttotal);
                tfootid.find('.percenttotal').html(data.percenttotal);
                tfootid.find('.projtotal').text(data.projtotal);

                proposed_total += parseInt($('#hid_total_prop_bud').val());
                prev_total += parseInt($('#hid_prev_appr_bud').val());
                percent_total += parseInt($('#hid_var').val());

                $('#mo_prop_budget').html(proposed_total.toFixed(2));
                $('#hid_total_prop_bud').val(proposed_total);

                $('#mo_prev_bud').html(prev_total.toFixed(2));
                $('#hid_prev_appr_bud').val(prev_total);

                $('#mo_var').html(percent_total);
                $('#hid_var').val(percent_total);
                
                //Initialize popovers.
                /*$("[data-toggle=popover]").popover({
                 trigger: 'click'
                 });*/
                //This block expands the budgets that has comments in it.
                var comment_data_id = data.capex_sp_budget_id_comment;
                if (comment_data_id !== '') {
                    $(comment_data_id).trigger("click");
                }
                console.log("opex: "+data.debug_data['opex']);
                console.log("capex: "+data.debug_data['capex']);
            }).fail(function (data) {
                console.log(data.toSource());
            });
            
        }
        function createopextable(tableid, tfootid) {
            var btype = tableid.attr('btype');//ID of the budget types
            var cctype = tableid.attr('cctype');//CC type determines if it's main office (1) or operations (0).
            console.log("cc_type:"+cctype+"-btype:"+btype);
            var transaction_id = $('#transaction_id').val();
            var branch_id = $('#branch_id').val();
            //alert(branch_id+' - '+btype+' - '+cctype+' - '+transaction_id);
            var powerpriv = false;//false for normal users
            $.ajax({
                url: PECO.base_url() + 'bos/getdatatabledata/' + branch_id,
                type: 'post',
                dataType: 'json',
                data: {'btype': btype, 'cctype': cctype, 'powerpriv': powerpriv, 'transaction_id': transaction_id}
            }).done(function (data) {
                tableid.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: data['data'],
                    language: {
                        "emptyTable": '<h5><i class="fa fa-search text-info"></i> No request for this budget-type.</h5>'
                    },
                    aoColumns: [
                        {"data": "expand", sClass: 'withsub hidden'},
                        {"data": "costcenter"},
                        {"data": "desc"},
                        {"data": "prevbudget"},
                        {"data": "projexpense"},
                        {"data": "propbudget"},
                        {"data": "amt"},
                        {"data": "percent"},
                        {"data": "control"}
                    ],
                    // set the initial value
                    pageLength: 10,
                    fnDrawCallback: function () {
                        //console.log('Table drawn..');
                    }
                });
                //Display the previous year based on the year the request were made.
                var request_year = data.year;
                var previous_year = parseInt(request_year) - 1;
                $('.prev_year').html(previous_year);
                
                tfootid.find('.proptotal').text(data.proptotal);
                tfootid.find('.prevtotal').text(data.prevtotal);
                tfootid.find('.amttotal').text(data.amttotal);
                tfootid.find('.percenttotal').html(data.percenttotal);
                tfootid.find('.projtotal').text(data.projtotal);
                //Initialize popovers.
                /*$("[data-toggle=popover]").popover({
                 trigger: 'click'
                 });*/
                //This block expands the budgets that has comments in it.
                var comment_data_id = data.opex_budget_id_comment;
                if (comment_data_id !== '') {
                    $(comment_data_id).trigger("click");
                }
                console.log("opex: "+data.debug_data['opex']);
                console.log("capex: "+data.debug_data['capex']);
            }).fail(function (data) {
                console.log(data.toSource());
            });
        }
        //operations (CAPEX, SP, OPEX)
        createdatatable($('#opcapextable'), $('#tfootopcapex'));
        createdatatable($('#opsptable'), $('#tfootopsp'));
        createopextable($('#opopextable'), $('#tfootopopex'));
        //main office (CAPEX, SP, OPEX)
        createdatatable($('#mocapextable'), $('#tfootmocapex'));
        createdatatable($('#mosptable'), $('#tfootmosp'));
        createopextable($('#moopextable'), $('#tfootmoopex'));

        $('body').on('mouseover', '.tooltips', function () {
            $(this).css('cursor', 'pointer');
            $(this).on('click', function () {
                $(this).closest('tr td').find('input').focus();
            });
        });
        //Initialize popovers.
        /*$("[data-toggle=popover]").popover({
         trigger: 'click'
         });*/

    });
</script>
