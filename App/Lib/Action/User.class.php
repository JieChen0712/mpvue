<?php
//用户管理的模块化代码
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class User extends Common{
    public $user_obj;
    
    /**
     * 架构函数
     */
    public function __construct() {
        $this->user_obj = M('user');
    }
    
    //根据请求参数openid获取用户id
    public function get_uid_by_openid($openid) {
        if (!$openid) {
            return false;
        }
        return $this->user_obj->where(['openid' => $openid])->getField('id');
    }
    
    
}