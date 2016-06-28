#  单元测试

## Service层单元测试

```php
# Service层的单元测试, 编写在test/Service/<ClassName>/<MethodName>.php

# 名字空间
namespace <PROJECT_GROUP>/<PROJECT_NAME>/UT/Service/<ClassName>;
# 类名. 继承
classname <MethodName> extends \FW_UT {
    # 注解声明这个方法是一个Case
    /**
     * @test
     */
    public function scene_1() {
        # 静态方式获取服务代理对象, 调用
        $proxy = \F_Ice::$ins->workApp->proxy_service->get('internal', 'Say');
        $data  = $proxy->hello('Jack');

        $this->assertEquals(0, $data['code']);
    }
}

```

## Action层单元测试

```php
# Action层的单元测试, 编写在test/Action/<ClassName>/<MethodName>.php

# 名字空间
namespace <PROJECT_GROUP>/<PROJECT_NAME>/UT/Action/<ClassName>;
# 类名. 继承
classname <MethodName> extends \FW_UT {
    # 注解声明这个方法是一个Case
    /**
     * @test
     */
    public function scene_1() {
        # 如果要MOCK输入数据. 直接修改runner->request等对象
        \F_Ice::$ins->runner->request = xxx;

        # 通过便利方法直接调用
        $data  = $this->callAction('Say', 'Helloworld');

        $this->assertEquals(0, $data['code']);
    }
}

```

## 单元测试的运行

参考bin/ut的示例工具命令