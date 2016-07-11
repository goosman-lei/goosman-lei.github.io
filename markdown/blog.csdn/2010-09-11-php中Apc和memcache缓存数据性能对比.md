由于是在项目基础上测试, 不方便贴出测试代码, 见谅.
如果对测试结果不认同, 请提供测试方法及数据, 互相学习.
 
论坛讨论, 再实验, 得出结果:
apc.user_entries_hint这个配置用来调整用户缓存变量数量, 当此值调到足够大后, web环境下apc性能与cli模式下一致. 均远高于memcache.
 
感谢论坛的[maquan](http://hi.csdn.net/maquan).
论坛帖子: http://topic.csdn.net/u/20100911/17/3b328e44-096d-4a09-bcb6-dc48a3052b25.html

 
/**
 * @author: selfimpr
 * @blog: http://blog.csdn.net/lgg201
 * @mail: goosman.lei@gmail.com
 */
 
系统现有APC存储数据量: 70万条左右.

测试数据: key, value都是15字节左右的随机字符串

测试方法:
1. 测试每次运行10组, 每组10000次读/写, 使用jpgraph生成折线图.
2. 读/写分别测试, 不会互相影响.
 
使用接口:

```php
//Memcache接口
$mem = new Memcache();
$mem-&gt;connect('localhost', 11211);
$mem-&gt;get();
$mem-&gt;set();
//Apc接口
apc_store();
apc_fetch();
```

 
结论:
1. 在nginx+fastcgi的web环境下, apc随着数据量增大, 性能下降明显, 在超过8万条后, 性能低于Memcache
2. 在CLI模式下运行, apc性能稳定, 60万条数据一直远超memcache
 
测试结果: (横轴为测试组, 每组1万条, 纵轴为1万条耗时, 黑色线为memcache, 蓝色线为apc)
 
apc和memcache初始数据: 0(万条), 读取性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195605gPvW.gif)
apc和memcache初始数据: 10(万条), 读取性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195611xkpL.gif)
apc和memcache初始数据: 20(万条), 读取性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195615igo4.gif)
apc和memcache初始数据: 30(万条), 读取性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195621wa87.gif)
apc和memcache初始数据: 40(万条), 读取性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195625Etah.gif)
apc和memcache初始数据: 50(万条), 读取性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195635QBpA.gif)
apc和memcache初始数据: 60(万条), 读取性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195646mkbW.gif)
apc和memcache初始数据: 0(万条), 写入性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195651u5Ce.gif)
apc和memcache初始数据: 10(万条), 写入性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195655Qpiq.gif)
apc和memcache初始数据: 20(万条), 写入性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195659JbgJ.gif)
apc和memcache初始数据: 30(万条), 写入性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195664jljI.gif)
apc和memcache初始数据: 40(万条), 写入性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195667ixXj.gif)
apc和memcache初始数据: 50(万条), 写入性能
![](http://hi.csdn.net/attachment/201009/11/0_1284195670UBB8.gif)
apc和memcache初始数据: 60(万条), 写入性能
![](http://hi.csdn.net/attachment/201009/11/0_12841956746AmS.gif)
