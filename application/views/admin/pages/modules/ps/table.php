<style>
    .dataTables_wrapper  .col-sm-6:last-child {
        margin-top: -50px !important;
        width: 20% !important;
        float: right !important;
    }
    .dataTables_wrapper  .col-sm-6:last-child .dataTables_filter{

        float: right !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    Profit Share
                </div>
            </div>
            <div class="portlet-body">
                <div class="col-md-9 pull-left" style="margin-left: -15px !important;">
                <form id="submitpstransactions" action="<?php echo base_url() ?>admin/submitpstransactions" method="post">

                    <div class="row">
                        <input type="hidden" name="typesid" id="typesid" value="265" />
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Month</label>
                                <input type="text" id="month" name="month" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Year</label>
                                <input type="text" id="year" value="<?php echo date('Y') ?>" name="year" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Paytype</label>
                                <input type="text" id="paytype" name="paytype" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <button type="button" id="btn_print_payslip" class="btn btn-default pull-right" style="margin-top: 25px;"><i class="fa fa-print"></i> Payslip</button>
                                <button type="submit" class="btn btn-primary" style="margin-top: 25px;"><i class="fa fa-save"></i> Save</button>
                                <button type="button" id="btn_print_report" class="btn btn-default pull-right" style="margin-top: 25px;"><i class="fa fa-print"></i> Report</button>
                                <button type="button" id="btn_export_bankfile" class="btn btn-default pull-right" style="margin-top: 25px;"><i class="fa fa-download"></i> Export</button>
                                <button type="button" id="btn_send_toemail" class="btn btn-default pull-right" style="margin-top: 25px;"><i class="fa fa-envelope"></i> Send</button>
                            </div>
                        </div>
                    </div>
                </form>

                </div>
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

<script src="<?php echo base_url(); ?>assets/pages/payroll/admin.js"></script>

<script type="text/javascript">

    var d = new Date();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();

    FINANCEADMIN.init(265 , month , year , 1);
    PECO.select2Basic($('#month' , document) , 'systems/select2month' ,'Select Month' , false, false,month);
    PECO.select2Basic($('#paytype' , document) , 'admin/getpaytype' , 'Select Paytype' , true, false,1);
</script>