<?php

//数组打印函数
function p($arr) {
    echo '<pre>' . print_r($arr, true) . '</pre>';
    die;
}

function toValid($m_temp) {
    return addslashes(htmlspecialchars(trim($m_temp)));
}

function setLog($m_array, $prex = "") {
    if (is_array($m_array)) {
        $m_array = json_encode($m_array);
    }
    import('Class.Logs', APP_PATH);
    $dir = "log/" . date("Y/m", time());
    if (!empty($prex)) {
        $filename = $prex . "-" . date("d", time()) . ".log";
    } else {
        $filename = date("d", time()) . ".log";
    }
    $logs = new Logs("", $dir, $filename);
    $logs->setlog($m_array);
}

function image_save($file, $path) {
    if ($file["type"] == "image/gif") {
        @$im = imagecreatefromgif($file['tmp_name']);
        if ($im) {
            $sign = imagegif($im, $path);
        } else {
            return "error";
        }
    } elseif ($file["type"] == "image/png" || $file["type"] == "image/x-png") {
        @$im = imagecreatefrompng($file['tmp_name']);
        if ($im) {
            $sign = imagepng($im, $path);
        } else {
            return "error";
        }
    } else {
        @$im = imagecreatefromjpeg($file['tmp_name']);
        if ($im) {
            $sign = imagejpeg($im, $path, 100);
        } else {
            return "error";
        }
    }
    return $sign;
}

function delEditorImage($m_string, $find_str) {
    $m_count = substr_count($m_string, "editor/");
    for ($i = 0; $i < $m_count; $i ++) {
        $m_tempstr = stristr($m_string, "editor/");
        $m_pos = stripos($m_tempstr, "alt");
        $m_image_src = substr($m_tempstr, 0, $m_pos);
        $m_pos -= 2;
        $m_tempimg = substr($m_tempstr, 0, $m_pos);
        $del_path = "public/Admin/" . $m_tempimg;
        $del_num = 0;
        clearstatcache();
        if (is_file($del_path)) {
            $del_res = unlink($del_path);
            ++$del_num;
        } else {
            $del_num = 0;
        }
        $m_string = str_ireplace($m_image_src, "", $m_string);
    }
    return $del_num;
}

function delUploadImage($m_upload_imagePath) {
    clearstatcache();
    if (is_file($m_upload_imagePath)) {
        $del_res = unlink($m_upload_imagePath);
        if ($del_res == false) {
            return error;
        } else {
            return success;
        }
    }
}

