<?php 

//轮盘抽奖支付
class IntegralpayAction extends TemCommonAction {
    
    public function __construct() {
        parent::__construct();
    }
    
    
    //支付页面
    public function pay() {
        $model = M('integralorder');
        
        $order_num = I('get.order_num');
        $where = [
            'order_num' => trim($order_num),
            'status' => 1,
        ];
        $order = $model->where($where)->find();
        
//        print_r($order);return;
        
        //判断订单是否符合支付要求
        $this->checkOrder($order,$order_num);
        
        
        //获取支付者
        $order['openid'] = $this->openid;
        
        if( $order['total_price'] != 0 ){
            //调用统一支付接口获取支付参数
            $return = $this->unifiedOrder($order);
            
            $tools = new JsApiPay();
            $jsApiParameters = $tools->GetJsApiParameters($return);
        }
        else{
            import('Lib.Action.Integralorder', 'App');
            $Integralorder = new Integralorder();

            $order_audit_result = $Integralorder->admin_audit($order_num);
            
            if( $order_audit_result['code'] != 1 ){
                error_tip($order_audit_result['msg']);
            }
            else{
                echo "<script>alert('积分支付成功！');window.location.href='".__APP__."/Sale/integralorder/all?part=1'; </script>";
                return;
            }
        }
        
        
        $this->jsApiParameters = $jsApiParameters;
        $this->order = $order;
        $this->display();
    }
    
    //判断订单是否符合支付要求
    private function checkOrder($order,$order_num) {
        if (!$order) {
            error_tip('未找到相关订单-'.$order_num);
        }
        if ($order['status'] > 1) {
            error_tip('此订单已支付');
        }
        if( $order['total_integral'] > 0 ){
            import('Lib.Action.Integral','App');
            $Integral = new Integral();
            $check_money = $Integral->check_enough_integral($order['user_id'],$order['total_integral'],TRUE);

            if( !$check_money ){
                error_tip('请检查您的积分余额以及未付款的订单！');
            }
        }
        if ($order['total_price'] < 0 || !is_numeric($order['total_price'])) {
            error_tip('订单金额必须大于0');
        }
        
    }
    

    //统一下单接口
    private function unifiedOrder($order) {
        import('Lib.Action.wxPay.WxPayJsApiPay','App');
        $input = new WxPayUnifiedOrder();
        $input->SetBody($order['order_num']);
        $input->SetAttach('Integral');
        $input->SetOut_trade_no($order['order_num']);
        $input->SetTotal_fee($order['total_price'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
//        $input->SetGoods_tag("test");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($order['openid']);
        $input->SetSpbill_create_ip('192.168.1.1');
        $result = WxPayApi::unifiedOrder($input);
        if ($result['return_code'] !== 'SUCCESS') {
            //跳转错误页面提示
            setLog('统一下单支付错误返回(积分商城):'.json_encode($result), 'wxpay');
            error_tip($result['return_msg']);
        }
        return $result;
    }
}

 ?>