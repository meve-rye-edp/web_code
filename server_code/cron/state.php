<?php 

//error_reporting(E_ALL);
//ini_set('display_errors',1);
$logpath = "C:\inetpub\wwwroot\eCAR\cron\state\state.json";
$response = array("success" => 0, "error" => 0, "error_msg"=>'');

if (isset($_POST['json']) && $_POST['json'] != '') {
    //json decoding and guid extraction 
    $json = $_POST['json'];
    $fp = fopen($logpath, 'w');
    if($fp){
        fwrite($fp, $json);
        fclose($fp);
        $response["success"] = 1;
    }else{
        $response["error"] = 1;
        $response["error_msg"] = "Unable to open file";
    }
    
}else{
    $response["error"] = 1;
    $response["error_msg"] = "Wrong tag.";
}
echo json_encode($response);

