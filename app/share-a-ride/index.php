<?php
//Include the necessary scripts
	$essentials->includeCSS("ride.superpackage.min.css");
	$essentials->includeJS("//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js");
	$essentials->includeJS("//tinymce.cachefly.net/4/tinymce.min.js");
	$essentials->includeJS("ride.superpackage.min.js");
	$essentials->includePluginClass("display/Ride_Share_Display");
	$essentials->includePluginClass("exceptions/No_Data_Returned");
	$essentials->includePluginClass("exceptions/Validation_Failed");
	$essentials->includePluginClass("processing/Ride_Share_Process");
	$essentials->requireLogin();
	$essentials->setTitle("Share a Ride");
	
//Instantiate the form element display and processor classes
	$params = $essentials->params ? $essentials->params[0] : 0;
	$userID = $essentials->user->ID;
	
	try {
		$display = new FFI\TA\Ride_Share_Display($params, $userID);
		new FFI\TA\Ride_Share_Process($params);
	} catch (No_Data_Returned $e) {
		wp_redirect($essentials->friendlyURL("share-a-ride"));
		exit;
	} catch (Validation_Failed $e) {
		echo $e->getMessage();
		exit;
	} catch (Exception $e) {
		wp_redirect($essentials->friendlyURL("share-a-ride"));
		exit;
	}
	
//Display the page
	echo "<h1>Share a Ride</h1>
	
<form class=\"form-horizontal\" method=\"post\">\n";

//Display the splash section
	$rand = mt_rand(0, 1);
	$classes = array("share-legacy", "share-color");
	
	echo "<section class=\"" . $classes[$rand] . "\" id=\"splash\">
<div class=\"ad-container\">
<div class=\"ad-contents\">
<h2>Share a Ride</h2>
</div>
</div>
</section>

";
	
//Display the directions
	echo "<section class=\"welcome\">
<h2>Share a Ride</h2>
<p>If you have few spare seats you could share during your back home or back to college, use this page to post this opening. If an individual in need of a ride finds your request, he or she will be in touch with you. This page can also be used if you are a commuter and could share a recurring trip with a few passengers.</p>
</section>

";

//Display the (2 of the) 5 W questions section
	echo "<section class=\"step stripe\">
<header>
<h2>The Five W Questions</h2>
<h3>... but we really only care about two of them.</h3>
<h4 class=\"step\">1</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\" for=\"who\">Who:</label>
<div class=\"controls\">
" . $display->getWho() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"what\">What:</label>
<div class=\"controls\">
" . $display->getWhat() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"when\">When:</label>
<div class=\"controls\">
" . $display->getWhen() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"from-where-city\">From Where:</label>
<div class=\"controls\">
<div class=\"input-append input-prepend\">
" . $display->getFromWhere() . "
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"to-where-city\">To Where:</label>
<div class=\"controls\">
<div class=\"input-append input-prepend\">
" . $display->getToWhere() . "
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"why\">Why:</label>
<div class=\"controls\">
" . $display->getWhy() . "
</div>
</div>
</section>

";

//Display the trip details section
	echo "<section class=\"step\">
<header>
<h2>Trip Details</h2>
<h3>Tell us a little bit about your trip.</h3>
<h4 class=\"step\">2</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\" for=\"seats\">I have room for:</label>
<div class=\"controls\">
<div class=\"input-append\">
" . $display->getSeats() . "
<span class=\"add-on\">individual(s)</span>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"males\">Joining me will be:</label>
<div class=\"controls\">
<div class=\"input-append input-prepend\">
" . $display->getMales() . "
<span class=\"add-on\">male(s) and</span>
" . $display->getFemales() . "
<span class=\"add-on\">female(s)</span>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"time\">I'll drive an extra:</label>
<div class=\"controls\">
<div class=\"input-append\">
" . $display->getMinutesWithin() . "
<span class=\"add-on\">minute(s) to drop off passengers</span>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"reimburse\">I want reimbursed for:</label>
<div class=\"controls\">
<div class=\"input-prepend input-append\">
<span class=\"add-on\">\$</span>
" . $display->getGasMoney() . "
<span class=\"add-on\">.00 of gas per person</span>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\">Room for luggage:</label>
<div class=\"controls\">
" . $display->getLuggage() . "
</div>
</div>
</section>

";

//Display the trip recurrence section
	echo "<section class=\"step stripe\">
<header>
<h2>Trip Recurrence (Optional)</h2>
<h3>This section is probably best for commuters. Do you make this trip often, and would like to arrange a regular pick up schedule?</h3>
<h4 class=\"step\">3</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\">I can share regularly:</label>
<div class=\"controls\">
" . $display->getRecurrence() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\">I'll share a ride every:</label>
<div class=\"controls\">
" . $display->getRecurrenceDays() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"until\">Until:</label>
<div class=\"controls\">
" . $display->getEndDate() . "
</div>
</div>
</section>

";

//Display the comments section
	echo "<section class=\"step\">
<header>
<h2>Closing Thoughts (Optional)</h2>
<h3>Is there anything you'd like to share with your passenger(s)?</h3>
<h4 class=\"step\">4</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\">Comments:</label>
<div class=\"controls\">
" . $display->getComments() . "
</div>
</div>
</section>

";

//Display the comments section
	echo "<section class=\"step stripe\">
<header>
<h2>The Fine Print</h2>
<h3>... but we won't make ours so small that you can't read it.</h3>
<h4 class=\"step\">5</h4>
</header>

<p>This service provided by the Student Government Association at Grove City College is strictly intended to serve as a convenient service to Grove City College attendees. Neither the Student Government Association nor Grove City College can be responsible for any property damage, personal injury, or further inconveniences which may result from sharing a ride with another student or faculty member. It is solely your responsiblity to take any necessary steps before and during the trip to prevent property damage and/or personal injury.</p>
</section>

";

//Display the submit button
	echo "<section class=\"no-border step stripe\">
<button class=\"btn btn-warning\" type=\"submit\">Agree<span class=\"collapse\"> to Terms</span> &amp; Submit<span class=\"collapse\"> Request</span></button>
<a class=\"btn\" href=\"" . $essentials->friendlyURL("") . "\">Cancel</a>
</section>
</form>";
?>