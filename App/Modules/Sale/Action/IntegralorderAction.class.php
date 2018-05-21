<?php

/**
 *    雨丝燕经销商后台——首页
 */
class IntegralorderAction extends TemCommonAction
{

    private $model;
    private $templet_model;
    private $templet_cat_model;
    private $cart_model;
    private $advert;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = M('integralorder');
        $this->templet_model = M('integraltemplet');
        $this->templet_cat_model = M('integraltemplet_category');
        $this->cart_model = M('integralorder_shopping_cart');
        $this->adv_model = M('integral_advert');
    }
    
    //下单 new
    public function index()
    {
        $share = I('share');
        $check_share_result = $this->check_share_link($share);
        
        if( !$check_share_result ){
            $encode_share = $this->encode_share();
            $url = __APP__.'/sale/integralorder/index?share='.$encode_share;
            header('Location:'.$url);
//            var_dump($check_share_result);
            exit();
        }
        
        
        $price = "price" . $this->manager['level'];
//        $where['active'] = '1';
        $id = I('id');
        if ($id) {
            $where['category_id'] = $id;
        }
        if ($id) {
        $condition_product['category_id'] = $id;
        }
        //出现在首页的产品
        $condition_product=[
            'active' => '1',
//            'statu' =>'1',
        ];
        //出现在首页的分类
        $condition_cat=[
            'statu' => 0,
            'pid'=> 0,
            'active' => 1,
        ];

        $products = $this->templet_model->where($condition_product)->select();

        foreach ($products as $key => $product) {
            $products[$key]['price'] = $product[$price];
        }
        $catss=$this->templet_cat_model->where($condition_cat)->select();

        //广告图片
        $condition_one=[
            'type'=> 1,
            'status' => 1,
        ];

        $condition_two=[
            'type'=> 2,
            'status' => 1,
            'integraltemplet_category_id'=>0,
        ];
        $adv_info=$this->adv_model->where($condition_one)->order('sequence desc,id desc')->limit(4)->select();
        $adv_hai=$this->adv_model->where($condition_two)->order('sequence desc,id desc')->find();

        $this->products = $products;
        $this->catss =$catss;

        $this->adv_info=$adv_info;
        $this->adv_hai=$adv_hai;
        $this->display();
    }

    //商品详情 new
    public function goods_detail()
    {
        $id = I('id');
        
        $share = I('share');
        $check_share_result = $this->check_share_link($share);
        
        if( !$check_share_result ){
            $encode_share = $this->encode_share();
            $url = __APP__.'/sale/integralorder/goods_detail?id='.$id.'&share='.$encode_share;
            header('Location:'.$url);
            exit();
        }
        
        
        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where(['active' => '1'])->find(I('id'));
        $product['price'] = $product[$price];
        $product['many_image']=explode(',',$product['many_image']);
        $this->product = $product;
        $this->display();
    }
    
    //商品详情 new
    public function goods_all()
    {
        
        $share = I('share');
        $name = I('name');
        
        $check_share_result = $this->check_share_link($share);
        
        if( !$check_share_result ){
            $encode_share = $this->encode_share();
            $url = __APP__.'/sale/integralorder/goods_all?share='.$encode_share.'&name='.$name;
            header('Location:'.$url);
            exit();
        }

        $condition = [
            'active' => '1',
        ];
        
        if( !empty($name) ){
            $condition['name'] = ['like',$name];
        }
        
        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where($condition)->select();
//        $product['price'] = $product[$price];
        
        foreach( $product as $k => $v ){
            $product[$k]['price'] = $v[$price];
        }
        
        $this->product = $product;
        $this->display();
    }//end func goods_all
    

    //提交订单详情 new
    public function buy_detail()
    {
        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where(['active' => '1'])->find(I('id'));
        $product['price'] = $product[$price];
        $product['total_integral'] =bcmul($product['integral'], I('num'), 2);
        $this->product = $product;
        $this->num = I('num');
        $this->total_money = bcmul($product['price'], I('num'), 2);
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->return_url = $this->base_url(__SELF__);
        $this->display();
    }

    
     //购物车提交订单详情 new
    public function buy_cart_detail() {
        $total_money = 0;
        $total_integral=0;
        $cart_ids = explode('|', I('cart_ids'));
        $carts = $this->get_shopping_cart(['id' => ['in', $cart_ids]]);
        foreach ($carts as $cart) {
            $total_money += bcmul($cart['product']['price'],$cart['num'],2);
            $total_integral += bcmul($cart['product']['integral'],$cart['num'],2);
        }
        $this->cart_ids = I('cart_ids');
        $this->carts = $carts;
        $this->total_money = $total_money;
        $this->total_integral = $total_integral;
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->return_url = $this->base_url(__SELF__);
        $this->display();
    }

    //添加到购物车
    public function add_shopping_cart()
    {
        $order_shopping_cart_obj = M('integralorder_shopping_cart');
        $condition_count = array(
            'uid'   =>  $this->uid,
        );
        $tid = I('id');
        $num = I('num');

        $data = [
            'uid' => $this->uid,
            'tid' =>$tid,
        ];

        $old_cart = $order_shopping_cart_obj->where($data)->find();
        if( !empty($old_cart) ){
            $old_cart_num = $old_cart['num'];
            $new_cart_num = bcadd($num,$old_cart_num,0);
            $save_info = array(
                'num'   =>  $new_cart_num,
                'updated'   =>  time(),
            );
            $save_res = $order_shopping_cart_obj->where($data)->save($save_info);
        }else{
            $add_info = array(
                'uid'   => $this->uid,
                'tid'   =>  $tid,
                'num'   =>  $num,
                'created'   =>  time(),
            );
            $save_res = $order_shopping_cart_obj->add($add_info);
        }
        if ($save_res) {
            $this->ajaxReturn(['code' => 1, 'msg' => '添加到购物车成功'], 'json');
        } else {
            $this->ajaxReturn(['code' => -1, 'msg' => '添加到购物车失败'], 'json');
        }

    }
    
    //我的购物车
    public function shopping_cart() {
        $total_money = 0;
        $carts = $this->get_shopping_cart(['uid' => $this->uid]);
        foreach ($carts as $cart) {
            $total_money += bcmul($cart['product']['price'],$cart['num'],2);
        }
        $this->carts = $carts;
        $this->total_money = $total_money;
        $this->display();
    }
    
    //获取购物车
    private function get_shopping_cart($where) {
        $tid = [];
        $temp_products = [];
        $price = 'price'.$this->manager['level'];
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



    //订单详情
    public function ddxq()
    {
        $ordernum = I('order_num');

        $orderObj = M('Integralorder');
        $templetObj = $this->templet_model;
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

        import('Lib.Action.Integralorder', 'App');
        $Integralorder = new Integralorder();

        $this->status_name = $Integralorder->status_name;
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
        $row = M('Integralorder')->where($whr)->order('time desc')->group('order_num')->select();
        foreach ($row as $b => $c) {
            $row[$b]['or_num'] = $d;
            $d++;
        }


        import('Lib.Action.Integralorder', 'App');
        
        $Integralorder = new Integralorder();

        $this->status_name = $Integralorder->status_name;
        $this->rel = $rel;
        $this->assign('row', $row);
        $this->assign('list', $list);
        $this->display();
    }

    //下级订单
    public function xjdd()
    {
        $orderObj = M('Integralorder');
        $distributor = M('distributor');
        $uid = $_SESSION['managerid'];
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
        $orderObj = M('Integralorder');
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
//    public function cldd()
//    {
//        $order_num = I('order_num');
//
//        // $mids = I('mids');
//        // $mids = substr($mids, 1);
//        // $order_nums = explode('_', $mids);
//
//        import('Lib.Action.Integralorder', 'App');
//        $Integralorder = new Integralorder();
//
//        $order_audit_result = $Integralorder->admin_audit($order_num);
//
//
//        $this->ajaxReturn($order_audit_result, 'json');
//        return;
//
//
//        if ($order_audit_result['code'] == 1) {
//            $this->ajaxReturn(TRUE, 'json');
//        } else {
//            $this->ajaxReturn(FALSE, 'json');
//        }
//    }//end func cldd



    //订单详细
    public function detail()
    {
        $orderObj = M('Integralorder');
        $templetObj = $this->templet_model;
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

        import('Lib.Action.Integralorder', 'App');
        $Integralorder = new Integralorder();

        $this->status_name = $Integralorder->status_name;
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
        
        import('Lib.Action.Integralorder','App');

        $Integralorder = new Integralorder();

        $write_info = array(

            'order_num' =>  $order_num,
            'p_ids' =>  $p_ids,
            'p_nums' => $p_nums,
            'cart_ids' => $cart_ids,

        );

        $return_result = $Integralorder->write_order($_SESSION['managerid'], $write_info);

        //只有在需要支付的时候才直接跳转的支付页面
        if( $return_result['total_price'] > 0 ){
            $return_result['return_url'] = __GROUP__.'/Integralpay/pay?order_num='.$return_result['order_num'];
        }
        else{
            $return_result['return_url'] = __GROUP__.'/Integralorder/all?part=1';
        }
        
        
        $this->ajaxReturn($return_result, 'json');
    }


    //删除订单
    public function delorder()
    {
        $order_num = I('post.id');

        $condition = array(
            'order_num' => $order_num,
            'status' => 1,
        );
        $order = M('Integralorder')->where($condition)->find();
        $del = M('Integralorder')->where($condition)->delete();

        if ($del && $order['o_id']) {
            //取消订单模板消息
            import('Lib.Action.Message', 'App');
            $message = new Message();
            $openid = M('distributor')->where(['id' => $order['o_id']])->getField('openid');
            $message->push(trim($openid), $order, $message->order_cancle);
        }
        $this->ajaxReturn($del, 'JSON');
    }

    //确认收货
    public function shouhuo()
    {
        $order_num = I('post.order_num');
        import('Lib.Action.Integralorder', 'App');
        $Integralorder = new Integralorder();
        $condition = array(
            'status' => 2,
            'order_num' => $order_num,
            'user_id' => $this->uid,
        );

        $res = $this->model->where($condition)->save(['status' => 3]);
        $this->ajaxReturn($res, 'JSON');

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

        $o_id = $_SESSION['managerid'];

        $condition = array(
            'o_id' => $o_id,
            'order_num' => $order_num,
        );

        $update_info = array(
            'shipper' => $shipper,
            'ordernumber' => $ordernumber
        );
        $save = M('Integralorder')->where($condition)->save($update_info);


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


    //订单明细

    public function all(){
        $order=M('Integralorder');
        $count = $order->where(array('user_id'=>$this->uid))->count('distinct order_num');

        if ($count > 0) {
            $applyList = $order->where(array('user_id' => $this->uid))->order('time desc')->group('order_num')->select();

            $all_order_info = $order->field('order_num,p_id,p_name,p_image,num,price,total_integral,integral')->where(array('user_id' => $this->uid))->select();

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
        $del = M('Integralorder')->where($condition)->delete();

        $this->ajaxReturn($del, 'JSON');
    }
    
    //取消订单
    public function cancel_order()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');
        $condition = array(
            'status' => 1,
            'order_num' => $order_num,
            'user_id' => $this->uid,
        );
        $del = M('Integralorder')->where($condition)->delete();

        $this->ajaxReturn($del, 'JSON');
    }


    public function commodity(){
        $condition_cat=[
            'active' => 1,
            'statu' => 1,
            'pid' => 0,
        ];
        $catss=$this->templet_cat_model->where($condition_cat)->select();

        $condition['pid'] = array('gt',0);
        $condition['statu']=1;
        $cateres=$this->templet_cat_model->where($condition)->select();

        $this->catss =$catss;
        $this->cateres = $cateres;

        $this->display();
    }

    public function goods_kind(){
        $category_id=I('get.id');
        $condition_one=[
            'integraltemplet_category_id'=>$category_id,
            'active' => 1,
            'type' => 2,
        ];
        $info_advert=M('integral_advert')->where($condition_one)->order('sequence desc,id desc')->find();

        $condition=[
            'category_id' =>$category_id,
            'active' => '1',
//            'statu' => '0',
        ];
        $category_info=$this->templet_model->where($condition)->select();


        //联表查询
        $material_info = [];
        //将id取出来

        //将取出来的id在另外的表根据id查询
        $mats = M('integraltemplet_category')->where(['id' => $info_advert['integraltemplet_category_id']])->select();
        //取出数据
        foreach ($mats as $v) {
            $material_info['id'] = $v;
        }

        foreach ($info_advert as $k => $v) {
            $info_advert['integraltemplet_category_name'] = $material_info['id']['name'];
        }


        $this->info_advert = $info_advert;
        $this->category_info = $category_info;

        $this->display();
    }

    public function  classify(){
        $bus_id = $_GET['bus_id'];

        $condition_category=[
            'pid' => 0,
            'statu' => 0,
        ];

        if(empty($bus_id)){
            $last = $this->templet_cat_model->where($condition_category)->field('id')->order('id asc')->find();

            $bus_id = isset($last['id'])?$last['id']:null;
        }


        $condition_templet=[
            'active' =>1,
            'statu' => 0,
            'category_id' => $bus_id,
        ];
        $catss=$this->templet_cat_model->where($condition_category)->select();
        $products = $this->templet_model->where($condition_templet)->select();

       $this->catss=$catss;
        $this->products=$products;
        $this->display();
    }
    
    
    public function center(){
        $this->display();
    }

    //改变购物车产品数量
    public function save_cart_num() {
        $cart_model = M('integralorder_shopping_cart');
        $cart_ids = explode('|', I('cart_ids'));
        $cart_nums = explode('|', I('cart_nums'));
        foreach ($cart_ids as $k => $id) {
            $cart_model->where(['id' => $id])->save(['num' => $cart_nums[$k]]);
        }
        $this->ajaxReturn(true, 'JSON');
    }

    //删除购物车
    public function del_cart() {
        if (IS_AJAX) {
            $cart_ids = explode('|', I('cart_ids'));
            $res = M('integralorder_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();
            $this->ajaxReturn($res, 'JSON');
        }
    }

    //判断购物车里sku_id是否存在
    public function is_null_sku_id() {
        import('Lib.Action.Sku', 'App');
        $sku = new Sku();
        $tids = [];
        $sku_ids = [];
        $name = "";
        $cart_ids = explode('|', I('cart_ids'));
        $carts = $this->cart_model->where(['uid' => $this->uid, 'id' => ['in',$cart_ids]])->select();
        foreach ($carts as $cart) {
            $tids[] = $cart['tid'];
            if ($cart['sku_id']) {
                $sku_ids[] = $cart['sku_id'];
            }
        }
        //得到不存在库存id集合
        $null_sku_ids = $sku->is_null_sku_id($sku_ids);
        if ($null_sku_ids) {
            $tids = $this->cart_model->field('tid')->where(['uid' => $this->uid, 'sku_id' => ['in',$null_sku_ids]])->select();
            foreach ($tids as $id) {
                $null_tids[] = $id['tid'];
            }
            $templets = $this->templet_model->where(['id' => ['in', $null_tids]])->select();

            foreach ($templets as $v) {
                $name .= ' '. $v['name'];
            }
            $msg = "$name 产品已失效，请清除";
            $return_result = [
                'code' => -1,
                'msg' => $msg,
            ];

        } else {
            $return_result = [
                'code' => 1,
            ];
        }
        $this->ajaxReturn($return_result);
    }

    //查看下单订单详情
    public function take_order_detail()
    {
        $order_num = I('get.order_number');

        $condition = array(
            'order_num' => $order_num,
        );
        $condition_order = M('integralorder')->where($condition)->find();
        $condition_info = M('integralorder')->where($condition)->select();
        $uid = $condition_order['user_id'];
        foreach ($condition_info as $k=>$v){
            $shipping=$v['ordernumber'];
        }
        $arr = explode(',',$shipping);

        $distributor_info = M('distributor')->where(array('id' => $uid))->find();
        $this->assign('condition_order', $condition_order);
        $this->assign('condition_info', $condition_info);
        $this->assign('distributor_info', $distributor_info);
        $this->assign('arr',$arr);
        $this->display();
    }

}

?>