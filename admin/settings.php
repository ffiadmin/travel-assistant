<?php
//Include the necessary scripts
	require_once(dirname(dirname(__FILE__)) . "/lib/display/Admin.php");
	
//Fetch the data from the plugin settings table
	$API = FFI\TA\Admin::settings();
	
//Display a the page heading
	echo "<div class=\"wrap\">
<h2>Settings</h2>

";

//Display the page's success message
	if (isset($_GET['updated'])) {
		echo "<div class=\"updated\">
<p><strong>Success:</strong> The Travel Assistant settings have been updated.</p>
</div>

";
	}

//Display a table containing a listing of all plugin settings
	$zones = array(
		"America/New_York"    => "Eastern Time",
		"America/Chicago"     => "Central Time",
		"America/Denver"      => "Mountain Time",
		"Amercia/Los_Angeles" => "Pacific Time",
		"America/Anchorage"   => "Alaska Time",
		"Pacific/Honolulu"    => "Hawaii Time"
	);
		
	foreach($zones as $key => $value) {
		$options .= "<option" . ($key == $API->TimeZone ? " selected" : "") . " value=\"" . $key . "\">" . $value . "</option>
";
	}

	echo "<form action=\"" . site_url() . "/wp-content/plugins/travel-assistant/admin/processing/settings.php\" method=\"post\">
<table class=\"form-table\">
<tbody>
<tr>
<th><label for=\"email-name\">Automated Email &quot;From&quot; Name:</label></th>
<td><input autocomplete=\"off\" class=\"regular-text\" id=\"email-name\" name=\"email-name\" type=\"text\" value=\"" . $API->EmailName . "\"></td>
</tr>

<tr>
<th><label for=\"email-address\">Automated Email &quot;From&quot; Address:</label></th>
<td>
<input autocomplete=\"off\" class=\"regular-text\" id=\"email-address\" name=\"email-address\" type=\"text\" value=\"" . $API->EmailAddress . "\">
<p class=\"description\">Should come from a domain you've registered in the Mandrill Control Panel.</p>
</td>
</tr>

<tr>
<th><label for=\"timezone\">Plugin Time Zone:</label></th>
<td>
<select id=\"timezone\" name=\"timezone\">
" . $options . "
</select>
</td>
</tr>
</tbody>
</table>

<p class=\"submit\">
<input class=\"button button-primary\" id=\"submit\" name=\"submit\" value=\"Update Settings\" type=\"submit\">
</p>
</form>
</div>";
?>