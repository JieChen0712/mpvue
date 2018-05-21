<?php

/**
 * 	微斯咖前台——防伪查询
 */
class SecurityAction extends Action {

    //微斯咖防伪查询系统
    public function index() {
        if (isset($_GET['status'])) {
            $status = I('get.status');
            $ptag = I('get.ptag');
            $d = 'distributor';
            $p = 'product';
            $t = 'templet';
            $field = "$d.name as dname,$d.phone,$d.wechatnum,$t.name as tname,$t.price,$d.disable";
            if ($status == 'b') {
                $map["$p.ptag_name"] = $ptag;
                $join1 = "$d on $p.receive_id = $d.id";
                $join2 = "$t on $p.templet_id = $t.id";
                $res = M($p)->field($field)->where($map)->join($join1)->join($join2)->order("$p.time desc")->find();
                if ($res) {
                    if ($res['dname'] == "" || $res['disable'] == 1) {
                        //经销商被删除则提示产品未经授权
                        $this->state = 'no';
                    } else {
                        $this->state = 'yes';
                        $this->res = $res;
                    }
                } else {
                    $this->state = 'no';
                }
            } else if ($status == 'm') {
                $join1 = "$d on $p.receive_id = $d.id";
                $join2 = "$t on $p.templet_id = $t.id";
                $condition = array(
                    "$p.ptag_name" => $ptag
                );
                $res = M($p)->field($field)->where($condition)->join($join1)->join($join2)->order("$p.time desc")->find();
                if ($res) {
                    if ($res['dname'] == "" || $res['disable'] == 1) {
                        //经销商被删除则提示产品未经授权
                        $this->state = 'no';
                    } else {
                        $this->state = 'yes';
                        $this->res = $res;
                    }
                } else {
                    $where['mbeg'] = array('elt', $ptag);
                    $where['mend'] = array('egt', $ptag);
                    $otherptag = M('product')->where($where)->order("$p.time desc")->getField('id');
                    $join1 = "$d on $p.receive_id = $d.id";
                    $join2 = "$t on $p.templet_id = $t.id";
                    $condition = array(
                        "$p.id" => $otherptag
                    );
                    $row = M($p)->field($field)->where($condition)->join($join1)->join($join2)->order("$p.time desc")->find();
                    if ($row) {
                        if ($row['dname'] == "" || $res['disable'] == 1) {
                            //经销商被删除则提示产品未经授权
                            $this->state = 'no';
                        } else {
                            $this->state = 'yes';
                            $this->res = $row;
                        }
                    } else {
                        $this->state = 'no';
                    }
                }
            }
        }

        $condition = [
            'status' => 1,
            'type' => 2,
        ];
        $info = M('info')->where($condition)->find();
        $this->info = $info;
        $this->display();
    }

    //防伪
    public function fwcode() {
        $fwcode = trim(I('post.fwcode'));
        $count = M('code')->where(array('code' => $fwcode))->count('id');
        if ($count > 0) {
            $data = array(
                'code' => $fwcode,
                'time' => time()
            );
            $record = M('Record');
            $flag = $record->add($data);
            if ($flag) {
                $num = $record->where(array('code' => $fwcode))->count('id');
                $first = $record->field('time')->where(array('code' => $fwcode))->order('time asc')->find();
                $list = $record->where(array('code' => $fwcode))->order('time desc')->limit(3)->select();
                foreach ($list as $k => $v) {
                    $list[$k]['time'] = date('Y-m-d H:i:s', $v['time']);
                }
                $list['count'] = $num;
                $list['first'] = date('Y-m-d H:i:s', $first['time']);
                $this->ajaxReturn($list, 'JSON');
            } else {
                $this->ajaxReturn('error', 'JSON');
            }
        } else {
            $this->ajaxReturn('none', 'JSON');
        }
    }

}

?>