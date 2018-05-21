<?php

/**
 *    微斯咖经销商后台——首页
 */
class MallorderAction extends TemCommonAction
{

    private $model;
    private $templet_model;
    private $templet_cat_model;
    private $cart_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = M('mall_order');
        $this->templet_model = M('mall_templet');
        $this->templet_cat_model = M('mall_templet_category');
        $this->cart_model = M('mall_order_shopping_cart');
    }

    //下单 new
    public function index()
    {
        $where['status'] = '1';
        $id = I('id');
        if ($id) {
            $where['category_id'] = $id;
        }
        $condition_product=[
            'status' => 1,
            'statu' =>1,
        ];
        $condition_cat=[
            'statu' => 1,
            'pid'=> 0,
        ];

        $products = $this->templet_model->where($condition_product)->select();

        $catss=$this->templet_cat_model->where($condition_cat)->select();

        $this->products = $products;
        $this->catss =$catss;
        $this->display();

    }

    //商品详情 new
    public function goods_detail()
    {

        $product = $this->templet_model->where(['active' => '1'])->find(I('id'));

        $this->product = $product;

        $this->display();
    }

    //提交订单详情 new
    public function buy_detail()
    {
        $product = $this->templet_model->where(['active' => '1'])->find(I('id'));
        $this->product = $product;
        $this->num = I('num');
        $this->total_money = bcmul($product['price'], I('num'), 2);
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->return_url = $this->base_url(__SELF__);
        $this->display();
    }


    //购物车提交订单详情 new
    public function buy_cart_detail()
    {
        $total_money = 0;
        $cart_ids = explode('|', I('cart_ids'));
        $carts = $this->get_shopping_cart(['id' => ['in', $cart_ids]]);
        foreach ($carts as $cart) {
            $total_money += bcmul($cart['product']['price'], $cart['num'], 2);
        }
        $this->cart_ids = I('cart_ids');
        $this->carts = $carts;
        $this->total_money = $total_money;
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->return_url = $this->base_url(__SELF__);
        $this->display();
    }

    //添加到购物车
    public function add_shopping_cart()
    {
        $data = [
            'uid' => $this->uid,
            'tid' => I('id'),
            'num' => I('num'),
            'created' => time()
        ];
        if ($this->cart_model->add($data)) {
            $this->ajaxReturn(['code' => 1, 'msg' => '添加到购物车成功'], 'json');
        } else {
            $this->ajaxReturn(['code' => -1, 'msg' => '添加到购物车失败'], 'json');
        }

    }

    //我的购物车
    public function shopping_cart()
    {
        $total_money = 0;
        $carts = $this->get_shopping_cart(['uid' => $this->uid]);
        foreach ($carts as $cart) {
            $total_money += bcmul($cart['product']['price'], $cart['num'], 2);
        }
        $this->carts = $carts;
        $this->total_money = $total_money;
        $this->display();
    }

    //获取购物车
    private function get_shopping_cart($where)
    {
        $tid = [];
        $temp_products = [];
        $price = 'price';
        $carts = $this->cart_model->where($where)->select();
        if ($carts) {
            foreach ($carts as $cart) {
                $tid[] = $cart['tid'];
            }
            array_unique($tid);
            //获取产品
            $products = $this->templet_model->where(['id' => ['in', $tid]])->select();
            foreach ($products as $key => $product) {
                $product['price'] = $product[$price];
                $temp_products[$product['id']] = $product;
            }

            foreach ($carts as $key => $cart) {
                $carts[$key]['product'] = $temp_products[$cart['tid']];
            }
        }
        return $carts;
    }


    /**
     * 获取退款金额
     *
     * @param int $level
     * @param decimal $recharge_money
     * @return decimal
     */
    private function get_min_refund_money($level, $recharge_money)
    {

        if ($level == 0) {

        }

        if ($recharge_money == 0 || $level == 0 || is_null($level)) {
            return 0;
        }

        $money_min_refund = M('money_min_refund');
        $set_info = $money_min_refund->where(array('id' => '1'))->find();

        $min_refund_key = 'level' . $level;
        $min_refund = isset($set_info[$min_refund_key]) ? $set_info[$min_refund_key] : 0;


        $refund_money = bcsub($recharge_money, $min_refund, 2);

        if ($refund_money < 0) {
            $refund_money = 0;
        }


        return $refund_money;
    }


    //订单详情
    public function ddxq()
    {
//        $where['order_num'] = I('order_num');
//        $row = M('Order')->where($where)->group('order_num')->find();
//        //把产品分别显示
//        $rol = M('Order')->where($where)->select();
//        foreach ($rol as $k => $v) {
//            $product = M('templet')->where(array('id' => $v['p_id']))->find();
//            $rol[$k]['p_name'] = $product['name'];
//        }
//        $this->row = $row;
//        $this->assign('rol', $rol);
//        $this->display();


        $ordernum = I('order_num');

        $orderObj = M('mall_order');
        $templetObj = M('mall_templet');
        $distributorObj = M('distributor');

        $shipper = AllShipperCode();

        $res = $orderObj->where(array('order_num' => $ordernum))->select();
        foreach ($res as $key => $value) {
            $pid = $value['p_id'];
            $userid = $value['user_id'];
            $v_ordernumber = $value['ordernumber'];
            $v_ordernumber_arr = !empty($v_ordernumber) ? explode(',', $v_ordernumber) : [];

            $arr = $value;
            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();

            $value_shipper = $value['shipper'];
            $arr['shipper_name'] = isset($shipper[$value_shipper]) ? $shipper[$value_shipper] : '未选择快递公司';
            $arr['ordernumber_arr'] = $v_ordernumber_arr;

            $orderInfo[] = $arr;
        }

        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();

        //多个快递单号
        $express_no = explode(',', $orderInfo[0]['ordernumber']);
        $express_count = count($express_no);
        $this->express_no = $express_no;
        $this->express_count = $express_count;

        $this->status_name = $Order->status_name;
        $this->orderInfo = $orderInfo;
        $this->shipper = $shipper;
        $this->display();
    }//end func ddxq


    //我的订单和下单功能
    public function my_dd()
    {
        //统计产品数量
        $price = "price" . $this->manager['level'];

        $condition_templet['active'] = '1';
        $list = $this->templet_model->where($condition_templet)->select();
        $a = 1;
        $d = 1;
        foreach ($list as $k => $v) {
            $list[$k]['price'] = $v[$price];
            $list[$k]['id_num'] = $a;
            $a++;
        }
        $manager = $this->manager;
        $manager['count'] = count($list);
        $this->manager = $manager;
        $whr['user_id'] = $this->manager['id'];
        //查询收货地址
        $rel = M('receiving')->where($whr)->find();
        //查询已下的订单
        $row = $this->model->where($whr)->order('time desc')->group('order_num')->select();
        foreach ($row as $b => $c) {
            $row[$b]['or_num'] = $d;
            $d++;
        }


        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();

        $this->status_name = $Order->status_name;
        $this->rel = $rel;
        $this->assign('row', $row);
        $this->assign('list', $list);
        $this->display();
    }


    public function my_dhj()
    {
        $price = "price" . $_SESSION['level'];
        $count = M('mall_templet')->count("id");
        $list = M('mall_templet')->field('id,name,image,disc,state,' . $price)->select();
        foreach ($list as $k => $v) {
            $list[$k]['price'] = $v[$price];
        }
        $manager = $this->manager;
        $manager['count'] = $count;
        $this->manager = $manager;
        $this->assign('list', $list);
        $this->display();
    }

