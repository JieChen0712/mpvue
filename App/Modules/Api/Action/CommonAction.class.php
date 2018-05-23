<?php

header("Content-type:text/html;charset=utf-8");

/**
 * 	调用api所要执行的操作
 */
class CommonAction extends Action {
    public $store_obj;
    public $store_id;//店铺id
    public $openid;//用户openid
    public function _initialize() {
        import('Lib.Action.Store', 'App');
        $this->store_obj = new Store();
        
        //接口请求带过的部分参数
        $store_id = trim(I('store_id'));//api传过来的店铺id
        $openid = trim(I('openid'));//api传过来的用户openid
       
        //检查店铺
        $res = $this->store_obj->check_store($store_id);
        if ($res['code'] != 1) {
            $this->ajaxReturn($res);
        }
        $this->store_id = $store_id;
        $this->openid = $openid;
    }
}

?>