<link rel="stylesheet" href="<?php echo base_url(); ?>assets/global/plugins/orgchart/2.1.9/jquery.orgchart.css" />

<style type="text/css">
    .paging_simple_numbers .pagination li a{
        padding: 5px 8px !important;
    }
    .dataTables_filter {
        padding-right: 20px !important;
    }

    .dataTables_filter input{
        width: 100px !important;
    }

    #chart-container {
        font-family: Arial;
        border: 2px dashed #aaa;
        border-radius: 5px;
        overflow: auto;
        text-align: center;
    }
</style>
<!-- START PAGE CONTENT-->
<div class="row">
    <div class="col-md-12 tabbable-line">
        <ul class="nav nav-tabs ">
            <li class="active">
                <a href="#maintenance" data-toggle="tab"><i class="fa fa-wrench"></i> Maintenance</a>
            </li>
            <li class="">
                <a href="#charts" data-toggle="tab"> <i class="fa fa-area-chart"></i> Chart</a>
            </li>
        </ul>
        <hr>
    </div>
</div>
<div class="row">
    <div class="tab-content">
        <div class="tab-pane fade in active" id="maintenance">
            <div class="col-md-6">

                <div class="portlet light bordered table">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-building font-yellow-gold"></i> Cost Center</div>

                    </div>
                    <div class="portlet-body">

                        <table id="tbl_cc_list" class="table table-hover table-striped table-bordered table-condensed">
                            <thead>
                            <th>#</th>
                            <th>CCID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Head</th>
                            <th>Exec</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet light bordered ">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-building font-yellow-gold"></i> Cost Center Details</div>
                    </div>
                    <div class="portlet-body ">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group summary column">
                                    <li class="list-group-item">
                                        <span class="col-md-5 label-name">Dept. Head</span>
                                        <span class="col-md-7 label-default " id="cc_text_head">N/A</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group summary column">
                                    <li class="list-group-item">
                                        <span class="col-md-5 label-name">Executive</span>
                                        <span class="col-md-7 label-default " id="cc_text_exec">N/A</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <form class="hidden" id="frm_assign_employee" action="<?php echo base_url('itd/assignccemployee');?>" method="post">
                                    <hr>
                                    <input id="input_cc_id" type="hidden" name="ccid"/>
                                    <div class="form-group row" id="">
                                        <div class="col-md-6">
                                            <input class="form-control" name="lastname" id="lastname" placeholder="Search empoyee.." />
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control" readonly name="firstname" id="firstname" placeholder="First name.." />
                                        </div>
                                    </div>
                                    <div class="form-group row" id="">
                                        <div class="col-md-6">
                                            Type
                                            <select class="form-control" name="type" id="input_type">
                                                <option value="1">Primary</option>
                                                <option value="0">Sub</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            Designation
                                            <select class="form-control" name="designation" id="input_designation">
                                                <option value="1">Member</option>
                                                <option value="2">Department Head</option>
                                                <option value="3">Department Executive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class=" btn btn-default pull-right"><i class="fa fa-plus"></i> Assign Employee <i class="fa fa-arrow-right"></i></button>
                                </form>

                            </div>
                            <div class="col-md-12">
                                <h4>Employees</h4>
                                <table id="tbl_cc_employee" class="table table-hover table-striped table-bordered tbl-sm">
                                    <thead>
                                    <th>#</th>
                                    <th>EmpCode</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="charts">
            <div class="portlet light bordered">
                <div class="portlet-body text-align-center" id="chart-container">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->

<script src="<?php echo base_url(); ?>assets/global/plugins/orgchart/2.1.9/jquery.orgchart.js" ></script>

<script src="<?php echo base_url();?>assets/pages/itd/ccmain.js" type="text/javascript"></script>
<script type="text/javascript">
    CCMAIN.init();
</script>
