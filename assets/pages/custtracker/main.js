//http://hpneo.github.io/gmaps/examples.html
var CUSTTRACKER = function() {
    PECO.getSelect2Plugins();


    var init_tracker = function() {

        $('#batchfile', document).fileinput({
            uploadAsync: true,
            showBrowse: true,
            browseOnZoneClick: true,
            showPreview: true,
            uploadExtraData: function (d) {
                return {
                    display: $('#search_type').select2('val')
                };
            },
        });


        var div = '#map';
        $(div).html('<div class="col-md-12">' +
            '<span class="text-info"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading mapping table..' +
            '</span></div>');
        var map = new GMaps({
            div: div,
            lat: 10.7133503,
            lng: 122.5580168,
            zoom: 13,
        });

        init_default_map('Iloilo', div, map);
        //PECO.initMapStyle(map);


        $('#search_type').select2({placeholder: 'Select type..'});

        $('#search_txt').val('');
        PECO.customerSelectTagging($('#search_txt'), 'Select customers...', '');



        $('#frm_search_geo').submit(function(e){
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json'
            }).done(function (d) {
                init_customers_location(map, d.geo);
            }).fail(function(){
                swal("Error!", "ERROR PHP", "error");
            });
        });


    };

    var init_customers_location = function(map, geo) {
        map.removeMarkers();
        var items, markers_data = [];
        if (geo.length > 0) {
            for (var i = 0; i < geo.length; i++) {
                var item = geo[i];
                if (item.lat != undefined && item.lon != undefined) {
                    markers_data.push({
                        lat: item.lat,
                        lng: item.lon,
                        title: item.servno,
                        infoWindow: {
                            content: item.content
                        },
                        label: item.servno,
                        color: 'blue'
                    });
                }
            }
        }
        map.addMarkers(markers_data);
        map.fitZoom();
    };

    var init_default_map = function(str, div, map) {
        GMaps.geocode({
            address: str,
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
                    PECO.scrollTo($(div));
                }
            }
        });
    };

    var pinSymbol = function(color) {
        return {
            path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
            fillColor: color,
            fillOpacity: 1,
            strokeColor: '#000',
            strokeWeight: 2,
            scale: 2
        };
    }

    return {
        init: function() {
            init_tracker();
        }
    }
}();
