<section class="vcard">
	<img src="<?php echo ProfileResource::getPhotoUrl($person);?>" alt="photo of <?php echo $person->name;?>" id="profile_photo" class="photo" />
<?php if(AuthController::is_authorized() && Application::$current_user->person_id == $person->id && $this->getState() === 'edit'):?>
	<a id="change_photo_link" href="<?php echo Application::url_with_member('photos');?>" title="Change your profile photo">change photo</a>
<?php endif;?>
	<div class="contact">
		<form action="<?php echo Application::url_with_member('profile');?>" method="post" id="person_form">
			<fieldset class="personal info">
				<legend>Personal Info</legend>
				<input type="hidden" value="<?php echo $person->profile->photo_url;?>" name="profile[photo_url]" id="profile[photo_url]" />
		        <p class="fn name">
					<label for="name">Name</label>
					<input type="text" id="name" name="name" value="<?php echo $person->name;?>" />
				</p>
		
				<p class="email">
					<label for="email">Email</label>
					<input type="email" id="email" name="email" value="<?php echo $person->email;?>" />
				</p>
				<p>
					<label for="url">Url</label>
					<input name="url" id="url" value="<?php echo $person->url;?>" />
				</p>
				<p>
					<label for="password">Password</label>
					<input type="password" id="password" name="password" value="" />
				</p>
				<address class="adr">
					<p>
						<label for="address">Address</label>
						<input type="text" id="profile[address]" name="profile[address]" value="<?php echo $person->profile->address;?>" />
					</p>
					<p>
						<label for="city">City</label>
						<input type="text" id="profile[city]" name="profile[city]" value="<?php echo $person->profile->city;?>" />
					</p>

					<p>
						<label for="profile[state]">State</label>
						<input type="text" id="profile[state]" name="profile[state]" value="<?php echo $person->profile->state;?>" />
					</p>

					<p>
						<label for="profile[zip]">Zip</label>
						<input type="text" id="profile[zip]" name="profile[zip]" value="<?php echo $person->profile->zip;?>" />
					</p>

					<p>
						<label for="profile[country]">Country</label>
						<input type="text" id="profile[country]" name="profile[country]" value="<?php echo $person->profile->country;?>" />
					</p>
				</address>				
			</fieldset>
			<fieldset class="site info">
				<legend>Site Info</legend>
				<p>
					<label for="profile[site_name]">Site Name</label>
					<input name="profile[site_name]" id="profile[site_name]" value="<?php echo $person->profile->site_name;?>" />
				</p>
				<p>
					<label for="profile[site_description]">Site Description</label>
					<textarea name="profile[site_description]" id="profile[site_description]" cols="20" rows="5"><?php echo $person->profile->site_description;?></textarea>
				</p>
				<p>
					<label for="do_list_in_directory">List in directory?</label>
					<input type="checkbox" value="true" name="do_list_in_directory" id="do_list_in_directory"<?php echo $person->do_list_in_directory ? 'checked="true"' : null;?> />
				</p>
			</fieldset>
			<fieldset class="toolbar">	
				<p>
					<button type="submit" name="save_button" id="save_button"><span>Save</span></button>
					<form action="<?php echo Application::url_with_member('profile');?>" method="post">
						<input type="hidden" value="show" name="state" />
						<button type="submit" name="cancel" id="cancel_button"><span>Cancel</span></button>
					</form>
				</p>
				
				<input type="hidden" id="id" name="id" value="<?php echo $person->id;?>" />
				<input type="hidden" name="_method" value="put" />
			</fieldset>
		</form>
	</div>
</section>