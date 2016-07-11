---
layout: post
title: python shell脚本(主要讲管道操作的支持)
date: 2009-10-22 08:56:00
categories: [脚本, python, shell, subprocess, cmd, freebsd]
tags: []
---
这里提供的只是一个支持管道的命令执行接口, 至于获取命令, 扩展自己的命令, 就不再赘述.
 
对于系统的命令, 可以直接调用这个接口方法, 多个命令支持管道操作.  发生错误时, 引发OSError.
 
 
 
1. 判断传入命令是否是字符串类型
2. 传入的每个命令.
3. 遍历所有命令.
4. 获取每个命令的命令及参数
5. 动态执行Popen, 并将返回值放入列表popens中
6. 动态执行构建的Popen, 第一个只有stdin不使用管道, 最后一个stdout指定为sys.stdout. 其余的都是PIPE
7. 遍历取出Popen对象, 将前一个对象的stdout写入到后一个对象的stdin中.
 

```python
'''
Created on 2009-10-21

@author: selfimpr
@blog: http://blog.csdn.net/lgg201
@E-mail: goosman.lei@gmail.com
@function: 测试过FreeBSD下可以使用. 是一个小练习, 作用是将系统命令作为参数传入, 执行.  接受的参数支持管道操作, 管道操作符使用|.
'''

from sys import stdout
from subprocess import Popen, PIPE

def pipecmd(cmdstr):
    if isinstance(cmdstr, str): # estimate if the argument is string
        cmds = cmdstr.split('|') # split intact cmdstr to sigle command
        cmds = [cmd.strip() for cmd in cmds] # strip space character
        length = len(cmds)
        popens = []
        for index, cmd in enumerate(cmds): # each all the commands
            cmd_args = cmd.split(' ')
            cmd_args = [arg.strip() for arg in cmd_args]
            try:
                #################
                # get all the instance of Popen
                #################
                popens.append(eval('Popen(cmd_args%(stdin)s%(stdout)s)' % /
                               {'stdin': '' if index == 0 else ', stdin=PIPE', /
                                'stdout': ', stdout=stdout' if index == length - 1 else ', stdout=PIPE'}))
            except OSError, e:
                print 'arises os error'
        #################
        # process pipe
        #################
        prev = None
        for index, popenobj in enumerate(popens):
            if not prev:
                prev = popenobj
                continue
            popenobj.stdin.write(prev.stdout.read())
            prev = popenobj
```

 
如下图, 解释了ls /bin | more | grep ^m命令怎么样执行管道操作.
![](http://hi.csdn.net/attachment/200910/22/8670_1256173858K9Z1.jpg)
 
其实, 管道说来也很简单的, 和理解流是一样的, 前一个流的输出作为后一个流的输入而已.  在这个脚本中, 第一个命令执行时, 没有输入, 后面每个命令都会将前一个命令的输出作为输入....直到最后一个命令执行时, 我们将输出指向了标准输出, 所以执行的结果就和我们平时执行命令行命令一样.    
 
管道, 流......讲的更通俗一点, 那就是水流了嘛, 接水管的游戏相比大家都玩过, 那么复杂的管道都能接出来, 还怕I/O中那几个小小的管道??
