**Mysql测试表创建**

```vb
DROP TABLE IF EXISTS userinfo;
CREATE TABLE userinfo
(
	id INT PRIMARY KEY AUTO_INCREMENT, 
	name VARCHAR(20), 
	sex VARCHAR(10), 
	province VARCHAR(30), 
	city VARCHAR(30)
);
ALTER TABLE userinfo ADD INDEX name_index (name);
ALTER TABLE userinfo ADD INDEX sex_index (sex);
ALTER TABLE userinfo ADD INDEX province_index (province);
ALTER TABLE userinfo ADD INDEX city_index (city);
DROP PROCEDURE IF EXISTS autoinsert;
DELIMITER /
CREATE PROCEDURE autoinsert(in num int, in base int)
	begin
		declare name_seed char(63) default &quot;_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ&quot;;
		declare sex_seed char(2) default &quot;mw&quot;;
		declare province_seed char(10) default &quot;ABCDEFGHIJ&quot;;
		declare city_seed char(10) default &quot;qrstuvwxyz&quot;;
		declare i int default base;
		declare name_rd int default 0;
		declare sex_rd int default 0;
		declare province_rd int default 0;
		declare city_rd int default 0;
		while(i &lt; num) do
			set name_rd = floor(rand() * 63) + 1;
			set sex_rd = floor(rand() * 2) + 1;
			set province_rd = floor(rand() * 10) + 1;
			set city_rd = floor(rand() * 10) + 1;
			INSERT INTO userinfo VALUES(i, 
				concat(substring(name_seed, floor(rand() * 63), 1), substring(name_seed, floor(rand() * 63), 1), substring(name_seed, floor(rand() * 63), 1), substring(name_seed, floor(rand() * 63), 1), substring(name_seed, floor(rand() * 63), 1), substring(name_seed, floor(rand() * 63), 1)), 
				substring(sex_seed, sex_rd, 1), 
				substring(province_seed, province_rd, 1), 
				substring(city_seed, city_rd, 1)
			);
			set i = i + 1;
		end while;
	end
/
DELIMITER ;
```

 
**TT使用tcrtest write -port 9001 localhost 200000000插入测试数据**
 
