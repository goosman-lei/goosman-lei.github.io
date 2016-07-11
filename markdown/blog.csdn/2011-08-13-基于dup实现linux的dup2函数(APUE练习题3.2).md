忽忽....习题...拿来做做, 请指点做的哪里不好...请不要指点这个应该或不应该做...这只是习题....

author: selfimpr
blog: http://blog.csdn.net/lgg201
mail: goosman.lei@gmail.com



```cpp
int ud_dup2(const int ofd, const int nfd) {
	//新描述符等于旧描述符,不关闭直接返回
	if(ofd	== nfd) return ofd;

	int pid			= getpid();
	char *pathname	= malloc(sizeof(char) * 128);
	sprintf(pathname, "/proc/%d/fd/%d", getpid(), nfd);
	//如果新描述符已经被打开,关闭它
	if(!access(pathname, F_OK)) close(nfd);

	int tmp;
	int max			= sysconf(_SC_OPEN_MAX);
	int fds[max], i = 0;
	//如果新描述符值大于最大描述符数, 返回错误
	if(max < nfd) return -1;
	do {
		tmp			= dup(ofd);
		//dup出错
		if(tmp < 0) break;
		fds[i ++]	= tmp;
	} while(tmp < nfd);

	//如果拷贝出错,则i不自减,也关闭最后一次复制的描述符, 否则,最后的为新描述符, 不关闭
	if(tmp == nfd) i --;
	//关闭复制的描述符
	while(i-- >= 0) close(fds[i]);

	if(tmp != nfd) return -1;
	return nfd;
}

```

