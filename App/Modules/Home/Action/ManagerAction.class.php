<?php

/**
 * 	微斯咖前台——经销商查询&申请总经销商
 */
class ManagerAction extends Action {

    //经销商查询表单
    public function index() {
        $condition=[
            'status'=>1,
            'type'=>1,
        ];
        $info=M('info')->where($condition)->find();
        $this->info=$info;

        $this->display();
    }

    //申请最高级别
    public function applyAllAgent() {
        
        $level = I('level');
        
        if( empty($_SESSION['oid']) ){
            checkAuth('applyAllAgent','',$level);
        }

        $list = M('regulations')->find();

        if( empty($level) || !is_numeric($level) ){
            $level = 1;
        }
        $this->list = $list;
        $this->level = $level;
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }



    //把图片保存在指定位置
    public function uploadCanvas() {
        $data = substr($_POST['imgData'], 22);
        $data = base64_decode($data);
        $tpname = md5(time() . rand()) . '.jpg';
        $type = $_POST['type'];
        $fileName = './upload/' . $type . '/' . $tpname;
        $fileNamea = '/upload/' . $type . '/' . $tpname;
        $a = file_put_contents($fileName, $data);
        $this->rotateImg($_POST['Orientation'], $fileName);
        ob_end_clean();
        $this->ajaxReturn($fileNamea, 'JSON');
    }


    public function rotateImg($ro, $fileName) {
        if ($ro == 6) {
            $degrees = 270;
            header('Content-type: image/jpeg');
            $source = imagecreatefromjpeg($fileName);
            $rotate = imagerotate($source, $degrees, 0);
            imagejpeg($rotate, $fileName);
        } else if ($ro == 8) {
            $degrees = 90;
            header('Content-type: image/jpeg');
            $source = imagecreatefromjpeg($fileName);
            $rotate = imagerotate($source, $degrees, 0);
            imagejpeg($rotate, $fileName);
        } else if ($ro == 3) {
            $degrees = 180;
            header('Content-type: image/jpeg');
            $source = imagecreatefromjpeg($fileName);
            $rotate = imagerotate($source, $degrees, 0);
            imagejpeg($rotate, $fileName);
        }
        return true;
    }

    //查询经销商
    public function search() {
        $val = I('post.val');
//        $sql = "select id,name,levname from distributor where ((wechatnum='$val' or phone='$val') and audited=1) limit 1";
//        $the_row = M('distributor')->query($sql);
//        $row = $the_row[0];
        
        $where = array(
            'audited'   =>  1,
            '_complex'  =>  array(
                'wechatnum' => $val,
                '_logic' => 'OR',	
                'phone' => $val,
            ),
        );
        
        $row = M('distributor')->where($where)->find();
        
        if (!empty($row)) {
            if ($row['disable'] == 1) {
                $this->ajaxReturn('disable', 'JSON');
            } else {
                $this->ajaxReturn($row, 'JSON');
            }
        } else {
            $this->ajaxReturn('error', 'JSON');
        }
    }

    //获得经销商详情
    public function detail() {
        $id = I('get.id');
        $no_header = I('get.no_header');
        $no_return = I('get.no_return');
        $top_type = I('get.top_type');
        
        if (empty($no_header)) {
            $no_header = 0;
        }
        
        if( empty($no_return) ){
            $no_return = 0;
        }
        
        if( empty($top_type) ){
            $top_type = 0;
        }


        $manager = M('distributor')->where(array('id' => $id))->find();

        if (!empty($manager)) {
            $manager['idennum'] = substr($manager['idennum'], 0, 6) . "******" . substr($manager['idennum'], -4, 4);
            $manager['authnum'] = substr($manager['authnum'], 0, 3) . '*****';
            $manager['phone'] = substr($manager['phone'], 0, 7) . "****";
            $manager['wechatnum'] = substr($manager['wechatnum'], 0, 2) . "****";
        }

        if (empty($manager['start_time'])) {
            $manager['start_time'] = $manager['time'];
        }
        if (empty($manager['end_time'])) {
            $manager['end_time'] = $manager['time'] + 3600 * 24 * 365;
        }

//		if($manager['end_time']<time()){
//			echo "<script>alert('经销商授权已经过期,请联系公司处理!');window.history.go(-1);</script>";
//		}
//                $manager = array(
//                    'name'  =>  'name',
//                    'wechatnum' =>  'wechatnum',
//                    'levname'   =>  '总代',
//                    'time'  =>  time(),
//                    'authnum'   =>  '123456',
//                );

        $this->top_type = $top_type;
        $this->no_return = $no_return;
        $this->no_header = $no_header;
        $this->manager = $manager;
        $this->display();
    }

