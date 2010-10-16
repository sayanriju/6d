if (typeof Object.merge !== 'function') {
	Object.merge = function (o) {
		function F() {}
		F.prototype = o;
		return new F();
	};
}

function SDObject(options){
	var observers = [];
	var me = this;
	this.delegate = options && options.delegate ? options.delegate : null;
	this.bind = function(fn) {
		return SDObject.decorateEventHandler(fn, me);
	};

	function notifySetObserversFor(key, value){
        var ubounds = observers.length;
        var indexer = 0;
        for(indexer; indexer < ubounds; indexer++){
            if(observers[indexer].observer.observerKeyValueSet !== undefined){
				if(observers[indexer].key === null || observers[indexer].key === key){
	                observers[indexer].observer.observerKeyValueSet(key, value);
				}
            }
        }
    }

    this.addObserver = function(observer, key){
        observers.push({observer: observer, key: key});
    };

    this.removeObserver = function(observer){
        var ubounds = observers.length;
        var indexer = 0;
        for(indexer; indexer < ubounds - 1; indexer++){
            if(observer === observers[indexer]){
                observers.splice(indexer, 1);
                break;
            }
        }
    };
	this.getUniqueId = function(){
		var today = new Date();
		return Date.UTC(today.getFullYear(), today.getMonth(), today.getDay(), today.getHours(), today.getMinutes(), today.getSeconds(), today.getMilliseconds());
	};
    this.set = function(key, value){
        notifySetObserversFor(key, value);
        me[key] = value;
    };

    this.get = function(key){
        return me[key];
    };
    if(options != null && options.nodeName === undefined){
        for(prop in options){
			if(isNaN(parseInt(prop))){
	            this.set(prop, options[prop]);				
			}
        }
    }
/*	if(arguments != null && arguments.nodeName === undefined){	
		for(i = 0; i < arguments.length; i++){
			for(prop in arguments[i]){
				this.set(prop, arguments[i][prop]);
			}
		}
	}*/
	return this;
}
SDObject.decorateEventHandler = function(fn, context){
	return function() {
		var args = new Array();
		if(window.event){
			var e = window.event;
			e.target = window.event.srcElement;
			args.push(e);
		}
		if(arguments && arguments.length > 0){
			var i = arguments.length;
			while(arg = arguments[--i]){
				args.push(arg);
			}
		}
		return fn.apply(context ? context : window, args);
	}
};
SDObject.capitalize = function(text){
	var words = text.toLowerCase().split('_');
	for(key in words){
		if(words[key].slice){
			words[key] = words[key].slice(0, 1).toUpperCase() + words[key].slice(1, words[key].length);
		}
	}	
	return words.join(' ');
};

SDObject.stringify = function(obj){
	var msg = [];
	for(prop in obj){
		if(obj[prop] !== null && typeof obj[prop] === 'object'){
			msg.push(SDObject.stringify(obj[prop]));
		}else{
			msg.push(prop + '=' + obj[prop]);				
		}
	}
	return msg;
};

function SDArray(ary){}
SDArray.collect = function(ary, delegate){
	var i = 0;
	var ubounds = ary.length;
	var collection = [];
	var val = null;
	var item = null;
	for(i = 0; i < ubounds; i++){
		item = ary.item ? ary.item(i) : ary[i];
		if(delegate(item)){
			collection.push(item);
		}
	}
	return collection;
};
SDArray.pluck = function(ary, delegate){
	var i = ary.length;
	var counter = 0;
	var temp = [];
	var item = null
	while(item = ary[--i]){
		temp.push(delegate(item, counter));
		counter++;
	}
	return temp;
};
SDArray.each = function(ary, delegate){
	var i = 0;
	var ubounds = ary.length;
	var item = null;
	for(i = 0; i < ubounds; i++){
		item = ary.item ? ary.item(i) : ary[i];
		delegate(ary[i], i);
	}
	return ary;
};
SDArray.contains = function(needle, ary, delegate){
	var i = ary.length;
	var item = null;
	var is_node_list = (i > 0 && ary.item);
	while(i--){
		item = is_node_list ? ary.item(i) : ary[i];
		if(!delegate){
			if(item === needle){
				return true;
			}			
		}else{
			return delegate(item, needle, i);
		}
	}
	return false;
};
SDArray.remove = function(item, ary){
	var ubounds = ary.length;
	var i = 0;
	for(i=0; i<ubounds; i++){
		if(ary[i] === item){
			ary.splice(i, 1);
			return ary;
		}
	}
	return ary;
};
SDArray.find = function(ary, delegate){
	var i = ary.length;
	var item = null;
	while(item = ary[--i]){
		if(delegate(item, i)){
			return item;
		}
	}
	return null;
};

function SDDom(id){
	if(id && id.length > 0){
		return document.getElementById(id);		
	}else{
		return id;
	}
}
SDDom.log = function(message){
	var console = null;
	function getConsole(){
		if(console != null){
			return console;
		}	
		console = SDDom('__6d_console');
		if(console == null){
			console = SDDom.create('div', {id:'__6d_console'});
			SDDom.setStyles({position: 'absolute', top: '0px', left: '0px', background: 'black', color: 'white', width: '300px', height: '200px'}, console);
			SDDom.append(document.body, console);
		}
		return console;
	}
	console = getConsole();
	console.innerHTML = message;
	return console;
}
// again, from prototype.js, thanks guys.
SDDom.keys = {
	BACKSPACE: 8
	, TAB: 9
	, RETURN: 13
	, ESC: 27
	, LEFT: 37
	, UP: 38
	, RIGHT: 39
	, DOWN: 40
	, DELETE: 46
	, HOME: 36
	, END: 35
	, PAGEUP: 33
	, PAGEDOWN: 34
	, INSERT: 45
};
SDDom.observers = [];
SDDom.remove = function(elem){
	if(elem){
		var parent = null;
		if(elem.item){
			if(elem.length > 0){
				var e = elem.item(0);
				parent = e.parentNode;
				do{
					SDDom.removeAllEventListeners(e);
					parent.removeChild(e);
				}while(e = elem.item(elem.length));
			}

		}else{
			if(elem && elem.parentNode){
				SDDom.removeAllEventListeners(elem);
				parent = elem.parentNode;
				parent.removeChild(elem);
			}
		}
	}
	return elem;
};
SDDom.removeAllChildren = function(elem){
	if(elem.hasChildNodes()){
		var i = 0;
		for(i = 0; i < elem.childNodes.length; i++){
			child = elem.childNodes[i];
			if(child.hasChildNodes()){
				SDDom.removeAllChildren(child);
			}else{
				SDDom.remove(child);				
			}
		}
	}
};
SDDom.show = function(elem){
	elem.style.display = 'block';
	elem.style.visibility = 'visible';
};

SDDom.hide = function(elem){
	elem.style.display = 'none';
	elem.style.visibility = 'hidden';
};
SDDom.toggle = function(elem){
	if(SDDom.isVisible(elem)){
		SDDom.hide(elem);
	}else{
		SDDom.show(elem);
	}
}
SDDom.isVisible = function(elem){
	var display = elem.style.display.length > 0 ? elem.style.display : 'block';
	var visibility = elem.style.visibility.length > 0 ? elem.style.visibility : 'visible';
	return display == 'block' && visibility == 'visible';
}
SDDom.byTag = function(tag, elem){
	var nodes = [];
	if(elem && elem !== document){
		var elems = document.getElementsByTagName(elem.nodeName);
		nodes = SDArray.collect(elems, function(elem){
			return SDArray.collect(elem.childNodes, function(node){
				if(node.nodeName.toLowerCase() === tag){
					return node;
				}
			});
		});
	}else{
		nodes = document.getElementsByTagName(tag);
	}
	return nodes.length > 0 ? nodes : null;
};
SDDom.findAll = function(css_selector, elem){
	if(!elem){
		elem = document;
	}
	return elem.querySelectorAll(css_selector);
};
SDDom.findFirst = function(css_selector, elem){
	if(!elem){
		elem = document;
	}
	return elem.querySelector(css_selector);
};
SDDom.getParent = function(tag, elem){
	var node_name = elem.nodeName.toLowerCase();
	if(tag == node_name){
		return elem;
	}
	function findParent(tag, elem){
		if(elem === document){
			return null;
		}
		if(elem.parentNode && elem.parentNode.parentNode){
			if(elem.parentNode.nodeName.toLowerCase() == tag){
				return elem.parentNode;
			}else{
				return findParent(tag, elem.parentNode);
			}
		}else{
			return null;
		}
	}
	
	return findParent(tag, elem);
	
};
SDDom.stop = function(e){
	if(e.preventDefault){
		e.preventDefault();
		e.stopPropagation();
	}else{
		e.cancelBubble = true;
	}	
	e.returnValue = false;	
};
SDDom.toggleClass = function(class_name, elem){
	if(SDDom.hasClass(class_name, elem)){
		SDDom.removeClass(class_name, elem);
	}else{
		SDDom.addClass(class_name, elem);
	}
	return elem;
};
SDDom.removeClass = function(class_name, elem){
	var names = elem.className.split(' ');
	var new_names = SDArray.collect(names, function(name){return name.length > 0 && name !== class_name;});
	elem.className = new_names.join(' ');
	return elem;
};
SDDom.addClass = function(class_name, elem){
	var names = elem.className.split(' ');
	var i = 0;
	var ubounds = names.length;
	var new_names = [];
	for(i = 0; i < ubounds; i++){
		if(names[i] !== class_name && names[i].length > 0){
			new_names.push(names[i]);
		}
	}
	new_names.push(class_name);
	elem.className = new_names.join(' ');
	return elem;
};
SDDom.hasClass = function(class_name, elem){
	var names = elem.className ? elem.className.split(' ') : [];
	var i = names.length;
	while(name = names[--i]){
		if(name === class_name){
			return true;
		}
	}
	return false;
};
SDDom.addEventListener = function(elem, name, fn){
	// IE doesn't fire an onload event when a script element loads, it implements onreadystatechange like XMLHTTpRequest.
	// So I'm coding for that scenario here.
	if(elem == null){
		throw "The DOM element that you want to listen to the '" + name + "' event is null.";
	}
	SDDom.observers.push([elem, name, fn]);
	if(elem.nodeName && name === 'load' && elem.nodeName.toLowerCase() === 'script' && elem.attachEvent){
		elem.onreadystatechange = function(){
			if(this.readyState === 'loaded' || this.readyState === 'complete'){
				fn();
			}
		};
	}
	if (elem.addEventListener){
		elem.addEventListener(name, fn, false);
	}else{
		elem.attachEvent('on' + name, fn);
	}
	return fn;
};

