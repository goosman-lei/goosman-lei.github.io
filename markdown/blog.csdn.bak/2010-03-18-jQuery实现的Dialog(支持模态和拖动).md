---
layout: post
title: jQuery实现的Dialog(支持模态和拖动)
date: 2010-03-18 10:55:00
categories: [dialog, jquery, float, div, function, url]
tags: []
---
下载链接:[http://download.csdn.net/source/2138962](http://download.csdn.net/source/2138962).
 
效果图:
![](http://hi.csdn.net/attachment/201003/18/8670_1268881026h04Q.png)
样式表:

```css
@CHARSET "UTF-8";
.seasy-dialog{z-index: 1002; position: relative;}
.seasy-dialog-header{clear: both;}
.seasy-dialog-header-left{float: left; background: url("../images/seasy.dialog.topleft.gif") no-repeat;}
.seasy-dialog-header-center{float: left; background: url("../images/seasy.dialog.topcenter.gif") repeat-x;}
.seasy-dialog-header-button{float: left; background: url("../images/seasy.dialog.close.gif") transparent no-repeat right bottom; cursor: pointer;}
.seasy-dialog-header-right{float: left; background: url("../images/seasy.dialog.topright.gif") no-repeat;}
.seasy-dialog-body{clear: both;}
.seasy-dialog-body-left{float: left; background: url("../images/seasy.dialog.middleleft.gif") repeat-y;}
.seasy-dialog-body-center{float: left; background: url("../images/seasy.dialog.middlecenter.gif") repeat;}
.seasy-dialog-body-title{clear: both; float: left; margin-left: -6px;}
.seasy-dialog-body-title-content{clear: both; float: left; background: url("../images/seasy.dialog.titlebg.png") repeat-x;}
.seasy-dialog-body-title-right{float: left; background: url("../images/seasy.dialog.titleright.gif") no-repeat;}
.seasy-dialog-body-title-button{float: right; background: url("../images/seasy.dialog.close.gif") 50% 50% no-repeat; cursor: pointer;}
.seasy-dialog-body-right{float: left; background: url("../images/seasy.dialog.middleright.gif") repeat-y;}
.seasy-dialog-body-body{clear: both; float: left; overflow: auto;}
.seasy-dialog-foot{clear: both;}
.seasy-dialog-foot-left{float: left; background: url("../images/seasy.dialog.bottomleft.gif") no-repeat;}
.seasy-dialog-foot-center{float: left; background: url("../images/seasy.dialog.bottomcenter.gif") repeat-x;}
.seasy-dialog-foot-right{float: left; background: url("../images/seasy.dialog.bottomright.gif") no-repeat;}
.seasy-modal-layer{width: 100%; height: 100%; position: absolute; left: 0px; top: 0px; background: #BBBBBB; filter: alpha(opacity=30); -moz-opacity: 0.3; opacity: 0.3;}
```

源文件:

```xhtml
/**
 * @author: selfimpr
 * @blog: http://blog.csdn.net/lgg201
 * @email: lgg860911@yahoo.com.cn
 * 使用方法:
 * var dialog = new Dialog({
 * 	target: '#dialog', 
 * 	width: 800, 
 * 	height: 600, 
 * 	modal: false, 
 * 	title: '标题', 
 * 	draggabled: false
 * });
 * 参数含义
 * target: 要用dialog包装的目标, 可以是选择器或标准DOM元素
 * width: dialog宽度, 不要设置过小, 没有做最小宽度检测.
 * height: dialog高度. 同样不要设置过小
 * modal: 是否是模态窗口
 * title: 窗口的标题
 * draggabled: 窗口是否可拖拽
 * 方法:
 * show(): 显示该窗口
 * destroy(): 关闭窗口. 这里只是简单做了隐藏, 并没有做真正的销毁工作.
 */
var Dialog = function(options) {
	this.target = $(options.target);
	this.width = options.width || 800;
	this.height = options.height || 600;
	this.modal = options.modal || false;
	this.title = options.title || $(options.target).selector;
	this.draggabled = options.draggabled || false;
	this._initialized = false;
	this._element = null;
	this._showing = false;
	this._init = (function() {
		var self = this;
		if(!this._initialized) {
			this.target.hide();
			this._element = $('&lt;div&gt;').addClass('seasy-dialog').width(this.width).height(this.height)
				.css('position', 'absolute')
				.append( //添加header
					$('&lt;div&gt;').addClass('seasy-dialog-header').width(this.width).height(27)
						.append($('&lt;div&gt;').addClass('seasy-dialog-header-left').width(17).height(27))
						.append($('&lt;div&gt;').addClass('seasy-dialog-header-center').width(this.width - 34).height(27))
						.append($('&lt;div&gt;').addClass('seasy-dialog-header-right').width(17).height(27))
				).append( //添加body
					$('&lt;div&gt;').addClass('seasy-dialog-body')
						.append($('&lt;div&gt;').addClass('seasy-dialog-body-left').width(17).height(this.height - 34))
						.append($('&lt;div&gt;').addClass('seasy-dialog-body-center').width(this.width - 34).height(this.height - 34)
							.append($('&lt;div&gt;').addClass('seasy-dialog-body-title').width(this.width - 34).height(25)
								.append($('&lt;div&gt;').addClass('seasy-dialog-body-title-content').height(25).text(this.title))
								.append($('&lt;div&gt;').addClass('seasy-dialog-body-title-right').width(7).height(25))
								.append($('&lt;div&gt;').addClass('seasy-dialog-body-title-button').width(25).height(25).click(function(event) {
									self.destroy();
								}))
							)
							.append($('&lt;div&gt;').addClass('seasy-dialog-body-body').width(this.width - 34).height(this.height - 79))
						)
						.append($('&lt;div&gt;').addClass('seasy-dialog-body-right').width(17).height(this.height - 34))
				).append( //添加foot
					$('&lt;div&gt;').addClass('seasy-dialog-foot').width(this.width).height(27)
						.append($('&lt;div&gt;').addClass('seasy-dialog-foot-left').width(17).height(27))
						.append($('&lt;div&gt;').addClass('seasy-dialog-foot-center').width(this.width - 34).height(27))
						.append($('&lt;div&gt;').addClass('seasy-dialog-foot-right').width(17).height(27))
				);
			$(window).resize(function(event) {
				self._location();
			});
			if(this.draggabled) {
				this._element.draggable({
					containment: 'window'
				});
			}
			this._location();
			this._initialized = !this._initialized;
		}
	});
	this._location = (function() {
		this._element
			.css('left', ($('body').offset().left + $(window).width() - this.width) / 2)
			.css('top', ($('body').offset().top + $(window).height() - this.height) / 2);
	});
	this.show = (function() {
		if(!this._showing) {
			$('body').prepend($('&lt;div&gt;').addClass('seasy-modal-layer')).css('overflow', 'hidden');
			this.target.after(this._element);
			this._element.children('.seasy-dialog-body').find('.seasy-dialog-body-body').append(this.target);
			this.target.show();
			this._element.css('z-index', 1002).show();
			this._showing = !this._showing;
		}
	});
	this.destroy = (function() {
		if(this._showing) {
			$('.seasy-modal-layer').remove();
			$('body').css('overflow', 'auto');
			this._element.after(this.target);
			this._element.hide();
			this.target.hide();
			this._showing = !this._showing;
		}
	});
	this._init();
}
```

