

    	<div class="row">


            <div class="col-md-9">
                <div class="portlet light">
                    <!--
                    <div class="portlet-title">
                        <div class="caption">
                            CAPTURE FROM FINAL READING
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label for="get_employee" class="entrylabels">Employee: <span class="required"></span></label>
                                        <input type="text"  class="getemployee form-control" name="getemployee" id="get_employee">
                                    </div>
                                    <div class="col-md-3">
                                        <button style="margin-top: 25px !important;" id="get_btn"  class="getbtn btn btn-md btn-primary add-entry btn-block">GET</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <button style="margin-top: 20px !important;" id="addaccomp" class="addaccomp btn btn-primary btn-block">Add Accomplishment</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <ul class="list-group">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="caption-subject font-green-sharp bold uppercase">Analysis Rechecked</span>
                                                <span class="caption-helper pull-right bold text-danger" id="total_analysis_rechecked">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="caption-subject font-green-sharp bold uppercase">Total Read</span>
                                                <span class="caption-helper pull-right bold text-danger" id="total_read">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="caption-subject font-green-sharp bold uppercase">Total GDLB</span>
                                                <span class="caption-helper pull-right bold text-danger" id="total_gdlb">0</span>
                                            </li>
                                        </div>
                                        <div class="col-md-5">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="caption-subject font-green-sharp bold uppercase">TOTAL SP</span>
                                                <span class="caption-helper pull-right bold text-danger" id="final_sp">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="caption-subject font-green-sharp bold  uppercase">TOTAL RG</span>
                                                <span class="caption-helper pull-right bold text-danger" id="final_rg">0</span>
                                            </li>
                                        </div>
                                    </div>
                                </ul>
                            </div>

                        </div>
                    </div> -->


                    <div class="portlet-title">
                        <div class="caption">Manual Entry</div>
                    </div>

                    <div  class="portlet-body">
                        <div class="row">

                            <div class="col-md-12">
                                <form id="frm_add_accomplishment" method="post" action="<?php echo base_url() ?>hris/addreadingaccomplishment">
                                    <div class="input-group add-accomp">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="select_employee" class="entrylabels">Employee: <span class="required"></span></label>
                                                <input type="text"  class="form-control" name="select_employee" id="select_employee" >
                                            </div>
                                            <div class="col-md-3">
                                                <label for="select_gdlb" class="entrylabels">GDLB: <span class="required"></span></label>
                                                <input type="text"  class="form-control" name="select_gdlb" id="select_gdlb">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="reading_count" class="entrylabels">Read Count: <span class="required"></span></label>
                                                <input type="text"  class="form-control" name="reading_count" id="reading_count">
                                            </div>
                                            <div  class="col-md-3">
                                                <label for="date_reading" class="entrylabels">Date Reading: <span class="required"></span></label>
                                                <input type="date"  class="form-control" name="date_reading" id="date_reading" placeholder="Enter Date Reading here..." >
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" style="margin-top: 24px !important;" class="btn btn-primary add-entry"><i class="fa fa-plus"></i> </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div style="margin-top: 50px !important;" class="col-md-12">
                                <table class="table table-responsive table-hover table-striped table-condensed table-bordered tbl-xs" id="accomplishment_table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>GDLB</th>
                                        <th>Reading Date</th>
                                        <th>No. RDG</th>
                                        <th>Misreading</th>
                                        <th>Type</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bar-chart font-green-sharp hide"></i>
                            <i class="fa fa-file-o" aria-hidden="true"></i> Summary
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Payroll Start: <span class="required"></span></label>
                                <input type="date"  class="form-control" name="payrollstart" id="payrollstart">

                                <label>Payroll End: <span class="required"></span></label>
                                <input type="date"  class="form-control" name="payrollend" id="payrollend">
                            </div>
                            <div class="col-md-12">
                                <button style="margin-top: 10px !important;" id="submit_payroll_btn" class="submitpayroll btn btn-primary input-block-level"><i class="fa fa-paper-plane" aria-hidden="true"></i>
                                    Submit Accomplishment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script src="<?php echo base_url(); ?>assets/pages/mrd/mrdmenu.js"></script>
<script>
    MRDMENU.accomplishment();
</script>
