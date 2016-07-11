---
layout: post
title: 扩展Javascript的String原型, 添加汉字截取
date: 2010-05-28 00:04:00
categories: [javascript, string, 扩展, function, c]
tags: []
---
需求: 所有非ASCII码算2个字符长度, ASCII码算1个字符长度, 进行字符串的截取
 
/**
 * @param begin 截取开始的索引
 * @param num 截取的长度
 */
String.prototype.chinesesubstr = (function(begin, num) {
 var ascRegexp = /[^/x00-/xFF]/g, i = 0;
 while(i < begin) (i ++ && this.charAt(i).match(ascRegexp) && begin --);
 i = begin;
 var end = begin + num;
 while(i < end) (i ++ && this.charAt(i).match(ascRegexp) && end --);
 return this.substring(begin, end);
});
 
用法:
将上面代码置于调用之前.
 
示例:
'中华人民共和国China万岁'.chinesesubstr(3, 10);
返回: "人民共和国C"
解释: "中华"4个字符, 从3开始, 也就是"华"字的第二个字符, 所以, 返回结果包含9个字符, 因为"华"取到半个字.
