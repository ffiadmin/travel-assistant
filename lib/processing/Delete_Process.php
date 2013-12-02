<?php
/**
 * Delete Ride Processing class
 *
 * This class is used to:
 *  - Determine whether or not a user has sumbitted the delete trip
 *    form.
 *  - Validate all incoming data.
 *  - Delete the trip.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends   FFI\TA\Processor_Base
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.processing
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(__FILE__) . "/Processor_Base.php");
require_once(dirname(dirname(__FILE__)) . "/exceptions/Validation_Failed.php");

class Delete_Process extends Processor_Base {
/**
 * Hold the ID of the trip to delete.
 *
 * @access private
 * @type   int
*/

	private $tripID;
	
/**
 * Hold the type of trip to delete.
 *
 * @access private
 * @type   string
*/

	private $type;
	
/**
 * CONSTRUCTOR
 *
 * This method will call helper methods to:
 *  - Determine whether or not a user has sumbitted the delete trip
 *    form.
 *  - Validate all incoming data.
 *  - Delete the trip.
 * 
 * @access public
 * @return void
 * @since  1.0
*/

	public function __construct() {
		parent::__construct();
	
	//Check to see if the user has submitted the form
		if ($this->userSubmittedForm()) {
			$this->validateAndRetain();
			$this->delete();
		}
	}
	
/**
 * Determine whether or not the user has submitted the form by
 * checking to see if all required data is present (but not 
 * necessarily valid).
 * 
 * @access private
 * @return bool         Whether or not the user has submitted the form
 * @since  1.0
*/
	
	private function userSubmittedForm() {
		if (is_array($_POST) && count($_POST) && 
			isset($_POST['ID']) && isset($_POST['type']) && 
			is_numeric($_POST['ID']) && !empty($_POST['type'])) {
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
 * @return void
 * @since  1.0
 * @throws Validation_Failed Thrown when ANY portion of the validation process fails
*/
	
	private function validateAndRetain() {
		global $wpdb;
		
	//Check to see if the user is logged in
		if (!is_user_logged_in()) {
			throw new Validation_Failed("You are not logged in");
		}
		
	//Get the trip data
		if ($_POST['type'] == "need") {
			$this->type = "ffi_ta_need";
		} elseif ($_POST['type'] == "share") {
			$this->type = "ffi_ta_share";
		} else {
			throw new Validation_Failed("This trip type does not exist");
		}
	
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $this->type . "` WHERE `ID` = %d", $_POST['ID']));
		
	//Check to see if the trip already exists
		if (!count($data)) {
			throw new Validation_Failed("This trip does not exist");
		}
		
	//Check to see if the user actually posted this trip
		$this->retainUserInfo();
		
		if ($data[0]->Person != $this->user->ID) {
			throw new Validation_Failed("You do not own this trip");
		}
	
		$this->tripID = $_POST['ID'];
	}
	
/**
 * Use the values validated and retained in memory by the 
 * validateAndRetain() method to delete an existing entry in the 
 * database.
 * 
 * @access private
 * @return void
 * @since  1.0
*/
	
	private function delete() {
		global $wpdb;
		
		$wpdb->delete($this->type, array (
			"ID" => $this->tripID
		), array (
			"%d"
		));
	}
}
?>