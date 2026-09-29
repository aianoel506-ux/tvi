

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    14th Month
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="submitpstransactions" action="<?php echo base_url() ?>admin/submitpstransactions" method="post">
                            <div class="row">
                                <input type="hidden" name="typesid" id="typesid" value="3072" />
                               <div class="col-md-4">
                                   <div class="input-group">
                                       <label>Month</label>
                                       <input type="text" id="month" name="month" class="form-control" />
                                       <span class="input-group-btn" style="width:0px;"></span>
                                       <label>Year</label>
                                       <input type="text" id="year" value="<?php echo date('Y') ?>" name="year" class="form-control" />
                                       <span class="input-group-btn" style="width:0px;"></span>
                                       <label>Paytype</label>
                                       <input type="text" id="paytype" name="paytype" class="form-control" />
                                   </div>
                               </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" style="margin-top: 25px;"><i class="fa fa-save"></i> Save</button>
                                        <button type="button" id="btn_print_payslip" class="btn btn-default pull-right" style="margin-top: 25px;"><i class="fa fa-print"></i> Payslip</button>
                                        <button type="button" id="btn_print_report" class="btn btn-default pull-right" style="margin-top: 25px;"><i class="fa fa-print"></i> Report</button>
                                        <button type="button" id="btn_export_bankfile" class="btn btn-default pull-right" style="margin-top: 25px;"><i class="fa fa-download"></i> Export</button>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <input type="text" id="viewtype" name="viewtype" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" id="getbasic">Get Basic</button>
                        <table class="table table-bordered table-hover table-condensed tbl-sm tbl-zoom" id="annualtable">
                            <thead>
                            <th></th>
                            <th>Account No.</th>
                            <th>Name</th>
                            <th>Gross</th>
                            <th>Deduction</th>
                            <th>Tax</th>
                            <th>Net</th>
                            <th>Type</th>
                            <th>Status</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/payroll/admin.js"></script>

<script type="text/javascript">

    var d = new Date();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();


    FINANCEADMIN.init(3072 , month , year , 3);
    PECO.select2Basic($('#month' , document) , 'systems/select2month' ,'Select Month' , false, false,month);
    PECO.select2Basic($('#year' , document) , 'systems/select2year' ,'Select Year' , false, false,year);
    PECO.select2Basic($('#paytype' , document) , 'admin/getpaytype' , 'Select Paytype' , true, false,3);
    PECO.select2Basic($('#viewtype' , document) , 'admin/select2payclass' , 'Select Payclass' , true, false,false);
</script>