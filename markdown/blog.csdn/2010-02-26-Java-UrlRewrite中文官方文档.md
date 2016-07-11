#安装
1. 下载jar包, 并加入到WEB-INF/lib下
2. 在WEB-INF/web.xml中增加下面的配置
`<filter>`
`<filter-name>UrlRewriteFilter</filter-name>`
`<filter-class>`
`org.tuckey.web.filters.urlrewrite.UrlRewriteFilter`
`</filter-class>`
`</filter>`
`<filter-mapping>`
`<filter-name>UrlRewriteFilter</filter-name>`
`<!-- ``拦截所有的``url -->`
`<url-pattern>/*</url-pattern>`
`<dispatcher>REQUEST</dispatcher>`
`<dispatcher>FORWARD</dispatcher>`
`</filter-mapping>`
3. 在WEB-INF/下增加配置文件urlrewrite.xml
4. 重启上下文环境
#过滤器的参数
1. `**confReloadCheckInterval: **`配置文件重加载间隔. 0表示随时加载, -1表示不重加载, 默认-1
2. `**confPath: **`配置文件路径. 是相对context的路径, 默认/WEB-INF/urlrewrite.xml
3. `**logLevel: **`设置日志级别, 可以是: TRACE, DEBUG, INFO(默认), WARN, ERROR, FATAL, log4j, commons, slf4j, sysout:{level}(比如 sysout:DEBUG), 如果你使用普通的日志级别有一定困难, 可以调为: sysout:DEBUG(表明是使用控制台输出的调试级别)
4. `**statusPath: **`设置改变状态路径, 不能和已经安装的应用冲突(注意, 默认是/rewrite-status), 注意, 必须以/开始
5. `**statusEnabled: **`设置status是否开启, 期望得到的值是true, false, 默认true
6. `**statusEnabledOnHosts: **`设置允许status的主机, *可以被用作通配符, 默认是”localhost, local, 127.0.0.1”
7. `**modRewriteConf: **`设置rewrite模式, 默认是false, 使用mod-rewrite(可以参照apache服务器的mod_rewrite相关资料)方式的配置文件, 如果设置为true并且confPath没有设置则配置文件路径将会被默认为/WEB-INF/.htaccess
8. `**modRewriteConfText: **`从这些参数的值加从载mod_rewrite样式的配置, 设置这些参数则其他所有的参数都会被忽略. 比如:
`<init-param>`
`<param-name>modRewriteConfText</param-name>`
`<param-value>`
`RewriteRule ^/~([^/]+)/?(.*) /u/$1/$2 [R]`
`RewriteRule ^/([uge])/([^/]+)$ /$1/$2/ [R]`
`</param-value>`
`</init-param>`
9. `**allowConfSwapViaHttp: **`设置是否允许通过HTTP方式交互设置参数, 比如, 通过调用/rewrite-status/?conf=WEB-INF/urlrewrite2.xml
#配置文件WEB-INF/urlrewrite.xml
1. DTD约束
	<!DOCTYPE urlrewrite
	        PUBLIC "-//tuckey.org//DTD UrlRewrite 3.0//EN"
	        "http://tuckey.org/res/dtds/urlrewrite3.0.dtd">
