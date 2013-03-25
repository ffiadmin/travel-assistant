<?php
/**
 * Ride request form display class
 *
 * This class is used to fetch data from the MySQL database for the 
 * ride request display form. If data is returned from the super class's
 * constructor, then the respective values are filled into their proper
 * locations in the form, then returned for display in the HTML form.
 * 
 * If no values are returned, then empty form items are constructed.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   includes.form.display
 * @since     v1.0 Dev
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(__FILE__))) . "/Destination_Manager.php");
require_once(dirname(dirname(__FILE__)) . "/processing/Ride_Data_Fetch.php");

class Ride_Request_Display extends Ride_Data_Fetch {
	private $rand = 0;
	private $whatValues = array("I need a lift", "I'm leaving and don't feel like driving", "I'm going somewhere", "Help! I need a ride!", "I'm leaving");
	private $whyValues = array("We aren't that curious", "Don't worry, we won't ask", "We won't be nosy", "We won't make you tell us", "We'll leave that up to you");
	
	public function __construct($ID) {
		parent::__construct("ffi_ta_need", $ID);
		$this->rand = mt_rand(0, 4);
	}
	
	public function getWho() {
		global $essentials;
		
		if ($this->data) {
			return "<input disabled id=\"who\" name=\"who\" type=\"text\" value=\"" . htmlentities($essentials->user->user_firstname . " " . $essentials->user->user_lastname) . "\">";
		} else {
			return "<input disabled id=\"who\" name=\"who\" type=\"text\" value=\"" . htmlentities($essentials->user->user_firstname . " " . $essentials->user->user_lastname) . "\">";
		}
	}
	
	public function getWhat() {
		return "<input disabled id=\"what\" name=\"what\" type=\"text\" value=\"" . htmlentities($this->whatValues[$this->rand]) . "\">";
	}
	
	public function getWhen() {
		return "<input id=\"when\" name=\"when\" placeholder=\"When do you plan on leaving?\" type=\"text\" value=\"" . htmlentities($this->data ? $this->data[0]->Leaving . " " . $this->data[0]->LeavingTimeZone : "") . "\">";
	}
	
	public function getWhere() {
		return "<input class=\"rounded-left\" id=\"where-city\" name=\"where-city\" placeholder=\"Destination city\" type=\"text\" value=\"" . htmlentities($this->data ? $this->data[0]->City : "") . "\">

<select class=\"input-small rounded-right\" id=\"where-state\" name=\"where-state\">
<option selected value=\"\">State</option>
" . Destination_Manager::buildStatesDropDown($this->data ? $this->data[0]->State : "", "codes") . "
</select>";
	}
	
	public function getWhy() {
		return "<input disabled id=\"why\" name=\"why\" type=\"text\" value=\"" . htmlentities($this->whyValues[$this->rand]) . "\">";
	}
	
	public function getMales() {
		return "<input class=\"input-mini rounded-left\" id=\"males\" max=\"5\" min=\"0\" name=\"males\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->MalesPresent : "0") . "\">";
	}
	
	public function getFemales() {
		return "<input class=\"input-mini\" id=\"females\" max=\"5\" min=\"0\" name=\"females\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->FemalesPresent : "0") . "\">";
	}
	
	public function getDaysNotice() {
		return "<input class=\"input-mini\" id=\"days\" max=\"30\" min=\"0\" name=\"days\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->DaysNotice : "1") . "\">";
	}
	
	public function getMinutesWithin() {
		return "<input class=\"input-mini\" id=\"time\" max=\"120\" min=\"0\" name=\"minutes\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->MinutesWithin : "15") . "\">";
	}
	
	public function getGasMoney() {
		return "<input class=\"input-mini\" id=\"reimburse\" max=\"100\" min=\"0\" name=\"reimburse\" type=\"number\" value=\"" . htmlentities($this->data ? $this->data[0]->GasMoney : "5") . "\">";
	}
	
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
<input" . ($checkedYes ? " checked" : "") . " data-toggle=\"button\" id=\"luggage-yes\" name=\"luggage\" type=\"radio\" value=\"1\">
<label class=\"btn" . ($checkedYes ? " active" : "") . "\" for=\"luggage-yes\">Yes</label>
<input" . ($checkedNo ? " checked" : "") . " data-toggle=\"button\" id=\"luggage-no\" name=\"luggage\" type=\"radio\" value=\"0\">
<label class=\"btn" . ($checkedNo ? " active" : "") . "\" for=\"luggage-no\">No</label>
</div>";
	}
	
	public function getRecurrence() {
		$checkedYes = false;
		$checkedNo = false;
		
	//Determine which option should be checked
		if (!$this->data || ($this->data && $this->data[0]->Recurring == "1")) {
			$checkedYes = true;
		}
		
		if ($this->data && $this->data[0]->Recurring == "0") {
			$checkedNo = true;
		}
	
	//Return the generated output
		return "<div class=\"btn-group\" data-toggle=\"buttons-radio\">
<input" . ($checkedYes ? " checked" : "") . " data-toggle=\"button\" id=\"recurring-yes\" name=\"recurring\" type=\"radio\" value=\"1\">
<label class=\"btn" . ($checkedYes ? " active" : "") . "\" for=\"recurring-yes\" id=\"recurring-yes-label\">Yes</label>
<input" . ($checkedNo ? " checked" : "") . " data-toggle=\"button\" id=\"recurring-no\" name=\"recurring\" type=\"radio\" value=\"0\">
<label class=\"btn" . ($checkedNo ? " active" : "") . "\" for=\"recurring-no\" id=\"recurring-no-label\">No</label>
</div>";
	}
	
	public function getRecurrenceDays() {
		$class = "";
		$daysID = array("monday", "tuesday", "wednesday", "thursday", "friday");
        $daysText = array("M<span class=\"collapse\">onday</span>", "T<span class=\"collapse\">uesday</span>", "W<span class=\"collapse\">ednesday</span>", "T<span class=\"collapse\">hursday</span>", "F<span class=\"collapse\">riday</span>");
		$daysVal = array($this->data[0]->Monday, $this->data[0]->Tuesday, $this->data[0]->Wednesday, $this->data[0]->Thursday, $this->data[0]->Friday);
		$enabled = ($this->data && $this->data[0]->Recurring == "1") ? true : false;
		$return = "<div class=\"btn-group\" data-toggle=\"buttons-checkbox\">
";
		$state = "";
		
	//Since the list is long and the values to check are many, construct the list of days in a loop
		for ($i = 0; $i < 5; ++$i) {
			$class = $daysVal[$i] == "1" ? " active" : "";
			$class = !$enabled ? " disabled" : $class;
			$state = $daysVal[$i] == "1" ? " checked" : "";
			$state = !$enabled ? " disabled" : $state;
			
			$return .= "<input class=\"recurring-day\" data-toggle=\"button\"" . $state . " id=\"" . $daysID[$i] . "\" name=\"" . $daysID[$i] . "\" type=\"checkbox\">
<label class=\"btn recurring-label" . $class . "\" for=\"" . $daysID[$i] . "\">" . $daysText[$i] . "</label>
";
		}
		
		$return .= "</div>";
		return $return;
	}
	
	public function getEndDate() {
		return "<input " . ($this->data && $this->data[0]->Recurring == "1" ? "" : " disabled") . " id=\"until\" name=\"until\" placeholder=\"How long will you need a ride?\" type=\"text\" value=\"" . htmlentities($this->data ? $this->data[0]->EndDate : "") . "\">";
	}
	
	public function getComments() {
		return "<textarea id=\"comments\" name=\"comments\">" . ($this->data ? $this->data[0]->Comments : "") . "</textarea>";
	}
}
?>