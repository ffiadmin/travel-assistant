<?php
/**
 * Processor Base class
 *
 * This is an abstract class which is designed to provide a basic
 * set of methods which is commonly useful when building a processor
 * class.
 * 
 * Its abilities include:
 *  - Checking and storing the user's login status.
 *  - Log the user into his or her account. This procedure will
 *    automatically store the user's account information.
 *  - Fetching and storing the plugin's settings from the database.
 *  - Validating if the value of an integer lies between two values.
 *
 * @abstract
 * @author     Oliver Spryn
 * @copyright  Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license    MIT
 * @namespace  FFI\TA
 * @package    lib.processing
 * @since      1.0
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-blog-header.php");
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-includes/pluggable.php");
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-includes/user.php");
require_once(dirname(dirname(__FILE__)) . "/exceptions/Login_Failed.php");

abstract class Processor_Base {
/**
 * Hold the user's login status.
 *
 * @access protected
 * @type   bool
*/
	
	protected $loggedIn = false;
	
/**
 * Hold the data about the user.
 *
 * @access protected
 * @type   WP_User
*/
	
	protected $user;
	
/**
 * Hold the plugin settings fetched from a database.
 *
 * @access protected
 * @type   object<mixed>
*/

	protected $settings;
	
/**
 * CONSTRUCTOR
 *
 * Determine whether or not the user is logged in and store the result.
 * 
 * @access protected
 * @return void
 * @since  1.0
*/
	
	protected function __construct() {
		$this->loggedIn = is_user_logged_in();
	}
	
/**
 * Log in the user. The user's account data will be automatically
 * retained, even if they were previously logged in.
 *
 * If this function is called, it will expect the credentials either 
 * from two $_POST arguments, like this:
 *
 *     - Username value: $_POST['username']
 *     - Password value: $_POST['password']
 *
 * ... or, the username and password can be passed into this method
 * as parameters, like:
 *  
 *     $this->login($username, $password)
 *
 * This function will log the user as if they had NOT checked the 
 * "Remember Me" checkbox.
 *
 * @access protected
 * @param  string    $username The user's username
 * @param  string    $password The user's plain text password
 * @return void
 * @since  1.0
 * @throws Login_Failed Thrown if a user's login credentials are invalid
*/
	
	protected function login() {
		if (!$this->loggedIn) {
			$args = func_get_args();
			
		//Was the username and password passed as arguments, or should we expect them in $_POST?
			if (count($args) == 2) {
				$credentials = array(
					"user_login"    => func_get_arg(0),
					"user_password" => func_get_arg(1),
					"remember"      => false
				);
			} else {
				$credentials = array(
					"user_login"    => $_POST['username'],
					"user_password" => $_POST['password'],
					"remember"      => false
				);
			}
			
		//Log the user in and retain the account information
			$this->user = wp_signon($credentials, false);
			
			if (is_wp_error($this->buyer)) {
				throw new Login_Failed("Your username or password is invalid");
			}
		} else {
			$this->user = wp_get_current_user();
		}
	}
	
/**
 * Fetch the plugin settings from the database and make the data
 * available to the rest of the class.
 *
 * @access protected
 * @return void
 * @since  1.0
*/

	protected function fetchSettings($tableName = "settings") {
		global $wpdb;

		$this->settings = $wpdb->get_results("SELECT * FROM `" . $tableName . "`");
	}
	
/**
 * Check to see if a particular integer value falls between a specified
 * range, including the extrema values.
 * 
 * @access protected
 * @param  int      $value The integer value to check
 * @param  int      $min   The minimum value the integer may equal
 * @param  int      $max   The maximum value the integer may equal
 * @return bool            Whether or not the integer falls within the specified range
 * @since  1.0
*/
	
	protected function intBetween($value, $min, $max) {
		if (!is_numeric($value)) {
			return false;
		}
		
		$value = intval($value);
		
	//Check the integer extrema
		return ($value >= $min && $value <= $max);
	}
}
?>