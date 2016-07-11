---
layout: post
title: QuickSort(快速排序)
date: 2009-05-14 21:27:00
categories: [blog, random, 算法, class, junit, 测试]
tags: []
---
......呵呵,写了比较久了,一直都没有发上来,这个最终测试的效率是500万数据排序需要1.7秒
 
这里提供的快速排序不是可以直接应用的，如果您要使用，请修改数据类型的比较操作为compare就可以了。
 
首先，来看看归并算法吧。

```java
package selfimpr.datastruct.highsort;

/**
 * 
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 * @data May 14, 2009-9:33:58 PM
 */
public class Amalgamate {
	public static long[] amalgamate(long[] array1, long[] array2) {
		int firstPointer,secondPointer, resultPointer;
		firstPointer = secondPointer = resultPointer = 0;
		long[] result = new long[array1.length + array2.length];
		while(firstPointer &lt; array1.length &amp;&amp; secondPointer &lt; array2.length) {
			if(array1[firstPointer] &lt; array2[secondPointer]) {
				result[resultPointer++] = array1[firstPointer++];
			}
			if(firstPointer &gt;= array1.length) break;
			if(array2[secondPointer] &lt;= array1[firstPointer]) {
				result[resultPointer++] = array2[secondPointer++];
			}
			if(secondPointer &gt;= array2.length) break;
		}
		for(; firstPointer&lt;array1.length; firstPointer ++) {
			result[resultPointer++] = array1[firstPointer];
		}
		for(; secondPointer&lt;array2.length; secondPointer ++) {
			result[resultPointer++] = array2[secondPointer];
		}
		return result;
	}
}

```

归并算法是比较简单的，这里仅仅是将两个数组归并为一个新的数组返回，所以没有加注释。。。
 
下面是快速排序的代码,里面都有注释的.

```java
package selfimpr.datastruct.highsort;

/**
 * 快速排序算法
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 * @data May 14, 2009-9:39:09 PM
 */
public class QuickSort {
	/**
	 * 计算复制的次数
	 */
	public static int copyCount = 0;
	/**
	 * 计算比较的次数
	 */
	public static int compareCount = 0;
	/**
	 * 排序算法
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail goosman.lei@gmail.com
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 14, 2009-9:39:43 PM
	 * @param array 要排序的数组
	 */
	public static void sort(long[] array) {
		sort(array, 0, array.length-1);
	}

	/**
	 * 快速排序的递归算法.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail goosman.lei@gmail.com
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 14, 2009-9:40:10 PM
	 * @param array 要排序的数组
	 * @param left 数组的做指针
	 * @param right 数组的右指针
	 */
	public static void sort(long[] array, int left, int right) {
		//右指针&lt;=左指针时说明当前分治得到的子数组长度为0
		if(right - left &lt;= 0) return ;
		//取当前子数组(也就是array数组中的left到right位置的数据)的最右面的数据为分治点.
		long division = array[right];
		//将数组以division为分治点进行分治,得到分割点的index.
		int partitionIndex = partition(array, division, left, right);
		//对分治后的左子数组进行快速排序
		sort(array, left, partitionIndex - 1);
		//将分治后的右子数组进行快速排序.
		sort(array, partitionIndex + 1, right);
	}
	
	public static void optimizeSort(long[] array, int left, int right) {
		if(right - left &lt;= 0) return ;
		compareCount ++;
		long division = (array[left] + array[right] + array[(left+right)/2])/3;
		int partitionIndex = partition(array, division, left, right);
		sort(array, left, partitionIndex - 1);
		sort(array, partitionIndex + 1, right);
	}
	
	/**
	 * 分治算法.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail goosman.lei@gmail.com
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 14, 2009-9:37:30 PM
	 * @param array 要分治的数组
	 * @param division 分治的中间值
	 * @param left 数组的左指针
	 * @param right 数组的右指针
	 * @return 分治得到的数组界限
	 */
	public static int partition(long[] array, long division, int left, int right) {
		int leftPointer = left-1;
		int rightPointer = right;
		while(true) {
			while(array[++leftPointer] &lt; division) {
				compareCount ++;
			}
			while(rightPointer &gt; left &amp;&amp; array[--rightPointer] &gt; division) {
				compareCount ++;
			}
			if(leftPointer &lt; rightPointer) {
				swap(array, leftPointer, rightPointer);
			} else {
				break;
			}
		}
		swap(array, leftPointer, right);
		return leftPointer;
	}
	
	/**
	 * 交换数组内leftPointer和rightPointer的数据.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail goosman.lei@gmail.com
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 14, 2009-9:38:44 PM
	 * @param array
	 * @param leftPointer
	 * @param rightPointer
	 */
	private static void swap(long[] array, int leftPointer, int rightPointer) {
		long temp = array[leftPointer];
		array[leftPointer] = array[rightPointer];
		array[rightPointer] = temp;
		copyCount += 3;
	}
}

```

 
下面是测试类,这里测试需要引入JUnit包.

```java
package selfimpr.datastruct.highsort.test;

import java.util.Random;

import selfimpr.datastruct.highsort.QuickSort;

import junit.framework.TestCase;

/**
 * 
 * @announce Keep all copyright, if you want to reprint, please announce source.
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 * @data May 14, 2009-9:45:55 PM
 */
public class QuickSortTest extends TestCase {
	Random random = new Random();
	int length = 5000000;
	public void testQuickSort() {
		long[] array = new long[length];
		for(int i=0; i&lt;length; i++) {
			array[i] = random.nextInt(length);
		}
//		display2(array);
		long start = System.currentTimeMillis();
		QuickSort.sort(array);
		System.out.println(System.currentTimeMillis() - start);
//		System.out.println(QuickSort.partition(array, array[array.length - 1], 0, array.length-1));
//		display2(array);
		System.err.println("CompareCount: " + QuickSort.compareCount + "; CopyCount: " + QuickSort.copyCount);
	}
	public void display1(long[] array) {
		for(int i=0; i&lt;array.length; i++) {
			System.err.print(array[i] + " ");
			if(i%5 == 0) System.err.println();
		}
		System.err.println();
	}
	public void display2(long[] array) {
		for(int i=0; i&lt;array.length; i++) {
			System.err.print(array[i] + " ");
		}
		System.err.println();
	}
}

```

 
就这样了
