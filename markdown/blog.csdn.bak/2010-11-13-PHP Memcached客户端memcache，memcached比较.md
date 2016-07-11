---
layout: post
title: PHP Memcached客户端memcache，memcached比较
date: 2010-11-13 19:34:00
categories: [memcached, php, session, function, 文档, 扩展]
tags: []
---
author: selfimpr
blog: http://blog.csdn.net/lgg201
mail: goosman.lei@gmail.com
 
1. 系统级锁定支持:
memcache客户端不支持锁相关的功能，而服务端又支持并发，这样其实就会带来数据混乱的问题，我们之前的做法是实现一个应用层的锁：

```c-sharp
&lt;?php
/**
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 */
$m = new Memcache();
$m-&gt;addServer(......);
//锁定
function lock($m, $key, $timeout = 5000, $wait = 15) {
  while($i ++ &lt;= $wait &amp;&amp; false !== $m-&gt;get('lock_'.$key));
  return false === $m-&gt;get('lock_'.$key) &amp;&amp; ($m-&gt;set('lock_'.$key, $timeout));
}
//解锁
function unlock($m, $key) {
  return $m-&gt;delete('lock_'.$key);
}
?&gt;
```

上面的方式构建锁定与解锁方法，然后在需要锁定的地方，get前加锁，set后解锁。
 
上面的解决方法，会降低并发导致的数据混乱问题，但不能根治，因为我们在lock的时候可能会是并发的，同时拿到了锁。。
后来，考虑过应用层实现的乐观锁机制，不过没有应用起来，也就不了了之了。。
 
最近换了Memcached客户端，发现其中支持cas这个方法，其实就是一个系统层的乐观锁。

```php
&lt;?php
$m = new Memcached();
$m-&gt;addServer(....);
$value = $m-&gt;get($key, $cas);
//业务操作
$m-&gt;cas($cas, $key, $value);
?&gt;
```

get的时候，传一个$cas参数，Memcached::get这个方法在定义的时候，$cas是一个引用参数，执行完后，$cas这个名字已经被修改为memcached服务端返回的一个唯一标识了。
当我们set这个key的时候，带上这个cas值，以cas接口设置过去，服务端就能根据cas值判断该key对应的值是否被修改过，如果被修改过，说明是脏数据，向客户端发送错误消息。
由于这个锁定的验证机制是memcached的服务端提供的，因此，我们可以相信它的正确性。
 
 
2. 获取Memcached中的所有的key
这个在memcache扩展中支持，Memcached扩展中反而不支持。
使用的方法是Memcache::getExtendedStats(....)，三个参数，这个这里就不写了，php-memcache文档翻译的时候，我会加进去。
顺便提下，有精力有时间的朋友请参与支持：http://code.google.com/p/phpdoc-zh/，PHP官方文档翻译，公益项目
 
3. 持久化连接问题
Memcache客户端是支持持久化连接的，而Memcached客户端不支持持久化连接，并且Memcached客户端在释放连接的时候本身可能有bug，在高并发的情况下会导致Memcached服务端大量的连接处于time_wait状态无法释放。。这样就会导致一部分请求失败。。
我们使用的是memcache作为session的handler，因为这个问题导致我们的session无法取到，客户端丢失连接，问题是比较严重的。
 
最终从网上找到了解决方案就是修改Memcached服务端的socket释放相关的配置，我们进行的是如下修改：
修改文件：/etc/sysctl.cnf，加入或修改下面两个配置
net.ipv4.tcp_tw_recycle=1 //回收time_wait状态的socket
net.ipv4.tcp_fin_timeout=3 //认为的超时时间
然后/sbin/sysctl -p把这个文件reload一下
 
这个配置的修改，在网上有一位朋友使用apache的ab做过并发测试
1000000请求30000并发，没有出现Memcached的连接失败问题。
 
呵呵，这几天了解到的就这么多，有不足之处或错误的地方，还望指正。
谢谢
