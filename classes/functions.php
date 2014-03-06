<?php
/**
 * @package wp-runequesters
 */

/***************************************************************
 * Functions for "Maps"
 ***************************************************************/

function wprq_get_user_created_map($map_id) {
	global $wpdb;
	$table = $wpdb->prefix . WPRQ_NAME . '_maps';
	$select_id_author = $wpdb->get_row("SELECT map_author FROM " . $table . " WHERE map_id = '" . $map_id . "'");
	return $select_id_author;
}

function wprq_message_response($type, $msg, $print=true) {
	$html = '<div class="updated alert ' . $type . ' fade in">';
	$html .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $msg;
	$html .= '</div>';

	if ( $print == true )
		echo $html;

	return $html;
}

function wprq_generate_form_for_points_map($point) {
	global $wpdb;

	$response = '';
	$response .= '<form name="form-add-point-map" id="form-add-point-map" action="" method="post">';
	$response .= '<input type="hidden" name="id" id="id" value="' . $point->point_id . '" class="hidden">';
	$response .= '<label for="title" id="label_title">' . __('Title' , WPRQ_TEXTDOMAIN) . '</label>';
	$response .= '<input type="text" name="title" id="title" value="' . $point->point_title . '" class="control text" maxlength="50">';
	$response .= '<br>';
	$response .= '<label for="icon" id="label_icon">' . __('Icon' , WPRQ_TEXTDOMAIN) . '</label>';
	$response .= '<select name="icon" id="icon" class="control">';

	$icons = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . WPRQ_NAME . '_maps_pictograms' . " " );	

	$selected = false;

	foreach ( $icons as $key => $icon) {
		$response .= '<option value="' . $icon->icon_slug . '"';
		
		if ($icon->icon_slug == $point->point_icon) {
			$response .= ' selected="selected"';
			$selected = true;
		}
		
		$response .= '>' . $icon->icon_title . '</option>';
	}

	$response .= '<option value="default"' . ( ($selected == false) ? ' selected="selected"' : '' ) . '>' . __('Default' , WPRQ_TEXTDOMAIN) . '</option>';
	$response .= '</select>';
	$response .= '<br>';
	$response .= '<label for="url" id="label_url">' . __('URL (more informatión)' , WPRQ_TEXTDOMAIN) . '</label>';
	$response .= '<input type="text" name="url" id="url" value="' . $point->point_url . '" class="control text">';
	$response .= '<br>';
	$response .= '<label for="description" id="label_description">' . __('Description' , WPRQ_TEXTDOMAIN) . '</label>';
	$response .= '<br>';
	$response .= '<textarea name="description" id="description" rows="5" cols="80" class="control">' . $point->point_description . '</textarea>';
	$response .= '<br>';
	$response .= '<input type="button" name="btn-update-point" id="btn-update-point" onclick="update_point();" value="' . __('Save' , WPRQ_TEXTDOMAIN) . '" class="submit button button-primary"> ';
	$response .= '<input type="button" name="btn-delete-point" id="btn-delete-point" value="' . __('Delete' , WPRQ_TEXTDOMAIN) . '" class="submit button button-delete" onclick="delete_point(' . $point->point_id . ');"">';
	$response .= '</form>';

	return $response;
}

function wprq_generate_display_for_points_map($point) {
	global $wpdb;

	$response = '';
	$response .= '<div>';
	$response .= '<p><strong>' . $point->point_title . '</strong></p>';
	$response .= '<p style="white-space: pre-wrap;">' . $point->point_description . '</p>';

	if ($point->point_url != '')
		$response .= '<p><a href="' . $point->point_url . '" target="_blank">' . __('Read more' , WPRQ_TEXTDOMAIN) . '</a></p>';

	$response .= '</div>';

	return $response;
}

function wprq_get_url_pictogram_map($point) {
	global $wpdb;

	$response = 'https://mts.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1';

	$icon = $wpdb->get_row( "SELECT icon_image FROM " . $wpdb->prefix . WPRQ_NAME . '_maps_pictograms' . " WHERE icon_slug = '" . $point->point_icon . "'" );	

	if ( $icon === null )
		return $response;

	if ( !file_exists( WPRQ_UPLOADS_DIR . 'pictograms/' . $icon->icon_image ) )
		return $response;

	$response = WPRQ_URL_UPLOADS_DIR . 'pictograms/' . $icon->icon_image;

	return $response;
}

/***************************************************************
 * Functions for "My Characters"
 ***************************************************************/

function get_id_user_of_character($id_character) {
	global $wpdb;
	$table = $wpdb->prefix . WPRQ_NAME . '_characters';
	$id_user = $wpdb->get_row("SELECT character_author FROM " . $table . " WHERE character_id = '" . $id_character . "'");
	return $id_user;
}


/***************************************************************
 * Delete Foder
 ***************************************************************/

function wprq_deleter_dir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

?>