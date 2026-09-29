

<!-- END PAGE CONTENT-->
<div class="row">
    <div class="col-md-4">
        <form id="frm_start_assessment" action="<?php echo base_url('cad/servicestart'); ?>" method="post">
            <input id="personid" name="personid" value="0" type="hidden">
            <input id="moduleid" name="moduleid" value="<?php echo $navid; ?>" type="hidden">
            <input id="types" name="types" value="0" type="hidden">
            <div class="mt-element-ribbon bg-grey-steel ">
                <div class="ribbon ribbon-left ribbon-color-primary uppercase">
                    <i class="fa fa-star"></i> Customers Information
                </div>
                
                <div class="ribbon-content">
                    <div class="form-group">
                        <label>Lastname:</label>
                        <input required class="form-control" id="lastname"  name="lastname" />
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Firstname:</label>
                            <input class="form-control" id="firstname" placeholder="First Name" name="firstname" />
                        </div>
                        <div class="form-group col-md-6">
                            <label>Middlename:</label>
                            <input class="form-control" id="middlename" placeholder="Middle Name" name="middlename" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address:</label>
                        <input class="form-control" id="address" placeholder="Address" name="address" />
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="btn-group pull-right">
                                <button type="reset" class="btn btn-default">Reset</button>
                                <button type="submit" class="btn btn-primary">Start <i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <ul class="list-group summary column no-border list-group-sm">
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
            <li class="list-group-item ">
                <label class="col-md-5 label-name">Total Amount (Paid)</label>
                <span class="col-md-7 label-default number" id="assessmenttotalamtpaid" style="color: red !important;">0.00</span>
            </li>
            <li class="list-group-item">
                <label class="col-md-5 label-name">Total Balance</label>
                <span class="col-md-7 label-default number" id="assessmenttotalamtbal">0.00</span>
            </li>
        </ul>
    </div>

    <div class="col-md-8">
        <div class="caption" style="font-size:18px;">
            <i class="fa fa-user"></i>
            <span class="caption-subject font-blue-sharp bold uppercase" id="servcustservno">N/A</span>
            <span class="caption-subject font-green-sharp bold uppercase" id="servcustname">N/A</span>
            <span class="caption-helper pull-right" id="servcustaddr">N/A</span>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 pull-left">
                    <a  style="margin-left: -15px;" href="javascript:;" class="btn btn-primary inline" id="print_application_cost_assesment" data-id=""><i class="fa fa-print"></i> Print Costing</a>
                </div>
                <table class="table table-hover table-bordered table-condensed" id="tbl_assesstments">
                    <thead>
                        <th><i class="fa fa-reorder"></i></th>
                        <th>Code</th>
                        <th>Descriptions</th>
                        <th>Vat</th>
                        <th>no-VAT</th>
                        <th>Total</th>
                        <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form class="" id="frm_add_services" method="post" action="<?php echo base_url('cad/addcustomercharges'); ?>">
                    <input class="form-control" type="hidden" name="origin" value="<?php echo $navid; ?>" />
                    <input class="form-control" type="hidden" name="dataid" id="servustdataid" value="0" />

                    <div class="form-group col-md-6">
                        <label>Service</label>
                        <input class="form-control" id="select2services" name="acctcode" placeholder="Select Service.." />
                    </div>
                    <div class="form-group col-md-6">
                        <label>Amount</label>
                        <div class="input-group input-icon">
                            <i class="fa fa-tag"></i>
                            <input class="form-control" id="serviceamt" name="acctamt" placeholder="Amount.." />
                            <span class="input-group-btn">
                                <button class="btn btn-default"><i class="fa fa-plus"></i> Add</button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/sservices.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/tellering/assessments.js"></script>
<script>
    SServcies.init();
</script>