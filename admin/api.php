<?php
//Include the necessary scripts
	require_once(dirname(dirname(__FILE__)) . "/lib/display/Admin.php");
	
//Fetch the data from the plugin API table
	$API = FFI\TA\Admin::APIData();

//Display a table containing a listing of all system APIs
	echo "<div class=\"wrap\">
<h2>API Management</h2>

";

//Display the page's success message
	if (isset($_GET['updated'])) {
		echo "<div class=\"updated\">
<p><strong>Success:</strong> The Travel Assistant API keys have been updated.</p>
</div>

";
	}

//Display a table containing a listing of all plugin settings
	echo "<p>The Travel Assistant requires access to two third-party services in order to provide features such as dynamically generated maps, interactive maps, trip directions, and sending emails.</p>
<p>Please open an account with <a href=\"https://code.google.com/apis/console/\" target=\"_blank\">Google API</a> for the generation of maps and trip directions, and <a href=\"http://mandrill.com/\" target=\"_blank\">Mandrill</a> for sending emails. Copy the API keys from these services into the form below. A free subscription to each of these services will suffice for sites with low or medium amounts of traffic.</p>
<p><strong>Note:</strong> You will need to activate the &quot;Google Maps API v3&quot; and &quot;Static Maps API&quot; services in order for the Travel Assistant to properly utilize these services.</p>

<form action=\"" . site_url() . "/wp-content/plugins/travel-assistant/admin/processing/api.php\" method=\"post\">
<table class=\"form-table\">
<tbody>
<tr>
<th><label for=\"google\">Google Maps API Key:</label></th>
<td><input autocomplete=\"off\" class=\"regular-text\" id=\"google\" name=\"google\" type=\"text\" value=\"" . htmlentities($API->GoogleMaps) . "\"></td>
</tr>

<tr>
<th><label for=\"mandrill\">Mandrill API Key:</label></th>
<td><input autocomplete=\"off\" class=\"regular-text\" id=\"mandrill\" name=\"mandrill\" type=\"text\" value=\"" . htmlentities($API->MandrillKey) . "\"></td>
</tr>
</tbody>
</table>

<p class=\"submit\">
<input class=\"button button-primary\" id=\"submit\" name=\"submit\" value=\"Update API Keys\" type=\"submit\">
</p>
</form>
</div>";
?>