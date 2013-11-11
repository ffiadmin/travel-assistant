<?php
//Include the necessary scripts
	$essentials->includeCSS("styles/my-trips.css");
	$essentials->includePluginClass("display/City");
	$essentials->includePluginClass("display/Map_Images");
	$essentials->requireLogin();
	$essentials->setTitle("My Trips");
	
//Fetch the Google Maps API key for use when creating the map views of each city
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;
	
	$dateFormatter = new DateTime();
	$timeZones = array (
		"Pacific/Honolulu"    => "HAT",
		"America/Anchorage"   => "AKT",
		"America/Los_Angeles" => "PT",
		"America/Denver"      => "MT",
		"America/Chicago"     => "CT",
		"America/New_York"    => "ET"
	);
	
//Display the trips the user needed
	$needed = FFI\TA\City::getMyNeeds($essentials->user->ID);

	echo "<section class=\"content even\">
<h2>Trips I've Needed</h2>

";

	if (count($needed)) {
		echo "<ul class=\"needed\">";
	
		foreach($needed as $need) {
			$dateFormatter = DateTime::createFromFormat("Y-m-d H:i:s", $need->Leaving);
		
			echo "
<li>
<img alt=\"" . htmlentities($need->FromCity . ", " . $need->FromState . " to " . $need->ToCity . ", " . $need->ToState) . "\" class=\"desktop\" src=\"" . Map_Images::myTripsPreviewLarge($need->FromLatitude, $need->FromLongitude, $need->ToLatitude, $need->ToLongitude) . "\">
<img alt=\"" . htmlentities($need->FromCity . ", " . $need->FromState . " to " . $need->ToCity . ", " . $need->ToState) . "\" class=\"mobile\" src=\"" . Map_Images::myTripsPreviewSmall($need->FromLatitude, $need->FromLongitude, $need->ToLatitude, $need->ToLongitude) . "\">
<h3>" . $need->FromCity . ", " . $need->FromState . "</h3>
<h4>to " . $need->ToCity . ", " . $need->ToState . "</h4>
<p class=\"left\">left on " . $dateFormatter->format("M jS \a\\t g:i A") . "</p>
<a class=\"btn btn-primary edit\" href=\"" . $essentials->friendlyURL("need-a-ride/" . $need->ID) . "\"><i class=\"icon-pencil icon-white\"></i><span class=\"desktop\"> Edit</span></a>
<button class=\"btn btn-danger delete\" data-id=\"" . $need->ID . "\"><i class=\"icon-trash icon-white\"></i><span class=\"desktop\"> Delete</span></button>
</li>
";
		}
		
		echo "</ul>
		
<div class=\"none\">
<p>You haven't asked for any trips, yet.</p>
<a href=\"" . $essentials->friendlyURL("need-a-ride") . "\">Ask for a ride &#187;</a>
</div>";
	} else {
		echo "<div class=\"none show\">
<p>You haven't asked for any trips, yet.</p>
<a href=\"" . $essentials->friendlyURL("need-a-ride") . "\">Ask for a ride &#187;</a>
</div>";
	}

	echo "
</section>

";

//Display the trips the user shared
	$shared = FFI\TA\City::getMyShares($essentials->user->ID);

	echo "<section class=\"content\">
<h2>Trips I've Shared</h2>

";

	if (count($shared)) {
		echo "<ul class=\"shared\">";
	
		foreach($shared as $share) {
			$dateFormatter = DateTime::createFromFormat("Y-m-d H:i:s", $share->Leaving);
		
			echo "
<li>
<img alt=\"" . htmlentities($share->FromCity . ", " . $share->FromState . " to " . $share->ToCity . ", " . $share->ToState) . "\" class=\"desktop\" src=\"" . Map_Images::myTripsPreviewLarge($share->FromLatitude, $share->FromLongitude, $share->ToLatitude, $share->ToLongitude) . "\">
<img alt=\"" . htmlentities($share->FromCity . ", " . $share->FromState . " to " . $share->ToCity . ", " . $share->ToState) . "\" class=\"mobile\" src=\"" . Map_Images::myTripsPreviewSmall($share->FromLatitude, $share->FromLongitude, $share->ToLatitude, $share->ToLongitude) . "\">
<h3>" . $share->FromCity . ", " . $share->FromState . "</h3>
<h4>to " . $share->ToCity . ", " . $share->ToState . "</h4>
<p class=\"left\">left on " . $dateFormatter->format("M jS \a\\t g:i A") . "</p>
<a class=\"btn btn-primary edit\" href=\"" . $essentials->friendlyURL("share-a-ride/" . $share->ID) . "\"><i class=\"icon-pencil icon-white\"></i><span class=\"desktop\"> Edit</span></a>
<button class=\"btn btn-danger delete\" data-id=\"" . $share->ID . "\"><i class=\"icon-trash icon-white\"></i><span class=\"desktop\"> Delete</span></button>
</li>
";
		}
		
		echo "</ul>
		
<div class=\"none\">
<p>You haven't shared any trips, yet.</p>
<a href=\"" . $essentials->friendlyURL("share-a-ride") . "\">Share a ride &#187;</a>
</div>";
	} else {
		echo "<div class=\"none show\">
<p>You haven't shared any trips, yet.</p>
<a href=\"" . $essentials->friendlyURL("share-a-ride") . "\">Share a ride &#187;</a>
</div>";
	}

	echo "
</section>";
?>