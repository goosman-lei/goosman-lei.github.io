需求相关: 公司20人左右, 每天中午, 下午两次订餐, 都是助理在群里说"开始订餐", 然后大家报菜名, 然后助理统计, 打电话.  今天, 助理说"开始订餐, 订什么密聊...", 突然就想做这么个东西....
 
耗时: 4.5小时
 
评估: 时间太短, 不能做到很好, 没有什么输入验证之类, 就自己公司内部一点人, 用用应该没啥问题.....bug估计很多...应该是没时间改bug的, 不过有不足之处请大家提出, 互相学习.
 
提醒: 由于我这边和服务器有个时差, 所以, 里面有代码在倒时差.....另外, 13点之前被认为是上午, 之后包括13点被认为是下午, 一天两次.
 
本文涉及到的插件:[jQuery](http://jquery.com),[jQuery.UI](http://jqueryui.com),[fullcalendar](http://arshaw.com/fullcalendar)
 
数据库创建:
CREATE TABLE USER
(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NAME VARCHAR(100)
);
CREATE TABLE ITEM
(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    USER_ID INT,
    FOOD TINYBOLB,
    ORDER_TIME TIMESTAMP
);
ALTER TABLE ITEM ADD FOREIGN KEY(USER_ID) REFERENCES USER(ID) ON DELETE CASCADE;
 
一个实体的基类, 封装的不太好...呵呵

```php
&lt;?php
/**
 * 实体基类
 * @author selfimpr
 * @blog http://blog.csdn.net/lgg201
 * @email goosman.lei@gmail.com
 *
 */
class Entity {
	protected static $_mapping = array();
	public static function getField($column_name) {
		return self::$_mapping[$column_name] ? self::$_mapping[$column_name] : $column_name;
	}
	public function set($field, $value) {
		$method = 'set'.ucwords($field);
		return $this-&gt;$method($value);
	}
	public function get() {
		$method = 'get'.ucwords($field);
		return $this-&gt;$method();
	}
}
?&gt;
```

 
用户类(这个里面是没有权限, 登录的概念的, 就公司内部一个小东西)

```php
&lt;?php
require_once './Entity.php';
/**
 * 用户实体类
 * @author selfimpr
 * @blog http://blog.csdn.net/lgg201
 * @email goosman.lei@gmail.com
 *
 */
class User extends Entity {
	private $id;
	private $name;
	
	public function User($id = NULL, $name = NULL) {
		$this-&gt;id = $id;
		$this-&gt;name = $name;
	}
	
	public function getId() {
		return $this-&gt;id;
	}
	public function setId($id) {
		$this-&gt;id = $id;
		return $this;
	}
	public function getName() {
		return $this-&gt;name;
	}
	public function setName($name) {
		$this-&gt;name = $name;
		return $this;
	}
}
?&gt;
```

 
订餐订单项类

```php
&lt;?php
require_once './Entity.php';
/**
 * 订餐订单
 * @author selfimpr
 * @blog http://blog.csdn.net/lgg201
 * @email goosman.lei@gmail.com
 *
 */
class Item extends Entity {
	private $id;
	private $user;
	private $food;
	private $orderTime;
	
	public function Item($id = NULL, $food = NULL, $orderTime = NULL) {
		$this-&gt;id = $id;
		$this-&gt;food = $food;
		$this-&gt;orderTime = $orderTime;
	}
	
	public function getId() {
		return $this-&gt;id;
	}
	public function setId($id) {
		$this-&gt;id = $id;
		return $this;
	}
	public function getUser() {
		return $this-&gt;user;
	}
	public function setUser($user) {
		$this-&gt;user = $user;
		return $this;
	}
	public function getFood() {
		return $this-&gt;food;
	}
	public function setFood($food) {
		$this-&gt;food = $food;
		return $this;
	}
	public function getOrderTime() {
		return $this-&gt;orderTime;
	}
	public function setOrderTime($orderTime) {
		$this-&gt;orderTime = $orderTime;
		return $this;
	}
}
?&gt;
```

 
数据库工具类

```php
&lt;?php
/**
 * Mysql工具类
 * @author selfimpr
 * @blog http://blog.csdn.net/lgg201
 * @email goosman.lei@gmail.com
 *
 */
class Mysql {
	private $host;
	private $username;
	private $password;
	private $db_name;
	private $character_set;
	
	public function __construct(
			$host = &quot;192.168.2.10&quot;,
			$username = &quot;root&quot;, 
			$password = &quot;jhx&quot;, 
			$db_name = &quot;eat&quot;, 
			$character_set = &quot;GBK&quot;) {
		$this-&gt;host = $host;
		$this-&gt;username = $username;
		$this-&gt;password = $password;
		$this-&gt;db_name = $db_name;
		$this-&gt;character_set = $character_set;
	}
	
	public function getConn() {
		$conn = mysql_connect($this-&gt;host, $this-&gt;username, $this-&gt;password) or die('数据库连接失败');
		mysql_select_db($this-&gt;db_name, $conn);
		mysql_set_charset($this-&gt;character_set, $conn);
		return $conn;
	}
	
	/**
	 * 
	 * @param $sql 查询用的sql
	 * @param $result 查询得到的结果集, 如果UPDATE, INSERT, DELETE 不同提供这个参数
	 * @return SELECT 返回查询得到的条数, UPDATE, INSERT, DELETE返回bool型, 标识是否执行成功.
	 */
	public function execute($sql) {
		$conn = $this-&gt;getConn();
		$resultset = mysql_query($sql, $conn) or die(mysql_error($conn));
		if(!is_bool($resultset)) {
			while($line = mysql_fetch_assoc($resultset)) {
				$result[] = $line;
			}
		} else {
			$result = $resultset;
		}
		
		mysql_close($conn) or die(mysql_error($conn));
		
//		echo &quot;【&quot;.$sql.&quot;】 excute success!&lt;br /&gt;&quot;;
		
		return $result;
	}
}
?&gt;
```

 
业务逻辑(没时间了就写一个文件里了)

```php
&lt;?php
require_once './mysql_util.php';
require_once './User.php';
require_once './Item.php';
/**
 * 获取当前库中所有的用户
 * @return unknown_type
 */
function getUsers() {
	$util = new Mysql();
	$users_data = $util-&gt;execute(&quot;SELECT * FROM USER&quot;);
	foreach($users_data as $index =&gt; $data) {
		$user = new User();
		foreach($data as $column_name =&gt; $value) {
			$user-&gt;set(User::getField($column_name), $value);
		}
		$users[] = $user;
	}
	return $users;
}
/**
 * 根据名字增加一个用户
 * @param $name
 * @return unknown_type
 */
function addUser($name) {
	$util = new Mysql();
	$name = iconv(&quot;UTF-8&quot;, &quot;gbk&quot; , $name);
	return $util-&gt;execute(&quot;INSERT INTO USER VALUES(NULL, '$name')&quot;);
}
/**
 * 根据id删除一个用户
 * @param $id
 * @return unknown_type
 */
function deleteUser($id) {
	$util = new Mysql();
	return $util-&gt;execute(&quot;DELETE FROM USER WHERE ID = $id&quot;);
}
/**
 * 根据id修改姓名
 * @param $id
 * @param $name
 * @return unknown_type
 */
function updateUser($id, $name) {
	$util = new Mysql();
	return $util-&gt;execute(&quot;UPDATE USER SET NAME = '$name' WHERE ID = $id&quot;);
}
/**
 * 插入一条订餐记录
 * @param $user_id
 * @param $food
 * @return unknown_type
 */
function addItem($user_id, $food, $ordertime) {
	$util = new Mysql();
	$year = date('Y', $ordertime);
	$month = date('n', $ordertime);
	$day = date('j', $ordertime);
	$hour = (date('G', $ordertime) + 8) % 24;
	$flag = $hour &lt; 13;
	$item = getItemByTimeAndUser($year, $month, $day, $flag, $user_id);
	$ordertime = &quot;$year-$month-$day $hour:00:00&quot;;
	$item &amp;&amp; ($item = $item[0]) &amp;&amp; ($flag = $flag ? ' &lt; ' : ' &gt;= ');
	return $item
			? $util-&gt;execute(&quot;UPDATE ITEM SET FOOD = '$food' WHERE USER_ID = $user_id AND YEAR(ORDER_TIME) = $year AND MONTH(ORDER_TIME) = $month AND DAY(ORDER_TIME) = $day AND HOUR(ORDER_TIME) $flag 13&quot;)
			: $util-&gt;execute(&quot;INSERT INTO ITEM(USER_ID, FOOD, ORDER_TIME) VALUES($user_id, '$food', '$ordertime')&quot;);
}
/**
 * 获取某天上午或下午的订餐
 * @param $year
 * @param $month
 * @param $day
 * @param $flag 上午true下午false
 * @return unknown_type
 */
function getItemsByTime($year, $month, $day, $flag) {
	$util = new Mysql();
	$flag = $flag ? ' &lt; ' : ' &gt;= ';
	$item_datas = $util-&gt;execute(&quot;SELECT ITEM.*, USER.NAME FROM ITEM JOIN USER ON ITEM.USER_ID = USER.ID WHERE YEAR(ORDER_TIME) = $year AND MONTH(ORDER_TIME) = $month AND DAY(ORDER_TIME) = $day AND HOUR(ORDER_TIME) $flag 13&quot;);
	if(empty($item_datas)) return array();
	foreach($item_datas as $index =&gt; $data) {
		$item = new Item();
		$items[$data['USER_ID']] = $item-&gt;setId($data['ID'])
						-&gt;setUser(new User($data['USER_ID'], $data['NAME']))
						-&gt;setFood($data['FOOD'])
						-&gt;setOrderTime(date($data['ORDER_TIME']));
	}
	return $items;
}
/**
 * 根据用户id, 时间, 获取其订饭记录
 * @param $year
 * @param $month
 * @param $day
 * @param $flag
 * @param $user_id
 * @return unknown_type
 */
function getItemByTimeAndUser($year, $month, $day, $flag, $user_id) {
	$util = new Mysql();
	$flag = $flag ? ' &lt; ' : ' &gt;= ';
	$item_datas = $util-&gt;execute(&quot;SELECT ITEM.*, USER.NAME FROM ITEM JOIN USER ON ITEM.USER_ID = USER.ID WHERE YEAR(ORDER_TIME) = $year AND MONTH(ORDER_TIME) = $month AND DAY(ORDER_TIME) = $day AND HOUR(ORDER_TIME) $flag 13 AND ITEM.USER_ID = $user_id&quot;);
	echo &quot;SELECT ITEM.*, USER.NAME FROM ITEM JOIN USER ON ITEM.USER_ID = USER.ID WHERE YEAR(ORDER_TIME) = $year AND MONTH(ORDER_TIME) = $month AND DAY(ORDER_TIME) = $day AND HOUR(ORDER_TIME) $flag 13 AND ITEM.USER_ID = $user_id&quot;;
	if(empty($item_datas)) return array();
	foreach($item_datas as $index =&gt; $data) {
		$item = new Item();
		$items[] = $item-&gt;setId($data['ID'])
						-&gt;setUser(new User($data['USER_ID'], $data['NAME']))
						-&gt;setFood($data['FOOD'])
						-&gt;setOrderTime(date($data['ORDER_TIME']));
	}
	return $items;
}
?&gt;
```

 
单元测试

```php
&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=UTF-8&quot;&gt;
&lt;?php
require_once './mysql_util.php';
require_once './bussiness.php';
/**
 * 本程序业务逻辑之下部分的单元测试
 * @author selfimpr
 * @blog http://blog.csdn.net/lgg201
 * @email goosman.lei@gmail.com
 */

/**
 * 测试用例1: 数据库连接
 */
//$util = new Mysql();
//$res = $util-&gt;execute('SHOW TABLES');
//print_r($res);
//echo '&lt;br&gt;==========数据库连接成功===========&lt;br&gt;';
/**
 * 测试用例2: 获取所有用户
 */
//var_dump(getUsers());
/**
 * 测试用例3: 增加用户
 */
//var_dump(addUser(&quot;张三&quot;));
/**
 * 测试用例4: 删除用户
 */
//var_dump(deleteUser(4));
/**
 * 测试用例5: 更新用户
 */
//var_dump(updateUser(5, '李四'));
/**
 * 测试用例6: 插入订餐项
 */
//var_dump(addItem(1, '辣子鸡丁'));
/**
 * 测试用例7: 获取某天上午或下午订餐
 */
//var_dump(getItemsByTime(2010, 6, 4, false));
/**
 * 测试用例8: 获取某人某天上午或下午订餐
 */
//var_dump(getItemByTimeAndUser(date('Y'), date('m'), date('d'), date('H') + 8 &lt; 13, 1));
?&gt;
```

 
添加用户的Ajax接口

```php
&lt;?php
require_once './bussiness.php';
/**
 * 添加用户ajax请求
 */
return addUser($_POST['name']);
?&gt;
```

 
删除用户的ajax接口

```php
&lt;?php
require_once './bussiness.php';
/**
 * 删除用户ajax请求
 */
return deleteUser($_POST['id']);
?&gt;
```

 
提交订餐项的ajax接口

```php
&lt;?php
require_once './bussiness.php';
$userId = $_POST['userId'];
$food = $_POST['food'];
$t = $_POST['t'];
/**
 * 提交一个订餐请求的ajax地址
 */
echo (string)addItem($userId, $food, $t);
?&gt;
```

 
展现订餐项的视图:

```php
&lt;?php
/**
 * 展现某个订餐时间的所有订餐项
 */
require_once './bussiness.php';
$t = $_GET['t'];
$year = date('Y', $t);
$month = date('n', $t);
$day = date('j', $t);
$hour = (date('G', $t) + 8) % 24;
$flag = $hour &lt; 13;
$items = getItemsByTime($year, $month, $day, $flag);
$users = getUsers();
?&gt;
&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=UTF-8&quot;&gt;
&lt;table cellspacing=&quot;0&quot;&gt;
&lt;tr&gt;
	&lt;th colspan=&quot;7&quot;&gt;谁填谁的, 填完&quot;吃啥&quot;点下后面的&quot;就吃这个&quot;&lt;/th&gt;
&lt;/tr&gt;
&lt;tr align=&quot;center&quot;&gt;
	&lt;td&gt;名字&lt;/td&gt;
	&lt;td&gt;吃啥&lt;/td&gt;
	&lt;td&gt;操作&lt;/td&gt;
	&lt;td&gt;分水岭&lt;/td&gt;
	&lt;td&gt;名字&lt;/td&gt;
	&lt;td&gt;吃啥&lt;/td&gt;
	&lt;td&gt;操作&lt;/td&gt;
&lt;/tr&gt;
&lt;?php 
foreach($users as $index =&gt; $user) {
	if($index % 2 !== 0) continue;
?&gt;
&lt;tr class=&quot;ctrl-record&quot; userId=&quot;&lt;?php echo $user-&gt;getId(); ?&gt;&quot;&gt;
	&lt;td&gt;&lt;?php echo iconv('gbk', 'utf-8', $user-&gt;getName()); ?&gt;&lt;/td&gt;
	&lt;td&gt;&lt;input class=&quot;food-input ctrl-food-input&quot; type=&quot;text&quot; value=&quot;&lt;?php echo $items[$user-&gt;getId()] ? $items[$user-&gt;getId()]-&gt;getFood() : '&nbsp;'; ?&gt;&quot;&gt;&lt;/td&gt;
	&lt;td&gt;&lt;button class=&quot;ctrl-submit&quot;&gt;就吃这个&lt;/button&gt;&lt;/td&gt;
	&lt;td&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-|分|-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;
	&lt;td&gt;&lt;?php if($users[$index + 1]) echo iconv('gbk', 'utf-8', $users[$index + 1]-&gt;getName()); else echo '&nbsp;'; ?&gt;&lt;/td&gt;
	&lt;td&gt;&lt;input class=&quot;food-input ctrl-food-input&quot; type=&quot;text&quot; value=&quot;&lt;?php if($users[$index + 1]) echo $items[$users[$index + 1]-&gt;getId()] ? $items[$users[$index + 1]-&gt;getId()]-&gt;getFood() : '&nbsp;'; else echo '&nbsp;'; ?&gt;&quot;&gt;&lt;/td&gt;
	&lt;td&gt;&lt;button class=&quot;ctrl-submit&quot;&gt;就吃这个&lt;/button&gt;&lt;/td&gt;
&lt;/tr&gt;
&lt;?php
}
?&gt;
&lt;/table&gt;
```

 
展现所有用户的视图

```php
&lt;?php
require_once './bussiness.php';
/**
 * 系统中用户的管理界面
 * @author selfimpr
 * @blog http://blog.csdn.net/lgg201
 * @email goosman.lei@gmail.com
 */
$users = getUsers();
?&gt;
&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=UTF-8&quot;&gt;
&lt;input type=&quot;text&quot; id=&quot;new_user_input&quot; onkeyup=&quot;javascript: event.keyCode == 13 &amp;&amp; $('#new_user').click();&quot; /&gt;&lt;button id=&quot;new_user&quot;&gt;新增&lt;/button&gt;
&lt;?php 
if(empty($users)) return ;
?&gt;
&lt;div style=&quot;width: 300px; clear: both;&quot;&gt;
&lt;?php 
foreach($users as $index =&gt; $user) {
?&gt;
&lt;div class=&quot;ctrl-record&quot; style=&quot;width: 120px; padding: 10px; height: 70px; border: 1px solid #808080; background: #E8E8E8; float: left; display: block; margin: 5px;&quot; userId=&quot;&lt;?php echo $user-&gt;getId(); ?&gt;&quot;&gt;
	&lt;input type=&quot;text&quot; value=&quot;&lt;?php echo iconv('gbk', 'utf-8', $user-&gt;getName()); ?&gt;&quot; style=&quot;display: block; width: 100px; height: 20px; line-height: 20px;&quot;/&gt;
	&lt;button class=&quot;ctrl-del-user&quot; style=&quot;width: 100px; height: 25px; margin-top: 5px;&quot;&gt;删除&lt;/button&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;?php
}
?&gt;
```

 
系统的入口index.php

```php
&lt;?php 
/**
 * 程序入口
 */
?&gt;
&lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Strict//EN&quot;&quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd&quot;&gt;
&lt;html&gt;
&lt;head&gt;
&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=UTF-8&quot;&gt;
&lt;link rel='stylesheet' type='text/css' href=&quot;./styles/ui.all.css&quot; mce_href=&quot;styles/ui.all.css&quot; /&gt;
&lt;link rel='stylesheet' type='text/css' href=&quot;./styles/fullcalendar.css&quot; mce_href=&quot;styles/fullcalendar.css&quot; /&gt;
&lt;link rel='stylesheet' type='text/css' href=&quot;./styles/eat.css&quot; mce_href=&quot;styles/eat.css&quot; /&gt;
&lt;mce:script type='text/javascript' src=&quot;./scripts/jquery.js&quot; mce_src=&quot;scripts/jquery.js&quot;&gt;&lt;/mce:script&gt;
&lt;mce:script type='text/javascript' src=&quot;./scripts/jquery-ui-1.7.2.custom.js&quot; mce_src=&quot;scripts/jquery-ui-1.7.2.custom.js&quot;&gt;&lt;/mce:script&gt;
&lt;mce:script type='text/javascript' src=&quot;./scripts/fullcalendar.js&quot; mce_src=&quot;scripts/fullcalendar.js&quot;&gt;&lt;/mce:script&gt;
&lt;mce:script type='text/javascript' src=&quot;./scripts/eat.js&quot; mce_src=&quot;scripts/eat.js&quot;&gt;&lt;/mce:script&gt;
&lt;/head&gt;
&lt;body&gt;
&lt;button id=&quot;maintain&quot;&gt;加人&lt;/button&gt;
&lt;div id='calendar' class=&quot;wrapper&quot;&gt;&lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;
```

 
系统涉及的javascript: eat.js

```javascript
$(document).ready(function() {
	/**
	 * 计算一月有多少天
	 */
	function daysInMonth(month,year) {
		var dd = new Date(year, month, 0);
		return dd.getDate();
	} 
	/**
	 * 预置日历初始化使用日期, 当天
	 */
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	
	/**
	 * 初始化日历中每天里面的上下午日程
	 */
	var day_nums = daysInMonth(m, y);
	var events = new Array();
	for(var i = 1; i &lt; day_nums; i ++) {
		events.push({
			title: '上午吃啥', 
			start: new Date(y, m, i, 1), 
			end: new Date(y, m, i, 11)
		});
		events.push({
			title: '下午吃啥', 
			start: new Date(y, m, i, 14), 
			end: new Date(y, m, i, 23)
		});
	}
	
	/**
	 * 初始化日历
	 */
	$('#calendar').fullCalendar({
		year: y, 
		month: m, 
		day: d, 
		defaultView: 'month', 
		events: events, 
		header: {left: '', center: 'title', right: ''}, 
		aspectRatio: 1.6, 
		weekMode: 'variable', 
		eventClick: function(calEvent, jsEvent, view) {
			/**
			 * 日程被点击后, 加载该时段的订餐视图
			 */
			$('&lt;div&gt;').load('./viewItems.php?t=' + (calEvent.start / 1000), function() {
				var self= this;
				/**
				 * 订餐的提交
				 */
				$('.ctrl-submit', this).bind('click', function(event) {
					var record = $(this).parents('tr.ctrl-record').get(0);
					var userId = $(record).attr('userId');
					var food = $('.ctrl-food-input', record).val();
					$(self).dialog('close');
					$.ajax({
						type: 'POST', 
						url: './submitItem.php', 
						data:'userId=' + userId + '&amp;food=' + food + '&amp;t=' + (calEvent.start / 1000), 
						async: false,  
						success: function(res) {
							$('&lt;div&gt;').text('嘿嘿嘿, 吃好喝好啊.').dialog({
								resizable: false, 
								modal: true, 
								title: '订了, 该干啥干啥吧.', 
								width: 600, 
								close: function() {
									$(this).dialog('destroy').remove();
								}, 
								buttons: {
									'就是关的时候方便点1': function() {
										$(this).dialog('close');
									}, 
									'就是关的时候方便点2': function() {
										$(this).dialog('close');
									}, 
									'就是关的时候方便点3': function() {
										$(this).dialog('close');
									}
								}
							});
						}
					});
				});
				/**
				 * 把加载到的订餐视图预置为jqueryUI.dialog
				 */
				var t = new Date(calEvent.start);
				$(this).css('margin', '0 auto').dialog({
					resizable: false, 
					modal: true, 
					title: '[' + t.getFullYear() + '年' + (t.getMonth() + 1) + '月' + t.getDay() + '日' + (t.getHours() &gt; 13 ? '下午' : '上午') + ']----你想吃啥啊?', 
					width: 1000, 
					close: function() {
						$(this).dialog('destroy').remove();
					}, 
					buttons: {
						'就是关的时候方便点1': function() {
							$(this).dialog('close');
						}, 
						'就是关的时候方便点2': function() {
							$(this).dialog('close');
						}, 
						'就是关的时候方便点3': function() {
							$(this).dialog('close');
						}
					}
				});
			});
		}
		
	});
	/**
	 * 修改当天的DOM, 加明显标记
	 */
	$('.fc-today').append('&lt;div style=&quot;margin-top: 40px; color: #888933; font-size: 20px; font-weight: bold;&quot; mce_style=&quot;margin-top: 40px; color: #888933; font-size: 20px; font-weight: bold;&quot;&gt;这里是今天&lt;/div&gt;');
	/**
	 * 加人按钮, 用来加载用户管理界面
	 */
	$('#maintain').bind('click', function(event) {
		$('&lt;div&gt;').load('./viewUsers.php', function() {
			$(this).dialog({
				resizable: false, 
				modal: true, 
				title: '嘿嘿, 吃着喝着啊', 
				width: 1128,
				height: 450, 
				close: function() {
					$(this).dialog('destroy').remove();
				}, 
				buttons: {
					'就是关的时候方便点1': function() {
						$(this).dialog('close');
					}, 
					'就是关的时候方便点2': function() {
						$(this).dialog('close');
					}, 
					'就是关的时候方便点3': function() {
						$(this).dialog('close');
					}, 
					'就是关的时候方便点4': function() {
						$(this).dialog('close');
					}
				}
			});
			bindMaitainArea(this);
		});
	});
	/**
	 * 用户管理界面加载完毕之后, 对其上的按钮绑定事件
	 */
	var bindMaitainArea = (function(context) {
		/**
		 * 新增用户的事件
		 */
		$('#new_user', context).bind('click', function(event) {
			var self = this;
			$.ajax({
				type: 'POST', 
				url: './addUser.php', 
				data:'name=' + $('#new_user_input').val(), 
				timeout:8000,
				async: false,  
				success: function(res) {
					$(self).parents('.ui-dialog-content').load('./viewUsers.php', function() {
						bindMaitainArea();
					});
				}
			});
		});
		/**
		 * 删除用户的事件
		 */
		$('.ctrl-del-user', context).bind('click', function(event) {
			var self = this;
			$.ajax({
				type: 'POST', 
				url: './delUser.php', 
				data:'id=' + $(self).parents('.ctrl-record').attr('userId'), 
				timeout:8000,
				async: false,  
				success: function(res) {
					$(self).parents('.ui-dialog-content').load('./viewUsers.php', function() {
						bindMaitainArea();
					});
				}
			});
		});
	});
});
```

 
系统涉及的自定义样式eat.css

```css
@CHARSET &quot;UTF-8&quot;;
body{font-size: 75%;}
.wrapper{width: 1024px; margin: 0 auto;}
.food-input{width: 300px; border: 1px solid #E0E0E0;}
table{border-left: 1px solid #D0D0D0; border-top: 1px solid #D0D0D0;}
th, td{border-right: 1px solid #D0D0D0; border-bottom: 1px solid #D0D0D0;}

```

 
 
 
 
