<?php
/**
 * Form data processing class
 *
 * This class is used to:
 *  - Determine whether or not a user has sumbitted the ride request
 *    form
 *  - Validate all incoming data
 *  - Either insert the data into a database or update existing data
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   includes.form.processing
 * @since     v1.0 Dev
*/

namespace FFI\TA;

class Ride_Request_Process {
/**
 * Hold a constant which will be used when processing the "From 
 * Where" form item
 *
 * @access private
 * @const
 * @type   string
*/

	const FROM = "from";

/**
 * Hold a constant which will be used when processing the "To 
 * Where" form item
 *
 * @access private
 * @const
 * @type   string
*/

	const TO = "to";

/**
 * Hold the DateTime objcet which will be used to format dates
 *
 * @access private
 * @type   DateTime
*/

	private $dateFormatter;
	
/**
 * Hold the ID of the user submitting the form
 *
 * @access private
 * @type   int
*/
	
	private $person;
	
/**
 * Hold a formatted string representing the date and time the user
 * is leaving
 *
 * @access private
 * @type   string
*/
	
	private $leavingDate;
	
/**
 * Hold the time zone in which the user will be leaving
 *
 * @access private
 * @type   string
*/
	
	private $leavingTimeZone;

/**
 * Hold the city from which the user will be traveling
 *
 * @access private
 * @type   string
*/
	
	private $fromCity;
	
/**
 * Hold the state from which the user will be traveling
 *
 * @access private
 * @type   string
*/
	
	private $fromStateCode;
	
/**
 * Hold the latitude of the user's origin city
 *
 * @access private
 * @type   float
*/
	
	private $fromLatitude;
	
/**
 * Hold the longitude of the user's origin city
 *
 * @access private
 * @type   float
*/
	
	private $fromLongitude;
	
/**
 * Hold the city to which the user will be traveling
 *
 * @access private
 * @type   string
*/
	
	private $toCity;
	
/**
 * Hold the state to which the user will be traveling
 *
 * @access private
 * @type   string
*/
	
	private $toStateCode;
	
/**
 * Hold the latitude of the user's destination city
 *
 * @access private
 * @type   float
*/
	
	private $toLatitude;
	
/**
 * Hold the longitude of the user's destination city
 *
 * @access private
 * @type   float
*/
	
	private $toLongitude;
	
/**
 * Hold the number of males present for the trip
 *
 * @access private
 * @type   int
*/
	
	private $males;
	
/**
 * Hold the number of females present for the trip
 *
 * @access private
 * @type   int
*/
	
	private $females;
	
/**
 * Hold the number of days the user will need if no one can help
 *
 * @access private
 * @type   int
*/
	
	private $days;
	
/**
 * Hold the number of minutes the driver should take this user within
 * their desired destination
 *
 * @access private
 * @type   int
*/
	
	private $minutes;
	
/**
 * Hold the gas money reimbursement total
 *
 * @access private
 * @type   int
*/
	
	private $reimburse;
	
/**
 * Hold whether this user will bring luggage
 *
 * @access private
 * @type   int
*/
	
	private $luggage;
	
/**
 * Hold whether this trip will be recurring
 *
 * @access private
 * @type   int
*/
	
	private $recurring;
	
/**
 * Hold whether this user will need a regular trip on a 
 * Monday
 *
 * @access private
 * @type   int
*/
	
	private $monday;
	
/**
 * Hold whether this user will need a regular trip on a 
 * Tuesday
 *
 * @access private
 * @type   int
*/
	
	private $tuesday;
	
/**
 * Hold whether this user will need a regular trip on a 
 * Wednesday
 *
 * @access private
 * @type   int
*/
	
	private $wednesday;
	
/**
 * Hold whether this user will need a regular trip on a 
 * Thursday
 *
 * @access private
 * @type   int
*/
	
	private $thursday;
	
/**
 * Hold whether this user will need a regular trip on a 
 * Friday
 *
 * @access private
 * @type   int
*/
	
	private $friday;
	
/**
 * Hold the ending date of the user's trip recurrence schedule
 *
 * @access private
 * @type   string
*/
	
	private $until;
	
/**
 * Hold the user's comments
 *
 * @access private
 * @type   string
*/
	
	private $comments;
	
/**
 * CONSTRUCTOR
 *
 * This method will call helper methods to:
 *  - Determine whether or not a user has sumbitted the ride request
 *    form
 *  - Validate all incoming data
 *  - Either insert the data into a database or update existing data
 * 
 * @access public
 * @param  int      $ID              The ID of the tuple to update in the database. $ID = 0 means there is no entry to update (i.e. insert a tuple).
 * @param  string   $redirectSuccess The URL of the page to redirect to on validation success
 * @param  string   $redirectFail    The URL of the page to redirect to on validation fail
 * @return void
 * @since  v1.0 Dev
*/
	