    //提交最高级别经销商申请
    public function e_apply() {
        $openid = $_SESSION['oid'];
        if (!IS_AJAX) {
            halt("页面不存在");
        }
        
        $IS_TEST = C('IS_TEST');
        if( $IS_TEST ){
            $openid = time();
        }
        
        $levname    =   trim(I('levname'));
        $phone  = I('phone');
        $headimgurl = $_SESSION['headimgurl'];
        $nickname = $_SESSION['nickname'];
        $name = trim(I('name'));
        $wechatnum = trim(I('wechatnum'));
        $email = trim(I('email'));
        $idennum = trim(I('idennum'));
        $idennumimg = trim(I('idennumimg'));
        $liveimg = trim(I('liveimg'));
        
        $level_name = C("LEVEL_NAME");
        $level_name_key = array_flip($level_name);
        $level = $level_name_key[$levname];
        
        //获取省市区地址
        $post_probably_address=trim(I('post.probably_address'));
        $post_addre = trim(I('post.addre'));
        $arr = explode(' ', $post_probably_address);
        $province=$arr[0];
        $city=$arr[1];
        $county = $arr[2];
//        if($county == ""){
//            $post_probably_address1=$province.$province.$city;
//        }else{
//            $post_probably_address1=$province.$city.$county;
//        }
//        $post_address = $post_probably_address1.$post_addre;
        
        
        $apply_info = array(
            'openid' => $openid,
            'headimgurl' => $headimgurl,
            'pid' => 0,
            'nickname' => $nickname,
            'level' => $level,
            'name' => $name,
            'wechatnum' => $wechatnum,
            'phone' => $phone,
            'email' => $email,
            'idennum' => $idennum,
            'address' => $post_addre,
            'idennumimg' => $idennumimg,
            'liveimg' => $liveimg,
            'province'  =>  $province,
            'city'      =>  $city,
            'county'    =>  $county,
        );
        
        import('Lib.Action.User','App');
        $User = new User();
        $return_result = $User->add($apply_info);
        $this->ajaxReturn($return_result,'json');
        return;
        

        //20117-10-18 重构邀请注册代码
//        if (strlen($phone) == '11') {
//            $search = '/^(1[3|5|7|8|][0-9])\d{8}$/';
//            if (!preg_match($search, $phone)) {
//                $this->ajaxReturn(array('status' => 6), 'json');
//            }
//        } else {
//            $this->ajaxReturn(array('status' => 6), 'json');
//        }
//        
//        
//        $manager = M('distributor')->where(array('openid' => $openid))->find();
//        if (!$manager) {//该微信号尚未申请
//           // $level = I('level');
//            
//            if ($level == 1) {
//                $managed = 1;
//            }
//            else{
//                $managed = 0;
//            }
//            
//            //查询未来授权人的级别和姓名
//
//           // $levname = $level_name[$level];
//            $password = md5(substr($phone, -6));
//            //add by z
//            //生成授权号
//            $authnum = substr($phone, -6) . substr(time(), -4);
//
//            
//
//            //
//            $m = array(
//                'audited' => 0,
//                'managed' => 1,
//                'pid' => 0,
//                'openid' => $openid,
//                'name' => trim(I('name')),
//                'wechatnum' => trim(I('wechatnum')),
//                'phone' => $phone,
//                'email' => trim(I('email')),
//                'idennum' => trim(I('idennum')),
//                'address' => $post_address,
//                'time' => time(),
//                'level' => $level,
//                'path' => 0,
//                'levname' => $levname,
//                'bossname' => '总部',
//                'headimgurl' => $headimgurl,
//                'nickname' => $_SESSION['nickname'],
//                'password' => $password,
//                'idennumimg' => I('idennumimg'),
//                'liveimg' => I('liveimg'),
//                'recommendID'   =>  0,
//                'authnum' => $authnum,
//                'rec_path' => 0
//            );
//
////            $this->ajaxReturn(array('status' => 2,'idennumimg'=>I('idennumimg'),'liveimg'=>I('liveimg')), 'json');return;
//            
//            //:add by z
//            if (M('distributor')->add($m)) {    //提交申请成功
//                
//                $dis_info = M('distributor')->where(array('openid'=>$openid))->field('id')->find();
//                $uid = $dis_info['id'];
//                
//                import('Lib.Action.User','App');
//                $User = new User();
//                $result = $User->update_distributor_bind($uid);
//                
//                $this->ajaxReturn(array('status' => 1), 'json');
//            } else {      //提交申请失败
//                $this->ajaxReturn(array('status' => 2), 'json');
//            }
//        } else if ($manager['managed'] !== 0) {    //该微信号已是联盟总代
//            if ($manager['managed'] == 1) {
//                $this->ajaxReturn(array('status' => 3), 'json');
//            } else if ($manager['managed'] == 2) {
//                $this->ajaxReturn(array('status' => 4), 'json');
//            } else {
//                //这里页面提示的是申请失败，待完善
//                $this->ajaxReturn(array('status' => 2), 'json');
//            }
//        } else {          //该微信号待审核
//            $this->ajaxReturn(array('status' => 5), 'json');
//        }
    }

