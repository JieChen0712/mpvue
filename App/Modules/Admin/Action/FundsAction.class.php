<?php

/**
 * 	微斯咖经销商后台——经销商管理
 */
class FundsAction extends CommonAction {

    //资金管理首页
    public function index() {
        //查看该经销商的资金表
        $money_funds = M('money_funds')->where(array('uid'=>$this->uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
        $this->recharge_money = $recharge_money;//虚拟币金额

        //查看该经销商可提取金额
        $min_refund_money = $this->get_min_refund_money($_SESSION['cur_level'],$recharge_money);
        $this->min_refund_money = $min_refund_money;//可提取金额
        $this->display();
    }


    /**
     * 获取退款金额
     *
     * @param int $level
     * @param decimal $recharge_money
     * @return decimal
     */
    private function get_min_refund_money($level,$recharge_money){


        if( $recharge_money == 0 || $level == 0 || is_null($level) ){
            return 0;
        }

        $money_min_refund = M('money_min_refund');
        $set_info = $money_min_refund->where(array('id'=>'1'))->find();

        $min_refund_key = 'level'.$level;
        $min_refund = isset($set_info[$min_refund_key])?$set_info[$min_refund_key]:0;


        $refund_money = bcsub($recharge_money,$min_refund,2);

        if( $refund_money < 0 ){
            $refund_money = 0;
        }


        return $refund_money;
    }


    //充值申请
    public function recharge_apply() {
        $money_apply_pay_type=C('FUNCTION_MODULE')['MONEY_APPLY_PAY_TYPE'];
        $distributor_obj = M('distributor');
        $money_apply_obj = M('money_apply');
        import('Lib.Action.Funds','App');
        $Funds = new Funds();

        $uid = $this->uid;

        $condition = array(
            'uid' => $uid,
        );
        $money_apply_info = $money_apply_obj->where($condition)->limit(20)->order('created desc')->select();


        $dis_info = array();
        foreach( $money_apply_info as $k => $v ){
            $v_audit_id = $v['audit_id'];

            if( $v_audit_id != 0 ){
                if( !isset($dis_info[$v_audit_id]) ){
                    $dis_info[$v_audit_id] = $distributor_obj->where(array('id'=>$v_audit_id))->find();
                }

                $audit_name = $dis_info[$v_audit_id]['name'];
            }
            else{
                $audit_name = '总部';
            }

            $money_apply_info[$k]['audit_name'] = $audit_name;
        }

//        print_r($money_apply_info);
        $this->money_apply_pay_type=$money_apply_pay_type;
        $this->is_parent_audit      =   $Funds->is_parent_audit;
        $this->min_apply_money  = $Funds->get_min_apply_money($uid);
        $this->money_apply_info =   $money_apply_info;
        $this->display();
    }



    //充值申请详细
    public function money_apply_detail(){
        $apply_id = I('get.id');

        $money_apply_obj = M('money_apply');

        $condition = array(
            'id' => $apply_id,
        );
        $money_apply_info = $money_apply_obj->where($condition)->find();

        $is_audit = 0;
        //如果审核人为自己才能进行审核
        if( !empty($money_apply_info) && $money_apply_info['audit_id'] == $this->uid ){
            $is_audit = 1;
        }

//        print_r($money_apply_info);return;

//        print_r($is_audit);return;

        $this->is_audit = $is_audit;
        $this->list = $money_apply_info;
        $this->display();
    }



    //提交充值申请
    public function money_apply_submit() {
        $money_apply=M('money_apply');
        $pay_way=trim(I('post.pay_way'));//1是总部审核 ，2是在线充值
        $money = trim(I('post.money'));
        $img = trim(I('post.imgval'));

        $distributor_obj = M('distributor');

        $uid = $this->uid;

        //如果是总部审核
        if($pay_way == 1){
            if(empty($img)){
                $return_result = array(
                    'code'  =>  -1,
                    'msg'   =>  '请上传图片！',
                );
                $this->ajaxReturn($return_result, 'JSON');
                return;
            }
        }
        //如果是在线支付
        elseif($pay_way == 2){
            $img =='';
        }
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $min_apply_money = $Funds->get_min_apply_money($uid);

        if( bccomp($money,$min_apply_money) == -1 ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '充值金额必须大于或等于最低申请金额！',
                'min_apply_money'   =>  $min_apply_money,
            );
            $this->ajaxReturn($return_result, 'JSON');return;
        }

        if(empty($money)){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '请输入正确的金额',
            );
            $this->ajaxReturn($return_result, 'JSON');return;
        }


        $condition = array(
            'id' => $uid,
        );
        $distributor_info = $distributor_obj->where($condition)->find();

        $audit_id = is_null($distributor_info['pid'])?0:$distributor_info['pid'];

        //如果属于最高级别经销商，申请充值的都是总部
        if ($distributor_info['level'] == 1) {
            $audit_id = 0;
        }


//        $audit_id = 0;//审核人直接改为总部
        $apply_result = $Funds->add_money_apply($uid,$audit_id,$money,$img,$pay_way);
        $result = [
            'code' => 1,
            'msg' => '提交成功,请耐心等候审核！',
            'info' =>$apply_result,
            'pay_way' => $pay_way,
            'money_apply_id'=>$apply_result['money_apply_id'],
        ];

        $this->ajaxReturn($result, 'JSON');
    }




