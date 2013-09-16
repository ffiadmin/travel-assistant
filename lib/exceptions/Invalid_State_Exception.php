<?php
/**
 * Invalid State Exception class
 *
 * This is a custom exception class intended to be thrown
 * when the user attempts to access information about a
 * non-existant US state.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends   FFI\TA\Base_Exception
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.exceptions
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(__FILE__) . "/Base_Exception.php");

final class Invalid_State_Exception extends Base_Exception {}
?>
