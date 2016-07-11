---
layout: post
title: PHP中require PK require_once, 构建自己的高效require_once(10倍的性能提升)
date: 2011-02-14 20:36:00
categories: [php, 测试, function, 优化, class, 网络]
tags: []
---
author: selfimpr
blog:[http://blog.csdn.net/lgg201](http://blog.csdn.net/lgg201)
mail: goosman.lei@gmail.com
 
经过测试, require_once是一个性能低下的语法结构, 当然, 这个性能低下是相对于require而言的, 本文阐述我们项目目前使用的require方式, 通过实验代码证明其高效性, 同时, 描述我们在使用过程中遇到的问题, 避免他人在同一个石头上绊倒....
如果有更好的建议和本文有不正确观点, 还望指正, 谢谢.
 
require: 引入一个文件, 运行时编译引入.
require_once: 功能等同于require, 只是当这个文件被引用过后, 不再编译引入.
 
上面就是两者的区别. 可以看出, 两者的不同仅在于require_once有一个判断是否已经引用过的机制...
 
通过网络搜索, 可以看到很多关于require_once性能比require低很多的数据, 这里就不再做这个试验.
 
我们项目中的做法是: 在每个文件起始位置定义一个全局变量, require的时候, 使用isset($xxxxxx) or require 'xxxxx.php';
这种做法有什么不足呢?
全局变量以$xxx方式定义的时候, 如果该文件再函数内被require, 该变量会被解析为函数的局部变量, 而不是全局的, 因此, 函数内部的isset($xxx) or require 'xxx.php'这个语法结构会失效, 带来的结果当然是意料不到的, 比如, 类的重定义, 方法的重定义等等.....
      前车之鉴, 所以, 全局变量的定义, 请使用$GLOBALS['xxx'], require的时候, 使用isset($GLOBALS['xxx']) or require 'xxx.php';, 使用GLOBALS会比直接定义稍慢, 但总比错是要好很多的...
 
由于我们之前的全局变量是直接定义的, 今天在和同事讨论的过程中, 想到另外一种写法:
定义的位置仍然使用$xxx方式直接定义, require的方法中进行修改(文件头部定义的全局变量和文件名是有关联的)
function ud_require($xxx) {
    global $$xxx;
    isset($$xxx) or require $xxx . '.php';
}
这种方式使用了动态变量, 经过和直接的GLOBALS方式比较, 有两个显著缺点:
1. 性能, 由于动态变量的引入, 比GLOBALS方式慢2倍左右
2. 无法解决间接引用问题, 因为我们无法预知被间接引用的文件名, 也就无法用global去声明那些被间接引用的文件中定义的标记性全局变量了.
 
好了....下面是我对GLOBALS方式的require和require_once的测试:
测试入口文件:
require_requireonce.php

```php
&lt;?php
function test1($filename) {
	//pathinfo($filename);
	isset($filename) or require $filename;
}
function test2() {
	require_once 'require_requireonce_requireonce.php';
}
$start = microtime(true);
while($i ++ &lt; 1000000) isset($GLOBALS['require_requireonce_require.php']) or require 'require_requireonce_require.php';
$end = microtime(true);
echo "不使用方法的isset or require方式: " . ($end - $start) . "&lt;br /&gt;/n";
$start = microtime(true);
while($j ++ &lt; 1000000) test1('require_requireonce_require.php');
$end = microtime(true);
echo "使用方法的isset or require方式: " . ($end - $start) . "&lt;br /&gt;/n";
$start = microtime(true);
while($k ++ &lt; 1000000) test2();
$end = microtime(true);
echo "require_once方式: " . ($end - $start) . "&lt;br /&gt;/n";
?&gt;
&lt;meta http-equiv="Content-Type: text/html; charset=utf-8" /&gt;
```
 
require_requireonce_require.php     (用于测试require的被引入文件)

```php
&lt;?php
$GLOBALS['require_requireonce_require.php'] = 1;
class T1 {}
?&gt;
```
 
require_requireonce_requireonce.php    (用于测试require_once的被引入文件)

```php
&lt;?php
class T2 {}
?&gt;

```
 
 
下面是测试的结果(单位: 秒):
不使用方法的isset or require方式: 0.22953701019287
使用方法的isset or require方式: 0.23866105079651
require_once方式: 2.3119640350342
 
可以看出, 不套一个方法的require速度是比使用方法的略快的, 两者速度都是require_once的10倍左右...
 
那么, 性能损耗究竟在哪里呢?
上面require_requireone.php文件中的test1方法中, 我注释了一句pathinfo($filename), 因为, 我本来意图是使用文件名不带后缀作为标记性的全局变量名的, 但是, 当我使用pathinfo之后, 我发现这种方式的性能消耗和require_once基本一致了......因此, 我在那里单独的加了一个pathinfo的调用, 又做了测试, 果然是pathinfo在捣鬼.......所以, 后面我就修改为了现在的版本, 直接使用文件名作为变量名, 如果你害怕文件名重复, 那不妨加上路径名...
 
猜测: 加上pathinfo之后, require和require_once的性能消耗基本一致, 那我们是否可以猜测PHP内部对require_once的处理是基于它的呢? 据说PHP5.3中对require_once做了显著的优化, 但是, 我测试过程中使用的是PHP5.3.5版本, 仍然能够看到和require明显的差距, 难道只是比之前版本较大优化? 这个倒还没有测试....
 
本文写完后, 我尝试把test1方法做了如下修改
isset($GLOBALS[substr($filename, 0, strlen($filename) - 4)]) or require $filename;
使用手动的字符串截取, 当然, 截取是要耗时的, 不过比pathinfo的版本是要好一点的. 这次的测试结果是:
不使用方法的isset or require方式: 0.21035599708557
使用方法的isset or require方式: 0.92985796928406
require_once方式: 2.3799331188202
 
好了, 不再说废话了, 结论:
对于require_once修改为isset or require方式, 需要注意以下几方面:
1. 每个文件头部定义唯一的一个标记性变量, 使用$GLOBALS['XXX'] = 1;的方式定义, 并且, 建议变量名是文件名或带路径的文件名(如果单独的文件名会重复)
2. 定义一个自定义require方法:
function ud_require_once($filename) {
    isset($GLOBALS[$filename]) or require $filename;
}
 
完, 谢谢.