    /**
     * 获取最低申请金额
     *
     * @return decimal $apply_money
     */
//    private function get_min_apply_money(){
//        
//        
//        $money_min_refund = M('money_min_refund');
//        $distributor_obj = M('distributor');
//        
//        $dis_info = $distributor_obj->where(array('id'=>$this->uid))->find();
//        
//        $level = $dis_info['level'];
//        
//        $set_info = $money_min_refund->where(array('id'=>'1'))->find();
//        
//        $min_apply_key = 'level'.$level;
//        $apply_money = isset($set_info[$min_apply_key])?$set_info[$min_apply_key]:0;
//        
//        if( $apply_money < 0 ){
//            $apply_money = 0;
//        }
//        
//        
//        return $apply_money;
//    }




    //我的下级申请充值记录
    public function subordinate_money_apply(){

        $distributor_obj = M('distributor');
        $money_apply_obj = M('money_apply');

        $condition = array(
            'audit_id' => $this->uid,
        );
        $money_apply_info = $money_apply_obj->where($condition)->order('created desc')->select();

        $audit_name = '总部';

        /**
         * TODO:提高SQL查询性能
         */
        foreach( $money_apply_info as $k => $v ){
            $v_uid = $v['uid'];

            $dis_info = $distributor_obj->where(array('id'=>$v_uid))->find();
            $user_name = $dis_info['name'];
            $user_levname = $dis_info['levname'];

            $money_apply_info[$k]['uid_name'] = $audit_name;
            $money_apply_info[$k]['uid_levname'] = $user_levname;
        }

        $this->money_apply_info =   $money_apply_info;
        $this->display();
    }


    //审核充值申请
    public function apply_pass(){
        $id = I('post.id');
        $pass = I('post.pass');

        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $return_result = $Funds->apply_pass($id,$pass,$this->uid);

        $this->ajaxReturn($return_result, 'JSON');
    }//end func apply_pass


