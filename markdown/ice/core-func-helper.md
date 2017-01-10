# 辅助函数

## 数组

### array_pluck() — 从数组中提取出指定的键/值对
```
$array = [
        ['developer' => ['id' => 1, 'name' => 'Taylor', 'gender' => 'male']],
        ['developer' => ['id' => 2, 'name' => 'Abigail', 'gender' => 'female']],
        ['developer' => ['id' => 2, 'name' => 'Abigail2', 'gender' => 'male']],
];

//取出某几个指定的键/值对
$array = array_pluck($array, array('developer.name', 'developer.gender'));

// [['name' => 'Taylor', 'gender' => 'male'], ['name' => 'Abigail', 'gender' => 'female'], ['name' => 'Abigail2', 'gender' => 'male']];


//取出某几个指定的键/值对，指定某个键值做索引，重复key的值, 保留前面, 跳过后面

$array = array_pluck($array, ['developer.name', 'developer.gender'], 'developer.id');

// [1 => ['name' => 'Taylor', 'gender' => 'male'], 2 => ['name' => 'Abigail', 'gender' => 'female']];


//取出全部键/值对，指定某个键值做索引，重复key的值, 保留前面, 跳过后面

$array = array_pluck($array, false, 'developer.id');

/*
[ 
        1 => ['developer' => ['id' => 1, 'name' => 'Taylor', 'gender' => 'male']],
        2 => ['developer' => ['id' => 2, 'name' => 'Abigail', 'gender' => 'female']],
];
*/
```

### array_where() — 用闭包函数过滤数组
```
$array = [100, 200, 300, 400, 500];

$array = array_where($array, function ($key, $value) {
        return $value > 200;
});

// [2 => 300, 3 => 400, 4 => 500];
```

### array_unshift_index() — 在数组指定位置插入一个单元
```
$array = [100, 200, 300, 400, 500];

array_unshift_index($array, 1, 150);

// [100, 150, 200, 300, 400, 500];
```

### array_get() — 基于点号路径从一个深度嵌套的数组中取出值，可以指定默认值
```
$array = ['products' => ['desk' => ['price' => 100]]];

$value = array_get($array, 'products.desk');

// ['price' => 100]


如果指定的键未找到，返回默认值

$value = array_get($array, 'products.desk2', ['price' => 200]);

// ['price' => 200]
```

### array_sort() — 根据某个值对数组进行排序
```
$array = [
        ['name' => 'Desk', 'score' => 2],
        ['name' => 'Chair', 'score' => 1],
];

//按score的值，以数字进行顺序排列

$array = array_sort($array, 'score', 'numeric', false);

/*
[
        ['name' => 'Chair', 'score' => 1],
        ['name' => 'Desk', 'score' => 2],
]
 */


//按score的值，以数字进行倒序排列

$array = array_sort($array, 'score', 'numeric', true);

/*
[
        ['name' => 'Desk', 'score' => 2],
        ['name' => 'Chair', 'score' => 1],
]
 */


//按name的值，以字符串进行顺序排列

$array = array_sort($array, 'name', 'string', false);

/*
[
        ['name' => 'Chair', 'score' => 1],
        ['name' => 'Desk', 'score' => 2],
]
 */
```

### array_head() — 返回数组的第一个元素
```
$array = [100, 200, 300];

$first = array_head($array);

// 100
```

### array_last() — 返回数组的最后一个元素
```
$array = [100, 200, 300];

$last = last($array);

// 300
```

### 其他请使用系统函数

[系统函数](http://php.net/manual/zh/book.array.php)


## 字符串

### str_is() — 判断字串是否匹配给定形式。星号表示通配符
```
$value = str_is('foo*', 'foobar');

// true


$value = str_is('baz*', 'foobar');

// false
```

### str_limit() — 函数限制一个字符串的长度。该函数接收一个字符串作为第一个参数，最大长度作为第二个参数
```
$value = str_limit('The PHP framework for web artisans.', 10);

// The PHP...
```

### str_random() — 生成指定长度的随机字符串
```
$string = str_random(40);
```

## 时间

## 常用

### info() — 记录info日志
```
info(['msg' => 'Info message']);
```

### warn() — 记录warn日志
```
warn(['msg' => 'Warn message']);

warn(['msg' => 'Warn message'], 100100);
```

### fatal() — 记录fatal日志
```
fatal(['msg' => 'Warn message']);

fatal(['msg' => 'Warn message'], 100100);
```

### value() — 返回给定的值，如果传递的是一个闭包函数，将会执行闭包函数并返回结果
```
$value = value(function () {
        return 'bar';
});
```

