<?php

/**
 * 	优惠券api
 */
class CouponsAction extends CommonAction {

    private $coupons_model;
    private $coupons_records_model;
    private $coupons_obj;
    private $user_obj;
    public $uid;//用户id
    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.User', 'App');
        import('Lib.Action.Coupons', 'App');
        $this->coupons_obj = new Coupons();
        $this->user_obj = new User();
        $this->coupons_model = M('coupons');
        $this->coupons_records_model = M('coupons_records');
        
         //根据openid获取用户id
        $uid = $this->user_obj->get_uid_by_openid($this->openid);
        if (!$uid) {
            $return_result = [
                'code' => -5,
                'msg' => '没有获取到微信用户',
            ];
            $this->ajaxReturn($return_result);
        }
        $this->uid = $uid;
    }
    
    //获取优惠券api
    public function get_coupons()
    {
        if( !IS_GET ){
            $return_result = [
                'code' => -5,
                'msg' => '请求方式错误'
            ];
            $this->ajaxReturn($return_result);
        }
        //请求接口参数
        $request_data = [
            'store_id' => $this->store_id,//店铺id
            'openid' => $this->openid,//微信openid
            'page' => trim(I('page')),//页数
        ];
        
        $condition = [
            'store_id' => $request_data['store_id'],
            'status' => $this->coupons_obj->status_yes,
        ];
        $page_info = array(
            'page_num' => $request_data['page'],
        );
        
        //获取被领取过的优惠券
        $coupons_ids = [];//被领取过的ids
        $coupons_records = $this->coupons_records_model->where(['uid'=>$this->uid, 'store_id'=>$request_data['store_id']])->select();
        foreach ($coupons_records as $records) {
            $coupons_ids[] = $records['$coupons_id'];
        }
        $result = $this->coupons_obj->get_coupons($page_info, $condition, $coupons_ids);
        $this->ajaxReturn($result);
    }
    
    //获取优惠券api
    public function get_coupons_records()
    {
        if( !IS_GET ){
            $return_result = [
                'code' => -5,
                'msg' => '请求方式错误'
            ];
            $this->ajaxReturn($return_result);
        }
        //请求接口参数
        $request_data = [
            'store_id' => $this->store_id,//店铺id
            'openid' => $this->openid,//微信openid
            'page' => trim(I('page')),//页数
        ];
        
        $condition = [
            'uid' => $this->uid,
            'store_id' => $request_data['store_id'],
        ];
        $page_info = array(
            'page_num' => $request_data['page'],
        );
        $result = $this->coupons_obj->get_coupons_records($page_info, $condition);
        $this->ajaxReturn($result);
    }
    
    //领取优惠券
    public function add_coupons()
    {
        if( !IS_POST ){
            $return_result = [
                'code' => -5,
                'msg' => '请求方式错误'
            ];
            $this->ajaxReturn($return_result);
        }
        //请求接口参数
        $request_data = [
            'openid' => $this->openid,//微信openid
            'coupons_id' => trim(I('coupons_id')),//优惠券id
            'store_id' => $this->store_id,//店铺id
        ];
        $request_data['uid'] = $this->uid;
        $result = $this->coupons_obj->add_coupoons($request_data);
        $this->ajaxReturn($result);
    }
}

?>