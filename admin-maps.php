<?php
/**
 * @package wp-runequesters
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

include_once( WPRQ_PATH . 'classes/functions.php' );

$show_list_maps = true;
$msg = array();

## ADD MAP

if ( isset($_GET["action"]) AND ($_GET["action"] == 'add') AND current_user_can( 'manage_options' ) ) {

	global $wpdb;

	$show_list_maps = false;

	include_once( WPRQ_PATH . 'classes/zebra-form/Zebra_Form.php' );

	$form = new Zebra_Form('form-add-map');

	## Title

	$form->add('label', 'label_title', 'title', __( 'Title' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('text', 'title');

	$obj->set_rule(array(
		'required' => array(
			'error', 
			sprintf(
				__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
				__( 'Title' , WPRQ_TEXTDOMAIN ) 
			),
		),
		'length' => array(
			1, 
			50, 
			'error', 
			sprintf(
				__( 'The %s must have between %s and %s characters!' , WPRQ_TEXTDOMAIN ),
				__( 'Title' , WPRQ_TEXTDOMAIN ),
				1,
				50
			),
		),
	));

	## Description

	$form->add('label', 'label_description', 'description', __( 'Description' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('textarea', 'description');

	## Dirs with maps

	$elements_maps = scandir(  WPRQ_UPLOADS_DIR . 'maps/' );

	$form->add('label', 'label_folder_map', 'folder_map', __( 'Folder Map' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('select', 'folder_map');

	$table_maps = $wpdb->prefix . WPRQ_NAME . '_maps';

	$maps = $wpdb->get_results( "SELECT * FROM " . $table_maps . "" );

	$folders_in_use = array();

	foreach ($maps as $key => $map) {
		$folders_in_use[] = $map->map_dir;
	}

	$folders = array();

	foreach ($elements_maps as $key => $element) {

		if ( is_dir( WPRQ_UPLOADS_DIR . 'maps/' . $element ) 
			AND $element != '.'
			AND $element != '..' 
			AND !( in_array($element, $folders_in_use) )
		) {
			$folders[$element] = $element;
		}
	}

	$obj->add_options($folders);

	## Submit

	$form->add('submit', 'btnsubmit', __( 'Add New Map', WPRQ_TEXTDOMAIN ), array(
		'class' => 'button button-primary',

	));

	## Validate the form

	if ($form->validate()) {
		global $wpdb;

		$table = $wpdb->prefix . WPRQ_NAME . '_maps';

		$data["map_author"] = get_current_user_id();
		$format[] = '%d';

		$data["map_dir"] = $_POST["folder_map"];
		$format[] = '%s';

		$data["map_title"] = $_POST["title"];
		$format[] = '%s';

		$data["map_description"] = $_POST["description"];
		$format[] = '%s';

		$data["map_status"] = 'publish';
		$format[] = '%s';

		$data["map_date_created"] = date("Y-m-d H:i:s");
		$format[] = '%s';

		$insert_map = $wpdb->insert( $table, $data, $format );

		if ( $insert_map === false ) {
			$form->add_error('error', __( "<strong>Oh snap!</strong> We couldn't insert map in database.", WPRQ_TEXTDOMAIN) );
		} else {
			$show_list_maps = true;
			$msg["type"]	= 'success';
			$msg["message"] = __( "<strong>Well done!</strong> The map was saved correctly.", WPRQ_TEXTDOMAIN );
		}

	}

	if ( $show_list_maps == false ) {

		?>

		<div class="wrap">
		
			<div class="icon32 icon32-main"><br></div>
			
			<h2>
				<?php echo sprintf( '%s %s', __( 'Add New' , WPRQ_TEXTDOMAIN ), __( 'Map' , WPRQ_TEXTDOMAIN ) ); ?>
				<?php echo '<p>' . __( 'Create a new map.' , WPRQ_TEXTDOMAIN ) . '</p>'; ?>
			</h2>
			
			<?php $form->render( WPRQ_PATH . 'tpls/form-add-map.php' ); ?>

		</div>

		<?php

	}

}

## EDIT MAP

if ( isset($_GET["action"]) AND ($_GET["action"] == 'edit') AND is_numeric($_GET["map"]) ) {

	$show_list_maps = false;

	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_maps';

	## select row
	
	$map = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE map_id = '%d'",
			$_GET["map"]
		)
	);

	if ( $map === null ) {

		$show_list_maps = true;
		$msg["type"]	= 'error';
		$msg["message"] = sprintf(
			__( "<strong>Oh snap!</strong> The map with ID = %d wasn't in database.", WPRQ_TEXTDOMAIN ),
			$_GET["map"]
		);

	} else {

		include_once( WPRQ_PATH . 'classes/zebra-form/Zebra_Form.php' );

		$form = new Zebra_Form('form-edit-map');

		## Title

		$form->add('label', 'label_title', 'title', __( 'Title' , WPRQ_TEXTDOMAIN) );

		$obj = $form->add('text', 'title', $map->map_title);

		$obj->set_rule(array(
			'required' => array(
				'error', 
				sprintf(
					__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
					__( 'Title' , WPRQ_TEXTDOMAIN ) 
				),
			),
			'length' => array(
				1, 
				50, 
				'error', 
				sprintf(
					__( 'The %s must have between %s and %s characters!' , WPRQ_TEXTDOMAIN ),
					__( 'Title' , WPRQ_TEXTDOMAIN ),
					1,
					50
				),
			),
		));

		## Description

		$form->add('label', 'label_description', 'description', __( 'Description' , WPRQ_TEXTDOMAIN) );

		$obj = $form->add('textarea', 'description', $map->map_description);

		## Submit

		$form->add('submit', 'btnsubmit', __( 'Edit Map', WPRQ_TEXTDOMAIN ), array(
			'class' => 'button button-primary',

		));

		## Validate the form

		if ($form->validate()) {
			global $wpdb;

			$table = $wpdb->prefix . WPRQ_NAME . '_maps';

			$data["map_title"] = $_POST["title"];
			$format[] = '%s';

			$data["map_description"] = $_POST["description"];
			$format[] = '%s';

			$data["map_status"] = 'publish';
			$format[] = '%s';

			$where["map_id"] = $_GET["map"];
			$where_format [] = '%d';

			$insert_map = $wpdb->update( $table, $data, $where, $format, $where_format  );

			if ( $insert_map === false ) {
				$form->add_error('error', __( "<strong>Oh snap!</strong> We couldn't update map in database.", WPRQ_TEXTDOMAIN) );
			} else {
				$show_list_maps = true;
				$msg["type"]	= 'success';
				$msg["message"] = __( "<strong>Well done!</strong> The map was updated correctly.", WPRQ_TEXTDOMAIN );
			}

		}

		if ( $show_list_maps == false ) {

			?>

			<div class="wrap">
			
				<div class="icon32 icon32-main"><br></div>
				
				<h2>
					<?php echo sprintf( '%s %s', __( 'Edit' , WPRQ_TEXTDOMAIN ), __( 'Map' , WPRQ_TEXTDOMAIN ) ); ?>
					<?php echo '<p>' . __( 'Modify a map.' , WPRQ_TEXTDOMAIN ) . '</p>'; ?>
				</h2>
				
				<?php

				$form->render( WPRQ_PATH . 'tpls/form-edit-map.php', false, array(
					'map' => $map
				) );

				?>

			</div>

			<?php

		}
	}

}

## POINTS MAP

if ( isset($_GET["action"]) AND ($_GET["action"] == 'points') AND is_numeric($_GET["map"]) ) {

	$show_list_maps = false;

	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_maps';

	## select row
	
	$map = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE map_id = '%d'",
			$_GET["map"]
		)
	);

	if ( $map === null ) {

		$show_list_maps = true;
		$msg["type"]	= 'error';
		$msg["message"] = sprintf(
			__( "<strong>Oh snap!</strong> The map with ID = %d wasn't in database.", WPRQ_TEXTDOMAIN ),
			$_GET["map"]
		);

	} else {

		?>

		<div class="wrap">
			
			<div class="icon32 icon32-main"><br></div>
			
			<h2>
				<?php echo sprintf( '%s %s', __( 'Points' , WPRQ_TEXTDOMAIN ), __( 'Map' , WPRQ_TEXTDOMAIN ) ); ?>
				<?php echo '<p>' . sprintf( __( 'Manage Points from map %s.' , WPRQ_TEXTDOMAIN ), $map->map_title ) . '</p>'; ?>
			</h2>
				
			<?php

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
					$_GET["map"]
				)
			);


			?>

			<script type="text/javascript">
			
			// <![CDATA[

				// Function: get VARs from URL
				function get_url_vars() {
					var vars = [], hash;
					var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
					for(var i = 0; i < hashes.length; i++) {
						hash = hashes[i].split('=');
						vars.push(hash[0]);
						vars[hash[0]] = hash[1];
					}
					return vars;
				}

				// Vars
				var CustomMap = CustomMap || {};
				CustomMap.ImagesBaseUrl = '<?php echo WPRQ_URL_UPLOADS_DIR . "maps/"; ?>';
				var map;
				var map_id = get_url_vars()["map"];
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
						icon: 'http://www.google.com/mapfiles/markerA.png',
						title: point["title"],
						draggable: true,
						html: '<div class=\"info-bubble\" id="point-' + point["id"] + '">' + point["html"] + '</div>'
					});;

					// Create a infowindow
					var contentString = "";
					var infowindow = new google.maps.InfoWindow({
						content: contentString
					});

					// Event (click): Open infowindow
					google.maps.event.addListener(markers[point_id], 'click', function () {
						// close any open infowindows
						infowindow.close();

						infowindow.setContent(this.html);
						infowindow.open(this.map, this);
						//console.log(this);
					});

					// Event (dragend): Update latLon of marker
					google.maps.event.addListener(markers[point_id], 'dragend', function(a) {
						var parm = {};
						parm["action"]		= "wprq-update_point_map";
						parm["id"] 			= point_id;
						parm["latitude"] 	= a.latLng.d;
						parm["longitude"] 	= a.latLng.e;

						jQuery.ajax({
							type: "POST",
							url: '<?php echo admin_url("admin-ajax.php"); ?>', 
							data: parm,
							dataType: "json",
							success: function(response){
								console.log(response);
								alert( response.request.message );
							},
							error: function(MLHttpRequest, textStatus, errorThrown){  
								alert("There was an error: " + errorThrown);
							}
						});
					});
				};

				function update_point() {
					// Disabled button "save"
					jQuery("#form-add-point-map #btn-update-point").attr("disabled", true);

					var parm = {};
					parm["action"]		= "wprq-update_point_map";
					parm["id"]			= jQuery("#form-add-point-map #id").val();
					parm["title"] 		= jQuery("#form-add-point-map #title").val();
					parm["description"] = jQuery("#form-add-point-map #description").val();
					parm["url"] 		= jQuery("#form-add-point-map #url").val();
					parm["icon"] 		= jQuery("#form-add-point-map #icon").val();

					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url("admin-ajax.php"); ?>', 
						data: parm,
						dataType: "json",
						success: function(response){
							//console.log(response);
							alert( response.request.message );
							if ( response.request.error == false ) {
								create_marker_of_point( response.data );
							}
						},
						error: function(MLHttpRequest, textStatus, errorThrown){  
							alert("There was an error: " + errorThrown);
						}
					});

					// Enabled button "save"
					jQuery("#form-add-point-map #btn-update-point").attr("disabled", false);
				};

				function delete_point(point_id) {
					var parm = {};
					parm["action"]		= "wprq-delete_point_map";
					parm["id"]			= point_id;

					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url("admin-ajax.php"); ?>', 
						data: parm,
						dataType: "json",
						success: function(response){
							//console.log(response);
							alert( response.request.message );
							if (markers[point_id] != undefined) {
								markers[point_id].setMap(null); // delete old marker
							}
						},
						error: function(MLHttpRequest, textStatus, errorThrown){  
							alert("There was an error: " + errorThrown);
						}
					});
				};

				function insert_point(lat, lon) {
					var parm = {};
					parm["action"]		= "wprq-insert_point_map";
					parm["map"]			= '<?php echo $_GET["map"]; ?>';
					parm["latitude"]	= lat;
					parm["longitude"]	= lon;
					parm["author"] 		= '<?php echo get_current_user_id(); ?>';

					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url("admin-ajax.php"); ?>', 
						data: parm,
						dataType: "json",
						success: function(response){
							console.log(response);
							alert( response.request.message );
							if ( response.request.error == false ) {
								create_marker_of_point( response.data );
							}
						},
						error: function(MLHttpRequest, textStatus, errorThrown){  
							alert("There was an error: " + errorThrown);
						}
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
					this._map.mapTypes.set('<?php echo $map->map_dir; ?>', new CustomMap.ImgMapType('<?php echo $map->map_dir; ?>', '#FFFFFF'));
					this._map.setMapTypeId('<?php echo $map->map_dir; ?>');

					// Listen to click on map
					google.maps.event.addListener(this._map, 'click', function(event){
						//console.log(event.latLng);

						if ( jQuery('#allow-add-points').prop('checked') == true ) {
							insert_point(event.latLng.d, event.latLng.e);
							// put lat and long to input
							//document.getElementById('coor_lat').value = event.latLng.d;
							//document.getElementById('coor_lon').value = event.latLng.e;
						}
					});

					// Get points and show markrs in map
					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url("admin-ajax.php"); ?>', 
						data: { action: "wprq-get_all_points_map", map: map_id },
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
				CustomMap.ImgMapType.prototype.maxZoom = <?php echo $max_zoom_map; ?>;

				CustomMap.ImgMapType.prototype.getTile = function (coord, zoom, ownerDocument) {

					var tilesCount = Math.pow(2, zoom);

					if (coord.x >= tilesCount || coord.x < 0 || coord.y >= tilesCount || coord.y < 0) {

						var div = ownerDocument.createElement('div');
						div.style.width = this.tileSize.width + 'px';
						div.style.height = this.tileSize.height + 'px';
						div.style.backgroundColor = this._backgroundColor;
						return div;
					}

					var img = ownerDocument.createElement('IMG');
					img.width = this.tileSize.width;
					img.height = this.tileSize.height;
					img.src = CustomMap.Utils.GetImageUrl(this._theme + '/tile_' + zoom + '_' + coord.x + '-' + coord.y + '.png');

					return img;

				};

				/* Other */
				CustomMap.Utils = CustomMap.Utils || {};

				CustomMap.Utils.GetImageUrl = function (image) {
					return CustomMap.ImagesBaseUrl + image;
				};

				CustomMap.Utils.SetOpacity = function (obj, opacity /* 0 to 100 */ ) {
					obj.style.opacity = opacity / 100;
					obj.style.filter = 'alpha(opacity=' + opacity + ')';
				};

				/* Map creation */
				google.maps.event.addDomListener(window, 'load', function () {
					var map = new CustomMap.CanvasMap(document.getElementById('canvas-map'));
				});

			// ]]>

			</script>
			<p>To add points, you must select this checkbox <input type="checkbox" id="allow-add-points" name="allow-add-points" value="true"> and you can add items by clicking the map.</p>
			<div id="canvas-map" style="display: inline-block;width:100%;height:500px;border-color: #ddd;box-shadow: inset 0 1px 2px rgba(0,0,0,.07);"></div>

		<?php

	}

}

