<section id="list-of-photos">
<?php require('index_phtml.php');?>
</section>
<form enctype="multipart/form-data" target="upload_target" method="post" id="media_form" action="<?php echo FrontController::urlFor('photos');?>">
	<fieldset>
		<legend>Photo Picker</legend>
		<input type="hidden" name="MAX_FILE_SIZE" value="{$max_filesize}" />
		<section>
			<label for="photo" id="photo_label">Upload a photo</label>
			<input type="file" name="photo" id="photo" />
		</section>
		<iframe src="<?php echo FrontController::urlFor('empty');?>" id="upload_target" name="upload_target" style="width:0;height:0;border:none;"></iframe>
	</fieldset>
</form>
<dl id="photos"></dl>
<script type="text/javascript">
</script>