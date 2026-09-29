/*
    AUTHOR: LUCKY JOHN F. FADERON
    DATE: 9/11/2017
 */

var AR = function() {
    PECO.getAmsChartPlugins();
    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();
    PECO.getNumberFormatPlugin();

    var tbl_ar = $('#tbl_ar', document);
    var frm_search = $('#frm_search', document);
    var input_ar_month = $('#input_ar_month', document);
    var input_ar_limit = $('#input_ar_limit', document);
    var input_ar_year = $('#input_ar_year', document);
    var filter_input = $('#filter_input', document);
    var filter_input = $('#filter_input', document);
    var tbl_ar_list = $('#tbl_ar_list', document);
    var tbl_acct_search = $('#tbl_acct_search', document);

    var tbl_billing_hist = $('#tbl_billing_hist', document);

    var servno = $('#servno', document);
    var mtr = $('#mtr', document);
    var init_event = function() {
        PECO.DTDefault(tbl_ar, 'Search Service Account Number..');
        if($.fn.select2) {
            input_ar_month.select2({'allowClear': true, 'placeholder': 'Select..'});
            input_ar_limit.select2({'allowClear': true, 'placeholder': 'Select..'});
        }

        frm_search.submit(function(e) {
            e.preventDefault();
            var servno = $('#servno', document).val();
            var mtr = $('#mtr', document).val();
            $('#tab_ar').trigger('click');
            init_tbl_ar(servno, mtr, true, false);
        });

        filter_input.on('click', 'button#btn_filter', function(e) {
            var this_ = $(this);
            var servno = $('#servno', document).val();
            var mtr = $('#mtr', document).val();
            init_tbl_ar(servno, mtr, false, this_);
        });

        // @TODO give user previlage on this function.
        // BILLING DEPARTMENT / CNC USERS / ADMIN
        $('#tagtowatchlist', document).click(function(e) {
           e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Adding this account to watch list?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
               swal.close(); 
            });
        });

        $('#btn_migrate_payment', document).click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_text_original = this_.text();

            var servno = $('#servno', document).val();
            var mtr = $('#mtr', document).val();
            $.ajax({
                url: PECO.base_url() + 'ar/getcustomerpayfromold',
                type: 'post',
                data: {'servno': servno, 'mtr': mtr},
                dataType: 'json',
                beforeSend: function() {
                    this_.html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Loading...');
                }
            }).done(function(d) {
                this_.html(this_text_original);
            }).fail(function() {
                PECO.phpError();
                this_.html(this_text_original);
            });
        });

        $('#btn_filter_up', document).click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            var input_ar_limit_val = this_.attr('data-val');
            var servno = $('#servno', document).val();
            var mtr = $('#mtr', document).val();
            if(input_ar_limit_val>0) {
                init_tbl_ar(servno, mtr, false, this_, input_ar_limit_val);
            }
        });

        $('#btn_filter_down', document).click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            var input_ar_limit_val = this_.attr('data-val');
            var servno = $('#servno', document).val();
            var mtr = $('#mtr', document).val();
            if(input_ar_limit_val>0) {
                init_tbl_ar(servno, mtr, false, this_, input_ar_limit_val);
            }
        });

        // ###################################################################/
        // ###################################################################/
        // SHORTCUTS #########################################################/
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


        // ###################################################################/
        // ###################################################################/
        // TABS ##############################################################/
        $('.ar-tab a').on('shown.bs.tab', function(e){
            var this_ = $(this);
            var target = this_.attr('href');
            if (target == '#readinghist') {

                var servno = $('#servno',document).val();
                var mtr = $('#mtr',document).val();
                init_reading_history(servno, mtr);
            }


            if (target == '#acctdetails') {
                init_other_graph(e);
            }

        });

        // ###################################################################/
        // ###################################################################/
        // CONTEX MENU
        init_ar_context_menu();
        // PRINTING STATEMENT OF ACCOUNT
        init_ar_statement_print();
    };

    var init_tbl_ar = function(servno, mtr, loading, obj, starts) {

        var input_ar_month = $('#input_ar_month', document).val();
        var input_ar_year = $('#input_ar_year', document).val();
        var input_ar_limit = $('#input_ar_limit', document).val();
        if(obj) {
            var obj_html = obj.html();
        }

        $.ajax({
            url: PECO.base_url() + 'ar/getbilling',
            type: 'post',
            data: {'servno': servno, 'mtr': mtr, 'limit': input_ar_limit, 'month': input_ar_month, 'year': input_ar_year, 'start': starts},
            dataType: 'json',
            beforeSend: function() {
                if(loading) {
                    PECO.DTphpLoading(tbl_ar, 'Loading A/R...');
                }
                if(obj) {
                    obj.html('<i class="fa fa-spinner fa-spin fa-pulse"></i>');
                }
            }
        }).done(function(d) {

            $('#amt_balance', document).html(d.amtbal);
            $('#amt_interest', document).html(d.amtint);
            $('#amt_overdue', document).html(d.amtdue);
            $('#amt_current', document).html(d.amtcur);
            $('#amt_prev', document).html(d.amtprev);
            $('#amt_paid', document).html(d.amtpaid);
            $('#kwh_ave', document).html(d.kwhave);
            $('#ar_name', document).html(d.name);
            $('#ar_addr', document).html(d.address);
            $('#acct_pic', document).attr('src', d.pic);
            $('#acct_pic', document).removeClass('success danger').addClass(d.statusclass);


            $('#mult', document).html(d.mult);
            $('#rate', document).html(d.rate);
            $('#gdlb', document).html(d.gdlb);
            $('#mtrno', document).html(d.mtrno);
            $('#status', document).html(d.status);
            $('#lastpay', document).html(d.lastpay);
            $('#nobills', document).html(d.nobills);
            $('#btn_filter_down', document).attr('data-val', d.starts);
            $('#btn_filter_up', document).attr('data-val', d.back);

            if(obj) {
                obj.html(obj_html);
            }

            tbl_ar.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                aaData: d.list,
                bSort: true,
                // scrollY: '300px',
                // "order": [[ 0, "desc"], [1, "desc"]],
                aoColumns: [
                    {"data": "month", sWidth: '', sClass: 'month', orderable: false},
                    {"data": "year", sWidth: '', sClass: 'year', orderable: false},
                    {"data": "billno", sWidth: '', sClass: 'text-info number billno', orderable: false},
                    {"data": "kwh", sWidth: '', sClass: 'text-bold number kwh', orderable: false},
                    {"data": "current", sWidth: '', sClass: 'text-danger text-bold number current', orderable: false},
                    {"data": "duedate", sWidth: '', sClass: 'duedate', orderable: false},
                    {"data": "interest", sWidth: '', sClass: 'text-danger number interest', orderable: false},
                    {"data": "amtpaid", sWidth: '', sClass: 'text-success number paid', orderable: false},
                    {"data": "datepaid", sWidth: '', sClass: 'text-success datepaid', orderable: false},
                    {"data": "balance", sWidth: '', sClass: 'text-danger number balance', orderable: false},
                    {"data": "remarks", sWidth: '', sClass: 'remarks', orderable: false},
                    {"data": "remarks", sWidth: '', sClass: 'remarks', orderable: false},
                    {"data": "remarks", sWidth: '', sClass: 'remarks', orderable: false},
                    {"data": "remarks", sWidth: '', sClass: 'remarks', orderable: false},
                    {"data": "remarks", sWidth: '', sClass: 'remarks', orderable: false},
                ],
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function (nRow, aData) {
                    if(aData.paid == true) {
                        //$('td', nRow).addClass('active');
                        $('td.current', nRow).removeClass('text-danger').addClass('text-success');
                    }
                }
            });
            PECO.dataTableScroller();

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
        }).fail(function() {
            PECO.DTphpError(tbl_ar, 'Error PHP!');
        });
    };

    var init_other_graph  = function(event) {
        var tab_title = $(event.target).text();         // active tab
        var tab_title_prev = $(event.relatedTarget).text();  // previous tab
        var tab_href = $(event.target).attr('href');  // previous tab

        if(tab_href=="#acctdetails") {
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
                "outlineColor": "#67b7dc",
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

    var init_ar_statement_print = function() {
        $(document).on('click','#printstatementbtn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_txt = this_.html();
            var input_ar_month = $('#input_ar_month', document).val();
            var input_ar_year = $('#input_ar_year', document).val();
            var input_ar_limit = $('#input_ar_limit', document).val();
            var servno = $('#servno', document).val();
            var mtr= $('#mtr', document).val();

            $.ajax({
                url: PECO.base_url() + 'ar/getbilling',
                type: 'post',
                data: {'servno': servno, 'mtr': mtr, 'limit': input_ar_limit, 'month': input_ar_month, 'year': input_ar_year},
                dataType: 'json',
                beforeSend: function() {
                    this_.html('<i class="fa fa-spin fa-spinner fa-pulse"></i> Processing...');
                }
            }).done(function (d) {
                this_.html(this_txt);
                var html = '';

                //first row
                html += '<div class="row">';
                html += '<div class="col-md-6 col-xs-6">';
                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 25%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Name</span>';
                html += '<span style="width: 75%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.name+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 25%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Address</span>';
                html += '<span style="width: 75%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.address+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 25%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Status</span>';
                html += '<span style="width: 75%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.status+'</span>';
                html += '</li>';
                html += '</ul>';


                html += '</div>';

                html += '<div class="col-md-4 col-xs-4">';

                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">GDLB</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default">'+d.gdlb+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Rate</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default">'+d.rate+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">MULT</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default">'+d.mult+'</span>';
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
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name" >Total Balance</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtbal+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Total Interest</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtint+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Due</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtdue+'</span>';
                html += '</li>';
                html += '</ul>';
                html += '</div>';

                html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';

                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 60%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Total Amount Paid</span>';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtpaid+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 60%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Last Pay Date</span>';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.lastpay+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 60%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Current</span>';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtcur+'</span>';
                html += '</li>';
                html += '</ul>';

                html += '</div>';

                html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';

                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Average KWH</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.kwhave+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Meter No.</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.mtrno+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">No. of Bills</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.nobills+'</span>';
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
                html += '<th rowspan="2">Year</th>';
                html += '<th rowspan="2">KWH</th>';
                html += '<th rowspan="2">Bill No.</th>';
                html += '<th rowspan="2">Amount Due</th>';
                html += '<th rowspan="2">Interest</th>';
                html += '<th rowspan="2">Amount Paid</th>';
                html += '<th rowspan="2">Due Date</th>';
                html += '<th rowspan="2">Date Paid</th>';
                html += '<th rowspan="2">Balance</th>';
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


                for(var index = 0;index < d.list.length; index++){
                    html += '<tr>';
                    html += '<td>'+d.list[index].month+'</td>';
                    html += '<td>'+d.list[index].year+'</td>';
                    html += '<td align="right">'+d.list[index].kwh+'</td>';
                    html += '<td>'+d.list[index].billno+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].current+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].interest+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].amtpaid+'</td>';
                    html += '<td>'+d.list[index].duedate+'</td>';
                    html += '<td>'+d.list[index].datepaid+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].balance+'</td>';
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

                PECO.pecoRepPrint("Statement of Account" , html);
            });
        });
    };


    var init_ar_context_menu = function() {
        tbl_ar.on('contextmenu', 'tr', function(e) {
            tbl_ar.find('tr').removeClass('info');
            var this_ = $(this);

            var data_text = $('.month', this_).html();
            var data_text_year = $('.year', this_).text();
            var data_id = $('.month #month_id', this_).attr('data-id');
            var data_month = this_.attr('data-month');
            var data_year = this_.attr('data-year');
            var data_schedid = this_.attr('data-schedid');

            if(data_id) {
                e.preventDefault();
                this_.closest('tr').addClass('info');
                // WRITE THE CONTEXT MENU IN THE PAGE
                var context_menu_list = '<ul id="monthly_context_menu" class="custom-menu">' +
                    '<li style="background: #ef582d; color: #fff; font-weight: bold;">'+data_text+ ' ' + data_text_year + '</li>' +
                    '<li data-action="print" data-id="'+data_id+'"><i class="fa fa-print fa-fw text-primary"></i> Print Actual Bill</li>' +
                    '<li data-action="preview" data-id="'+data_id+'"><i class="fa fa-print fa-fw text-primary"></i> Preview Actual Bill</li>' +
                    '<li data-action="remarks" data-id="'+data_id+'"><i class="fa fa-search fa-fw text-primary"></i> Reading Remarks</li>' +
                    '<li data-action="ebill" data-id="'+data_id+'"><i class="fa fa-envelope fa-fw text-info"></i> Re-Send eBill</li>' +
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
                tbl_ar.find('tr').removeClass('info');
            }
        });

        $('body').on('click', '.custom-menu li', function(e){
            e.preventDefault();
            var this_ = $(this);
            var id = this_.attr('data-id');

            // This is the triggered action name
            switch($(this).attr("data-action")) {
                // A case for each action. Your actions here
                case "print":
                    print_actual_billing(id, this_);
                    break;
                case "preview":
                    preview_actual_billing(id, this_);
                    break;
                case "ebill":
                    email_actual_billing(id, this_);
                    break;
            }
            // Hide it AFTER the action was triggered

        });
    };

    var email_actual_billing = function(id, li) {
        $.ajax({
            url: PECO.base_url() + 'billing/sendebill',
            type: 'post',
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
                li.find('.fa').removeClass('fa-print text-danger').addClass('fa-spinner fa-spin');
            }
        }).done(function(d){
            if(d.qry==true) {
                li.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-envelope');
                PECO.initAlerts('e-Mail', 'Billing Statement Sent!', 'Success');
            }else{
                li.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-envelope text-danger');
            }
        });
    };



    var print_actual_billing = function(id, li) {
        $.ajax({
            url: PECO.base_url() + 'billing/singleprintbill',
            type: 'post',
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
                li.find('.fa').removeClass('fa-print').addClass('fa-spinner fa-spin');
            }
        }).done(function(d){
            li.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-print');
            console.log(d);
            if(d.qry==true) {
                PECO.pecoBill('Billing Form', d.html);
                $(".custom-menu").hide(100);
                tbl_ar.find('tr').removeClass('info');
            }else{
                PECO.initAlerts('No Billing yet!', 'Billing Print', 'warning');
            }
        }).fail(function(){
            li.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-print');
        });
    };


    var preview_actual_billing = function(id, li) {
        $.ajax({
            url: PECO.base_url() + 'billing/singlepreviewbill',
            type: 'post',
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
                li.find('.fa').removeClass('fa-print').addClass('fa-spinner fa-spin');
            }
        }).done(function(d){
            li.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-print');
            console.log(d);
            if(d.qry==true) {
                PECO.pecoBill('Billing Form', d.html);
                $(".custom-menu").hide(100);
                tbl_ar.find('tr').removeClass('info');
            }else{
                PECO.initAlerts('No Billing yet!', 'Billing Print', 'warning');
            }
        }).fail(function(){
            li.find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-print');
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
                scrollY: '200px',
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
            PECO.dataTableScroller();

            $('#tbl_rv_history').dataTable().empty();
            $('#tbl_rv_history').dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                //aaData: d.list,
                bSort: false,
                scrollY: '150px',
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
                scrollY: '150px',
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

    var init_search = function() {
        $('#frm_search_name', document).submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function() {
                    PECO.DTphpLoading(tbl_acct_search, 'Loading A/R...');
                }
            }).done(function(d) {
                tbl_acct_search.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    aaData: d.list,
                    bSort: false,
                    scrollY: '400px',
                    aoColumns: [
                        {"data": "id", sWidth: '', sClass: ''},
                        {"data": "text", sWidth: '100px', sClass: ''},
                        {"data": "name", sWidth: '', sClass: ''},
                        {"data": "addr", sWidth: '', sClass: ''},
                        {"data": "control", sWidth: '', sClass: ''}
                    ],
                    "language": PECO.DTEmptyMessage(),
                    searchHighlight: true,
                });
                PECO.dataTableScroller();
            });
        });

        tbl_acct_search.on('click', '#search_btn_row', function(e) {
            e.preventDefault();
            $('#frm_search #servno').val($(this).attr('data-servno'));
            setTimeout(function(){
                $('#frm_search', document).trigger('submit');
            }, 500);
        });
    };

    var init_cnc_list = function() {
        PECO.select2Basic ($('#select2dist', document), 'query/select2district', 'District', false, false, false);

        PECO.DTDefault(tbl_ar_list, 'Select district first!');

        $(document).on('change', '#select2dist', function(e) {
            var this_ = $(this);
            var this_val = this_.select2('val');
            init_cnc_list_tbl(this_val, true);
        });

        $(document).on('click', '#btn_query_payment_all', function(e) {
            var nums = 1;
            var nums_total = $('tbody tr', tbl_ar_list).length;
            /*
            var refreshIntervalId = setInterval(function() {
                if(nums<=nums_total) {
                    loop_cnt(nums);
                    nums += 1;
                }else{
                    alert('loop done!');
                    clearInterval(refreshIntervalId);
                }
            },1000);
            */
            var first_button = $('#btn_query_payment').first();
            ajax_query_pay(first_button, nums_total);
        });

        $(document).on('click', '#btn_query_payment', function(e) {
            var this_ = $(this);
            var this_txt = this_.text();
            this_.html('Loading...');
            setTimeout(function() {
                this_.html(this_txt);
            },300);
        });
    };

    var loop_cnt = function(num) {
        $('#btn_query_payment.'+num, tbl_ar_list).trigger('click');
    };

    var ajax_query_pay = function(this_, totalnum) {
        var this_text_original = this_.text();
        var servno = this_.attr('data-servno');
        var mtr = this_.attr('data-mtr');

        var year = $('#inputyear', document).val();
        var month = $('#inputmonth', document).val();

        $.ajax({
            url: PECO.base_url() + 'ar/getcustomerpayfromold',
            type: 'post',
            data: {'servno': servno, 'mtr': mtr, 'year': year, 'month': month},
            dataType: 'json',
            beforeSend: function() {
                this_.html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Loading...');
            }
        }).done(function(d) {
            $('#qry_stat', document).show();

            this_.html(this_text_original).removeClass('btn-default').addClass('btn-success');
            this_.removeAttr('data-scroll-to');
            var this_num = this_.attr('data-num');
            var next_num_add = Number(this_num) + 1;
            var next_num = $('#btn_query_payment.' + next_num_add, tbl_ar_list);

            $toElement = next_num.attr('data-scroll-to', true);

            if(next_num.attr('data-num') <= totalnum) {
                ajax_query_pay(next_num, totalnum);
                var percent = 0;
                var actual_num = next_num.attr('data-num');
                percent = ((Number(actual_num) / Number(totalnum) )*100);
                $('#q_percent').html(percent).number(true, 2);
            }


        }).fail(function() {
            PECO.phpError();
            this_.html(this_text_original);
        });

    };

    var init_cnc_list_tbl = function(dist, loading) {
        var year = $('#inputyear', document).val();
        var month = $('#inputmonth', document).val();
        $.ajax({
            url: PECO.base_url() + 'ar/getarlist',
            type: 'post',
            data: {'dist': dist, 'year': year, 'month': month},
            dataType: 'json',
            beforeSend: function() {
                if(loading) {
                    PECO.DTphpLoading(tbl_ar_list, 'Loading A/R...');
                }
            }
        }).done(function(d) {
            tbl_ar_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                aaData: d.list,
                bSort: false,
                aoColumns: [
                    {"data": "num", sWidth: '', sClass: ''},
                    {"data": "servno", sWidth: '', sClass: ''},
                    {"data": "name", sWidth: '', sClass: ''},
                    {"data": "address", sWidth: '', sClass: ''},
                    {"data": "current", sWidth: '', sClass: 'number'},
                    {"data": "overdue", sWidth: '', sClass: 'number'},
                    {"data": "control", sWidth: '', sClass: ''},
                ],
                "language": PECO.DTEmptyMessage(),
                searchHighlight: true,
            });
        });
    };

    return {
        init: function() {
            init_event();
        },
        search: function() {
            init_search();
        },
        cnclist: function() {
            init_cnc_list();
        }
    }
}();