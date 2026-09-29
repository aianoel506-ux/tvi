<style>
    .table .label {
        margin-left: 2px !important;
    }
    .table td {
        position: relative;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">

            <div class="portlet-body">
                <div class="col-md-6 pull-left" style="margin-left: -15px;">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input style="width: 30%" id="reader_year" name="year" type="text" class="form-control" placeholder="Year" />
                            <input style="width: 30%" id="reader_month" name="month" type="text" class="form-control" placeholder="Month" />
                            <input style="width: 40%" id="reader_dist" name="month" type="text" class="form-control" placeholder="Select district.." />
                            <span class="input-group-btn">
                                <a href="javascript:;" id="btn_scan_sched_reader_data" class="btn btn-default"><i class="fa fa-search  "></i> Scan</a>
                                <a href="javascript:;" id="btn_fix_sched_reader_data" class="btn btn-danger"><i class="fa fa-wrench  "></i> Fix Data</a>
                            </span>
                        </div>

                    </div>
                </div>
                <table id="tbl_mrd_sched_records" class="table table-hover table-bordered table-condensed table-striped">
                    <thead>
                    <th>Records</th>
                    <th>GDLB</th>
                    <th>Accounts</th>
                    <th>Assignments</th>
                    <th>Schedule</th>
                    <th>Read/Assign</th>
                    <th>Status</th>
                    <th>Fix</th>
                    </thead>
                    <tbody></tbody>
                </table>


                <hr>
                <span style="display: inline-block; margin-top: 0px !important; margin-left: 0px; padding: 5px 8px; background: rgba(160, 229, 91, 0.25)">Accounts Count Normal</span>
                <span style="display: inline-block; margin-top: 0px !important; margin-left: 0px; padding: 5px 8px; background: rgba(237, 109, 109, 0.30)">Accounts Count Almost Reach Limit</span>
                <span style="display: inline-block; margin-top: 0px !important; margin-left: 0px; padding: 5px 8px; background: rgba(255, 106, 38, 0.3)">Readings</span>
                <span style="display: inline-block; margin-top: 0px !important; margin-left: 0px; padding: 5px 8px; background: rgba(38, 171, 255, 0.3)">Assigned</span>

            </div>
        </div>
    </div>

</div>



<script src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script>
<script type="text/javascript">
    MRD.datafix();
</script>
