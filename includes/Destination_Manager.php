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
			$states = $wpdb->get_results("SELECT * FROM `ffi_ta_states` ORDER BY `Name` ASC"); //Code order is the same as Name order
			
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
	
	public static function buildStatesDropDown($selectedValue, $valueStyle = "URL") {
		$names = self::getStateNames();
		$values = $valueStyle == "URL" ? self::getStateURLs() : self::getStateCodes();
		
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
}
?>