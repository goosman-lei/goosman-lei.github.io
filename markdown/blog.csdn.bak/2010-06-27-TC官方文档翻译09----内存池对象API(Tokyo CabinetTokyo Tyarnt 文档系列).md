---
layout: post
title: TC官方文档翻译09----内存池对象API(Tokyo Cabinet/Tokyo Tyarnt 文档系列)
date: 2010-06-27 03:40:00
categories: [文档, api, exe, struct, tree, blog]
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
 
 

内存池API
typedef struct {//内存池元素
         void
*ptr; //指针
         void
(*del)(void *); //删除函数
} TCMPELEM;
 
typedef struct { //内存池对象
         void
*mutex; //互斥对象, 用于锁
         TCMPELEM
*elems; //内存池元素数组
         int
anum; //内存池元素数组大小
         int
num; //已被使用的内存池大小
} TCMPOOL;
 
TCMPOOL *tcmpoolnew();
         初始化创建一个内存池, 默认创建的内存池容量为128
void tcmpooldel(TCMPOOL *mpool);
         遍历所有的内存池元素, 并逐个调用创建时他们自己定义的del方法
void tcmpoolpush(TCMPOOL *mpool, void *ptr,
void (*del)(void *));
         向内存池中放入一个元素, ptr是要存放的内容, del是释放该元素时使用的函数. 返回ptr
void *tcmpoolpushptr(TCMPOOL *mpool, void
*ptr);
         当要放入的元素的内容可以用free进行释放的时候, 使用此函数, 内部的调用如下: tcmpoolpush(mpool,
ptr, (void (*)(void *))free), 可以看出是对free做了强制类型转换然后作为参数
TCXSTR *tcmpoolpushxstr(TCMPOOL *mpool,
TCXSTR *xstr);
         向内存池中插入一个可扩充字符串TCXSTR, 与tcmpoolpushptr同理, 不过这里传入的释放函数是tcxstrdel
TCLIST *tcmpoolpushlist(TCMPOOL *mpool,
TCLIST *list);
         向内存池中插入一个列表对象, 与tcmpoolpushptr同理, 不过这里传入的释放函数是tclistdel
TCMAP *tcmpoolpushmap(TCMPOOL *mpool, TCMAP
*map);
         同上, 这里的释放函数是tcmapdel
TCTREE *tcmpoolpushtree(TCMPOOL *mpool,
TCTREE *tree);
         同上, 这里的释放函数是tctreedel
void *tcmpoolmalloc(TCMPOOL *mpool, size_t
size);
         向指定的内存池中申请size大小的内存空间, 内部是调用TCMALLOC分配空间, 并放入了内存池的管理
TCXSTR *tcmpoolxstrnew(TCMPOOL *mpool);
         创建一个可扩展字符串对象(TCXSTR), 然后加入到内存池管理
TCLIST *tcmpoollistnew(TCMPOOL *mpool);
         创建一个列表对象, 然后加入到内存池管理
TCMAP *tcmpoolmapnew(TCMPOOL *mpool);
         创建一个TCMAP对象, 然后加入到内存池管理
TCTREE *tcmpooltreenew(TCMPOOL *mpool);
         创建一个TCTREE对象, 然后加入到内存池管理
void tcmpoolpop(TCMPOOL *mpool, bool exe);
         把内存池中的最后一个元素弹出, exe是指定是否执行该元素的析构函数(就是传入的del)
void tcmpoolclear(TCMPOOL *mpool, bool exe);
         把内存池置空, exe用来指定是否为每个元素执行析构函数.
         对于tcmpoolpop和tcmpoolclear函数而言, 其实内部并没有做真正的移除操作, 而是改变内存池的num属性, 这样不仅达到了对内存池的管理, 而且由于没有实际的操作, 达到了效率的提升
TCMPOOL *tcmpoolglobal();
         用来获取一个全局的内存池对象, 如果目前没有全局内存池对象, 则创建一个, 并在创建完之后, 通过atexit()函数注册了一个终止函数tcmpooldelglobal,在这个终止函数里对全局内存池对象做了销毁操作(没有对内存池中的对象进行销毁)
 
