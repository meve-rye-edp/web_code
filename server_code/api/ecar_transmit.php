<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
/*
 * API page for the client webview to receive the latest BMS information
 */

$response = array("success" => 0, "error" => 0, "error_msg"=>'', "json" => '');
if (isset($_POST['bms_request']) && $_POST['bms_request'] != '') {
    $db = new DB_Helper();
    $conn = $db->dbConnect();
    $ecar_id = $_POST["bms_request"]["ecar_id"];
    //on successful dB connection
    if( $conn ) {

        //retrieve baterry information 
        $bat_sql = $db->generateSQL('batteries', 1, $ecar_id);
        $bat_query = sqlsrv_query($conn, $bat_sql, array(), array( "Scrollable" => 'keyset' ));
        $row_count  = sqlsrv_num_rows($bat_query);
        if($row_count === false){
            //on no match found, send error
            $response["error"] = 1;
            $response["error_msg"] = "Query Error";
        }else{
            $row = sqlsrv_fetch_array( $bat_query, SQLSRV_FETCH_ASSOC);
            $response["success"] = 1;
            $response["json"]["battery"] = $row;
        }

        //modules 

        $mod_sql = $db->generateSQL('modules', 24, $ecar_id);
        $mod_query = sqlsrv_query($conn, $mod_sql, array(), array( "Scrollable" => 'keyset' ));
        $row_count  = sqlsrv_num_rows($mod_query);
        if($row_count === false){
            //on no match found, send error
            $response["error"] = 1;
            $response["error_msg"] = "Query Error";
        }else{
            $mod_index = 0;
            while($row = sqlsrv_fetch_array( $mod_query, SQLSRV_FETCH_ASSOC)){
                $response["success"] = 1;
                $response["json"]["modules"][$mod_index] = $row;
                $mod_index++;
            }
        }

        //locations
        $loc_sql = $db->generateSQL('locations', 1, $ecar_id);
        $loc_query = sqlsrv_query($conn, $loc_sql, array(), array( "Scrollable" => 'keyset' ));
        $row_count  = sqlsrv_num_rows($loc_query);
        if($row_count === false){
            //on no match found, send error
            $response["error"] = 1;
            $response["error_msg"] = "Query Error";
        }else{
            $row = sqlsrv_fetch_array( $loc_query, SQLSRV_FETCH_ASSOC);
            $response["success"] = 1;
            $response["json"]["locations"] = $row;
        }

    }else{
       $response["error"] = 1;
        $response["error_msg"] = "Database Connection Failed";  
    }
}else{
    $response["error"] = 1;
    $response["error_msg"] = "Access Denied";
}

echo json_encode($response);
/*
 * Helper Functions Class
 */

class DB_Helper {
    
    //Generate select statements for querying latest entries by post_time
    public function generateSQL($table, $amt, $ecar_id){
        $sql = "SELECT TOP $amt * FROM $table WHERE ECAR_ID = '$ecar_id' ORDER BY post_time DESC ";   
        return $sql;
    }
 
    public function dbConnect(){
       //Attempt server connection
      $serverName = "192.168.2.62"; //serverName\instanceName
      $connectionInfo = array( "Database"=>"MEV", "UID"=>"mev_dba", "PWD"=>"ryerson2012");
      $conn = sqlsrv_connect( $serverName, $connectionInfo);
      return $conn;
    }
    
}
?>
