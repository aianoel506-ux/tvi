<?php
/**
 * Created by PhpStorm.
 * User: IT
 * Date: 11/22/2018
 * Time: 9:37 AM
 */

$weekname = array(
    '1' => 'Monday',
    '2' => 'Tuesday',
    '3' => 'Wednesday',
    '4' => 'Thursday',
    '5' => 'Friday',
    '6' => 'Saturday',
    '7' => 'Sunday',
);
?>
<div class="row" style="margin:10px 10px 0px 10px">

    <div class="col-md-8">
        <form id="submitemployeesched"  action="<?php echo base_url() ?>hris/addempschedule" method="post">

            <input type="hidden" value="" id="fromdate" name="fromdate" />
            <input type="hidden" value="" id="todate" name="todate" />
            <input type="hidden" id="workshiftidhidden" name="workshiftidhidden" />
            <input type="hidden" id="branchidhidden" name="branchidhidden" />


                <div class="col-md-4">
                    <label>Month</label>
                    <input type="text" id="monthsched" name="monthsched" class="form-control" />
                </div>
                <div class="col-md-4">
                    <label>Type</label>
                    <input disabled type="text" name="typesched" id="typesched" class="form-control" />
                </div>
                <div class="col-md-4">
                    <label>Day</label>
                    <select class="form-control" name="dayofweek" id="daysched">
                        <?php
                        foreach ($weekname as $key => $value){
                            echo ' <option value="'.$key.'">'.$value.'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label>Branch</label>
                    <input type="text" id="branchsched" name="branchsched" class="form-control" />
                </div>

                <div id="employeefield" class="col-md-6 hidden">
                    <label>Employee</label>
                    <input type="text" id="employeesched" name="empid" class="form-control" />
                </div>

                <div class="col-md-3 pull-right">
                    <button type="submit" style="margin:20px 0px 20px 0px;" class="btn btn-default pull-right">Add <i class="fa fa-plus"></i></button>
                </div>
        </form>
    </div>

    <div class="col-md-4" style="margin: 10px 0px 0px 0px">
        <div class="panel panel-default">

            <div class="panel-body">
                <h3>Date: <span><?php echo date('Y/m/d') ?></span></h3>
                From: <span id="fromdatedisplay">2018-01-01</span>
                To: <span id="todatedisplay">2018-01-01</span>
            </div>

        </div>
    </div>
</div>

<script>
    PECO.select2Basic($("#typesched") , 'ts/gettypesched' , 'Select Type',false,false,false);
    PECO.select2Basic($("#branchsched") , 'ts/getcompanybranches' , 'Select Branch',false,false,false);
    PECO.select2Basic($("#monthsched") , 'systems/select2month' , 'Select Month',false,false,false);

    $(document).on('change','#monthsched',function () {
        var this_ = $(this).val();

        if(this_ != ''){
            $('#typesched').prop('disabled', false);
        }else{
            $('#typesched').prop('disabled', true);
        }
    });

    $(document).on('change','#typesched',function () {

        var this_ = $(this);
        var this_val = this_.val();
        if(this_val != ''){
            var month = $('#monthsched',document).val();

              $.ajax({
                  url:PECO.base_url()+'ts/getdatesched',
                  type:'post',
                  data:{"type":this_val , "month" :  month},
                  dataType:'json'
              }).done(function (data) {
                  $('#fromdate').val(data.fromdate);
                  $('#todate').val(data.todate);
                  $('#fromdatedisplay').text(data.fromdate);
                  $('#todatedisplay').text(data.todate);
              }).fail(function () {
                  PECO.phpError();
              });


            $('#employeefield').removeClass('hidden');
            $('#teamfield').removeClass('hidden');
            PECO.select2Basic($('#employeesched') , 'hris/getallsbtsemployee','Select Employee' , false, false,false,false,false,<?php echo user_id(); ?>);
            PECO.select2Basic($('#teamsched') , 'hris/getallsbtsteam','Select Team' , false, false,false,false,false , this_val);
        }
    });
    $(document).on('change','#branchsched',function () {
        var this_ = $(this);
        var this_val = this_.val();
        if(this_val != ''){
            $.ajax({
                url:PECO.base_url()+'ts/getworkshiftid',
                type:'post',
                data:{"branchid":this_val },
                dataType:'json'
            }).done(function (data) {
                $('#workshiftidhidden').val(data.workshiftid);
                $('#branchidhidden').val(data.branchid);
            }).fail(function () {
                PECO.phpError();
            });
        }else{
            $('#workshiftidhidden').val('');
        }
    });

    $(document).on('submit','#submitemployeesched',function (e) {
        e.preventDefault();
        var this_  = $(this);
        $.ajax({
            url:this_.attr("action"),
            type:this_.attr("method"),
            data:this_.serialize(),
            dataType:'json'
        }).done(function (d) {
            PECO.initAlerts(d.msg , "PECO.net" , d.func);
            $('#sbtsemployee').select2('val','');
            $('#employeesched').select2('val','');
            $('#teamsched').select2('val','');
            fetchdefaultschedofthemonth();
        }).fail(function () {
            PECO.phpError();
        });
        e.stopImmediatePropagation();
    });

    var fetchdefaultschedofthemonth = function(month  , types){


        var d = new Date();
        var monthdata = (month > 0)? month : d.getMonth() + 1;
        var typedata = (types > 0 )? types : $('#typehalfshift',document).val();


        $.ajax({
            url:PECO.base_url()+'ts/getdatasched',
            type:"post",
            data:{"typedata":typedata,"month":monthdata},
            dataType:'json'
        }).done(function (d) {

            $('#tabledata',document).html(d.tabledata);
          /*  $('#schedulingbuttons',document).html(d.schedbtn);
            $('#shifttablesched',document).DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                searchHighlight: true,
                scrollX: true,
                scrollY: '300px',
                scrollCollapse: true,
                paging:         false,
                fixedColumns: {
                    leftColumns: 2
                },
                drawCallback: function() {
                    setTimeout(function(){
                        PECO.dataTableScroller();
                    },100);
                }
            }); */
        }).fail(function () {
            PECO.phpError();
        });

    };


    $('#daysched').select2({
        "allowClear":true
    });
</script>