SDDom.removeAllEventListeners = function(elem){
	var i = SDDom.observers.length;
	while(observer = SDDom.observers[--i]){
		if(observer[0] === elem){
			SDDom.removeEventListener(observer[0], observer[1], observer[2]);
		}
	}
};
SDDom.removeEventListener = function(elem, name, fn){
	if(elem.removeEventListener){
		elem.removeEventListener(name, fn, false);
	}else{
		elem.detachEvent('on' + name, fn);
	}	
};

// Remove all event listeners on unload. TODO: Test this for order issues if someone is listening for the unload event too.
SDDom.addEventListener(window, 'unload', function(e){
	var i = SDDom.observers.length;
	while(observer = SDDom.observers[--i]){
		SDDom.removeEventListener(observer[0], observer[1], observer[2]);
	}
});

SDDom.getStyle = function(elem, name){
	return elem.currentStyle ? elem.currentStyle[name] : document.defaultView.getComputedStyle(elem, null).getPropertyValue(name);	
};
SDDom.getWidth = function(elem){
	if(elem === window){
		elem = document.body;
	}
	var width = SDDom.getStyle(elem, 'width') || 0;
	if(!elem.curretnStyle){
		width = SDDom.getStyle(elem, 'width') || 0;
		var padding_left = SDDom.getStyle(elem, 'padding-left') || 0;
		var padding_right = SDDom.getStyle(elem, 'padding-right') || 0;
		var margin_left = 0;//SDDom.getStyle(elem, 'margin-left') || 0;
		var margin_right = 0;//SDDom.getStyle(elem, 'margin-right') || 0;
		var border_width = SDDom.getStyle(elem, 'border-width') || 0;
		width = parseInt(width) + parseInt(padding_left) + parseInt(padding_right) + parseInt(margin_left) + parseInt(margin_right) + parseInt(border_width) * 2;
	}
	return width;
};
SDDom.getHeight = function(elem){
	if(elem === window){
		elem = document.body;
	}
	var height = SDDom.getStyle(elem, 'height') || 0;
	if(!elem.curretnStyle){
		height = SDDom.getStyle(elem, 'height') || 0;
		var padding_top = SDDom.getStyle(elem, 'padding-top') || 0;
		var padding_bottom = SDDom.getStyle(elem, 'padding-bottom') || 0;
		var margin_top = 0;//SDDom.getStyle(elem, 'margin-top') || 0;
		var margin_bottom = 0;//SDDom.getStyle(elem, 'margin-bottom') || 0;
		var border_width = SDDom.getStyle(elem, 'border-width') || 0;
		height = parseInt(height) + parseInt(padding_top) + parseInt(padding_bottom) + parseInt(margin_top) + parseInt(margin_bottom) + parseInt(border_width) * 2;
	}
	return height;
};

// From prototype.js
//http://prototypejs.org/
SDDom.getPosition = function(elem, from){
	var top = 0, left = 0;
	do {
		top += elem.offsetTop  || 0;
		left += elem.offsetLeft || 0;
		elem = elem.offsetParent;
	} while (elem && elem.tagName && from !== elem);	
	return {x:left, y:top};
};
SDDom.setStyles = function(styles, elem){
	if(elem){
		if(!elem.style){
			elem.style = [];
		}
		for(style in styles){
			elem.style[style] = styles[style];
		}		
	}
	return elem;
};
SDDom.create = function(tag, properties){
	var elem = document.createElement(tag);
	for(prop in properties){
		elem[prop] = properties[prop];
	}
	return elem;
};
SDDom.insertBefore = function(elem, parent){
	parent.parentNode.insertBefore(elem, parent);
	return elem;
};
SDDom.insertAfter = function(elem, parent){
	if(parent.parentNode){
		parent.parentNode.appendChild(elem)
	}else{
		document.appendChild(elem);
	}
	return elem;
};
SDDom.append = function(parent, elem){
	parent.appendChild(elem);
	return elem;
};
SDDom.toQueryString = function(form){
	var qs = [];
	var fields = SDDom.findAll('input,select,textarea', form);
	fields = SDArray.collect(fields, function(field){
		return !(!field.name || field.disabled || field.type == 'submit' || field.type == 'reset' || field.tpe == 'file');
	});
	var values = SDArray.pluck(fields, function(field){
		if(field.tagName.toLowerCase() === 'select'){
			return field.name + '=' + field.selectedIndex > 0 ? field.options[field.selectedIndex-1].value : null;
		}else if(field.type && ((field.type === 'radio' || field.type === 'checkbox') && field.checked)){			
			return field.name + '=' + field.value;
		}else{
			return field.name + '=' + field.value;
		}
	});
	return values.join('&');
};
SDDom.pageX = function(e){
	return e.pageX ? e.pageX : e.clientX;
};
SDDom.pageY = function(e){
	return e.pageY ? e.pageY : e.clientY;
};
SDObject.extend = function(dest, src){
	for(prop in src){
		dest[prop] = src[prop];
	}
	return dest;
};
function SDAjax(options){
	SDObject.apply(this, [options]);
	this.options = {
		method: 'post'
		, asynchronous: true
		, contentType: 'application/x-www-form-urlencoded'
		, encoding: 'UTF-8'
		, parameters: ''
		, evalJSON: true
		, evalJS: true
	};
	var events = ['UNSENT', 'OPENED', 'HEADERS_RECEIVED', 'LOADING', 'DONE'];
	SDObject.extend(this.options, options || {});
	if(!request){
		var request = createTransport();
	}
	function createTransport(){
		if(XMLHttpRequest)return new XMLHttpRequest();
		if(ActiveXObject && ActiveXObject('Msxml2.XMLHTTP')) return new ActiveXObject('Msxml2.XMLHTTP');
		if(ActiveXObject && ActiveXObject('Microsoft.XMLHTTP')) return new ActiveXObject('Microsoft.XMLHTTP');
		return null;
	}
	function didStateChange(){
		var state = events[request.readyState];
		if(this.options[state]){
			this.options[state][1].apply(this.options[state][0], [request]);
		}
		if(state === 'DONE'){
			request = null;
		}
	}
	function getHeaders(method, params){
		var header = {"X-Requested-With":"XMLHttpRequest", "Accept":"text/javascript, text/html, application/xml, text/xml, */*"};
		if(method === 'post'){
			header["Content-type"] = 'application/x-www-form-urlencoded; charset=UTF-8';
		}
		return header;
	}
	this.send = function(url){
		if(request == null) return;
		if(this.options.parameters){
			if(this.options.method == 'get'){
				url += (/\?/.test(url) ? '&' : '?') + this.options.parameters;
			}else if(/Konqueror|Safari|KHTML/.test(navigator.userAgent)){
				this.options.parameters += '&_=';
			}
		}
		if(!SDArray.contains(this.options.method, ['get', 'post'])){
			this.options.parameters += '&_method=' + this.options.method;
			this.options.method = 'post';
		}
		//TODO: attachEvent doesn't work in IE for the readystatechange event on the request. I'm not sure why but
		// I'm working around it for now. I'd love to fix this.
		//SDDom.addEventListener(request, 'readystatechange', this.bind(didStateChange));
		request.onreadystatechange = this.bind(didStateChange);
		request.open(this.options.method.toUpperCase(), url, this.options.asynchronous);		
		var headers = getHeaders(this.options.method, this.options.parameters);
		for(name in headers){
			request.setRequestHeader(name, headers[name]);
		}
		request.send(this.options.method === 'post' ? this.options.parameters : null);
	};
	return this;
	
};
function UIResponder(){
	SDObject.apply(this, arguments);
	var is_first_responder = false;
	this.nextResponder = function(){
		
	};
	this.isFirstResponder = function(){
		return is_first_responder;
	};
	this.canBecomeFirstResponder = function(){
		
	};
	this.becomeFirstResponder = function(){
		
	};
	this.canResignFirstResponder = function(){
		
	};
	this.resignFirstResponder = function(){
		
	};
	
	this.touchesBeganWithEvent = function(e){
		
	};
	this.touchesMovedWithEvent = function(e){
		
	};
	this.toucesEndedWithEvent = function(e){
		
	};
	this.touchesCancelledWithEvent = function(e){
		
	};
	this.motionBeganWithEvent = function(e){
		
	};
	this.motionEndedWithEvent = function(e){
		
	};
	this.motionCancelledWithEvent = function(e){
		
	};
	
	this.canPerformActionWithSender = function(action, sender){
		
	};
	this.undoManager = null;	
	return this;
}

