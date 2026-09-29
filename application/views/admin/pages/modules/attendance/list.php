
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/clockface/css/clockface.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />

<h3 class="page-title">
    <i class="fa <?php echo $pageicon; ?> fa-fw text-<?php echo $pageclass; ?>"></i><span class="text-<?php echo $pageclass; ?>"><?php echo $pagetitle; ?></span><small> view</small>
</h3>

<!-- START PAGE CONTENT-->
<div class="portlet light">
    <div class="portlet-title tabbable-line">
        <div style="width: 40%; display: inline-block;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-icon">
                            <i class="fa fa-calendar"></i>
                            <input class="form-control" id="to_date" value="<?php echo sql_time()->DATENUM; ?>" type="date" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input class="form-control" id="dept" value="" type="text" placeholder="Department"/>
                    </div>
                </div>
            </div>

        </div>
        <ul class="nav nav-tabs" id="pay_class">
            <li class="type active" data-id="0">
                <a href="#all" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-search"></i> All </a>
            </li>
            <?php
            $pay_class = $this->db->select()->from('prime_types_parameter')
                ->where(array('codes' => 'EMPAYCLASS' , 'status' => 1))->get();
            if($pay_class->num_rows() > 0) {
                $num = 0;
                $drop_down = '';
                foreach($pay_class->result() as $row) {
                    if($num<2) {
                        echo '<li class="type" data-id="' . $row->sysid . '">';
                        echo '<a href="#' . $row->names . '" data-toggle="tab"><i class="fa fa-tag"></i> ' . $row->names . ' </a>';
                        echo '</li>';
                    }else{
                        $drop_down .= '<li class="type" data-id="' . $row->sysid . '">';
                        $drop_down .= '<a href="#' . $row->names . '" data-toggle="tab"><i class="fa fa-tag"></i> ' . $row->names . ' </a>';
                        $drop_down .= '</li>';
                    }
                    $num++;
                }
                echo '<li class="dropdown pull-right tabdrop">';
                echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">';
                echo '<i class="fa fa-ellipsis-v"></i> More';
                echo '</a>';
                echo '<ul class="dropdown-menu">'.$drop_down.'</ul>';
                echo '</li>';

            }
            ?>
    </div>
    <div class="portlet-body">
        <div class="tab-content">
            <div class="tab-pane active" id="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a style="margin-left: 5px !important;" href="#tbl_onleave" id="annualtaxbtn" data-toggle="ajax-modal" data-view="" data-arr="" class="btn btn-primary btn-sm">On leave</a>
                            <button class="btn btn-primary btn-sm" id="generateattendbtn">Generate Attendance</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon">From</span>
                            <input type="date" id="datestart" name="datestart" class="form-control " value="2019-08-05">
                            <span class="input-group-addon">To</span>
                            <input type="date" id="dateend" name="dateend" class="form-control " value="2019-08-09">
                            <span class="input-group-btn">
                                <button id="submitfiltersearch" type="button" class="btn btn-default"> <i class="fa fa-search"></i> Generate Tardiness</button>
                                </span>
                        </div>
                    </div>
                </div>
                <table width="100%" class="table table-bordered table-hover table-striped table-condensed tbl-sm" id="tbl_attendance">
                    <thead>
                    <tr>
                        <th rowspan="2" width="10px;"></th>
                        <th rowspan="2" width="15px;"><i class="fa fa-navicon"></i></th>
                        <th rowspan="2">Name</th>
                        <th colspan="3" class="info">AM</th>
                        <th colspan="3" class="warning">PM</th>
                        <th colspan="3">IRREG.</th>
                        <th colspan="2">OT</th>
                        <th colspan="2">LOCATOR</th>
                        <th colspan="3">Total</th>
                        <th colspan="2" class="danger"></th>
                    </tr>
                    <tr>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>Late</th>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>Late</th>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>Late</th>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>OUT</th>
                        <th>IN</th>
                        <th>Late</th>
                        <th>Locator</th>
                        <th>OT</th>
                        <th>Status</th>
                        <th><i class="fa fa-wrench"></i></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

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
                                                <input readonly class="form-control" placeholder="Date" id="datetoday" name="today" value="" type="date">
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
                                                <input  readonly class="form-control" placeholder="Time" id="oldtimelog" name="oldtimelog" value="" type="text">

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-icon">
                                                <i class="fa fa-clock-o"></i>
                                                <input  class="form-control timepicker timepicker-default" placeholder="Time" id="newtimelog" name="newtimelog" value="" type="text">
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
</form>


<script src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>


<script src="<?php echo base_url(); ?>assets/pages/hris/view.js" type="text/javascript"></script>
<script type="text/javascript">
    HRIS.attendancedaily();
    $("#attendancemodal").draggable({
        handle: ".modal-header"
    });
</script>
