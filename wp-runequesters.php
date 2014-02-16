<?php
/**
 * @package wp-runequesters
 */
/*
Plugin Name: WP Runequesters
Plugin URI: http://wordpress.org/plugins/wp-runequesters/
Description: WP Runequesters
Version: 1.0.0
Author: WaKeMaTTa (Mohamed Ziata)
Author URI: https://github.com/WaKeMaTTa/
License: GPLv2 or later
Textdomain: wprq
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Name Plugin
if ( !defined( 'WPRQ_NAME' ) )
	define( 'WPRQ_NAME', 'wprq' );

// URL Plugin
if ( !defined( 'WPRQ_URL' ) )
	define( 'WPRQ_URL', plugin_dir_url( __FILE__ ) );

// Define Basename
if ( !defined( 'WPRQ_BASENAME' ) )
	define( 'WPRQ_BASENAME', plugin_basename( __FILE__ ) );

if ( !defined( 'WPRQ_PATH' ) )
	define( 'WPRQ_PATH', plugin_dir_path( __FILE__ ) );

// Define Textdomain
if ( !defined( 'WPRQ_TEXTDOMAIN' ) )
	define( 'WPRQ_TEXTDOMAIN', WPRQ_NAME );

// Define Uploads Dir
if ( !defined( 'WPRQ_UPLOADS_DIR' ) )
	define( 'WPRQ_UPLOADS_DIR', WP_CONTENT_DIR . '/uploads/' . WPRQ_NAME . '/');

if ( !defined( 'WPRQ_URL_UPLOADS_DIR' ) ) {
	$upload_dir = wp_upload_dir();
	define( 'WPRQ_URL_UPLOADS_DIR', $upload_dir["baseurl"] . '/' . WPRQ_NAME . '/');
}

if ( !defined( 'WPRQ_URL_UPLOADS_DIR_CHARACTERS' ) )
	define( 'WPRQ_URL_UPLOADS_DIR_CHARACTERS', WPRQ_URL_UPLOADS_DIR . 'characters/');

// Define Version Plugin (key and value)
if ( !defined( 'WPRQ_VERSION_KEY' ) )
	define( 'WPRQ_VERSION_KEY', WPRQ_NAME . '_version');

// Define Version Plugin (value)
if ( !defined( 'WPRQ_VERSION_VALUE' ) )
	define( 'WPRQ_VERSION_VALUE', '1.0.0' );

add_option( WPRQ_VERSION_KEY, WPRQ_VERSION_VALUE );

// [ Good Infomation: http://wp.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/ ]

// Include a files
require_once dirname( __FILE__ ) . '/admin.php';
#include_once dirname( __FILE__ ) . '/widget.php';

// Function to activate the plugin
function wprq_init() {
	// Globals
	global $wpdb;

	// Loading textdomain
	load_plugin_textdomain( 'wprq', false, dirname(plugin_basename( __FILE__ )).'/lang/' );
}

// Activate the plugin
add_action( 'init', 'wprq_init' );

function wprq_activation() {
	// Create database table
	wprq_create_database_table();

	$dirs["base"] 				= WPRQ_UPLOADS_DIR;
	$dirs["characters"] 		= WPRQ_UPLOADS_DIR . 'characters/';
	$dirs["characters_avatars"] = WPRQ_UPLOADS_DIR . 'characters/avatars/';
	$dirs["characters_pdfs"] 	= WPRQ_UPLOADS_DIR . 'characters/pdfs/';
	$dirs["maps"] 				= WPRQ_UPLOADS_DIR . 'maps/';

	// Makeing a folders for upload imagens and more
	foreach ($dirs as $path) {
		wp_mkdir_p( trailingslashit( $path ) );
		chmod( $path, 0775 );
		$handle = fopen( $path . 'index.html', 'w');
		fwrite ( $handle , '<html><head></head><body></body></html>' );
		fclose ( $handle );
	}
}

// Activation Plugin
register_activation_hook( __FILE__, 'wprq_activation');

// Database tables
function wprq_create_database_table() {
	global $wpdb;

	$prefix = $wpdb->prefix . WPRQ_NAME . '_';

	$sqls["characters"] = "CREATE TABLE " . $prefix . "characters (
		character_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		character_author bigint(20) unsigned NOT NULL,
		character_name varchar(250) NOT NULL,
		character_description text NOT NULL,
		character_privacity varchar(20) NOT NULL DEFAULT 'private' COMMENT 'public-open | public-close | private',
		character_avatar varchar(60) NOT NULL,
		character_date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		character_date_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (character_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";

	$sqls["characters_uploads"] = "CREATE TABLE " . $prefix . "characters_uploads (
		upload_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		upload_character bigint(20) unsigned NOT NULL,
		upload_author bigint(20) unsigned NOT NULL,
		upload_file_name varchar(60) NOT NULL,
		upload_type varchar(20) NOT NULL,
		upload_size bigint(20) unsigned NOT NULL,
		upload_date_uploaded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (upload_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";

	$sqls["maps"] = "CREATE TABLE " . $prefix . "maps (
		map_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		map_dir varchar(250) NOT NULL,
		map_author bigint(20) unsigned NOT NULL,
		map_title varchar(150) NOT NULL,
		map_description text NOT NULL,
		map_num_points int(11) unsigned NOT NULL,
		map_status varchar(20) NOT NULL DEFAULT 'publish' COMMENT 'publish | deleted',
		map_date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		map_date_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (map_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";

	$sqls["maps_points"] = "CREATE TABLE " . $prefix . "maps_points (
		point_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		point_map bigint(20) unsigned NOT NULL,
		point_author bigint(20) unsigned NOT NULL,
		point_title varchar(150) NOT NULL,
		point_description text NOT NULL,
		point_url text NOT NULL,
		point_icon varchar(50) NOT NULL,
		point_latitude varchar(20) NOT NULL,
		point_longitude varchar(20) NOT NULL,
		point_image varchar(60) NOT NULL,
		point_date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		point_date_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (point_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";

	$sqls["points_icons"] = "CREATE TABLE " . $prefix . "points_icons (
		icon_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		icon_title varchar(50) NOT NULL,
		icon_slug varchar(50) NOT NULL,
		icon_image varchar(60) NOT NULL,
		PRIMARY KEY  (icon_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	foreach ($sqls as $key => $sql) {
		dbDelta($sql);
	}
}
