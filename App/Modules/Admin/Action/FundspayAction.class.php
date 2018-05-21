<?php 

//轮盘抽奖支付
class FundspayAction extends CommonAction {
    
    public function __construct() {
        parent::__construct();
    }
    
    
    //支付页面
    public function pay() {
        $model = M('money_apply');
        $money_apply_id=trim(I('money_apply_id'));
        //查询出需要线上支付的金额
        $condition_check=[
          'id'=>$money_apply_id,
        ];
        $apply_money_info=$model->where($condition_check)->find();

        $dis_info=M('distributor')->where(['id'=>$this->uid])->find();

        //判断订单是否符合支付要求
        $this->checkOrder($apply_money_info,$dis_info);

        //获取支付者
        $apply_money_info['openid'] = $dis_info['openid'];

        //调用统一支付接口获取支付参数
        $return = $this->unifiedOrder($apply_money_info);

        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($return);


        $this->jsApiParameters = $jsApiParameters;
        $this->apply_money_info = $apply_money_info;

        $this->return_url = $this->get_base_url(I('return_url'));
        $this->base_return_url = I('return_url');
        $this->display();
    }
    
    //判断订单是否符合支付要求
    private function checkOrder($apply_money_info,$dis_info) {
        if (!$dis_info) {
            error_tip('未找到用户信息');
        }
        if (!$apply_money_info) {
            error_tip('未找到相关充值信息');
        }

        if ($apply_money_info['status'] > 0) {
            error_tip('此订单已支付');
        }

        if ($apply_money_info['apply_money'] <= 0 || !is_numeric($apply_money_info['apply_money'])) {
            error_tip('充值金额必须大于0');
        }
    }
    

    //统一下单接口
    private function unifiedOrder($apply_money_info) {
        import('Lib.Action.wxPay.WxPayJsApiPay','App');
        $input = new WxPayUnifiedOrder();
        $input->SetBody($apply_money_info['id']);
        $input->SetAttach('apply');
        $input->SetOut_trade_no($apply_money_info['id']);
        $input->SetTotal_fee($apply_money_info['apply_money'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
//        $input->SetGoods_tag("test");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($apply_money_info['openid']);
        $input->SetSpbill_create_ip('192.168.1.1');
        $result = WxPayApi::unifiedOrder($input);
        if ($result['return_code'] !== 'SUCCESS') {
            //跳转错误页面提示
            setLog('统一下单支付错误返回(资金充值):'.json_encode($result), 'wxpay');
            error_tip($result['return_msg']);
        }
        return $result;
    }



}

 ?>