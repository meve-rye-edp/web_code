<?php

?>
<script type="text/javascript">
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var oldMarker;
    var map;
    
        $("document").ready(function () {
                init();
        });
        function init(){
                directionsDisplay = new google.maps.DirectionsRenderer();
                var myLatlng = new google.maps.LatLng(localStorage.getItem("latitude"), localStorage.getItem("longitude"));
				//var myLatlng = new google.maps.LatLng(43.6439182, -79.3876406);
                var myOptions = {
                zoom: 14,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById("map_view"), myOptions);
                marker = new google.maps.Marker({
                        map:map,
                        draggable:true,
                        animation: google.maps.Animation.DROP,
                        position: myLatlng,
                        title: "Current Location"
                });
                directionsDisplay.setMap(map)
        }  
        
         function placeMarker(location, singleLoc) {
            marker = new google.maps.Marker({
                position: location,
                map: map,
                animation: google.maps.Animation.DROP,
                title: "Location: " + location
            });
            if(singleLoc){
                if (oldMarker != undefined){
                    oldMarker.setMap(null);
                }
                oldMarker = marker;
            }
            map.setCenter(location);

        }
        
        function last24Hours() {
            var start = new google.maps.LatLng(localStorage.getItem("latitude"), localStorage.getItem("longitude"));
            var end = new google.maps.LatLng(43.813334, -79.438470);
            var request = {
              origin:start,
              destination:end,
              travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, function(result, status) {
              if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
              }
            });
        }
        
        var testLocationsLatitude = [43.813334, 43.815554, 43.823334, 43.823359, 43.843672];
        var testLocationsLongitude = [-79.438472, -79.438490, -79.438470, -79.431470, -79.438638];
        function test(){
            console.log("Dropping Locations")
            for (var i = 0; i < testLocationsLongitude.length; i++) {
                console.log("Location: " + testLocationsLatitude[i]+ " / " +testLocationsLongitude[i]);
                placeMarker(new google.maps.LatLng(testLocationsLatitude[i], testLocationsLongitude[i]), false)
            }
            
        }
        
</script>
<!--
<div data-role="button" onClick="init()">Latest Only</div>
<div data-role="button" onClick="test()">Last 24 Hours</div>-->
<div id="map_view"  style="width:100%; height:480px;">Initializing...</div>


