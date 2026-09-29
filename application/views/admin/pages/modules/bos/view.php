<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/FixedColumns/css/dataTables.fixedColumns.css"/>

        <div class="portlet box tabbable">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-green-sharp bold uppercase">Budget Request (PCEO View)</span>
                    <span class="caption-helper"> as of <?php echo date('F d, Y'); ?></span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active"><a style="font-size: 18px; padding-left: 20px; padding-right: 20px;" href="#CAPEX" data-toggle="tab"><i class="fa fa-tag fa-fw"></i> CAPEX / SP</a></li>
                    <li class=""><a style="font-size: 18px; padding-left: 20px; padding-right: 20px;" href="#OPEX" data-toggle="tab"><i class="fa fa-tag fa-fw"></i> OPEX</a></li>
                </ul>
            </div>  
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane fade in" id="OPEX">
                        <div class="caption">
                            <h4 class="caption-subject font-green-sharp bold uppercase">Operations</h4>
                            <span class="caption-helper"></span>
                        </div>
                        <hr>
                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase">OPEX</span>
                            <span class="caption-helper">Operation Expenditure</span>
                        </div>
                        <hr>
                        <!--<a class="btn btn-info btn-xs">View All</a><a class="btn btn-default btn-xs">100</a><a class="btn btn-default btn-xs">102</a><a class="btn btn-default btn-xs">103</a>-->
                        <table class="table table-hover table-condensed table-stiped table-bordered tbl-sm margin-top-10" id="opopextable" btype="OPEX" cctype="0">
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
                                <td class="number proptotal"></td>
                                <td class="number amttotal"></td>
                                <td class="number percenttotal">%</td>
                                <td></td>
                            </tfoot>
                        </table>
                        <hr>
                        <div class="caption">
                            <h4 class="caption-subject font-green-sharp bold uppercase">Main Office</h4>
                            <span class="caption-helper"></span>
                        </div>
                        <hr>

                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase">OPEX</span>
                            <span class="caption-helper">Operation Expenditure</span>
                        </div>
                        <hr>
                        <table class="table table-hover table-condensed table-stiped table-bordered tbl-sm margin-top-10" id="moopextable" btype="OPEX" cctype="1">
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
                                <td colspan="2">Total Budget (Main Office)</td>
                                <td class="number prevtotal"></td>
                                <td class="number projtotal"></td>
                                <td class="number proptotal"></td>
                                <td class="number amttotal"></td>
                                <td class="number percenttotal">%</td>
                                <td></td>
                            </tfoot>
                        </table>
                        <hr>
                    </div>
                    <div class="tab-pane fade in active" id="CAPEX">
                        <div class="caption">
                            <h4 class="caption-subject font-green-sharp bold uppercase">Operations</h4>
                            <span class="caption-helper"></span>
                        </div>
                        <hr>
                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase">CAPEX</span>
                            <span class="caption-helper">Capitalize Expenditure</span>
                        </div>
                        <table class="table table-hover table-condensed table-stiped table-bordered tbl-sm" id="opcapextable" btype="CAPEX" cctype="0">
                            <thead>
                                <tr>
                                    <th rowspan="2" colspan="2">COST Center</th>
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
                            <td class="number proptotal"></td>
                            <td class="number prevtotal"></td>
                            <td class="number amttotal"></td>
                            <td class="number percenttotal">%</td>
                            <td></td>
                            </tfoot>
                        </table>
                        <hr>
                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase">SP</span>
                            <span class="caption-helper">Special Projects</span>
                        </div>
                        <table class="table table-hover table-condensed table-stiped table-bordered tbl-sm" id="opsptable" btype="SP" cctype="0">
                            <thead>
                                <tr>
                                    <th rowspan="2" colspan="2">COST Center</th>
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
                        <hr class="margin-top-20">
                        <div class="caption">
                            <h4 class="caption-subject font-green-sharp bold uppercase">Main Office</h4>
                            <span class="caption-helper"></span>
                        </div>
                        <hr>
                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase">CAPEX</span>
                            <span class="caption-helper">Capitalize Expenditure</span>
                        </div>
                        <table class="table table-hover table-condensed table-stiped table-bordered tbl-sm" id="mocapextable" btype="CAPEX" cctype="1">
                            <thead>
                                <tr>
                                    <th rowspan="2" colspan="2">COST Center</th>
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
                            <td class="number proptotal"></td>
                            <td class="number prevtotal"></td>
                            <td class="number amttotal"></td>
                            <td class="number percenttotal">%</td>
                            <td></td>
                            </tfoot>
                        </table>
                        <hr>
                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase">SP</span>
                            <span class="caption-helper">Special Projects</span>
                        </div>
                        <table class="table table-hover table-condensed table-stiped table-bordered tbl-sm" id="mosptable" btype="SP" cctype="1">
                            <thead>
                                <tr>
                                    <th rowspan="2" colspan="2">COST Center</th>
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
                            <td class="number proptotal"></td>
                            <td class="number prevtotal"></td>
                            <td class="number amttotal"></td>
                            <td class="number percenttotal">%</td>
                            <td></td>
                            </tfoot>
                        </table>
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
                                            <li class="list-group-item"> Total Proposed Budget <span class="label label-default pull-right" id="daterange">0</span> </li>
                                            <li class="list-group-item"> Previously Approved Budget (<?php echo date('Y', strtotime('-1 year')); ?>): <span class="label label-default pull-right" id="totalut">0</span> </li>
                                            <li class="list-group-item"> Variance (Increase/Decrease): <span class="label label-default pull-right" id="totallate">0</span> </li>
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
                                            <li class="list-group-item"> Total Proposed Budget <span class="label label-default pull-right" id="daterange">0</span> </li>
                                            <li class="list-group-item"> Previously Approved Budget (<?php echo date('Y', strtotime('-1 year')); ?>): <span class="label label-default pull-right" id="totalut">0</span> </li>
                                            <li class="list-group-item"> Variance (Increase/Decrease): <span class="label label-default pull-right" id="totallate">0</span> </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 pull-right">
                                <div class="portlet table light">

                                    <div class="portlet-body">
                                        <ul class="list-group summary no-border list-group-sm">
                                            <li class="list-group-item"><div class="caption">
                                                    <span class="caption-subject font-green-sharp bold uppercase">Summary Grand Total</span>
                                                    <span class="caption-helper"></span>
                                                </div> </li>
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
    BOS.capex();
    function approvalIndicator(buttonId,statusValue){
        buttonId = '#'+buttonId;
        if (statusValue == '0' || statusValue == 0){
             if ($(buttonId).closest("tr").find('td').hasClass("success")){
                  $(buttonId).closest("tr").find('td').each(function(){ $(this).removeClass("success"); });
             }
             $(buttonId).closest("tr").find('td').each(function(){ $(this).addClass("danger"); });
        }else{
            if ($(buttonId).closest("tr").find('td').hasClass("danger")){
                $(buttonId).closest("tr").find('td').each(function(){ $(this).removeClass("danger"); });
            }
            //$(buttonId).closest("tr").find('td').each(function(){ $(this).addClass("success"); });
        }
    }
    function buttonChangeColorAndValue(buttonId,statusValue){
        buttonId = '#'+buttonId;
        if (statusValue == '1' || statusValue == 1){
             if ($(buttonId).hasClass("btn-success")){
                  $(buttonId).removeClass("btn-success");
             }
             $(buttonId).addClass("btn-danger");
             $(buttonId).val("Disapprove");
        }else{
            if ($(buttonId).hasClass("btn-danger")){
                $(buttonId).removeClass("btn-danger");
            }
            $(buttonId).addClass("btn-success");
            $(buttonId).val("Revoke Disapproval");
        }
    }
    $('body').on('click', '.toggleApproval', function () {
        var budgetDataId_buttonId = $(this).attr('id');
        var status = toggleBudget(budgetDataId_buttonId);
        approvalIndicator(budgetDataId_buttonId,status);
        buttonChangeColorAndValue(budgetDataId_buttonId,status);
    });
    function toggleBudget(budgetDataId){
        var statusValue = null;
        $.ajax({
            async: false,
            url: PECO.base_url() + 'bos/perBudgetDisapproval',
            type: 'post',
            data: {'budgetDataId': budgetDataId},
            dataType: 'json',
            success: function (d) {
                statusValue = d.status;
                console.log("status:"+statusValue+" d.msg:"+d.msg+" func:"+d.func);
                PECO.initAlerts(d.msg, 'Information', d.func);
                
            },
            error: function (d){
                console.log("Error: status:"+statusValue+" d.msg:"+d.msg+" func:"+d.func);
                //PECO.PhpError();
                PECO.initAlerts('perBudgetDisapproval ajax encountered an error.', 'Information', 'warning');
            }
        });
        return statusValue;
    }
    function viewNewlyInserted(budget_data_id, lastId){
        var commentDivId = '#'+budget_data_id;
        $.ajax({
            url: PECO.base_url() + 'bos/viewNewRemarks',
            type: 'post',
            data: {'budgetDataId': budget_data_id, 'lastId': lastId},
            dataType: 'json'
        }).done(function (d) {
            $(commentDivId).append(d.html);
            PECO.initAlerts(d.msg, 'Information', d.func);
        }).fail(function () {
            //PECO.PhpError();
            PECO.initAlerts('viewNewRemarks has encountered an error.', 'Information', 'warning');
        });

    }
    //TODO the approval and disapproval indicator should change depending on the current status of the request after first loading.
    function createdatatable(tableid, tfootid) {
        var btype = tableid.attr('btype');
        var cctype = tableid.attr('cctype');//cctype detemines if it's main office (1) or operations (0).
        var powerpriv = 2;
        $.ajax({
            url: PECO.base_url() + 'bos/getdatatabledata',
            type: 'post',
            dataType: 'json',
            data: {'btype': btype, 'cctype': cctype, 'powerpriv': powerpriv}
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
                    "emptyTable": '<h5><i class="fa fa-search text-info"></i> No data found!</h5>'
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
                    console.log('Table drawn..');
                }
            });
            tfootid.find('.proptotal').text(data.proptotal);
            tfootid.find('.prevtotal').text(data.prevtotal);
            tfootid.find('.amttotal').text(data.amttotal);
            tfootid.find('.percenttotal').text(data.percenttotal);
        }).fail(function () {
            //PECO.phpError();
        });
    }

    function createopextable(tableid, tfootid) {
        var btype = tableid.attr('btype');
        var cctype = tableid.attr('cctype');//CC type determines if it's main office (1) or operations (0).
        var powerpriv = 2;//2 for pceo approved items
        $.ajax({
            url: PECO.base_url() + 'bos/getdatatabledata',
            type: 'post',
            dataType: 'json',
            data: {'btype': btype, 'cctype': cctype, 'powerpriv': powerpriv}
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
                    "emptyTable": '<h5><i class="fa fa-search text-info"></i> No data found!</h5>'
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
                    console.log('Table drawn..');
                }
            });
            tfootid.find('.proptotal').text(data.proptotal);
            tfootid.find('.prevtotal').text(data.prevtotal);
            tfootid.find('.amttotal').text(data.amttotal);
            tfootid.find('.percenttotal').text(data.percenttotal);
            tfootid.find('.projtotal').text(data.projtotal);
        }).fail(function () {
            //PECO.phpError();
        });
    }
    createdatatable($('#opcapextable'), $('#tfootopcapex'));
    createdatatable($('#opsptable'), $('#tfootopsp'));

    createdatatable($('#mocapextable'), $('#tfootmocapex'));
    createdatatable($('#mosptable'), $('#tfootmosp'));

    createopextable($('#opopextable'), $('#tfootopopex'));
    createopextable($('#moopextable'), $('#tfootmoopex'));

    $('tbody td:not(.readonly)');//makes the whole table data read only.
    $('body').on('mouseover', '.tooltips', function () {
        $(this).css('cursor', 'pointer');
        $(this).on('click', function () {
            $(this).closest('tr td').find('input').focus();
        });
    });
</script>