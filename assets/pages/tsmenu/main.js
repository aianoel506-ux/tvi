var TS = function() {

    // initialize plugins
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();
    PECO.getTypeHeadPlugins();
    PECO.getDTResizableColumn();

    // PECO.getAmsChartPlugins();

    // VARIABLES
    var tbl_ticket_list = $('#tbl_ticket_list', document);

    $(document).on('keypress', '.disabled-submit', function(e) {
        if(e.keyCode == 13) {
            e.preventDefault();
        }
    });


    var $ps_container = $('#ps_container', document);
    var $ps_overlay = $('#ps_overlay', document);
    var $ps_close = $('#ps_close', document);

    var height = screen.height;
    if(height<=768){
        var var_table_scroll_height = '380px';
        var var_table_records = 20;
    }else{
        var var_table_scroll_height = '290px';
        var var_table_records = 25;
    }

    var config = {
        'info': true,
        'paginate': false,
        'filter': true,
        //'scroll': var_table_scroll_height,
        'length' : var_table_records
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

    var init_hover_editable = function(e) {

        var status = $('#btn_filters button.active').attr('data-id');
        if(status!=314) {
            var target = $(e.target);
            var this_tr = target.closest('td');
            //var this_tcid = $('td', this_tr).eq(3).find('input[type=hidden]').val();

            this_tr.find('a.label').css('visibility', 'hidden');

            $('#select2_equipments', this_tr).attr('type', 'text');
            $('#select2_findings', this_tr).attr('type', 'text');
            $('#select2_teams', this_tr).attr('type', 'text');
            $('#select2_status', this_tr).attr('type', 'text');

            PECO.select2Basic($('#select2_equipments', this_tr), 'ts/selecttcequipments', 'Equipments..', true, false, false, false, true);
            PECO.select2Basic($('#select2_findings', this_tr), 'ts/selecttcfindings', 'Findings..', true, false, false, false, true);
            PECO.select2Basic($('#select2_teams', this_tr), 'ts/gettsteamno', 'Team..', false, false, false, false, true);
            PECO.select2Basic($('#select2_status', this_tr), 'ts/gettsstatus', 'Team..', false, false, false, false, true);


            if (this_tr.hasClass('circuit') == true && this_tr.closest('tr').find('td.equipments input').val() != '') {
                $.post(PECO.base_url() + 'ts/checkoutagetype',
                    {'typesid': this_tr.closest('tr').find('td.equipments input').val()},
                    function (data) {
                        if (data.circuit == true) {
                            $('#select2_circuitlevel', this_tr).attr('type', 'text');
                            PECO.select2Basic($('#select2_circuitlevel', this_tr), 'ts/select2circuitlevel', 'Circuit Level..', true, false, false, false, true);
                        } else {
                            $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit a.label').css('visibility', 'visible');
                        }
                    },
                    'json');
            } else {
                $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit a.label').css('visibility', 'visible');
            }
        }
    };

    var init_date_filter = function(int, config) {
        PECO.iCheckRow($('#icheckdatefilter', document), 'square', 'red');
        var sname = $('#search_name', document).val();
        var saddr = $('#search_addr', document).val();

        $(document).on('ifChecked', '#icheckdatefilter', function () {
            var this_ = $(this);
            this_.attr('checked',  '#icheckdatefilter',true);

            var status = $('#btn_filters button.active').attr('data-id');
            if(sname != '' || saddr != '') {
                init_list_table(status, int, config, true, false, false, true);
            }else {
                init_list_table(status, int, config, true);
            }
        }).on('ifUnchecked', function () {
            var this_ = $(this);
            this_.attr('checked',  '#icheckdatefilter',false);

            var status = $('#btn_filters button.active').attr('data-id');
            if(sname != '' || saddr != '') {
                init_list_table(status, int, config, true, false, false, true);

            }else {
                init_list_table(status, int, config, true);
            }
        });

        $(document).on('keyup', '.filter-dates', function(e) {
            e.preventDefault();

            var status = $('#btn_filters button.active').attr('data-id');

            if(sname != '' || saddr != '') {
                init_list_table(status, int, config, true, false, false, true);
            }else {
                init_list_table(status, int, config, true);
            }
        });
    };

    var init_trouble_call = function(int) {

        $('body').addClass('page-sidebar-closed');
        $('.page-sidebar-menu').addClass('page-sidebar-menu-closed');


        $(document).on('keypress', function(e) {
            var keycode = ( e.keyCode ? e.keyCode : e.which );
            if (keycode === 27) {
                e.preventDefault();
                var status = $('#btn_filters button.active').attr('data-id');
                init_list_table(status, int, config);
            }
        });

        $('#btn_list_limit', document).click(function(e){
            e.preventDefault();
            var status = $('#btn_filters button.active').attr('data-id');
            init_list_table(status, int, config);
        });

        $(document).on('keypress', '.search-submit', function(e) {
            if(e.keyCode==13){
                e.preventDefault();
                $('#btn_search', document).trigger('click');
            }
        });

        init_fast_search(int, config);
        init_date_filter(int, config);

        init_list_table(300, int, config);

        tbl_ticket_list.hoverIntent({
            over: init_hover_editable,
            out: init_default_editable,
            selector: 'tbody tr td',
        });

        setInterval(function(){
            var status = $('#btn_filters button.active').attr('data-id');
            init_list_table(status, int, config);
        }, 600000);


        init_accomplishment_average();
        setInterval(function(){
            init_accomplishment_average();
        }, 600000);

        img_stacs_event();

        $(document).on('click', '#btn_refresh_list', function(e){
            e.preventDefault();
            var status = $('#btn_filters button.active').attr('data-id');

            $('#btn_trouble_call_findings', document).attr('data-arr', '').find("#cnt").text('');
            init_list_table(status, 1, config);
        });

        $('#btn_select_all_tickets', document).click(function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_selected = 0;
            $('tbody tr.list-ticket td.tcno, tbody tr.list-ticket td.tcname, tbody tr.list-ticket td.tcaddress', tbl_ticket_list).each(function(){
                $(this).trigger('click');
                this_selected += 1;
            });
            if(this_selected>0) {
                if ($('.fa', this_).hasClass('fa-square-o')) {
                    $('.fa', this_).removeClass('fa-square-o').addClass('fa-square text-success');
                } else {
                    $('.fa', this_).removeClass('fa-square text-success').addClass('fa-square-o');
                }
            }

        });

        tbl_ticket_list.on('click', 'tbody str.list-ticket td.number', function(e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_teamid  = $('td.team input', this_tr).select2('val');
            var this_tcid = $('#ticketid', this_tr).val();
            if(this_teamid>0) {
                alert('Team: ' + this_teamid + ' Ticket ID: ' + this_tcid);
                $.ajax({
                    url: PECO.base_url() + 'ts/teamqueue',
                    type: 'post',
                    data: {'teamid': this_teamid, 'tcid': this_tcid},
                    dataType: 'json',
                }).done(function(d) {
                    var status = $('#btn_filters button.active').attr('data-id');
                    init_list_table(status, 1, config);
                });
            }else{
                PECO.initAlerts('Cannot Queue this when the team is not assign yet!', 'PECO.net Troube Call', 'info', 3000);
            }
        });


        tbl_ticket_list.on('click', 'tbody tr .queue-btn', function(){
            var this_btn_ = $(this);
            var this_btn_tr = this_btn_.closest('tr');
            this_btn_tr.on('submit', '.popover #frm_submit_queue', function(e) {
                var form = $(this);
                e.preventDefault();
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                }).done(function(d) {
                    var status = $('#btn_filters button.active').attr('data-id');
                    init_list_table(status, 1, config);
                    this_btn_.html(d.num);
                });
                e.stopImmediatePropagation();
            });
        });


        tbl_ticket_list.on('click', 'tbody tr td.status .label', function(e){
            e.preventDefault();

            var this_ = $(this);
            var this_tr = this_.closest('tr');
            this_.remove();
            $('#select2_status_adm', this_tr).attr('type', 'text');
            PECO.select2Basic($('#select2_status_adm', this_tr), 'ts/gettsstatus', 'Status..', false, false, this_.val(), false, true);

        });

        tbl_ticket_list.on('click', 'tbody tr.list-ticket td.tcno, tbody tr.list-ticket td.tcname, tbody tr.list-ticket td.tcaddress', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            $('td', this_tr).toggleClass("active");


            var checkBoxes = $("#ticketidhid", this_tr);
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));

            this_tr.toggleClass('selected');
            var selected_arr = [];
            var selected_cnt = 0;
            $('tbody tr.selected input#ticketid',tbl_ticket_list).each(function() {
                var this_selected = $(this);
                var this_val = this_selected.val();
                selected_arr.push(this_val);
                selected_cnt += 1;
            });
            var selected_ids = selected_arr.join();

            $('#btn_trouble_call_findings',document).attr('data-arr', selected_ids);
            if(selected_cnt>0) {
                $('#btn_trouble_call_findings #cnt', document).text(selected_cnt);
            }else{
                $('#btn_trouble_call_findings #cnt', document).text('');
            }

            PECO.select2Basic($('#select_group', document), 'ts/getselect2group', 'Select group..');


        });

        $('#btn_filters').on('click', 'button', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr('data-id');
            init_list_table(this_val, int, config, true);
            $('#btn_filters button').removeClass('active');

            $('#btn_trouble_call_findings', document).attr('data-arr', '').find("#cnt").text('');
            this_.addClass('active');
        });


        tbl_ticket_list.on('keyup', 'tr #etc_input', function(e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            if(this_tr.hasClass('list-ticket')) {
                var ticketid = $('input#ticketid', this_tr).val();
                $.ajax({
                    url: PECO.base_url() + 'ts/updateetcrow',
                    type: 'post',
                    data: {'etc': this_val, 'ticketid': ticketid},
                    dataType: 'json'
                }).done(function(d){

                }).fail(function(){
                    PECO.phpError();
                });
            }else{
                var groupid = $('input#groupid', this_tr).val();
                $.ajax({
                    url: PECO.base_url() + 'ts/updateetcrowgroup',
                    type: 'post',
                    data: {'etc': this_val, 'groupid': groupid},
                    dataType: 'json'
                }).done(function(d){

                }).fail(function(){
                    PECO.phpError();
                });
            }
        });


        tbl_ticket_list.on('change', 'tr.list-ticket #select2_ref', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var ticketid = $('input#ticketid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/addequipmentsrow',
                type: 'post',
                data: {'equipid': this_val, 'ticketid': ticketid},
                dataType: 'json'
            }).done(function(d){
                if(this_val>0) {
                    $('td.findings', this_tr).html('<input class="form-control inline" id="select2_findings" name="findings[]" value="" />');
                    PECO.select2Basic($('#select2_findings', this_tr), 'ts/selecttcfindings', 'Findings....', true, false);
                    $('#select2_status', this_tr).val(311).trigger('change');

                    if(d.circuit==true) {
                        $('#select2_circuitlevel', this_tr).select2('destroy').attr('type', 'text');
                        PECO.select2Basic($('#select2_circuitlevel', this_tr), 'ts/select2circuitlevel', 'Circuit Level..', true, false, false, false, true);
                        $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit a').remove();
                    }else{
                        $('#select2_circuitlevel', this_tr).select2('destroy').attr('type', 'hidden');
                        $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit a').html('<code>N/A</code>');
                    }
                }else{
                    $('#select2_findings', this_tr).val('').trigger('change');
                    $('#select2_status', this_tr).val(377).trigger('change');

                    $('#select2_circuitlevel', this_tr).select2('destroy').attr('type', 'hidden');
                    $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit span').html('<code>N/A</code>');

                }
            });
        });

        tbl_ticket_list.on('change', 'tr.list-group #select2_equipments', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var groupid = $('input#groupid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/addequipmentsrowgroup',
                type: 'post',
                data: {'equipid': this_val, 'groupid': groupid},
                dataType: 'json'
            }).done(function(d){
                if(this_val>0) {
                    $('td.findings', this_tr).html('<input class="form-control inline" id="select2_findings" name="findings[]" value="" />');
                    PECO.select2Basic($('#select2_findings', this_tr), 'ts/selecttcfindings', 'Findings....', true, false);
                    $('#select2_status', this_tr).val(311).trigger('change');

                    if(d.circuit==true) {
                        $('#select2_circuitlevel', this_tr).select2('destroy').attr('type', 'text');
                        PECO.select2Basic($('#select2_circuitlevel', this_tr), 'ts/select2circuitlevel', 'Circuit Level..', true, false, false, false, true);
                        $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit a').remove();
                    }else{
                        $('#select2_circuitlevel', this_tr).select2('destroy').attr('type', 'hidden');
                        $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit a').html('<code>N/A</code>');
                    }
                }else{
                    $('#select2_findings', this_tr).val('').trigger('change');
                    $('#select2_status', this_tr).val(377).trigger('change');
                    $('#select2_circuitlevel', this_tr).select2('destroy').attr('type', 'hidden');
                    $('#select2_circuitlevel', this_tr).closest('tr').find('td.circuit a').html('<code>N/A</code>');
                }
            });
        });

        tbl_ticket_list.on('change', 'tr.list-ticket #select2_findings', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var ticketid = $('input#ticketid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/addfindingsrow',
                type: 'post',
                data: {'findingid': this_val, 'ticketid': ticketid},
                dataType: 'json'
            }).done(function(d){
            });
        });

        tbl_ticket_list.on('change', 'tr.list-group #select2_findings', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var groupid = $('input#groupid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/addfindingsrowgroup',
                type: 'post',
                data: {'findingid': this_val, 'groupid': groupid},
                dataType: 'json'
            }).done(function(d){
            });
        });

        tbl_ticket_list.on('change', 'tr.list-ticket #select2_circuitlevel', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var ticketid = $('input#ticketid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/addcircuitrow',
                type: 'post',
                data: {'circuitid': this_val, 'ticketid': ticketid},
                dataType: 'json'
            }).done(function(d){
                console.log(d);
            });
        });

        tbl_ticket_list.on('change', 'tr.list-group #select2_circuitlevel', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var groupid = $('input#groupid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/addcircuitrowgroup',
                type: 'post',
                data: {'circuitid': this_val, 'groupid': groupid},
                dataType: 'json'
            }).done(function(d){
                console.log(d);
            });
        });

        tbl_ticket_list.on('change', 'tr.list-ticket #select2_teams', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var ticketid = $('input#ticketid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/assigntoteamrow',
                type: 'post',
                data: {'teamno': this_val, 'ticketid': ticketid},
                dataType: 'json'
            }).done(function(d){
                if(this_val>0) {
                    if(this_val==466) {
                        var status_val = 1007;
                    }else {
                        var status_val = 377;
                    }
                    $('#select2_status', this_tr).val(status_val).trigger('change');
                    $('td.equipments', this_tr).html('<input class="form-control inline" id="select2_equipments" name="findings[]" value="" />');
                    PECO.select2Basic($('#select2_equipments', this_tr), 'ts/selecttcequipments', 'Equipments....', true, false);
                }else{
                    $('td.equipments', this_tr).html('Not Assign');
                    $('#select2_status', this_tr).val(300).trigger('change');
                }
            });
        });

        tbl_ticket_list.on('change', 'tr.list-group #select2_teams', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            var this_tr = this_.closest('tr');
            var ticketid = $('input#groupid', this_tr).val();
            $.ajax({
                url: PECO.base_url() + 'ts/assigntoteamrowgroup',
                type: 'post',
                data: {'teamno': this_val, 'groupid': ticketid},
                dataType: 'json'
            }).done(function(d){
                if(this_val>0) {
                    if(this_val==466) {
                        var status_val = 1007;
                    }else {
                        var status_val = 377;
                    }
                    $('#select2_status', this_tr).val(status_val).trigger('change');
                    $('td.equipments', this_tr).html('<input class="form-control inline" id="select2_equipments" name="findings[]" value="" />');
                    PECO.select2Basic($('#select2_equipments', this_tr), 'ts/selecttcequipments', 'Equipments....', true, false);
                }else{
                    $('td.equipments', this_tr).html('Not Assign');
                    $('#select2_status', this_tr).val(300).trigger('change');
                }
            });
        });

        tbl_ticket_list.on('change', 'tr.list-ticket #select2_status', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            var ticketid = $('input#ticketid', this_tr).val();
            if(
                this_val == 314 ||
                this_val == 303 ||
                this_val == 1025 ||
                this_val == 1028 ||
                this_val == 1015 ||
                this_val == 1017 ||
                this_val == 1018 ||
                this_val == 1016
            ) {
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

        tbl_ticket_list.on('change', 'tr.list-ticket #select2_status_adm', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            var ticketid = $('input#ticketid', this_tr).val();
            if(this_val == 303) {
                swal({
                    title: "Cancelation!",
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
                        this_tr.fadeOut('fast');
                    }
                });
            }
        });

        tbl_ticket_list.on('change', 'tr.list-group #select2_status', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            var groupid = $('input#groupid', this_tr).val();
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
                        url: PECO.base_url() + 'ts/accomplishrowgroup',
                        type: 'post',
                        data: {'groupid': groupid, 'statusid': this_val, 'remarks': inputValue},
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
                    url: PECO.base_url() + 'ts/accomplishrowgroup',
                    type: 'post',
                    data: {'groupid': groupid, 'statusid': this_val, 'remarks': ''},
                    dataType: 'json'
                }).done(function (d) {
                    if (d.qry == true) {
                        //init_list_table(300, int, config);
                    }
                });
            }
        });

        tbl_ticket_list.on('change', 'tr.list-group #select2_status_adm', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            var groupid = $('input#groupid', this_tr).val();
            if( this_val == 303) {
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
                        url: PECO.base_url() + 'ts/accomplishrowgroup',
                        type: 'post',
                        data: {'groupid': groupid, 'statusid': this_val, 'remarks': inputValue},
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
                    url: PECO.base_url() + 'ts/accomplishrowgroup',
                    type: 'post',
                    data: {'groupid': groupid, 'statusid': this_val, 'remarks': ''},
                    dataType: 'json'
                }).done(function (d) {
                    if (d.qry == true) {
                        this_tr.fadeOut('fast');
                    }
                });
            }
        });

