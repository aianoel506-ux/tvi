<!-- BEGIN PAGE LEVEL STYLES -->


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<!--<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>-->
<style>
    .form-md-line-input {
        position: relative !important;
    }
    .form-md-line-input .fileinput .input-group-addon{
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }
    .form-md-line-input .select2-container{
        margin-bottom: 0px !important;
    }
    .select2-drop{
        margin-top: -15px !important;
    }
    .portlet.table {
        padding: 0px 0px !important;
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
    .list-group.list-group-xs .list-group-item span,
    .list-group.list-group-xs .list-group-item span.label-name{
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }

    .list-group.list-group-xs .list-group-item span.label-name::after {
        top: -2px !important;
    }
    /*  #confidentialtable_filter{
          margin-top: -50px !important;
      }
      #rankandfiletable_filter{
          margin-top: -50px !important;
      } */
    #confidentialtable tbody tr:hover td{
        background-color: #ffbc42 !important;
    }
    #rankandfiletable tbody tr:hover td{
        background-color: #ffbc42 !important;
    }
    #tierd1table tbody tr:hover td{
        background-color: #ffbc42 !important;
    }
    #tierd2table tbody tr:hover td{
        background-color: #ffbc42 !important;
    }
</style>

<ul id="dashboardview-menu" class="nav nav-tabs">

    <li class="">
        <a href="#tierd1" data-toggle="tab" aria-expanded="true">
            <i class="fa fa-users"></i> Tier 1</a>
    </li>
    <li class="">
        <a href="#tierd2" data-toggle="tab" aria-expanded="true">
            <i class="fa fa-users"></i> Tier 2</a>
    </li>
    <li class="active">
        <a href="#rankandfile" data-toggle="tab" aria-expanded="true">
            <i class="fa fa-users"></i> Rank and File</a>
    </li>
    <li class="">
        <a href="#confidential" data-toggle="tab" aria-expanded="true">
            <i class="fa fa-users"></i> Confidential</a>
    </li>
    <li class="pull-right">
        <a href="#encoded" data-toggle="tab" aria-expanded="true">
            <i class="fa fa-pencil"></i> Encoded</a>
    </li>
    <li class="pull-right">
        <a href="#employee" data-toggle="tab" aria-expanded="true">
            <i class="fa fa-pencil"></i> Employee</a>
    </li>


    <!-- <a  style="margin-left: 5px !important;" href="#form_annual_taxation" id="annualtaxbtn" data-toggle="ajax-modal" data-view="" data-arr="" class="btn btn-primary pull-right btn-sm">Annual Taxation</a> -->
    <a style="margin-left: 5px !important;" href="<?php echo base_url() ?>module/f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59/list"  class="btn btn-primary pull-right btn-sm">Employees</a>
    <!-- <button style="margin-left: 5px !important;"  id="savepayrollbtn" class="btn btn-sm btn-primary pull-right"><i class="fa fa-save"></i> Save</button> -->
</ul>

