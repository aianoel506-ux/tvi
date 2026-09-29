<?php

$ids = $this->input->post('ids');
$view = $this->input->post('view');
$data_arr = explode('/', $view);
$fromdate = $data_arr[0];
$todate = $data_arr[1];
$typedata = $data_arr[2];
$weekdesc = '';
if($ids == 1){
    $weekdesc = 'Monday';
}else if($ids == 2){
    $weekdesc = 'Tuesday';
}else if($ids == 3){
    $weekdesc = 'Wednesday';
}else if($ids == 4){
    $weekdesc = 'Thursday';
}else if($ids == 5){
    $weekdesc = 'Friday';
}else if($ids == 6){
    $weekdesc = 'Saturday';
}else if($ids == 7){
    $weekdesc = 'Sunday';
}
?>

<input type="hidden" value="<?php echo $fromdate; ?>" id="fromdategroup" />
<input type="hidden" value="<?php echo $todate; ?>" id="todategroup" />
<div class="row">
    <div class="col-md-8" style=" height: 400px;overflow-y: scroll;">
        <table class="table table-bordered table-hover table-condensed table-responsive tbl-sm" id="groupsched">
            <thead>
                <th></th>
                <th>Branch</th>
                <th></th>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div class="col-md-4">


        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $weekdesc; ?> <br><br><?php echo $fromdate.' - '.$todate; ?></h3>
            </div>
            <div class="panel-body">
                <h3 id="itemdesc">No selected !</h3>
                <form id="submitschedule" action="<?php echo base_url() ?>hris/addempschedule" method="post">

                    <input type="hidden" value="<?php echo $ids; ?>" id="dayofweek" name="dayofweek" />
                    <input type="hidden" value="<?php echo $fromdate; ?>" id="fromdate" name="fromdate" />
                    <input type="hidden" value="<?php echo $todate; ?>" id="todate" name="todate" />
                    <input type="hidden" id="branchidhidden" name="branchidhidden" />
                    <input type="hidden" id="workshiftidhidden" name="workshiftidhidden" />
                    <input type="text" name="empid" class="form-control" id="sbtsemployee" /><br /><br />
                    <input type="text" name="team" class="form-control" id="sbtsteam" /><br /><br />
                    <button class="btn btn-primary pull-right" type="submit">Add</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/pages/hris/shiftsched.js"></script>

<script>
    SHIFTSCHED.groupsched(<?php echo $ids ?> , $('#fromdategroup').val() , $('#todategroup').val());

    PECO.select2Basic($('#sbtsemployee') , 'hris/getallsbtsemployee','Select Employee' , false, false,false);
    PECO.select2Basic($('#sbtsteam') , 'hris/getallsbtsteam','Select Team' , false, false,false,false,false,<?php echo $typedata ?>);
</script>