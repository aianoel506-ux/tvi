var TELLERING = function () {
    // INITIALIZE HIGHLIGHTS SEARCH IN TABLE
    PECO.getHighlightsPlugin();
	PECO.getSelectPlugins();
	PECO.getiCheckPlugin();
	PECO.getNumberFormatPlugin();
	PECO.getSweetAlert();


    // VARIABLES
    var tbl_sp = $(document).find('#tbl_bill_sp');
	var tbl_reg = $(document).find('#tbl_bill_reg');
	var tbl_searchres = $(document).find('#tbl_keyword_res');
	var tbl_acct_rec = $(document).find('#tbl_acct_rec');
	var frm_submit_pay = $('#frm_submit_pay');
	var frm_search_key = $('#frm_search_key');

	var tbl_trn_list = $(document).find('#tbl_trn_list');

	// DISABLE F5 (REFRESH)
	shortcut.add('F5', function(){
		return false;
	});

    shortcut.add('F2', function () {
        $(document).find('#amtcash').focus();
        return false;
    });
    shortcut.add('F3', function () {
        $(document).find('#amtchk').focus();
        return false;
    });
    shortcut.add('F4', function () {
        $('#bulplaycheckbox', document).iCheck('toggle');
        return false;
    });

    shortcut.add('F6', function () {
       // $('#void_window').modal('toggle');
      //  return false;
    });

    shortcut.add('esc', function () {
        // RESET ALL
        show_transactions();
        return false;
    });
	
	var init_acct_rec_tbl = function(form) {
		$.ajax({
            url: PECO.base_url()+'tellering/getacctrec',
            type: 'post',
            dataType: 'json',
            data: form.serialize()
        }).done(function (data) {	
			console.log(data);
			tbl_acct_rec.dataTable().empty();
			tbl_acct_rec.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                aaData: data.months,
                bSort: false,
                aoColumns: [
                    {"data": "month", sWidth: '80px', sClass: 'text-bold'},
                    {"data": "kwh", sWidth: '60px', sClass: 'number'},
                    {"data": "bill", sWidth: '70px', sClass: 'text-primary text-center'},
                    {"data": "current", sWidth: '140px', sClass: 'number'},
                    {"data": "duedate", sWidth: '70px', sClass: 'text-align-center'},
                    {"data": "datepaid", sWidth: '90px', sClass: 'text-align-center'},
                    {"data": "amtpd", sWidth: '140px', sClass: 'number'},
                    {"data": "interest", sWidth: '120px', sClass: 'number'},
                    {"data": "datepaidsur", sWidth: '', sClass: 'number'},
                    {"data": "rem", sWidth: '', sClass: 'text-align-center'},


				],
				fnRowCallback: function(nRow, aData) {
					console.log(aData);
					if(aData.prevyr==true) {
						$(nRow).addClass('text-danger');
					}
				}
			});
		}).fail(function(){
			PECO.phpError();
		});
	};
	var init_acct_rec = function(form) {
		$(document).on('submit', '#frm_acct_rec', function(e){
			e.preventDefault();
			var this_ = $(this);
			init_acct_rec_tbl(form);
		});
	};
	
	var init_func = function () {
        show_transactions();



        var activeTab = null;

        $('#btn_transaction_testprint', document).click(function(e) {
            e.preventDefault();
            $.post(PECO.base_url() + 'tellering/printtest', function(d) {
                PECO.initAlerts(d.msg, 'PECO.net', d.func);
            }, 'json');
        });



        $('#btn_transaction_table', document).click(function(e) {
            e.preventDefault();
            show_transactions();
        });


		 $('#submit_or_void').submit(function(e){
			 var form = $(this);
			 e.preventDefault();
			 $.ajax({
				url: form.attr('action'),
				type: form.attr('method'),
				dataType: 'json',
				data: form.serialize(),
				beforeSend: function() {
					$('#btn_submit_void').find('.fa').removeClass('fa-save')
						.addClass('fa-spinner fa-palse fa-spin');
				}
			 }).done(function (data) {
				PECO.initAlerts('Request submited!', 'Request', 'success');
			 	$('#btn_submit_void').find('.fa')
						.removeClass('fa-spinner fa-palse fa-spin')
						.addClass('fa-save');
			 }).fail(function(){
				PECO.phpError(); 
				 $('#btn_submit_void').find('.fa')
						.removeClass('fa-spinner fa-palse fa-spin')
						.addClass('fa-save');
			 });
		 });


        $('#search_trn').submit(function(e){

            var form = $(this);

            e.preventDefault();
            $.ajax({
                url: PECO.base_url() + 'tellering/search',
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function() {

                }
            }).done(function(d){
                if(d.qry==true) {
                    var d1 = new $.Deferred();

                    $.when(d1).then(function() {});

                    $('#trn-list').load(PECO.base_url()+ "pages/load/" + d.folder +"/"+ d.file,
                        {
                            'servno': d.servno,
                            'mtr': d.mtr,
                            'moduleid': d.moduleid,
                        }, function() {
                            d1.resolve();
                            $('#amtcash').focus().select();
                        }
                    );
                }
            }).fail(function(){
                PECO.phpError();
            });
        });

        $('#pay_type_button').on('click', 'a', function(e){
            var this_ = $(this);
            $('#pay_type_button a').removeClass('active');
            this_.addClass('active');
            $('#search_module').val(this_.attr('data-val'));
            $(document).find('#search_txt').focus();
        });

        $(document).on('click', '#trn_type_filter a', function(e) {
            e.preventDefault();
            var this_type = $(this).attr('data-type');
            init_user_transaction_tbl(false, this_type);
        });

        $(document).on('click', '#btn_transaction_reports', function(e) {
            e.preventDefault();
            $.post(PECO.base_url() + 'tellering/printvalidation', function(d){
                PECO.initAlerts(d.msg, 'PECO.net', d.func);
            }, 'json');
        });


        $(document).on('click', '#btn_clear_trans', function (e) {
            e.preventDefault();
            var user_id = $(this).attr('data-id');

            swal({
                title: "Are you sure?",
                text: 'Clearing transaction in admin level will delete all records, unrecoverable, and must done during development only!',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + "admin/paymentstrans",
                        method: "post",
                        dataType: "json",
                        data: {'userid': user_id}
                    }).done(function (d) {
                        swal("Success!", "Transactions Cleared!", "success");
                        init_user_transaction_tbl();
                    });
                }
            });
        });



        $(document).on('click', '#orvoidbtn', function (e) {
            e.preventDefault();

            $("#void_window", document).draggable({
                handle: ".modal-header"
            });

            var ids = [];
            var count = 0;

            $(document).find('tbody tr input:checked', tbl_trn_list).each(function () {
                var this_val = $(this).val();
                ids.push(this_val);
                count++;
            });


            if (count > 0) {
                $.ajax({
                    url: PECO.base_url() + 'tellering/getpaymentdetails',
                    type: 'post',
                    data: {'ids': ids},
                    dataType: 'json'
                }).done(function(d) {
                    $(document).find('#amtpd').text(d.totalamount);
                    $(document).find('#amtvat').text(d.vattaxamt);
                    $(document).find('#amtfrtx').text(d.franchisetax);
                    $(document).find('#amtnovat').text(d.nonvatt);
                    $(document).find('#ors').val(ids.join(","));
                    $(document).find('#dataid').val(d.dataid);
                    $('#void_window').modal('show');
                    return false;
                });
            }else{
                PECO.initAlerts("Please select item to void.","Void Transations","info",3000);
            }
        });

        $(document).on('submit', '#submit_or_void', function(e){
			e.preventDefault();
			var from_ = $(this);
            $.ajax({
                url: from_.attr('action'),
                type: from_.attr('method'),
                data: from_.serialize(),
                dataType: 'json'
            }).done(function(d) {
				if(d.qry == true){
 					PECO.initAlerts(d.msg , "Void" , d.func , 3000);
                    $(document).find('#void_window').modal('hide');
				}else{
                    PECO.initAlerts(d.msg , "Void" , d.func , 3000);
				}
            }).fail(function(){
            	PECO.phpError();
			});
		});

    };



	var init_queue = function() {
		setInterval(function() {
			var num = $('#search_txt').val();
			if(isNaN(num)==true) {
				num = 0;
			}else{
				if(num=='') {
					num = 0;
				} else {
                    num = num;
                }
			}
			$('#queue_num').text(num);
			$('#queue_last_num').val(Number(num) + 1);
		}, 200);
	};



	var init_user_transaction_tbl = function(userid, paytype) {


        var userid = (userid) ? userid : false;
        var paytype = (paytype) ? paytype : false;
        var tbl_trn_list = $('#tbl_trn_list')

        $.ajax({
            url: PECO.base_url() + 'tellering/trnlist',
            type: 'post',
            data: {'userid': userid, 'paytype': paytype},
            dataType: 'json',
            beforeSend: function() {
                //PECO.DTphpLoading(tbl_trn_list, 'Loading your transactions...');
            }
        }).done(function(d){
            $(document).find('#total_amt').html(d.totalamt);
            $(document).find('#total_chk').html(d.totalamtchk);
            $(document).find('#total_cash').html(d.totalamtcash);
            // tbl_trn_list.dataTable().empty();
            var oTable = tbl_trn_list.DataTable({
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                scrollY: '300px',
                aaData: d.list,
                "order": [[ 1, 'desc' ]],
                aoColumns: [
                    {"data": "expand", sWidth: '10px', sClass: 'text-align-center'},
                    {"data": "trnno", sWidth: '50px'},
                    {"data": "orno", sWidth: '100px', sClass: '' },
                    {"data": "servno", sWidth: '', sClass: 'font-blue text-bold' },
                    {"data": "frtx", sWidth: '', sClass: 'number text-danger' },
                    {"data": "amt", sWidth: '', sClass: 'number font-green text-bold' },
                    {"data": "mode", sWidth: '', sClass: '' },
                    {"data": "payfor", sWidth: '', sClass: '' },
                    {"data": "control", sWidth: '', sClass: 'control' },
                    {"data": "select", sWidth: '20px', sClass: 'checkcontrol text-align-center' },
                ],
                "columnDefs": [
                    {"targets": 0,"orderable": false}
                ],
                fnRowCallback: function(nRow, data) {
                    // RE-INITIALIZE TOOLTIPS
                    $(nRow).find('[data-toggle="popover"]').popover({animate: true, html: true});
                    $(nRow).find('td').addClass(data.rowclass);
                    $(nRow).find('.icheck').iCheck({
                        checkboxClass: 'icheckbox_minimal',
                        radioClass: 'iradio_minimal',
                        increaseArea: '20%' // optional
                    });
                },
                "language": PECO.DTEmptyMessage('No payment received yet!'),
            }).on('ifChecked', '.icheck', function (e) {
                var this_ = $(this);
                this_.attr('checked', true);
            }).on('ifUnchecked', function (e) {
                var this_ = $(this);
                this_.attr('checked', false);
            });
            //PECO.initSlimScroll($('#tbl_trn_list_wrapper .dataTables_scrollBody', oTable));
            PECO.initDTSlimScroll('tbl_trn_list');
        }).fail(function(){
            PECO.phpError();
        });

        tbl_trn_list.on('click', 'tr td, tr input', function(e) {
            var this_ = $(this);
            var input_checkbox = this_.closest("tr").find('.checkcontrol input');

            if(input_checkbox.is(':checked')) {
                input_checkbox.attr('checked', false);
            }else{
                input_checkbox.attr('checked', true);
            }
        });



	};

	var init_user_validation = function() {
        $(document).on('submit', '#frm_teller_validation', function(e){
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: 'Tellers validation',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes, Validate!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (d) {
                        swal.close();
                    }).fail(function(){
                        swal("Error!", " Validation error!", "error");
                    });
                } else {
                    swal("Cancelled", " Validation cancelled", "error");
                }
            });
        });

        $('#frm_teller_validation').on('keyup', 'input.inline', function(e) {
             compute_valiation();
        });
        $('#frm_teller_validation').on('blur', 'input.inline', function(e) {
             compute_valiation();
        });
    };

	var compute_valiation = function() {
        var total_amt = 0;
        var cash_amt = $('#totalcash').val();
        var cash_chk = $('#totalcheck').val();
        var total_amt = (Number(cash_amt) + Number(cash_chk));
        $('#total_amt_validate').val(total_amt).number(true, 2);
    };


    var show_transactions = function() {
        var d1 = new $.Deferred();

        $.when(d1).then(function() {});

        $('#trn-list').load(PECO.base_url() + 'tellering/transactions', function(){
            d1.resolve();
            init_user_transaction_tbl();
            init_user_validation();

            var tbl_trn_list = $('#tbl_trn_list');
            PECO.dtSubDetails(tbl_trn_list, 'tellering/ordetails');

            tbl_trn_list.on('click', '#print_or_trans', function(e) {
               e.preventDefault();
               var orno = $(this).attr('data-or');
               $.post( PECO.base_url() + 'tellering/printordetails', {'orno': orno}, function(d){
                   console.log(d);
               });
            });
		});
    };

    return {
        init: function() {
			init_func();
            init_queue();
		},
        usertrntable: function() {
            show_transactions();
        }

    };
}();


