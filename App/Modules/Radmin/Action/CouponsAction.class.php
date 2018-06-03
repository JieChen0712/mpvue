<?php

/**
 * 	优惠券
 */
header("Content-Type: text/html; charset=utf-8");

class CouponsAction extends CommonAction {

    private $user_model;
    private $coupons_model;
    private $coupons_records_model;
    private $coupons_obj;
    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.Coupons', 'App');
        $this->coupons_obj = new Coupons();
        $this->coupons_model = M('coupons');
        $this->coupons_records_model = M('coupons_records');
        $this->user_model = M('user');

    }

    //优惠券
    public function index()
    {
        $condition = $this->condition;
        
        $page_info = [
            'page_num' => I('get.p'),
        ];
        $result = $this->coupons_obj->get_coupons($page_info, $condition);
//        var_dump($result['list']);die;
        $this->count = $result['count'];
        $this->p = I('p');
        $this->limit = $result['limit'];
        $this->list = $result['list'];
        $this->display();
    }

    //add
    public function add()
    {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        if (IS_POST) {
            $store_id = trim(I('store_id'));
            if($this->is_super){
                if (empty($store_id)) {
                    $this->error('请选择商城');
                }
            } else {
                $store_id = $this->store_id;
            }
            $data = [
                'name' => trim(I('name')),
                'img' => trim(I('img')),
                'status' => trim(I('status')),
                'created' => time(),
                'store_id' => $store_id,
            ];
            $res = $this->coupons_model->add($data);
            if ($res) {
                $this->add_active_log('添加'.I('name').'优惠券成功');
                $this->success('添加成功');

            } else {
                $this->error('添加失败');
            }
        } else {
            $this->display();
        }
    }
    
    //编辑
    public function edit()
    {
        $id = trim(I(id));
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        if (IS_POST) {
            $data = [
                'id' => $id,
                'name' => trim(I('name')),
                'img' => trim(I('img')),
                'status' => trim(I('status')),
                'updated' => time(),
            ];
            $res = $this->coupons_model->save($data);
            if ($res) {
                $this->add_active_log('编辑'.I('name').'优惠券成功');
                $this->success('编辑成功');

            } else {
                $this->error('编辑失败');
            }
        } else {
            $this->row = $this->coupons_model->find($id);
            $this->display();
        }
    }
    
    //优惠券领取记录
    public function records()
    {
        $condition = $this->condition;
        $page_info = [
            'page_num' => I('get.p'),
        ];
        $result = $this->coupons_obj->get_coupons_records($page_info, $condition);
//        var_dump($result['list']);die;
        $this->count = $result['count'];
        $this->p = I('p');
        $this->limit = $result['limit'];
        $this->list = $result['list'];
        $this->display();
    }
}

?>