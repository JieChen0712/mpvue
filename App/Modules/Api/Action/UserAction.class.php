<?php

/**
 * 调用微信/小程序接口api
 */
class UserAction extends CommonAction {
    private $wxapi_obj;
    public $store_obj;
    private $user_model;
    public function _initialize() {
        import('Lib.Action.Wxapi', 'App');
        import('Lib.Action.Store', 'App');
        $this->store_obj = new Store();
        $this->user_model = M('user');
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
            'code' => trim(I('code')),//微信code
            'nickname' => trim(I('nickname')),
            'headimgurl' => trim(I('headimgurl')),
            'province' => trim(I('province')),
            'city' => trim(I('city')),
            
        ];
        //获取微信openid
        $wx_result = $this->wxapi_obj->get_openid($request_data['code']);
        if ($wx_result['code'] != 1) {
            $this->ajaxReturn($wx_result);
        }
        $openid = $wx_result['info']['openid'];
        $request_data['openid'] = $openid;
        //成功获取opend则判断有没有注册
        $user = $this->user_model->where(['openid' => $openid])->find();
        if ($user) {
            //已经注册
            $this->ajaxReturn($wx_result);
        } else {
            //注册
            $wx_result = $this->register($request_data);
            $this->ajaxReturn($wx_result);
        }
    }
    
    //注册
    public function register($data) {
        $add = [
            'openid' => $data['openid'],
            'nickname' => $data['nickname'],
            'headimgurl' => $data['headimgurl'],
            'province' => $data['province'],
            'city' => $data['city'],
            'created' => time(),
        ];
        $res = $this->user_model->add($add);
        if ($res) {
            $return_result = [
                'code' => 1,
                'msg' => '注册成功',
                'info' => [
                    'openid' => $data['openid'],
                ],
            ];
        } else {
            setLog('注册失败：'.json_encode($add), 'wxapi');
            $return_result = [
                'code' => -3,
                'msg' => '注册失败',
            ];
        }
        return $return_result;
    }
}

?>