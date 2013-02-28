<?php
/**
 * AJAX Data Fetching base class
 *
 * This abstract class is used to gain access to core Wordpress methods
 * and variables (such as $wpdb) and also prevent Wordpress from showing 
 * a template with its output.
 * 
 * A child class will extend this class, to generate its own content 
 * without any template content. This is generally helpful for pages
 * which are designed for AJAX calls, and will output only, say, JSON
 * or XML content. These pages are not intended to be viewed by the end
 * user.
 *
 * @abstract
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   includes
 * @since     v1.0 Dev
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/wp-blog-header.php");

abstract class AJAX_Data_Fetch {
/**
 * CONSTRUCTOR
 *
 * This method will:
 *  - Disable Wordpress from displaying template content from the
 *    current theme
 *
 * @access public
 * @return void
 * @since  v1.0 Dev
*/
	public function __construct() {
		define("WP_USE_THEMES", false);
	}
	
/**
 * This function will output an HTTP header indicating the MIME type
 * of the page's contents
 *
 * @access public
 * @param  string   $mime The MIME type of the page's contents
 * @return void
 * @since  v1.0 Dev
*/
	
	public function setMimeTypeHeader($mime) {
		header("Content-type: " . $mime);
	}
}