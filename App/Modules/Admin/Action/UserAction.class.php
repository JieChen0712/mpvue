<?php

/**
 * 	微斯咖经销商后台
 */
class UserAction extends CommonAction {


    public function index() {
        import('Lib.Action.Integral', 'App');
        $Integral = new Integral();
        
        import('Lib.Action.User', 'App');
        $User = new User();
        
        import('Lib.Action.Stock', 'App');
        $Stock = new Stock();

        //资金
        $money_funds = M('money_funds')->where(array('uid'=>$this->uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
        
        //消息
        $inform_dis = M('inform_dis')->where(['openid'=>$_SESSION['oid'],'status'=>'0'])->count();
        
        //积分
        $integral_info = $Integral->get_user_integral_info($this->uid);

        $this->user_integral = isset($integral_info['score'])?$integral_info['score']:0;

        $this->money = $recharge_money;
        
        //微信jssdk
        $this->msg_mun = $inform_dis;
        $this->signPackage = get_jsapi_ticket();
        $this->app_id = C('APP_ID');
        $this->app_secret = C('APP_SECRET');
        $this->open_upgrade_apply = $User->open_upgrade_apply;
        $this->display();
    }

    //个人信息
    public function info() {
        $list = M('distributor')->where(array('id' => $this->uid))->select();
        $rec_id=$list[0]['recommendID'];
        $rec_info=M('distributor')->where(array('id'=>$rec_id))->find();
        if(empty($rec_info)){
                $rec_info['name']='总部';
        }
        $this->assign('list', $list);
        $this->rec_info=$rec_info;
        $this->display();
    }//end func info


    //修改密码
    public function edit_password() {
        $this->display();
    }

    public function edit_newpassword() {

        $user = M('distributor');

        $pd_old = I('post.pd_old');
        $pd_new = trim(I('post.pd_new'));
        $pd_news = trim(I('post.pd_news'));

        $pd_old = md5($pd_old);
        $id = $this->uid;
        $m_row = $user->where(array('id' => $id))->find();

        if ($m_row['password'] == $pd_old) {
            if ($pd_new != $pd_news) {
                $response['status'] = 0;
                $response['message'] = "两次密码输入不一致！";
                return $this->ajaxReturn($response, 'json');
            }
            if (($pd_new == "") || strlen($pd_new) < 6 || strlen($pd_new) > 16) {
                $response['status'] = 0;
                $response['message'] = "密码长度不能小于6位和大于16位！";
                return $this->ajaxReturn($response, 'json');
            }
            $m_newpass = I('post.pd_new');
            $data['password'] = md5($m_newpass);
            if ($user->where(array('id' => $id))->save($data)) {
                $response['status'] = 1;
                $response['message'] = "修改密码成功!";
                session(null); // 清空当前的session
                return $this->ajaxReturn($response, 'json');
            } else {
                $response['status'] = 0;
                $response['message'] = "修改密码失败！";
                return $this->ajaxReturn($response, 'json');
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "旧密码输入错误！";
            return $this->ajaxReturn($response, 'json');
        }
        $this->display();
    }

    //退出登录
    public function exit_login() {
        $id = $this->uid;
        if (!empty($id)) {
            $response['status'] = 1;
            $response['message'] = "退出成功!";
            session(null); // 清空当前的session
            return $this->ajaxReturn($response, 'json');
        }
    }

    //总资产
    public function assets() {

        $money_funds = M('money_funds')->where(array('uid'=>$this->uid))->find();
        if( empty($money_funds) ){
            $money_funds = [
                'recharge_money'    =>  0,
                'apply_money'   =>  0,
                'can_refund_money'  =>  0,
                'his_recharge_money'    =>  0,
                'his_charge_money'  =>  0,
                'his_refund_money'  =>  0,
            ];
        }
        //申请充值通过的总金额
        $money_apply=M('money_apply')->where(array('uid'=>$this->uid,'status'=>'1'))->sum('apply_money');
        //申请提现通过的总金额
        $money_refund_apply=M('money_refund_apply')->where(array('uid'=>$this->uid,'status'=>'1'))->sum('apply_money');

        $not_can_refund_money=bcsub($money_funds['recharge_money'],$money_funds['can_refund_money'],2);
        $this->money_funds = $money_funds;
        $this->not_can_refund_money=$not_can_refund_money;
        $this->money_apply = $money_apply;
        $this->money_refund_apply = $money_refund_apply;
        $this->display();
    }//end func 


    //账单
    public function bill() {
        import('Lib.Action.Funds','App');
        $Funds = new Funds();


        $money_funds = M('money_funds')->where(array('uid'=>$this->uid))->find();
        if( empty($money_funds) ){
            $money_funds = [
                'recharge_money'    =>  0,
                'apply_money'   =>  0,
                'can_refund_money'  =>  0,
                'his_recharge_money'    =>  0,
                'his_charge_money'  =>  0,
                'his_refund_money'  =>  0,
            ];
        }

        $condition['uid']   = $this->uid;
        $page_info = [
            'page_num'  =>  1,
            'page_list_num' =>  30,
        ];

        $result = $Funds->get_money_recharge_log($page_info,$condition);

//        print_r($result);return;

        $this->types = [
//            'all'       =>  '全部',
            'recharge'  =>  '充值',
            'charge'    =>  '扣费',
            'refund'    =>  '提现',
        ];
        $this->money_funds = $money_funds;
        $this->list = $result['list'];
        $this->display();
    }//end func bill


    //获取账单的AJAX
    public function get_bill_ajax(){
        if( !IS_AJAX ){
          return FALSE;
        }

        $start_time=trim(I('start_time'));
        $end_time=trim(I('end_time'));
        $type = trim(I('type'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

//
//         $start_time=20171102;
//         $end_time=20171218;
//         $type='charge';
//         $page_num=1;
        if( empty($start_time) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '开始时间获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if( empty($end_time) ){
            $return_result = [
                'code'  =>  5,
                'msg'   =>  '结束时间获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if( empty($type) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if( empty($page_num) ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }


        import('Lib.Action.Funds','App');
        $Funds = new Funds();

        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }
        $condition['uid']   = $this->uid;

//        //开始时间-结束时间
//        $month=($month.'01');
//        $start_time = strtotime($month);
//        $end_time = strtotime('+1 month -1 seconds',$start_time);
//        $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        $start_time=strtotime($start_time);
        $end_times=strtotime($end_time);
        $end_time=strtotime('+1 days',$end_times);
        $condition['created']=array(array('egt', $start_time), array('elt', $end_time));
        //充值
        if( $type == 'recharge' ){

            $page_info = [
                'page_num'  =>  $page_num,
                'page_list_num' =>  $page_list_num,
            ];
            $result = $Funds->get_money_recharge_log($page_info,$condition);
        }

        //扣费
        elseif( $type == 'charge' ){
            $page_info = [
                'page_num'  =>  $page_num,
                'page_list_num' =>  $page_list_num,
            ];
            $result = $Funds->get_money_charge_log($page_info,$condition);
        }

        //提现记录
        elseif( $type == 'refund' ){
            $page_info=[
                'page_num' => $page_num,
                'page_list_num' => $page_list_num,
            ];
            $result = $Funds->get_money_refund($page_info,$condition);
        }
        //收入
        $income=M('money_recharge_log')->where($condition)->sum('money');
        if(empty($income)){
            $income='0.00';
        }
        //支出
        $pay_one=M('money_charge_log')->where($condition)->sum('money');
        $pay_two=M('money_refund')->where($condition)->sum('money');
        $pay=bcadd($pay_one,$pay_two,2);
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
            'uid'   =>  $this->uid,
            'income'=>$income,
            'pay' => $pay
        ];
        $this->ajaxReturn($return_result);
    }//end func get_bill_ajax


    //银行卡
    public function bankcard(){
        $this->display();
    }
    public function get_bankcard() {
        if(!IS_AJAX){
            return FALSE;
        }
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));
//      $page_num=1;

        if( empty($page_num) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Team','App');
        $Team = new Team();

        $condition=[
            'uid' => $this->uid,
        ];

        $page_info=[
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $list=$Team->get_distributor_bank($page_info,$condition);
        $result=[
            'code' => 1,
            'msg' => '获取成功',
            'info' => $list,
        ];
//        $this->list=$list;
        $this->ajaxReturn($result);

    }//end func bankcard


    //积分
    public function integral() {

//        import('Lib.Action.Integral','App');
//        $Integral =new Integral();
//        $my_integral=M('integral')->where(array('uid'=>$this->uid))->find();
//
//        $condition_type_one=array(
//            'uid' => $this->uid,
//            'class' => 1,
//        );
//        $condition_type_two=array(
//            'uid' => $this->uid,
//            'class' => 2,
//        );
////        $my_integral_one=M('integral_log')->where($condition_type_one)->sum('score');
////        $my_integral_two=M('integral_log')->where($condition_type_two)->sum('score');
////
//        $my_xf=$my_integral['score']-$my_integral['his_score'];
//        if( empty($my_integral) ){
//            $my_integral = [
//                'score' => 0,
//                'his_score'    =>  0,
//            ];
//        }
//        $condition['uid']   = $this->uid;
//        $page_info = [
//            'page_num'  =>  1,
//            'page_list_num' =>  30,
//        ];
//        $result = $Integral->get_integral_log($page_info,$condition);
//        $result_rule=$Integral->get_integral_rule($page_info,$condition);
//        $this->types = [
//            'integral_info'  =>  '积分记录',
//            'rule'    =>  '赚积分的规则',
//        ];
//        $this->my_integral = $my_integral;
//        $this->my_xf = $my_xf;
//
//        $this->list = $result['list'];
//        $this->integral_rule = $result_rule['list'];

        $this->display();
    }//end func integral


    //获取积分的AJAX
    public  function get_integral_ajax(){
        if( !IS_AJAX ){
            return FALSE;
        }

        $month = trim(I('month'));
        $type = trim(I('type'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));
//        $month='201709';
//        $type='integral_info';
//        $page_num=1;
        if( empty($month) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '月份获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        if( empty($type) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if( empty($page_num) ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Integral','App');
        $Integral = new Integral();

        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }
        $condition['uid']   = $this->uid;

        //开始时间-结束时间
        $month=($month.'01');
        $start_time = strtotime($month);
        $end_time = strtotime('+1 month -1 seconds',$start_time);
        $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));

        //积分记录
        if($type == 'integral_info'){
            $page_info=[
                'page_num' => $page_num,
                'page_list_num' => $page_list_num,
            ];
            $result = $Integral->get_integral_log($page_info,$condition);

        }

        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
        ];
        $this->ajaxReturn($return_result);

    }

    //获取自己的积分
    public function get_score(){
        if( !IS_AJAX ){
            return FALSE;
        }

        $score_info=M('integral')->where(array('uid'=>$this->uid))->find();
        $score=isset($score_info['score'])?$score_info['score']:'0';

        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'score'  =>  $score,
        ];
        $this->ajaxReturn($return_result);
    }

