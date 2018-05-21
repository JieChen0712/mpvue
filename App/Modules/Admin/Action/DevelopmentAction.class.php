<?php

/**
 * 	微斯咖经销商后台——经销商管理
 */
class DevelopmentAction extends Action {

    //经销商申请表单
    public function apply() {
        $ct = I('ct');
        $level = I('level');
        $pid = I('pid');
        
        if( empty($_SESSION['oid']) ){
            $return_url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/Development/apply?ct=" . $ct.'&pid='.$pid.'&level='.$level;
            checkAuth('apply', I('get.ct'),'',$return_url);
//            session('openid', $_SESSION['oid']);
            session('headimgurl', $_SESSION['headimgurl']);
        }
        
        $not_apply = FALSE;
        
        $pid = decode(I('pid')); 
        $p_openid = '';
        if( !empty($pid) ){
            $p_openid = M('distributor')->getFieldByid($pid,'openid');
        }
        
        //还是本人的信息，无法提交申请
        if( !empty($p_openid) && $p_openid == $_SESSION['oid'] ){
            $not_apply = TRUE;
        }
        
        
        $manager['id'] = $pid; 
        
        $list = M('regulations')->find();
        $levnameArr = C('LEVEL_NAME');
//        $lev = session('level');
        $lev = decode(I('level'));
        
        
        session('pid', $pid);
        session('level', $level);
        
        $this->level = $lev;
        $this->ct = I('get.ct');
        $this->manager = $manager;
        $this->levname = $levnameArr[$lev];
        $this->list = $list;
        $this->not_apply = $not_apply;
        $this->display();
    }

    //经销商申请表单处理
    public function applyHandle() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }

        //session数据
        $openid = $_SESSION['oid'];
        $headimgurl = $_SESSION['headimgurl'];
        $pid = $_SESSION['pid'];
        $nickname = $_SESSION['nickname'];
        $sex = $_SESSION['sex'];

//        $this->ajaxReturn(array('test' => $openid), 'json');
//        return;
        //提交的数据
        $post_level = I('post.level');
        $post_name = trim(I('post.name'));
        $post_wechatnum = trim(I('post.wechatnum'));
        $post_phone = trim(I('post.phone'));
        $post_email = trim(I('post.email'));
        $post_idennum = trim(I('post.idennum'));
        $post_probably_address=trim(I('post.probably_address'));
        $post_addre = trim(I('post.addre'));
        $post_idennumimg = trim(I('post.idennumimg'));
        $post_liveimg = trim(I('post.liveimg'));
        $post_headimgurl = I('post.headimgurl');


        if( $pid == NULL ){
            $pid = I('post.pid');
        }

//$this->ajaxReturn(['code'=>  var_export($_SESSION,1)], 'json');die();

        //参数的逻辑判断
        if (strlen($post_phone) == '11') {
            $search = '/^(1[1|2|3|4|5|6|7|8|9][0-9])\d{8}$/';
            if (!preg_match($search, $post_phone)) {
                $return_result = [
                    'code'      =>  6,
                    'msg'       =>  '手机号码格式不对！',
                ];
                
                $this->ajaxReturn($return_result, 'json');
            }
        } else {
            $return_result = [
                'code'      =>  7,
                'msg'       =>  '手机号码格式不对！',
            ];
            $this->ajaxReturn($return_result, 'json');
        }
        
        if( C('IS_TEST') == TRUE ){
            $openid = rand(0,99).time();
        }

        if (!$openid) {
            //add by z
            setLog('申请pid为' . $pid . '的经销商没有获取到openid', 'no-openid');
            $return_result = [
                'code'      =>  3,
                'msg'       =>  '获取微信授权状态失败，请重试！',
            ];
            $this->ajaxReturn($return_result, 'json');
        }


        //获取省市区地址
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


        //
        $apply_info = array(
            'openid' => $openid,
            'headimgurl' => $headimgurl,
            'pid' => $pid,
            'nickname' => $nickname,
            'level' => $post_level,
            'name' => $post_name,
            'wechatnum' => $post_wechatnum,
            'phone' => $post_phone,
            'email' => $post_email,
            'idennum' => $post_idennum,
            'address' => $post_addre,
            'idennumimg' => $post_idennumimg,
            'liveimg' => $post_liveimg,
            'province'  =>  $province,
            'city'      =>  $city,
            'county'    =>  $county,
//            'headimgurl'    =>  $post_headimgurl,
            'sex'   =>  $sex,
        );
        
        import('Lib.Action.User','App');
        $User = new User();
        $return_result = $User->add($apply_info);
        
        //申请成为经销商的逻辑代码
