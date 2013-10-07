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

class Trip_Info {
	public static function getNeeded($tripID) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT `Leaving`, `LeavingTimeZone`, `FromCity` AS `FromCityID`, `City` AS `FromCity`, `State` AS `FromState`, `ToCityID`, `ToCity`, `ToState`, `MalesPresent`, `FemalesPresent`, `MalesPresent` + `FemalesPresent` + 1 AS `Total`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `RequesteeID`, `Requestee` FROM (SELECT ffi_ta_need.ID, `Leaving`, `LeavingTimeZone`, `FromCity`, `ToCity` AS `ToCityID`, `City` AS `ToCity`, `State` AS `ToState`, `MalesPresent`, `FemalesPresent`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `Person` AS `RequesteeID`, `Requestee` FROM `ffi_ta_need` LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Requestee` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_need.Person = users.ID LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.ToCity = ffi_ta_cities.ID) `q` LEFT JOIN `ffi_ta_cities` ON q.FromCity = ffi_ta_cities.ID WHERE q.ID = %d", $tripID));
	}
	
	public static function getAvailable($tripID) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT `Leaving`, `LeavingTimeZone`, `FromCity` AS `FromCityID`, `City` AS `FromCity`, `State` AS `FromState`, `ToCityID`, `ToCity`, `ToState`, `Seats`, `MalesPresent`, `FemalesPresent`, `MalesPresent` + `FemalesPresent` + 1 AS `Total`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `SharerID`, `Sharer` FROM (SELECT ffi_ta_share.ID, `Leaving`, `LeavingTimeZone`, `FromCity`, `ToCity` AS `ToCityID`, `City` AS `ToCity`, `State` AS `ToState`, `Seats`, `MalesPresent`, `FemalesPresent`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments`, `Person` AS `SharerID`, `Sharer` FROM `ffi_ta_share` LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Sharer` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_share.Person = users.ID LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.ToCity = ffi_ta_cities.ID) `q` LEFT JOIN `ffi_ta_cities` ON q.FromCity = ffi_ta_cities.ID WHERE q.ID = %d", $tripID));
	}
}
?>