    //积分规则ajax
    public function get_rule(){
        if( !IS_AJAX ){
            return FALSE;
        }
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

        //$page_num=1;
        if( empty($page_num) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        import('Lib.Action.Integral','App');
        $Integral = new Integral();

        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }
        $condition['uid']   = $this->uid;

        $page_info=[
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $result = $Integral->get_integral_rule($page_info,$condition);

        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
        ];
        $this->ajaxReturn($return_result);
    }

    public function pay_way(){
        $this->display();
    }
    //添加支付方式
    public function get_pay_way(){
        if(!IS_AJAX){
            return FALSE;
        }
        $dis_bank=M('distributor_bank');
        $type=trim(I('post.type'));
        $cardname=trim(I('post.cardname'));
        $cardnumber=trim(I('post.cardnumber'));
        $cardaccount=trim(I('post.cardaccount'));
        $id=trim(I('id'));

        $status=trim(I('status'));
//      var_dump($id);die;
        if($type == '银行卡'){
            if($cardnumber =='' || $cardname == ''){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '信息填写不完整',
                ];
                return  $this->ajaxReturn($return_result,'json');
            }
            $condition_card=[
                'uid' => $this->uid,
                'card_number' => $cardnumber,
                'type' => $type,
            ];
            $result=$dis_bank->where($condition_card)->find();
            if(empty($id)){
                if($result){
                    $return_result = [
                        'code'  =>  4,
                        'msg'   =>  '添加失败，该银行卡已存在',
                        'info' => $result,
                    ];
                    return  $this->ajaxReturn($return_result,'json');
                }
            }

        }elseif($type == '支付宝'){
            if($cardaccount =='' || $cardname == ''){
                $return_result = [
                    'code'  =>  5,
                    'msg'   =>  '信息填写不完整',
                ];
                return  $this->ajaxReturn($return_result,'json');
            }
            $condition_card=[
                'uid' => $this->uid,
                'card_account' => $cardaccount,
                'type' => $type,
            ];
            $result=$dis_bank->where($condition_card)->find();
            if(empty($id)){
                if($result){
                    $return_result = [
                        'code'  =>  6,
                        'msg'   =>  '添加失败，该账号已存在',
                        'info' => $result,
                    ];
                    return  $this->ajaxReturn($return_result,'json');
                }
            }
        }elseif($type == '微信支付'){
            if($cardaccount =='' || $cardname == ''){
                $return_result = [
                    'code'  =>  7,
                    'msg'   =>  '信息填写不完整',
                ];
                return  $this->ajaxReturn($return_result,'json');
            }
            $condition_card=[
                'uid' => $this->uid,
                'card_account' => $cardaccount,
                'type' => $type,
            ];
            $result=$dis_bank->where($condition_card)->find();
            if(empty($id)){
                if($result){
                    $return_result = [
                        'code'  =>  8,
                        'msg'   =>  '添加失败，该账号已存在',
                        'info' => $result,
                    ];
                    return  $this->ajaxReturn($return_result,'json');
                }
            }
        }


//        $condition=[
//            'uid' => $this->uid,
//            'card_number' => $cardnumber,
//        ];
//        $result=$dis_bank->where($condition)->find();
//
//        if($result){
//            $return_result = [
//                'code'  =>  3,
//                'msg'   =>  '添加失败，该银行卡已存在',
//            ];
//            return  $this->ajaxReturn($return_result,'json');
//        }elseif(empty($cardnumber)){
//            $status=1;
//            $data = array(
//                'uid'=>$this->uid,
//                'type'=>I('post.type'),
//                'card_name' => I('post.cardname'),
//                'bank' => I('post.bank'),
//                'card_type' => I('post.cardtype'),
//                'card_number'=>I('post.cardnumber'),
//                'card_account'=>I('post.cardaccount'),
//                'status' => $status,
//                'updated' => time()
//            );
//        }else{