function https_request($url, $data = null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
* curl抓取数据
*
* @param string $url
* @param string $data
* @param strnig $method
* @return bool/string
*/
function curl_snatch($url,$data,$method='GET')
{
   if( function_exists('curl_init') ){
        $ch = curl_init();

        if( $method=='GET'){
            $url = $url.'?'.$data;
        }
        curl_setopt($ch, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

//        curl_setopt($ch, CURLOPT_SSLVERSION, 3);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        if( $method=='POST' ){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
   }

   return FALSE;
}//end func curl_snatch

function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date($format, $time);
}

function dateLimit($format = 'Y-m-d', $time) {
    if (empty($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    $time = $time + 3600 * 24 * 365;
    return date($format, $time);
}

function uploadImg($name, $url) {

    import('ORG.Net.UploadFile');
    $upload = new UploadFile(); // 实例化上传类
    $upload->maxSize = 3145728; // 设置附件上传大小
    $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
    $upload->savePath = $url; // 设置附件上传目录
    $upload->saveRule = time() . '_' . mt_rand();
    $imgUrl = $upload->savePath . $upload->saveRule;
    $imgUrl = ltrim($imgUrl, '.');
    if (!$upload->uploadOne($name, $url)) {// 上传错误提示错误信息
        $this->error($upload->getErrorMsg());
    } else {// 上传成功
        return $imgUrl;
    }
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = 'cxphp') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @return String
 */
function decode($string = '', $skey = 'cxphp') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

//获取jsapi_ticket
function get_jsapi_ticket() {
    import('Wechat.Jssdk', APP_PATH);
    $jssdk = new Jssdk(C('APP_ID'), C('APP_SECRET'));
    return $jssdk->GetSignPackage();
}

//获取access_token
function get_access_token() {
    import('Wechat.Jssdk', APP_PATH);
    $jssdk = new Jssdk(C('APP_ID'), C('APP_SECRET'));
    return $jssdk->getAccessToken();
}

//    function get_test(){
//        return 123;
//    }


function get_ip() { 
    if (getenv('HTTP_CLIENT_IP')) { 
        $ip = getenv('HTTP_CLIENT_IP'); 
    } 
    elseif (getenv('HTTP_X_FORWARDED_FOR')) { 
        $ip = getenv('HTTP_X_FORWARDED_FOR'); 
    } 
    elseif (getenv('HTTP_X_FORWARDED')) { 
        $ip = getenv('HTTP_X_FORWARDED'); 
    } 
    elseif (getenv('HTTP_FORWARDED_FOR')) { 
        $ip = getenv('HTTP_FORWARDED_FOR'); 

    } 
    elseif (getenv('HTTP_FORWARDED')) { 
        $ip = getenv('HTTP_FORWARDED'); 
    } 
    else { 
        $ip = $_SERVER['REMOTE_ADDR']; 
    } 
    return $ip; 
} 

/**
 * 错误提示页面
 * @param type $content         提示内容
 * @param type $url             直接跳转页面
 * @param type $return_url      提示页面左上返回按钮链接，默认为回退
 */
function error_tip($content, $url,$return_url='') {
    if (!$url) {
        $url = __APP__.'/admin/tip/error';
    }
    $url = $url . '?msg='. $content.'&return_url='.$return_url;
    header("location:$url");
    exit();
}



/* 
    * 经典的概率算法， 
    * $proArr是一个预先设置的数组， 
    * 假设数组为：array(100,200,300，400)， 
    * 开始是从1,1000 这个概率范围内筛选第一个数是否在他的出现概率范围之内，  
    * 如果不在，则将概率空间，也就是k的值减去刚刚的那个数字的概率空间， 
    * 在本例当中就是减去100，也就是说第二个数是在1，900这个范围内筛选的。 
    * 这样 筛选到最终，总会有一个数满足要求。 
    * 就相当于去一个箱子里摸东西， 
    * 第一个不是，第二个不是，第三个还不是，那最后一个一定是。 
    * 这个算法简单，而且效率非常 高， 
    */  
   function get_rand($proArr) {   
       $result = '0';    
       //概率数组的总概率精度   
       $proSum = array_sum($proArr);    
       //概率数组循环   
       foreach ($proArr as $key => $proCur) {   
           $randNum = mt_rand(1, $proSum);   
           if ($randNum <= $proCur) {   
               $result = $key;   
               break;   
           } else {   
               $proSum -= $proCur;   
           }         
       }   
       unset ($proArr);    
       return $result;   
   }
   
   /**
 * 获得当前月/上个月份
 * @param int $type 0当前月1上个月2下个月
 * @return string
 */
function get_month($type = 0) {
    $tmp_date=date('Ym');
    if (!$type) {
        return $tmp_date;
    } else {
        //切割出年份
        $tmp_year=substr($tmp_date,0,4);
        //切割出月份
        $tmp_mon =substr($tmp_date,4,2);
        $tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
        $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);
        if($type == 2){
            //得到当前月的下一个月   
            return $fm_next_month=date("Ym",$tmp_nextmonth);          
        }else if($type == 1){
            //得到当前月的上一个月   
            return $fm_forward_month=date("Ym",$tmp_forwardmonth);           
        }
    }
} 



//----------start 双向加密（该版本用于链接上，公开性较高，尽量不要用于保密性高的地方）----------------
function  tiriEncode($str , $factor = 0){
    $len = strlen($str);
    if(!$len){
        return;
    }
    if($factor  === 0){
        $factor = mt_rand(1, min(255 , ceil($len / 3)));
    }
    $c = $factor % 8;

    $slice = str_split($str ,$factor);
    for($i=0;$i < count($slice);$i++){
        for($j=0;$j< strlen($slice[$i]) ;$j ++){
            $slice[$i][$j] = chr(ord($slice[$i][$j]) + $c + $i);
        }
    }
    $ret = pack('C' , $factor).implode('' , $slice);
    return base64URLEncode($ret);
}

function tiriDecode($str){  
    if($str == ''){
        return;
    }     
    $str = base64URLDecode($str);
    $factor =  ord(substr($str , 0 ,1));
    $c = $factor % 8;
    $entity = substr($str , 1);
    $slice = str_split($entity , $factor);
    if(!$slice){
        return false;
    }
    for($i=0;$i < count($slice); $i++){
        for($j =0 ; $j < strlen($slice[$i]); $j++){
            $slice[$i][$j] = chr(ord($slice[$i][$j]) - $c - $i );
        }
    }
    return implode($slice);
}

function base64URLEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64URLDecode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function stringXor($str){
    for ($i = 0; $i < strlen($str); ++$i) {
        $str[$i] = chr(ord($str[$i]) ^ 0x7F);
    }
    return $str;
}
//----------end 双向加密----------------



//得到某个数字的范围
/**
 * PS:
 * $stage_data = [
 *  100000,
 *  200000,
 *  300000,
 *  400000,
 *  600000,
 *  800000
 *  ];
 * 
 *  $stage_num = 390000;
 * 
 *   $res = binarySearch($rebate_percent,$stage_num);
 * 
 *   echo $res;
 * 
 *  (result: 2)
 */
function binarySearch(&$stage_data,$stage_num){
    $count = count($stage_data);
    
    array_push($stage_data,$stage_num);
    $data = array_unique($stage_data);
    asort($data);
    $data = array_values($data);
    
    $key = array_search($stage_num,$data);
    
    $new_count = count($data);
    
    if( $key != 0 && $new_count != $count ){
        $key--;
    }
    
    return $key;
}

//自动给字符串标星号
function asterisk($str){
    
    if( is_array($str) ){
        return $str;
    }
    $asterisk = '***';
    $str_len = mb_strlen($str);
    
    if( $str_len < 4 ){
        return $asterisk;
    }
    
    $num = $str_len/4;
    
    if( $num > 2 ){
        $num = 3;
    }
    $end_beg = $str_len-$num;
    
    $new_str = displaystr($str, 0,$num).$asterisk.displaystr($str, $end_beg,$num);
    
    return $new_str;
}

//中英文混合都可无乱码截取的方法
function displaystr($str, $start, $lenth){  
        $len = strlen($str);  
        $r = array();  
        $n = 0;  
        $m = 0;  
        for($i = 0; $i < $len; $i++) {  
            $x = substr($str, $i, 1);  
            $a  = base_convert(ord($x), 10, 2);  
            $a = substr('00000000'.$a, -8);  
            if ($n < $start){  
                if (substr($a, 0, 1) == 0) {  
                }elseif (substr($a, 0, 3) == 110) {  
                    $i += 1;  
                }elseif (substr($a, 0, 4) == 1110) {  
                    $i += 2;  
                }  
                $n++;  
            }else{  
                if (substr($a, 0, 1) == 0) {  
                    $r[ ] = substr($str, $i, 1);  
                }elseif (substr($a, 0, 3) == 110) {  
                    $r[ ] = substr($str, $i, 2);  
                    $i += 1;  
                }elseif (substr($a, 0, 4) == 1110) {  
                    $r[ ] = substr($str, $i, 3);  
                    $i += 2;  
                }else{  
                    $r[ ] = '';  
                }  
                if (++$m >= $lenth){  
                    break;  
                }  
            }  
        }  
        return join('',$r);  
    }   
