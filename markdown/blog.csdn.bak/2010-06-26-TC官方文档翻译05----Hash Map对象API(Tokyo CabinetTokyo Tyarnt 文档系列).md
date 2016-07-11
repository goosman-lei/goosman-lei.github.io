---
layout: post
title: TC官方文档翻译05----Hash Map对象API(Tokyo Cabinet/Tokyo Tyarnt 文档系列)
date: 2010-06-26 01:22:00
categories: [文档, api, struct, 算法, 存储, 数据库]
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
 

Hash Map的API
typedef struct _TCMAPREC { //HASH MAP元素结构定义
         int32_t
ksiz; //key的大小
         int32_t
vsiz; //value的大小
         struct
_TCMAPREC *left; //左子节点用来维护二叉搜索树
         struct
_TCMAPREC *right; //右子节点
         struct
_TCMAPREC *pref; //前一个元素用来维护链表
         struct
_TCMAPREC *next; //后一个元素
} TCMAPREC;
 
typedef struct { //HASH MAP结构定义
         TCMAPREC
**buckets; //桶数组
         TCMAPREC
*first; //第一个元素
         TCMAPREC
*last; //最后一个元素
         TCMAPREC
*cur; //当前元素
         uint32_t
bnum; //桶大小
         uint64_t
rnum; //记录个数
         uint64_t
msiz; //总大小(字节数)
} TCMAP;
 
TCMAP *tcmapnew(void);
         创建4093(默认值)个桶的map
TCMAP *tcmapnew2(uint32_t bnum);
         创建指定数量桶的map.
         MAP有一个预定义的最小内存使用量131072, 如果bnum指定的数量个桶占用空间小于该值, 则按该值分配, 否则按实际量来分配内存. MAP对象的其实属性都设置为空或0等默认值.
TCMAP *tcmapnew3(const char *str, …);
         接受不定个字符串, 以每两个为一对key-value的方式初始化一个MAP对象, 该MAP对象实际上是调用tcmapnew2创建的, 初始元素个数是31.
TCMAP *tcmapdup(const TCMAP *map);
         取传入的map的bnum, rnum以及TCMAPDEFBNUM(4093)中最大的值作为桶数量调用tcmapnew2创建一个map, 把参数map中的记录拷贝到新的map中并返回
void tcmapdel(TCMAP *map);
         完全释放一个map对象.
void tcmapput(TCMAP *map, const void *kbuf,
int ksiz, const void *vbuf, int vsiz);
         map:要插入数据的map对象
         kbuf:
key字符串指针
         ksiz:
key的大小, 如果ksiz超过TCMAPKMAXSIZ(0XFFFFFF),区0XFFFFFF.
         vbuf:值的字符串指针
         vsiz:值的大小
1.      用TCMAPHASH1算法对key求一个hash值, 并通过这个值得到key对应的桶数组索引.
2.      分别获取到桶和桶的地址
3.      用TCMAPHASH2对key再次求hash, 将hash值与~TCMAPKMAXSIZ做与运算(求反)
4.      接下来就是二叉搜索树插入算法中的循环(寻找插入节点位置), 但是这里有两种策略, 首先会按照hash值来处理查找, 当hash值相同时则比较实际值.
5.      找到了把新的值插入, 如果有重复的key(指真实key而不是hash后的)则覆盖值
void tcmapput2(TCMAP *map, const char
*kstr, const char *vstr);
         是调用tcmapput实现的, 不过这里是对字符串的处理做了封装.
bool tcmapputkeep(TCMAP *map, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         与tcmapput算法相同, 不过在这里碰到key完全相同的时候是不会覆盖的, 而是返回false, 对应的, 当插入成功时, 返回true
bool tcmapputkeep2(TCMAP *map, const char
*kstr, const char *vstr);
         同tcmapputkeep, 不过是对字符串的处理
void tcmapputcat(TCMAP *map, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         同上面的tcmapput方法算法, 不过在key完全相同时, 是通过内存拷贝将值进行了连接
void tcmapputcat2(TCMAP *map, const char
*kstr, const char *vstr);
         同tcmapputcat, 是对字符串的处理
bool tcmapout(TCMAP *map, const void *kbuf,
int ksiz);
         使用的是与tcmapput的二叉搜索树插入算法对应的移除算法(二叉搜索树的移除主要问题在于查找后即节点, 当然在这里可以会有更多细节处理, 没有细看), 移除指定元素, 如果没有找到, 返回false, 找到并移除了返回true.
bool tcmapout2(TCMAP *map, const char
*kstr);
         tcmapout的字符串版本
const void *tcmapget(const TCMAP *map,
const void *kbuf, int ksiz, int *sp);
         从数据库检索指定key对应的记录, 如果查找成功, 返回改记录的指针, 否则, 返回NULL
const char *tcmapget2(const TCMAP *map,
const char *kstr);
         从数据库检索指定字符串key存储的字符串记录.
bool tcmapmove(TCMAP *map, const void
*kbuf, int ksiz, bool head);
         对于那些hash值相同的key-value数据, 实际上是在树的同一个节点上存储为一个链表的, 该方法是将指定key的值移动到链表的两端, 如果head是true, 移动到第一个元素, 如果是false, 移动到末尾. 如果没有查找到key对应的记录, 返回false, 否则, 返回true
bool tcmapmove2(TCMAP *map, const char
*kstr, bool head);
         tcmapmove的字符串版本.
void tcmapiterinit(TCMAP *map);
         MAP迭代的初始化, 实际上就是把map的cur指针移动到桶数组第一个元素
cosnt void *tcmapiternext(TCMAP *map, int
*sp);
         迭代器的next方法, 获取当前迭代点的元素, 并将迭代位置向前推一个, 如果没有元素可迭代了, 就返回NULL. 返回时同时向sp中记录了返回值的大小
const char *tcmapiternext2(TCMAP *map);
         tcmapiternext的字符串版本
uint64_t tcmaprnum(const TCMAP *map);
         返回当前map对象中存储的记录条数
uint64_t tcmapmsiz(const TCMAP *map);
         返回当前map对象占用内存大小(不仅仅是存储的记录的大小, 也包括了map对象保留的一些数据比如rnum, msiz等)
TCLIST *tcmapkeys(const TCMAP *map);
         以链表的方式遍历map返回内部保存的所有的key的列表对象
TCLIST *tcmapvals(const TCMAP *map);
         以链表的方式遍历map返回内部保存的所有值的列表对象.
int tcmapaddint(TCMAP *map, const void
*kbuf, int ksiz, int num);
         对指定key的值以int数据增加num值, 如果该key对应的记录大小和int不相等, 会返回INT_MIN, 否则返回相加后的值
double tcmapadddouble(TCMAP *map, const
void *kbuf, int ksiz, double num);
         同tcmapaddint, 不过这里增加的是double值
void tcmapclear(TCMAP *map);
         清空map中的所有记录
void tcmapcutfront(TCMAP *map, int num);
         移除map中的前num个元素, 内部是调用了map的迭代接口和out接口处理的.
void *tcmapdump(const TCMAP *map, int *sp);
         将map进行序列化并返回, sp将记录序列化结果的长度
TCMAP *tcmapload(const void *ptr, int size);
         从ptr的前size长度的内容加载一个map对象并返回. 由于这里load到的MAP是通过tcmapnew创建的, 因此可以通过tcmapdel来释放.
 
