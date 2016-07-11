效果图
 
![](http://p.blog.csdn.net/images/p_blog_csdn_net/lgg201/EntryImages/20091203/scrollable633954800959062500.jpg)
 
代码:

```xhtml
&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"&gt;
&lt;html&gt;
	&lt;head&gt;
		&lt;title&gt;Scrollable&lt;/title&gt;
		&lt;mce:script type="text/javascript"&gt;&lt;!--
			resizeCallback = function() {
				var containerStyle = document.getElementById('scrollable_container').style;
				var containerNewWidth = document.body.clientWidth - 32;
				containerStyle.width = containerNewWidth + 'px';
				document.getElementById('scrollable_content').style.left = '0px';
			}
			window.onresize = resizeCallback;
			var scroll = false;
			function scrollToRight(speed, ele) {
				var container = ele.nextSibling;
				var content = container.firstChild;
				var containerLeft = container.offsetLeft - 16;
				var contentLeft = content.offsetLeft;
				var minus_result = containerLeft - contentLeft;
				speed = minus_result &gt; speed ? speed : (minus_result &gt; 0 ? minus_result : 0);
				if(scroll &amp;&amp; minus_result &gt; 0) {
					content.style.left = contentLeft +  speed;
					setTimeout(function(){scrollToRight(speed, ele)}, 50);
				}
			}
			function scrollToLeft(speed, ele) {
				var container = ele.previousSibling;
				var content = container.firstChild;
				var containerRight = container.offsetWidth;
				var contentRight = content.offsetLeft + content.offsetWidth;
				var minus_result = contentRight - containerRight;
				speed = minus_result &gt; speed ? speed : (minus_result &gt; 0 ?  minus_result : 0);
				if(scroll &amp;&amp; minus_result &gt; 0) {
					content.style.left = content.offsetLeft - speed;
					setTimeout(function(){scrollToLeft(speed, ele)}, 50);
				}
			}
			var contentWidth = 0;
		
// --&gt;&lt;/mce:script&gt;
		&lt;mce:style type="text/css"&gt;&lt;!--
			body{background: #778899;}
			
			.author-information{background: #BBBBBB; width: 500px; height: 150px; text-align: left; padding: 0 5px; }
			.author-information-label{clear: both; float: left; color: white; width: 80px; text-align: right; margin: 3px 0;}
			.author-information-information{color: blue; font-weight: bold; float: left; text-align: left; width: 420px; margin: 3px 0;}
			
			.scrollable{float: left; margin-right: 5px; width: 100%; background: url(images/button_content.gif) repeat-x;}
			.scrollable-left-button{float: left; background: url(images/scrollable_navigate_arrow_left.gif) no-repeat 0 0; width: 16px; height: 26px; cursor: hand;}
			.scrollable-left-button:hover{background-position: 0 -26px;}
			.scrollable-container{float: left; position: relative; height: 26px; overflow: hidden;}
			.scrollable-content{float: left; position: relative; height: 26px; overflow: hidden;}
			.scrollable-button-item{float: left;}
			.scrollable-button-item:hover span{color: #E8E8E8; height: 16px; line-height: 16px;}
			.scrollable-button-item:hover .scrollable-button-item-content{height: 16px; margin: 3px 2px; padding: 1px 0; border-left: 1px solid #4586A6; border-right: 1px solid #4586A6; border-bottom: 1px solid #4586A6;}
			.scrollable-button-item-content{float: left; background: url(images/button_content.gif) repeat-x; height: 26px; text-align: center; line-height: 26px;}
			.scrollable-button-item-content span{float: left; font-size: 12px; font-weight: bolder; color: #FBFBFB; text-align: center; padding: 0 3px; padding-top: 2px; cursor: hand;}
			.scrollable-button-item-separator{float: left; background: url(images/button_separator.gif) no-repeat 0 0; width: 2px; height: 26px;}
			.scrollable-right-button{float: left; background: url(images/scrollable_navigate_arrow_right.gif) no-repeat 0 0; width: 16px; height: 26px; cursor: hand;}
			.scrollable-right-button:hover{background-position: 0 -26px;}
		
--&gt;&lt;/mce:style&gt;&lt;style type="text/css" mce_bogus="1"&gt;			body{background: #778899;}
			
			.author-information{background: #BBBBBB; width: 500px; height: 150px; text-align: left; padding: 0 5px; }
			.author-information-label{clear: both; float: left; color: white; width: 80px; text-align: right; margin: 3px 0;}
			.author-information-information{color: blue; font-weight: bold; float: left; text-align: left; width: 420px; margin: 3px 0;}
			
			.scrollable{float: left; margin-right: 5px; width: 100%; background: url(images/button_content.gif) repeat-x;}
			.scrollable-left-button{float: left; background: url(images/scrollable_navigate_arrow_left.gif) no-repeat 0 0; width: 16px; height: 26px; cursor: hand;}
			.scrollable-left-button:hover{background-position: 0 -26px;}
			.scrollable-container{float: left; position: relative; height: 26px; overflow: hidden;}
			.scrollable-content{float: left; position: relative; height: 26px; overflow: hidden;}
			.scrollable-button-item{float: left;}
			.scrollable-button-item:hover span{color: #E8E8E8; height: 16px; line-height: 16px;}
			.scrollable-button-item:hover .scrollable-button-item-content{height: 16px; margin: 3px 2px; padding: 1px 0; border-left: 1px solid #4586A6; border-right: 1px solid #4586A6; border-bottom: 1px solid #4586A6;}
			.scrollable-button-item-content{float: left; background: url(images/button_content.gif) repeat-x; height: 26px; text-align: center; line-height: 26px;}
			.scrollable-button-item-content span{float: left; font-size: 12px; font-weight: bolder; color: #FBFBFB; text-align: center; padding: 0 3px; padding-top: 2px; cursor: hand;}
			.scrollable-button-item-separator{float: left; background: url(images/button_separator.gif) no-repeat 0 0; width: 2px; height: 26px;}
			.scrollable-right-button{float: left; background: url(images/scrollable_navigate_arrow_right.gif) no-repeat 0 0; width: 16px; height: 26px; cursor: hand;}
			.scrollable-right-button:hover{background-position: 0 -26px;}
		&lt;/style&gt;
	&lt;/head&gt;
	&lt;body&gt;
		&lt;div class="scrollable"&gt;
			&lt;div id="scrollable_left_button" class="scrollable-left-button" onmouseover="scroll=true;scrollToRight(7, event.srcElement);" onmouseout="scroll=false;" onmousedown="scroll=true;scrollToRight(14, event.srcElement);" onmouseup="scroll=false;" &gt;&lt;/div&gt;
			&lt;div id="scrollable_container" class="scrollable-container"&gt;
				&lt;div id="scrollable_content" class="scrollable-content"&gt;
				&lt;!-- 
				如果是动态界面, 一般是这个地方循环输出item, 循环的时候根据字号, 字数以及间隔宽度计算每个菜单的长度
				比如, 我之前项目中使用下面代码进行菜单生成和长度计算, 
				每个字12px的这里按13px计算刚好, 加的8, 其中左右3px的padding以及2px的间隔(separator):
							&lt;c:forEach var="menu" items="${menus}"&gt;
								&lt;mce:script type="text/javascript"&gt;&lt;!--
								contentWidth += '${menu.title }'.length * 13 + 8;
								
// --&gt;&lt;/mce:script&gt;
								&lt;div class="scrollable-button-item"&gt;
									&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;菜单&lt;/span&gt;&lt;/div&gt;
									&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
								&lt;/div&gt;
							&lt;/c:froEach&gt;
				 --&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单一&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单二&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单三&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单四&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单五&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单六&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单七&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单八&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单九&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单十&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单一&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单二&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单三&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单四&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单五&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单六&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单七&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单八&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单九&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
						&lt;div class="scrollable-button-item"&gt;
							&lt;div class="scrollable-button-item-content"&gt;&lt;span&gt;测试的菜单十&lt;/span&gt;&lt;/div&gt;
							&lt;div class="scrollable-button-item-separator"&gt;&lt;/div&gt;
						&lt;/div&gt;
				&lt;/div&gt;
			&lt;/div&gt;
			&lt;div id="scrollable_right_button" class="scrollable-right-button" onmouseover="scroll=true;scrollToLeft(7, event.srcElement);" onmouseout="scroll=false;" onmousedown="scroll=true;scrollToLeft(14, event.srcElement);" onmouseup="scroll=false;"&gt;&lt;/div&gt;
		&lt;/div&gt;
		&lt;center&gt;
			&lt;div class="author-information"&gt;
				&lt;div class="author-information-label"&gt;网络昵称:&lt;/div&gt;&lt;div class="author-information-information"&gt;selfimpr&lt;/div&gt;
				&lt;div class="author-information-label"&gt;个人博客:&lt;/div&gt;&lt;div class="author-information-information"&gt;&lt;a href="http://blog.csdn.net/lgg201" mce_href="http://blog.csdn.net/lgg201"&gt;http://blog.csdn.net/lgg201&lt;/a&gt;&lt;/div&gt;
				&lt;div class="author-information-label"&gt;E-mail:&lt;/div&gt;&lt;div class="author-information-information"&gt;&lt;a href="mailto:lgg860911@yahoo.com.cn" mce_href="mailto:lgg860911@yahoo.com.cn"&gt;lgg860911@yahoo.com.cn&lt;/a&gt;&lt;/div&gt;
				&lt;div class="author-information-label"&gt;声明:&lt;/div&gt;&lt;div class="author-information-information"&gt;任何形式的转载请保留原作者信息.&lt;/div&gt;
				&lt;div class="author-information-label"&gt;欢迎访问:&lt;/div&gt;&lt;div class="author-information-information"&gt;&lt;a href="http://www.heyjava.com" mce_href="http://www.heyjava.com"&gt;http://www.heyjava.com&lt;/a&gt;&lt;/div&gt;
			&lt;/div&gt;
		&lt;/center&gt;
	&lt;/body&gt;
&lt;/html&gt;
&lt;mce:script type="text/javascript"&gt;&lt;!--
	//这里设置的730就是上面计算出来的总长度, 加10是为了hover时候字体变大宽度也可以适应.
	document.getElementById('scrollable_content').style.width = (1720 + 10) + 'px';
	resizeCallback();
// --&gt;&lt;/mce:script&gt;
```

 
 
相关图片:
左按钮:![](http://p.blog.csdn.net/images/p_blog_csdn_net/lgg201/EntryImages/20091203/scrollable_navigate_arrow_left633954800958750000.gif)
 
右按钮:![](http://p.blog.csdn.net/images/p_blog_csdn_net/lgg201/EntryImages/20091203/scrollable_navigate_arrow_right633954800958750000.gif)
 
菜单背景:![](http://p.blog.csdn.net/images/p_blog_csdn_net/lgg201/EntryImages/20091204/button_content.gif)
 
菜单间隔:![](http://p.blog.csdn.net/images/p_blog_csdn_net/lgg201/EntryImages/20091204/button_separator.gif)
 
 
