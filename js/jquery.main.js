var sThemePath = '', sBlogName = '', sBaseUrl = '', _per_page = 20;
// init page
jQuery(function(){
	
	jQuery('script').each(function() {
	  if(jQuery(this).attr('src') !== undefined) {
		if(jQuery(this).attr('src').indexOf('theme_path') > -1) {
		  sThemePath = jQuery(this).attr('src').split('theme_path=')[1].split('&')[0];
		} // end if
		if(jQuery(this).attr('src').indexOf('blog_name') > -1) {
		  sBlogName = jQuery(this).attr('src').split('blog_name=')[1].split('&')[0];
		} // end if
		if(jQuery(this).attr('src').indexOf('baseurl') > -1) {
		  sBaseUrl = jQuery(this).attr('src').split('baseurl=')[1].split('&')[0];
		} // end if
	  } // end if
	});
	
	initPopups();
	new touchNav({
		navBlock: 'info-list' 
	});
	initFadeDrop();
	initPager();
	
	jQuery('.btn-submit').click(function(){
		processAjaxFilters(0);
		return false;
	});
	
	jQuery('#search-field').keypress(function(e) {
		var characterCode;
		if(e && e.which) { 
			e = e;
			characterCode = e.which;
		} else {
			e = event;
			characterCode = e.keyCode;
		}
		if(characterCode == 13) {
			processAjaxFilters(0);
			return false;
		} else {
			return true;
		}
	});
	
	jQuery('select[name="perpage"]').change(function(){
		processAjaxFilters(0);
	});
	
	jQuery('form#filter-form input[type="checkbox"]').click(function(){
		var val = jQuery(this).val();
		var name = jQuery(this).attr('name');
		var selector = '';
		switch(name) {
			case 'countries':
				selector = '#country_popup input[type=checkbox]';
				break;
			case 'regions':
				selector = '#region_popup input[type=checkbox]';
				break;
			case 'sectors':
				selector = '#sector_popup input[type=checkbox]';
				break;
			
		}
		if(jQuery(this).is(':checked')) {
			jQuery(selector).each(function(){
				if(jQuery(this).val()==val) jQuery(this).attr('checked', true);
			});
		} else {
		
			var keyword = jQuery('#s').val();
			if(keyword) {
				keyword = keyword.toLowerCase();
				var lbl = jQuery(this).parent().text();
				if(lbl.toLowerCase()==keyword) {
					jQuery('#s').val('');
				}
			}
			selector += ':checked';
			jQuery(selector).each(function(){
				if(jQuery(this).val()==val) jQuery(this).attr('checked', false);
			});
			
			
		}
		
		processAjaxFilters(0);
		
	});	
});

// init fade drop
function initFadeDrop(){
	var animSpeed = 300; //ms
	jQuery('ul.info-list > li').each(function(){
		var hold = jQuery(this);
		var drop = hold.find('div.drop');
		if (drop.length) {
			hold.hover(function(){
				drop.stop().css({opacity:0,display:'block'}).animate({opacity:1},{duration: animSpeed, queue: false})
			},function(){
				drop.stop().animate({opacity:0},{duration: animSpeed, queue: false,complete:function(){
					jQuery(this).css({display:'none'});
				}})
			})
		}
	});
};

//init the paging links
function initPager() {
	
	var total_results = jQuery('#total_results').val();
	var total_pages = Math.ceil(total_results/20); //default limit is 20
	jQuery('#paging > li > a').click(function(){
			var className = jQuery(this).parent().attr('class');
			var cur_page = parseInt(jQuery('#cur_page').html());
			var per_page = jQuery('select[name="perpage"]').val();
			_per_page = per_page;
			if(className=='link-prev') {
				var offset = (cur_page-2)*per_page;
				if(cur_page==1) return false;
			} else if(className=='link-next') {
				var offset = (cur_page)*per_page;
				if(cur_page==total_pages) return false;
			} else {
				
				var offset = parseInt(jQuery(this).html()) - 1;
				offset = offset*per_page;
			}
			processAjaxFilters(offset);
		});
}

// navigation accesibility module
function touchNav(opt) {
	this.options = {
		hoverClass: 'hover',
		menuItems: 'li',
		menuOpener: 'a',
		menuDrop: 'div',
		navBlock: null
	}
	for(var p in opt) {
		if(opt.hasOwnProperty(p)) {
			this.options[p] = opt[p];
		}
	}
	this.init();
}
touchNav.prototype = {
	init: function() {
		if(typeof this.options.navBlock === 'string') {
			this.menu = document.getElementById(this.options.navBlock);
		} else if(typeof this.options.navBlock === 'object') {
			this.menu = this.options.navBlock;
		}
		if(this.menu) {
			this.getElements();
			this.addEvents();
		}
	},
	getElements: function() {
		this.menuItems = this.menu.getElementsByTagName(this.options.menuItems);
	},
	getOpener: function(obj) {
		for(var i = 0; i < obj.childNodes.length; i++) {
			if(obj.childNodes[i].tagName && obj.childNodes[i].tagName.toLowerCase() == this.options.menuOpener.toLowerCase()) {
				return obj.childNodes[i];
			}
		}
		return false;
	},
	getDrop: function(obj) {
		for(var i = 0; i < obj.childNodes.length; i++) {
			if(obj.childNodes[i].tagName && obj.childNodes[i].tagName.toLowerCase() == this.options.menuDrop.toLowerCase()) {
				return obj.childNodes[i];
			}
		}
		return false;
	},
	addEvents: function() {
		// attach event handlers
		this.preventCurrentClick = true;
		for(var i = 0; i < this.menuItems.length; i++) {
			this.bind(function(i){
				var item = this.menuItems[i];
				// only for touch input devices
				if(this.isTouchDevice && this.getDrop(item)) {
					this.addHandler(this.getOpener(item), 'click', this.bind(this.clickHandler));
					this.addHandler(this.getOpener(item), 'touchstart', this.bind(function(){
						this.currentItem = item;
						this.currentLink = this.getOpener(item);
						this.pressHandler.apply(this, arguments);
					}));
				}
				// for desktop computers and touch devices
				this.addHandler(item, 'mouseover', this.bind(function(){
					this.currentItem = item;
					this.mouseoverHandler();
				}));
				this.addHandler(item, 'mouseout', this.bind(function(){
					this.currentItem = item;
					this.mouseoutHandler();
				}));
			})(i);
		}
		// hide dropdowns when clicking outside navigation
		if(this.isTouchDevice) {
			this.addHandler(document, 'touchstart', this.bind(this.clickOutsideHandler));
		}
	},
	mouseoverHandler: function() {
		this.addClass(this.currentItem, this.options.hoverClass);
	},
	mouseoutHandler: function() {
		this.removeClass(this.currentItem, this.options.hoverClass);
	},
	hideActiveDropdown: function() {
		for(var i = 0; i < this.menuItems.length; i++) {
			this.removeClass(this.menuItems[i], this.options.hoverClass);
		}
		this.activeParent = null;
	},
	pressHandler: function(e) {
		// hide previous drop (if active)
		if(this.currentItem != this.activeParent && !this.isParent(this.activeParent, this.currentLink)) {
			this.hideActiveDropdown();
		}
		// handle current drop
		this.activeParent = this.currentItem;
		if(this.hasClass(this.currentItem, this.options.hoverClass)) {
			this.preventCurrentClick = false;
		} else {
			this.preventEvent(e);
			this.preventCurrentClick = true;
			this.addClass(this.currentItem, this.options.hoverClass);
		}
	},
	clickHandler: function(e) {
		// prevent first click on link
		if(this.preventCurrentClick) {
			this.preventEvent(e);
		}
	},
	clickOutsideHandler: function(event) {
		var e = event.changedTouches ? event.changedTouches[0] : event;
		if(this.activeParent && !this.isParent(this.menu, e.target)) {
			this.hideActiveDropdown();
		}
	},
	preventEvent: function(e) {
		if(!e) e = window.event;
		if(e.preventDefault) e.preventDefault();
		e.returnValue = false;
	},
	isParent: function(parent, child) {
		while(child.parentNode) {
			if(child.parentNode == parent) {
				return true;
			}
			child = child.parentNode;
		}
		return false;
	},
	isTouchDevice: (function() {
		try {
			return ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;
		} catch (e) {
			return false;
		}
	}()),
	addHandler: function(object, event, handler) {
		if (object.addEventListener) object.addEventListener(event, handler, false);
		else if (object.attachEvent) object.attachEvent('on' + event, handler);
	},
	removeHandler: function(object, event, handler) {
		if (object.removeEventListener) object.removeEventListener(event, handler, false);
		else if (object.detachEvent) object.detachEvent('on' + event, handler);
	},
	hasClass: function(obj,cname) {
		return (obj.className ? obj.className.match(new RegExp('(\\s|^)'+cname+'(\\s|$)')) : false);
	},
	addClass: function(obj,cname) {
		if (!this.hasClass(obj,cname)) obj.className += " "+cname;
	},
	removeClass: function(obj,cname) {
		if (this.hasClass(obj,cname)) obj.className=obj.className.replace(new RegExp('(\\s|^)'+cname+'(\\s|$)'),' ');
	},
	bind: function(func, scope){
		var newScope = scope || this;
		return function() {
			return func.apply(newScope, arguments);
		}
	}
}

