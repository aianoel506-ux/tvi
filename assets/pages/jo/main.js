var JO = function() {
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();

    var jo_tbl = $('#tbl_jo_list', document);
    var jotrntrailtbl = $('#jotrntrailtbl', document);
    var jotrnlogstbl = $('#jotrnlogstbl', document);


    var init_jo_fn = function() {
        PECO.meterSearchForm();

        $(document).on('click', '#btn_print_order', function(e) {
            e.preventDefault();
            var btn = $(this);
            var btn_html = btn.html();
            var joid = btn.attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'jo/printorder',
                type: 'post',
                data: {'joid': joid},
                dataType: 'json',
                beforeSend: function() {
                    PECO.btnLoading(btn, 'Getting...');
                }
            }).done(function(d) {
                PECO.btnSuccess(btn, 'Printing..', btn_html, 'btn-default');
                if(d.qry == true) {
                    PECO.pecoRepPrint('Job Order', d.html, false);
                }
            }).fail(function() {
                PECO.btnErrorPHP(btn, btn_html, 'btn-default');
            });
        });
    };



    var init_date_filter = function(view) {
        $('#filters', document).on('ifChecked', '#icheckdatefilter', function (e) {
            var this_ = $(this);
            this_.attr('checked',  '#icheckdatefilter', true);
            init_jo_tbl(view, false, true);
        }).on('ifUnchecked', function (e) {
            var this_ = $(this);
            this_.attr('checked',  '#icheckdatefilter', false);
            init_jo_tbl(view, false, true);
        });

        $(document).on('keyup', '.filter-dates', function(e) {
            e.preventDefault();
            init_jo_tbl(view, false, true);
        });
    };

    var init_jo_dashboard = function(view) {
        init_jo_tbl(view, false, false);

        init_date_filter(view);

        ASSET.initDeletePicRow();

        PECO.btnClearTrans($('#btn_clear_trans', document));


        $(document).on('click', '#btn_refresh_list', function(e) {
            e.preventDefault();
            init_jo_tbl(view, false, true);
        });

//searching Meter#, S/N & Reading
        jo_tbl.on('keypress','#input_mtrno',function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var input_serial = $('#input_serial',this_row);
            var input_rdg = $('#input_rdg',this_row);
            var input_mtrno = this_.val();
            if (code == 13) {
                $.ajax({
                    url: PECO.base_url() + 'jo/ugetmeterinfo',
                    type: 'post',
                    data: {mtrno: input_mtrno},
                    dataType: 'json'
                }).done(function (d) {
                    input_serial.val(d.serial);
                    input_rdg.val(d.reading);
                    if (d.reading == ''){
                        input_rdg.focus();
                    }
                });
            }
        });


