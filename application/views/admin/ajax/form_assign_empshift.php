<?php
$ids  = $this->input->post('ids');
$branchid  = $this->input->post('view');

$data_arr = explode('-', $ids);
$date = $data_arr[0].'-'.$data_arr[1].'-'.$data_arr[2];
$shiftid = $data_arr[3];


$getbranch = $this->db->select("pcb.desc as branchdesc, pemw.desc as timedesc")->from("prime_company_branch_workshift_matrix as pcbwm")
    ->join("prime_company_branch as pcb","pcb.sysid = pcbwm.branchid","left")
    ->join("prime_employee_main_workshift as pemw","pemw.sysid = pcbwm.workshiftid","left")
    ->where(array("pcbwm.workshiftid" => $shiftid , "pcbwm.branchid" => $branchid))
    ->get()->row();
?>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <div class="col-md-4">
                <label>Date:</label>
                <label><?php echo $date; ?></label>
            </div>
            <div class="col-md-8">
                <label>Time Shift:</label>
                <label><?php echo ($getbranch) ? $getbranch->branchdesc.' - '.$getbranch->timedesc : ''; ?></label>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-6" style=" height: 200px;overflow-y: scroll;">
            <table class="table table-bordered table-hover table-responsive tbl-sm" id="teamtable">
                <thead>
                    <th></th>
                    <th>Team</th>
                    <th></th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="col-md-6" style=" height: 200px;overflow-y: scroll;">
            <table class="table table-bordered table-hover table-responsive tbl-sm" id="teamemp">
                <thead>
                <th></th>
                <th>Employee</th>
                <th></th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/pages/hris/shiftsched.js"></script>

<script>
    SHIFTSCHED.init(<?php echo $branchid;?>,<?php echo $shiftid;?>,<?php echo $data_arr[0];?>,<?php echo $data_arr[1];?>,<?php echo $data_arr[2];?>);
</script>