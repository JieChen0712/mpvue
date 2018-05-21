<?php
/**
*	微斯咖前台——宣传图
*/
class TruerecordAction extends Action {
	public function index()
	{
		$row = D('Truerecord')->find();
		$this->row = $row;
		$this->display();
	}
}
?>