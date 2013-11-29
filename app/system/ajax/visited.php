<?php
//Include the necessary scripts
	require_once("../../../lib/display/User.php");
	
//Display the listing of visited locations
	echo FFI\TA\User::getLocations();
?>