<?php require('index_phtml.php');?>
<form enctype="multipart/form-data" target="upload_target" method="post" id="media_form" action="<?php echo FrontController::urlFor('photos');?>">
	<fieldset>
		<legend>Media</legend>
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
	});
	var photo = null;
	function didMouseUp(e){
		photo = null;
	}
	function didMouseDown(e){
		photo = e.target;
	}
	function didMouseMove(e){
		if(photo !== null){
			SDDom.setStyles({left: SDDom.pageX(e) + 'px', top:SDDom.pageY(e) + 'px'}, elem);
		}
	}
	function photoWasUploaded(photo_name, file_name, photo_path, width){
		photoDidUpload(photo_name, file_name, photo_path, width);
	}

	function photoDidChange(e){
		if(SDDom('photo_names[' + this.value + ']')){
			alert("you've already added that photo.");
			SDDom.stop(e);
		}else{
			SDDom('media_form').submit();
		}
	}
	function photosDidLoad(request){
		SDDom('list-of-photos').innerHTML = request.responseText;
	};
	
	function photoDidUpload(photo_name, file_name, photo_path, width, error_message){
		if(error_message.length > 0){
			alert(error_message);
		}else{
			SDDom('photo').value = null;
			var dd = SDDom.create('dd');
			dd.innerHTML = photo_name;
			var items = SDDom.findAll('#photos dd');
			var count = 0;
			if(items && items.length > 0){
				count = items.length;
			}
			var hidden_field = SDDom.create('input', {"type":"hidden", "value":photo_name + '=' + file_name, "id":"photo_names[" + photo_name + "]", "name":"photo_names[]"});
			SDDom.append(SDDom('photos'), dd);
			(new SDAjax({method: 'get', DONE: [top, photosDidLoad]})).send(SDDom('media_form').action.replace('photos', 'photos.phtml'));
		}
	};
</script>