// Popups function
function initPopups() {
	var _zIndex = 1000;
	var _fadeSpeed = 350;
	var _faderOpacity = 0.7;
	var _faderBackground = '#1e75bb';
	var _faderId = 'lightbox-overlay';
	var _closeLink = 'a.btn-close, a.close, a.cancel, .close';
	var _fader;
	var _lightbox = null;
	var _ajaxClass = 'ajax-load';
	var _openers = jQuery('a.open-popup');
	var _page = jQuery(document);
	var _minWidth = 913;
	var _scroll = false;

	// init popup fader
	_fader = jQuery('#'+_faderId);
	if(!_fader.length) {
		_fader = jQuery('<div />');
		_fader.attr('id',_faderId);
		jQuery('body').append(_fader);
	}
	_fader.css({
		opacity:_faderOpacity,
		backgroundColor:_faderBackground,
		position:'absolute',
		overflow:'hidden',
		display:'none',
		top:0,
		left:0,
		zIndex:_zIndex
	});

	// IE6 iframe fix
	if(jQuery.browser.msie && jQuery.browser.version < 7) {
		if(!_fader.children().length) {
			var _frame = jQuery('<iframe src="javascript:false" frameborder="0" scrolling="no" />');
			_frame.css({
				opacity:0,
				width:'100%',
				height:'100%'
			});
			var _frameOverlay = jQuery('<div>');
			_frameOverlay.css({
				top:0,
				left:0,
				zIndex:1,
				opacity:0,
				background:'#5b5b5b',
				position:'absolute',
				width:'100%',
				height:'100%'
			});
			_fader.empty().append(_frame).append(_frameOverlay);
		}
	}

	// lightbox positioning function
	function positionLightbox() {
		if(_lightbox) {
			var _windowHeight = jQuery(window).height();
			var _windowWidth = jQuery(window).width();
			var _lightboxWidth = _lightbox.outerWidth();
			var _lightboxHeight = _lightbox.outerHeight();
			var _pageHeight = _page.height();

			if (_windowWidth < _minWidth) _fader.css('width',_minWidth);
				else _fader.css('width','100%');
			if (_windowHeight < _pageHeight) _fader.css('height',_pageHeight);
				else _fader.css('height',_windowHeight);

			_lightbox.css({
				position:'absolute',
				zIndex:(_zIndex+1)
			});
			

			// vertical position
			if (_windowHeight > _lightboxHeight) {
				if (jQuery.browser.msie && jQuery.browser.version < 7) {
					_lightbox.css({
						position:'absolute',
						top: parseInt(jQuery(window).scrollTop()) + (_windowHeight - _lightboxHeight) / 2
					});
				} else {
					_lightbox.css({
						position:'fixed',
						top: (_windowHeight - _lightboxHeight) / 2
					});
					
					if(_windowWidth < _minWidth) {
						_lightbox.css({
							position:'absolute'
						});
					}
				}
			} else {
				var _faderHeight = _fader.height();
				if(_faderHeight < _lightboxHeight) _fader.css('height',_lightboxHeight);
				if (!_scroll) {
					if (_faderHeight - _lightboxHeight > parseInt(jQuery(window).scrollTop())) {
						_faderHeight = parseInt(jQuery(window).scrollTop())
						_scroll = _faderHeight;
					} else {
						_scroll = _faderHeight - _lightboxHeight;
					}
				}
				_lightbox.css({
					position:'absolute',
					top: _scroll
				});
			}

			// horizontal position
			if (_fader.width() > _lightbox.outerWidth()) _lightbox.css({left:(_fader.width() - _lightbox.outerWidth()) / 2});
			else _lightbox.css({left: 0});
		}
	}

	// show/hide lightbox
	function toggleState(_state) {
		if(!_lightbox) return;
		if(_state) {
			_fader.fadeIn(_fadeSpeed,function(){
				_lightbox.fadeIn(_fadeSpeed);
			});
			_scroll = false;
			positionLightbox();
		} else {
			_lightbox.fadeOut(_fadeSpeed,function(){
				_fader.fadeOut(_fadeSpeed);
				_scroll = false;
			});
		}
	}

	// popup actions
	function initPopupActions(_obj) {
		if(!_obj.get(0).jsInit) {
			_obj.get(0).jsInit = true;
			// close link
			_obj.find(_closeLink).click(function(){
				_lightbox = _obj;
				toggleState(false);
				return false;
			});
		}
	}

	// lightbox openers
	_openers.each(function(){
		var _opener = jQuery(this);
		var _target = _opener.attr('href');

		// popup load type - ajax or static
		if(_opener.hasClass(_ajaxClass)) {
			_opener.click(function(){
				// ajax load
				if(jQuery('div[rel*="'+_target+'"]').length == 0) {
					jQuery.ajax({
						url: _target,
						type: "POST",
						dataType: "html",
						success: function(msg){
							// append loaded popup
							_lightbox = jQuery(msg);
							_lightbox.find('img').load(positionLightbox)
							_lightbox.attr('rel',_target).hide().css({
								position:'absolute',
								zIndex:(_zIndex+1),
								top: -9999,
								left: -9999
							});
							jQuery('body').append(_lightbox);

							// init js for lightbox
							initPopupActions(_lightbox);

							// show lightbox
							toggleState(true);
						},
						error: function(msg){
							alert('AJAX error!');
							return false;
						}
					});
				} else {
					_lightbox = jQuery('div[rel*="'+_target+'"]');
					toggleState(true);
				}
				return false;
			});
		} else {
			if(jQuery(_target).length) {
				// init actions for popup
				var _popup = jQuery(_target);
				initPopupActions(_popup);
					// open popup
					_opener.click(function(){
					if(_lightbox) {
						_lightbox.fadeOut(_fadeSpeed,function(){
							_lightbox = _popup.hide();
							toggleState(true);
						})
					} else {
						_lightbox = _popup.hide();
						toggleState(true);
					}
					return false;
				});
			}
		}
	});

	// event handlers
	jQuery(window).resize(positionLightbox);
	jQuery(window).scroll(positionLightbox);
	jQuery(document).keydown(function (e) {
		if (!e) evt = window.event;
		if (e.keyCode == 27) {
			toggleState(false);
		}
	})
	_fader.click(function(){
		if(!_fader.is(':animated')) toggleState(false);
		return false;
	})
}

/*
 * JavaScript Custom Forms 1.4.0
 */
jcf = {
	// global options
	modules: {},
	plugins: {},
	baseOptions: {
		labelActiveClass:'jcf-label-active',
		labelDisabledClass:'jcf-label-disabled',
		hiddenClass:'jcf-hidden',
		focusClass:'jcf-focus',
		wrapperTag: 'div'
	},
	// replacer function
	customForms: {
		setOptions: function(obj) {
			for(var p in obj) {
				if(obj.hasOwnProperty(p) && typeof obj[p] === 'object') {
					jcf.lib.extend(jcf.modules[p].prototype.defaultOptions, obj[p]);
				}
			}
		},
		replaceAll: function() {
			for(var k in jcf.modules) {
				var els = jcf.lib.queryBySelector(jcf.modules[k].prototype.selector);
				for(var i = 0; i<els.length; i++) {
					if(els[i].jcf) {
						// refresh form element state
						els[i].jcf.refreshState();
					} else {
						// replace form element
						if(!jcf.lib.hasClass(els[i], 'default') && jcf.modules[k].prototype.checkElement(els[i])) {
							new jcf.modules[k]({
								replaces:els[i]
							});
						}
					}
				}
			}
		},
		refreshAll: function() {
			for(var k in jcf.modules) {
				var els = jcf.lib.queryBySelector(jcf.modules[k].prototype.selector);
				for(var i = 0; i<els.length; i++) {
					if(els[i].jcf) {
						// refresh form element state
						els[i].jcf.refreshState();
					}
				}
			}
		},
		refreshElement: function(obj) {
			if(obj && obj.jcf) {
				obj.jcf.refreshState();
			}
		},
		destroyAll: function() {
			for(var k in jcf.modules) {
				var els = jcf.lib.queryBySelector(jcf.modules[k].prototype.selector);
				for(var i = 0; i<els.length; i++) {
					if(els[i].jcf) {
						els[i].jcf.destroy();
					}
				}
			}
		}
	},	
	// detect device type
	isTouchDevice: (function() {
		try {
			return ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;
		} catch (e) {
			return false;
		}
	}()),
	// define base module
	setBaseModule: function(obj) {
		jcf.customControl = function(opt){
			this.options = jcf.lib.extend({}, jcf.baseOptions, this.defaultOptions, opt);
			this.init();
		}
		for(var p in obj) {
			jcf.customControl.prototype[p] = obj[p];
		}
	},
	// add module to jcf.modules
	addModule: function(obj) {
		if(obj.name){
			// create new module proto class
			jcf.modules[obj.name] = function(){
				jcf.modules[obj.name].superclass.constructor.apply(this, arguments);
			}
			jcf.lib.inherit(jcf.modules[obj.name], jcf.customControl);
			for(var p in obj) {
				jcf.modules[obj.name].prototype[p] = obj[p]
			}
			// on create module
			jcf.modules[obj.name].prototype.onCreateModule();
			// make callback for exciting modules
			for(var mod in jcf.modules) {
				if(jcf.modules[mod] != jcf.modules[obj.name]) {
					jcf.modules[mod].prototype.onModuleAdded(jcf.modules[obj.name]);
				}
			}
		}
	},
	// add plugin to jcf.plugins
	addPlugin: function(obj) {
		if(obj && obj.name) {
			jcf.plugins[obj.name] = function() {
				this.init.apply(this, arguments);
			}
			for(var p in obj) {
				jcf.plugins[obj.name].prototype[p] = obj[p];
			}
		}
	},
	// miscellaneous init
	init: function(){
		this.eventPress = this.isTouchDevice ? 'touchstart' : 'mousedown';
		this.eventMove = this.isTouchDevice ? 'touchmove' : 'mousemove';
		this.eventRelease = this.isTouchDevice ? 'touchend' : 'mouseup';
		return this;
	}
}.init();

/*
 * Custom Form Control prototype
 */
