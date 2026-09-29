var TEST = function() {

    var init_testing_fn = function() {

        var overlays = [];
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 10.6984998, lng: 122.5690597 },
            zoom: 20,
            mapTypeId: 'satellite'

        });
        const drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYLINE,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [
                    google.maps.drawing.OverlayType.MARKER,
                    google.maps.drawing.OverlayType.CIRCLE,
                    google.maps.drawing.OverlayType.POLYGON,
                    google.maps.drawing.OverlayType.POLYLINE,
                    google.maps.drawing.OverlayType.RECTANGLE,
                ],
            },
            markerOptions: {
                icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
            },
            circleOptions: {
                fillColor: "#ffff00",
                fillOpacity: 1,
                strokeWeight: 5,
                clickable: false,
                editable: true,
                zIndex: 1,
            },
        });
        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
            overlays.push(event); // store reference to added overlay
        });

        $(document).on('click', '#btn_undo', function(e) {
            e.preventDefault();

            var lastOverlay = overlays.length > 0 ? overlays[overlays.length - 1] : null;

            if (lastOverlay && lastOverlay.type === "polyline") {
                var path = lastOverlay.overlay.getPath();
                path.pop(); // remove last line segment
            }
        });

        shortcut.add('ctrl+z', function () {
            var lastOverlay = overlays.length > 0 ? overlays[overlays.length - 1] : null;
            if (lastOverlay && lastOverlay.type === "polyline") {
                var path = lastOverlay.overlay.getPath();
                path.pop(); // remove last line segment
            }
            return false;
        });
    };

    return {
        init: function() {
            init_testing_fn();
        }
    }
}();