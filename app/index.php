<?php
//Fetch the Google Maps API key for use in the Google Maps JavaScript request
	$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
	$API = $APIs[0]->GoogleMaps;
	
//Include the necessary scripts
	$essentials->setTitle("Travel Assistant");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=" . $API . "&sensor=false");
	$essentials->includeJS("scripts/FFI_Map_Maker.js");
	$essentials->includeJS("scripts/FFI_Jump.min.js");
	$essentials->includeCSS("styles/welcome.css");
	
	echo "<script>
$(function() {
	$('select#states').FFI_Jump();
	$('article#map').FFI_Map_Maker();
});
</script>

";

//Display the a Google Maps of the United States with all avaliable or needed trip locations
	echo "<article id=\"map\"><h2>Map of Avaliable or Needed Trip Locations</h2></article>

";
	
//Description and "I Have or Need a Ride" Section
	$states = $wpdb->get_results("SELECT `Name` FROM `ffi_ta_states` ORDER BY `Name` ASC");
	$list = "<select name=\"states\" id=\"states\">
<option selected value=\"\">- Select a State -</option>\n";
	
	foreach($states as $state) {
		$list .= "<option value=\"" . strtolower($state->Name) . "\">" . $state->Name . "</option>\n";
	}
	
	$list .= "</select>";

	echo "<article class=\"center content\" id=\"welcome\">
<h2>SGA Travel Assistant</h2>
<p>Whether you are a commuter and are looking for someone with whom you can share a ride, or already thinking about planning your next trip home, the SGA Travel Assistant is here to help. If you are in need of a ride home, browse the listing of avaliable rides to your hometown or post your need and have someone help you out. If you have an extra seat or two to spare, you can post their avaliability here and help someone with their ride home.</p>

<ul>
<li class=\"need\">
<h3>I Need a Ride</h3>

<div class=\"control-group\">
<label class=\"control-label\" for=\"states\">Browse by State:</label>
<div class=\"input-append\">
" . $list . "
<button class=\"btn btn-primary\" id=\"jumper\">Go!</button>
</div>
</div>

<a class=\"btn btn-block\" href=\"" . $essentials->friendlyURL("need-a-ride") . "\">Ask for a Ride</a>
</li>

<li class=\"share\">
<a href=\"" . $essentials->friendlyURL("share-a-ride") . "\">
<h3>I Can Share a Ride</h3>
</a>
</li>
</ul>
</article>";
?>