<?php 
$login_validate = validation_errors(); 
if(!empty($login_validate)){
	$hass_error = ' has-error';	
}else{
	$hass_error = ' ';	
}
?>

    <div class="login-box">
      <div class="login-logo">
        <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url().PATH_GLOBAL?>/tp/img/logo/peco-logo-login.png" /></a>
      </div><!-- /.login-logo -->
      <div class="login-box-body animated flipInY fast">
      	<?php echo form_open(base_url().'verifylogin'); ?>	
        <p><i class="fa fa-edit"></i> Regsiter an account</p>
		<?php echo $login_validate;?>
       		
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Full name"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="email" class="form-control" placeholder="Email"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Retype password"/>
            <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">    
              <div class="checkbox icheck">
                <label>
                  <input type="checkbox"> I agree to the <a href="#">terms</a>
                </label>
              </div>                        
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
            </div><!-- /.col -->
          </div>
          <hr>
          <div class="row">
          	<div class="col-md-6">
            	<p ><a class="text-warning" href="<?php echo base_url(); ?>auth/forgot/">Forgot your password?</a></p>
            </div>
           	<div class="col-md-6">
            	<p class="pull-right"><a href="<?php echo base_url(); ?>auth">Login</a></p>
            </div>
         </div>
        </form>


      </div>
      <div class="footer-copy-right margin-top-20" align="center"><p>PECO <i class="fa fa-copyright"></i> 2015, Allrights Reserved.</p></div>
      <!-- /.login-box-body -->
    </div><!-- /.login-box -->


