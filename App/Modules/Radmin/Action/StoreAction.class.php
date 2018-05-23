<?php

/**
 *    微斯咖经销商管理系统
 */
class StoreAction extends CommonAction
{
    
    private $admin_model;
    private $store_model;

    public function _initialize()
    {
        parent::_initialize();
        
        import('Lib.Action.Store','App');
        $this->Store = new Store();
        
        $this->store_model = $this->Store->store_model;
        $this->admin_model = M('admin');
    }

    //
    public function index(){
        
        $condition = [];
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        
        $result = $this->Store->get_store($page_info,$condition);
//        print_r($result);return;
        
        $this->list = $result['list'];
        $this->count = $result['count'];
        
        $this->p = I('p');
        $this->limit=$result['limit'];
        $this->display();
    }
    
    
    //添加商城
    public function edit(){
        $id = I('id');
        
        $list = [];
        if( !empty($id) ){
            $list = $this->store_model->where(['id'=>$id])->find();
            
            if( empty($list) ){
                $this->error('无法获取到商城信息！');
            }
        }
        
        $this->list;
        $this->display();
    }
    
    //添加商城提交
    public function edit_post(){
        $id = I('id');
        $name = trim(I('name'));
        $appid = trim(I('appid'));
        
        if( empty($name) || empty($appid) ){
            $this->error('商城名称和appid不能为空！');
            return;
        }
        
        //添加商城
        if( empty($id) ){
            $data = [
                'name'  =>  $name,
                'appid' =>  $appid,
                'created'   =>  time(),
                'updated'   =>  time(),
            ];
            
            $result = $this->store_model->add($data);
            
            $msg = '添加商城《'.$name.'》';
        }
        else{//修改商城
            $data = [
                'name'  =>  $name,
                'appid' =>  $appid,
                'updated'   =>  time(),
            ];
            $result = $this->store_model->save($data);
            $msg = '序号为'.$id.'的商城修改';
        }
        
        if( $result ){
            $this->add_active_log($msg.'成功，序号为'.$result);
            $this->success('提交成功！');
        }
        else{
            $this->error('提交失败，请重试！');
        }
    }
    
    //删除商城
    public function del_store(){
        $id = trim(I('id'));
        
        $info = $this->store_model->where(array('id' => $id))->find();
        
        $name = $info['name'];
        
        if(empty($info)){
            $this->error('没有该商城!');
        }
        else {
            $res = $this->store_model->where(array('id' => $id))->delete();
            if($res) {
                $this->success('删除商城成功！');
                $this->add_active_log('删除商城'.$name.'成功，序号：'.$id);
            } else {
                $this->error('删除商城失败！');
            }
        }
    }
    
    //分配业务员
    public function dealing_admin(){
        
        $admin_info = $this->admin_model->order('id desc')->select();
        
//        print_r($admin_info);return;
        
        $this->id = trim(I('id'));
        $this->admin_info = $admin_info;
        $this->display();
    }
    
    //分配业务员操作
    public function dealing_admin_post(){
        $aid = I('aid');
        $store_id = I('id');
        
        
        
        
    }
    
    
    
}

?>