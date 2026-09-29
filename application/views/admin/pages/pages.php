<link href="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css"/>

<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">
				<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
				<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
								<h4 class="modal-title">Modal title</h4>
							</div>
							<div class="modal-body">
								 Widget settings form goes here
							</div>
							<div class="modal-footer">
								<button type="button" class="btn blue">Save changes</button>
								<button type="button" class="btn default" data-dismiss="modal">Close</button>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				
				<h3 class="page-title">
				Dashboard <small>dashboard & statistics</small>
				</h3>
				<div class="page-bar">
					<?php echo create_breadcrumb(); ?>
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
							Actions <i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li>
									<a href="#">Action</a>
								</li>
								<li>
									<a href="#">Another action</a>
								</li>
								<li>
									<a href="#">Something else here</a>
								</li>
								<li class="divider">
								</li>
								<li>
									<a href="#">Separated link</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				<div class="row">
               
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 margin-bottom-10">
						<div class="dashboard-stat blue-madison">
							<div class="visual">
								<i class="fa fa-briefcase fa-icon-medium"></i>
							</div>
							<div class="details">
								<div class="number">
									 &#8369; 168,492.54
								</div>
								<div class="desc">
									 Current Paid
								</div>
							</div>
							<a class="more" href="javascript:;">
							View more <i class="m-icon-swapright m-icon-white"></i>
							</a>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="dashboard-stat red-intense">
							<div class="visual">
								<i class="fa fa-shopping-cart"></i>
							</div>
							<div class="details">
								<div class="number">
									 &#8369; 230,127,390
								</div>
								<div class="desc">
									 Accoutn Recievables
								</div>
							</div>
							<a class="more" href="javascript:;">
							View more <i class="m-icon-swapright m-icon-white"></i>
							</a>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="dashboard-stat green-haze">
							<div class="visual">
								<i class="fa fa-group fa-icon-medium"></i>
							</div>
							<div class="details">
								<div class="number">
									 &#8369; 230,295,882.54
								</div>
								<div class="desc">
									 Total Accomulated Sales
								</div>
							</div>
							<a class="more" href="javascript:;">
							View more <i class="m-icon-swapright m-icon-white"></i>
							</a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<!-- Begin: life time stats -->
						<div class="portlet light">
							<div class="portlet-title">
								<div class="caption">
									<i class="icon-bar-chart font-green-sharp"></i>
									<span class="caption-subject font-green-sharp bold uppercase">Overview</span>
									<span class="caption-helper">Current Billing Per Lot & Book</span>
								</div>
								<div class="tools">
									<a href="javascript:;" class="collapse">
									</a>
									<a href="#portlet-config" data-toggle="modal" class="config">
									</a>
									<a href="javascript:;" class="reload">
									</a>
									<a href="javascript:;" class="fullscreen">
									</a>
									<a href="javascript:;" class="remove">
									</a>
								</div>
							</div>
							<div class="portlet-body">
								<div class="tabbable-line">
									<ul class="nav nav-tabs">
										<li class="dropdown">
											<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
											Paid  <i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu" role="menu">
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Governments </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Special</a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Regular</a>
												</li>
											</ul>
										</li>
                                        <li class="dropdown">
											<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
											Overdue  <i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu" role="menu">
                                           		 <li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Governments </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Special</a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Regular</a>
												</li>
											</ul>
										</li>
                                        <li class="dropdown">
											<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
											w/PN  <i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu" role="menu">
                                           		 <li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Governments </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Special</a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Regular</a>
												</li>
											</ul>
										</li>
										<li class="dropdown">
											<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
											w/FDO  <i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu" role="menu">
                                           		 <li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Governments </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Special </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													High-Voltage Regular </a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Special</a>
												</li>
												<li>
													<a href="#overview_4" tabindex="-1" data-toggle="tab">
													Array Regular</a>
												</li>
											</ul>
										</li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="overview_1">
											<div class="table-responsive">
												<table class="table table-striped table-hover table-bordered">
												<thead>
												<tr>
													<th>
														 District / Lot / Book
													</th>
													<th>
														 Amount
													</th>
													<th>
														 Customer
													</th>
													<th>
													</th>
												</tr>
												</thead>
												<tbody>
												<tr>
													<td>
														<a href="javascript:;">
														S 7-1</a>
													</td>
													<td>
														 &#8369; 12,625.50
													</td>
													<td>
														 809
													</td>
													<td>
														<a href="javascript:;" class="btn default btn-xs green-stripe">
														View </a>
													</td>
												</tr>
												<tr>
													<td>
														<a href="javascript:;">
														S 7-2</a>
													</td>
													<td>&#8369; 12,625.50 </td>
													<td> 809 </td>
													<td>
														<a href="javascript:;" class="btn default btn-xs green-stripe">
														View </a>
													</td>
												</tr>
												<tr>
													<td>
														<a href="javascript:;">
														S 7-3</a>
													</td>
													<td>&#8369; 12,625.50 </td>
													<td> 809 </td>
													<td>
														<a href="javascript:;" class="btn default btn-xs green-stripe">
														View </a>
													</td>
												</tr>
												<tr>
													<td>
														<a href="javascript:;">
														S 7-4</a>
													</td>
													<td>&#8369; 12,625.50 </td>
													<td> 809 </td>
													<td>
														<a href="javascript:;" class="btn default btn-xs green-stripe">
														View </a>
													</td>
												</tr>
												<tr>
													<td>
														<a href="javascript:;">
														A 1-1</a>
													</td>
													<td>&#8369; 12,625.50 </td>
													<td> 809 </td>
													<td>
														<a href="javascript:;" class="btn default btn-xs green-stripe">
														View </a>
													</td>
												</tr>
												<tr>
													<td>
														<a href="javascript:;">
														A 1-2</a>
													</td>
													<td>&#8369; 12,625.50 </td>
													<td> 809 </td>
													<td>
														<a href="javascript:;" class="btn default btn-xs green-stripe">
														View </a>
													</td>
												</tr>
												<tr>
													<td>
														<a href="javascript:;">A 1-2</a>
													</td>
													<td>&#8369; 12,625.50 </td>
													<td> 809 </td>
													<td>
														<a href="javascript:;" class="btn default btn-xs green-stripe">
														View </a>
													</td>
												</tr>
												</tbody>
												</table>
											</div>
										</div>
										<div class="tab-pane" id="overview_2">
											<div class="table-responsive">
												<table class="table table-striped table-hover table-bordered">
												<thead>
												  <tr>
												    <th>District / Lot / Book</th>
												    <th> Amount </th>
												    <th> Customer </th>
												    <th></th>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-1</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-2</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-3</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-4</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> A 1-1</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> A 1-2</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;">A 1-2</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												</thead>
												<tbody>
												</tbody>
												</table>
											</div>
										</div>
										<div class="tab-pane" id="overview_3">
											<div class="table-responsive">
												<table class="table table-striped table-hover table-bordered">
												<thead>
												  <tr>
												    <th>District / Lot / Book</th>
												    <th> Amount </th>
												    <th> Customer </th>
												    <th></th>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-1</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-2</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-3</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> S 7-4</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> A 1-1</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;"> A 1-2</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												  <tr>
												    <td><a href="javascript:;">A 1-2</a></td>
												    <td>&#8369; 12,625.50 </td>
												    <td> 809 </td>
												    <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
											      </tr>
												</thead>
												<tbody>
												</tbody>
												</table>
											</div>
										</div>
										<div class="tab-pane" id="overview_4">
											<div class="table-responsive">
											  <table class="table table-striped table-hover table-bordered">
											    <thead>
											      <tr>
											        <th>District / Lot / Book</th>
											        <th> Amount </th>
											        <th> Customer </th>
											        <th></th>
										          </tr>
											      <tr>
											        <td><a href="javascript:;"> S 7-1</a></td>
											        <td>&#8369; 12,625.50 </td>
											        <td> 809 </td>
											        <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
										          </tr>
											      <tr>
											        <td><a href="javascript:;"> S 7-2</a></td>
											        <td>&#8369; 12,625.50 </td>
											        <td> 809 </td>
											        <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
										          </tr>
											      <tr>
											        <td><a href="javascript:;"> S 7-3</a></td>
											        <td>&#8369; 12,625.50 </td>
											        <td> 809 </td>
											        <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
										          </tr>
											      <tr>
											        <td><a href="javascript:;"> S 7-4</a></td>
											        <td>&#8369; 12,625.50 </td>
											        <td> 809 </td>
											        <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
										          </tr>
											      <tr>
											        <td><a href="javascript:;"> A 1-1</a></td>
											        <td>&#8369; 12,625.50 </td>
											        <td> 809 </td>
											        <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
										          </tr>
											      <tr>
											        <td><a href="javascript:;"> A 1-2</a></td>
											        <td>&#8369; 12,625.50 </td>
											        <td> 809 </td>
											        <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
										          </tr>
											      <tr>
											        <td><a href="javascript:;">A 1-2</a></td>
											        <td>&#8369; 12,625.50 </td>
											        <td> 809 </td>
											        <td><a href="javascript:;" class="btn default btn-xs green-stripe"> View </a></td>
										          </tr>
										        </thead>
											    <tbody>
										        </tbody>
										      </table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- End: life time stats -->
					</div>
					<div class="col-md-6">
						<!-- Begin: life time stats -->
						<div class="portlet light">
							<div class="portlet-title tabbable-line">
								<div class="caption">
									<i class="icon-share font-red-sunglo"></i>
									<span class="caption-subject font-red-sunglo bold uppercase">Revenue</span>
									<span class="caption-helper">monthly stats...</span>
								</div>
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#portlet_tab1" data-toggle="tab">
										Sales </a>
									</li>
								</ul>
							</div>
							<div class="portlet-body">
								<div class="tab-content">
									<div class="tab-pane active" id="portlet_tab1">
										<div id="statistics_1" class="chart">
										</div>
									</div>
								</div>
								<div class="well margin-top-10 no-margin no-border">
									<div class="row">
										<div class="col-md-3 col-sm-3 col-xs-6 text-stat">
											<span class="label label-success">
											Revenue Average: </span>
											<h3></h3>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					
					</div>
				</div>
                
                
                <div class="row">
					<div class="col-md-6 col-sm-6">
						<!-- BEGIN PORTLET-->
						<div class="portlet light calendar ">
							<div class="portlet-title ">
								<div class="caption">
									<i class="icon-calendar font-green-sharp"></i>
									<span class="caption-subject font-green-sharp bold uppercase">Calendar</span>
								</div>
							</div>
							<div class="portlet-body">
								<div id="calendar">
								</div>
							</div>
						</div>
						<!-- END PORTLET-->
					</div>
                    <div class="col-md-6 col-sm-6">
						<div class="portlet light ">
							<div class="portlet-title">
								<div class="caption">
									<i class="icon-share font-blue-steel hide"></i>
									<span class="caption-subject font-blue-steel bold uppercase">Recent Activities</span>
								</div>
								<div class="actions">
									<div class="btn-group">
										<a class="btn btn-sm btn-default btn-circle" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
										Filter By <i class="fa fa-angle-down"></i>
										</a>
										<div class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">
											<label><input type="checkbox"/> Finance</label>
											<label><input type="checkbox" checked=""/> Membership</label>
											<label><input type="checkbox"/> Customer Support</label>
											<label><input type="checkbox" checked=""/> HR</label>
											<label><input type="checkbox"/> System</label>
										</div>
									</div>
									<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;">
									</a>
								</div>
							</div>
							<div class="portlet-body">
								<div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
									<ul class="feeds">
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-info">
															<i class="fa fa-check"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 You have 4 pending tasks. <span class="label label-sm label-warning ">
															Take action <i class="fa fa-share"></i>
															</span>
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 Just now
												</div>
											</div>
										</li>
										<li>
											<a href="javascript:;">
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-success">
															<i class="fa fa-bar-chart-o"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 Finance Report for year 2013 has been released.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 20 mins
												</div>
											</div>
											</a>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-danger">
															<i class="fa fa-user"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 You have 5 pending membership that requires a quick review.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 24 mins
												</div>
											</div>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-info">
															<i class="fa fa-shopping-cart"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 New order received with <span class="label label-sm label-success">
															Reference Number: DR23923 </span>
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 30 mins
												</div>
											</div>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-success">
															<i class="fa fa-user"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 You have 5 pending membership that requires a quick review.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 24 mins
												</div>
											</div>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-default">
															<i class="fa fa-bell-o"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 Web server hardware needs to be upgraded. <span class="label label-sm label-default ">
															Overdue </span>
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 2 hours
												</div>
											</div>
										</li>
										<li>
											<a href="javascript:;">
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-default">
															<i class="fa fa-briefcase"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 IPO Report for year 2013 has been released.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 20 mins
												</div>
											</div>
											</a>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-info">
															<i class="fa fa-check"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 You have 4 pending tasks. <span class="label label-sm label-warning ">
															Take action <i class="fa fa-share"></i>
															</span>
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 Just now
												</div>
											</div>
										</li>
										<li>
											<a href="javascript:;">
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-danger">
															<i class="fa fa-bar-chart-o"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 Finance Report for year 2013 has been released.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 20 mins
												</div>
											</div>
											</a>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-default">
															<i class="fa fa-user"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 You have 5 pending membership that requires a quick review.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 24 mins
												</div>
											</div>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-info">
															<i class="fa fa-shopping-cart"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 New order received with <span class="label label-sm label-success">
															Reference Number: DR23923 </span>
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 30 mins
												</div>
											</div>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-success">
															<i class="fa fa-user"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 You have 5 pending membership that requires a quick review.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 24 mins
												</div>
											</div>
										</li>
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-warning">
															<i class="fa fa-bell-o"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 Web server hardware needs to be upgraded. <span class="label label-sm label-default ">
															Overdue </span>
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 2 hours
												</div>
											</div>
										</li>
										<li>
											<a href="javascript:;">
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-info">
															<i class="fa fa-briefcase"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 IPO Report for year 2013 has been released.
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 20 mins
												</div>
											</div>
											</a>
										</li>
									</ul>
								</div>
								<div class="scroller-footer">
									<div class="btn-arrow-link pull-right">
										<a href="javascript:;">See All Records</a>
										<i class="icon-arrow-right"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
				<!-- END PAGE CONTENT-->
			</div>
            
</div>