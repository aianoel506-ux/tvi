var MTS = function() {
    var input_date_ret = $('#input_date_ret', document);
    var tbl_mts_rdg = $('#tbl_mts_rdg', document);

    var fn_mts_handler = function() {
        PECO.getSelect2Plugins();
        PECO.getHighlightsPlugin();
        PECO.meterSearchForm();

        tbl_mts_readigns();

        input_date_ret.change(function() {
            tbl_mts_readigns();
        });

        PECO.dtSubDetails(tbl_mts_rdg, 'assets/mtsreadingdetails');
        $(document).on('submit','#frm_save_mts_reading',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                dataType: 'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                tbl_mts_readigns();
            }).fail(function () {
                PECO.phpError();
            });
        });

        var acct = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/accountsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        acct.initialize(), acctsearch.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "text",
            source: acct.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{pics}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{text}}</b>, {{name}}</h5>', "<p>{{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

            $('#acctid', document).val(selection.id);
            $.ajax({
                url: PECO.base_url() + 'peco/getaccountinfo',
                type: 'post',
                data: {'id': selection.id},
                dataType: 'json',
            }).done(function(d) {

                $('#jo_acct_name', document).html(d.name);
                $('#jo_acct_addr', document).html(d.addr);
                $('#jo_acct_mtrno', document).html(d.mtrno);
                $('#jo_acct_serial', document).html(d.serial);

            });
        });
    };

    var tbl_mts_readigns = function() {
        var date_ret_val = input_date_ret.val();

        $('#reading_date_text', document).text(date_ret_val);
        $.ajax({
            url : PECO.base_url() + 'assets/tblmtsreading',
            type: 'post',
            data: {'datereturned': date_ret_val},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_mts_rdg, 'Loading reading returned as of : ' + date_ret_val);
            }
        }).done(function(d) {
            tbl_mts_rdg.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                pageLength: 20,
                saveState: true,
                aoColumns: [
                    {"data": "expand", sWidth: '', sClass: 'text-align-center'},
                    {"data": "mtrno", sWidth: '', sClass: ''},
                    {"data": "serial", sWidth: '', sClass: ''},
                    {"data": "servno", sWidth: '', sClass: ''},
                    {"data": "ownername", sWidth: '', sClass: ''},
                    {"data": "reading", sWidth: '', sClass: ''},
                    {"data": "encodedby", sWidth: '', sClass: ''},
                    {"data": "encodeddate", sWidth: '', sClass: ''},
                    ],
                searchHighlight: true,
                "language": PECO.DTEmptyMessage(d.msg)
            });
        });
    };

    return {
        init: function() {
            fn_mts_handler();
        }
    }
}();