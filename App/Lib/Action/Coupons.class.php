<?php
//优惠券
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class Coupons extends Common{
    
    private $user_model;
    private $coupons_model;
    private $coupons_records_model;
    
    public $status_yes = 1;//开启/显示
    public $status_no = 0;//关闭/不显示
    public $status_name = [];
    /**
     * 架构函数
     */
    public function __construct() {
        $this->coupons_model = M('coupons');
        $this->coupons_records_model = M('coupons_records');
        $this->user_model = M('user');
        
        $this->status_name[$this->status_yes] = '使用中';
        $this->status_name[$this->status_no] = '已失效';
    }
    
     //获取优惠券
    public function get_coupons($page_info=array(),$condition=array(), $coupons_ids=array()){
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        $count = $this->coupons_model->where($condition)->count();
        
        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->coupons_model->where($condition)->page($page_con)->select();
            }
            else{
                $list = $this->coupons_model->where($condition)->select();
            }

            $list = $this->get_related_data($list, 'store', ['store_id']);
            
            foreach( $list as $k => $v ){
                $list[$k]['status_name'] = $this->status_name[$v['status']];
                $list[$k]['created'] = date('Y-m-d H:i:s',$v['created']);
                $list[$k]['updated'] = date('Y-m-d H:i:s',$v['updated']);
                
                //领取/未领取做标记
                if (in_array($v['id'], $coupons_ids)) {
                    $list[$k]['is_get'] = true;
                } else {
                    $list[$k]['is_get'] = false;
                }
            }
            
            //-----end 整理添加相应其它表的信息-----
        }

        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }


        $return_result = array(
            'code' => 1,
            'msg' => '获取优惠券成功',
            'list'  =>  empty($list)?[]:$list,
            'page'  =>  $page,
            'count' =>  $count,
        );

        return $return_result;
    }
    
     //获取优惠券领取记录
    public function get_coupons_records($page_info=array(),$condition=array()){

        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        $count = $this->coupons_records_model->where($condition)->count();
        
        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->coupons_records_model->where($condition)->page($page_con)->select();
            }
            else{
                $list = $this->coupons_records_model->where($condition)->select();
            }
            
            $list = $this->get_related_data($list, 'user', ['uid']);
            $list = $this->get_related_data($list, 'store', ['store_id']);
            $list = $this->get_related_data($list, 'coupons', ['coupons_id']);
//            echo '<pre>';var_dump($list);die;
            foreach( $list as $k => $v ){
                $list[$k]['created'] = date('Y-m-d H:i:s',$v['created']);
            }
            
            //-----end 整理添加相应其它表的信息-----
        }

        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }


        $return_result = array(
            'code' => 1,
            'msg' => '获取领取优惠券记录成功',
            'list'  =>  empty($list)?[]:$list,
            'page'  =>  $page,
            'count' =>  $count,
        );

        return $return_result;
    }
    
    //领取优惠券
    public function add_coupoons($data) {
        $store_id = $data['store_id'];//店铺id
        $uid = $data['uid'];//用户id
        $coupons_id = $data['coupons_id'];//优惠券id
        
        if (empty($coupons_id)) {
            $return_result = [
                'code' => -1,
                'msg' => '优惠券不存在',
            ];
            return $return_result;
        }
        $coupons = $this->coupons_model->where(['id' => $coupons_id, 'status' => $this->status_yes])->find();
        if (empty($coupons)) {
            $return_result = [
                'code' => -2,
                'msg' => '优惠券不存在或已经失效了',
            ];
            return $return_result;
        }
        
        $add = [
            'store_id' => $store_id,
            'uid' => $uid,
            'coupons_id' => $coupons_id,
            'created' => time(),
        ];
        if (!$this->coupons_records_model->add($add)) {
            setLog('优惠券领取失败：'.json_encode($add),'coupons');
            $return_result = [
                'code' => -3,
                'msg' => '优惠券领取失败',
            ];
            return $return_result;
        }
        $return_result = [
            'code' => 1,
            'msg' => '优惠券领取成功',
        ];
        return $return_result;
    }
}