        if(empty($id)){
            $condition_status=[
                'uid' => $this->uid,
                'status' => '1',
            ];
            $info=$dis_bank->where($condition_status)->count();
            if($info == 0){
                $status=1;
            }else{
                $dis_bank->where(array('uid' => $this->uid))->save(['status'=>0]);
                $status=1;
            }
        }else{
            $dis=$dis_bank->find($id);
            $status=$dis['status'];
        }


        $data = array(
            'uid'=>$this->uid,
            'type'=>I('post.type'),
            'card_name' => I('post.cardname'),
            'bank' => I('post.bank'),
            'card_type' => I('post.cardtype'),
            'card_number'=>I('post.cardnumber'),
            'card_account'=>I('post.cardaccount'),
            'status' => $status,
            'updated' => time()
        );

//        }


        if(empty($id)){
            $res = $dis_bank->add($data);
        }else{
            $infos=$dis_bank->find($id);
            if($type == $infos['type']){
                $res = $dis_bank->where(array('id'=>$id))->save($data);
            }else{
                $clean = array(
                    'type'=>'',
                    'card_name' => '',
                    'bank' => '',
                    'card_type' => '',
                    'card_number'=>'',
                    'card_account'=>'',
                );
                $dis_bank->where(array('id'=>$id))->save($clean);
                if($type == '支付宝' || $type == '微信支付'){
                    $datas=[
                        'type'=>I('post.type'),
                        'card_name' => I('post.cardname'),
                        'card_account'=>I('post.cardaccount'),
                        'updated' => time()
                    ];
                    $res=$dis_bank->where(array('id'=>$id))->save($datas);
                }
                else{
                    $odata = array(
                        'uid'=>$this->uid,
                        'type'=>I('post.type'),
                        'card_name' => I('post.cardname'),
                        'bank' => I('post.bank'),
                        'card_type' => I('post.cardtype'),
                        'card_number'=>I('post.cardnumber'),
                        'status' => $status,
                        'updated' => time()
                    );
                    $res = $dis_bank->where(array('id'=>$id))->save($odata);
                }
            }
        }
        if(empty($id)){
            if($res){
                $return_result = [
                    'code'  =>  1,
                    'msg'   =>  '添加成功',
                    'info' =>$res,
                ];
                return  $this->ajaxReturn($return_result,'json');
            }else{
                $return_result = [
                    'code'  => 2,
                    'msg'   =>  '添加失败',
                    'info' =>$res,
                ];
                return  $this->ajaxReturn($return_result,'json');
            };
        }else{
            if($res){
                $return_result = [
                    'code'  =>  1,
                    'msg'   =>  '修改成功',
                    'info' =>$res,
                ];
                return  $this->ajaxReturn($return_result,'json');
            }else{
                $return_result = [
                    'code'  => 2,
                    'msg'   =>  '修改失败',
                    'info' =>$res,
                ];
                return  $this->ajaxReturn($return_result,'json');
            };
        }
    }

    //修改默认银行卡状态
    public function set_pay_way_status(){
        if(!IS_AJAX){
            return FALSE;
        }
        $id=I('post.pay_way_id');
        $condition['uid'] = $this->uid;

        $dis_bank=M('distributor_bank');

        $dis_bank->where($condition)->save(['status' => 0]);

        $condition['id'] = $id;
        $res = $dis_bank->where($condition)->save(['status' => 1]);
        $this->ajaxReturn($res, 'json');
    }

    //修改银行卡状态
    public function set_pay_way(){
        if(!IS_AJAX){
            return FALSE;
        }

        $id=I('post.pay_way_id');
//		$id='66';

        $condition['uid'] = $this->uid;
        $dis_bank=M('distributor_bank');

        $condition['id'] = $id;
        $res = $dis_bank->where($condition)->find();

        $this->ajaxReturn($res, 'json');
    }

    //提现获取默认银行卡
    public function get_refund_bank(){
        if(!IS_AJAX){
               return FALSE;
        }
        import('Lib.Action.Team','App');
        $Team = new Team();
        $condition=[
            'uid' =>$this->uid,
            'status' => 1,
        ];
        $result=$Team->get_distributor_bank([],$condition);
        $return_result = [
            'code'  => 1,
            'msg'   =>  '获取成功',
            'info' =>$result,
        ];
        $this->ajaxReturn($return_result, 'json');
    }

    //钱包明细
    public function money_detail(){
        $this->display();
    }

    //充值申请详情
    public function get_money_apply_detail(){
        if(!IS_AJAX){
             return FALSE;
        }
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

//     $page_num=1;

        if( empty($page_num) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Funds','App');
        $Funds = new Funds();

        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }

        $page_info=[
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $condition=[
            'uid' => $this->uid,
        ];

        $result = $Funds->get_money_apply($page_info,$condition);
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
        ];

        $this->ajaxReturn($return_result);
    }

    //提现申请
    public function get_money_refund_detail(){
        if(!IS_AJAX){
            return FALSE;
        }

        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

//       $page_num=1;

        if( empty($page_num) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Funds','App');
        $Funds = new Funds();

        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }

        $page_info=[
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $condition=[
            'uid' => $this->uid,
        ];

        $result = $Funds->get_money_refund_apply($page_info,$condition);
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
        ];

        $this->ajaxReturn($return_result);
    }

    //钱包明细详情页
    public function money_detail_info(){
        $this->display();
    }

    //钱包详情页ajax
    public function get_money_detail_info(){
        if(!IS_AJAX){
              return FALSE;
        }

        //提现
        $refund_id = trim(I('post.refund_id'));
        //充值
          $apply_id = trim(I('post.apply_id'));

        

        if($apply_id != null && $refund_id == null){
            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $condition['id']=$apply_id;
            $result = $Funds->get_money_apply([],$condition);
        }

        if($apply_id == null && $refund_id != null){

            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $condition['id']=$refund_id;
            $result = $Funds->get_money_refund_apply([],$condition);
        }
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
        ];
        $this->ajaxReturn($return_result);
    }

    //检测是否有银行卡
    public function get_check_bank(){
        if(!IS_AJAX){
            return FALSE;
        }
        $condition['uid'] = $this->uid;
        $dis_bank=M('distributor_bank');

        $result = $dis_bank->where($condition)->count('id');
        if($result){
            $return_result = [
                'code'  =>  1,
                'msg'   =>  '获取成功',
                'info'  =>  $result,
            ];
        }else{
            $return_result = [
                'code'  =>  0,
                'msg'   =>  '获取失败',
                'info'  =>  $result,
            ];
        }
        $this->ajaxReturn($return_result);
    }
    
    //上传头像
    public function upload_img() {
		$access_token = get_access_token();
		$server_id =I('post.server_id');
		$img_url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$server_id";
		$upload_dir = "upload/headimg/";
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0777, true);
		}
		$img_name = md5(time().rand()).'.jpg';
		$file_name = $upload_dir.$img_name;
		$img = file_get_contents($img_url);
        if (file_put_contents($file_name, $img)) {
            //删除原来的头像
            $distributor = M('distributor');
            $old_img = $distributor->where(['id' => $this->uid])->getField('headimgurl');
            $old_img = substr($old_img, 1);
            if(file_exists($old_img)){
                unlink($old_img);
            }
            $file_name = '/'.$file_name;
            $distributor->where(['id' => $this->uid])->save(['headimgurl' => $file_name]);
            $this->ajaxReturn($file_name, 'json');
        }
        $this->ajaxReturn(false, 'json');
		
    }

    // 绑定微信页
    public function bindwechat() {
        if( empty($_SESSION['userinfo']) ){
            $return_url = __APP__.'/admin/user/bindwechat';
            checkAuth('getinfo','','',$return_url);
        }
        $this->display();
    }

    //绑定微信
    public function bind_wechat(){

        $distributor_model = M('distributor');

        $userinfo = $_SESSION['userinfo'];
        $openid = $userinfo['openid'];
        
        $manager = $this->manager;
        $cur_id = $manager['id'];


        if($manager['openid'] == $openid) {
            $result=[
                'code' => 3,
                'msg' => '你已经绑定该微信，无需重复绑定！'
            ];
            $this->ajaxReturn($result);
        }

        $row = $distributor_model->where(['openid' => $openid])->find();

        if(!empty($row) && $row['id'] != $cur_id) {
            $result=[
                'code' => 4,
                'msg' => '该微信已经绑定其他帐号('. $row['wechatnum'] .')，请先解绑！'
            ];
            $this->ajaxReturn($result);
        }

        $headimgurl = $userinfo['headimgurl'];
        $new_save = [
            'openid'    =>  $openid,
            'headimgurl'    =>  $headimgurl,
        ];

        $condition = [
            'id'    =>  $cur_id,
        ];
        $save_res = $distributor_model->where($condition)->save($new_save);
        
        if( $save_res ){

            $result=[
                'code' => 1,
                'msg' => '绑定成功，请重试登录！'
            ];
            $this->ajaxReturn($result);
        }
        else{
            $result=[
                'code' => 2,
                'msg' => '绑定失败，请重试！'
            ];
            $this->ajaxReturn($result);
        }
        
    }
    
    //解除绑定
    public function unbind_wechat(){
        $password = trim(I('password'));
        $name = $_SESSION['loginName'];
        $manager = $this->manager;

        if($manager['password'] == md5($password)) {

            $new_id = $manager['id'];
            $new_openid = 'changeopenid-'.md5($name).'-'.$new_id;
            session('oid', $new_openid);
            $res = M('distributor')->where(array('wechatnum' => $name))->save(['openid' => $new_openid]);

            if($res) {
                $result=[
                    'code' => 1,
                    'msg' => '解除绑定成功!！'
                ];
            } else {
                $result=[
                    'code' => 2,
                    'msg' => '解除绑定失败!'
                ];
            }
            $this->ajaxReturn($result);
        } else {
            $result=[
                'code' => 3,
                'msg' => '用户密码错误!'
            ];
            $this->ajaxReturn($result);
        }
    }
    
        
    //绑定微信
