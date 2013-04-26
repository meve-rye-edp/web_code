<?php
?>

<script src="http://dev.arcx.com/ecar/scripts/jquery.percentageloader-0.1.min.js"></script>

    <style>
      #topLoader {
        width: 160px;
        height: 160px;
        margin-bottom: 32px;
         margin-left:auto;
        margin-right:auto;
        display: inline-block;
      }
      
      #container {
        margin-left:auto;
        margin-right:auto;
        display: inline-block;
      }
      
      #bms_text{
          font-weight:bold;color:#0F97A6;letter-spacing:1pt;word-spacing:2pt;font-size:16px;text-align:left;font-family:arial, helvetica, sans-serif;line-height:1;
          text-align: center;
		  font-size: 25px;
      }
      
      
      #menu_title{
          font-weight:bold;color:#ffffff;letter-spacing:1pt;word-spacing:2pt;font-size:16px;text-align:left;font-family:arial, helvetica, sans-serif;line-height:1;
          text-align: center;
      }
      
     #menu {text-align:center;}
    #menu div 
    {
        display:block; margin-left:auto;
        margin-right:auto;
    }
    .btm_right_settings{
        position:fixed;
        right:5;
        top:10%;
    }
    
    #popupPanel-popup {
    right: 0 !important;
    left: auto !important;
}
#popupPanel {
    width: 225px;
    border: 2px solid #000;
    border-right: none;
    background: rgba(0,0,0,0.7);
    height: 100%;
    margin: -1px 0;
}

.ui-dialog-background {
	opacity: 0.5;
	display: block !important;
	-webkit-transition: opacity 0.5s ease-in;
}
 
.ui-dialog-background.pop.in {
	opacity: 1;
	-webkit-transition: opacity 0.5s ease-in;
}
 
.ui-dialog {
	min-height: 100% !important;
	background: transparent !important;
}
      

    </style>
  

<script>
           var soc = 0.9;
$(document).ready(function() {
  var $topLoader = $("#topLoader").percentageLoader({width: 160, height: 160, controllable : true, progress : localStorage.getItem("soc")/100 , onProgressUpdate : function(val) {
      $topLoader.setValue(Math.round(val * 100.0));
      
	  
	  init();
	  
	  $( "#popupPanel-test" ).on({
		   popupafteropen: function(event, ui) {
			console.log("Popop opened"); 
		   }
		});
    
    }});

  var topLoaderRunning = false;
  
    $( "#popupPanel" ).on({
    popupbeforeposition: function() {
        var h = $( window ).height();

        $( "#popupPanel" ).css( "height", h );
    }
    
});

   //handle menu clicks
    $('#popup button').click(function(){
            var brn = $(this);
            alert(btn);
            //need to prevent default action
            return false;
    });


 });     
        
    function setBatteryCharge(percent){
                    if (topLoaderRunning) {
          return;
        }
        topLoaderRunning = true;
        $topLoader.setProgress(0);
        var kb = 0;
        var totalKb = 999;

        var animateFunc = function() {
          kb += 17;
          $topLoader.setProgress(kb / totalKb);

          if (kb < totalKb) {
            setTimeout(animateFunc, 25);
          } else {
            topLoaderRunning = false;
          }
        }

        setTimeout(animateFunc, 25);
    }
	
      function init(){
		console.log("Initalizing google maps");
		directionsDisplay = new google.maps.DirectionsRenderer();
		var myLatlng = new google.maps.LatLng(localStorage.getItem("latitude"), localStorage.getItem("longitude"));
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
		directionsDisplay.setMap(map);
}           
 
      </script>

<br/>
<div id="container">   
<div id="bms_text">Current State of Charge</div><br>
   <div id="topLoader"></div>
</div>

<!-- -->

<!--<a id="home-dialog" href="content/dialog/get-home.html"  data-role="button" data-rel="dialog" data-transition="pop">Can I get home?</a>-->
<a href="https://maps.google.ca/maps/ms?ie=UTF8&oe=UTF8&msa=0&msid=209444310717577622142.0004c2c121933cc5a9993" data-role="button" data-rel="dialog">Nearest EV Charge Station?</a>
<p>Link Provided by Plug'n Drive Ontario</p>
<!--<a href="content/dialog/get-home.html" data-role="button" data-rel="dialog">Favorite Locations</a>-->


<a href="#popupPanel" data-role="button" data-rel="popup" data-transition="slide" data-position-to="window" data-role="button" data-iconpos="notext" data-icon="gear" class="btm_right_settings">Open panel</a>
			

<div data-role="popup" id="popupPanel"  data-theme="none" data-shadow="true" data-tolerance="0,0">
        <li data-swatch="a" class="ui-li ui-li-divider ui-btn ui-bar-a ui-btn-up-undefined" data-role="list-divider" data-form="ui-bar-a"><div id="menu_title">Options</div></li>
        <button data-theme="a" onClick="getJSONInfo()">Manual Update</button>
        <button data-theme="a" onClick="notifySOC(20)">Test Notify</button>
        <button data-theme="a"  onClick="clearAndroidCache()">Clear Cache</button>
        <button data-theme="a"  onClick="clearLocalStorage()">Clear Local Storage</button>
        <button data-theme="a"><a href="logout.php" style="color:white; text-decoration: none;"> Logout</a></button>
</div>

<!--<a href="#popupPanel-test" data-rel="popup" data-position-to="window">Position to window</a>-->

<div data-role="popup" id="popupPanel-test">
	<p>I am positioned to the window.</p>
	<div id="map_view"  style="width:100%; height:300px;">Initializing...</div>
</div>




 

	