    public function jssdkUpImg() {
        //通过微信jssdk图像接口下载图片保存到服务器
        $accessTokenfile = file_get_contents('./access_token.json');
        $accessTokens = json_decode($accessTokenfile, true);
        $accessToken = $accessTokens['access_token'];
        $serverId = trim($_POST['serverId']);
        $type = trim($_POST['type']);
        $del_img = substr(trim($_POST['del_img']), 1);
        $imgUrl = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$accessToken&media_id=$serverId";

        $img_name = md5(time() . rand()) . '.jpg';
        $fileName = './img/' . $type . '/' . $img_name;
        $showImg = '/img/' . $type . '/' . $img_name;
        $img = file_get_contents($imgUrl);
        $result = file_put_contents($fileName, $img);
        //删除上一张图片
        if (file_exists($del_img)) {
            unlink($del_img);
        }
        ob_end_clean();
        $this->ajaxReturn($showImg, 'JSON');
    }
    
    
    /**
     * 获取用户信息
     * 
     * @return json
     */
    public function get_user_ajax(){
        
        if( !IS_AJAX ){
            return FALSE;
        }

        $id = I('post.id');	

        if( empty($id) ){
            $id = $_SESSION['managerid'];
        }
        
        if( empty($id) ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            ];
            $this->ajaxReturn($result);
            return;
        }
        
        
        $manager = M('distributor')->where(array('id' => $id))->find();
        $cavconfig = M('distributor_certificate')->select();
        
        if (!empty($manager)) {
            
            foreach($cavconfig as $v){
              $temp = json_decode($v['value'],true);
              if($temp['hide']==1){
                $manager[$v['name']] = asterisk($manager[$v['name']]);
              }
            }
          
//          $manager['idennum'] = substr($manager['idennum'], 0, 6) . "******" . substr($manager['idennum'], -4, 4);
//          $manager['authnum'] = substr($manager['authnum'], 0, 3) . '*****';
//          $manager['phone'] = substr($manager['phone'], 0, 7) . "****";
//          $manager['wechatnum'] = substr($manager['wechatnum'], 0, 2) . "****";
        }
        
