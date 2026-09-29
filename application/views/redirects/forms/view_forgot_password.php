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
        <p><i class="glyphicon glyphicon-envelope"></i> Send reset option</p>
		<?php echo $login_validate;?>
       		
          <div class="form-group has-feedback">
            <input type="email" class="form-control" placeholder="Email"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="row">
          
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Reset</button>
            </div><!-- /.col -->
          </div>
          <hr>
          <div class="row">
          	<div class="col-md-6">
            	<p ><a class="text-warning" href="<?php echo base_url(); ?>auth">Login</a></p>
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


