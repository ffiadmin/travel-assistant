<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;

//Include the necessary scripts
	$essentials->includeCSS("trips.min.css");
	$essentials->includePluginClass("display/Trip_Info");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("//tinymce.cachefly.net/4/tinymce.min.js");
	$essentials->includeJS("trips.superpackage.min.js");

//Fetch the trip information
	$params = $essentials->params ? $essentials->params[0] : 0;
	$info = FFI\TA\Trip_Info::getNeeded($params);
	
//Check to see if any information was returned
	if (!count($info)) {
		wp_redirect($essentials->friendlyURL(""));
		exit;
	}
	
	$formatter = DateTime::createFromFormat("Y-m-d H:i:s", $info->Leaving, new DateTimeZone($info->LeavingTimeZone));
	$timezones = array (
		"Pacific/Honolulu"    => "HAT",
		"America/Anchorage"   => "AKT",
		"America/Los_Angeles" => "PT",
		"America/Denver"      => "MT",
		"America/Chicago"     => "CT",
		"America/New_York"    => "ET"
	);
	
//Initialize the jQuery Directions plugin
	$essentials->includeHeadHTML("<script>\$(function(){\$('section.directions').FFI_TA_Directions('" . $info->FromCity . ", " . $info->FromState . "', '" . $info->ToCity . ", " . $info->ToState . "');\$('section.printable-directions').FFI_TA_Steps('" . $info->FromCity . ", " . $info->FromState . "', '" . $info->ToCity . ", " . $info->ToState . "');\$('span.assist').FFI_TA_Trip(" . (is_user_logged_in() ? "{'showLogin':false}" : "") . ")})</script>");

//Set the page title
	$title = $info->FromCity . ", " . $info->FromState . " to " . $info->ToCity . ", " . $info->ToState;
	$essentials->setTitle($title);
	
//Display the page header
	echo "<section id=\"splash\">
<h2>" . $title . "</h2>

<section class=\"leaving\">
<div>
<h3>" . $formatter->format("M") . "</h3>
<h4>" . $formatter->format("j") . "</h4>
</div>

<h5>" . $formatter->format("g:i A") . " " . $timezones[$info->LeavingTimeZone] . "</h5>
<span class=\"assist\" data-id=\"" . $params . "\" data-mode=\"assist\" data-name=\"" . htmlentities($info->Requestee) . "\" data-total=\"" . $info->Total . "\">I Can Help!</span>
</section>

<section class=\"directions\"></section>
</section>

";

//Display the trip overview section
	$settings = $wpdb->get_results("SELECT `TimeZone` FROM `ffi_ta_settings`");
	$date = new DateTime("now", new DateTimeZone($settings[0]->TimeZone));
	$header = $info->Requestee . " ";
	
	if ($info->Total > 1) { //Total includes the person who asked for the ride
		$header .= "and " . ($info->Total - 1) . " " . ($info->Total - 1 == 1 ? "other person" : "others") . " need a ride from";		
	} else {
		$header .= "needs a ride from";
	}
	
	$messages = array (
		"It's the Weekend!",
		"Only 4 Days till Friday!",
		"Only 3 Days till Friday!",
		"Only 2 Days till Friday!",
		"Tomorrow is Friday!",
		"It's Friday!!!",
		"It's the Weekend!"
	);

	echo "<section class=\"center content overview\">
<h2>Trip Overview</h2>
<h3>" . $header . "</h3>

<ul class=\"trip\">
<li class=\"from\"><span>" . $info->FromCity . ", " . $info->FromState . "</span></li>
<li class=\"text\">to</li>
<li class=\"to\"><span>" . $info->ToCity . ", " . $info->ToState . "</span></li>
</ul>

<ul class=\"details\">
<li class=\"men\">
<figure></figure>
<h3>" . $info->MalesPresent . " " . ($info->MalesPresent == 1 ? "Man" : "Men") . "</h3>
<p>In addition to " . $info->Requestee . ", " . ($info->MalesPresent == 0 ? "no men" : $info->MalesPresent . " " . ($info->MalesPresent == 1 ? "man" : "men")) . " will be joining this ride.</p>
</li>

