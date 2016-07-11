Author: selfimpr
Blog: http://blog.csdn.net/lgg201
Mail: goosman.lei@gmail.com
Copyright: 转载请注明出处

0. 下载:
本程序可自由修改, 自由分发, 可在http://download.csdn.net/user/lgg201下载
1. 分页的需求
信息的操纵和检索是当下互联网和企业信息系统承担的主要责任. 信息检索是从大量的数据中找到符合条件的数据以用户界面展现给用户.
符合条件的数据通常会有成千上万条, 而用户的单次信息接受量是很小的, 因此, 如果一次将所有符合用户条件的数据展现给用户, 对于多数场景, 其中大部分数据都是冗余的.
信息检索完成后, 是需要经过传输(从存储介质到应用程序)和相关计算(业务逻辑)的, 因此, 我们需要一种分段的信息检索机制来降低这种冗余.
分页应运而生.
2. 分页的发展
基本的分页程序, 将数据按照每页记录数(page_size)将数据分为ceil(total_record / page_size)页, 第一次为用户展现第一段的数据, 后续的交互过程中, 用户可以选择到某一页对数据进行审阅.
后来, 主要是在微博应用出现后, 由于其信息变化很快, 而其特性为基于时间线增加数据, 这样, 基本的分页程序不能再满足需求了: a) 当获取下一页时, 数据集可能已经发生了很多变化, 翻页随时都可能导致数据重复或跳跃; b) 此类应用采用很多采用一屏展示多段数据的用户界面, 更加加重了数据重复/跳跃对用户体验的影响. 因此, 程序员们开始使用since_id的方式, 将下一次获取数据的点记录下来, 已减轻上述弊端.
在同一个用户界面, 通过用户阅读行为自动获取下一段/上一段数据的确比点击"下一页"按钮的用户体验要好, 但同样有弊端: a) 当用户已经到第100页时, 他要回到刚才感兴趣的第5页的信息时, 并不是很容易, 这其实是一条设计应用的规则, 我们不能让用户界面的单页屏数过多, 这样会降低用户体验; b) 单从数据角度看, 我们多次读取之间的间隔时间足够让数据发生一些变化, 在一次只展示一屏时, 我们很难发现这些问题(因此不影响用户体验),
 然而当一页展示100屏数据时, 这种变化会被放大, 此时, 数据重复/跳跃的问题就会再次出现; c) 从程序的角度看, 将大量的数据放置在同一个用户界面, 必然导致用户界面的程序逻辑受到影响. 基于以上考虑, 目前应用已经开始对分页进行修正, 将一页所展示的屏数进行的限制, 同时加入了页码的概念, 另外也结合since_id的方式, 以达到用户体验最优, 同时保证数据逻辑的正确性(降低误差).
3. 分页的讨论
感谢xp/jp/zq/lw四位同事的讨论, 基于多次讨论, 我们分析了分页程序的本质. 主要的结论点如下:
1) 分页的目的是为了分段读取数据
2) 能够进行分页的数据一定是有序的, 哪怕他是依赖数据库存储顺序. (这一点换一种说法更容易理解: 当数据集没有发生变化时, 同样的输入, 多次执行, 得到的输出顺序保持不变)
3) 所有的分段式数据读取, 要完全保证数据集的一致性, 必须保证数据集顺序的一致性, 即快照
4) 传统的分页, 分段式分页(每页内分为多段)归根结底是对数据集做一次切割, 映射到mysql的sql语法上, 就是根据输入求得limit子句, 适用场景为数据集变化频率低
5) since_id类分页, 其本质是假定已有数据无变化, 将数据集的某一个点的id(在数据集中可以绝对定位该数据的相关字段)提供给用户侧, 每次携带该id读取相应位置的数据, 以此模拟快照, 使用场景为数据集历史数据变化频率低, 新增数据频繁
6) 如果存在一个快照系统, 能够为每一个会话发起时的数据集产生一份快照数据, 那么一切问题都迎刃而解
7) 在没有快照系统的时候, 我们可以用since_id的方式限定数据范围, 模拟快照系统, 可以解决大多数问题
8) 要使用since_id方式模拟快照, 其数据集排序规则必须有能够唯一标识其每一个数据的字段(可能是复合的)
4. 实现思路
1) 提供SQL的转换函数
2) 支持分段式分页(page, page_ping, ping, ping_size), 传统分页(page, page_size), 原始分页(offset-count), since_id分页(prev_id, next_id)
3) 分段式分页, 传统分页, 原始分页在底层均转换为原始分页处理
5. 实现定义
ping_to_offset
输入:
page#请求页码, 范围: [1, total_page], 超过范围以边界计, 即0修正为1, total_page + 1修正为total_page
ping#请求段号, 范围: [1, page_ping], 超过范围以边界计, 即0修正为1, page_ping + 1修正为page_ping
page_ping#每页分段数, 范围: [1, 无穷]
count#要获取的记录数, 当前应用场景含义为: 每段记录数, 范围: [1, 无穷]
total_record#总记录数, 范围: [1, 无穷]
输出:
offset#偏移量
count#读取条数
offset_to_ping
输入:
offset#偏移量(必须按照count对齐, 即可以被count整除), 范围: [0, 无穷]
page_ping#每页分段数, 范围: [1, 无穷]
count#读取条数, 范围: [1, 无穷]
输出:
page#请求页码
ping#请求段号
page_ping#每页分段数
count#要获取的记录数, 当前应用场景含义为: 每段记录数
page_to_offset
输入:
page#请求页码, 范围: [1, total_page], 超过范围以边界计, 即0修正为1, total_page + 1修正为total_page
total_record#总记录数, 范围: [1, 无穷]
count#要获取的记录数, 当前应用场景含义为: 每页条数, 范围: [1, 无穷]
输出:
offset#偏移量
count#读取条数
offset_to_page
输入:
offset#偏移量(必须按照count对齐, 即可以被count整除), 范围: [0, 无穷]
count#读取条数, 范围: [1, 无穷]
输出:
page#请求页码
count#要获取的记录数, 当前应用场景含义为: 每页条数
sql_parser#将符合mysql语法规范的SQL语句解析得到各个组件
输入:
sql#要解析的sql语句
输出:
sql_components#SQL解析后的字段
sql_restore#将SQL语句组件集转换为SQL语句
输入:
sql_components#要还原的SQL语句组件集
输出:
sql#还原后的SQL语句
sql_to_count#将符合mysql语法规范的SELECT语句转换为获取计数
输入:
sql_components#要转换为查询计数的SQL语句组件集
alias#计数字段的别名
输出:
sql_components#转换后的查询计数SQL语句组件集
sql_add_offset
输入:
sql_components#要增加偏移的SQL语句组件集, 不允许存在LIMIT组件
offset#偏移量(必须按照count对齐, 即可以被count整除), 范围: [0, 无穷]
count#要获取的记录数, 范围: [1, 无穷]
输出:
sql_components#已增加LIMIT组件的SQL语句组件集
sql_add_since#增加since_id式的范围
输入:
sql_components#要增加范围限定的SQL语句组件集
prev_id#标记上一次请求得到的数据左边界
next_id#标记上一次请求得到的数据右边界
输出:
sql_components#增加since_id模拟快照的范围限定后的SQL语句组件集
datas_boundary#获取当前数据集的边界
输入:
sql_components#要读取的数据集对应的SQL语句组件集
datas#结果数据集
输出:
prev_id#当前数据集左边界
next_id#当前数据集右边界
mysql_paginate_query#执行分页支持的SQL语句
输入:
sql#要执行的业务SQL语句
offset#偏移量(必须按照count对齐, 即可以被count整除), 范围: [0, 无穷]
count#读取条数, 范围: [1, 无穷]
prev_id#标记上一次请求得到的数据左边界
next_id#标记上一次请求得到的数据右边界
输出:
datas#查询结果集
offset#偏移量
count#读取条数
prev_id#当前数据集的左边界
next_id#当前数据集的右边界
6. 实现的执行流程
分段式分页应用(page, ping, page_ping, count):
total_record= sql_to_count(sql);
(offset, count)= ping_to_offset(page, ping, page_ping, count, total_record)
(datas, offset, count)= mysql_paginate_query(sql, offset, count, NULL, NULL);
(page, ping, page_ping, total_record, count)= offset_to_ping(offset, page_ping, count, total_record);
return (datas, page, ping, page_ping, total_record, count);
传统分页应用(page, count):
total_record= sql_to_count(sql);
(offset, count)= page_to_offset(page, count, total_record)
(datas, offset, count)= mysql_paginate_query(sql, offset, count, NULL, NULL);
(page, total_record, count)= offset_to_page(offset, count, total_record);
return (datas, page, total_record, count);
since_id分页应用(count, prev_id, next_id):
total_record= sql_to_count(sql);
(datas, offset, count, prev_id, next_id)= mysql_paginate_query(sql, NULL, count, prev_id, next_id);
return (count, prev_id, next_id);
复合型分段式分页应用(page, ping, page_ping, count, prev_id, next_id):
total_record= sql_to_count(sql);
(offset, count)= ping_to_offset(page, ping, page_ping, count, total_record)
(datas, offset, count, prev_id, next_id)= mysql_paginate_query(sql, offset, count, prev_id, next_id);
(page, ping, page_ping, total_record, count)= offset_to_ping(offset, page_ping, count, total_record);
return (datas, page, ping, page_ping, total_record, count, prev_id, next_id);
复合型传统分页应用(page, count, prev_id, next_id):
total_record= sql_to_count(sql);
(offset, count)= page_to_offset(page, count, total_record)
(datas, offset, count, prev_id, next_id)= mysql_paginate_query(sql, offset, count, prev_id, next_id);
(page, total_record, count)= offset_to_page(offset, count, total_record);
return (datas, page, total_record, count, prev_id, next_id);
mysql_paginate_query(sql, offset, count, prev_id, next_id)
need_offset= is_null(offset);
need_since= is_null(prev_id) || is_null(next_id);
sql_components= sql_parser(sql);
if ( need_offset ) :
sql_components= sql_add_offset(sql_components, offset, count);
endif
if ( need_since ) :
sql_components= sql_add_since(sql_components, prev_id, next_id);
endif
sql= sql_restore(sql_components);
datas= mysql_execute(sql);
(prev_id, next_id)= datas_boundary(sql_components, datas);
ret= (datas);
if ( need_offset ) :
append(ret, offset, count);
endif
if ( need_since ) :
append(ret, prev_id, next_id);
endif
return (ret);
7. 测试点
1) 传统分页
2) 分段分页
3) 原始分页
4) since_id分页
5) 复合型传统分页
6) 复合型分段分页
7) 复合型原始分页
8. 测试数据构建
DROP DATABASE IF EXISTS `paginate_test`;
CREATE DATABASE IF NOT EXISTS `paginate_test`;
USE `paginate_test`;


DROP TABLE IF EXISTS `feed`;
CREATE TABLE IF NOT EXISTS `feed` (
`feed_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '微博ID', 
`ctime` INT NOT NULL COMMENT '微博创建时间', 
`content` CHAR(20) NOT NULL DEFAULT '' COMMENT '微博内容', 
`transpond_count` INT NOT NULL DEFAULT 0 COMMENT '微博转发数'
) COMMENT '微博表';


DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
`comment_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '评论ID', 
`content` CHAR(20) NOT NULL DEFAULT '' COMMENT '评论内容', 
`feed_id` INT NOT NUL COMMENT '被评论微博ID'
) COMMENT '评论表';


