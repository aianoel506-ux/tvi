/*
    AUTHOR: LUCKY JOHN F. FADERON
    DATE: 9/11/2017
 */

var INQUIRY = function() {
    PECO.getAmsChartPlugins();
    PECO.getSelect2Plugins();
    PECO.getSweetAlert();
    PECO.getiCheckPlugin();

    var frm_new_ticket = $('#frm_new_ticket', document);
    var ar_table = $('#tbl_ar');
    var tbl_pn_file = $('#tbl_pn_file');
    var ar_search_frm = $('#frm_search');
    var tbl_billing_hist = $('#tbl_billing_hist', document);
    var tbl_billhist_rv = $('#tbl_billhist_rv', document);
    var tbl_ticket_history = $('#tbl_ticket_history', document);
    var tbl_payments_applied = $('#tbl_payments_applied', document);

    var select_ticket = $('#select_ticket');
    var select_ticketpart = $('#select_ticketpart');
    var select_district = $('#select_district');
    var select_priority = $('#select_priority');

    var formatDataSelection = function (data) {
        return data.text.split(',', 1);
    };

    var formatData = function (data) {
        if (data.loading)
            return data.name;
        /*
         markup = '<li class="media select-2">'+
         '<a class="pull-left" href="javascript:;">'+
         '<img class="media-object" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCI+PHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iMzIiIHk9IjMyIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEycHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9zdmc+" alt="32x32" data-src="holder.js/32x32" style="width: 32px; height: 32px;">'+
         '</a>'+
         '<div class="media-body">';
         '<p><i class="fa fa-tag"></i><span><b>' + data.text + '</b></span></p>'+
         '<p>'+data.gender+' ' + data.birthday + '<p>'+
         '<p><i class="fa fa-map-marker"></i> <span>'+data.address+'<span></p>'+
         '</div></li>';
         */
        var gender;
        var bday;
        var addr;
        var pics;
        if (data.details == true) {
            gender = (data.gender) ? data.gender : '';
            bday = (data.birthday) ? '<li style="font-size: 11px; margin: 1px 1px !important; padding: 0px 0px !important; line-height: 12px;"> ' + data.birthday + '<li>' : '';
            addr = (data.address) ? '<li style="font-size: 11px; margin: 1px 1px !important; padding: 0px 0px !important; line-height: 12px;"><span>' + data.address + '<span></li>' : '';
        } else {
            gender = '';
            bday = '';
            addr = '';
        }
        pics = (data.pic) ? '<img src="' + PECO.base_url() + data.pic + '" width="100%"/>' : '';
        markup = '<div style="position: relative;">' +
            '<div style="float: left; width: 20%; height: 100%; position: absolute;">' + pics + '</div>' +
            '<ul style="margin: 0px 0px; padding: 0px 0px; background: transparent; position: relative; left: 20%; width: 78%; margin-left: 5px;"><li><span><span style="float: right">' + gender + '</span><b>' + data.text + '</b></span></li>' +
            bday +
            addr +
            '</div>';
        return markup;
    };

    var init_inquiry = function() {

        shortcut.add('F2', function () {
            var servno = $('#servno',document).val();
            if(servno=='') {
                PECO.initAlerts('Please key in Service Number first!', 'PECO.net', 'info');
                return false;
            }
            $( '[data-toggle="tab"][href="#readinghist"]', document).trigger( 'click' );
            return false;
        });

        shortcut.add('F3', function () {
            var servno = $('#servno',document).val();
            if(servno=='') {
                PECO.initAlerts('Please key in Service Number first!', 'PECO.net', 'info');
                return false;
            }
            $( '[data-toggle="tab"][href="#acctdetails"]', document).trigger( 'click' );
            return false;
        });

        shortcut.add('F4', function () {
            var servno = $('#servno',document).val();
            if(servno=='') {
                PECO.initAlerts('Please key in Service Number first!', 'PECO.net', 'info');

                return false;
            }

            select_ticket.select2('val', '');

            var servno = $('#servno',document).val();
            var mtr = $('#mtr',document).val();
            init_ticketing_form();
            init_get_account_info(servno, mtr, $('#compacctid'));

            $( '[data-toggle="tab"][href="#ticket"]', document).trigger( 'click' );
            return false;
        });

        shortcut.add('F5', function(){

            var servno = $('#servno',document).val();
            if(servno=='') {
                PECO.initAlerts('Please key in Service Number first!', 'PECO.net', 'info');
                return false;
            }

            $( '[data-toggle="tab"][href="#pninq"]', document).trigger( 'click' );
            return false;
        });

        shortcut.add('F6', function(){
            var servno = $('#servno',document).val();
            if(servno=='') {
                PECO.initAlerts('Please key in Service Number first!', 'PECO.net', 'info');
                return false;
            }

            $( '[data-toggle="tab"][href="#tagging"]', document).trigger( 'click' );
            return false;
        });

        shortcut.add('F9', function(){
            var servno = $('#servno',document).val();
            if(servno=='') {
                PECO.initAlerts('Please key in Service Number first!', 'PECO.net', 'info');
                return false;
            }

            $( '[data-toggle="tab"][href="#meterhist"]', document).trigger( 'click' );
            return false;
        });

        shortcut.add('F10', function () {
            var servno = $('#servno',document).val();
            if(servno=='') {
                PECO.initAlerts('Please key in Service Number first!', 'PECO.net', 'info');
                return false;
            }


            $( '[data-toggle="tab"][href="#payments"]', document).trigger( 'click' );
            return false;
        });


        $('body').on('keypress', '#servno', function(e) {
            var code = e.keyCode || e.which;
            if(code == 13) {
                $('#mtr').focus();
            }
        });

        PECO.select2Basic(select_ticket, 'user/getticketselect', 'Select Ticket..', false, false, false);
        PECO.select2Basic(select_priority, 'user/getpriorityselect', 'Select Priority..', false, false, false);

        $('#reset', document).click(function(){
            acct_res_default();
        });


        //change event of drop down
        select_ticket.on('change', function(e){
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val>0) {
                select_ticketpart.attr('readonly', false);
                PECO.select2BasicId(select_ticketpart, 'user/getticketpartselect', this_val, false, false, false, false);
            }else{
                select_ticketpart.attr('readonly', true).val('').select2('destroy');
            }

            if(this_val==278) {

                $('.billing').removeClass('hidden');
                $('.services, .payments').addClass('hidden');

                var servno = $('#servno',document).val();
                var mtr = $('#mtr',document).val();

                init_reading_history_rv(servno, mtr);

            } else if(this_val==279) {

                $('.payments').removeClass('hidden');
                $('.services, .billing').addClass('hidden');

            } else if(this_val==280) {

                $('.services').removeClass('hidden');
                $('.payments, .billing').addClass('hidden');


                PECO.employeeSelectTagging($('#empid', document), true);
            }else{

                $('.payments, .billing, .services').addClass('hidden');

            }
        });

        frm_new_ticket.submit(function(e) {
            var form = $(this);
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Adding new ticket',
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
                        data: new FormData(form[0]),
                        processData: false,
                        contentType: false,
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });
        });


        ar_table.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            bStateSave: true,
            scrollY: '300px',
            language: {
                "emptyTable": '<h4><i class="fa fa-warning text warning"></i> No record found! </h4>',
            },
        });
        $('#prev_month').select2({
            'placeholder': 'Select..'
        });
        ar_search_frm.submit(function (e) {
            e.preventDefault();
            $('#tab_ar').trigger('click');
            var form = $(this);
            init_search_form(form);
        });




        $('.ar-tab a').on('shown.bs.tab', function(event){
            var this_ = $(this);
            var servno = $('#servno',document).val();
            var mtr = $('#mtr',document).val();
            var target = this_.attr('href');
            var module_id = this_.attr('data-module');

                if (target == '#pninq') {
                    PECO.DTDefault(tbl_pn_file, 'No P.N. File!');
                }
                if (target == '#acctdetails') {
                    init_other_graph(event);
                }
                if (target == '#payments') {
                    init_payments_applied(servno, mtr);
                }
                if (target == '#readinghist') {
                    init_reading_history(servno, mtr);
                }
                if (target == '#tagging') {
                    init_tagging(module_id);
                }
                if (target == '#meterhist') {
                    init_meter_history(servno, mtr);
                }

        });




        $('#tag_container', document).on('click', 'a .add', function(e) {
            var this_ = $(this);
            var data_id = this_.attr('data-id');

            var data_module_main = $('.ar-tab li.active a').attr('data-module');
            var data_module = this_.attr('data-module');

            var tag_name = this_.closest('div').find('.desc ').text();
            var servno = $('#servno', document).val();
            var mtr = $('#mtr', document).val();
            swal({
                title: "Are you sure?",
                text: tag_name,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Tag!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'systems/taggthis',
                        type: 'post',
                        data: {'moduleid': data_module, 'tagid': data_id, 'servno': servno, 'mtr': mtr},
                        dataType: 'json'
                    }).done(function (d) {
                        init_tagging_details(data_module, d.dataid, d.page, d.title);
                        init_tagging(data_module_main);
                        swal.close();
                    }).fail(function(){
                        swal("Error!", tag_name + " tag error!", "error");
                    });
                } else {
                    swal("Cancelled", tag_name + " tagging cancelled", "error");
                }
            });
        });





        ar_table.on('contextmenu', 'tr', function(e) {
            ar_table.find('tr').removeClass('info');
            var this_ = $(this).find('#month');
            var data_text = this_.text();
            var data_id = this_.attr('data-id');
            var data_month = this_.attr('data-month');
            var data_year = this_.attr('data-year');
            var data_schedid = this_.attr('data-schedid');

            if(data_id) {
                e.preventDefault();
                this_.closest('tr').addClass('info');
                // WRITE THE CONTEXT MENU IN THE PAGE
                var context_menu_list = '<ul id="monthly_context_menu" class="custom-menu">' +
                    '<li style="background: #00A8FF; color: #fff; font-weight: bold;"><i class="fa fa-calendar"></i> '+data_text+'</li>' +
                    '<li data-action="print" data-schedid="'+data_schedid+'" data-id="' + data_id + '" data-month="' + data_month + '" data-year="' + data_year + '"><i class="fa fa-print fa-fw text-primary"></i> Print Actual Bill</li>' +
                    '<li data-action="remarks" data-schedid="'+data_schedid+'" data-id="' + data_id + '" data-month="' + data_month + '" data-year="' + data_year + '"><i class="fa fa-search fa-fw text-primary"></i> Reading Remarks</li>' +
                    '<li data-action="ebill" data-schedid="'+data_schedid+'" data-id="' + data_id + '" data-month="' + data_month + '" data-year="' + data_year + '"><i class="fa fa-envelope fa-fw text-info"></i> Re-Send eBill</li>' +
                    '</ul>';
                $('body').append(context_menu_list);

                // Show contextmenu
                $(".custom-menu").finish().toggle(100).// In the right position (the mouse)
                css({top: e.pageY + "px", left: e.pageX + "px"});

                var windowHeight = $(window).height()/2;
                var windowWidth = $(window).width()/2;
                if(e.clientY > windowHeight && e.clientX <= windowWidth) {
                    $(".custom-menu").css("left", e.clientX);
                    $(".custom-menu").css("bottom", $(window).height()-e.clientY);
                    $(".custom-menu").css("right", "auto");
                    $(".custom-menu").css("top", "auto");
                } /* else if(e.clientY > windowHeight && e.clientX > windowWidth) {
                    //When user click on bottom-right part of window
                    $(".custom-menu").css("right", $(window).width()-e.clientX);
                    $(".custom-menu").css("bottom", $(window).height()-e.clientY);
                    $(".custom-menu").css("left", "auto");
                    $(".custom-menu").css("top", "auto");
                } else if(e.clientY <= windowHeight && e.clientX <= windowWidth) {
                    //When user click on top-left part of window
                    $(".custom-menu").css("left", e.clientX);
                    $(".custom-menu").css("top", e.clientY);
                    $(".custom-menu").css("right", "auto");
                    $(".custom-menu").css("bottom", "auto");
                } else {
                    //When user click on top-right part of window
                    $(".custom-menu").css("right", $(window).width()-e.clientX);
                    $(".custom-menu").css("top", e.clientY);
                    $(".custom-menu").css("left", "auto");
                    $(".custom-menu").css("bottom", "auto");
                }
                */
            }
        });

        $(document).click(function(e){
            if ($(".custom-menu").has(e.target).length === 0) {
                $(".custom-menu").hide(100);
                $('#monthly_context_menu').remove();
                ar_table.find('tr').removeClass('info');
            }
        });

        $('body').on('click', '.custom-menu li', function(e){
            e.preventDefault();
            var bill = $(this);

            // This is the triggered action name
            switch($(this).attr("data-action")) {
                // A case for each action. Your actions here
                case "print":
                    print_actual_billing(bill);
                    break;

                case "ebill":
                    alert('emill');
                    //email_actual_billing(bill);
                    break;
            }
            // Hide it AFTER the action was triggered

        });

        $(document).on('click','#printstatementbtn',function (e) {
           e.preventDefault();
           var form = $(document).find('#frm_search');
           $.ajax({
               url: PECO.base_url() + 'billing/getartbl',
               type: 'post',
               dataType: 'json',
               data: form.serialize()
           }).done(function (d) {
               var html = '';

               //first row
               html += '<div class="row">';


               html += '<div class="col-md-6 col-xs-6">';

               html += '<ul class="list-group summary column no-border list-group-xs">';
               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Name</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.arname+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Address</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.araddr+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Status</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.status+'</span>';
               html += '</li>';
               html += '</ul>';


               html += '</div>';

               html += '<div class="col-md-4 col-xs-4">';

               html += '<ul class="list-group summary column no-border list-group-xs">';
               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">GDLB</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.gdlb+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Rate</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.rate+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">MULT</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.mult+'</span>';
               html += '</li>';
               html += '</ul>';

               html += '</div>';

               html += '<div class="col-md-2 col-xs-2" style="postion: relative !important;">';
               html += '<img height="height: 70px;" style="" src="' + PECO.base_url() + 'query/barcode/' + d.servno + '" />';
               html += '</div>';


               html += '</div>';

               //-----------------------------//

               html += '<div class="row" style="border-top:solid 1px gray;">';

               html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
               html += '<ul class="list-group summary column no-border list-group-xs">';
               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Total Balance</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.amtbal+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Total Interest</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.amtint+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Due</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.amtdue+'</span>';
               html += '</li>';
               html += '</ul>';
               html += '</div>';

               html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';

               html += '<ul class="list-group summary column no-border list-group-xs">';
               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Total Amount Paid</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.amtpd+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Last Pay Date</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.lastpd+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Current</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.amtcur+'</span>';
               html += '</li>';
               html += '</ul>';

               html += '</div>';

               html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';

               html += '<ul class="list-group summary column no-border list-group-xs">';
               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Average KWH</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.avkwh+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">Meter No.</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.mtrno+'</span>';
               html += '</li>';

               html += '<li class="list-group-item">';
               html += '<span class="label-name col-md-5">No. of Bills</span>';
               html += '<span class="label-default col-md-7 number" style="text-align: right">'+d.nobill+'</span>';
               html += '</li>';
               html += '</ul>';

               html += '</div>';

               html += '</div>';

               //--------------------------------

               //table start
               html += '<div class="row" style="margin-top: 10px !important; border-top: 1px solid lightslategray">';
               html += '<div class="col-md-12">';

               html += '<table class="table table-bordered table condensed tbl-xs">';

               html += '<thead>';

                    html += '<tr>';

                    html += '<th rowspan="2">Month</th>';
                    html += '<th rowspan="2">KWH</th>';
                    html += '<th rowspan="2">Bill No.</th>';
                    html += '<th rowspan="2">Amount Due</th>';
                    html += '<th rowspan="2">Due Date</th>';
                    html += '<th rowspan="2">Date Paid</th>';
                    html += '<th rowspan="2">Amount Paid</th>';
                    html += '<th rowspan="2">Interest</th>';
                    html += '<th rowspan="2">Sur.Pay</th>';
                    html += '<th colspan="5">Referrals</th>';

                    html += '</tr>';

                    html += '<tr>';

                    html += '<th>C</th>';
                    html += '<th>R</th>';
                    html += '<th>PN</th>';
                    html += '<th>U</th>';
                    html += '<th>J</th>';

                    html += '</tr>';




               html += '</thead>';

               html += '<tbody>';

               var index = 0;

               for(index = 0;index <=12; index++){
                    html += '<tr>';
                    html += '<td>'+d.inquiry[index].month+'</td>';
                    html += '<td align="right">'+d.inquiry[index].kwh+'</td>';
                    html += '<td>'+d.inquiry[index].bill+'</td>';
                    html += '<td align="right">'+d.inquiry[index].current+'</td>';
                    html += '<td>'+d.inquiry[index].duedate+'</td>';
                    html += '<td>'+d.inquiry[index].datepaid+'</td>';
                    html += '<td align="right">'+d.inquiry[index].amtpd+'</td>';
                    html += '<td align="right">'+d.inquiry[index].interest+'</td>';
                    html += '<td>'+d.inquiry[index].datepaidsur+'</td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '</tr>';
               }

               html += '</tbody>';


               html += '</table>';

               html += '</div><footer></footer>';
               html += '</div>';

               PECO.pecoRepPDF(d.servno, html);
           });
        });



        PECO.dtSubDetails(tbl_ticket_history, 'cwdo/getticketdetailsbasic');

    };

    var acct_res_default = function() {
        tbl_billhist_rv.dataTable().empty();
    };

    var init_ticketing_form = function() {

        PECO.iCheckRow($('#reqverification', document), 'minimal', 'red');

        $('#complainants', document).iCheck({
            checkboxClass: 'icheckbox_minimal-blue', // minimal / square / polaris / futurico // red / green / blue
            radioClass: 'iradio_minimal-blue',
            increaseArea: '20%' // optional
        }).on('ifChecked', function(){
            var this_ = $(this);
            this_.attr('checked', true);

            $('.complainants-input input', document).each(function () {
                $(this).attr('disabled', true);
            });

        }).on('ifUnchecked', function(){
            var this_ = $(this);
            this_.attr('checked', false);

            $('.complainants-input input', document).each(function () {
                $(this).attr('disabled', false);
            });
        });
    };

    var init_payments_applied = function(servno, mtr) {
        $.ajax({
            url: PECO.base_url() + 'billing/getpayapplied',
            type: 'post',
            dataType: 'json',
            data: {'servno': servno, 'mtr': mtr},
            beforeSend: function () {
                PECO.DTphpLoading(tbl_payments_applied, ' Loading A/R ..');
            }
        }).done(function (d) {
            tbl_payments_applied.dataTable().empty();
            tbl_payments_applied.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                aaData: d.list,
                bSort: false,
                scrollY: '160px',
                aoColumns: [
                    {"data": "num", sWidth: '', sClass: ''},
                    {"data": "orno", sWidth: '', sClass: ''},
                    {"data": "year", sWidth: '', sClass: ''},
                    {"data": "month", sWidth: '', sClass: ''},
                    {"data": "amtpd", sWidth: '', sClass: 'number text-bold'},
                    {"data": "interest", sWidth: '', sClass: 'number text-danger'},
                    {"data": "datecreated", sWidth: '', sClass: ''},
                    {"data": "createdby", sWidth: '', sClass: ''},
                ],
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function (nRow, aData) {
                    PECO.iCheckRow($('.icheck', nRow), 'minimal', 'blue');
                }
            });
        });
    };

    var init_tagging = function(moduleid) {
        var tag_container = $('#tag_container', document);
        var servno = $('#servno', document).val();
        var mtr = $('#mtr', document).val();
        $.ajax({
            url: PECO.base_url() + 'systems/getmoduletaggs',
            type: 'post',
            dataType: 'json',
            data: {'moduleid': moduleid, 'servno': servno, 'mtr': mtr},
            beforeSend: function(){
                tag_container.html('<h4><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading tagging..</h4>');
            }
        }).done(function(d){
            tag_container.html(d.html);
            $('.popovers', tag_container).each(function(){
                var this_ = $(this);
                PECO.popOverRow($(this), true, true, 'popover-success popover-lg popover-table');
                this_.click(function(ev){
                    $('.popovers', tag_container).not(this).popover('hide');
                    setTimeout(function(){
                        init_popover_table($('#tbl_taglist'), this_);
                    },200);
                });
            });
        }).fail(function(){
           PECO.phpError();
        });
    };

    var init_popover_table = function(el, data) {
        $.ajax({
            url: PECO.base_url() + 'systems/gettaggingtable',
            type: 'post',
            data: {'acctid': data.attr('data-acctid'), 'tagid': data.attr('data-id'), 'moduleid': data.attr('data-module')},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(el, 'Loading tagging...');
            }
        }).done(function(d){
            el.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                aaData: d.list,
                bSort: false,
                aoColumns: [
                    {"data": "num", sWidth: '', sClass: ''},
                    {"data": "user", sWidth: '', sClass: 'text-primary'},
                    {"data": "date", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '', sClass: ''},
                ],
                "language": PECO.DTEmptyMessage(),
            });
        }).fail(function(){
           PECO.DTphpError(el);
        });
    };

    var init_tagging_details = function(moduleid, acctid, page, title) {
        var d1 = new $.Deferred();

        $.when(d1).then(function () {
        });
        $('#tagging_details').modal('show');
        $('#tagging_details .modal-header').html(title);
        $('#tagging_details .modal-body', document).html('Loading...');
        $('#tagging_details .modal-body', document).load(PECO.base_url() + "pages/loader",
            {
                'moduleid': moduleid,
                'acctid': acctid,
                'page': page
            },
            function () {
                d1.resolve();
                var tbl = $('#tbl_billing_hist_ref', document);
                init_reading_history_ref(tbl, acctid);
            });
    };


    var email_actual_billing = function(bill) {
        var acctid = bill.attr('data-id');
        alert(acctid);
    };

    var print_actual_billing = function(bill) {
        var acctid = bill.attr('data-id');
        var month = bill.attr('data-month');
        var year = bill.attr('data-year');
        var schedid = bill.attr('data-schedid');
        $.ajax({
            url: PECO.base_url() + 'billing/singleprintbill',
            type: 'post',
            data: {'acctid': acctid, 'month': month, 'year': year, 'schedid': schedid},
            dataType: 'json',
            beforeSend: function() {
                bill.find('.fa').removeClass('fa-print').addClass('fa-spinner fa-spin');
            }
        }).done(function(d){
            bill.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-print');
            console.log(d);
            if(d.qry==true) {
                PECO.pecoBill('Billing Form', d.html);
                $(".custom-menu").hide(100);
                ar_table.find('tr').removeClass('info');
            }else{
                PECO.initAlerts('No Billing yet!', 'Billing Print', 'warning');
            }
        }).fail(function(){
            bill.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-print');
        });
    };

    var init_search_form = function(form) {
        $('#ar_stats').html('');
        $.ajax({
            url: PECO.base_url() + 'billing/getartbl',
            type: 'post',
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function () {
                ar_table.dataTable().empty();
                PECO.DTphpLoading(ar_table, ' Loading A/R ..');
            }
        }).done(function (d) {
            if(d.qry==true) {
                $('#ar_name').html(d.arname);
                $('#ar_addr').html(d.araddr);
                $('#mult').html(d.mult);
                $('#rate').html(d.rate);
                $('#gdlb').html(d.gdlb);
                $('#ar_mtrno').html(d.mtrno);
                $('#ar_amtbal').html(d.amtbal);
                $('#ar_total_paid').html(d.amtpd);
                $('#ar_total_int').html(d.amtint);
                $('#ar_ave_kwh').html(d.avkwh);
                $('#acc_stat').html(d.status);
                $('#ar_total_due').html(d.amtdue);
                $('#ar_amt_curr').html(d.amtcur);
                $('#ar_no_bill').html(d.nobill);
                $('#ar_stats').append(d.armon);
                //if($('.nav-tabs .active').attr('href')=="#billar") {
                    ar_table.dataTable({
                        bDestroy: true,
                        bPaginate: false,
                        bFilter: false,
                        bInfo: false,
                        aaData: d.inquiry,
                        bSort: false,
                        aoColumns: [
                            {"data": "month", sWidth: '80px', sClass: 'text-bold'},
                            {"data": "kwh", sWidth: '60px', sClass: 'number'},
                            {"data": "bill", sWidth: '70px', sClass: 'text-primary text-center'},
                            {"data": "current", sWidth: '140px', sClass: 'number'},
                            {"data": "duedate", sWidth: '70px', sClass: 'text-align-center'},
                            {"data": "datepaid", sWidth: '90px', sClass: 'text-align-center'},
                            {"data": "amtpd", sWidth: '140px', sClass: 'number'},
                            {"data": "interest", sWidth: '120px', sClass: 'number'},
                            {"data": "datepaidsur", sWidth: '', sClass: 'number'},
                            {"data": "rem", sWidth: '', sClass: 'text-align-center'},
                            {"data": "rem", sWidth: '', sClass: 'text-align-center'},
                            {"data": "rem", sWidth: '', sClass: 'text-align-center'},
                            {"data": "rem", sWidth: '', sClass: 'text-align-center'},
                            {"data": "rem", sWidth: '', sClass: 'text-align-center'},
                        ],
                        fnRowCallback: function (nRow, aData) {

                            if(aData.paid==true) {
                                $(nRow).addClass('success').eq(3);
                            }else{
                                if(aData.curmo==true) {
                                    $(nRow).addClass('info').eq(3);
                                }
                                if(parseInt(aData.current)>0 && aData.curmo==false) {
                                    console.log(aData.current);
                                    $(nRow).find('td').eq(3).addClass('text-danger');
                                }
                            }

                        }
                    });
               // }


                var chart_data = d.kwharr;
                AmCharts.addInitHandler(function(chart) {
                    // check if there are graphs with autoColor: true set
                    for(var i = 0; i < chart.graphs.length; i++) {
                        var graph = chart.graphs[i];
                        if (graph.autoColor !== true)
                            continue;
                        var colorKey = "autoColor-"+i;
                        graph.lineColorField = colorKey;
                        graph.fillColorsField = colorKey;
                        for(var x = 0; x < chart.dataProvider.length; x++) {
                            var color = chart.colors[x]
                            chart.dataProvider[x][colorKey] = color;
                        }
                    }

                }, ["serial"]);

                var chart = AmCharts.makeChart("monthlykwh", {
                    "type": "serial",
                    "categoryField": "month",
                    "autoMargins": true,
                    "marginBottom:": 1,
                    "addClassNames": true,
                    "useGraphSettings": true,
                    "outlineColor": "#67b7dc",
                    "colors": ["#67b7dc", "#fdd400", "#84b761", "#cc4748", "#cd82ad", "#2f4074", "#448e4d", "#b7b83f", "#b9783f", "#b93e3d", "#913167","#666","#777"],
                    "dataProvider": chart_data,
                    "graphs": [{
                        "autoColor": true,
                        "fixedColumnWidth": 14,
                        "valueField": "value",
                        "type": "column",
                        "fillAlphas": 0.5,
                        "lineWidth": 0,
                        "showBalloon": true,
                        "balloonText": "<span style='font-size:12px;'>[[month]]: <b>[[value]]</b> KWH</span>",
                    }],
                    "valueAxes": [{
                        //"maximum": 1000,
                        //"minimum": 20,
                        "axisAlpha": 0,
                        "dashLength": 1,
                        "position": "left",
                        "labelsEnabled": false
                    }],
                    "startDuration": 1,
                    "categoryAxis": {
                        "gridAlpha": 0,
                        "axisAlpha": 0,
                        "minHorizontalGap": 1,
                        "gridPosition": "start",
                        "labelRotation": 90,
                        "tickPosition": "start",
                        "tickLength": 5,
                        "color": "#000",
                        "fontSize": 9,
                        "position": "top"
                    },
                    "labelText": " ",
                    "labelPosition": "inside",
                });




            }else{
                alert(d.msg);
            }
        });
    };

    var init_other_graph  = function(event) {
        var tab_title = $(event.target).text();         // active tab
        var tab_title_prev = $(event.relatedTarget).text();  // previous tab
        var tab_href = $(event.target).attr('href');  // previous tab

        if(tab_href=="#acctdetails") {
            init_search_form(ar_search_frm);
            other_info_graph();
        }

        $('body').on('change', '#servno', function(e){
            other_info_graph();
        });
    };

    var other_info_graph = function() {
        // OTHER INFO GRAPH
        var servno = $('#servno').val();
        var mtr = $('#mtr').val();
        var prev_year = $('#prev_year').val();
        var prev_month = $('#prev_month').val();
        $.ajax({
            url: PECO.base_url() + 'billing/getotherinfo',
            type: 'post',
            data: {'servno': servno, 'mtr': mtr, 'year': prev_year, 'month': prev_month},
            dataType: 'json',
            beforeSend: function() {
                $('#othergraph').html('<h3 class="text-info" style="margin: 10px 10px;"><i class="fa fa-spinner fa-spin"></i> Loading AR graph... </h3>');
            }
        }).done(function(d) {

            var chart = AmCharts.makeChart("othergraph", {
                "type": "serial",
                "theme": "light",
                "dataProvider": d.otheramt,
                "addClassNames": true,
                "valueAxes": [{
                    "integersOnly": true,
                    "reversed": false,
                    "axisAlpha": 0,
                    "dashLength": 5,
                    "gridCount": 12,
                    "title": "Billing Amount",
                    "stackType": "regular",
                    "gridAlpha": 0.07,
                    "position": "left",
                    "unitPosition": "left",
                }],
                //"startDuration": 1,
                "graphs": [{
                    "id": "g2",
                    "balloonText": "Current [[month]]: &#x20b1; [[value]]",
                    "bullet": "round",
                    "hidden": false,
                    "title": "Current",
                    "valueField": "curr",
                    "fillAlphas": 0.5,
                    "lineAlpha": 0.8,
                    "lineColor": "#059ffd",
                    "classNameField": "bulletClass",
                    "bulletSize": 10,
                    "bulletColor": '#059ffd',
                    "bulletBorderColor": "#05dffd",
                    "bulletBorderThickness": 2,
                    "fillColors": [
                        "#059ffd",
                        "#a4dcfe"
                    ],
                }, {
                    "id": "g3",
                    "balloonText": "Previous [[month]]: &#x20b1; [[value]]",
                    "bullet": "round",
                    "title": "Previous",
                    "valueField": "prev",
                    "fillAlphas": 0.3,
                    "lineAlpha": 0.8,
                    "bulletColor": ' #ff8a33 ',
                    "lineColor": "#fc0404",
                    "fillColors": [
                        "#ff4933",
                        "#ffd433"
                    ],
                }],
                "chartCursor": {
                    "cursorAlpha": 0,
                    "zoomable": true,
                    "pan": true,
                    "valueLineEnabled": true,
                    "valueLineBalloonEnabled": true,
                    "valueLineAlpha": 0.2,
                    "fullWidth": true,
                },
                "categoryField": "month",
                "categoryAxis": {
                    "startOnAxis": true,
                    "axisColor": "#DADADA",
                    "gridAlpha": 0.1,
                    "title": "Year",
                },
                "legend": {
                    "equalWidths": false,
                    "position": "bottom",
                    "valueAlign": "left",
                    "labelWidth": 100,
                    "valueWidth": 200,
                    "align": "left",
                    "labelText": "Php",
                },
                "export": {
                    "enabled": true,
                    "position": "bottom-right"
                }
            });
        }).fail(function(){
            console.log('PHP Error!');
        });



    };

    var init_ticket_history = function(acctid) {
        $.ajax({
            url: PECO.base_url() + 'cwdo/gettickethistory',
            type: 'post',
            dataType: 'json',
            data: {'acctid': acctid},
            beforeSend: function() {
                PECO.DTphpLoading(tbl_ticket_history, 'Loading ticket history...');
            }
        }).done(function (d) {
            tbl_ticket_history.dataTable().empty();
            tbl_ticket_history.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: false,
                aaData: d.list,
                bSort: false,
                //scrollY: '',
                aoColumns: [
                    {"data": "expand", sWidth: '', sClass: 'text-align-center'},
                    {"data": "ticketno", sWidth: '', sClass: 'text-primary'},
                    {"data": "complaints", sWidth: '', sClass: 'text-danger'},
                    {"data": "particular", sWidth: '', sClass: ''},
                    {"data": "remarks", sWidth: '', sClass: 'number'},
                    {"data": "createdby", sWidth: '', sClass: 'text-info'},
                    {"data": "datecreated", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'control'}
                ],
                "language": PECO.DTEmptyMessage(),
                fnRowCallback: function(nRow, aData, Index) {
                    PECO.dtExpandBtn(nRow, aData.expand);
                }
            });
        }).fail(function(){
            PECO.DTphpError(tbl_ticket_history, 'Error loading ticket: PHP error!');
        });

    };

    var init_get_account_info = function(servno, mtr, input) {

        $.ajax({
            url: PECO.base_url() + 'cwdo/getacctinfo',
            type: 'post',
            dataType: 'json',
            data: {'servno': servno, 'mtr': mtr},
        }).done(function (d) {
            input.val(d.acctid);
            init_ticket_history(d.acctid);
        });
    };

    var init_reading_history_rv = function(servno, mtr) {

        $.ajax({
            url: PECO.base_url() + 'billing/getbillinghist',
            type: 'post',
            dataType: 'json',
            data: {'servno': servno, 'mtr': mtr},
            beforeSend: function () {
                PECO.DTphpLoading(tbl_billhist_rv, ' Loading A/R ..');
            }
        }).done(function (d) {
            tbl_billhist_rv.dataTable().empty();
            tbl_billhist_rv.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: false,
                aaData: d.list,
                bSort: false,
                scrollY: '160px',
                aoColumns: [
                    {"data": "year", sWidth: '', sClass: ''},
                    {"data": "month", sWidth: '', sClass: ''},
                    {"data": "kwhuse", sWidth: '', sClass: 'number'},
                    {"data": "prsrdg", sWidth: '', sClass: 'number'},
                    {"data": "prvrdg", sWidth: '', sClass: 'number'},
                    {"data": "prvdte", sWidth: '', sClass: ''},
                    {"data": "prsdte", sWidth: '', sClass: ''},
                    {"data": "mtrser", sWidth: '', sClass: 'number'},
                    {"data": "serial", sWidth: '', sClass: 'number'},
                    {"data": "batch", sWidth: '', sClass: ''},
                    {"data": "select", sWidth: '', sClass: 'control'}
                ],
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function (nRow, aData) {
                    $('.icheck', nRow).iCheck({
                        checkboxClass: 'icheckbox_minimal-red',
                        radioClass: 'iradio_minimal-red',
                        increaseArea: '20%' // optional
                    }).on('ifChecked', function(){
                        var this_ = $(this);
                        this_.attr('checked', true);
                        this_.closest('tr').find('td').addClass('danger');
                    }).on('ifUnchecked', function(){
                        var this_ = $(this);
                        this_.attr('checked', false);
                        this_.closest('tr').find('td').removeClass('danger');
                    });
                }
            }).on('click', 'tr td', function(e) {
                 var this_ = $(this);
                 this_.closest('tr').find('td').toggleClass('danger');
                 this_.closest('tr').find('td.control .icheck').iCheck('toggle');
            });

            PECO.initDTNicescroller(true);
        });
    };

    var init_reading_history = function(servno, mtr) {
        $.ajax({
            url: PECO.base_url() + 'billing/getbillinghist',
            type: 'post',
            dataType: 'json',
            data: {'servno': servno, 'mtr': mtr},
            beforeSend: function () {

                PECO.DTphpLoading(tbl_billing_hist, ' Loading A/R ..');
            }
        }).done(function (d) {
            tbl_billing_hist.dataTable().empty();
            tbl_billing_hist.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                aaData: d.list,
                bSort: false,
                scrollY: '160px',
                aoColumns: [
                    {"data": "month", sWidth: '', sClass: ''},
                    {"data": "year", sWidth: '', sClass: ''},
                    {"data": "kwhuse", sWidth: '', sClass: 'number'},
                    {"data": "prsrdg", sWidth: '', sClass: 'number'},
                    {"data": "prvrdg", sWidth: '', sClass: 'number'},
                    {"data": "prvdte", sWidth: '', sClass: ''},
                    {"data": "prsdte", sWidth: '', sClass: ''},
                    {"data": "nodays", sWidth: '', sClass: 'number'},
                    {"data": "mtrser", sWidth: '', sClass: ''},
                    {"data": "serial", sWidth: '', sClass: ''},
                    {"data": "moyr", sWidth: '', sClass: ''},
                    {"data": "batch", sWidth: '', sClass: ''}
                ],
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    PECO.iCheckRow($('.icheck', nRow),'minimal', 'blue');
                }
            });

            $('#tbl_rv_history').dataTable().empty();
            $('#tbl_rv_history').dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                //aaData: d.list,
                bSort: false,
                scrollY: '80px',
                /*aoColumns: [
                    {"data": "month", sWidth: '', sClass: ''},
                ],*/
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    PECO.iCheckRow($('.icheck', nRow),'minimal', 'blue');
                }
            });

            $('#tbl_meter_history').dataTable().empty();
            $('#tbl_meter_history').dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                //aaData: d.list,
                bSort: false,
                scrollY: '80px',
                /*aoColumns: [
                    {"data": "month", sWidth: '', sClass: ''},
                ],*/
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    PECO.iCheckRow($('.icheck', nRow),'minimal', 'blue');
                }
            });
            PECO.initDTNicescroller(false);
        });

    };


    var init_reading_history_ref = function(tbl, acctid) {
        $.ajax({
            url: PECO.base_url() + 'billing/getbillinghist',
            type: 'post',
            dataType: 'json',
            data: {'acctid': acctid},
            beforeSend: function () {
                tbl.dataTable().empty();
                PECO.DTphpLoading(tbl, ' Loading A/R ..');
            }
        }).done(function (d) {
            tbl.dataTable().empty();
            tbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: false,
                scrollY: '250px',
                aoColumns: [
                    {"data": "month", sWidth: '5%', sClass: ''},
                    {"data": "year", sWidth: '5%', sClass: ''},
                    {"data": "kwhuse", sWidth: '', sClass: 'number text-danger'},
                    {"data": "current", sWidth: '', sClass: 'number text-primary'},
                    {"data": "code", sWidth: '', sClass: ''},
                    {"data": "select", sWidth: '', sClass: 'text-align-center'}
                ],
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    PECO.iCheckRow($('.icheck', nRow),'minimal', 'blue');
                    $('.tooltips', nRow).tooltip();
                }
            });
            setTimeout(function(){

                PECO.initDTNicescroller();
            }, 1000);
        });
    };
    return {
        init: function() {
            init_inquiry();
        }
    }
}();
