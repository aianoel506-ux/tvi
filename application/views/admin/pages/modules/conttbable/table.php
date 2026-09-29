<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 3/16/2018
 * Time: 9:35 AM
 */

?>


<ul class="nav nav-tabs">
    <!--  <li class="active">
      <a href="#portlet_tab3" data-toggle="tab" aria-expanded="false">Flexi Time</a>
  </li>-->
    <li class="">
        <a href="#portlet_tab2" data-toggle="tab" aria-expanded="false">Control</a>
    </li>
    <li class="active">
        <a href="#portlet_tab1" data-toggle="tab" aria-expanded="true">Contributions</a>
    </li>
    <li class="">
        <a href="#portlet_tab3" data-toggle="tab" aria-expanded="true">Employee Contribution</a>
    </li>
    <li class="">
        <a href="#portlet_tab4" data-toggle="tab" aria-expanded="true">Contribution Matrix</a>
    </li>
   <!-- <li class="active">
        <a href="#portlet_tab1" data-toggle="tab" aria-expanded="true">Contributions</a>
    </li> -->

</ul>

<div class="tab tab-content">
    <div class="tab-pane fade in" id="portlet_tab3">
        <div class="row">
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            Add Contribution
                        </div>
                    </div>
                    <div class="portlet-body">
                       <div class="row">
                            <form id="submitempcont" action="<?php echo base_url() ?>payroll/addcontributiontoemp" method="post">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <input type="text" class="form-control" name="employeecont" id="employeecont" />
                                </div>
                                <div class="form-group">
                                    <label>Cont. Type</label>
                                    <input type="text" class="form-control" name="conttype" id="conttype" />
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary pull-right">Add</button>
                                </div>
                            </form>
                       </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            Employees
                        </div>
                    </div>
                    <div class="portlet-body ">
                            <div class="row">
                                <table class="table table-bordered table-condensed table-hover table-striped" id="employeeconttbl">
                                    <thead>
                                        <th></th>
                                        <th>Employee</th>
                                        <th>Deduction Type</th>
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
    <div class="tab-pane fade in" id="portlet_tab2">
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            Add Contribution Rates
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <form id="submitcontribution" action="<?php echo base_url() ?>payroll/submitcontribution" method="POST">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <input type="text" name="conttype" id="conttype" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>From</label>
                                        <input placeholder="0.00" type="text" name="fromrange" id="fromrange" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>To</label>
                                        <input placeholder="0.00" type="text" name="torange" id="torange" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>Monthly Salary Credit</label>
                                        <input placeholder="0.00" type="text" name="monthlysalcredit" id="monthlysalcredit" class="form-control" />
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ER</label>
                                        <input placeholder="0.00" type="text" name="ercont" id="ercont" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>EE</label>
                                        <input placeholder="0.00" type="text" name="eecont" id="eecont" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>TOTAL</label>
                                        <input placeholder="0.00" type="text" name="totalcont" id="totalcont" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>Month</label>
                                        <input type="text" name="monthcont" id="monthcont" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" name="yearcont" id="yearcont" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            Group Deletion
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <input type="text" name="deletiontype" id="deletiontype" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Month</label>
                                    <input type="text" name="monthdeletion" id="monthdeletion" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Year</label>
                                    <input type="text" name="deletionyear" id="deletionyear" class="form-control" />
                                </div>
                                <div class="form-group">
                                   <button id="deleteratesbtn" class="btn btn-danger"><i class="fa fa-trash"></i> Delete Rates </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade in active" id="portlet_tab1">
        <div class="portlet light bordered table">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <span class="font-green-jungle">Contribution Table</span>
                </div>
                <?php echo draw_tab('EMPCONT', 72, true, true);?>
            </div>
            <div class="portlet-body" style="margin-top: 40px !important;">
                <table  class="table table-bordered table-condensed tbl-sm table-hover" id="conttable">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Base</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Amount</th>
                        <th>Rate Employee</th>
                        <th>Rate Employer</th>
                        <th>Var</th>
                        <th>Types</th>
                        <th>Date Created</th>
                        <th>Created by</th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
                <tbody></tbody>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in" id="portlet_tab4">
        <div class="row">
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            Contributions
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <table class="table table-bordered table-striped" id="tbl_contribs">
                                <thead>
                                    <th>Contributions</th>
                                    <th width="10%"></th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            Earnings Affected
                        </div>
                    </div>
                    <div class="portlet-body ">
                        <div class="row">
                            <table class="table table-bordered table-striped table-condensed tbl-xs" id="tbl_earnings">
                                <thead>
                                <th></th>
                                <th>Name</th>
                                <th>Description</th>
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



<script src="<?php echo base_url() ?>assets/pages/conttbable/main.js"></script>
<script>
    CONTRIBUTION.init();
    PECO.select2Basic($('#employeecont',document) , 'hris/getemployees' , 'Select Employee' , false,false,false,false,false,true);
</script>