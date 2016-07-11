---
layout: post
title: Double Bubble Sort(双向的冒泡排序)
date: 2009-04-19 12:34:00
categories: [算法, random, 测试, class, blog]
tags: []
---
算法解析:
1. flag变量记录目前排序算法的移动方向.
2. 算法在数组左右各增加一个指针,记录冒泡的始末位置.
3. 根据flag使指针进行移动,冒泡.
 
测试环境: Intel Pentium 4 cpu 3.01GHZ(不知被谁超频了). 内存512*2.
测试结果: 1万条随机数据550毫秒左右.
 
算法类:

```java
package selfimpr.datastruct.simplesort.doublebubblesort;

/**
 * 
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail lgg860911@yahoo.com.cn
 * @blog http://blog.csdn.net/lgg201
 * @data Apr 19, 2009-12:59:44 PM
 */
public class Sort {
	
	public static void doubleBubble(long[] arr) {
		//记录算法的移动方向.true为向右移动.
		boolean flag = true;
		//算法的左指针.
		int left = 0;
		//算法的右指针.
		int right = arr.length -1;
		//算法的当前指针位置.
		int i;
		//左右指针重叠后结束算法
		while(left &lt;= right) {
			if(flag) {
				//算法向右移动进行冒泡排序
				i = left;
				for(; i&lt;right; i++) {
					if(arr[i] &gt; arr[i+1]) {
						swap(arr, i, i+1);
					}
				}
				flag = false;
				right --;
			} else {
				//算法向左移动进行冒泡排序
				i = right;
				for(; i&gt;left; i--) {
					if(arr[i] &lt; arr[i-1]) {
						swap(arr, i, i-1);
					}
				}
				flag = true;
				left ++;
			}
		}
	}

	private static void swap(long[] arr, int i, int j) {
		long temp = arr[i];
		arr[i] = arr[j];
		arr[j] = temp;
	}
}
```

 
测试类:

```java
package selfimpr.datastruct.simplesort.doublebubblesort.test;

import java.util.Random;

import selfimpr.datastruct.simplesort.doublebubblesort.Sort;

/**
 * 
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail lgg860911@yahoo.com.cn
 * @blog http://blog.csdn.net/lgg201
 * @data Apr 19, 2009-1:00:06 PM
 */
public class Test {
	
	private static Random random = new Random();

	public static void main(String[] args) {
		long[] array = new long[10000];
		for(int i=0; i&lt;array.length; i++) {
			array[i] = random.nextInt(10000);
		}
		long start = System.currentTimeMillis();
		Sort.doubleBubble(array);
		System.err.println(System.currentTimeMillis() - start);
	}

	public static void display(long[] arr) {
		for(long l : arr) {
			System.err.print(l + &quot; &quot;);
		}
		System.out.println();
	}
}

```

