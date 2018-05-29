<?php
//用户管理的模块化代码
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class Store extends Common{

    public $store_model;//商城信息
    public $topmap_model;//顶部轮播图
    
    public $status_yes = 1;//店铺开启
    public $status_no = 0;//店铺关闭

    /**
     * 架构函数
     */
    public function __construct() {
        $this->store_model = M('store');
        $this->topmap_model = M('topmap');
    }
    
    
    //商城信息
    public function get_store($page_info=array(),$condition=array()){
        import('ORG.Util.Page');
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $count = $this->store_model->where($condition)->count('id');
        
        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->store_model->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $this->store_model->where($condition)->order('id desc')->select();
            }

            //-----整理添加相应其它表的信息-----
            
//            foreach( $list as $k => $v ){
//                
//            }
            //-----end 整理添加相应其它表的信息-----
        }


        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }

        
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );

        return $return_result;
    }//end func get_money_apply
    
    
    
    //获取顶部轮播图
    public function get_topmap($page_info=array(),$condition=array()){
        import('ORG.Util.Page');
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $count = $this->topmap_model->where($condition)->count('id');
        
        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->topmap_model->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $this->topmap_model->where($condition)->order('id desc')->select();
            }

            //-----整理添加相应其它表的信息-----
            
//            foreach( $list as $k => $v ){
//                
//            }
            //-----end 整理添加相应其它表的信息-----
        }


        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }

        
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );

        return $return_result;
    }//end func get_topmap
    
    
    /**
     * 检查店铺合法性
     * @param type $store_id
     */
    public function check_store($store_id) {
        if (empty($store_id)) {
            $return_result = [
                'code' => -1,
                'msg' => '商城/店铺id为空',
            ];
            return $return_result;
        }
        $store = $this->store_model->where(['id' => $store_id, 'status' => $this->status_yes])->find();
        if (empty($store)) {
            $return_result = [
                'code' => -2,
                'msg' => '商城/店铺id为空',
            ];
            return $return_result;
        }
        $return_result = [
            'code' => 1,
            'msg' => '商城/店铺检查通过',
        ];
        return $return_result;
    }
}