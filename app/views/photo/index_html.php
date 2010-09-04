<section id="list-of-photos">
	<?php for($i=0; $i < count($photos); $i++):?>
		<?php $image = $photos[$i];?>
		<?php if(strpos($image->src, 'http://') === false):?>
	<div class="inline">
		<h3><?php echo $image->title;?></h3>
		<img class="manipulate" src="<?php echo PhotoResource::getLittleSrc($image->src);?>" width="<?php echo PhotoResource::getThumbnailWidth($image->src);?>" />
		<?php if(AuthController::isAuthorized() && $this->current_user->person_id == $this->site_member->person_id):?>
		<form method="post" action="<?php echo FrontController::urlFor('photo');?>">
			<input type="hidden" value="delete" name="_method" />
			<input type="hidden" value="<?php echo $image->src;?>" name="src" />
			<button type="submit"><span>Delete</span></button>
		</form>
		<?php endif;?>
	</div>
		<?php endif;?>
	<?php endfor;?>
</section>
<?php if(AuthController::isAuthorized() && $this->current_user->person_id == $this->site_member->person_id):?>
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
<?php endif;?>
