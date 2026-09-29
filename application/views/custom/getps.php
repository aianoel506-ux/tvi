<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 5/8/2019
 * Time: 5:01 PM
 */
$sysmode = $this->db->sysmode;
if($sysmode=='dev') {
    $sysmode_msg = '<a href="javascript:;" class="font-red-thunderbird tooltips" title="System is in Development Database"><i class="fa fa-gears"></i> Development DB</a>';
}else{
    $sysmode_msg = '<a href="javascript:;" class="font-green-sharp tooltips" title="System is in live Database"><i class="fa fa-wifi"></i> Live DB</a>';
}

?>

<div class="container" style="margin-top: 20px;">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">

                <span class="caption-subject font-green-sharp bold uppercase">Custom <span class="pull-right"></span>
            </div>
        </div>
        <div class="portlet-body">

            <span class="form-control" id="sentid" ></span>
            <h3 id="qry_stat"><i class="fa fa-circle-o-notch fa-spin"></i> Loading assets...<span class="pull-right"><?php echo $sysmode_msg; ?></span></h3>
        </div>
    </div>
</div>


<script>
    var CUST = function() {

        var cust_init = function() {
            qry_ps();
            setInterval(function() {
                qry_ps();
            }, 10000);
        };

        var qry_ps = function() {

            var sentid_val = $('#sentid', document).text();
            var sentid_input = $('#sentid', document);
            $.ajax({
                url: PECO.base_url() + 'admin/qryps',
                data: {'sentval': sentid_val},
                type: 'post',
                dataType: 'json',
            }).done(function(d) {
                if(d.qry == true) {
                    if (sentid_val != '') {
                        sentid_input.append(', ' + d.new_ids);
                    } else {
                        sentid_input.text(d.new_ids);
                    }
                }
            });
        };

        return {
            init: function() {
                cust_init();
            }
        }
    }();

    CUST.init();
</script>
