<?php
/*
Plugin Name: Travel Assistant
Plugin URI: http://forwardfour.com/travel-assistant
Description: This is a plugin which is used to organize student travels to and from Grove City College. Commutes can benefit from it by organizing regular carpools for their daily commute with others from their hometown. Resident students can use it to organize trips with other students to and from home for breaks.
Version: 1.0
Author: Oliver Spryn
Author URI: http://forwardfour.com/
License: MIT
*/

	namespace FFI\TA;
	
//Create plugin-specific global definitions
	define("FFI\TA\FAKE_ADDR", get_site_url() . "/travel-assistant/");
	define("FFI\TA\PATH", plugin_dir_path(__FILE__));
	define("FFI\TA\REAL_ADDR", get_site_url() . "/wp-content/plugins/travel-assistant/");
	define("FFI\TA\URL_ACTIVATE", "travel-assistant");
	
	define("FFI\TA\ACTIVE", true);
	define("FFI\TA\NAME", "Travel Assistant");
	
//Instantiate the Interception_Manager
	if(!is_admin()) {
		require_once(PATH . "includes/Interception_Manager.php");
		new Interception_Manager();
	}
?>