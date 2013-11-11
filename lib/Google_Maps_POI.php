<?php
namespace FFI\TA;

require_once(dirname(dirname(__FILE__)) . "/lib/AJAX_Data_Fetch.php");

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
		$query = $wpdb->get_results("SELECT ffi_ta_cities.*, `Name` AS `StateName`, COALESCE(q1.FromNeeds, 0) AS `FromNeeds`, COALESCE(q2.ToNeeds, 0) AS `ToNeeds`, COALESCE(q3.FromShares, 0) AS `FromShares`, COALESCE(q4.ToShares, 0) AS `ToShares` FROM `ffi_ta_cities` LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `FromNeeds` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.FromCity = ffi_ta_cities.ID WHERE ffi_ta_need.Leaving > NOW() GROUP BY ffi_ta_cities.ID) `q1` ON ffi_ta_cities.ID = q1.CityID LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `ToNeeds` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.ToCity = ffi_ta_cities.ID WHERE ffi_ta_need.Leaving > NOW() GROUP BY ffi_ta_cities.ID) `q2` ON ffi_ta_cities.ID = q2.CityID LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `FromShares` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.FromCity = ffi_ta_cities.ID WHERE ffi_ta_share.Leaving > NOW() GROUP BY ffi_ta_cities.ID) `q3` ON ffi_ta_cities.ID = q3.CityID LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `ToShares` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.ToCity = ffi_ta_cities.ID WHERE ffi_ta_share.Leaving > NOW() GROUP BY ffi_ta_cities.ID) `q4` ON ffi_ta_cities.ID = q4.CityID LEFT JOIN `ffi_ta_states` ON ffi_ta_cities.State = ffi_ta_states.Code HAVING `FromNeeds` > 0 OR `ToNeeds` > 0 OR `FromShares` > 0 OR `ToShares` > 0");
		
		foreach($query as $item) {
			array_push($return, $this->addMarker($item->City . ", " . $item->State, $item->City, $item->StateName, $item->Latitude, $item->Longitude, $item->FromNeeds, $item->FromShares, $item->ToNeeds, $item->ToShares));
		}
		
		echo json_encode($return);
	}
	
	private function addMarker($name, $city, $state, $latitude, $longitude, $fromNeeds, $fromShares, $toNeeds, $toShares) {
		return array("name" => $name, "city" => $city, "state" => $state, "latitude" => $latitude, "longitude" => $longitude, "fromNeeds" => $fromNeeds, "fromShares" => $fromShares, "toNeeds" => $toNeeds, "toShares" => $toShares);
	}
}
?>