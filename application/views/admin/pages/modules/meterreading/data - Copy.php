<?php
	$firstname = $this->model_query->get_owner_info($dataid)->FIRSTNAME;
?>
<!-- 
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/fixedcolumn/css/fixedColumns.bootstrap.css"/>
-->

<style>
	.asset-pic{
		display: inline-block;	
	}
	.asset-pic .main{
		width: 100%;	
	}
	.asset-pic .sub{
		width: 30%;	
		height: 90px;
	}
	.asset-pic .sub.more{
		border: 1px solid #ccc;
	}	
</style>

        <div class="tab-pane fade in <?php ($task_flow==false) ? 'active' : ''; ?>" id="data">
            <div class="row">
             <div class="col-md-8">
             <div class="row">
             	<div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                            <span class="caption-helper">General</span>
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
                                <img src="<?php echo base_url(); ?>uploads/images/qr/2015-08-02-CPU022....png" width="100%" height="100%" />
                            </div>
                            <div class="col-md-9">
                             	<legend>Customer Information:</legend>
                                <h3>Name: <strong><?php echo $this->model_query->get_owner_info($dataid)->FIRSTNAME;?> <?php echo $this->model_query->get_owner_info($dataid)->MIDDLENAME;?> <?php echo $this->model_query->get_owner_info($dataid)->LASTNAME;?>
                                </strong>
                                </h3>
										  <h4>Gender: <strong><?php
										  echo $this->model_query->get_owner_info($dataid)->GENDER;
										  
										  ?></strong> </h4>
										  <!-- for address -->
										    <h4>Address: <strong><?php
										  echo $this->model_query->get_owner_info($dataid)->STREET.', ';
										  echo $this->model_query->get_owner_info($dataid)->DIST.', ';
										  echo $this->model_query->get_owner_info($dataid)->CITY;
										  
										  ?></strong> </h4>
										  <!--for Account Details -->
										  
											<hr>
										  
											<legend>Account Details:</legend>
											<h4>Acccount Rate:<strong>
											<?php
											echo $this->model_query->get_owner_info($dataid)->rate;
											?>	</strong>
											</h4>
											<h4>Status of Connection:<strong>
											<?php
											echo $this->model_query->get_owner_info($dataid)->p_names;
										
											?></strong> 	
											</h4>
										   <h4>Type of Owner:<strong>
											<?php
											echo $this->model_query->get_account_owner_type($dataid)->o_names;
											?>	</strong>
											</h4>
											<h4>Type of Location:<strong>
											<?php
											echo $this->model_query->get_account_owner_location($dataid)->l_names;
											?>	</strong>
											</h4>
										  
										  <!--End for Account Details -->
										  
									
                                <hr>
										  
                                <legend>Additional Details:</legend>
										  <h4>
											Date Created:<strong>
											<?php
											echo $this->model_query->get_owner_info($dataid)->DC;
											?>	</strong>
											</h4>
											<h4>	Created by:<strong>
											<?php
											echo $this->model_query->get_owner_info($dataid)->u_first_name.', ';
											echo $this->model_query->get_owner_info($dataid)->u_last_name;
											?></strong>  
											</h4>
											
                                <hr>
										  <!-- remove marlon asset cost not needed
                                <legend>Asset Cost:</legend>
                                Actual: <strong>P24,800.00</strong> Depreciation: <strong>0.30 (month) </strong> Current: <strong>P23,800.00</strong>
										  -->
                                <hr>
                                <legend>Status:</legend>
                                <span class="label label-success">Deployed</span>  Deployed Date: <strong>2015-07-10</strong>   PO Date: <strong>2015-03-10</strong> 
                                <hr>
										  <!-- gin delete ni marlon kay wala sya kabalo kung para sa anu ni ang mga button
                                <legend>Control:</legend>
                                <button class="btn btn-primary">Request</button> <button class="btn btn-danger">Edit</button>
										  -->
                                <hr>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-12 col-sm-12">
					<div class="portlet light ">
						<div class="portlet-title">
							<div class="caption">
								<i class="icon-share font-blue-steel hide"></i>
								<span class="caption-subject font-blue-steel bold uppercase">Requirements Checklist:</span>
							</div>
							<!-- remove ni marlon kay sa una nga view ni
							<div class="actions">
								<div class="btn-group">
									<a class="btn btn-sm btn-default btn-circle" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
									Filter By <i class="fa fa-angle-down"></i>
									</a>
									<div class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">
										<label><div class="checker"><span class="checked"><input type="checkbox"></span></div> Finance</label>
										<label><div class="checker"><span class="checked"><input type="checkbox" checked=""></span></div> Membership</label>
										<label><div class="checker"><span class="checked"><input type="checkbox"></span></div> Customer Support</label>
										<label><div class="checker"><span class="checked"><input type="checkbox" checked=""></span></div> HR</label>
										<label><div class="checker"><span><input type="checkbox"></span></div> System</label>
									</div>
								</div>
							</div> -->
						</div>
						<div class="portlet-body">
					<table class="table table-hover table-condensed table-stripped" id="req-list">
                    <thead>
                    	<th>#</th>
                    	<th>Requirements</th>
                    	<th>Status</th>
                    </thead>
						 
						<?php
                 		
							
								//$req_array_cnt = $this->db->query('select count(names) from prime_requirement_paramenter as prm inner join trn_customer_accounts_requirements as trn on prm.sysid = trn.reqid where accountid = 1');
                        /*
                    
                        for ($i=0; $i<$req_array_cnt; $i++)
                        {
                            echo "<input type='checkbox' name='chk[]' value='A' /> </input>";
                        }
                        */
						
						$req_query = $this->model_query->get_account_application_requirements($dataid);
                        if($req_query){
								$num = 1;
                            foreach($req_query as $row){
								echo '<tr>';
								echo '<td><a class="btn btn-danger btn-xs" href="javascript:;"><i class="fa fa-times"></i></a></td>';
								echo '<td>'.$row->NAMES.'<em></em></td>';
								echo '<td id="td-stats">'.status_label($row->STATUS, $row->SYSID)->label.'</td>';
								echo '</tr>';
                            }
                        }else{
                            echo '<tr><td colspan="3">No item(s)</td></tr>';
                        }
                        ?>
                    <tfoot>
                    	<tr>
                        	<td></td>
                            <td></td>
                            <td>
                            <?php
							/*
								if(status_label($row->STATUS)->res==true){
									echo '<button type="button" class="btn btn-primary btn-sm tooltips " data-container="body" data-placement="left" data-html="true" data-original-title="Override"><i class="fa fa-pencil"></i></button>';
								}
						    */
							?>
                            </td>
                        </tr>
                    </tfoot>
                    </table>
                    
					
						
			
							
                            <!-- amu man gin dula ni marlon kay sa daan ni. :)
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
									
								</ul>
								
								-->
							
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
                            <span class="caption-subject font-green-sharp bold uppercase">History</span>
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
                    	<table class="table table-stiped table-condensed table-hover">
                        	<thead>
                            	<tr>
                                	<th>Date</th>
                                	<th>Descriptions</th>
                                	<th><i class="fa fa-wrench fa-sm"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            	<tr>
                                	<td>2015-09-01</td>
                                	<td>Re-asign to Lucky</td>
                                	<td><i class="fa fa-search fa-sm"></i></td>
                                </tr>
                            	<tr>
                                	<td>2014-09-01</td>
                                	<td>Deployed to Ken</td>
                                	<td><i class="fa fa-search fa-sm"></i></td>
                                </tr>
                            </tbody>
                        </table>
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
                            <span class="caption-subject font-green-sharp bold uppercase">Image</span>
                            <span class="caption-helper"></span>
                        </div>
                        
                    </div>
                    <div class="portlet-body">
                    	<div class="asset-pic">
                    	<img class="main" src="<?php echo base_url(); ?>uploads/images/assets/20150918_150820.jpg" width="100%"/>
                    	<img class="sub" src="<?php echo base_url(); ?>uploads/images/assets/20150918_150820.jpg" />
                        <img class="sub" src="<?php echo base_url(); ?>uploads/images/assets/20150918_150820.jpg" />
                        <img class="sub more tooltips " data-container="body" data-placement="bottom" data-html="true" data-original-title="View More" src="<?php echo base_url(); ?>assets/global/img/view-more-icon.png" />
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
                            <span class="caption-subject font-green-sharp bold uppercase">File Attachedments</span>
                            <span class="caption-helper">documents upon purchased</span>
                        </div>
                        
                    </div>
                    <div class="portlet-body">
                    	<ul class="feeds">
									<li>
										<div class="col1">
											<div class="cont">
												<div class="cont-col1">
													<div class="label label-sm label-info">
														<i class="fa fa-file-pdf-o"></i>
													</div>
												</div>
												<div class="cont-col2">
													<div class="desc">
                                                    <a href="javascript:;">
														 official-recipt.pdf
                                                    </a>
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
										
										<div class="col1">
											<div class="cont">
												<div class="cont-col1">
													<div class="label label-sm label-success">
														<i class="fa fa-file-pdf-o"></i>
													</div>
												</div>
												<div class="cont-col2">
													<div class="desc">
                                                    <a href="javascript:;">
														 po-reciept.pdf
                                                    </a>
													</div>
												</div>
											</div>
										</div>
										<div class="col2">
											<div class="date">
												 20 mins
											</div>
										</div>
										
									</li>


									<li>
										<div class="col1">
											<div class="cont">
												<div class="cont-col1">
													<div class="label label-sm label-danger">
														<i class="fa fa-file-pdf-o"></i>
													</div>
												</div>
												<div class="cont-col2">
													<div class="desc">
                                                    <a href="javascript:;">
														 prs-reciept.pdf
                                                    </a>
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
									
								</ul>
                    </div>
                  </div>
               </div>
               </div>
               
               </div>
           </div>
        </div>        
    </div>
	<!--  <h4 align="center">Loading content, please wait..<br><br><br><i class="fa fa-circle-o-notch fa-spin fa-2x"></i></h4> -->
    </div>
           
           
                 
        

<!-- 
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
-->
<script>
	$('#req-list').on('click', 'input[type=checkbox]', function(e){
		$.id_ 	= $(this).attr('id');
		
		$.ajax({
			url: base_url+'query/statreq',
			data: {'id': $.id_},
			type: 'post',
			dataType: 'json',
		}).done(function(data){
			if(data['qry']==true){
				PECO.initAlerts(data['msg'], 'Requirements', 'success');
			}else{
				PECO.initAlerts(data['msg'], 'Requirements', 'error');
			}
		}).fail(function(){
			PECO.initAlerts('PHP NOT FOUND', 'Requirements', 'error');
		});
		
	});
</script>