function UIWindow(){
	SDObject.apply(this, arguments);
	var keyWindow = false;
	var windowLevel = null;
	this.keyWindow = function(){
		return keyWindow;
	}
	this.makeKeyAndVisible = function(){
		
	};
	this.becomeKeyWindw = function(){
		
	};
	this.makeKeyWindow = function(){
		
	};
	this.resignKeyWindow = function(){
		
	};
	this.sendEvents = function(){
		
	};
	return this;
}

function UIView(id, options){	
	this.container = null;
	UIResponder.apply(this, [options]);
	this.activeView = null;
	if(id){
		this.container = SDDom(id);
	}
	if(this.container && !this.container.id){
		this.container.id = this.getUniqueId();
	}
	this.width = (this.container ? SDDom.getWidth(this.container) : 0);
	
	this.id = id;
	if(this.container && this.onClick){
		SDDom.addEventListener(this.container, 'click', this.bind(this.onClick));		
	}
	this.setHtml = function(html){
		this.container.innerHTML = html;
	};
	this.isVisible = function(){
		var display = this.container.style.display.length > 0 ? this.container.style.display : 'block';
		var visibility = this.container.style.visibility.length > 0 ? this.container.style.visibility : 'visible';
		return display == 'block' || visibility == 'visible';
	};
	this.show = function(){
		SDDom.show(this.container);
	};
	this.hide = function(){
		SDDom.hide(this.container);
	};
	this.toggle = function(){
		SDDom.toggle(this.container);
	};
	this.open = function(url, options){
		this.activeView = window.open(url, (options.name ? options.name : this.id + '_view_' + (new Date()).UTC)
			, (options.options ? options.options : 'dependent=yes,directories=no,height=600,location=no,menubar=no,resizable=yes,outerHeight=600,outerWidth=600,scrollbars=yes,status=no,titlebar=no,toolbar=no, width=600'));
		
	};
	this.viewDidClose = function(e){
		this.activeView = null;
	};
	this.eventViewDidClose = this.bind(this.viewDidClose);	
	return this;
}

function UIController(views){
	SDObject.apply(this, arguments);
	this.views = views;
	return this;
}

UIView.Panel = function(id){
	UIView.apply(this, arguments);
	return this;
}

UIView.Button = function(id){
	UIView.apply(this, arguments);
	return this;
}

UIView.ContactPanel = function(id){
	UIView.Panel.apply(this, arguments);
	this.onClick = function(e){
		alert(e);
		SDDom.stop(e);
	}
	return this;
}
UIView.Overlay = function(options){
	UIView.apply(this, [this.id, options]);
	var today = new Date();
	this.id = 'overlay_' + Date.UTC(today.getFullYear(), today.getMonth(), today.getDay());
	this.container = SDDom.create('div');
	this.container.id = '__overlay';
	SDDom.setStyles({"top":"0", "left":"0", "bottom":"0", "right":"0", "display": "none", "width": "100%", "height":"100%", "position":"fixed", "background": "#000", "opacity":".5"}, this.container);
	SDDom.setStyles({zIndex: 1}, this.container);
	SDDom.insertBefore(this.container, document.body.children[0]);
	
	this.toggle = function(){
		SDDom.toggle(this.container);
	}
};

UIView.Modal = function(id, options){
	var today = new Date();	
	this.div = document.createElement('div');
	this.div.id = 'content_' + Date.UTC(today.getFullYear(), today.getMonth(), today.getDay());
	if(!options.handle){
		throw new Exception("I need the DOM id of the element who's click event I'll handle to open the modal window.");
	}
	this.overlay = new UIView.Overlay(null);
	this.onHandleClick = function(e){
		this.overlay.toggle();
		SDDom.setStyles({zIndex: 2, position: 'absolute', top: '20%', left: '50%', marginLeft: -1*SDDom.getWidth(this.container) / 2, marginTop: -1*SDDom.getHeight(this.container)/2}, this.container);
		this.toggle();
		if(this.didClickHandle){
			this.didClickHandle(e);
		}
		if(this.delegate && this.delegate.didClickHandle){
			this.delegate.didClickHandle.apply(this.delegate, [e]);
		}
		SDDom.stop(e);
	};
	this.onCloseClick = function(e){
		this.hide();
		this.overlay.toggle();
		if(this.didClickClose){
			this.didClickClose(e);
		}
		if(this.delegate && this.delegate.didClickCancel){
			this.delegate.didClickCancel.apply(this.delegate, [e]);
		}
	};
	this.onOkClick = function(e){
		this.hide();
		this.overlay.toggle();
		if(this.didClickOk){
			this.didClickOk.apply(this, e);
		}
		if(this.delegate && this.delegate.didClickOk){
			this.delegate.didClickOk.apply(this.delegate, [e]);
		}
	};
	this.onViewClick = function(e){		
		if(this.didClickView){
			this.didClickView(e);
		}
		if(this.delegate && this.delegate.didClickView){
			this.delegate.didClickView.apply(this.delegate, [e]);
		}
	};
	UIView.apply(this, [id, options]);	
	this.handle = null;
	try{
		this.handle = SDDom(options.handle);		
	}catch(e){
		throw new Exception("An exception occurred when I tried to get the DOM element by the id you gave me in options.handle: " + e);
	}
	this.setHtml = function(html){
		this.div.innerHTML = html;
	};
	SDDom.append(this.container, this.div);		
	SDDom.addEventListener(this.handle, 'click', this.bind(this.onHandleClick));
	this.closeHandle = SDDom.create('button');
	var properties = {innerHTML: 'Clear All', value: 'Cancel', id: 'close_button_' + Date.UTC(today.getFullYear(), today.getMonth(), today.getDay())};
	for(prop in properties){
		this.closeHandle[prop] = properties[prop];
	}
	this.okHandle = SDDom.create('button');
	properties = {innerHTML: 'Ok', value: 'Ok', id: 'ok_button_' + Date.UTC(today.getFullYear(), today.getMonth(), today.getDay())};
	for(prop in properties){
		this.okHandle[prop] = properties[prop];
	}
	SDDom.append(this.container, this.closeHandle);
	SDDom.append(this.container, this.okHandle);

	SDDom.addEventListener(this.closeHandle, 'click', this.bind(this.onCloseClick));
	SDDom.addEventListener(this.okHandle, 'click', this.bind(this.onOkClick));
	SDDom.addEventListener(this.div, 'click', this.bind(this.onViewClick));
};

UIView.ContactLink = function(id){
	this.onClick = function(e){
		SDDom.stop(e);
	};
	UIView.Button.apply(this, arguments);
	return this;
}