//PECO.select2Basic($('#select_teamno', document), 'ts/gettsteamno', 'Select team..');
        PECO.select2Basic($('#select_group', document), 'ts/getselect2group', 'Select group..');

//PECO.dtSubDetails(tbl_ticket_list, 'ts/getticketdetails');

        tbl_ticket_list.on('click', 'tr.list-ticket #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'ts/getticketdetails',
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





        tbl_ticket_list.on('click', 'tr.list-group #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'ts/getgrouplist',
                    type: 'post',
                    data: {'id': data_id, 'int': 1},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');
                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td  style="padding-left: 24px !important; padding-bottom: 15px !important;"  colspan="' + thisTr_child + '" class="sub-table">' + d.html + '</td></tr>');
                    tbl_ticket_list.find('#loading').remove();

                    var sub_details_datatable = thisTr.next('tr').find('#tbl_sub_details');

                    sub_details_datatable.dataTable({
                        bDestroy: true,
                        bPaginate: true,
                        bFilter: true,
                        bInfo: true,
                        bSort: true,
                        "searchHighlight": true,
                        "language": PECO.DTEmptyMessage(),
                        "order": [ 1, 'desc' ],
                    });

                    sub_details_datatable.on('click', 'tr #btn-expand', function () {
                        var this_ = $(this);
                        var thisTr = this_.closest('tr');
                        var thisTr_child = thisTr.children('td').length;
                        var data_id = this_.attr('data-id');
                        if (this_.hasClass('expanded') == false) {
                            thisTr.next('#error').remove();
                            this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                            $.ajax({
                                url: PECO.base_url()+'ts/getticketdetails',
                                type: 'post',
                                data: {'id': data_id},
                                dataType: 'json',
                                beforeSend: function () {
                                    thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                                }
                            }).done(function(d){
                                thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="">' + d.html + '</td></tr>');
                                tbl_ticket_list.find('#loading').remove();
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


        $(document).on('click', '#btn_delete_group_list_row', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr('data-id');
            var this_group = this_.attr('data-group');
            swal({
                title: "Confirm Action",
                text: 'Please confirm action, remove from group',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'ts/removelistgroup',
                        type:'post',
                        data:{'ticketid': this_val, 'groupid': this_group},
                        dataType:'json'
                    }).done(function (d) {
                        if(d.qry==true) {
                            init_list_table(300, int, config);
                        }
                        swal.close();
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        $(document).on('submit', '#frm_join_togroup', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Confirm Action",
                text: 'Please confirm action, join to group!',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:form.attr("action"),
                        type:form.attr("method"),
                        data:form.serialize(),
                        dataType:'json'
                    }).done(function (d) {
                        if(d.qry==true) {
                            init_list_table(300, int, config);
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        $('#btn_deploy', document).click(function(e) {
            e.preventDefault();
            var selected = {};
            var selected = $.map($('input.icheck:checked', tbl_ticket_list), function(c){
                return c.value;
            });
            swal({
                title: "Confirm Action",
                text: 'Please confirm action, deploy team!',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'ts/teamdeploy',
                        type:'post',
                        data: {'ticketids': selected},
                        dataType:'json'
                    }).done(function (d) {
                        if(d.qry==true) {
                            init_list_table(300, int, config);
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });


        init_followup();
    };


    var init_followup = function() {
        tbl_ticket_list.on('click', 'tr.list-ticket #btn_followup', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var ticketid = this_.attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'ts/followupticket',
                type: 'post',
                data: {'ticketid': ticketid},
                dataType: 'json',
            }).done(function(d) {
                if(d.qry==true) {
                    this_row.addClass('danger');
                }
            }).fail(function(){
                PECO.phpError();
            });
        });
        tbl_ticket_list.on('click', 'tr.list-group #btn_followup', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var groupid = this_.attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'ts/followupgroup',
                type: 'post',
                data: {'groupid': groupid},
                dataType: 'json',
            }).done(function(d) {
                if(d.qry==true) {
                    this_row.addClass('danger');
                }
            }).fail(function(){
                PECO.phpError();
            });
        });
    };

    var init_fast_search = function(int, config) {

        $(document).on('keypress', '.search-submit', function(e) {
            if(e.keyCode==13){
                e.preventDefault();
                $('#btn_search', document).trigger('click');
            }
        });

        $('#btn_search', document).click(function(e){
            e.preventDefault();
            var status = $('#btn_filters button.active').attr('data-id');
            init_list_table(status, int, config, true, false, false, true);
        });
    };


    var init_list_table = function(status, int, config, loading, types, views, search) {
        var view = (views) ? views : 'ts';
        var type = (types) ? types : false;
        var total_tc = 0;
        var status_ = (status) ? status : false;
        var int_ = (int) ? int : false;
        var config_ = (config) ? config : false;
        var loading_ = (loading) ? true : false;
        var search_ = (search) ? 1 : 0;

        var paginate = (config_ && config_['paginate'] != 'undefined') ? config_['paginate'] : false;
        var filter = (config_ && config_['filter'] != 'undefined') ? config_['filter'] : true;
        var info = (config_ && config_['info'] != 'undefined') ? config_['info'] : false;
        var length = (config_ && config_['length'] != 'undefined') ? config_['length'] : 10;
        var scroll = (config_ && config_['scroll'] != 'undefined') ? config_['scroll'] : false;

        var list_limit = $('#list_limit', document).val();

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

        // #########################################################
        $('#btn_trouble_call_findings', document).attr('data-arr', '').find("#cnt").text('');

        //setTimeout(function(){
        $.ajax({
            url: PECO.base_url() + 'ts/gettroublecalllist',
            type: 'post',
            dataType: 'json',
            data: {
                'status': status_,
                'complaints': view,
                'int': int_,
                'types': type,
                'limit': list_limit,
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
                    tbl_ticket_list.dataTable().empty();
                    PECO.DTphpLoading(tbl_ticket_list, 'Loading Trouble call listing...');
                }
            }
        }).done(function (d) {
            if(d.followupcnt>0) {
                $('#followupcnt', document).html(d.followupcnt);
            }

            var order_ = (status_==314) ? 'desc' : 'asc';

            total_tc = d.tccnt;

            $('#trouble_call_count').html(total_tc);

            //tbl_ticket_list.DataTable().empty();
            var oTable = tbl_ticket_list.DataTable({
                bDestroy: true,
                bPaginate: paginate,
                bFilter: filter,
                bInfo: info,
                aaData: d.list,
                bSort: true,
                pageLength: length,
                saveState: true,
                aoColumns: [
                    {"data": "expand", sWidth: '', sClass: 'text-align-center'},
                    {"data": "num", sWidth: '', sClass: 'text-align-center'},
                    {"data": "queue", sWidth: '', sClass: 'text-align-center'},
                    {"data": "ticketno", sWidth: '10%', sClass: 'text-primary tcno'},
                    {"data": "name", sWidth: '', sClass: 'text-danger tcname'},
                    {"data": "address", sWidth: '35%', sClass: 'text-info tcaddress'},
                    {"data": "time", sWidth: '', sClass: ''},
                    {"data": "complaints", sWidth: '', sClass: ''},
                    {"data": "team", sWidth: '10%', sClass: 'team'},
                    {"data": "tcequipments", sWidth: '15%', sClass: 'equipments'},
                    {"data": "tcfindings", sWidth: '15%', sClass: 'findings'},
                    {"data": "circuit", sWidth: '15%', sClass: 'circuit'},
                    {"data": "etc", sWidth: '15%', sClass: 'etc'},
                    {"data": "status", sWidth: '10%', sClass: 'status'},
                    {"data": "control", sWidth: '10%', sClass: 'contols'},
                ],

                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),
                "sDom": "Rlfrtip",
                "aaSorting": [ [3,order_], [2,'asc'] ],
                "fnRowCallback": function(nRow, aData, Index) {
                    if(aData.listtype == 'group') {
                        $(nRow).addClass('list-group');
                    }else{
                        $(nRow).addClass('list-ticket');
                    }

                    $(nRow).addClass(aData.followup);

                    PECO.dtExpandBtn(nRow, aData.expand);
                    //(elem, url, placeholder, full, allowall, selectedval, labeled)
                    var equipment_id = aData.tcequipmentid;
                    var finding_id = aData.tcfindingid;
                    var circuit_id = aData.circuitid;
                    var team_id = aData.teamid;
                    //PECO.select2Basic($('#select2_equipments', nRow), 'ts/selecttcequipments', 'Equipments..', true, false, equipment_id, false, true);
                    //PECO.select2Basic($('#select2_findings', nRow), 'ts/selecttcfindings', 'Findings..', true, false, finding_id, false, true);
                    //PECO.select2Basic($('#select2_teams', nRow), 'ts/gettsteamno', 'Team..', false, false, team_id, false, true);
                    //PECO.select2Basic($('#select2_status', nRow), 'ts/gettsstatus', 'Status..', false, false, team_id, false, true);
                    //PECO.select2Basic($('#select2_circuitlevel', nRow), 'ts/select2circuitlevel', 'Circuit Level..', true, false, circuit_id, false, true);
                    PECO.iCheckRow($('.icheck', nRow), 'minimal', 'blue');


                    $('#etc_input', nRow).tooltip();
                    // CREATE SORT NUMBER
                    var index = Index + 1;
                    $('td:eq(1)', nRow).html(index);

                    var text_queue_class = '';
                    if(aData.queue>0) {
                        text_queue_class = 'text-danger text-bold';
                    }
                    var types;
                    if($(nRow).hasClass('list-group')){
                        types = 2;
                    }else{
                        types = 1;
                    }
                    var index = Index + 1;
                    var tcid = $(nRow).find('td').eq(3).find('input[type=hidden]').val();
                    var queue_form = ''
                        + '<form id=\'frm_submit_queue\' action=\'' + PECO.base_url() + 'ts/teamqueue' + '\' method=\'post\'>'
                        + '<input type=\'hidden\' class=\'form-control\' name=\'teamid\' value=\'' + team_id + '\'>'
                        + '<input type=\'hidden\' class=\'form-control\' name=\'tcid\' value=\'' + tcid + '\'>'
                        + '<input type=\'hidden\' class=\'form-control\' name=\'types\' value=\'' + types + '\'>'
                        + '<div class=\'input-group\'>'
                        + '<input type=\'text\' class=\'form-control\' name=\'queue\' placeholder=\'Queue Num.\'>'
                        + '<span class=\'input-group-btn\'><button class=\'btn btn-primary\'>Save</button></span>'
                        + '</div>'
                        + '</form>';
                    var a = '<a class="popovers queue-btn '+text_queue_class+'" href="javascript:;" title="Team Queue <button type=\'button\' aria-hidden=\'true\' class=\'close\'> &times;</button>" data-trigger="click" data-content="' + queue_form + '" data-placement="right" data-original-title="Team Queue">'+aData.queue+'</a>'
                    //$('td:eq(2)', nRow).html(a);

                    $('.popovers', nRow).each(function(){
                        PECO.popOverRow($(this), true, true, 'popover-danger');
                    });



                },
                drawCallback: function() {
                },
            });

            //PECO.initDTNicescroller();

            $('.dataTables_length select.form-control').select2();



        }).fail(function(){
            PECO.DTphpError(tbl_ticket_list, 'Error loading ticket: PHP error!');
        });
        //}, 250);
        return false;
    };




    var init_accomplishment = function() {
        var ts_frm_accomplishment = $('#ts_frm_accomplishment', document);
        var frm_ts_search = $('#frm_ts_search', document);
        var opt_search_action = $('#opt_search_action', document);
        var search_label = $('#search_label', document);
        var input_search_text = $('#input_search_text', document);
        var input_search_type = $('#input_search_type', document);

        input_search_type.val(1); // FIREFOX AUTO FILL
        input_search_text.val('TC No.'); // FIREFOX AUTO FILL

        $('#accomp_tcno', document).val('');

        opt_search_action.on('click', 'li', function() {
            var this_ = $(this);
            var this_val = this_.attr('data-id');
            var text_ = $('a', this).text();
            search_label.text(text_);
            input_search_type.val(this_val)
            input_search_text.val(text_)
        });

        frm_ts_search.submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var this_fa = $('.fa.search-stat', form);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function(){
                    this_fa.removeClass('fa-search').addClass('fa-spinner fa-spin fa-pulse');
                }
            }).done(function(d){
                if(d.qry==false) {
                    swal(d.title, d.msg, d.func);
                }
                $('#accomp_tcno', document).val(d.tcno);
                $('#comp_name', document).html(d.complainants);
                $('#comp_dist', document).html(d.district);
                $('#comp_landmarks', document).html(d.landmarks);
                $('#comp_compstated', document).html(d.reportstated);
                $('#comp_datecreated', document).html(d.datecreated);
                $('#comp_createdby', document).html(d.createdby);
                $('#comp_status', document).html(d.status);

                $('#comp_teams', document).html(d.teams);
                this_fa.removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-search');
            }).fail(function(){
                this_fa.removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-search');
            });
        });

        ts_frm_accomplishment.submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function(){

                }
            }).done(function(d){
                swal(d.title, d.msg, d.func);
            }).fail(function() {
                swal('PHP 404', 'Error PHP', 'error');
            });
        });

        PECO.select2Basic($('#select2tsfindings', document), 'ts/selecttsfinding', 'Select TC Findings..', true);
        $('#select_accomp_type').select2(
            {
                'placeholder': "Select accomplishment..",
                'allowClear': true,
            }
        );
    };

    var init_trouble_logs = function(dataid) {
        init_tbl_trouble_logs(dataid);
    };

    var init_tbl_trouble_logs = function(dataid) {
        var tbl_ticket_log = $('#tbl_ticket_log');
        $.ajax({
            url: PECO.base_url() + 'ts/gettslogs',
            type: 'post',
            data: {'ticketid': dataid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_ticket_log, 'Loading Trouble Call logs...');
            }
        }).done(function(d) {
            tbl_ticket_log.dataTable().empty();
            tbl_ticket_log.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                //scrollY: '',
                aoColumns: [
                    {"data": "sysid", sWidth: '', sClass: 'number'},
                    {"data": "codes", sWidth: '', sClass: 'text-primary'},
                    {"data": "action", sWidth: '', sClass: 'text-bold'},
                    {"data": "descs", sWidth: '', sClass: 'text-bold'},
                    {"data": "remarks", sWidth: '', sClass: ''},
                    {"data": "datecreated", sWidth: '', sClass: ''},
                    {"data": "createdby", sWidth: '', sClass: 'team'}
                ],
                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),

                "order": [ 5, 'desc' ],
            });
        });
    };

    var init_list = function(int) {
        var int_ = (int) ? int : false;

        init_followup();

        var loading = $('#loading', document);

        var height = screen.height;
        if(height <= 768){
            var var_table_scroll_height = '410px';
            var var_table_records = 20;
        }else{
            var var_table_scroll_height = '500px';
            var var_table_records = 25;
        }

        var config = {
            'info': true,
            'paginate': false,
            'filter': true,
            'scroll': var_table_scroll_height,
            'length' : var_table_records
        };

        $('#btn_filters', document).on('click', 'button', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr('data-id');
            init_list_table(this_val, false, config, true);
            $('#btn_filters button').removeClass('active');

            $('#btn_trouble_call_findings', document).attr('data-arr', '').find("#cnt").text('');
            this_.addClass('active');

            loading.removeClass('hidden');

            setTimeout(function(){

                loading.addClass('hidden');
            }, 500);
        });

        $('#btn_list_limit', document).click(function(e){
            e.preventDefault();
            var status = $('#btn_filters button.active').attr('data-id');
            init_list_table(status, int, config);
        });


        init_fast_search(int, config);
        init_date_filter(int, config);

        init_accomplishment_average();
        setInterval(function(){
            init_accomplishment_average();
        }, 600000);

        // PECO.dtSubDetails(tbl_ticket_list, 'ts/getticketdetails', {'view': 'basic'});
        $('[data-toggle=tab]').on('shown.bs.tab', function(e) {
            var tbl = $('#tbl_accomp_tc_list', document);
            var this_ = $(this);
            var target = this_.attr('href');
            if (target == '#accomp') {
                var status = $('#btn_filters button.active').attr('data-id');
                $.ajax({
                    url: PECO.base_url() +'ts/gettcaveragelist',
                    type: 'post',
                    data: {'status': status, 'table': true},
                    dataType: 'json',
                    beforeSend: function() {
                        PECO.DTphpLoading(tbl, 'Loading accomplishments..');
                    }
                }).done(function(d) {
                    tbl.DataTable({
                        bDestroy: true,
                        bPaginate: false,
                        bFilter: true,
                        bInfo: true,
                        aaData: d.list,
                        bSort: true,
                        pageLength: 50,
                        saveState: true,
                        "order": [[ 0, "desc" ]],
                        aoColumns: [
                            {"data": "tcno", sWidth: '', sClass: 'number'},
                            {"data": "name", sWidth: '', sClass: ''},
                            {"data": "created", sWidth: '200px', sClass: 'text-align-center'},
                            {"data": "updated", sWidth: '200px', sClass: 'text-align-center'},
                            {"data": "diffsecond", sWidth: '', sClass: 'number'},
                            {"data": "diffmin", sWidth: '', sClass: 'number'},
                            {"data": "diffhour", sWidth: '', sClass: 'number'},
                            {"data": "equipment", sWidth: '', sClass: ''},
                            {"data": "findings", sWidth: '', sClass: ''},
                            {"data": "circuit", sWidth: '', sClass: ''},
                            {"data": "action", sWidth: '', sClass: ''},
                            {"data": "shift", sWidth: '', sClass: ''},
                        ],
                    });
                });
            }
        });


        img_stacs_event();

        tbl_ticket_list.on('click', 'tr.list-ticket #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'ts/getticketdetails',
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



        tbl_ticket_list.on('click', 'tr.list-group #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'ts/getgrouplist',
                    type: 'post',
                    data: {'id': data_id},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');
                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td  style="padding-left: 24px !important; padding-bottom: 15px !important;"  colspan="' + thisTr_child + '" class="sub-table">' + d.html + '</td></tr>');
                    tbl_ticket_list.find('#loading').remove();

                    var sub_details_datatable = thisTr.next('tr').find('#tbl_sub_details');

                    sub_details_datatable.on('click', 'tr #btn-expand', function () {
                        var this_ = $(this);
                        var thisTr = this_.closest('tr');
                        var thisTr_child = thisTr.children('td').length;
                        var data_id = this_.attr('data-id');
                        if (this_.hasClass('expanded') == false) {
                            thisTr.next('#error').remove();
                            this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                            $.ajax({
                                url: PECO.base_url()+'ts/getticketdetails',
                                type: 'post',
                                data: {'id': data_id},
                                dataType: 'json',
                                beforeSend: function () {
                                    thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                                }
                            }).done(function(d){
                                thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="">' + d.html + '</td></tr>');
                                tbl_ticket_list.find('#loading').remove();


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


                    sub_details_datatable.dataTable({
                        bDestroy: true,
                        bPaginate: true,
                        bFilter: true,
                        bInfo: true,
                        bSort: true,
                        "searchHighlight": true,
                        "language": PECO.DTEmptyMessage(),
                        "order": [ 1, 'desc' ],
                    });

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



        loading.removeClass('hidden');

        setTimeout(function(){
            init_list_table(300, int_, config, true);
            loading.addClass('hidden');
        },1000);

        setInterval(function(){

            var status = $('#btn_filters button.active').attr('data-id');

            init_list_table(status, int_, config);

        }, 300000);

        $(document).on('click', '#btn_refresh_list', function(e){
            e.preventDefault();
            var status = $('#btn_filters button.active').attr('data-id');

            $('#btn_trouble_call_findings', document).attr('data-arr', '').find("#cnt").text('');

            init_list_table(status, false, config);

            loading.removeClass('hidden');

            setTimeout(function(){

                loading.addClass('hidden');
            }, 500);
        });
    };


    var init_modal = function() {

        var this_modal = $('#modal_ajax');

        var select_outage = $('#select_outage', document);
        var select_district = $('#select_district', document);
        var select_priority = $('#select_priority', document);
        var select_landmark = $('#select_landmark', document);
        var select_outages = $('#select_outages', document);

        PECO.select2Basic(select_outage, 'user/getoutage', 'Select Ticket..', true, false, false);
        PECO.select2Basic(select_district, 'user/getdistrictselect', 'Select District..', true, false, false);
        PECO.select2Basic(select_priority, 'user/getpriorityselect', 'Select Priority..', false, false, false);
        PECO.select2Basic(select_outages, 'ts/select2outages', 'Outage Type..', false, false, false);


        select_district.change(function(e){
            var this_ = $(this);
            if(this_.val() != '') {
                select_landmark.val('');
                select_landmark.attr('readonly', false);
                select_landmark.select2({
                    placeholder: 'Landmark..',
                    tags: false,
                    multiple: false,
                    minimumInputLength: 3,
                    //tags: [],
                    ajax: {
                        url: base_url + "user/getlandmark",
                        dataType: 'json',
                        type: "POST",
                        quietMillis: 50,
                        data: function (term) {
                            return {
                                term: term,
                                dist: select_district.val()
                            };
                        },
                        results: function (data) {
                            return {
                                results: $.map(data.list, function (item) {
                                    return {
                                        text: item.text,
                                        id: item.id
                                    };
                                })
                            };
                        }
                    },
                    initSelection: function (element, callback) {
                        /*
                        if (initdata) {
                            callback(initdata);
                        }
                        */
                    },
                    createSearchChoice: function (term, data) {
                        if ($(data.list).filter(function () {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                            return {id: term, text: term + ' - <i class="fa fa-plus text-primary"></i> Add'};
                        }
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    formatResult: PECO.formatDataListBasic, // omitted for brevity, see the source of this page
                    formatSelection: PECO.formatDataSelectionFull, // omitted for brevity, see the source of this page
                }).select2("val", []).select2('open');
            }else{
                select_landmark.val('');
                select_landmark.attr('readonly', true);
            }
        });


        PECO.select2Basic($('#select2_equipments', this_modal), 'ts/selecttcequipments', 'Equipments..', true, false);
        PECO.select2Basic($('#select2_findings', this_modal), 'ts/selecttcfindings', 'Findings..', true, false);
        PECO.select2Basic($('#select2_teams', this_modal), 'ts/gettsteamno', 'Team..', false, false);
        PECO.select2Basic($('#select2_status', this_modal), 'ts/gettsstatus', 'Status..', false, false);
        PECO.select2Basic($('#select2_circuitlevel', this_modal), 'ts/select2circuitlevel', 'Circuit Level..', true, false);

        // ###########################################
        // TROUBLE CALL INFO ENTRY
        var frm_info_entry = $('#frm_info_entry');
        frm_info_entry.submit(function(e) {
            var status = $('#btn_filters button.active').attr('data-id');
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Confirm Action",
                text: 'Please confirm action, adding trouble info!',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr("action"),
                        type: form.attr("method"),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (d) {
                        if(d.qry==true) {
                            init_list_table(status, 1);
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });

        });
    };

    var init_summary = function() {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target === '#summary') {
                init_summary_status_pie();
                init_summary_district_pie();
                init_summary_barangay_cluster();
            }
        });

        PECO.select2Basic($('#select2_export_status', document), 'ts/gettsstatus', 'Status..', false, true, 314);


        $('#filter_barangay_dist', document).on('click', 'li a[data-toggle="tab"]', function(e){
            var this_ = $(this);
            var this_val = this_.attr('data-id');

            init_summary_barangay_cluster(this_val);
        });

        $('#btn_refresh_chart_status', document).click(function(e) {
            e.preventDefault();
            init_summary_status_pie();
        });

        $('#btn_get_status', document).click(function(e) {
            e.preventDefault();
            init_summary_status_pie();
        });

        $('#btn_refresh_chart_dist', document).click(function(e) {
            e.preventDefault();
            init_summary_district_pie();
        });

        $('#btn_refresh_chart_barangay', document).click(function(e) {
            e.preventDefault();
            init_summary_barangay_cluster();
        });
    };

    var init_summary_daily_trends_chart = function() {

        $.ajax({
            url: PECO.base_url() +'ts/getdailytrends',
            type: 'post',
            data: {},
            dataType: 'json'
        }).done(function(d) {

            var chart = AmCharts.makeChart("dailytrends", {
                "type": "serial",
                "theme": "light",
                "marginRight": 10,
                "autoMarginOffset": 20,
                "marginTop": 7,
                "dataProvider": d.trends,
                "valueAxes": [{
                    "axisAlpha": 0.2,
                    "dashLength": 1,
                    "position": "left"
                }],

                "legend": {
                    "useGraphSettings": true
                },

                "mouseWheelZoomEnabled": true,
                "graphs": [
                    {
                        "id": "v1",
                        "lineColor": "#0981f9",
                        "bullet": "round",
                        "bulletBorderThickness": 1,
                        "hideBulletsCount": 30,
                        "title": "Trouble Call",
                        "valueField": "reports",
                        "type": "smoothedLine",
                        "fillAlphas": 0,
                        "balloonText":"<div style='margin:10px; text-align:left;'><span style='font-size:13px'>[[category]]</span><br><span style='font-size:15px'>Trouble Call:[[value]]</span>",
                    },
                    {
                        "id": "g2",
                        "lineColor": "#02db87",
                        "bullet": "round",
                        "bulletBorderThickness": 1,
                        "hideBulletsCount": 30,
                        "title": "Accomplished",
                        "valueField": "accomp",
                        "lineThickness": 2,
                        "fillAlphas": 0.1,
                        "fillColors": "#02db87",
                        "type": "smoothedLine",
                        "balloonText":"<div style='margin:10px; text-align:left;'><span style='font-size:13px'>[[category]]</span><br><span style='font-size:15px'>Accomplished:[[value]]</span>",
                    },
                    {
                        "id": "v3",
                        "bullet": "round",
                        "lineColor": "#ff0000",
                        "bulletBorderThickness": 1,
                        "hideBulletsCount": 30,
                        "title": "Pending",
                        "valueField": "pending",
                        "fillAlphas": 0,
                        "type": "smoothedLine",
                        "balloonText":"<div style='margin:10px; text-align:left;'><span style='font-size:13px'>[[category]]</span><br><span style='font-size:15px'>Pending:[[value]]</span>",
                    },
                ],
                "chartScrollbar": {
                    "autoGridCount": true,
                    "graph": "v1",
                    "scrollbarHeight": 20
                },
                "chartCursor": {
                    "cursorPosition": "mouse"
                },
                "categoryField": "date",
                "categoryAxis": {
                    "parseDates": true,
                    "axisColor": "#DADADA",
                    "minorGridEnabled": true
                },
                "export": {
                    "enabled": true,
                    "position": "bottom-right"
                }
            });


            chart.addListener("dataUpdated", zoomChart);
            zoomChart(chart);

        });



    };

    var init_summary_district_pie = function() {
        $.ajax({
            url: PECO.base_url() +'ts/getdistrictpie',
            type: 'post',
            data: {},
            dataType: 'json'
        }).done(function(d) {
            var chart = AmCharts.makeChart("districtpie", {
                "type": "pie",
                "startDuration": 0,
                "theme": "light",
                "addClassNames": true,
                "legend": {
                    "position": "right",
                    "marginRight": 100,
                    "autoMargins": false
                },
                "innerRadius": "30%",
                "defs": {
                    "filter": [{
                        "id": "shadow",
                        "width": "200%",
                        "height": "200%",
                        "feOffset": {
                            "result": "offOut",
                            "in": "SourceAlpha",
                            "dx": 0,
                            "dy": 0
                        },
                        "feGaussianBlur": {
                            "result": "blurOut",
                            "in": "offOut",
                            "stdDeviation": 5
                        },
                        "feBlend": {
                            "in": "SourceGraphic",
                            "in2": "blurOut",
                            "mode": "normal"
                        }
                    }]
                },
                "dataProvider": d.districts,
                "valueField": "cnt",
                "titleField": "dist",
                "export": {
                    "enabled": true,
                    "position": "bottom-right"
                }
            });

            chart.addListener("init", handleInit);

            chart.addListener("rollOverSlice", function (e) {
                handleRollOver(e);
            });

            function handleInit() {
                chart.legend.addListener("rollOverItem", handleRollOver);
            }

            function handleRollOver(e) {
                var wedge = e.dataItem.wedge.node;
                wedge.parentNode.appendChild(wedge);
            }
        });
    };


    var init_summary_status_pie = function() {
        var date_from = $('#input_satus_from', document).val();
        var date_to = $('#input_satus_to', document).val();
        $.ajax({
            url: PECO.base_url() +'ts/getstatuspie',
            type: 'post',
            data: {'from': date_from, 'to': date_to},
            dataType: 'json'
        }).done(function(d) {
             var chart = AmCharts.makeChart("statuspie", {
                 "type": "pie",
                 "startDuration": 0,
                 "theme": "light",
                 "addClassNames": true,
                 "legend": {
                     "position": "right",
                     "marginRight": 100,
                     "autoMargins": false
                 },
                 "innerRadius": "30%",
                 "defs": {
                     "filter": [{
                         "id": "shadow",
                         "width": "200%",
                         "height": "200%",
                         "feOffset": {
                             "result": "offOut",
                             "in": "SourceAlpha",
                             "dx": 0,
                             "dy": 0
                         },
                         "feGaussianBlur": {
                             "result": "blurOut",
                             "in": "offOut",
                             "stdDeviation": 5
                         },
                         "feBlend": {
                             "in": "SourceGraphic",
                             "in2": "blurOut",
                             "mode": "normal"
                         }
                     }]
                 },
                 "dataProvider": d.status,
                 "valueField": "cnt",
                 "titleField": "status",
                 "colorField": "color",
                 "export": {
                     "enabled": true,
                     "position": "bottom-right"
                 },
             });

             chart.addListener("init", handleInit);

             chart.addListener("rollOverSlice", function (e) {
                 handleRollOver(e);
             });

             function handleInit() {
                 chart.legend.addListener("rollOverItem", handleRollOver);
             }

             function handleRollOver(e) {
                 var wedge = e.dataItem.wedge.node;
                 wedge.parentNode.appendChild(wedge);
             }
        });
    };

    var init_summary_barangay_cluster = function(dist) {
        var dist_ = (dist) ? dist : false;
        $.ajax({
            url: PECO.base_url() +'ts/getbarangaycluster',
            type: 'post',
            data: {'dist': dist_},
            dataType: 'json'
        }).done(function(d) {

            var bchart = AmCharts.makeChart("chartbarangay", {
                "type": "serial",
                "theme": "light",
                "categoryField": "texts",
                "rotate": true,
                "startDuration": 0,
                "categoryAxis": {
                    "gridPosition": "start",
                    "position": "left"
                },
                "marginTop": 20,
                //"colors": ["#FF6600", "#FCD202", "#B0DE09", "#0D8ECF", "#2A0CD0", "#CD0D74", "#CC0000", "#00CC00", "#0000CC", "#DDDDDD", "#999999", "#333333", "#990000"],
                "trendLines": [],
                "graphs": [
                    {
                        "id": "g1",
                        "balloonText":"<div style='margin:10px; text-align:left;'><span style='font-size:13px'>[[category]]</span><br><span style='font-size:15px'>Trouble Call: [[value]]</span>",
                        "fillColor": "#0981f9",
                        "fillAlphas": 0.2,
                        "lineColor": "#0981f9",
                        "bullet": "round",
                        "bulletBorderThickness": 1,
                        "lineThickness": 2,
                        "lineAlpha": 0.8,
                        "title": "Trouble Call",
                        "type": "line",
                        "valueField": "cnt",
                        "autoColor": true
                    },

                    {
                        "id": "g2",
                        "balloonText":"<div style='margin:10px; text-align:left;'><span style='font-size:13px'>[[category]]</span><br><span style='font-size:15px'>Accomplished: [[value]]</span>",
                        "title": "Accomplished",
                        "type": "line",
                        "fillColor": "#02db87",
                        "lineColor": "#02db87",
                        "fillAlphas": 0.2,
                        "valueField": "accomp",
                        "autoColor": true
                    },
                ],
                "guides": [],
                "chartScrollbar": {
                    "autoGridCount": true,
                    "graph": "g1",
                    "scrollbarHeight": 150
                },
                "chartCursor": {
                    "cursorPosition": "mouse"
                },
                "valueAxes": [
                    {
                        "id": "g1",
                        "position": "top",
                        "axisAlpha": 0
                    }
                ],
                "allLabels": [],
                "balloon": {},
                "titles": [],
                "dataProvider": d.barangays,
                "export": {
                    "enabled": true,
                    "position": "top-left"
                }

            });


        });
    };

    var zoomChart = function(chart){
        chart.zoomToIndexes(chart.dataProvider.length - 20, chart.dataProvider.length - 1);
    };

    var init_accomplishment_average = function() {
        $.ajax({
            url: PECO.base_url() +'ts/gettcaveragelist',
            type: 'post',
            data: {},
            dataType: 'json'
        }).done(function(d) {
            $('#general_average', document).html(d.general.ave + ' '+ d.general.unit + '<br>' + d.general.cnt + ' TC');
            $('#shift1_average', document).html(d.shift1.ave + ' '+ d.shift1.unit + ' <br> ' + d.shift1.cnt + ' TC');
            $('#shift2_average', document).html(d.shift2.ave + ' '+ d.shift2.unit + ' <br> ' + d.shift2.cnt + ' TC');
            $('#shift3_average', document).html(d.shift3.ave + ' '+ d.shift3.unit + ' <br> ' + d.shift3.cnt + ' TC');
        });
    };

    var init_line_team_fn = function() {

        init_list_table(1007, true, false, true, 1045); // PRW 1045

        $(document).on('click', '#btn_refresh_list', function(e){
            e.preventDefault();
            init_list_table(1007, true, false, true, 1045); // PRW 1045
        });


        img_stacs_event();


        tbl_ticket_list.on('click', 'tr.list-ticket #btn_crw_prw_accomplished', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = 314;
            var ticketid = $('input#ticketid', this_tr).val();
            swal({
                title: "Accomplishment!",
                text: "State Comments:",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Write something"
            }, function (inputValue) {
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
        });


        tbl_ticket_list.on('click', 'tr.list-group #btn_crw_prw_accomplished', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = 314;
            var groupid = $('input#groupid', this_tr).val();
            swal({
                title: "Accomplishment!",
                text: "State Comments:",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Write something"
            }, function (inputValue) {

                if (inputValue === false) return false;


                $.ajax({
                    url: PECO.base_url() + 'ts/accomplishrowgroup',
                    type: 'post',
                    data: {'groupid': groupid, 'statusid': this_val, 'remarks': inputValue},
                    dataType: 'json'
                }).done(function (d) {
                    if (d.qry == true) {
                        this_tr.fadeOut('fast');
                    }
                    swal(d.title, d.msg, d.func);
                });

            });
        });


        tbl_ticket_list.on('click', 'tr.list-ticket #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'ts/getticketdetails',
                    type: 'post',
                    data: {'id': data_id},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="">' + d.html + '</td></tr>');
                    tbl_ticket_list.find('#loading').remove();

                    // img_stacs(thisTr.next());

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





        tbl_ticket_list.on('click', 'tr.list-group #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'ts/getgrouplist',
                    type: 'post',
                    data: {'id': data_id, 'int': 1},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');
                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td  style="padding-left: 24px !important; padding-bottom: 15px !important;"  colspan="' + thisTr_child + '" class="sub-table">' + d.html + '</td></tr>');
                    tbl_ticket_list.find('#loading').remove();

                    var sub_details_datatable = thisTr.next('tr').find('#tbl_sub_details');

                    sub_details_datatable.dataTable({
                        bDestroy: true,
                        bPaginate: true,
                        bFilter: true,
                        bInfo: true,
                        bSort: true,
                        "searchHighlight": true,
                        "language": PECO.DTEmptyMessage(),
                        "order": [ 1, 'desc' ],
                    });

                    sub_details_datatable.on('click', 'tr #btn-expand', function () {
                        var this_ = $(this);
                        var thisTr = this_.closest('tr');
                        var thisTr_child = thisTr.children('td').length;
                        var data_id = this_.attr('data-id');
                        if (this_.hasClass('expanded') == false) {
                            thisTr.next('#error').remove();
                            this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                            $.ajax({
                                url: PECO.base_url()+'ts/getticketdetails',
                                type: 'post',
                                data: {'id': data_id},
                                dataType: 'json',
                                beforeSend: function () {
                                    thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                                }
                            }).done(function(d){
                                thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="">' + d.html + '</td></tr>');
                                tbl_ticket_list.find('#loading').remove();
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

    };

    var img_stacs_event = function() {
        console.log('Image stacks loaded...');

        $(document).on('click', '#album_thumb', function (e) {
            e.preventDefault();
            var this_ = $(this);

            $('#ps_close').bind('click', function () {
                $ps_container.hide();
                $ps_close.hide();
                $ps_overlay.fadeOut(400);
            });

            var $elem = this_;
            var album_name = $elem.attr('data-id');
            var $loading = $('<div />', {className: 'loading'});
            $elem.append($loading);
            $('img', $ps_container).remove();
            $.post(PECO.base_url() + 'ts/gettcalbum', {album_name: album_name}, function (data) {
                var items_count = data.length;
                $('#ps_container', document).css('position', 'fixed')
                    .css('top', '0px');
                $(document).scrollTop(0);

                for (var i = 0; i < items_count; ++i) {
                    var item_source = data[i].split('./');
                    var item_url = PECO.base_url() + item_source[1];
                    var cnt = 0;
                    /*
                    $('#ps_container', document).append(
                        '<img src="'+item_url+'" />'
                    ).show();
                    */

                    $('<img />', document).load(function () {
                        var $image = $(this);
                        ++cnt;
                        resizeCenterImage($image);
                        $ps_container.append($image);
                        var r = Math.floor(Math.random() * 41) - 20;
                        //if (cnt < items_count) {
                        $image.css({
                            '-moz-transform': 'rotate(' + r + 'deg)',
                            '-webkit-transform': 'rotate(' + r + 'deg)',
                            'transform': 'rotate(' + r + 'deg)',
                            'z-index': '1000'
                        });
                        //}
                        //if (cnt == items_count) {
                        $loading.remove();
                        $ps_container.show();
                        $ps_close.show();
                        $ps_overlay.show();

                        $('#ps_next_photo', document).show();
                        //}
                    }).attr('src', item_url);
                }
            }, 'json');
        });
        $(document).on('click', '#ps_container img', function () {
            var $current = $ps_container.find('img:last');
            var r = Math.floor(Math.random() * 41) - 20;

            var currentPositions = {
                marginLeft: $current.css('margin-left'),
                marginTop: $current.css('margin-top')
            }
            var $new_current = $current.prev();

            $current.animate({
                'marginLeft': '250px',
                'marginTop': '-385px'
            }, 250, function () {
                $(this).insertBefore($ps_container.find('img:first'))
                    .css({
                        '-moz-transform': 'rotate(' + r + 'deg)',
                        '-webkit-transform': 'rotate(' + r + 'deg)',
                        'transform': 'rotate(' + r + 'deg)'
                    })
                    .animate({
                        'marginLeft': currentPositions.marginLeft,
                        'marginTop': currentPositions.marginTop
                    }, 250, function () {
                        $new_current.css({
                            '-moz-transform': 'rotate(0deg)',
                            '-webkit-transform': 'rotate(0deg)',
                            'transform': 'rotate(0deg)'
                        });
                    });
            });
        });
    };

    var resizeCenterImage = function($image) {
        var theImage = new Image();
        theImage.src = $image.attr("src");
        var imgwidth = theImage.width;
        var imgheight = theImage.height;

        var containerwidth = 700;
        var containerheight = 530;

        if (imgwidth > containerwidth) {
            var newwidth = containerwidth;
            var ratio = imgwidth / containerwidth;
            var newheight = imgheight / ratio;
            if (newheight > containerheight) {
                var newnewheight = containerheight;
                var newratio = newheight / containerheight;
                var newnewwidth = newwidth / newratio;
                theImage.width = newnewwidth;
                theImage.height = newnewheight;
            }
            else {
                theImage.width = newwidth;
                theImage.height = newheight;
            }
        }
        else if (imgheight > containerheight) {
            var newheight = containerheight;
            var ratio = imgheight / containerheight;
            var newwidth = imgwidth / ratio;
            if (newwidth > containerwidth) {
                var newnewwidth = containerwidth;
                var newratio = newwidth / containerwidth;
                var newnewheight = newheight / newratio;
                theImage.height = newnewheight;
                theImage.width = newnewwidth;
            }
            else {
                theImage.width = newwidth;
                theImage.height = newheight;
            }
        }
        $image.css({
            'width': theImage.width,
            'height': theImage.height,
            'margin-top': -(theImage.height / 2) - 10 + 'px',
            'margin-left': -(theImage.width / 2) - 10 + 'px'
        });
    };

    var init_cwd_list = function() {
        init_list_table(300, false, false, false, false, 'cwd');
    };

    var init_joborder_fn = function(){
        init_list_table(300, false, false, false, false, 'JO');
    };

    return {
        init: function(int_) {
            init_trouble_call(int_);
        },
        list: function(status, int) {
            //init_list_table(status, int);
            init_list(int);
        },
        accomplishment: function() {
            init_accomplishment();
        },
        logs: function(dataid) {
            init_trouble_logs(dataid);
        },
        modal: function() {
            init_modal();
        },
        summary: function() {
            init_summary();
        },
        lineteam: function() {
            init_line_team_fn();
        },
        joborders: function() {
            init_joborder_fn();
        }
    };
}();
