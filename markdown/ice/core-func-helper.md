# 辅助函数
** 注意：本文档中的所有代码示例，仅为演示方法的使用，不代表实现功能的正确或合适方法 **

## 数组
### array_add()
```
array_add()
向数组中添加一个键-值对(如果给定的键不存在)
$array = array_add(['name' => 'Desk'], 'price', 100);
// ['name' => 'Desk', 'price' => 100]
```

### array_collapse()
```array_collapse()
array_collapse()
将一个数组的数组打散合并到一个单一数组
$array = array_collapse([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);
// [1, 2, 3, 4, 5, 6, 7, 8, 9]
```

### array_divide()
```array_divide()
array_divide()
返回两个数组，一个包含原数组的所有键，另一个包含原数组的所有值
list($keys, $values) = array_divide(['name' => 'Desk']);
// $keys: ['name']
// $values: ['Desk']
```

### array_dot()
```array_dot()
array_dot()
将一个多维数组转换为一维数组，并使用点号指示深度
$array = array_dot(['foo' => ['bar' => 'baz']]);
// ['foo.bar' => 'baz'];
```
### array_except()
```array_except()
array_except()
从一个数组中移除指定的键/值对
$array = ['name' => 'Desk', 'price' => 100];
$array = array_except($array, ['price']);
// ['name' => 'Desk']
```

### array_first()
```array_first()
array_first()
返回数组中第一个通过判断返回为真的元素
$array = [100, 200, 300];
$value = array_first($array, function ($key, $value) {
        return $value >= 150;
});
// 200

默认值可作为第三个参数传入。如果没有值通过判断，将返回默认值：
$value = array_first($array, $callback, $default);
```

### array_flatten()
```array_flatten()
array_flatten()
将一个多维数组转换为一维数组
$array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];
$array = array_flatten($array);
// ['Joe', 'PHP', 'Ruby'];
```

### array_forget()
```array_forget
array_forget()
基于点号路径从一个深度嵌套的数组中移除指定的键/值对
$array = ['products' => ['desk' => ['price' => 100]]];
array_forget($array, 'products.desk');
// ['products' => []]
```

### array_get()
```array_get()
array_get()
基于点号路径从一个深度嵌套的数组中取出值
$array = ['products' => ['desk' => ['price' => 100]]];
$value = array_get($array, 'products.desk');
// ['price' => 100]

也接受默认值，如果指定的键未找到，返回默认值
$value = array_get($array, 'names.john', 'default');
```

### array_has()
```array_has()
array_has() checks that a given item exists in an array using "dot" notation

$array = ['products' => ['desk' => ['price' => 100]]];
$hasDesk = array_has($array, ['products.desk']);
// true
```

### array_only()
```array_only()
array_only() 从给定的数组中返回指定的键/值对

$array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];
$array = array_only($array, ['name', 'price']);
// ['name' => 'Desk', 'price' => 100]
```

### array_last()
```array_last()
array_last()

```

### array_pluck()
```array_pluck()
array_pluck()
从给定的数组中提取出键/值对

$array = [
        ['developer' => ['id' => 1, 'name' => 'Taylor']],
        ['developer' => ['id' => 2, 'name' => 'Abigail']],
];
$array = array_pluck($array, 'developer.name');
// ['Taylor', 'Abigail'];

You may also specify how you wish the resulting list to be keyed

$array = array_pluck($array, 'developer.name', 'developer.id');
// [1 => 'Taylor', 2 => 'Abigail'];
```

```array_prepend()
array_prepend()
```

```array_pull()
array_pull()
从数组中移除并返回一个键/值对
$array = ['name' => 'Desk', 'price' => 100];
$name = array_pull($array, 'name');
// $name: Desk
// $array: ['price' => 100]
```

### array_set()
```array_set()
array_set()
基于点号路径为一个深度嵌套的数组设置值
$array = ['products' => ['desk' => ['price' => 100]]];
array_set($array, 'products.desk.price', 200);
// ['products' => ['desk' => ['price' => 200]]]
```

```array_sort()
array_sort()
依据给定闭包的返回值排序数组
$array = [
        ['name' => 'Desk'],
        ['name' => 'Chair'],
];
$array = array_values(array_sort($array, function ($value) {
        return $value['name'];
}));

/*
[
   ['name' => 'Chair'],
   ['name' => 'Desk'],
]
 */
```

### array_sort_recursive()
```array_sort_recursive()
array_sort_recursive()
用 sort 函数递归排序数组
$array = [
        [
                'Roman',
                'Taylor',
                'Li',
        ],
        [
                'PHP',
                'Ruby',
                'JavaScript',
        ],
];

$array = array_sort_recursive($array);

/*
[
   [
        'Li',
        'Roman',
        'Taylor',
   ],
   [
        'JavaScript',
        'PHP',
        'Ruby',
   ]
];
 */
```

### array_where()
```array_where()
array_where()
用给定闭包过滤数组
$array = [100, '200', 300, '400', 500];

$array = array_where($array, function ($key, $value) {
        return is_string($value);
});

// [1 => 200, 3 => 400]
```

### head()
```head()
head()
简单返回给定数组的第一个元素
$array = [100, 200, 300];
$first = head($array);
// 100
```

### last()
```last()
last()
返回给定数组的最后一个元素
$array = [100, 200, 300];
$last = last($array);
// 300
```

## 路径
```app_path()
app_path()
```

```base_path()
base_path()
```

```config_path()
config_path()
```

```database_path()
database_path()
```

```elixir()
elixir()
```

```public_path()
public_path()
```

```resource_path()
resource_path()
```

```storage_path()
storage_path()
```

## 字符串

```camel_case()
camel_case()
```

```class_basename()
class_basename()
```

```e()
e()
```

```ends_with()
ends_with()
```

```snake_case()
snake_case()
```

```str_limit()
str_limit()
```

```starts_with()
starts_with()
```

```str_contains()
str_contains()
```

```str_finish()
str_finish()
```

```str_is()
str_is()
```

```str_plural()
str_plural()
```

```str_random()
str_random()
```

```str_singular()
str_singular()
```

```str_slug()
str_slug()
```

```studly_case()
studly_case()
```

```title_case()
title_case()
```

```trans()
trans()
```

```trans_choice()
trans_choice()
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
```abort()
abort()
```

```abort_if()
abort_if()
```

```abort_unless()
abort_unless()
```

```auth()
auth()
```

```back()
back()
```

```bcrypt()
bcrypt()
```

```cache()
cache()
```

```collect()
collect()
```

```config()
config()
```

```csrf_field()
csrf_field()
```

```csrf_token()
csrf_token()
```

```dd()
dd()
```

```dispatch()
dispatch()
```

```env()
env()
```

```event()
event()
```

```factory()
factory()
```

```info()
info()
```

```logger()
logger()
```

```method_field()
method_field()
```

```old()
old()
```

```redirect()
redirect()
```

```request()
request()
```

```response()
response()
```

```session()
session()
```

```value()
value()
```

```view()
view()
```
