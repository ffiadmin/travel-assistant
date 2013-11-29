<?php
/**
 * Trip Request Processing class
 *
 * This class is used to:
 *  - Determine whether or not a user has submitted a trip request.
 *  - Determine what kind of request is being submitted, either 
 *    requesting a seat for a ride which is being shared, or requesting
 *    to fulfill a needed ride. 
 *  - Validate all incoming data.
 *  - Log the transaction and notify the Initiator (the person triggering
 *    this action) and the Poster (the person who posted the request or
 *    ride availability).
 *
 * DEFINITIONS:
 *  - Initiator: The person who triggered this action by pressing either
 *               the "I Need This Ride" or "I Can Help" buttons.
 *  - Poster:    The person who posted this information online, hoping
 *               that someone else will find his or her posting.
 *
 * @author     Oliver Spryn
 * @copyright  Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends    Processor_Base
 * @license    MIT
 * @namespace  FFI\TA
 * @package    lib.processing
 * @since      1.0
*/

namespace FFI\TA;

require_once(dirname(__FILE__) . "/Processor_Base.php");
require_once(dirname(dirname(__FILE__)) . "/display/Trip_Info.php");
require_once(dirname(dirname(__FILE__)) . "/emails/Email_Initiator.php");
require_once(dirname(dirname(__FILE__)) . "/emails/Email_Poster.php");
require_once(dirname(dirname(__FILE__)) . "/exceptions/Validation_Failed.php");

class Trip extends Processor_Base {
/**
 * Hold the user's comments.
 *
 * @access private
 * @type   string
*/
	
	private $comments;

/**
 * Hold the ID of the trip.
 *
 * @access private
 * @type   int
*/
	
	private $ID;
		
/**
 * Hold the trip information.
 *
 * @access private
 * @type   string
*/
	
	private $info;
	
/**
 * Hold the mode of the trip.
 *
 * @access private
 * @type   string
*/
	
	private $mode;
	
/**
 * Hold the account information for the poster.
 *
 * @access private
 * @type   WP_User
*/
	
	private $poster;
	
/**
 * CONSTRUCTOR
 *
 * This method will call helper methods to:
 *  - Determine whether or not a user has submitted a trip request.
 *  - Determine what kind of request is being submitted, either 
 *    requesting a seat for a ride which is being shared, or requesting
 *    to fulfill a needed ride. 
 *  - Validate all incoming data.
 *  - Log the transaction and notify the Initiator (the person triggering
 *    this action) and the Poster (the person who posted the request or
 *    ride availability).
 * 
 * @access public
 * @return void
 * @since  1.0
*/

	public function __construct() {
		parent::__construct();
		
	//Check to see if the user has submitted the form
		if ($this->userSubmittedForm()) {
			$this->login();
			$this->fetchSettings("ffi_ta_settings");
			$this->validateAndRetain();
			$this->sendEmails();
			$this->updateLocal();
		}
	}
	
/**
 * Determine whether or not the user has submitted the form by
 * checking to see if all required data is present (but not
 * necessarily valid).
 *
 * @access private
 * @return bool     Whether or not the user has submitted the form
 * @since  1.0
*/
	
	private function userSubmittedForm() {
		if (is_array($_POST) && count($_POST) &&
			isset($_POST['id']) && isset($_POST['mode']) && ($this->loggedIn || (!$this->loggedIn && isset($_POST['username']) && isset($_POST['password']))) &&
			is_numeric($_POST['id']) && !empty($_POST['mode']) && ($this->loggedIn || (!$this->loggedIn && !empty($_POST['username']) && !empty($_POST['password'])))) {
			return true;
		}
		
		return false;
	}
	
/**
 * Determine whether or not all of the required information has been
 * submitted and is completely valid. If validation has succeeded, then
 * store the data within the class for later database entry.
 *
 * @access private
 * @return void
 * @since  1.0
 * @throws Validation_Failed Thrown when ANY portion of the validation process fails
*/

	private function validateAndRetain() {
	//Fetch, validate, and retain the trip data		
		$this->ID = $_POST['id'];
		$this->mode = strtolower($_POST['mode']);
		
		if ($this->mode == "assist") {
			$this->info = Trip_Info::getNeeded($this->ID);
		} elseif ($this->mode == "request") {
			$this->info = Trip_Info::getAvailable($this->ID);
		} else {
			throw new Validation_Error("The form processing mode must be either &quot;assist&quot; or &quot;request&quot;.");
		}
		
	//Fetch, validate, and retain the poster user data
		$this->poster = get_userdata($this->mode == "assist" ? $this->info->RequesteeID : $this->info->SharerID);
		
		if (!$this->poster) {
			throw new Validation_Failed("The person who posted this trip is not available");
		}

		if ($this->user->ID == $this->poster->ID) {
			throw new Validation_Failed("You cannot perform this transaction on yourself");
		}
		
	//Retain the comments
		$this->comments = $_POST['comments'];
	}
	
/**
 * Send an email to the poster notifying them of the request
 * and another email to the initiator, with his or her "receipt".
 *
 * @access private
 * @return void
 * @since  1.0
*/
	
