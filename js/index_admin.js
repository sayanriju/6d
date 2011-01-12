sixd.view.index = function(){
	sixd.view.apply(this, [document]);
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

sixd.controller.index = function(view){
	sixd.controller.apply(this, [view]);
	this.home_page = new sixd.model.home_page();
	var self = this;
	view.add_subscriber(this, 'field_should_save');
}
sixd.controller.index.prototype.field_should_save = function(publisher, info){
	this.save(info);
};
sixd.controller.index.prototype.success = function(data, status, request, type){
	alert(status);
};
sixd.controller.index.prototype.save = function(home_page){
	this.home_page = home_page;
	jQuery.ajax('view', home_page);
};
sixd.model.home_page = function(){
	sixd.model.apply(this, []);
	this.html = null;
};
sixd.main(function(e){
	var index_view = new sixd.view.index();
	new sixd.controller.index(index_view);
});