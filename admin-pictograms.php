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

$show_list_pictograms = true;
$msg = array();

## ADD PICTOGRAM

if ( isset($_GET["action"]) AND ($_GET["action"] == 'add') AND current_user_can( 'manage_options' ) ) {

	global $wpdb;

	$show_list_pictograms = false;

	include_once( WPRQ_PATH . 'classes/zebra-form/Zebra_Form.php' );

	$form = new Zebra_Form('form-add-pictogram');

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

	## Slug

	$form->add('label', 'label_slug', 'slug', __( 'Slug' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('text', 'slug');

	$obj->set_rule(array(
		'required' => array(
			'error', 
			sprintf(
				__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
				__( 'Slug' , WPRQ_TEXTDOMAIN ) 
			),
		),
		'length' => array(
			1, 
			50, 
			'error', 
			sprintf(
				__( 'The %s must have between %s and %s characters!' , WPRQ_TEXTDOMAIN ),
				__( 'Slug' , WPRQ_TEXTDOMAIN ),
				1,
				50
			),
		),
	));

	## Upload avatar character

	$form->add('label', 'label_upload_pictogram', 'upload_pictogram', __( 'Pictogram (max 34px height)' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('file', 'upload_pictogram');

	$obj->set_rule(array(
		'required' => array(
			'error', 
			sprintf(
				__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
				__( 'Pictogram' , WPRQ_TEXTDOMAIN ) 
			),
		),
		'upload' => array(
			WPRQ_UPLOADS_DIR . 'pictograms/', 
			ZEBRA_FORM_UPLOAD_RANDOM_NAMES, 
			'error',
			sprintf(
				__( 'Could not upload file!<br>Check that the "%s" folder exists and that it is writable' , WPRQ_TEXTDOMAIN ), 
				WPRQ_UPLOADS_DIR . 'pictograms/'
			)
		),
		'filetype' => array(
			'png, jpg, jpeg, gif',
			'error',
			sprintf(
				__( 'Only could upload files with extension: %s' , WPRQ_TEXTDOMAIN ), 
				'<code>.png</code>, <code>.jpg/jpeg</code>, <code>.gif</code>'
			),
		)
	));

	## Submit

	$form->add('submit', 'btnsubmit', __( 'Add New Pictogram', WPRQ_TEXTDOMAIN ), array(
		'class' => 'button button-primary',

	));

	## Validate the form

	if ($form->validate()) {
		global $wpdb;

		$table = $wpdb->prefix . WPRQ_NAME . '_maps_pictograms';

		$data["icon_author"] = get_current_user_id();
		$format[] = '%d';

		$data["icon_title"] = $_POST["title"];
		$format[] = '%s';

		$data["icon_slug"] = $_POST["slug"];
		$format[] = '%s';

		$data["icon_image"] = $form->file_upload["upload_pictogram"]["file_name"];
		$format[] = '%s';

		$data["icon_date_created"] = date("Y-m-d H:i:s");
		$format[] = '%s';

		$insert_map = $wpdb->insert( $table, $data, $format );

		if ( $insert_map === false ) {
			$form->add_error('error', __( "<strong>Oh snap!</strong> We couldn't insert pictogram in database.", WPRQ_TEXTDOMAIN) );
		} else {
			$show_list_pictograms = true;
			$msg["type"]	= 'success';
			$msg["message"] = __( "<strong>Well done!</strong> The pictogram was saved correctly.", WPRQ_TEXTDOMAIN );
		}

	}

	if ( $show_list_pictograms == false ) {

		?>

		<div class="wrap">
		
			<div class="icon32 icon32-main"><br></div>
			
			<h2>
				<?php echo sprintf( '%s %s', __( 'Add New' , WPRQ_TEXTDOMAIN ), __( 'Pictogram' , WPRQ_TEXTDOMAIN ) ); ?>
				<?php echo '<p>' . __( 'Create a new pictogram.' , WPRQ_TEXTDOMAIN ) . '</p>'; ?>
			</h2>
			
			<?php $form->render( WPRQ_PATH . 'tpls/form-add-pictogram.php' ); ?>

		</div>

		<?php

	}

}

## EDIT PICTOGRAM

if ( isset($_GET["action"]) AND ($_GET["action"] == 'edit') AND is_numeric($_GET["pictogram"]) ) {

	$show_list_pictograms = false;

	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_maps_pictograms';

	## select row
	
	$pictogram = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE icon_id = '%d'",
			$_GET["pictogram"]
		)
	);

	if ( $pictogram === null ) {

		$show_list_pictograms = true;
		$msg["type"]	= 'error';
		$msg["message"] = sprintf(
			__( "<strong>Oh snap!</strong> The map with ID = %d wasn't in database.", WPRQ_TEXTDOMAIN ),
			$_GET["pictogram"]
		);

	} else {

		include_once( WPRQ_PATH . 'classes/zebra-form/Zebra_Form.php' );

		$form = new Zebra_Form('form-edit-pictogram');

		## Title

		$form->add('label', 'label_title', 'title', __( 'Title' , WPRQ_TEXTDOMAIN) );

		$obj = $form->add('text', 'title', $pictogram->icon_title);

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

		## Slug

		$form->add('label', 'label_slug', 'slug', __( 'Slug' , WPRQ_TEXTDOMAIN) );

		$obj = $form->add('text', 'slug', $pictogram->icon_slug, array(
			'disabled' => 'disabled',
		));

		## Upload avatar character

		$form->add('label', 'label_upload_pictogram', 'upload_pictogram', __( 'Pictogram (max 34px height)' , WPRQ_TEXTDOMAIN) );

		$obj = $form->add('file', 'upload_pictogram');

		$obj->set_rule(array(
			'upload' => array(
				WPRQ_UPLOADS_DIR . 'pictograms/', 
				ZEBRA_FORM_UPLOAD_RANDOM_NAMES, 
				'error',
				sprintf(
					__( 'Could not upload file!<br>Check that the "%s" folder exists and that it is writable' , WPRQ_TEXTDOMAIN ), 
					WPRQ_UPLOADS_DIR . 'pictograms/'
				)
			),
			'filetype' => array(
				'png, jpg, jpeg, gif',
				'error',
				sprintf(
					__( 'Only could upload files with extension: %s' , WPRQ_TEXTDOMAIN ), 
					'<code>.png</code>, <code>.jpg/jpeg</code>, <code>.gif</code>'
				),
			)
		));

		## Submit

		$form->add('submit', 'btnsubmit', __( 'Pictogram Map', WPRQ_TEXTDOMAIN ), array(
			'class' => 'button button-primary',

		));

		## Validate the form

		if ($form->validate()) {
			global $wpdb;

			$table = $wpdb->prefix . WPRQ_NAME . '_maps_pictograms';

			$data["icon_title"] = $_POST["title"];
			$format[] = '%s';

			$data["icon_title"] = $_POST["title"];
			$format[] = '%s';

			if ( isset($form->file_upload["upload_pictogram"]) ) {
				$data["icon_image"] = $form->file_upload["upload_pictogram"]["file_name"];
				$format[] = '%s';
			}

			$where["icon_id"] = $_GET["pictogram"];
			$where_format [] = '%d';

			$insert_pictogram = $wpdb->update( $table, $data, $where, $format, $where_format  );

			if ( $insert_pictogram === false ) {
				$form->add_error('error', __( "<strong>Oh snap!</strong> We couldn't update pictogram in database.", WPRQ_TEXTDOMAIN) );
			} else {
				$show_list_pictograms = true;
				$msg["type"]	= 'success';
				$msg["message"] = __( "<strong>Well done!</strong> The pictogram was updated correctly.", WPRQ_TEXTDOMAIN );
			}

		}

		if ( $show_list_pictograms == false ) {

			?>

			<div class="wrap">
			
				<div class="icon32 icon32-main"><br></div>
				
				<h2>
					<?php echo sprintf( '%s %s', __( 'Edit' , WPRQ_TEXTDOMAIN ), __( 'Pictogram' , WPRQ_TEXTDOMAIN ) ); ?>
					<?php echo '<p>' . __( 'Modify a pictogram.' , WPRQ_TEXTDOMAIN ) . '</p>'; ?>
				</h2>
				
				<?php

				$form->render( WPRQ_PATH . 'tpls/form-edit-pictogram.php', false, array(
					'pictogram' => $pictogram->icon_image
				) );

				?>

			</div>

			<?php

		}
	}

}

## DELETE PICTOGRAM

if ( isset($_GET["action"]) AND ($_GET["action"] == 'delete') AND is_numeric($_GET["pictogram"]) ) {
	
	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_maps_pictograms';

	## select row
	
	$pictogram_row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE icon_id = '%d'",
			$_GET["pictogram"]
		)
	);

	if ( $pictogram_row === null ) {

		$show_list_pictograms = true;
		$msg["type"]	= 'error';
		$msg["message"] = sprintf(
			__( "<strong>Oh snap!</strong> The pictogram with ID = %d wasn't in database.", WPRQ_TEXTDOMAIN ),
			$_GET["pictogram"]
		);

	} else {

		// delete row

		$where["icon_id"] = $_GET["pictogram"];
		$where_format = '%d';

		$delete_pictogram = $wpdb->delete( $table, $where, $where_format );

		if ( $delete_pictogram == false ) {

			$show_list_pictograms = true;
			$msg["type"]	= 'error';
			$msg["message"] = __( "<strong>Oh snap!</strong> The pictogram wasn't deleted.", WPRQ_TEXTDOMAIN );

		} else {

			$show_list_pictograms = true;
			$msg["type"]	= 'success';
			$msg["message"] = __( '<strong>Well done!</strong> The pictogram was deleted correctly.', WPRQ_TEXTDOMAIN );

			unlink( WPRQ_UPLOADS_DIR . 'pictograms/' . $pictogram_row->icon_image );

			$wpdb->query( "UPDATE " . $wpdb->prefix . WPRQ_NAME . '_maps_points' . " SET point_icon = 'default' WHERE point_icon NOT IN ( SELECT icon_slug FROM " . $table . " )" );

		}

	}

	$show_list_pictograms = true;

}

## SHOW LIST PICTOGRAM

if ( $show_list_pictograms == true ) {

	include_once( plugin_dir_path(__FILE__) . 'classes/WP_List_Table_Pictograms.php' );

	// Prepare Table of elements
	$list_table_pictograms = new WP_List_Table_Pictograms();
	$list_table_pictograms->prepare_items();

	$button_add = sprintf('<a href="?page=%s&action=%s" class="add-new-h2">%s</a>',
		$_REQUEST['page'],
		'add',
		__( 'Add New' , WPRQ_TEXTDOMAIN )
	);

	?>

	<div class="wrap">
		
		<div class="icon32 icon32-main"><br></div>
		
		<h2>
			<?php _e( 'Pictogram', WPRQ_TEXTDOMAIN ); ?>
			<?php echo $button_add; ?>
		</h2>

		<?php

		if ( !empty($msg) )
			message_response($msg["type"], $msg["message"]);

		$list_table_pictograms->display();

		?>

	</div>

	<?php

}

?>