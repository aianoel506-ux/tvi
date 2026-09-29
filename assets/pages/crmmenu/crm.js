var CRM = function() {

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

        /*
        tbl_ticket_list.hoverIntent({
            over: init_hover_editable,
            out: init_default_editable,
            selector: 'tbody tr td',
        });

         */

        setInterval(function(){
            var status = $('#btn_filters button.active').attr('data-id');
            init_list_table(status, int, config);
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

        $('#btn_filters').on('click', 'button', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr('data-id');
            init_list_table(this_val, int, config, true);
            $('#btn_filters button').removeClass('active');

            $('#btn_trouble_call_findings', document).attr('data-arr', '').find("#cnt").text('');
            this_.addClass('active');
        });

        tbl_ticket_list.on('click', 'tr #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                $.ajax({
                    url: PECO.base_url()+'crm/cdedetails',
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
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
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
        var view = (views) ? views : 'cde';
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
            url: PECO.base_url() + 'crm/cdelist',
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
                    {"data": "expand", sWidth: '10px', sClass: 'text-align-center'},
                    {"data": "num", sWidth: '70px', sClass: 'ticket-no blue text-primary'},
                    {"data": "cdname", sWidth: '130px', sClass: 'cover'},
                    {"data": "info", sWidth: '', sClass: ''},
                    {"data": "address", sWidth: '', sClass: ''},
                    {"data": "timelapse", sWidth: '100px', sClass: ''},
                    {"data": "remarks", sWidth: '', sClass: 'remarks'},
                    {"data": "status", sWidth: '5%', sClass: 'status'},
                    {"data": "control", sWidth: '10%', sClass: 'contols'},
                ],

                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),
                "sDom": "Rlfrtip",
                "aaSorting": [ [3,order_], [2,'asc'] ],
                "fnRowCallback": function(nRow, aData, Index) {
                    PECO.dtExpandBtn(nRow, aData.expand);
                    PECO.iCheckRow($('.icheck', nRow), 'minimal', 'blue');


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


        img_stacs_event();

        tbl_ticket_list.on('click', 'tr #btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
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
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
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
            $.post(PECO.base_url() + 'crm/getcustalbum', {album_name: album_name}, function (data) {
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

    var init_view = function(dataid) {

        init_ajax_view(dataid);

        $(document).on('click', '#btn_refresh_view', function(e) {
            e.preventDefault();
            init_ajax_view(dataid);
        });

        $("a.gallery-btn").fancybox({
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',

            openEffect  : 'fade',
            closeEffect : 'fade',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'overlayShow'	:	false
        });
    };

    var init_ajax_view = function(dataid) {

        var trn_container = $('#trn_container', document);
        PECO.blockUI({
            target: trn_container,
            animate: true,
            overlayColor: '#64A8C8'
        });

        trn_container.load(PECO.base_url() + 'crm/loadview',
            {'id': dataid},
            function(d){
                PECO.unblockUI(el);
            });
    };


    return {
        init: function(int_) {
            init_trouble_call(int_);
        },
        list: function(status, int) {
            init_list(int);
        },
        logs: function(dataid) {
            init_trouble_logs(dataid);
        },
        view: function(dataid) {
            init_view(dataid);
        },
    };
}();
