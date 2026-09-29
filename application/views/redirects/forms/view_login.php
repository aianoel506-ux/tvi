

<link href="<?php echo base_url(); ?>assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/admin/pages/css/login.css"/>




<?php
$login_validate = validation_errors();
if(!empty($login_validate)){
    $hass_error = ' has-error';
}else{
    $hass_error = ' ';
}

$mobileview = (isset($mobileview)) ? $mobileview : 0;
$redirect = $this->input->get('redirect');
?>
<div class="wrapper">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="login-box-bulletin">
            <div class="bulletin-title animated fadeInDown fast">
                <div id="clock"></div>
            </div>
        </div>
        <div class="login-box-body animated fadeInRight fast">


            <div class="login-logo">
                <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url();?>assets/global/img/logo/peco-logo-login.png" /></a>
            </div>

            <hr>

            <form id="form_login"  class="form-without-legend" action="<?php echo base_url();?>auth" method="post" autocomplete="off">
                <h4>Login your account <span class="query-stats pull-right small"></span></h4>
                <?php echo $login_validate;?>

                <input type="hidden" value="<?php echo $mobileview; ?>" name="mobileview" />
                <input type="hidden" value="<?php echo $redirect; ?>" name="redirect" />

                <div class="form-group form-md-line-input has-feedback <?php echo $hass_error;?>">
                    <div class="input-icon left">
                        <input type="text" class="form-control input-sm" name="username" id="loginusername" placeholder="Username" autocomplete="no" autofocus>
                        <div class="form-control-focus">
                        </div>
                        <span class="help-block"></span>
                        <i class="fa fa-envelope-o"></i>
                    </div>
                </div>


                <div class="form-group form-md-line-input has-feedback <?php echo $hass_error;?>">
                    <div class="input-icon left">
                        <input type="password" class="form-control input-sm" name="password" placeholder="Password">
                        <div class="form-control-focus">
                        </div>
                        <span class="help-block"></span>
                        <i class="fa fa-key"></i>
                    </div>
                </div>

                <div class="form-controller">
                    <div class="form-group form-md-checkboxes ">
                        <div class="row">
                            <div class="col-md-12">
                    <span class="pull-right" style="text-align:right">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-sign-in"></i> Login</button>
                    </span>

                                <div class="md-checkbox col-md-6">
                                    <input type="checkbox" id="rem" name="rememberme" class="md-check">
                                    <label for="rem">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                        Remember me</label>
                                </div>
                                <br>
                                <br>
                                <br>
                                <hr>
                                <center>
                                    <p class="small">No account yet?, click Create Account to setup an employee account or click forgot password to reset your password.
                                    </p>
                                    <hr>
                                    <a class="btn btn-info btn-xs" href="peco/registration">Create Account</a>
                                    <a class="btn btn-default btn-xs" href="peco/forgotpassword">Forgot Password</a>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>







                <hr>
            </form>

            <div class="footer-copy-right margin-top-20" align="center"><p>PA Energy <i class="fa fa-copyright"></i> 2020, All-rights Reserved.</p></div>

        </div>

        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.min.js" type="text/javascript"></script><!-- slider for products -->
<script src='<?php echo base_url(); ?>assets/global/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->



<!-- Core PECO JavaScript -->
<script src="<?php echo base_url(); ?>assets/global/scripts/peco.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/auth.js" type="text/javascript"></script>
<script>
    AUTH.loginscreen();
</script>
