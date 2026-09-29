<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

<script type="text/javascript">
	var init_dt_scroller = function(){
		$('.dataTables_scrollBody').niceScroll({
			styler:"fb",
			cursorcolor:"rgba(215, 98, 44, 0.6)", 
			cursorwidth: '6', 
			cursorborderradius: '0px', 
			background: 'transparent', 
			cursorborder: ''
		});	
		$("html").niceScroll({
			styler:"fb",
			cursorcolor:"rgba(215, 98, 44, 0.6)", 
			cursorwidth: '8', 
			cursorborderradius: '0px', 
			background: 'transparent', 
			cursorborder: '', 
			zindex: '1000'
		});
	}
	var moduleTable = $('#data').dataTable({
			"destroy": true,
			"processing": true,
			"scrollY": 500,
			"sScrollX": "100%",
        	"bScrollCollapse": true,
			
			"oLanguage": {
				sProcessing: "<img src='<?php echo base_url(); ?>assets/global/img/loading-spinner-blue.gif' />"
			},
		
			"language": {
				"emptyTable":     "My Custom Message On Empty Table"
			},
			
			"columnDefs": [ 
				{ "targets": -1, "orderable": false, "searchable": false },
			 ]
	 });
	
	 init_dt_scroller();
	 
	 $('#data').on('click', 'tbody tr', function(){
		 $('#data tbody tr').removeClass('info');
		$.this_ = $(this);
		$.this_.addClass('info');
	 });
</script>
  