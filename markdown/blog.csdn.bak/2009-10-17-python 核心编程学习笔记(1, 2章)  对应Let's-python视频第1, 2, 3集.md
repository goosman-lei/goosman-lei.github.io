---
layout: post
title: python 核心编程学习笔记(1, 2章)  对应Let's-python视频第1, 2, 3集
date: 2009-10-17 20:06:00
categories: [python, 编程, list, class, input, 文档]
tags: []
---
#Let's python 系列视频对应学习笔记, 笔记是我自己学习过程中的记录, 里面有很多讲的不清楚的地方, 很多都是只能我自己看懂的, 关于笔记, 我会和视频同步发布.
#顺便提下个人观点: 笔记只能帮助我们重新温习一遍刚才学过的内容, 有人说笔记可以在未来用于查询, 我倒是觉得API文档更详尽, 更准确. 真正理解了思想之后, 笔记或许整年都用不上一次.
#
#基本配置及基础语法(1, 2章)
1 Python.exe 的解释器options: 
1.1 –d   提供调试输出
1.2 –O   生成优化的字节码(生成.pyo文件)
1.3 –S   不导入site模块以在启动时查找python路径
1.4 –v   冗余输出(导入语句详细追踪)
1.5 –m mod 将一个模块以脚本形式运行
1.6 –Q opt 除法选项(参阅文档)
1.7 –c cmd 运行以命令行字符串心事提交的python脚本
1.8 file   以给定的文件运行python脚本
2 _在解释器中表示最后一个表达式的值.
3 print支持类c的printf格式化输出: print “%s is number %d!” % (“python”, 1)
4 print的输入内容后面加逗号, 就会使其输入不换行
5 把输出重定向到日志文件: 
logfile = open(“c:/1.log”, “a”);   //打开文件c:/1.log使用a模式..即add, 添加.
print >> logfile, “Fatal error: invalid input!”;   >>为重定向..将print的结果重定向到logfile, 输出内容是”Fatal error: invalid input!”…
logfile.close();  //关闭文件流…
6 程序输入: raw_input(“提示字符串”): user = raw_input(“请输入您的姓名”);
7 int(数值)…..将数值字符串转换成整数值…
8 运算符:
8.1 + - * / %是和其他语言相同的加减乘及取模运算.取余运算
8.2 / 在浮点取模中得到的结果是完整的浮点数
8.3 // 在浮点取模中得到的结果是经过舍去运算的结果.
8.4 ** 是乘方
8.5 >>和<<的移位运算也支持. 但不支持java中的>>> 和<<< 移位.
8.6 < <= > >= ++ != <> 等比较运算符
8.7 and or not 等逻辑运算符
9 变量和赋值: python是弱类型语言..
10 list, tuple, map * 4 得到的结果是一个新的 list | tuple | map, 是原数据的4份
11 数字: 
11.1 int(有符号整数)
11.2 long(长整数)
11.3 bool(布尔值)
11.4 float(浮点值)
11.5 complex(复数)
11.6 python2.3开始, 如果结果从int溢出, 会自动转型为long
11.7 python2.4开始支持decimal数字类型, 需要导入decimal模块..由于在二进制表示中会有一个无限循环片段, 普通的浮点1.1实际是不能被精确表示的, 被表示为1.1000000000000001. 使用print decimal.Decimal(‘1.1’);则可以得到精确的1.1
12 字符串:  引号之间的字符集合, 支持使用成对的单引号和双引号, 三引号(三个连续单引号或双引号)可以用来包含特殊字符.  使用索引运算符[]和切片运算符[ : ]可以得到子字符串…字符串中第一个字符的索引是0, 最后一个字符的索引是-1;
13 列表和元组: 可以看作是普通的数组, 能保存任意数量任意类型的python对象…
13.1 列表元素用中括号包裹, 元素的个数及元素的值可以改变.
13.2 元组元素用小括号包裹, 不可以更改, 尽管他们的内容可以, 元组可以看成是只读的列表.  可以使用切片运算得到子集.
14 字典: 相当于其他语言中的map, 使用{key: value}的方式表示. 取值的方式和其他语言的map一致.  也可以直接使用map[key] = value的方式为其赋值.
15 条件语句: 
if expression: 
       path 1
elif expression2:
       path2
