<?php 
$login_validate = validation_errors(); 
if(!empty($login_validate)){
	$hass_error = ' has-error';	
}else{
	$hass_error = ' ';	
}
?>

<style>
    .login-logo {
        text-align: center;
        padding-top: 30px;
    }
    .login-box .login-box-body
    {
        background: #fff;
        padding: 20px 20px;
        margin: 8% auto;
        width: 30%;
        box-shadow: rgba(0,0,0,0.5) 0px 30px 80px;
        -moz-box-shadow: rgba(0,0,0,0.5) 0px 30px 80px;
        -webkit-box-shadow: rgba(0,0,0,0.5) 0px 30px 80px;
    }

    @media (min-width: 1024px) {

        .wrapper{
            background-size: 100% 100% !important;
            position: fixed !important;
            top: 0px !important;
            bottom: 0px !important;
            width: 100%;
            background: transparent !important;
        }


    }

    @media (max-width: 1024px) {
        .login-box-bulletin {
            display: none;
        }

        .login-box-body {
            width: 50% !important;
        }


        .wrapper{
            background: #fff !important;

            position: fixed !important;
            width: 100% !important;
            top: 0px !important;
            bottom: 0px !important;
        }

        .login-box-body {
            width: 80% !important;
            padding-left: 50px !important;
            padding-right: 50px !important;
            margin-right: 10% !important;
            margin-left: 10% !important;
            box-shadow: none !important;
            -moz-box-shadow: none !important;
            -webkit-box-shadow: rnone !important;
        }

        .footer-copy-right {
            width: 100%;
            position: absolute;
            left: 0px;
            right: 0px;
            bottom: 0px;
        }
    }

    @media (max-width: 720px) {
        .login-box-body {
            width: 100% !important;
            padding-left: 50px !important;
            padding-right: 50px !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
    }


</style>
<div class="wrapper">
    <div class="login-box">
      <div class="login-logo">
        <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url();?>assets/global/img/logo/peco-logo-login.png" /></a>
      </div><!-- /.login-logo -->
      <div class="login-box-body animated flipInY fast">
	  <form role="form" class="form-without-legend" action="<?php echo base_url();?>auth" method="post" id="form-login" >
        <h4>Login your account <span class="query-stats pull-right small"></span></h4>
		<?php echo $login_validate;?>
       		
        <div class="form-group form-md-line-input has-feedback <?php echo $hass_error;?>">
            <div class="input-icon">
                <input type="text" class="form-control input-sm" name="username" placeholder="Username" autocomplete="off" autofocus>
                <div class="form-control-focus">
                </div>
                <span class="help-block"></span>
                <i class="fa fa-envelope-o"></i>
            </div>
        </div>
        
        
        <div class="form-group form-md-line-input has-feedback <?php echo $hass_error;?>">
            <div class="input-icon">
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
                	<button class="btn btn-primary" type="submit">Login</button>
                </span>
              
                <div class="md-checkbox col-md-6">
                    <input type="checkbox" id="rem" name="rememberme" class="md-check">
                    <label for="rem">
                    <span class="inc"></span>
                    <span class="check"></span>
                    <span class="box"></span>
                    Remember me</label>
                </div>
            </div>
        </div>
        </div>
        </div>
        
        
        

        
        
        
          <hr>
          <div class="row">
          	<div class="col-md-6">
            	<p ><a class="text-warning" href="<?php echo base_url(); ?>auth/forgot/">Forgot your password?</a></p>
            </div>
           	<div class="col-md-6">
            	<p class="pull-right"><a href="<?php echo base_url(); ?>auth/register/">Register</a></p>
            </div>
         </div>
        </form>


      </div>
      <div class="footer-copy-right margin-top-20" align="center"><p>PECO <i class="fa fa-copyright"></i> 2015, Allrights Reserved.</p></div>
      <!-- /.login-box-body -->
    </div><!-- /.login-box -->
</div>