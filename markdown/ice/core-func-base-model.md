# Model基类
** 注意：本文档中的所有代码示例，仅为演示方法的使用，不代表实现功能的正确或合适方法 **

在Ice中建立自己的Model，除从 Ice_DB_Query 继承外，亦可从 Ice_DB_Model 继承。本类提供了诸多更方便日常使用的方法，使你大部分的数据库操作可直接使用这些方法完成而无需再写自己得方法，大大加快你的开发效率。

Ice_DB_Model 继承自 Ice_DB_Query，因此具有原 Ice_DB_Query 类的所有功能和特性。除此之外，它具的方便日常使用的方法如下：


### add($data) 插入数据
向表中插入一条数据，基本与 Ice\DB\Query::insert 相同，本方法后续会提供自动处理功能，因此建议使用本方法插入数据。
```php
<?php
namespace <PROJECT_NAMESPACE>\Model;
class User extends \Ice_DB_Model {
     * 此处省略若干代码
}

# call:
$data = [
    'id'    => 99,
    'name'  => 'user1',
    'age'   => 29,
    'sex'   => '男',
];
$result = $model->add($data);
print_r($result);

# output:
# 1
```

### getInfoByPk($pk) 根据主键获取一条数据
```php
<?php
//获取id为99的用户信息
$result = $model->getInfoByPk(99);
print_r($result);

# output:
Array
(
    [id] => 99
    [name] => user1
    [age] => 29
    [sex] => 男
)
```

### getInfoByAssoc($data) 根据一个关联数组条件获取单条数据
关联数组将被拼为 k1=v1 AND k2=v2 格式条件
```php
<?php
# 获取一个年龄29岁的男性用户信息
$result = $model->getInfoByAssoc(['age' => 29, 'sex' => '男']);
print_r($result);

# output:
Array
(
    [id] => 99
    [name] => user1
    [age] => 29
    [sex] => 男
)
```

### getListByPks($pks) 根据多个主键批量获取信息列表
```php
<?php
# 获取id为1，2的用户信息
$result = $model->getListByPks([1,2]);
print_r($result);

# output:
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => user1
            [age] => 29
            [sex] => 男
        )

    [1] => Array
        (
            [id] => 2
            [name] => user1
            [age] => 29
            [sex] => 男
        )

)
```

### getListByAssoc($data, ……) 根据一个关联数组条件获取列表数据
关联数组将被拼为 k1=v1 AND k2=v2 格式条件
```php
<?php
# 获取全部年龄29岁的男性用户信息
$result = $model->getListByAssoc(['age' => 29, 'sex' => '男']);
print_r($result);

# output:
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => user1
            [age] => 29
            [sex] => 男
        )

    [1] => Array
        (
            [id] => 2
            [name] => user1
            [age] => 29
            [sex] => 男
        )

)
```

### updateByPk($data, $pk) 根据主键更新数据
```php
<?php
# 修改id为99的用户年龄为30岁，名字为 newNmae
$result = $model->updateByPk(['age' => 29, 'name' => 'newNmae'], 99);
print_r($result);

# output:
# 1
```

### updateByAssoc($data, $$assoc) 根据一个关联数组条件更新数据
关联数组将被拼为 k1=v1 AND k2=v2 格式条件
```php
<?php
# 修改所有女性用户的年龄为18岁
$result = $model->updateByAssoc(['age' => 18], ['sex' => '女']);
print_r($result);

# output:
11
```

### setFieldByPk($field, $value, $pk) 根据主键设置某列数据
```php
<?php
# 将id为99的用户性别设置为女
$result = $model->setFieldByPk('sex', '女', 99);
print_r($result);

# output:
# 1
```

### incrFieldByPk($field, $pk, $step = 1) 根据主键给某字段+1(或更多)
```php
<?php
# 给id为99的用户年龄+1
$result = $model->incrFieldByPk('age', 99);
print_r($result);

# output:
# 1

# 给id为99的用户年龄+5
$result = $model->incrFieldByPk('age', 99, 5);
print_r($result);

# output:
# 1
```

### decrFieldByPk($field, $pk, $step = 1) 根据主键给某字段-1(或更多)
```php
<?php
# 给id为99的用户年龄-1
$result = $model->decrFieldByPk('age', 99);
print_r($result);

# output:
# 1

# 给id为99的用户年龄-5
$result = $model->decrFieldByPk('age', 99, 5);
print_r($result);

# output:
# 1
```

### deleteByPk($pk) 根据主键删除数据
```php
<?php
# 将id为99的用户记录删除
$result = $model->deleteByPk(99);
print_r($result);

# output:
# 1
```

### deleteByAssoc($data) 根据一个关联数组条件删除数据
关联数组将被拼为 k1=v1 AND k2=v2 格式条件
```php
<?php
# 将所有18岁女性用户删除
$result = $model->deleteByAssoc(['age' => 18, 'sex' => '女']);
print_r($result);


# output:
# 11
```