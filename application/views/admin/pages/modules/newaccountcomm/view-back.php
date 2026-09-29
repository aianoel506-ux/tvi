
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-12">
						<div class="portlet light">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-edit"></i>
									<span class="caption-subject font-green-sharp bold uppercase">Profile</span>
									<span class="caption-helper">person's basic information</span>
								</div>
								<div class="tools">
									<a href="javascript:;" class="collapse" data-original-title="" title="">
                                </a>
								

									<a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                </a>
								

									<a href="javascript:;" class="reload" data-original-title="" title="">
                                </a>
								

									<a href="javascript:;" class="fullscreen" data-original-title="" title="">
                                </a>
								

									<a href="javascript:;" class="remove" data-original-title="" title="">
                                </a>
								

								</div>
							</div>
							<div class="portlet-body">
								<div class="row">
									<div class="col-md-3">
										<?php if($this->model_query->get_owner_info($dataid)->TYPES==1) { ?>
										<img class="img img-bordered" src="<?php echo get_users_pic_url($this->model_query->get_owner_info($dataid)->OWNERSYSID, true); ?>" width="100%" height="100%"/>
										<?php } else { ?>
										<img class="img" src="<?php echo base_url(); ?>uploads/corporation/<?php echo $this->model_query->get_owner_info($dataid)->OWNERSYSID; ?>/primary.jpg" width="100%" height="100%"/>
										<?php } ?>
									</div>
									<div class="col-md-9">

										<ul class="list-group summary column no-border">
											<?php if($this->model_query->get_owner_info($dataid)->TYPES==1) { ?>
											<li class="list-group-item">
												<div class="row">
													<h3><span class=" label-name col-md-3">Name </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->FIRSTNAME; ?> <?php echo $this->model_query->get_owner_info($dataid)->MIDDLENAME; ?> <?php echo $this->model_query->get_owner_info($dataid)->LASTNAME; ?></span></span></div></h3>
											</li>
											<li class="list-group-item">
												<div class="row"><span class=" label-name col-md-3">Contact Number </span><span class="label label-default col-md-9 pull-right"><span id="name">+639284450000</span></span>
												</div>
											</li>
											<li class="list-group-item">
												<div class="row"><span class=" label-name col-md-3">Gender </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo gender($this->model_query->get_owner_info($dataid)->GENDER); ?></span></span>
												</div>
											</li>
											<li class="list-group-item">
												<div class="row"><span class=" label-name col-md-3">Birthday </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->BIRTHDAY; ?></span></span>
												</div>
											</li>
											<li class="list-group-item">
												<div class="row"><span class=" label-name col-md-3">Civil Status </span><span class="label label-default col-md-9 pull-right"><span id="name">Single</span></span>
												</div>
											</li>
											<li class="list-group-item">
												<div class="row"><span class=" label-name col-md-3">Address </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php
                                                        echo $this->model_query->get_owner_info($dataid)->STREET . ', ';
                                                        echo get_district_name($this->model_query->get_owner_info($dataid)->DIST) . ', ';
                                                        echo get_city_name($this->model_query->get_owner_info($dataid)->CITY);
                                                        ?></span></span>
												</div>
											</li>
											<?php  } else { ?>
											<li class="list-group-item">
												<div class="row">
													<h3><span class=" label-name col-md-3">Corp. Name </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->CORPNAME; ?> - <?php echo $this->model_query->get_owner_info($dataid)->CORPDESC; ?></span></span></div></h3>
											</li>
											<li class="list-group-item">
												<div class="row"><span class=" label-name col-md-3">Contact Number </span><span class="label label-default col-md-9 pull-right"><span id="name">+639284450000</span></span>
												</div>
											</li>
											<li class="list-group-item">
												<div class="row"><span class=" label-name col-md-3">Address </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php
                                                        echo $this->model_query->get_owner_info($dataid)->STREET . ', ';
                                                        echo get_district_name($this->model_query->get_owner_info($dataid)->DIST) . ', ';
                                                        echo get_city_name($this->model_query->get_owner_info($dataid)->CITY);
                                                        ?></span></span>
												</div>
											</li>
											<li class="list-group-item">
												<hr style="margin: 3px 0px;">
											</li>
											<li class="list-group-item">
												<div class="row">
													<h4 style="margin: 0px 0px;"><span class=" label-name col-md-3">Representative </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->LASTNAME; ?>, <?php echo $this->model_query->get_owner_info($dataid)->FIRSTNAME; ?></span></span></div></h4>
											</li>
											<?php } ?>
										</ul>
										</div>
										</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12 col-sm-12">
											<div class="portlet light ">
												<div class="portlet-title">
													<div class="caption">
														<i class="fa fa-edit"></i>
														<span class="caption-subject font-green-sharp bold uppercase">Subscription Location</span>
														<span class="caption-helper">mapping and specific geodata</span>
													</div>

												</div>
												<div class="portlet-body">
													<div class="row">
														<div class="col-md-3">
															<h5 class="text-info"><i class="fa fa-map-o fa-fw"></i> <b>Default Map</b></h5><img src="<?php echo base_url(); ?>assets/global/img/samplemap.gif" width="100%" height="200px"/>
															<span style="font-size: 10px">X/Y<code class="pull-right"><?php echo number_format($this->model_query->get_owner_info($dataid)->LAT, 7); ?> / <?php echo number_format($this->model_query->get_owner_info($dataid)->LON, 7); ?></code></span>
														</div>


														<div class="col-md-9">


															<h5 class="text-info"><i class="fa fa-map-marker fa-fw"></i> <b>Location Details</b></h5>
															<ul class="list-group summary column no-border">

																<li class="list-group-item">
																	<div class="row"><span class=" label-name col-md-3">Landmark </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php
                                                        echo $this->model_query->get_owner_info($dataid)->STREET . ', ';
                                                        echo get_district_name($this->model_query->get_owner_info($dataid)->DIST); ?>
                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">House / Gate No </span><span class="label label-default col-md-9 pull-right"><span id="name">223</span></span>
																	</div>
																</li>
																<li class="list-group-item">
																	<div class="row"><span class=" label-name col-md-3">Brgy / Street Name </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->STREET; ?></span></span>
																	</div>
																</li>
																<li class="list-group-item">
																	<div class="row"><span class=" label-name col-md-3">District </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo get_district_name($this->model_query->get_owner_info($dataid)->DIST); ?></span></span>
																	</div>
																</li>
																<li class="list-group-item">
																	<div class="row"><span class=" label-name col-md-3">Lot & Book </span><span class="label label-default col-md-9 pull-right"><span id="name">01-01</span></span>
																	</div>
																</li>
																<li class="list-group-item">
																	<div class="row"><span class=" label-name col-md-3">Map Updated </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->GEODATE; ?></span></span>
																	</div>
																</li>
																<li class="list-group-item">
																	<div class="row"><span class=" label-name col-md-3">Updated By </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo get_users_info($this->model_query->get_owner_info($dataid)->GEOUSER)->lastname.', '.get_users_info($this->model_query->get_owner_info($dataid)->GEOUSER)->firstname ; ?></span></span>
																	</div>
																</li>
															</ul>

														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="portlet light">
												<div class="portlet-title">
													<div class="caption">
														<i class="fa fa-edit"></i>
														<span class="caption-subject font-green-sharp bold uppercase">Validations</span>
														<span class="caption-helper">account application validation</span>
													</div>
													<div class="tools">
														<a href="javascript:;" class="collapse" data-original-title="" title="">
                                        </a>
													

														<a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                        </a>
													

														<a href="javascript:;" class="reload" data-original-title="" title="">
                                        </a>
													

														<a href="javascript:;" class="fullscreen" data-original-title="" title="">
                                        </a>
													

														<a href="javascript:;" class="remove" data-original-title="" title="">
                                        </a>
													

													</div>
												</div>
												<div class="portlet-body">
													<div class="row">
														<div class="col-md-12">
															<a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-group"></i>
                                            <div>
                                                  Legal
                                            </div>
                                            <span class="badge badge-success">
                                                0</span>
                                            </a>
														


															<a href="javascript:;" class="icon-btn">
                                            <i class="fa fa-group"></i>
                                            <div>
                                                  Payments
                                            </div>
                                            <span class="badge badge-danger">
                                                2</span>
                                            </a>
														



														</div>
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-12">
									<div class="portlet light">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-edit"></i>
												<span class="caption-subject font-green-sharp bold uppercase">Account Details</span>
												<span class="caption-helper"></span>
											</div>
											<div class="tools">
												<a href="javascript:;" class="collapse" data-original-title="" title="">
                                </a>
											

												<a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                </a>
											

												<a href="javascript:;" class="reload" data-original-title="" title="">
                                </a>
											

												<a href="javascript:;" class="fullscreen" data-original-title="" title="">
                                </a>
											

												<a href="javascript:;" class="remove" data-original-title="" title="">
                                </a>
											

											</div>
										</div>
										<div class="portlet-body">
											<ul class="list-group summary column no-border">
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Rate </span><span class="label label-default col-md-7 pull-right"><span id="name">Special</span></span>
													</div>
												</li>
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Connection </span><span class="label label-default col-md-7 pull-right"><span id="name">Corporation</span></span>
													</div>
												</li>
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Ownership </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>Individual</span>
													</div>
												</li>
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Land </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>Owned</span>
													</div>
												</li>
											</ul>

										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="portlet light">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-edit"></i>
												<span class="caption-subject font-green-sharp bold uppercase">Assessment</span>
												<span class="caption-helper"></span>
											</div>

										</div>
										<div class="portlet-body">

											<ul class="list-group summary column no-border">
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Load </span><span class="label label-default col-md-7 pull-right"><span id="name">Special</span></span>
													</div>
												</li>
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Lot & Bk </span><span class="label label-default col-md-7 pull-right"><span id="name">01-01</span></span>
													</div>
												</li>
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Job Type </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>ECALES</span>
													</div>
												</li>
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Deposit </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>200,000.00</span>
													</div>
												</li>
												<li class="list-group-item">
													<div class="row"><span class=" label-name col-md-5">Requirements </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>5 / 10</span>
													</div>
												</li>
											</ul>


											<hr>
											<legend>Status:</legend>
											<span class="label label-success"><i class="fa fa-check"></i> Initial Deposit</span>
											<span class="label label-warning">On-going</span>

										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
		
<script>
	$( '#req-list' ).on( 'click', 'input[type=checkbox]', function ( e ) {
		$.id_ = $( this ).attr( 'id' );

		$.ajax( {
			url: base_url + 'query/statreq',
			data: {
				'id': $.id_
			},
			type: 'post',
			dataType: 'json',
		} ).done( function ( data ) {
			if ( data[ 'qry' ] == true ) {
				PECO.initAlerts( data[ 'msg' ], 'Requirements', 'success' );
			} else {
				PECO.initAlerts( data[ 'msg' ], 'Requirements', 'error' );
			}
		} ).fail( function () {
			PECO.initAlerts( 'PHP NOT FOUND', 'Requirements', 'error' );
		} );

	} );
</script>