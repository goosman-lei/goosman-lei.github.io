---
layout: post
title: SiteMesh官方文档翻译(含Freemarker但不包括velocity部分)
date: 2010-01-30 10:46:00
categories: [freemarker, velocity, 文档, decorator, include, class]
tags: []
---
#安装配置
1. 创建普通的web项目或直接使用sitemesh-blank.war
2. 将sitemesh-2.4.1.jar拷贝到[web-app]/WEB-INF/lib下
3. 创建sitemesh的配置文件: [web-app]/WEB-INF/decorators.xml, 顶级标签为:
<decorators>
</decorators>
4. (可选的): 创建文件[web-app]/WEB-INF/sitemesh.xml, 包含以下内容:
<sitemesh>
       <!-- 指定装饰器配置文件路径 -->
       <property name=”decorators-file” value=”/WEB-INF/decorators.xml” />
       <excludes file=”${decorators-file}” />
 
       <page_parsers>
              <parser content-type=”text/html”
                            class=”com.opensymphony.module.sitemesh.parser.HTMLPageParser”  />
              <parser content-type=”text/html; charset=ISO-8859-1”
                            class=”com.opensymphony.module.sitemesh.parser.HTMLPageParser” />
       </page_parsers>
 
       <decorator-mappers>
              <mapper 
class=”com.opensymphony.module.sitemesh.mapper.ConfigDecoratorMapper”>
<param name=”config” value=”${decorators-file}” />
              </mapper>
       </decorator-mappers>
</sitemesh>
5. 在[web-app]/WEB-INF/web.xml中的<web-app>标签内增加下面内容将sitemesh加入到应用中
<filter>
       <filter-name>sitemesh</filter-name>
       <filter-class>com.opensymphony.sitemesh.webapp.SiteMeshFilter</filter-class>
</filter>
<filter-mapping>
       <filter-name>sitemesh</filter-name>
       <url-pattern>/*</url-pattern>
</filter-mapping>
#字符集
1. 默认编码: iso8859-1
2. 设置服务器解析后台页面的编码: <%@ page contentType=”text/html; charset=utf-8”%>
3. 告知浏览器解析界面的编码: <meta http-equiv=”content-type” content=”text/html; charset=utf-8” >
4. 设置sitemesh的装饰器解析时使用的编码
<page:applyDecorator name=”form” encoding=”utf-8”>
       ……
</page:applyDecorator>
#构造装饰器
1. 兼容的一些装饰漆:
1.1. meta tags(关键字, 描述, 作者)
1.2. stylesheet(CSS样式表)
1.3. header(头部)
1.4. navigation(导航)
1.5. footer(底部)
1.6. copyright notice(版权声明)
2. 首先, 需要定义各种导航/布局, 比如: 是否需要一个默认的装饰器(一个对所有页面适用的标准装饰器)? 是否在首页有特定的布局? 文档中是否需要头部? 网站是否需要打印版?
3. 下面是一个web应用程序的示例结构, sitemesh不是必须的.
/decorators: 包含所有的装饰器文件的目录
/includes: 将要被其他文件包含的所有文件
/images: 包含所有的图片
/styles: 包含所有的样式表
/scripts: 包含所有的脚本文件
4. 良好的习惯:
4.1. 将整个应用都使用的样式表都放入同一个页面定义, 并按照下面方式引入:
**<%**
**String userAgent = request.getHeader("User-Agent");**
**if (userAgent != null && userAgent.indexOf("MSIE") == -1) {**
**       out.print("<link href=/"" + request.getContextPath() + "/styles/ns4.css/" rel=/"stylesheet/" type=/"text/css/">");**
**} else {**
**       out.print("<link href=/"" + request.getContextPath() + "/styles/ie4.css/" rel=/"stylesheet/" type=/"text/css/">");**
**}**
**%>**
4.2. 在你的装饰器中使用includes(比如: includes/navigation.jsp)
4.3. 不要尝试使用绝对路径(/), 用<%=request.getContextPate() %>去代替, 这会使得应用在发生变迁的时候变得非常容易.
4.4. 使你的装饰器兼容多浏览器(比如IE, Mozilla, Opera…)将会大大提高整个应用的兼容性
4.5. 使用frame (框架)的时候要特别小心, 因为装饰器可能不支持frame
5. 第一个装饰器: 最基本的, 你仅仅需要知道可以使用的一些装饰器标签, title, head, body这三个标签可能是经常使用的.
`1: <%--`
`2: % ``这个主装饰器应用于所有的页面.`
`3: %``它包含标准的缓存, 样式表, 头部, 底部和版权声明.`
`4: --%>`
`5: **<%@ taglib uri="http://www.opensymphony.com/sitemesh/decorator" prefix="decorator" %>**`
`6: <%@ include file="[/includes/cache.jsp](file:///D:/sitemesh-2.4.1/docs/cache.jsp.txt)" %>`
`7: <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">`
`8: <html>`
`9: <head>`
`10: <title>**<decorator:title default="INTRANET" />**</title>`
`11: **<decorator:head />**`
`12: **<%@ include file="[/includes/style.jsp](file:///D:/sitemesh-2.4.1/docs/style.jsp.txt)" %>**`
`13: </head>`
`14: <body bgcolor="#FFFFFF" background="<%=request.getContextPath()%>/images/bg.gif">`
`15: <script type="text/javascript">window.status = "Loading: **<decorator:title default="INTRANET" />**...";</script>`
`16: <%@ include file="/includes/header.jsp"%>`
`17: <table width="100%" border="0" cellspacing="0" cellpadding="0">`
`18: <tr>`
`19: <td height="20" nowrap> </td>`
`20: </tr>`
`21: <tr>`
`22: <td width="1%" nowrap> </td>`
`23: <td width="16%" valign="top" nowrap>`
`23: <script type="text/javascript">window.status = "Loading: Navigation...";</script>`
`24: <%@ include file="/includes/navigation.jsp" %>`
`25: </td>`
`26: <td width="2%" nowrap> </td>`
`27: <td valign="top">`
`28: <br>`
`29: <script type="text/javascript">window.status = "Loading: Document body...";</script>`
`30: <div class="docBody">**<decorator:body />**</div>`
`31: </td>`
`32: <td width="1%" nowrap> </td>`
`33: </tr>`
`34: </table>`
`35: <br>`
`36: **<%@ include file="/includes/footer.jsp" %>**`
`37: **<%@ include file="/includes/copyright.jsp" %>**`
`38: <script type="text/javascript">window.status = "Done";</script>`
`39: </body>`
`40: </html>`
` `
`第1-4行: 对装饰器的解释, 这种方式不同于一般的马上进行装饰器的工作.`
`第5行: 引入标签库, 这对所有要使用内部装饰器的页面都是必须的`
`第6行: 设置通知浏览器缓存页面的响应头, 如果你的应用是经常变动的, 省略这里`
`第10行: 如果请求页面没有title, 默认title将使用”INTRANET”`
`第15行: 页面在加载的时候状态条的消息`
`第30行: 整个请求页面的body放入docBody. 将导航和body进行了划分.`
6. 现在用你喜欢的编辑器打开WEB-INF/decorators.xml让sitemesh知道你有了一个装饰器(通过映射):
<decorators defaultdir=”/decorators”>
       <decorator name=”main” page=”main.jsp”>
              <pattern>/*</pattern>
       </decorator>
</decorators>
7. 现在部署你的web应用, 访问欢迎界面, main装饰器就会被应用上.
#freemarker支持
1. sitemesh2.0.2之后开始支持freemarker
	<#include "/includes/decorators/header.dec">
	    <h2>**${title}**</h2>
	    **${head}**
	    <img src="**${base}**/images/logo.gif" border="0">
	    <td valign="top" class="body">
	        <div class="header">
	            <span class="pagetitle">**${title}**</span>
	        </div>
	        **${body}**
	    </td>
	<#include "/includes/decorators/footer.dec">
