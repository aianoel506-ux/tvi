var REFERRALS = function() {

    PECO.getHighlightsPlugin();


    var fn_init_handler = function(dataid) {
        handler_ref_query(dataid, 1);

        $('#select2reftype', document).select2({
            allowClear: true,
            width: '100%'
        }).change(function(e) {
            handler_ref_query(dataid, 2);
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val == 1) {
                $('#input_ref_amount', document).attr('disabled', false);
                $('#input_ref_amount', document).focus();
            } else {
                $('#input_ref_amount', document).attr('disabled', true);
                $('#input_ref_payable_cnt', document).focus();
            }
        });

        $(document).on('change, keyup', '#refinput input', function(e) {
            handler_ref_query(dataid, 2);
        });

        PECO.select2Basic($('#input_ref_months', document), 'systems/select2month', 'Select month..');
        $('#input_ref_months', document).change(function(e) {
            handler_ref_query(dataid, 2);
        });

    };

    var handler_ref_query = function(dataid, query) {
        var reftype = $('#select2reftype', document).val();
        var form = $('#frm_save_referrals', document);
        $.ajax({
            url: PECO.base_url() + 'cad/getrefdetails',
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
        }).done(function(d) {
            if(query == 1) {
                $('#input_ref_amount', document).val(d.refamt);
                $('#input_ref_payable_cnt', document).val(d.refpaycnt);
            } else {
                if(reftype==2) {
                    $('#input_ref_amount', document).val(d.refamt);
                }
            }


            var tbl_referrals_ar = $('#tbl_referrals_ar', document);
            tbl_referrals_ar.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No stocks in inventory yet!'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: 'number'},
                    {"data": "amt", sWidth: '', sClass: 'number'},
                    {"data": "year", sWidth: '', sClass: ''},
                    {"data": "month", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '', sClass: ''},
                    {"data": "attachment", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '60px', sClass: ''},
                ],

                searchHighlight: true,
                fnRowCallback: function (nRow, aData) {

                }
            });
        });
    };


    return {
        init: function(dataid) {
            fn_init_handler(dataid);
        }
    }
}();