<?php
/**
 * Network Connection Error Exception class
 *
 * This is a custom exception class intended to be thrown
 * when the server is attempting to communicate with another
 * server (via cURL) and the connection attempt fails.
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

final class Network_Connection_Error extends Base {}
?>