//    public function wsh() {
//        $orderObj = M('Order');
//        $templetObj = M('Templet');
//        $distributorObj = M('distributor');
//        $ordernum = $_GET['order'];
//        $res = $orderObj->where(array('order_num' => $ordernum))->select();
//        foreach ($res as $key => $value) {
//            $pid = $value['p_id'];
//            $userid = $value['user_id'];
//            $arr = $value;
//            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
//            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();
//            $orderInfo[] = $arr;
//        }
//        $this->orderInfo = $orderInfo;
//        $this->display();
//    }

    //下级订单
    public function xjdd()
    {
        $orderObj = M('mall_order');
        $distributor = M('distributor');
        $uid = $this->uid;
        $where['status'] = array('eq', 2);
        $where['o_id'] = $uid;
        $map['status'] = array('eq', 3);
        $map['o_id'] = $uid;
        $a['_complex'] = $where;
        $condition[] = $a;
        $condition['_logic'] = 'OR';
        $condition['_complex'] = $map;
        $res_not_count = $orderObj->where(array('o_id' => $uid, 'status' => 1))->count('distinct order_num');
        $res_yes_count = $orderObj->where($condition)->count('distinct order_num');
        $res_no = $orderObj->where(array('o_id' => $uid, 'status' => 1))->group('order_num')->select();
        $res_ye = $orderObj->where($condition)->group('order_num')->select();
        foreach ($res_no as $key => $value) {
            $userid = $value['user_id'];
            $arr = $value;
            $arr['uInfo'] = $distributor->where("id={$userid}")->find();
            $res_not[] = $arr;
        }
        foreach ($res_ye as $key => $value) {
            $userid = $value['user_id'];
            $arr2 = $value;
            $arr2['uInfo'] = $distributor->where("id={$userid}")->find();
            $res_yes[] = $arr2;
        }
        $this->res_yes_count = $res_yes_count;
        $this->res_not_count = $res_not_count;
        $this->res_count = $res_not_count + $res_yes_count;
        $this->res_not_total = $res_not_total;
        $this->res_yes_total = $res_yes_total;
        $this->res_not = $res_not;
        $this->res_yes = $res_yes;
        $this->display();
    }

    public function cxxjdd()
    {
        $orderObj = M('mall_order');
        $distributorObj = M('distributor');
        $row = I('post.s_order_num');
        $where['order_num'] = $row;
        $res = $orderObj->where($where)->group('order_num')->find();
        if (!empty($res)) {
            $res['uInfo'] = $distributorObj->where(array('id' => $res['user_id']))->find();

            $this->ajaxReturn($res, 'json');
            exit();
        }
        $this->ajaxReturn('none', 'json');
    }

    //
    public function cldd()
    {
        $order_num = I('order_num');

        // $mids = I('mids');
        // $mids = substr($mids, 1);
        // $order_nums = explode('_', $mids);

        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();

        $order_audit_result = $Order->admin_audit($order_num);


        $this->ajaxReturn($order_audit_result, 'json');
        return;


        if ($order_audit_result['code'] == 1) {
            $this->ajaxReturn(TRUE, 'json');
        } else {
            $this->ajaxReturn(FALSE, 'json');
        }
    }//end func cldd

    public function xjjg()
    {
        $distributor = M('distributor');
        $list = $distributor->where(array('pid' => $this->manager['id']))->select();
        //统计下级人数
        $count = count($list);
        $manager = $this->manager;
        $manager['count'] = $count;
        $this->manager = $manager;
        $this->assign('list', $list);
        $this->display();
    }

    public function xjjg_xq()
    {
        $where['id'] = I('id');
        $manager = M('distributor')->field('id,name,level,levname,headimgurl')->where($where)->find();
        $price = "price" . $manager['level'];
        $list = M('mall_templet')->field('id,name,image,disc,state,' . $price)->select();
        foreach ($list as $k => $v) {
            $list[$k]['price'] = $v[$price];
        }
        $this->manager = $manager;
        $this->assign('list', $list);
        $this->display();
    }

