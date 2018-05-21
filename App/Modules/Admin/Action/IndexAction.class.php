<?php

/**
 * 	微斯咖经销商后台——首页
 */
class IndexAction extends CommonAction {

    //经销商后台首页
    public function index() {
//        echo $_SESSION['oid'];return;
    	$not_veryfy_url = __GROUP__.'/Login/loginout';

//        if ( empty($_SESSION['oid']) ) {
//            session('headimgurl', null);
//            $this->redirect($not_veryfy_url);
//        }
//        echo $_SESSION['oid'];return;
        
        
        $redirect_url = I('redirect_url');
        if( !empty($redirect_url) ){
            $redirect_url = base64_decode($redirect_url);
            $this->redirect($redirect_url);
        }
        
        
        $distributor = M('distributor');
        //查询是否为经销商，若是，将经销商ID存入session
        $list = $distributor->where(array('openid' => $_SESSION['oid']))->find();

        
        
        //$list = $distributor->where(array('openid' => $_SESSION['oid']))->find();
        if (!empty($list)) {
            
            if ( !empty($_SESSION['headimgurl']) && $_SESSION['headimgurl'] != $list['headimgurl'] ) {
//                $arr['headimgurl'] = $_SESSION['headimgurl'];
//                $add = $distributor->where(array('openid' => $_SESSION['oid']))->save($arr);
                $_SESSION['headimgurl'] = $list['headimgurl'];
            }
            
            if ($list['audited'] == 1 && $list['disable'] != 1 ) {
                session('managerid', $list['id']);
            }
            elseif ( $list['disable'] == 1 ) {
                session('logina', null);
                session('login', null);
                $this->redirect($not_veryfy_url);
            }
            else {
//                $this->redirect('admin/Login/index');
                $this->redirect($not_veryfy_url);
            }
        } else {
            $this->redirect($not_veryfy_url);
        }
        //总数
        $id = $this->uid;
        //推介
        $my_count=$distributor->where(array('recommendID' => $id,'audited'=>'1'))->count();
        $myteam_my_count = number_format($my_count);
        //直属
        $under_count=$distributor->where(array('pid' => $id,'audited'=>'1'))->count();
        $myteam_under_count =  number_format($under_count);

        $this->assign("myteam_my_count", $myteam_my_count);
        $this->assign("myteam_under_count", $myteam_under_count);

        $this->activity = C('ACTIVITY');
        //获取未审核代理
        $where = [
            'pid' => $this->uid,
            'audited' => 0,
//            'managed' => 0
        ];
        $field = ['id','authnum', 'name', 'level', 'levname','phone'];
        //待审核经销商
        $no_audit = $distributor->where($where)->field($field)->select();
        
        import('Lib.Action.Team','App');
        $team_obj = new Team();
        $p_money=$team_obj->get_team_money($this->uid);
        //个人/团队业绩
        $this->person_money = number_format($p_money,2);

        //读取缓存团队
        import('Lib.Action.Funds','App');
        $Funds_obj = new Funds();
        $team_path = get_team_path_by_cache();
        if(C('MONEY_COUNT_WAY') || (!C('MONEY_COUNT_WAY') && !$Funds_obj->is_parent_audit)){
            $uids = $team_obj->get_team_ids($this->uid, $team_path);
        }else{
            $uids=$this->uid;
        }

        $team_money = $team_obj->get_team_money($uids);
        //出于性能考虑，把团队id和团队业绩保存到session(或其它)里
        // session('uids', $uids);
        // session('team_money', $team_money);
        $this->team_money = number_format($team_money,2);
        
        //总推荐返利
//        $this->rec_money = M('recommend_rebate')->where(['user_id' => $this->uid])->sum('money');
//        
//        //订单奖励
//        $order_rebate=M('rerebate')->where(array('user_id'=>$this->uid))->sum('money');
//        $this->order_rebate=$order_rebate;
//        //充值奖励
//        $apply_rebate=M('rebate_apply')->where(array('user_id'=>$this->uid))->sum('money');
//        $this->apply_rebate=$apply_rebate;
        
        //平级推荐订单返利，平级推荐充值返利，低推高一次性返利
        $other_money = M('rebate_other')->where(['uid' => $this->uid])->sum('money');
        //团队奖励
        $team_moneys=M('rebate_team')->where(['uid' => $this->uid])->sum('rebate_money');
        $total_money=bcadd($other_money,$team_moneys,2);
        //总奖励
        $this->total_money = $total_money ;

        //滚动通知
        $this->inform=M('inform')->where(array('status'=>1))->find();

        $this->ct = encode(time());
        $this->no_audit = $no_audit;
        $this->display();
    }
    
    
    //个人中心
    public function center() {
        $this->display();
    }
    
    
    
    public function get_wechat_info(){
        
        $options = array(
            'token' => C('APP_TOKEN'), //填写您设定的key
            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        
        import("Wechat.Wechat", APP_PATH);
        $this->wechat_obj = new Wechat($options);
        
        
        $res = $this->wechat_obj->getUserInfo($_SESSION['oid']);
        
        print_r($res);
    }//end func get_wechat_info
    
}

?>