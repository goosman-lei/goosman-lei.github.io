
 
由于CSDN博客系统的代码编辑器bug, 只好以文本形式提供
 
<?php
/**
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 */
header("Content-Type: text/html; charset=utf-8;");
function user_cputime() {
$rusage = getrusage();
return $rusage['ru_utime.tv_sec'] + $rusage['ru_utime.tv_usec'] / 1000000;
}
function system_cputime() {
$rusage = getrusage();
return $rusage['ru_stime.tv_sec'] + $rusage['ru_stime.tv_usec'] / 1000000;
}
$cpu_time_tmp = array();
function cputime_start() {
global $cpu_time_tmp;
$rusage = getrusage();
$cpu_time_tmp[] = $rusage['ru_utime.tv_sec'] + $rusage['ru_utime.tv_usec'] / 1000000;
$cpu_time_tmp[] = $rusage['ru_stime.tv_sec'] + $rusage['ru_stime.tv_usec'] / 1000000;
}
function cputime_end() {
global $cpu_time_tmp;
$rusage = getrusage();
printf("user_cpu: %.8f, system_cpu: %.8f<br />/n", $rusage['ru_utime.tv_sec'] + $rusage['ru_utime.tv_usec'] / 1000000 - $cpu_time_tmp[0], $rusage['ru_stime.tv_sec'] + $rusage['ru_stime.tv_usec'] / 1000000 - $cpu_time_tmp[1]);
$cpu_time_tmp = array();
}
?>
<?php
isset($_POST['code']) and $code = preg_replace("/////(.)/", "$1", $_POST['code']);;
?>
<body onkeypress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13)) document.forms['input'].submit();">
<form action="<?php echo $_SERVER[PHP_SELF]; ?>" method="POST" name="input">

```

<?php echo $code; ?>

```

<input type="submit" value="submit" /><input type="reset" value="reset" />
<br />
<input id="pre" type="button" value="pre" />
<input id="printr" type="button" value="printr" />
<input id="vardump" type="button" value="vardump" />
<input id="time" type="button" value="time" />
<input id="usercpu" type="button" value="usercpu" />
<input id="systemcpu" type="button" value="systemcpu" />
<input id="cpu" type="button" value="cpu" />
<a href="<?php echo $_SERVER[PHP_SELF]; ?>">重置本页</a>
<a href="<?php echo $_SERVER[PHP_SELF]; ?>" target="_blank">新开页签</a>
</form>
<?php
if(isset($_POST['code'])) {
print "<h1>测试结果</h1>";
eval($code);
}
?>
</body>
<script type="text/javascript">
if ( document.addEventListener ) {
document.addEventListener( "DOMContentLoaded", domready, false );
} else if ( document.attachEvent ) {
document.attachEvent("onreadystatechange", function(){
if ( document.readyState === "complete" ) {
domready();
}
});
}
function domready() {
document.getElementById('pre').onclick = function() {
var ele = document.getElementById('code'), value = ele.value, prefix = //n.+$/.test(value) ? "/n" : "";
ele.value += prefix + 'echo "<pre>";/necho "</pre>";';
};
document.getElementById('printr').onclick = function() {
var ele = document.getElementById('code'), value = ele.value, prefix = //n.+$/.test(value) ? "/n" : "";
ele.value += prefix + 'echo "<pre>";/nprint_r();/necho "</pre>";';
};
document.getElementById('vardump').onclick = function() {
var ele = document.getElementById('code'), value = ele.value, prefix = //n.+$/.test(value) ? "/n" : "";
ele.value += prefix + 'echo "<pre>";/nvar_dump();/necho "</pre>";';
};
document.getElementById('time').onclick = function() {
var ele = document.getElementById('code'), value = ele.value, prefix = //n.+$/.test(value) ? "/n" : "";
ele.value += prefix + '$start = microtime(true);/n$end = microtime(true);/nprintf("%.8f<br />", $end - $start);';
};
document.getElementById('usercpu').onclick = function() {
var ele = document.getElementById('code'), value = ele.value, prefix = //n.+$/.test(value) ? "/n" : "";
ele.value += prefix + '$start = user_cputime();/n$end = user_cputime();/nprintf("%.8f<br />", $end - $start);';
};
document.getElementById('systemcpu').onclick = function() {
var ele = document.getElementById('code'), value = ele.value, prefix = //n.+$/.test(value) ? "/n" : "";
ele.value += prefix + '$start = system_cputime();/n$end = system_cputime();/nprintf("%.8f<br />", $end - $start);';
};
document.getElementById('cpu').onclick = function() {
var ele = document.getElementById('code'), value = ele.value, prefix = //n.+$/.test(value) ? "/n" : "";
ele.value += prefix + '$start = cputime_start();/n$end = cputime_end();/n';
};
};
</script>
 