2. <urlrewrite />
2.1. default-match-type(可选): 
2.1.1. regex, 默认. 所有未指定match-type属性的rule都使用java正则表达式进行匹配
2.1.2. wildcard: 所有未指定match-type属性的rule都使用通配符匹配引擎匹配
2.2. decode-using(可选):
2.2.1. header, utf8: 默认. 使用request.getCharacterEncoding()得到的编码对URL解码, 如果是空, 使用utf8.
2.2.2. null: 不进行解码. 设置为: decode-using=”null”
2.2.3. header: 仅仅使用request.getCharacterEncoding()解码
2.2.4. [encoding]: 仅仅使用一个指定的字符编码比如ISO-8859-1.
2.2.5. header, [encoding]: 对一个URL解码时使用request.getCharacterEncoding(), 如果得到的值为空, 则使用encoding指定的编码.
2.3. use-query-string(可选):
2.3.1. false: 默认. 在from进行匹配的时候, 查询字符串不会参加
2.3.2. true: 查询字符串参与from的匹配
2.4. use-context(可选):
2.4.1. false: 默认. from元素匹配时, application的contex路径将不会增加到url中
2.4.2. true: application的contex路径参与from元素的匹配
3. <rule />: 0个或多个
3.1. enabled(可选):
3.1.1. true: 默认.允许这个规则
3.1.2. false: 废弃这个规则
3.2. match-type(可选):
3.2.1. regex: 默认. 使用java正则匹配
3.2.2. wildcard: 使用通配符表达式引擎
4. <outbound-ruld />: 0个或多个. 和普通的rule非常相似, 但是这里是在response.encodeURL()方法调用时进行重写的.
4.1. enabled(可选):
4.1.1. true: 默认. 允许规则
4.1.2. false: 废弃规则
4.2. encodefirst(可选):
4.2.1. fasle: 默认, 在运行了encodeURL()方法之后运用这个重写规则
4.2.2. true: 在encodeURL()之前运用这个重写规则
5. <name />: 一个用于记录规则名称的可选元素, 可以在<rule />和<outbound-rule />上使用
6. <note />: 用于记录规则描述的一个简单可选元素, 可以用在<rule />和<outbound-rule />上.
7. <condition />: 针对规则的选择条件. 注意, 在规则运用的时候必须满足所有的条件.
7.1. type(可选): 
7.1.1. header: 默认. 如果设置, 头名称必须通过<condition />的name属性指定
7.1.2. method: 请求方法. GET, POST, HEAD等
7.1.3. port: application运行的端口
7.1.4. time: 服务器当前时间(使用Unix时间戳), 这个通常被用于确保内容仅在设置的时间存活
7.1.5. year: 服务器的当前年
7.1.6. month: 服务器的当前月份
7.1.7. dayofmonth: 当天是一月的第几天, 每月第一天是1
7.1.8. dayofweek: 当天是一周的第几天, 星期天是7
7.1.9. ampm: 上午或下午
7.1.10. hourofday: 一天的第多少小时(24小时制)
7.1.11. minute: 当前服务器时间的分
7.1.12. second: 当前服务器时间的秒
7.1.13. millisecond: 当前服务器时间的毫秒
7.1.14. attribute: 检查request的属性(getAttribute)值, 要检查的属性名称通过<condition />的name指定
7.1.15. auth-type: 检查request属性的值.   request.getAuthType
7.1.16. character-encoding: 接收到请求的编码
7.1.17. content-length: 请求的长度(对于拒绝响应大请求很有用)
7.1.18. content-type: 请求类型
7.1.19. context-path: 请求的contex路径
7.1.20. cookie: 检查cookie值, cookie的名称通过<condition />的name属性指定
7.1.21. parameter: 检查请求参数, 参数名称通过<condition />的name属性指定
7.1.22. path-info: 相当于request.getPathInfo()
7.1.23. path-translated: 相当于request.getTranslated()
7.1.24. protocol: 用于过滤协议
7.1.25. query-string: 得到url后面的参数字符串
7.1.26. remote-addr: IP地址过滤
7.1.27. remote-host: 远程主机过滤(注意, 仅仅在应用服务器配置了查看(远程)主机名时才可用)
7.1.28. remote-user: 当前登录用户, 如果用户被授权可用
7.1.29. requested-session-id: 当前session的id
7.1.30. request-uri: 请求URL的从协议名到查询字符串部分
7.1.31. request-url: 重构后的URL, 返回的URL包含协议, 服务器名称, 端口, 路径, 但不包含查询字符串
7.1.32. session-attribute: 检查session中的属性(getAttribute), 属性名称通过<condition />的name属性设置.
7.1.33. session-isnew: 检查session是不是新的
7.1.34. server-name: 请求发送到的服务器的主机名(从host这个头中得到的不是机器名)
7.1.35. scheme: 请求的scheme
7.1.36. user-in-role: 注意, 这里的值不能是正则表达式
7.2. name: 配合一些特殊type使用的, 可以是任何值
7.3. next: 
7.3.1. and: 默认. 下一个和这一个条件都必须匹配
7.3.2. or: 下一个或这一个条件匹配
7.4. operator: 
7.4.1. equal: 默认. 指定正则和真实值匹配
7.4.2. notequal: 真实值和正则不匹配
7.4.3. greater: 大于, 仅用于数值
7.4.4. less: 小于
7.4.5. greaterorequal: 大于等于
7.4.6. lessorequal: 小于等于
8. <from />: 通常在<rule />和<outbound-rule />中都必须指定一个, 值可以是正则表达式(Perl5方式的正则), 注意: from指定的url是和contex相关的
8.1. casesensitive: 
8.1.1. false: 默认. 大小写不敏感
8.1.2. true: 大小写敏感
9. <to />: 可以是一个perl5样式的正则替换表达式
9.1. type:
9.1.1. forward: 默认. 请求匹配这个<rule />的所有<condition />, 并且URL使用内部跳转到”to”指定的地址(注意, 这里forward到的URL必须和UrlRewriteFilter位于同一个容器中)
9.1.2. passthrough: 和forward相同
9.1.3. redirect: 请求匹配所有<condition />和这个<rule />的<from />, 通知客户端跳转到<to />指定地址
9.1.4. permanent-redirect: 相当于做了以下事情
response.setStatus(
        HttpServletResponse.SC_MOVED_PERMANENTLY
);
response.setHeader(“Location”, [<to />指定的值]);
9.1.5. temporary-redirect: 相当于做了以下事情
response.setStatus(
        HttpServletResponse. SC_MOVED_TEMPORARILY
);
response.setHeader(“Location”, [<to />指定的值]);
9.1.6. pre-include
9.1.7. post-include
9.1.8. proxy: 请求URL将会以全路径被代理, 使用此特性需要引入commons-http和commons-codec包
9.2. last: 
9.2.1. false: 默认. 其余<rule />将会处理如果这个匹配
9.2.2. true: 如果匹配这个规则将不会处理
9.3. encode:
9.3.1. false: <rule />下是默认值. 在rewrite之前, 用response.encodeURL([to的值])编码URL
9.3.2. true: <outbound-rule />下默认值. 不会编码URL
9.4. context: 
如果应用服务器配置了允许”穿透context”通信, 那么这个属性可以被用于forward(并且仅仅能用于forward)请求到另外一个serlvet context…..也就是跨应用forward
在Tomcat上, server.xml或context.xml中配置crossContext=”true”, 例如: 允许两个应用”app”和”forum”之间通信, 那么可以如下配置:
<Context docBase=”app” path=”/app” reloadable=”true” crossContext=”true” />
<Context docBase=”forum” path=”/forum” reloadable=”true” crossContext=”true” />
10. <to />的其他方面
10.1. <to />可以是null, 意义为: 如果匹配请求不再继续, 相当于没有调用chain.doFilter
10.2. 使用$N获取<from />中配置的子组, N必须是1至10之间的数
10.3. 任何<condition />中可以使用的type中的值都可以在<to />中使用, 比如<to>/%{parameter:page}</to>
10.4. 函数调用: ${函数名: 参数1:参数2}  可以在<set />和<to />中使用
**name**
**example**
**example returns**
replace
${replace:my cat is a blue cat:cat:dog}
my dog is a blue dog
replaceFirst
${replace:my cat is a blue cat:cat:dog}
my cat is a blue dog
escape
${escape:a b c}
a+b+c
unescape
${unescape:a+b+c}
a b c
lower
${lower:Hello World}
hello world
upper
${upper:hello}
HELLO
trim
${trim: abc def }
abc def
11. <set />: 在匹配规则的时候, 允许设置一些值.
11.1. type:
11.1.1. request: 默认. 类似于request.setAttribute
11.1.2. session: session.setAttribute
11.1.3. response-header: response.setHeader
11.1.4. cookie: 值以”[value][:domain[:lifetime[:path]]]”的格式设置.  是指给客户端浏览器设置cookie, cookie名称由<set />的name属性指定
11.1.4.1. value: cookie的值
11.1.4.2. domain: 服务器
11.1.4.3. lifetime: 存货时间
11.1.4.4. path: cookie的path
11.1.5. status: response.setStatus
11.1.6. content-type: response.setContentType
11.1.7. charset: response.setCharacterEncoding
11.1.8. expires: 设置HTTP头中的过期时间, 设置的格式为{数值类型}, 比如: “1 day 2 seconds”
11.1.9. locale: response.setLocale
11.1.10. parameter: 允许将request.getParameter得到的某个参数的值在这里进行重新处理
11.1.11. method: 允许将request.getMethod()得到的值进行重新处理
11.2. name: type是request, session, response-header, cookie的时候, 必须设置name
11.3. 举例:
<rule>
    <condition name=”user-agent”>Mozilla/3/.0 (compatible; AvantGo .*)</condition>
    <from>.*</from>
    <set name=”client”>AvantGo</set>
