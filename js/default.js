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
	var i = ary.length;
	while(i--){
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
		if(elem.item){
			if(elem.length > 0){
				var e = elem.item(0);
				var parent = e.parentNode;
				do{
					parent.removeChild(e);
				}while(e = elem.item(elem.length));
			}

		}else{
			if(elem && elem.parentNode){
				elem.parentNode.removeChild(elem);
			}
		}
	}
	return elem;
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
	return elem;
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

SDDom.getHeight = function(elem){
	if(elem == window){
		elem = document.body;
	}
	return elem.clientHeight;
};
SDDom.getWidth = function(elem){
	if(elem === window){
		elem = document.body;
	}
	return elem.clientWidth;
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
	parent.insertBefore(elem, parent.firstChild);
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
	SDDom.append(document.body, SDDom.create('div', {id:id}));
	var self = UIView.apply(this, arguments);
	SDDom.setStyles({position: 'absolute', margin: '0 auto', display: 'block', top: 0, left: 0, width: '360px', height: '400px', border: 'solid 5px #fff', background: '#000', overflow: 'hidden'}, this.container);
	var bounds = {ux: SDDom.getWidth(window), lx: 0, uy: SDDom.getHeight(window), ly: 0};
	var handle_view = new UIView.TitleBar(this.container, {delegate: this, bounds: bounds, text: 'Photo Picker'});
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
		photo_upload_field = SDDom('photo');
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
		SDDom('photo').value = null;
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