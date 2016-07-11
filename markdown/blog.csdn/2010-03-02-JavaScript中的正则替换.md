String.replace(regexp, replaceText); 这是String类中的replace方法原型
 
replace方法接受两个参数:
regexp: 正则表达式, 用来在字符串中搜索的规则.
replaceText: 用来替换字符串中匹配正则表达式的子串的字符串
 
在JavaScript中, 支持正则替换, 正则替换的规则如下:
$$: 原意打印一个$符号
$&: 与规则匹配的整个子串
$`(大键盘1旁边的键): 整个字符串中, 与规则匹配的子串之前的部分
$'(单引号): 整个字符串中, 与规则匹配的子串之后的部分
$n: $1, $2等从1-9的数值, 代表正则匹配得到的第n个子组的匹配子串
$nn: 第01-99个子组的匹配子串.
 
 
以字符串"abcdefg"使用正则表达式/(?:(bc)(de)(f))/进行正则替换为例(?:)表示是非捕获子组, 属于正则表达式范畴, 这里不做讨论
则有:
$&代表bcdef
$`代表a
$'代表g
$1代表bc
$2代表de
$3代表f
 
下面是一个比较完整的例子, 在例子中为了方便操作, 使用了jQuery, 可以到[jQuery官方网站](http://jquery.com/)下载
[http://jquery.com/](http://jquery.com/)
 

```xhtml
&lt;!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"&gt;
&lt;html&gt;
	&lt;head&gt;
		&lt;meta http-equiv="Content-Type" content="text/html; charset=UTF-8"&gt;
		&lt;title&gt;Javascript练习-2-正则表达式&lt;/title&gt;
		&lt;mce:script type="text/javascript" src="scripts/jquery-1.3.2.js" mce_src="scripts/jquery-1.3.2.js"&gt;&lt;/mce:script&gt;
		&lt;mce:script type="text/javascript"&gt;&lt;!--
	$(function() {
		var body = $('body');
		function append(msg) {
			return body.append($('&lt;div&gt;').text(msg));
		}
		function appendHtml(msg) {
			return body.append($('&lt;div&gt;').html(msg));
		}
		function appendHr() {
			return body.append($('&lt;hr&gt;'));
		}
		var re = /(https?):////(/w+(?:/./w+){2,})///?((/w+)=(/w+))/;

		appendHtml("[prefix]http://www.google.com/?user=selfimpr[suffix].replace(re4, " +
				"" + 
				"/"{" + 
				"url: '$&amp;', " + 
				"prefix: '$`', " + 
				"suffix: '$'', " + 
				"protocal: '$1', " + 
				"server: '$2', " + 
				"queryString: '$3', " + 
				"argName: '$4', " + 
				"argValue: '$5'" + 
				"}/")");
		appendHtml('&lt;font color="red"&gt;' + "[prefix]http://www.google.com/?user=selfimpr[suffix]".replace(re4, 
				"&lt;br /&gt;" + 
				"{&lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "url: /"$&amp;/", &lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "prefix: /"$`/", &lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "suffix: /"$'/", &lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "protocal: /"$1/", &lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "server: /"$2/", &lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "queryString: /"$3/", &lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "argName: /"$4/", &lt;br /&gt;" + 
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;" + "argValue: /"$5/"&lt;br /&gt;" + 
				"}" + 
				"&lt;br /&gt;") + "&lt;/font&gt;");

	});
// --&gt;&lt;/mce:script&gt;
	&lt;/head&gt;
	&lt;body&gt;
	&lt;/body&gt;
&lt;/html&gt;
```

