var controller = new sixd();
controller.did_click = function(e){
	SDDom('post_code').innerText = decodeURIComponent(SDDom('pre_code').value);
}
controller.listen_for(sixd('go'), 'click', controller.did_click);
