var photo;
var user_message;
var cropper;
var slider;
var profile_controller;
var photo_viewer;

function sixd(){}
sixd.outline = function(fn){
	SDDom.addEventListener(window, 'load', fn);
}

sixd.outline(function(e){
	user_message = get_user_message();		
	photo = get_photo();
	slider = get_slider();
	cropper = get_cropper();	
	create_photo_viewer();	
	add_change_photo_link();	
	save_photo_when_saving_profile();
});

function get_cropper(){
	return UIView.Cropper(null, {canvases: SDDom.findAll('.canvas'), delegate: window});
}
function get_slider(){
	var original_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
	return UIView.Slider('resizer', {direction: 'horizontal', delegate: {sliderIsMoving: function(percent){SDDom.setStyles({width: ((percent.x * original_size.width) + original_size.width) + 'px'}, photo);}}});
}
function get_user_message(){
	return SDDom('user_message');
}
function get_photo(){
	return SDDom('profile_photo');
}
function create_photo_viewer(){
	photo_viewer = new UIView.PhotoViewer('photo_viewer', {delegate: window, title: 'Photo Picker'});
	photo_viewer.toggle();
}
function add_change_photo_link(){
	var change_photo_link = SDDom('change_photo_link');
	if(change_photo_link){
		change_photo_link = SDDom.addEventListener(change_photo_link, 'click', SDObject.decorateEventHandler(change_photo_was_clicked));
	}
	return change_photo_link;
}
function save_photo_when_saving_profile(){
	var save_button = SDDom('save_button');
	if(save_button){
		SDDom.addEventListener(save_button, 'click', SDObject.decorateEventHandler(function(e){
				SDDom.stop(e);
				window.photoDidSave = function(request){
					SDDom('person_form').submit();
				}
				var new_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
				var pos = SDDom.getPosition(photo);
				var canvas = SDDom.getParent('div', photo);
				var canvas_position = SDDom.getPosition(canvas);
				var canvas_size = {width: SDDom.getWidth(canvas), height: SDDom.getHeight(canvas)};
				var offset = {x: canvas_position.x - pos.x, y: canvas_position.y - pos.y, ratio: new_size.width/cropper.getCanvas(photo).original_size.width};
				photoWasDoubleClicked(photo, new_size, pos, offset, canvas_size);
			})
		);
	}
}
function change_photo_was_clicked(e){
	SDDom.stop(e);
	photo_viewer.refresh(e.target.href);
	photo_viewer.toggle();
	SDDom.setStyles({top: '0px', left: (SDDom.getWidth(document.body) - SDDom.getWidth(photo_viewer.container)/2) + 'px'}, photo_viewer.container);
}
function photoHasChanged(photo){
}
function profileDidSave(request){
	var response = JSON.parse(request.responseText);
	user_message.innerHTML += response.message;
	SDDom.show(user_message);
}
function photoWasDoubleClicked(photo, new_size, pos, offset, canvas_size){
	var dst_file_name = photo.src.replace(/media\/([a-zA-Z0-9]*)\/(.*)\.(\w)/, 'media/$1/profile.$3');
	SDDom('profile[photo_url]').value = dst_file_name;
	var src_file_name = photo.src;
	(new SDAjax({method: 'put', parameters: ['ratio=' + offset.ratio, 'offset_x=' + offset.x, 'offset_y=' + offset.y, 'dst_w=' + canvas_size.width, 'dst_h=' + canvas_size.height, 'src_file_name=' + src_file_name, 'dst_file_name=' + dst_file_name].join('&'), DONE: [window, photoDidSave]})).send(SDObject.rootUrl + 'photo.phtml');
}

function imageWasClicked(e){
	photo.src = e.target.src;
}

function photoDidUpload(response){
};

function photoDidSave(request){
	var response = JSON.parse(request.responseText);
	if(response.did_save == 'false'){
		user_message.innerHTML += response.message;
		SDDom.show(user_message);
	}
	if(slider.container){
		slider.reset();		
	}
	var profile_form = SDDom('person_form');
	(new SDAjax({method: 'put', parameters: SDDom.toQueryString(profile_form), DONE: [window, profileDidSave]})).send(profile_form.action + '.json');
}