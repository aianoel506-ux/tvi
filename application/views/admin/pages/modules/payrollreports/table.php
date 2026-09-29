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

<?php

?>

<div class="portlet light bordered table">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <h4 class="font-green-haze bold"><i class="fa fa-file-text-o"></i> Reports</h4>
        </div>
        <ul class="nav nav-tabs pull-right">

            <li class="active">
                <a href="#dashboard" data-toggle="tab">
                    <span style="margin-top: 2px;margin-left: 5px;" class="badge badge-danger pull-right">2</span>Dashboard
                </a>
            </li>

            <li class="">
                <a href="#paysliptab" data-toggle="tab">
                    Payslip</a>
            </li>

            <?php
            //  if(user_id() == 1){
            ?>
            <li class="">
                <a href="#net15report" data-toggle="tab">
                    <span style="margin-top: 2px;margin-left: 5px;" class="badge badge-danger pull-right"></span>NET 15
                </a>
            </li>
            <li class="">
                <a href="#net1530report" data-toggle="tab">
                    <span style="margin-top: 2px;margin-left: 5px;" class="badge badge-danger pull-right"></span>NET 15/30
                </a>
            </li>
            <?php
            //  }
            ?>
            <!--   <li class="">
                   <a href="#taxreports" data-toggle="tab">
                       Tax Reports</a>
               </li>
               <li class="">
                   <a href="#hdmfreports" data-toggle="tab">
                       HDMF Reports</a>
               </li>
               <li class="">
                   <a href="#coopreports" data-toggle="tab">
                       COOP Reports</a>
               </li>
               <li class="">
                   <a href="#pecewareports" data-toggle="tab">
                       PECEWA Reports</a>
               </li>
               <li class="">
                   <a href="#meminsreports" data-toggle="tab">
                       MEM_INS Reports</a>
               </li>
               <li class="">
                   <a href="#ssscontreports" data-toggle="tab">
                       SSS Cont Reports</a>
               </li>
               <li class="">
                   <a href="#sssloanreports" data-toggle="tab">
                       SSS Loan Reports</a>
               </li>
               <li class="">
                   <a href="#transactionrecheck" data-toggle="tab">
                       Transaction Recheck</a>
               </li>
               <li class="">
                   <a href="#employeeinfo" data-toggle="tab">
                       Employee Info</a>
               </li> -->
        </ul>
    </div>


    <div class="portlet-body tab-content" style="padding: 10px 10px; background: #fff;">

        <div class="tab-pane fade in" id="net1530report">
            <form id="submitnet1530report" action="<?php echo base_url() ?>payroll/submitnet1530report" method="post">
                <div class="col-md-2">

                    <div class="form-group">
                        <label>Payroll Date</label>
                        <input type="date" class="form-control" id="payrolldate1530" />
                    </div>
                </div><div class="col-md-2">
                    <div class="form-group">
                        <label>Year</label>
                        <input type="text" name="net1530year" id="net1530year" class="form-control" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" name="net1530month" id="net1530month" class="form-control" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Payclass</label>
                        <input type="text" name="net1530payclass" id="net1530payclass" class="form-control" />
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" style="margin-top: 23px;" class="btn btn-primary">GET</button>
                    </div>
                </div>
            </form>
            <div class="col-md-2 pull-right">
                <div class="form-group">
                    <button id="printnet1530" style="margin-top: 23px;" class="btn btn-primary pull-right">Print</button>
                </div>
            </div>
            <table class="table table-bordered table-hover table-condensed" id="net1530table">
                <thead>
                <th>EMP NAME</th>
                <th>ACCOUNT NO.</th>
                <th>NET 15</th>
                <th>NET 30</th>
                <th>TOTAL NET</th>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                <tr>
                    <td class="bold">TOTAL</td>
                    <td></td>
                    <td id="net15f" class="bold">0</td>
                    <td id="net30f" class="bold">0</td>
                    <td id="total1530f" class="bold">0</td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="tab-pane fade in" id="net15report">
            <div class="portlet light">
                <div class="portlet-title well">
                    <div class="caption">
                        <div class="input-group">
                            <span class="input-group-addon">
                                Signatory
                            </span>
                            <input type="date" class="form-control input-md" id="payrolldate15"/>
                            <span class="input-group-btn" style="width:0px;"></span>
                            <input type="text" class="form-control input-md" id="namesig" value=""  placeholder="Enter name"/>
                            <span class="input-group-btn" style="width:0px;"></span>
                            <input type="text" style="width: 300px; display: inline-block;" class="form-control input-md" id="possig" value="ASST. VICE PRESIDENT FOR OPERATIONS" placeholder="Enter position" />
                        </div>
                    </div>
                    <code class="pull-right" style="margin-top: 15px;">Note: Indicate the name and position of the signatory for the print our report.</code>

                </div>
                <div class="portlet-body">

                    <hr>
                    <div class="row">
                        <form id="submitnet15report" action="<?php echo base_url() ?>payroll/submitnet15report" method="post">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Year</label>
                                    <input type="text" name="net15year" id="net15year" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Month</label>
                                    <input type="text" name="net15month" id="net15month" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Payclass</label>
                                    <input type="text" name="net15payclass" id="net15payclass" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Paytype</label>
                                    <input type="text" name="net15paytype" id="net15paytype" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" style="margin-top: 23px;" class="btn btn-primary">GET</button>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="button" id="printnet15" style="margin-top: 23px;" class="btn btn-primary pull-right">Print</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover table-condensed" id="net15table">
                        <thead>
                        <th>EMP NAME</th>
                        <th>ACCOUNT NO.</th>
                        <th>NET</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade in" id="employeeinfo">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Employee</label>

                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="sssloanreports">
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY CONFIDENTIAL & SUPERVISOR DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>

                                        <select class="form-control" name="getmonth" id="configetmonthsssloan">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }

                                            }

                                            ?>

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="yearconfisssloan" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="confisearchsssloan" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="confiprintsssloan" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>

                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="confidentialtablesssloan">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>SSS LOAN</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY RANK AND FILE DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>
                                        <select class="form-control" name="getmonth" id="rankandfilegetmonthsssloan">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }
                                            }

                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="rankandfileyearsssloan" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="rankandfilesearchsssloan" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="rankandfileprintsssloan" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="rankandfiletablesssloan">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>SSS LOAN</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="ssscontreports">
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY CONFIDENTIAL & SUPERVISOR DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>

                                        <select class="form-control" name="getmonth" id="configetmonthssscont">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }

                                            }

                                            ?>

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="yearconfissscont" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="confisearchssscont" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="confiprintssscont" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>

                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="confidentialtablessscont">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>SSS Cont</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY RANK AND FILE DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>
                                        <select class="form-control" name="getmonth" id="rankandfilegetmonthssscont">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }
                                            }

                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="rankandfileyearssscont" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="rankandfilesearchssscont" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="rankandfileprintssscont" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="rankandfiletablessscont">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>SSS Cont</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="meminsreports">

        </div>
        <div class="tab-pane fade in" id="pecewareports">
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY CONFIDENTIAL & SUPERVISOR DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>

                                        <select class="form-control" name="getmonth" id="configetmonthpecewa">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }

                                            }

                                            ?>

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="yearconfipecewa" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="confisearchpecewa" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="confiprintpecewa" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>

                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="confidentialtablepecewa">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>PECEWA</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY RANK AND FILE DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>
                                        <select class="form-control" name="getmonth" id="rankandfilegetmonthpecewa">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }
                                            }

                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="rankandfileyearpecewa" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="rankandfilesearchpecewa" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="rankandfileprintpecewa" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="rankandfiletablepecewa">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>PECEWA</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="coopreports">
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY CONFIDENTIAL & SUPERVISOR DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>

                                        <select class="form-control" name="getmonth" id="configetmonthcoop">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }

                                            }

                                            ?>

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="yearconficoop" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="confisearchcoop" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="confiprintcoop" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>

                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="confidentialtablecoop">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>COOP</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY RANK AND FILE DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>
                                        <select class="form-control" name="getmonth" id="rankandfilegetmonthcoop">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }
                                            }

                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="rankandfileyearcoop" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="rankandfilesearchcoop" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="rankandfileprintcoop" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="rankandfiletablecoop">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>COOP</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane  fade in" id="transactionrecheck">
            <div class="row">
                <div class="col-md-12">
                    <form id="submitrecheck" action="<?php echo base_url() ?>payroll/gettransactiondetails" method="post">
                        <div class="col-md-3">
                            <label>Employee</label>
                            <input type="text" name="employeesearch" id="employeesearch" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Month</label>
                                    <input type="text" name="monthsearch" id="monthsearch" class="form-control" />
                                </div>

                                <div class="col-md-4">
                                    <label>Year</label>
                                    <input type="text" name="yearsearch" id="yearsearch" class="form-control" />
                                </div>
                                <div class="col-md-4">
                                    <label>Type</label>
                                    <input type="text" name="typesearch" id="typesearch" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" style="margin-top: 29px;" class="btn btn-default btn-sm"><i class="fa fa-search"></i>Search</button>
                        </div>
                    </form>
                </div>
                <br>
                <div class="col-md-3">
                    <div class="portlet">
                        <div class="portlet-title">
                            <div class="caption">
                                Payroll Loans Breakdown
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-bordered tbl-xs" id="payrollbreakdownloan">
                                <thead>
                                <th></th>
                                <th>Type</th>
                                <th>Amount</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="portlet">
                        <div class="portlet-title">
                            <div class="caption">
                                Payroll Transaction
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-bordered tbl-xs" id="payrolltransactionval">
                                <thead>
                                <th></th>
                                <th>Type</th>
                                <th>Amount</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade in" id="hdmfreports">
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY CONFIDENTIAL & SUPERVISOR DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>

                                        <select class="form-control" name="getmonth" id="configetmonthhdmf">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }

                                            }

                                            ?>

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="yearconfihdmf" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="confisearchhdmf" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="confiprinthdmf" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>

                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="confidentialtablehdmf">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>HDMF LOAN</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY RANK AND FILE DEDUCTIONS AUGUST 2018
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>
                                        <select class="form-control" name="getmonth" id="rankandfilegetmonthhdmf">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }
                                            }

                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="rankandfileyearhdmf" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="rankandfilesearchhdmf" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="rankandfileprinthdmf" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                            <table class="table table-bordered table-responsive table-hover tbl-xs" id="rankandfiletablehdmf">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>HDMF LOAN</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane  fade in" id="taxreports" >
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY CONFIDENTIAL & SUPERVISOR TAX
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>

                                        <select class="form-control" name="getmonth" id="configetmonth">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }

                                            }

                                            ?>

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="yearconfi" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="confisearchtax" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="confiprinttax" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>

                            <table class="table table-bordered table-responsive table-hover tbl-sm" id="confidentialtabletax">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>WTAX</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                MONTHLY RANK AND FILE TAX
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Month</label>
                                        <select class="form-control" name="getmonth" id="rankandfilegetmonth">
                                            <?php
                                            $month = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            );
                                            foreach ($month as $key => $value) {
                                                if($key == date('m')){
                                                    $selected = 'selected';
                                                    echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
                                                }else{
                                                    echo '<option  value="'.$key.'">'.$value.'</option>';
                                                }
                                            }

                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" value="<?php echo date('Y') ?>" name="yeardata" id="rankandfileyear" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button style="margin-top: 26px !important;" id="rankandfilesearchtax" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    <button style="margin-top: 26px !important;" id="rankandfileprinttax" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                            <table class="table table-bordered table-responsive table-hover tbl-sm" id="rankandfiletabletax">
                                <thead>
                                <th></th>
                                <th>Employee Name</th>
                                <th>WTAX</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="paysliptab" >

            <div class="row">
                <div class="col-md-12">
                    <form id="generaterepdata" action="<?php echo base_url() ?>payroll/getreportsdata" method="post" >
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="payclass">Pay Class</label>
                                <select name="payclass" id="payclasscombo" class="form-control">
                                    <option></option>
                                    <option value="128">Rank and File</option>
                                    <option value="1">Confidential</option>
                                    <option value="3077">Tier 1</option>
                                    <option value="3078">Tier 2</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">

                                <div width="50%">
                                    <label for="month">Month</label>
                                    <select name="month" id="month" class="form-control">
                                        <option></option>
                                        <?php
                                        $month = 1;
                                        for($month = 1;$month<=12;$month++){
                                            echo "  <option value=".$month.">".date('F', mktime(0, 0, 0, $month, 1))."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div width="50%">
                                    <label for="year">Year</label>
                                    <select name="year" id="year" class="form-control">
                                        <option></option>
                                        <?php
                                        $year = 2018;
                                        for($year = 2018;$year<=2040;$year++){
                                            echo "  <option value=".$year.">".$year."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="payrollperiod">Payroll Period</label>
                                <select name="payrollperiod"  id="payrollperiod" class="form-control">
                                    <option></option>
                                    <option value="1">1st Half</option>
                                    <option value="2">2nd Half</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="btn-grp">
                                <button type="submit" style="margin-top: 21px;" class="btn btn-primary">Get Data</button>
                                <button style="margin-top: 23px !important;" class="btn btn-primary" type="button" id="exportexcelbtn"><i class="fa fa-sign-out"></i> Export Bank File</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- <button style="margin-top: 23px !important;" class="btn btn-primary" type="button" id="prinreportbtn"><i class="fa fa-print"></i> Print Report</button> -->
                    <button style="margin-top: 23px !important;" class="btn btn-primary" type="button" id="payslip"><i class="fa fa-print"></i> Print Payslip</button>
                    <!--<button style="margin-top: 23px !important;" class="btn btn-primary" type="button" id="sendpayslip"><i class="fa fa-envelope"></i> Send Payslip</button> -->
                </div>
            </div>
            <br>
            <table class="table table-bordered table-condensed table-hover tbl-xs" id="payrollreportstbl">
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
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
        <div class="tab-pane active  fade in" id="dashboard" >
            <div class="row" style="padding: 10px 10px;">
                <form id="submitgetreports" action="<?php echo base_url() ?>payroll/getreports" method="post">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Year</label>
                            <input type="text" name="payrollyear" id="payrollyear" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Month</label>
                            <input type="text" name="payrollmonth" id="payrollmonth" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="sel1">Payclass</label>
                            <select name="payrollpayclass" class="form-control" id="payrollpayclass">
                                <option value="3077">Tier 1</option>
                                <option value="3078">Tier 2</option>
                                <option value="128">Rank and File</option>
                                <option value="1">Confidential</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="inputgroupid" id="inputgroupid" />
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="sel1">Paytype</label>
                            <select name="payrollpaytype" class="form-control" id="payrollpaytype">
                                <option value="1">1st half</option>
                                <option value="2">2nd half</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button style="margin-top: 22px !important;" class="btn btn-primary" type="submit">GET </button>
                            <input type="hidden" id="payrolldataid" />
                        </div>
                    </div>
                </form>
                <div id="pceobtn"></div>

            </div>

            <div class="tabbable-line">
                <ul class="nav nav-tabs ">
                    <li class="active">
                        <a href="#payrollregister" data-toggle="tab" aria-expanded="false"> Payroll Register </a>
                    </li>
                    <li class="">
                        <a href="#earningregister" data-toggle="tab" aria-expanded="true"> Earning Register</a>
                    </li>
                    <li class="">
                        <a href="#deductionsregister" data-toggle="tab" aria-expanded="false">Deductions Register</a>
                    </li>
                    <li class="">
                        <a href="#overtimeregister" data-toggle="tab" aria-expanded="false">Overtime Register</a>
                    </li>

                    <!-- <li class="">
                         <a href="#annualregister" data-toggle="tab" aria-expanded="false">Annual Register</a>
                     </li> -->
                </ul>


                <div id="approval_buttons" class="btn-group pull-right" style="padding: 10px 10px;">

                </div>
                <div class="tab-content">

                    <div class="tab-pane" id="annualregister">
                        <button id="printannualreg" class="btn btn-primary btn-xs">PRINT ANNUAL REGISTER</button>
                        <button id="printannualregbyemp" class="btn btn-primary btn-xs">PRINT ANNUAL REGISTER BY EMPLOYEE</button>
                        <table class="table table-bordered tbl-xs" id="annualregtbl">
                            <thead>
                            <th></th>
                            <th>DEPT CODE</th>
                            <th>BASIC</th>
                            <th>GROSS</th>
                            <th>TAX</th>
                            <th>DEDUCTION</th>
                            <th>NET</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="tab-pane active" id="payrollregister">
                        <!--  <button id="printdepttotalpayreg">PRINT DEPARTMENT TOTALS</button> -->
                        <button id="printpayreg" class="btn btn-primary btn-xs">PRINT PAYROLL REGISTER</button>
                        <button id="printpayregbyemp" class="btn btn-primary btn-xs">PRINT PAYROLL REGISTER BY EMPLOYEE</button>

                        <table class="table table-bordered table-condensed table-hover tbl-xs" id="payrollregistertable2">
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
                                <td class="number bold text-info" id="resultgrossearnings2"></td>
                                <td class="number bold text-info" id="resulttotaldedn2"></td>
                                <td class="number bold text-info" id="resulttotalnet2"></td>
                                <td class="number bold text-info" id="resultssscont2"></td>
                                <td class="number bold text-info" id="resultsssloan2"></td>
                                <td class="number bold text-info" id="resulthdmfcont2"></td>
                                <td class="number bold text-info" id="resulthdmfloan2"></td>
                                <td class="number bold text-info" id="resultpecewaloan2"></td>
                                <td class="number bold text-info" id="resultcooploan2"></td>
                                <td class="number bold text-info" id="resultpagibigadd2"></td>
                                <td class="number bold text-info" id="resultotherdeduction2"></td>
                                <td class="number bold text-info" id="resulthmodedn2"></td>
                                <td class="number bold text-info" id="resultdeda2"></td>
                                <td class="number bold text-info" id="resultelectricbill2"></td>
                                <td class="number bold text-info" id="resultmemins2"></td>
                                <td class="number bold text-info" id="resultlwop2"></td>
                                <td class="number bold text-info" id="resultbasetax2"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane" id="earningregister">
                        <button id="printearregdepttotals" class="btn btn-primary btn-xs">PRINT EARNINGS REGISTER</button>
                        <button id="printearregbyemp" class="btn btn-primary btn-xs">PRINT EARNINGS REGISTER BY EMPLOYEE</button>
                        <table class="table table-bordered table-condensed table-hover tbl-xs" id="earningregistertable2">
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
                                <td class="number bold text-info" id="totaleaningbasicrate2"></td>
                                <td class="number bold text-info" id="totalearningcola2"></td>
                                <td class="number bold text-info" id="totalearningtransallw2"></td>
                                <td class="number bold text-info" id="totalearningricesubsi2"></td>
                                <td class="number bold text-info" id="totalearningholidaypay2"></td>
                                <td class="number bold text-info" id="totalearningnitediff2"></td>
                                <td class="number bold text-info" id="totalearningotpay2"></td>
                                <td class="number bold text-info" id="totalearningactingallw2"></td>
                                <td class="number bold text-info" id="totalearningotheradd2"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane" id="deductionsregister">
                        <button id="printdednregdepttotals" class="btn btn-primary btn-xs">PRINT DEDUCTIONS REGISTER</button>
                        <button id="printdednregbyemp" class="btn btn-primary btn-xs">PRINT DEDUCTIONS REGISTER BY EMPLOYEE</button>
                        <table class="table borderless table-condensed table-hover tbl-xs" id="deductionsregistertable2">
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
                                <td class="text-danger number" id="totaldeductionssscont2"></td>
                                <td class="text-danger" id="totaldeductionsssloan2"></td>
                                <td class="text-danger" id="totaldeductionhdmfcont2"></td>
                                <td class="text-danger" id="totaldeductionhdmfloan2"></td>
                                <td class="text-danger" id="totaldeductionpecewaloan2"></td>
                                <td class="text-danger" id="totaldeductioncooploan2"></td>
                                <td class="text-danger" id="totaldeductionpagibigad2"></td>
                                <td class="text-danger" id="totaldeductionotherdedn2"></td>
                                <td class="text-danger" id="totaldeductionhmodedn2"></td>
                                <td class="text-danger" id="totaldeductiondeda2"></td>
                                <td class="text-danger" id="totaldeductionelectbill2"></td>
                                <td class="text-danger" id="totaldeductionmemins2"></td>
                                <td class="text-danger" id="totaldeductionlwop2"></td>
                                <td class="text-danger" id="totaldeductionbasetax2"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane" id="overtimeregister">
                        <button id="printotregdepttotals" class="btn btn-primary btn-xs">PRINT OVERTIME REGISTER</button>
                        <button id="printotregbyemp" class="btn btn-primary btn-xs">PRINT OVERTIME REGISTER BY EMPLOYEE</button>
                        <table class="table table-bordered table-condensed table-hover tbl-xs" id="overtimeregistertable2">
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
                                <th id="totalndothrs2">0.00</th>
                                <th id="totalndotpay2">0.00</th>
                                <th id="totalothrs2">0.00</th>
                                <th id="totalone252">0.00</th>
                                <th id="totalone302">0.00</th>
                                <th id="totalone502">0.00</th>
                                <th id="totalone602">0.00</th>
                                <th id="totalone802">0.00</th>
                                <th id="totaltwo102">0.00</th>
                                <th id="totaltwo302">0.00</th>
                                <th id="totaltwo602">0.00</th>
                            </tr>
                            </tfoot>

                        </table>
                    </div>
                    <input type="hidden" id="get_value">

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
<script>
    PAYROLL.init();
    PAYROLL.report();
    PAYROLL.taxreport();
    PAYROLL.hdmfreport();
    PAYROLL.sssloanreport();
    PAYROLL.ssscontreport();
    PAYROLL.pecewareport();
    PAYROLL.coopreport();

    $('#configetmonth').select2({
        "allowClear":true
    });
    $('#rankandfilegetmonth').select2({
        "allowClear":true
    });

    $('#configetmonthhdmf').select2({
        "allowClear":true
    });
    $('#rankandfilegetmonthhdmf').select2({
        "allowClear":true
    });

    $('#configetmonthsssloan').select2({
        "allowClear":true
    });
    $('#rankandfilegetmonthsssloan').select2({
        "allowClear":true
    });

    $('#configetmonthssscont').select2({
        "allowClear":true
    });
    $('#rankandfilegetmonthssscont').select2({
        "allowClear":true
    });

    $('#configetmonthpecewa').select2({
        "allowClear":true
    });
    $('#rankandfilegetmonthpecewa').select2({
        "allowClear":true
    });

    $('#configetmonthcoop').select2({
        "allowClear":true
    });
    $('#rankandfilegetmonthcoop').select2({
        "allowClear":true
    });
    $('#payrollpaytype').select2({
        "allowClear":true
    });
    $('#payrollpayclass').select2({
        "allowClear":true
    });
    $('#approvedpayrollpaytype').select2({
        "allowClear":true
    });
    $('#approvedpayrollpayclass').select2({
        "allowClear":true
    });

    var date = new Date();
    PECO.select2Basic($('#payrollyear', document) , 'systems/select2year' , 'Select Year' , false,false,date.getFullYear());
    PECO.select2Basic($('#payrollmonth', document) , 'systems/select2month' , 'Select Month' , false,false,date.getMonth() + 1);
    PECO.select2Basic($('#net15year', document) , 'systems/select2year' , 'Select Year' , false,false,false);
    PECO.select2Basic($('#net1530year', document) , 'systems/select2year' , 'Select Year' , false,false,false);
    PECO.select2Basic($('#net15month', document) , 'systems/select2month' , 'Select Month' , false,false,false);
    PECO.select2Basic($('#net1530month', document) , 'systems/select2month' , 'Select Month' , false,false,false);
    PECO.select2Basic($('#approvedpayrollyear', document) , 'systems/select2year' , 'Select Year' , false,false,false);
    PECO.select2Basic($('#approvedpayrollmonth', document) , 'systems/select2month' , 'Select Month' , false,false,false);
    PECO.select2Basic($('#net15payclass', document) , 'payroll/getpayrollpaytype' , 'Select Payclass' , false,false,false);
    PECO.select2Basic($('#net1530payclass', document) , 'payroll/getpayrollpaytype' , 'Select Payclass' , false,false,false);
    PECO.select2Basic($('#net15paytype', document) , 'hris/getpayrollpaytypelist' , 'Select Paytype' , false,false,false);

</script>
