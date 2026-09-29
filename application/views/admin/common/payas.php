

<div class="col-md-4">
    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-edit"></i>
                <span class="caption-subject font-green-sharp bold uppercase">Add Services / Materials</span>
                <span class="caption-helper"></span>
            </div>
            <div class="tools">

            </div>
        </div>
        <div class="portlet-body">
            <form class="form-horizontal" style="padding-top: 0px !important; margin-top: 0px !important;" id="frm_add_services" action="<?php echo base_url('cad/addcustomercharges');?>" method="post">
                <ul class="list-group summary column no-border">
                    <li class="list-group-item">
                        <strong>Add Service(s) Render</strong>
                    </li>
                    <li class="list-group-item" style="padding-top: 20px;"><br>
                        <input class="form-control" type="hidden" name="dataid" id="appid" value="<?php echo $dataid; ?>">
                        <input class="form-control" type="hidden" name="origin" id="origin" value="<?php echo $origin; ?>">
                        <div class="form-group insert-data">
                            <label class="input-label col-md-3">Account Code</label>
                            <div class="col-md-9">
                                <input required class="form-control" id="acctcode" name="acctcode" />
                            </div>
                        </div>
                        <div class="form-group insert-data">
                            <label class="input-label col-md-3">Amount (non-vat)</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input required="" placeholder="0.00" class="form-control input-lg" type="text" name="acctamt" id="acctamt" value="">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-plus"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </li>
                    <li class="list-group-item"></li>
                    <li class="list-group-item">
                        <strong>Service(s) / Material(s) Assessments</strong>
                        <br>
                    </li>
                    <li class="list-group-item">
                        <label class="col-md-5 label-name">Amount (no-vat)</label>
                        <span class="col-md-7 label-default number" id="assesstmentnovat">0.00</span>
                    </li>
                    <li class="list-group-item">
                        <label class="col-md-5 label-name">Total Vat</label>
                        <span class="col-md-7 label-default number" id="assesstmentvat">0.00</span>
                    </li>
                    <li class="list-group-item">
                        <label class="col-md-5 label-name">Total Amount</label>
                        <span class="col-md-7 label-default number" id="assessmenttotalamt">0.00</span>
                    </li>
                    <li class="list-group-item " >
                        <label class="col-md-5 label-name">Total Amount (Paid)</label>
                        <span class="col-md-7 label-default number" id="assessmenttotalamtpaid" style="color: red !important;">0.00</span>
                    </li>
                    <li class="list-group-item">
                        <label class="col-md-5 label-name">Total Balance</label>
                        <span class="col-md-7 label-default number" id="assessmenttotalamtbal">0.00</span>
                    </li>
                </ul>
            </form>
        </div>
    </div>

</div>
<div class="col-md-8">
    <div class="portlet light table">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-edit"></i>
                <span class="caption-subject font-green-sharp bold uppercase">Services / Materials List</span>
                <span class="caption-helper"></span>
            </div>
            <div class="tools">
                <button class="btn btn-primary inline" id="print_application_cost_assesment"><i class="fa fa-print"></i> Print Summary Cost</button>
            </div>
        </div>
        <div class="portlet-body">

            <table class="table table-borderd table-hover table-striped table-bordered table-condensed table-advance" id="tbl_assesstments">
                <thead>
                <th><i class="fa fa-reorder"></i></th>
                <th>Account Code</th>
                <th>Account Name</th>
                <th>Amount No-VAT</th>
                <th>VAT Amt.</th>
                <th>Total</th>
                <th width="60px">Stat</th>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>/assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>/assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>/assets/global/scripts/peco.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>/assets/pages/tellering/assessments.js" type="text/javascript"></script>

<script>
    ASSESSMENTS.init(<?php echo $dataid; ?>, <?php echo $origin; ?>);
</script>