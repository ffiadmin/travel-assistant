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
	$essentials->includePluginClass("display/Trip_Info");

//Fetch the information for the state
	$params = $essentials->params ? $essentials->params[0] : "DNE";
	$failRedirect = $essentials->friendlyURL("");
	$info = FFI\BE\Trip_Info::getOriginCitiesByState($params);

//Set the page title
	$essentials->setTitle($info[0]->StateName);

//Display the page
	echo "<h1>" . $info[0]->StateName . "</h1>

";

//Display the welcome splash section
	echo "<section id=\"splash\">
<div class=\"ad-container\" style=\"background-image:url(" . $essentials->normalizeURL("styles/splash/state-backgrounds/" . $info[0]->Image) . ".jpg)\">
<div class=\"ad-contents\">
<h2>" . $info[0]->StateName . "</h2>
</div>
</div>
</section>

";

//Display all trips into Pennsylvania
	echo "<section class=\"center content\">
<h2>" . $info[0]->StateName . " Cities</h2>
<p>Below is a listing of cities within " . $info[0]->StateName . " which have at least one ride requested or available. Click on one of the cities below to see a listing of rides which will be leaving that city, and where they are all headed to!</p>

<ul class=\"cities\">";

	foreach($info as $city) {
		echo "
<li>
<a href=\"" . $essentials->friendlyURL("browse/" . $params . "/" . FFI\BE\Trip_Info::URLPurify($city->City)) . "\">
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
?>
