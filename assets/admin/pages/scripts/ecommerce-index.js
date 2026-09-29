var EcommerceIndex = function () {
	

	
    function showTooltip(x, y, labelX, labelY) {
        $('<div id="tooltip" class="chart-tooltip">' + (labelY.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')) + ' PESOS<\/div>').css({
            position: 'absolute',
            display: 'none',
            top: y - 40,
            left: x - 60,
            border: '0px solid #ccc',
            padding: '2px 6px',
            'background-color': '#fff'
        }).appendTo("body").fadeIn(200);
    }

    var initChart1 = function () {
		$.ajax({
			type: 'POST',
			url: base_url+'admin/sample_flot/',
			data: 'html',   
			cache: false,
			dataType:"json"
		}).done(function(responsse){
			$.new_data = responsse['example'];
			var data = $.new_data;
			var plot_statistics = $.plot(
                $("#statistics_1"), 
                [
                    {
                        data:data,
                        lines: {
                            fill: 0.4,
                            lineWidth: 0
                        },
                        color: ['#9ACAE6']
                    },
                    {
                        data: data,
                        points: {
                            show: true,
                            fill: true,
                            radius: 5,
                            fillColor: "#9ACAE6",
                            lineWidth: 3
                        },
                        color: 'rgba(255,255,255,0.6)',
                        shadowSize: 0
                    }
                ], 
                {

                    xaxis: {
                        tickLength: 0,
                        tickDecimals: 0,                        
                        mode: "categories",
                        min: 2,
                        font: {
                            lineHeight: 15,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    yaxis: {
                        ticks: 3,
                        tickDecimals: 0,
                        tickColor: "#f0f0f0",
                        font: {
                            lineHeight: 15,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    grid: {
                        backgroundColor: {
                            colors: ["#fff", "#fff"]
                        },
                        borderWidth: 1,
                        borderColor: "#f0f0f0",
                        margin: 0,
                        minBorderMargin: 0,
                        labelMargin: 20,
                        hoverable: true,
                        clickable: true,
                        mouseActiveRadius: 6
                    },
                    legend: {
                        show: false
                    }
                }
            );
		});
        

		var previousPoint = null;

		$("#statistics_1").bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));
			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;

					$("#tooltip").remove();
					var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);

					showTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1]);
				}
			} else {
				$("#tooltip").remove();
				previousPoint = null;
			}
		});

    }
	var initCalendar = function () {
            if (!jQuery().fullCalendar) {
                return;
            }

            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();

            var h = {};

            if ($('#calendar').width() <= 400) {
                $('#calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'today,month,agendaWeek,agendaDay'
                };
            } else {
                $('#calendar').removeClass("mobile");
                if (PECO.isRTL()) {
                    h = {
                        right: 'title',
                        center: '',
                        left: 'prev,next,today,month,agendaWeek,agendaDay'
                    };
                } else {
                    h = {
                        left: 'title',
                        center: '',
                        right: 'prev,next,today,month,agendaWeek,agendaDay'
                    };
                }
            }

           
			$.ajax({
					type: 'POST',
					url: base_url+'admin/sample_calendar/',
					data: 'html',   
					cache: false,
					dataType:"json"
				}).done(function(response){
					$('#calendar').fullCalendar('destroy'); // destroy the calendar
					$('#calendar').fullCalendar({ //re-initialize the calendar
						disableDragging : true,
						header: h,
						editable: false,
						events: [
							{
								title  : 'event1',
								start  : '2010-01-01'
							},
							{
								title  : 'event2',
								start  : '2010-01-05',
								end    : '2010-01-07'
							},
							{
								title  : 'event3',
								start  : '2010-01-09T12:30:00',
								allDay : false // will make the time show
							}
						]
					});
					console.log(response);
				});
        }
		
		


    return {

        //main function
        init: function () {
            initChart1();
			initCalendar();
        }

    };

}();