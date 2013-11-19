<?php
/**
 * Trip Information class
 *
 * This class is designed to gather information about a particular 
 * trip. This trip can be either a trip which is being requested
 * or a trip which is available.
 * 
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.display
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-blog-header.php");

class Trip_Info {
	public static function getNeeded($tripID) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT `Leaving`, `LeavingTimeZone`, `FromCity` AS `FromCityID`, `City` AS `FromCity`, `State` AS `FromState`, `ToCityID`, `ToCity`, `ToState`, `MalesPresent`, `FemalesPresent`, `MalesPresent` + `FemalesPresent` + 1 AS `Total`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `RequesteeID`, `Requestee` FROM (SELECT ffi_ta_need.ID, `Leaving`, `LeavingTimeZone`, `FromCity`, `ToCity` AS `ToCityID`, `City` AS `ToCity`, `State` AS `ToState`, `MalesPresent`, `FemalesPresent`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `Person` AS `RequesteeID`, `Requestee` FROM `ffi_ta_need` LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Requestee` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_need.Person = users.ID LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.ToCity = ffi_ta_cities.ID) `q` LEFT JOIN `ffi_ta_cities` ON q.FromCity = ffi_ta_cities.ID WHERE q.ID = %d", $tripID));
	}
	
	public static function getAvailable($tripID) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT `Leaving`, `LeavingTimeZone`, `FromCity` AS `FromCityID`, `City` AS `FromCity`, `State` AS `FromState`, `ToCityID`, `ToCity`, `ToState`, `Seats`, `MalesPresent`, `FemalesPresent`, `MalesPresent` + `FemalesPresent` + 1 AS `Total`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `SharerID`, `Sharer` FROM (SELECT ffi_ta_share.ID, `Leaving`, `LeavingTimeZone`, `FromCity`, `ToCity` AS `ToCityID`, `City` AS `ToCity`, `State` AS `ToState`, `Seats`, `MalesPresent`, `FemalesPresent`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `Person` AS `SharerID`, `Sharer` FROM `ffi_ta_share` LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Sharer` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_share.Person = users.ID LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.ToCity = ffi_ta_cities.ID) `q` LEFT JOIN `ffi_ta_cities` ON q.FromCity = ffi_ta_cities.ID WHERE q.ID = %d", $tripID));
	}
	
/**
 * This method will generate a set of statistical data for
 * the current user. The data will be returned as an object
 * and will contain the following pieces of information:
 *  - Total number of rides requested
 *  - Total number of rides shared
 *  - The date of the next ride, either requested or shared
 *  - The number of currently active recurring trips
 *
 * @access public
 * @return object<mixed> An object containing a set of statistical data for the current user
 * @since  1.0
 * @static
*/
	
	public static function getUserStats() {
		global $wpdb;
		
	//Get the current user's ID
		$userID = get_current_user_id();
		
		if ($userID == 0) {
			return;
		}
		
	//Fetch the data
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM (SELECT COUNT(*) AS `Needed` FROM `ffi_ta_need` WHERE `Person` = %d) `q1` JOIN (SELECT COUNT(*) AS `Shared` FROM `ffi_ta_share` WHERE `Person` = %d) `q2` JOIN (SELECT IF (COUNT(*) = 0, 0, `Leaving`) AS `Next` FROM ((SELECT `Leaving` FROM `ffi_ta_need` WHERE `Person` = %d AND `Leaving` > NOW()) UNION (SELECT `Leaving` FROM `ffi_ta_share` WHERE `Person` = %d AND `Leaving` > NOW())) `q` ORDER BY `Next` ASC LIMIT 1) `q3` JOIN (SELECT SUM(`Recurring`) AS `Recurring` FROM ((SELECT COUNT(*) AS `Recurring` FROM `ffi_ta_need` WHERE `Leaving` < NOW() AND `EndDate` > NOW() AND Fulfilled > 0 AND `Person` = %d) UNION (SELECT COUNT(*) AS `Recurring` FROM `ffi_ta_share` WHERE `Leaving` < NOW() AND `EndDate` > NOW() AND Fulfilled > 0 AND `Person` = %d)) `q`) `q4`", $userID, $userID, $userID, $userID, $userID, $userID));
		
		return $data[0];
	}
	
/**
 * This method is designed to query the local database to
 * generate a listing of all cities to which the current user
 * has requested or shared a ride. Values are echoed as a
 * JSON encoded array.
 *
 * @access public
 * @return void
 * @since  1.0
 * @static
*/
	
	public static function getUserLocations() {
		global $wpdb;
		
	//Get the current user's ID
		$userID = get_current_user_id();
		
		if ($userID == 0) {
			return;
		}
		
	//Fetch the data
		$query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ((SELECT `FromCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM ffi_ta_need LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.FromCity = ffi_ta_cities.ID WHERE `Person` = %d) UNION (SELECT `ToCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM ffi_ta_need LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.ToCity = ffi_ta_cities.ID WHERE `Person` = %d) UNION (SELECT `FromCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM ffi_ta_share LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.FromCity = ffi_ta_cities.ID WHERE `Person` = %d) UNION (SELECT `ToCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM ffi_ta_share LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.ToCity = ffi_ta_cities.ID WHERE `Person` = %d)) `q` GROUP BY `ID`", $userID, $userID, $userID, $userID));
		
	//Build the JSON array and echo the data
		$return = array();
	
		foreach($query as $item) {
			array_push($return,	array(
				"city"       => $item->City,
				"latitude"   => $item->Latitude,
				"longitude"  => $item->Longitude,
				"name"       => $item->City . ", " . $item->State,
				"state"      => $item->State
			));
		}
		
		echo json_encode($return);
	}
	
