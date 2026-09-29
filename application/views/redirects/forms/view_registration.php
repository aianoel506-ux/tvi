

<link href="<?php echo base_url(); ?>assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/admin/pages/css/registration.css"/>




<?php
$login_validate = validation_errors();
if(!empty($login_validate)){
    $hass_error = ' has-error';
}else{
    $hass_error = ' ';
}

$mobileview = (isset($mobileview)) ? $mobileview : 0;
?>
<div class="wrapper">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="login-box-bulletin">
            <div class="bulletin-title animated fadeInDown fast">
                <div id="clock"></div>
            </div>
            <div class="bulletin-content " style="">
                <div class="row">
                    <div class="bulletin-details">
                        <div class="featured-news animated fadeInDown fast">
                            <div class="news-title">Featured News


                            </div>
                            <div class="news-content">


                            </div>
                        </div>
                        <div class="flash-news animated fadeInUp fast">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-box-body animated fadeInRight fast">


            <div class="login-logo">
                <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url();?>assets/global/img/logo/peco-logo-login.png" /></a>
            </div>

            <hr>


            <form id="form_register"  class="form-without-legend" action="<?php echo base_url();?>peco/registeremployee" method="post" >
                <h4><i class="fa fa-edit"></i> Registration<span class="query-stats pull-right small"></span></h4>
                <?php echo $login_validate;?>
                <input type="hidden" value="<?php echo $mobileview; ?>" name="mobileview" />

                <div class="form-group form-md-line-input">
                    <div class="input-icon">
                        <input required type="text" class="form-control" name="empid" id="empid" autocomplete="no" placeholder="Enter Employee ID | ex: P10233 | Lastname">
                        <span class="help-block">If you dont know your employee ID please contact HRD.</span>
                        <i class="fa fa-user"></i>
                    </div>
                </div>

                <div class="form-group margin-top-30 form-md-line-input has-feedback <?php echo $hass_error;?> row">
                    <div class="input-icon">
                        <input required type="email" class="form-control input-sm" name="email" placeholder="Email address.. | ex: lucky.faderon@panayelectric.com">
                        <div class="form-control-focus">
                        </div>

                        <span class="help-block">Enter your official employee email address.</span>
                        <i class="fa fa-envelope"></i>
                    </div>
                </div>

                <div class="form-group margin-top-30 form-md-line-input has-feedback <?php echo $hass_error;?> row">
                    <label style="padding-left: 10px;"><b>Captcha Security</b> : Please enter the code from the box.</label>
                    <div class="input-group">
                        <span class="input-group-addon">

                            <img id="img_captcha" src="<?php echo base_url(); ?>peco/gencaptcha" />
                        </span>
                            <input required type="text" class="form-control input-lg" name="captcha" id="input_captcha" placeholder="Enter captcha code..">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default inline" id="btn_captcha_refresh">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </span>
                        <div class="form-control-focus"></div>
                    </div>
                </div>

                <hr class="margin-top-20">

                <div class="form-controller">
                    <div class="form-group form-md-checkboxes ">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="pull-right" style="text-align:right">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-mail-forward"></i> Register</button>
                                    <a href="<?php echo base_url(); ?>" class="btn btn-default"><i class="fa fa-sign-in"></i> Login</a>
                                </span>

                                <div class="col-md-6">
                                    <input required value="1" type="checkbox" name="terms" id="terms">

                                    <label for="terms">

                                        Accept the

                                    </label>

                                    <a href="javascript:;" id="link_term">Terms and Condtions</a>
                                </div>
                            </div>
                        </div>
                    </div>






                    <hr>
                    <div id="qry_stat"></div>

            </form>

            <div class="footer-copy-right margin-top-20" align="center"><p>PECO <i class="fa fa-copyright"></i> 2015, Allrights Reserved.</p></div>

        </div>

        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
</div>


<script src="<?php echo base_url(); ?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
<script src="<?php echo base_url(); ?>assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.min.js" type="text/javascript"></script><!-- slider for products -->
<script src='<?php echo base_url(); ?>assets/global/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->



<script src="<?php echo base_url(); ?>assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/auth.js" type="text/javascript"></script>
<script>
    AUTH.registration();
</script>