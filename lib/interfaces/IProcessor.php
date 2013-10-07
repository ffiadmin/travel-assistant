<?php
/**
 * Processor Declaration interface
 *
 * This interface is designed to enforce the abilities and 
 * basic interface each of the custom processor classes must
 * possess within this plugin.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.interfaces
 * @since     1.0
*/

namespace FFI\TA;

interface IProcessor {
/**
 * Determine whether or not the user has submitted the form by
 * checking to see if all required data is present (but not
 * necessarily valid).
 *
 * @access private
 * @return bool     Whether or not the user has submitted the form
 * @since  1.0
*/
	
	private function userSubmittedForm();
	
/**
 * Determine whether or not all of the required information has been
 * submitted and is completely valid. If validation has succeeded, then
 * store the data within the class for later database entry.
 * 
 * @access private
 * @return boolean           Whether or not validation has succeeded
 * @since  1.0
 * @throws Validation_Failed Thrown when ANY portion of the validation process fails
*/
	
	private function validateAndRetain();
}
?>