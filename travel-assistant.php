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
	define("FFI\TA\CDN", false);
	define("FFI\TA\FAKE_ADDR", get_site_url() . "/travel-assistant/");
	define("FFI\TA\PATH", plugin_dir_path(__FILE__));
	define("FFI\TA\REAL_ADDR", get_site_url() . "/wp-content/plugins/travel-assistant/");
	define("FFI\TA\RESOURCE_PATH", (CDN ? "//ffistatic.appspot.com/sga" : site_url()) . "/wp-content/plugins/travel-assistant/");
	define("FFI\TA\URL_ACTIVATE", "travel-assistant");
	
	define("FFI\TA\ENABLED", true);
	define("FFI\TA\NAME", "Travel Assistant");
	
//Instantiate the Interception_Manager
	if(!is_admin()) {
		require_once(PATH . "lib/Interception_Manager.php");
		$intercept = new Interception_Manager();
		$intercept->registerException("browse", "browse/index.php", 2);
		$intercept->registerException("browse", "browse/index.php", 2, 3);
		$intercept->registerException("need-a-ride", "need-a-ride/index.php", 2);
		$intercept->registerException("share-a-ride", "share-a-ride/index.php", 2);
		$intercept->registerException("trips/available", "trips/available/index.php", 3);
		$intercept->registerException("trips/needed", "trips/needed/index.php", 3);
		$intercept->highlightNavLink(URL_ACTIVATE);
		$intercept->go();
	} else {
		function addMenuItems() {
   			global $submenu;
			
		//Add the desired pages to the Wordpress Administration menu
			add_menu_page("Settings", "Travel Assistant", "update_core", "travel-assistant/admin/settings.php");
			add_submenu_page("travel-assistant/admin/settings.php", "API Management", "API Management", "update_core", "travel-assistant/admin/api.php");
			
		//Modify the name of the first sub-menu item
			$submenu['travel-assistant/admin/settings.php'][0][0] = "Settings";
		}

		add_action("admin_menu", "FFI\\TA\\addMenuItems");
	}
?>
