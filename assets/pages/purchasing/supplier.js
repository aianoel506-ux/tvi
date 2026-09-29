var SUPPLIER = function () {
    PECO.getHighlightsPlugin();

    var handler_search_supplier = function() {
        var item_category = $('#item_category_search', document);
        var supp_category = $('#item_supplier_search', document);
        var supplier_branch = $('#supplier_branch', document);
        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/itemcategory?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), item_category.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<i class="fa fa-tag"></i>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{codes}}</b>, {{names}}</h5>', "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };

    var handler_search_vendor = function() {
        var item_category = $('#item_category_search', document);
        var supp_category = $('#item_supplier_search', document);
        var supplier_branch = $('#supplier_branch', document);
        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/itemcategory?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), item_category.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<i class="fa fa-tag"></i>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{codes}}</b>, {{names}}</h5>', "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };


    var tbl_supplier = function () {
        var tbl_supplier = $('#tbl_supplier', document);

        var date_start = $('#input_date_start', document).val();
        var date_end = $('#input_date_start', document).val();

        $.ajax({
            url: PECO.base_url() + 'purchasing/tblsuppliers',
            type: 'post',
            data: {datestart: date_start, dateend: date_end},
            dataType: 'json',
            beforeSend: function () {
                PECO.DTphpLoading(tbl_supplier, 'Loading assets lists..');
            }
        }).done(function (d) {
            tbl_supplier.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "expand", sWidth: '20px', sClass: ''},
                    {"data": "name", sWidth: '', sClass: ''},
                    {"data": "address", sWidth: '', sClass: ''},
                    {"data": "phone", sWidth: '', sClass: ''},
                    {"data": "email", sWidth: '', sClass: ''},
                    {"data": "products", sWidth: '', sClass: ''},
                    {"data": "purchasedqty", sWidth: '', sClass: ''},
                    {"data": "purchasedamt", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.DTphpError(tbl_supplier);
        });
    };
    var init_list = function () {
        tbl_supplier();
        $(document).on('click', '.btn-refresh', function (e) {
            e.preventDefault();
            tbl_supplier();
        });
    };
    return {
        list: function () {
            init_list();
        }
    }
}();
