<?php

/**
 * 	雨丝燕营销
 */
class LunpanAction extends CommonAction {

    
    
    //我的奖品
    public function myprize(){
        
        $sail_id = $_SESSION['sail_id'];
        $userdetail = $_SESSION['userdetail'];
        $sale_openid = $_SESSION['sale_openid'];
        
        //登录
        if( empty($sale_openid) || empty($userdetail) ){
            $return_url = base64_encode(__GROUP__.'/lunpan/myprize');
            checkAuth('getsaleinfo','','',$return_url);
        }
        elseif( $userdetail['subscribe'] == '0' ){
            $url = __URL__.'/index';
            $this->redirect($url);
        }
        else{
            //设置用户
            $set_user = $this->set_sale_user('lunpan');
            if( $set_user['code'] != 1 ){
                $this->error($set_user['msg'],'http://'.C('YM_DOMAIN').'/home/index');
                return;
            }
        }
        
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        
        $condition = [
            'sale_id'    =>  $sail_id,
            'type'      =>  'lunpan',
        ];
        
        $sale_order = $Sale->get_sale_order([],$condition);
//        print_r($sale_order);return;
        
        $this->list = $sale_order['list'];
        $this->display();
    }
    
    
    
    //我的奖品详情
    public function myprizedetail(){
        
        $order_num = I('order_num');
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        
        $condition = [
            'sail_id'    =>  $_SESSION['sail_id'],
            'order_num' =>  $order_num,
        ];
        
        $sale_order_info = $Sale->get_sale_order([],$condition);
        
        if( empty($sale_order_info['list']) ){
            $this->error('查无此订单信息！');
        }
        
        $this->list = $sale_order_info['list'][0];
        $this->display();
    }
    
    
    
    //获取奖品ajax
    public function get_prize_ajax(){
        
        $model = M('sale_lunpan');
        
        $field = 'id,name,today_num,img';
        
        $info = $model->field($field)->select();
        
        $userdetail = $_SESSION['userdetail'];
        
        $subscribe = $userdetail['subscribe'];
        
        if( empty($info) ){
            $return_result = [
                'code'  =>  1,
                'msg'   =>  '获取成功，但轮盘信息为空！',
                'info'  =>  $info,
                'subscribe' =>  $subscribe,
            ];
        }
        else{
            $return_result = [
                'code'  =>  1,
                'msg'   =>  '获取成功',
                'info'  =>  $info,
                'subscribe' =>  $subscribe,
            ];
        }
        
        $this->ajaxReturn($return_result);
    }//end func get_prize_ajax
    
    
    //获取中奖信息
    public function get_sale_record_ajax(){
        
        $p_num = I('p');
        
        $condition['type'] = 'lunpan';
        $other['get_lunpan_win'] = 1;
        
        $other = [
            'get_lunpan_win'   =>  1,
        ];
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  $p_num,
        );
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->get_sale_record($page_info,$condition,$other);
        
        $this->ajaxReturn($result);
        
    }//end func get_sale_record_ajax
    
    
    
    //轮盘抽奖
    public function lottery_ajax(){
        if( !IS_AJAX ){
            return FALSE;
        }
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        
        $code = $_SESSION['sale_code'];
        
        $result = $Sale->lottery($_SESSION['sale_openid'],$code);
        
        $this->ajaxReturn($result);
    }//end func lottery_ajax
    
    
    //提交订单
    public function write_order_ajax(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        $record_id = I('record_id');
        $s_name = I('s_name');
        $s_phone = I('s_phone');
        $s_addre = I('s_addre');
        $notes = I('notes');
        $area = I('area');
        
        $s_addre = $area.$s_addre;
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        
        $write_info = [
            'record_id' =>  $record_id,
            'p_type'    =>  'lunpan',
            's_name'    =>  $s_name,
            's_phone'   =>  $s_phone,
            's_addre'   =>  $s_addre,
            'notes'     =>  $notes,
        ];
        
        $write_order_result = $Sale->write_order($_SESSION['sail_id'],$write_info);
        
        
        $this->ajaxReturn($write_order_result);
    }//end func write_order_ajax
    
    
    
    //绑定代理账户
    public function bind_disid_ajax(){
        
        $name = I('name');//账号
        $passwd = I('passwd');//密码
        
        if( empty($name) || empty($passwd) ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '提交的信息不全！',
            ];
            $this->ajaxReturn($result);
        }
        
        $manager = $this->manager;
        
        if( $manager['bind_disid'] != 0 ){
            $result = [
                'code'  =>  3,
                'msg'   =>  '账号已绑定代理，无法再绑定！',
            ];
            $this->ajaxReturn($result);
        }
        
        
        $condition = [
            'wechatnum'  =>  $name,
        ];
        
        $dis_info = M('distributor')->field('id','password','disable')->where($condition)->find();
        
        $cur_password = $dis_info['password'];
        
        if( empty($dis_info) || $cur_password != md5($passwd) ){
            $result = [
                'code'  =>  4,
                'msg'   =>  '账号或密码错误，请重试！',
            ];
            $this->ajaxReturn($result);
        }
        
        $disable = $dis_info['disable'];
        $disid = $dis_info['id'];
        
        if( $disable == 1 ){
            $result = [
                'code'  =>  5,
                'msg'   =>  '账号已经被禁用,无法绑定！',
            ];
            $this->ajaxReturn($result);
        }
        
        $condition_sale = [
            'id'    => $this->uid,
        ];
        
        $save = [
            'bind_disid'    =>  $disid,
        ];
        
        $save_res = M('sale_user')->where($condition_sale)->save($save);
        
        if( !$save_res ){
            $result = [
                'code'  =>  6,
                'msg'   =>  '绑定失败，请重试！',
            ];
            $this->ajaxReturn($result);
        }
        
        
        $result = [
            'code'  =>  1,
            'msg'   =>  '绑定成功！',
        ];
        $this->ajaxReturn($result);
    }
    
    
    

}

?>