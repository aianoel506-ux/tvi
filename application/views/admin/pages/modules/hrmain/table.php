
<div clas="row" style="padding:  20px 20px;">
    <div class="col-md-12">
        <div class="btn-group pull-right">
            <div class="tabbable-line pull-right" style="width: auto;">
                <ul id="dashboardview-menu" class="nav nav-tabs pull-right">
                    <li  >
                        <a href="#department" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-reorder"></i>Departments</a>
                    </li>
                    <li   class="active">
                        <a href="#positions" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-reorder"></i>Positions</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="tab-content" style="padding: 15px 15px;">
    <div class="tab-pane fade in active" id="positions">
        <div class="row">
            <div class="col-md-2">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            Add Position
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="submitposition" action="<?php echo base_url() ?>hris/addpositions" method="post">

                            <div class="form-group">
                                <labe>Names</labe>
                                <input required type="text" name="names" class="form-control input-sm" />
                            </div>
                            <div class="form-group">
                                <labe>Descriptions</labe>
                                <input required type="text" name="descriptions" class="form-control input-sm" />
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Add Position</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            Positions
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-bordered table-responsive tbl-xs" id="positionstable">
                            <thead>
                            <th></th>
                            <th>Codes</th>
                            <th>Names</th>
                            <th>Description</th>
                            <th></th>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in" id="department">
        <div class="row">
            <div class="col-md-2">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            Add Department
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="submitdepartment" action="<?php echo base_url() ?>hris/adddepartment" method="post">
                            <div class="form-group">
                                <label>Codes</label>
                                <input required type="text" name="codes" id="codes" class="form-control input-sm" />
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input required type="text" name="name" id="name" class="form-control input-sm" />

                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input required type="text" name="desc" id="desc" class="form-control input-sm" />
                            </div>
                            <div class="form-group">
                                <label>Floor</label>
                                <input required type="text" name="floor" id="floor" class="form-control input-sm" />
                            </div>
                            <div class="form-group">
                               <button class="btn btn-primary" type="submit">Add</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="col-md-10">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            Department
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-bordered table-responsive tbl-xs" id="departmenttable">
                            <thead>
                                <th></th>
                                <th>Codes</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Floor</th>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>

        </div>

       <!-- <div class="row">
            <table class="table  table-bordered table-responsive tbl-md" id="employeedept">
                <thead>
                    <th></th>
                    <th>Name</th>
                    <th>Department</th>
                </thead>
                <tbody></tbody>
            </table>
        </div> -->
    </div>
    <div class="tab-pane fade in" id="payroll">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    Payroll Matrix
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-3">
                        <form id="submitmatrixform" action="<?php echo base_url() ?>hris/addpayrollmatrix" method="post">
                            <div class="form-group">
                                <label>Codes</label>
                                <input type="text" class="form-control input-sm" name="codes" required/>
                            </div>
                            <div class="form-group">
                                <label>Types</label>
                                <input type="text" class="form-control input-sm" id="typesnames" name="typesnames" />
                            </div>
                            <div class="form-group">
                                <label>Functions</label>
                                <input type="text" class="form-control input-sm" name="functions" required/>
                            </div>
                            <div class="form-group">
                                <label>Effects</label>
                                <input type="text" class="form-control input-sm" name="effects" required/>
                            </div>
                            <div class="form-group">
                                <label>No tax</label>
                                <input type="text" class="form-control input-sm" name="notax" required/>
                            </div>
                            <div class="form-group">
                                <label>Capping</label>
                                <input type="text" class="form-control input-sm" name="capping" required/>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-sm btn-primary" type="submit">Add</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered table-hover table-condensed tbl-sm" id="payrollmatrixtable">
                            <thead>
                            <th></th>
                            <th>Codes</th>
                            <th>Types</th>
                            <th>Functions</th>
                            <th>Effects</th>
                            <th>No Tax</th>
                            <th>Capping</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in" id="attendance">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    Attendance Manual Logs
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
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/hrmain.js"></script>
<script type="text/javascript">
    MAINTENACE.init();
</script>

