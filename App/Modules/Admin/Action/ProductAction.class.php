<?php

/**
 * 	微斯咖经销商后台——产品下单
 */
class ProductAction extends CommonAction {

    //产品分类
    public function products() {
        $templet_category_obj = M('templet_category');
        
        $category = $templet_category_obj->select();


        $this->category =   $category;
        $this->display();
    }

    //产品列表
    public function product_list() {
        $templet_obj = M('Templet');
        $distributor_obj = M('distributor');
        
        $search = trim(I('get.search'));
        
        $where['openid'] = $_SESSION['oid'];
        $manager = $distributor_obj->field('id,level')->where($where)->find();
        $price = "price";
        
        $categor = I('category');
        $condition = array();
        
        if( !empty($categor) ){
            $condition = array(
                'category'   =>  $categor,
            );
        }
        
        if( !empty($search) ){
            $condition = array(
                'name'  =>  array(
                    'like',"%$search%"
                ),
            );
        }
        
        $condition['active'] = '1';
        
        $templet_info = $templet_obj->where($condition)->select();
        
        
        
        foreach( $templet_info as $k => $v ){
            $the_price = $v[$price];
            
            $templet_info[$k]['price'] = $the_price;
        }
        
        $this->level = $manager['level'];
        $this->templet_info = $templet_info;
        $this->display();
    }

    //产品详情
    public function product_single() {
        $templet_obj = M('Templet');
        $distributor_obj = M('distributor');
        
        $id = I('id');
        
        $where['openid'] = $_SESSION['oid'];
        $manager = $distributor_obj->field('id,level')->where($where)->find();
        $price_key = "price" . $manager['level'];
        
//        echo $price_key;
        
        $condition['id'] = $id;
        $condition['active'] = '1';
        $list = $templet_obj->where($condition)->find();
        
        
//        $list['price'] = $list[$price_key];
        
        //商品属性
        
        import('Lib.Action.Sku','App');
        $sku = new Sku();
        $has_property = true;
        $properties = $sku->get_templet_properties($id);
        if (!$properties) {
            $has_property = 0;
        }
        $this->properties = $properties;
        $this->has_property = $has_property;
        $this->skus = $sku->get_templet_skus($id);
        $this->list = $list;
        $this->display();
    }

    

    //地址管理
    public function address() {
        $receiving_obj = M('receiving');
        $distributor_obj = M('distributor');
        
        $managerid = $this->uid;
        
        $return_url = I('return_url');
        
        $condition_man = array(
            'id'    =>  $managerid,
        );
        
        $manager = $distributor_obj->where($condition_man)->find();
        
        
        $condition = array(
            'user_id'    =>  $managerid,
        );
        
        $list = $receiving_obj->where($condition)->select();
        
//        print_r($list);return;
        
        $this->return_url = $return_url;
        $this->manager = $manager;
        $this->list = $list;
        $this->display();
    }
    
    
    //地址选择
    public function address_choose() {
        $receiving_obj = M('receiving');
        $distributor_obj = M('distributor');
        
        $managerid = $this->uid;
        
        $return_url = I('return_url');
        
        $condition_man = array(
            'id'    =>  $managerid,
        );
        
        $manager = $distributor_obj->where($condition_man)->find();
        
        
        $condition = array(
            'user_id'    =>  $managerid,
        );
        
        $list = $receiving_obj->where($condition)->select();
        
        $this->true_return_url = $this->get_base_url($return_url);
        $this->return_url = $return_url;
        $this->manager = $manager;
        $this->list = $list;
        $this->display();
    }//end func address_choose
    

