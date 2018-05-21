<?php

/**
 *    微斯咖经销商后台——首页
 */
class OrderAction extends CommonAction
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

    //下单 new
    public function index()
    {
//        $price = "price" . $this->manager['level'];
//
//        $where['active'] = '1';
//        $id = I('id');
//        if ($id) {
//            $where['category_id'] = $id;
//        }
//        $products = $this->templet_model->where($where)->select();
//        foreach ($products as $key => $product) {
//            $products[$key]['price'] = $product[$price];
//        }
//        if (IS_AJAX) {
//            $this->ajaxReturn($products, 'json');
//        } else {
////            $this->products = $products;
//            $this->cats = $this->templet_cat_model->select();
//            $this->display();
//        }
        $is_stock = C('FUNCTION_MODULE')['STOCK_ORDER'];
        $this->is_stock = $is_stock;
        $this->display();
    }

    //商品详情 new
    public function goods_detail()
    {
        $shipping_reduce_way=C('SHIPPING_REDUCE_WAY');
        if(empty($shipping_reduce_way)){
            $shipping_reduce_way=0;
        }
        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where(['active' => '1'])->find(I('id'));
        $product['price'] = $product[$price];
        if ($product['many_image']) {
            $product['many_image']=explode(',',$product['many_image']);
        }

        if(C('ORDER_SHIPPING')&&!empty($product['template_id'])){
            $templet_info=M('shipping_goods_shipping_template')->where(['id'=>$product['template_id']])->find();
            $reduce_info=M('shipping_reduce')->where(['id'=>$templet_info['reduce_id'],'shipping_reduce_way'=>$shipping_reduce_way])->find();
        }

        $this->reduce_info=$reduce_info;
        $this->product = $product;
        $this->display();
    }

    //提交订单详情 new
    public function buy_detail()
    {
        //属性
        $sku_id = I('sku_id');
        if ($sku_id) {
            import('Lib.Action.Sku','App');
            $sku = new Sku();
            //获取库存信息
            $sku_info = $sku->get_templet_sku($sku_id);
            //获取规格值
            $property = $sku->get_templet_property_com($sku_id);
            $value = $sku->get_value($property);
            $this->properties = $value;
        }

        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where(['active' => '1'])->find(I('id'));
        if ($sku_info) {
            $product['price'] = $sku_info['price'. $this->manager['level']];
        } else {
            $product['price'] = $product[$price];
        }
        $this->sku_id = $sku_id;

        $this->product = $product;
        $num=I('num');
        $this->num = I('num');
        $total_money_order = bcmul($product['price'], I('num'), 2);
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->return_url = $this->base_url(__SELF__);
        $toatl_money = $total_money_order;
        if(C('ORDER_SHIPPING')){
            //运费相关
            $template_id=$product['template_id'];
            $shipping_way=I('shipping_way');
            $shipping_id=I('shipping_way_id');
            $condition=[
                'template_id'=> $template_id,
//            'shipping_way'=>$shipping_way,
                'id' =>$shipping_id,
            ];

//        $condition=[
//            'template_id'=> $template_id,
//        ];
            //判断是否存在满减免运费

            $flag=true;
            $template=M('shipping_goods_shipping_template')->find($template_id);
            $shipping_reduce_way=C('SHIPPING_REDUCE_WAY');
            if($shipping_reduce_way){
                $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>$shipping_reduce_way])->find();
            }else{
                $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>0])->find();
            }

            if($reduce_info['type'] == 1){
                $flag=$num<$reduce_info['need_num'];
            }elseif ($reduce_info['type'] == 2){
                $flag=$total_money_order<$reduce_info['need_money'];
            }elseif ($reduce_info['type'] == 3){
                $flag= ($num<$reduce_info['need_num'])&&($total_money_order<$reduce_info['need_money']);
            }


            if(empty($template['reduce_id']) || $flag){
                $template_info=M('shipping_way')->where($condition)->find();
                $template_info_first_num=$template_info['first_num'];
                $template_info_first_fee=$template_info['first_fee'];
                $template_info_continue_num=$template_info['continue_num'];
                $template_info_continue_fee=$template_info['continue_fee'];
                $product_parameter=$product['product_parameter'];
                $total_num=$product_parameter*$num;

                //运费计算
                if($total_num<=$template_info_first_num){
                    $total_money_fee=bcadd($template_info_first_fee,0,2);
                    $toatl_money=bcadd($total_money_order,$template_info_first_fee,2);
                }

                if($total_num>$template_info_first_num){
                    $num_two=$total_num-$template_info_first_num;
                    $continue_money=bcmul(ceil($num_two/$template_info_continue_num),$template_info_continue_fee,2);
                    $total_money_fee=bcadd($template_info_first_fee,$continue_money,2);
                    $toatl_money=bcadd($total_money_order,$total_money_fee,2);

                }
            }else{
                $total_money_fee=0;
                $toatl_money=$toatl_money=bcadd($total_money_order,$total_money_fee,2);
            }
            //参数
//            import('Lib.Action.Common', 'App');
//            $Common=new Common();
//            $canshu=$Common->get_related_data($template_id,);

        }
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        //找出自己的等级和推荐人的等级
        $distributor=M('distributor');
        $myself_info=$distributor->where(['id'=>$this->uid])->find();
        $rec_info=$distributor->where(['id'=>$myself_info['recommendID']])->find();

        $is_top_supply = $Stock->is_top_supply;
        //检查下单方式以及是否是首次下单
        $condition=[
            'user_id'=>$this->uid,
            'status'=>['gt',1],
        ];
        $is_first_buy_order=M('stock_order')->where($condition)->count('id');

        $this->myself_info_level=$myself_info['level'];
        $this->rec_info_level=$rec_info['level'];
        $this->is_first_buy_order=$is_first_buy_order;
        $this->is_top_supply=$is_top_supply;

        $this->reduce_info=$reduce_info;
        $this->total_money_fee=$total_money_fee;
        $this->total_money=$toatl_money;
        
        if( $Stock->open_stock ){
            $this->display('buy_detail_stock');
        }
        else{
            $this->display();
        }
    }
    
    
        //提交订单详情 new
        public function buy_detail_auto()
    {
        $auto_order = I('order');
        //属性
        $sku_id = I('sku_id');
        if ($sku_id) {
            import('Lib.Action.Sku','App');
            $sku = new Sku();
            //获取库存信息
            $sku_info = $sku->get_templet_sku($sku_id);
            //获取规格值
            $property = $sku->get_templet_property_com($sku_id);
            $value = $sku->get_value($property);
            
            $properties = $value;
        }

        $price = "price" . $this->manager['level'];
        $product_ids = explode(",",I('id'));
        $product_num = explode(",",I('num'));
        
        $order_info = [];
        
        if(!empty($auto_order)){
            $address = M('order')->where(['order_num' => $auto_order])->find();
            
            $address['area'] = $address['county'];
            $address['name'] = $address['s_name'];
            $address['phone'] = $address['s_phone'];
            $address['addre'] = $address['s_addre'];
//          $this->address = $address;
        }else{
            $address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        }
        
        $order_info['area'] = $address['county'];
        $order_info['city'] = $address['city'];
        $order_info['province'] = $address['province'];
        $order_info['address_detail'] = $address['address_detail'];
        $order_info['name'] = $address['s_name'];
        $order_info['phone'] = $address['s_phone'];
        $order_info['addre'] = $address['s_addre'];
        
        $total_money_order = 0;
        $toatl_money = 0;
        $this->return_url = $this->base_url(__SELF__);
        foreach($product_ids as $k => $v){
            $product = $this->templet_model->where(['active' => '1'])->find($v);
            if ($sku_info) {
                $product['price'] = $sku_info['price'. $this->manager['level']];
            } else {
                $product['price'] = $product[$price];
            }
            $order_info['list'][$k]['sku_id'] = $sku_id;
            $order_info['list'][$k]['product'] = $product;
            $order_info['list'][$k]['num'] = $product_num[$k];
            $order_info['list'][$k]['properties'] = $properties;
//          $this->sku_id = $sku_id;
    
//          $this->product = $product;
//          $num=I('num');
//          $this->num = I('num');
            $total_money_order = bcmul($product['price'], $product_num[$k], 2);
            
//          setLog($total_money_order);
            
            if(C('ORDER_SHIPPING')){
                //运费相关
                $template_id=$product['template_id'];
                $shipping_way=I('shipping_way');
                $shipping_id=I('shipping_way_id');
                $condition=[
                    'template_id'=> $template_id,
    //            'shipping_way'=>$shipping_way,
                    'id' =>$shipping_id,
                ];
    
    //        $condition=[
    //            'template_id'=> $template_id,
    //        ];
                //判断是否存在满减免运费
    
                $flag=true;
                $template=M('shipping_goods_shipping_template')->find($template_id);
                $shipping_reduce_way=C('SHIPPING_REDUCE_WAY');
                if($shipping_reduce_way){
                    $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>$shipping_reduce_way])->find();
                }else{
                    $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>0])->find();
                }
    
                if($reduce_info['type'] == 1){
                    $flag=$num<$reduce_info['need_num'];
                }elseif ($reduce_info['type'] == 2){
                    $flag=$total_money_order<$reduce_info['need_money'];
                }elseif ($reduce_info['type'] == 3){
                    $flag= ($num<$reduce_info['need_num'])&&($total_money_order<$reduce_info['need_money']);
                }
    
    
                if(empty($template['reduce_id']) || $flag){
                    $template_info=M('shipping_way')->where($condition)->find();
                    $template_info_first_num=$template_info['first_num'];
                    $template_info_first_fee=$template_info['first_fee'];
                    $template_info_continue_num=$template_info['continue_num'];
                    $template_info_continue_fee=$template_info['continue_fee'];
                    $product_parameter=$product['product_parameter'];
                    $total_num=$product_parameter*$num;
    
                    //运费计算
                    if($total_num<=$template_info_first_num){
                        $total_money_fee+=bcadd($template_info_first_fee,0,2);
                        $toatl_money+=bcadd($total_money_order,$template_info_first_fee,2);
                    }
    
                    if($total_num>$template_info_first_num){
                        $num_two=$total_num-$template_info_first_num;
                        $continue_money=bcmul(ceil($num_two/$template_info_continue_num),$template_info_continue_fee,2);
                        $total_money_fee+=bcadd($template_info_first_fee,$continue_money,2);
                        $toatl_money+=bcadd($total_money_order,$total_money_fee,2);
    
                    }
                }else{
                    $total_money_fee=0;
                    $toatl_money+=bcadd($total_money_order,$total_money_fee,2);
                }
                
                //参数
    //            import('Lib.Action.Common', 'App');
    //            $Common=new Common();
    //            $canshu=$Common->get_related_data($template_id,);
            }else{
                $toatl_money += $total_money_order;
            }
            
            $order_info['list'][$k]['reduce_info'] = $reduce_info;
        }
