<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 4/4/2019
 * Time: 10:09 AM
 */

?>

<div class="row">
    <div class="col-md-6">
        <h4>Status</h4>
        <ul class="list-group summary column">
            <li class="list-group-item">
                <span class="col-md-5 label-name">DB MySQL</span>
                <span class="col-md-7 label-default">Online</span>
            </li>
            <li class="list-group-item">
                <span class="col-md-5 label-name">DB MS SQL</span>
                <span class="col-md-7 label-default">Online</span>
            </li>
        </ul>
    </div>

    <div class="col-md-6">
        <div class="login-content">
            <h1><i class="fa fa-warning text-warning"></i>  Login</h1>
            <p>Please sign-in to begin setup!</p>
            <form action="<?php echo base_url('setup/slogin');?>" id="form_login" class="login-form" method="post">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span>Enter any username and password. </span>
                </div>
                <div class="form-group row">
                    <div class="col-xs-6">
                        <input class="form-control" type="text" autocomplete="off" placeholder="Username" name="username" required="" />
                    </div>
                    <div class="col-xs-6">
                        <input class="form-control" type="password" autocomplete="off" placeholder="Password" name="password" required="" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <!-- <div class="g-recaptcha" data-sitekey="6Le6OpsUAAAAABHm4s74NsH8kNtdw-fVPz2JSgsE"></div> -->

                    </div>
                    <div class="col-sm-8 text-right">
                        <button class="btn green" type="submit">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<hr>
<div style="display:inline-block; height: 50px; margin-top: 50px; background: url('<?php echo base_url('assets/global/img/caution_stripes_repeat.png'); ?>'); width: 100%;">

</div>

<script type="text/javascript" src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>assets/pages/syssetup.js'></script>

<script type="text/javascript">
    SYSSETUP.login();
</script>