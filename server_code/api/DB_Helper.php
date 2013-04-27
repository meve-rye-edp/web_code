<?php
/*
 * Author:  Ariel Fertman
 * Contact: arielfertman@gmail.com
 * Date:    2012
 * 
 * Database functions page
 * Holds all functions use to pull information from the database
 * Database: location_track
 *          Tables: users, locations
 *                  
 * NEED TO CHANGE TO FIT THE NEEDS OF OUR DATABASE
 */ 
/*
 * Helper Functions Class
 */

class DB_Helper {
    
      public function dbConnect(){
         //Attempt server connection
        $serverName = "192.168.2.**"; //serverName\instanceName
        $connectionInfo = array( "Database"=>"MEV", "UID"=>"mev_dba", "PWD"=>"******");
        $conn = sqlsrv_connect( $serverName, $connectionInfo);
        return $conn;
        
    }
    
    public function dBDisconnect($connection){
        sqlsrv_close($connection);
    }
    
    public function generateSQL($table, $data){
        //assoc. array: columns => value 
        $columns = "";  
        $values = "";  
        foreach ($data as $column => $value) {  
            $values .= ($values == "") ? "" : "', '";  
            $values .= $value;  
        }  
        //strip last comma
        $vals = substr($values, 0, -1);
        $sql = "INSERT INTO $table VALUES ('$vals')";   
        return $sql;
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
    
}
?>
