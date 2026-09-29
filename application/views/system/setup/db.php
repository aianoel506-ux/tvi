<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 3/29/2019
 * Time: 4:33 PM
 */

?>

<div class="row">
    <div class="col-md-3">

        <h3 class="font-green bold uppercase"><i class="icon-layers font-red"></i> Welcome</h3>
        <hr>
        <div class="well">
            <b>Note:</b> This module is for super / admin user online.
        </div>

        <ul class="list-group summary page-sidebar-menu">
            <li class="list-group-item"> <span>Monthly Bandwidth Transfer</span>

                <div class="progress progress-striped progress-mini active">
                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                        <span class="sr-only"> 40% Complete (success) </span>
                    </div>
                </div>

                <span class="percent">77%</span>
                <div class="stat">21419.94 / 14000 MB</div>
            </li>

            <li class="list-group-item"> <span>Disk Space Usage</span>

                <div class="progress progress-striped progress-mini active">
                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" style="width: 65%">
                        <span class="sr-only"> 65% Complete (success) </span>
                    </div>
                </div>

                <span class="percent">65%</span>
                <div class="stat">2600 / 4000 MB</div>
            </li>
        </ul>
    </div>

    <div class="col-md-9">
        <h3 class="font-green bold uppercase"><i class="icon-layers font-red"></i> Control Panel</h3>

        <a href="<?php echo base_url('setup/db/resettrans');?>" class="icon-btn icon-btn-lg">
            <i class="fa fa-refresh"></i>
            <div> Reset <br>Transactions </div>
        </a>

        <a href="<?php echo base_url('setup/db/resetsessions');?>" class="icon-btn icon-btn-lg">
            <i class="fa fa-refresh"></i>
            <div> Reset <br>Session </div>
        </a>

        <a href="javascript:;" id="btn_logout" class="icon-btn icon-btn-lg">
            <i class="fa fa-sign-out"></i>
            <div> Sign Out <br> <span class="label label-info"><?php echo get_users_info(user_id())->username; ?></span></div>
        </a>
    </div>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>assets/pages/syssetup.js'></script>
<script type="text/javascript">
    SYSSETUP.init();
    SYSSETUP.db();
</script>