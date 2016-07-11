author: selfimpr
blog: http://blog.csdn.net/lgg201
mail: lgg860911@yahoo.com.cn

**item**
**mysql_query**
**mysql_unbuffered_query**
函数原型
resource mysql_query($query[, $link = NULL]);
resource mysql_unbuffered_query($query[, $link = NULL]);
数据读取的处理流程
1. ******以****mysqlnd****为例**
2. 源代码中有分歧的入口php_mysqlnd_conn_store_result_pub
3. 检查连接的最后一次执行的SQL是否SELECT,检查连接当前状态是否是抓取数据,如果不是则返回错误
4. 执行php_mysqlnd_res_store_result_pub函数
5. 构造结果集对象MYSQLND_RES:增加连接zval的引用计数,设置抓取接口函数(mysqlnd_fetch_row_buffered),抓取缓冲区长度获取函数(mysqlnd_fetch_lengths_buffered)
6. 在结果集对象成员中分配结果数据存储缓冲区空间(默认16000),分配存储长度使用的空间(field_count个unsigned
 long的空间)
7. 调用php_mysqlnd_res_store_result_fetch_data_pub函数读取数据
8. 分配数据存储空间(1个MYSQLND_RES_BUFFERED结构,
 free_rows(硬编码1)个MYSQLND_MEMORY_POOL_CHUNK指针)
9. 设置结果集的行解码器(php_mysqlnd_rowp_read_binary_protocol, php_mysqlnd_rowp_read_text_protocol)
10. 分配行数据包存储空间,并初始化数据包成员属性
11. 循环按行读取服务端响应数据

- 自动检查并扩展行缓冲区(第8步分配的MYSQLND_RES_BUFFERED->row_buffers),以10%的速度扩展
- 维护free_rows计数,用于上一步的缓冲区自动扩展
- 将读取到的包中的行数据写入到行缓冲区中对应行上(数组对应下标)
- 更新已获取的行数
- 清理包(row_packet)的临时数据

1. 返回结果的构建

- 检查是否有row_count(即是否有结果)
- 分配row_count * field_count个zval结构
- 清空分配的所有zval的内存空间(memset(p,
 0, size))

1. 节省内存,释放多余分配的行缓冲区内存空间
2. 变更连接的状态(结果集已就绪)
3. 初始化数据集的游标(指针)到数据首指针
4. 设置受影响行数为row_count(源码注释:
 libmysql文档说明affected_rows同样适用于SELECT语句)
5. 释放用于按行读取的row_packet数据结构
6. 将结果集对象作为返回值返回

1. ******以****mysqlnd****为例**
2. 源代码中有分歧的入口php_mysqlnd_conn_use_result_pub
3. 检查连接的最后一次执行的SQL是否SELECT,检查连接当前状态是否是抓取数据,如果不是则返回错误
4. 执行php_mysqlnd_res_use_result_pub函数
5. 根据协议不同初始化结果集基本属性

- 读取抓取接口函数
- 抓取缓冲区长度获取函数
- 行解码器(二进制协议/文本协议两种)

1. 创建结果集内存池(默认16000)
2. 分配一个数据存储空间(无缓冲-MYSQLND_RES_UNBUFFERED)
3. 设置按行的包读取函数
4. 初始化行的封装结构体(struct st_mysqlnd_packet_row)基本属性

- 结果集内存池
- 字段数(发送查询时已得到)
- 是否二进制协议
- 字段元数据

1. 将结果集对象作为返回值返回

 
   
