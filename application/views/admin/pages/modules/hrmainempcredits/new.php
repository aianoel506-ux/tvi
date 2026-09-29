<ul class="nav nav-tabs">
    <li class="">
        <a href="#portlet_tab2" data-toggle="tab" aria-expanded="false">(VL, SL, EL etc...)</a>
    </li>
    <li class="active">
        <a href="#portlet_tab1" data-toggle="tab" aria-expanded="true">Flexi</a>
    </li>
    <li class="">
        <a href="#portlet_tab4" data-toggle="tab" aria-expanded="true">Union</a>
    </li>
    <li class="">
        <a href="#portlet_tab3" data-toggle="tab" aria-expanded="false">Credit list</a>
    </li>
</ul>

<div class="tab-content">

    <div class="tab-pane fade in " id="portlet_tab4">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-2">
                                <form id="submitunion" action="<?php echo base_url() ?>hris/addunioncredits" method="post">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <input type="text" name="unionyear" id="unionyear" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>Days</label>
                                        <input required type="text" name="uniondays" id="uniondays" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label>Hours</label>
                                        <input required type="text" name="unionhours" id="unionhours" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                       <button class="btn btn-primary pull-left" type="submit">Add</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-8">
                                <table class="table table-condensed" id="unioncreditstbl">
                                    <thead>
                                        <th></th>
                                        <th>Credit</th>
                                        <th>Year</th>
                                        <th>Status</th>
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
    <div class="tab-pane fade in " id="portlet_tab3">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Year</label>
                    <input type="text" name="creditsyears" id="creditsyears" class="form-control" />
                </div>
            </div>
        </div>
       <div class="row">
           <div class="col-md-12">
               <table style="margin-bottom: 40px;" class="table table-bordered table-condensed table-hover" id="credistlistemp">
                   <thead>
                   <th></th>
                   <th>Empid</th>
                   <th>Lastname</th>
                   <th>Firstname</th>
                   <th>VL</th>
                   <th>SL</th>
                   <th>EL</th>
                   </thead>
                   <tbody>

                   </tbody>
               </table>
           </div>
       </div>
        <hr>
        <hr>
    </div>
    <div class="tab-pane fade in " id="portlet_tab2">
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">Leave Type Credits</div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-bordered" id="leavecreditstype">
                            <thead>
                            <th></th>
                            <th>Type</th>
                            <th>Control</th>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <button type="button" id="leavetype"
                                class="btn btn-primary popovers"
                                data-container="body" onclick=" "
                                data-trigger="click"
                                data-placement="top"
                                data-html="true"
                                data-content="<input required class='form-control' required type='text' id='names' name='names' placeholder='Enter names'/><br /><input required class='form-control' type='text' id='desc' desc='leavetype'  placeholder='Enter descriptions'/><br /><button id='saveleavetypebtn' class='btn btn-primary btn-md'>Save</button><button id='closepopover' class='btn btn-danger btn-md'>Close</button>"
                                data-original-title="Leave Type"><i class="fa fa-plus"></i> Add</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">Credits Info</div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="well" id="creditsselected">No selected credits</div>
                                <input type="hidden" id="creditsysid" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="">YEAR</label>
                                <input type="text" id="yeartxt" name="yeartxt" class="form-control"/>
                                <label class="">No. Days</label>
                                <input type="text" id="nodays" name="nodays" class="form-control"/>
                                <label class="">No. Hours</label>
                                <input type="text" id="nohours" name="nohours" class="form-control"/>
                            </div>
                            <div class="col-md-8" style="margin-top: 28px;">
                                <button style="width: 100%;" id="btn_applyall" type="button" class="btn btn-default input-block-level"><i class="fa fa-share-all"></i> APPLY TO ALL</button>
                                <br>
                                <br>
                                <a style="width: 100%;" id="btn_selected" href="#form_select_employee_for_leave_credits" data-toggle="ajax-modal" data-arr="" data-view=""  class="btn btn-primary input-block-level"><i class="fa fa-check-square"></i> SELECTED ONLY</a>
                                <br>
                                <!-- <button id="copyyearbtn" type="button" class="btn btn-primary input-block-level"><i class="fa fa-copy"></i> COPY TO NEXT YEAR</button> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in active" id="portlet_tab1">
        <form id="submitflexi" action="<?php echo base_url() ?>hris/submitflexi" method="post">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Employee</label>
                    <input type="text" name="empsubmitflexiloyee" id="employee" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" id="trntype" name="trntype">
                        <option value="1">Day</option>
                        <option value="2">Hour</option>
                    </select>
                </div>
                <div id="day">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" name="fromdate" id="fromdate" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" name="todate" id="todate" class="form-control" />
                    </div>
                </div>
                <div id="hourly" class="hidden">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>hh</label>
                                <input type="text" name="fromflexihour" id="fromflexihour" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>mm</label>
                                <input type="text" name="fromfleximinutes" id="fromfleximinutes" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sel1">AM/PM</label>
                                <select class="form-control" id="fromflexiampm" name="fromflexiampm">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>hh</label>
                                <input type="text" name="toflexihour" id="toflexihour" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>mm</label>
                                <input type="text" name="tofleximinutes" id="tofleximinutes" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sel1">AM/P</label>
                                <select class="form-control" id="toflexiampm" name="toflexiampm">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Start of Flexi</label>
                    <input type="date" name="startofflexi" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Expiration</label>
                    <select class="form-control" id="expiration" name="expiration">
                        <option value="1">1 Month</option>
                        <option value="2">2 Months</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Purpose</label>
                    <textarea class="form-control" name="purpose" rows="5" id="comment"></textarea>
                </div>
                <div class="form-group">
                    <button class="pull-right btn btn-primary" type="submit">Add</button>
                </div>
            </div>
        </form>

    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/hrmain.js"></script>

<script type="text/javascript">
    MAINTENACE.initleavecreditsentry();
    $('#trntype').select2({
        "allowClear" : true
    });
    $('#expiration').select2({
        "allowClear" : true
    });
    $('#creditsyears' , document).select2('val' , '');
    PECO.select2Basic($('#employee') , 'hris/getemployees' , 'Select Employee' , false , false , false);
    PECO.select2Basic($('#creditsyears' , document) , 'systems/select2year' , 'Select Year' , false , false , false);
    PECO.select2Basic($('#unionyear' , document) , 'systems/select2year' , 'Select Year' , false , false , false);

</script>