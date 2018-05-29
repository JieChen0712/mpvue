<?php

/**
 *    微斯咖经销商管理系统
 */
class TopmapAction extends CommonAction
{
    
    private $topmap_model;

    public function _initialize()
    {
        parent::_initialize();
        
        import('Lib.Action.Store','App');
        $this->Store = new Store();
        
        $this->topmap_model = $this->Store->topmap_model;
    }

    //
    public function index(){
        
        $condition = [];
        
        //
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        
        if( !$this->is_super ){
            $condition['store_id'] = $this->store_id;
        }
        
        $result = $this->Store->get_topmap($page_info,$condition);
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
            $list = $this->topmap_model->where($condition)->find();
            
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
        $img = I('img');
        $store_id = I('store_id');
        
        
        $update_id = $id;
        
        if( !empty($img) ){
            if( count($img) > 5 ){
                $this->error('轮播图最多不超过5个！');
            }
            
            $img = implode(',', $img);
        }
        
        
        //添加商城
        if( empty($id) ){
            if( empty($store_id) ){
                $this->error('商城必须要选择！');
            }
            
            $data = [
                'store_id'  =>  $store_id,
                'img'    =>  $img,
                'created'=> time(),
            ];
            
            $result = $this->topmap_model->add($data);
            
            $update_id = $result;
        }
        else{//修改商城
            
            
            $condition = [
                'id'    =>  $id,
            ];
            
            $data = [
                'img'  =>  $img,
//                'updated'   =>  time(),
            ];
            $result = $this->topmap_model->where($condition)->save($data);
        }
        
        if( $result ){
            $this->add_active_log('修改轮播图');
            $this->success('提交成功！');
        }
        else{
            $this->error('提交失败，请重试！');
        }
    }
    
    //删除商城
    public function del_topmap(){
        
//        if( !$this->is_super ){
//            $this->error('该账号无权操作此功能！');return;
//        }
        
        $id = trim(I('id'));
        
        $info = $this->topmap_model->where(array('id' => $id))->find();
        
        if(empty($info)){
            $this->error('没有这条信息!');
        }
        else {
            $res = $this->topmap_model->where(array('id' => $id))->delete();
            if($res) {
                $this->success('删除轮播图成功！');
                $this->add_active_log('删除轮播图成功');
            } else {
                $this->error('删除商城失败！');
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

        $upload->savePath = './upload/topmap/';// 设置附件上传目录

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