    //审核充值申请
//    public function apply_pass(){
//        $id = I('post.id');
//        $pass = I('post.pass');
//        
//        $money_apply_obj = M('money_apply');
//        
//        $return_result = array(
//            'code'  =>  0,
//            'msg'   =>  '',
//        );
//        
//        if( empty($id) || is_null($pass) || !in_array($pass,array('1','2')) ){
//            $return_result = array(
//                'code'  =>  2,
//                'msg'   =>  '提交的参数有误！',
//            );
//            $this->ajaxReturn($return_result, 'JSON');
//            return;
//        }
//        
//        $condition = array(
//            'id'    =>  $id,
//            'status'    =>  '0',
//        );
//        
//        $money_apply_info = $money_apply_obj->where($condition)->find();
//        
//        if( empty($money_apply_info) ){
//            $return_result = array(
//                'code'  =>  3,
//                'msg'   =>  '没有符合条件的充值申请！',
//            );
//            $this->ajaxReturn($return_result, 'JSON');
//            return;
//        }
//        
//        $uid    =   $money_apply_info['uid'];
//        $audit_id = $money_apply_info['audit_id'];
//        $apply_money = $money_apply_info['apply_money'];
//        
//        
//        if( $audit_id != $this->uid ){
//            $return_result = array(
//                'code'  =>  4,
//                'msg'   =>  '该条充值申请不是由您进行审核！',
//            );
//            $this->ajaxReturn($return_result, 'JSON');
//            return;
//        }
//        
//        
//        //审核人扣费，并给申请人充值
//        $recharge_result = $this->recharge($uid,$apply_money,$id,$money_apply_info);
//        
//        if( $recharge_result['code'] != 1 ){
//            $return_result = array(
//                'code'  =>  6,
//                'msg'   =>  $recharge_result['msg'],
////                'errror_info'   =>  M('money_charge_log')->getLastSql(),
//            );
//            $this->ajaxReturn($return_result, 'JSON');
//            return;
//        }
//        
//        
//        //改写申请记录
//        $condition_save['id']   =   $id;
//        
//        $data = array(
//            'status'    =>  $pass,
//            'updated'   =>  time(),
//        );
//        
//        $res = $money_apply_obj->where($condition_save)->save($data);
//        
//        if( $res ){
//            $return_result = array(
//                'code'  =>  1,
//                'msg'   =>  '审核成功！！',
//            );
//            $this->ajaxReturn($return_result, 'JSON');
//        }
//        else{
//            $return_result = array(
//                'code'  =>  5,
//                'msg'   =>  '审核失败，请重试！',
//            );
//            $this->ajaxReturn($return_result, 'JSON');
//        }
//        
//    }



    //充值记录
    public function money_recharge_log(){

        $money_recharge_log = M('money_recharge_log');
        $distributor_obj = M('distributor');
        $condition = array();

        $type = I("get.type");
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');


        $uid = $this->uid;

        $condition['uid']   =   $uid;

        if( is_numeric($type) ){
            $condition['type'] = $type;
        }

        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }


        $count = $money_recharge_log->where($condition)->count();


        $list = array();

        $list_apply = array();
        $list_order = array();

        if( $count > 0 ){

            $list = $money_recharge_log->where($condition)->limit(30)->order('id desc')->select();

            //-----整理添加相应其它表的信息-----
            $uids = array();
            $applys = array();
            $order_notes = array();

            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_source_id = $v['source_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_source_id]) ){
                    $uids[$v_source_id] = $v_source_id;
                }
            }

            array_values($uids);

            $condition_dis = array();
            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info[0]['name'] = '总部';
            foreach( $dis_info as $k_dis=>$v_dis ){

                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }


            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_source_id = $v['source_id'];
                $v_type = $v['type'];

                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['source_info'] = $dis_key_info[$v_source_id];

                if( $v_type == 1 ){
                    $list_apply[] = $list[$k];
                }
                elseif( $v_type == 3 ){
                    $list_order[] = $list[$k];
                }



            }
            //-----end 整理添加相应其它表的信息-----
        }

        $this->list_apply = $list_apply;
        $this->list_order = $list_order;
        $this->list = $list;
        $this->display();
    }


    //订单详情
    public function ddxq() {
        $where['order_num'] = I('order_num');
        $row = M('Order')->where($where)->group('order_num')->find();
        //把产品分别显示
        $rol = M('Order')->where($where)->select();
        foreach ($rol as $k => $v) {
            $product = M('templet')->where(array('id' => $v['p_id']))->find();
            $rol[$k]['p_name'] = $product['name'];
        }
        $this->row = $row;
        $this->assign('rol', $rol);
        $this->display();
    }


    //提交记录
    public function money_refund(){
        $money_refund_obj = M('money_refund');
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');
        $condition = array();


        $uid = $this->uid;
        $condition['uid']   =   $uid;


        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
            'page_list_num' =>  10,
        );

        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $result = $Funds->get_money_refund($page_info,$condition);


