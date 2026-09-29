<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/admin/pages/css/lock.css"/>
<?php
$login_validate = validation_errors();
if(!empty($login_validate)){
    $hass_error = ' has-error';
}else{
    $hass_error = ' ';
    //get_owner_pic($id, $dir);
}

$qry_user_info = $this->db->select()->from('prime_system_users')->where('sysid', user_id())->get()->row();

$username = ($qry_user_info) ? $qry_user_info->username : '';



if($qry_user_info && $qry_user_info->personid!='') {
    $user_pic_url = get_owner_pic($qry_user_info->personid, 'person');
    $qry_person = $this->db->select()->from('person')->where('sysid', $qry_user_info->personid)->get()->row();
    $fullname = $qry_person->lastname . ', '. $qry_person->firstname . ' ' . $qry_person->middlename[0] . '.';
}else {
    $user_pic_url = get_users_pic_url(user_id(), true, true);
    $fullname = $qry_user_info->lastname . ', ' . $qry_user_info->firstname;
}

?>
<div class="login-logo">
    <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url();?>assets/global/img/logo/peco-logo-login.png" /></a>
</div>
<div class="wrapper">

    <div class="outer">

        <div class="middle">
            <div class="inner">


                <div class="page-lock-box ">
                    <div class="login-box-body page-lock animated flipInY fast">

                        <div class="page-body">

                            <img src="<?php echo $user_pic_url; ?>" class="page-lock-img img-circle" />
                            <div class="page-lock-info">
                                <h1><?php echo ucfirst($username); ?></h1>
                                <span class="email"><?php echo $fullname;?></span>
                                <span class="locked">
                                <i class="fa fa-lock"></i> Locked </span>
                                <form id="form_unlock" class="form-inline" action="<?php echo base_url(); ?>auth/unlock" method="post">
                                    <div class="input-group input-group-lg input-icon">
                                        <i class="fa fa-key"></i>
                                        <input name="password" type="password" class="form-control" placeholder="Password">
                                        <span class="input-group-btn"><button type="submit" class="btn blue icn-only"><i class="fa fa-unlock"></i></button></span>
                                    </div>
                                    <div class="relogin">
                                        <a id="btn-logout" data-method="post" title="Logout Account"  href="#<?php echo base_url(); ?>auth/logout">
                                            <i class="fa fa-sign-out fa-fw"></i> Not <?php echo ucfirst($username); ?> ? </a>
                                    </div>
                                </form>
                            </div>
                            <i class="fa fa-lock lock-backgroud"></i>
                        </div>
                    </div>
                    <div class="page-footer-custom">
                        2015 &copy; <?php echo SYSTEM_NAME; ?> | ERP Dashboard V2.
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/auth.js" type="text/javascript"></script>
<script>
    AUTH.lockscreen();
</script>