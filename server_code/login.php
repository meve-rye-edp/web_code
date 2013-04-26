<?php
$error = "";    
$username = "";  
$password = ""; 
//check to see if they've submitted the login form 
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])){
        $username = $_COOKIE['username'];
        $password = $_COOKIE['password'];
    }
    
if(isset($_POST['submit-login'])) {  
    $db = new DB_Helper();
    $conn = $db->dbConnect();
    $username = $_POST['uid_r'];  
    $password = $_POST['pwd_r'];  
   
    if( $conn ) { //on succesful connection   
        //verify guid from post
        if($db->authenticate($conn, $username, $password)){
            //if remember me is checked
             if (isset($_POST['rememberme'])) {
                /* Set cookie to last 1 year */
                setcookie('username', $_POST['uid_r'], time()+60*60*24*365);
                setcookie('password', $_POST['pwd_r'], time()+60*60*24*365);
            } else {
                /* Cookie expires when browser closes */
                setcookie('username', $_POST['uid_r'], false);
                setcookie('password', $_POST['pwd_r'], false);
            }
            
            //successful login, redirect them to appropriate page
            session_start();
            $_SESSION['logged-in'] = true;
            $_SESSION['username'] = $username;
            $car_arr = $db->getECarInfo($conn, $username); 
            if($car_arr != false){
                $_SESSION['ecar_id'] = $car_arr["ECAR_ID"];
                $_SESSION['ecar_name'] = $car_arr["ecar_name"];
            }else{
                $_SESSION['ecar_id'] = "Did not work";
            }
            header("Location: index.php");
        }else{
            $error = "Incorrect username or password. Please try again."; 
        }
    }else{
        $error = "Unable to establish database connection, please come back later."; 
    }
}



class DB_Helper {
    
    public function authenticate($conn, $user, $pass){
        $sql = "SELECT * FROM users WHERE username = '$user'";
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => 'keyset' ));
        // check for result
        $row_count  = sqlsrv_num_rows($result);
        if ($row_count === false) {
           return false;
        } else {
            $result = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            $encrypted_password = $result['password_encrypted'];
            // check for password equality
            if ($encrypted_password == md5($pass)) {
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function getECarInfo($conn, $user){
        $sql = "SELECT * FROM users WHERE username = '$user'";
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => 'keyset' ));
        // check for result
        $row_count  = sqlsrv_num_rows($result);
        if ($row_count === false) {
           return false;
        } else {
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            $user_id = $row["USER_ID"];
            $sql = "SELECT * FROM cars WHERE USER_ID = '$user_id'";
            $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => 'keyset' ));
            // check for result
            $row_count  = sqlsrv_num_rows($result);
            if ($row_count === false) {
               return false;
            } else {
                return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            }
 
        }
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
<html> 
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="height=device-height, initial-scale=1"> 	
	<title>Mobile Home</title> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.css" />
	<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.js"></script>
</head> 

<style type="text/css">
.ui-page {
    height:100%;
}

#loginform{
	padding: 25px;
}
#username_field, #password_field, #password_field_plain{
	width:100%;
}
</style>

<body> 

<div data-role="page"> 
	<div data-role="header"> 
		<h1>MEVE Login</h1> 
	</div> 
	<div align="center" data-role="content">
            <form id="loginform" action="login.php" method="POST" data-ajax="false">
                                <input id="username_field" name="uid_r" Placeholder="Username" type="text" autocorrect="off" autocapitalize="off" value="<?php
                                    if($username != "")  {  
                                            echo $username; 
                                        }
                                ?>">
                                <input id="password_field" name="pwd_r" Placeholder="Password" type="password" autocomplete="off" autocorrect="off" autocapitalize="off" value="<?php
                                    if($password != "")  {  
                                            echo $password; 
                                        }
                                ?>">
                                <fieldset data-role="controlgroup">
                                        <input type="checkbox" id="remember_me" name="rememberme">
                                        <label for="remember_me">Remember me?</label>
                                </fieldset>
                        <input type="submit" value="Login" name="submit-login" />
                        <?php
                            if($error != "")  {  
                                echo '<div class="errorMsg">'.$error.'</div>'; 
                            }  
                        ?>
                </form>
                
        </div>

</body>
</html>