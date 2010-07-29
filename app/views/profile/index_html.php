<section class="vcard">
	<img src="<?php echo ProfileResource::getPhotoUrl($person);?>" alt="photo of {$person->name}" class="photo" id="profile_photo" />
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

<?php if($person->profile->site_name !== null):?>
		<p class="fn org"><?php echo $person->profile->site_name;?></p>
<?php endif;?>
	</address>
</section>
<?php if(AuthController::isAuthorized() && $this->current_user->person_id === $this->site_member->person_id):?>
<a href="<?php echo FrontController::urlFor('profile', array('state'=>'modify'));?>" id="edit_link">edit</a>
<section style="display: none;">
	<?php require('edit_html.php');?>
</section>
<form enctype="multipart/form-data" target="upload_target" method="post" id="media_form" action="<?php echo FrontController::urlFor('photos');?>">
	<fieldset>
		<legend>Photo Picker</legend>
		<input type="hidden" name="MAX_FILE_SIZE" value="{$max_filesize}" />
		<section>
			<label for="photo" id="photo_label">Add a photo</label>
			<input type="file" name="photo" id="photo" />
		</section>
		<iframe src="<?php echo FrontController::urlFor('empty');?>" id="upload_target" name="upload_target" style="width:10;height:10;border:none;"></iframe>
	</fieldset>
</form>
<dl id="photos"></dl>
<script type="text/javascript">
	SDDom.addEventListener(window, 'load', function(e){
		SDDom.addEventListener(SDDom('photo'), 'change', photoDidChange);
		init();
	});
	var photo;
	var original_size;
	var start_position;
	var user_message;
	function init(){
		photo = SDDom('profile_photo');
		original_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		SDDom.addEventListener(photo, 'mousedown', didMouseDown);
		SDDom.addEventListener(photo, 'mouseup', didMouseUp);
		SDDom.addEventListener(photo, 'mouseout', didMouseUp);
		SDDom.addEventListener(photo, 'dblclick', didDoubleClick);
		user_message = SDDom('user_message');
		SDDom.show(user_message);
		user_message.innerHTML = original_size.width;
	}
	function didDoubleClick(e){
		alert(e);
	}
	function resize(photo, position){
		var current_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		var xdiff = position.x - start_position.x;
		user_message.innerHTML = [position.x, position.y] + ', ' + xdiff;
		SDDom.setStyles({width: xdiff + 'px'}, photo);
	}
	function didMouseMove(e){
		var position = {x:SDDom.pageX(e), y:SDDom.pageY(e)};
		resize(photo, position);
		previous_mouse_position = position;
	}
	function didMouseDown(e){
		start_position = {x: SDDom.pageX(e) - original_size.width, y: SDDom.pageY(e) - original_size.height};
		SDDom.stop(e);
		SDDom.addEventListener(photo, 'mousemove', didMouseMove);
	}
	function didMouseUp(e){
		SDDom.removeEventListener(photo, 'mousemove', didMouseMove);
		original_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		start_position = null;
	}
	function photoWasUploaded(photo_name, file_name, photo_path, width){
		photoDidUpload(photo_name, file_name, photo_path, width);
	}

	function photoDidChange(e){
		SDDom('media_form').submit();
	}
	
	function profileDidSave(request){
		SDDom('user_message').innerHTML = request.responseText;
	};

	function photoDidUpload(photo_name, file_name, photo_path, width, error_message){
		if(error_message.length > 0){
			alert(error_message);
		}else{
			SDDom('profile_photo').src = photo_path;
			var profile_form = SDDom('person_form');
			SDDom('photo_url').value = photo_path;
			(new SDAjax({method: 'put', parameters: SDDom.toQueryString(profile_form), DONE: [window, profileDidSave]})).send(profile_form.action + '.json');
		}
	};
</script>
<?php endif;?>
