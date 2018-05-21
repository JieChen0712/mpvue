<?php

/**
 * 	微斯咖经销商后台——首页
 */
class LoginAction extends Action {

    //经销商后台首页
    public function index() {
        $this->display();
    }

    public function login_green() {
        $this->display();
    }
    
    //检测用户登录
    public function checkLogin() {
        $name = I('post.name');
        $password = I('post.password');
        $row = M('Distributor')->where(array('wechatnum' => $name))->find();
        if (!$row) {
            $this->ajaxReturn('none', 'JSON');
        } else {
            if ($row['password'] == md5($password)) {
                if ($row['audited'] != 1) {
                    $this->ajaxReturn('nomanager', 'JSON');
                } else if ($row['disable'] == 1) {
                    $this->ajaxReturn('disable', 'JSON');
                } else {
                    session('loginName', $row['wechatnum']);
                    //这个与home/wechataction/getUserInfo写入的session一致
                    session('login', 'yes');
                    session('logina', 'yes');
                    session('oid', $row['openid']);
                    session('headimgurl', $row['headimgurl']);
                    session('nickname', $row['nickname']);
                    
//                    $uio = $row['wechatnum'];
//                    $jkl = substr($row['password'], 4, 22).md5(date('Ymd'));
//                    $data['url'] = __GROUP__."/Login/successLogin/?uio=".$row['wechatnum']."&jkl=" .$jkl;
                    
                    $data = 'succ';
                    $this->ajaxReturn($data, 'JSON');
                }
            } else {
                $this->ajaxReturn('none', 'JSON');
            }
        }
    }
    
    
    //修改密码
    public function changePsw() {
        $this->display();
    }

    //确定修改密码
    public function enterCPsw() {
        $psw = I('post.psw');
        $fpsw = I('post.fpsw');
        $tpsw = I('post.tpsw');
        $password = M('Distributor')->where(array('id' => $this->uid))->getfield('password');
        if ($password != md5($psw)) {
            //原密码不对
            $this->ajaxReturn('0', 'JSON');
        } else {
            if ($fpsw != $tpsw) {
                //两次密码输入不一致
                $this->ajaxReturn('1', 'JSON');
            } else {
                $res = M('Distributor')->where(array('id' => $this->uid))->save(array('password' => md5($fpsw)));
                if (!$res) {
                    //修改失败
                    $this->ajaxReturn('2', 'JSON');
                } else {
                    //修改成功
                    $this->ajaxReturn('3', 'JSON');
                }
            }
        }
    }
    
    //退出登陆
    public function loginout() {
        
        $return_url = trim(I('return_url'));
        
        M('Distributor')->where(array('id' => session('managerid')))->setField('status', 0);
        session('oid', null);
        session('headimgurl', null);
        session('nickname', null);
        session('managerid', null);
        session('login', null);
        session('logina', null);
        
        $exit_url = __GROUP__.'/Login/index';
        if( !empty($return_url) ){
            $exit_url = base64_decode($return_url);
        }
        
        
        $this->redirect($exit_url);
    }
    
    
    
