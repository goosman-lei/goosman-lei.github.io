---
layout: post
title: [php内核bug]动态链接方式编译的扩展, 扩展全局空间dtor导致core dump
date: 2013-01-18 14:15:00
categories: []
tags: []
---
author: goosman.lei(雷果国)blog: http://blog.csdn.net/lgg201mail: goosman.lei@gmail.com
相关代码可以参考<php extending and embedding>一书第12章, "Extension Globals"一节.

注册扩展的全局空间代码如下:#ifdef ZTS    ts_allocate_id(&sample_globals_id, sizeof(zend_sample_globals), (ts_allocate_ctor)ZEND_MODULE_GLOBALS_CTOR_N(sample), (ts_allocate_dtor)ZEND_MODULE_GLOBALS_DTOR_N(sample));#else    sample_globals_ctor(&sample_globals TSRMLS_CC);#endif

在ts_allocate_id()函数调用中, 向resource_types_table这个数组中写入了一条记录.在tsrm_shutdown()的过程中, 将调用注册的dtor回调函数.
但是我这边在按照书上编码完后, 运行测试代码会有coredump.经过跟踪发现, 在zend_shutdown()的调用过程中, 已经对模块调用了DL_UNLOAD(module->handle); 导致当时注册的句柄(dtor)在执行tsrm_shutdown()时已经不可访问.
同时, 看到标准扩展中的ext/standard/file.c中也有这种注册方式的使用, 不过, 它应该是静态编译所以没有问题. 而我的扩展是编译.so动态链接的.
下面是跟踪zend_shutdown()最终到DL_UNLOAD()的调用路径.zend_shutdown()  => zend_desctroy_modules() => zend_hash_graceful_reverse_destroy() => zend_hash_apply_deleter() => module_destructor() => DL_UNLOAD()
