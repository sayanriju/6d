<style type="text/css">
	div.canvas{
		width: 120px;
		height: 120px;
		overflow: hidden;
		position: relative;
		top: 0;
		left: 0;
	}
	.vcard img{
		position: absolute;
		top: 0;
		left: 0;
	}
	
</style>
<section class="vcard">
	<div class="canvas">
		<img src="<?php echo ProfileResource::getPhotoUrl($person);?>" alt="photo of {$person->name}" class="photo" id="profile_photo" />
	</div>
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
	var current_position;
	var canvas;
	var xdiff, ydiff;
	var photo_size;
	var canvas_position;
	function init(){
		photo = SDDom('profile_photo');
		canvas = SDDom.findFirst('.canvas');
		canvas_position = SDDom.getPosition(canvas, window);
		original_size = {width: SDDom.getWidth(canvas), height: SDDom.getHeight(canvas)};
		photo_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		SDDom.addEventListener(canvas, 'mousedown', didMouseDown);
		SDDom.addEventListener(canvas, 'mouseup', didMouseUp);
		SDDom.addEventListener(canvas, 'mouseout', didMouseUp);
		SDDom.addEventListener(canvas, 'dblclick', didDoubleClick);
		user_message = SDDom('user_message');
		SDDom.show(user_message);
	}
	function photoDidSave(request){
		alert(request.responseText);
	}
	function didDoubleClick(e){
		var new_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		var pos = SDDom.getPosition(photo);
		var offset = {x: canvas_position.x - pos.x, y: canvas_position.y - pos.y, ratio: new_size.width/photo_size.width};
		(new SDAjax({method: 'put', parameters: ['ratio=' + offset.ratio, 'x=' + offset.x, 'y=' + offset.y, 'file_name=' + photo.src].join('&'), DONE: [window, photoDidSave]})).send('<?php echo FrontController::urlFor('photo.phtml');?>');
		
	}
	function resize(photo, position){
		var current_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		var diff = position.x - start_position.x;
		SDDom.setStyles({width: diff + 'px'}, photo);
	}
	function move(photo, position, diff){
		SDDom.setStyles({left: (position.x - diff.x) + 'px', top: (position.y - diff.y) + 'px'}, photo);
	}
	function didMouseMove(e){
		var position = {x: SDDom.pageX(e), y: SDDom.pageY(e)};
		if(e.shiftKey){
			resize(photo, position);
		}else{
			move(photo, position, {x: xdiff, y:ydiff});
		}		
	}
	function didMouseDown(e){
		var position = {x: SDDom.pageX(e), y: SDDom.pageY(e)};		
		var photo_position = SDDom.getPosition(photo);
		xdiff = position.x - photo_position.x + canvas_position.x;
		ydiff = position.y - photo_position.y + canvas_position.y;
		start_position = {x: position.x - original_size.width, y: position.y - original_size.height};
		SDDom.stop(e);
		SDDom.addEventListener(canvas, 'mousemove', didMouseMove);
	}
	function didMouseUp(e){
		SDDom.removeEventListener(canvas, 'mousemove', didMouseMove);
		original_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
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