else:
       path3
16 while循环
while expression:
       process business
17 for循环
for item in list|tuple|map:
       print item
17.1 range(len(list))得到一个list长度范围内的整数list, 方便遍历过程中获取索引值.
17.2 python2.3中增加了enumerate(), 可以通过它遍历list, 同时得到索引和值
for index, data in enumerate(list):
       print index, “:”, data, 
17.3 列表解析: sqdEvens = [x ** 2 for x in range(8) if not x % 2], 获取一个序列, 该序列是0-8的数字中所有x%2为0(false)的x的平方
18 文件和内建函数: open(), file()
18.1 handle = open(file_name, access_mode = “r”), 只读方式打开文件, 得到的句柄是handle..该方法如果没有提供access_mode, 默认是r
19 异常处理: raise可以故意引发异常
try: 
       # process
except IOError, e:
       # error process
20 函数: 如果函数中没有return语句, 自动返回None对象
def function_name([arguments]):
       “optional document string”
       function_suite
20.1 python的函数调用中参数是引用传递
20.2 可以在定义函数的时候, 在参数列表中通过=设置参数的默认值.
21 类: 
21.1 定义:
class class_name:
       static_variable_name = value
       def __init__(self, [arguments]):
              //operation
              //self in here is the reference for this class instance
       def general_method_name(self, [arguments]):
              //operation
              //self is the class instance
              //if you want to use class variable, please use like self.__class__.__name__
21.2 实例化: instance = class_name([arguments, …]);
22 模块: 不带.py后缀名的文件名…一个模块创建之后, 可以使用import导入这个模块使用.
22.1 访问模块内的函数或变量: module_name.function() | module_name.variable | module_name.class_name
22.2 sys模块概览
22.2.1 sys.stdout.write(‘Hello World!/n’)  //使用sys模块的标准输出
22.2.2 sys.platform  //返回系统的标记
22.2.3 sys.version  //返回系统的版本
23 PEP: 一个PEP就是一个python增强提案(python enhancement proposal), 是在新版python中增加新特性的方式…索引网址是: [http://python.org/dev/peps](http://python.org/dev/peps)
24 一些常用函数
24.1 dir([obj])  显示对象的属性, 如果没有提供参数, 显示全局变量的名字
24.2 help([obj])  显示对象的文档, 如果没有参数, 进入交互式帮助
24.3 int(obj)  将一个对象转换为整数
24.4 len(obj)  返回对象的长度
24.5 open(file_name, mode)  以mode(r|w|a…)方式打开一个文件
24.6 range([[start, ]stop[, step]])  返回一个整数列表…结束值是stop-1, step默认是1
24.7 raw_input(str)  提示str等待用户输入
24.8 str(obj)  将一个对象转换为字符串
24.9 type(obj)  返回对象的类型…返回值本身是一个type对象
24.10 sum(iterable[, start=0])  可以对纯数值的list|tuple|map进行求和操作..
24.11 dir([object])  如果没有参数获得当前脚本scope内定义的对象, 如果有参数, 返回该对象内部定义的对象, 如果该对象有一个__dir__方法, 该方法将被调用, 并且必须返回属性的列表…这就允许通过自定义__getattr__()或__getattribute__()方法的方式实现dir的自定义显示属性列表….如果没有指定参数, 是根据该对象的__dict__内存字典的最佳聚合信息显示的..
24.12 type([object])  参数为空显示<type ‘type’>, 参数不为空显示该对象的类型
24.13 type(name, bases, dict)  通过名称, 基类, 内存字典动态创建一个类型
24.14 object__name.__doc__  查看该对象的文档字符串
24.15 __doc__ 对象的文档字符串, 该文档字符串在定义对象时写在对象语句块中第一句, 使用单纯的字符串的方式表示
24.16 sys.exit()  退出python解释器
24.17 append(Object)  给list添加一个元素
24.18 os.linesep 返回的是系统换行符…不同的系统换行符是不同的, 使用linesep可以提高代码跨平台性
24.19 string_variable_name.strip([chars])  脱离, 滤去字符串中的某些字符, 如果没有参数返回原字符串
25 数值按进制分为:
25.1 二进制: 0b101010
25.2 八进制: 07167
25.3 十进制: 98767
25.4 十六进制: 0xf2134
 
