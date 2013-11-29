<?php
//Include the necessary scripts
	require_once("../../../lib/exceptions/Login_Failed.php");
	require_once("../../../lib/exceptions/Mandrill_Send_Failed.php");
	require_once("../../../lib/exceptions/Network_Connection_Error.php");
	require_once("../../../lib/exceptions/Validation_Failed.php");
	require_once("../../../lib/processing/Trip.php");

//Perform the request operation
	try {
		new FFI\TA\Trip();
		echo "success";
	} catch (FFI\TA\Login_Failed $e) {
		echo $e->getMessage();
	} catch (FFI\TA\Validation_Failed $e) {
		echo $e->getMessage();
	} catch (FFI\TA\Network_Connection_Error $e) {
		echo $e->getMessage();
	} catch (FFI\TA\Mandrill_Send_Failed $e) {
		echo $e->getMessage();
	} catch (Exception $e) {
		echo "Unknown error: " . $e->getMessage();
	}
?>