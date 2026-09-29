var UTILITY = function() {
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();
    PECO.getNumberFormatPlugin();

    var tbl_meter_list = $('#tbl_meter_list', document);
    var jo_tbl = $('#tbl_jo_sheet', document);


    var init_utility_fn = function() {

        $(document).on('click', '#btn_accomplish_joborder', function(e) {
            var this_ = $(this);
            var this_html = this_.html();
            e.preventDefault();
            $.ajax({
                url: PECO.base_url() + 'jo/accomplish',
                type: 'post',
                data: {},
                dataType: 'json',
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...')
                }
            }).done(function(d) {
                PECO.btnSuccess(this_, 'Done!', this_html, 'btn-success');
            }).fail(function() {
                PECO.btnErrorPHP(this_, this_html, 'btn-success');
            });

        });

        $(document).on('click', '#btn_get_mtr', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr('data-val');
            var this_class = this_.attr('href').replace('#', '');
            $('.' + this_class, document).val(this_val);
            setTimeout(function() {
                $('.' + this_class, document).focus();
            }, 300);
            $('#modal_ajax').modal('toggle');
        });


        jo_tbl.on('keypress', '#input_mtrno', function(e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            var this_date = $('#input_datecomp', this_tr);
            var this_serial = $('#input_serial', this_tr);
            if(e.keyCode == 13) {

                if(this_val == '') {
                    var this_group = this_.closest('.input-group');
                    $('a.btn', this_group).trigger('click');
                }else {
                    e.preventDefault();
                    $.ajax({
                        url: PECO.base_url() + 'jo/ugetmeterinfo',
                        type: 'post',
                        data: {'mtrno': this_val},
                        dataType: 'json',
                        beforeSend: function () {
                            this_.attr('readonly', true);
                        }
                    }).done(function (d) {
                        if (d.qry == true) {
                            this_date.val('').focus();
                            this_.attr('readonly', true);
                            this_serial.val(d.serial);
                        } else {
                            this_.focus();
                            this_.attr('readonly', false);
                        }
                    });
                }
            }
        });
        jo_tbl.on('keyup', '#input_datecomp', function(e) {

            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_mtrno = $('#input_mtrno', this_tr);
            var this_serial = $('#input_serial', this_tr);
            if(e.keyCode == 27) {
                e.preventDefault();
                this_.val('').attr('readonly', false);
                this_serial.val('');

                //$('.icheck', this_tr).trigger('click');
                $('.icheck', this_tr).iCheck('uncheck');

                this_mtrno.attr('readonly', false).val('').focus();
            }

            if(e.keyCode == 13) {
                this_.attr('readonly', true);
                var index = $('input#input_datecomp').index(this) + 1;

                var joid = this_.attr('data-joid');
                var mtrno = $('#input_mtrno', this_tr).val();
                var serial = $('#input_serial', this_tr).val();

                if(this_tr.hasClass('issue') == true) {
                    // AJAX START
                    $.ajax({
                        url: PECO.base_url() + 'jo/submitissuancetemprow',
                        type: 'post',
                        data: {'mtrno': mtrno, 'serial': serial, 'joid': joid, 'dateaccomp': this_.val()},
                        dataType: 'json'
                    }).done(function (d) {
                        if (d.qry == true) {
                            var this_input = $('input#input_mtrno').eq(index).focus();
                            setTimeout(function () {
                                this_input.select();
                            }, 100);

                            $('td:first-child', this_tr).html('<span class="label label-success"><i class="fa fa-check"></i></span>');

                            init_meterlist();

                            this_tr.removeClass('issue');

                            $('td:last-child', this_tr).prepend('<button id="btn_reissue" style="margin-bottom: 3px;" data-id="' + d.assetid + '" data-joid="' + d.joid + '" class="btn btn-default btn-xs inline">Revert</button>');
                            $('td.status', this_tr).html('<a href="javascript:;" class="label tooltips" data-placement="top" title="Issued" style="background: #FC0000; color: #FCF9F9"><i class="fa fa-times"></i> Issued </a>');
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            }
        });


        $(document).on('click', '#btn_refresh_mtrlist', function(e) {
            init_meterlist();
        });

        $(document).on('click', '#btn_refresh_joborder', function(e) {
            init_joborder();
        });


        jo_tbl.on('click', '#btn_reissue', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var joid = this_.attr('data-joid');
            var assetid = this_.attr('data-id');
            e.preventDefault();
            $.ajax({
                url: PECO.base_url() + 'jo/submitmeterreissuerow',
                type: 'post',
                data: {'joid': joid, 'assetid': assetid},
                dataType: 'json'
            }).done(function (d) {
                if (d.qry == true) {
                    init_joborder();
                    init_meterlist();
                }
            });
        });

    };

    var init_meterlist = function(dataid) {
        $.ajax({
            url: PECO.base_url() + 'assets/getassetlist',
            type: 'post',
            data: {'status': 3202, 'view': 'utility', 'dataid': dataid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_meter_list, 'Loading meter lists...');
            }

        }).done(function(d) {
            tbl_meter_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                scrollY: '350px',
                aaData: d.list,
                aoColumns: [
                    {data: 'assetnumber', sWidth:'', sClass: 'assetnum'},
                    {data: 'assetserial', sClass: 'assetserial', sWidth: '25%'},
                    {data: 'type'},
                    {data: 'ercseal'},
                    {data: 'pecoseal'},
                    {data: 'ampere'},
                    {data: 'volts'},
                    {data: 'control', sClass: 'input', sWidth: '50px'}
                ],
                language: PECO.DTEmptyMessage('No meter record found!'),
                searchHighlight: true,
            });
            PECO.dataTableScroller();
        });
    };


    var init_joborder = function() {

        $.ajax({
            url: PECO.base_url() + 'jo/getjoborderlist',
            type: 'post',
            dataType: 'json',
            data: {
                'view': 2,
                'complaints': 'JO',
            },
            beforeSend: function() {
                PECO.DTphpLoading(jo_tbl, 'Loading Trouble call listing...');
            }
        }).done(function(d) {
            jo_tbl.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                pageLength: 20,
                saveState: true,
                aoColumns: [
                    {"data": "checkbox", sWidth: '10px', sClass: 'text-primary relative', orderable: false},
                    {"data": "joborder", sWidth: '100px', sClass: 'text-primary'},
                    {"data": "type", sWidth: '', sClass: 'text-primary'},
                    {"data": "acctdetails", sWidth: '30%', sClass: 'text-info address '},
                    {"data": "mtrno", sWidth: '', sClass: ''},
                    {"data": "serial", sWidth: '', sClass: ''},
                    {"data": "datecomply", sWidth: '', sClass: ''},
                    {"data": "datecreated", sWidth: '12%', sClass: ''},
                    {"data": "status", sWidth: '12%', sClass: 'status'},
                    {"data": "control", sWidth: '10%', sClass: 'contols'},
                ],
                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),
                "sDom": "Rlfrtip",
                order: [[1, 'desc']],
                fnRowCallback: function(nRow, aData, index) {
                    $(nRow).addClass('issue');
                    PECO.iCheckRow($('.icheck', nRow), 'minimal', 'blue');
                }
            });

        });

    };

    return {
        init: function() {
            init_utility_fn();
        },
        mtr: function(dataid) {
            init_meterlist(dataid);
        },
        jo: function() {
            init_joborder();
        }
    }
}();