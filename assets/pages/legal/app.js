var LEGAL = function() {
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();
    PECO.getDatePickerPlugins();

    var tbl_ledger = $('#tbl_ledger', document);
    var tbl_ir_reports = $('#tbl_ir_reports', document);
    var tbl_flexible_payment_plan = $('#tbl_flexible_payment_plan', document);
    var frm_ledger_entry = $('#frm_ledger_entry', document);

    var init_app = function(refid) {
        init_tbl_ledger(refid);
        init_ledger_form();

        $('#input_bank_list', document).select2();
        $('#input_date_spec', document).datepicker();


        PECO.select2Types($('#input_entry_type', document), 'LEGALTRN', 'Select trans..', true, false, false, false, true);

        $('.nav-tabs a').on('shown.bs.tab', function(event){
            var x = $(event.target).text();         // active tab
            var y = $(event.relatedTarget).text();  // previous tab
            var this_ = $(this);
            var this_href = this_.attr('href');
            if(this_href == '#ledger') {
                init_tbl_ledger(refid);
            }
            if(this_href == '#payments') {
                init_tbl_payments(refid);
            }
            if(this_href == '#reports') {
                init_tbl_reports(refid);

                $("#irdate", document).datepicker({
                    format: "yyyy-mm-dd",
                    position: 'bottom',
                    autoclose: true,
                });
                $("#select2violation", document).select2({
                    allowClear: true,
                    placeholder: 'Select violation..'
                });
            }
        });

        tbl_ledger.on('click', '#btn_delete_ledger_item', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_id = this_.attr('data-id');
            swal({
                title: "Are you sure?",
                text: 'Delete ledger item.',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'legal/deleteledgeritem',
                        type: 'post',
                        data: {'id': this_id},
                        dataType: 'json'
                    }).done(function(d) {
                        this_tr.fadeTo('fast', 0.5, function() {
                            init_tbl_ledger(refid);
                            swal.close();
                        });
                    }).fail(function() {
                        PECO.phpError();
                        swal.close();
                    });
                } else {
                    swal.close();
                }
            });
        });
    };

    var init_tbl_payments = function(refid) {
        $.ajax({
            url: PECO.base_url() + 'legal/getflexipayment',
            type: 'post',
            data: {'refid': refid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_flexible_payment_plan, 'Loading flexible payment plan....');
            }
        }).done(function(d) {
            tbl_flexible_payment_plan.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {data: 'num', sClass: 'number', sWidth: '5%'},
                    {data: 'year', sClass: 'text-primary number', sWidth: ''},
                    {data: 'month', sClass: 'text-info number', sWidth: ''},
                    {data: 'duedate', sClass: 'text-danger', sWidth: ''},
                    {data: 'amount', sClass: 'number', sWidth: ''},
                    {data: 'datepaid', sClass: 'text-align-center ', sWidth: ''},
                    {data: 'status', sClass: '', sWidth: ''},
                    {data: 'control', sClass: '', sWidth: ''},
                ],
                language: PECO.DTEmptyMessage('No payment info!')
            });
        }).fail(function() {
            PECO.DTphpError(tbl_flexible_payment_plan);
        });
    };

    var init_ledger_form = function() {
        $(document).on('submit', '#frm_ledger_entry', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: form.attr('title'),
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'post',
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function(d) {
                        init_tbl_ledger(d.refid);
                        swal.close();
                    }).fail(function() {
                        PECO.phpError();
                        swal.close();
                    });
                } else {
                    swal.close();
                }
            });
        });
    };

    var init_tbl_reports = function(refid) {
        PECO.DTDefault(tbl_ir_reports, 'No IR Reports!');
    };

    var init_tbl_ledger = function(refid) {
        $.ajax({
            url: PECO.base_url() + 'legal/tblledger',
            type: 'post',
            data: {refid: refid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_ledger, 'Loading apprehension ledger...');
            }
        }).done(function(d) {
            $('#total_amt', document).html(d.totalamt);
            $('#total_paid', document).html(d.totalpaid);
            $('#total_balance', document).html(d.totalbalance);

            tbl_ledger.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {data: 'num', sClass: 'number text-danger', sWidth: '5%'},
                    {data: 'datepost', sClass: ' text-info', sWidth: ''},
                    {data: 'types', sClass: ' text-info', sWidth: ''},
                    {data: 'acctno', sClass: 'bold', sWidth: ''},
                    {data: 'amt', sClass: ' number text-bold', sWidth: ''},
                    {data: 'paid', sClass: 'number ', sWidth: ''},
                    {data: 'nextdue', sClass: 'text-danger', sWidth: ''},
                    {data: 'top', sClass: 'number', sWidth: ''},
                    {data: 'monthly', sClass: 'number', sWidth: ''},
                    {data: 'status', sClass: 'text-primary', sWidth: ''},
                    {data: 'checkbox', sClass: 'input', sWidth: '15px', sortable: false},
                    {data: 'control', sClass: '', sWidth: '10px', sortable: false},
                ],
                "order": [1, "desc"],
                language: PECO.DTEmptyMessage('No account entry yet!'),
                searchHighlight: true,
                fnRowCallback: function(nRow, aData, index) {
                    PECO.iCheckRow($('#row_icheck', nRow), 'minimal', 'red');
                    $('.tooltips', nRow).tooltip();
                }
            });
        }).fail(function() {
            PECO.DTphpError(tbl_ledger);
        });
    };

    return {
        app: function(refid) {
            init_app(refid);
        }
    }
}();