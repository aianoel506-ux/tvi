var Calendar = function() {

    var init_puslsate = function() {
        $('body').find('.holiday-pulse').each(function(){
            var this_ = $(this);
            var x = this_.closest('.fc-day-grid-event').css('backgroundColor');
            $(this).pulsate({
                color: hexc(x),
                reach: 20,
                speed: 500,
                glow: true,
                repeat: 5,
            });
        });
    };

    // CONVERT COLOR TO HEX
    var hexc = function (colorval) {
        var parts = colorval.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        delete(parts[0]);
        for (var i = 1; i <= 3; ++i) {
            parts[i] = parseInt(parts[i]).toString(16);
            if (parts[i].length == 1) parts[i] = '0' + parts[i];
        }
        return '#' + parts.join('');
    }

    var ajax_add_events = function() {
        var events_arr = [];
        $.ajax({
            url: PECO.base_url() + 'admin/events',
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d) {
            events_arr = d.list;
            console.log(events_arr);
        }).fail(function() {
            PECO.phpError();
        });
        return events_arr;
    };
    // Any value represanting monthly repeat flag
    var REPEAT_MONTHLY = 1;
    // Any value represanting yearly repeat flag
    var REPEAT_YEARLY = 2;
    var defaultEvents = ajax_add_events();

    var init_calendar_table = function() {
        if (!jQuery().fullCalendar) {
            return;
        }

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        var h = {};

        if (PECO.isRTL()) {
            if ($('#calendar').parents(".portlet").width() <= 720) {
                $('#calendar').addClass("mobile");
                h = {
                    right: 'title, prev, next',
                    center: '',
                    left: 'agendaDay, agendaWeek, month, today'
                };
            } else {
                $('#calendar').removeClass("mobile");
                h = {
                    right: 'title',
                    center: '',
                    left: 'agendaDay, agendaWeek, month, today, prev,next'
                };
            }
        } else {
            if ($('#calendar').parents(".portlet").width() <= 720) {
                $('#calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'today,month,agendaWeek,agendaDay'
                };
            } else {
                $('#calendar').removeClass("mobile");
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev,next,today,month,agendaWeek,agendaDay'
                };
            }
        }

        var initDrag = function(el) {
            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
                title: $.trim(el.text()), // use the element's text as the event title,
                allDay: el.attr('event-stat'),
            };
            // store the Event Object in the DOM element so we can get to it later
            el.data('eventObject', eventObject);
            // make the event draggable using jQuery UI

            el.draggable({
                zIndex: 999,
                revert: true, // will cause the event to go back to its
                revertDuration: 0 //  original position after the drag
            });
            // INITIALIZE EVEVENTS
            //console.log(eventObject);
        };

        var addEvent = function(title) {
            title = title.length === 0 ? "Untitled Event" : title;
            var html = $('<div class="external-event label label-default">' + title + '</div>').attr('event-stat', true);
            jQuery('#event_box').append(html);
            initDrag(html);
        };

        $('#external-events .external-event').each(function() {
            initDrag($(this));
        });

        $('#event_add').unbind('click').click(function() {
            var title = $('#event_title').val();
            addEvent(title);
        });

        //predefined events
        $('#event_box').html("");
        addEvent("My Event 1");
        addEvent("My Event 2");
        addEvent("My Event 3");
        addEvent("My Event 4");
        addEvent("My Event 5");
        addEvent("My Event 6");

        $('#calendar').fullCalendar('destroy'); // destroy the calendar
        $('#calendar').fullCalendar({ //re-initialize the calendar
            header: h,
            defaultView: 'month', // change default view with available options from http://arshaw.com/fullcalendar/docs/views/Available_Views/
            slotMinutes: 15,
            editable: true,
            droppable: false, // this allows things to be dropped onto the calendar !!!
            eventSources: [defaultEvents],

            drop: function(event, date, allDay) { // this function is called when something is dropped
                // @TODO EVENT HANDLE FOR DRAG AND DROP IS BUG ON UI-DIALOG DRAG

                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('eventObject');

                console.log(event);

                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject);

                // assign it the date that was reported
                copiedEventObject.start = date;
                copiedEventObject.allDay = allDay;
                copiedEventObject.className = $(this).attr("data-class");

                // render the event on the calendar
                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove();
                }

            },

            eventResizeStart: function( event, jsEvent, ui, view ) {
                console.log({'Event Resize Start': event });
                //console.log({'Event: jsEvent': jsEvent});
                //console.log({'Event: ui': ui});
                //console.log({'Event: view': view});
            },
            eventResizeStop: function( event, jsEvent, ui, view ) {
                console.log({'Event Resize Start': event });
                //console.log({'Event: jsEvent': jsEvent});
                //console.log({'Event: ui': ui});
                //console.log({'Event: view': view});
            },

            eventDrop: function(event, delta, revertFunc) {
                console.log( {'Event Drop': event } );
            },

            eventDragStart: function( event, jsEvent, ui, view ) {
                console.log('Event Drag Starts!');
            },

            eventDragStop: function( event, jsEvent, ui, view ) {
                console.log('Event Drag Dropped!');
            },

            dragOpacity:  0.3,
            dayRender: function( date, cell ) {
                // Get all events
                var events = $('#calendar').fullCalendar('clientEvents').length ? $('#calendar').fullCalendar('clientEvents') : defaultEvents;
                // Start of a day timestamp
                var dateTimestamp = date.hour(0).minutes(0);
                var recurringEvents = new Array();

                // find all events with monthly repeating flag, having id, repeating at that day few months ago
                var monthlyEvents = events.filter(function (event) {
                    return event.repeat === REPEAT_MONTHLY &&
                        event.id &&
                        moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'months', true) % 1 == 0
                });

                // find all events with monthly repeating flag, having id, repeating at that day few years ago
                var yearlyEvents = events.filter(function (event) {
                    return event.repeat === REPEAT_YEARLY &&
                        event.id &&
                        moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'years', true) % 1 == 0
                });

                recurringEvents = monthlyEvents.concat(yearlyEvents);

                $.each(recurringEvents, function(key, event) {
                    var timeStart = moment(event.start);

                    // Refething event fields for event rendering
                    var eventData = {
                        id: event.id,
                        allDay: event.allDay,
                        title: event.title,
                        description: event.description,
                        start: date.hour(timeStart.hour()).minutes(timeStart.minutes()).format("YYYY-MM-DD"),
                        //start: timeStart,
                        end: event.end ? event.end.format("YYYY-MM-DD") : "",
                        //end: event.end,
                        url: event.url,
                        className: event.className,
                        backgroundColor: event.backgroundColor,
                        textColor: event.textColor,
                        repeat: event.repeat,
                        editable: event.editable,
                        holiday: event.holiday,
                    };

                    // Removing events to avoid duplication
                    $('#calendar').fullCalendar( 'removeEvents', function (event) {
                        return eventData.id === event.id &&
                            moment(event.start).isSame(date, 'day');
                    });
                    // Render event
                    $('#calendar').fullCalendar('renderEvent', eventData, true);

                });

            },


            eventRender: function (event, element) {
                element.find('.fc-title')
                    .html('<span style="display: block; color: '+event.textColor+' !important; padding-right: 30px;">'+event.title+'</span>')
                    .append('<span style="font-size: 10px"> '+event.description+'</span></div>');



                if(event.className == 'pulsate') {
                    element.addClass('fc-holiday');
                    var pulsate_html = '';

                    pulsate_html += '<div class="holiday-pulse" style="width: auto; min-height: 50px; display: block; outline: 0px; text-align: left; box-shadow: rgba(57, 155, 195, 0) 0px 0px 13px; outline-offset: 20px;">';
                    if(event.holiday==true) {
                        pulsate_html += '<span class="" style="display: block !important; text-align: left; font-size: 0.8em">Holiday</span>';
                    }else {
                        if (event.allDay == true) {
                            pulsate_html += '<span class="" style="display: block !important; text-align: left; font-size: 0.8em">All Day</span>';
                        }else {
                            pulsate_html += '<span class="" style="display: block !important; text-align: left; font-size: 0.8em">&nbsp;</span>';
                        }
                    }
                    pulsate_html += '<span style="display: block; color: '+event.textColor+' !important; padding-right: 30px;">'+event.title+'</span>';
                    pulsate_html += '<span style="font-size: 10px"> '+event.description+'</span>';
                    pulsate_html += '</div>';

                    element.html(pulsate_html);
                }

                if(event.allDay == true) {
                    element.find('.fc-content').prepend('<span class="" style="display: block !important; text-align: left;">All Day</span>');
                }

                element.attr('href', 'javascript:void(0);');
                element.click(function() {
                    console.log(event);
                    $("#startTime").html(moment(event.start).format('MMM Do h:mm A'));
                    $("#endTime").html(moment(event.end).format('MMM Do h:mm A'));
                    $("#eventInfo").html(event.description);
                    if(event.url != "") {
                        $("#eventLink").attr('href', event.url);
                    }
                    $("#eventContent").dialog({
                        modal: false, // disabled dim background
                        title: event.title,
                        width:350,
                        show: {
                            effect: "fade",
                            duration: 200
                        },
                        hide: {
                            effect: "fade",
                            duration: 200
                        }
                    });
                    $('body').find('.ui-widget-header')
                        .css('background', event.backgroundColor)
                        .css('color', event.textColor);
                    $('body').find('.ui-dialog').css('border-color', event.backgroundColor);
                });

            },

        });

        $('.fc-button-group').on('click', 'button', function(){
            init_puslsate();
        });
        init_puslsate();
    };
    return {
        //main function to initiate the module
        init: function() {
            Calendar.initCalendar();
        },

        initCalendar: function() {
            init_calendar_table();
        }

    };

}();