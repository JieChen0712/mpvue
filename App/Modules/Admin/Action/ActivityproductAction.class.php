<?php 

class ActivityproductAction extends ActivityAction {
    
    private $model;
    private $type = 0;
    public function __construct() {
        parent::__construct();
        $this->model = M('activity_product');
    }

    //活动礼品
	public function gift() {
		$this->display();
	}

    //经销商活动中心页面
    public function index() {
        $search = trim(I('get.search'));
        
        if( !empty($search) ){
            $condition = [
                'name'  => ['like',"%$search%"],
            ];
        }
        $condition['active'] = '1';
        $condition['type'] = $this->type;
        $product = $this->model->where($condition)->select();
        
        $this->templet_info = $product;
        $this->display();
    }
    
    //产品详情
    public function detail() {
        $id = I('id');
        $condition['id'] = $id;
        $condition['active'] = '1';
        $condition['type'] = $this->type;
        $list = $this->model->where($condition)->find();
        //商品属性
//        import('Lib.Action.Sku','App');
//        $sku = new Sku();
//        $has_property = true;
//        $properties = $sku->get_templet_properties($id);
//        if (!$properties) {
//            $has_property = 0;
//        }
//        $this->properties = $properties;
//        $this->has_property = $has_property;
//        $this->skus = $sku->get_templet_skus($id);
        $this->list = $list;
        $this->display();
    }
    
    public function buy() {
        
        if ($this->activity['WAY'] == 0) {
            //尚未开发，收货地址待重写
            
        } else {
            $receiving_obj = M('receiving');
        }
        
        $temp_id = I('temp_id');
        $num = I('num');
        $addre_id = I('addre_id');
        
        //商品属性/库存代码
//        $sku_id = I('sku_id');
//        if ($sku_id) {
//            import('Lib.Action.Sku','App');
//            $sku = new Sku();
//            $sku_info = $sku->get_templet_sku($sku_id);
//            if (!$sku->check_templet_quantity($sku_info, $sku_id, $temp_id, $num)) {
//                echo "<script>alert('库存不足，请重新下单!');</script>";
//                exit();
//            }
//        }
        //:商品属性/库存代码
        
        if( empty($temp_id)){
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('没有下单信息!');history.go(-1); </script>";
            exit();
        }
        
        //经销商信息
        //-------地址信息
        if( !empty($addre_id) ){
            $condition = array(
                'id'    =>  $addre_id,
                'user_id'   => $this->uid,
            );
        } else{
            $condition = array(
                'user_id'   => $this->uid,
                'default'   =>  '1',
            );
        }
        
        $receiving_info = $receiving_obj->where($condition)->order('id desc')->find();
        
        if( empty($receiving_info) ){
            $condition = array(
                'user_id'   => $this->uid,
            );
            $receiving_info = $receiving_obj->where($condition)->order('id desc')->find();
        }
        //------------要下单的产品信息----------
        
        $templet_infos = $this->model->where(['id' => $temp_id])->select();
        $templet_infos[0]['buy_num'] = $num;
        $return_url = '&return_url='.$this->base_url(__SELF__);
        
        $this->return_url = $return_url;
        $this->templet_info = $templet_infos;
        $this->receiving_info   =   $receiving_info;
        $this->open_wxpay = $this->activity['OPEN_WXPAY'];
        $this->display();
    }
    
    private function base_url($array){
        
        $base_str = base64_encode($array);
        
        return $base_str;
    }//end func get_base_url

        //经销商活动中心页面
    public function tip() {
			header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('对不起,该功能尚未开放!');history.go(-1);</script>";
            exit();
            $this->display();
    }
}

 ?>