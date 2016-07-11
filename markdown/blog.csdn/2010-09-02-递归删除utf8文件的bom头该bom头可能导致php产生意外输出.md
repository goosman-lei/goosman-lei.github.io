不废话了, 是php代码, 如果要处理大文件, 可以改改, 这里是直接读入到数组中
 

```php
//author: selfimpr
//blog: http://blog.csdn.net/lgg201
//mail: goosman.lei@gmail.com
//EF BB BF这三个字节称为bom头
function hasbom(&amp;$content) {
	$firstline = $content[0];
	return ord(substr($firstline, 0, 1)) === 0xEF
		and ord(substr($firstline, 1, 1)) === 0xBB 
		and ord(substr($firstline, 2, 1)) === 0xBF;
}
function unsetbom(&amp;$content) {
	hasbom($content) and ($content[0] = substr($content[0], 3)); 
}
function write($filename, &amp;$content) {
	$file = fopen($filename, 'w');
	fwrite($file, implode($content, ''));
	fclose($file);
}
function filenames($path) {
	$directory = opendir($path);
	while (false != ($filename = readdir($directory))) strpos($filename, '.') !== 0 and $filenames[] = $filename;
	closedir($directory);
	return $filenames;
}
function process($path) {
	$parent = opendir($path);
	while (false != ($filename = readdir($parent))) {
		echo $filename.&quot;/n&quot;;
		if(strpos($filename, '.') === 0) continue;
		if(is_dir($path.'/'.$filename)) {
			process($path.'/'.$filename);
		} else {
			$content = file($path.'/'.$filename);
			unsetbom($content);
			write($path.'/'.$filename, $content);
		}
	}
	closedir($parent);
}
process('/home/selfimpr/t');
```

 
