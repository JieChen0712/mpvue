<?php

/**
 * 	发货经销商后台——货物管理
 */
header("Content-Type:text/html;charset=utf-8");

class GAction extends Action {

    //查询发货记录
    public function index() {
        import('ORG.Util.Pageone');
        $pp = 'product';
        $d = 'distributor';
        $field = "$pp.ptag_name,$pp.product_num,$pp.time,$d.name as receiveName,m.name as sendName";
        $where = array(
            "$pp.receive_id" => session('managerid'),
            "_logic" => 'or',
            "$pp.send_id" => session('managerid'),
        );
        $join1 = "$d on $pp.receive_id=$d.id ";
        $join2 = "$d as m on $pp.send_id=m.id";
        $count = M($pp)->where($where)->join($join1)->join($join2)->count();
        if ($count > 0) {
            $p = new Pageone($count, 10);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = M($pp)->field($field)->where($where)->join($join1)->join($join2)->order('product.time desc')->limit($limit)->select();

            $page = $p->show();
            $this->page = $page;
            $this->list = $list;
        }
        $this->display();
    }

    //扫描
    public function s() {
        if ($_GET['st'] && $_GET['p']) {
            $this->showAgentInfo($_GET['p'], $_GET['st']);
        }
        $self = M('Distributor')->field('id,pid,level')->where(array('openid' => session('oid'), 'audited' => 1))->find();
        //找到下属经销商并保存到session
        if (!isset($_SESSION['myAgent'])) {
            $myAgent = $this->getAgent($self);
            if ($myAgent) {
                session('myAgent', $myAgent);
            }
        }
        $this->signPackage = get_jsapi_ticket();
        $this->app_id = C('APP_ID');
        $this->app_secret = C('APP_SECRET');
        $this->myagent = session('myAgent');
        $this->display();
    }