//        $return_result = $this->applyCon($apply_info);


        $this->ajaxReturn($return_result, 'json');
    }


    //申请成为经销商的逻辑代码
    //已废弃
//    private function applyCon($info) {
//        $return_result = array();
//
//        if( empty($info) ){
//            $return_result = array('code' => 2,'msg'=>'提交的数据不完整');
//            return $return_result;
//        }
//        
//        //session数据
//        $openid = $info['openid'];
//        $headimgurl = $info['headimgurl'];
//        $pid = $info['pid'];
//        $nickname = $info['nickname'];
//        //提交的数据
//        $level = $info['level'];
//        $name = $info['name'];
//        $wechatnum = $info['wechatnum'];
//        $phone = $info['phone'];
//        $email = $info['email'];
//        $idennum = $info['idennum'];
//        $address = $info['address'];
//        $idennumimg = $info['idennumimg'];
//        $province = $info['province'];
//        $city = $info['city'];
//        $county = $info['county'];
//        $liveimg = $info['liveimg'];
//        //$headimgurl = $info['headimgurl'];
//        //常量
//        $level_name = C("LEVEL_NAME");
//        $distributor_model = M('distributor');
//
//
//        //先查询提交的经销商是否已有
//        $condition_sear = [
//            'openid'    =>  $openid,
//            '_logic'    =>  'or',
//            'wechatnum'  =>  $wechatnum,
//        ];
//        $manager = $distributor_model->where($condition_sear)->find();
//
//        if (!empty($manager)) {
//            
//            if( $manager['openid'] == $openid ){
//                setLog('openid' . $openid . '的代理再次提交申请','openid-the_same');
//            }
//            elseif( $manager['wechatnum'] == $wechatnum ){
//                $return_result = [
//                    'code'      =>  4,
//                    'msg'       =>  '您填写的微信号已被申请经销商，如有疑问，请联系总部！',
//                ];
//                return $return_result;
//            }
//            
//            if ($manager['audited'] == 1) {
//                //该微信号已是经销商
//                $return_result = [
//                    'code'      =>  5,
//                    'msg'       =>  '您当前的微信已申请成为经销商，无法再次申请！',
//                ];
//                return $return_result;
//            } else {
//                //该微信号待审核
//                $return_result = [
//                    'code'      =>  8,
//                    'msg'       =>  '您当前的微信已申请成为经销商，正在审核中，请耐心等待！',
//                ];
//                return $return_result;
//            }
//        } 
//        
//         
//        //查询未来授权人的级别和姓名
//        $parent = $distributor_model->where(array('id' => $pid))->find();
//
//        if( empty($parent) ){
//            $return_result = [
//                'code'      =>  9,
//                'msg'       =>  '对不起，找不到您的推荐人信息，请重试并向您的推荐人确认！',
//            ];
//            return $return_result;
//        }
//
//        $levname = $level_name[$level];
//
//        //$parent_bossname = $parent['name'];
//        $pname = $parent['name'];
//        $bossname = $pname;
//        //$parent_pid = $parent['pid'];
//        $parent_openid = $parent['openid'];
//        $parent_path = $parent['path'];
//        $parent_level = $parent['level'];
//        $parent_pid = $parent['pid'];
//        $parent_rec_path = $parent['rec_path'];
//        $path = 0;
//        $isRecommend = '0'; //是否被推荐，默认为0
//        $audited = 0; //审核状态默认为0（未审核）
//        $isInternal = '0';//是否内部人员，默认为0
//        $grow_model = C('GROW_MODEL');//发展方式
//        
//        $recommendID = $pid; //推荐人
//
//        //----------根据不同系统的需求更改------------------
//        //改为所有级别都能并只能推荐最高级别
////            //只有最高级别才能推荐最高级别
////            if( $level == 1 && $parent_level != 1 ){
////                $returnInfo = array('status' => 2,'judgment'=>'e');
////                return $returnInfo;
////            }
////            
////            //除了最高级别能推荐同级外，其它级别不能推荐同级
////            if( $level != 1 && $level == $parent_level ){
////                $returnInfo = array('status' => 2,'judgment'=>'f');
////                return $returnInfo;
////            }
//
//
//        //判断是推荐还是发展下级
//        //级别由高到低是1,2,3...
////            if ( $level == 1 ) {
////                $isRecommend = '1'; //记录为被推荐用户
////                $audited = '2';//直接由总部审核
////            }
//
//
//        if( $level < $parent_level ){
//            //在没有低推高的情况下是不允许申请的
//            $not_grow = [1,2];//不能发展的情况
//            if(in_array($grow_model, $not_grow) ){
//                $return_result = [
//                    'code'      =>  10,
//                    'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
//                ];
//                return $return_result;
//            }
//            elseif( $grow_model == 4 ){
//                $GROW_MODEL_LEVEL = C('GROW_MODEL_LEVEL');
//                
//                $spe_grow_model = isset($GROW_MODEL_LEVEL[$parent_level])?$GROW_MODEL_LEVEL[$parent_level]:$GROW_MODEL_LEVEL[0];
//                
//                if( in_array($spe_grow_model,$not_grow) ){
//                    $return_result = [
//                        'code'      =>  12,
//                        'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
//                    ];
//                    return $return_result;
//                }
//            }
//            else{
//                $return_result = [
//                    'code'      =>  13,
//                    'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
//                ];
//                return $return_result;
//            }
//            
//
//            $isRecommend = '1';
//            import('Lib.Action.User','App');
//            $User = new User();
//            $get_pid_result = $User->get_recommend_hight_level_parent($level,$parent);
//
//            if( $get_pid_result['code'] != 1 ){
////                $returnInfo = array('status' => 2,'judgment'=>'j');
//                return $get_pid_result;
//            }
//
//            $pid = $get_pid_result['info']['pid'];
//            $pname = $bossname = $get_pid_result['info']['name'];
//            $parent_openid = !empty($get_pid_result['info']['openid'])?$get_pid_result['info']['openid']:$parent_openid;
//            $parent_path = $get_pid_result['info']['path'];
//            $parent_rec_path = $get_pid_result['info']['rec_path'];
//        }
//
//
//        if( $level == $parent_level ){
//            //在没有平级推的情况下是不允许申请的
//            $not_grow = [1];//不能发展的情况
//            if(in_array($grow_model, $not_grow) ){
//                $return_result = [
//                    'code'      =>  11,
//                    'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
//                ];
//                return $return_result;
//            }
//            elseif( $grow_model == 4 ){
//                $GROW_MODEL_LEVEL = C('GROW_MODEL_LEVEL');
//                
//                $spe_grow_model = isset($GROW_MODEL_LEVEL[$parent_level])?$GROW_MODEL_LEVEL[$parent_level]:$GROW_MODEL_LEVEL[0];
//                
//                if( in_array($spe_grow_model,$not_grow) ){
//                    $return_result = [
//                        'code'      =>  14,
//                        'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
//                    ];
//                    return $return_result;
//                }
//            }
//            else{
//                $return_result = [
//                    'code'      =>  15,
//                    'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
//                ];
//                return $return_result;
//            }
//
//
//            $isRecommend = '1';
//            import('Lib.Action.User','App');
//            $User = new User();
//            $get_pid_result = $User->get_recommend_hight_level_parent($level,$parent);
//
//            if( $get_pid_result['code'] != 1 ){
////                $return_result = array('status' => 2,'judgment'=>'j');
//                return $get_pid_result;
//            }
//
//            $pid = $get_pid_result['info']['pid'];
//            $pname = $bossname = $get_pid_result['info']['name'];
//            $parent_openid = !empty($get_pid_result['info']['openid'])?$get_pid_result['info']['openid']:$parent_openid;
//            $parent_path = $parent['path'];
//            // $parent_rec_path = $get_pid_result['info']['rec_path'];
//        }
//
//        //----------end根据不同系统的需求更改------------------
//
//
//
//        //最高级的归属肯定是总部
//        if ($level == 1) {
//            //如果是最高级别的经销商申请
//            $managed = 1;
//
//            //如果是推荐后最高级的上级还是推荐人则这里屏蔽
//            //$pid = 0;
//            //$bossname = '总部';
//        } else {
//            $managed = 0;
//        }
//
//        if( $pid != 0 ){
//            $pdpath = explode('-', $parent_path);
//            if ($pdpath[1]) {
//                $tallest = $pdpath[1];
//            } else {
//                $tallest = $pid;
//            }
//
//            //当前申请经销商的path（PATH是经销商链）
//            $path = $parent_path . '-' . $pid;
//        }
//        else{
//            $bossname = '总部';
//            $pname = '';
//            $tallest = 0;
//        }
//
//        //如果最高级别为总部，则总是为总部审核
//        if( $pid == 0 ){
//            $audited = 2;
//        }
//
//        if( $recommendID == 0 ){
//            $rec_path = '0';
//        }
//        else{
//            $parent_rec_path = $distributor_model->where(['id' => $recommendID])->getField('rec_path');
//            $rec_path = $parent_rec_path.'-'.$recommendID;
//        }
//
//
//        $password = md5(substr($phone, -6)); //默认密码
//
//        $authnum = substr($phone, -6) . substr(time(), -4); //生成授权号
////            //同级发展记录同级上级的id(同级不能发货可以由同级的上级发货)
////            $indirectId = 0;
////            if ($level == $parent_level && $level != 1) {
////                $high = $distributor_model->where(array('id' => $parent_pid))->find();
////                $indirectId = $high['id'];
////                setLog('id为' . $pid . '的经销商同级发展了手机号为' . $phone . '的经销商记录同级上级的id方便发货'
////                        . $high['id'], 'same-recommend-same');
////            }
//
//        $data = array(
//            'managed' => $managed,
//            'audited' => $audited,
//            'pid' => $pid,
//            'openid' => $openid,
//            'name' => $name,
//            'wechatnum' => $wechatnum,
//            'phone' => $phone,
//            'email' => $email,
//            'idennum' => $idennum,
//            'address' => $address,
//            'time' => time(),
//            'level' => $level,
//            'levname' => $levname,
//            'bossname' => $bossname,
//            'pname' => $pname,
//            'headimgurl' => $headimgurl,
//            'nickname' => $nickname,
//            'path' => $path,
//            'rec_path'  =>  $rec_path,
//            'password' => $password,
//            'idennumimg' => $idennumimg,
//            'liveimg' => $liveimg,
//            'recommendID' => $recommendID,
//            'tallestID' => $tallest,
//            'isRecommend' => $isRecommend,
//            'isInternal' => $isInternal,
//            'authnum' => $authnum,
//            'province'  =>  $province,
//            'city'      =>  $city,
//            'county'    =>  $county,
//                //'indirectId' => $indirectId,
//        );
//
//        $add_res = $distributor_model->add($data);
//
//        if ($add_res) {//提交申请成功
//
//            //清除团队缓存
//            clean_team_path_cache();
//
//            $dis_info = $distributor_model->where(array('openid'=>$openid))->field('id')->find();
//            $uid = $dis_info['id'];
//
//            import('Lib.Action.User','App');
//            $User = new User();
//            $result = $User->update_distributor_bind($uid);
//
//
//            //上级审核的情况才需要给上级发消息
//            if( $audited == 1 ){
//                //----------公众号推送给申请人的上级--------
//                $touser = $parent_openid;
//                $keyword1 = $name;
//                $sendTime = date("Y-m-d H:i:s");
//                $template_id = C('SQ_MB');
//                $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
//                $sendData = array(
//                    'first' => array('value' => ("经销商申请通知"), 'color' => "#CC0000"),
//                    'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
//                    'keyword2' => array('value' => ("联系方式：" . $phone . "，申请时间：" . $sendTime), 'color' => '#000'),
//                    'remark' => array('value' => ("点击进行审核"), 'color' => '#CC0000')
//                );
//                //$sendMsg = new \Org\Net\OrderPush('wx9d1a5134c0eaeb2e', '587a2933829facd9b56ff74c83512ba4');
//    //            import('ORG.Net.OrderPush');
//    //            $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));
//    //            $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');
//
//                import("Wechat.Wechat", APP_PATH);
//                $options = array(
//                    'token' => C('APP_TOKEN'), //填写您设定的key
//                    'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
//                    'appid' => C('APP_ID'), //填写高级调用功能的app id
//                    'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
//                );
//                $this->wechat_obj = new Wechat($options);
//                $template = array(
//                    'touser' => $touser,
//                    'template_id' => $template_id,
//                    'url' => $url,
//                    'topcolor' => '#7B68EE',
//                    'data' => $sendData
//                );
//
//                $this->wechat_obj->sendTemplateMessage($template);
//
//                //----------end公众号推送给申请人的上级--------
//            }
//            
//            
////                $this->ajaxReturn(array('status' => 1), 'json');
//
//            $return_result = [
//                'code'      =>  1,
//                'msg'       =>  '申请成功！',
//            ];
//        } else {      //提交申请失败
////                $this->ajaxReturn(array('status' => 2), 'json');
//            $return_result = [
//                'code'      =>  2,
//                'msg'       =>  '对不起，系统繁忙，请重试！',
//                'info'  =>$data,
//            ];
//        }
//        
//
//        return $return_result;
//    }



    //----------------旧版申请--------------------

    //经销商申请表单
    public function newapply() {
        if( empty($_SESSION['oid']) ){
            checkAuth('apply', I('get.ct'));
//            session('openid', $_SESSION['oid']);
            session('headimgurl', $_SESSION['headimgurl']);
        }

        $levnameArr = C('LEVEL_NAME');
        $lev = session('level');
        $this->level = $lev;
        $this->ct = I('get.ct');
        $this->levname = $levnameArr[$lev];
        $this->display();
    }

    //经销商申请表单处理
    public function newapplyHandle() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }

        //session数据
        $openid = $_SESSION['oid'];
        $headimgurl = $_SESSION['headimgurl'];
        $pid = $_SESSION['pid'];
        $nickname = $_SESSION['nickname'];