UIView.AdminMenu = function(id){
	this.onClick = function(e){
		if(e.target.id === 'new_post_link'){
			SDDom.stop(e);
			this.open(e.target, 'admin_menu');
		}
	};
	UIView.apply(this, arguments);
	return this;
}
UIView.Slider = function(id, options){
	this.direction = null;
	UIView.apply(this, arguments);
	var delegate = this.delegate;
	var container = this.container;
	var handle_view = null;
	if(this.container){
		handle_view = new UIView.Handle(this.container, {delegate: this.delegate, direction: this.direction});	
	}
	this.reset = function(){
		handle_view.reset();
	};
	return this;
}
UIView.TitleBar = function(id, options){
	this.bounds = null;
	this.direction = null;
	UIView.apply(this, arguments);
	
	function moveHoriz(mouse_position, handle){
		if((mouse_position.x >= bounds.lx && mouse_position.x <= bounds.ux)){
			SDDom.setStyles({left: mouse_position.x + 'px'}, handle);
		}		
	}
	function moveVert(mouse_position, handle){
		if((mouse_position.y >= bounds.ly && mouse_position.y <= bounds.uy)){
			SDDom.setStyles({top: mouse_position.y + 'px'}, handle);
		}
	}
	function moveBoth(mouse_position, handle){
		if((mouse_position.x >= bounds.lx && mouse_position.x <= bounds.ux) && (mouse_position.y >= bounds.ly && mouse_position.y <= bounds.uy)){
			SDDom.setStyles({left: (mouse_position.x + mouse_offset.x) + 'px', top: (mouse_position.y + mouse_offset.y) + 'px'}, handle);
		}		
	}
	function isClickingHandle(e){
		mouse_offset = {x: SDDom.getPosition(container).x - SDDom.pageX(e), y: SDDom.getPosition(container).y - SDDom.pageY(e)};
		SDDom.addEventListener(document, 'mousemove', mouseIsMovingFunction);
		SDDom.stop(e);
	}	
	function wasReleased(e){
		SDDom.removeEventListener(document, 'mousemove', mouseIsMovingFunction);
	}
	function move(h, position){
		moveFunc(position, h);
		delegate && delegate.isMoving ? delegate.isMoving(position) : void(0);		
	}
	function mouseIsMoving(e){
		var position = {x: SDDom.pageX(e), y: SDDom.pageY(e)};
		move(container, position);
	}
	var mouseIsMovingFunction = SDObject.decorateEventHandler(mouseIsMoving);
	var isClickingHandleFunction = SDObject.decorateEventHandler(isClickingHandle);
	var wasReleasedFunction = SDObject.decorateEventHandler(wasReleased);
	var handle = null;
	init(this.container);
	var delegate = this.delegate;
	var container = this.container;
	var container_position = SDDom.getPosition(this.container);
	var total_width = SDDom.getWidth(this.container);
	var total_height = SDDom.getHeight(this.container);
	var mouse_offset = {x: 0, y: 0};
	var bounds = this.bounds || {ux: container_position.x + total_width, uy: container_position.y + total_height, lx: container_position.x, ly: container_position.y};
	var direction = this.direction;
	var moveFunc = (this.direction == 'horizontal' ? moveHoriz : this.direction == 'vertical' ? moveVert : moveBoth);
	this.height = SDDom.getHeight(handle);
	this.width = SDDom.getWidth(handle);
	
	this.reset = function(){
		SDDom.setStyles({left: 0, top: 0}, handle);
	};
	function init(container){
		handle = SDDom.create('div');
		var title = SDDom.create('h3');
		title.innerHTML = options.text || '';
		SDDom.setStyles({height: options.height || '20px', width: options.width || '100%', background: '#fff', position: 'absolute', left: '0', cursor: 'move', "z-index":"1000"}, handle);
		SDDom.append(handle, title);
		SDDom.append(container, handle);
		SDDom.addEventListener(handle, 'mousedown', isClickingHandleFunction);
		SDDom.addEventListener(document, 'mouseup', wasReleasedFunction);
	}
	
	return this;
}

UIView.Handle = function(id, options){
	this.bounds = null;
	this.direction = null;
	UIView.apply(this, arguments);
	function moveHoriz(mouse_position, handle_position, handle){
		if((mouse_position.x >= bounds.lx && mouse_position.x <= bounds.ux)){
			SDDom.setStyles({left: handle_position.x + 'px'}, handle);
		}		
	}
	function moveVert(mouse_position, handle_position, handle){
		if((mouse_position.y >= bounds.ly && mouse_position.y <= bounds.uy)){
			SDDom.setStyles({top: handle_position.y + 'px'}, handle);
		}
	}
	function moveBoth(mouse_position, handle_position, handle){	
		if((mouse_position.x >= bounds.lx && mouse_position.x <= bounds.ux) && (mouse_position.y >= bounds.ly && mouse_position.y <= bounds.uy)){			
			SDDom.setStyles({left: handle_position.x + 'px', top: handle_position.y + 'px'}, handle);
		}		
	}
	function isClickingContainer(e){
		var position = {x: SDDom.pageX(e), y: SDDom.pageY(e)};
		SDDom.addEventListener(document, 'mousemove', mouseIsMovingFunction);
		move(handle, position, diff);
		SDDom.stop(e);
	}	
	function wasReleased(e){
		SDDom.removeEventListener(document, 'mousemove', mouseIsMovingFunction);
	}
	function mouseIsMoving(e){	
		var position = {x: SDDom.pageX(e), y: SDDom.pageY(e)};
		move(handle, position, diff);
	}	
	function move(h, position, d){
		var width = SDDom.getWidth(h) / 2;
		var height = SDDom.getHeight(h) / 2;
		var handle_position = {x: position.x  - d.x - width, y: position.y - d.y - height};
		moveFunc(position, handle_position, handle);
		var percent = {x:(handle_position.x / total_width), y: (handle_position.y / total_height)};
		delegate && delegate.sliderIsMoving ? delegate.sliderIsMoving(percent) : void(0);		
	}
	
	this.reset = function(){
		SDDom.setStyles({left: 0, top: 0}, handle);
	};
	var isClickingContainerFunction = SDObject.decorateEventHandler(isClickingContainer);
	var wasReleasedFunction = SDObject.decorateEventHandler(wasReleased);
	var mouseIsMovingFunction = SDObject.decorateEventHandler(mouseIsMoving);
	
	var handle = null;
	init(this.container);
	var delegate = this.delegate;
	var container = this.container;
	var container_position = SDDom.getPosition(this.container);
	var total_width = SDDom.getWidth(this.container);
	var total_height = SDDom.getHeight(this.container);
	var diff = {x: SDDom.getPosition(this.container).x, y: SDDom.getPosition(this.container).y};
	var bounds = this.bounds || {ux: container_position.x + total_width, uy: container_position.y + total_height, lx: container_position.x, ly: container_position.y};
	var direction = this.direction;
	SDDom.setStyles({position: 'relative'}, container);
	var moveFunc = (this.direction == 'horizontal' ? moveHoriz : this.direction == 'vertical' ? moveVert : moveBoth);
	function init(container){
		handle = SDDom.create('span');
		SDDom.setStyles({display: 'block', height: options.height || '15px', width: options.width || '15px', background: '#fff', position: 'absolute', left: '0', top: '-6px'}, handle);
		SDDom.setStyles({position: 'relative'});
		SDDom.append(container, handle);
		SDDom.addEventListener(container, 'mousedown', isClickingContainerFunction);
		SDDom.addEventListener(document, 'mouseup', wasReleasedFunction);
	}
	
	return this;
}
UIView.Cropper = function(id, options){
	this.canvases = null;
	options.width = options.width ? options.width : 200;
	options.height = options.height ? options.height : 200;
	UIView.apply(this, [id, options]);
	var delegate = this.delegate;
	start_position = null;
	var self = this;
	this.getCanvas = function(photo){
		for(var i = 0; i < this.canvases.length; i++){
			if(this.canvases[i].photo == photo){
				return this.canvases[i];
			}
		}
		return null;
	};
	function photoWatcher(){
		for(var i = 0; i < self.canvases.length; i++){
			if(self.canvases[i].photo.src != self.canvases[i].original_src){
				self.canvases[i].start_size = {width: SDDom.getWidth(self.canvases[i].photo), height: SDDom.getHeight(self.canvases[i].photo)};
				self.canvases[i].original_size = {width: SDDom.getWidth(self.canvases[i].photo), height: SDDom.getHeight(self.canvases[i].photo)};
				self.canvases[i].original_src = self.canvases[i].photo.src;
				delegate && delegate.photoHasChanged ? delegate.photoHasChanged(self.canvases[i].photo) : null;
			}
		}
	}
	function doubleClickedOnPhoto(e){
		var new_size = {width: SDDom.getWidth(e.target), height: SDDom.getHeight(e.target)};
		var pos = SDDom.getPosition(e.target);
		var view = getCanvas(e.target.src);
		var canvas_position = SDDom.getPosition(view.canvas);
		var canvas_size = {width: SDDom.getWidth(view.canvas), height: SDDom.getHeight(view.canvas)};
		var photo = SDDom.findFirst('img', view.canvas);
		var offset = {x: canvas_position.x - pos.x, y: canvas_position.y - pos.y, ratio: new_size.width/view.original_size.width};
		delegate && delegate.photoWasDoubleClicked ? delegate.photoWasDoubleClicked(photo, new_size, pos, offset, canvas_size) : void(0);
	}
	function resize(photo, position){
		var current_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		var view = getCanvas(photo.src);
		var diff = position.x - start_position.x;
		SDDom.setStyles({width: diff + 'px'}, photo);
	}
	function move(photo, position, diff){
		SDDom.setStyles({left: (position.x - diff.x) + 'px', top: (position.y - diff.y) + 'px'}, photo);
	}
	function mouseIsMoving(e){
		SDDom.stop(e);
		var photo = e.target.src ? e.target : SDDom.findFirst('img', e.target);
		var position = {x: SDDom.pageX(e), y: SDDom.pageY(e)};
		if(e.shiftKey){
			resize(photo, position);
		}else{
			move(photo, position, {x: xdiff, y:ydiff});
		}		
	}
	function isClickingOnPhoto(e){
		var position = {x: SDDom.pageX(e), y: SDDom.pageY(e)};		
		var photo = e.target.src ? e.target : SDDom.findFirst('img', e.target);
		var view = SDArray.find(canvases, function(c, i){return c.photo.src == photo.src;});
		var canvas_position = SDDom.getPosition(view.canvas);
		var photo_position = SDDom.getPosition(photo);
		xdiff = position.x - photo_position.x + canvas_position.x;
		ydiff = position.y - photo_position.y + canvas_position.y;
		start_position = {x: position.x - view.start_size.width, y: position.y - view.start_size.height};
		SDDom.stop(e);
		SDDom.addEventListener(view.canvas, 'mousemove', mouseIsMovingFunction);
	}
	function wasReleasedOverPhoto(e){
		var photo = e.target.src ? e.target : SDDom.findFirst('img', e.target);
		var view = getCanvas(photo.src);
		view.photo = photo;
		view.start_size = {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)};
		SDDom.removeEventListener(view.canvas, 'mousemove', mouseIsMovingFunction);
	}
	function photoFinishedLoading(e){
		var view = getCanvas(e.target.src);
		view.original_size = {width: SDDom.getWidth(view.photo), height: SDDom.getHeight(view.photo)};
	}
	
	function getCanvas(src){
		return SDArray.find(canvases, function(c, i){return c.photo.src == src;});
	}
	var isClickingOnPhotoFunction = SDObject.decorateEventHandler(isClickingOnPhoto);
	var wasReleasedOverPhotoFunction = SDObject.decorateEventHandler(wasReleasedOverPhoto);
	var doubleClickedOnPhotoFunction = SDObject.decorateEventHandler(doubleClickedOnPhoto);
	var photoFinishedLoadingFunction = SDObject.decorateEventHandler(photoFinishedLoading);
	var mouseIsMovingFunction = SDObject.decorateEventHandler(mouseIsMoving);
	var canvases = init(this.canvases);
	this.canvases = canvases;
	function init(canvases){
		var canvas;
		var list = [];
		var photo;
		for(var i = 0; i < canvases.length; i++){
			canvas = canvases[i];
			SDDom.setStyles({position: 'relative', height: options.height + 'px', width: options.width + 'px'}, canvas);
			photo = SDDom.findFirst('img', canvas);
			SDDom.setStyles({position: 'absolute', top: 0, left: 0}, photo);
			list.push({canvas: canvas
				, photo: photo
				, original_src: photo.src
				, start_size: {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)}
				, original_size: {width: SDDom.getWidth(photo), height: SDDom.getHeight(photo)}});
			observe(canvas);
		}
		return list;
	}
	function observe(canvas){
		var photo = SDDom.findFirst('img', canvas);
		SDDom.addEventListener(canvas, 'mousedown', isClickingOnPhotoFunction);
		SDDom.addEventListener(canvas, 'mouseup', wasReleasedOverPhotoFunction);
		SDDom.addEventListener(canvas, 'mouseout', wasReleasedOverPhotoFunction);
		SDDom.addEventListener(canvas, 'dblclick', doubleClickedOnPhotoFunction);
		SDDom.addEventListener(photo, 'load', photoFinishedLoadingFunction);
		setInterval(photoWatcher, 50);
	}
	
	return this;
}

