// Google Maps Scripts
// When the window has finished loading create our google map below
google.maps.event.addDomListener(window, 'load', init(lat, lon));
function init(lat, lon) {
    // Basic options for a simple Google Map 
    // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions

	var latitude = lat;
	var longtitude = lon;
	var cityCircle;
    var mapOptions = {
        // How zoomed in you want the map to start at (always required)
        zoom: 20,

        // The latitude and longitude to center the map (always required)
        center: new google.maps.LatLng(latitude,longtitude), // New York
		center: {lat: latitude, lng: longtitude},
		center: {lng: longtitude, lat: latitude},
        // Disables the default Google Maps UI components
        disableDefaultUI: true,
        scrollwheel: true,
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
    var mapElement = document.getElementById('map');
	
	// Create the Google Map using out element and options defined above
    var map = new google.maps.Map(mapElement, mapOptions);
	



    // Custom Map Marker Icon - Customize the map-marker.png file to customize your icon

    var image = PECO.base_url()+'assets/global/img/peco-map-marker.png';
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
		strokeOpacity: 0.1,
		strokeWeight: 1,
		fillOpacity: 0.1
  	});
	circle.bindTo('center', beachMarker, 'position');


     var iw1 = new google.maps.InfoWindow({
       content: "Panay Electric Company, Inc."
     });
}