//      var_dump($toatl_money);die;
        $order_info['total_money_fee'] = $total_money_fee;
        $order_info['total_money'] = $toatl_money;
        $this->order_info = $order_info;
        
        $this->product_ids = implode("|",$product_ids);
        $this->product_num = implode("|",$product_num);
//      $this->reduce_info=$reduce_info;
//      $this->total_money_fee=$total_money_fee;
//      $this->total_money=$toatl_money;
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        if( $Stock->open_stock ){
            $this->display('buy_detail_stock');
        }
        else{
            $this->display();
        }
    }
    
    
        //提交订单详情 new
    public function buy_detail_shop()
    {
        //属性
        $sku_id = I('sku_id');
        $order_num = I('order');
        $total_money_fee = bcadd(C('SHOP_IN_SHOP_SHIPPING'),0,2);
        $shipper_num = C('SHOP_IN_SHOP_NUM');
        if ($sku_id) {
            import('Lib.Action.Sku','App');
            $sku = new Sku();
            //获取库存信息
            $sku_info = $sku->get_templet_sku($sku_id);
            //获取规格值
            $property = $sku->get_templet_property_com($sku_id);
            $value = $sku->get_value($property);
            $this->properties = $value;
        }

        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where(['active' => '1'])->find(I('id'));
        if ($sku_info) {
            $product['price'] = $sku_info['price'. $this->manager['level']];
        } else {
            $product['price'] = $product[$price];
        }
        $this->sku_id = $sku_id;

        $this->product = $product;
        $num=I('num');
        $this->num = I('num');
        $total_money = $total_money_order = bcmul($product['price'], I('num'), 2);
        
//      if($num<$shipper_num){
//          $total_money_order = bcadd($total_money_order,$total_money_fee,2);
//      }else{
//          $total_money_fee ="0.00";
//      }
        
        $shop_order = M('shop_order')->where(['order_num'=>$order_num])->find();
        $addre_detail = explode(' ',$shop_order['s_addre']);
        $shop_order['province'] = $addre_detail[0];
        $shop_order['city'] = $addre_detail[1];
        $shop_order['county'] = $addre_detail[2];
//      var_dump($addre_detail);die;
        $this->addre_detail = $addre_detail[3];
        $this->share_info = $shop_order;
        $this->return_url = $this->base_url(__SELF__);
        if(C('ORDER_SHIPPING')){
            //运费相关
            $template_id=$product['template_id'];
            $shipping_way=I('shipping_way');
            $shipping_id=I('shipping_way_id');
            $condition=[
                'template_id'=> $template_id,
//            'shipping_way'=>$shipping_way,
                'id' =>$shipping_id,
            ];

//        $condition=[
//            'template_id'=> $template_id,
//        ];
            //判断是否存在满减免运费

            $flag=true;
            $template=M('shipping_goods_shipping_template')->find($template_id);
            $shipping_reduce_way=C('SHIPPING_REDUCE_WAY');
            if($shipping_reduce_way){
                $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>$shipping_reduce_way])->find();
            }else{
                $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>0])->find();
            }

            if($reduce_info['type'] == 1){
                $flag=$num<$reduce_info['need_num'];
            }elseif ($reduce_info['type'] == 2){
                $flag=$total_money_order<$reduce_info['need_money'];
            }elseif ($reduce_info['type'] == 3){
                $flag= ($num<$reduce_info['need_num'])&&($total_money_order<$reduce_info['need_money']);
            }


            if(empty($template['reduce_id']) || $flag){
                $template_info=M('shipping_way')->where($condition)->find();
                $template_info_first_num=$template_info['first_num'];
                $template_info_first_fee=$template_info['first_fee'];
                $template_info_continue_num=$template_info['continue_num'];
                $template_info_continue_fee=$template_info['continue_fee'];
                $product_parameter=$product['product_parameter'];
                $total_num=$product_parameter*$num;

                //运费计算
                if($total_num<=$template_info_first_num){
                    $total_money_fee=bcadd($template_info_first_fee,0,2);
                    $total_money=bcadd($total_money_order,$template_info_first_fee,2);
                }

                if($total_num>$template_info_first_num){
                    $num_two=$total_num-$template_info_first_num;
                    $continue_money=bcmul(ceil($num_two/$template_info_continue_num),$template_info_continue_fee,2);
                    $total_money_fee=bcadd($template_info_first_fee,$continue_money,2);
                    $total_money=bcadd($total_money_order,$total_money_fee,2);

                }
            }else{
                $total_money_fee=0;
                $total_money=$total_money=bcadd($total_money_order,$total_money_fee,2);
            }
            //参数
