<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/10/2018
 * Time: 5:50 PM
 */

?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class=" icon-layers font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Account Info</span>
                    <div class="caption-desc font-grey-cascade">Address</div>
                </div>
            </div>
            <div class="portlet-body">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class=" icon-layers font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Complaints History</span>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-hover table-bordered table-condensed" id="tbl_ticket_log">
                    <thead>
                    <th>#</th>
                    <th>Codes</th>
                    <th>Action</th>
                    <th>Descriptions</th>
                    <th>Comments</th>
                    <th>Time</th>
                    <th>User</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.logs(<?php echo $dataid; ?>);
</script>

