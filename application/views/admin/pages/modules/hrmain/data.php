<?php
/**
 * Created by PhpStorm.
 * User: IT
 * Date: 7/24/2018
 * Time: 4:11 PM
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    Workshift Approval
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-bordered table-hover table-responsive table-condensed tbl-sm" id="workshiftpending">
                    <thead>
                        <th></th>
                        <th>Empid</th>
                        <th>Name</th>
                        <th>Workshift</th>
                        <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
                <hr>
                <button class="btn btn-primary btn-sm" data-id="<?php echo $dataid; ?>" id="approveallbtn">Approve All</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/hris/view.js"></script>
<script>
    HRIS.initpendingworkshift(<?php echo $dataid; ?>);
</script>