---
layout: post
title: RedBlackTree(红黑树)--一种自平衡(最优)的二叉搜索树算法
date: 2009-05-07 16:10:00
categories: [算法, null, blog, random, class, 数据结构]
tags: []
---
注: 看这个之前,先看看二叉搜索树吧.
 
红黑树简介:红黑树是一种自平衡二叉查找树，是在计算机科学中用到的一种数据结构，典型的用途是实现关联数组。它是在1972年由Rudolf Bayer发明的，他称之为"对称二叉B树"，它现代的名字是在 Leo J. Guibas 和 Robert Sedgewick 于1978年写的一篇论文中获得的。它是复杂的，但它的操作有着良好的最坏情况运行时间，并且在实践中是高效的: 它可以在O(log n)时间内做查找，插入和删除，这里的n 是树中元素的数目。
 
红黑树规则:
1. 每一个节点不是红色就是黑色的.
2. 根总是黑色的.
3. 如果节点是红色的, 则它的子节点必须是黑色的(反之不一定为真).
4. 从根到叶节点或空子节点的每条路径,必须包含相同数目的黑色节点(即黑色节点长度相等).
 
目前只实现了插入算法, 删除算法用状态码的方式屏蔽.
正在努力实现删除算法..
废话少说,上代码,注释里面都有的.
 
使用这里提供的测试类需要引入JUnit.
 
节点类:Node.java

```java
package selfimpr.datastruct.redblacktree;

/**
 * 
 * @author selfimpr
 * @mail lgg860911@yahoo.com.cn
 * @blog http://blog.csdn.net/lgg201
 */
public class Node {
	public Node left;
	public Node right;
	public long value;
	public boolean isRed = true;
	public boolean isDelete = false;
	public Node() {
		
	}
	public Node(long value) {
		this.value = value;
	}
}

```

 
树的类: RedBlackTree.java

