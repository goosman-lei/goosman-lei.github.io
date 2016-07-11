---
layout: post
title: php-mysql扩展的mysql_connect/mysql_pconnect比较
date: 2012-10-24 02:30:00
categories: []
tags: []
---
author: selfimpr
blog: http://blog.csdn.net/lgg201
mail: lgg860911@yahoo.com.cn

**item**
**mysql_connect**
**mysql_pconnect**
函数原型
resource mysql_connect($host_port, $user, $passwd, $newlink, $client_flags);
第四个参数$newlink标记是否创建新的资源对象
resource mysql_pconnect($host_port, $user, $passwd, $client_flags);
allow_persistent指令
设置此指令使得两个函数行为均和mysql_connect()一致
连接资源对象获取
1. 对$host_port, $user, $passwd, $client_flags求哈希值
2. 在普通资源列表(EG(regular_list))中查找连接对象(已找到并且没有设置$newlink强制创建新连接)
3. 检查找到的对象是否资源类型
4. 从查找到的对象中读取连接对象
5. 将当前获取的连接对象设置为全局默认连接对象
6. 增加连接对象的引用计数,设置zval属性返回

1. 对$host_port, $user, $passwd, $client_flags求哈希值
2. 从持久化资源列表(EG(persist_list))中查找连接对象(没有找到)
3. 检查max_links配置指令限制是否到达
4. 检查max_persistent配置指令限制是否到达
5. 分配连接对象(php_mysql_conn)空间
6. 设置连接对象的基础属性
7. 初始化驱动层连接对象(mysqlnd/libmysql两种方式)
8. 设置连接超时时间
9. 发起驱动层的真实连接请求
10. 构造持久化列表元素对象,将新连接对象设置到元素中
11. 将连接对象更新到持久化列表中
12. 更新(增加)num_persistent/num_links计数
13. 注册资源类型返回值
14. 将当前获取的连接设置为全局默认连接对象

1. 对$host_port, $user, $passwd, $client_flags求哈希值
2. 在普通资源列表(EG(regular_list))中查找连接对象(未找到或设置了$newlink强制创建新连接)
3. 检查max_links配置指令限制
4. 分配连接对象(php_mysql_conn)空间
5. 设置连接对象基础属性
6. 初始化驱动层连接对象(mysqlnd/libmysql)
7. 设置连接超时时间
8. 发起驱动层的真实连接
9. 将连接对象注册为资源类型返回值
10. 将连接对象更新到普通资源列表(EG(regualr_list))中
11. 更新num_links计数
12. 将当前获取的连接对象设置为全局默认连接对象

1. 对$host_port, $user, $passwd, $client_flags求哈希值
2. 从持久化资源列表中查找连接对象(已找到)
3. 检查查找到的持久化资源的类型是否匹配
4. 从持久化资源中读取连接对象
5. 设置连接对象基本属性
6. 检查服务端是否主动关闭
7. 如果服务端主动关闭则进行重连
8. 注册资源类型返回值
9. 将当前获取的连接设置为全局默认连接对象

regular_list Vs. persistent_list
1. regular_list和persistent_list两者都是HashTable
2. 两者都是执行全局环境executor_globals的成员
3. 两者生命周期不同, regular_list在php_request_shutdown()时被释放,也就是单个请求处理完成之后释放,而persistent_list在php_module_shutdown()的时候调用zend_shutdown()释放,也就是在整个进程完成执行时释放




