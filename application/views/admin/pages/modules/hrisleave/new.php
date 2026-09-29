<?php
$info = get_user_employee_info();

if($info){
    $qry_nav_file = $this->db->select("sysid")->from('prime_module_navigations_main')  //
    ->where('hashcode', $this->uri->segment(2))                         //
    ->get()->row();
    ?>

<div class="row">
    <div class="col-md-12">
        <ul id="dashboardview-menu" class="nav nav-tabs pull-right">
            <li class="active">
                <a href="#new" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-address-book"></i> New</a>
            </li>
            <li class="">
                <a href="#draft" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-calendar"></i> Draft</a>
            </li>
            <li class="">
                <a href="#approved_disapproved" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-calendar"></i> Approved / Disapproved</a>
            </li>
        </ul>

    </div>
</div>


    <div class="tab-content">

        <div class="tab-pane fade in active" id="new">
            <div class="row">
                <div class="col-md-7">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                Leave Info
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="hidden" name="hiddenempid" id="hiddenempid" value="<?php echo $info->sysid; ?>" />
                                            <input type="hidden" name="moduleid" id="moduleid" value="<?php echo $qry_nav_file->sysid; ?>" />
                                            <input type="hidden" name="dataid" id="dataid" value="<?php echo $info->sysid ?>" />
                                            <input type="hidden" id="hiddenleavedays" name="hiddenleavedays" />
                                            <input type="hidden" id="userloggedin" value="<?php echo user_id(); ?>" name="userloggedin" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-body">
                                            <ul class="list-group summary column" id="list_leave_credits"></ul>
                                        </div>
                                        <div style="margin-top: 20px !important;" class="portlet-title">
                                            <div class="caption">Reason</div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="form-group leave-input">
                                                <textarea class="form-control" id="reason" name="reason" maxlength="225" rows="2" placeholder="Enter your reason here..."></textarea>
                                            </div>
                                        </div>
                                    </div>


                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-5">
                    <div class="portlet light">
                        <form id="submitleavereq" action="<?php echo base_url() ?>request/draftleaverequest" method="post">
                        <div class="portlet-title">
                            <div class="caption">
                                Transactions
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <input style="width: 50%; display: inline-block;"  class="form-control" id="selectleavetype" name="selectleavetype" />
                                <input type="hidden" name="hiddenempid" value="<?php echo $info->sysid ?>" />
                                <input style="width: 50%; display: inline-block;" value="" type="text" name="yearleave" id="yearleave" class="form-control pull-right" />
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sel1">Type</label>
                                        <select class="form-control" name="leavetype" id="typeofleave">
                                            <option value="1">Regular Leave</option>
                                            <option value="2">Locator Leave</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <select class="form-control" name="trnofleave" id="trnofleave">
                                            <option value="1">Day(s)</option>
                                            <option value="2">Hour(s)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="daystype">

                                <div class="form-group col-md-6">
                                    <label for="fromdate" >From</label>
                                    <input  type="date" name="fromdate" id="fromdate" class="form-control" />
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="todate" >To</label>
                                    <input  type="date" name="todate" id="todate" class="form-control" />
                                </div>
                            </div>
                            <div id="hourstype" class="hidden">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <input  type="text" name="fromhours" id="fromhours" class="form-control" placeholder="hh"/>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <input  type="text" name="fromminutes" id="fromminutes" class="form-control" placeholder="mm" />
                                    </div>

                                    <div class="form-group col-md-3">
                                        <input  type="text" name="fromseconds"  value="00" id="fromseconds" class="form-control" placeholder="ss" />
                                    </div>

                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="fromampm">
                                            <option>AM</option>
                                            <option>PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <input  type="text" name="tohours" id="tohours" class="form-control" placeholder="hh"/>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <input  type="text" name="tominutes" id="tominutes" class="form-control" placeholder="mm" />
                                    </div>

                                    <div class="form-group col-md-3">
                                        <input  type="text" name="toseconds" value="00" id="toseconds" class="form-control" placeholder="ss" />
                                    </div>

                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="toampm">
                                            <option>AM</option>
                                            <option>PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label>Leave Date</label>
                                        <input type="date" name="leavedate" id="leavedate" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary pull-right" style="margin-right: 13px;">Add</button>
                                </div>
                            </div>


                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade in" id="approved_disapproved">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                Approved / Disapproved Request
                            </div>
                        </div>
                        <div class="portlet-body">

                            <table class="table table-bordered table-condensed" id="approvedleaverequest">
                                <thead>
                                <th></th>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Reason</th>
                                <th>Status</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade in " id="draft">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">Draft Requested</div>
                            <button id="submitrequest" class="btn btn-primary pull-right">Submit Request</button>
                        </div>
                        <div class="portlet-body">

                            <table class="table table-bordered" id="draftrequestleavetbl">
                                <thead>
                                <th></th>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>From Time</th>
                                <th>To Time</th>
                                <th>Type</th>
                                <th>Date Created</th>
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



<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url() ?>assets/pages/request.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/view.js" ></script>
<script>
    REQUEST.init(<?php echo $info->sysid;?> , <?php echo date('Y') ?>);
    REQUEST.leavecredits($('#list_leave_credits'), <?php echo $info->sysid;?>);
    REQUEST.initreqtbl(<?php echo $info->sysid;?>, 307);
    REQUEST.initcomponents();



</script>
<?php
    }else{
        echo "You're not allowed to request leave form.";
    }
?>