
<style>
    #tbl_ar .sorting_disabled:after
    {
        display: none !important;
    }
    #tbl_ar tr td {
        cursor: pointer;
    }
    .select2-chosen {
        padding: 5px 10px;
    }
    .search-input > .form-control {
        padding: 2px 5px !important;
        font-size: 30px;
        text-transform: uppercase;
    }
    .name-label, .addr-label, .name-data, .addr-data {
        display: inline-block;
        paddign: 0px 0px;
    }
    .name-label, .addr-label {
        width: 20%;
    }
    .name-data {
        color: #0C7FAF;
        font-weight: bold;
    }
    .input-label {
        color: #515151;
    }
    #monthlykwh {
        width: 100%;
        height: 100px;
        font-size: 9px;
    }

    .amcharts-graph-g2 .amcharts-graph-stroke {
        stroke-dasharray: 3px 3px;
        stroke-linejoin: round;
        stroke-linecap: round;
        -webkit-animation: am-moving-dashes 1s linear infinite;
        animation: am-moving-dashes 1s linear infinite;
    }

    @-webkit-keyframes am-moving-dashes {
        100% {
            stroke-dashoffset: -31px;
        }
    }
    @keyframes am-moving-dashes {
        100% {
            stroke-dashoffset: -31px;
        }
    }


    .lastBullet {
        -webkit-animation: am-pulsating 1s ease-out infinite;
        animation: am-pulsating 1s ease-out infinite;
    }

    @-webkit-keyframes am-pulsating {
        0% {
            stroke-opacity: 1;
            stroke-width: 0px;
        }
        100% {
            stroke-opacity: 0;
            stroke-width: 50px;
        }
    }
    @keyframes am-pulsating {
        0% {
            stroke-opacity: 1;
            stroke-width: 0px;
        }
        100% {
            stroke-opacity: 0;
            stroke-width: 50px;
        }
    }

    .amcharts-graph-column-front {
        -webkit-transition: all .3s .3s ease-out;
        transition: all .3s .3s ease-out;
    }
    .amcharts-graph-column-front:hover {
        fill: #496375;
        stroke: #496375;
        -webkit-transition: all .3s ease-out;
        transition: all .3s ease-out;
    }

    .amcharts-graph-g3 {
        stroke-linejoin: round;
        stroke-linecap: round;
        stroke-dasharray: 500%;
        stroke-dasharray: 0 /;    /* fixes IE prob */
        stroke-dashoffset: 0 /;   /* fixes IE prob */
        -webkit-animation: am-draw 10s;
        animation: am-draw 10s;
    }
    @-webkit-keyframes am-draw {
        0% {
            stroke-dashoffset: 500%;
        }
        100% {
            stroke-dashoffset: 0%;
        }
    }
    @keyframes am-draw {
        0% {
            stroke-dashoffset: 500%;
        }
        100% {
            stroke-dashoffset: 0%;
        }
    }
    .amChartsPeriodSelector .amChartsButton {
        padding-top: 5px;
        padding-bottom: 3px;
        -moz-border-radius: 0;
        border-radius: 0;
        border: 0;
        border-bottom: 1px solid #dddddd;
        outline: none;
        background: #fff;
        color: #000;
    }

    .amChartsPeriodSelector .amChartsButton:hover {
        background-color: #eeeeee;
    }

    .amChartsPeriodSelector .amChartsButtonSelected {
        background-color: #fff;
        border: 0;
        border-bottom: 1px solid #0088CC;
        color: #000000;
        padding-bottom: 3px;
        -moz-border-radius: 0;
        border-radius: 0;
        margin: 1px;
        outline: none;
    }

    .amcharts-pie-slice {
        transform: scale(1);
        transform-origin: 50% 50%;
        transition-duration: 0.3s;
        transition: all .3s ease-out;
        -webkit-transition: all .3s ease-out;
        -moz-transition: all .3s ease-out;
        -o-transition: all .3s ease-out;
        cursor: pointer;
        box-shadow: 0 0 30px 0 #000;
    }

    .amcharts-pie-slice:hover {
        transform: scale(1.1);
        filter: url(#shadow);
    }


</style>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="row">

                            <div class="col-md-4">
                                <form id="frm_search" class="" action="<?php echo base_url('billing/inquiry'); ?>" method="post">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="input-label">Search</label>
                                            <div class="input-group search-input">

                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-lg"><i class="fa fa-angle-down "></i></button>
                                                </span>
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
                            <div class="col-md-7">
                                <h2 style="margin: 10px 0px;">
                                    <span class="input-label name-label">Name:</span><span class="name-data" id="ar_name">None</span>
                                </h2>
                                <span class="input-label addr-label">Address:</span><span class="addr-data" id="ar_addr">None</span>

                            </div>
                            <div class="col-md-1" style="text-align: center; border-left: 1px solid #ccc; margin-bottom: 5px;">
                                <img style="margin: 5px auto;" width="90%" src="<?php echo base_url('assets/global/img/person_default.jpg'); ?>" />
                            </div>


                        </div>
                    </div>

                    <ul class="list-group summary column table">
                        <li class="list-group-item"> GDLB: <span class="label label-default pull-right" id="gdlb"></span> </li>
                        <li class="list-group-item"> RATE: <span class="label label-default pull-right" id="rate"></span> </li>
                        <li class="list-group-item"> MULT: <span class="label label-default pull-right" id="mult"></span> </li>
                        <li class="list-group-item"> STATUS: <span class="label label-default pull-right" id="acc_stat"></span> </li>
                    </ul>
                    <div class="row">
                        <div class="col-md-2 col-sm-2 col-xs-2">
                            <p class="margin-top-10">Previous Receivable</p>
                            <div class="input-group">
                                <input id="prev_year" class="form-control" placeholder="Year" name="prevyear" />
                                <select id="prev_month" class="form-control" name="prevmonth" >
                                    <option>Select..</option>
                                    <?php
                                    for($m=1; $m<=12; $m++) {
                                        $dt = DateTime::createFromFormat('m', $m);
                                        $mname = $dt->format('F');
                                        echo '<option value="'.$m.'">'.$mname.'</option>';
                                    }
                                    ?>
                                </select>
                                <span class="input-group-btn">
                                    <button style="height: 68px !important; display: inline-block !important;" type="button" class="btn btn-default">
                                        <i class="fa fa-search fa-2x"></i>
                                    </button>
                                    <button style="height: 68px !important; display: inline-block !important;" type="button" class="btn btn-default">
                                        <i class="fa fa-refresh fa-2x"></i>
                                    </button>
                                </span>
                            </div>
                            <ul class="nav nav-tabs tabs-left ar-tab" style="min-height: 350px;">
                                <li class="active">
                                    <a id="tab_ar" href="#billar" data-toggle="tab" aria-expanded="true">
                                        <i class="fa fa-tag fa-fw"></i> Billing AR
                                    </a>
                                </li>
                                <li class="">
                                    <a id="tab_othinfo" href="#acctdetails" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-tag fa-fw"></i> Other Information
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#payments" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-tag fa-fw"></i> Payments
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#payments" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-tag fa-fw"></i> Reading History
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#pninq" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-tag fa-fw"></i> P.N. Inquiry
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#tagging" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-tag fa-fw"></i> Account Tagged <span style="margin-top: 2px;" class="badge badge-danger pull-right">2</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-10">

                            <div class=" tab-content">

                                <div class="tab-pane active fade in" id="billar">
                                    <div class="row margin-bottom-10" >
                                        <div class="col-md-3"  style="padding: 20px 20px; padding-left: 30px;">
                                            <ul class="list-group summary column no-border">
                                                <li class="list-group-item"> <i class="fa fa-square text-danger"></i> Total Blance: <span class="label label-default pull-right" id="ar_amtbal" style="color: red;">0.00</span> </li>
                                                <li class="list-group-item"> <i class="fa fa-square text-success"></i> Total Paid: <span class="label label-default pull-right" id="ar_total_paid">0.00</span> </li>
                                                <li class="list-group-item"> <i class="fa fa-square text-info"></i> Average KWH: <span class="label label-default pull-right" id="ar_ave_kwh">0</span> </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3" style="padding: 20px 20px; margin-top: 5px; border-left: 1px solid #ccc">
                                            <ul class="list-group summary column no-border">
                                                <li class="list-group-item"> Total Interest: <span class="label label-default pull-right" id="ar_total_int">0.00</span> </li>
                                                <li class="list-group-item"> Last Pay Date: <span class="label label-default pull-right" id="ar_date_lp">0000-00-00</span> </li>
                                                <li class="list-group-item"> Meter No.: <span class="label label-default pull-right" id="ar_mtrno">0</span> </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3" style="padding: 20px 20px; margin-top: 5px; border-left: 1px solid #ccc">
                                            <ul class="list-group summary column no-border">
                                                <li class="list-group-item"><i class="fa fa-square text-danger"></i> Due: <span class="label label-default pull-right" id="ar_total_due">0.00</span> </li>
                                                <li class="list-group-item"><i class="fa fa-square text-success"></i> Current: <span class="label label-default pull-right" id="ar_amt_curr">0.00</span> </li>
                                                <li class="list-group-item"><i class="fa fa-square text-primary"></i> No. of Bills: <span class="label label-default pull-right" id="ar_no_bill">0</span> </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3" style="position: relative; margin-top: 5px; border-left: 1px solid #ccc">
                                            <span class="text-info" style="font-size: 9px; position: absolute; bottom: -10px; right: 35px;">Monthly KWH Consumptions</span>
                                            <div id="monthlykwh"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="min-height: 300px;">
                                        <table width="100%" id="tbl_ar" class="table table-hover table-stripped table-condensed table-bordered tbl-xs"  style="margin-top: -5px !important;">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="text-align-center"><i class="fa fa-square text-primary"></i> Month</th>
                                                    <th rowspan="2" class="text-align-center"><i class="fa fa-square text-info"></i> KWH</th>
                                                    <th rowspan="2" class="text-align-center">Bill No.</th>
                                                    <th rowspan="2" class="text-align-center"><i class="fa fa-square text-danger"></i> Amount Due</th>
                                                    <th rowspan="2" class="text-align-center">Duedate</th>
                                                    <th rowspan="2" class="text-align-center">Date Paid</th>
                                                    <th rowspan="2" class="text-align-center">Amt. Paid</th>
                                                    <th rowspan="2" class="text-align-center">Interest</th>
                                                    <th rowspan="2" class="text-align-center">Sur.Pay</th>
                                                    <th colspan="5" class="text-align-center info">Referrals</th>
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

                                    </div>

                                    <div class="col-md-12">
                                        <div class="btn-group" id="ar_stats">

                                        </div>
                                        <a class="btn btn-default pull-right" href="javascript:;" >
                                            <i class="fa fa-print fa-fw"></i> Print Statment
                                        </a>
                                    </div>

                                </div>

                                <div class="tab-pane fade in" id="acctdetails">
                                    <div style="width:100%; height: 250px; border: 1px solid ; margin-top: 20px; margin-bottom: 10px; padding-bottom: 10px;" id="othergraph"></div>
                                    <div class="row">
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

                                <div class="tab-pane fade in" id="pninq">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h4 class="text-primary">P.N. Entry</h4>
                                            <form class="" role="form" method="post" action="<?php echo base_url('tellering/processpn');?>">
                                                <div class="form-body">
                                                    <div class="form-group">
                                                        <label class="input-label">Remarks</label>
                                                        <textarea class="form-control" name="pnrem" placeholder="Reasons for P.N."></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="input-label">Minimum Amount</label>
                                                        <input class="form-control" readonly name="minamt" placeholder="Min. Amount" />
                                                    </div>
                                                </div>
                                                <div class="form-actions">
                                                    <button class="btn btn-default" type="reset">Reset</button>
                                                    <button class="btn btn-primary" type="submit">Save</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="col-md-7">
                                            <h4 class="text-primary">P.N. Files</h4>
                                            <table id="tbl_pn_file" class="table table-hover table-striped table-condensed table-sm">
                                                <thead>
                                                    <th>#</th>
                                                    <th>Year</th>
                                                    <th>Month</th>
                                                    <th>Amount</th>
                                                    <th>Rem</th>
                                                    <th>Paid</th>
                                                    <th><i class="fa fa-wrench"></i></th>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-2">
                                            <h4 class="text-primary">Summary</h4>
                                            <ul class="list-group summary row">
                                                <li class="list-group-item">
                                                    <span class="label-name">Total</span>
                                                    <span class="label label-default">0.00</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="label-name">Amount Paid</span>
                                                    <span class="label label-default">0.00</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="label-name">Amount Balance</span>
                                                    <span class="label label-default">0.00</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade in" id="payments">
                                    Payments
                                </div>

                                <div class="tab-pane fade in" id="tagging">
                                    <div class="portlet-body">
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-group"></i>
                                            <div>
                                                Users
                                            </div>
                                            <span class="badge badge-danger">
												2 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-barcode"></i>
                                            <div>
                                                Products
                                            </div>
                                            <span class="badge badge-success">
												4 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-bar-chart-o"></i>
                                            <div>
                                                Reports
                                            </div>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-sitemap"></i>
                                            <div>
                                                Categories
                                            </div>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-calendar"></i>
                                            <div>
                                                Calendar
                                            </div>
                                            <span class="badge badge-success">
												4 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-envelope"></i>
                                            <div>
                                                Inbox
                                            </div>
                                            <span class="badge badge-info">
												12 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-bullhorn"></i>
                                            <div>
                                                Notification
                                            </div>
                                            <span class="badge badge-danger">
												3 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-map-marker"></i>
                                            <div>
                                                Locations
                                            </div>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-money"><i></i></i>
                                            <div>
                                                Finance
                                            </div>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-plane"></i>
                                            <div>
                                                Projects
                                            </div>
                                            <span class="badge badge-info">
												21 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-thumbs-up"></i>
                                            <div>
                                                Feedback
                                            </div>
                                            <span class="badge badge-info">
												2 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-cloud"></i>
                                            <div>
                                                Servers
                                            </div>
                                            <span class="badge badge-danger">
												2 </span>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-globe"></i>
                                            <div>
                                                Regions
                                            </div>
                                        </a>
                                        <a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-heart-o"></i>
                                            <div>
                                                Popularity
                                            </div>
                                            <span class="badge badge-info">
												221 </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>

<!-- Resources -->

<script src="<?php echo base_url();?>assets/global/plugins/amcharts_v3/amcharts.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/global/plugins/amcharts_v3/serial.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/global/plugins/amcharts_v3/amstock.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/global/plugins/amcharts_v3/pie.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/global/plugins/amcharts_v3/plugins/export/export.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/global/plugins/amcharts_v3/themes/black.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/pages/inquiry/inquiry.js" type="text/javascript"></script>
<script>
    INQUIRY.init();
</script>