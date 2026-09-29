<html>
    <style>
        body{
            margin: 0px 0px;
            padding: 0px 0px;
            margin-top: 70px !important;
            background: #fff !important;
            min-height:  500px;
        }
        
        .labels {
            color: red;
            background-color: transparent;
            font-family: "Lucida Grande", "Arial", sans-serif;
            font-size: 10px;
            text-align: center;
            width: 30px;
            white-space: nowrap;
          }
        .print-btn {
            display: inline-block;
            text-decoration: none;
            float: left;
            position: fixed;
            top: 2px;
            right: 2px;
            z-index: 1000;
        }
        #map {
            /*height: 500px;
            width: 1000px;            
            ensure that the map is also visible when printing*/
            display: inline-block !important;
        }
		@media print{
			.labels {
				background-color: transparent;
				font-family: "Lucida Grande", "Arial", sans-serif;
				font-size: 10px;
				text-align: center !important;
				width: 30px;
				white-space: nowrap;
          }
		}
    </style>
    <body>
        <!--<a class="btn btn-info btn-xs print-btn list-print" href="javascript:;"><i class="fa fa-print"></i> Print List</a>-->
        <a class="btn btn-info btn-xs print-btn map-print" href="javascript:;"><i class="fa fa-print"></i> Print</a>
        <div id="customer_list">
            <hr class="print-hidden margin-bttom-20">
            
            <img src="<?php echo base_url(); ?>assets/global/img/PECO-LETTER-HEAD.png" style="position: absolute; top:0px; left: 0px; right: 0px; height: 50px; width: 100%;" />

            <h3>Customer List - For Connection</h3>
            <table class="table table-hover table-striped tbl-xs">

                <thead>
                <th></th>
                <th>Customer Name</th>
                <th>Address</th>
                <th>Meter Serial</th>
                <th>G/D/L/B</th>
                <th>Date Time</th>
                </thead>
                <tbody>
                    <?php
                    $qry_asset = $this->db->select('ah.datecreated, ah.assetid, ah.ownerid, ah.ownertype, am.serialcodes')
                            ->from('assets_main_owner_history AS ah')
                            ->join('assets_main am', 'ah.assetid = am.sysid')
                            ->where('ah.status', 1)
                            ->limit(30)
                            ->get();

                    if ($qry_asset->num_rows() > 0) {
                        $num = 1;
                        $acctids = '';
                        foreach ($qry_asset->result() as $row) {

                            if ($row->ownertype == 91) {
                                $qry_owner = $this->db->select('o.ownerid, p.firstname, p.lastname, p.middlename, oa.addrspecific, o.accountid')
                                                ->from('customer_accounts_owners AS o')
                                                ->join('person AS p', 'p.sysid = o.ownerid')
                                                ->join('customer_accounts_address AS oa', 'oa.acctid = o.accountid')
                                                ->where('o.sysid', $row->ownerid)
                                                ->get()->row();
                                if ($qry_owner) {
                                    $owner = $qry_owner->lastname . ', ' . $qry_owner->firstname . ' ' . $qry_owner->middlename;
                                    $addrspec = $qry_owner->addrspecific;
                                    $acctids .= $qry_owner->accountid . ',';
                                } else {
                                    $owner = '';
                                    $addrspec = '';
                                }
                            } else {
                                $qry_owner = $this->db->select('c.descs, oa.addrspecific, o.accountid')
                                                ->from('customer_accounts_owners AS o')
                                                ->join('corporation AS c', 'c.sysid = o.ownerid')
                                                ->join('customer_accounts_address AS oa', 'oa.acctid = o.accountid')
                                                ->where('o.sysid', $row->ownerid)
                                                ->get()->row();
                                if ($qry_owner) {
                                    $owner = $qry_owner->descs;
                                    $addrspec = $qry_owner->addrspecific;
                                    $acctids .= $qry_owner->accountid . ',';
                                } else {
                                    $owner = '';
                                    $addrspec = '';
                                }
                            }
                            echo '
            <tr>
                <td>' . $num++ . '</td>
                <td>' . $owner . '</td>
                <td>' . $addrspec . '</td>
                <td class="text-danger">' . $row->serialcodes . '</td>
                <td>';
                            echo (acct_gdlb($row->ownerid)) ? acct_gdlb($row->ownerid)->GDLB : 'N/A';
                            echo '</td>
                <td>';
                            echo $row->datecreated;
                            echo '</td>
                
            </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="map" class="gmaps margin-bottom-40 print-hidden" style="height: 1000px;width: 1000px"></div>
    <!--    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRngKslUGJTlibkQ3FkfTxj3Xss1UlZDA"></script>-->
        <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
        
        <script src="https://cdn.rawgit.com/googlemaps/v3-utility-library/master/markerwithlabel/src/markerwithlabel.js"></script>
        <script src="<?php echo base_url() ?>assets/global/tp/pages/jquery.googlemap.js"></script>

        <script type="text/javascript">

            $(function () {

                //var image = '';
                var image = PECO.base_url() + 'assets/global/img/peco-map-marker.png';
                $("#map").googleMap();

                function initMap() {
                    $.ajax({
                        url: PECO.base_url() + 'query/marklocation',
                        type: 'POST',
                        data: {'acctid': '<?php echo $acctids ?>'},
                        dataType: 'json',
                    }).done(function (d) {
                        var latLng = new google.maps.LatLng(10.7202, 122.5621);
                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 13,
                            center: latLng,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        });
                        
                        for (var i = 0; i < d['latitude'].length; i++) {
                            
                            var homeLatLng = new google.maps.LatLng(d.latitude[i], d.longitude[i]);
                            new MarkerWithLabel({
                                position: homeLatLng,
                                //title: d.address[i],
                                map: map,
                                draggable: true,
                                raiseOnDrag: true,
                                labelContent: i,
                                labelClass: "labels", // the CSS class for the label
                                icon: pinSymbol('white'),
                                labelAnchor: new google.maps.Point(15, 35)
                                //icon: image,

                            });
                        }

                        

                        
                        /*
                        var marker = new MarkerWithLabel({
                            position: homeLatLng,
                            map: map,
                            draggable: true,
                            raiseOnDrag: true,
                            labelAnchor: new google.maps.Point(15, 65),
                            labelClass: "labels", // the CSS class for the label
                            labelInBackground: false,
                            icon: pinSymbol('red')
                        });

                        var iw = new google.maps.InfoWindow({
                            content: "Home For Sale"
                        });
                        google.maps.event.addListener(marker, "click", function (e) {
                            iw.open(map, this);
                        });*/
                    }).fail(function () {
                        console.log('FAIL');
                    });

                }

                function pinSymbol(color) {
                    return {
                        path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
                        fillColor: color,
                        fillOpacity: 1,
                        strokeColor: '#F00',
                        strokeWeight: 2,
                        scale: 1
                    };
                }
                $('.map-print').on('click', function printMaps() {

                    var body = $('body');
                    var mapContainer = $('#map');
                    var mapContainerParent = mapContainer.parent();
                    var printContainer = $('<div>');
                    var table = $('#customer_list');
                    
                    
                    mapContainer.css('text-indent', '12px');
                    printContainer
                            .addClass('print-container')
                            .css('position', 'relative')
                            .height(mapContainer.height())
                            .append(table)
                            .append(mapContainer)
                            .prependTo(body);
                    $('footer').css('page-break-after', 'always');
                    var content = body
                            .children()
                            .not('script')
                            .not(printContainer)
                            .detach();

                    // Patch for some Bootstrap 3.3.x `@media print` styles. :|
                    var patchedStyle = $('<style>')
                            .attr('media', 'print')
                            .text('img { max-width: none !important; } .labels {text-align: center !important;}' +
                                    'a[href]:after { content: ""; }')
                            .appendTo('head');

                    window.print();
					
                    body.prepend(content);
                    mapContainerParent.prepend(table).prepend(mapContainer);
					load_map(window, initMap);
                    printContainer.remove();
                    patchedStyle.remove();
                });
                $('.list-print').on('click', function() {
                    var table = $('#customer_list');
                    var printContainer = $('<div>');
                    
                    printContainer.append(table);
                    
                    window.print();
                });
				load_map(window, initMap);
				function load_map(window, initMap) {
                google.maps.event.addDomListener(window, 'load', initMap);
				}
				
            });
        </script>
    </body>
</html>