
```cpp
/**
 * auhtor: selfimpr
 * blog: http://blog.csdn.net/lgg201
 * mail: goosman.lei@gmail.com
 * 输出整数类型边界值及其大小(K&R <The C programming language> exer 2-1)
 */

#include <stdio.h>
#include <limits.h>

/* 计算整数类型边界值 */
#define INTMIN(type)	(((type)-1 < (type)0) ? ((type)1 << (8 * sizeof(type) - (type)1)) : ((type)0))
#define INTMAX(type)	(((type)-1 < (type)0) ? ~((type)1 << (8 * sizeof(type) - (type)1)) : (~(type)0))

/* 直接输出头文件宏定义的类型边界值或其字面量值 */
#define PRINT_TYPE_SIZE_HEAD(type, type_modifier, min_macro, max_macro) \
	(printf("%-30s: size = %lu, min_value = %20" type_modifier \
		", max_value = %20" type_modifier "\n", #type, \
		sizeof(type), min_macro, max_macro))

/* 计算输出类型边界值 */
#define PRINT_TYPE_SIZE_CALC(type, type_modifier, min_macro, max_macro) \
	(PRINT_TYPE_SIZE_HEAD(type, type_modifier, INTMIN(type), INTMAX(type)))

/* 输出类型大小的入口宏 */
#define PRINT_TYPE_SIZE PRINT_TYPE_SIZE_HEAD

/**
 * 输出本机char和所有整型的类型长度及最大最小值
 */
int main(void) {
	PRINT_TYPE_SIZE(char, "d", CHAR_MIN, CHAR_MAX);
	PRINT_TYPE_SIZE(short int, "hd", SHRT_MIN, SHRT_MAX);
	PRINT_TYPE_SIZE(int, "d", INT_MIN, INT_MAX);
	PRINT_TYPE_SIZE(long int, "ld", LONG_MIN, LONG_MAX);
	PRINT_TYPE_SIZE(long long int, "lld", LLONG_MIN, LLONG_MAX);
	printf("\n");
	PRINT_TYPE_SIZE(signed char, "d", SCHAR_MIN, SCHAR_MAX);
	printf("\n");
	PRINT_TYPE_SIZE(unsigned char, "u", 0, UCHAR_MAX);
	PRINT_TYPE_SIZE(unsigned short int, "hu", 0, USHRT_MAX);
	PRINT_TYPE_SIZE(unsigned int, "u", 0, UINT_MAX);
	PRINT_TYPE_SIZE(unsigned long int, "lu", 0L, ULONG_MAX);
	PRINT_TYPE_SIZE(unsigned long long int, "llu", 0ULL, ULLONG_MAX);
}

```


