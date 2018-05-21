<?php

/**
 * 	微斯咖经销商后台——经销商管理
 */
class ManageAction extends CommonAction {

    //经销商管理页面
    public function index() {

        $id = $this->uid;
        $field = array('id','authnum', 'name', 'level', 'levname','status');
        //待审核经销商
        $pending_m = M('distributor')->where(array('pid' => $id, 'audited' => 0, 'managed' => 0))->field($field)->select();
//      if (!$pending_m) {
//          header("Content-Type:text/html;charset=utf-8");
//          echo "<script>alert('对不起,您没有要审核的经销商!');history.go(-1);</script>";
//          exit();
//      }
        $this->pending_m = $pending_m;

        $this->display();
    }

    //页面上写了，但是实际没有的路径，8.19增加
    public function get_not_audit(){
        $id = $this->uid;
//        $field = array('id','authnum', 'name', 'level', 'levname');
        $pending_m = M('distributor')->where(array('pid' => $id, 'audited' => 0))->select();

        $this->ajaxReturn($pending_m);
    }


    //经销商申请表单
    public function apply() {
        checkAuth('apply', I('get.ct'));
        session('openid', $_SESSION['oid']);
        session('headimgurl', $_SESSION['headimgurl']);
        $levnameArr = C('LEVEL_NAME');
        $lev = session('level');
        
        $this->level = $lev;
        $this->ct = I('get.ct');
        $this->levname = $levnameArr[$lev];
        $this->display();
    }

    //经销商申请表详细资料
    public function detail() {

        $id = I('id');
        $row = M('distributor')->field('id,name,wechatnum,phone,email,levname,idennum,address,headimgurl,idennumimg,liveimg')->find($id);
        $this->row = $row;
        $this->display();
    }

    //经销商申请表单处理
    public function applyHandle() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }

        //session数据
        $openid = _session('openid');
        $headimgurl = _session('headimgurl');
        $pid = _session('pid');
        $nickname = _session('nickname');

//        $this->ajaxReturn(array('test' => $openid), 'json');
//        return;
        //提交的数据
        $post_level = $this->_post('level');
        $post_name = $this->_post('name');
        $post_wechatnum = $this->_post('wechatnum');
        $post_phone = $this->_post('phone');
        $post_email = $this->_post('email');
        $post_idennum = $this->_post('idennum');
        $post_address = $this->_post('address');
        $post_idennumimg = $this->_post('idennumimg');
        $post_liveimg = $this->_post('liveimg');
        $post_headimgurl = $this->_post('headimgurl');


        //参数的逻辑判断
        if (strlen($post_phone) == '11') {
            $search = '/^(1[1|2|3|4|5|6|7|8|9][0-9])\d{8}$/';
            if (!preg_match($search, $post_phone)) {
                $this->ajaxReturn(array('status' => 6), 'json');
            }
        } else {
            $this->ajaxReturn(array('status' => 6), 'json');
        }

        if (!$openid) {
            //add by z
            setLog('申请pid为' . $pid . '的经销商没有获取到openid', 'no-openid');
            $this->ajaxReturn(array('status' => 0), 'json');
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
        $ajaxReturn = $this->applyHandleCon($apply_info);


        $this->ajaxReturn($ajaxReturn, 'json');
    }

    //新版经销商申请
    //------------------***********----------------
    // 经销商申请表单
