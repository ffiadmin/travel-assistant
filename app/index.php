<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;
	
//Include the necessary scripts
	$essentials->setTitle("Travel Assistant");
	$essentials->includePluginClass("Destination_Manager");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("scripts/FFI_Map_Maker.js");
	$essentials->includeCSS("styles/welcome.css");
	$essentials->includeHeadHTML("<script>\$(function(){\$('article.map').FFI_Map_Maker()})</script>");

//Display the a Google Maps of the United States with all avaliable or needed trip locations
	echo "<article class=\"map\"><h2>Map of Avaliable or Needed Trip Locations</h2></article>

";
	
//Display the plugin description and navigation tiles section
	echo "<article class=\"center content welcome\">
<h2>SGA Travel Assistant</h2>
<p>Whether you are a commuter and are looking for someone with whom you can share a ride, or already thinking about planning your next trip home, the SGA Travel Assistant is here to help. If you are in need of a ride home, browse the listing of avaliable rides to your hometown or post your need and have someone help you out. If you have an extra seat or two to spare, you can post their avaliability here and help someone with their ride home.</p>

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
<h3>My Trips</h3>
</a>
</li>
</ul>
</article>

";

//Browse trips section
	$totals = FFI\TA\Destination_Manager::getTotals();

	echo "<article class=\"center content even browse\">
<h2>Browse Trips</h2>

<ul class=\"quick-ref\">
<li><span>" . $totals["needs"] . " <span>" . ($totals["needs"] == 1 ? "ride" : "rides") . "</span> needed</span></li>
<li><span>" . $totals["shares"] . " <span>" . ($totals["shares"] == 1 ? "ride" : "rides") . "</span> avaliable</span></li>
<li class=\"college\"><a href=\"" . $essentials->friendlyURL("browse/pennsylvania/grove-city") . "\">Rides for</a></li>
</ul>

" . FFI\TA\Destination_Manager::getStatesList() . "
</article>";
?>
