<?php
//Include the necessary scripts
	require_once("../../../lib/exceptions/Network_Connection_Error.php");
	require_once("../../../lib/processing/Proxy.php");
	
//Perform the purchase operation
	try {
		$URL = "http://maps.googleapis.com/maps/api/directions/json?origin=" . urlencode(urldecode($_GET['from'])) . "&destination=" . urlencode(urldecode($_GET['to'])) . "&sensor=false&mode=driving";
		$proxy = new FFI\TA\Proxy($URL);
		
		echo $proxy->fetch();
	} catch (FFI\TA\Network_Connection_Failed $e) {
		echo $e->getMessage();
	} catch (Exception $e) {
		echo "Unknown error: " . $e->getMessage();
	}
?>