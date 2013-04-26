<?php

	//first we need to get WOEID depending on location
	$woeid = geocode_yahoo(43.8138,-79.4383);
	$elevation = elevation_google(43.8138,-79.4383);
	echo 'WOEID is '.$woeid.' from location 43.8138,-79.4383</br>';
	echo 'Elevation is '.$elevation.' from location 43.8138,-79.4383</br>';
	
	//use the woeid to get weather information from yahoo api (note, all info is given in xml)
	$result = file_get_contents('http://weather.yahooapis.com/forecastrss?w='.$woeid.'&u=c');
	$xml = simplexml_load_string($result);
	//parse xml
	$xml->registerXPathNamespace('yweather', 'http://xml.weather.yahoo.com/ns/rss/1.0');
	$location = $xml->channel->xpath('yweather:location');
	//if our location exists
	if(!empty($location)){
		foreach($xml->channel->item as $item){
			$current = $item->xpath('yweather:condition');
			$forecast = $item->xpath('yweather:forecast');
			$current = $current[0];
		}
		
			echo 'The day is '.$forecast[0]['day'].'</br>';
			echo 'Current temp is '.$current['temp'].'</br>';
			echo 'Current condition is '.$current['text'].'</br>';
			echo 'High temp is '.$forecast[0]['high'].'</br>';
			echo 'Low temp is '.$forecast[0]['low'].'</br>';
	}else{
			echo 'No location found';
	}
	
	function geocode_yahoo($lat, $long) {

	$url = 'http://where.yahooapis.com/geocode?location='.$lat.','.$long.'&flags=J&gflags=R&appid=zHgnBS4m';
	$data = file_get_contents($url);

	 if ($data != '') {
		 $json = json_decode($data, true);
		return $json['ResultSet']['Results'][0]['woeid'];
	 }
	return false;
	}
	
	function elevation_google($lat, $long) {

	$url = 'http://maps.googleapis.com/maps/api/elevation/json?locations='.$lat.','.$long.'&sensor=false';
	$data = file_get_contents($url);

	 if ($data != '') {
		 $json = json_decode($data, true);
		return $json["results"][0]["elevation"];
	 }
	return false;
	}
?>
