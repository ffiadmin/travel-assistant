<?php
/**
 * User Information class
 *
 * This class is designed to gather information about a particular 
 * user. Its capibilities include:
 *  - Get a listing of locations the user has visited.
 *  - Get a listing of trip the user has requested.
 *  - Get a listing of other trips the user has participated in.
 *  - Get a listing of trip the user has shared.
 *  - Get a statistical overview of a user's data.
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

class User {
/**
 * This method is designed to query the local database to
 * generate a listing of all cities to which the current user
 * has requested, shared, or participated in a ride. Values
 * are returned as a JSON encoded array.
 *
 * @access public
 * @return string A JSON encoded array of cities a user has visited
 * @since  1.0
 * @static
*/
	
	public static function getLocations() {
		global $wpdb;
		
	//Get the current user's ID
		$userID = get_current_user_id();
		
		if ($userID == 0) {
			return;
		}
		
	//Fetch the data
		$query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ((SELECT `FromCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.FromCity = ffi_ta_cities.ID WHERE `Person` = %d) UNION (SELECT `ToCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.ToCity = ffi_ta_cities.ID WHERE `Person` = %d) UNION (SELECT `FromCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.FromCity = ffi_ta_cities.ID WHERE `Person` = %d) UNION (SELECT `ToCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.ToCity = ffi_ta_cities.ID WHERE `Person` = %d) UNION (SELECT `FromCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM `ffi_ta_transactions` LEFT JOIN `ffi_ta_cities` ON ffi_ta_transactions.FromCity = ffi_ta_cities.ID WHERE `Initiator` = %d) UNION (SELECT `ToCity` AS `ID`, `City`, `State`, `Latitude`, `Longitude` FROM `ffi_ta_transactions` LEFT JOIN `ffi_ta_cities` ON ffi_ta_transactions.ToCity = ffi_ta_cities.ID WHERE `Initiator` = %d)) `q` GROUP BY `ID`", $userID, $userID, $userID, $userID, $userID, $userID));
		
	//Build the JSON array and echo the data
		$return = array();
	
		foreach($query as $item) {
			array_push($return,	array(
				"city"      => $item->City,
				"latitude"  => $item->Latitude,
				"longitude" => $item->Longitude,
				"name"      => $item->City . ", " . $item->State,
				"state"     => $item->State
			));
		}
		
		return json_encode($return);
	}
	
/**
 * This method will generate a list of locations where the user has
 * requested a trip.
 *
 * @access public
 * @return object A list of cities where a user has requested a trip
 * @since  1.0
 * @static
*/
	
	public static function getNeeds() {
		global $wpdb;
		
	//Get the current user's ID
		$userID = get_current_user_id();
		
		if ($userID == 0) {
			return;
		}
		
	//Fetch the data
		return $wpdb->get_results($wpdb->prepare("SELECT ffi_ta_need.ID, ffi_ta_need.Leaving, ffi_ta_need.LeavingTimeZone, ffi_ta_need.Fulfilled, ffi_ta_need.EndDate, q1.City AS `FromCity`, q1.State AS `FromState`, q1.Latitude AS `FromLatitude`, q1.Longitude AS `FromLongitude`, q2.City AS `ToCity`, q2.State AS `ToState`, q2.Latitude AS `ToLatitude`, q2.Longitude AS `ToLongitude` FROM `ffi_ta_need` LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q1` ON ffi_ta_need.FromCity = q1.ID LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q2` ON ffi_ta_need.ToCity = q2.ID WHERE `Person` = %d ORDER BY `Leaving` DESC", $userID));
	}
	
/**
 * This method will generate a list of locations where the user has
 * not requested or shared a trip, but participated in a trip by clicking
 * on either a "I Need This Ride" or "I Can Help" button.
 *
 * @access public
 * @return object A list of cities where a user has participated in a trip
 * @since  1.0
 * @static
*/
	
	public static function getOther() {
		global $wpdb;
		
	//Get the current user's ID
		$userID = get_current_user_id();
		
		if ($userID == 0) {
			return;
		}
		
	//Fetch the data
		return $wpdb->get_results($wpdb->prepare("SELECT ffi_ta_transactions.ID, ffi_ta_transactions.Type, `Person`, ffi_ta_transactions.Leaving, ffi_ta_transactions.LeavingTimeZone, ffi_ta_transactions.EndDate, q1.City AS `FromCity`, q1.State AS `FromState`, q1.Latitude AS `FromLatitude`, q1.Longitude AS `FromLongitude`, q2.City AS `ToCity`, q2.State AS `ToState`, q2.Latitude AS `ToLatitude`, q2.Longitude AS `ToLongitude` FROM `ffi_ta_transactions` LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q1` ON ffi_ta_transactions.FromCity = q1.ID LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q2` ON ffi_ta_transactions.ToCity = q2.ID LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Person` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id WHERE `meta_key` = 'first_name') AS `q3` ON ffi_ta_transactions.Poster = q3.ID WHERE `Initiator` = %d ORDER BY `Leaving` DESC", $userID));
	}
	
/**
 * This method will generate a list of locations where the user has
 * shared a trip.
 *
 * @access public
 * @return object A list of cities where a user has shared a trip
 * @since  1.0
 * @static
*/
	
	public static function getShares() {
		global $wpdb;
		
	//Get the current user's ID
		$userID = get_current_user_id();
		
		if ($userID == 0) {
			return;
		}
		
	//Fetch the data
		return $wpdb->get_results($wpdb->prepare("SELECT ffi_ta_share.ID, ffi_ta_share.Leaving, ffi_ta_share.LeavingTimeZone, ffi_ta_share.Seats, ffi_ta_share.Fulfilled, ffi_ta_share.EndDate, q1.City AS `FromCity`, q1.State AS `FromState`, q1.Latitude AS `FromLatitude`, q1.Longitude AS `FromLongitude`, q2.City AS `ToCity`, q2.State AS `ToState`, q2.Latitude AS `ToLatitude`, q2.Longitude AS `ToLongitude` FROM `ffi_ta_share` LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q1` ON ffi_ta_share.FromCity = q1.ID LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q2` ON ffi_ta_share.ToCity = q2.ID WHERE `Person` = %d ORDER BY `Leaving` DESC", $userID));
	}
	
/**
 * This method will generate a set of statistical data for
 * the current user. The data will be returned as an object
 * and will contain the following pieces of information:
 *  - Total number of rides requested.
 *  - Total number of rides shared.
 *  - Total number of rides the user has not requested or 
 *    shared, but has participated in.
 *  - The date of the next ride, either requested or shared.
 *  - The number of currently active recurring trips.
 *
 * @access public
 * @return object An object containing a set of statistical data for the current user
 * @since  1.0
 * @static
*/
	
	public static function getStats() {
		global $wpdb;
		
	//Get the current user's ID
		$userID = get_current_user_id();
		
		if ($userID == 0) {
			return;
		}
		
	//Fetch the data
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM (SELECT COUNT(*) AS `Needed` FROM `ffi_ta_need` WHERE `Person` = %d) `q1` JOIN (SELECT COUNT(*) AS `Shared` FROM `ffi_ta_share` WHERE `Person` = %d) `q2` JOIN (SELECT COUNT(*) AS `Other` FROM `ffi_ta_transactions` WHERE `Initiator` = %d) `q3` JOIN (SELECT IF (COUNT(*) = 0, 0, `Leaving`) AS `Next` FROM ((SELECT `Leaving`, `Fulfilled` FROM `ffi_ta_need` WHERE `Person` = %d AND `Leaving` > NOW() AND `Fulfilled` > 0) UNION (SELECT `Leaving`, `Fulfilled` FROM `ffi_ta_share` WHERE `Person` = %d AND `Leaving` > NOW() AND `Fulfilled` > 0) UNION (SELECT `Leaving`, '1' AS `Fulfilled` FROM `ffi_ta_transactions` WHERE `Initiator` = %d AND `Leaving` > NOW())) `q` ORDER BY `Next` ASC LIMIT 1) `q4` JOIN (SELECT SUM(`Recurring`) AS `Recurring` FROM ((SELECT COUNT(*) AS `Recurring` FROM `ffi_ta_need` WHERE `Leaving` < NOW() AND `EndDate` > NOW() AND `Fulfilled` > 0 AND `Person` = %d) UNION (SELECT COUNT(*) AS `Recurring` FROM `ffi_ta_share` WHERE `Leaving` < NOW() AND `EndDate` > NOW() AND `Fulfilled` > 0 AND `Person` = %d) UNION (SELECT COUNT(*) AS `Recurring` FROM `ffi_ta_transactions` WHERE `Leaving` < NOW() AND `EndDate` > NOW() AND `Initiator` = %d)) `q`) `q5`", $userID, $userID, $userID, $userID, $userID, $userID, $userID, $userID, $userID));
		
		return $data[0];
	}
}
?>