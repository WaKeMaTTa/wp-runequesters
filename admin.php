<?php
/**
 * @package wp-runequesters
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Action create admin menu of the plugin
add_action( 'admin_menu', 'wprq_admin_menu' );

// Show warnings messages
wprq_admin_warnings();

// Function prepare warnings messages panel control wordpress
function wprq_admin_warnings() {
	
	global $pagenow;
	
	if ( $pagenow == 'plugins.php' && isset($_GET["page"]) == 'wprq' ) {
		
		if ( get_option( 'wprq_alert_code' ) ) {
			function wpinimat_alert() {
				$alert = array(
					'code'	=> (int) get_option( 'wprq_alert_code' ),
					'msg'	=> get_option( 'wprq_alert_msg' )
				);
			?>
				<div class="error">
					<p><strong><?php _e( 'WP Runequesters', WPRQ_TEXTDOMAIN ); _e( 'Error Code', WPRQ_TEXTDOMAIN ); echo ': ' . $alert["code"]; ?></strong></p>
					<p><?php esc_html_e( $alert["msg"], WPRQ_TEXTDOMAIN ); ?></p>
					<p><a href="https://github.com/WaKeMaTTa/wp-runequesters/issues" target="_blank"><?php _e( 'Report this error here.', WPRQ_TEXTDOMAIN ); ?></a></p>
				</div>
			<?php
			}

			add_action( 'admin_notices', 'wprq_alert' );
		}
	}
}

// Function check the version of wordpress and the plugin if all well, the plugin is ON.
function wprq_admin_init() {
	global $wp_version;
	
	// all admin functions are disabled in old versions
	if ( !function_exists('is_multisite') && version_compare( $wp_version, '3.0', '>=' ) ) {
		
		function wprq_version_warning() {
			?>
			<div id="wp-runequesters-warning" class="updated fade">
				<p>
					<strong>
					<?php
						sprintf(
							__( '%s %s requires WordPress 3.0 or higher.', WPRQ_TEXTDOMAIN ), 
							__( 'WP Runequesters', WPRQ_TEXTDOMAIN ), 
							WPRQ_VERSION 
						);
					?>
					</strong> 
					<?php 
						sprintf( 
							__( 'Please <a href="%s">upgrade WordPress</a> to a current version.', WPRQ_TEXTDOMAIN ), 
							'http://codex.wordpress.org/Upgrading_WordPress'
						);
					?>
				</p>
			</div>
			<?php
		}
		
		add_action( 'admin_notices', 'wprq_version_warning' );

		return;
	}
}

// Activate the plugin configuration
add_action('admin_init', 'wprq_admin_init');


// Function create the admin menu
function wprq_admin_menu() {
	
	$page["main"] = add_menu_page(
		__( 'WP RuneQuesters', WPRQ_TEXTDOMAIN ),		# page_title
		__( 'WP RuneQuesters', WPRQ_TEXTDOMAIN ),		# menu_title
		'read',											# capability
		'wprq',											# menu_slug
		'wprq_index',									# function (optional)
		WPRQ_URL . 'assets/img/icon-logo-16.png'		# icon_url (optional)
	);

	$page["mycharacters"] = add_submenu_page(
		'wprq',											# parent_slug
		__( 'My characters', WPRQ_TEXTDOMAIN ),			# page_title
		__( 'My characters', WPRQ_TEXTDOMAIN ),			# menu_title
		'read',											# capability
		'wprq/mycharacters',							# menu_slug
		'wprq_mycharacters'								# function (optional)
	);

	$page["maps"] = add_submenu_page(
		'wprq',											# parent_slug
		__( 'Maps', WPRQ_TEXTDOMAIN ),					# page_title
		__( 'Maps', WPRQ_TEXTDOMAIN ),					# menu_title
		'manage_options',								# capability
		'wprq/maps',									# menu_slug
		'wprq_maps'										# function (optional)
	);
	
	// Register CSS and Js			
	foreach ($page as &$details) {
		add_action( 'admin_print_styles-' . $details, 'wprq_admin_stylesheet' );
		add_action( 'admin_print_scripts-' . $details, 'wprq_admin_script' );
	}

	unset($details);

	return $page;										
}

// Styles
function wprq_admin_stylesheet() {
	// CSS Custom
	wp_register_style( 'style-admin.css', WPRQ_URL . 'assets/css/style-admin.css', array(), '1.0.0', 'all');
	wp_enqueue_style( 'style-admin.css' );
}

// Scripts
function wprq_admin_script() {
	wp_register_script( 'script-admin.js', WPRQ_URL . 'assets/js/script-admin.js', array(), '1.0.0', true);
	wp_enqueue_script( 'script-admin.js' );

	wp_register_script( 'zebra-form.js', WPRQ_URL . 'assets/js/zebra-form.min.js', array(), '1.0.0', true);
	wp_enqueue_script( 'zebra-form.js' );

	wp_register_script( 'google-maps.js', 'https://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=false', array(), '3.x.x', false);
	wp_enqueue_script( 'google-maps.js' );
}

// Function Page Main
function wprq_index() {
	// Page Main for Player
	require( plugin_dir_path(__FILE__) . 'admin-index.php' );
}

// Function Page My Characters
function wprq_mycharacters() {
	// Page Main for Player
	require( plugin_dir_path(__FILE__) . 'admin-my-characters.php' );
}

// Function Page Maps
function wprq_maps() {
	// Page Main for Player
	require( plugin_dir_path(__FILE__) . 'admin-maps.php' );
}


/***************************************************************
 * Ajax Functions MAP
 ***************************************************************/

