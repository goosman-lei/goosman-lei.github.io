---
layout: post
title: Ubuntu中利用Grub 2修复移动硬盘引导
date: 2011-03-01 09:35:00
categories: [ubuntu, 工具, 2010, os]
tags: []
---
grub 2的安装需要安装以下包：
        grub-pc, grub-common
可以在包管理工具中发现。
 
grub-install -v来查看当前版本， 传统的grub版本号为0.97及以下， grub2的版本号是1.96或更高。
 
grub 2的配置文件是/boot/grub/grub.cfg， 但是这个文件是不允许修改的，我们要通过对/etc/grub.d/下的文件以及/etc/default/grub文件进行编辑，使用update-grub来生成配置文件。
 
也就是说， 一个正常的流程应该是：
sudo apt-get install grub-pc, grub-common    #如果已经安装了grub2则不需要
sudo grub-install --root-directory=/ /dev/sda    #在设备上安装grub2引导
sudo update-grub  #生成grub配置文件
 
现在的问题就在update-grub，因为grub-install是可以指定设备来进行安装的， 但是生成配置文件的update-grub是不能指定的。
whereis update-grub
查找到它在/usr/sbin/目录下， 编辑发现它是对grub-mkconfig做的一个快捷方式，代码如下：

```cpp
#!/bin/sh
set -e
exec grub-mkconfig -o /boot/grub/grub.cfg "$@"
```
 
那我们就继续来查看grub-mkconfig的代码（下面仅截取前面的关键目录定义部分）

```cpp
prefix=/usr
exec_prefix=${prefix}
sbindir=${exec_prefix}/sbin
libdir=${exec_prefix}/lib
sysconfdir=/etc
PACKAGE_NAME=GRUB
PACKAGE_VERSION=1.98+20100804-5ubuntu3
host_os=linux-gnu
datarootdir=${prefix}/share
datadir=${datarootdir}
pkgdatadir=${datadir}/`echo grub | sed "${transform}"`
grub_cfg=""
grub_mkconfig_dir=${sysconfdir}/grub.d

```
 
可以看出在这里它定义了路径，其中sysconfdir指定了系统配置文件路径，经过简单核对后面代码，它就是使用这个目录下的grub.d/*和default/grub来生成配置文件的。。。
那么，我们就可以对sysconfdir进行一个修改， 让它指向要修复的移动硬盘的etc目录下，然后运行
sudo grub-mkconfig -o /自定义路径/grub.cfg
这样就会在“/自定义路径/”下生成了grub.cfg
 
当然，此时还是有问题的， 因为现在查找到的系统是将目前操作系统所在硬盘作为主硬盘扫描的，所以，对grub.cfg中操作系统设置的部分进行一个照猫画虎的修改就可以了，修改这部分主要关注的是系统内核版本和设备
ls -l /dev/disk/by-uuid #查看所有设备的uuid
ls -l {要修复的硬盘挂载点}/boot       #查看可用内核
 
好了，到此，将这个修改后的grub.cfg移动到“{要修复的硬盘挂载点}/boot/grub/”， 重启以目标硬盘引导。。
 
我的到这里就成功了....忽忽，关于/etc/grub.d/*和/etc/default/grub的配置，本文没有提到，可以参阅[https://help.ubuntu.com/community/Grub2#/etc/default/grub](https://help.ubuntu.com/community/Grub2#/etc/default/grub)
 
 
