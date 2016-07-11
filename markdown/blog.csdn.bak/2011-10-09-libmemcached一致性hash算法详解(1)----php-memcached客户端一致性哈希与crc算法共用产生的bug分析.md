---
layout: post
title: libmemcached一致性hash算法详解(1)----php-memcached客户端一致性哈希与crc算法共用产生的bug分析
date: 2011-10-09 17:09:00
categories: [算法, memcached, 服务器, php, 测试, struct]
tags: []
---
author: selfimpr
blog: http://blog.csdn.net/lgg201
mail: goosman.lei@gmail.com

事情的起源, 是同事使用下面的代码, 得到了一个诡异的结果, 而且是稳定的产生我们不期望的结果.


```php
<?php
$mem = new Memcached;
$mem->addServers(array(array('10.8.8.32',11300,100),array('10.8.8.32',11301,0)));
$mem->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
$mem->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);
for ($i=0;$i<10;$i++){
    $key = "item_$i";
    $arr = $mem->getServerByKey($key);
    echo ($key.":\t".$arr['port']."\n");
}
print_r($mem->getServerList());
```
代码很简单, 就是创建了一个php的Memcached客户端, 增加了两台服务器, 设置了分布式算法为一致性哈希, Hash算法为CRC.
运行了很多次这个测试用例, 产生的输入都如下:


```php
item_1:	11301
item_2:	11301
item_3:	11301
item_4:	11301
item_5:	11301
item_6:	11301
item_7:	11301
item_8:	11301
item_9:	11301
Array
(
    [0] => Array
        (
            [host] => 10.8.8.32
            [port] => 11300
            [weight] => 100
        )

    [1] => Array
        (
            [host] => 10.8.8.32
            [port] => 11301
            [weight] => 0
        )

)
```
从上面的输出中我们可以看出, 两台服务器都是OK的, 但是, 只能所有的key都被分布到了一台服务器上.
后来, 我尝试对测试用例做了以下修改:
1. 将Hash算法换做其他支持的算法
2. 将分布式算法换成普通的算法
我做的以上尝试, 输出的结果都是我们期望的, key被分布到不同的服务器上.

然后就是痛苦的问题跟踪, 最终发现问题出在php的memcached客户端对libmemcached的实现上
在libmemcached中, 用来代表一组服务器(针对同一个客户端服务)的结构(libmemcached/memcached.h中定义)是: struct memcached_st {};下面摘取其中的部分定义:


```cpp
struct memcached_st {
  uint8_t purging;
  bool is_allocated;
  uint8_t distribution;
  uint8_t hash;
...
  memcached_hash hash_continuum;
...
};
```
请记住hash和hash_continuum这两个字段.
然后我们看一个函数:
libmemcached/memcached_hash.c中的memcached_generate_hash函数, 进入这个函数的流程如下:
"php-memcached扩展php_memcached.c中getServeredByKey函数"  调用  "libmemcached的libmemcached/memcache_server.c中的memcached_server_by_key函数", 在其中又调用了 "libmemcached/memcached_hash.c中的memcached_generate_hash函数"
在这个函数中做了3件比较重要的事:
1. 生成要寻找的key的hash值
2. 如果需要, 更新服务器的一致性hash点集
3. 将key分布到服务器上

我们分别来看这3件事:
1. 生成key的hash值:
继续跟踪代码, 我们发现在generate_hash函数中有如下一句代码:
hash= memcached_generate_hash_value(key, key_length, ptr->hash);
查看memcached_generate_hash_value函数源代, 我们得知该函数是使用第3个参数指定的hash算法, 产生key的hash值, 这里使用的是ptr->hash
注: ptr就是前面提到的memcached_st结构
2. 更新服务器的一致性hash点集
这里, 我们需要说的是, 哪怕不需要, 在我们测试代码中的addServer调用时, 也会执行这个函数, 所以, 我们需要关注其中所做的事情
我们跟踪到update_continuum函数中, 分析源代码, 总结这个函数所做的事情, 用php代码描述如下:


```php
<?php
$servers	= array(
	array('10.8.8.32', 11301), 
	array('10.8.8.32', 11300), 
);
$points		= array();
$index		= 0;
foreach ( $servers as $server ) {
	$i	= 0;
	while ( $i ++ < 100 ) { //libmemcached中100是两个常量求得的值
		$points[]	= array(
			'index'	=> $index, 
			'value'	=> hash_value, 
		);
	}
	$index ++;
}
//这里再对$servers按照元素的'value'排序

```
也就是: 以$host:$port-$i作为key产生100个hash值, 所有服务器产生的这些hash值再排序
这里在libmemcached的update_continuum中, 我们需要找到下面这句代码:


```cpp
value= memcached_generate_hash_value(sort_host, sort_host_length, ptr->hash_continuum);
```
也就是求每个点的hash值, 可以看到, 这里用了ptr中的hash_continuum字段给定的hash算法计算.

3. 将key分布到服务器上
这里的分布过程, 其实就是对上面产生的点集进行一个二分查找, 找到离key的hash值最近的点, 以其对应服务器作为key的服务器, 用php代码描述如下:


```php
<?php
$points	= array();	//之前取到的服务器产生的点集
$hash	= 1;		//要查找的key的hash值
$begin = $left = 0;
$end = $right = floor(count($points) / 2);
while ( $left < $right ) {
	$middle	= $left + floor(($left + $right) / 2);
	if ( $points[$middle]['value'] < $hash ) $left = $middle + 1;
	else $right = $middle;
}
//数组越界检查
if ( $right = $end ) $right = $begin;
//这里就得到了key分布到的服务器是所有服务器中的第$index个
$index	= $servers[$right]['index'];
```

主要的过程分析完了, 对造成这个问题的关键点用红字标识了出来, 我们可以看到, 对key和对服务器求hash值的算法在memcached_st结构中是由不同的字段指定的.
那么, 问题就明了了, 我们取看看php-memcached中的setOption方法的实现, 它只是修改了ptr->hash字段.
因此, 测试用例的运行情况是:
对key使用crc算法求hash
对服务器点集使用默认算法求hash值

经过对两种算法比较, 默认的算法产生的hash数值都比较大, 而crc产生的hash值最大就是几万的样子(具体上限没有计算)
所以, 原因就找到了, 服务器点集的hash值都大于key产生的hash值, 所以查找时永远都落在点集的第一个点上.

至此, 问题的原因已经查明, 解决方法也就有了: 修改php的memcached扩展, 在setOption中增加修改ptr->hash_continuum字段的操作, 然后测试用例做响应修改即可.

下一篇文章将展示的是一个从libmemcached源代码提取出来的简化版一致性hash算法, 简单明了, 可以很容易说明libmemcached的一致性hash算法实现
[libmemcached一致性hash算法详解(2)----简化版的libmemcached一致性hash算法实现](http://blog.csdn.net/lgg201/article/details/6856387)
