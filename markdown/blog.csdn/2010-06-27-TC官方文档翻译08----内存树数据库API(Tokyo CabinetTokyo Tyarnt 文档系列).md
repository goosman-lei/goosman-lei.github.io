/**
 * 转载请注明出处, 由于个人技术能力有限, 英语水平欠缺,
 * 有翻译不合适或错误的地方, 请纠正,
 * 希望不要因为我的错误误导您, 希望您的智慧可以加入.
 * @translator: selfimpr
 * @mail: lgg860911@yahoo.com.cn
 * @blog: http://blog.csdn.net/lgg201
 */
 
 

内存树数据库API
typedef struct {
         void
*mmtx; //锁(互斥对象)
         TCTREE
*tree; //TCTREE, 由此可以看出, 内存树数据库是对TCTREE的简单封装(主要是增加了锁的机制).
} TCNDB;
 
TCNDB *tcndbnew();
         创建比较方法为默认的tccmplexical, cmpop为NULL的数据库
TCNDB *tcndbnew2(TCCMP cmp, void *cmpop);
         创建一个TCNDB对象, 出示化它的锁以及tree中的相关数据(cmp和cmpop参数请参见TCTREE)
void tcndbdel(TCNDB *ndb);
         删除指定数据库, 对数据库记录的释放, 调用的是tctreedel
void tcndbput(TCNDB *ndb, const void *kbuf,
int ksiz, const void *vbuf, int vsiz);
         向数据库中存储一条记录.
void tcndbput2(TCNDB *ndb, const char
*kstr, const char *vstr);
         tcndbput的字符串版本
bool tcndbputkeep(TCNDB *ndb, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         插入时如果该key已经有值, 则无操作
bool tcndbputkeep2(TCNDB *ndb, const char
*kstr, const char *vstr);
         tcndbputkeep的字符串版本
void tcndbputcat(TCNDB *ndb, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         插入时, 如果该key已经有值, 则直接把新值追加到原始值后面存储.
void tcndbputcat2(TCNDB *ndb, const char
*kstr, const char *vstr);
         tcndbputcat的字符串版本
bool tcndbout(TCNDB *ndb, const void *kbuf,
int ksiz);
         从数据库中删除指定key的记录
bool tcndbout2(TCNDB *ndb, const void
*kstr);
         tcndbout的key为字符串版本
void *tcndbget(TCNDB *ndb, const void
*kbuf, int ksiz, int *sp);
         从数据库中检索指定key对应的值, 在函数返回使sp将会记录该值的大小
char *tcndbget2(TCNDB *ndb, const char
*kstr);
         tcndbget的字符串版本
int tcndbvsiz(TCNDB *ndb, const void *kbuf,
int ksiz);
         获取数据库中指定key对应值的大小
int tcndbvsiz2(TNCDB *ndb, const char
*kstr);
         tcndbvsiz的字符串版本
void tcndbiterinit(TNCDB *ndb);
         初始化数据库迭代器
void *tcndbiternext(TCNDB *ndb, int *sp);
         返回当前迭代点的key值, sp记录其大小, 并将迭代点后移
char *tcndbiternext2(TCNDB *ndb);
         tcndbiternext的字符串版本
TCLIST *tcndbfwmkeys(TCNDB *ndb, const void
*pbuf, int psiz, int max);
         返回以指定key开头的最多max个key组成的列表对象.
         首先保存一份当前普通的迭代位置, 然后调用tctreeiterinit2接口(以指定key重置迭代接口, 也就是将tree的cur指向其第一个以指定key开始的节点位置)重置迭代接口, 然后进行迭代获取到max个或所有的key并放入创建的TCLIST中, 最后把原有的普通迭代位置还原.
TCLIST *tcndbfwmkeys2(TCNDB *ndb, const
char *pstr, int max);
         tcndbfwmkeys的字符串版本
uint64_t tcndbrnum(TCNDB *ndb);
         返回数据库中的记录数
uint64_t tcndbmsiz(TCNDB *ndb);
         返回数据库大小
int tcndbaddint(TCNDB *ndb, const void
*kbuf, int ksiz, int num);
         向数据库指定key对应的值上增加num并返回增加后的值
double tcndbadddouble(TNCDB *ndb, const
void *kbuf, int ksiz, double num);
         tcndbaddint的double版
void tcndbvanish(TCNDB *ndb);
         清空数据库
void tcndbcutfringe(TCNDB *ndb, int num);
         删除数据库的num个外围节点, 实际调用的是tctreecutfringe.
 
