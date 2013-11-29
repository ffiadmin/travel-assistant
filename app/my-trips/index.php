<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;

//Include the necessary scripts
	$essentials->includeCSS("my-trips.min.css");
	$essentials->includePluginClass("display/Map_Images");
	$essentials->includePluginClass("display/User");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("my-trips.superpackage.min.js");
	$essentials->requireLogin();
	$essentials->setTitle("My Trips");
	
//Fetch the user's statistics
	$stats = FFI\TA\User::getStats();

	if ($stats->Next != "0") {
		$dateFormatter = DateTime::createFromFormat("Y-m-d H:i:s", $stats->Next);
		$date = $dateFormatter->format("M jS");
	} else {
		$date = "None";
	}
	
//Display the user's trip stats and a map of visited places
	echo "<section class=\"welcome\">
<article class=\"map\"><h2>Map of Visited Locations</h2></article>

<ul class=\"stats\">
<li class=\"next\"><p>" . $date . "<span>Next Trip</span></p></li>
<li class=\"needed\"><p><span class=\"count\">" . $stats->Needed . "</span><span>" . ($stats->Needed == 1 ? "Trip" : "Trips") . " I've Needed</span></p></li>
<li class=\"shared\"><p><span class=\"count\">" . $stats->Shared . "</span><span>" . ($stats->Shared == 1 ? "Trip" : "Trips") . " I've Shared</span></p></li>
<li class=\"other\"><p><span class=\"count\">" . $stats->Other . "</span><span>Other " . ($stats->Other == 1 ? "Trip" : "Trips") . "</span></p></li>
<li class=\"recurring\"><p><span class=\"count\">" . $stats->Recurring . "</span><span>Active Recurring " . ($stats->Recurring == 1 ? "Trip" : "Trips") . "</span></p></li>
</ul>
</section>

";
	
//Display the trips the user needed
	$needed = FFI\TA\User::getNeeds();
	$date = new DateTime();
	$now = $date->getTimestamp();
	
	echo "<section class=\"content even\">
<h2>Trips I've Needed</h2>

