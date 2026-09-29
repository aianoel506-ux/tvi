$(function(){
	$('#frm_newaccount').submit(function(e){
		e.preventDefault();

		var this_form = $(this);

		//check for k for missing fields
		if (frm_newaccount.firstname.value == "") {
			PECO.initAlerts('Please Enter Your First Name.', 'Alert', 'warning');
			frm_newaccount.firstname.focus();
				return (false);
		}
		if (frm_newaccount.lastname.value == "") {
			PECO.initAlerts('Please Enter Your Last Name.', 'Alert', 'warning');
			frm_newaccount.lastname.focus();
			return (false);
		}
		if (frm_newaccount.middle_initial.value == "") {
			PECO.initAlerts('Please Enter Your Middle Name.', 'Alert', 'warning');

			frm_newaccount.middle_initial.focus();
			return (false);
		}
		if (frm_newaccount.addrcity.value == "" || frm_newaccount.addrcity.value == "City..") {
			PECO.initAlerts('Please Enter City.', 'Alert', 'warning');
			frm_newaccount.addrcity.focus();
			return (false);
		} 
		if (frm_newaccount.addrcountry.value == "" || frm_newaccount.addrcountry.value == "Country..") {
			PECO.initAlerts('Please Enter Country.', 'Alert', 'warning');
			frm_newaccount.addrcountry.focus();
			return (false);
		} 
		if (frm_newaccount.addrdistrict.value == "" || frm_newaccount.addrdistrict.value == "District..") {
			PECO.initAlerts('Please Enter District.', 'Alert', 'warning');
			frm_newaccount.addrdistrict.focus();
			return (false);
		} 
		if (frm_newaccount.addrspecific.value == "") {
			PECO.initAlerts('Please Enter Address.', 'Alert', 'warning');
			frm_newaccount.addrspecific.focus();
			return (false);
		} 
		if (frm_newaccount.acctrate.value == "") {
			PECO.initAlerts('Please Enter Account Rate.', 'Alert', 'warning');
			frm_newaccount.acctrate.focus();
			return (false);
		}
		if (frm_newaccount.accttype.value == "") {
			PECO.initAlerts('Please Enter Account Type.', 'Alert', 'warning');
			frm_newaccount.accttype.focus();
			return (false);
		}


		$.ajax({
		
			url: this_form.attr('action'),
			type: this_form.attr('method'),
			data: this_form.serialize(),
			dataType: 'json',
			beforeSend: function(){
				$('#query-status').html('Loading..');
			}

		}).fail(function(){
			PECO.initAlerts('Error Query', 'New Account', 'error');
			console.log('sala');
			//	}).done(function(data){
		}).done(function(data){
			if(data['qry']==true){
				$('#query-status').html('');
				PECO.initAlerts(data['msg'], 'New Account', 'success');
				console.log(data['msg']);
				setTimeout(function(){
					$('#acct_rate, #acct_type, #conn_type, #loc_type, #acct_req, #acct_req_add').select2("val", '');
					this_form.find('.data-entry').val('');
				}, 2000);
			}else{
				PECO.initAlerts(data['msg'], 'New Account', 'error');
				$('#query-status').html('');
				console.log(data['msg']);
			}
			
		});
	});
	// ################################################
	$('#draft_button').click(function(){
		alert('Under Construction.');
	});
	
	$('#cancel_button').click(function(){
		alert('Under Construction.');
		
	});
	// ######################################################
        
	$("#mask_number").inputmask({
		"mask": "9",
		"repeat": 10,
		"greedy": false
    }); 
	
	$("#podate").inputmask("d/m/y", {
		autoUnmask: true  
	});  
	$("#waranty").inputmask("d/m/y", {
		autoUnmask: true
    });  
	$("#acct_issued").inputmask("d/m/y", {
		autoUnmask: true
    }); 
	
	
	$("#acct_rate").select2({
	
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_rate_class/",
			dataType: 'json',
			quietMillis: 100,
			data: function (term) {
				return {
					term: term
				};
			},
			results: function (data) {
				var myResults = [];
				$.each(data, function (index, item) {
					myResults.push({
						'id': item.id,
						'text': item.text
					});
				});
				return {
					results: myResults
				};
			}
			
		},
		
    }).change(function(){
		// ADD AJAX UPDATE IF APPLICABLE //
		console.log('SPECS: '+$(this).val());
	});
	
	$("#acct_type").select2({
	tags: false,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 5,
	  ajax: {
			url: base_url+"admin/get_types/SAPPS",
			dataType: 'json',
			quietMillis: 100,
			data: function (term) {
				return {
					term: term
				};
			
			},
			results: function (data) {
				var myResults = [];
				$.each(data, function (index, item) {
					myResults.push({
						'id': item.id,
						'text': item.text
					});
				});
				// IF (THIS NOT ALLOW MULTIPLE SELECTION (DISABLED) 
				//if($("#acct_type").val()==""){	
					return {
						results: myResults
					};
				//}
				
			}
			
		},
		
    }).change(function(){
		console.log($(this).val());
		$.this_val = $(this).val();
		
		// REQUIREMENTS VALUE IF STATUS OF CONNECTION VALUE IS CHANGED
		$('#acct_req').attr('readonly', false);
		$('#acct_req').select2("enable", false).select2("val", '');
		// ===========================================================
		// CHECK VALUE OF STATUS OF CONNECTION IF NOT EMPTY TRUE THEN INITIALIZE TYPES
		if( $.this_val!="" ){
			
			$('#conn_type').closest('.form-group').find('.select2-container').removeClass('select2-container-disabled');
			$('#conn_type').attr('readonly', false);
			$('#conn_type').attr('disabled', false);
			
			// INITIALIZE STATUS OF CONNECTION
			$("#conn_type").select2({
				tags: false,
				triggerChange: true,
				allowClear: true,
				maximumSelectionLength: 5,
				  ajax: {
						url: base_url+"admin/get_types/STAPPS",
						dataType: 'json',
						quietMillis: 100,
						data: function (term) {
							return {
								term: term
							};
						
						},
						results: function (data) {
							var myResults = [];
							$.each(data, function (index, item) {
								myResults.push({
									'id': item.id,
									'text': item.text
								});
							});
							// IF (THIS NOT ALLOW MULTIPLE SELECTION (DISABLED) 
							//if($("#acct_type").val()==""){	
								return {
									results: myResults
								};
							//}
							
						}
						
					},
					
				}).change(function(){
					PECO.select2_scroller();
					// ADD AJAX UPDATE IF APPLICABLE //
					$.type_val = $(this).val();
					console.log('TYPE OF CONNECTION: '+$.type_val+' STATUS OF CONNECTION : '+$('#acct_type').val());
					
					if($(this).val()!=""){
					
					$("#loc_type").select2({
						//url: base_url+"admin/sample_select2",
						tags: true,
						triggerChange: true,
						allowClear: true,
						maximumSelectionLength: 3,
						  ajax: {
								url: base_url+"admin/get_types/STLAPPS",
								dataType: 'json',
								quietMillis: 100,
								data: function (term) {
									return {
										term: term
									};
								},
								results: function (data) {
									var myResults = [];
									$.each(data, function (index, item) {
										myResults.push({
											'id': item.id,
											'text': item.text
										});
									});
									return {
										results: myResults
									};
								}
								
							},
							
						}).change(function(){
							// ADD AJAX UPDATE IF APPLICABLE //
							$.location_type = $(this).val();
							console.log('LOC TYPE: '+$.location_type); 
							
							if($.location_type != ""){
							
							$("#acct_req").select2({
										tags: true,
										triggerChange: true,
										allowClear: true,
										maximumSelectionLength: 3,
										  ajax: {
												url: base_url+"admin/get_item_type_requirements/"+$.type_val+"/"+$('#acct_type').val()+"/"+$('#loc_type').val(),
												dataType: 'json',
												quietMillis: 100,
												data: function (term) {
													return {
														term: term
													};
												},
												results: function (data) {
													var myResults = [];
													$.each(data, function (index, item) {
														myResults.push({
															'id': item.id,
															'text': item.text
														});
													});
													return {
														results: myResults
													};
												}
												
											},
											initSelection: function (element, callback) {
												$.ajax({
												url: base_url+"admin/get_item_type_requirements/"+$.type_val+"/"+$('#acct_type').val()+"/"+$('#loc_type').val(),
													dataType: 'json',
												}).done(function(data){
													var selections = [];
													$.each(data, function (index, item) {
														selections.push({
															'id': item.id,
															'text': item.text
														});
													});
													
													if( selections != "" ){
														callback(selections);
													}
													
												});
												
											},
											
										}).select2('val', []).change(function(){
											
											// ADD AJAX UPDATE IF APPLICABLE //
											console.log('TYPE OF CONNECTION: '+$(this).val()+' STATUS OF CONNECTION : '+$('#acct_type').val()+' LOCATION TYPES : '+$('#loc_type').val());
											
										});
										$("#acct_req_add").val("Loading..").fadeTo(1, 500, function(){
											setTimeout(function(){
										
											$.ignore_vals = $('#acct_req').val();
						
											$("#acct_req_add").select2({
											//url: base_url+"admin/sample_select2",
											tags: true,
											triggerChange: true,
											allowClear: true,
											maximumSelectionLength: 3,
											  ajax: {
													url: base_url+"admin/get_item_type_add_requirements/"+$.ignore_vals,
													dataType: 'json',
													quietMillis: 100,
													data: function (term) {
														return {
															term: term
														};
													},
													results: function (data) {
														var myResults = [];
														$.each(data, function (index, item) {
															myResults.push({
																'id': item.id,
																'text': item.text
															});
														});
														return {
															results: myResults
														};
													}
													
												},
												
											}).change(function(){
												// ADD AJAX UPDATE IF APPLICABLE //
												console.log('ADD REQ: '+$(this).val());
											}); 
											PECO.select2_scroller();
											}, 1000);
											// ADDITIONAL REQUIREMENTS INIT SCROLLER
											PECO.select2_scroller();
										});
										
									PECO.select2_scroller();	
							}else{
								$('#acct_req').select2("enable", false).select2("val", '');
							}
							PECO.select2_scroller();
						});
					}else{
						$('#loc_type').select2("enable", true).select2("val", '');
						$('#acct_req').select2("enable", false).select2("val", '');
					}
					// REQUIREMENTS INIT SCROLLER
					PECO.select2_scroller();
					
				});
	
		} else {
			$('#conn_type').closest('.form-group').find('.select2-container').removeClass('select2-container-disabled');
			$('#conn_type').attr('readonly', true);
			$('#conn_type').select2("enable", false).select2("val", '');
			$('#acct_req').select2("enable", false).select2("val", '');
			$('#loc_type').select2("enable", true).select2("val", '');
		}
		// CONNECTION TYPE INIT SCROLLER
		PECO.select2_scroller();
	});
	
   /////////////////
 
   
   
	
	
	
	$("#acct_user").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_user_basic/",
			dataType: 'json',
			quietMillis: 100,
			data: function (term) {
				return {
					term: term
				};
			},
			results: function (data) {
				var myResults = [];
				$.each(data, function (index, item) {
					myResults.push({
						'id': item.id,
						'text': item.text
					});
				});
				return {
					results: myResults
				};
			}
			
		},
		
    }).change(function(){
		// ADD AJAX UPDATE IF APPLICABLE //
		console.log('USER: '+$(this).val());
	});

 
 	$("#acct_type_selection").select2({
	//url: base_url+"admin/sample_select2",
	tags: true,
	triggerChange: true,
    allowClear: true,
	maximumSelectionLength: 3,
	  ajax: {
			url: base_url+"admin/get_types",
			dataType: 'json',
			quietMillis: 100,
			data: function (term) {
				return {
					term: term
				};
			},
			results: function (data) {
				var myResults = [];
				$.each(data, function (index, item) {
					myResults.push({
						'id': item.id,
						'text': item.text
					});
				});
				return {
					results: myResults
				};
			}
			
		},
		initSelection: function (element, callback) {
			$.ajax({
				url: base_url+"admin/get_types",
				dataType: 'json',
			}).done(function(data){
				var selections = [];
				$.each(data, function (index, item) {
					selections.push({
						'id': item.id,
						'text': item.text
					});
				});
				
				callback(selections)
				
			});
			
		},
		
    }).change(function(){
		console.log($(this).val());
		// ADD AJAX UPDATE IF APPLICABLE //
	});
        
    $('#country_select, #city_select, #district_select').select2();
	
	

});