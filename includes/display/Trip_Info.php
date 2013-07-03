<?php
/**
 * Trip details information class
 *
 * This class is used to fetch data from the MySQL database for 
 * information regarding available or needed rides. These rides 
 * can be fetched as a summary, such as the total number of rides
 * which are needed or available, an overview of rides by city, and
 * the a detailed set of information for rides for a particular 
 * city.
 * 
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\BE
 * @package   includes.display
 * @since     1.0
*/

namespace FFI\BE;

class Trip_Info {
/**
 * This function will return a listing of origin cities (cities in the 
 * given state that a user will be leaving, but not necessarily travelling
 * out of state) sorted alphabetically with the number of rides needed or 
 * available, when given the URL of the desired state.
 *
 * @access public
 * @param  string                $stateURL     The URL of the state in which to fetch the listing of cities
 * @return array<object<string>>               The Wordpress array of objects returned from the database query
 * @since  1.0
 * @static
*/

	public static function getOriginCitiesByState($stateURL) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT `City`, `StateName`, `Code`, REPLACE(LOWER(`StateName`), ' ', '-') AS `URL`, `Image`, `District`, `Latitude`, `Longitude`, `Needs`, `Shares` FROM (SELECT ffi_ta_cities.*, ffi_ta_states.Name AS `StateName`, ffi_ta_states.Code, ffi_ta_states.Image, ffi_ta_states.District, COALESCE(q1.Needs, 0) AS `Needs`, COALESCE(q2.Shares, 0) AS `Shares` FROM `ffi_ta_cities` LEFT JOIN `ffi_ta_states` ON ffi_ta_cities.State = ffi_ta_states.Code LEFT JOIN (SELECT `FromCity`, COUNT(`FromCity`) AS `Needs` FROM `ffi_ta_need` GROUP BY `FromCity`) `q1` ON ffi_ta_cities.ID = q1.FromCity LEFT JOIN (SELECT `FromCity`, COUNT(`FromCity`) AS `Shares` FROM `ffi_ta_share` GROUP BY `FromCity`) `q2` ON ffi_ta_cities.ID = q2.FromCity) `query` WHERE `Needs` > 0 OR `Shares` > 0 HAVING `URL` = %s ORDER BY `City` ASC", $stateURL));
	}

/**
 * This function will take either the name of a state or city and prepare
 * it for use in a URL by removing any spaces and special characters, and
 * then making all characters lower case, which is this plugin's convention
 * when placing names of cities and states in a URL.
 *
 * 
 * @access public
 * @param  string $name The name of a city or state
 * @return string       The URL purified version of the city or state name
 * @since  1.0
 * @static
*/
	public static function URLPurify($name) {
		$name = preg_replace("/[^a-zA-Z0-9\s]/", "", $name); //Remove all non-alphanumeric characters, except for spaces
		$name = preg_replace("/[\s]/", "-", $name);          //Replace remaining spaces with a "-"
		$name = str_replace("--", "-", $name);               //Replace "--" with "-", will occur if a something like " & " is removed
		return strtolower($name);
	}
}
?>
