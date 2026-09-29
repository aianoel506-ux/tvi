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
</span>
</div>
</div>
<div class="portlet-body form">

<div class="row form-body">
<div class="col-md-6">
<div class="row">
<div class="col-md-6">

<div class="portlet-body">
<!-- Customer Information -->
<legend>Customer Information:</legend>
<h4>Name: <br><strong><?php echo $this->model_query->get_owner_info($dataid)->FIRSTNAME;?> <?php echo $this->model_query->get_owner_info($dataid)->MIDDLENAME;?> <?php echo $this->model_query->get_owner_info($dataid)->LASTNAME;?>
</strong>
</h4>
<h4>Gender:<br>								 <strong><?php
echo $this->model_query->get_owner_info($dataid)->GENDER;?></strong></h4>
<h4>Address: <br><strong>
<?php
echo $this->model_query->get_owner_info($dataid)->STREET.', ';
echo $this->model_query->get_owner_info($dataid)->DIST.', ';
echo $this->model_query->get_owner_info($dataid)->CITY;

?>
</strong> </h4>
<!--for Account Details -->

<hr>

<legend>Account Details:</legend>
<h4>Acccount Rate:<br><strong>
<?php
echo $this->model_query->get_owner_info($dataid)->rate;
?>	</strong>
</h4>
<h4>Status of Connection:<br><strong>
<?php
echo $this->model_query->get_account_owner_type($dataid)->o_names;
?></strong> 	
</h4>
<h4>Type of Owner:<br><strong>
<?php
echo $this->model_query->get_account_owner_conn($dataid)->p_names;
?>	</strong>
</h4>
<h4>Type of Location:<br><strong>
<?php
echo $this->model_query->get_account_owner_location($dataid)->l_names;
?>	</strong>
</h4>

<!--End for Account Details -->
<hr>
<legend>Additional Details:</legend>
<h4>
Date Created:<br><strong>
<?php
echo $this->model_query->get_owner_info($dataid)->DC;
?>	</strong>
</h4>
<h4>	Created by:<br><strong>
<?php
echo $this->model_query->get_owner_info($dataid)->u_first_name.', ';
echo $this->model_query->get_owner_info($dataid)->u_last_name;
?></strong>  
</h4>
<div class="margin-top-20"></div>

</div>	
<!-- end display like other modules -->
</div>
<div class="col-md-6">
<div class="form-group form-md-line-input form-md-floating-label">
<select class="form-control <?php echo htmlspecialchars($edited); ?>" id="account_type" name="account_type">
<?php
echo "<option value='$account_type_val'>$account_type_name</option>";
$account_types = $this->model_inspection_queries->get_account_types();
foreach($account_types as $row){
	$code = $row->names;
	$id = $row->sysid;
	echo "<option value='$id'>$code</option>";
}
?>
</select>
<label for="account_type" class="control-label">Account Type</label>
</div>

</div>
</div>

<div class="portlet light bordered">
<div class="portlet-title">
<div class="caption">
<i class="icon-map"></i>
<span class="caption-subject">Mapping</span>
</div>
</div>
<div class="portlet-body">
<!-- script for google maps api -->
<script src="http://maps.googleapis.com/maps/api/js"></script>
<script>
function initialize() {
	var mapProp = {
center:new google.maps.LatLng(10.702143,122.564495),
zoom:17,
mapTypeId:google.maps.MapTypeId.ROADMAP,
draggableCursor:"crosshair"
	};
	var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
	var marker = new google.maps.Marker({
position: new google.maps.LatLng(10.702143,122.564495),
map: map,
title: 'Hello World!',
draggable: true
	});
}
function update_coordinates()
{
}
google.maps.event.addDomListener(window, 'load', initialize);
//       google.maps.event.addListener(marker, 'dragend', function(a));
//       console.log(a);
google.addListener(marker, "dragend", function() {
	ga('send', 'event', 'map', 'drag/move', 'map');
	var point = marker.getPoint();
	map.panTo(point);
	document.getElementById("lat").innerHTML = point.lat().toFixed(5);
	document.getElementById("lng").innerHTML = point.lng().toFixed(5);
});
</script>

