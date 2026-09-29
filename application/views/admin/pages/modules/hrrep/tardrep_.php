<div class="row">
    <div class="portlet">
        <div class="portlet-title">
            <div class="caption">
                Monthly Tardiness Report
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Month:</label>
                        <input type="text" name="month" id="month" class="form-control" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Year:</label>
                        <input type="text" name="year" id="year" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <button style="margin-top: 26px !important;" class="btn btn-primary" id="searchtardibtn"><i class="fa fa-search"></i> Search</button>
                        <button style="margin-top: 26px !important;" class="btn btn-default" id="printmonthlytard"><i class="fa fa-print"></i> Print Summary</button>
                        <button style="margin-top: 26px !important;" class="btn btn-default" id="printmonthlytarddetails"><i class="fa fa-print"></i> Print with Details</button>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-responsive table-hover tbl-xs" id="tardinesstable">
                <thead>
                <th></th>
                <th>Employee</th>
                <th>Workshift Assigned</th>
                <th>Date</th>
                <th>Am In</th>
                <th>Am Out</th>
                <th>Am Late</th>
                <th>Pm In</th>
                <th>Pm Out</th>
                <th>Pm Late</th>
                <th>Total Late</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>




<script src="<?php echo base_url() ?>assets/pages/tardrep/tardrep.js"></script>

<form id="submittimemodify" action="<?php echo base_url() ?>hris/modifyattendance" method="post">
    <div class="modal fade draggable-modal" id="attendancemodal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title"><i class="fa fa-edit text-danger"></i> Attendance Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-12 margin-top-10">
                                        <div class="col-md-6">
                                            <input type="hidden" id="userid" name="userid" />
                                            <div class="input-icon">
                                                <i class="fa fa-calendar"></i>
                                                <input class="form-control" placeholder="Date" id="datetoday" name="today" value="" type="date">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-icon">
                                                <i class="fa fa-pencil"></i>
                                                <select class="form-control" placeholder="Type" id="timetype" name="timetype" value="" type="text">
                                                    <option value="selectime">Select time</option>
                                                    <option value="0">AM IN</option>
                                                    <option value="1">AM OUT</option>
                                                    <option value="2">PM IN</option>
                                                    <option value="3">PM OUT</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 margin-top-10">
                                        <div class="col-md-6">
                                            <div class="input-icon">
                                                <i class="fa fa-clock-o"></i>
                                                <input readonly class="form-control" placeholder="Time" id="oldtimelog" name="oldtimelog" value="" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-icon">
                                                <i class="fa fa-clock-o"></i>
                                                <input class="form-control timepicker timepicker-default" placeholder="Time" id="newtimelog" name="newtimelog" value="" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 margin-top-10">
                                        <div class="col-md-12">
                                            <textarea rows="4" cols="50" class="form-control" name="reason"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn blue">Send for Approval</button>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
</form>

<script>
    TARDREP.init();
</script>
