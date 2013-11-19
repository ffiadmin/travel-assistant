<?php
/**
 * Ride Share Processing class
 *
 * This class is used to:
 *  - Determine whether or not a user has sumbitted the ride sharing
 *    form.
 *  - Validate all incoming data.
 *  - Either insert the data into a database or update existing data.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends   Processor_Base
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.processing
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(__FILE__) . "/Processor_Base.php");
require_once(dirname(dirname(__FILE__)) . "/exceptions/Validation_Failed.php");
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-includes/link-template.php");

class Ride_Share_Process extends Processor_Base {
/**
 * Hold a constant which will be used when processing the "From 
 * Where" form item.
 *
 * @access private
 * @const
 * @type   string
*/

	const FROM = "from";

/**
 * Hold a constant which will be used when processing the "To 
 * Where" form item.
 *
 * @access private
 * @const
 * @type   string
*/

	const TO = "to";

/**
 * Hold the DateTime objcet which will be used to format dates.
 *
 * @access private
 * @type   DateTime
*/

	private $dateFormatter;
	
/**
 * Hold the ID of the user submitting the form.
 *
 * @access private
 * @type   int
*/
	
	private $person;
	
/**
 * Hold a formatted string representing the date and time the user
 * is leaving.
 *
 * @access private
 * @type   string
*/
	
	private $leavingDate;
	
/**
 * Hold the time zone in which the user will be leaving.
 *
 * @access private
 * @type   string
*/
	
	private $leavingTimeZone;
	
/**
 * Hold the city from which the user will be traveling.
 *
 * @access private
 * @type   string
*/
	
	private $fromCity;
	
/**
 * Hold the state from which the user will be traveling.
 *
 * @access private
 * @type   string
*/
	
	private $fromStateCode;
	
/**
 * Hold the latitude of the user's origin city.
 *
 * @access private
 * @type   float
*/
	
	private $fromLatitude;
	
/**
 * Hold the longitude of the user's origin city.
 *
 * @access private
 * @type   float
*/
	
	private $fromLongitude;
	
/**
 * Hold the city to which the user will be traveling.
 *
 * @access private
 * @type   string
*/
	
	private $toCity;
	
/**
 * Hold the state to which the user will be traveling.
 *
 * @access private
 * @type   string
*/
	
	private $toStateCode;
	
/**
 * Hold the latitude of the user's destination city.
 *
 * @access private
 * @type   float
*/
	
	private $toLatitude;
	
/**
 * Hold the longitude of the user's destination city.
 *
 * @access private
 * @type   float
*/
	
	private $toLongitude;
	
/**
 * Hold the total number of avaliable seats.
 *
 * @access private
 * @type   int
*/
	
	private $seats;
	
/**
 * Hold the number of males present for the trip.
 *
 * @access private
 * @type   int
*/
	
	private $males;
	
/**
 * Hold the number of females present for the trip.
 *
 * @access private
 * @type   int
*/
	
	private $females;
	
/**
 * Hold the number of minutes the is willing to travel out of his or her
 * way to take passengers to their proper destination.
 *
 * @access private
 * @type   int
*/
	
	private $minutes;
	
/**
 * Hold the gas money reimbursement total.
 *
 * @access private
 * @type   int
*/
	
	private $reimburse;
	
/**
 * Hold whether there is room for luggage.
 *
 * @access private
 * @type   int
*/
	
	private $luggage;
	
/**
 * Hold whether this trip will be recurring.
 *
 * @access private
 * @type   int
*/
	
	private $recurring;
	
/**
 * Hold whether this user can share a regular trip on a 
 * Monday.
 *
 * @access private
 * @type   int
*/
	
	private $monday;
	
/**
 * Hold whether this user can share a regular trip on a 
 * Tuesday.
 *
 * @access private
 * @type   int
*/
	
	private $tuesday;
	
/**
 * Hold whether this user can share a regular trip on a 
 * Wednesday.
 *
 * @access private
 * @type   int
*/
	
	private $wednesday;
	
/**
 * Hold whether this user can share a regular trip on a 
 * Thursday.
 *
 * @access private
 * @type   int
*/
	
	private $thursday;
	
/**
 * Hold whether this user can share a regular trip on a 
 * Friday.
 *
 * @access private
 * @type   int
*/
	
	private $friday;
	
/**
 * Hold the ending date of the user's trip recurrence schedule.
 *
 * @access private
 * @type   string
*/
	
	private $until;
	
/**
 * Hold the user's comments.
 *
 * @access private
 * @type   string
*/
	
	private $comments;
	
