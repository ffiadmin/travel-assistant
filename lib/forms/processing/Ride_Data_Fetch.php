<?php
/**
 * Form data fetching class
 *
 * This class is used to fetch data from the MySQL database from
 * two identically structured tables:
 *  - ffi_ta_need
 *  - ffi_ta_share
 *
 * This class exists solely to be extended, so that classes which
 * will build the forms will be able to use this class to fetch data
 * and fill the form with its appropriate values.
 *
 * @abstract
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.form.processing
 * @since     v1.0 Dev
*/

namespace FFI\TA;

abstract class Ride_Data_Fetch {
/**
 * Hold the results of the SQL query.
 *
 * @access protected
 * @type   boolean|object<mixed>
*/
	
	protected $data;
	
/**
 * CONSTRUCTOR
 *
 * This method will:
 *  - Grab the name of the table which which data should be fetched
 *  - Run the SQL query to fetch data
 *  - Share the data with child classes
 * 
 * @access public
 * @param  string   $tableName The name of the table from which data should be fetched
 * @param  int      $ID        The ID of the tuple to fetch from the database
 * @param  int      $userID    The ID of the user requesting this information
 * @return void
 * @since  v1.0 Dev
*/

	public function __construct($tableName, $ID, $userID) {
		global $wpdb;
		
		if ($ID) {
			$tableName = esc_sql($tableName);
			$this->data = $wpdb->get_results($wpdb->prepare("SELECT {$tableName}.*, ffi_ta_cities.City AS `FromCityName`, ffi_ta_cities.State AS `FromState`, cities.City AS `ToCityName`, cities.State AS `ToState` FROM `{$tableName}` LEFT JOIN `wp_usermeta` ON {$tableName}.Person = wp_usermeta.user_id LEFT JOIN `ffi_ta_cities` ON {$tableName}.FromCity = ffi_ta_cities.ID LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `cities` ON {$tableName}.ToCity = cities.ID WHERE {$tableName}.ID = %d AND {$tableName}.Person = %d LIMIT 1", $ID, $userID));
			
		//SQL returned 0 tuples
			if (!count($this->data)) {
				$this->data = NULL;
			}
		} else {
			$this->data = false;
		}
	}
}
?>
