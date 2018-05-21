<?php

/**
 * 	零售出库
 */
header("Content-Type:text/html;charset=utf-8");

class RetailsendAction extends CommonAction {

    
    public function index() {

        if($_GET['st'] && $_GET['p']){
            $this->showAgentInfo($_GET['p'],$_GET['st']);
        }
        $self = M('Distributor')->field('id,pid,level')->where(array('openid' => session('oid'),'audited' => 1))->find();
        
        //找到下属代理并保存到session
        $myAgent = $this->getAgent($self);
        $this->signPackage = get_jsapi_ticket();
        $this->app_id = C('APP_ID');
        $this->app_secret = C('APP_SECRET');
        $this->myagent = $myAgent;
        $this->display();
    }
    
     //连扫
    public function setLs() {
        $ls = I('post.ls');
        if ($ls == 1) {
            session('ls', 'yes');
        } else {
            session('ls', 'no');
        }
        $this->ajaxReturn($_SESSION['ls'], 'JSON');
    } 

    //查找小标
    public function searchxb() {
        $ptagObj = M('ptag');
        $ptag = $_POST['ptag'];
        $res = $ptagObj->where(array('ptag_name' => $ptag))->find();
        if (!$res) {
            $this->ajaxReturn('none', 'JSON');
        } else {
            $this->ajaxReturn($res, 'JSON');
        }
    }
    
    
    //找下属代理
    public function getAgent($row,$search=''){
        if(!$row){
            return false;
        }else{
            if($search && $search !='all'){
                    $where['name'] = array('like',"%$search%");
            }
            //找出属于自己发的链接并等级小于自己的代理， 并且找出发给自己链接并且等级小于自己的代理
            $where['level'] = array('gt',$row['level']);
            $where['pid'] = $row['id'];
            $where['audited'] = 1;
            $map['level'] = array('gt',$row['level']);
            $map['id'] = $row['pid'];
            $map['audited'] = 1;
            $a['_complex'] = $where;
            $condition[] = $a;
            $condition['_logic'] = 'OR';
            $condition['_complex'] = $map;
            $agent = M('Distributor')->field("id,name,levname,number")->where($condition)->order('level desc')->select();
            return $agent;
        }
    }
    
