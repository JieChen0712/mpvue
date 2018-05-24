<?php

/**
 * 调用微信/小程序接口api
 */
class UserAction extends CommonAction {
    private $wxapi_obj;
    private $store_obj;
    public function _initialize() {
        import('Lib.Action.Wxapi', 'App');
        import('Lib.Action.Store', 'App');
        $this->store_obj = new Store();
        $store = $this->store_obj->get_store([], ['id' => $this->store_id]);//获取店铺信息
        $appid = $store['$list'][0]['appid'];
        $appsecret = $store['$list'][0]['appsecret'];
        
        $this->wxapi_obj = new Wxapi($appid, $appsecret);
    }
    
    //用户登陆/注册
    public function login()
    {
        if( !IS_AJAX ){
            return FALSE;
        }
        //请求接口参数
        $request_data = [
            'store_id' => $this->store_id,
            'code' => trim(I('code')),//微信code
            'nickname' => trim(I('nickname')),
            'headimgurl' => trim(I('headimgurl')),
            'sex' => trim(I('sex')),
            'province' => trim(I('province')),
            'city' => trim(I('city')),
            
        ];
        //获取微信openid
        $wx_result = $this->wxapi_obj->get_openid($request_data['code']);
        if ($wx_result['code'] != 1) {
            $this->ajaxReturn($wx_result);
        }
        //成功获取opend则判断有没有注册
        
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
        if( !IS_AJAX ){
            return FALSE;
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
            return FALSE;
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