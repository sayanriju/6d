var photo;
var user_message;
var slider;
var profile_controller;
var photo_viewer;

sixd.main(function(e){
	user_message = get_user_message();		
	photo = get_photo();
	slider = get_slider();
	create_photo_viewer();	
	add_change_photo_link();	
	save_photo_when_saving_profile();
});

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
				photoWasDoubleClicked(photo, new_size);
			})
		);
	}
}
function change_photo_was_clicked(e){
	SDDom.stop(e);
	photo_viewer.refresh(e.target.href);
	photo_viewer.toggle();
	SDDom.setStyles({top: '20px', left: (SDDom.getWidth(document.body) - SDDom.getWidth(photo_viewer.container) - 30) + 'px'}, photo_viewer.container);
}
function photoHasChanged(photo){
}
function profileDidSave(request){
	var response = JSON.parse(request.responseText);
	user_message.innerHTML += response.message;
	SDDom.show(user_message);
}
function photoWasDoubleClicked(photo, new_size){
	var dst_file_name = photo.src.replace(/media\/([a-zA-Z0-9]*)\/(.*)\.(\w)/, 'media/$1/profile.$3');
	SDDom('profile[photo_url]').value = dst_file_name;
	var src_file_name = photo.src;
	(new SDAjax({method: 'put', parameters: ['ratio=1', 'offset_x=0', 'offset_y=0', 'dst_w=' + new_size.width, 'dst_h=' + new_size.height, 'src_file_name=' + src_file_name, 'dst_file_name=' + dst_file_name].join('&'), DONE: [window, photoDidSave]})).send(SDObject.rootUrl + 'photo.phtml');
}

function imageWasClicked(e){
	SDDom.stop(e);
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