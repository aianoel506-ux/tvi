

<h3>Print Employee Paylips</h3>
<div class="portlet light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#batch" data-id="2" data-toggle="tab"> Per Payclass </a>
            </li>
            <li class="">
                <a href="#emp" data-id="4" data-toggle="tab" aria-expanded="true"> Per Employee </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <div class="tab-content">
            <div class="tab-pane fade in active" id="batch">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well">

                            <form id="frm_get_payslip_data" action="<?php echo base_url() ?>payroll/getpayslippreview" method="post" >
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="payclass">Pay Class</label>
                                            <input name="payclass" id="payclasscombo" class="form-control" required>
                                            <!--
                                            <select name="payclass" id="payclasscombo" class="form-control" required>
                                                <option></option>
                                                <option value="128">Rank and File</option>
                                                <option value="1">Confidential</option>
                                                <option value="3077">Tier 1</option>
                                                <option value="3078">Tier 2</option>
                                            </select>-->
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                            <div width="50%">
                                                <label for="month">Month</label>
                                                <input name="month" id="month" class="form-control" required>
                                                <!--<select name="month" id="month" class="form-control" required>
                                <option></option>
                                <?php
                                                for($month = 1;$month<=12;$month++){
                                                    echo "<option value=".$month.">".date('F', mktime(0, 0, 0, $month, 1))."</option>";
                                                }
                                                ?>
                            </select>-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div width="50%">
                                                <label for="year">Year</label>
                                                <input name="year" id="year" class="form-control" value="<?php echo date('Y');?>" onclick="this.select();" placeholder="Provide Year..." required />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="payrollperiod">Payroll Period</label>
                                            <select name="payrollperiod"  id="payrollperiod" class="form-control" required>
                                                <option></option>
                                                <option value="1">1st Half</option>
                                                <option value="2">2nd Half</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="btn-group pull-right">
                                            <button type="submit" style="margin-top: 21px;" class="btn btn-primary">Get Data</button>
                                            <button style="margin-top: 21px !important;" class="btn btn-default" type="button" id="payslip"><i class="fa fa-print"></i> Print Payslip</button>
                                            <button style="margin-top: 21px !important;" class="btn btn-success" type="button" id="btn_send_payslips"><i class="fa fa-paper-plane"></i> Send</button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered table">
                            <div class="portlet-title">
                                <div class="caption">
                                    <h3 class="color-blue"><i class="fa fa-search"></i> Pay Slip Preview</h3>
                                </div>
                                <div class="tools">

                                </div>
                            </div>
                            <div class="portlet-body">
                                <table style="width: 100%;" class="table table-bordered table-condensed table-hover" id="payrollreportstbl">
                                    <thead>
                                    <th></th>
                                    <th><i class="fa fa-reorder"></i></th>
                                    <th>Emp Code</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Basic</th>
                                    <th>Deductions</th>
                                    <th>Earnings</th>
                                    <th>Tax</th>
                                    <th>Net Pay</th>
                                    <th>Control</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="emp">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well">
                            <form id="frm_get_emp_payslip_data" action="<?php echo base_url() ?>payroll/getemppayslippreview" method="post" >
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Employee Name</label>
                                            <input id="empid" name="empid" class="form-control" required>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>From</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Month</span>
                                                <input name="from_month" id="from_month" class="form-control" placeholder="Month..." required>
                                            </div>
                                            <br>
                                            <div class="input-group">
                                                <span class="input-group-addon">Paytype</span>
                                                <select name="from_paytype"  id="from_paytype" class="form-control" required>
                                                    <option></option>
                                                    <option value="1">1st Half</option>
                                                    <option value="2">2nd Half</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>To</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Month</span>
                                                <input name="to_month" id="to_month" class="form-control" placeholder="Month..." required>
                                            </div>
                                            <br>
                                            <div class="input-group">
                                                <span class="input-group-addon">Paytype</span>
                                                <select name="to_paytype"  id="to_paytype" class="form-control" required>
                                                    <option></option>
                                                    <option value="1">1st Half</option>
                                                    <option value="2">2nd Half</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>From</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Date</span>
                                                <input type="date" name="datefrom" id="datefrom" class="form-control" placeholder="Select Date..." required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>To</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Date</span>
                                                <input type="date" name="dateto" id="dateto" class="form-control" placeholder="Select Date..." required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="margin-top: 20px">
                                        <div class="btn-group pull-right">
                                            <button type="submit" class="btn btn-primary">Get Data</button>
                                            <button type="buttom" class="btn btn-default" id="print_emp_payslips"><i class="fa fa-print"></i> Print</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered table">
                            <div class="portlet-title">
                                <div class="caption">
                                    <h3 class="color-blue"><i class="fa fa-search"></i> Pay Slip Summary</h3>
                                </div>
                                <div class="tools">

                                </div>
                            </div>
                            <div class="portlet-body">
                                <table style="width: 100%;" class="table table-bordered table-condensed table-hover" id="emppayrollreportstbl">
                                    <thead>
                                    <th><i class="fa fa-reorder"></i></th>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Paytype</th>
                                    <th>Basic</th>
                                    <th>Deductions</th>
                                    <th>Earnings</th>
                                    <th>Tax</th>
                                    <th>Net Pay</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>
<script src="<?php echo base_url() ?>assets/pages/payroll/main.js"></script>
<script src="<?php echo base_url() ?>assets/pages/payroll/payslip.js"></script>
<script>
    PAYROLL.init();
    PAYROLL.report();
    PAYSLIP.init();
</script>