jcf.setBaseModule({
	init: function(){
		if(this.options.replaces) {
			this.realElement = this.options.replaces;
			this.realElement.jcf = this;
			this.replaceObject();
		}
	},
	defaultOptions: {
		// default module options (will be merged with base options)
	},
	checkElement: function(el){
		return true; // additional check for correct form element
	},
	replaceObject: function(){
		this.createWrapper();
		this.attachEvents();
		this.fixStyles();
		this.setupWrapper();
	},
	createWrapper: function(){
		this.fakeElement = jcf.lib.createElement(this.options.wrapperTag);
		this.labelFor = jcf.lib.getLabelFor(this.realElement);
		jcf.lib.disableTextSelection(this.fakeElement);
		jcf.lib.addClass(this.realElement, jcf.baseOptions.hiddenClass);
	},
	attachEvents: function(){
		jcf.lib.event.add(this.realElement, 'focus', this.onFocusHandler, this);
		jcf.lib.event.add(this.realElement, 'blur', this.onBlurHandler, this);
		jcf.lib.event.add(this.fakeElement, 'click', this.onFakeClick, this);
		jcf.lib.event.add(this.fakeElement, jcf.eventPress, this.onFakePressed, this);
		jcf.lib.event.add(this.fakeElement, jcf.eventRelease, this.onFakeReleased, this);

		if(this.labelFor) {
			this.labelFor.jcf = this;
			jcf.lib.event.add(this.labelFor, 'click', this.onFakeClick, this);
			jcf.lib.event.add(this.labelFor, jcf.eventPress, this.onFakePressed, this);
			jcf.lib.event.add(this.labelFor, jcf.eventRelease, this.onFakeReleased, this);
		}
	},
	fixStyles: function() {
		// hide mobile webkit tap effect
		var tapStyle = 'rgba(255,255,255,0)';
		this.realElement.style.webkitTapHighlightColor = tapStyle; 
		this.fakeElement.style.webkitTapHighlightColor = tapStyle; 
		if(this.labelFor) {
			this.labelFor.style.webkitTapHighlightColor = tapStyle; 
		}
	},
	setupWrapper: function(){
		// implement in subclass
	},
	refreshState: function(){
		// implement in subclass
	},
	destroy: function() {
		if(this.fakeElement && this.fakeElement.parentNode) {
			this.fakeElement.parentNode.removeChild(this.fakeElement);
		}
		jcf.lib.removeClass(this.realElement, jcf.baseOptions.hiddenClass);
		this.realElement.jcf = null;
	},
	onFocus: function(){
		// emulated focus event
		jcf.lib.addClass(this.fakeElement,this.options.focusClass);
	},
	onBlur: function(cb){
		// emulated blur event
		jcf.lib.removeClass(this.fakeElement,this.options.focusClass);
	},
	onFocusHandler: function() {
		// handle focus loses
		if(this.focused) return;
		this.focused = true;
		
		// handle touch devices also
		if(jcf.isTouchDevice) {
			if(jcf.focusedInstance && jcf.focusedInstance.realElement != this.realElement) {
				jcf.focusedInstance.onBlur();
				jcf.focusedInstance.realElement.blur();
			}
			jcf.focusedInstance = this;
		}
		this.onFocus.apply(this, arguments);
	},
	onBlurHandler: function() {
		// handle focus loses
		if(!this.pressedFlag) {
			this.focused = false;
			this.onBlur.apply(this, arguments);
		}
	},
	onFakeClick: function(){
		if(jcf.isTouchDevice) {
			this.onFocus();
		} else {
			this.realElement.focus();
		}
	},
	onFakePressed: function(e){
		this.pressedFlag = true;
	},
	onFakeReleased: function(){
		this.pressedFlag = false;
	},
	onCreateModule: function(){
		// implement in subclass
	},
	onModuleAdded: function(module) {
		// implement in subclass
	},
	onControlReady: function() {
		// implement in subclass
	}
});

function processAjaxMap() {
	var url =  sThemePath + "/map_search.php",
	urlSep = "?", country_fltr = '', region_fltr = '', sector_fltr = '', budget_fltr = '', org_fltr = '',
	sep = "", selectedFltrs = [], isFilter = false;

	jQuery('#map_canvas').empty();
	
	var html = "<center><p>" +
			"<img src='"+sThemePath+"/images/ajax-loader.gif' alt='Loading results' />" +
			"</p></center>";
			
	
	jQuery('#map_canvas').html(html);
	
	jQuery('form#filter-form input[type=checkbox]:checked').each(function(){
		var control_name = jQuery(this).attr('name');
		var key = jQuery(this).val();
		switch(control_name) {
			case 'countries':
				if(!selectedFltrs['countries']) selectedFltrs['countries'] = [];
				if(country_fltr.length==0) sep = '';
				country_fltr += sep + key;
				sep = "|";
				var lbl = jQuery(this).parent().text();
				if(key!='All') selectedFltrs['countries'][key] = lbl;
				break;
			case 'regions':
				if(!selectedFltrs['regions']) selectedFltrs['regions'] = [];
				if(region_fltr.length==0) sep = '';
				region_fltr += sep + key;
				sep = "|";
				var lbl = jQuery(this).parent().text();
				if(key!='All') selectedFltrs['regions'][key] = lbl;
				break;
			case 'sectors':
				if(!selectedFltrs['sectors']) selectedFltrs['sectors'] = [];
				if(sector_fltr.length==0) sep = '';
				sector_fltr += sep + key;
				sep = "|";
				var lbl = jQuery(this).parent().text();
				if(key!='All') selectedFltrs['sectors'][key] = lbl;
				break;
			case 'budgets':
				if(!selectedFltrs['budgets']) selectedFltrs['budgets'] = [];
				if(budget_fltr.length==0) sep = '';
				budget_fltr += sep + key;
				sep = "|";
				var lbl = jQuery(this).parent().text();
				if(key!='All') selectedFltrs['budgets'][key] = lbl;
				break;
			case 'organisations':
				if(!selectedFltrs['organisations']) selectedFltrs['organisations'] = [];
				if(org_fltr.length==0) sep = '';
				org_fltr += sep + key;
				sep = "|";
				var lbl = jQuery(this).parent().text();
				if(key!='All') selectedFltrs['organisations'][key] = lbl;
				break;
		}
	});
	
	country_fltr = country_fltr.replace(/(All\|)|(\|All)|(All)/g, '');
	region_fltr = region_fltr.replace(/(All\|)|(\|All)|(All)/g, '');
	sector_fltr = sector_fltr.replace(/(All\|)|(\|All)|(All)/g, '');
	budget_fltr = budget_fltr.replace(/(All\|)|(\|All)|(All)/g, '');
	org_fltr = org_fltr.replace(/(All\|)|(\|All)|(All)/g, '');

	var keyword = jQuery('#s').val();
	if(keyword) {
		url +=  urlSep + "query=" + encodeURI(keyword);
		urlSep = "&";
	}
	
	
	if(country_fltr.length>0) {
		url +=  urlSep + "countries=" + country_fltr;
		urlSep = "&";
		isFilter = true;
	}
	if(region_fltr.length>0) {
		url +=  urlSep + "regions=" + region_fltr;
		urlSep = "&";
		isFilter = true;
	}
	if(sector_fltr.length>0) {
		url +=  urlSep + "sectors=" + sector_fltr;
		urlSep = "&";
		isFilter = true;
	}
	if(budget_fltr.length>0) {
		url +=  urlSep + "budgets=" + budget_fltr;
		urlSep = "&";
		isFilter = true;
	}
	if(org_fltr.length>0) {
		url +=  urlSep + "organisations=" + org_fltr;
		urlSep = "&";
		isFilter = true;
	}
	
	jQuery.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		success: function(result){
			
			var myLatLng = new google.maps.LatLng(-3.2013100765,-9.64460607187);
			var myOptions = {
				zoom : 2,
				center : myLatLng,
				mapTypeId : google.maps.MapTypeId.ROADMAP,
				scrollwheel: false,
				streetViewControl : false
			};

			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			if (!google.maps.Polygon.prototype.getBounds) {

				google.maps.Polygon.prototype.getBounds = function(latLng) {

						var bounds = new google.maps.LatLngBounds();
						var paths = this.getPaths();
						var path;
						
						for (var p = 0; p < paths.getLength(); p++) {
								path = paths.getAt(p);
								for (var i = 0; i < path.getLength(); i++) {
										bounds.extend(path.getAt(i));
								}
						}

						return bounds;
				}

			}
			var data = result['objects'];
			for(idx in data) {
				var lats = [];
				var lat_size =  data[idx]['path'].length;

				for (var t=0; t <lat_size; t++) {
					var inner = [];
					for (var i=0; i <data[idx]['path'][t].length; i++) {
						var lat = data[idx]['path'][t][i].split(',');
						inner.push(new google.maps.LatLng(lat[0], lat[1]));
					}
					lats.push(inner);
				}
				var polygon = new google.maps.Polygon({
					paths: lats,
					strokeColor: "#FFFFFF",
					strokeOpacity: 0.8,
					strokeWeight: 2,
					fillColor: "#F96B15",
					fillOpacity: 0.65,
					country: data[idx]['name'],
					total_cnt: data[idx]['total_cnt'],
					total_activities_url: "?countries="+idx,
					iso2 : idx
				});
				if(data.length==1) map.setCenter(polygon.getBounds().getCenter());
				polygon.setMap(map);
				google.maps.event.addListener(polygon, 'click', function(event){
					if (typeof currentPolygon != 'undefined') {
						currentPolygon.setOptions({fillColor: "#F96B15"});
					}
					this.setOptions({fillColor: "#2D6A98"});
					var keyword = jQuery('#search-field').val();
					
					if(keyword) {
						keyword = encodeURI(keyword);
					}
					
					var contentString = "" + 
					"<h2>" + 
						"<img src='"+sThemePath+"/images/flags/" + this.iso2.toLowerCase() + ".gif' />" +
						this.country + 
					"</h2>" +
					"<dl>" +
					"<dt>Total Activities:</dt>" +
					"<dd>" +
						"<a href='"+sBaseUrl+"/?page_id=16&query=" + keyword + "&countries=" + this.iso2 + "'>"+this.total_cnt+" project(s)</a>" +
					"</dd>" +
						"<a href='"+sBaseUrl+"/?page_id=16&query=" + keyword + "&countries=" + this.iso2 + "'>show all activities for this country</a>" +
					"</dl>";
					
					infowindow.setContent(contentString);
					infowindow.setPosition(event.latLng);
					infowindow.open(map);
					currentPolygon = this;
				});
			}
		},
		error: function(msg){
			alert('AJAX error!' + msg);
			return false;
		}
	});
}

