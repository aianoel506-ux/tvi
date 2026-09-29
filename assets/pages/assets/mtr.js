var MTR = function() {
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();
    PECO.getNumberFormatPlugin();

    var tbl_meter_list = $('#tbl_meter_list', document);
    var asset_loading_box = $('.with-loading', document);
    var remarkstable = $('#remarkstable', document);


    var init_submit_reading_row = function(this_) {
        var submit = false;
        var asset_id =   this_.attr('data-id')
        var reading =   this_.val();

        $.ajax({
            url: PECO.base_url() + 'assets/submitrowreading',
            data: {"asset_id":asset_id , "reading":reading},
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d){
            console.log(d);
            submit = d.qry;
            // PECO.initAlerts(d.msg, d.title, d.func, false, false);
        });
        return submit;
    };
    var init_submit_mult_row = function(this_) {
        var submit = false;
        var asset_id =   this_.attr('data-id')
        var mult =   this_.val();

        $.ajax({
            url: PECO.base_url() + 'assets/submitrowmult',
            data: {"asset_id":asset_id , "mult":mult},
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d){
            console.log(d);
            submit = d.qry;
            // PECO.initAlerts(d.msg, d.title, d.func, false, false);
        });
        return submit;
    };
    var init_submit_volts_row = function(this_) {
        var submit = false;
        var asset_id =   this_.attr('data-id')
        var volt =   this_.val();

        $.ajax({
            url: PECO.base_url() + 'assets/submitrowvolts',
            data: {"asset_id":asset_id , "volt":volt},
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d){
            console.log(d);
            submit = d.qry;
            // PECO.initAlerts(d.msg, d.title, d.func, false, false);
        });
        return submit;
    };
    var init_submit_wiresize_row = function(this_) {
        var submit = false;
        var asset_id =   this_.attr('data-id')
        var wiresize =   this_.val();

        $.ajax({
            url: PECO.base_url() + 'assets/submitrowwiresize',
            data: {"asset_id":asset_id , "wiresize":wiresize},
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d){
            console.log(d);
            submit = d.qry;
            // PECO.initAlerts(d.msg, d.title, d.func, false, false);
        });
        return submit;
    };

    var init_mis_key = function() {
        tbl_meter_list.on('keypress', '#reading', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_val = this_.val();
            if (e.keyCode == 13) {
                e.preventDefault();
                if(init_submit_reading_row(this_)==true ) {
                    var index = $('input#reading').index(this) + 1;
                    var this_input = $('input#reading').eq(index).focus();
                    this_input.val(this_val);
                    setTimeout(function () {
                        this_input.select();
                    }, 100);

                }
            }
        });
        tbl_meter_list.on('keypress', '#mult', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_val = this_.val();
            if (e.keyCode == 13) {
                e.preventDefault();
                if(init_submit_mult_row(this_)==true ) {
                    var index = $('input#mult').index(this) + 1;
                    var this_input = $('input#mult').eq(index).focus();
                    this_input.val(this_val);
                    setTimeout(function () {
                        this_input.select();
                    }, 100);

                }
            }
        });
        tbl_meter_list.on('keypress', '#volts', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_val = this_.val();
            if (e.keyCode == 13) {
                e.preventDefault();
                if(init_submit_volts_row(this_)==true ) {
                    var index = $('input#volts').index(this) + 1;
                    var this_input = $('input#volts').eq(index).focus();
                    this_input.val(this_val);
                    setTimeout(function () {
                        this_input.select();
                    }, 100);

                }
            }
        });
        tbl_meter_list.on('keypress', '#wiresize', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_val = this_.val();
            if (e.keyCode == 13) {
                e.preventDefault();
                if(init_submit_wiresize_row(this_)==true ) {
                    var index = $('input#wiresize').index(this) + 1;
                    var this_input = $('input#wiresize').eq(index).focus();
                    this_input.val(this_val);
                    setTimeout(function () {
                        this_input.select();
                    }, 100);

                }
            }
        });

        // ################################################################
        // ARROW DOWN NEXT READING INPUT
        tbl_meter_list.on('keydown', 'input', function(e) {
            if (e.which === 40) {
                console.log('Arrow down');
                var index = $('input').index(this) + 1;
                var this_input = $('input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
            }
        });
        // ################################################################

        // ################################################################
        // ARROW UP PREVIOUS READING INPUT ################################
        tbl_meter_list.on('keydown', 'input', function(e) {
            if (e.which === 38) {
                var index = $('input').index(this) - 1;
                var this_input = $('input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
            }
        });
        // ################################################################
    };

    var init_mis_fn = function() {
        init_mis_tbl();
        init_mis_key();

        $(document).on('click', '#btn_sync_asset', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var btn_html = this_.html();
            $.ajax({
                url: PECO.base_url() + 'assets/syncmtrasset',
                type: 'post',
                data: {},
                dataType: 'json',
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Syncing...');
                }
            }).done(function() {
                PECO.btnSuccess(this_, 'Done!', btn_html, 'btn-danger');
            }).fail(function() {
                PECO.btnErrorPHP(this_, btn_html, 'btn-danger');
            });
        });



        $(document).on('keypress', '#search_mtrno', function(e) {
            var this_ = $(this);
            var this_val = this_.val();
            if(e.keyCode == 13) {
                e.preventDefault();
            }
        });

        tbl_meter_list.on("click", 'tr td', function() {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            //$('td', tbl_meter_list).removeClass('active');

            //$('td', this_tr).addClass('active');
            $('.icheck', this_tr).trigger('click');

            var asset_num = $('td.assetnum', this_tr).text();
            var asset_serial = $('td.assetserial', this_tr).text();

            $('#text_asset_number', document).text(asset_num);
            $('#text_asset_serial', document).text(asset_serial);

            /*
            $.ajax({
               url: PECO.base_url() + 'assets/assetinfo',
               type: 'post',
               data: {'number': asset_num, 'serial': asset_serial},
               dataType: 'json',
               beforeSend: function() {
                   PECO.blockUIRipple({
                       target: asset_loading_box,
                       animate: true,
                       overlayColor: '#EF582D'
                   });
               }
            }).done(function(d) {
                if(d.qry == true) {
                    $('#text_asset_acctno', document).html(d.acctno);
                    $('#text_asset_name', document).html(d.name);
                    $('#text_asset_address', document).html(d.address);
                    $('#asset_spec_arr', document).html(d.specs);
                    $('#text_asset_dateissued', document).html(d.dateissued);
                    $('#text_asset_issuedby', document).html(d.issuedby);
                    $('#asset_img_box', document).html(d.pictures);
                }
                PECO.unblockUI(asset_loading_box);
            }).fail(function() {
                PECO.unblockUI(asset_loading_box);
            });
            */

        });
    };

    var init_mis_tbl = function() {
        var types = $('#btn_types_group .active', document).attr('data-id');
        var status = $('ul.meterstat li.active a', document).attr('data-id');
        var search_text = $('#itemsearch', document).val();

        var date_start = $('#datestart', document).val();
        var date_end = $('#dateend', document).val();

        $.ajax({
            url: PECO.base_url() + 'assets/getassetlist',
            type: 'post',
            data: {'dataid': types, 'status': status, 'searchtxt': search_text , "datestart" : date_start , "dateend" : date_end},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_meter_list, 'Loading meter lists...');
            }

        }).done(function(d) {
            tbl_meter_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                scrollY: '350px',
                aaData: d.list,
                aoColumns: [
                    {data: 'expand', sWidth: '10px', sClass: 'text-align-center'},
                    {data: 'assetnumber', sWidth:'', sClass: 'assetnum', sWidth: '7%'},
                    {data: 'assetserial', sClass: 'assetserial', sWidth: '7%'},
                    {data: 'types', sWidth:''},
                    {data: 'make', sWidth:''},
                    {data: 'ercseal', sWidth:''},
                    {data: 'pecoseal', sWidth:''},
                    {data: 'ampere', sClass: 'assetserial', sWidth: '5%'},
                    {data: 'volts', sClass: 'volts', sWidth: '5%'},
                    {data: 'reading', sClass: 'reading', sWidth: '5%'},
                    {data: 'mult', sClass: 'mult', sWidth: '5%'},
                    {data: 'wiresize', sClass: 'wiresize', sWidth: '5%'},
                    {data: 'modified', sWidth: '150px' ,sClass: 'text-primary bold'},
                    {data: 'status', sClass: 'text-info'},
                    {data: 'control', sClass: 'controls', sWidth: '50px'},
                ],
                language: PECO.DTEmptyMessage('No meter record found!'),
                searchHighlight: true,
                order: [[7, 'desc']],
                fnRowCallback: function(nRow){
                    $('.tooltips' , nRow).tooltip();

                }
            });
            //PECO.dataTableScroller();
        });
    };

    var init_new_mis = function () {

        $(document).on('click','.btnchangestat' , function () {
            alert("test");
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            $('tr.even > td > input').each(function () {
              // alert($(this).val());
            });

        });

        $(document).on('click', '#modal_close', function(e) {
            e.preventDefault();
            $('#modal_ajax', document).modal('toggle');
        });


        $(document).on('submit','#submitbrand',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "Brand"  , data.func);
                if(data.qry == true){
                    this_[0].reset();
                    PECO.select2Basic($('#assetbrand',document) , 'assets/getbrands' , 'Select Brand',false,false,false);
                }
            }).fail(function () {
                PECO.phpError();
            });
            e.stopImmediatePropagation();
        });

        $(document).on('submit','#submitmeterissuance',function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: "Asset will be added.",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Save!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data: new FormData(this_[0]),
                        processData: false,
                        contentType: false,
                        dataType:'json'
                    }).done(function (data) {
                        swal("Asset", data.msg, data.func);
                        if(data.qry == true){
                            this_[0].reset();
                            $(document).find('#assettypes').select2('val','');
                            $(document).find('#assetbrand').select2('val','');
                            init_mis_tbl();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });
        });
    };

    var init_mis_events = function () {
        $(document).on('click','#submitfiltersearch',function (e) {
             var fromdate = $(document).find('#fromdate').val();
             var todate = $(document).find('#todate').val();
            init_mis_tbl(fromdate, todate);

        })
        $(document).on('click','#btn_types_group .btn',function () {
            $('#btn_types_group .btn',document).removeClass('active');
            var this_ = $(this);
            this_.addClass('active');
            init_mis_tbl();
        });
        $(document).on('click','#searchbtn',function () {
           // $('#btn_types_group .btn',document).removeClass('active');
            var this_ = $(this);
            this_.addClass('active');
            init_mis_tbl();
        });
        $(document).on('click','ul.meterstat li a',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            if(dataid == 1){
                $(document).find('.activestats').removeClass('hidden');
            }else if(dataid == 2){
                $(document).find('.activestats').addClass('hidden');
            }

            init_mis_tbl();
        });


        tbl_meter_list.on('click', '#btn_del', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr('data-id');
            var this_tr = this_.closest('tr');
            $.ajax({
                url: PECO.base_url() + 'assets/deactivateasset',
                type: 'post',
                data: {id: this_val},
                dataType: 'json',
            }).done(function(){
                this_tr.fadeOut('fast');
            });
        });

        $(document).on('submit', '#frm_release_asset', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: "Asset will be released",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Save!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json',
                    }).done(function (d) {
                        swal("Asset", d.msg, d.func);
                        if(d.qry == true){
                            init_mis_tbl();
                        }
                    }).fail(function() {
                       PECO.phpError();
                       swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });


        $('#datafile', document).fileinput({
            //uploadUrl: url, // server upload action
            uploadAsync: true,
            showBrowse: true,
            browseOnZoneClick: true,
            uploadExtraData: function (d) {
                return {
                    misno: false,
                };
            },
            dropZoneEnabled: false,
            showPreview: false,
        }).on('fileuploaded', function(event, data, previewId, index) {
            if(data.response && data.response.misno > 0) {
                setTimeout(function() {
                    init_mis_tbl();
                }, 500)
            }
        });
    };

    var loadremarkstable = function (dataid) {
        $.ajax({
            url:PECO.base_url()+'assets/getremarkstable',
            type:'post',
            data:{"dataid" : dataid},
            dataType:'json'
        }).done(function (data) {
           // remarkstable
            remarkstable.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: false,
                bProcessing: true,
                scrollY: '200px',
                aaData: data.remarksdata,
                aoColumns: [
                    {data: 'name'},
                    {data: 'remarks'},
                    {data: 'datecreated'},
                ],
                language: PECO.DTEmptyMessage('No remarks record found!'),
                searchHighlight: true,
                order: [[2, 'desc']]
            });
            PECO.dataTableScroller();
        }).fail(function () {
            PECO.phpError();
        });

    };

    var init_view_asset = function (dataid) {

        PECO.select2Basic($('#remarkstype'),'assets/getmisremtypes' , 'Select Type' , false,false,false);
        loadremarkstable(dataid);
        $(document).on('submit','#submitremarks',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "Asset" , data.func);
                if(data.qry == true){
                    this_[0].reset();
                    $(document).find('#remarkstype').select2('val' , '');
                    loadremarkstable(data.dataid);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });


        $('#brand').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2brands',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'assets/editinfo',
            title: 'Modify Brand',
            placeholder: 'Modify Brand',
            inputclass: 'form-control input-large',
            emptytext: 'Select Brand',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });

        loadassetsspecification(dataid);

    };

    var loadassetsspecification = function (dataid) {
        $.ajax({
            url:PECO.base_url()+'assets/getassetsspecifications',
            type:'post',
            data:{"dataid" : dataid},
            dataType:'json'
        }).done(function (data) {

            var x;
            for(x = 0; x < data.specid.length; x++){
                console.log(data.specid[x].val);

                $('#'+data.specid[x].val).editable({
                    url: PECO.base_url() + 'assets/editinfo',
                    title: 'Enter '+data.specid[x].names,
                    inputclass: 'form-control input-large',
                    emptytext: 'Enter '+data.specid[x].names,
                    placeholder: data.specid[x].names,
                    success: function (response, newValue) {
                        if (!response.success)
                           loadremarkstable(dataid);
                            return response.msg;
                    },
                });



            }
        }).fail(function () {
            PECO.phpError();
        });
    };

    return {
        init: function() {
            init_mis_fn();
            init_mis_events();
        },
        newmi:function () {
            init_new_mis();
        },
        viewasset:function (dataid) {
            init_view_asset(dataid);
        }
    }
}();

