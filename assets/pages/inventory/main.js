var INVENTORY = function() {

    PECO.getHighlightsPlugin();
    PECO.getDataTablePlugin();
    PECO.getSelect2Plugins();

    var init_products = function() {

        tbl_products();
        $(document).on('click', '#btn_product_refresh', function(e) {
            e.preventDefault();
            tbl_products();
        });

        handler_tbl_stocks();

        $(document).on('click', '#btn_refresh_stocks', function(e) {
            e.preventDefault();
            handler_tbl_stocks();
        });

        $(document).on('submit', '#frm_add_stock_item', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: 'Save stock items',
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
                        type: 'post',
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg,d.title,d.func);
                        swal.close();
                        handler_tbl_stocks();
                    }).fail(function() {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });


        $(document).on('submit', '#frm_stock_out', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                PECO.initAlerts(d.msg, d.title, d.func);
                handler_tbl_stocks();
            });
        });

        $(document).on('keyup', '#search_stockout_code', function(e) {
            handler_stockout_query();
        });
    };


    var handler_stockout_query = function() {
        var codes = $('#search_stockout_code', document).val();
        $.ajax({
            url: PECO.base_url() + 'inventory/querystockout',
            type: 'post',
            data: {'codes': codes},
            dataType: 'json',
        }).done(function(d) {
            if(d.qry) {
                $('#stockout_text_desc', document).text(d.desc);
                $('#stockout_text_stocks', document).text(d.qty);
                $('#stock_out_stat', document).text('');
            } else {
                $('#search_stockout_code', document).val('');
                $('#stock_out_stat', document).text(d.msg);
            }
        });
    };



    var handler_tbl_stocks = function() {
        var tbl_stocks_ = $('#tbl_stocks', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblstocks',
            type: 'post',
            data: {},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_stocks_, 'Loading stocks...');
            }
        }).done(function(d) {
            tbl_stocks_.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No stocks in inventory yet!'),
                aoColumns: [
                    {"data": "expand", sWidth: '20px', sClass: 'danger number'},
                    {"data": "stockid", sWidth: '', sClass: 'bold number'},
                    {"data": "storage", sWidth: '40px', sClass: 'text-info'},
                    {"data": "supplier", sWidth: '', sClass: 'text-primary'},
                    {"data": "product", sWidth: '120px', sClass: ''},
                    {"data": "brand", sWidth: '', sClass: 'font-yellow'},
                    {"data": "qty", sWidth: '', sClass: 'number td-zoom'},
                    {"data": "requested", sWidth: '', sClass: 'number td-zoom'},
                    {"data": "released", sWidth: '', sClass: 'number td-zoom'},
                    {"data": "return", sWidth: '', sClass: 'number td-zoom'},
                    {"data": "onhand", sWidth: '', sClass: 'bold number td-zoom'},
                    {"data": "price", sWidth: '', sClass: 'text-danger number'},
                    {"data": "unit", sWidth: '', sClass: 'number'},
                    {"data": "purchasedate", sWidth: '', sClass: 'number'},
                    {"data": "status", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    $(nRow).addClass(aData.rowbg);
                    PECO.dtExpandBtn($(nRow), aData.num);
                    PECO.popOverRow($('.popovers', nRow), true, true, 'popover-info')
                }
            });
        });
    };

    var search_handler = function() {

        var item_search = $('#item_search', document);
        var item_id = $('#item_id', document);
        var item_desc = $('#item_desc', document);
        var text_lastdate = $('#text_lastdate', document);

        var text_lastprice = $('#text_lastprice', document);
        var text_itemtotal = $('#text_itemtotal', document);

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
            // text_lastprice.text(selection.amts_text);
            text_lastdate.text(selection.date);
            // text_itemtotal.text(Number(selection.amts * Number(item_qty.val()))).number(true, 2);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });



        var search_text_supplier = $('#search_text_supplier', document);
        var supp_id = $('#supplier_id', document);


        var b = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/suppliers?query=%QUERY", wildcard: "%QUERY"}
        });

        b.initialize(), search_text_supplier.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: b.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile([
                    '<div class="media">',
                    '<div class="pull-left">',
                    '<div class="media-object">',
                    '<img src="{{picture}}" width="50" height="50"/>',
                    "</div>",
                    "</div>",
                    '<div class="media-body">',
                    '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{names}}</b></h5>',
                    "<p>{{address}}</p>",
                    "</div>",
                    "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            supp_id.val(selection.id);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });

        PECO.select2Types($('#select2brands', document), 'ITEMBRANDS', 'Brand...');
    };

    var tbl_products = function() {
        var tbl_products = $('#tbl_products', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblproducts',
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_products, 'Loading products...');
            }
        }).done(function(d) {
            tbl_products.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: ''},
                    {"data": "supplier", sWidth: '', sClass: ''},
                    {"data": "product", sWidth: '', sClass: ''},
                    {"data": "brand", sWidth: '', sClass: ''},
                    {"data": "qty", sWidth: '', sClass: 'number'},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(tbl_products);
        });
    };


    var init_inventory = function() {
        $('table.types', document).each(function() {
            var table = $(this);
            init_tbl_initialization(table, table.attr('data-code'), table.attr('data-title'));
        });

        $(document).on('click', '.btn-refresh', function() {
            var this_ = $(this);
            var this_portlet = this_.closest('.portlet');
            var table = $('table.types', this_portlet);
            init_tbl_initialization(table, table.attr('data-code'), table.attr('data-title'));
        });

        $(document).on('submit', '#frm_add_types', function(e) {
            e.preventDefault();
            var form = $(this);
            var model_content = form.closest('#modal_ajax');
            var modal_title = $('#modal_title', model_content);
            var msgtitle = modal_title.text();

            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + msgtitle + "</span>",
                content: 'Please confirm action taken',
                buttons: '[Yes][No]',
                buttonsPosition: 'right',
                buttonClass: 'btn-primary, btn-danger',
                buttonsIcon: 'fa-angle-double-right, fa-times',
                inputIcon: 'fa fa-user',
                inputIconPosition: 'left',
            },function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (data) {

                        PECO.initAlerts(data.msg, msgtitle, data.func);
                        var _portlet = $('#' + data.table).closest('.portlet ');
                        $('.btn-refresh', _portlet).trigger('click');

                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        $('#inventory_tab a[data-toggle="tab"]', document).on('shown.bs.tab', function (e) {
            var this_ = $(this);
            var this_href = this_.attr('href').replace('#', '');
            if(this_href == 'suppliers') {
                handler_tbl_suppliers();
            }
        });



        $(document).on('submit', '#frm_stock_in', function(e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                if(d.qry) {
                    tbl_stocks_in_list();
                }else{
                    PECO.initAlerts(d.msg, 'Error', d.func);
                }
            });
        });

        PECO.dtSubDetails($('#tbl_stocks', document), 'inventory/stockdetails');


        $(document).on('submit', '#frm_generate_codes', function(e) {
            e.preventDefault();
            var form = $(this);
            generate_barcode_html(form, 0);
        });

        $(document).on('click', '#btn_print_codes', function(e) {
            e.preventDefault();
            var form = $('#frm_generate_codes', document);
            generate_barcode_html(form, 1);
        });
    };

    var generate_barcode_html = function(form, type) {
        var barcode_content = $('#barcode_content', document);
        var input_stockid = $('#select2stock', document).val();
        var input_codestart = $('#input_codestart', document).val();
        var input_codecount = $('#input_codecount', document).val();
        $.ajax({
            url: form.attr('action'),
            data: {stockid: input_stockid, codestart: input_codestart, codecount: input_codecount, type: type},
            type: 'post',
            dataType: 'json',
            beforeSend: function () {
                barcode_content.html('<h3><i class="fa fa-circle-o-notch fa-spin"></i> Loading preview items...</h3>');
            }
        }).done(function (d) {
            if(type == 1) {
                PECO.pecoRepPrint('Barcode', d.html);
            }
            barcode_content.html(d.msg);
        }).fail(function(e) {
            PECO.phpError();
            barcode_content.html('<h3><i class="fa fa-times text-danger"></i> PHP Error!</h3>');
        });
    }

    var handler_tbl_suppliers = function() {
        var tbl_supplier = $('#tbl_supplier', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblsuppliers',
            type: 'popst',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_supplier, 'Loading suppliers...');
            }
        }).done(function(d) {
            tbl_supplier.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: ''},
                    {"data": "name", sWidth: '', sClass: ''},
                    {"data": "address", sWidth: '', sClass: ''},
                    {"data": "email", sWidth: '', sClass: ''},
                    {"data": "telephone", sWidth: '', sClass: ''},
                    {"data": "cellphone", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(tbl_supplier);
        });
    };

    var init_tbl_initialization = function(el, code, msg) {
        $.ajax({
            url: PECO.base_url() + 'inventory/tblgetdatainit',
            data: {codes: code},
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(el, msg);
            }
        }).done(function(d) {
            el.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                searching: false,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "expand", sWidth: '20px', sClass: ''},
                    {"data": "codes", sWidth: '', sClass: ''},
                    {"data": "descs", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(el);
        });
    };

    var stocks_handler = function(dataid) {
        tbl_stocks_list(dataid);
    };

    var tbl_stocks_list = function(dataid) {
        tbl_stocks_in_list(dataid);
    };

    var stocks_entry_window = function() {

        setTimeout(function() {

            PECO.select2Basic($('#select2stock', document), 'assets/select2stocks', 'Select stock...', true, false, false, true);


            PECO.DTDefault($('#tbl_stocks_in_list', document), 'Select stock!');
            $(document).on('change', '#select2stock', function(e) {
                tbl_stocks_in_list();
                $('#search_text', document).focus();
            });
        }, 500);



        $(document).on('submit', '#frm_scan_entry', function(e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                if(d.qry) {
                    tbl_stocks_in_list();
                }else{
                    PECO.initAlerts(d.msg, 'Error', d.func);
                }
            });
        });

    };

    var stocks_in_hanlder = function() {
        PECO.select2Basic($('#select2stock', document), 'assets/select2stocks', 'Select stock...', true, false, false, true);

        PECO.DTDefault($('#tbl_stocks_in_list', document), 'Select stock!');
        $(document).on('change', '#select2stock', function(e) {
            tbl_stocks_in_list();
        });

        $(document).on('click', '#btn_stock_in_save', function(e) {
            var stockid = $('#select2stock', document).val();
            if(stockid>0) {
                swal({
                    title: "Are you sure?",
                    text: 'Save stock items',
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
                            url: PECO.base_url() + 'inventory/savestockin',
                            type: 'post',
                            data: {stockid: stockid},
                            dataType: 'json'
                        }).done(function (d) {
                            PECO.initAlerts(d.msg,d.title,d.func);
                            swal.close();
                            tbl_stocks_in_list();
                        }).fail(function() {
                            PECO.phpError();
                            swal.close();
                        });
                    }else{
                        swal.close();
                    }
                });
            }
        });



    };


    var tbl_stocks_list = function(dataid) {
        var tbl_stocks_in_list = $('#tbl_stocks_in_list', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblgetstockin',
            data: {stockid: dataid, status: 304},
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_stocks_in_list, 'Loading encoding...');
            }
        }).done(function(d) {
            tbl_stocks_in_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: ''},
                    {"data": "serials", sWidth: '', sClass: ''},
                    {"data": "date", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(el);
        });
    };

    var tbl_stocks_in_list = function() {
        var stockid = $('#select2stock', document).val();
        var tbl_stocks_in_list = $('#tbl_stocks_in_list', document);
        if(stockid > 0) {
            $.ajax({
                url: PECO.base_url() + 'inventory/tblgetstockin',
                data: {stockid: stockid},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    PECO.DTphpLoading(tbl_stocks_in_list, 'Loading encoding...');
                }
            }).done(function (d) {
                tbl_stocks_in_list.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: true,
                    bStateSave: true,
                    aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                    language: PECO.DTEmptyMessage('No data yet'),
                    aoColumns: [
                        {"data": "num", sWidth: '20px', sClass: ''},
                        {"data": "serials", sWidth: '', sClass: ''},
                        {"data": "date", sWidth: '', sClass: ''},
                        {"data": "status", sWidth: '', sClass: ''},
                        {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                    ],
                    searchHighlight: true
                });
            }).fail(function () {
                PECO.DTphpError(el);
            });
        } else {

            PECO.DTphpLoading(tbl_stocks_in_list, 'Loading encoding...');
            PECO.DTDefault(tbl_stocks_in_list, 'Select stock!');
        }
    };

    var stock_generate_code = function() {
        PECO.select2Basic($('#select2stock', document), 'assets/select2stocks', 'Select stock...', true, false, false, true);


    };

    return {
        init: function() {
            init_inventory();
        }, products: function() {
            init_products();
        }, search: function() {
            search_handler();
        }, stocks: function(dataid) {
            stocks_handler(dataid);
        }, stocksin: function() {
            stocks_in_hanlder();
        }, stockinentry: function() {
            stocks_entry_window();
        }, stockgeneratecode: function() {
            stock_generate_code();
        }
    }
}();