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
            if( !$this->is_super && $this->store_id != $id ){
                $this->error('该账号无权管理此商城！');
            }
            
            $condition['id'] = $id;
            $list = $this->store_model->where($condition)->find();
            
            if( empty($list) ){
                $this->error('无法获取到商城信息！');
            }
        }
        
        $this->list = $list;
        $this->display();
    }
    
    //添加商城提交
    public function edit_post(){
        $id = I('id');
        $name = trim(I('name'));
        $appid = trim(I('appid'));
        $appsecret = trim(I('appsecret'));
        $qrcode = I('qrcode');
        
        if( empty($name)  ){
            $this->error('商城名称不能为空！');
            return;
        }
        if( empty($appid) || empty($appsecret) ){
            $this->error('appid和appsecret不能为空！');
            return;
        }
        
        $update_id = $id;
        
        
        
        //添加商城
        if( empty($id) ){
            $data = [
                'name'  =>  $name,
                'appid' =>  $appid,
                'appsecret' =>  $appsecret,
                'qrcode'    =>  $qrcode,
                'created'   =>  time(),
                'updated'   =>  time(),
            ];
            
            $result = $this->store_model->add($data);
            
            $update_id = $result;
            $msg = '添加商城《'.$name.'》';
        }
        else{//修改商城
            
            if( !$this->is_super && $id == $this->store_id ){
                $this->error('该账号无权管理此商城！');
                return;
            }
            
            $condition = [
                'id'    =>  $id,
            ];
            
            $data = [
                'name'  =>  $name,
                'appid' =>  $appid,
                'appsecret' =>  $appsecret,
                'updated'   =>  time(),
            ];
            $result = $this->store_model->where($condition)->save($data);
            //$err = $this->store_model->getLastSql();//getDbError();
            $msg = '序号为'.$id.'的商城修改';
        }
        
        if( $result ){
            $this->add_active_log($msg.'成功，商城标识为'.$update_id);
            $this->success('提交成功！');
        }
        else{
            $this->error('提交失败，请重试！');
        }
    }
    
    //删除商城
    public function del_topmap(){
        
        if( !$this->is_super ){
            $this->error('该账号无权操作此功能！');return;
        }
        
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
                $this->add_active_log('删除商城'.$name.'成功，商城标识：'.$id);
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

        $upload->savePath = './upload/store/';// 设置附件上传目录

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