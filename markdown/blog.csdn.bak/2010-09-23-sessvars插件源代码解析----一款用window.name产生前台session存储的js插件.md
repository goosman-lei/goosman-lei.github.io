---
layout: post
title: sessvars插件源代码解析----一款用window.name产生前台session存储的js插件
date: 2010-09-23 19:46:00
categories: [session, 存储, function, json, javascript, string]
tags: []
---
插件官方地址：[http://www.thomasfrank.se/sessionvars.html](http://www.thomasfrank.se/sessionvars.html)
下载地址：[http://www.thomasfrank.se/sessvars.js](http://www.thomasfrank.se/sessvars.js)
@译者: selfimpr
@blog: http://blog.csdn.net/lgg201
@mail: lgg860911@yahoo.com.cn
@转载请声明出处
 
插件文档翻译：[http://blog.csdn.net/lgg201/archive/2010/09/23/5902321.aspx](http://blog.csdn.net/lgg201/archive/2010/09/23/5902321.aspx)
 
勘误： 里面的prefs中配置项的单位是KB， 不是MB。
 

```javascript
/*
sessvars ver 1.01
- JavaScript based session object
copyright 2008 Thomas Frank
This EULA grants you the following rights:
Installation and Use. You may install and use an unlimited number of copies of the SOFTWARE PRODUCT.
Reproduction and Distribution. You may reproduce and distribute an unlimited number of copies of the SOFTWARE PRODUCT either in whole or in part; each copy should include all copyright and trademark notices, and shall be accompanied by a copy of this EULA. Copies of the SOFTWARE PRODUCT may be distributed as a standalone product or included with your own product.
Commercial Use. You may sell for profit and freely distribute scripts and/or compiled scripts that were created with the SOFTWARE PRODUCT.
v 1.0 --&gt; 1.01
sanitizer added to toObject-method &amp; includeFunctions flag now defaults to false
@url: http://www.thomasfrank.se/sessionvars.html
@译者: selfimpr
@blog: http://blog.csdn.net/lgg201
@mail: lgg860911@yahoo.com.cn
sessvars是一款利用window.name实现前台跨窗口(跨域)会话对象的javascript插件.
*/
sessvars=function(){
	//
	var x={};
	
	//
	x.$={
		//一些配置属性
		prefs:{
			//使用内存大小限制, 单位MB, Opera 9.25限制在2M左右, IE7.0, Firefox 1.5/2.0, Safari 3.0都可以支持10M
			memLimit:2000,
			//每当window的unload事件触发时, 是否自动将当前保存页面中保存的数据写入window.name以便跨页面传播(参见x.$.flush方法末尾)
			autoFlush:true,
			//是否允许跨域数据共享, 开启后, 跨域的多个脚本需要同时使用此插件
			crossDomain:false,
			includeProtos:false,
			includeFunctions:false
		},
		//一个指向外层x对象的引用, 所有数据时临时暂存在这个对象上, 在flush时写入到window.name
		parent:x,
		//清理内存
		clearMem:function(){
			//将parent中所有非$的属性全部清除, $就是x.$, 是一些配置信息， 而不是要保存的信息
			for(var i in this.parent){if(i!=&quot;$&quot;){this.parent[i]=undefined}};
			//将清理后的结果写入window.name, 这样, 页面切换时, 就不会保存数据
			this.flush();
		},
		//获取当前已使用的内存大小, 单位KB
		usedMem:function(){
			x={};
			//使用一个空对象调用flush, 返回已存数据大小
			return Math.round(this.flush(x)/1024);
		},
		//获取当前使用内存占配置的最大内存占用的比例
		usedMemPercent:function(){
			return Math.round(this.usedMem()/this.prefs.memLimit);
		},
		//将当前页面保存的数据写入到window.name
		flush:function(x){
			//这里this是x.$(外面的那个, 也就是sessvars=function 这个函数内的第一句声明的x)
			var y,o={},j=this.$$;
			//x(内部x)默认为顶层window对象
			x=x||top;
			//由于this是x.$, 所以, parent实际上就是x(注意, 这里还是外面的x), 即这里将x.$.parent中存放的变量全部拷贝到o这个对象中
			for(var i in this.parent){o[i]=this.parent[i]};
			//将o里面的$对象重新赋值为当前的prefs配置
			o.$=this.prefs;
			//下面两句设置了外层x.$.$$中的两个配置项includeProtos和includeFunctions使用当前的prefs中的配置
			j.includeProtos=this.prefs.includeProtos;
			j.includeFunctions=this.prefs.includeFunctions;
			//调用x.$.$$.make(外面x)方法, 将o对象转换的字符串(json转换)
			y=this.$$.make(o);
			//如果x(内部x)不是顶层window对象, 比如usedMem方法中的调用, 就返回字符串长度, 这里就得到了已经存放的长度
			if(x!=top){return y.length};
			//如果y的长度超过了配置的memLimit参数, 返回false
			if(y.length/1024&gt;this.prefs.memLimit){return false}
			//此时可以确定内部的x是顶层window对象, 将字符串y写入到window.name中.
			x.name=y;
			return true;
		},
		//获取当前域名
		getDomain:function(){
				var l=location.href
				l=l.split(&quot;///&quot;).join(&quot;//&quot;);
				l=l.substring(l.indexOf(&quot;://&quot;)+3).split(&quot;/&quot;)[0];
				while(l.split(&quot;.&quot;).length&gt;2){l=l.substring(l.indexOf(&quot;.&quot;)+1)};
				return l
		},
		//输出调试信息
		debug:function(t){
			//t保存一个要调试的sessvars对象, a获取当前函数
			var t=t||this,a=arguments.callee;
			//如果文档没有body, 200毫秒后重试一次
			if(!document.body){setTimeout(function(){a(t)},200);return};
			//调用一次要调试的对象的flush, 将值刷入到window.name
			t.flush();
			//以下内容构建一个用来显示调试信息的html元素, 显示信息包括已使用内存大小, 已使用内存百分比, 已保存数据.
			var d=document.getElementById(&quot;sessvarsDebugDiv&quot;);
			if(!d){d=document.createElement(&quot;div&quot;);document.body.insertBefore(d,document.body.firstChild)};
			d.id=&quot;sessvarsDebugDiv&quot;;
			d.innerHTML='&lt;div style=&quot;line-height:20px;padding:5px;font-size:11px;font-family:Verdana,Arial,Helvetica;'+
						'z-index:10000;background:#FFFFCC;border: 1px solid #333;margin-bottom:12px&quot; mce_style=&quot;line-height:20px;padding:5px;font-size:11px;font-family:Verdana,Arial,Helvetica;'+
						'z-index:10000;background:#FFFFCC;border: 1px solid #333;margin-bottom:12px&quot;&gt;'+
						'&lt;b style=&quot;font-family:Trebuchet MS;font-size:20px&quot; mce_style=&quot;font-family:Trebuchet MS;font-size:20px&quot;&gt;sessvars.js - debug info:&lt;/b&gt;&lt;br/&gt;&lt;br/&gt;'+
						'Memory usage: '+t.usedMem()+' Kb ('+t.usedMemPercent()+'%)&nbsp;&nbsp;&nbsp;'+
						'&lt;span style=&quot;cursor:pointer&quot; mce_style=&quot;cursor:pointer&quot;&gt;&lt;b&gt;[Clear memory]&lt;/b&gt;&lt;/span&gt;&lt;br/&gt;'+
						top.name.split('/n').join('&lt;br/&gt;')+'&lt;/div&gt;';
			d.getElementsByTagName('span')[0].onclick=function(){t.clearMem();location.reload()}
		},
		//初始化sessvars对象
		init:function(){
			//以下提到的x都是外面的x
			//t和this都是x.$
			var o={}, t=this;
			//将之前保存的window.name使用x.$.$$.toObject函数(反json)成为一个对象, 如果之前没有, 那么o是新的空对象
			try {o=this.$$.toObject(top.name)} catch(e){o={}};
			//如果之前使用过sessvars, 那么当前对象的prefs使用上一次的(从o中获取到的)
			this.prefs=o.$||t.prefs;
			//如果允许跨域或就是当前域, 将上一次保存的所有数据写入到新的对象的中
			//(这里this.parent实际上还是外层的x对象, 因为this = x.$, x.$.parent = x)
			if(this.prefs.crossDomain || this.prefs.currentDomain==this.getDomain()){
				for(var i in o){this.parent[i]=o[i]};
			}
			else {
				//如果不是, 保存一次当前域名
				this.prefs.currentDomain=this.getDomain();
			};
			this.parent.$=t;
			//将内容写入到window.name
			t.flush();
			//定义一个用来处理自动刷入数据的闭包
			var f=function(){if(t.prefs.autoFlush){t.flush()}};
			//绑定unload事件处理autoflush
			if(window[&quot;addEventListener&quot;]){addEventListener(&quot;unload&quot;,f,false)}
			else if(window[&quot;attachEvent&quot;]){window.attachEvent(&quot;onunload&quot;,f)}
			else {this.prefs.autoFlush=false};
		}
	};
	
	x.$.$$={
		//对输出进行压缩
		compactOutput:false, 		
		//是否处理原型属性(未被重新赋值的)
		includeProtos:false, 	
		//是否处理函数
		includeFunctions: false,
		//是否检测循环引用
		detectCirculars:true,
		//是否还原循环引用
		restoreCirculars:true,
		//创建json串
		make:function(arg,restore) {
			//resotre表明是否还原循环引用
			this.restore=restore;
			//用于循环引用检测及路径生成的暂存数组
			this.mem=[];this.pathMem=[];
			return this.toJsonStringArray(arg).join('');
		},
		//将json串转换成对象
		toObject:function(x){
			//检测是否匹配json格式的正则
			if(!this.cleaner){
				try{this.cleaner=new RegExp('^(&quot;(////.|[^&quot;//////n//r])*?&quot;|[,:{}//[//]0-9.//-+Eaeflnr-u //n//r//t])+?$')}
				catch(a){this.cleaner=/^(true|false|null|/[.*/]|/{.*/}|&quot;.*&quot;|/d+|/d+/./d+)$/}
			};
			//如果不是合法json, 返回空对象
			if(!this.cleaner.test(x)){return {}};
			//使用eval创建对象
			eval(&quot;this.myObj=&quot;+x);
			//如果不还原循环引用直接返回对象
			if(!this.restoreCirculars || !alert){return this.myObj};
			//处理函数的json反序列化
			if(this.includeFunctions){
				var x=this.myObj;
				for(var i in x){if(typeof x[i]==&quot;string&quot; &amp;&amp; !x[i].indexOf(&quot;JSONincludedFunc:&quot;)){
					x[i]=x[i].substring(17);
					eval(&quot;x[i]=&quot;+x[i])
				}}
			};
			//创建还原循环引用缓冲存储
			this.restoreCode=[];
			//创建当前对象的json串, 这里使用了make函数restore参数, 所以会将需要还原的循环引用保存到restoreCode中
			this.make(this.myObj,true);
			//把记录的需要还原的循环引用join成字符串, 方便正则替换
			var r=this.restoreCode.join(&quot;;&quot;)+&quot;;&quot;;
			//处理数组下标访问及path末尾的&quot;.;&quot;, 这些字符的生成参见toJsonStringArray中循环引用字符串生成部分
			eval('r=r.replace(///W([0-9]{1,})(//W)/g,&quot;[$1]$2&quot;).replace(///.//;/g,&quot;;&quot;)');
			//用eval建立循环引用
			eval(r);
			//返回json反序列化后的对象
			return this.myObj
		},
		//把对象转换成json字符串数据
		toJsonStringArray:function(arg, out) {
			if(!out){this.path=[]};
			out = out || [];
			var u; // undefined
			switch (typeof arg) {
			case 'object':
			//最后处理的对象保存
				this.lastObj=arg;
				//检测循环引用
				if(this.detectCirculars){
					//m用来保存已处理对象, n用来保存已处理对象路径
					var m=this.mem; var n=this.pathMem;
					for(var i=0;i&lt;m.length;i++){
						if(arg===m[i]){
							out.push('&quot;JSONcircRef:'+n[i]+'&quot;');return out
						}
					};
					//循环引用处理的对象记录及路径记录
					m.push(arg); n.push(this.path.join(&quot;.&quot;));
				};
				if (arg) {
					//处理数组对象
					if (arg.constructor == Array) {
						out.push('[');
						for (var i = 0; i &lt; arg.length; ++i) {
							//路径处理
							this.path.push(i);
							//处理数组元素的间隔符
							if (i &gt; 0)
								out.push(',/n');
							//递归处理数组元素的字符串化
							this.toJsonStringArray(arg[i], out);
							//路径处理
							this.path.pop();
						}
						out.push(']');
						return out;
						//普通对象处理
					} else if (typeof arg.toString != 'undefined') {
						out.push('{');
						var first = true;
						for (var i in arg) {
							//如果不处理原型属性, 并且当前对象的i属性和原型的i属性严格相等, 则不处理
							if(!this.includeProtos &amp;&amp; arg[i]===arg.constructor.prototype[i]){continue};
							//路径处理
							this.path.push(i);
							//记录处理当前属性时输出数组的长度
							var curr = out.length; 
							//处理对象属性的间隔符
							if (!first)
								out.push(this.compactOutput?',':',/n'); //译者注: 如果是压缩输出就不输出换行, 这个在数组对象处理处也应该使用
							//递归处理元素属性名称
							this.toJsonStringArray(i, out);
							//属性名和属性值的间隔
							out.push(':');        
							//递归处理元素属性值            
							this.toJsonStringArray(arg[i], out);
							//如果当前处理的属性值是undefiend, 则删除针对该属性的输出
							if (out[out.length - 1] == u)
								out.splice(curr, out.length - curr);
							else
								//除第一次外, 都不再是first, 即要输出属性间隔符
								first = false;
							//路径处理
							this.path.pop();
						}
						out.push('}');
						return out;
					}
					return out;
				}
				//如果不是数组对象, 且没有toString方法, 认为是空对象
				out.push('null');
				return out;
			case 'unknown':
			case 'undefined':
			case 'function':
				//如果不处理函数, 将函数输出为一个undefind
				if(!this.includeFunctions){out.push(u);return out};
				//将函数转换成字符串
				arg=&quot;JSONincludedFunc:&quot;+arg;
				out.push('&quot;');
				var a=['/n','//n','/r','//r','&quot;','//&quot;'];
				//对函数中的原文字符串进行转义替换
				arg+=&quot;&quot;; for(var i=0;i&lt;6;i+=2){arg=arg.split(a[i]).join(a[i+1])};
				out.push(arg);
				out.push('&quot;');
				return out;
			case 'string':
				//如果需要对循环引用进行还原, 处理还原
				if(this.restore &amp;&amp; arg.indexOf(&quot;JSONcircRef:&quot;)==0){
					//将需要还原的循环引用写入restoreCode暂存, 对此的下一步处理参见x.$.$$.toObject方法
					this.restoreCode.push('this.myObj.'+this.path.join(&quot;.&quot;)+&quot;=&quot;+arg.split(&quot;JSONcircRef:&quot;).join(&quot;this.myObj.&quot;));
				};
				out.push('&quot;');
				var a=['/n','//n','/r','//r','&quot;','//&quot;'];
				//转义处理
				arg+=&quot;&quot;; for(var i=0;i&lt;6;i+=2){arg=arg.split(a[i]).join(a[i+1])};
				out.push(arg);
				out.push('&quot;');
				return out;
			default:
				//非特殊情况, 直接使用String转类型
				out.push(String(arg));
				return out;
			}
		}
	};
	
	x.$.init();
	return x;
}()
```