//            import('Lib.Action.Common', 'App');
//            $Common=new Common();
//            $canshu=$Common->get_related_data($template_id,);

        }
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        $this->shipper_fee=$shipper_fee;
        $this->shipper_num=$shipper_num;
        $this->reduce_info=$reduce_info;
        $this->total_money_fee=$total_money_fee;
        $this->total_money=$total_money;
        
        if( $Stock->open_stock ){
            $this->display('buy_detail_stock');
        }
        else{
            $this->display();
        }
    }

    //购物车提交订单详情 new
    public function buy_cart_detail_stock()
    {
        $tids = "";
        $nums = "";
        
        //属性
        $sku_ids = "";
        //运费
        $shipping_way="";
        $shipping_id="";
        $total_money = 0;
        $total_nums=0;
        $total_money_fee = 0;
        $cart_ids = explode('|', I('cart_ids'));
        $carts = $this->get_shopping_cart(['id' => ['in', $cart_ids]]);



        foreach ($carts as $k => $cart) {
            $total_money += bcmul($cart['product']['price'], $cart['num'], 2);
            $template_ids[]=$cart['product']['template_id'];
            $total_nums +=$cart['num'];
            if($k == (count($carts)-1)){
                $tids .= $cart['tid'];
                $nums .= $cart['num'];
                $sku_ids .= $cart['sku_id'];
            }else{
                $tids .= $cart['tid'] . '|';
                $nums .= $cart['num'] . '|';
                $sku_ids .= $cart['sku_id'] . '|';
            }
        }
        //计算运费
        if(C('ORDER_SHIPPING')){
            import('Lib.Action.Shipping', 'App');
            $shipping = (new Shipping())->count_shipping($carts);
            $total_money_fee = bcadd($shipping['shipping_fee'],0,2);
            if(!C('SHIPPING_REDUCE_WAY')){
                foreach ($template_ids as $v=>$k){

                    $shipping = M('shipping_goods_shipping_template')->where(['id'=>$k])->find();
                    $redcuce_info=M('shipping_reduce')->where(['id'=>$shipping['reduce_id'],'shipping_reduce_way'=>0])->find();
                    if($redcuce_info){
                        if ($total_nums>=$redcuce_info['need_num'] && $total_money>=$redcuce_info['need_money']){
                            $total_money_fee = 0;
                        }
                    }
                }
            }
        }

        $this->total_money_fee = $total_money_fee;
        $this->tids = $tids;
        $this->nums = $nums;
        $this->sku_ids = $sku_ids;
        $this->cart_ids = I('cart_ids');
        $this->carts = $carts;
        $this->total_money = bcadd($total_money ,$total_money_fee,2);
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->return_url = $this->base_url(__SELF__);
        $this->display();
    }

    //购物车提交订单详情 new
    public function buy_cart_detail()
    {
        $tids = "";
        $nums = "";
        
        //属性
        $sku_ids = "";
        //运费
        $shipping_way="";
        $shipping_id="";
        $total_money = 0;
        $total_nums=0;
        $total_money_fee = 0;
        $cart_ids = explode('|', I('cart_ids'));
        $carts = $this->get_shopping_cart(['id' => ['in', $cart_ids]]);



        foreach ($carts as $cart) {
            $total_money += bcmul($cart['product']['price'], $cart['num'], 2);
            $template_ids[]=$cart['product']['template_id'];
            $total_nums +=$cart['num'];
            $tids .= $cart['tid'] . '|';
            $nums .= $cart['num'] . '|';
            $sku_ids .= $cart['sku_id'] . '|';
        }
        //计算运费
        if(C('ORDER_SHIPPING')){
            import('Lib.Action.Shipping', 'App');
            $shipping = (new Shipping())->count_shipping($carts);
            $total_money_fee = bcadd($shipping['shipping_fee'],0,2);
            if(!C('SHIPPING_REDUCE_WAY')){
                foreach ($template_ids as $v=>$k){

                    $shipping = M('shipping_goods_shipping_template')->where(['id'=>$k])->find();
                    $redcuce_info=M('shipping_reduce')->where(['id'=>$shipping['reduce_id'],'shipping_reduce_way'=>0])->find();
                    if($redcuce_info){
                        if ($total_nums>=$redcuce_info['need_num'] && $total_money>=$redcuce_info['need_money']){
                            $total_money_fee = 0;
                        }
                    }
                }
            }
        }

        $this->total_money_fee = $total_money_fee;
        $this->tids = $tids;
        $this->nums = $nums;
        $this->sku_ids = $sku_ids;
        $this->cart_ids = I('cart_ids');
        $this->carts = $carts;
        $this->total_money = bcadd($total_money ,$total_money_fee,2);
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->return_url = $this->base_url(__SELF__);
        $this->display();
    }

    //添加到购物车
    public function add_shopping_cart()
    {
       if( !IS_AJAX ){
            return FALSE;
        }
        
        $order_shopping_cart_obj = M('order_shopping_cart');
        
        
        $tid = I('id');
        $num = I('num');
        
        //属性
        $sku_id = I('sku_id');
        $price_filed = 'price'.$this->manager['level'];
        
        if( empty($tid) || empty($num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '请选择产品并且数量不能少于1！',
            );
            $this->ajaxReturn($return_result, 'json');return;
        }
        
        $condition_count = array(
            'uid'   =>  $this->uid,
        );
        
        $order_shopping_cart_count = $order_shopping_cart_obj->where($condition_count)->count();
        
        if( $order_shopping_cart_count >= 100 ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '您的购物车太满了，请先整理您的购物车！',
            );
            $this->ajaxReturn($return_result, 'json');
            return;
        }
        
        
        //购物车同样商品添加先直接按照产品ID进行添加
        //TODO:不同产品属性生成不同的购物车信息
        $condition = array(
            'uid'   =>  $this->uid,
            'tid'   =>  $tid,
            'sku_id' => $sku_id,
        );
        
        $old_cart = $order_shopping_cart_obj->where($condition)->find();
        
        
        if( !empty($old_cart) ){
            $old_cart_num = $old_cart['num'];
            
            $new_cart_num = bcadd($num,$old_cart_num,0);
            
            $save_info = array(
                'num'   =>  $new_cart_num,
                'updated'   =>  time(),
            );
            $save_res = $order_shopping_cart_obj->where($condition)->save($save_info);
        }
        else{
            //属性
            $properties = '';
            if ($sku_id) {
                import('Lib.Action.Sku','App');
                $sku = new Sku();
                $sku_info = $sku->get_templet_sku($sku_id);
                //判断库存
                if (!$sku->check_templet_quantity($sku_info, $sku_id, $tid, $num)) {
                    $return_result = array(
                        'code'  =>  5,
                        'msg'   =>  '库存不足！',
                    );
                    $this->ajaxReturn($return_result, 'json');
                }
                $properties = $sku->get_value($sku_info['properties']);
                $price = $sku_info[$price_filed];
            } else {
                $price = M('templet')->where(['id' => $tid])->getField($price_filed);
            }
            $add_info = array(
                'uid'   => $this->uid,
                'tid'   =>  $tid,
                'num'   =>  $num,
                'created'   =>  time(),
                //商品属性/库存代码
                'sku_id' => $sku_id,
                'properties' => $properties,
                'price' => $price,
            );
            
            $save_res = $order_shopping_cart_obj->add($add_info);
        }
        
        
        if( $save_res ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '添加购物车成功！',
            );
        }
        else{
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '添加购物车失败，请重试！',
            );
        }
        
        $this->ajaxReturn($return_result, 'json');

    }

    //我的购物车
    public function shopping_cart()
    {
        $total_money = 0;
        $tid = [];
        $carts = $this->get_shopping_cart(['uid' => $this->uid]);
        foreach ($carts as $cart) {
            $tid[] = $cart['tid'];
        }
        //删除已经不存在的产品
        array_unique($tid);
        foreach ($tid as $id) {
            if (!$this->templet_model->find($id)) {
                $this->cart_model->where(['tid' => $id])->delete();
            }
        }
        $carts = $this->get_shopping_cart(['uid' => $this->uid]);
        foreach ($carts as $cart) {
            $total_money += bcmul($cart['product']['price'], $cart['num'], 2);
        }
        $this->carts = $carts;
        $this->total_money = $total_money;
        $this->display();
    }

    //获取购物车
    private function get_shopping_cart($where)
    {
        $tid = [];
        $temp_products = [];
        $price = 'price' . $this->manager['level'];
        $carts = $this->cart_model->where($where)->select();
        if ($carts) {
            foreach ($carts as $cart) {
                $tid[] = $cart['tid'];
            }
            array_unique($tid);
            //获取产品
            $products = $this->templet_model->where(['id' => ['in', $tid]])->select();
            foreach ($products as $key => $product) {
//                $product['price'] = $product[$price];
                $temp_products[$product['id']] = $product;
            }

            foreach ($carts as $key => $cart) {
                $temp_products[$cart['tid']]['price'] = $cart['price'];
                $carts[$key]['product'] = $temp_products[$cart['tid']];
            }
        }
        return $carts;
    }


    /**
     * 获取退款金额
     *
     * @param int $level
     * @param decimal $recharge_money
     * @return decimal
     */
    private function get_min_refund_money($level, $recharge_money)
    {

        if ($level == 0) {

        }

        if ($recharge_money == 0 || $level == 0 || is_null($level)) {
            return 0;
        }

        $money_min_refund = M('money_min_refund');
        $set_info = $money_min_refund->where(array('id' => '1'))->find();

        $min_refund_key = 'level' . $level;
        $min_refund = isset($set_info[$min_refund_key]) ? $set_info[$min_refund_key] : 0;


        $refund_money = bcsub($recharge_money, $min_refund, 2);

        if ($refund_money < 0) {
            $refund_money = 0;
        }


        return $refund_money;
    }


    //订单详情
    public function ddxq()
    {
//        $where['order_num'] = I('order_num');
//        $row = M('Order')->where($where)->group('order_num')->find();
//        //把产品分别显示
//        $rol = M('Order')->where($where)->select();
//        foreach ($rol as $k => $v) {
//            $product = M('templet')->where(array('id' => $v['p_id']))->find();
//            $rol[$k]['p_name'] = $product['name'];
//        }
//        $this->row = $row;
//        $this->assign('rol', $rol);
//        $this->display();


        $ordernum = I('order_num');

        $orderObj = M('Order');
        $templetObj = M('Templet');
        $distributorObj = M('distributor');

        $shipper = AllShipperCode();

        $res = $orderObj->where(array('order_num' => $ordernum))->select();
        foreach ($res as $key => $value) {
            $pid = $value['p_id'];
            $userid = $value['user_id'];
            $v_ordernumber = $value['ordernumber'];
            $v_ordernumber_arr = !empty($v_ordernumber) ? explode(',', $v_ordernumber) : [];

            $arr = $value;
            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();

            $value_shipper = $value['shipper'];
            $arr['shipper_name'] = isset($shipper[$value_shipper]) ? $shipper[$value_shipper] : '未选择快递公司';
            $arr['ordernumber_arr'] = $v_ordernumber_arr;

            $orderInfo[] = $arr;
        }

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        //多个快递单号
        $express_no = explode(',', $orderInfo[0]['ordernumber']);
        $express_count = count($express_no);
        $this->express_no = $express_no;
        $this->express_count = $express_count;

        $this->status_name = $Order->status_name;
        $this->orderInfo = $orderInfo;
        $this->shipper = $shipper;
        $this->display();
    }//end func ddxq


    //我的订单和下单功能
    public function my_dd()
    {
        //统计产品数量
        $price = "price" . $this->manager['level'];

        $condition_templet['active'] = '1';
        $list = M('templet')->where($condition_templet)->select();
        $a = 1;
        $d = 1;
        foreach ($list as $k => $v) {
            $list[$k]['price'] = $v[$price];
            $list[$k]['id_num'] = $a;
            $a++;
        }
        $manager = $this->manager;
        $manager['count'] = count($list);
        $this->manager = $manager;
        $whr['user_id'] = $this->manager['id'];
        //查询收货地址
        $rel = M('receiving')->where($whr)->find();
        //查询已下的订单
        $row = M('Order')->where($whr)->order('time desc')->group('order_num')->select();
        foreach ($row as $b => $c) {
            $row[$b]['or_num'] = $d;
            $d++;
        }


        import('Lib.Action.Order', 'App');
        $Order = new Order();

        $this->status_name = $Order->status_name;
        $this->rel = $rel;
        $this->assign('row', $row);
        $this->assign('list', $list);
        $this->display();
    }


    public function my_dhj()
    {
        $price = "price" . $_SESSION['level'];
        $count = M('templet')->count("id");
        $list = M('templet')->field('id,name,image,disc,state,' . $price)->select();
        foreach ($list as $k => $v) {
            $list[$k]['price'] = $v[$price];
        }
        $manager = $this->manager;
        $manager['count'] = $count;
        $this->manager = $manager;
        $this->assign('list', $list);
        $this->display();
    }

