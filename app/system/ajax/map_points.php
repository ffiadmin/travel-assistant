<?php
//Include the necessary scripts
	require_once("../../../lib/display/Trip_Info.php");
	
//Display the listing of available trips
	echo FFI\TA\Trip_Info::getOverview();
?>