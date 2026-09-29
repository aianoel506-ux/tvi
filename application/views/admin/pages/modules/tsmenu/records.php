<style>
    .table-striped>tbody>tr:nth-child(odd)>td,
    .table-striped>tbody>tr:nth-child(odd)>th {
        background-color: #9ce9ff;
    }
    #shiftassignmenttable tbody tr td{
       height: 17px !important;
    }
    #tstableshift{
        table-layout: fixed !important;
    }
    #tstableshift_filter{
        margin-top:30px !important;
        float:left !important;
    }

    .table-condensed .md-checkbox.checkonly {
        width: 20px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
    .table-condensed .md-checkbox.checkonly label {
        width: 20px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
    .table thead {

        background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjMwJSIgc3RvcC1jb2xvcj0iI2Y2ZjZmNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjMwJSIgc3RvcC1jb2xvcj0iI2Y2ZjZmNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNlNWU1ZTUiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+) !important;
        background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 30%, #f6f6f6 30%, #e5e5e5 100%) !important;
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(30%,#f6f6f6), color-stop(30%,#f6f6f6), color-stop(100%,#e5e5e5)) !important;
        background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ) !important;

    }
    .table tr.odd td.zui-sticky-col
    {
        background: rgba(73,169,255,0.30) !important;
    }
    .table tr.even td.zui-sticky-col
    {
        background: rgba(73,169,255,0.15) !important;
    }
    td.highlight {
        background-color: whitesmoke !important;
    }
    .table td {
        position: relative;
        min-height: 50px !important;
    }
    .table td .list-group-item{
        position: relative;
    }

    .DTFC_LeftBodyWrapper {
        z-index: 200;
        background: #fff;
        bottom: -20px !important;
        margin-bottom: -20px !important;
    }

    .table#tbl_schedule_calendar td .btn {
        display: none;
        z-index: 100;
    }

    .table#tbl_schedule_calendar td .btn#btn_edit {
        position: absolute;
        top: 0px;
        right: -20px;
        padding: 1px 2px !important;
    }
    .table#tbl_schedule_calendar td .btn#btn_delete_all {
        position: absolute;
        top: 20px;
        right: -20px;
        padding: 1px 2px !important;
    }

    .table#tbl_schedule_calendar td .btn#btn_delete {
        position: absolute;
        top: -3px;
        right: -25px;
    }
    .table td.date-gdlb:hover {
        border-colo: rgba(0,0,0,0.30);
        -webkit-box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        -moz-box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        -o-box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        box-shadow: rgba(0,0,0,0.30) 0px 0px 10px;
        z-index: 100;
    }
    .table td:hover .btn#btn_delete_all,
    .table td:hover .btn#btn_edit{
        display: inline-block;
    }
    .table td .list-group-item:hover .btn#btn_delete {
        display: inline-block;
    }
    .dataTables_scrollHead {
        overflow: hidden !important;
    }
    .table .list-group.list-group-xs .list-group-item {
        height: 20px !important;
    }
    #shifttablesched > tbody  > tr > td.space{
        height: 2px !important;
    }

</style>
<div class="row">
    <div class="col-md-12">
        <div class="btn-group pull-right">
            <div class="tabbable-line pull-right" style="width: auto;">
                <ul id="dashboardview-menu" class="nav nav-tabs pull-right">

                    <?php
                   if(user_id() == 84 || user_id() == 1){
                        ?>
                        <li class="">
                            <a href="#ts"  data-toggle="tab" aria-expanded="true">
                                <i class="fa fa-reorder"></i>Trouble Shooters</a>
                        </li>
                        <?php
                    }
                    ?>

                    <?php
                    if(user_id() == 77 || user_id() == 1){
                    ?>

                    <li>
                        <a  href="#shifting" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-file-o"></i> SWB Shifting</a>
                    </li>
                        <?php
                    }
                    ?>


                    <li>
                        <a href="#branches"  data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-reorder"></i>Branches</a>
                    </li>
                    <li class="active">
                        <a href="#tssbemployee"  data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-reorder"></i>Employee</a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>


