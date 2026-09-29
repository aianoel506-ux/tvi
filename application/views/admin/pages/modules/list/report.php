<div class="row">
    <div class="col-md-2">
        <div>
            <input class="form-control" id="input_jobcat">
        </div>
    </div>
    <div class="col-md-2">
        <div>
            <button type="button" id="print_registers" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print Registers</button>
        </div>
    </div>
    <div class="col-md-8">
        <div class="pull-right">
            <button type="button" id="print_registers_summ" command="summary" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print Registers Summary</button>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <h3 class="color-blue">Employee List</h3>
                </div>
                <div class="tools">

                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-bordered table-hover tbl-xs" id="emplistreptbl">
                    <thead>
                    <th>No.</th>
                    <th>Name</th>
                    <th>D.O.B</th>
                    <th>Age</th>
                    <th>Date Hired</th>
                    <th>Yrs of Srvc</th>
                    <th>Position</th>
                    <th>CC</th>
                    <th>Payclass</th>
                    <th>M/F</th>
                    <th>ID No.</th>
                    <th>PAG-IBIG No.</th>
                    <th>TIN No.</th>
                    <th>SSS No.</th>
                    <th>PHILHEALTH</th>
                    <th>ADDRESS</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/hris/report.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/hris/report23.js"></script>

<script type="text/javascript">
    HRISREP.init();
    //REPORT23.init23();
    PECO.select2Basic($('#brgy') , 'test/getbarangays' , 'Select Barangays' , false,false,false);
    PECO.select2Basic($('#bioid') , 'test/testbioid' , 'Bio ID' , false,false,false);
</script>