UIView.PhotoViewer = function(id, options){
	SDDom.append(document.body.children[0], SDDom.create('div', {id:id}));
	var self = UIView.apply(this, arguments);
	SDDom.setStyles({position: 'absolute', margin: '0 auto', display: 'block', top: 0, left: 0, width: '360px', height: '400px', border: 'solid 5px #fff', background: '#000', overflow: 'hidden'}, this.container);
	var bounds = {ux: SDDom.getWidth(window), lx: 0, uy: SDDom.getHeight(window), ly: 0};
	var handle_view = new UIView.TitleBar(this.container, {delegate: this, bounds: bounds, text: options.title});
	this.frame = SDDom.create('div', {className: 'frame'});
	var height = (SDDom.getHeight(this.container) - handle_view.height - 60) + 'px';
	SDDom.setStyles({height: height, "margin-top": handle_view.height + 'px'}, this.frame);
	this.scroll_view = SDDom.create('div', {className: 'scroll_view', id: 'scroll_view'});
	SDDom.setStyles({height: height, top: handle_view.height + 'px', overflow: 'auto', width: '100%'}, this.scroll_view);
	
	this.close_link = SDDom.create('a', {title: 'close the photo viewer', innerHTML: 'x', href: 'javascript:void(0);'});
	SDDom.setStyles({position: 'absolute', top: '0', left: '0', display: 'block', width: '20px', height: '15px', "z-index":"10001", border: 'solid 1px rgb(100,100,100)', "border-radius": '10px', color: 'rgb(80,80,80)', "line-height":"12px", "text-align":"center", "text-decoration":"none", "box-shadow":"1px 1px 7px rgb(0,0,0)"}, this.close_link);
	SDDom.append(this.container, this.close_link);
	SDDom.setStyles({position: 'absolute'}, this.container);
	SDDom.append(this.frame, this.scroll_view);
	SDDom.append(this.container, this.frame);
	var photo_upload_field = null;
	this.isMoving = function(percent){
		//SDDom.setStyles({top: percent.y * 200 + 'px', left: percent.x * 100 + 'px'}, this.container);
	};
	
	this.refresh = function(url){
		(new SDAjax({method: 'get', DONE: [this, this.refreshIsDone]})).send(url.replace('.html', '') + '.phtml');
	};
	this.refreshIsDone = function(request){
		this.scroll_view.innerHTML = request.responseText;
		photo_upload_field = SDDom('photo_upload_field');
		SDDom.addEventListener(photo_upload_field, 'change', UIView.PhotoViewer.photoDidChange);
	};
	this.close = function(e){
		this.toggle();
	};
	this.clicked = function(e){
		if(e.target.nodeName == 'IMG'){
			SDDom.toggleClass('selected', e.target);
			if(this.delegate && this.delegate.imageWasClicked){
				this.delegate.imageWasClicked(e);
			}
		}else if(e.target.type == 'file'){
			
		}
	};
	
	SDDom.addEventListener(this.close_link, 'click', this.bind(this.close, this));
	SDDom.addEventListener(this.frame, 'click', this.bind(this.clicked, this));
	return self;
};
UIView.PhotoViewer.photosDidLoad = function(request){
	var response = JSON.parse(request.responseText);
	var html = '<ul>';
	for(var i = 0; i < response.length; i++){
		html += '<h3>' + response[i].title + '</h3>';
		html += '<li><form action="' + SDObject.rootUrl + '/photo/" method="post" class="delete"><img src="' + response[i].little_src + '" width="' + response[i].width + '" /><input name="src" value="' + response[i].src + '" type="hidden" /><input name="_method" type="hidden" value="delete" /><button type="submit">Delete</button></form></li>';
	}
	html += '</ul>';
	SDDom('list-of-photos').innerHTML = html;
};
UIView.PhotoViewer.photoDidChange = function(e){
	if(SDDom('photo_names[' + e.target.value + ']')){
		alert("you've already added that photo.");
		SDDom.stop(e); 
	}else{
		SDDom('media_form').submit();
	}
};
UIView.PhotoViewer.photoDidUpload = function(response){
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
		(new SDAjax({method: 'get', DONE: [UIView.PhotoViewer, UIView.PhotoViewer.photosDidLoad]})).send(SDDom('media_form').action.replace('photos', 'photos.json'));
	}
};


function sixd(){
	var observers = [];
	this.listen_for = function(elem, name, fn){
		if (elem.addEventListener){
			elem.addEventListener(name, fn, false);
		}else{
			elem.attachEvent('on' + name, fn);
		}
		return fn;
	};
	this.add_subscriber = function(subscriber, notification){
		observers.push({subscriber: subscriber, notification: notification});
	};
	this.publish = function(notification, info){
		var i = 0;
		for(i = 0; i < observers.length; i++){
			if(observers[i].notification === notification){
				observers[i].subscriber[notification].apply(observers[i].subscriber, [observers[i].publisher, info]);
			}
		}
	};
	return this;
}
sixd.extend = function(child, supertype){
	child.prototype.__proto__ = supertype.prototype;
	child.prototype.__super = supertype;
}