//        $this->ajaxReturn(array('test' => $openid), 'json');
//        return;
        //提交的数据
        $post_level = I('post.level');
        $post_name = trim(I('post.name'));
        $post_wechatnum = trim(I('post.wechatnum'));
        $post_phone = trim(I('post.phone'));
        $post_email = trim(I('post.email'));
        $post_idennum = trim(I('post.idennum'));
        $post_address = trim(I('post.address'));
        $post_idennumimg = trim(I('post.idennumimg'));
        $post_liveimg = I('post.liveimg');
        $post_headimgurl = I('post.headimgurl');
        
//$this->ajaxReturn(['code'=>  var_export($_SESSION,1)], 'json');die();

        //参数的逻辑判断
        if (strlen($post_phone) == '11') {
            $search = '/^(1[1|2|3|4|5|6|7|8|9][0-9])\d{8}$/';
            if (!preg_match($search, $post_phone)) {
                $return_result = [
                    'code'      =>  6,
                    'msg'       =>  '手机号码格式不对！',
                ];

                $this->ajaxReturn($return_result, 'json');
            }
        } else {
            $return_result = [
                'code'      =>  7,
                'msg'       =>  '手机号码格式不对！',
            ];
            $this->ajaxReturn($return_result, 'json');
        }

        if( C('IS_TEST') == TRUE ){
            $openid = rand(0,99).time();
        }

        if (!$openid) {
            //add by z
            setLog('申请pid为' . $pid . '的经销商没有获取到openid', 'no-openid');
            $return_result = [
                'code'      =>  3,
                'msg'       =>  '获取微信授权状态失败，请重试！',
            ];
            $this->ajaxReturn($return_result, 'json');
        }


        //
        $apply_info = array(
            'openid' => $openid,
            'headimgurl' => $headimgurl,
            'pid' => $pid,
            'nickname' => $nickname,
            'level' => $post_level,
            'name' => $post_name,
            'wechatnum' => $post_wechatnum,
            'phone' => $post_phone,
            'email' => $post_email,
            'idennum' => $post_idennum,
            'address' => $post_address,
            'idennumimg' => $post_idennumimg,
            'liveimg' => $post_liveimg,
//            'headimgurl'    =>  $post_headimgurl
        );

        //申请成为经销商的逻辑代码
        $return_result = $this->applyCon($apply_info);


        $this->ajaxReturn($return_result, 'json');
    }


    //申请成为经销商的逻辑代码
    private function newapplyCon($info) {
        $returnInfo = array();

        if( empty($info) ){
            $returnInfo = array('status' => 2,'judgment'=>'a');
        }

        //session数据
        $openid = $info['openid'];
        $headimgurl = $info['headimgurl'];
        $pid = $info['pid'];
        $nickname = $info['nickname'];
        //提交的数据
        $level = $info['level'];
        $name = $info['name'];
        $wechatnum = $info['wechatnum'];
        $phone = $info['phone'];
        $email = $info['email'];
        $idennum = $info['idennum'];
        $address = $info['address'];
        $idennumimg = $info['idennumimg'];
        $liveimg = $info['liveimg'];
        //$headimgurl = $info['headimgurl'];
        //常量
        $level_name = C("LEVEL_NAME");
        $distributor_model = M('distributor');


        //先查询提交的经销商是否已有
        $condition_sear = [
            'openid'    =>  $openid,
            '_logic'    =>  'or',
            'wechatnum'  =>  $wechatnum,
        ];
        $manager = $distributor_model->where($condition_sear)->find();

        if (!empty($manager)) {

            if( $manager['openid'] == $openid ){
                setLog('openid' . $openid . '的代理再次提交申请','openid-the_same');
            }
            elseif( $manager['wechatnum'] == $wechatnum ){
                $return_result = [
                    'code'      =>  4,
                    'msg'       =>  '您填写的微信号已被申请经销商，如有疑问，请联系总部！',
                ];
                return $return_result;
            }

            if ($manager['audited'] == 1) {
                //该微信号已是经销商
                $return_result = [
                    'code'      =>  5,
                    'msg'       =>  '您当前的微信已申请成为经销商，无法再次申请！',
                ];
                return $return_result;
            } else {
                //该微信号待审核
                $return_result = [
                    'code'      =>  8,
                    'msg'       =>  '您当前的微信已申请成为经销商，正在审核中，请耐心等待！',
                ];
                return $return_result;
            }
        }





        //查询未来授权人的级别和姓名
        $parent = $distributor_model->where(array('id' => $pid))->find();

        if( empty($parent) ){
            $return_result = [
                'code'      =>  9,
                'msg'       =>  '对不起，找不到您的推荐人信息，请重试并向您的推荐人确认！',
            ];
            return $returnInfo;
        }

        $levname = $level_name[$level];

        //$parent_bossname = $parent['name'];
        $pname = $parent['name'];
        $bossname = $pname;
        //$parent_pid = $parent['pid'];
        $parent_openid = $parent['openid'];
        $parent_path = $parent['path'];
        $parent_level = $parent['level'];
        $parent_pid = $parent['pid'];
        $parent_rec_path = $parent['rec_path'];
        $path = 0;
        $isRecommend = '0'; //是否被推荐，默认为0
        $audited = 0; //审核状态默认为0（未审核）
        $isInternal = '0';//是否内部人员，默认为0
        $grow_model = C('GROW_MODEL');//发展方式

        $recommendID = $pid; //推荐人

        //----------根据不同系统的需求更改------------------
        //改为所有级别都能并只能推荐最高级别
//            //只有最高级别才能推荐最高级别
//            if( $level == 1 && $parent_level != 1 ){
//                $returnInfo = array('status' => 2,'judgment'=>'e');
//                return $returnInfo;
//            }
//
//            //除了最高级别能推荐同级外，其它级别不能推荐同级
//            if( $level != 1 && $level == $parent_level ){
//                $returnInfo = array('status' => 2,'judgment'=>'f');
//                return $returnInfo;
//            }


        //判断是推荐还是发展下级
        //级别由高到低是1,2,3...
//            if ( $level == 1 ) {
//                $isRecommend = '1'; //记录为被推荐用户
//                $audited = '2';//直接由总部审核
//            }

        if( $level < $parent_level ){
            //在没有低推高的情况下是不允许申请的
            if( $grow_model != 3 ){
                $return_result = [
                    'code'      =>  10,
                    'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
                ];
                return $returnInfo;
            }



            $isRecommend = '1';
            import('Lib.Action.User','App');
            $User = new User();
            $get_pid_result = $User->get_recommend_hight_level_parent($level,$parent);

            if( $get_pid_result['code'] != 1 ){
//                $returnInfo = array('status' => 2,'judgment'=>'j');
                return $get_pid_result;
            }

            $pid = $get_pid_result['info']['pid'];
            $pname = $bossname = $get_pid_result['info']['name'];
            $parent_openid = !empty($get_pid_result['info']['openid'])?$get_pid_result['info']['openid']:$parent_openid;
            $parent_path = $get_pid_result['info']['path'];
            $parent_rec_path = $get_pid_result['info']['rec_path'];
        }


        if( $level == $parent_level ){
            //在没有平级推的情况下是不允许申请的
            if( $grow_model == 1 ){
                $return_result = [
                    'code'      =>  11,
                    'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
                ];
                return $returnInfo;
            }



            $isRecommend = '1';
            import('Lib.Action.User','App');
            $User = new User();
            $get_pid_result = $User->get_recommend_hight_level_parent($level,$parent);

            if( $get_pid_result['code'] != 1 ){
                $returnInfo = array('status' => 2,'judgment'=>'j');
                return $returnInfo;
            }

            $pid = $get_pid_result['info']['pid'];
            $pname = $bossname = $get_pid_result['info']['name'];
            $parent_openid = !empty($get_pid_result['info']['openid'])?$get_pid_result['info']['openid']:$parent_openid;
            $parent_path = $get_pid_result['info']['path'];
            $parent_rec_path = $get_pid_result['info']['rec_path'];
        }

        //----------end根据不同系统的需求更改------------------



        //最高级的归属肯定是总部
        if ($level == 1) {
            //如果是最高级别的经销商申请
            $managed = 1;

            //如果是推荐后最高级的上级还是推荐人则这里屏蔽
            //$pid = 0;
            //$bossname = '总部';
        } else {
            $managed = 0;
        }

        if( $pid != 0 ){
            $pdpath = explode('-', $parent_path);
            if ($pdpath[1]) {
                $tallest = $pdpath[1];
            } else {
                $tallest = $pid;
            }

            //当前申请经销商的path（PATH是经销商链）
            $path = $parent_path . '-' . $pid;
        }
        else{
            $bossname = '总部';
            $pname = '';
            $tallest = 0;
        }

        //如果最高级别为总部，则总是为总部审核
        if( $pid == 0 ){
            $audited = 2;
        }

        if( $recommendID == 0 ){
            $rec_path = '0';
        }
        else{
            $rec_path = $parent_rec_path.'-'.$recommendID;
        }


        $password = md5(substr($phone, -6)); //默认密码

        $authnum = substr($phone, -6) . substr(time(), -4); //生成授权号
//            //同级发展记录同级上级的id(同级不能发货可以由同级的上级发货)
//            $indirectId = 0;
//            if ($level == $parent_level && $level != 1) {
//                $high = $distributor_model->where(array('id' => $parent_pid))->find();
//                $indirectId = $high['id'];
//                setLog('id为' . $pid . '的经销商同级发展了手机号为' . $phone . '的经销商记录同级上级的id方便发货'
//                        . $high['id'], 'same-recommend-same');
//            }

        $data = array(
            'managed' => $managed,
            'audited' => $audited,
            'pid' => $pid,
            'openid' => $openid,
            'name' => $name,
            'wechatnum' => $wechatnum,
            'phone' => $phone,
            'email' => $email,
            'idennum' => $idennum,
            'address' => $address,
            'time' => time(),
            'level' => $level,
            'levname' => $levname,
            'bossname' => $bossname,
            'pname' => $pname,
            'headimgurl' => $headimgurl,
            'nickname' => $nickname,
            'path' => $path,
            'rec_path'  =>  $rec_path,
            'password' => $password,
            'idennumimg' => $idennumimg,
            'liveimg' => $liveimg,
            'recommendID' => $recommendID,
            'tallestID' => $tallest,
            'isRecommend' => $isRecommend,
            'isInternal' => $isInternal,
            'authnum' => $authnum,
            //'indirectId' => $indirectId,
        );

        $add_res = $distributor_model->add($data);

        if ($add_res) {//提交申请成功

            $dis_info = $distributor_model->where(array('openid'=>$openid))->field('id')->find();
            $uid = $dis_info['id'];

            import('Lib.Action.User','App');
            $User = new User();
            $result = $User->update_distributor_bind($uid);


            //----------公众号推送给申请人的上级--------
            $touser = $parent_openid;
            $keyword1 = $name;
            $sendTime = date("Y-m-d H:i:s");
            $template_id = C('SQ_MB');
            $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
            $sendData = array(
                'first' => array('value' => ("经销商申请通知"), 'color' => "#CC0000"),
                'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
                'keyword2' => array('value' => ("联系方式：" . $phone . "，申请时间：" . $sendTime), 'color' => '#000'),
                'remark' => array('value' => ("点击进行审核"), 'color' => '#CC0000')
            );
            //$sendMsg = new \Org\Net\OrderPush('wx9d1a5134c0eaeb2e', '587a2933829facd9b56ff74c83512ba4');
//            import('ORG.Net.OrderPush');
//            $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));
//            $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');

            import("Wechat.Wechat", APP_PATH);
            $options = array(
                'token' => C('APP_TOKEN'), //填写您设定的key
                'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
                'appid' => C('APP_ID'), //填写高级调用功能的app id
                'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
            );
            $this->wechat_obj = new Wechat($options);
            $template = array(
                'touser' => $touser,
                'template_id' => $template_id,
                'url' => $url,
                'topcolor' => '#7B68EE',
                'data' => $sendData
            );

            $this->wechat_obj->sendTemplateMessage($template);

            //----------end公众号推送给申请人的上级--------
//                $this->ajaxReturn(array('status' => 1), 'json');

            $return_result = [
                'code'      =>  1,
                'msg'       =>  '申请成功！',
            ];
        } else {      //提交申请失败
//                $this->ajaxReturn(array('status' => 2), 'json');
            $return_result = [
                'code'      =>  2,
                'msg'       =>  '对不起，系统繁忙，请重试！',
            ];
        }


        return $return_result;
    }
    //--------------end 旧版申请--------------------
    /**
     * 自动注册
     */
    public function auto_sign_up(){
        if( empty($_SESSION['wechatinfo']) || empty($_SESSION['oid']) ){
            $return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);
            checkAuth('index','','',$return_url);
        }
        
        
        $data = I('data');
        $data = tiriDecode($data);
        $data = unserialize($data);
        