<li class=\"women\">
<figure></figure>
<h3>" . $info->FemalesPresent . " " . ($info->FemalesPresent == 1 ? "Woman" : "Women") . "</h3>
<p>In addition to " . $info->Requestee . ", " . ($info->FemalesPresent == 0 ? "no women" : $info->FemalesPresent . " " . ($info->FemalesPresent == 1 ? "woman" : "women")) . " will be joining this ride.</p>
</li>

<li class=\"minutes\">
<figure></figure>
<h3>" . $info->MinutesWithin . " " . ($info->MinutesWithin == 1 ? "Minute" : "Minutes") . " from Destination</h3>
<p>" . ($info->MinutesWithin == 0 ? "You will need to drive " . ($info->Total == 1 ? $info->Requestee : "everyone") . " right to the final destination, even if it is out of your way." : "As long as your destination is within " . $info->MinutesWithin . " " . ($info->MinutesWithin == 1 ? "minute" : "minutes") . " of " . ($info->Total == 1 ? $info->Requestee : "everyone") . "'s final destination, " . ($info->Total == 1 ? "this person" : "everyone") . " is able go the rest of the way from there. This will save you from having to drive out of your way to drop " . ($info->Total == 1 ? $info->Requestee : "everyone") . " off.") . "</p>
</li>

<li class=\"reimbursement\">
<figure></figure>
<h3>\$" . $info->GasMoney . ".00 for Fuel</h3>
<p>" . ($info->GasMoney == 0 ? ($info->Total == 1 ? $info->Requestee : "No one in this group") . " is able to contribute gas money for fuel expenses." : ($info->Total == 1 ? $info->Requestee : "Everyone in this group") . " is able to contribute \$" . $info->GasMoney . ".00 for fuel expenses.") . "</p>
</li>

<li class=\"luggage\">
<figure></figure>
<h3>" . ($info->Luggage == 1 ? "Bringing" : "Not bringing") . " Luggage</h3>
<p>" . ($info->Luggage == 1 ? ($info->Total == 1 ? $info->Requestee . " will need" : "Everyone in this group will need") : ($info->Total == 1 ? $info->Requestee . " does not" : "No one in this group will need")) . " room for luggage.</p>
</li>

<li class=\"friday-o-meter\">
<figure></figure>
<h3>" . $messages[$date->format("w")] . "</h3>
<p>Just in case you forgot how close Friday really is&hellip;</p>
</li>
</ul>
</section>

";

//Display the trip recurrence section
	$CSS = " even";

	if ($info->EndDate != "0000-00-00") {
		$endFormatter = DateTime::createFromFormat("Y-m-d", $info->EndDate, new DateTimeZone($info->LeavingTimeZone));

		echo "<section class=\"center content even recurrence\">
<h2>Trip Recurrence</h2>
<p><strong>" . $info->Requestee . "</strong> is requesting this ride from <strong>" . $formatter->format("m/d/Y") . "</strong> until <strong>" . $endFormatter->format("m/d/Y") . "</strong> every&hellip;</p>

<ul>
<li" . ($info->Monday == 0 ? " class=\"no\"" : "") . ">M</li>
<li" . ($info->Tuesday == 0 ? " class=\"no\"" : "") . ">T</li>
<li" . ($info->Wednesday == 0 ? " class=\"no\"" : "") . ">W</li>
<li" . ($info->Thursday == 0 ? " class=\"no\"" : "") . ">R</li>
<li" . ($info->Friday == 0 ? " class=\"no\"" : "") . ">F</li>
</ul>

<figure></figure>
</section>

";

		$CSS = "";
	}

//Display the user comments section
	if ($info->Comments != "") {
		echo "<section class=\"center comments content" . $CSS . "\">
<h2>Comments</h2>
<figure></figure>

" . $info->Comments . "
</section>

";

		$CSS = ($CSS == "" ? " even" : "");
	}

//Display the trip directions
	echo "<section class=\"center content" . $CSS . " printable-directions show-header\">
<h2>Trip Directions</h2>
</section>";
?>