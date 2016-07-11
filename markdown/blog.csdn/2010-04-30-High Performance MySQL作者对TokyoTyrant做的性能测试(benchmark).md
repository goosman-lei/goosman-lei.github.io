英语水品欠佳, 有错误还请指出, 以便获取更多知识, 谢谢.
 
 
#High Performance MySQL作者对TT做的性能测试(benchmark)
作者的标准测试有两次是涉及TT的, 下面这次是专门关于TT的, 另外一次是Mysql和几种NoSQL数据库的对比测试.
原文地址: 
专门针对TT的测试
Tokyo
Tyrant – The Extras Part I : Is it Durable? (http://www.mysqlperformanceblog.com/2009/11/10/tokyo-tyrant-the-extras-part-i-is-it-durable/)
Tokyo
Tyrant – The Extras Part II : The Performance Wall (http://www.mysqlperformanceblog.com/2009/11/11/tokyo-tyrant-the-extras-part-ii-the-performance-wall/)
Tokyo
Tyrant -The Extras Part III : Write Bottleneck (http://www.mysqlperformanceblog.com/2009/11/12/tokyo-tyrant-%E2%80%93-the-extras-part-iii-write-bottleneck/)
多种数据库对比测试
MySQL-Memcached
or NOSQL Tokyo Tyrant – part 1 (http://www.mysqlperformanceblog.com/2009/10/15/mysql-memcached-or-nosql-tokyo-tyrant-part-1/)
MySQL-Memcached
or NOSQL Tokyo Tyrant – part 2 (http://www.mysqlperformanceblog.com/2009/10/16/mysql_memcached_tyrant_part2/)
MySQL-Memcached
or NOSQL Tokyo Tyrant – part 3 (http://www.mysqlperformanceblog.com/2009/10/19/mysql_memcached_tyrant_part3/)
可靠性:
1. 脚本向TC持续插入id-timestampe的数据, 通过kill -9杀死服务
     结果: 重启服务后所有以插入记录恢复
     分析: TC使用了内存映射文件, 文件缓存操作, 加载文件, 写回数据, 释放内存等工作都由系统完成, 因此kill -9不会导致数据丢失.
2. 突然

断电后还是可能会导致数据错误
     代码分析
/* Synchronize updated contents of a hash
database object with the file and the device. */
bool tchdbsync(TCHDB *hdb){
 assert(hdb);
  //锁定数据库
 if(!HDBLOCKMETHOD(hdb, true)) return false;
  //文件描述符小于0, 以writer打开, 在一个事务中的情况, 
 if(hdb->fd < 0 || !(hdb->omode & HDBOWRITER) ||
hdb->tran){
   //设置错误代码
   tchdbsetecode(hdb, TCEINVALID, __FILE__, __LINE__, __func__);
    解锁数据库
   HDBUNLOCKMETHOD(hdb);
   return false;
  }
  //首先检查是否以异步方式启动,如果是把刷延迟记录池中的记录
  //如果同步启动或刷延迟记录失败, 解锁返回
 if(hdb->async && !tchdbflushdrp(hdb)){
   HDBUNLOCKMETHOD(hdb);
   return false;
  }
  //将内存同步更新到物理设备上
 bool rv = tchdbmemsync(hdb, true);
 HDBUNLOCKMETHOD(hdb);
 return rv;
}
/* Synchronize updating contents on memory
of a hash database object. */
bool tchdbmemsync(TCHDB *hdb, bool phys){
 assert(hdb);
 if(hdb->fd < 0 || !(hdb->omode & HDBOWRITER)){
   tchdbsetecode(hdb, TCEINVALID, __FILE__, __LINE__, __func__);
   return false;
  }
 bool err = false;
 char hbuf[HDBHEADSIZ];
 tchdbdumpmeta(hdb, hbuf);
 memcpy(hdb->map, hbuf, HDBOPAQUEOFF);
 if(phys){
   size_t xmsiz = (hdb->xmsiz > hdb->msiz) ? hdb->xmsiz :
hdb->msiz;
   //msync会将所有改变的内存映射到磁盘
   if(msync(hdb->map, xmsiz, MS_SYNC) == -1){
     tchdbsetecode(hdb, TCEMMAP, __FILE__, __LINE__, __func__);
     err = true;
    }
   if(fsync(hdb->fd) == -1){
     tchdbsetecode(hdb, TCESYNC, __FILE__, __LINE__, __func__);
     err = true;
    }
  }
 return !err;
}
     分析: 
         根据作者分析, 如果msync函数未调用, 则不能保证数据和磁盘同步.
         只有tchdbmemsync函数调用了msync, 而tchdbmemsync被以下方法调用:
              tchdboptimize
              tchdbsync
              tchdbtranbegin
              tchdbtrancommit
              tchdbtranabort
              tchdbcloseimpl
              tchdbcopyimpl
         即: 在优化, 同步, 关闭连接, 开启, 提交, 事务中断操作发生时才会同步数据.
         然而, TC中的事务实际是一个全局事务, 所有写入操作都会被锁, 即同时只有一个写入.
         作者查找了Tyrant对Cabinet的调用没有发现对该方法的自动调用.
     解决方案: 为了可靠性, 就需要直接调用同步命令, 每次写入后进行同步.
![](http://hi.csdn.net/attachment/201004/30/8670_127260460401o0.jpg)
     问题: 同步带来了25倍左右的性能差
     分析:
         从改为同步后的性能问题可以看出TC的同步实现的是全局同步, 所有的改变被一次刷入数据库, 因此需要修改为记录级别的同步, 如果有32个线程, 则仅有1个线程在运行, 其他31个被锁定, 严重的降低了性能.
         作者认为可以借鉴InnoDB的小技巧. 通过一个简单的脚本在后台单独的运行, 每秒同步一次, 作者在做了这种同步与非同步的测试, 性能差为0.
         这个小的任务脚本, 可以通过TT提供的扩展接口自动进行调度.
     提升同步: 通过一个简单的脚本在后台单独的运行, 交给TT扩展接口自动调度.
![](http://hi.csdn.net/attachment/201004/30/8670_1272604604717C.jpg)
     结果: 作者测试结果为同步后性能降低一半, 但是可以通过调整调度频率来调节性能.
![](http://hi.csdn.net/attachment/201004/30/8670_1272604604u7n2.jpg)
数据增长带来的问题:
  性能降低的一个显而易见的原因是数据量的增大导致内存操作的减少, 取而代之的是磁盘操作.
  作者禁用了文件系统缓存后测试了同步与异步的性能, 性能差是20倍, IO速率分别为31M/s和3.2M/s
![](http://hi.csdn.net/attachment/201004/30/8670_1272604604uts9.jpg)
  从数据库方向, 作者测试了不同内存分配下的性能比对, 当然是内存高的性能高, 但是和mysql的对比结果是, 256M内存分配时, TT的并发是964TPS, 而mysql是160TPS, 因此, 作者认为5倍的提升是可取的.
![](http://hi.csdn.net/attachment/201004/30/8670_1272604604Mjjr.jpg)
写入瓶颈
  TT同时只允许一个writer
![](http://hi.csdn.net/attachment/201004/30/8670_127260460491o7.jpg)
  在作者对写入瓶颈的测试中,
8线程是性能最佳的.
  作者在文末表明一个观点: 通过memcached客户端将数据分发到多个后端的TT数据库, 但是, 还没有做这方面的测试.
 