//        print_r($data);return;
        
        if( empty($data) ){
            error_tip('邀请链接有误，无法注册！');
            return;
        }
        
        
        $distributor_obj = M('distributor');
        
        $wechatinfo = $_SESSION['wechatinfo'];
        $openid = empty($_SESSION['oid'])?$wechatinfo['openid']:$_SESSION['oid'];
        
//        print_r($wechatinfo);return;  
        
        $condition = [
            'openid'    =>  $openid,
        ];
        
        $dis_info = $distributor_obj->field('name')->where($condition)->find();
        
        if( !empty($dis_info) ){
//            error_tip('您已经是经销商（'.$dis_info['name'].'）了！');
            error_tip('您已经是经销商了，或者请退出登录后再尝试！');
            return;
        }
        
        
        import('Lib.Action.User','App');
        $User = new User();
        
        $LEVEL_NUM = C('LEVEL_NUM');
        $default_level = $LEVEL_NUM-1;
        $audited = 1;
        $level = !empty($data['level'])?$data['level']:$default_level;
        $wechatnum = !empty($data['wechatnum'])?$data['wechatnum']:'特邀注册-'.rand(10,99).time();
        $phone = $data['phone'];
        $email = $data['email'];
        $idennum = $data['idennum'];
        $deadline = $data['deadline'];
        $cur_time = date('Ymd');
        $name = !empty($data['name'])?$data['name']:$wechatinfo['nickname'];
        $pid = !empty($data['pid'])?$data['pid']:0;
        
        if( $cur_time > $deadline ){
            error_tip('特邀链接已过期！');
            return;
        }
        
        
        $info = [
            'openid'        =>  $openid,
            'headimgurl'    =>  $wechatinfo['headimgurl'],
            'pid'           =>  $pid,
            'nickname'      =>  $wechatinfo['nickname'],
            'level'         =>  $level,
            'name'          =>  $name,
            'wechatnum'     =>  $wechatnum,
            'phone'         =>  $phone,
            'email'         =>  $email,
            'idennum'       =>  $idennum,
            'address'       =>  $wechatinfo['country'].'-'.$wechatinfo['province'].'-'.$wechatinfo['city'],
            'idennumimg'    =>  '',
            'liveimg'       =>  '',
            'audited'       =>  $audited,
            'recommendID'   =>  0,
        ];
        
        if( empty($phone) ){
            $info['password'] = '123456';
        }
        
        
        $add_result = $User->add($info,$for='radmin_invite');
        
        if( $add_result['status'] == 1 ){
            //echo "<script>alert('特邀注册成功!');window.location.href='".__APP__."/Admin/user/index'; </script>";
            $url = __APP__.'/Admin/user/index';
            $this->redirect($url);
        }
        else{
//            print_r($add_result);return;
            error_tip($add_result['msg']);
        }
    }//end func auto_sign_up
    
    

    /* ajax时间请求倒计时 */

    public function countdown() {
        $row = I('get.ct');
        $ct = decode($row); //解密时间
        $tcountdown = ($ct + 10000 - time());
        $this->ajaxReturn($tcountdown, 'json');
    }

}

?>