//Save accomplishments.
        jo_tbl.on('keypress','#input_mtrno', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var input_serial = $('#input_serial',this_row).val();
            var input_mtrno = $('#input_mtrno',this_row).val();
            var input_rdg = this_.val();
            if (input_serial != '') {
                if (code == 13) {
                    e.preventDefault();
                    $.ajax({
                        url: PECO.base_url() + 'jo/saveaccomplishments',
                        type: 'post',
                        data: {
                            'mtrno': input_mtrno,
                            'serial': input_serial,
                            'reading': input_rdg
                        },
                        dataType: 'json'
                    })
                }
            }
        });

        PECO.select2Basic($('#select2status', document), 'jo/select2jostatus', 'Status... ', false, true, false, false, true);

        $(document).on('change', '#select2status', function() {
            init_jo_tbl(view, false, true);
        });

        $('#btn_filters', document).on('click', 'button', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $('#btn_filters button').removeClass('active');
            this_.addClass('active');
            init_jo_tbl(view, false, true);
        });


        jo_tbl.on('click', 'tr.list-ticket #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url() + 'jo/getjodetails',
                    type: 'post',
                    data: {'id': data_id, 'type': 'all'},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="">' + d.html + '</td></tr>');
                    jo_tbl.find('#loading').remove();

                    ASSET.initMtrPicUploadRow(thisTr.next());

                    ASSET.initMtrPics(thisTr.next(), $('#mtrno', thisTr).text(), d.acctid, d.year, d.month, 'all');


                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    jo_tbl.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                jo_tbl.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });
    };


    var init_jo_tbl = function(view, search, loading) {


        var loading_ = (loading) ? true : false;


        var search_ = (search) ? 1 : 0;

        var status_ = $('#select2status', document).val();

        var types_ = $('#btn_filters button.active').attr('data-id');

        // #########################################################
        var sname = $('#search_name', document).val();
        var saddr = $('#search_addr', document).val();

        // #########################################################
        var datefilter_ = 0;
        var datefilter_checkbox = $('#icheckdatefilter', document);
        if (datefilter_checkbox.is(":checked")) {
            datefilter_ = 1;
        }
        var filteryear = $('#filteryear', document).val();
        var filtermonth = $('#filtermonth', document).val();
        var filterday = $('#filterday', document).val();


        $.ajax({
            url: PECO.base_url() + 'jo/getjoborderlist',
            type: 'post',
            dataType: 'json',
            data: {
                'view': view,
                'status': status_,
                'types': types_,
                'complaints': 'JO',
                'searching': search_,
                'sname': sname,
                'saddr': saddr,
                'datefilter': datefilter_,
                'filteryear': filteryear,
                'filtermonth': filtermonth,
                'filterday': filterday
            },
            beforeSend: function() {
                if(loading_) {
                    PECO.DTphpLoading(jo_tbl, 'Loading Trouble call listing...');
                }
            }
        }).done(function(d) {
            var oTable = jo_tbl.DataTable({
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
                    {"data": "num", sWidth: '', sClass: 'text-align-center'},
                    {"data": "joborder", sWidth: '', sClass: 'text-info joborder'},
                    {"data": "requester", sWidth: '20%', sClass: 'text-danger name'},
                    {"data": "acctdetails", sWidth: '35%', sClass: 'text-info address'},
                    {"data": "datecreated", sWidth: '12%', sClass: ''},
                    {"data": "dateupdated", sWidth: '12%', sClass: ''},
                    {"data": "transaction", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '10%', sClass: 'status'},
                    {"data": "control", sWidth: '10%', sClass: 'controls'},
                ],

                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),
                "sDom": "Rlfrtip",
                order: [[1, 'desc']],
                fnRowCallback: function(nRow, aData, index) {
                    $(nRow).addClass('list-ticket');


                    $('.tooltips', nRow).tooltip();
                    $('.popovers', nRow).each(function(){
                        PECO.popOverRow($(this), true, true, 'popover-danger');
                    });

                }
            });

        });

    };

    var init_mis_trn = function(trnid, joid) {
        init_jo_trail(trnid);


        $(document).on('click', '#btn_get_mtr', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr('data-val');
            $('#mtrsearch', document).val(this_val);
            setTimeout(function() {
                $('#mtrsearch', document).focus().trigger('change');
            }, 300);
            $('#modal_ajax').modal('toggle');
        });

        $(document).on('keypress', '#mtrsearch', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code == 13) {
                e.preventDefault();
                $.ajax({
                    url: PECO.base_url() + 'search/getmeterinfo',
                    type: 'post',
                    data: {'mtrno': $(this).val()},
                    dataType: 'json',
                }).done(function(selection) {
                    if(selection.qry == true) {
                        $('#assetid', document).val(selection.id);
                        $('#mis_serial', document).html(selection.serial);
                        $('#mis_type', document).html(selection.type);
                        $('#mis_brand', document).html(selection.brand);
                        $('#mis_volts', document).html(selection.volts);
                        $('#mis_ampere', document).html(selection.ampere);
                        $('#mis_pecoseal', document).html(selection.pecoseal);
                        $('#mis_ercseal', document).html(selection.ercseal);
                        $('#mis_wiresize', document).html(selection.wiresize);
                        $('#mis_kh', document).html(selection.kh);
                        $('#mis_reading', document).html(selection.reading);
                        $('#mis_status', document).html(selection.status);
                        $('#btn_asset_mtrview', document).attr('href', PECO.base_url() + 'module/6052521b7625e31d4ee9cc706732484fcf850877/view/' + selection.id);
                    }else{

                        $('#assetid', document).val('');
                        $('#btn_asset_mtrview', document).attr('href', '#');

                        $('.display-text', document).each(function() {
                            $(this).html('N/A');
                        });
                    }
                }).fail(function() {
                    PECO.phpError();

                    $('#assetid', document).val('');
                    $('#btn_asset_mtrview', document).attr('href', '#');

                    $('.display-text', document).each(function() {
                        $(this).html('N/A');
                    });
                });
            }
        });


        $('[data-toggle=tab]').on('shown.bs.tab', function(e) {

            var this_ = $(this);
            var target = this_.attr('href');
            if (target == '#trails') {
                init_jo_trail(trnid);
            }
            if (target == '#logs') {
                init_jo_logs(joid);
            }
        });
        PECO.select2Basic($('#empid', document), 'hris/getemployees', 'Select Employee...', false);

        $(".date-picker", document).datepicker({
            rtl: PECO.isRTL(),
            orientation: "left",
            autoclose: !0
        });

        $(document).on('submit', '#frm_utility_accomplishment', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: 'Accomplishment of transaction, update master file!',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function(d) {
                        swal('Job Order: Accomplishemnt', d.msg, d.func);
                        if(d.qry == true) {
                            window.location = '';
                        }
                    }).fail(function() {
                        swal.close();
                        PECO.phpError();
                    });
                }
            });
        })

        $(document).on('submit', '#frm_utility_accomplishment_fdo', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: 'Disconnection, update master file!',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function(d) {
                        swal('Job Order: Accomplishemnt', d.msg, d.func);
                        if(d.qry == true) {
                            window.location = '';
                        }
                    }).fail(function() {
                        swal.close();
                        PECO.phpError();
                    });
                }
            });
        });

        $(document).on('submit', '#frm_submit_mtr_assignment', function(e) {
            e.preventDefault();
            var form = $(this);


            var trnid = $('#trnid', document).val();
            var flowid = $('#flowid', document).val();
            var stageid = $('#stageid', document).val();
            var moduleid = $('#moduleid', document).val();
            var dataid = $('#dataid', document).val();
            var trntital = $('#trntital', document).val();
            var status = $('#status', document).val();
            var routeto = $('#routeto', document).val();
            var ramarks = $('#remarks', document).val();

            swal({
                title: "Are you sure?",
                text: 'Accomplishment of job order, will update master file.',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function(d) {
                        swal("Job Order", d.msg, d.func);
                        window.location = '';
                        /*
                        $.ajax({
                            url: PECO.base_url() + 'query/requestprocess',
                            type: 'post',
                            data: {
                                'trnid': trnid,
                                'flowid': flowid,
                                'stageid': stageid,
                                'moduleid': moduleid,
                                'dataid': dataid,
                                'trntitle': trntital,
                                'status': status,
                                'routeto': routeto,
                                'remarks': ramarks
                            },
                            dataType: 'json'
                        }).done(function(d) {
                            // window.location = d.url.link;
                        });
                        */
                    }).fail(function() {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click', '#btn_cancel_issuance', function() {
            var this_ = $(this);
            var joid = this_.attr('data-joid');
            var trailid = this_.attr('data-trailid');
            var ownerid = this_.attr('data-ownerid');
            var type = this_.attr('data-type');

            var trnid = $('#trnid', document).val();
            var flowid = $('#flowid', document).val();
            var stageid = $('#stageid', document).val();
            var moduleid = $('#moduleid', document).val();
            var dataid = $('#dataid', document).val();
            var trntital = $('#trntital', document).val();
            var status = $('#status', document).val();
            var routeto = $('#routeto', document).val();

            swal({
                title: "Cancel Issuance Data",
                text: "State Comments:",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Write something"
            }, function (inputValue) {
                if (inputValue === false) return false;
                $.ajax({
                    url: PECO.base_url() + 'jo/cancelmtrissuance',
                    type: 'post',
                    data: {'joid': joid, 'trailid': trailid, 'ownerid': ownerid, 'remarks': inputValue},
                    dataType: 'json'
                }).done(function(d) {
                    swal("Job Order", d.msg, d.func);
                    if(d.qry==true) {
                        window.location = '';
                        if(type==1) {
                            $.ajax({
                                url: PECO.base_url() + 'query/requestprocess',
                                type: 'post',
                                data: {
                                    'trnid': trnid,
                                    'flowid': flowid,
                                    'stageid': stageid,
                                    'moduleid': moduleid,
                                    'dataid': dataid,
                                    'trntitle': trntital,
                                    'status': status,
                                    'routeto': routeto,
                                    'remarks': inputValue
                                },
                                dataType: 'json'
                            }).done(function (d) {
                                // window.location = d.url.link;
                            });
                        }
                    }
                }).fail(function() {
                    PECO.phpError();
                    swal.close();
                });
            });
        });
    };

    var init_jo_logs = function (joid) {
        $.ajax({
            url:PECO.base_url()+'jo/getjoborderlogs',
            type:'post',
            data:{"joid" : joid},
            dataType:'json',
            beforeSend: function() {
                PECO.DTphpLoading(jotrnlogstbl, 'Loading trail routes...');
            }
        }).done(function (d) {
            jotrnlogstbl.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: false,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                pageLength: 10,
                saveState: true,
                order: [['2', 'desc']],
                aoColumns: [
                    {"data": "num"},
                    {"data": "desc"},
                    {"data": "datecreated"},
                    {"data": "createdby"},
                ],

                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),
                "sDom": "Rlfrtip"
            });
        }).fail(function () {
            PECO.DTphpError(jotrnlogstbl);
        });
    };

    var init_jo_trail = function (trnid) {
        $.ajax({
            url:PECO.base_url()+'jo/getjobordertrntrail',
            type:'post',
            data:{"trnid" : trnid},
            dataType:'json',
            beforeSend: function() {
                PECO.DTphpLoading(jotrntrailtbl, 'Loading trail routes...');
            }
        }).done(function (d) {
            jotrntrailtbl.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: false,
                bInfo: true,
                aaData: d.jotraildata,
                bSort: true,
                pageLength: 10,
                saveState: true,
                order: [['2', 'desc']],
                aoColumns: [
                    {"data": "num"},
                    {"data": "desc"},
                    {"data": "datecreated"},
                    {"data": "dateupdated"},
                    {"data": "createdby"},
                    {"data": "updatedby"}
                ],

                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),
                "sDom": "Rlfrtip"
            });
        }).fail(function () {
            PECO.DTphpError(jotrntrailtbl);
        });
    };

    return {
        init: function() {
            init_jo_fn();
        },

        dashboard: function(view) {
            init_jo_dashboard(view);
        },

        table: function(view, search, loading) {
            init_jo_tbl(view, search, loading);
        },

        mistrn: function (trnid, joid) {
            init_mis_trn(trnid, joid);
        }
    }
}();