<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<div class="well" style="margin-top: 5%;">
        <h1>Generate Sha</h1>
        	<form id="frm_gen" action="<?php echo base_url('admin/gensha');?>" method="post">
            	<div class="form-group">
                	<input class="form-control" name="str" placeholder="String..">
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" readonly id="sha" value="" placeholder="Sha1">
                 </div>
                <hr>
                <button class="btn btn-primary">Generate</button>
            </form>
        </div>
    </div>
</div>
<script>
	$('#frm_gen').submit(function(e){
		var form = $(this);
		e.preventDefault();
		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			data: form.serialize(),
			dataType:"json",
		}).done(function(d){
			$('#sha').val(d.sha);
		}).fail(function(){
			$('#sha').val('Error!');
		});
	});
	

</script>