<?php
/**
 * Update Plugin APIs Processing class
 *
 * This class is used to:
 *  - Determine whether or not a user has sumbitted the plugin APIs
 *    form.
 *  - Validate all incoming data.
 *  - Upadate the plugin's APIs.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends   FFI\TA\Processor_Base
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.processing
 * @since     1.0.0
*/

namespace FFI\TA;

require_once(dirname(__FILE__) . "/Processor_Base.php");

class API_Process extends Processor_Base {
/**
 * Hold the Google API key.
 *
 * @access private
 * @type   string
*/
	
	private $google;
	
	
/**
 * Hold the Mandrill API Key.
 *
 * @access private
 * @type   string
*/
	
	private $mandrill;
	
/**
 * CONSTRUCTOR
 *
 * This method will call helper methods to:
 *  - Determine whether or not a user has sumbitted the plugin APIs
 *    form.
 *  - Validate all incoming data.
 *  - Upadate the plugin's APIs.
 * 
 * @access public
 * @return void
 * @since  1.0.0
*/
	
	public function __construct() {
		parent::__construct();
		$this->hasAdminPrivileges();
		
	//Check to see if the user has submitted the form
		if ($this->userSubmittedForm()) {
			$this->validateAndRetain();
			$this->update();
		}
	}
	
/**
 * Determine whether or not the user has submitted the form by
 * checking to see if all required data is present (but not
 * necessarily valid).
 *
 * @access private
 * @return bool     Whether or not the user has submitted the form
 * @since  1.0.0
*/
	
	private function userSubmittedForm() {
		if (is_array($_POST) && count($_POST) &&
			isset($_POST['google']) && isset($_POST['mandrill']) &&
			!empty($_POST['google']) && !empty($_POST['mandrill'])) {
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
 * @since  1.0.0
 * @throws Validation_Failed Thrown when ANY portion of the validation process fails
*/

	private function validateAndRetain() {
	//Retain the Google API key
		$this->google = $_POST['google'];
		
	//Retain the Mandrill API key	
		$this->mandrill = $_POST['mandrill'];
	}
	
/**
 * Update the plugin's APIs.
 *
 * @access private
 * @return void
 * @since  1.0.0
*/

	private function update() {
		global $wpdb;
		
		$wpdb->update("ffi_ta_apis", array (
			"GoogleMaps"  => $this->google,
			"MandrillKey" => $this->mandrill
		), array (
			"ID" => 1
		), array (
			"%s", "%s"
		), array (
			"%d"
		));
		
		wp_redirect(admin_url() . "admin.php?page=travel-assistant/admin/api.php&updated=1");
		exit;
	}
}
?>