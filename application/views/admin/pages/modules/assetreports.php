<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/fixedcolumn/css/fixedColumns.bootstrap.css"/>



				<h3 class="page-title">
				<?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
				</h3>
                <div class="row">	
                <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Reports</span>
                            <span class="caption-helper">asset summary</span>
                        </div>
                        <div class="pull-right">
                           <a class="btn btn-default btn-xs fullscreen" href="javascript:;"><i class="fa fa-search fa-fw"></i> Expand</a>
                           <a class="btn btn-warning btn-xs" href="javascript:;"><i class="fa fa-print fa-fw"></i> Print</a>
                           <a class="btn btn-success btn-xs" href="javascript:;"><i class="fa fa-check fa-fw"></i> Active</a>
                           <a class="btn btn-danger btn-xs" href="javascript:;"><i class="fa fa-times fa-fw"></i> In-Active</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                    
                    	<table class="table table-striped table-hover tbl-sm" id="assettable">
                        	<thead>
                            	<tr>
                                	<th><i class="fa fa-reorder"></i></th>
                                	<th>Asset Code</th>
                                	<th>Asset Descriptions</th>
                                	<th>Asset Owner</th>
                                	<th>Asset Location</th>
                                	<th>Asset Status</th>
                                	<th>Asset Type</th>
                                	<th><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    	
                    </div>
                    
                  </div>
                </div>
              </div>
		
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/assets-powerplant/assetreports.js"></script>

<script>
    ASSETREPORT.init();

	$('#data').dataTable({
		"columnDefs": [ 
				{ "targets": -1, "orderable": false, "searchable": false },
		]	
	});
</script>