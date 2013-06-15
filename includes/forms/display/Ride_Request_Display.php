<?php
/**
 * Ride request form display class
 *
 * This class is used to fetch data from the MySQL database for the 
 * ride request display form. If data is returned from the super class's
 * constructor, then the respective values are filled into their proper
 * locations in the form, then returned for display in the HTML form.
 * 
 * If no values are returned, then form items with empty or default
 * values are constructed.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends   Ride_Data_Fetch
 * @license   MIT
 * @namespace FFI\TA
 * @package   includes.form.display
 * @since     v1.0 Dev
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(__FILE__))) . "/Destination_Manager.php");
require_once(dirname(dirname(__FILE__)) . "/processing/Ride_Data_Fetch.php");

class Ride_Request_Display extends Ride_Data_Fetch {
/**
 * Hold a reference to the random number generated to fetch a value from
 * the $whatValues and $whyValues array.
 *
 * @access private
 * @type   int
*/

	private $rand = 0;
	
/**
 * Whether or not this form will have its "Trip Recurrence" section filled
 * out. This value is stored for reference so it does not have to be determined
 * multiple times.
 *
 * @access private
 * @type   boolean
*/
	
	private $recurring = false;
	
/**
 * An array of default values which will be chosen to the the "What" form
 * input element in section one of the form.
 *
 * @access private
 * @type   array<string>
*/
	
	private $whatValues = array("I need a lift", "I'm leaving and don't feel like driving", "I'm going somewhere", "Help! I need a ride!", "I'm leaving");
	
/**
 * An array of default values which will be chosen to the the "Who" form
 * input element in section one of the form.
 *
 * @access private
 * @type   array<string>
*/
	
	private $whyValues = array("We aren't that curious", "Don't worry, we won't ask", "We won't be nosy", "We won't make you tell us", "We'll leave that up to you");
	
/**
 * CONSTRUCTOR
 *
 * Grab the data from the database using the super constructor, see if
 * any data was returned, and if not, redirect to the URL indicated by
 * $failRedirect.
 * 
 * @access public
 * @param  int      $ID           The ID of the ride request to fetch from the database
 * @param  int      $userID       The ID of the user requesting this page
 * @param  string   $failRedirect The URL to redirect to if the SQL query returns zero tuples (i.e. an invalid $ID or $userID is given)
 * @return void
 * @since  v1.0 Dev
*/
	
	public function __construct($ID, $userID, $failRedirect) {
		parent::__construct("ffi_ta_need", $ID, $userID);
		$this->rand = mt_rand(0, 4);
		
	//"Leave me!" - http://johnnoble.net/img/photos/denethor_a.jpg
		if (is_null($this->data)) {
			wp_redirect($failRedirect);
			exit;
		}
	}
	
