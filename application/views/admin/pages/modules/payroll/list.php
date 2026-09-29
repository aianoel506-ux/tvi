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
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
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



        <div class="row">
            <div class="col-md-12">
                <div class="portlet light table">
                    <div class="portlet-title">

                        <h4 class="pull-left">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Payroll</span>
                            <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
                        </h4>
                    </div>
                    <div class="portlet-body ">
                        <div class="tab-content">

                            <div class="tab-pane fade in active" id="payroll_main">
                              <form id="frm_process_payroll" action="<?php echo base_url('payroll/emplist'); ?>" method="post">
                                    <input type="hidden" name="process" value="1" />
                                    <input type="hidden" name="viewtype" value="1" />
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <small>Period Year</small>
                                                    <input class="form-control input-sm" id="periodyear" name ="year" value="<?php echo date("Y"); ?>" />
                                                </div>
                                                <div class="col-md-3">
                                                    <small>Period Month</small>
                                                    <input class="form-control input-sm" id="select2month" name="month"  value="<?php echo (int) date('m'); ?>" />
                                                </div>
                                                <div class="col-md-3">
                                                    <small>Payment Type</small>


                                                    <select disabled required class="form-control input-sm" id="select2paytype" name="paytype">
                                                        <option></option>
                                                        <option value="1" selected>1st half</option>
                                                        <option value="2">2nd half</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <small>View by Department</small>
                                                    <input class="form-control input-sm" id="deptselect" name="dept" placeholder="Select dept.." />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                        <span class="pull-right tabbable-line">
                                            <?php echo draw_tab('EMPAYCLASS', false, false, false, true); ?>
                                        </span>
                                        </div>


                                        <div class="tab-content">



                                            <div class="tab-pane fade in active" id="all">
                                                <div class="col-md-12">
                                                    <hr>
                                                    <table class="table table-responsive table-hover table-striped table-condensed table-bordered tbl-sm" id="payroll_table" width="100%">
                                                        <thead>
                                                        <th><i class="fa fa-reorder"></i></th>
                                                        <th>Emp. Code</th>
                                                        <th> Last Name</th>
                                                        <th>First Name</th>
                                                        <th>Middle Name</th>
                                                        <th>Department</th>
                                                        <th>Basic</th>
                                                        <th>Earnings</th>
                                                        <th>Loans</th>
                                                        <th>Premiums</th>
                                                        <th>TAX</th>
                                                        <th>Deduction</th>
                                                        <th>Net</th>
                                                        <th>Status</th>
                                                        <!-- <th><i class="fa fa-wrench"></i></th> -->
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                    <hr>
                                                    <div class="col-md-12">
                                                        <h3 col-md-3><i class="fa fa-file-o fa-fw"></i> Summary</h3>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <ul class="list-group summary column">
                                                                    <li class="list-group-item"> Total Budget: <span class="label label-default pull-right" id="daterange">0.00</span> </li>
                                                                    <li class="list-group-item"> Total Expense: <span class="label label-default pull-right" id="totalempnet">0.00</span> </li>
                                                                </ul>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <ul class="list-group summary column">
                                                                    <li class="list-group-item"> Total Budget (Rank and File): <span class="label label-default pull-right" id="daterange">0.00</span> </li>
                                                                    <li class="list-group-item"> Total Budget (Confidential): <span class="label label-default pull-right" id="daterange">0.00</span> </li>
                                                                </ul>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <ul class="list-group summary column">
                                                                    <li class="list-group-item"> Total Payable (Rank and File): <span class="label label-default pull-right" id="daterange">0.00</span> </li>
                                                                    <li class="list-group-item"> Total Payable (Confidential): <span class="label label-default pull-right" id="daterange">0.00</span> </li>
                                                                </ul>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <ul class="list-group summary column">
                                                                    <li class="list-group-item"> Total Deduction (Rank and File): <span class="label label-default pull-right" id="daterange">0.00</span> </li>
                                                                    <li class="list-group-item"> Total Deduction (Confidential): <span class="label label-default pull-right" id="daterange">0.00</span> </li>
                                                                    <li class="list-group-item">Total Deduction: <span class="label label-default pull-right" id="totaldeduction">0.00</span> </li>
                                                                </ul>
                                                            </div>


                                                        </div>
                                                        <div class="row margin-bottom-20">
                                                            <div class="col-md-4">
                                                                <button type="button" id="processpayroll" class="btn btn-primary mt-sweetalert" data-title="Are you sure you want to process payroll?" data-message="Confirm Action" data-type="info" data-show-confirm-button="true" data-confirm-button-class="btn-success" data-show-cancel-button="true" data-cancel-button-class="btn-default" data-close-on-confirm="false" data-close-on-cancel="false" data-confirm-button-text="Yes" data-cancel-button-text="No" data-popup-title-success="Thank you" data-popup-message-success="Payroll Successfully Processed" data-popup-title-cancel="Cancelled" ><i class="fa fa-forward fa-fw"></i> Process Payroll</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                               </form>
                            </div>

                            <div class="tab-pane fade in" id="meter_reader">

                                <div class="col-lg-12 col-md-12 col-xs-12">
                                    <table class="table table-responsive table-hover table-striped table-condensed table-bordered tbl-sm" id="meter_reader_payroll">
                                        <thead>
                                        <th>#</th>
                                        <th>Emp ID</th>
                                        <th>Fullname</th>
                                        <th>GDLB</th>
                                        <th>Regular</th>
                                        <th>Special</th>
                                        <th>Regular Rate</th>
                                        <th>Special Rate</th>
                                        <th>Regular Deduction</th>
                                        <th>Special Deduction</th>
                                        <th>Amount</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-lg-3 col-md-3 col-xs-3">

                                    <div class="portlet light">
                                        <div class="portlet-title">
                                            <div class="caption">Summary</div>
                                        </div>
                                        <div class="portlet-body">

                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="col-md-8">
                                                        <span class="caption-subject font-green-sharp bold uppercase">GDLB :</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="caption-helper" id="total_gdlbval">0</span>
                                                    </div>


                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="col-md-8">
                                                        <span class="caption-subject font-green-sharp bold uppercase">REGULAR :</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="caption-helper" id="total_regval">0</span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="col-md-8">
                                                        <span class="caption-subject font-green-sharp bold uppercase">SPECIAL :</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="caption-helper" id="total_spval">0</span>
                                                    </div>
                                                </li>

                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="col-md-8">
                                                        <span class="caption-subject font-green-sharp bold uppercase">Total Reading :</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="caption-helper" id="total_reading">0</span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="col-md-8">
                                                        <span class="caption-subject font-green-sharp bold uppercase">Amount :</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="caption-helper" id="amountval">0</span>
                                                        <input type="hidden" id="hiddenamountval" />
                                                    </div>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="col-md-8">
                                                        <span class="caption-subject font-green-sharp bold uppercase">Deduction :</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="caption-helper" id="total_deduction" style="color: red;">0</span>
                                                        <input type="hidden" id="hiddentotal_deduction" />
                                                    </div>
                                                </li>

                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="col-md-8">
                                                        <span class="caption-subject font-green-sharp bold uppercase">Total AMOUNT :</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="caption-helper" id="total_amountval" style="color: green;font-weight: bold;">0</span>
                                                        <input type="hidden" id="hiddentotal_amountval" />
                                                    </div>
                                                </li>

                                            </ul>
                                            <div class="col-md-8">
                                                <button class="btn btn-default mt-sweetalert" data-title="Do you agree to the Terms and Conditions?" data-message="Duis mollis, est non commodo luctus, nisi erat porttitor ligula, mattis consectetur purus sit amet eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum." data-type="info" data-show-confirm-button="true" data-confirm-button-class="btn-success" data-show-cancel-button="true" data-cancel-button-class="btn-default" data-close-on-confirm="false" data-close-on-cancel="false" data-confirm-button-text="Yes, I agree" data-cancel-button-text="No, I disagree" data-popup-title-success="Thank you" data-popup-message-success="You have agreed to our Terms and Conditions" data-popup-title-cancel="Cancelled" data-popup-message-cancel="You have disagreed to our Terms and Conditions">Agree to Terms &amp; Conditions</button>


                                            </div>
                                            <div class="col-md-4">

                                            </div>

                                        </div>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>


                </div>
            </div>



        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-sweetalert/ui-sweetalert.min.js" type="text/javascript"></script>


<script src="<?php echo base_url(); ?>assets/pages/payroll/main.js"></script>


<script type="text/javascript">
   PAYROLL.init();
</script>
