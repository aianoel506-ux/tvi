/*
    AUTHOR: LUCKY JOHN F. FADERON
    DATE: 9/11/2017
 */

var INQUIRY = function() {
    PECO.getAmsChartPlugins();
    //PECO.getSelect2Plugins();

    var ar_table = $('#tbl_ar');

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
        $('#prev_month').select2();
        $('#frm_search').submit(function (e) {
            $('#tab_ar').trigger('click');
            var form = $(this);
            e.preventDefault();
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
                    $('#ar_ave_kwh').html(d.avkwh);
                    $('#acc_stat').html(d.status);
                    ar_table.dataTable({
                        bDestroy: true,
                        bPaginate: false,
                        bFilter: false,
                        bInfo: false,
                        aaData: d.months,
                        bSort: false,
                        aoColumns: [
                            {"data": "month", sWidth: '', sClass: 'text-bold'},
                            {"data": "kwh", sWidth: '', sClass: 'number'},
                            {"data": "bill", sWidth: '', sClass: 'text-primary text-center'},
                            {"data": "current", sWidth: '', sClass: 'number'},
                            {"data": "duedate", sWidth: '', sClass: 'center'},
                            {"data": "amtpd", sWidth: '', sClass: 'number'},
                            {"data": "datepaid", sWidth: '', sClass: 'center'},
                            {"data": "interest", sWidth: '', sClass: 'number'},
                            {"data": "datepaidsur", sWidth: '', sClass: 'number'},
                            {"data": "rem", sWidth: '', sClass: 'number'},
                            {"data": "control", sWidth: '', sClass: 'control center'}
                        ],
                        fnRowCallback: function(nRow, aData) {

                        }
                    });

                    var chart_data = d.kwharr;

                    console.log(chart_data);

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
                    var chart = AmCharts.makeChart("chartdiv", {
                        "type": "serial",
                        "categoryField": "month",
                        "autoMargins": true,
                        "addClassNames": true,
                        "useGraphSettings": true,
                        "outlineColor": "",
                        "colors": ["#67b7dc", "#fdd400", "#84b761", "#cc4748", "#cd82ad", "#2f4074", "#448e4d", "#b7b83f", "#b9783f", "#b93e3d", "#913167","#666","#777"],
                        "dataProvider": chart_data,
                        "graphs": [{
                            "autoColor": true,
                            "fixedColumnWidth": 15,
                            "valueField": "value",
                            "type": "column",
                            "fillAlphas": 0.5,
                            "lineWidth": 1,
                            "showBalloon": true,
                            "balloonText": "<span style='font-size:13px;'>[[month]]: <b>[[value]]</b> KWH</span>",

                        }],
                        "valueAxes": [{
                            //"maximum": 1000,
                            "minimum": 20,
                            "axisAlpha": 0,
                            "dashLength": 1,
                            "position": "left"
                        }],
                        "startDuration": 1,
                        "categoryAxis": {
                            "gridAlpha": 0,
                            "axisAlpha": 0,
                            "minHorizontalGap": 0,
                            "gridPosition": "start",
                            "labelRotation": 45,
                            "tickPosition": "start",
                            "tickLength": 5
                        },
                        "labelText": " ",
                        "labelPosition": "inside",

                    });


                    // OTHER ARR
                    var other_data_provider = d.otheramt;

                    // OTHER INFO GRAPH
                    AmCharts.makeChart("othergraph", {
                        "type": "serial",
                        "theme": "light",
                        "legend": {
                            "useGraphSettings": true
                        },
                        "dataProvider": other_data_provider,
                        "valueAxes": [{
                            "integersOnly": true,
                            "reversed": false,
                            "axisAlpha": 0,
                            "dashLength": 5,
                            "gridCount": 10,
                            "position": "left",
                            "title": "Billing"
                        }],
                        //"startDuration": 1,
                        "graphs": [{
                            "id": "g3",
                            "balloonText": "Due as of [[month]]: [[value]]",
                            "bullet": "round",
                            "hidden": false,
                            "title": "Current",
                            "valueField": "curr",
                            "fillAlphas": 0
                        }, {
                            "id": "g2",
                            "balloonText": "Due as of [[month]]: [[value]]",
                            "bullet": "round",
                            "title": "Previous",
                            "valueField": "prev",
                            "fillAlphas": 0
                        }],
                        "chartCursor": {
                            "cursorAlpha": 0,
                            "zoomable": false
                        },
                        "categoryField": "month",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "axisAlpha": 0,
                            "fillAlpha": 0.05,
                            "fillColor": "#000000",
                            "gridAlpha": 0,
                            "position": "top"
                        },
                        "export": {
                            "enabled": true,
                            "position": "bottom-right"
                        }
                    });
                }else{
                    alert(d.msg);
                }
            });
        });


    };
    return {
        init: function() {
            init_inquiry();
        }
    }
}();
