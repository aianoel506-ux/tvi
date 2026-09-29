<div class="page-content-wrapper cad-new-account ">
    <div class="page-content  animated fadeInUp fast" id="form_wizard_1">
        <div class="row">
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase">Agenda</span>
                            <span class="caption-helper"></span>
                        </div> 
                        <div class="tools">
                        <a href="#portlet-config" data-toggle="modal" class="" data-original-title="" title=""><i class="fa fa-plus"></i>
                        </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form class="form-horizontal" action="<?php echo base_url('user/drawmycalendar'); ?>" method="post" id="frm_calendar_input">
                            <div class="form-group">
                                <label class="col-md-3">Year:</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="year" value="<?php echo date('Y'); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3">Month:</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="month" id="select_month">
                                        <option></option>
                                        <?php
                                            $cur_m = date('m');
                                            for($m=1; $m<=12; $m++) {
                                                $dateObj   = DateTime::createFromFormat('!m', $m);
                                                $monthName = $dateObj->format('F'); // March
                                                if($cur_m==$m) {
                                                    $opt_select = 'selected';
                                                }else{
                                                    $opt_select = '';
                                                }
                                                echo '<option '.$opt_select.' value="'.$m.'">'.$monthName.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                         
                        </form> 
                        <hr>
                        <h4>Today's Agenda</h4>
                            <ul class="list-group summary">
                                <li class="list-group-item"> Lucky John's Birthday <span class="label label-default pull-right" id="daterange"><i class="fa fa-gift"></i></span> </li>
                                <li class="list-group-item"> Final Presentation (3:00 PM) <span class="label label-default pull-right" id="totalut"><i class="fa fa-warning"></i></span> <br>PECO</li>
                                <li class="list-group-item"> Holiday Tomorrow<span class="label label-default pull-right" id="totallate"><i class="fa fa-search"></i></span> </li>
                            </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="portlet table light">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-sharp bold uppercase" id="dis_month"><?php echo date('F'); ?></span>
                            <span class="caption-helper" id="dis_year"><?php echo date('Y'); ?></span>
                        </div> 
                    </div>
                    <div class="portlet-body" id="calendar_container">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    init_draw_calendar($('#frm_calendar_input'));
    $('#select_month').change(function(e){
        e.preventDefault();
        init_draw_calendar($('#frm_calendar_input'));
    });
    
    function init_draw_calendar(form) {
        $.ajax({
           url: form.attr('action'),
           type: form.attr('method'),
           data: form.serialize(),
           dataType: 'json',
        }).done(function(data){
            $('#dis_year').html(data.year);
            $('#dis_month').html(data.month);
            $('#calendar_container').html(data.calendar);
        }).fail(function(){
           PECO.phpError(); 
        });
        
    }
</script>