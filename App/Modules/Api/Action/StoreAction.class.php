<?php

/**
 * 	微斯咖
 */
class StoreAction extends CommonAction {

    private $store_model;//商城
    private $templet_model;//产品
    private $templet_category_model;//产品分类
    private $topmap_model;//首页顶部轮播图

    private $templet_obj;
    private $user_obj;

    /**
     * 架构函数
     */
    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.Templet', 'App');
        $this->templet_obj = new Templet();
        
        import('Lib.Action.User', 'App');
        $this->user_obj = new User();
        
        $this->store_model = M('store');
        $this->templet_model = M('templet');
        $this->templet_category_model = M('templet_category');
        $this->topmap_model = M('topmap');
    }
    
    
    
    //获取商城信息
    public function get_store(){
        if( !$this->isAjax() ){
            return;
        }
        
        $store_id = trim(I('post.store_id'));
        
        //setLog('传值：'.print_r(I(),1),'get_store_error');
        
        if( empty($store_id) || !isset($store_id) ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '商城标识有误！',
            ];

            $this->ajaxReturn($result);
        }
        
        $condition = [
            'id'  =>  $store_id,
        ];
        
        $info = $this->store_model->field('name,appid,qrcode')->where($condition)->find();
        
        if( empty($info) ){
            $result = [
                'code'  =>  3,
                'msg'   =>  '查无此商城！',
            ];

            $this->ajaxReturn($result);
        }
        
        $info['qrcode'] = 'https://'.C('YM_DOMAIN').$info['qrcode'];
        
        $result = [
            'code'  =>  1,
            'msg'   =>  '获取成功！',
            'info'  =>  $info,
        ];
        
        $this->ajaxReturn($result);
    }
    
    
    
    
    //获取产品信息
    public function get_templet(){
        
        $store_id = trim(I('post.store_id'));
        $active = trim(I('post.active'));
        $type = trim(I('post.type'));
        $page_num = trim(I('post.page'));
        $page_list_num = trim(I('post.page_list_num'));
        
        //setLog('传值：'.print_r(I(),1),'get_templet_error');
        
        
        if( $active!= NULL && !in_array($active,[0,1]) ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '提交的上下架状态参数有误！',
            ];
            $this->ajaxReturn($result);
        }
        
        if( !in_array($type,[0,1,2]) ){
            $result = [
                'code'  =>  3,
                'msg'   =>  '提交的产品状态状态参数有误！',
            ];
            $this->ajaxReturn($result);
        }
        
        $condition = [
            'store_id'  =>  $store_id,
        ];
        
        if( $active == NULL ){
            $active = 1;
        }
        
        if( $type != NULL ){
            $condition['type']  =   $type;
        }
        
        $page_info['page_num'] = $page_num;
        if( $page_list_num != null && is_numeric($page_list_num) ){
            $page_info['page_list_num'] = $page_list_num;
        }
        
        
        $condition['active'] = $active;
        
        $info = $this->templet_obj->get_templet($page_info,$condition);
        
        
        if( empty($info) ){
            $result = [
                'code'  =>  3,
                'msg'   =>  '查无商品！',
            ];

            $this->ajaxReturn($result);
        }
        
        $result = [
            'code'  =>  1,
            'msg'   =>  '获取成功！',
            'info'  =>  [
                'list'  => $info['list'],
                'count' =>  $info['count'],
            ],
        ];
        
        $this->ajaxReturn($result);
    }
    
    
    
    
    //获取产品详情
    public function get_templet_detail(){
        //请求接口参数
        $request_data = [
            'store_id' => $this->store_id,//店铺id
            'id' => trim(I('id')),//产品id
        ];
        
        $condition = [
            'id' => $request_data['id'],
            'store_id' => $request_data['store_id'],
            'active' => 1,
        ];
        $result = $this->templet_obj->get_templet([], $condition);
        $this->ajaxReturn($result, 'json');
    }
    
    
    //获取顶部轮播图
    public function get_topmap(){
        
        $store_id = trim(I('post.store_id'));
        
        if( empty($store_id) ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '商城ID有误！',
            ];

            $this->ajaxReturn($result);
        }
        
        
        $condition = [
            'store_id'  =>  $store_id,
        ];
        
        $topmap_img = $this->topmap_model->where($condition)->getField('img');
        
        
        $result = [
            'code'  =>  1,
            'msg'   =>  '获取成功！',
            'info'  =>  $topmap_img,
        ];
        
        $this->ajaxReturn($result);
        
    }
    
    
    
    
}

?>