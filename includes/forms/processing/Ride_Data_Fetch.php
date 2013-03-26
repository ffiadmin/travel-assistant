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
 * @package   includes.form.processing
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
 * @return void
 * @since  v1.0 Dev
*/

	public function __construct($tableName, $ID) {
		global $wpdb;
		
		if ($ID) {
			$tableName = esc_sql($tableName);
			$this->data = $wpdb->get_results($wpdb->prepare("SELECT {$tableName}.ID, `Leaving`, `LeavingTimeZone`, ffi_ta_cities.City AS `City`, ffi_ta_cities.State AS `State`, `MalesPresent`, `FemalesPresent`, `DaysNotice`, `MinutesWithin`, `GasMoney`, `Luggage`, `Monday`, `Tuesday`, `Wednesday`, `Thursday`, `Friday`, `EndDate`, `Comments` FROM `{$tableName}` LEFT JOIN `wp_usermeta` ON {$tableName}.Person = wp_usermeta.user_id LEFT JOIN `ffi_ta_cities` ON {$tableName}.City = ffi_ta_cities.ID WHERE ffi_ta_need.ID = %d LIMIT 1", $ID));
			
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