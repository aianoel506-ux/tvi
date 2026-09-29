<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 7/12/2018
 * Time: 4:04 PM
 */



?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <button id="pceosaveempsalaryinc" type="button" class="btn btn-primary">Save Salary</button>
                </div>
            </div>
            <div class="portlet-body">




                <table class="table table-bordered table-hover table-responsive table-condensed tbl-xs tbl-zoom" id="pceosalaryinc">
                    <thead>
                    <th></th>
                    <th>Emp. Code</th>
                    <th>Lastname</th>
                    <th>Firstname</th>
                    <th>Department</th>
                    <th>Basic</th>
                    <th>Increase Amount</th>
                    <th>Total</th>
                    <th>Control</th>
                    </thead>

                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/pages/hris/salaryinc.js"></script>

<script>
    SALARYINC.pceoview(<?php echo $dataid; ?>);
</script>
