
<link href="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css"/>

<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">


        <h3 class="page-title">
            Maintenance <small>system related maintenance</small>
        </h3>
        <div class="page-bar">
            <?php echo create_breadcrumb(); ?>
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                        Actions <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="#">Action</a>
                        </li>
                        <li>
                            <a href="#">Another action</a>
                        </li>
                        <li>
                            <a href="#">Something else here</a>
                        </li>
                        <li class="divider">
                        </li>
                        <li>
                            <a href="#">Separated link</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->

        <div class="row">
            <div class="col-md-4">
                <div class="portlet blue box">
                    <div class="portlet-title">
                        <div class="caption">
                            Father -> MySQL Update
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="father_exec_res">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item"> Father Cnt: <span class="label label-default pull-right" id="">N/A</span> </li>
                                <li class="list-group-item"> Existing Act: <span class="label label-default pull-right" id="">N/A</span> </li>
                                <li class="list-group-item"> Not Existing: <span class="label label-default pull-right" id="">N/A</span> </li>
                                <li class="list-group-item"> Error Msg: <span class="label label-default pull-right" id="">N/A</span> </li>
                            </ul>
                        </div>
                        <hr>
                        <div class="input-group">
                            <input class="form-control" id="f_input_d" style="display: inline-block; width: 30%;" placeholder="D"/>
                            <input class="form-control" id="f_input_l" style="display: inline-block; width: 30%; margin-left: 10px;" placeholder="L"/>
                            <input class="form-control" id="f_input_b" style="display: inline-block; width: 30%; margin-left: 10px;" placeholder="B"/>
                            <span class="input-group-btn">
                                <input id="btn_exec_father" onclick="" value="Execute" class="btn btn-primary" type="button">
                            </span>
                        </div>
                        <div class="form-group">
                            <hr>
                            <div id="father_exec_qry_timer" class="" style="font-size: 22px;"><i class="fa fa-warning text-warning"></i> Leave D/L/B if want to query all!</div>
                        </div>
                    </div>
                </div>

                <div class="portlet blue box">
                    <div class="portlet-title">
                        <div class="caption">
                            CT Account -> MySQL Update
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="city_exec_res">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item"> Father Cnt: <span class="label label-default pull-right" id="">N/A</span> </li>
                                <li class="list-group-item"> Existing Act: <span class="label label-default pull-right" id="">N/A</span> </li>
                                <li class="list-group-item"> Not Existing: <span class="label label-default pull-right" id="">N/A</span> </li>
                                <li class="list-group-item"> Error Msg: <span class="label label-default pull-right" id="">N/A</span> </li>
                            </ul>
                        </div>
                        <hr>
                        <div class="btn-group">
                            <input id="btn_exec_ct" onclick="" value="Execute" class="btn btn-primary" type="button">
                        </div>
                        <div id="ct_exec_qry_timer" class="pull-right" style="font-size: 22px;"><i class="fa fa-warning text-warning"></i></div>
                    </div>
                </div>


        </div>


        <div class="col-md-4">

            <div class="portlet yellow box">
                <div class="portlet-title">
                    <div class="caption">
                        Interest -> MySQL Update
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="interest_exec_res">
                        Interest Update Query
                    </div>
                    <hr>
                    <div class="btn-group">
                        <input id="btn_exec_interest" onclick="" value="Execute" class="btn btn-primary" type="button">
                    </div>
                    <div id="interest_exec_qry_timer" class="pull-right" style="font-size: 22px;"><i class="fa fa-warning text-warning"></i></div>
                </div>
            </div>

            <div class="portlet green box">
                <div class="portlet-title">
                    <div class="caption">
                        Payments Migrations
                    </div>
                </div>
                <div class="portlet-body">
                    <form id="frm_payments_migration" action="<?php echo base_url('ar/getcustomerpayfromoldmonthly'); ?>" method="post">
                        <div class="form-group row">
                            <div class="col-md-6">
                                Year
                                <input class="form-control" name="year" placeholder="Year" required />
                            </div>
                            <div class="col-md-6">
                                Month
                                <input class="form-control" name="month" placeholder="Month" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    Records<br>
                                    <em class="font-green-haze" id="pay_num"></em>
                                </div>
                                <div class="col-md-5">
                                    Inserted<br>
                                    <em class="font-red-haze" id="pay_inserted"></em>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-danger pull-right" type="submit" title="Execute">Execute</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">

            <div class="portlet red box">
                <div class="portlet-title">
                    <div class="caption">
                        BillTRN Export / Import
                    </div>

                </div>
                <div class="portlet-body">
                    <form id="frm_billtrn_pump" class="" action="<?php echo base_url('billing/billtrnquery');?>" method="post">
                        <?php
                        $qry_bill_current = $this->db->select('month, year')
                            ->from('billing_reports')
                            ->group_by('month, year')
                            ->order_by('year', 'desc')
                            ->order_by('month', 'desc')
                            ->get()->row();

                        $current_billtrn = ($qry_bill_current) ? $qry_bill_current->year . ' / ' . $qry_bill_current->month : 'None';
                        ?>
                        <h4>Current: <span class="pull-right text-bold text-danger billtrn-current"><?php echo $current_billtrn; ?></span></h4>
                        <hr>
                        <div class="form-group row">
                            <div class=" col-md-6">
                                <input class="form-control" placeholder="Year" name="year">
                            </div>
                            <div class=" col-md-6">
                                <input class="form-control" placeholder="Month" name="month">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type">
                                <option value="1">Basic</option>
                                <option value="2">Review (Error)</option>
                            </select>
                        </div>
                        <hr>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Execute</button>
                            <button type="reset" class="btn btn-default">clear</button>
                        </div>
                        <span class="qry_stat pull-right"></span>
                    </form>
                </div>
            </div>


            <div class="portlet blue box">
                <div class="portlet-title">
                    <div class="caption">
                        Bill TRN => Bill Main
                    </div>
                </div>
                <div class="portlet-body">
                    <form id="frm_billtrn_to_billmain" action="<?php echo base_url('query/copybilltrntobillmain'); ?>" method="post">
                        <div class="form-group row">
                            <div class=" col-md-6">
                                <input required class="form-control" placeholder="Year" name="year">
                            </div>
                            <div class=" col-md-6">
                                <input class="form-control" placeholder="Month" name="month">
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-default" type="submit">Query</button>
                        </div>
                        <div id="stat_billtrn_to_billmain" class="pull-right" style="font-size: 22px;"><i class="fa fa-warning text-warning"></i></div>

                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- END PAGE CONTENT-->
