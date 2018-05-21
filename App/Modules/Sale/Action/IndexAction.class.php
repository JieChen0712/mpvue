<?php

/**
 * 	雨丝燕
 */
class IndexAction extends Action {

    //
    public function index() {
        echo '营销首页';
    }
    
    
    //轮盘首页
    public function lunpan() {
        
        $userdetail = $_SESSION['userdetail'];
        $sale_openid = $_SESSION['sale_openid'];
        $sail_id = $_SESSION['sail_id'];
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        
        $is_subscribe = 0;//是否检测关注
        
        //登录
        if( empty($sale_openid) || empty($userdetail)  ){
            $return_url = __APP__.'/sale/index/lunpan';
//            echo $return_url;return;
            checkAuth('getsaleinfo','','',$return_url);
        }
        
        if( $userdetail['subscribe'] == '1' || !$is_subscribe ){
            $wechat_info = !$is_subscribe?$_SESSION['wechatinfo']:($_SESSION['userdetail']);
            $openid = $wechat_info['openid'];
            
//            print_r($wechat_info);return;
            
            //设置用户
            $set_user = $Sale->set_user($openid,$wechat_info,'lunpan');
            
//            print_r($set_user);return;
            
            if( $set_user['code'] != 1 ){
    //            print_r($set_user);
                $return_url = 'http://'.C('YM_DOMAIN').'/home/index';
                error_tip($set_user['msg'], '',$return_url);
                return;
            }
        }
        else{
            session('userdetail',null);
            session('sale_openid',null);
        }
        
        
        //得到
        $check_res = [
            'code'  =>  -1,
            'msg'   =>  '系统没有得到扫码信息，请重新扫码！',
        ];
        if( !empty($_SESSION['sale_code']) ){
            $check_res = $Sale->check_code($_SESSION['sale_code'],'lunpan',$sail_id);

            if( $check_res['code'] != 1 ){
                //session('sale_code',null);
            }
        }
        
        
        //轮盘活动基础信息
        $condition['type']  =   'lunpan';
        $result = $Sale->get_sale_base([],$condition);
        
        //轮盘信息
        $condition['type']  =   'lunpan';
        $lunpan_set = $Sale->get_lunpan_set([],[]);
        
        //获取中奖信息
        $condition['p_type']  =   'lunpan';
        $page_info = [
            'page_list_num' =>  20,
            'page_num'      =>  1,
        ];
        $order_info = $Sale->get_sale_order($page_info,$condition);
        $order_list = $order_info['list'];
        
        $order_list_fixed = [
//            ['s_phone_encrypt'=> '135*****954','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '137*****832','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '138*****002','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '135*****453','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '135*****223','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '135*****356','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '134*****551','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '135*****249','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '131*****742','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '131*****854','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '132*****965','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '170*****367','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '158*****312','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '159*****117','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '150*****053','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '135*****773','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '132*****873','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '135*****617','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '138*****901','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '135*****240','p_name'=>'一等奖'],
//            ['s_phone_encrypt'=> '131*****953','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '131*****809','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '135*****724','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '132*****668','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '132*****906','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '134*****983','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '137*****822','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '137*****439','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '137*****777','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '138*****567','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '138*****415','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '138*****489','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '150*****744','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '158*****019','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '158*****808','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '131*****061','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '134*****263','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '134*****237','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '134*****257','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '188*****252','p_name'=>'二等奖'],
//            ['s_phone_encrypt'=> '188*****116','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '188*****740','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****880','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '137*****974','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****752','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '138*****005','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****980','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '132*****511','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '131*****927','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '137*****837','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****905','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '137*****605','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****286','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '138*****118','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****103','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '158*****861','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '188*****998','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****662','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '135*****545','p_name'=>'三等奖'],
//            ['s_phone_encrypt'=> '132*****400','p_name'=>'三等奖'],
        ];
        
        $order_list = array_merge($order_list,$order_list_fixed);
        shuffle($order_list);
//        print_r($order_list);return;
//        print_r($result);return;
        
        $this->lunpan_set = $lunpan_set['list'];
        $this->order_list = $order_list;
        $this->check_res    =   $check_res;
        $this->list = $result['list'];
        $this->display();
    }
    
    
    
    
    

}

?>