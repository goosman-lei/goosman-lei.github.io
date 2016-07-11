---
layout: post
title: TC官方文档翻译12----编码API(Tokyo Cabinet/Tokyo Tyarnt 文档系列)
date: 2010-06-28 00:29:00
categories: [文档, api, 算法, url, xml, scheme]
tags: []
---
/**
 * 转载请注明出处, 由于个人技术能力有限, 英语水平欠缺,
 * 有翻译不合适或错误的地方, 请纠正,
 * 希望不要因为我的错误误导您, 希望您的智慧可以加入.
 * @translator: selfimpr
 * @mail: goosman.lei@gmail.com
 * @blog: http://blog.csdn.net/lgg201
 */
 
**由于能力有限, 自己对TC的文件数据库部分尚未理解, 所以暂时到此为止.**
 
 
<!--
 /* Font Definitions */
 @font-face
	{font-family:宋体;
	panose-1:2 1 6 0 3 1 1 1 1 1;
	mso-font-alt:SimSun;
	mso-font-charset:134;
	mso-generic-font-family:auto;
	mso-font-pitch:variable;
	mso-font-signature:3 135135232 16 0 262145 0;}
@font-face
	{font-family:"Cambria Math";
	panose-1:2 4 5 3 5 4 6 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:roman;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1107304683 0 0 159 0;}
@font-face
	{font-family:Calibri;
	panose-1:2 15 5 2 2 2 4 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:swiss;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1073750139 0 0 159 0;}
@font-face
	{font-family:"/@宋体";
	panose-1:2 1 6 0 3 1 1 1 1 1;
	mso-font-charset:134;
	mso-generic-font-family:auto;
	mso-font-pitch:variable;
	mso-font-signature:3 135135232 16 0 262145 0;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	text-align:justify;
	text-justify:inter-ideograph;
	mso-pagination:none;
	font-size:10.5pt;
	mso-bidi-font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-fareast-font-family:宋体;
	mso-bidi-font-family:"Times New Roman";
	mso-font-kerning:1.0pt;}
p.MsoIntenseQuote, li.MsoIntenseQuote, div.MsoIntenseQuote
	{mso-style-priority:30;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-link:"明显引用 Char";
	mso-style-next:正文;
	margin-top:10.0pt;
	margin-right:46.8pt;
	margin-bottom:14.0pt;
	margin-left:46.8pt;
	text-align:justify;
	text-justify:inter-ideograph;
	mso-pagination:none;
	border:none;
	mso-border-bottom-alt:solid #4F81BD .5pt;
	padding:0cm;
	mso-padding-alt:0cm 0cm 4.0pt 0cm;
	font-size:10.5pt;
	mso-bidi-font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-fareast-font-family:宋体;
	mso-bidi-font-family:"Times New Roman";
	color:#4F81BD;
	mso-font-kerning:1.0pt;
	font-weight:bold;
	font-style:italic;}
span.Char
	{mso-style-name:"明显引用 Char";
	mso-style-priority:30;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:明显引用;
	mso-ansi-font-size:10.5pt;
	mso-bidi-font-size:11.0pt;
	color:#4F81BD;
	mso-font-kerning:1.0pt;
	font-weight:bold;
	font-style:italic;}
.MsoChpDefault
	{mso-style-type:export-only;
	mso-default-props:yes;
	font-size:10.0pt;
	mso-ansi-font-size:10.0pt;
	mso-bidi-font-size:10.0pt;
	mso-ascii-font-family:Calibri;
	mso-fareast-font-family:宋体;
	mso-hansi-font-family:Calibri;
	mso-font-kerning:0pt;}
 /* Page Definitions */
 @page
	{mso-page-border-surround-header:no;
	mso-page-border-surround-footer:no;}
@page WordSection1
	{size:612.0pt 792.0pt;
	margin:72.0pt 90.0pt 72.0pt 90.0pt;
	mso-header-margin:36.0pt;
	mso-footer-margin:36.0pt;
	mso-paper-source:0;}
div.WordSection1
	{page:WordSection1;}
-->
编码API
char *tcurlencode(const char *ptr, int
size);
         使用URL编码规则编码一个序列化对象.
char *tcurldecode(const char *str, int
*sp);
         使用URL编码规则解码str, sp记录解码后返回值长度
