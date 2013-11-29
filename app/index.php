<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;
	
//Include the necessary scripts
	$essentials->includeCSS("welcome.min.css");
	$essentials->includeHeadHTML("<script>\$(function(){\$('article.map').FFI_TA_Map_Maker()})</script>");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("map-maker.min.js");
	$essentials->includePluginClass("display/State");
	$essentials->includePluginClass("display/Trip_Info");
	$essentials->setTitle("Travel Assistant");

//Display the a Google Map of the United States with all available or needed trip locations
	echo "<article class=\"map\"><h2>Map of Available or Needed Trip Locations</h2></article>

";
	
//Display the plugin description and navigation tiles section
	echo "<article class=\"center content welcome\">
<h2>SGA Travel Assistant</h2>
<p>Whether you are a commuter and are looking for someone with whom you can share a ride, or already thinking about planning your next trip home, the SGA Travel Assistant is here to help. If you are in need of a ride home, browse the listing of available rides to your hometown or post your need and have someone help you out. If you have an extra seat or two to spare, you can post their availability here and help someone with their ride home.</p>

<ul>
<li class=\"request\">
<a href=\"" . $essentials->friendlyURL("need-a-ride") . "\">
<h3>Request Ride</h3>
</a>
</li>

<li class=\"share\">
<a href=\"" . $essentials->friendlyURL("share-a-ride") . "\">
<h3>Share Ride</h3>
</a>
</li>

<li class=\"my-trips\">
<a href=\"" . $essentials->friendlyURL("my-trips") . "\">
<h3>My Trips<span class=\"tablet\"> &#187;</span></h3>
</a>
</li>
</ul>
</article>

";

//Browse trips section
	$totals = FFI\TA\Trip_Info::getTotals();

	echo "<article class=\"center content even browse\">
<h2>Browse Trips</h2>

<ul class=\"quick-ref\">
<li><span>" . $totals["needs"] . " <span>" . ($totals["needs"] == 1 ? "ride" : "rides") . "</span> needed</span></li>
<li><span>" . $totals["shares"] . " <span>" . ($totals["shares"] == 1 ? "ride" : "rides") . "</span> available</span></li>
<li class=\"college\"><a href=\"" . $essentials->friendlyURL("browse/pennsylvania/grove-city") . "\">Rides for</a></li>
</ul>

" . FFI\TA\State::getStatesList() . "
</article>";
?>