<div class="tab-content">

    <div class="tab-pane fade in" id="ts" style="margin-top: 20px !important;">
       <div class="row">
           <div class="col-md-5">
               <div class="input-group">
                   <input type="text" class="form-control input-sm" id="monthts"  placeholder="Month" />
                   <span class="input-group-btn" style="width:0px;"></span>
                  <!-- <input type="text" class="form-control input-sm" id="dayts"  placeholder="Day" />
                   <span class="input-group-btn" style="width:0px;"></span> -->
                   <input type="text" class="form-control input-sm" id="yearts" placeholder="Year"  />
                   <span class="input-group-btn" style="width:0px;"></span>
                   <input type="text" class="form-control input-sm" id="typets" placeholder="Type"  />
               </div>

           </div>
           <div class="col-md-7">
               <div class="btn-group">
                   <button type="button" id="filtertssched" class="btn btn-default">Filter</button>
                   <a href="#addtssched" id="addtssched" data-toggle="ajax-modal" data-view="" data-arr="" class="btn btn-primary "><i class="fa fa-plus"></i> Add</a>
               </div>

               <button id="tsreportbtn" class="btn btn-primary pull-right">Report</button>
           </div>
       </div>
       <div class="row" style="margin-top: 20px !important;">
            <div class="col-md-12">
                <div id="tstable"></div>
            </div>
       </div>

    </div>

    <div class="tab-pane fade in active" id="tssbemployee">
        <div class="col-md-3">
            <form id="submitemptosched" action="<?php echo base_url() ?>ts/submitemptosched" method="post">
                <div class="form-group">
                    <label>Select Employee</label>
                    <input type="text" name="employeelist" id="employeelist" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="emptype" class="form-control" id="emptype">
                        <option value="sb">Substation</option>
                        <option value="ts">Trouble shooter</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-default pull-right">Add</button>
                </div>
            </form>
        </div>
        <div class="col-md-8">
            <table class="table table-bordered table-responsive table-hover tbl-sm" id="operationemptbl">
                <thead>
                    <th></th>
                    <th>Name</th>
                    <th></th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade in" id="branches">
        <div class="portlet light">
            <div  class="portlet-title">
                <div class="caption">
                    Branches
                </div>
            </div>
            <div class="portlet-body">
               <div class="row">
                   <div class="col-md-3">
                       <form id="submitbranch" action="<?php echo base_url() ?>ts/addbranch" method="post">
                           <div class="form-group">
                               <label>Code</label>
                               <input required type="text" name="codetxt" id="codetxt" class="form-control input-sm" />
                           </div>
                           <div class="form-group">
                               <label>Descriptions</label>
                               <input required type="text" name="desctxt" id="desctxt" class="form-control input-sm" />
                           </div>
                           <div class="form-group">
                               <label>Type</label>
                               <input required type="text" name="typebranch" id="typebranch" class="form-control input-sm" />
                           </div>
                           <div class="form-group">
                               <label>Address</label>
                               <input  type="text" name="addresstxt" id="addresstxt" class="form-control input-sm" />
                           </div>
                           <div class="form-group">
                               <label>Contact No.</label>
                               <input  type="text" name="contacttxt" id="contacttxt" class="form-control input-sm" />
                           </div>
                           <div class="form-group">
                               <button type="submit" class="btn btn-primary pull-right">Add Branch</button>
                           </div>
                       </form>
                   </div>
                   <div class="col-md-9">
                       <table class="table table-bordered  table-responsive tbl-sm" id="branchestable">
                           <thead>
                           <th></th>
                           <th>Code</th>
                           <th>Descriptions</th>
                           <th>Address</th>
                           <th>Contact No</th>
                           <th></th>
                           </thead>
                           <tbody></tbody>
                       </table>
                   </div>
               </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in" id="shifting">
        <div class="portlet">
            <div class="portlet-title">
                <div style="margin-top: 20px;" class="caption pull-right">
                    Employee Shifting Schedule <?php echo date('Y'); ?>
                </div>
                <div class="row">
                    <form method="post" action="<?php echo base_url() ?>ts/getdatasched" id="getdatasched">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Type</label>
                                <select id="typehalfshift" class="form-control" name="typedata">
                                    <option value="1">1st Half</option>
                                    <option value="2">2nd Half</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Month</label>
                                <input type="text" name="month" id="monthshift" autocomplete="off" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button style="margin-top: 25px !important;" type="submit" class="btn btn-default ">View <i class="fa fa-search"></i></button>
                        </div>

                        <div class="col-md-3" style="margin-top: 21px;">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a  href="#form_add_employee_tosched" title="Assign Team/Employee Group" data-toggle="ajax-modal" class="btn  btn-primary">Add <i class="fa fa-plus"></i></a>
                                <button type="button" class="btn btn-secondary">|</button>
                                <button  type="button" id="reportschedbtn" class="btn btn-primary ">Report</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="portlet-body">

                <div class="row">
                    <div class="col-md-12">
                        <div id="tabledata"></div>

                    </div>
                </div>
            </div>
        </div>

    </div>


</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

<script src="<?php echo base_url() ?>assets/pages/hris/shiftsched.js"></script>
<script>
    SHIFTSCHED.init();
    SHIFTSCHED.empoperationtbl(<?php echo user_id() ?>);

</script>