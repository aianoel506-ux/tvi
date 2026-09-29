
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/fixedcolumn/css/fixedColumns.bootstrap.css"/>

<style>
	table tbody td, table th {
		white-space: nowrap;
		position: relative;
	}
	table tbdoy td.role-name{
		min-width: 	150px;
	}
	table tbody .role-controls{
		padding-top: 10px !important;
		padding-left: 30px !important;
		padding-right: 30px !important;
		min-width: 130px;
	}
	table tbody td.role-controls a{
		margin: 0px 0px !important;	
		margin-left: -20px !important;
		margin-right: 30px !important;
	}
	table tbody .num{
		max-width: 50px !important;
	}
	table tbody td .md-checkbox-inline{
		position: absolute;
		top: -5px;
		left: -10px;	
	}

    .dropdown-submenu .dropdown-menu{
        left:10px;
        top:32px;
    }
</style>

<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">
				
				
				<h3 class="page-title">
				Maintenance <small>system related maintenance</small>
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
								<li class="divider"></li>
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
                	<div class="col-md-12">
                    <div class="portlet light">

							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-wrench"></i> Modules and Access Maintenance
								</div>
								<div class="tools">
									<a href="javascript:;" class="collapse" data-original-title="" title="">
									</a>
									<a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
									</a>
									<a href="javascript:;" class="reload" data-original-title="" title="">
									</a>
									<a href="javascript:;" class="remove" data-original-title="" title="">
									</a>
								</div>
							</div>

							<div class="portlet-body">
                                <button class="btn blue"><i class="fa fa-plus"></i> Add Module </button>
                                <div class="btn-group pull-right" id="btn_levels">
                                    <button data-id="1" class="btn btn-default active"><i class="fa fa-navicon fa-fw"></i> 1st Level </button>
                                    <button data-id="2" class="btn btn-default"><i class="fa fa-navicon fa-fw"></i> 2nd Level </button>
                                    <button data-id="3" class="btn btn-default"><i class="fa fa-navicon fa-fw"></i> 3rd Level </button>
                                </div>
                                <hr>
                                <table id="tbl_module_main" class="table table-hover table-stripped">
                                    <thead>
                                        <tr>
                                            <th class="num"><i class="fa fa-reorder"></i></th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Descriptions</th>
                                            <th>Icon</th>
                                            <th>Status</th>
                                            <th>Control</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                         </div>
                    </div>
                </div>               
                
				<!-- END PAGE CONTENT-->
			</div>
            
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>


<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/modules.js" ></script>
<script type="text/javascript">
    MODULES.init();
</script>