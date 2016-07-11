最近项目需要使用ThinkPHP, 就对其源代码进行了一次review, 以熟悉相关逻辑.
过程中产生了这份文档, 分享给大家, 希望大家能够有所收获, 有不合适的地方请指正.

下面是总述, 全文比较大, 有50多页的word文档, 请到[我的CSDN下载区](http://download.csdn.net/user/lgg201)下载

版本: ThinkPHP_3.0RC2_Core
作者: selfimpr
Blog: [http://blog.csdn.net/lgg201](http://blog.csdn.net/lgg201)
Mail: [goosman.lei@gmail.com](mailto:goosman.lei@gmail.com)
环境/工具:
•    php-5.3.5-fpm
•    nginx-1.0.14
•    vim + vim.debugger
•    xdebug-2.2.0RC1
参考:     
•    模式: ThinkPHP_3.0_Full
•    概念: ThinkPHP3.0完全开发手册
目的: 
1.    熟悉ThinkPHP工作流程
2.    熟悉ThinkPHP提供的公共函数库
3.    熟悉ThinkPHP的Action, Model, View等封装
4.    了解ThinkPHP的ORM
5.    熟悉ThinkPHP的模板引擎和标签库
概要: 通过阅读ThinkPHP源代码熟悉基于其的MVC开发, 并了解MVC框架实现细节.
评价:
1.    文档鼓励跨模块调用, 增加了系统耦合度
2.    系统未设计统一出口, 导致系统可控性较差
3.    整体代码略显杂乱, 少量硬编码
代码问题:
1.    redirect()函数中设计了中转页, 但未暴露相应编程接口
2.    URL_CASE_INSENSITIVE表意不明确, 它的含意是使用C风格命名(下划线)还是使用Java风格命名(驼峰)
3.    系统函数中大量使用了静态变量缓存, 但是部分实现上存在读/写key不一致的bug, 比如A, D
4.    U方法中将协议硬编码为http://, 不利于扩展
5.    Db的工厂接口getInstance()会导致factory()被调用两次
6.    配置了读写分离(DB_RW_SEPARATE)和DB_MASTER_NUM(大于0)时, 存在bug导致master库之前的数据库不能被命中.
7.    Db->add()方法中对_after_insert()回调的处理依赖last_insert_id, 对无自增id的表则无法处理
8.    TagLibCx中compiler()方法对<literal>标签的处理, literal编号只有一位数字, 当模板中<literal>标签超过10个时, 导致模板无法展现.
涉及点:
1.    设计相关概念
2.    请求分发流程
3.    数据库抽象层
4.    Mysql数据库驱动层
5.    Model层基类
6.    视图层
7.    内建标签库
8.    少量公共函数

