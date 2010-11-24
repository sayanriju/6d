<section class="vcard">
	<div class="photo">
		<div class="canvas">
			<img class="photo" src="<?php echo ProfileResource::getPhotoUrl($person);?>" alt="photo of {$person->name}" id="profile_photo" />
		</div>
		
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
<?php if(AuthController::is_authorized() && Application::$current_user->person_id === Application::$member->person_id):?>
	<form action="<?php echo Application::url_with_member('profile');?>" method="post">
		<input type="hidden" name="state" value="edit" />
		<button type="submit"><span>Edit your profile</span></button>
	</form>
<?php endif;?>

</section>
