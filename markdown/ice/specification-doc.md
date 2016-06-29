# 注解规范

文档是一个项目的必要组成部分. Ice基于PHPDoc的规范, 取其中比较重要的Tag, 构建自己的注解系统.

基于Ice注解规范的注解, 可以用来自动产生文档静态站.

好处:

* 文档和代码放在一起, 不割裂尽量保证维护的及时性

* 自动生成静态文档站

## 哪些代码需要文档注解

* Action层代码

* Service层代码

* Daemon层代码

## 怎么写文档注解

```php
<?php
namespace xxx;
/**
 * <类的功能说明>
 * @copyright <版权说明部分>
 * @author <作者信息部分>
 */
class A {
    /**
     * <方法功能说明>

     * @param <参数类型> <$开头的参数名>
     * @param string $arg1 
     * @param int $arg2 
     * @param mixed $arg3 

     * @error 39337 输入错误
     * @error 39333 内部错误

     * @return <类型> <返回值说明>
     * @return array {
     *     "code": int,
     *     "data": string
     * }
     */
    public function foo($arg1, $arg2, $arg3) {
        return array('code' => 1, 'data' => 'Hi');
    }
}
```

## 怎么生成文档站

```bash
# 直接运行构建出的项目的

bin/api-doc-gen

# 目前该工具仅解析出service层注解的结构化数据, 渲染HTML及其他层次解析的工作正在开发中.
```