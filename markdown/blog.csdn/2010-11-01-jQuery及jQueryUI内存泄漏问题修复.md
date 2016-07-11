1. jQuery的remove带来的内存泄漏修复：
将remove方法内的代码修改为下面代码即可：

```javascript
		if ( !selector || jQuery.filter( selector, [ this ] ).length ) {
			// Prevent memory leaks
			var item = $(this);
			var clearItem = $('#clear-use-memory');
			if(clearItem.length == 0){
				jQuery('&lt;div/&gt;').hide().attr('id','clear-use-memory').appendTo('body');
				clearItem = jQuery('#clear-use-memory');
			}
			item.appendTo(clearItem);
			jQuery('*',clearItem).each(function(i, e) {
				(events = jQuery.data(this, 'events')) &amp;&amp; jQuery.each(events, function(i, e1) {
					jQuery(e).unbind(i + '.*');
				});
				jQuery.event.remove(this);
				jQuery.removeData(this);
			});
			clearItem[0].innerHTML = '';
			item = null;
		}
```

 
2. jQueryUI内存泄漏：
这个bug是存在于所有的widget扩展而来的插件，下面的修复方式是官方关于此bug给出的修复方案：将200行左右的widget方法进行如下修改

```javascript
$.widget = function(name, prototype) {
	var namespace = name.split(&quot;.&quot;)[0];
	name = name.split(&quot;.&quot;)[1];
	// create plugin method
	$.fn[name] = function(options) {
		var isMethodCall = (typeof options == 'string'),
			args = Array.prototype.slice.call(arguments, 1);
		// prevent calls to internal methods
		if (isMethodCall &amp;&amp; options.substring(0, 1) == '_') {
			return this;
		}
		// handle getter methods
		if (isMethodCall &amp;&amp; getter(namespace, name, options, args)) {
			var instance = $.data(this[0], name);
			return (instance ? instance[options].apply(instance, args)
				: undefined);
		}
		// handle initialization and non-getter methods
		return this.each(function() {
			var instance = $.data(this, name);
			// constructor
			(!instance &amp;&amp; !isMethodCall &amp;&amp;
				$.data(this, name, new $[namespace][name](this, options))._init());
			// method call
			(instance &amp;&amp; isMethodCall &amp;&amp; $.isFunction(instance[options]) &amp;&amp;
				instance[options].apply(instance, args));
		});
	};
	// create widget constructor
	$[namespace] = $[namespace] || {};
	$[namespace][name] = function(element, options) {
		var self = this;
		this.namespace = namespace;
		this.widgetName = name;
		this.widgetEventPrefix = $[namespace][name].eventPrefix || name;
		this.widgetBaseClass = namespace + '-' + name;
		this.options = $.extend({},
			$.widget.defaults,
			$[namespace][name].defaults,
			$.metadata &amp;&amp; $.metadata.get(element)[name],
			options);
		this.element = $(element)
			.bind('setData.' + name, this._handleData) 
			.bind('getData.' + name, this._handleData) 
			.bind('remove', this._handleRemove); 
	};
	// add widget prototype
	$[namespace][name].prototype = $.extend({}, $.widget.prototype, prototype, {  
 		_handleData : function(e, k, v){  
	 		var instance = $.data(e.target, name);  
	 		if (instance) 
	 			return instance[ v ? '_setData' : '_getData' ](k,v);  
 		}, 
 		_handleRemove : function(e) {  
	 		var instance = $.data(e.target, name);
	 		if (!instance) return null;	                         
	 		var result = instance.destroy();
	 		// default  
	 		instance.element 
		 		.removeData(instance.widgetName) 
		 		.unbind('.' + instance.widgetName) ;
	 		return result;  
 		} 
 	});
	// TODO: merge getter and getterSetter properties from widget prototype
	// and plugin prototype
	$[namespace][name].getterSetter = 'option';
};
```