//    public function wsh() {
//        $orderObj = M('Order');
//        $templetObj = M('Templet');
//        $distributorObj = M('distributor');
//        $ordernum = $_GET['order'];
//        $res = $orderObj->where(array('order_num' => $ordernum))->select();
//        foreach ($res as $key => $value) {
//            $pid = $value['p_id'];
//            $userid = $value['user_id'];
//            $arr = $value;
//            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
//            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();
//            $orderInfo[] = $arr;
//        }
//        $this->orderInfo = $orderInfo;
//        $this->display();
//    }

    //下级订单
    public function xjdd()
    {
        $orderObj = M('order');
        $distributor = M('distributor');
        $uid = $this->uid;
        $where['status'] = array('eq', 2);
        $where['o_id'] = $uid;
        $map['status'] = array('eq', 3);
        $map['o_id'] = $uid;
        $a['_complex'] = $where;
        $condition[] = $a;
        $condition['_logic'] = 'OR';
        $condition['_complex'] = $map;
        $res_not_count = $orderObj->where(array('o_id' => $uid, 'status' => 1))->count('distinct order_num');
        $res_yes_count = $orderObj->where($condition)->count('distinct order_num');
        $res_no = $orderObj->where(array('o_id' => $uid, 'status' => 1))->group('order_num')->select();
        $res_ye = $orderObj->where($condition)->group('order_num')->select();
        foreach ($res_no as $key => $value) {
            $userid = $value['user_id'];
            $arr = $value;
            $arr['uInfo'] = $distributor->where("id={$userid}")->find();
            $res_not[] = $arr;
        }
        foreach ($res_ye as $key => $value) {
            $userid = $value['user_id'];
            $arr2 = $value;
            $arr2['uInfo'] = $distributor->where("id={$userid}")->find();
            $res_yes[] = $arr2;
        }
        $this->res_yes_count = $res_yes_count;
        $this->res_not_count = $res_not_count;
        $this->res_count = $res_not_count + $res_yes_count;
        $this->res_not_total = $res_not_total;
        $this->res_yes_total = $res_yes_total;
        $this->res_not = $res_not;
        $this->res_yes = $res_yes;
        $this->display();
    }

    public function cxxjdd()
    {
        $orderObj = M('order');
        $distributorObj = M('distributor');
        $row = I('post.s_order_num');
        $where['order_num'] = $row;
        $res = $orderObj->where($where)->group('order_num')->find();
        if (!empty($res)) {
            $res['uInfo'] = $distributorObj->where(array('id' => $res['user_id']))->find();

            $this->ajaxReturn($res, 'json');
            exit();
        }
        $this->ajaxReturn('none', 'json');
    }

    //
    public function cldd()
    {
        $order_num = I('order_num');

        // $mids = I('mids');
        // $mids = substr($mids, 1);
        // $order_nums = explode('_', $mids);

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        $order_audit_result = $Order->admin_audit($order_num);
        $this->ajaxReturn($order_audit_result, 'json');
        
    }//end func cldd

    public function xjjg()
    {
        $distributor = M('distributor');
        $list = $distributor->where(array('pid' => $this->manager['id']))->select();
        //统计下级人数
        $count = count($list);
        $manager = $this->manager;
        $manager['count'] = $count;
        $this->manager = $manager;
        $this->assign('list', $list);
        $this->display();
    }

    public function xjjg_xq()
    {
        $where['id'] = I('id');
        $manager = M('distributor')->field('id,name,level,levname,headimgurl')->where($where)->find();
        $price = "price" . $manager['level'];
        $list = M('templet')->field('id,name,image,disc,state,' . $price)->select();
        foreach ($list as $k => $v) {
            $list[$k]['price'] = $v[$price];
        }
        $this->manager = $manager;
        $this->assign('list', $list);
        $this->display();
    }

