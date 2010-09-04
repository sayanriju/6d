var photo = null;
SDDom.addEventListener(window, 'load', function(e){
	photo = SDDom('photo');
	if(photo){
		SDDom.addEventListener(photo, 'change', photoDidChange);
	}
});

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
	var response = JSON.parse(request.responseText);
	var html = '<dl>';
	for(var i = 0; i < response.length; i++){
		html += '<dt>' + response[i].title + '</dt>';
		html += '<dd><img src="' + response[i].little_src + '" width="' + response[i].width + '" /></dd>';
	}
	html += '</dl>';
	SDDom('list-of-photos').innerHTML = html;
}

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
		(new SDAjax({method: 'get', DONE: [top, photosDidLoad]})).send(SDDom('media_form').action.replace('photos', 'photos.json'));
	}
}
