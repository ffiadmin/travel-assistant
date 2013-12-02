/**
 * Google Maps directions pluguin
 *
 * This plugin will leverage the Google Maps API to fetch and display
 * a set of directions between two points of interest, along with the
 * total distance traveled and the amount of time required for such 
 * a trip.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI_TA
 * @since     1.0.0
*/

(function($) {
	$.fn.FFI_TA_Steps = function(fromPOI, toPOI) {
		return this.each(function() {
			var container = $(this);
			
			$.ajax({
				data        : {
					from    : fromPOI,
					to      : toPOI
				},
			    dataType    : 'json',
				type        : 'GET',
				url         : $.fn.FFI_TA_Steps.defaults.processURL,
				error       : function(jqXHR, textStatus) {
					alert('An error was encountered while creating a list of directions for this trip.\n\nIf this is the first time you have seen this error, wait one minute and try reloading this page. If this error continues to occur, contact the site administrator for assistance and include the details listed below.\n\n-----------------------\n\nPage URL:\n' + window.location.href + '\n\nAJAX Status:\n' + textStatus);
				},
				success     : function(data) {
					var leg = data.routes[0].legs[0];
					var steps = leg.steps;
					
				//Render the total trip distance and duration
					var HTML = '<ul class="totals">';
					HTML += '<li><div><div>' + $.fn.FFI_TA_Steps.boldNumbers(leg.distance.text) + '</div></div></li>';
					HTML += '<li><div><div>' + $.fn.FFI_TA_Steps.boldNumbers(leg.duration.text) + '</div></div></li>';
					HTML += '<li class="print" onclick="window.print()"><figure></figure></li>';
					HTML += '</ul>';
					
				//Render the directions from POI A to B
					HTML += '<ol class="steps">';
					
					for (var i = 0; i < steps.length; ++i) {
						HTML += '<li><div>' + steps[i].html_instructions + '</div><span class="distance">' + $.fn.FFI_TA_Steps.boldNumbers(steps[i].distance.text) + '</span><span class="duration">' + $.fn.FFI_TA_Steps.boldNumbers(steps[i].duration.text) + '</span></li>';
					}
					
					HTML += '</ol>';
					
					container.append(HTML);
				}
			});
		});
	}
	
/**
 * This function takes and will BOLD all of the numbers within the
 * string, including any commas and period which make up larger numbers
 * or floating point numbers.
 * 
 * @access public
 * @param  string input The string to parse and BOLD all numbers
 * @return string       The input string with all numbers in BOLD
 * @since  1.0.0
*/

	$.fn.FFI_TA_Steps.boldNumbers = function(input) {
		var output = '';
		
		for (var i = 0; i < input.length; ++i) {
			output += (!isNaN(input.charAt(i)) || input.charAt(i) == '.' || input.charAt(i) == ',') ? ('<b>' + input.charAt(i) + '</b>') : input.charAt(i) ;
		}
		
		return output;
	}

/**
 * Plugin default settings
 *
 * @access public
 * @type   object<string>
*/

	$.fn.FFI_TA_Steps.defaults = {
		'processURL' : document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'wp-content/plugins/travel-assistant/app/system/ajax/directions.php'
	};
})(jQuery);