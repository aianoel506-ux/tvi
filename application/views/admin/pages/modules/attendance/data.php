<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            Pending Attendance Approval
        </div>
    </div>
    <div class="portlet-body">
        <table class="table table-bordered table-hover table-responsive tbl-sm" id="attendancerequesttbl">
            <thead>
            <th></th>
            <th>Emp ID</th>
            <th>Name</th>
            <th>Date logs</th>
            <th>Type</th>
            <th>Time Logs</th>
            <th>Remarks</th>
            <th>Status</th>
            <th></th>

            </thead>
            <tbody>

            </tbody>

        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <table class="table table-bordered table-striped table-hover table-condensed" id="personalinfotbl">
            <thead>
            <th>#</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Address</th>
            <th>Birth Date</th>
            <th>Control</th>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div class="col-md-4">
            <form method="post" id="personentry" action="<?php echo base_url() ?>test/personentry">
                <div class="form-group">
                    <input type="text" class="form-control" name="lname" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="fname" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="mname" placeholder="Middle Name" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="address" placeholder="Address" required>
                </div>
                <div class="form-group">
                    <input type="date" class="form-control" name="bdate" placeholder="Birth Date" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/hrmain.js"></script>
<script>
    MAINTENACE.init_attendance_approval();
</script>
