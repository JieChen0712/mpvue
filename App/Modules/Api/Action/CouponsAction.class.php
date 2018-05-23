<?php

/**
 * 	优惠券api
 */
class CouponsAction extends CommonAction {

    private $coupons_model;
    private $coupons_records_model;
    private $coupons_obj;
    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.Coupons', 'App');
        $this->coupons_obj = new Coupons();
        $this->coupons_model = M('coupons');
        $this->coupons_records_model = M('coupons_records');

    }
    
    //获取优惠券api
    public function get_coupons()
    {
        if( !IS_AJAX ){
            return FALSE;
        }
        $condition = [
            'store_id' => $this->store_id,
            'status' => $this->coupons_obj->status_yes,
        ];
        $page_info = array(
            'page_num' =>  I('get.page'),
        );
        $result = $this->coupons_obj->get_coupons($page_info, $condition);
        $this->ajaxReturn($result);
    }
}

?>