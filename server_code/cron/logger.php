<?php

/*
 * This logger allows the client side application send json data logs to act 
 * as a console.log function that records all information in a log file
 */
error_reporting(E_ALL);
ini_set('display_errors',1);
$date = date('Y-m-d H:i:s');
$logpath = "C:\inetpub\wwwroot\eCAR\cron\logs\log.txt";
$response = array("success" => 0, "error" => 0);


if (isset($_POST['log']) && $_POST['log'] != '') {
    $json = $_POST["log"];
    $response["success"] = 1;
    error_log($date.": ".$json."\n", 3, $logpath);
}else{
    $response["error"] = 1;
}

echo json_encode($response);

?>
