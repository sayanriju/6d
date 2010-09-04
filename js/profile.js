SDDom.addEventListener(window, 'load', function(e){
	init2();
	if(SDDom('photo')){
		SDDom.addEventListener(SDDom('photo'), 'change', photoDidChange);		
		SDDom.addEventListener(SDDom('save_button'), 'click', function(e){
			SDDom.stop(e);
			window.photoDidSave = function(request){
				SDDom('person_form').submit();
			}
			var new_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
			var pos = SDDom.getPosition(photo);
			var canvas = SDDom.getParent('div', photo);
			var canvas_position = SDDom.getPosition(canvas);
			var canvas_size = {width: SDDom.getWidth(canvas), height: SDDom.getHeight(canvas)};
			var view = cropper.canvases[0];
			var offset = {x: canvas_position.x - pos.x, y: canvas_position.y - pos.y, ratio: new_size.width/view.original_size.width};
			profile_controller.photoWasDoubleClicked(photo, new_size, pos, offset, canvas_size);
		});
	}
});
var photo;
var user_message;
var cropper;
var original_size;
var slider;
var profile_controller;
function init2(){
	photo = SDDom('profile_photo');	
	profile_controller = {photoWasDoubleClicked: function(photo, new_size, pos, offset, canvas_size){
		(new SDAjax({method: 'put', parameters: ['ratio=' + offset.ratio, 'offset_x=' + offset.x, 'offset_y=' + offset.y, 'dst_w=' + canvas_size.width, 'dst_h=' + canvas_size.height, 'file_name=' + photo.src].join('&'), DONE: [window, photoDidSave]})).send(SDObject.rootUrl + 'photo.phtml');
		}}
	slider = UIView.Slider('resizer', {direction: 'horizontal', delegate: {sliderIsMoving: function(percent){SDDom.setStyles({width: ((percent.x * original_size.width) + original_size.width) + 'px'}, photo);}}});
	
	cropper = UIView.Cropper(null, {canvases: SDDom.findAll('.canvas'), delegate: profile_controller});
	original_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
	user_message = SDDom('user_message');		
}

function photoWasUploaded(photo_name, file_name, photo_path, width){	
	photoDidUpload(photo_name, file_name, photo_path, width);
}

function photoDidChange(e){	
	SDDom('media_form').submit();
}

function profileDidSave(request){
	var response = JSON.parse(request.responseText);
	user_message.innerHTML = response.message;
	SDDom.show(user_message);
};
function photoDidUpload(photo_name, file_name, photo_path, width, error_message){
	if(error_message.length > 0){
		alert(error_message);
	}else{
		photo.src = photo_path;
		var profile_form = SDDom('person_form');
		SDDom('profile[photo_url]').value = photo_path;
		(new SDAjax({method: 'put', parameters: SDDom.toQueryString(profile_form), DONE: [window, profileDidSave]})).send(profile_form.action + '.json');
	}
};

function photoDidSave(request){
	slider.reset();
}