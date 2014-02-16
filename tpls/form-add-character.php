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
			<th scope="row"><?php echo $label_upload_avatar; ?></th>
			<td><?php echo $upload_avatar; ?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_upload_pdf; ?></th>
			<td><?php echo $upload_pdf; ?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_terms; ?></th>
			<td><?php echo $accept_yes . $note_accept_yes; ?></td>
		</tr>
	</tbody>
</table>
<p class="submit"><?php echo $btnsubmit; ?></p>