    //发货
    public function stock() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }

        $ptag = I('ptag');
        $status = I('status');
        $receive_id = I('receive_id');
        $send_id = I('send_id');
        $ptagList = explode('|', $ptag);
        array_shift($ptagList);
        $statusList = explode('|', $status);
        array_shift($statusList);
        $orderNum = $this->random(6);
        $model = M('Ptag');
        $product = M('Product');
        $self = M('Distributor')->field('id,pid,level')->where(array('openid' => session('oid'), 'audited' => 1))->find();
        foreach ($ptagList as $key => $value) {
            //判断是否有权发货
            if (!$this->checkAuth($self, $value, $statusList[$key])) {
                setLog($self['id'] . ' 经销商无权发货' . $value . '标签', 'agent-stock');
                $this->ajaxReturn(array('ptag' => $value, 'state' => 'ptag'), 'JSON');
            }

            //判断是否重复发货
            if (!$this->checkRepeat($self, $value, $statusList[$key])) {
                setLog($self['id'] . ' 经销商发货的标签' . $value . '重复出库', 'agent-stock');
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
                    'is_mobile' => 1,
                );
            } else {
                $where = array(
                    'ptag_beg' => array('elt', $v),
                    'ptag_end' => array('egt', $v)
                );
                //待修改
                $ptag_name = $model->where($where)->getField('ptag_name');
                $templet_id = $product->where(array('ptag_name' => $ptag_name, 'status' => 'system'))->getField('templet_id');
                
                if(empty($templet_id)){
                    $ptag_name = $model->where($where)->getField('ptag_name');
                    $map['mbeg'] = array('elt', $v);
                    $map['mend'] = array('egt', $v);
                    $map['status'] = 'system';
                    $templet_id = $product->where($map)->getField('templet_id');
                }
                if(empty($templet_id)){
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
                    'is_mobile' => 1,
                );
            }
            $res = M('product')->add($data);
        }
        if ($res) {
            $this->ajaxReturn('success', 'JSON');
        } else {
            $all_info = I();
            setLog('发货失败--info:'.print_r($all_info,TRUE).',$ptagList:'.print_r($ptagList,TRUE).',$statusList:'.print_r($statusList,TRUE).',sql:'.M('product')->getDbError(), 'agent-stock');
            $this->ajaxReturn('error', 'JSON');
        }
    }

    //选择收货经销商
    public function saveAgent() {
        session('agentId', $_POST['id']);
        $res = M('Distributor')->field('id,name,disable')->where(array('id' => session('agentId'), 'audited' => 1))->find();
        //判断此经销商有没有禁用
        if ($res['disable'] == 1) {
            $this->ajaxReturn(1, 'JSON');
        } else {
            $this->ajaxReturn($res, 'JSON');
        }
    }

    //发货成功清除session
    public function clearAgent() {
        session('myAgent', null);
        session('agentId', null);
        session('ls', null);
        $this->ajaxReturn('success', 'JSON');
    }

    //找下属经销商
    public function getAgent($row, $search = '') {
        if (!$row) {
            return false;
        } else {
            if ($search && $search != 'all') {
                $where['name'] = array('like', "%$search%");
            }
            //找出属于自己发的链接并等级小于自己的经销商， 并且找出发给自己链接并且等级小于自己的经销商
            //change by z 前三级可以同级发货
            $where['level'] = array('gt', $row['level']);
            $where['pid'] = $row['id'];
            $where['audited'] = 1;
            $map['level'] = array('gt', $row['level']);
            $map['id'] = $row['pid'];
            $map['audited'] = 1;
            $a['_complex'] = $where;
            $condition[] = $a;
            $condition['_logic'] = 'OR';
            $condition['_complex'] = $map;
            $agent = M('Distributor')->field("id,name,levname")->where($condition)->order('level desc')->select();
            return $agent;
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

    //获取最高级经销商id
    public function getPid($id) {
        $row = M('Distributor')->field('pid')->find($id);
        if (!$row) {
            return false;
        } else {
            if ($row['pid'] != 0) {
                self::getPid($row['pid']);
            } else {
                session('m_pid', $id);
                return true;
            }
        }
        return $id;
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

    //在主页显示经销商信息
    public function showAgentInfo($ptag, $status) {
        $this->redirect("Home/Security/index?status=$status&ptag=$ptag");
    }

    //查找
    public function search() {//session('managerid','2015086074');
        $status = I('post.status');
        $p = 'product';
        $d = 'distributor';
        $field = "$p.ptag_name,$p.product_num,$p.time,$d.name as receiveName,m.name as sendName";
        if ($status == 'time') {
            $time = strtotime($_POST['time']);
            $gt = $time - 86400;
            $lt = $time + 86400;
            $m = session('managerid');
            $where = "($p.time>$gt and $p.time<$lt) and $p.send_id=$m";
            $list = M($p)->field($field)->where($where)->join("$d on $p.receive_id=$d.id")->select();
            if (!$list) {
                $this->ajaxReturn('none', 'JSON');
            } else {
                $this->ajaxReturn($list, 'JSON');
            }
        } else if ($status == 'order') {
            $orderNum = I('post.orderNum');
            $condition["$p.orderNumber"] = $orderNum;
            $condition['_logic'] = 'or';
            $condition["$d.name"] = $orderNum;
            $map[] = $condition;
            $where["$p.receive_id"] = session('managerid');
            $where['_logic'] = 'or';
            $where["$p.send_id"] = session('managerid');
            $map[] = $where;
            $tj['_complex'] = $map;
            $join1 = "$d on $p.receive_id=$d.id ";
            $join2 = "$d as m on $p.send_id=m.id";
            $list = M($p)->field($field)->where($tj)->join($join1)->join($join2)->select();
            if (!$list) {
                $this->ajaxReturn('none', 'JSON');
            } else {
                $this->ajaxReturn($list, 'JSON');
            }
        }
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

    //查找经销商
    public function searchAgent() {
        $search = I('post.search');
        $self = M('Distributor')->field('id,pid,level')->where(array('openid' => session('oid'), 'audited' => 1))->find();
        //找到下属经销商并保存到session
        $myAgent = $this->getAgent($self, $search);
        $this->ajaxReturn($myAgent, 'JSON');
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



    //----------------**********-------------------
    //查找订单号
    public function searchorder() {
        if(!IS_AJAX){
            return FALSE;
        }
        $search = I('post.search');
        import('Lib.Action.Order','App');
        $Order = new Order();

        //$search='501491443269337';
        $condition=[
            'order_num' => array('like',"$search%"),
            'o_id' =>session('managerid'),
        ];
        $other['is_group'] = 1;

        $info=$Order->get_order([]  ,$condition,$other);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];
        $this->ajaxReturn($return_result);

    }


    //显示经销商
    public function show_agent(){
        if(!IS_AJAX){
            return FALSE;
        }
        $uid=session('managerid');
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

        //$page_num=1;

        import('Lib.Action.User', 'App');
        $User = new User();
        $condition=[
            'pid' => $uid,
        ];
        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $result = $User->get_distributor($page_info, $condition);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }

    //点击经销商显示订单
    public function get_agent_order(){
        if(!IS_AJAX){
            return FALSE;
        }
        $oid=session('managerid');
        $agent_id=trim(I('post.agent_id'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

       // $page_num=1;

      //  $agent_id=4;
			

        $other['is_group']=1;
        $condition=[
            'user_id' => $agent_id,
            'o_id' =>  $oid,
        ];

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($agent_id)) {
            $return_result = [
                'code' => 3,
                'msg' => '参数获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($page_list_num)) {
            $page_list_num = 10;
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        import('Lib.Action.Order','App');
        $Order = new Order();
        $info=$Order->get_order($page_info,$condition,$other);
        $agent_count=M('order')->where($condition)->group()->count();
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
            'agent_count' => $agent_count,
        ];

        $this->ajaxReturn($return_result);
    }

}

?>