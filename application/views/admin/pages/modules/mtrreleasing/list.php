
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">

<style>
.form-md-line-input {
	posiotn: relative !important;	
}
.form-md-line-input .fileinput .input-group-addon{
	background: rgba(177,176,176,0.47) !important;
	z-index: 3000 !important;	
}
.form-md-line-input .fileinput .input-group-addon .btn.red-intense {
	background: rgba(251,124,126,0.77) !important;
}
.form-md-line-input .select2-container{
	margin-bottom: 0px !important;
}
.select2-drop{
	margin-top: -15px !important;
}
.portlet.table {
	padding: 0px 0px !important;	
}

</style>
 

				
    <h3 class="page-title">
    <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
    </h3>
    <div class="row">
		<form role="form" class="form-horizontal asset-entry-form" id="entry-form-ajaxify">	
       
        	<div class="col-md-12">
            <div class="portlet light table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Employee List</span>
                        <span class="caption-helper"></span>
                        
                        <div class="btn-group" style="margin-left: 50px;">
                            <button type="button" class="btn btn-success btn-xs">Active</button>
                            <button type="button" class="btn btn-danger btn-xs">In-Active</button>
                        </div>
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
                <div class="portlet-body ">
				
                  <table class="table table-responsive table-hover table-striped table-condensed table-bordered" id="emptable">
                    <thead>
                        <th>Emp. Code</th>
                        <th><i class="fa fa-venus-mars fa-fw text-info"></i> Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Control</th>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>

                </div>		

            </div>
          </div>
          
          </form>

        </div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				
				
  
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>

<script type="text/javascript">
	$(function() {
		Layout.initEmployeeList('Loading employee list', 'info', false, false, true, true, true, true, '<?php echo $this->uri->segment(2); ?>');
	});
</script>