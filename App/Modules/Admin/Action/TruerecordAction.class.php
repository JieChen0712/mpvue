<?php
/**
*	微斯咖前台——实体备案
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