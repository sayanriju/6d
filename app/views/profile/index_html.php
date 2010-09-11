<section class="vcard">
	<div class="photo">
		<div class="canvas">
			<img class="photo" src="<?php echo ProfileResource::getPhotoUrl($person);?>" alt="photo of {$person->name}" id="profile_photo" />
		</div>
		
<?php if(AuthController::isAuthorized() && Application::$current_user->person_id == $person->id && $this->getState() === 'edit'):?>
		<form method="post" action="<?php echo FrontController::urlFor('photo');?>" class="delete">
			<input type="hidden" value="delete" name="_method" />
			<input type="hidden" value="<?php echo $person->profile->photo_url;?>" name="src" />
			<button type="submit"><span>Delete</span></button>
		</form>

		<form enctype="multipart/form-data" target="upload_target" method="post" id="media_form" action="<?php echo FrontController::urlFor('photos');?>">
			<fieldset>
				<legend>Photo Picker</legend>
				<input type="hidden" name="MAX_FILE_SIZE" value="{$max_filesize}" />
				<label for="photo" id="photo_label">Upload a photo</label>
				<input type="file" name="photo" id="photo" />
				<iframe src="<?php echo FrontController::urlFor('empty');?>" id="upload_target" name="upload_target" style="width:0;height:0;border:none;"></iframe>
			</fieldset>
		</form>
		<dl id="photos"></dl>
<?php endif;?>
	</div>
	<div class="contact">
		<span class="fn name">{$person->name}</span>
<?php if($person->email !== null):?>
		<a class="email" href="mailto:{$person->email}">{$person->email}</a>
<?php endif;?>
		<address class="adr">
<?php if($person->profile->address !== null):?>
			<p class="street-address"><?php echo $person->profile->address;?></p>
<?php endif;?>

<?php if($person->profile->city !== null):?>
			<p class="locality"><?php echo $person->profile->city;?></p>
<?php endif;?>

<?php if($person->profile->state !== null):?>
			<p class="region"><?php echo $person->profile->state;?></p>
<?php endif;?>

<?php if($person->profile->zip !== null):?>
			<p class="postal-code"><?php echo $person->profile->zip;?></p>
<?php endif;?>

<?php if($person->profile->country !== null):?>
			<p class="country-name"><?php echo $person->profile->country;?></p>
<?php endif;?>
		</address>

<?php if($person->profile->site_name !== null):?>
		<p class="fn org"><?php echo $person->profile->site_name;?></p>
<?php endif;?>
	</div>
<?php if(AuthController::isAuthorized() && Application::$current_user->person_id === Application::$member->person_id):?>
	<form action="<?php echo FrontController::urlFor('profile');?>" method="post">
		<input type="hidden" name="state" value="edit" />
		<button type="submit"><span>Edit your profile</span></button>
	</form>
<?php endif;?>

</section>
