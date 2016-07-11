原文地址:[http://www.thomasfrank.se/sessionvars.html](http://www.thomasfrank.se/sessionvars.html)
译者： selfimpr
博客： http://blog.csdn.net/lgg201
邮箱： goosman.lei@gmail.com
 
插件源代码解析：[http://blog.csdn.net/lgg201/archive/2010/09/23/5902274.aspx](http://blog.csdn.net/lgg201/archive/2010/09/23/5902274.aspx)
 
 
**不使用cookie的session变量**

我不喜欢javascript的cookie实现, 存储空间限制在4 * 20K每域名, 仅仅可以使用字符串类型, 并且获取和设置cookie的语法过于复杂.
最重要的是浏览器发送的每个请求头都会携带cookie, 而很多防火墙对请求报头长度有限制导致你的网页的加载可能被阻止.(我看到过这样的事情, 这很糟糕)
因此我写了这个小脚本, 以便于不使用cookie来实现javascript的session变量, 它允许你存储2MB的数据, 并且远比使用cookie来的简单.

**使用方法:**
当引入了sessvars脚本后, 就有了一个sessvars对象, 它基本上就是一个普通的javascript对象, 可以增加新的属性或者改变已有属性的值, 不同的是sessvars可以在不同的页面之间传递, 比如, 你可以使用:
sessvars.myObj = {name: "Thomas", age: 35}
在另一个引入了sessvars的页面中, 你同样可以通过sessvars.myObj得到这个对象.

**方法: **在存储数据的时候, sessvars.$这个名字是不可以使用的, 因为它包含了一些插件内部实现的有用的方法
sesvars.$.clearMem(): 清除已经存储的所有数据
sessvars.$.usedMem(): 返回存储数据已经使用的内存大小, 单位KB
sessvars.$.usedMemPercent(): 返回存储数据已经使用的内存所占百分比
sessvars.$.debug(): 打印一个调试信息到页面, 包含已使用内存, 已使用内存百分比, 已存储所有数据
sessvars.$.flush(): 使当前已经保存的数据在下一次页面切换时也可以使用(因为直接通过sessvars.xx = xxx赋值的数据其实并不会立刻写入到window.name, 而是需要显示调用这个方法, 当然, 可以通过配置autoflush的方式自动刷入, autoflush默认是启用的)

**属性/标记: **一些标记用来觉得sessvars的行为
sessvars.$.prefs.memlimit: 默认2000(KB), 只sessvars可以使用的最大内存, 这个值还会受浏览器限制, opera 9.25限制在2M左右, IE7.0， firefox1.5/2.0以及safari3.0都更高一些, 是10MB.
sessvars.$.prefs.autoFlush: 默认true, 决定sessvars是否在window.unload事件发生时自动的调用flush将数据写入window.name
sessvars.$.prefs.includeFunctions: 默认false, 决定sessvars是否可以存储方法
sesvars.$.prefs.includeProtos: 默认false, 决定sessvars是否存储对象的prototype中的值

**表象的后面: 怎么样完成的数据存储?**
window.name原来的目的是保存window和frame的名字以便于在脚本中使用名字定位访问, 为了不产生干扰, 在我的脚本中只使用了top.name
window.name的优势在于可以在页面切换的时候保存(哪怕是跨域), 并且它可以保存一个很长的名字, 不足之处在于它仅能存储字符串值, 我使用了JSON格式串来进行序列化/反序列.
最主要的是我在window.unload中做了自动的flush消除了每次值改变后去手动save/flush的工作.

**安全注意事项:**
sessvars有一个跨域选项(crossDomain), 但是它默认是false, 当它是false时你不能使用sessvars访问其他站点的window.name, 自己站点设置的数据别人则可以通过在浏览器地址栏输入javascript:alert(window.name)获取到.
因此, 请不要使用sessvars存储敏感信息比如密码, visa卡号等.
但是sessvars也有比cookie安全的地方: window.name的内容不会在请求头发送.
