---
layout: post
title: 快速排序(QuickSort)C语言版
date: 2009-12-16 14:08:00
categories: [语言, c, numbers, 算法, email, blog]
tags: []
---
快速排序的核心在于分治.
 
分治算法:
1. 认定只有一个元素或没有元素的数组是有序的.
2. 将数组按照一个分界值分为左右两部分. 左面所有元素值比分界值小, 右面所有元素值比分界值大或等于.
3. 将左右两部分分别再分治, 直到要分支的部分只有一个元素或没有元素, 那么整个数组就是有序的了.
 
作者: selfimpr
博客:[http://blog.csdn.net/lgg201](http://blog.csdn.net/lgg201)
邮箱:[goosman.lei@gmail.com](mailto:goosman.lei@gmail.com)
 
 

```cpp
#include &lt;stdio.h&gt;
#include &lt;time.h&gt;
#include &lt;stdlib.h&gt;
#define LENGTH 500000

/*
 * 打印一个指定的数组
 */
void printArray(int array[], int length);

/*
 * 将指定数组的指定部分分治, 返回分治点.
 * @argument division: 分界点(下标)
 * @argument left: 分治部分的左下标
 * @argument right: 分治部分的右下标
 */
int part(int array[], int division, int left, int right);

/*
 * 排序指定数组的指定位置元素.
 */
void sort(int array[], int left, int right);

/*
 * @author: selfimpr
 * @blog: http://blog.csdn.net/lgg201
 * @email: goosman.lei@gmail.com
 */
int main() {
   int array[LENGTH];
   int i;
   srand( (unsigned) time(NULL) );

   for(i = 0; i &lt; LENGTH; i ++) {
      array[i] = rand() % LENGTH;
   }

   /*
    * printArray(array, LENGTH);
    * printf("/n/n");
    */

   clock_t start = clock();
   printf("Start at %d./n", (unsigned)start); 

   sort(array, 0, LENGTH - 1);

   clock_t end = clock();
   printf("End at %d./n", (unsigned)end);

   /* 
    * printArray(array, LENGTH);
    */

   printf("Sorted %d numbers in %d millisecond.", LENGTH, (unsigned)end - (unsigned)start);

   return 0;
}

void printArray(int array[], int length) {
   int i;
   for(i = 0; i &lt; length; i ++) {
      printf("%d ", array[i]);
      if((i + 1) % 10 == 0) {
	 printf("/n");
      }
   }
}

/*
 * 分治算法:
 *    以division下标的元素值为分界点, 将数组分成左右两个部分.
 *    返回分界点的下标.
 * 过程:
 *    1. 保留分界点元素的值.
 *    2. 移动左指针, 直到一个值不小于分界值.
 *    3. 判断是否已经划分了所有元素, 如果是, 跳出.
 *    4. 将左指针目前的值复制到division位置, 重置division为left位置.
 *    5. 和2-4步骤一样, 处理右指针.
 *    6. 将分界值放入最终确定的分界下标.
 *    7. 返回分界下标.
 */
int part(int array[], int division, int left, int right) {
   int tmp = array[division];
   while(1) {
      while(array[left] &lt; tmp) {
	 left ++;
      }
      if(left &gt;= right) break;
      array[division] = array[left];
      division = left;
      while(array[right] &gt;= tmp) {
	 right --;
      }
      if(left &gt;= right) break;
      array[division] = array[right];
      division = right;
   }
   array[division] = tmp;
   return division;
}

/*
 * 过程:
 *    1. 将整个left到right位置分治(分治之后左子数组, 分界值, 右子数组是有序的.)
 *    2. 将分治得到的左子数组和右子数组分别分治.
 *    3. 如果传入的left &gt;= right, 说明要排序的部分元素少于或等于1个, 
 *	 那么必然是有序的,所以直接返回.
 */
void sort(int array[], int left, int right) {
   if(left &gt;= right) {
      return ;
   }
   int division = (left + right) / 2;
   /* 
    * printf("division: %d, ", array[division]);
    */
   int partition = part(array, division, left, right);
   /*
    *  printf("partition: %d, left: %d, right: %d, ", partition, left, right);
    *  printArray(array, LENGTH);
    */
   sort(array, left, partition - 1);
   sort(array, partition + 1, right);
}
```

