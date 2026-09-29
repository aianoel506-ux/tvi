var BILLING = function () {
    // INITIALIZE HIGHLIGHTS SEARCH IN TABLE
	PECO.getHighlightsPlugin();
	PECO.getSelect2Plugins();
	PECO.getSweetAlert();
	PECO.getNumberFormatPlugin();
	
    var tbl_billing_trn = $('#tbl_billing_entry');
	var tbl_rate = $('#tbl_bill_rate');

	var init_billing_process = function() {
		PECO.DTDefault(tbl_billing_trn, 'Select GDLB First..');

        PECO.select2Basic($('#schedid'), 'mrd/getgdlbsched', false, false, false, false, true);

        $('.datepicker').datepicker({
            // rtl: PECO.isRTL(),
            orientation: "left",
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

        $('#btn_bill_generate', document).click(function(e){
            e.preventDefault();
            var this_ = $(this);
            var schedid = $('#schedid').select2('val');
            var bill_no = $('#billno_start', document).val();
            var input_duedate = $('#input_duedate', document).val();
            if (confirm("Generate Bill?") == true) {
                $.ajax({
                    url: PECO.base_url() + 'billing/processbilling',
                    type: 'post',
                    data: {'showall': 4, 'billing': true, 'schedid': schedid, 'billno': bill_no, 'duedate': input_duedate},
                    dataType: 'json',
                    beforeSend: function() {
                        this_.find('.fa').removeClass('fa-tags').addClass('fa-spinner fa-spin fa-pulse');
                    }
                }).done(function(d){
                    this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-tags');
                    PECO.initAlerts(d.msg, 'Billing Process', d.func, false);
                }).fail(function(){
                    this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-tags');
                    PECO.phpError();
                });
            } else {
                return false;
            }
        });

        $('#btn_bill_print').click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            var schedid = $('#schedid').select2('val');
            var cust_num = $('#total_customer').val();

            $.ajax({
                url: PECO.base_url() + 'billing/printbilling',
                type: 'post',
                data: {'schedid': schedid, 'top': cust_num},
                dataType: 'json',
                beforeSend: function () {
                    this_.find('.fa').removeClass('fa-print').addClass('fa-spinner fa-spin fa-pulse');
                }
            }).done(function (d) {
                if (d.qry == true) {
                    PECO.pecoBill('BILLING', d.html);
                } else {
                    PECO.initAlerts(d.msg, 'Billing', 'info');
                }
                this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-print');
            }).fail(function () {
                PECO.phpError();
                this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-print');
            });


        });


        // ----------------------------------------------------------------
        // | BILLING
        $(document).on('click', '#get_billing_list', function(e){
            e.preventDefault();
            var schedid = $('#schedid').select2('val');
            init_analysis_table(schedid);
        });

        $(document).on('click', '#print_gdlb_billing_register', function(e){
            e.preventDefault();
            var schedid = $('#schedid').select2('val');

            var this_ = $(this);
            var this_html = this_.html();

            $.ajax({
                url: PECO.base_url() + 'mrd/readinganalysis',
                type: 'post',
                data: {'schedid': schedid, 'showall': 2, 'ct': false, 'register': true},
                dataType: 'json',
                beforeSend: function () {
                    PECO.btnLoading(this_, ' ');
                },
            }).done(function (data) {
                if (data.qry == true) {
                    PECO.btnSuccess(this_, '', this_html, 'btn-default');

                    var html = '';
                    html += data.registerheader;
                    html+= '<div class="row">';
                    html+= '<div class="col-md-12 cold-sm-12 col-xs-12 col-lg-12">';

                    html+= '<table class="table table-condensed tbl-xs print-table-standard">';
                    html+= '<thead>';
                    html+= '<th>#</th>';
                    html+= '<th>SERVNO</th>';
                    html+= '<th>NAME</th>';
                    html+= '<th>MTR</th>';
                    html+= '<th>MULT</th>';
                    html+= '<th class="text-align-center">RATECLASS</th>';
                    html+= '<th class="text-align-left">KWH</th>';
                    html+= '<th class="text-align-left">DEMAND</th>';
                    html+= '<th class="text-align-left">NETMTR</th>';
                    html+= '<th class="text-align-left">AMT ERP</th>';
                    html+= '<th class="text-align-left">AMT PECO</th>';
                    html+= '<th class="text-align-left">DIFF</th>';
                    html+= '</thead>';
                    html+= '<tbody>';
                    if(data.list.length > 0) {

                        for(index=0;index<data.list.length;index++) {
                            html += '<tr>';
                            html += '<td>' + data.list[index].num + '</td>';
                            html += '<td>' + data.list[index].serviceno + '</td>';
                            html += '<td>' + data.list[index].name + '</td>';
                            html += '<td>' + data.list[index].meter + '</td>';
                            html += '<td>' + data.list[index].mult + '</td>';
                            html += '<td class="text-align-center">' + data.list[index].rem + '</td>';
                            html += '<td class="number">' + data.list[index].currcon + '</td>';
                            html += '<td class="number">' + txt_content(data.list[index].curdem) + '</td>';
                            html += '<td class="number">' + txt_content(data.list[index].netmet) + '</td>';
                            html += '<td class="number">' + data.list[index].current + '</td>';
                            html += '<td class="number">' + data.list[index].legacycur + '</td>';
                            html += '<td class="number">' + data.list[index].diff + '</td>';
                            html += '</tr>';
                        }

                    }

                    html += '<tr>';
                    html += '<td></td>';
                    html += '<td colspan="7" class=" bold">Total</td>';
                    html += '<td class="number "></td>';
                    html += '<td class="number bold">' + data.totalamt + '</td>';
                    html += '<td class="number bold">' + data.totalamtlegacy + '</td>';
                    html += '<td class="number bold">' + data.totalamtdiff + '</td>';
                    html += '</tr>';
                    html+= '</tbody>';
                    html+= '</table>';
                    PECO.pecoRepPrint('Billing Register', html, false);
                } else {
                    PECO.btnErrorPHP(this_, this_html, 'btn-default');
                }
            }).fail(function() {
                PECO.btnErrorPHP(this_, this_html, 'btn-default');
            });
        });

		$('#frm_filter_billing').submit(function(e){
			e.preventDefault();
			var form = $(this);
            init_billing_table(form);
		});

        // PECO.dtSubDetails(tbl_billing_trn, 'billing/getbillingdetails')



        tbl_billing_trn.on('click', '#btn_expand_all', function(e) {
            e.preventDefault();
            var this_ = $(this);
            tbl_billing_trn.find('tr').each(function(){
               $(this).find('#btn-expand').trigger('click');
            });
        });

        tbl_billing_trn.on('click', '#btn_actual_read', function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var acctid = this_row.find('#acctid').val();
            var schedid = this_row.find('#schedid').val();
            var readid = this_row.find('#readid').val();
            var data_stat = this_.attr('data-stat');
            $.ajax({
                url: PECO.base_url() + 'mrd/submitactualreadingrow',
                type: 'post',
                data: {'readid': readid, 'type': data_stat, 'remarks': 'ACTUAL'},
                dataType: 'json',
                beforeSend: function() {
                    this_.removeClass('btn-primary').find('.fa').removeClass('fa-check fa-save').addClass('fa-circle-o-notch fa-spin fa-pulse');
                }
            }).done(function(d){
                if(data_stat==1) {
                    this_.attr('data-stat', 0);
                    this_row.removeClass('info danger warning').addClass('success');
                    this_.removeClass('btn-primary').addClass('btn-success').find('.fa').removeClass('fa-save fa-circle-o-notch fa-spin fa-pulse').addClass('fa-check');
                }else {
                    this_.attr('data-stat', 1);
                    this_row.removeClass('info danger warning success');
                    this_.removeClass('btn-success').addClass('btn-primary').find('.fa').removeClass('fa-check fa-circle-o-notch fa-spin fa-pulse').addClass('fa-save');

                }
            }).fail(function(){
                PECO.phpError();
            });
        });

        init_btn_expand_more_charges();
        init_btn_expand_rate();
        init_btn_expand(0);
	};




	var init_billing_inquiry = function() {
		PECO.DTDefault(tbl_billing_trn, 'Select GDLB First..');

        PECO.select2Basic($('#select_gdlb', document), 'query/select2gdlb', false, false, false, false, true);
        PECO.select2Basic($('#select_month', document), 'systems/select2month', false, false, false, false, false);

        $('.datepicker').datepicker({
            // rtl: PECO.isRTL(),
            orientation: "left",
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

        $('#btn_bill_generate').click(function(e){
            e.preventDefault();
            var this_ = $(this);
            var schedid = $('#schedid').select2('val');
            var bill_no = $('#billno_start', document).val();
            var input_duedate = $('#input_duedate', document).val();
            if (confirm("Generate Bill?") == true) {
                $.ajax({
                    url: PECO.base_url() + 'billing/processbilling',
                    type: 'post',
                    data: {'showall': 4, 'billing': true, 'schedid': schedid, 'billno': bill_no, 'duedate': input_duedate},
                    dataType: 'json',
                    beforeSend: function() {
                        this_.find('.fa').removeClass('fa-tags').addClass('fa-spinner fa-spin fa-pulse');
                    }
                }).done(function(d){
                    this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-tags');
                    PECO.initAlerts(d.msg, 'Billing Process', d.func, false);
                }).fail(function(){
                    this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-tags');
                    PECO.phpError();
                });
            } else {
                return false;
            }
        });


        $(document).on('click', '#btn_close_billing', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_html = this_.html();
            var year = $('#select_year', document).val();
            var month = $('#select_month', document).val();
            swal({
                title: "Are you sure?",
                text: 'Closing the billing will deactivate all the reading schedules.',
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
                        url: PECO.base_url() + 'billing/closebilling',
                        type: 'post',
                        data: {'year': year, 'month': month},
                        beforeSend: function () {
                            PECO.btnLoading(this_, 'Closing billing...');
                        },
                        dataType: 'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg, 'PECO.net', d.func);
                        PECO.btnSuccess(this_, 'Done!', this_html, 'btn-danger');
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });


        $('#billing_register').click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            var year = $('#select_year').val();
            var month = $('#select_month').select2('val');
            var btn_html = this_.html();

            $.ajax({
                url: PECO.base_url() + 'reports/getbillingregister',
                type: 'post',
                data: {'year': year, 'month': month},
                dataType: 'json',
                beforeSend: function () {
                    PECO.btnLoading(this_, 'Generating register...');
                }
            }).done(function (d) {
                var content = '';
                content += d.header;
                content += d.html;
                PECO.pecoRepPrint('', content, false);
                PECO.btnSuccess(this_, 'Done!', btn_html, 'btn-primary');
            }).fail(function () {
                PECO.phpError();
                PECO.btnErrorPHP(this_, 'PHP Error', btn_html, 'btn-primary')
            });
        });


        $('#billing_print').click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            var schedid = $('#schedid').select2('val');
            var cust_num = $('#total_customer').val();

            $.ajax({
                url: PECO.base_url() + 'billing/printbilling',
                type: 'post',
                data: {'schedid': schedid, 'top': cust_num},
                dataType: 'json',
                beforeSend: function () {
                    this_.find('.fa').removeClass('fa-print').addClass('fa-spinner fa-spin fa-pulse');
                }
            }).done(function (d) {
                if (d.qry == true) {
                    PECO.pecoBill('BILLING', d.html);
                } else {
                    PECO.initAlerts(d.msg, 'Billing', 'info');
                }
                this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-print');
            }).fail(function () {
                PECO.phpError();
                this_.find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-print');
            });
        });


        $('#get_mrd_list').click(function(e){
            e.preventDefault();
            var schedid = $('#schedid').select2('val');
            init_analysis_table(schedid);
        });

		$('#frm_filter_billing').submit(function(e){
			e.preventDefault();
			var form = $(this);
            init_billing_table(form);
		});
        // PECO.dtSubDetails(tbl_billing_trn, 'billing/getbillingdetails')



        tbl_billing_trn.on('click', '#btn_expand_all', function(e) {
            e.preventDefault();
            var this_ = $(this);
            tbl_billing_trn.find('tr').each(function(){
               $(this).find('#btn-expand').trigger('click');
            });
        });

        tbl_billing_trn.on('click', '#btn_actual_read', function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var acctid = this_row.find('#acctid').val();
            var schedid = this_row.find('#schedid').val();
            var readid = this_row.find('#readid').val();
            var data_stat = this_.attr('data-stat');
            $.ajax({
                url: PECO.base_url() + 'mrd/submitactualreadingrow',
                type: 'post',
                data: {'readid': readid, 'type': data_stat, 'remarks': 'ACTUAL'},
                dataType: 'json',
                beforeSend: function() {
                    this_.removeClass('btn-primary').find('.fa').removeClass('fa-check fa-save').addClass('fa-circle-o-notch fa-spin fa-pulse');
                }
            }).done(function(d){
                if(data_stat==1) {
                    this_.attr('data-stat', 0);
                    this_row.removeClass('info danger warning').addClass('success');
                    this_.removeClass('btn-primary').addClass('btn-success').find('.fa').removeClass('fa-save fa-circle-o-notch fa-spin fa-pulse').addClass('fa-check');
                }else {
                    this_.attr('data-stat', 1);
                    this_row.removeClass('info danger warning success');
                    this_.removeClass('btn-success').addClass('btn-primary').find('.fa').removeClass('fa-check fa-circle-o-notch fa-spin fa-pulse').addClass('fa-save');

                }
            }).fail(function(){
                PECO.phpError();
            });
        });

        init_btn_expand_more_charges();
        init_btn_expand_rate();
        init_btn_expand(0);
	};

	var init_btn_expand_more_charges = function() {
        tbl_billing_trn.on('click', 'tr #btn_show_charges', function(e){
            e.preventDefault();
            $(this).toggleClass('active');
        });
    };

	var init_btn_expand_rate = function() {
        tbl_billing_trn.on('click', '.rate-sub', function(e){
            e.preventDefault();
            var this_ = $(this);
            this_.closest('li.list-group-item').find('ul.sub').toggleClass('hidden');
        });
    };

	var init_btn_expand = function(ctt) {
        tbl_billing_trn.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            var sched_id = this_.attr('data-sched');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'billing/getbillingcompute',
                    type: 'post',
                    data: {'acctid': data_id, 'schedid': sched_id, 'ctt': ctt},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info"><td colspan="' + thisTr_child + '"><i class="fa fa-spinner fa-spin fa-pulse"></i> Computing charges, please wait...</td></tr>');
                        this_.addClass('hidden');
                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '">' + d.html + '</td></tr>');
                    tbl_billing_trn.find('#loading').remove();
                    this_.removeClass('hidden');
                    PECO.initDTNicescroller();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    tbl_billing_trn.find('#loading').remove();
                    this_.removeClass('hidden');
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                tbl_billing_trn.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                this_.removeClass('hidden');
            }
            this_.toggleClass('expanded');
        });
    };

    var init_analysis_table =  function(data, ctt) {
        var ctt_ = (ctt) ? 1 : 0;
        var show_all = $('#showall').select2('val');
        $('#total_customer').val(0);
        $.ajax({
            url: PECO.base_url() + 'mrd/readinganalysis',
            type: 'post',
            data: {'schedid': data, 'showall': 2, 'ct': ctt_},
            dataType: 'JSON',
            beforeSend: function() {
                tbl_billing_trn.dataTable().empty();
                PECO.DTphpLoading(tbl_billing_trn, ' Computing billing data...');
            },
        }).done(function(data) {
            if(data.qry==true) {


                $('#total_amt', document).html(data.totalamt);

                tbl_billing_trn.dataTable().empty();
                tbl_billing_trn.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    //scrollY: '500px',
                    aaData: data.list,
                    aoColumns: [
                        //{"data": "expand", sClass: 'text-align-center', sWidth: '10px'},
                        {"data": "seq", sClass: 'text-align-center', sWidth: '25px'},
                        {"data": "serviceno", sClass: 'text-primary text-bold'},
                        {"data": "name", sWidth: '300px'},
                        {"data": "meter"},
                        {"data": "meterno"},
                        {"data": "serial", "sClass": "text-info"},
                        {"data": "mult", "sClass": "text-danger", sWidth: '110px'},
                        {"data": "rem", sClass: 'controls', sWidth: '100px'},
                        {"data": "curread", sClass: 'number text-success'},
                        {"data": "prevread", sClass: 'number prevread'},
                        {"data": "currcon", sClass: 'number curcon text-success'},
                        {"data": "prevcon", sClass: 'number prevcon'},
                        {"data": "curdem", sClass: 'number curdem'},
                        {"data": "netmet", sClass: 'number netmet'},
                        //{"data": "regbill", sClass: 'regbill', sWidth: '15px'},
                        {"data": "totalamt", sClass: 'number text-primary text-bold', sWidth: '120px'}, // ADD RELATIVE FOR PERCENT STATS ABSOLUTE
                        {"data": "stats", sClass: '', sWidth: '60px'}

                    ],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found. </h4>'
                    },
                    columnDefs: [
                        {"orderable": false, searchable: false, "targets": 0},
                        //{"orderable": false, searchable: false, "targets": -2}, // REM
                        {"orderable": false, searchable: false, "targets": -3},
                        {"orderable": false, searchable: false, "targets": -4},
                        //{"orderable": false, searchable: false, "targets": -1},
                    ],
                    fnRowCallback: function (nRow, aData, iDisplayIndex) {
                        //$(nRow).addClass(aData.rowbg);
                        $(nRow).find('.icheck').each(function () {
                            var this_ = $(this);
                            var this_td_class = this_.closest('td');
                            var check_color = 'grey';
                            if (this_td_class.hasClass('addbill')) {
                                check_color = 'yellow';
                            }
                            if (this_td_class.hasClass('chckread')) {
                                check_color = 'red';
                            }
                            this_.iCheck({
                                checkboxClass: 'icheckbox_flat-' + check_color,
                                increaseArea: '20%' // optional
                            });
                        });

                        $('td', nRow).addClass(aData.rowbg);

                        $(nRow).find('.tooltips').each(function () {
                            $(this).tooltip();
                        });

                    },
                    fnDrawCallback: function () {
                        PECO.select2_scrollertbl(tbl_billing_trn);
                    },

                    "order": [
                        [0, 'asc']
                    ],
                });

                // PECO.initDTNicescroller();
                $('body').find('#total_customer').val(data.billcnt);

            }else{
                PECO.DTAlert(tbl_billing_trn, data.msg, data.func);
            }
        }).fail(function() {
            PECO.DTDefault(tbl_billing_trn, ' No Record found: Error404');
        });
    };
	
    var init_billing_table = function (form) {
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            dataType: 'json',
			data: form.serialize(),
            beforeSend: function () {
                tbl_billing_trn.dataTable().empty();
                PECO.DTphpLoading(tbl_billing_trn, ' Loading billing... ');
            }
        }).done(function (d) {
            tbl_billing_trn.dataTable().empty();
            tbl_billing_trn.dataTable({
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.data,
                aoColumns: [
                    {"data": "expand"},
                    {"data": "seq"},
                    {"data": "billno"},
                    {"data": "servno", sClass: "text-info"},
                    {"data": "mtr"},
                    {"data": "name"},
                    {"data": "addrspec"},
                    {"data": "due", sClass: "number"},
                    {"data": "prevamt", sClass: "number  text-danger text-bold"},
                    {"data": "surcharge", sClass: "number text-danger"},
                    {"data": "current", sClass: "number text-primary text-bold"},
                    {"data": "total", sClass: "number"},
                    {"data": "stat", sClass: "text-align-center"},
                    {"data": "ebill", sClass: "text-align-center"},
                    {"data": "control", sClass: 'center'},
                ],
                "columnDefs": [
                    /* IMPORTANT IF USED DYNAMIC SERVER TABLE LIST
                     {"orderable": false, searchable: false, "targets": 0},
                     {"orderable": false, searchable: false, "targets": 2},
                     {"orderable": false, searchable: false, "targets": 3},
                     {"orderable": false, searchable: false, "targets": 6},
                     {"orderable": false, searchable: false, "targets": 7},
                     {"orderable": false, searchable: false, "targets": 8},
                     {"orderable": false, searchable: false, "targets": 9},
                     {"orderable": false, searchable: false, "targets": 10},
                     {"orderable": false, searchable: false, "targets": 11},
                     {"orderable": false, searchable: false, "targets": 12}
                     */
                ],
                "order": [[1, "asc"]],
                "lengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": 20,
                fnDrawCallback: function () {
                    console.log('Table drawn..');

                },
                /*
                 * Highlith on search inside the table
                 * need datatable plugins
                 * https://cdn.datatables.net/plug-ins/1.10.12/features/searchHighlight/dataTables.searchHighlight.min.js
                 * https://bartaz.github.io/sandbox.js/jquery.highlight.js
                 * https://cdn.datatables.net/plug-ins/1.10.12/features/searchHighlight/dataTables.searchHighlight.css
                 */
                searchHighlight: true
            });

        });
		

    };

    var init_billing_rate_maintenance_approval = function(stat) {
        init_billing_rate_maintenance_table_approval(stat);

        $(document).on('submit', '#frm_rates_approval', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure you want to confirm this rates?",
                text: 'Approval Rates',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        method: "post",
                        dataType: "json",
                        data: form.serialize()
                    }).done(function (d) {
                        swal.close();
                        init_billing_rate_maintenance_table_approval(stat);
                        if(d.stat == 300) {
                            $('#confirm_button', document).remove();
                        }
                    }).fail(function() {
                        init_billing_rate_maintenance_table_approval(stat);
                        swal.close();
                    })
                }
            });

        });
    };
	
	var init_billing_rate_maintenance = function() {
		$('#filtermonth').select2({allowClear: true, 'placeholder': 'Select..'});
		
		// #######################################################
		// INITIALIZE TABLE ######################################
		init_billing_rate_maintenance_table();

        $(document).on('click', '#btn_rate_search', function(e){
			e.preventDefault();
			init_billing_rate_maintenance_table();
		});
		
		$(document).on('change', '#filtermonth', function(e){
			e.preventDefault();
			init_billing_rate_maintenance_table();
		});

		$(document).on('click', '#btn_send_btn', function(e) {
		    e.preventDefault();
		    var this_ = $(this);
		    var this_html = this_.html();
            var year = $('#filteryear', document).val();
            var month = $('#filtermonth', document).select2('val');
            $.ajax({
                url: PECO.base_url() + 'billing/sendratestoaudit',
                type: 'post',
                data: {'month': month, 'year': year},
                dataType: 'json',
                beforeSend: function() {
                    this_.html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Please wait...');
                }
            }).done(function(d){
                if(d.qry==true) {
                    init_billing_rate_maintenance_table();
                    this_.html(this_html);
                } else {
                    this_.html('<i class="fa fa-refresh"></i> Retry');
                }
            }).fail(function() {
                this_.html('PHP Error');
            });
        });
		

		$('#frm_add_rates').on('reset', function(e) {
			$('input').each(function(){
				$(this).select2('val', '');
			});
		});


		$('#btn_import_rates', document).click(function(e) {
		    e.preventDefault();
		    var this_ = $(this);
		    var this_orig_html = this_.html();
		    var year = $('#filteryear', document).val();
		    var month = $('#filtermonth', document).val();

		    $.ajax({
                url: PECO.base_url() + 'systems/getbillingratefromlegacy',
                type: 'post',
                data: {'month': month, 'year': year},
                dataType: 'json',
                beforeSend: function() {
                    this_.html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Getting data from server...');
                }
            }).done(function(d) {
                if(d.qry==true) {
                    this_.html('<i class="fa fa-check text-success"></i> Success!');
                    init_billing_rate_maintenance_table(month, year);
                    setTimeout(function () {
                        this_.html(this_orig_html);
                    }, 1000);
                }else{
                    this_.html(this_orig_html);
                    PECO.initAlerts(d.msg, 'Data Pullout', 'info');
                    init_billing_rate_maintenance_table(month, year);
                }
            });
        });
		
		$('#frm_add_rates').submit(function(e){
			var form = $(this);
			e.preventDefault();
			$.ajax({
				url: form.attr('action'),
				type: form.attr('method'),
				data: form.serialize(),
				dataType: 'json',
			}).done(function(d) {
				console.log(d);
				if(d.qry==true) {
					var month = $('#filtermonth').select2('val');
					var year = $('#filteryear').val();
					init_billing_rate_maintenance_table();
					PECO.initAlerts(d.msg, 'Success', 'success');
				}else{
					PECO.initAlerts(d.msg, 'Warning', 'warning');
				}
			});
		});
	};


	var init_billing_rate_maintenance_table = function(month, year) {

		var month = $('#filtermonth').select2('val');
		var year = $('#filteryear').val();

		tbl_rate.find('thead tr').empty();		
		tbl_rate.find('tfoot tr').empty();
		tbl_rate.find('tbody tr').empty();

		$.ajax({
			url: PECO.base_url()+'billing/getratelist',
			type: 'POST',
			dataType: 'json',
			data: {'month': month, 'year': year}
		}).done(function(d) {
			if(d.qry==true) {
				tbl_rate.find('thead tr').append('<th width="70px">R.Code</th><th width="230px">Descriptions</th><th  width="70px">Units</th>');
				// ADD DYNAMIC COLUMN IN ROWS
				for (var j = 0; j < d.colnum; j++) {
					tbl_rate.find('thead tr').append('<th class="number">' + d.column[j].th + '</th>');
				};
				// ADD CONTROL COLUMN IN ROWS
				tbl_rate.find('thead tr').append('<th width="80px">Control</th>');


                if(d.ratereqstat != 301) {
                    tbl_rate.find('tfoot tr').append('<td>Add Rate</td><td>' +
                        '<input class="form-control inline input-xs" style="width: 100% !important" id="rate_select" name="brateid"></input></div>' +
                        '</div></td><td><input class="form-control input-xs inline" style="width: 100% !important" id="unit_select" name="rateunit" placeholder="Units.."/></td>');

                    // ADD DYNAMIC COLUMN IN FOOTER
                    for (var j = 0; j < d.colnum; j++) {
                        tbl_rate.find('tfoot tr').append('<td class="number"><div class="input-icon left"><i class="fa fa-pencil"></i><input class="form-control input-xs number inline" style="width: 100% !important;" placeholder="0.00" autocomplete="off" name="' + d.column[j].input + '"/></div></td>');
                    }
                    // ADD CONTROL COLUMN IN FOOTER
                    tbl_rate.find('tfoot tr').append('<th><button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Add</button><button type="reset" class="btn btn-default btn-xs"><i class="fa fa-refresh"></i></button></th>');

                    $('.btn-trn', document).each(function() {
                        $(this).removeClass('hidden');
                    });
                }else{
                    $('.btn-trn', document).each(function() {
                        $(this).addClass('hidden');
                    });
                }
				tbl_rate.dataTable().empty();
				tbl_rate.dataTable({
					bDestroy: true,
					bPaginate: false,
					bFilter: true,
					bInfo: true,
					bStateSave: true,
					bProcessing: true,
					bLengthChange: false,
					aaData: d.data,
					pageLength: 50,
					language: {
						"emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
					},
					searchHighlight: true,
					fnRowCallback: function (nRow, aData, iDisplayIndex) {
						$(nRow).find('td').each(function(){
							
							var tds = $(this);
							setTimeout(function(){
								if(tds.text() <= 0 || $('input', tds).val() <= 0) {
									tds.addClass('text-danger');
									$('input', tds).addClass('text-danger');
								}
								
								if(tds.text() < 0 || $('input', tds).val() < 0) {
									tds.addClass('danger');
									$('input', tds).addClass('text-danger');
								}

								if(tds.text() > 0 && tds.hasClass('number') || $('input', tds).val() > 0) {
									tds.addClass('text-color-blue');
									$('input', tds).addClass('text-color-blue');
								}
							}, 50);
							
						});
					}
				});
				
				tbl_rate.find('td .popovers').each(function(e){
					$(this).popover();
				});
				
				tbl_rate.find('th').each(function() {
					var th_this = $(this);
					var th_index = th_this.index();
					$('tr').each(function() {
						$(this).find('td').eq(th_index).addClass(th_this.attr('class'));
					});
				});
				
				tbl_rate.find('thead th').click(function(){
					var th_this = $(this);
					var th_index = th_this.index();
					tbl_rate.find('tbody tr td').removeClass('info');
					tbl_rate.find('tbody tr').each(function() {
						$(this).find('td').eq(th_index).addClass('info');
					});
				});
				
				tbl_rate.find('#rate_select').select2({
					data: d.rateselect,
					placeholder: 'Select..',
					allowClear: true,
				});
				
				tbl_rate.find('#unit_select').select2({
					data: d.rateunitlist,
					placeholder: 'Select..',
					allowClear: true,
				});
				
				

				PECO.select2_scroller();
				
			}else{
				PECO.initAlerts('No record found!', 'Warning', 'warning');
			}
		}).fail(function(){
			PECO.phpError();
		});	
	};
	var init_billing_rate_maintenance_table_approval = function(stat) {

		var month = $('#filtermonth', document).val();
		var year = $('#filteryear', document).val();

		tbl_rate.find('thead tr').empty();
		tbl_rate.find('tfoot tr').empty();
		tbl_rate.find('tbody tr').empty();

		$.ajax({
			url: PECO.base_url()+'billing/getratelist',
			type: 'POST',
			dataType: 'json',
			data: {'month': month, 'year': year, 'stats': stat}
		}).done(function(d) {
			if(d.qry==true) {
				tbl_rate.find('thead tr').append('<th width="70px">R.Code</th><th width="230px">Descriptions</th><th  width="70px">Units</th>');
				// ADD DYNAMIC COLUMN IN ROWS
				for (var j = 0; j < d.colnum; j++) {
					tbl_rate.find('thead tr').append('<th class="number">' + d.column[j].th + '</th>');
				};
				// ADD CONTROL COLUMN IN ROWS
				tbl_rate.find('thead tr').append('<th width="80px">Control</th>');

				tbl_rate.find('tfoot tr').append('<td>Add Rate</td><td>'+
												 '<input class="form-control inline input-xs" style="width: 100% !important" id="rate_select" name="brateid"></input></div>'+
												 '</div></td><td><input class="form-control input-xs inline" style="width: 100% !important" id="unit_select" name="rateunit" placeholder="Units.."/></td>');
				// ADD DYNAMIC COLUMN IN FOOTER
				for (var j = 0; j < d.colnum; j++) {
					tbl_rate.find('tfoot tr').append('<td class="number"><div class="input-icon left"><i class="fa fa-pencil"></i><input class="form-control input-xs number inline" style="width: 100% !important;" placeholder="0.00" autocomplete="off" name="' + d.column[j].input + '"/></div></td>');
				};
				// ADD CONTROL COLUMN IN FOOTER
				tbl_rate.find('tfoot tr').append('<th><button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Add</button><button type="reset" class="btn btn-default btn-xs"><i class="fa fa-refresh"></i></button></th>');
				tbl_rate.dataTable().empty();
				tbl_rate.dataTable({
					bDestroy: true,
					bPaginate: false,
					bFilter: true,
					bInfo: true,
					bStateSave: true,
					bProcessing: true,
					bLengthChange: false,
					aaData: d.data,
					pageLength: 50,
					language: {
						"emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
					},
					searchHighlight: true,
					fnRowCallback: function (nRow, aData, iDisplayIndex) {
						$(nRow).find('td').each(function(){

							var tds = $(this);
							setTimeout(function(){
								if(tds.text() <= 0 || $('input', tds).val() <= 0) {
									tds.addClass('text-danger');
									$('input', tds).addClass('text-danger');
								}

								if(tds.text() < 0 || $('input', tds).val() < 0) {
									tds.addClass('danger');
									$('input', tds).addClass('text-danger');
								}

								if(tds.text() > 0 && tds.hasClass('number') || $('input', tds).val() > 0) {
									tds.addClass('text-color-blue');
									$('input', tds).addClass('text-color-blue');
								}
							}, 50);

						});
					}
				});

				tbl_rate.find('td .popovers').each(function(e){
					$(this).popover();
				});

				tbl_rate.find('th').each(function() {
					var th_this = $(this);
					var th_index = th_this.index();
					$('tr').each(function() {
						$(this).find('td').eq(th_index).addClass(th_this.attr('class'));
					});
				});

				tbl_rate.find('thead th').click(function(){
					var th_this = $(this);
					var th_index = th_this.index();
					tbl_rate.find('tbody tr td').removeClass('info');
					tbl_rate.find('tbody tr').each(function() {
						$(this).find('td').eq(th_index).addClass('info');
					});
				});

				tbl_rate.find('#rate_select').select2({
					data: d.rateselect,
					placeholder: 'Select..',
					allowClear: true,
				});

				tbl_rate.find('#unit_select').select2({
					data: d.rateunitlist,
					placeholder: 'Select..',
					allowClear: true,
				});



				PECO.select2_scroller();

			}else{
				PECO.initAlerts('No record found!', 'Warning', 'warning');
			}
		}).fail(function(){
			PECO.phpError();
		});
	};


    var init_billing = function () {
        init_billing_table();
    };

    var init_billing_process_ct = function() {
        PECO.select2Basic($('#ctid'), 'mrd/getctgroup', 'CT Group..', true, true);
        PECO.DTDefault(tbl_billing_trn, 'Select GDLB First..');

        $(document).on('click', '#get_ct_group', function(e) {
            e.preventDefault();
            var ctid = $('#ctid', document).select2('val');
            init_analysis_table(ctid, '1');
        });


        init_btn_expand_more_charges();
        init_btn_expand_rate();
        init_btn_expand(true);

    };

    var txt_content = function(html) {
        return html.replace(/<[^>]*>/g, "");
    };

    return {
        init: function () {
            init_billing();
        },
		ratemaintenance: function() {
			init_billing_rate_maintenance();
		},
		rateapproval: function(stat) {
            init_billing_rate_maintenance_approval(stat);
		},
		billinginquiry: function() {
        	init_billing_inquiry();
		},
		billingprocess: function() {
        	init_billing_process();
		},
		billingprocessct: function() {
        	init_billing_process_ct();
		}
    };
}();


