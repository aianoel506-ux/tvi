var LEGAL = function () {
    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();



    var frm_entry = $('#frm_entry', document);
    var tbl_penalties = $('#tbl_penalties', document);
    var tbl_rabill = $('#tbl_rabill', document);

    var init_new_bank_popover = function() {
        $(document).on('submit', '#frm_new_bank_entry', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Save New Bank Details",
                text: 'Save this new bank entry?',
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
                        method: form.attr('method'),
                        dataType: "json",
                        data: form.serialize()
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal('Error','Error PHP', 'error');
                    });
                }
            });
            e.stopImmediatePropagation();
        });
    };

    var init_verifications = function(dataid) {
        var tbl_apprehension = $('#apprehension_match');

        PECO.select2Basic($('#bankname'), "systems/select2banklist", 'Select bank..', false, false);

        $('#btn_add_bank').popover({
            'html': true,
            'animate': true,
        }).on('click', function(e) {
            init_new_bank_popover();
        });

        init_new_bank_popover();

        init_apprehension_table(1, dataid);
        $('#table_title').html('All Matched Data');
        $('#tbl_view_type_btn').on('click', 'a', function(e) {
           e.preventDefault();
           var this_ = $(this);
           var viewid = this_.attr('data-id');
           init_apprehension_table(viewid, dataid);
           $('#table_title').html(this_.attr('title'));
        });

        $('body').on('click', '#btn_exempt', function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: Apprehension Clear</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]',
                buttonsPosition: 'right',
                buttonClass: 'btn-lg btn-success, btn-lg btn-danger',
                buttonsIcon: 'fa-check, fa-times',
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url: this_.attr('href'),
                        type: 'post',
                        data: {'id': this_.attr('data-id')},
                        dataType: 'json',
                        async: false,
                        beforeSend: function () {

                        }
                    }).done(function (data) {
                        PECO.initAlerts(data.msg, data.title, data.func);
                        if(data.qry==true) {
                            this_.addClass('animated fast rollOut').replaceWith('<h4 class="animated flipInX text-success text-bold pull-right"><i class="fa fa-check"></i> Exemption</h4>');
                        }
                    }).fail(function(){
                        PECO.phpError();
                    });
                }
            });
        });

        function init_apprehension_table(viewtype, dataid)
        {
            $.ajax({
                url: PECO.base_url()+'legal/apprehensionmatch',
                type: 'post',
                dataType: 'json',
                data: {'type': viewtype, 'id': dataid},
            }).done(function (d) {
                $('#verfiy_result').html(d.html);
                // tbl_apprehension.dataTable().empty();
                tbl_apprehension.DataTable({
                    bDestroy: true,
                    bProcessing: true,
                    bPaginate: true,
                    bLengthChange: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    //scrollY: '260px',
                    language: {
                        "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                    },
                    aaData: d.list,
                    aoColumns: [
                        {"data": "num", sClass: 'number text-danger', sWidth: ''},
                        {"data": "name", sClass: 'text-bold', sWidth: '35%'},
                        {"data": "address", sClass: '', sWidth: '40%'},
                        {"data": "apprehensiondate", sClass: 'number', sWidth: ''},
                    ],
                    searchHighlight: true,
                });
            }).fail(function(){
                PECO.DTphpError(tbl_apprehension);
            });
        }
    }


    var init_apprehensions = function (ticketid , typesid ,inspector) {

        $(document).on('click','#deletestaggeredpayment' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");


            swal({
                title: "Are you sure?",
                text: "Transaction will be deleted.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Delete!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'legal/deletestaggeredpayment',
                        type:'post',
                        data:{"stagid" : dataid, "refid" : ticketid},
                        dataType:'json'
                    }).done(function (data) {
                        if(data.qry == true){
                            init_ratbl(ticketid , typesid);
                        }
                        swal("Sent!", data.msg, data.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });

        });

        $(document).on('submit','#submitstaggered' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                    PECO.initAlerts(data.msg , "PECO Legal" , data.func);
                    if(data.qry == true){
                        $('#submitstaggered')[0].reset();
                        init_ratbl(ticketid , typesid);
                    }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#ledger') {
                init_ratbl(ticketid , typesid);
            }else if(target == '#logs'){

            }

        });

        PECO.select2Basic($('#checkbankid', document), "legal/getbanksid", 'Bank Type..', false, false,false);
        PECO.select2Basic($('#trntype', document), "query/legaltrntype", 'Transaction Type..', false, false);
        PECO.select2Basic($('#inspector', document), "legal/getinspectors", 'Select Inspector', false, false , inspector);
        frm_entry.submit(function(e) {
            e.preventDefault();
            var this_form = $(this);
            swal({
                title: "Are you sure?",
                text: "Add apprehension ledger!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_form.attr('action'),
                        type: this_form.attr('method'),
                        data: this_form.serialize(),
                        dataType: 'json',
                        beforeSend: function () {
                            $('#query-status').html('Loading..');
                        }
                    }).fail(function () {
                        PECO.phpError();
                        swal.close();
                    }).done(function (data) {
                        swal("Legal", data.msg, data.func);
                        if(data.qry == true){
                            //frm_entry[0].reset();
                        }
                    });
                }else{
                    swal.close();
                }
            });

        });
    };


    var init_ratbl = function(ticketid , typesid) {

        $.ajax({
            url: PECO.base_url() + 'legal/getstaggeredtrans',
            type: 'post',
            data: {'ticketid': ticketid , "typesid" : typesid},
            dataType: 'json',
            beforeSend: function () {
                tbl_rabill.dataTable().empty();
                PECO.DTphpLoading(tbl_rabill, 'Loading ledger..');
            }
        }).fail(function () {
            PECO.DTphpError(tbl_rabill);
        }).done(function (d) {
            if(d.typesid == 150){
                tbl_rabill.DataTable({
                    bDestroy: true,
                    bProcessing: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    scrollY: '260px',
                    language: {
                        "emptyTable": PECO.DTEmptyMessage('No legal ledger entry'),
                    },
                    aaData: d.staggereddata,
                    aoColumns: [
                        {"data": "month" , sWidth: '5%'},
                        {"data": "year" , sClass:'number'},
                        {"data": "duedate" , sClass:'number', sWidth: '10%'},
                        {"data": "amt" , sClass:'number'},
                        {"data": "paid" , sClass:'number'},
                        {"data": "status" , sClass:'number'},
                        {"data": "control"}
                    ],
                    searchHighlight: true
                });
            }else if(d.typesid == 151){
                tbl_rabill.DataTable({
                    bDestroy: true,
                    bProcessing: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    scrollY: '260px',
                    language: {
                        "emptyTable": PECO.DTEmptyMessage('No legal ledger entry')
                    },
                    aaData: d.list,
                    aoColumns: [
                        {"data": "month" , sWidth: '5%'},
                        {"data": "year" , sClass:'number'},
                        {"data": "duedate" , sClass:'number'},
                        {"data": "amt" , sClass:'number'},
                        {"data": "paid" , sClass:'number'},
                        {"data": "status" , sClass:'number'},
                        {"data": "control"}

                    ],
                    searchHighlight: true
                });
            }

        });

    };

    var init_legalpayments = function(servno){
        init_legalpayments_tbl(servno);
    };

    var init_legalpayments_tbl = function(servno){


        $.ajax({
            url: PECO.base_url()+'legal/getpenaltypaymentstbl',
            type: 'post',
            dataType: 'json',
            data: {'servno': servno},
        }).done(function (d) {
            $('#amtcashb' , document).val(d.amttopay);
            $('#sptotalamt' , document).val(d.amttopay);
            tbl_penalties.dataTable().empty();
            tbl_penalties.dataTable({
                bDestroy: true,
                bProcessing: true,
                bPaginate: true,
                bLengthChange: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                scrollY: '260px',
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                },
                aaData: d.list,
                aoColumns: [
                    {"data": "num"},
                    {"data": "acctcode"},
                    {"data": "novat"},
                    {"data": "vat"},
                    {"data": "total"},
                    {"data": "chk"},
                    {"data": "control"}

                ],
                searchHighlight: true,
            });
            PECO.initDTNicescroller();
        }).fail(function(){
            PECO.DTphpError(tbl_apprehension);
        });
    };

    return {
        apprehensions: function (ticketid , typesid , inspector) {
            init_apprehensions(ticketid , typesid , inspector);
        },
        verification: function (dataid) {
            init_verifications(dataid);
        },
        legalpayments:function (servno) {
            init_legalpayments(servno);
        }
    }
}(jQuery);