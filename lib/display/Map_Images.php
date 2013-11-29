<?php
/**
 * Google Map Images Generator Information class
 *
 * This class is used to generate URLs used when fetching dynamic
 * map images from the Google Maps API.
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

class Map_Images {
/**
 * Hold the Google API key from the database.
 *
 * @access private
 * @type   string
*/

	private static $API = "";
	
/**
 * Generate a map of a city with a red pin marking its location
 * for use on the Browse Trips when selecting a destination city
 * in desktop mode.
 * 
 * @access public
 * @param  string $city      The name of the city
 * @param  string $state     The name or code of the state
 * @param  float  $latitude  The latitude of the city
 * @param  float  $longitude The longitude of the city
 * @return string            A URL to the Google Static Maps API of the desired location
 * @since  1.0
 * @static
*/
	
	public static function browseLarge($city, $state, $latitude, $longitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city) . ",+" . urlencode($state) . "&zoom=13&scale=1&size=230x190&markers=color:red%7C" . urlencode($latitude) . "," . urlencode($longitude) . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}
	
/**
 * Generate a map of a city with a red pin marking its location
 * for use on the Browse Trips when selecting a destination city
 * in mobile mode.
 * 
 * @access public
 * @param  string $city      The name of the city
 * @param  string $state     The name or code of the state
 * @param  float  $latitude  The latitude of the city
 * @param  float  $longitude The longitude of the city
 * @return string            A URL to the Google Static Maps API of the desired location
 * @since  1.0
 * @static
*/
	
	public static function browseSmall($city, $state, $latitude, $longitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city) . ",+" . urlencode($state) . "&zoom=13&scale=1&size=100x100&markers=color:red%7C" . urlencode($latitude) . "," . urlencode($longitude) . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}
	
/**
 * Generate a map of a city with a red pin marking its location
 * for use on the Browse Trips page banner.
 * 
 * @access public
 * @param  string $city      The name of the city
 * @param  string $state     The name or code of the state
 * @param  float  $latitude  The latitude of the city
 * @param  float  $longitude The longitude of the city
 * @return string            A URL to the Google Static Maps API of the desired location
 * @since  1.0
 * @static
*/
	
	public static function cityBanner($city, $state, $latitude, $longitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city) . ",+" . urlencode($state) . "&zoom=13&size=1000x141&scale=2&markers=color:red%7C" . urlencode($latitude) . "," . urlencode($longitude) . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}
	
/**
 * Generate a map of a city with a red pin marking its location
 * for use on the Browse Trips page when selecting an origin city.
 * 
 * @access public
 * @param  string $city      The name of the city
 * @param  string $state     The name or code of the state
 * @param  float  $latitude  The latitude of the city
 * @param  float  $longitude The longitude of the city
 * @return string            A URL to the Google Static Maps API of the desired location
 * @since  1.0
 * @static
*/
	
	public static function cityPreview($city, $state, $latitude, $longitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city) . ",+" . urlencode($state) . "&zoom=13&size=180x180&markers=color:red%7C" . urlencode($latitude) . "," . urlencode($longitude) . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}
	
/**
 * Generate a map of a city with a red pin marking its location
 * for use as a banner image in emails.
 * 
 * @access public
 * @param  string $cityAndState The name of the city and state, in a format like this: Orlando, FL or Orlando, Flordia
 * @param  float  $latitude     The latitude of the city
 * @param  float  $longitude    The longitude of the city
 * @return string               A URL to the Google Static Maps API of the desired location
 * @since  1.0
 * @static
*/
	
	public static function emailBanner($cityAndState, $latitude, $longitude) {
		self::getAPIKey();
	
		return "https://maps.googleapis.com/maps/api/staticmap?center=" . urlencode($cityAndState) . "&zoom=13&size=331x188&scale=2&markers=color:red%7C" . urlencode($latitude) . "," . urlencode($longitude) . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}

/**
 * This function will fetch the Google API key from the database.
 *
 * @access private
 * @return void
 * @since  1.0
 * @static
*/
	
	private static function getAPIKey() {
		global $wpdb;
	
		if (self::$API == "") {		
			$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
			self::$API = $APIs[0]->GoogleMaps;
		}
	}
	
/**
 * Generate a map image for use on the desktop version of the
 * My Trips page.
 * 
 * @access public
 * @param  float  $fromLatitude  The latitude of the origin city
 * @param  float  $fromLongitude The longitude of the origin city
 * @param  float  $toLatitude    The latitude of the destination city
 * @param  float  $toLongitude   The longitude of the destination city
 * @return string            A URL to the Google Static Maps API of the desired location
 * @since  1.0
 * @static
*/

	public static function myTripsPreviewLarge($fromLatitude, $fromLongitude, $toLatitude, $toLongitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?size=180x180&markers=color:green%7Clabel:A%7C" . urlencode($fromLatitude) . "," . urlencode($fromLongitude) . "&markers=color:red%7Clabel:B%7C" . urlencode($toLatitude) . "," . urlencode($toLongitude) . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}
	
/**
 * Generate a map image for use on the mobile version of the
 * My Trips page.
 * 
 * @access public
 * @param  float  $fromLatitude  The latitude of the origin city
 * @param  float  $fromLongitude The longitude of the origin city
 * @param  float  $toLatitude    The latitude of the destination city
 * @param  float  $toLongitude   The longitude of the destination city
 * @return string            A URL to the Google Static Maps API of the desired location
 * @since  1.0
 * @static
*/
	
	public static function myTripsPreviewSmall($fromLatitude, $fromLongitude, $toLatitude, $toLongitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?size=100x100&markers=color:green%7Clabel:A%7C" . urlencode($fromLatitude) . "," . urlencode($fromLongitude) . "&markers=color:red%7Clabel:B%7C" . urlencode($toLatitude) . "," . urlencode($toLongitude) . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}
}
?>