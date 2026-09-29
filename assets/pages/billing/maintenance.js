/**
 * Created by ITD on 10/31/2017.
 */
var BILLMAIN = function() {
    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();
    var tbl_charges_main = $('#tbl_charges_main', document);
    var tbl_cont_comp = $('#comp_container', document);
    var title_comp = $('#comp_title', document);
    var init_maintenance = function() {
        PECO.DTDefault(tbl_charges_main, 'No Charges Main Yet!');
        init_charges_main_tbl();
        PECO.DTDefault(tbl_cont_comp, 'Select Charges');
        tbl_charges_main.on('click', 'tr', function(e){
            e.preventDefault();
            var this_tr = $(this);
            var this_id = this_tr.find('#ch_id').val();
            var this_title = this_tr.find('td').eq(2).text();
            var year = $('#rate_class_year', document).val();
            var month = $('#rate_class_month', document).val();
            $('tbody tr', tbl_charges_main).removeClass('row-info');
            this_tr.addClass('row-info');
            title_comp.html('<i class="fa fa-tag"></i> '+this_title);
            $.ajax({
                url: PECO.base_url() + 'billing/chargescomp',
                type: 'post',
                data: {id: this_id, year: year, month: month},
                dataType: 'json',
                beforeSend: function() {
                    PECO.DTphpLoading(tbl_cont_comp, 'Loading rates');
                }
            }).done(function(d) {
                tbl_cont_comp.dataTable().empty();
                tbl_cont_comp.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    aaData: d.list,
                    aoColumns: [
                        {"data": "ratename", sClass: '', sWidth: ''},
                        {"data": "rate", sClass: '', sWidth: '100px'},
                    ]
                });
            }).fail(function(){
                PECO.DTphpError(tbl_cont_comp, '<h4 class="text-danger"><i class="fa fa-warning"></i> <b>ERROR</b>: PHP cannot load data!</h4>');
            });
        });
    };

    var init_charges_main_tbl = function() {
        $.ajax({
            url: PECO.base_url() + 'billing/getchargesmain',
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_charges_main, 'Loading charges main..');
            }

        }).done(function(d){
            tbl_charges_main.dataTable().empty();
            tbl_charges_main.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                aaData: d.list,
                aoColumns: [
                    {"data": "num", sClass: 'text-align-center', sWidth: '20px'},
                    {"data": "name", sClass: 'text-primary text-bold',  sWidth: '70px'},
                    {"data": "descs", sWidth: ''},
                    {"data": "year", sWidth: ''},
                    {"data": "month", sWidth: ''}
                ]
            });

        }).fail(function(){
            PECO.DTphpError(tbl_charges_main, '<h4 class="text-danger"><i class="fa fa-warning"></i> <b>ERROR</b>: PHP cannot load data!</h4>');
        });

    }

    return {
        init: function() {
            init_maintenance();
        }
    }
}();