TCMAP *tcurlbreak(const char *str);
         把个顶的url字符串str按照URL规则分裂成为一个TCMAP对象, 返回的TCMAP对象中有以下key:
         self:
URL自身
         scheme:协议, 支持HTTP, HTTPS, FTP, FILE等
         host:主机名或IP
         port:主机端口号
         authority:站点信息(不是很清楚, 有懂的朋友麻烦邮件goosman.lei@gmail.com,共同学习, 谢谢)
         path:资源路径
         file:访问文件名(无目录)
         query:查询字符串
         fragment:
url最后的#后面的东西, 通常是访问锚点. BOM中的window.location.hash
char *tcurlresolve(const char *base, const
char *target);
         用一个绝对路径的URL解析一个相对路径的URL, 如果target是相对的, 返回的是相对base的域的URL, 否则返回target的拷贝.
char *tcbaseencode(const char *ptr, int
size);
         base64编码一个序列化对象
char *tcbasedecode(const char *str, int
*sp);
         base64编码解码
char *tcquoteencode(const char *ptr, int
size);
         Quoted-printable编码一个序列化对象
char *tcquotedecode(const char *str, int
*sp);
         Quoted-printable编码解码
char *tcmimeencode(const char *str, const
char *encname, bool base);
         MIME编码, encname指定编码名字, base指定是否使用base64进行编码, 如果是false使用Quoted-printable编码
char *tcmimedecode(const char *str, char
*enp);
         MIME解码, 解码后, enp将会记录tcmimeencode中的encname类型名
char *tcmimebreak(const char *ptr, int
size, TCMAP *headers, int *sp);
         把ptr给定的MIME内容拆分成head和body, body作为函数的内容返回, sp将记录body的大小, 对于拆分得到的头, 组装成TCMAP记录到headers中, headers中有如下key
         TYPE:
Content-Type
         CHARSET:
Content-Transfer-Encoding
         BOUNDARY:
Content-Type标头的边界参数值
         DISPOSITION:
MIME协议的Content-Disposition标头
         FILENAME:文件名
         NAME:属性名
TCLIST *tcmimeparts(const char *ptr, int
size, const char *boundary);
         根据boundary分割ptr指定的MIME数据为一个TCLIST
char *tchexencode(const char *ptr, int
size);
         把一个序列化对象编码成16进制, 内部是没读取一个字节, 用sprintf输入到新的字符串中, 最终返回新字符串
char *tcpackencode(const char *ptr, int
size, int *sp);
         使用Packbits算法压缩序列化对象, sp记录压缩后大小
char *tcpackdecode(const char *ptr, int
size, int *sp);
         使用Packbits算法解压缩
char *tcbsencode(const char *ptr, int size,
int *sp);
         使用TCBS算法压缩序列化对象, sp记录压缩后大小
char *tcbsendecode(const char *ptr, int
size, int *sp);
         使用TCBS算法解压
char *tcdeflate(const char *ptr, int size,
int *sp);
         使用Deflate算法压缩序列化对象, sp记录压缩后大小
char *tcinflate(const char *ptr, int size,
int *sp);
         使用Deflate算法解压缩
char *tcgzipencode(const char *ptr, int
size, int *sp);
         gzip压缩
char *tcgzipdecode(const char *ptr, int
size, int *sp);
         gzip解压缩
unsigned int tcgetcrc(const char *ptr, int
size);
         获取CRC32校验和
char *tcbzipencode(const char *ptr, int
size, int *sp);
         使用BZIP2压缩
char *tcbzipdecode(const char *ptr, int
size, int *sp);
         BZIP2解压缩
char *tcberencode(const unsigned int *ary,
int anum, int *sp);
         使用BER编码一个无符号整数数组
unsigned int *tcberdecode(const char *ptr,
int size, int *np);
         从一个字符串利用BER算法解码出一个无符号整数数组, np记录数组大小
char *tcxmlescape(const char *str);
         用来将给定的xml字符串str中的特殊字符(xml中定义的)进行转义, 该函数只会转义’&’, ‘<’, ‘>’,
‘”’等4个字符
char *tcxmlunescape(const char *str);
         将给定的xml字符串str中的实体字符(xml中定义的)还原成原始字符, 该函数只会转义’&amp;’, ‘&lt’,
‘&gt;’, ‘quot;’等
