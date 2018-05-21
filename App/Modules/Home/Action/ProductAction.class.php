<?php
/**
*	微斯咖前台——产品中心
*/
class ProductAction extends Action {

	//产品中心首页
	public function index()
	{
		$list = M('Goods')->order('time desc')->select();
		$this->assign('list',$list);
		$this->display();
	}

	//产品详情页面
	public function detail()
	{
		$pd = $_GET['pd'];
		$list = M('Goods')->field('news')->where(array('id'=>$pd))->find();
		$this->assign('news',$list);
		$this->display();
	}
}
?>