<?php 

class ActivityAction extends Action {
    protected $uid;
    protected $activity;
    protected $openid;


    public function _initialize() {
        $this->activity = C('ACTIVITY');
        //代理系统外活动中心(普通客户)
        if ($this->activity['WAY'] == 0) {
            //尚未开发
            
        } else {
            if (!isset($this->uid)) {
                $this->redirect(__GROUP__.'/Login/index');
            }
            $this->uid = $this->uid;
            $this->openid = $_SESSION['oid'];
        }
        
    }
}

 ?>