    //零售发货
    public function sellout() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }

        $ptag = I('ptag');
        $status = I('status');
        $receive_id = I('receive_id');
        $send_id = I('send_id');
        $sellname = trim(I('sellname'));
        $sellphone = trim(I('sellphone'));
        $remark = trim(I('remark'));
        
        $ptagList = explode('|', $ptag);
        array_shift($ptagList);
        $statusList = explode('|', $status);
        array_shift($statusList);
        $orderNum = $this->random(6);
        $model = M('Ptag');
        $product = M('Product');
        $self = M('Distributor')->field('id,pid,level')->where(array('openid' => session('oid'), 'audited' => 1))->find();
        foreach ($ptagList as $key => $value) {
            if( empty($value) || !is_numeric($value) ){
                $this->ajaxReturn(array('ptag' => $value, 'state' => 'error','msg'=>'获取不到标签值或者标签值有误！'), 'JSON');
            }
            
            //判断是否有权发货
            if (!$this->checkAuth($self, $value, $statusList[$key])) {
                $this->ajaxReturn(array('ptag' => $value, 'state' => 'ptag','self'=>$self), 'JSON');
            }

            //判断是否重复发货
            if (!$this->checkRepeat($self, $value, $statusList[$key])) {
                $this->ajaxReturn(array('repeat' => $value, 'state' => 'repeat'), 'JSON');
            }
        }
        
        
        
        //标签写入数据库
        foreach ($ptagList as $k => $v) {
            if ($statusList[$k] == 'b') {
                //待修改
                $row = $model->field('ptag_beg,ptag_end,ptag_total')->where(array('ptag_name' => $v))->find();
                $templet_id = $product->where(array('ptag_name' => $v, 'status' => 'system'))->getField('templet_id');

                $data = array(
                    'ptag_name' => $v,
                    'mbeg' => $row['ptag_beg'],
                    'mend' => $row['ptag_end'],
                    'templet_id' => $templet_id,
                    'send_id' => $send_id,
                    'receive_id' => $receive_id,
                    'product_num' => $row['ptag_total'],
                    'orderNumber' => $orderNum,
                    'time' => time(),
                    'status' => 'big',
                    'statusbm' => 'b',
                    'sellname'  =>  $sellname,//零售出货客户名
                    'sellphone' =>  $sellphone,//零售出货客户手机
                    'remark' => $remark,//零售出货客户名
                    'is_sell'   =>  '1',//是否零售出货
                );
            } else {
                $where = array(
                    'ptag_beg' => array('elt', $v),
                    'ptag_end' => array('egt', $v)
                );
                //待修改
                $ptag_name = $model->where($where)->getField('ptag_name');
                $templet_id = $product->where(array('ptag_name' => $ptag_name, 'status' => 'system'))->getField('templet_id');
                if (empty($templet_id)) {
                    $ptag_name = $model->where($where)->getField('ptag_name');
                    $map['mbeg'] = array('elt', $v);
                    $map['mend'] = array('egt', $v);
                    $map['status'] = 'system';
                    $templet_id = $product->where($map)->getField('templet_id');
                }
                if (empty($templet_id)) {
                    $templet_id = $product->where(array('ptag_name' => $v))->getField('templet_id');
                }
                $data = array(
                    'ptag_name' => $v,
                    'mbeg' => $v,
                    'mend' => $v,
                    'templet_id' => $templet_id,
                    'send_id' => $send_id,
                    'receive_id' => $receive_id,
                    'product_num' => 1,
                    'orderNumber' => $orderNum,
                    'time' => time(),
                    'status' => 'm',
                    'statusbm' => 'm',
                    'sellname'  =>  $sellname,//零售出货客户名
                    'sellphone' =>  $sellphone,//零售出货客户手机
                    'remark' => $remark,//零售出货客户名
                    'is_sell'   =>  '1',//是否零售出货
                );
            }
            
            
            $res = $product->add($data);
        }
        
        if ($res) {
//            $data['name'] = I('sellname');
//            $data['phone'] = I('sellphone');
//            M('recordlist')->add($data);
            $this->ajaxReturn('success', 'JSON');
        } else {
            $this->ajaxReturn('error', 'JSON');
        }
    }
    
    
    //根据时间来生成单号
    public function random($len) {
        $chars = "0123456789";
        $str = "";
        for ($i = 0; $i < $len; $i++) {
            $str.= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        $time = time();
        $time = substr($time, 6);
        return $str . $time;
    }
    
    
    //判断是否有权发货
    public function checkAuth($agent, $p, $status) {
        if ($status == 'b') {
            $count = M('Product')->where(array('receive_id' => $agent['id'], 'ptag_name' => $p))->count('id');
        } else if ($status == 'm') {
            //如果大标签发货了，小标签也可以发货
            $count = M('Product')->where(array('receive_id' => $agent['id'], 'ptag_name' => $p))->count('id');
            if (!$count) {
                $where["ptag_beg"] = array('elt', $p); //ok
                $where["ptag_end"] = array('egt', $p); //ok
                $ptag = M('Ptag')->where($where)->getField('ptag_name');
                $count = M('Product')->where(array('receive_id' => $agent['id'], 'ptag_name' => $ptag))->count('id');
            }
            //add by z
            //如果还没找到就在mbeg和mend字段里找(pc后台小标出库标签写进了这两个字段)
            if (!$count) {
                $map = array(
                    'mbeg' => array('elt', $p),
                    'mend' => array('egt', $p),
                    'receive_id' => $agent['id'],
                    'ptag_name' => 'mtag'
                );
                $count = M('Product')->where($map)->count('id');
            }
            //:add by z
        }
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    
    //判断是否重复发货
    public function checkRepeat($agent, $p, $status) {
        $count = M('Product')->where(array('send_id' => $agent['id'], 'ptag_name' => $p))->count('id');
        if ($count) {
            return false;
        }
        if ($status == 'b') {
            //找到小标
            $mtag = M('ptag')->where(array('ptag_name' => $p))->find();
            //同一个经销商如果大标发货给别人，小标就不能发货了
            $flag = $this->checkStock($agent['id'], $mtag['ptag_beg'], $mtag['ptag_end'], 'big');
            if (!$flag) {
                return false;
            }
        } else if ($status == 'm') {

            //找到大标
            $where["ptag_beg"] = array('elt', $p);
            $where["ptag_end"] = array('egt', $p);
            $ptag = M('Ptag')->where($where)->getField('ptag_name');

            //同一个经销商如果大标发货给别人，小标就不能发货了
            $flag = $this->checkStock($agent['id'], $ptag, '', 'm');
            if (!$flag) {
                return false;
            }
        }
        return true;
    }
    
    //判断大标发货，小标就不能继续出,反之亦然
    public function checkStock($agent_id, $beg, $end, $flag) {
        $product = M('Product');
        if ($flag == 'm') {
            $sql = "select id from product where send_id=$agent_id and ptag_name=$beg and status='big'";
        } else if ($flag == 'big') {
            $sql = "select id from product where send_id=$agent_id and (ptag_name>='$beg' and ptag_name<='$end') and status=m";
        }
        $row = M('Product')->query($sql);
        if ($row) {
            return false;
        } else {
            return true;
        }
    }

}

?>