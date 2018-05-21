<?php

/**
 * 	微斯咖前台——微主页
 */
class IndexAction extends Action {

    //微主页
    public function index() {

        
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        $GW = $FUNCTION_MODULE['GW'];
        
        if( !$GW ){
            $url = __ROOT__.'/admin/index';
            $this->redirect($url);
            return;
        }
        

        //主页显示经销商信息
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
        $this->display();
    }
    
    
    public function homepage(){
        //主页显示经销商信息
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
        
        $this->display('index');
    }
    
    //轮播图
    public function get_advert(){
        if(!IS_AJAX){
         return FALSE;
        }
        $count=M('goods_advert')->where(array('status'=>1,'type'=>1))->count('id');
        if($count>0){
            $info=M('goods_advert')->where(array('status'=>1,'type'=>1))->order("sequence desc,id desc")->limit(5)->select();
        }
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $info,
        ];
        $this->ajaxReturn($return_result);

    }

    public function index_clr() {
        $this->display();
    }

    public function index_jmd() {
        $this->display();
    }
}

?>