<?php
/**
 * Mandrill Send Email Failed Exception class
 *
 * This is a custom exception class intended to be thrown
 * when a connection to the Mandrill service is successful,
 * but Mandrill fails to send the desired email.
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

final class Mandrill_Send_Failed extends Base {}
?>