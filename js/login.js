sixd.view.login = function(id, options){
	var self = sixd.view.apply(this, [id, options]);		
	this.form = SDDom('login_form');
	this.email = SDDom('email');
	this.password = SDDom('password');
	this.labels = {"email": SDDom.findFirst('label[for="email"]', this.container), "password": SDDom.findFirst('label[for="password"]', this.container)};
	SDDom.hide(this.labels.email);
	SDDom.hide(this.labels.password);	
	this.init = function(){
		var timeout = setTimeout(function(){
			self.email.focus();
			self.email.select();
		}, 500);
		
		this.listen_for(this.form, 'submit', sixd.bind(this.form_submitted, this));
		this.listen_for(this.email, 'blur', sixd.bind(this.field_blurred, this));
		this.listen_for(this.password, 'blur', sixd.bind(this.field_blurred, this));
		this.listen_for(this.email, 'focus', sixd.bind(this.field_blurred, this));
		this.listen_for(this.password, 'focus', sixd.bind(this.field_blurred, this));
		this.listen_for(this.email, 'keypress', sixd.bind(this.keypressed, this));
		this.listen_for(this.password, 'keypress', sixd.bind(this.keypressed, this));
	}
};
sixd.view.login.prototype.set_html = function(html){
	this.container.innerHTML = html;
	this.controller.html_was_set(html);
};

sixd.view.login.prototype.button_clicked = function(e){
	if(self.email.value.length == 0 || self.password.value.length == 0){
		SDDom.stop(e);
	}
};
sixd.view.login.prototype.event_clicked = function(e){
	var name = e.target.nodeName.toLowerCase() + '_clicked';
	if(this[name]){
		this[name](e);
	}
};
sixd.view.login.prototype.event_will_hide = function(){
	this.clear();
	parent_event_will_hide.call(this);
};

sixd.view.login.prototype.field_blurred = function(e){
	if(e.target.value.length == 0){
		if(!SDDom.isVisible(this.labels[e.target.id])){
			SDDom.show(this.labels[e.target.id]);
		}
	}else{
		console.log(this);
		if(SDDom.isVisible(this.labels[e.target.id])){
			SDDom.hide(this.labels[e.target.id]);
		}
	}
};
sixd.view.login.prototype.keypressed = function(e){
	if(e.target.value.length == 0){
		SDDom.hide(this.labels[e.target.id]);
	}
};
sixd.view.login.prototype.form_submitted = function(e){
	
};

sixd.main(function(e){
	var view = new sixd.view.login('login_form', null);
	view.init();
	view.show();
});
