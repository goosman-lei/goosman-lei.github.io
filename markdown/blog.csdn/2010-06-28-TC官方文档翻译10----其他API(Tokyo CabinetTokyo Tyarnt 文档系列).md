/**
 * 转载请注明出处, 由于个人技术能力有限, 英语水平欠缺,
 * 有翻译不合适或错误的地方, 请纠正,
 * 希望不要因为我的错误误导您, 希望您的智慧可以加入.
 * @translator: selfimpr
 * @mail: goosman.lei@gmail.com
 * @blog: http://blog.csdn.net/lgg201
 */
 

一些混杂的工具API
long tclmax(long a, long b);
         返回a和b中的大值
long tclmin(long a, long b);
         返回a和b中的小值
unsigned long tclrand();
         返回一个无符号长整型的随机数. 内部是通过/dev/urandom生成随机数然后与时间戳进行计算(没看懂, 有懂的朋友有空的话发邮件goosman.lei@gmail.com讲讲, 学习学习, 谢谢).
double tcdrand();
         生成double型的随机数.
int tcstricmp(const char *astr, const char
*bstr);
         大小写不敏感的比较两个字符串, a>b返回正数, 小于返回负数, 相等返回0
bool tcstrfwm(const char *str, const char
*key);
         如果str以key开始则返回true
bool tcstrifwm(const char *str, const char
*key);
         tcstrfwm的大小写不敏感版本
bool tcstrbwm(const char *str, const char
*key);
         检查str是否以key结尾
bool tcstribwm(const char *str, const char
*key);
         tcstrbwm的大小写不敏感版本
int tcstrdist(const char *astr, const char
*bstr);
         计算两个字符串的编辑距离(实际上就是两个字符串的相似度)
         算法是通过一个数组模拟矩阵实现的. 如果需要更详细了解该算法, 可以搜(Levenshtein Distance (LD, 来文史特*距离*))相关资料.
int tcstrdistutf(const char *astr, const
char *bstr);
         计算UTF-8编码的字符串的编辑距离
char *tcstrtoupper(char *str);
         将字符串转换成大写
char *tcstrtolower(char *str);
         将字符串转换成小写
char *tcstrtrim(char *str);
         截掉字符串两端的非打印字符
char *tcstrsqzspc(char *str);
         去掉字符串两端非打印字符, 并且使字符串内部每个单词中间也最多有一个.
char *tcstrsubchr(char *str, const char
*rstr, const char *sstr);
将str中所有的在rstr中给出的字符按顺序替换为sstr中对应位置的字符. 实际上就是一个字符表的替换, 比如tctrsubchar(“hello
world”, “wo”, “-*”)的返回结果就会是”hell* -*rld”,当sstr的长度比rstr小时, 实际上会导致rstr这个字符表中后面的部分字符在sstr中找不到对照, 发生这种情况时, 会把str中所有匹配的字符都移除掉.
int tcstrcntutf(const char *str);
         统计给定的字符串以utf-8编码的字符数
char *tcstrcututf(char *str, int num);
         从str按照utf-8编码方式截取num个字符, 该函数会对传入的str发生效应(改变了它)
void tcstrutftoucs(const char *str, uint16_t
*ary, int *np);
         将给定的utf-8编码的str字符串以UCS-2码转入ary数组中, np记录数组大小
int tcstrucstoutf(const uint16_t *ary, int
num, char *str);
         将给定的UCS-2数组转换成一个utf-8字符串放入str, 返回值是该字符串长度, 参数num指定ary的大小
TCLIST *tcstrsplit(const char *str, const
char *delims);
         将给定的字符串str用delims作为间隔符分割成一个列表对象
char *tcstrjoin(const TCLIST *list, char
delim);
         将给定的列表对象用给定的间隔符delim连接成一个字符串
int64_t tcatoi(const char *str);
         把给定的字符串str转换成一个数值, 与javascript中的parseInt功能类似. 如果不是有效的整数表达式, 将返回0
double tcatof(const char *str);
         tcatoi的double版本如果不是有效的浮点表达式, 将返回0.0
bool tcregexmatch(const char *str, const
char *regex);
         正则表达式匹配, 如果正则以*开头, 则表明是一个大小写不敏感的正则, 匹配成功返回true, 否则返回false;
char *tcregexreplace(const char *str, const
char *regex, const char *alt);
         将str中所有匹配正则regex的子串都替换成alt, 在alt中可以使用&代表整个匹配子串, 用/1----/9代表匹配子组, 关于正则匹配的语法, 可以详细查阅正则表达式相关内容, alt中的上述语法为后引用. 如果正则regex在str中没有匹配, 将返回str的一个拷贝.
void tcmd5hash(const void *ptr, int size,
char *buf);
         对指定的序列化对象获取一个MD5的hash值, 放入buf中, buf需要48bytes或更大的内存空间.
void tcarccipher(const void *ptr, int size,
const void *kbuf, int ksiz, void *obuf);
         用ARC4加密算法对给定的序列化对象加密或解密, 结果输出到obuf中, obuf需要提供与ptr相同或更大的内存空间.
double tctime();
         获取当前时间戳, 精确到微秒
void tccalendar(int64_t t, int jl, int
*yearp, int *monp, int *dayp, int *hourp, int *minp, int *secp);
获取指定时间的格林威治日历, 参数t为时间戳, jl为时差, 后面每个参数是计算后用来存储结果的指针, 分别是年, 月, 日, 时, 分, 秒
void tcdatestrwww(int64_t t, int jl, char
*buf);
         根据时间戳t和时差jl获取一个W3CDTF时间格式串放入buf中, buf需要48bytes或更大的内存(W3CDTF格式: "YYYY-MM-DDThh:mm:ddTZD")
void tcdatestrhttp(int64_t t, int jl, char
*buf);
         根据时间戳t和时差jl获取一个RFC 1123规范的时间格式串放入buf中, buf需要48bytes或更大内存(RFC 1123时间格式: "Wdy,
DD-Mon-YYYY hh:mm:dd TZD")
int64_t tcstrmktime(const char *str);
         通过给定字符串获取一个时间戳, 该字符串可以是十进制, 十六进制, W3CDTF, RFC822(1123)等格式, 十进制的时候, 可以通过增加后缀表明单位, s表示秒, m表示分, h表示小时, d表示天.
int tcjetlag();
         用来获取本地时间的时差
int tcdayofweek(int year, int mon, int
day);
         指定年, 月, 日, 返回当前是星期几, 0代表星期天, 6代表星期六
 