function processAjaxFilters(offset) {
	processAjaxMap();
	var baseUrl = sBaseUrl, isFilter = false, selectedFltrs = [];
	jQuery('#info-table > tbody').empty();
	
	var html = "<tr>" +
			"<td colspan='5' align='center'>" +
			"<img src='"+sThemePath+"/images/ajax-loader.gif' alt='Loading results' />" +
			"</td>" +
			"</tr>";
			
	
	jQuery('#info-table > tbody').html(html);
	
	var url =  sThemePath + "/ajax_search.php";
	var urlSep = "?";
	var country_fltr = '', region_fltr = '', sector_fltr = '', budget_fltr = '', org_fltr = '';
	var sep = "";
	jQuery('form#filter-form input[name="countries"]:checked').each(function(){
		if(!selectedFltrs['countries']) selectedFltrs['countries'] = [];
		var key = jQuery(this).val();
		country_fltr += sep + key;
		var lbl = jQuery(this).next().text();
		selectedFltrs['countries'][key] = lbl;
		sep = "|";
		isFilter = true;
	});
	
	sep = "";
	jQuery('form#filter-form input[name="regions"]:checked').each(function(){
		if(!selectedFltrs['regions']) selectedFltrs['regions'] = [];
		var key = jQuery(this).val();
		region_fltr += sep + key;
		var lbl = jQuery(this).next().text();
		selectedFltrs['regions'][key] = lbl;
		sep = "|";
		isFilter = true;
	});
	
	sep = "";
	jQuery('form#filter-form input[name="sectors"]:checked').each(function(){
		if(!selectedFltrs['sectors']) selectedFltrs['sectors'] = [];
		var key = jQuery(this).val();
		sector_fltr += sep + key;
		var lbl = jQuery(this).next().text();
		selectedFltrs['sectors'][key] = lbl;
		sep = "|";
		isFilter = true;
	});
	
	sep = "";
	jQuery('form#filter-form input[name="budgets"]:checked').each(function(){
		if(!selectedFltrs['budgets']) selectedFltrs['budgets'] = [];
		var key = jQuery(this).val();
		budget_fltr += sep + key;
		var lbl = jQuery(this).next().text();
		selectedFltrs['budgets'][key] = lbl;
		sep = "|";
		isFilter = true;
	});
	
	sep = "";
	jQuery('form#filter-form input[name="organisations"]:checked').each(function(){
		if(!selectedFltrs['organisations']) selectedFltrs['organisations'] = [];
		var key = jQuery(this).val();
		org_fltr += sep + key;
		var lbl = jQuery(this).next().text();
		selectedFltrs['organisations'][key] = lbl;
		sep = "|";
		isFilter = true;
	});
	
	var keyword = jQuery('#search-field').val();
	if(keyword) {
		url +=  urlSep + "query=" + encodeURI(keyword);
		urlSep = "&";
	}
	
	
	if(country_fltr.length>0) {
		url +=  urlSep + "countries=" + country_fltr;
		urlSep = "&";
	}
	if(region_fltr.length>0) {
		url +=  urlSep + "regions=" + region_fltr;
		urlSep = "&";
	}
	if(sector_fltr.length>0) {
		url +=  urlSep + "sectors=" + sector_fltr;
		urlSep = "&";
	}
	if(budget_fltr.length>0) {
		url +=  urlSep + "budgets=" + budget_fltr;
		urlSep = "&";
	}
	if(org_fltr.length>0) {
		url +=  urlSep + "organisations=" + org_fltr;
		urlSep = "&";
	}
	
	var per_page = jQuery('select[name="perpage"]').val();
	
	url +=  urlSep + "limit=" + per_page;
	urlSep = "&";
	
	url +=  urlSep + "offset=" + offset;
	urlSep = "&";
	
	jQuery.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		success: function(data){
			
			var meta = data.meta;
			var objects = data.objects;
			
			applyResults(meta, objects);
			
		},
		error: function(msg){
			alert('AJAX error!' + msg);
			return false;
		}
	});
	
	if(isFilter) {
		applyFilterHTML(selectedFltrs);
	} else {
		jQuery('#info-list').empty().hide();
	}
}


function applyFilterHTML(selected) {
	jQuery('#info-list').empty();
	var baseUrl = sBaseUrl;
	var html = '';
	var sep = '';
	if(!jQuery.isEmptyObject(selected.organisations)) {
		html += '<li>';
		for(key in selected.organisations) {
			var lbl = selected.organisations[key];
			if(lbl.length > 10) {
				lbl = lbl.substring(0,10)+"...";
			}
			html += '<a href="#" title="'+selected.organisations[key]+'">'+lbl+'</a>';
			sep = ', ';
			delete selected.organisations[key];
			break;
		}
		html += '<div class="drop">' +
					'<ul>' +
						'<li class="remove"><a href="#" id="organisations">Clear</a></li>';
						
		for(key in selected.organisations) {
			var lbl = selected.organisations[key];
			if(lbl.length > 10) {
				lbl = lbl.substring(0,10)+"...";
			}
			html += '<li><a href="#" title="'+selected.organisations[key]+'">'+lbl+'</a></li>';
		}			
		html += '</ul></div></li>';
	}
	sep = '';
	if(!jQuery.isEmptyObject(selected.countries)) {
		html += '<li>';
		for(key in selected.countries) {
			var lbl = selected.countries[key];
			html += '<a href="#">'+lbl+'</a>';
			sep = ', ';
			delete selected.countries[key];
			break;
		}
		html += '<div class="drop">' +
					'<ul>' +
						'<li class="remove"><a href="#" id="countries">Clear</a></li>';
						
		for(key in selected.countries) {
			var lbl = selected.countries[key];
			html += '<li><a href="#">'+lbl+'</a></li>';
		}			
		html += '</ul></div></li>';
	}
	sep = '';
	if(!jQuery.isEmptyObject(selected.regions)) {
		html += '<li>';
		for(key in selected.regions) {
			var lbl = selected.regions[key];
			html += '<a href="#">'+lbl+'</a>';
			sep = ', ';
			delete selected.regions[key];
			break;
		}
		html += '<div class="drop">' +
					'<ul>' +
						'<li class="remove"><a href="#" id="regions">Clear</a></li>';
						
		for(key in selected.regions) {
			var lbl = selected.regions[key];
			html += '<li><a href="#">'+lbl+'</a></li>';
		}			
		html += '</ul></div></li>';
	}
	sep = '';
	if(!jQuery.isEmptyObject(selected.sectors)) {
		html += '<li>';
		for(key in selected.sectors) {
			var lbl = selected.sectors[key];
			html += '<a href="#">'+lbl+'</a>';
			sep = ', ';
			delete selected.sectors[key];
			break;
		}
		html += '<div class="drop">' +
					'<ul>' +
						'<li class="remove"><a href="#" id="sectors">Clear</a></li>';
						
		for(key in selected.sectors) {
			var lbl = selected.sectors[key];
			html += '<li><a href="#">'+lbl+'</a></li>';
		}			
		html += '</ul></div></li>';
	}
	sep = '';
	if(!jQuery.isEmptyObject(selected.budgets)) {
		html += '<li>';
		for(key in selected.budgets) {
			var lbl = selected.budgets[key];
			html += '<a href="#">'+lbl+'</a>';
			sep = ', ';
			delete selected.budgets[key];
			break;
		}
		html += '<div class="drop">' +
					'<ul>' +
						'<li class="remove"><a href="#" id="budgets">Clear</a></li>';
						
		for(key in selected.budgets) {
			var lbl = selected.budgets[key];
			html += '<li><a href="#">'+lbl+'</a></li>';
		}			
		html += '</ul></div></li>';
	}
	html += '</ul>';
	jQuery('#info-list').html(html);
	jQuery('#info-list').show();
	
	new touchNav({
		navBlock: 'info-list' 
	});
	initFadeDrop();
	
	
	jQuery("#info-list li.remove a").click(function(){
		var id = jQuery(this).attr('id');
		var selector = 'form#filter-form input[name="'+id+'"]';
		
		jQuery(selector).attr('checked', false);
		processAjaxFilters(0);
		return false;
	});
}

function applyResults(meta, objects) {
	
	var limit = meta.limit,
		offset = meta.offset,
		total_count = meta.total_count,
		baseUrl = sBaseUrl;
		
	var html = "";
	if(total_count>0) {
		
		for(idx in objects) {
			var project = objects[idx];
			var description = project.descriptions[0].description;
			if(description.length > 70) {
				description = description.substring(0,70)+"...";
			}
			html += "<tr>" +
					"<td class='col1'>" +
					"<strong class='title'><a href='"+baseUrl+"/?page_id=20&id="+project.iati_identifier+"'>"+project.titles[0].title+"</a></strong>" +
					"<p>"+description+"</p>" +
					"</td>" +
					"<td>";
			var sep = '';
			for(i in project.recipient_country) {
				html += sep + "<a hrerf='?page_id=16&countries=" + project.recipient_country[i].iso + "'>" + project.recipient_country[i].name + "</a>";
				sep = ', ';
			}
			var currency = '€';
			if(project.activity_transactions.length > 0) {
				for(i in project.activity_transactions) {
					if(project.activity_transactions[i].currency=='USD') currency = '$';
					if(project.activity_transactions[i].currency=='GBP') currency = '£';
					break;
				}
			}
			
			var project_budget = '';
			if(project.statistics && project.statistics.total_budget) {
				project_budget = format_number(project.statistics.total_budget);
			}
			
			html += "</td>" +
					"<td>"+project.start_actual+"</td>" +
					"<td>"+currency+" " + project_budget + "</td>" +
					"<td class='last'>";
					
			var sep = '';
			for(i in project.activity_sectors) {
				html += sep + "<a hrerf='?page_id=16&sectors=" + project.activity_sectors[i].code + "'>" + project.activity_sectors[i].name + "</a>";
				sep = ', ';
			}
			html +=	"</td>" +
					"</tr>";
			
		}
		
		//fix the paging 
		var per_page = jQuery('select[name="perpage"]').val();
		var total_pages = Math.ceil(total_count/limit);
		var cur_page = offset/limit + 1;
		var paging_block = "<li class='link-prev'><a href='javascript:#'>previous</a></li>";
		var page_limit = 7;
		var fromPage = cur_page - 3;
		if(fromPage<=0) fromPage = 1;
		var loop_limit = (total_pages>page_limit?(fromPage + page_limit - 1):total_pages);
		

		for(i=fromPage; i<=loop_limit; i++) {
			paging_block += "<li>";
			if(cur_page==i) {
				paging_block += "<strong id='cur_page'>"+i+"</strong>";
			} else {
				paging_block += "<a href='javascript:#'>"+i+"</a>";
			}
			paging_block += "</li>";
		}
		if((fromPage+loop_limit)<(total_pages-3)) {
			if(total_pages>page_limit) {
				paging_block += "<li>...</li>";
			}
			
			for(i=total_pages-2; i<=total_pages; i++) {
				paging_block += "<li>";
				
				paging_block += "<a href='javascript:#'>"+i+"</a>";
			
				paging_block += "</li>";
			}
		} else {

			for(i=loop_limit+1; i<=total_pages; i++) {
				paging_block += "<li>";
				if(cur_page==i) {
					paging_block += "<strong id='cur_page'>"+i+"</strong>";
				} else {
					paging_block += "<a href='javascript:#'>"+i+"</a>";
				}
				paging_block += "</li>";
			}
			
		}
		
		paging_block += "<li class='link-next'><a href='javascript:#'>next</a></li>";
		jQuery('#paging').empty().html(paging_block);
		
		jQuery('#paging > li > a').click(function(){
			var className = jQuery(this).parent().attr('class');
			var cur_page = parseInt(jQuery('#cur_page').html());
			var per_page = jQuery('select[name="perpage"]').val();
			if(className=='link-prev') {
				var offset = (cur_page-2)*per_page;
				if(cur_page==1) return false;
			} else if(className=='link-next') {
				var offset = (cur_page)*per_page;
				if(cur_page==total_pages) return false;
			} else {
				
				var offset = parseInt(jQuery(this).html()) - 1;
				offset = offset*per_page;
			}
			processAjaxFilters(offset);
			return false;
		});
	
	} else {
		html += "<tr>" +
				"<td class='col1' colspan='4'>" +
				"No results" +
				"</td>" +
				"<td class='last'></td>" +
				"</tr>";
		jQuery('#paging-block').hide();
			
	}
	
	jQuery('.title>mark').html(total_count);
	
	jQuery('#info-table > tbody').empty();
	jQuery('#info-table > tbody').html(html);
}


