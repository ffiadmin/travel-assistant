<?php
namespace FFI\TA;

class Destination_Manager {
	private static $previousSelectedOption = "";
	private static $stateCodes = array();
	private static $stateMenu = "";
	private static $stateNames = array();
	private static $stateURLs = array();
	
	public static function getStateNames() {
		global $wpdb;
		
	//Has this function already been called and make a local copy of the state names?
		if (empty(self::$stateNames)) {
			$states = $wpdb->get_results("SELECT * FROM `ffi_ta_states` ORDER BY `Name` ASC");
			
			foreach($states as $state) {
				array_push(self::$stateNames, $state->Name);
			}
		}
		
		return self::$stateNames;
	}
	
	public static function getStateCodes() {
		global $wpdb;
		
	//Has this function already been called and make a local copy of the state codes?
		if (empty(self::$stateCodes)) {
			$states = $wpdb->get_results("SELECT * FROM `ffi_ta_states` ORDER BY `Code` ASC"); //Code order is the same as Name order
			
			foreach($states as $state) {
				array_push(self::$stateCodes, $state->Code);
			}
		}
		
		return self::$stateCodes;
	}
	
	public static function getStateURLs() {
		global $wpdb;
		
	//Has this function already been called and make a local copy of the state URLs?
		if (empty(self::$stateURLs)) {
			foreach(self::getStateNames() as $state) {
				array_push(self::$stateURLs, self::URLPurify($state));
			}
		}
		
		return self::$stateURLs;
	}
	
	public static function buildStatesDropDown($selectedValue, $valueStyle = "name") {
		$names = $valueStyle == "name" ? self::getStateNames() : self::getStateCodes();
		$values = self::getStateCodes();
		
		if (empty(self::$stateMenu) || self::$previousSelectedOption != $selectedValue) {
			self::$stateMenu = "";
			
			for ($i = 0; $i < count($names); ++$i) {
				if ($selectedValue == $values[$i]) {
					self::$stateMenu .= "<option selected value=\"" . htmlentities($values[$i]) . "\">" . $names[$i] . "</option>\n";
				} else {
					self::$stateMenu .= "<option value=\"" . htmlentities($values[$i]) . "\">" . $names[$i] . "</option>\n";
				}
			}
			
			mb_substr(self::$stateMenu, 0, -1); //Remove the trailing "\n"
		}
		
		return self::$stateMenu;
	}
	
	public static function URLPurify($name) {
		$name = preg_replace("/[^a-zA-Z0-9\s]/", "", $name); //Remove all non-alphanumeric characters, except for spaces
		$name = preg_replace("/[\s]/", "-", $name);          //Replace remaining spaces with a "-"
		return strtolower($name);
	}
	
	public static function getStatesList($columnLength = 17) {
		global $wpdb;
		global $essentials;
			
	//Fetch the data from the database
		$states = $wpdb->get_results("SELECT ffi_ta_states.Name, COALESCE(Needs.Needs, 0) AS `Needs`, COALESCE(Shares.Shares, 0) AS `Shares` FROM `ffi_ta_states` LEFT JOIN ( SELECT DISTINCT ffi_ta_cities.State, COUNT(ffi_ta_cities.State) AS `Shares` FROM `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.City = ffi_ta_cities.ID GROUP BY ffi_ta_cities.State ) `Shares` ON ffi_ta_states.Code = Shares.State LEFT JOIN ( SELECT DISTINCT ffi_ta_cities.State, COUNT(ffi_ta_cities.State) AS `Needs` FROM `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.City = ffi_ta_cities.ID GROUP BY ffi_ta_cities.State ) `Needs` ON ffi_ta_states.Code = Needs.State ORDER BY ffi_ta_states.Name ASC");
		$count = 0;
		$return = "<ul class=\"states\">
<li>
<ul>
";
		foreach($states as $state) {
		//Should a new column be started?
			if ($count % $columnLength == 0 && $count != 0) {
				$return .= "</ul>
</li>

<li>
<ul>
";
			}
			
			
		//Echo the list item content
			$return .= "<li>
<a href=\"" . $essentials->friendlyURL("browse/" . self::URLPurify($state->Name)) . "\">
<h3>" . $state->Name . "</h3>
<p class=\"needed" . ($state->Needs > 0 ? " highlight" : "") . "\">" . $state->Needs . "  <span>" . ($state->Needs == 1 ? "Need" : "Needs") . "</span></p>
<p class=\"shares" . ($state->Shares > 0 ? " highlight" : "") . "\">" . $state->Shares . " <span>" . ($state->Shares == 1 ? "Ride" : "Rides") . "</span></p>
</a>
</li>
";

			++$count;
		}
		
		$return .= "</ul>
</li>
</ul>";
		
		return $return;
	}
	
	public static function getTotals() {
		global $wpdb;
			
	//Fetch the data from the database
		$totals = $wpdb->get_results("SELECT COUNT(ffi_ta_need.ID) AS Need, (SELECT COUNT(ffi_ta_share.ID) FROM ffi_ta_share) AS Shares FROM ffi_ta_need");
		
		return array("needs" => $totals[0]->Need, "shares" => $totals[0]->Shares);
	}
}
?>