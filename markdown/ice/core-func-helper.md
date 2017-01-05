# 辅助函数

## 数组

### array_pluck() 从数组中提取出指定的键/值对
```
$array = [
        ['developer' => ['id' => 1, 'name' => 'Taylor', 'gender' => 'male']],
        ['developer' => ['id' => 2, 'name' => 'Abigail', 'gender' => 'female']],
        ['developer' => ['id' => 2, 'name' => 'Abigail2', 'gender' => 'male']],
];

//取出某一个指定的键/值对
$array = array_pluck($array, 'developer.name');

// ['Taylor', 'Abigail', 'Abigail2'];


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

### array_where() 用闭包函数过滤数组
```
$array = [100, 200, 300, 400, 500];

$array = array_where($array, function ($key, $value) {
        return $value > 200;
});

// [2 => 300, 3 => 400, 4 => 500];
```

### array_unshift_index() 在数组指定位置插入一个单元
```
$array = [100, 200, 300, 400, 500];

array_unshift_index($array, 1, 150);

// [100, 150, 200, 300, 400, 500];
```

### array_get() 基于点号路径从一个深度嵌套的数组中取出值，可以指定默认值
```
$array = ['products' => ['desk' => ['price' => 100]]];

$value = array_get($array, 'products.desk');

// ['price' => 100]


如果指定的键未找到，返回默认值

$value = array_get($array, 'products.desk2', ['price' => 200]);

// ['price' => 200]
```

### array_sort() 根据某个值对数组进行排序
```
$array = [
        ['name' => 'Desk', 'score' => 2],
        ['name' => 'Chair', 'score' => 1],
];

//按score的值，以数字进行顺序排列

$array = array_sort($array, 'score', 'num', false);

/*
[
        ['name' => 'Chair', 'score' => 1],
        ['name' => 'Desk', 'score' => 2],
]
 */


//按score的值，以数字进行倒序排列

$array = array_sort($array, 'score', 'num', true);

/*
[
        ['name' => 'Desk', 'score' => 2],
        ['name' => 'Chair', 'score' => 1],
]
 */


//按name的值，以字符串进行顺序排列

$array = array_sort($array, 'name', 'str', false);

/*
[
        ['name' => 'Chair', 'score' => 1],
        ['name' => 'Desk', 'score' => 2],
]
 */
```

### array_head() 返回数组的第一个元素
```
$array = [100, 200, 300];

$first = array_head($array);

// 100
```

### array_last() 返回数组的最后一个元素
```
$array = [100, 200, 300];

$last = last($array);

// 300
```


## 字符串

```camel_case()
camel_case()
给定字串转换为驼峰式

$camel = camel_case('foo_bar');
// fooBar
```

```class_basename()
class_basename()
返回删除了名字空间的类名

$class = class_basename('Foo\Bar\Baz');
// Baz
```

```e()
e()
为给定的字串调用 htmlentities 

echo e('<html>foo</html>');
// &lt;html&gt;foo&lt;/html&gt;
```

```ends_with()
ends_with()
判断字串是否以给定值结尾

$value = ends_with('This is my name', 'name');
// true
```

```snake_case()
snake_case()
将给定字串转换为蛇形式

$snake = snake_case('fooBar');
// foo_bar
```

```str_limit()
str_limit()
函数限制一个字符串的长度。该函数接收一个字符串作为第一个参数，最大长度作为第二个参数

$value = str_limit('The PHP framework for web artisans.', 7);
// The PHP...
```

```starts_with()
starts_with()
判断字串是否以给定值开头

$value = starts_with('This is my name', 'This');
// true
```

```str_contains()
str_contains()
判断字串是否包含给定值

$value = str_contains('This is my name', 'my');
// true
```

```str_finish()
str_finish()
为字串添加给定单例

$string = str_finish('this/string', '/');
// this/string/
```

```str_is()
str_is()
判断字串是否匹配给定形式。星号表示通配符

$value = str_is('foo*', 'foobar');
// true
$value = str_is('baz*', 'foobar');
// false
```

```str_random()
str_random()
生成指定长度的随机字符串

$string = str_random(40);
```

```str_singular()
str_singular()
将字串转换为其单数形式。该函数目前仅支持英文

$singular = str_singular('cars');
// car
```

```str_slug()
str_slug()
将字串转换为URL友好型： function generates a URL friendly "slug" 

