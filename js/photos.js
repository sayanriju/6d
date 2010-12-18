var photo = null;
SDDom.addEventListener(window, 'load', function(e){
	photo = SDDom('photo_upload_field');
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
function photoWasUploaded(response){
	photoDidUpload(response);
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
		html += '<dd><form action="' + SDObject.rootUrl + 'photo/" method="post" class="delete"><img src="' + response[i].little_src + '" width="' + response[i].width + '" /><input name="src" value="' + response[i].src + '" type="hidden" /><input name="_method" type="hidden" value="delete" /><button type="submit">Delete</button></form></dd>';
	}
	html += '</dl>';
	SDDom('list-of-photos').innerHTML = html;
}

function photoDidUpload(response){
	if(response.message.length > 0){
		alert(response.message);
	}else{
		SDDom('photo_upload_field').value = null;
		var dd = SDDom.create('dd');
		dd.innerHTML = response.photo_name;
		var items = SDDom.findAll('#photos dd');
		var count = 0;
		if(items && items.length > 0){
			count = items.length;
		}
		var hidden_field = SDDom.create('input', {"type":"hidden", "value":response.photo_name + '=' + response.file_name, "id":"photo_names[" + response.photo_name + "]", "name":"photo_names[]"});
		SDDom.append(SDDom('photos'), dd);
		(new SDAjax({method: 'get', DONE: [top, photosDidLoad]})).send(SDDom('media_form').action.replace('photos', 'photos.json'));
	}
}
