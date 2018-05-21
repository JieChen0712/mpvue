<?php

/**
 * 	妈儿宝经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class RebateAction extends CommonAction {

    //返利页面
    public function index() {
        $this->display();
    }

    //返利全部列表
    public function fl() {
        $tmoney = 0; //统计金额的参数
        $distributor = M('distributor');
        $id = session('managerid');
        $condition['user_id'] = $id;
        $list = M('recommend_rebate')->where($condition)->order('time desc')->select();
        $field = "id,name,levname,recommendID";
        foreach ($list as $k => $v) {
            $row = $distributor->where(array('id' => $v['x_id']))->field($field)->find();
            $list[$k]['name'] = $row['name'];
            $list[$k]['levname'] = $row['levname'];
            $list[$k]['recommendID'] = $row['recommendID'];
        }
        $this->tmoney = $tmoney;
        $this->assign('list', $list);
        $this->display();
    }
    
    
    //应收返利详情
    public function flxq() {
        $distributor = M('distributor');
        
        $rebate = M('recommend_rebate');
        $id = session('managerid');
        $condition['user_id'] = $id;
        $condition['id'] = I('get.id');
        $list = $rebate->where($condition)->order('time desc')->find();
        $field = "id,name,level,levname,phone";
        $row = $distributor->where(array('id' => $list['x_id']))->field($field)->find();
        $list['x_info'] = $row;
        
        $this->assign('list', $list);
        $this->display();
    }
    
    
    

    //应付返利列表
    public function yf_fl() {
        $a = 0;
        $distributor = M('distributor');
        $order = M('order');
        $rebate = M('rerebate');
        $id = session('managerid');
        $condition['pay_id'] = $id;
        $list = M('rerebate')->where($condition)->order('time desc')->select();
        $field = "id,name,level,levname";
        $arra['pay_id'] = $id;
        $arra['state'] = 0;
        $conta = $rebate->where($arra)->count(); //统计未付款
        $arrb['pay_id'] = $id;
        $arrb['state'] = 1;
        $contb = $rebate->where($arrb)->count(); //统计已付款未确认
        $arrc['pay_id'] = $id;
        $arrc['state'] = 2;
        $contc = $rebate->where($arrc)->count(); //统计已付款已确认
        $contd = $contb + $contc;
        $cont['conta'] = $conta;
        $cont['contb'] = $contd;
        foreach ($list as $k => $v) {
            $a++;
            $list[$k]['a'] = $a;
            
            $rol = $order->where(array('order_num' => $v['order_num']))->group('order_num')->field('time,total_price')->find();
            $list[$k]['order_info'] = $rol;
            
            $row = $distributor->where(array('id' => $v['user_id']))->field($field)->find();
            $list[$k]['user_info'] = $row;
            
            $row2 = $distributor->where(array('id' => $v['x_id']))->field($field)->find();
            $list[$k]['x_info'] = $row2;
        }
        
        $this->list = $list;
        $this->cont = $cont;
        $this->assign('list', $list);
        $this->display();
    }

    //应付返利详情
    public function yf_flxq() {
        $distributor = M('distributor');
        $order = M('order');
        $rebate = M('rerebate');
        $id = session('managerid');
        $condition['pay_id'] = $id;
        $condition['id'] = I('get.id');
        $list = M('rerebate')->where($condition)->order('time desc')->find();
        $field = "id,name,level,levname,phone";
        
        
        $row = $distributor->where(array('id' => $list['user_id']))->field($field)->find();
        $list['user_info'] = $row;
            
        $row2 = $distributor->where(array('id' => $list['x_id']))->field($field)->find();
        $list['x_info'] = $row2;
        
        $rol = $order->where(array('order_num' => $list['order_num']))->group('order_num')->field('time,total_price')->find();
        $list['order_info'] = $rol;
        
        
        $this->assign('list', $list);
        $this->display();
    }
    
    //应付推荐返利列表
    public function yf_rec_fl() {
        $a = 0;
        $distributor = M('distributor');
        $order = M('order');
        $rebate = M('recommend_rebate');
        $id = session('managerid');
        $condition['payer_id'] = $id;
        $list = M('recommend_rebate')->where($condition)->order('time desc')->select();
        $field = "id,name,level,levname";
        $arra['payer_id'] = $id;
        $arra['status'] = 0;
        $conta = $rebate->where($arra)->count(); //统计未付款
        $arrb['payer_id'] = $id;
        $arrb['status'] = 1;
        $contb = $rebate->where($arrb)->count(); //统计已付款未确认
        $arrc['payer_id'] = $id;
        $arrc['status'] = 2;
        $contc = $rebate->where($arrc)->count(); //统计已付款已确认
        $contd = $contb + $contc;
        $cont['conta'] = $conta;
        $cont['contb'] = $contd;
        foreach ($list as $k => $v) {
            $row = $distributor->where(array('id' => $v['user_id']))->field($field)->find();
            $a++;
            $list[$k]['a'] = $a;
            $list[$k]['name'] = $row['name'];
            $list[$k]['level'] = $row['level'];
            $list[$k]['levname'] = $row['levname'];
            
            $x_info = $distributor->where(array('id' => $v['x_id']))->field($field)->find();
            $list[$k]['x_name'] = $x_info['name'];
            $list[$k]['x_level'] = $x_info['level'];
            $list[$k]['x_levname'] = $x_info['levname'];
            
        }
        $this->cont = $cont;
        $this->assign('list', $list);
        $this->display();
    }

    //应付推荐返利详情
    public function yf_rec_flxq() {
        $distributor = M('distributor');
        $order = M('order');
        $rebate = M('recommend_rebate');
        $id = session('managerid');
        $condition['payer_id'] = $id;
        $condition['id'] = I('get.id');
        $list = M('recommend_rebate')->where($condition)->order('time desc')->find();
        $field = "id,name,level,levname,phone";
        $row = $distributor->where(array('id' => $list['user_id']))->field($field)->find();
        $list['phone'] = $row['phone'];
        $list['name'] = $row['name'];
        $list['level'] = $row['level'];
        $list['levname'] = $row['levname'];
        
        $x_info = $distributor->where(array('id' => $list['x_id']))->field($field)->find();
        $list['x_phone'] = $x_info['phone'];
        $list['x_name'] = $x_info['name'];
        $list['x_level'] = $x_info['level'];
        $list['x_levname'] = $x_info['levname'];
        
        
        $this->assign('list', $list);
        $this->display();
    }
    
    
    

    //应收返利列表
    public function ys_fl() {
        $a = 0;
        $distributor = M('distributor');
        $order = M('order');
        $rerebate = M('Rerebate');
        $id = session('managerid');
        
        $condition = array(
            'user_id' => $id,
            'state' => array('gt', '0'),//显示已经审核的返利信息
        );

        $list_count = $rerebate->where($condition)->count();
        
        if ($list_count > 0) {
//                    $p = new Page($list_count, 8);
//                    $limit= $p->firstRow . "," . $p->listRows;
//                    $page = $p->show();

            $list = $rerebate->where($condition)->order('time desc')->select();

            $user_id_arr = array(); //经销商用户信息
            $user_id_str = '';
            $order_num_arr = array(); //订单下单信息
            $order_num_str = '';

            foreach ($list as $k => $v) {
                $list_user_id = $v['user_id'];
                $list_x_id = $v['x_id'];
                $list_order_num = $v['order_num'];
                $list_month = $v['month'];

                //出现的经销商用户ID数组
                if (!isset($user_id_arr[$list_user_id])) {
                    $user_id_arr[$list_user_id] = $list_user_id;
                }
                if (!isset($user_id_arr[$list_x_id])) {
                    $user_id_arr[$list_x_id] = $list_x_id;
                }

                //订单下单数组
                if (!isset($order_num_arr[$list_order_num])) {
                    $order_num_arr[$list_order_num] = $list_order_num;
                }
            }

            //经销商信息
            $user_id_str = implode(',', $user_id_arr);
            $condition_dis['id'] = array('in', $user_id_str);
            $distributor_info = $distributor->where($condition_dis)->select();
            $dis_info = array(); //以pid为key的经销商信息数组

            foreach ($distributor_info as $val1) {
                $dis_info[$val1['id']] = $val1;
            }


            //订单下单信息
            $order_num_str = implode(',', $order_num_arr);
            $condition_ord['order_num'] = array('in', $order_num_str);
            $order_info = $order->where($condition_ord)->select();
            $ord_info = array(); //以order_num为key的订单下单信息数组

            foreach ($order_info as $val2) {
                //这里会出现多条同一order_num的订单下单信息重叠，但由于主要获取总价格及数量，所以不影响
                $ord_info[$val2['order_num']] = $val2;
            }


            $benefit_order_info = array(); //订单下单计算信息
            $condition_order_count_info = array(); //订单下单获取条件
            //订单下单返利信息调整
            foreach ($list as $k => $v) {
                $list_user_id = $v['user_id'];
                $list_x_id = $v['x_id'];
                $list_order_num = $v['order_num'];
                $list_month = $v['month'];
                $benefit_order_key = $list_month . '_' . $list_user_id;

                
                $list[$k]['user_info'] = isset($dis_info[$list_user_id]) ? $dis_info[$list_user_id] : array();
                $list[$k]['x_info'] = isset($dis_info[$list_x_id]) ? $dis_info[$list_x_id] : array();
                $list[$k]['order_info'] = isset($ord_info[$list_order_num]) ? $ord_info[$list_order_num] : array();
//                $list[$k]['order_count_info'] = isset($benefit_order_info[$benefit_order_key]) ? $benefit_order_info[$benefit_order_key] : array();
            }
        }
        
//        print_r($list);return;
        
        $this->cont = $list_count;
        $this->assign('list', $list);
        $this->display();
    }

    //应收返利详情
    public function ys_flxq() {
        $distributor = M('distributor');
        $order = M('order');
        $rebate = M('Rerebate');
        $id = session('managerid');
        $condition['user_id'] = $id;
        $condition['id'] = I('get.id');
        $list = $rebate->where($condition)->order('time desc')->find();
        $field = "id,name,level,levname,phone";
        $row = $distributor->where(array('id' => $list['user_id']))->field($field)->find();
        $list['phone'] = $row['phone'];
        $list['name'] = $row['name'];
        $list['level'] = $row['level'];
        $list['levname'] = $row['levname'];
        $rol = $order->where(array('order_num' => $list['order_num']))->group('order_num')->find();
        $list['dtime'] = $rol['time'];
        $list['total_price'] = $rol['total_price'];
        $list['rol']    =   $rol;
        
        
        $this->assign('list', $list);
        $this->display();
    }

    //推荐人列表
    public function my_dl() {
        $distributor = M('distributor');
        $field = 'id,name,levname,phone,headimgurl';

        $managerid = session('managerid');

        $condition = array(
            'recommendID' => $managerid,
            'isRecommend' => '1',
        );
        
        $row = $distributor->where(array('id' => $managerid))->field($field)->find();
        $manager = $distributor->where($condition)->field($field)->select();
        $count = count($manager);

        $this->count = $count;
        $this->row = $row;
        $this->assign('manager', $manager);
        $this->display();
    }

    //推荐人订单下单
    public function my_dlxq() {
        $a = 0;
        $order = M('Order');
        $distributor = M('distributor');
        $field = 'id,name,levname,phone,headimgurl';
        $id = I('get.id');
        $manager = $distributor->where(array('id' => $id))->field($field)->find();
        $manager['count'] = $order->where(array('user_id' => $id))->count('distinct order_num');
        $list = $order->where(array('user_id' => $id))->group('order_num')->select();
        foreach ($list as $k => $v) {
            $a++;
            $list[$k]['a'] = $a;
        }
        $this->manager = $manager;
        $this->assign('list', $list);
        $this->display();
    }

    public function my_dlxqa() {
        $where['order_num'] = I('id');
        $row = M('Order')->where($where)->group('order_num')->find();
        //把产品分别显示
        $rol = M('Order')->where($where)->select();
        foreach ($rol as $k => $v) {
            $product = M('templet')->where(array('id' => $v['p_id']))->find();
            $rol[$k]['p_name'] = $product['name'];
        }
        $relist = M('Rerebate')->where($where)->find();
        $this->relist = $relist;
        $this->row = $row;
        $this->assign('rol', $rol);
        $this->display();
    }

    //提交返利截图改变状态
    public function fukuan() {
        $id = I('post.id');
        $arr = array(
            'img' => I('post.imgval'),
            'state' => 1,
            'time' => time()
        );
        $row = M('rerebate')->where(array('id' => $id))->save($arr);
        if ($row) {
            $this->ajaxReturn('1', 'JSON');
        } else {
            $this->ajaxReturn('2', 'JSON');
        }
    }
    
    
    //提交返利截图改变状态
    public function fukuan_rec() {
//        $this->ajaxReturn(I(), 'JSON');
//        return;
        
        $id = I('post.id');
        $arr = array(
            'img' => I('post.imgval'),
            'status' => 1,
            'time' => time()
        );
        $row = M('recommend_rebate')->where(array('id' => $id))->save($arr);
        if ($row) {
            $this->ajaxReturn('1', 'JSON');
        } else {
            $this->ajaxReturn('2', 'JSON');
        }
    }
    

    //确认收取返利改变状态
    public function shoukuan() {
        $id = I('post.id');
        if (I('post.pid') == 1) {
            $data['state'] = 2;
            $row = M('Rerebate')->where(array('id' => $id))->save($data);
        } else {
            $data['status'] = 2;
            $row = M('Recommend_rebate')->where(array('id' => $id))->save($data);
        }
        
        if ($row) {
            $this->ajaxReturn('1', 'JSON');
        } else {
            $this->ajaxReturn('2', 'JSON');
        }
    }
    
    
//    //确认支付返利
//    public function rebate_pay_aduit(){
//        $id = I('post.id');
//        $type = I('type');
//        $pass = I('pass');
//        
//        
//        import('Lib.Action.Funds','App');
//        $Funds = new Funds();
//        
//        $return_result = [
//            'code'  =>  2,
//            'msg'   =>  '参数错误！',
//        ];
//        
//        if( $type == 'rerebate' && $pass == 1 ){
//            $rebate_info = M('Rerebate')->where(array('id' => $id,'state'=>0))->find();
//            
//            if( empty($rebate_info) ){
//                $return_result = [
//                    'code'  =>  3,
//                    'msg'   =>  '查无此返利数据！',
//                ];
//                $this->ajaxReturn($return_result, 'JSON');return;
//            }
//
//            $user_id = $rebate_info['user_id'];
//            $money = $rebate_info['money'];
//
//            $return_result = $Funds->rebate_aduit_recharge($user_id,$money);
//            
//            if( $return_result['code'] == 1 ){
//                $arr = array(
//                    'img' => I('post.imgval'),
//                    'state' => 1,
//                    'time' => time()
//                );
//                $row = M('rerebate')->where(array('id' => $id))->save($arr);
//                
//                if( !$row ){
//                    $return_result = [
//                        'code'  =>  3,
//                        'msg'   =>  '返利审核失败，请重试！',
//                    ];
//                }else{
//                    $return_result = [
//                        'code'  =>  1,
//                        'msg'   =>  '返利审核成功！',
//                    ];
//                }
//            }
//        }
//        
//        $this->ajaxReturn($return_result, 'JSON');
//    }//end func rebate_pay_aduit
    

}

?>