<?php
/**
*	微斯咖前台——宣传图
*/
class TaobaoAction extends Action {
	public function index()
	{
		$row = D('Publicity')->find();
		$this->row = $row;
		$this->display();
	}
}
?>