var MRD = function () {
	// INITIALIZE HIGHLIGHTS SEARCH IN TABLE
	PECO.getHighlightsPlugin();
	
	// VARIABLES
	var table_reading_entry = $('#tbl_reading_entry');
	

	
	var init_reading = function () {
		table_reading_entry.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
				language: {
				  "emptyTable": '<h4><i class="fa fa-search text-info"></i> Search schedule code first.</h4>'
				},
				"columnDefs": [
                     {orderable: false, searchable: false, "targets": '_all'},
                ],
		});
		
		$('#get_mrd_list').click(function(e) {
			console.log('loading reading list..');
			e.preventDefault();
			init_reading_table();
		});
	};
	
	var formatDataSelection = function(route) {

		if (!route.id) {
			return route.text;
		}
		var $route = $('<span><i class="fa fa-check text-success"></i> ' + route.text.split('-', 1) + '</span>');
		return $route
	}

	var formatState = function(route) {
		var text_arr = route.text.split('-');
		if (!route.id) {
			return route.text;
		}
		var $route = $(
				'<p><b>' + text_arr[0] + '</b> - '+text_arr[1]+'</p>'
				);
		return $route;
	}
	var droawSelect2Tbl_entry = function() {
		$('tr td', table_reading_entry).each(function (e) {
			$(this).find('#findings').select2({
				placeholder: 'Select..',
				allowClear: true,
				formatResult: formatState,
				formatSelection: formatDataSelection
			}).change(function () {
				var this_ = $(this);
				var this_tr = this_.closest('tr');
				var this_sub = this_tr.find('#sub_findings');
				if (this_.val() != '') {
					this_sub.attr('disabled', false);
				} else {
					this_sub.attr('disabled', true);
				}
			});
			
		});
		PECO.select2_scroller();
	}   
    var init_reading_table = function () {
	
        $.ajax({
            url: PECO.base_url() + 'mrd/samplereading',
            type: 'POST',
            dataType: 'json',
        }).done(function (data) {
            table_reading_entry.dataTable().empty();
            var dt = table_reading_entry.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data['data'], 
                aoColumns: [
                    {"data": "expand"},
                    {"data": "sysid"},
                    {"data": "servno", sClass: "text-info"},
					{"data": "mtr"},
					{"data": "mtrserial",
						mRender: function(data) {
							return Math.floor((Math.random() * 5000000) + 1000000);	
						}
					},
					{"data": "name"},
					{"data": "address"},
					{"data": "readinput"},
					{"data": "findings"},
					{"data": "control"},
                ],
				language: {
				  "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
				},
                "columnDefs": [
                    /* IMPORTANT IF USED DYNAMIC SERVER TABLE LIST
                     {"orderable": false, searchable: false, "targets": 0},
                     */
                ],
                "order": [[1, "asc"]],
                "lengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": 10,
                fnDrawCallback: function () {
                    console.log('Table drawn..');
					droawSelect2Tbl_entry();
                },
                /*
                 * Highlith on search inside the table
                 * need datatable plugins
                 */
                searchHighlight: true
            });


            table_reading_entry.on('click', '#btn-expand', function () {
                console.log('details clicked!');
                var this_ = $(this);
                var thisTr = this_.closest('tr');
                var thisTr_child = thisTr.children('td').length;
                if (this_.hasClass('expanded') == false) {

                    $.ajax({
                        url: PECO.base_url() + 'settings/getinfo',
                        type: 'post',
                        dataType: 'json',
                        data: {'id': this_.attr('data-id')},
                        beforeSend: function () {
                            thisTr.after('<tr id="loading"><td colspan="' + thisTr_child + '">Loading..</td></tr>');
                        }
                    }).done(function (data) {
                        if (data.qry === true) {
                            var data_details = '<div class="row">';
                           	data_details += '<div class="col-md-3"><h5 class="text-info"><i class="fa fa-map-o fa-fw"></i> <b>Default Map</b></h5><img src="'+PECO.base_url()+'assets/global/img/samplemap.gif" width="100%" height="130px"/><br>Geo Data X/Y: <code>10.2533488 / 132.155221</code></div>';
							
							data_details += '<div class="col-md-9">';
							
							data_details += '<div class="col-md-4">';
							data_details += '<h5 class="text-info"><i class="fa fa-map-marker fa-fw"></i> <b>Location Details</b></h5>';
                            data_details += '<ul class="list-group">';
                            data_details += '<li class="list-group-item">Landmark: <span class="data pull-right">Robinsons Place Iloilo</span></li>';
                            data_details += '<li class="list-group-item">House / Gate No: <span class="data pull-right"> 322</span></li>';
                            data_details += '<li class="list-group-item">Brgy / Streen Name: <span class="data pull-right"> Mabini Street</span></li>';
                            data_details += '<li class="list-group-item">District: <span class="data pull-right"> Iloilo</span></li>';
							data_details += '<li class="list-group-item">Lot & Book: <span class="data pull-right">01-01</span></li>';
							data_details += '<li class="list-group-item">Map Updated: <span class="data pull-right">2016-01-01</span></li>';
                            data_details += '</ul>';
                            data_details += '</div>';
							
							data_details += '<div class="col-md-2 pull-right">';
							data_details += '<button class="btn btn-default btn-block btn-xs margin-bottom-10">Print Map</button>';
							data_details += '<button class="btn btn-info btn-block  btn-xs">Print Account</button>';
							data_details += '</div>';
							
							
							data_details += '</div>';
							
                            data_details += '</div>';
                            thisTr.after('<tr class="animated fadeIn fast compact" id="details"><td colspan="' + thisTr_child + '">' + data_details + '</td></tr>');
							
                        } else {
                            thisTr.after('<tr class="animated fadeIn fast compact"  id="details"><td colspan="' + thisTr_child + '"><i class="fa fa-warning text-warning"></i> No Record Found!</td></tr>');
                        }
                    });

                    this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                    thisTr.next('#loading').remove();
                } else {
                    thisTr.next('#details').remove();
                    thisTr.next('#loading').remove();
                    this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                }
                this_.toggleClass('expanded');
            });

            /*
             // Array to track the ids of the details displayed rows
             var detailRows = [];
             
             
             // On each draw, loop over the `detailRows` array and show any child rows
             dt.on('draw', function () {
             $.each(detailRows, function (i, id) {
             $('#' + id + ' td.details-control').trigger('click');
             });
             });
             */
        }).fail(function() {
			PECO.phpError();
		});
		
		table_reading_entry.on('change', '#findings', function () { 
			var this_ = $(this);
			console.log('Findings changed - value: '+this_.val() );
			$.ajax({
				url: PECO.base_url()+'mrd/getfindingssub',
				type: 'POST',
				data: {'id': this_.val()},
				dataType: 'json',
			}).done(function(data) {
				var sub_findings = this_.closest('tr').find('#findings_desc');
				if(data.qry == true) {
					sub_findings.select2({ 
						data: data.list,
						placeholder: 'Select..',
						allowClear: true,
						formatResult: formatState,
						formatSelection: formatDataSelection
					}).attr('disabled', false);	
				}else{
					sub_findings.select2('destroy').attr('disabled', true).val('');
				}
			});
		});
    };
	var init_reading_map_loc_start = function(this_) {
		google.maps.event.addDomListener(window, 'load', init_reading_map_loc(this_));
	};
	
	var init_reading_map_loc = function(this_) {
			
			// Basic options for a simple Google Map 
			// For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
			var latitude = 10.702137;
			var longtitude = 122.564495;
			var cityCircle;
			var mapOptions = {
				// How zoomed in you want the map to start at (always required)
				zoom: 16,
		
				// The latitude and longitude to center the map (always required)
				center: new google.maps.LatLng(latitude,longtitude), // New York
				center: {lat: latitude, lng: longtitude},
				center: {lng: longtitude, lat: latitude},
				// Disables the default Google Maps UI components
				disableDefaultUI: true,
				scrollwheel: false,
				draggable: true,
				styles: [{
					"featureType": "water",
					"elementType": "geometry",
					"stylers": [{
						"color": "#91CFEE"
					}, {
						"lightness": 17
					}]
				}, {
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers": [{
						"color": "#FFFFFF"
					}, {
						"lightness": 20
					}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers": [{
						"color": "#F3FB60"
					}, {
						"lightness": 17
					}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers": [{
						"color": "#F0CC9B"
					}, {
						"lightness": 29
					}, {
						"weight": 0.2
					}]
				}, {
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers": [{
						"color": "#cccccc"
					}, {
						"lightness": 18
					}]
				}, {
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers": [{
						"color": "#cccccc"
					}, {
						"lightness": 16
					}]
				}, {
					"featureType": "poi",
					"elementType": "geometry",
					"stylers": [{
						"color": "#cccccc"
					}, {
						"lightness": 21
					}]
				}, {
					"elementType": "labels.text.stroke",
					"stylers": [{
						"visibility": "on"
					}, {
						"color": "#FFFFFF"
					}, {
						"lightness": 1
					}]
				}, {
					"elementType": "labels.text.fill",
					"stylers": [{
						"saturation": 36
					}, {
						"color": "#DF5F09"
					}, {
						"lightness": 40
					}]
				}, {
					"elementType": "labels.icon",
					"stylers": [{
						"visibility": "on"
					}]
				}, {
					"featureType": "transit",
					"elementType": "geometry",
					"stylers": [{
						"color": "#cccccc"
					}, {
						"lightness": 19
					}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers": [{
						"color": "#FFFFFF"
					}, {
						"lightness": 20
					}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers": [{
						"color": "#FFFFFF"
					}, {
						"lightness": 17
					}, {
						"weight": 1.2
					}]
				}]
		
			};
		
			// Get the HTML DOM element that will contain your map 
			// We are using a div with id="map" seen below in the <body>
			var mapElement = $('#map');
			
			// Create the Google Map using out element and options defined above
			var map = new google.maps.Map(mapElement, mapOptions);
			
		
		
		
			// Custom Map Marker Icon - Customize the map-marker.png file to customize your icon
		
			var image = PECO.base_url()+'assets/global/tp/img/peco-map-marker.png';
			var myLatLng = new google.maps.LatLng(latitude,longtitude);
			var beachMarker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				icon: image,
				title: 'Panay Electric Company, Inc.'
			});
		
		
			// Add circle overlay and bind to marker
			var circle = new google.maps.Circle({
				map: map,
				radius: 100, 
				fillColor: '',
				strokeColor: "#FF0000",
				strokeOpacity: 0.5,
				strokeWeight: 1,
				fillOpacity: 0.2
			});
			circle.bindTo('center', beachMarker, 'position');
			
			/*var marker1 = new MarkerWithLabel({
			   position: beachMarker,
			   draggable: true,
			   raiseOnDrag: true,
			   map: map,
			   labelContent: "PECO",
			   labelAnchor: new google.maps.Point(latitude,longtitude),
			   labelClass: "labels", // the CSS class for the label
			   labelStyle: {opacity: 0.75}
			 });*/
		
			 var iw1 = new google.maps.InfoWindow({
			   content: "Panay Electric Company, Inc."
			 });
			 //google.maps.event.addListener(marker1, "click", function (e) { iw1.open(map, this)});
		};

  
    return {
        reading: function () {
            init_reading();
        },
    };
}();


