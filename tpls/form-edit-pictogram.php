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
			<th scope="row"><?php echo $label_title; ?></th>
			<td><?php echo $title; ?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_slug; ?></th>
			<td><?php echo $slug; ?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_upload_pictogram; ?></th>
			<td><?php echo '<img src="' . WPRQ_URL_UPLOADS_DIR . 'pictograms/' . $pictogram . '" /><br>' . $upload_pictogram; ?></td>
		</tr>
	</tbody>
</table>
<p class="submit"><?php echo $btnsubmit; ?></p>