#  单元测试

## Service层单元测试

```php
# Service层的单元测试, 编写在test/Service/<ClassName>/<MethodName>Test.php

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
# Action层的单元测试, 编写在test/Action/<ClassName>/<MethodName>Test.php

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

## Mock内部调用示例

```php

class DemoModel {
    public function insert() {
        // 数据库操作
    }
}

class DemoService {
    public function say() {
        $model  = new DemoModel();
        $lastId = $model->insert();
        if ($lastId > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

假定上面场景, DemoService的say()方法需要进行单元测试. 它内部调用了DemoModel的实例方法insert().

由于insert()方法涉及DB操作, 这样就使得单元测试的运行依赖于环境.

对于这种Case, 建议通过下面Mock的方式解除对环境的依赖.

class DemoServiceSayTestCase extends \FW_UT {
    /**
     * @test
     */
    public function sense_normal() {
        $modelMock = new \PHPUnit_Extensions_MockClass('DemoModel', ['insert'], $this);
        $modelMock->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));

        $service = new DemoService;
        $this->assertTrue($service->say());
    }

    /**
     * @test
     */
    public function sense_insert_failed() {
        $modelMock = new \PHPUnit_Extensions_MockClass('DemoModel', ['insert'], $this);
        $modelMock->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(FALSE));

        $service = new DemoService;
        $this->assertFalse($service->say());
    }

}
```