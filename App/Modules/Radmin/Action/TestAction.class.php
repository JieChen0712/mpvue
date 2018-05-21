<?php

/**
 *  微斯咖经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class TestAction extends CommonAction {

    public function phpinfo(){
        echo phpinfo();
    }
    
    public function add_user_bind(){
        
        $distributor_obj = M('distributor');
        $distributor_bind_model = M('distributor_bind');
        
        import('Lib.Action.User','App');
        $User = new User();
        
        
        $field = 'id';
        
//        $condition = array(
//            'pid'   =>  array('neq',0),
//        );
        
        $condition = array(
//            'pid'   =>  array('eq',0),
        );
        
        $distributor_info = $distributor_obj->field($field)->where($condition)->order('id asc')->select();
        
        foreach( $distributor_info as $k => $v ){
            
            $uid = $v['id'];
            
            $result = $User->update_distributor_bind($uid);
            
            print_r($result);
            echo '<br />';
            echo $distributor_bind_model->getLastSql();
            echo '<br />';
        }
    }
    
    
    public function generate_order_count(){
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        
        $order_obj = M('order');
        
        $condition = [
            'order_num'  =>  '281516606619430',
        ];
        
        $all_order = $order_obj->where($condition)->select();
        
        $order_info = [];
        
        foreach( $all_order as $k => $v ){
            $v_order_num = $v['order_num'];
            
            $order_info[$v_order_num][] = $v;
        }
        
        foreach( $order_info as $v_o ){
            $v_paytime = $v_o['0']['paytime'];
            
            $month = date('Ymd',$v_paytime);
            
            $res = $Order->generate_order_count($v_o,$month);
            
            print_r($res);echo '<hr />';
        }
    }
    
    
    
    
    
    public function get_c(){
        
        $config_path = __APP__.'/App/Conf/config.php';
        $other_path = __ROOT__.'/index.php';
        
        echo $other_path.'<br />';
        
        if( file_exists($other_path) ){
//            $config = file_get_contents($config_path);
//            print_r($config);
            
            echo '1';
        }
        else{
            echo '0';
        }
    }
    
    
    public function test_rebate_recharge(){
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        $uid = 64;
        $money = 100;
        
        $recharge_info = array(
            'source_id' =>  0,
            'note'  =>  '测试',
        );
        
        $recharge_result = $Funds->recharge($uid,$money,'rebate_recharge',$recharge_info);
        
        print_r($recharge_result);
        
        
    }
    
    public function test_rebate(){
        
        $rebate_percent = [
            100000  =>  0.05,
            200000  =>  0.06,
            300000  =>  0.07,
            400000  =>  0.08,
            600000  =>  0.09,
            800000  =>  0.1,
        ];
        
        $stage_data = array_keys($rebate_percent);
        
        $stage_num = 390000;
        
        $res = binarySearch($stage_data,$stage_num);
        
        echo $res;
    }
    
    
    public function get_str(){
        
        $qq_str = '<iframe frameborder="0" width="640" height="498" src="https://v.qq.com/iframe/player.html?vid=w05654ovh45&tiny=0&auto=0" allowfullscreen></iframe>';
        
        $aqy_str = '<iframe src="http://open.iqiyi.com/developer/player_js/coopPlayerIndex.html?vid=146e4b1886899e5a52cb776c16588316&tvId=817693600&accessToken=2.f22860a2479ad60d8da7697274de9346&appKey=3955c3425820435e86d0f4cdfe56f5e7&appId=1368&height=100%&width=100%" frameborder="0" allowfullscreen="true" width="100%" height="100%"></iframe>';
        
        $sohu_str = 'http://tv.sohu.com/upload/static/share/share_play.html#94021036_9413537_0_9001_0';
        
        $bil_str = '<embed height="415" width="544" quality="high" allowfullscreen="true" type="application/x-shockwave-flash" src="//static.hdslb.com/miniloader.swf" flashvars="aid=2464614&page=1" pluginspage="//www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash"></embed>';
        
        $str = $aqy_str;
        
        $src_num = strpos($str,'http');
        $str_src = substr($str,$src_num);
//        $src_num2 = strpos($str,'');
//        $src = substr($str_src,0,$src_num2);
        $src_arr = explode('"', $str_src);
        $src = $src_arr['0'];
        
        echo $src_num2.'<hr />';
        echo $src;
    }
    
    //查找没有下级的人数
    public function no_lowest() {
        $model = M('distributor');
        $users = $model->select();
        foreach ($users as $user) {
            $rec = $model->where(['recommendID' => $user['id'], 'audited' => 1])->find();
            if (!$rec) {
                $no[] = $user;
            }
        }
        echo '<pre>';var_dump($no);
    }
    
    //查找哪些代理is_lowest没有置1
    public function is_no_lowest() {
        $model = M('distributor');
        $users = $model->select();
        foreach ($users as $user) {
            $rec = $model->where(['recommendID' => $user['id'], 'audited' => 1])->find();
            if (!$rec && $user['is_lowest'] == 0) {
                $no[] = $user['id'];
            }
        }
        echo '<pre>';var_dump($no);
    }
    
    //查找哪些代理有下级但is_lowest没有置0的
    public function is_yes_lowest() {
        //清除团队缓存
        clean_team_path_cache();
        $model = M('distributor');
        $users = $model->select();
        foreach ($users as $user) {
            $rec = $model->where(['recommendID' => $user['id'], 'audited' => 1])->find();
            if ($rec && $user['is_lowest'] == 1) {
                $no[] = $user;
            }
        }
//        echo '<pre>';var_dump($no);die;
        foreach ($no as $v) {
            $model->where(['id' => $v['id']])->save(['is_lowest' => 0]);
        }
    }
    
    //修复rec_path
    public function update_rec_path() {
        $model = M('distributor');
        $users = $model->order('id asc')->select();
        foreach ($users as $user) {
            if ($user['recommendID'] == 0) {
                $model->where(['id' => $user['id']])->save(['rec_path' => 0, 'is_lowest' => 1]);
            } else {
                $parent = $model->where(['id' => $user['recommendID']])->find();
                $path = $parent['rec_path'].'-'.$parent['id'];
                $model->where(['id' => $user['id']])->save(['rec_path' => $path, 'depth'=>$parent['depth']+1, 'is_lowest' => 1]);
            }
        }
        echo 'finished';
    }
    
    //修复path
    public function update_path() {
        $model = M('distributor');
        $users = $model->order('id asc')->select();
        foreach ($users as $user) {
            if ($user['pid'] == 0) {
                $model->where(['id' => $user['id']])->save(['path' => 0]);
            } else {
                $parent = $model->where(['id' => $user['pid']])->find();
                $path = $parent['path'].'-'.$parent['id'];
                
                $data = [
                    'path' => $path,
                    'bossname' => $parent['name'],
                    'pname' => $parent['name']
                ];
                $model->where(['id' => $user['id']])->save($data);
            }
        }
        echo 'finished';
    }
    
    
    public function video(){
        $this->display();
    }
    
    
    public function get_prices(){
        import('Lib.Action.Sku','App');
        $Sku = new Sku();
        
        $templet_id = 15;
        $properties = '1:24;2:29';
        $the_sku_id = 57;
        $sku_ids[] = $the_sku_id;
        $new_sku_info = $Sku->get_templet_sku_ids($sku_ids);
        
        print_r($new_sku_info);
    }

    //修复授权时间
    public function update_time(){
        $model = M('distributor');
        $dis_info=$model->select();
        foreach ($dis_info as $key => $value){
            //获取申请时间
            $id=$value['id'];
            $apply_time=$value['time'];
            //将申请时间作为授权时间
            $data = array(
                'audit_time' => $apply_time,
                'end_times'=>strtotime("+1 year", $apply_time),
            );
            $model->where(array('id' => $id))->save($data);
        }
        echo 'finished';
    }
    
    
    
    public function test_refund_stock(){
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        
//        $uid = 103;
//        $stock_info[] = [
//            'p_id'   =>  38,
//            'num'   =>  1,
//        ];
//        
//        $res = $Stock->add_stock_refund_apply($uid,$stock_info);
//        print_r($res);
        
        
        $apply_id = 2;
        $pass = 1;
        
        $res2 = $Stock->pass_stock_refund_apply($apply_id,$pass,'test');
        
        print_r($res2);
    }
    
    
    
}

?>
