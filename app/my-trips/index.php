<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;

//Include the necessary scripts
	$essentials->includeCSS("my-trips.css");
	$essentials->includeHeadHTML("<script>\$(function(){\$('article.map').FFI_TA_Visited()})</script>");
	$essentials->includePluginClass("display/City");
	$essentials->includePluginClass("display/Map_Images");
	$essentials->includePluginClass("display/Trip_Info");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("FFI_TA_Visited.js");
	$essentials->includeJS("my-trips.js");
	$essentials->requireLogin();
	$essentials->setTitle("My Trips");
	
//Fetch the user's statistics
	$stats = FFI\TA\Trip_Info::getUserStats();

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
<li class=\"needed\"><p>" . $stats->Needed . "<span>Trips I've Needed</span></p></li>
<li class=\"shared\"><p>" . $stats->Shared . "<span>Trips I've Shared</span></p></li>
<li class=\"recurring\"><p>" . $stats->Recurring . "<span>Active Recurring Trips</span></p></li>
</ul>
</section>

";
	
//Display the trips the user needed
	$needed = FFI\TA\City::getMyNeeds($essentials->user->ID);
	$now = time();
	
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
			$imgLarge = Map_Images::myTripsPreviewLarge($need->FromLatitude, $need->FromLongitude, $need->ToLatitude, $need->ToLongitude);
			$imgSmall = Map_Images::myTripsPreviewSmall($need->FromLatitude, $need->FromLongitude, $need->ToLatitude, $need->ToLongitude);
			
		//Generate a tooltip for each of the items
			$message = "";
			
			if ($need->Fulfilled > 0) {
				$endPast = true;
				$message = "<span class=\"notice fulfilled\">Fulfilled</span>";
			}
			
			if ($need->Fulfilled > 0 && $now < $leaveTS) {
				$endPast = false;
				$message = "<span class=\"notice fulfilled\">Future Ride</span>";
			}
			
			if ($need->Fulfilled > 0 && $now > $leaveTS && $now < $endTS) {
				$endPast = false;
				$message = "<span class=\"notice recurring\">Recurring</span>";
			}
			
			if ($need->Fulfilled == 0 && $now > $leaveTS) {
				$endPast = true;
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
	$shared = FFI\TA\City::getMyShares($essentials->user->ID);

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
			$imgLarge = Map_Images::myTripsPreviewLarge($share->FromLatitude, $share->FromLongitude, $share->ToLatitude, $share->ToLongitude);
			$imgSmall = Map_Images::myTripsPreviewSmall($share->FromLatitude, $share->FromLongitude, $share->ToLatitude, $share->ToLongitude);
			
		//Generate a tooltip for each of the items
			$message = "";
			
			if ($share->Fulfilled > 0) {
				$endPast = true;
				$message = "<span class=\"notice fulfilled\">Shared</span>";
			}
			
			if ($share->Fulfilled > 0 && $now < $leaveTS) {
				$endPast = false;
				$message = "<span class=\"notice fulfilled\">Future Ride</span>";
			}
			
			if ($share->Fulfilled > 0 && $now > $leaveTS && $now < $endTS) {
				$endPast = false;
				$message = "<span class=\"notice recurring\">Recurring</span>";
			}
			
			if ($share->Fulfilled == 0 && $now > $leaveTS) {
				$endPast = true;
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
	
			echo "<a class=\"btn btn-primary edit\" href=\"" . $essentials->friendlyURL("need-a-ride/" . $share->ID) . "\"><i class=\"icon-pencil icon-white\"></i><span class=\"desktop\"> Edit</span></a>
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
</section>";
?>