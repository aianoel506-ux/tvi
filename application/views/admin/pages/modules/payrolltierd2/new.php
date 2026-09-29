<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css"/>
<style>
    .form-md-line-input {
        position: relative !important;
    }
    .form-md-line-input .fileinput .input-group-addon{
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }
    .form-md-line-input .select2-container{
        margin-bottom: 0px !important;
    }
    #processpayrollbtn{
        margin-top: 30px;
    }
    .listtabs{
        height: 87px !important;
    }
    .listtabs li{
        height: 50px !important;
    }
    .listtabs li a{
        height: 80px !important;
    }



</style>

<h4 class="pull-left">
    <i class="fa fa-edit"></i>
    <span class="caption-subject font-green-sharp bold uppercase">Payroll</span>
    <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
</h4>
<div class="row"><div class="col-md-12">



        <form class="form-horizontal" id="frm_process_payroll" action="<?php echo base_url('payroll/emplist'); ?>" method="post">
            <?php pages_parent_navigation($navid, array('payclass' => 3078)); ?>
            <?php if(user_id() == 1) { ?>

            <div class="tabbable-line">
                <ul class="nav nav-tabs" id="payroll_data_tab">
                    <li class="active">
                        <a href="#payroll" data-toggle="tab">Payroll</a>
                    </li>
                    <li class="">
                        <a href="#reg_payroll" data-toggle="tab">Payroll Register</a>
                    </li>
                    <li class="">
                        <a href="#reg_earnigs" data-toggle="tab">Earnings Register</a>
                    </li>
                    <li class="">
                        <a href="#reg_deduction" data-toggle="tab">Deduction Register</a>
                    </li>
                    <li class="">
                        <a href="#reg_overtime" data-toggle="tab">Overtime Register</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane " id="reg_payroll">
                    <!-- <table id="tbl_registers" class="table table-hover table-bordered table-condensed">

                     </table> -->
                    <!-- <button id="printpayreg" class="btn btn-primary btn-xs">PRINT PAYROLL REGISTER</button>
                     <button id="printpayregbyemp" class="btn btn-primary btn-xs">PRINT PAYROLL REGISTER BY EMPLOYEE</button> -->

                    <table class="table table-bordered table-condensed table-hover tbl-xs" id="payrollregpreviewtrn">
                        <thead>
                        <tr>
                            <th></th>
                            <th>DEPT CODE</th>
                            <th>GROSS EARNINGS</th>
                            <th>TOTAL DEDN</th>
                            <th>TOTAL NET</th>
                            <th>SSS CONT</th>
                            <th>SSS LOAN</th>
                            <th>HDMF CONT</th>
                            <th>HDMF LOAN</th>
                            <th>PECEWA LOAN</th>
                            <th>COOP LOAN</th>
                            <th>PAGIBIG AD</th>
                            <th>OTHER DEDN</th>
                            <th>HMO DEDN</th>
                            <th>DED A</th>
                            <th>ELECTRIC BILL</th>
                            <th>MEM INS</th>
                            <th>LWOP</th>
                            <th>BASE TAX</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td>Total Result</td>
                            <td class="number bold text-info" id="resultgrossearningsprev"></td>
                            <td class="number bold text-info" id="resulttotaldednprev"></td>
                            <td class="number bold text-info" id="resulttotalnetprev"></td>
                            <td class="number bold text-info" id="resultssscontprev"></td>
                            <td class="number bold text-info" id="resultsssloanprev"></td>
                            <td class="number bold text-info" id="resulthdmfcontprev"></td>
                            <td class="number bold text-info" id="resulthdmfloanprev"></td>
                            <td class="number bold text-info" id="resultpecewaloanprev"></td>
                            <td class="number bold text-info" id="resultcooploanprev"></td>
                            <td class="number bold text-info" id="resultpagibigaddprev"></td>
                            <td class="number bold text-info" id="resultotherdeductionprev"></td>
                            <td class="number bold text-info" id="resulthmodednprev"></td>
                            <td class="number bold text-info" id="resultdedaprev"></td>
                            <td class="number bold text-info" id="resultelectricbillprev"></td>
                            <td class="number bold text-info" id="resultmeminsprev"></td>
                            <td class="number bold text-info" id="resultlwopprev"></td>
                            <td class="number bold text-info" id="resultbasetaxprev"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="tab-pane " id="reg_earnigs">
                    <!--<table id="tbl_registers" class="table table-hover table-bordered table-condensed">
                    </table> -->
                    <!-- <button id="printearregdepttotals" class="btn btn-primary btn-xs">PRINT EARNINGS REGISTER</button>
                     <button id="printearregbyemp" class="btn btn-primary btn-xs">PRINT EARNINGS REGISTER BY EMPLOYEE</button> -->
                    <table class="table table-bordered table-condensed table-hover tbl-xs" id="earningregpreviewtrn">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="changablecol">401</th>
                            <th>410</th>
                            <th class="changablecol">401</th>
                            <th>410</th>
                            <th class="changablecol">401</th>
                            <th class="changablecol">401</th>
                            <th class="changablecol">401</th>
                            <th class="changablecol">401</th>
                            <th class="changablecol">401</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>DEPT CODE</th>
                            <th>BASIC RATE</th>
                            <th>COLA</th>
                            <th>TRANS ALLW</th>
                            <th>RICE SUBSI</th>
                            <th>HOLIDAY PAY</th>
                            <th>NITE DIFF</th>
                            <th>OT PAY</th>
                            <th>ACTING ALLW</th>
                            <th>OTHERADD</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td>Total Result</td>
                            <td class="number bold text-info" id="totaleaningbasicrateprev"></td>
                            <td class="number bold text-info" id="totalearningcolaprev"></td>
                            <td class="number bold text-info" id="totalearningtransallwprev"></td>
                            <td class="number bold text-info" id="totalearningricesubsiprev"></td>
                            <td class="number bold text-info" id="totalearningholidaypayprev"></td>
                            <td class="number bold text-info" id="totalearningnitediffprev"></td>
                            <td class="number bold text-info" id="totalearningotpayprev"></td>
                            <td class="number bold text-info" id="totalearningactingallwprev"></td>
                            <td class="number bold text-info" id="totalearningotheraddprev"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="tab-pane " id="reg_deduction">
                    <!-- <table id="tbl_registers" class="table table-hover table-bordered table-condensed">
                     </table> -->
                    <!-- <button id="printdednregdepttotals" class="btn btn-primary btn-xs">PRINT DEDUCTIONS REGISTER</button>
                     <button id="printdednregbyemp" class="btn btn-primary btn-xs">PRINT DEDUCTIONS REGISTER BY EMPLOYEE</button> -->
                    <table class="table borderless table-condensed table-hover tbl-xs" id="deductionsregpreviewtrn">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>256</th>
                            <th>262</th>
                            <th>260/408/261</th>
                            <th>274</th>
                            <th>264</th>
                            <th>265</th>
                            <th>261</th>
                            <th></th>
                            <th>405</th>
                            <th>178</th>
                            <th>175</th>
                            <th>MEM INS</th>
                            <th class="changablecol">401</th>
                            <th>245</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>DEPT CODE</th>
                            <th>SSS CONT</th>
                            <th>SSS LOAN</th>
                            <th>HDMF CONT</th>
                            <th>HDMF LOAN</th>
                            <th>PECEWA LOAN</th>
                            <th>COOP LOAN</th>
                            <th>PAGIBIG AD</th>
                            <th>OTHER DEDN</th>
                            <th>HMO DEDN</th>
                            <th>DED A</th>
                            <th>ELECT BILL</th>
                            <th>MEM INS</th>
                            <th>LWOP</th>
                            <th>BASE TAX</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                        <tr>
                            <td ></td>
                            <td >TOTAL RESULT</td>
                            <td class="text-danger number" id="totaldeductionssscontprev"></td>
                            <td class="text-danger" id="totaldeductionsssloanprev"></td>
                            <td class="text-danger" id="totaldeductionhdmfcontprev"></td>
                            <td class="text-danger" id="totaldeductionhdmfloanprev"></td>
                            <td class="text-danger" id="totaldeductionpecewaloanprev"></td>
                            <td class="text-danger" id="totaldeductioncooploanprev"></td>
                            <td class="text-danger" id="totaldeductionpagibigadprev"></td>
                            <td class="text-danger" id="totaldeductionotherdednprev"></td>
                            <td class="text-danger" id="totaldeductionhmodednprev"></td>
                            <td class="text-danger" id="totaldeductiondedaprev"></td>
                            <td class="text-danger" id="totaldeductionelectbillprev"></td>
                            <td class="text-danger" id="totaldeductionmeminsprev"></td>
                            <td class="text-danger" id="totaldeductionlwopprev"></td>
                            <td class="text-danger" id="totaldeductionbasetaxprev"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="tab-pane " id="reg_overtime">
                    <!-- <table id="tbl_registers" class="table table-hover table-bordered table-condensed">
                     </table> -->
                    <!--  <button id="printotregdepttotals" class="btn btn-primary btn-xs">PRINT OVERTIME REGISTER</button>
                      <button id="printotregbyemp" class="btn btn-primary btn-xs">PRINT OVERTIME REGISTER BY EMPLOYEE</button> -->
                    <table class="table table-bordered table-condensed table-hover tbl-xs" id="overtimeregpreviewtrn">
                        <thead>
                        <tr>
                            <th></th>
                            <th>DEPT CODE</th>
                            <th>NDOT 8 Hrs</th>
                            <th>NDOT 8 Pay</th>
                            <th>OT Hrs</th>
                            <th>125%</th>
                            <th>130%</th>
                            <th>150%</th>
                            <th>160%</th>
                            <th>180%</th>
                            <th>210%</th>
                            <th>230%</th>
                            <th>260%</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th></th>
                            <th>Total Result</th>
                            <th id="totalndothrsprev">0.00</th>
                            <th id="totalndotpayprev">0.00</th>
                            <th id="totalothrsprev">0.00</th>
                            <th id="totalone25prev">0.00</th>
                            <th id="totalone30prev">0.00</th>
                            <th id="totalone50prev">0.00</th>
                            <th id="totalone60prev">0.00</th>
                            <th id="totalone80prev">0.00</th>
                            <th id="totaltwo10prev">0.00</th>
                            <th id="totaltwo30prev">0.00</th>
                            <th id="totaltwo60prev">0.00</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
                <div class="tab-pane " id="reg_annual">
                    <table id="tbl_reg_annual" class="table table-hover table-bordered table-condensed">

                    </table>
                </div>

                <div class="tab-pane active" id="payroll">
                    <!--<button type="button" id="expandall" class="btn btn-sm btn-primary pull-right"><i class="fa fa-expand"></i> Expand all</button> -->
                    <table class="table table-responsive table-hover table-striped table-condensed table-bordered tbl-sm payroll_table" id="payroll_table" width="100%">
                        <thead>
                        <th></th>
                        <th><i class="fa fa-reorder"></i></th>
                        <th>Emp. Code</th>
                        <th> Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Dept.</th>
                        <th>Basic</th>
                        <th>Earnings</th>
                        <th>Loans</th>
                        <th>Prem.</th>
                        <th>TAX</th>
                        <!-- <th>Late (m)</th>
                         <th>LWOP</th> -->
                        <th>Deductions</th>
                        <th>Net</th>
                        <th>Status</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <hr>


                    <div class="row">
                        <div class="col-md-3">
                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Loans</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalloanssum">00.00</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Premiums</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalpremiumssum">00.00</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total TAX</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totaltaxsum">00.00</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Deductions</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totaldeductionssum">00.00</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-3">

                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Budget</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalbudget">00.00</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Budget Balance</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalbudgetbalance">00.00</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-3">

                            <ul class="list-group summary column no-border list-group-sm">
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Earnings</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalearningssum">00.00</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Net</span>
                                    <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalnetsum">00.00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php  } ?>
        </form>
    </div>
</div>
<hr>
<hr>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/payroll/main.js"></script>
<script type="text/javascript">
    PAYROLL.init(3078);
    PAYROLL.initpreviewtrn(3078);
</script>
