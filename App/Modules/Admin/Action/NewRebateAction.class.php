<?php

header("Content-Type: text/html; charset=utf-8");

class NewRebateAction extends CommonAction {
    private $new_rebate_obj;
    private $distributor_model;
    private $rebate_other_model;
    private $rebate_team_model;
    
    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.NewRebate','App');
        $this->new_rebate_obj = new NewRebate();
        $this->rebate_other_model = M('rebate_other');
        $this->distributor_model = M('distributor');
        $this->rebate_team_model=M('rebate_team');
    }
    //我的奖励
    public  function  index(){
        //返利是否开启
        $this->rebate = C('REBATE');
        $this->personal_rebate_ratio=C('PERSONAL_REBATE_RATIO');
//        $this->team_rebate_ratio=C('TEAM_REBATE_RATIO');
        $this->display();
    }
    
    //获取返利接口
    public function get_other_rebate() {
        $type = I('type');
        $month = I('month');
        $page = I('page');
        if($type == 2){
           $types=array('in',array(2,3,4));
        }else{
            $types=$type;
        }
        if (empty($month)) {
            $month =  get_month();
        }
        $where = [
            'uid' => $this->uid,
            'type'=>$types,
            'month' => $month
        ];

        $page_info=[
            'page_num' => $page,
            'page_list_num' => '',
        ];
        $list = $this->new_rebate_obj->get_other_rebate($page_info, $where);

//        var_dump($list);die;
        //总奖励
        $list['total_money'] = $this->rebate_other_model->where(['uid'=>$this->uid,'type'=>$types])->sum('money');
        //当月总返利金额
        $list['month_total_money'] = $this->rebate_other_model->where($where)->sum('money');
        $result = [
            'code' => 1,
            'msg' => '获取返利成功',
            'result' => $list,
        ];

        $this->ajaxReturn($result, 'json');
    }

    //审核其它返利
    public function audit_other_rebate(){
        $id = I('post.id');
        $pass = I('pass');
        import('Lib.Action.NewRebate','App');
        $rebate = new NewRebate();
        $return_result = $rebate->audit_other_rebate([$id], $pass);
        $this->ajaxReturn($return_result, 'JSON');
    }//end func rebate_pay_aduit
    
    //审核团队返利
    public function audit_team_rebate(){
        $id = I('post.id');
        $pass = I('pass');
        import('Lib.Action.NewRebate','App');
        $rebate = new NewRebate();
        $return_result = $rebate->audit_team_rebate([$id], $pass);
        $this->ajaxReturn($return_result, 'JSON');
    }//end func rebate_pay_aduit
    
    //获取团队返利的接口
    public function get_team_rebate(){
//      $type = I('type');
        $month = I('month');
        $page = I('page');
        if (empty($month)) {
            $month =  get_month();
        }
        $where = [
            'uid' => $this->uid,
//          'type' => $type,
            'month' => $month
        ];

       
        $page_info=[
            'page_num' => $page,
            'page_list_num' => '',
        ];
        $list = $this->new_rebate_obj->get_team_rebate($page_info, $where);
        //总奖励
        $list['total_money'] = $this->rebate_team_model->where(['uid'=>$this->uid])->sum('rebate_money');
        //当月总返利金额
        $list['month_total_money'] = $this->rebate_team_model->where($where)->sum('rebate_money');
        $list['page'] = 20;
        $result = [
            'code' => 1,
            'msg' => '获取返利成功',
            'result' => $list,
        ];
       // var_dump($list);die;

       $this->ajaxReturn($result, 'json');
    }
}

?>