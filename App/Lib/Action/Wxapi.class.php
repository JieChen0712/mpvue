<?php
//调用微信api
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class Wxapi {
    const GET_OPENID_URL = 'https://api.weixin.qq.com/sns/jscode2session';//获取openid url
    public $appid;//小程序ID
    public $appsecret;//小程序密钥
    /**
     * 架构函数
     */
    public function __construct($appid, $appsecret) {
        $this->appid = $appid;
        $this->appsecret = $appsecret;        
    }
    
     //获取openid
    public function get_openid($code) {
        if (empty($code)) {
            $return_result = [
                'code' => -1,
                'msg' => 'code不能为空',
            ];
            return $return_result;
        }
        $url = self::GET_OPENID_URL;//请求的地址
        //get请求的参数
        $param = 'appid='.$this->appid . '&secret='.$this->appsecret . '&js_code='.$code . '&grant_type=authorization_code';
        $result =curl_snatch($url);
        $result = json_decode($result, true);
        
        if (isset($result['errcode'])) {
            setLog('获取openid：'.json_encode($result),'wxapi');
            $return_result = [
                'code' => $result['errcode'],
                'msg' => $result['errmsg'],
            ];
        } else {
            $return_result = [
                'code' => 1,
                'msg' => '获取openid成功',
                'info' => [
                    'openid' => $result['openid'],
                ],
            ];
        }
        return $return_result;
    }
}