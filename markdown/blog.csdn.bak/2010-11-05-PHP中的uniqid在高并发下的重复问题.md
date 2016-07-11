---
layout: post
title: PHP中的uniqid在高并发下的重复问题
date: 2010-11-05 14:27:00
categories: [php, function, 测试, file, user, web]
tags: []
---
项目是一个高并发的web项目，并且会有后台进程（pcntl并发），两者都会利用uniqid去生成唯一id，今天发现一个bug，在高并发情况下，uniqid可能产生重复输出。
 
以下是测试代码：

```php
&lt;?php
function new_child($func_name) {
    $args = func_get_args();
    unset($args[0]);
    $pid = pcntl_fork();
    if($pid == 0) {
        function_exists($func_name) and exit(call_user_func_array($func_name, $args)) or exit(-1);
    } else if($pid == -1) {
        echo &quot;Couldn&rsquo;t create child process.&quot;;
    } else {
        return $pid;
    }   
}
function generate() {
    $t = array();
    while($i ++ &lt; 10) {
        $uid = uniqid(true).&quot;/n&quot;;
        array_push($t, $uid);
    }   
    sort($t);
    while(-- $i &gt;=0) {
        echo array_shift($t);
    }   
}
while($i ++ &lt; 1000) {
    new_child(generate);
}
?&gt;

```

 
测试方法： 命令行运行此程序，重定向输出到文件，然后利用下面程序检查重复：

```c-sharp
&lt;?php
$f = file(&quot;tttttt&quot;);
$f = array_count_values($f);
foreach($f as $k =&gt; $c) if($c &gt; 1) echo $c.'_'.$k;
?&gt;

```

 
解决方法： 我们现在是在uniqid后又加了rand(1, 10000)，在1000并发，每进程10次uniqid的情况下，再没有产生重复。