//        $this->min_refund_money  = $Funds->get_min_refund_money();
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->display();
    }



    //提现申请
    public function refund_apply() {
        $distributor_obj = M('distributor');
        $money_refund_apply_obj = M('money_refund_apply');
        import('Lib.Action.Funds','App');
        $Funds = new Funds();


        $condition = array(
            'uid' => $this->uid,
        );
        $money_apply_info = $money_refund_apply_obj->where($condition)->limit(20)->order('created desc')->select();



        foreach( $money_apply_info as $k => $v ){
            $v_audit_id = $v['audit_id'];

            if( $v_audit_id != 0 ){
                $dis_info = $distributor_obj->where(array('id'=>$v_audit_id))->find();
                $audit_name = $dis_info['name'];
            }
            else{
                $audit_name = '总部';
            }

            $money_apply_info[$k]['audit_name'] = $audit_name;
        }

        $this->min_refund_money  = $Funds->get_user_can_refund_money($this->uid);
        $this->money_apply_info =   $money_apply_info;
        $this->display();
    }



    //提现申请详细
    public function money_refund_apply_detail(){
        $apply_id = I('get.id');

        $money_refund_apply_obj = M('money_refund_apply');

        $condition = array(
            'id' => $apply_id,
        );
        $money_apply_info = $money_refund_apply_obj->where($condition)->find();

        $is_audit = 0;
        //如果审核人为自己才能进行审核
        if( !empty($money_apply_info) && $money_apply_info['audit_id'] == $this->uid ){
            $is_audit = 1;
        }

        $this->is_audit = $is_audit;
        $this->list = $money_apply_info;
        $this->display();
    }



    //提交提现申请
    public function money_refund_apply_submit() {
        $money = trim(I('post.money'));
        $img = trim(I('post.imgval'));
        $apply_remark = trim(I('post.apply_remark'));
        $pay_type =trim(I('post.paytype'));
        $card_name =trim(I('post.cardname'));
        $card_number =trim(I('post.cardnumber'));
        $account_name =trim(I('post.accountname'));

        $uid = $this->uid;

//        print_r($this->_post());return;

        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $can_refund_money = $Funds->get_user_can_refund_money($uid);

        if( bccomp($money,$can_refund_money,2) == 1 ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '提现金额必须小于或等于可提现金额！',
            );
            $this->ajaxReturn($return_result, 'JSON');return;
        }

        if(empty($money)){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '请输入正确的金额',
            );
            $this->ajaxReturn($return_result, 'JSON');return;
        }

        $audit_id = 0;//审核人直接改为总部

        $apply_result = $Funds->add_money_refund_apply($uid,$audit_id,$money,$apply_remark,$img,$pay_type,$card_name,$card_number,$account_name);
        $result = [
            'code' => 1,
            'msg' => '提交成功,请耐心等候审核！',
            'info' =>$apply_result,
        ];

        $this->ajaxReturn($apply_result, 'JSON');
    }


    //审核下级充值申请
    public function xj_apply_list(){
        $money_apply_obj = M('money_apply');
        $distributor_obj = M('distributor');


        $condition = $condition_no = $condition_yes = array(
            'audit_id'   =>  $this->uid,
        );

        $condition_no['status']    =   '0';
        $condition_yes['status']    =   array('neq','0');

        $list_no = $money_apply_obj->where($condition_no)->order('id desc')->select();//充值申请的不能限定显示数
        $list_yes = $money_apply_obj->where($condition_yes)->order('id desc')->limit(10)->select();


        $count_total = $money_apply_obj->where($condition)->count();
        $count_no = $money_apply_obj->where($condition_no)->count();
        $count_yes =  $count_total - $count_no;


        if( !empty($list_no) || !empty($list_yes) ){
            //-----整理添加相应其它表的信息-----

            $uids = array();
            //获得用户信息
            foreach( $list_yes as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_audit_id]) ){
                    $uids[$v_audit_id] = $v_audit_id;
                }
            }

            foreach( $list_no as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_audit_id]) ){
                    $uids[$v_audit_id] = $v_audit_id;
                }
            }

            array_values($uids);

            //整理经销商信息
            $condition_dis = array();
            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){

                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }


            //整理
            $dis_key_info['0']['name'] = '总部';
            foreach( $list_yes as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];
                $v_status = $v['status'];


                $list_yes[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list_yes[$k]['audit_info'] = $dis_key_info[$v_audit_id];
            }
            foreach( $list_no as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];
                $v_status = $v['status'];

                $list_no[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list_no[$k]['audit_info'] = $dis_key_info[$v_audit_id];
            }
            //-----end 整理添加相应其它表的信息-----
        }

