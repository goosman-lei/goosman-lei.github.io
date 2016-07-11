---
layout: post
title: 使用alarm配合信号实现sleep
date: 2011-10-13 01:51:00
categories: [signal, null, solaris]
tags: []
---
author: selfimpr
blog: http://blog.csdn.net/lgg201
mail: goosman.lei@gmail.com

APUE中描述Solaris 9是使用alarm实现的sleep, 其语义如下:
如果在sleep之前有一个未到期的alarm时钟, 则中断时钟
1. 如果剩余时间大于sleep时间: 执行sleep, 在sleep完了之后, 继续执行剩余的时间(前面的alarm被中断时的剩余时间 减去 sleep时间)
2. 如果剩余时间小于sleep时间: 执行sleep, 但是sleep的是剩余时间, 并由sleep返回执行后的剩余时间(也就是alarm被中断时的剩余时间 减去 实际sleep时间)

下面是一个简化的实现, 语义为:
如果sleep时发现已经有一个alarm时钟, 中断alarm时钟, sleep指定时间, 然后, 继续未完成的alarm时钟(两个时间不互相影响)
这个语义比较鸡肋...忽忽...



```cpp
#include <apue.h>
#include <signal.h>

static int ud_sleep(int);
static void sig_alrm(int);
static void ud_wait(int);

void main(void) {
	printf("first test case: \n");
	printf("begin at: %d\n", time(NULL));
	//设置一个时钟
	alarm(6);
	printf("after alarm(6) at: %d\n", time(NULL));
	//模拟等待3秒
	ud_wait(3);
	printf("after sleep(3) at: %d\n", time(NULL));
	//进行一次5秒的睡眠
	ud_sleep(5);
	printf("after ud_sleep(5) at: %d\n", time(NULL));
	//等一会儿, 看看第一个alarm剩余的时间到了会发生什么
	ud_wait(5);
}

/**
 *  睡眠seconds秒
 *	与信号的交互规则: 如果之前有alarm时钟, 则以将seconds时间插入到原来的alarm中方式处理
 */
static int ud_sleep(int seconds) {
	sigset_t newmask, oldmask, susmask;
	int unslept1, unslept2;

	//设置SIGALRM的处理器
	if ( signal(SIGALRM, sig_alrm) == SIG_ERR ) err_sys("signal(SIGALRM) error");

	//阻塞SIGALRM信号
	sigemptyset(&newmask);
	sigaddset(&newmask, SIGALRM);
	if ( sigprocmask(SIG_BLOCK, &newmask, &oldmask) < 0 ) err_sys("sigprocmask(SIG_BLOCK) error");

	//设置alarm时钟
	unslept1 = alarm(0);
	alarm(seconds);

	//以原有的信号掩码等待alarm时钟
	susmask = oldmask;
	sigdelset(&susmask, SIGALRM);
	sigsuspend(&susmask);

	//读取剩余睡眠时间并重置信号处理器和信号掩码
	unslept2 = alarm(0);
	if ( signal(SIGALRM, sig_alrm) == SIG_ERR ) err_sys("signal(SIGALRM) error");
	if ( sigprocmask(SIG_SETMASK, &oldmask, NULL) < 0 ) err_sys("sigprocmask(SIG_SETMASK) error");

	alarm(unslept1);
	return unslept2;
}
static void sig_alrm(int signo) {
	signal(SIGALRM, sig_alrm);
	printf("receive SIGALRM at: %d\n", time(NULL));
}
static void ud_wait(int imax) {
	int i = 0, j;
	while ( i ++ < imax * 10000 ) {
		j = 0;
		while ( j ++ < 30000) {
			;
		}
	}
}

```


