<?php

/**
 * 	用户登录控制
 */
class CommonAction extends Action {

    public function _initialize() {
        $admin_model = M('admin');
        
        $the_module_name = strtolower(MODULE_NAME);
        $the_action_name = strtolower(ACTION_NAME);
        
        if (!isset($_SESSION['aid'])) {
            echo "<script>window.top.location.href ='" . __APP__ . "/Radmin/Login/index';</script>";
        }
//        //SESSION时间到期，销毁SESSION，跳到登录页
//        if (time() - session('session_start_time') > C('SESSION_OPTIONS')['expire']) {
//            session_destroy();//真正的销毁在这里！
//            echo "<script>window.top.location.href ='" . __APP__ . "/Radmin/Login/index';</script>";
//        }
        
        
        
        $this->superids = [1,2];//超级管理员ID
        $this->aid = $_SESSION['aid'];
        $admin_info = $admin_model->where(['id'=>$this->aid])->find();
        $this->admin_info = $admin_info;
        $this->admin_auth = explode(',',$admin_info['auth']);
        
        
        if( !in_array($this->aid, $this->superids) && !empty($this->admin_auth) ){
            
            import('Lib.Action.Admin','App');
            $Admin = new Admin();
            
            $admin_auth_extra = $Admin->admin_auth_extra;
            
            if( !in_array($the_module_name, $admin_auth_extra) ){
                $admin_auth_module = $Admin->admin_auth_module;
            
                $all_action_str = '';
                foreach( $admin_auth_module as $auth_num => $action ){
                    if( in_array($auth_num, $this->admin_auth) ){
                        $all_action_str = $all_action_str.','.$action;
                    }
                }
                $all_action = explode(',',$all_action_str);
                $this->all_action = $all_action;
                
                if( !in_array($the_module_name, $all_action) ){
    //                echo "<script>alert('请注意，该账号无权限使用改模块！');window.top.location.href ='" . __APP__ . "/Radmin/index';</script>";
                    echo '权限不足';
                    exit();
                }
            }
        }
        
        import('Lib.Action.User','App');
        $User = new User();
        
        $this->open_upgrade_apply = $User->open_upgrade_apply;
    }

    
    //$otherFiled 连表查询出来的数据可能在更深的数组里。。。
    public function exportExcel($expTitle, $expCellName, $expTableData, $otherFiled = []) {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件名称
        $fileName = $_SESSION['account'] . date('_YmdHis'); //or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);

        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1'); //合并单元格
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            $other = [];
            for ($j = 0; $j < $cellNum; $j++) {
                if (!$expTableData[$i][$expCellName[$j][0]]) {
                    foreach ($otherFiled as $v) {
                        if (!isset($other[$v][$expCellName[$j][0]])) {
                            $other[$v][$expCellName[$j][0]] = 1;
                            $data = $expTableData[$i][$v][$expCellName[$j][0]];
                            break;
                        }
                    }
//                    if (!isset($other[$otherFiled[$i]][$expCellName[$j][0]])) {
//                        $other[$otherFiled[$i]][$expCellName[$j][0]] = 1;
//                        $data = $expTableData[$i][$otherFiled[$j]][$expCellName[$j][0]];
//                        setLog(json_encode($other));
//                    }
                } else {
                    $data = $expTableData[$i][$expCellName[$j][0]];
                }
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $data);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls"); //attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //导出Excel
    function expUser() {
        $xlsName = "User";
        $xlsModel = M('distributor');
        $pd = I('get.pd');
        
        //测试viska版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'viskatest' && $_SESSION['aid'] == 2 ){
            $this->error('测试版本不能导出');
            return;
        }
        
        if ($pd == 'manager') {
            
            
            $xlsCell = array(
                array('id', '编号'),
                array('name', '姓名'),
                array('phone', '手机号'),
                array('wechatnum', '微信号'),
                array('idennum', '身份证'),
                array('audname', '状态'),
                array('levname', '代理级别'),
                array('bossname', '上级名称'),
                array('pname', '审核人'),
                array('timea', '授权时间')
            );
            $xlsData = $xlsModel->where(array('level' => I('get.level')))->Field('id,name,phone,wechatnum,idennum,audited,levname,bossname,pname,time')->select();
            foreach ($xlsData as $k => $v) {
                $xlsData[$k]['idennum'] = " " . $xlsData[$k]['idennum'];
                //$xlsData[$k]['sex']=$v['sex']==1?'男':'女';
                if ($xlsData[$k]['audited'] == 0) {
                    $xlsData[$k]['audname'] = "未审核";
                } else if ($xlsData[$k]['audited'] == 2) {
                    $xlsData[$k]['audname'] = "待总部审核";
                } else {
                    $xlsData[$k]['audname'] = "已审核";
                }
                $xlsData[$k]['timea'] = date("Y-m-d H:i:s", $xlsData[$k]['time']);
            }
            $this->exportExcel($xlsName, $xlsCell, $xlsData);
        } 
        elseif( $pd=='money_recharge_log' ){
            $xlsName = 'money_recharge_log';
            $xlsCell = array(
                array('dis_name', '代理名字'),
                array('dis_phone', '手机号'),
                array('dis_wechatnum', '微信号'),
                array('dis_idennum', '身份证'),
                array('dis_levname', '级别'),
                array('dis_authnum', '授权编号'),
                array('dis_time', '申请时间'),
                array('dis_update_time', '审核时间'),
                array('source_name', '虚拟币来源'),
                array('money', '充值金额'),
                array('type_name', '充值类型'),
                array('note_name', '备注'),
                array('created_format', '记录时间'),
            );
            
            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $result = $Funds->get_money_recharge_log();
            
            $this->exportExcel($xlsName, $xlsCell, $result['list']);
        }
        elseif( $pd=='money_charge_log' ){
            $xlsName = 'money_charge_log';
            $xlsCell = array(
                array('dis_name', '代理名字'),
                array('dis_phone', '手机号'),
                array('dis_wechatnum', '微信号'),
                array('dis_idennum', '身份证'),
                array('dis_levname', '级别'),
                array('dis_authnum', '授权编号'),
                array('dis_time', '申请时间'),
                array('dis_update_time', '审核时间'),
                array('type_name', '扣费类型'),
                array('order_num_format', '扣费信息'),
                array('money', '扣费金额'),
                array('created_format', '扣费时间'),
            );
            
            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $result = $Funds->get_money_charge_log();
            
            $this->exportExcel($xlsName, $xlsCell, $result['list']);
        } elseif( $pd=='money_funds' ){
            //根据条件筛选
            $con_url = I('con_url');
            $condition = unserialize(base64_decode($con_url));
            $xlsName = 'money_funds';
            $xlsCell = array(
                array('dis_name', '代理名字'),
                array('dis_phone', '手机号'),
                array('dis_wechatnum', '微信号'),
                array('dis_idennum', '身份证'),
                array('dis_levname', '级别'),
                array('dis_authnum', '授权编号'),
                array('dis_time', '申请时间'),
                array('dis_update_time', '审核时间'),
                array('recharge_money', '当前剩余金额'),
                array('can_refund_money', '当前可提现金额'),
                array('his_recharge_money', '历史总充值金额'),
                array('his_charge_money', '历史总扣费金额'),
            );
            
            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $result = $Funds->get_money_funds_log("", $condition);
            
            $this->exportExcel($xlsName, $xlsCell, $result['list']);
        }
        elseif( $pd=='money_refund' ){
            $xlsName = 'money_refund';
            $xlsCell = array(
                array('dis_name', '代理名字'),
                array('dis_phone', '手机号'),
                array('dis_wechatnum', '微信号'),
                array('dis_idennum', '身份证'),
                array('dis_levname', '级别'),
                array('dis_authnum', '授权编号'),
                array('dis_time', '申请时间'),
                array('dis_update_time', '审核时间'),
                array('money', '提现金额'),
                array('type_name', '类型'),
                array('created_format', '扣费时间'),
            );
            
            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $result = $Funds->get_money_refund();
            
            $this->exportExcel($xlsName, $xlsCell, $result['list']);
        } elseif( $pd=='count_user' ){
            $xlsCell = array(
                array('name', '代理'),
                array('wechatnum', '微信号'),
                array('phone', '手机号'),
                array('levname', '级别'),
                array('authnum', '授权编号'),
                array('audited', '状态'),
                array('time', '申请时间'),
//                array('update_time', '审核时间'),
                array('rec_name', '推荐人'),
                array('rec_wechatnum', '推荐人微信号'),
                array('rec_phone', '推荐人手机号'),
                array('rec_levname', '推荐人级别'),
                array('rec_authnum', '推荐人授权编号'),
                array('rec_audited', '推荐人状态'),
                array('rec_time', '推荐人申请时间'),
//                array('rec_update_time', '推荐人审核时间'),
                array('team_num', '我的团队总人数'),
                array('person_money', '个人业绩'),
                array('team_money', '团队业绩'),
            );
            
            import('Lib.Action.User','App');
            $User = new User();
            $condition['is_get_team_info'] = TRUE;
            
            //根据条件筛选
            $con_url = I('con_url');
            $condition = unserialize(base64_decode($con_url));
//            var_dump($condition);die;
            $user_order_count = $User->get_users_count($condition, $condition);
            
//print_r($user_order_count);return;
            
            if( $user_order_count['code'] != 1 ){
                $this->error($user_order_count['msg']);
                return;
            }
            
            $count_result = $user_order_count['result'];
            $dis_info = $count_result['dis_info'];
            $distributor = M('distributor');
            foreach ($dis_info as $k => $v) {
                if ($v['recommendID']) {
                    $rec = $distributor->find($v['recommendID']);
                    $dis_info[$k]['rec_name'] = $rec['name'];
                    $dis_info[$k]['rec_wechatnum'] = $rec['wechatnum'];
                    $dis_info[$k]['rec_phone'] = $rec['phone'];
                    $dis_info[$k]['rec_levname'] = $rec['levname'];
                    $dis_info[$k]['rec_authnum'] = $rec['authnum'];
                    if ($rec['audited'] == 1) {
                        $dis_info[$k]['rec_audited'] = '已审核';
                    } else {
                        $dis_info[$k]['rec_audited'] = '未审核';
                    }
                    $dis_info[$k]['rec_time'] = date('Y-m-d H:i:s', $rec['time']);
//                    $dis_info[$k]['rec_update_time'] = date('Y-m-d H:i:s', $rec['update_time']);
                } else {
                    $dis_info[$k]['rec_name'] = '总部';
                    $dis_info[$k]['rec_wechatnum'] = '总部';
                    $dis_info[$k]['rec_phone'] = '总部';
                    $dis_info[$k]['rec_levname'] = '总部';
                    $dis_info[$k]['rec_authnum'] = '总部';
                    $dis_info[$k]['rec_audited']  = '总部';
                    $dis_info[$k]['rec_time'] = '总部';
                    $dis_info[$k]['rec_update_time'] = '总部';
                }
                if ($v['audited'] == 1) {
                    $dis_info[$k]['audited'] = '已审核';
                } else {
                    $dis_info[$k]['audited'] = '未审核';
                }
                $dis_info[$k]['time'] = date('Y-m-d H:i:s', $v['time']);
//                $dis_info[$k]['update_time'] = date('Y-m-d H:i:s', $v['update_time']);
                
                
            }
            
            $this->exportExcel($xlsName, $xlsCell, $dis_info);
        } elseif( $pd=='team' ){
            $xlsCell = array(
               array('name', '代理'),
                array('wechatnum', '微信号'),
                array('phone', '手机号'),
                array('levname', '级别'),
                array('authnum', '授权编号'),
                array('audited', '状态'),
                array('rec_name', '推荐人'),
                array('rec_wechatnum', '推荐人微信号'),
                array('rec_phone', '推荐人手机号'),
                array('rec_levname', '推荐人级别'),
                array('rec_authnum', '推荐人授权编号'),
                array('rec_audited', '推荐人状态'),
            );
            $distributor = M('distributor');
            import('Lib.Action.Team','App');
            $team_obj = new Team();

            //读取缓存团队
            $team_path = get_team_path_by_cache();

            $team_ids = $team_obj->get_team_ids(trim(I('id')), $team_path);
            $field = 'id,name,wechatnum,phone,levname,authnum,audited,recommendID';
            $users = $distributor->field($field)->where(['id' => ['in', $team_ids]])->order('level asc,time asc')->select();
            
            
            foreach ($users as $k => $v) {
                if ($v['audited'] == 1) {
                    $users[$k]['audited'] = '已审核';
                } else {
                    $users[$k]['audited'] = '未审核';
                }
                if ($v['recommendID']) {
                    $rec = $distributor->field($field)->find($v['recommendID']);
                    $users[$k]['rec_name'] = $rec['name'];
                    $users[$k]['rec_phone'] = $rec['phone'];
                    $users[$k]['rec_wechatnum'] = $rec['wechatnum'];
                    $users[$k]['rec_levname'] = $rec['levname'];
                    $users[$k]['rec_authnum'] = $rec['authnum'];
                    if ($rec['audited'] == 1) {
                        $users[$k]['rec_audited'] = '已审核';
                    } else {
                        $users[$k]['rec_audited'] = '未审核';
                    }
                } else {
                    $users[$k]['rec_name'] = '总部';
                    $users[$k]['rec_phone'] = '总部';
                    $users[$k]['rec_wechatnum'] = '总部';
                    $users[$k]['rec_levname'] = '总部';
                    $users[$k]['rec_authnum'] = '总部';
                    $users[$k]['rec_audited'] = '总部';
                }
                
            }
            $this->exportExcel($xlsName, $xlsCell, $users);
        } elseif( $pd=='rebate_other' ){
            $xlsCell = array(
                array('name', '获利人姓名'),
                array('wechatnum', '微信号'),
                array('phone', '手机号'),
                array('levname', '级别'),
                array('name', '被推荐人姓名'),
                array('wechatnum', '微信号'),
                array('phone', '手机号'),
                array('levname', '级别'),
                array('name', '支付人'),
                array('money', '返利金额'),
                array('status_name', '状态'),
                array('time', '返利时间'),
                array('month', '月份'),
            );
            //根据条件筛选
            $con_url = I('con_url');
            $condition = unserialize(base64_decode($con_url));
            import('Lib.Action.NewRebate','App');
            $rebate = new NewRebate();
            $page_info = [
                'page_list_num' => 1000000,
            ];
            $rebate_info = $rebate->get_other_rebate($page_info, $condition);
            $this->exportExcel($xlsName, $xlsCell, $rebate_info['list'], ['uid_info','rec_id_info','payer_id_info']);
        } elseif( $pd=='rebate_team' ){
            $xlsCell = array(
                array('name', '代理姓名'),
                array('phone', '代理手机号'),
                array('levname', '代理等级'),
                array('person_money', '个人业绩'),
                array('total_money', '团队业绩'),
                array('ratio', '返利比例'),
                array('rebate_money', '返利金额'),
                array('status_name', '状态'),
                array('month', '月份'),
            );
            //根据条件筛选
            $con_url = I('con_url');
            $condition = unserialize(base64_decode($con_url));
            import('Lib.Action.NewRebate','App');
            $rebate = new NewRebate();
            $page_info = [
                'page_list_num' => 1000000,
            ];
            $rebate_info = $rebate->get_team_rebate($page_info, $condition);
            $this->exportExcel($xlsName, $xlsCell, $rebate_info['list'], ['uid_info']);
        }
        elseif ($pd == 'integralorder'){
            $order = M('integralorder');
            $templet = M('integraltemplet');
            $xlsCell = array(
                array('order_num', '订单号'),
                array('name', '订货代理'),
                array('phone', '订货代理电话'),
                array('o_id', '接单代理'),
                array('bossphone', '接单代理电话'),
                array('p_name', '产品名称'),
                array('num', '产品数量'),
                array('integral', '单价积分'),
                array('total_integral', '总积分'),
                array('s_name', '收货人姓名'),
                array('s_phone', '收货人电话'),
                array('s_addre', '收货地址'),
                array('shipper','快递公司'),
                array('ordernumber', '快递单号'),
                array('notes', '备注'),
                array('timea', '申请时间'),
                array('sname', '状态')
            );

            //根据条件筛选
            $condition = [];
            $con_url = I('con_url');
            $condition1 = unserialize(base64_decode($con_url));

            $applyList = $order->where($condition1)->order('time desc')->select();

            foreach ($applyList as $k => $v) {
                $list = $xlsModel->where(array('id' => $v['user_id']))->find();
                if ($v['o_id']) {
                    $parent = $xlsModel->field(['id,name,phone'])->find($v['o_id']);
                    $bossname = $parent['name'];
                    $bossphone = $parent['phone'];
                } else {
                    $bossname = '总部';
                    $bossphone = '总部';
                }
                $applyList[$k]['name'] = $list['name'];
                $applyList[$k]['phone'] = $list['phone'];
                $applyList[$k]['levname'] = $list['levname'];
                $applyList[$k]['o_id'] = $bossname;
                $applyList[$k]['bossphone'] = $bossphone;

                //产品规格显示
                $applyList[$k]['p_name'] = $v['p_name']. ' '. $v['style'];

                if( $pd == 'orderapply' ){
                    $applyList[$k]['order_num'] = '`'.$v['order_num'];
                }

                if ($applyList[$k]['status'] == 1) {
                    $applyList[$k]['sname'] = "待付款";
                } else if ($applyList[$k]['status'] == 2) {
                    $applyList[$k]['sname'] = "配送中";
                }
                else if ($applyList[$k]['status'] == 6) {
                    $applyList[$k]['sname'] = "待发货";
                }else {
                    $applyList[$k]['sname'] = "已收货";
                }
                $applyList[$k]['timea'] = date("Y-m-d H:i:s", $applyList[$k]['time']);
            }
            $this->exportExcel($xlsName, $xlsCell, $applyList);
        }
        else {//订单
            $order = M('Order');
            $templet = M('Templet');
            $xlsCell = array(
                array('order_num', '订单号'),
                array('name', '订货代理'),
                array('phone', '订货代理电话'),
                array('o_id', '接单代理'),
                array('bossphone', '接单代理电话'),
                array('p_name', '产品名称'),
                array('num', '产品数量'),
                array('price', '单价'),
                array('total_price', '总价'),
                array('s_name', '收货人姓名'),
                array('s_phone', '收货人电话'),
                array('s_addre', '收货地址'),
                array('shipper','快递公司'),
                array('ordernumber', '快递单号'),
                array('notes', '备注'),
                array('timea', '申请时间'),
                array('sname', '状态')
            );
            
            //根据条件筛选
            $condition = [];
            $con_url = I('con_url');
            $condition1 = unserialize(base64_decode($con_url));

            $applyList = $order->where($condition1)->order('time desc')->select();

            foreach ($applyList as $k => $v) {
                $list = $xlsModel->where(array('id' => $v['user_id']))->find();
                if ($v['o_id']) {
                    $parent = $xlsModel->field(['id,name,phone'])->find($v['o_id']);
                    $bossname = $parent['name'];
                    $bossphone = $parent['phone'];
                } else {
                    $bossname = '总部';
                    $bossphone = '总部';
                }
                $applyList[$k]['name'] = $list['name'];
                $applyList[$k]['phone'] = $list['phone'];
                $applyList[$k]['levname'] = $list['levname'];
                $applyList[$k]['o_id'] = $bossname;
                $applyList[$k]['bossphone'] = $bossphone;
                
                //产品规格显示
                $applyList[$k]['p_name'] = $v['p_name']. ' '. $v['style'];
                
                if( $pd == 'orderapply' ){
                    $applyList[$k]['order_num'] = '`'.$v['order_num'];
                }

                if ($applyList[$k]['status'] == 1) {
                    $applyList[$k]['sname'] = "待发货";
                } else if ($applyList[$k]['status'] == 2) {
                    $applyList[$k]['sname'] = "配送中";
                } else {
                    $applyList[$k]['sname'] = "已收货";
                }
                $applyList[$k]['timea'] = date("Y-m-d H:i:s", $applyList[$k]['time']);
            }
            
            
            
            $this->exportExcel($xlsName, $xlsCell, $applyList);
        }
    }
    
    
    

