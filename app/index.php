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
		
		var points = new google.maps.FusionTablesLayer({
		  query: {
            select: '\'Location\'',
            from: '1e0wPmtYawLjFAoV6BipQxCyAO9IkbkXuzifuats'
          }
		});
		
		points.setMap(map);
	  });
    </script>";

	echo "<div id=\"map_canvas\" style=\"width:100%; height:480px\"></div>";
?>