****
**基础功能函数**
**
```php
&lt;?php
require 'jpgraph/jpgraph.php';
require 'jpgraph/jpgraph_line.php';
function randChar() {
    static $chars = array(
	'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 
	'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 
	'u', 'v', 'w', 'x', 'y', 'z', 
	'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 
	'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 
	'U', 'V', 'W', 'X', 'Y', 'Z', 
	'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 
    );
    return $chars[rand(0, 61)];
}
function createKey($length = 20) {
    $str = '';
    while($length -- &gt; 0) $str .= randChar();
    return $str;
}
function createValue($length = 200) {
    $str = '';
    while($length -- &gt; 0) $str .= randChar();
    return $str;
}
function createNumberValue() {
    return rand(0, 2147483647);
}
function generateSeed($length) {
	$length = $length * $length;
	$result = &quot;&quot;;
	while($length -- &gt; 0) $result .= randChar();
	return $result;
}
function randString($length, &amp;$seed, $n = 10) {
	return substr($seed, rand(0, strlen($seed) - $length - 1), $length);	
}
function drawline($data1, $legend1, $data2, $legend2, $title, $xtitle, $ytitle) {
     
    // Create the graph and specify the scale for both Y-axis
    $graph = new Graph(800,600);
    $graph-&gt;SetScale('textlin');
    $graph-&gt;SetY2Scale('lin');
    $graph-&gt;SetShadow();
     
    // Adjust the margin
    $graph-&gt;img-&gt;SetMargin(40,140,20,40);
     
    // Create the two linear plot
    $lineplot=new LinePlot($data1);
    $lineplot2=new LinePlot($data2);
     
    // Add the plot to the graph
    $graph-&gt;Add($lineplot);
    $graph-&gt;AddY2($lineplot2);
    $lineplot2-&gt;SetColor('orange');
    $lineplot2-&gt;SetWeight(2);
     
    // Adjust the axis color
    $graph-&gt;y2axis-&gt;SetColor('orange');
    $graph-&gt;yaxis-&gt;SetColor('blue');
     
    $graph-&gt;title-&gt;Set($title);
    $graph-&gt;xaxis-&gt;title-&gt;Set($xtitle);
    $graph-&gt;yaxis-&gt;title-&gt;Set($ytitle);
     
    $graph-&gt;title-&gt;SetFont(FF_FONT1,FS_BOLD);
    $graph-&gt;yaxis-&gt;title-&gt;SetFont(FF_FONT1,FS_BOLD);
    $graph-&gt;xaxis-&gt;title-&gt;SetFont(FF_FONT1,FS_BOLD);
     
    // Set the colors for the plots
    $lineplot-&gt;SetColor('blue');
    $lineplot-&gt;SetWeight(2);
    $lineplot2-&gt;SetColor('orange');
    $lineplot2-&gt;SetWeight(2);
     
    // Set the legends for the plots
    $lineplot-&gt;SetLegend($legend1);
    $lineplot2-&gt;SetLegend($legend2);
     
    // Adjust the legend position
    $graph-&gt;legend-&gt;Pos(0.05,0.5,'right','center');
     
    // Display the graph
    $graph-&gt;Stroke();
}
function drawline1($datas) {
    $width = 800;
    $height = 600;
    $graph = new Graph($width, $height);
    $graph-&gt;SetScale('intlin');
    $lineplot = new LinePlot($datas);
    $graph-&gt;Add($lineplot);
    $graph-&gt;Stroke();
}
function drawline2($title, $xtitle, $ytitle) {
	
	$width = 1200;
	$height = 600;
	
	// Create the graph and set a scale.
	// These two calls are always required
	$graph = new Graph($width,$height);
	$graph-&gt;SetScale('intlin');
	$graph-&gt;SetShadow();
	
	$graph-&gt;img-&gt;SetMargin(60,350,20,40);
	 
	// Setup margin and titles
	$graph-&gt;title-&gt;Set($title);
	$graph-&gt;xaxis-&gt;title-&gt;Set($xtitle);
	$graph-&gt;yaxis-&gt;title-&gt;Set($ytitle);
	 
	$graph-&gt;yaxis-&gt;title-&gt;SetFont( FF_FONT1 , FS_BOLD );
	$graph-&gt;xaxis-&gt;title-&gt;SetFont( FF_FONT1 , FS_BOLD );
	
	$arg_num = func_num_args();
	$i = 3;
	while($i &lt; $arg_num) {
		$data = func_get_arg($i);
		$legend = $data['legend'];
		$data = $data['data'];
		$lineplot = new LinePlot($data);
		$lineplot-&gt;SetWeight(1);
		$lineplot-&gt;SetLegend($legend);
		$graph-&gt;Add($lineplot);
		$i ++;
	}
	$graph-&gt;legend-&gt;SetPos(0.05, 0.5, 'right', 'center');
	 
	// Display the graph
	$graph-&gt;Stroke();
}
function getCurrentMemoryStr() {
	$a = system('free -m');
	$matches = NULL;
	preg_match('/(/d+)/D+(/d+)/D+(/d+)/', $a, $matches);
	return &quot;内存总量(M): &quot;.$matches[1].&quot;, 已使用(M): &quot;.$matches[2].&quot;, 空闲(M): &quot;.$matches[3].&quot;;&quot;;
}
function getCurrentMemory() {
	$a = NULL;
	exec('free -m', $a);
	$matches = NULL;
	preg_match('/(/d+)/D+(/d+)/D+(/d+)/', $a[1], $matches);
	return (int)$matches[2];
}
?&gt;
```

 
**
**Mysql工具类**
**
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
	
	private $conn;
	
	public function __construct(
			$host = &quot;localhost&quot;,
			$username = &quot;root&quot;, 
			$password = &quot;root&quot;, 
			$db_name = &quot;test&quot;, 
			$character_set = &quot;GBK&quot;) {
		$this-&gt;host = $host;
		$this-&gt;username = $username;
		$this-&gt;password = $password;
		$this-&gt;db_name = $db_name;
		$this-&gt;character_set = $character_set;
		$this-&gt;createConn();
	}
	
	public function createConn() {
		$this-&gt;conn = mysql_connect($this-&gt;host, $this-&gt;username, $this-&gt;password) or die('数据库连接失败');
		mysql_select_db($this-&gt;db_name, $this-&gt;conn);
		mysql_set_charset($this-&gt;character_set, $this-&gt;conn);
	}
	
	public function getConn() {
		return $this-&gt;getConn();
	}
	
	/**
	 * 
	 * @param $sql 查询用的sql
	 * @param $flag 标记是否把查询结果集遍历到数组
	 * @return 
	 */
	public function execute($sql, $flag = true) {
		$resultset = mysql_query($sql, $this-&gt;conn) or die(mysql_error($this-&gt;conn));
		if($flag) return ;
		if(!is_bool($resultset)) {
			while($line = mysql_fetch_assoc($resultset)) {
				$result[] = $line;
			}
		} else {
			$result = $resultset;
		}
//		echo &quot;【&quot;.$sql.&quot;】 excute success!&lt;br /&gt;&quot;;
		
		return $result;
	}
	
	public function explain($sql) {
		return $this-&gt;execute('EXPLAIN '.$sql, false);
	}
}
?&gt;

