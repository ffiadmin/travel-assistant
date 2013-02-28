<?php
	$essentials->setTitle("Travel Assistant");
	$essentials->includeJS("//maps.googleapis.com/maps/api/js?key=AIzaSyCCfXsNv47Xg62-Kz6opyvmn3YBPhliZ0k&sensor=false");
	
	 echo "<script>
	  $(document).ready(function() {
		var map = new google.maps.Map(document.getElementById('map_canvas'), {
          center : new google.maps.LatLng(37.0902400, -95.7128910), //Center of United States
		  mapTypeControl : false,
		  mapTypeId : google.maps.MapTypeId.TERRAIN,
		  panControl : false,
		  streetViewControl : false,
          zoom : 4,                                                 //Zoom out to see all of the United States
		  zoomControl : false
        });
		
		var infoWindow = new google.maps.InfoWindow;
		var documentURL = document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'travel-assistant/';
		var dataURL = document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'wp-content/plugins/travel-assistant/app/includes/ajax/map_points.php';
		
		$.ajax({
			'dataType' : 'xml',
			'url' : dataURL,
			'type' : 'GET',
			'success' : function(data) {
				var markers = $(data).find('marker');
				
				for (var i = 0; i < markers.length; ++i) {
					var html = '<b>' + markers.eq(i).attr('name') + '</b><br><br><a href=\'' + documentURL + markers.eq(i).attr('state').toLowerCase() + '/' + markers.eq(i).attr('city').toLowerCase() + '\'>Avaliable Trips</a>';
					var point = new google.maps.LatLng(parseFloat(markers.eq(i).attr('lat')), parseFloat(markers.eq(i).attr('lng')));
					var marker = new google.maps.Marker({
						'animation' : google.maps.Animation.DROP,
						'map' : map,
						'position' : point
					});
					
					markerClick(marker, html)
				}
			}
		});
		
		function markerClick(marker, html) {
			google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
			});
		}
	  });
    </script>";

	echo "<div id=\"map_canvas\" style=\"width:100%; height:480px\"></div>";
?>