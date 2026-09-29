<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption" style="width: 75%;">
                    <p>
                        <span class="label label-danger">NOTE: </span>
                        &nbsp;<i class="text-danger">Format value of leave is Days - Hours - Minutes</i>
                    </p>
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="yearcredits" id="yearcredits" class="form-control" />
                        </div>
                        <div class="col-md-3">
                            <select id="jobcat" name="jobcat" class="form-control input-sm">
                                <option value=""></option>
                                <option value="1">Regular</option>
                                <option value="2">Tiered</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="costcentercredits" id="costcentercredits" class="form-control" />
                        </div>

                        <div class="col-md-2  pull-right" style="margin-bottom: 10px;">
                            <button id="printleavecredits" class="btn btn-primary">Print <i class="fa fa-print"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet-body">

                <h3>Credit Balance Report</h3>
                <table class="table table-bordered table-condensed" id="empcreditstbl">
                    <thead>
                    <th></th>
                    <th>Employee</th>
                    <?php
                    $leavereparr = array(
                        330,331,332,333,334,335
                    );
                    $sql = $this->db->select("sysid , names")->from("prime_types_parameter")
                        ->where(array("codes" => 'LEAVECREDITS' , "status" => 1))
                        ->where_in("sysid" , $leavereparr)
                        ->get();
                    if($sql->num_rows() > 0){
                        foreach ($sql->result() as $row){
                            echo '<th>'.$row->names.'</th>';
                        }
                    }
                    ?>
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

    MAINTENACE.initleavecredits();

</script>