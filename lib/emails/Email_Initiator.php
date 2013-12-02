<?php
/**
 * Trip Initiator Emailer class
 *
 * This class is designed to build an email to be sent to a
 * person who presses either the "I Can Help" or "I Need This 
 * Ride" button.
 *
 * DEFINITIONS:
 *  - Initiator: The person who triggered this action by pressing either
 *               the "I Need This Ride" or "I Can Help" buttons.
 *  - Poster:    The person who posted this information online, hoping
 *               that someone else will find his or her posting.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @extends   FFI\TA\Email_Base
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.email
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(__FILE__) . "/Email_Base.php");
require_once(dirname(dirname(__FILE__)) . "/display/Map_Images.php");

class Email_Initiator extends Email_Base {
/**
 * Hold the PREFORMATTED departure date and time.
 *
 * @access public
 * @type   string
*/

	public $departureTime;
	
/**
 * Hold the name of the origin city and state, in a format such
 * as this:
 * 
 *     Orlando, FL
 *
 * @access public
 * @type   string
*/

	public $fromCityAndState;
	
/**
 * Hold the latitude of the city.
 *
 * @access public
 * @type   float
*/

	public $latitude;
	
/**
 * Hold the longitude of the city.
 *
 * @access public
 * @type   float
*/

	public $longitude;
	
/**
 * Hold the mode of the email, whether this is being sent to someone
 * who requested a seat for an existing ride, or if it is being sent
 * to someone who has just fulfilled a request for a ride.
 *
 * @access public
 * @type   string
*/

	public $mode;
	
/**
 * Hold the name of the poster.
 *
 * @access public
 * @type   string
*/

	public $poster;

/**
 * Hold the first name of the poster.
 *
 * @access public
 * @type   string
*/

	public $posterFirstName;
	
/**
 * Hold the name of the destination city and state, in a format such
 * as this:
 * 
 *     Orlando, FL
 *
 * @access public
 * @type   string
*/

	public $toCityAndState;

