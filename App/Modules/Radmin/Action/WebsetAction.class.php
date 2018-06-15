<?php

/**
 *  微斯咖经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class WebsetAction extends CommonAction {

    //
    public function index() {
        //获取系统头像
        $img_path = '/upload/system_logo/system_logo.png?'.rand(5,99999);
        //检查自定义的是否存在
//      if(!file_exists($img_path)){
//          $img_path = '/Public/Radmin_v3/images/logo/system_logo.png?'.rand(5,99999);
//      }
        $aid = $_SESSION['aid'];
        $this->webconfig = $this->get_config();
        $this->img_path=$img_path;
        $this->display();
    }

    //获取配置
    public function get_webconfig() {
        
        if (!IS_AJAX) {
            return FALSE;
        }

        $webconfig = $this->get_config();
        
        $result = [
            'code' => 1,
            'msg' => '',
            'config' => $webconfig,
        ];
        echo $this->ajaxReturn($result);
    }
    
    
    private function get_config(){
//      $webconfig = [
//       'APP_GROUP_LIST'=>'Radmin,Api',
//       'DEFAULT_GROUP'=>'Radmin',
//       'APP_GROUP_MODE'=>'1',
//       'APP_GROUP_PATH'=>'Modules',
//       'URL_MODEL'=>'2',
//       'DB_HOST'=>'localhost',
//       'DB_USER'=>'normal',
//       'DB_PWD'=>'O1fpGg1eXENRCN7t',
//       'DB_NAME'=>'applets-back',
//       'DB_PREFIX'=>'',
//       'URL_HTML_SUFFIX'=>'',
//       'LOAD_EXT_CONFIG'=>'verify',
//       'SHOW_PAGE_TRACE'=>'',
//       'URL_CASE_INSENSITIVE'=>'1',
//       'URL_ROUTER_ON'=>'1',
//       'URL_ROUTE_RULES'=> array(
//               '/^p_(\d+)_o_(\d+)$/'=>'Admin/Manage/apply?pid=:1&oid=:2',
//       ),
//      
//       'APP_TOKEN'=>'',
//       'APP_AESK'=>'',
//       'YM_DOMAIN'=>'mall.wsxitong.cn',
//       'APP_ID'=>'',
//       'APP_SECRET'=>'',
//       'IS_TEST'=>'',
//       'SYSTEM_UPDATE'=>'git pull',
//       'LOG_RECORD'=>'',
//      ];
        $webconfig = [
           'APP_GROUP_LIST'=>C('APP_GROUP_LIST'),
           'DEFAULT_GROUP'=>C('DEFAULT_GROUP'),
           'APP_GROUP_MODE'=>C('APP_GROUP_MODE'),
           'APP_GROUP_PATH'=>C('APP_GROUP_PATH'),
           'URL_MODEL'=>C('URL_MODEL'),
           'DB_HOST'=>C('DB_HOST'),
           'DB_USER'=>C('DB_USER'),
           'DB_PWD'=>C('DB_PWD'),
           'DB_NAME'=>C('DB_NAME'),
           'DB_PREFIX'=>C('DB_PREFIX'),
           'URL_HTML_SUFFIX'=>C('URL_HTML_SUFFIX'),
           'LOAD_EXT_CONFIG'=>C('LOAD_EXT_CONFIG'),
           'SHOW_PAGE_TRACE'=>C('SHOW_PAGE_TRACE'),
           'URL_CASE_INSENSITIVE'=>C('URL_CASE_INSENSITIVE'),
           'URL_ROUTER_ON'=>C('URL_ROUTER_ON'),
           'URL_ROUTE_RULES'=> C('URL_ROUTE_RULES'),
          
           'APP_TOKEN'=>C('APP_TOKEN'),
           'APP_AESK'=>C('APP_AESK'),
           'YM_DOMAIN'=>C('YM_DOMAIN'),
           'APP_ID'=>C('APP_ID'),
           'APP_SECRET'=>C('APP_SECRET'),
           'IS_TEST'=>C('IS_TEST'),
           'SYSTEM_UPDATE'=>C('SYSTEM_UPDATE'),
           'LOG_RECORD'=>C('LOG_RECORD'),
        ];
        
        return $webconfig;
    }



    //修改网站配置
    public function update_webset() {
      $UPDATE_COMMAND = I('SYSTEM_UPDATE');
        $UPDATE_COMMAND = empty($UPDATE_COMMAND)?'git pull':$UPDATE_COMMAND;
        $new_config = [
          'APP_GROUP_LIST'=>C('APP_GROUP_LIST'),
           'DEFAULT_GROUP'=>C('DEFAULT_GROUP'),
           'APP_GROUP_MODE'=>C('APP_GROUP_MODE'),
           'APP_GROUP_PATH'=>C('APP_GROUP_PATH'),
           'URL_MODEL'=>C('URL_MODEL'),
           'DB_HOST'=>C('DB_HOST'),
           'DB_USER'=>C('DB_USER'),
           'DB_PWD'=>C('DB_PWD'),
           'DB_NAME'=>C('DB_NAME'),
           'DB_PREFIX'=>C('DB_PREFIX'),
           'URL_HTML_SUFFIX'=>C('URL_HTML_SUFFIX'),
           'LOAD_EXT_CONFIG'=>C('LOAD_EXT_CONFIG'),
           'SHOW_PAGE_TRACE'=>C('SHOW_PAGE_TRACE'),
           'URL_CASE_INSENSITIVE'=>C('URL_CASE_INSENSITIVE'),
           'URL_ROUTER_ON'=>C('URL_ROUTER_ON'),
           'URL_ROUTE_RULES'=> C('URL_ROUTE_RULES'),
          
           'APP_TOKEN'=>C('APP_TOKEN'),
           'APP_AESK'=>C('APP_AESK'),
           'YM_DOMAIN'=>C('YM_DOMAIN'),
           'APP_ID'=>C('APP_ID'),
           'APP_SECRET'=>C('APP_SECRET'),
           'IS_TEST'=>C('IS_TEST'),
           'SYSTEM_UPDATE'=>$UPDATE_COMMAND,
           'LOG_RECORD'=>C('LOG_RECORD'),
        ];

        $result = $this->update_config($new_config);

        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
//        print_r($result);
        $this->success('保存成功');
    }
    
    //获取某级别是否存在代理
    public function get_level_exist() {
        if (!IS_AJAX) {
            return FALSE;
        }

        $level = I('level');

        if (empty($level) || !is_numeric($level)) {
            $result = [
                'code' => 2,
                'msg' => '提交级别必须为数字',
            ];
            echo $this->ajaxReturn($result);
        }

        $condition = [
            'level' => ['EGT', $level],
        ];

        $dis_info = M('distributor')->field('id')->where($condition)->find();

        if (!empty($dis_info)) {
            $result = [
                'code' => 3,
                'msg' => '该级别及其以下级别还有代理，无法删除！',
            ];
            echo $this->ajaxReturn($result);
        }

        $result = [
            'code' => 1,
            'msg' => '可删除！',
        ];
        echo $this->ajaxReturn($result);
    }

    //修改config配置
    private function update_config($new_config = [],$filename='') {

        if (empty($new_config)) {
            $return_result = [
                'code' => 2,
                'msg' => '没有新的提交'
            ];
            return $return_result;
        }
        
        if( empty($filename) ){
            $filename = 'config.php';
        }
        
        //文件路径地址
//        $path =  'App/Conf/text.php';//测试文本
        $path = 'App/Conf/'.$filename; //正式
        
        if (file_exists($path)) {
            $return_result['file_exists'] = '存在';
        }
        if (is_writable($path)) {
            $return_result['is_writable'] = '可写';
        }

        //读取配置文件,
        $file = include $path;

//        print_r($file);return;
        //合并数组，相同键名，后面的值会覆盖原来的值
        $res = array_merge($file, $new_config);

        //print_r($res);return;
        //数组循环，拼接成php文件
        $str = '<?php' . "\n" . ' return array(' . "\n";

        //config配置数组目前最多三维
        foreach ($res as $key => $value) {
            // '\'' 单引号转义
            if (is_array($value)) {
                $new_str = '   \'' . $key . '\'' . '=> array(' . "\n";

                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        $new_str2 = '       \'' . $k . '\'' . '=> array(' . "\n";
                        foreach ($v as $kk => $vv) {
                            $new_str2 .= '          \'' . $kk . '\'' . '=>' . '\'' . $vv . '\'' . ',' . "\n";
                        }
                        $new_str2 .= '              ),' . "\n";
                        $new_str .= $new_str2;
                    } else {
                        $new_str .= '           \'' . $k . '\'' . '=>' . '\'' . $v . '\'' . ',' . "\n";
                    }
                }
                $new_str .= '   ),' . "\n";
                $str .= $new_str;
            } else {
                $str .= '   \'' . $key . '\'' . '=>' . '\'' . $value . '\'' . ',' . "\n";
            }
//            print_r($str);
        };
        $str .= "\n" . '); ?>';

        //print_r($str);
        //return;
        //写入文件中,更新配置文件
        if (file_put_contents($path, $str)) {
            $return_result['code'] = 1;
            $return_result['msg'] = '保存成功！';
        } else {
            $return_result['code'] = 3;
            $return_result['msg'] = '保存失败！';
        }
        //print_r($return_result);
        return $return_result;
    }
    
    //修改额外配置
    public function update_extra(){
        
        $all_info = I();
        
        
        $return_result = $this->update_config($all_info,'extra.php');
        
        
        if( $return_result['code'] == 1 ){
            $this->add_active_log('修改网站配置成功');
            $this->success('修改网站配置成功');
        }
        else{
            $this->error($return_result['msg']);
        }
    }
    
    

    //发布更新
    public function replace() {
        $this->display();
    }

    //更新操作
    public function run_replace_ajax() {

//        if (!IS_AJAX) {
//            return FALSE;
//        }


        $SYSTEM_UPDATE = C('SYSTEM_UPDATE');

        if (empty($SYSTEM_UPDATE)) {
            $SYSTEM_UPDATE = 'git pull';
        }


        $res = exec($SYSTEM_UPDATE);
        $result = [
            'code' => 1,
            'msg' => '返回结果：  '.$res,
            'excu' => $res,
        ];
        $this->add_active_log('操作系统更新');
        $this->ajaxReturn($result);
    }

    //清空缓存
    public function clear_cache() {
        $this->display();
    }

    //提交清空缓存
    public function clear_cache_ajax() {

        if (!IS_AJAX) {
            return FALSE;
        }

        $cache = I('cache');
        $data = I('data');
        $logs = I('logs');
        $temp = I('temp');

        $runtime_path = 'App/Runtime'; //缓存位置

        $cache_path = $runtime_path . '/Cache';
        $data_path = $runtime_path . '/Data';
        $logs_path = $runtime_path . '/Logs';
        $temp_path = $runtime_path . '/Temp';

        $exec_res = [];
        if ($cache == 1 && file_exists($cache_path)) {
            $exec_res['cache'] = $this->del_dir($cache_path);
            $this->add_active_log('清除Cache(缓存)');
        }

        if ($data == 1 && file_exists($data_path)) {
            $exec_res['data'] = $this->del_dir($data_path);
            $this->add_active_log('清除Date(数据)');
        }

        if ($logs == 1 && file_exists($logs_path)) {
            $exec_res['logs'] = $this->del_dir($logs_path);
            $this->add_active_log('清除Logs(日志)');
        }

        if ($temp == 1 && file_exists($temp_path)) {
            $exec_res['temp'] = $this->del_dir($temp_path);
            $this->add_active_log('清除Temp(模板)');
        }
        

        $result = [
            'code' => 1,
            'msg' => '更新成功！',
            'exec' => $exec_res,
        ];
        $this->ajaxReturn($result);
    }

    //删除文件夹
    private function del_dir($dir) {
//        if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
//               $str = "rmdir /s/q " . $dir;
//        } else {
//               $str = "rm -Rf " . $dir;
//        }
//        $exec_res = 'succ';
//        $exec_res = exec($str);
//        return $exec_res;
        //先删除目录下的文件： 
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->del_dir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹： 
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    //获取缓存信息
    public function get_runtime_info() {

        if (!IS_AJAX) {
            return FALSE;
        }


        $runtime_path = 'App/Runtime'; //缓存位置

        $cache = $runtime_path . '/Cache';
        $data = $runtime_path . '/Data';
        $logs = $runtime_path . '/Logs';
        $temp = $runtime_path . '/Temp';


        $cache_size = $data_size = $logs_size = $temp_size = 0;

        if (file_exists($cache)) {
            $cache_size = $this->getDirSize($cache);
            $cache_size = $this->getRealSize($cache_size);
        }

        if (file_exists($data)) {
            $data_size = $this->getDirSize($data);
            $data_size = $this->getRealSize($data_size);
        }

        if (file_exists($logs)) {
            $logs_size = $this->getDirSize($logs);
            $logs_size = $this->getRealSize($logs_size);
        }

        if (file_exists($temp)) {
            $temp_size = $this->getDirSize($temp);
            $temp_size = $this->getRealSize($temp_size);
        }
        
        
        $result = [
            'code' => 1,
            'msg' => '',
            'info' => [
                'cache' => [
                    'file_exists' => file_exists($cache),
                    'is_writable' => is_writable($cache),
                    'size' => $cache_size,
                ],
                'data' => [
                    'file_exists' => file_exists($data),
                    'is_writable' => is_writable($data),
                    'size' => $data_size,
                ],
                'logs' => [
                    'file_exists' => file_exists($logs),
                    'is_writable' => is_writable($logs),
                    'size' => $logs_size,
                ],
                'temp' => [
                    'file_exists' => file_exists($temp),
                    'is_writable' => is_writable($temp),
                    'size' => $temp_size,
                ],
            ],
        ];

        $this->ajaxReturn($result);
    }

    // 获取文件夹大小  
    public function getDirSize($dir) {
        $handle = opendir($dir);
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    $sizeResult += $this->getDirSize("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);
        return $sizeResult;
    }

    // 单位自动转换函数  
    private function getRealSize($size) {
        if( empty($size) ){
            return '0 B';
        }
        
        $kb = 1024;         // Kilobyte  
        $mb = 1024 * $kb;   // Megabyte  
        $gb = 1024 * $mb;   // Gigabyte  
        $tb = 1024 * $gb;   // Terabyte  

        if ($size < $kb) {
            return $size . " B";
        } else if ($size < $mb) {
            return round($size / $kb, 2) . " KB";
        } else if ($size < $gb) {
            return round($size / $mb, 2) . " MB";
        } else if ($size < $tb) {
            return round($size / $gb, 2) . " GB";
        } else {
            return round($size / $tb, 2) . " TB";
        }
    }

    //设置后台样式页面
    public function system_style() {
        $webset = M('website_set');
        
        //获取总后台样式
        $radmin_style = $webset->where(array('name'=>'radmin_style'))->field('value')->find();
        
        //获取代理后台样式
        $admin_style = $webset->where(array('name'=>'admin_style'))->field('value')->find();
        
        //获取总后台登录样式
        $radmin_login = $webset->where(array('name'=>'radmin_login'))->field('value')->find();
        
//      var_dump($radmin_style['value']);
//      var_dump($admin_style['value']);
//      var_dump($radmin_login['value']);die;
        
        
        
        $this->radmin_style=$radmin_style['value'];
        $this->radmin_login=$radmin_login['value'];
        $this->admin_style=$admin_style['value'];
        $this->display();
    }
    
    //上传logo
    public function upload(){
      
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array( 'png'); // 设置附件上传类型

        $upload->savePath = './upload/system_logo/';// 设置附件上传目录
        $upload->saveRule = 'system_logo';

        $upload->uploadReplace = true; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'custom';  //可以设置为hash或date
//      $upload->subDir = 'system_logo/';
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        $result_msg = "";
        if(empty($info)){
            $result_msg = "上传格式不正确,上传失败";
        }else{
            $result_msg = "上传成功";
        }
        $result = [
          'code' => 0,
          'msg' => $result_msg,
          'src' => $image,
        ];
        $this->ajaxReturn($result);
    }
    
    //上传mp文本
    public function upload_mp(){
      
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('txt'); // 设置附件上传类型

        $upload->savePath = './';// 设置附件上传目录
        $upload->saveRule = I('mp_file');

        $upload->uploadReplace = true; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'custom';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        $result = [
          'code' => 0,
          'msg' => '上传成功',
          'src' => $image
        ];
        $this->ajaxReturn($result);
    }
    
    //数据库版自定义授权书
    public function certificate_set(){
      $certificate = M('distributor_certificate');

      $cavconfig = F('certificate_config');
      if(empty($cavconfig)){
          $cavconfig = $certificate->field('name,value')->select();
          F('certificate_config',$cavconfig);
      }else{
          $cavconfig = F('certificate_config');
      }

      $bgImg = $certificate->where(['name' => 'bgImg'])->find();
      $bgImg = $bgImg['value'];
      $bgImg = stripslashes($bgImg);
      
      $data = array();
      foreach($cavconfig as $v){
        $data[$v['name']][] = $v['value'];
        
      }
      $cavconfig = json_encode($data);
      $img_show = 1;
      
      if(empty($bgImg)){
        $img_show = 0;
      }
      
      $this->bgImg = $bgImg;
      $this->img_show = $img_show;
      $this->cavconfig =$cavconfig;
      $this->add_active_log;
      $this->display();
    }
    
    //Config版自定义授权书
//  public function certificate_set(){
//    $cavconfig = C('CAVCONFIG');
//    $bgImg = $cavconfig['bgImg'];
//    
//    $this->bgImg = $bgImg;
//    $this->cavconfig = json_encode($cavconfig);
//    $this->display();
//  }
    
    //保存授权书的参数
    public function certificate_save(){
      if(!IS_AJAX){
        return FALSE;
      }
      $certificate = M('distributor_certificate');

//    $cavconfig = F('certificate_config');
//    if(!empty($cavconfig)){
          F('certificate_config',null);
//    }
      //接收处理转义字符
      $cavconfig = I('cavconfig');
      $cavconfig = stripslashes($cavconfig);
      $cavconfig = preg_replace("/&quot;/", "\"", $cavconfig);
      
      $cavconfig = json_decode($cavconfig,true);
      
      $result = null;
      $data = array();
      
      foreach($cavconfig as $k => $v){
          $data['name'] = $k;
          $data['value'] = json_encode($v);
          
          $data['value'] = preg_replace("/^\"/", "", $data['value']);
          $data['value'] = preg_replace("/\"$/", "", $data['value']);
          
          //查询是否存在
          $exsit = $certificate -> where(array('name' => $k))->field('id')->find();
          //存在就更新，不存在就添加
          if(empty($exsit)){
            $result = $certificate->add($data);
          }else{
            $result = $certificate->where(array('name' => $data['name']))->save($data);
          }
      }
      
      $result = [
        'code'=>1,
        'msg'=>"修改成功"
      ];
      $this->add_active_log;
      $this->ajaxReturn($result);
    }
    
    //隐藏文本
    public function hide_text(){
      if(!IS_AJAX){
        return FALSE;
      }
      $str = I('txt');
      $this->add_active_log;
      $this->ajaxReturn(asterisk($str));
    }
    
    //上传授权书
    public function upload_certificate(){
      
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型

        $upload->savePath = './upload/';// 设置附件上传目录
//      $upload->saveRule = 'certificate';

        $upload->uploadReplace = true; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'custom';  //可以设置为hash或date
        $upload->subDir = "certificate/";
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        $result = [
          'code' => 0,
          'msg' => '上传成功',
          'src' => $image
        ];
        $this->ajaxReturn($result);
    }
    
    //选择后台页面样式
    public function change_system_style(){
      $style_url = I('style_url');
      $style_name = I('style_name');
      
      if(empty($style_url)||empty($style_name)){
        return $this->error('获取参数失败！');
      }
      
      $webset = M('website_set');
      
      $exsit = $webset-> where(['name'=> $style_name])-> field('id')-> find();
      
      $data['name'] = $style_name;
      $data['value'] = $style_url;
      $result = null;
      
      if(empty($exsit)){
        $result = $webset->add($data);
      }else{
        $result = $webset->where(['name'=>$style_name])-> save($data);
      }
      
      $this->success('修改成功！');
}
    //网站配置的返利配置
    public function set_rebate_config(){
        //返利配置
        $OPEN = I('OPEN');
        $ORDER = I('ORDER');
        $MONEY = I('MONEY');
        $ONCE = I('ONCE');
        $SAME_DEVELOPMENT=I('SAME_DEVELOPMENT');
        $DEVELOPMENT=I('DEVELOPMENT');
        $PERSONAL=I('PERSONAL');
        $ORDINARY_TEAM = I('ORDINARY_TEAM');
        $CLICK_TEAM_REBATE=I('CLICK_TEAM_REBATE');

        $REBATE['OPEN']=isset($OPEN)?$OPEN:'0';
        $REBATE['ORDER']=isset($ORDER)?$ORDER:'0';
        $REBATE['MONEY']=isset($MONEY)?$MONEY:'0';
        $REBATE['ONCE']=isset($ONCE)?$ONCE:'0';
        $REBATE['SAME_DEVELOPMENT']=isset($SAME_DEVELOPMENT)?$SAME_DEVELOPMENT:'0';
        $REBATE['DEVELOPMENT']=isset($DEVELOPMENT)?$DEVELOPMENT:'0';
        $REBATE['PERSONAL']=isset($PERSONAL)?$PERSONAL:'0';
        $REBATE['ORDINARY_TEAM']=isset($ORDINARY_TEAM)?$ORDINARY_TEAM:'0';
        $REBATE['CLICK_TEAM_REBATE']=isset($CLICK_TEAM_REBATE)?$CLICK_TEAM_REBATE:'0';
//        $REBATE = array(
//            'OPEN' => (int)$OPEN,
//            'ORDER' => (int)$ORDER, //平级推荐订单返利开启/关闭
//            'MONEY' => (int)$MONEY, //平级推荐充值返利开启/关闭
//            'ONCE' => (int)$ONCE, //低推高一次性返利开启/关闭
//        );
        $new_config['REBATE'] = $REBATE;

        $result = $this->update_config($new_config);

        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
//        print_r($result);
        $this->success('保存成功');
    }

    //网站配置--功能模块配置
    public function set_function_module_config(){
        //功能模块
        $FUNCTION_MODULE = I('FUNCTION_MODULE');

        $FUNCTION_MODULE['MONEY'] = $FUNCTION_MODULE['MONEY']==1?1:0;
        $FUNCTION_MODULE['INTEGRAL_SHOP'] = $FUNCTION_MODULE['INTEGRAL_SHOP']==1?1:0;
        $FUNCTION_MODULE['MALL_SHOP'] = $FUNCTION_MODULE['MALL_SHOP']==1?1:0;
        $FUNCTION_MODULE['MARKET'] = $FUNCTION_MODULE['MARKET']==1?1:0;
        $FUNCTION_MODULE['GW'] = $FUNCTION_MODULE['GW']==1?1:0;
        $FUNCTION_MODULE['TEAM'] = $FUNCTION_MODULE['TEAM']==1?1:0;
        $FUNCTION_MODULE['STOCK'] = $FUNCTION_MODULE['STOCK']==1?1:0;
        $FUNCTION_MODULE['BOSS_ORDER'] = $FUNCTION_MODULE['BOSS_ORDER']==1?1:0;
        $FUNCTION_MODULE['STOCK_ORDER'] = $FUNCTION_MODULE['STOCK_ORDER']==1?1:0;

        $ORDER_SHIPPING=I('ORDER_SHIPPING');
        $SHIPPING_REDUCE_WAY=I('SHIPPING_REDUCE_WAY');
        $new_config['ORDER_SHIPPING']=isset($ORDER_SHIPPING)?$ORDER_SHIPPING:'0';
        $new_config['SHIPPING_REDUCE_WAY']=isset($SHIPPING_REDUCE_WAY)?$SHIPPING_REDUCE_WAY:'0';

        //额外的配置
        $FUNCTION_MODULE['LOAD_EXT_CONFIG'] = 'extra';

        $new_config['FUNCTION_MODULE'] = $FUNCTION_MODULE;
        $result = $this->update_config($new_config);

        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
//        print_r($result);
        $this->success('保存成功');
    }

    //网站配置的品牌商城的返利配置
    public function set_mall_rebate_config(){
        //返利配置
        $OPEN = I('OPEN');
        $ORDER = I('ORDER');
        $IS_OPEN = I('IS_OPEN');
        $MALL_REFUND_PAY_TYPE = I('MALL_REFUND_PAY_TYPE');

        $MALL_REBATE['OPEN']=isset($OPEN)?$OPEN:'0';
        $MALL_REBATE['ORDER']=isset($ORDER)?$ORDER:'0';
        $MALL_REFUND['IS_OPEN']=isset($IS_OPEN)?$IS_OPEN:'0';
        $MALL_REFUND['MALL_REFUND_PAY_TYPE']=isset($MALL_REFUND_PAY_TYPE)?$MALL_REFUND_PAY_TYPE:'0';
        $new_config['MALL_REBATE'] = $MALL_REBATE;
        $new_config['MALL_REFUND'] = $MALL_REFUND;
        $result = $this->update_config($new_config);
        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
        $this->success('保存成功');
    }
}

?>