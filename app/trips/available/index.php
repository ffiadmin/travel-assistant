<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;

//Include the necessary scripts
	$essentials->includeCSS("styles/trips.css");
	$essentials->includePluginClass("display/Trip_Info");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("scripts/FFI_TA_Directions.js");
	$essentials->includeJS("scripts/FFI_TA_Steps.js");
	$essentials->includeJS("scripts/FFI_TA_Trip.js");

//Fetch the trip information
	$params = $essentials->params ? $essentials->params[0] : 0;
	$info = FFI\TA\Trip_Info::getAvailable($params);
	$formatter = DateTime::createFromFormat("Y-m-d H:i:s", $info[0]->Leaving, new DateTimeZone($info[0]->LeavingTimeZone));
	$timezones = array(
		"Pacific/Honolulu"    => "HAT",
		"America/Anchorage"   => "AKT",
		"America/Los_Angeles" => "PT",
		"America/Denver"      => "MT",
		"America/Chicago"     => "CT",
		"America/New_York"    => "ET"
	);
	
//Initialize the jQuery Directions plugin
	$essentials->includeHeadHTML("<script>\$(function(){\$('section.directions').FFI_TA_Directions('" . $info[0]->FromCity . ", " . $info[0]->FromState . "', '" . $info[0]->ToCity . ", " . $info[0]->ToState . "');\$('section.printable-directions').FFI_TA_Steps('" . $info[0]->FromCity . ", " . $info[0]->FromState . "', '" . $info[0]->ToCity . ", " . $info[0]->ToState . "');\$('span.assist').FFI_TA_Trip(" . (is_user_logged_in() ? "{'showLogin':false}" : "") . ")})</script>");

//Set the page title
	$title = $info[0]->FromCity . ", " . $info[0]->FromState . " to " . $info[0]->ToCity . ", " . $info[0]->ToState;
	$essentials->setTitle($title);
	
//Display the page header
	echo "<section id=\"splash\">
<h2>" . $title . "</h2>

<section class=\"leaving\">
<div>
<h3>" . $formatter->format("M") . "</h3>
<h4>" . $formatter->format("j") . "</h4>
</div>

<h5>" . $formatter->format("g:i A") . " " . $timezones[$info[0]->LeavingTimeZone] . "</h5>
<span class=\"assist\" data-id=\"" . $params . "\" data-mode=\"request\" data-name=\"" . htmlentities($info[0]->Sharer) . "\" data-total=\"" . $info[0]->Total . "\">I Need This Ride!</span>
</section>

<section class=\"directions\"></section>
</section>

";

//Display the trip overview section
	echo "<section class=\"center content overview\">
<h2>Trip Overview</h2>
<h3>" . $info[0]->Sharer . " has a ride for " .  $info[0]->Seats . " " . ($info[0]->Seats == 1 ? "person" : "people") . " from</h3>

<ul class=\"trip\">
<li class=\"from\"><span>" . $info[0]->FromCity . ", " . $info[0]->FromState . "</span></li>
<li class=\"text\">to</li>
<li class=\"to\"><span>" . $info[0]->ToCity . ", " . $info[0]->ToState . "</span></li>
</ul>

<ul class=\"details\">
<li class=\"seats\">
<figure></figure>
<h3>" . $info[0]->Seats . " " . ($info[0]->Seats == 1 ? "Seat" : "Seats") . " Available</h3>
<p>" . $info[0]->Seats . " " . ($info[0]->Seats == 1 ? "seat is" : "seats are") . " available for this ride.</p>
</li>

<li class=\"men\">
<figure></figure>
<h3>" . $info[0]->MalesPresent . " " . ($info[0]->MalesPresent == 1 ? "Man" : "Men") . "</h3>
<p>In addition to " . $info[0]->Sharer . ", " . ($info[0]->MalesPresent == 0 ? "no men" : $info[0]->MalesPresent . " " . ($info[0]->MalesPresent == 1 ? "man" : "men")) . " will be joining this ride.</p>
</li>

<li class=\"women\">
<figure></figure>
<h3>" . $info[0]->FemalesPresent . " " . ($info[0]->FemalesPresent == 1 ? "Woman" : "Women") . "</h3>
<p>In addition to " . $info[0]->Sharer . ", " . ($info[0]->FemalesPresent == 0 ? "no women" : $info[0]->FemalesPresent . " " . ($info[0]->FemalesPresent == 1 ? "woman" : "women")) . " will be joining this ride.</p>
</li>

<li class=\"minutes\">
<figure></figure>
<h3>" . $info[0]->MinutesWithin . " " . ($info[0]->MinutesWithin == 1 ? "Minute" : "Minutes") . " of Extra Driving</h3>
<p>" . ($info[0]->MinutesWithin == 0 ? $info[0]->Sharer . " will not drive any extra distance to get you to your final destination. You may need to make additional arrangements to get you to your final destination." : $info[0]->Sharer . " is willing to drive an extra " . $info[0]->MinutesWithin . " " . ($info[0]->MinutesWithin == 1 ? "minute" : "minutes") . " out of the way to get you to your final destination.") . "</p>
</li>

<li class=\"reimbursement\">
<figure></figure>
<h3>\$" . $info[0]->GasMoney . ".00 for Fuel</h3>
<p>" . $info[0]->Sharer . " " . ($info[0]->GasMoney == 0 ? "is not requesting any" : "is requesting a \$" . $info[0]->GasMoney . ".00") . " reimbursement for fuel expenses.</p>
</li>

<li class=\"luggage\">
<figure></figure>
<h3>Luggage Room " . ($info[0]->Luggage == 1 ? "Available" : "Unvailable") . "</h3>
<p>" . $info[0]->Sharer . ($info[0]->Luggage == 1 ? " can" : " cannot") . " provide room for luggage.</p>
</li>
</ul>
</section>

";

//Display the trip recurrence section
	$CSS = " even";

	if ($info[0]->EndDate != "0000-00-00") {
		$endFormatter = DateTime::createFromFormat("Y-m-d", $info[0]->EndDate, new DateTimeZone($info[0]->LeavingTimeZone));

		echo "<section class=\"center content even recurrence\">
<h2>Trip Recurrence</h2>
<p><strong>" . $info[0]->Sharer . "</strong> can provide this ride from <strong>" . $formatter->format("m/d/Y") . "</strong> until <strong>" . $endFormatter->format("m/d/Y") . "</strong> every&hellip;</p>

<ul>
<li" . ($info[0]->Monday == 0 ? " class=\"no\"" : "") . ">M</li>
<li" . ($info[0]->Tuesday == 0 ? " class=\"no\"" : "") . ">T</li>
<li" . ($info[0]->Wednesday == 0 ? " class=\"no\"" : "") . ">W</li>
<li" . ($info[0]->Thursday == 0 ? " class=\"no\"" : "") . ">R</li>
<li" . ($info[0]->Friday == 0 ? " class=\"no\"" : "") . ">F</li>
</ul>

<figure></figure>
</section>

";

		$CSS = "";
	}

//Display the user comments section
	if ($info[0]->Comments != "") {
		echo "<section class=\"center comments content" . $CSS . "\">
<h2>Comments</h2>
<figure></figure>

" . $info[0]->Comments . "
</section>

";

		$CSS = ($CSS == "" ? " even" : "");
	}

//Display the trip directions
	echo "<section class=\"center content" . $CSS . " printable-directions show-header\">
<h2>Trip Directions</h2>
</section>";
?>