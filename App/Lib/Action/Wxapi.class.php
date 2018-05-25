<?php
//调用微信api
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class Wxapi {
    const GET_OPENID_URL = 'https://api.weixin.qq.com/sns/jscode2session?';//获取openid url
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
        $url = self::GET_OPENID_URL . 'appid='.$this->appid . '&secret='.$this->appsecret . '&js_code='.$code . '&grant_type=authorization_code';
        $result = $this->request($url);
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
        return $result;
    }
    
    /**
     * 以post方式请求对应的接口url
     *
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     */
    private static function request($url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
//        if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
//            && WxPayConfig::CURL_PROXY_PORT != 0){
//            curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
//            curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
//        }
        curl_setopt($ch,CURLOPT_URL, $url);
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        } else {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        }
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
//            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
//            curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
//            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
//            curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        curl_close($ch);
        return $data;
    }
}