	public function __construct($ID, $redirectSuccess, $redirectFail) {
	//Check to see if the user has submitted the form
		if ($this->userSubmittedForm()) {
			$this->dateFormatter = new \DateTime();
			
		//If all form elements are valid, then store these values in the database
			if ($this->validateAndRetain()) {
				if (intval($ID)) {
					$this->update($ID);
					wp_redirect($redirectSuccess . "?action=updated");
				} else {
					$this->insert();
					wp_redirect($redirectSuccess . "?action=inserted");
				}
			} else {
				wp_redirect($redirectFail . "?message=invalid");
			}
			
			exit;
		}
	}
	
/**
 * Determine whether or not the user has submitted the form by
 * checking to see if all required data is present (but not 
 * necessarily valid).
 * 
 * @access private
 * @return boolean  Whether or not the user has submitted the form
 * @since  v1.0 Dev
*/
	
	private function userSubmittedForm() {
		if (is_array($_POST) && count($_POST) && 
			isset($_POST['when']) && isset($_POST['from-where-city']) && isset($_POST['from-where-state']) && isset($_POST['to-where-city']) && isset($_POST['to-where-state']) && isset($_POST['males']) && isset($_POST['females']) && isset($_POST['days']) && isset($_POST['minutes']) && isset($_POST['reimburse']) && isset($_POST['luggage']) && isset($_POST['recurring']) &&
			!empty($_POST['when']) && !empty($_POST['from-where-city']) && !empty($_POST['from-where-state']) && !empty($_POST['to-where-city']) && !empty($_POST['to-where-state']) && is_numeric($_POST['males']) && is_numeric($_POST['females']) && is_numeric($_POST['days']) && is_numeric($_POST['minutes']) && is_numeric($_POST['reimburse']) && is_numeric($_POST['luggage']) && is_numeric($_POST['recurring'])) {
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
 * @return boolean  Whether or not validation has succeeded
 * @since  v1.0 Dev
*/
	
	private function validateAndRetain() {
		global $essentials;
		
	//Retain the user ID, an earlier script will already have ensured the user is logged in
		$this->person = $essentials->user-ID;
		
	//Validate and retain the leaving date
		$date = trim(mb_substr($_POST['when'], 0, -3)); //Will handle trimming " ET" and "AKT"
		$date = strtotime($date);
		
		if ($date !== FALSE) {
			$this->dateFormatter->setTimestamp($date);
			$this->leavingDate = $this->dateFormatter->format("Y-m-d H:i:s");
		} else {
			return false;
		}
		
	//Validate and retain the leaving time zone
		$timeZone = strtoupper(trim(mb_substr($_POST['when'], -3, 3))); //Will handle getting " ET" and "AKT"
		$validZones = array("ET", "CT", "MT", "PT", "AKT", "HAT");
		
		if (in_array($timeZone, $validZones)) {
			$this->leavingTimeZone = $timeZone;
		} else {
			return false;
		}
		
	//Validate and retain the origin and destination city names and state codes
		$fromCityName = $_POST['from-where-city'];
		$fromStateCode = strtoupper($_POST['from-where-state']);	
		$toCityName = $_POST['to-where-city'];
		$toStateCode = strtoupper($_POST['to-where-state']);
		$validStates = array("AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY");
		
		if (in_array($fromStateCode, $validStates) && in_array($toStateCode, $validStates) &&
			$this->getCoords($fromCityName, $fromStateCode, self::FROM) && $this->getCoords($toCityName, $toStateCode, self::TO) &&
			($this->fromCity != $this->toCity || ($this->fromCity == $this->toCity && $this->fromStateCode != $this->toStateCode))) {
			//getCords() already set the needed variables
		} else {
			return false;
		}
		
	//Validate and retain the number of males/females	
		if ($this->intBetween($_POST['males'], 0, 5) && $this->intBetween($_POST['females'], 0, 5)) {
			$this->males = $_POST['males'];
			$this->females = $_POST['females'];
		} else {
			return false;
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
			return false;
		}
		
	//Validate and retain the reimbursement total
		if ($this->intBetween($_POST['reimburse'], 0, 100)) {
			$this->reimburse = $_POST['reimburse'];
		} else {
			return false;
		}
		
	//Validate and retain the luggage value
		if ($this->intBetween($_POST['luggage'], 0, 1)) {
			$this->luggage = $_POST['luggage'];
		} else {
			return false;
		}
	
	//Validate and retain the recurrence value
		if ($this->intBetween($_POST['recurring'], 0, 1)) {
			$this->recurring = $_POST['recurring'];
		} else {
			return false;
		}
		
	//Validate and retain the recurring days and end date
		if ($this->recurring) {
			$recurringDays = (isset($_POST['monday']) || isset($_POST['tuesday']) || isset($_POST['wednesday']) || isset($_POST['thursday']) || isset($_POST['friday']));
			$recurringDate = strtotime($_POST['until']);
			
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
 * @param  string   $city  The name of the city to locate
 * @param  string   $state The state in which the city is located
 * @param  string   $type  Whether to store the results in the "From Where" or "To Where" variables
 * @return boolean         Whether or not the latitude and longitude of the city could be determined
 * @since  v1.0 Dev
*/
	
	private function getCoords($city, $state, $type) {
		$URL = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($city . ", " . $state) . "&components=country:US&sensor=false";
		$data = file_get_contents($URL);
		
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
 * Check to see if a particular integer value falls between a specified
 * range
 * 
 * @access private
 * @param  int      $value The integer value to check
 * @param  int      $min   The minimum value the integer may equal
 * @param  int      $max   The maximum value the integer may equal
 * @return boolean         Whether or not the integer falls within the specified range
 * @since  v1.0 Dev
*/
	
	private function intBetween($value, $min, $max) {
		if (!is_numeric($value)) {
			return false;
		}
		
		$value = intval($value);
		
	//Check the integer extrema
		if ($value >= $min && $value <= $max) {
			return true;
		}
		
		return false;
	}
	
/**
 * Determine whether or not the given city and state has already been 
 * entered into the "ffi_ta_cities" table, and it if has, fetch the 
 * city ID, otherwise store these values with their corrdinates into
 * the table and get the city ID.
 * 
 * @access private
 * @param  string   $city      The name of the city
 * @param  string   $stateCode The state code where the city resides
 * @param  int      $latitude  The latitude of the city
 * @param  int      $longitude The longitude of the city
 * @return int      The ID of the city of interest
 * @since  v1.0 Dev
*/
	
	private function cityID($city, $stateCode, $latitude, $longitude) {
		global $wpdb;
		
		$cityData = $wpdb->get_results($wpdb->prepare("SELECT ID FROM `ffi_ta_cities` WHERE `City` = %s AND `State` = %s", $city, $stateCode));
		
	//Has this city already been entered into the table?
		if (count($cityData)) {
			return $cityData[0]->ID;
		} else {
			$wpdb->insert("ffi_ta_cities", array(
				"ID" => NULL,
				"City" => $city,
				"State" => $stateCode,
				"Latitude" => $latitude,
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
 * @since  v1.0 Dev
*/
	
	private function insert() {
		global $wpdb;
		
	//Have these cities ever been recorded before?
		$fromCityID = $this->cityID($this->fromCity, $this->fromStateCode, $this->fromLatitude, $this->fromLongitude);
		$toCityID = $this->cityID($this->toCity, $this->toStateCode, $this->toLatitude, $this->toLongitude);
		
	//Insert the request in the database
		$wpdb->insert("ffi_ta_need", array(
			"ID" => NULL,
			"Person" => $this->person,
			"Leaving" => $this->leavingDate,
			"LeavingTimeZone" => $this->leavingTimeZone,
			"FromCity" => $fromCityID,
			"ToCity" => $toCityID,
			"MalesPresent" => $this->males,
			"FemalesPresent" => $this->females,
			"DaysNotice" => $this->days,
			"MinutesWithin" => $this->minutes,
			"GasMoney" => $this->reimburse,
			"Luggage" => $this->luggage,
			"Monday" => $this->monday,
			"Tuesday" => $this->tuesday,
			"Wednesday" => $this->wednesday,
			"Thursday" => $this->thursday,
			"Friday" => $this->friday,
			"EndDate" => $this->until,
			"Comments" => $this->comments
		), array(
			"%s", "%d", "%s", "%s", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%s", "%s"
		));
	}
	
/**
 * Use the values validated and retained in memory by the 
 * validateAndRetain() method to update an existing entry in the 
 * database
 * 
 * @access private
 * @param  int      $ID The ID of the tuple to update
 * @return void
 * @since  v1.0 Dev
*/
	
	private function update($ID) {
		global $wpdb;
		
	//Have these cities ever been recorded before?
		$fromCityID = $this->cityID($this->fromCity, $this->fromStateCode, $this->fromLatitude, $this->fromLongitude);
		$toCityID = $this->cityID($this->toCity, $this->toStateCode, $this->toLatitude, $this->toLongitude);
		
	//Update the request in the database
		$wpdb->update("ffi_ta_need", array(
			"Person" => $this->person,
			"Leaving" => $this->leavingDate,
			"LeavingTimeZone" => $this->leavingTimeZone,
			"FromCity" => $fromCityID,
			"ToCity" => $toCityID,
			"MalesPresent" => $this->males,
			"FemalesPresent" => $this->females,
			"DaysNotice" => $this->days,
			"MinutesWithin" => $this->minutes,
			"GasMoney" => $this->reimburse,
			"Luggage" => $this->luggage,
			"Monday" => $this->monday,
			"Tuesday" => $this->tuesday,
			"Wednesday" => $this->wednesday,
			"Thursday" => $this->thursday,
			"Friday" => $this->friday,
			"EndDate" => $this->until,
			"Comments" => $this->comments
		), array (
			"ID" => $ID
		), array(
			"%d", "%s", "%s", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%s", "%s"
		), array (
			"%d"
		));
	}
}
?>
