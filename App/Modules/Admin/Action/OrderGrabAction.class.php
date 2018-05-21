<?php

/**
 *    微斯咖经销商后台
 */
class OrderGrabAction extends CommonAction
{

    private $model;
    private $templet_model;
    private $templet_cat_model;
    private $cart_model;
    private $shipping_goods_model;
    private $shipping_way_model;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = M('order');
        $this->templet_model = M('templet');
        $this->templet_cat_model = M('templet_category');
        $this->cart_model = M('order_shopping_cart');
        $this->shipping_goods_model=M('shipping_goods_shipping_template');
        $this->shipping_way_model=M('shipping_way');
    }

    //抢单中心
    public function index(){
        
        $this->display();
    }
    
    //抢单
    public function graborder(){
        $order_num = I('order_num');
        
        import('Lib.Action.Order', 'App');
        $order = new Order();
        
        $order_nums = [$order_num];
        
        $result = $order->grab_order($this->uid,$order_nums);
        
        $this->ajaxReturn($result);
    }
    
    
    
    
}

?>