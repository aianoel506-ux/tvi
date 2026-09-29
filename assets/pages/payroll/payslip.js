var PAYSLIP = function () {
    PECO.getHighlightsPlugin();
    PECO.getNumberFormatPlugin();
    PECO.getiCheckPlugin();
    PECO.getNumberFormatPlugin();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();

    PECO.DTDefault($('#payrollreportstbl', document), 'Get data first!');
    //PECO.dtSubDetails($('#payrollreportstbl', document), 'payroll/payrollinfo');

    var payroll_tbl_ = $('#payrollreportstbl', document);

    var init_email_payslips = function() {
        payroll_tbl_.on('click', '#btn-expand', function (e) {
            e.preventDefault();

            var month = $('#month', document).select2('val');
            var year = $('#year', document).val();
            var payclass = $('#payclasscombo', document).val();
            var paytype = $('#payrollperiod', document).val();

            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');

            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url() + 'payroll/payslipinfo',
                    type: 'post',
                    data: {'id': data_id, 'year': year, 'month': month, 'paytype': paytype, 'payclass': payclass},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info"><td colspan="' + thisTr_child + '"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');
                    }
                }).done(function (d) {
                    thisTr.after('<tr class="animated fadeIn fast compact ' + d.func + '" id="details"><td colspan="' + thisTr_child + '">' + d.html + '</td></tr>');
                    payroll_tbl_.find('#loading').remove();
                }).fail(function () {
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    payroll_tbl_.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                payroll_tbl_.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');

        });


        payroll_tbl_.on('click', '#btn_send_payslip', function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            init_send_payslip(this_tr);
        });


        $('#btn_send_payslips', document).click(function (e) {
            e.preventDefault();
            var this_total_row = $('tbody tr', payroll_tbl_).length + 1;
            init_row_mail(0, this_total_row, 0);
        });
    };

    var init_row_mail = function(index, totalrows, sent) {
        var this_tr = $('tr', payroll_tbl_).eq(index);
        var send = init_send_payslip(this_tr);
        var new_index = (Number(index) + 1);
        if(new_index == totalrows) {
            /*
            if(sent >= totalrows) {
                swal(
                    'Done!',
                    sent + ' out of ' + totalrows + ' sent!',
                    'success'
                );
            }else{
                swal(
                    'Done!',
                    sent + ' out of ' + totalrows + ' sent!',
                    'warning'
                );
            }
            */
        }else{
            var sent = send.sent;
            init_row_mail(new_index, totalrows, sent);
        }
    };


    var init_row_mail_bak = function(index, totalrows, sent) {
        var new_index = (Number(index) + 1);
        if(new_index == totalrows) {
            /*
            if(sent >= totalrows) {
                swal(
                    'Done!',
                    sent + ' out of ' + totalrows + ' sent!',
                    'success'
                );
            }else{
                swal(
                    'Done!',
                    sent + ' out of ' + totalrows + ' sent!',
                    'warning'
                );
            }

             */
        }else{
            var this_tr = $('tr', payroll_tbl_).eq(new_index);
            var send = init_send_payslip(this_tr);
            var sent = send.sent;
            init_row_mail(new_index, totalrows, sent);
            console.log(sent);
        }
    };


    var init_send_payslip = function(this_tr) {
        var res = {};
        var this_btn = $('#btn_send_payslip', this_tr);
        var empid = this_btn.attr('data-empid');
        var payclass = this_btn.attr('data-payclass');
        var year = this_btn.attr('data-year');
        var month = this_btn.attr('data-month');
        var period = this_btn.attr('data-period');

        setTimeout(function() {
            $.ajax({
                url: base_url + 'payroll/emailpayslip',
                type: 'post',
                data: {
                    'empid': empid,
                    'payclass': payclass,
                    'year': year,
                    'month': month,
                    'period': period
                },
                retries: 1,
                retryInterval: 1000,
                dataType: 'json',
                async: false,
                cache: false,
                beforeSend: function() {
                    this_tr.removeClass('row-success row-warning').addClass('row-info');
                    $('.fa', this_btn).removeClass('fa-envelope').addClass('fa-circle-o-notch fa-spin');
                }
            }).done(function(d) {
                if(d.qry == true){
                    this_tr.removeClass('row-info row-warning').addClass('row-success');
                    $('.fa', this_btn).removeClass('fa-envelope fa-circle-o-notch fa-spin').addClass('fa-check');
                }else{
                    this_tr.removeClass('row-info row-success').addClass('row-warning');
                    $('.fa', this_btn).removeClass('fa-envelope fa-circle-o-notch fa-spin').addClass('fa-warning');
                    PECO.initAlerts(d.msg,d.title,'info',500);
                }
                res = d;
            }).fail(function() {
                res = {'qry': false, 'sent': 0};
                var this_btn = $('#btn_send_payslip', this_tr);
                $('.fa', this_btn).removeClass('fa-envelope').addClass('fa-times');
                this_tr.addClass('row-danger');
            });
        }, 1000);

        return res;
    };

    return {
        init: function () {
            init_email_payslips();
        }
    }
}();