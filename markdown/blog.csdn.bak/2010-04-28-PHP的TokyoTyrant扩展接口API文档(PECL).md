---
layout: post
title: PHP的TokyoTyrant扩展接口API文档(PECL)
date: 2010-04-28 09:18:00
categories: [php, 扩展, 文档, api, string, 数据库]
tags: []
---
手册的官方地址: http://us3.php.net/manual/en/book.tokyo-tyrant.php
 
具体内容如下:
 

```php
TCT支持的追加参数:
mode: 
bnum: 桶数组元素个数,如果不大于0,使用默认值, 默认值是131071, 建议值是所有存储的记录条数的0.5-4倍.
apow: 和一个key关联的记录数,如果是负值, 使用默认值, 默认值为4, 意为2的4次方.
fpow: FreeBlockPool是一块在bucket后的内存空间, 每个元素指向一个内存空洞, 包括了空洞的位置和尺寸信息, fpow指定了这块空间元素数量的最大值.
opts: 通过位或运算指定以下选项:
    TDBTLARGE: 数据库大小是否可以在64位时大于2GB.
    TDBTDEFLATE: 指定记录通过Deflate压缩.
    TDBTBZIP: 指定记录通过BZIP2压缩.
    TDBTTCBS: 指定记录通过TCBS压缩.
rcnum: 指定最大缓存的记录数, 如果不大于0, 缓存失效, 没有默认值.
lcnum: 指定最大缓存的叶子节点数量, 默认值4096.
ncnum: 指定最大缓存的非叶子节点数量, 默认值512.
xmsiz: 指定映射的额外内存大小, 默认67108864.
dfunit: 指定内存碎片整理的单位步数.
idx: 指定表的索引.
###################################################
###################################################
TokyoTyrant_PHP支持链式操作,类似jQuery的方式, 可以$conn-&gt;put()-&gt;add()-&gt;list()的方式一直调用.
##################################################
TokyoTyrant类中的接口
##################################################
TokyoTyant::add(string $key, number $increment[, int $type = 0]):
增加int或double值，返回值是增长之后该key对应的新值,如果key不存在则创建一个新的并以increment参数作为初始值, type取值为TokyoTyrant::RDBREC_INT或TokyoTyrant::RDBREC_DBL, 分别代表将increment参数的值作为int, double处理.
TokyoTyrant::connect(string $host, [, int $port = TokyoTyrant::RDBDEF_PORT[, array $options]]:
连接远程数据库.
$options可以包括timeout(超时时间, 默认5.0), reconnect(默认True), persistent(默认True)
返回当前连接对象, 如果失败抛出TokyoTyrantException
TokyoTyrant::connectUri(string $uri):
通过Uri连接到数据库.
uri: tcp://localhost:1978/
返回当前连接对象或在失败时抛出TokyoTyrantException.
TokyoTyrant::_construct([string $host[, int $port = TokyoTyrant::RDBDEF_PORT[, array $options]]]):
构建一个新的TokyoTyrant对象.参数与connect意义相同.
连接数据库失败时抛出TokyoTyrantException.
TokyoTyrant::copy(string $path): 
创建一个当前数据库的拷贝. path参数指定要拷贝到的路径, 用户必须要有文件的写权限.
TokyoTyrant::ext(string $name, int $options, string $key, string $value):
执行一个远程脚本扩展.指的就是启动ttserver时通过-ext指定的lua脚本文件中定义的函数.
name: 要执行的函数名称.
options: TokyoTyrant::RDBXO_LCKREC用于记录锁定, TokyoTyrant::RDBXO_LCKGLB用于全局锁定.
key: 要传递给函数的key.
value: 要传递给函数的value.
返回脚本函数执行的结果.
TokyoTyrant::fwmKeys(string $prefix, int $max_recs):
通过key前缀匹配获取指定条数的记录.
prefix: 用以匹配的key前缀.
max_recs: 返回的记录条数.
以数组形式返回匹配到的key.
TokyoTyrant::get(mixed $keys):
用于获取一个或多个值, 接受一个字符串或一个字符串数组的key.
根据接受参数不同, 返回单个的字符串或数组. 发生错误是抛出TokyoTyrantException, 如果key没有找到, 返回空字符串, 在传递了数组参数时仅仅所有key都存在才会返回, 不会因为一个key找不到而返回错误.
TokyoTyrant::getIterator(void):
获取一个迭代器, 用于迭代所有的key/value, 返回的是一个TokyoTyrantIterator对象如果失败抛出TokyoTyrantException.
经测试没有迭代, 也没有报错.
TokyoTyrant::num(void):
获取数据库内的记录总条数.
TokyoTyrant::out(mixed $keys):
通过参数指定的一个或多个key移除记录.
keys: 一个字符串或字符串数组
返回当前TokyoTyrant对象或在失败时抛出TokyoTyrant异常.
TokyoTyrant::put(mixed $keys[, string $value]):
将一个或多个key-value对插入到数据库中, 如果keys是字符串, 第二个参数就是对应的value, 如果第一个参数是数组, 第二个参数无效, 是数组的时候, 数组自身维护key-value, 如果key存在, 则替换.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::putCat(mixed $keys[, string $value]):
如果keys是数组, 将value追加到已经存在的key原值之后, 第二个参数只有在keys是字符串时有效, 如果记录不存在, 创建新的记录.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::putKeep(mixed $keys[, string $value]):
向数据库插入一个或多个key-value对, 如果keys是字符串, 第二个参数就是它对应的value, 如果第一个参数是数组, 第二个参数失效. 如果key已经存在, 这个方法抛出一个异常标示该记录已经存在.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::putNr(mixed $keys[, string $value]):
向数据库插入一个或多个key-value, 这个方法不会等待服务端的响应.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::putShl(string $key, string $value, int $width):
连接一条记录并自左端开始截掉$width个字符.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::restore(string $log_dir, int $timestamp[, bool $check_consistency = true]):
通过update log还原数据库(这个方法不能在32位平台下使用).
log_dir: update log的路径
timestamp: 从什么时候开始还原, 微秒级的时间戳
checkconsistency: 默认true, 是否检测一致性.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::setMaster(string $host, int $port, int $timestamp[, bool $check_consistency = true]):
指ttserver的双机模式下的从机的设置(此方法在32位平台下不能使用).
$host: 从机地址.
$port: 从机端口.
$timestamp: 开始的时间戳.
$checkconsistency: 默认true, 是否检测一致性.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::size(string $key):
获取指定key对应的value的大小.
返回对应value的大小或在失败时抛出一个TokyoTyrantException.
TokyoTyrant::stat(void):
返回远程数据库的统计数据, 返回值是数组形式.
TokyoTyrant::sync(void):
在物理设备上同步数据库. 不懂具体含义.
TokyoTyrant::tune(float $timeout[, int $options = TokyoTyrant::RDBT_RECON]): 
调整数据库连接参数.
timeout: 默认5.0.
options: 基于位的参数调整, 可以是0或TokyoTyrant::RDBT_RECON, 建议不要修改第二个参数.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
TokyoTyrant::vanish(void):
将远程数据库清空.
返回当前连接对象TokyoTyrant或者在失败时抛出TokyoTyrantException.
##################################################################
TokyoTyrantTable类的接口, 该类继承自TokyoTyrant.
##################################################################
TokyoTyrantTable::add(string $key, mixed $increment[, string $type]):
该方法table database不支持. 详细解释见TokyoTyrant::add.
TokyoTyrantTable::genUid(void):
生成在当前table database内唯一的id, TableDatabase行引用使用一个数字主键.
返回唯一主键或在发生错误时抛出TokyoTyrantException.
TokyoTyrantTable::get(mixed $keys):
根据keys是一个整数还是一个整数数组获取数据库中的一行或多行.
TokyoTyrantTable::getIterator(void):
获取一个可以迭代整个数据库的迭代器.
返回当前连接对象TokyoTyrantTable或在错误时抛出TokyoTyrantException.
经测试会抛出没有定义该方法异常.
TokyoTyrantTable::getQuery(void):
获取一个TykyoTyrantQuery查询对象用以在数据库上执行搜索.
TokyoTyrantTable::out(mixed $keys):
从数据库删除指定key对应的记录, keys可以是单个整数或整数数组.
TokyoTyrantTable::put(string $key, array $columns):
向数据库插入新的行, key是该行的主键, 如果传NULL将会自动生成一个唯一id, value是一个数组包含通过key-value组织的行的内容.
返回该数据插入后的主键或在错误时抛出TokyoTyrantException.
TokyoTyrantTable::putCat(string $key, array $columns):
同put, 差别在于对已经存在的key, 会将对应列的值追加到原值之后.
返回该数据插入后的主键或在错误时抛出TokyoTyrantException.
TokyoTyrantTable::putKeep(string $key, array $columns);
和TokyoTyrant中的同名方法类似, 只不过参数类型不同.
TokyoTyrantTable::putNr(string $key[, string $value]): 
Table database不支持该方法.
TokyoTyrantTable::putKeep(string $key, string $value, int $width);
Table database不支持该方法.
TokyoTyrantTable::setIndex(string $column, int $type):
给指定列设置索引, 索引类型可以是TokyoTyrant::RDBIT_*系列的常量, 传入一个TokyoTyrant::RDBIT_VOID移除所有的索引.
所有索引类型包括: 
TokyoTyrant::RDBIT_LEXICAL: 0, 文本索引.
TokyoTyrant::RDBIT_DECIMAL: 1, 数字索引.
TokyoTyrant::RDBIT_TOKEN: 2, 标记倒排索引.
TokyoTyrant::RDBIT_QGRAM: 3, QGram倒排索引.
TokyoTyrant::RDBIT_OPT: 9998, 对索引优化.
TokyoTyrant::RDBIT_VOID: 9999, 移除索引.
TokyoTyrant::RDBIT_KEEP: 16777216, 保持已有索引.
##########################################################
TokyoTyrantQuery类中的接口
##########################################################
TokyoTyrantQuery::addCond(string $name, int $op, string $expr):
增加一个查询条件. 
name: 条件对应的列名.
op: 操作符, 是TokyoTyrant::RDBQC_*系列的常量.
expr: 表达式, 指通过op进行比较的另一个运算数.
op可以接受的参数有:
TokyoTyrant::RDBQC_STREQ: 0, 字符串相等判断.
TokyoTyrant::RDBQC_STRINC: 1, 字符串包含判断.
TokyoTyrant::RDBQC_STRBW: 2, 字符串以xx开始判断.
TokyoTyrant::RDBQC_STREW: 3, 字符串以xx结尾判断.
TokyoTyrant::RDBQC_STRAND: 4, $expr包含所有的右逗号(或空格)隔开部分全部都包含在目标中.
TokyoTyrant::RDBQC_STROR: 5, $expr包含所有的右逗号(或空格)隔开部分的其中至少一个包含在目标中.
TokyoTyrant::RDBQC_STROREQ: 6, $expr包含所有的右逗号(或空格)隔开部分的其中某部分与目标完全相同.
TokyoTyrant::RDBQC_STRRX: 7, 正则表达式匹配.
TokyoTyrant::RDBQC_NUMEQ: 8, 数字等于.
TokyoTyrant::RDBQC_NUMGT: 9, 数字大于.
TokyoTyrant::RDBQC_NUMGE: 10, 数字大于等于.
TokyoTyrant::RDBQC_NUMLT: 11, 数字小于.
TokyoTyrant::RDBQC_NUMLE: 12, 数字小于等于.
TokyoTyrant::RDBQC_NUMBT: 13, 数字范围(between), 范围涉及两个值, 在$expr中用,隔开.
TokyoTyrant::RDBQC_NUMOREQ: 14, 和给定的任意一个值相等即匹配, 多个值之间在$expr中用,隔开.
TokyoTyrant::RDBQC_NEGATE: 16777216, 与给定条件不相等的.
TokyoTyrant::RDBQC_NOIDX: 33554432, 无索引标记, 没有查到具体用法.
TokyoTyrantQuery::__construct(TokyoTyrantTable $table):
通过活动的数据库连接构建一个查询对象.
TokyoTyrantQuery::count(void):
返回当前查询对象中所有条件过滤后的记录数量或在发生错误时抛出TokyoTyrantException.
TokyoTyrantQuery::current(void):
返回当前元素.主要用来作为php中的迭代接口.
TokyoTyrantQuery::hint(void):
获取类似于关系数据库中执行计划的文本.
TokyoTyrantQuery::key(void):
返回当前key, 主要用做php的迭代接口.
TokyoTyrantQuery::metaSearch(array $queries, int $type):
在同一个数据库执行多个查询返回匹配记录集, 当前对象总是搜索中最左边的对象.
type表明的是查询的关系.参数值可以是: 
TokyoTyrant::RDBMS_UNION: 0, 并集.
TokyoTyrant::RDBMS_ISECT: 1, 交集.
TokyoTyrant::RDBMS_DIFF: 2, 差集.
TokyoTyrantQuery::next(void):
返回结果集中下一条记录. 主要用做php中的迭代接口.
TokyoTyrantQuery::out(void):
移除query匹配的所有记录, 和search工作原理一样, 不过不是返回结果而石山出他们.
TokyoTyrantQuery::rewind(void):
重置结果集并执行查询(如果没有执行过), 主要用做php的迭代接口.
TokyoTyrantQuery::search(void):
在表数据库上执行查询, 返回包含匹配记录的数组, 在返回的数组中, 第一级元素以主键作为key, 第二级是行数据.
TokyoTyrantQuery::setLimit([int $max[, int $skip]]):
设置查询返回的最大记录数以及从哪里开始.
TokyoTyrantQuery::valid(void):
检查当前项的合法性, 主要用作php迭代的接口. 
```