sixd.bind = function(fn, context){
	return function() {
		var args = new Array();
		if(window.event){
			var e = window.event;
			e.target = window.event.srcElement;
			args.push(e);
		}
		if(arguments && arguments.length > 0){
			var i = arguments.length;
			while(arg = arguments[--i]){
				args.push(arg);
			}
		}
		return fn.apply(context ? context : this, args);
	}
}
sixd.array = function(){
	
};

sixd.array.remove_from = function(item, ary, fn){
	var ubounds = ary.length;
	var i = 0;
	for(i=0; i < ubounds; i++){
		if(fn !== null){
			if(fn(item, i)){
				ary.splice(i,1);
				return ary;
			}
		}else if(ary[i] === item){
			ary.splice(i, 1);
			return ary;
		}
	}
	return ary;
};
sixd.array.each = function(ary, fn){
	var i = 0;
	for(i = 0; i < ary.length; i++){
		fn(ary[i]);
	}
	return ary;
};
sixd.array.collect = function(ary, fn){
	var temp = [];
	var i = 0;
	for(i = 0; i < ary.length; i++){
		temp.push(fn(ary[i], i));
	}
	return temp;
};
sixd.array.contains = function(item, ary, fn){
	var i = 0;
	for(i = 0; i < ary.length; i++){
		if(fn(item, ary[i])){
			return true;
		}
	}
	return false;
};
sixd.array.pluck = function(ary, key){
	var temp = [];
	var i = 0;
	for(i = 0; i < ary.length; i++){
		temp.push(ary[i][key]);
	}
	return temp;
};
sixd.array.find = function(item, ary, fn){
	var i = ary.length;
	while(i--){
		if(fn(ary[i])){
			return ary[i];
		}
	}
	return null;
};

sixd.main = function(fn){
	SDDom.addEventListener(window, 'load', fn);
};
sixd.get = function(url, fn, context){
	(new SDAjax({method: 'get', DONE: [context, fn]})).send(url);
};
sixd.model = function(child){
	sixd.apply(this, arguments);
};
sixd.file_uploader = function(form, elem, callback, delegate){
	sixd.apply(this, arguments);
	this.button = elem;
	this.callback = callback;
	this.form = form;
	this.delegate = delegate;
	this.changed = sixd.bind(function(e){
		this.form.action += '&callback=' + this.callback;
		this.form.submit();
	}, this);
	this.did_upload = function(response){
		this.reset();
		this.delegate.did_upload(response);
	};
	this.reset = function(){
		this.form.reset();
	};
	this.listen_for(elem, 'change', this.changed);
};

sixd.controller = function(view, options){
	sixd.apply(this, [this]);
	this.handle = SDDom(options && options.handle_id ? options.handle_id : SDDom.create('div'));
	this.views = [view];
	this.delegate = options && options.delegate ? options.delegate : null;
	view.controller = this;
	var self = this;
	this.handle_clicked = this.listen_for(this.handle, 'click', function(e){
		SDDom.stop(e);
		self.get_view().toggle();
		self.get_view().make_active(1000);
	});
};



sixd.view = function(id, options){
	sixd.apply(this, [this]);
	var self = this;
	this.options = SDObject.extend({tag: 'div'}, options);
	this.event_clicked = function(e){};
	this.event_dbl_clicked = function(e){};
	this.event_will_show = function(){
		if(this.controller && this.controller.event_will_show){
			this.controller.event_will_show(this);			
		}
	};
	this.event_will_hide = function(){
		if(this.controller && this.controller.event_will_hide){
			this.controller.event_will_hide(this);			
		}
	};
	this.dbl_clicked = function(e){
		if(self.controller && self.controller.dbl_clicked !== undefined){
			self.controller.dbl_clicked(e);
		}
		self.event_dbl_clicked(e);
	};
	this.clicked = function(e){
		if(self.controller && self.controller.clicked !== undefined){
			self.controller.clicked(e);
		}
		self.event_clicked(e);
	};

	this.is_visible = function(){
		return SDDom.isVisible(this.container);
	};
	this.clear = function(){
		SDDom.removeAllChildren(this.container);
	};
	this.toggle = function(){		
		if(SDDom.isVisible(this.container)){
			this.hide();
		}else{
			this.show();
		}
	};
	this.hide = function(){
		this.event_will_hide();
		SDDom.hide(this.container);
	};
	this.show = function(){
		this.event_will_show();
		SDDom.show(this.container);
	};
	this.display = function(elem){
		if(this.controller && this.controller.display){
			this.controller.display(elem);
		}
	};
	this.move_to_center = function(){
		SDDom.setStyles({padding: '0px', position: 'fixed', top: '50%', left: '50%'}, this.container);
		var width = -1*SDDom.getWidth(this.container)/2 + 'px';
		var height = -1*SDDom.getHeight(this.container)/2 + 'px';
		SDDom.setStyles({"margin-left":width, "margin-top":height}, this.container);
	};
	this.make_active = function(position){
		SDDom.setStyles({"z-index":position}, this.container);			
	};
	this.make_inactive = function(position){
		SDDom.setStyles({"z-index":position}, this.container);
	};
	this.controller = null;
	this.id = id;
	this.container = SDDom(id) || SDDom.create(this.options.tag, {id: id});
	SDDom.setStyles({"display":"none"}, this.container);
	if(id == null){
		SDDom.insertBefore(this.container, document.body.children[0]);		
	}	
	this.listen_for(this.container, 'click', this.clicked);
	this.listen_for(this.container, 'dblclick', this.dbl_clicked);
	return this;
};