/**
 * This method is designed to query the local database to
 * generate a listing of all cities which has at least one
 * ride requested or available going to or from it. All 
 * available information about the city will be provided, as
 * such as the number of rides, city name, state, and 
 * coordinates. Values are echoed as a JSON encoded array.
 *
 * @access public
 * @return void
 * @since  1.0
 * @static
*/
	
	public static function getOverview() {
		global $wpdb;
		
	//Fetch the data
		$query = $wpdb->get_results("SELECT ffi_ta_cities.*, `Name` AS `StateName`, COALESCE(q1.FromNeeds, 0) AS `FromNeeds`, COALESCE(q2.ToNeeds, 0) AS `ToNeeds`, COALESCE(q3.FromShares, 0) AS `FromShares`, COALESCE(q4.ToShares, 0) AS `ToShares` FROM `ffi_ta_cities` LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `FromNeeds` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.FromCity = ffi_ta_cities.ID WHERE (ffi_ta_need.Leaving > NOW() AND ffi_ta_need.Fulfilled = 0) OR (ffi_ta_need.Leaving <= NOW() AND ffi_ta_need.EndDate > NOW() AND ffi_ta_need.Fulfilled = 0) GROUP BY ffi_ta_cities.ID) `q1` ON ffi_ta_cities.ID = q1.CityID LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `ToNeeds` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.ToCity = ffi_ta_cities.ID WHERE (ffi_ta_need.Leaving > NOW() AND ffi_ta_need.Fulfilled = 0) OR (ffi_ta_need.Leaving <= NOW() AND ffi_ta_need.EndDate > NOW() AND ffi_ta_need.Fulfilled = 0) GROUP BY ffi_ta_cities.ID) `q2` ON ffi_ta_cities.ID = q2.CityID LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `FromShares` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.FromCity = ffi_ta_cities.ID WHERE (ffi_ta_share.Leaving > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) OR (ffi_ta_share.Leaving <= NOW() AND ffi_ta_share.EndDate > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) GROUP BY ffi_ta_cities.ID) `q3` ON ffi_ta_cities.ID = q3.CityID LEFT JOIN (SELECT ffi_ta_cities.ID AS `CityID`, COUNT(ffi_ta_cities.ID) AS `ToShares` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.ToCity = ffi_ta_cities.ID WHERE (ffi_ta_share.Leaving > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) OR (ffi_ta_share.Leaving <= NOW() AND ffi_ta_share.EndDate > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) GROUP BY ffi_ta_cities.ID) `q4` ON ffi_ta_cities.ID = q4.CityID LEFT JOIN `ffi_ta_states` ON ffi_ta_cities.State = ffi_ta_states.Code HAVING `FromNeeds` > 0 OR `ToNeeds` > 0 OR `FromShares` > 0 OR `ToShares` > 0");
		
	//Build the JSON array and echo the data
		$return = array();
	
		foreach($query as $item) {
			array_push($return,	array(
				"city"       => $item->City,
				"fromNeeds"  => $item->FromNeeds,
				"fromShares" => $item->FromShares,
				"latitude"   => $item->Latitude,
				"longitude"  => $item->Longitude,
				"name"       => $item->City . ", " . $item->State,
				"state"      => $item->StateName,
				"toNeeds"    => $item->ToNeeds,
				"toShares"   => $item->ToShares
			));
		}
		
		echo json_encode($return);
	}
	
/**
 * Return an array of the total number of trips which are needed
 * and available.
 * 
 * @access public
 * @return array<int> An array of available and needed trips
 * @since  1.0
 * @static
*/
	
	public static function getTotals() {
		global $wpdb;
			
	//Fetch the data from the database
		$totals = $wpdb->get_results("SELECT COUNT(ffi_ta_need.ID) AS Need, (SELECT COUNT(ffi_ta_share.ID) FROM ffi_ta_share WHERE (ffi_ta_share.Leaving > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) OR (ffi_ta_share.Leaving <= NOW() AND ffi_ta_share.EndDate > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled)) AS Shares FROM ffi_ta_need WHERE (ffi_ta_need.Leaving > NOW() AND ffi_ta_need.Fulfilled = 0) OR (ffi_ta_need.Leaving <= NOW() AND ffi_ta_need.EndDate > NOW() AND ffi_ta_need.Fulfilled = 0)");
		
		return array("needs" => $totals[0]->Need, "shares" => $totals[0]->Shares);
	}
}
?>