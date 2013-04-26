<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$output = array("success" => 0, "error" => 0, "error_msg"=>'',"json" => '');

if (isset($_POST['graph_req']) && $_POST['graph_req'] != '') {
        require  $_SERVER['DOCUMENT_ROOT']."\eCAR\api\DB_Helper.php";
        $dbhelper = new DB_Helper();
        $conn = $dbhelper->dbConnect();
        $req_info = $_POST["graph_req"];

	$req_day = $req_info["date"];

	$month = substr($req_day, 0, -6);
	$day = substr($req_day, 3, -3);
	$year = "20".substr($req_day, 6);
	
	$date = $year."-".$month."-".$day;
	$date2 = $year."-".$month."-".($day + 1);

	$ecar_id = $req_info["ecar_id"];
        
	if ($conn) {
		$loc_sql = "SELECT * FROM locations WHERE ECAR_ID = '$ecar_id' AND post_time >= '$date' AND post_time < '$date2' ORDER BY post_time DESC ";
		$loc_query = sqlsrv_query($conn, $loc_sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if( $loc_query === false ) {
 		    die( print_r( sqlsrv_errors(), true));
		} 
		$row_count = sqlsrv_num_rows($loc_query);

		if ($row_count === false){	
			$output["error"] = 1;
			$output["error_msg"] = "Query error";
		} else if($row_count == 0){
                    $output["error"] = 2;
                    $output["error_msg"] = "No data for that day";
                }else{
			$mod_index = 0;
			$output["success"] = 1;
			while($row = sqlsrv_fetch_array($loc_query, SQLSRV_FETCH_ASSOC)){ 

				$output["json"]["condition"]["$mod_index"] = $row["w_condition"];
				$output["json"]["temp"]["$mod_index"] = $row["outdoor_temp"];
				$output["json"]["time"]["$mod_index"] = date_format($row["post_time"],'H:i:s');

				$output["json"]["latitude"]["$mod_index"] = $row["latitude"];
				$output["json"]["longitude"]["$mod_index"] = $row["longitude"];

				$mod_index++;
			}
		}
		
		$bat_sql = "SELECT * FROM batteries WHERE ECAR_ID = '$ecar_id' AND post_time >= '$req_day' AND post_time < '$date2' ORDER BY post_time DESC ";	
		$bat_query = sqlsrv_query($conn, $bat_sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));	
		$row_count = sqlsrv_num_rows($bat_query);

		if ($row_count === false){	
			$output["error"] = 1;
			$output["error_msg"] = "Query error";
		} else if($row_count == 0){
                    $output["error"] = 2;
                    $output["error_msg"] = "No data for that day";
                }else{
			$mod_index = 0;
			$output["success"] = 1;
			while($row = sqlsrv_fetch_array($bat_query, SQLSRV_FETCH_ASSOC)){
				
				$output["json"]["soc"]["$mod_index"] = $row["soc"];
				$output["json"]["current"]["$mod_index"] = $row["current"];
				$mod_index++;
			}
		}
	} else {
		$output["error"] = 1;
		$output["error_msg"] = "Database Connection Failed";
	}
}else{
    $output["error"] = 1;
    $output["error_msg"] = "Wrong Tag";
}  
echo json_encode($output);
	 

?>