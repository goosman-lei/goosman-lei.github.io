---
layout: post
title: PHP Memcached扩展安装
date: 2010-11-12 00:32:00
categories: [memcached, 扩展, php, download, session, vim]
tags: []
---
author: selfimpr
blog: http://blog.csdn.net/lgg201
mail: lgg860911@yahoo.com.cn
 
注意:
    以下所有操作如果提示无权限, 则加sudo
    版本号与您下载的不一致, 则请对应修改
   
1. 下载文件:
    https://launchpad.net/libmemcached/+download, 下载libmemcached依赖库
    http://pecl.php.net/package/memcached, 下载php memcached扩展
2. 解压
    tar zxvf libmemcached-0.44.tar.gz
    tar zxvf memcached-1.0.2.tgz
3. 编译libmemcached
    cd libmemcached-0.44
    ./configure --prefix=/usr/local/libmemcached
    make
    make install
4. 编译memcached扩展
    cd memcached-1.0.2
    phpize
    ./configure --with-libmemcached-dir=/usr/local/libmemcached #与安装libmemcached时指定的prefix一致
    make

5. 安装扩展
    cp modules/memcached.so /usr/local/lib/php/extersion/no-debug-non-zts-20060613 #php路径不一致时请修改
    vim /usr/local/lib/php.ini #或其他编辑器
    #加入一行: extersion=memcached.so
6. 重启php服务
7. 验证(查看phpinfo)
memcached
memcached support    enabled
Version     1.0.2
libmemcached version     0.44
Session support     yes
igbinary support     no