/**
 * Build the HTML and plain-text versions of the email body 
 * from the information gathered previously.
 *
 * @access public
 * @return void
 * @since  1.0
*/
	
	public function buildBody() {
	//Generate the absolute URL to the directory where the images in the email can be found
		$directory = "http://" . $_SERVER['HTTP_HOST'] . str_replace("ajax/trip.php", "", $_SERVER['PHP_SELF']) . "images/email-assets/";
		
	//Generate the HTML version of the email
		$this->HTMLBody = "<!DOCTYPE html>
<html lang=\"en-US\">
<head>
<meta charset=\"utf-8\">
<title>" . $this->subject . "</title>
</head>

<body>
<table cellpadding=\"none\" style=\"border-collapse:collapse; border-top: 5px solid #000000;\" width=\"660\">
<tbody>
<tr>
<td align=\"center\" height=\"144\" style=\"border-left: 1px solid #000000; border-right: 1px solid #000000;\" valign=\"top\" width=\"660\">
<img alt=\"SGA Email Header\" height=\"144\" src=\"" . $directory . "header.jpg\" width=\"660\" />
</td>
</tr>

<tr>
<td align=\"center\" height=\"376\" style=\"border: 1px solid #000000;\" valign=\"top\" width=\"660\">
<img alt=\"Map of " . htmlentities($this->toCityAndState) . "\" height=\"376\" src=\"" . Map_Images::emailBanner($this->toCityAndState, $this->latitude, $this->longitude) . "\" width=\"660\" />
</td>
</tr>

<tr>
<td style=\"border-left: 1px solid #000000; border-right: 1px solid #000000;\">
<table>
<tbody>
<tr height=\"30\">
<td colspan=\"3\"></td>
</tr>

<tr>
<td width=\"25\"></td>
<td align=\"center\">
<p align=\"center\" style=\"font-family: Arial,sans-serif; font-size: 16px;\">Congratulations! " . ($this->mode == "assist" ? "You've successfully sent a notification to <strong>" . $this->poster . "</strong> which states that you are willing to provide the transportation for a trip <strong>from " . $this->fromCityAndState . " to " . $this->toCityAndState . " on " . $this->departureTime . "</strong>." : "You've successfully sent a notification to <strong>" . $this->poster . "</strong> which requests transportation for your trip <strong>from " . $this->fromCityAndState . " to " . $this->toCityAndState . " on " . $this->departureTime . "</strong>.") . "</p>
</td>
<td width=\"25\"></td>
</tr>

<tr height=\"30\">
<td colspan=\"3\"></td>
</tr>
</tbody>
</table>

<img alt=\"Content Divider\" height=\"28\" src=\"" . $directory . "divider.jpg\" width=\"660\" />
</td>
</tr>

<tr>
<td style=\"border-left: 1px solid #000000; border-right: 1px solid #000000;\">
<table>
<tbody>
<tr>
<td colspan=\"5\" height=\"10\"></td>
</tr>

<tr>
<td align=\"center\" colspan=\"5\" height=\"25\">
<h2 style=\"font-family: Arial,sans-serif; font-size: 24px; font-weight: 100; margin: 5px 0px 0px 0px; text-align: center;\">Next Steps</h2>
</td>
</tr>

<tr>
<td colspan=\"5\" height=\"25\"></td>
</tr>

<tr>
<td width=\"35\"></td>
<td>
<img alt=\"Step Numbers\" height=\"232\" src=\"" . $directory . "numbers.jpg\" width=\"69\" />
</td>
<td width=\"25\"></td>
<td>
<table height=\"245\">
<tbody>
<tr>
<td height=\"80\" valign=\"middle\"><p style=\"font-family: Arial,sans-serif; font-size: 16px;\">" . ($this->mode == "assist" ? "<strong>Reply to this email to send an email to " : "<strong>Wait for an email from ") . $this->poster . "</strong> indicating the best <strong>location to meet</strong> just prior to the trip.</p></td>
</tr>

<tr>
<td height=\"80\" valign=\"middle\"><p style=\"font-family: Arial,sans-serif; font-size: 16px;\"><strong>Meet " . $this->posterFirstName . "</strong> at the agreed upon location on <strong>" . $this->departureTime . "</strong>.</p></td>
</tr>

<tr>
<td height=\"80\" valign=\"middle\"><p style=\"font-family: Arial,sans-serif; font-size: 16px;\">" . ($this->mode == "assist" ? "Drive safely!" : "Ride safely!") . "</p></td>
</tr>
</tbody>
</table>
</td>
<td width=\"35\"></td>
</tr>

<tr height=\"30\">
<td colspan=\"5\"></td>
</tr>
</tbody>
</table>
</td>
</tr>

<tr>
<td align=\"center\" bgcolor=\"#181818\" height=\"45\" style=\"border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;\" valign=\"middle\">
<img alt=\"Small SGA Logo\" height=\"32\" src=\"" . $directory . "logo.jpg\" width=\"64\" />
</td>
</tr>

<tr>
<td><img alt=\"Shadow\" height=\"28\" src=\"" . $directory . "shadow.jpg\" width=\"660\" /></td>
</tr>

<tr height=\"30\">
<td colspan=\"5\"></td>
</tr>

<tr>
<td><p style=\"font-family: Arial,sans-serif; font-size: 10px; margin: 0px;\"><strong>Disclaimer:</strong> This service provided by the Student Government Association at Grove City College is strictly intended to serve as a convenient service to Grove City College attendees. Neither the Student Government Association nor Grove City College can be responsible for any property damage, personal injury, or further inconveniences which may result from sharing a ride with another student or faculty member. It is solely your responsiblity to take any necessary steps before and during the trip to prevent property damage and/or personal injury.</p></td>
</tr>
</tbody>
</table>
</body>
</html>";

	//Generate the plain-text version of the email
		$this->textBody = "Congratulations! " . ($this->mode == "assist" ? "You've successfully sent a notification to " . $this->poster . " which states that you are willing to provide the transportation for a trip from " . $this->fromCityAndState . " to " . $this->toCityAndState . " on " . $this->departureTime . "." : "You've successfully sent a notification to " . $this->poster . " which requests transportation for your trip from " . $this->fromCityAndState . " to " . $this->toCityAndState . " on " . $this->departureTime . ".") . "

*** Next Steps ***
 
   1. " . ($this->mode == "assist" ? "Reply to this email to send an email to " : "Wait for an email from ") . $this->poster . " indicating the best location to meet just prior to the trip.
   2. Meet " . $this->posterFirstName . " at the agreed upon location on " . $this->departureTime . ".
   3. " . ($this->mode == "assist" ? "Drive safely!" : "Ride safely!") . "
   
*** Disclaimer ***

   This service provided by the Student Government Association at Grove City College is strictly intended to serve as a convenient service to Grove City College attendees. Neither the Student Government Association nor Grove City College can be responsible for any property damage, personal injury, or further inconveniences which may result from sharing a ride with another student or faculty member. It is solely your responsiblity to take any necessary steps before and during the trip to prevent property damage and/or personal injury.

~ The Student Government Association";
	}
}
?>