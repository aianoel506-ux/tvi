var ASSESSMENT = function() {

    var fn_init = function(dataid) {
        PECO.select2Types($('#select2paytype', document), 'CADAPPPAYTYPE', 'Select paytype...', false, false, false, false, true);
        PECO.select2Types($('#select2DUname', document), 'DUNAME', 'Select DU name...', false, false, false, false, true);
        PECO.select2Basic($('#input_start_months', document), 'systems/select2month', 'Select month..');


        handler_assessment_query(dataid, 0);
        $(document).on('change', '#select2paytype', function(e) {
            handler_assessment_query(dataid, 0);
        });
        $(document).on('click', '#generate_proposal', function(e) {
            handler_assessment_query(dataid, 1);
        });


        $(document).on('change, keyup', '#assessment_input input', function(e) {
            handler_assessment_query(dataid, 0);
        });

    };

    var handler_assessment_query = function(dataid, generate) {
        var form = $('#frm_save_assessment', document);
        var tbl_assessment_ar = $('#tbl_assessment_billing', document);
        $.ajax({
            url: PECO.base_url() + 'cad/getassessmentdetails',
            type: 'post',
            data: form.serialize() + '&generate=' + generate,
            dataType: 'json',
            beforeSend: function() {
                if(generate == 1) {
                    PECO.DTphpLoading(tbl_assessment_ar, 'Generating billing...');
                }else{
                    PECO.DTphpLoading(tbl_assessment_ar, 'Loading billing structure...');
                }
            }
        }).done(function(d) {
            $('#input_payable_count', document).val(d.paycnt);
            $('#input_interest_rate', document).val(d.intrate);
            $('#input_amt_permonth', document).val(d.amtpermonth);
            $('#text_principal_amt', document).text(d.totalamttext);

            tbl_assessment_ar.DataTable({
                searching: false,
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No stocks in inventory yet!'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: 'number'},
                    {"data": "year", sWidth: '', sClass: ''},
                    {"data": "month", sWidth: '', sClass: ''},
                    {"data": "duedate", sWidth: '', sClass: ''},
                    {"data": "amt", sWidth: '', sClass: 'number'},
                    {"data": "paid", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '', sClass: ''},
                    {"data": "emailed", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '60px', sClass: ''},
                ],

                searchHighlight: true,
                fnRowCallback: function (nRow, aData) {

                }
            });
        }).fail(function() {
            PECO.phpError();
            PECO.DTDefault($('#tbl_assessment_billing', document), 'No billing yet...');
        });
    }

    return {
        init: function(dataid) {
            fn_init(dataid);
        }
    }
}();