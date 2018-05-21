<?php

class AgentseeAction extends CommonAction {

    //按分类查看经销商
    public function index() {
        $distributor_obj = M('distributor');


        $agentRow = $distributor_obj->field('id,name,levname,level')->where(array('id' => session('managerid')))->find();


        $level_num = C('LEVEL_NUM');
        
        $total = array();
        $where = array(
            'pid' => session('managerid'),
            'audited' => 1,
        );
        
        for ($i = 1; $i <= $level_num; $i++) {
            $where['level'] =   $i;
            $total[$i] = $distributor_obj->where($where)->count();
        }

        $this->total = $total;
        $this->agentrow = $agentRow;
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //查看经销商
    public function agentSee() {
        $level = I('get.level');
        $where = array(
            'pid' => session('managerid'),
            'level' => $level,
            'audited' => 1
        );
        $list = D('distributor')->field('id,name,levname,authnum')->where($where)->select();
        $this->list = $list;
        $this->display();
    }

}

?>