function format_number(format) {
  
	var s = format.split('.');
	var parts = "";
	if(s[0].length>3) {
		parts = "." + s[0].substring(s[0].length-3, s[0].length);
		s[0] = s[0].substring(0, s[0].length-3);
		if(s[0].length>3) {
			parts = "." + s[0].substring(s[0].length-3, s[0].length) + parts;
			s[0] = s[0].substring(0, s[0].length-3);
			if(s[0].length>3) {
				parts = "." + s[0].substring(s[0].length-3, s[0].length) + parts;
				s[0] = s[0].substring(0, s[0].length-3);
			} else {
				parts = s[0] + parts;
			}
		} else {
			parts = s[0] + parts;
		}
	} else {
		parts = s[0] + parts;
	}
	
	var ret = parts;
	
	if(s.length>1) {
		if(s[1]!="00") {
			ret += "," + s[1];
		}
	}
	
	return ret;
};

/*
 * JCF Utility Library
 */
jcf.lib = {
	bind: function(func, scope){
		return function() {
			return func.apply(scope, arguments);
		}
	},
	browser: (function() {
		var ua = navigator.userAgent.toLowerCase(), res = {},
		match = /(webkit)[ \/]([\w.]+)/.exec(ua) || /(opera)(?:.*version)?[ \/]([\w.]+)/.exec(ua) ||
				/(msie) ([\w.]+)/.exec(ua) || ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+))?/.exec(ua) || [];
		res[match[1]] = true;
		res.version = match[2] || "0";
		res.safariMac = ua.indexOf('mac') != -1 && ua.indexOf('safari') != -1;
		return res;
	})(),
	getOffset: function (obj) {
		if (obj.getBoundingClientRect) {
			var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft;
			var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
			var clientLeft = document.documentElement.clientLeft || document.body.clientLeft || 0;
			var clientTop = document.documentElement.clientTop || document.body.clientTop || 0;
			return {
				top:Math.round(obj.getBoundingClientRect().top + scrollTop - clientTop),
				left:Math.round(obj.getBoundingClientRect().left + scrollLeft - clientLeft)
			}
		} else {
			var posLeft = 0, posTop = 0;
			while (obj.offsetParent) {posLeft += obj.offsetLeft; posTop += obj.offsetTop; obj = obj.offsetParent;}
			return {top:posTop,left:posLeft};
		}
	},
	getScrollTop: function() {
		return window.pageYOffset || document.documentElement.scrollTop;
	},
	getScrollLeft: function() {
		return window.pageXOffset || document.documentElement.scrollLeft;
	},
	getWindowWidth: function(){
		return document.compatMode=='CSS1Compat' ? document.documentElement.clientWidth : document.body.clientWidth;
	},
	getWindowHeight: function(){
		return document.compatMode=='CSS1Compat' ? document.documentElement.clientHeight : document.body.clientHeight;
	},
	getStyle: function(el, prop) {
		if (document.defaultView && document.defaultView.getComputedStyle) {
			return document.defaultView.getComputedStyle(el, null)[prop];
		} else if (el.currentStyle) {
			return el.currentStyle[prop];
		} else {
			return el.style[prop];
		}
	},
	getParent: function(obj, selector) {
		while(obj.parentNode && obj.parentNode != document.body) {
			if(obj.parentNode.tagName.toLowerCase() == selector.toLowerCase()) {
				return obj.parentNode;
			}
			obj = obj.parentNode;
		}
		return false;
	},
	isParent: function(child, parent) {
		while(child.parentNode) {
			if(child.parentNode === parent) {
				return true;
			}
			child = child.parentNode;
		}
		return false;
	},
	getLabelFor: function(object) {
		if(jcf.lib.getParent(object,'label')) {
			return object.parentNode;
		} else if(object.id) {
			return jcf.lib.queryBySelector('label[for="' + object.id + '"]')[0];
		}
	},
	disableTextSelection: function(el){
		el.setAttribute('unselectable', 'on');
		el.style.MozUserSelect = 'none';
		el.style.WebkitUserSelect = 'none';
		el.style.UserSelect = 'none';
		el.onselectstart = function() {return false};
	},
	enableTextSelection: function(el) {
		el.removeAttribute('unselectable');
		el.style.MozUserSelect = '';
		el.style.WebkitUserSelect = '';
		el.style.UserSelect = '';
		el.onselectstart = null;
	},
	queryBySelector: function(selector, scope){
		return this.getElementsBySelector(selector, scope);
	},
	prevSibling: function(node) {
		while(node = node.previousSibling) if(node.nodeType == 1) break;
		return node;
	},
	nextSibling: function(node) {
		while(node = node.nextSibling) if(node.nodeType == 1) break;
		return node;
	},
	fireEvent: function(element,event) {
		if (document.createEventObject){
			var evt = document.createEventObject();
			return element.fireEvent('on'+event,evt)
		}
		else{
			var evt = document.createEvent('HTMLEvents');
			evt.initEvent(event, true, true );
			return !element.dispatchEvent(evt);
		}
	},
	isParent: function(p, c) {
		while(c.parentNode) {
			if(p == c) {
				return true;
			}
			c = c.parentNode;
		}
		return false;
	},
	inherit: function(Child, Parent) {
		var F = function() { }
		F.prototype = Parent.prototype
		Child.prototype = new F()
		Child.prototype.constructor = Child
		Child.superclass = Parent.prototype
	},
	extend: function(obj) {
		for(var i = 1; i < arguments.length; i++) {
			for(var p in arguments[i]) {
				if(arguments[i].hasOwnProperty(p)) {
					obj[p] = arguments[i][p];
				}
			}
		}
		return obj;
	},
	hasClass: function (obj,cname) {
		return (obj.className ? obj.className.match(new RegExp('(\\s|^)'+cname+'(\\s|$)')) : false);
	},
	addClass: function (obj,cname) {
		if (!this.hasClass(obj,cname)) obj.className += " "+cname;
	},
	removeClass: function (obj,cname) {
		if (this.hasClass(obj,cname)) obj.className=obj.className.replace(new RegExp('(\\s|^)'+cname+'(\\s|$)'),' ');
	},
	toggleClass: function(obj, cname, condition) {
		if(condition) this.addClass(obj, cname); else this.removeClass(obj, cname);
	},
	createElement: function(tagName, options) {
		var el = document.createElement(tagName);
		for(var p in options) {
			if(options.hasOwnProperty(p)) {
				switch (p) {
					case 'class': el.className = options[p]; break;
					case 'html': el.innerHTML = options[p]; break;
					case 'style': this.setStyles(el, options[p]); break;
					default: el.setAttribute(p, options[p]);
				}
			}
		}
		return el;
	},
	setStyles: function(el, styles) {
		for(var p in styles) {
			if(styles.hasOwnProperty(p)) {
				switch (p) {
					case 'float': el.style.cssFloat = styles[p]; break;
					case 'opacity': el.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity='+styles[p]*100+')'; el.style.opacity = styles[p]; break;
					default: el.style[p] = (typeof styles[p] === 'undefined' ? 0 : styles[p]) + (typeof styles[p] === 'number' ? 'px' : '');
				}
			}
		}
		return el;
	},
	getInnerWidth: function(el) {
		return el.offsetWidth - (parseInt(this.getStyle(el,'paddingLeft')) || 0) - (parseInt(this.getStyle(el,'paddingRight')) || 0);
	},
	getInnerHeight: function(el) {
		return el.offsetHeight - (parseInt(this.getStyle(el,'paddingTop')) || 0) - (parseInt(this.getStyle(el,'paddingBottom')) || 0);
	},
	getAllClasses: function(cname, prefix, skip) {
		if(!skip) skip = '';
		if(!prefix) prefix = '';
		return cname ? cname.replace(new RegExp('(\\s|^)'+skip+'(\\s|$)'),' ').replace(/[\s]*([\S]+)+[\s]*/gi,prefix+"$1 ") : '';
	},
	getElementsBySelector: function(selector, scope) {
		if(typeof document.querySelectorAll === 'function') {
			return (scope || document).querySelectorAll(selector);
		}
		var selectors = selector.split(',');
		var resultList = [];
		for(var s = 0; s < selectors.length; s++) {
			var currentContext = [scope || document];
			var tokens = selectors[s].replace(/^\s+/,'').replace(/\s+$/,'').split(' ');
			for (var i = 0; i < tokens.length; i++) {
				token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');
				if (token.indexOf('#') > -1) {
					var bits = token.split('#'), tagName = bits[0], id = bits[1];
					var element = document.getElementById(id);
					if (tagName && element.nodeName.toLowerCase() != tagName) {
						return [];
					}
					currentContext = [element];
					continue;
				}
				if (token.indexOf('.') > -1) {
					var bits = token.split('.'), tagName = bits[0] || '*', className = bits[1], found = [], foundCount = 0;
					for (var h = 0; h < currentContext.length; h++) {
						var elements;
						if (tagName == '*') {
							elements = currentContext[h].getElementsByTagName('*');
						} else {
							elements = currentContext[h].getElementsByTagName(tagName);
						}
						for (var j = 0; j < elements.length; j++) {
							found[foundCount++] = elements[j];
						}
					}
					currentContext = [];
					var currentContextIndex = 0;
					for (var k = 0; k < found.length; k++) {
						if (found[k].className && found[k].className.match(new RegExp('(\\s|^)'+className+'(\\s|$)'))) {
							currentContext[currentContextIndex++] = found[k];
						}
					}
					continue;
				}
				if (token.match(/^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/)) {
					var tagName = RegExp.$1 || '*', attrName = RegExp.$2, attrOperator = RegExp.$3, attrValue = RegExp.$4;
					if(attrName.toLowerCase() == 'for' && this.browser.msie && this.browser.version < 8) {
						attrName = 'htmlFor';
					}
					var found = [], foundCount = 0;
					for (var h = 0; h < currentContext.length; h++) {
						var elements;
						if (tagName == '*') {
							elements = currentContext[h].getElementsByTagName('*');
						} else {
							elements = currentContext[h].getElementsByTagName(tagName);
						}
						for (var j = 0; j < elements.length; j++) {
							found[foundCount++] = elements[j];
						}
					}
					currentContext = [];
					var currentContextIndex = 0, checkFunction;
					switch (attrOperator) {
						case '=': checkFunction = function(e) { return (e.getAttribute(attrName) == attrValue) }; break;
						case '~': checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('(\\s|^)'+attrValue+'(\\s|$)'))) }; break;
						case '|': checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('^'+attrValue+'-?'))) }; break;
						case '^': checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) == 0) }; break;
						case '$': checkFunction = function(e) { return (e.getAttribute(attrName).lastIndexOf(attrValue) == e.getAttribute(attrName).length - attrValue.length) }; break;
						case '*': checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) > -1) }; break;
						default : checkFunction = function(e) { return e.getAttribute(attrName) };
					}
					currentContext = [];
					var currentContextIndex = 0;
					for (var k = 0; k < found.length; k++) {
						if (checkFunction(found[k])) {
							currentContext[currentContextIndex++] = found[k];
						}
					}
					continue;
				}
				tagName = token;
				var found = [], foundCount = 0;
				for (var h = 0; h < currentContext.length; h++) {
					var elements = currentContext[h].getElementsByTagName(tagName);
					for (var j = 0; j < elements.length; j++) {
						found[foundCount++] = elements[j];
					}
				}
				currentContext = found;
			}
			resultList = [].concat(resultList,currentContext);
		}
		return resultList;
	},
	scrollSize: (function(){
		var content, hold, sizeBefore, sizeAfter;
		function buildSizer(){
			if(hold) removeSizer();
			content = document.createElement('div');
			hold = document.createElement('div');
			hold.style.cssText = 'position:absolute;overflow:hidden;width:100px;height:100px';
			hold.appendChild(content);
			document.body.appendChild(hold);
		}
		function removeSizer(){
			document.body.removeChild(hold);
			hold = null;
		}
		function calcSize(vertical) {
			buildSizer();
			content.style.cssText = 'height:'+(vertical ? '100%' : '200px');
			sizeBefore = (vertical ? content.offsetHeight : content.offsetWidth);
			hold.style.overflow = 'scroll'; content.innerHTML = 1;
			sizeAfter = (vertical ? content.offsetHeight : content.offsetWidth);
			if(vertical && hold.clientHeight) sizeAfter = hold.clientHeight;
			removeSizer();
			return sizeBefore - sizeAfter;
		}
		return {
			getWidth:function(){
				return calcSize(false);
			},
			getHeight:function(){
				return calcSize(true)
			}
		}
	}()),
	domReady: function (handler){
		var called = false
		function ready() {
			if (called) return;
			called = true;
			handler();
		}
		if (document.addEventListener) {
			document.addEventListener("DOMContentLoaded", ready, false);
		} else if (document.attachEvent) {
			if (document.documentElement.doScroll && window == window.top) {
				function tryScroll(){
					if (called) return
					if (!document.body) return
					try {
						document.documentElement.doScroll("left")
						ready()
					} catch(e) {
						setTimeout(tryScroll, 0)
					}
				}
				tryScroll()
			}
			document.attachEvent("onreadystatechange", function(){
				if (document.readyState === "complete") {
					ready()
				}
			})
		}
		if (window.addEventListener) window.addEventListener('load', ready, false)
		else if (window.attachEvent) window.attachEvent('onload', ready)
	},
	event: (function(){
		var guid = 0;
		function fixEvent(e) {
			e = e || window.event;
			if (e.isFixed) {
				return e;
			}
			e.isFixed = true; 
			e.preventDefault = e.preventDefault || function(){this.returnValue = false}
			e.stopPropagation = e.stopPropagaton || function(){this.cancelBubble = true}
			if (!e.target) {
				e.target = e.srcElement
			}
			if (!e.relatedTarget && e.fromElement) {
				e.relatedTarget = e.fromElement == e.target ? e.toElement : e.fromElement;
			}
			if (e.pageX == null && e.clientX != null) {
				var html = document.documentElement, body = document.body;
				e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
				e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
			}
			if (!e.which && e.button) {
				e.which = e.button & 1 ? 1 : (e.button & 2 ? 3 : (e.button & 4 ? 2 : 0));
			}
			if(e.type === "DOMMouseScroll" || e.type === 'mousewheel') {
				e.mWheelDelta = 0;
				if (e.wheelDelta) {
					e.mWheelDelta = e.wheelDelta/120;
				} else if (e.detail) {
					e.mWheelDelta = -e.detail/3;
				}
			}
			return e;
		}
		function commonHandle(event, customScope) {
			event = fixEvent(event);
			var handlers = this.events[event.type];
			for (var g in handlers) {
				var handler = handlers[g];
				var ret = handler.call(customScope || this, event);
				if (ret === false) {
					event.preventDefault()
					event.stopPropagation()
				}
			}
		}
		var publicAPI = {
			add: function(elem, type, handler, forcedScope) {
				if (elem.setInterval && (elem != window && !elem.frameElement)) {
					elem = window;
				}
				if (!handler.guid) {
					handler.guid = ++guid;
				}
				if (!elem.events) {
					elem.events = {};
					elem.handle = function(event) {
						return commonHandle.call(elem, event);
					}
				}
				if (!elem.events[type]) {
					elem.events[type] = {};
					if (elem.addEventListener) elem.addEventListener(type, elem.handle, false);
					else if (elem.attachEvent) elem.attachEvent("on" + type, elem.handle);
					if(type === 'mousewheel') {
						publicAPI.add(elem, 'DOMMouseScroll', handler, forcedScope);
					}
				}
				var fakeHandler = jcf.lib.bind(handler, forcedScope);
				fakeHandler.guid = handler.guid;
				elem.events[type][handler.guid] = forcedScope ? fakeHandler : handler;
			},
			remove: function(elem, type, handler) {
				var handlers = elem.events && elem.events[type];
				if (!handlers) return;
				delete handlers[handler.guid];
				for(var any in handlers) return;
				if (elem.removeEventListener) elem.removeEventListener(type, elem.handle, false);
				else if (elem.detachEvent) elem.detachEvent("on" + type, elem.handle);
				delete elem.events[type];
				for (var any in elem.events) return;
				try {
					delete elem.handle;
					delete elem.events;
				} catch(e) {
					if(elem.removeAttribute) {
						elem.removeAttribute("handle");
						elem.removeAttribute("events");
					}
				}
				if(type === 'mousewheel') {
					publicAPI.remove(elem, 'DOMMouseScroll', handler);
				}
			}
		}
		return publicAPI;
	}())
}