//    public function ysh() {
//        $orderObj = M('Order');
//        $templetObj = M('Templet');
//        $distributorObj = M('distributor');
//        $ordernum = $_GET['order'];
//        $res = $orderObj->where(array('order_num' => $ordernum))->select();
//        foreach ($res as $key => $value) {
//            $pid = $value['p_id'];
//            $userid = $value['user_id'];
//            $arr = $value;
//            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
//            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();
//            $orderInfo[] = $arr;
//        }
//        $this->orderInfo = $orderInfo;
//        $this->display();
//    }


    //订单详细
    public function detail()
    {
        $orderObj = M('Order');
        $templetObj = M('Templet');
        $distributorObj = M('distributor');
        $ordernum = I('order');

        $res = $orderObj->where(array('order_num' => $ordernum))->select();
        foreach ($res as $key => $value) {
            $pid = $value['p_id'];
            $userid = $value['user_id'];
            $arr = $value;
            $arr['pInfo'] = $templetObj->where("id={$pid}")->find();
            $arr['uInfo'] = $distributorObj->where("id={$userid}")->find();
            $orderInfo[] = $arr;
        }

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        $this->status_name = $Order->status_name;
        $this->orderInfo = $orderInfo;
        $this->display();
    }//end func detail


    //添加和修改收货地址
    public function recehand()
    {
        $id = I('post.id');
        $name = I('post.name');
        $phone = I('post.phone');
        $addre = I('post.addre');
        $receiving = M('receiving');
        $where['user_id'] = $id;
        $rel = M('receiving')->where($where)->find();
        //判断地址存不存在
        if ($rel) {
            $arr = array(
                'name' => $name,
                'phone' => $phone,
                'addre' => $addre
            );
            $rew = $receiving->where($where)->save($arr);
            $this->ajaxReturn('1', 'JSON');
        } else {
            $arr = array(
                'user_id' => $id,
                'name' => $name,
                'phone' => $phone,
                'addre' => $addre
            );
            $rew = $receiving->add($arr);
            $this->ajaxReturn($rew, 'JSON');
        }
    }

    //订单写入order表
    public function orderhand()
    {
        $auto_order = I('auto_order_num');
        //$total_price = I('post.money');
        $order_num = I('post.order_num');
        $p_ids = I('post.p_ids');
        $p_nums = I('post.p_nums');
        $note = I('post.note');
        $pay_type = I('post.pay_type');//支付类型
        $pay_photo = I('post.pay_photo');
        
        $s_name = I('s_name');
        $s_phone = I('s_phone');
        $province = I('province');
        $county = I('county');
        $city = I('city');
        $addre = I('addre');

        $type = I('type');  // 判断订单的来源类型shop：店中店
        $pay_type = 0;
        
        $cart_ids = I('post.cart_ids');

        import('Lib.Action.Order', 'App');

        $Order = new Order();

        //属性
        $sku_ids = I('sku_ids');

        //


        //运费相关
        $shipping_way_id=I('post.shipping_way_id');
        $shipping_way=I('post.shipping_way');
        
        $total_money_fee = 0;
        $total_money = 0;
        $total_nums=0;

        if ($cart_ids) {
            //购物车产品结算，运费计算
            $ids = explode('|', $cart_ids);
            $carts = $this->get_shopping_cart(['id' => ['in', $ids]]);

            foreach ($carts as $cart) {
                $total_money += bcmul($cart['product']['price'], $cart['num'], 2);
                $template_ids[]=$cart['product']['template_id'];
                $total_nums +=$cart['num'];
            }
            //计算运费
            if(C('ORDER_SHIPPING')){
                import('Lib.Action.Shipping', 'App');
                $shipping = (new Shipping())->count_shipping($carts);
                $total_money_fee = bcadd($shipping['shipping_fee'],0,2);
                if(!C('SHIPPING_REDUCE_WAY')){
                    foreach ($template_ids as $v=>$k){
                        $shipping = M('shipping_goods_shipping_template')->where(['id'=>$k])->find();
                        $redcuce_info=M('shipping_reduce')->where(['id'=>$shipping['reduce_id'],'shipping_reduce_way'=>0])->find();
                        if($redcuce_info){
                            if ($total_nums>=$redcuce_info['need_num'] && $total_money>=$redcuce_info['need_money']){
                                $total_money_fee = 0;
                            }
                        }
                    }
                }
                $shipping_way_id = $shipping['shipping_ids'];
            }
        }

        $write_info = array(
            'auto_order_num' => $auto_order,
            'order_num' => $order_num,
            'p_ids' => $p_ids,
            'p_nums' => $p_nums,
            'cart_ids' => $cart_ids,
            'note' => $note,
            'pay_type'  =>  $pay_type,
            'pay_photo' =>  $pay_photo,
            'sku_ids' => $sku_ids,
            'shipping_way_id'=>$shipping_way_id,
            'shipping_way'=>$shipping_way,
            'total_money_fee' => $total_money_fee,
        );
        
        if(!empty($type)&&$type=='shop'){
            $write_info = [];
            $order_shop_num = I('order_shop_num');
            if(empty($order_shop_num)){
                $result = [
                    'code' => 2,
                    'msg'  => '订单号不能为空'
                ];
                $this->ajaxReturn($result);
            }
            
            $order = M('shop_order');
            $shop_templet = M('shop_templet');
            $templet = M('templet_category');
            $condition = [
                'order_num' => $order_shop_num
            ];
            $order_info = $order->where($condition)->find();
//          var_dump($order_info);die;
            $p_id = '';
            if(!empty($order_info)){
                $p_id = $order_info['p_id'];
            }
            $is_bind = $shop_templet->where(['id'=>$p_id])->getField('bind_pid');
            if(empty($is_bind)){
               $result = [
                    'code' => 3,
                    'msg' => "该产品没有关联产品"
               ];
            }else{
                import('Lib.Action.Order','App');
                $Order = new Order();
                $shipper_num = C('SHOP_IN_SHOP_NUM');
                $cart_id = $templet->where(['pid' => $is_bind])->getField('category_id');
                $write_info = array(
                        'order_shop_num' => $order_shop_num,
                        'share_phone' => $s_phone,
                        'share_addre' => $addre,
                        'share_province' => $province,
                        'share_city' => $city,
                        'share_county' => $county,
                        'share_name' => $s_name,
                        'order_num' => $order_num,
                        'p_ids' => array($is_bind),
                        'p_nums' => array($order_info['total_num']),
                        'cart_ids' => $cart_id,
                        'note' => $order_info['note'],
                        'pay_type'  =>  $pay_type,
                        'pay_photo' =>  $pay_photo,
                        'sku_ids' => $sku_ids,
                        'shipping_way_id'=>$shipping_way_id,
                        'shipping_way'=>$shipping_way,
                        'total_money_fee' => $total_money_fee,
                        'type' => $type,
                    );
//              if($order_info['total_num']>=$shipper_num){
//                  $write_info['total_money_fee'] = $order_info['total_price'];
//                  $write_info['shop_shipper'] = '1';     // 1为免邮，2为算邮费
//              }else{
//                  $write_info['total_money_fee'] = C('SHOP_IN_SHOP_SHIPPING');
//                  $write_info['shop_shipper'] = '2';     // 1为免邮，2为算邮费
//              }
    //          var_dump($write_info);die;
        }
        }else if(!empty($auto_order)){
//          setLog("not shop but auto_order");
            $order = M('order');
            $dis_info=M('distributor')->where(['id'=>$this->uid])->find();
            foreach ($p_ids as $kk=>$vv){
                $product= M('templet')->where(['id'=>$vv])->find();
                $arr1['tid']=$vv;
                $arr1['num']=$p_nums[$kk];
                $price_level='price'.$dis_info['level'];
                $arr1['price']=$product[$price_level];
                $arr1['product']=$product;
                $arr2[]=$arr1;
            }
            import('Lib.Action.Shipping', 'App');
            $Shipping=new Shipping();


            if(empty($s_phone)||empty($s_name)||empty($province)||empty($county)||empty($city)||empty($addre)){
                $condition_order = [
                    'uid' => $this->uid,
                    'order_num' => $auto_order,
                ];
                $order_info = $order->where($condition_order)->find();

    //          var_dump($order_info);die;
                if($order_info){
                    $total_money_fee=$Shipping->count_shipping($arr2,$order_info['province']);

                    $write_info['share_phone'] = $order_info['s_phone'];
                    $write_info['share_addre'] = $order_info['s_addre'];
                    $write_info['share_province'] = $order_info['province'];
                    $write_info['share_city'] = $order_info['city'];
                    $write_info['share_county'] = $order_info['county'];
                    $write_info['share_name'] = $order_info['s_name'];
                    $write_info['auto_order'] = $auto_order;
                    $write_info['total_money_fee']=$total_money_fee['shipping_fee'];
                }
            }else{
                $total_money_fee=$Shipping->count_shipping($arr2,$province);

                $write_info['share_phone'] = $s_phone;
                $write_info['share_addre'] = $addre;
                $write_info['share_province'] = $province;
                $write_info['share_city'] = $city;
                $write_info['share_county'] = $county;
                $write_info['share_name'] = $s_name;
                $write_info['auto_order'] = $auto_order;
                $write_info['total_money_fee']=$total_money_fee['shipping_fee'];
            }
            
        }

        $return_result = $Order->write_order($this->uid, $write_info);
        

        $this->ajaxReturn($return_result, 'json');
    }


    //删除订单
    public function delorder()
    {
        $order_num = I('post.id');


        import('Lib.Action.Order', 'App');
        $order = new Order();
        $result = $order->delorder($order_num);

        $this->ajaxReturn($result, 'JSON');
        return;

        if ($result['code'] == 1) {
            $this->ajaxReturn(TRUE, 'JSON');
        } else {
            $this->ajaxReturn(FALSE, 'JSON');
        }

//        $condition = array(
//            'order_num' => $order_num,
//            'status' => 1,
//        );
//        $order = M('Order')->where($condition)->find();
//        $del = M('Order')->where($condition)->delete();
//
//        if ($del && $order['o_id']) {
//            //取消订单模板消息
//            import('Lib.Action.Message', 'App');
//            $message = new Message();
//            $openid = M('distributor')->where(['id' => $order['o_id']])->getField('openid');
//            $message->push(trim($openid), $order, $message->order_cancle);
//        }
//        $this->ajaxReturn($del, 'JSON');
    }

    //确认收货
    public function shouhuo()
    {
        $order_num = I('post.order_num');
        import('Lib.Action.Order', 'App');
        $Order = new Order();
//        $condition = array(
//            'status' => 2,
//            'order_num' => $order_num,
//            'user_id' => $this->uid,
//        );
//
//        $res = $this->model->where($condition)->save(['status' => 3]);

        $result = $Order->confirm_order($order_num);


        /**
         * TODO:
         * 改为ajax直接返回$result
         */
        if ($result['code'] != 1) {
            $this->ajaxReturn(FALSE, 'JSON');
        }

        $this->ajaxReturn(TRUE, 'JSON');
    }//end func shouhuo


    //快递单号填写
    public function ordernb()
    {
        if (!$this->isPost()) {
            return false;
        }

        $order_num = I('post.order_num');
        $ordernumber = I('post.ordernumber');
        $shipper = I('shipper');

        $o_id = $this->uid;

        $condition = array(
            'o_id' => $o_id,
            'order_num' => $order_num,
        );

        $update_info = array(
            'shipper' => $shipper,
            'ordernumber' => $ordernumber
        );
        $save = M('order')->where($condition)->save($update_info);


        if ($save) {
            $result = array(
                'code' => 1,
                'msg' => '填写成功！',
            );
        } else {
            $result = array(
                'code' => 2,
                'msg' => '填写失败，请重试！',
            );
        }

        $this->ajaxReturn($result, 'JSON');
    }


    //下单订单明细

    public function all()
    {
//        $order = M('order');
//        $count = $order->where(array('user_id' => $this->uid))->count('distinct order_num');
//
//        if ($count > 0) {
//            $applyList = $order->where(array('user_id' => $this->uid))->order('time desc')->group('order_num')->select();
//
//            $all_order_info = $order->field('order_num,p_id,p_name,p_image,num,price')->where(array('user_id' => $this->uid))->select();
//
//            $all_order_key_info = array();
//            foreach ($all_order_info as $k_ao => $v_ao) {
//                $v_ao_order_num = $v_ao['order_num'];
//                $all_order_key_info[$v_ao_order_num][] = $v_ao;
//            }
//
//            foreach ($applyList as $k => $v) {
//                $v_order_num = $v['order_num'];
//                $v_ordernumber = $v['ordernumber'];
//                $v_ordernumber_arr = !empty($v_ordernumber) ? explode(',', $v_ordernumber) : [];
//                $applyList[$k]['ordernumber_arr'] = $v_ordernumber_arr;
//                $the_order_info = isset($all_order_key_info[$v_order_num]) ? $all_order_key_info[$v_order_num] : array();
//                $applyList[$k]['row'] = $the_order_info;
//            }
//            //联表查询
//            $distributor_info = [];
//            //将id取出来
//            foreach ($applyList as $v) {
//                if (!isset($ids[$v['user_id']])) {
//                    $ids[$v['user_id']] = $v['user_id'];
//                }
//            }
//            //将取出来的id在另外的表根据id查询
//            $cats = M('distributor')->where(['id' => ['in', $ids]])->select();
//
//            //取出数据
//            foreach ($cats as $v) {
//                $distributor_info[$v['id']] = $v;
//            }
//
//            foreach ($applyList as $k => $v) {
//                $applyList[$k]['distributor_headimgurl'] = $distributor_info[$v['user_id']]['headimgurl'];
//                $applyList[$k]['distributor_name'] = $distributor_info[$v['user_id']]['name'];
//            }
//
//            $this->assign('applyList', $applyList);
//        }
        $this->stock=trim(I('stock'));
        $stock_order=C('FUNCTION_MODULE')['STOCK_ORDER'];
        $this->is_open_stock=$stock_order;

        $this->display();
    }

        //下单和审核的订单明细ajax

    public function get_all()
    {
        if (!IS_AJAX) {
          return FALSE;
        }

        $type = trim(I('type'));
        $page_num = trim(I('page_num'));
        $status = trim(I('status'));
        $page_list_num = trim(I('page_list_num'));
        
        
//         $status='';
//         $page_num = 1;
//        $type = 'take';

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($type)) {
            $return_result = [
                'code' => 3,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        if( empty($type) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }


        $other['is_group'] = 1;
//      是否开启店中店，允许查看分享出去的链接

        if ($type == 'take') {
            if( $status == 7 ){
                $condition = [
                    'user_id' => ['neq',$this->uid],
                    'status' => $status,
                ];
            }
            elseif ($status != null) {
                $condition = [
                    'user_id' => $this->uid,
                    'status' => $status,
                ];
            }
            else {
                $condition = [
                    'user_id' => $this->uid,
                ];
            }
            $info = $Order->get_order($page_info, $condition, $other);
        } elseif ($type == 'shipping') {
            if ($status != null) {
                $condition = [
                    'o_id' => $this->uid,
                    'status' => $status,
                ];
            } else {
                $condition = [
                    'o_id' => $this->uid,
                ];
            }
            $info = $Order->get_order($page_info, $condition, $other);
        }elseif( $type == 'shop'){
            import('Lib.Action.Shoporder', 'App');
            $Shoporder = new Shoporder(); 
            $other['shop_open']=true;
            if ($status != null) {
                $condition = [
                    'o_id' => $this->uid,
                    'status' => $status,
                ];
            } else {
                $condition = [
                    'o_id' => $this->uid,
                ];
            }
            $info = $Shoporder->get_order($page_info, $condition, $other);
        }

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
            'status' => $status,
            'condition' =>  $condition,
        ];
        $this->ajaxReturn($return_result);

    }
    //删除已收货订单
    public function del_order()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');
        $condition = array(
            'status' => 3,
            'order_num' => $order_num,
            'user_id' => $this->uid,
        );
        $del = M('Order')->where($condition)->delete();

        $this->ajaxReturn($del, 'JSON');
    }

    //根据user_id取消订单
    public function cancel_order()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');

        import('Lib.Action.Order', 'App');
        $order = new Order();
        $result = $order->delorder($order_num);

        $this->ajaxReturn($result, 'JSON');
    }

    //审核订单模块
    public function examine()
    {
        import('Lib.Action.Order','App');
        $Order = new Order();
        $this->auto_order_open = $Order->auto_order;
        
        $order = M('order');

        $count = $order->where(array('o_id' => $this->uid))->count('distinct order_num');

        if ($count > 0) {
            $applyList = $order->where(array('o_id' => $this->uid))->order('time desc')->group('order_num')->select();

            $all_order_info = $order->field('order_num,p_id,p_name,p_image,num,price')->where(array('o_id' => $this->uid))->select();

            $all_order_key_info = array();
            foreach ($all_order_info as $k_ao => $v_ao) {
                $v_ao_order_num = $v_ao['order_num'];
                $all_order_key_info[$v_ao_order_num][] = $v_ao;
            }

            foreach ($applyList as $k => $v) {
                $v_order_num = $v['order_num'];
                $v_ordernumber = $v['ordernumber'];
                $v_ordernumber_arr = !empty($v_ordernumber) ? explode(',', $v_ordernumber) : [];
                $applyList[$k]['ordernumber_arr'] = $v_ordernumber_arr;
                $the_order_info = isset($all_order_key_info[$v_order_num]) ? $all_order_key_info[$v_order_num] : array();
                $applyList[$k]['row'] = $the_order_info;
            }
            //联表查询
            $distributor_info = [];
            //将id取出来
            foreach ($applyList as $v) {
                if (!isset($ids[$v['user_id']])) {
                    $ids[$v['user_id']] = $v['user_id'];
                }
            }

            //将取出来的id在另外的表根据id查询
            $cats = M('distributor')->where(['id' => ['in', $ids]])->select();

            //取出数据
            foreach ($cats as $v) {
                $distributor_info[$v['id']] = $v;
            }
            foreach ($applyList as $k => $v) {
                $applyList[$k]['distributor_headimgurl'] = $distributor_info[$v['user_id']]['headimgurl'];
                $applyList[$k]['distributor_name'] = $distributor_info[$v['user_id']]['name'];
            }

            $this->assign('applyList', $applyList);
        }
        $this->display();
    }
    
    //审核店中店订单模块
    public function examine_shop()
    {   
        import('Lib.Action.Order','App');
        $Order = new Order();
        $this->auto_order_open = $Order->auto_order;
        $this->display();
    }

 //审核订单
    public function firm_order()
    {
        $order_num = I('post.order_num');
        $type = I('type');
        if(!empty($type)&&$type == 'shop'){
            import('Lib.Action.Shoporder', 'App');
            $Order = new Shoporder();
        }else{
            import('Lib.Action.Order', 'App');
            $Order = new Order();
        }
        
        $res = $Order->admin_audit($order_num);
//        setLog(json_encode($admin_audit_result));
//        $res = 0;
//        if( $admin_audit_result['code'] == 1 ){
//            $res = 1;
//        }
        
        $this->ajaxReturn($res, 'JSON');

    }

    //根据o_id取消订单
    public function cancel_order_oid()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');
        $type_order = I('type_order');
        import('Lib.Action.Shoporder', 'App');
        import('Lib.Action.Order', 'App');
        if($type_order=="shop"){
            $order = new Shoporder();
        }else{
            $order = new Order();
        }
        
        $result = $order->delorder($order_num, $this->uid);

        $this->ajaxReturn($result, 'JSON');
        return;


