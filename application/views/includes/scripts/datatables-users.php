<script src="<?php echo base_url().PATH_GLOBAL?>/peco.js" type="text/javascript"></script>
<script src="<?php echo base_url().PATH_GLOBAL?>/tp/js/layout-admin.js" type="text/javascript"></script>
<script src="<?php echo base_url().PATH_PLUGINS?>/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url().PATH_PLUGINS?>/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	console.log('Initializing PECO layout...');
	PECO_layout.init_admin_users();
});
</script>