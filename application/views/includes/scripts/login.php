
<script src="<?php echo base_url(); ?>assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$('#form-login').submit(function(e){
		$.this_form = $(this);
		e.preventDefault();
		$.ajax({
			url: $.this_form.attr('action'),
			type: $.this_form.attr('method'),
			data: $.this_form.serialize(),
			dataType: "json",
			beforeSend: function(){
				PECO.start_pageLogin_loading({animate: true, message: '<span class="text-info animated fadeInDown fast"><i class="fa fa-circle-o-notch fa-spin"></i> Authenticating....</span>', messageSize: '18px'});	
			}
		}).done(function(data){
		    if(data) {
                if (data.num > 0) {
                    $('.query-stats').html(data['message']);
                    PECO.stop_pageLogin_loading();
                    PECO.start_pageLogin_loading({
                        animate: true,
                        message: '<span class="text-success animated fadeInUp fast">Hello, ' + data['username'] + '!</span>',
                        messageSize: '35px'
                    });
                    setTimeout(function () {
                        window.location = base_url;
                    }, 1500);
                } else {
                    $('.query-stats').html(data['message']);
                    PECO.stop_pageLogin_loading();
                    $('.login-box-body').find('.form-group').addClass('has-error');
                    $('.login-box-body').removeClass('flipInY').addClass('shake').fadeTo(1000, 1, function () {
                        $(this).removeClass('shake');
                    });
                }
            }else{
                //$('.query-stats').html(data.message);
                PECO.stop_pageLogin_loading();
            }
		}).fail(function(){
			PECO.stop_pageLogin_loading();
			console.log('Unable to find the PHP file');
		});
	});
	$.backstretch([
		"<?php echo base_url(); ?>assets/global/img/bg/2.jpg",
		"<?php echo base_url(); ?>assets/global/img/bg/3.jpg",
		"<?php echo base_url(); ?>assets/global/img/bg/4.jpg",
		"<?php echo base_url(); ?>assets/global/img/bg/5.jpg",
		], {
		  fade: 1000,
		  duration: 8000
	});
</script>
  