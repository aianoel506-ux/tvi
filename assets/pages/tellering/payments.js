var PAYMENTS = function () {
    // VARIABLES | INITIALIZED
    var tbl_reg = $(document).find('#tbl_billing', document);

    var tbl_assessments = $(document).find('#tbl_assesstments');
    // #############################################################################################
    // #############################################################################################
    // CAD MODULE
    // #############################################################################################
    var init_cad_payments = function(servno, moduleid) {
        PECO.DTDefault(tbl_assessments, 'Labor and services assessments');
        init_cad_payments_tbl(servno, moduleid);
        init_calculator_cad();
    };

    var tbl_penalties = $(document).find('#tbl_penalties');


    // #############################################################################################
    // #############################################################################################
    // LEGAL MODULE
    // #############################################################################################
    var init_legal_payments = function(servno) {
        PECO.DTDefault(tbl_penalties, 'Legal Payments');
        init_legal_payments_tbl(servno);
        init_calculator_legal();
    };

    var init_legal_payments_tbl = function(dataid) {

        $.ajax({
            url: PECO.base_url()+'legal/getpenaltypaymentstbl',
            type: 'post',
            data: {'servno': dataid},
            dataType: 'json',
            beforeSend: function(){
                PECO.DTphpLoading(tbl_penalties,'Loading legal payments..');
            }
        }).done(function(data){
            if( data.qry==true ) {
                $('#totalitem').text(data.qty);
                $('#servamt').html(data.servamt);
                $('#assesstmentvat').html(data.totalvat);
                $('#assesstmentnovat').html(data.totalnvat);
                $('#assessmenttotalamt').html(data.total);
                $('#assessmenttotalamtpaid').html(data.totalpaid);
                $('#assessmenttotalamtbal').html(data.balance);
                $('#sptotalamt').val(data.balance);
                $('#servgrandtotal').html(data.total);

                $('#initdepamt').html(data.initdepamt);
                $('#gdrdepamt').html(data.gdrdepamt);
                $('#laborservamt').html(data.laborservamt);
                $('#otheramt').html(data.otheramt);

                $('#totalvat').html(data.otheramt);

                $('#ar_btn').html(data.arbtn);

                $('#input_typesid', document).val(data.typesid);


                tbl_penalties.dataTable().empty();
                tbl_penalties.dataTable({
                    // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    bLengthChange: false,
                    scrollY: '30vh',
                    aaData: data.list,
                    aoColumns: [
                        {"data": "num"},
                        {"data": "acctno", sWidth: '170px', sClass: '' },
                        {"data": "nonvat", sWidth: '', sClass: 'number novat' },
                        {"data": "vat", sWidth: '', sClass: 'text-danger number vat' },
                        {"data": "cwt", sWidth: '', sClass: 'input text-info number frtx' },
                        {"data": "total", sWidth: '25%', sClass: 'number text-bold total' },
                        {"data": "chk", sWidth: '30px', sClass: 'text-align-center chk' },
                        {"data": "control", sWidth: '20px', sClass: 'hidden-print text-align-center' },
                    ],
                    fnRowCallback: function(nRow, data) {
                        // RE-INITIALIZE TOOLTIPS
                        $(nRow).addClass(data.rowclass);
                        $(nRow).find('.tooltips').tooltip();
                        $(nRow).find('.popovers').each(function (e) {
                            $(this).popover({
                                html: true,
                                animation: true,
                                template: '<div class="popover popover-info"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                            });
                        });
                        //$(nRow).find('td').addClass(data.rowclass);
                        $(nRow).find('.icheck').each(function () {
                            var icheck_ = $(this);
                            PECO.iCheckRow(icheck_, 'minimal', 'red');
                        });


                        $('#check_payadd', nRow).on('ifChecked', function () {
                            $(nRow).toggleClass('payable');
                            init_calculator_legal();
                        }).on('ifUnchecked', function () {
                            $(nRow).toggleClass('payable');
                            init_calculator_legal();
                        });

                        $('td.frtx input', nRow).keyup(function(e){
                            init_calculator_legal();
                            var this_ = $(this);
                            var total_novatamt = $('td.novat', nRow).text().replace(',', '');
                            var total_vatamt = $('td.vat', nRow).text().replace(',', '');
                            var total_amt = Number(total_novatamt) + Number(total_vatamt);
                            var this_amt = this_.val();
                            if(this_amt <= total_amt) {
                                var new_total_amt = Number(total_amt) - Number(this_amt);
                                $('td.total', nRow).text(new_total_amt).number(true, 2);
                                this_.parent('td').removeClass('text-danger').addClass('text-info');
                            }else{
                                $('td.total', nRow).text(total_amt).number(true, 2);
                                this_.parent('td').removeClass('text-info').addClass('text-danger');
                                setTimeout(function(){
                                    this_.val('');
                                }, 1000);
                            }
                        });


                    }
                }).on('ifChecked', function(event){
                    init_calculator_legal();
                }).on('ifUnchecked', function(event){
                    init_calculator_legal();
                });

                PECO.dataTableScroller();


                // FRTX NAVIGATION
                tbl_penalties.on('keydown', 'td.frtx input', function(e) {
                    var this_ = $(this);
                    if (e.which === 40) {
                        var index = $('td.frtx input').index(this) + 1;
                        console.log(index);
                        var this_input = $('td.frtx input').eq(index).focus();
                        setTimeout(function() {
                            this_input.select();
                        },100);
                    }
                });

                tbl_penalties.on('keydown', 'td.frtx input', function(e) {
                    var this_ = $(this);
                    if (e.which === 38) {
                        var index = $('td.frtx input').index(this) - 1;
                        console.log(index);
                        var this_input = $('td.frtx input').eq(index).focus();
                        setTimeout(function() {
                            this_input.select();
                        },100);
                    }
                });


                //PECO.initDTNicescroller();
                if(data.settled==true) {
                    PECO.initAlerts('No account to pay!', 'PECO.tellering', 'info', 2000);
                    $(document).find('.pay-input').each(function(){
                       $(this).find('input').attr('disabled', true);
                    });
                }else{
                    $(document).find('.pay-input').each(function(){
                        $(this).find('input').attr('disabled', false);
                    });
                }

            }else{
                PECO.DTDefault(tbl_penalties, 'Legal Payments was not process yet!');
                PECO.initAlerts('No account to pay!', 'PECO.tellering', 'info', 2000);
                $(document).find('.pay-input').each(function(){
                    $(this).find('input').attr('disabled', true);
                });
            }

        });

        if(PECO.sysCheckMode()==true) {
            console.log('Services Table Loaded!');
        }

       $(document).find('#frm_legal_pay').submit(function(e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                beforeSend: function(){
                    PECO.blockUI({
                        target: this_,
                        animate: true,
                        overlayColor: false
                    });
                },
                dataType: 'json'
            }).done(function(d){
                PECO.unblockUI(this_);

                if(d.qry==true) {
                    $(document).find('#search_trn').trigger('submit');
                    setTimeout(function () {
                        TELLERING.usertrntable();
                    }, 2000);
                }else{
                    PECO.initAlerts(d.msg, 'Payments', d.func);
                }
            }).fail(function(){
                PECO.phpError();
                PECO.unblockUI(this_);
            });
        });

        init_calculator_legal();
        // ############################################################
        $(document).on('keyup','.pay-input input', function(e){
            init_calculator_legal();
        });
    };

    var init_calculator_legal = function() {
        var amt_cash_input      = $(document).find('#amtcash');
        var amt_check_input     = $(document).find('#amtchk');
        var amt_change_input    = $(document).find('#spamtchange');
        var amt_topd_input      = $(document).find('#sptotalamt');
        var amt_rec_input       = $(document).find('#spamtrec');
        var amt_chk_bal         = $(document).find('#amtchkb');
        var amt_cash_bal        = $(document).find('#amtcashb');

        amt_topd_input.number(true, 2);


        var amt_cash            = PECO.numberString(amt_cash_input.val());
        var amt_check           = PECO.numberString(amt_check_input.val());

        var total_amt_rec       = Number(amt_cash) + Number(amt_check);

        amt_rec_input.val(total_amt_rec);
        amt_rec_input.number(true, 2);


        // GET AMT CHECK
        var total_cash_amt              = 0;
        var total_check_amt             = 0;
        var total_checked_chk_amt       = 0;
        var amt_total_frtx              = 0;
        var amt_total_frtx_check        = 0;


        tbl_penalties.find("tr.payable td.chk input:checked").each(function (e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_framt = this_tr.find('td.frtx input').val();

            var this_vatamt = this_tr.find('td.vat .value').text().replace(',','');
            var this_novatamt = this_tr.find('td.novat').text().replace(',','');

            amt_total_frtx_check +=  Number(this_framt);
            total_checked_chk_amt += (Number(this_.val()) - this_framt);
            total_check_amt += (Number(this_vatamt) + Number(this_novatamt));
        });


        tbl_penalties.find("tr.payable td.frtx input").each(function (e) {
            var this_ = $(this);
            amt_total_frtx += Number(this_.val());
        });

        tbl_penalties.find("tr.payable td.total").each(function (e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_vatamt = this_tr.find('td.vat .value').text().replace(',','');
            var this_novatamt = this_tr.find('td.novat').text().replace(',','');
            total_cash_amt += (Number(this_vatamt) + Number(this_novatamt));
        });


        var new_total_frtx_cash_amt = Number(amt_total_frtx - amt_total_frtx_check);
        var new_total_cash_amt = (Number(total_cash_amt) - Number(total_check_amt)) - Number(new_total_frtx_cash_amt);
        var new_total_check_amt = (Number(total_check_amt) - Number(amt_total_frtx_check));

        console.log('#############################################################');
        console.log('TOTAL AMT: '       + total_cash_amt );
        console.log('TOTAL CASH: '      + new_total_cash_amt );
        console.log('TOTAL CHECK: '     + new_total_check_amt);
        console.log('TOTAL FRTX: '      + amt_total_frtx );
        console.log('_____________________________________________________________');
        console.log('FRTX CHECK: '      + amt_total_frtx_check);
        console.log('FRTX CASH: '       + new_total_frtx_cash_amt);

        $(document).find('#totalcheck').text(new_total_check_amt).number(true, 2);
        $(document).find('#totalcash').text(new_total_cash_amt).number(true, 2);
        $(document).find('#totalfrtx').text(amt_total_frtx).number(true, 2);

        var new_total_amt_topd = (new_total_cash_amt + new_total_check_amt);
        var new_total_cash_amt_b = (Number(new_total_cash_amt) - Number(amt_cash_input.val()));
        var new_total_check_amt_b = (Number(new_total_check_amt) - Number(amt_check_input.val()));

        amt_chk_bal.val(new_total_check_amt_b).number(true, 2);
        amt_cash_bal.val(new_total_cash_amt_b).number(true, 2);
        amt_topd_input.val(new_total_amt_topd).number(true, 2);


        var amt_change_new = Number(amt_cash_input.val()) - Number(new_total_cash_amt);

        amt_change_input.val(amt_change_new);
        amt_change_input.number(true, 2);

        /*
        var total_new_cash_amt = Number(total_checked_cash_amt) - Number(total_checked_chk_amt);
        var total_cash_bal_math = Number(total_checked_cash_amt) - Number(total_checked_chk_amt);
        var amt_chk_bal_math =  Number(total_checked_chk_amt) - Number(amt_check);
        var amt_cash_bal_math = Number(total_cash_bal_math) - Number(amt_cash);


        // COMPUTE
        var amt_topd_new = Number(amt_topd) - Number(amt_check);
        var amt_change_new = Number(amt_cash) - Number(amt_topd_new);
        amt_change_input.val(amt_change_new);
        */
    };

    var init_cad_payments_tbl = function(dataid, moduleid, type) {
        var type = (type) ? type : 1;
        $.ajax({
            url: PECO.base_url()+'cad/getcustomerservices',
            type: 'post',
            data: {'appid': dataid, 'moduleid': moduleid, 'type': 1},
            dataType: 'json',
            beforeSend: function(){
                PECO.DTphpLoading(tbl_assessments,'Loading labor and services assessments..');
            }
        }).done(function(data){
            if( data.qry==true ) {
                $('#totalitem').text(data.qty);
                $('#servamt').html(data.servamt);
                $('#assesstmentvat').html(data.totalvat);
                $('#assesstmentnovat').html(data.totalnvat);
                $('#assessmenttotalamt').html(data.total);
                $('#assessmenttotalamtpaid').html(data.totalpaid);
                $('#assessmenttotalamtbal').html(data.balance);
                $('#sptotalamt').val(data.balance);
                $('#servgrandtotal').html(data.total);

                $('#initdepamt').html(data.initdepamt);
                $('#gdrdepamt').html(data.gdrdepamt);
                $('#laborservamt').html(data.laborservamt);
                $('#otheramt').html(data.otheramt);

                $('#totalvat').html(data.otheramt);

                $('#ar_btn').html(data.arbtn);
                // tbl_assessments.dataTable().empty();
                tbl_assessments.DataTable({
                    // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    scrollY: '30vh',
                    aaData: data.list,
                    aoColumns: [
                        {"data": "num"},
                        {"data": "acctno", sWidth: '170px', sClass: '' },
                        {"data": "nonvat", sWidth: '', sClass: 'number novat' },
                        {"data": "vat", sWidth: '', sClass: 'text-danger number vat' },
                        {"data": "cwt", sWidth: '', sClass: 'input text-info number frtx' },
                        {"data": "total", sWidth: '150px', sClass: 'number text-bold total' },
                        {"data": "chk", sWidth: '30px', sClass: 'text-align-center chk' },
                        {"data": "control", sWidth: '20px', sClass: 'hidden-print text-align-center' },
                    ],
                    fnRowCallback: function(nRow, data) {
                        // RE-INITIALIZE TOOLTIPS
                        $(nRow).find('.tooltips').tooltip();
                        $(nRow).find('.popovers').each(function (e) {
                            $(this).popover({
                                html: true,
                                animation: true,
                                template: '<div class="popover popover-info"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                            });
                        });
                        //$(nRow).find('td').addClass(data.rowclass);
                        $(nRow).find('.icheck').each(function () {
                            var icheck_ = $(this);
                            PECO.iCheckRow(icheck_, 'minimal', 'blue');
                        });

                        $('td.frtx input', nRow).keyup(function(e){
                            init_calculator_cad();
                            var this_ = $(this);
                            var total_novatamt = $('td.novat', nRow).text().replace(',', '');
                            var total_vatamt = $('td.vat', nRow).text().replace(',', '');
                            var total_amt = Number(total_novatamt) + Number(total_vatamt);
                            var this_amt = this_.val();
                            if(this_amt <= total_amt) {
                                var new_total_amt = Number(total_amt) - Number(this_amt);
                                $('td.total', nRow).text(new_total_amt).number(true, 2);
                                this_.parent('td').removeClass('text-danger').addClass('text-info');
                            }else{
                                $('td.total', nRow).text(total_amt).number(true, 2);
                                this_.parent('td').removeClass('text-info').addClass('text-danger');
                                setTimeout(function(){
                                    this_.val('');
                                }, 1000);
                            }
                        });


                    }
                }).on('ifChecked', function(event){
                    init_calculator_cad();
                }).on('ifUnchecked', function(event){
                    init_calculator_cad();
                });

                PECO.initDTSlimScroll('tbl_assesstments');


                // FRTX NAVIGATION
                tbl_assessments.on('keydown', 'td.frtx input', function(e) {
                    var this_ = $(this);
                    if (e.which === 40) {
                        var index = $('td.frtx input').index(this) + 1;
                        console.log(index);
                        var this_input = $('td.frtx input').eq(index).focus();
                        setTimeout(function() {
                            this_input.select();
                        },100);
                    }
                });

                tbl_assessments.on('keydown', 'td.frtx input', function(e) {
                    var this_ = $(this);
                    if (e.which === 38) {
                        var index = $('td.frtx input').index(this) - 1;
                        console.log(index);
                        var this_input = $('td.frtx input').eq(index).focus();
                        setTimeout(function() {
                            this_input.select();
                        },100);
                    }
                });


                //PECO.initDTNicescroller();
                if(data.settled==true) {
                    PECO.initAlerts('No account to pay!', 'PECO.tellering', 'info', 2000);
                    $(document).find('.pay-input').each(function(){
                       $(this).find('input').attr('disabled', true);
                    });
                }else{
                    $(document).find('.pay-input').each(function(){
                        $(this).find('input').attr('disabled', false);
                    });
                }

            }else{
                PECO.DTDefault(tbl_assessments, 'Labor or Services was not process yet!');
                PECO.initAlerts('No account to pay!', 'PECO.tellering', 'info', 2000);
                $(document).find('.pay-input').each(function(){
                    $(this).find('input').attr('disabled', true);
                });
            }

        });

        if(PECO.sysCheckMode()==true) {
            console.log('Services Table Loaded!');
        }

        $(document).find('#frm_cad_pay').submit(function(e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                beforeSend: function(){
                    PECO.blockUI({
                        target: this_,
                        animate: true,
                        overlayColor: false
                    });
                },
                dataType: 'json'
            }).done(function(d){
                PECO.unblockUI(this_);

                if(d.qry==true) {
                    $(document).find('#search_trn').trigger('submit');
                    setTimeout(function () {
                        TELLERING.usertrntable();
                    }, 2000);
                }else{
                    PECO.initAlerts(d.msg, 'Payments', d.func);
                }
            }).fail(function(){
                PECO.phpError();
                PECO.unblockUI(this_);
            });
        });

        init_calculator_cad();
        // ############################################################
        $(document).on('keyup','.pay-input input', function(e){
            init_calculator_cad();
        });
    };

    var init_calculator_cad = function() {
        var amt_cash_input      = $(document).find('#amtcash');
        var amt_check_input     = $(document).find('#amtchk');
        var amt_change_input    = $(document).find('#spamtchange');
        var amt_topd_input      = $(document).find('#sptotalamt');
        var amt_rec_input       = $(document).find('#spamtrec');
        var amt_chk_bal         = $(document).find('#amtchkb');
        var amt_cash_bal        = $(document).find('#amtcashb');

        amt_topd_input.number(true, 2);


        var amt_cash            = PECO.numberString(amt_cash_input.val());
        var amt_check           = PECO.numberString(amt_check_input.val());

        var total_amt_rec       = Number(amt_cash) + Number(amt_check);

        amt_rec_input.val(total_amt_rec);
        amt_rec_input.number(true, 2);


        // GET AMT CHECK
        var total_cash_amt              = 0;
        var total_check_amt             = 0;
        var total_checked_chk_amt       = 0;
        var amt_total_frtx              = 0;
        var amt_total_frtx_check        = 0;


        tbl_assessments.find("td.chk input:checked").each(function (e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_framt = this_tr.find('td.frtx input').val();

            var this_vatamt = this_tr.find('td.vat .value').text().replace(',','');
            var this_novatamt = this_tr.find('td.novat').text().replace(',','');

            amt_total_frtx_check +=  Number(this_framt);
            total_checked_chk_amt += (Number(this_.val()) - this_framt);
            total_check_amt += (Number(this_vatamt) + Number(this_novatamt));
        });

        tbl_assessments.find("td.frtx input").each(function (e) {
            var this_ = $(this);
            amt_total_frtx += Number(this_.val());
        });

        tbl_assessments.find("td.total").each(function (e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');

            var this_vatamt = this_tr.find('td.vat .value').text().replace(',','');
            var this_novatamt = this_tr.find('td.novat').text().replace(',','');

            total_cash_amt += (Number(this_vatamt) + Number(this_novatamt));
        });
        var new_total_frtx_cash_amt = Number(amt_total_frtx - amt_total_frtx_check);
        var new_total_cash_amt = (Number(total_cash_amt) - Number(total_check_amt)) - Number(new_total_frtx_cash_amt);
        var new_total_check_amt = (Number(total_check_amt) - Number(amt_total_frtx_check));

        console.log('#############################################################');
        console.log('TOTAL AMT: '       + total_cash_amt );
        console.log('TOTAL CASH: '      + new_total_cash_amt );
        console.log('TOTAL CHECK: '     + new_total_check_amt);
        console.log('TOTAL FRTX: '      + amt_total_frtx );
        console.log('_____________________________________________________________');
        console.log('FRTX CHECK: '      + amt_total_frtx_check);
        console.log('FRTX CASH: '       + new_total_frtx_cash_amt);

        $(document).find('#totalcheck').text(new_total_check_amt).number(true, 2);
        $(document).find('#totalcash').text(new_total_cash_amt).number(true, 2);
        $(document).find('#totalfrtx').text(amt_total_frtx).number(true, 2);

        var new_total_amt_topd = (new_total_cash_amt + new_total_check_amt);
        var new_total_cash_amt_b = (Number(new_total_cash_amt) - Number(amt_cash_input.val()));
        var new_total_check_amt_b = (Number(new_total_check_amt) - Number(amt_check_input.val()));

        amt_chk_bal.val(new_total_check_amt_b).number(true, 2);
        amt_cash_bal.val(new_total_cash_amt_b).number(true, 2);
        amt_topd_input.val(new_total_amt_topd).number(true, 2);


        var amt_change_new = Number(amt_cash_input.val()) - Number(new_total_cash_amt);

        amt_change_input.val(amt_change_new);
        amt_change_input.number(true, 2);

        /*
        var total_new_cash_amt = Number(total_checked_cash_amt) - Number(total_checked_chk_amt);
        var total_cash_bal_math = Number(total_checked_cash_amt) - Number(total_checked_chk_amt);
        var amt_chk_bal_math =  Number(total_checked_chk_amt) - Number(amt_check);
        var amt_cash_bal_math = Number(total_cash_bal_math) - Number(amt_cash);


        // COMPUTE
        var amt_topd_new = Number(amt_topd) - Number(amt_check);
        var amt_change_new = Number(amt_cash) - Number(amt_topd_new);
        amt_change_input.val(amt_change_new);
        */
    };


    // #############################################################################################
    // #############################################################################################
    // BILLING PAYMENTS
    // #############################################################################################

    var init_bill_payments = function(servno, mtr, moduleid) {

        var tbl = $('#tbl_billing');


        PECO.iCheckRow($('#bulplaycheckbox', document), 'minimal', 'blue');

        // ENABLING TABLE SIZE
        var width = screen.width;
        var height = screen.height;
        if(height<=768){
            var_table_scroll_height = '200px';
        }else{
            var_table_scroll_height = '280px';
        }

        $.ajax({
            url: PECO.base_url() + 'tellering/getbilling',
            type: 'post',
            dataType: 'json',
            data: {'servno': servno, 'mtr': mtr},

            beforeSend: function () {
                tbl.dataTable().empty();
                PECO.DTphpLoading(tbl, ' Loading A/R ..');
            }

        }).done(function (data) {

            tbl.dataTable().empty();
            var oTable = tbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bSort: false,
                bStateSave: true,
                bProcessing: true,
                aaData: data.tellering,
                //scrollY: var_table_scroll_height,
                scrollY:        '30vh',
                aoColumns: [
                    {"data": "month", sClass: 'text-align-left text-bold', sWidth: '60px'},
                    {"data": "year", sClass: 'text-align-center text-info'},
                    {"data": "current", sClass: 'number amt', sWidth: '25%'},
                    {"data": "interest", sClass: 'number int', sWidth: '20%'},
                    {"data": "vat", sClass: 'number vat', sWidth: '10%'},
                    {"data": "amtpd", sClass: 'number amtpd', sWidth: '30%'},
                    {"data": "frtx", sClass: 'number frtx', sWidth: '15%'},
                    {"data": "chk", sClass: 'chk', sWidth: ''},
                    {"data": "ref", sClass: 'number'},
                    {"data": "inf", sClass: 'number', sWidth: ''},
                    {"data": "select", sClass: 'number select', sWidth: ''},
                    {"data": "control", sClass: 'number control', sWidth: ''},
                    {"data": "del", sClass: 'number control', sWidth: ''},
                ],
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                },
                fnRowCallback: function (nRow, aData) {
                    $(nRow).addClass(aData.rowbg);

                    /*
                    if(aData.curmo==true) {
                        $(nRow).addClass('success').eq(3);
                    }
                    */
                    $(nRow).find('[data-toggle="tooltips"]').each(function () {
                        $(this).tooltip();
                    });
                    $(nRow).find('[data-toggle="popover"]').each(function () {
                        $(this).popover({
                            html: true,
                        });
                    });


                    if (parseInt(aData.current) > 0 && aData.curmo == false) {
                        console.log(aData.current);
                        $(nRow).find('td').eq(3).addClass('text-danger');
                    }

                    $(nRow).each(function (e) {
                        $(this).find('input.number').each(function () {
                            $(this).inputmask({
                                alias: 'decimal', groupSeparator: ',', autoGroup: true
                            });
                        });
                    });


                    //PECO.iCheckRow($('input.select', nRow), 'minimal', 'red');
                    //PECO.iCheckRow($('input.check', nRow), 'minimal', 'blue');


                    $(nRow).find('.popovers').each(function (e) {
                        $(this).popover({
                            html: true,
                            animation: true,
                        });
                    });
                }

            }).on('click', 'tr', function() {
                var this_ = $(this);
                var inputs = $('input[type=checkbox].select', this_);
                inputs.prop("checked", !inputs.prop("checked"));
                init_calculator();
            });

            /*.on('ifChecked', 'tr input', function (e) {
                var this_ = $(this);
                this_.attr('checked', true);
                init_calculator();
            }).on('ifUnchecked', 'tr input', function (e) {
                var this_ = $(this);
                this_.attr('checked', false);
                init_calculator();
            }).on('keyup', 'tr input', function (e) {
                var this_input = $(this);
                init_calculator(this_input);
            }).on('keyup', 'tr', function (e) {
                e.preventDefault()
                var this_tr = $(this);
                var key = (e.keyCode ? e.keyCode : e.which);
                if (key == 115) {
                    this_tr.find('td.select').find('.icheck').iCheck('toggle');
                }
                if (key == 116) {
                    this_tr.find('td.chk').find('.icheck').iCheck('toggle');
                }
            });
            */

            var col_cnt = oTable.fnSettings().aoColumns.length;

            $(document).on('click', '#btn_add_row', function(e) {
                e.preventDefault();
                alert('add row');

                oTable.row.add( {
                    "month":       "11",
                } ).draw();
            });


            $(document).find('#footnote').html(data.footnote);
            $(document).find('#curr_amt').html(data.amtcur);
            $(document).find('#amt_total').html(data.amtbal);
            $(document).find('#min_amt').html(data.minamt);
            $(document).find('#num_stat_bill').text(data.nobill);
            $(document).find('#num_ar_bill').text(data.nobill);
            $(document).find('#num_pay').html(data.numpay);
            $(document).find('#sptotalamt').val(data.amtdue);
            $(document).find('#acc_stat').html(data.status);

            setTimeout(function () {
                var this_input_first = $(document).find('td.int:first input', tbl);
                if (this_input_first.attr('type') == 'hidden') {
                    $('#amtcash').focus().select();
                } else {
                    this_input_first.focus().select();
                }
            }, 200);

            $(document).find('#cust-name').html(data.name);
            $(document).find('#cust-servno').text(data.servno);
            $(document).find('#cust-addr').html(data.address);
            //$(document).find('#amtcash', document).val(data.amttopay);
            $(document).find('#mask_number').focus();

            init_calculator();
        });

        $('#frm_submit_pay').submit(function(e) {
            e.preventDefault();
            var form_ = $(this);
            $.ajax({
                url: form_.attr('action'),
                type: form_.attr('method'),
                data: form_.serialize(),
                beforeSend: function() {
                    PECO.blockUI({
                        target: form_,
                        animate: true,
                    });
                }
            }).done(function(d){
                PECO.unblockUI(form_);
                TELLERING.usertrntable();
            }).fail(function(){
                PECO.unblockUI(form_);
                PECO.phpError();
            });
        });

        init_calculator();
        $(document).on('keyup','.pay-input input', function(e){
            init_calculator();
        });


    };

    var reset_transactions = function() {
        $(document).find('input').each(function(){
            if($(this).hasClass('mtr')==false && $(this).hasClass('servno')==false) {
                $(this).val('');
            }
            reset_billing_transactions();
            $('#search_txt').focus();
        });
    };

    var reset_billing_transactions = function() {
        tbl_reg.dataTable().empty();
        $(document).find('.list-group').find('.label-default').each(function(){
            $(this).text('');
        });
    };

    var init_payments_table_nav = function() {
        // NET AMT NAVIGATION
        tbl_reg.on('keydown', 'td.amtpd input', function(e) {
            var this_ = $(this);
            if (e.which === 40) {
                var index = $('td.amtpd input').index(this) + 1;
                console.log(index);
                var this_input = $('td.amtpd input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
            }
        });
        tbl_reg.on('keydown', 'td.amtpd input', function(e) {
            var this_ = $(this);
            if (e.which === 38) {
                var index = $('td.amtpd input').index(this) - 1;
                console.log(index);
                var this_input = $('td.amtpd input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
            }
        });
        // INTEREST NAVIGATION
        tbl_reg.on('keydown', 'td.int input', function(e) {
            var this_ = $(this);
            if (e.which === 40) {
                var index = $('td.int input').index(this) + 1;
                console.log(index);
                var this_input = $('td.int input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
            }
        });
        tbl_reg.on('keydown', 'td.int input', function(e) {
            var this_ = $(this);
            if (e.which === 38) {
                var index = $('td.int input').index(this) - 1;
                console.log(index);
                var this_input = $('td.int input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
            }
        });
        // FRTX NAVIGATION
        tbl_reg.on('keydown', 'td.frtx input', function(e) {
            var this_ = $(this);
            if (e.which === 40) {
                var index = $('td.frtx input').index(this) + 1;
                console.log(index);
                var this_input = $('td.frtx input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
            }
        });
        tbl_reg.on('keydown', 'td.frtx input', function(e) {
            var this_ = $(this);
            if (e.which === 38) {
                var index = $('td.frtx input').index(this) - 1;
                console.log(index);
                var this_input = $('td.frtx input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
            }
        });

    };


    var init_calculator = function(this_input) {
        console.log('Init calculator function...');
        var this_input = (this_input) ? this_input : false;
        var amt_total_net = 0;
        var amt_cash_bal = 0;
        var amt_check_bal = 0;
        var amt_check_bal_val = 0;
        var amt_total_check = 0;
        var amt_total_cash = 0;
        var input_months_arr = [];
        var change_amt_color = '';
        var amt_total_frtx = 0;
        var amt_total_vat = 0;
        var amt_total_int = 0;
        //$(document).find('#sptotalamt').val('0');

        var amt_cash = PECO.numberString($(document).find('#amtcash').val());
        var amt_check = PECO.numberString($(document).find('#amtchk').val());

        tbl_reg.find("tr td.select input:checked").each(function () {
            var this_ = $(this);
            var row = this_.closest("tr");

            var amt_net         = row.find("td.amtpd").find('input').val();
            var amt_int         = row.find("td.int").find('input').val();
            var amt_fr          = row.find('td.frtx').find('input').val();
            var amt_vat         = row.find('td.vat').find('input').val();

            var amt_net_number  = PECO.numberString(amt_net);
            var amt_int_number  = PECO.numberString(amt_int);
            var amt_fr_number   = PECO.numberString(amt_fr);
            var amt_vat_number  = PECO.numberString(amt_vat);

            amt_total_net += (Number(amt_net_number) - Number(amt_fr_number));
            amt_total_frtx += amt_fr_number;
            amt_total_vat += amt_vat_number;
            amt_total_int += amt_int_number;
        });

        tbl_reg.find("td.chk input:checked").each(function () {
            var this_ = $(this);
            var row = this_.closest("tr");
            var amt_net = row.find("td.amtpd").find('input').val();
            var amt_chk_number = PECO.numberString(amt_net);
            amt_total_check += amt_chk_number;
        });
        amt_total_cash = amt_total_net - amt_total_check;
        // COMPUTE AMT CASH BALANC
        if(amt_check>0) {
            amt_check_bal = amt_check - amt_total_net;
            if(amt_check_bal <= 0) {
                //amt_check_bal = 0; // Auto zero check balance
                amt_cash_bal = amt_cash + amt_check_bal;
                amt_check_bal_val = 0;
                $(document).find('#amtchk').removeClass('text-danger');
            }else{
                $(document).find('#amtchk').addClass('text-danger');
                amt_check_bal_val = amt_check - amt_total_net;
            }
        }else{
            amt_cash_bal = amt_cash - amt_total_net;
        }
        var amt_rec = amt_cash + amt_check;

        $(document).find('#amtcashb').val($.number(amt_cash_bal, 2));
        $(document).find('#amtchkb').val($.number(amt_check_bal_val, 2));
        $(document).find('#spamtrec').val(amt_rec);
        $(document).find('#spamtrectxt').val($.number(amt_rec, 2));
        $(document).find('#totalcheck').text($.number(amt_total_check, 2));
        $(document).find('#totalcash').text($.number(amt_total_cash, 2));
        $(document).find('#totalfrtx').text($.number(amt_total_frtx, 2));
        $(document).find('#totalvat').text($.number(amt_total_vat, 2));
        $(document).find('#totalint').text($.number(amt_total_int, 2));
        $(document).find('#sptotalamt').val($.number(amt_total_net, 2));
        $(document).find('#spamtchange').val($.number(amt_cash_bal, 2)).addClass(change_amt_color);
        if(amt_cash_bal>0) {
            $(document).find('#spamtchange').removeClass('text-danger').addClass('text-primary');
        }else{
            $(document).find('#spamtchange').removeClass('text-primary').addClass('text-danger');
        }

        console.log(amt_cash + ' / ' + amt_cash_bal);

        // CHANGING OF VAT PROPORTIONATE
        if(this_input != '') {
            if(this_input.closest('td').hasClass('amtpd')) {
                var this_tr = this_input.closest('tr');
                var amt_bill = this_tr.find('td.amt').text();
                var amt_bill_number = PECO.numberString(amt_bill);
                var amt_vat_old = this_tr.find('td.vat input').val();
                if(amt_bill_number > 0) {
                    var amt_vat_old_number = PECO.numberString(amt_vat_old);
                    var amt_net = this_input.val();
                    var amt_net_number = PECO.numberString(amt_net);
                    var amt_new_vat = 0;
                    if( amt_net_number < amt_bill_number ) {
                        var percent_pay = amt_net_number / amt_bill_number;
                        var amt_new_vat = amt_vat_old_number * percent_pay;
                        this_tr.find('td.vat .txt').val(amt_new_vat);
                        amt_new_vat = amt_new_vat;
                    }else{
                        amt_new_vat = amt_vat_old_number;
                    }
                    this_tr.find('td.vat span.txt').text($.number(amt_new_vat, 2));
                }
            }
        }
    };


    return {
        cad: function(servno, moduleid) {
            init_cad_payments(servno, moduleid);
        },
        bill: function(servno, mtr, moduleid) {
            init_bill_payments(servno, mtr, moduleid);
        },
        legal: function(servno) {
            init_legal_payments(servno);
        },
    };
}();


