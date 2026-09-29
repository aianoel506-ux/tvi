
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
                <th>Employee ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Middle Name</th>
                <th>Gender</th>
                <th>Birthday</th>
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
<!-- marlon added script -->
<script src="<?php echo base_url(); ?>assets/pages/hris/view.js"></script>

<script type="text/javascript">
$(function() {

    init_list();
    function init_list(depid) {
    var depid = (depid) ? depid : '';
    $('#emptable').dataTable().empty();
    $('#emptable').dataTable({
        bDestroy: true,
        bPaginate: true, 
        bFilter: true, 
        bInfo: true,
        bStateSave: true,
        //scrollY: '300px',
		scrollY: false,
        bProcessing: true,
        bServerSide: true,
        //"order": [[ 0, "desc" ], [ 1, "asc" ]],
        oLanguage: {
            sProcessing: "Loading table.. <br>"
        },
        ajax: {
            url: '<?php echo base_url("query/emplist/"); ?>',
            type : "POST",
            data : {'depid' : depid, 'modulehash': '<?php echo $this->uri->segment(2); ?>'},
        },
        aoColumns: [
            { "data": "employee_id", sWidth: ''},
            { "data": "firstname", sWidth: ''}, 
            { "data": "lastname", sWidth: ''},
            { "data": "middle_name", sWidth: ''},
            { "data": "gender", sWidth: ''},
            { "data": "birthdate", sWidth: ''},
            { "data": "empstat", sWidth: ''},
            { "data": "controls", sWidth: ''}
        ],
        columnDefs: [ 
            { "targets": -2, "orderable": false, "searchable": false },
            { "targets": -1, "orderable": false, "searchable": false }
         ]
    });
   // PECO.initDTNicescroller();

}
});
</script>