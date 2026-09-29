var latitude=0;
var longitude=0;
$(document).ready(function() {
    if (<?php echo $status ?> === 2 || <?php echo $status ?> === 1){
        ajax_request(<?php echo $rate_id ?>);
    }
});
function round_2dec(num){
    num = Math.round(num * 100) / 100;
    return num;
};
function ajax_request (rate_class){
    $.ajax({
            type: "POST",
            url: "<?php echo base_url();?>query/rateClassConstants",
            dataType: "json",
            data: {rate:rate_class},
            success: function(result){
            var dops = result['dailyops'];
            var mops = result['monthlyops'];
            var demand = result['demand'];
            var rate = result['rates'];
            /*DEBUGGER
            alert(mops+" "+dops+" "+rate);
            console.log(json);
            */
            $('[name="daily_ops"]').val(dops);
            $('[name="monthly_ops"]').val(mops);
            $('[name="rate"]').val(rate);

            if ($("[name='equipment_wattage[]']").length ) { 
                var total_load=0;
                var deposit_cost=0; 
                var power = [];
                var qty = [];

                var table_size = $('#equipment_added tbody tr').length;

                if (table_size === 1){
                    total_load += document.getElementsByName("equipment_wattage[]")[0].value * document.getElementsByName("equipment_qty[]")[0].value;
                    deposit_cost = (total_load * dops * mops * rate * demand)/1000;
                    $('[name="deposit_cost"]').val(round_2dec(deposit_cost));
                }else{
                    var power = document.form1.elements["equipment_wattage[]"]; 
                    var qty = document.form1.elements["equipment_qty[]"]; 
                    for(i=0;i<power.length;i++){    
                        total_load += power[i].value * qty[i].value;
                    }
                    deposit_cost = (total_load * dops * mops * rate * demand)/1000;
                    $('[name="deposit_cost"]').val(round_2dec(deposit_cost));
                }
            }else{
                $('[name="deposit_cost"]').val(0);
            }

        }
    });
};
$('#rate_class').change(function(e) {
    var rate_class = $( "#rate_class" ).val();

    if(rate_class !== '0'){
        ajax_request(rate_class);
    }else{
        $('[name="daily_ops"]').val(rate_class);
        $('[name="monthly_ops"]').val(rate_class);
        $('[name="rate"]').val(rate_class);
        $('[name="deposit_cost"]').val(rate_class);
    }
});
$(document).on("keyup", "[name='equipment_wattage[]'], [name='equipment_qty[]']", function() {
    var rate_class = $( "#rate_class" ).val();
    if(rate_class !== '0'){
        ajax_request(rate_class);
    }
});
$("#inspection_date").datepicker();


var capture_map_data = function () {
    document.getElementById("latitude").value = latitude;
    document.getElementById("longitude").value = longitude;
};
//MAPS SCRIPT START
var map = new GMaps({
    div: '#gmap_geocoding',
    lat: 10.702530,
    lng: 122.564495
});

var handleAction = function () {
    var text = $.trim($('#gmap_geocoding_address').val());
    GMaps.geocode({
        address: text,
        callback: function (results, status) {
            if (status == 'OK') {
                var latlng = results[0].geometry.location;
                latitude = latlng.lat();
                longitude = latlng.lng();
                map.setCenter(latitude, longitude);
                map.addMarker({
                    lat: latlng.lat(),
                    lng: latlng.lng()
                });
                PECO.scrollTo($('#gmap_geocoding'));
            }
        }
    });
};

$('#gmap_geocoding_btn').click(function (e) {
    e.preventDefault();
    handleAction();
});

$("#gmap_geocoding_address").keypress(function (e) {
    var keycode = (e.keyCode ? e.keyCode : e.which);
    if (keycode == '13') {
        e.preventDefault();
        handleAction();
    }
});
$('#form_add_eq').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize(),
        dataType: "json",
        success: function(equipments){
            //console.log(equipments['message']);//debug
            initEquipments();
        }
    });
    initEquipments();
    var rate_class = $( "#rate_class" ).val();
    if(rate_class !== '0'){
       ajax_request(rate_class);
    }
});
/*$("#equipment_added").on('click', 'span', function(){
    $.btnid = $(this).closest('tr').find('td').eq(3).find('span');
    if ($.btnid.attr('id') === 'delete_existing'){
        $.sysid = $(this).closest('tr').find('td').eq(0).find('input');
        var sysid = $.sysid.val();
        $("#equipment_added tbody").append("<input type='hidden' name='delete_equipment_id[]' value='"+sysid+"'>");
    }
    $(this).closest('tr').fadeOut('fast', function() {

        $(this).remove(); 
        var rate_class = $( "#rate_class" ).val();
        if(rate_class !== '0'){
           ajax_request(rate_class);
        }
    });

});*/
$("#equipment_added").on('click', 'span', function(){
    //$.btnid = $(this).closest('tr').find('td').eq(4).find('span');

    $.sysid = $(this).closest('tr').find('td').eq(1).find('input');
    var sysid = $.sysid.val();
    //console.log(sysid);//debug
    var page = '';
    if (<?php echo $status ?> === 1){
        page = 'changeEquipmentStatus';
    }else{
        page = 'deleteEquipment';
    }
    $.ajax({
        type: "POST",
        url: "<?php echo base_url();?>query/"+page,
        data: {sysid: sysid},
        dataType: "json",
        success: function(equipments){
            console.log(equipments['result']);//debug
            initEquipments();
        }
    });
    $(this).closest('tr').fadeOut('fast', function() {

        $(this).remove(); 
        var rate_class = $( "#rate_class" ).val();
        if(rate_class !== '0'){
           ajax_request(rate_class);
        }
    });

});
$('#capture').click(function (e) {
    e.preventDefault();
    capture_map_data();
});

/*$("#update_account_info").click(function (e){
    return false;
});*/


initEquipments();
function initEquipments(){
       $('#equipment_added').dataTable({
               "info": false,
               "destroy": true,
               "oLanguage": {
                       sProcessing: "<img src='<?php echo base_url(); ?>assets/global/img/loading.gif' />"
               },
               "scrollY"			: "300px",
               "processing"		: true,
               "serverSide"		: true,
               "ajax": {
                       "url" : "<?php echo base_url(); ?>query/init_acctequipment/<?php echo $dataid; ?>",
                       "type" : "POST"
               },
               "language": {
                       "emptyTable":     "My Custom Message On Empty Table"
               },
               "columnDefs": [
                       { "targets":  1, "sClass": 'hidden'},
                       { "targets": -1, "orderable": false, "searchable": false },
       ]
       });
       dt_scroller();
}
function dt_scroller() {
       $('.dataTables_scrollBody').niceScroll({
               styler:"fb",
               cursorcolor:"rgba(215, 98, 44, 0.6)", 
               cursorwidth: '6', 
               cursorborderradius: '0px', 
               background: 'transparent', 
               cursorborder: ''
       });
}	
