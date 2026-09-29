var GMAPSMAIN = function() {

    var init_default = function() {
    };

    var init_mapping = function(dataid, div, editable, moduleid) {
        PECO.getGoogleKey();
        $(div).html('<span class="text-info"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading mapping table..</span>');

        var meter = false, house = false, sysidhouse, sysidmeter, lathouse, lnghouse, althouse, latmeter, lngmeter, altmeter, spec, mapurl;
        // GET MAPPING INFO
        $.ajax({
            url: PECO.base_url() + 'inspection/getaccountmapping',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {'id': dataid},
        }).done(function (data) {
            console.log(data);
            lathouse = data.lathouse;
            lnghouse = data.lnghouse;
            althouse = data.althouse;
            mapurl = data.mapurl;
            sysidhouse = data.sysidhouse;

            latmeter = data.latmeter;
            lngmeter = data.lngmeter;
            altmeter = data.altmeter;
            sysidmeter = data.sysidmeter;

            spec = data.spec;

            meter = data.meter;
            house = data.house;

        });

        if(house == true) {
            //alert('house');
            var map = new GMaps({
                div: div,
                lat: lathouse,
                lng: lnghouse,
            });
            set_marker(map, lathouse, lnghouse, 'Location', 'Text', 18, 340, dataid, moduleid, sysidhouse);
            if(meter == true) {
                set_marker(map, latmeter, lngmeter, 'Location', 'Text', 18, 320, dataid, moduleid, sysidmeter);
            }
        }else{
            var map = new GMaps({
                div: div,
                lat: lathouse,
                lng: lnghouse,
            });
            if(meter == true) {
                set_marker(map, latmeter, lngmeter, 'Location', 'Text', 18, 320, dataid, moduleid, sysidmeter);
            }

        }

        map.setZoom(16);

        if (editable) {
            $(div).before(
                '<input type="hidden" value="' + dataid + '" name="dataid" id="input_dataid"/>' +
                '<input type="hidden" id="input_lat" name="lon" readonly="" value="' + lathouse + '">' +
                '<input type="hidden" id="input_lon" name="lat" readonly="" value="' + lnghouse + '">' +
                '<div class="form-group well" style="padding: 10px 10px; margin-bottom: 0px;">' +
                '<div class="row">' +
                '<div class="col-md-6">' +
                '<div class="form-group">' +
                '<label for="latitude">Latitude</label>' +
                '<h3 class="text-info" style="padding: 0px 0px !important; margin: 0px 0px;"><i class="fa fa-location-arrow"></i> <span id="text_lat">' + lathouse + '</span></h3>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                '<div class="form-group">' +
                '<label for="longitude">Longitude</label>' +
                '<h3 class="text-info" style="padding: 0px 0px !important;; margin: 0px 0px;"><i class="fa fa-location-arrow"></i> <span id="text_lon">' + lnghouse + '</span></h3>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="form-group has-success"><div class="input-group input-icon">' +
                '<i class="fa fa-search"></i><input type="text" class="form-control tooltips" data-container="body" data-placement="top" data-original-title="Google Maps Address"  id="search_address" value="" placeholder="Search address...">' +
                '<span class="input-group-btn">' +
                '<button class="btn green-turquoise fa fa-search" id="btn_search_address"></button>' +
                '</span>' +
                '</div></div>' +
                '<div class="form-group has-success"><div class="input-group input-icon">' +
                '<i class="fa fa-map-pin"></i><input type="text" class="form-control tooltips" data-container="body" data-placement="top" data-original-title="Google Maps URL"  id="search_url" value="'+mapurl+'" placeholder="Update URL...">' +
                '<span class="input-group-btn">' +
                '<button class="btn green-turquoise fa fa-save" id="btn_search_url' +
                '' +
                '"></button>' +
                '</span>' +
                '</div></div>' +
                '<div class="form-group has-success margin-top-10">' +
                '<div class=" input-icon ">' +
                '<i class="fa fa-map-o"></i><input type="text" class="form-control tooltips" data-container="body" data-placement="right" data-original-title="Specific Address / Landmarks" id="spec_address" value="' + spec + '" placeholder="Specific Address">' +
                '</div></div>'
            );
        }
        $(div).after(
            '<div class="input-icon well" style="padding: 5px 5px;">' +
            '<i class="fa fa-map-marker" style="margin-top: 10px !important; margin-left: 0px !important; color: red !important;"></i>' +
            '<h4 class="text-info"> ' +
            '<span id="details_address" style="margin-left: 20px; display: inline-block;">' + spec + '</span>' +
            '</h4>' +
            '</div>'
        );
        $('.tooltips').tooltip();


        /*
        if (editable == true) {
            map.addListener('click', function (event) {
                latitude = event.latLng.lat();
                longitude = event.latLng.lng();
                map.removeMarkers();
                set_marker(map, latitude, longitude, 'map click location', 'coordinate: (' + latitude + ', ' + longitude + ')', map.getZoom());
                get_map_address(latitude, longitude);
            });
        }
        */

        $('#btn_search_address').click(function (e) {
            e.preventDefault();
            handle_search(map, dataid);
        });


        $('#btn_search_url').click(function (e) {
            e.preventDefault();
            var el = $('#search_url', document);
            handle_map_update(el, dataid, moduleid,'#gmap_geocoding');
        });


        if (lathouse == 0 || lnghouse == 0) {
            GMaps.geolocate({
                success: function (position) {
                    map.setCenter(position.coords.latitude, position.coords.longitude);
                    lat = position.coords.latitude;
                    lon = position.coords.longitude;
                    get_map_address(lathouse, lnghouse);
                },
                error: function (error) {
                    alert('Geolocation failed: ' + error.message);
                },
                not_supported: function () {
                    alert("Your browser does not support geolocation");
                }
            });
        } else {
            get_map_address(lathouse, lnghouse);
        }

        handle_contextmenu(map, dataid, moduleid);
    };

    var delMarker = function (markerPar) {
        var sysid = markerPar.get('id');
        $.ajax({
            url: PECO.base_url() + 'inspection/removegoemarker',
            type: 'post',
            data: {'id': sysid},
            dataType: 'json'
        }).done(function(d) {
            markerPar.setMap(null);
            PECO.initAlerts('Map Marker has been removed!', 'Mapping', 'warning');
        });
    };

    var handle_contextmenu = function(map, dataid, moduleid) {

        var meter_marker = PECO.base_url()+'assets/global/img/map_marker_meter.png';
        var house_marker = PECO.base_url()+'assets/global/img/map_marker_house.png';

        map.setContextMenu({
            control: 'map',
            options: [
                {
                    title: '<i class="fa fa-plus"></i> Add House Location',
                    name: 'add_house_marker',
                    action: function(e) {
                        var this_ = this;
                        $.ajax({
                            url: PECO.base_url() + 'inspection/updategeodata',
                            data: {
                                'a': $('#spec_address', document).val(),
                                'x': e.latLng.lat(),
                                'y': e.latLng.lng(),
                                'i': dataid,
                                'moduleid': moduleid,
                                'inspdate': $('#inspection_date', document).val(),
                                'remarks': $('#inspection_remarks', document).val(),
                                'types': 340,
                            },
                            type: 'post',
                            dataType: 'json'
                        }).done(function (data) {
                            var marker = this_.addMarker({
                                id: data.newid,
                                lat: e.latLng.lat(),
                                lng: e.latLng.lng(),
                                title: 'New marker',
                                icon: {
                                    size: new google.maps.Size(64, 64),
                                    url: house_marker
                                },
                                draggable:true,
                                dataid: dataid,
                                datatype: 340
                            });

                            marker.addListener('dragend', handlerDragEvent);
                            google.maps.event.addListener(marker, "rightclick", function (point) {delMarker(marker)});
                        });

                    }
                },{
                    title: '<i class="fa fa-plus"></i> Add Meter Location',
                    name: 'add_meter_marker',
                    action: function(e) {
                        var this_ = this;
                        $.ajax({
                            url: PECO.base_url() + 'inspection/updategeodata',
                            data: {
                                'a': $('#spec_address', document).val(),
                                'x': e.latLng.lat(),
                                'y': e.latLng.lng(),
                                'i': dataid,
                                'moduleid': moduleid,
                                'inspdate': $('#inspection_date', document).val(),
                                'remarks': $('#inspection_remarks', document).val(),
                                'types': 320,
                            },
                            type: 'post',
                            dataType: 'json'
                        }).done(function (data) {
                            var marker = this_.addMarker({
                                id: data.newid,
                                lat: e.latLng.lat(),
                                lng: e.latLng.lng(),
                                title: 'New marker',
                                icon: {
                                    size: new google.maps.Size(64, 64),
                                    url: meter_marker
                                },
                                draggable:true,
                                dataid: dataid,
                                datatype: 320
                            });

                            marker.addListener('dragend', handlerDragEvent);
                            google.maps.event.addListener(marker, "rightclick", function (point) {delMarker(marker)});
                        });

                    }
                }, {
                    title: '<i class="fa fa-refresh"></i> Center here',
                    name: 'center_here',
                    action: function(e) {
                        this.setCenter(e.latLng.lat(), e.latLng.lng());
                    }
                }]
        });


    };

    var handlerDragEvent = function(e) {
        var this_ = this;
        var lat = e.latLng.lat();
        var lng = e.latLng.lng();
        var id = this_.get('dataid');
        var datatype = this_.get('datatype');
        var moduleid = this_.get('datamodule');

        get_map_address(lat, lng);

        $('#text_lat', document).text(lat);
        $('#text_lon', document).text(lng);

        $.ajax({
            url: PECO.base_url() + 'inspection/updategeodata',
            data: {
                'a': $('#spec_address', document).val(),
                'x': lat,
                'y': lng,
                'i': id,
                'moduleid': moduleid,
                'inspdate': $('#inspection_date', document).val(),
                'remarks': $('#inspection_remarks', document).val(),
                'types': datatype,
            },
            type: 'post',
            dataType: 'json'
        }).done(function (data) {

            $('#search_url', document).val(data.url);
            this_['id'] = data.newid; // ASSIGN A NEW ID ON PRESENT MARKER
        }).fail(function () {
            PECO.initAlerts('Error PHP', 'ERROR', 'error');
        });
    };




    var handle_search = function(map, dataid) {
        var text = $.trim($('#search_address', document).val());
        GMaps.geocode({
            address: text,
            callback: function (results, status) {

                if (status == 'OK') {
                    var latlng = results[0].geometry.location;
                    latitude = latlng.lat();
                    longitude = latlng.lng();
                    //set_marker(map, latitude, longitude, "Inspection location", text, 17, dataid);
                    get_map_address(latitude, longitude);

                    map.setCenter(latitude, longitude);
                }
            },
        });
    };


    var set_marker = function(map, lat, lon, title, text, zoom, type, dataid, moduleid, sysid) {

        $('#input_lat', document).val(lat);
        $('#text_lat', document).text(lat);
        $('#input_lon', document).val(lon);
        $('#text_lon', document).text(lon);


        var image = PECO.base_url()+'assets/global/img/map_marker_house.png';
        if(type == 340) {
            var image = PECO.base_url()+'assets/global/img/map_marker_house.png';
        }
        if(type == 320) {
            var image = PECO.base_url()+'assets/global/img/map_marker_meter.png';
        }
        // map.setCenter(lat, lon);
        var marker = map.addMarker({
            id: sysid,
            lat: lat,
            lng: lon,
            title: title,
            infoWindow: {
                content: '<span style="color:#000"><i class="fa fa-map-pin"></i> ' + text + '</span>'
            },

            icon: {
                size: new google.maps.Size(64, 64),
                url: image
            },

            dataid: dataid,
            datatype: type,
            moduleid: moduleid,
            draggable: true
        });


        google.maps.event.addListener(marker, "rightclick", function (point) {
            delMarker(marker)
        });

        map.setZoom(zoom);

        // marker.addListener('drag', handlerDragEvent);
        marker.addListener('dragend', handlerDragEvent);
    };


    var currentId = 0;
    var uniqueId = function() {
        return ++currentId;
    };

    var get_map_address = function(lat, lon) {
        // GET MAP ADDRESS NAME
        var mapinfo;
        return $.ajax({
            url: "https://maps.googleapis.com/maps/api/geocode/json?key="+PECO.google_api()+"&latlng=" + lat + "," + lon + "&sensor=true",
            async: false,
            dataType: 'json',
            data: 'text',
        }).done(function (data) {
            mapinfo = data;
            console.log(data);
            var address_full = data.results[0].formatted_address;
            $('#search_address', document).val(address_full);
        });
        return mapinfo;
    };


    var init_map_specific = function(div, dataid, type) {
        var lat, lon, alt, spec, title, text;
        $.ajax({
            url: PECO.base_url() + 'inspection/getaccountmap',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {'id': dataid, 'type': type},
        }).done(function (data) {
            if(data.qry==true) {
                console.log(data);
                lat = data.lat;
                lon = data.lon;
                alt = data.alt;
                //spec = data.spec;
                //title = data.servno;
                //text = data.name;
                var map = new GMaps({
                    div: div,
                    lat: lat,
                    lng: lon,
                    zoomControl : false,
                    zoomControlOpt: {
                        style : "SMALL",
                        position: "TOP_LEFT"
                    },
                    panControl : false,
                    streetViewControl : false,
                    mapTypeControl: false,
                    overviewMapControl: false
                });


                set_marker_specific(map, lat, lon, alt);
                $(div).after(
                    '<div class="row">' +
                    '<div class="col-md-12">'+
                    '<code class="" style="font-size: 10px; display: block">'+
                    '<span id="map_lat">'+lat+'</span>'+
                    ' / ' +
                    '<span id="map_lon">'+lon+'</span>'+
                    '</code>' +
                    '</div>'+
                    '<div class="col-md-12"><div class="well" style="padding: 5px 10px;"><span class="text-info text-color-blue">'+get_map_address(lat, lon).responseJSON.results[0].formatted_address+'</span></div></div>'+
                    '</div>'
                );

            }else{
                $(div).html('<img src="'+PECO.base_url()+'assets/global/img/no_maps.png" style="width: 100%; height: 100%; margin: 10% auto;" />');
            }
        });
    };

    var set_marker_specific = function(map, lat, lon, alt) {
        var image = PECO.base_url()+'assets/global/img/peco-map-marker.png';
        map.setCenter(lat, lon);
        map.addMarker({
            lat: lat,
            lng: lon,
            icon: {
                size: new google.maps.Size(64, 64),
                url: image
            },
        });
        map.setZoom(18);
    };

    var handle_map_update = function(el, dataid, moduleid, div) {
        var url = $.trim(el.val());
        $.ajax({
            url: PECO.base_url() + 'inspection/updatemapurl',
            type: 'post',
            data: {appid: dataid, url: url},
            dataType: 'json'
        }).done(function(d) {
            if(d.qry) {

                var lat = d.lat;
                var lng = d.lng;
                var id = d.id;

                var map = new GMaps({
                    div: div,
                    lat: lat,
                    lng: lng,
                    zoomControl : false,
                    zoomControlOpt: {
                        style : "SMALL",
                        position: "TOP_LEFT"
                    },
                    panControl : false,
                    streetViewControl : false,
                    mapTypeControl: false,
                    overviewMapControl: false
                });

                map.setCenter(lat, lng);
                set_marker(map, lat, lng, 'Location', 'Text', 18, 340, dataid, moduleid, id);
            }else{
                alert('Qry Error!');
            }
        }).fail(function() {
            alert('PHP Error!');
        });
    };

    return {
        init: function(div) {
            init_default(div);
        },
        mapping: function(dataid, div, editable, moduleid) {
            setTimeout(function() {
                if (typeof google === 'object' && typeof google.maps === 'object') {
                    init_mapping(dataid, div, editable, moduleid);
                }else{
                    alert('Google Map API not loaded!');
                }
            },1000);
        },
        mapspec: function(div, dataid, type) {
            init_map_specific(div, dataid, type);
        }
    }
}();