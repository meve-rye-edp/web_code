var date = "01-01-13";
var graph_title = "SOC Analysis on " + date;
var graph_bool = true;
var parrent_array = new Array();
var arr_data =  new Array(["6:56:22", 95] ,["13:56:22", 93] , ["16:56:22", 55]);
var soc_data = new Array(); //default values
var curr_data = new Array();
var temp_data = new Array(-4,-2,3);
var cond_data = new Array("Cloudy", "Rain", "Sunny");
var latlong = new Array([43.813334,-79.438472] , [42.813334,-79.438472], [43.813334,-79.418472]);
var data = new Object();
var ecar_id = "d9066b4c-309c-4f28-a9cc-a5df8694bc41";
var plot;

//google stuff
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var oldMarker;
var map;

$(document).ready(function() {
  $.jqplot.config.enablePlugins = true;
  init();
  init_google_maps();
    
  $('#datepicker').datepicker().on('changeDate', function(ev){
        date = $('#datepicker').attr("value");
        graph_title = "SOC Analysis on " + date;
        jsonPost();
  });


 $('#graph_btn').click(function(){
        $('#button-reset').show();
	$('#chartdiv').empty();
   plot = $.jqplot('chartdiv',  arr_data,
		{ title: graph_title,
		  axes:{
		  xaxis:{
                    renderer:$.jqplot.DateAxisRenderer, 
                    tickOptions:{formatString:'%H:%M:%S'},
                    min:arr_data[0][arr_data[0].length-1][0],
                    max:arr_data[0][0][0]
                  }
		},
		  series:[{color:'#43A8F0'}],
		 highlighter: {
			 sizeAdjust: 10,
			 tooltipLocation: 'n',
			 tooltipAxes: 'n',
			 tooltipFormatString: '<b><i><span style="color:red;">%s</span></i></b> %d%',
			 useAxesFormatters: false
		 },
		 cursor: {
			 show: true,
                         zoom:true
		 },
		 series:[{color:'#11BCF0'}]
		 
		});
   $('#button-reset').click(function() { plot.resetZoom() });
   latlonDistance();
});




//graphing clicks
    $('ul#nav li a').click(function(){
            var page = $(this).attr('href');
            if(page.substring(1,0) != "#"){
                    return true;
            }else{
                if(page == "#soc-graph"){
                        graph_bool = true;
                        graph_title = "SOC Analysis on " + date;
                        $('#soc-graph').attr('class', 'active');
                        $('#cur-graph').attr('class', '');
                }else if(page == "#curr-graph"){
                        graph_bool = false;
                        arr_data = curr_data;
                        graph_title = "Curent Analysis on " + date;
                        $('#soc-graph').attr('class', '');
                        $('#cur-graph').attr('class', 'active');
                }
                if(graph_bool){
                    arr_data = [soc_data];
                }else{
                    arr_data = [curr_data];
                }
                return false;
            }
    });
	
		
});

function init(){
        $('#no_data').hide();
	soc_outdoor = true;
	arr_data = new Array([["12:56:22", 95] ,["13:56:22", 93] , ["14:56:22", 55]]);
	graph_title = "SOC Analysis " + date;
        
}


function init_google_maps(){
	directionsDisplay = new google.maps.DirectionsRenderer();
	var myLatlng = new google.maps.LatLng(43.813334, -79.406472);
	var myOptions = {
	zoom: 15,
	center: myLatlng,
	mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map_view"), myOptions);
	marker = new google.maps.Marker({
			map:map,
			draggable:true,
			animation: google.maps.Animation.DROP,
			title: "Current Location",
			icon: "../analysis/img/logo_meve.png"
	});
	directionsDisplay.setMap(map)
}  


        
 function placeMarker(location, singleLoc) {
	marker = new google.maps.Marker({
		position: location,
		map: map,
		animation: google.maps.Animation.DROP,
		title: "Location: " + location,
		icon: "../analysis/img/logo_meve.png"
	});
	if(singleLoc){
		if (oldMarker != undefined){
			oldMarker.setMap(null);
		}
		oldMarker = marker;
	}
	map.setCenter(location);

}


 $('#chartdiv').bind('jqplotDataMouseOver',
	function (ev, seriesIndex, pointIndex, data) { 
		$('#chartdiv').attr('title', 'SOC: ' + soc_data[pointIndex][1] +'\nTemperature: ' + temp_data[pointIndex] + '\n Condition: ' + cond_data[pointIndex]);
	});
	
 $('#chartdiv').bind('jqplotDataClick',
	function (ev, seriesIndex, pointIndex, data) { 
		placeMarker(new google.maps.LatLng(latlong[pointIndex][0], latlong[pointIndex][1]), true);
	});
        
        
//post date to and receive graph data
function jsonPost(){
    data["date"] = date;
    data["ecar_id"] = ecar_id;
    var postData = {graph_req:data};
    $.ajax({
         type: 'POST',
         url: "http://dev.arcx.com/ecar/api/graph.php",
         data: postData,
         success: function(data){
            var jsonData = JSON.parse(data);
            if(jsonData["success"] == 1){
                parseGraphData(jsonData["json"]);
                $('#graph_btn').removeAttr('disabled');
                $('#no_data').hide();
                
            }else if(jsonData["error"] == 2){
               // console.log("No data for that day was found");
                $('#graph_btn').attr("disabled", "disabled");
                $('#no_data').show();
            }else{
                console.log("Error returned e: " + jsonData["error_msg"]);
                $('#graph_btn').attr("disabled", "disabled");
            }
         }
 }); 
}

function parseGraphData(g_data){
    cond_data = [];
    temp_data = [];
    latlong = [];
    soc_data = [];
    curr_data = [];
    
    for(var i = 0; i < g_data["condition"].length; i++){
        cond_data.push(g_data["condition"][i]);
        temp_data.push(g_data["temp"][i]);
        soc_data.push([g_data["time"][i],g_data["soc"][i]]);
        curr_data.push([g_data["time"][i],g_data["current"][i]]);
        latlong.push([g_data["latitude"][i],g_data["longitude"][i]]);
    }
    
    if(graph_bool){
        arr_data = [soc_data];
    }else{
        arr_data = [curr_data];
    }
    
}

function latlonDistance(){
	var lat1 = parseFloat(latlong[0][1]);
	var lat2 = parseFloat(latlong[latlong.length-1][0]);
	var lon1 = parseFloat(latlong[0][1]);
	var lon2 = parseFloat(latlong[latlong.length-1][1]);
	var R = 6371; // km
	var d = Math.acos(Math.sin(lat1)*Math.sin(lat2) + 
                  Math.cos(lat1)*Math.cos(lat2) *
                  Math.cos(lon2-lon1)) * R;
	console.log(d);
	
}