add_action("wp_ajax_wprq-get_all_points_map", "wprq_get_all_points_map_callback");
add_action("wp_ajax_nopriv_wprq-get_all_points_maps", "wprq_get_all_points_map_callback");

function wprq_get_all_points_map_callback() {

	$response["request"] = array();
	$response["data"] = array();

	global $wpdb;

	include_once( plugin_dir_path(__FILE__) . 'classes/functions.php' );

    $table_points = $wpdb->prefix . WPRQ_NAME . '_maps_points';

	$points = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM " . $table_points . " WHERE point_map = '%d'",
			$_REQUEST["map"]
		)
	);

	$response["data"] = array();

	foreach ($points as $key => $point) {
		$response["data"][$key]["id"] 			= $point->point_id;
		$response["data"][$key]["map"] 			= $point->point_map;
		$response["data"][$key]["title"] 		= $point->point_title;
		$response["data"][$key]["description"] 	= $point->point_description;
		$response["data"][$key]["url"] 			= $point->point_url;
		$response["data"][$key]["latitude"] 	= $point->point_latitude;
		$response["data"][$key]["longitude"] 	= $point->point_longitude;
		$response["data"][$key]["html"] 		= wprq_generate_form_for_points_map($point);
	}

	header( "Content-Type: application/json" );
	echo json_encode($response);
	exit;
}

add_action("wp_ajax_wprq-insert_point_map", "wprq_insert_point_map_callback");

function wprq_insert_point_map_callback() {

	$response["request"] = array();
	$response["data"] = array();

	global $wpdb;

	include_once( plugin_dir_path(__FILE__) . 'classes/functions.php' );

	$table = $wpdb->prefix . WPRQ_NAME . '_maps_points';

	if ( isset($_REQUEST["author"]) AND isset($_REQUEST["latitude"]) AND isset($_REQUEST["latitude"]) ) {

		$data["point_author"] = $_REQUEST["author"];
		$format[] = '%d';

		$data["point_map"] = $_REQUEST["map"];
		$format[] = '%d';

		$data["point_icon"] = 'default';
		$format[] = '%d';

		$data["point_latitude"] = $_REQUEST["latitude"];
		$format[] = '%s';

		$data["point_longitude"] = $_REQUEST["longitude"];
		$format[] = '%s';

	}

	if ( isset($data) ) {

		$data["point_date_modified"] = date("Y-m-d H:i:s");
		$format[] = '%s';

		$inserted = $wpdb->insert( $table, $data, $format );

		if ( $inserted != false ) {

			$response["request"]["error"] = false;
			$response["request"]["message"] = __("Point created correctly.", WPRQ_TEXTDOMAIN);

			$table_points = $wpdb->prefix . WPRQ_NAME . '_maps_points';

			$point = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM " . $table_points . " WHERE point_id = '%d'",
					$wpdb->insert_id
				)
			);

			$response["data"]["id"] 			= $point->point_id;
			$response["data"]["map"] 			= $point->point_map;
			$response["data"]["title"] 			= $point->point_title;
			$response["data"]["description"] 	= $point->point_description;
			$response["data"]["url"] 			= $point->point_url;
			$response["data"]["latitude"] 		= $point->point_latitude;
			$response["data"]["longitude"] 		= $point->point_longitude;
			$response["data"]["html"] 			= wprq_generate_form_for_points_map($point);

			$table2 = $wpdb->prefix . WPRQ_NAME . '_maps';
			$wpdb->query("UPDATE " . $table2 . " SET map_num_points = map_num_points+1 WHERE map_id = '" . $point->point_map . "'");

		} else {

			$response["request"]["error"] = true;
			$response["request"]["message"] = __("We cant insert new point.", WPRQ_TEXTDOMAIN);

		}

	} else {

		$response["request"]["error"] = true;
		$response["request"]["message"] = __("Missing parameters.", WPRQ_TEXTDOMAIN);

	}

	header( "Content-Type: application/json" );
	echo json_encode($response);
	exit;
}

