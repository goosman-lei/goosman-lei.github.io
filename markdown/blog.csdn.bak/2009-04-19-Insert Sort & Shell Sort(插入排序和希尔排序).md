---
layout: post
title: Insert Sort & Shell Sort(插入排序和希尔排序)
date: 2009-04-19 13:11:00
categories: [insert, shell, random, blog, class, 测试]
tags: []
---
 算法分析:
插入排序的一个扩展是希尔排序,那么运用面向对象的思想,希尔排序就是插入排序的一个泛化.反过来看,插入排序就是希尔排序的一个特化,也就是说插入排序是一种特殊的希尔排序(间距alternation为1).
 
插入排序的原理:
假设一个数组array的前n项是有序的(从小到大),那么,它的第n+1项只需要向前遍历,当他遇到一个数据array[m]比他小的时候,那么,将array[n+1]插入到array[m]和array[m+1]之间,就可以保证array的前n+1项是有序的.
 
希尔排序的原理:
希尔排序的基础仍然是插入排序,但是,希尔排序增加了alternation的概念.
举例说明:
long[] array = {3, 8, 2, 4, 9, 6, 5, 2, 7, 0}; 在希尔排序中,第一次,让间隔为4, 我们就首先对{array[0], array[4], array[8]}, {array[1], array[5], array[9]}, {array[2], array[6]}, {array[3], array[7]}这几组数据排序,这样排序之后,array={3, 0, 2, 4, 7, 6, 5, 2, 9, 8};可以看出, 我们复制了更少的次数, 但是,每个数据距离它的最终位置却要近了很多(相比插入排序的alternation=1). 然后做一次alternation=1的希尔排序(也就是插入排序), 就可以得到最终结果.
 
测试结果:
插入排序:一万条数据230毫秒左右.三万条数据2200毫秒左右. 使用范围: 一万条数据以内.
希尔排序:500万条数据6000毫秒左右.100万条数据1000毫秒左右. 50万数据500毫秒左右.  由此可以看出, 希尔排序是比较稳定的, 在数据量比较大的时候,大概1000条数据1毫秒. 推荐使用范围: 50万条数据以内.
 
以下粘贴插入排序和希尔排序的代码:
插入排序:

```java
package selfimpr.datastruct.simplesort.insertsort;

/**
 * 插入排序算法.希尔排序初步.
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 * @data Apr 19, 2009-1:13:57 PM
 */
public class InsertSort {
	
	public static void sort(long[] arr) {
		sort(arr, 1);
	}
	/**
	 * 插入排序的扩展, 本方法可以提供给希尔排序直接使用.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail goosman.lei@gmail.com
	 * @blog http://blog.csdn.net/lgg201
	 * @data Apr 19, 2009-1:13:30 PM
	 * @param arr
	 * @param alternation 数据的间隔距离.
	 */
	public static void sort(long[] arr, int alternation) {
		
		for(int i=alternation; i&lt;arr.length; i++) {
			int start = i%alternation;
			int j = i-alternation;
			long temp = arr[i];
			while(j&gt;=start) {
				if(arr[j] &gt; temp) {
					arr[j+alternation] = arr[j];
				} else {
					break;
				}
				j-=alternation;
			}
			arr[j+alternation] = temp;
		}
	}
	
	/**
	 * 去除数组中重复的数据.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail goosman.lei@gmail.com
	 * @blog http://blog.csdn.net/lgg201
	 * @data Apr 19, 2009-1:12:08 PM
	 * @param arr
	 */
	public static void noDup(long[] arr) {
		sort(arr);
		for(int i=0; i&lt;arr.length; i++) {
			int j=i;
			if(j &gt; arr.length || arr[j] == -1) continue;
			while(arr[j] == arr[j+1])j++;
			remove(arr, i, j);
		}
	}

	/**
	 * 删除数组中从start到end的数据.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail goosman.lei@gmail.com
	 * @blog http://blog.csdn.net/lgg201
	 * @data Apr 19, 2009-1:12:46 PM
	 * @param arr
	 * @param start
	 * @param end
	 */
	private static void remove(long[] arr, int start, int end) {
		if(start == end) return;
		int count = end - start;
		for(; end&lt;arr.length; ) {
			arr[start++] = arr[end++];
		}
		for(; count &gt; 0; count--) {
			arr[--end] = -1;
		}
	}
}

```

插入排序的测试代码:

```java
package selfimpr.datastruct.simplesort.insertsort.test;

import java.util.Random;

import selfimpr.datastruct.simplesort.insertsort.InsertSort;

/**
 * 测试结果:1万条数据250毫秒左右.
 * 适应数据量: 1万条以内.
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 * @data Apr 19, 2009-1:16:44 PM
 */
public class Test {
	
	private static Random random = new Random();

	/**
	 * @param args
	 */
	public static void main(String[] args) {

		long[] array = new long[10000];
		for(int i=0; i&lt;array.length; i++) {
			array[i] = random.nextInt(10000);
		}
		long start = System.currentTimeMillis();
		InsertSort.sort(array);
		System.err.println(System.currentTimeMillis() - start);
	}
	public static void display(long[] array) {
		for(int i=0; i&lt;array.length; i++) {
			System.err.print(array[i] + " ");
			if(i%5 == 0) System.err.println();
		}
		System.err.println();
	}
}

```

希尔排序:

```java
package selfimpr.datastruct.highsort;


/**
 * 
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 * @data Apr 19, 2009-1:36:47 PM
 */
public class ShellSort {
	//排序方法
	public static void sort(long[] array) {
		int length = array.length; //获取数组长度
		int alternation = getAlternation(length); //获取间隔长度
		//alternation=getNexAlternation(alternation)是获取下一次的间隔长度
		for (; alternation &gt;= 1; alternation = getNextAlternation(alternation)) {
			sort(array, alternation);
		}
	}
	public static void sort(long[] arr, int alternation) {
		for(int i=alternation; i&lt;arr.length; i++) {
			int start = i%alternation;
			int j = i-alternation;
			long temp = arr[i];
			while(j&gt;=start) {
				if(arr[j] &gt; temp) {
					arr[j+alternation] = arr[j];
				} else {
					break;
				}
				j-=alternation;
			}
			arr[j+alternation] = temp;
		}
	}
	

	public static void display(long[] array) {
		for(int i=0; i&lt;array.length; i++) {
			System.err.print(array[i] + " ");
			if(i%5 == 0) System.err.println();
		}
		System.err.println();
	}

	private static int getAlternation(int length) {
		int alternation = 1;
		while (alternation &lt; length) {
			alternation = alternation * 3 + 1;
		}
		return getNextAlternation(alternation);
	}

	private static int getNextAlternation(int alternation) {
		return (alternation - 1) / 3;
	}
}
```

希尔排序的测试代码:

```java
package selfimpr.datastruct.highsort.test;

import java.util.Random;

import selfimpr.datastruct.highsort.ShellSort;
import junit.framework.TestCase;

public class ShellSortTest extends TestCase {
	Random random = new Random();
	public void testShellSort() {
		long[] array = new long[5000000];
		for(int i=0; i&lt;array.length; i++) {
			array[i] = random.nextInt(5000000);
		}
		long start = System.currentTimeMillis();
		ShellSort.sort(array);
		long end = System.currentTimeMillis();
		System.err.println(end - start);
	}
}

```

