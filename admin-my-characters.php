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

$show_list_characters = true;
$msg = array();

## ADD CHARACTER

if ( isset($_GET["action"]) AND ($_GET["action"] == 'add') ) {

	$show_list_characters = false;

	include_once( WPRQ_PATH . 'classes/zebra-form/Zebra_Form.php' );

	$form = new Zebra_Form('form-add-character');

	## Name

	$form->add('label', 'label_name', 'name', __( 'Name of character' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('text', 'name');

	$obj->set_rule(array(
		'required' => array(
			'error', 
			sprintf(
				__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
				__( 'Name of character' , WPRQ_TEXTDOMAIN ) 
			),
		),
		'length' => array(
			1, 
			50, 
			'error', 
			sprintf(
				__( 'The %s must have between %s and %s characters!' , WPRQ_TEXTDOMAIN ),
				__( 'Name of character' , WPRQ_TEXTDOMAIN ),
				1,
				50
			),
		),
	));

	## Description

	$form->add('label', 'label_description', 'description', __( 'Short description' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('textarea', 'description');

	## Privacity

	$form->add('label', 'label_privacity', 'privacity', __( 'Privacity' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('select', 'privacity');

	$obj->add_options(array(
		'private' 		=> __( 'Private' , WPRQ_TEXTDOMAIN),
		'public-close' 	=> __( 'Public (close)' , WPRQ_TEXTDOMAIN),
		'public-open' 	=> __( 'Public (open)' , WPRQ_TEXTDOMAIN)
	));

	$note_privacity_1 = '<strong>';
	$note_privacity_1 .=  __( 'Private' , WPRQ_TEXTDOMAIN );
	$note_privacity_1 .= '</strong> ';
	$note_privacity_1 .= '<br>☒ ' . __( 'Visible on Blog' , WPRQ_TEXTDOMAIN );
	$note_privacity_1 .= '<br>☑ ' . __( 'Owner can upload a new version of it' , WPRQ_TEXTDOMAIN );
	$note_privacity_1 .= '<br>☒ ' . __( 'Administrator of site can upload a new version of it' , WPRQ_TEXTDOMAIN );
	$note_privacity_1 .= '<br>☒ ' . __( 'Others can upload a new version of it' , WPRQ_TEXTDOMAIN );
	
	$note_privacity_2 = '<strong>';
	$note_privacity_2 .=  __( 'Public (close)' , WPRQ_TEXTDOMAIN );
	$note_privacity_2 .= '</strong> ';
	$note_privacity_2 .= '<br>☑ ' . __( 'Visible on Blog' , WPRQ_TEXTDOMAIN );
	$note_privacity_2 .= '<br>☑ ' . __( 'Owner can upload a new version of it' , WPRQ_TEXTDOMAIN );
	$note_privacity_2 .= '<br>☑ ' . __( 'Administrator of site can upload a new version of it' , WPRQ_TEXTDOMAIN );
	$note_privacity_2 .= '<br>☒ ' . __( 'Others can upload a new version of it' , WPRQ_TEXTDOMAIN );
	
	$note_privacity_3 = '<strong>';
	$note_privacity_3 .=  __( 'Public (open)' , WPRQ_TEXTDOMAIN );
	$note_privacity_3 .= '</strong> ';
	$note_privacity_3 .= '<br>☑ ' . __( 'Visible on Blog' , WPRQ_TEXTDOMAIN );
	$note_privacity_3 .= '<br>☑ ' . __( 'Owner can upload a new version of it' , WPRQ_TEXTDOMAIN );
	$note_privacity_3 .= '<br>☑ ' . __( 'Administrator of site can upload a new version of it' , WPRQ_TEXTDOMAIN );
	$note_privacity_3 .= '<br>☑ ' . __( 'Others can upload a new version of it' , WPRQ_TEXTDOMAIN );

	$obj->set_rule(array(
		'required' => array(
			'error', 
			sprintf(
				__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
				__( 'Privacity' , WPRQ_TEXTDOMAIN ) 
			)
		)
	));

	$form->add('note', 'note_privacity_1', 'privacity', $note_privacity_1);
	$form->add('note', 'note_privacity_2', 'privacity', $note_privacity_2);
	$form->add('note', 'note_privacity_3', 'privacity', $note_privacity_3);

	## Upload avatar character

	$form->add('label', 'label_upload_avatar', 'upload_avatar', __( 'Avatar character' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('file', 'upload_avatar');

	$obj->set_rule(array(
		'upload' => array(
			WPRQ_UPLOADS_DIR . 'characters/avatars/', 
			ZEBRA_FORM_UPLOAD_RANDOM_NAMES, 
			'error',
			sprintf(
				__( 'Could not upload file!<br>Check that the "%s" folder exists and that it is writable' , WPRQ_TEXTDOMAIN ), 
				WPRQ_UPLOADS_DIR . 'characters/avatars/'
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

	## Upload PDF character

	$form->add('label', 'label_upload_pdf', 'upload_pdf', __( 'PDF character' , WPRQ_TEXTDOMAIN) );

	$obj = $form->add('file', 'upload_pdf');

	$obj->set_rule(array(
		'required' => array(
			'error', 
			sprintf(
				__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
				__( 'PDF character' , WPRQ_TEXTDOMAIN ) 
			)
		),
		'upload' => array(
			WPRQ_UPLOADS_DIR . 'characters/pdfs/', 
			ZEBRA_FORM_UPLOAD_RANDOM_NAMES, 
			'error',
			sprintf(
				__( 'Could not upload file!<br>Check that the "%s" folder exists and that it is writable' , WPRQ_TEXTDOMAIN ), 
				WPRQ_UPLOADS_DIR . 'characters/pdfs/'
			)
		),
		'filetype' => array(
			'pdf',
			'error',
			sprintf(
				__( 'Only could upload files with extension: %s' , WPRQ_TEXTDOMAIN ), 
				'<code>.pdf</code>'
			),
		)
	));

	## Terms + Licence

	$form->add('label', 'label_terms', 'accept_yes', __( 'Terms', WPRQ_TEXTDOMAIN));

	$obj = $form->add('checkbox', 'accept', 'yes');

	$obj->set_rule(array(
		'required' => array(
			'error', 
			sprintf(
				__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
				__( 'Terms' , WPRQ_TEXTDOMAIN ) 
			)
		)
	));

	$note_licence = __( 'By checking this checkbox, confirmed that he will agree that the content under license Creative Commons By-SA 3.0' , WPRQ_TEXTDOMAIN );

	$form->add('note', 'note_accept_yes', 'accept_yes', $note_licence);

	$form->add('label', 'label_accept_yes', 'accept_yes', '');

	## Submit

	$form->add('submit', 'btnsubmit', __( 'Add New Character', WPRQ_TEXTDOMAIN ), array(
		'class' => 'button button-primary',

	));

	## Validate the form

	if ( $form->validate() ) {
		global $wpdb;

		$table_characters = $wpdb->prefix . WPRQ_NAME . '_characters';

		$data_char["character_author"] = get_current_user_id();
		$format_char[] = '%d';

		$data_char["character_name"] = $_POST["name"];
		$format_char[] = '%s';

		$data_char["character_description"] = $_POST["description"];
		$format_char[] = '%s';

		$data_char["character_privacity"] = $_POST["privacity"];
		$format_char[] = '%s';

		$data_char["character_date_created"] = date("Y-m-d H:i:s");
		$format_char[] = '%s';

		if ( isset($form->file_upload["upload_avatar"]) ) {

			## Save Avatar

			$data_char["character_avatar"] = $form->file_upload["upload_avatar"]["file_name"];
			$format_char[] = '%s';

			$source_image 	= $form->file_upload["upload_avatar"]["path"] . $form->file_upload["upload_avatar"]["file_name"];

			$sizes[] = 64;
			$sizes[] = 128;
			$sizes[] = 256;
			$sizes[] = 512;

			foreach ($sizes as $key => $size) {
				$image = wp_get_image_editor( $source_image );
				$image->resize( $size, $size, false );
				$image->save( $form->file_upload["upload_avatar"]["path"] . $size . '-' . $form->file_upload["upload_avatar"]["file_name"] );
			}

		} else {
			$data_char["character_avatar"] = '';
			$format_char[] = '%s';
		}

		$insert_character = $wpdb->insert( $table_characters, $data_char, $format_char );

		if ( $insert_character === false ) {
			$form->add_error('error', __( "<strong>Oh snap!</strong> We couldn't insert map in database.", WPRQ_TEXTDOMAIN) );

		} else {

			$id_character = $wpdb->insert_id;

			$table_uploads = $wpdb->prefix . WPRQ_NAME . '_characters_uploads';

			$data_pdf["upload_character"] = $id_character;
			$format_pdf[] = '%d';

			$data_pdf["upload_author"] = get_current_user_id();
			$format_pdf[] = '%d';

			$data_pdf["upload_type"] = $form->file_upload["upload_pdf"]["type"];
			$format_pdf[] = '%s';

			$data_pdf["upload_size"] = $form->file_upload["upload_pdf"]["size"];
			$format_pdf[] = '%d';

			$data_pdf["upload_file_name"] = $form->file_upload["upload_pdf"]["file_name"];
			$format_pdf[] = '%s';

			$insert_pdf = $wpdb->insert( $table_uploads, $data_pdf, $format_pdf );

			$show_list_characters = true;
			$msg["type"]	= 'success';
			$msg["message"] = __( "<strong>Well done!</strong> The character was saved correctly.", WPRQ_TEXTDOMAIN );
		}

	}

	if ( $show_list_characters == false ) {

		?>

		<div class="wrap">
		
			<div class="icon32 icon32-main"><br></div>
			
			<h2>
				<?php echo sprintf( '%s %s', __( 'Add New' , WPRQ_TEXTDOMAIN ), __( 'Character' , WPRQ_TEXTDOMAIN ) ); ?>
				<?php echo '<p>' . __( 'Create a new character.' , WPRQ_TEXTDOMAIN ) . '</p>'; ?>
			</h2>
			
			<?php $form->render( WPRQ_PATH . 'tpls/form-add-character.php' ); ?>

		</div>

		<?php

	}

}

## EDIT CHARACTER

if ( isset($_GET["action"]) AND ($_GET["action"] == 'edit') ) {

	$show_list_characters = false;

	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_characters';
	
	$character = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE character_id = '%d'",
			$_GET["character"]
		)
	);

	if ( $character === null ) {

		$show_list_characters = true;
		$msg["type"]	= 'error';
		$msg["message"] = sprintf(
			__( "<strong>Oh snap!</strong> The character with ID = %d wasn't in database.", WPRQ_TEXTDOMAIN ),
			$_GET["character"]
		);

	} else {

		$that_user_can_edit = false;

		# See if that user can edit that character

		if ( ($character->character_privacity == 'private') AND ($character->character_author == get_current_user_id()) ) {

			$that_user_can_edit = true;

		} elseif ( ($character->character_privacity == 'public-close') AND ($character->character_author == get_current_user_id()) ) {

			$that_user_can_edit = true;

		} elseif ( ($character->character_privacity == 'public-close') AND current_user_can( 'manage_options' ) ) {

			$that_user_can_edit = true;

		} elseif ($character->character_privacity == 'public-open') {

			$that_user_can_edit = true;

		}

		if ( $that_user_can_edit == false ) {

			# Can't edit

			$show_list_characters = true;
			$msg["type"]	= 'error';
			$msg["message"] = sprintf(
				__( "<strong>Oh snap!</strong> You can't edit the character with ID = %d.", WPRQ_TEXTDOMAIN ),
				$_GET["character"]
			);

		} else {

			# Can edit

			include_once( WPRQ_PATH . 'classes/zebra-form/Zebra_Form.php' );

			$form = new Zebra_Form('form-edit-character');

			## Name

			$form->add('label', 'label_name', 'name', __( 'Name of character' , WPRQ_TEXTDOMAIN) );

			$obj = $form->add('text', 'name', $character->character_name );

			$obj->set_rule(array(
				'required' => array(
					'error', 
					sprintf(
						__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
						__( 'Name of character' , WPRQ_TEXTDOMAIN ) 
					),
				),
				'length' => array(
					1, 
					50, 
					'error', 
					sprintf(
						__( 'The %s must have between %s and %s characters!' , WPRQ_TEXTDOMAIN ),
						__( 'Name of character' , WPRQ_TEXTDOMAIN ),
						1,
						50
					),
				),
			));

			## Description

			$form->add('label', 'label_description', 'description', __( 'Short description' , WPRQ_TEXTDOMAIN) );

			$obj = $form->add('textarea', 'description', $character->character_description );

			## Privacity

			$form->add('label', 'label_privacity', 'privacity', __( 'Privacity' , WPRQ_TEXTDOMAIN) );

			$obj = $form->add('select', 'privacity', $character->character_privacity );

			$obj->add_options(array(
				'private' 		=> __( 'Private' , WPRQ_TEXTDOMAIN),
				'public-close' 	=> __( 'Public (close)' , WPRQ_TEXTDOMAIN),
				'public-open' 	=> __( 'Public (open)' , WPRQ_TEXTDOMAIN)
			));

			$note_privacity_1 = '<strong>';
			$note_privacity_1 .=  __( 'Private' , WPRQ_TEXTDOMAIN );
			$note_privacity_1 .= '</strong> ';
			$note_privacity_1 .= '<br>☒ ' . __( 'Visible on Blog' , WPRQ_TEXTDOMAIN );
			$note_privacity_1 .= '<br>☑ ' . __( 'Owner can upload a new version of it' , WPRQ_TEXTDOMAIN );
			$note_privacity_1 .= '<br>☒ ' . __( 'Administrator of site can upload a new version of it' , WPRQ_TEXTDOMAIN );
			$note_privacity_1 .= '<br>☒ ' . __( 'Others can upload a new version of it' , WPRQ_TEXTDOMAIN );
			
			$note_privacity_2 = '<strong>';
			$note_privacity_2 .=  __( 'Public (close)' , WPRQ_TEXTDOMAIN );
			$note_privacity_2 .= '</strong> ';
			$note_privacity_2 .= '<br>☑ ' . __( 'Visible on Blog' , WPRQ_TEXTDOMAIN );
			$note_privacity_2 .= '<br>☑ ' . __( 'Owner can upload a new version of it' , WPRQ_TEXTDOMAIN );
			$note_privacity_2 .= '<br>☑ ' . __( 'Administrator of site can upload a new version of it' , WPRQ_TEXTDOMAIN );
			$note_privacity_2 .= '<br>☒ ' . __( 'Others can upload a new version of it' , WPRQ_TEXTDOMAIN );
			
			$note_privacity_3 = '<strong>';
			$note_privacity_3 .=  __( 'Public (open)' , WPRQ_TEXTDOMAIN );
			$note_privacity_3 .= '</strong> ';
			$note_privacity_3 .= '<br>☑ ' . __( 'Visible on Blog' , WPRQ_TEXTDOMAIN );
			$note_privacity_3 .= '<br>☑ ' . __( 'Owner can upload a new version of it' , WPRQ_TEXTDOMAIN );
			$note_privacity_3 .= '<br>☑ ' . __( 'Administrator of site can upload a new version of it' , WPRQ_TEXTDOMAIN );
			$note_privacity_3 .= '<br>☑ ' . __( 'Others can upload a new version of it' , WPRQ_TEXTDOMAIN );

			$obj->set_rule(array(
				'required' => array(
					'error', 
					sprintf(
						__( '%s is required!' , WPRQ_TEXTDOMAIN ) , 
						__( 'Privacity' , WPRQ_TEXTDOMAIN ) 
					)
				)
			));

			$form->add('note', 'note_privacity_1', 'privacity', $note_privacity_1);
			$form->add('note', 'note_privacity_2', 'privacity', $note_privacity_2);
			$form->add('note', 'note_privacity_3', 'privacity', $note_privacity_3);

			## Upload avatar character

			$form->add('label', 'label_upload_avatar', 'upload_avatar', __( 'Avatar character' , WPRQ_TEXTDOMAIN) );

			$obj = $form->add('file', 'upload_avatar');

			$obj->set_rule(array(
				'upload' => array(
					WPRQ_UPLOADS_DIR . 'characters/avatars/', 
					ZEBRA_FORM_UPLOAD_RANDOM_NAMES, 
					'error',
					sprintf(
						__( 'Could not upload file!<br>Check that the "%s" folder exists and that it is writable' , WPRQ_TEXTDOMAIN ), 
						WPRQ_UPLOADS_DIR . 'characters/avatars/'
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

			## Upload PDF character

			$form->add('label', 'label_upload_pdf', 'upload_pdf', __( 'Upload new version' , WPRQ_TEXTDOMAIN) );

			$obj = $form->add('file', 'upload_pdf');

			$obj->set_rule(array(
				'upload' => array(
					WPRQ_UPLOADS_DIR . 'characters/pdfs/', 
					ZEBRA_FORM_UPLOAD_RANDOM_NAMES, 
					'error',
					sprintf(
						__( 'Could not upload file!<br>Check that the "%s" folder exists and that it is writable' , WPRQ_TEXTDOMAIN ), 
						WPRQ_UPLOADS_DIR . 'characters/pdfs/'
					)
				),
				'filetype' => array(
					'pdf',
					'error',
					sprintf(
						__( 'Only could upload files with extension: %s' , WPRQ_TEXTDOMAIN ), 
						'<code>.pdf</code>'
					),
				)
			));

			## Terms + Licence

			$form->add('label', 'label_terms', 'accept_yes', __( 'Terms', WPRQ_TEXTDOMAIN));

			$obj = $form->add('checkbox', 'accept', 'yes', array(
				'checked' => 'checked',
				'disabled' => 'disabled'
			));

			$note_licence = __( 'By checking this checkbox, confirmed that he will agree that the content under license Creative Commons By-SA 3.0' , WPRQ_TEXTDOMAIN );

			$form->add('note', 'note_accept_yes', 'accept_yes', $note_licence);

			$form->add('label', 'label_accept_yes', 'accept_yes', '');

			## Submit

			$form->add('submit', 'btnsubmit', __( 'Update Character', WPRQ_TEXTDOMAIN ), array(
				'class' => 'button button-primary',

			));

			## Validate the form

			if ($form->validate()) {

				## Resize and Save Avatar 

				if ( isset($form->file_upload["upload_avatar"]) ) {

					## Delete old Avatar

					if ( $character->character_avatar != '' ) {
						unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/' . $character->character_avatar);
						unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/64-' . $character->character_avatar);
						unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/128-' . $character->character_avatar);
						unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/256-' . $character->character_avatar);
						unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/512-' . $character->character_avatar);
					}

					## Save new Avatar

					$data1["character_avatar"] = $form->file_upload["upload_avatar"]["file_name"];
					$format1[] = '%s';

					$source_image = $form->file_upload["upload_avatar"]["path"] . $form->file_upload["upload_avatar"]["file_name"];

					$sizes[] = 64;
					$sizes[] = 128;
					$sizes[] = 256;
					$sizes[] = 512;

					foreach ($sizes as $key => $size) {
						$image = wp_get_image_editor( $source_image );
						$image->resize( $size, $size, false );
						$image->save( $form->file_upload["upload_avatar"]["path"] . $size . '-' . $form->file_upload["upload_avatar"]["file_name"] );
					}

				}

				# Update Character

				$table_characters = $wpdb->prefix . WPRQ_NAME . '_characters';

				$data_char["character_name"] = $_POST["name"];
				$format_char[] = '%s';

				$data_char["character_description"] = $_POST["description"];
				$format_char[] = '%s';

				$data_char["character_privacity"] = $_POST["privacity"];
				$format_char[] = '%s';

				if ( isset($form->file_upload["upload_avatar"]) ) {
					$data_char["character_avatar"] = $form->file_upload["upload_avatar"]["file_name"];
					$format_char[] = '%s';
				}

				$where_char["character_id"] = $_GET["character"];
				$where_format_char[] = '%d';

				$update_char = $wpdb->update( $table_characters, $data_char, $where_char, $format_char, $where_format_char );

				# Insert PDF

				if ( isset($form->file_upload["upload_pdf"]) ) {

					$table_uploads = $wpdb->prefix . WPRQ_NAME . '_characters_uploads';

					$data_pdf["upload_character"] = $_GET["character"];
					$format_pdf[] = '%d';

					$data_pdf["upload_author"] = get_current_user_id();
					$format_pdf[] = '%d';

					$data_pdf["upload_type"] = $form->file_upload["upload_pdf"]["type"];
					$format_pdf[] = '%s';

					$data_pdf["upload_size"] = $form->file_upload["upload_pdf"]["size"];
					$format_pdf[] = '%d';

					$data_pdf["upload_file_name"] = $form->file_upload["upload_pdf"]["file_name"];
					$format_pdf[] = '%s';

					$insert_pdf = $wpdb->insert( $table_uploads, $data_pdf, $format_pdf );

				} else {

					$insert_pdf = null;

				}

				# Message

				if ( $insert_pdf === false ) {
					$form->add_error('error', __( "<strong>Oh snap!</strong> We couldn't insert new File PDF in database.", WPRQ_TEXTDOMAIN) );

				} elseif ( $update_char === false ) {
					$form->add_error('error', __( "<strong>Oh snap!</strong> We couldn't update the information in database.", WPRQ_TEXTDOMAIN) );

				} else {
					$show_list_characters = true;
					$msg["type"]	= 'success';
					$msg["message"] = __( "<strong>Well done!</strong> The character was saved correctly.", WPRQ_TEXTDOMAIN );
				}

			}

			if ( $show_list_characters == false ) {

				?>

				<div class="wrap">
				
					<div class="icon32 icon32-main"><br></div>
					
					<h2>
						<?php echo sprintf( '%s %s', __( 'Edit' , WPRQ_TEXTDOMAIN ), __( 'Character' , WPRQ_TEXTDOMAIN ) ); ?>
						<?php echo '<p>' . __( 'Modify a character.' , WPRQ_TEXTDOMAIN ) . '</p>'; ?>
					</h2>
					
					<?php

					$table_pdf = $wpdb->prefix . WPRQ_NAME . '_characters_uploads';
					
					$pdfs = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM " . $table_pdf . " WHERE upload_character = '%d' ORDER BY upload_date_uploaded ASC",
							$_GET["character"]
						)
					);

					$form->render( WPRQ_PATH . 'tpls/form-edit-character.php', false, array(
						'pdfs' => $pdfs,
						'avatar' => $character->character_avatar
					));

					?>

				</div>

				<?php

			}
		}

	}

}

## DELETE MAP

if ( isset($_GET["action"]) AND ($_GET["action"] == 'delete') AND isset($_GET["character"]) ) {

	$show_list_characters = true;
	
	global $wpdb;

	$table = $wpdb->prefix . WPRQ_NAME . '_characters';
	
	$character = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . $table . " WHERE character_id = '%d'",
			$_GET["character"]
		)
	);

	if ( $character === null ) {

		$msg["type"]	= 'error';
		$msg["message"] = sprintf(
			__( "<strong>Oh snap!</strong> The character with ID = %d wasn't in database.", WPRQ_TEXTDOMAIN ),
			$_GET["character"]
		);

	} else {

		# Character is Public

		if ( ( $character->character_privacity == 'public-close' ) OR ( $character->character_privacity == 'public-open' ) ) {

			$msg["type"]	= 'warning';
			$msg["message"] = __( "<strong>Oh snap!</strong> The character no longer belongs to you, it belongs to the community, so if you want to delete communicate to the Administrator.", WPRQ_TEXTDOMAIN );

		} elseif ( $character->character_author != get_current_user_id() ) {

			$msg["type"]	= 'warning';
			$msg["message"] = __( "<strong>Oh snap!</strong> Is not your character, you can't delete.", WPRQ_TEXTDOMAIN );

		} else {

			## Select PDFs

			$table_pdf = $wpdb->prefix . WPRQ_NAME . '_characters_uploads';

			$pdfs = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM " . $table_pdf . " WHERE upload_character = '%d' ORDER BY upload_date_uploaded ASC",
					$_GET["character"]
				)
			);

			## Delete PDFs

			foreach ($pdfs as $key => $pdf) {
				unlink( WPRQ_UPLOADS_DIR . 'characters/pdfs/' . $pdf->upload_file_name);
			}

			## Delte PDF from DB

			$where_pdf["upload_character"] = $_GET["character"];
			$where_format_pdf[] = '%d';

			$delete_pdfs = $wpdb->delete(
				$table_pdf,
				$where_pdf,
				$where_format_pdf
			);

			## Delete Avatar (file)

			if ( $character->character_avatar != '' ) {
				unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/' . $character->character_avatar);
				unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/64-' . $character->character_avatar);
				unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/128-' . $character->character_avatar);
				unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/256-' . $character->character_avatar);
				unlink( WPRQ_UPLOADS_DIR . 'characters/avatars/512-' . $character->character_avatar);
			}

			## Delete Character from DB

			$table_char = $wpdb->prefix . WPRQ_NAME . '_characters';

			$where_char["character_id"] = $_GET["character"];
			$where_format_char[] = '%d';

			$delete_char = $wpdb->delete(
				$table_char,
				$where_char,
				$where_format_char
			);

			$show_list_characters = true;
			$msg["type"]	= 'success';
			$msg["message"] = __( '<strong>Well done!</strong> The character was deleted correctly.', WPRQ_TEXTDOMAIN );

		}

	}
}

## SHOW LIST CHARACTERS

if ( $show_list_characters == true ) {

	include_once( plugin_dir_path(__FILE__) . 'classes/WP_List_Table_Characters.php' );

	// Prepare Table of elements
	$list_table_characters = new WP_List_Table_Characters();
	$list_table_characters->prepare_items();

	$button_add_character = sprintf('<a href="?page=%s&action=%s" class="add-new-h2">%s</a>',
		$_REQUEST['page'],
		'add',
		__( 'Add New' , WPRQ_TEXTDOMAIN )
	);

?>

<div class="wrap">
	
	<div class="icon32 icon32-main"><br></div>
	
	<h2>
		<?php _e( 'My characters', WPRQ_TEXTDOMAIN ); ?>
		<?php echo $button_add_character; ?>
	</h2>

	<?php

	if ( !empty($msg) )
		wprq_message_response($msg["type"], $msg["message"]);

	$list_table_characters->display();

	?>

</div>

<?php

}

?>