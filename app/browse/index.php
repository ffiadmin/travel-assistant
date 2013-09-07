<?php
//This page must have at least a state name in the URL
	if (!$essentials->params) {
		wp_redirect($essentials->friendlyURL(""));
		exit;
	}
	
//Identify whether this page should be showing a listing of origin cities, or destination cities 
	define("FFI\TA\DISPLAY_MODE", isset($essentials->params[2]) ? "destination" : "origin");

//Fetch the Google Maps API key for use when creating the map views of each city
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;

//Include the necessary scripts
	$essentials->includeCSS("styles/browse.css");
	$essentials->includePluginClass("display/City");
	$essentials->includePluginClass("display/State");
	
//Check to see if this state exists
	$params = $essentials->params[0];
	$state = FFI\TA\State::exists($params);

	if ($state) {
		$info = FFI\TA\City::getOriginCities($params);
	} else {
		wp_redirect($essentials->friendlyURL(""));
		exit;
	}
	
//Display a listing a available origin cities
	if (FFI\TA\DISPLAY_MODE == "origin") {
		$essentials->includeHeadHTML("<script>\$(function(){\$('button.request').click(function(){document.location='" . $essentials->friendlyURL("need-a-ride") . "'});\$('button.share').click(function(){document.location='" . $essentials->friendlyURL("share-a-ride") . "'})})</script>");

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
<div style=\"background-image: url('//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city->City) . ",+" . urlencode($city->Code) . "&zoom=13&size=180x180&markers=color:red%7C" . $city->Latitude . "," . $city->Longitude . "&key=" . $API . "&sensor=false&visual_refresh=true')\">
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
	//Display a comment if no cities are available for a particular state
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
//Display a listing a available destination cities
	} else {
		$needs = FFI\TA\City::getDestinationNeedCities($essentials->params[2], $state->Code);
		$shares = FFI\TA\City::getDestinationShareCities($essentials->params[2], $state->Code);
		$title = $needs[0]->FromCity . ", " . $state->Name;
		
	//Set the page title
		$essentials->setTitle($title);

	//Display the page
		echo "<h1>" . $title . "</h1>

";

	//Display the welcome splash section
		echo "<section id=\"splash\">
<div class=\"ad-container\" style=\"background-image:url('//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($needs[0]->FromCity) . ",+" . urlencode($needs[0]->FromState) . "&zoom=13&size=1000x141&scale=2&markers=color:red%7C" . $needs[0]->FromLatitude . "," . $needs[0]->FromLongitude . "&key=" . $API . "&sensor=false&visual_refresh=true')\">
<div class=\"ad-contents\">
<h2>" . $title . "</h2>
</div>
</div>
</section>

";

	//Display all of the destination cities which are requesting rides
		if (count($needs)) {
			$formatter = new DateTime();
			$URL = "";
			
			echo "<section class=\"center content\">
<h2>Needed Rides</h2>
<p>Below is a listing of requests from people who need a ride from " . $title . " to one of the destination cities listed below. Each request will be listed with the departure date and the total number of occupants requesting a ride within the party.</p>

<ul class=\"destinations\">";

			foreach($needs as $city) {
				$formatter = DateTime::createFromFormat("Y-m-d H:i:s", $city->Leaving, new DateTimeZone($city->LeavingTimeZone));
				$URL = FFI\TA\State::URLPurify($needs[0]->FromCity . "-" . $needs[0]->FromState . "-to-" . $city->ToCity . "-" . $city->ToState);
				
				echo "
<li>
<a href=\"" . $essentials->friendlyURL("trips/needed/" . $city->ID . "/" . $URL) . "\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"desktop\" src=\"//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city->ToCity) . ",+" . urlencode($city->ToState) . "&zoom=13&size=230x190&scale=1&markers=color:red%7C" . $city->ToLatitude . "," . $city->ToLongitude . "&key=" . $API . "&sensor=false&visual_refresh=true\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"mobile\" src=\"//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city->ToCity) . ",+" . urlencode($city->ToState) . "&zoom=13&scale=1&size=100x100&markers=color:red%7C" . $city->ToLatitude . "," . $city->ToLongitude . "&key=" . $API . "&sensor=false&visual_refresh=true\">
<h3>" . $city->ToCity . ", " . $city->ToState . "</h3>
<ul>
<li class=\"departure\">" . $formatter->format("M jS") . "<span class=\"mobile\"> at " . $formatter->format("g:i A") . "</span></li>
<li class=\"occupants\">" . $city->Occupants . "<span class=\"mobile\"> " . ($city->Occupants == 1 ? "person" : "people") . "</span></li>
</a>
</ul>
</li>
";
			}

			echo "</ul>
</section>";
		}
		
	//Display all of the destination cities which have rides available
		if (count($shares)) {
			$formatter = new DateTime();
			$URL = "";
			
			echo "<section class=\"center content even\">
<h2>Available Rides</h2>
<p>Below is a listing of available rides from people leaving " . $title . " to one of the destination cities listed below. Each item will be listed with the departure date and the total number of available seats.</p>

<ul class=\"destinations\">";

			foreach($shares as $city) {
				$formatter = DateTime::createFromFormat("Y-m-d H:i:s", $city->Leaving, new DateTimeZone($city->LeavingTimeZone));
				$URL = FFI\TA\State::URLPurify($shares[0]->FromCity . "-" . $shares[0]->FromState . "-to-" . $city->ToCity . "-" . $city->ToState);
				
				echo "
<li>
<a href=\"" . $essentials->friendlyURL("trips/available/" . $city->ID . "/" . $URL) . "\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"desktop\" src=\"//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city->ToCity) . ",+" . urlencode($city->ToState) . "&zoom=13&size=230x190&scale=1&markers=color:red%7C" . $city->ToLatitude . "," . $city->ToLongitude . "&key=" . $API . "&sensor=false&visual_refresh=true\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"mobile\" src=\"//maps.googleapis.com/maps/api/staticmap?center=" . urlencode($city->ToCity) . ",+" . urlencode($city->ToState) . "&zoom=13&scale=1&size=100x100&markers=color:red%7C" . $city->ToLatitude . "," . $city->ToLongitude . "&key=" . $API . "&sensor=false&visual_refresh=true\">
<h3>" . $city->ToCity . ", " . $city->ToState . "</h3>
<ul>
<li class=\"departure\">" . $formatter->format("M jS") . "<span class=\"mobile\"> at " . $formatter->format("g:i A") . "</span></li>
<li class=\"occupants\">" . $city->Seats . "<span class=\"mobile\"> " . ($city->Seats == 1 ? "seat" : "seats") . "</span></li>
</a>
</ul>
</li>
";
			}

			echo "</ul>
</section>";
		}
	}
?>