```java
package selfimpr.datastruct.redblacktree;

/**
 * 
 * @author selfimpr
 * @mail lgg860911@yahoo.com.cn
 * @blog http://blog.csdn.net/lgg201
 */
public class RedBlackTree {
	public Node root;
	public RedBlackTree() {
		
	}
	public RedBlackTree(long value) {
		root = new Node(value);
	}
	
	/**
	 * 向树中插入一个值为value的节点.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail lgg860911@yahoo.com.cn
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 7, 2009-1:39:23 PM
	 * @param value 要插入的值.
	 */
	public void insert(long value) {
		//创建要插入的节点.
		Node node = new Node(value);
		if(root == null) {
			//如果根节点为空,直接插入,并将根节点颜色设置为黑色.
			root = node;
			root.isRed = false;
			return ;
		}
		Node ancestor = null; //祖宗节点. 祖父节点的父节点.
		Node grandparent = null; //祖父节点. 父亲节点的父亲节点.
		Node parent = null; //父亲节点.
		Node current = root; //当前节点.
		boolean isLeft = false; //记录查找到的插入位置是parent节点的左子节点还是右子节点.
		while(current != null) {
			//如果发现一个节点是黑色的,并且他的两个子节点都是红色.
			//这里做的是查找节点过程中的颜色变换和树的平衡.
			if(!current.isRed 
					&amp;&amp; current.left != null &amp;&amp; current.left.isRed 
					&amp;&amp; current.right != null &amp;&amp; current.right.isRed) {
				//将左子节点和右子节点的颜色都改变为黑色.
				current.left.isRed = false;
				current.right.isRed = false;
				//如果当前节点的父节点不为null,说名当前节点不是根节点,
				//因此将当前节点设置为红色.
				if(parent != null) {
					current.isRed = true;
				}
				
				//1. grandparent == null 时说明parent是根节点, 
				//   此时, 上面的颜色变换不能引起违反红黑树规则, 
				//   所以没有必要进行相应的颜色变换和旋转.
				//2. parent是黑色的时候, 也不会引起违反红黑树规则, 
				//   所以没有必要进行相应的颜色变换和旋转.
				//3. 发生下面的情况时, 说明上面的颜色变换导致parent
				//   节点和他的连个子节点都成为了红色,因此,需要通过
				//   颜色变换保证遵守红黑树规则.
				if(grandparent != null &amp;&amp; parent.isRed) {
					//改变祖父节点的颜色.
					changeColor(grandparent);
					//如果是内侧子孙节点.首先改变current的颜色为黑色,
					//进行一次旋转, 使得以parent为根的子树不违反
					//红黑树规则4(红色节点不能有红色的子节点)
					//旋转之后,颜色序列成为:
					//grandparent--red, parent--black, current--red,
					//同时,旋转导致以parent为根的子树的黑色长度比其他子树多1.
					//注意: 旋转的方向和isLeft相反.
					//即:true == isLeft时右旋. false == isLeft时左旋.
					//这样的旋转能够保证把current旋转到外侧.
					if(!isOutGrandsonNode(current, parent, grandparent)) {
						changeColor(current);
						//改变后颜色序列成为:
						//grandparent--red, parent--red, current--black
						if(isLeft) {
							roundRight(parent, grandparent);
						} else {
							roundLeft(parent, grandparent);
						}
						//旋转后,原parent节点成为原current的子节点.
						//因此,新的颜色序列为:
						//grandparent--red, current--black, parent--red
					} else {
						//如果是外侧子孙节点,则改变parent节点的颜色.
						//此时的颜色序列是:
						//grandparent--red, parent--red, current--red.
						changeColor(parent);
						//改变后颜色为:
						//grandparent--red, parent--black, current--red.
						//同时, 由于直接将parent改为黑色, 所以,以parent为根的子树
						//的黑色长度比grandparent另一个子树多1.
					}
					/**
					 * 1. 内侧子孙节点的情况得到了颜色序列:
					 * grandparent--red, current--black, parent--red
					 * 同时parent为根节点的子树黑色长度比其他子树多1.
					 * 即:grandparent的parent一端子树比另一端子树黑色长度大1.
					 * 可以看出,如果讲grandparent为根的子树进行一次与isLeft相反方向的旋转,
					 * 得到的子树根为原current.
					 * 新根的另一边子树为原根节点(grandparent)为根的子树.
					 * 所以, 旋转使得新形成的子树的黑色长度平衡.
					 * 又由于原current是黑色, 他的左右节点是原grandparent和parent,
					 * 两个都是红色, 因此,颜色也平衡了.
					 */
					
					/**
					 * 2. 外侧子孙节点的情况得到了颜色序列:
					 *  grandparent--red, parent--black, current--red.
					 *  将grandparent为根的子树以isLeft相反方向进行一次旋转之后,
					 *  同理上述的1, 使得子树能够达到黑色长度和颜色规则同时平衡.
					 *  
					 */
					if(isLeft) {
						roundRight(grandparent, ancestor);
					} else {
						roundLeft(grandparent, ancestor);
					}
				}
			}
			//以下代码是促使循环向下执行.并修改isLeft状态.
			ancestor = grandparent;
			grandparent = parent;
			parent = current;
			if(value &lt; current.value) {
				current = current.left;
				isLeft = true;
			} else {
				current = current.right;
				isLeft = false;
			}
		}
		
		//以下代码为节点的插入算法.
		
		//根据isLeft状态,直接将要插入的node和树连接起来.
		if(isLeft) parent.left = node;
		else parent.right = node;
		
		//因为新插入的节点必然是红色的,因此,如果新插入节点的父节点是红色,需要进行颜色变换.
		if(parent.isRed) {
			//以下颜色变换算法和上面查找节点算法中的颜色变换算法一致.
			//只不过这里的current节点是新插入的节点node.
			changeColor(grandparent);
			if(!isOutGrandsonNode(node, parent, grandparent)) {
				changeColor(node);
				if(node == parent.left) {
					roundRight(parent, grandparent);
				} else {
					roundLeft(parent, grandparent);
				}
			} else {
				changeColor(parent);
			}
			if(node == parent.left) {
				roundRight(grandparent, ancestor);
			} else {
				roundLeft(grandparent, ancestor);
			}
		}
	}
	
	public void display() {
		traversalOf(root);
		System.out.println();
	}
	
	private void traversalOf(Node node) {
		if(node == null) return ;
		traversalOf(node.left);
		if(!node.isDelete) {
			System.out.print(node.value + " ");
		}
		traversalOf(node.right);
	}
	
	public boolean delete(long value) {		
		Node current = root; //当前节点.
		
		while(current != null &amp;&amp; value != current.value) {
			if(value &lt; current.value) {
				current = current.left;
			} else {
				current = current.right;
			}
		}
		
		if(current != null) {
			current.isDelete = true;
			return true;
		}
		
		return false;
	}
	
	public boolean search(long value) {
		Node current = root; //当前节点.
		
		while(current != null &amp;&amp; value != current.value) {
			if(value &lt; current.value) {
				current = current.left;
			} else {
				current = current.right;
			}
		}
		
		return current == null ? false : true;
	}
	
	/**
	 * 判断指定节点是祖父节点的外侧子孙节点还是内侧子孙节点.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail lgg860911@yahoo.com.cn
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 7, 2009-1:37:43 PM
	 * @param current 需要判断的节点.
	 * @param parent 父节点
	 * @param grandparent 祖父节点
	 * @return 外侧返回true
	 */
	private boolean isOutGrandsonNode(Node current, Node parent,
			Node grandparent) {
		return (parent == grandparent.left &amp;&amp; current == parent.left)
			|| (parent == grandparent.right &amp;&amp; current == parent.right);
	}
	
	/**
	 * 改变指定节点的颜色
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail lgg860911@yahoo.com.cn
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 7, 2009-1:37:19 PM
	 * @param node 指定的节点.
	 */
	private void changeColor(Node node) {
		node.isRed = !node.isRed;
	}
	
	/**
	 * current为根的子树进行右转.
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail lgg860911@yahoo.com.cn
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 7, 2009-12:49:39 PM
	 * @param current 旋转的子树的根
	 * @param parent 子树的根的父节点.
	 * @return 旋转后子树的根.
	 */
	private Node roundRight(Node current, Node parent) {
		//当parent==null时,说明current是根节点.
		//因此,旋转的时候需要考虑根节点的引用指向.
		if(parent == null) {
			//由于右转之后,current的left节点上移,所以旋转后的root节点必然是current.left
			//我们还是把这棵树理解成为子树.那么这棵子树旋转后的顶点必然是current.left
			root = current.left;
			//右旋导致子树原根节点的左子节点的右子节点向右平移.连接到原根节点的左子节点上.
			current.left = current.left.right;
			//旋转前的根节点下移,成为新的根节点的右子节点.
			root.right = current;
			//返回旋转后的子树的新根.
			return root;
		}
		//当parent!=null时,需要考虑子树旋转后和整棵树的挂接.
		if(current == parent.left) {
			//右旋必然导致子树的新根是current.left,由于子树在整棵树中是
			//parent的左子节点,所以,将新的根和parent的左子节点链接.
			parent.left = current.left;
			//右旋导致旋转前的根节点的左子节点的右子节点向右平移, 连接到原根节点的左子节点上.
			current.left = parent.left.right;
			//旋转前的根节点下移,成为新的根节点的右子节点.
			parent.left.right = current;
			//返回旋转后的子树的新根.
			return parent.left;
		}
		//当parent!=null时,需要考虑子树旋转后和整棵树的挂接.
		if(current == parent.right) {
			//右旋必然导致子树的新根是current.right,由于子树在整棵树中是
			//parent的右子节点,所以,将新的根和parent的右子节点链接.
			parent.right = current.left;
			//右旋导致旋转前的根节点的左子节点的右子节点向右平移, 连接到原根节点的左子节点上.
			current.left = parent.right.right;
			//旋转前的根节点下移,成为新的根节点的右子节点.
			parent.right.right = current;
			//返回旋转后的子树的新根.
			return parent.right;
		}
		return null;
	}
	
	/**
	 * current为根的子树进行左旋
	 * @announce Keep all copyright, if you want to reprint, please announce source.
	 * @author selfimpr
	 * @mail lgg860911@yahoo.com.cn
	 * @blog http://blog.csdn.net/lgg201
	 * @data May 7, 2009-1:19:42 PM
	 * @param current 子树的根
	 * @param parent 子树的根的父节点.
	 * @return 旋转后子树的根.
	 */
	private Node roundLeft(Node current, Node parent) {
		//当parent==null时,说明current是根节点.
		//因此,旋转的时候需要考虑根节点的引用指向.
		if(parent == null) {
			//由于子树的定点是根,所以左旋必然导致当前根的右子节点上移成为新的根.
			root = current.right;
			//左旋导致旋转前的根节点的右子节点的左子节点左移成为原根节点的右子节点.
			current.right = current.right.left;
			//左旋导致原根节点下移,成为新的根节点的左子节点.
			root.left = current;
			//返回左旋后的子树的新根.
			return root;
		}
		//当parent!=null时,需要考虑子树旋转后和整棵树的挂接.
		if(current == parent.left) {
			//左旋必然导致原根节点的右子节点成为新的根节点,由于子树在整棵树中
			//是parent节点的左子节点,所以,由parent.left链接子树的新根.
			parent.left = current.right;
			//左旋导致旋转前根节点的右子节点的左子节点左移成为原根节点的右子节点.
			current.right = parent.left.left;
			//左旋导致原根节点下移,成为新的根节点的左子节点.
			parent.left.left = current;
			//返回旋转后的子树的新根.
			return parent.left;
		}
		//当parent!=null时,需要考虑子树旋转后和整棵树的挂接.
		if(current == parent.right) {
			//左旋必然导致原根节点的右子节点成为新的根节点,由于子树在整棵树中
			//是parent节点的右子节点,所以,由parent.right链接子树的新根.
			parent.right = current.right;
			//左旋导致旋转前根节点的右子节点的左子节点左移成为原根节点的右子节点.
			current.right = current.right.left;
			//左旋导致原根节点下移,成为新的根节点的左子节点.
			parent.right.left = current;
			//返回旋转后的子树的新根.
			return parent.right;
		}
		return null;
	}
	
}

```

 
测试类: RedBlackTreeTest.java

```java
package selfimpr.datastruct.redblacktree.test;

import java.util.Random;

import junit.framework.TestCase;
import selfimpr.datastruct.redblacktree.RedBlackTree;

/**
 * 
 * @author selfimpr
 * @mail lgg860911@yahoo.com.cn
 * @blog http://blog.csdn.net/lgg201
 */
public class RedBlackTreeTest extends TestCase {
	
	Random random = new Random();
	final int length = 1000;
	
	public void testTree() {
		RedBlackTree tree = new RedBlackTree();
		for(int i=0; i&lt;length; i++) {
			tree.insert(i);
		}
		tree.display();
		for(int i=20; i&lt;300; i++) {
			tree.delete(i);
		}
		tree.display();
	}
}

```

