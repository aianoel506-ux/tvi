$(function(){
	$('#frm_newaccount').submit(function(e){
		PECO.getSweetAlert();
		e.preventDefault();
		if($('#lastname').val() == ''){
			PECO.initAlerts("Lastname is empty","PECO.net","warning");
		}else if($('#firstname').val() == ''){
            PECO.initAlerts("Firstname is empty","PECO.net","warning");
		}else if($('#middle_initial').val() == ''){
            PECO.initAlerts("Middlename is empty","PECO.net","warning");
        }else if($('#bday').val() == ''){
            PECO.initAlerts("Birthdate is empty","PECO.net","warning");
		}else if($('#zipcode').val() == ''){
            PECO.initAlerts("Zip code is empty","PECO.net","warning");
        }else if($('#addrcity').val() == ''){
            PECO.initAlerts("City is empty","PECO.net","warning");
		}else if($('#nationality').val() == ''){
            PECO.initAlerts("Nationality is empty","PECO.net","warning");
        }else if($('#marital').val() == ''){
            PECO.initAlerts("Marital is empty","PECO.net","warning");
        }else if($('#searchdept').val() == ''){
            PECO.initAlerts("Department is empty","PECO.net","warning");
        }else if($('#searchpos').val() == ''){
            PECO.initAlerts("Position is empty","PECO.net","warning");
        }else if($('#agencyfield').val() == ''){
            PECO.initAlerts("Agency is empty","PECO.net","warning");
        }else if($('#salary').val() == ''){
            PECO.initAlerts("Salary is empty","PECO.net","warning");
        }else if($('#employmentdate').val() == ''){
            PECO.initAlerts("Employment date is empty","PECO.net","warning");
        }else if($('#search_job_cat').val() == ''){
            PECO.initAlerts("Job Category is empty","PECO.net","warning");
        }else if($('#accountno').val() == ''){
            PECO.initAlerts("Account No. is empty","PECO.net","warning");
        }else if($('#costgroup').val() == ''){
            PECO.initAlerts("Cost Group is empty","PECO.net","warning");
        }else if($('#searchpay').val() == ''){
            PECO.initAlerts("PayClass is empty","PECO.net","warning");
        }else{

            var this_form = $(this);
            swal({
                title: "Are you sure?",
                text: "New employee will be added.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url: this_form.attr('action'),
                        type: this_form.attr('method'),
                        data: this_form.serialize(),
                        dataType: 'json',
                        beforeSend: function(){
                            $('#query-status').html('Loading..');
                        }
                    }).fail(function(){
                        PECO.phpError();
                    }).done(function(data){
                        if(data.qry ==true){
                            $('#query-status').html('');
                            swal("Registered!",data.msg, "success");
                            setTimeout(function(){
                                $('#acct_rate, #acct_type, #conn_type, #loc_type, #acct_req, #acct_req_add,#agencyfield,#addrcity,#addrdistrict,#nationality,#marital,#searchdept,#searchpos,#searchpay,#search_job_cat').select2("val", '');
                                $('#employmentdate').val('yyyy-MM-dd')
                                this_form.find('.data-entry').val('');
                            }, 2000);
                        }else{
                            swal("Failed.",data.msg, data.func);
                        }
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
		}
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