//    public function ysh() {
//        $orderObj = M('Order');
//        $templetObj = M('Templet');
//        $distributorObj = M('distributor');
//        $ordernum = $_GET['order'];
//        $res = $orderObj->where(array('order_num' => $ordernum))->select();
//        foreach ($res as $key => $value) {
//            $pid = $value['p_id'];
//            $userid = $value['user_id'];
//            $arr = $value;
//            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
//            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();
//            $orderInfo[] = $arr;
//        }
//        $this->orderInfo = $orderInfo;
//        $this->display();
//    }


    //订单详细
    public function detail()
    {
        $orderObj = M('mall_order');
        $templetObj = M('mall_templet');
        $distributorObj = M('distributor');
        $ordernum = I('order');

        $res = $orderObj->where(array('order_num' => $ordernum))->select();
        foreach ($res as $key => $value) {
            $pid = $value['p_id'];
            $userid = $value['user_id'];
            $arr = $value;
            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();
            $orderInfo[] = $arr;
        }

        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();

        $this->status_name = $Order->status_name;
        $this->orderInfo = $orderInfo;
        $this->display();
    }//end func detail


    //添加和修改收货地址
    public function recehand()
    {
        $id = I('post.id');
        $name = I('post.name');
        $phone = I('post.phone');
        $addre = I('post.addre');
        $receiving = M('receiving');
        $where['user_id'] = $id;
        $rel = M('receiving')->where($where)->find();
        //判断地址存不存在
        if ($rel) {
            $arr = array(
                'name' => $name,
                'phone' => $phone,
                'addre' => $addre
            );
            $rew = $receiving->where($where)->save($arr);
            $this->ajaxReturn('1', 'JSON');
        } else {
            $arr = array(
                'user_id' => $id,
                'name' => $name,
                'phone' => $phone,
                'addre' => $addre
            );
            $rew = $receiving->add($arr);
            $this->ajaxReturn($rew, 'JSON');
        }
    }

    //订单写入order表
    public function orderhand()
    {

        //$total_price = I('post.money');
        $order_num = I('post.order_num');
        $p_ids = I('post.p_ids');
        $p_nums = I('post.p_nums');

        $cart_ids = I('post.cart_ids');

        import('Lib.Action.Mallorder', 'App');

        $Order = new Mallorder();

        $write_info = array(

            'order_num' => $order_num,
            'p_ids' => $p_ids,
            'p_nums' => $p_nums,
            'cart_ids' => $cart_ids,

        );

        $return_result = $Order->write_order($this->uid, $write_info);

        $this->ajaxReturn($return_result, 'json');
    }


    //删除订单
    public function delorder()
    {
        $order_num = I('post.id');


        import('Lib.Action.Mallorder', 'App');
        $order = new Order();
        $result = $order->delorder($order_num);

        $this->ajaxReturn($result, 'JSON');
        return;

        if ($result['code'] == 1) {
            $this->ajaxReturn(TRUE, 'JSON');
        } else {
            $this->ajaxReturn(FALSE, 'JSON');
        }

//        $condition = array(
//            'order_num' => $order_num,
//            'status' => 1,
//        );
//        $order = M('Order')->where($condition)->find();
//        $del = M('Order')->where($condition)->delete();
//
//        if ($del && $order['o_id']) {
//            //取消订单模板消息
//            import('Lib.Action.Message', 'App');
//            $message = new Message();
//            $openid = M('distributor')->where(['id' => $order['o_id']])->getField('openid');
//            $message->push(trim($openid), $order, $message->order_cancle);
//        }
//        $this->ajaxReturn($del, 'JSON');
    }

    //确认收货
    public function shouhuo()
    {
        $order_num = I('post.order_num');
        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();
//        $condition = array(
//            'status' => 2,
//            'order_num' => $order_num,
//            'user_id' => $this->uid,
//        );
//
//        $res = $this->model->where($condition)->save(['status' => 3]);

        $result = $Order->confirm_order($order_num);

        /**
         * TODO:
         * 改为ajax直接返回$result
         */
        if ($result['code'] != 1) {
            $this->ajaxReturn(FALSE, 'JSON');
        }

        $this->ajaxReturn(TRUE, 'JSON');
    }//end func shouhuo


    //快递单号填写
    public function ordernb()
    {
        if (!$this->isPost()) {
            return false;
        }

        $order_num = I('post.order_num');
        $ordernumber = I('post.ordernumber');
        $shipper = I('shipper');

        $o_id = $this->uid;

        $condition = array(
            'o_id' => $o_id,
            'order_num' => $order_num,
        );

        $update_info = array(
            'shipper' => $shipper,
            'ordernumber' => $ordernumber
        );
        $save = M('mall_order')->where($condition)->save($update_info);


        if ($save) {
            $result = array(
                'code' => 1,
                'msg' => '填写成功！',
            );
        } else {
            $result = array(
                'code' => 2,
                'msg' => '填写失败，请重试！',
            );
        }

        $this->ajaxReturn($result, 'JSON');
    }


    //下单订单明细

    public function all()
    {
        $order = M('mall_order');
        $count = $order->where(array('user_id' => $this->uid))->count('distinct order_num');

        if ($count > 0) {
            $applyList = $order->where(array('user_id' => $this->uid))->order('time desc')->group('order_num')->select();

            $all_order_info = $order->field('order_num,p_id,p_name,p_image,num,price')->where(array('user_id' => $this->uid))->select();
            $all_order_key_info = array();
            foreach ($all_order_info as $k_ao => $v_ao) {
                $v_ao_order_num = $v_ao['order_num'];
                $all_order_key_info[$v_ao_order_num][] = $v_ao;
            }

            foreach ($applyList as $k => $v) {
                $v_order_num = $v['order_num'];
                $v_ordernumber = $v['ordernumber'];
                $v_ordernumber_arr = !empty($v_ordernumber) ? explode(',', $v_ordernumber) : [];
                $applyList[$k]['ordernumber_arr'] = $v_ordernumber_arr;
                $the_order_info = isset($all_order_key_info[$v_order_num]) ? $all_order_key_info[$v_order_num] : array();
                $applyList[$k]['row'] = $the_order_info;
            }
            //联表查询
            $distributor_info = [];
            //将id取出来
            foreach ($applyList as $v) {
                if (!isset($ids[$v['user_id']])) {
                    $ids[$v['user_id']] = $v['user_id'];
                }
            }
            //将取出来的id在另外的表根据id查询
            $cats = M('distributor')->where(['id' => ['in', $ids]])->select();

            //取出数据
            foreach ($cats as $v) {
                $distributor_info[$v['id']] = $v;
            }

            foreach ($applyList as $k => $v) {
                $applyList[$k]['distributor_headimgurl'] = $distributor_info[$v['user_id']]['headimgurl'];
                $applyList[$k]['distributor_name'] = $distributor_info[$v['user_id']]['name'];
            }

            $this->assign('applyList', $applyList);
        }
        $this->display();
    }

    //下单和审核的订单明细ajax

    public function get_all()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $type = trim(I('type'));
        $page_num = trim(I('page_num'));
        $status = trim(I('status'));
        $page_list_num = trim(I('page_list_num'));

