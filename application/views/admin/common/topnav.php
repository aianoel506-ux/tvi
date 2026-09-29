<?php
    $last_url = get_uri_data()->uri_string;
    $last_nid = get_uri_data()->uri_navids;
?>

<style>
    .table.tbl-notify {
		border-top: none !important;
		border-left: none !important;
		border-right: none !important;	
		margin: 0px 0px !important;
	}
	.dropdown-menu .dataTables_wrapper > * {
		border: none !important;	
		margin-top: -1px !important;
	}
	.table.tbl-notify th.external {
		background: rgba(216,214,214,0.40) !important;
	}
</style>

<input type="hidden" id="segs" value="<?php echo $last_url;?>" />
<input type="hidden" id="nids" value="<?php echo $last_nid;?>" />
<input type="hidden" id="user" value="<?php echo user_id();?>" />

<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo sidebar-toggler">
            <a href="<?php echo base_url(); ?>">

            </a>
            <div class="menu-toggler">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE ACTIONS -->
        <!-- DOC: Remove "hide" class to enable the page header actions -->
        <!-- END PAGE ACTIONS -->
        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
            <!-- BEGIN HEADER SEARCH BOX -->
            <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
            <form class="search-form search-form-expanded" is-submit="false" action="<?php echo base_url(); ?>search" method="POST">
                <div class="input-group">
                    <input type="hidden" class="" value="1" name="searchtype"/>
                    <input type="text" class="form-control" placeholder="Search..." name="keyword" required id="global_search" autocomplete="off">
                    <span class="input-group-btn">
                        <a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
                    </span>
                </div>
            </form>
            <input type="hidden" value="0" id="total-notifications-log" />
            <input type="hidden" value="0" id="total-messages-log" />
            <input type="hidden" value="0" id="total-tasks-log" />
            <!-- END HEADER SEARCH BOX -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">


                    <li class="dropdown dropdown-extended">
                        <a id="quick_launch" href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="" data-close-others="true">
                            <i class="icon-paper-plane"></i> Quick Launch
                        </a>
                        <ul class="dropdown-menu" id="quick_launch_item">
                            <li class="external">
                                <h3>Quick Launch Items</h3>
                            </li>

                            <!--
                            <li class="items">
                                <a href="#form_tc_entry" data-toggle="ajax-modal" title="Trouble Call Entry">
                                    <i class="icon-flag"></i> Trouble Calls <code class="quick-button-label">TS</code>
                                </a>
                            </li>
                            <li class="items">
                                <a href="#form_cwdo" data-toggle="ajax-modal" title="Complaints Entry">
                                    <i class="icon-flag"></i> Complaints <code class="quick-button-label">CWD</code>
                                </a>
                            </li>
                            <li class="items">
                                <a href="#form_joborder_entry" data-toggle="ajax-modal" title="Job Orader Entry">
                                    <i class="icon-flag"></i> Job Orders <code class="quick-button-label">JO</code>
                                </a>
                            </li>
                            <li class="items">
                                <a href="#form_apprehension_entry" data-toggle="ajax-modal" title="Apprehensions Entry">
                                    <i class="icon-flag"></i> Apprehensions <code class="quick-button-label">LEGAL</code>
                                </a>
                            </li>

                            <li class="items">
                                <a href="<?php echo base_url('user/tellering'); ?>" target="_blank" >
                                    <i class="fa fa-money"></i> Tellering <code class="quick-button-label">CC</code>
                                </a>
                            </li>
                            <li class="items">
                                <a href="<?php echo base_url('module/9e6a55b6b4563e652a23be9d623ca5055c356940/entry'); ?>" >
                                    <i class="fa fa-edit"></i> Meter Reading <code class="quick-button-label">MRD</code>
                                </a>
                            </li>

                            <li class="items">
                                <a href="<?php echo base_url('module/13682ac418603aa0966369d46bbf282f562acf47/table'); ?>" >
                                    <i class="fa fa-pencil"></i> Evaluation <code class="quick-button-label">CAD</code>
                                </a>
                            </li>
                            <li class="items">
                                <a href="#form_apt_inquiry" data-toggle="ajax-modal" >
                                    <i class="fa fa-question-circle"></i> APT Inquiry <code class="quick-button-label">CAD</code>
                                </a>
                            </li>

                            <li class="items">
                                <a href="#form_tech_support" data-toggle="ajax-modal" title="Tech. Issue Entry">
                                    <i class="icon-question"></i> Tech. Support <code class="quick-button-label">IT</code>
                                </a>
                            </li>
                            -->
                        </ul>
                    </li>

                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after "dropdown-extended" to change the dropdown styte -->
                    <!-- DOC: Apply "dropdown-hoverable" class after below "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                    <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->
                    <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="" data-close-others="true">
                            <i class="icon-bell"></i>
                            <span class="badge badge-default" id="cnt"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3>
                                    <span class="bold" id="msg">12 pending</span> notifications</h3>
                                <a href="<?php echo base_url(); ?>profile#activities">view all</a>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller animated fadeInDown fast" style="height: 250px;" data-handle-color="#637283">

                                </ul>
                            </li>
                        </ul>
                    </li>
                    <!-- END NOTIFICATION DROPDOWN -->
                    <!-- BEGIN INBOX DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="" data-close-others="true">
                            <i class="icon-envelope-open"></i>
                            <span class="badge badge-default animated bounce fast"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3>You have
                                    <span class="bold">7 New</span> Messages</h3>
                                <a href="app_inbox.html">view all</a>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller animated fadeInDown fast" style="height: 275px;" data-handle-color="#637283">

                                </ul>
                            </li>
                        </ul>
                    </li>
                    <!-- END INBOX DROPDOWN -->
                    <!-- BEGIN TODO DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="" data-close-others="true">
                            <i class="icon-calendar"></i>
                            <span class="badge badge-default"></span>
                        </a>
                        <ul class="dropdown-menu extended tasks">
                            <li class="external">
                                <h3>You have
                                    <span class="bold">12 pending</span> tasks</h3>
                                <a href="<?php echo base_url('user/todo'); ?>">view all</a>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller animated fadeInDown fast" style="height: 275px;" data-handle-color="#637283">

                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- END TODO DROPDOWN -->
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-user dropdown-light">

                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="" data-close-others="true">
                            <img alt="" class="img-circle" src="<?php echo get_users_pic_url(); ?>"/>
                            <span class="username username-hide-on-mobile">
                                <?php echo user_info()->firstname . ' ' . user_info()->lastname; ?><br>
                            </span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="<?php echo base_url('profile'); ?>">
                                    <i class="icon-user"></i> My Profile </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('user/mycalendar'); ?>">
                                    <i class="icon-calendar"></i> My Calendar </a>
                            </li>
                            <li>
                                <a href="inbox.html">
                                    <i class="icon-envelope-open"></i> My Inbox <span class="badge badge-danger">
                                        3 </span>
                                </a>
                            </li>
                            <li>
                                <a href="page_todo.html">
                                    <i class="icon-rocket"></i> My Tasks <span class="badge badge-success">
                                        7 </span>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li class="dropdown-submenu">
                                <a  id="btn-lock" data-module="<?php echo $last_nid; ?>" data-segs="<?php echo $last_url; ?>"  data-method="post" title="Lock Account" href="#<?php echo base_url(); ?>auth/lock">
                                    <i class="fa fa-edit"></i> Request </a>
                                <ul class="dropdown-menu" style="">

                                    <li class="dropdown-submenu">
                                        <a href="javascript:;"> HRIS </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="#form_empoyee_leave_request" data-toggle="ajax-modal" title="Leave Entry">
                                                    LEAVE </a>
                                            </li>

                                        </ul>
                                    </li>

                                    <li>
                                        <a href="#">
                                          EPRS </a>
                                    </li>

                                </ul>
                            </li>
                            <li class="divider">
                            </li>
                            <li>
                                <a id="btn-lock" data-module="<?php echo $last_nid; ?>" data-segs="<?php echo $last_url; ?>"  data-method="post" title="Lock Account" href="#<?php echo base_url(); ?>auth/lock">
                                    <i class="fa fa-lock"></i> Lock Screen </a>
                            </li>
                            <li>
                                <a id="btn-logout" data-module="<?php echo $last_nid; ?>" data-segs="<?php echo $last_url; ?>" data-method="post" title="Logout Account" href="#<?php echo base_url(); ?>auth/logout">
                                    <i class="fa fa-sign-out"></i> Log Out </a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown dropdown-quick-sidebar-toggler">
                        <a href="javascript:;" class="dropdown-toggle">
                            <i class="fa fa-angle-left "></i>
                        </a>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>

<div class="clearfix"></div>
<div class="page-container">


<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<script>
    $('#tbl_task').dataTable({
		bPaginate: false,
		bFilter: false,
		bInfo: false,	
	});
</script>
