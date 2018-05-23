<?php
//用户管理的模块化代码
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class Templet extends Common{

    public $model;
    

    /**
     * 架构函数
     */
    public function __construct() {
        $this->model = M('templet');
    }
    
    
    //商城信息
    public function get_temple($page_info=array(),$condition=array()){
        import('ORG.Util.Page');
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $count = $this->model->where($condition)->count('id');
        
        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->model->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $this->model->where($condition)->order('id desc')->select();
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
            'count' =>  $count,
            'page'  =>  $page,
        );

        return $return_result;
    }//end func get_money_apply
    
    
}