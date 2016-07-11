 
author: selfimpr
blog:[http://blog.csdn.net/lgg201](http://blog.csdn.net/lgg201)
mail:[lgg860911@yahoo.com.cn](mailto:lgg860911@yahoo.com.cn)
 
四种语法的差异
在PHP中,包含一个文件有4种方式:require,require_once,include,include_once.其中require_once/include_once与require/include相比,在功能上,仅仅是增加了一个是否已经加载过的检测,require_once/include_once在一次PHP执行过程中,保证一个文件只被加载一次.
require_once/include_once这样只加载一次的功能,通常是为了避免发生函数/类重定义等异常,或者多次包含同一文件导致的变量覆写.
在说明了_once版本和没有_once的版本之间的区别后,我们同样需要知道require和include之间的区别.下面是php手册对require的解释:
require() is identical to include() except upon failure it will also produce a fatal E_ERROR level error. In other words, it will halt the script whereas include() only emits a warning (E_WARNING) which allows the script to continue.
可以看出,require和include之间的区别仅在于发生错误时(比如被包含文件查找不到),require引发一个E_ERROR级别的错误,而include引发一个E_WARNING级别的错误.(E_ERROR级别的错误会中断脚本执行)
require_once和include_once之间的区别也就不言而喻了.
 
一个需要注意的点
require/require_once/include/include_once都是语法结构,不是函数,可以通过function_exists验证
 
性能问题
require/require_once的性能问题,在[http://blog.csdn.net/lgg201/archive/2011/02/14/6184745.aspx](http://blog.csdn.net/lgg201/archive/2011/02/14/6184745.aspx)中已经做了比较详细的阐述,这里仅仅列举一个用以发现性能问题的示例:
main.php

```php
&lt;?php
/**
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: lgg860911@yahoo.com.cn
 * require/require_once性能测试
 */
/**
 * 测试require
 * @param unknown_type $filename
 */
function test_require($filename) {
        isset($GLOBALS[$filename]) or (($GLOBALS[$filename] = 1) and require $filename);    
}
/**
 * 测试require_once
 * @param unknown_type $filename
 */
function test_require_once($filename) {
        require_once($filename);
}
/**
 * cpu时间缓存
 */
$cpu_time_tmp = array();
/**
 * 记录开始cpu时间
 */
function cputime_start() {
        global $cpu_time_tmp;
        $rusage = getrusage();
        $cpu_time_tmp[] = $rusage['ru_utime.tv_sec'] + $rusage['ru_utime.tv_usec'] / 1000000;
        $cpu_time_tmp[] = $rusage['ru_stime.tv_sec'] + $rusage['ru_stime.tv_usec'] / 1000000;
}
/**
 * 输出运行cpu消耗
 */
function cputime_end() {
        global $cpu_time_tmp;
        $rusage = getrusage();
        printf("user_cpu: %.8f, system_cpu: %.8f&lt;br /&gt;/n", $rusage['ru_utime.tv_sec'] + $rusage['ru_utime.tv_usec'] / 1000000 - $cpu_time_tmp[0], $rusage['ru_stime.tv_sec'] + $rusage['ru_stime.tv_usec'] / 1000000 - $cpu_time_tmp[1]);
        $cpu_time_tmp = array();
}
$times = 1000000;
print "&lt;h1&gt;测试用例运行次数: $times&lt;/h1&gt;";
print "&lt;b&gt;&lt;font color='blue'&gt;require方式消耗: &lt;/font&gt;&lt;/b&gt;";
cputime_start();
while($i ++ &lt; $times) test_require("required_require.php");
cputime_end();
print "&lt;b&gt;&lt;font color='blue'&gt;require_once方式消耗: &lt;/font&gt;&lt;/b&gt;";
cputime_start();
while($j ++ &lt; $times) test_require_once("required_requireonce.php");
cputime_end();
?&gt;
&lt;meta http-equiv="Content-Type: text/html; charset=utf-8" /&gt;
```
 
 
required_require.php

```php
&lt;?php
class T1{}
?&gt;
```
 
 
required_require_once.php

```php
&lt;?php
class T2{}
?&gt;
```
 
 
输出结果:
测试用例运行次数: 1000000
require方式消耗: user_cpu: 0.56000000, system_cpu: 0.01000000
require_once方式消耗: user_cpu: 2.84000000, system_cpu: 1.05000000
多次运行,结果稳定. 可以看出require_once与require相比,系统cpu消耗是其105倍,用户cpu消耗约为5倍左右, 总体cpu时间消耗是其8倍左右.
 
自动加载
PHP引入了__autoload机制, 如果脚本解释执行过程中定义了__autoload函数, 那么当需要类而类未定义的时候, 就会将类名作为参数, 调用__autoload函数, __autoload由用户程序员自己来实现一个文件的加载机制.
在使用__autoload机制时,我们需要确保文件层次结构,命名有严格的规范.
自动加载的应用场景:
当我们需要一个动态的加载机制的时候,比如使用可变类名的情况(new $classname),在这种情形下,我们为了避免一次去加载所有可能的类定义文件,通常会有一个规则去require类定义文件,那我们可以把这个require放入到__autoload中,对其做一个统一的规范.
 
spl自动加载
__autoload解决了类的自动加载问题,但是,当系统比较大的时候,可能一个autoload并不能完全的(或难度较大)规范自动加载规则(特别是在与其他系统协同工作时).那我们首先想到的就是提供多个autoload功能的函数来解决这个问题,spl自动加载就是解决这个问题的.
通过函数spl_autoload_register($autoload_callback, $throw, $prepend)/spl_autoload_unregister来维护一个autoload函数的栈, 三个参数都是可选的, $autoload_callback是要加入的函数名, $throw指函数注册过程发生错误(比如提供的函数名不存在)时是否抛出异常, $prepend用来指明是否是向autoload栈的前面插入.
 
总结
关于"包含文件",能够想到的就这么多,在这里做一个小的总结:
1. 尽可能自定义一个require_once函数来管理这个once的加载机制, 这样做不仅有性能上的提升,而且同时对"包含文件"进行了一个统一的管理
2. 对于利用面向对象特性较多的系统,应尝试使用autoload机制, 当系统比较复杂时, 可以使用spl_autoload_xxx对autoload进行管理
3. 对于非文件域需要的(比如类的继承等)"包含文件", 尽可能的把require/include放入到函数等具体的处理过程中,在真正需要的时候条件包含.
比如:
common.func.php

```php
&lt;?php
function ud_require_once($filename) {
isset($GLOBALS[$filename]) or (($GLOBALS[$filename] = 1) and require $filename);
}
?&gt;
```
 
 
user.func.php

```php
&lt;?php
function f() {
}
?&gt;
```
 
 
A.class.php

```c-sharp
&lt;?php
class A {
}
?&gt;
```
 
 
B.class.php

```c-sharp
&lt;?php
//common.func.php和A.class.php是当前文件B.class.php无论如何都需要的,所以在文件头部进行包含
function_exists('ud_require_once') or require 'common.func.php';
ud_require_once("A.class.php");
class B extends A {
	public function test() {
		//user.func.php只有在此函数调用时才需要,所以在这里进行require
		ud_require_once("user.func.php");
		f();
	}
}
?&gt;
```
 
 
4. 这一点实际是一个老生长谈的问题, 文件结构和命名的规范...无论你是否使用autoload, 无论你是否使用自定义require_once, 无论你是否使用可变类名(new $classname()方式), 都需要遵循一个统一的文件结构和命名规范, 一旦这个规范确立了, 无论这个规范有多烂, 都要遵循它, 当然不是不能更改, 但是更改需要有一个统一的规划, 并且这种对规范的修改带来的对旧有代码的改动工作, 最好开发工具进行修改.
5. 与第四点同样重要的问题, 就是代码长度的问题, PHP是解释型语言, 多一个无用的东西, 每次执行就都会多一些消耗, 哪怕那只是一个函数定义....所以, 要尽可能的让自己的php文件功能单一, 让每次require都高效的包含所需文件
 
关于php的文件包含, 我只有这么多的认识, 希望有更多/更好这方面认知的朋友能够不吝赐教/拍砖
 
补记:
感谢[http://topic.csdn.net/u/20110224/14/ec28b643-2508-467f-9ed9-500f2208121a.html](http://topic.csdn.net/u/20110224/14/ec28b643-2508-467f-9ed9-500f2208121a.html)中7楼BooJS提供的文档和数据, 这里提出了包含文件时绝对路径和相对路径的问题.
PHP文档中提到:
include/require, 如果给定路径是已定义的(绝对/相对), include_path将会被忽略, 比如../, 而对于既不是绝对也不是相对路径的, PHP顺次查找include_path中配置的路径来寻找文件.
 
BooJS提出使用压力测试工具进行外部测试, 以下是测试代码及结果
测试工具: apache ab
压力: 10000请求, 150并发
服务器环境: nginx + php-fpm, cpu: AMD Athlon(tm) Dual Core Processor 4450B双核, 3.5G内存
代码: 四个文件的代码, 都比较简单, 所以放一起发出来

```php
&lt;?php
while($i ++ &lt; 100)
isset($GLOBALS["required_require.php"]) or require "required_require.php";
?&gt;
&lt;?php
while($i ++ &lt; 100)
require_once "required_require.php";
?&gt;
&lt;?php
while($i ++ &lt; 100)
isset($GLOBALS["required_require.php"]) or require "/media/development/workspace/php/php-test-require/required_require.php";
?&gt;
&lt;?php
while($i ++ &lt; 100)
require_once "/media/development/workspace/php/php-test-require/required_require.php";
?&gt;

```
 
 
测试结果:
相对路径/自定义require_once
  50% 95
  66% 101
  75% 107
  80% 110
  90% 124
  95% 128
  98% 134
  99% 161
 100% 8877 (longest request)

相对路径/系统require_once
  50% 151
  66% 154
  75% 159
  80% 162
  90% 177
  95% 185
  98% 196
  99% 225
 100% 11696 (longest request)

绝对路径/自定义require_once
  50% 98
  66% 103
  75% 107
  80% 112
  90% 122
  95% 127
  98% 139
  99% 152
 100% 7988 (longest request)

绝对路径/系统require_once
  50% 124
  66% 128
  75% 132
  80% 136
  90% 152
  95% 159
  98% 171
  99% 185
 100% 11695 (longest request)


我们取99%的响应时间分析:
相对路径ud_require_once: 161
相对路径require_once: 225
绝对路径ud_require_once: 152
绝对路径require_once: 185

首先, 我们可以确信的是绝对路径方式比相对路径要快, 上面的数据从这一方面来说是可信的(文件内只有一次require_once的, 经过测试, 数据不足以致信).

那么从整体的响应时间来看, 我们仍然可以看出, 系统的require_once是有一定劣势的....当然, 这里扯出来的是上面我没有提到的一个问题, 就是绝对路径的require优于相对路径require.

其实, 在做这个测试之前, 我们可以通过我提到的我的测试结果来推测到这个结论:
测试用例运行次数: 1000000
require方式消耗: user_cpu: 0.56000000, system_cpu: 0.01000000
require_once方式消耗: user_cpu: 2.84000000, system_cpu: 1.05000000
这里我们得到的是CPU消耗, 很明显可以看到系统提供的require_once较之自定义的, cpu消耗要高不少, 所以, 服务器整体压力是必然会上去的.
 
 