    //-------------------------以下是废弃代码 2017.8.18----------------------
    
    
    //登陆成功进入后台
    public function successLogin() {
        $wechatnum = $_GET['uio'];
        $pass = $_GET['jkl'];
        $distributor = M('distributor');
        $manager = $distributor->where(array('wechatnum' => $wechatnum))->find();
        if ($manager) {
            $passone = substr($manager['password'], 4, 22).md5(date('Ymd'));
            if ($pass != $passone) {
                $this->redirect('Login/index');
            } else {
                
                session('oid', $manager['openid']);//微信openid
                session('wechatnum', $manager['wechatnum']);//微信号
                session('headimgurl', $manager['headimgurl']);//微信头像logo
                session('nickname', $manager['nickname']);//名字
                session('name', $manager['name']);//名字
                session('managerid', $manager['id']);//当前登录用户的ID
                
                
                session('login', 'yes');
                //登陆成功下次可以直接进入后台不用登陆
                $distributor->where(array('wechatnum' => $wechatnum))->setField('status', 1);
                $this->user_count   = $this->get_user_count();
                $this->activity = C('ACTIVITY');
                
               //获取未审核代理
                $where = [
                    'pid' => $manager['id'],
                    'audited' => 0,
                    'managed' => 0
                ];
                $field = ['id','authnum', 'name', 'level', 'levname'];
                //待审核经销商
                $no_audit = $distributor->where($where)->field($field)->select();
                
                import('Lib.Action.Team','App');
                $team_obj = new Team();
                //个人/团队业绩
                $this->person_money = $team_obj->get_team_money($manager['id']);

                //读取缓存团队
                $team_path = get_team_path_by_cache();

                $uids = $team_obj->get_team_ids($manager['id'], $team_path);
                $team_money = $team_obj->get_team_money($uids);
                //出于性能考虑，把团队id和团队业绩保存到session(或其它)里
                // session('uids', $uids);
                // session('team_money', $team_money);
                $this->team_money = $team_money;
                
                //总推荐返利
                $this->rec_money = M('recommend_rebate')->where(['user_id' => $this->uid])->sum('money');
                
                 //订单奖励
                $order_rebate=M('rerebate')->where(array('user_id'=>$this->uid))->sum('money');
                $this->order_rebate=$order_rebate;
                //充值奖励
                $apply_rebate=M('rebate_apply')->where(array('user_id'=>$this->uid))->sum('money');
                $this->apply_rebate=$apply_rebate;
                
                $this->level_num = C('LEVEL_NUM');
                $this->level_name = C('LEVEL_NAME');
                $this->manager = $manager;
                $this->no_audit = $no_audit;
                
                $this->display('Index/index');
            }
        } else {
            $this->redirect('Login/index');
        }
    }

    
    
    
    //获取用户统计信息
    public function get_user_count(){
        $the_module_name = strtolower(MODULE_NAME);
        $the_action_name = strtolower(ACTION_NAME);
        
//        $count_module_name = array(
//            'index',
//        );
//        //'funds','rebate','order'
//        
//        $count_action_name = array(
//            'index',''
//        );
//        
//        //限定几个页面进行统计，其它页面不进行额外计算
//        if( !in_array($the_module_name, $count_module_name) && !in_array($the_action_name, $count_action_name) ){
//            return False;
//        }
        
        
        $user_count = array(
            'team_num'  =>  0,          //团队人数
            'my_total_money'    =>  0,  //我的业绩
            'team_total_money'  =>  0,  //团队业绩
            'status2'           =>  0,  //待审核
            'status3'           =>  0,  //已发货
            'recharge_money'    =>  0,  //虚拟币
        );
        
        $uid = $this->uid;
        
        
        import('Lib.Action.User','App');
        $User = new User();
        
        
        $user_team_info = $User->get_user_team_count($uid);
        
        
        if( $user_team_info['code'] == 1 ){
            $user_count['team_num']         =   $user_team_info['result']['team_num'];
            $user_count['my_total_money']   =   $user_team_info['result']['my_order_info']['total_money'];
            $user_count['team_total_money'] =   $user_team_info['result']['team_order_info']['total_money'];
            $user_count['status2']          =   isset($user_team_info['result']['my_order_status']['status2'])?$user_team_info['result']['my_order_status']['status2']:0;
            $user_count['status3']          =   isset($user_team_info['result']['my_order_status']['status3'])?$user_team_info['result']['my_order_status']['status3']:0;
        }
        
        
        
        //查看该经销商的资金表
        $money_funds = M('money_funds')->where(array('uid'=>$uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
        $user_count['recharge_money'] = $recharge_money;//虚拟币金额
        
        
        return $user_count;
    }//end func get_user_count
    
}

?>