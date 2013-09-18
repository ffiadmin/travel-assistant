<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;

//Include the necessary scripts
	$essentials->includeCSS("styles/trips.css");
	$essentials->includePluginClass("display/Trip_Info");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("scripts/FFI_TA_Directions.js");

//Fetch the trip information
	$params = $essentials->params ? $essentials->params[0] : 0;
	$info = FFI\TA\Trip_Info::getNeeded($params);
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
	$essentials->includeHeadHTML("<script>\$(function(){\$('section.directions').FFI_TA_Directions('" . $info[0]->FromCity . ", " . $info[0]->FromState . "', '" . $info[0]->ToCity . ", " . $info[0]->ToState . "')})</script>");

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
</section>

<section class=\"directions\"></section>
</section>

";

//Display the trip overview section
	$header = $info[0]->Requestee . " ";
	
	if ($info[0]->Total > 1) { //Total includes the person who asked for the ride
		$header .= "and " . ($info[0]->Total - 1) . " " . ($info[0]->Total - 1 == 1 ? "other person" : "others") . " need a ride from";		
	} else {
		$header .= "needs a ride from";
	}

	echo "<section class=\"center content overview\">
<h2>Trip Overview</h2>
<h3>" . $header . "</h3>

<ul>
<li class=\"from\">" . $info[0]->FromCity . ", " . $info[0]->FromState . "</li>
<li class=\"text\">to</li>
<li class=\"to\">" . $info[0]->ToCity . ", " . $info[0]->ToState . "</li>
</ul>
</section>

";

//Display the trip details section
	echo "<section class=\"center content details even\">
<h2>Trip Details</h2>

<ul>
<li class=\"" . ($info[0]->MalesPresent == 0 ? "no-" : "") . "men\"><span>" . $info[0]->MalesPresent . " additional " . ($info[0]->MalesPresent == 1 ? "man" : "men") . " riding</span></li>
<li class=\"" . ($info[0]->FemalesPresent == 0 ? "no-" : "") . "women\"><span>" . $info[0]->FemalesPresent . " additional " . ($info[0]->FemalesPresent == 1 ? "woman" : "women") . " riding</span></li>
<li class=\"" . ($info[0]->GasMoney == 0 ? "no-" : "") . "reimbursement\"><span>Contributing \$" . $info[0]->GasMoney . ".00 for fuel</span></li>
<li class=\"" . ($info[0]->Luggage == 0 ? "no-" : "") . "luggage\"><span>" . ($info[0]->Luggage == 1 ? "Needs room" : "Does not need room") . " for luggage</span></li>
</ul>
</section>

";

//Display the trip recurrence section
	echo "<section class=\"center content recurrence\">
<h2>Trip Recurrence</h2>

<figure class=\"icon\"></figure>

<div>
<ul>
<li" . ($info[0]->Monday == 0 ? " class=\"no\"" : "") . ">M</li>
<li" . ($info[0]->Tuesday == 0 ? " class=\"no\"" : "") . ">T</li>
<li" . ($info[0]->Wednesday == 0 ? " class=\"no\"" : "") . ">W</li>
<li" . ($info[0]->Thursday == 0 ? " class=\"no\"" : "") . ">T</li>
<li" . ($info[0]->Friday == 0 ? " class=\"no\"" : "") . ">F</li>
</ul>
</div>
</section>

";
?>