sixd.ui = function(){
	sixd.apply(this, [this]);
}
sixd.ui.home = function(){
	sixd.ui.apply(this, [document]);
	var cached_elem = null;
	var textarea = SDDom.create('textarea');
	this.home_page = new sixd.model.home_page();
	var self = this;
	function swap(elem){
		SDDom.show(textarea);
		textarea.focus();
		SDDom.hide(elem);
	}
	function change_to_edit(elem){
		cached_elem = elem;
		SDDom.insertBefore(textarea, elem);
		SDDom.hide(elem);
		textarea.focus();
		change_to_edit = swap;
	}
	function save(e){
		cached_elem.innerHTML = e.target.value;
		self.home_page.html = e.target.value;
		SDDom.hide(textarea);
		SDDom.show(cached_elem);
		self.publish('field_should_save', self.home_page);
	}
	
	this.event_dbl_clicked = function(e){
		change_to_edit(e.target);
	};
	
	this.listen_for(textarea, 'blur', save);
}

sixd.controller.home = function(view){
	sixd.controller.apply(this, [view]);
	this.home_page = new sixd.model.home_page();
	var self = this;
	view.add_subscriber(this, 'field_should_save');
}
sixd.controller.home.prototype.field_should_save = function(publisher, info){
	this.save(info);
};
sixd.controller.home.prototype.success = function(data, status, request, type){
	alert(status);
};
sixd.controller.home.prototype.save = function(home_page){
	this.home_page = home_page;
	jQuery.ajax('view', home_page);
};
sixd.model.home_page = function(){
	sixd.model.apply(this, []);
	this.html = null;
};
sixd.main(function(e){
	// include jquery.
	var script = document.createElement('script');
	script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js';
	script.type = 'text/javascript';
	SDDom.insertBefore(script, document.getElementsByTagName('script')[0]);	
	var index_view = new sixd.ui.home();
	new sixd.controller.home(index_view);
});