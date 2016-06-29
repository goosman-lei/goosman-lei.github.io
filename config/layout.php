<?php
$topnav = array(
    array(
        'sn'    => 'ice',
        'link'  => '/ice',
        'text'  => 'Ice',
        'class' => '',
    ),
    array(
        'sn'    => 'blog',
        'link'  => '',
        'text'  => 'Blog',
        'class' => '',
    ),
    array(
        'sn'    => 'contact',
        'link'  => 'mailto:goosman.lei@gmail.com',
        'text'  => 'Contact',
        'class' => 'hero',
    ),
);
$sidenav = array(
    array(
        'sn'   => '/overview',
        'link' => '/ice',
        'text' => '概览',
        'class' => '',
        'target' => '',
        'page'   => array(
            'md' => '/ice/index.md',
        ),
    ),
    array(
        'sn'   => '/project',
        'link' => '#',
        'text' => '项目',
        'class' => '',
        'target' => '',
        'children' => array(
            array(
                'sn'     => '/project/github',
                'link'   => 'https://github.com/goosman-lei/ice',
                'text'   => 'Github主页',
                'class'  => '',
                'target' => '_blank',
            ),
            array(
                'sn'     => '/project/download-zipball',
                'link'   => 'https://github.com/goosman-lei/ice/zipball/master',
                'text'   => 'zip包下载',
                'class'  => '',
                'target' => '_blank',
            ),
            array(
                'sn'     => '/project/download-tarball',
                'link' => 'https://github.com/goosman-lei/ice/tarball/master',
                'text' => 'tar.gz包下载',
                'class' => '',
                'target' => '_blank',
            ),
        ),
    ),
    array(
        'sn'     => '/manual',
        'link'   => '',
        'text'   => '文档',
        'class'  => '',
        'target' => '',
        'children' => array(
            array(
                'sn'     => '/manual/arch',
                'link'   => '/ice/arch.html',
                'text'   => '整体架构',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/arch.md',
                ),
            ),
            array(
                'sn'       => '/manual/runner',
                'link'     => '/ice/core-func-runner.html',
                'text'     => '运行方式',
                'class'    => '',
                'target'   => '',
                'page'   => array(
                    'md' => '/ice/core-func-runner.md',
                ),
                'children' => array(
                    array(
                        'sn'     => '/manual/runner/web',
                        'link'   => '/ice/core-func-runner-web.html',
                        'text'   => 'Web',
                        'class'  => '',
                        'target' => '',
                        'page'   => array(
                            'md'     => '/ice/core-func-runner-web.md',
                        ),
                    ),
                    array(
                        'sn'     => '/manual/runner/service',
                        'link'   => '/ice/core-func-runner-service.html',
                        'text'   => 'Service',
                        'class'  => '',
                        'target' => '',
                        'page'   => array(
                            'md' => '/ice/core-func-runner-service.md',
                        ),
                    ),
                    array(
                        'sn'     => '/manual/runner/daemon',
                        'link'   => '/ice/core-func-runner-daemon.html',
                        'text'   => 'Daemon',
                        'class'  => '',
                        'target' => '',
                        'page'   => array(
                            'md' => '/ice/core-func-runner-daemon.md',
                        ),
                    ),
                    array(
                        'sn'     => '/manual/runner/embeded',
                        'link'   => '/ice/core-func-runner-embeded.html',
                        'text'   => 'Embeded',
                        'class'  => '',
                        'target' => '',
                        'page'   => array(
                            'md' => '/ice/core-func-runner-embeded.md',
                        ),
                    ),
                ),
            ),
            array(
                'sn'     => '/manual/input-output',
                'link'   => '/ice/core-func-input-output.html',
                'text'   => '交互数据',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-input-output.md',
                ),
            ),
            array(
                'sn'     => '/manual/resource',
                'link'   => '/ice/core-func-resource.html',
                'text'   => '资源管理',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-resource.md',
                ),
            ),
            array(
                'sn'     => '/manual/filter',
                'link'   => '/ice/core-func-filter.html',
                'text'   => '过滤器',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-filter.md',
                ),
            ),
            array(
                'sn'     => '/manual/feature',
                'link'   => '/ice/core-func-feature.html',
                'text'   => 'Feature机制',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-feature.md',
                ),
            ),
            array(
                'sn'     => '/manual/logger',
                'link'   => '/ice/core-func-logger.html',
                'text'   => '日志处理',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-logger.md',
                ),
            ),
            array(
                'sn'     => '/manual/db',
                'link'   => '/ice/core-func-db.html',
                'text'   => 'DB查询工具',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-db.md',
                ),
            ),
            array(
                'sn'     => '/manual/ut',
                'link'   => '/ice/core-func-ut.html',
                'text'   => '单元测试',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-ut.md',
                ),
            ),
        ),
    ),
    array(
        'sn'   => '/specification',
        'link' => '',
        'text' => '规范',
        'class' => '',
        'target' => '',
        'children' => array(
            array(
                'sn'     => '/specification/name',
                'link'   => '/ice/specification-name.html',
                'text'   => '命名规范',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/specification-name.md',
                ),
            ),
            array(
                'sn'     => '/specification/develop',
                'link'   => '/ice/specification-develop.html',
                'text'   => '开发规范',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/specification-develop.md',
                ),
            ),
            array(
                'sn'     => '/specification/doc',
                'link'   => '/ice/specification-doc.html',
                'text'   => '注解规范',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/specification-doc.md',
                ),
            ),
        ),
    ),
);
$main = array(
    'path_info' => array(
        'static_base_url' => '/static'
    ),
    'topnav_infos' => $topnav,
    'sidenav_infos' => $sidenav,
);
