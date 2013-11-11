<?php
/**
 * City details information class
 *
 * This class is used to fetch data from the MySQL database for 
 * information regarding cities. Some of the capibilities of
 * this class includes:
 *  - fetch a listing of cities within a state with the number
 *    of needed or available rides
 * 
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.display
 * @since     1.0
*/

namespace FFI\TA;

class City {
/**
 * This function will return a listing of origin cities sorted
 * alphabetically with the number of rides needed or available,
 * when given the URL of the desired state.
 *
 * @access public
 * @param  string $stateURL     The URL of the state in which to fetch the listing of cities
 * @return object               The Wordpress array of objects returned from the database query
 * @since  1.0
 * @static
*/

	public static function getOriginCities($stateURL) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT `City`, `StateName`, `Code`, REPLACE(LOWER(`StateName`), ' ', '-') AS `URL`, `Image`, `District`, `Latitude`, `Longitude`, `Needs`, `Shares` FROM (SELECT ffi_ta_cities.*, ffi_ta_states.Name AS `StateName`, ffi_ta_states.Code, ffi_ta_states.Image, ffi_ta_states.District, COALESCE(q1.Needs, 0) AS `Needs`, COALESCE(q2.Shares, 0) AS `Shares` FROM `ffi_ta_cities` LEFT JOIN `ffi_ta_states` ON ffi_ta_cities.State = ffi_ta_states.Code LEFT JOIN (SELECT `FromCity`, COUNT(`FromCity`) AS `Needs` FROM `ffi_ta_need` GROUP BY `FromCity`) `q1` ON ffi_ta_cities.ID = q1.FromCity LEFT JOIN (SELECT `FromCity`, COUNT(`FromCity`) AS `Shares` FROM `ffi_ta_share` GROUP BY `FromCity`) `q2` ON ffi_ta_cities.ID = q2.FromCity) `query` WHERE `Needs` > 0 OR `Shares` > 0 HAVING `URL` = %s ORDER BY `City` ASC", $stateURL));
	}
	
	public static function getDestinationNeedCities($cityURL, $stateCode) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT q.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `Occupants`, `FromCity`, `FromState`, `FromLatitude`, `FromLongitude`, `City` AS `ToCity`, `State` AS `ToState`, `Latitude` AS `ToLatitude`, `Longitude` AS `ToLongitude` FROM (SELECT ffi_ta_need.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `MalesPresent` + `FemalesPresent` + 1 AS `Occupants`, `City` AS `FromCity`, `State` AS `FromState`, `Latitude` AS `FromLatitude`, `Longitude` AS `FromLongitude`, `ToCity` FROM  `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.FromCity = ffi_ta_cities.ID LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Requestee` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id  WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_need.Person = users.ID WHERE REPLACE(LOWER(ffi_ta_cities.City), ' ', '-') = %s AND ffi_ta_cities.State = %s ORDER BY `Leaving` ASC) `q` LEFT JOIN `ffi_ta_cities` ON q.ToCity = ffi_ta_cities.ID ORDER BY `ToCity` ASC, `ToState` ASC, `Leaving` ASC", $cityURL, $stateCode));
	}
	
	public static function getDestinationShareCities($cityURL, $stateCode) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT q.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `Seats`, `FromCity`, `FromState`, `FromLatitude`, `FromLongitude`, `City` AS `ToCity`, `State` AS `ToState`, `Latitude` AS `ToLatitude`, `Longitude` AS `ToLongitude` FROM (SELECT ffi_ta_share.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `Seats`, `City` AS `FromCity`, `State` AS `FromState`, `Latitude` AS `FromLatitude`, `Longitude` AS `FromLongitude`, `ToCity` FROM  `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.FromCity = ffi_ta_cities.ID LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Requestee` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id  WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_share.Person = users.ID WHERE REPLACE(LOWER(ffi_ta_cities.City), ' ', '-') = %s AND ffi_ta_cities.State = %s ORDER BY `Leaving` ASC) `q` LEFT JOIN `ffi_ta_cities` ON q.ToCity = ffi_ta_cities.ID ORDER BY `ToCity` ASC, `ToState` ASC, `Leaving` ASC", $cityURL, $stateCode));
	}
	
	public static function getMyTrips($userID) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("(SELECT ffi_ta_cities.*, 'Share' AS `Type` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.FromCity = ffi_ta_cities.ID WHERE `Person` = %d GROUP BY ffi_ta_cities.ID) UNION (SELECT ffi_ta_cities.*, 'Need' AS `Type` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.FromCity = ffi_ta_cities.ID WHERE `Person` = %d GROUP BY ffi_ta_cities.ID)", $userID));
	}
	
	public static function getMyNeeds($userID) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT ffi_ta_need.ID, ffi_ta_need.Leaving, ffi_ta_need.LeavingTimeZone, q1.City AS `FromCity`, q1.State AS `FromState`, q1.Latitude AS `FromLatitude`, q1.Longitude AS `FromLongitude`, q2.City AS `ToCity`, q2.State AS `ToState`, q2.Latitude AS `ToLatitude`, q2.Longitude AS `ToLongitude` FROM `ffi_ta_need` LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q1` ON ffi_ta_need.FromCity = q1.ID LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q2` ON ffi_ta_need.ToCity = q2.ID WHERE `Person` = %d ORDER BY `Leaving` ASC", $userID));
	}
	
	public static function getMyShares($userID) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT ffi_ta_share.ID, ffi_ta_share.Leaving, ffi_ta_share.LeavingTimeZone, q1.City AS `FromCity`, q1.State AS `FromState`, q1.Latitude AS `FromLatitude`, q1.Longitude AS `FromLongitude`, q2.City AS `ToCity`, q2.State AS `ToState`, q2.Latitude AS `ToLatitude`, q2.Longitude AS `ToLongitude` FROM `ffi_ta_share` LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q1` ON ffi_ta_share.FromCity = q1.ID LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `q2` ON ffi_ta_share.ToCity = q2.ID WHERE `Person` = %d ORDER BY `Leaving` ASC", $userID));
	}
}
?>