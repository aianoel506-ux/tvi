<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>


<?php
$first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
$last_day_this_month  = date('Y-m-t');
?>


<div class="form-group" style="float: left; display:inline-block;">
    Date Range
    <div class="input-group input-large date-picker input-daterange" id="schedule_date_range" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
        <input type="text" class="form-control" name="from" id="schedule_date_start" value="<?php echo $first_day_this_month; ?>">
        <span class="input-group-addon"> to </span>
        <input type="text" class="form-control"  name="to" id="schedule_date_end" value="<?php echo $last_day_this_month; ?>">
    </div>
</div>
<div class="form-group" style="float: left; margin-left: 10px; display:inline-block;">

    Billing Date
    <div class="input-group input-large" id="schedule_date_billing">
        <span class="input-group-addon"> Yr. </span>
        <input type="text" class="form-control" style="width: 80px;" name="billyr" id="schedule_billyr" value="<?php echo date('Y');?>">
        <span class="input-group-addon"> Mo. </span>
        <select style="width: 150px;" class="form-control select2" name="billmo" id="schedule_billmo">
            <?php

            for($i=1; $i<=12; $i++) {
                if($i==date('m')) {
                    $selected = 'selected';
                }else{
                    $selected = '';
                }
                echo '<option '.$selected.' value="'.$i.'">'.date_formating($i, 'm', 'F').'</option>';
            }
            ?>
        </select>
        <span class="input-group-addon">Type</span>
        <select style="width: 150px;" class="form-control select2" name="types" id="schedule_types">
            <option value="1">Detailed</option>
            <option value="2">Summary</option>
        </select>

        <span class="input-group-btn">
            <button id="btn_get_reports" class="btn btn-primary"><i class="fa fa-search"></i></button>
        </span>
    </div>
</div>

<div class="btn-group pull-right" style="margin-top: 15px;">
    <button class="btn btn-success" id="btn_excel_report"><i class="fa fa-file-excel-o"></i> Get</button>
    <button class="btn btn-default" id="btn_print_report"><i class="fa fa-print"></i> Print</button>
</div>

<div class="row">
    <div class="col-md-12">
        <table id="tbl_reading_reports" class="table table-hover table-bordered table-condensed tbl-sm">
            <thead>
            <th width="200px">Reader</th>
            <th>Date</th>
            <th>GDLB</th>
            <th class="text-align-center">Read</th>
            <th class="text-align-center">Unread</th>
            <th class="text-align-center">Recheck</th>
            <th class="text-align-center">Total</th>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="warning">
                        <h3 class="text-align-center"><i class="fa fa-warning text-danger"></i> No data query..</h3>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<hr>
<hr>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>


<script src="<?php echo base_url(); ?>assets/pages/mrd/reports.js"></script>
<script type="text/javascript">
    MRDREP.init();
    MRDREP.reading();
</script>