// custom radio module
jcf.addModule({
	name:'radio',
	selector: 'input[type="radio"]',
	defaultOptions: {
		wrapperClass:'rad-area',
		focusClass:'rad-focus',
		checkedClass:'rad-checked',
		uncheckedClass:'rad-unchecked',
		disabledClass:'rad-disabled',
		radStructure:'<span></span>'
	},
	getRadioGroup: function(item){
		var name = item.getAttribute('name');
		if(name) {
			return jcf.lib.queryBySelector('input[name="'+name+'"]', jcf.lib.getParent('form'));
		} else {
			return [item];
		}
	},
	setupWrapper: function(){
		jcf.lib.addClass(this.fakeElement, this.options.wrapperClass);
		this.fakeElement.innerHTML = this.options.radStructure;
		this.realElement.parentNode.insertBefore(this.fakeElement, this.realElement);
		this.refreshState();
		this.addEvents();
	},
	addEvents: function(){
		jcf.lib.event.add(this.fakeElement, 'click', this.toggleRadio, this);
		if(this.labelFor) {
			jcf.lib.event.add(this.labelFor, 'click', this.toggleRadio, this);
		}
	},
	onFocus: function(e) {
		jcf.modules[this.name].superclass.onFocus.apply(this, arguments);
		setTimeout(jcf.lib.bind(function(){
			this.refreshState();
		},this),10);
	},
	toggleRadio: function(){
		if(!this.realElement.disabled) {
			this.realElement.checked = true;
		}
		this.refreshState();
	},
	refreshState: function(){
		var els = this.getRadioGroup(this.realElement);
		for(var i = 0; i < els.length; i++) {
			var curEl = els[i].jcf;
			if(curEl) {
				if(curEl.realElement.checked) {
					jcf.lib.addClass(curEl.fakeElement, curEl.options.checkedClass);
					jcf.lib.removeClass(curEl.fakeElement, curEl.options.uncheckedClass);
					if(curEl.labelFor) {
						jcf.lib.addClass(curEl.labelFor, curEl.options.labelActiveClass);
					}
				} else {
					jcf.lib.removeClass(curEl.fakeElement, curEl.options.checkedClass);
					jcf.lib.addClass(curEl.fakeElement, curEl.options.uncheckedClass);
					if(curEl.labelFor) {
						jcf.lib.removeClass(curEl.labelFor, curEl.options.labelActiveClass);
					}
				}
				if(curEl.realElement.disabled) {
					jcf.lib.addClass(curEl.fakeElement, curEl.options.disabledClass);
					if(curEl.labelFor) {
						jcf.lib.addClass(curEl.labelFor, curEl.options.labelDisabledClass);
					}
				} else {
					jcf.lib.removeClass(curEl.fakeElement, curEl.options.disabledClass);
					if(curEl.labelFor) {
						jcf.lib.removeClass(curEl.labelFor, curEl.options.labelDisabledClass);
					}
				}
			}
		}
	}
});

