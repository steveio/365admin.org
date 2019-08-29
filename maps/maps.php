<!DOCTYPE html>
<html>
  <head>
<title>Google Maps lat/long finder</title>
<meta name="keywords" content="Google Maps lat/long finder" />
<meta name="description" content="Find the latitude and longitude of a location chosen on a map" />
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script src="js/site.js" type="text/javascript"></script>
  <script src="js/common.js" type="text/javascript"></script>
  </head>
<body>
<div class="realContent">
 <p>Click on the map and the latitude and longitude of where you've clicked will be shown.  Alternatively, enter an address or place / location (including country) into the "search for an address" field</p>
<!-- 
  <p>Click on the map and the latitude and longitude of where you've clicked will
  be shown. Alternatively click the 'Get Map Centre' button to get the location
  of the centre of the map. Or you can type the latitude/longitude to see
  the location on the map. And you can use Street View to improve your positioning,
  where it's available.</p>
 -->
  <table style="width:820px;">
    <tr>
      <td colspan="2"><b>Click on the map to get the latitude/longitude...</b></td>
    </tr>
    <tr>
      <td><div id="map" style="width: 345px; height: 398px"></div></td>
      <td valign="top">
        <table>
					<tr>
            <td colspan="3"><b>...or search for an address...</b></td>
          </tr>
					<tr>
						<td colspan="3">
							<input id="postcode" />
							<input type="submit" onclick="Geocode()" value="Search" />
						</td>
					</tr>
					<tr>
						<td colspan="3" id="error">
						</td>
					</tr>
					<tr>
						<td colspan="3" id="locations">
						</td>
					</tr>
		<!-- 
          <tr>
            <td colspan="3"><b>...or click this button to get the latitude/longitude of the centre of the map...</b></td>
          </tr>
          <tr>
            <td colspan="3">
              <input type="button" value="Get map centre" onclick="GetLocationInfo(map.getCenter())" />
            </td>
          </tr>
         -->
          <tr>
            <td><img src="images/lat.png" alt="Latitude" /></td>
            <td><label for="lat">Latitude:</label></td>
            <td><input id="lat"   /></td>
          </tr>
          <tr>
            <td><img src="images/lng.png" alt="Longitude" /></td>
            <td><label for="long">Longitude:</label></td>
            <td><input id="long" /></td>
          </tr>
          
          <tr>
            <td colspan="3">
              <b>...or type in your latitude/longitude and click this to view its location...</b>
            </td>
          </tr>
          <tr>
            <td colspan="3">
              <input type="button" value="Go to this location" onclick="GotoLatLong()" />
            </td>
          </tr>
					<tr>
						<td colspan="3">Lat/lng in deg min sec</td>
					</tr>
					<tr>
						<td><img src="images/lat.png" alt="Latitude" /></td>
            <td>Latitude:</td>
            <td id="latDMS"></td>
          </tr>
					<tr>
						<td><img src="images/lng.png" alt="Longitude" /></td>
            <td>Longitude:</td>
            <td id="lngDMS"></td>
          </tr>
          <!--
					<tr>
						<td><img src="images/lat.png" alt="Northing" /></td>
						<td>Northing:</td>
            <td id="northing"></td>
					</tr>
					<tr>
						<td><img src="images/lng.png" alt="Easting" /></td>
						<td>Easting:</td>
            <td id="easting"></td>
					</tr>
          <tr>
            <td colspan="2">Map zoom:</td>
            <td id="zoom"></td>
          </tr>
          -->
          <tr>
            <td colspan="2">Altitude:</td>
            <td id="elevation"></td>
          </tr>
          
          <tr>
            <td colspan="2">Approximate address:</td>
            <td id="address"></td>
          </tr>
          <!--
          <tr>
            <td colspan="3">
              <b>...and if Street View is available you can improve your positioning by moving to the location you're interested in below</b>
            </td>
          </tr>
          <tr>
            <td colspan="3">
              <input type="button" value="Start StreetView" onclick="StartStreetView();" id="streetViewBtn" />
            </td>
          </tr>
          
          <tr>
            <td colspan="3" id="mapLink">
            </td>
          </tr>
          -->
          
          <tr>
				<td colspan="3">
					Save this location:<br /> 
					<input type="submit" onclick="SaveCoords()" value="Save Location" />
				</td>
			</tr>
        </table>
      </td>
    </tr>
  </table>
  <div id="panoError"></div>
  <div id="pano" style="width: 750px; height: 300px;">
  </div>

  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
	<script src="js/jscoord-1.1.1.js" type="text/javascript"></script>
  <script type="text/javascript">
  /* <![CDATA[ */
      var latlng = new google.maps.LatLng(54.559322587438636, -4.1748046875);
      var options = {
          zoom: 5,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          draggableCursor: "crosshair",
          streetViewControl: false
        };
      var map = new google.maps.Map(document.getElementById("map"), options);
      $("#zoom").html(5);
		  google.maps.event.addListener(map,"click", function(location)
      {
        GetLocationInfo(location.latLng);
      });
      google.maps.event.addListener(map,'zoom_changed', function(oldLevel, newLevel)
      {
        $("#zoom").html(map.getZoom());
      });
      var myPano = new google.maps.StreetViewPanorama(document.getElementById("pano"),
            { visible:false});
      myPano.setPov({
        heading: 265,
        zoom:1,
        pitch:0});
      $('#pano').hide();
      google.maps.event.trigger(myPano, 'resize');

      var initListener;
      var marker;
      function StartStreetView() {
        // street view
        if ($("#streetViewBtn").val() == "Start StreetView") {
          initListener = google.maps.event.addListener(myPano, "position_changed", handlePanoMove);
          $('#pano').show();
          myPano.setVisible(true);
          $("#streetViewBtn").val("End StreetView");
          google.maps.event.trigger(myPano, 'resize');
          GotoLatLong();
        }
        else {
          google.maps.event.removeListener(initListener);
          myPano.setVisible(false);
          $('#pano').hide();
          $("#streetViewBtn").val("Start StreetView");
          google.maps.event.trigger(myPano, 'resize');
        }
      }

			function Geocode()
			{
				$("#locations").html("");
				$("#error").html("");
				// geocode with google.maps.Geocoder
				var localSearch = new google.maps.Geocoder();
				var postcode = $("#postcode").val();
				localSearch.geocode({ 'address': postcode },
					function(results, status) {
						if (results.length == 1) {
							var result = results[0];
							var location = result.geometry.location;
							GotoLocation(location);
						}
						else if (results.length > 1) {
							$("#error").html("Multiple addresses found");
							// build a list of possible addresses
							var html = "";
							for (var i=0; i<results.length; i++) {
								html += '<a href="javascript:GotoLocation(new google.maps.LatLng(' + 
									results[i].geometry.location.lat() + ', ' + results[i].geometry.location.lng() + '))">' + 
									results[i].formatted_address + "</a><br/>";
							}
							$("#locations").html(html);
						}
						else {
							$("#error").html("Address not found");
						}
					});
			}
			
			function GotoLocation(location) {
				GetLocationInfo(location);
				map.setCenter(location);
			}
			
      function GetLocationInfo(latlng)
      {
        if (latlng != null)
        {
          ShowLatLong(latlng);
          UpdateStreetView(latlng);
        }
      }

      function GotoLatLong()
      {
        if ($("#lat").val() != "" && $("#long").val() != "") {
          var lat = $("#lat").val();
          var long = $("#long").val();
          var latLong = new google.maps.LatLng(lat, long);
          ShowLatLong(latLong);
          map.setCenter(latLong);
          UpdateStreetView(latLong);
        }
      }

      function ShowLink(){
        $("#mapLink").html('<a href="ShowMap.php?lat=' + $("#lat").val() +
          '&long=' + $("#long").val() + '&zoom=' + $("#zoom").html() + '">Link for this map</a>');
      }

			function toDMS(latOrLng) {
				var d = parseInt(latOrLng);
				var md = Math.abs(latOrLng - d) * 60;
				var m = Math.floor(md);
				var sd = (md - m) * 60;
				return Math.abs(d) + "\u00B0 " + m + "' " + roundNumber(sd, 4) + '"';
			}
			
			function latToDMS(lat) {
				var dms = toDMS(lat);
				if (lat > 0)
					return dms + "N";
				else
					return dms + "S";
			}
			
			function lngToDMS(lng) {
				var dms = toDMS(lng);
				if (lng > 0)
					return dms + "E";
				else
					return dms + "W";
			}
			
      function ShowLatLong(latLong)
      {
        // show the lat/long
        if (marker != null) {
          marker.setMap(null);
        }
        marker = new google.maps.Marker({
          position: latLong,
          map: map});
        $("#lat").val(roundNumber(latLong.lat(), 6));
        $("#long").val(roundNumber(latLong.lng(), 6));
				$("#latDMS").html(latToDMS(latLong.lat()));
				$("#lngDMS").html(lngToDMS(latLong.lng()));
        ShowLink();
        GetElevation(latLong.lat(), latLong.lng(), '#elevation');
        ReverseGeocode(latLong.lat(), latLong.lng(), '#address');
				
				// UK easting/northing
				if ((latLong.lat() > 49) & (latLong.lat() < 61) & 
				(latLong.lng() > -12) & (latLong.lng() < 3)) {
					var ll2w = new LatLng(latLong.lat(), latLong.lng());
					ll2w.WGS84ToOSGB36();
					var os2w = ll2w.toOSRef();
					$('#easting').html(Math.round(os2w.easting));
					$('#northing').html(Math.round(os2w.northing));
				}
				else {
					$('#easting').html("Not in UK");
					$('#northing').html("Not in UK");
				}
      }

      function UpdateStreetView(latLong)
      {
        // street view
        if ($("#streetViewBtn").val() == "End StreetView") {
          $("#panoError").html("");
          myPano.setVisible(true);
          myPano.setPosition(latLong);
          // also set via the service API so we know if there is a view available
          var service = new google.maps.StreetViewService();
          service.getPanoramaByLocation(latLong, 50,
            function(result, status) {
              if (status != google.maps.StreetViewStatus.OK) {
                $("#panoError").html("No street view available");
                myPano.setVisible(false);
              }
            }
          );
        }
      }

      function handlePanoMove(location)
      {
        ShowLatLong(myPano.getPosition());
      }
  /* ]]> */
  </script>
  
  </body>
  </html>
