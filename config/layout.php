<?php
$topnav = array(
    array(
        'sn'    => 'ice',
        'link'  => '/ice',
        'text'  => 'Ice',
        'class' => '',
    ),
    array(
        'sn'    => 'blog-history',
        'link'  => '/blog-history',
        'text'  => 'CSDN-BLOG',
        'class' => '',
        'page' => array (
          'md' => '/blog.csdn/index.md',
          'seo_title' => 'Goosman.lei 博客',
        ),
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
        'sn'     => '/slide',
        'link'   => 'http://static-cdn.tec-inf.com/pdf/001.ice-use-slide.pdf',
        'text'   => 'Ice介绍PPT',
        'class'  => '',
        'target' => '_blank',
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
                    'seo_title' => 'PHP Ice框架整体架构介绍',
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
                    'seo_title' => 'PHP Ice框架Feature介绍',
                    'seo_keywords' => '客户端API开发,版本兼容,API版本,API兼容',
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
                    'seo_title' => 'PHP Ice框架资源管理介绍',
                    'seo_keywords' => '高可用,自动化降级,降级,PHP框架,PHP开发',
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
                    'seo_title' => 'PHP Ice框架数据过滤层介绍',
                    'seo_keywords' => '数据校验,数据过滤,数据验证,PHP框架,PHP开发',
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
                    'seo_title' => 'PHP Ice框架运行方式介绍',
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
                            'seo_title' => 'PHP Ice框架运行方式介绍',
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
                            'seo_title' => 'PHP Ice框架运行方式介绍',
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
                            'seo_title' => 'PHP Ice框架运行方式介绍',
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
                            'seo_title' => 'PHP Ice框架运行方式介绍',
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
                    'seo_title' => 'PHP Ice框架交互数据封装',
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
                    'seo_title' => 'PHP Ice框架日志处理',
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
                    'seo_title' => 'PHP Ice框架数据库访问层',
                    'seo_keywords' => 'PHP框架,PHP开发,PHP 数据库访问,数据库访问代码,mysql查询',
                ),
            ),
            array(
                'sn'     => '/manual/base_model',
                'link'   => '/ice/core-func-base-model.html',
                'text'   => 'Model基础封装',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/core-func-base-model.md',
                    'seo_title' => 'PHP Ice框架Model层封装',
                    'seo_keywords' => 'PHP框架,PHP开发,PHP 数据库访问,数据库访问代码,mysql查询',
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
                    'seo_title' => 'PHP Ice框架单元测试介绍',
                    'seo_keywords' => 'PHP框架,PHP开发,PHP单元测试,PHP Unit,覆盖率',
                ),
            ),
            array(
                'sn'     => '/manual/mapp',
                'link'   => '/ice/multi-app-support.html',
                'text'   => '多APP支持',
                'class'  => '',
                'target' => '',
                'page'   => array(
                    'md' => '/ice/multi-app-support.md',
                    'seo_title' => '多APP支持',
                    'seo_keywords' => '多APP支持',
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
                    'seo_title' => 'PHP Ice框架命名规范',
                    'seo_keywords' => 'PHP框架,PHP开发,PHP 命名规范,规范,PSR-4',
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
                    'seo_title' => 'PHP Ice框架开发规范',
                    'seo_keywords' => 'PHP框架,PHP开发,开发规范',
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
                    'seo_title' => 'PHP Ice框架注解规范',
                    'seo_keywords' => 'PHP框架,PHP开发,注解,PHPDoc,PHP文档',
                ),
            ),
        ),
    ),
);
$main = array(
    'path_info' => array(
        'static_base_url' => 'http://static-cdn.tec-inf.com'
    ),
    'topnav_infos' => $topnav,
    'sidenav_infos' => $sidenav,
);
