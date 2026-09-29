<div class="page-sidebar-wrapper">
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse" data-auto-speed="300" data-auto-scroll="true">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu page-sidebar-menu-compact page-sidebar-menu-hover-submenu" data-keep-expanded="TRUE" data-auto-scroll="true" data-slide-speed="200">

            <li class="heading clock">
                <h3 class="uppercase">
                    <span class="title" id="clock">
                          <span class="unit" id="hours">00</span>:
                          <span class="unit" id="minutes">00</span>:
                          <span class="unit" id="seconds">00</span>
                          <span class="unit" id="ampm">XX</span>
                    </span>
                </h3>
            </li>


            <li class="start <?php echo check_nav_sub('shortcut'); ?>">
                <a href="<?php echo base_url('user/shortcut'); ?>">
                <i class="fa fa-mouse-pointer"></i>
                <span class="title"> User's Shortcut</span>
                <span class="selected"></span>
                </a>
            </li>

            <?php


                // GET USER SPECIFIC DASHBOARD
                if(user_id() == 1 || in_array(1, user_info()->roles)) {
                    echo '<li class="start '.check_nav_sub('cad') .'">';
                    echo '<a href="'.base_url().'">';
                    echo '<i class="fa icon-basket"></i>';
                    echo '<span class="title"> Admin Dashboard</span>';
                    echo '<span class="selected"></span>';
                    echo '</a>';
                    echo '</li>';
                }

                $user_access_matrix_id_arr = get_users_roles_matrix_id_arr();
                if(count($user_access_matrix_id_arr) > 0) {
                    if( super_admin() ) {
                        $cnt_dashboard = $this->db->select('COUNT(navids) AS CNT')
                            ->from('prime_system_roles_dashboards')
                            ->get()->row();
                    }else{
                        $cnt_dashboard = $this->db->select('COUNT(navids) AS CNT')
                            ->from('prime_system_roles_dashboards')
                            ->where_in('roleid', $user_access_matrix_id_arr)
                            ->get()->row();
                    }
                    if($cnt_dashboard->CNT > 0) {
                        if($cnt_dashboard->CNT>1) {
                            // SUBBED DASHBOARDS ####################
                            if(super_admin()) {
                                $qry_dashboard = $this->db->select()
                                    ->from('prime_system_roles_dashboards')
                                    ->get();
                            }else {
                                $qry_dashboard = $this->db->select()
                                    ->from('prime_system_roles_dashboards')
                                    ->where_in('roleid', $user_access_matrix_id_arr)
                                    ->get();
                            }
                            if($qry_dashboard->num_rows()>0) {
                                $qry_dashboard_arr = array();
                                foreach($qry_dashboard->result() as $drows) {
                                    $qry_dashboard_arr[] = $drows->navids;
                                }
                                echo '<li class="'.check_nav_uri('cad').'">';
                                echo '<a href="javascript:;">';
                                echo '<i class="fa fa-user fa-fw"></i>';
                                echo '<span class="title"> Users Dashboard</span>';
                                echo '<span class="arrow "></span>';
                                echo '<span class="selected"></span>';
                                echo '</a>';
                                echo '<ul class="sub-menu">';

                                $query_sub_dashboard = $this->db->select()
                                    ->from('prime_module_navigations_main')
                                    ->where(array('parent' => 0))
                                    ->get();

                                if($query_sub_dashboard->num_rows() > 0) {
                                    foreach ($query_sub_dashboard->result() as $dsrows) {
                                        if(check_nav_parent_dashboards($dsrows->sysid) || super_admin()) {
                                            echo '<li class="' . check_nav_sub('projectmon') . ' ">';
                                            echo '<a href="javascript:;">';
                                            echo '<i class="fa ' . $dsrows->icon . ' text-danger"></i>';
                                            echo '<span class="menu-name">' . $dsrows->name . '</span></a>';

                                            echo '<ul  class="sub-menu">';
                                            echo nav_children_dashboards($dsrows->sysid, 2);
                                            echo '</ul>';
                                            echo '</li>';
                                        }
                                    }
                                }
                                echo '</ul>';
                                echo '</li>';
                            }
                        }else{
                            // SINGLE DASHOARDS ######################
                            $qry_dashboard = $this->db->select('navids')
                                ->from('prime_system_roles_dashboards')
                                ->where_in('roleid', $user_access_matrix_id_arr)
                                ->get()->row();
                            if($qry_dashboard) {
                                $qry_single_dashboard = $this->db->select('sysid, parent AS PARENT, htmlclass, icon, name, desc, htmlid, url, hashcode, levels, pagefile, type')
                                    ->from('prime_module_navigations_main')
                                    ->where(array('sysid' => $qry_dashboard->navids))
                                    ->get()->row();
                                if($qry_single_dashboard) {
                                    $htmlclass = $qry_single_dashboard->htmlclass;
                                    $navicon = $qry_single_dashboard->icon;
                                    $navdesc = $qry_single_dashboard->desc;
                                    $navhash = $qry_single_dashboard->hashcode;
                                    $url = 'module/' . $navhash . '/' . $qry_single_dashboard->url;

                                    echo '<li class="' . check_nav_sub('database') . '">';
                                    echo '<a href="'.base_url($url).'">';
                                    echo '<i class="fa '.$navicon.'""></i>';
                                    echo '<span class="title">' . $qry_single_dashboard->name . '</span>';
                                    echo '<span class="selected"></span>';
                                    echo '</a>';
                                    echo '</li>';
                                }else{
                                    echo '<li class="">';
                                    echo '<a href="javascript:;">';
                                    echo '<i class="fa fa-times text-danger"></i>';
                                    echo 'No Access';
                                    echo '</li>';
                                }
                            }
                        }
                    }



            } ?>

            <?php
            $nav_main = $this->db->select('
                nm.sysid,
                nm.code,
                nm.name,
                nm.desc,
                nm.parent,
                nm.levels,
                nm.type,
                nm.sorting,
                nm.htmlclass,
                nm.htmlid,
                nm.url,
                nm.icon,
                nm.hashcode,
                nm.pagefile,
                nm.withpay,
                nm.status
            ')
                ->from('prime_module_navigations_main AS nm')
                ->where(array('nm.status' => 1, 'nm.type' => 1))
                ->group_by('
                    nm.sysid,
                    nm.code,
                    nm.name,
                    nm.desc,
                    nm.parent,
                    nm.levels,
                    nm.type,
                    nm.sorting,
                    nm.htmlclass,
                    nm.htmlid,
                    nm.url,
                    nm.icon,
                    nm.hashcode,
                    nm.pagefile,
                    nm.withpay,
                    nm.status
                ')
                ->get();

            foreach($nav_main->result() as $row){

                if(check_nav_parent($row->sysid)>0){
                    if(nav_sub($row->sysid)>0){
                        $sub_menu_class = $this->model_admin->init_navigation_open_sub($this->uri->segment(2), $row->sysid);
                        $selected = ($sub_menu_class->mode=='open') ? '<span class="selected"></span>' : '';

                        $htmlclass = $row->htmlclass;
                        $navicon = $row->icon;
                        $navdesc = $row->desc;
                        $navhash = $row->hashcode;
                        $url_str = ($row->url!='') ? $row->url : $row->pagefile;
                        $url_str = base_url('module/'.$row->hashcode);

                        echo '<li class="'.$sub_menu_class->class.'" >
								<a href="'.$url_str.'">
									<i class="fa '.$row->icon.'"></i> <span class="title">'.$row->name.'</span> <span class="arrow "></span>
								'.$selected.'
								</a>
								';
                        echo nav_children($row->sysid, 2);
                        echo '<li>';
                    }else{
                        if($row->hashcode=="" || $row->pagefile==""){
                            $htmlclass = 'danger';
                            $navicon = 'fa-warning';
                            $navdesc = 'This page is under maintenance';
                        }else{
                            $htmlclass = $row->htmlclass;
                            $navicon = $row->icon;
                            $navdesc = $row->desc;
                        }

                        $sub_menu_class = $this->model_admin->init_navigation_open_sub($this->uri->segment(2), $row->sysid);
                        $selected = ($sub_menu_class->mode=='open') ? '<span class="selected"></span>' : '';

                        echo '<li class=" tooltips '.$sub_menu_class->class.'" data-container="body" data-placement="right" data-html="true" data-original-title="'.$navdesc.'">
						<a href="'.base_url('module/'.$row->hashcode.'').'">
							<i class="fa '.$navicon.'"></i> <span class="title"> '.$row->name.'</span>
							'.$selected.'
						</a>
						<li>';
                    }

                }
            }



            // ADMIN ACCESS MODULE
            if(super_admin()){


                echo '<li class="'.check_nav_uri('settings').'">
					<a href="javascript:;">
					<i class="fa fa-gear"></i>
					<span class="title">Settings</span>
					<span class="arrow "></span>
					<span class="selected"></span>
					</a>
					<ul class="sub-menu">
                        <li class="'.check_nav_sub('projectmon').' tooltips" data-container="body" data-placement="right" data-html="true" data-original-title="Project Monitoring System">
							<a href="'.base_url('settings/projectmon').'">
							<i class="fa fa-tasks text-danger"></i>
							<span class="menu-name">Project Monitoring</span></a>
						</li>
						<li class="'.check_nav_sub('flow').' tooltips" data-container="body" data-placement="right" data-html="true" data-original-title="Transaction Flow Controls">
							<a href="'.base_url('settings/flow').'">
							<i class="fa fa-exchange text-danger"></i>
							<span class="menu-name">TRN Flow</span></a>
						</li>
						<li >							
							<a href="javascript:;">
                            <i class="fa fa-gear text-danger"></i>
                            <span class="title">Maintenance</span>
                            <span class="arrow "></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="'.check_nav_sub('attributes').'">
                                    <a href="'.base_url('settings/attributes').'">
                                    <i class="fa fa-tag"></i> Attributes Parameters</a>
                                </li>
                                <li class="'.check_nav_sub('types').'">
                                    <a href="'.base_url('settings/types').'">
                                    <i class="fa fa-tag"></i> Types Parameters</a>
                                </li>
                                <li class="'.check_nav_sub('icons').'">
                                    <a href="'.base_url('settings/icons').'">
                                    <i class="fa fa-tag"></i> Icon Parameters</a>
                                </li>
                                <li class="'.check_nav_sub('maintenance').'">
                                    <a href="'.base_url('settings/maintenance').'">
                                    <i class="fa fa-tag"></i> Session Parameters</a>
                                </li>
                                <li class="'.check_nav_sub('roles').'">
                                    <a href="'.base_url('settings/roles').'">
                                    <i class="fa fa-tag"></i> Roles Management</a>
                                </li>
                            </ul>
						</li>
						<li class="'.check_nav_sub('modules').'">
							<a href="'.base_url('settings/modules').'">
							<i class="fa fa-cube text-danger"></i>
							<span class="menu-name">Modules</span></a>
						</li>
						<li class="'.check_nav_sub('access').'">
							<a href="'.base_url('settings/access').'">
							<i class="fa fa-check-circle-o text-danger"></i>
							<span class="menu-name">Access</span><span class="selected"></span></a>
						</li>
						<li class="'.check_nav_sub('database').'">
							<a href="'.base_url('settings/database').'">
							<i class="fa fa-database text-danger"></i>
							<span class="menu-name">Database</span>
							<span class="selected"></span>
							</a>
						</li>					
						<li class="nav-item '.check_nav_sub('migration').'">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-database text-danger"></i>
                                <span class="title">Migration</span>
                                <span class=""></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item '.check_nav_sub('migratefather').'">
                                    <a href="'.base_url('settings/migratefather').'" class="nav-link "><i class="fa fa-database text-warning"></i> Father Update</a>
                                </li>
                                <li class="nav-item '.check_nav_sub('migratebilltrn').'">
                                    <a href="'.base_url('settings/migratebilltrn').'" class="nav-link "><i class="fa fa-database text-warning"></i> Billtrn Update </a>
                                </li>
                                <li class="nav-item '.check_nav_sub('paymentsupdate').'">
                                    <a href="'.base_url('settings/paymentsupdate').'" class="nav-link "><i class="fa fa-database text-warning"></i> Payments Update </a>
                                </li>
                            </ul>
                        </li>
						
						<li class="'.check_nav_sub('tables').'">
							<a href="'.base_url('settings/tables').'">
							<i class="fa fa-table text-danger"></i>
							<span class="menu-name">Tables</span>
							<span class="selected"></span>
							</a>
						</li>
						
						<li class="'.check_nav_sub('database').'">
							<a href="'.base_url('settings/testing').'">
							<i class="fa fa-edit text-danger"></i>
							<span class="menu-name">Testing</span>
							<span class="selected"></span>
							</a>
						</li>
					    <li class="">
							<a href="http://pecodevserver/templates/metronic/metronic_v4.7.5/" target="_blank">
							<i class="fa fa-file-text text-danger"></i>
							<span class="menu-name">Templates</span></a>
						</li>
					</ul>
				</li>';
            }
            ?>

            <li class="<?php echo ($this->uri->segment(1)=='help') ? 'active' : ''; ?>">
                <a href="<?php echo base_url('help'); ?>">
                    <i class="fa icon-info""></i>
                    <span class="title">Help</span>
                    <span class="selected"></span>
                </a>
            </li>



            <!-- 
            <li>
                <a href="javascript:;">
                <i class="fa fa-users"></i>
                <span class="title">Application</span>
                <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="<?php echo base_url(); ?>customer/newentry">
                        <i class="fa fa-file"></i>
                        New Account</a>
                    </li>
                    <li>
                        <a href="ecommerce_orders.html">
                        <i class="fa fa-reorder"></i>
                        APT</a>
                    </li>
                    <li>
                        <a href="ecommerce_orders_view.html">
                        <i class="fa fa-table"></i>
                        Reports</a>
                    </li>
                </ul>
            </li>
            
            <li>
                <a href="javascript:;">
                <i class="fa fa-search"></i>
                <span class="title">Metter Reading</span>
                <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Sub Menu 1</a>
                    </li>
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Sub Menu 2</a>
                    </li>
                </ul>
            </li>
            
            <li>
                <a href="javascript:;">
                <i class="fa fa-folder-open"></i>
                <span class="title">Billing</span>
                <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Sub Menu 1</a>
                    </li>
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Sub Menu 2</a>
                    </li>
                </ul>
            </li>
            
            
            <li>
                <a href="javascript:;">
                <i class="fa fa-shopping-cart"></i>
                <span class="title">Tellering</span>
                <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Sub Menu 1</a>
                    </li>
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Sub Menu 2</a>
                    </li>
                </ul>
            </li>
            
            <li>
                <a href="javascript:;">
                <i class="fa fa-suitcase"></i>
                <span class="title">Property</span>
                <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Assets</a>
                    </li>
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="icon-home"></i>
                        Inventory</a>
                    </li>
                </ul>
            </li>
            
            <li>
                <a href="javascript:;">
                <i class="fa fa-suitcase"></i>
                <span class="title">Procurement</span>
                <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
							<li>
								<a href="javascript:;">
								<i class="icon-settings"></i> Request<span class="arrow"></span>
								</a>
								<ul class="sub-menu">
									<li>
										<a href="#"><i class="icon-docs"></i> PRF</a>
									</li>
									<li>
										<a href="#"><i class="icon-docs"></i> PRS</a>
									</li>
									<li>
										<a href="#"><i class="icon-docs"></i> PO</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="javascript:;">
								<i class="icon-globe"></i> Reports <span class="arrow"></span>
								</a>
								<ul class="sub-menu">
									<li>
										<a href="#"><i class="icon-tag"></i> Sample Link 1</a>
									</li>
									<li>
										<a href="#"><i class="icon-pencil"></i> Sample Link 1</a>
									</li>
									<li>
										<a href="#"><i class="icon-graph"></i> Sample Link 1</a>
									</li>
								</ul>
							</li>
	
						</ul>
            </li>
            
            <li>
                <a href="javascript:;">
                <i class="fa fa-wrench"></i>
                <span class="title">System</span>
                <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="fa fa-gears"></i>
                        Maintenance</a>
                    </li>
                    <li>
                        <a href="ecommerce_index.html">
                        <i class="fa fa-database"></i>
                        Database</a>
                    </li>
                </ul>
            </li>
            -->
            <?php if(user_id() <= 5) { ?>
            <li>
                <a href="javascript:;">
                    <i class="icon-settings"></i>
                    <span class="title">System</span>
                    <span class="arrow "></span>
                </a>
                <ul class="sub-menu">
                    <li class="content"> <span>Monthly Bandwidth Transfer</span>

                        <div class="progress progress-striped progress-mini active">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                <span class="sr-only"> 40% Complete (success) </span>
                            </div>
                        </div>

                        <span class="percent">77%</span>
                        <div class="stat">21419.94 / 14000 MB</div>
                    </li>

                    <li class="content"> <span>Disk Space Usage</span>

                        <div class="progress progress-striped progress-mini active">
                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" style="width: 65%">
                                <span class="sr-only"> 65% Complete (success) </span>
                            </div>
                        </div>

                        <span class="percent">65%</span>
                        <div class="stat">2600 / 4000 MB</div>
                    </li>
                </ul>
            </li>
            <?php } ?>
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
</div>