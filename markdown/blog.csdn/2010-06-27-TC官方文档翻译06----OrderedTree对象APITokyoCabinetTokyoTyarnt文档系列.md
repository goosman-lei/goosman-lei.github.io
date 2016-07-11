/**
 * 转载请注明出处, 由于个人技术能力有限, 英语水平欠缺,
 * 有翻译不合适或错误的地方, 请纠正,
 * 希望不要因为我的错误误导您, 希望您的智慧可以加入.
 * @translator: selfimpr
 * @mail: goosman.lei@gmail.com
 * @blog: http://blog.csdn.net/lgg201
 */
 
 

有序树APID
typedef struct _TCTREEREC { //树的节点元素
int32_t ksiz; //key的大小
int32_t vsiz; //值的大小
struct
_TCTREEREC *left; //左子节点
struct
_TCTREEREC *right; //右子节点
} TCTREEREC;
 
typedef struct { //树
         TCTREEREC
*root; //根节点
         TCTREEREC
* cur; //当前节点
         uint64_t
rnum; //记录数量
         uint64_t
msiz; //记录总大小
         TCCMP
cmp; //用于比较的函数指针
         void
*cmpop; //比较函数的一个参数, 具体见tctreenew2函数
} TCTREE;
 
TCTREE *tctreenew(void);
         直接调用tctreenew2创建树, 传入的参数为: tccmplexical, NULL.
TCTREE *tctreenew2(TCCMP cmp, void *cmpop);
         根据传入的自定义比较函数和一个隐式参数创建树对象. 创建的过程仅初始化了TCTREE结构定义的各个属性, 其中cmp是两条记录值比较时候使用的函数, 在调用时, 该函数将会接受到5个参数, 分别是: (a记录的key指针, a记录的大小, b记录的key指针, b记录的大小, 预定义的参数cmpop), 其中第五个参数cmpop就是在创建的时候指定的cmpop. 对于自定义比较函数cmp而言, 已经有一些内建的函数: tccmplexical(默认的.),
tccmpdecimal, tccmpint32, tccmpint64等
TCTREE *tctreedup(const TCTREE *tree);
         复制一棵树对象
void tctreedel(TCTREE *tree);
         释放整棵树
void tctreeput(TCTREE *tree, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         将一条记录存储到树中
void tctreeput2(TCTREE *tree, const char
*kstr, const char *vstr);
         存储一条记录到树的字符串版本.
bool tctreeputkeep(TCTREE *tree, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         存储一条记录到树中, 如果该key已经存在, 则不做修改.
bool tctreeputkeep2(TCTREE *tree, const
char *kstr, const char *vstr);
         tctreeputkeep的字符串版本.
void tctreeputcat(TCTREE *tree, const void
*kbuf, int ksiz, const void *vbuf, int vsiz);
         存储一条记录到树中, 如果该key已经存在, 则对其值做连接操作.
void tctreeputcat2(TCTREE *tree, const char
*kstr, const char *vstr);
         tctreeputcat的字符串版本.
bool tctreeout(TCTREE *tree, const void
*kbuf, int ksiz);
         从树中移除指定key的数据
bool tctreeout2(TCTREE *tree, const char
*kstr);
         tctreeout的字符串版本
const void *tctreeget(TCTREE *tree, const
void *kbuf, int ksiz, int *sp);
         通过指定的key获取一条记录的值, 传入的指针sp将会被改变为返回记录的值的大小
const char *tctreeget2(TCTREE *tree, const
char *kstr);
         tctreeget的字符串版本
void tctreeiterinit(TCTREE *tree);
         初始化树的迭代器, 实际上就是把树结构中的cur指向树的最左子节点
const void *tctreeiternext(TCTREE *tree,
int *sp);
         返回树的当前迭代元素, 修改sp为元素大小, 并移动迭代指针
const char *tctreeiternext2(TCTREE *tree);
         tctreeiternext的字符串版本
uint64_t tctreernum(const TCTREE *tree);
         返回树当前记录数量
uint64_t tctreemsiz(const TCTREE *tree);
         返回树的内存大小
TCLIST *tctreekeys(const TCTREE *tree);
         返回树中所有的key的列表对象
TCLIST *tctreevals(const TCTREE *tree);
         返回当前树中所有值的列表对象
int tctreeaddint(TCTREE *tree, const void
*kbuf, int ksiz, int num);
         向指定key的值上增加num大小, 并返回相加后的值
double tctreeadddouble(TCTREE *tree, const
void *kbuf, int ksiz, double num);
         tctreeaddint的double版本
void tctreeclear(TCTREE *tree);
         完全释放一个树
void tctreecutfringe(TCTREE *tree, int
num);
         删除树的num个外围子节点. 算法中, 首先对树中的所有节点广度优先遍历, 放入一个数组中, 然后对该数组从后向前进行释放节点.
void *tctreedump(const TCTREE *tree, int
*sp);
         序列化树, sp记录序列化结果的长度.
TCTREE *tctreeload(const void *ptr, int
size, TCCMP cmp, void *cmpop);
从一个序列化串中加载一个树, cmp和cmpop意义同tctreenew一样, ptr是序列化串的指针, size是其大小.
 
