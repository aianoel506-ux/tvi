<?php
/**
 * Created by PhpStorm.
 * User: DUDEZKIE
 * Date: 7/5/2019
 * Time: 3:38 PM
 */



$id = $this->input->post('ids');

?>

<div class="row">

    <div class="col-md-12">

        <div class="portlet light bordered table">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder font-green-haze"></i> Meter List (Available)
                </div>

                <div class="tools">
                    <button id="btn_refresh_mtrlist" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Refresh</button>
                </div>

            </div>
            <div class="portlet-body">
                <table class="table table-hover table-bordered table-condensed" id="tbl_meter_list">
                    <thead>
                    <th>Mtr No.</th>
                    <th>Serial</th>
                    <th>Type</th>
                    <th>ERC Seal</th>
                    <th>PECO Seal</th>
                    <th>Ampere</th>
                    <th>Volts</th>
                    <th>JO#</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script src="<?php echo base_url(); ?>assets/pages/utility/main.js"></script>

<script>
    UTILITY.mtr(<?php echo $id; ?>);


</script>