/**
 * Output a prefilled form element containing the "Who" form element 
 * for section one of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getWho() {
		global $essentials;
		
		return "<input autocomplete=\"off\" disabled id=\"who\" name=\"who\" type=\"text\" value=\"" . htmlentities($essentials->user->user_firstname . " " . $essentials->user->user_lastname) . "\">";
	}
	
/**
 * Output a prefilled form element containing the "What" form element 
 * for section one of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getWhat() {
		return "<input autocomplete=\"off\" disabled id=\"what\" name=\"what\" type=\"text\" value=\"" . htmlentities($this->whatValues[$this->rand]) . "\">";
	}
	
/**
 * Output a prefilled form element containing the "When" form element 
 * for section one of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getWhen() {
		if ($this->data) {
			$dateFormatter = new \DateTime($this->data[0]->Leaving);
		
			return "<input autocomplete=\"off\" class=\"validate[required,validate[required,custom[dateTimeFormat]]\" id=\"when\" name=\"when\" placeholder=\"When do you plan on leaving?\" type=\"text\" value=\"" . htmlentities($dateFormatter->format("m/d/Y h:i a") . " " . $this->data[0]->LeavingTimeZone) . "\">";
		} else {
			return "<input autocomplete=\"off\" class=\"validate[required,custom[dateTimeFormat],past[now]]\" id=\"when\" name=\"when\" placeholder=\"When do you plan on leaving?\" type=\"text\" value=\"\">";
		}
	}
	
/**
 * Output a prefilled form element containing the "Where" city text 
 * input and state drop down menu for section one of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getWhere() {
		return "<input autocomplete=\"off\" class=\"rounded-left validate[required]\" id=\"where-city\" name=\"where-city\" placeholder=\"Destination city\" type=\"text\" value=\"" . htmlentities($this->data ? $this->data[0]->CityName : "") . "\">

<select class=\"input-small rounded-right validate[required]\" id=\"where-state\" name=\"where-state\">
<option selected value=\"\">State</option>
" . Destination_Manager::buildStatesDropDown($this->data ? $this->data[0]->State : "", "codes") . "
</select>";
	}
	
/**
 * Output a prefilled form element containing the "Why" form element 
 * for section one of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getWhy() {
		return "<input autocomplete=\"off\" disabled id=\"why\" name=\"why\" type=\"text\" value=\"" . htmlentities($this->whyValues[$this->rand]) . "\">";
	}
	
/**
 * Output a prefilled form element containing the "number of males" form 
 * element for section two of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getMales() {
		return "<input autocomplete=\"off\" class=\"input-mini rounded-left validate[required,custom[integer],min[0],max[5]]\" id=\"males\" max=\"5\" min=\"0\" name=\"males\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->MalesPresent : "0") . "\">";
	}
	
/**
 * Output a prefilled form element containing the "number of females" form 
 * element for section two of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getFemales() {
		return "<input autocomplete=\"off\" class=\"input-mini validate[required,custom[integer],min[0],max[5]]\" id=\"females\" max=\"5\" min=\"0\" name=\"females\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->FemalesPresent : "0") . "\">";
	}
	
/**
 * Output a prefilled form element containing the "number of days notice"
 * form element for section two of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getDaysNotice() {
		return "<input autocomplete=\"off\" class=\"input-mini validate[required,custom[integer],min[0],max[30]]\" id=\"days\" max=\"30\" min=\"0\" name=\"days\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->DaysNotice : "1") . "\">";
	}
	
/**
 * Output a prefilled form element containing the "number of minutes within
 * destination" form element for section two of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getMinutesWithin() {
		return "<input autocomplete=\"off\" class=\"input-mini validate[required,custom[integer],min[0],max[120]]\" id=\"time\" max=\"120\" min=\"0\" name=\"minutes\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->MinutesWithin : "15") . "\">";
	}
	
/**
 * Output a prefilled form element containing the "reimbursement gas money
 * total" form element for section two of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getGasMoney() {
		return "<input autocomplete=\"off\" class=\"input-mini validate[required,custom[integer],min[0],max[100]]\" id=\"reimburse\" max=\"100\" min=\"0\" name=\"reimburse\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->GasMoney : "5") . "\">";
	}
	
/**
 * Output a prefilled form element containing the "Luggage" form element 
 * for section two of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getLuggage() {
		$checkedYes = false;
		$checkedNo = false;
		
	//Determine which option should be checked
		if (!$this->data || ($this->data && $this->data[0]->Luggage == "1")) {
			$checkedYes = true;
		}
		
		if ($this->data && $this->data[0]->Luggage == "0") {
			$checkedNo = true;
		}
		
	//Return the generated output
		return "<div class=\"btn-group\" data-toggle=\"buttons-radio\">
<input autocomplete=\"off\"" . ($checkedYes ? " checked" : "") . " data-toggle=\"button\" id=\"luggage-yes\" name=\"luggage\" type=\"radio\" value=\"1\">
<label class=\"btn\" for=\"luggage-yes\">Yes</label>
<input autocomplete=\"off\"" . ($checkedNo ? " checked" : "") . " data-toggle=\"button\" id=\"luggage-no\" name=\"luggage\" type=\"radio\" value=\"0\">
<label class=\"btn\" for=\"luggage-no\">No</label>
</div>";
	}
	
/**
 * Output a prefilled form element containing the "Recurrence" form element 
 * for section three of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getRecurrence() {
		$checkedYes = false;
		$checkedNo = false;
		
	//Determine which option should be checked
		if ($this->data && ($this->data[0]->Monday == "1" || $this->data[0]->Tuesday == "1" || $this->data[0]->Wednesday == "1" || $this->data[0]->Thursday == "1" || $this->data[0]->Friday == "1")) {
			$checkedYes = true;
			$this->recurring = true;
		} else {
			$checkedNo = true;
		}
	
	//Return the generated output
		return "<div class=\"btn-group\" data-toggle=\"buttons-radio\">
<input autocomplete=\"off\"" . ($checkedYes ? " checked" : "") . " data-toggle=\"button\" id=\"recurring-yes\" name=\"recurring\" type=\"radio\" value=\"1\">
<label class=\"btn\" for=\"recurring-yes\" id=\"recurring-yes-label\">Yes</label>
<input autocomplete=\"off\"" . ($checkedNo ? " checked" : "") . " data-toggle=\"button\" id=\"recurring-no\" name=\"recurring\" type=\"radio\" value=\"0\">
<label class=\"btn\" for=\"recurring-no\" id=\"recurring-no-label\">No</label>
</div>";
	}
	
/**
 * Output a prefilled form element containing the "recurrence days" form 
 * element for section three of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getRecurrenceDays() {
		$class = "";
		$daysID = array("monday", "tuesday", "wednesday", "thursday", "friday");
        $daysText = array("M<span class=\"collapse\">onday</span>", "T<span class=\"collapse\">uesday</span>", "W<span class=\"collapse\">ednesday</span>", "T<span class=\"collapse\">hursday</span>", "F<span class=\"collapse\">riday</span>");
		$daysVal = array($this->data[0]->Monday, $this->data[0]->Tuesday, $this->data[0]->Wednesday, $this->data[0]->Thursday, $this->data[0]->Friday);
		$enabled = $this->recurring ? true : false;
		$return = "<div class=\"btn-group\" data-toggle=\"buttons-checkbox\">
";
		$state = "";
		
	//Since the list is long and the values to check are many, construct the list of days in a loop
		for ($i = 0; $i < 5; ++$i) {
			$class = !$enabled ? " disabled" : $class;
			$state = $daysVal[$i] == "1" ? " checked" : "";
			$state = !$enabled ? " disabled" : $state;
			
			$return .= "<input autocomplete=\"off\" class=\"recurring-day\" data-toggle=\"button\"" . $state . " id=\"" . $daysID[$i] . "\" name=\"" . $daysID[$i] . "\" type=\"checkbox\">
<label class=\"btn recurring-label" . $class . "\" for=\"" . $daysID[$i] . "\">" . $daysText[$i] . "</label>
";
		}
		
		$return .= "</div>";
		return $return;
	}
	
/**
 * Output a prefilled form element containing the "recurrencing until" form 
 * element for section three of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getEndDate() {
		if ($this->recurring) {
			$dateFormatter = new \DateTime($this->data[0]->EndDate);
			
			return "<input autocomplete=\"off\" class=\"validate[required,custom[date]]\" id=\"until\" name=\"until\" placeholder=\"How long will you need a ride?\" type=\"text\" value=\"" . htmlentities($dateFormatter->format("m/d/Y")) . "\">";
		} else {
			return "<input autocomplete=\"off\" class=\"validate[required,custom[date],past[now]]\" disabled id=\"until\" name=\"until\" placeholder=\"How long will you need a ride?\" type=\"text\" value=\"\">";
		}
	}
	
/**
 * Output a prefilled form element containing the "Comments" form element 
 * for section four of this form.
 * 
 * @access public
 * @return string   A form item prefilled with a value from either the database or a default value
 * @since  v1.0 Dev
*/
	
	public function getComments() {
		return "<textarea id=\"comments\" name=\"comments\">" . ($this->data ? $this->data[0]->Comments : "") . "</textarea>";
	}
}
?>