</rule>
<rule>
    <condition name=”user-agent”>UP/.Browser/3.*SC03 .*</condition>
    <from>.*</from>
    <set name=”client”>Samsung SCH-6100</set>
</rule>
12. <run />: 允许在<rule />和<condition />都匹配的时候, 执行一个对象方法
12.1. class: 全限定名的类名, 期望调用方法的类名.
12.2. method(可选): 默认值为run.  期望调用的方法名. 该方法必须有两个参数(HttpServletRequest request, HttpServletResponse response).  注意, 如果该对象有init(ServletConfig)或destroy()方法, 在创建和销毁对象的时候会自动调用, ServletConfig中可以得到初始化参数, 参数通过<init-param />的方式传递:
<run class=”selfimpr.MyServlet” method=”doGet”>
    <init-param>
           <param-name>id</param-name>
           <param-value>1</param-value>
    </init-param>
</run>
12.3. neweachtime: 默认false. 表明是否每次请求都创建一个对象实例.
13. Tip
13.1. 在配置中如果要使用”&”, 用&amp;
13.2. 简单起见, 给<from />的配置前面和后面分别加上^, $, 这两个是正则表达式中的强制开始和结尾标志
13.3. 如果使用<outbound-rule>要记得代码中的url都是编码过的
13.4. 正则表达式非常复杂灵活, 请阅读java.util.regex.Pattern中的java正则介绍
13.5. 如果觉得正则难以理解, 可以使用通配符方式
13.6. contex是非常重要的, 如果有一个应用的context是”/myapp”, 并且你的请求是”/myapp/somefolder/somepage.jsp”, 容器交给UrlRewriteFilter的url会是”/somefolder/somepage.jsp”, 这可能难以理解, 但是在你的<rule>和<condition>中不要包含context path, 它是容器负责处理的.
14. 通配符: 
通配符匹配引擎可以替代正则表达式, 在<condition>和<rule>中设置match-type是wildcard用以开启支持通配符.(或者设置default-match-type)
例如:
/big/url/*匹配/big/url/abc.html但是不匹配/big/url/abc/dir/或/big/url/abc/
/big/url/**匹配/big/url/abc.html, /big/url/abc/dir/和/big/url/abc/
也可以和正则的替换一样, 每个*代表一个参数, 在<set>和<to>中用$N的方式使用
#使用mod-rewrite样式的配置
##filter配置
     <filter>
         <filter-name>UrlRewriteFilter</filter-name>
         <filter-class>org.tuckey.web.filters.urlrewrite.UrlRewriteFilter</filter-class>
 
         <!-- defaults to false. use mod_rewrite style configuration file (if this is true and confPath
         is not specified confPath will be set to /WEB-INF/.htaccess) -->
         <init-param>
             <param-name>modRewriteConfText</param-name>
             <param-value><![CDATA[
 
                 # redirect mozilla to another area
                 RewriteCond  %{HTTP_USER_AGENT}  ^Mozilla.*
                 RewriteRule  ^/no-moz-here$                 /homepage.max.html  [L]
 
             ]]></param-value>
         </init-param>
 
     </filter>
 
     <filter-mapping>
         <filter-name>UrlRewriteFilter</filter-name>
         <url-pattern>/*</url-pattern>
         <dispatcher>REQUEST</dispatcher>
         <dispatcher>FORWARD</dispatcher>
     </filter-mapping>
##WEB-INF/.htaccess下的具体匹配配置
# redirect mozilla to another area
     RewriteCond  %{HTTP_USER_AGENT}  ^Mozilla.*
     RewriteRule  ^/no-moz-here$                 /homepage.max.html  [L]
#URL注解匹配
1. urlrewrite3.0之后, 使用JDK1.6及以上可以使用注解来生成urlrewrite的配置文件.
