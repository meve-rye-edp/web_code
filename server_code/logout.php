<?php

/*
 * Author:  Ariel Fertman
 * Contact: arielfertman@gmail.com
 * Date:    2012
 * 
 * Logout page (from website), unsets all session variables
 * 
 */ 
session_start();
session_destroy();
header("Location: login.php");
?>
