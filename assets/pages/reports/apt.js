/**
 * Created by SE on 0011, May 11, 2017.
 */

var APT = function() {
    PECO.getHighlightsPlugin();
    PECO.fancybox();

    $.extend(true, $.fn.DataTable.TableTools.classes, {
        "container": "btn-group tabletools-dropdown-on-portlet",
        "buttons": {
            "normal": "btn btn-sm default",
            "disabled": "btn btn-sm default disabled"
        },
        "collection": {
            "container": "DTTT_dropdown dropdown-menu tabletools-dropdown-menu"
        }
    });

    var tbl_app = $('#tbl_app_list');
    var tbl_summary_res = $('#tbl_summary_res');

    var init_apt_list = function() {

    };

    var init_apt_summary = function() {
        dt_apt_summary();
    };

    var init_apt_aging = function() {
        chart_apt_aging();
    };

    var chart_apt_aging = function() {
        $.ajax({
            url: PECO.base_url() + 'reports/chartaptaging',
            type: 'post',
            dataType: 'json',
            data: {},
        }).done(function(d){


            var chart = AmCharts.makeChart("chart_aging", {
                "type": "serial",
                "theme": "light",
                /*
                "legend": {
                    "horizontalGap": 10,
                    "maxColumns": 1,
                    "position": "bottom",
                    "useGraphSettings": false,
                    "markerSize": 10
                },
                */
                "dataProvider": d.months,
                "valueAxes": [{
                    "stackType": "regular",
                    "axisAlpha": 0.3,
                    "gridAlpha": 0
                }],
                "graphs": [{
                    "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                    "fillAlphas": 0.8,
                    "labelText": "[[value]]",
                    "lineAlpha": 0.3,
                    "title": "Unaccomplished",
                    "type": "column",
                    "color": "#000000",
                    "valueField": "unaccomp",
                    "fillColorsField": "unAccompColor"
                }, {
                    "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                    "fillAlphas": 0.8,
                    "labelText": "[[value]]",
                    "lineAlpha": 0.3,
                    "title": "Accomplished",
                    "type": "column",
                    "color": "#000000",
                    "valueField": "accomp",
                    "fillColorsField": "AccompColor"
                },  ],
                "categoryField": "month",
                "categoryAxis": {
                    "gridPosition": "start",
                    "axisAlpha": 0,
                    "gridAlpha": 0,
                    "position": "left",
                    "labelRotation": 0,
                },
                "export": {
                    "enabled": true
                }

            });

            AmCharts.addInitHandler(function(chart) {
                // iterate through data
                for(var i = 0; i < d.months.length; i++ ) {
                    var dp = d.months[i];
                    dp.total = 0;
                    dp.totalText = 0;
                    for(var x = 0; x < chart.graphs.length; x++ ) {
                        var g = chart.graphs[x];
                        dp.totalText += dp[g.valueField];
                        if (dp[g.valueField] > 0)
                            dp.total += dp[g.valueField];
                    }
                }
                // add additional graph
                var graph = new AmCharts.AmGraph();
                graph.valueField = "total";
                graph.labelText = "[[totalText]]";
                graph.visibleInLegend = false;
                graph.showBalloon = false;
                graph.lineAlpha = 0;
                graph.fontSize = 15;
                chart.addGraph(graph);

            }, ["serial"]);

        });
    };

    var dt_apt_summary = function() {
       $.ajax({
           url: PECO.base_url() + 'reports/datatableaptsummary',
           type: 'post',
           dataType: 'json',
           data: {},
           beforeSend: function(){
               PECO.DTphpLoading(tbl_summary_res, 'Loading APT Summary..');
           }
       }).done(function(d){
           tbl_summary_res.dataTable().empty();
           tbl_summary_res.dataTable({
               bDestroy: true,
               bPaginate: false,
               bFilter: false,
               bInfo: false,
               bSort: false,
               bStateSave: true,
               bProcessing: true,
               aaData: d.list,
               //scrollY: var_table_scroll_height,
               scrollY: '30vh',
               aoColumns: [
                   {"data": "types", sClass: 'text-bold', sWidth: ''},
                   {"data": "accomp", sClass: 'number text-success', sWidth: ''},
                   {"data": "unaccomp", sClass: 'number text-danger', sWidth: ''},
                   {"data": "total", sClass: 'number text-bold text-primary', sWidth: ''},
               ],
               language: PECO.DTEmptyMessage()
           });
       });
    };

    var init_app_client = function() {
        var chartData1 = [];
        var chartData2 = [];
        var chartData3 = [];
        var chartData4 = [];

        generateChartData();

        function generateChartData() {
            var firstDate = new Date();
            firstDate.setDate( firstDate.getDate() - 500 );
            firstDate.setHours( 0, 0, 0, 0 );

            for ( var i = 0; i < 500; i++ ) {
                var newDate = new Date( firstDate );
                newDate.setDate( newDate.getDate() + i );


                var a2 = Math.round( Math.random() * (10 - 1) + 1) ;
                var b2 = Math.round( Math.random() * (10 - 1) + 1) ;

                var a3 = Math.round( Math.random() * (10 - 1) + 1) ;
                var b3 = Math.round( Math.random() * (10 - 1) + 1) ;

                var a1 = a2 + a3;
                var b1 = b2 + b3;


                chartData1.push( {
                    "date": newDate,
                    "value": a1,
                    "volume": b1
                } );
                chartData2.push( {
                    "date": newDate,
                    "value": a2,
                    "volume": b2
                } );
                chartData3.push( {
                    "date": newDate,
                    "value": a3,
                    "volume": b3
                } );
            }
        }

        var chart = AmCharts.makeChart( "app_chart", {
            "type": "stock",
            "theme": "light",
            "addClassNames": true,
            "synchronizeGrid": true,
            "dataSets": [ {
                "title": "All",
                "fieldMappings": [ {
                    "fromField": "value",
                    "toField": "value"
                }, {
                    "fromField": "volume",
                    "toField": "volume"
                } ],
                "dataProvider": chartData1,
                "categoryField": "date"
            }, {
                "title": "Lory",
                "fieldMappings": [ {
                    "fromField": "value",
                    "toField": "value"
                }, {
                    "fromField": "volume",
                    "toField": "volume"
                } ],
                "dataProvider": chartData2,
                "categoryField": "date"
            }, {
                "title": "Nicole",
                "fieldMappings": [ {
                    "fromField": "value",
                    "toField": "value"
                }, {
                    "fromField": "volume",
                    "toField": "volume"
                } ],
                "dataProvider": chartData3,
                "categoryField": "date"
            }
            ],

            "panels": [ {
                "showCategoryAxis": false,
                "title": "Value",
                "percentHeight": 70,
                "stockGraphs": [ {
                    "id": "g1",
                    "valueField": "value",
                    "comparable": true,
                    "compareField": "value",
                    "balloonText": "[[title]]:<b>[[value]]</b>",
                    "compareGraphBalloonText": "[[title]]:<b>[[value]]</b>"
                } ],
                "stockLegend": {
                    "periodValueTextComparing": "[[percents.value.close]]%",
                    "periodValueTextRegular": "[[value.close]]"
                }
            }, {
                "title": "Applicant",
                "percentHeight": 30,
                "stockGraphs": [ {
                    "valueField": "volume",
                    "type": "column",
                    "showBalloon": true,
                    "fillAlphas": 1
                } ],
                "stockLegend": {
                    "periodValueTextRegular": "[[value.close]]"
                }
            } ],

            "chartScrollbarSettings": {
                "graph": "g1"
            },

            "chartCursorSettings": {
                "valueBalloonsEnabled": true,
                "fullWidth": true,
                "cursorAlpha": 0.1,
                "valueLineBalloonEnabled": true,
                "valueLineEnabled": true,
                "valueLineAlpha": 0.5
            },

            "periodSelector": {
                "position": "left",
                "periods": [ {
                    "period": "MM",
                    "count": 1,
                    "label": "1 month",
                    "selected": true,
                }, {
                    "period": "YYYY",
                    "count": 1,
                    "label": "1 year",
                }, {
                    "period": "YTD",
                    "label": "YTD"
                }, {
                    "period": "MAX",
                    "label": "MAX"
                } ]
            },

            "dataSetSelector": {
                "position": "left"
            },

            "export": {
                "enabled": true
            }
        } );
        $('.amcharts-data-set-select').addClass('form-control');
    };

    var init_app_table = function () {

        $.ajax({
            url: PECO.base_url() + 'cad/getapplications',
            type: 'post',
            dataType: 'json',
        }).done(function(data){
            console.log(data);
            var oTable = tbl_app.dataTable({
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                //bProcessing: true,
                aaData: data.list,
                aoColumns: [
                    {data: 'expand', sWidth: '50px', sClass: 'text-align-center hidden-print'},
                    {data: 'name', sClass: 'text-info text-bold'},
                    {data: 'address'},
                    {data: 'pending'},
                    {data: 'datestart'},
                    {data: 'dateend'},
                    {data: 'reqstat', sWidth: '200px'},
                    {data: 'status', sWidth: '120px', sClass: 'hidden-print'},
                ],
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries found",
                    "infoFiltered": "(filtered1 from _MAX_ total entries)",
                    "lengthMenu": "Show _MENU_ entries",
                    "search": "Search:",
                    "zeroRecords": "No matching records found"
                },
                "order": [
                    [0, 'asc']
                ],
                "lengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                "pageLength": 10, // set the initial value,
                "columnDefs": [{  // set default column settings
                    'orderable': false,
                    'targets': [0]
                }, {
                    "searchable": false,
                    "targets": [0]
                }],
                "order": [
                    [1, "asc"]
                ],
                // set the initial value
                "pageLength": 10,

                "dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",


                "tableTools": {
                    "sSwfPath": PECO.base_url() + "assets/global/plugins/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [{
                        "sExtends": "pdf",
                        "sButtonText": "PDF"
                    }, {
                        "sExtends": "csv",
                        "sButtonText": "CSV"
                    }, {
                        "sExtends": "xls",
                        "sButtonText": "Excel"
                    }, {
                        "sExtends": "print",
                        "sButtonText": "Print",
                        "sInfo": 'Please press "CTR+P" to print or "ESC" to quit',
                        "sMessage": "APT "
                    }]
                },
                searchHighlight: true,
            });

            var oTableColReorder = new $.fn.dataTable.ColReorder( oTable );

            var tableWrapper = $('#tbl_app_list_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper
            tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown

            $('.tooltips').tooltip();
            PECO.initDTNicescroller();

            if(PECO.sysCheckMode()==true) {
                window.onscroll = function (ev) {
                    if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight) {
                        if(!$('#toast-container').length || !$('.fancybox-overlay').length) {
                            /*
                            setTimeout(function () {
                                PECO.initAlerts('Table column of application list pending transaction can be re-arange :) <br> ' +
                                    '<a target="_blank" class="mix-preview fancybox-button" href="' + PECO.base_url() + 'assets/global/img/drag_column_tips.png" title="Tips: Column Re-order" data-rel="fancybox-button">' +
                                    '<img src="' + PECO.base_url() + 'assets/global/img/drag_column_tips.png" width="100%" />' +
                                    '</a>', 'Tips', 'info', true);
                            }, 2000);

                            $("a[href*='.jpg'], a[href*='.png']").attr('class', 'fancybox').fancybox({
                                maxWidth: 900,
                                maxHeight: 700,
                                fitToView: true,
                                width: '80%',
                                height: '80%',
                                autoSize: true,
                                closeClick: false,
                                openEffect: 'none',
                                closeEffect: 'none'
                            });
                            */
                        }
                    }
                };
            }

        }).fail(function(){
            PECO.DTphpError(tbl_app);
        });
    };


    return {
        applied: function() {
            init_app_client();
            init_app_table();
            init_apt_summary();
            init_apt_aging();
            PECO.dtSubDetails(tbl_app, 'cad/getapplicationinfo');
        }
    }
}();
