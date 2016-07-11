有朋友提到了jQuery.load方法的缓存问题, 这里就对其load进行一下小的改造(基本上是照抄), 让我们可以在使用load时, 同样的指定ajax选项.
 
扩展后的使用接口:
1. 如果只给定一个参数, 那么该参数是url
 
2. 以下三条只适用于给定两个参数的情况
2. 1 如果第二个参数是函数, 则认为它是回调函数
2.2 如果第二个参数是字符串, 则认为是给定的请求参数(一旦给定参数, 则使用POST提交)
2.3 如果第二个参数是对象, 则认为该参数是设置的ajax选项.
 
3. 如果第二个参数是对象, 并且给定的第三个参数, 就认为第三个参数是ajax选项
 
4. 按照顺序传递了四个, 参数, 意义就很明确了...
 
不足之处, 请指出共同学习.
 
 

```javascript
	//对jQuery的load进行了扩展, 支持第四个参数, 用来控制ajax加载的选项
	jQuery.fn.extend({
		// Keep a copy of the old load
		_load: jQuery.fn.load,
	
		load: function( url, params, callback, ajaxOptions ) {
			if ( typeof url !== "string" )
				return this._load( url );
	
			var off = url.indexOf(" ");
			if ( off &gt;= 0 ) {
				var selector = url.slice(off, url.length);
				url = url.slice(0, off);
			}
	
			// Default to a GET request
			var type = "GET";
	
			//处理第二个参数
			if ( params ) {
				// 如果是函数, 说明是回调
				if ( jQuery.isFunction( params ) ) {
					// We assume that it's the callback
					callback = params;
					params = null;
	
				// 如果是字符串, 说明是参数 
				} else if( typeof params === "string" ) {
					type = "POST";
				//如果是对象, 说明是选项
				} else if(typeof params === 'object') {
					ajaxOptions = params;
				}
			}
	
	
			//处理第三个参数
			if(callback &amp;&amp; typeof params === 'object') {
				ajaxOptions = callback;
			}
			
			
	
			var self = this;
	
			// Request the remote document
			ajaxOptions = $.extend(true, ajaxOptions, {
				url: url,
				type: type,
				dataType: "html",
				data: params,
				complete: function(res, status){
					// If successful, inject the HTML into all the matched elements
					if ( status == "success" || status == "notmodified" )
						// See if a selector was specified
						self.html( selector ?
							// Create a dummy div to hold the results
							jQuery("&lt;div/&gt;")
								// inject the contents of the document in, removing the scripts
								// to avoid any 'Permission Denied' errors in IE
								.append(res.responseText.replace(/&lt;script(.|/s)*?//script&gt;/g, ""))
	
								// Locate the specified elements
								.find(selector) :
	
							// If not, just inject the full result
							res.responseText );
	
					if( callback )
						self.each( callback, [res.responseText, status, res] );
				}
			});
			jQuery.ajax(ajaxOptions);
			return this;
		}
	});
```

