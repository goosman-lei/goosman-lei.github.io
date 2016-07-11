
author: selfimpr
date: 2010-07-12
email: lgg860911@yahoo.com.cn
blog: http://blog.csdn.net/lgg201
注: 这里会公布所有的测试脚本, 测试脚本有不足指出还请指教学习, 如果有朋友有跑出来新的数据, 烦请发一份给我, 谢谢.
测试脚本共享地址:[http://blog.csdn.net/lgg201/archive/2010/07/17/5742763.aspx](http://blog.csdn.net/lgg201/archive/2010/07/17/5742763.aspx)
 
 
测试的要点主要有:
 
1.      Memcache, TCH, TCB, TCT, Mysql写入数据的性能对比.
2.      Memcache, TCH, TCB, TCT, Mysql(根据主键)读取数据的性能对比.
3.      Mysql, TCT检索数据的性能对比.
4.      由于网上有人说TCH在数据量超过内存后性能会急剧下降, 而TCB性能稳定, 因此, 就这个问题也将单独测试.
 

 
#测试脚本
**all-get-compare.php**: 所有5种存储产品的根据key获取数据的测试, 此测试输入参数num, 表示进行num次测试, 输出为对比折线图以及各种存储产品的平均每条耗时.
**all-set-compare.php**: 所有5种存储产品的数据写入测试, 此测试输入参数有num和length, num代表测试的写入次数, length代表每次写入的数据大小(TCT和Mysql的写入略大于此值), 输出为5种对比折线图及各种存储产品的平均每条耗时. 另外, 此脚本可以接受case参数(值为test_tch, test_tcb, test_tct,
test_memcache, test_mysql之一), num和length意义同上, case参数是为了单独测试某种存储产品的写入耗时, 另一方面, 可以用此脚本插入更为合理的测试数据(比存储产品自身的测试数据写入工具慢).
**autoinsert.php**: all-set-compare.php带case参数情况下的原型.
**basic_funcs.php**: 基础函数, 主要包含图表生成函数, 随机数据生成函数.
**mysql_util.php**: 一个简单的Mysql封装类.
**search-compare.php**: 对搜索的测试, 两个输入参数case和num, 意义同上, 其中case可选值为: test_name, test_sex, test_location, 分别是根据名称, 性别, 省市搜索, 此测试只比较TCT和mysql的性能. 输出为两者搜索性能的对比图和平均每条耗时.
**tct_setIndex.php**: 为TCT中的指定列设置索引.
**tt-compare-get.php**: 此测试用于TCT, TCB, TCH在数据量超出内存后的读取测试, 两个输入参数case和num, case可选值为: test_tch, test_tcb, test_tct. 输出为带内存占用的时间线分析图.
**tt-compare-insert.php**: 此测试用于TCT, TCB, TCH在数据量超出内存后的写入测试, 输入参数与tt-compare-get.php相同. 输出也和tt-compare-get.php相同.
**concurrent_mysql.php**: 此测试用于Mysql的并发测试和一次Mysql操作各部分用时比例测试.
 
 
 

#测试结果
注1: 内存比较图顶端标题有平均每条操作时间
注2: 多种存储产品比较图各自的标题后有平均每条操作时间
注3: 本测试中使用的时间单位一律为e-6秒, 即1/1000000秒
####TCH, TCB, TCT,
Memcache, Mysql性能对比(key-value存取)
写入测试
测试脚本: all-set-compare.php
原始数据: Memcache空, 其余都2亿
bnum: 4亿
输入参数: num=100000&length=10240
![](http://hi.csdn.net/attachment/201007/17/0_1279375962t33t.gif)
图中红色是Mysql, 蓝色是TCB, 黑色是TCH, 黄色是TCT, 绿色是Memcache, 5种产品平均每条数据写入时间为:
Mysql
TCB
TCT
TCH
Memcache
181e-6秒
324e-6秒
300e-6秒
68e-6秒
39e-6秒
从上面的写入性能来看, Memcache最快, 其次TCH, 这两个基本在同一数量级, 而其余三者性能相差比较大.
读取测试(根据key)
测试脚本: all-get-compare.php
原始数据: Memcache空, 其余都2亿
bnum: 4亿
输入参数: num=100000&length=10240
![](http://hi.csdn.net/attachment/201007/17/0_1279375977z6R7.gif)
图中红色是Mysql, 蓝色是TCB, 黑色是TCH, 黄色是TCT, 绿色是Memcache, 5种产品平均每条数据写入时间为:
Mysql
TCB
TCT
TCH
Memcache
90e-6秒
114e-6秒
42e-6秒
34e-6秒
25e-6秒
从上面的读取性能看, 也是Memcache最快, 其次是TCH和TCT, 另外两种则有较大差距.
key-value性能测试结论
根据上面写入和读取的性能测试来看, Memcache, TCH在游戏数据的存储方面备选, 由于在查阅资料的过程中, 发现有观点认为TCH在数据量超出内存后会导致性能下降, 而TCB则性能稳定, 不受此影响, 通过下面内存峰值是TCB和TCH的读取写入性能测试来比较二者.
####TCH, TCB的内存峰值插入测试
TCH写入
测试脚本: tt-compare-insert.php
原始数据: 0
bnum: 400万
输入参数: case=test_tch&num=500000&length=10240
![](http://hi.csdn.net/attachment/201007/17/0_12793760087PWy.gif)
从上图对照可以得到结论: TCH数据库在数据量超出内存后写入性能会有明显的波动(约0.6倍).
TCH读取
测试脚本: tt-compare-get.php
原始数据: 0
bnum: 400万
输入参数: case=test_tch&num=500000(内存满负荷)
![](http://hi.csdn.net/attachment/201007/17/0_1279376024TAr7.gif)
输入参数: case=test_tch&num=500000(内存空闲)
![](http://hi.csdn.net/attachment/201007/17/0_1279376041z2MW.gif)
对比上面两图顶端标题中的平均耗时: 内存满负荷261.4e-6秒, 内存空闲30.05e-6秒, 性能差8倍左右, 因此, 可以得到结论: TCH在内存满负荷后性能会下降8倍左右.
TCH在内存满负荷情况下, 产生如下性能损耗: 写入降低为原来的60%左右, 读取性能降低为原来的12%左右.
TCB写入
测试脚本: tt-compare-insert.php
原始数据: 0
bnum: 400万
输入参数: case=test_tcb&num=500000&length=10240
![](http://hi.csdn.net/attachment/201007/17/0_1279376056cX7V.gif)
从上图可以看出TCB的性能比较稳定, 写入性能与内存消耗几乎无关.
TCB读取
测试脚本: tt-compare-insert.php
原始数据: 0
bnum: 400万
输入参数: case=test_tcb&num=500000(内存满负荷)

![](http://hi.csdn.net/attachment/201007/17/0_1279376065R8Vr.gif)    输入参数: case=test_tcb&num=500000(内存空闲)
![](http://hi.csdn.net/attachment/201007/17/0_1279376108KCBB.gif)


对比上面两图顶端标题中的平均耗时: 内存满负荷197.22e-6秒, 内存空闲32.25e-6秒, 性能差8倍左右, 因此, 可以得到结论: TCB在内存满负荷后性能会下降8倍左右.
TCB在内存满负荷情况下, 产生如下性能损耗: 写入降低为原来的60%左右, 读取性能降低为原来的12%左右.
TCB,
TCH的内存峰值性能测试数据
 
TCH读取
TCH写入
TCB读取
TCB写入
内存满
261e-6
207e-6
197e-6
539e-6
内存空
31e-6
32e-6
由此表格可以看出, TCB在内存满时读取上的平均优势为60e-6秒左右, 而TCB的写入性能约为TCH的50%, 由上面图表分析可以证实网上查阅得到的TCH在数据量超过内存容量后性能下降, 但是, TCB的读取也会有所下降, 究其整体运行的性能来看, TCH优于TCB.
因此, 对于游戏部分的存储, 在目前的三种备选方案TCH, TCB, Memcache中, 又将TCB淘汰出局.
在仅剩的TCH与Memcache中, 但从速度而言, Memcache有无可比拟的优势, 但是, 从另一个角度来看, Memcache作为一个纯粹的缓存产品, 不能够独立的处理数据存储业务, 因此, 如果使用Memcache作为key-value的存储接口, 那么为了数据的持久化及其安全性, 必然要使用某种持久化存储工具去实现.
如果Memcache再套一个持久化存储工具, 那么这中间必然会带来一定的性能损失, 从另一方面来讲, Memcache外加持久化工具, 必然要增加编程实现上的难度, 也就是间接的增大项目风险.
从安全性方面来讲, TT系列的数据库都提供了备份和还原接口, 能够支持完整备份和增量备份, 也能够支持指定时间的恢复, 而Memcache在这一方面也要比TT弱.
####TCT和Mysql的检索测试


测试数据量在第一部分讨论过, 以单表1.6亿为准, 但是, 由于Mysql用来写入测试数据的程序在7000万数据以后变得很慢, 所以, Mysql只插入了7000万数据, TCT使用上面建立的数据库, 2亿条初始数据.
根据名字检索
测试脚本: search-compare.php
原始数据: Mysql7000万, 其余都2亿
bnum: 4亿
输入参数: case=test_name&num=20(由于Mysql这里不使用索引, 性能极低, 所以使用20条检索测试)
![](http://hi.csdn.net/attachment/201007/17/0_1279376152cvfC.gif)
图中蓝色为Mysql, 黑色为TCT, 可以看出, 此时TCT的性能远高于Mysql并且平稳, 此时使用的查询条件为like ‘%%’, Mysql不使用索引. 而TCT的包含查询仍然能够使用文本索引
图中平均每条搜索时间为TCT: 22568e-6秒, Mysql:5103998e-6秒
根据性别检索
测试脚本: search-compare.php
原始数据: Mysql7000万, 其余都2亿
bnum: 4亿
输入参数: case=test_name&num=20000
![](http://hi.csdn.net/attachment/201007/17/0_12793761646c6M.gif)
图中蓝色为Mysql, 黑色为TCT, 此时, 根据性别检索的条件为=’’, Mysql使用索引, 并且由于是=条件, 所以Mysql可以使用常量表优化, TCT使用文本索引, 可以看到, Mysql性能优于TCT.
图中平均检索时间为TCT: 1456e-6, Mysql: 25e-6.
根据省市查询
测试脚本: search-compare.php
原始数据: Mysql7000万, 其余都2亿
bnum: 4亿
输入参数: case=test_location&num=20000
![](http://hi.csdn.net/attachment/201007/17/0_1279376176hhae.gif)
图中Mysql是蓝色线, TCT是黑色线, Mysql性能高于TCT. 此时Mysql是根据情况在province和city两列上选择索引匹配, 并且由于=存在, Mysql可以使用常量表优化. TCT使用province作为主索引, city作为辅助索引检索
图中每条检索平均耗时为TCT: 189e-6秒, Mysql:28e-6秒
所有条件一起检索
测试脚本: search-compare.php
原始数据: Mysql7000万, 其余都2亿
bnum: 4亿
输入参数: case=test_all&num=20000
![](http://hi.csdn.net/attachment/201007/17/0_1279376188asjf.gif)
图中Mysql是蓝色线, TCT是黑色线, Mysql性能高于TCT. 此时Mysql是根据情况在province和city两列上选择索引匹配, 并且由于=存在, Mysql可以使用常量表优化. 与省市检索一样, 这里没有用到sex索引.
图中每条检索平均耗时为TCT: 190e-6秒, Mysql:27e-6秒
关系数据库对比结果
 
TCT
Mysql
索引生效
190e-6
27e-6
索引无效
22568e-6
5103998e-6
         Mysql在索引无法使用时, 表现比TCT差, 但只要能使索引生效, Mysql的性能就会高于TCT, 分析游戏中需要检索排序的数据:
         找人: 需要对玩家的name和uid进行%%方式的检索, 在单独使用这两条检索时, 无法命中索引, 但是, 这种检索的数据量为每玩家一条, 也就是单表50-100万条左右, 根据经验在这个范围内Mysql的全表扫描性能也能满足需求.
         日记, 系统消息等检索: 这些表数据量会比较大, 但是这些表中建立索引都是可以命中的.
         寄售商品: 与日记, 系统消息相似, 索引有效, 并且数据量更小.
         排名: 数据量与找人相同, 但索引有效.
         TCT在这一方面存在的另一个缺陷是只能支持单表, 这实际上就导致了单表数据量增大, 即便对于原本小数据量的表, 在这种情况下, 也会变成大数据量.
#补充1: mysql连接过程耗时测试
10万次操作, 每次操作包括以下操作: 连接(打开连接, 选择数据库, 设置数据库编码), 一条更新10行的update, 一条取前10条的全表扫描检索, 一条insert单条的insert, 关闭连接.
按照上述分块, 操作过程中的平均时间为
![](http://hi.csdn.net/attachment/201007/17/0_1279376267Pno1.gif)


#补充2: Mysql并发连接测试
使用上述测试Mysql操作分块的代码, 利用apache的ab进行并发连接测试. 开启10000并发做10000次请求, 得到以下结果:

 
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
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:12.0pt;
	margin-left:0cm;
	text-indent:17.85pt;
	line-height:200%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-ascii-font-family:Calibri;
	mso-ascii-theme-font:minor-latin;
	mso-fareast-font-family:宋体;
	mso-fareast-theme-font:minor-fareast;
	mso-hansi-font-family:Calibri;
	mso-hansi-theme-font:minor-latin;
	mso-bidi-font-family:"Times New Roman";
	mso-bidi-theme-font:minor-bidi;
	mso-fareast-language:EN-US;
	mso-bidi-language:EN-US;}
p.MsoNoSpacing, li.MsoNoSpacing, div.MsoNoSpacing
	{mso-style-priority:1;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-ascii-font-family:Calibri;
	mso-ascii-theme-font:minor-latin;
	mso-fareast-font-family:宋体;
	mso-fareast-theme-font:minor-fareast;
	mso-hansi-font-family:Calibri;
	mso-hansi-theme-font:minor-latin;
	mso-bidi-font-family:"Times New Roman";
	mso-bidi-theme-font:minor-bidi;
	mso-fareast-language:EN-US;
	mso-bidi-language:EN-US;}
.MsoChpDefault
	{mso-style-type:export-only;
	mso-default-props:yes;
	font-size:11.0pt;
	mso-ansi-font-size:11.0pt;
	mso-bidi-font-family:"Times New Roman";
	mso-bidi-theme-font:minor-bidi;
	mso-font-kerning:0pt;
	mso-fareast-language:EN-US;
	mso-bidi-language:EN-US;}
.MsoPapDefault
	{mso-style-type:export-only;
	margin-bottom:12.0pt;
	text-indent:17.85pt;
	line-height:200%;}
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
一万并发, 一万请求
Server
Software:        nginx/0.7.62
Server
Hostname:        localhost
Server
Port:            80
 
Document
Path:         /end/concurrent_mysql.php?num=1
Document
Length:        193 bytes        文档大小
 
Concurrency
Level:      10000并发连接数
Time
taken for tests:   3.384 seconds       所有请求耗时
Complete
requests:      10000         完成请求数
Failed
requests:        733                 失败请求数
   (Connect: 0, Receive: 0, Length: 733,
Exceptions: 0)
Write
errors:           0
Non-2xx
responses:      9350HTTP响应头非2xx的数量
Total
transferred:      3525248 bytes      总传输量
HTML
transferred:       1900140 bytes  HTML传输量
Requests
per second:    2955.47 [#/sec] (mean)  每秒平均请求数
Time
per request:       3383.552 [ms] (mean)      平均事务时间(毫秒)
Time
per request:       0.338 [ms] (mean,
across all concurrent requests)      平均每条并发请求独立的响应时间(毫秒)
Transfer
rate:          1017.46 [Kbytes/sec]
received         传输速度(接收)
 
Connection
Times (ms)
              min  mean[+/-sd] median   max
Connect:        0 218 706.9     10    3064
Processing:     3  41 100.5     11     973
Waiting:        1  36 100.0      8     971
Total:          6 259 729.2     21    3287
 
Percentage
of the requests served within a certain time (ms)
  50%    21        50%的请求在21毫秒内响应
  66%    22
  75%    26
  80%    47        80%的请求在47毫秒内响应
  90%   559
  95%  3071
  98%  3124
  99%  3136
 100%  3287 (longest request)
 
