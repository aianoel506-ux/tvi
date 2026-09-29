<link href="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet"/>
<link href="<?php echo base_url(); ?>assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<style>

    .fc-widget-header {
        font-weight:bold !important;
        background: #fff !important;
        background: -webkit-linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* For Safari 5.1 to 6.0 */
        background: -o-linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* For Opera 11.1 to 12.0 */
        background: -moz-linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* For Firefox 3.6 to 15 */
        background: linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* Standard syntax */
    }
    .fc-widget-header span {
        padding: 4px 5px !important;
        display: inline-block;
    }

    .fc-day-top {
        background: rgba(75, 175, 224, 0.2) !important;
    }


    .fc-day-top.fc-other-month {
        background: transparent !important;
    }

    .fc-day.fc-today {
        -moz-box-shadow: inset 0 0 20px rgba(255,42,0,0.10);
        -webkit-box-shadow: inset 0 0 20px rgba(255,42,0,0.10);
        background: rgba(255, 243, 86, 0.30) !important;
        border: 1px solid #FFF356;
    }

    .fc-day-top.fc-today {
        background: rgba(255, 243, 86, 0.30) !important;
        border: 1px solid #FFF356;
    }

    .fc-day {
        background: -webkit-linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* For Safari 5.1 to 6.0 */
        background: -o-linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* For Opera 11.1 to 12.0 */
        background: -moz-linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* For Firefox 3.6 to 15 */
        background: linear-gradient(rgba(75, 175, 224, 0.05), #fff); /* Standard syntax */
    }
    .fc-day:hover,
    .fc-sat:hover,
    .fc-sun:hover
    {
        background: rgba(75, 175, 224, 0.10);
        box-shadow: rgba(79, 181, 247, 0.20) 0px 0px 10px 2px !important;
        cursor: pointer !important;
    }
    .fc-sun .fc-day-number, .fc-day-header.fc-sun span {
        color: red;
    }
    .fc-sat .fc-day-number {
        color: #0a6aa1;
    }
    /*.fc-sat,
    .fc-sun,
    */
    .fc-other-month {
        background: rgba(200,200,200,0.20);
    }
    .fc-other-month:hover {
        background: rgba(200,200,200,0.20);
        box-shadow: transparent 0px 0px 0px 0px !important;
        cursor: default !important;
    }
    .fc-title {
        color: inherit !important;
    }

    .fc-day-number {
        padding: 5px 5px;
        color: #000;
        font-weight: bold;
        float: right;
        margin: 2px 2px 0 0;
        width: 20px;
        text-align: center;
    }
    .fc-day:hover .fc-day-number {
        color: rgba(7, 130, 208, 0.70) !important;
    }
    .fc-event-container .fc-day-grid-event {
        padding: 2px 5px;
    }
    .fc-event-container .fc-day-grid-event .fc-time {
        margin-right: 5px;
    }
    .fc-event-container .fc-day-grid-event.black.bordered {
        border-width: 1px !important;
        border-style: solid !important;
        border-color: #000 !important;
    }
    .fc-event-container .fc-day-grid-event.black *{
        color: #000 !important;
    }
    .fc-event-container .fc-day-grid-event.white.bordered {
        border-width: 1px !important;
        border-style: solid !important;
        border-color: #fff !important;
    }
    .fc-event-container .fc-day-grid-event.white * {
        color: #fff !important;
    }
    .fc-content-skeleton table tbody tr td {
        padding-top: 5px !important;
        padding-left: 5px !important;
        padding-right: 5px; !important;
    }
    .fc-content-skeleton table tbody tr td:last-child {
        padding-bottom: 5px !important;
    }


    .ui-widget {
        font-family: Verdana,Arial,sans-serif;
    }

    .ui-widget-content {
        background: #F9F9F9;
        border: 1px solid #90d93f;
        color: #222222;
    }

    .ui-dialog {
        left: 0;
        outline: 0 none;
        padding: 0 !important;
        position: absolute;
        top: 0;
    }

    .ui-dialog .ui-dialog-content {
        background: none repeat scroll 0 0 transparent;
        border: 0 none;
        overflow: auto;
        position: relative;
    }

    .ui-widget-header {
        border: 0;
        color: #fff;
        font-weight: normal;
    }

    .ui-dialog .ui-dialog-titlebar {
        position: relative;
        font-size: 1.2em;
        font-weight: bold;
    }

    .ui-dialog {
        box-shadow: rgba(200, 181, 247, 0.60) 0px 3px 10px 2px !important;
    }

    #eventInfo, #eventLink {
        font-size: 0.8em;
    }

</style>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="portlet light calendar">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-calendar"></i>Calendar
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="calendar" class="has-toolbar">
                        </div>
                    </div>
                </div>
            </div>

        </div>

<div id="eventContent" title="Event Details" style="display:none;">
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
    <p id="eventInfo"></p>
    <p><strong><a id="eventLink" href="" target="_blank">Read More</a></strong></p>
</div>

<div class="modal fade" id="event_modal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Unknown</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- END PAGE CONTENT-->
<!-- IMPORTANT! fullcalendar depends on jquery-ui.min.js for drag & drop support -->
<!--
<script src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/fullcalendar.min.js"></script>
-->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.pulsate.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.pulsate.min.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/moment-with-locales.min.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/fullcalendar.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url(); ?>assets/pages/calendar.js"></script>
<script type="text/javascript">
    Calendar.init();
</script>