//         $status='1';
//         $page_num = 1;
//        $type = 'take';

        import('Lib.Action.Mallorder', 'App');
        $Order = new Mallorder();

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($type)) {
            $return_result = [
                'code' => 3,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        if( empty($type) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }


        $other['is_group'] = 1;
        if ($type == 'take') {
            if ($status != null) {
                $condition = [
                    'user_id' => $this->uid,
                    'status' => $status,
                ];
            } else {
                $condition = [
                    'user_id' => $this->uid,
                ];
            }
            $info = $Order->get_order($page_info, $condition, $other);
        } elseif ($type == 'shipping') {
            if ($status != null) {
                $condition = [
                    'o_id' => $this->uid,
                    'status' => $status,
                ];
            } else {
                $condition = [
                    'o_id' => $this->uid,
                ];
            }
            $info = $Order->get_order($page_info, $condition, $other);
        }

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
            'status' => $status,
        ];
        $this->ajaxReturn($return_result);


    }

    //删除已收货订单
    public function del_order()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');
        $condition = array(
            'status' => 3,
            'order_num' => $order_num,
            'user_id' => $this->uid,
        );
        $del = $this->model->where($condition)->delete();

        $this->ajaxReturn($del, 'JSON');
    }

    //根据user_id取消订单
    public function cancel_order()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');

        import('Lib.Action.Mallorder', 'App');
        $order = new Order();
        $result = $order->delorder($order_num);

        $this->ajaxReturn($result, 'JSON');
        return;