// custom checkbox module
/*
jcf.addModule({
	name:'checkbox',
	selector:'input[type="checkbox"]',
	defaultOptions: {
		wrapperClass:'chk-area',
		focusClass:'chk-focus',
		checkedClass:'chk-checked',
		labelActiveClass:'chk-label-active',
		uncheckedClass:'chk-unchecked',
		disabledClass:'chk-disabled',
		chkStructure:'<span></span>'
	},
	setupWrapper: function(){
		jcf.lib.addClass(this.fakeElement, this.options.wrapperClass);
		this.fakeElement.innerHTML = this.options.chkStructure;
		this.realElement.parentNode.insertBefore(this.fakeElement, this.realElement);
		jcf.lib.event.add(this.realElement, 'click', this.onRealClick, this);
		this.refreshState();
	},
	onFakePressed: function() {
		jcf.modules[this.name].superclass.onFakePressed.apply(this, arguments);
		this.realElement.focus();
	},
	onFakeClick: function(e) {
		jcf.modules[this.name].superclass.onFakeClick.apply(this, arguments);
		this.tmpTimer = setTimeout(jcf.lib.bind(function(){
			this.toggle();
		},this),10);
		return false;
	},
	onRealClick: function(e) {
		setTimeout(jcf.lib.bind(function(){
			this.refreshState();
		},this),10);
		e.stopPropagation();
	},
	toggle: function(e){
		if(!this.realElement.disabled) {
			if(this.realElement.checked) {
				this.realElement.checked = false;
			} else {
				this.realElement.checked = true;
			}
		}
		this.refreshState();
		processAjaxFilters(0);
		return false;
	},
	refreshState: function(){
		if(this.realElement.checked) {
			jcf.lib.addClass(this.fakeElement, this.options.checkedClass);
			jcf.lib.removeClass(this.fakeElement, this.options.uncheckedClass);
			if(this.labelFor) {
				jcf.lib.addClass(this.labelFor, this.options.labelActiveClass);
			}
		} else {
			jcf.lib.removeClass(this.fakeElement, this.options.checkedClass);
			jcf.lib.addClass(this.fakeElement, this.options.uncheckedClass);
			if(this.labelFor) {
				jcf.lib.removeClass(this.labelFor, this.options.labelActiveClass);
			}
		}
		if(this.realElement.disabled) {
			jcf.lib.addClass(this.fakeElement, this.options.disabledClass);
			if(this.labelFor) {
				jcf.lib.addClass(this.labelFor, this.options.labelDisabledClass);
			}
		} else {
			jcf.lib.removeClass(this.fakeElement, this.options.disabledClass);
			if(this.labelFor) {
				jcf.lib.removeClass(this.labelFor, this.options.labelDisabledClass);
			}
		}
	}
});
*/
// custom select module
jcf.addModule({
	name:'select',
	selector:'select',
	defaultOptions: {
		handleDropPosition: true,
		wrapperClass:'select-area',
		focusClass:'select-focus',
		dropActiveClass:'select-active',
		selectedClass:'item-selected',
		disabledClass:'select-disabled',
		valueSelector:'span.center',
		optGroupClass:'optgroup',
		openerSelector:'a.select-opener',
		selectStructure:'<span class="left"></span><span class="center"></span><a class="select-opener"><em></em></a>',
		selectPrefixClass:'select-',
		dropMaxHeight: 200,
		dropFlippedClass: 'select-options-flipped',
		dropHiddenClass:'options-hidden',
		dropScrollableClass:'options-overflow',
		dropClass:'select-options',
		dropClassPrefix:'drop-',
		dropStructure:'<div class="drop-list"></div>',
		dropSelector:'div.drop-list'
	},
	checkElement: function(el){
		return (!el.size && !el.multiple);
	},
	setupWrapper: function(){
		jcf.lib.addClass(this.fakeElement, this.options.wrapperClass);
		this.realElement.parentNode.insertBefore(this.fakeElement, this.realElement);
		this.fakeElement.innerHTML = this.options.selectStructure;
		this.fakeElement.style.width = (this.realElement.offsetWidth > 0 ? this.realElement.offsetWidth + 'px' : 'auto');
		jcf.lib.addClass(this.fakeElement, jcf.lib.getAllClasses(this.realElement.className, this.options.selectPrefixClass, jcf.baseOptions.hiddenClass));

		// create select body
		this.opener = jcf.lib.queryBySelector(this.options.openerSelector, this.fakeElement)[0];
		this.valueText = jcf.lib.queryBySelector(this.options.valueSelector, this.fakeElement)[0];
		this.opener.jcf = this;

		this.createDropdown();
		this.refreshState();
		this.addEvents();
		this.onControlReady(this);
		this.hideDropdown(true);
	},
	addEvents: function(){
		jcf.lib.event.add(this.fakeElement, 'click', this.toggleDropdown, this);
		jcf.lib.event.add(this.realElement, 'change', this.onChange, this);
	},
	onFakeClick: function() {
		// do nothing (drop toggles by toggleDropdown method)
	},
	onFocus: function(){
		// Mac Safari Fix
		if(jcf.lib.browser.safariMac) {
			this.realElement.setAttribute('size','2');
		}
		jcf.modules[this.name].superclass.onFocus.apply(this, arguments);
		jcf.lib.event.add(this.realElement, 'keydown', this.onKeyDown, this);
		if(jcf.activeControl && jcf.activeControl != this) {
			jcf.activeControl.hideDropdown();
			jcf.activeControl = this;
		}
	},
	onBlur: function(){
		// Mac Safari Fix
		if(jcf.lib.browser.safariMac) {
			this.realElement.removeAttribute('size');
		}
		if(!this.isActiveDrop() || !this.isOverDrop()) {
			jcf.modules[this.name].superclass.onBlur.apply(this);
			if(jcf.activeControl === this) jcf.activeControl = null;
			if(!jcf.isTouchDevice) {
				this.hideDropdown();
			}
		}
		jcf.lib.event.remove(this.realElement, 'keydown', this.onKeyDown);
	},
	onChange: function() {
		this.refreshState();
	},
	onKeyDown: function(e){
		jcf.tmpFlag = true;
		setTimeout(function(){jcf.tmpFlag = false},100);
		var context = this;
		context.keyboardFix = true;
		setTimeout(function(){
			context.refreshState();
		},10);
		if(e.keyCode == 13) {
			context.toggleDropdown.apply(context);
			return false;
		}
	},
	onResizeWindow: function(e){
		if(this.isActiveDrop()) {
			this.hideDropdown();
		}
	},
	onScrollWindow: function(e){
		if(this.isActiveDrop()) {
			this.positionDropdown();
		}
	},
	onOptionClick: function(e){
		var opener = e.target && e.target.tagName && e.target.tagName.toLowerCase() == 'li' ? e.target : jcf.lib.getParent(e.target, 'li');
		if(opener) {
			this.realElement.selectedIndex = parseInt(opener.getAttribute('rel'));
			if(jcf.isTouchDevice) {
				this.onFocus();
			} else {
				this.realElement.focus();
			}
			this.refreshState();
			this.hideDropdown();
			jcf.lib.fireEvent(this.realElement, 'change');
		}
		return false;
	},
	onClickOutside: function(e){
		if(jcf.tmpFlag) {
			jcf.tmpFlag = false;
			return;
		}
		if(!jcf.lib.isParent(this.fakeElement, e.target) && !jcf.lib.isParent(this.selectDrop, e.target)) {
			this.hideDropdown();
		}
	},
	onDropHover: function(e){
		if(!this.keyboardFix) {
			this.hoverFlag = true;
			var opener = e.target && e.target.tagName && e.target.tagName.toLowerCase() == 'li' ? e.target : jcf.lib.getParent(e.target, 'li');
			if(opener) {
				this.realElement.selectedIndex = parseInt(opener.getAttribute('rel'));
				this.refreshSelectedClass(parseInt(opener.getAttribute('rel')));
			}
		} else {
			this.keyboardFix = false;
		}
	},
	onDropLeave: function(){
		this.hoverFlag = false;
	},
	isActiveDrop: function(){
		return !jcf.lib.hasClass(this.selectDrop, this.options.dropHiddenClass);
	},
	isOverDrop: function(){
		return this.hoverFlag;
	},
	createDropdown: function(){
		// remove old dropdown if exists
		if(this.selectDrop) {
			this.selectDrop.parentNode.removeChild(this.selectDrop);
		}

		// create dropdown holder
		this.selectDrop = document.createElement('div');
		this.selectDrop.className = this.options.dropClass;
		this.selectDrop.innerHTML = this.options.dropStructure;
		jcf.lib.setStyles(this.selectDrop, {position:'absolute'});
		this.selectList = jcf.lib.queryBySelector(this.options.dropSelector,this.selectDrop)[0];
		jcf.lib.addClass(this.selectDrop, this.options.dropHiddenClass);
		document.body.appendChild(this.selectDrop);
		this.selectDrop.jcf = this;
		jcf.lib.event.add(this.selectDrop, 'click', this.onOptionClick, this);
		jcf.lib.event.add(this.selectDrop, 'mouseover', this.onDropHover, this);
		jcf.lib.event.add(this.selectDrop, 'mouseout', this.onDropLeave, this);
		this.buildDropdown();
	},
	buildDropdown: function() {
		// build select options / optgroups
		this.buildDropdownOptions();

		// position and resize dropdown
		this.positionDropdown();

		// cut dropdown if height exceedes
		this.buildDropdownScroll();
	},
	buildDropdownOptions: function() {
		this.resStructure = '';
		this.optNum = 0;
		for(var i = 0; i < this.realElement.children.length; i++) {
			this.resStructure += this.buildElement(this.realElement.children[i]) +'\n';
		}
		this.selectList.innerHTML = this.resStructure;
	},
	buildDropdownScroll: function() {
		if(this.options.dropMaxHeight) {
			if(this.selectDrop.offsetHeight > this.options.dropMaxHeight) {
				this.selectList.style.height = this.options.dropMaxHeight+'px';
				this.selectList.style.overflow = 'auto';
				this.selectList.style.overflowX = 'hidden';
				jcf.lib.addClass(this.selectDrop, this.options.dropScrollableClass);
			}
		}
		jcf.lib.addClass(this.selectDrop, jcf.lib.getAllClasses(this.realElement.className, this.options.dropClassPrefix, jcf.baseOptions.hiddenClass));
	},
	buildElement: function(obj){
		// build option
		var res = '';
		if(obj.tagName.toLowerCase() == 'option') {
			if(!jcf.lib.prevSibling(obj) || jcf.lib.prevSibling(obj).tagName.toLowerCase() != 'option') {
				res += '<ul>';
			}
			res += '<li rel="'+(this.optNum++)+'" class="'+(obj.className? obj.className : '')+' jcfcalc"><a href="#">'+(obj.title? '<img src="'+obj.title+'" alt="" />' : '')+'<span>' + obj.innerHTML + '</span></a></li>';
			if(!jcf.lib.nextSibling(obj) || jcf.lib.nextSibling(obj).tagName.toLowerCase() != 'option') {
				res += '</ul>';
			}
			return res;
		}
		// build option group with options
		else if(obj.tagName.toLowerCase() == 'optgroup' && obj.label) {
			res += '<div class="'+this.options.optGroupClass+'">';
			res += '<strong class="jcfcalc"><em>'+(obj.label)+'</em></strong>';
			for(var i = 0; i < obj.children.length; i++) {
				res += this.buildElement(obj.children[i]);
			}
			res += '</div>';
			return res;
		}
	},
	positionDropdown: function(){
		var ofs = jcf.lib.getOffset(this.fakeElement), selectAreaHeight = this.fakeElement.offsetHeight, selectDropHeight = this.selectDrop.offsetHeight;
		if(this.options.handleDropPosition && jcf.lib.getScrollTop() + jcf.lib.getWindowHeight() < ofs.top + selectAreaHeight + selectDropHeight) {
			this.selectDrop.style.top = (ofs.top - selectDropHeight)+'px';
			jcf.lib.addClass(this.selectDrop, this.options.dropFlippedClass);
		} else {
			this.selectDrop.style.top = (ofs.top + selectAreaHeight)+'px';
			jcf.lib.removeClass(this.selectDrop, this.options.dropFlippedClass);
		}
		this.selectDrop.style.left = ofs.left+'px';
		this.selectDrop.style.width = this.fakeElement.offsetWidth+'px';
	},
	showDropdown: function(){
		document.body.appendChild(this.selectDrop);
		jcf.lib.removeClass(this.selectDrop, this.options.dropHiddenClass);
		jcf.lib.addClass(this.fakeElement,this.options.dropActiveClass);
		this.positionDropdown();

		// show current dropdown
		jcf.lib.event.add(window, 'resize', this.onResizeWindow, this);
		jcf.lib.event.add(window, 'scroll', this.onScrollWindow, this);
		jcf.lib.event.add(document, jcf.eventPress, this.onClickOutside, this);
		this.positionDropdown();
	},
	hideDropdown: function(partial){
		if(this.selectDrop.parentNode) {
			if(this.selectDrop.offsetWidth) {
				this.selectDrop.parentNode.removeChild(this.selectDrop);
			}
			if(partial) {
				return;
			}
		}
		if(typeof this.origSelectedIndex === 'number') {
			this.realElement.selectedIndex = this.origSelectedIndex;
		}
		jcf.lib.removeClass(this.fakeElement,this.options.dropActiveClass);
		jcf.lib.addClass(this.selectDrop, this.options.dropHiddenClass);
		jcf.lib.event.remove(window, 'resize', this.onResizeWindow);
		jcf.lib.event.remove(window, 'scroll', this.onScrollWindow);
		jcf.lib.event.remove(document.documentElement, jcf.eventPress, this.onClickOutside);
		if(jcf.isTouchDevice) {
			this.onBlur();
		}
	},
	toggleDropdown: function(){
		if(jcf.isTouchDevice) {
			this.onFocus();
		} else {
			this.realElement.focus();
		}
		this.dropOpened = true;
		if(!this.realElement.disabled) {
			if(this.isActiveDrop()) {
				this.hideDropdown();
			} else {
				this.showDropdown();
			}
		}
		this.refreshState();
	},
	scrollToItem: function(){
		if(this.isActiveDrop()) {
			var dropHeight = this.selectList.offsetHeight;
			var offsetTop = this.calcOptionOffset(this.getFakeActiveOption());
			var sTop = this.selectList.scrollTop;
			var oHeight = this.getFakeActiveOption().offsetHeight;
			//offsetTop+=sTop;

			if(offsetTop >= sTop + dropHeight) {
				this.selectList.scrollTop = offsetTop - dropHeight + oHeight;
			} else if(offsetTop < sTop) {
				this.selectList.scrollTop = offsetTop;
			}
		}
	},
	getFakeActiveOption: function(c) {
		return jcf.lib.queryBySelector('li[rel="'+(typeof c === 'number' ? c : this.realElement.selectedIndex) +'"]',this.selectList)[0];
	},
	calcOptionOffset: function(fake) {
		var h = 0;
		var els = jcf.lib.queryBySelector('.jcfcalc',this.selectList);
		for(var i = 0; i < els.length; i++) {
			if(els[i] == fake) break;
			h+=els[i].offsetHeight;
		}
		return h;
	},
	childrenHasItem: function(hold,item) {
		var items = hold.getElementsByTagName('*');
		for(i = 0; i < items.length; i++) {
			if(items[i] == item) return true;
		}
		return false;
	},
	removeSelectedClass: function(){
		var children = jcf.lib.queryBySelector('li',this.selectList);
		for(var i = children.length - 1; i >= 0; i--) {
			jcf.lib.removeClass(children[i], this.options.selectedClass);
		}
	},
	setSelectedClass: function(c){
		jcf.lib.addClass(this.getFakeActiveOption(c), this.options.selectedClass);
	},
	refreshSelectedClass: function(c){
		this.removeSelectedClass(c);
		this.setSelectedClass(c);
		if(this.realElement.disabled) {
			jcf.lib.addClass(this.fakeElement, this.options.disabledClass);
			if(this.labelFor) {
				jcf.lib.addClass(this.labelFor, this.options.labelDisabledClass);
			}
		} else {
			jcf.lib.removeClass(this.fakeElement, this.options.disabledClass);
			if(this.labelFor) {
				jcf.lib.removeClass(this.labelFor, this.options.labelDisabledClass);
			}
		}
	},
	refreshSelectedText: function() {
		if(!this.dropOpened && this.realElement.title) {
			this.valueText.innerHTML = this.realElement.title;
		} else {
			if(this.realElement.options[this.realElement.selectedIndex].title) {
				this.valueText.innerHTML = '<img src="'+this.realElement.options[this.realElement.selectedIndex].title+'" alt="" />' + this.realElement.options[this.realElement.selectedIndex].innerHTML;
			} else {
				this.valueText.innerHTML = this.realElement.options[this.realElement.selectedIndex].innerHTML;
			}
		}
	},
	refreshState: function(){
		this.origSelectedIndex = this.realElement.selectedIndex;
		this.refreshSelectedClass();
		this.refreshSelectedText();
		this.positionDropdown();
		if(this.selectDrop.offsetWidth) {
			this.scrollToItem();
		}
	}
});

