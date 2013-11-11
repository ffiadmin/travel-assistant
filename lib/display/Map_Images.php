<?php
class Map_Images {
	private static $API = "";

	private static function getAPIKey() {
		global $wpdb;
	
		if (self::$API == "") {		
			$APIs = $wpdb->get_results("SELECT `GoogleMaps` FROM `ffi_ta_apis`");
			self::$API = $APIs[0]->GoogleMaps;
		}
	}
	
	public static function myTripsPreviewSmall($fromLatitude, $fromLongitude, $toLatitude, $toLongitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?size=100x100&markers=color:green%7Clabel:A%7C" . $fromLatitude . "," . $fromLongitude . "&markers=color:red%7Clabel:B%7C" . $toLatitude . "," . $toLongitude . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}

	public static function myTripsPreviewLarge($fromLatitude, $fromLongitude, $toLatitude, $toLongitude) {
		self::getAPIKey();
	
		return "//maps.googleapis.com/maps/api/staticmap?size=180x180&markers=color:green%7Clabel:A%7C" . $fromLatitude . "," . $fromLongitude . "&markers=color:red%7Clabel:B%7C" . $toLatitude . "," . $toLongitude . "&key=" . self::$API . "&sensor=false&visual_refresh=true&style=feature:road|color:0xFFFFFF&style=feature:road.arterial|color:0xF1C40F&style=feature:road.highway|color:0xF1C40F&style=feature:landscape|color:0xECF0F1&style=feature:water|color:0x73BFC1&style=feature:road|element:labels|visibility:off&style=feature:poi.park|element:geometry.fill|color:0x2ECC71&style=feature:landscape.man_made|element:geometry|visibility:off";
	}
}
?>