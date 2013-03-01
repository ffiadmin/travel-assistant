<?php
namespace FFI\TA;

require_once(dirname(dirname(__FILE__)) . "/includes/AJAX_Data_Fetch.php");

class Google_Maps_POI extends AJAX_Data_Fetch {
	public function __construct() {
	//Call the super class
		parent::__construct();
		parent::setMimeTypeHeader("application/json");
		
	//Echo the JSON
		$this->buildMarkers();
	}
	
	private function buildMarkers() {
		global $wpdb;
		$return = array();
		$query = $wpdb->get_results("SELECT ffi_ta_cities.*, ffi_ta_states.Name AS StateName FROM ffi_ta_cities LEFT JOIN ffi_ta_states ON ffi_ta_cities.State = ffi_ta_states.Code");
		
		foreach($query as $item) {
			array_push($return, $this->addMarker($item->City . ", " . $item->State, $item->City, $item->StateName, $item->Latitude, $item->Longitude));
		}
		
		echo json_encode($return);
	}
	
	private function addMarker($name, $city, $state, $latitude, $longitude) {
		return array("name" => $name, "city" => $city, "state" => $state, "latitude" => $latitude, "longitude" => $longitude);
	}
}
?>