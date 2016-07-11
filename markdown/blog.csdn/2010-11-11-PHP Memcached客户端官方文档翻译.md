呵呵， 项目要换memcache客户端了，今天看了看Memcached文档，顺便简单翻译了。。不足之处请指教
translator：selfimpr
blog: http://blog.csdn.net/lgg201
mail: lgg860911@yahoo.com.cn
 

```php
介绍
memcached是一个高性能分布式内存对象缓存系统, 通常用于在动态web应用上减缓数据库加载压力以提高速度.
这个扩展使用libmemcached库提供的api和memcached服务端进行交互, 它也同样提供了一个session处理器(memcached)
关于libmemcached的信息参见http://tangent.org/552/libmemcached.html
失效时间
一些存储命令控制发送一个过期值(与一个存储项或一个操作请求的客户端相关)到服务端. 这个值可以是一个Unix时间戳(自1970年1月1日起的秒数), 或者从现在起的时间差. 对于后一种情况, 时间差秒数不能超过60 * 60 * 24 * 30(30天的秒数), 如果过期时间超过这个值, 服务端会将其作为Unix时间戳与现在时间进行比较.
如果过期值为0(默认), 此项永不过期(但是它可能会因为为了给其他项分配空间而被删除)
返回回调
返回回调在Memcached::getDelayed或Memcached::getDelayedByKey方法获取到每个元素时被调用. 回调函数中可以接受到Memcached对象和数组结构的元素信息, 并且回调函数不会返回任何值.
READ-THROUGH缓存回调
Read-through缓存回调在元素没有从服务端检索到时被调用. 回调接受Memcached对象, 请求key, 值变量的引用等三个参数, 回调函数可以返回true或false来响应值的设置. 如果回调函数返回true, memcached将会把$value值保存到服务端兵器返回到原来的调用函数中. 
仅仅Memcached::get和Memcached::getByKey支持这些回调, 因为memcache协议在请求多个key时不提供哪个key未找到的信息.
API
Memcached::add(string $key, mixed $value[, int $expiration])
在新的key西面添加一个元素, 如果key已经存在则失败. 成功返回TRUE, 失败返回FALSE. 如果key已经存在Memcached::getResultCode将返回Memcached::RES_NOTSTORED
Memcached::addByKey(string $server_key, string $key, mixed $value[, int $expiration])
向指定服务器指定key增加一个元素, 与add的区别在于此方法可以指定服务器, 这个在需要将一些相关key存放到一台服务器时很有用.
Memcached::addServer(string $host, int $port[, int $weight])
向服务器池中添加一台服务器. 在这个时机是不会建立连接的, 但是, 如果使用consitent key分布方式, 一些内部的数据结构将会被更新, 不过, 如果你需要添加多台服务器, 最好使用Memcached::addServers, 这种更新就只会发生一次.
相同的服务器可以多次出现在服务器池中, 因为这里没有重复检测. 当然, 这是不好的方式, 我们可以用weight选项来提供服务器被选中的权重.
Memcached::addServers(array $servers)
向服务器池中增加多台服务器. 每台服务器信息以一个数组方式提供(主机名, 端口以及可选的权重), 此时不会建立连接.
相同的服务器可以多次出现在服务器池中, 因为这里没有重复检测. 当然, 这是不好的方式, 我们可以用weight选项来提供服务器被选中的权重.
Memcached::append(string $key, string $value)
将一个字符串值追加到已有的元素后面
Memcached::appendByKey(string $server_key, string $key, string $value)
等同于append, 增加了指定服务器的选项
Memcached::cas(double $cas_token, string $key, mixed $value[, int expiration])
此函数执行一个&rdquo;检查设置&rdquo;的操作, 因此, 元素仅仅会在从当前客户端到现在其他客户端没有对其更新的情况下才会被写入. 这个检查是通过memcache指定给已有元素的一个唯一的64位值cas_token参数来实现的.
关于怎么获取到这个标记(cas_token)参见Memcached::get*方法的文档.
Memcached::casByKey(double $cas_token, string $server_key, string $key, mixed $value[, int $expiration])
等同于cas方法, 不过可以指定服务器.
Memcached::__construct([string $persistent_id])
构造器, 创建一个代表memcache服务连接的实例. 
默认情况下Memcached实例会在请求结束后被销毁, 如果要在多个请求之间共享实例, 使用persistent_id参数指定一个唯一的id, 所有通过相同persistent_id创建的实例将会共享连接.
Memcached::decrement(string $key[, $int $offset])
将一个数字元素的值减小offset, 如果元素值不是数值, 将其作为0处理. 如果操作会将此值改变为小于0的值, 则使此值为0. 如果key不存在, 此方法执行失败.
offset默认1
Memcached::delete(string $key[, int $time])
从服务器删除key, time参数指客户端期望的服务器拒绝这个key的add和replace命令的总秒数. 在这段时间里, 元素被加入到一个删除序列, 也就是说它不能用get命令检索到, 这个key的add和replace命令也会失败(不过set命令会成功), 在这个时间过去后, 元素最终被从服务器内存删除. time参数默认是0(也就是说元素立即被删除并且后续的关于此key的存储命令会成功).
Memcached::deleteByKey(string $server_key, string $key[, int $time])
等同于delete, 不过可以指定服务器
Memcached::fetch(void)
返回最后一次请求的下一个值
Memcached::fetchAll(void)
一次检索出最后一次请求的所有值
Memcached::flush([int $delay])
使所有已经存在于缓存中的元素立即(默认)或在delay延迟之后失效. 失效后的元素不会再被检索到(除非在flush之后又进行了存储), flush不会整整的释放所有的已存在元素的内存, 而是可以在这些内存上重新存放新的元素.
Memcached::get(string $key[, callback $cache_cb[, double &amp;$cas_token]])
返回事先存储在key下的元素, 如果元素查找到并且提供了cas_token参数, cas_token将会被设置为此元素的CAS标记. CAS标记的使用参见Memcached::cas方法. Read-through缓存回调通过cache_cb参数指定
Memcached::getByKey(string $server_key, string $key[, callback $cache_cb[, double &amp;$cas_token]])
等同于get, 不过可以通过server_key指定服务器
Memcached::getDelayed(array $keys[, bool $with_cas[, callback $value_cb]])
用于向memcache请求获取keys数组指定的多个key对应的元素. 这个方法不会等待服务端返回, 当你需要读取收集元素的时候, 调用Memcached::fetch或Memcached::fetchAll. 如果with_cas设置为true, CAS标记也会被请求.
作为抓取结果的替代方案, 可以指定value_cb参数作为返回回调来处理返回结果
Memcached::getDelayedByKey(string $server_key, array $keys[, bool $with_cas[, callback $value_cb]])
等同于getDelayed, 提供了server_key用于指定服务器
Memcached::getMulti(array $keys[, array &amp;$cas_tokens[, integer $flags]])
与get类似, 但是这个方法用于检索keys数组指定的多个key对应的元素. 如果cas_tokens变量提供了, 将会被填充为被发现的元素的CAS标记.
注意: 此方法不能指定Read-through缓存回调, 因为memcache协议不能提供多个key请求时未发现的key的信息
flags参数用于指定附加选项, 目前仅支持Memcached::GET_PRESERVE_ORDER以保证返回与请求的key顺序一致.
Memcached::getMultiByKey(string $server_key, array $keys[, string &amp;$cas_tokens[, integer $flags]])
等同于getMulti, 不过可以通过server_key指定服务器
Memcached::getOption(int $option)
返回Memcached选项值, 一些选项是libmemcached定义的, 另外一些特殊的是Memcached扩展特有的. 更详细信息参见Memcached常量
Memcached::getResultCode(void)
返回Memcached::RES_*常量中的一个来表明Memcached最后一个方法的执行结果
Memcached::getResultMessage(void)
返回Memcached最后一个执行的方法的执行结果的字符串描述
Memcached::getServerByKey(string $server_key)
返回指定server_key对应的服务器信息, server_key等同所有Memcached::*ByKey方法中的server_key参数
Memcached::getServerList(void)
返回服务器池中所有服务器列表
Memcached::getStats(void)
返回一个数组, 包含所有当前可用memcache服务器状态信息. 关于这些统计信息的详细规范参见memcache 协议
Name              Type     Meaning
----------------------------------
pid               32u      Process id of this server process服务端进程号
uptime            32u      Number of seconds this server has been running服务器运行时间
time              32u      current UNIX time according to the server当前服务器时间戳
version           string   Version string of this server服务器版本信息
pointer_size      32       Default size of pointers on the host OS
                           (generally 32 or 64)主机系统的位数(通常是32或64)
rusage_user       32u:32u  Accumulated user time for this process 
                           (seconds:microseconds)此进程累积使用的user CPU时间(秒:微秒)
rusage_system     32u:32u  Accumulated system time for this process 
                           (seconds:microseconds)此进程累积使用的system CPU时间(秒:微秒)
curr_items        32u      Current number of items stored by the server服务器当前存储的元素数量
total_items       32u      Total number of items stored by this server 
                           ever since it started自服务开启曾经存储过的元素数量
bytes             64u      Current number of bytes used by this server 
                           to store items当前用于元素存储使用的内存容量
curr_connections  32u      Number of open connections当前打开连接数
total_connections 32u      Total number of connections opened since 
                           the server started running自服务开启总共打开过的连接数
connection_structures 32u  Number of connection structures allocated                            by the server分配给服务器的连接结构数
cmd_get           64u      Cumulative number of retrieval requests累积检索请求数量
cmd_set           64u      Cumulative number of storage requests累积存储请求数量
get_hits          64u      Number of keys that have been requested and 
                           found present缓存命中的数量
get_misses        64u      Number of items that have been requested 
                           and not found缓存未命中数量
evictions         64u      Number of valid items removed from cache                                                                           
                           to free memory for new items为给新元素分配空间而移除的有效元素数量                                                                                       
bytes_read        64u      Total number of bytes read by this server 
                           from network通过网络从此服务器读取的总字节数
bytes_written     64u      Total number of bytes sent by this server to 
                           network通过网络向此服务器发送的总字节数
limit_maxbytes    32u      Number of bytes this server is allowed to
                           use for storage. 此服务器允许存储的字节数
threads           32u      Number of worker threads requested. 工作线程数
                           (see doc/threads.txt)
Memcached::getVersion(void)
返回一个包含所有可用服务器的版本信息的数组
Memcached::increment(string $key[, int $offset])
以offset作为差值增加指定key对应数值元素的值, 如果元素不是数值, 以0处理. 如果key不存在, 此方法失败.
offset默认1
Memcached::prepend(string $key, string $value)
append的反向追加
Memcached::prependByKey(string $server_key, string $key, string $value)
等同于prepend, 可以通过server_key指定服务器
Memcached::replace(string $key, mixed $value[, int $expiration])
与set类似, 但是这个方法在key不存在的时候会失败
Memcached::replaceByKey(string $server_key, string $key, mixed $value[, int $expiration])
等同于replace, 只是多出一个server_key用来指定服务器
Memcached::set(string $key, mixed $value[, int $expiration])
将值存储到memcache服务器的指定key下. expiration参数可以用于控制值的失效.
value可以是除了资源类型之外的任意PHP类型, 因为资源类型被序列化后不能重现, 如果Memcached::OPT_COMPRESSION选项被开启, 序列化值将被首先压缩
Memcached::setByKey(string $server_key, string $key, mixed $value[, int $expiration])
等同于set, 可以通过server_key指定服务器
Memcached::setMulti(array $items[, int $expiration])
与set类似, 用于设置多个key-value到缓存中, expiration指定所有的元素的失效时间
Memcached::setMultiByKey(string $server_key, array $items[, int $expiration])
等同于setMulti, 不过可以通过server_key指定服务器
Memcached::setOption(int option, mixed $value)
用于设置Memcached选项, 其中Memcached::OPT_HASH需要设置为Memcached::HASH_*系列常量, Memcached::OPT_DISTRIBUTION需要设置为Memcached::DISTRIBUTON_*系列常量

```

