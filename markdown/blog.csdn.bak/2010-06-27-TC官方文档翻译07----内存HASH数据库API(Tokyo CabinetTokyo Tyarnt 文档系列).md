---
layout: post
title: TC官方文档翻译07----内存HASH数据库API(Tokyo Cabinet/Tokyo Tyarnt 文档系列)
date: 2010-06-27 03:37:00
categories: [文档, 数据库, api, struct, blog, 存储]
tags: []
---
/**
 * 转载请注明出处, 由于个人技术能力有限, 英语水平欠缺,
 * 有翻译不合适或错误的地方, 请纠正,
 * 希望不要因为我的错误误导您, 希望您的智慧可以加入.
 * @translator: selfimpr
 * @mail: lgg860911@yahoo.com.cn
 * @blog: http://blog.csdn.net/lgg201
 */
 
 

内存HASH数据库API
typedef struct { //内存hash数据库
         void
**mmtxs; //全局锁对象
         void
*imtx; //基于路径的锁对象
         TCMAP
**maps; //内部TCMAP对象
         in
titer; //maps的迭代位置
}
 
TCMDB *tcmdbnew();
         创建一个桶大小为TCMDBDEFBNUM(65536)的数据库.
TCMDB *tcmdbnew2(uint32_t bnum);
         创建有指定bnum的桶数组的数据库. 实际上是创建了TCMDBMNUM(默认8)个TCMAP对象, 每个map中的桶大小为bnum / TCMDBMNUM +
17.
void tcmdbdel(TCMDB *mdb);
         删除指定的内存数据库
void tcmdbput(TCMDB *mdb, const void *kbuf,
int ksiz, const void *vbuf, int vsiz);
         通过对key进行hash得到要存入的map, 然后调用map的tcmapput插入
void tcmdbput2(TCMDB *mdb, const char
*kstr, const char *vstr);
         tcmdbput的字符串版本.
bool tcmdbputkeep(TCMDB *mdb, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         向数据库中插入一条记录, 如果已经存在, 则无操作
bool tcmdbputkeep2(TCMDB *mdb, const char
*kstr, const char *vstr);
         tcmdbputkeep的字符串版本
void tcmdbputcat(TCMDB *mdb, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         向数据库中插入一条记录, 如果已经存在, 则将新的值连接到原始值末尾
void tcmdbputcat2(TCMDB *mdb, const char
*kstr, const char *vstr);
         tcmdbputcat的字符串版本
bool tcmdbout(TCMDB *mdb, const void *kbuf,
int ksiz);
         从数据库中删除指定key的元素
bool tcmdbout2(TCMDB *mdb, const char
*kstr);
         tcmdbout的字符串版本
void *tcmdbget(TCMDB *mdb, const void
*kbuf, int ksiz, int *sp);
         从数据库中检索指定key对应的值, sp用来记录值的大小.
char *tcmdbget2(TCMDB *mdb, const char
*kstr);
         tcmdbget的字符串版本
int tcmdbvsiz(TCMDB *mdb, const void *kbuf,
int ksiz);
         返回指定key对应的值的内存大小
int tcmdbvsiz2(TCMDB *mdb, const char
*kstr);
         tcmdbvsiz的字符串key版本
void tcmdbiterinit(TCMDB *mdb);
         迭代器初始化, 首先通过tcmapiterinit初始化每个map的迭代器, 然后重置mdb的iter
void *tcmdbiternext(TCMDB *mdb, int *sp);
         获取下一条记录, sp将记录这条记录的值的大小
char *tcmdbiternext2(TCMDB *mdb);
         tcmdbiternext的字符串版本
TCLIST *tcmdbfwmkeys(TCMDB *mdb, const void
*pbuf, int psiz, int max);
        获取当前数据库中所有和指定key(pbuf至pbuf+psiz的内存内容)相同(也就是说以指定key开头)的所有key, 如果指定max, 就只获取max个, 该匹配是逐个从8个map里去查找
TCLIST *tcmdbfwmkeys2(TCMDB *mdb, const
char *pstr, int max);
         tcmdbfwmkeys的字符串版本
uint64_t tcmdbrnum(TCMDB *mdb);
         返回当前数据库的记录数
uint64_t tcmdbmsiz(TCMDB *mdb);
         返回当前数据库的内存占用
int tcmdbaddint(TCMDB *mdb, const void
*kbuf, int ksiz, int num);
         对数据库中指定key对应的值加num存储, 并返回增加后的值.
double tcmdbadddouble(TCMDB *mdb, const
void *kbuf, int ksiz, double num);
         tcmdbaddint的double版本.
void tcmdbvanish(TCMDB *mdb);
         清除当前数据库中的所有记录
void tcmdbcutfront(TCMDB *mdb, int num);
         移除数据库中的前num条记录. 在实际的移除中, 可能移除的并不是前num条. 而是通过num / TCMDBMNUM + 1计算到要从每个map中移除多少条记录, 然后通过map的tcmapcutfront接口进行移除
 