/**
 * CONSTRUCTOR
 *
 * This method will call helper methods to:
 *  - Determine whether or not a user has sumbitted the ride sharing
 *    form.
 *  - Validate all incoming data.
 *  - Either insert the data into a database or update existing data.
 * 
 * @access public
 * @param  int    $ID The ID of the tuple to update in the database. $ID = 0 means there is no entry to update (i.e. insert a tuple).
 * @return void
 * @since  1.0
*/
	
	public function __construct($ID) {
		parent::__construct();
	
	//Check to see if the user has submitted the form
		if ($this->userSubmittedForm()) {
			$this->dateFormatter = new \DateTime();
			$this->validateAndRetain();
			
			if (intval($ID)) {
				$this->update($ID);
			} else {
				$this->insert();
			}
		}
	}
	
/**
 * Determine whether or not the user has submitted the form by
 * checking to see if all required data is present (but not 
 * necessarily valid).
 * 
 * @access private
 * @return bool    Whether or not the user has submitted the form
 * @since  1.0
*/
	
	private function userSubmittedForm() {
		if (is_array($_POST) && count($_POST) && 
			isset($_POST['when']) && isset($_POST['from-where-city']) && isset($_POST['from-where-state'])  && isset($_POST['to-where-city']) && isset($_POST['to-where-state']) && isset($_POST['seats']) && isset($_POST['males']) && isset($_POST['females']) && isset($_POST['minutes']) && isset($_POST['reimburse']) && isset($_POST['luggage']) && isset($_POST['recurring']) &&
			!empty($_POST['when']) && !empty($_POST['from-where-city']) && !empty($_POST['from-where-state']) && !empty($_POST['to-where-city']) && !empty($_POST['to-where-state']) && is_numeric($_POST['seats']) && is_numeric($_POST['males']) && is_numeric($_POST['females']) && is_numeric($_POST['minutes']) && is_numeric($_POST['reimburse']) && is_numeric($_POST['luggage']) && is_numeric($_POST['recurring'])) {
			return true;	
		}
		
		return false;
	}
	
/**
 * Determine whether or not all of the required information has been
 * submitted and is completely valid. If validation has succeeded, then
 * store the data within the class for later database entry.
 * 
 * @access private
 * @return bool              Whether or not validation has succeeded
 * @since  1.0
 * @throws Validation_Failed Thrown when ANY portion of the validation process fails
*/
	
	private function validateAndRetain() {
		global $essentials;
		
	//Retain the user ID, an earlier script will already have ensured the user is logged in
		$this->person = $essentials->user->ID;
		
	//Validate and retain the leaving date
		$date = trim(mb_substr($_POST['when'], 0, -3)); //Will handle trimming " ET" and "AKT"
		$date = strtotime($date);
		
		if ($date !== FALSE) {
			$this->dateFormatter->setTimestamp($date);
			$this->leavingDate = $this->dateFormatter->format("Y-m-d H:i:s");
		} else {
			throw new Validation_Failed("The leaving date is invalid");
		}
		
	//Validate and retain the leaving time zone
		$timeZone = strtoupper(trim(mb_substr($_POST['when'], -3, 3))); //Will handle getting " ET" and "AKT"
		$validZones = array (
			"HAT" => "Pacific/Honolulu",
			"AKT" => "America/Anchorage",
			"PT"  => "America/Los_Angeles",
			"MT"  => "America/Denver",
			"CT"  => "America/Chicago",
			"ET"  => "America/New_York"
		);
		
		if (array_key_exists($timeZone, $validZones)) {
			$this->leavingTimeZone = $validZones[$timeZone];
		} else {
			throw new Validation_Failed("The timezone is invalid");
		}
		
	//Validate and retain the origin and destination city names and state codes
		$fromCityName = $_POST['from-where-city'];
		$fromStateCode = strtoupper($_POST['from-where-state']);	
		$toCityName = $_POST['to-where-city'];
		$toStateCode = strtoupper($_POST['to-where-state']);
		$validStates = array("AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY");
		
		if (in_array($fromStateCode, $validStates) &&
			in_array($toStateCode, $validStates) &&
			$this->getCoords($fromCityName, $fromStateCode, self::FROM) &&
			$this->getCoords($toCityName, $toStateCode, self::TO) &&
			($this->fromCity != $this->toCity || ($this->fromCity == $this->toCity && $this->fromStateCode != $this->toStateCode))) {
			//getCords() already set the needed variables
		} else {
			throw new Validation_Failed("The either the origin or destination city or state name is invalid");
		}
		
	//Validate and retain the number of seats
		if ($this->intBetween($_POST['seats'], 1, 10)) {
			$this->seats = $_POST['seats'];
		} else {
			throw new Validation_Failed("The number of seats is invalid");
		}
		
	//Validate and retain the number of males/females	
		if ($this->intBetween($_POST['males'], 0, 5) && $this->intBetween($_POST['females'], 0, 5)) {
			$this->males = $_POST['males'];
			$this->females = $_POST['females'];
		} else {
			throw new Validation_Failed("The number of men or women is invalid");
		}
		
	//Validate and retain the number of days notice needed before a trip
		if ($this->intBetween($_POST['days'], 0, 30)) {
			$this->days = $_POST['days'];
		} else {
			return false;
		}
		
	//Validate and retain the number of minutes within the final destination value
		if ($this->intBetween($_POST['minutes'], 0, 120)) {
			$this->minutes = $_POST['minutes'];
		} else {
			throw new Validation_Failed("The minutes within the final destination value is invalid");
		}
		
	//Validate and retain the reimbursement total
		if ($this->intBetween($_POST['reimburse'], 0, 100)) {
			$this->reimburse = $_POST['reimburse'];
		} else {
			throw new Validation_Failed("The reimbursement total is invalid");
		}
		
	//Validate and retain the luggage value
		if ($this->intBetween($_POST['luggage'], 0, 1)) {
			$this->luggage = $_POST['luggage'];
		} else {
			throw new Validation_Failed("The luggage value is invalid");
		}
	
	//Validate and retain the recurrence value
		if ($this->intBetween($_POST['recurring'], 0, 1)) {
			$this->recurring = $_POST['recurring'];
		} else {
			throw new Validation_Failed("The trip recurrence value is invalid");
		}
		
	//Validate and retain the recurring days and end date
		if ($this->recurring) {
			$recurringDate = strtotime($_POST['until']);
			$recurringDays = (isset($_POST['monday']) ||
								isset($_POST['tuesday']) ||
								isset($_POST['wednesday']) ||
								isset($_POST['thursday']) ||
								isset($_POST['friday'])
							);
			
			
			if ($recurringDays && $recurringDate !== FALSE) {
				$this->dateFormatter->setTimestamp($recurringDate);
				
				$this->monday = isset($_POST['monday']) ? "1" : "0";
				$this->tuesday = isset($_POST['tuesday']) ? "1" : "0";
				$this->wednesday = isset($_POST['wednesday']) ? "1" : "0";
				$this->thursday = isset($_POST['thursday']) ? "1" : "0";
				$this->friday = isset($_POST['friday']) ? "1" : "0";
				$this->until = $this->dateFormatter->format("Y-m-d");
			} else {
				$this->monday = 0;
				$this->tuesday = 0;
				$this->wednesday = 0;
				$this->thursday = 0;
				$this->friday = 0;
				$this->until = NULL;
			}
		} else {
			$this->monday = 0;
			$this->tuesday = 0;
			$this->wednesday = 0;
			$this->thursday = 0;
			$this->friday = 0;
			$this->until = NULL;
		}
		
	//Retain the comments
		$this->comments = $_POST['comments'];
		
		return true;
	}
	
