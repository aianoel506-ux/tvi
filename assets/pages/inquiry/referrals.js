var REFERRALS = function() {
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();
    PECO.getSweetAlert();
    PECO.getiCheckPlugin();

    var tbl_referrals_logs = $('#tbl_referrals_logs');
    var tbl_view_ar = $('#tbl_view_ar', document);

    var init_referrals = function() {

        $('.reftrn a').on('shown.bs.tab', function(event){
            var this_ = $(this);
            var target = this_.attr('href');
            var module_id = this_.attr('data-module');
            if(target=='#reftbl') {
                PECO.DTDefault(tbl_referrals_logs, 'No referrals!');
            }
        });
        var acctid = $('#input_acct_id', document).val();
        init_tbl_view_ar(acctid);

        tbl_view_ar.on('click', 'tr td', function(e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var input = $('#month', this_tr);
            $('td', this_tr).toggleClass('active');
            this_tr.toggleClass('selected');
            input.iCheck('toggle');
        });


        $(document).on('submit', '#frm_tagging', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: 'Save Changes',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Save!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        dataType: 'json',
                        data: form.serialize(),
                    }).done(function (d) {
                        swal("Success!", d.title + ' has been saved!', 'success');
                        if (d.qry == true) {
                            init_tbl_view_ar(acctid);
                        }
                    }).fail(function () {
                        swal("Error!", "Error PHP", 'error');
                    });
                }
            });
        });

        PECO.select2Basic($('#select2_ref', document), 'cwdo/select2referrals', 'Referrals..', true, false, false, false, false);

    };

    var init_tbl_view_ar = function(acctid) {
        $.ajax({
            url: PECO.base_url() + 'cwdo/getviewar',
            type: 'post',
            data: {'acctid': acctid},
            dataType: 'json'
        }).done(function(d) {
            tbl_view_ar.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                saveState: true,
                order: [[ 0, "desc" ]],
                aoColumns: [
                    {"data": "month", sWidth: '100px', sClass: '', bSortable: false},
                    {"data": "year", sWidth: '', sClass: 'text-align-center', bSortable: false},
                    {"data": "current", sWidth: '', sClass: 'text-primary number', bSortable: false},
                    {"data": "kwh", sWidth: '', sClass: 'number', bSortable: false},
                    {"data": "prsrdg", sWidth: '', sClass: 'number', bSortable: false},
                    {"data": "prsdte", sWidth: '', sClass: 'text-align-center', bSortable: false},
                    {"data": "reff", sWidth: '', sClass: 'text-align-center', bSortable: false},
                    {"data": "checkbox", sWidth: '15px', sClass: 'text-align-center', bSortable: false},
                ],
                fnRowCallback: function(nRow, aData, index) {
                    PECO.iCheckRow($('#month', nRow), 'minimal', 'red');
                }
            });
        });
    };



    return {
        init: function() {
            init_referrals();
        }
    }
}();