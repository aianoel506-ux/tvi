<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <?php
    $page_title = (isset($pagetitle)) ? $pagetitle . ' | ' : '';

    ?>
    <title><?php echo $page_title . SYSTEM_NAME ; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>

    <meta data-toggle="api" data-val="<?php echo google_api_key();?>" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?php echo base_url('assets/global/css/fonts-css.css'); ?>" rel="stylesheet" type="text/css"/>

    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url(); ?>assets/global/img/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo base_url(); ?>assets/global/img/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/global/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url(); ?>assets/global/img/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/global/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo base_url(); ?>manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo base_url(); ?>assets/global/img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">


    <link href="<?php echo base_url(); ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/uniform/css/uniform.default.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/select2/select2.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/select2/select2-bootstrap.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css" rel="stylesheet"/>

    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="" id="head_plugins_marker" />


    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.css"/>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">
    <link href="<?php echo base_url(); ?>assets/global/plugins/animate.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="<?php echo base_url() ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->

    <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>

    <link href="<?php echo base_url(); ?>assets/admin/layout/css/themes/pae-blue.min.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="<?php echo base_url(); ?>assets/global/css/pace-default.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-toastr/toastr.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/fancybox/3.5/jquery.fancybox.min.css">

    <!-- END THEME STYLES -->
    <script> var base_url = "<?php echo base_url(); ?>"; </script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-hoverintent.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.perfectscrollbar/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.nicescroll/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-toastr/toastr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/notification/SmartNotification.js"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-idle-timeout/jquery.idletimeout.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-idle-timeout/jquery.idletimer.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/pace.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-shortcutkeys/shortcutkey.js"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/holder.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/fancybox/3.5/jquery.fancybox.min.js" type="text/javascript"></script>
    <script src='<?php echo base_url(); ?>assets/global/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script>
    <script src='<?php echo base_url(); ?>assets/global/plugins/accounting/accounting.min.js' type="text/javascript"></script>

    <script src="<?php echo base_url(); ?>assets/global/scripts/peco.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/scripts/customer.js" type="text/javascript"></script>
    <?php
    if(SYSTEM_ONLINE == TRUE) {
    ?>
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3&key=<?php echo google_api_key();?>&sensor=false&libraries=places&callback=initMap&libraries=drawing&v=weekly"></script>
    <?php } ?>
    <script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>

    <link href="<?php echo base_url(); ?>assets/global/plugins/jquery.perfectscrollbar/perfect-scrollbar.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>

</head>

<body class="page-sidebar-closed-hide-logo page-content-white page-quick-sidebar-over-content page-header-fixed page-footer-fixed fixed-navigation">