//        $condition = array(
//            'status' => 1,
//            'order_num' => $order_num,
//            'user_id' => $this->uid,
//        );
//        $del = M('Order')->where($condition)->delete();
//
//        $this->ajaxReturn($del, 'JSON');

    }

    //审核订单模块
    public function examine()
    {
        $order = M('mall_order');

        $count = $order->where(array('o_id' => $this->uid))->count('distinct order_num');

        if ($count > 0) {
            $applyList = $order->where(array('o_id' => $this->uid))->order('time desc')->group('order_num')->select();

            $all_order_info = $order->field('order_num,p_id,p_name,p_image,num,price')->where(array('o_id' => $this->uid))->select();

            $all_order_key_info = array();
            foreach ($all_order_info as $k_ao => $v_ao) {
                $v_ao_order_num = $v_ao['order_num'];
                $all_order_key_info[$v_ao_order_num][] = $v_ao;
            }

            foreach ($applyList as $k => $v) {
                $v_order_num = $v['order_num'];
                $v_ordernumber = $v['ordernumber'];
                $v_ordernumber_arr = !empty($v_ordernumber) ? explode(',', $v_ordernumber) : [];
                $applyList[$k]['ordernumber_arr'] = $v_ordernumber_arr;
                $the_order_info = isset($all_order_key_info[$v_order_num]) ? $all_order_key_info[$v_order_num] : array();
                $applyList[$k]['row'] = $the_order_info;
            }
            //联表查询
            $distributor_info = [];
            //将id取出来
            foreach ($applyList as $v) {
                if (!isset($ids[$v['user_id']])) {
                    $ids[$v['user_id']] = $v['user_id'];
                }
            }

            //将取出来的id在另外的表根据id查询
            $cats = M('distributor')->where(['id' => ['in', $ids]])->select();

            //取出数据
            foreach ($cats as $v) {
                $distributor_info[$v['id']] = $v;
            }
            foreach ($applyList as $k => $v) {
                $applyList[$k]['distributor_headimgurl'] = $distributor_info[$v['user_id']]['headimgurl'];
                $applyList[$k]['distributor_name'] = $distributor_info[$v['user_id']]['name'];
            }

            $this->assign('applyList', $applyList);
        }
        $this->display();
    }

    //审核订单
    public function firm_order()
    {
        $order_num = I('post.order_num');
        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();
        $condition = array(
            'status' => 1,
            'order_num' => $order_num,
            'o_id' => $this->uid,
        );

        $res = $this->model->where($condition)->save(['status' => 2]);
        $this->ajaxReturn($res, 'JSON');

    }

    //根据o_id取消订单
    public function cancel_order_oid()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');

        import('Lib.Action.Mallorder', 'App');
        $order = new Order();
        $result = $order->delorder($order_num, $this->uid);

        $this->ajaxReturn($result, 'JSON');
        return;


