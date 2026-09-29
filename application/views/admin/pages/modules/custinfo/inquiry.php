
<!-- Add fancyBox main JS and CSS files -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/fancybox/source/jquery.fancybox.css?v=2.1.2" media="screen" />

<!-- Add Button helper (this is optional) -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />

<!-- Add Thumbnail helper (this is optional) -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />

<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
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


    .custom-menu {
        display: none;
        z-index: 1000;
        position: absolute;
        overflow: hidden;
        border: 1px solid #CCC;
        white-space: nowrap;
        font-family: sans-serif;
        background: #FFF;
        color: #333;
        border-radius: 5px;
        padding: 0;
    }

    /* Each of the items in the list */
    .custom-menu li {
        padding: 8px 12px;
        cursor: pointer;
        list-style-type: none;
        transition: all .3s ease;
        user-select: none;
    }

    .custom-menu li:hover {
        background-color: #DEF;
    }
    .fancybox-inner {
        max-height: 550px !important;
    }
    .fancybox-image {
        height: 100% !important;
    }


</style>
<div class="row padding-top-20">

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

<ul class="list-group summary column table">
    <li class="list-group-item"> <span class="col-md-4 label-name">GDLB</span> <span class="col-md-8 label-default pull-right" id="gdlb"></span> </li>
    <li class="list-group-item"> <span class="col-md-4 label-name">RATE</span> <span class="col-md-8 label-default pull-right" id="rate"></span> </li>
    <li class="list-group-item"> <span class="col-md-4 label-name">MULT</span> <span class="col-md-8 label-default pull-right" id="mult"></span> </li>
    <li class="list-group-item"> <span class="col-md-4 label-name">STATUS</span> <span class="col-md-8 label-default pull-right" id="acc_stat"></span> </li>
    <li class="list-group-item"> <span class="col-md-4 label-name">LOAD <span class="badge badge-info">C</span></span> <span class="col-md-8 label-default pull-right" id="acc_stat"></span> </li>
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
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <button style="height: 68px !important; display: inline-block !important;" type="button" class="btn btn-default">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </span>
        </div>
        <ul class="nav nav-tabs tabs-left ar-tab nav-tabs-label" style="min-height: 350px;">

            <li class="active">
                <a id="tab_ar" data-module="<?php echo $navid;?>" href="#billar" data-toggle="tab" aria-expanded="true">
                    <span class="label bg-grey-steel bg-font-grey-steel">EN</span> Billing AR
                </a>
            </li>

            <li class="">
                <a href="#readinghist" data-module="<?php echo $navid;?>" data-toggle="tab" aria-expanded="false">
                    <span class="label bg-grey-steel bg-font-grey-steel">F2</span> Reading History
                </a>
            </li>

            <li class="">
                <a id="tab_othinfo" data-module="<?php echo $navid;?>" href="#acctdetails" data-toggle="tab" aria-expanded="false">
                    <span class="label bg-grey-steel bg-font-grey-steel">F3</span> Other Information
                </a>
            </li>


            <li class="">
                <a href="#ticket" data-module="<?php echo $navid;?>" data-toggle="tab" aria-expanded="false">
                    <span class="label bg-grey-steel bg-font-grey-steel">F4</span> Complaints <span style="margin-top: 2px;" class="badge badge-danger pull-right"></span>
                </a>
            </li>


            <li class="">
                <a href="#pninq" data-module="<?php echo $navid;?>" data-toggle="tab" aria-expanded="false">
                    <span class="label bg-grey-steel bg-font-grey-steel">F5</span> P.N. Inquiry
                </a>
            </li>

            <li class="">
                <a href="#tagging" data-module="<?php echo $navid;?>" data-toggle="tab" aria-expanded="false">
                    <span class="label bg-grey-steel bg-font-grey-steel">F6</span> Account Tagged <span style="margin-top: 2px;" class="badge badge-danger pull-right">2</span>
                </a>
            </li>

            <li class="">
                <a href="#meterhist" data-module="<?php echo $navid;?>" data-toggle="tab" aria-expanded="false">
                    <span class="label bg-grey-steel bg-font-grey-steel">F9</span> Meter History
                </a>
            </li>

            <li class="">
                <a href="#payments" data-module="<?php echo $navid;?>" data-toggle="tab" aria-expanded="false">
                    <span class="label bg-grey-steel bg-font-grey-steel">F10</span> Payments
                </a>
            </li>

        </ul>
    </div>
    <div class="col-md-10">
        <div class=" tab-content">

            <div class="tab-pane fade in" id="ticket">
                <div class="tabbable-line tabs-below">
                    <div class="tab-content" style="min-height: 400px;">
                        <div class="tab-pane fade in active" id="new_ticket">
                            <form class="" action="<?php echo base_url('cwdo/newticket'); ?>" method="post" id="frm_new_ticket" enctype="multipart/form-data">
                            <div class="row">

                                <input type="hidden" class="" name="acctid" id="compacctid"  value="1" />
                                <div class="col-md-5">
                                    <h4>Complainants
                                        <div class="input-group pull-right">
                                            <label for="complainants" class="text-primary">Same as account
                                                <input type="checkbox" class="icheck" name="complainants" id="complainants"  value="1" />
                                            </label>
                                        </div>
                                    </h4>
                                    <hr>
                                    <div class="form-group complainants-input">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Last Name</label>
                                                <input class="form-control" placeholder="Lastname" name="lastname" />
                                            </div>
                                            <div class="col-md-5">
                                                <label>First Name</label>
                                                <input class="form-control" placeholder="Firstname" name="firstname" />
                                            </div>
                                            <div class="col-md-3">
                                                <label>Middle N.</label>
                                                <input class="form-control" placeholder="Middlename" name="middlename" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <div class="col-md-7 complainants-input">
                                            <label class="">Address</label>
                                            <input name="address" class="form-control" placeholder="Address..."/>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="">Contact</label>
                                            <input name="contact" class="form-control" placeholder="Contact..."/>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="">Remarks</label>
                                        <input name="remarks" class="form-control" placeholder="Remarks..." />
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label>Complain <span class="required"></span></label>
                                            <input class="form-control" name="tickettype" id="select_ticket"/>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Particular <span class="required"></span></label>
                                            <input placeholder="Particular select.." class="form-control" name="ticketpart" id="select_ticketpart" readonly/>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label>Priority <span class="required"></span></label>
                                        <input class="form-control" name="priority" id="select_priority"/>

                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div  style="height: 350px;">
                                        <h4>Tagging

                                            <div class="input-group pull-right">
                                                <label for="reqverification" class="text-danger">Request for Verification
                                                    <input type="checkbox" class="icheck" name="reqverification" id="reqverification" value="1" />
                                                </label>
                                            </div>
                                        </h4>
                                        <hr>
                                        <div class="form-group billing hidden">
                                            <table class="table table-hover table-condensed table-bordered table-striped tbl-xs" id="tbl_billhist_rv">
                                                <thead>
                                                <th>Month</th>
                                                <th>Year</th>
                                                <th>KWH Used</th>
                                                <th>Prs. Read</th>
                                                <th>Prv. Read</th>
                                                <th>Prs. Date</th>
                                                <th>Prv. Date</th>
                                                <th>Mtr No.</th>
                                                <th>Mtr Serial</th>
                                                <th>Batch</th>
                                                <th></th>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div class="form-group payments  hidden">
                                            <label>OR No.</label>
                                            <input class="form-control" id="orno" placeholder="88888.." name="orno" />
                                        </div>
                                        <div class="form-group services  hidden">
                                            <label>Tag Personel</label>
                                            <input class="form-control" id="empid" placeholder="Employee.." name="empid" />
                                            <label>Comment</label>
                                            <textarea class="form-control" id="empcomp" placeholder="Comment.." name="empcomp"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-8">
                                            <label class="control-label col-md-4">Attachedments</label>
                                            <div class="col-md-3" style="z-index: 3000">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div class="form-control uneditable-input input-fixed" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                            <span class="fileinput-filename"> </span>
                                                        </div>
                                                        <span class="input-group-addon btn default btn-file">
                                                            <span class="fileinput-new"><i class="fa fa-file"></i></span>
                                                            <span class="fileinput-exists"> <i class="fa fa-refresh"></i> </span>
                                                            <input type="file" name="attachedment"> </span>
                                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">

                                            <div class="btn-group pull-right">

                                                <button type="reset" class="btn btn-default" id="reset">Reset</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </form>
                        </div>

                        <div class="tab-pane fade in" id="ticket_list">
                            <table class="table table-hover table-condensed table-bordered table-striped" id="tbl_ticket_history">
                                <thead>
                                <th></th>
                                <th>Ticket #</th>
                                <th>Complaints</th>
                                <th>Particular</th>
                                <th>Ramarks</th>
                                <th>Created by</th>
                                <th>Date Created</th>
                                <th>Status</th>
                                <th></th>
                                </thead>
                            </table>
                        </div>
                    </div>



                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#new_ticket" data-toggle="tab"><i class="fa fa-file fa-fw"></i> New Ticket</a>
                        </li>
                        <li class="">
                            <a href="#ticket_list" data-toggle="tab"><i class="fa fa-reorder fa-fw"></i> Ticket History <span style="margin-top: 2px; margin-left: 5px;" class="badge badge-danger pull-right">1</span></a>
                        </li>

                    </ul>

                </div>
            </div>

            <div class="tab-pane active fade in" id="meterhist">
                <h4>Meter History</h4>
                <table  width="100%" class="table table-hover table-bordered tbl-xs" id="tbl_meter_history">
                    <thead>
                    <th>Service No.</th>
                    <th>N.MTR</th>
                    <th>Date</th>
                    <th>Form No.</th>
                    <th>Name</th>
                    <th>Tran. Code</th>
                    <th>Serial</th>
                    <th>Brand</th>
                    <th>Type</th>
                    <th>Volts</th>
                    <th>Amps</th>
                    <th></th>
                    </thead>

                    <tbody>

                    </tbody>
                </table>
            </div>
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
                    <a id="printstatementbtn" class="btn btn-default pull-right" >
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
                <h4>Pay Applied</h4>
                <table width="100%" class="table table-hover table-condensed table-striped table-bordered tbl-sm" id="tbl_payments_applied">
                    <thead>
                    <th>#</th>
                    <th>OR No.</th>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Amt Paid</th>
                    <th>Interest Paid</th>
                    <th>Date Paid</th>
                    <th>Collected By</th>
                    </thead>
                    <tbody></tbody>
                </table>
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

            <div class="tab-pane fade in" id="tagging">
                <div class="portlet-body">
                    <p class="margin-top-10">Tagging</p>
                    <div class="row">
                        <div id="tag_container">
                            <h4><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading tagging..</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="tagging_details" >
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <form id="frm_tagging" method="post" action="<?php echo base_url('systems/savetagging'); ?>" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Modal Title</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn green"><i class="fa fa-save"></i> Save</button>
                </div>
                <div class="modal-body">Form Content</div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/fancybox/source/jquery.fancybox.js?v=2.1.3"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.5"></script>

<!-- Resources -->

<script src="<?php echo base_url();?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
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