        if (empty($manager['start_time'])) {
            $manager['start_time'] = $manager['time'];
            $manager['start_time_format1'] = date('Y.m.d',$manager['start_time']);
            $manager['start_time_format2'] = date('Y-m-d',$manager['start_time']);
            $manager['start_time_format3'] = date('Y年m月d日',$manager['start_time']);
        }
        if (empty($manager['end_time'])) {
            $manager['end_time'] = $manager['time'] + 3600 * 24 * 365;
            $manager['end_time_format1'] = date('Y.m.d',$manager['end_time']);
            $manager['end_time_format2'] = date('Y-m-d',$manager['end_time']);
            $manager['end_time_format3'] = date('Y年m月d日',$manager['end_time']);
        }
        
        
        $result = [
            'code'  =>  1,
            'msg'   =>  '获取成功！',
            'info'  =>  $manager,
        ];
        $this->add_active_log;
        $this->ajaxReturn($result);
    }//end func get_user_ajax
    
    /**
     * 获取模型信息
     */
    public function get_userinfo_model(){
      if(!IS_AJAX){
        return FALSE;
      }
      $result = [
            'code' => 1,
            'info' => [
                      //字段名、中文解释、 是否开启、示例文字、   x轴、y轴、字体颜色、        字体样式   、 移动层级、是否隐藏部分文字
                       'name' => ['代理姓名','1','测试姓名','50','50','#000000','bold 20px Arial','1','0'],
                       'wechatnum' => ['微信号','1','测试微信号','50','50','#000000','bold 20px Arial','2','0'],
                       'idennum' => ['代理身份证号','1','440123201701011234','50','50','#000000','bold 20px Arial','3','0'],
                       'levname' => ['代理等级','1','一级经销商','50','50','#000000','bold 20px Arial','4','0'],
                       'authnum' => ['授权编号','1','1234(测试授权号)','50','50','#000000','bold 20px Arial','5','0'],
                       'phone' => ['代理电话号码','1','13504521352','50','50','#000000','bold 20px Arial','6','0'],
                       'bossname' => ['上级名称','1','总部','50','50','#000000','bold 20px Arial','7','0'],
                       'start_time_format1' => ['开始日期格式一','1','2017.01.01','50','50','#000000','bold 20px Arial','8','0'],
                       'start_time_format2' => ['开始日期格式二','0','2017-01-01','50','50','#000000','bold 20px Arial','9','0'],
                       'start_time_format3' => ['开始日期格式三','0','2017年1月1日','50','50','#000000','bold 20px Arial','10','0'],
                       'end_time_format1' => ['结束日期格式一','1','2018.01.01','50','50','#000000','bold 20px Arial','11','0'],
                       'end_time_format2' => ['结束日期格式二','0','2018-01-01','50','50','#000000','bold 20px Arial','12','0'],
                       'end_time_format3' => ['结束日期格式三','0','2018年1月1日','50','50','#000000','bold 20px Arial','13','0'],
                       'company' => ['公司地址','1','','50','50','#000000','bold 20px Arial','14','0'],
                      ],
            'msg' => '获取成功！',
          ];
      $this->add_active_log;
      $this->ajaxReturn($result);
    }
    
    
    //获取所有级别
    public function all_levname(){
        if( !IS_AJAX ){
             return FALSE;
        }

        $level_name = C("LEVEL_NAME");

        $result = [
            'code'  =>  1,
            'msg'   =>  '获取成功！',
            'info'  =>  $level_name,
        ];

        $this->ajaxReturn($result);
    }
    
//获取授权书配置    
    public function get_certificate_config(){
      if(!IS_AJAX){
        return FALSE;
      }
      $certificate = M('distributor_certificate');
      
      $cavconfig = $certificate->field('name,value')->select();
      
      $data = array();
      foreach($cavconfig as $v){
        if($v['name']=="bgImg"){
            $bgimg = stripslashes($v['value']); 
            $data[$v['name']][] = $bgimg;
        }else{
            $data[$v['name']][] = $v['value'];
        }
        
      }
      $cavconfig = json_encode($data);
      
      $result = [
        'code' => 1,
        'info' =>$cavconfig,
        'msg' =>"获取成功！",    
      ];
      $this->add_active_log;
      $this->ajaxReturn($result);
      
    }
    
    //获取授权书配置
//  public function get_certificate_config(){
//    if(!IS_AJAX){
//      return FALSE;
//    }
//    $cavConfig = C('CAVCONFIG');
//    
//    $result = [
//      'code' => 1,
//      'info' =>json_encode($cavConfig),
//      'msg' =>"获取成功！",    
//    ];
//    
//    $this->ajaxReturn($result);
//  }

}

?>