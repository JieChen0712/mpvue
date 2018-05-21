<?php

/**
 * 	微斯咖经销商后台——经销商名片
 */
class SignatureAction extends Action {

    //下级经销商生成--ilylimss
    public function create() {
        $agentRow = M('distributor')->where(array('id' => session('managerid')))->find();
        
        $level_names = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');
        $GROW_MODEL = C('GROW_MODEL');
        


        //判断注册大于两天的不进行新手帮助
        $minus = $agentRow['time'] + 3600*24*2;
        $show_guide = 'true';
        if( $minus < time() ){
          $show_guide = "false";
        }
        
        
        //最低级无权分享链接
        if ( $agentRow['level'] >= C('LEVEL_NUM') && $GROW_MODEL == 1 ) {
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('" . $level_names[$level_num] . "无权分享链接!');history.go(-1);</script>";
            exit();
        }
        $agentRow['encodeid'] = encode($agentRow['id']);
        
        if( $agentRow['statu'] == 1 ){
            echo "<script>alert('您已被暂停分享发展链接，请联系总部开启!');history.go(-1);</script>";
            return;
        }
        
        $ctime = time();
        $this->ct = encode($ctime);

        $data = array(
            'time' => $this->t,
            'creattime' => $ctime
        );
//        $res = M('Sharelink')->add($data);
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->agentRow = $agentRow;
        $this->manager = $agentRow;
        $this->show_guide = $show_guide;
        $this->display();
    }


    //经销商分享名片
    public function sharesign() {
        $id = $_SESSION['managerid'];
        $encodeid = I('get.id');
        $did = decode($encodeid);
        
        $level = I('get.level');
        $t = I('get.t');
        $ct = I('get.ct');
        

        if ($did && !$id) {
            $row = M('distributor')->field('level')->where(array('id' => $did))->find();
            if (!$row) {
                $url = "http://" . C('YM_DOMAIN');
                header("Location: $url");
                exit();
            }
            
//            //级别为最低的经销商分享出去不能申请
//            if ($row['level'] >= C('LEVEL_NUM') || $row['level'] <= 0) {
//                $url = "http://" . C('YM_DOMAIN') . "/index.php/Home/Manager/detail/id/$did";
//                header("Location: $url");
//            } else {
                $url = "http://" . C('YM_DOMAIN') . "/index.php/admin/Signature/getsign?pid=$did&level=$level&ct=$ct";
                ob_clean();
                header("Location: $url");
//            }
        } else {
            $manager = M('distributor')->where(array('id' => $id))->find();
            
            if( !empty($manager) ){
                $manager['idennum'] = substr($manager['idennum'],0,6)."******".substr($manager['idennum'],-4,4);
                $manager['authnum']  = substr($manager['authnum'],0,3).'*****';
                $manager['phone'] = substr($manager['phone'],0,7)."****";
                $manager['wechatnum'] = substr($manager['wechatnum'],0,2)."****";
            }

            if(empty($manager['start_time'])){
                    $manager['start_time'] = $manager['time'];
            }
            if(empty($manager['end_time'])){
                    $manager['end_time'] = $manager['time']+3600*24*365;
            }
            
            $this->manager = $manager;
            $this->display();
        }
        
        
    }

    //用户扫码获取经销商名片
    public function getsign() {
        $ct = decode(I('get.ct'));
        $tcountdown = ($ct + 10000 - time());
        if ($tcountdown < 0) {
            $this->redirect("guoqi");
        }
        $pid = I('get.pid');
        $level = I('get.level');
        /* 时间 */
        $this->ct = I('get.ct');
        session('pid', $pid);
        session('level', $level);
        $manager = M('distributor')->where(array('id' => $pid))->find();
        
        if( !empty($manager) ){
            $manager['idennum'] = substr($manager['idennum'],0,6)."******".substr($manager['idennum'],-4,4);
            $manager['authnum']  = substr($manager['authnum'],0,3).'*****';
            $manager['phone'] = substr($manager['phone'],0,7)."****";
            $manager['wechatnum'] = substr($manager['wechatnum'],0,2)."****";
        }

        if(empty($manager['start_time'])){
                $manager['start_time'] = $manager['time'];
        }
        if(empty($manager['end_time'])){
                $manager['end_time'] = $manager['time']+3600*24*365;
        }

        $level = C('LEVEL_NUM');
        $this->pid=$pid;
        $this->assign('manager', $manager);
        $this->assign('level_num', $level);
        $this->display();
    }

    /* 过期页面 */

    public function guoqi() {
        $this->display();
    }

}

?>
