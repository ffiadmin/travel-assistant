<?php
/**
 * User Login Failed Exception class
 *
 * This is a custom exception class intended to be thrown
 * when the application attempts to log an individual in 
 * using a username and password supplied by the user, and
 * their credentials are invalid, or a user is logged in with
 * insufficient privileges.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends   FFI\TA\Base
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.exceptions
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(__FILE__) . "/Base.php");

final class Login_Failed extends Base {}
?>