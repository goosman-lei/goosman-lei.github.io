#  Ice多APP支持
## 为什么需要多APP支持？

* ice框架是为了服务化而设计，倾向于大型项目拆分为小型项目，独立服务部署，解决开发复杂度。
* 但对于许多小的项目，可能需要在ice中支持多个APP，比如客户端api， 网站www， 移动端h5， 后台admin等，
* 对于这些项目，期初可能都会有公用的逻辑，如果全部拆出来，刚启动的项目人力上不够充足,
* 因此特地在Ice中支持多APP

### 为什么要选择Ice?

* Ice的优点是可以支持Local, Remote, Internal调用，现有的许多服务已经使用了Ice开发，可以直接嵌入到新的
* 项目中使用，另外Ice中的许多机制可以使用，如资源管理，数据库lib等

#  实现步骤
* 说明: 我的项目命名空间: 'zeus\ares'
1.将原来的action目录降两级，形成apps/appName/action目录,如下图:
```
src/
├── apps
│   ├── admin
│   │   ├── action
│   │   │   ├── Index
│   │   │   └── Say
│   │   ├── filter
│   │   ├── lib
│   │   ├── smarty-tpl
│   │   │   ├── Index
│   │   │   ├── _common
│   │   │   └── say
│   │   └── webroot
│   └── www
│       ├── action
│       │   └── Say
│       ├── filter
│       ├── lib
│       ├── smarty-tpl
│       │   ├── _common
│       │   └── say
│       └── webroot
├── conf
├── daemon
├── lib
├── model
├── service
── webroot
```
2.修改conf/app.php文件
runner中注册不同的appName, 如www, admin， 并include相应app的配置进来
```
<?php
$namespace = 'zeus\ares';
$app_class = '\\Ice\\Frame\\App';

$root_path = __DIR__ . '/..';
$var_path  = $root_path . '/../var';
$run_path  = $var_path . '/run';
$log_path  = $var_path . '/logs';

@include(__DIR__ . '/service.inc');
@include(__DIR__ . '/daemon.inc');
@include(__DIR__ . '/www.inc');
@include(__DIR__ . '/admin.inc');

$runner = array(
    'www' => array(
        'frame'       => $www_frame,
        'routes'      => $www_routes,
        'temp_engine' => $www_temp_engine,
        'log'         => $www_logger,
        'filter'      => $www_filter,
    ),
    'admin' => array(
        'frame'       => $admin_frame,
        'routes'      => $admin_routes,
        'temp_engine' => $admin_temp_engine,
        'log'         => $admin_logger,
        'filter'      => $admin_filter,
    ),
    'service' => array(
        'log'    => $service_logger,
        'filter' => $service_filter,
    ),
    'daemon' => array(
        'log'    => $daemon_logger,
        'filter' => $daemon_filter,
    ),
);
```
3.在每个app下面创建webroot/web.php
```
<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
$config = array(
    'sub_dir'   => 'apps/www',
    'name'      => 'www',
);

$runner = new \zeus\ares\Lib\WebRunner(__DIR__ . '/../../../conf/app.php', $config);
$runner->run();
```
4.在lib中添加lib/WebRunner.php
```
<?php
namespace zeus\ares\Lib;
class WebRunner extends \Ice\Frame\Runner\Web{
    private $subAppConfig = array();
    public function __construct($confPath, $subAppConfig){
        $this->subAppConfig = $subAppConfig;
        $this->initSubApp();
        parent::__construct($confPath);
    }

    public function initSubApp(){
        $this->name = $this->subAppConfig['name'];
    }

    public function getAppActionPath(){
        $subdir = str_replace("/", "\\", $this->subAppConfig['sub_dir']);
        $subAppAction = "\\zeus\\ares\\{$subdir}\\action";
        return $subAppAction;
    }
}
```
5.修改nginx，将web打到对应app的webroot下
