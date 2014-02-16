<?php
/**
 * @package wp-runequesters
 */
?>

<?php 
if (isset($zf_error)) {
	echo $zf_error;
} elseif (isset($error)) {
	echo $error;
} elseif (isset($update)) {
	echo $update;
}
?>

<table class="form-table">
	<tbody>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_name; ?></th>
			<td><?php echo $name; ?></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><?php echo $label_description; ?></th>
			<td><?php echo $description; ?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_upload_avatar; ?></th>
			<td>
			<?php
			echo '<img src="';
			if (empty($avatar)) {
				echo WPRQ_URL . 'assets/img/character-unknown.png';
			} else {
				echo WPRQ_URL_UPLOADS_DIR . 'characters/avatars/128-' . $avatar;
			}
			echo '" height="128" /><br>' . $upload_avatar;
			?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_privacity; ?></th>
			<td><?php echo $privacity; ?></td>
		</tr>
		<tr class="form-field form-note">
			<td colspan="2">
				<div class="note_privacity"><?php echo $note_privacity_1; ?></div>
				<div class="note_privacity"><?php echo $note_privacity_2; ?></div>
				<div class="note_privacity"><?php echo $note_privacity_3; ?></div>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_terms; ?></th>
			<td><?php echo $accept_yes . $note_accept_yes; ?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_upload_pdf; ?></th>
			<td><?php echo $upload_pdf; ?></td>
		</tr>
	</tbody>
</table>

<table class="wp-list-table widefat fixed characters">
	<thead>
		<tr class="form-field">
			<th scope="col"><?php echo __( 'Version', WPRQ_TEXTDOMAIN ); ?></th>
			<th scope="col"><?php echo __( 'Author', WPRQ_TEXTDOMAIN ); ?></th>
			<th scope="col"><?php echo __( 'Size', WPRQ_TEXTDOMAIN ); ?></th>
			<th scope="col"><?php echo __( 'Date Uploaded', WPRQ_TEXTDOMAIN ); ?></th>
			<th scope="col"><?php echo __( 'File', WPRQ_TEXTDOMAIN ); ?></th>
		</tr>
	</thead>
	<tbody>
	
	<?php
	
	foreach ($pdfs as $key => $pdf) {

		$user = get_userdata($pdf->upload_author);

		?>

		<tr class="form-field form-required">
			<th scope="row"><?php echo __( 'Character', WPRQ_TEXTDOMAIN ) . ' v' . ($key+1); ?></th>
			<td><?php echo $user->data->display_name; ?></td>
			<td>
			<?php 
				if ( $pdf->upload_size < 1024 ) {
					echo $pdf->upload_size . ' B';
				} elseif ( ($pdf->upload_size/1024) < 1024 ) {
					$size = $pdf->upload_size/1024;
					echo number_format($size, 2) . ' KB';
				} else {
					$size = $pdf->upload_size/1024/1024;
					echo number_format($size, 2) . ' MB';
				}
				?>
			</td>
			<td><?php echo date("d/m/Y H:i", strtotime($pdf->upload_date_uploaded)); ?></td>
			<td><?php echo '<a class="button" target="_blank" href="' . WPRQ_URL_UPLOADS_DIR_CHARACTERS . $pdf->upload_file_name . '">' . __( 'Download', WPRQ_TEXTDOMAIN ) . '</a>'; ?></td>
		</tr>

		<?php

	}

	?>

	</tbody>
</table>

<p class="submit"><?php echo $btnsubmit; ?></p>

