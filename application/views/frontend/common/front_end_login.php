<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/29/2018
 * Time: 2:54 PM
 */

?>


<link href="<?php echo base_url(); ?>assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/frontend/pages/css/login.css"/>


<div class="front-end-login">

    <form id="form_login" class="form-without-legend" action="<?php echo base_url(); ?>auth" method="post">
        <h4 class="login-header">

            <span class="pull-right">Agent Login</span>
        </h4>
        <div class="login-content">
            <div class="form-group form-md-line-input has-feedback ">
                <div class="input-icon">
                    <input class="form-control input-sm" name="username" placeholder="Username" autocomplete="off" autofocus="" type="text">
                    <div class="form-control-focus">
                    </div>
                    <span class="help-block"></span>
                    <i class="fa fa-envelope-o"></i>
                </div>
            </div>


            <div class="form-group form-md-line-input has-feedback  ">
                <div class="input-icon">
                    <input class="form-control input-sm" name="password" placeholder="Password" type="password">
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
                	<button class="btn btn-primary" type="submit">Login</button>
                </span>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

<!-- Core PECO JavaScript -->
<script src="<?php echo base_url(); ?>assets/global/scripts/peco.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/auth.js" type="text/javascript"></script>
<script>
    AUTH.loginscreen();
</script>