sixd.view.overlay = function(){
	var today = new Date();
	this.id = 'overlay_' + Date.UTC(today.getFullYear(), today.getMonth(), today.getDay());
	sixd.view.apply(this, [this.id, null]);
	this.container = SDDom.create('div');
	this.container.id = this.id;
	SDDom.setStyles({"top":"0", "left":"0", "bottom":"0", "right":"0", "display": "none", "width": "100%", "height":"100%", "position":"fixed", "background": "#000", "opacity":".5"}, this.container);
	SDDom.setStyles({zIndex: 1}, this.container);
	SDDom.insertBefore(this.container, document.body.children[0]);
};
sixd.view.modal = function(id, options){
	this.overlay = new sixd.view.overlay();
	this.overlay.make_active(999);
	var self = sixd.view.apply(this, [id, options]);
	this.options = SDObject.extend({background: 'rgb(0,0,0)', width: 400, height: 400, color: 'rgb(255,255,255)', padding: '0px'}, options);
	SDDom.setStyles({"position":"fixed", "top":"25%", "left":"50%", "margin-left":-1 * this.options.width / 2 + 'px', "z-index":1000, "display": 'none', "background": this.options.background, "color": this.options.color, "padding": this.options.padding, "width": this.options.width + 'px', "height": this.options.height + 'px'}, this.container);
	var parent_event_will_show = self.event_will_show;
	var parent_event_will_hide = self.event_will_hide;
	
	this.event_will_show = function(){
		this.overlay.show();
		parent_event_will_show.call(this);
	};
	this.event_will_hide = function(){
		this.overlay.hide();
		parent_event_will_hide.call(this);
	};
	return this;
};
sixd.view.make_closable = function(view){
	sixd.apply(this, [this]);
	var close_button = SDDom.create('button', {id: view.container.id + '_close_button'});
	var span = SDDom.create('span', {id: view.container.id + '_span_close_button'});
	span.innerText = 'x';
	SDDom.append(close_button, span);
	SDDom.addClass('close', close_button);
	this.clicked = sixd.bind(function(e){
		view.hide();
	}, this);
	
	this.listen_for(close_button, 'click', this.clicked);
	if(view.container.firstChild){
		SDDom.insertBefore(close_button, view.container.firstChild);
	}else{
		SDDom.append(view.container, close_button);
	}
	return this;
};
sixd.view.post = function(id, options){	
	sixd.view.apply(this, [id, options]);
	var self = this;
	this.post = {title: null, body: null, post_type: null, id: null, source: null, tags: null, description: null, password: null, post_date: null, is_published: null, make_home_page: null};
	this.title = null;
	this.body = null;
	this.post_types = null;
	this.post_type = null;
	this.id = null;
	this.source = null;
	this.tags = null;
	this.description = null;
	this.password = null;
	this.post_date = null;
	this.is_published = null;
	this.make_home_page = null;
	this.frame = null;
	this.send_to_list = null;
	this.selected_images = new sixd.model.images();
	this.selected_images.add_subscriber(this, 'image_was_added');
	this.selected_images.add_subscriber(this, 'image_was_removed');
	
	var first_article = SDDom.findFirst('article.hentry', document);
	SDDom.insertBefore(this.container, first_article);
	SDDom.addClass('hentry new', this.container);
	function set_outlets(){
		self.title = SDDom('title');
		self.body = SDDom('body');
		self.post_types = SDDom.findFirst('input[name="post_type"]', this.container);
		self.id = SDDom('id');
		self.source = SDDom('source');
		self.tags = SDDom('tags');
		self.description = SDDom('description');
		self.password = SDDom('password');
		self.post_date = SDDom('post_date');
		self.is_published = SDDom('is_published');
		self.make_home_page = SDDom('make_home_page');
		self.send_to_list = SDDom('send_to_list');
		SDDom.append(self.send_to_list, SDDom.create('ul'));
	}
	function highlight_post_type(elem){
		var selected = SDDom.findFirst('li.selected', elem.parentNode.parentNode);
		if(selected !== null){
			SDDom.removeClass('selected', selected);			
		}
		elem.parentNode.className = 'selected';
	};
	function swap_view_to(post_type){
		var name = 'swap_to_' + post_type;
		if(self[name]){
			self[name]();
		}else{
			SDDom.show(self.body);
		}
	}
	
	this.swap_to_photo = function(){
		if(SDDom.isVisible(this.body)){
			SDDom.hide(this.body);
			this.frame = SDDom('post_view_frame');
			this.frame = this.frame === null ? SDDom.create('div', {id:'post_view_frame'}) : this.frame;
			SDDom.insertBefore(this.frame, this.body);
		}
		
		SDDom.toggleClass('photo', this.container);
	};
	this.swap_to_album = function(){
		this.swap_to_photo();
		SDDom.toggleClass('album', this.container);
	};
	this.set_post_type = function(post_type){
		this.post_type = post_type;
		var elem = SDDom.findFirst('input[value="' + post_type + '"]', this.container);
		if(elem){
			highlight_post_type(elem);
		}
		if(SDDom.isVisible(this.container)){
			elem.setAttribute('checked', true);
			swap_view_to(post_type);			
		}
	};
	this.event_will_show = function(){
		this.controller.event_will_show(this);
	};
	this.event_will_hide = function(){
		this.clear();
	};
	this.set_html = function(html){
		this.container.innerHTML = html;
		this.controller.html_was_set(html);
		set_outlets();
		if(this.selected_images.get_length() > 1){
			this.set_post_type('album');
		}else if(this.selected_images.get_length() > 0){
			this.set_post_type('photo');
		}else{
			this.set_post_type(SDDom.findFirst('ul li input[checked="checked"]', this.container).value);
		}
		this.body.value = sixd.array.pluck(this.selected_images.get_list(), 'src').join('\n');			
		for(var i = 0; i < this.selected_images.get_length(); i++){
			this.add_image_to_frame(this.selected_images.get_list()[i]);
		}
		sixd.view.make_closable(this);
	};
	this.input_clicked = function(e){
		if(e.target.type === 'radio'){
			this.set_post_type(e.target.value);
		}
	};
	this.event_clicked = function(e){
		var name = e.target.nodeName.toLowerCase() + '_clicked';
		if(this[name]){
			this[name](e);
		}
	};
	function switch_post_type(){
		if(self.selected_images.get_length() > 1){
			self.set_post_type('album');
		}else if(self.selected_images.get_length() > 0){
			self.set_post_type('photo');			
		}
	}
	this.remove_image_from_frame = function(img){
		img = SDDom.findFirst('img[src="' + img.src + '"]', this.frame);
		SDDom.remove(img);
	};
	this.add_image_to_frame = function(img){
		this.frame.innerHTML += '<img src="' + img.src + '" />';
	};
	this.image_was_added = function(publisher, info){
		switch_post_type();
		if(this.body){
			this.body.value = sixd.array.pluck(this.selected_images.get_list(), 'src').join('\n');			
			this.add_image_to_frame(info);
		}
	}
	this.image_was_removed = function(publisher, info){
		switch_post_type();
		if(this.body){
			this.body.value = sixd.array.pluck(this.selected_images.get_list(), 'src').join('\n');
			this.remove_image_from_frame(info);
		}
	}
	this.add_person = function(person){
		var li = SDDom.create('li', {id: 'person_id_' + person.id});
		li.innerHTML = person.name + '<input type="hidden" value="' + person.id + '" name="people[]" />';
		SDDom.append(this.send_to_list, li);
	};
	this.remove_person = function(person){
		SDDom.remove(SDDom('person_id_' + person.id));
	};
	this.add_group = function(text){
		var li = SDDom.create('li', {id: 'group_' + text.replace('/\s/g', '').toLowerCase()});
		li.innerHTML = text + '<input type="hidden" value="' + encodeURIComponent(text) + '" name="groups[]" />';
		SDDom.append(this.send_to_list, li);
	};
	this.remove_group = function(text){
		SDDom.remove(SDDom('group_' + text.replace('/\s/g', '').toLowerCase()));
	};
	return this;
};

sixd.view.film_strip = function(id, options){	
	sixd.view.apply(this, [id, options]);
	this.is_selected = function(img){
		return img.className === 'selected';
	};
	this.make_scrollable = function(elem){
		var lis = SDDom.findAll('li', this.container);
		var i = lis.length;
		var width = 0;
		var tmp_width = 0;
		for(i = 0; i < lis.length; i++){
			width = width + 5 + SDDom.getWidth(lis[i]);
		}
		SDDom.setStyles({width: width + 'px'}, elem);
	};
	this.set_html = function(html){
		this.container.innerHTML = html;
		this.controller.html_was_set(html);
		this.make_scrollable(SDDom.findFirst('.scrollable ul', this.container));
		sixd.view.make_closable(this);
	};
	this.img_clicked = function(e){
		SDDom.toggleClass('selected', e.target);
	};
	this.event_clicked = function(e){
		var name = e.target.nodeName.toLowerCase() + '_clicked';
		if(this[name]){
			this[name](e);
		}
	};
	this.event_will_hide = function(){
		this.clear();
	};
	
	this.add_image = function(image){
		var li = SDDom.create('li');
		var title = SDDom.create('h3');
		var img = new Image();
		var form = SDDom.create('form', {method:'post', action: SDObject.rootUrl + '/photo', className:'delete'});
		img.src = image.photo_path;
		SDDom.append(form, SDDom.create('input', {type: 'hidden', value:'delete', name:'_method'}));
		SDDom.append(form, SDDom.create('input', {type: 'hidden', value:image.photo_path, name:'src'}));
		SDDom.append(form, SDDom.create('button', {type: 'submit', innerHTML:'Delete'}));
		
		SDDom.append(li, title);
		SDDom.append(li, img);
		SDDom.append(li, form);
		SDDom.append(SDDom.findFirst('ul', this.container), li);
		this.make_scrollable(SDDom.findFirst('.scrollable ul', this.container));
	};
	SDDom.addClass('film_strip', this.container);
	SDDom.setStyles({display: 'none', background: '#000000', color: '#fff', padding: '0px'}, this.container);
	return this;
};

sixd.model.images = function(list){
	sixd.apply(this, arguments);
	var images = (list == null ? [] : list);
	this.each = function(fn){
		sixd.array.each(images, fn);
	};
	this.contains = function(image){
		//return sixd.array.contains(image, images, function(item, image){return item.src == image.src;});
		var contains = false;
		sixd.array.each(images, function(img){
			if(img.src == image.src){
				contains = true;
			}
		});
		return contains;
	};
	this.add = function(img){
		images.push(img);
		this.publish('image_was_added', img);
	};
	this.remove = function(img){
		images = sixd.array.remove_from(img, images, function(item){return item.src == img.src;});
		this.publish('image_was_removed', img);
	};
	this.get_list = function(){
		return images;
	};
	this.get_length = function(){
		return images.length;
	};
};
sixd.model.people = function(list){
	sixd.apply(this, arguments);
	var people = (list == null ? [] : list);
	this.each = function(fn){
		sixd.array.each(people, fn);
	};
	this.contains = function(person){
		var contains = false;
		sixd.array.each(person, function(p){
			if(person.id === p.id){
				contains = true;
			}
		});
		return contains;
	};
	this.add = function(id){
		people.push(id);
		this.publish('person_was_added', id);
	};
	this.remove = function(id){
		people = sixd.array.remove_from(id, people, function(item){return item == id;});
		this.publish('person_was_removed', id);
	};
	this.get_list = function(){
		return people;
	};
	this.get_length = function(){
		return people.length;
	};
};
sixd.model.people.decode = function(value){
	value = decodeURIComponent(value).replace(/\+/g, '');
	return JSON.parse(value);
};
sixd.model.groups = function(list){
	sixd.apply(this, arguments);
	var groups = (list == null ? [] : list);
	this.each = function(fn){
		sixd.array.each(groups, fn);
	};
	this.contains = function(group){
		var contains = false;
		sixd.array.each(group, function(g){
			if(group === g){
				contains = true;
			}
		});
		return contains;
	};
	this.add = function(text){
		groups.push(text);
		this.publish('group_was_added', text);
	};
	this.remove = function(text){
		people = sixd.array.remove_from(text, groups, function(item){return item == text;});
		this.publish('group_was_removed', text);
	};
	this.get_list = function(){
		return groups;
	};
	this.get_length = function(){
		return groups.length;
	};
};
sixd.model.groups.decode = function(value){
	value = decodeURIComponent(value).replace(/\+/g, ' ');
	return value;
};

