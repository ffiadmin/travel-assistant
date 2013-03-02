<?php
//Include the necessary scripts
	$essentials->requireLogin();
	$essentials->setTitle("Ask for Ride");
	$essentials->includePluginClass("Destination_Manager");
	$essentials->includeJS("//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
	$essentials->includeJS("//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js");
	$essentials->includeJS("//cdnjs.cloudflare.com/ajax/libs/tinymce/3.5.8/tiny_mce.js");
	$essentials->includeJS("scripts/jquery-ui.timepicker.js");
	$essentials->includeJS("scripts/ride.js");
	$essentials->includeCSS("styles/ride.css");
	
	echo "<h1>Ask for Ride</h1>
	
<form class=\"form-horizontal\">\n";
	
//Display the directions
	echo "<section class=\"welcome\">
<h2>Ask for a Ride</h2>
<p>If you are in need of ride back home or back to college, use this page to post your request. If a willing individual finds your request, he or she will be in touch with you. This page can also be used if you are a commuter and would like to schedule a regular trip with a driver.</p>
</section>

";

//Display the (2 of the) 5 W questions section
	echo "<section class=\"step\">
<header>
<h2>The Five W Questions</h2>
<h3>... but we really only care about two of them.</h3>
<h4 class=\"step\">1</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\" for=\"who\">Who:</label>
<div class=\"controls\">
<input disabled id=\"who\" name=\"who\" type=\"text\" value=\"" . htmlentities($essentials->user->user_firstname . " " . $essentials->user->user_lastname) . "\">
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"what\">What:</label>
<div class=\"controls\">
<input disabled id=\"what\" name=\"what\" type=\"text\" value=\"You need a lift\">
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"when\">When:</label>
<div class=\"controls\">
<input id=\"when\" name=\"when\" placeholder=\"When do you plan on leaving?\" type=\"text\">
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"where-city\">Where:</label>
<div class=\"controls\">
<div class=\"input-append\">
<input id=\"where-city\" name=\"where-city\" placeholder=\"To which city are you going?\" type=\"text\">

<select id=\"where-state\" name=\"where-state\">
<option selected value=\"\">- Select a State -</option>
" . Destination_Manager::buildStatesDropDown("") . "
</select>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"why\">Why:</label>
<div class=\"controls\">
<input disabled id=\"why\" name=\"why\" type=\"text\" value=\"None of our business\">
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
<label class=\"control-label\" for=\"seats\">Reserve:</label>
<div class=\"controls\">
<div class=\"input-append\">
<input class=\"input-mini\" id=\"seats\" name=\"seats\" type=\"text\" value=\"1\">
<span class=\"add-on\">seat(s)</span>
</div>

<div class=\"slider\" style=\"display:inline-block; width: 300px; margin-left: 178px;\"></div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"days\">I need:</label>
<div class=\"controls\">
<div class=\"input-append\">
<input class=\"input-mini\" id=\"days\" name=\"days\" type=\"text\" value=\"1\">
<span class=\"add-on\">day(s) notice before my trip</span>
</div>

<div class=\"slider\" style=\"display:inline-block; width: 300px; margin-left: 50px;\"></div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"reimburse\">I can pay for:</label>
<div class=\"controls\">
<div class=\"input-prepend input-append\">
<span class=\"add-on\">\$</span>
<input class=\"input-mini\" id=\"reimburse\" name=\"reimburse\" type=\"text\" value=\"5\">
<span class=\"add-on\">.00 of gas</span>
</div>

<div class=\"slider\" style=\"display:inline-block; width: 300px; margin-left: 135px;\"></div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\">I'll be bringing luggage:</label>
<div class=\"controls\">
<div class=\"btn-group\" data-toggle=\"buttons-radio\">
<button class=\"btn\" type=\"button\">Yes</button>
<button class=\"btn\" type=\"button\">No</button>
</div>
</div>
</div>
</section>

";

//Display the trip recurrence section
	echo "<section class=\"step\">
<header>
<h2>Trip Recurrence (Optional)</h2>
<h3>This section is probably best for commuters. Do you make this trip often, and would like to arrange a regular pick up schedule?</h3>
<h4 class=\"step\">3</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\" for=\"recurrence\">I need a ride regularly:</label>
<div class=\"controls\">
<input id=\"recurrence\" name=\"recurrence\" type=\"checkbox\">
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\">I need a ride every:</label>
<div class=\"controls\">
<div class=\"btn-group\" data-toggle=\"buttons-checkbox\">
<button class=\"btn\" disabled type=\"button\">Sunday</button>
<button class=\"btn\" disabled type=\"button\">Monday</button>
<button class=\"btn\" disabled type=\"button\">Tuesday</button>
<button class=\"btn\" disabled type=\"button\">Wednesday</button>
<button class=\"btn\" disabled type=\"button\">Thursday</button>
<button class=\"btn\" disabled type=\"button\">Friday</button>
<button class=\"btn\" disabled type=\"button\">Saturday</button>
</div>
</div>
</div>
</section>

";

//Display the comments section
	echo "<section class=\"step\">
<header>
<h2>Closing Thoughts (Optional)</h2>
<h3>Is there anything you'd like to share with your driver?</h3>
<h4 class=\"step\">4</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\">Comments:</label>
<div class=\"controls\">
<textarea></textarea>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\">I will bring my pet piranha:</label>
<div class=\"controls\">
<div class=\"btn-group\" data-toggle=\"buttons-radio\">
<button class=\"btn\" type=\"button\">Yes</button>
<button class=\"btn\" type=\"button\">No</button>
</div>
</div>
</div>
</section>

";

//Display the submit button
	echo "<section class=\"no-border step\">
<button class=\"btn btn-primary\" type=\"submit\">Submit Request</button>
<button class=\"btn\" type=\"button\">Cancel</button>
</section>
</form>";
?>