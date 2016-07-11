---
layout: post
title: BinarySearchTree Implements(Java 实现的二叉搜索树算法)
date: 2009-05-05 02:49:00
categories: [java, null, random, 算法, class, blog]
tags: []
---
二叉搜索树的删除算法的确是比较复杂的...画了一个删除算法的活动图,希望可以帮助到各位博友.
 
以下是关于二叉搜索树的代码,比较难懂的都在代码中有注释:
节点类:Node
 
```java
package selfimpr.datastruct.binarysearchtree;

/**
 * 
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 */
public class Node {
	public Node left;
	public Node right;
	public long value;
	public Node() {
		
	}
	public Node(long value) {
		this.value = value;
	}
}

```

 
二叉搜索树类:BinarySearchTree
 
```java
package selfimpr.datastruct.binarysearchtree;

/**
 * 
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 */
public class BinarySearchTree {
	public Node root;
	public BinarySearchTree() {
		
	}
	public BinarySearchTree(long value) {
		root = new Node(value);
	}
	
	//向树中插入一个值为value的节点
	public void insert(long value) {
		if(root == null) {
			root = new Node(value);
			return ;
		}
		Node current = root;
		Node parent;
		while(true) {
			parent = current;
			if(value &lt; current.value) {
				current = current.left;
				if(current == null) {
					parent.left = new Node(value);
					return ;
				}
			} else {
				current = current.right;
				if(current == null) {
					parent.right = new Node(value);
					return ;
				}
			}
		}
	}
	
	//查找树中是否存在值为value的节点
	public boolean search(long value) {
		Node current = root;
		while(current != null) {
			if(value &lt; current.value) {
				current = current.left;
				continue;
			}
			if(value &gt; current.value) {
				current = current.right;
				continue;
			}
			return true;
		}
		return false;
	}
	
	public boolean delete(long value) {
		//根节点value和目标value相等时,直接删除.
		if(value == root.value) {
			root = null;
			return true;
		}
		Node current = root; //当前节点
		Node parent = null; //当前节点的父节点
		boolean isLeft = false; //标记current是parent的左子树还是右子树
		//查找value在树中对应的节点
		while(current != null &amp;&amp; value != current.value) {
			//改变parent引用
			parent = current;
			if(value &lt; current.value) {
				//value小于current.value时,current改变为current的left节点
				current = current.left;
				isLeft = true;
				continue ;
			}
			if(value &gt; current.value) {
				//value大于current.value时,current改变为current的right节点
				current = current.right;
				isLeft = false;
				continue ;
			}
		}
		//如果current==null说明树中没有值为value的节点.
		if(current == null) {
			return false;
		}
		
		//此处以上的代码经检验是正确的.此时查找到的current的value为目标value
		//也就是说,此时的current是要删除的节点
		
		if(current.left == null &amp;&amp; current.right == null) {
			//current没有子节点, 直接根据isLeft将parent指向current的引用切断
			if(isLeft) {
				parent.left = null;
			} else {
				parent.right = null;
			}
		} else if(current.left != null &amp;&amp; current.right == null) {
			//current只有左子节点, 根据isLeft切断parent指向current的引用,
			//让它指向current的左子节点
			if(isLeft) {
				parent.left = current.left;
			} else {
				parent.right = current.left;
			}
		} else if(current.left == null &amp;&amp; current.right != null) {
			//current只有右子节点, 根据isLeft切断parent指向current的引用,
			//让它指向current的右子节点
			if(isLeft) {
				parent.left = current.right;
			} else {
				parent.right = current.right;
			}
		} else {
			//以下代码是查找current的后继节点(current右子树中的最小值节点)
			//重构current的右子树的过程.
			Node subsequence; //后继节点
			if(current.right.left == null) {
				//如果current.right.left为空,说明后继节点就是current.right
				subsequence = current.right;
			} else {
				//设置后继节点和后继节点父节点的初值.
				subsequence = current.right; //后继节点
				Node previouSubsequence = current; //后继节点的父节点
				while(subsequence.left != null) {
					//由于后继节点是最小值节点.而二叉搜索树当前节点的左子节点值
					//总是小于当前节点,所以子树中的最后一个左子树的根就是后继节点
					previouSubsequence = subsequence;
					subsequence = subsequence.left;
				}
				//重构右current的右子树.
				//后继节点的前一个节点断开与后继节点的引用,
				//指向后继节点的右子节点.
				//这里因为后继节点是最后一个左子树的根节点,所以一定不存在左子节点.
				//因此下面的代码能够保证树中数据的完整性.
				previouSubsequence.left = subsequence.right;
				subsequence.right = current.right;
				//至此,通过后继节点完成了对current的右子树的重构
			}
			//由后继节点的左子节点指向current的左子树.
			subsequence.left = current.left;
			//至此,完成了对current子树的重构,下面的代码只是根据isLeft将其
			//和parent节点链接起来,并切断parent和current的引用.
			if(isLeft) {
				parent.left = subsequence;
			} else {
				parent.right = subsequence;
			}
		}
		//删除完成, 返回true;
		return true;
	}
	
	//展示树中的数据.
	public void display() {
		display(root);
		System.out.println();
	}
	
	//遍历树.
	private void display(Node node) {
		if(node == null) return ;
		display(node.left);
		System.out.print(node.value + " ");
		display(node.right);
		
	}
}

```

 
单元测试类:BinarySearchTreeTest

```java
package selfimpr.datastruct.binarysearchtree.test;

import java.util.Random;

import junit.framework.TestCase;
import selfimpr.datastruct.binarysearchtree.BinarySearchTree;

/**
 * 
 * @author selfimpr
 * @mail goosman.lei@gmail.com
 * @blog http://blog.csdn.net/lgg201
 */
public class BinarySearchTreeTest extends TestCase {
	
	Random random = new Random();
	
	public void testTree() {
		BinarySearchTree tree = new BinarySearchTree();
		
		tree.insert(50);
		for(int i=0; i&lt;100; i++) {
			tree.insert(random.nextInt(100));
		}
		tree.display();
		
//		int num = 0;
//		for(int i=0; i&lt;1000; i++) {
//			num = random.nextInt(100);
//			System.err.println("Test number is: " + num + ";result: " + tree.search(num));
//		}
		
		for(int i=0; i&lt;1000; i++) {
			tree.delete(random.nextInt(100));
			tree.display();
		}
	}
}

```

 
后面继续会有快速排序法的实现,队列相关的实现(双端队列, 基于数组的,基于链表的队列), 栈, 链表的相关实现(单链表,双链表, 双端链表, 回环链表等), 最后可能会有堆的实现和红黑树的实现都发到上面来,请各位参考指导.
 
二叉搜索树删除算法活动图:
 
![BinarySearchTreeDeleteAlgorithm](http://p.blog.csdn.net/images/p_blog_csdn_net/lgg201/EntryImages/20090505/BinarySearchTreeDeleteAlgorithm.jpg)
