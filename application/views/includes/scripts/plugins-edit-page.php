<script src="<?php echo base_url().PATH_GLOBAL?>/peco.js" type="text/javascript"></script>
<script src="<?php echo base_url().PATH_GLOBAL?>/tp/js/layout-admin.js" type="text/javascript"></script>
<script src="<?php echo base_url().PATH_PLUGINS?>/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<script src="<?php echo base_url().PATH_PLUGINS?>/iCheck/icheck.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(function () {
	PECO_layout.init_admin_page_edit();
	$("#page-content").wysihtml5();
	$('input[type=radio]').iCheck({
		  checkboxClass: 'icheckbox_flat-red',
			 radioClass: 'iradio_flat-red'
	});
});
</script>
