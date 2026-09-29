
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/clockface/css/clockface.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
<div class="tabbable-line">
    <ul class="nav nav-tabs">
        <!--  <li class="active">
          <a href="#portlet_tab3" data-toggle="tab" aria-expanded="false">Flexi Time</a>
      </li>-->
        <li class="active">
            <a href="#portlet_tab1" data-toggle="tab" aria-expanded="true">Workshift</a>
        </li>
        <li class="">
            <a href="#portlet_tab2" data-toggle="tab" aria-expanded="false">Employee Workshift</a>
        </li>


    </ul>
</div>


<div class="tab-content" style="margin-top: 20px;">
    <div class="tab-pane fade in active" id="portlet_tab1">
        <div class="row">
            <div class="col-md-4">
                <form id="submitworkshift" method="post" action="<?php echo base_url() ?>hris/addworkshift">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                Add Workshift
                            </div>
                        </div>
                        <div class="portlet-body">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="amstarthour" placeholder="hh" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="amstartminutes" placeholder="mm" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="amstartampm" id="sel1">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="amendhour" placeholder="hh" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="amendminutes" placeholder="mm" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="amendampm" id="sel1">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="pmstarthour" placeholder="hh" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="pmstartminutes" placeholder="mm" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="pmstartampm" id="sel1">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="pmendhour" placeholder="hh" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="pmendminutes" placeholder="mm" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="pmendampm" id="sel1">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Descriptions</label>
                                <textarea class="form-control" rows="4" cols="50" required name="workshiftdesc"></textarea>
                            </div>

                            <!--  <div class="form-group">
                              <label>AM Start</label>
                              <div class="input-icon">
                                  <i class="fa fa-clock-o"></i>
                                  <input name="amstart" class="form-control timepicker timepicker-default" type="text">
                              </div>
                          </div>
                          <div class="form-group">
                              <label>AM End</label>
                              <div class="input-icon">
                                  <i class="fa fa-clock-o"></i>
                                  <input name="amend" class="form-control timepicker timepicker-default" type="text">
                              </div>
                          </div>
                          <div class="form-group">
                              <label>PM Start</label>
                              <div class="input-icon">
                                  <i class="fa fa-clock-o"></i>
                                  <input name="pmstart" class="form-control timepicker timepicker-default" type="text">
                              </div>
                          </div>
                          <div class="form-group">
                              <label>PM End</label>
                              <div class="input-icon">
                                  <i class="fa fa-clock-o"></i>
                                  <input name="pmend" class="form-control timepicker timepicker-default" type="text">
                              </div>
                          </div>-->


                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"> Save <i class="fa fa-save"></i></button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="col-md-8">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">Workshift List</div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-bordered  table-responsive tbl-xs" id="workshifttable">
                            <thead>
                            <th></th>
                            <th>Codes</th>
                            <th>Description</th>
                            <th>Log Count</th>
                            <th>AM Start</th>
                            <th>AM End</th>
                            <th>PM Start</th>
                            <th>AM End</th>
                            <th>Status</th>
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
    <div class="tab-pane fade in" id="portlet_tab2">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-hover table-condensed table-striped tbl-sm" id="employeeworkshift">
                    <thead>
                        <th></th>
                        <th>Employee</th>
                        <th>TIME 1</th>
                        <th>TIME 2</th>
                        <th>TIME 3</th>
                        <th>TIME 4</th>
                        <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
                <hr>
                <hr>
            </div>
        </div>
    </div>
</div>



    <script src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/hrmain.js"></script>


<script type="text/javascript">
    MAINTENACE.initworkshiftlist();
</script>
