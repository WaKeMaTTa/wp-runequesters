<?php
/**
 * @package wp-runequesters
 */

add_shortcode("wprq_map", function($atts) {

	wp_register_script( 'google-maps.js', 'https://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=false', array(), '3.x.x', false);
	wp_enqueue_script( 'google-maps.js' );

	$atts = shortcode_atts(
		array(
			'id' => false,
			'height' => '500px',
			'width' => '100%',
			'bgcolor' => '#ffffff'
		),
		$atts
	);

	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_maps';

	$map = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE map_id = '%d'",
			$atts["id"]
		)
	);

	## Data Map: max zoom

	$tiles = scandir( WPRQ_UPLOADS_DIR . 'maps/' . $map->map_dir . '/' );

	$keys_needle = array_search('.', $tiles);
	unset($tiles[$keys_needle]);
	
	$keys_needle = array_search('..', $tiles);
	unset($tiles[$keys_needle]);

	$total_tiles = count($tiles);

	switch ($total_tiles) {
		case 1365:
			$max_zoom_map = 5;
			break;
		case 341:
			$max_zoom_map = 4;
			break;
		case 85:
			$max_zoom_map = 3;
			break;
		case 21:
			$max_zoom_map = 2;
			break;
		case 5:
			$max_zoom_map = 1;
			break;
		default:
			$max_zoom_map = 0;
			break;
	}

	## Data Map: Points

	$table_points = $wpdb->prefix . WPRQ_NAME . '_maps_points';

	$points = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM " . $table_points . " WHERE point_map = '%d'",
			$atts["id"]
		)
	);

	$html = '
	<style type="text/css">
		#canvas-map img {
			max-width: inherit !important;
		}
	</style>

	<script type="text/javascript">
			
	// <![CDATA[

	jQuery(document).ready(function($){

		// Function: get VARs from URL
		function get_url_vars() {
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf("?") + 1).split("&");
			for(var i = 0; i < hashes.length; i++) {
				hash = hashes[i].split("=");
				vars.push(hash[0]);
				vars[hash[0]] = hash[1];
			}
			return vars;
		}

		// Vars
		var CustomMap = CustomMap || {};
		CustomMap.ImagesBaseUrl = "' . WPRQ_URL_UPLOADS_DIR . "maps/" . '";
		var map;
		var map_id = ' . $atts["id"] . ';
		var markers = new Array();

		// Function for create Marker of 1 Point
		function create_marker_of_point(point) {
			point_id = point["id"];
			
			if (markers[point_id] != undefined) {
				markers[point_id].setMap(null); // delete old marker
			}

			markers[point_id] = new google.maps.Marker({
				position: new google.maps.LatLng( point["latitude"] , point["longitude"] ),
				map: map,
				animation: google.maps.Animation.DROP,
				icon: point["icon_url"],
				title: point["title"],
				draggable: false,
				html: "<div class=\"info-bubble\" id=\"point-" + point["id"] + "\">" + point["html"] + "</div>"
			});

			// Create a infowindow
			var contentString = "";
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});

			// Event (click): Open infowindow
			google.maps.event.addListener(markers[point_id], "click", function () {
				// close any open infowindows
				infowindow.close();

				infowindow.setContent(this.html);
				infowindow.open(this.map, this);
				//console.log(this);
			});
		};

		/* CanvasMap class */
		CustomMap.CanvasMap = function (container) {

			// Create map
			this._map = new google.maps.Map(container, {
				zoom: 1,
				center: new google.maps.LatLng(0, 0),
				mapTypeControl: false,
				streetViewControl: false,
				disableDefaultUI: false
			});

			map = this._map;

			// Set custom tiles
			this._map.mapTypes.set("' . $map->map_dir . '", new CustomMap.ImgMapType("' . $map->map_dir . '", "' . $atts["bgcolor"] . '"));
			this._map.setMapTypeId("' . $map->map_dir . '");

			// Get points and show markrs in map
			jQuery.ajax({
				type: "POST",
				url: "' . admin_url("admin-ajax.php") . '", 
				data: { action: "wprq-get_all_points_map", map: map_id, not_form: true },
				dataType: "json",
				success: function(response){ 
					var points = response.data;
					for (var i in points ) {
						create_marker_of_point(points[i]);
					}

				},
				error: function(MLHttpRequest, textStatus, errorThrown){  
					alert("There was an error: " + errorThrown);
				},
				timeout: 60000
			});

		};

		/* ImgMapType class */
		CustomMap.ImgMapType = function (theme, backgroundColor) {
			this.name = this._theme = theme;
			this._backgroundColor = backgroundColor;
		};

		CustomMap.ImgMapType.prototype.tileSize = new google.maps.Size(256, 256);
		CustomMap.ImgMapType.prototype.minZoom = 0;
		CustomMap.ImgMapType.prototype.maxZoom = ' . $max_zoom_map . ';

		CustomMap.ImgMapType.prototype.getTile = function (coord, zoom, ownerDocument) {

			var tilesCount = Math.pow(2, zoom);

			if (coord.x >= tilesCount || coord.x < 0 || coord.y >= tilesCount || coord.y < 0) {

				var div = ownerDocument.createElement("div");
				div.style.width = this.tileSize.width + "px";
				div.style.height = this.tileSize.height + "px";
				div.style.backgroundColor = this._backgroundColor;
				return div;
			}

			var img = ownerDocument.createElement("IMG");
			img.width = this.tileSize.width;
			img.height = this.tileSize.height;
			img.src = CustomMap.Utils.GetImageUrl(this._theme + "/tile_" + zoom + "_" + coord.x + "-" + coord.y + ".png");

			return img;

		};

		/* Other */
		CustomMap.Utils = CustomMap.Utils || {};

		CustomMap.Utils.GetImageUrl = function (image) {
			return CustomMap.ImagesBaseUrl + image;
		};

		CustomMap.Utils.SetOpacity = function (obj, opacity /* 0 to 100 */ ) {
			obj.style.opacity = opacity / 100;
			obj.style.filter = "alpha(opacity=" + opacity + ")";
		};

		/* Map creation */
		google.maps.event.addDomListener(window, "load", function () {
			var map = new CustomMap.CanvasMap(document.getElementById("canvas-map"));
		});

	});

	// ]]>

	</script>

	<div id="canvas-map" style="display: inline-block;width: ' . $atts["width"] . '; height: ' . $atts["height"] . ';"></div>';

	return $html;

});