//    public function apply_agent() {
//      //  checkAuth('apply', I('get.ct'));
//        session('openid', $_SESSION['oid']);
//        session('headimgurl', $_SESSION['headimgurl']);
//        $levnameArr = C('LEVEL_NAME');
//        $lev = session('level');
//        $this->level = $lev;
//        $this->ct = I('get.ct');
//        $this->levname = $levnameArr[$lev];
//        $this->display();
//    }
//
//
//    public function newApplyHandle(){
//        if(!IS_AJAX){
//            return FALSE;
//        }
//        $openid = session('openid');
//        $headimgurl = session('headimgurl');
//        $pid = session('pid');
//        $nickname = session('nickname');
//        $openimg=C(IS_SUBMIT_ID_CARD_IMG);
//
//        $post_level = trim(I('post.level'));
//        $post_name = trim(I('post.name'));
//        $post_wechatnum = trim(I('post.wechat'));
//        $post_phone = trim(I('post.phone'));
//        $post_email = trim(I('post.email'));
//        $post_idennum = trim(I('post.identityCard'));
//        $post_probablyAddress = trim(I('post.probablyAddress'));
//        $post_address = trim(I('post.address'));
//        $post_idennumimg = trim(I('post.fileUpload'));
//
//
//        if ($openimg == 1) {
//            if ($post_idennumimg == '') {
//                $response['code'] = 2;
//                $response['msg'] ="对不起，请上传图片！" ;
//                return  $this->ajaxReturn($response,'json');
//            }
//        }
//       if($post_name == '' ||$post_wechatnum == '' ||$post_phone == '' ||$post_email == '' ||$post_idennum == '' ||$post_probablyAddress == '' ||$post_address == ''){
//            $response['code'] = 3;
//            $response['msg'] ="对不起，信息填写不完整！" ;
//            return  $this->ajaxReturn($response,'json');
//        }
//
//
//        if(strlen($post_idennum) != 18){
//            $response['stacodetus'] = 4;
//            $response['msg'] ="对不起，请输入18位身份证！" ;
//            return  $this->ajaxReturn($response,'json');
//        }
//
//        if (strlen($post_phone) == '11') {
//            $search = '/^(1[3|5|7|8|][0-9])\d{8}$/';
//            if (!preg_match($search, $post_phone)) {
//                $this->ajaxReturn(array('status' => 6), 'json');
//            }
//        }
//
//        if (!$openid) {
//            //add by z
//            setLog('申请pid为' . $pid . '的经销商没有获取到openid', 'no-openid');
//            $this->ajaxReturn(array('status' => 0), 'json');
//        }
//
//        $apply_info = array(
//            'openid' => $openid,
//            'headimgurl' => $headimgurl,
//            'pid' => $pid,
//            'nickname' => $nickname,
//            'level' => $post_level,
//            'name' => $post_name,
//            'wechatnum' => $post_wechatnum,
//            'phone' => $post_phone,
//            'email' => $post_email,
//            'idennum' => $post_idennum,
//            'address' => $post_address,
//            'idennumimg' => $post_idennumimg,
//
//        );
//
//        //申请成为经销商的逻辑代码
//        $ajaxReturn = $this->applyHandleCon($apply_info);
//
//
//        $this->ajaxReturn($ajaxReturn, 'json');
//
//
//    }

//-------------*********************---------------------------
    //申请成为经销商的逻辑代码
    private function applyHandleCon($info) {
        $returnInfo = array();

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
        $manager = $distributor_model->where(array('openid' => $openid))->find();

        if (!empty($manager)) {
            if ($manager['audited'] == 1) {
                //该微信号已是经销商
                $returnInfo = array('status' => 3);
            } else {
                //该微信号待审核
                $returnInfo = array('status' => 4);
            }
        } else {
            //查询未来授权人的级别和姓名
            $parent = $distributor_model->where(array('id' => $pid))->find();
            $levname = $level_name[$level];

            //$parent_bossname = $parent['name'];
            $pname = $parent['name'];
            $bossname = $pname;
            //$parent_pid = $parent['pid'];
            $parent_openid = $parent['openid'];
            $parent_path = $parent['path'];
            $parent_level = $parent['level'];
            $path = 0;
            $isRecommend = 0; //是否被推荐，默认为0
            $audited = 0;//审核状态默认为0（未审核）


            //判断是推荐还是发展下级
            //( 被发展的经销商申请级别比合伙人高则为推荐；
            //反之，合伙人比被发展的经销商级别高则为下级经销商生成 )
            if( $level >= $parent_level ){
                $isRecommend = 1;//记录为被推荐用户
            }


            //最高级的归属肯定是总部
            if ($level == 1) {
                //如果是最高级别的经销商申请
                $managed = 1;
                $pid = 0;
                $bossname = '总部';
            } else {
                $managed = 0;

                $pdpath = explode('-', $parent_path);
                if ($pdpath[1]) {
                    $tallest = $pdpath[1];
                } else {
                    $tallest = $pid;
                }

                //当前申请经销商的path（PATH是经销商链）
                $path = $parent_path . '-' . $pid;
            }



            $password = md5(substr($phone, -6)); //默认密码
            $recommendID = $pid; //推荐人
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
                'password' => $password,
                'idennumimg' => $idennumimg,
                'liveimg' => $liveimg,
                'recommendID' => $recommendID,
                'tallestID' => $tallest,
                'isRecommend' => $isRecommend,
                'authnum' => $authnum,
                //'indirectId' => $indirectId,
            );

            $add_res = $distributor_model->add($data);

            if ($add_res) {//提交申请成功
                //----------公众号推送给申请人的上级--------
                $touser = $parent_openid;
                $keyword1 = $name;
                $sendTime = date("Y-m-d H:i:s");
                $template_id = C('SQ_MB');
                $url = "http://" . C('YM_DOMAIN') . "/index.php/admin/";
                $sendData = array(
                    'first' => array('value' => ("经销商申请通知"), 'color' => "#CC0000"),
                    'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
                    'keyword2' => array('value' => ("联系方式：" . $phone . "，申请时间：" . $sendTime), 'color' => '#000'),
                    'remark' => array('value' => ("点击进行审核"), 'color' => '#CC0000')
                );
                //$sendMsg = new \Org\Net\OrderPush('wx9d1a5134c0eaeb2e', '587a2933829facd9b56ff74c83512ba4');
//                import('ORG.Net.OrderPush');
//                $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));
//                $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');

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
                $returnInfo = array('status' => 1);
            } else {      //提交申请失败
//                $this->ajaxReturn(array('status' => 2), 'json');
                $returnInfo = array('status' => 2);
            }
        }

        return $returnInfo;
    }