//     public function bindwechat(){
        
//         if( empty($_SESSION['userinfo']) ){
//             $return_url = __APP__.'/admin/user/bindwechat';
//             checkAuth('getinfo','','',$return_url);
//         }
        
//         $userinfo = $_SESSION['userinfo'];
//         $openid = $userinfo['openid'];
//         $headimgurl = $userinfo['headimgurl'];
        
//         $tip_return_url = __APP__.'/admin/user/info';;
//         if( empty($openid) ){
//             $content = '没有获取到微信绑定信息！';
//             error_tip($content, '',$tip_return_url);
//         }
        
//         $distributor_model = M('distributor');
        
        
//         $manager = $this->manager;
        
//         $cur_id = $manager['id'];
//         $cur_openid = $manager['openid'];
        
//         if( $cur_openid == $openid ){
//             $content = '您已经绑定了微信，无须再绑定！';
//             error_tip($content, '',$tip_return_url);
//         }
        
//         //-----start 已经绑定该微信的账号重新设置openid-----
//         $condition_ser = [
//             'openid'    =>  $openid,
//         ];
//         $ser_info = $distributor_model->where($condition_ser)->field('id,wechatnum')->find();
        
//         if( !empty($ser_info) ){
//             $ser_wechatnum = $ser_info['wechatnum'];
//             $ser_id = $ser_info['id'];
//             $new_openid = 'changeopenid-'.md5($ser_wechatnum).'-'.$ser_id;
            
