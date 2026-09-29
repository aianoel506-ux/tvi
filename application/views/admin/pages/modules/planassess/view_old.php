<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
 
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->

        <div class="row">
            <div class="col-md-12">
                <h3 class="page-title">Inspection<small>beta</small></h3>
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-green-sharp">
                                <i class="icon-info font-green-sharp"></i>
                                <span class="caption-subject bold uppercase">Transaction Code: 
                                <?php 
                                    $dataid = $this->input->post('dataid');
                                    $trn = $this->input->post('trn'); 
                                    $status = $this->input->post('status'); 
                                    
                                    $data = $this->model_inspection_queries->edit_data($dataid);

                                    $address_id = $data->address_id;
                                    $account_type_val = $data->account_type_id;
                                    $inspection_date_val =  ($data->logdate == "0000-00-00") ? "2000-01-01" : $data->logdate;
                                    $district_val = $data->district_id;
                                    $city_val = $data->city_id;
                                    $specific_address_val = $data->addrspecific;
                                    $account_type_name = $data->account_type_name;
                                    $district_name = $data->district_name;
                                    $city_name = $data->city_name;
                                    $x = $data->addrmapx;
                                    $y = $data->addrmapy;
                                    $edited = 'edited';
                                    $rate_id = $data->rate_id;
                                    $rate_name = $data->rate_name;
                                    $page = basename(__FILE__, '.php');
                                    
                                    echo $trn." ".$dataid." ".$status." x=".$x." y=".$y;
                                    
                                    $form_action = base_url()."query/editSubmittedData";
                                ?>
                            </span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" action="<?php echo $form_action; ?>" method="post" name="form1">
                                <div class="row form-body">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input form-md-floating-label" name="inspection_date">
                                                    <div class = "form-control form-control-static">
                                                        <?php echo $inspection_date_val; ?>
                                                    </div>
                                                    <label for="inspection_date" class="control-label">
                                                        Inspection Date
                                                    </label>
                                                </div>
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                    <div class="form-control form-control-static" name="district">
                                                         <?php echo $district_name; ?>
                                                    </div>
                                                    <label for="district" class="control-label">District</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                    <div class="form-control form-control-static" name="account_type">
                                                         <?php echo $account_type_name; ?>
                                                    </div>
                                                    <label for="account_type" class="control-label">Account Type</label>
                                                </div>
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                    <div class="form-control form-control-static" name="city">
                                                         <?php echo $city_name; ?>
                                                    </div>
                                                    <label for="city" class="control-label">City</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label">
                                            <div class="form-control form-control-static" name="specific_address">
                                                <?php echo $specific_address_val; ?>
                                            </div>
                                            <label for="specific_address" class="control-label">
                                                Specific Address
                                            </label>
                                            <span class="help-block">Add more address details not specified above.</span>
                                        </div>
                                        <div class="portlet light bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="icon-map"></i>
                                                    <span class="caption-subject">Mapping</span>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group form-md-line-input has-info">
                                                            <input class="form-control" type="text" id="latitude" name="latitude" readonly value="<?php echo htmlspecialchars($x); ?>">
                                                            <label for="latitude">Latitude</label>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-md-line-input has-info">
                                                            <input class="form-control" type="text" id="longitude" name="longitude" readonly value="<?php echo htmlspecialchars($y); ?>">
                                                            <label for="longitude">Longitude</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="trn" name="trn" value="<?php echo htmlspecialchars($trn); ?>">
                                                <input type="hidden" id="dataid" name="dataid" value="<?php echo htmlspecialchars($dataid); ?>">
                                                <input type="hidden" id="status" name="status" value="<?php echo htmlspecialchars($status); ?>">
                                                <input type="hidden" id="address_id" name="address_id" value="<?php echo htmlspecialchars($address_id); ?>">
                                                <div id="gmap_geocoding" class="gmaps"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption  text-align-center">
                                                    <i class="caption-subject">
                                                        List of Equipments
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="portlet-body portlet-empty " style="height: 414px">
                                                <table class="table table-striped table-hover table-condensed" id="equipment_added">
                                                    <thead>
                                                        <tr>
                                                            <th>Type</th>
                                                            <th>Sysid</th>
                                                            <th>Power(Watts)</th>
                                                            <th>Quantity</th>
                                                            <th><i class="fa fa-wrench"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                     <input type="hidden" id="rate_class" name="rate_class" value="<?php echo htmlspecialchars($rate_id); ?>">
                                                    <label for="rate_class" class="control-label">Rate Class</label>
                                                    <div class="form-control form-control-static">
                                                         <?php echo $rate_name; ?>
                                                    </div>
                                                </div>
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                    <input id="daily_ops" name="daily_ops" class="form-control edited" type="text" value="0" readonly>
                                                    <label for="daily_ops" class="control-label">Daily Operations(hours/day)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                    <input id="rate" name="rate" class="form-control edited" type="text" value="0" readonly>
                                                    <label for="rate" class="control-label">Rate</label>
                                                </div>
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                    <input id="monthly_ops" name="monthly_ops" class="form-control edited" type="text" value="0" readonly>
                                                    <label for="monthly_ops" class="control-label">Monthly Operations(days/month)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group form-md-line-input form-md-floating-label">
                                                    <input id="deposit_cost" name="deposit_cost" class="form-control edited" type="text" value="0" readonly>
                                                    <label for="deposit_cost" class="control-label">Deposit Cost</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-lg inline col-md-offset-5 blue-steel fa fa-save submit" id="update_account_info">Edit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<!-- DATE PICKER!-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<!-- DATE PICKER END!-->
<!-- GOOGLE MAPS LIBS START !-->
<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script>
<!-- GOOGLE MAPS LIBS END !-->

<script type="text/javascript">
    $(document).ready(function() {
        if (<?php echo $status ?> === 2 || <?php echo $status ?> === 1){
            ajax_request(<?php echo $rate_id ?>);
        }
        set_marker(latitude, longitude, 'address on map', 'coordinate: ('+latitude+', '+longitude+')', 20);
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
    $("#inspection_date").datepicker();
    //MAPS SCRIPT START
    var latitude = $('[name="latitude"]').val();
    var longitude = $('[name="longitude"]').val();
    var map = new GMaps({
        div: '#gmap_geocoding',
        lat: latitude,
        lng: longitude,
    });
    function set_marker(x, y, title, text, zoom){
        map.setCenter(x, y);
        map.addMarker({
            lat: x,
            lng: y,
            title: title,
            infoWindow: {
                content: '<span style="color:#000">'+text+'</span>'
            }
        });
        PECO.scrollTo($('#gmap_geocoding'));
        map.setZoom(zoom);
    };
    //MAP CODE END
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
initEquipments();
function initEquipments(){
	$('#equipment_added').dataTable({
                "bFilter":false,
		"info": false,
		"destroy": true,
		"oLanguage": {
			sProcessing: "<img src='<?php echo base_url(); ?>assets/global/img/loading.gif' />"
		},
		"scrollY"			: "300px",
		"processing"		: true,
		"serverSide"		: true,
		"ajax": {
			"url" : "<?php echo base_url(); ?>query/init_acctequipment/<?php echo $dataid; ?>/<?php echo $page?>",
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
 </script>