</div>

</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-timer/jquery.timer.js"></script>

<script>
    PECO.getHighlightsPlugin();
    $('#frm_billtrn_pump').submit(function(e){
        var form = $(this);
        e.preventDefault();

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('.qry_stat').html('Processing.. <i class="fa fa-spinner fa-spin"></i>');
            }
        }).done(function(d){
            console.log(d);
            $('.billtrn-current').html(d.curr);
            $('.qry_stat').html(d.msg);
        }).fail(function(){
            $('#qry_stat').html('PHP Error!');
        });
    });
    $('#frm_payments_migration').submit(function(e){

        e.preventDefault();
        var form = $(this);
        var btn = $('.btn', form);
        var btn_txt = btn.html();
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                btn.attr('disabled', true).addClass('disabled');
                btn.html('Processing.. <i class="fa fa-spinner fa-spin"></i>');
            }
        }).done(function(d){
            btn.html(btn_txt);
            $('#pay_num', form).html(d.num);
            $('#pay_inserted', form).html(d.ins);
            btn.attr('disabled', false).removeClass('disabled');
        }).fail(function(){
            btn.html('PHP Error!');
            setTimeout(function() {
                btn.html(btn_txt);
                btn.attr('disabled', false).removeClass('disabled');
            }, 2000);
        });
    });

    var count = 0;
    var timer = $.timer(function() {
        $('#father_exec_qry_timer').html(msToHMS(++count));
    });

    var timer_test = $.timer(function() {
        $('#timer_counter').html(msToHMS(++count));
    });
    $('#timer_start_btn').click(function(e) {
        timer_test.set({ time : 70, autostart : true });
    });

    var count_int = 0;
    var timer_int = $.timer(function() {
        $('#interest_exec_qry_timer').html(msToHMS(++count));
    });
    $('#btn_exec_interest').click(function(){
        timer_int.set({ time : 70, autostart : true });
        $.ajax({
            url: PECO.base_url() + 'billing/interestquery',
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                $('#interest_exec_res').html('<i class="fa fa-spinner fa-pulse fa-spin"></i> query in progress..');
            }
        }).done(function(d){
            timer_int.pause();
            $('#interest_exec_qry_timer').addClass('text-success');
            $('#interest_exec_res').html(d.msg);
        });
    });

    $(document).on('submit', '#frm_billtrn_to_billmain', function(e){
        e.preventDefault();

        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $('#stat_billtrn_to_billmain', document).removeClass('text-success').addClass('text-info').html('<i class="fa fa-spinner fa-pulse fa-spin"></i>');
            }
        }).done(function(d){
            $('#stat_billtrn_to_billmain', document).removeClass('text-info').addClass('text-success');
            $('#stat_billtrn_to_billmain', document).html(d.msg);
        });
    });

    $('#btn_exec_father').click(function(){
        timer.set({ time : 70, autostart : true });
        var d = $('#f_input_d', document).val();
        var l = $('#f_input_l', document).val();
        var b = $('#f_input_b', document).val();

        $.ajax({
            url: PECO.base_url() + 'billing/fathertrnquery',
            type: 'post',
            data: {
                d: d,
                l: l,
                b: b
            },
            dataType: 'json',
            beforeSend: function() {

            }
        }).done(function(d){
            timer.pause();
            $('#father_exec_qry_timer').addClass('text-success');
            $('#father_exec_res').html(d.msg);
        });
    });


    /*
    var Example1 = new (function() {

        // Stopwatch element on the page
        var $stopwatch;

        // Timer speed in milliseconds
        var incrementTime = 70;

        // Current timer position in milliseconds
        var currentTime = 0;

        // Start the timer
        $(function() {
            $stopwatch = $('#id_father_exec_qry');
            Example1.Timer = $.timer(updateTimer, incrementTime, true);
        });
        // Output time and increment
        function updateTimer() {
            var timeString = formatTime(currentTime);
            $stopwatch.html(timeString);
            currentTime += incrementTime;
        }
        // Reset timer
        this.resetStopwatch = function() {
            currentTime = 0;
            Example1.Timer.stop().once();
        };
    });
    */


    // Common functions
    function pad(number, length) {
        var str = '' + number;
        while (str.length < length) {str = '0' + str;}
        return str;
    }
    function formatTime(time) {
        time = time / 10;
        var min = parseInt(time / 6000),
            sec = parseInt(time / 60) - (min * 60),
            hundredths = pad(time - (sec * 60) - (min * 6000), 2);
        return (min > 0 ? pad(min, 2) : "00") + ":" + pad(sec, 2) + ":" + hundredths;
    }

    function msToHMS( ms ) {
        // 1- Convert to seconds:
        var seconds = ms / 10;
        // 2- Extract hours:
        var hours = parseInt( seconds / 3600 ); // 3,600 seconds in 1 hour
        seconds = seconds % 3600; // seconds remaining after extracting hours
        // 3- Extract minutes:
        var minutes = parseInt( seconds / 60 ); // 60 seconds in 1 minute
        // 4- Keep only seconds not extracted to minutes:
        seconds = (seconds % 60).toFixed(1);
        return pad(hours, 2)+":"+pad(minutes, 2)+":"+pad(seconds, 2);
    }



</script>