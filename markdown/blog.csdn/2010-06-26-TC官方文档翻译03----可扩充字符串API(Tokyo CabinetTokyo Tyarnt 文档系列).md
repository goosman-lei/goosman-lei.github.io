/**
 * 转载请注明出处, 由于个人技术能力有限, 英语水平欠缺,
 * 有翻译不合适或错误的地方, 请纠正,
 * 希望不要因为我的错误误导您, 希望您的智慧可以加入.
 * @translator: selfimpr
 * @mail: lgg860911@yahoo.com.cn
 * @blog: http://blog.csdn.net/lgg201
 */
 

可扩充字符串API
typedef struct {
         char
*ptr; //字符串指针
         int
size; //当前字符串占用大小
         int
assize; //字符串对象当前分配的内存大小
} TCXSTR;
TCXSTR *tcxstrnew(void);
         创建一个可扩充字符串.
         函数首先分配一个TCXSTR结构的内存, 然后为其内部的ptr分配初始的TCXSTRUNIT(默认12)大小内存, 设置size为0, assize为TCXSTRUNIT, 将ptr[0]设置为/0(也就是设置为空字符串)
TCXSTR *tcxstrnew2(const char *str);
         指定一个字符串作为初始值创建一个可扩充字符串对象
         返回的可扩充字符串对象是按照str量身定做, 但是如果str长度加1小于TCXSRUNIT时, 该字符串的分配内存会是TCXSTRUNIT.
TCXSTR *tcxstrnew3(int asiz);
         指定一个初始内存大小来创建一个可扩充字符串对象.
与tcxstrnew相似, 不过在为ptr分配内存时, 按照指定的asiz而不是TCXSTRUNIT
TCXSTR *tcxstrdup(const TCXSTR *xstr);
         复制指定的可扩充字符串, 但是, 新的可扩充字符串中的asize是取xstr的size+1和TCXSTRUNIT两者的大值.
void tcxstrdel(TCXSTR *xstr);
         释放指定的可扩充字符串对象.
void tcxstrcat(TCXSTR *xstr, const void
*ptr, int size);
         该函数用来连接字符串, 将ptr的size个字符连接到xstr字符串末尾
         函数中首先计算新字符串的长度, 然后对xstr的asize进行扩充(每次2倍)知道其大小适合新字符串存储, 重新分配内存, 内存拷贝, 然后修改xstr的size, 为字符串追加字符串结束符.
void tcxstrcat2(TCXSTR *xstr, const char
*str);
         与tcxstrcat类似, 不过是将str全部连接到xstr上.
const void *tcxstrptr(const TCXSTR *xstr);
         返回可变字符串中存储的字符串的值, 可以直接作为字符串使用.
int tcxstrsize(const TCXSTR *xstr);
         返回当前该可扩充字符串对象中实际存储的字符串的大小
void tcxstrclear(TCXSTR *xstr);
         将指定的可扩充字符串对象中已经存储的字符串设置为空字符串(直接将该指针指向/0), 并将xstr->size设置为0
char *tcxstrprintf(TCXSTR *xstr, const
char*format, …);
         格式化一个可扩充字符串对象
返回的指针可以用free释放.
 
