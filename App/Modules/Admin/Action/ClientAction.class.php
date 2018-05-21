<?php

class ClientAction extends Action {

    public function index() {
        $this->display();
    }

    // 登录
    public function login() {
        $admin = M('admin')->where(array('username' => I('username')))->find();

        //用户名或密码错误
        if (!$admin || $admin['password'] != md5(I('password'))) {
            echo "<script>";
            echo "alert('用户名或密码错误！');history.back(-1);";
            echo "</script>";
        } else {   //登录成功
            //session记录
            session('aid', $admin['id']);
            session('aname', $admin['username']);
            if (I('remember')) {
                setcookie("username", $admin['username'], time() + 30 * 24 * 3600);
            }
            $this->redirect('scan');
        }
    }

    // 扫描页
    public function scan() {
        if (!$_SESSION['aname']) {
            $this->redirect('index');
            exit();
        } else {
            $this->signPackage = get_jsapi_ticket();
            $this->level_num = C('LEVEL_NUM');
            $this->level_name = C('LEVEL_NAME');
            $this->app_id = C('APP_ID');
            $this->app_secret = C('APP_SECRET');
            $this->templet = M('templet')->where(['active'=>'1'])->select();
            $this->display();
        }
    }

    //手机发货
    public function phoneStock() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }

        $ptag = I('ptag');
        $status = I('status');
        $receive_id = I('receive_id');
        $templet_id = I('templet_id');
        $type = I('type');
        $ptagList = explode('|', $ptag);
        array_shift($ptagList);
        $statusList = explode('|', $status);
        array_shift($statusList);
        $model = M('Ptag');
        $mtagObj = D('Mtag');
        $product = M('Product');

        $count_tag = count($ptagList);
        for ($j = 0; $j < $count_tag; $j ++) {
            $where['ptag_name'] = $ptagList[$j];
            $where['status'] = 'system';
            $count = $product->where($where)->count('id');
            $notp = $model->where(array('ptag_name'=>$where['ptag_name']))->find();
            if($count>0){
                    //标签不能重复出库
//                    $this->ajaxReturn('repeat','json');
                $this->ajaxReturn(array('err_code' => 'repeat', 'err_msg' => $ptagList[$j] . '标签已经出库,不能重复出库'), 'json');
            }
            elseif( $type == 'm' ){
                $check_mtag_name = $mtagObj->where(array('mtag_name'=>$where['ptag_name']))->find();
                
                if( empty($check_mtag_name) ){
                    $this->ajaxReturn(array('err_code' => 'wrong', 'err_msg' => $ptagList[$j] . '标签不存在'), 'json');
                }
            }
            elseif (!$notp && $type != 'm' ) {
                    //判断是否为正确标签
//                    $this->ajaxReturn('wrong','json');
                    $this->ajaxReturn(array('err_code' => 'wrong', 'err_msg' => $ptagList[$j] . '标签不存在'), 'json');
            }
            
            
            //old代码
//            if ($type == 'b') {
//                $where['ptag_name'] = $ptagList[$j];
//                $where['status'] = 'system';
//                $notp = $model->where(array('ptag_name' => $ptagList[$j]))->find();
//                //add by z 判断小标是否已出库
//                $num = $notp['ptag_beg'];
//                if (!empty($notp)) {
//                    for ($c = 0; $c < C('TAG_LEN'); $c++) {
//                        $map["mbeg"] = array('elt', $num);
//                        $map["mend"] = array('egt', $num);
//                        $map["status"] = 'system';
//                        $map["statusbm"] = 'm';
//                        $listcont = $product->where($map)->count();
//                        if ($listcont > 0) {
//                            //小标已经出库
//                            $num = sprintf("%011d", $num);
//                            setLog($num . '小标签已经出库,对应的大标不能出库', 'phoneTagStock');
//                            $this->ajaxReturn(array('err_code' => 'haveStock', 'err_msg' => $num . '小标签已经出库,对应的大标不能出库'), 'json');
//                        }
//                        $num++;
//                    }
//                }
//                //:add by z 判断小标是否已出库
//            } else if ($type == 'm') {
//                $where = array(
//                    'mbeg' => array('elt', $ptagList[$j]),
//                    'mend' => array('egt', $ptagList[$j]),
//                    'status' => 'system',
//                );
//                $notp = $mtagObj->where(array('mtag_name' => $ptagList[$j]))->find();
//            }
//            $count = $product->where($where)->count('id');
//
//            if ($count > 0) {
//                //标签不能重复出库
//                setLog($ptagList[$j] . '标签已经出库.不能重复出库', 'phoneTagStock');
//                $this->ajaxReturn(array('err_code' => 'repeat', 'err_msg' => $ptagList[$j] . '标签已经出库,不能重复出库'), 'json');
//            }
//            if (!$notp) {
//                //判断是否为正确标签
//                setLog($ptagList[$j] . '标签不存在', 'phoneTagStock');
//                $this->ajaxReturn(array('err_code' => 'wrong', 'err_msg' => $ptagList[$j] . '标签不存在'), 'json');
//            }
        }
        //标签写入数据库
        foreach ($ptagList as $k => $v) {
            if ($type == 'b') {
                //找到小标签
                $row = $model->field('ptag_beg,ptag_end,ptag_total')->where(array('ptag_name' => $v))->find();
                $data = array(
                    'ptag_name' => $v,
                    'mbeg' => $row['ptag_beg'],
                    'mend' => $row['ptag_end'],
                    'templet_id' => $templet_id,
                    'send_id' => 0,
                    'receive_id' => $receive_id,
                    'product_num' => $row['ptag_total'],
                    'orderNumber' => 0,
                    'time' => time(),
                    'status' => 'system',
                    'statusbm' => 'b',
                    'pid' => 0,
                    'is_mobile' => 1,
                );
            } else if ($type == 'm') {
                $data = array(
                    'ptag_name' => $v,
                    'mbeg' => $v,
                    'mend' => $v,
                    'templet_id' => $templet_id,
                    'send_id' => 0,
                    'receive_id' => $receive_id,
                    'product_num' => 1,
                    'orderNumber' => 0,
                    'time' => time(),
                    'status' => 'system',
                    'statusbm' => 'm',
                    'pid' => 0,
                    'is_mobile' => 1,
                );
            }
            $res = $product->add($data);
            
        }

        if ($res) {
            $this->ajaxReturn(array('err_code' => 'success'), 'json');
        } else {
            setLog('出库失败,'. print_r(I(),1), 'phoneTagStock');
            $this->ajaxReturn(array('err_code' => 'error', 'err_msg' => '出库失败'), 'json');
        }
    }

    // 获得经销商
    public function getAgent() {
        $id = I('post.id');
        if ($id != '0') {
            $agent = M('Agent')->where(array('agent_type' => $id))->field('agent_name,agent_id')->select();
            if ($agent) {
                $this->ajaxReturn($agent, 'JSON');
            } else {
                $this->ajaxReturn('notselect', 'JSON');
            }
        } else {
            $this->ajaxReturn('noselect', 'JSON');
        }
    }

    // 获得标签
    public function getTag() {

        $res = I('post.result');
        $tag = substr($res, -12);
        $row = M('ptag')->where(array('ptag_name' => $tag))->find();
        $this->ajaxReturn($row, 'JSON');
    }

    //获取大标签
    public function BigPtag() {
        $ptag_name = I('post.m_url');
        $field = 'ptag_name,ptag_beg,ptag_end,ptag_total';
        $row = M('Ptag')->field($field)->where(array('ptag_name' => $ptag_name))->find();
        $this->ajaxReturn($row, 'JSON');
    }

    //获取收货经销商
    public function agent() {
        $m_search = toValid(I('term'));
//        $where['name'] = array('LIKE', '%' . $m_search . '%');
//        $where['audited'] = 1;
        
        $where['audited'] = 1;
        $where['_complex'] = array(
            'name'  =>  array('LIKE', '%' . $m_search . '%'),
            '_logic'    =>  'or',
            'phone' =>  array('LIKE', '%' . $m_search . '%'),
        );
        
        $list = M('Distributor')->field("id,name,levname,phone")->where($where)->select();
        $this->ajaxReturn($list, 'JSON');
    }

    public function Client() {

        $this->display();
    }

}
?>