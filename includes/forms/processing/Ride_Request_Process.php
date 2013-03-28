<?php
namespace FFI\TA;

class Ride_Request_Process {
	private $dateFormatter;
	private $person;
	private $leavingDate;
	private $leavingTimeZone;
	private $city;
	private $stateCode;
	private $latitude;
	private $longitude;
	private $males;
	private $females;
	private $days;
	private $minutes;
	private $reimburse;
	private $luggage;
	private $recurring;
	private $monday;
	private $tuesday;
	private $wednesday;
	private $thursday;
	private $friday;
	private $until;
	private $comments;
	
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
					die("FAIL");
					$this->insert();
					wp_redirect($redirectSuccess . "?action=inserted");
				}
			} else {
				wp_redirect($redirectFail . "?message=invalid");
			}
			
			exit;
		}
	}
	
	private function userSubmittedForm() {
		if (is_array($_POST) && count($_POST) && 
			isset($_POST['when']) && isset($_POST['where-city']) && isset($_POST['where-state']) && isset($_POST['males']) && isset($_POST['females']) && isset($_POST['days']) && isset($_POST['minutes']) && isset($_POST['reimburse']) && isset($_POST['luggage']) && isset($_POST['recurring']) &&
			!empty($_POST['when']) && !empty($_POST['where-city']) && !empty($_POST['where-state']) && is_numeric($_POST['males']) && is_numeric($_POST['females']) && is_numeric($_POST['days']) && is_numeric($_POST['minutes']) && is_numeric($_POST['reimburse']) && is_numeric($_POST['luggage']) && is_numeric($_POST['recurring'])) {
			return true;	
		}
		
		return false;
	}
	
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
		
	//Validate and retain the city name and state code	
		$cityName = $_POST['where-city'];
		$stateCode = strtoupper($_POST['where-state']);
		$validStates = array("AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY");
		
		if (in_array($stateCode, $validStates) && $this->getCoords($cityName, $stateCode)) {
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
	
	private function getCoords($city, $state) {
		$URL = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($city . ", " . $state) . "&components=country:US&sensor=false";
		$data = file_get_contents($URL);
		
	//Check if data was returned from Google APIs
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
		$this->city = $data->results[0]->address_components[0]->long_name;
		$this->stateCode = $state;
		$this->latitude = $data->results[0]->geometry->location->lat;
		$this->longitude = $data->results[0]->geometry->location->lng;
		
		return true;
	}
	
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
	
	private function cityID() {
		global $wpdb;
		
		$cityData = $wpdb->get_results($wpdb->prepare("SELECT ID FROM `ffi_ta_cities` WHERE `City` = %s AND `State` = %s", $this->city, $this->stateCode));
		
		if (count($cityData)) {
			return $cityData[0]->ID;
		} else {
			$wpdb->insert("ffi_ta_cities", array(
				"ID" => NULL,
				"City" => $this->city,
				"State" => $this->stateCode,
				"Latitude" => $this->latitude,
				"Longitude" => $this->longitude
			), array(
				"%s", "%s", "%s", "%s", "%s"
			));
			
			return $wpdb->insert_id;
		}
	}
	
	private function insert() {
		global $wpdb;
		
	//Has this city ever been recorded before?
		$cityID = $this->cityID();
		
	//Insert the request in the database
		$wpdb->insert("ffi_ta_need", array(
			"ID" => NULL,
			"Person" => $this->person,
			"Leaving" => $this->leavingDate,
			"LeavingTimeZone" => $this->leavingTimeZone,
			"City" => $cityID,
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
			"%s", "%d", "%s", "%s", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%s", "%s"
		));
	}
	
	private function update($ID) {
		global $wpdb;
		
	//Has this city ever been recorded before?
		$cityID = $this->cityID();
		
	//Update the request in the database
		$wpdb->update("ffi_ta_need", array(
			"Person" => $this->person,
			"Leaving" => $this->leavingDate,
			"LeavingTimeZone" => $this->leavingTimeZone,
			"City" => $cityID,
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
			"%d", "%s", "%s", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%d", "%s", "%s"
		), array (
			"%d"
		));
	}
}
?>