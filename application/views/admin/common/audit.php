<?php

if($flowid == 15) {
?>
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption"> <i class="fa fa-edit"></i> <span class="caption-subject font-green-sharp bold uppercase">Billing Compute</span> <span class="caption-helper"></span> </div>
        <div class="tools"> </div>
    </div>
    <div class="portlet-body">
        <table class="table table-hover table-bordered table-striped table-condensed">
            <thead>
                <th>Rate Class</th>
                <th>KWH</th>
                <th>Amount</th>
                <th>Confirm</th>
            </thead>
            <tbody>
            <?php
            $qry_rateclass = $this->db->query("SELECT * FROM prime_system_rate_class_main");
            if($qry_rateclass->num_rows() > 0) {
                foreach($qry_rateclass->result() as $row) {
                    echo '<tr>';
                    echo '<td>'.$row->classifications.'</td>';
                    echo '<td class="input">';
                    echo '<div class="input-icon left">';
                    echo '<i class="fa fa-pencil"></i>';
                    echo '<input class="form-control input-xs inline" id="input_kwh" data-id="'.$row->sysid.'"></td>';
                    echo '</div>';
                    echo '<td></td>';
                    echo '<td><button class="btn btn-default inline"></button></td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>