//             $save_info = [
//                 'openid'    =>  $new_openid,
//             ];
//             $distributor_model->where($condition_ser)->save($save_info);
//         }
//         //-----end 已经绑定该微信的账号重新设置openid-----
        
        
//         $condition = [
//             'id'    =>  $cur_id,
//         ];
//         $new_save = [
//             'openid'    =>  $openid,
//             'headimgurl'    =>  $headimgurl,
//         ];
//         $save_res = $distributor_model->where($condition)->save($new_save);
        
//         if( $save_res ){
//             $content = '绑定成功，请重试登录！';
//             $tip_return_url = __APP__.'/admin/login/loginout';
// //            error_tip($content, $tip_return_url);
            
//             echo '<script type="text/javascript">alert("'.$content.'");window.location.href="'.$tip_return_url.'";</script>';
//         }
//         else{
//             $content = '绑定失败，请重试！';//$distributor_model->getLastSql();
//             error_tip($content, '',$tip_return_url);
//         }
//     }//end func bind_wechat
    
    //升级申请
    public function upgrade_apply(){
        $level_name=C('LEVEL_NAME');
        $id=trim(I('id'));
        $desc_info=M('distributor_upgrade_desc')->where(['id'=>$id])->find();
        if (!$desc_info) {
            echo '未找到升级说明';
            exit();
        }
        $desc_info['level_name']=$level_name[$desc_info['level']];
        $this->desc_info=$desc_info;
        $this->display();
    }
    
    //添加升级申请
    public function add_upgrade_apply_ajax(){
         
        $apply_level = I('apply_level');
        $note = I('note');
        $depositimg=I('flimg');
        import('Lib.Action.User','App');
        $User = new User();
        
        $result = $User->add_upgrade_apply($this->uid,$this->manager,$apply_level,$note,$depositimg);
        
        $this->ajaxReturn($result);
    }

    //银行卡删除
    public function del_bankcard(){
        $id=trim(I('id'));
        if(empty($id)){
            $result = [
                'code'  =>  2,
                'msg'   =>  'id不能为空！',
            ];
            $this->ajaxReturn($result);
        }
        $res=M('distributor_bank')->delete($id);
        if($res){
            $result = [
                'code'  =>  1,
                'msg'   =>  '删除成功！',
            ];
        }else{
            $result = [
                'code'  =>  3,
                'msg'   =>  '删除失败！',
            ];
        }
        $this->ajaxReturn($result);
    }

    // 消息提示
    public function toast (){
        $this->display();
    }

    // 滚动加载消息模块
    public function get_msg(){
        if(!IS_AJAX){
             return FALSE;
        }
        $inform_dis = M('inform_dis');
        $inform = M('inform');
        $distributor = M('distributor');
        $target_table = null;
        
        $openid = $_SESSION['oid'];
        $msg_info = null;
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));
        $type = trim(I('type'));
        $uid = null;
        
