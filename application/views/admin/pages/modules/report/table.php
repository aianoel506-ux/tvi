
<div class="row" style="padding: 15px 15px">

    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                Contractual DTR
            </div>
        </div>
        <div class="portlet-body">
            <a href="#form_print_dtr"  data-toggle="ajax-modal" data-view="" data-arr="" class="btn btn-primary  btn-sm"><i class="fa fa-print"></i> Print DTR</a>
        </div>
    </div>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                Regular DTR
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <form id="submitdtrreport" action="<?php echo base_url() ?>hris/getmonthlydtrreport" method="post">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Employees</label>
                            <input  type="text" name="employeeregulardtr[]" multiple="multiple" id="employeeregulardtr" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Month</label>
                            <input type="text" name="month" id="month" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Year</label>
                            <input type="text" name="year" id="year" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" style="margin-top: 26px;" class="btn btn-primary">Generate <i class="fa fa-search"></i></button>
                            <span id="genloading" class="hidden"><i class="fa fa-spinner fa-pulse"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-danger">
                            <strong>Note!</strong> Please use short bondpaper in printing and landscape format.
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                Timelogs Checker
            </div>
        </div>
        <div class="portlet-body">
            <form id="submitattlogs" action="<?php echo base_url() ?>hris/fetchempattlogs" method="post">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <input type="text" name="employeeattlogs" id="employee" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Year</label>
                                    <input type="text" name="yearattlogs" id="yearattlogs" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Month</label>
                                    <input type="text" name="monthattlogs" id="monthattlogs" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="text" name="fromday" id="fromday" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="text" name="today" id="today" class="form-control" />
                                </div>
                            </div>
                            <span class="input-group-btn">
                                                        <div class="form-group">
                                                            <button type="submit" style="margin-top: 25px;" class="btn btn-primary">Search <i class="fa fa-search"></i></button>
                                                        </div>
                                                    </span>

                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>


    <div class="row" style="margin-bottom: 200px !important;">
        <div class="container">
            <div  id="timelogshtml"></div>
        </div>
    </div>

</div>
<script src="<?php echo base_url() ?>assets/pages/hris/attreport.js"></script>
<script>
    $(document).ready(function(){
        $('#employeeregulardtr',document).select2('val','');
    });

    ATTENDANCEREPORT.init();
    PECO.select2Basic($('#yearattlogs'),'systems/select2year','Select Year', false,false,year);
    PECO.select2Basic($('#monthattlogs'),'systems/select2month','Select Month', false,false,false);
    PECO.select2Basic($('#employee'),'hris/getemployees','Select Employee', false,false,false);
    PECO.select2BasicMult($('#employeeregulardtr',document) , 'hris/getallemployeesfordtr' ,false);
</script>