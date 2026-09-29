<?php
$ids = $this->uri->segment(4);
$emp_info = $this->model_query->emp_info($ids);
$payroll_info = $this->model_query->payroll_info($ids);
//initialize and check empty variables here

$basic = isset($payroll_info->current_salary) ? $payroll_info->current_salary : '';
$cola = isset($payroll_info->cola)? $payroll_info->cola : '';
$other_earnings = isset($payroll_info->other_earnings) ? $payroll_info->other_earnings : '';
$holiday_pay = isset($payroll_info->holidaypay) ? $payroll_info->holidaypay : '';
$overtime_pay = isset($payroll_info->otpay) ? $payroll_info->otpay : '';

$night_differential = isset($payroll_info->ndpay) ? $payroll_info->ndpay : '';
$adjustments = isset($payroll_info->adjustment_earnings) ? $payroll_info->adjustment_earnings : '';

$sss_premium = isset($payroll_info->sss_employee) ? $payroll_info->sss_employee : '';
$sss_loans = isset($payroll_info->sss_loan) ? $payroll_info->sss_loan : '';
$pagibig_premium = isset($payroll_info->pagibig_employee)?$payroll_info->pagibig_employee :'';
$pecewa = isset($payroll_info->pagibig_loan)?$payroll_info->pagibig_loan:'';
$cooperative = isset($payroll_info->union_fees)?$payroll_info->union_fees:'';
$cooperative_loan = isset($payroll_info->union_loan)?$payroll_info->union_loan:'';
$hmo_deduction = isset($payroll_info->hmo_employee)?$payroll_info->hmo_employee:'';
$electric_bills = isset($payroll_info->coop_loan)?$payroll_info->coop_loan:'';
$other_deduction = isset($payroll_info->other_deductions)?$payroll_info->other_deductions:'';
$leave_without_pay = isset($payroll_info->electric_bills)?$payroll_info->electric_bills:'';
$withholding_tax = isset($payroll_info->withholding_tax)?$payroll_info->withholding_tax:'';

$netpay = isset($payroll_info->netpay)?$payroll_info->netpay:'';

?>

        <a class="btn btn-default pull-right" class="pull-right" href="<?php echo base_url('module/'.$this->uri->segment(2).'/list'); ?>"> <i class="fa fa-backward"> </i> Back To List</a>
        <h3 class="page-title">
            <i class="fa fa-user fa-fw"></i> <span class="caption-subject font-green-sharp bold uppercase"><?php echo $emp_info->firstname . " " . $emp_info->lastname; ?> </span> <small>payslip</small>
        </h3>
        <div class="row">
            <div class="col-md-6">
            <div class="portlet box green-meadow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="glyphicon glyphicon-plus"></i>Earnings
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="" title="">
                        </a>
                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                        </a>
                        <a href="" class="fullscreen" data-original-title="" title="">
                        </a>
                        <a href="javascript:;" class="reload" data-original-title="" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <ul class="list-group summary column no-border list-group-sm">
                        <li class="list-group-item">
                            <span class=" label-name col-md-5">Basic </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $basic ?></span></span>
                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-5">COLA </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $cola ?></span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-5">Others </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $other_earnings ?></span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-5">Holiday Pay </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $holiday_pay ?></span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-5">Overtime Pay </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $overtime_pay ?></span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-5">Night Different </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $night_differential ?></span></span>

                        </li>
                        <li class="list-group-item">
                            <span class=" label-name col-md-5">Adjustments</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $adjustments  ?></span></span>

                        </li>
                    </ul>
                    <hr>
                    <ul class="list-unstyled amounts">
                       
                        <li>
                            <strong>Net Income: <?php echo $netpay ?> </strong> 
                        </li>
                    </ul>
                    <br>
                    <a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
                        Print <i class="fa fa-print"></i>
                    </a>
                </div>
            </div>
            </div>


            <div class="col-md-6">
                <div class="portlet box green-meadow">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="glyphicon glyphicon-minus"></i>Deductions
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title="">
                            </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                            </a>
                            <a href="" class="fullscreen" data-original-title="" title="">
                            </a>
                            <a href="javascript:;" class="reload" data-original-title="" title="">
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <ul class="list-group summary column no-border list-group-sm">

                            <li class="list-group-item">
                                <span class=" label-name col-md-5">SSS Premium </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $sss_premium  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">SSS Loans </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $sss_loans  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">PAG-IBIG PREM </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $pagibig_premium  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">PECEWA </span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $pecewa  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">Cooperative</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $cooperative  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">Cooperative Loan</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $cooperative_loan  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">HMO Deduction</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $hmo_deduction  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">Electric bills</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $electric_bills  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">Others</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $other_deduction  ?></span></span>

                            </li>
                            <li class="list-group-item">
                                <span class=" label-name col-md-5">Leave Without Pay</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $leave_without_pay  ?></span></span>

                            </li>
                            <li class="list-group-item">

                                <span class=" label-name col-md-5">Withholding Tax</span><span class="label label-default col-md-7 pull-right"><span id="name"><?php echo $withholding_tax  ?></span></span>

                            </li>

                        </ul>
                    </div>
                </div>
            </div>


        </div>

   