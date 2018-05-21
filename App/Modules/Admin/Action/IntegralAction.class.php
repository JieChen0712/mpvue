<?php

/**
 * 	微斯咖代理后台——首页
 */
class IntegralAction extends CommonAction {

    //代理后台首页
    public function index() {
    	
        $id = $this->uid;
        
        $condition = array(
            'uid'   =>  $id,
        );
        
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
            'page_list_num' =>  50,
        );
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        $result = $Integral->get_integral_log($page_info,$condition);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->integral_status = $Integral->integral_status;
        $this->integral_class = $Integral->integral_class;
        
        $this->display();
    }
    
    
    //获取签到状态
    public function get_sign_up_state(){
        
        $is_sign_up = 2;//默认为2，未签到
        $uid = $this->uid;
        
        //获取充值记录
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        if( $Integral->integral_open ){
            $condition = array(
                'uid'   =>  $uid,
                'type'  =>  3,
            );

            $start_time = $end_time = date('Ymd');

            //开始时间-结束时间
            if ( !empty($start_time) && !empty($end_time)) {
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time) + 86399;

                $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
            }

            $integral_log_info = $Integral->get_integral_log([],$condition);
            
            if( !empty($integral_log_info['list']) ){
                $is_sign_up = 1;
            }
        }
        else{
            $is_sign_up = 0;
        }
        
        return $is_sign_up;
    }//end func get_sign_up_state
    
    
    //获取签到状态
    public function get_sign_up_state_ajax(){
        if( !IS_AJAX ){
            exit();
        }
        
        
        $is_sign_up = $this->get_sign_up_state();
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '获取成功！',
            'is_sign_up'    =>  $is_sign_up,
        );
        
        $this->ajaxReturn($return_result);
    }//end func get_sign_up_state_ajax
    
    
    //签到
    public function sign_up(){
        if( !IS_AJAX ){
            exit();
        }
        
        $uid = $this->uid;
        
        //获取充值记录
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        
        $is_sign_up = $this->get_sign_up_state();
        
        
        $sign_in_excu = FALSE;
        $msg = '签到失败！';
        
        if( $is_sign_up == 2 ){
            $admin_sign_in_result = $Integral->admin_sign_in($uid);
            
            if( $admin_sign_in_result['code'] == 1 ){
                $msg = '签到成功！';
                
                $return_result = array(
                    'code'  =>  1,
                    'msg'   =>  '签到成功！'
                );
            }
            else{
                $return_result = $admin_sign_in_result;
            }
        }
        else{
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '已经签到！'
            );
        }
        
        
        $this->ajaxReturn($return_result);
    }//end func sign_up
    
    

}

?>