$title = str_slug("Laravel 5 Framework", "-");
// laravel-5-framework
```

```studly_case()
studly_case()
将字串转换为 StudlyCase 型

$value = studly_case('foo_bar');
// FooBar
```

```title_case()
title_case()

```

## URLs

```action()
action()
```

```asset()
asset()
```

```secure_asset()
secure_asset()
```

```route()
route()
```

```secure_url()
secure_url()
```

```url()
url()
```

## 其他

### abort()
```abort()
abort()
```

### abort_if()
```abort_if()
abort_if()
```

### abort_unless()
```abort_unless()
abort_unless()
```

```auth()
auth()
```

### back()
```back()
back()
```

### bcrypt()
```bcrypt()
bcrypt()
```

### cache()
```cache()
cache()
may be used to get values from the cache. If the given key does not exist in the cache, an optional default value will be returned

$value = cache('key');
$value = cache('key', 'default');

You may add items to the cache by passing an array of key / value pairs to the function. You should also pass the number of minutes or duration the cached value should be considered valid

cache(['key' => 'value'], 5);
cache(['key' => 'value'], Carbon::now()->addSeconds(10));
```

### collect()
```collect()
collect()
```

### config()
```config()
config()
gets the value of a configuration variable. The configuration values may be accessed using "dot" syntax, which includes the name of the file and the option you wish to access. A default value may be specified and is returned if the configuration option does not exist

$value = config('app.timezone');
$value = config('app.timezone', $default);

The config helper may also be used to set configuration variables at runtime by passing an array of key / value pairs

config(['app.debug' => true]);
```

### dd()
```dd()
dd()
dumps the given variables and ends execution of the script

dd($value);
dd($value1, $value2, $value3, ...);

If you do not want to halt the execution of your script, use the dump function instead
dump($value);
```

### env()
```env()
env()
gets the value of an environment variable or returns a default value

$env = env('APP_ENV');
// Return a default value if the variable doesn't exist...
$env = env('APP_ENV', 'production');
```

### factory()
```factory()
factory()
creates a model factory builder for a given class, name, and amount. It can be used while testing or seeding

$user = factory(App\User::class)->make();
```

### info()
```info()
info()
will write information to the log
info('Some helpful information!');

An array of contextual data may also be passed to the function
info('User login attempt failed.', ['id' => $user->id]);
```

### logger()
```logger()
logger()
can be used to write a debug level message to the log
logger('Debug message');

An array of contextual data may also be passed to the function
logger('User has logged in.', ['id' => $user->id]);

A logger instance will be returned if no value is passed to the function
logger()->error('You are not allowed here.');
```

### request()
```request()
request()
returns the current request instance or obtains an input item

$request = request();
$value = request('key', $default = null)
```

### response()
```response()
response()
creates a response instance or obtains an instance of the response factory

return response('Hello World', 200, $headers);
return response()->json(['foo' => 'bar'], 200, $headers);
```

### value()
```value()
value()
```






## 路径
```app_path()
app_path()
returns the fully qualified path to the app directory. You may also use the  app_path function to generate a fully qualified path to a file relative to the application directory

$path = app_path();
$path = app_path('Http/Controllers/Controller.php');
```

```base_path()
base_path()
returns the fully qualified path to the project root. You may also use the  base_path function to generate a fully qualified path to a given file relative to the project root directory

$path = base_path();
$path = base_path('vendor/bin');
```

```config_path()
config_path()
returns the fully qualified path to the application configuration directory

$path = config_path();
```

```database_path()
database_path()

returns the fully qualified path to the application's database directory

$path = database_path();
```

```elixir()
elixir()
gets the path to a versioned Elixir file

elixir($file);
```

```public_path()
public_path()
returns the fully qualified path to the public directory

$path = public_path();
```

```resource_path()
resource_path()

returns the fully qualified path to the resources directory. You may also use the resource_path function to generate a fully qualified path to a given file relative to the storage directory

$path = resource_path();
$path = resource_path('assets/sass/app.scss');
```

```storage_path()
storage_path()

returns the fully qualified path to the storage directory. You may also use the storage_path function to generate a fully qualified path to a given file relative to the storage directory

$path = storage_path();
$path = storage_path('app/file.txt');
```