sixd.view.addressbook = function(id, options){
	var self = sixd.view.modal.apply(this, [id, options]);
	var parent_event_will_hide = self.event_will_hide;
	this.people = new sixd.model.people();
	this.people.add_subscriber(this, 'person_was_added');
	this.people.add_subscriber(this, 'person_was_removed');
	this.groups = new sixd.model.groups();
	this.groups.add_subscriber(this, 'group_was_added');
	this.groups.add_subscriber(this, 'group_was_removed');
	
	this.set_html = function(html){
		this.container.innerHTML = html;
		this.controller.html_was_set(html);
		sixd.view.make_closable(this);
	};
	this.input_clicked = function(e){
		if(e.target.type === 'checkbox'){
			if(e.target.name === 'groups'){
				this.delegate.group_was_clicked(e.target);
			}else if(e.target.name === 'people'){
				this.delegate.person_was_clicked(e.target);
			}
		}
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
	this.group_was_added = function(publisher, text){
		if(text === 'All Contacts'){
			var group_checkboxes = SDDom.findAll('input[name="groups"]', this.container);

			for(var i = 0; i < group_checkboxes.length; i++){
				var checkbox = group_checkboxes[i];
				if(checkbox.value !== 'All+Contacts'){
					checkbox.disabled = true;
					checkbox.checked = false;
					var text = sixd.model.groups.decode(checkbox.value);
					this.groups.remove(text);
				}
			}
			var people_checkboxes = SDDom.findAll('input[name="people"]', this.container);
			for(var i = 0; i < people_checkboxes.length;i++){
				var checkbox = people_checkboxes[i];
				checkbox.disabled = true;
				checkbox.checked = false;
				var person = sixd.model.people.decode(checkbox.value);
				this.people.remove(person);
			}
		}
	};
	this.group_was_removed = function(publisher, text){
		if(text === 'All Contacts'){
			var checkboxes = SDDom.findAll('input[type="checkbox"]', this.container);
			sixd.array.each(checkboxes, function(checkbox){
				if(checkbox.value !== 'All+Contacts'){
					checkbox.disabled = false;			
				}
			});
		}
	};
	this.person_was_added = function(publisher, person){
		
	};
	this.person_was_removed = function(publisher, text){
		
	};
};
sixd.controller.addressbook = function(view, options){
	sixd.controller.apply(this, [view, options]);
	var self = this;
	view.delegate = this;
	this.callback = options.callback;
	this.clicked = function(e){
		if(this.delegate && this.delegate.event_view_was_clicked){
			this.delegate.event_view_was_clicked(this.get_view());
		}
	};
	this.person_was_clicked = function(checkbox){
		if(checkbox.checked){
			this.get_view().people.add(sixd.model.people.decode(checkbox.value));
		}else{
			this.get_view().people.remove(sixd.model.people.decode(checkbox.value));
		}
	};
	this.group_was_clicked = function(checkbox){
		if(checkbox.checked){
			this.get_view().groups.add(sixd.model.groups.decode(checkbox.value));
		}else{
			this.get_view().groups.remove(sixd.model.groups.decode(checkbox.value));
		}
	};
	this.get_view = function(){
		return this.views[0];
	};
	this.event_will_hide = function(view){
	};
	this.event_will_show = function(view){
		if(this.delegate && this.delegate.event_view_will_show){
			this.delegate.event_view_will_show(view);			
		}
		var url = this.handle.href;
		sixd.get(url.replace('.html', '') + '.phtml', this.request_is_done, this);
	};
	this.request_is_done = function(request){
		this.get_view().set_html(request.responseText);
	};
	this.html_was_set = function(html){
	};
};
sixd.controller.post = function(view, options){
	sixd.controller.apply(this, [view, options]);
	this.addressbook_controller = null;
	var self = this;
	this.get_view = function(){
		return this.views[0];
	};
	this.event_will_show = function(view){
		if(this.delegate !== null){
			this.delegate.event_view_will_show(view);
		}
		var url = this.handle.href;
		sixd.get(url.replace('.html', '') + '.phtml', this.request_is_done, this);
	};
	this.request_is_done = function(request){
		this.get_view().set_html(request.responseText);
		this.addressbook_controller = new sixd.controller.addressbook(new sixd.view.addressbook('addressbook_view', {tag:'div'}), {handle_id: 'address'});
		this.addressbook_controller.get_view().make_active();
		this.addressbook_controller.delegate = this;
		this.addressbook_controller.get_view().people.add_subscriber(this, 'person_was_added');
		this.addressbook_controller.get_view().people.add_subscriber(this, 'person_was_removed');
		this.addressbook_controller.get_view().groups.add_subscriber(this, 'group_was_added');
		this.addressbook_controller.get_view().groups.add_subscriber(this, 'group_was_removed');
	};
	this.person_was_added = function(publisher, person){
		this.get_view().add_person(person);
	};
	this.person_was_removed = function(publisher, person){
		this.get_view().remove_person(person);
	};
	this.group_was_added = function(publisher, text){
		this.get_view().add_group(text);
	};
	this.group_was_removed = function(publisher, text){
		this.get_view().remove_group(text);
	};
	this.event_view_will_show = function(view){
		if(this.delegate !== null){
			this.delegate.event_view_will_show(view);
		}
	};
	this.html_was_set = function(html){
		
	};
	this.clicked = function(e){
		this.delegate.event_view_was_clicked(this.get_view());
	};
	this.image_was_removed = function(publisher, info){
		this.get_view().selected_images.remove(info);
	};
	this.image_was_added= function(publisher, info){
		this.get_view().selected_images.add(info);
	};
};
sixd.controller.film_strip = function(view, options){
	sixd.controller.apply(this, [view, options]);
	var self = this;
	this.uploader = null;
	this.callback = options.callback;
	this.selected_images = new sixd.model.images();
	this.clicked = function(e){
		if(e.target.nodeName === 'IMG'){
			var image = new Image();
			image.src = e.target.src;
			if(!this.get_view().is_selected(e.target)){
				this.selected_images.add(image);
				this.publish('image_was_added', image);
			}else{
				this.selected_images.remove(image);				
				this.publish('image_was_removed', image);
			}
		}
		this.delegate.event_view_was_clicked(this.get_view());
	};
	this.get_view = function(){
		return this.views[0];
	};
	this.event_will_hide = function(view){
		this.post_controller = null;
	};
	this.event_will_show = function(view){
		this.delegate.event_view_will_show(view);
		var url = this.handle.href;
		sixd.get(url.replace('.html', '') + '.phtml', this.request_is_done, this);
	};
	this.request_is_done = function(request){
		this.get_view().set_html(request.responseText);
	};
	this.html_was_set = function(html){
		var form = SDDom.findFirst('form[enctype="multipart/form-data"]', this.get_view().container);
		this.uploader = new sixd.file_uploader(form, SDDom.findFirst('input[type="file"]', this.get_view().container), this.callback, this);
	};
	this.did_upload = function(response){
		this.get_view().add_image(response);
	};
}

sixd.app = function(controllers){
	var i = controllers.length;
	while(controller = controllers[--i]){
		controller.delegate = this;
	}
	this.controllers = controllers;
	this.event_view_was_clicked = function(view){
		for(var i = 0; i < this.controllers.length; i++){
			this.controllers[i].get_view().make_inactive(i);
		}
		view.make_active(i+1);
	};
	this.event_view_will_show = function(view){
		for(var i = 0; i < this.controllers.length; i++){
			this.controllers[i].get_view().make_inactive(i);
		}
		view.make_active(i+1);
	};
};
var fs_controller = null;
var post_controller = null;
var app = null;
sixd.main(function(e){
	if(SDDom('photos_link') && SDDom.findFirst('article.hentry', document)){
		fs_controller = new sixd.controller.film_strip(new sixd.view.film_strip(null), {handle_id: 'photos_link', callback: 'fs_controller.uploader.did_upload'});
		post_controller = new sixd.controller.post(new sixd.view.post('post_view', {tag: 'article'}), {handle_id: 'new_post_link'});
		fs_controller.add_subscriber(post_controller, 'image_was_added');
		fs_controller.add_subscriber(post_controller, 'image_was_removed');
		app = new sixd.app([fs_controller, post_controller]);
	}
});