//        $condition = array(
//            'status' => 1,
//            'order_num' => $order_num,
//            'o_id' => $this->uid,
//        );
//        setLog(json_encode($condition));
//        $del = M('Order')->where($condition)->delete();
//
//        $this->ajaxReturn($del, 'JSON');
    }

    //查看审核订单详情页面
    public function shipping_order_detail()
    {

        $order_num = I('get.order_number');
        $type = I('type');
        $condition = array(
            'order_num' => $order_num,
        );
        $order= M('order');
        if($type == 'shop'){
            $order= M('shop_order');
        }
        $condition_order = $order->where($condition)->find();
//      var_dump($type);die;
        $condition_info = $order->where($condition)->select();
        $uid = $condition_order['user_id'];
        if($type == 'shop'){
            $uid = $condition_order['o_id'];
        }
        $distributor_info = M('distributor')->where(array('id' => $uid))->find();
        $this->assign('condition_order', $condition_order);
        $this->assign('condition_info', $condition_info);
        $this->assign('distributor_info', $distributor_info);

        $this->display();
    }

    //查看下单订单详情
    public function take_order_detail()
    {
        $order_num = I('get.order_number');

        $condition = array(
            'order_num' => $order_num,
        );
        $condition_order = M('order')->where($condition)->find();
        $condition_info = M('order')->where($condition)->select();
        $uid = $condition_order['user_id'];
        foreach ($condition_info as $k=>$v){
            $shipping=$v['ordernumber'];
        }
        $arr = explode(',',$shipping);

        $distributor_info = M('distributor')->where(array('id' => $uid))->find();
        $this->assign('condition_order', $condition_order);
        $this->assign('condition_info', $condition_info);
        $this->assign('distributor_info', $distributor_info);
        $this->assign('arr',$arr);
        $this->display();
    }

    //查看下单订单详情页面ajax
    public function get_take_order_detail()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = trim(I('get.order_number'));
        //$order_num='321501223177436';
        if (empty($order_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '订单号获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        $condition = [
            'order_num' => $order_num,
        ];
        import('Lib.Action.Order', 'App');
        $Order = new Order();
        $info = $Order->get_sao_order([], $condition);

        $this->ajaxReturn($info, 'JSON');

    }


    //获取产品信息ajax
    public function get_templet()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $name = trim(I('get.name'));
        $page_num = trim(I('page_num'));
        $category = trim(I('post.category'));
        $page_list_num = trim(I('page_list_num'));
        
        $type = I('type');
        $sort = I('sort');

        if (!empty($type)) {
            $sort_info = $type . ' desc';
            if (!empty($sort)) {
                $sort_info = $type.$this->manager['level'].' '. $sort;
            }
        }else{
            $sort_info= 'sequence desc,id desc';
        }

        //$name='水';
        // $page_num=1;
        // $category=3;

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        //每页默认为10
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $price = "price" . $this->manager['level'];


        if(!empty($category)){
            $condition=[
                'category_id' => $category,
                'active' => '1',
            ];

            $info = $Order->get_templet($page_info, $condition, $sort_info, $price);
        }else{
            if(!empty($name)){
                $condition['name']  = array('like',"%$name%");
                $condition['active'] = '1';
            }else{
                $condition=[
                    'active' => '1',
                ];
            }

            $info = $Order->get_templet($page_info, $condition, $sort_info, $price);
        }

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];
        $this->ajaxReturn($return_result);
    }

    //产品分类
    public function templet_category()
    {
        if (!IS_AJAX) {
//          return FALSE;
        }

//        $page_num = trim(I('page_num'));
//        $page_list_num = trim(I('page_list_num'));
//
//        // $page_num=1;
//        import('Lib.Action.Order', 'App');
//        $Order = new Order();
//
//        //每页默认为10
//        if (empty($page_list_num)) {
//            $page_list_num = 10;
//        }
//
//        $page_info = [
//            'page_num' => $page_num,
//            'page_list_num' => $page_list_num,
//        ];
//        if (empty($page_num)) {
//            $return_result = [
//                'code' => 2,
//                'msg' => '页码获取失败',
//            ];
//            $this->ajaxReturn($return_result);
//        }
//
//        $condition = [];
//        $result = $Order->get_templet_category($page_info, $condition);
        
        $condition=[
            'status' => 1,
        ];
        $cats = $this->templet_cat_model->where($condition)->order('sequence desc,id desc')->select();
        //一级分类
        foreach ($cats as $cat) {
            if ($cat['pid'] == 0) {
                $one[] = $cat;
                $one_id[] = $cat['id'];
            }
        }
        //二级分类关联一级分类
        foreach ($cats as $cat) {
            if (in_array($cat['pid'], $one_id)) {
                $two[$cat['pid']] = $cat;
                $two_id[] = $cat['id'];
            }
        }
        //三级分类关联二级分类
        foreach ($cats as $cat) {
            if (in_array($cat['pid'], $two_id)) {
                $three[$cat['pid']] = $cat;
            }
        }
        
        //判断是否有子分类
        foreach ($one as $k => $v) {
            $one[$k]['has_child'] = 0;
            if (isset($two[$v['id']])) {
                $one[$k]['has_child'] = 1;
            }
        }
        foreach ($two as $k => $v) {
            $two[$k]['has_child'] = 0;
            if (isset($three[$v['id']])) {
                $two[$k]['has_child'] = 1;
            }
        }
        $result = [
            'one' => $one,
            'two' => $two,
            'three' => $three
        ];
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);

    }
    
     //获取产品子分类
    public function get_son_templet_category()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $pid = I('pid');
        //全部
        if ($pid == -1) {
            $one_cats = $this->templet_cat_model->where(['pid' => 0,'status'=> 1])->order('sequence desc,id desc')->select();
            foreach ($one_cats as $cat) {
                $one_ids[] = $cat['id'];
            }
            $where['pid'] = ['in', $one_ids];
            $where['status'] =1;
        } else {
            $where['pid'] = $pid;
            $where['status'] =1;
        }
        //二级分类
        $two = $this->templet_cat_model->where($where)->order('sequence desc,id desc')->select();
        foreach ($two as $v) {
            $two_ids[] = $v['id'];
        }
        //三级分类关联二级分类
        $cats = $this->templet_cat_model->where(['pid' => ['in', $two_ids],'status'=>1])->order('sequence desc,id desc')->select();
        foreach ($cats as $cat) {
            $three[$cat['pid']][] = $cat;
        }
        //判断是否有子分类
        foreach ($two as $k => $v) {
            $two[$k]['has_child'] = 0;
            if (isset($three[$v['id']])) {
                $two[$k]['has_child'] = 1;
            }
        }
        $result = [
            'two' => $two,
            'three' => $three
        ];
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }

    //商品搜索
    public function get_search_order(){
        
        if(!IS_AJAX){
            return FALSE;
        }

        $name = trim(I('post.name'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));

//        $name="动";
//        $page_num=1;

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        //每页默认为10
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];

        $price = "price" . $this->manager['level'];

        $condition['name']  = array('like',"%$name%");
        $condition['active'] = '1';

        $info = $Order->get_templet($page_info, $condition, '', $price);

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];

        $this->ajaxReturn($return_result);
    }
    
    public function success() {
        $this->order = M('order')->where(['order_num' => I('order_num')])->find();
        $this->display();
    }

    //运费相关--提交订单时快递方式的选择
    public function get_shipping_ajax(){
        if(!IS_AJAX){
            return FALSE;
        }
        $id=I('id');
     
     // $id=15;

        if (empty($id)) {
            $return_result = [
                'code' => 2,
                'msg' => 'id获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        $product = $this->templet_model->where(['active' => '1'])->find($id);

        $product_template_id=$product['template_id'];
//        $shipping_way_id=trim(I('shipping_id'));
        $condition=[
            'template_id'=>$product_template_id,
//            'shipping_way_id'=>$shipping_way_id
        ];
        $shipping_reduce_way=C('SHIPPING_REDUCE_WAY');
        if(!$shipping_reduce_way){
            $shipping_reduce_way=0;
        }
        $info =$this->shipping_way_model->where($condition)->select();
        $templet_info=M('shipping_goods_shipping_template')->where(['id'=>$product_template_id])->find();
        $reduce_info=M('shipping_reduce')->where(['id'=>$templet_info['reduce_id'],'shipping_reduce_way'=>$shipping_reduce_way])->find();
        $return_result = [
            'code' => 1,
            'product'=>$product,
            'info' => $info,
            'reduce_info' => $reduce_info,
            'msg' => '获取成功',
        ];

        $this->ajaxReturn($return_result);
    }
    
    //改变购物车产品数量
    public function save_cart_num() {
        $cart_model = M('order_shopping_cart');
        $cart_ids = explode('|', I('cart_ids'));
        $cart_nums = explode('|', I('cart_nums'));
        foreach ($cart_ids as $k => $id) {
            $cart_model->where(['id' => $id])->save(['num' => $cart_nums[$k]]);
        }
        $this->ajaxReturn(true, 'JSON');
    }
    
    //删除购物车
    public function del_cart() {
        if (IS_AJAX) {
            $cart_ids = explode('|', I('cart_ids'));
            $res = M('order_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();
            $this->ajaxReturn($res, 'JSON');
        }
    }

    //获取默认地址
    public function get_default_address(){
        if(!IS_AJAX){
          return FALSE;
        }
        $condition=[
            'default'=>1,
            'user_id'=>$this->uid,
        ];
        $info=M('address')->where($condition)->find();
        $return_result = [
            'code' => 1,
            'info' => $info,
            'msg' => '获取成功',
        ];

        $this->ajaxReturn($return_result);
    }

    //快递方式运费计算处理
    public function get_shipping_fee(){
        if(!IS_AJAX){
            return FALSE;
        }


        $id=I('post.id');
//        $id=2;

        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where(['active' => '1'])->find($id);
        $product['price'] = $product[$price];
        //产品的参数
        $product_parameter=$product['product_parameter'];

        $template_id=$product['template_id'];
        $shipping_way=I('post.shipping_way');
        $shipping_id=I('post.shipping_id');
//        $template_id=144;
//        $shipping_way=0;
//        $shipping_id=629;
        //运费相关
        $condition=[
            'template_id'=> $template_id,
//          'shipping_way'=>$shipping_way,
            'id' =>$shipping_id,
        ];

        $num=I('num');
//        $num=10;
        $total_money_order = bcmul($product['price'], $num, 2);
        import('Lib.Action.Order', 'App');
        $Order = new Order();
        
        $info=$Order->get_shipping_fee($num,$total_money_order,$condition,$product_parameter,$template_id);
        $return_result = [
            'code' => 1,
            'info' => $info,
            'msg' => '获取成功',
        ];

        $this->ajaxReturn($return_result);
    }
    
    //判断购物车里sku_id是否存在
    public function is_null_sku_id() {
        import('Lib.Action.Sku', 'App');
        $sku = new Sku();
        $tids = [];
        $sku_ids = [];
        $name = "";
        $cart_ids = explode('|', I('cart_ids'));
        $carts = $this->cart_model->where(['uid' => $this->uid, 'id' => ['in',$cart_ids]])->select();
        foreach ($carts as $cart) {
            $tids[] = $cart['tid'];
            if ($cart['sku_id']) {
                $sku_ids[] = $cart['sku_id'];
            }
        }
        //得到不存在库存id集合
        $null_sku_ids = $sku->is_null_sku_id($sku_ids);
        if ($null_sku_ids) {
            $tids = $this->cart_model->field('tid')->where(['uid' => $this->uid, 'sku_id' => ['in',$null_sku_ids]])->select();
            foreach ($tids as $id) {
                $null_tids[] = $id['tid'];
            }
            $templets = $this->templet_model->where(['id' => ['in', $null_tids]])->select();
            
            foreach ($templets as $v) {
                $name .= ' '. $v['name'];
            }
            $msg = "$name 产品已失效，请清除";
            $return_result = [
                'code' => -1,
                'msg' => $msg,
            ];

        } else {
            $return_result = [
                'code' => 1,
            ];
        }
        $this->ajaxReturn($return_result);
    }
    
    //统计订单/购物车数量
    public function get_count() {
        $no_delivery_count = 0;
        $yes_delivery_count = 0;
        $order = $this->model->where(['user_id' => $this->uid, 'status' => ['in', [1,2]]])->group('order_num')->select();
        foreach ($order as $v) {
            if ($v['status'] == 1) {
                $no_delivery_count++;
            } else {
                $yes_delivery_count++;
            }
        }
        $cart_count = $this->cart_model->where(['uid' => $this->uid])->count('id');
        $result = [
            'no_delivery_count' => $no_delivery_count,
            'yes_delivery_count' => $yes_delivery_count,
            'cart_count' => $cart_count,
        ];
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'result' => $result
        ];
        $this->ajaxReturn($return_result);
    }
    
    
    
    //===================start 库存下单===================================
    
    //转为库存  orderstock
    public function orderstock()
    {
        //$total_price = I('post.money');
        $order_num = I('post.order_num');
        $p_ids = I('post.p_ids');
        $p_nums = I('post.p_nums');
        $pay_photo = I('pay_photo');
        $pay_type = I('pay_type');
        
        $p_ids = explode('|',$p_ids);
        $p_nums = explode('|',$p_nums);
        
        if(empty($pay_type)){
            $return_result = [
                'code' => -1,
                'info' => null,
                'msg' => "请选择支付方式"
            ];
            $this->ajaxReturn($return_result);
        }

        $cart_ids = I('post.cart_ids');
        $buy_way=trim(I('buy_way'));
        import('Lib.Action.Stock','App');

        $Stock = new Stock();

        $write_info = array(
            'order_num' =>  $order_num,
            'p_ids' =>  $p_ids,
            'p_nums' => $p_nums,
            'cart_ids' => $cart_ids,
            'pay_photo' => $pay_photo,
            'pay_type' => $pay_type,
            'buy_way' => $buy_way,
        );
        
//        $return_result = $Stock->conversion_stock($this->uid, $write_info);
        $return_result = $Stock->stock_order($this->uid, $write_info);
        
        if( $return_result['code'] == 1 ){
            $return_result['return_url'] = __APP__.'/admin/order/stock';//改为库存的链接
        }
        
        $this->ajaxReturn($return_result, 'json');
    }//end func orderstock
    
    
    //下库存订单
    public function stock_to_order(){
        
        
        
        $order_num = I('post.order_num');
        $p_ids = I('post.p_ids');
        $p_nums = I('post.p_nums');
        $note = I('post.note');
        $pay_type = I('post.pay_type');//支付类型
        $pay_photo = I('post.pay_photo');
        
        
        $pay_type = 0;
        
        $cart_ids = I('post.cart_ids');

        import('Lib.Action.Stock','App');

        $Stock = new Stock();

        //属性
        $sku_ids = I('sku_ids');

        $write_info = array(

            'order_num' => $order_num,
            'p_ids' => $p_ids,
            'p_nums' => $p_nums,
            'cart_ids' => $cart_ids,
            'note' => $note,
            'pay_type'  =>  $pay_type,
            'pay_photo' =>  $pay_photo,
            'sku_ids' => $sku_ids,
        );
        
        $return_result = $Stock->stock_to_order($this->uid, $write_info);
        
        if( $return_result['code'] == 1 ){
            $return_result['return_url'] = __APP__.'/admin/order/all?part=1';//改为库存的链接
        }
        
        $this->ajaxReturn($return_result, 'json');
    }//end func stock_to_order
    
    
    
    //库存
    public function stock(){
        
        $this->return_url = $this->base_url(__SELF__);
        $this->address = M('address')->where(['user_id' => $this->uid, 'default' => 1])->find();
        $this->display();
    }//end func stock
    
    
    //转移库存
    public function transfer_stock(){
        
        
        $this->display();
    }//end func transfer_stock
    
    
    //转移库存AJAX
    public function transfer_stock_ajax(){
        if( !IS_AJAX ){
            return FALSE;
        }
        //info格式:array(array('p_id'=>'产品A'，'num'=>100),array(...));
        $info = I('post.info');
        $tid = I('post.tid');
        
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        $info = [$info];
        
        $return_result = $Stock->transfer_stock($this->uid,$tid, $info);
        
        if( $return_result['code'] == 1 ){
            $return_result['return_url'] = '';//改为库存的链接
        }
        
        $this->ajaxReturn($return_result, 'json');
    }//end func transfer_stock
    
    
    
    //获取库存信息
    public function get_stock_info_ajax(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        $condition_special['key_for_temp_id'] = TRUE;
        
        
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));
        $get_all = trim(I('get_all'));
        $pid = trim(I('pid'));
        $num_not_empty = trim(I('num_not_empty'));
        
        //每页默认为10
        if( empty($page_list_num) ){
            $page_list_num = 10;
        }

        $page_info = [
            'page_num'  =>  $page_num,
            'page_list_num' =>  $page_list_num,
        ];
        
        
        if( $get_all ){
            $page_info = [];
        }
        
        
        $condition = [
            'uid'   => $this->uid,
        ];
        
        if( !empty($pid) ){
            $condition['pid'] = $pid;
        }
        if( $num_not_empty ){
            $condition['num'] = array('neq',0);
        }
        
        
        $info = $Stock->get_stock($page_info,$condition,$condition_special);
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $info,
        ];
        
        $this->ajaxReturn($return_result);
    }//end func get_stock_info_ajax
    
    //库存列表
    public function stock_list(){
        $uid = $this->uid;
        $pid = I('pid');
        $condition = [];
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
            'page_list_num' => 30
        );
        
        if(!empty($uid)){
            $condition = [
                'pid' => $pid,
                'uid' => $uid
            ];
            
            import('Lib.Action.Stock','App');
            $Stock = new Stock();
            $result = $Stock->get_stock_log($page_info,$condition);
        }