<div class="tab-content">

    <div class="tab-pane fade in " id="tierd1">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <input  type="text" name="tierd1month" id="tierd1month" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input  type="text" name="tierd1year" id="tierd1year" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select  class="form-control" id="tierd1typehalf">
                                <option value="1">1st Half</option>
                                <option value="2">2nd Half</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input id="noofholidaystierd1"  type="text" class="form-control" placeholder="No. of Holidays">
                            <span class="input-group-btn">
                                        <button data-payclass="3077" id="applyholidaybtn" class="btn btn-default" type="button"> Apply</button>
                                      </span>
                        </div><!-- /input-group -->

                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-2 pull-right">
                        <div id="tierd1encodestatus"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">

                        <table class="zui-table table table-hover table-striped table-bordered" id="tierd1table">
                            <thead>
                            <tr></tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade in " id="tierd2">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <input  type="text" name="tierd2month" id="tierd2month" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input  type="text" name="tierd2year" id="tierd2year" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select  class="form-control" id="tierd2typehalf">
                                <option value="1">1st Half</option>
                                <option value="2">2nd Half</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input id="noofholidaystierd2"  type="text" class="form-control" placeholder="No. of Holidays">
                            <span class="input-group-btn">
                                        <button data-payclass="3078" id="applyholidaybtn" class="btn btn-default" type="button"> Apply</button>
                                      </span>
                        </div><!-- /input-group -->

                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-2 pull-right">
                        <div id="tierd2encodestatus"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">

                        <table class="zui-table table table-hover table-striped table-bordered" id="tierd2table">
                            <thead>
                            <tr></tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade in " id="employee">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    Payroll Employee
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                   <form id="submitpayrollnewemployee" action="<?php echo base_url() ?>payroll/addnewpayrollemp" method="post">
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Employee</label>
                               <input type="text" name="payrollemployee" id="payrollemployee" class="form-control"/>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Account No.</label>
                               <input type="text" placeholder="9 digits only" name="payrollaccountno" maxlength="9" id="payrollaccountno" class="form-control"/>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Payclass</label>
                               <select class="form-control" name="payrollpayclass" id="payrollpayclass">
                                   <option value="2">Confidential</option>
                                   <option value="1">Rank and File</option>
                                   <option value="3">Tier 2</option>
                                   <option value="4">Tier 1</option>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Type</label>
                               <select class="form-control" name="payrolltype" id="payrolltype">
                                   <option value="1">Main Office</option>
                                   <option value="2">Power Plant</option>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-1">
                           <button type="submit" style="margin-top: 26px;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</button>
                       </div>
                   </form>
                </div>
                <hr>
                <table class="table table-bordered table-condensed table-responsive table-hover" id="payrollemployeetbl">
                    <thead>
                    <th></th>
                    <th>Lastname</th>
                    <th>Firstname</th>
                    <th>Account No.</th>
                    <th>Payclass</th>
                    <th>Group</th>
                    <th>Status</th>
                    <th></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in " id="encoded">
        <div class="row">
            <form id="submitencoded" action="<?php echo base_url() ?>payroll/fetchencoded" method="post">
                <div class=col-md-2>
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" name="monthencoded" id="monthencoded" class="form-control" />
                    </div>
                </div>
                <div class=col-md-2>
                    <div class="form-group">
                        <label>Year</label>
                        <input type="text" name="yearencoded" id="yearencoded" class="form-control" />
                    </div>
                </div>
                <div class=col-md-2>
                    <div class="form-group">
                        <label>Payclass</label>
                        <select class="form-control" name="payclassencoded" id="payclassencoded">
                            <option value="129">Confidential</option>
                            <option value="128">Rank And File</option>
                            <option value="3077">Tier 1</option>
                            <option value="3078">Tier 2</option>
                        </select>
                    </div>
                </div>
                <div class=col-md-2>
                    <div class="form-group">
                        <label>Paytype</label>
                        <select  class="form-control" name="paytypeencoded" id="paytypeencoded">
                            <option value="1">1st Half</option>
                            <option value="2">2nd Half</option>
                        </select>
                    </div>
                </div>
                <div class=col-md-2>
                    <div class="form-group">
                        <button type="submit" style="margin-top: 26px;" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <!--  <div class="col-md-12">
                  <h3><i  class="fa fa-shield  text-danger"></i> Payroll Manual Encoded Transaction</h3>
              </div> -->
            <div class="col-md-12">
                <table class="table table-bordered table-responsive table-hover tbl-sm" id="encodedtable">
                    <thead>
                    <th></th>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Inserted Amt</th>
                    <th>Amount</th>
                    <th>Created By</th>
                    <th>Date Created</th>
                    <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>


        </div>
    </div>
    <div class="tab-pane fade in " id="confidential">

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <input   type="text" name="confidentialmonth" id="confidentialmonth" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input   type="text" name="confidentialyear" id="confidentialyear" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group hidden">
                            <select class="form-control" id="confipaytype">
                                <option value="0">Distributed</option>
                                <option value="1">1st Half</option>
                                <option value="2">2nd Half</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <?php
                        /*  $checkforholiday = $this->db->select("sysid")->from("payroll_transactions")
                              ->where(array("status" => 1 , "typesid" => 263 , "months" => date('m') , "years" => date('Y')))
                              ->get()->row();
                          if(!$checkforholiday){ */
                        ?>
                        <div class="input-group">
                            <input id="noofholidaysconfidential"  type="text" class="form-control" placeholder="No. of Holidays">
                            <span class="input-group-btn">
                                        <button data-payclass="129" id="applyholidaybtn" class="btn btn-default" type="button">Apply</button>
                                      </span>
                        </div><!-- /input-group -->
                    </div>

                    <div class="col-md-2">
                    </div>
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-2 pull-right">
                        <div id="confipayrollencodestatus"></div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-12">

                        <table  class="zui-table table table-hover table-striped table-bordered" id="confidentialtable">
                            <thead>
                            <tr>

                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
        <br>
        <br>
        <br>
    </div>
    <div class="tab-pane fade in active"  id="rankandfile" >
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <input  type="text" name="ranknfilemonth" id="ranknfilemonth" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input  type="text" name="ranknfileyear" id="ranknfileyear" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select  class="form-control" id="typehalf">
                                <option value="1">1st Half</option>
                                <option value="2">2nd Half</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input id="noofholidaysranknfile"  type="text" class="form-control" placeholder="No. of Holidays">
                            <span class="input-group-btn">
                                        <button data-payclass="128" id="applyholidaybtn" class="btn btn-default" type="button"> Apply</button>
                                      </span>
                        </div><!-- /input-group -->

                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-2 pull-right">
                        <div id="rnfpayrollencodestatus"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">

                        <table class="zui-table table table-hover table-striped table-bordered" id="rankandfiletable">
                            <thead>
                            <tr></tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <br>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.fixedColumns.min.js"></script>



<script src="<?php echo base_url() ?>assets/pages/payroll/hrdataentry.js"></script>

<script>
    $('#confipaytype' , document).select2({
        "allowClear" : true
    });
    $('#payrollpayclass' , document).select2({
        "allowClear" : true
    });
    $('#payrolltype' , document).select2({
        "allowClear" : true
    });
    HR_PAYROLL_DATA_ENTRY.init();
</script>