";

	if (count($needed)) {
		echo "<ul class=\"needed\">";
	
		foreach($needed as $need) {
		//Generate information used throughout this iteration
			$leavingFormatter = DateTime::createFromFormat("Y-m-d H:i:s", $need->Leaving);
			$leaveTS = $leavingFormatter->getTimestamp();
			$endFormatter = DateTime::createFromFormat("Y-m-d", $need->EndDate);
			$endTS = $endFormatter->getTimestamp();
			
			$text = htmlentities($need->FromCity . ", " . $need->FromState . " to " . $need->ToCity . ", " . $need->ToState);
			$imgLarge = FFI\TA\Map_Images::myTripsPreviewLarge($need->FromLatitude, $need->FromLongitude, $need->ToLatitude, $need->ToLongitude);
			$imgSmall = FFI\TA\Map_Images::myTripsPreviewSmall($need->FromLatitude, $need->FromLongitude, $need->ToLatitude, $need->ToLongitude);
			
		//Generate a pill for each of the items
			$message = "";
			$endPast = $now > $endTS;
			
			if ($need->Fulfilled > 0) {
				$message = "<span class=\"notice fulfilled\">Fulfilled</span>";
			}
			
			if ($need->Fulfilled > 0 && $now < $leaveTS) {
				$message = "<span class=\"notice fulfilled\">Future Ride</span>";
			}
			
			if ($need->Fulfilled > 0 && $now > $leaveTS && $now < $endTS) {
				$message = "<span class=\"notice recurring\">Recurring</span>";
			}
			
			if ($need->Fulfilled == 0 && $now > $leaveTS) {
				$message = "<span class=\"notice warning\">Not Fulfilled</span>";
			}
		
		//Display the trip entry
			echo "
<li>
<img alt=\"" . $text . "\" class=\"desktop\" src=\"" . $imgLarge . "\">
<img alt=\"" . $text . "\" class=\"mobile\" src=\"" . $imgSmall . "\">
<h3>" . $need->FromCity . ", " . $need->FromState . $message . "</h3>
<h4>to " . $need->ToCity . ", " . $need->ToState . "</h4>
<p>" . $leavingFormatter->format("M jS, Y \a\\t g:i A") . "</p>
";

			if ($endTS) {
				echo "<p>" . ($endPast ? "ended" : "ending") . " on " . $endFormatter->format("M jS, Y") . "</p>
";
			}
	
			echo "<a class=\"btn btn-primary edit\" href=\"" . $essentials->friendlyURL("need-a-ride/" . $need->ID) . "\"><i class=\"icon-pencil icon-white\"></i><span class=\"desktop\"> Edit</span></a>
<button class=\"btn btn-danger delete\" data-id=\"" . $need->ID . "\" data-type=\"need\"><i class=\"icon-trash icon-white\"></i><span class=\"desktop\"> Delete</span></button>
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
	$shared = FFI\TA\User::getShares();

	echo "<section class=\"content\">
<h2>Trips I've Shared</h2>

";

	if (count($shared)) {
		echo "<ul class=\"shared\">";
	
		foreach($shared as $share) {
		//Generate information used throughout this iteration
			$leavingFormatter = DateTime::createFromFormat("Y-m-d H:i:s", $share->Leaving);
			$leaveTS = $leavingFormatter->getTimestamp();
			$endFormatter = DateTime::createFromFormat("Y-m-d", $share->EndDate);
			$endTS = $endFormatter->getTimestamp();
			
			$text = htmlentities($share->FromCity . ", " . $share->FromState . " to " . $share->ToCity . ", " . $share->ToState);
			$imgLarge = FFI\TA\Map_Images::myTripsPreviewLarge($share->FromLatitude, $share->FromLongitude, $share->ToLatitude, $share->ToLongitude);
			$imgSmall = FFI\TA\Map_Images::myTripsPreviewSmall($share->FromLatitude, $share->FromLongitude, $share->ToLatitude, $share->ToLongitude);
			
		//Generate a pill for each of the items
			$message = "";
			$endPast = $now > $endTS;
			
			if ($share->Fulfilled > 0) {
				$message = "<span class=\"notice fulfilled\">Shared</span>";
			}
			
			if ($share->Fulfilled > 0 && $now < $leaveTS) {
				$message = "<span class=\"notice fulfilled\">Future Ride</span>";
			}
			
			if ($share->Fulfilled > 0 && $now > $leaveTS && $now < $endTS) {
				$message = "<span class=\"notice recurring\">Recurring</span>";
			}
			
			if ($share->Fulfilled == 0 && $now > $leaveTS) {
				$message = "<span class=\"notice warning\">Not Shared</span>";
			}
		
		//Display the trip entry
			echo "
<li>
<img alt=\"" . $text . "\" class=\"desktop\" src=\"" . $imgLarge . "\">
<img alt=\"" . $text . "\" class=\"mobile\" src=\"" . $imgSmall . "\">
<h3>" . $share->FromCity . ", " . $share->FromState . $message . "</h3>
<h4>to " . $share->ToCity . ", " . $share->ToState . "</h4>
<p>" . $leavingFormatter->format("M jS, Y \a\\t g:i A") . "</p>
";

			if ($endTS) {
				echo "<p>" . ($endPast ? "ended" : "ending") . " on " . $endFormatter->format("M jS, Y") . "</p>
";
			}
	
			echo "<a class=\"btn btn-primary edit\" href=\"" . $essentials->friendlyURL("share-a-ride/" . $share->ID) . "\"><i class=\"icon-pencil icon-white\"></i><span class=\"desktop\"> Edit</span></a>
<button class=\"btn btn-danger delete\" data-id=\"" . $share->ID . "\" data-type=\"share\"><i class=\"icon-trash icon-white\"></i><span class=\"desktop\"> Delete</span></button>
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
</section>

";

//Display other trips the user has been involved with
	$others = FFI\TA\User::getOther();

	echo "<section class=\"content even other\">
<h2>Other Trips I've Participated In</h2>
<p>Rides posted by other people which you have participated in by clicking either the &quot;I Need This Ride&quot; or &quot;I Can Help&quot; buttons will show in a list below.</p>

";

	if (count($others)) {
		echo "<ul class=\"other\">";
	
		foreach($others as $other) {
		//Generate information used throughout this iteration
			$leavingFormatter = DateTime::createFromFormat("Y-m-d H:i:s", $other->Leaving);
			$endFormatter = DateTime::createFromFormat("Y-m-d", $other->EndDate);
			$endTS = $endFormatter->getTimestamp();
			
			$text = htmlentities($other->FromCity . ", " . $other->FromState . " to " . $other->ToCity . ", " . $other->ToState);
			$imgLarge = FFI\TA\Map_Images::myTripsPreviewLarge($other->FromLatitude, $other->FromLongitude, $other->ToLatitude, $other->ToLongitude);
			$imgSmall = FFI\TA\Map_Images::myTripsPreviewSmall($other->FromLatitude, $other->FromLongitude, $other->ToLatitude, $other->ToLongitude);
		
		//Display the trip entry
			echo "
<li>
<img alt=\"" . $text . "\" class=\"desktop\" src=\"" . $imgLarge . "\">
<img alt=\"" . $text . "\" class=\"mobile\" src=\"" . $imgSmall . "\">
<h3>" . $other->FromCity . ", " . $other->FromState . "</h3>
<h4>to " . $other->ToCity . ", " . $other->ToState . "</h4>
<p>" . ($other->Type == "ASSIST" ? "You shared a ride with " . $other->Person : $other->Person . " shared a ride with you") . "</p>
<p>" . $leavingFormatter->format("M jS, Y \a\\t g:i A") . "</p>
";

			if ($endTS) {
				$endPast = $now > $endTS;
				echo "<p>" . ($endPast ? "ended" : "ending") . " on " . $endFormatter->format("M jS, Y") . "</p>
";
			}
	
			echo "</li>
";
		}
		
		echo "</ul>";
	} else {
		echo "<div class=\"none show\">
<p>You haven't been involved with anyone else's trips.</p>
<a href=\"" . $essentials->friendlyURL("") . "\">Browse available trips &#187;</a>
</div>";
	}

	echo "
</section>";
?>