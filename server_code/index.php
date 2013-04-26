<?php
session_start();
if(isset($_SESSION['logged-in'])){
    
}else{
    header("Location: logout.php");
}
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>MEVE</title>
    
</head>
	<link rel="stylesheet" href="scripts/themes/meve_theme.min.css" />
	<link rel="stylesheet" href="scripts/jquery.mobile.structure-1.2.0.min.css" />
	<script src="scripts/jquery-1.8.2.min.js"></script>
    <script src="scripts/jquery.mobile-1.2.0.min.js"></script>
    <script src="scripts/general.js"></script>
    <style>
        
        #navbar{
            background-color: #000000 !important;
        }
		
		body {
    font-size: 14px;
}

@media screen and (-webkit-device-pixel-ratio: 0.75) {
    body {font-size: 10.5px;}
}
@media screen and (-webkit-device-pixel-ratio: 1.0) {desktop browsers
    body {font-size: 14px;}
}
@media screen and (-webkit-device-pixel-ratio: 1.5) {e.g. Google Nexus S (Samsung Galaxy S)
    body {font-size: 16px;}
}
@media screen and (-webkit-device-pixel-ratio: 2.0) {e.g. iPad
    body {font-size: 18px;}
}
        
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            ecarID = '<?php echo $_SESSION['ecar_id']; ?>';
            ecarName = '<?php echo $_SESSION['ecar_name']; ?>';
            console.log("Ecar id is: " + ecarID + " and ecar name is " + ecarName);
            transmitECarInfo();
        });
    </script>
<body>
    <!--    MAIN PAGE-->
	<div data-role="page"> 
		<div data-role="header" >
			<div data-role="navbar" >
				<ul id="nav">
					<li ><a href="mainview" data-role="button" data-icon="home" data-transition="fade">Main</a></li>
					<li ><a href="batview" data-role="button" data-icon="gear" data-transition="fade">Battery</a></li>
					<li ><a href="modview" data-role="button" data-icon="gear" data-transition="fade">Modules</a></li>
					<li ><a href="locview" data-role="button" data-icon="search" data-transition="fade">Locations</a></li>
				</ul>
			</div>
		</div>
           
          <div id="content" data-role="content"></div>  
       </div>
    <link href="styles/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
</body>

</html>