/**
 * Created by SE on 0011, May 11, 2017.
 */
var BUDGET = function() {
    var init_budget_projection = function() {
        var chart = AmCharts.makeChart("budget_projection", {
            "type": "serial",
            "theme": "light",
            "pathToImages": PECO.getGlobalPluginsPath() + "amcharts/amcharts/images/",
            "autoMargins": false,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "addClassNames": true,
            "startDuration": 1,
            "fontFamily": 'Open Sans',
            "color":    '#888',

            "dataProvider": [{
                "year": 2009,
                "budget": 23.5,
                "expenses": 18.1
            }, {
                "year": 2010,
                "budget": 26.2,
                "expenses": 22.8
            }, {
                "year": 2011,
                "budget": 30.1,
                "expenses": 23.9
            }, {
                "year": 2012,
                "budget": 29.5,
                "expenses": 25.1
            }, {
                "year": 2013,
                "budget": 30.6,
                "expenses": 27.2,
            }, {
                "year": 2014,
                "budget": 34.1,
                "expenses": 29.9,
            }, {
                "year": 2015,
                "budget": 35.1,
                "expenses": 30.9,
            },  {
                "year": 2016,
                "budget": 32.1,
                "expenses": 29.9,
            }, {
                "year": 2017,
                "budget": 35.1,
                "expenses": 31.2,
                "dashLengthColumn": 5,
                "alpha": 0.2,
                "additional": "(projection)"
            }
            ],
            "valueAxes": [{
                "axisAlpha": 0,
                "position": "left"
            }],
            "startDuration": 1,
            "graphs": [{
                "alphaField": "alpha",
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "dashLengthField": "dashLengthColumn",
                "fillAlphas": 1,
                "title": "Budget",
                "type": "column",
                "valueField": "budget",

            }, {
                "alphaField": "alpha",
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "dashLengthField": "dashLengthColumn",
                "fillAlphas": 1,
                "type": "column",
                "title": "Expenses",
                "valueField": "expenses",
            }],
            "categoryField": "year",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0
            },
            "export": {
                "enabled": true
            },
            panels: [{
                title: "Expenses",
                percentHeight: 70,
                stockGraphs: [{
                    id: "g1",
                    valueField: "value"
                }],
                stockLegend: {
                    valueTextRegular: " ",
                    markerType: "none"
                }
            }],

            chartScrollbarSettings: {
                graph: "expenses"
            },
        });

        $('#budget_projection').closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });


        var cc_stat = AmCharts.makeChart("cost_center_stats", {
            "theme": "light",
            "type": "serial",
            "dataProvider": [{
                "costcenter": "400",
                "expense": 3.5,
                "budget": 4.2
            }, {
                "costcenter": "600",
                "expense": 1.7,
                "budget": 3.1
            }, {
                "costcenter": "300",
                "expense": 2.8,
                "budget": 2.9
            }, {
                "costcenter": "301",
                "expense": 2.6,
                "budget": 2.3
            }, {
                "costcenter": "100",
                "expense": 1.4,
                "budget": 2.1
            }, {
                "costcenter": "401",
                "expense": 2.6,
                "budget": 4.9
            }
            ],
            "valueAxes": [{
                "unit": "%",
                "position": "left",
                "title": "GDP growth rate",
            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "GDP grow in [[category]] Expense: <b>[[value]]</b>",
                "fillAlphas": 0.9,
                "lineAlpha": 0.2,
                "title": "2004",
                "type": "column",
                "valueField": "expense"
            }, {
                "balloonText": "GDP grow in [[category]] Budget: <b>[[value]]</b>",
                "fillAlphas": 0.9,
                "lineAlpha": 0.2,
                "title": "2005",
                "type": "column",
                "clustered":false,
                "columnWidth":0.5,
                "valueField": "budget"
            }],
            "plotAreaFillAlphas": 0.1,
            "categoryField": "costcenter",
            "categoryAxis": {
                "gridPosition": "start"
            },
            "export": {
                "enabled": true
            }
        });
    }

    var init_budget_pie = function() {
        /**
         * Define data for each year
         */
        var chartData = {
            "2003": [
                { "sector": "ITD", "size": 6.6 },
                { "sector": "CNC", "size": 0.6 },
                { "sector": "AUDIT", "size": 23.2 },
                { "sector": "ACCOUNTING", "size": 2.2 },
                { "sector": "LEGAL", "size": 4.5 },
                { "sector": "PURCHASING", "size": 14.6 },
                { "sector": "MAIN OFFICE", "size": 9.3 },
                { "sector": "CAD", "size": 22.5 } ],
            "2004": [
                { "sector": "ITD", "size": 6.4 },
                { "sector": "CNC", "size": 0.5 },
                { "sector": "AUDIT", "size": 22.4 },
                { "sector": "ACCOUNTING", "size": 2 },
                { "sector": "LEGAL", "size": 4.2 },
                { "sector": "PURCHASING", "size": 14.8 },
                { "sector": "MAIN OFFICE", "size": 9.7 },
                { "sector": "CAD", "size": 22 } ],
            "2005": [
                { "sector": "ITD", "size": 6.1 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 20.9 },
                { "sector": "ACCOUNTING", "size": 1.8 },
                { "sector": "LEGAL", "size": 4.2 },
                { "sector": "PURCHASING", "size": 13.7 },
                { "sector": "MAIN OFFICE", "size": 9.4 },
                { "sector": "CAD", "size": 22.1 } ],
            "2006": [
                { "sector": "ITD", "size": 6.2 },
                { "sector": "CNC", "size": 0.3 },
                { "sector": "AUDIT", "size": 21.4 },
                { "sector": "ACCOUNTING", "size": 1.9 },
                { "sector": "LEGAL", "size": 4.2 },
                { "sector": "PURCHASING", "size": 14.5 },
                { "sector": "MAIN OFFICE", "size": 10.6 },
                { "sector": "CAD", "size": 23 } ],
            "2007": [
                { "sector": "ITD", "size": 5.7 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 20 },
                { "sector": "ACCOUNTING", "size": 1.8 },
                { "sector": "LEGAL", "size": 4.4 },
                { "sector": "PURCHASING", "size": 15.2 },
                { "sector": "MAIN OFFICE", "size": 10.5 },
                { "sector": "CAD", "size": 24.7 } ],
            "2008": [
                { "sector": "ITD", "size": 5.1 },
                { "sector": "CNC", "size": 0.3 },
                { "sector": "AUDIT", "size": 20.4 },
                { "sector": "ACCOUNTING", "size": 1.7 },
                { "sector": "LEGAL", "size": 4 },
                { "sector": "PURCHASING", "size": 16.3 },
                { "sector": "MAIN OFFICE", "size": 10.7 },
                { "sector": "CAD", "size": 24.6 } ],
            "2009": [
                { "sector": "ITD", "size": 5.5 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 20.3 },
                { "sector": "ACCOUNTING", "size": 1.6 },
                { "sector": "LEGAL", "size": 3.1 },
                { "sector": "PURCHASING", "size": 16.3 },
                { "sector": "MAIN OFFICE", "size": 10.7 },
                { "sector": "CAD", "size": 25.8 } ],
            "2010": [
                { "sector": "ITD", "size": 5.7 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 20.5 },
                { "sector": "ACCOUNTING", "size": 1.6 },
                { "sector": "LEGAL", "size": 3.6 },
                { "sector": "PURCHASING", "size": 16.1 },
                { "sector": "MAIN OFFICE", "size": 10.7 },
                { "sector": "CAD", "size": 26 } ],
            "2011": [
                { "sector": "ITD", "size": 4.9 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 19.4 },
                { "sector": "ACCOUNTING", "size": 1.5 },
                { "sector": "LEGAL", "size": 3.3 },
                { "sector": "PURCHASING", "size": 16.2 },
                { "sector": "MAIN OFFICE", "size": 11 },
                { "sector": "CAD", "size": 27.5 } ],
            "2012": [
                { "sector": "ITD", "size": 4.7 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 18.4 },
                { "sector": "ACCOUNTING", "size": 1.4 },
                { "sector": "LEGAL", "size": 3.3 },
                { "sector": "PURCHASING", "size": 16.9 },
                { "sector": "MAIN OFFICE", "size": 10.6 },
                { "sector": "CAD", "size": 28.1 } ],
            "2013": [
                { "sector": "ITD", "size": 4.3 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 18.1 },
                { "sector": "ACCOUNTING", "size": 1.4 },
                { "sector": "LEGAL", "size": 3.9 },
                { "sector": "PURCHASING", "size": 15.7 },
                { "sector": "MAIN OFFICE", "size": 10.6 },
                { "sector": "CAD", "size": 29.1 } ],
            "2014": [
                { "sector": "ITD", "size": 4 },
                { "sector": "CNC", "size": 0.2 },
                { "sector": "AUDIT", "size": 16.5 },
                { "sector": "ACCOUNTING", "size": 1.3 },
                { "sector": "LEGAL", "size": 3.7 },
                { "sector": "PURCHASING", "size": 14.2 },
                { "sector": "MAIN OFFICE", "size": 12.1 },
                { "sector": "CAD", "size": 29.1 } ],
            "2015": [
                { "sector": "ITD", "size": 2000 },
                { "sector": "CNC", "size": 3000 },
                { "sector": "AUDIT", "size": 2000 },
                { "sector": "ACCOUNTING", "size": 6000 },
                { "sector": "LEGAL", "size": 4000 },
                { "sector": "PURCHASING", "size": 2000 },
                { "sector": "MAIN OFFICE", "size": 3000 },
                { "sector": "CAD", "size": 2000 } ],
            "2016": [
                { "sector": "ITD", "size": 1000 },
                { "sector": "CNC", "size": 5000 },
                { "sector": "AUDIT", "size": 3000 },
                { "sector": "ACCOUNTING", "size": 5000 },
                { "sector": "LEGAL", "size": 6000 },
                { "sector": "PURCHASING", "size": 3000 },
                { "sector": "MAIN OFFICE", "size": 2000 },
                { "sector": "CAD", "size": 3000 } ]

        };


        /**
         * Create the chart
         */
        var currentYear = 2016;
        var pie_chart = AmCharts.makeChart( "cc_pie_chart", {
            "type": "pie",
            "theme": "light",
            "dataProvider": [],
            "valueField": "size",
            "titleField": "sector",
            "startDuration": 0,
            "innerRadius": 80,
            "pullOutRadius": 20,
            "addClassNames": true,
            "marginTop": 30,
            "titles": [{
                "text": "Cost Center Budgets"
            }],
            "allLabels": [{
                "y": "54%",
                "align": "center",
                "size": 25,
                "bold": true,
                "text": "2003",
                "color": "#555"
            }, {
                "y": "49%",
                "align": "center",
                "size": 15,
                "text": "Year",
                "color": "#555"
            }],
            "listeners": [ {
                "event": "init",
                "method": function( e ) {
                    var chart = e.chart;

                    function getCurrentData() {
                        var data = chartData[currentYear];
                        currentYear++;
                        if (currentYear > 2016)
                            currentYear = 2003;
                        return data;
                    }

                    function loop() {
                        chart.allLabels[0].text = currentYear;
                        var data = getCurrentData();
                        chart.animateData( data, {
                            duration: 1000,
                            complete: function() {
                                setTimeout( loop, 3000 );
                            }
                        } );
                    }

                    loop();
                }
            } ],
            "export": {
                "enabled": true
            },
            "legend":{
                "position":"right",
                "marginRight":100,
                "autoMargins":false
            },
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
        } );
        pie_chart.addListener("init", handleInit);

        pie_chart.addListener("rollOverSlice", function(e) {
            handleRollOver(e);
        });

        function handleInit(){
            pie_chart.legend.addListener("rollOverItem", handleRollOver);
        }

        function handleRollOver(e){
            var wedge = e.dataItem.wedge.node;
            wedge.parentNode.appendChild(wedge);
        }
    }

    return {
        projection: function() {
            init_budget_projection();
            init_budget_pie();
        }
    }
}();