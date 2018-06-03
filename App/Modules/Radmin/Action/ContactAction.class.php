<?php

/**
 *    微斯咖经销商管理系统
 */
class ContactAction extends CommonAction
{
    
    private $contact_model;

    public function _initialize()
    {
        parent::_initialize();
        
        import('Lib.Action.Store','App');
        $this->Store = new Store();
        
        $this->contact_model = $this->Store->contact_model;
    }

    //
    public function index(){
        
        $condition = [];
        
        //
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        
        $condition = $this->condition;
        
        $result = $this->Store->get_contact($page_info,$condition);
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
            
            
            $condition['id'] = $id;
            $list = $this->contact_model->where($condition)->find();
            
            if( empty($list) ){
                $this->error('无法获取到该信息！');
            }
            
            $list['img'] = explode(',', $list['img']);
        }
        else{
            $store_info = M('store')->field('id,name')->select();
        }
        
        $this->store_info = $store_info;
        $this->list = $list;
        $this->display();
    }
    
    //修改轮播图
    public function edit_post(){
        $id = I('id');
        $store_id = I('store_id');
        $name = I('name');
        $addres = I('addres');
        $phone = I('phone');
        $latitude = I('latitude');
        $longitude = I('longitude');
        
        $update_id = $id;
        
        //添加商城
        if( empty($id) ){
            if($this->is_super){
                if (empty($store_id)) {
                    $this->error('请选择商城');
                }
            } else {
                $store_id = $this->store_id;
            }
            
            $data = [
                'store_id'  =>  $store_id,
                'name'    =>  $name,
                'addres'    =>  $addres,
                'phone'     =>  $phone,
                'latitude'  =>  $latitude,
                'longitude' =>  $longitude,
                'created'=> time(),
            ];
            
            $result = $this->contact_model->add($data);
            
            $update_id = $result;
        }
        else{//修改商城
            
            
            $condition = [
                'id'    =>  $id,
            ];
            
            $data = [
                'name'    =>  $name,
                'addres'    =>  $addres,
                'phone'     =>  $phone,
                'latitude'  =>  $latitude,
                'longitude' =>  $longitude,
                'updated'   =>  time(),
            ];
            $result = $this->contact_model->where($condition)->save($data);
        }
        
        if( $result ){
            $this->add_active_log('修改联系信息，序号：'.$update_id);
            $this->success('提交成功！');
        }
        else{
            $this->error('提交失败，请重试！');
        }
    }
    
    //删除商城
    public function del_contact(){
        
//        if( !$this->is_super ){
//            $this->error('该账号无权操作此功能！');return;
//        }
        
        $id = trim(I('id'));
        
        $info = $this->contact_model->where(array('id' => $id))->find();
        
        if(empty($info)){
            $this->error('没有这条信息!');
        }
        else {
            $res = $this->contact_model->where(array('id' => $id))->delete();
            if($res) {
                $this->success('删除序号为'.$id.'的联系信息成功！');
                $this->add_active_log('删除联系信息成功');
            } else {
                $this->error('删除联系信息失败！');
            }
        }
    }
    
    
    
    
     /**
     * +-------------------------------------------------
     * 上传图片
     * +-------------------------------------------------
     * @param string $name
     * +-------------------------------------------------
     * @return string $info(中文提示)
     * +-------------------------------------------------
     */
    public function upload()
    {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型

        $upload->savePath = './upload/contact/';// 设置附件上传目录

        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        return $image;

    }
    
    
    
}

?>