//        var_dump($list_yes);var_dump($list_no);return;
//        echo $count_no.'<br />'.$count_yes;return;

        $this->count_no =   $count_no;
        $this->count_yes =   $count_yes;

        $this->list_yes =   $list_yes;
        $this->list_no =   $list_no;


        $this->display();
    }//end func xj_apply_list

    //充值、提现申请显示
    public function check_apply(){
//
//        $money_apply=M('money_apply');
//        $money_refund_apply=M('money_refund_apply');
//        $condition=[
//            'audit_id'=>$this->uid,
//        ];
//
//        $apply_info=$money_apply->where($condition)->select();
//
//
//        //联表查询
//        $dis_info = [];
//        //将id取出来
//        foreach ($apply_info as $v) {
//            if (!isset($ids[$v['uid']])) {
//                $ids[$v['uid']] = $v['uid'];
//            }
//        }
//        //将取出来的id在另外的表根据id查询
//        $cats = M('distributor')->where(['id' => ['in', $ids]])->select();
//
//        //取出数据
//        foreach ($cats as $v) {
//            $dis_info[$v['id']] = $v;
//        }
//        foreach ($apply_info as $k => $v) {
//            $apply_info[$k]['name'] = $dis_info[$v['uid']]['name'];
//            $apply_info[$k]['level'] = $dis_info[$v['uid']]['level'];
//            $apply_info[$k]['levname'] = $dis_info[$v['uid']]['levname'];
//        }
//
//        $money_refund_apply=$money_refund_apply->where($condition)->select();
//
//        $dis_apply_info = [];
//        foreach ($money_refund_apply as $v) {
//            if (!isset($idss[$v['uid']])) {
//                $idss[$v['uid']] = $v['uid'];
//            }
//        }
//        $cats_info = M('distributor')->where(['id' => ['in', $idss]])->select();
//        //取出数据
//        foreach ($cats_info as $v) {
//            $dis_apply_info[$v['id']] = $v;
//        }
//
//        foreach ($money_refund_apply as $k => $v) {
//            $money_refund_apply[$k]['name'] = $dis_apply_info[$v['uid']]['name'];
//            $money_refund_apply[$k]['level'] = $dis_apply_info[$v['uid']]['level'];
//            $money_refund_apply[$k]['levname'] = $dis_apply_info[$v['uid']]['levname'];
//        }
//
//        $this->assign('apply_info', $apply_info);
//        $this->assign('money_refund_apply', $money_refund_apply);

//        import('Lib.Action.Funds','App');
//        $Funds = new Funds();
//        $money_refund_apply=M('money_refund_apply');
//        $money_apply = M('money_apply')->where(array('audit_id'=>$this->uid))->select();
//        $money_refund_apply=$money_refund_apply->where(array('audit_id'=>$this->uid))->select();
//        $dis_apply_info = [];
//        foreach ($money_apply as $v) {
//          if (!isset($idss[$v['uid']])) {
//               $idss[$v['uid']] = $v['uid'];
//           }
//        }
//        $page_info = [
//            'page_num'  =>  1,
//            'page_list_num' =>  30,
//        ];
//        $cats_info = M('distributor')->where(['id' => ['in', $idss]])->select();
//        foreach ($cats_info as $v) {
//            $dis_apply_info[$v['id']] = $v;
//            $condition['uid']   =$dis_apply_info[$v['id']]['id'];
//          $result= $Funds->get_money_apply($page_info,$condition);
//        }
//
//
//        //联表查询
//        $dis_info = [];
//        //将id取出来
//        foreach ($money_refund_apply as $v) {
//            if (!isset($ids[$v['uid']])) {
//                $ids[$v['uid']] = $v['uid'];
//            }
//        }
//        //将取出来的id在另外的表根据id查询
//        $cats = M('distributor')->where(['id' => ['in', $ids]])->select();
//
//        //取出数据
//        foreach ($cats as $v) {
//            $dis_info[$v['id']] = $v;
//            $condition['uid']   =$dis_info[$v['id']]['id'];
//            $result_info= $Funds->get_money_refund_apply($page_info,$condition);
//        }
//
//        $this->types = [
//            'apply'  =>  '充值申请',
//            'refund'    =>  '提现申请',
//        ];
//        $this->money_apply = $money_apply;
//        $this->list = $result['list'];
//        $this->list = $result_info['list'];
        
        import('Lib.Action.User','App');
        $User = new User();
        
        $this->open_upgrade_apply = $User->open_upgrade_apply;
        $this->rebate_status = C('REBATE')['OPEN'];
        $this->display();
    }

    //充值、提现申请列表的ajax
    public function get_apply_ajax(){
        if( !IS_AJAX ){
            return FALSE;
        }
        
        //这是前端显示的规则
        $admin_status = [
            0   =>  '未审核',
            1   =>  '已审核',
            2   =>  '不通过',
        ];
        

        $type = trim(I('type'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));
        $status = trim(I('status'));

//           $type='apply';
//           $page_num=1;
        
        
        

        if( empty($type) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if( empty($page_num) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Funds','App');
        $Funds = new Funds();

        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 20;
        }
        $condition['audit_id']   = $this->uid;

        if( $status != null ){
            $condition['status'] = $status;
        }
        
        $page_info=[
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        //充值
        if( $type == 'apply' ){
            $result = $Funds->get_money_apply($page_info,$condition);
        }

        //提现
        elseif($type == 'refund'){
            $result = $Funds->get_money_refund_apply($page_info,$condition);
        }
        //返利
        elseif($type == 'rebate'){
//            $condition['status'] = $status==2?3:$status;//返利的状态2为已通过，状态3为不通过
//            
//            import('Lib.Action.Rebate','App');
//            $Rebate = new Rebate();
//            
//            $condition = [
//                'pay_id'   =>  $this->uid,
//            ];
//            
//            if( $status != null ){
//                $condition['state'] = $status;
//            }
//            $result =$Rebate->get_rerebate($page_info,$condition);
            //重构返利后显示方法
            $where = [
                'payer_id' => $this->uid,
                'status' => $status,
            ];
            import('Lib.Action.NewRebate','App');
            $rebate = new NewRebate();
            $result = $rebate->get_other_rebate($page_info, $where);
        } elseif($type == 'team'){
            $where = [
                'payer_id' => $this->uid,
                'status' => $status,
            ];
            import('Lib.Action.NewRebate','App');
            $rebate = new NewRebate();
            $result = $rebate->get_team_rebate($page_info, $where);
        }
        elseif( $type=='upgrade_apply' ){
            import('Lib.Action.User','App');
            $User = new User();
            
            $result = $User->get_distributor_upgrade_apply($page_info,$condition);
        }
        //全部
        elseif($type == 'all'){
            
            $result =$Funds->get_money_apply($page_info,$condition);
            $result_refund = $Funds->get_money_refund_apply($page_info,$condition);

        }
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $result,
            'info_refund' => $result_refund,
            'audit_id'   =>  $this->uid,
            'condition' =>  $condition,
        ];
        $this->ajaxReturn($return_result);
    }

    //充值申请详细信息显示
    public function recharge_check(){
        $id=I('get.id');
        $condition = array(
            'id' =>$id,
        );
        $money_apply=M('money_apply')->where($condition)->find();

        $uid=$money_apply['uid'];
        $audit_id=$money_apply['audit_id'];

        $distributor_info=M('distributor')->where(array('id'=>$uid))->find();

        $money_funds=M('money_funds')->where(array('uid'=>$audit_id))->find();

        $check_info=number_format($money_funds['recharge_money']-$money_apply['apply_money'],2);

        $this->assign('money_apply', $money_apply);
        $this->assign('distributor_info', $distributor_info);

        $this->money_funds =$money_funds;
        $this->check_info =$check_info;
        $this->display();
    }

    //提现申请详细信息显示
    public function money_refund_check(){
        $id=I('get.id');
        $condition = array(
            'id' =>$id,
        );
        $money_refund_apply=M('money_refund_apply')->where($condition)->find();
        $uid=$money_refund_apply['uid'];
        $audit_id=$money_refund_apply['audit_id'];

//        $dis_money_refund_apply = M('money_refund_apply')->table(array('money_refund_apply'=>'a','distributor'=>'b'))->where('a.uid=b.id')->select();
//        var_dump($dis_money_refund_apply);
//        die;
        $distributor=M('distributor')->where(array('id'=>$uid))->find();
        $money_funds=M('money_funds')->where(array('uid'=>$audit_id))->find();
//        $check_info=number_format($money_funds['recharge_money']+$money_refund_apply['apply_money'],2);

        $this->money_refund_apply =$money_refund_apply;
        $this->assign('distributor', $distributor);
        $this->money_funds =$money_funds;
//        $this->check_info =$check_info;
        $this->display();
    }
    
    //升级申请
    public function upgrade_apply_check(){
        $id=I('get.id');
        
        import('Lib.Action.User','App');
        $User = new User();
        
        $condition = [
            'id'    =>  $id,
            'audit_id'   =>  $this->uid,
//            'status'=>  0,
        ];
        
        $result = $User->get_distributor_upgrade_apply([],$condition);
        
        $this->list = $result['list'][0];
        $this->display();
    }
    
    //审核升级申请
    public function upgrade_apply_pass(){
        $id = I('post.id');
        $status = I('post.status');//2为通过，3为不通过

        import('Lib.Action.User','App');
        $User = new User();
        
        $result = $User->upgrade_apply_pass($id,$status,$this->uid);

        $this->ajaxReturn($result, 'JSON');
    }
    
    

    //提现
    public function apply_refund_pass(){
        $id = I('post.id');
        $pass = I('post.pass');

        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $return_result = $Funds->apply_refund_pass($id,$pass,$_SESSION['managerid']);

        $this->ajaxReturn($return_result, 'JSON');
    }


    //审核申请搜素
    public function get_apply_search(){

        if(!IS_AJAX){
         // return FALSE;
        }
        $type=trim(I('post.type'));
        $name = trim(I('post.name'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

        $type='refund';
        $name="1";
        $page_num=1;

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Funds', 'App');
        $Funds = new Funds();

        //每页默认为10
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

//        $condition['name']  = array('like',"%$name%");
//        $condition['audit_id']   = $this->uid;
        $condition=[
            'name'=>array('like',"%$name%"),
            'audit_id' => $this->uid,
        ];
        //充值
        if( $type == 'apply' ){
            $result = $Funds->get_money_apply($page_info,$condition);
        }

        //提现
        elseif($type == 'refund'){
            $result = $Funds->get_money_refund_apply($page_info,$condition);
        }

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];

        $this->ajaxReturn($return_result);
    }
    
    //返利审核
    public function rebate_check(){
        
        $id = I('id');
        $type = I('type');
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();

        $condition = [
            'id'   =>  $id,
        ];

//        $result =$Rebate->get_rerebate([],$condition);
        import('Lib.Action.NewRebate','App');
        $rebate = new NewRebate();
        if ($type == 'rebate') {
            $result = $rebate->get_other_rebate([], $condition);

    //        print_r($result);return;
            $rebate_money = isset($result['list']['0']['money'])?$result['list']['0']['money']:0;
        } else if ($type == 'team') {
            $result = $rebate->get_team_rebate([], $condition);
            $rebate_money = isset($result['list']['0']['rebate_money'])?$result['list']['0']['rebate_money']:0;
        }
//        $rebate_money = $rebate_money+0.22;
        
        $money_funds = M('money_funds')->where(array('uid'=>$this->uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
//        $recharge_money = $recharge_money +110;
        
        $this->recharge_money = $recharge_money;//虚拟币金额
        
        $this->recharge_back = bcsub($recharge_money,$rebate_money,2);
        $this->list = $result['list']['0'];
        $this->type = $type;
        $this->display();
    }//end func rebate_check
    

}

?>