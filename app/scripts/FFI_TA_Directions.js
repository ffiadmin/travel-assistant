/**
 * Google Maps directions routing pluguin
 *
 * This plugin requires the Google Maps JavaScript API v3 to be included
 * in the DOM before this plugin is instantited.
 *
 * This plugin will leverage the Google Maps JavaScript API v3 to create 
 * a Google Maps widget in the targeted object with directions between
 * two points of interest
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI_TA
 * @since     1.0
*/

(function($) {
	$.fn.FFI_TA_Directions = function(fromPOI, toPOI) {
		return this.each(function() {
			var directionsRenderer = new google.maps.DirectionsRenderer();
			var directionsService = new google.maps.DirectionsService();
			
		//Google Maps instantiation and configuration
			var map = new google.maps.Map($(this).get(0), {
				center            : new google.maps.LatLng(37.0902400, -95.7128910), //Center of the United States
				mapTypeControl    : false,
				mapTypeId         : google.maps.MapTypeId.ROADMAP,
				streetViewControl : false,
				zoom              : 4
			});
			
			directionsRenderer.setMap(map);
			
		//Render the directions from POI A to B
			directionsService.route({
				destination : toPOI,
				origin      : fromPOI,
				travelMode  : google.maps.DirectionsTravelMode.DRIVING
			}, function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsRenderer.setDirections(response);
				} else {
					alert('An error was encountered while creating a map of trip route.\n\nIf this is the first time you have seen this error, wait one minute and try reloading this page before performing another search. If this error continues to occur, contact the site administrator for assistance and include the details listed below.\n\n-----------------------\n\nPage URL:\n' + window.location.href);
				}
			});
		});
	}
})(jQuery)
