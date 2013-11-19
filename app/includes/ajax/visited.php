<?php
//Include the necessary scripts
	require_once("../../../lib/display/Trip_Info.php");
	
//Display the listing of visited locations
	FFI\TA\Trip_Info::getUserLocations();
?>