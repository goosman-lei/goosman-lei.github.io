<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>Ice framework By Goosman.lei</title>

    <!-- Styles -->
    <link href="{%$path_info.static_base_url%}/css/theDocs.all.min.css" rel="stylesheet">
    <link href="{%$path_info.static_base_url%}/css/theDocs.css" rel="stylesheet">
    <link href="{%$path_info.static_base_url%}/css/custom.css" rel="stylesheet">

    <!-- Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Raleway:100,300,400,500%7CLato:300,400' rel='stylesheet' type='text/css'>

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{%$path_info.static_base_url%}/apple-touch-icon.png">
    <link rel="icon" href="{%$path_info.static_base_url%}/img/favicon.ico">
  </head>

  <body>

    <header class="site-header">

      <!-- Top navbar & branding -->
      <nav class="navbar navbar-default">
        <div class="container">

          <!-- Toggle buttons and brand -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar" aria-expanded="true" aria-controls="navbar">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </button>

            <button type="button" class="navbar-toggle for-sidebar" data-toggle="offcanvas">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="">Goosman.lei</a>
          </div>
          <!-- END Toggle buttons and brand -->

          <!-- Top navbar -->
          <div id="navbar" class="navbar-collapse collapse" aria-expanded="true" role="banner">
            <ul class="nav navbar-nav navbar-right">
              {%foreach $topnav_infos as $topnav_info%}
                <li class="{%if $topnav_info.class%}{%$topnav_info.class%}{%/if%}{%if $topnav_info.sn == $curr_topnav%} active{%/if%}"{%if isset($topnav_info.target) && $topnav_info.target%} target="{%$topnav_info.target%}"{%/if%}><a href="{%$topnav_info.link%}">{%$topnav_info.text%}</a></li>
              {%/foreach%}
            </ul>
          </div>
          <!-- END Top navbar -->

        </div>
      </nav>
      <!-- END Top navbar & branding -->
      
    </header>


    <main class="container">
      <div class="row">

        <!-- Sidebar -->
        <aside class="col-md-3 col-sm-3 sidebar">

<!-- TODO 解递归当场遍历出树结构菜单 -->
{%php%}
$sidenav_stack = array(
    array(-1, $_smarty_tpl->tpl_vars['sidenav_infos']->value)
);
$sidenav_space = '  ';

echo '<ul class="sidenav">' . chr(10);
while (!empty($sidenav_stack)) {
    $stack_depth    = count($sidenav_stack);
    $current_info   = array_pop($sidenav_stack);
    $internal_index = $current_info[0];
    $internal_datas = $current_info[1];

    while ($internal_index + 1 < count($internal_datas)) {
        $internal_index ++;
        $tmp_data = $internal_datas[$internal_index];
        if ($tmp_data['sn'] == $_smarty_tpl->tpl_vars['curr_sidenav']) {
            $tmp_data['class'] = isset($tmp_data['class']) && $tmp_data['class'] ? $tmp_data['class'] . ' active' : 'active';
            $_smarty_tpl->assign('page', isset($tmp_data['page']) ? $tmp_data['page'] : array());
        }
        echo str_repeat($sidenav_space, $stack_depth) . '<li><a href="' . $tmp_data['link'] . '"'
            . ($tmp_data['class'] ? ' class="' . $tmp_data['class'] . '"' : '')
            . ($tmp_data['target'] ? ' target="' . $tmp_data['target'] . '"' : '')
            . '>' . $tmp_data['text'] . '</a>' . chr(10);
        if (!empty($tmp_data['children'])) {
            echo str_repeat($sidenav_space, $stack_depth) . '<ul>' . chr(10);
            array_push($sidenav_stack, array($internal_index, $internal_datas));
            array_push($sidenav_stack, array(-1, $tmp_data['children']));
            continue 2;
        }
    }
    echo str_repeat($sidenav_space, $stack_depth - 1) . '</ul>' . ($stack_depth > 1 ? '</li>' : '') . chr(10);
}
{%/php%}
        </aside>
        <!-- END Sidebar -->


        <!-- Main content -->
        <article class="col-md-9 col-sm-9 main-content" role="main">
          
          <header>
            <h1>{%$page.title|default:''%}</h1>
            <p>{%$page.desc|default:''%}</p>
{%$page.md|render_toc%}
          </header>

          <section>
{%$page.md|render_markdown%}
          </section>

          
        </article>
        <!-- END Main content -->
      </div>
    </main>


    <!-- Footer -->
    <footer class="site-footer">
      <div class="container">
        <a id="scroll-up" href="#"><i class="fa fa-angle-up"></i></a>

        <div class="row">
          <div class="col-md-6 col-sm-6">
            <p>Copyright &copy; 2016. All right reserved(京ICP备11026137号-2)</p>
          </div>
          <div class="col-md-6 col-sm-6">
<script language="javascript" type="text/javascript" src="http://js.users.51.la/18906058.js"></script>
<noscript><a href="http://www.51.la/?18906058" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/18906058.asp" style="border:none" /></a></noscript>
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->

    <!-- Scripts -->
    <script src="{%$path_info.static_base_url%}/js/theDocs.all.min.js"></script>
    <script src="{%$path_info.static_base_url%}/js/theDocs.js"></script>
    <script src="{%$path_info.static_base_url%}/js/custom.js"></script>

  </body>
</html>