DROP TABLE IF EXISTS `hot`;
CREATE TABLE IF NOT EXISTS `hot` (
`feed_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '微博ID', 
`hot` INT NOT NULL DEFAULT 0 COMMENT '微博热度'
) COMMENT '热点微博表';
9. 测试用例:
1) 搜索最热微博(SELECT f.feed_id, f.content, h.hot FROM feed AS f JOIN hot AS h ON f.feed_id = h.feed_id ORDER BY hhot DESC, f.feed_id DESC)
2) 搜索热评微博(SELECT f.feed_id, f.content, COUNT(c.*) AS count FROM feed AS f JOIN comment AS c ON f.feed_id = c.feed_id GROUP BY c.feed_id ORDER BY count DESC, f.feed_id DESC)
3) 搜索热转微博(SELECT feed_id, content, transpond_count FROM feed ORDER BY transpond_count DESC, feed_id DESC)
4) 上面3种场景均测试7个测试点
10. 文件列表
readme.txt当前您正在阅读的开发文档
page.lib.php分页程序库
test_base.php单元测试基础函数
test_convert.php不同分页之间的转换单元测试
test_parse.phpSQL语句解析测试
test_page.php分页测试

下面是源代码:
page.lib.php


```php
<?php
/*
 * 分页程序核心库
 * 1. 各种分页的转换
 * 2. SQL语句解析
 * 3. SQL语句修改
 * 4. SQL语句还原
 * 5. 自动的分页支持
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 */
#分页术语
define('TERM_DATAS',						'datas');			#分页得到数据
define('TERM_COUNT',						'count');			#期望的分段记录数
define('TERM_TOTAL_RECORD',					'total_record');	#总记录数
define('TERM_OFFSET',						'offset');			#偏移量
define('TERM_PAGE',							'page');			#页码
define('TERM_PING',							'ping');			#段号
define('TERM_PAGE_PING',					'page_ping');		#每页段数
define('TERM_PREV_ID',						'prev_id');			#范围左标记
define('TERM_NEXT_ID',						'next_id');			#范围右标记

#sql语法解析错误
define('E_SQL_SELECT_PARSER',				'SQL-SELECT语法解析错误: %s');	#SQL语法解析错误

#SQL语法解析特殊字符
define('SPACES',					" \t\f\r\n");
define('QUOTES',					'"\'');
define('COMMA',						',');
define('LBRACKET',					'(');
define('RBRACKET',					')');
define('DOT',						'.');
define('SPACE',						' ');

#SQL语法解析后的组件
define('CP_SELECT',						'_select');
define('CP_OPTIONS',					'_options');
define('CP_FIELDS',						'_fields');
define('CP_FROM',						'_from');
define('CP_TABLES',						'_tables');
define('CPK_TABLES_TABLE',				'_table');
define('CPK_TABLES_ALIAS',				'_alias');
define('CPK_TABLES_CONDITION',			'_condition');
define('CPK_TABLES_SEPARATER',			'_separater');
define('CP_WHERE',						'_where');
define('CP_CONDITIONS',					'_conditions');
define('CP_GROUP_BY',					'_group_by');
define('CP_GROUPS',						'_groups');
define('CPK_GROUPS_FIELD',				'_field');
define('CPK_GROUPS_ORDER',				'_order');
define('CP_HAVING',						'_having');
define('CP_FILTERS',					'_filters');
define('CP_ORDER_BY',					'_order_by');
define('CP_ORDERS',						'_orders');
define('CPK_ORDERS_FIELD',				'_field');
define('CPK_ORDERS_ORDER',				'_order');
define('CP_LIMIT',						'_limit');
define('CP_OFFSET',						'_offset');
define('CP_COUNT',						'_count');

#SQL语法中的关键字
define('KW_COUNT',						'COUNT');
define('KW_SELECT',						'SELECT');
define('KW_ALL',						'ALL');
define('KW_DISTINCT',					'DISTINCT');
define('KW_DISTINCTROW',				'DISTINCTROW');
define('KW_HIGH_PRIORITY',				'HIGH_PRIORITY');
define('KW_STRAIGHT_JOIN',				'STRAIGHT_JOIN');
define('KW_SQL_SMALL_RESULT',			'SQL_SMALL_RESULT');
define('KW_SQL_BIG_RESULT',				'SQL_BIG_RESULT');
define('KW_SQL_BUFFER_RESULT',			'SQL_BUFFER_RESULT');
define('KW_SQL_CACHE',					'SQL_CACHE');
define('KW_SQL_NO_CACHE',				'SQL_NO_CACHE');
define('KW_SQL_CALC_FOUND_ROWS',		'SQL_CALC_FOUND_ROWS');
define('KW_FROM',						'FROM');
define('KW_WHERE',						'WHERE');
define('KW_JOIN',						'JOIN');
define('KW_LEFT',						'LEFT');
define('KW_RIGHT',						'RIGHT');
define('KW_INNER',						'INNER');
define('KW_OUTER',						'OUTER');
define('KW_CROSS',						'CROSS');
define('KW_AS',							'AS');
define('KW_ON',							'ON');
define('KW_GROUP',					'GROUP');
define('KW_ORDER',					'ORDER');
define('KW_BY',						'BY');
define('KW_GROUP_BY',				KW_GROUP . SPACE . KW_BY);
define('KW_HAVING',					'HAVING');
define('KW_ORDER_BY',				KW_ORDER . SPACE . KW_BY);
define('KW_ASC',					'ASC');
define('KW_DESC',					'DESC');
define('KW_LIMIT',					'LIMIT');
define('KW_AND',					'AND');
define('KW_OR',						'OR');
define('KWS_EQ',					'=');
define('KWS_LT',					'<');
define('KWS_GT',					'>');
define('KWS_LE',					'<=');
define('KWS_GE',					'>=');

define('ORDER_ALIAS_PREFIX',		'__o_');
define('SINCE_ID_SEPARATER_0',		'|');
define('SINCE_ID_SEPARATER_1',		':');
define('DIRECT_PREV',				'PREV');
define('DIRECT_NEXT',				'NEXT');
define('COUNT_DEFAULT_ALIAS',		'__c');

#当前解析器需要处理的SELECT选项
define('ENABLE_OPTIONS',				'_enable_options');
$GLOBALS[ENABLE_OPTIONS]	= array(
	KW_ALL, KW_DISTINCT, KW_DISTINCTROW, 
	KW_HIGH_PRIORITY, KW_STRAIGHT_JOIN, KW_SQL_SMALL_RESULT, 
	KW_SQL_BIG_RESULT, KW_SQL_BUFFER_RESULT, KW_SQL_CACHE, 
	KW_SQL_NO_CACHE, KW_SQL_CALC_FOUND_ROWS, 
);

function p_datas($info) {
	return $info[TERM_DATAS];
}
#读取每段记录数
function p_count($info) {
	return intval($info[TERM_COUNT]);
}
#读取总记录数
function p_total_record($info) {
	return intval($info[TERM_TOTAL_RECORD]);
}
#读取偏移量
function p_offset($info) {
	return intval($info[TERM_OFFSET]);
}
#读取页码
function p_page($info) {
	return intval($info[TERM_PAGE]);
}
#读取分段号
function p_ping($info) {
	return intval($info[TERM_PING]);
}
#读取每页分段数
function p_page_ping($info) {
	return intval($info[TERM_PAGE_PING]);
}
#读取范围左标识
function p_prev_id($info) {
	return strval($info[TERM_PREV_ID]);
}
#读取范围右标识
function p_next_id($info) {
	return strval($info[TERM_NEXT_ID]);
}

#分段分页到偏移量转换
function ping_to_offset($page, $ping, $page_ping, $count, $total_record) {
	$ping		= min($page_ping, max(1, $ping));
	$total_ping	= ceil($total_record / $count);
	$total_page	= ceil($total_ping / $page_ping);
	$page		= min($total_page, max(1, $page));
	$real_ping	= ($page - 1) * $page_ping + $ping;
	$real_ping	= min($total_ping, max(1, $real_ping));
	$offset		= ($real_ping - 1) * $count;
	return array(
		TERM_OFFSET	=> intval($offset), 
		TERM_COUNT	=> intval($count), 
	);
}
#偏移量到分段分页转换
function offset_to_ping($offset, $page_ping, $count) {
	$real_ping	= ($offset / $count) + 1;
	$ping		= ($real_ping - 1) % $page_ping + 1;
	$page		= ($real_ping - $ping) / $page_ping + 1;
	return array(
		TERM_PAGE			=> intval($page), 
		TERM_PING			=> intval($ping), 
		TERM_PAGE_PING		=> intval($page_ping), 
		TERM_COUNT			=> intval($count), 
	);
}
#传统分页到偏移量转换
function page_to_offset($page, $count, $total_record) {
	$total_page	= ceil($total_record / $count);
	$page		= min($total_page, max(1, $page));
	$offset		= ($page - 1) * $count;
	return array(
		TERM_OFFSET	=> intval($offset), 
		TERM_COUNT	=> intval($count), 
	);
}
#偏移量到传统分页转换
function offset_to_page($offset, $count) {
	$page	= floor($offset / $count) + 1;
	return array(
		TERM_PAGE	=> intval($page), 
		TERM_COUNT	=> intval($count), 
	);
}


#------------------------------------------------SQL语法解析器
/* 解析器要处理的SELECT语法
SELECT
    [ALL | DISTINCT | DISTINCTROW ]
      [HIGH_PRIORITY]
      [STRAIGHT_JOIN]
      [SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
      [SQL_CACHE | SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
    select_expr [, select_expr ...]
    [FROM table_references
    [WHERE where_condition]
    [GROUP BY {col_name | expr | position}
      [ASC | DESC], ... [WITH ROLLUP]]
    [HAVING where_condition]
    [ORDER BY {col_name | expr | position}
      [ASC | DESC], ...]
    [LIMIT {[offset,] row_count | row_count OFFSET offset}]
*/
#符合mysql语法规范的SELECT语句
function sql_parser($sql) {
	$tokens		= sql_tokens($sql);
	$components	= sql_analysis($tokens);
	return $components;
}
#sql_parser的逆向函数
/* sql_components的结构
array(
	CP_SELECT		=> KW_SELECT, 									#SELECT关键字
	CP_OPTIONS		=> $GLOBALS[ENABLE_OPTIONS], 					#SELECT选项
	CP_FIELDS		=> array(										#查询字段
		array('field_0_comp_0', 'field_0_comp_1', ...), 			#字段0的构成
		array('field_1_comp_0', 'field_2_comp_1', ...), 			#字段1的构成
		...
	), 
	CP_FROM			=> KW_FROM,										#FROM关键字
	CP_TABLES		=> array(										#目标表
		array(
			CPK_TABLES_TABLE		=> array('table_0_comp_0', 'table_0_comp_1', ...), 		#数据表描述
			CPK_TABLES_ALIAS		=> 'alias', 											#别名
			CPK_TABLES_CONDITION	=> array('cond_0_comp_0', 'cond_0_comp_1', ...), 		#条件
		), 
		array(
			CPK_TABLES_SEPARATER	=> array(KW_LEFT, KW_JOIN), 							#表0和表1的连接符(KW_LEFT, KW_RIGHT, KW_INNER, KW_CROSS, KW_OUTER, KW_JOIN, COMMA)
			CPK_TABLES_TABLE		=> array('table_1_comp_0', 'table_1_comp_1', ...), 		#数据表描述
			CPK_TABLES_ALIAS		=> 'alias', 											#别名
			CPK_TABLES_CONDITION	=> array('cond_1_comp_0', 'cond_1_comp_1', ...), 		#条件
		), 
	), 
	CP_WHERE		=> KW_WHERE,									#WHERE关键字
	CP_CONDITIONS	=> array(										#查询条件(WHERE)
		array('cond_comp_0', 'cond_comp_1', ...), 					#条件构件
	), 
	CP_GROUP_BY		=> KW_GROUP_BY									#GROUP BY关键字
	CP_GROUPS		=> array(																#分组项
		array(
			CPK_GROUPS_FIELD => array('field_0_comp_0', 'field_0_comp_1', ...), 			#分组项0的字段构件
			CPK_GROUPS_ORDER => KW_ASC, 													#分组项0的排序规则(KW_ASC, KW_DESC)
		), 
		array(
			CPK_GROUPS_FIELD => array('field_1_comp_0', 'field_0_comp_1', ...), 			#分组项1的字段构件
			CPK_GROUPS_ORDER => KW_DESC, 													#分组项1的排序规则(KW_ASC, KW_DESC)
		), 
	), 
	CP_HAVING		=> KW_HAVING,									#HAVING关键字
	CP_FILTERS		=> array(										#过滤条件(HAVING)
		array('cond_comp_0', 'cond_comp_1', ...), 					#条件构件
	), 
	CP_ORDER_BY		=> KW_ORDER_BY,									#ORDER BY关键字
	CP_ORDERS		=> array(																#排序项
		array(
			CPK_ORDERS_FIELD => array('field_0_comp_0', 'field_0_comp_1', ...), 			#排序项0的字段构件
			CPK_ORDERS_ORDER => KW_ASC, 													#排序项0的排序规则(KW_ASC, KW_DESC)
		), 
		array(
			CPK_ORDERS_FIELD => array('field_1_comp_0', 'field_0_comp_1', ...), 			#排序项1的字段构件
			CPK_ORDERS_ORDER => KW_DESC, 													#排序项1的排序规则(KW_ASC, KW_DESC)
		), 
	), 
	CP_LIMIT		=> KW_LIMIT, 									#LIMIT关键字
	CP_OFFSET		=> 0,											#偏移量
	CP_COUNT		=> 10, 											#最大记录数
);
TODO 实现还原
 */
function sql_restore($sc) {
	$r	= array();
	sr_restore_select($sc, $r);
	sr_restore_options($sc, $r); 
	sr_restore_fields($sc, $r); 
	sr_restore_tables($sc, $r); 
	sr_restore_conditions($sc, $r); 
	sr_restore_groups($sc, $r); 
	sr_restore_filters($sc, $r); 
	sr_restore_orders($sc, $r); 
	sr_restore_offset_count($sc, $r);
	return implode_exclude_bracket(SPACE, $r);
}
#将SQL语句转换为计数SQL(不支持外连接)
function sql_to_count($sql, $alias = COUNT_DEFAULT_ALIAS) {
	$sc	= sql_parser($sql);
	$sc	= sc_to_count($sc, $alias);
	return sql_restore($sc);
}
#SQL语句增加since_id
function sql_add_since($sql, $prev_id, $next_id, &$need_reverse) {
	$sc	= sql_parser($sql);
	$sc	= sc_add_since($sc, $prev_id, $next_id, $need_reverse);
	return sql_restore($sc);
}
#为SQL语句设置偏移量
function sql_add_offset($sql, $offset, $count) {
	$sc	= sql_parser($sql);
	$sc	= sc_add_offset($sql, $offset, $count);
	return sql_restore($sc);
}
#对外的单一接口提供
/* 
	1) 传统分页
	2) 分段分页
	3) 原始分页
	4) since_id分页
	5) 复合型传统分页
	6) 复合型分段分页
	7) 复合型原始分页
 */
function mysql_paginate_query($conn, $sql, $count, $offset = NULL, $prev_id = NULL, $next_id = NULL) {
	#修正偏移量
	$offset	= correct_offset($offset);
	$sc		= sql_parser($sql);

	#增加偏移量
	sc_add_offset($sc, $offset, $count);
	
	#增加since条件
	sc_add_since($sc, $prev_id, $next_id, $need_reverse);

	#结构化SQL到SQL语句
	$sql	= sql_restore($sc);
	#执行查询
	$datas	= mysql_geta_all($conn, $sql);
	if ( $need_reverse )
		$datas	= array_reverse($datas);
	#获取新的数据边界
	if ( $prev_id || $next_id )
		sc_datas_boundary($sc, $datas, $prev_id, $next_id);
	
	return array(
		TERM_DATAS		=> $datas, 
		TERM_OFFSET		=> $offset, 
		TERM_COUNT		=> $count, 
		TERM_PREV_ID	=> $prev_id, 
		TERM_NEXT_ID	=> $next_id, 
	);
}
#获取Top N数据
function mysql_paginate_top($conn, $sql, $count) {
	#获取总记录数
	$total_record	= mysql_get_total_record($conn, $sql);
	#读取数据
	$data_info	= mysql_paginate_query($conn, $sql, 
				$count, NULL, 
				NULL, NULL);
	return array(
		TERM_DATAS			=> p_datas($data_info), 
		TERM_COUNT			=> p_count($data_info), 
		TERM_TOTAL_RECORD	=> $total_record, 
	);
}
#传统方式结合since_id分页
#(TODO 分段分页/传统分页/原始分页与since_id分页联合使用时, 记录数是since_id条件附加之前的记录数, 因此会导致页码数据错乱, 目前不对此进行处理)
function mysql_paginate_tradition_since_id($conn, $sql, $page, 
		$count, $prev_id = NULL, $next_id = NULL, $total_record = NULL) {
	#修正总记录数
	mysql_correct_total_record($conn, $sql, $total_record);
	#分页信息转换为偏移量
	$offset_info	= page_to_offset($page, $count, $total_record);
	#读取数据
	$data_info	= mysql_paginate_query($conn, $sql, 
				p_count($offset_info), p_offset($offset_info), 
				$prev_id, $next_id);
	#修正为页码信息
	$page_info		= offset_to_page(p_offset($data_info), p_count($data_info));
	return array(
		TERM_DATAS			=> p_datas($data_info), 
		TERM_PAGE			=> p_page($page_info), 
		TERM_TOTAL_RECORD	=> $total_record, 
		TERM_COUNT			=> p_count($page_info), 
		TERM_PREV_ID		=> p_prev_id($data_info), 
		TERM_NEXT_ID		=> p_next_id($data_info), 
	);
}
#单纯的传统分页
function mysql_paginate_tradition($conn, $sql, $page, $count, $total_record = NULL) {
	$info	= mysql_paginate_tradition_since_id($conn, $sql, $page, $count, NULL, NULL, $total_record);
	unset($info[TERM_PREV_ID]);
	unset($info[TERM_NEXT_ID]);
	return $info;
}
#分段分页结合since_id分页
#(TODO 分段分页/传统分页/原始分页与since_id分页联合使用时, 记录数是since_id条件附加之前的记录数, 因此会导致页码数据错乱, 目前不对此进行处理)
function mysql_paginate_ping_since_id($conn, $sql, $page, $ping, 
		$page_ping, $count, $prev_id = NULL, $next_id = NULL, $total_record = NULL) {
	#修正总记录数
	mysql_correct_total_record($conn, $sql, $total_record);
	#分段信息转换为offset
	$offset_info	= ping_to_offset($page, $ping, $page_ping, $count, $total_record);
	#读取数据
	$data_info		= mysql_paginate_query($conn, $sql, 
					p_count($offset_info), p_offset($offset_info), 
					$prev_id, $next_id);
	#修正为分段信息
	$ping_info		= offset_to_ping(p_offset($data_info), 
					$page_ping, p_count($data_info));
	return array(
		TERM_DATAS			=> p_datas($data_info), 
		TERM_PAGE			=> p_page($ping_info), 
		TERM_PING			=> p_ping($ping_info), 
		TERM_PAGE_PING		=> p_page_ping($ping_info), 
		TERM_COUNT			=> p_count($ping_info), 
		TERM_TOTAL_RECORD	=> $total_record, 
		TERM_PREV_ID		=> p_prev_id($data_info), 
		TERM_NEXT_ID		=> p_next_id($data_info), 
	);
}
#单纯的分段分页
function mysql_paginate_ping($conn, $sql, $page, $ping, $page_ping, $count, $total_record = NULL) {
	$info	= mysql_paginate_ping_since_id($conn, $sql, $page, $ping, 
			$page_ping, $count, NULL, NULL, $total_record);
	unset($info[TERM_PREV_ID]);
	unset($info[TERM_NEXT_ID]);
	return $info;
}
#原始分页结合since_id分页
#(TODO 分段分页/传统分页/原始分页与since_id分页联合使用时, 记录数是since_id条件附加之前的记录数, 因此会导致页码数据错乱, 目前不对此进行处理)
function mysql_paginate_raw_since_id($conn, $sql, $offset, $count, 
		$prev_id = NULL, $next_id = NULL, $total_record = NULL) {
	#修正总记录数
	mysql_correct_total_record($conn, $sql, $total_record);
	$offset			= min($total_record, max(0, $offset));
	#读取数据
	$data_info		= mysql_paginate_query($conn, $sql, 
					$count, $offset, $prev_id, $next_id);
	return array(
		TERM_DATAS			=> p_datas($data_info), 
		TERM_OFFSET			=> p_offset($data_info), 
		TERM_COUNT			=> p_count($data_info), 
		TERM_TOTAL_RECORD	=> $total_record, 
		TERM_PREV_ID		=> p_prev_id($data_info), 
		TERM_NEXT_ID		=> p_next_id($data_info), 
	);
}
#单纯的原始分页
function mysql_paginate_raw($conn, $sql, $offset, $count, $total_record = NULL) {
	$info	= mysql_paginate_raw_since_id($conn, $sql, $offset, $count, NULL, NULL, $total_record);
	unset($info[TERM_PREV_ID]);
	unset($info[TERM_NEXT_ID]);
	return $info;
}
#单纯的since_id分页
function mysql_paginate_since_id($conn, $sql, $count, $prev_id = NULL, $next_id = NULL) {
	#获取总记录数
	$total_record	= mysql_get_total_record($conn, $sql);
	#读取数据
	$data_info	= mysql_paginate_query($conn, $sql, $count, NULL,
				is_null($prev_id) ? TRUE : $prev_id,
				is_null($next_id) ? TRUE : $next_id);
	return array(
		TERM_DATAS			=> p_datas($data_info), 
		TERM_PREV_ID		=> p_prev_id($data_info), 
		TERM_NEXT_ID		=> p_next_id($data_info), 
		TERM_COUNT			=> p_count($data_info), 
		TERM_TOTAL_RECORD	=> $total_record, 
	);
}
#获取总记录数
function mysql_get_total_record($conn, $sql) {
	$sql	= sql_to_count($sql, COUNT_DEFAULT_ALIAS);
	$row	= mysql_geta($conn, $sql);
	$total_record	= $row[COUNT_DEFAULT_ALIAS];
	return $total_record;
}
#修正总记录数
function mysql_correct_total_record($conn, $sql, &$total_record = NULL) {
	if ( !is_numeric($total_record) ) 
		$total_record	= intval(mysql_get_total_record($conn, $sql));
	$total_record	= intval($total_record);
}



#-------------------------------------------------------------------------以下部分是基础函数, 非外部接口
#----------------------------------------SQL语句解析基础函数
#将sql字符串处理为tokens
/*
 * 1. 忽略空白元素
 * 2. 将逗号/左括号/右括号作为词法单元处理
 * 3. 对字符串字面量特殊处理(防止转义等带来的影响)
 * 语法描述
SELECT		::= UNIT SEPARATER UNIT [SEPARATER UNIT [...]]
UNIT		::= WORD | LITERAL
SEPARATER	::= COMMA | SPACES | LBRACKET | RBRACKET
LITERAL		::= LQUOTE WORD RQUOTE
WORD		::= CHARACTER [CHARACTER [...]]
LQUOTE		::= "'" | "\""
RQUOTE		::= "'" | "\""
CHARACTER	::= #非特殊含义字符集
LBRACKET	::= "("
RBRACKET	::= ")"
COMMA		::= ","
SPACES		::= " " | "\t" | "\f" | "\r" | "\n"
 */
function sql_tokens($s) {
	$r	= array();
	$l	= strlen($s);
	while ( $l > 0 ) {
		if ( sp_is_quote($s) ) {
			sp_read_string($s, $l, $r);
		} else if ( sp_is_space($s) ) {
			sp_read_spaces($s, $l);
		} else if ( sp_is_comma($s) ) {
			sp_read_comma($s, $l, $r);
		} else if ( sp_is_lbracket($s) ) {
			sp_read_lbracket($s, $l, $r);
		} else if ( sp_is_rbracket($s) ) {
			sp_read_rbracket($s, $l, $r);
		} else {
			sp_read_word($s, $l, $r);
		}
	}
	return $r;
}
#对解析得到的词法单元进行语法分析
function sql_analysis($t) {
	$r	= array();
	$i	= 0;
	$l	= count($t);
	#读取SELECT关键字
	spa_read_select($t, $i, $l, $r);
	#读取SELECT选项
	spa_read_options($t, $i, $l, $r);
	#读取查询的目标字段
	spa_read_fields($t, $i, $l, $r);
	if ( spa_is_from($t, $i) ) {
		#读取FROM关键字
		spa_read_from($t, $i, $l, $r);
		#读取查询的目标表
		spa_read_tables($t, $i, $l, $r);
	}
	if ( spa_is_where($t, $i) ) {
		#读取WHERE关键字
		spa_read_where($t, $i, $l, $r);
		#读取查询条件
		spa_read_conditions($t, $i, $l, $r);
	}
	if ( spa_is_group_by($t, $i) ) {
		#读取GROUP BY关键字
		spa_read_group_by($t, $i, $l, $r);
		#读取分组信息
		spa_read_groups($t, $i, $l, $r);
	}
	if ( spa_is_having($t, $i) ) {
		#读取HAVING关键字
		spa_read_having($t, $i, $l, $r);
		#读取结果集过滤条件
		spa_read_filters($t, $i, $l, $r);
	}
	if ( spa_is_order_by($t, $i) ) {
		#读取ORDER BY关键字
		spa_read_order_by($t, $i, $l, $r);
		#读取排序信息
		spa_read_orders($t, $i, $l, $r);
	}
	if ( spa_is_limit($t, $i) ) {
		#读取LIMIT关键字
		spa_read_limit($t, $i, $l, $r);
		#读取偏移量和最大记录数
		spa_read_offset_count($t, $i, $l, $r);
	}
	return $r;
}
#----------------------------------错误处理
#sql语法解析错误处理
function sql_parser_error() {
	$msgs	= func_get_args();
	trigger_error(sprintf(E_SQL_SELECT_PARSER, implode("\t", $msgs)), E_USER_ERROR);
}
#------------------------------------词法分析基础函数
#词法分析: 检查是否空白字符
function sp_is_space($s) {
	return strpos(SPACES, $s[0]) !== FALSE;
}
#词法分析: 检查是否引号
function sp_is_quote($s) {
	return strpos(QUOTES, $s[0]) !== FALSE;
}
#词法分析: 检查是否逗号
function sp_is_comma($s) {
	return $s[0] === COMMA;
}
#词法分析: 检查是否左括号
function sp_is_lbracket($s) {
	return $s[0] === LBRACKET;
}
#词法分析: 检查是否右括号
function sp_is_rbracket($s) {
	return $s[0] === RBRACKET;
}
#词法分析: 检查是否单词字符(认为所有非上面列出的5种特殊字符, 均为单词字符)
function sp_is_wordchar($s) {
	return !sp_is_quote($s) && !sp_is_space($s) && !sp_is_comma($s) && !sp_is_lbracket($s) && !sp_is_rbracket($s);
}
#词法分析: 读取词法单元的基础函数
function sp_read_base($f, &$s, &$l, &$r = NULL, $single = FALSE) {
	$i	= -1;
	while ( ++ $i < $l ) 
		#检查是否符合词法单元条件
		if ( !$f($s[$i]) || ($single && $i > 0) )
			break;
	if ( !is_null($r) )
		$r[]	= substr($s, 0, $i);
	$s	= substr($s, $i);
	$l	-= $i;
}
#词法分析: 读取空白单元
function sp_read_spaces(&$s, &$l, &$r = NULL) {
	sp_read_base('sp_is_space', $s, $l, $r);
}
#词法分析: 读取逗号单元
function sp_read_comma(&$s, &$l, &$r = NULL) {
	sp_read_base('sp_is_comma', $s, $l, $r, TRUE);
}
#词法分析: 读取单词单元
function sp_read_word(&$s, &$l, &$r = NULL) {
	sp_read_base('sp_is_wordchar', $s, $l, $r);
}
#词法分析: 读取左括号单元
function sp_read_lbracket(&$s, &$l, &$r = NULL) {
	sp_read_base('sp_is_lbracket', $s, $l, $r, TRUE);
}
#词法分析: 读取右括号单元
function sp_read_rbracket(&$s, &$l, &$r = NULL) {
	sp_read_base('sp_is_rbracket', $s, $l, $r, TRUE);
}
#词法分析: 读取字符串字面量单元
function sp_read_string(&$s, &$l, &$r = NULL) {
	if ( !sp_is_quote($s) ) 
		sql_parser_error(__FILE__, __LINE__, __FUNCTION__);
	$q	= $s[0];
	$i	= 1;
	$e	= FALSE;
	while ( $i < $l ) {
		#读取字符并下移指针
		$c	= $s[$i ++];
		#转义状态直接将设置为非转义状态
		if ( $e ) $e = FALSE;
		#终止引号跳出处理
		else if ( $c === $q ) break;
		#转义字符设置转义状态
		else if ( $c === '\\' ) $e = TRUE;
	}
	if ( $i > $l || $e ) 
		sql_parser_error(__FILE__, __LINE__, __FUNCTION__);
	if ( !is_null($r) )
		$r[]	= substr($s, 0, $i);
	$s	= substr($s, $i);
	$l	-= $i;
}

#--------------------------------------------语法分析基础函数-简单语法单元检查
#语法分析: 检查是否逗号
function spa_is_comma($t, $i, &$n = NULL) {
	$n	= 1;
	return sp_is_comma($t[$i]);
}
#语法分析: 检查是否左括号
function spa_is_lbracket($t, $i, &$n = NULL) {
	$n	= 1;
	return sp_is_lbracket($t[$i]);
}
#语法分析: 检查是否右括号
function spa_is_rbracket($t, $i, &$n = NULL) {
	$n	= 1;
	return sp_is_rbracket($t[$i]);
}
#语法分析: 检查是否句点
function spa_is_dot($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === DOT;
}
#语法分析: 检查是否SELECT
function spa_is_select($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_SELECT;
}
#语法分析: 检查是否SELECT选项
function spa_is_option($t, $i, &$n = NULL) {
	$n	= 1;
	return in_array(strtoupper($t[$i]), $GLOBALS[ENABLE_OPTIONS]);
}
#语法分析: 检查是否FROM
function spa_is_from($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_FROM;
}
#语法分析: 检查是否WHERE
function spa_is_where($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_WHERE;
}
#语法分析: 检查是否JOIN
/* 实现语法:
join_table:
	  table_reference [INNER | CROSS] JOIN table_factor [join_condition]
	| table_reference {LEFT|RIGHT} [OUTER] JOIN table_reference join_condition
 */
function spa_is_join($t, $i, &$n = NULL) {
	$t_i0	= strtoupper($t[$i]);
	$t_i1	= strtoupper($t[$i + 1]);
	$t_i2	= strtoupper($t[$i + 2]);
	if ( $t_i0 !== KW_JOIN && $t_i1 !== KW_JOIN && $t_i2 !== KW_JOIN )
		return FALSE;
	if ( $t_i0 === KW_JOIN ) 
		$n	= 1;
	else if ( $t_i1 === KW_JOIN ) {
		if ( $t_i0 !== KW_INNER && $t_i0 !== KW_CROSS && $t_i0 !== KW_LEFT && $t_i0 !== KW_RIGHT )
			return FALSE;
		$n	= 2;
	} else if ( $t_i2 === KW_JOIN ) {
		if ( $t_i0 !== KW_LEFT && $t_i0 !== KW_RIGHT || $t_i1 !== OUTER ) 
			return FALSE;
		$n	= 3;
	}
	return TRUE;
}
#语法分析: 检查是否AS
function spa_is_as($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_AS;
}
#语法分析: 检查是否ON
function spa_is_on($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_ON;
}
#语法分析: 检查是否GROUP BY
function spa_is_group_by($t, $i, &$n = NULL) {
	$n	= 2;
	return strtoupper($t[$i]) === KW_GROUP && strtoupper($t[$i + 1]) === KW_BY;
}
#语法分析: 检查是否HAVING
function spa_is_having($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_HAVING;
}
#语法分析: 检查是否ORDER BY
function spa_is_order_by($t, $i, &$n = NULL) {
	$n	= 2;
	return strtoupper($t[$i]) === KW_ORDER && strtoupper($t[$i + 1]) === KW_BY;
}
#语法分析: 检查是否ASC
function spa_is_asc($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_ASC;
}
#语法分析: 检查是否DESC
function spa_is_desc($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_DESC;
}
#语法分析: 检查是否LIMIT
function spa_is_limit($t, $i, &$n = NULL) {
	$n	= 1;
	return strtoupper($t[$i]) === KW_LIMIT;
}
#---------------------------------------语法分析-语法边界检查
#CP_FIELDS语法单元中单条field自身结束而非整个语法单元结束的检查
function spa_end_field_self($t, $i, $l) {
	return spa_is_comma($t, $i) || $i >= $l;
}
#CP_FIELDS语法单元结束标记检查
function spa_end_fields($t, $i, $l) {
	return spa_is_from($t, $i) || spa_is_where($t, $i) || spa_is_group_by($t, $i) || spa_is_having($t, $i)
		|| spa_is_order_by($t, $i) || spa_is_limit($t, $i) || $i >= $l;
}
#CP_FIELDS语法单元中的单条field结束标记检查
function spa_end_field($t, $i, $l) {
	return spa_end_field_self($t, $i, $l) || spa_end_fields($t, $i, $l);
}
#CP_TABLES语法单元中单表自身结束而非整个语法单元结束的检查
function spa_end_table_self($t, $i, $l) {
	return spa_is_comma($t, $i) || spa_is_join($t, $i) || $i >= $l;
}
#CP_TABLES语法单元结束标记检查
function spa_end_tables($t, $i, $l) {
	return spa_is_where($t, $i) || spa_is_group_by($t, $i) || spa_is_having($t, $i)
		|| spa_is_order_by($t, $i) || spa_is_limit($t, $i) || $i >= $l;
}
#CP_TABLES语法单元中的单表结束标记检查
function spa_end_table($t, $i, $l) {
	return spa_end_table_self($t, $i, $l) || spa_end_tables($t, $i, $l);
}
#CP_CONDITIONS语法单元中单条条件自身结束而非整个语法单元结束的检查
function spa_end_condition_self($t, $i, $l) {
#TODO 暂时不对where条件做详细处理
	return $i >= $l;
}
#CP_CONDITIONS语法单元的结束标记检查
function spa_end_conditions($t, $i, $l) {
	return spa_is_group_by($t, $i)  || spa_is_having($t, $i)|| spa_is_order_by($t, $i)
		|| spa_is_limit($t, $i) || $i >= $l;
}
#CP_CONDITIONS语法单元中的单条条件结束标记检查
function spa_end_condition($t, $i, $l) {
	return spa_end_condition_self($t, $i, $l) || spa_end_conditions($t, $i, $l);
}
#CP_GROUPS语法单元中单个分组标记自身结束而非整个语法单元结束的检查
function spa_end_group_self($t, $i, $l) {
	return spa_is_comma($t, $i) || $i >= $l;
}
#CP_GROUPS语法单元中的结束标记检查
function spa_end_groups($t, $i, $l) {
	return spa_is_having($t, $i) || spa_is_order_by($t, $i) || spa_is_limit($t, $i) || $i >= $l;
}
#CP_GROUPS语法单元中的单个分组标记结束标记检查
function spa_end_group($t, $i, $l) {
	return spa_end_group_self($t, $i, $l) || spa_end_groups($t, $i, $l);
}
#CP_FILTERS语法单元中单条条件自身结束而非整个语法单元结束的检查
function spa_end_filter_self($t, $i, $l) {
#TODO 暂时不对having条件做详细处理
	return $i >= $l;
}
#CP_FILTERS语法单元中的结束标记检查
function spa_end_filters($t, $i, $l) {
	return spa_is_order_by($t, $i) || spa_is_limit($t, $i) || $i >= $l;
}
#CP_FILTERS语法单元中的单条条件结束标记检查
function spa_end_filter($t, $i, $l) {
	return spa_end_filter_self($t, $i, $l) || spa_end_filters($t, $i, $l);
}
#CP_ORDERS语法单元中单条排序规则自身结束而非整个语法单元结束的检查
function spa_end_order_self($t, $i, $l) {
	return spa_is_comma($t, $i) || $i >= $l;
}
#CP_ORDERS语法单元中的结束标记检查
function spa_end_orders($t, $i, $l) {
	return spa_is_limit($t, $i) || $i >= $l;
}
#CP_ORDERS语法单元中的单条排序规则结束标记检查
function spa_end_order($t, $i, $l) {
	return spa_end_filter_self($t, $i, $l) || spa_end_orders($t, $i, $l);
}
#-----------------------------------------------语法分析-各个语法单元的读取
#将括号内容作为整体读取(这里主要做栈的处理)
function spa_read_bracket($t, &$i, $l, $include_bracket = FALSE) {
	$tmp	= array();
	if ( $include_bracket )
		$tmp[]	= $t[$i];
	$i ++;
	$stack	= 0;
	while ( !spa_is_rbracket($t, $i) || $stack > 0 ) {
		if ( spa_is_lbracket($t, $i) )
			$stack ++;
		else if ( spa_is_rbracket($t, $i) )
			$stack --;
		$tmp[]	= $t[$i ++];
	}
	if ( !spa_is_rbracket($t, $i) )
		sql_parser_error(__FILE__, __LINE__, __FUNCTION__);
	if ( $include_bracket )
		$tmp[]	= $t[$i];
	$i ++;
	return $tmp;
}
#读取SELECT关键字
function spa_read_select($t, &$i, $l, &$r) {
	if ( !spa_is_select($t, $i) )
		sql_parser_error(__FILE__, __LINE__, __FUNCTION__);
	$r[CP_SELECT]	= $t[$i ++];
}
#读取SELECT选项
function spa_read_options($t, &$i, $l, &$r) {
	while ( spa_is_option($t, $i) ) 
		$r[CP_OPTIONS][]	= $t[$i ++];
}
#读取查询字段
function spa_read_fields($t, &$i, $l, &$r) {
	while ( !spa_end_fields($t, $i, $l) ) {
		spa_read_field($t, $i, $l, $r);
		spa_read_field_separater($t, $i, $l, $r);
	}
	#SELECT语句至少需要一个查询字段
	if ( count($r[CP_FIELDS]) < 1 ) 
		sql_parser_error(__FILE__, __LINE__, __FUNCTION__);
}
#读取单个查询字段
function spa_read_field($t, &$i, $l, &$r) {
	$tmp	= array();
	while ( !spa_end_field($t, $i, $l) ) 
		if ( spa_is_lbracket($t, $i) ) {
			$tmp	= array_merge($tmp, spa_read_bracket($t, $i, $l, TRUE));
		} else 
			$tmp[]	= $t[$i ++];
	$r[CP_FIELDS][]	= $tmp;
}
#读取查询字段分隔符
function spa_read_field_separater($t, &$i, $l, &$r) {
	if ( spa_is_comma($t, $i, $n) ) {
		#$r[CP_FIELDS][]	= array_slice($t, $i, $n);
		$i	+= $n;
	}
}
#读取哦FROM关键字
function spa_read_from($t, &$i, $l, &$r) {
	if ( spa_is_from($t, $i) )
		$r[CP_FROM]	= $t[$i ++];
}
#读取要查询的表
function spa_read_tables($t, &$i, $l, &$r) {
	while ( !spa_end_tables($t, $i, $l) ) {
		$tmp	= array();
		spa_read_table_separater($t, $i, $l, $tmp);
		spa_read_table_name($t, $i, $l, $tmp);
		spa_read_table_alias($t, $i, $l, $tmp);
		spa_read_table_condition($t, $i, $l, $tmp);
		$r[CP_TABLES][]	= $tmp;
	}
}
#读取表名
function spa_read_table_name($t, &$i, $l, &$r) {
	if ( spa_is_lbracket($t, $i) ) {
		$r[CPK_TABLES_TABLE]	= sql_analysis(spa_read_bracket($t, $i, $l));
	} else {
		$r[CPK_TABLES_TABLE]	= array($t[$i ++]);
		if ( spa_is_dot($t, $i) ) 
			array_push($r[CPK_TABLES_TABLE], $t[$i ++], $t[$i ++]);
	}
}
#读取表的别名
function spa_read_table_alias($t, &$i, $l, &$r) {
	if ( spa_is_as($t, $i) )
		$i ++;
	if ( !spa_is_on($t, $i) && !spa_end_table($t, $i, $l) ) 
		$r[CPK_TABLES_ALIAS]	= $t[$i ++];
}
#读取表的连接条件
function spa_read_table_condition($t, &$i, $l, &$r) {
	if ( !spa_is_on($t, $i) ) return ;
	$i ++;
	while ( !spa_end_table($t, $i, $l) ) 
		if ( sp_is_lbracket($t, $i) )
			$r[CPK_TABLES_CONDITION]	= array_merge($r[CPK_TABLES_CONDITION], spa_read_bracket($t, $i, $l, TRUE));
		else 
			$r[CPK_TABLES_CONDITION][]	= $t[$i ++];
}
#读取表的连接符
function spa_read_table_separater($t, &$i, $l, &$r) {
	if ( spa_is_comma($t, $i, $n) || spa_is_join($t, $i, $n) ) {
		$r[CPK_TABLES_SEPARATER]	= array_slice($t, $i, $n);
		$i	+= $n;
	}
}
#读取WHERE关键字
function spa_read_where($t, &$i, $l, &$r) {
	if ( spa_is_where($t, $i) )
		$r[CP_WHERE]	= $t[$i ++];
}
#读取查询条件(WHERE)
function spa_read_conditions($t, &$i, $l, &$r) {
	while ( !spa_end_conditions($t, $i, $l) ) {
		$tmp	= array();
		spa_read_condition($t, $i, $l, $tmp);
		$r[CP_CONDITIONS][]	= $tmp;
	}
}
#读取单条查询条件(WHERE)
function spa_read_condition($t, &$i, $l, &$r) {
#TODO 对条件进行细节处理
	$tmp	= array();
	while( !spa_end_condition($t, $i, $l) ) 
		if ( spa_is_lbracket($t, $i) ) 
			$tmp	= array_merge($tmp, spa_read_bracket($t, $i, $l, TRUE));
		else 
			$tmp[]	= $t[$i ++];
	$r	= $tmp;
}
#读取GROUP BY关键字
function spa_read_group_by($t, &$i, $l, &$r) {
	if ( spa_is_group_by($t, $i) )
		$r[CP_GROUP_BY]	= $t[$i ++] . SPACE . $t[$i ++];
}
#读取分组项
function spa_read_groups($t, &$i, $l, &$r) {
	while ( !spa_end_groups($t, $i, $l) ) {
		$tmp	= array();
		spa_read_group_field($t, $i, $l, $tmp);
		spa_read_group_order($t, $i, $l, $tmp);
		$r[CP_GROUPS][]	= $tmp;
		spa_read_group_separater($t, $i, $l, $r[CP_GROUPS]);
	}
}
#读取单条分组项
function spa_read_group_field($t, &$i, $l, &$r) {
	$tmp	= array();
	while ( !spa_end_group($t, $i, $l) && !spa_is_asc($t, $i) && !spa_is_desc($t, $i) ) 
		if ( spa_is_lbracket($t, $i) ) 
			$tmp	= array_merge($tmp, spa_read_bracket($t, $i, $l, TRUE));
		else 
			array_push($tmp, $t[$i ++]);
	$r[CPK_GROUPS_FIELD]	= $tmp;
}
#读取分组项排序
function spa_read_group_order($t, &$i, $l, &$r) {
	if ( spa_is_desc($t, $i) || spa_is_asc($t, $i) )
		$r[CPK_GROUPS_ORDER]	= $t[$i ++];
}
#读取分组项分隔符
function spa_read_group_separater($t, &$i, $l, &$r) {
	if ( spa_is_comma($t, $i, $n) ) {
		#$r[]	= array_slice($t, $i, $n);
		$i		+= $n;
	}
}
#读取HAVING关键字
function spa_read_having($t, &$i, $l, &$r) {
	if ( spa_is_having($t, $i) )
		$r[CP_HAVING]	= $t[$i ++];
}
#读取过滤条件(HAVING)
function spa_read_filters($t, &$i, $l, &$r) {
	while ( !spa_end_filters($t, $i, $l) ) {
		$tmp	= array();
		spa_read_filter($t, $i, $l, $tmp);
		$r[CP_FILTERS][]	= $tmp;
	}
}
function spa_read_filter($t, &$i, $l, &$r) {
#TODO 对条件进行细节处理
	$tmp	= array();
	while ( !spa_end_filter($t, $i, $l) )
		if ( spa_is_lbracket($t, $i) )
			$tmp	= array_merge($tmp, spa_read_bracket($t, $i, $l, TRUE));
		else 
			$tmp[]	= $t[$i ++];
	$r	= $tmp;
}
#读取ORDER BY关键字
function spa_read_order_by($t, &$i, $l, &$r) {
	if ( spa_is_order_by($t, $i) ) 
		$r[CP_ORDER_BY]	= $t[$i ++] . SPACE . $t[$i ++];
}
#读取排序项
function spa_read_orders($t, &$i, $l, &$r) {
	while ( !spa_end_orders($t, $i, $l) ) {
		$tmp	= array();
		spa_read_order_field($t, $i, $l, $tmp);
		spa_read_order_order($t, $i, $l, $tmp);
		$r[CP_ORDERS][]	=  $tmp;
		spa_read_order_separater($t, $i, $l, $r[CP_ORDERS]);
	}
}
#读取排序项字段
function spa_read_order_field($t, &$i, $l, &$r) {
	$tmp	= array();
	while ( !spa_end_order($t, $i, $l) && !spa_is_asc($t, $i) && !spa_is_desc($t, $i) ) 
		if ( spa_is_lbracket($t, $i) ) 
			$tmp	= array_merge($tmp, spa_read_bracket($t, $i, $l, TRUE));
		else
			array_push($tmp, $t[$i ++]);
	$r[CPK_ORDERS_FIELD]	= $tmp;
}
#读取排序项顺序
function spa_read_order_order($t, &$i, $l, &$r) {
	if ( spa_is_asc($t, $i) || spa_is_desc($t, $i) ) 
		$r[CPK_ORDERS_ORDER]	= $t[$i ++];
}
#读取排序项分隔符
function spa_read_order_separater($t, &$i, $l, &$r) {
	if ( spa_is_comma($t, $i, $n) ) {
		#$r[]	= array_slice($t, $i, $n);
		$i		+= $n;
	}
}
#读取LIMIT关键字
function spa_read_limit($t, &$i, $l, &$r) {
	if ( spa_is_limit($t, $i, $l) )
		$r[CP_LIMIT]	= $t[$i ++];
}
#读取偏移参数
function spa_read_offset_count($t, &$i, $l, &$r) {
	$offset	= 0;
	if ( spa_is_comma($t, $i + 1) ) {
		$offset	= $t[$i];
		$i		+= 2;
	}
	$count	= $t[$i ++];
	$r[CP_OFFSET]	= $offset;
	$r[CP_COUNT]	= $count;
}
#-------------------------------------------SQL还原
#递归的将$pieces所有子项按照顺序用$glue连接
function sr_recursive_implode($glue, $pieces) {
	if ( !is_array($pieces) ) return $pieces;
	foreach ( $pieces as &$piece ) 
		if ( is_array($piece) )
			$piece	= sr_recursive_implode($glue, $piece);
	return implode_exclude_bracket($glue, $pieces);
}
function implode_exclude_bracket($glue, $pieces) {
	$ret	= '';
	if ( is_array($pieces) ) {
		$tmp	= array();
		foreach ( $pieces as $piece ) {
			if ( $piece === LBRACKET || $piece === RBRACKET ) {
				$ret	.= implode($glue, $tmp) . $piece;
				$tmp	= array();
			} else {
				$tmp[]	= $piece;
			}
		}
		$ret	.= implode($glue, $tmp);
	}
	return $ret;
}
#检查给定数据$a是否非空数组, 如果指定了$k则检查$a[$k]
function is_no_empty_array($a, $k = NULL) {
	if ( !is_null($k) ) 
		if ( !array_key_exists($k, $a) ) return FALSE;
		else $a = $a[$k];
	return is_array($a) && !empty($a);
}
function sr_restore_select($sc, &$r) {
	if ( array_key_exists(CP_SELECT, $sc) ) 
		array_push($r, KW_SELECT);
}
#还原选项
function sr_restore_options($sc, &$r) {
	if ( is_no_empty_array($sc, CP_OPTIONS) )
		array_push($r, sr_recursive_implode(SPACE, $sc[CP_OPTIONS]));
}
#还原查询字段
function sr_restore_fields($sc, &$r) {
	if ( is_no_empty_array($sc, CP_FIELDS) ) {
		$tmp	= array();
		foreach ( $sc[CP_FIELDS] as $field ) 
			array_push($tmp, sr_recursive_implode(SPACE, $field));
		array_push($r, implode_exclude_bracket(COMMA, $tmp));
	}
}
#还原表
function sr_restore_tables($sc, &$r) {
	if ( is_no_empty_array($sc, CP_TABLES) ) {
		$ret_arr	= array();
		foreach ( $sc[CP_TABLES] as $table ) {
			$tmp	= array();
			if ( array_key_exists(CPK_TABLES_SEPARATER, $table) )
				$tmp[]	= sr_recursive_implode(SPACE, $table[CPK_TABLES_SEPARATER]);
			if ( array_key_exists(CPK_TABLES_TABLE, $table) ) 
				if ( array_key_exists(CP_SELECT, $table[CPK_TABLES_TABLE]) )
					$tmp[]	= LBRACKET . sql_restore($table[CPK_TABLES_TABLE]) . RBRACKET;
				else
					$tmp[]	= sr_recursive_implode(SPACE, $table[CPK_TABLES_TABLE]);
			if ( array_key_exists(CPK_TABLES_ALIAS, $table) )
				$tmp[]	= KW_AS . SPACE . strval($table[CPK_TABLES_ALIAS]);
			if ( array_key_exists(CPK_TABLES_CONDITION, $table) )
				$tmp[]	= KW_ON . SPACE . sr_recursive_implode(SPACE, $table[CPK_TABLES_CONDITION]);
			$ret_arr[]	= implode_exclude_bracket(SPACE, $tmp);
		}
		array_push($r, KW_FROM . SPACE . implode_exclude_bracket(SPACE, $ret_arr));
	}
}
#还原条件
function sr_restore_conditions($sc, &$r) {
	if ( is_no_empty_array($sc, CP_CONDITIONS) ) 
		array_push($r, KW_WHERE . SPACE . sr_recursive_implode(SPACE, $sc[CP_CONDITIONS]));
}
#还原分组项
function sr_restore_groups($sc, &$r) {
	if ( is_no_empty_array($sc, CP_GROUPS) ) {
		$tmp	= array();
		foreach ( $sc[CP_GROUPS] as $group ) {
			$g	= sr_recursive_implode(SPACE, $group[CPK_GROUPS_FIELD]);
			if ( array_key_exists(CPK_GROUPS_ORDER, $group) )
				$g	.= SPACE . $group[CPK_GROUPS_ORDER];
			$tmp[]	= $g;
		}
		array_push($r, KW_GROUP_BY . SPACE . implode_exclude_bracket(COMMA, $tmp));
	}
}
#还原过滤条件
function sr_restore_filters($sc, &$r) {
	if ( is_no_empty_array($sc, CP_FILTERS) ) 
		array_push($r, KW_HAVING . SPACE . sr_recursive_implode(SPACE, $sc[CP_FILTERS]));
}
#还原排序项
function sr_restore_orders($sc, &$r) {
	if ( is_no_empty_array($sc, CP_ORDERS) ) {
		$tmp	= array();
		foreach ( $sc[CP_ORDERS] as $order ) {
			$o	= sr_recursive_implode(SPACE, $order[CPK_ORDERS_FIELD]);
			if ( array_key_exists(CPK_ORDERS_ORDER, $order) )
				$o	.= SPACE . $order[CPK_ORDERS_ORDER];
			$tmp[]	= $o;
		}
		array_push($r, KW_ORDER_BY . SPACE . implode_exclude_bracket(COMMA, $tmp));
	}
}
#还原偏移量
function sr_restore_offset_count($sc, &$r) {
	if ( array_key_exists(CP_COUNT, $sc) ) {
		$count	= intval($sc[CP_COUNT]);
		$offset	= intval(array_key_exists(CP_OFFSET, $sc) ? $sc[CP_OFFSET] : 0);
		array_push($r, KW_LIMIT . SPACE . $offset . COMMA . $count);
	}
}

#------------------------------------------------分页相关SQL重组子逻辑: sql_to_count相关
#将结构化SQL转换为计数的结构化SQL(不支持外连接)
function sc_to_count($sc, $alias = COUNT_DEFAULT_ALIAS) {
	#count字段构建
	$count_field	= sc_build_count_field($sc, $alias);
	#删除排序项
	sc_delete_orders($sc);
	#合并HAVING条件到WHERE
	$sc[CP_FIELDS]	= array($count_field);
	return $sc;
}
#构造count查询字段
function sc_build_count_field(&$sc, $alias = COUNT_DEFAULT_ALIAS) {
	#构造count统计查询
	$count_field	= is_no_empty_array($sc, CP_GROUPS)
					? sc_build_count_by_group($sc)
					: sc_build_count_normal($sc);
	#别名构建
	if ( !empty($alias) ) 
		array_push($count_field, KW_AS, $alias);
	return $count_field;
}
#根据分组构造count统计
function sc_build_count_by_group(&$sc) {
	$count_field	= array(
		KW_COUNT . LBRACKET, 
		KW_DISTINCT, 
		tidy_fields_to_one(read_group_fields($sc)),
		RBRACKET, 
	);
	unset($sc[CP_GROUP_BY]);
	unset($sc[CP_GROUPS]);
	return $count_field;
}
#无分组的count构造
function sc_build_count_normal(&$sc) {
	return array(
		KW_COUNT . LBRACKET, 
		'*', 
		RBRACKET
	);
}
#删除排序相关项
function sc_delete_orders(&$sc) {
	unset($sc[CP_ORDER_BY]);
	unset($sc[CP_ORDERS]);
}
#读取分组项中的字段信息
function read_group_fields($sc) {
	$tmp	= array();
	foreach ( $sc[CP_GROUPS] as $group ) 
		$tmp[]	= $group[CPK_GROUPS_FIELD];
	return $tmp;
}
#将多个字段信息整理成一个逗号分隔的大项(用于外层嵌套括号构成复杂字段)
function tidy_fields_to_one($fields) {
	$tmp	= array();
	foreach ( $fields as $field ) {
		array_push($tmp, $field);
		array_push($tmp, COMMA);
	}
	array_pop($tmp);
	return $tmp;
}

#------------------------------------------------分页相关SQL重组子逻辑: sql_add_since相关
#结构化SQL增加since_id
function sc_add_since(&$sc, $prev_id, $next_id, &$need_reverse) {
	$need_reverse	= FALSE;
	if ( is_null($prev_id) && is_null($next_id) ) 
		return $sc;
	#将排序项拷贝到查询字段中, 修正排序项为新的查询字段的别名
	cp_orders_to_fields($sc);
	$need_prev	= !is_null($prev_id) && !is_bool($prev_id);
	$need_next	= !is_null($next_id) && !is_bool($next_id);
	#如果是向前查找, 需要对排序项进行逆序, 并对返回结果进行逆序
	if ( $need_prev && !$need_next ) {
		sc_apply_since_id($sc, $prev_id, DIRECT_PREV);
		sc_orders_reverse($sc);
		$need_reverse	= TRUE;
	#如果是向后查找, 直接追加条件
	} else if ( !$need_prev && $need_next ) {
		sc_apply_since_id($sc, $next_id, DIRECT_NEXT);
	#如果是闭区间查找, 则反向追加条件
	} else if ( $need_prev && $need_next ) {
		sc_apply_since_id($sc, $prev_id, DIRECT_NEXT);
		sc_apply_since_id($sc, $next_id, DIRECT_PREV);
	}
	return $sc;
}
#将排序项复制到查询字段, 并自动设置别名, 使用别名作为排序项
function cp_orders_to_fields(&$sc) {
	#读取排序项中的字段信息
	$o_fields	= read_order_fields($sc);
	#将排序项转换成查询字段格式
	$a_fields	= convert_orders_to_fields($sc, $o_fields, $n_o_fields);
	#将排序项转换而来的查询字段合并到原始查询字段
	sc_merge_having_to_where($sc);
	#将排序项字段合并到查询字段
	$sc[CP_FIELDS]	= array_merge($sc[CP_FIELDS], $a_fields);
	#设置新的排序项为别名
	$sc[CP_ORDERS]	= $n_o_fields;
}
#将id描述的规则按照方向direction应用到sc中
function sc_apply_since_id(&$sc, $id, $direction) {
	$infos		= explain_since_id($id);
	if ( !array_key_exists(CP_FILTERS, $sc) ) {
		$sc[CP_HAVING]	= KW_HAVING;
		$sc[CP_FILTERS]	= array();
	} else {
		array_unshift($sc[CP_FILTERS], LBRACKET);
		array_push($sc[CP_FILTERS], RBRACKET, KW_AND);
	}
	$filters	= id_info_to_filter($sc, $infos, $direction);
	array_unshift($filters, LBRACKET);
	array_push($filters, RBRACKET);
	sc_append_filter($sc, $filters);
}
#读取排序项中的字段信息
function read_order_fields($sc) {
	$tmp	= array();
	foreach ( $sc[CP_ORDERS] as $group ) 
		$tmp[]	= $group[CPK_ORDERS_FIELD];
	return $tmp;
}
#将排序项转换为查询字段格式, 并输出它们的别名的排序项数据
function convert_orders_to_fields($sc, $o_fields, &$n_o_fields = NULL) {
	$tmp	= array();
	$i		= 0;
	$n_o_fields	= array();
	foreach ( $o_fields as &$o_field ) {
		$alias		= ORDER_ALIAS_PREFIX . $i;
		array_push($o_field, KW_AS, $alias);
		array_push($n_o_fields, array(
			CPK_ORDERS_FIELD	=> array($alias), 
			CPK_ORDERS_ORDER	=> array_key_exists(CPK_ORDERS_ORDER, $sc[CP_ORDERS][$i])
								? $sc[CP_ORDERS][$i][CPK_ORDERS_ORDER]
								: KW_ASC, 
		));
		$i ++;
	}
	return $o_fields;
}
#将HAVING条件合并到WHERE
function sc_merge_having_to_where(&$sc) {
	if ( array_key_exists(CP_FILTERS, $sc) ) {
		$conditions	= array();
		if ( array_key_exists(CP_CONDITIONS, $sc) ) {
			$conditions	= $sc[CP_CONDITIONS];
			array_unshift($conditions, LBRACKET);
			array_push($conditions, RBRACKET);
		}
		array_push($conditions, KW_AND);
		array_push($conditions, LBRACKET);
		$conditions	= array_merge($conditions, $sc[CP_FILTERS]);
		array_push($conditions, RBRACKET);
		$sc[CP_CONDITIONS]	= $conditions;
		unset($sc[CP_HAVING]);
		unset($sc[CP_FILTERS]);
	}
}
#将since_id还原为数组形式
function explain_since_id($id) {
	$tmp	= array();
	$items	= explode(SINCE_ID_SEPARATER_0, $id);
	foreach ( $items as $item ) {
		$item	= explode(SINCE_ID_SEPARATER_1, $item);
		$tmp[]	= array($item[0], $item[1]);
	}
	return $tmp;
}
#将since_id的数组描述转换为过滤条件
function id_info_to_filter($sc, $infos, $direction) {
	$tmp	= array();
	$i	= -1;
	$l	= count($infos);
	while ( ++ $i < $l ) {
		$order	= find_order_from_orders($sc, $infos[$i][0]);
		$sign	= $direction === DIRECT_PREV
				? (strtoupper($order) === KW_ASC ? KWS_LT : KWS_GT)
				: (strtoupper($order) === KW_ASC ? KWS_GT : KWS_LT);
		$t		= array();
		$j		= -1;
		while ( ++ $j < $i ) 
			array_push($t, $infos[$j][0], KWS_EQ, $infos[$j][1], KW_AND);
		array_unshift($t, LBRACKET);
		array_push($t, $infos[$i][0], $sign, $infos[$i][1], RBRACKET, KW_OR);
		$tmp	= array_merge($tmp, $t);
	}
	array_pop($tmp);
	return $tmp;
}
#在结构化SQL中追加一些过滤条件
function sc_append_filter(&$sc, $filters) {
	if ( empty($filters) ) return ;
	$sc[CP_HAVING]	= KW_HAVING;
	$original	= is_no_empty_array($sc, CP_FILTERS)
				? $sc[CP_FILTERS]
				: array();
	$sc[CP_FILTERS]	= array_merge($original, $filters);
}
#从结构化SQL的分组项中找到字段排序(TODO 这里的处理只适用于简单字段)
function find_order_from_orders($sc, $name) {
	foreach ( $sc[CP_ORDERS] as $o ) 
		if ( sr_recursive_implode(SPACE, $o[CPK_ORDERS_FIELD]) == $name ) 
			return array_key_exists(CPK_ORDERS_ORDER, $o) ? $o[CPK_ORDERS_ORDER] : KW_ASC;
	return KW_ASC;
}
#将结构化SQL中的所有ORDER项逆序
function sc_orders_reverse(&$sc) {
	if ( is_no_empty_array($sc, CP_ORDERS) ) {
		foreach ( $sc[CP_ORDERS] as &$item ) {
			if ( array_key_exists(CPK_ORDERS_ORDER, $item) && strtoupper($item[CPK_ORDERS_ORDER]) === KW_DESC )
				$item[CPK_ORDERS_ORDER]	= KW_ASC;
			else 
				$item[CPK_ORDERS_ORDER]	= KW_DESC;
		}
	}
}

#------------------------------------------------分页相关SQL重组子逻辑: mysql_paginate_query相关
#修正偏移量值
function correct_offset($offset) {
	return is_numeric($offset) ? intval($offset) : 0;
}
#为结构化的SQL增加偏移量部分
function sc_add_offset(&$sc, $offset, $count) {
	$sc[CP_LIMIT]	= KW_LIMIT;
	$sc[CP_OFFSET]	= $offset;
	$sc[CP_COUNT]	= $count;
	return $sc;
}
#使用结构化SQL和输出数据构造数据边界
function sc_datas_boundary($sc, &$datas, &$prev_id, &$next_id) {
	$prev	= $datas[0];
	$next	= $datas[count($datas) - 1];
	$orders	= orders_to_presentable($sc[CP_ORDERS]);
	$prev_id	= build_since_id($orders, $prev);
	$next_id	= build_since_id($orders, $next);
	delete_order_alias_datas($orders, $datas);
}
#将排序项转换为可读形式
function orders_to_presentable($orders) {
	$tmp	= array();
	foreach ( $orders as $order ) {
		$a	= sr_recursive_implode(SPACE, $order[CPK_ORDERS_FIELD]);
		$o	= array_key_exists(CPK_ORDERS_ORDER, $order)
			? strtoupper($order[CPK_ORDERS_ORDER])
			: KW_ASC;
		$tmp[$a]	= $o;
	}
	return $tmp;
}
#根据排序项可视结构和数据项构造标识
function build_since_id($orders, $data) {
	$id	= array();
	foreach ( $orders as $f => $o ) {
		if ( is_array($data) && array_key_exists($f, $data) ) {
			$id[]	= $f . SINCE_ID_SEPARATER_1 . $data[$f];
		}
	}
	return implode(SINCE_ID_SEPARATER_0, $id);
}
#删除排序项增加上去的字段
function delete_order_alias_datas($orders, &$datas) {
	foreach ( $orders as $f => $o ) 
		foreach ( $datas as &$data ) 
			if ( array_key_exists($f, $data) ) unset($data[$f]);
}

#-------------------------------------------------数据库操作
#查询单条记录
function mysql_geta($conn, $sql) {
	$rs		= mysql_query($sql, $conn);
	if ( mysql_errno($conn) )
		mysql_error_handler(mysql_errno($conn), mysql_error($conn));
	$row	= mysql_fetch_array($rs, MYSQL_ASSOC);
	if ( mysql_errno($conn) )
		mysql_error_handler(mysql_errno($conn), mysql_error($conn));
	return $row;
}
#查询多条记录
function mysql_geta_all($conn, $sql) {
	$rs		= mysql_query($sql, $conn);
	if ( mysql_errno($conn) )
		mysql_error_handler(mysql_errno($conn), mysql_error($conn));
	$rows	= array();
	while ( $row = mysql_fetch_array($rs, MYSQL_ASSOC) )
		if ( mysql_errno($conn) )
			mysql_error_handler(mysql_errno($conn), mysql_error($conn));
		else
			array_push($rows, $row);
	return $rows;
}

```

test_base.php


```php
<?php
/*
 * 分页程序单元测试基础库
 * 1. 断言环境设置
 * 2. 定义mysql的错误处理
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 */
require dirname(__FILE__) . '/page0.lib.php';
assert_options(ASSERT_ACTIVE, TRUE);
assert_options(ASSERT_WARNING, TRUE);
assert_options(ASSERT_CALLBACK, 'assert_handler');

function assert_info($file = NULL, $line = NULL, $sign = NULL) {
	static $msg;
	if ( func_num_args() > 1 )
		$msg	= sprintf(chr(10) . 'assert error at %s[%s]: %s', $file, $line, $sign);
	else 
		return $msg;
}
function assert_handler() {
	echo assert_info();
}
function mysql_error_handler($errno, $errstr) {
	echo $errstr . chr(10);
}

```

test_convert.php


```php
<?php
/*
 * 分页程序 分页之间的转换测试
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 */
require dirname(__FILE__) . '/test_base.php';
#ping_to_offset单元测试
function test_ping_to_offset() {
	$total_record	= 92;
	$count			= 10;
	$page_ping		= 3;
	$info			= ping_to_offset(-1, 1, $page_ping, $count, $total_record);
	assert(p_offset($info) === 0);
	$info			= ping_to_offset(3, 1, $page_ping, $count, $total_record);
	assert(p_offset($info) === 60);
	$info			= ping_to_offset(4, 1, $page_ping, $count, $total_record);
	assert(p_offset($info) === 90);
	$info			= ping_to_offset(5, 1, $page_ping, $count, $total_record);
	assert(p_offset($info) === 90);
	$info			= ping_to_offset(2, 3, $page_ping, $count, $total_record);
	assert(p_offset($info) === 50);
	$info			= ping_to_offset(4, 2, $page_ping, $count, $total_record);
	assert(p_offset($info) === 90);
}
#offset_to_ping单元测试
function test_offset_to_ping() {
	$page_ping	= 3;
	$count		= 10;
	$info		= offset_to_ping(0, $page_ping, $count);
	assert(p_page($info) === 1);
	assert(p_ping($info) === 1);
	$info		= offset_to_ping(10, $page_ping, $count);
	assert(p_page($info) === 1);
	assert(p_ping($info) === 2);
	$info		= offset_to_ping(20, $page_ping, $count);
	assert(p_page($info) === 1);
	assert(p_ping($info) === 3);
	$info		= offset_to_ping(30, $page_ping, $count);
	assert(p_page($info) === 2);
	assert(p_ping($info) === 1);
	$info		= offset_to_ping(40, $page_ping, $count);
	assert(p_page($info) === 2);
	assert(p_ping($info) === 2);
	$info		= offset_to_ping(50, $page_ping, $count);
	assert(p_page($info) === 2);
	assert(p_ping($info) === 3);
	$info		= offset_to_ping(60, $page_ping, $count);
	assert(p_page($info) === 3);
	assert(p_ping($info) === 1);
	$info		= offset_to_ping(70, $page_ping, $count);
	assert(p_page($info) === 3);
	assert(p_ping($info) === 2);
	$info		= offset_to_ping(90, $page_ping, $count);
	assert(p_page($info) === 4);
	assert(p_ping($info) === 1);
}
#page_to_offset单元测试
function test_page_to_offset() {
	$total_record	= 92;
	$count			= 10;
	$info			= page_to_offset(-1, $count, $total_record);
	assert(p_offset($info) === 0);
	$info			= page_to_offset(1, $count, $total_record);
	assert(p_offset($info) === 0);
	$info			= page_to_offset(3, $count, $total_record);
	assert(p_offset($info) === 20);
	$info			= page_to_offset(9, $count, $total_record);
	assert(p_offset($info) === 80);
	$info			= page_to_offset(10, $count, $total_record);
	assert(p_offset($info) === 90);
	$info			= page_to_offset(12, $count, $total_record);
	assert(p_offset($info) === 90);
}
#offset_to_page单元测试
function test_offset_to_page() {
	$count	= 10;
	$info	= offset_to_page(0, $count);
	assert(p_page($info) === 1);
	$info	= offset_to_page(30, $count);
	assert(p_page($info) === 4);
	$info	= offset_to_page(80, $count);
	assert(p_page($info) === 9);
	$info	= offset_to_page(90, $count);
	assert(p_page($info) === 10);
}

test_ping_to_offset();
test_offset_to_ping();
test_page_to_offset();
test_offset_to_page();

```

test_parse.php


```php
<?php
/*
 * 分页程序 SQL解析测试(由于处理简单, 可能会有某些SQL处理不了, 需要使用此工具先确认可用)
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 */
require dirname(__FILE__) . '/test_base.php';

function test_sql_parser($sql) {
	$components	= sql_parser($sql);
	echo '测试SQL转换:' . chr(10);
	echo sql_restore($components) . chr(10) . chr(10);
}
function test_sql_to_count($sql) {
	echo '测试SQL附加COUNT:' . chr(10);
	echo sql_to_count($sql) . chr(10) . chr(10);
}
function test_sql_add_since($sql, $prev_id = NULL, $next_id = NULL) {
	echo '测试SQL附加since_id条件:' . chr(10);
	echo sql_add_since($sql, $prev_id, $next_id, $need_reverse) . chr(10);
	if ( $need_reverse )
	echo '只按照prev_id附加since_id时, 需要对查询结果逆序' . chr(10);
	echo chr(10);
}

#样例SQL, 不关心语义正确性
$sql	= 'SELECT ALL HIGH_PRIORITY STRAIGHT_JOIN SQL_SMALL_RESULT SQL_BUFFER_RESULT SQL_CACHE SQL_CALC_FOUND_ROWS '
		. ' if(a.feed_id < 100, 100, 200) as t, f.feed_id d, f.content, `h`.`hot`, 3 + "\"\Hello FROM ,* ", *, \'world\''
		. ' FROM feed AS f INNER JOIN comment c ON f.feed_id = c.feed_id AND (1 + (2 -1)) * 3 > 4 LEFT JOIN (SELECT * FROM (SELECT * FROM hot) AS t) ON f.feed_id = h.feed_id, hot AS h1, hot h2, hot'
		. ' WHERE 1 = 2 AND hot IS NOT NULL OR (1 > 2 AND 3 < 4)'
		. ' GROUP by h.hot desc, f.feed_id, f.ctime ASc, (f.ctime + 1) + 3 desc'
		. ' HAVING 1 = 2 AND 2 AND (feed.feed_id < 5)'
		. ' ORDER by hot desc, f.feed_id * (100 + 2) asC, c.comment_id desc'
		. ' LIMIT 1, 3'
		;
if ( $argc > 1 )
	$sql	= $argv[1];
test_sql_parser($sql);
test_sql_to_count($sql);
test_sql_add_since($sql, $argv[2] ? $argv[2] : '__o_0:1948|__o_1:333333', $argv[3]);

echo '原始SQL:' . chr(10);
echo $sql . chr(10);

```

test_page.php


```php
<?php
/*
 * 分页程序 分页测试
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 */
require dirname(__FILE__) . '/test_base.php';

#初始化数据量
define('FEED_ID_MIN',				1000);
define('FEED_COUNT',				972);
define('COMMENT_ID_MIN',			1000);
define('COMMENT_MAX_COUNT',			10);
define('HOT_MIN',					1);
define('HOT_MAX',					100);
define('TRANSPOND_COUNT_MAX',		100);

#数据库信息
$db_host	= '127.0.0.1';
$db_port	= '3306';
$db_user	= 'paginate_test';
$db_pass	= 'paginate_test';
$db_db		= 'paginate_test';
$db_charset	= 'UTF-8';

#全局数据变量名
define('ALL_DATAS',					'_all_datas');

#涉及到的key
define('K_HC_FEEDS',				'_hc_feed');
define('K_TC_FEEDS',				'_tc_feed');
define('K_CC_FEEDS',				'_cc_feed');
define('K_FEED_ID',					'feed_id');

#转发比较函数
function comp_transpond_count($a, $b) {
	$tc	= $b['transpond_count'] - $a['transpond_count'];
	$ic	= $b['feed_id'] - $a['feed_id'];
	return $tc != 0 ? $tc : $ic;
}
#评论比较函数
function comp_comment_count($a, $b) {
	$cc	= $b['comment_count'] - $a['comment_count'];
	$ic	= $b['feed_id'] - $a['feed_id'];
	return $cc != 0 ? $cc : $ic;
}
#热点比较函数
function comp_hot($a, $b) {
	$hc	= $b['hot'] - $a['hot'];
	$ic	= $b['feed_id'] - $a['feed_id'];
	return $hc != 0 ? $hc : $ic;
}

#数据库初始化临时文件
$tmp_file	= '/tmp/__paginate_test_tmp.sql';
#数据库创建脚本
$db_init	= <<<doc
DROP DATABASE IF EXISTS `paginate_test`;
CREATE DATABASE IF NOT EXISTS `paginate_test`;
USE `paginate_test`;

DROP TABLE IF EXISTS `feed`;
CREATE TABLE IF NOT EXISTS `feed` (
	`feed_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '微博ID', 
	`ctime` INT NOT NULL COMMENT '微博创建时间', 
	`content` CHAR(100) NOT NULL DEFAULT '' COMMENT '微博内容', 
	`transpond_count` INT NOT NULL DEFAULT 0 COMMENT '微博转发数'
) COMMENT '微博表';

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
	`comment_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '评论ID', 
	`content` CHAR(100) NOT NULL DEFAULT '' COMMENT '评论内容', 
	`feed_id` INT NOT NULL COMMENT '被评论微博ID'
) COMMENT '评论表';

DROP TABLE IF EXISTS `hot`;
CREATE TABLE IF NOT EXISTS `hot` (
	`feed_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '微博ID', 
	`hot` INT NOT NULL DEFAULT 0 COMMENT '微博热度'
) COMMENT '热点微博表';
doc;

#初始化数据库
function init_db() {
	global $db_host, $db_port, $db_user, $db_pass, $db_db, $db_init, $tmp_file;
	$datas	= generate_data();
	file_put_contents($tmp_file, $db_init . chr(10) . data_to_sql($datas));
	`mysql -u$db_user -p$db_pass -h$db_host -P$db_port $db_db -e 'SOURCE $tmp_file' && rm $tmp_file`;
	$tc_datas	= $datas;
	$cc_datas	= $datas;
	$hc_datas	= $datas;
	foreach ( $cc_datas as $k => $v ) 
		if ( count($v['comments']) <= 0 ) 
			unset($cc_datas[$k]);
	usort($tc_datas, 'comp_transpond_count');
	usort($cc_datas, 'comp_comment_count');
	usort($hc_datas, 'comp_hot');
	$GLOBALS[ALL_DATAS]	= array(
		K_TC_FEEDS	=> $tc_datas, 
		K_CC_FEEDS	=> $cc_datas, 
		K_HC_FEEDS	=> $hc_datas, 
	);
}
#生成测试数据
function generate_data() {
	$i		= -1;
	$j		= 0;
	$feeds	= array();
	while ( ++ $i < FEED_COUNT ) {
		$feed_id			= FEED_ID_MIN + $i;
		$ctime				= time();
		$transpond_count	= rand(0, TRANSPOND_COUNT_MAX);
		$hot				= rand(HOT_MIN, HOT_MAX);
		$comments			= array();
		$comment_count		= rand(0, COMMENT_MAX_COUNT);
		$k					= -1;
		while ( ++ $k < $comment_count ) {
			$comment_id		= COMMENT_ID_MIN + $j ++;
			$comments[]	= array(
				'content'		=> sprintf('cid: %d, fid: %d, ccnt: %d, tcnt: %d, hot: %d', $comment_id, $feed_id, $comment_count, $transpond_count, $hot), 
				'comment_id'	=> $comment_id, 
			);
		}
		$feeds[]	= array(
			'feed_id'			=> $feed_id, 
			'content'			=> sprintf('fid: %d, ccnt: %d, tcnt: %d, hot: %d', $feed_id, $comment_count, $transpond_count, $hot), 
			'comments'			=> $comments, 
			'comment_count'		=> $comment_count, 
			'transpond_count'	=> $transpond_count, 
			'hot'				=> $hot, 
			'ctime'				=> $ctime, 
		);
	}
	return $feeds;
}
#将生成的测试数据转换为sql语句
function data_to_sql($feeds) {
	$feed_sql		= 'INSERT INTO feed(feed_id, ctime, content, transpond_count) VALUES ';
	$comment_sql	= 'INSERT INTO comment(comment_id, content, feed_id) VALUES ';
	$hot_sql		= 'INSERT INTO hot(feed_id, hot) VALUES ';
	foreach ( $feeds AS $feed ) {
		$feed_sql	.= sprintf('(%d, %d, "%s", %d), ', $feed['feed_id'], $feed['ctime'], $feed['content'], $feed['transpond_count']);
		if ( !empty($feed['comments']) )
			foreach ( $feed['comments'] as $comment ) 
				$comment_sql	.= sprintf('(%d, "%s", %d), ', $comment['comment_id'], $comment['content'], $feed['feed_id']);
		$hot_sql	.= sprintf('(%d, %d), ', $feed['feed_id'], $feed['hot']);
	}
	return substr($feed_sql, 0, strlen($feed_sql) - 2) . ";\n" . substr($comment_sql, 0, strlen($comment_sql) - 2) .  ";\n" . substr($hot_sql, 0, strlen($hot_sql) - 2);
}
#格式化打印测试数据
function print_feeds($feeds) {
	foreach ( $feeds as $feed ) {
		printf("\tfeed_id: %d, transpond_count: %d, hot: %d, content: '%s'\n", $feed['feed_id'], $feed['transpond_count'], $feed['hot'], $feed['content']);
		foreach ( $feed['comments'] as $comment ) {
			printf("\t\tcomment_id: %d, content: '%s'\n", $comment['comment_id'], $comment['content']);
		}
	}
}
#格式化打印所有测试数据
function print_all_feeds($all_feeds) {
	foreach ( $all_feeds as $key => $feeds ) {
		printf("order type: %s\n", $key);
		print_feeds($feeds);
		printf("\n\n");
	}
}
#基本断言函数
function assert_base($info, $datas, $offset, $count, $file, $line, $sign) {
	assert_info($file, $line, $sign . ':count');
	assert(intval($count) === count(p_datas($info)));
	assert_info($file, $line, $sign . ':datas');
	if ( is_array(p_datas($info)) )
	foreach ( p_datas($info) as $data ) 
		assert(intval($data[K_FEED_ID]) == intval($datas[$offset ++][K_FEED_ID]));
}
#断言热门数据
function assert_hot($info, $offset, $count, $file, $line) {
	assert_base($info, $GLOBALS[ALL_DATAS][K_HC_FEEDS], $offset, $count, $file, $line, __FUNCTION__);
}
#断言热转数据
function assert_transpond($info, $offset, $count, $file, $line) {
	assert_base($info, $GLOBALS[ALL_DATAS][K_TC_FEEDS], $offset, $count, $file, $line, __FUNCTION__);
}
#断言热评数据
function assert_comment($info, $offset, $count, $file, $line) {
	assert_base($info, $GLOBALS[ALL_DATAS][K_CC_FEEDS], $offset, $count, $file, $line, __FUNCTION__);
}

#初始化数据库连接
function init_conn() {
	global $db_host, $db_port, $db_user, $db_pass, $db_db, $db_charset;
	static $conn;
	if ( is_null($conn) ) {
		$conn	= mysql_connect($db_host . ':' . $db_port, $db_user, $db_pass);
		mysql_select_db($db_db, $conn);
		mysql_set_charset($db_charset, $conn);
	}
	return $conn;
}

#单纯传统分页测试
function test_tradition($sql, $assert, $total_record) {
	$total_page	= ceil($total_record / 10);
	$remain		= $total_record % 10 ? $total_record % 10 : 10;
	$info	= mysql_paginate_tradition(init_conn(), $sql, -1, 10);
	$assert($info, 0, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition(init_conn(), $sql, 1, 10);
	$assert($info, 0, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition(init_conn(), $sql, 2, 10);
	$assert($info, 10, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition(init_conn(), $sql, 2, 10);
	$assert($info, 10, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition(init_conn(), $sql, $total_page - 1, 10);
	$assert($info, ($total_page - 2) * 10, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition(init_conn(), $sql, $total_page, 10);
	$assert($info, ($total_page - 1) * 10, $remain, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition(init_conn(), $sql, $total_page + rand(1, 10), 10);
	$assert($info, ($total_page - 1) * 10, $remain, __FILE__, __LINE__);
}
#单纯分段分页测试
function test_ping($sql, $assert, $total_record) {
	$total_ping	= ceil($total_record / 10);
	$total_page	= ceil($total_ping / 3);
	$remain		= $total_record % 10 ? $total_record % 10 : 10;
	$remain_ping	= $total_ping % 3 ? $total_ping % 3 : 3;
	$info	= mysql_paginate_ping(init_conn(), $sql, -1, -1, 3, 10);
	$assert($info, 0, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping(init_conn(), $sql, -1, 2, 3, 10);
	$assert($info, 10, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping(init_conn(), $sql, 1, 3, 3, 10);
	$assert($info, 20, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping(init_conn(), $sql, 1, 4, 3, 10);
	$assert($info, 20, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping(init_conn(), $sql, 5, 2, 3, 10);
	$assert($info, 130, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping(init_conn(), $sql, $total_page, $remain_ping, 3, 10);
	$assert($info, ($total_ping - 1) * 10, $remain, __FILE__, __LINE__);
	$info	= mysql_paginate_ping(init_conn(), $sql, $total_page + rand(1, 10), $remain_ping, 3, 10);
	$assert($info, ($total_ping - 1) * 10, $remain, __FILE__, __LINE__);
}
#单纯原始分页测试
function test_raw($sql, $assert, $total_record) {
	$remain		= $total_record % 10 ? $total_record % 10 : 10;
	$info	= mysql_paginate_raw(init_conn(), $sql, -1, 10);
	$assert($info, 0, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_raw(init_conn(), $sql, 100, 10);
	$assert($info, 100, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_raw(init_conn(), $sql, ($total_record - $remain), 10);
	$assert($info, ($total_record - $remain), $remain, __FILE__, __LINE__);
}
#单纯since_id分页测试
function test_since_id($sql, $assert) {
	#第一页
	$info_0	= mysql_paginate_since_id(init_conn(), $sql, 10);
	$assert($info_0, 0, 10, __FILE__, __LINE__);
	#无数据(第一页之前
	$info_1	= mysql_paginate_since_id(init_conn(), $sql, 10, p_prev_id($info_0));
	$assert($info_1, 0, 0, __FILE__, __LINE__);
	#第二页
	$info_2	= mysql_paginate_since_id(init_conn(), $sql, 10, NULL, p_next_id($info_0));
	$assert($info_2, 10, 10, __FILE__, __LINE__);
	#第三页
	$info_3	= mysql_paginate_since_id(init_conn(), $sql, 10, NULL, p_next_id($info_2));
	$assert($info_3, 20, 10, __FILE__, __LINE__);
	#第四页
	$info_4	= mysql_paginate_since_id(init_conn(), $sql, 10, NULL, p_next_id($info_3));
	$assert($info_4, 30, 10, __FILE__, __LINE__);
	#第三页
	$info_5	= mysql_paginate_since_id(init_conn(), $sql, 10, p_prev_id($info_4), NULL);
	$assert($info_5, 20, 10, __FILE__, __LINE__);
	#第二页(第二页至第三页之间的前10条)
	$info_5	= mysql_paginate_since_id(init_conn(), $sql, 10, p_next_id($info_0), p_prev_id($info_4));
	$assert($info_5, 10, 10, __FILE__, __LINE__);
}
#传统分页复合since_id分页测试
function test_tradition_since_id($sql, $assert, $total_record) {
	$total_page	= ceil($total_record / 10);
	$remain		= $total_record % 10 ? $total_record % 10 : 10;
	$info	= mysql_paginate_tradition_since_id(init_conn(), $sql, -1, 10);
	$assert($info, 0, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition_since_id(init_conn(), $sql, 2, 10);
	$assert($info, 10, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition_since_id(init_conn(), $sql, $total_page - 1, 10);
	$assert($info, ($total_page - 2) * 10, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_tradition_since_id(init_conn(), $sql, $total_page, 10);
	$assert($info, ($total_page - 1) * 10, $remain, __FILE__, __LINE__);
	#第一页
	$info_0	= mysql_paginate_tradition_since_id(init_conn(), $sql, 1, 10, TRUE, TRUE);
	$assert($info_0, 0, 10, __FILE__, __LINE__);
	#无数据(第一页之前)
	$info_1	= mysql_paginate_tradition_since_id(init_conn(), $sql, 1, 10, p_prev_id($info_0));
	$assert($info_1, 0, 0, __FILE__, __LINE__);
	#第二页
	$info_2	= mysql_paginate_tradition_since_id(init_conn(), $sql, 1, 10, NULL, p_next_id($info_0));
	$assert($info_2, 10, 10, __FILE__, __LINE__);
	#第三页
	$info_3	= mysql_paginate_tradition_since_id(init_conn(), $sql, 1, 10, NULL, p_next_id($info_2));
	$assert($info_3, 20, 10, __FILE__, __LINE__);
	#第二页
	$info_4	= mysql_paginate_tradition_since_id(init_conn(), $sql, 1, 10, p_prev_id($info_3));
	$assert($info_4, 10, 10, __FILE__, __LINE__);
	#第四页
	$info_5	= mysql_paginate_tradition_since_id(init_conn(), $sql, 1, 10, NULL, p_next_id($info_3));
	$assert($info_5, 30, 10, __FILE__, __LINE__);
	#第三页
	$info_6	= mysql_paginate_tradition_since_id(init_conn(), $sql, 2, 10, NULL, p_next_id($info_0));
	$assert($info_6, 20, 10, __FILE__, __LINE__);
	#第八页
	$info_7	= mysql_paginate_tradition_since_id(init_conn(), $sql, 7, 10, NULL, p_next_id($info_0));
	$assert($info_7, 70, 10, __FILE__, __LINE__);
	#最后一页
	$info_8	= mysql_paginate_tradition_since_id(init_conn(), $sql, $total_page - 1, 10, NULL, p_next_id($info_0));
	$assert($info_8, ($total_page - 1) * 10, $remain, __FILE__, __LINE__);
	#(TODO 分段分页/传统分页/原始分页与since_id分页联合使用时, 记录数是since_id条件附加之前的记录数, 因此会导致页码数据错乱, 目前不对此进行处理)
	#越界访问
	$info_8	= mysql_paginate_tradition_since_id(init_conn(), $sql, $total_page, 10, NULL, p_next_id($info_0));
	$assert($info_8, 0, 0, __FILE__, __LINE__);
}
#分段分页复合since_id分页测试
function test_ping_since_id($sql, $assert, $total_record) {
	$total_ping	= ceil($total_record / 10);
	$total_page	= ceil($total_ping / 3);
	$remain		= $total_record % 10 ? $total_record % 10 : 10;
	$remain_ping	= $total_ping % 3 ? $total_ping % 3 : 3;
	$info	= mysql_paginate_ping_since_id(init_conn(), $sql, -1, -1, 3, 10);
	$assert($info, 0, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping_since_id(init_conn(), $sql, -1, 2, 3, 10);
	$assert($info, 10, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 3, 3, 10);
	$assert($info, 20, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 4, 3, 10);
	$assert($info, 20, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping_since_id(init_conn(), $sql, 5, 2, 3, 10);
	$assert($info, 130, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_ping_since_id(init_conn(), $sql, $total_page, $remain_ping, 3, 10);
	$assert($info, ($total_ping - 1) * 10, $remain, __FILE__, __LINE__);
	$info	= mysql_paginate_ping_since_id(init_conn(), $sql, $total_page + rand(1, 10), $remain_ping, 3, 10);
	$assert($info, ($total_ping - 1) * 10, $remain, __FILE__, __LINE__);
	#第一页第一段
	$info_0	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 1, 3, 10, TRUE, TRUE);
	$assert($info_0, 0, 10, __FILE__, __LINE__);
	#无数据(第一页之前)
	$info_1	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 1, 3, 10, p_prev_id($info_0));
	$assert($info_1, 0, 0, __FILE__, __LINE__);
	#第一页第二段
	$info_2	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 1, 3, 10, NULL, p_next_id($info_0));
	$assert($info_2, 10, 10, __FILE__, __LINE__);
	#第一页第三段
	$info_3	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 1, 3, 10, NULL, p_next_id($info_2));
	$assert($info_3, 20, 10, __FILE__, __LINE__);
	#第一页第二段
	$info_4	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 1, 3, 10, p_prev_id($info_3));
	$assert($info_4, 10, 10, __FILE__, __LINE__);
	#第一页第一段
	$info_5	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 1, 3, 10, NULL, p_next_id($info_3));
	$assert($info_5, 30, 10, __FILE__, __LINE__);
	#第一页第三段
	$info_6	= mysql_paginate_ping_since_id(init_conn(), $sql, 1, 2, 3, 10, NULL, p_next_id($info_0));
	$assert($info_6, 20, 10, __FILE__, __LINE__);
	#第七页第三段
	$info_7	= mysql_paginate_ping_since_id(init_conn(), $sql, 7, 2, 3, 10, NULL, p_next_id($info_0));
	$assert($info_7, 200, 10, __FILE__, __LINE__);
	#倒数第二页最后一段
	$info_9	= mysql_paginate_ping_since_id(init_conn(), $sql, $total_page - 1, 2, 3, 10, NULL, p_next_id($info_0));
	$assert($info_9, ($total_ping - $remain_ping - 1) * 10, 10, __FILE__, __LINE__);
	#(TODO 分段分页/传统分页/原始分页与since_id分页联合使用时, 记录数是since_id条件附加之前的记录数, 因此会导致页码数据错乱, 目前不对此进行处理)
	#越界访问
	$info_10	= mysql_paginate_ping_since_id(init_conn(), $sql, $total_page + rand(1, 10), 3, 3, 10, NULL, p_next_id($info_0));
	$assert($info_10, 0, 0, __FILE__, __LINE__);
}
#原始分页复合since_id分页测试
function test_raw_since_id($sql, $assert, $total_record) {
	$remain		= $total_record % 10 ? $total_record % 10 : 10;
	$info	= mysql_paginate_raw_since_id(init_conn(), $sql, -1, 10);
	$assert($info, 0, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_raw_since_id(init_conn(), $sql, 100, 10);
	$assert($info, 100, 10, __FILE__, __LINE__);
	$info	= mysql_paginate_raw_since_id(init_conn(), $sql, $total_record - $remain, 10);
	$assert($info, $total_record - $remain, $remain, __FILE__, __LINE__);
	#第一页
	$info_0	= mysql_paginate_raw_since_id(init_conn(), $sql, 0, 10, TRUE, TRUE);
	$assert($info_0, 0, 10, __FILE__, __LINE__);
	#无数据(第一页之前)
	$info_1	= mysql_paginate_raw_since_id(init_conn(), $sql, 0, 10, p_prev_id($info_0));
	$assert($info_1, 0, 0, __FILE__, __LINE__);
	#第二页
	$info_2	= mysql_paginate_raw_since_id(init_conn(), $sql, 0, 10, NULL, p_next_id($info_0));
	$assert($info_2, 10, 10, __FILE__, __LINE__);
	#第三页
	$info_3	= mysql_paginate_raw_since_id(init_conn(), $sql, 0, 10, NULL, p_next_id($info_2));
	$assert($info_3, 20, 10, __FILE__, __LINE__);
	#第二页
	$info_4	= mysql_paginate_raw_since_id(init_conn(), $sql, 0, 10, p_prev_id($info_3));
	$assert($info_4, 10, 10, __FILE__, __LINE__);
	#第四页
	$info_5	= mysql_paginate_raw_since_id(init_conn(), $sql, 0, 10, NULL, p_next_id($info_3));
	$assert($info_5, 30, 10, __FILE__, __LINE__);
	#最后一页
	$info_6	= mysql_paginate_raw_since_id(init_conn(), $sql, $total_record - $remain - 10, 10, NULL, p_next_id($info_0));
	$assert($info_6, $total_record - $remain, $remain, __FILE__, __LINE__);
	#(TODO 分段分页/传统分页/原始分页与since_id分页联合使用时, 记录数是since_id条件附加之前的记录数, 因此会导致页码数据错乱, 目前不对此进行处理)
	#越界访问
	$info_7	= mysql_paginate_raw_since_id(init_conn(), $sql, 970, 10, NULL, p_next_id($info_0));
	$assert($info_7, 0, 0, __FILE__, __LINE__);
}

#初始化数据
init_db();

$h_sql	= 'SELECT f.feed_id, f.content, h.hot FROM feed AS f JOIN hot AS h ON f.feed_id = h.feed_id ORDER BY h.hot DESC, f.feed_id DESC';
$c_sql	= 'SELECT f.feed_id, f.content, COUNT(c.comment_id) AS count FROM feed AS f JOIN comment AS c ON f.feed_id = c.feed_id GROUP BY c.feed_id ORDER BY COUNT(c.comment_id) DESC, f.feed_id DESC';
$t_sql	= 'SELECT feed_id, content, transpond_count FROM feed ORDER BY transpond_count DESC, feed_id DESC';

$data_infos	= array(
	array($c_sql, 'assert_comment', count($GLOBALS[ALL_DATAS][K_CC_FEEDS])), 
	array($h_sql, 'assert_hot', count($GLOBALS[ALL_DATAS][K_HC_FEEDS])), 
	array($t_sql, 'assert_transpond', count($GLOBALS[ALL_DATAS][K_TC_FEEDS])), 
);
$use_cases	= array(
	'test_tradition', 'test_ping', 'test_raw', 'test_since_id', 
	'test_tradition_since_id', 'test_ping_since_id', 'test_raw_since_id', 
);

foreach ( $data_infos as $data_info ) {
	foreach ( $use_cases as $use_case )
		call_user_func_array($use_case, $data_info);
}

```


