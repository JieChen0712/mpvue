<?php

/**
 * 	雨林控股经销商后台——首页
 */
class Index1Action extends Action {

    //经销商后台首页
    public function index() {
    	

//        if ($_SESSION['logina'] != 'yes') {
//            session('headimgurl', null);
//            checkAuth('index');
//        }
//        session('logina', null);
        $distributor = M('distributor');
        //查询是否为经销商，若是，将经销商ID存入session
        if ($_SESSION['oid']) {
            $manager = $distributor->where(array('openid' => $_SESSION['oid']))->find();
        }
        if ($_SESSION['headimgurl'] != $manager['headimgurl']) {
            $arr['headimgurl'] = $_SESSION['headimgurl'];
            $add = $distributor->where(array('openid' => $_SESSION['oid']))->save($arr);
        }
        $list = $distributor->where(array('openid' => $_SESSION['oid']))->find();
        if (!empty($list)) {
            if ($list['audited'] == 1 && $list['status'] == 1) {
                session('managerid', $list['id']);
            } else {
                $this->redirect('admin/Login/index');
            }
        } else {
            $this->redirect('admin/Login/index');
        }
        
        //查看该经销商的资金表
        $money_funds = M('money_funds')->where(array('uid'=>$manager['id']))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
        $this->recharge_money = $recharge_money;//虚拟币金额
        
        
        $this->content_list = get_admin_content_list('main');
        $this->user_count   = $this->get_user_count();
        
        $this->assign('manager', $list);
        $this->display();
    }
    
    //进入主页的后门
    // public function back(){
    // 	$this->display('Index/index');
    // }
    //个人中心
    public function center() {
        $manager = M('distributor')->where(array('openid' => $_SESSION['oid']))->find();
        $this->assign('manager', $manager);
        $this->display();
    }
    
    
    //获取用户统计信息
    public function get_user_count(){
        $the_module_name = strtolower(MODULE_NAME);
        $the_action_name = strtolower(ACTION_NAME);
        
        $count_module_name = array(
            'index','login','funds','rebate','order'
        );
        
        $count_action_name = array(
            'index',''
        );
        
        //限定几个页面进行统计，其它页面不进行额外计算
        if( !in_array($the_module_name, $count_module_name) && !in_array($the_action_name, $count_action_name) ){
            return False;
        }
        
        
        $user_count = array(
            array(
                'name'  =>  '旗下代理',
                'count' =>  400,
            ),
            array(
                'name'  =>  '我的订单',
                'count' =>  5,
            ),
            array(
                'name'  =>  '待审核代理',
                'count' =>  12268,
            ),
        );
        
        return $user_count;
    }//end func get_user_count
    

}

?>