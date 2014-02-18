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

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#title").keypress(function(e){
			jQuery("#slug").val( jQuery("#title").val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-') );
		});
	});
</script>

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
			<td><?php echo $upload_pictogram; ?></td>
		</tr>
	</tbody>
</table>
<p class="submit"><?php echo $btnsubmit; ?></p>