	private function sendEmails() {		
	//Create a date and time string for the departure time
		$formatter = \DateTime::createFromFormat("Y-m-d H:i:s", $this->info->Leaving, new \DateTimeZone($this->info->LeavingTimeZone));
		
	//Send the initiator an email
		$emailInitiator = new Email_Initiator();
		$emailInitiator->fromEmail = $this->mode == "assist" ? $this->poster->user_email : $this->settings[0]->EmailAddress;
		$emailInitiator->fromName = $this->mode == "assist" ? $this->poster->first_name . " " . $this->poster->last_name : $this->settings[0]->EmailName;
		$emailInitiator->subject = ($this->mode == "assist" ? "Assisting" : "Requesting") . " a Trip to " . $this->info->ToCity . ", " . $this->info->ToState;
		$emailInitiator->toEmail = $this->user->user_email;
		$emailInitiator->toName = $this->user->first_name . " " . $this->user->last_name;
		
		$emailInitiator->departureTime = $formatter->format("M jS \a\\t g:i A");
		$emailInitiator->fromCityAndState = $this->info->FromCity . ", " . $this->info->FromState;
		$emailInitiator->latitude = 0.0;
		$emailInitiator->longitude = 0.0;
		$emailInitiator->mode = $this->mode;
		$emailInitiator->poster = $this->poster->first_name . " " . $this->poster->last_name;
		$emailInitiator->posterFirstName = $this->poster->first_name;
		$emailInitiator->toCityAndState = $this->info->ToCity . ", " . $this->info->ToState;

		$emailInitiator->buildBody();
		$emailInitiator->send();
		
	//Send the poster an email
		$emailPoster = new Email_Poster();
		$emailPoster->fromEmail = $this->mode == "assist" ? $this->settings[0]->EmailAddress : $this->user->user_email;
		$emailPoster->fromName = $this->mode == "assist" ? $this->settings[0]->EmailName : $this->user->first_name . " " . $this->user->last_name;
		$emailPoster->subject = ($this->mode == "assist" ? "Assistance Available for a" : "Request for a Seat on your") . " Trip to " . $this->info->ToCity . ", " . $this->info->ToState;
		$emailPoster->toEmail = $this->poster->user_email;
		$emailPoster->toName = $this->poster->first_name . " " . $this->poster->last_name;

		$emailPoster->comments = $this->comments;
		$emailPoster->departureTime = $formatter->format("M jS \a\\t g:i A");
		$emailPoster->fromCityAndState = $this->info->FromCity . ", " . $this->info->FromState;
		$emailPoster->initiator = $this->user->first_name . " " . $this->user->last_name;
		$emailPoster->initiatorFirstName = $this->user->first_name;
		$emailPoster->latitude = 0.0;
		$emailPoster->longitude = 0.0;
		$emailPoster->mode = $this->mode;
		$emailPoster->toCityAndState = $this->info->ToCity . ", " . $this->info->ToState;

		$emailPoster->buildBody();
		$emailPoster->send();
	}
	
/**
 * Update the trip and log the transaction in the database.
 *
 * @access private
 * @return void
 * @since  1.0
*/

	private function updateLocal() {
		global $wpdb;
		
	//Update the trip information
		$wpdb->query($wpdb->prepare("UPDATE `" . ($this->mode == "assist" ? "ffi_ta_need" : "ffi_ta_share") . "` SET `Fulfilled` = Fulfilled + 1 WHERE `ID` = %d", $this->ID));
		
	//Log the transaction
		$wpdb->insert("ffi_ta_transactions", array(
			"ID"              => NULL,
			"Type"            => strtoupper($this->mode),
			"FromCity"        => $this->info->FromCityID,
			"ToCity"          => $this->info->ToCityID,
			"Initiator"       => $this->user->ID,
			"Poster"          => $this->poster->ID,
			"Leaving"         => $this->info->Leaving,
			"LeavingTimeZone" => $this->info->LeavingTimeZone,
			"EndDate"         => $this->info->EndDate
		), array (
			"%d", "%s", "%d", "%d", "%d", "%d", "%s", "%s", "%s"
		));
	}
}
?>