//    //审核经销商
    public function audit() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }
        $distributor = M('distributor');
        $returnrate = M('returnrate');
        $recommend = M('recommend_rebate');
        vendor("phpqrcode.phpqrcode");

        $manager = I('pend');
        $flag = I('flag');
        $managers = explode('|', $manager);


        //搜索要审核的经销商进行状态判断
//        $condition_all = [
//            'id' => ['in',$managers],
//        ];
//        
//        $all_dis = $distributor->where($condition_all)->select();




        //拒绝
//        if(!$flag) {
//            $where = [
//                'id' => ['in', $managers],
//                'audited' => 0,
//            ];
//            if ($distributor->where($where)->delete()) {
//                $this->ajaxReturn(['status' => 1], 'json');
//            }
//            $this->ajaxReturn(['status' => 0], 'json');
//        }

        $field = '';
        $rebate_error = array();
        $audit_way = C('AUDIT_WAY');//1.直接上级审核通过;2.上级审核后总部审
        $audit_way_level = C('AUDIT_WAY_LEVEL');

//        import('ORG.Net.OrderPush');
//        $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));

        import("Wechat.Wechat", APP_PATH);
        $options = array(
            'token' => C('APP_TOKEN'), //填写您设定的key
            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        $this->wechat_obj = new Wechat($options);

        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();

        $set_audit_way = $audit_way;
        foreach ($managers as $m) {
            if (!$m) {
                continue;
            }
            $condition = ['id' => $m];
            
            if( $set_audit_way == 3 ){
                $audit_detail = $distributor->where($condition)->field('level')->find();
                $audit_level = $audit_detail['level'];
                
                $set_audit_way = isset($audit_way_level[$audit_level])?$audit_way_level[$audit_level]:$audit_way_level[0];
            }

            //公众号推送
            $distributor->where($condition)->setField('audited', $set_audit_way);

            if( $set_audit_way == 1 ){
                //授权期限相关
                $save=[
                    'audit_time' => time(),
                    'end_times'=>strtotime("+1 year",time()),
                ];
                $distributor->where(['id' => $m])->save($save);

                $user = $distributor->field('level,pid,recommendID')->where(['id' => $m])->find();
                $path = C('DEFAULT_TEAM');
                if ($path == 'path') {
                    $distributor->where(['id' => $user['pid']])->save(['is_lowest' => 0]);
                } else {
                    $distributor->where(['id' => $user['recommendID']])->save(['is_lowest' => 0]);
                }

                $dis_info = $distributor->where(array('id' => $m))->find();

                $touser = $dis_info['openid'];
//                $uName = $dis_info['name'];
//                $keyword1 = $dis_info['name'];
//                $phone = $dis_info['phone'];
//                $bname = $dis_info['bossname'];
                if($user['recommendID']){
                    //查找出推荐人的等级信息
                    $rec_info=$distributor->where(['id'=>$user['recommendID']])->find();
                    if(($rec_info['level'] == 1 ) && ($user['level'] == 1)){
                        //推荐返利
                        $rebate_result = $Rebate->admin_user_audit_rebate($m,$dis_info);
                    }
                }

                if( $rebate_result['code'] != 1 ){
                    //                $rebate_error = $rebate_result;
                    //                break;
                }


//                $sendTime = date("Y-m-d H:i:s");
//                $template_id = C('SH_MB');
//                $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
//
//                $SYSTEM_NAME = C('SYSTEM_NAME');
//
//                $sendData = array(
//                    'first' => array('value' => ("$uName,您的".$SYSTEM_NAME."微商管理系统经销商审核成功！"), 'color' => "#CC0000"),
//                    'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
//                    'keyword2' => array('value' => ("$phone"), 'color' => '#000'),
//                    'keyword3' => array('value' => ("$sendTime"), 'color' => '#000'),
//                    'remark' => array('value' => ("欢迎您加入".$SYSTEM_NAME."微商管理系统。您的直属上级:" . $bname . "。"), 'color' => '#CC0000')
//                );
//
////                $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');
//
//                $template = array(
//                    'touser' => $touser,
//                    'template_id' => $template_id,
//                    'url' => $url,
//                    'topcolor' => '#7B68EE',
//                    'data' => $sendData
//                );
//
//                $this->wechat_obj->sendTemplateMessage($template);

                //这里是调用message的模板消息 edit by qjq 2018-1-30（注释上面旧的模板消息就可以开启此方法）
                import('Lib.Action.Message','App');
                $message = new Message();
                $message->push(trim($touser), $dis_info, $message->audit_manager);
                
                //代理任务升级
                import('Lib.Action.Upgrade', 'App');
                $user = $distributor->where(['id' => $user['recommendID'], 'audited' => 1])->find();
                (new Upgrade())->upgrade($user);
                
//                //保险起见，再重新找is_lowest没有置0的，并且置0(影响团队业绩)
//                import('Lib.Action.Team', 'App');
//                (new team())->is_yes_lowest();
                //调用结束

            }

        }

        //清除团队缓存
        clean_team_path_cache();


        if( !empty($rebate_error) ){
            $rebate_error['status'] = 2;
            $this->ajaxReturn($rebate_error, 'json');
        }


        $this->ajaxReturn(array('status' => 1,'data'=>$manager), 'json');
    }//end func audit




    //删除经销商
    public function delete() {
        $manager = I('pend');
        $managers = explode('|', $manager);

        $distributor_obj = M('distributor');

        import('ORG.Net.OrderPush');
        $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));

        foreach ($managers as $m) {
            $dis_info = $distributor_obj->where(array('id' => $m))->find();
            $touser = $dis_info['openid'];
//            $uName = $dis_info['name'];
//            $keyword1 = $dis_info['name'];
//            $phone = $dis_info['phone'];
//            $bname = $dis_info['bossname'];

//            $sendTime = date("Y-m-d H:i:s");
//            $template_id = C('SH_MB');
//            $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
//
//            $SYSTEM_NAME = C('SYSTEM_NAME');
//            $sendData = array(
//                'first' => array('value' => urlencode("$uName,您的".$SYSTEM_NAME."微商管理系统经销商审核不通过！"), 'color' => "#CC0000"),
//                'keyword1' => array('value' => urlencode("$keyword1"), 'color' => '#000'),
//                'keyword2' => array('value' => urlencode("$phone"), 'color' => '#000'),
//                'keyword3' => array('value' => urlencode("$sendTime"), 'color' => '#000'),
//                'remark' => array('value' => urlencode("具体原因请联系上级或总部了解情况"), 'color' => '#CC0000')
//            );
//
//            $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');

//            这里是调用message的模板消息 edit by qjq 2018-1-30（注释上面旧的模板消息就可以开启此方法）
            import('Lib.Action.Message','App');
            $message = new Message();
            $message->push(trim($touser), $dis_info, $message->not_audit_manager);
//            调用结束

            $distributor_obj->where(array('id' => $m))->delete();
        }

        //清除团队缓存
        clean_team_path_cache();
        
        $this->ajaxReturn(array('status' => 1,'data'=>$manager), 'json');
    }//end func delete

    /* ajax时间请求倒计时 */

    public function countdown() {
        $row = I('get.ct');
        $ct = decode($row); //解密时间
        $tcountdown = ($ct + 10000 - time());
        $this->ajaxReturn($tcountdown, 'json');
    }

    //判断是否有未审核代理
    public function is_audit() {

        //待审核经销商
        $users = M('distributor')->where(array('pid' => $this->uid, 'audited' => 0, 'managed' => 0))->select();
        if (!$users) {
            $this->ajaxReturn(false, 'json');
        }
        $this->ajaxReturn(true, 'json');
    }




}

?>