//     $page_num=1;

        if( empty($page_num) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        
        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }
        
        $page_info=[
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        
        if(!empty($openid)){
            $user_id = $distributor->where(['openid'=>$openid])->find();
            if(!empty($user_id)){
                $uid = $user_id['id'];
//              $user_id = $user_id['id'];
////              var_dump($user_id);die;
//              $msg_info = $inform_dis -> where(['uid'=>$user_id])->order('created desc')->select();
            }
            $condition=[
                'uid' => $uid,
            ];
            
            if( empty($type) ){
                $return_result = [
                    'code'  =>  2,
                    'msg'   =>  '类型获取失败',
                ];
                $this->ajaxReturn($return_result);
            }else if($type=="user"){
                $target_table = $inform_dis;
                $condition = [
                    'openid' => $user_id['openid']
                ];
            }else{
                $target_table = $inform;
                $condition=[
                    'status' => 1,
                ];
            }
            
            $list = array();
            $page = '';
            //如果页码为空的话默认值为1
            $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
            //每页的数量
            $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?10:$page_info['page_list_num'];
            $count = $target_table->where($condition)->count();
            
            if( $count > 0 ){
                if( !empty($page_info) ){
    
                    $page_con = $page_num.','.$page_list_num;
                    $list = $target_table->where($condition)->order('time desc')->page($page_con)->select();
                }
                else{
                    $list = $target_table->where($condition)->order('time desc')->select();
                }
            }
            
            
            if( !empty($page_info) ){
                //*分页显示*
                import('ORG.Util.Page');
                $p = new Page($count, $page_list_num);
                $page = $p->show();
            }
    
    
            $return_result = array(
                'code'  => 1, 
                'list'  => $list,
                'page'  => $page,
                'count' => $count,
                'limit' => $page_list_num,
                'msg'   => '获取成功'
            );
    
            $this->ajaxReturn( $return_result );
                
        }
        
        
    }
    
    // 设置为已读
    public function read_msg(){
        if(!IS_AJAX){
            return FALSE;
        }
        
        $msg_id = I('id');
        $inform_dis = M('inform_dis');
        $data['status'] = 1;
        
        $result = [
            'code' => 0,
            'info' => null,
            'msg' => '修改失败'
        ];
        if(!empty($msg_id)){
            $flag = true;
            $where = 'id in('.implode(',',$msg_id).')'; 
            $res = $inform_dis->where($where)->save($data);
             
            if($res){
                $result = [
                    'code' => 1,
                    'info' => $res,
                    'msg' => '修改成功'
                ];
            }
        }
        $this->ajaxReturn($result);
    }

    //编辑银行卡信息显示页
    public function edit_bankcard_info(){
        $id=trim(I('id'));
        if(empty($id)){
            $result = [
                'code'  =>  2,
                'msg'   =>  'id不能为空！',
            ];
            $this->ajaxReturn($result);
        }
        $res=M('distributor_bank')->find($id);
        if($res){
            $result = [
                'code'  =>  1,
                'info' => $res,
                'msg'   =>  '获取成功！',
            ];
        }else{
            $result = [
                'code'  =>  3,
                'msg'   =>  '获取失败！',
            ];
        }
        $this->ajaxReturn($result);
    }
    
    //申请说明
    public function upgrade_explain() {
        $upgrade_model = M('distributor_upgrade_apply');
        $level_name = C('LEVEL_NAME');
        $list = M('distributor_upgrade_desc')->select();
        foreach ($list as $k=>$v) {
            $list[$k]['levname'] = $level_name[$v['level']];
            //判断有没有升级过
            $yes_apply = $upgrade_model->where(['uid'=>$this->uid, 'apply_level'=>$v['level'], 'status'=>0])->find();
            $yes_audit = $upgrade_model->where(['uid'=>$this->uid, 'apply_level'=>$v['level'], 'status'=>1])->find();
            if ($yes_apply) {
                $list[$k]['status_name'] = '已申请';
            }
            if ($yes_audit) {
                $list[$k]['status_name'] = '已升级';
            }
            if ($v['level'] >= $this->manager['level']) {
                $list[$k]['status_name'] = '不符合';
            }
            
        }
        $this->list = $list;
        $this->display();
    }
}

?>