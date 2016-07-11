/**
 * 转载请注明出处, 由于个人技术能力有限, 英语水平欠缺,
 * 有翻译不合适或错误的地方, 请纠正,
 * 希望不要因为我的错误误导您, 希望您的智慧可以加入.
 * @translator: selfimpr
 * @mail: lgg860911@yahoo.com.cn
 * @blog: http://blog.csdn.net/lgg201
 */
 

Array List的API
TCLIST *tclistnew(void);
         创建新的列表对象, 默认分配的是64个元素大小.
TCLIST *tclistnew2(int anum);
         创建指定大小的列表
TCLIST *tclistnew3(const char *str, …);
         创建包含指定所有字符串参数的列表对象, 除了第一个参数, 后面的都可以是NULL.
TCLIST *tclistdup(const TCLIST *list);
         复制一个新的列表对象
void tclistdel(TCLIST *list);
         删除一个列表对象, 并删除其中存储的所有的元素对象
int tclistnum(const TCLIST *list);
         返回当前列表中存储的元素的个数
const void *tclistval(const TCLIST *list, int
index, int *sp);
         返回指定列表对象的第index个元素, sp指定一个int指针用来存放最终返回的元素的长度. index超过元素个数时返回NULL
const char *tclistval2(const TCLIST *list,
int index);
         返回指定列表中的第index个元素, index超过元素个数时返回NULL
void tclistpush(TCLIST *list, const void
*ptr, int size);
         将ptr指定的变量的size个内容作为一个元素追加到list中, 如果list大小不够会实现对list进行扩充
void tclistpush2(TCLIST *list, const char
*str);
把一个字符串追加到list中
void *tclistpop(TCLIST *list, int *sp);
         把list中的最后一个元素弹出返回, sp为指定的一个int指针, 由函数在执行结束前把元素大小存入sp
char *tclistpop2(TCLIST *list);
         把list中最后一个元素作为字符串弹出.
void tclistunshift(TCLIST *list, const void
*ptr, int size);
         向列表最前面插入一个元素, 如果列表中用于存放数据的数组第一个元素已经有数据,就会将该数组中存放的元素, 在为其分配的区域中后移, 如果在这个过程中发现已分配区域大小不足, 会对其扩充. 完成空间的分配后, 就把给定的ptr中的值拷贝到第一个元素的位置.
void tclistunshift2(TCLIST *list, const
char *str);
         同tclistunshift, 不过这里插入的是一个字符串
void *tclistshift(TCLIST *list, int *sp);
         返回指定列表对象的第一个元素值, 并将该元素大小记录到传入的sp指针上.
char *tclistshift2(TCLIST *list);
         同tclistshift, 这里返回字符串.
void tclistinsert(TCLIST *list, int index,
const void *ptr, int size);
         将列表index位置之后的元素整体后移, 挪出来一个元素位置, 把ptr放入. size指要插入元素的大小
void tclistinsert2(TCLIST *list, int index,
const char *str);
         向列表中指定位置增加一个字符串元素
void *tclistremove(TCLIST *list, int index,
int *sp);
         从列表中移除指定索引位置的元素, 返回值是移除的元素的值, sp在函数结束时被改写为移除元素的大小.
char *tclistremove2(TCLIST *list, int
index);
         移除指定索引位置的字符串元素.
void tclistover(TCLIST *list, int index,
const void *ptr, int size);
         将ptr指定的值拷贝到列表的指定索引index上, size是指复制ptr是多少内容
void tclistover2(TCLIST *list, int index,
const char *ptr);
         将ptr指定的字符串拷贝到列表的指定索引index上.
void tclistsort(TCLIST *list);
         对指定的列表中元素进行排序, 大小比较策略由tclistelemcmp函数定义. 
int tclistlsearch(const TCLIST *list, const
void *ptr, int size);
         线性搜索(也就是逐个比较), 如果没有查找到返回-1, 否则返回索引.
int tclistbsearch(const TCLIST *list, const
void *ptr, int size);
         假定列表是已排序的, 使用bsearch库函数进行二分法查找. 同样是找到就返回索引, 找不到就返回-1.
void tclistclear(TCLIST *list);
         释放一个列表(会逐个释放列表中的每一个元素)
void *tclistdump(const TCLIST *list, int
*sp);
         将指定的列表对象序列化成一个字节数组, 返回字节数组指针, 并通过改写sp返回数组大小.
TCLIST *tclistload(const void *ptr, int
size);
         把一个字节数组加载成一个列表对象, size表明通过改字节数组的哪些元素去加载.
 