//        $condition = array(
//            'status' => 1,
//            'order_num' => $order_num,
//            'o_id' => $this->uid,
//        );
//        setLog(json_encode($condition));
//        $del = M('Order')->where($condition)->delete();
//
//        $this->ajaxReturn($del, 'JSON');
    }

    //查看审核订单详情页面
    public function shipping_order_detail()
    {

        $order_num = I('get.order_number');

        $condition = array(
            'order_num' => $order_num,
        );
        $condition_order = $this->model->where($condition)->find();
        $condition_info = $this->model->where($condition)->select();
        $uid = $condition_order['user_id'];
        $distributor_info = M('distributor')->where(array('id' => $uid))->find();
        $this->assign('condition_order', $condition_order);
        $this->assign('condition_info', $condition_info);
        $this->assign('distributor_info', $distributor_info);

        $this->display();
    }

    //查看下单订单详情
    public function take_order_detail()
    {
        $order_num = I('get.order_number');

        $condition = array(
            'order_num' => $order_num,
        );
        $condition_order = $this->model->where($condition)->find();
        $condition_info = $this->model->where($condition)->select();
        $uid = $condition_order['user_id'];
        $distributor_info = M('distributor')->where(array('id' => $uid))->find();
        $this->assign('condition_order', $condition_order);
        $this->assign('condition_info', $condition_info);
        $this->assign('distributor_info', $distributor_info);
        $this->display();
    }

    //查看下单订单详情页面ajax
    public function get_take_order_detail()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = trim(I('get.order_number'));
        //$order_num='321501223177436';
        if (empty($order_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '订单号获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        $condition = [
            'order_num' => $order_num,
        ];
        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();
        $info = $Order->get_sao_order([], $condition);

        $this->ajaxReturn($info, 'JSON');

    }


    //获取产品信息ajax
    public function get_templet()
    {
        if (!IS_AJAX) {
           return FALSE;
        }

        $name = trim(I('get.name'));
        $page_num = trim(I('page_num'));
        $category = trim(I('post.categpry'));
        $page_list_num = trim(I('page_list_num'));


        //$name='水';
        $page_num=1;
        $category='';

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();

        //每页默认为10
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $price = "price" . $this->manager['level'];


        if(!empty($category)){
            $condition=[
                'category_id' => $category,
                'active' => '1',
            ];
            $info = $Order->get_templet($page_info, $condition,$price);
        }else{
            if(!empty($name)){
                $condition['name']  = array('like',"%$name%");
                $condition['active'] = '1';
            }else{
                $condition=[
                    'active' => '1',
                ];
            }
            $info = $Order->get_templet($page_info, $condition,$price);
        }

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];
        $this->ajaxReturn($return_result);
    }

    //产品分类
    public function templet_category()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

        // $page_num=1;
        import('Lib.Action.Mallorder', 'App');
        $Order = new Order();

        //每页默认为10
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        $condition = [];
        $result = $Order->get_templet_category($page_info, $condition);

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);

    }




    //---------------********商品分类********----------------


    public function apply_return(){

        $this->display();
    }
    public function brokerage(){

        $this->display();
    }
    public function brokerage_detail(){

        $this->display();
    }
    public function brokerage_message(){

        $this->display();
    }
    public function commodity(){
        $where['status'] = '1';
        $id = I('id');
        if ($id) {
            $where['category_id'] = $id;
        }
        $products = $this->templet_model->where($where)->select();
        $catss=$this->templet_cat_model->select();

       $condition['pid'] = array('gt',0);
        $condition['statu']=0;
        $cateres=$this->templet_cat_model->where($condition)->select();

        $this->products = $products;
        $this->catss =$catss;
        $this->cateres = $cateres;

        $this->display();
    }

    public function goods_kind(){
        $category_id=I('get.id');
        $condition=[
            'category_id' =>$category_id,
            'status' => 1,
            'statu' => 0,
        ];
        $category_info=$this->templet_model->where($condition)->select();

       $this->category_info = $category_info;

        $this->display();
    }

    public function center(){
        //营业额
        $this->business_money = M('mall_money_log')->where(['uid' => $this->uid])->sum('order_money');
        //佣金
        $this->funds = M('mall_money_funds')->where(['uid' => $this->uid])->find();
        $this->display();
    }
    
    //我的佣金
    public function bonus() {
        $this->funds = M('mall_money_funds')->where(['uid' => $this->uid])->find();
        $this->display();
    }
    
    //提现申请
    public function bonus_withdraw() {
        $card_id = I('card_id');
        $this->funds = M('mall_money_funds')->where(['uid' => $this->uid])->find();
        if ($card_id) {
            $this->bank = M('distributor_bank')->find($card_id);
        }
        $this->display();
    }
    
    //我的团队代理信息
    public function  my_team()
    {
        $id = $this->uid;
        $model_dirstributor = M('distributor');
        $list = $model_dirstributor->where(array('pid' => $id))->select();
        $this->recommend_info = $list;
        $this->display();
    }

    /*
     *  @param string $type
     * @param int $page_num
     */
    public function get_my_team(){
        if( !IS_AJAX ){
        return FALSE;
        }

        $type = trim(I('type'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));
        $condition['id'] = $this->uid;
        $condition['pid'] = $this->uid;
//        $condition['recommendID'] = $this->uid;

        import('Lib.Action.User','App');
        $User=new User();

       // $type='under';
       // $page_num=10;
        if( empty($type) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        if( empty($page_num) ){
        $return_result = [
            'code'  =>  3,
            'msg'   =>  '页码获取失败',
        ];
        $this->ajaxReturn($return_result);
        }
        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }
        //直属
        if( $type == 'recommend' ){
            $page_info = [
                'page_num'  =>  $page_num,
                'page_list_num' =>  $page_list_num,
            ];
            $result = $User->get_distributor($page_info,array('recommendID' => $condition['id']));

        }
        //推介
        elseif($type == 'under'){
            $page_info = [
                'page_num'  =>  $page_num,
                'page_list_num' =>  $page_list_num,
            ];
            $result = $User->get_distributor($page_info,array('pid' => $condition['id']));
        }

        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
            'id'   =>  $this->uid,

        ];
        $this->ajaxReturn($return_result);
    }
}

?>