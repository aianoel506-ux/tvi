var FormWizard = function () {


	return {
		//main function to initiate the module
		init: function () {
			if (!jQuery().bootstrapWizard) {
				return;
			}

			
			
			$('#form_wizard_1').find('.button-submit').hide();

			var form = $('#frm_newaccount');
			var error = $('.alert-danger', form);
			var success = $('.alert-success', form);

			form.validate({
				doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
				errorElement: 'span', //default input error message container
				errorClass: 'help-block help-block-error', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {

					//profile
					firstname: {
						required: true
					},
					lastname: {
						required: true
					},
					middlename: {
						required: true
					},
					birthdate: {
						required: true
					},
					email: {
						required: true,
						email: true
					},
					phone: {
						required: true
					},
					mobile: {
						required: true
					},
					gender: {
						required: true
					},
					address: {
						required: true
					},
					city: {
						required: true
					},
					country: {
						required: true
					},
					addrspecific: {
						required: true
					},

					// ACCOUNT
					acctrate: {
						required: true
					},
					accttype: {
						required: true
					},
					conntype: {
						required: true
					},
					loctype: {
						required: true
					},
					acctreq: {
						required: true
					},


				},

				messages: { // custom messages for radio buttons and checkboxes
					//'payment[]': {
					//   required: "Please select at least one option",
					//    minlength: jQuery.validator.format("Please select at least one option")
					//}
				},

				errorPlacement: function (error, element) { // render error placement for each input type
					if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
						error.insertAfter("#form_gender_error");
					} else {
						error.insertAfter(element); // for other inputs, just perform default behavior
					}
				},

				invalidHandler: function (event, validator) { //display error alert on form submit   
					success.hide();
					error.show();
					PECO.scrollTo(error, -200);
					// PECO.initNicescroll();
				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
				},

				unhighlight: function (element) { // revert the change done by hightlight
					$(element)
						.closest('.form-group').removeClass('has-error'); // set error class to the control group
				},

				success: function (label) {

					if (label.attr("for") == "gender") { // for checkboxes and radio buttons, no need to show OK icon
						label
							.closest('.form-group').removeClass('has-error').addClass('has-success');
						label.remove(); // remove error label here
					} else { // display success icon for other inputs
						label
							.addClass('valid') // mark the current input as valid and display OK icon
							.closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
					}



				},

				submitHandler: function (form) {
					var form = $('#frm_newaccount');
					$.ajax({

						url: form.attr('action'),
						type: form.attr('method'),
						data: form.serialize(),
						dataType: 'json',
						beforeSend: function () {
							$('#query-status').html('Loading..');
						}

					}).fail(function (d) {
						PECO.initAlerts(d.msg, 'New Account', 'error');
						console.log(d.msg);
						$('#query-status').html(d.msg);
						//	}).done(function(data){
					}).done(function (d) {
						if (d.qry == true) {
							$('#query-status').html('');
							PECO.initAlerts(d.msg, 'New Account', 'success');
							console.log(d.msg);
							setTimeout(function () {
								$('#acct_rate, #acct_type, #conn_type, #loc_type, #acct_req, #acct_req_add').select2("val", '');
								form.find('.data-entry').val('');
							}, 2000);
						} else {
							PECO.initAlerts(d.msg, 'New Account', 'error');
							$('#query-status').html(d.msg);
							console.log("Data Error: " + d.msg);
						}

					});

					success.show();
					error.hide();
					//add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
				}

			});

			var displayConfirm = function () {
				$('#tab4 .form-control-static', form).each(function () {
					var input = $('[name="' + $(this).attr("data-display") + '"]', form);
					if (input.is(":radio")) {
						input = $('[name="' + $(this).attr("data-display") + '"]:checked', form);
					}
					if (input.is(":text") || input.is("textarea")) {
						$(this).html(input.val());
					} else if (input.is("select")) {
						$(this).html(input.find('option:selected').text());
					} else if (input.is(":radio") && input.is(":checked")) {
						$(this).html(input.attr("data-title"));
					}
				});
			}

			var handleTitle = function (tab, navigation, index) {
				var total = navigation.find('li').length;
				var current = index + 1;
				// set wizard title
				$('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
				// set done steps
				jQuery('li', $('#form_wizard_1')).removeClass("done");
				var li_list = navigation.find('li');
				for (var i = 0; i < index; i++) {
					jQuery(li_list[i]).addClass("done");
				}

				if (current == 1) {
					$('#form_wizard_1').find('.button-previous').hide();
					$('#form_wizard_1').find('.button-submit').hide();
				} else {
					if (current == total) {
						$('#form_wizard_1').find('.button-submit').show();
					}
					$('#form_wizard_1').find('.button-previous').show();
				}

				if (current >= total) {
					$('#form_wizard_1').find('.button-next').hide();
					$('#form_wizard_1').find('.button-submit').show();
					displayConfirm();
				} else {
					$('#form_wizard_1').find('.button-next').show();
					$('#form_wizard_1').find('.button-submit').hide();
				}
				PECO.scrollTo($('.page-title'));
				//PECO.initNicescroll();
			}

			// default form wizard
			$('#form_wizard_1').bootstrapWizard({
				'nextSelector': '.button-next',
				'previousSelector': '.button-previous',
				onTabClick: function (tab, navigation, index, clickedIndex) {
					return false;
					/*
					success.hide();
					error.hide();
					if (form.valid() == false) {
					    return false;
					}
					handleTitle(tab, navigation, clickedIndex);
					*/
				},
				onNext: function (tab, navigation, index) {
					success.hide();
					error.hide();

					if (form.valid() == false) {
						return false;
					}

					handleTitle(tab, navigation, index);
				},
				onPrevious: function (tab, navigation, index) {
					success.hide();
					error.hide();

					handleTitle(tab, navigation, index);
				},
				onTabShow: function (tab, navigation, index) {
					var total = navigation.find('li').length;
					var current = index + 1;
					var $percent = (current / total) * 100;
					$('#form_wizard_1').find('.progress-bar').css({
						width: $percent + '%'
					});
				}
			});
			$('#form_wizard_1').find('.button-previous').hide();
			/*
            $('#form_wizard_1 .button-submit').click(function () {
                alert('Finished! Hope you like it :)');
            }).hide();
			*/

			//apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
			$('#country_list', form).change(function () {
				form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
			});
		}

	};

}();