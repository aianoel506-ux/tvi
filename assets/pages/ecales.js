// Author: Lucky John Faderon
// March 31, 2017

var ECALES = function () {
    PECO.getNumberFormatPlugin();
    PECO.getHighlightsPlugin();

    var tbl_ecales_list = $('#tbl_ecales',document);

    var item_search = $('#item_search', document);
    var svcs_search = $('#svcs_search', document);
    var item_id = $('#item_select', document);
    var svcs_id = $('#svcs_select', document);
    var item_desc = $('#item_desc', document);
    var text_lastprice = $('#text_lastprice', document);
    var text_lastdate = $('#text_lastdate', document);
    var text_itemtotal = $('#text_itemtotal', document);
    var svcs_lastprice = $('#svcs_lastprice', document);
    var svcs_lastdate = $('#svcs_lastdate', document);
    var svcs_itemtotal = $('#svcs_itemtotal', document);
    var item_qty = $('#input_qty', document);
    var svcs_days = $('#svcs_days', document);
    var frm_process_ecales = $('#frm_process_ecales', document);
    var tbl_ecales_service = $('#tbl_ecales_service', document);
    var tbl_ecales_summary = $('#tbl_ecales_summary',document);
    var tbl_ecales_templates = $('#tbl_ecales_templates',document);

    var init_analysis = function (ecalesid,appid) {

        $('body').on('submit', '#frm_add_item', function (e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function() {

                },
            }).done(function(d) {
                init_ecales_list(ecalesid);
                init_ecales_summary(ecalesid);
                $('.input-reset', document).each(function(e) {
                    $(this).val('');
                });
                setTimeout(function() {
                    item_qty.val('1');
                }, 200);
            }).fail(function() {
                PECO.phpError();
            });
        });

        $('body').on('submit', '#frm_add_service', function (e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function() {

                },
            }).done(function(d) {
                init_ecales_service_list(ecalesid);
                init_ecales_summary(ecalesid);
                $('.input-reset', document).each(function(e) {
                    $(this).val('');
                });
                setTimeout(function() {
                    svcs_days.val('1');
                }, 200);
            }).fail(function() {
                PECO.phpError();
            });
        });

        tbl_ecales_list.on('keypress','#item_price',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            var this_ = $(this);
            var item_id = this_.attr('data-id');
            var supp_id = this_.attr('data-supplier');
            var amt = this_.val();
            if (keycode == 13) {
                //alert(item_id + ' - ' + supp_id + ' - ' + amt);
                $.ajax({
                    url: base_url + 'analysis/updateecalesitems',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        type: 'price',
                        itemspecid : item_id,
                        suppid : supp_id,
                        amt : amt
                    }
                }).done(function (e) {
                    PECO.initAlerts(e.msg,'ECALES',e.func);
                    this_.val(e.price);
                });
            }
        });

        tbl_ecales_list.on('keypress','#item_qty',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            var this_ = $(this);
            var trn_id = this_.attr('data-id');
            var qty = this_.val();
            if (keycode == 13) {
                //alert(item_id + ' - ' + supp_id + ' - ' + amt);
                $.ajax({
                    url: base_url + 'analysis/updateecalesitems',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        type: 'qty',
                        sysid : trn_id,
                        qty : qty
                    }
                }).done(function (e) {
                    PECO.initAlerts(e.msg,'ECALES',e.func);
                    this_.val(e.qty);
                });
            }
        });

        tbl_ecales_service.on('keypress','#service_rate',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            var this_ = $(this);
            var trn_id = this_.attr('data-id');
            var qty = this_.val();
            if (keycode == 13) {
                //alert(item_id + ' - ' + supp_id + ' - ' + amt);
                $.ajax({
                    url: base_url + 'analysis/updateecalesservice',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        type: 'rate',
                        serviceid : trn_id,
                        servicerate : parseFloat(qty.replace(',','')),
                    }
                }).done(function (e) {
                    PECO.initAlerts(e.msg,'ECALES',e.func);
                    this_.val(e.rate);
                });
            }
        });

        tbl_ecales_service.on('keypress','#no_days',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            var this_ = $(this);
            var sys_id = this_.attr('data-serv');
            var ecales_id = this_.attr('data-id');
            var qty = this_.val();
            if (keycode == 13) {
                //alert(item_id + ' - ' + supp_id + ' - ' + amt);
                $.ajax({
                    url: base_url + 'analysis/updateecalesservice',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        type: 'days',
                        sysid : sys_id,
                        ecalesid : ecales_id,
                        days : parseFloat(qty.replace(',','')),
                    }
                }).done(function (e) {
                    PECO.initAlerts(e.msg,'ECALES',e.func);
                    this_.val(e.days);
                });
            }
        });

        tbl_ecales_list.on('keyup','#item_price, #item_qty',function () {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var amt =  parseFloat($('td.amt input', this_row).val());
            var qty =  parseFloat($('td.quantity input', this_row).val());
            var total = amt * qty;
            $('td.total', this_row).text(total.toLocaleString('en', {minimumFractionDigits: 2}));
            //alert(amt + ' - ' + qty);
            ecales_totals();
        });

        tbl_ecales_service.on('keyup','#service_rate, #no_days',function () {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var rate =  parseFloat($('td.serv_rate input', this_row).val().replace(',',''));
            var days =  parseFloat($('td.serv_days input', this_row).val());
            var total = rate * days;

            $('td.servtotal_amt', this_row).text(total.toLocaleString('en', {minimumFractionDigits: 2}));
            //alert(amt + ' - ' + qty);
            ecales_serv_totals();
        });

        tbl_ecales_list.on('ifChanged','#customer_provided',function () {
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            var this_ecales = this_.attr('data-ecales');
            var this_status = this_.attr('checked');
            var checked = 0;
            var ecales_cust_amt = $('#ecales_cust_amt',document);
            var ecales_peco_amt = $('#ecales_peco_amt',document);
            var ecales_cust_qty = $('#ecales_cust_qty',document);
            var ecales_peco_qty = $('#ecales_peco_qty',document);
            var ecales_amt_total = $('#ecales_amt_total',document);
            // For some browsers, `attr` is undefined; for others,
            // `attr` is false.  Check for both.
            if (this_.is(':checked')) {
                checked = 1;
            }

            $.ajax({
                url: base_url + 'analysis/changeecalespayable',
                type: 'post',
                dataType: 'json',
                data: {
                    trnid : this_id,
                    checked : checked,
                    ecalesid : this_ecales
                }
            }).done(function (d) {
                PECO.initAlerts(d.msg,'ECALES: Customer Provided',d.func);
                ecales_cust_amt.text(d.custamt);
                //ecales_peco_amt.text(d.pecoamt);
                ecales_cust_qty.text(d.custqty);
                //ecales_peco_qty.text(d.pecoqty);
                ecales_amt_total.text(d.totalamt);
                init_ecales_summary(this_ecales);
            }).fail(function () {
                PECO.initAlerts('Failed to change CP status','ECALES: Customer Provided','error');
                if (this_status) {
                    this_.checked = false;
                } else {
                    this_.checked = true;
                }
            });
        });

        $(document).on('submit', '#frm_process_ecales', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: 'Process ECALES',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (d) {
                        swal('Process ECALES', d.msg, d.func);
                        for (var i = 0 ; d.charges.length > i ; i++) {
                            PECO.initAlerts(d.charges[i].msg,'Inventory Charges',d.charges[i].func);
                        }
                    }).fail(function() {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('keyup', '.input-entry input', function () {
            var this_ = $(this);
            var item_amts = text_lastprice.text().replace(',', '');
            var item_qty = $('#input_qty', document).val();

            if(this_.val() != '') {
                text_itemtotal.text(Number(item_amts) * Number(item_qty)).number(true, 2);
            } else {
                item_id.val('');
                item_desc.val('N/A');
                text_lastprice.text('N/A');
                text_lastdate.text('N/A');
                text_itemtotal.text('N/A');
            }
        });

        $(document).on('change', '.input-entry input', function () {
            var this_ = $(this);
            var item_amts = text_lastprice.text().replace(',', '');
            var item_qty = $('#input_qty', document).val();

            if(this_.val() != '') {
                text_itemtotal.text(Number(item_amts) * Number(item_qty)).number(true, 2);
            } else {
                item_id.val('');
                item_desc.val('N/A');
                text_lastprice.text('N/A');
                text_lastdate.text('N/A');
                text_itemtotal.text('N/A');
            }
        });

        $(document).on('change', '#svcs_days', function () {
            var this_ = $(this);
            var svcs_amts = svcs_lastprice.text().replace(',', '');
            var svcs_qty = this_.val();

            if(this_.val() != '') {
                svcs_itemtotal.text(Number(svcs_amts) * Number(svcs_qty)).number(true, 2);
            } else {
                svcs_id.val('');
                svcs_lastprice.text('N/A');
                svcs_lastdate.text('N/A');
                svcs_itemtotal.text('N/A');
            }
        });

        $(document).on('keyup', '.input-entry #svcs_days', function () {
            var this_ = $(this);
            var svcs_amts = svcs_lastprice.text().replace(',', '');
            var svcs_qty = this_.val();

            if(this_.val() != '') {
                svcs_itemtotal.text(Number(svcs_amts) * Number(svcs_qty)).number(true, 2);
            } else {
                svcs_id.val('');
                svcs_lastprice.text('N/A');
                svcs_lastdate.text('N/A');
                svcs_itemtotal.text('N/A');
            }
        });

        $('#tbl_ecales').on('click', '#del_btn', function (e) {
            var btn = $(this);
            var url = btn.attr('href');
            var id = btn.attr('data-id');
            var title = btn.attr('title');
            e.preventDefault();
            var conf = confirm('Delete this item?');
            if(conf == true) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {'id': id},
                    dataType: 'json',
                    beforeSend: function () {
                        btn.removeClass(btn.attr('btn-default')).addClass('btn-default').find('.fa').addClass('fa-spinner fa-spin fa-pulse');
                    }
                }).done(function (data) {
                    if (data.qry == true) {
                        PECO.initAlerts(data.msg, btn.attr('title'), 'success');
                        btn.removeClass('btn-default').addClass(btn.attr('btn-success')).find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-check');
                        init_ecales_list(ecalesid);
                        init_ecales_summary(ecalesid);
                    } else {
                        PECO.initAlerts(data.msg, btn.attr('title'), 'warning');
                        btn.removeClass('btn-default').addClass(btn.attr('btn-warning')).find('.fa').removeClass('fa-spinner fa-spin fa-palse').addClass('fa-warning');
                    }
                }).fail(function () {
                    PECO.phpError();
                    btn.removeClass('btn-default').find('.fa').removeClass('fa-spinner fa-spin fa-pulse');
                });
            }
        });

        $('#tbl_ecales_service').on('click', '#del_btn', function (e) {
            var btn = $(this);
            var url = btn.attr('href');
            var id = btn.attr('data-id');
            var title = btn.attr('title');
            e.preventDefault();
            var conf = confirm('Delete this item?');
            if(conf == true) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {'id': id},
                    dataType: 'json',
                    beforeSend: function () {
                        btn.removeClass(btn.attr('btn-default')).addClass('btn-default').find('.fa').addClass('fa-spinner fa-spin fa-pulse');
                    }
                }).done(function (data) {
                    if (data.qry == true) {
                        PECO.initAlerts(data.msg, btn.attr('title'), 'success');
                        btn.removeClass('btn-default').addClass(btn.attr('btn-success')).find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-check');
                        init_ecales_service_list(ecalesid);
                        init_ecales_summary(ecalesid);
                    } else {
                        PECO.initAlerts(data.msg, btn.attr('title'), 'warning');
                        btn.removeClass('btn-default').addClass(btn.attr('btn-warning')).find('.fa').removeClass('fa-spinner fa-spin fa-palse').addClass('fa-warning');
                    }
                }).fail(function () {
                    PECO.phpError();
                    btn.removeClass('btn-default').find('.fa').removeClass('fa-spinner fa-spin fa-pulse');
                });
            }
        });

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/itemsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), item_search.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "desc",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{code}}</b></h5>', "<p>{{desc}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            item_id.val(selection.id);
            item_desc.val(selection.desc);
            text_lastprice.text(selection.amts_text);
            text_lastdate.text(selection.date);
            text_itemtotal.text(Number(selection.amts * Number(item_qty.val()))).number(true, 2);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });

        var b = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/servicesearch?query=%QUERY", wildcard: "%QUERY"}
        });

        b.initialize(), svcs_search.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "desc",
            source: b.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile([
                    '<div class="media">',
                    '<div class="pull-left">',
                    '<div class="media-object">',
                    '<img src="{{img}}" width="50" height="50"/>',
                    "</div>",
                    "</div>",
                    '<div class="media-body">',
                    '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{code}}</b></h5>',
                    "<p>{{desc}}</p>",
                    "</div>",
                    "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            svcs_id.val(selection.id);
            svcs_lastprice.text(selection.amts_text);
            svcs_lastdate.text(selection.date);
            svcs_itemtotal.text(Number(selection.amts * Number(svcs_days.val()))).number(true, 2);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });

        $(document).on('click','#add_ecales_template',function () {
            swal({
                title: "Save as Template?",
                text: "Template Name",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Please provide a name for this template."
            }, function (inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("You need to provide a template name in order to proceed!");
                    return false
                } else {
                    $.ajax({
                        url: base_url + 'analysis/saveecalestemplate',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            ecalesid : ecalesid,
                            name : inputValue
                        }
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                    }).fail(function () {
                        swal("Error!", "ECALES template not saved!", "error");
                    })
                }
            });
        });

        // PECO.select2Basic($('#item_select'), 'analysis/getitemselect', 'Items..', true);
        //PECO.select2Basic($('#rate_class_select'), 'analysis/initrateclasslist', 'Rate Class..');

        init_ecales_list(ecalesid);
        init_touchspin_qty();
        init_ecales_service_list(ecalesid);
        init_ecales_summary(ecalesid);
        init_ecales_logs(appid);

    };


    var init_ecales_list = function (ecalesid) {
        $.ajax({
            url: PECO.base_url() + 'analysis/getcustomerecalestable',
            type: 'post',
            data: {'ecalesid': ecalesid},
            dataType: 'json',
        }).done(function (data) {
            $('#ecales_amt_total').text(data.totalamt);
            $('#ecales_amt_qty').text(data.totalqty);
            $('#ecales_number').text(data.ecalesnum);
            $('#ecales_cust_amt').text(data.custamt);
            $('#ecales_cust_qty').text(data.custqty);
            tbl_ecales_list.dataTable({
                bDestroy: true,
                bPaginate: false,
                bLengthChange: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                //scrollY: '260px',
                language: PECO.DTEmptyMessage('No ECALES item(s) yet!'),
                aaData: data.list,
                aoColumns: [
                    {"data": "num", sClass: 'number text-danger', sWidth: ''},
                    {"data": "item", sClass: 'text-bold', sWidth: '50%'},
                    {"data": "amt", sClass: 'amt number font-red-flamingo', sWidth: ''},
                    {"data": "qty", sClass: 'number quantity', sWidth: '80px'},
                    {"data": "stock", sClass: ' stock', sWidth: ''},
                    {"data": "total", sClass: 'total number text-info text-bold', sWidth: ''},
                    {"data": "person", sClass: 'text-align-center', sWidth: 'controls relative'},
                    {"data": "control", sClass: 'text-align-center', sWidth: 'controls'},
                ],
                searchHighlight: true,
                fnRowCallback(nRow, aData, i) {
                    $('.icheck', nRow);
                    PECO.iCheckRow($('.icheck', nRow), 'minimal', 'blue');
                }
            });
            PECO.dataTableScroller();
            //PECO.initSlimScroll('.dataTables_scrollBody');
        }).fail(function () {
            PECO.DTphpError(tbl_ecales_list);
        });
    };

    var init_ecales_service_list = function (ecalesid) {
        var ecales_service_amt = $('#ecales_service_amt',document);
        var ecales_service_days = $('#ecales_service_days',document);
        $.ajax({
            url: PECO.base_url() + 'analysis/getcustomerecalesservices',
            type: 'post',
            data: {'ecalesid': ecalesid},
            dataType: 'json',
        }).done(function (data) {
            tbl_ecales_service.dataTable({
                bDestroy: true,
                bPaginate: false,
                bLengthChange: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                //scrollY: '260px',
                language: PECO.DTEmptyMessage('No ECALES service(s) yet!'),
                aaData: data.list,
                aoColumns: [
                    {"data": "num", sClass: 'number text-danger', sWidth: ''},
                    {"data": "service", sClass: 'text-bold', sWidth: '350px'},
                    {"data": "rate", sClass: 'serv_rate number font-red-flamingo', sWidth: ''},
                    {"data": "days", sClass: 'serv_days number text-info text-bold', sWidth: ''},
                    {"data": "total", sClass: 'servtotal_amt number text-info text-bold', sWidth: ''},
                    {"data": "control", sClass: 'text-align-center', sWidth: 'controls'},
                ],
                searchHighlight: true,
            });
            ecales_service_amt.text(data.totalcost);
            //PECO.dataTableScroller();
            //PECO.initSlimScroll('.dataTables_scrollBody');
        }).fail(function () {
            PECO.DTphpError(tbl_ecales_list);
        });
    };

    var init_touchspin_qty = function () {
        $("#touchspin_item_qty").TouchSpin({
            buttondown_class: 'btn red',
            buttonup_class: 'btn green',
            min: 1,
            max: 1000000000,
            stepinterval: 1,
            maxboostedstep: 10000000,
            prefix: 'Qty',
            verticalbuttons: false
        });
    };

    var init_compute_ecales_total = function (this_) {
        var amts = $('#lastprice').text().replace(',', '');
        var qty = this_.val();
        var amttotal = Number(amts) * Number(qty);
        $('#itemtotal').html(amttotal.toLocaleString());
    };

    var ecales_totals = function () {
        var sum_total = 0; // iterate through each td based on class and add the values
        var sum_qty = 0;

        $('.total').each(function () {
            var total_val = $(this).text();
            // add only if the value is number
            if(!isNaN(total_val) && total_val.length != 0) {
                sum_total += parseFloat(total_val);
            }
        });

        $('.item_qty').each(function (i,e) {
            var qty_val = parseInt($(e).val());
            // add only if the value is number
            if(!isNaN(qty_val)) {
                sum_qty += qty_val;
            }
        });

        $('#ecales_amt_total',document).text(sum_total);
        $('#ecales_amt_qty',document).text(sum_qty);
    };

    var ecales_serv_totals = function () {
        var sum_serv_total = 0;
        $('.servtotal_amt').each(function () {
            var total_val = $(this).text().replace(',','');
            if(!isNaN(total_val) && total_val.length != 0) {
                sum_serv_total += parseFloat(total_val);
            }
        });

        $('#ecales_service_amt',document).text(sum_serv_total.toLocaleString('en', {minimumFractionDigits: 2}));
    };

    var init_ecales_summary = function (ecalesid) {
        $.ajax({
            url: PECO.base_url() + 'analysis/getecalessummary',
            type: 'post',
            data: {'ecalesid': ecalesid},
            dataType: 'json',
        }).done(function (data) {
            tbl_ecales_summary.dataTable({
                bDestroy: true,
                bPaginate: true,
                bLengthChange: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                //scrollY: '260px',
                language: PECO.DTEmptyMessage('No ECALES item(s) yet!'),
                aaData: data.list,
                aoColumns: [
                    {"data": "num", sClass: 'number text-danger', sWidth: ''},
                    {"data": "type", sClass: 'text-bold', sWidth: ''},
                    {"data": "item", sClass: 'text-bold', sWidth: ''},
                    {"data": "amt", sClass: 'amt number font-red-flamingo', sWidth: ''},
                    {"data": "qty", sClass: 'number quantity text-align-center', sWidth: ''},
                    {"data": "unit", sClass: 'text-info text-bold', sWidth: ''},
                    {"data": "total", sClass: 'total number text-info text-bold', sWidth: ''},
                    {"data": "paidby", sClass: 'text-align-center', sWidth: 'controls'},
                ],
                searchHighlight: true,
            });
            PECO.dataTableScroller();
            //PECO.initSlimScroll('.dataTables_scrollBody');
            $('#summary_util_amt',document).text(data.summary_util_amt);
            $('#summary_util_vat',document).text(data.summary_util_vat);
            $('#summary_util_total',document).text(data.summary_util_total);
            $('#summary_items_amt',document).text(data.summary_items_amt);
            $('#summary_items_vat',document).text(data.summary_items_vat);
            $('#summary_items_total',document).text(data.summary_items_total);
            $('#summary_total_amt',document).text(data.summary_total_amt);
            $('#summary_total_vat',document).text(data.summary_total_vat);
            $('#summary_grand_total',document).text(data.summary_grand_total);
        }).fail(function () {
            PECO.DTphpError(tbl_ecales_list);
        });
    };

    var init_revoke_ecales = function (ecalesid) {
        init_ecales_info(ecalesid);

        $(document).on('submit','#revoke_ecales',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Revoke ECALES?",
                text: "Current ECALES details will be removed.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Revoke ECALES!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        dataType: 'json',
                        data: this_.serialize()
                    }).done(function (d) {
                        swal('Revoke ECALES',d.msg,d.func);
                        location.reload();
                    })
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });

        });
    };

    var init_ecales_info = function (ecalesid) {
        $.ajax({
            url: base_url + 'analysis/getecalesinfo',
            type: 'post',
            dataType: 'json',
            data: {
                id : ecalesid
            }
        }).done(function (d) {
            $('#total_load').text(d.totalload);
            $('#total_amt').text(d.totalcost);
            $('#total_qty').text(d.totalqty);
            $('#ecales_remarks').css('word-break','break-all').text(d.remarks);
        })
    };

    var init_ecales_logs = function (appid) {
        var tbl_revoked_ecales_logs = $('#tbl_revoked_ecales_logs',document);
        PECO.dtSubDetails(tbl_revoked_ecales_logs, 'analysis/getecalessubdetails');

        $.ajax({
            url: base_url + 'analysis/ecalesrevokedlogs',
            type: 'post',
            dataType: 'json',
            data: {
                appid : appid
            }
        }).done(function (d) {
            tbl_revoked_ecales_logs.dataTable({
                bDestroy: true,
                bPaginate: true,
                bLengthChange: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                //scrollY: '260px',
                language: PECO.DTEmptyMessage('No ECALES has been revoked for this application.'),
                aaData: d.list,
                aoColumns: [
                    {"data": "expand", sClass: 'withsub text-align-center text-danger', sWidth: ''},
                    {"data": "totalload", sClass: '', sWidth: '10%'},
                    {"data": "totalcost", sClass: 'number', sWidth: '10%'},
                    {"data": "totalqty", sClass: 'text-align-center', sWidth: '10%'},
                    {"data": "remarks", sClass: '', sWidth: '150px'},
                    {"data": "indcharge", sClass: 'text-info text-bold', sWidth: '150px'},
                    {"data": "reason", sClass: 'text-info text-bold', sWidth: '150px'},
                    {"data": "attachment", sClass: 'text-align-center', sWidth: '5%'},
                ],
                searchHighlight: true,
            });
        })
    };

    var init_ecales_templates = function (ecalesid) {
        dt_ecales_templates(ecalesid);

        $(document).on('click','#btn_apply_ecales_template',function () {
            var this_ = $(this);
            var templateid = this_.attr('data-id');

            swal({
                title: "Load ECALES Template?",
                text: 'Current ECALES items will be overwritten.',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                confirmButtonText: "Load Template",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: base_url + 'analysis/applytemplate',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            templateid : templateid,
                            ecalesid : ecalesid,
                        }
                    }).done(function (d) {
                        swal('ECALES Template',d.msg,d.func);
                        init_ecales_list(ecalesid);
                        init_ecales_service_list(ecalesid);
                        init_ecales_summary(ecalesid);
                    }).fail(function () {
                        swal('ECALES Template', 'Script Error!', 'error');
                    });
                }else{
                    swal.close();
                }
            });

        });

    };

    var dt_ecales_templates = function (ecalesid) {
        PECO.dtSubDetails(tbl_ecales_templates, 'analysis/getecalestemplatedetails');
        $.ajax({
            url: base_url + 'analysis/dtecalestemplates',
            type: 'post',
            dataType: 'json',
            data: {
                ecalesid: ecalesid
            },
            beforeSend: function () {
                PECO.DTphpLoading(tbl_ecales_templates, 'Fetching ECALES Templates...');
            }
        }).done(function (d) {
            tbl_ecales_templates.dataTable({
                bDestroy: true,
                bPaginate: true,
                bLengthChange: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                //scrollY: '260px',
                language: PECO.DTEmptyMessage('No saved template.'),
                aaData: d.list,
                aoColumns: [
                    {"data": "expand", sClass: 'withsub text-align-center text-danger expand', sWidth: '10px'},
                    {"data": "name", sClass: '', sWidth: '10%'},
                    {"data": "desc", sClass: '', sWidth: '200px'},
                    {"data": "control", sClass: 'text-align-center', sWidth: '10px'},
                ],
                searchHighlight: true,
            });
        });
    };

    return {
        analysis: function (ecalesid,appid) {
            init_analysis(ecalesid,appid);
        },
        revoke: function (ecalesid) {
            init_revoke_ecales(ecalesid);
        },
        templates: function (ecalesid) {
            init_ecales_templates(ecalesid);
        }
    }
}(jQuery);