    //地址编辑
    public function address_edit() {
        $receiving_obj = M('receiving');
        
        $managerid = $this->uid;
        $return_url = I('return_url');
        
        $id = I('id');
        
        $condition = array(
            'user_id'    =>  $managerid,
            'id'    =>  $id,
        );
        
        
        $list = $receiving_obj->order('id desc')->where($condition)->find();
        
        $this->return_url = $return_url;
        $this->list = $list;
        $this->display();
    }
    
    
    //编辑地址
    public function address_edit_post(){
        $receiving_obj = M('receiving');
        
        
        $managerid = $this->uid;
        
        $id = I('id');
        $name = trim(I('name'));
        $phone = trim(I('phone'));
        $provice = trim(I('province'));
        $city = trim(I('city'));
        $county = trim(I('county'));
        $addre = trim(I('addre'));
        $default = trim(I('default'));
        $return_url = I('return_url');
        
        if( $name == '' || $phone == '' || $provice == '' || $city == '' || $county == '' ){
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('地址信息必须完整填写!');history.go(-1);</script>";
            exit();
        }
        
        $area = $provice . ' ' . $city . ' ' . $county;
        
        $search = '/^(1[1|2|3|4|5|6|7|8|9][0-9])\d{8}$/';
        if ( strlen($phone) != '11' || !preg_match($search, $phone)) {
            
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('您的手机号填写有问题，请填写正确可用的手机号!');history.go(-1);</script>";
            exit();
        }
        
        $default = empty($default)?'0':'1';
        
        
        $save_info = array(
            'name'  =>  $name,
            'phone' =>  $phone,
            'addre' =>  $addre,
            'area'  =>  $area,
            'default'   =>  $default,
        );
        
        $condition = array(
            'id'    =>  $id,
            'user_id'   => $managerid, 
        );
        
        
        if( $default == '1' ){
            
            $old_receiving = $receiving_obj->field('default')->where($condition)->find();
            
            //如果更改为默认地址，需要把现有默认的改为非默认地址
            if( $old_receiving['default'] != 1 ){
                $condition_set['user_id'] = $managerid;
                $condition_set['default'] = '1';
                $change_default['default'] = '0';
                $receiving_obj->where($condition_set)->save($change_default);
            }
        }
        
        
        if( !empty($id) ){
            $save_res = $receiving_obj->where($condition)->save($save_info);
        }
        else{
            $condition_sear['user_id']  =   $managerid;
            $rec_count = $receiving_obj->where($condition_sear)->count();
            
            //第一次添加都为默认地址
            if( $rec_count == 0 ){
                $save_info['default'] = 1;
            }
            elseif( $rec_count > 10 ){
                header("Content-Type:text/html;charset=utf-8");
                echo "<script>alert('您的地址添加太多了，最多只能是10个，请管理好您的地址!');history.go(-1);</script>";
                exit();
            }
            
            $save_info['user_id'] = $managerid;
            $save_res = $receiving_obj->add($save_info);
        }
        
        
        if( $save_res ){
            if( !empty($return_url) ){
                $return_url = $this->get_base_url($return_url);
                
                
                header("Content-Type:text/html;charset=utf-8");
                echo "<script>alert('编辑成功!');window.location.href='".$return_url."'; </script>";
                exit();
            }
            
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('编辑成功!');window.location.href='".__GROUP__."/product/address'; </script>";
            exit();
        }
        else{
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('编辑失败或未做出修改，请重试!');history.go(-1);</script>";
            exit();
        }
        
        
    }//end func address_edit_post
    
    
    //删除地址
    public function address_del(){
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $receiving_obj = M('receiving');
        
        $id = I('id');
        $managerid = $this->uid;
        
        $condition = array(
            'id'    =>  $id,
            'user_id'   =>  $managerid,
        );
        
        $info = $receiving_obj->where($condition)->find();
        
        if( empty($info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有找到该地址信息！',
            );
            $this->ajaxReturn($return_result, 'json');
            return;
        }
        
        $del_res = $receiving_obj->where($condition)->delete();
        
        $last_id = 0;
        if( $info['default'] == 1 && $del_res ){
            
            $condition_last = array(
                'user_id'   =>  $managerid,
            );
            $last_info = $receiving_obj->where($condition_last)->order('id desc')->find();
            $last_id = $last_info['id'];
            
            $change_default['default'] = '1';
            $condition_last['id']   =   $last_id;
            $receiving_obj->where($condition_last)->save($change_default);
        }
        
        
        if( $del_res ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '删除地址成功！',
                'last_id'   =>  $last_id,
            );
            $this->ajaxReturn($return_result, 'json');
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '删除地址失败，请重试！',
            );
            $this->ajaxReturn($return_result, 'json');
        }
    }//end func address_del
    
    
    
    
    
    //地址编辑
    public function product_buy() {
        
        $receiving_obj = M('receiving');
        $templet_obj = M('Templet');
        $distributor_obj = M('distributor');
        $order_shopping_cart_obj = M('order_shopping_cart');
        
        $temp_id = I('temp_id');
        $num = I('num');
        $cart_id = I('cart_id');
        $addre_id = I('addre_id');
        
        //商品属性/库存代码
        $sku_id = I('sku_id');
        if ($sku_id) {
            import('Lib.Action.Sku','App');
            $sku = new Sku();
            $sku_info = $sku->get_templet_sku($sku_id);
            if (!$sku->check_templet_quantity($sku_info, $sku_id, $temp_id, $num)) {
                echo "<script>alert('库存不足，请重新下单!');</script>";
                exit();
            }
        }
        //:商品属性/库存代码
        
        if( empty($temp_id) && empty($cart_id) ){
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('没有下单信息!');history.go(-1); </script>";
            exit();
        }
        
        //经销商信息
        $managerid = $this->uid;
        
        $condition_man = array(
            'id'    =>  $managerid,
        );
        
        $manager = $distributor_obj->field('id,level')->where($condition_man)->find();
        
        //-------地址信息
        if( !empty($addre_id) ){
            $condition = array(
                'id'    =>  $addre_id,
                'user_id'   =>  $managerid,
            );
        }
        else{
            $condition = array(
                'user_id'   =>  $managerid,
                'default'   =>  '1',
            );
        }
        
        
        $receiving_info = $receiving_obj->where($condition)->order('id desc')->find();
        
        if( empty($receiving_info) ){
            $condition = array(
                'user_id'   =>  $managerid,
            );
            $receiving_info = $receiving_obj->where($condition)->order('id desc')->find();
        }
        
//        $receiving_default_info = array();
//        if( !empty($receiving_info) ){
//            
//            foreach( $receiving_info as $k_r => $v_r ){
//                $v_r_default = $v_r['default'];
//                
//                if( empty($receiving_default_info) ){
//                    $receiving_default_info = $v_r;
//                }
//                
//                if( $v_r_default == '1' ){
//                    $receiving_default_info = $v_r;
//                }
//            }
//        }
        
        
        //------------要下单的产品信息----------
        $price = "price";
        $condition_temp['active'] = '1';
        
        $shopping_cart_key = array();
        
        //购物车的产品
        
        $cal_id = 1;
        if( !empty($cart_id) && empty($temp_id) ){
            $cart_ids = explode('n', $cart_id);
            
            $condition_cart = array(
                'id'    =>  array('in',$cart_ids),
            );
            
            $shopping_cart = $order_shopping_cart_obj->where($condition_cart)->select();
            
//            $temp_id = array();
            
            foreach( $shopping_cart as $k_s => $v_s ){
                $v_s_tid = $v_s['tid'];
                
                
//                $temp_id[] = $v_s_tid;
//                $shopping_cart_key[$v_s_tid] = $v_s;
                
                //同一件商品不同属性情况
                $templet_info = $templet_obj->find($v_s_tid);
                $templet_info['cal_id'] = $cal_id;
                $templet_info['price'] = $v_s['price'];
                $templet_info['buy_num'] = $v_s['num'];
                $templet_info['sku_id'] = $v_s['sku_id'];
                $templet_infos[] = $templet_info;
                $cal_id++;
            }
        } else {
            $templet_infos = $templet_obj->where(['id' => $temp_id])->select();
            if ($sku_info) {
                $templet_infos[0]['price'] = $sku_info['price'];
            }
            $templet_infos[0]['cal_id'] = 1;
            $templet_infos[0]['buy_num'] = $num;
            $templet_infos[0]['sku_id'] = $sku_id;
            $cal_id++;
        }
        
//        foreach( $templet_info as $k => $v ){
//            $v_id = $v['id'];
//            $the_price = $v[$price];
//            
//            $templet_info[$k]['cal_id'] = $cal_id;
//            $templet_info[$k]['price'] = isset($shopping_cart_key[$v_id]['price'])?$shopping_cart_key[$v_id]['price']:$the_price;
//            $templet_info[$k]['buy_num'] = isset($shopping_cart_key[$v_id]['num'])?$shopping_cart_key[$v_id]['num']:$num;
//            $templet_info[$k]['sku_id'] = isset($shopping_cart_key[$v_id]['sku_id'])?$shopping_cart_key[$v_id]['sku_id']:$sku_id;
//            
//            $cal_id++;
//        }
        
        $templet_count = $cal_id-1;
//        print_r($templet_info);return;
        
//        var_dump($templet_info);die;
        $return_url = '&return_url='.$this->base_url(__SELF__);
        
        $this->manager = $manager;
        $this->return_url = $return_url;
        $this->cart_id = $cart_id;
        $this->templet_info = $templet_infos;
        $this->receiving_info   =   $receiving_info;
//        $this->receiving_default_info   =   $receiving_default_info;
        $this->templet_count = $templet_count;
        $this->display();
    }
    
    
    
    
    //购物车详情
    public function shopping_cart() {
        
        $order_shopping_cart_obj = M('order_shopping_cart');
        $templet_obj = M('Templet');
        
        $uid = $this->uid;
        
        //商品属性/库存代码
        $list = $order_shopping_cart_obj->where(['uid' => $uid])->select();
        
        $cal_id = 0;
        
        if( !empty($list) ){
            $all_tid = array();
            foreach( $list as $k => $v ){
                $the_tid = $v['tid'];
                
                $all_tid[] = $the_tid;
            }
            
            array_unique($all_tid);
            
            
            $contdition_temp = array(
                'id'   =>  array('in',$all_tid),
            );
            
            $temp_info = $templet_obj->where($contdition_temp)->select();
            $temp_key_info = array();
            foreach( $temp_info as $k_t => $v_t ){
                $the_id = $v_t['id'];
                $temp_key_info[$the_id] = $v_t;
            }
            
            $cal_id = 1;
            foreach( $list as $k => $v ){
                $the_tid = $v['tid'];
                
                $list[$k]['temp_info'] = $temp_key_info[$the_tid];
                $list[$k]['cal_id'] = $cal_id;
                $list[$k]['price'] = $v['price'];
                
                $cal_id++;
            }
            
        }
        $this->cal_id = $cal_id;
        $this->list = $list;
        $this->display();
    }
    
    //添加到购物车
    public function add_shopping_cart(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $order_shopping_cart_obj = M('order_shopping_cart');
        
        
        $tid = I('tid');
        $num = I('num');
        $sku_id = I('sku_id');
        
        if( empty($tid) || empty($num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '请选择产品并且数量不能少于1！',
            );
            $this->ajaxReturn($return_result, 'json');return;
        }
        
        $uid = $this->uid;
        
        $condition_count = array(
            'uid'   =>  $uid,
        );
        
        $order_shopping_cart_count = $order_shopping_cart_obj->where($condition)->count();
        
        if( $order_shopping_cart_count >= 10 ){
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
            'uid'   =>  $uid,
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
            //商品属性/库存代码
            $properties = '';
            if ($sku_id) {
                import('Lib.Action.Sku','App');
                $sku = new Sku();
                $sku_info = $sku->get_templet_sku($sku_id);
                if (!$sku->check_templet_quantity($sku_info, $sku_id, $tid, $num)) {
                    echo "<script>alert('库存不足，请重新下单!');history.go(-1);</script>";
                    exit();
                }
                $properties = $sku_info['properties'];
                $price = $sku_info['price'];
            } else {
                $price = M('templet')->where(['id' => $tid])->getField('price');
            }
            $add_info = array(
                'uid'   =>  $uid,
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
    }//end func add_shopping_cart
    
    
    //删除购物车
    public function del_shopping_cart(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $order_shopping_cart_obj = M('order_shopping_cart');
        
        
        $uid = $this->uid;
        
        $shopping_cart_id = I('id');
        
        if( empty($shopping_cart_id) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有检测到任何的提交！',
            );
            $this->ajaxReturn($return_result, 'json');
            return;
        }
        
        
        $condition = array(
            'id'    =>  $shopping_cart_id,
            'uid'   =>  $uid,
        );
        
        $del_res = $order_shopping_cart_obj->where($condition)->delete();
        
        
        if( $del_res ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '删除购物车成功！',
            );
        }
        else{
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '删除购物车失败，请重试！',
            );
        }
        
        $this->ajaxReturn($return_result, 'json');
    }//end func del_shopping_cart

    
    
    
    //
    public function base_url($array){
        
        $base_str = base64_encode($array);
        
        return $base_str;
    }//end func get_base_url
    
    
    public function get_base_url($base_str){
        $base_url = base64_decode($base_str);
        
        return $base_url;
    }
    
    //属性
    //获取产品属性和库存
    public function get_properties() {
        $id = I('id');
        if (!$id || !is_numeric($id)) {
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '产品id不存在',
            ];
            $this->ajaxReturn($return_result, 'json');
        }
        $product = M('Templet')->find($id);
        $product['price'] = $product['price'.$this->manager['level']];
        //商品属性
        import('Lib.Action.Sku','App');
        $sku = new Sku();
        $properties = $sku->get_templet_properties($id);
        $skus = $sku->get_templet_skus($id);

        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取数据成功！',
            'properties' =>  $properties,
            'skus'  =>  $skus,
            'product' => $product
        ];
        $this->ajaxReturn($return_result, 'json');
    }
    

}//end class