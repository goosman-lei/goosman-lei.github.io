---
layout: post
title: TC官方文档翻译02----基础API(Tokyo Cabinet/Tokyo Tyarnt 文档系列)
date: 2010-06-26 01:15:00
categories: [文档, api, tree, null, 工具, blog]
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
 

工具API是一组方便在内存处理记录的例程, 另外还有可扩充字符串, array list,
hash map, ordered tree也都是很有用的. 在tcutil.h中有详细的说明
描述
使用工具api需要引入以下头文件
#include <tcutil.h>
#include <stdlib.h>
#include <stdbool.h>
#include <stdint.h>
TCXSTR是可扩充字符串类型, 可扩充字符串对象使用tcxstrnew函数创建, 通过tcxstrdel函数删除. TCLIST被用于数组列表, tclistnew和tclistdel分别用于数组列表的创建和删除. TCMAP被用于hash map, 同样, tcmapnew和tcmapdel是创建和删除的接口. TCTREE是ordered tree, 同样有tctreenew和tctreedel接口. 为了避免内存泄露, 在不使用的时候删除每个对象非常重要.
基础api
extern const char *tcversion;
常量tcversion包含了版本信息
extern void (*tcfatalfunc)(const char *);
         用来处理致命错误的函数指针, 参数指定为错误消息. 该指针初始值为NULL, 如果是NULL, 那么默认的处理函数会在发生致命错误时被调用, 致命的错误指内存分配失败
void *tcmalloc(size_t size);
用于在内存中分配一个区域
‘size’指定要分配的内存大小. 
返回值是指向被分配区域的的指针
这个函数会处理内存分配的失败, 由于它的返回值是通过malloc调用来分配的, 所以, 可以在不使用的使用通过调用free来释放.
void *tccalloc(size_t nmemb, size_t size);
         与tcmalloc类似, 不过是calloc的调用, nmemb参数表示要分配的元素的个数.
void *tcrealloc(void *prt, size_t size);
         与tcmalloc类似, 不过是realloc的调用. ptr参数表示要重新分配的内存地址.
void *tcmemdup(const void *ptr, size_t
size);
         用于复制内存块
         ptr:指定要复制源地址
         size:指定要复制的块的大小
返回复制得到的块的地址.
该函数先通过tcmalloc分配size大小空间, 然后用memcpy拷贝内存块, 最后给新的内存块末尾增加了/0, 因此返回的指针可以被直接作为字符串使用. 由于内存块是用malloc分配的, 所以在不使用的时候, 可以通过free释放.
char *tcstrdup(const void *str);
         str:指定要复制的字符串
返回复制后的字符串地址. 返回的指针可以用free释放.
void tcfree(void *ptr);
         ptr:要释放的指针, 如果是NULL, 该函数不会产生作用.
尽管这只是对free的一个包装, 但在应用中对malloc系列的函数还是很有用的.
 
