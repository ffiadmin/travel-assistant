<?php
//This page must have at least a state name in the URL
	if (!$essentials->params) {
		wp_redirect($essentials->friendlyURL(""));
		exit;
	}
	
//Identify whether this page should be showing a listing of origin cities, or destination cities 
	define("FFI\TA\DISPLAY_MODE", isset($essentials->params[2]) ? "destination" : "origin");

//Include the necessary scripts
	$essentials->includeCSS("browse.min.css");
	$essentials->includePluginClass("display/City");
	$essentials->includePluginClass("display/Map_Images");
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
	if (FFI\TA\DISPLAY_MODE == "origin" && $info[0]->District != 1) {
	//Set the page title
		$essentials->setTitle($state->Name);

	//Display the page
		echo "<h1>" . $state->Name . "</h1>

";

	//Display the welcome splash section
		echo "<section id=\"splash\">
<div class=\"ad-container state\" style=\"background-image:url(" . $essentials->normalizeURL("system/images/state-backgrounds/" . $state->Image) . ".jpg)\">
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
<a href=\"" . $essentials->friendlyURL("browse/" . $params . "/" . FFI\TA\City::URLPurify($city->City)) . "\">
<div style=\"background-image: url('" . FFI\TA\Map_Images::cityPreview($city->City, $city->Code, $city->Latitude, $city->Longitude) . "')\">
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
<a class=\"btn btn-warning\" href=\"" . $essentials->friendlyURL("need-a-ride") . "\">Request Ride</a>
<a class=\"btn btn-warning\" href=\"" . $essentials->friendlyURL("share-a-ride") . "\">Share Ride</a>
</p>
</section>";
		}
//Display a listing a available destination cities
	} else {
		if ($info[0]->District != 1) {
			$needs = FFI\TA\City::getDestinationNeedCities($essentials->params[2], $state->Code);
			$shares = FFI\TA\City::getDestinationShareCities($essentials->params[2], $state->Code);
		} else {
			$needs = FFI\TA\City::getDestinationNeedCities(FFI\TA\City::URLPurify($info[0]->City), $state->Code);
			$shares = FFI\TA\City::getDestinationShareCities(FFI\TA\City::URLPurify($info[0]->City), $state->Code);
		}
		
	//Does this city have any needs or shares?
		if (count($needs)) {
			$info = &$needs[0];
		} elseif (count($shares)) {
			$info = &$shares[0];
		} else {
			wp_redirect($essentials->friendlyURL("browse/" . $params));
			exit;
		}
		
	//Set the page title
		$title = $info->FromCity . ", " . $state->Name;
		$essentials->setTitle($title);

	//Display the page
		echo "<h1>" . $title . "</h1>

";

	//Display the welcome splash section
		echo "<section id=\"splash\">
<div class=\"ad-container city\" style=\"background-image:url('" . FFI\TA\Map_Images::cityBanner($info->FromCity, $info->FromState, $info->FromLatitude, $info->FromLongitude) . "')\">
<div class=\"ad-contents\">
<h2>" . $title . "</h2>
</div>
</div>
</section>

";

	//Display all of the destination cities which are requesting rides
		if (count($needs)) {
			echo "<section class=\"center content\">
<h2>Needed Rides</h2>
<p>Below is a listing of requests from people who need a ride from " . $title . " to one of the destination cities listed below. Each request will be listed with the departure date and the total number of occupants requesting a ride within the party.</p>

<ul class=\"destinations\">";

			foreach($needs as $city) {
				$formatter = DateTime::createFromFormat("Y-m-d H:i:s", $city->Leaving, new DateTimeZone($city->LeavingTimeZone));
				$URL = FFI\TA\City::URLPurify($info->FromCity . "-" . $info->FromState . "-to-" . $city->ToCity . "-" . $city->ToState);
				
				echo "
<li>
<a href=\"" . $essentials->friendlyURL("trips/needed/" . $city->ID . "/" . $URL) . "\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"desktop\" src=\"" . FFI\TA\Map_Images::browseLarge($city->ToCity, $city->ToState, $city->ToLatitude, $city->ToLongitude) . "\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"mobile\" src=\"" . FFI\TA\Map_Images::browseSmall($city->ToCity, $city->ToState, $city->ToLatitude, $city->ToLongitude) . "\">
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
			echo "<section class=\"center content" . (count($needs) ? " even" : ""). "\">
<h2>Available Rides</h2>
<p>Below is a listing of available rides from people leaving " . $title . " to one of the destination cities listed below. Each item will be listed with the departure date and the total number of available seats.</p>

<ul class=\"destinations\">";

			foreach($shares as $city) {
				$formatter = DateTime::createFromFormat("Y-m-d H:i:s", $city->Leaving, new DateTimeZone($city->LeavingTimeZone));
				$URL = FFI\TA\City::URLPurify($info->FromCity . "-" . $info->FromState . "-to-" . $city->ToCity . "-" . $city->ToState);
				
				echo "
<li>
<a href=\"" . $essentials->friendlyURL("trips/available/" . $city->ID . "/" . $URL) . "\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"desktop\" src=\"" . FFI\TA\Map_Images::browseLarge($city->ToCity, $city->ToState, $city->ToLatitude, $city->ToLongitude) . "\">
<img alt=\"" . htmlentities($city->ToCity . ", " . $city->ToState) . " Map\" class=\"mobile\" src=\"" . FFI\TA\Map_Images::browseSmall($city->ToCity, $city->ToState, $city->ToLatitude, $city->ToLongitude) . "\">
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