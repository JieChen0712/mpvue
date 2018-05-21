<?php

/**
 * 	微斯咖经销商后台
 */
class MarketAction extends CommonAction {

    //营销首页
    public function index() {
        $this->display();
    }

    public function my_center() {
        $this->display();
    }
    
    //商学院首页
    public function business_index() {

        $market_business = M('market_business');
        if (IS_AJAX) {
            $id = I('id');
            if ($id) {
                $where['category_id'] = $id;
            } else {
                $where['category_id'] =['egt', 0];
            }
            $business = $market_business->where($where)->select();
            $this->ajaxReturn($business, 'json');
          
        } else {
            $market_business_category = M('market_business_category');

            $business_category = $market_business_category->select();
            $business = $market_business->select();
            $this->business = $business;
            $this->business_category = $business_category;
            $this->display();
        }
    }

    //商学院详情页
    public function business_detail() {
        $bus_id = $_GET['bus_id'];
        $market_business = M('market_business');
        $business = $market_business->where(array('id'=>$bus_id))->select();
        $this->business = $business;
        $this->display();
    }

    //视频中心首页
    public function movie_index(){
        $market_movie = M('market_movie');
        $movie = $market_movie->select();
        $this->movie = $movie;
        $this->display();
    }

    //素材库首页
    public function material_index(){
        $market_material=M('market_material');
        $material= $market_material->select();
        $this->material=$material;
        $this->display();
    }

    //素材库详情页
    public function material_detail() {
        $material_id = $_GET['material_id'];
        $market_material_detail=M('market_material_detail');
        $material_detail = $market_material_detail->where(array('material_id'=>$material_id))->select();
        foreach ($material_detail as $k => $v) {
            $arr = explode(',', $v['image']);
            $material_detail[$k]['image'] = $arr;
        }
        $this->material_detail=$material_detail;

        $this->display();
    }


}

?>