## DELETE MAP

if ( isset($_GET["action"]) AND ($_GET["action"] == 'delete') AND is_numeric($_GET["map"]) ) {
	
	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_maps';

	## select row
	
	$map_row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE map_id = '%d'",
			$_GET["map"]
		)
	);

	if ( $map_row === null ) {

		$show_list_maps = true;
		$msg["type"]	= 'error';
		$msg["message"] = sprintf(
			__( "<strong>Oh snap!</strong> The map with ID = %d wasn't in database.", WPRQ_TEXTDOMAIN ),
			$_GET["map"]
		);

	} else {

		// delete row

		$where["map_id"] = $_GET["map"];
		$where_format = '%d';

		$delete_map = $wpdb->delete( $table, $where, $where_format );

		if ( $delete_map == false ) {

			$show_list_maps = true;
			$msg["type"]	= 'error';
			$msg["message"] = __( "<strong>Oh snap!</strong> The map wasn't deleted.", WPRQ_TEXTDOMAIN );

		} else {

			$show_list_maps = true;
			$msg["type"]	= 'success';
			$msg["message"] = __( '<strong>Well done!</strong> The map was deleted correctly.', WPRQ_TEXTDOMAIN );

			wprq_deleter_dir( WPRQ_UPLOADS_DIR . 'maps/' . $map_row->map_dir . '/');

			$wpdb->query( "DELETE FROM " . $wpdb->prefix . WPRQ_NAME . '_maps_points' . " WHERE point_map NOT IN ( SELECT m.map_id FROM " . $table . " as m )" );

		}

	}

	$show_list_maps = true;

}

## SHOW LIST MAP

if ( $show_list_maps == true ) {

	include_once( plugin_dir_path(__FILE__) . 'classes/WP_List_Table_Maps.php' );

	// Prepare Table of elements
	$list_table_maps = new WP_List_Table_Maps();
	$list_table_maps->prepare_items();

	if ( current_user_can( 'manage_options' ) )
		$button_add_map = sprintf('<a href="?page=%s&action=%s" class="add-new-h2">%s</a>',
			$_REQUEST['page'],
			'add',
			__( 'Add New' , WPRQ_TEXTDOMAIN )
		);

	?>

	<div class="wrap">
		
		<div class="icon32 icon32-main"><br></div>
		
		<h2>
			<?php _e( 'Maps', WPRQ_TEXTDOMAIN ); ?>
			<?php echo $button_add_map; ?>
		</h2>

		<?php

		if ( !empty($msg) )
			message_response($msg["type"], $msg["message"]);

		$list_table_maps->display();

		?>

	</div>

	<?php

}

?>