2. 安装freemarker支持
2.1. 拷贝freemarker.jar到[web-app]/WEB-INF/lib下
2.2. 在web.xml中增加下面内容:
    <servlet>
       <servlet-name>sitemesh-freemarker</servlet-name>
        <servlet-class>com.opensymphony.module.sitemesh.freemarker.FreemarkerDecoratorServlet</servlet-class>
       <init-param>
           <param-name>TemplatePath</param-name>
           <param-value>/</param-value>
       </init-param>
       <init-param>
           <param-name>default_encoding</param-name>
           <param-value>ISO-8859-1</param-value>
       </init-param>
       <load-on-startup>1</load-on-startup>
    </servlet>
 
    <servlet-mapping>
       <servlet-name>sitemesh-freemarker</servlet-name>
       <url-pattern>*.dec</url-pattern>
    </servlet-mapping>
2.3. 修改decorators.xml中要使用freemarker的decorator的page指向一个后缀为dec的文件.
3. FreemarkerDecoratorServlet向contex对象中放入了一些东西可以在模板中使用
3.1. 基础属性:
3.1.1. 所有的request, request参数, session, servlet context属性变量${Session[“user”]}
3.1.2. 创建变量: <#assign ww=JspTaglibs[“/WEB-INF/webwork.tld”]>  使用创建的变量去加载jsp taglibs: <@ww.property value=”myVar” />
3.2. sitemesh的context 属性:
3.2.1. base: request.getContextPath()
3.2.2. title: 解析页面标题
3.2.3. head: 解析页面头部
3.2.4. body: 解析页面体
3.2.5. page: 内部页面对象
#装饰器映射
1. 当一个页面被解析的时候, 它会被映射成为一个装饰器, 这个映射就扮演了链接DecoratorMappers的角色.
2. 对于每一个请求, 整个过程的第一个mapper要求知道使用哪个装饰器, 如果知道使用哪个装饰器, 传递一个页面对象和HttpServletRequest, 返回一个装饰器对象, 否则返回null. 如果返回了null, 下一个mapper继续查询, 重复这个过程直到没有mapper或返回了一个合法的装饰器, 如果没有mapper返回装饰器, 页面将不会被装饰以原始的状态返回.
3. 这种mapper的链式协同使用了责任链设计模式.
4. mapper:
4.1. 通过请求页面路径确定装饰器
4.2. 基于时间, 地域, 浏览器使用不同的装饰器
4.3. 为搜索引擎机器人使用简单的装饰器
4.4. 基于URL参数, 请求属性或meta标签切换装饰器
4.5. 使用用户自定义装饰器基于用户配置.
5. DecoratorMapper的主实现是从/WEB-INF/decorators.xml读取装饰器映射的ConfigDecoratorMapper, 它根据配置的url pattern使用合适的装饰器
6. DecoratorMappers书写很简单, 在发布包中包含了一些示例说明它的写法以及灵活性, 它们是:
6.1. AgentDecoratorMapper: 
6.2. ConfigDecoratorMapper
6.3. CookieDecoratorMapper: 基于cookie值映射
6.4. EnvEntryDecoratorMapper: 
6.5. FileDecoratorMapper: 
6.6. FrameSetDecoratorMapper:
6.7. InlineDecoratorMapper
6.8. LanguageDecoratorMapper
6.9. PageDecoratorMapper
6.10. ParameterDecoratorMapper
6.11. SessionDecoratorMapper
6.12. PrintableDecoratorMapper
6.13. RobotDecoratorMapper
#标签
1. sitemesh有两个标签库
2. 装饰器标签: 这类标签用于创建页面装饰器, 一个装饰器通常是通过标签插入一些占位符然后用原始页面中的数据填充以构建一个HTML布局(或者其他合适的原始页面类型).
2.1. <decorator:head />: 没有属性. 向页面中插入原始页面<head />标签内部的内容, 但不会包含标签自身.
2.2. <decorator:body />: 没有属性.  插入原始页面<body />中的内容, 但不包括标签自身.
2.2.1. 注意: body内容的onload和onunload事件(和其他的body属性)可以通过获取属性的的标签得到并包含在装饰器中.
2.2.2. 例如: <body onload=”<decorator:getProperty property=/”body.onload/” />” />
2.3. <decorator:title [default=””] />: 插入原始页面的<title />中的内容, 但不包括标签自身, 如果原始页面没有title标签, 那么使用default指定的字符串.
2.4. <decorator:getProperty property=”” [default=””] [writeEntireProperty=””] />: 插入原始页面中某个属性的值.
2.4.1. property: 指定要获取的属性名称. 下面对常见的用法解释中所有的标签是指原始页面
2.4.1.1. <html />标签的属性: 直接使用名称获取. 比如: <html template=”funky” /> 则有template=funky
2.4.1.2. 某个标签内部的内容: 用标签名获取title=My Funky Page
2.4.1.3. 普通标签的属性: 标签名.属性名获取body.bgcolor=green
2.4.1.4. meta标签的值: meta.名称, 比如: <meta name=”author” content=”Bob” /> 就可以使用meta.author获取到Bob
2.4.2. default: 如果没有找到指定的属性值的默认值
2.4.3. writeEntireProperty: 前置一个空格并且包含属性名称的完全形式
2.4.3.1. 可以接受的值: true, yes, 1, 默认是不使用这种方式的
2.4.3.2. 以<body <decorator:getProperty property=”body.onload” />>为例, 假设body.onload属性值为window.alert();:
2.4.3.2.1. 不设置此属性: <body window.alert();>, 也就是说不使用writeEntireProperty就需要手动设置属性的名称在模板页中.
2.4.3.2.2. 设置此属性: <body onload=”window.alert();” />
2.5. <decorator:usePage id=”” />: 将page对象暴露成为一个装饰器jsp中的变量.
2.5.1. id: page对象的名称
2.5.2. 示例:
<decorator:usePage id="myPage" />
<% if ( myPage.getIntProperty("rating") == 10 ) { %>
  <b>10 out of 10!</b>
<% } %>
2.5.3. 注意: 这里引入的page对象, 实际上就是前面一直说的原始页面, 也就是我们的url所请求的页面.
3. page标签: 用于在当前页面中装饰内联的或外部的内容.
3.1. <page:applyDecorator name=”” [page=”” title=””]> </page:applyDecorator>: 装饰器的包含.
3.1.1. name: decorators.xml中配置的装饰器的名字.
3.1.2. page: 对于要应用的装饰器要使用的页面对象
3.1.3. title: 和page一起才有效, 是重写page指定的页面对象的title
3.2. <page:param name=””></page:param>: 在<page:applyDecorator />内部为请求的页面指定参数.