//      var_dump($result['list']);die;
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->count = count($result['list']);
        
        $this->display();
        
    }
    
    
    //审核云仓订单
    public function firm_stock_order()
    {
        $order_num = I('post.order_num');
        import('Lib.Action.Stock', 'App');
        $Stock = new Stock();
        
        $order_num_arr[] = $order_num;
        
        $result = $Stock->audit_order($order_num_arr,'admin');
//        $res = 0;
//        if( $result['code'] == 1 ){
//            $res = 1;
//        }
        
        $this->ajaxReturn($result, 'JSON');

    }
    
    
    //取消云仓订单
    public function cancel_stock_order()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $order_num = I('post.order_num');

        import('Lib.Action.Stock', 'App');
        $Stock = new Stock();
        $result = $Stock->delorder($order_num,$this->uid);

        $this->ajaxReturn($result, 'JSON');
    }
    
    
          //查看下单订单详情
    public function stock_order_detail()
    {
        $order_num = I('get.order_number');

        $condition = array(
            'order_num' => $order_num,
        );
        $condition_order = M('stock_order')->where($condition)->find();
        $condition_info = M('stock_order')->where($condition)->select();
        $uid = $condition_order['user_id'];
        foreach ($condition_info as $k=>$v){
            $shipping=$v['ordernumber'];
        }
        $arr = explode(',',$shipping);
            
        $distributor_info = M('distributor')->where(array('id' => $uid))->find();
        $this->assign('condition_order', $condition_order);
        $this->assign('condition_info', $condition_info);
        $this->assign('distributor_info', $distributor_info);
        $this->assign('arr',$arr);
        $this->display();
    }
    //===================end 库存下单===================================
    
    // 云仓库存退货页面 
    public function stock_return(){
        $this->display();
    }
    
    // 云仓库存退货申请 
    public function stock_return_apply_ajax(){
        if(!IS_AJAX){
            return FALSE;
        }
        
        $pids = I('p_ids');
        $pnums = I('p_nums');
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        if(empty($pids)||empty($pnums)){
            $result = [
                'code' => -1,
                'info' => null,
                'msg' => "退货申请的商品或数量有误"
            ];
        }else{
            $stock_info = [];
            foreach($pids as $k => $v){
                $stock_info[$k]['p_id'] = $v;
                $stock_info[$k]['num'] = $pnums[$k];
            }
//          var_dump($stock_info);die;
            $result = $Stock->add_stock_refund_apply($this->uid,$stock_info);
        }
        
        $this->ajaxReturn($result);
    }
    
    // 获取云仓库存退货申请信息 
    public function get_stock_return_apply(){
        if(!IS_AJAX){
            return FALSE;
        }
        
        $page_num = trim(I('page_num'));
        $status = trim(I('status'));
        $page_list_num = trim(I('page_list_num'));
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

//      if ($status!="") {
//          $return_result = [
//              'code' => 3,
//              'msg' => '状态获取失败',
//          ];
//          $this->ajaxReturn($return_result);
//      }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        
        $condition = [
            'uid' => $this->uid,
            'status' => $status
        ];
        
        $result = $Stock->get_stock_refund_apply($page_info,$condition);
        
        $result = [
            'code' => 1,
            'info' => $result,
            'msg' => '获取成功'
        ];
        
        $this->ajaxReturn($result);
    }
    // 获取某个退货申请
