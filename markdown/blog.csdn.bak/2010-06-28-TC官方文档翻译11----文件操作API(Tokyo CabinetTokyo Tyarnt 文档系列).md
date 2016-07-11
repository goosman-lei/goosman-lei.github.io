---
layout: post
title: TC官方文档翻译11----文件操作API(Tokyo Cabinet/Tokyo Tyarnt 文档系列)
date: 2010-06-28 00:28:00
categories: [文档, api, path, null, shell, blog]
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
 
 

文件系统API
char *tcrealpath(const char *path);
         返回指定路径的全路径, 如果给定的路径是非法的,则返回NULL
bool tcstatfile(const char *path, bool
*isdirp, int64_t *sizep, int64_t *mtimep);
         获取文件的状态信息, 如果成功返回true, 否则返回false, 成功后isdirp中将记录文件是否是目录, sizep用来记录文件的大小, mtimep用来记录文件最后修改时间
void *tcreadfile(const char *path, int
limit, int *sp);
         读取一个文件的内容, 当path为NULL时, 将从标准输入中进行读取, limit则指定了读取多少个字符, sp将在最终用来记录读取到的数据量, 返回值就是读取到的内容.
TCLIST *tcreadfilelines(const char *path);
         读取文件的每一行并存入一个列表对象中. 如果path指定为NULL, 将会从标准输入中读取.
bool tcwritefile(const char *path, const
void *ptr, int size);
         向指定文件中写入内容, 如果path为NULL, 将会向标准输出写入, 写入成功返回true, 否则返回false
bool tccopyfile(const char *src, const char
*dest);
         拷贝文件, 如果dest指定的文件已经存在, 将会被覆盖. 复制成功返回true, 否则返回false
TCLIST *tcreaddir(const char *path);
         读取指定目录, 并返回列表形式的文件名
TCLIST *tcglobpat(const char *pattern);
         获取所有匹配给定模式的文件名列表
bool tcremovelink(const char *path);
         删除给定的文件或目录, 如果是目录, 还会递归的删除孩子
bool tcwrite(int fd, const void *buf,
size_t size);
         把给定的内容buf写入到指定的文件描述符对应的文件中
bool tcread(int fd, void *buf, size_t
size);
         从给定的文件描述符对应文件中读取内容到buf中, size指定了buf的大小
bool tclock(int fd, bool ex, bool nb);
         通过fcntl()函数设置文件锁, fd指定文件描述符, ex指定是互斥锁还是共享锁(只读锁), ex为true表示是互斥锁, nb指定是否等待锁定, 当nb为false时, 会一直等待直到这个锁定请求完成.
bool tcunlock(int fd);
         解锁文件.
int tcsystem(const char **args, int anum);
         执行一个shell命令, args是一个字符串数组, 用来指定命令和它的参数, anum指明该数组大小.
 
