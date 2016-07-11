---
layout: post
title: Javascript中可以接受参数的延时函数回调
date: 2010-02-22 11:20:00
categories: [javascript, callback, function, blog, null]
tags: []
---

```javascript
/**
 * @argument callback: 第一个参数, 函数类型, 可以接受任意多个参数
 * @argument timeout: 第二个参数, 数值类型, 表明延时的时间, 毫秒为单位
 * @argument 可变参: 可以接受可变参, 所有第三个及之后参数作为可变参, 
 * 			按照原顺序传递给回调函数callback使用
 * @author selfimpr
 * @blog http://blog.csdn.net/lgg201
 * @mail goosman.lei@gmail.com 
 */
window._setTimeout = window.setTimeout;
window.setTimeout = function() {
    var callback = arguments[0];
    var timeout = arguments[1];
    var args = Array.prototype.slice.call(arguments, 2);
    window._setTimeout(function() {
        callback.apply(null, args);
    }, timeout);
}
```