<div id="googleMap" style="width:460px;height:380px;"></div>
<br>
<div class="input-group" name="lol">
<input type="text" class="form-control" id="gmap_geocoding_address" placeholder="Search addr...">
<span class="input-group-btn">
<button class="btn green-turquoise fa fa-search" id="gmap_geocoding_btn"></button>
<button class="btn blue-madison fa fa-camera tooltips" data-container="body" data-placement="top" data-original-title="Capture map coordinates" id="capture" name="capture"> Save Coordinates</button>
<label for="capture" class="tooltip">Capture</label>
</span>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group form-md-line-input has-info">
<!--    <input class="form-control" type="text" id="latitude" name="latitude" readonly value="<?php echo htmlspecialchars($x); ?>"> -->
<label for="latitude">Latitude</label>
<input id="lat" type="text" name="latitude">
</div> 
</div>
<div class="col-md-6">
<div class="form-group form-md-line-input has-info">
<!--    <input class="form-control" type="text" id="longitude" name="longitude" readonly value="<?php echo htmlspecialchars($y); ?>"> -->
<label for="longitude">Longitude</label>
<input id="lng" type="text" name="longitude">
</div>
</div>
</div>
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
<div class="actions">
<a href="#basic" class="btn btn-default btn-sm" data-toggle="modal">
<i class="fa fa-plus"></i>
Add
</a>
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

<div class="caption  text-align-left">
<i class="caption-subject">Summary:  </i>
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
<button type="submit" class="btn btn-lg inline col-md-offset-5 blue-steel fa fa-save submit" id="update_account_info">Submit</button>
</div>
<!-- </form> -->
</div>
</div>
</div>
</div>
</div>
<!-- MODAL START!-->
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<form action="<?php echo base_url();?>query/addEquipments" method="post" id="form_add_eq">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h4 class="modal-title">Equipments/Appliances</h4>
</div>
<div class="modal-body">
<div class="row">

<div class="col-md-6">
<div class="form-group form-md-line-input form-md-floating-label">
<select class="form-control" name="eq_add_code" id="equipment">
<option class="input-sm" value=""></option>
<?php
$equipment = $this->model_inspection_queries->get_equipments();
foreach($equipment as $row){
	$name = $row->codes;
	$id = $row->sysid;
	echo "<option value='$id'>$name</option>";
}
?>
</select>
<label for="eq_add_code" class="control-label">Equipment</label>
</div>
<div class="input-group">
<label for="eq_add_qty" class="control-label">Quantity</label>
<input type="number" class="form-control" name="eq_add_qty" placeholder="Quantity..." value="0">
<span class="help-block">Example: How many?</span>
</div>
</div>
<input type="hidden" id="dataid" name="dataid" value="<?php echo htmlspecialchars($dataid); ?>">
<div class="col-md-6">
<div class="input-group">
<label for="eq_add_power" class="control-label">Power Rating(Watts)</label>
<input type="number" class="form-control" name="eq_add_power" placeholder="Watts..." value="0">
<span class="help-block">Example: 25, 50, 200, etc.</span>
</div>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn default" data-dismiss="modal" name="cancel" id="cancel">back</button>
<button type="submit" class="btn blue" name="add" id="add">Add</button>
</div>
</form>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
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
var latitude = $('[name="latitude"]').val();
var longitude = $('[name="longitude"]').val();
var map = new GMaps({
div: '#gmap_geocoding',
lat: latitude,
lng: longitude,
});
map.addListener('click', function(event){
	latitude = event.latLng.lat();
	longitude = event.latLng.lng();
	map.removeMarkers();
	set_marker(latitude, longitude, 'map click location', 'coordinate: ('+latitude+', '+longitude+')', map.getZoom());
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
var handleAction = function () {
	var text = $.trim($('#gmap_geocoding_address').val());
	GMaps.geocode({
address: text,
callback: function (results, status) {
			if (status == 'OK') {
				var latlng = results[0].geometry.location;
				latitude = latlng.lat();
				longitude = latlng.lng();
				set_marker(latitude, longitude, "Inspection location", text, 14);
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
	if (keycode === '13') {
		e.preventDefault();
		handleAction();
	}
});
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
			initEquipments();
		}
	});
	initEquipments();
	var rate_class = $( "#rate_class" ).val();
	if(rate_class !== '0'){
		ajax_request(rate_class);
	}
});
$("#equipment_added").on('click', 'span', function(){
	
	$.sysid = $(this).closest('tr').find('td').eq(1).find('input');
	var sysid = $.sysid.val();
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
			"url" : "<?php echo base_url(); ?>query/init_acctequipment/<?php echo $dataid; ?>/<?php echo $page; ?>",
			"type" : "POST"
		},
		"language": {
			"emptyTable":     "No Available Data."
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