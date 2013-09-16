<?php
/**
 * State details information class
 *
 * This class is used to fetch data from the MySQL database for 
 * information regarding US states. Some of the capibilities of
 * this class includes:
 *  - checking if a state or district exists
 *  - fetch all available information about a state by the URL
 *  - generating a dropdown list of state codes (PA, OH, NY, ...)
 * 
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   includes.display
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(dirname(__FILE__)) . "/exceptions/Invalid_State_Exception.php");

class State {
/**
 * This method will determine whether or not a particular state exists
 * when given the URL of the state. All available information about a
 * state will be returned (see State::getInfo) on success, or false
 * if the state does not exist.
 *
 * @access public
 * @param  string         $stateURL The URL of the state to check
 * @return boolean|object           State information on success or false if the state does not exist
 * @see                             State::getInfo()
 * @static
*/

	public static function exists($stateURL) {
		try {
			$info = self::getInfo($stateURL);
			return $info;
		} catch (Invalid_State_Exception $e) {
			return false;
		}
	}

/**
 * This method will fetch all available data about a particular state
 * as defined in the ffi_ta_states relation. This method will NOT fetch
 * information such as how many rides are needed or available for a 
 * a particular state, it will ONLY fetch data from the ffi_ta_states
 * relation.
 *
 * @access public
 * @param  string $stateURL      The URL of the state to fetch information
 * @return object                All available state information from the ffi_ta_states relation
 * @static
 * @throws InvalidStateException Thrown when the given state does not exist
*/

	public static function getInfo($stateURL) {
		global $wpdb;

	//Fetch the data
		$data = $wpdb->get_results($wpdb->prepare("SELECT ffi_ta_states.Code, `Name`, `Image`, `District`, `URL` FROM `ffi_ta_states` LEFT JOIN (SELECT `Code`, LOWER(REPLACE(`Name`, ' ', '-')) AS `URL` FROM `ffi_ta_states`) `q` ON ffi_ta_states.Code = q.Code WHERE `URL` = %s", $stateURL));

	//Was a state returned?
		if (count($data)) {
			return $data[0];
		}

		throw new Invalid_State_Exception("The state URL &quot;" . $stateURL . "&quot; does not exist.");
	}

/**
 * This function will take either the name of a state and prepare it
 * for use in a URL by removing any spaces and special characters, and
 * then making all characters lower case, which is this plugin's
 * convention when placing names of cities and states in a URL.
 * 
 * @access public
 * @param  string $name The name of a state
 * @return string       The URL purified version of the state name
 * @since  1.0
 * @static
*/
	public static function URLPurify($name) {
		$name = preg_replace("/[^a-zA-Z0-9\s\-]/", "", $name); //Remove all non-alphanumeric characters, except for spaces
		$name = preg_replace("/[\s]/", "-", $name);          //Replace remaining spaces with a "-"
		$name = str_replace("--", "-", $name);               //Replace "--" with "-", will occur if a something like " & " is removed
		return strtolower($name);
	}
}
?>
