<?php
$date = $this->input->post('ids');
$bioid = $this->input->post('view');

$getempinfo = $this->db->select("p.lastname,p.firstname,pem.empid,pem.sysid,peb.bioid,pemwm.workshift_id")
    ->from("prime_employee_main as pem")
    ->join("person as p","p.sysid  = pem.personid" , "left")
    ->join("prime_employee_bioid as peb" , "peb.empid = pem.sysid","left")
    ->join("prime_employee_main_workshift_matrix as pemwm" , "pemwm.empid = pem.sysid","left")
    ->where(array("peb.bioid" => $bioid,"pemwm.status" => 1))
    ->get()->row();
$name  = ($getempinfo) ? $getempinfo->lastname.', '.$getempinfo->firstname : '';
$bioid  = ($getempinfo) ? $getempinfo->bioid:'';
?>
    <div class="container">
        <div class="row">
        <div class="col-md-9">
            <div class="portlet">
                <div class="portlet-title">
                        <div class="row">
                            <div class="col-md-3">
                                 Name: <b><?php echo $name; ?></b>
                            </div>
                            <div class="col-md-3">
                               Time Logs as of: <b><?php echo $date; ?></b>
                            </div>
                            <div class="col-md-6">
                                <label>Workshift</label>
                                <input type="text" id="attendanceworkshift" class="form-control input-sm" />
                            </div>
                        </div>
                        <input type="hidden" id="emplogdateval" value="<?php echo $date; ?>" />
                        <input type="hidden" id="empid" value="<?php echo $getempinfo->sysid; ?>" />
                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-responsive table-hover tbl-sm" id="timelogstable">
                        <thead>
                        <th></th>
                        <th>Bio ID</th>
                        <th>Log Time</th>
                        <th>Remarks</th>
                        <th>Log Type</th>

                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>


               <!-- <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            Adding Time Logs
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="addtimelogssubmit" action="<?php echo base_url() ?>hris/addtimelogsattendance" method="post">
                            <input type="hidden" name="hiddendate" value="<?php echo $date; ?>" />
                            <input type="hidden" name="hiddenbioid" value="<?php echo $bioid; ?>" />
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Time Logs</label>
                                    <input type="text" name="timelogsinput" id="timelogsinput" class="form-control input-sm" placeholder="Enter time logs" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Remarks</label>
                                    <input type="text" name="remarksinput" id="remarksinput" class="form-control input-sm" placeholder="Enter remarks">
                                </div>
                                <div class="col-md-3">
                                    <label>Log Type</label>
                                    <input type="text" name="logtypeinput" id="logtypeinput" class="form-control input-sm" required>
                                </div>
                                <div class="col-md-3">
                                    <button style="margin-top: 25px !important;" type="submit" class="btn btn-primary input-sm btn-md" id="addtimelogs"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> -->
        </div>
        </div>
    </div>



<script>
    HRIS.initemptattendancetable(<?php echo $bioid; ?>);
    PECO.select2Basic($('#logtypeinput'),'hris/gettimelogsselect2', 'Select log type',false,false,false);
    PECO.select2Basic($('#attendanceworkshift'),'hris/getselect2workshift', 'Emp. workshift',false,false,<?php echo $getempinfo->workshift_id; ?>);
    $(document).on('change','#attendanceworkshift',function (e) {

        var this_ = $(this);
        var this_val = this_.val();
        var this_empid = $('#empid',document).val();


       $.ajax({
            url:PECO.base_url()+'hris/updateworkshift',
            type:'post',
            data:{"empid":this_empid , "val":this_val},
            dataType:'json'
        }).done(function (d) {
            PECO.initAlerts(d.msg, "PECO.net" , d.func);
       }).fail(function () {
           PECO.phpError();
       });
       e.stopImmediatePropagation();
    });
</script>