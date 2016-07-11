在eclipse开发中 ，可能由于各种原因导致项目文件（eclipse内置）丢失或错乱， 因而， 失去一些能力。。
 
以下是经过被痛苦折磨后查阅总结的一点东西
 

@author: selfimpr
@mail: lgg860911@yahoo.com.cn
@blog: http://blog.csdn.net/lgg201
 

```xhtml
设置完下面内容， 支持以下特性
PHP： 库函数， 本项目， 跨项目代码提示
Javascript： 代码提示。
以下.project, .buildpath文件均在项目根目录下
.project文件： 用于说明项目基本信息及其使用的构建工具， 下面是典型配置
&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;
&lt;projectDescription&gt;
	&lt;!-- 项目名称 --&gt;
	&lt;name&gt;oo&lt;/name&gt;
	&lt;comment&gt;&lt;/comment&gt;
	&lt;projects&gt;
	&lt;/projects&gt;
	&lt;buildSpec&gt;
		&lt;buildCommand&gt;
			&lt;name&gt;org.eclipse.wst.validation.validationbuilder&lt;/name&gt;
			&lt;arguments&gt;
			&lt;/arguments&gt;
		&lt;/buildCommand&gt;
		&lt;!-- 加这个可以使用javascript代码提示 --&gt;
		&lt;buildCommand&gt;
		    &lt;name&gt;org.eclipse.wst.jsdt.core.javascriptValidator&lt;/name&gt;
		    &lt;arguments&gt;
		    &lt;/arguments&gt;
		&lt;/buildCommand&gt;
		&lt;buildCommand&gt;
			&lt;name&gt;org.eclipse.dltk.core.scriptbuilder&lt;/name&gt;
			&lt;arguments&gt;
			&lt;/arguments&gt;
		&lt;/buildCommand&gt;
	&lt;/buildSpec&gt;
	&lt;natures&gt;
		&lt;nature&gt;org.eclipse.php.core.PHPNature&lt;/nature&gt;
		&lt;nature&gt;org.eclipse.wst.jsdt.core.jsNature&lt;/nature&gt;
	&lt;/natures&gt;
&lt;/projectDescription&gt;
.buildpath: 项目路径配置
&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;
&lt;buildpath&gt;
	&lt;!-- 自己要使用的其他源文件路径 --&gt;
	&lt;buildpathentry kind=&quot;src&quot; path=&quot;../conf&quot;/&gt;
	&lt;!-- PHP语言库 --&gt;
	&lt;buildpathentry kind=&quot;con&quot; path=&quot;org.eclipse.php.core.LANGUAGE&quot;/&gt;
&lt;/buildpath&gt;

```

