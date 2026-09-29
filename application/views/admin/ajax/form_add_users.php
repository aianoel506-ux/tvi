<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 2/6/2019
 * Time: 9:58 AM
 */

?>

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption"><i class="fa fa-user fa-fw"></i>Users Maintenance
            <span class="caption-helper">
                                            Create New User | <?php echo date('F d, Y'); ?>
             </span>
        </div>
    </div>
    <div class="portlet-body">

        <form id="register-form" class="" action="<?php echo base_url();?>settings/adduser" method="post" novalidate style="display: block;" autocomplete="off">
            <div class="row">
                <div class="col-md-5">
                    <p class ="hint">Enter your personal details below:</p>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input required id="lastname" class="form-control placeholder-no-fix" type="text" placeholder="Last Name" name="lastname">
                    </div>

                    <div class="form-group">
                        <label>First Name</label>
                        <input required id="firstname" class="form-control placeholder-no-fix" type="text" placeholder="First Name" name="firstname">
                    </div>

                    <div class="form-group">
                        <label>Middle Name</label>
                        <input required id="middlename" class="form-control placeholder-no-fix" type="text" placeholder="First Name" name="middlename">
                    </div>

                </div>

                <div class="col-md-5">
                    <p class="hint"> Enter your account details below: </p>
                    <div class="form-group">
                        <label>Username</label>
                        <input required id ="username"  class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input autocomplete="off" required class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password" placeholder="Password" name="password"/>
                    </div>
                    <div class="form-group">
                        <label>Re-type Your Password</label>
                        <input autocomplete="off" id="rpassword" class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Re-type Your Password" name="rpassword"/>
                    </div>
                    <div class="form-group form-md-line-input" style="margin-top: -20px">
                        <label>Roles: </label>
                        <input multiple id="selectroles" name="selectroles" type="text" class="form-control" placeholder=" Roles">
                    </div>
                </div>

            </div>


            <div class="modal-footer">
                <button type="submit" class="btn blue">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/access.js" ></script>
<script>
    PECO.handlerEmployeeSearchBasic();
    ACCESS.init();
</script>


