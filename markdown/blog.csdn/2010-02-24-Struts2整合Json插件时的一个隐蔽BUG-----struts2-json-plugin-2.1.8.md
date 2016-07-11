结论:
         在使用了Json插件的所有Struts2的Action中,
             1. 避免使用get开头的action方法(用来转发的方法),
             2. 严格的为json类型的result配置includeProperties, excludeProperties等参数.
 

```java
/**
 * @modifier: selfimpr
 * @blog: http://blog.csdn.net/lgg201
 * @email: goosman.lei@gmail.com
 */
private void bean(Object object) throws JSONException {
	this.add("{");

	BeanInfo info;

	try {
		Class clazz = object.getClass();

		info = ((object == this.root) &amp;&amp; this.ignoreHierarchy) ? Introspector.getBeanInfo(clazz, clazz
				.getSuperclass()) : Introspector.getBeanInfo(clazz);

		//这里得到的是指定的根对象中所有的get开头的方法对应的描述对象
		PropertyDescriptor[] props = info.getPropertyDescriptors();

		boolean hasData = false;
		for (int i = 0; i &lt; props.length; ++i) {
			PropertyDescriptor prop = props[i];
			//getter对应的属性名称
			String name = prop.getName();
			//getter方法自己
			Method accessor = prop.getReadMethod();
			Method baseAccessor = null;
			if (clazz.getName().indexOf("$$EnhancerByCGLIB$$") &gt; -1) {
				try {
					baseAccessor = Class.forName(
							clazz.getName().substring(0, clazz.getName().indexOf("$$"))).getMethod(
							accessor.getName(), accessor.getParameterTypes());
				} catch (Exception ex) {
					LOG.debug(ex.getMessage(), ex);
				}
			} else
				baseAccessor = accessor;

			if (baseAccessor != null) {

				JSON json = baseAccessor.getAnnotation(JSON.class);
				if (json != null) {
					if (!json.serialize())
						continue;
					else if (json.name().length() &gt; 0)
						name = json.name();
				}

				// ignore "class" and others
				if (this.shouldExcludeProperty(clazz, prop)) {
					continue;
				}
				String expr = null;
				//这里会检查通过给result配置includeProperties, excludeProperties等参数进行的属性过滤
				//所以, 如果有严格的过滤配置, 也不存在问题.
				if (this.buildExpr) {
					expr = this.expandExpr(name);
					if (this.shouldExcludeProperty(expr)) {
						continue;
					}
					expr = this.setExprStack(expr);
				}

				//如果上面没有过滤, 那么这个getter会被调用.
				//问题就出在这个地方.
				//如果你的Action中有以get开头的响应方法, 这里也会被当作是属性的getter拿进来.
				//如果没有在includeProperties, excludeProperties等参数中过滤掉, 会被下面这句去执行.
				//这个问题一般情况下是不会有表象的, 仅仅是"悄悄的"多执行了一些数据库操作
				//但是, 特殊情况如下: 
				//1. 假设请求的Action为: user!findAll.action, 用来响应获取所有的用户
				//2. 假设UserAction中还有一个响应方法, getAllUserByGroup, 这个用来响应获取某个组的用户
				//3. 假设返回json, 而没有过滤掉getAllUserByGroup对应的名称allUserByGroup
				//4. 假设UserAction中有一个属性List users, 并且findAll和getAllUserByGroup都用这个属性存储
				//结果是很严重的, 当你请求findAll这个Action的时候, findAll中的代码首先加载users, 得到
				//了所有的用户.     返回result 交给JSONWritter解析, 它会把getAllUserByGroup作为
				//一个属性的getter方法来解析并调用, 这样, getAllUserByGroup中的代码就会再次运行, 修改users
				//的值.
				//这个问题一旦出现, 非常不易察觉......
				
				//如果没有使用只有getter和setter方法而没有属性的代码习惯, 那么请修改struts2-json插件这个位置, 
				//加入下面代码
				//if(object.getDeclaredField(name)  == null) continue;
				Object value = accessor.invoke(object, new Object[0]);
				boolean propertyPrinted = this.add(name, value, accessor, hasData);
				hasData = hasData || propertyPrinted;
				if (this.buildExpr) {
					this.setExprStack(expr);
				}
			}
		}

		// special-case handling for an Enumeration - include the name() as
		// a property */
		if (object instanceof Enum) {
			Object value = ((Enum) object).name();
			this.add("_name", value, object.getClass().getMethod("name"), hasData);
		}
	} catch (Exception e) {
		throw new JSONException(e);
	}

	this.add("}");
}
```

