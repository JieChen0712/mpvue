<?php
/**
*	微斯咖前台——公司介绍
*/
class CompanyAction extends Action {

	//公司介绍页面
	public function index()
	{
		$list=D('Aptitude')->order('time desc')->select();
		$this->assign('list', $list);
		$this->display();
	}
	
	public function gszz()
	{
		$id = $_GET['id'];
		$list=D('Aptitude')->where(array('id'=>$id))->find();
		$this->list=$list;
		$this->display();
	}
}
?>