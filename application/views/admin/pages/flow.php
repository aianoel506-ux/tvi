
<link href="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css"/>



<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">
				
				
				<h3 class="page-title">
				Transaction Flow <small>maintenance</small>
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
                <table class="table table-condensed table-bordered table-hover table-hover tbl-sm" id="trnflowmaintbl">
                    <thead>
                        <th></th>
                        <th>Codes</th>
                        <th>Flow ID</th>
                        <th>Names</th>
                        <th>Descriptions</th>
                        <th>Control</th>
                    </thead>
                    <tbody>
                    </tbody>

                </table>
				
				
                
                
                
				<!-- END PAGE CONTENT-->
			</div>
            
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/angularjs/angular.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/angularjs/angular-sanitize.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/angularjs/angular-touch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/angularjs/plugins/angular-ui-router.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/angularjs/plugins/ocLazyLoad.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/angularjs/plugins/ui-bootstrap-tpls.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/pages/settings/trnflow.js"></script>

<script>
    TRANSACTIONSFLOW.init();
</script>