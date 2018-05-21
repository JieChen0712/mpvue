<?php

header("Content-type:text/html;charset=utf-8");

/**
 * 营销模块
 */
class CommonAction extends Action {
    protected $uid;
    protected $activity;
    protected $openid;
    
    protected $user_type = 0;
    //用户类型
    protected $user_type_names = [
        0   =>  '未知',
        1   =>  '经销商',
        2   =>  '游客',
    ];
    
    
    public function _initialize() {
        $the_module_name = strtolower(MODULE_NAME);
        $the_action_name = strtolower(ACTION_NAME);
        
        
        if(  empty($_SESSION['oid']) ){
            $url = __APP__.'/admin/index';
            $this->redirect($url);
        }
        
        if( $the_module_name != 'index' && empty($_SESSION['sale_openid']) && $the_action_name != 'lunpan' ){
            $url = __APP__.'/admin/index';
            $this->redirect($url);
        }
        
        
        $condition = [
            'openid'    =>  $_SESSION['sale_openid'],
        ];
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->get_sale_user([],$condition);
        $user = $result['list'];

        session('sail_id',$user[0]['id']);
        $this->user = $user[0];
        $this->sail_id = $user[0]['id'];
        
        
        
        $this->uid = $user[0]['id'];
        $this->sale_openid = $_SESSION['sale_openid'];
        $this->openid = $_SESSION['oid'];
//        $this->manager = $manager;
        $this->userdetail = $_SESSION['userdetail'];
        $this->level_num = C('LEVEL_NAME');
        $this->manager = $user[0];
    }

    //设置
    public function set_sale_user($type='none'){
        
        import('Lib.Action.Sale','App');
        
        $wechat_info = $_SESSION['userdetail'];
        $openid = $wechat_info['openid'];
        
        $Sale = new Sale();
        $result = $Sale->set_user($openid,$wechat_info,$type);
        
        return $result;
    }
    
    
    
    
}

?>