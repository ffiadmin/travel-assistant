<?php
namespace FFI\TA;

require_once(dirname(dirname(__FILE__)) . "/includes/AJAX_Data_Fetch.php");

class Google_Maps_POI extends AJAX_Data_Fetch {
	public function __construct() {
	//Call the super class
		parent::__construct();
		parent::setMimeTypeHeader("application/xml");
		
	//Echo the XML
		echo "<markers>";
		$this->buildMarkerNodes();
		echo "</markers>";
	}
	
	private function buildMarkerNodes() {
		global $wpdb;
		$query = $wpdb->get_results("SELECT ffi_ta_cities.*, ffi_ta_states.Name AS StateName FROM ffi_ta_cities LEFT JOIN ffi_ta_states ON ffi_ta_cities.State = ffi_ta_states.Code");
		
		foreach($query as $item) {
			$this->addMarker($item->City . ", " . $item->State, $item->City, $item->StateName, $item->Latitude, $item->Longitude);
		}
	}
	
	private function addMarker($name, $city, $state, $lat, $lng) {
		echo "<marker name=\"" . $name . "\" city=\"" . $city . "\" state=\"" . $state . "\" lat=\"" . $lat . "\" lng=\"" . $lng . "\"/>";
	}
}
?>