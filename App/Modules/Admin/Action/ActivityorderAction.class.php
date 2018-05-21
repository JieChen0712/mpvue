<?php 

class ActivityorderAction extends ActivityAction {
    
    private $model;
    private $type = 0;
    public function __construct() {
        parent::__construct();
        $this->model = M('activity_order');
    }
    
    //我的订单
    public function index() {
        $order = M('activity_order')->where(['user_id' => $this->uid, 'type' => $this->type])->select();
        $this->order =  $order;
        $this->open_wxpay = $this->activity['OPEN_WXPAY'];
        $this->display();
    }


    //活动中心产品立即购买和购物车下单处理函数
    public function submit() {
        $order_num = I('post.order_num');
        $p_ids = I('post.p_ids');
        $p_nums = I('post.p_nums');
        $notes = I('post.textarea');
        $rec_id = I('post.rec_id');
        $cart_id = I('cart_id');
//        $sku_ids = I('sku_ids');
        //代理系统外活动中心收货地址(普通客户)
        if ($this->activity['WAY'] == 0) {
            //尚未开发，收货地址待重写
            
        } else {
            $receiving_obj = M('receiving');
        }
        
        $condition = array(
            'user_id'   => $this->uid,
            'id'    =>  $rec_id,
        );
        $receiving_info = $receiving_obj->where($condition)->find();
        
        if( empty($receiving_info) ){
            $result = [
                'code'  => '22',
                'msg'   =>  '请必须填写收货信息！'
            ];
            $this->ajaxReturn($result, 'json');
            return;
        }
        
        $s_name = $receiving_info['name'];
        $s_phone = $receiving_info['phone'];
        $s_area = $receiving_info['area'];
        $s_addre = $receiving_info['addre'];
        
        //直接把省市区组合为详细地址
        $s_addre = $s_area.$s_addre;
        
        $write_info = array(
            'order_num' =>  $order_num,
            'p_ids' =>  $p_ids,
            'p_nums'    =>  $p_nums,
            'user_name' =>  $s_name,
            'addre' =>  $s_addre,
            'phone' =>  $s_phone,
            'textarea'  =>  $notes,
        );
        
        $result = $this->add($write_info);
        
        if ($cart_id) {
            //购物车下单后逻辑
        }
        
        $this->ajaxReturn($result, 'json');
    }
    
    private function add($write_info) {
        $product_model = M('activity_product');
        $model = $this->model;
        $product_ids = $write_info['p_ids'];
        $product_nums = $write_info['p_nums'];
        $total_price = 0;
        $total_num = 0;
        //计算总金额和总数量
        foreach ($product_ids as $key => $id) {
            $product = $product_model->find($id);
            $price = bcmul($product['price'], $product_nums[$key], 2);
            $total_price = bcadd($total_price,$price,2);
            $total_num += $product_nums[$key];
        }
        
        //判断金额
        if( $total_price <= 0 || !is_numeric($total_price)) {
            return [
                'code'  =>  '-3',
                'msg'   =>  '订单总金额不能小于或等于0！',
                'error_info' => $total_price,
            ];
        }
        //判断数量
        if( $total_num <= 0 || !is_numeric($total_num)) {
            return [
                'code'  =>  '-4',
                'msg'   =>  '订单总产品数量不能小于或等于0！！',
                'error_info' => $total_num,
            ];
        }
        if ($this->activity['OPEN_WXPAY']) {
            $status = 1;
        } else {
            $status = 2;
        }
        foreach ($product_ids as $key => $id) {
            $product = $product_model->find($id);
            $data = [
                'order_num' => $write_info['order_num'],  //订单号
                'user_id' => $this->uid,          //下单用户
                'product_id' => $id,        //产品ID
                'product_name' => $product['name'],    //产品名字------2016.11.9新增，ID应该废弃
                'status' => $status,              //订单状态，默认1为未审核
                's_name' => $write_info['user_name'],        //收货人名字
                's_address' => $write_info['addre'],      //收货人地址
                's_phone' => $write_info['phone'],      //收货人手机
                'notes' => $write_info['textarea'],          //订单备注
                'num' => $product_nums[$key],          //产品数量
                'price' => $product['price'],      //产品单价
                'time' => time(),           //订单生成日期
                'total_num' => $total_num,  //总数量----下单时多个产品记录为同一订单号的多条记录
                'total_price'  => $total_price,
                'paytime'   =>  0,          //支付时间-------2016.11.9近期新增，一般在审核时更新，如没虚拟币模块，可为审核时间
            ];
            $result = $model->add($data);
            if (!$result) {
                $error_info[] = $this->model->getDbError();
            }
        }
        
        if (!$result) {
             return [
                'code'  =>  '-1',
                'msg'   =>  '创建订单失败！',
                'error_info'=>  $error_info,
            ];
        }
        return [
            'code'  =>  '1',
            'msg'   =>  '创建订单成功！',
            'error_info'=>  $write_info['order_num'],
        ];
    }
	
    public function detail() {
        $order_num = I('get.order_num');
        $orders = $this->model->where(['order_num' => $order_num, 'user_id' => $this->uid])->select();
        if (!$orders) {
            //跳转到提示页面
            error_tip('订单不存在');
        }
        
        $shipper = AllShipperCode();
        $product_model = M('activity_product');
        foreach ($orders as $order) {
            $order['pInfo'] = $product_model->find($order['product_id']);
            
            $value_shipper = $order['shipper'];
            $order['shipper_name'] = isset($shipper[$value_shipper])?$shipper[$value_shipper]:'未选择快递公司';
            
            $orderInfo[] = $order;
        }
        
        $this->orderInfo = $orderInfo;
        $this->open_wxpay = $this->activity['OPEN_WXPAY'];
        $this->display();
    }
    
    //删除订单
    public function cancle() {
        $order_num = trim($_POST['order_num']);
        $res = $this->model->where(['user_id' => $this->uid, 'order_num' => $order_num])->delete();
        $this->ajaxReturn($res, 'json'); 
    }
    
    //收货
    public function receiving() {
        $order_num = trim($_POST['order_num']);
        $res = $this->model->where(['user_id' => $this->uid, 'order_num' => $order_num])->save(['status' => 5]);
        $this->ajaxReturn($res, 'json');
    }
}

 ?>