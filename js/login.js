sixd.view.login = function(id, options){
	var self = sixd.view.apply(this, [id, options]);
	var parent_event_will_show = self.event_will_show;
	var parent_event_will_hide = self.event_will_hide;
		
	this.set_html = function(html){
		this.container.innerHTML = html;
		this.controller.html_was_set(html);
	};
	
	this.button_clicked = function(e){
		console.log(e.target);
		SDDom.stop(e);
	};
	this.event_clicked = function(e){
		var name = e.target.nodeName.toLowerCase() + '_clicked';
		if(this[name]){
			this[name](e);
		}
	};
	this.event_will_hide = function(){
		this.clear();
		parent_event_will_hide.call(this);
	};
	this.email = SDDom('email');
	this.password = SDDom('password');
	this.labels = {"email": SDDom.findFirst('label[for="email"]', this.container), "password": SDDom.findFirst('label[for="password"]', this.container)};
	SDDom.hide(this.labels.email);
	SDDom.hide(this.labels.password);
	
	var timeout = setTimeout(function(){
		self.email.focus();
		self.email.select();
	}, 500);
	
	this.field_blurred = function(e){
		if(e.target.value.length == 0){
			if(!SDDom.isVisible(self.labels[e.target.id])){
				SDDom.show(self.labels[e.target.id]);
			}
		}else{
			if(SDDom.isVisible(self.labels[e.target.id])){
				SDDom.hide(self.labels[e.target.id]);
			}
		}
	};
	this.keypressed = function(e){
		if(e.target.value.length == 0){
			SDDom.hide(self.labels[e.target.id]);
		}
	};
	this.listen_for(this.email, 'blur', this.field_blurred);
	this.listen_for(this.password, 'blur', this.field_blurred);
	this.listen_for(this.email, 'focus', this.field_blurred);
	this.listen_for(this.password, 'focus', this.field_blurred);
	this.listen_for(this.email, 'keypress', this.keypressed);
	this.listen_for(this.password, 'keypress', this.keypressed);
};
sixd.main(function(e){
	var view = new sixd.view.login('login_form', null);
	view.show();
});
