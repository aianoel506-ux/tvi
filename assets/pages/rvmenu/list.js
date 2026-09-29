var LIST = function() {
    var tbl_ticket_list = $('#tbl_ticket_list', document);

    var init_hover_editable = function(e) {
        var target = $(e.target);
        var this_tr = target.closest('td');

        this_tr.find('a.label').css('visibility', 'hidden');

        $('#select2_ref', this_tr).attr('type', 'text');
        $('#select2_status', this_tr).attr('type', 'text');

        PECO.select2Basic($('#select2_ref', this_tr), 'cwdo/select2referrals', 'Referrals..', true, false, false, false, true);
        PECO.select2Basic($('#select2_status', this_tr), 'ts/gettsstatus', 'Team..', false, false, false, false, true);
    };

    var init_default_editable = function(e) {
        var target = $(e.target);
        var this_input = $('input', target);

        var this_typeid = this_input.select2('val');
        this_input.select2('destroy');
        this_input.attr('type', 'hidden');

        if(this_typeid>0) {
            $.ajax({
                url: PECO.base_url() + 'ts/gettypestext',
                type: 'post',
                data: {'id': this_typeid},
                dataType: 'json',
                beforeSend: function () {
                    target.find('a')
                        .css('background-color', 'transparent')
                        .css('color', '#000000')
                        .css('visibility', 'visible')
                        .html('<i class="fa fa-spinner fa-pulse fa-spin"></i>');
                }
            }).done(function (d) {
                if (d) {
                    target.find('a')
                        .css('background-color', 'transparent')
                        .css('color', '#000000')
                        .css('visibility', 'visible')
                        .html(d.text);
                } else {
                    if (target.hasClass('circuit') == true && target.closest('tr').find('td.equipments input').val() != '') {
                        $.post(PECO.base_url() + 'ts/checkoutagetype',
                            {'typesid': target.closest('tr').find('td.equipments input').val()},
                            function (data) {
                                if (data.circuit == true) {

                                    target.find('a')
                                        .css('background-color', 'transparent')
                                        .css('color', '#0abebf')
                                        .css('visibility', 'visible')
                                        .html('Select...');
                                } else {
                                    target.find('a')
                                        .css('background-color', 'transparent')
                                        .css('color', '#0abebf')
                                        .css('visibility', 'visible')
                                        .html('<code>N/A</code>');
                                }
                            },
                            'json');
                    } else {
                        target.find('a')
                            .css('background-color', 'transparent')
                            .css('color', '#0abebf')
                            .css('visibility', 'visible')
                            .html('Select..');
                    }
                }
            });
        }else{

            target.find('a')
                .css('background-color', 'transparent')
                .css('color', '#0abebf')
                .css('visibility', 'visible')
                .html('<span class="label label-default"><i class="fa fa-question"></i> N/A</span>');
        }

    };


    var init_list = function() {
        init_list_table();

        tbl_ticket_list.hoverIntent({
            over: init_hover_editable,
            out: init_default_editable,
            selector: 'tbody tr td.editable',
        });

        $(document).on('click', '#btn_refresh_list', function(e){
            e.preventDefault();
            var status = $('#btn_filters button.active').attr('data-id');
            init_list_table(status);
        });

        tbl_ticket_list.on('change', 'tr #select2_ref', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var ticketid = $('input#ticketid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'cwdo/addreferralsrow',
                type: 'post',
                data: {'findingid': this_val, 'ticketid': ticketid},
                dataType: 'json'
            }).done(function(d){
                swal({
                    title: "Accomplishment!",
                    text: "State Comments:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Write something"
                }, function (inputValue) {
                    if (this_val > 0) {
                        $('td.findings', this_tr).html('<input class="form-control inline" id="select2_findings" name="findings[]" value="" />');
                        PECO.select2Basic($('#select2_findings', this_tr), 'ts/selecttcfindings', 'Findings....', true, false);
                        $('#select2_status', this_tr).val(300).trigger('change');
                    } else {
                        $('#select2_status', this_tr).val(307).trigger('change');
                    }
                });
            });
        });

        tbl_ticket_list.on('change', 'tr #select2_status', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            var ticketid = $('input#ticketid', this_tr).val();
            if(this_val == 314 || this_val == 303) {
                swal({
                    title: "Accomplishment!",
                    text: "State Comments:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Write something"
                }, function (inputValue) {
                    /*
                    if (inputValue === false) return false;

                    if (inputValue === "") {
                        swal.showInputError("You need to write something!");
                        return false
                    }
                    */
                    //swal("Nice!", "You wrote: " + inputValue, "success");

                    if (inputValue === false) return false;


                    $.ajax({
                        url: PECO.base_url() + 'ts/accomplishrow',
                        type: 'post',
                        data: {'ticketid': ticketid, 'statusid': this_val, 'remarks': inputValue},
                        dataType: 'json'
                    }).done(function (d) {
                        if (d.qry == true) {
                            this_tr.fadeOut('fast');
                        }
                        swal(d.title, d.msg, d.func);
                    });

                });
            }else{
                $.ajax({
                    url: PECO.base_url() + 'ts/accomplishrow',
                    type: 'post',
                    data: {'ticketid': ticketid, 'statusid': this_val, 'remarks': ''},
                    dataType: 'json'
                }).done(function (d) {
                    if (d.qry == true) {
                        //init_list_table(300, int, config);
                    }
                });
            }
        });

        tbl_ticket_list.on('click', 'tr #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'cwdo/getticketdetails',
                    type: 'post',
                    data: {'id': data_id},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="">' + d.html + '</td></tr>');
                    tbl_ticket_list.find('#loading').remove();

                    //img_stacs(thisTr.next());

                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    tbl_ticket_list.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                tbl_ticket_list.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });

        tbl_ticket_list.on('submit', '#frm_remarks', function(e) {
            e.preventDefault();
            var form = $(this);
            var remarks = $('#remarks', form);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json'
            }).done(function (d) {
                remarks.attr('placeholder', d.placeholder).val('').blur();
            }).fail(function(){
                remarks.blur();
            });
        });
    };




    var init_list_table = function(status) {
        $.ajax({
            url: PECO.base_url() + 'cwdo/getticketlist',
            type: 'post',
            dataType: 'json',
            data: {'status': status, 'complaints': 'rv'},
            beforeSend: function() {
                PECO.DTphpLoading(tbl_ticket_list, 'Loading ticket history...');
            }
        }).done(function (d) {
            tbl_ticket_list.dataTable().empty();
            tbl_ticket_list.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: false,
                aaData: d.list,
                bSort: false,
                //scrollY: '',
                aoColumns: [
                    {"data": "expand", sWidth: '', sClass: 'text-align-center'},
                    {"data": "num", sWidth: '', sClass: 'text-align-center'},
                    {"data": "ticketno", sWidth: '10%', sClass: 'text-primary tcno'},
                    {"data": "name", sWidth: '25%', sClass: 'text-danger tcname'},
                    {"data": "acctname", sWidth: '25%', sClass: 'text-info tcaddress'},
                    {"data": "time", sWidth: '', sClass: ''},
                    {"data": "complaints", sWidth: '', sClass: ''},
                    {"data": "codes", sWidth: '10%', sClass: 'codes'},
                    {"data": "remarks", sWidth: '10%', sClass: 'remarks'},
                    {"data": "status", sWidth: '10%', sClass: 'status editable'},
                    {"data": "control", sWidth: '10%', sClass: 'control'},
                ],
                "language": PECO.DTEmptyMessage(),
                fnRowCallback: function(nRow, aData, Index) {
                    PECO.dtExpandBtn(nRow, aData.expand);

                    // CREATE SORT NUMBER
                    var index = Index +1;
                    $('td:eq(1)',nRow).html(index);
                }
            });
        }).fail(function(){
            PECO.DTphpError(tbl_ticket_list, 'Error loading ticket: PHP error!');
        });
    };
    return {
        init: function() {
            init_list();
        }
    }
}();