<?php

/**
 * 	微斯咖
 */
class IndexAction extends Action {

    //
    public function index() {
        
    }
    
    //测试打印表单
    public function test_kdn(){
        header("Content-type: text/html; charset=utf-8");
        $url = __APP__.'/api/kdniao/print_orderdemo';
        $url = 'localhost/viska/api/kdniao/print_orderdemo';
        
      $data = [
          'ShipperCode'   =>  'SF',//快递公司编码
          'OrderCode'     => '123456789',//订单号（不可重复提交，重复提交系统会返回具体错误代码。）
          'LogisticCode'  =>  '789456123',//快递单号
          'PayType'       =>  '1',//邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
          'Cost'          =>  '99.99',
          'sendername'    =>  '测试寄送人',
          'sendermobile'  =>  '13678123456',
          'senderprovincename'    =>  '广东省',//不要缺少“省”
          'sendercityname'    =>  '广州市',//不要缺少“市”
          'senderexpareaName' =>  '天河区',//不要缺少“区”或“县”
          'senderaddress' =>  '测试地址',
          'receivername'  =>  '测试收货人',
          'receivermobile' =>  '13678123456',
          'receiverprovincename'  =>  '广东省',
          'receivercityname'  =>  '广州市',
          'receiverexpareaName'   =>  '萝岗区',
          'receiveraddress'   =>  '测试地址',
//        'return_number' =>  '1234561',
          'Remark'        =>  '测试的电子面单',//备注
          'GoodsName' =>  '测试商品',//商品名称
          'Goodsquantity' =>  '100',//商品数量
          'GoodsPrice'    =>  '88.88',//商品价格
          'GoodsDesc'     =>  '用于测试',//商品描述
          'IsReturnPrintTemplate' =>  '0',//返回电子面单模板：0-不需要；1-需要（如果调用批量打印则封装这个为0）
          'returnjson' =>  1,//是否返回json格式
      ];
        
//        $data = [
//            'ShipperCode'   =>  'SF',//快递公司编码
////          'ShipperCode'   =>  I('ShipperCode'),//快递公司编码
//            'OrderCode'     => I('OrderCode'),//订单号（不可重复提交，重复提交系统会返回具体错误代码。）
//            'LogisticCode'  =>  I('LogisticCode'),//快递单号
//            'PayType'       =>  '1',//邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
//            'Cost'          =>  I('Cost'),
//            'sendername'    =>  I('sendername'),
//            'sendermobile'  =>  I('sendermobile'),
//            'senderprovincename'    =>  I('senderprovincename'),//不要缺少“省”
//            'sendercityname'    =>  I('sendercityname'),//不要缺少“市”
//            'senderexpareaName' =>  I('senderexpareaName'),//不要缺少“区”或“县”
//            'senderaddress' =>  I('senderaddress'),
//            'receivername'  =>  I('receivername'),
//            'receivermobile' =>  I('receivermobile'),
//            'receiverprovincename'  =>  I('receiverprovincename'),
//            'receivercityname'  =>  I('receivercityname'),
//            'receiverexpareaName'   =>  I('receiverexpareaName'),
//            'receiveraddress'   =>  I('receiveraddress'),
//            'return_number' =>  '1234561',
//            'Remark'        =>  I('Remark'),//备注
//            'GoodsName' =>  I('GoodsName'),//商品名称
//            'Goodsquantity' =>  I('Goodsquantity'),//商品数量
//            'GoodsPrice'    =>  I('GoodsPrice'),//商品价格
//            'GoodsDesc'     =>  I('GoodsDesc'),//商品描述
//            'IsReturnPrintTemplate' =>  '0',//返回电子面单模板：0-不需要；1-需要（如果调用批量打印则封装这个为0）
//            'returnjson' =>  1,//是否返回json格式
//        ];
        
        $res = curl_snatch($url,$data,$method='POST');
        
        
        print_r($res);
    }
    
    //批量打印的接口
    public function test_build_form(){
        //先通过快递鸟电子面单接口提交电子面单后，再组装POST表单调用快递鸟批量打印接口页面
        
        
        header("Content-type: text/html; charset=utf-8");
        
        if( C('IS_TEST') && 0 ){
            $url = C('YM_DOMAIN').'/api/kdniao/build_form';
        }
        else{
            $url = __APP__.'/api/kdniao/build_form';
        }
        
        
        $OrderCode = [
            '619589585151','619589585152'
        ];
        $PortName = '打印机名称一';
        
        $data = [
            'OrderCode' => $OrderCode,
            'PortName' => $PortName,
        ];
        
        $res = '<form id="form1" method="POST" action="' . $url . '"><input type="text" name="OrderCode" value="' . $OrderCode . '"/><input type="text" name="PortName" value="' . $PortName . '"/></form><script>form1.submit();</script>';
        
        //print_r($data);return;
        
        //$res = curl_snatch($url,$data,'POST');
        
        //$res = $this->sendPost($url,$data);
        
        print_r($res);
        
    }
    
    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    public function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if (empty($url_info['port'])) {
            $url_info['port'] = 80;
        }
        
        header("Content-type: text/html; charset=utf-8");
        
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
                if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                        break;
                }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd); 

        return $gets;
    }//end func sendPost
    
    
    
    
}

?>