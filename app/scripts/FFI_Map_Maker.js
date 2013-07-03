/**
 * Google Maps maker and POI marker
 *
 * This plugin requires the Google Maps JavaScript API v3 to be included
 * in the DOM before this plugin is instantited.
 *
 * This plugin will leverage the Google Maps JavaScript API v3 to create 
 * a Google Maps widget in the targeted object. This plugin has two mapping
 * modes, both of which interpret the given options quite differently.
 *
 * City mode:    In city mode, this plugin will understand that only one 
 *               point is intended to be marked, indicating a city or town. 
 *               The map will center itself at the given latitude and 
 *               longitude, and will place a marker at that point. The default 
 *               zoom level will be quite close. This point will not have a
 *               information balloon.
 *
 * Country mode: In country mode, this plugin will understand that multiple
 *               points will be marked on the map, indicating several cities
 *               or towns. The map will center itself at the given latitude
 *               and longitude, but will make a request to the server for 
 *               the list of points which should be plotted. The plugin will
 *               mark these points using the data recieved from the server
 *               and will give each of these point its own unique information
 *               balloon. The default coordinate points are centered on the 
 *               United States and the zoom level is enough to display the
 *               entire country.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI
 * @since     v1.0 Dev
*/

(function($) {
	$.fn.FFI_Map_Maker = function(options) {
	//Allow the user to override default options
		var opts = $.extend($.fn.FFI_Map_Maker.defaults, options);
		
	//Generate the URL where destination points can be fetched (used only in country mode)
		var infoWindow = new google.maps.InfoWindow;
		var dataURL = document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'wp-content/plugins/travel-assistant/app/includes/ajax/map_points.php';
		
	//Depending on the mode, the zoom will differ
		var zoom;
		
		if ($.fn.FFI_Map_Maker.defaults.mode.toLowerCase() == 'city') {
			zoom = $.fn.FFI_Map_Maker.defaults.cityModeZoom;
		} else {
			zoom = $.fn.FFI_Map_Maker.defaults.countryModeZoom;
		}
		
		return this.each(function() {
		//Google Maps instantiation and configuration
			var map = new google.maps.Map($(this).get(0), {
				center : new google.maps.LatLng($.fn.FFI_Map_Maker.defaults.latitude, $.fn.FFI_Map_Maker.defaults.longitude),
				mapTypeControl : false,
				mapTypeId : google.maps.MapTypeId.TERRAIN,
				scrollwheel : false,
				streetViewControl : false,
				zoom : zoom
			});
			
		//If the map mode is set to city, then the marker will be placed at the latitude and longitude given in the options...
			if ($.fn.FFI_Map_Maker.defaults.mode.toLowerCase() == 'city') {
				var point = new google.maps.LatLng($.fn.FFI_Map_Maker.defaults.latitude, $.fn.FFI_Map_Maker.defaults.longitude);
				var marker = new google.maps.Marker({
					'animation' : google.maps.Animation.DROP,
					'map' : map,
					'position' : point
				});
		//... otherwise fetch the list of coordinates from the server
			} else {
				var documentURL = document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'travel-assistant/';
				
			//Make the AJAX request to the server
				$.ajax({
					'dataType' : 'json',
					'url' : dataURL,
					'type' : 'GET',
					'success' : function(data) {
					//Build each of the markers
						for (var i = 0; i < data.length; ++i) {
						//Clean up the state and city name for URL formatting
							var cleanState = data[i].state.replace(/[^A-Za-z0-9\s]/g, '').replace(/[\s]/g, '-').toLowerCase();
							var cleanCity = data[i].city.replace(/[^A-Za-z0-9\s]/g, '').replace(/[\s]/g, '-').toLowerCase();
							
							var html = '<b>' + data[i].name + '</b><br>';

						//Don't show any information that has "0" rides needed/available
							if (data[i].fromNeeds > 0) {
								html += data[i].fromNeeds + ' ' + (data[i].fromNeeds == 1 ? 'ride' : 'rides') + ' leaving here needed<br>';
							}

							if (data[i].fromShares > 0) {
								html += data[i].fromShares + ' ' + (data[i].fromShares == 1 ? 'ride' : 'rides') + ' leaving here avaliable<br>';
							}

							if (data[i].toNeeds > 0) {
								html += data[i].toNeeds + ' ' + (data[i].toNeeds == 1 ? 'ride' : 'rides') + ' going here needed<br>';
							}

							if (data[i].toShares > 0) {
								html += data[i].toShares + ' ' + (data[i].toShares == 1 ? 'ride' : 'rides') + ' going here avaliable<br>';
							}

							html += '<br><a href=\'' + documentURL + 'browse/' + cleanState + '/' + cleanCity + '\'>Browse Trips</a>';
							
						//Build the marker
							var point = new google.maps.LatLng(parseFloat(data[i].latitude), parseFloat(data[i].longitude));
							var marker = new google.maps.Marker({
								'animation' : google.maps.Animation.DROP,
								'map' : map,
								'position' : point
							});
							
						//Add a custom marker balloon
							$.fn.FFI_Map_Maker.markerClick(map, marker, infoWindow, html)
						}
					}
				});
			}
		});
	}
	
/**
* The plugin settings
*
* @access public
* @since  v1.0 Dev
* @type   object 
*/
	
	$.fn.FFI_Map_Maker.defaults = {
		'cityModeZoom' : 8,
		'countryModeZoom' : 4,
		'latitude' : 37.0902400,
		'longitude' : -95.7128910,
		'mode' : 'country'
	};
	
/**
* Add an information balloon when a marker is clicked
*
* @access public
* @param  Map        map        A reference to the Google Map object
* @param  Marker     marker     A reference to the marker which was clicked
* @param  InfoWindow infoWindow A reference to the balloon which will display the content
* @param  string     html       The content to display inside of the balloon
* @return void
* @since  v1.0 Dev
*/
	
//Callback function when a marker is clicked
	$.fn.FFI_Map_Maker.markerClick = function(map, marker, infoWindow, html) {
		google.maps.event.addListener(marker, 'click', function() {
			infoWindow.setContent(html);
			infoWindow.open(map, marker);
		});
	}
})(jQuery)
