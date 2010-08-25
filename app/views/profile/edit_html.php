<section class="vcard">
	<div class="photo">
		<div class="canvas">
			<img src="<?php echo ProfileResource::getPhotoUrl($person);?>" alt="photo of {$person->name}" id="profile_photo" />
		</div>
		<div id="resizer"></div>
<?php if(AuthController::isAuthorized() && $this->current_user->person_id == $person->person_id && $this->getState() === 'edit'):?>
			<form method="post" action="<?php echo FrontController::urlFor('photo');?>" class="delete">
				<input type="hidden" value="delete" name="_method" />
				<input type="hidden" value="<?php echo $person->profile->photo_url;?>" name="src" />
				<button type="submit"><span>Delete</span></button>
			</form>
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
		</div>
<?php endif;?>
	<div class="contact">

		<form action="<?php echo FrontController::urlFor('profile');?>" method="post" id="person_form">
			<fieldset>
				<legend>{$person->name}</legend>
				<input type="hidden" value="<?php echo $person->profile->photo_url;?>" name="profile[photo_url]" id="profile[photo_url]" />
		        <p class="fn name">
					<label for="name">Name</label>
					<input type="text" id="name" name="name" value="{$person->name}" />
				</p>
		
				<p class="email">
					<label for="email">Email</label>
					<input type="email" id="email" name="email" value="{$person->email}" />
				</p>
				<p>
					<label for="url">Url</label>
					<input name="url" id="url" value="{$person->url}" />
				</p>
				<p>
					<label for="profile[site_name]">Site Name</label>
					<textarea name="profile[site_name]" id="profile[site_name]" cols="20" rows="5">
						<?php echo $person->profile->site_name;?>
					</textarea>
				</p>
				<p>
					<label for="profile[site_description]">Site Description</label>
					<textarea name="profile[site_description]" id="profile[site_description]" cols="20" rows="5">
						<?php echo $person->profile->site_description;?>
					</textarea>
				</p>
				<p>
					<label for="do_list_in_directory">List in directory?</label>
					<input type="checkbox" value="true" name="do_list_in_directory" id="do_list_in_directory"<?php echo $person->do_list_in_directory ? 'checked="true"' : null;?> />
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
	
				<p>
					<button type="submit" name="save_button" id="save_button"><span>Save</span></button>
				</p>
				
				<input type="hidden" id="id" name="id" value="{$person->id}" />
				<input type="hidden" name="_method" value="put" />
			</fieldset>
		</form>
	</div>
</section>