```

 
**
**TCT
设置索引**
**
```php
&lt;?php
function getClientTct($host = 'localhost', $port = 9003) {
	$tt = new TokyoTyrantTable();
	$tt-&gt;connect($host, $port);
	return $tt;
}
$client = getClientTct();
$client-&gt;setIndex('name', TokyoTyrant::RDBIT_LEXICAL);
$client-&gt;setIndex('sex', TokyoTyrant::RDBIT_LEXICAL);
$client-&gt;setIndex('province', TokyoTyrant::RDBIT_LEXICAL);
$client-&gt;setIndex('city', TokyoTyrant::RDBIT_LEXICAL);
echo '索引设置成功';
?&gt;
```

 
**
**所有产品写
入性能对比**
**
```php
&lt;?php
set_time_limit(0);
require_once 'mysql_util.php';
require 'basic_funcs.php';
$current_test = $_GET['case'];
$num = (int)$_GET['num'];
$length = (int)$_GET['length'];
function getClientMemcache($host = 'localhost', $port = 11211) {
	$memcache = new Memcache();
	$memcache-&gt;connect($host, $port);
	return $memcache;
}
function getClientTch($host = 'localhost', $port = 9001) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTcb($host = 'localhost', $port = 9002) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTct($host = 'localhost', $port = 9003) {
	$tt = new TokyoTyrantTable();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientMysql($host = &quot;localhost&quot;,
			$username = &quot;root&quot;, 
			$password = &quot;root&quot;, 
			$db_name = &quot;test&quot;, 
			$character_set = &quot;GBK&quot;) {
	$client = new Mysql($host, $username, $password, $db_name, $character_set);
	return $client;
}
function test_tch($num, $length) {
	$limit = floor($num / 20);
	$client = getClientTch();
	$value = createValue($length);
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;put($num, $value);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCH set test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tcb($num, $length) {
	$limit = floor($num / 20);
	$client = getClientTcb();
	$value = createValue($length);
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;put($num, $value);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCB set test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tct($num, $length) {
	$limit = floor($num / 20);
	$client = getClientTct();
	$sexies = array('m', 'w');
	$provinces = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$cities = array('q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	$value = array('name' =&gt; createValue(6), 'sex' =&gt; $sexies[rand(0, 1)], 'province' =&gt; $provinces[rand(0, 9)], 'city' =&gt; $cities[rand(0, 9)]);
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;put($num, $value);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCT set test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_memcache($num, $length) {
	$limit = floor($num / 20);
	$client = getClientMemcache();
	$value = createValue($length);
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;set($num, $value);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Memcache set test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_mysql($num, $length) {
	$limit = floor($num / 20);
	$client = getClientMysql();
	$name = createValue($length);
	$sexies = array('m', 'w');
	$provinces = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$cities = array('q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;execute(&quot;INSERT INTO userinfo VALUES(NULL, '&quot;.$name.&quot;', '&quot;.$sexies[rand(0, 1)].&quot;', '&quot;.$provinces[rand(0, 9)].&quot;', '&quot;.$cities[rand(0, 9)].&quot;')&quot;);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Mysql set test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test($num, $length) {
	$limit = floor($num / 20);
	drawline2(&quot;Mysql, TCT, TCB, TCH, Memcache compare in set&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		test_tch($num, $length), 
		test_tcb($num, $length), 
		test_tct($num, $length), 
		test_memcache($num, $length), 
		test_mysql($num, $length)
	);
}
if(!$current_test) {
	test($num, $length);
} else {
	$limit = floor($num / 20);
	drawline2(&quot;Mysql, TCT, TCB, TCH, Memcache compare in set&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		$current_test($num, $length)
	);
}
?&gt;
```

 
**
**所有产品读取性能对比**
**
```php
&lt;?php
set_time_limit(0);
require_once 'mysql_util.php';
require 'basic_funcs.php';
$current_test = $_GET['case'];
$num = (int)$_GET['num'];
function getClientMemcache($host = 'localhost', $port = 11211) {
	$memcache = new Memcache();
	$memcache-&gt;connect($host, $port);
	return $memcache;
}
function getClientTch($host = 'localhost', $port = 9001) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTcb($host = 'localhost', $port = 9002) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTct($host = 'localhost', $port = 9003) {
	$tt = new TokyoTyrantTable();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientMysql($host = &quot;localhost&quot;,
			$username = &quot;root&quot;, 
			$password = &quot;root&quot;, 
			$db_name = &quot;test&quot;, 
			$character_set = &quot;GBK&quot;) {
	$client = new Mysql($host, $username, $password, $db_name, $character_set);
	return $client;
}
function test_tch($num) {
	$limit = floor($num / 20);
	$client = getClientTch();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;get($num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCH get test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tcb($num) {
	$limit = floor($num / 20);
	$client = getClientTcb();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;get($num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCB get test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tct($num) {
	$limit = floor($num / 20);
	$client = getClientTct();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;get($num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCT get test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_memcache($num) {
	$limit = floor($num / 20);
	$client = getClientMemcache();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;get($num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Memcache get test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_mysql($num) {
	$limit = floor($num / 20);
	$client = getClientMysql();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;execute(&quot;SELECT * FROM userinfo WHERE id = &quot;.$num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Mysql get test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test($num) {
	$limit = floor($num / 20);
	drawline2(&quot;Mysql, TCT, TCB, TCH, Memcache compare in get&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		test_tch($num), 
		test_tcb($num), 
		test_tct($num), 
		test_memcache($num), 
		test_mysql($num)
	);
}
test($num);
?&gt;
```

 
**
**搜索比较**
**
```php
&lt;?php
set_time_limit(0);
require_once 'mysql_util.php';
require 'basic_funcs.php';
$current_test = $_GET['case'];
$num = (int)$_GET['num'];
$debug = $_GET['debug'];

function getClientTct($host = '192.168.2.22', $port = 9003) {
	$tt = new TokyoTyrantTable();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientMysql($host = &quot;localhost&quot;,
			$username = &quot;root&quot;, 
			$password = &quot;root&quot;, 
			$db_name = &quot;test&quot;, 
			$character_set = &quot;GBK&quot;) {
	$client = new Mysql($host, $username, $password, $db_name, $character_set);
	return $client;
}
function test_tct_name($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientTct();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$query = $client-&gt;getQuery();
		$query-&gt;addCond('name', TokyoTyrant::RDBQC_STRINC, createValue(rand(1, 5)));
		$query-&gt;setLimit(10, 0);
		$begin = microtime(true);
		$query-&gt;search();
		$end = microtime(true);
		if($debug) echo $query-&gt;hint().'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCT search by name test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_mysql_name($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientMysql();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$name = createValue(rand(1, 5));
		$sql = &quot;SELECT * FROM userinfo WHERE name LIKE '%$name%' limit 0, 10&quot;;
		$begin = microtime(true);
		$client-&gt;execute($sql);
		$end = microtime(true);
		if($debug) echo $sql.'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Mysql search by name test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tct_sex($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientTct();
	$times = array();
	$tmp = $num;
	$total = 0;
	$sexies = array('m', 'w');
	while($num -- &gt;= 0) {
		$query = $client-&gt;getQuery();
		$query-&gt;addCond('sex', TokyoTyrant::RDBQC_STREQ, $sexies[rand(0, 1)]);
		$query-&gt;setLimit(10, 0);
		$begin = microtime(true);
		$query-&gt;search();
		$end = microtime(true);
		if($debug) echo $query-&gt;hint().'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCT search by sex test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_mysql_sex($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientMysql();
	$times = array();
	$tmp = $num;
	$total = 0;
	$sexies = array('m', 'w');
	while($num -- &gt;= 0) {
		$sex = $sexies[rand(0, 1)];
		$sql = &quot;SELECT * FROM userinfo WHERE sex = '$sex' limit 0, 10&quot;;
		$begin = microtime(true);
		$client-&gt;execute($sql);
		$end = microtime(true);
		if($debug) echo $sql.'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Mysql search by sex test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tct_location($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientTct();
	$times = array();
	$tmp = $num;
	$total = 0;
	$provinces = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$cities = array('q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	while($num -- &gt;= 0) {
		$query = $client-&gt;getQuery();
		$query-&gt;addCond('province', TokyoTyrant::RDBQC_STREQ, $provinces[rand(0, 9)]);
		$query-&gt;addCond('city', TokyoTyrant::RDBQC_STREQ, $cities[rand(0, 9)]);
		$query-&gt;setLimit(10, 0);
		$begin = microtime(true);
		$query-&gt;search();
		$end = microtime(true);
		if($debug) echo $query-&gt;hint().'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCT search by location test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_mysql_location($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientMysql();
	$times = array();
	$tmp = $num;
	$total = 0;
	$provinces = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$cities = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');
	while($num -- &gt;= 0) {
		$province = $provinces[rand(0, 9)];
		$city = $cities[rand(0, 9)];
		$sql = &quot;SELECT * FROM userinfo WHERE province = '$province' AND city = '$city' limit 0, 10&quot;;
		$begin = microtime(true);
		$client-&gt;execute($sql);
		$end = microtime(true);
		if($debug) echo $sql.'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Mysql search by location test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tct_all($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientTct();
	$times = array();
	$tmp = $num;
	$total = 0;
	$sexies = array('m', 'w');
	$provinces = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$cities = array('q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	while($num -- &gt;= 0) {
		$query = $client-&gt;getQuery();
		$query-&gt;addCond('province', TokyoTyrant::RDBQC_STREQ, $provinces[rand(0, 9)]);
		$query-&gt;addCond('city', TokyoTyrant::RDBQC_STREQ, $cities[rand(0, 9)]);
		$query-&gt;addCond('sex', TokyoTyrant::RDBQC_STREQ, $sexies[rand(0, 1)]);
		$query-&gt;addCond('name', TokyoTyrant::RDBQC_STRINC, createValue(rand(1, 5)));
		$query-&gt;setLimit(10, 0);
		$begin = microtime(true);
		$query-&gt;search();
		$end = microtime(true);
		if($debug) echo $query-&gt;hint().'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCT search by all test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_mysql_all($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientMysql();
	$times = array();
	$tmp = $num;
	$total = 0;
	$sexies = array('m', 'w');
	$provinces = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$cities = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');
	while($num -- &gt;= 0) {
		$province = $provinces[rand(0, 9)];
		$city = $cities[rand(0, 9)];
		$sex = $sexies[rand(0, 1)];
		$name = createValue(rand(1, 5));
		$sql = &quot;SELECT * FROM userinfo WHERE province = '$province' AND city = '$city' AND sex = '$sex' AND name like '%$name%' limit 0, 10&quot;;
		$begin = microtime(true);
		$client-&gt;execute($sql);
		$end = microtime(true);
		if($debug) echo $sql.'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Mysql search by location test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_tct_regexp($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientTct();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$query = $client-&gt;getQuery();
		$query-&gt;addCond('name', TokyoTyrant::RDBQC_STRRX, '.*'.createValue(rand(1, 5)).'.*');
		$query-&gt;setLimit(10, 0);
		$begin = microtime(true);
		$query-&gt;search();
		$end = microtime(true);
		if($debug) echo $query-&gt;hint().'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;TCT search by regexp test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_mysql_regexp($num) {
	global $debug;
	$limit = floor($num / 20);
	$client = getClientMysql();
	$times = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$name = createValue(rand(1, 5));
		$sql = &quot;SELECT * FROM userinfo WHERE name REGEXP '.*$name.*' limit 0, 10&quot;;
		$begin = microtime(true);
		$client-&gt;execute($sql);
		$end = microtime(true);
		if($debug) echo $sql.'&lt;br /&gt;';
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			$total = 0;
		}
	}
	return array(
		'legend' =&gt; &quot;Mysql search by regexp test result &quot;.(array_sum($times) / $tmp),
		'data' =&gt; $times, 
	);
}
function test_name($num) {
	$limit = floor($num / 20);
	$data1 = test_tct_name($num);
	$data2 = test_mysql_name($num);
	global $debug;
	if($debug) return ;
	drawline2(&quot;Mysql, TCT compare in get by name&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		$data1, $data2
	);
}
function test_sex($num) {
	$limit = floor($num / 20);
	$data1 = test_tct_sex($num);
	$data2 = test_mysql_sex($num);
	global $debug;
	if($debug) return ;
	drawline2(&quot;Mysql, TCT compare in get by sex&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		$data1, $data2
	);
}
function test_location($num) {
	$limit = floor($num / 20);
	$data1 = test_tct_location($num);
	$data2 = test_mysql_location($num);
	global $debug;
	if($debug) return ;
	drawline2(&quot;Mysql, TCT compare in get by location&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		$data1, $data2
	);
}
function test_all($num) {
	$limit = floor($num / 20);
	$data1 = test_tct_all($num);
	$data2 = test_mysql_all($num);
	global $debug;
	if($debug) return ;
	drawline2(&quot;Mysql, TCT compare in get by all&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		$data1, $data2
	);
}
function test_regexp($num) {
	$limit = floor($num / 20);
	$data1 = test_tct_regexp($num);
	$data2 = test_mysql_regexp($num);
	global $debug;
	if($debug) return ;
	drawline2(&quot;Mysql, TCT compare in get by name&quot;, 
		&quot;Test case number&quot;, 
		&quot;run time per $limit record(S)&quot;, 
		$data1, $data2
	);
}
$current_test($num);
?&gt;
```

 
**
**TCH, TCB, TCT的内存压力写入测试**
**
```php
&lt;?php
set_time_limit(0);
require 'basic_funcs.php';
$current_test = $_GET['case'];
$num = $_GET['num'];
$length = $_GET['length'];
function getClientTch($host = 'localhost', $port = 9001) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTcb($host = 'localhost', $port = 9002) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTct($host = 'localhost', $port = 9003) {
    $tt = new TokyoTyrantTable();
    $tt-&gt;connect($host, $port);
    return $tt;
}
function test_tch($num, $length) {
	$limit = floor($num / 20);
	$value = createValue($length);
	$client = getClientTch();
	$times = array();
	$mems = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;put($num, $value);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			array_push($mems, getCurrentMemory());
			$total = 0;
		}
	}
	drawline($times, &quot;Time for &quot;.$limit.&quot; record.&quot;, $mems, &quot;Current memory used(M)&quot;, &quot;TCH test for full memory(&quot;.(array_sum($times) / $tmp).&quot;)&quot;, &quot;test case number&quot;, &quot;Time or MemoryUsed&quot;);
}
function test_tcb($num, $length) {
	$limit = floor($num / 20);
	$value = createValue($length);
	$client = getClientTcb();
	$times = array();
	$mems = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;put($num, $value);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			array_push($mems, getCurrentMemory());
			$total = 0;
		}
	}
	drawline($times, &quot;Time for &quot;.$limit.&quot; record.&quot;, $mems, &quot;Current memory used(M)&quot;, &quot;TCB test for full memory(&quot;.(array_sum($times) / $tmp).&quot;)&quot;, &quot;test case number&quot;, &quot;Time or MemoryUsed&quot;);
}
function test_tct($num, $length) {
	$limit = floor($num / 20);
	$value = array('value' =&gt; createValue($length));
	$client = getClientTct();
	$times = array();
	$mems = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;put($num, $value);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			array_push($mems, getCurrentMemory());
			$total = 0;
		}
	}
	drawline($times, &quot;Time for &quot;.$limit.&quot; record.&quot;, $mems, &quot;Current memory used(M)&quot;, &quot;TCT test for full memory(&quot;.(array_sum($times) / $tmp).&quot;)&quot;, &quot;test case number&quot;, &quot;Time or MemoryUsed&quot;);
}
$current_test($num, $length);
?&gt;
```

 
**
**TCH, TCB, TCT的内存压力读取测试**
**
```php
&lt;?php
set_time_limit(0);
require 'basic_funcs.php';
$current_test = $_GET['case'];
$num = (int)$_GET['num'];
function getClientTch($host = 'localhost', $port = 9001) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTcb($host = 'localhost', $port = 9002) {
	$tt = new TokyoTyrant();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function getClientTct($host = 'localhost', $port = 9003) {
	$tt = new TokyoTyrantTable();
	$tt-&gt;connect($host, $port);
	return $tt;
}
function test_tch($num) {
	$limit = floor($num / 20);
	$client = getClientTch();
	$times = array();
	$mems = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;get($num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			array_push($mems, getCurrentMemory());
			$total = 0;
		}
	}
	drawline($times, &quot;Time for &quot;.$limit.&quot; record.&quot;, $mems, &quot;Current memory used(M)&quot;, &quot;TCH test for full memory(&quot;.(array_sum($times) / $tmp).&quot;)&quot;, &quot;test case number&quot;, &quot;Time or MemoryUsed&quot;);
}
function test_tcb($num) {
	$limit = floor($num / 20);
	$client = getClientTcb();
	$times = array();
	$mems = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;get($num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			array_push($mems, getCurrentMemory());
			$total = 0;
		}
	}
	drawline($times, &quot;Time for &quot;.$limit.&quot; record.&quot;, $mems, &quot;Current memory used(M)&quot;, &quot;TCB test for full memory(&quot;.(array_sum($times) / $tmp).&quot;)&quot;, &quot;test case number&quot;, &quot;Time or MemoryUsed&quot;);
}
function test_tct($num) {
	$limit = floor($num / 20);
	$client = getClientTct();
	$times = array();
	$mems = array();
	$tmp = $num;
	$total = 0;
	while($num -- &gt;= 0) {
		$begin = microtime(true);
		$client-&gt;get($num);
		$end = microtime(true);
		$total += $end - $begin;
		if(!($num % $limit)) {
			array_push($times, $total);
			array_push($mems, getCurrentMemory());
			$total = 0;
		}
	}
	drawline($times, &quot;Time for &quot;.$limit.&quot; record.&quot;, $mems, &quot;Current memory used(M)&quot;, &quot;TCT test for full memory(&quot;.(array_sum($times) / $tmp).&quot;)&quot;, &quot;test case number&quot;, &quot;Time or MemoryUsed&quot;);
}
$current_test($num);
?&gt;
```

 
**
**Mysql并发测试(ab -n 10000 -c 10000 http://localhost/concurrent_mysql.php)
**

```php
#! /usr/local/bin/php
&lt;?php
function test($num) {
	$times = array();
	while($num -- &gt; 0) {
		$begin = microtime(true);
		$conn = mysql_connect('localhost', 'root', 'root', 'test');
		mysql_select_db('test', $conn);
		mysql_set_charset('GBK', $conn);
		$end = microtime(true);
		$times['open'] += $end - $begin;
		
		$begin = microtime(true);
		mysql_query(&quot;UPDATE userinfo SET name = 'hello' WHERE id &lt;= 10&quot;);
		$end = microtime(true);
		$times['update'] += $end - $begin;
		
		$begin = microtime(true);
		mysql_query(&quot;SELECT * FROM userinfo LIMIT 0, 10&quot;);
		$end = microtime(true);
		$times['query'] += $end - $begin;
		
		$begin = microtime(true);
		mysql_query(&quot;INSERT INTO userinfo VALUES(NULL, 'hello', 'm', 'A', 'x'&quot;);
		$end = microtime(true);
		$times['insert'] += $end - $begin;
		
		$begin = microtime(true);
		mysql_close($conn);
		$end = microtime(true);
		$times['close'] += $end - $begin;
	}
	echo &quot;{open: &quot;.$times['open'].&quot;, update: &quot;.$times['update'].&quot;, query: &quot;.$times['query'].&quot;, insert: &quot;.$times['insert'].&quot;, close: &quot;.$times['close'].&quot;}&quot;;
}
test($_GET['num'] ? $_GET['num'] : 100000);
?&gt;
```

