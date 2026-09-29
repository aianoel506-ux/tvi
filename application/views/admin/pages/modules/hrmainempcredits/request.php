<style>
    #printleavetbl_filter > label{
       top: -35px;
        left: 160px;
        position: absolute;
    }
</style>
<div class="tab-pane fade in active" id="applyleave">

    <div class="col-md-12 ">
        <!-- BEGIN Portlet PORTLET-->
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-pin font-yellow-crusta"></i>
                    <span class="caption-subject bold font-yellow-crusta uppercase"> Leave </span>
                    <span class="caption-helper">request</span>
                </div>
                <ul class="nav nav-tabs">
                    <!--  <li class="active">
                      <a href="#portlet_tab3" data-toggle="tab" aria-expanded="false">Flexi Time</a>
                  </li>-->
                    <li class="">
                        <a href="#portlet_tab2" data-toggle="tab" aria-expanded="false">Print</a>
                    </li>
                    <li class="active">
                        <a href="#portlet_tab1" data-toggle="tab" aria-expanded="true">Regular / Locator Leave</a>
                    </li>
                    <li class="">
                        <a href="#portlet_tab3" data-toggle="tab" aria-expanded="true">Flexi Leave</a>
                    </li>
                    <li class="">
                        <a href="#portlet_tab4" data-toggle="tab" aria-expanded="true">Union Leave</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane fade in" id="portlet_tab4">
                        <div class="tabbable-line">
                            <ul class="nav nav-tabs ">
                                <li class="active">
                                    <a href="#unionreq" data-toggle="tab" aria-expanded="false">Union Request</a>
                                </li>
                                <li class="">
                                    <a href="#unionsum" data-toggle="tab" aria-expanded="false">Union Summary</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="unionreq">

                                    <div class="row" style="margin-bottom: 150px;">
                                        <form id="submitunionleave" method="post" action="<?php echo base_url() ?>hris/submitunionleave">
                                            <div class="col-md-3">
                                                <div id="unionevent">
                                                    <div class="form-group">
                                                        <label>Employee</label>
                                                        <input type="text" name="unionempname" id="unionempname" class="form-control" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Year</label>
                                                        <input type="text" name="unionyear" id="unionyear" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sel1">Type</label>
                                                    <select class="form-control" id="uniontype" name="uniontype">
                                                        <option value="1">Daily</option>
                                                        <option value="2">Hourly</option>
                                                    </select>
                                                </div>
                                                <div id="uniondate">
                                                    <div class="form-group">
                                                        <label>From</label>
                                                        <input type="date" name="fromdate" class="form-control" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label>To</label>
                                                        <input type="date" name="todate" class="form-control" />
                                                    </div>
                                                </div>
                                                <div id="uniontime" class="hidden">
                                                    <div class="row">
                                                        <label>From</label>
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-md-4">
                                                            <input type="text" name="fromhours" placeholder="hh" class="form-control" />
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" name="fromminutes" placeholder="mm" class="form-control" />
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select class="form-control" name="fromampm" id="fromampm">
                                                                <option value="AM">AM</option>
                                                                <option value="PM">PM</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label>To</label>
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-md-4">
                                                            <input type="text" name="tohours" placeholder="hh" class="form-control" />
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" name="tominutes" placeholder="mm" class="form-control" />
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select class="form-control" name="toampm" id="toampm">
                                                                <option value="AM">AM</option>
                                                                <option value="PM">PM</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Leave Date</label>
                                                                <input type="date" name="leavedate" class="form-control" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="comment">Remarks</label>
                                                    <textarea class="form-control" rows="5" id="remarks" name="remarks"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary pull-right">Add</button>
                                                </div>
                                            </div>

                                        </form>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            Union Balance
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <table class="table table-bordered table-striped table-condensed" id="unionbalancetbl">
                                                            <thead>
                                                            <th></th>
                                                            <th>Credit</th>
                                                            <th>Year</th>
                                                            <th>Date Created</th>
                                                            <th>Created by</th>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            Union Pending Transaction

                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <button id="saveunionpendingbtn" data-empid="" data-year="" style="margin-bottom: 10px;" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                                                        <table class="table table-bordered table-striped table-condensed" id="unionpendingtrntbl">
                                                            <thead>
                                                            <th></th>
                                                            <th>From Date</th>
                                                            <th>To Date</th>
                                                            <th>From Time</th>
                                                            <th>To Time</th>
                                                            <th>Total</th>
                                                            <th></th>
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
                                <div class="tab-pane fade in" id="unionsum">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Year</label>
                                                <input type="text" class="form-control" id="unionyearsummary" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <table class="table table-bordered table-striped table-condensed" id="availedunion">
                                            <thead>
                                            <th>Employee</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>From Time</th>
                                            <th>To Time</th>
                                            <th>Total</th>
                                            <th>Leave Date</th>
                                            <th>Year</th>
                                            <th>Date Created</th>
                                            <th>Created By</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="portlet_tab3">
                       <div class="row" style="margin-bottom: 150px;">
                           <div class="col-md-3">
                               <form id="submitflexitrn" action="<?php echo base_url() ?>hris/submitflexitrn" method="post">
                                   <div class="form-group">
                                       <label>Employee</label>
                                       <input type="text" class="form-control" id="employee" name="employee" />
                                   </div>
                                   <div class="form-group">
                                       <label>Year</label>
                                       <input type="text" name="flexiyear" id="flexiyear" class="form-control" />
                                   </div>
                                   <div class="form-group">
                                       <label for="sel1">Type</label>
                                       <select class="form-control" id="flexitype" name="flexitype">
                                           <option value="1">Daily</option>
                                           <option value="2">Hourly</option>
                                       </select>
                                   </div>
                                   <div id="flexidate">
                                       <div class="form-group">
                                           <label>From</label>
                                           <input type="date" name="fromdate" class="form-control" />
                                       </div>
                                       <div class="form-group">
                                           <label>To</label>
                                           <input type="date" name="todate" class="form-control" />
                                       </div>
                                   </div>
                                   <div id="flexitime" class="hidden">
                                       <div class="row">
                                           <label>From</label>
                                       </div>
                                       <div class="row">

                                           <div class="col-md-4">
                                               <input type="text" name="fromhours" placeholder="hh" class="form-control" />
                                           </div>
                                           <div class="col-md-4">
                                               <input type="text" name="fromminutes" placeholder="mm" class="form-control" />
                                           </div>
                                           <div class="col-md-4">
                                               <select class="form-control" name="fromampm" id="fromampm">
                                                   <option value="AM">AM</option>
                                                   <option value="PM">PM</option>
                                               </select>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <label>To</label>
                                       </div>
                                       <div class="row">

                                           <div class="col-md-4">
                                               <input type="text" name="tohours" placeholder="hh" class="form-control" />
                                           </div>
                                           <div class="col-md-4">
                                               <input type="text" name="tominutes" placeholder="mm" class="form-control" />
                                           </div>
                                           <div class="col-md-4">
                                               <select class="form-control" name="toampm" id="toampm">
                                                   <option value="AM">AM</option>
                                                   <option value="PM">PM</option>
                                               </select>
                                           </div>
                                       </div>
                                       <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Leave Date</label>
                                                    <input type="date" name="leavedate" class="form-control" />
                                                </div>
                                            </div>
                                       </div>
                                   </div>
                                   <div class="form-group">
                                       <label for="comment">Remarks</label>
                                       <textarea class="form-control" rows="5" id="remarks" name="remarks"></textarea>
                                   </div>
                                   <div class="form-group">
                                       <button type="submit" class="btn btn-primary pull-right">Add</button>
                                   </div>
                               </form>

                           </div>
                           <div class="col-md-9">
                                <div id="empflexibalance" class="hidden">
                                    <div class="row">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    Pending Transactions
                                                    <button type="button" data-id="" id="savependingflexitrn" class="btn btn-primary" style="right: 10px; position: absolute;">Save <i class="fa fa-save"></i></button>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <span class="pull-right">Total: <b><span id="totalpendingcredits"></span></b></span>
                                                <table  class="table table-bordered table-condensed table-hover" id="pendingflexitable">
                                                    <thead>
                                                    <th></th>
                                                    <th>From Date</th>
                                                    <th>To Date</th>
                                                    <th>From Time</th>
                                                    <th>To Time</th>
                                                    <th>Total</th>
                                                    <th></th>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    Flexi Balance
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <span class="pull-right">Total Balance: <b><span id="totalflexicreditslabel"></span></b></span>
                                                <table class="table table-bordered table-condensed table-hover" id="totalflexicredits">
                                                    <thead>
                                                    <th></th>
                                                    <th>Flexi Time</th>
                                                    <th>Purpose</th>
                                                    <th>Expiry</th>
                                                    <th>Date Encoded</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    Availed
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <span class="pull-right">Total Incurred: <b><span id="totalspent"></span></b></span>
                                                <table class="table table-bordered table-condensed table-hover" id="totalincurredtbl">
                                                    <thead>
                                                    <th></th>
                                                    <th>From Date</th>
                                                    <th>To Date</th>
                                                    <th>From Time</th>
                                                    <th>To Time</th>
                                                    <th>Leave Date</th>
                                                    <th>Date Encoded</th>
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
                    <div class="tab-pane fade in active" id="portlet_tab1">
                        <!-- <form method="post" action="<?php echo base_url() ?>request/processleaveform" id="submitleaveform2"> -->
                        <form method="post" action="<?php echo base_url() ?>request/draftleaverequest" id="submitleaveform2">
                            <input type="hidden" name="hiddenempid" id="hiddenempid" value="" />
                            <div class="row" style="margin-bottom: 100px !important;">
                                <div class="col-md-7">
                                    <div class="portlet light">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                Employee
                                            </div>
                                        </div>
                                        <div class="portlet-body">

                                            <div class="row">
                                                <div class="col-md-6 filter-empyr">

                                                    <div class="form-group">
                                                        <label>Year</label>
                                                        <input  class="form-control" id="yearleave" value="<?php echo date('Y'); ?>" name="yearleave"  />
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Employee</label>
                                                        <input  value="" type="text" name="userloggedinname" id="employeeselect2" class="form-control"/>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="comment">Remarks</label>
                                                        <textarea class="form-control" rows="5" name="remarks" id="remarks"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <ul class="list-group summary column" id="list_leave_credits"></ul>
                                            </div>
                                            <div id="draftpanel">
                                                <h3>Draft</h3>
                                                <table class="table table-bordered" id="draftrequestleavetbl">
                                                    <thead>
                                                    <th></th>
                                                    <th>Leave Type</th>
                                                    <th>From</th>
                                                    <th>To</th>
                                                    <th>From Time</th>
                                                    <th>To Time</th>
                                                    <th>Type</th>
                                                    <th></th>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                <div class="form-actions">
                                                    <button id="submitform" type="button" class="btn btn-primary pull-right">Save</button>
                                                    <button id="resetbtn" style="margin-right: 20px;" type="button" class="btn btn-info pull-right">Reset</button>
                                                </div>
                                            </div>


                                        </div>
                                    </div>


                                    <!--  <div class="form-group">
                                          <label>Reason</label>
                                          <textarea class="form-control" id="reason" name="reason" maxlength="225" rows="2" placeholder="Enter your reason here..."></textarea>
                                      </div> -->

                                </div>
                                <div class="col-md-5">

                                    <div class="portlet light">
                                        <div class="portlet-title">
                                            <div class="caption">Transactions</div>
                                        </div>
                                        <div class="portlet-body form portlet-empty" style="">

                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label>Leave Type</label>
                                                    <input  class="form-control" id="selectleavetype2" name="selectleavetype"  />
                                                    <input type="hidden" id="hiddenleavenames" />
                                                </div>

                                                <div class="form-group">
                                                    <label>From</label>
                                                    <input  type="date" name="fromdate" id="fromdate2" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>To</label>
                                                    <input  type="date" name="todate" id="todate2" class="form-control" />
                                                </div>
                                                <div class="row">
                                                    <div class="input-group"></div>
                                                    <div class="col-md-3">
                                                        <input type="=text" id="fromhours" name="fromhours" class="form-control" placeholder="hh" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="=text" id="fromminutes" name="fromminutes" class="form-control" placeholder="mm" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" value="00" id="fromseconds" name="fromseconds" class="form-control" placeholder="ss" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="fromampm" class="form-control" id="fromampm">
                                                            <option value="AM">AM</option>
                                                            <option value="PM">PM</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <input type="=text" id="tohours" name="tohours" class="form-control" placeholder="hh" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="=text" id="tominutes" name="tominutes" class="form-control" placeholder="mm" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" value="00" id="toseconds" name="toseconds" class="form-control" placeholder="ss" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="toampm" class="form-control" id="toampm">
                                                            <option value="AM">AM</option>
                                                            <option value="PM">PM</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div c;ass="form-group">
                                                    <label>Leave Date</label>
                                                    <input type="date" id="leavedate" class="form-control" name="leavedate"  />
                                                </div>

                                                <div class="form-group">
                                                    <label>Type</label>
                                                    <input type="text" name="leavetype" id="leavetype" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" id="addtrnrequestleavebtn" class="btn btn-primary pull-right">Add</button>
                                                </div>
                                                <br>
                                                <br>
                                                <br>
                                                <hr>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>
                    <div class="tab-pane fade in " id="portlet_tab2">

                        <ul class="nav nav-tabs">
                            <!--  <li class="active">
                              <a href="#portlet_tab3" data-toggle="tab" aria-expanded="false">Flexi Time</a>
                          </li>-->
                            <li class="active">
                                <a href="#activeleave" data-toggle="tab" aria-expanded="true">Active</a>
                            </li>
                            <li class="">
                                <a href="#cancelledleave" data-toggle="tab" aria-expanded="false">Cancelled</a>
                            </li>

                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="activeleave">
                                <div class="row">

                                    <div class="col-md-12">
                                        <form id="printleaveform" action="<?php echo base_url() ?>hris/printleaveform" method="post">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Employee</label>
                                                    <input type="text" name="employeeprint" id="employeeprint" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Year</label>
                                                    <input type="text" name="yearprint" id="yearprint" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Type</label>
                                                    <select class="form-control" id="trntype" name="trntype">
                                                        <option value="1">Regular / Locator</option>
                                                        <option value="2">Flexi</option>
                                                        <option value="3">Union</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>SUPERVISOR</label>
                                                    <input type="text" name="supervisor" id="supervisor" class="form-control" />

                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label></label>
                                                    <input type="checkbox" name="tempsupp" id="tempsupp" class="form-control" />

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>EXECUTIVE</label>
                                                    <input type="text" name="executive" id="executive" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label></label>
                                                    <input type="checkbox" name="consultant" id="consultant" class="form-control" />

                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Date Created</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <label class="input-group-addon">From</label>
                                                            <input type="date" name="fromdate" id="fromdate" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <label class="input-group-addon">To</label>
                                                            <input type="date" name="todate" id="todate" class="form-control" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                           <div class="col-md-1">
                                               <div class="form-group">
                                                   <button style="margin-top: 24px !important;" type="submit" class="btn btn-primary pull-right">Search</button>
                                               </div>
                                           </div>

                                        </form>


                                    </div>
                                    <div class="col-md-12">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    Request List
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="form-group">
                                                    <a style="margin-left: 5px !important;margin-top: 15px;" href="#tbl_leave_history" id="viewallleavehist" data-toggle="ajax-modal" data-view="" data-arr="" class="btn btn-primary pull-right">View All</a>
                                                </div>
                                                <table class="table table-bordered table-hover table-condensed table-striped" id="printleavetbl">
                                                    <thead>
                                                    <th></th>
                                                    <th>Created by</th>
                                                    <th>Updated by</th>
                                                    <th>Date Created</th>
                                                    <th>Date Updated</th>
                                                    <th></th>
                                                    <th></th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                                <hr>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="tab-pane fade in " id="cancelledleave">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                     Request List
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <table class="table table-bordered table-responsive table-hover" id="cancelledleavetbl">
                                                    <thead>
                                                    <th></th>
                                                    <th>Created by</th>
                                                    <th>Updated by</th>
                                                    <th>Date Created</th>
                                                    <th>Date Updated</th>
                                                    <th></th>
                                                    <th></th>
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
            </div>
        </div>
    </div>


</div>




<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/hrmain.js"></script>


<script type="text/javascript">
    MAINTENACE.initleaverequest();
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd;
    }

    if (mm < 10) {
        mm = '0' + mm;
    }

    today = yyyy + '-' + mm + '-' + dd;
    $(document).find('#leavedate').val(today);
    $(document).find('#trntype').select2();
    $(document).find('#uniontype').select2();
    $(document).on('change','#trntype' , function () {
        var this_ = $(this);
        if(this_.val() == 3){
            PECO.select2Basic($('#executive',document),'hris/leaveemployee','Select Employee',false,false,1);
        }else{
            PECO.select2Basic($('#executive',document),'hris/leaveemployee','Select Employee',false,false,false);
        }
    });
    PECO.select2Basic($('#executive',document),'hris/leaveemployee','Select Employee',false,false,false);
    PECO.select2Basic($('#flexiyear') , 'systems/select2year' , 'Select Year' , false,false,false);
    PECO.select2Basic($('#unionyearsummary') , 'systems/select2year' , 'Select Year' , false,false,false);
</script>
