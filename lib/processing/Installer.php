<?php
/**
 * Travel Assistant Installer class
 *
 * This class will install the Travel Assistant by creating 
 * several tables in the database and populating some of them
 * with default values.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.processing
 * @since     1.0.0
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-blog-header.php");

class Installer {
/**
 * CONSTRUCTOR
 *
 * This constructor bootstraps the functionality of the class. It will do so
 * by calling a set of helper functions to build and populate the database.
 * 
 * @access public
 * @return void
 * @since  1.0.0
*/

	public function __construct() {
		$this->createRelations();
		$this->establishFKs();
		$this->populateDefaults();
	}
	
/**
 * This method will set up the database by creating the following
 * relations:
 *  - ffi_ta_apis
 *  - ffi_ta_cities
 *  - ffi_ta_need
 *  - ffi_ta_settings
 *  - ffi_ta_share
 *  - ffi_ta_states
 *  - ffi_ta_transactions
 * 
 * @access private
 * @return void
 * @since  1.0.0
*/
	
	private function createRelations() {
		global $wpdb;
		
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_ta_apis` (
						`ID` int(1) NOT NULL,
						`GoogleMaps` char(39) COLLATE utf8_unicode_ci NOT NULL,
						`MandrillKey` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
						PRIMARY KEY (`ID`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_ta_cities` (
						`ID` int(11) NOT NULL AUTO_INCREMENT,
						`City` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`State` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`Latitude` float NOT NULL,
						`Longitude` float NOT NULL,
						PRIMARY KEY (`ID`),
						KEY `FFI_TA_CITIES_REFERENCES_STATES_idx` (`State`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_ta_need` (
						`ID` int(11) NOT NULL AUTO_INCREMENT,
						`Person` bigint(20) unsigned NOT NULL,
						`Leaving` datetime NOT NULL,
						`LeavingTimeZone` enum('Pacific/Honolulu','America/Anchorage','America/Los_Angeles','America/Denver','America/Chicago','America/New_York') COLLATE utf8_unicode_ci DEFAULT 'America/New_York',
						`FromCity` int(11) NOT NULL,
						`ToCity` int(11) NOT NULL,
						`MalesPresent` int(1) NOT NULL,
						`FemalesPresent` int(1) NOT NULL,
						`Fulfilled` int(1) DEFAULT '0',
						`MinutesWithin` int(2) NOT NULL,
						`GasMoney` int(3) NOT NULL,
						`Luggage` int(1) NOT NULL,
						`Monday` int(1) DEFAULT '0',
						`Tuesday` int(1) DEFAULT '0',
						`Wednesday` int(1) DEFAULT '0',
						`Thursday` int(1) DEFAULT '0',
						`Friday` int(1) DEFAULT '0',
						`EndDate` date DEFAULT '0000-00-00',
						`Comments` longtext COLLATE utf8_unicode_ci NOT NULL,
						PRIMARY KEY (`ID`),
						KEY `FFI_TA_NEED_REFERENCES_CITY_idx` (`FromCity`),
						KEY `FFI_TA_NEED_TO_CITY_REFERENCES_CITY_idx` (`ToCity`),
						KEY `FFI_TA_NEED_REFERNECES_USERS_idx` (`Person`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_ta_settings` (
						`ID` int(1) NOT NULL,
						`EmailName` varchar(50) DEFAULT 'No Reply',
						`EmailAddress` varchar(50) NOT NULL,
						`TimeZone` enum('Pacific/Honolulu','America/Anchorage','America/Los_Angeles','America/Denver','America/Chicago','America/New_York') DEFAULT 'America/New_York',
						PRIMARY KEY (`ID`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_ta_share` (
						`ID` int(11) NOT NULL AUTO_INCREMENT,
						`Person` bigint(20) unsigned NOT NULL,
						`Leaving` datetime NOT NULL,
						`LeavingTimeZone` enum('Pacific/Honolulu','America/Anchorage','America/Los_Angeles','America/Denver','America/Chicago','America/New_York') COLLATE utf8_unicode_ci DEFAULT 'America/New_York',
						`FromCity` int(11) NOT NULL,
						`ToCity` int(11) NOT NULL,
						`Seats` int(11) NOT NULL,
						`MalesPresent` int(1) NOT NULL,
						`FemalesPresent` int(1) NOT NULL,
						`Fulfilled` int(2) DEFAULT '0',
						`MinutesWithin` int(2) NOT NULL,
						`GasMoney` int(3) NOT NULL,
						`Luggage` int(1) NOT NULL,
						`Monday` int(1) DEFAULT '0',
						`Tuesday` int(1) DEFAULT '0',
						`Wednesday` int(1) DEFAULT '0',
						`Thursday` int(1) DEFAULT '0',
						`Friday` int(1) DEFAULT '0',
						`EndDate` date DEFAULT '0000-00-00',
						`Comments` longtext COLLATE utf8_unicode_ci NOT NULL,
						PRIMARY KEY (`ID`),
						KEY `FFI_TA_SHARE_FROM_CITY_REFERENCES_CITY_idx` (`FromCity`),
						KEY `FFI_TA_SHARE_TO_CITY_REFERENCES_CITY_idx` (`ToCity`),
						KEY `FFI_TA_SHARE_REFERENCES_USERS_idx` (`Person`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_ta_states` (
						`Code` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`Name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`Image` varchar(13) NOT NULL,
						`District` int(1) NOT NULL,
						PRIMARY KEY (`Code`),
						UNIQUE KEY `Name_UNIQUE` (`Name`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_ta_transactions` (
						`ID` int(11) NOT NULL AUTO_INCREMENT,
						`Type` enum('ASSIST','REQUEST') NOT NULL,
						`FromCity` int(11) NOT NULL,
						`ToCity` int(11) NOT NULL,
						`Initiator` bigint(20) unsigned NOT NULL,
						`Poster` bigint(20) unsigned NOT NULL,
						`Leaving` datetime NOT NULL,
						`LeavingTimeZone` enum('Pacific/Honolulu','America/Anchorage','America/Los_Angeles','America/Denver','America/Chicago','America/New_York') DEFAULT 'America/New_York',
						`EndDate` date DEFAULT '0000-00-00',
						PRIMARY KEY (`ID`),
						KEY `FFI_TA_TRANSACTIONS_FROM_CITY_REFERENCES_CITY_idx` (`FromCity`),
						KEY `FFI_TA_TRANSACTIONS_TO_CITY_REFERENCES_CITY_idx` (`ToCity`),
						KEY `FFI_TA_TRANSACTIONS_REFERENCES_INITIATOR_idx` (`Initiator`),
						KEY `FFI_TA_TRANSACTIONS_REFERENCES_POSTER_idx` (`Poster`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1");
	}
	
/**
 * This method will continue to set up the database by establishing
 * foreign key relations between the tables created thus far, and also
 * with other Wordpress tables. The tables establshing foregin key
 * relationships include:
 *  - ffi_ta_cities
 *  - ffi_ta_need
 *  - ffi_ta_share
 *  - ffi_ta_transactions
 * 
 * @access private
 * @return void
 * @since  1.0.0
*/
	
	private function establishFKs() {
		global $wpdb;
		
		$wpdb->query("ALTER TABLE `ffi_ta_cities`
	ADD CONSTRAINT `FFI_TA_CITIES_REFERENCES_STATES` FOREIGN KEY (`State`) REFERENCES `ffi_ta_states` (`Code`) ON DELETE NO ACTION ON UPDATE NO ACTION");
		
		$wpdb->query("ALTER TABLE `ffi_ta_need`
	ADD CONSTRAINT `FFI_TA_NEED_FROM_CITY_REFERENCES_CITY` FOREIGN KEY (`FromCity`) REFERENCES `ffi_ta_cities` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	ADD CONSTRAINT `FFI_TA_NEED_REFERNECES_USERS` FOREIGN KEY (`Person`) REFERENCES `wp_users` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
	ADD CONSTRAINT `FFI_TA_NEED_TO_CITY_REFERENCES_CITY` FOREIGN KEY (`ToCity`) REFERENCES `ffi_ta_cities` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION");
						  
		$wpdb->query("ALTER TABLE `ffi_ta_share`
	ADD CONSTRAINT `FFI_TA_SHARE_REFERENCES_USERS` FOREIGN KEY (`Person`) REFERENCES `wp_users` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
	ADD CONSTRAINT `FFI_TA_SHARE_FROM_CITY_REFERENCES_CITY` FOREIGN KEY (`FromCity`) REFERENCES `ffi_ta_cities` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	ADD CONSTRAINT `FFI_TA_SHARE_TO_CITY_REFERENCES_CITY` FOREIGN KEY (`ToCity`) REFERENCES `ffi_ta_cities` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION");
	
		$wpdb->query("ALTER TABLE `ffi_ta_transactions`
  ADD CONSTRAINT `FFI_TA_TRANSACTIONS_FROM_CITY_REFERENCES_CITY` FOREIGN KEY (`FromCity`) REFERENCES `ffi_ta_cities` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FFI_TA_TRANSACTIONS_REFERENCES_INITIATOR` FOREIGN KEY (`Initiator`) REFERENCES `wp_users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FFI_TA_TRANSACTIONS_REFERENCES_POSTER` FOREIGN KEY (`Poster`) REFERENCES `wp_users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FFI_TA_TRANSACTIONS_TO_CITY_REFERENCES_CITY` FOREIGN KEY (`ToCity`) REFERENCES `ffi_ta_cities` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION");
	}
	
/**
 * This method will continue to set up the database by populating the
 * following tables with a default set of values, if they are not already
 * populated by a previous installation:
 *  - ffi_ta_apis
 *  - ffi_ta_settings
 *  - ffi_ta_states
 * 
 * @access private
 * @return void
 * @since  1.0.0
*/
	
	private function populateDefaults() {
		global $wpdb;
		
		if (!count($wpdb->get_results("SELECT * FROM `ffi_ta_apis`"))) {
			$wpdb->query("INSERT INTO `ffi_ta_apis` (`ID`, `GoogleMaps`, `MandrillKey`) VALUES (1, '', '')");
		}
		
		if (!count($wpdb->get_results("SELECT * FROM `ffi_ta_settings`"))) {
			$wpdb->query("INSERT INTO `ffi_ta_settings` (`ID`, `EmailName`, `EmailAddress`, `TimeZone`) VALUES (1, 'No Reply', 'example@changeme.com', 'America/New_York')");
		}
		
		if (!count($wpdb->get_results("SELECT * FROM `ffi_ta_states`"))) {
			$wpdb->query("INSERT INTO `ffi_ta_states` (`Code`, `Name`, `Image`, `District`) VALUES
							('AK', 'Alaska', 'alaska', 0),
							('AL', 'Alabama', 'south', 0),
							('AR', 'Arkansas', 'south', 0),
							('AZ', 'Arizona', 'west', 0),
							('CA', 'California', 'california', 0),
							('CO', 'Colorado', 'west', 0),
							('CT', 'Connecticut', 'northeast', 0),
							('DC', 'District of Columbia', 'washington-dc', 1),
							('DE', 'Delaware', 'south', 0),
							('FL', 'Florida', 'south', 0),
							('GA', 'Georgia', 'south', 0),
							('HI', 'Hawaii', 'hawaii', 0),
							('IA', 'Iowa', 'midwest', 0),
							('ID', 'Idaho', 'west', 0),
							('IL', 'Illinois', 'midwest', 0),
							('IN', 'Indiana', 'midwest', 0),
							('KS', 'Kansas', 'midwest', 0),
							('KY', 'Kentucky', 'south', 0),
							('LA', 'Louisiana', 'south', 0),
							('MA', 'Massachusetts', 'northeast', 0),
							('MD', 'Maryland', 'south', 0),
							('ME', 'Maine', 'northeast', 0),
							('MI', 'Michigan', 'midwest', 0),
							('MN', 'Minnesota', 'midwest', 0),
							('MO', 'Missouri', 'midwest', 0),
							('MS', 'Mississippi', 'south', 0),
							('MT', 'Montana', 'west', 0),
							('NC', 'North Carolina', 'south', 0),
							('ND', 'North Dakota', 'midwest', 0),
							('NE', 'Nebraska', 'midwest', 0),
							('NH', 'New Hampshire', 'northeast', 0),
							('NJ', 'New Jersey', 'northeast', 0),
							('NM', 'New Mexico', 'west', 0),
							('NV', 'Nevada', 'west', 0),
							('NY', 'New York', 'new-york', 0),
							('OH', 'Ohio', 'midwest', 0),
							('OK', 'Oklahoma', 'south', 0),
							('OR', 'Oregon', 'west', 0),
							('PA', 'Pennsylvania', 'pennsylvania', 0),
							('RI', 'Rhode Island', 'northeast', 0),
							('SC', 'South Carolina', 'south', 0),
							('SD', 'South Dakota', 'midwest', 0),
							('TN', 'Tennessee', 'south', 0),
							('TX', 'Texas', 'south', 0),
							('UT', 'Utah', 'west', 0),
							('VA', 'Virginia', 'south', 0),
							('VT', 'Vermont', 'northeast', 0),
							('WA', 'Washington', 'west', 0),
							('WI', 'Wisconsin', 'midwest', 0),
							('WV', 'West Virginia', 'south', 0),
							('WY', 'Wyoming', 'west', 0)");
		}
	}
}
?>