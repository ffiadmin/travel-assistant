/**
 * Google Maps Maker and POI Marker Plugin
 *
 * This plugin requires the Google Maps JavaScript API v3 to
 * be included in the DOM before this plugin is instantited.
 *
 * This plugin will leverage the Google Maps JavaScript API
 * v3 to create a Google Maps widget in the targeted object.
 * 
 * It will understand that multiple points will be marked on
 * the map, indicating several cities or towns. The map will
 * center itself at the given latitude and longitude, but will
 * make a request to the server for the list of points which 
 * should be plotted. The plugin will mark these points using
 * the data recieved from the server and will give each of
 * these points its own unique information balloon. The default
 * coordinate points are centered on the United States and the 
 * zoom level is enough to display the entire country.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI_TA
 * @since     1.0.0
*/

(function($) {
	$.fn.FFI_TA_Map_Maker = function(options) {
	//Allow the user to override default options
		$.extend($.fn.FFI_TA_Map_Maker.defaults, options);
		
	//Generate the URL where destination points can be fetched
		var infoWindow = new google.maps.InfoWindow;
		
	//Set the zoom
		var zoom = $.fn.FFI_TA_Map_Maker.defaults.zoom;
		
		return this.each(function() {
        //Create map styles
		    var styles = [
                {
                    'featureType'    : 'road',
                    'stylers'        : [{
                        'color'      : '#FFFFFF'
                    }]
                }, {
                    'featureType'    : 'road.arterial',
                    'stylers'        : [{
                        'color'      : '#F1C40F'
                    }]
                }, {
                    'featureType'    : 'road.highway',
                    'stylers'        : [{
                        'color'      : '#F1C40F'
                    }]
                }, {
                    'featureType'    : 'landscape',
                    'stylers'        : [{
                        'color'      : '#ECF0F1'
                    }]
                }, {
                    'featureType'    : 'water',
                    'stylers'        : [{
                        'color'      : '#73BFC1'
                    }]
                }, {
                    'featureType'    : 'road',
                    'elementType'    : 'labels',
                    'stylers'        : [{
                        'visibility' : 'off'
                    }]
                }, {
                    'featureType'    : 'poi.park',
                    'elementType'    : 'geometry.fill',
                    'stylers'        : [{
                        'color'      : '#2ECC71'
                    }]
                }, {
                    'featureType'    : 'landscape.man_made',
                    'elementType'    : 'geometry',
                    'stylers'        : [{
                        'visibility' : 'off'
                    }]
                }
		    ];

		    var styledMap = new google.maps.StyledMapType(styles, {
		        'name' : 'Splash Map'
		    });

		//Google Maps instantiation and configuration
			var bounds = new google.maps.LatLngBounds();
			var map = new google.maps.Map($(this).get(0), {
				center            : new google.maps.LatLng($.fn.FFI_TA_Map_Maker.defaults.latitude, $.fn.FFI_TA_Map_Maker.defaults.longitude),
				mapTypeControl    : false,
				mapTypeId         : google.maps.MapTypeId.ROADMAP,
				scrollwheel       : false,
				streetViewControl : false,
				zoom              : $.fn.FFI_TA_Map_Maker.defaults.zoom
			});

			map.mapTypes.set('map_style', styledMap);
			map.setMapTypeId('map_style');
			
		//Generate the base URL for links which will be used in the balloons
			var documentURL = document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'travel-assistant/';
		
		//Make the AJAX request to the server
			$.ajax({
				dataType : 'json',
				url      : $.fn.FFI_TA_Map_Maker.defaults.processURL,
				type     : 'GET',
				success  : function(data) {
					var showLink = false;

				//Build each of the markers
					for (var i = 0; i < data.length; ++i) {
						showLink = false
						var html = '<b>' + data[i].name + '</b><br>';

					//Don't show any information that has "0" rides needed/available
						if (data[i].fromNeeds > 0) {
							showLink = true;

							html += data[i].fromNeeds + ' ' + (data[i].fromNeeds == 1 ? 'ride' : 'rides') + ' leaving here needed<br>';
						}

						if (data[i].fromShares > 0) {
							showLink = true;

							html += data[i].fromShares + ' ' + (data[i].fromShares == 1 ? 'ride' : 'rides') + ' leaving here avaliable<br>';
						}

						if (data[i].toNeeds > 0) {
							html += data[i].toNeeds + ' ' + (data[i].toNeeds == 1 ? 'ride' : 'rides') + ' going here needed<br>';
						}

						if (data[i].toShares > 0) {
							html += data[i].toShares + ' ' + (data[i].toShares == 1 ? 'ride' : 'rides') + ' going here avaliable<br>';
						}

					//Determine whether to show a link to this city
						var cleanState = data[i].state.replace(/[^A-Za-z0-9\s]/g, '').replace(/[\s]/g, '-').toLowerCase();
						var cleanCity = data[i].city.replace(/[^A-Za-z0-9\s]/g, '').replace(/[\s]/g, '-').toLowerCase();

						if (showLink) {
							html += '<br><a href=\'' + documentURL + 'browse/' + cleanState + '/' + cleanCity + '/\'>Browse Trips</a>';
						}
							
					//Build the marker
						var point = new google.maps.LatLng(parseFloat(data[i].latitude), parseFloat(data[i].longitude));
						var marker;
						bounds.extend(point);
							
					//Make a custom marker icon for the origin location
						if (cleanState == $.fn.FFI_TA_Map_Maker.defaults.originState.toLowerCase() && 
							cleanCity  == $.fn.FFI_TA_Map_Maker.defaults.originCity.toLowerCase()) {
							marker = new google.maps.Marker({
								animation : google.maps.Animation.DROP,
								map       : map,
								icon      : $.fn.FFI_TA_Map_Maker.defaults.originIcon,
								position  : point
							});
						} else {
							marker = new google.maps.Marker({
								animation : google.maps.Animation.DROP,
								map       : map,
								position  : point
							});
						}
							
					//Add a custom marker balloon
						$.fn.FFI_TA_Map_Maker.markerClick(map, marker, infoWindow, html)
					}

				//Zoom into the plotted points, if any were added
					if (data.length > 0) {
						map.fitBounds(bounds);
						map.panToBounds(bounds);
					}
				}
			});
		});
	}
	
/**
 * Add an information balloon when a marker is clicked.
 *
 * @access public
 * @param  Map        map        A reference to the Google Map object
 * @param  Marker     marker     A reference to the marker which was clicked
 * @param  InfoWindow infoWindow A reference to the balloon which will display the content
 * @param  string     html       The content to display inside of the balloon
 * @return void
 * @since  1.0.0
*/
	
//Callback function when a marker is clicked
	$.fn.FFI_TA_Map_Maker.markerClick = function(map, marker, infoWindow, html) {
		google.maps.event.addListener(marker, 'click', function() {
			infoWindow.setContent(html);
			infoWindow.open(map, marker);
		});
	}

/**
 * The plugin settings.
 *
 * @access public
 * @since  1.0.0
 * @type   object<mixed>
*/
	
	$.fn.FFI_TA_Map_Maker.defaults = {
		'latitude'    : 38.3,
		'longitude'   : -95.7,
		'originCity'  : 'grove-city',
		'originIcon'  : '//mt.google.com/vt/icon?name=icons/spotlight/university_search_v_L_8x.png&scale=1.5',
		'originState' : 'pennsylvania',
		'processURL'  : document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'wp-content/plugins/travel-assistant/app/system/ajax/map_points.php',
		'zoom'        : 4
	};
})(jQuery);