---
layout: post
title: Struts2中ActionMapping对象的构建过程
date: 2010-01-24 19:55:00
categories: [struts, action, redirect, url]
tags: []
---
1. ActionMapping的处理过程
1.1. org.apache.struts2.dispatcher.mapper.DefaultActionMapper.getMapping()方法:
1.1.1. 创建新的ActionMapping对象
1.1.2. 获取uri(在处理ActionMapping之前, struts对request对象进行了一次封装, 那个时候已经对uri进行了处理)
1.1.3. 处理分号, 如果uri中有分号, 会将分号及其后面的部分过滤
1.1.4. 删除后缀: 这里删除的是配置的url规则中的后缀, 比如*.action
1.1.5. namespace和name处理: DefaultActionMapper.parseNameAndNamespace()方法:
1.1.5.1. 判断最后一个/的位置, 
1.1.5.2. 处理namespace和name
1.1.5.2.1. 如果uri中没有/, 说明是访问根路径, 返回的namespace是””, action名字就是uri自身
1.1.5.2.2. 如果uri中只有一个/, 则namespace是”/”, action名字则会认为是uri的剩余部分
1.1.5.2.3. 如果配置了struts.mapper.alwaysSelectFullNamespace常量, 就始终认为最后一个/之前的部分是namespace, 剩下的部分是action名称
1.1.5.2.4. 如果不是上面的情况, 就会读取所有的package的namespace配置, 寻求与当前的uri匹配程度最大(相同的内容最长)的package, 并以其namespace作为当前的namespace, 剩余部分作为action的name
1.1.5.3. 如果配置了struts.enable.SlashesInActionNames常量, 在这里处理Action的name中的斜杠/
1.1.5.4. 为ActionMapping对象设置namespace和name属性
1.1.6. 处理特殊参数: DefaultActionMapper.handleSpecialParameters()方法:
1.1.6.1. 在struts中有4种特殊的参数前缀, 分别是: method:, action:, redirect:, redirect-action:. 每个前缀会对应一个在DefaultActionMapper.prefixTrie这个容器中的一个处理接口, 其中, 四种前缀的含义分别为:
1.1.6.1.1. method: 将调用同Action中的指定方法处理
1.1.6.1.2. action: 如果允许动态方法调用, 并且action:后面的内容中包含!, 那么会按照动态方法调用方式设置action和method, 否则, 直接将action:后面的内容设置为action
1.1.6.1.3. redirect: 直接跳转到指定的url
1.1.6.1.4. redirect-action: Action跳转
1.1.6.2. 创建一个空HashSet: uniqueParameters
1.1.6.3. 获取到原生的request对象的所有参数的Map: parameterMap
1.1.6.4. 迭代parameterMap, 在保证每个名字的参数只处理一次的前提下, 将所有需要特殊处理的参数进行处理, 并添加到uniqueParameters这个HashSet中
1.1.7. 处理Action的名字: 这里主要是处理动态方法调用的情况