    /**
     *
     * 显示导入页面 ...
     */
    /*     * 实现导入excel
     * */
    function impUser() {
        if (!empty($_FILES)) {
            import("@.ORG.UploadFile");
            $config = array(
                'allowExts' => array('xlsx', 'xls'),
                'savePath' => './Public/upload/',
                'saveRule' => 'time',
            );
            $upload = new UploadFile($config);
            if (!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            } else {
                $info = $upload->getUploadFileInfo();
            }

            vendor("PHPExcel.PHPExcel");
            $file_name = $info[0]['savepath'] . $info[0]['savename'];
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name, $encode = 'utf-8');
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            for ($i = 3; $i <= $highestRow; $i++) {
                $data['account'] = $data['truename'] = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
                $sex = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
                // $data['res_id']    = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                $data['class'] = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
                $data['year'] = $objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();
                $data['city'] = $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
                $data['company'] = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
                $data['zhicheng'] = $objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue();
                $data['zhiwu'] = $objPHPExcel->getActiveSheet()->getCell("J" . $i)->getValue();
                $data['jibie'] = $objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue();
                $data['honor'] = $objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
                $data['tel'] = $objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue();
                $data['qq'] = $objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue();
                $data['email'] = $objPHPExcel->getActiveSheet()->getCell("O" . $i)->getValue();
                $data['remark'] = $objPHPExcel->getActiveSheet()->getCell("P" . $i)->getValue();
                $data['sex'] = $sex == '男' ? 1 : 0;
                $data['res_id'] = 1;

                $data['last_login_time'] = 0;
                $data['create_time'] = $data['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
                $data['login_count'] = 0;
                $data['join'] = 0;
                $data['avatar'] = '';
                $data['password'] = md5('123456');
                M('Member')->add($data);
            }
            $this->success('导入成功！');
        } else {
            $this->error("请选择上传的文件");
        }
    }
    
    
    
