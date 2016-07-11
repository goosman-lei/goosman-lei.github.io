**Bug报告地址**:[http://jira.opensymphony.com/browse/SIM-127](http://jira.opensymphony.com/browse/SIM-127)
**Bug描述**: 在和sitemesh和struts整合使用时, 由于request.getRequestURI()得到的是请求的路径(浏览器地址栏输入的路径), 而通过request.getPathInfo()得到的确实配置的实际页面的路径, 因此, 根据sitemesh的com.opensymphony.module.sitemesh.mapper.ConfigDecoratorMapper.getDecorator(HttpServletRequest, Page)这个方法的处理不能得到有效的装饰器配置.
**涉及源代码**:

```java
    public Decorator getDecorator(HttpServletRequest request, Page page) {
        String thisPath = request.getServletPath();

        // getServletPath() returns null unless the mapping corresponds to a servlet
        if (thisPath == null) {
            String requestURI = request.getRequestURI();
            if (request.getPathInfo() != null) {
                // strip the pathInfo from the requestURI
                thisPath = requestURI.substring(0, requestURI.indexOf(request.getPathInfo()));
            }
            else {
                thisPath = requestURI;
            }
        }

        String name = null;
        try {
            name = configLoader.getMappedName(thisPath);
        }
        catch (ServletException e) {
            e.printStackTrace();
        }

        Decorator result = getNamedDecorator(request, name);
        return result == null ? super.getDecorator(request, page) : result;
    }
```

**处理方案**(此种解决方案经过测试在使用一级目录配置装饰器的时候工作良好.):

```java
	public Decorator getDecorator(HttpServletRequest request, Page page) {
		String thisPath = request.getServletPath();

		// getServletPath() returns null unless the mapping corresponds to a
		// servlet
		if (thisPath == null) {
			String requestURI = request.getRequestURI();
			if (request.getPathInfo() != null) {
				// strip the pathInfo from the requestURI
				thisPath = requestURI.substring(0, requestURI.indexOf(request
						.getPathInfo()));
			} else {
				thisPath = requestURI;
			}
		} else {
			thisPath = request.getRequestURI();
		}

		String name = null;
		try {
			name = configLoader.getMappedName(thisPath);
		} catch (ServletException e) {
			e.printStackTrace();
		}

		Decorator result = getNamedDecorator(request, name);
		return result == null ? super.getDecorator(request, page) : result;
	}
```

