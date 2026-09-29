<script src="<?php echo base_url().PATH_PLUGINS?>/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url().PATH_PLUGINS?>/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<script type="text/javascript">
 $(function () {
	$("#basic").dataTable();
	$('#datatable').dataTable({
	  "bPaginate": true,
	  "bLengthChange": false,
	  "bFilter": true,
	  "bSort": true,
	  "bInfo": true,
	  "bAutoWidth": false
	});
  });
</script>