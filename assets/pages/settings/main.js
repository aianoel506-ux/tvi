var SETTINGS = function () {
// INITIALIZE HIGHLIGHTS SEARCH IN TABLE
    PECO.getHighlightsPlugin();
    PECO.getSelectPlugins();
    // VARIABLES
    var tbl_changes = $('body').find('#tbl_changes');
    var tbl_aging = $('body').find('#tbl_aging');
    var tbl_matric = $('body').find('#tbl_matric');
    var tbl_responsibility = $('body').find('#tbl_responsibility');
    var commit_graph = "commit_graph";
    var dev_graph = "dev_graph";
    var dev_graph_summ = "dev_graph_summ";
    var init_func = function () {

    }
    var init_project_mon = function () {
        init_tbl_proj_mon_responsibility();
        init_commit_chart(commit_graph);
        $('body').on('click', '#ref_git_data', function (e) {
            init_commit_chart(commit_graph);
        });
        $('#get_git_data').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: PECO.base_url() + 'settings/getprojmondata',
                type: 'post',
                dataType: 'json',
            }).done(function (d) {
                console.log(d);
                init_commit_chart(commit_graph);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };
    var init_tbl_proj_mon_matrics = function () {
        $.ajax({
            url: PECO.base_url() + 'settings/getprojmonmatrics',
            type: 'post',
            dataType: 'json',
            data: {}
        }).done(function (data) {
            $('#matrics_message').html(data.message);
            tbl_matric.dataTable().empty();
            tbl_matric.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bSort: true,
                bStateSave: true,
                bProcessing: true,
                //scrollY: '300px',
                keys: true,
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                },
                aaData: data.list,
                aoColumns: [
                    {"data": "line", sClass: 'number text-bold', sWidth: ''},
                    {"data": "name", sClass: '', sWidth: ''},
                ],
                fnRowCallback: function (nRow, aData) {

                }
            });
            PECO.initDTNicescroller();
        }).fail(function () {
            PECO.phpError();
        });
    };
    var init_tbl_proj_mon_responsibility = function () {
        $.ajax({
            url: PECO.base_url() + 'settings/getprojmonresponsibility',
            type: 'post',
            dataType: 'json',
            data: {}
        }).done(function (data) {
            console.log(data);
            $('#responsibility_message').html(data.message);
            tbl_responsibility.dataTable().empty();
            tbl_responsibility.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bSort: true,
                bStateSave: true,
                bProcessing: true,
                //scrollY: '450px',
                keys: true,
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                },
                aaData: data.list,
                aoColumns: [
                    {"data": "pic", sClass: '', sWidth: '30px'},
                    {"data": "name", sClass: 'text-info', sWidth: '100px'},
                    {"data": "files", sClass: '', sWidth: ''},
                ],
                fnRowCallback: function (nRow, aData) {

                }
            });
            PECO.initDTNicescroller();
        }).fail(function () {
            PECO.phpError();
        });
    };
    var init_commit_chart = function (id) {
        $.ajax({
            url: PECO.base_url() + 'settings/getprojmongraph',
            type: 'post',
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                //id.html('<h4 class="text-info"><i class="fa fa-circle-o-notch fa-spin"></i> Loading graph..</h4>');	
            }
        }).done(function (d) {
            var commits = d.versions;
            var developers = d.dev;
            // console.log('DEVELOPERS: ' + developers);
            // console.log('COMMITS: ' + commits);
            var general_chart = AmCharts.makeChart(id, {
                "type": "serial",
                "theme": "light",
                "dataDateFormat": "YYYY-MM-DD",
                "dataProvider": commits,
                "addClassNames": true,
                "startDuration": 1,
                //"color": "#FFFFFF",
                "synchronizeGrid": true,
                "marginLeft": 0,
                "categoryField": "date",
                
                "categoryAxis": {
                    "parseDates": true,
                    "minPeriod": "DD",
                    "autoGridCount": true,
                    //"gridCount": 30,
                    "gridAlpha": 0.1,
                    "gridColor": "#000",
                    "axisColor": "#555555",
                    "dateFormats": [{
                            "period": 'DD',
                            "format": 'DD'
                        }, {
                            "period": 'WW',
                            "format": 'MMM DD'
                        }, {
                            "period": 'MM',
                            "format": 'MMM'
                        }, {
                            "period": 'YYYY',
                            "format": 'YYYY'
                        }
                    ]
                },
                "valueAxes": [{
                        "id": "a1",
                        "title": "Changes / Commits",
                        "gridAlpha": 0,
                        "axisAlpha": 0,
                        "gridColor": "#CCCFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0,
                        //"offset": 65,
                        "axisAlpha": 1,
                        "position": "left"
                    }, {
                        "id": "a2",
                        "title": "",
                        "position": "left",
                        "gridAlpha": 0,
                        "axisAlpha": 0,
                        "labelsEnabled": false,
                        "gridColor": "#CCCFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                    }, {
                        "id": "a3",
                        "title": "Deletions / Insertions",
                        "position": "right",
                        "gridAlpha": 0,
                        "axisAlpha": 0,
                        "inside": false,
                        "gridColor": "#CCCFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                    }, {
                        "id": "a4",
                        "title": "",
                        "position": "right",
                        "gridAlpha": 0,
                        "axisAlpha": 0,
                        "inside": false,
                        "gridColor": "#CCCFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0,
                        "labelsEnabled": false,
                    },
                ],
                "graphs": [
                    {
                        "id": "g1",
                        "valueField": "value",
                        "title": "Commits",
                        "type": "column",
                        "fillAlphas": 0.5,
                        "valueAxis": "a1",
                        "balloonText": "Commits: <b>[[value]]</b>",
                        "legendValueText": "[[value]] hits",
                        "legendPeriodValueText": "total: [[value.sum]] hi",
                        "lineColor": "#4da6ff",
                        "alphaField": "alpha"
                    }, {
                        "id": "g2",
                        "title": "changes",
                        "valueField": "changes",
                        "classNameField": "bulletClass",
                        "type": "line",
                        "valueAxis": "a2",
                        "lineColor": "#ffb701",
                        "lineThickness": 2,
                        "legendValueText": "[[value]]/[[value]]",
                        "descriptionField": "townName",
                        "bullet": "round",
                        "bulletSizeField": "townSize",
                        "bulletBorderColor": "#ffb701",
                        "bulletBorderAlpha": 1,
                        "bulletBorderThickness": 2,
                        "bulletColor": "#ffb701",
                        "labelText": "[[townName2]]",
                        "labelPosition": "right",
                        "balloonText": "Changes: <b>[[value]]</b>",
                        "showBalloon": true,
                        "animationPlayed": true,
                    }, {
                        "id": "g3",
                        "title": "Deletions",
                        "valueField": "deletions",
                        "type": "line",
                        "valueAxis": "a3",
                        "lineColor": "#ff5755",
                        "balloonText": "[[value]]",
                        "lineThickness": 2,
                        "legendValueText": "[[value]]",
                        "bullet": "square",
                        "bulletBorderColor": "#ff5755",
                        "bulletBorderThickness": 1,
                        "bulletBorderAlpha": 1,
                        "dashLengthField": "dashLength",
                        "animationPlayed": true,
                        "balloonText": "Deletion: <b>[[value]]</b>",
                    }, {
                        "id": "g4",
                        "title": "Insertion",
                        "valueField": "insertions",
                        "type": "line",
                        "valueAxis": "a4",
                        "lineColor": "#023dff",
                        "balloonText": "[[value]]",
                        "lineThickness": 2,
                        "legendValueText": "[[value]]",
                        "bullet": "square",
                        "bulletBorderColor": "#023dff",
                        "bulletBorderThickness": 1,
                        "bulletBorderAlpha": 1,
                        "dashLengthField": "dashLength",
                        "animationPlayed": true,
                        "balloonText": "Insertion: <b>[[value]]</b>",
                    }
                ],
                "stockEvents": [{
                    date: new Date(2017, 1, 19),
                    type: "sign",
                    backgroundColor: "#85CDE6",
                    graph: "g1",
                    text: "S",
                    description: "This is description of an event"
                },
                ],
                "chartCursor": {
                    "zoomable": false,
                    "categoryBalloonDateFormat": "DD",
                    "cursorAlpha": 0,
                    "valueBalloonsEnabled": false
                },
                "chartCursor": {
                    "graphBulletSize": 1.5,
                    "oneBalloonOnly": false,
                    "zoomable": true,
                },
                "legend": {
                    "bulletType": "round",
                    "equalWidths": false,
                    "valueWidth": 120,
                    "useGraphSettings": true,
                },
                "scrollBarSettings": {
                    "graphType": "line",
                    "usePeriod": "mm"
                },
                "panelsSettings": {
                    "fontFamily": "Arial",
                    "creditsPosition": "bottom-right",
                    "panelSpacing": 1,
                    "marginLeft": 15,
                    "marginRight": 15,
                    "usePrefixes": true,
                    "panEventsEnabled": true
                },
                "chartCursorSettings": {
                    "cursorAlpha": 0.5,
                    "cursorColor": '#444444',
                    "valueLineAlpha": 0,
                    "valueBalloonsEnabled": true,
                    "oneBalloonOnly": true
                },
                "categoryAxesSettings": {
                    "minPeriod": "hh",
                    "parseDates": true,
                    "equalSpacing": false,
                    "gridAlpha": 0,
                    "axisAlpha": 0,
                    "inside": true,
                    "maxSeries": 0
                },
                "periodSelector": {
                    "position": "left",
                    "inputFieldsEnabled": false,
                    "periods": [{
                            "period": "DD",
                            "selected": false,
                            "count": 1,
                            "label": "1d"
                        }, {
                            "period": "DD",
                            "selected": false,
                            "count": 10,
                            "label": "10d"
                        }, {
                            "period": "MM",
                            "selected": true,
                            "count": 1,
                            "label": "1m"
                        }, {
                            "period": "MM",
                            "selected": false,
                            "count": 6,
                            "label": "6m"
                        }, {
                            "period": "YYYY",
                            "selected": false,
                            "count": 6,
                            "label": "1y"
                        }, {
                            "period": "MAX",
                            "label": "MAX",
                            selected: false,
                        },
                        
                    ]
                },
                "export": {
                    "enabled": true
                }
            });
            // DEV PIE GRAPH
            var dev_charts = AmCharts.makeChart(dev_graph, {
                "type": "pie",
                "startDuration": 1,
                //"startEffect": "elastic",
                "theme": "light",
                "addClassNames": true,
                "titles": [{
                        "text": "PECO.net Development Authors",
                        "position": "left"
                    }],
                "valueField": "size",
                "pullOutRadius": 20,
                "depth3D": 2,
                "legend": {
                    "position": "right",
                    "marginRight": 100,
                    "autoMargins": true
                },
                "innerRadius": "20%",
                "defs": {
                    "filter": [{
                            "id": "shadow",
                            "width": "250%",
                            "height": "250%",
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
                "dataProvider": developers,
                "valueField": "activities",
                "titleField": "name",
                "colorField": "color",
                "labelColorField": "color",
                "export": {
                    "enabled": true
                }
            });
            dev_charts.addListener("init", handleInit);
            dev_charts.addListener("rollOverSlice", function (e) {
                handleRollOver(e);
            });
            function handleInit() {
                dev_charts.legend.addListener("rollOverItem", handleRollOver);
            }

            function handleRollOver(e) {
                var wedge = e.dataItem.wedge.node;
                wedge.parentNode.appendChild(wedge);
            }

            // DEV STATS
            var graphs_arr = d.dev;
            console.log(graphs_arr);
            var dev_charts_suumm = AmCharts.makeChart(dev_graph_summ, {
                "type": "serial",
                "theme": "light",
                "dataProvider": graphs_arr,
                "valueAxes": [{
                        //"maximum": 1000,
                        "minimum": 20,
                        "axisAlpha": 0,
                        "dashLength": 4,
                        "position": "left"
                    }],
                "startDuration": 1,
                "graphs": [{
                        "balloonText": "<span style='font-size:13px;'>[[category]]'s activities: <b>[[value]]</b> commits</span>",
                        "bulletOffset": 30,
                        "bulletSize": 32,
                        "colorField": "color",
                        "cornerRadiusTop": 0,
                        "customBulletField": "pics",
                        "fillAlphas": 1,
                        "lineAlpha": 0,
                        "type": "column",
                        "valueField": "activities",
                        "fixedColumnWidth": 60,
                    }],
                "marginTop": 10,
                "marginRight": 20,
                "marginLeft": 100,
                "marginBottom": 10,
                "autoMargins": false,
                "categoryField": "name",
                "categoryAxis": {
                    "axisAlpha": 0,
                    "gridAlpha": 0,
                    "inside": true,
                    "tickLength": 0
                },
                "export": {
                    "enabled": true
                }
            });



        });
    };
    return {
        init: function () {
            init_func();
        }
        ,
        initprojectmon: function () {
            init_project_mon();
        }
    };
}();