    /**
     *
     * 显示导入页面 ...
     */
    /*     * 实现导入excel
     * */
    function impexcel($info,$type='',$extension) {
        if (!empty($info)) {
            
            vendor("PHPExcel.PHPExcel");
            $file_name = $info[0]['savepath'] . $info[0]['savename'];
            
//            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
//            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            
            if( $extension == 'xlsx' ){
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            }
            else{
                $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            
            
            $objPHPExcel = $objReader->load($file_name, $encode = 'utf-8');
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            
            $column_str = 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
            $column = [];
            
            for( $i=0;$i<=30;$i++ ){
                $cur_column = substr($column_str, $i,1);
                if( $cur_column == $highestColumn ){
                    break;
                }
                
                $column[] = $cur_column;
            }
            
//            $return_result = [
//                'code'  =>  1,
//                'msg'   =>  'succ',
//                'info'  =>  [
//                    $column,$highestColumn
//                ],
//            ];
//
//            return $return_result;
            
            
            $update_res = FALSE;
            
            $error_msg = '';
            
            if( $type == 'order' ){
                $update_array = array();
                $data = array();
                $all_shipper = AllShipperCode();
                $all_shipper_flip = array_flip($all_shipper);
                
                for ($i = 3; $i <= $highestRow; $i++) {
                    
                    
                    $this_order_num = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
                    if( !is_numeric($this_order_num) ){
                        $this_order_num_len = strlen($this_order_num);
                        $this_order_num = substr($this_order_num,1,$this_order_num_len);
                    }
                    
                    
                    $this_shipper = '';
                    $this_shipper_true = trim($objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue());
                    $this_ordernumber = trim($objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue());
                    
                    if( isset($all_shipper[$this_shipper_true]) ){
                        $this_shipper = $this_shipper_true;
                    }
                    elseif( isset($all_shipper_flip[$this_shipper_true]) ){
                        $this_shipper = $all_shipper_flip[$this_shipper_true];
                    }
                    
                    if( !empty($this_order_num) &&!empty($this_ordernumber) && !empty($this_shipper) ){
                        $update_array[$i]['order_num'] = $data[$i]['order_num'] = $this_order_num;
                        
                        $update_array[$i]['shipper'] = $this_shipper;
                        $update_array[$i]['ordernumber'] = $this_ordernumber;
                    
//                        $data[$i]['name'] = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
//                        $data[$i]['phone'] = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
//                        $data[$i]['bossname'] = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
//                        $data[$i]['bossphone'] = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
//                        $data[$i]['pr_namenum'] = $objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();
//                        $data[$i]['s_name'] = $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
//                        $data[$i]['s_phone'] = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
//                        $data[$i]['s_addre'] = $objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue();
                        
                        
                        
//                        $data[$i]['notes'] = $objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue();
//                        $data[$i]['timea'] = $objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
                    }
                }
                
                //return $update_array;
                
//                print_r($update_array);return;
                
                $update_res = $this->batch_update('order',$update_array,'order_num');

                $error_msg = '请注意填写的快递单号字段必须为文本格式，并且按照导出的EXCEL格式进行导入，如果已经设置了快递单号的不能进行导入设置！';
            }
            else if( $type == 'integralorder' ){
                $update_array = array();
                $data = array();
                $all_shipper = AllShipperCode();
                $all_shipper_flip = array_flip($all_shipper);

                for ($i = 3; $i <= $highestRow; $i++) {


                    $this_order_num = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
                    if( !is_numeric($this_order_num) ){
                        $this_order_num_len = strlen($this_order_num);
                        $this_order_num = substr($this_order_num,1,$this_order_num_len);
                    }


                    $this_shipper = '';
                    $this_shipper_true = trim($objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue());
                    $this_ordernumber = trim($objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue());

                    if( isset($all_shipper[$this_shipper_true]) ){
                        $this_shipper = $this_shipper_true;
                    }
                    elseif( isset($all_shipper_flip[$this_shipper_true]) ){
                        $this_shipper = $all_shipper_flip[$this_shipper_true];
                    }

                    if( !empty($this_order_num) &&!empty($this_ordernumber) && !empty($this_shipper) ){
                        $update_array[$i]['order_num'] = $data[$i]['order_num'] = $this_order_num;
                        $update_array[$i]['shipper'] = $this_shipper;
                        $update_array[$i]['ordernumber'] = $this_ordernumber;

//                        $data[$i]['name'] = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
//                        $data[$i]['phone'] = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
//                        $data[$i]['bossname'] = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
//                        $data[$i]['bossphone'] = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
//                        $data[$i]['pr_namenum'] = $objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();
//                        $data[$i]['s_name'] = $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
//                        $data[$i]['s_phone'] = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
//                        $data[$i]['s_addre'] = $objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue();



//                        $data[$i]['notes'] = $objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue();
//                        $data[$i]['timea'] = $objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
                    }
                }

                //return $update_array;

//                print_r($update_array);return;

                $update_res = $this->batch_update('integralorder',$update_array,'order_num');

                $error_msg = '请注意填写的快递单号字段必须为文本格式，并且按照导出的EXCEL格式进行导入，如果已经设置了快递单号的不能进行导入设置！';
            }
            elseif( $type == 'manager' ){
//                echo 'manager<hr />';
                
                $update_res = TRUE;
                $update_array = array();
                $data = array();
                $all_bosswechatnum = array();
                
                import('Lib.Action.User','App');
                $User = new User();
                
                $levnames = C('LEVEL_NAME');
                $levnames_key = array_flip($levnames);
                
                //这里写入导入的excel表的对应字符
                $all_cnkey = [
                    'authnum'=>'授权代码',
                    'name'=>'授权名',//*这是必须输入*
                    'phone'=>'手机号',
                    'wechatnum'=>'微信号',//*这是必须输入*
                    'level'=>'代理级别',//如果输入必须为数字//*代理级别或代理级别名*必须输入一个
                    'levname'=>'代理级别名',//这个才是代理级别名
                    'bossauthnum'=>'上家授权代码',
                    'bossname'=>'上家名称',
                    'pname'=>'审核人姓名',
                    'recommendIDauthnum'=>'推荐人授权代码',
                    'recommendIDname'=>'推荐人姓名',
                    'time'=>'授权时间',
                ];

                $key = [];//对照key
                
                for ($i = 1; $i <= $highestRow; $i++) {

                    if( empty($key) ){
                        foreach( $column as $alphabet ){
                            $value = $objPHPExcel->getActiveSheet()->getCell($alphabet . $i)->getValue();

                            if( empty($value) ){
                                continue;
                            }

                            $the_key = array_search($value,$all_cnkey);

                            $key[$alphabet] = $the_key;
                        }
                        //这个循环不参与
                        continue;
                    }
                    
//                    return $res = [
//                        'code'  =>  2,
//                        'msg'   =>  '11',
//                        'info'  =>  $key,
//                    ];
                    
                    $newdata = [];
                    foreach( $key as $alphabet => $sqlkey ){

                        $newdata[$sqlkey] = $objPHPExcel->getActiveSheet()->getCell($alphabet . $i)->getValue();
                    }

//                    $address = $this->check_str($address);

//                    return $res = [
//                        'code'  =>  2,
//                        'msg'   =>  '11',
//                        'info'  =>  $newdata,
//                    ];
                    
                    //因为这个最高级别不输入
//                    if( $newdata['level'] == 1 ){
//                        continue;
//                    }
                    
//                    return $res = [
//                        'code'  =>  2,
//                        'msg'   =>  '122221',
//                        'info'  =>  $newdata,
//                    ];
                    
                    

                    if( !empty($newdata['name']) && !empty($newdata['wechatnum']) ){

                        if( isset($newdata['level']) ){
                            $newdata['levname'] = $levnames[$newdata['level']];
                        }
                        
                        if( isset($newdata['levname']) && empty($newdata['level']) ){
                            //从级别名称获取级别
                            if( isset($levnames_key[$newdata['levname']]) ){
                                $newdata['level'] = $levnames_key[$newdata['levname']];
                            }
                        }
                        
                        
                        if( empty($newdata['level']) || !is_numeric($newdata['level']) ){
                            continue;
                        }
                        
                        
                        $recommend_info = $par_info = [];

                        if( !empty($newdata['bossauthnum']) && $newdata['bossauthnum'] != '总部' ){
                            $condition_sear = [
                                'authnum'  =>  $newdata['bossauthnum'],
                            ];

                            $par_info = M('distributor')->where($condition_sear)->field('id')->find();
                        }

                        if( !empty($par_info) ){
                            $data[$i]['pid'] = $par_info['id'];
                        }
                        else{
                            $data[$i]['pid'] = 0;
                        }
                        
                        if( !empty($newdata['recommendIDauthnum']) && $newdata['recommendIDauthnum'] != '总部' ){
                            $condition_sear = [
                                'authnum'  =>  $newdata['recommendIDauthnum'],
                            ];

                            $recommend_info = M('distributor')->where($condition_sear)->field('id')->find();
                        }
                        
                        if( !empty($recommend_info) ){
                            $data[$i]['recommendID'] = $recommend_info['id'];
                        }
                        else{
                            $data[$i]['recommendID'] = 0;
                        }

//                        $all_bosswechatnum[$i] = $bosswechatnum;

                        $data[$i]['name'] = $newdata['name'];
                        $data[$i]['wechatnum'] = $newdata['wechatnum'];
                        $data[$i]['phone'] = $newdata['phone'];
                        $data[$i]['idennum'] = $this->check_str($newdata['idennum']);
                        $data[$i]['address'] = !empty($newdata['address'])?$newdata['address']:'未知';
                        $data[$i]['bossname'] = $newdata['bossname'];
                        $data[$i]['levname'] = $newdata['levname'];
                        $data[$i]['level'] = $newdata['level'];
                        $data[$i]['authnum'] = $newdata['authnum'];
                        $data[$i]['pname'] = !empty($newdata['pname'])?$newdata['pname']:'总部';

//                        if( !empty($newdata['time']) && is_numeric(strtotime($newdata['time'])) ){
//                            $data[$i]['time'] = strtotime($newdata['time']);
//                        }
//                        else{
                            $data[$i]['time'] = time();
//                        }

                        //默认
                        $data[$i]['headimgurl'] = __PUBLIC__.'/images/headimg.png';
                        $data[$i]['nickname'] = $data[$i]['name'];
//                        $data[$i]['path'] = '0';
                        $data[$i]['password'] = md5(substr($data[$i]['phone'], -6));
                        $data[$i]['isRecommend'] = 0;
                        $data[$i]['audited'] = 1;
                        $data[$i]['managed'] = 2;
                        $data[$i]['openid'] = 'radmin'.time().md5($data[$i]['wechatnum']);
                        $data[$i]['email'] = '';
                        $data[$i]['isInternal'] = '0';
//                        $data[$i]['authnum'] = substr($phone, -6) . substr(time(), -4); //生成授权号
                        $data[$i]['tallestID'] = 0;
                        $data[$i]['recommendID'] = 0;
                        
                        
                        
//                        return $res = [
//                            'code'  =>  2,
//                            'msg'   =>  '133331',
//                            'info'  =>  [$data[$i],$i],
//                        ];
                        
                        $add_result = $User->add($data[$i],'radmin');

                        if( $add_result['code'] != 1 ){
                            $error_msg = '部分导入报错：'.$add_result['msg'].','.$error_msg;
                            $update_res = FALSE;
                        }
                        
//                        return $res = [
//                            'code'  =>  2,
//                            'msg'   =>  '1000031',
//                            'info'  =>  $add_result,
//                        ];
                    }
                    
                    
//                    return $res = [
//                            'code'  =>  2,
//                            'msg'   =>  '155531',
//                            'info'  =>  $newdata,
//                        ];
                }
                
                
                
//                $new_data = array_values($data);
                
//                print_r($new_data);return;
                
//                $update_res = $distributor_model->addAll($new_data);
                
//                var_dump($update_res);return;
                
//                $error_msg = $distributor_model->getLastSql();
//                echo $error_msg;return;
                
                
            }
            
            if( empty($key) ){
                $update_res = FALSE;
            }
            elseif( empty($data) ){
                $code = 4;
                $msg = '没有获取到内容！';
            }
            
            if( $update_res ){
//                $this->success('导入成功！');
                $msg = '导入成功！';
                $code = 1;
            }
            else{
//                $this->error('导入失败！'.$error_msg);
                $msg = '导入失败！'.$error_msg;
                $code = 2;
            }
            
            
        } else {
            $msg = '没有获取到任何内容';
            $code = 3;
        }
        
        $return_result = [
            'code'  =>  $code,
            'msg'   =>  $msg,
            'info'  =>  $data,
            'key'   =>  $key,
        ];
        
        return $return_result;
    }
    
    //检查是否字符串格式，并进行处理
    private function check_str($data){
        
        if(is_string($data) ){
            return $data;
        }
        
        return '';
    }//end func check_str
    
    
    
    //批量修改  data二维数组 field关键字段  参考ci 批量修改函数 传参方式
    private function batch_update($table_name='',$data=array(),$field=''){
        if(!$table_name||!$data||!$field){
            return false;
        }else{
            $sql='UPDATE `'.$table_name.'`';
        }
        $con=array();
        $con_sql=array();
        $fields=array();
        foreach ($data as $key => $value) {
            $x=0;
            foreach ($value as $k => $v) {
                if($k!=$field&&!$con[$x]&&$x==0){
                    $con[$x]=" set {$k} = (CASE {$field} ";
                }elseif($k!=$field&&!$con[$x]&&$x>0){
                    $con[$x]="  {$k} = (CASE {$field} ";
                }
                if($k!=$field){
                    $temp=$value[$field];
                    $con_sql[$x].=   " WHEN '{$temp}' THEN '{$v}' ";
                    $x++;
                }
            }
            $temp=$value[$field];
            if(!in_array($temp,$fields)){
                    $fields[]=$temp;
            }    
        }
        $num=count($con)-1;
        foreach ($con as $key => $value) {
            foreach ($con_sql as $k => $v) {
                if($k==$key&&$key<$num){
                    $sql.=$value.$v.' end),';
                }elseif($k==$key&&$key==$num){
                    $sql.=$value.$v.' end)';
                }
            }
        }
        $str=implode(',',$fields);
        $sql.=" where {$field} in({$str})";
//        return $sql;
        
        $res=M($table_name)->execute($sql);
        return $res;
    }//end func batch_update
    
    

    //获取代理
    public function agent() {
        $m_search = toValid(I('term'));
        $where['name'] = array('LIKE', '%' . $m_search . '%');
        $where['audited'] = 1;
        $list = M('Distributor')->field("id,name,levname,level")->where($where)->select();
        $this->ajaxReturn($list, 'JSON');
    }

    
    /**
     * 添加后台操作日志
     * @param string $log  //日志记录，尽量简短的记录关键信息
     * @return bool
     */
    public function add_active_log($log){
        
        $aid = $_SESSION['aid'];//当前登录的后台管理用户
        
        import('Lib.Action.Admin','App');
        $Admin = new Admin();
        
        $result = $Admin->add_active_log($aid,$log);
        
        return $result;
    }//end func add_active_log
    
    public function upload()
    {
        $upload_dir_name = I('post.upload_dir_name');
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型

        $upload->savePath = './upload/'.$upload_dir_name.'/';// 设置附件上传目录

        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        if ($upload->upload()) {
            $info = $upload->getUploadFileInfo();
            $image = substr($info[0]['savepath'],1) . $info[0]['savename'];
            
            $this->ajaxReturn(['code' => 0, 'msg' => '上传成功', 'src' => $image], 'JSON');
        } else {
            $this->ajaxReturn(['code' => 1, 'msg' => '上传失败'], 'JSON');
        }
//        return $image;
    }

    /**
     *
     * @param type $data 要连表的数据
     * @param type $rel_tabel_name 要连表的表名
     * @param type $rel_field 关联字段
     * @return type
     */
    public function get_related_data($data, $rel_tabel_name, $rel_field) {
        $ids = [];
        $rel_data = [];
        foreach ($data as $v) {
            $ids[] = $v[$rel_field];
        }
        array_unique($ids);
        if ($ids) {
            $rel_info = M($rel_tabel_name)->where(['id' => ['in', $ids]])->select();
            foreach ($rel_info as $info) {
                $rel_data[$info['id']] = $info;
            }

            foreach ($data as $k => $v) {
                $data[$k]['info'] = $rel_data[$v[$rel_field]];
            }
        }
        return $data;
    }
    
    
    //订单xls表导入相关方法
    //导入快递单号
    public function impordernumber(){
        $upload_res = $this->xlsupload();
        
        if( $upload_res['code'] != 1 ){
//            $this->error($upload_res['msg']);
            echo $this->ajaxReturn($upload_res,'JSON');
            return;
        }
//        print_r($upload_res);return;
        
        $info = $upload_res['info'];
        $path = $upload_res['path'];
        $extension = $info[0]['extension'];
        $type = I('type');
        
        $type = 'order';
        
        $res = $this->impexcel($info,$type,$extension);
        
        
        $this->success($res['msg']);
        
        //echo $this->ajaxReturn($res);
        
    }//end func impordernumber
    
    //导入订单号
    public function impordernumber_ajax(){
        $upload_res = $this->xlsupload();
        
        if( $upload_res['code'] != 1 ){
//            $this->error($upload_res['msg']);
            echo $this->ajaxReturn($upload_res,'JSON');
            return;
        }
//        print_r($upload_res);return;
        
        $info = $upload_res['info'];
        $path = $upload_res['path'];
        $extension = $info[0]['extension'];
//        $type = I('type');

        $type = 'order';
        
        $res = $this->impexcel($info,$type,$extension);
        
        echo $this->ajaxReturn($res,'JSON');
    }//end func impordernumber_ajax

    //导入订积分商城单号
    public function impordernumber_integralorder_ajax(){
        $upload_res = $this->xlsupload();

        if( $upload_res['code'] != 1 ){
//            $this->error($upload_res['msg']);
            echo $this->ajaxReturn($upload_res,'JSON');
            return;
        }
//        print_r($upload_res);return;

        $info = $upload_res['info'];
        $path = $upload_res['path'];
        $extension = $info[0]['extension'];
//        $type = I('type');

        $type = 'integralorder';

        $res = $this->impexcel($info,$type,$extension);

        echo $this->ajaxReturn($res,'JSON');
    }//end func impordernumber_ajax

    //导入用户
    public function impordemanager_ajax(){
        $upload_res = $this->xlsupload();
        
        if( $upload_res['code'] != 1 ){
//            $this->error($upload_res['msg']);
            $this->ajaxReturn($upload_res,'JSON');
        }
//        print_r($upload_res);return;
        
        $info = $upload_res['info'];
        $path = $upload_res['path'];
        $extension = $info[0]['extension'];
        $type = I('type');
        
        $type = 'manager';
        
        $res = $this->impexcel($info,$type,$extension);
        
        $this->ajaxReturn($res,'JSON');
        
        //$this->success($res);
    }//end func impordernumber_ajax
    
    
    
    //upload名字改为xlsupload
    function xlsupload() {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        //$upload->maxSize = 1048576;
        $upload->allowExts = array('xls','xlsx'); // 设置附件上传类型
        $upload->savePath = './upload/excel/';
        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ym';
        $upload->subType = 'date';  //可以设置为hash或date
        if (!$upload->upload()) {
            $msg = $this->error($upload->getErrorMsg());
            
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  $msg,
            );
            
        } else {

            $info = $upload->getUploadFileInfo();
            $path = $info[0]['savepath'] . $info[0]['savename'];
            //return __ROOT__ . substr($path, 1);
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '成功导入！',
                'path'  =>  $path,
                'info'  =>  $info,
//                'extension' =>  $info['extension'],
            );
            
        }
        
        return $return_result;
    }//end func upload

    //log文件的下载
    public function downfile(){
        $files=I('filename');
        $filename='log/'.$files;
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
    }

}

?>