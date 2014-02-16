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
		<tr class="form-field">
			<th scope="row"><?php echo $label_description; ?></th>
			<td><?php echo $description; ?></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php echo $label_folder_map; ?></th>
			<td><?php echo $folder_map; ?></td>
		</tr>
	</tbody>
</table>
<p class="submit"><?php echo $btnsubmit; ?></p>