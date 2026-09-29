
<!-- Add fancyBox main JS and CSS files -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/fancybox/source/jquery.fancybox.css?v=2.1.2" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />

<!-- Add Thumbnail helper (this is optional) -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/pages/billing/main.css" rel="stylesheet" type="text/css" />


<div class="portlet light " style="margin: 0px -20px;">
    <div class="portlet-title">
        <div class="row">
            <div class="col-md-4">


                <form id="frm_search" class="" action="<?php echo base_url('billing/inquiry'); ?>" method="post" style="margin-top: 10px;">
                    <div class="form-body">
                        <div class="form-group">
                            <div class="input-group search-input">
                                <input name="servno" id="servno" style="width: 79%; display: inline-block" class="form-control input-lg" placeholder="Servno" />
                                <input name="mtr" id="mtr" style="width: 20%; display: inline-block; text-align: center" class="form-control input-lg" placeholder="Mtr" value="1" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-info btn-lg"><i class="fa fa-search "></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-6">
                <h2 style="margin: 0px 0px;">
                    <span class="input-label name-label" style="font-size: 11px; vertical-align: top; margin-top: 10px; text-align: right; padding-right: 10px;">Name</span><span class="name-data" id="ar_name">None</span>
                </h2>
                <span class="input-label addr-label" style="font-size: 11px; vertical-align: top; margin-top: 0px; text-align: right; padding-right: 10px;">Address</span><span class="addr-data" id="ar_addr">None</span>
            </div>
            <div class="acct-image-container" style="text-align: center; border-left: 1px solid rgba(0,0,0,0.08); margin-bottom: 5px;">
                <a href="#form_ar_name_search" title="Search Name" data-toggle="ajax-modal" class="btn btn-default btn-lg"><i class="fa fa-tag"></i></a>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-group summary column table">
                    <li class="list-group-item"> <span class="col-md-4 label-name">GDLB</span> <span class="col-md-8 label-default pull-right" id="gdlb"></span> </li>
                    <li class="list-group-item"> <span class="col-md-4 label-name">RATE</span> <span class="col-md-8 label-default pull-right" id="rate"></span> </li>
                    <li class="list-group-item"> <span class="col-md-4 label-name">MULT</span> <span class="col-md-8 label-default pull-right" id="mult"></span> </li>
                    <li class="list-group-item"> <span class="col-md-4 label-name">STATUS</span> <span class="col-md-8 label-default pull-right" id="status"></span> </li>
                </ul>
            </div>
                <div class="col-md-2" id="filter_input">

                    <div class="form-group margin-top-10" style="margin-bottom: 0px;">
                        View Limit
                        <input class="form-control" id="input_ar_limit" value="12" />
                    </div>
                    <div class="form-group margin-top-10" style="margin-bottom: 0px;" >
                        Year
                        <input class="form-control" id="input_ar_year" value="<?php echo date('Y');?>" />
                    </div>


                </div>
            <div class="col-md-10">

                    <div class=" tab-content">
                        <div class="tab-pane active fade in" id="billar">

                            <div class="row margin-bottom-10">
                                <div class="col-md-3" style="padding: 20px 20px; padding-left: 30px;">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item"> <i class="fa fa-square text-danger"></i> Blance: <span class="label label-default pull-right" id="amt_balance" style="color: red;">0.00</span> </li>
                                        <li class="list-group-item"> <i class="fa fa-square text-success"></i> Paid: <span class="label label-default pull-right" id="amt_paid">0.00</span> </li>
                                        <li class="list-group-item"><i class="fa fa-square text-primary"></i> No. of Bills: <span class="label label-default pull-right" id="nobills">0</span> </li>
                                    </ul>
                                </div>
                                <div class="col-md-3" style="padding: 20px 20px; margin-top: 5px; border-left: 1px solid #ccc">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item"> Interest: <span class="label label-default pull-right" id="amt_interest">0</span> </li>
                                        <li class="list-group-item"> Pay Date: <span class="label label-default pull-right" id="lastpay">0000-00-00</span> </li>
                                        <li class="list-group-item"> Meter No.: <span class="label label-default pull-right" id="mtrno">0</span> </li>
                                    </ul>
                                </div>
                                <div class="col-md-3" style="padding: 20px 20px; margin-top: 5px; border-left: 1px solid #ccc">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item"><i class="fa fa-square text-danger"></i> Due: <span class="label label-default pull-right" id="amt_overdue">0.00</span> </li>
                                        <li class="list-group-item"><i class="fa fa-square text-success"></i> Current: <span class="label label-default pull-right" id="amt_current">0.00</span> </li>
                                        <li class="list-group-item"> <i class="fa fa-square text-info"></i> Average KWH: <span class="label label-default pull-right" id="kwh_ave">0</span> </li>
                                    </ul>
                                </div>
                                <div class="col-md-3" style="position: relative; margin-top: 5px; border-left: 1px solid #ccc">
                                    <span class="text-info" style="font-size: 9px; position: absolute; bottom: -10px; right: 35px;">Monthly KWH Consumptions</span>
                                    <div id="monthlykwh" style="overflow: hidden; text-align: left;">
                                    </div>

                                </div>
                            </div>
                            <table class="table table-hover table-striped table-bordered table-condensed tbl-xs" id="tbl_ar">
                                <thead>
                                <tr>
                                    <th rowspan="2" >Month</th>
                                    <th rowspan="2" >Year</th>
                                    <th rowspan="2" >Billno</th>
                                    <th rowspan="2" >KWH</th>
                                    <th rowspan="2" >Current</th>
                                    <th rowspan="2" >Duedate</th>
                                    <th rowspan="2" >Interest</th>
                                    <th rowspan="2" >Paid</th>
                                    <th rowspan="2" >Date Paid</th>
                                    <th rowspan="2" >Balance</th>
                                    <th colspan="5" class="info">Referrals</th>
                                </tr>

                                <tr>
                                    <th>C</th>
                                    <th>R</th>
                                    <th>PN</th>
                                    <th>U</th>
                                    <th>J</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <hr style="margin-top: 5px;">
                            <div class="btn-group">
                                <?php
                                if(user_id() == 1) {
                                    echo '<button id="btn_migrate_payment" title="Migrate Payments" class="btn btn-xs btn-danger">Migrate Payments</button>';
                                }
                                ?>
                            </div>

                            <div class="btn-group pull-right">
                                <button id="printstatementbtn" class="btn btn-xs btn-default"><i class="fa fa-print"></i> Print Statement</button>
                            </div>
                        </div>

                        <div class="tab-pane fade in" id="acctdetails">
                            <div style="width:100%; height: 250px; border: 1px solid #67b7dc; margin-top: 20px; margin-bottom: 10px; padding-bottom: 10px;" id="othergraph"></div>
                            <div class="row margin-top-20">
                                <div class="col-md-4">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item"> TNO No.: <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Contract Date: <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Meter No.: <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Serial No.: <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Feeder No. <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Transformer No. <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Check Meter No. <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Load. <span class="label label-default pull-right" id="">N/A</span> </li>
                                    </ul>
                                </div>

                                <div class="col-md-4">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item"> Rate Class: <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> RGD No. <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> CC No.<span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Status <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Status Date <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Meter Report Date<span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Previous Meter No.<span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Previous Mult Code: <span class="label label-default pull-right" id="">N/A</span> </li>
                                    </ul>
                                </div>

                                <div class="col-md-4">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item"> Last Reading: <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> New Init Reading. <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> FDO No.<span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> FDO Date <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Present Date <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Present Reading <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Previous Date <span class="label label-default pull-right" id="">N/A</span> </li>
                                        <li class="list-group-item"> Previous Reading <span class="label label-default pull-right" id="">N/A</span> </li>
                                    </ul>
                                </div>

                            </div>
                            <hr>
                            <div class="input-group pull-right">
                                <button class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                            </div>
                        </div>

                        <div class="tab-pane fade in" id="readinghist">
                            <ul class="list-group summary column table no-border margin-top-20">
                                <li class="list-group-item" style="width: 25%"><span class="col-md-4 label-name">Con. Date</span> <span class="col-md-8 label-default" id="rdghist_condte">0000-00-00</span> </li>
                                <li class="list-group-item" style="width: 25%"><span class="col-md-6 label-name">Meter Repl Date</span> <span class="col-md-6 label-default" id="rdghist_repldate">0000-00-00</span> </li>
                                <li class="list-group-item" style="width: 25%"><span class="col-md-4 label-name">Initial Read</span> <span class="col-md-8 label-default" id="rdghist_initrdg">0000</span> </li>
                                <li class="list-group-item" style="width: 25%"><span class="col-md-4 label-name">Last Read</span> <span class="col-md-8 label-default" id="rdghist_lastrdg">0000</span> </li>
                            </ul>
                            <table class="table table-hover table-bordered tbl-xs" id="tbl_billing_hist">
                                <thead>
                                <th>Month</th>
                                <th>Year</th>
                                <th>KWH Used</th>
                                <th>Prs. Read</th>
                                <th>Prv. Read</th>
                                <th>Prs. Date</th>
                                <th>Prv. Date</th>
                                <th>No. of Days</th>
                                <th>Mtr No.</th>
                                <th>Mtr Serial</th>
                                <th>MOYR</th>
                                <th>Batch</th>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>RV History</strong>
                                    <table width="100%" class="table table-hover table-bordered tbl-xs" id="tbl_rv_history">
                                        <thead>
                                        <th>RV No.</th>
                                        <th>Meter #</th>
                                        <th>Date Issued</th>
                                        <th>Date Acted</th>
                                        <th>Reading</th>
                                        <th>Inspector</th>
                                        <th>Complete</th>
                                        <th></th>
                                        </thead>

                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12"><hr>
                                    <ul class="list-group summary column no-border margin-top-20">
                                        <li class="list-group-item"><span class="col-md-2 label-name">Findings</span> <span class="col-md-10 label-default" id="rgdhist_findings">N/A</span> </li>
                                    </ul>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
        <hr style="margin-bottom: 30px;">
    </div>
</div>


<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>


<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
