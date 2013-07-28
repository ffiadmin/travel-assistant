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
 * @package   includes.display
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
}
?>
