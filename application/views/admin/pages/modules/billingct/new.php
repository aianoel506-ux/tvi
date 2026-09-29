<?php  // GET MAX BILLNO
$qry_billno = $this->db->select_max('billno')->get('billing_reports')->row();
$last_billno = ($qry_billno) ? ($qry_billno->billno + 1) : 1;
?>

<div class="row margin-bottom-30">
    <div class="col-md-12">
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
                            <input class="" type="hidden" id="total_customer" value="" />
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        CT Group
                                    </span>
                                    <input id="ctid" name="ctid" type="text" class="form-control input-sm" placeholder="No schedule yet." />
                                    <span class="input-group-btn">
                                        <button id="get_ct_group" class="btn btn-info btn-sm "><i class="fa fa-download fa-fw"></i> Get List</button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-5 pull-right">
                                <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-hashtag"></i>
                                            </span>
                                    <input name="billno" value="<?php echo $last_billno; ?>" class="form-control input-sm" id="billno_start" placeholder="Bill Number Starts" />
                                    <div class="input-group-btn">
                                        <div class="btn-goup" style="margin-left: 5px;">
                                            <button id="btn_bill_generate" type="button" class="btn btn-danger btn-sm"><i class="fa fa-tags"></i> Generate Bills</button>
                                            <button id="btn_bill_print" type="button" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print Bills</button>
                                            <button id="btn_bill_ebill" type="button" class="btn btn-info btn-sm"><i class="fa fa-envelope"></i> e-Send eBills</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="col-md-12 margin-top-10">

                        <table width="100%" class="table table-condensed table-bordered table-striped table-hover tbl-sm" id="tbl_billing_entry">
                            <thead>
                            <tr>
                                <th colspan="7" style="text-align: left !important; border-bottom: 1px #ccc solid;" class="info">Account Details</th>
                                <th></th>
                                <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;" class="warning">Reading</th>
                                <th colspan="2" style="text-align: center !important; border-bottom: 1px #ccc solid;" class="danger">Consumption / KWH</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th class="text-align-center"><a id="btn_expand_all" href="javascript:;">
                                        <i id="" class="fa fa-plus-square-o"></i>
                                        All</a></th>
                                <th>Service #</th>
                                <th>Name</th>
                                <th>MTR</th>
                                <th>Meter</th>
                                <th>Serial</th>
                                <th>Multiplier</th>
                                <th width="100px">Rate Class</th>
                                <th width="100px">Present</th>
                                <th width="100px">Previous</th>
                                <th width="80px">Present</th>
                                <th width="100px">Previous</th>
                                <th width="80px">Demand</th>
                                <th width="80px">Net Metering</th>
                                <th>Current</th
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="14" class="number">Total</td>
                                    <td id="total_amt"></td>
                                </tr>
                            </tfoot>
                        </table>



                    </div>

                    <div class="col-md-12">
                        <h3>Summary </h3>
                        <hr>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>



<script src="<?php echo base_url(); ?>assets/pages/billing/main.js" type="text/javascript"></script>
<script>
    BILLING.billingprocessct();
</script>