//  public function get_applyid_info(){
//      if(!IS_AJAX){
//          return FALSE;
//      }
//      
//      $id = I('id');
//      if(empty($id)){
//          $result = [
//              'code' => -1,
//              'info' => null,
//              'msg' => '暂无该申请记录',
//          ];
//      }else{
//          $condition = [
//              'id' => $id,
//              'uid' => $uid
//          ];
//          import('Lib.Action.Stock','App');
//          $Stock = new Stock();
//          $result_stock = $Stock->get_stock_refund_apply([],$condition);
//          $result = [
//              'code' => 1,
//              'info' => $result_stock['list'],
//              'msg' => '获取成功',
//          ];
//      }
//      $this->ajaxReturn($result);
//  }
    
    // 获取某个退货申请
    public function stock_return_detail(){
        
        $id = I('id');
        if(!empty($id)){
            $condition = [
                'id' => $id,
                'uid' => $this->uid,
            ];
            import('Lib.Action.Stock','App');
            $Stock = new Stock();
            $result_stock = $Stock->get_stock_refund_apply([],$condition);
            
            $this->list = $result_stock['list'];
        }
        $this->display();
    }
    
    // 取消云仓退货申请
    public function cancel_stock_apply(){
        if(!IS_AJAX){
            return FALSE;
        }
        $apply_id = I('apply_id');
        $stock_return_apply = M('stock_refund_apply');
        $result = [];
        
        if(empty($apply_id)){
            $result = [
                'code' => -1,
                'msg' => '没有找到该申请',
            ];
        }else{
            $condition = [
                'uid' => $this->uid,
                'id' => $apply_id,
                'status' => 0,
            ];
            
            $res = $stock_return_apply->where($condition)->delete();
            if($res){
                $result = [
                    'code' => 1,
                    'msg' =>'取消申请成功',
                ];
            }else{
                $result = [
                    'code' => -2,
                    'msg' => '取消申请失败',
                ];
            }
        }
        $this->ajaxReturn($result);
    }
    
    function get_stock_list(){
        if(!IS_AJAX){
            return FALSE;
        }
        $pid = I('pid');
        $type = I('type');
        $month=trim(I('month'));
        $stock_log = M('stock_log');
        
        if(!empty($pid)){
            $condition['pid'] = $pid;
        }
        if(!empty($type)){
            $condition['type'] = $type;
        }
        $condition['uid'] = $this->uid;
        
        $total_num = $stock_log->where($condition)->sum('point');
        
        if(!empty($month)){
            $month_str=$month.'01';
            $start_time = strtotime($month_str);
            $end_time = strtotime('+1 month -1 sec', $start_time);
            $condition['created'] = ['between',[$start_time,$end_time]];
        }else{
            $month_str = date('Ym',time());
            $month_str = $month_str.'01';
            $start_time = strtotime($month_str);
            $end_time = strtotime('+1 month -1 sec', $start_time);
            $condition['created'] = ['between',[$start_time,$end_time]];
        }
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        $total_month_num = $stock_log->where($condition)->sum('point');
       
//      var_dump($month);die;
        //获取记录
        $page_info = array(
            'page_num' =>  I('get.page'),
            'page_list_num' => 10
        );
        
        $result = $Stock->get_stock_log($page_info,$condition);
        $result['total_num'] = $total_num;
        $result['total_month_num'] = $total_month_num;
        
        $this->ajaxReturn($result);
    }
    
    public function get_shipping_money_fee(){
        if(!IS_AJAX){
            return FALSE;
        }
        
        $p_ids = I('p_ids');
        $p_nums = I('p_nums');
        $province = trim(I('province'));
        
        if(empty($p_ids)||empty($p_nums)||!is_array($p_ids)||!is_array($p_nums)||empty($province)){
            $result = [
                'code' => -1,
                'msg' => '查询失败，请检查参数！'
            ];
        }else{
            $templet = M('templet');
            $dis_info=M('distributor')->where(['id'=>$this->uid])->find();
            foreach ($p_ids as $kk=>$vv){
                $product= $templet->where(['id'=>$vv])->find();
                $arr1['tid']=$vv;
                $arr1['num']=$p_nums[$kk];
                $price_level='price'.$dis_info['level'];
                $arr1['price']=$product[$price_level];
                $arr1['product']=$product;
                $arr2[]=$arr1;
            }
            
            import('Lib.Action.Shipping', 'App');
            $Shipping=new Shipping();
            
            $total_money_fee=$Shipping->count_shipping($arr2,$province);
            
            $result = [
                'code' => 1,
                'info' => $total_money_fee,
                'msg' => '获取邮费成功！',
            ];
        }
        $this->ajaxReturn($result);
    }
}

?>