/**
 * Fetch the latitude and longitude of a particular city from the Google
 * Geocode API, and store these results, along with the city and state
 * code, for later entry into a database
 * 
 * @access private
 * @param  string  $city  The name of the city to locate
 * @param  string  $state The state in which the city is located
 * @param  string  $type  Whether to store the results in the "From Where" or "To Where" variables
 * @return bool            Whether or not the latitude and longitude of the city could be determined
 * @since  1.0
*/
	
	private function getCoords($city, $state, $type) {
		$URL = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($city . ", " . $state) . "&components=country:US&sensor=false";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $URL);
		$data = curl_exec($ch);
		curl_close($ch);
		
	//Check if data was returned from the Google API
		if ($data === FALSE) {
			return false;
		}
		
		$data = json_decode($data);
		
	//The API will ALWAYS return data, even if it is given bad input. Check to see if the output is indeed a city.
		$returnType = $data->results[0]->address_components[0]->types;
		
	//If this particular element in the returned data is not "locality", then the API did not return a city
		if (!isset($returnType[0]) || (isset($returnType[0]) && $returnType[0] != "locality")) {
			return false;
		}
		
	//All information past this point is considered valid; share this data with the class
		if ($type == self::FROM) {
			$this->fromCity = $data->results[0]->address_components[0]->long_name;
			$this->fromStateCode = $state;
			$this->fromLatitude = $data->results[0]->geometry->location->lat;
			$this->fromLongitude = $data->results[0]->geometry->location->lng;
		} else {
			$this->toCity = $data->results[0]->address_components[0]->long_name;
			$this->toStateCode = $state;
			$this->toLatitude = $data->results[0]->geometry->location->lat;
			$this->toLongitude = $data->results[0]->geometry->location->lng;
		}
		
		return true;
	}
	
