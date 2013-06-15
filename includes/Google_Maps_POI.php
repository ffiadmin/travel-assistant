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
		$query = $wpdb->get_results("SELECT * FROM (SELECT ffi_ta_cities.*, ffi_ta_states.Name AS `StateName`, COALESCE(q1.Needs, 0) AS `Needs`, COALESCE(q2.Shares, 0) AS `Shares` FROM `ffi_ta_cities` LEFT JOIN `ffi_ta_states` ON ffi_ta_cities.State = ffi_ta_states.Code LEFT JOIN (SELECT `City`, COUNT(`City`) AS `Needs` FROM `ffi_ta_need` GROUP BY `City`) `q1` ON ffi_ta_cities.ID = q1.City LEFT JOIN (SELECT `City`, COUNT(`City`) AS `Shares` FROM `ffi_ta_share` GROUP BY `City`) `q2` ON ffi_ta_cities.ID = q2.City) `query` WHERE `Needs` > 0 OR `Shares` > 0");
		
		foreach($query as $item) {
			array_push($return, $this->addMarker($item->City . ", " . $item->State, $item->City, $item->StateName, $item->Latitude, $item->Longitude, $item->Needs, $item->Shares));
		}
		
		echo json_encode($return);
	}
	
	private function addMarker($name, $city, $state, $latitude, $longitude, $needs, $shares) {
		return array("name" => $name, "city" => $city, "state" => $state, "latitude" => $latitude, "longitude" => $longitude, "needs" => $needs, "shares" => $shares);
	}
}
?>