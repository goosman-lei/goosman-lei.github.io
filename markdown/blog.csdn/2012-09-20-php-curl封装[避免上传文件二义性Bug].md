由于php的curl在curl_setopt($curl, CURLOPT_POSTFIELDS, xxx)时, 当xxx为数组时, 如果值的第一个字符是@, 则认为是文件上传, 当同时需要上传文件, 也需要提交可能首字符为@的其他普通数据时, 存在冲突. 因此, 在api_common.php中的post数据的设置进行了封装




```php
<?php
/**
 * php-curl库封装
 * author: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: lgg860911@yahoo.com.cn
 */

define('API_CURL_UPLOAD_FILE',							'__file');

#支持的请求方法
define('REQUEST_METHOD_GET',							'GET');
define('REQUEST_METHOD_POST',							'POST');
define('REQUEST_METHOD_HEAD',							'HEAD');

#请求行为
define('REQUEST_BEHAVIOR_ALLOW_REDIRECT',				'allow_redirect');
define('REQUEST_BEHAVIOR_MAX_REDIRECT',					'max_redirect');
define('REQUEST_BEHAVIOR_USER_AGENT',					'user_agent');
define('REQUEST_BEHAVIOR_AUTOREFERER',					'autoreferer');
define('REQUEST_BEHAVIOR_UPLOAD',						'upload');
define('REQUEST_BEHAVIOR_CONNECTTIMEOUT',				'connecttimeout');
define('REQUEST_BEHAVIOR_DNS_CACHE_TIMEOUT',			'dns_cache_timeout');
define('REQUEST_BEHAVIOR_TIMEOUT',						'timeout');
define('REQUEST_BEHAVIOR_ENCODING',						'encoding');
define('REQUEST_BEHAVIOR_ERROR_HANDLER',				'error_handler');
define('REQUEST_BEHAVIORS',								'behaviors');
$GLOBALS[REQUEST_BEHAVIORS]	= array(
	REQUEST_BEHAVIOR_ALLOW_REDIRECT				=> TRUE, 
	REQUEST_BEHAVIOR_MAX_REDIRECT				=> 5, 
	REQUEST_BEHAVIOR_USER_AGENT					=> 'curl-lib', 
	REQUEST_BEHAVIOR_AUTOREFERER				=> TRUE, 
	REQUEST_BEHAVIOR_UPLOAD						=> FALSE, 
	REQUEST_BEHAVIOR_CONNECTTIMEOUT				=> 3, 
	REQUEST_BEHAVIOR_DNS_CACHE_TIMEOUT			=> 3600, 
	REQUEST_BEHAVIOR_TIMEOUT					=> 3, 
	REQUEST_BEHAVIOR_ENCODING					=> 'gzip', 
	REQUEST_BEHAVIOR_ERROR_HANDLER				=> '__default_curl_error_handler', 
);

define('MULTIPART_FORM_DATA_HEAD_FMT',				'Content-Type: multipart/form-data; boundary=----------------------------%s');
define('MULTIPART_FORM_DATA_BODY_STRING',			"------------------------------%s\r\nContent-Disposition: form-data; name=\"%s\"\r\n\r\n%s\r\n");
define('MULTIPART_FORM_DATA_BODY_FILE',				"------------------------------%s\r\nContent-Disposition: form-data; name=\"%s\"; filename=\"%s\"\r\nContent-Type: application/octet-stream\r\n\r\n%s\r\n");
define('MULTIPART_FORM_DATA_BODY_END',				"------------------------------%s--\r\n\r\n");

#响应键值
define('RESP_CODE',									'resp_code');
define('RESP_BODY',									'resp_body');
define('RESP_HEADER',								'resp_header');

#HTTP 1xx状态验证
define('HTTP_1XX_RESP',								'/^HTTP\/1.[01] 1\d{2} \w+/');

#默认错误处理的错误消息
define('E_CURL_ERROR_FMT',								'curl "%s" error[%d]: %s');

#默认的curl错误处理
function __default_curl_error_handler($curl, $url, $errno, $errstr) {
	trigger_error(sprintf(E_CURL_ERROR_FMT, $url, $errno, $errstr), E_USER_ERROR);
}
#切换CURL请求方法
function __method_switch($curl, $method) {
	switch ( $method) {
		case REQUEST_METHOD_POST:
			__curl_setopt($curl, CURLOPT_POST, TRUE);
			break;
		case REQUEST_METHOD_HEAD:
			__curl_setopt($curl, CURLOPT_NOBODY, TRUE);
			break;
		case REQUEST_METHOD_GET:
			__curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
			break;
		default:
			break;
	}
}
#设置默认头信息
function __default_header_set($curl) {
	__curl_setopt($curl, CURLOPT_RETURNTRANSFER,			TRUE);
	__curl_setopt($curl, CURLOPT_HEADER,					TRUE);
	__curl_setopt($curl, CURLOPT_FOLLOWLOCATION,			(bool)curl_behavior(REQUEST_BEHAVIOR_ALLOW_REDIRECT));
	__curl_setopt($curl, CURLOPT_MAXREDIRS,					(int)curl_behavior(REQUEST_BEHAVIOR_MAX_REDIRECT));
	__curl_setopt($curl, CURLOPT_USERAGENT,					(string)curl_behavior(REQUEST_BEHAVIOR_USER_AGENT));
	__curl_setopt($curl, CURLOPT_AUTOREFERER,				(bool)curl_behavior(REQUEST_BEHAVIOR_AUTOREFERER));
	__curl_setopt($curl, CURLOPT_UPLOAD,					(bool)curl_behavior(REQUEST_BEHAVIOR_UPLOAD));
	__curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,			(int)curl_behavior(REQUEST_BEHAVIOR_CONNECTTIMEOUT));
	__curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT,			(int)curl_behavior(REQUEST_BEHAVIOR_DNS_CACHE_TIMEOUT));
	__curl_setopt($curl, CURLOPT_TIMEOUT,					(int)curl_behavior(REQUEST_BEHAVIOR_TIMEOUT));
	__curl_setopt($curl, CURLOPT_ENCODING,					(string)curl_behavior(REQUEST_BEHAVIOR_ENCODING));
}
#设置用户自定义头信息
function __custom_header_set($curl, $headers = NULL) {
	if ( empty($headers) ) return ;
	if ( is_string($headers) ) 
		$headers	= explode("\r\n", $headers);
	#类型修复
	foreach ( $headers as &$header ) 
		if ( is_array($header) ) 
			$header	= sprintf('%s: %s', $header[0], $header[1]);
	__curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
}
#设置请求body
function __datas_set($curl, $datas = NULL) {
	if ( empty($datas) ) return ;
	if ( is_array($datas) ) {
		$custom_body		= FALSE;
		$uniqid				= uniqid();
		$custom_body_str	= '';
		foreach ( $datas as $name => $data ) {
			if ( is_array($data) && array_key_exists(API_CURL_UPLOAD_FILE, $data) ) {
				$file	= $data[API_CURL_UPLOAD_FILE];
				if ( file_exists($file) ) {
					$custom_body		= TRUE;
					$custom_body_str	.= sprintf(MULTIPART_FORM_DATA_BODY_FILE, 
										$uniqid, $name, 
										$file, file_get_contents($file));
				}
			} else {
				$custom_body_str		.= sprintf(MULTIPART_FORM_DATA_BODY_STRING, 
										$uniqid, $name, $data);
			}
		}
		if ( $custom_body ) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(sprintf(MULTIPART_FORM_DATA_HEAD_FMT, $uniqid)));
			$datas				= $custom_body_str . sprintf(MULTIPART_FORM_DATA_BODY_END, $uniqid);
		}
	}
	__curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
}
#对curl_setopt的封装
function __curl_setopt($curl, $optname, $optval) {
	curl_setopt($curl, $optname, $optval);
	__curl_error($curl);
}
#curl错误检查处理
function __curl_error($curl) {
	if ( curl_errno($curl) ) {
		$url	= curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
		$errno	= curl_errno($curl);
		$errstr	= curl_error($curl);
		$errh	= curl_behavior(REQUEST_BEHAVIOR_ERROR_HANDLER);
		if ( function_exists($errh) )
			$errh($curl, $url, $errno, $errstr);
	}
}

#api默认行为切换
function curl_behavior($names, $values = NULL) {
	if ( !is_string($names) && !is_array($names) ) return ;
	if ( !is_null($values) ) {
		if ( is_string($names) ) 
			$GLOBALS[REQUEST_BEHAVIORS][$names]	= $values;
		else if ( is_array($names) && !is_array($values) )
			foreach ( $names as $name )
				$GLOBALS[REQUEST_BEHAVIORS][$name]	= $values;
		else if ( is_array($names) && is_array($values) )
			foreach ( $names as $k => $name ) 
				$GLOBALS[REQUEST_BEHAVIORS][$name]	= $values[$k];
	}
	if ( is_string($names) ) {
		$return	= $GLOBALS[REQUEST_BEHAVIORS][$names];
	} else if ( is_array($names) ) {
		$return	= array();
		foreach ( $names as $name ) 
			$return[$name]	= array_key_exists($name, $GLOBALS[REQUEST_BEHAVIORS]) 
							? $GLOBALS[REQUEST_BEHAVIORS][$name]
							: NULL;
	}
	return $return;
}
#请求入口
function curl_request($url, $method, $datas = NULL, $headers = NULL) {
	$curl	= curl_init($url);
	__method_switch($curl, $method);
	__default_header_set($curl);
	__custom_header_set($curl, $headers);
	__datas_set($curl, $datas);
	$response	= curl_exec($curl);
	__curl_error($curl);
	$status_code	= curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$components		= explode("\r\n\r\n", $response);
	$i				= -1;
	while ( ++ $i < count($components) ) 
		if ( !preg_match(HTTP_1XX_RESP, $components[$i]) ) break;
	$headers		= $components[$i];
	$body			= implode("\r\n\r\n", array_slice($components, $i + 1));
	return array(
		RESP_CODE	=> $status_code, 
		RESP_HEADER	=> $headers, 
		RESP_BODY	=> $body, 
	);
}
#GET请求
function curl_get($url, $headers = NULL) {
	return curl_request($url, REQUEST_METHOD_GET, NULL, $headers);
}
#POST请求
function curl_post($url, $datas = NULL, $headers = NULL) {
	return curl_request($url, REQUEST_METHOD_POST, $datas, $headers);
}
#HEAD请求
function curl_head($url, $headers = NULL) {
	return curl_request($url, REQUEST_METHOD_HEAD, NULL, $headers);
}
#构造上传文件字段
function curl_post_file($file) {
	return array(
		API_CURL_UPLOAD_FILE	=> $file, 
	);
}
#读取响应码
function curl_resp_code($resp) {
	return $resp[RESP_CODE];
}
#读取响应头
function curl_resp_header($resp) {
	return $resp[RESP_HEADER];
}
#读取响应体
function curl_resp_body($resp) {
	return $resp[RESP_BODY];
}

```

