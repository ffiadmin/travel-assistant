<?php
/**
 * Database No Data Returned Exception class
 *
 * This is a custom exception class intended to be thrown
 * when an SQL query returns 0 tuples.
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

final class No_Data_Returned extends Base {}
?>