<?php
//This page must have at least a state name in the URL
	if (!$essentials->params) {
		wp_redirect($essentials->friendlyURL(""));
		exit;
	}

//Fetch the Google Maps API key for use when creating the map views of each city
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;

//Include the necessary scripts
	$essentials->includeCSS("styles/browse.css");
	$essentials->includePluginClass("display/City");
	$essentials->includePluginClass("display/State");
	$essentials->includeHeadHTML("<script>\$(function(){\$('button.request').click(function(){document.location='" . $essentials->friendlyURL("need-a-ride") . "'});\$('button.share').click(function(){document.location='" . $essentials->friendlyURL("share-a-ride") . "'})})</script>");

//Check to see if this state exists
	$params = $essentials->params[0];
	$state = FFI\TA\State::exists($params);

	if ($state) {
		$info = FFI\TA\City::getOriginCities($params);
	} else {
		wp_redirect($essentials->friendlyURL(""));
		exit;
	}

//Set the page title
	$essentials->setTitle($state->Name);

//Display the page
	echo "<h1>" . $state->Name . "</h1>

";

//Display the welcome splash section
	echo "<section id=\"splash\">
<div class=\"ad-container\" style=\"background-image:url(" . $essentials->normalizeURL("styles/splash/state-backgrounds/" . $state->Image) . ".jpg)\">
<div class=\"ad-contents\">
<h2>" . $state->Name . "</h2>
</div>
</div>
</section>

";

//Display all origin state cities
	if (count($info)) {
		echo "<section class=\"center content\">
<h2>" . $state->Name . " Cities</h2>
<p>Below is a listing of cities within " . $state->Name . " which have at least one ride requested or available. Click on one of the cities below to see a listing of rides which will be leaving that city, and where they are all headed to!</p>

<ul class=\"cities\">";

		foreach($info as $city) {
			echo "
<li>
<a href=\"" . $essentials->friendlyURL("browse/" . $params . "/" . FFI\TA\State::URLPurify($city->City)) . "\">
<div style=\"background-image: url('//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city->City) . ",+" . urlencode($city->Code) . "&zoom=13&size=180x180&markers=color:red%7C" . $city->Latitude . "," . $city->Longitude . "&key=" . $API . "&sensor=false')\">
<p class=\"needed" . ($city->Needs > 0 ? " highlight" : "") . "\">" . $city->Needs . " <span>" . ($city->Needs == 1 ? "Need" : "Needs") . "</span></p>
<p class=\"shares" . ($city->Shares > 0 ? " highlight" : "") . "\">" . $city->Shares . " <span>" . ($city->Shares == 1 ? "Ride" : "Rides") . "</span></p>
</div>
<h3>" . $city->City . ", " . $city->Code . "</h3>
</a>
</li>
";
		}

		echo "</ul>
</section>";
//Display a comment if no cities are availabke for a particular state
	} else {
		echo "<section class=\"center content\">
<h2>Nothing Available</h2>
<p>We do not currently have anyone who needs or can share a ride in the state of " . $state->Name . ". Sorry about that. :-(</p>
<p class=\"center\">
<button class=\"btn btn-warning request\">Request Ride</button>
<button class=\"btn btn-warning share\">Share Ride</button>
</p>
</section>";
	}
?>
