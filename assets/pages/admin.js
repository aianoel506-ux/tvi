var ADMINDASBHOARD = function() {

    var amchart_init = function(id){

        var gaugeChart = AmCharts.makeChart(id, {
            "type": "gauge",
            "theme": "light",
            "axes": [{
                "axisThickness": 1,
                "axisAlpha": 0.2,
                "tickAlpha": 0.2,
                "valueInterval": 20,
                "bands": [{
                    "color": "#84b761",
                    "endValue": 50,
                    "startValue": 0
                }, {
                    "color": "#fdd400",
                    "endValue": 70,
                    "startValue": 50
                }, {
                    "color": "#cc4748",
                    "endValue": 100,
                    //"innerRadius": "95%",
                    "startValue": 70
                }],
                "bottomText": "0%",
                "bottomTextYOffset": -20,
                "endValue": 100
            }],
            "arrows": [{
                "color": "#67b7dc",
                "innerRadius": "20%",
                "nailRadius": 0,
                "radius": "85%"
            }],
            "export": {
                "enabled": false
            }
        });
        return gaugeChart;
    };

    var init_admin_dashboard = function() {

        var cpu_gaugeChart = amchart_init('cpugauge');
        var mem_gaugeChart = amchart_init('memgauge');
        var disk_gaugeChart = amchart_init('diskgauge');
        var temp_gaugeChart = amchart_init('tempgauge');

        init_ajax_gauge('settings/gettemp', 'temperature', temp_gaugeChart);
        init_ajax_gauge('settings/getcpu', 'cpu', cpu_gaugeChart);
        init_ajax_gauge('settings/getmem', 'mem', mem_gaugeChart);
        init_ajax_gauge('settings/getdisk', 'disk', disk_gaugeChart);
    };

    var init_ajax_gauge = function(url, name, gauge) {
        $.ajax({
            url: PECO.base_url() + url,
            dataType: 'json',
            success: function (response) {
                update(name, response);
                if(response.percent) {
                    if (gauge) {
                        if (gauge.arrows) {
                            if (gauge.arrows[0]) {
                                if (gauge.arrows[0].setValue) {
                                    gauge.arrows[0].setValue(response.percent);
                                    gauge.axes[0].setBottomText(response.percent + response.unit);
                                }
                            }
                        }
                    }
                }else{
                    if (gauge) {
                        if (gauge.arrows) {
                            if (gauge.arrows[0]) {
                                if (gauge.arrows[0].setValue) {
                                    gauge.arrows[0].setValue(0);
                                    gauge.axes[0].setBottomText(0 + response.unit);
                                }
                            }
                        }
                    }
                }

                setTimeout(function () {
                    init_ajax_gauge(url, name, gauge);
                }, 1000);
            }
        });
    };


    var update = function(name, response) {
        if(typeof response.output != "undefined") {
            $("#" + name + "Div .title").text(response.title);
            $("#" + name + "Div pre").text(response.output.join('\n'));

        }else{
            $("#" + name + "Div .title").text('No Sensor!');
            $("#" + name + "Div pre").text('No sensor detected on this device!');
        }


        $("pre").niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '5',
            cursorborderradius: '1px',
            background: 'transparent',
            cursorborder: '',
            zindex: '1000'
        });
    };


    return {
        init: function() {
            init_admin_dashboard();
        }
    }
}();