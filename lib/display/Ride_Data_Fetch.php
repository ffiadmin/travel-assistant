<?php
/**
 * Form Data Fetching class
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
 * @package   lib.display
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(dirname(__FILE__)) . "/exceptions/No_Data_Returned.php");
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-blog-header.php");

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
 *  - Grab the name of the table which which data should be fetched.
 *  - Run the SQL query to fetch data.
 *  - Share the data with child classes.
 * 
 * @access public
 * @param  string           $tableName The name of the table from which data should be fetched
 * @param  int              $ID        The ID of the tuple to fetch from the database
 * @param  int              $userID    The ID of the user requesting this information
 * @return void
 * @since  1.0
 * @throws No_Data_Returned            Thrown if the given table and ID yield no results
*/

	public function __construct($tableName, $ID, $userID) {
		global $wpdb;
		
	//Don't continue if the ID is zero
		if ($ID == 0) {
			$this->data = false;
			return;
		}
		
	//Try to fetch the data
		$tableName = esc_sql($tableName);
		$temp = $wpdb->get_results($wpdb->prepare("SELECT {$tableName}.*, ffi_ta_cities.City AS `FromCityName`, ffi_ta_cities.State AS `FromState`, cities.City AS `ToCityName`, cities.State AS `ToState` FROM `{$tableName}` LEFT JOIN `wp_usermeta` ON {$tableName}.Person = wp_usermeta.user_id LEFT JOIN `ffi_ta_cities` ON {$tableName}.FromCity = ffi_ta_cities.ID LEFT JOIN (SELECT * FROM `ffi_ta_cities`) `cities` ON {$tableName}.ToCity = cities.ID WHERE {$tableName}.ID = %d AND {$tableName}.Person = %d LIMIT 1", $ID, $userID));
		$this->data = $temp[0];
			
	//SQL returned 0 tuples
		if (!count($this->data)) {
			throw new No_Data_Returned("No trip information exists for the given ID");
		}
	}
}
?>