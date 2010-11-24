<section id="list-of-photos" class="scrollable">
	<ul>
	<?php for($i=0; $i < count($photos); $i++):?>
		<?php $image = $photos[$i];?>
		<li<?php echo strpos($image->src, 'profile') !== false ? ' class="profile"' : null;?>>
		<?php if(strpos($image->src, 'http://') === false):?>
			<h3><?php echo $image->title;?></h3>
			<img src="<?php echo PhotoResource::getLittleSrc($image->src);?>" />
		<?php if(AuthController::is_authorized() && Application::$current_user->person_id == Application::$member->person_id):?>
			<form method="post" action="<?php echo Application::url_with_member('photo');?>" class="delete">
				<input type="hidden" value="delete" name="_method" />
				<input type="hidden" value="<?php echo $image->src;?>" name="src" />
				<button type="submit">Delete</button>
			</form>
		<?php endif;?>
		<?php endif;?>
		</li>
	<?php endfor;?>
	</ul>
</section>
<?php if(AuthController::is_authorized() && Application::$current_user->person_id == Application::$member->person_id):?>
<form enctype="multipart/form-data" target="upload_target" method="post" id="media_form" name="media_form" action="<?php echo Application::url_with_member('photos');?>">
	<fieldset>
		<legend>Photo Picker</legend>
		<input type="hidden" name="MAX_FILE_SIZE" value="{$max_filesize}" />
		<section>
			<label for="photo" id="photo_label">Upload a photo</label>
			<input type="file" name="photo" id="photo_upload_field" />
		</section>
		<iframe src="<?php echo Application::url_with_member('empty');?>" id="upload_target" name="upload_target" style="width:0;height:0;border:none;"></iframe>
	</fieldset>
</form>
<?php endif;?>