jcf.lib.domReady(function(){
	jcf.customForms.replaceAll();
});

/*! HTML5 Shiv vpre3.6 | @afarkas @jdalton @jon_neal @rem | MIT/GPL2 Licensed */
;(function(o,s){var g=o.html5||{};var j=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;var d=/^<|^(?:a|b|button|code|div|fieldset|form|h1|h2|h3|h4|h5|h6|i|iframe|img|input|label|li|link|ol|option|p|param|q|script|select|span|strong|style|table|tbody|td|textarea|tfoot|th|thead|tr|ul)$/i;var x;var k="_html5shiv";var c=0;var u={};var h;(function(){var A=s.createElement("a");A.innerHTML="<xyz></xyz>";x=("hidden" in A);h=A.childNodes.length==1||(function(){try{(s.createElement)("a")}catch(B){return true}var C=s.createDocumentFragment();return(typeof C.cloneNode=="undefined"||typeof C.createDocumentFragment=="undefined"||typeof C.createElement=="undefined")}())}());function i(A,C){var D=A.createElement("p"),B=A.getElementsByTagName("head")[0]||A.documentElement;D.innerHTML="x<style>"+C+"</style>";return B.insertBefore(D.lastChild,B.firstChild)}function q(){var A=n.elements;return typeof A=="string"?A.split(" "):A}function w(A){var B=u[A[k]];if(!B){B={};c++;A[k]=c;u[c]=B}return B}function t(D,A,C){if(!A){A=s}if(h){return A.createElement(D)}C=C||w(A);var B;if(C.cache[D]){B=C.cache[D].cloneNode()}else{if(d.test(D)){B=(C.cache[D]=C.createElem(D)).cloneNode()}else{B=C.createElem(D)}}return B.canHaveChildren&&!j.test(D)?C.frag.appendChild(B):B}function y(C,E){if(!C){C=s}if(h){return C.createDocumentFragment()}E=E||w(C);var F=E.frag.cloneNode(),D=0,B=q(),A=B.length;for(;D<A;D++){F.createElement(B[D])}return F}function z(A,B){if(!B.cache){B.cache={};B.createElem=A.createElement;B.createFrag=A.createDocumentFragment;B.frag=B.createFrag()}A.createElement=function(C){if(!n.shivMethods){return B.createElem(C)}return t(C)};A.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+q().join().replace(/\w+/g,function(C){B.createElem(C);B.frag.createElement(C);return'c("'+C+'")'})+");return n}")(n,B.frag)}function e(A){if(!A){A=s}var B=w(A);if(n.shivCSS&&!x&&!B.hasCSS){B.hasCSS=!!i(A,"article,aside,figcaption,figure,footer,header,hgroup,nav,section{display:block}mark{background:#FF0;color:#000}")}if(!h){z(A,B)}return A}var n={elements:g.elements||"abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video",shivCSS:!(g.shivCSS===false),supportsUnknownElements:h,shivMethods:!(g.shivMethods===false),type:"default",shivDocument:e,createElement:t,createDocumentFragment:y};o.html5=n;e(s);var b=/^$|\b(?:all|print)\b/;var l="html5shiv";var r=!h&&(function(){var A=s.documentElement;return !(typeof s.namespaces=="undefined"||typeof s.parentWindow=="undefined"||typeof A.applyElement=="undefined"||typeof A.removeNode=="undefined"||typeof o.attachEvent=="undefined")}());function f(E){var F,C=E.getElementsByTagName("*"),D=C.length,B=RegExp("^(?:"+q().join("|")+")$","i"),A=[];while(D--){F=C[D];if(B.test(F.nodeName)){A.push(F.applyElement(v(F)))}}return A}function v(C){var D,A=C.attributes,B=A.length,E=C.ownerDocument.createElement(l+":"+C.nodeName);while(B--){D=A[B];D.specified&&E.setAttribute(D.nodeName,D.nodeValue)}E.style.cssText=C.style.cssText;return E}function a(D){var F,E=D.split("{"),B=E.length,A=RegExp("(^|[\\s,>+~])("+q().join("|")+")(?=[[\\s,>+~#.:]|$)","gi"),C="$1"+l+"\\:$2";while(B--){F=E[B]=E[B].split("}");F[F.length-1]=F[F.length-1].replace(A,C);E[B]=F.join("}")}return E.join("{")}function p(B){var A=B.length;while(A--){B[A].removeNode()}}function m(A){var E,C,B=A.namespaces,D=A.parentWindow;if(!r||A.printShived){return A}if(typeof B[l]=="undefined"){B.add(l)}D.attachEvent("onbeforeprint",function(){var F,J,H,L=A.styleSheets,I=[],G=L.length,K=Array(G);while(G--){K[G]=L[G]}while((H=K.pop())){if(!H.disabled&&b.test(H.media)){try{F=H.imports;J=F.length}catch(M){J=0}for(G=0;G<J;G++){K.push(F[G])}try{I.push(H.cssText)}catch(M){}}}I=a(I.reverse().join(""));C=f(A);E=i(A,I)});D.attachEvent("onafterprint",function(){p(C);E.removeNode(true)});A.printShived=true;return A}n.type+=" print";n.shivPrint=m;m(s)}(this,document));