/**
 * Determine whether or not the given city and state has already been 
 * entered into the "ffi_ta_cities" table, and it if has, fetch the 
 * city ID, otherwise store these values with their corrdinates into
 * the table and get the city ID.
 * 
 * @access private
 * @param  string  $city      The name of the city
 * @param  string  $stateCode The state code where the city resides
 * @param  int     $latitude  The latitude of the city
 * @param  int     $longitude The longitude of the city
 * @return int                The ID of the city of interest
 * @since  1.0
*/
	
	private function cityID($city, $stateCode, $latitude, $longitude) {
		global $wpdb;
		
		$cityData = $wpdb->get_col($wpdb->prepare("SELECT `ID` FROM `ffi_ta_cities` WHERE `City` = %s AND `State` = %s", $city, $stateCode));
		
	//Has this city already been entered into the table?
		if (count($cityData)) {
			return $cityData[0];
		} else {
			$wpdb->insert("ffi_ta_cities", array(
				"ID"        => NULL,
				"City"      => $city,
				"State"     => $stateCode,
				"Latitude"  => $latitude,
				"Longitude" => $longitude
			), array(
				"%s", "%s", "%s", "%s", "%s"
			));
			
			return $wpdb->insert_id;
		}
	}
	
/**
 * Use the values validated and retained in memory by the 
 * validateAndRetain() method to insert a new entry into the database
 * 
 * @access private
 * @return void
 * @since  1.0
*/
	
	private function insert() {
		global $wpdb;
		
	//Have these cities ever been recorded before?
		$fromCityID = $this->cityID($this->fromCity, $this->fromStateCode, $this->fromLatitude, $this->fromLongitude);
		$toCityID = $this->cityID($this->toCity, $this->toStateCode, $this->toLatitude, $this->toLongitude);
		
	//Insert the sharing information in the database
		$wpdb->insert("ffi_ta_share", array(
			"ID"              => NULL,
			"Person"          => $this->person,
			"Leaving"         => $this->leavingDate,
			"LeavingTimeZone" => $this->leavingTimeZone,
			"FromCity"        => $fromCityID,
			"ToCity"          => $toCityID,
			"Seats"           => $this->seats,
			"MalesPresent"    => $this->males,
			"FemalesPresent"  => $this->females,
			"MinutesWithin"   => $this->minutes,
			"GasMoney"        => $this->reimburse,
			"Luggage"         => $this->luggage,
			"Monday"          => $this->monday,
			"Tuesday"         => $this->tuesday,
			"Wednesday"       => $this->wednesday,
			"Thursday"        => $this->thursday,
			"Friday"          => $this->friday,
			"EndDate"         => $this->until,
			"Comments"        => $this->comments
		), array(
			"%d", "%d", "%s", "%s", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%s", "%s"
		));
		
	//Redirect to the trip
		$ID = $wpdb->insert_id;
		$URL = $this->fromCity . "-" . $this->fromStateCode . "-to-" . $this->toCity . "-" . $this->toStateCode;
        wp_redirect(get_site_url() . "/travel-assistant/trips/available/" . $ID . "/" . $this->URLPurify($URL));
        exit;
	}
	
/**
 * Use the values validated and retained in memory by the 
 * validateAndRetain() method to update an existing entry in the 
 * database
 * 
 * @access private
 * @param  int     $ID The ID of the tuple to update
 * @return void
 * @since  1.0
*/
	
	private function update($ID) {
		global $wpdb;
		
	//Have these cities ever been recorded before?
		$fromCityID = $this->cityID($this->fromCity, $this->fromStateCode, $this->fromLatitude, $this->fromLongitude);
		$toCityID = $this->cityID($this->toCity, $this->toStateCode, $this->toLatitude, $this->toLongitude);
		
	//Update the sharing information in the database
		$wpdb->update("ffi_ta_share", array(
			"Leaving"         => $this->leavingDate,
			"LeavingTimeZone" => $this->leavingTimeZone,
			"FromCity"        => $fromCityID,
			"ToCity"          => $toCityID,
			"Seats"           => $this->seats,
			"MalesPresent"    => $this->males,
			"FemalesPresent"  => $this->females,
			"DaysNotice"      => $this->days,
			"MinutesWithin"   => $this->minutes,
			"GasMoney"        => $this->reimburse,
			"Luggage"         => $this->luggage,
			"Monday"          => $this->monday,
			"Tuesday"         => $this->tuesday,
			"Wednesday"       => $this->wednesday,
			"Thursday"        => $this->thursday,
			"Friday"          => $this->friday,
			"EndDate"         => $this->until,
			"Comments"        => $this->comments
		), array (
			"ID" => $ID
		), array(
			"%s", "%s", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%s", "%s"
		), array (
			"%d"
		));
		
	//Redirect to the trip
		$URL = $this->fromCity . "-" . $this->fromStateCode . "-to-" . $this->toCity . "-" . $this->toStateCode;
        wp_redirect(get_site_url() . "/travel-assistant/trips/needed/" . $ID . "/" . $this->URLPurify($URL));
        exit;
	}
}
?>