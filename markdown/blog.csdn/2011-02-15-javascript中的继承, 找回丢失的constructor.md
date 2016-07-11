autho: selfimprblog: [http://blog.csdn.net/lgg201](http://blog.csdn.net/lgg201)mail: lgg860911@yahoo.com.cnjavascript的继承方案: 1. 子类构造器中以自身对象作为宿主对象对父类进行调用.
```javascript
function User(username, password) {
    this.username = username;
    this.password = password;
}
function Admin(username, password, auth) {
    User.apply(this, arguments);
    this.auth = auth;
}
//当然, 这里面的调用是可以更换为call, 或者直接传递参数的方式, 比如:
function Admin(username, password, auth) {
    User.call(this, username, password);
    this.auth = auth;
}
//或者
function Admin(username, password, auth) {
    this.__constructor = User;
    this.__constructor(username, password);
    delete this.__constructor;
    this.auth = auth;
}
```
 2. 原型链: 上面的方法对于父类在构造器外通过prototype设置的成员是无法继承的, 而原型链的方式能够解决这个问题, 并且, 从语法角度来看, 这才是真正的继承, 因为instanceof能够检测到子类对象和父类存在关系.  当然, 这种方式是有缺点的, 那就是只能单继承
```javascript
function User(username, password) {
    this.username = username;
    this.password = password;
}
User.prototype.login = function() {
    alert('user ' + this.username + ' login success');
}
function Admin(username, password, auth) {
    User.apply(this, arguments);
    this.auth = auth;
}
//这里创建一个父类对象, 作为子类的prototype
Admin.prototype = new User();
Admin.prototype.login = function() {
    alert('admin [' + this.username + '] login success, your authentication is: ' + this.auth);
}
var admin = new Admin('admin', 'admin', 1986);
//这里我们测试的是重写父类方法
admin.login();
//这里我们测试的是instanceof检测
alert("admin instanceof Admin: " + (admin instanceof Admin));
alert("admin instanceof User: " + (admin instanceof User));
```
 3. 上面的原型链方式其实有一点不足, 那就是当我们创建一个Admin的对象之后, 然后获取它的constructor, 得到的是User构造器, 而理想情况下, 这应该是Admin的构造器. 所以, 我们需要对上面的继承再进行少许的修改: **在子类构造器中保留对象对自己构造器的引用**代码如下:
```javascript
function User(username, password) {
    this.username = username;
    this.password = password;
}
User.prototype.login = function() {
    alert('user ' + this.username + ' login success');
}
function Admin(username, password, auth) {
    User.apply(this, arguments);
    this.auth = auth;
    //这里保留一次自己的构造器引用
    this.constructor = arguments.callee;
}
//这里创建一个父类对象, 作为子类的prototype
Admin.prototype = new User();
Admin.prototype.login = function() {
    alert('admin [' + this.username + '] login success, your authentication is: ' + this.auth);
}
var admin = new Admin('admin', 'admin', 1986);
//这里我们测试的是重写父类方法
admin.login();
//这里我们测试的是instanceof检测
alert("admin instanceof Admin: " + (admin instanceof Admin));
alert("admin instanceof User: " + (admin instanceof User));
//检测对象的构造器是否合适
alert(admin.constructor);
```
 
