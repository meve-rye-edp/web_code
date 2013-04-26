<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);
/*
 * API to insert data from e-car webpage to database
 * Only inserting is available on this page
 * Car must identifiy itself with its assigned GUID
 * Once authenticated a database connection is established
 * The page will then insert all the information processed
 * The page will then close the dB connection
 * The response from the sever will be an assoiative array with success and error values
 * Success 1 = Successfuly inserted all data
 * Error 1 = Incorrect post tag submitted.
 * Error 2 = Connection to database could not be established.
 * Error 3 = ECar Guid failed Authorization.
 * Error 4 = Unable to insert data into database.
 * Error 5 = No location found for the WOEID.
*/


if (isset($_POST['bms']) && $_POST['bms'] != '') {
    require  $_SERVER['DOCUMENT_ROOT']."\eCAR\api\DB_Helper.php";
    $response = array("success" => 0, "error" => 0, "error_msg"=>'', "entries"=>0);
    $dbhelper = new DB_Helper();
    
    //json decoding and guid extraction 
    $json = $_POST["bms"];
    //Attempt server connection
    $conn = $dbhelper->dbConnect();

    if( $conn ) { //on succesful connection   
        //verify guid from post
      
            $bat_guid = $json[0]["guid"];
            
            $sql_auth_car = "SELECT * FROM cars WHERE ECAR_ID = '$bat_guid'";
            $auth_car = sqlsrv_query($conn, $sql_auth_car, array(), array( "Scrollable" => 'keyset' ));
            $row_count  = sqlsrv_num_rows($auth_car);
            if($row_count === false){
                //on no match found, send error
                $response["error"] = 3;
                $response["error_msg"] = "ECar Guid failed Authorization";
            }else{
                $json_index = 0;
                for ($json_index=0; $json_index < sizeof($json); $json_index++) {
                    /*
                     * Server connection and car auth have passed, now to get woied using longitude and latitude
                     * Then get local weather information from yahoo
                     * Finally go through each table and parse the json results accordingly
                     */
                    $woeid = $dbhelper->geocode_yahoo($json[$json_index]["general"]["lat"],$json[$json_index]["general"]["lon"]);
                    $result = file_get_contents('http://weather.yahooapis.com/forecastrss?w='.$woeid.'&u=c');
                    //parse yahoo with simplexml 
                    $xml = simplexml_load_string($result);
                    $xml->registerXPathNamespace('yweather', 'http://xml.weather.yahoo.com/ns/rss/1.0');
                    $location = $xml->channel->xpath('yweather:location');
                    if(!empty($location)){
                            foreach($xml->channel->item as $item){
                                    $current = $item->xpath('yweather:condition');
                                    $forecast = $item->xpath('yweather:forecast');
                                    $current = $current[0];
                            }   
                            //location table insert SQL
                            $loc_array = array(
                                "ECAR_ID" => $bat_guid,
                                "longitude" => $json[$json_index]["general"]["lon"],
                                "latitude" => $json[$json_index]["general"]["lat"],
                                "outdoor_temp" => $current['temp'],
                                "w_condition" => $current['text'],
                                "speed" => $json[$json_index]["general"]["speed"],
                                "post_time" => $json[$json_index]["post_time"].":000" 
                            );
                            $sql_loc_table = $dbhelper->generateSQL("locations", $loc_array);
                            $result_loc_table = sqlsrv_query($conn, $sql_loc_table);

                    }else{
                         $response["error"] = 5;
                         $response["error_msg"] = "No location found for the WOEID.";
                    }

                    //battery table insert SQL
                     $bat_array = array(
                         "ECAR_ID" => $bat_guid,
                         "soc" => $json[$json_index]["battery"]["soc"],
                         "mode" => $json[$json_index]["battery"]["mode"],
                         "state" => $json[$json_index]["battery"]["state"],
                         "faultmap" => $json[$json_index]["battery"]["faultMap"],
                         "vstate" => $json[$json_index]["battery"]["vState"],
                         "voltage" => $json[$json_index]["battery"]["voltage"],
                         "vCellMin" => $json[$json_index]["battery"]["vCellMin"],
                         "vCellMax" => $json[$json_index]["battery"]["vCellMax"],
                         "balancing" => $json[$json_index]["battery"]["balancing"],
                         "[current]" => $json[$json_index]["battery"]["current"],
                         "maxCurrentOut" => $json[$json_index]["battery"]["maxCurrentOut"],
                         "maxCurrentIn" => $json[$json_index]["battery"]["maxCurrentIn"],
                         "tState" => $json[$json_index]["battery"]["tState"],
                         "cTempMin" => $json[$json_index]["battery"]["cTempMin"],
                         "cTempMax" => $json[$json_index]["battery"]["cTempMax"],
                         "pTempMin" => $json[$json_index]["battery"]["pTempMin"],
                         "pTempMax" => $json[$json_index]["battery"]["pTempMax"],
                         "discharge" => $json[$json_index]["battery"]["discharge"],
                         "sensors" => $json[$json_index]["battery"]["sensors"],
                         "post_time" => $json[$json_index]["post_time"].":000"   
                        );
                    $sql_bat_table = $dbhelper->generateSQL("batteries", $bat_array);
                    $result_bat_table = sqlsrv_query($conn, $sql_bat_table);

                    if(!$result_bat_table || !$result_loc_table){
                        $response["error"] = 4;
                        $response["error_msg"] = "Unable to insert data into database index";
                    }else{
                        $response["success"] = 1;
                    }
                } //end json index for loop
            } //end if statement 
            
            $dbhelper->dBDisconnect($conn);

    }else{
         $response["error"] = 2;
         $response["error_msg"] = "Connection to database could not be established.";
    }
}else{
    $response["error"] = 1;
    $response["error_msg"] = "Incorrect post tag submitted.";
}
echo json_encode($response);

?>
