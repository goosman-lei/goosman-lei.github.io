---
layout: post
title: IE7中onpropertychange引发的Stack overflow at line xxx问题解决
date: 2010-06-17 15:23:00
categories: [ie, function, input, 扩展, string, 浏览器]
tags: []
---
与之相关的链接:
http://blog.csdn.net/lgg201/archive/2010/05/28/5630392.aspx
http://blog.csdn.net/lgg201/archive/2010/05/28/5629526.aspx
 

```javascript
$('#diary_title').bind('keydown', function(event) {
    if($(this).val().len() &gt;= 50) $(this).attr('maxLength', $(this).val().length);
}).bind('input', function(event) {
    $(this).val($(this).val().chinesesubstr(0, 50));
}).bind('propertychange', function(event) {
    $(this).val($(this).val().chinesesubstr(0, 50));
});
```

 
对字符串扩展, 并使用了上面代码做了input的长度限制之后, 运行一直良好, 直到今天测试提到IE7下输入框键一直按下输入导致的Stack overflow at line xxx的错误...
 
本来以为是自己些的截取字符串方法造成的溢出, 结果不是, 不过所幸找到一个原来字符串截取的bug, 以下是修正后的汉字截取

```javascript
/**
 * 扩展了String的chinesesubstr方法, 用于非ascii的字符截取
 * @param {} begin 开始字符数
 * @param {} num 截取的字符数
 * @return {}
 */
String.prototype.chinesesubstr = (function(begin, num) {
	var ascRegexp = /[^/x00-/xFF]/g, i = 0;
	var chs = this.toCharArray();
	while(i++ &lt; begin) (ascRegexp.test(this.charAt(i)) &amp;&amp;	begin --);
	i = begin;
	var end = begin + num;
        //需要对是否到达字符串末尾进行检测
	while(i++ &lt; end &amp;&amp; i &lt; this.length) (ascRegexp.test(this.charAt(i)) &amp;&amp; end --);
	return this.substring(begin, end);
});
```

 
万般无奈, 看到IE7响应的是propertychange事件, 就在该事件中增加了alert, 发现的问题是, 输入或点击等操作, 只要稍微碰触到该元素, 就会导致多次alert, 我们知道, 其实alert是modal的, 也就是它弹出来之后, 我们对浏览器是无法操作的, 因此, 就猜测propertychange在IE7是异步响应的, 因此, 在propertychange事件的处理中把多余的调用屏蔽了(propertychange对我来说, 不需要那么频繁的响应, 一个输入动作一次足以), 如下:

```c-sharp
$('.selector').bind('input', function(event) {
		if($.syncProcessSign) return ;
		$.syncProcessSign = true;
		if($(this).val().len() &gt;= 60) $(this).val($(this).val().chinesesubstr(0, 60));
		$.syncProcessSign = false;
}).bind('propertychange', function(event) {
		if($.syncProcessSign) return ;
		$.syncProcessSign = true;
		if($(this).val().len() &gt;= 60) $(this).val($(this).val().chinesesubstr(0, 60));
		$.syncProcessSign = false;
})
```

增加一个全局的同步信号, 如果正在处理, 就直接返回.....
果然, 问题解决了......
 
依然疑惑的是IE7中, propertychange事件的响应究竟是因为异步还是因为bug导致一个动作引发多次响应.
