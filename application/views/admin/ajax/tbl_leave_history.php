<?php
/**
 * Created by PhpStorm.
 * User: peco_
 * Date: 3/22/2019
 * Time: 8:37 AM
 */

$view = $this->input->post('view');
$dataarr = $this->input->post('ids');
$data = explode("-", $dataarr);

?>

<div class="row" style="margin: 5px 5px;">
    <input type="hidden" id="hiddenview" value="<?php echo $view; ?>" />
    <input type="hidden" id="hiddenyear" value="<?php echo $data[0]; ?>" />
    <input type="hidden" id="hiddentype" value="<?php echo $data[1]; ?>" />
    <div class="col-md-3">
        <label>Leave Type</label>
        <input type="text" name="leavetype" id="reportleavetype" class="form-control" />
    </div>
    <div class="col-md-3 pull-right">
        <button class="btn btn-primary" id="empleavehistrep" style="margin-top: 24px;right: 15px;position: absolute;">Report</button>
    </div>
</div>
<br>
<div class="row" style="margin: 5px 5px;">
    <div class="col-md-12">
        <div id="empleavehistory"></div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        loadempleavehistory($('#hiddenview' , document).val() ,  $('#hiddenyear' , document).val());
    });

    function loadempleavehistory(empid , year , report , leavetype){
        $.ajax({
            url:PECO.base_url()+'hris/getempleavehistory',
            type:'post',
            data:{"empid" : empid , "year" : year , "report" : report , "leavetype" : leavetype},
            dataType:'json'
        }).done(function (data) {
            if(data.report > 0){
                printleavedetails("Leave Report" , data.html , false);
            }else{
                $(document).find('#empleavehistory').html(data.html);
                $(document).find('#empleavehistorytbl').dataTable();
            }
        }).fail(function () {
            PECO.phpError();
        });
    };

    var printleavedetails = function(reptitle, content, header = true){
        // Open a new window for the printable table
        var win = window.open('', '');
        var head = '<title>' + reptitle + '</title>';

        var header_html = '';
        if(header == true) {
            header_html = '<img  style="display: inline-block; height: 80px; float: left; z-index: 2 !important; position: absolute; left: 0px;" src="' + PECO.base_url() + 'assets/global/img/PECO_LEFT_HEAD.png" /><img style="display: inline-block; height: 80px; width: 100%; position: absolute; top 0px; right: 0px; z-index: 0;" src="' + PECO.base_url() + 'assets/global/img/PECO_REP_HEAD.png" />';
        }
        win.document.title = reptitle;
        win.document.body.innerHTML =
            '<head>' +
            '<title>'+reptitle+'</title>'+
            '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
            '<style>body{margin: 0px 0px !important; font-family: arial; background: #fff;}</style>' +
            '</head>' +
            header_html +
            '<div style="position: absolute; left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
            //  win.print(); // blocking - so close will not
            //  win.close(); // execute until this is done
        }, 250);
    };


    PECO.select2Basic($('#reportleavetype' , document) , 'request/getleavetype' , 'Leave Type' , false,false,false,false,false,<?php echo $view; ?>);

    $(document).on('change' , '#reportleavetype' , function (e) {
        var this_ = $(this);
        if(this_.val() != ''){
            loadempleavehistory($('#hiddenview' , document).val(),  $('#hiddenyear' , document).val()  , 0  , this_.val());
        }else{
            loadempleavehistory($('#hiddenview' , document).val(), $('#hiddenyear' , document).val() );
        }
        e.stopImmediatePropagation();
    });
    $(document).on('click' , '#empleavehistrep' , function (e) {
        var leavetype = $(document).find('#reportleavetype').val();
        if(leavetype == ''){
            PECO.initAlerts("Select leave type" , "PECO" , "info");
        }else{
            loadempleavehistory($('#hiddenview' , document).val() , $('#hiddenyear' , document).val() ,1 ,leavetype);

        }
        e.stopImmediatePropagation();
    });
</script>