add_action("wp_ajax_wprq-update_point_map", "wprq_update_point_map_callback");
//add_action("wp_ajax_nopriv_wprq-get_all_points_maps", "wprq_get_all_points_map_callback");

function wprq_update_point_map_callback() {

	$response["request"] = array();
	$response["data"] = array();

	global $wpdb;

	include_once( plugin_dir_path(__FILE__) . 'classes/functions.php' );

	$table = $wpdb->prefix . WPRQ_NAME . '_maps_points';

	if ( isset( $_REQUEST["author"]) ) {
		$data["point_author"] = $_REQUEST["author"];
		$format[] = '%d';
	}

	if ( isset( $_REQUEST["title"]) ) {
		$data["point_title"] = $_REQUEST["title"];
		$format[] = '%s';
	}

	if ( isset( $_REQUEST["description"]) ) {
		$data["point_description"] = $_REQUEST["description"];
		$format[] = '%s';
	}

	if ( isset( $_REQUEST["url"]) ) {
		$data["point_url"] = $_REQUEST["url"];
		$format[] = '%s';
	}

	if ( isset( $_REQUEST["icon"]) ) {
		$data["point_icon"] = $_REQUEST["icon"];
		$format[] = '%d';
	}

	if ( isset( $_REQUEST["latitude"]) ) {
		$data["point_latitude"] = $_REQUEST["latitude"];
		$format[] = '%s';
	}

	if ( isset( $_REQUEST["longitude"]) ) {
		$data["point_longitude"] = $_REQUEST["longitude"];
		$format[] = '%s';
	}

	$response["request"]["_REQUEST"] = $_REQUEST;
	$response["request"]["_GET"] = $_GET;
	$response["request"]["_POST"] = $_POST;

	if ( isset($_REQUEST["id"]) AND isset($data) ) {

		$data["point_date_modified"] = date("Y-m-d H:i:s");
		$format[] = '%s';

		$where["point_id"] = $_REQUEST["id"];
		$where_format[] = '%d';

		$num_rows_updated = $wpdb->update( $table, $data, $where, $format, $where_format );

		if ( $num_rows_updated > 0 ) {

			$response["request"]["error"] = false;
			$response["request"]["message"] = __("Update correctly.", WPRQ_TEXTDOMAIN);

		} else {

			$response["request"]["error"] = false;
			$response["request"]["message"] = __("Not update.", WPRQ_TEXTDOMAIN);

		}

		$table_points = $wpdb->prefix . WPRQ_NAME . '_maps_points';

		$point = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . $table_points . " WHERE point_id = '%d'",
				$_REQUEST["id"]
			)
		);

		$response["data"]["id"] 			= $point->point_id;
		$response["data"]["map"] 			= $point->point_map;
		$response["data"]["title"] 			= $point->point_title;
		$response["data"]["description"] 	= $point->point_description;
		$response["data"]["url"] 			= $point->point_url;
		$response["data"]["latitude"] 		= $point->point_latitude;
		$response["data"]["longitude"] 		= $point->point_longitude;
		$response["data"]["html"] 			= wprq_generate_form_for_points_map($point);

	} else {

		$response["request"]["error"] = true;
		$response["request"]["message"] = __("Missing parameters.", WPRQ_TEXTDOMAIN);

	}

	header( "Content-Type: application/json" );
	echo json_encode($response);
	exit;
}

add_action("wp_ajax_wprq-delete_point_map", "wprq_delete_point_map_callback");

function wprq_delete_point_map_callback() {

	$response["request"] = array();
	$response["data"] = array();

	global $wpdb;

	include_once( plugin_dir_path(__FILE__) . 'classes/functions.php' );

	$table = $wpdb->prefix . WPRQ_NAME . '_maps_points';

	if ( isset($_REQUEST["id"]) ) {

		$where["point_id"] = $_REQUEST["id"];
		$where_format[] = '%d';

		$deleted = $wpdb->delete( $table, $where, $where_format = null  );

		if ( $deleted != false ) {

			$response["request"]["error"] = false;
			$response["request"]["message"] = __("Point deleted.", WPRQ_TEXTDOMAIN);

			$table2 = $wpdb->prefix . WPRQ_NAME . '_maps';
			$wpdb->query("UPDATE " . $table2 . " SET map_num_points = map_num_points-1 WHERE map_id = '" . $point->point_map . "'");

		} else {

			$response["request"]["error"] = true;
			$response["request"]["message"] = __("We can't delete the point.", WPRQ_TEXTDOMAIN);

		}

	} else {

		$response["request"]["error"] = true;
		$response["request"]["message"] = __("Missing parameters.", WPRQ_TEXTDOMAIN);

	}

	header( "Content-Type: application/json" );
	echo json_encode($response);
	exit;
}
